<?php
// Connect to the database
include 'config.php';

// Get the client ID from the request
$client_id = $_POST['client_id'];

// Delete the client's information from the database
$sql = "DELETE FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();

// Close the database connection
$stmt->close();
$conn->close();

// Return a success message
echo 'Client deleted successfully!';
?>