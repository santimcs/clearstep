<?php

require "DB.inc";

if (!($connection = @ mysql_connect($hostName,$username,$password)))
    showerror();

if (!mysql_select_db($databaseName, $connection))
    showerror( );

$query =  "SELECT date, setindex FROM setindex ORDER BY date DESC";

if (!($result = mysql_query($query,$connection)))
   showerror();

$stock_header = <<<EOD
<html>
    <head>
        <title>SET Index Inquiry</title>
        <link href="css/table_style.css" rel="stylesheet" type="text/css">

        <!-- Include Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        
        <script type="text/javascript">
        \$(function () {

            var labels = [];
            var dataPoints = [];
EOD;

$i = 0;
$stock_details = '';
$table_rows = ''; // To store table rows
while($row = mysql_fetch_array($result))
{
     $i++;

     $date = $row['date'];
     $setindex = $row['setindex'];

     // Create JavaScript arrays for labels and data points
     $stock_details .= "labels.push('$date');\n";
     $stock_details .= "dataPoints.push($setindex);\n";

    // Also build the HTML table if needed for display
    $table_rows .=<<<EOD
        <tr>
            <td class="date">$date</td>
            <td class="setindex">$setindex</td>
        </tr>
EOD;
}

$stock_footer = <<<EOD
            // Reverse the arrays so that data appears in chronological order
            labels.reverse();
            dataPoints.reverse();

            // Create the Chart.js chart
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line', // Chart type: line chart
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'SET Index',
                        data: dataPoints,
                        fill: true,
                        backgroundColor: 'rgba(75,192,192,0.4)',
                        borderColor: 'rgba(75,192,192,1)',
                        tension: 0.1 // Smoothness of the line
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Index'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Stock Exchange of Thailand Index'
                        }
                    }
                }
            });

            // Toggle the display of the table
            \$('#toggleButton').click(function() {
                \$('#datatable').fadeToggle(400);
            });

        });
        </script>
    </head>
    <body>
        <div style="width:80%; margin:0 auto;">
            <canvas id="myChart"></canvas>
        </div>
        <input type="button" value="Show/Hide Table" id="toggleButton">
        <table id="datatable" class="myTable" align="center">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Set Index</th>
                </tr>
            </thead>
            <tbody>
EOD;

$stock_footer .= $table_rows;

$stock_footer .= "
            </tbody>
        </table>
    </body>
</html>";

$stock = <<<STOCK
    $stock_header
    $stock_details
    $stock_footer
STOCK;

print $stock;
?>