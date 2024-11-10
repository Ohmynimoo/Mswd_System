<?php
include 'config.php';

// Set the default timezone
date_default_timezone_set('UTC');

// Set the response type to JSON
header('Content-Type: application/json');

// Function to validate date format (YYYY-MM-DD)
function isValidDateFormat($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}

// Function to sanitize and validate date input
function sanitizeAndValidateDate($inputDate) {
    if (!$inputDate) {
        return false;
    }

    $date = date_create_from_format('Y-m-d', $inputDate);
    return ($date && $date->format('Y-m-d') === $inputDate && isValidDateFormat($inputDate)) ? $inputDate : false;
}

// Function to check if individual already exists (avoid duplication)
function checkDuplicateIndividual($conn, $firstName, $lastName, $middleName) {
    $sql = "SELECT id FROM individuals WHERE firstName = ? AND lastName = ? AND middleName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $firstName, $lastName, $middleName);
    $stmt->execute();
    $stmt->store_result();

    // Return true if a matching record is found
    return $stmt->num_rows > 0;
}

// Handle POST requests (adding records)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate input data
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $middleName = filter_input(INPUT_POST, 'middleName', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $birthPlace = filter_input(INPUT_POST, 'birthPlace', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $education = filter_input(INPUT_POST, 'education', FILTER_SANITIZE_STRING);
    $income = filter_input(INPUT_POST, 'income', FILTER_VALIDATE_FLOAT);
    $occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $mobileNumber = filter_input(INPUT_POST, 'mobileNumber', FILTER_SANITIZE_STRING);
    $clientType = filter_input(INPUT_POST, 'clientType', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $assistanceType = filter_input(INPUT_POST, 'assistanceType', FILTER_SANITIZE_STRING);
    $fundType = filter_input(INPUT_POST, 'fundType', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $beneficiary = filter_input(INPUT_POST, 'beneficiary', FILTER_SANITIZE_STRING);

    // Check if all required fields are present
    if (!$firstName || !$lastName || !$middleName || !$age || !$birthPlace || !$address || !$education ||
        !$income || !$occupation || !$gender || !$mobileNumber || !$clientType || !$date ||
        !$assistanceType || !$fundType || !$amount || !$beneficiary) {
        
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
        exit;
    }

    // Validate the date input
    $formattedDate = sanitizeAndValidateDate($date);
    if (!$formattedDate) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid date format. Please use YYYY-MM-DD format.']);
        exit;
    }

    // Check if an individual with the same First Name, Last Name, and Middle Name already exists
    if (checkDuplicateIndividual($conn, $firstName, $lastName, $middleName)) {
        http_response_code(409); // Conflict
        echo json_encode(['status' => 'error', 'message' => 'Duplicate entry: This information is already existing please just update on the records.']);
        exit;
    }

    // Prepare SQL statement to insert the individual
    $sql = "INSERT INTO individuals (firstName, lastName, middleName, age, birthPlace, address, education, income, occupation, gender, mobileNumber, clientType, date, assistanceType, fundType, amount, beneficiary)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute statement
    $stmt->bind_param("sssisssisssssssss", $firstName, $lastName, $middleName, $age, $birthPlace, $address, $education, $income, $occupation, $gender, $mobileNumber, $clientType, $formattedDate, $assistanceType, $fundType, $amount, $beneficiary);

    if ($stmt->execute()) {
        $individualId = $stmt->insert_id; // Get the last inserted ID for the individual

        // Handle Family Members Data
        if (!empty($_POST['familyFirstName']) && is_array($_POST['familyFirstName'])) {
            addFamilyMembers($conn, $individualId);
        }

        echo json_encode(['status' => 'success', 'message' => 'Record and family members added successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error adding record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

// Close the database connection
$conn->close();

// Function to add family members
function addFamilyMembers($conn, $individualId) {
    $familyFirstNames = $_POST['familyFirstName'];
    $familyLastNames = $_POST['familyLastName'];
    $familyMiddleNames = $_POST['familyMiddleName'];
    $familyDateOfBirths = $_POST['familyDateOfBirth'];
    $familyGenders = $_POST['familyGender'];
    $familyRelationships = $_POST['familyRelationship'];

    // Prepare SQL statement to insert family members
    $stmtFamily = $conn->prepare("INSERT INTO family_members (individual_id, firstName, lastName, middleName, dateOfBirth, gender, relationship) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmtFamily === false) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare family statement: ' . $conn->error]);
        exit;
    }

    // Insert each family member
    for ($i = 0; $i < count($familyFirstNames); $i++) {
        $familyFirstName = $familyFirstNames[$i];
        $familyLastName = $familyLastNames[$i];
        $familyMiddleName = $familyMiddleNames[$i];
        $familyDateOfBirth = sanitizeAndValidateDate($familyDateOfBirths[$i]);
        $familyGender = $familyGenders[$i];
        $familyRelationship = $familyRelationships[$i];

        // Check if family date of birth is valid
        if (!$familyDateOfBirth) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid family member date of birth format for family member ' . ($i + 1)]);
            exit;
        }

        // Bind parameters and execute for each family member
        $stmtFamily->bind_param("issssss", $individualId, $familyFirstName, $familyLastName, $familyMiddleName, $familyDateOfBirth, $familyGender, $familyRelationship);
        $stmtFamily->execute();
    }

    $stmtFamily->close();
}
?>
