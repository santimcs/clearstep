<?php include('inc/header.php'); ?>

	<div class="container page-content">
		<div class="row">
		    <ol class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="PerFrm3.php">P/E Ratio</a></li>
			</ol>
			<h1><span class="icon-rocket"></span>P/E Ratio Inquiry</h1>
		</div>	<!-- End of Row -->
		
		<div class="row">
			<div class="col-md-4">
			</div>
			<div class="col-md-4">
				<form method="post" action="PerInq3.php">
            </br>
            </br>
                <div>
	                  <h3><span class="label label-default">Sector</span></h3>
                    <?php
	                     require "DB.inc";
                       require "SectorOpt.php";
                       if (!($connection = @ mysql_connect($hostName, $username, $password)))
                          showerror( );
                       if (!mysql_select_db($databaseName, $connection))
                          showerror( );
						$defaultValue = '';
                       selectDistinctName($connection, "stockname", "sector", "Sector_Name", $defaultValue);
                    ?>
                </div>
                </br>
                </br>
				<div>
					<button type="submit" class="btn btn-large btn-primary">Submit</button>
				</div>
            </form>
        </div>  <!-- class="col-md-4" -->
		</div>  <!-- End of Row -->
	</div>  <!-- Container -->
	
<?php include('inc/footer.php'); ?>


