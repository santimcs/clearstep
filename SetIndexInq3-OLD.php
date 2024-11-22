<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
	showerror();

if (!mysql_select_db($databaseName, $connection))
	showerror( );

$query =  "SELECT date, setindex FROM setindex ORDER BY date DESC";

if (!($result = mysql_query($query,$connection)))
   showerror();

$stock_header=<<<EOD

<html>
	<head>
		<title>SET Index Inquiry</title>
		<link href="css/table_style.css" rel="stylesheet" type="text/css">
				
      <!-- Old jQuery version -->
      <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script> -->

      <!-- Updated jQuery version -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

		// <script src="http://code.highcharts.com/stock/highstock.js"></script>
		// <script src="http://code.highcharts.com/stock/modules/exporting.js"></script>
      // <script src="https://code.highcharts.com/stock/highstock.js"></script>
      // <script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
      <script src="https://code.highcharts.com/highcharts.js"></script>
      <script src="https://code.highcharts.com/stock/highstock.js"></script>
      <script src="https://code.highcharts.com/modules/exporting.js"></script>
		<script type="text/javascript">
		$(function () {
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
				var tableData = {};			
				var table = $('table');
				tableData.xLabels = [];
				tableData.setindex = [];
				var srySetindex = [];
				
				table.find('tbody td.date').each(function(){
					tableData.xLabels.push( Date.parse($(this).text()) );
				});
			//	console.log(tableData.xLabels);	
				table.find('tbody td.setindex').each(function(){
					tableData.setindex.push( $(this).html() );
				});
				
				for (var i = 0; i < tableData.xLabels.length; ++i) {
					srySetindex.push([(tableData.xLabels[i]),parseFloat(tableData.setindex[i])]); 
				};			
				srySetindex.reverse();
				$('#container').highcharts('StockChart', {
					rangeSelector: {
						selected: 1
					},
					title: {
						text: 'Stock Exchange of Thailand'
					},
					subtitle: {
						text: 'Index'
					},					
					series: [
						{
							name: 'Index',
							data: srySetindex,
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
		<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto">
		</div>
		<table id="datatable" class="myTable" align="center">
			<thead>
				<tr>
					<th></th>
					<th>Set Index</th>
				</tr>
			</thead>
			<tbody>
EOD;
$i = 0;
$stock_details = '';
while($row = mysql_fetch_array($result))
{
	 $i = $i + 1;

     $date = $row['date'];
     $setindex = $row['setindex'];

     $stock_details .=<<<EOD
	
				<tr>
					<td class="date">$date</td>
					<td class="setindex">$setindex</td>
				</tr>
EOD;
}

$stock_footer ="					
			</tbody>
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
