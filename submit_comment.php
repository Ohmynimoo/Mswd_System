<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notificationId = $_POST['notification_id'];
    $comment = trim($_POST['comment']); // Trim whitespace

    // Reject empty comments
    if (empty($comment)) {
        echo "Comment cannot be empty.";
        exit;
    }
    
    // Check if the user is logged in or not
    if (isset($_SESSION['userid'])) {
        $userId = $_SESSION['userid']; // Logged-in user's ID
    } else {
        $userId = 1; // Assign to "Anonymous User" with ID = 1
    }

    $conn->begin_transaction();

    try {
        // Fetch client name from the notifications table
        $query = "SELECT client_name FROM notifications WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $stmt->bind_result($clientName);
        $stmt->fetch();
        $stmt->close();

        if (!$clientName) {
            throw new Exception("Client name not found for notification ID: " . $notificationId);
        }

        // Fetch the client ID by matching the first_name, middle_name, and last_name
        $query = "
            SELECT id FROM users 
            WHERE CONCAT(first_name, ' ', middle_name, ' ', last_name) = ?
        ";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("s", $clientName);
        $stmt->execute();
        $stmt->bind_result($clientId);
        $stmt->fetch();
        $stmt->close();

        if (!$clientId) {
            throw new Exception("Client ID not found for client name: " . $clientName);
        }

        // Insert a notification for the client
        $notificationMessage = "New comment on your uploaded file.";
        $query = "INSERT INTO client_notifications (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("is", $clientId, $notificationMessage);
        $stmt->execute();
        $clientNotificationId = $stmt->insert_id;
        $stmt->close();

        // Insert the comment, using anonymous user ID (1) if not logged in
        $query = "INSERT INTO comments (notification_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("iis", $clientNotificationId, $userId, $comment);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo "Comment submitted and client notified successfully.";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "Failed to submit comment: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
