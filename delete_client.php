<?php
$host = 'localhost';
$dbname = 'databank_db';
$username = 'root';
$password = '';

if (!isset($_GET['id'])) {
    die('Invalid request.');
}

$clientId = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete points associated with fields of the client
    $stmt = $pdo->prepare("
        DELETE POINT 
        FROM POINT 
        INNER JOIN FIELD ON POINT.field_id = FIELD.id
        WHERE FIELD.client_id = :client_id
    ");
    $stmt->execute(['client_id' => $clientId]);

    // Delete fields of the client
    $stmt = $pdo->prepare("DELETE FROM FIELD WHERE client_id = :client_id");
    $stmt->execute(['client_id' => $clientId]);

    // Delete the client
    $stmt = $pdo->prepare("DELETE FROM CLIENT WHERE id = :client_id");
    $stmt->execute(['client_id' => $clientId]);

    header('Location: admin_page.php');
    exit;
} catch (PDOException $e) {
    die("Error deleting client: " . $e->getMessage());
}
?>
