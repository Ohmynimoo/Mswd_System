<?php
include 'config.php';

header('Content-Type: application/json');

// Retrieve the main form data
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
$mobileNumber = trim($_POST['mobileNumber']);
$gender = $_POST['gender'];
$clientType = $_POST['clientType'];
$date = $_POST['date'];
$assistanceType = $_POST['assistanceType'];
$fundType = $_POST['fundType'];
$amount = $_POST['amount'];
$beneficiary = $_POST['beneficiary'];

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($age) || empty($birthPlace) || empty($address) || empty($education) || empty($occupation) || empty($mobileNumber) || empty($gender) || empty($clientType) || empty($assistanceType) || empty($fundType) || empty($amount) || empty($beneficiary)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields cannot be empty.']);
    exit;
}

// Validate mobile number (11 digits)
if (!preg_match('/^\d{11}$/', $mobileNumber)) {
    echo json_encode(['status' => 'error', 'message' => 'Mobile number must be exactly 11 digits.']);
    exit;
}

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Start transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Update the main individual record
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

    if (!$conn->query($sql)) {
        throw new Exception('Error updating individual record: ' . $conn->error);
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Record updated successfully.']);
} catch (Exception $e) {
    // Rollback the transaction if something went wrong
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>
