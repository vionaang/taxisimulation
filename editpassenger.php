<?php
require 'connect.php';
// include 'header.php';

$id = $_POST['id'];
$query = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM _passenger WHERE id_passenger = ".$id));
?>
<center><h2>EDIT PASSENGER</h2></center>
<input type="hidden" value="1" id="editpassenger" name="editpassenger">
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
			<label for="gender">RFM Score</label>
			<input type="text" class="form-control" id="rfm_score" name="rfm_score" placeholder="RFM Score" value="<?= $query['rfm_score'];?>" required>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-md-8 offset-md-2">
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
<br>
<div class="row justify-content-center">
	<button type="submit" class="btn btn-primary" onclick="saveedit()">Save changes</button>
</div>				
<!-- </form> -->