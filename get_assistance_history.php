<?php
header('Content-Type: application/json');
include 'config.php';

$individual_id = $_GET['individual_id'] ?? null;

if (!$individual_id) {
    echo json_encode(['status' => 'error', 'message' => 'Individual ID is required']);
    exit;
}

try {
    // Use mysqli instead of PDO
    $stmt = $conn->prepare("SELECT clientType, assistanceType, fundType, amount, date, beneficiary FROM assistance_request_history WHERE individual_id = ?");
    $stmt->bind_param("i", $individual_id);  // Bind parameter as an integer
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($data ?: []); // Send empty array if no data
} catch (Exception $e) {
    error_log("Error in get_assistance_history.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error fetching assistance history']);
}
