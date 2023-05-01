<?php include('inc/header.php'); ?>

	<div class="container page-content">
		<div class="row">
		    <ol class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="about.php">Daily</a></li>
				<li><a href="LoHiInq2.php">Low/High</a></li>
			</ol>
			<h1><span class="icon-rocket"></span>Low/High Inquiry</h1>
		</div>	<!-- End of Row -->
		
		<div class="row">
		<div class="col-md-4">
		</div>
		<div class="col-md-4">
			<form method="post" action="LoHiInq2.php" name="signup" id="signup">
				<h3><span class="label label-default">Type</span></h3>
					<input  type="radio" name="direction" value="NewLow">
					<label for="NewLow">New Low</label>
					<input  type="radio" name="direction" value="Low">
					<label for="Low">Low</label>
					<input  type="radio" name="direction" value="High">
					<label for="High">High</label>
					<input  type="radio" name="direction" value="NewHigh" checked="checked">
					<label for="NewHigh">New High</label>
				<div>
					<h3><span class="label label-default">Date</span></h3>
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
				</div>
				</br>
				<div>
					<button type="submit" class="btn btn-large btn-primary">Submit</button>
				</div>
			</form>
		</div>  <!-- class="col-md-4" -->
		</div>  <!-- End of Row -->
	</div>  <!-- Container -->
	
<?php include('inc/footer.php'); ?>
