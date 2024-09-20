<?php
// notification_details.php
include 'config.php';

if (isset($_GET['id'])) {
    $notificationId = $_GET['id'];

    // Modify the query to include the category
    $query = "
    SELECT 
        notifications.*,
        uploads.user_id,
        uploads.category, -- Fetch the category from uploads table
        users.first_name,
        users.middle_name,
        users.last_name,
        users.mobile,
        users.birthday,
        users.address,
        users.birthplace,
        users.gender
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

    // Fetch the files associated with the notification
    $fileIds = explode(',', $notification['file_ids']);
    $fileIdsString = implode("','", $fileIds);
    $fileQuery = "SELECT filename, file_type, file_data FROM uploads WHERE id IN ('$fileIdsString')";
    $fileResult = $conn->query($fileQuery);

    $files = [];
    if ($fileResult->num_rows > 0) {
        while ($row = $fileResult->fetch_assoc()) {
            $fileData = base64_encode($row['file_data']);
            $files[] = [
                'filename' => $row['filename'],
                'file_type' => $row['file_type'],
                'file_data' => 'data:'. $row['file_type']. ';base64,'. $fileData
            ];  
        }
    }

    $conn->close();
} else {
    echo "No notification ID provided.";
    exit;
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

    <!-- Updated Sidebar Menu -->
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
                        <a href="individuals.php" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Individuals</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" data-widget="pushmenu">
                        <i class="nav-icon far fa-bell"></i>
                        <p>
                            Notifications
                            <span class="right badge badge-warning" id="notification-count">0</span>
                        </p>
                        </a>
                        <ul class="nav nav-treeview" id="notification-menu">
                        <li class="nav-item">
                            <a class="nav-link">No Notifications</a>
                        </li>
                        </ul>
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
                        <h1>Assistance Request</h1>
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
                        <!-- Personal details in horizontal layout using Bootstrap grid -->
                        <div class="row mb-10">
                            <div class="col-md-2">
                                <strong>First Name:</strong> <?php echo htmlspecialchars($notification['first_name']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Middle Name:</strong> <?php echo htmlspecialchars($notification['middle_name']); ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Last Name:</strong> <?php echo htmlspecialchars($notification['last_name']); ?>
                            </div>
                        </div>
                        <div class="row mb-10">
                            <div class="col-md-2">
                                <strong>Mobile:</strong> <?php echo htmlspecialchars($notification['mobile']); ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Birthday:</strong> <?php echo htmlspecialchars($notification['birthday']); ?>
                            </div>
                            <div class="col-md-2">
                                <strong>Gender:</strong> <?php echo htmlspecialchars($notification['gender']); ?>
                            </div>
                        </div>
                        <div class="row mb-10">
                            <div class="col-md-6">
                                <strong>Address:</strong> <?php echo htmlspecialchars($notification['address']); ?>
                            </div>
                            <div class="col-md-10">
                                <strong>Birthplace:</strong> <?php echo htmlspecialchars($notification['birthplace']); ?>
                            </div>
                        </div>
                        <div class="row mb-10">
                            <div class="col-md-10">
                                <strong>Category:</strong> <?php echo htmlspecialchars($notification['category']); ?>
                            </div>
                        </div>
                        
                        <h5 class="mt-4"><strong>Uploaded Files:</strong></h5>
                        <div class="uploaded-files-container">
                            <div class="uploaded-files">
                                <?php foreach ($files as $file): ?>
                                    <img src="<?php echo $file['file_data']; ?>" alt="<?php echo htmlspecialchars($file['filename']); ?>" class="enlarge-image">
                                <?php endforeach; ?>
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

                        <!-- Denied Button -->
                        <div class="mt-3">
                            <button type="button" class="btn btn-danger btn-block" id="deny-request" data-notification-id="<?php echo $notificationId; ?>">
                                <i class="fas fa-times-circle"></i> Deny Request
                            </button>
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