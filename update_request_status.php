<?php
include 'config.php';  // Include the database connection

header('Content-Type: application/json');  // Ensure the response is in JSON format

if (isset($_POST['notification_id']) && isset($_POST['status'])) {
    $notificationId = $_POST['notification_id'];
    $status = $_POST['status'];  // Status is passed dynamically (e.g., 'Processing', 'Approved', etc.)

    // Check what status is being received
    error_log('Received Status: ' . $status);

    // Fetch the file_ids associated with the notification
    $query = "SELECT file_ids FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fileIds = $row['file_ids'];  // Comma-separated file_ids

        // Split the file_ids into an array
        $fileIdArray = explode(',', $fileIds);

        // Prepare the SQL query to update the request_status dynamically for each file ID
        $fileIdPlaceholders = implode(',', array_fill(0, count($fileIdArray), '?'));
        $updateQuery = "UPDATE uploads SET request_status = ? WHERE id IN ($fileIdPlaceholders)";

        $updateStmt = $conn->prepare($updateQuery);

        // Bind the dynamic status and the file IDs
        $types = 's' . str_repeat('i', count($fileIdArray));  // 's' for status, 'i' for file IDs
        $updateStmt->bind_param($types, $status, ...$fileIdArray);

        // Log the query for debugging (remove in production)
        error_log('Update Query: ' . $updateQuery);

        // Execute the update query
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request status updated to ' . $status . '.']);
        } else {
            // Log MySQL error if query fails
            error_log('MySQL Error: ' . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to update request status.']);
        }

        $updateStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'No files found for the notification.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Notification ID or status not provided.']);
}
