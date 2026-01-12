<?php
//session_start();
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
    
    // Ricerca utente
    $sql = "SELECT id, username, password FROM utenti WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verifica password
    if ($utente && password_verify($password, $utente['password'])) {
        // Login riuscito
       // $_SESSION['user_id'] = $utente['id'];
       // $_SESSION['username'] = $utente['username'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login effettuato con successo',
            'user' => ['id' => $utente['id'], 'username' => $utente['username']]
        ]);
    } else {
        // Credenziali errate
        echo json_encode(['success' => false, 'message' => 'Username o password non corretti']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Errore del database: ' . $e->getMessage()]);
}
?>
