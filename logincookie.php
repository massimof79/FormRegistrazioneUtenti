<?php
header('Content-Type: application/json');

/* Verifica se l'utente è già loggato tramite cookie */
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Utente già loggato a sistema'
    ]);
    exit;
}

$pdo = new PDO(
    'mysql:host=localhost;dbname=nome_db;charset=utf8mb4',
    'db_user',
    'db_password',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$data = json_decode(file_get_contents('php://input'), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';
$remember = (bool)($data['remember'] ?? false);

$stmt = $pdo->prepare(
    'SELECT password_hash FROM utenti WHERE username = :u'
);
$stmt->execute(['u' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Credenziali errate'
    ]);
    exit;
}

/* Impostazione cookie */
$expire = $remember ? time() + 86400 * 30 : 0;

setcookie('username', $username, $expire, '/', '', false, true);
setcookie('password', $password, $expire, '/', '', false, true);

echo json_encode([
    'success' => true,
    'message' => 'Login effettuato'
]);
