<?php
include 'config.php';
header('Content-Type: application/json');

// Handle GET requests (fetching records)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch individuals along with their family members using JOIN
    $sql = "
        SELECT 
            i.*, 
            f.id as family_id, 
            f.firstName as familyFirstName, 
            f.lastName as familyLastName, 
            f.middleName as familyMiddleName, 
            f.dateOfBirth as familyDateOfBirth, 
            f.gender as familyGender, 
            f.relationship as familyRelationship 
        FROM individuals i
        LEFT JOIN family_members f ON i.id = f.individual_id";
    
    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        $individuals = [];

        // Process the result and group family members by individual
        while ($row = $result->fetch_assoc()) {
            $individualId = $row['id'];

            // If the individual isn't already added, initialize their entry
            if (!isset($individuals[$individualId])) {
                $individuals[$individualId] = [
                    'id' => $row['id'],
                    'firstName' => $row['firstName'],
                    'lastName' => $row['lastName'],
                    'middleName' => $row['middleName'],
                    'age' => $row['age'],
                    'birthPlace' => $row['birthPlace'],
                    'address' => $row['address'],
                    'education' => $row['education'],
                    'income' => $row['income'],
                    'occupation' => $row['occupation'],
                    'mobileNumber' => $row['mobileNumber'],
                    'gender' => $row['gender'],
                    'clientType' => $row['clientType'],
                    'date' => $row['date'],
                    'assistanceType' => $row['assistanceType'],
                    'fundType' => $row['fundType'],
                    'amount' => $row['amount'],
                    'beneficiary' => $row['beneficiary'],
                    'familyMembers' => []
                ];
            }

            // Add family member to the individual's familyMembers array
            if (!empty($row['family_id'])) { // Ensure the family member exists
                $individuals[$individualId]['familyMembers'][] = [
                    'id' => $row['family_id'],
                    'firstName' => $row['familyFirstName'],
                    'lastName' => $row['familyLastName'],
                    'middleName' => $row['familyMiddleName'],
                    'dateOfBirth' => $row['familyDateOfBirth'],
                    'gender' => $row['familyGender'],
                    'relationship' => $row['familyRelationship']
                ];
            }
        }

        // Prepare the data in the expected format
        foreach ($individuals as $individual) {
            $data[] = $individual;
        }

        echo json_encode($data);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error fetching records: ' . $conn->error]);
    }
} else {
    // Handle non-GET requests
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

$conn->close();
