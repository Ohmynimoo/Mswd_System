<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
include 'config.php';

// Function to display an error message and log it
function displayError($message) {
    error_log($message);  // Log the error for debugging
    echo "<script>alert('Error: " . htmlspecialchars($message) . "');</script>";
    exit;
}

// Debugging: Check if notification_id is received via POST
if (isset($_POST['notification_id'])) {
    echo "Received Notification ID: " . htmlspecialchars($_POST['notification_id']);
} else {
    echo "Notification ID not received.";
    displayError("Missing notification ID.");
}

// Check if the notification ID and new file are defined
if (isset($_POST['notification_id']) && !empty(trim($_POST['notification_id'])) && isset($_FILES['new_file'])) {
    $notificationId = trim($_POST['notification_id']);
    $newFile = $_FILES['new_file'];

    // Validate that the notification ID exists in the notifications table
    $checkQuery = "SELECT id FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($checkQuery);
    if (!$stmt) {
        displayError("Database error: " . $conn->error);
    }

    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        // If notification ID does not exist, log and display the error
        displayError("Notification ID does not exist: " . $notificationId);
    }

    $stmt->close();

    // Check if the file upload was successful
    if ($newFile['error'] === UPLOAD_ERR_OK) {
        // Get the file type and name
        $fileType = mime_content_type($newFile['tmp_name']);
        $fileName = basename($newFile['name']);
        $fileData = file_get_contents($newFile['tmp_name']); // Read the file data

        // Insert the new file into the reuploaded_files table
        $query = "INSERT INTO reuploaded_files (notification_id, filename, file_type, file_data) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            displayError("Database error: " . $conn->error);
        }

        $stmt->bind_param("isss", $notificationId, $fileName, $fileType, $fileData);
        
        // Execute the query
        if ($stmt->execute()) {
            // If successful, display a success alert using JavaScript
            echo "<script>alert('File successfully reuploaded.');</script>";
        } else {
            displayError("SQL execution failed: " . $stmt->error);
        }

        $stmt->close();
    } else {
        // Handle file upload error
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
        ];

        $errorCode = $newFile['error'];
        $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : "Unknown upload error.";
        displayError("File upload error: " . $errorMessage);
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle cases where the notification ID or file was not provided
    if (!isset($_POST['notification_id']) || empty(trim($_POST['notification_id']))) {
        displayError("Missing notification ID.");
    } elseif (!isset($_FILES['new_file'])) {
        displayError("No file uploaded.");
    } else {
        displayError("Unknown error occurred.");
    }
    exit;
}
?>
