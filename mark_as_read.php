<?php
include 'config.php';

header('Content-Type: application/json'); // Set the header to return JSON data

if (isset($_POST['id'])) {
    $notificationId = $_POST['id'];

    // Mark the notification as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->close();

    // Get the count of unread notifications
    $unreadCountQuery = "SELECT COUNT(*) AS unread_count FROM notifications WHERE is_read = 0";
    $result = $conn->query($unreadCountQuery);
    $unreadCount = 0;
    if ($result) {
        $row = $result->fetch_assoc();
        $unreadCount = $row['unread_count'];
    }

    // Return the unread count to the client as a JSON response
    echo json_encode(['unread_count' => $unreadCount]);
} else {
    // If the ID is not set, return an error response
    echo json_encode(['error' => 'Invalid notification ID']);
}

$conn->close();
