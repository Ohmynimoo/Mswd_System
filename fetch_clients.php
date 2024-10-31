<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mswd_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Query to fetch client data, handling '0000-00-00' for birthdays
$sql = "SELECT first_name, last_name, middle_name, mobile, DATE_FORMAT(birthday, '%Y-%m-%d') as birthday, address, birthplace, id FROM clients";  

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    // In case of no data or query failure
    echo json_encode(['error' => 'No clients found or query failed']);
    exit;
}

// Return data as JSON
echo json_encode($data);

// Close the connection
$conn->close();
?>