<?php
// Display errors for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database configuration
require "DB.inc";

// Establish database connection
$connection = @mysql_connect($hostName, $username, $password);
if (!$connection) {
    showerror();
}

if (!mysql_select_db($databaseName, $connection)) {
    showerror();
}

// Retrieve the stock name from POST request
$stock_name = isset($_POST['Stock_Name']) ? mysql_real_escape_string($_POST['Stock_Name']) : '';

// Fetch the maximum date for the selected stock
$query_max = "SELECT MAX(date) AS max_date FROM price WHERE name = '$stock_name'";
$result_max = mysql_query($query_max, $connection);
if (!$result_max) {
    showerror();
}
$row_max = mysql_fetch_assoc($result_max);
$to_date = $row_max['max_date'];

// Fetch the stock data by joining 'price' and 'setindex' tables
$query = "SELECT 
            price.name, 
            price.date AS date, 
            DAYNAME(price.date) AS day, 
            price.price, 
            price.maxp, 
            price.minp,
            setindex.setindex, 
            price.qty, 
            (price.qty * price.price) AS amt
          FROM price 
          INNER JOIN setindex ON price.date = setindex.date
          WHERE price.name = '$stock_name' 
          ORDER BY price.date DESC";

$result = mysql_query($query, $connection);
if (!$result) {
    showerror();
}

$num_days = mysql_num_rows($result);

// Initialize arrays to store chart data
$labels = [];
$prices = [];
$setindexes = []; // New array for setindex data

// Initialize variable to store table rows
$stock_details = '';

// Counter to calculate percentage change
$i = 0;
$price0 = 0;

// Loop through the fetched data to populate the table and chart data
while ($row = mysql_fetch_assoc($result)) {
    $i++;

    $date = $row['date'];
    $day = $row['day'];
    $price = $row['price'];
    $maxp  = $row['maxp'];
    $minp  = $row['minp'];
    $setindex = $row['setindex'];
    $qty  = $row['qty'];
    $fmtQty = number_format($qty, 0, '.', ',');	 
    $amt = $row['amt'];
    $fmtAmt = number_format($amt / 1000000, 3, '.', '');

    if ($i == 1) {
        $price0 = $price;
        $pct = 0;
    } else {
        $pct = number_format((($price - $price0) / $price0) * 100, 2, '.', '');
    }

    // Add data to chart arrays
    $labels[] = $date; // You can format the date as needed
    $prices[] = $price;
    $setindexes[] = $setindex; // Add setindex to its array

    // Append the row to the table details
    $stock_details .=<<<EOD
        <tr>
            <td class="date">$date</td>				
            <td class="price">$price</td>
            <td class="max">$maxp</td>
            <td class="min">$minp</td>
            <td class="setindex">$setindex</td> <!-- New column for Set Index -->
            <td class="qty">$fmtQty</td>
        </tr>\n
EOD;
}

if ($num_days > 0) {
    // Encode PHP arrays into JSON for JavaScript
    $labels_json = json_encode(array_reverse($labels)); // Reverse to have chronological order
    $prices_json = json_encode(array_reverse($prices));
    $setindexes_json = json_encode(array_reverse($setindexes)); // Encode setindex data
} else {
    $labels_json = json_encode([]);
    $prices_json = json_encode([]);
    $setindexes_json = json_encode([]);
}

// Close the database connection
mysql_close($connection);

// Define the HTML structure with embedded JavaScript for Chart.js
$stock =<<<STOCK
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stock Inquiry - $stock_name</title>
    <link href="css/table_style.css" rel="stylesheet" type="text/css">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include jQuery (optional, only if needed for other functionalities) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Optional: Add some basic styling */
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        #chartContainer { width: 80%; margin: auto; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .date { width: 15%; }
        .price, .max, .min, .setindex, .qty { width: 15%; }
    </style>
</head>
<body>
    <h1>$stock_name</h1>	
    <div id="chartContainer">
        <canvas id="myChart"></canvas>
    </div>
    <table id="datatable" class="myTable" border="1" align="center">
        <thead>
            <tr>	
                <th>Date</th>
                <th>Price</th>
                <th>Maximum</th>
                <th>Minimum</th>
                <th>Set Index</th> <!-- New header for Set Index -->
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            $stock_details
        </tbody>
    </table>

    <script type="text/javascript">
        // Parse PHP JSON data into JavaScript variables
        const labels = $labels_json;
        const prices = $prices_json;
        const setindexes = $setindexes_json; // Received setindex data

        // Check if there is data to display
        if (labels.length > 0 && prices.length > 0 && setindexes.length > 0) {
            // Setup the Chart.js chart
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line', // You can change this to 'bar', 'pie', etc.
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Price Over Time',
                            data: prices,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Light blue fill
                            borderColor: 'rgba(54, 162, 235, 1)', // Blue border
                            borderWidth: 1,
                            fill: true,
                            tension: 0.1, // Smoothness of the line
                            yAxisID: 'yPrice'
                        },
                        {
                            label: 'Set Index Over Time',
                            data: setindexes,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Light red fill
                            borderColor: 'rgba(255, 99, 132, 1)', // Red border
                            borderWidth: 1,
                            fill: true,
                            tension: 0.1, // Smoothness of the line
                            yAxisID: 'ySetIndex'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    stacked: false,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        yPrice: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Price'
                            },
                            beginAtZero: false
                        },
                        ySetIndex: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Set Index'
                            },
                            grid: {
                                drawOnChartArea: false, // Prevent grid lines from overlapping
                            },
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            mode: 'nearest',
                            intersect: false
                        },
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Stock Price vs. Set Index Over Time'
                        }
                    }
                }
            });
        } else {
            // If no data, display a message
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = '<p style="text-align:center;">No data available to display the chart.</p>';
        }
    </script>
</body>
</html>
STOCK;

// Output the complete HTML
echo $stock;
?>