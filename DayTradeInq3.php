<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName))
   showerror();
                            
$direction = isset($_POST['direction']) ? $_POST['direction'] : '';
$present = isset($_POST['present']) ? $_POST['present'] : '';

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price"; 
if (!($result_max = mysql_query($query_max,$connection)))
   showerror();
$row_max = mysql_fetch_array($result_max);
extract($row_max);
$to_date = date($max_date);
//print $to_date;
				  
$query = "SELECT A.name AS name, S.category AS cat, A.minp AS minp, 
		A.price AS price, A.maxp AS maxp, FORMAT(A.qty,0) AS volume,
		FORMAT(A.price * A.qty / 1000000,0) AS amount, PER, yield,
		FORMAT(P.dividend,2) AS dividend, PBV, T.price AS avgp,
		CASE WHEN A.price < T.price THEN 'ddd'
			WHEN A.price > T.price THEN 'uuu'
			ELSE 'sss' END AS trend
		FROM daytrade D JOIN price A USING (name)
		JOIN stockname S USING (name)
		JOIN per P USING (name)
		JOIN tendays T USING (name)			
		WHERE A.date = '$to_date'
          ORDER BY D.name";
//print $query;          

if (!($result = mysql_query($query,$connection)))
   showerror();
   
$num_rows = mysql_num_rows($result);
//print $num_rows;
				
$stock_header = <<<EOD
<html>
	<head>
		<link rel="shortcut icon" type="image/ico" href="media/images/santi.ico" />
		
		<title>Day Trade Inquiry</title>
		<link href="css/global.css" rel="stylesheet" type="text/css">
		<link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">

		<script type="text/javascript" src="js/vendor/jquery.js"></script>
		<script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#example').dataTable( {
					"pagingType": "full_numbers",
					"order": [[ 9, "desc" ]]
				} );
			} );

</script>

</head>

<body id="twoCol" class="dt-example">
	<div id="container">
		<div id="contentWrap">
			<div id="main">
				<h1>Day Trading Inquiry As End Of $to_date</h1>
					<table id="example" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>				
							<th>Min</th>	
							<th>Price</th>	
							<th>Max</th>	
							<th>Amt</th>	              				              				       
							<th>PER</th>							
							<th>P/BV</th>							
							<th>Avg Price</th>
							<th>Pct</th>							
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Name</th>
							<th>Type</th>				
							<th>Min</th>	
							<th>Price</th>	
							<th>Max</th>	
							<th>Amt</th>	              				              				       
							<th>PER</th>						
							<th>P/BV</th>							
							<th>10 Days</th>
							<th>Pct</th>							
						</tr>
					</tfoot>					
					<tbody>			
EOD;
$item = 0;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $name = $row['name'];
     $cat = $row['cat'];
     $minp = $row['minp'];
     $price = $row['price'];          
     $maxp = $row['maxp'];
     $amount = $row['amount'];    
     $PER = $row['PER'];
     $yield = $row['yield'];  	
     $dividend = $row['dividend'];	
	$PBV = $row['PBV'];
	$avgp = $row['avgp'];	
	$trend = $row['trend'];	
     $item = $item + 1;
	$pct = number_format(($price-$avgp)/$avgp*100,2);	
     $stock_details .= <<<EOD
						<tr>
							<td>$name</td>
							<td>$cat</td>				
							<td>$minp</td>	
							<td>$price</td>	
							<td>$maxp</td>		
							<td>$amount</td>		
							<td>$PER</td>						
							<td>$PBV</td>		 
							<td class="center">$avgp</td>
							<td>$pct</td>							
						</tr>\n					
EOD;
}

$stock_footer ="			</tbody>
					</table>
	
					</br>
					</br>
				</div>
				<div id=\"sidebar\">
					<h2>DayTradeInq</h2>
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

     // print "There are $num_days days in our database";
     print $stock;
?>
