<?php
require 'connect.php';
// include 'header.php';

$id = $_POST['id'];

$query = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM _driver WHERE id_driver = ".$id));
?>
<center><h2>EDIT DRIVER</h2></center>
<input type="hidden" value="1" id="editdriver" name="editdriver">
<input type="hidden" value="<?= $id; ?>" id="id" name="id">				
<hr>
<div class="row">
	<div class="col-sm-12 col-md-4 offset-md-2">
		<div class="form-group">
			<label for="nama">Name</label>
			<input type="text" class="form-control" id="nama" name="nama" placeholder="Name" value="<?= $query['name'];?>" required>
		</div>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="nama">Rating</label>
			<input type="text" class="form-control" id="rating" name="rating" placeholder="Rating" value="<?= $query['rating'];?>" required>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-4 offset-md-2">
		<label for="nama">Total Trip</label>
		<input type="text" class="form-control" id="total_trip" name="total_trip" placeholder="Total Trip" value="<?= $query['total_trip'];?>" required>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="gender">Total Distance</label>
			<input type="text" class="form-control" id="total_distance" name="total_distance" placeholder="Total Distance" value="<?= $query['total_distance'];?>" required>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-4 offset-md-2">
		<label for="nama">Cancellation Rate</label>
		<input type="text" class="form-control" id="cancel_rate" name="cancel_rate" placeholder="Cancellation Rate" value="<?= $query['cancellation_rate'];?>" required>
	</div>
	<div class="col-sm-12 col-md-4">
		<label for="nama">Status</label>
		<select class="form-control" id="status" name="status" required>
			<option value="" hidden>Choose Status</option>
			<?php
			if($query['status'] == 0){
				echo '<option value="1">Online</option>';
				echo '<option value="0" selected>Offline</option>';
			}
			else{
				echo '<option value="1" selected>Online</option>';
				echo '<option value="0">Offline</option>';
			}
			?>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-8 offset-md-2">
		<div class="form-group">
			<label for="gender">RFM Score</label>
			<input type="text" class="form-control" id="rfm_score" name="rfm_score" placeholder="RFM Score" value="<?= $query['rfm_score'];?>" required>
		</div>
	</div>
</div>
<div class="row justify-content-center">
	<button type="submit" class="btn btn-primary" onclick="saveedit()">Save changes</button>
</div>				
<!-- </form> -->