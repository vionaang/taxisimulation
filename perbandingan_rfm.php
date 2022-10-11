<?php 	 
session_start();
$_SESSION['page'] = 'newsimulation';
include 'header.php';
include 'nav.php';
include 'connect.php';

$sql = mysqli_query($conn, "SELECT * FROM _simulation");
?>

<script language="Javascript">
function selectRFM(source) {
	if(document.getElementById('check_rfm').checked){
		document.getElementById('rfm_filter1').style.display = "block";
		document.getElementById('rfm_filter2').style.display = "block";
		document.getElementById('rfm_filter3').style.display = "block";

	}
	else{
		document.getElementById('rfm_filter1').style.display = "none";
		document.getElementById('rfm_filter2').style.display = "none";
		document.getElementById('rfm_filter3').style.display = "none";

	}
}
</script>

<div class="container" style="margin-top: 5%">
	<div class="content">
		<div class="form">
			<form action="add.php" method="POST">
				<center><h2>Comparing RFM Simulation</h2></center>
				<input type="hidden" value="1" id="addRFMcomparison" name="addRFMcomparison">		
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
				<div class="row">
					<div class="col-sm-12 col-md-8 offset-md-2">
						<div class="form-group">
							<input class="form-input" onClick="selectRFM(this)" type="checkbox" id="check_rfm" name="check_rfm" value="check_rfm">
								<label class="form-check-label" for="flexCheckDefault">
									Apakah R-Multi Layer-FM?
								</label><br>
							<input type="text" class="form-control" style="display: none; margin-top: 10px;" id="rfm_filter1" name="rfm_filter1" placeholder="Hari paling besar">
							<input type="text" class="form-control" style="display: none; margin-top: 10px;" id="rfm_filter2" name="rfm_filter2" placeholder="Hari tengah">
							<input type="text" class="form-control" style="display: none; margin-top: 10px;" id="rfm_filter3" name="rfm_filter3" placeholder="Hari paling kecil">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12 col-md-3 offset-md-2">
						<div class="form-group">
							<label for="Recency categories">Pengkategorian Recency (Day)</label>
							<input type="text" class="form-control" id="recency5" name="recency5" placeholder="Kategori 5 (hari)"><br>
							<input type="text" class="form-control" id="recency4" name="recency4" placeholder="Kategori 4 (hari)"><br>
							<input type="text" class="form-control" id="recency3" name="recency3" placeholder="Kategori 3 (hari)"><br>
							<input type="text" class="form-control" id="recency2" name="recency2" placeholder="Kategori 2 (hari)"><br>
							<input type="text" class="form-control" id="recency1" name="recency1" placeholder="Kategori 1 (hari)">
						</div>
					</div>
				</div>
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
						loadRFMFaktor: 1,
						id_simulation : card
					},
					success : function(result) {
						$('#showFaktor').html(result);
					}
				});
			}
		}
	</script>