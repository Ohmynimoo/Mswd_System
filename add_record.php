<?php
include 'config.php';
date_default_timezone_set('UTC');
header('Content-Type: application/json');

// Function to validate date format (YYYY-MM-DD)
function isValidDateFormat($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}

// Sanitize and validate date input
function sanitizeAndValidateDate($inputDate) {
    $date = date_create_from_format('Y-m-d', $inputDate);
    return ($date && $date->format('Y-m-d') === $inputDate && isValidDateFormat($inputDate)) ? $inputDate : false;
}

// Read JSON input from request body
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if data is properly received and required fields are present
if (empty($inputData) || !is_array($inputData)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input.']);
    exit;
}

// List of required fields
$requiredFields = ['firstName', 'lastName', 'middleName', 'age', 'birthPlace', 'address', 'education', 'income', 'occupation', 'gender', 'mobileNumber', 'clientType', 'date', 'assistanceType', 'fundType', 'amount', 'beneficiary'];

// Check if all required fields are present and valid
foreach ($requiredFields as $field) {
    if (empty($inputData[$field])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing or invalid input: ' . $field]);
        exit;
    }
}

// Validate date
$formattedDate = sanitizeAndValidateDate($inputData['date']);
if (!$formattedDate) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format. Use YYYY-MM-DD.']);
    exit;
}

// Insert individual data into the database
$sql = "INSERT INTO individuals (firstName, lastName, middleName, age, birthPlace, address, education, income, occupation, gender, mobileNumber, clientType, date, assistanceType, fundType, amount, beneficiary) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssisssisssssssss", $inputData['firstName'], $inputData['lastName'], $inputData['middleName'], $inputData['age'], $inputData['birthPlace'], $inputData['address'], $inputData['education'], $inputData['income'], $inputData['occupation'], $inputData['gender'], $inputData['mobileNumber'], $inputData['clientType'], $formattedDate, $inputData['assistanceType'], $inputData['fundType'], $inputData['amount'], $inputData['beneficiary']);

if ($stmt->execute()) {
    $individualId = $stmt->insert_id; // Get the ID of the newly inserted individual

    // Check for family members and insert them into the family_members table
    if (!empty($inputData['familyMembers']) && is_array($inputData['familyMembers'])) {
        $familySql = "INSERT INTO family_members (individual_id, firstName, lastName, middleName, dateOfBirth, gender, relationship) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtFamily = $conn->prepare($familySql);

        if ($stmtFamily === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
            exit;
        }

        foreach ($inputData['familyMembers'] as $familyMember) {
            $familyDateOfBirth = sanitizeAndValidateDate($familyMember['dateOfBirth']);
            if (!$familyDateOfBirth) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid date format for family member date of birth.']);
                exit;
            }

            $stmtFamily->bind_param("issssss", $individualId, $familyMember['firstName'], $familyMember['lastName'], $familyMember['middleName'], $familyDateOfBirth, $familyMember['gender'], $familyMember['relationship']);
            $stmtFamily->execute();
        }

        $stmtFamily->close();
    }

    echo json_encode(['status' => 'success', 'message' => 'Record and family members added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error adding record: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
