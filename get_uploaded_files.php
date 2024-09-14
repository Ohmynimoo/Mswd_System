<?php
include 'config.php';

if (isset($_GET['fileIds'])) {
  $fileIds = $_GET['fileIds'];
  $fileIdsArray = explode(',', $fileIds);
  $fileIdsString = implode("','", $fileIdsArray);

  $query = "SELECT filename, file_type, file_data FROM uploads WHERE id IN ('$fileIdsString')";
  $result = $conn->query($query);

  $files = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $fileData = base64_encode($row['file_data']);
      $files[] = [
        'filename' => $row['filename'],
        'file_type' => $row['file_type'],
        'file_data' => 'data:' . $row['file_type'] . ';base64,' . $fileData
      ];
    }   
  }

  $conn->close();
  echo json_encode($files);
} else {
  echo json_encode([]);
}
?>