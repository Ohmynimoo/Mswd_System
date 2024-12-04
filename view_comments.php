<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    die('Error: User is not logged in.');
}

$userId = $_SESSION['userid'];

// Get the notification ID if it's passed via GET
$notificationId = isset($_GET['notification_id']) ? intval($_GET['notification_id']) : null;

// Fetch comments and uploaded file information
$query = "
    SELECT 
        cn.id as notification_id, 
        cn.message, 
        c.comment, 
        c.created_at,
        u.client_name, 
        u.category, 
        u.filename,
        u.file_type,
        u.upload_date,
        u.file_data
    FROM 
        client_notifications cn
    LEFT JOIN 
        comments c ON cn.id = c.notification_id
    LEFT JOIN 
        uploads u ON cn.user_id = u.user_id
    WHERE 
        cn.user_id = ?";

$params = [$userId];

// Add notification ID filter if provided
if ($notificationId) {
    $query .= " AND cn.id = ?";
    $params[] = $notificationId;
}

$query .= " ORDER BY cn.id DESC";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// Bind the dynamic parameters (all integers for now)
$stmt->bind_param(str_repeat("i", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data structure to hold notifications and comments
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notificationId = $row['notification_id'];
    if (!isset($notifications[$notificationId])) {
        $notifications[$notificationId] = [
            'message' => $row['message'],
            'comment' => $row['comment'],
            'created_at' => $row['created_at'],
            'client_name' => $row['client_name'],
            'files' => []
        ];
    }
    // Add file information to notifications
    $notifications[$notificationId]['files'][] = [
        'filename' => $row['filename'],
        'file_type' => $row['file_type'],
        'file_data' => $row['file_data'],
        'upload_date' => $row['upload_date'],
        'category' => $row['category']
    ];
}

// Fetch user information
$sql = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, mobile, birthday, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Error: No user found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Comments</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="view_comments.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="index3.html" class="brand-link">
                <img src="dist/img/mswdLogo.png" alt="MSWDO Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">MSWDO</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block"><?php echo htmlspecialchars($user['fullname']); ?></a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="guide.php" class="nav-link">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>Guide</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./client.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Types of Assistance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./personal_information.php" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Personal Information</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./request_status.php" class="nav-link">
                                <i class="nav-icon fas fa-user"></i>
                                <p>Status</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="view_comments.php" class="nav-link active">
                                <i class="far fa-bell"></i>
                                <p>
                                    Notifications
                                    <span class="right badge badge-warning" id="notification-count">0</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Comments about your request</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                <?php if (!empty($notifications)): ?>
                    <div class="row comment-box">
                        <div class="col-md-4 col-sm-6 uploaded-file-list">
                            <ul class="list-group">
                                <?php
                                $uniqueFiles = [];
                                foreach ($notifications as $notification) {
                                    if (!empty($notification['files'])) {
                                        foreach ($notification['files'] as $file) {
                                            if (!in_array($file['filename'], $uniqueFiles)) {
                                                $uniqueFiles[] = $file['filename'];
                                                ?>
                                                <li class="list-group-item">
                                                    <?php if (strpos($file['file_type'], 'image') === 0): ?>
                                                        <img src="data:<?php echo htmlspecialchars($file['file_type']); ?>;base64,<?php echo base64_encode($file['file_data']); ?>" alt="<?php echo htmlspecialchars($file['filename']); ?>" class="img-fluid img-thumbnail">
                                                    <?php else: ?>
                                                        <a href="download.php?file_id=<?php echo htmlspecialchars($notification['notification_id']); ?>">
                                                            <i class="fas fa-file file-icon"></i> <?php echo htmlspecialchars($file['filename']); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    <p><strong>Upload Date:</strong> <?php echo htmlspecialchars($file['upload_date']); ?></p>
                                                    <p><strong>File Category:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($file['category']); ?></span></p>
                                                </li>
                                            <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        </div>

                        <div class="col-md-8 col-sm-6 comment-details">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Notification Message:</h5>
                                    <?php foreach ($notifications as $notification): ?>
                                        <p class="card-text"><?php echo htmlspecialchars($notification['message']); ?></p>

                                        <?php if (!empty($notification['comment'])): ?>
                                            <p class="card-text">
                                                <i class="fas fa-comment"></i> 
                                                <strong>Comment:</strong> 
                                                <span class="highlight-comment"><?php echo htmlspecialchars($notification['comment']); ?></span>
                                                <br>
                                                <small>
                                                    <i class="fas fa-clock"></i> 
                                                    <?php echo !empty($notification['created_at']) ? date('M d, Y h:i A', strtotime($notification['created_at'])) : 'No date available'; ?>
                                                </small>
                                            </p>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <!-- File re-upload form starts here -->
                                    <h5 class="mt-4">Re-upload Files</h5>
                                    <form id="reuploadForm" action="reupload.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="notification_id" value="<?php echo htmlspecialchars($notificationId); ?>" />
                                        <div class="form-group">
                                            <input type="file" class="form-control-file" id="reupload_files" name="reupload_files[]" multiple required onchange="validateFiles()">
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                    </form>
                                </div>
                                <div class="alert alert-danger d-none" id="file-upload-alert" role="alert">
                                    Only JPEG, JPG, and PNG files are allowed!
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No comments found.</p>
                <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.js"></script>
    <script src="view_comments.js"></script>
    <script>
        function validateFiles() {
            const allowedExtensions = ['jpeg', 'jpg', 'png'];
            const fileInput = document.getElementById('reupload_files');
            const files = fileInput.files;
            const alertBox = document.getElementById('file-upload-alert');
            
            let invalidFiles = [];
            
            for (let i = 0; i < files.length; i++) {
                const fileName = files[i].name;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(fileExtension)) {
                    invalidFiles.push(fileName);
                }
            }

            if (invalidFiles.length > 0) {
                alertBox.textContent = `Invalid files detected: ${invalidFiles.join(', ')}. Only JPEG, JPG, and PNG are allowed.`;
                alertBox.classList.remove('d-none');
                fileInput.value = ''; // Clear the file input to prevent form submission
            } else {
                alertBox.classList.add('d-none');
            }
        }
    </script>
</body>
</html>
