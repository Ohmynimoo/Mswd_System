<?php
include 'config.php';

header('Content-Type: application/json');

// Check if the request method is PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Use PUT for updates.']);
    exit;
}

// Retrieve the raw JSON input and decode it
$data = json_decode(file_get_contents('php://input'), true);

// Check if data is properly received and required fields are present
if (!$data || !isset($data['id']) || empty($data['firstName']) || empty($data['lastName']) || empty($data['age']) || empty($data['birthPlace']) || empty($data['address']) || empty($data['education']) || empty($data['income']) || empty($data['occupation']) || empty($data['mobileNumber']) || empty($data['gender']) || empty($data['clientType']) || empty($data['assistanceType']) || empty($data['fundType']) || empty($data['amount']) || empty($data['beneficiary'])) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields cannot be empty.']);
    exit;
}

// Assign variables from the JSON data
$id = $data['id'];
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$middleName = $data['middleName'];
$age = $data['age'];
$birthPlace = $data['birthPlace'];
$address = $data['address'];
$education = $data['education'];
$income = $data['income'];
$occupation = $data['occupation'];
$mobileNumber = $data['mobileNumber'];
$gender = $data['gender'];
$clientType = $data['clientType'];
$date = $data['date'];
$assistanceType = $data['assistanceType'];
$fundType = $data['fundType'];
$amount = $data['amount'];
$beneficiary = $data['beneficiary'];

// Establish a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Failed to connect to database.']);
    exit;
}

// Prepare the SQL query to update the record
$sql = "UPDATE individuals SET 
        firstName = '$firstName', 
        lastName = '$lastName', 
        middleName = '$middleName', 
        age = '$age', 
        birthPlace = '$birthPlace', 
        address = '$address', 
        education = '$education', 
        income = '$income', 
        occupation = '$occupation', 
        mobileNumber = '$mobileNumber', 
        gender = '$gender', 
        clientType = '$clientType', 
        date = '$date', 
        assistanceType = '$assistanceType', 
        fundType = '$fundType', 
        amount = '$amount', 
        beneficiary = '$beneficiary' 
    WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Record edited successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating record: ' . $conn->error]);
}

$conn->close();
?>
