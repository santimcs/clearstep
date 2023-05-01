<?php
require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
	showerror();
   
if (!mysql_select_db($databaseName))
	showerror();
                            
$direction = isset($_POST['direction']) ? $_POST['direction'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$duration = isset($_POST['duration']) ? $_POST['duration'] : '';

if ($duration == "28")
	$query_prd = "SELECT DATE_SUB('$to_date', INTERVAL 27 DAY) AS fm_date";
if ($duration == "14")
	$query_prd = "SELECT DATE_SUB('$to_date', INTERVAL 13 DAY) AS fm_date";
if ($duration == "7")
	$query_prd = "SELECT DATE_SUB('$to_date', INTERVAL 6 DAY) AS fm_date";
if ($duration == "1")
	$query_prd = "SELECT DATE_SUB('$to_date', INTERVAL 0 DAY) AS fm_date";

// Find period
if (!($result_prd = mysql_query($query_prd,$connection)))
	showerror();
$row_prd = mysql_fetch_array($result_prd);
extract($row_prd);

// Find minimum, maximum index
$query_set = "SELECT MIN(setindex) AS min_index, MAX(setindex) AS max_index
							FROM setindex
							WHERE date BETWEEN '$fm_date' AND '$to_date'";
if (!($result_set = mysql_query($query_set,$connection)))
   showerror();
$row_set = mysql_fetch_array($result_set);
extract($row_set);
 
if ($direction == "T0102") 
	$dirtext = " A.Minp <= 1.99 AND A.Maxp >= 2.00   GROUP BY A.Name";
if ($direction == "T0405")
	$dirtext = " A.Minp <= 4.98 AND A.Maxp >= 5.00    GROUP BY A.Name";
if ($direction == "T0910")     
	$dirtext = " A.Minp <= 9.95 AND A.Maxp >= 10.00   GROUP BY A.Name";
if ($direction == "T2425")     
	$dirtext = " A.Minp <= 24.9 AND A.Maxp >= 25.00    GROUP BY A.Name";

$from_files = "price AS A";
				  
$query = "SELECT A.Name, S.category AS Cat, COUNT(*) AS Frequency, MIN(A.Minp) AS Minimump, 
		  MAX(A.Maxp) AS Maximump,AVG(A.Qty)*AVG(A.Price)/1000000 AS amt,
		  PER, yield, FORMAT(P.dividend,2) AS dividend,
		  M.maxp AS maxminmaxp, M.minp AS maxminminp, M.spread AS spd, top100
          FROM $from_files
		  JOIN Stockname AS S USING (Name)
		  JOIN per AS P USING (Name)
		  JOIN maxminp M USING (name)
          WHERE Date BETWEEN '$fm_date' AND '$to_date'    
          AND $dirtext";
// print $query;          

if (!($result = mysql_query($query,$connection)))
	showerror();
   
$num_rows = mysql_num_rows($result);
//print $num_rows;
				
$stock_header = <<<EOD

<html>
<head>
	<link rel="shortcut icon" type="image/ico" href="media/images/santi.ico" />
	<title>Frequency Inquiry</title>
	<link href="css/global.css" rel="stylesheet" type="text/css">
	<link href="css/vendor/jquery.dataTables.css" rel="stylesheet" type="text/css">
		
	<script type="text/javascript" src="js/vendor/jquery.js"></script>
	<script type="text/javascript" src="js/vendor/jquery.dataTables.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#example').dataTable( {
				"pagingType": "full_numbers",
				"order": [[ 12, "desc" ],[ 5, "desc" ]]
			} );
		} );
	</script>
</head>

<body id="twoCol" class="dt-example">
	<div id="container">
		<div id="contentWrap">
			<div id="main">
				<h1>Frequency Inquiry</h1>
				<table id="example" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Frq.</th>	
							<th>Min.</th>
							<th>Max.</th>								
							<th>Amount</th>	 
							<th>PER</th>	
							<th>Yield</th>  
							<th>Div.</th>
							<th>Max</th>
							<th>Min</th>  
							<th>Spd</th>
							<th>100</th>								
						</tr>
					</thead>	
					<tbody>
EOD;
$item = 0;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $Name = $row['Name'];
     $Cat = $row['Cat'];
     $Frequency = $row['Frequency'];
     $Minimump = $row['Minimump'];
     $Maximump = $row['Maximump'];
     $amt = $row['amt'];
	 $fmtAmt = number_format($amt,3,'.','');
	 
	 $PER = $row['PER'];
     $yield = $row['yield'];  	
     $dividend = $row['dividend'];	
     $maxminmaxp = $row['maxminmaxp'];
     $maxminminp = $row['maxminminp'];
     $spd = $row['spd'];
     $top100 = $row['top100'];	
     $item = $item + 1;
     
     $stock_details .= <<<EOD
	
						<tr>
							<td>$Name</td>	
							<td>$Cat</td>
							<td>$Frequency</td>	
							<td>$Minimump</td>	
							<td>$Maximump</td>
							<td>$fmtAmt</td>	
							<td>$PER</td>		
							<td>$yield</td>		 
							<td>$dividend</td>	
							<td>$maxminmaxp</td>	
							<td>$maxminminp</td>	
							<td>$spd</td>
							<td>$top100</td>							
						</tr>					
EOD;
}

$stock_details .=<<<EOD

					</tbody>
				</table>
				</br>
				</br>
EOD;

$stock_footer ="	
			</div>	<!-- Main -->
			<div id=\"sidebar\">
				<h2>FrqncyInq.php</h2>
			</div>
		</div>			<!-- ContentWrap -->
		<div id=\"footer\"><a href='mailto: santimcs@hotmail.com'>santimcs@hotmail.com</a></div>
		<div class=\"random_img\">
			<img src=\"http://localhost/random/rotate.php\" width=\"1160\">
		</div>			
	</div>	<!-- Container -->
</body>
</html>";

$stock = <<<STOCK
	$stock_header
	$stock_details
	$stock_footer
STOCK;

	print $stock;
?>
