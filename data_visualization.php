<?php
include 'config.php';

if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$startMonth = isset($_GET['startMonth']) ? intval($_GET['startMonth']) : 1;
$endMonth = isset($_GET['endMonth']) ? intval($_GET['endMonth']) : 12;

// Combined query for individuals and assistance_request_history tables
$query = "
    SELECT YEAR(date) AS year, MONTH(date) AS month, assistanceType, 
        SUM(CASE WHEN fundType = 'LGU Fund' THEN 1 ELSE 0 END) AS lgu_count, 
        SUM(CASE WHEN fundType = 'Barangay Fund' THEN 1 ELSE 0 END) AS barangay_count, 
        SUM(CASE WHEN fundType = 'Sk Fund' THEN 1 ELSE 0 END) AS sk_count,
        'individuals' AS source_table
    FROM individuals 
    WHERE YEAR(date) = $year AND MONTH(date) BETWEEN $startMonth AND $endMonth
    GROUP BY YEAR(date), MONTH(date), assistanceType
    UNION
    SELECT YEAR(date) AS year, MONTH(date) AS month, assistanceType, 
        SUM(CASE WHEN fundType = 'LGU Fund' THEN 1 ELSE 0 END) AS lgu_count, 
        SUM(CASE WHEN fundType = 'Barangay Fund' THEN 1 ELSE 0 END) AS barangay_count, 
        SUM(CASE WHEN fundType = 'Sk Fund' THEN 1 ELSE 0 END) AS sk_count,
        'assistance_request_history' AS source_table
    FROM assistance_request_history
    WHERE YEAR(date) = $year AND MONTH(date) BETWEEN $startMonth AND $endMonth
    GROUP BY YEAR(date), MONTH(date), assistanceType";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: ". mysqli_error($conn));
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_close($conn);
echo json_encode($data);
?>
