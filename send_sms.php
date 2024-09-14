<?php
// Include database connection
include 'config.php';

// Your Semaphore API Key
$apiKey = 'deff2993b2ac14211fab692061d13ba1';  // Ensure this is correct

// Get mobile number, message, and notification ID from form POST data
$mobile = $_POST['mobile'];
$message = $_POST['message'];
$notificationId = $_POST['notification_id'];

// Validate the mobile number
if (!preg_match("/^09\d{9}$/", $mobile)) {
    echo "Invalid mobile number format.";
    exit;
}

// Prepare the data for the API request
$data = array(
    'apikey' => $apiKey, 
    'number' => $mobile, 
    'message' => $message, 
    'sendername' => 'SEMAPHORE'  // Optional
);

// Send the request via cURL
$ch = curl_init('https://semaphore.co/api/v4/messages');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request and capture the response
$response = curl_exec($ch);

// Check for any cURL errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Log the full response for debugging purposes
file_put_contents('sms_response.log', $response, FILE_APPEND);

// Parse the API response
$responseData = json_decode($response, true);

// Check if API response is valid
if (!$responseData) {
    echo 'Failed to parse API response. Full response: ' . $response;
    exit;
}

// Determine if the SMS was sent successfully
$status = isset($responseData['status']) && $responseData['status'] == 'queued' ? 'success' : 'failed';
$errorMsg = isset($responseData['error']) ? $responseData['error'] : 'Unknown error';

// Store SMS log in the database
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
