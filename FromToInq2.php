<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName))
   showerror();
                            
$from = isset($_POST['from']) ? $_POST['from'] : '';
$to = isset($_POST['to']) ? $_POST['to'] : '';
	  
$query = "DELETE FROM tendays";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "INSERT INTO tendays 
		SELECT name, min(price),avg(price),max(price),avg(qty),min(date),max(date)
		FROM price
		WHERE date BETWEEN '$from' AND '$to'
		GROUP BY name";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "SELECT name, minp, T.price, maxp, qty, qty*((minp+maxp)/2) AS amt, per, category
          FROM tendays T
		JOIN per P USING (name)
		JOIN StockName S USING (name)
          ORDER BY amt DESC";
//print $query;          

if (!($result = mysql_query($query,$connection)))
   showerror();
   
$num_rows = mysql_num_rows($result);
//print $num_rows;
				
$stock_header = <<<EOD
<html>
	<head>
		<meta charset="utf-8">
		<link rel="shortcut icon" type="image/ico" href="http://www.datatables.net/media/images/favicon.ico" />
	
		<title>Ten Days Inquiry</title>

	<link href="css/global.css" rel="stylesheet" type="text/css">
	<link href="css/vendor/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
		
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript" src="js/vendor/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#example').dataTable( {
				"paging": false
			} );
		} );
	</script>
	</head>

	<body id="twoCol" class="dt-example">
		<div id="container">
			<div id="contentWrap">
				<div id="main">
					<h1>Date Between $from And $to</h1>
					<table id="example" class="display" cellspacing="0" width="80%">
						<thead>
							<tr>
								<th>Item</th>			
								<th>Name</th>
								<th>Minimum</th>				
								<th>Price</th>	
								<th>Maximum</th>
								<th>Amount</th>
								<th>PER</th>
								<th>Category</th>								
							</tr>
						</thead>
						<tbody>			
EOD;
$item = 0;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $name = $row['name'];
     $minp = $row['minp'];
     $price = $row['price'];
     $maxp = $row['maxp'];	
     $item = $item + 1;
     $amt = $row['amt'];
	$fmtAmt = number_format($amt/1000000,3,'.','');
	
     $per = $row['per'];	
     $category = $row['category'];		
//     $from_date = $row['from_date'];    
//	$to_date = $row['to_date'];

     $stock_details .= <<<EOD
						<tr>
							<td>$item</td>			
							<td>$name</td>
							<td>$minp</td>				
							<td>$price</td>
							<td>$maxp</td>	
							<td>$fmtAmt</td>	
							<td>$per</td>	
							<td>$category</td>								
						</tr>					
EOD;
}

$stock_footer ="					
						</tbody>
					</table>
				</div>
				<div id=\"sidebar\">
					<h2>FromToInq.php</h2>
				</div>				
               </div>
			<div id=\"footer\"><a href='mailto: santimcs@hotmail.com'>santimcs@hotmail.com</a></div>
          </div>
     </body>
</html>";

$stock = <<<STOCK
	$stock_header
	$stock_details
	$stock_footer
STOCK;

     print $stock;
?>
