<?php
include 'config.php';

header('Content-Type: application/json');

$id = $_POST['id'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$middleName = $_POST['middleName'];
$age = $_POST['age'];
$birthPlace = $_POST['birthPlace'];
$address = $_POST['address'];
$education = $_POST['education'];
$income = $_POST['income'];
$occupation = $_POST['occupation'];
$mobileNumber = $_POST['mobileNumber'];
$gender = $_POST['gender'];
$clientType = $_POST['clientType'];
$date = $_POST['date'];
$assistanceType = $_POST['assistanceType'];
$fundType = $_POST['fundType'];
$amount = $_POST['amount'];
$beneficiary = $_POST['beneficiary'];

// Update the record in the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    echo json_encode(['status' => 'error', 'message' => 'Failed to connect to database.']);
    exit;
}

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