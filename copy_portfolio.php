<?php
ini_set('max_execution_time', 600); // set the maximum execution time to 600 seconds (10 minutes)

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

// Copy data from the "buy" table to the "portfolios" table
$sql = "SELECT name, date, volbuy AS qty, price AS u_cost, active, period, grade, dividend 
FROM buy ORDER BY name";
$result = $conn1->query($sql);
$count = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $name = $row["name"];
        $date = $row["date"];
        $qty = $row["qty"];
        $u_cost = $row["u_cost"];
        $active = $row["active"];
        $period = $row["period"];
        $grade = $row["grade"];
        $dividend = $row["dividend"];

        

        $sql = "INSERT INTO portfolios (name, date, qty, u_cost, active, period, grade, dividend) 
        VALUES ('$name', '$date', $qty, $u_cost, $active, '$period', '$grade', $dividend)";

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
