<?php
// Database connection
$host = 'localhost';
$dbname = 'databank_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if client ID is passed
    if (isset($_GET['id'])) {
        $client_id = $_GET['id'];

        // Fetch client details
        $stmt = $pdo->prepare("
            SELECT 
                CLIENT.id AS client_id, 
                CLIENT.name AS client_name, 
                CLIENT.phone AS client_phone, 
                FIELD.location AS field_location,
                GROUP_CONCAT(CONCAT(POINT.latitude, ',', POINT.longitude) SEPARATOR ' | ') AS points
            FROM CLIENT
            LEFT JOIN FIELD ON CLIENT.id = FIELD.client_id
            LEFT JOIN POINT ON FIELD.id = POINT.field_id
            WHERE CLIENT.id = :client_id
            GROUP BY CLIENT.id, FIELD.id
        ");
        $stmt->execute(['client_id' => $client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            die("Client not found.");
        }
    } else {
        die("Invalid client ID.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Client Details</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($client['client_name']) ?></h5>
            <p class="card-text"><strong>Phone:</strong> <?= htmlspecialchars($client['client_phone']) ?></p>
            <p class="card-text"><strong>Field Location:</strong> <?= htmlspecialchars($client['field_location']) ?></p>
            <p class="card-text"><strong>Points:</strong> <?= htmlspecialchars($client['points']) ?></p>
        </div>
        <div class="card-footer">
            <a href="admin_page.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
