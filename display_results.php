<?php

    session_start();

    require_once 'vendor/autoload.php'; // Assuming you installed 'monolog/monolog' and 'davaxi/sparkline' packages via composer

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Davaxi\Sparkline;

    // Database connections
    $db_stock = new PDO('mysql:host=localhost;dbname=stock', 'root', '');
    $db_development = new PDO('sqlite:C:\\ruby\\portlt\\db\\development.sqlite3');
    $db_portpg = new PDO('pgsql:host=localhost;dbname=portpg_development', 'postgres', 'admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dividend Payout Ratio</title>
    <link href="css/global.css" rel="stylesheet">
</head>
<body>
    <div class="container page-content">
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['inp_date'])) {
                    $_SESSION['inp_date'] = $_POST['inp_date'];
                }
                $inp_date = $_SESSION['inp_date'];     
                          
                // When indent the $sql_upd will cause error, don't know why 
                // $sql_upd = <<<SQL
                // UPDATE buy B
                // SET dividend =
                // (SELECT DIVIDEND FROM dividend D
                // WHERE B.name = D.name)
                // SQL;
                // $db_stock->exec($sql_upd);

                // If do not use heredoc, will not error, so should avoid heredoc 
                $sql_upd = "UPDATE buy B
                SET dividend =
                (SELECT DIVIDEND FROM dividend D
                WHERE B.name = D.name)";
                $db_stock->exec($sql_upd);

                // First SQL query
                $sql1 = "SELECT B.name, volbuy, B.price AS u_cost,
                dividend, P.price AS mkt_price, period AS prd
                FROM buy B
                JOIN price P
                ON B.name = P.name
                WHERE P.date = ?
                AND active = 1
                ORDER BY period, name";
                $statement = $db_stock->prepare($sql1);
                $statement->execute([$inp_date]);
                $rows1 = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Second SQL query
                $sql2 = "SELECT name, aq_eps AS eps
                FROM epss
                WHERE year = 2022 AND quarter = 4";
                $statement = $db_development->prepare($sql2);
                $statement->execute();
                $rows2 = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Third SQL query
                $sql3 = "SELECT name, market
                FROM tickers";
                $statement = $db_portpg->prepare($sql3);
                $statement->execute();
                $rows3 = $statement->fetchAll(PDO::FETCH_ASSOC);
            
                function findRowByName($rows, $name) {
                    foreach ($rows as $row) {
                        if ($row['name'] === $name) {
                            return $row;
                        }
                    }
                    return null;
                }
                
                $merged = [];
                foreach ($rows1 as $row1) {
                    $row2 = findRowByName($rows2, $row1['name']);
                    $row3 = findRowByName($rows3, $row1['name']);
                
                    if ($row2 !== null && $row3 !== null) {
                        $mergedRow = array_merge($row1, $row2, $row3);
                        $merged[] = $mergedRow;
                    }
                }
        
                $formatted_rows = array_map(function ($row) {
                    $cost_amt = $row['volbuy'] * $row['u_cost'];
                    $mkt_amt = $row['volbuy'] * $row['mkt_price'];
                    $div_amt = $row['volbuy'] * $row['dividend'];
                    return [
                        'prd' => $row['prd'],
                        'name' => $row['name'],
                        'shares' => number_format($row['volbuy']),
                        'u_cost' => number_format($row['u_cost'], 2),
                        'mkt_price' => number_format($row['mkt_price'], 2),
                        'dividend' => number_format($row['dividend'], 4),
                        'cst_percent' => number_format($div_amt / $cost_amt * 100, 2) . "%",
                        'mkt_percent' => number_format($div_amt / $mkt_amt * 100, 2) . "%",
                        'eps' => number_format($row['eps'], 4),
                        'dpr_percent' => number_format($row['dividend'] / $row['eps'] * 100, 2) . "%",
                        'market' => $row['market']
                    ];
                    }, $merged);

                    // Display the results using table, tr, and td elements
                echo "<div class=\"row\"><div class=\"col-md-12\">";
                // echo "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\">";
                echo "<table id=\"example\" class=\"display\" cellspacing=\"2\" width=\"100%\">";

                echo "<tr><th>T</th><th>Name</th><th>Shares</th><th>Div</th><th>U_cost</th><th>Price</th><th>EPS</th><th>Cst-%</th><th>Mkt-%</th><th>Dpr-%</th><th>Market</th></tr>";
                foreach ($formatted_rows as $row) {
                    echo "<tr>";
                    echo "<td>{$row['prd']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['shares']}</td>";
                    echo "<td>{$row['dividend']}</td>";
                    echo "<td>{$row['u_cost']}</td>";
                    echo "<td>{$row['mkt_price']}</td>";
                    echo "<td>{$row['eps']}</td>";
                    // Check if mkt_percent is greater than or equal to 5.00
                    $cst_percent_value = floatval(str_replace('%', '', $row['cst_percent']));
                    if ($cst_percent_value >= 5.00) {
                        echo "<td style=\"color: #00FF00 !important;\">{$row['cst_percent']}</td>";
                    } else {
                        echo "<td>{$row['cst_percent']}</td>";
                    }                    
                    // Check if mkt_percent is greater than or equal to 5.00
                    $mkt_percent_value = floatval(str_replace('%', '', $row['mkt_percent']));
                    if ($mkt_percent_value >= 5.00) {
                        echo "<td style=\"color: #00FF00 !important;\">{$row['mkt_percent']}</td>";
                    } else {
                        echo "<td>{$row['mkt_percent']}</td>";
                    }                    
                    echo "<td>{$row['dpr_percent']}</td>";
                    echo "<td>{$row['market']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div></div>";
            }
        ?>
    </div>
</body>
</html>