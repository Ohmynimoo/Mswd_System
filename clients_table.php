<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clients Records</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
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
          <a href="" class="d-block">Social Worker Admin</a>
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

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="display-3">Client's Assistance Request Management</h1>
          </div>
        </div>
      </div>
    </section>

    <div class="container mt-1">
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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
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

  <!-- Table Initialization -->
  <script>
  $(document).ready(function() {
    // Destroy the DataTable if it already exists to avoid duplication
    if ($.fn.DataTable.isDataTable('#clientsTable')) {
      $('#clientsTable').DataTable().clear().destroy();
    }

    // Initialize the DataTable
    var table = $('#clientsTable').DataTable({
      "ajax": {
        "url": "fetch_clients.php",  // Ensure this is the correct endpoint
        "dataSrc": function(json) {
          // Filter out entries with birthday = "0000-00-00"
          return json.filter(function(client) {
            return client.birthday !== "0000-00-00";
          });
        },
        "cache": false,
        "error": function(xhr, error, thrown) {
          console.error("Error loading data:", xhr.responseText);
          alert("Failed to load data. Check console for details.");
        }
      },
      "columns": [
        { "data": "first_name" },
        { "data": "last_name" },
        { "data": "middle_name" },
        { "data": "mobile" },
        {
          "data": "birthday",
          "render": function(data, type, row, meta) {
            if (data === null || data === "0000-00-00" || data === "") {
              return "N/A";  // Display 'N/A' for invalid dates
            }
            return data;  // Return valid birthday
          }
        },
        { "data": "address" },
        { "data": "birthplace" },
        {
          "data": "id",
          "render": function(data, type, row, meta) {
            return `
              <div class="btn-group" role="group">
                <a href="client_details.php?client_id=${data}" class="btn btn-primary btn-sm">Notify Client</a>
                <a href="add_clients_approved.php?client_id=${data}&show_form=true" class="btn btn-success btn-sm">Add to Record</a>
              </div>`;
          }
        }
      ]
    });

    // Reload table after an update
    table.ajax.reload(null, false);
  });
</script>


</div>
</body>
</html>