<?php 	 
session_start();
$_SESSION['page'] = 'newsimulation2';
include 'header.php';
include 'nav.php';
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

	function selectFaktor(source) {
		checkboxes = document.getElementsByName('checkbox[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}

		if(source.checked==true){
			document.getElementById('dur_prec').style.display = "block";
			document.getElementById('dis_prec').style.display = "block";
			document.getElementById('rat_prec').style.display = "block";
			document.getElementById('rfmd_prec').style.display = "block";
			document.getElementById('ttrip_prec').style.display = "block";
			document.getElementById('cancel_prec').style.display = "block";
			document.getElementById('tdist_prec').style.display = "block";
			document.getElementById('rfmp_prec').style.display = "block";
		}
		else{
			document.getElementById('dur_prec').style.display = "none";
			document.getElementById('dis_prec').style.display = "none";
			document.getElementById('rat_prec').style.display = "none";
			document.getElementById('rfmd_prec').style.display = "none";
			document.getElementById('ttrip_prec').style.display = "none";
			document.getElementById('cancel_prec').style.display = "none";
			document.getElementById('tdist_prec').style.display = "none";
			document.getElementById('rfmp_prec').style.display = "none";
		}
	}
	function selectArea(source) {
		checkboxes = document.getElementsByName('checkarea[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}

	function check(source) {
		if(source.checked==true){
			if(source.value == "duration"){
				precentage = document.getElementById('dur_prec')
			}
			else if(source.value == "distance"){
				precentage = document.getElementById('dis_prec')

			}
			else if(source.value == "rating"){
				precentage = document.getElementById('rat_prec')

			}
			else if(source.value == "rfm_driver"){
				precentage = document.getElementById('rfmd_prec')

			}
			else if(source.value == "total_trip"){
				precentage = document.getElementById('ttrip_prec')

			}
			else if(source.value == "cancellation_rate"){
				precentage = document.getElementById('cancel_prec')

			}
			else if(source.value == "total_distance"){
				precentage = document.getElementById('tdist_prec')

			}
			else if(source.value == "rfm_pass"){
				precentage = document.getElementById('rfmp_prec')

			}
			precentage.style.display = "block";
		}
		else{
			if(source.value == "duration"){
				precentage = document.getElementById('dur_prec')
			}
			else if(source.value == "distance"){
				precentage = document.getElementById('dis_prec')

			}
			else if(source.value == "rating"){
				precentage = document.getElementById('rat_prec')

			}
			else if(source.value == "rfm_driver"){
				precentage = document.getElementById('rfmd_prec')

			}
			else if(source.value == "total_trip"){
				precentage = document.getElementById('ttrip_prec')

			}
			else if(source.value == "cancellation_rate"){
				precentage = document.getElementById('cancel_prec')

			}
			else if(source.value == "total_distance"){
				precentage = document.getElementById('tdist_prec')

			}
			else if(source.value == "rfm_pass"){
				precentage = document.getElementById('rfmp_prec')

			}
			precentage.style.display = "none";

		}
	}
</script>

<div class="container" style="margin-top: 5%">
	<div class="content">
		<div class="form">
			<form action="add.php" method="POST">
				<center><h2>Create RFM Simulation</h2></center>
				<input type="hidden" value="1" id="addsimulation" name="addsimulation">	
				<div class="text-center">
					<button class="btn btn-warning"><a style="color: black" href="form.php">Normal Simulation</a></button>
				</div>	
				<hr>
				<div class="row">
					<div class="col-sm-12 col-md-4 offset-md-2">
						<div class="form-group">
							<label for="nama">Simulation Name</label>
							<input type="text" class="form-control" id="nama" name="nama" placeholder="Name" required>
						</div>
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">Method</label>
						<select class="form-control" id="method" name="method" required>
							<option value="" hidden>Choose Method</option>
							<option value="Hungarian Programming">Hungarian Programming</option>
							<option value="Goal Programming">Goal Programming</option>
							<option value="Random Assignment">Random Assignment</option>
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
						<input type="time" value="<?= date('H:i'); ?>" class="form-control" name="starttime" id="starttime" required="">
					</div>
					<div class="col-sm-12 col-md-4">
						<label for="nama">End Time</label>
						<input type="time" value="<?= date('H:i'); ?>" class="form-control" name="endtime" id="endtime" required="">
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
				<div class="row">
					<div class="col-sm-12 col-md-8 offset-md-2">
						<div class="form-group">
							<label for="Faktor">Faktor yang digunakan</label>
							<!-- <ul id="sortable" class="list-group"> -->
								<!-- </ul> -->
								<div class="form-check">
									<div class="row">
										<div class="col-md-6">
											<input class="form-check-input" onClick="selectFaktor(this)" type="checkbox" name="all" value="all">
											<label class="form-check-label" for="flexCheckDefault">
												All
											</label><br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="duration">
											<label class="form-check-label " for="flexCheckDefault">
												Duration
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="dur_prec" name="dur_prec" placeholder="%"><br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="distance">
											<label class="form-check-label" for="flexCheckDefault">
												Distance
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="dis_prec" name="dis_prec" placeholder="%">
											<br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="rating">
											<label class="form-check-label" for="flexCheckDefault">
												Rating
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="rat_prec" name="rat_prec" placeholder="%">
											<br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="rfm_driver">
											<label class="form-check-label" for="flexCheckDefault">
												RFM Score Driver
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="rfmd_prec" name="rfmd_prec" placeholder="%">
										</div>
										<div class="col-md-6">
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="total_trip" id="total_trip">
											<label class="form-check-label" for="flexCheckDefault">
												Total Trip
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="ttrip_prec" name="ttrip_prec" placeholder="%">
											<br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="cancellation_rate" id="cancellation_rate">
											<label class="form-check-label" for="flexCheckDefault">
												Cancellation Rate
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="cancel_prec" name="cancel_prec" placeholder="%">
											<br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="total_distance" id="total_distance">
											<label class="form-check-label" for="flexCheckDefault">
												Total Distance Driver
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="tdist_prec" name="tdist_prec" placeholder="%">
											<br>
											<input class="form-check-input" onClick="check(this)" type="checkbox" name="checkbox[]" value="rfm_pass" id="rfm_pass">
											<label class="form-check-label" for="flexCheckDefault">
												RFM Score Passenger
											</label>
											<input type="number" min ="0" max="100" style="display: none; width: 70%" class="form-check-label form-control" id="rfmp_prec" name="rfmp_prec" placeholder="%">

										</div>
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
									<input class="form-check-input" onClick="selectArea(this)" type="checkbox" name="all" value="all">
									<label class="form-check-label" for="flexCheckDefault">
										All
									</label><br>
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
			getArea();
			getFaktor();
		});
		$("#sortable").sortable();

		function getArea(){
			$.ajax({
				url     : "query.php",
				type    : "POST",
				async   : false,
				data    : {
					loadCheckboxArea: 1
				},
				success : function(result) {
					$('#showarea').html(result);
				}
			});
		}
		function getFaktor(){
			$.ajax({
				url     : "query.php",
				type    : "POST",
				async   : false,
				data    : {
					loadFaktor: 1
				},
				success : function(result) {
					$('#sortable').html(result);
				}
			});
		}
	</script>