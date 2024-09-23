<?php
// Include database connection
include 'config.php';

$apiKey = 'deff2993b2ac14211fab692061d13ba1';  // Semaphore API key

$mobile = $_POST['mobile'];
$message = $_POST['message'];
$notificationId = $_POST['notification_id'];

// Validate mobile number format (Philippines-specific)
if (!preg_match("/^09\d{9}$/", $mobile)) {
    echo "Invalid mobile number format.";
    exit;
}

// Prepare data for sending SMS via Semaphore API
$data = array(
    'apikey' => $apiKey, 
    'number' => $mobile, 
    'message' => $message, 
    'sendername' => 'SEMAPHORE'  // Sender name (optional, based on Semaphore account)
);

$ch = curl_init('https://semaphore.co/api/v4/messages');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Decode API response from Semaphore
$responseData = json_decode($response, true);

if (!$responseData) {
    echo 'Failed to parse API response. Full response: ' . $response;
    exit;
}

// Check if the SMS was queued or sent successfully
$status = isset($responseData['status']) && $responseData['status'] == 'queued' ? 'success' : 'failed';
$errorMsg = isset($responseData['error']) ? $responseData['error'] : 'Unknown error';

// Log the SMS in the database
$responseJSON = json_encode($responseData); 
$sql = "INSERT INTO sms_logs (notification_id, mobile, message, status, response) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Debug to see if query preparation worked
if ($stmt === false) {
    echo "Failed to prepare SMS log statement: " . $conn->error;
    exit;
}

$stmt->bind_param("issss", $notificationId, $mobile, $message, $status, $responseJSON);

if ($stmt->execute()) {
    echo 'SMS logged successfully!';
} else {
    // Debug to show why SMS log failed
    echo 'Error logging SMS into the database: ' . $conn->error;
}

$stmt->close();
$conn->close();
?>
