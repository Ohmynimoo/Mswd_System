<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include 'config.php';

// Check for connection error
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;   
}

// Updated Query to ensure unique results and handle duplicates
$sql = "
    SELECT DISTINCT 
        c.first_name, 
        c.last_name, 
        c.middle_name, 
        c.mobile, 
        DATE_FORMAT(c.birthday, '%Y-%m-%d') as birthday, 
        c.address, 
        c.birthplace, 
        c.id,
        COALESCE(u.request_status, 'No Request') AS request_status
    FROM clients c
    LEFT JOIN (
        SELECT client_name, request_status 
        FROM uploads 
        WHERE (client_name, upload_date, id) IN (
            SELECT client_name, MAX(upload_date) AS max_date, MAX(id) AS max_id
            FROM uploads
            GROUP BY client_name
        )
    ) u ON CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name) = u.client_name";  

$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} 

// Return data as JSON
echo json_encode($data);

// Close the connection
$conn->close();
?>
