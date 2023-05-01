<?php include('inc/header.php'); ?>

<div class="container page-content">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="PriceFrm3.php">Dividend</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Dividend</h1>
	</div>	<!-- End of Row -->
		
	<div class="row">
		<div class="col-sm-4 col-md-4 widget">
			<!-- <p>---------1---------2---------3 Column 1 ---------1---------2---------3</p>
			<div class="thumbnail widget">
				<img src="http://lorempixel.com/400/400" />
			</div> -->
		</div>
		<div class="col-sm-4 col-md-4 widget">
			<!-- <p>---------1---------2---------3 Column 2 ---------1---------2---------3</p> -->

            <form method="post" action="YieldInq3.php" role="form">
				<fieldset>
					<legend>Legend</legend>	
	                <label>Price date</label>
                    <?php
	                     require "DB.inc";
                       require "DateOpt.php";
                       if (!($connection = @ mysql_connect($hostName, $username, $password)))
                          showerror( );
                       if (!mysql_select_db($databaseName, $connection))
                          showerror( );
						$date_select = '';
                       selectDistinctDate($connection, "price", "date", "present", $date_select);
                    ?>
					<br />
					<br />					
                    <button type="submit" class="btn btn-large btn-primary">Submit</button> 
                </fieldset>
            </form>
        </div>  <!-- End of col-md-4 -->

		<div class="col-sm-4 col-md-4 widget">
			<!-- <p>---------1---------2---------3 Column 3 ---------1---------2---------3</p> -->
			<!-- <div class="thumbnail widget">
				<img src="http://lorempixel.com/400/400" />
			</div> -->
		</div> 
	</div>	<!-- End of Row -->		
</div> <!-- End of Container -->	
<?php include('inc/footer.php'); ?>
