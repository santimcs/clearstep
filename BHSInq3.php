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

$query = "UPDATE SAA,PRICE SET SAA.PRICE = PRICE.PRICE WHERE PRICE.NAME=SAA.NAME AND PRICE.DATE='$to_date'";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "UPDATE SAA SET GAIN = (TP-PRICE)/PRICE*100";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "SELECT SAA.name AS name, StockName.category AS category,
          SAA.price, TP, gain, Buy, Hold, Sell, (Buy*2)+Hold+(Sell*-2) AS Score, ROE, PER, SAA.div AS yld,
		PER.PBV
          FROM SAA INNER JOIN StockName ON StockName.name = SAA.name
          INNER JOIN ROE on SAA.name = ROE.name
          INNER JOIN PER on SAA.name = PER.name";		

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
				"order": [[ 5, "desc" ],[ 4, "desc" ]]
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
							<th scope="col">Cat.</th>							
							<th scope="col">Price</th>
							<th scope="col">Cons.</th>
							<th scope="col">Pct</th>
                            <th scope="col">Div</th>
							<th scope="col">B</th>
							<th scope="col">H</th>
							<th scope="col">S</th>
							<th scope="col">Score</th>
							<th scope="col">ROE</th>
							<th scope="col">P/E</th>
							<th scope="col">P/BV</th>
						</tr>
					</thead>
					<tbody>
EOD;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $stock_name = $row['name'];
     $category = $row['category'];	
     $price = $row['price'];
     $TP = $row['TP'];
     $Gain = $row['gain'];
    $Div = $row['yld'];
	$Buy  = $row['Buy'];
	$Hold  = $row['Hold'];	 
	$Sell  = $row['Sell'];	 
     $Score = $row['Score'];
     $ROE = $row['ROE'];
     $PER = $row['PER'];
     $PBV = $row['PBV'];
     $stock_details .=<<<EOD
						<tr>
							<td>$stock_name</td>
							<td>$category</td>							
							<td>$price</td>
							<td>$TP</td>
							<td>$Gain</td>
                            <td>$Div</td>
							<td>$Buy</td>	
							<td>$Hold</td>	 
							<td>$Sell</td>	 
							<td>$Score</td>
							<td>$ROE</td>
							<td>$PER</td>
							<td>$PBV</td>
						</tr>\n
EOD;
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
