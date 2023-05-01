<?php include('inc/header.php'); ?>

<div class="container page-content">

	<div class="row">

		<ol class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="about.php">Daily</a></li>
			<li><a href="FromToFrm2.php">Set Date</a></li>
		</ol>
		<h1><span class="icon-rocket"></span>Set Date</h1>
	</div>	<!-- End of Row -->
	
	<div class="row">
		<div class="col-md-3">
		</div>
		<div class="col-md-3">	
		<form method="post" action="FromToInq2.php">
				<h3><span class="label label-default">To Date</span></h3>
				<input type="text" name="to" id="to">					
				<h3><span class="label label-default">From Date</span></h3>
				<input type="text" name="from" id="from">	

				<div>
				</br>				
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
		</form>

	</div>	<!-- End of Row -->

</div> <!-- Container -->
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>	

	<script>
	$(function() {
		$( "#from" ).datepicker({
			defaultDate: "-2w",
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			numberOfMonths: 2,
			onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
	</script>	
	
<?php include('inc/footer.php'); ?>