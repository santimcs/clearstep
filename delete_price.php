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

// Delete all data from the "stock_prices" table
$sql = "DELETE FROM prices";
if ($conn->query($sql) === TRUE) {
    echo "All data deleted from stock_prices table.";
} else {
    echo "Error deleting data: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
