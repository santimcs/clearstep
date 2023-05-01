<?php include('inc/header.php'); ?>

<div class="container">
<div class="page-content">

	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="#">Day Trade</a></li>
			<li><a href="FrqncyFrm3.php">Frequency</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Frequency</h1>
	</div>	<!-- End of Row -->
		
	<div class="row">
		<form class="form-horizontal" method="post" action="FrqncyInq3.php">
			<div class="form-group">		
				<label class="control-label col-md-4" for="date">Type</label>
				<div class="col-md-8">
					<input  type="radio" name="direction" value="T0102" checked="checked">
					<label for="NewLow">1 -> 2</label>
					<input  type="radio" name="direction" value="T0405">
					<label for="Low">4 -> 5</label>
					<input  type="radio" name="direction" value="T0910">
					<label for="High">9 -> 10</label>
					<input  type="radio" name="direction" value="T2425">
					<label for="NewHigh">24 -> 25</label>
				</div>
			</div>	<!-- End of form-group -->
			<div class="form-group">
				<label class="control-label col-md-4">Price date</label>
				<div class="col-md-8">
						<?php
							require "DB.inc";
							require "DateOpt.php";
							if (!($connection = @ mysql_connect($hostName, $username, $password)))
								showerror( );
							if (!mysql_select_db($databaseName, $connection))
								showerror( );
							$date_select = '';
							selectDistinctDate($connection, "price", "date", "to_date", $date_select);
						?>
				</div>
			</div>	<!-- End of form-group -->
			<div class="form-group">			
				<label class="control-label col-md-4">Duration</label>
				<div class="col-md-8">
					<input  type="radio" name="duration" value="1" id="1" checked="checked">
					<label for="7">Today</label>			 
					<input  type="radio" name="duration" value="7" id="7">
					<label for="7">1 Week</label>
					<input  type="radio" name="duration" value="14" id="14">
					<label for="14">2 Weeks</label>
					<input  type="radio" name="duration" value="28" id="28">
					<label for="28">1 Month</label>			
				</div>
			</div>	<!-- End of form-group -->
			<div class="form-group">
				<div class="col-md-offset-4 col-md-8">			
					<button type="submit" class="btn btn-large btn-primary">Submit</button>
				</div>
			</div>	<!-- End of form-group -->				
		</form>
		
	</div>	<!-- End of Row -->	
	
</div> <!-- End of Page-content -->	
</div> <!-- End of Container -->	

<?php include('inc/footer.php'); ?>

