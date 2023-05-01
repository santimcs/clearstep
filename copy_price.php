<?php
ini_set('max_execution_time', 600); // set the maximum execution time to 300 seconds (5 minutes)

// Connect to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname1 = "stock";
$dbname2 = "stockdb";

$conn1 = new mysqli($servername, $username, $password, $dbname1);
if ($conn1->connect_error) {
    die("Connection failed: " . $conn1->connect_error);
}

$conn2 = new mysqli($servername, $username, $password, $dbname2);
if ($conn2->connect_error) {
    die("Connection failed: " . $conn2->connect_error);
}

// Copy data from the "price" table to the "stock_prices" table
$sql = "SELECT name, date, opnp AS open, maxp AS high, minp AS low, price AS close, qty FROM price";
$result = $conn1->query($sql);

$count = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $name = $row["name"];
        $date = $row["date"];
        $open = $row["open"];
        $high = $row["high"];
        $low = $row["low"];
        $close = $row["close"];
        $qty = $row["qty"];

        $sql = "INSERT INTO prices (name, date, open, high, low, close, qty) VALUES ('$name', '$date', $open, $high, $low, $close, $qty)";

        if ($conn2->query($sql) === TRUE) {
            $count++;
        } else {
            echo "Error: " . $sql . "<br>" . $conn2->error;
        }
    }
}
if ($conn2->query($sql) === TRUE) {
    $count++;
} else {
    echo "Error: " . $sql . "<br>" . $conn2->error;
}
// Close the database connections
$conn1->close();
$conn2->close();

// Display the number of records converted
echo "Converted " . $count . " records.";
?>
