<?php
session_start();
include 'config.php';

$userId = $_SESSION['userid'];  // Ensure the user is logged in and user ID is available

// Mark all notifications as read for the current user
$query = "UPDATE client_notifications SET is_read = 1 WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}

$stmt->close();
$conn->close();
?>
