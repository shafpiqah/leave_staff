<?php include('includes/header.php') ?>
<?php include('../includes/session.php') ?>
<?php
if (isset($_POST['apply'])) {
	$empid = $session_id;
	$fromdate = date('d-m-Y', strtotime($_POST['date_from']));
	$todate = date('d-m-Y', strtotime($_POST['date_to']));
	$description = $_POST['description'];
	$status = 0;
	$isread = 0;
	// $leave_days=$_POST['leave_days'];
	$outstation_location = $_POST['outstation_location'];
	$outstation_transport = $_POST['outstation_transport'];

	$datePosting = date("Y-m-d");

	if ($fromdate > $todate) {
		echo "<script>alert('End Date should be greater than Start Date');</script>";
	}
	//   this one to set leave limit, maybe
	// elseif($leave_days <= 0)
	// {
	//     echo "<script>alert('YOU HAVE EXCEEDED YOUR LEAVE LIMIT. LEAVE APPLICATION FAILED');</script>";
	//   }
	else {

		$DF = date_create($_POST['date_from']);
		$DT = date_create($_POST['date_to']);

		$diff = date_diff($DF, $DT);
		$num_days = (1 + $diff->format("%a"));

		// add logic when choosing tranpost for outstation
		if ($outstation_transport == 'department') {
			$driver_name = $_POST['driver_name'];
			$vehicle_plate_number = $_POST['vehicle_plate_number'];
			$sql = "INSERT INTO tblleaves (ToDate, FromDate, Description, Status, IsRead, empid, num_days, PostingDate, Location, TransportType, DriverName, VehiclePlateNumber) 
		VALUES (:todate, :fromdate, :description, :status, :isread, :empid, :num_days, :datePosting, :outstation_location, :outstation_transport, :driver_name, :vehicle_plate_number)";
		} elseif ($outstation_transport == 'own') {
			$sql = "INSERT INTO tblleaves (ToDate, FromDate, Description, Status, IsRead, empid, num_days, PostingDate, Location, TransportType) 
		VALUES (:todate, :fromdate, :description, :status, :isread, :empid, :num_days, :datePosting, :outstation_location, :outstation_transport)";
		} else {
			echo "<script>alert('Invalid transport option');</script>";
		}

		$query = $dbh->prepare($sql);

		$query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
		$query->bindParam(':todate', $todate, PDO::PARAM_STR);
		$query->bindParam(':description', $description, PDO::PARAM_STR);
		$query->bindParam(':status', $status, PDO::PARAM_STR);
		$query->bindParam(':isread', $isread, PDO::PARAM_STR);
		$query->bindParam(':empid', $empid, PDO::PARAM_STR);
		$query->bindParam(':num_days', $num_days, PDO::PARAM_STR);
		$query->bindParam(':datePosting', $datePosting, PDO::PARAM_STR);

		$query->bindParam(':outstation_location', $outstation_location, PDO::PARAM_STR);
		$query->bindParam(':outstation_transport', $outstation_transport, PDO::PARAM_STR);

		if ($outstation_transport == 'department') {
			$query->bindParam(':driver_name', $driver_name, PDO::PARAM_STR);
			$query->bindParam(':vehicle_plate_number', $vehicle_plate_number, PDO::PARAM_STR);
		}


		$query->execute();
		$lastInsertId = $dbh->lastInsertId();
		if ($lastInsertId) {
			echo "<script>alert('Leave Application was successful.');</script>";
			echo "<script type='text/javascript'> document.location = 'leave_history.php'; </script>";
		} else {
			echo "<script>alert('Something went wrong. Please try again');</script>";
		}

	}

}

?>

<body>
	<!-- <div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo"><img src="../vendors/images/deskapp-logo-svg.png" alt=""></div>
			<div class='loader-progress' id="progress_div">
				<div class='bar' id='bar1'></div>
			</div>
			<div class='percent' id='percent1'>0%</div>
			<div class="loading-text">
				Loading...
			</div>
		</div>
	</div> -->

	<?php include('includes/navbar.php') ?>

	<?php include('includes/right_sidebar.php') ?>

	<?php include('includes/left_sidebar.php') ?>

	<div class="mobile-menu-overlay"></div>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pb-20">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Outstation Application</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
									<li class="breadcrumb-item active" aria-current="page">Apply for Leave</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>

				<div style="margin-left: 50px; margin-right: 50px;" class="pd-20 card-box mb-30">
					<div class="clearfix">
						<div class="pull-left">
							<h4 class="text-blue h4">Staff Form</h4>
							<p class="mb-20"></p>
						</div>
					</div>
					<div class="wizard-content">
						<form method="post" action="">
							<section>

								<?php if ($role_id = 'Staff'): ?>
									<?php $query = mysqli_query($conn, "select * from tblemployees where emp_id = '$session_id'") or die(mysqli_error());
									$row = mysqli_fetch_array($query);
									?>

									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label>First Name </label>
												<input name="firstname" type="text" class="form-control wizard-required"
													required="true" readonly autocomplete="off"
													value="<?php echo $row['FirstName']; ?>">
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label>Last Name </label>
												<input name="lastname" type="text" class="form-control" readonly
													required="true" autocomplete="off"
													value="<?php echo $row['LastName']; ?>">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label>Email Address</label>
												<input name="email" type="text" class="form-control" required="true"
													autocomplete="off" readonly value="<?php echo $row['EmailId']; ?>">
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
											<label>Location : </label>
											<input name="outstation_location" type="text" class="form-control" required="true" autocomplete="off" >
										<?php endif ?>
									</div>
									<div class="row">
									<label for="outstation_transport">Select Transport:</label>
												<select name="outstation_transport" id="outstation_transport">
													<option value="own">Own Transport</option>
													<option value="department">Department Transport</option>
												</select>
												<br>

												<!-- Additional fields for "department" transport -->
												<div id="department_fields" style="display:none;">
													<label for="driver_name">Driver Name:</label>
													<input type="text" name="driver_name" id="driver_name">
													<br>

													<label for="vehicle_plate_number">Vehicle Plate Number:</label>
													<input type="text" name="vehicle_plate_number"
														id="vehicle_plate_number">
													<br>
												</div>
											</div>
										</div>
								<div class="row">
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label>Start Leave Date :</label>
											<input name="date_from" type="text" class="form-control date-picker"
												required="true" autocomplete="off">
										</div>
									</div>
									<div class="col-md-6 col-sm-12">
										<div class="form-group">
											<label>End Leave Date :</label>
											<input name="date_to" type="text" class="form-control date-picker"
												required="true" autocomplete="off">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8 col-sm-12">
										<div class="form-group">
											<label>Reason :</label>
											<textarea id="textarea1" name="description" class="form-control" required
												length="150" maxlength="150" required="true"
												autocomplete="off"></textarea>
										</div>
									</div>
									<div class="col-md-4 col-sm-12">
										<div class="form-group">
											<label style="font-size:16px;"><b></b></label>
											<div class="modal-footer justify-content-center">
												<button class="btn btn-primary" name="apply" id="apply"
													data-toggle="modal">Apply&nbsp;Leave</button>
											</div>
										</div>
									</div>
								</div>
							</section>
						</form>
					</div>
				</div>

				<script>
					// JavaScript to show/hide additional fields based on selected option
					document.getElementById('outstation_transport').addEventListener('change', function () {
						var departmentFields = document.getElementById('department_fields');
						departmentFields.style.display = (this.value === 'department') ? 'block' : 'none';
					});
				</script>


			</div>
			<?php include('includes/footer.php'); ?>
		</div>
	</div>
	<!-- js -->
	<?php include('includes/scripts.php') ?>
</body>

</html>