<?php
include 'config.php';

if (isset($_POST['id'])) {
    $notificationId = intval($_POST['id']);
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();

    // Get updated unread count
    $result = $conn->query("SELECT COUNT(*) as unread_count FROM notifications WHERE is_read = 0");
    $unreadCount = ($result && $row = $result->fetch_assoc()) ? $row['unread_count'] : 0;

    echo json_encode(['unread_count' => $unreadCount]);
}
$conn->close();
?>
