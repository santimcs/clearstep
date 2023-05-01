<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Price Record</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $(function() {
        $("#date").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
</script>
</head>
<body>
    <h1>Insert Price Record</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database credentials
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "stock";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Variables from the form
        $name = $_POST['name'];
        $date = $_POST['date'];
        $price = $_POST['price'];
        $maxp = $_POST['maxp'];
        $minp = $_POST['minp'];
        $qty = $_POST['qty'];
        $opnp = $_POST['opnp'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO price (name, date, price, maxp, minp, qty, opnp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdddid", $name, $date, $price, $maxp, $minp, $qty, $opnp);

        // Execute the prepared statement
        if ($stmt->execute()) {
            echo "<p>New record created successfully</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        // Close the prepared statement and the connection
        $stmt->close();
        $conn->close();
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="date">Date:</label>
        <input type="text" id="date" name="date" required>
        <br>

        <label for="opnp">Opening Price:</label>
        <input type="number" id="opnp" name="opnp" step="0.01">
        <br>

        <label for="maxp">Max Price:</label>
        <input type="number" id="maxp" name="maxp" step="0.01">
        <br>

        <label for="minp">Min Price:</label>
        <input type="number" id="minp" name="minp" step="0.01">
        <br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>
        <br>

        <label for="qty">Quantity:</label>
        <input type="number" id="qty" name="qty" required>
        <br>
        
        <input type="submit" value="Save">
    </form>
</body>
</html>
