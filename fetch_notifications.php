<?php
include 'config.php';

// SQL query to fetch notifications grouped by notification ID
$sql = "
    SELECT 
        notifications.id, 
        CONCAT(users.first_name, ' ', users.middle_name, ' ', users.last_name) AS full_name, 
        uploads.category, 
        notifications.notification_date 
    FROM notifications
    INNER JOIN uploads ON FIND_IN_SET(uploads.id, notifications.file_ids)
    INNER JOIN users ON uploads.user_id = users.id
    GROUP BY notifications.id  -- Group by notification ID
    ORDER BY notifications.notification_date DESC";

$result = $conn->query($sql);

// Check if query was successful
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Check if query returns any rows
if ($result->num_rows > 0) {
    $notifications = array();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = array(
            'id' => $row['id'],
            'message' => $row['full_name'] . ' requests for ' . $row['category'] . ' assistance',
            'notification_date' => $row['notification_date']
        );
    }
    echo json_encode($notifications);
} else {
    echo json_encode(array());
}

$conn->close();
?>
