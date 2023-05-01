<?php
// Connect to the PostgreSQL database
$pg_conn = pg_connect("host=localhost dbname=portpg_development user=postgres password=admin");

// Connect to the MySQL database
$mysql_conn = new mysqli("localhost", "root", "", "stockdb");

if ($mysql_conn->connect_error) {
    die("Connection failed: " . $mysql_conn->connect_error);
}

// Read data from the PostgreSQL 'tickers' table
$result = pg_query($pg_conn, "SELECT * FROM tickers");

// Insert data into the MySQL 'tickers' table
$insert_query = "INSERT INTO tickers (name, full_name, sector, subsector, market, website, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $mysql_conn->prepare($insert_query);

// Counter for the number of rows converted
$count = 0;

while ($row = pg_fetch_assoc($result)) {
    $stmt->bind_param("ssssssss",
        // $row['id'],
        $row['name'],
        $row['full_name'],
        $row['sector'],
        $row['subsector'],
        $row['market'],
        $row['website'],
        $row['created_at'],
        $row['updated_at']
    );
    $stmt->execute();

    // Increment the counter
    $count++;
}

// Close the prepared statement
$stmt->close();

// Close the PostgreSQL connection
pg_close($pg_conn);

// Close the MySQL connection
$mysql_conn->close();

// Echo the number of rows converted
echo "Number of rows converted: " . $count;
?>

