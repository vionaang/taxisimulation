<?php 	 
session_start();
$_SESSION['page'] = 'simulasi';
include 'nav.php';

// function loadSimulasi($conn){
// 	$output = '';
// 	$kueri = "SELECT * FROM _simulation";
// 	$res = mysqli_query($conn,$kueri);
// 	$isFirst = true;
// 	while($row = mysqli_fetch_assoc($res)){
// 		if($isFirst){
// 			$isFirst = false;
// 			$output .= '<option value='.$row['id'].' selected>'.$row['id'].' - '.$row['simulation_name'].'</option>';
// 		}
// 		else
// 			$output .= '<option value='.$row['id'].'>'.$row['id'].' - '.$row['simulation_name'].'</option>';
// 	}
// 	return $output;
// } 	

?>
<div style="margin: 5%" id="history">
	<h1 style="text-align:center;">History Simulasi</h1><br>
	<center><button class="btn btn-warning"><a href="simulasi.php" style="color: black">Back</a></button></center>
	<div class="content">
		<!-- <div class="header-patient">
			<form method="POST">
				<div class="wrapper">
					<button class="btn btn-primary" style="margin-right: 5%; width: 100%"><a href="add_driver.php">Add Driver</a></button>
					<input class="form-control searchbar"  type="text" name="search" id="search" onkeyup="cari()" placeholder="Search driver...">
					<i class="fas fa-search search_icon" style="position: absolute;"></i>
				</div>
			</form>
		</div> -->
		<!-- <br><br> -->

		<!-- <div class="row">
			<div class="col-md-6 offset-md-3">
				<center><label>Pilih Simulasi</label></center>
				<select class="form-control" id="simulasi" name="simulasi" onchange="showdata()">	
					<option value="" hidden>Pilih Simulasi</option>
				</select><br>
			</div>
		</div> -->
		<div class="row">
			<div class="col-md-8">
				<p style="font-weight: bold;">Assignment</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="10%">Batch</th>
								<th scope="col" width="10%">ID Driver</th>
								<th scope="col" width="10%">ID Pass</th>
								<th scope="col" width="10%">Pickup Duration</th>
								<th scope="col" width="10%">Pickup Distance</th>
								<th scope="col" width="10%">Trip Duration</th>
								<th scope="col" width="10%">Trip Distance</th>
								<th scope="col" width="10%">Price</th>
								<th scope="col" width="10%">Status</th>
							</tr>
						</thead>
						<tbody id="showdata3">
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-2">
				<p style="font-weight: bold;">Generate Driver</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="40%">Batch</th>
								<th scope="col" width="40%">Driver</th>
							</tr>
						</thead>
						<tbody id="showdata">
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-2">
				<p style="font-weight: bold;">Generate Passenger</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="40%">Batch</th>
								<th scope="col" width="40%">Pass</th>
							</tr>
						</thead>
						<tbody id="showdata2">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	 
include 'footer.php';
?>
<script type="text/javascript">
	$( document ).ready(function() {
		var id =<?=$_GET['id'];?>;
		showGenDriver(id);
		showGenPassenger(id);
		showAssignment(id);
	});

	function showGenDriver(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id,
				getGenDriver : 1,
			},
			success : function(show) {
				$('#showdata').html(show);
			}
		});
	}
	function showGenPassenger(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id,
				getGenPassenger : 1,
			},
			success : function(show) {
				$('#showdata2').html(show);
			}
		});
	}
	function showAssignment(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id,
				getAssignment : 1,
			},
			success : function(show) {
				$('#showdata3').html(show);
			}
		});
	}
</script>
</body>
</html>