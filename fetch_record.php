<?php
include 'config.php';
header('Content-Type: application/json');

// Handle GET requests (fetching records)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM individuals";
    $result = $conn->query($sql);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $individualId = $row['id'];

            // Fetch family members for each individual
            $familySql = "SELECT * FROM family_members WHERE individual_id = ?";
            $stmt = $conn->prepare($familySql);
            $stmt->bind_param("i", $individualId);
            $stmt->execute();
            $familyResult = $stmt->get_result();

            $row['familyMembers'] = $familyResult->fetch_all(MYSQLI_ASSOC);
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error fetching records: ' . $conn->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

$conn->close();
?>
