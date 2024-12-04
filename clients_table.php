<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients Records</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .main-header {
            border-radius: 5px;
            text-align: center;
        }
        .main-header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .main-header h1 i {
            margin-right: 10px;
        }
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .breadcrumb-item a {
            color: #007bff;
        }
        .table {
            margin-bottom: 30px;
            border-radius: 5px;
            overflow: hidden;
        }
        .table thead {
            background: #007bff;
            color: white;
        }
        .table tbody tr:hover {
            background: #f1f1f1;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }
        .status-approved {
            background: #28a745;
            color: white;
        }
        .status-pending {
            background: #ffc107;
            color: black;
        }
        .status-processing {
            background: #17a2b8;
            color: white;
        }
        .status-denied {
            background: #dc3545;
            color: white;
        }
        .no-request-status {
            background: #6c757d;
            color: white;
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
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
    </ul>
  </nav>
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
            <a class="nav-link" href="#" role="button" data-widget="pushmenu" id="notification-toggle">
              <i class="nav-icon far fa-bell"></i>
              <p>
                Client's Request
                <span class="right badge badge-warning" id="notification-count">3</span>
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="clients_table.php" class="nav-link">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>Add to Record</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="individuals.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
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

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="main-header">
          <h1><i class="fas fa-file-alt"></i> Client's Assistance Request Management</h1>
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="mswdDashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Assistance Requests</li>
          </ol>
        </div>
      </div>
    </section>

    <div class="container mt-3">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">List of Individuals Requesting Assistance</h3>
        </div>
        <div class="card-body">
          <table id="clientsTable" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>Mobile Number</th>
                <th>Birthday</th>
                <th>Address</th>
                <th>Birthplace</th>
                <th>Status</th>
                <th>Add to Record</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
 <script>
    $(document).ready(function() {
    $('#clientsTable').DataTable({
    "ajax": {
      "url": "fetch_clients.php",
      "dataSrc": function(json) {
        return json.map(client => ({
          ...client,
          status: `<span class="status-badge ${
            client.request_status === 'Approved' ? 'status-approved' :
            client.request_status === 'Pending' ? 'status-pending' :
            client.request_status === 'Processing' ? 'status-processing' :
            client.request_status === 'Denied' ? 'status-denied' : 'no-request-status'
          }">${client.request_status}</span>`,
          actions: client.request_status === 'Approved' 
            ? `<a href="add_clients_approved.php?client_id=${client.id}" class="btn btn-success btn-sm" title="Add to Record">
                  <i class="fas fa-check"></i>
              </a>` 
            : client.request_status === 'Denied'
            ? `<button class="btn btn-danger btn-sm delete-client" data-id="${client.id}" title="Delete Client">
                  <i class="fas fa-trash"></i>
              </button>` 
            : `<button class="btn btn-secondary btn-sm" disabled title="Cannot Add to Record">
                  <i class="fas fa-times"></i>
              </button>`
        }));
      }
    },
    "columns": [
      { "data": "first_name" },
      { "data": "last_name" },
      { "data": "middle_name" },
      { "data": "mobile" },
      { "data": "birthday" },
      { "data": "address" },
      { "data": "birthplace" },
      { "data": "status", "orderable": false },
      { "data": "actions", "orderable": false }
    ]
  });
  // Handle client deletion
  $('#clientsTable').on('click', '.delete-client', function() {
    const clientId = $(this).data('id');
    if (confirm('Are you sure you want to delete this client?')) {
      $.ajax({
        url: 'denied_client.php',
        method: 'POST',
        data: { client_id: clientId },
        success: function(response) {
          if (response.success) {
            alert('Client successfully deleted.');
            $('#clientsTable').DataTable().ajax.reload(); // Refresh the table
          } else {
            alert('Failed to delete client: ' + response.message);
          }
        },
        error: function() {
          alert('An error occurred while deleting the client.');
        }
      });
    }
  });
});
</script>
</body>
</html>