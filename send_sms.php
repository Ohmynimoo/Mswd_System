<?php
// Include database connection
include 'config.php';

$apiKey = 'deff2993b2ac14211fab692061d13ba1';  // Ensure this is correct

$mobile = $_POST['mobile'];
$message = $_POST['message'];
$notificationId = $_POST['notification_id'];

if (!preg_match("/^09\d{9}$/", $mobile)) {
    echo "Invalid mobile number format.";
    exit;
}

$data = array(
    'apikey' => $apiKey, 
    'number' => $mobile, 
    'message' => $message, 
    'sendername' => 'SEMAPHORE'  // Optional
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

file_put_contents('sms_response.log', $response, FILE_APPEND);

$responseData = json_decode($response, true);

if (!$responseData) {
    echo 'Failed to parse API response. Full response: ' . $response;
    exit;
}

$status = isset($responseData['status']) && $responseData['status'] == 'queued' ? 'success' : 'failed';
$errorMsg = isset($responseData['error']) ? $responseData['error'] : 'Unknown error';

$responseJSON = json_encode($responseData);  // Assign json_encode to a variable
$sql = "INSERT INTO sms_logs (notification_id, mobile, message, status, response) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $notificationId, $mobile, $message, $status, $responseJSON);

if ($stmt->execute()) {
    if ($status == 'success') {
        echo 'SMS sent successfully!';
    } else {
        echo 'Failed to send SMS. Error: ' . $errorMsg;
    }
} else {
    echo 'Error logging SMS into the database: ' . $conn->error;
}

$stmt->close();
?>
