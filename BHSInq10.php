<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();

if (!mysql_select_db($databaseName, $connection))
     showerror( );

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price";
if (!($result_max = mysql_query($query_max,$connection)))
   showerror();
$row_max = mysql_fetch_array($result_max);
extract($row_max);
$to_date = date($max_date);
//print "Maximum date: " . $to_date;

$createTempTableSQL = "
    CREATE TEMPORARY TABLE temp_price AS
    SELECT NAME, PRICE, DATE
    FROM price
    WHERE DATE = '$to_date';
";
if (!($result = mysql_query($createTempTableSQL, $connection)))
   showerror();

$query = "UPDATE Consensus
INNER JOIN temp_price ON temp_price.NAME = Consensus.NAME
SET Consensus.PRICE = temp_price.price";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "UPDATE Consensus SET GAIN = (target-PRICE)/PRICE*100";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "SELECT Consensus.name AS name, buy.date AS date, FORMAT(buy.price,2) AS cost,
          Consensus.price, target, gain, Buy, Hold, Sell, (Buy*2)+Hold+(Sell*-2) AS Score, 
			 tendays.price AS tdprice
          FROM Consensus INNER JOIN StockName ON StockName.name = Consensus.name
          INNER JOIN tendays on Consensus.name = tendays.name
          INNER JOIN buy on Consensus.name = buy.name WHERE buy.active = 1
		  ORDER BY name ";		

if (!($result = mysql_query($query,$connection)))
   showerror();

$stock_header=<<<EOD
<html>
<head>
	<link rel="shortcut icon" type="image/ico" href="media/images/santi.ico" />
	<title>BHS Inquiry</title>
	<link href="css/global.css" rel="stylesheet" type="text/css">
	<link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="js/vendor/jquery.js"></script>
	<script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#example').dataTable( {
				"pagingType": "full_numbers",
				"order": [[ 0, "asc" ],[ 1, "asc" ]]
			} );
		} );
	</script>
</head>

<body id="twoCol" class="dt-example">
	<div id="container">
		<div id="contentWrap">
			<div id="main">
				<h1>BHS Inquiry As End Of  $to_date</h1>
					<table id="example" class="display" cellspacing="0" width="100%">
					<thead>  
						<tr>
							<th scope="col">Name</th>
							<th scope="col">Date</th>	
							<th scope="col">Cost</th>						
							<th scope="col">Price</th>
							<th scope="col">Cons.</th>
							<th scope="col">Actual</th>
							<th scope="col">Project</th>
							<th scope="col">Buy</th>
							<th scope="col">Hold</th>
							<th scope="col">Sell</th>
							<th scope="col">Score</th>

						</tr>
					</thead>
					<tbody>
EOD;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $stock_name = $row['name'];
     $date = $row['date'];	
     $cost = $row['cost'];
     $price = $row['price'];
     $target = $row['target'];
     $Gain = $row['gain'];
	$Buy  = $row['Buy'];
	$Hold  = $row['Hold'];	 
	$Sell  = $row['Sell'];	 
     $Score = $row['Score'];

     $tdprice = $row['tdprice'];
	$Actual = number_format(($price-$cost)/$cost*100,2);
	$Project = number_format(($target-$price)/$price*100,2);
// $stock_details = isset($_POST['stock_details']) ? $_POST['stock_details'] : '';

$stock_details .= <<<EOT
						<tr>
							<td>$stock_name</td>
							<td>$date</td>	
							<td>$cost</td>						
							<td>$price</td>
							<td>$target</td>
							<td>$Actual</td>
							<td>$Project</td>
							<td>$Buy</td>	
							<td>$Hold</td>	 
							<td>$Sell</td>	 
							<td>$Score</td>

						</tr>\n
EOT;
}

$stock_footer ="			</tbody>
					</table>
				</div>
				<div id=\"sidebar\">
					<h2>BHSInq</h2>
				</div>
			</div>
			    <div id=\"footer\"><a href='mailto: santimcs@hotmail.com'>santimcs@hotmail.com</a></div>
		</div>
     </body>
</html>";

$stock =<<<STOCK
	$stock_header
	$stock_details
	$stock_footer
STOCK;

     print $stock;

?>
