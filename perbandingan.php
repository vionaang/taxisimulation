<?php 	 
session_start();
$_SESSION['page'] = 'perbandingan';
include 'header.php';
include 'nav.php';
include 'connect.php';

$sql = mysqli_query($conn, "SELECT * FROM _simulation");

?>

<script language="Javascript">
function selectRFM(source) {
	if(document.getElementById('check_rfm').checked){
		document.getElementById('rfm_filter').style.display = "block";
	}
	else{
		document.getElementById('rfm_filter').style.display = "none";
	}
}
</script>

<div class="container" style="margin-top: 5%">
	<div class="content">
		<div class="form">
			<form action="add.php" method="POST">
				<center><h2>Comparing Method Simulation</h2></center>
				<input type="hidden" value="1" id="addComparison" name="addComparison">		
				<hr>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<div class="form-group">
							<label for="nama">Simulation Name</label>	
							<input type="text" class="form-control" id="nama" name="nama" placeholder="Name" required>
						</div>
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">Comparison</label>
						<select class="form-control" onchange="Compare()"id="comparison" name="comparison" required>
							<option value="" hidden>Choose Comparison</option>
							<?php 
								while ($row = $sql->fetch_assoc()){
								echo "<option value=". $row['id'] .">" . $row['id'] ." - " . $row['simulation_name'] . "</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="nama">Method</label>
						<select class="form-control" id="method" name="method" required>
							<option value="" hidden>Choose Method</option>
							<option value="Hungarian Programming">Hungarian Programming</option>
							<option value="Goal Programming">Goal Programming</option>
							<option value="Random Assignment">Random Assignment</option>
						</select>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="nama">Start Date</label>
						<input type="date" class="form-control" name="startdate" id="startdate" required="">
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">End Date</label>
						<input type="date" class="form-control" name="enddate" id="enddate" required="">
					</div>
				</div><br>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<label for="nama">Start Time</label>
						<input type="time" class="form-control" name="starttime" id="starttime" required="">
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">End Time</label>
						<input type="time" class="form-control" name="endtime" id="endtime" required="">
					</div>
				</div><br>
				
				<div class="col-sm-12 col-md-8 offset-md-2">
						<div class="form-group">
							<label for="Faktor">Faktor yang digunakan</label>
							<!-- <ul id="sortable" class="list-group"> -->
								<!-- </ul> -->
								<div class="form-check">
									<div class="row">
										<div class="col-md-6">
											<div id="showFaktor"></div>
										</div>
									</div>
								</div>
						</div>
				</div>		
					<div class="row">
						<div class="col-sm-12 col-md-8 offset-md-2">
							<div class="form-group">
								<label for="Faktor">Area yang digunakan</label>
								<div class="form-check">
									<span id="showarea"></span>
								</div>
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
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			document.getElementById('startdate').valueAsDate = new Date();
			document.getElementById('enddate').valueAsDate = new Date();
		});
		function Compare(){
			getArea();
			getFaktor();
		}

		function getArea(){
		var card = document.getElementById("comparison").value;
			if (card.value != "") {
				$.ajax({
					url     : "query.php",
					type    : "POST",
					async   : false,
					data    : {
						loadRFMCheckboxArea: 1,
						id_simulation : card
					},
					success : function(result) {
						$('#showarea').html(result);
					}
				});
			}
		}
		function getFaktor(){
		var card = document.getElementById("comparison").value;
			if (card.value != "") {
				$.ajax({
					url     : "query.php",
					type    : "POST",
					async   : false,
					data    : {
						loadComparisonFaktor: 1,
						id_simulation : card
					},
					success : function(result) {
						$('#showFaktor').html(result);
					}
				});
			}
		}
	</script>