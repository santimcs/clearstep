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
    <title>Dividend</title>
    <link href="inc/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="container page-content">
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // $inp_date = $_SESSION['inp_date'];                
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
                        'u_cost' => "B" . number_format($row['u_cost'], 2),
                        'mkt_price' => "B" . number_format($row['mkt_price'], 2),
                        'dividend' => "B" . number_format($row['dividend'], 4),
                        'cst_percent' => number_format($div_amt / $cost_amt * 100, 2) . "%",
                        'mkt_percent' => number_format($div_amt / $mkt_amt * 100, 2) . "%",
                        'eps' => "B" . number_format($row['eps'], 4),
                        'dpr_percent' => number_format($row['dividend'] / $row['eps'] * 100, 2) . "%",
                        'market' => $row['market']
                    ];
                    }, $merged);
                

                // Display the results
                echo "<div class=\"row\"><div class=\"col-md-12\"><pre>";
                echo "prd\tname\tshares\tdiv\tu_cost\tprice\teps\tcst-%\tmkt-%\tdpr-%\tmarket\n";
                foreach ($formatted_rows as $row) {
                    echo "{$row['prd']}\t{$row['name']}\t{$row['shares']}\t{$row['dividend']}\t{$row['u_cost']}\t{$row['mkt_price']}\t{$row['eps']}\t{$row['cst_percent']}\t{$row['mkt_percent']}\t{$row['dpr_percent']}\t{$row['market']}\n";
                }
                echo "</pre></div></div>";
            }
        ?>
    </div>
</body>
</html>
