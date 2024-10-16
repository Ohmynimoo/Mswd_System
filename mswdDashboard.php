<?php
session_start();

// Check if the admin is logged in and has admin privileges
if (!isset($_SESSION['admin_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MSWD Admin</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="mswdDashboard.css">
  <script src="libs/chart.js"></script>
  <script src="libs/chartjs-adapter-date-fns.js"></script>
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
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="display-3">Assistance to Individuals in Crisis Situation <b>Data Visualization</b></h1>
          </div>
        </div>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="chart-container">
          <div class="year-selector">
            <label for="year">Select Year:</label>
            <select id="year" class="modern-dropdown"></select>
          </div>
          <div class="month-selector">
            <label for="month-from">From:</label>
            <select id="month-from" class="modern-dropdown">
              <option value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
            </select>
            <label for="month-to">To:</label>
            <select id="month-to" class="modern-dropdown">
              <option value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
            </select>
          </div>
          <div class="pie-charts">
            <div class="pie-chart-container">
              <canvas id="pieChart1"></canvas>
            </div>
            <div class="pie-chart-container">
              <canvas id="pieChart2"></canvas>
            </div>
            <div class="pie-chart-container">
              <canvas id="pieChart3"></canvas>
            </div>
          </div>
          <!-- Dropdown to select chart type -->
          <label for="chart-type">Select Chart Type:</label>
          <select id="chart-type" class="modern-dropdown">
              <option value="bar">Bar Chart</option>
              <option value="pie">Pie Chart</option>
              <option value="line">Line Chart</option>
          </select>
          <!-- Unified dynamic chart -->
          <canvas id="dynamicChart"></canvas>
        </div>
      </div>
    </section>
  </div>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
<script src="data_visualization.js"></script>
<script src="notifications.js"></script>
<script>
    let currentChart;  // Store reference to the current chart
    function fetchData(year, startMonth, endMonth) {
        return $.ajax({
            url: 'data_visualization.php',
            method: 'GET',
            data: {
                year: year,
                startMonth: startMonth,
                endMonth: endMonth
            },
            dataType: 'json'
        });
    }
    function updateChart(type, data) {
        if (currentChart) {
            currentChart.destroy();  // Destroy previous chart instance
        }
        const labels = data.map(item => `${item.month}/${item.year}`);
        const lguData = data.map(item => item.lgu_count);
        const barangayData = data.map(item => item.barangay_count);
        const skData = data.map(item => item.sk_count);
        const chartData = {
            labels: labels,
            datasets: [
                {
                    label: 'LGU Fund',
                    data: lguData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                    label: 'Barangay Fund',
                    data: barangayData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                },
                {
                    label: 'SK Fund',
                    data: skData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                }
            ]
        };
        currentChart = new Chart(document.getElementById('dynamicChart').getContext('2d'), {
            type: type,  // Chart type (bar, pie, line, etc.)
            data: chartData,
            options: {
                responsive: true,
                scales: type === 'bar' || type === 'line' ? {
                    y: {
                        beginAtZero: true
                    }
                } : {},  // Scales only for bar/line charts
                plugins: {
                    title: {
                        display: true,
                        text: 'Assistance by Month'
                    },
                    legend: {
                        display: type !== 'bar',  // Show legend for pie/line charts
                        position: 'top'
                    }
                }
            }
        });
    }
    document.getElementById('chart-type').addEventListener('change', function() {
        const selectedType = this.value;
        const year = $('#year').val();
        const startMonth = $('#month-from').val();
        const endMonth = $('#month-to').val();

        fetchData(year, startMonth, endMonth).done(function(data) {
            updateChart(selectedType, data);
        });
    });

    $('#year, #month-from, #month-to').on('change', function() {
        const selectedType = $('#chart-type').val();
        const year = $('#year').val();
        const startMonth = $('#month-from').val();
        const endMonth = $('#month-to').val();

        fetchData(year, startMonth, endMonth).done(function(data) {
            updateChart(selectedType, data);
        });
    });

    $(document).ready(function() {
        const year = $('#year').val();
        const startMonth = $('#month-from').val();
        const endMonth = $('#month-to').val();
        const selectedType = $('#chart-type').val();

        fetchData(year, startMonth, endMonth).done(function(data) {
            updateChart(selectedType, data);
        });
    });
</script>
</body>
</html>