<?php
// Connect to the database
include 'config.php';

// Check if client_id is provided in the URL
$client_id = isset($_GET['client_id']) ? intval($_GET['client_id']) : 0;

// Initialize empty variables for client data
$firstName = $lastName = $middleName = $mobile = $birthday = $address = $birthplace = "";

// If client_id is available, fetch data from the clients table
if ($client_id > 0) {
    $sql = "SELECT first_name, last_name, middle_name, mobile, birthday, address, birthplace FROM clients WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Populate variables with client data
        $row = $result->fetch_assoc();
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $middleName = $row['middle_name'];
        $mobile = $row['mobile'];
        $birthday = $row['birthday'];
        $address = $row['address'];
        $birthplace = $row['birthplace'];
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Individuals Records</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
            <h1>Assistance to Individuals in Crisis Situation (AICS) by: MSWDO of Bulan, Sorsogon</h1>
          </div>
        </div>
      </div>
    </section>

    <div class="container mt-5">
      <h2>Assistance Record Management</h2>
      <button id="addRecordButton" class="btn btn-success mb-3">Add Record</button>
      
      <form id="recordForm" class="form-horizontal" action="delete_client.php" method="POST">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <h4>Personal Information</h4>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label for="middleName">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middleName" value="<?php echo $middleName; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName; ?>" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="birthPlace">Birth Place</label>
            <input type="text" class="form-control" id="birthPlace" name="birthPlace" value="<?php echo $birthplace; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
          </div>
          <div class="form-group col-md-4">
            <label for="mobileNumber">Mobile Number</label>
            <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" value="<?php echo $mobile; ?>" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="education">Educational Attainment</label>
            <input type="text" class="form-control" id="education" name="education" required>
          </div>
          <div class="form-group col-md-4">
            <label for="income">Income Per Day</label>
            <input type="number" class="form-control" id="income" name="income" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="occupation">Occupation</label>
            <input type="text" class="form-control" id="occupation" name="occupation" required>
          </div>
          <div class="form-group col-md-4">
            <label for="birthday">Birth Date</label>
            <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo $birthday; ?>" required onchange="calculateAge()">
          </div>
          <div class="form-group col-md-4">
            <label for="age">Age</label>
            <input type="text" class="form-control" id="age" name="age" readonly>
          </div>
        </div>
        
        <!-- Assistance Information Section -->
        <h4>Assistance Information</h4>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="clientType">Client Type</label>
            <select class="form-control" id="clientType" name="clientType">
              <option value="">Select Client Type</option>
              <option value="4ps">4ps</option>
              <option value="Senior Citizen">Senior Citizen</option>
              <option value="PWD">Person With Disabilities (PWD)</option>
              <option value="Solo Parent">Solo Parent</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="assistanceType">Assistance Type</label>
            <select class="form-control" id="assistanceType" name="assistanceType" required>
              <option value="">Select Assistance Type</option>
              <option value="Medical Assistance">Medical Assistance</option>
              <option value="Burial Assistance">Burial Assistance</option>
              <option value="Transportation Assistance">Transportation Assistance</option>
              <option value="Educational Assistance">Educational Assistance</option>
              <option value="Emergency Shelter Assistance">Emergency Shelter Assistance</option>
              <option value="Livelihood Assistance">Livelihood Assistance</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="fundType">Fund Type</label>
            <select class="form-control" id="fundType" name="fundType" required>
              <option value="">Select Fund Type</option>
              <option value="LGU Fund">LGU Fund</option>
              <option value="Barangay Fund">Barangay Fund</option>
              <option value="SK Fund">SK Fund</option>
            </select>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
          </div>
          <div class="form-group col-md-4">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
          </div>
          <div class="form-group col-md-4">
            <label for="beneficiary">Beneficiary</label>
            <input type="text" class="form-control" id="beneficiary" name="beneficiary" required>
          </div>
        </div>

        <!-- Family Members Section -->
        <h3>Family Members</h3>
        <div id="familyMembersContainer" class="container">
          <div class="card mb-3">
            <div class="card-header">
              <h4>Head of the Family</h4>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="familyLastName[]">Last Name</label>
                  <input type="text" class="form-control" name="familyLastName[]" required>
                </div>
                <div class="form-group col-md-4">
                  <label for="familyFirstName[]">First Name</label>
                  <input type="text" class="form-control" name="familyFirstName[]" required>
                </div>
                <div class="form-group col-md-4">
                  <label for="familyMiddleName[]">Middle Name</label>
                  <input type="text" class="form-control" name="familyMiddleName[]">
                </div>
              </div>

              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="familyDateOfBirth[]">Date of Birth</label>
                  <input type="date" class="form-control" name="familyDateOfBirth[]" required>
                </div>
                <div class="form-group col-md-4">
                  <label for="familyGender[]">Gender</label>
                  <select class="form-control" name="familyGender[]" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label for="familyRelationship[]">Relationship</label>
                  <input type="text" class="form-control" name="familyRelationship[]" value="Head" required>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Button to add more family members -->
        <button type="button" class="btn btn-primary mb-3" id="addFamilyMember">Add Family Member</button><br>

        <!-- Submit button -->
        <button type="submit" class="btn btn-success">Submit</button>
      </form>
    </div>

  <script>
    // Toggle form visibility
    document.getElementById('addRecordButton').addEventListener('click', function() {
      var form = document.getElementById('addRecordForm');
      form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });

    // Calculate age from birth date
    function calculateAge() {
      const birthdayInput = document.getElementById('birthday').value;
      if (birthdayInput) {
        const birthDate = new Date(birthdayInput);
        const diff = Date.now() - birthDate.getTime();
        const ageDate = new Date(diff);
        const age = Math.abs(ageDate.getUTCFullYear() - 1970);
        document.getElementById('age').value = age;
      } else {
        document.getElementById('age').value = '';
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const birthdayInput = document.getElementById('birthday');
      if (birthdayInput.value) {
        calculateAge();
      }
    });

    // Handle form submission (without toast notification)
    document.querySelector('form#recordForm').addEventListener('submit', function(event) {
      event.preventDefault();  // Prevent actual form submission

      var clientId = document.querySelector('input[name="client_id"]').value;

      // AJAX request to delete client's information
      fetch('delete_client.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'client_id=' + clientId
      })
      .then(response => response.text())
      .then(data => {
          console.log(data);
          document.getElementById('recordForm').reset();
          document.getElementById('age').value = '';
      })
      .catch(error => console.error('Error:', error));
    });
  </script>

  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  <script src="records.js"></script>
  <script src="notifications.js"></script>
  <script src="add_fam_member.js"></script>
</body>
</html>