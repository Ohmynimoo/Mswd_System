<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ensure user ID is set in the session
$userId = $_SESSION['userid'] ?? null; 
$user = null;
// Fetch user details if the user is logged in
if ($userId) {
    $conn = new mysqli("localhost", "root", "", "mswd_system");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Fetch the user's details (fullname, mobile, etc.)
    $sql = "SELECT fullname, mobile, birthday, address FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}
// Ensure that the category is retained across form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? null;
    $_SESSION['category'] = $category; // Store category in session

    // Process file uploads
    $files = $_FILES['files'];
    $uploadOk = 1;
    $allowedTypes = ["jpg", "png", "jpeg", "gif"];
    $uploadedFiles = [];
    $fileIds = [];
    $existingFiles = [];
    // Clear previous error or success messages
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    // Fetch previously uploaded files by the user for the same category
    $stmt = $conn->prepare("SELECT filename FROM uploads WHERE user_id = ? AND category = ?");
    $stmt->bind_param("is", $userId, $category);
    $stmt->execute();
    $existingResult = $stmt->get_result();
    
    while ($row = $existingResult->fetch_assoc()) {
        $existingFiles[] = $row['filename'];
    }
    $stmt->close();
    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = basename($files["name"][$i]);
        $fileTmpName = $files["tmp_name"][$i];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $files["size"][$i];
        // Validate file size (max 5MB)
        if ($fileSize > 5000000) {
            $_SESSION['error_message'][] = "File " . htmlspecialchars($fileName) . " is too large (max size: 5MB).";
            $uploadOk = 0;
        }
        // Validate file type
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error_message'][] = "File " . htmlspecialchars($fileName) . " has an invalid type. Allowed types are: " . implode(", ", $allowedTypes) . ".";
            $uploadOk = 0;
        }
        // Check for duplicate files
        if (in_array($fileName, $existingFiles)) {
            $_SESSION['error_message'][] = "File " . htmlspecialchars($fileName) . " has already been submitted.";
            $uploadOk = 0;
        }
        // Proceed with upload if no issues
        if ($uploadOk == 1) {
            $fileData = file_get_contents($fileTmpName);
            $fileType = mime_content_type($fileTmpName);
            $stmt = $conn->prepare("INSERT INTO uploads (user_id, category, filename, file_data, file_type, upload_date) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("issss", $userId, $category, $fileName, $fileData, $fileType);
            if ($stmt->execute()) {
                $fileIds[] = $stmt->insert_id;
                $uploadedFiles[] = $fileName;
            } else {
                $_SESSION['error_message'][] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    // Notify user and add notification if files were successfully uploaded
    if ($uploadOk == 1 && !empty($uploadedFiles)) {
        $message = htmlspecialchars($user['fullname']) . " has uploaded new files in category: " . htmlspecialchars($category);
        $fileNames = implode(", ", $uploadedFiles);
        $fileIdsStr = implode(",", $fileIds);

        $stmt = $conn->prepare("INSERT INTO notifications (client_name, mobile, birthday, address, category, message, file_names, file_ids) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $user['fullname'], $user['mobile'], $user['birthday'], $user['address'], $category, $message, $fileNames, $fileIdsStr);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Your files have been successfully submitted to the MSWD of Bulan.";
        } else {
            $_SESSION['error_message'][] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
    // Redirect back to the upload page
    header("Location: upload.php");
    exit();
}
?>
