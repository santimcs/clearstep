<!DOCTYPE html>
<html lang="en">
<!-- ... -->
<body>
    <div class="container page-content">
        <!-- ... -->

        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // ...
                $merged = array_filter(/* ... */);

                $formatted_rows = array_map(/* ... */);

                // Display the results
                echo "<div class=\"row\"><div class=\"col-md-12\"><pre>";
                echo "prd\tname\tshares\tdiv\tu_cost\tprice\teps \tcst-%\tmkt-%\tdpr-%\tmarket\n";
                foreach ($formatted_rows as $row) {
                    echo "{$row['prd']}\t{$row['name']}\t{$row['shares']}\t{$row['dividend']}\t{$row['u_cost']}\t{$row['mkt_price']}\t{$row['eps']} \t{$row['cst_percent']}\t{$row['mkt_percent']}\t{$row['dpr_percent']}\t{$row['market']}\n";
                }
                echo "</pre></div></div>";
            }
        ?>
    </div>
</body>
</html>
