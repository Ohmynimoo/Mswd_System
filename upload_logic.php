<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user ID is set in the session
$userId = $_SESSION['userid'] ?? null;
$user = null;

if ($userId) {
    // Establish database connection
    $conn = new mysqli("localhost", "root", "", "mswd_system");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the user's details from the `users` table
    $sql = "SELECT first_name, middle_name, last_name, mobile, birthday, address, birthplace FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the birthday is invalid (0000-00-00 or empty) and set it to NULL
        if ($user['birthday'] === '0000-00-00' || empty($user['birthday'])) {
            $user['birthday'] = null; // Set as NULL if invalid date
        }
    }
    $stmt->close();

    // Ensure that the category is retained across form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $category = $_POST['category'] ?? null;
        $_SESSION['category'] = $category; // Store category in session

        // Insert client data into `clients` table if not already exists
        $stmt = $conn->prepare("INSERT IGNORE INTO clients (id, first_name, middle_name, last_name, mobile, birthday, address, birthplace, category) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error in preparing statement: " . $conn->error);
        }

        $stmt->bind_param("issssisss", $userId, $user['first_name'], $user['middle_name'], $user['last_name'], $user['mobile'], $user['birthday'], $user['address'], $user['birthplace'], $category);
        $stmt->execute();
        $stmt->close();

        // Process file uploads
        $files = $_FILES['files'];
        $uploadOk = 1;
        $allowedTypes = ["jpg", "png", "jpeg", "gif"];
        $uploadedFiles = [];
        $fileIds = [];  // Initialize fileIds array

        // Loop through uploaded files
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = basename($files["name"][$i]);
            $fileTmpName = $files["tmp_name"][$i];
            $fileType = mime_content_type($fileTmpName);
            $fileData = file_get_contents($fileTmpName);

            $stmt = $conn->prepare("INSERT INTO uploads (user_id, client_name, category, filename, file_data, file_type, upload_date) 
                                    VALUES (?, ?, ?, ?, ?, ?, NOW())");

            if ($stmt === false) {
                die("Error in preparing statement: " . $conn->error);
            }

            // Concatenate first, middle, and last name to form client_name
            $clientName = trim($user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']);
            $stmt->bind_param("isssss", $userId, $clientName, $category, $fileName, $fileData, $fileType);

            if ($stmt->execute()) {
                $uploadedFiles[] = $fileName;
                $fileIds[] = $stmt->insert_id;  // Collect the inserted file ID
            } else {
                $_SESSION['error_message'][] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }

        // Notify user and add notification if files were successfully uploaded
        if ($uploadOk == 1 && !empty($uploadedFiles)) {
            $message = htmlspecialchars($clientName) . " has uploaded new files in category: " . htmlspecialchars($category);
            $fileNames = implode(", ", $uploadedFiles);
            $fileIdsStr = implode(",", $fileIds);  // Convert file IDs array to string

            $stmt = $conn->prepare("INSERT INTO notifications (client_name, mobile, birthday, address, category, message, file_names, file_ids) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $clientName, $user['mobile'], $user['birthday'], $user['address'], $category, $message, $fileNames, $fileIdsStr);

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
}
