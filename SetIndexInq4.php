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

        <!-- Подключаем Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        
        <script type="text/javascript">
        \$(function () {

            var labels = [];
            var dataPoints = [];
            
EOD;

$i = 0;
$stock_details = '';
$table_rows = ''; // Для хранения строк таблицы
while($row = mysql_fetch_array($result))
{
     $i++;

     $date = $row['date'];
     $setindex = $row['setindex'];

     // Формируем JavaScript-массивы для меток и данных
     $stock_details .= "labels.push('$date');\n";
     $stock_details .= "dataPoints.push($setindex);\n";

    // Также формируем HTML-таблицу, если она нужна для отображения
    $table_rows .=<<<EOD
        <tr>
            <td class="date">$date</td>
            <td class="setindex">$setindex</td>
        </tr>
EOD;
}

$stock_footer = <<<EOD
            // Разворачиваем массивы, чтобы данные отображались в хронологическом порядке
            labels.reverse();
            dataPoints.reverse();

            // Создаем график Chart.js
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line', // Тип графика: линия
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'SET Index',
                        data: dataPoints,
                        fill: true,
                        backgroundColor: 'rgba(75,192,192,0.4)',
                        borderColor: 'rgba(75,192,192,1)',
                        tension: 0.1 // Гладкость линии
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Дата'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Индекс'
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
                            text: 'Индекс фондовой биржи Таиланда'
                        }
                    }
                }
            });

            // Управление отображением таблицы
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
        <input type="button" value="Показать/Скрыть таблицу" id="toggleButton">
        <table id="datatable" class="myTable" align="center">
            <thead>
                <tr>
                    <th>Дата</th>
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

$stock =<<<STOCK
    $stock_header
    $stock_details
    $stock_footer
STOCK;

print $stock;
?>