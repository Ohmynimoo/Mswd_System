<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mswd_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Adjust the query to fetch personal information (you can adjust table name and fields as per your database)
$sql = "SELECT id, first_name, last_name, middle_name, mobile, birthday, address, birthplace, category 
        FROM clients";  // Replace 'clients' with the correct table name containing personal info

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data = ['message' => 'No clients found'];
}

echo json_encode($data);

$conn->close();
?>
