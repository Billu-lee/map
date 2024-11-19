<!-- save_data.php -->
<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'databank_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get input data
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'] ?? '';
    $phone = $data['phone'] ?? '';
    $place = $data['place'] ?? '';
    $cornerPoints = $data['cornerPoints'] ?? [];

    if ( empty($name) || empty($phone) || empty($place)  || count($cornerPoints) < 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Insert client
    $stmt = $pdo->prepare("INSERT INTO CLIENT (name, phone) VALUES (:name, :phone)");
    $stmt->execute(['name' => $name, 'phone' => $phone]);
    $clientId = $pdo->lastInsertId();

    // Insert field
    $stmt = $pdo->prepare("INSERT INTO FIELD (location, client_id) VALUES (:place, :client_id)");
        $stmt->execute([
            'client_id' => $clientId,
            'place'=> $place,//binds the value of $place to the :place placeholder

    ]);
    $fieldId = $pdo->lastInsertId();

    // Insert points
    $stmt = $pdo->prepare("INSERT INTO POINT (latitude, longitude, field_id) VALUES (:latitude, :longitude, :field_id)");
    foreach ($cornerPoints as $point) {
        $stmt->execute([
            'latitude' => $point[0],
            'longitude' => $point[1],
            'field_id' => $fieldId,
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Data saved successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
