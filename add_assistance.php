<?php
header('Content-Type: application/json');
include 'config.php';

$response = ['status' => 'error', 'message' => 'An unexpected error occurred'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $individual_id = $_POST['individual_id'] ?? null;
        $clientType = $_POST['client_type'] ?? null;
        $assistanceType = $_POST['assistance_type'] ?? null;
        $fundType = $_POST['fund_type'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $date = $_POST['date'] ?? null;
        $beneficiary = $_POST['beneficiary'] ?? null;

        // Validate required fields
        if (!$individual_id || !$assistanceType || !$fundType || !$amount || !$date || !$beneficiary) {
            throw new Exception('All fields are required');
        }

        // Use mysqli instead of PDO
        $stmt = $conn->prepare("INSERT INTO assistance_request_history (individual_id, clientType, assistanceType, fundType, amount, date, beneficiary) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdss", $individual_id, $clientType, $assistanceType, $fundType, $amount, $date, $beneficiary);
        $stmt->execute();

        $response = ['status' => 'success', 'message' => 'Assistance record added successfully'];
    } else {
        http_response_code(405);
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log("Error in add_assistance.php: " . $e->getMessage());
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
