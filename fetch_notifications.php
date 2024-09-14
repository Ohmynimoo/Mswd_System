<?php
include 'config.php';

// SQL query to fetch notifications
$sql = "SELECT id, 'Someone sends a request' AS message, notification_date FROM notifications ORDER BY notification_date DESC";
$result = $conn->query($sql);

// Check if query was successful
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Check if query returns any rows
if ($result->num_rows > 0) {
    $notifications = array();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    echo json_encode($notifications);
} else {
    echo json_encode(array());
}

$conn->close();
?>
