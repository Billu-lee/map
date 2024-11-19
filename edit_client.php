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
        $stmt = $pdo->prepare("SELECT * FROM CLIENT WHERE id = :client_id");
        $stmt->execute(['client_id' => $client_id]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            die("Client not found.");
        }
    } else {
        die("Invalid client ID.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];

        // Update client details
        $update_stmt = $pdo->prepare("UPDATE CLIENT SET name = :name, phone = :phone WHERE id = :client_id");
        $update_stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'client_id' => $client_id,
        ]);

        header("Location: admin_page.php");
        exit();
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
    <title>Edit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Edit Client</h1>
    <form method="POST" class="card p-4">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($client['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($client['phone']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Save Changes</button>
        <a href="admin_page.php" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
