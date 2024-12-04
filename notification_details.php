<?php
// Include the database connection
include 'config.php';

if (isset($_GET['id'])) {
    $notificationId = $_GET['id'];  // Fetch the notification ID from the URL

    // Fetch notification and related user details
    $query = "
    SELECT 
        notifications.*, 
        uploads.user_id, 
        uploads.category,
        users.first_name,
        users.middle_name,
        users.last_name,
        users.mobile,
        users.birthday,
        users.address,
        users.birthplace,
        users.gender,
        notifications.file_ids -- Include file_ids in the query
    FROM notifications
    INNER JOIN uploads ON FIND_IN_SET(uploads.id, notifications.file_ids)
    INNER JOIN users ON uploads.user_id = users.id
    WHERE notifications.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();
    $stmt->close();

    // Fetch the file_ids as an array
    $fileIdsString = $notification['file_ids']; // Retrieve the file_ids string from the notification
    $fileIdsArray = explode(',', $fileIdsString); // Convert it into an array for processing

    // Fetch the original files associated with the notification
    $fileQuery = "SELECT id, filename, file_type, file_data, upload_date FROM uploads WHERE id IN (" . implode(',', array_fill(0, count($fileIdsArray), '?')) . ")";
    $stmt = $conn->prepare($fileQuery);
    $stmt->bind_param(str_repeat('i', count($fileIdsArray)), ...$fileIdsArray);
    $stmt->execute();
    $fileResult = $stmt->get_result();

    $files = [];
    if ($fileResult->num_rows > 0) {
        while ($row = $fileResult->fetch_assoc()) {
            $fileData = base64_encode($row['file_data']);
            $files[] = [
                'id' => $row['id'],
                'filename' => $row['filename'],
                'file_type' => $row['file_type'],
                'upload_date' => $row['upload_date'],
                'file_data' => 'data:' . $row['file_type'] . ';base64,' . $fileData
            ];
        }
    }

    // Fetch the reuploaded files
    $reuploadQuery = "SELECT id, filename, file_type, file_data, upload_date FROM uploads 
                      WHERE user_id = ? AND category = ? AND id NOT IN (" . implode(',', array_fill(0, count($fileIdsArray), '?')) . ")";
    $stmt = $conn->prepare($reuploadQuery);
    $params = array_merge([$notification['user_id'], $notification['category']], $fileIdsArray);
    $stmt->bind_param("is" . str_repeat('i', count($fileIdsArray)), ...$params);
    $stmt->execute();
    $reuploadResult = $stmt->get_result();

    $reuploadedFiles = [];
    if ($reuploadResult->num_rows > 0) {
        while ($row = $reuploadResult->fetch_assoc()) {
            $fileData = base64_encode($row['file_data']);
            $reuploadedFiles[] = [
                'id' => $row['id'],
                'filename' => $row['filename'],
                'file_type' => $row['file_type'],
                'upload_date' => $row['upload_date'],
                'file_data' => 'data:' . $row['file_type'] . ';base64,' . $fileData
            ];
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notification Details</title>
    <link href="plugins/BOOTSTRAP5/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="notification_details.css">
    <link rel="stylesheet" href="mswdDashboard.css">
    <style>
        .file-details {
            margin-bottom: 20px;
        }
        .file-details img {
            max-width: 150px;
            display: block;
            margin-bottom: 10px;
        }
        .file-details p {
            margin: 0;
            font-size: 14px;
        }

    </style>
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
            <img src="dist/img/mswdLogo.png" alt="MSWDO Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">MSWDO</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">Social Worker Admin</a>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="mswdDashboard.php" class="nav-link active">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" role="button" data-widget="pushmenu" id="notification-toggle">
                    <i class="nav-icon far fa-bell"></i>
                    <p>
                        Client's Request
                        <span class="right badge badge-warning" id="notification-count">0</span>
                    </p>
                    </a>

                    <!-- Scrollable dropdown for notifications with search bar -->
                    <ul class="nav nav-treeview direct-chat-messages overflow-auto" id="notification-menu" style="display: none;">
                    <!-- Search bar for notifications -->
                    <li class="nav-item">
                        <input type="text" class="form-control" id="notification-search" placeholder="Search by client name..." />
                    </li>

                    <!-- Notifications will be dynamically added here -->
                    <li class="nav-item">
                        <a class="nav-link">No notifications found.</a>
                    </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="clients_table.php" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Add to Record</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="individuals.php" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Records</p>
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
                        <h1>Validate Clients Assistance Request</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Details</h3>
                    </div>
                    <div class="card-body notification-details">
                        <h5><strong>Personal Information of the Client</strong></h5>
                        
                        <!-- Use Bootstrap table for structured layout -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th>First Name</th>
                                        <td><?php echo htmlspecialchars($notification['first_name']); ?></td>
                                        <th>Middle Name</th>
                                        <td><?php echo htmlspecialchars($notification['middle_name']); ?></td>
                                        <th>Last Name</th>
                                        <td><?php echo htmlspecialchars($notification['last_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td><?php echo htmlspecialchars($notification['mobile']); ?></td>
                                        <th>Birthday</th>
                                        <td><?php echo htmlspecialchars($notification['birthday']); ?></td>
                                        <th>Gender</th>
                                        <td><?php echo htmlspecialchars($notification['gender']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td colspan="5"><?php echo htmlspecialchars($notification['address']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Birthplace</th>
                                        <td colspan="5"><?php echo htmlspecialchars($notification['birthplace']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td colspan="5"><?php echo htmlspecialchars($notification['category']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    
                        <!-- Display original files with upload_date -->
                        <h5 class="mt-4"><strong>Uploaded Files:</strong></h5>
                        <div class="uploaded-files-container">
                            <div class="uploaded-files">
                                <?php
                                if (!empty($files)) {
                                    foreach ($files as $file) {
                                        echo '<div class="file-details">';
                                        echo '<img src="' . $file['file_data'] . '" alt="Uploaded File" class="enlarge-image">';
                                        echo '<p>Upload Date: ' . htmlspecialchars($file['upload_date']) . '</p>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo "<p>No files uploaded.</p>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Display reuploaded files with upload_date -->
                        <div class="uploaded-files-container">
                            <div class="uploaded-files">
                                <?php
                                if (!empty($reuploadedFiles)) {
                                    foreach ($reuploadedFiles as $file) {
                                        echo '<div class="file-details">';
                                        echo '<img src="' . $file['file_data'] . '" alt="Reuploaded File" class="enlarge-image">';
                                        echo '<p>Upload Date: ' . htmlspecialchars($file['upload_date']) . '</p>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo "<p>No reuploaded files.</p>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="comment">Comment:</label>
                            <textarea class="form-control" id="comment" rows="3"></textarea>
                        </div>
                        <button class="btn btn-primary" id="submit-comment" data-notification-id="<?php echo $notificationId; ?>">Submit Comment</button>

                        <!-- Send SMS section starts here -->
                        <hr>
                        <h3 class="mt-4">Send SMS</h3>
                        <div class="row">
                            <!-- SMS for Interview -->
                            <div class="col-md-6">
                                <form id="send-sms-interview-form">
                                    <div class="form-group">
                                        <label for="mobile"><i class="fas fa-phone-alt"></i> Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile-interview" value="<?php echo htmlspecialchars($notification['mobile']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="message-interview"><i class="fas fa-comment-alt"></i> Message</label>
                                        <textarea class="form-control sms-textarea" id="message-interview" rows="3">Hello, this is a reminder from MSWDO of Bulan Sorsogon regarding your request. Please bring the physical requirements in our office for an interview. Thank You.</textarea>
                                    </div>
                                    <button type="button" class="btn btn-info btn-block" id="send-sms-interview" data-notification-id="<?php echo $notificationId; ?>">
                                        <i class="fas fa-paper-plane"></i> Send SMS for an Interview
                                    </button>
                                </form>
                            </div>

                            <!-- SMS for Pay out -->
                            <div class="col-md-6">
                                <form id="send-sms-payout-form">
                                    <div class="form-group">
                                        <label for="mobile-payout"><i class="fas fa-phone-alt"></i> Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile-payout" value="<?php echo htmlspecialchars($notification['mobile']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="message-payout"><i class="fas fa-comment-alt"></i> Message</label>
                                        <textarea class="form-control sms-textarea" id="message-payout" rows="3">Hello, this is a reminder from MSWDO of Bulan Sorsogon that your request has been approved. Please go to the Treasury for Pay out.</textarea>
                                    </div>
                                    <button type="button" class="btn btn-success btn-block" id="send-sms-payout" data-notification-id="<?php echo $notificationId; ?>">
                                        <i class="fas fa-paper-plane"></i> Send SMS for Pay out
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- SMS for Deny -->
                        <div class="col-md-15">
                            <form id="send-sms-deny-form">
                                <div class="form-group">
                                    <label for="mobile-deny"><i class="fas fa-phone-alt"></i> Mobile Number</label>
                                    <input type="text" class="form-control" id="mobile-deny" value="<?php echo htmlspecialchars($notification['mobile']); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="message-deny"><i class="fas fa-comment-alt"></i> Message</label>
                                    <textarea class="form-control sms-textarea" id="message-deny" rows="3">Hello, this is a notification from MSWDO of Bulan Sorsogon. Unfortunately, your request has been denied. Please contact our office for further details. Thank you.</textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-block" id="send-sms-deny" data-notification-id="<?php echo $notificationId; ?>">
                                    <i class="fas fa-paper-plane"></i> Send SMS for Deny
                                </button>
                            </form>
                        </div>

            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div id="commentToast" class="toast bg-success" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Comment Status</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body" id="commentToastBody">
                        <!-- Dynamic message will be inserted here -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal for enlarging images -->
<div id="lightboxModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>
<script src="plugins/BOOTSTRAP5/js/bootstrap.bundle.min.js"></script>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script src="notifications.js"></script>
<script src="notification_details.js"></script>
</body>
</html>