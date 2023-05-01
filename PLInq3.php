<html>
	<head>
		<title>
			Portfolio Inquiry
		</title>
	</head>
	<body>
<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();
   
if (!mysql_select_db($databaseName))
   showerror();
// $sector_name = isset($_POST['Sector_Name']) ? $_POST['Sector_Name'] : '';   
$price_date = isset($_POST['Price_Date']) ? $_POST['Price_Date'] : '';
$Active = isset($_POST['Active']) ? $_POST['Active'] : '';
$order = isset($_POST['Sequence']) ? $_POST['Sequence'] : '';

$query = "SELECT period, buy.name AS name, buy.date AS date, FORMAT(volbuy,0) AS volbuy,
					FORMAT(buy.price,2) AS buy_price, price.price AS mkt_price,
          FORMAT((volbuy * buy.price),2) AS amtbuy, buy.grade AS grade,
					FORMAT((volbuy * price.price),2) AS amtmkt, 
					FORMAT(((price.price - buy.price) * volbuy),2) AS amtpol,
					FORMAT((((price.price - buy.price)*volbuy)/(volbuy*buy.price)*100),2) AS pctpol,
					(((price.price - buy.price)*volbuy)/(volbuy*buy.price)*100) AS percent
          FROM buy INNER JOIN price ON buy.name = price.name
          WHERE price.date = '$price_date'
          AND buy.active = '$Active'
          AND buy.date <= '$price_date'
          ORDER BY period, $order ASC, buy.date ASC";
          
if (!($result = mysql_query($query,$connection)))
   showerror();
   
$num_rows = mysql_num_rows($result);
//print $num_rows;

$stock_header = <<<EOD
		<table width='100%' border='0' cellspacing='1' cellpadding='0'>
			<tr>
				<td width='10%'>
					<div align='center'>
					  As end of
					</div>
				</td>
				<td width='78%'>
					<div align='center'>
						<font color="#003366" size="5">Portfolio Profit/Loss Position</font>
					</div>				
				</td>				
				<td width='12%'>
					<div align='center'>
					  $price_date
					</div>
				</td>		
			</tr>
		</table>		
		<table width='100%' border='0' cellspacing='1' cellpadding='2'>
			<tr>
				<td width='2%' bgcolor='#BDD3F7'>
					<p  align='center'>
					T
					</p>
				</td>	
				<td width='2%' bgcolor='#BDD3F7'>
					<p  align='center'>
					Gr
					</p>
				</td>
				<td width='10%' bgcolor='#BDD3F7'>
					<p  align='center'>
					Stock Symbol
					</p>
				</td>

				<td width='10%' bgcolor='#BDD3F7'>
					<p  align='center'>
					Date
					</p>
				</td>				
				<td  width='10%' bgcolor='#BDD3F7'>
					<div align='center'>
						Volume
					</div>
				</td>
				<td  width='10%' bgcolor='#BDD3F7'>
					<div align='center'>
						Avg
						<br>
						  Price 
					</div>
				</td>
				<td width='10%' bgcolor='#BDD3F7'>
					<div align='center'>
						Market
						<br>
						  Price 
					</div>
				</td>
				<td width='12%' bgcolor='#BDD3F7'>
					<div align='center'>
						Amount
					</div>
				</td>
				<td width='12%' bgcolor='#BDD3F7'>
					<div align='center'>
						Market
						<br>
						  Value
					</div>
				</td>
				<td width='12%' bgcolor='#BDD3F7'>
					<div align='center'>
						Unrealize
						<br>
						  P/L 
					</div>
				</td>
				<td width='12%' bgcolor='#BDD3F7'>
					<div align='center'>
						Percent
						<br>
							P/L
					</div>
				</td>
			</tr>
EOD;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
     $period = $row['period'];	
     $grade = $row['grade'];	     
     $name = $row['name'];
     $date = $row['date'];     
     $volbuy = $row['volbuy'];
     $buy_price = $row['buy_price'];
     $mkt_price = $row['mkt_price'];     
     $amtbuy = $row['amtbuy'];
     $amtmkt = $row['amtmkt'];     
     $amtpol = $row['amtpol'];
     $amtpol = $row['amtpol'];
     $pctpol = $row['pctpol'];
     
     // Loss item
     if ($mkt_price < $buy_price)
     { 
     $stock_details .= <<<EOD
			<tr bgcolor='#E7F3FF'>
				<td>
					$period
				</td>	
				<td>
					$grade
				</td>						
				<td>
					$name
				</td>
				<td align='center'>
					$date
				</td>				
				<td>
					<div align='right'>
						$volbuy
					</div>
				</td>
				<td>
					<div align='right'>
						$buy_price
					</div>
				</td>
				<td >
					<div align='right'>
						$mkt_price
					</div>
				</td>
				<td >
					<div align='right'>
						$amtbuy
					</div>
				</td>
				<td>
					<div align='right'>
						$amtmkt
					</div>
				</td>
				<td>
					<div align='right'>
						<font color="#FF0000">$amtpol</font>
					</div>
				</td>
				<td>
					<div align='right'>
						<font color="#FF0000">$pctpol</font>
					</div>
				</td>
EOD;
} else { 
     // Profit item
     //if ($mkt_price >= $buy_price) {
     $stock_details .=<<<EOD
			<tr bgcolor='#E7F3FF'>
				<td >
					$period
				</td>
				<td >
					$grade
				</td>							
				<td >
					$name
				</td>
				<td align='center'>
					$date
				</td>						
				<td>
					<div align='right'>
						$volbuy
					</div>
				</td>
				<td>
					<div align='right'>
						$buy_price
					</div>
				</td>
				<td >
					<div align='right'>
						$mkt_price
					</div>
				</td>
				<td >
					<div align='right'>
						$amtbuy
					</div>
				</td>
				<td>
					<div align='right'>
						$amtmkt
					</div>
				</td>
				<td>
					<div align='right'>
						<font color="#009900">$amtpol</font>
					</div>
				</td>
				<td>
					<div align='right'>
						<font color="#009900">$pctpol</font>
					</div>
				</td>
EOD;
		}
}

// Find total buy amount
$query_buy = "SELECT 
          SUM(volbuy * buy.price) AS ttlbuy
          FROM buy INNER JOIN price ON buy.name = price.name
          WHERE price.date = '$price_date'
          AND active = 1";
          
if (!($result_buy = mysql_query($query_buy,$connection)))
   showerror();
$row_buy = mysql_fetch_array($result_buy);
extract($row_buy);
$cost = number_format($ttlbuy,2);
//print $cost;

// Find total market amount
$query_mkt = "SELECT 
          SUM(volbuy * price.price) AS ttlmkt
          FROM buy INNER JOIN price ON buy.name = price.name
          WHERE price.date = '$price_date'
          AND active = 1";
if (!($result_mkt = mysql_query($query_mkt,$connection)))
   showerror();
$row_mkt = mysql_fetch_array($result_mkt);
extract($row_mkt);
$market = number_format($ttlmkt,2);
//print $market;

// Find profit/loss amt & pct
$profit = number_format(($ttlmkt - $ttlbuy),2);
//print $profit;
$percent = number_format((($ttlmkt - $ttlbuy) / $ttlbuy) * 100,2); 
//print $percent;

if ($profit >= 0)
{
$stock_ttl =<<<EOD
			<tr>
				<td colspan='6'>
				</td>
				<td  bgcolor='#BDD3F7'>
					<div align='right'>
						<b>
							Total 
						</b>
					</div>
				</td>
				<td  bgcolor='#BDD3F7'>
					<div align='right'>
						$cost
					</div>
				</td>
				<td  width='10%' bgcolor='#BDD3F7'>
					<div align='right'>
						$market
					</div>
				</td>
				<td  width='13%' bgcolor='#BDD3F7'>
					<div align='right'>
						<font color='#009900'>$profit</font>
					</div>
				</td>
				<td  width='13%' bgcolor='#BDD3F7'>
					<div align='right'>
						<font color='#009900'>$percent</font>
					</div>
				</td>
			</tr>
EOD;
} else {
$stock_ttl =<<<EOD
			<tr>
				<td colspan='6'>
				</td>
				<td  bgcolor='#BDD3F7'>
					<div align='right'>
						<b>
							Total 
						</b>
					</div>
				</td>
				<td  bgcolor='#BDD3F7'>
					<div align='right'>
						$cost
					</div>
				</td>
				<td  width='10%' bgcolor='#BDD3F7'>
					<div align='right'>
						$market
					</div>
				</td>
				<td  width='13%' bgcolor='#BDD3F7'>
					<div align='right'>
						<font color='#FF0000'>$profit</font>
					</div>
				</td>
				<td  width='13%' bgcolor='#BDD3F7'>
					<div align='right'>
						<font color='#FF0000'>$percent</font>
					</div>
				</td>
			</tr>
EOD;
}	

// Find set index
$query_set = "SELECT setindex FROM setindex WHERE date = '$price_date'";
if (!($result_set = mysql_query($query_set,$connection)))
   showerror();
$row_set = mysql_fetch_array($result_set);
extract($row_set);
//print $market;

$stock_set =<<<EOD
			<tr>
				<td colspan='8'>
				</td>
				<td  bgcolor='#BDD3F7' colspan='2'>
					<div align='right'>
						<b>
							SET Index : 
						</b>
					</div>
				</td>
				<td  bgcolor='#BDD3F7'>
					<div align='right'>
						$setindex
					</div>
				</td>
			</tr>
EOD;

$stock_footer ="</table>\n";

$stock =<<<STOCK
$stock_header
$stock_details
$stock_ttl
$stock_set
$stock_footer
STOCK;

     // print "There are $num_days days in our database";
     print $stock;
?>
</body>
</html>