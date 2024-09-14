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
            <a href="mswdDashboard.php" class="nav-link">
              <i class="nav-icon fas fa-home"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="individuals.php" class="nav-link active">
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
            <h1>Assistance to Individuals in Crisis Situation (AICS) by: MSWDO of Bulan, Sorsogon</h1>
          </div>
        </div>
      </div>
    </section>

    <div class="container mt-5">
    <h2>Assistance Record Management</h2>
    <button id="addRecordButton" class="btn btn-success mb-3">Add Record</button>
    <div id="addRecordForm" class="container" style="display: none;">
        <form id="recordForm" class="form-horizontal">
            <!-- Personal Information Section -->
            <h4>Personal Information</h4>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="middleName">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middleName" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="age">Age</label>
                    <input type="number" class="form-control" id="age" name="age" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="birthPlace">Birth Place</label>
                    <input type="text" class="form-control" id="birthPlace" name="birthPlace" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="address">Address (House No., Street, Barangay)</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="mobileNumber">Mobile Number</label>
                    <input type="number" class="form-control" id="mobileNumber" name="mobileNumber" min="0" max="99999999999" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="education">Educational Attainment</label>
                    <input type="text" class="form-control" id="education" name="education" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="income">Income Per Day</label>
                    <input type="number" class="form-control" id="income" name="income" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="occupation">Occupation</label>
                    <input type="text" class="form-control" id="occupation" name="occupation" required>
                </div>
            </div>

            <!-- Assistance Information Section -->
            <h4>Assistance Information</h4>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="clientType">Client Type</label>
                    <select class="form-control" id="clientType" name="clientType" required>
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
  
    <table id="example2" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>More Informations</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>

 <div class="modal fade" id="recordModal" tabindex="-1" role="dialog" aria-labelledby="recordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recordModalLabel">Record Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editRecordButton">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRecordModal" tabindex="-1" role="dialog" aria-labelledby="editRecordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editRecordModalLabel">Edit Record</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="editForm">
              <!-- Form fields here -->
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveEditButton">Save Changes</button>
          </div>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src ="records.js"> </script>
<script src ="notifications.js"> </script>
<script src ="add_fam_member.js"> </script>
</body>
</html>
