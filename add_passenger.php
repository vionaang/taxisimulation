<?php 	
session_start();
$_SESSION['page'] = 'managepass';
include 'nav.php';
include 'header.php';

if (isset($_GET['stat'])) {
	if ($_GET['stat'] == 1) {
		echo "<script>alert('Data entered successfully');</script>";
	}
}
?>
<div class="container" style="margin-top: 5%">
	<div class="content">
		<div class="form">
			<form action="add.php" method="POST">
				<center><h2>ADD Passenger</h2></center>
				<input type="hidden" value="1" id="addpassenger" name="addpassenger">		
				<hr>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<div class="form-group">
							<label for="nama">Name</label>
							<input type="text" class="form-control" id="nama" name="nama" placeholder="Name" required>
						</div>
					</div>
					<div class="col-sm-12 col-md-4">
						<div class="form-group">
							<label for="gender">RFM Score</label>
							<input type="text" class="form-control" id="rfm_score" name="rfm_score" placeholder="RFM Score" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-8 offset-md-2">
						<label for="nama">Status</label>
						<select class="form-control" id="status" name="status" required>
							<option value="" hidden>Choose Status</option>
							<option value="1">Online</option>
							<option value="0">Offline</option>
						</select>
					</div>
				</div>
				<br>
				<div class="row justify-content-center">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>				
			</form>
		</div>
	</div>
</div>

<?php 	 
	include 'footer.php';
?>