<?php
// Connect to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stockdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete all data from the "tickers" table
$sql = "DELETE FROM tickers";
if ($conn->query($sql) === TRUE) {
    $deleted_rows = $conn->affected_rows;
    echo "$deleted_rows rows deleted from tickers table.";
} else {
    echo "Error deleting data: " . $conn->error;
}

// Close the database connection
$conn->close();
?>

