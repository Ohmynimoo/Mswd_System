<?php
include 'config.php';

// Fetch all notifications
$sql = "
    SELECT 
        notifications.id, 
        notifications.message,
        notifications.notification_date,
        notifications.is_read
    FROM notifications
    INNER JOIN uploads ON FIND_IN_SET(uploads.id, notifications.file_ids)
    INNER JOIN users ON uploads.user_id = users.id
    GROUP BY notifications.id
    ORDER BY notifications.notification_date DESC";

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Collect all notifications
$notifications = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = array(
            'id' => $row['id'],
            'message' => $row['message'],
            'notification_date' => $row['notification_date'],
            'is_read' => $row['is_read'],
        );
    }
}

// Count unread notifications
$unreadCountQuery = "SELECT COUNT(*) as unread_count FROM notifications WHERE is_read = 0";
$unreadResult = $conn->query($unreadCountQuery);

if ($unreadResult === false) {
    die("Error executing unread count query: " . $conn->error);
}

$unreadCount = $unreadResult->fetch_assoc()['unread_count'];

// Return notifications and unread count
echo json_encode(array(
    'notifications' => $notifications,
    'unread_count' => $unreadCount,
));

$conn->close();
?>
