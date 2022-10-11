<?php 	
session_start();
$_SESSION['page'] = 'managearea';
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
				<center><h2>Add area</h2></center>
				<input type="hidden" value="1" id="addarea" name="addarea">		
				<hr>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<div class="form-group">
							<label for="up_lat">Upper Latitude</label>
							<input type="text" class="form-control" id="upper_lat" name="upper_lat" placeholder="Upper Latitude"required>
						</div>
					</div>
					<div class="col-sm-12 col-md-4">
						<div class="form-group">
							<label for="up_long">Upper Longitude</label>
							<input type="text" class="form-control" id="upper_long" name="upper_long" placeholder="Upper Longitude" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="bot_lat">Bottom Latitude</label>
						<input type="text" class="form-control" id="bottom_lat" name="bottom_lat" placeholder="Bottom Latitude" required>
					</div>
					<div class="col-sm-12 col-md-4">
						<div class="form-group">
							<label for="gender">Bottom Longitude</label>
							<input type="text" class="form-control" id="bottom_long" name="bottom_long" placeholder="Bottom Longitude" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-8 offset-md-2">
						<label for="nama">Type</label>
						<select class="form-control" id="type" name="type" required>
							<option value="" hidden>Choose Type</option>
							<option value="1">Pusat Kota</option>
							<option value="2">Pinggir Kota</option>
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