<?php include('inc/header.php'); ?>

<div class="container page-content">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="PriceFrm3.php">Price</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Price</h1>
	</div>	<!-- End of Row -->
		
	<div class="row">
		<div class="col-sm-4 col-md-4 widget">
			<p>---------1---------2---------3 Column 1 ---------1---------2---------3</p>
			<div class="thumbnail widget">
				<img src="http://lorempixel.com/400/400" />
			</div><!--end thumbnail -->
		</div>
		<div class="col-sm-4 col-md-4 widget">
			<p>---------1---------2---------3 Column 2 ---------1---------2---------3</p>
			<form method="post" action="PriceInq3.php" role="form" class="foot-form">
			<fieldset>
				<legend>Legend</legend>		

				<label for="name">Stock Name</label>
<?php
	require "DB.inc";
  require "NameOpt.php";
  if (!($connection = @ mysql_connect($hostName, $username, $password)))
     showerror( );
  if (!mysql_select_db($databaseName, $connection))
     showerror( );
		$defaultValue = '';
     selectDistinctName($connection, "stockname", "name", "Stock_Name", $defaultValue);
?>
				<br />
				<button type="submit" class="btn btn-large btn-primary">Submit</button>
				</fieldset>
			</form>
		</div>
		<div class="col-sm-4 col-md-4 widget">
			<p>---------1---------2---------3 Column 3 ---------1---------2---------3</p>
			<div class="thumbnail widget">
				<img src="http://lorempixel.com/400/400" />
			</div><!--end thumbnail -->
		</div> <!-- End of col-md-4 -->
	</div>	<!-- End of Row -->		
</div> <!-- End of Container -->	
<?php include('inc/footer.php'); ?>
               
