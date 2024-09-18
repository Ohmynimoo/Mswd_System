<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notificationId = $_POST['notification_id'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['userid'];

    $conn->begin_transaction();

    try {
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

        $query = "SELECT id FROM users WHERE fullname = ?";
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

        $query = "INSERT INTO comments (notification_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("iis", $clientNotificationId, $userId, $comment);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo "Comment submitted and client notified successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to submit comment: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>