<?php
header('Content-Type: application/json');

// Configurazione database
$host = 'localhost';
$dbname = 'nome_database';
$user = 'root';
$pass = '';

try {
    // Connessione PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ricezione dati JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    
    // Validazione
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username e password obbligatori']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La password deve essere di almeno 6 caratteri']);
        exit;
    }
    
    // Crittografia password con bcrypt
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Preparazione query
    $sql = "INSERT INTO utenti (username, password) VALUES (:username, :password)";
    $stmt = $pdo->prepare($sql);
    
    // Binding parametri
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':password', $passwordHash, PDO::PARAM_STR);
    
    // Esecuzione
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Registrazione completata con successo']);
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Username già esistente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore del database: ' . $e->getMessage()]);
    }
}
?>
