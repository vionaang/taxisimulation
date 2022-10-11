<?php 	
session_start();
$_SESSION['page'] = 'managedriver';
include 'nav.php';
include 'header.php';

if (isset($_GET['stat'])) {
	if ($_GET['stat'] == 1) {
		echo "<script>alert('Data entered successfully');</script>";
	}
	else if ($_GET['stat'] == 2) {
		echo "<script>alert('Sorry, there was an error uploading your picture.');</script>";
	}
	else if ($_GET['stat'] == 4) {
		echo "<script>alert('File has not been uploaded');</script>";
	}
}
?>
<div class="container" style="margin-top: 5%">
	<div class="content">
		<div class="form">
			<form action="add.php" method="POST">
				<center><h2>ADD DRIVER</h2></center>
				<input type="hidden" value="1" id="adddriver" name="adddriver">		
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
							<label for="nama">Rating</label>
							<input type="text" class="form-control" id="rating" name="rating" placeholder="Rating" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="nama">Total Trip</label>
						<input type="text" class="form-control" id="total_trip" name="total_trip" placeholder="Total Trip" required>
					</div>
					<div class="col-sm-12 col-md-4">
						<div class="form-group">
							<label for="gender">Total Distance</label>
							<input type="text" class="form-control" id="total_distance" name="total_distance" placeholder="Total Distance" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="nama">Cancellation Rate</label>
						<input type="text" class="form-control" id="cancel_rate" name="cancel_rate" placeholder="Cancellation Rate" required>
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">Status</label>
						<select class="form-control" id="status" name="status" required>
							<option value="" hidden>Choose Status</option>
							<option value="1">Online</option>
							<option value="0">Offline</option>
						</select>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-12 col-md-8 offset-md-2">
						<div class="form-group">
							<label for="gender">RFM Score</label>
							<input type="text" class="form-control" id="rfm_score" name="rfm_score" placeholder="RFM Score" required>
						</div>
					</div>
				</div>
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