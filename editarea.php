<?php
require 'connect.php';
// include 'header.php';

$id = $_POST['id'];

$query = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM _area WHERE id = ".$id));
?>
<center><h2>EDIT AREA</h2></center>
<input type="hidden" value="1" id="editarea" name="editarea">
<input type="hidden" value="<?= $id; ?>" id="id" name="id">				
<hr>
<div class="row">
	<div class="col-sm-12 col-md-4 offset-md-2">
		<div class="form-group">
			<label for="up_lat">Upper Latitude</label>
			<input type="text" class="form-control" id="upper_lat" name="upper_lat" placeholder="Upper Latitude" value="<?= $query['upper_lat'];?>" required>
		</div>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="up_long">Upper Longitude</label>
			<input type="text" class="form-control" id="upper_long" name="upper_long" placeholder="Upper Longitude" value="<?= $query['upper_long'];?>" required>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-4 offset-md-2">
		<label for="bot_lat">Bottom Latitude</label>
		<input type="text" class="form-control" id="bottom_lat" name="bottom_lat" placeholder="Bottom Latitude" value="<?= $query['bottom_lat'];?>" required>
	</div>
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
			<label for="gender">Bottom Longitude</label>
			<input type="text" class="form-control" id="bottom_long" name="bottom_long" placeholder="Bottom Longitude" value="<?= $query['bottom_long'];?>" required>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-8 offset-md-2">
		<label for="nama">Type</label>
		<select class="form-control" id="type" name="type" required>
			<option value="" hidden>Choose Type</option>
			<?php
			if($query['type'] == 2){
				echo '<option value="1">Pusat Kota</option>';
				echo '<option value="2" selected>Pinggir Kota</option>';
			}
			else{
				echo '<option value="1" selected>Pusat Kota</option>';
				echo '<option value="2">Pinggir Kota</option>';
			}
			?>
		</select>
		</div>
	</div>

<div class="row justify-content-center">
	<button type="submit" class="btn btn-primary" onclick="saveedit()">Save changes</button>
</div>				
<!-- </form> -->