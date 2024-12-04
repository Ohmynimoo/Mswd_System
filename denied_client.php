<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include 'config.php';

// Check if client_id is provided
if (!isset($_POST['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Client ID is missing']);
    exit;
}

$client_id = intval($_POST['client_id']);

// Delete the client from the database
$sql = "DELETE FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $client_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Client deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete client']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare the query']);
}

$conn->close();
?>
