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
$sql = "SELECT name, q4, q3, q2, q1, dividend, shares AS qty, xdate, paiddate AS pay_date, actual FROM dividend";
$result = $conn1->query($sql);

$count = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $name = $row["name"];
        $q4 = $row["q4"];
        $q3 = $row["q3"];
        $q2 = $row["q2"];
        $q1 = $row["q1"];
        $dividend = $row["dividend"];
        $qty = $row["qty"];
        $xdate = $row["xdate"];
        $pay_date = $row["pay_date"];
        $actual = $row["actual"];

        $sql = "INSERT INTO dividends (name, q4, q3, q2, q1, dividend, qty, xdate, pay_date, actual) 
        VALUES ('$name', $q4, $q3, $q2, $q1, $dividend, $qty, '$xdate', '$pay_date', $actual)";

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
// dividend the database connections
$conn1->close();
$conn2->close();

// Display the number of records converted
echo "Converted " . $count . " records.";
?>
