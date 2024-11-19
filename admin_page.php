<?php
$host = 'localhost';
$dbname = 'databank_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch clients with their fields and points
    $stmt = $pdo->query("
        SELECT 
            CLIENT.id AS client_id, 
            CLIENT.name AS client_name, 
            CLIENT.phone AS client_phone, 
            FIELD.id AS field_id, 
            FIELD.location AS field_location,
            GROUP_CONCAT(CONCAT(POINT.latitude, ',', POINT.longitude) SEPARATOR ' | ') AS points
        FROM CLIENT
        LEFT JOIN FIELD ON CLIENT.id = FIELD.client_id
        LEFT JOIN POINT ON FIELD.id = POINT.field_id
        GROUP BY CLIENT.id, FIELD.id
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            font-size: 2rem;
            color: #343a40;
            font-weight: 600;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
                text-align: center;
            }
            .table-responsive {
                border: none;
            }
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4 text-center">Admin Dashboard</h1>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
            <tr>
                <th>Client ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Field Location</th>
                <th>Points</th>
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['client_id']) ?></td>
                    <td><?= htmlspecialchars($row['client_name']) ?></td>
                    <td><?= htmlspecialchars($row['client_phone']) ?></td>
                    <td><?= htmlspecialchars($row['field_location']) ?></td>
                    <td><?= htmlspecialchars($row['points']) ?></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="view_client.php?id=<?= $row['client_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="edit_client.php?id=<?= $row['client_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_client.php?id=<?= $row['client_id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
