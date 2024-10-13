<?php
session_start();
include 'config.php';

$userId = $_SESSION['userid'];  // Ensure the user is logged in and user ID is available

$query = "SELECT id, message FROM client_notifications WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($notifications);
?>
