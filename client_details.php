<?php
// Database connection (use your actual connection details)
$host = 'localhost';
$dbname = 'mswd_system';
$user = 'root';
$pass = '';

try {
    // Establish database connection using PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get client ID from the query string
if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];

    // Fetch client details from the database
    $stmt = $conn->prepare("SELECT first_name, last_name, mobile FROM clients WHERE id = ?");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        echo "Client not found.";
        exit;
    }

    // Define notification details if needed (dummy for now)
    $notificationId = 123;  // Example notification ID (replace with real data as needed)
} else {
    echo "No client ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Details</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    
    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="landing_page.php" class="brand-link">
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
                        <a href="clients_table.php" class="nav-link">
                            <i class="nav-icon far fa-bell"></i>
                            <p>Clients</p>
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
    <!-- Sidebar End -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!-- You can add breadcrumb or other elements here -->
                </div>
            </div>
        </section>

        <div class="container mt-5">
            <!-- Display client's name only -->
            <h2>Client Name: <?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></h2>

            <!-- Send SMS section starts here -->
            <hr>
            <h3 class="mt-4">Send SMS</h3>
            <div class="row">
                <!-- SMS for Interview -->
                <div class="col-md-6">
                    <form id="send-sms-interview-form">
                        <div class="form-group">
                            <label for="mobile"><i class="fas fa-phone-alt"></i> Mobile Number</label>
                            <input type="text" class="form-control" id="mobile-interview" value="<?php echo htmlspecialchars($client['mobile']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="message-interview"><i class="fas fa-comment-alt"></i> Message</label>
                            <textarea class="form-control sms-textarea" id="message-interview" rows="3">Hello, this is a reminder from MSWDO of Bulan Sorsogon regarding your request. Please bring the physical requirements to our office for an interview. Thank you.</textarea>
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
                            <input type="text" class="form-control" id="mobile-payout" value="<?php echo htmlspecialchars($client['mobile']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="message-payout"><i class="fas fa-comment-alt"></i> Message</label>
                            <textarea class="form-control sms-textarea" id="message-payout" rows="3">Hello, this is a reminder from MSWDO of Bulan Sorsogon that your request has been approved. Please go to the Treasury for pay out.</textarea>
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
            <!-- Send SMS section ends here -->

            <!-- Back button -->
            <a href="clients_table.php" class="btn btn-secondary mt-5">Back to Clients Table</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>

    <script>
        // Handle SMS for interview
        $('#send-sms-interview').on('click', function() {
            const notificationId = $(this).data('notification-id');
            const mobile = $('#mobile-interview').val();
            const message = $('#message-interview').val();

            // Logic to send SMS (replace with actual AJAX request)
            alert('Sending SMS for Interview to ' + mobile + ': ' + message);
        });

        // Handle SMS for payout
        $('#send-sms-payout').on('click', function() {
            const notificationId = $(this).data('notification-id');
            const mobile = $('#mobile-payout').val();
            const message = $('#message-payout').val();

            // Logic to send SMS (replace with actual AJAX request)
            alert('Sending SMS for Pay out to ' + mobile + ': ' + message);
        });

        // Handle Deny request
        $('#deny-request').on('click', function() {
            const notificationId = $(this).data('notification-id');

            // Logic to deny request (replace with actual AJAX request)
            alert('Request Denied for Notification ID: ' + notificationId);
        });
    </script>
</div>
</body>
</html>