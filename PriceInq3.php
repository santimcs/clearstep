<?php
// TO MODIFY NUMBER OF MONTHS GRAPH, CHANGE rangeSelector
require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
   showerror();

if (!mysql_select_db($databaseName, $connection))
     showerror( );

$stock_name = isset($_POST['Stock_Name']) ? $_POST['Stock_Name'] : '';
//$duration = $_POST[duration];

// Find maximum date
$query_max = "SELECT MAX(date) AS max_date FROM price WHERE name = '$stock_name'";
if (!($result_max = mysql_query($query_max,$connection)))
   showerror();
$row_max = mysql_fetch_array($result_max);
extract($row_max);
$to_date = date($max_date);
//print "Maximum date: " . $to_date;

//int "From date: " . $fm_date;

$query = "SELECT name, price.date AS date, DAYNAME(price.date) AS day, price, maxp, minp,
		setindex, qty, qty*price AS amt
          FROM price INNER JOIN setindex ON price.date = setindex.date
          WHERE name = '$stock_name' 
		ORDER BY price.date DESC";

if (!($result = mysql_query($query,$connection)))
   showerror();

$num_days = mysql_num_rows($result);

$stock_header=<<<EOD
<html>
	<head>
		<title>Price Inquiry</title>
		<link href="css/table_style.css" rel="stylesheet" type="text/css">
		
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.highcharts.com/stock/highstock.js"></script>
		<script src="http://code.highcharts.com/stock/modules/exporting.js"></script>
		<script type="text/javascript">
			$(function() {
				/**
				* Dark theme for Highcharts JS
				* @author Torstein Honsi
				*/

				// Load the fonts
				Highcharts.createElement('link', {
				href: '//fonts.googleapis.com/css?family=Unica+One',
				rel: 'stylesheet',
				type: 'text/css'
				}, null, document.getElementsByTagName('head')[0]);

				Highcharts.theme = {
					colors: ["#2b908f", "#90ee7e", "#f45b5b", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
					"#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
					chart: {
						backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
						stops: [
							[0, '#2a2a2b'],
							[1, '#3e3e40']
						]
					},
					style: {
						fontFamily: "'Unica One', sans-serif"
						},
						plotBorderColor: '#606063'
					},
					title: {
						style: {
							color: '#E0E0E3',
							textTransform: 'uppercase',
							fontSize: '20px'
						}
					},
					subtitle: {
						style: {
							color: '#E0E0E3',
							textTransform: 'uppercase'
						}
					},
					xAxis: {
						gridLineColor: '#707073',
						labels: {
							style: {
								color: '#E0E0E3'
							}
						},
						lineColor: '#707073',
						minorGridLineColor: '#505053',
						tickColor: '#707073',
						title: {
							style: {
								color: '#A0A0A3'
							}
						}
					},
					yAxis: {
						gridLineColor: '#707073',
						labels: {
							style: {
								color: '#E0E0E3'
							}
						},
						lineColor: '#707073',
						minorGridLineColor: '#505053',
						tickColor: '#707073',
						tickWidth: 1,
						title: {
							style: {
								color: '#A0A0A3'
							}
						}
					},
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.85)',
						style: {
							color: '#F0F0F0'
						}
					},
					plotOptions: {
						series: {
							dataLabels: {
								color: '#B0B0B3'
							},
							marker: {
								lineColor: '#333'
							}
						},
						boxplot: {
							fillColor: '#505053'
						},
						candlestick: {
							lineColor: 'white'
						},
						errorbar: {
							color: 'white'
						}
					},
					legend: {
						itemStyle: {
							color: '#E0E0E3'
						},
						itemHoverStyle: {
							color: '#FFF'
						},
						itemHiddenStyle: {
							color: '#606063'
						}
					},
					credits: {
						style: {
							color: '#666'
						}
					},
					labels: {
						style: {
							color: '#707073'
						}
					},
					drilldown: {
						activeAxisLabelStyle: {
							color: '#F0F0F3'
						},
						activeDataLabelStyle: {
							color: '#F0F0F3'
						}
					},
					navigation: {
						buttonOptions: {
							symbolStroke: '#DDDDDD',
							theme: {
								fill: '#505053'
							}
						}
					},
					// scroll charts
					rangeSelector: {
						buttonTheme: {
							fill: '#505053',
							stroke: '#000000',
							style: {
								color: '#CCC'
							},
							states: {
								hover: {
									fill: '#707073',
									stroke: '#000000',
									style: {
										color: 'white'
									}
								},
								select: {
									fill: '#000003',
									stroke: '#000000',
									style: {
										color: 'white'
									}
								}
							}
						},
						inputBoxBorderColor: '#505053',
						inputStyle: {
							backgroundColor: '#333',
							color: 'silver'
						},
						labelStyle: {
							color: 'silver'
						}
					},
					navigator: {
						handles: {
							backgroundColor: '#666',
							borderColor: '#AAA'
						},
						outlineColor: '#CCC',
						maskFill: 'rgba(255,255,255,0.1)',
						series: {
							color: '#7798BF',
							lineColor: '#A6C7ED'
						},
						xAxis: {
							gridLineColor: '#505053'
						}
					},
					scrollbar: {
						barBackgroundColor: '#808083',
						barBorderColor: '#808083',
						buttonArrowColor: '#CCC',
						buttonBackgroundColor: '#606063',
						buttonBorderColor: '#606063',
						rifleColor: '#FFF',
						trackBackgroundColor: '#404043',
						trackBorderColor: '#404043'
					},
					// special colors for some of the
					legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
					background2: '#505053',
					dataLabelsColor: '#B0B0B3',
					textColor: '#C0C0C0',
					contrastTextColor: '#F0F0F3',
					maskColor: 'rgba(255,255,255,0.3)'
				};

				// Apply the theme
				Highcharts.setOptions(Highcharts.theme);
				
				//add your code here
				var tableData = {};	
				var table = $('table');
				tableData.xLabels = [];
				tableData.price = [];
				tableData.max = [];
				tableData.min = [];
				var sryPrice = [];
				var sryMax = [];
				var sryMin = [];
				
				table.find('tbody td.date').each(function(){
					tableData.xLabels.push( Date.parse($(this).text()) );
				});
				tableData.xLabels.reverse();
			//	console.log(tableData.xLabels);	
				table.find('tbody td.price').each(function(){
					tableData.price.push( $(this).html() );
				});
				tableData.price.reverse();
				table.find('tbody td.max').each(function(){
					tableData.max.push( $(this).html() );
				});
				tableData.max.reverse();
				table.find('tbody td.min').each(function(){
					tableData.min.push( $(this).html() );
				});	
				tableData.min.reverse();
				for (var i = 0; i < tableData.xLabels.length; ++i) {
					sryPrice.push([(tableData.xLabels[i]),parseFloat(tableData.price[i])]); 
					sryMax.push([(tableData.xLabels[i]),parseFloat(tableData.max[i])]);	
					sryMin.push([(tableData.xLabels[i]),parseFloat(tableData.min[i])]);					
				};	
				$('#container').highcharts('StockChart',{
					rangeSelector : {
						selected : 1
					},
					credits : {
						enabled : true
					},					
					title: {
						text: 'Stock Price'
					},			
					series: [
						{
							data: sryPrice,
							type : 'areaspline',
							threshold : null,
							tooltip: {
								valueDecimals: 2
							},
							fillColor : {
								linearGradient : {
									x1: 0,
									y1: 0,
									x2: 0,
									y2: 1
								},
								stops : [
									[0, Highcharts.getOptions().colors[0]],
									[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
								]
							}								
						}
					]
				});
				$('<input type="button" value="toggle table" id="toggleButton">')
				.insertBefore('#datatable');
				$('#toggleButton').click(function() {
					$('#datatable').fadeToggle(4000, function(){
						//
					});
				});
			});
		</script>	
	</head>
	<body>
		<h1 align="center">$stock_name</h1>	
		<div id="container" style="min-width: 310px; height: 400px;"></div>
		<table id="datatable" class="myTable" border="1" align="center">
						<thead>
							<tr>	
								<th></th>
								<th>Price</th>
								<th>Maximum</th>
								<th>Minimum</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
EOD;
$i = 0;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
	   $i = $i + 1;
     $stock_name = $row['name'];
     $date = $row['date'];
     $day = $row['day'];
     $price = $row['price'];
     $maxp  = $row['maxp'];
     $minp  = $row['minp'];
     $setindex = $row['setindex'];
     $qty  = $row['qty'];
     $fmtQty = number_format($qty,0,'.',',');	 
     $amt = $row['amt'];
     $fmtAmt = number_format($amt/1000000,3,'.',',');
    
     if ($i == 1) {
	     $price0 = $price;
	     $pct = 0;
   } else {
	   $pct = number_format(($price - $price0)/$price0 * 100,2,'.','');
 }

$stock_details .=<<<EOD
							<tr>
								<td class="date">$date</td>				
								<td class="price">$price</td>
								<td class="max">$maxp</td>
								<td class="min">$minp</td>
								<td class="qty">$fmtQty</td>
							</tr>\n
EOD;
}


$stock_footer ="					</tbody>
					</table>
     </body>
</html>";


$stock =<<<STOCK
	$stock_header
	$stock_details
	$stock_footer
STOCK;

     print $stock;
?>
