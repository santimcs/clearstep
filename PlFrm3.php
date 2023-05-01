<?php include('inc/header.php'); ?>

<div class="container">
<div class="page-content">

	<div class="row">
		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="PlFrm3.php">Portfolio</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Portfolio</h1>
	</div>	<!-- End of Row -->
		
	<div class="row">
		<form class="form-horizontal" method="post" action="PLInq3.php">
			<div class="form-group">
				<label class="control-label col-md-4" for="date">Price Date</label>
				<div class="col-md-8">	
<?php
	require "DB.inc";
    // selectDistinct( ) function shown in Example 6-9 goes here
  require "DateOpt.php";

  // Connect to the server
  if (!($connection = @ mysql_connect($hostName, $username, $password)))
     showerror( );

  if (!mysql_select_db($databaseName, $connection))
     showerror( );
     
  $date_select = '2005-04-07';
  //print $date_select;
  selectDistinctDate($connection, "price", "date", "Price_Date", $date_select);
?>		
				</div>	<!-- End of controls -->
			</div>	<!-- End of form-group -->
			<div class="form-group">
				<label class="control-label col-md-4" for="Active">Active?</label>
				<div class="col-md-8">
					<input type="radio" name="Active" value="1" CHECKED> Yes 
					<br/>
					<input type="radio" name="Active" value="no"> No 
				</div> <!-- End of controls -->	
			</div>	<!-- End of form-group -->
			<div class="form-group">
				<label class="control-label col-md-4" for="orderby">Order By</label>
				<div class="col-md-8">
					<input type="radio" name="Sequence" value="date"> Date
					<br/> 
					<input type="radio" name="Sequence" value="name"> Name 
					<br/> 
					<input type="radio" name="Sequence" value="percent" CHECKED> Percent 
					<br/> 
				</div> <!-- End of controls -->	
			</div>	<!-- End of form-group -->					
			<div class="form-group">
				<div class="col-md-offset-4 col-md-8">				
					<br/> 									
					<button type="submit" class="btn btn-large btn-primary">Submit</button>
					&nbsp;&nbsp;&nbsp; 
					<button type="reset" class="btn btn-large btn-danger">Reset</button> 
				</div> <!-- End of controls -->							
			</div>	<!-- End of form-group -->					
		</form>	
		
	</div>	<!-- End of Row -->	
	
</div> <!-- End of Page-content -->	
</div> <!-- End of Container -->	

<?php include('inc/footer.php'); ?>
