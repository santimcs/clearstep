<html>
<head>
    <title>Div 2</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="css/global.css" rel="stylesheet" type="text/css">
    <link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="js/vendor/jquery.js"></script>
	<script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
	<script type="text/javascript">
	    $(document).ready(function() {
            $('#example').dataTable( {
                "pagingType": "full_numbers",
                "order": [[ 1, "desc" ],[ 2, "desc" ],[ 0, "asc" ]]
            } );
		} );
	</script>
</head>

<body id='twoCol' class='dt-example'>
    <div id="container">
        <div id="contentWrap">
            <div id="main">


<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "stock";

// Create connectionection
$connection = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price"; 

if (!($result_max = mysqli_query($connection, $query_max)))
   showerror();
   
$row_max = mysqli_fetch_array($result_max);
extract($row_max);
$to_date = date($max_date);

$ttl_amt = 0.0;

$sql = "SELECT Y.name AS name, Y.actual AS actual, xdate, paiddate,
q4, shares, Y.dividend, P.price AS price, Y.actual
  FROM dividend AS Y, price AS P
  WHERE Y.name = P.name
  AND P.date = '$to_date'";

$result = mysqli_query($connection, $sql);

if (mysqli_num_rows($result) > 0) {
        echo   "<h1>Dividend Inquiry</h1>
                <table id = 'example' class='display' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>A</th>
                            <th>xdate</th>
                            <th>paid date</th>
                            <th>q4</th>
                            <th>shares</th>
                            <th>amount</th>
                            <th>price</th>
                            <th>percent</th>
                        </tr>
                    </thead>
                    <tbody>";
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $dividend = $row['dividend'];  
        $dividend = number_format($dividend,4);      
        $price = $row['price']; 
        $percent = number_format($dividend/$price*100,2);
        $q4 = $row['q4']; 
        $shares = $row['shares']; 
        $amount = number_format($q4*$shares,2);
        $ttl_amt = $ttl_amt + (float) $amount;
        echo        "
                        <tr>
                            <td>" . $row["name"]. "</td>
                            <td>" . $row["actual"]. "</td>                            
                            <td>" . $row["xdate"]. "</td>
                            <td>" . $row["paiddate"]. "</td>
                            <td>" . $row["q4"]. "</td>
                            <td>" . number_format($row["shares"]). "</td>
                            <td>" . $amount. "</td>
                            <td>" . $price. "</td>
                            <td>" . $percent. "</td>
                        </tr>
                    ";
    }


        echo  "</tbody>
                </table>";
    } else {
        echo "0 results";
    }

?>

            </div>
        </div>
    </div>
    <br/>
    <br/>
    <br/>

<?php
// Find total dividend amount
$query_dvd = "SELECT SUM(q4 * shares) AS ttl_dvd FROM dividend";
if (!($result_dvd = mysqli_query($connection, $query_dvd)))
   showerror();
$row_dvd = mysqli_fetch_array($result_dvd);
extract($row_dvd);
$ttl_dividend = number_format($ttl_dvd,2);
//print $market;
    echo "<p align='center'> Dividend amount = " . $ttl_dividend . "</p>";


    mysqli_close($connection);

?>

</body>
</html>
