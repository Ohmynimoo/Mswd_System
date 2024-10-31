//reupload.php
<?php
session_start();
include 'config.php'; // Include the database connection config

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    die('Error: User is not logged in.');
}

$userId = $_SESSION['userid'];

// Get the notification ID from the form
$notificationId = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : null;

// Ensure the notification ID is valid
if (!$notificationId) {
    die('Error: Invalid notification ID.');
}

// Ensure files were uploaded
if (!isset($_FILES['reupload_files'])) {
    die('Error: No files uploaded.');
}

// Fetch the user's details (client_name) and category from `client_notifications`
$query = "SELECT cn.message, u.client_name, u.category 
          FROM client_notifications cn
          JOIN uploads u ON cn.user_id = u.user_id
          WHERE cn.id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Error in preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $notificationId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $notificationData = $result->fetch_assoc();
    $clientName = $notificationData['client_name'];
    $category = $notificationData['category'];
} else {
    die('Error: No notification data found.');
}

// Process reuploaded files
$files = $_FILES['reupload_files'];
$allowedTypes = ['jpg', 'jpeg', 'png']; // Allowed file types
$uploadedFiles = [];
$fileIds = [];
$uploadOk = 0; // Initialize uploadOk to 0 initially

for ($i = 0; $i < count($files['name']); $i++) {
    $fileName = basename($files['name'][$i]);
    $fileTmpName = $files['tmp_name'][$i];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $fileType = mime_content_type($fileTmpName);

    // Check if the file has an allowed type and extension
    if (in_array($fileExtension, $allowedTypes) && preg_match('/^image\/(jpeg|jpg|png)$/', $fileType)) {
        $fileData = file_get_contents($fileTmpName);

        // Insert the new file into the uploads table
        $stmt = $conn->prepare("INSERT INTO uploads (user_id, client_name, category, filename, file_data, file_type, upload_date) 
                                VALUES (?, ?, ?, ?, ?, ?, NOW())");

        if ($stmt === false) {
            die("Error in preparing statement: " . $conn->error);
        }

        $stmt->bind_param("isssss", $userId, $clientName, $category, $fileName, $fileData, $fileType);

        if ($stmt->execute()) {
            $uploadedFiles[] = $fileName;
            $fileIds[] = $stmt->insert_id; // Collect the inserted file ID
            $uploadOk = 1; // Set to 1 to indicate at least one file was successfully uploaded
        } else {
            $_SESSION['error_message'][] = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'][] = "File type not allowed for file: " . htmlspecialchars($fileName);
    }
}

// Update the notification if files were successfully uploaded
if (!empty($fileIds)) {
    // Fetch the current file_ids from the notification
    $stmt = $conn->prepare("SELECT file_ids FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $stmt->bind_result($currentFileIds);
    $stmt->fetch();
    $stmt->close();

    // Combine old file_ids with the new file_ids from reupload
    $allFileIds = array_merge(explode(',', $currentFileIds), $fileIds);
    $updatedFileIds = implode(',', $allFileIds);

    // Update the notification record with the new file_ids
    $stmt = $conn->prepare("UPDATE notifications SET file_ids = ? WHERE id = ?");
    $stmt->bind_param("si", $updatedFileIds, $notificationId);
    $stmt->execute();
    $stmt->close();
}

// Notify user and add notification if files were successfully uploaded
if ($uploadOk == 1 && !empty($uploadedFiles)) {
    // Assuming $user array is defined earlier in the code
    $message = htmlspecialchars($clientName) . " has uploaded new files in category: " . htmlspecialchars($category);
    $fileNames = implode(", ", $uploadedFiles);
    $fileIdsStr = implode(",", $fileIds);  // Convert file IDs array to string

    $stmt = $conn->prepare("INSERT INTO notifications (client_name, mobile, birthday, address, category, message, file_names, file_ids) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $clientName, $user['mobile'], $user['birthday'], $user['address'], $category, $message, $fileNames, $fileIdsStr);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Your files have been successfully submitted to the MSWD of Bulan.";
        } else {
            $_SESSION['error_message'][] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        die("Error preparing notification statement: " . $conn->error);
    }
}

$conn->close();

// Redirect back to the comments or notifications page
header("Location: view_comments.php?notification_id=" . $notificationId);
exit();

?>
