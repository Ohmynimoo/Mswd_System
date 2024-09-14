<?php
include 'config.php';

if (isset($_POST['id'])) {
    $notificationId = $_POST['id'];
    $query = "UPDATE notifications SET viewed = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Notification marked as read.";
    } else {
        echo "Failed to mark notification as read.";
    }
    $stmt->close();
} else {
    echo "No notification ID provided.";
}
$conn->close();
