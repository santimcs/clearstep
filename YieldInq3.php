<?php

require "DB.inc";

$present = isset($_POST['present']) ? $_POST['present'] : '';
// $sector_name = isset($_POST['Sector_Name']) ? $_POST['Sector_Name'] : ''; 

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName))
   showerror();

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price"; 
if (!($result_max = mysql_query($query_max,$connection)))
   showerror();
$row_max = mysql_fetch_array($result_max);
extract($row_max);
$to_date = date($max_date);

$query = "UPDATE dividend 
          SET dividend = q1 +q2 +q3 +q4";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "SELECT Y.name AS name, xdate, paiddate,
		q4, q3, q2, q1, Y.dividend, P.price, Y.actual
          FROM dividend AS Y, price AS P
          WHERE Y.name = P.name

          AND P.date = '$present'
";
//print $query;
          
if (!($result = mysql_query($query,$connection)))
   showerror();
   
$num_rows = mysql_num_rows($result);
//print $num_rows;

$stock_header = <<<EOD
<html>
	<head>

		<title>Div 1</title>

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
	<body id="twoCol" class="dt-example">
		<div id="container">
			<div id="contentWrap">
				<div id="main">
					<h1>Dividend Inquiry As End Of $to_date</h1>
					<table id="example" class="display" cellspacing="0" width="100%">
						<thead>  
							<tr>
								<th>Name</th>
								<th>A</th>
								<th>X-Date</th>
								<th>Pay Date</th>								
								<th>Q4</th>
								<th>Q3</th>
								<th>Q2</th>
								<th>Q1</th>
								<th>Dividend</th>
								<th>Price</th>
								<th>Yield</th>
								
							</tr>
						</thead>
						<tbody>
EOD;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $name = $row['name'];
     $xdate = $row['xdate'];  
     $paiddate = $row['paiddate'];  
     $q4 = $row['q4'];  
     $q4 = number_format($q4,4);  	
     $q3 = $row['q3']; 
     $q3 = number_format($q3,4); 	
     $q2 = $row['q2']; 
     $q2 = number_format($q2,4); 	
     $q1 = $row['q1']; 
     $q1 = number_format($q1,4); 	
     $dividend = $row['dividend'];  
     $dividend = number_format($dividend,4);      
     $price = $row['price'];   
	$fmtPrice = number_format($price,2,'.','');
     $percent = number_format($dividend/$price*100,2);    
     
     $actual= $row['actual'];
	
     $stock_details .= <<<EOD
							<tr>
								<td>$name</td>
								<td>$actual</td>
								<td>$xdate</td>
								<td>$paiddate</td>
								<td>$q4</td>
								<td>$q3</td>
								<td>$q2</td>
								<td>$q1</td>
								<td>$dividend</td>
								<td>$price</td>
								<td>$percent</td>
								
							</tr>\n
EOD;
}
$stock_footer ="					
						</tbody>
					</table>
					</br>
					</br>						
				</div>
				<div id=\"sidebar\">
					<h2>YieldInq</h2>
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
