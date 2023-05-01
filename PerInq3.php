<?php include('inc/header.php'); ?>

<div class="container page-content">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="PerFrm3.php">P/E Ratio</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>P/E Ratio Inquiry</h1>
	</div>	<!-- End of Row -->

	<div class="panel panel-success">
		<table class="table">
			<thead>  
				<tr>	
					<th>Rank</th>				
					<th>Name</th>
					<th>PER</th>
					<th>PBV</th>
				</tr>
			</thead>
			<tbody>
<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName, $connection))
     showerror( );
 
$sector_name = isset($_POST['Sector_Name']) ? $_POST['Sector_Name'] : ''; 
//$sector_name = '';       
//$sector_name = $_POST[Sector_Name];

$query = "SELECT per.name, per.Price, EPS,PER, PBV,Yield, Dividend,
          ROA, ROE, NPM, DERatio, Par, Shares, category,
          ROE.Profit
          FROM per INNER JOIN stockname ON per.name = stockname.name
          INNER JOIN ROE ON per.name = ROE.name
          WHERE sector = '$sector_name' ORDER BY PER";

#print $query;
$result = '';                    
if (!($result = mysql_query($query,$connection)))
   showerror();
$nbr = 0;  
$stock_details = ''; 
while($row = mysql_fetch_array($result))
{
     $nbr = $nbr + 1; 		
     $stock_name = $row['name'];
     $Price = $row['Price'];
     $EPS = $row['EPS'];     
     $PER  = $row['PER'];
     $PBV = $row['PBV'];
     $Yield = $row['Yield'];
     
     $stock_details .=<<<EOD
				<tr>
					<td>$nbr</td>				
					<td>$stock_name</td>
					<td>$PER</td>
					<td>$PBV</td>
				</tr>\n
EOD;
}

    print $stock_details;
	 
?>
			</tbody>
		</table>
	</div>  <!-- End of class="panel panel-default">	
</div>	<!-- End Of Container -->

<?php include('inc/footer.php'); ?>
