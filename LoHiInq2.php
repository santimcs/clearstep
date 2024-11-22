<?php include('inc/header.php'); ?>

<div class="container page-content">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="#">Daily</a></li>
			<li><a href="LoHiFrm2.php">Low/High</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Low/High Inquiry</h1>
	</div>	<!-- End of Row -->


	<div class="panel panel-success">


		<table class="table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>				
					<th>Price</th>	
					<th>Amount</th>
					<th>PER</th>	
					<th>Yield</th>  
					<th>Dividend</th>
					<th>PBV</th>
				</tr>
			</thead>
			<tbody>			

<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName))
   showerror();
                            
$direction = isset($_POST['direction']) ? $_POST['direction'] : '';
$present = isset($_POST['present']) ? $_POST['present'] : '';
 
if ($direction == "NewLow") 
		$dirtext = " A.Price < B.Price";
if ($direction == "Low")
	  $dirtext = " A.Price <= B.Price";
if ($direction == "High")     
		$dirtext = " A.price >= B.Price";
if ($direction == "NewHigh")     
		$dirtext = " A.price > B.Price";
		
if ($direction == "NewLow") 
		$from_files = "pricetdy AS A, pricelo AS B, stockname AS S, per AS P";
if ($direction == "Low")
	  $from_files = "pricetdy AS A, pricelo AS B, stockname AS S, per AS P";
if ($direction == "High")
	  $from_files = "pricetdy AS A, pricehi AS B, stockname AS S, per AS P";
if ($direction == "NewHigh")     
	  $from_files = "pricetdy AS A, pricehi AS B, stockname AS S, per AS P";
	  
$query = "DELETE FROM pricetdy";
if (!($result = mysql_query($query,$connection)))
   showerror();

$query = "INSERT INTO pricetdy 
		SELECT name, price, qty, qty*price AS amt FROM price
		WHERE date = '$present'";
if (!($result = mysql_query($query,$connection)))
	showerror();

$query = "DELETE FROM pricelo";
if (!($result = mysql_query($query,$connection)))
	showerror();
 
$query = "INSERT INTO pricelo 
		SELECT name, min(price) FROM price
		WHERE date >= DATE_SUB('$present', INTERVAL 1 YEAR) AND date < '$present' 
		GROUP BY name";
if (!($result = mysql_query($query,$connection)))
	showerror();

$query = "DELETE FROM pricehi";
if (!($result = mysql_query($query,$connection)))
	showerror();
 
$query = "INSERT INTO pricehi 
		SELECT name, max(price) FROM price
		WHERE date >= DATE_SUB('$present', INTERVAL 1 YEAR) AND date < '$present'
		GROUP BY name";
if (!($result = mysql_query($query,$connection)))
	showerror();
   
$query = "SELECT A.name AS name, S.category AS cat, A.price AS priceA, FORMAT(A.qty,0) AS qty,
		amt, PER, yield, FORMAT(P.dividend,2) AS dividend, PBV
        FROM $from_files
        WHERE A.name = B.name
        AND $dirtext
        AND   A.name = S.name
        AND   A.name = P.name
        AND A.price <> 9900
        AND B.price <> 9900
        ORDER BY A.name";

if (!($result = mysql_query($query,$connection)))
	showerror();
   
$num_rows = mysql_num_rows($result);

if ($direction == "NewLow") 
		$header = "New Low " . $num_rows . ' Items';
if ($direction == "Low")
		$header = "Low " . $num_rows . ' Items';
if ($direction == "High")     
		$header = "High " . $num_rows . ' Items';
if ($direction == "NewHigh") 
		$header = "New High " . $num_rows . ' Items';

$query_set = "SELECT setindex FROM setindex WHERE date = '$present'";
if (!($result_set = mysql_query($query_set,$connection)))
	showerror();
$row_set = mysql_fetch_array($result_set);
extract($row_set);
$present_ind = $setindex;

$stock_details = '';				
while($row = mysql_fetch_array($result))
{
    $name = $row['name'];
    $cat = $row['cat'];
    $priceA = $row['priceA'];
    $amt = $row['amt'];  
	$fmtAmt = number_format($amt/1000000,3,'.','');	
	$PER = $row['PER'];
    $yield = $row['yield'];  	
    $dividend = $row['dividend'];
	$PBV = $row['PBV'];

    $stock_details .= <<<EOD
				
				<tr>
					<td>$name</td>
					<td>$cat</td>				
					<td>$priceA</td>	
					<td>$fmtAmt</td>	
					<td>$PER</td>		
					<td>$yield</td>		 
					<td>$dividend</td>		
					<td>$PBV</td>
				</tr>					
EOD;
}
    print $stock_details;
	 
?>
			</tbody>
		</table>
	</div>  <!-- End of class="panel panel-default">	
</div>	

