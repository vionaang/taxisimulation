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
$id_simulation = $_GET['id'];
$id_main_simulation = $_GET['id_main'];
if($id_main_simulation == 0)$id_main_simulation = $id_simulation;
$id_hungarian = 0;
$id_random = 0;
$id_goal = 0;
$id_rfm = 0;
$id_rfm_comparing = 0;

$main_method = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM _simulation WHERE id= ".$id_simulation));
$factor_used = mysqli_query($conn, "SELECT f.name, fu.precentage FROM _factor_used fu JOIN _factor f ON fu.id_factor = f.id WHERE fu.id_simulation = ".$id_simulation);
$query_batch = mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = ".$id_simulation);
$jumlah_batch = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _batch WHERE id_simulation = ".$id_main_simulation));
$query_1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE method = 'Hungarian Programming' AND (id = $id_simulation OR id_main_simulation = ".$id_simulation.")"));
$query_2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE method = 'Random Assignment' AND (id = $id_simulation OR id_main_simulation = ".$id_simulation.")"));
$query_3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE method = 'Goal Programming' AND (id = $id_simulation OR id_main_simulation = ".$id_simulation.")"));
$query_4 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE method = 'Modified RFM Comparison' AND (id = $id_simulation OR id_main_simulation = ".$id_simulation.")"));
$query_5 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE method = 'RFM Comparison' AND (id = $id_simulation OR id_main_simulation = ".$id_simulation.")"));

$jumlah_method = 0;
if($query_1['id']!=null) {$id_hungarian = $query_1['id']; $jumlah_method++;}
if($query_2['id']!=null) {$id_random = $query_2['id']; $jumlah_method++;}
if($query_3['id']!=null) {$id_goal = $query_3['id']; $jumlah_method++;}
if($query_4['id']!=null) {$id_rfm = $query_4['id']; $jumlah_method++;}
if($query_5['id']!=null) {$id_rfm_comparing = $query_5['id']; $jumlah_method++;}
// // 
// $jumlah_simulasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) FROM _simulation WHERE id_main_simulation =".$id_simulation." or id=".$id_simulation));

?>
<style type="text/css">
	.card{
		background-color: #1b1b72;
		border-radius: 25px;
	}
</style>
<div style="margin: 5%" id="history">
	<input type="hidden" id="id_main" value="<?=$id_main_simulation;?>">
	<input type="hidden" id="jumlah_method" value="<?=$jumlah_method;?>">
	<input type="hidden" id="id_hungarian" value="<?=$id_hungarian;?>">
	<input type="hidden" id="id_random" value="<?=$id_random;?>">
	<input type="hidden" id="id_goal" value="<?=$id_goal;?>">
	<input type="hidden" id="id_rfm" value="<?=$id_rfm;?>">
	<input type="hidden" id="id_rfm_comparing" value="<?=$id_rfm_comparing;?>">

	<input type="hidden" id="jumlah_batch" value="<?=$jumlah_batch['x'];?>">
	<button class="btn btn-warning" onclick="location.href='simulasi.php'"><i class="fas fa-arrow-left"></i></button>	

	<div class="content">
		<br>	
		<h1 style="text-align:center;">History <?=$main_method['simulation_name'];?></h1><br>
		<p> <b>Factor Used</b><br>
			<?php
			while ($row = mysqli_fetch_assoc($factor_used)) {
				echo $row['name'].' '.$row['precentage'].'%<br>';
			}
			?>
		</p>
		<div class="row">
			<div class="col-md-6">
				<p style="font-weight: bold;">Assignment <?=$main_method['method'];?></p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<!-- <th scope="col" width="10%">Batch</th> -->
								<th scope="col" width="10%">ID Driver</th>
								<th scope="col" width="10%">ID Pass</th>
								<th scope="col" width="10%">Pickup Duration</th>
								<th scope="col" width="10%">Pickup Distance</th>
								<th scope="col" width="10%">Rating</th>
								<th scope="col" width="10%">RFM Driver</th>
								<th scope="col" width="10%">RFM Passenger</th>

								<!-- <th scope="col" width="10%">Trip Duration</th> -->
								<!-- <th scope="col" width="10%">Trip Distance</th> -->
								<?php
									if($main_method['method']!='Goal Programming' && $main_method['method']!='Random Assignment' && $main_method['method']!= 'Hungarian Programming'){
										echo '
											<th scope="col" width="10%">RFM Driver</th>
											<th scope="col" width="10%">RFM Passenger</th>
										';
									}
								?>
								<th scope="col" width="10%">Price</th>
								<!-- <th scope="col" width="10%">Status</th> -->
							</tr>
						</thead>
						<tbody id="showdata3">
						</tbody>
					</table>
				</div>
			</div>
			<?php

			$other_simul = mysqli_query($conn,"SELECT * FROM _simulation WHERE id_main_simulation = ".$_GET['id']);

			while($row = mysqli_fetch_assoc($other_simul)){
				$x = $row['id'];
				$kueri=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver LEFT JOIN _driver d ON gd.id_driver = d.id_driver JOIN _batch b ON a.id_batch = b.id_batch  WHERE a.id_simulation = $x");

				echo '

				<div class="col-md-6">
				<p style="font-weight: bold;">Assignment '.$row['method'].'</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
				<table class="table table-hover table-light">
				<thead>
				<tr>
				<th scope="col" width="5%">#</th>
				<!-- <th scope="col" width="10%">Batch</th> -->
				<th scope="col" width="10%">ID Driver</th>
				<th scope="col" width="10%">ID Pass</th>
				<th scope="col" width="10%">Pickup Duration</th>
				<th scope="col" width="10%">Pickup Distance</th>
				<th scope="col" width="10%">Rating</th>
				';
				
				if($row['method']!='Goal Programming' && $row['method']!='Random Assignment' && $row['method']!= 'Hungarian Programming'){
					echo '
						<th scope="col" width="10%">RFM Driver</th>
						<th scope="col" width="10%">RFM Passenger</th>
					';
				}				

				echo '<th scope="col" width="10%">Price</th>
				</tr>
				</thead>
				<tbody>';
				$i=1;
				$batch = 0;
				while ($row2 = mysqli_fetch_assoc($kueri)) {
					if($batch != $row2['batch_num']){
						$i=1;
						$batch = $row2['batch_num'];
						echo '<tr style="background-color: lightgrey"><td colspan=11>Batch '.$batch.'</td></tr>';
					}
					$row3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_driver as x FROM _driver WHERE id_driver = ".$row2['id_driver']));
					$row4 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_pass as x FROM _passenger WHERE id_passenger = ".$row2['id_passenger']));
					echo 
					'<tr>
					<td>'.$i++.'</td>
					<td>D'.$row2['id_driver'].'</td>
					<td>P'.$row2['id_passenger'].'</td>
					<td>'.$row2['pickup_duration'].'</td>
					<td>'.$row2['pickup_distance'].'</td>
					<td>'.$row2['rating'].'</td>';
					if($row['method']!='Goal Programming' && $row['method']!='Random Assignment' && $row['method']!= 'Hungarian Programming'){
						echo '<td>'.$row3['x'].'</td>
							<td>'.$row4['x'].'</td>';
					}
					echo '<td>'.$row2['price'].'</td>
					</tr>';
				}
				echo '</tbody>
				</table>
				</div>
				</div>

				';
			}
			?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<p style="font-weight: bold;">Time Comparison</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th rowspan="2" width="5%" style="vertical-align: middle; ">Batch</th>
								<?php
								$comparison = mysqli_query($conn,"SELECT method FROM _simulation WHERE id_main_simulation = $id_simulation OR id = $id_simulation ORDER BY id");
								while($row = mysqli_fetch_assoc($comparison)){
									echo '<th colspan = "2"><center>'.$row['method'].'</center></th>';
								}
								?>
							</tr>
							<tr>
								<?php
								for ($i=0; $i < $jumlah_method ; $i++) { 
									echo '
									<th scope="col" width="20%"><center>Assign Time (s)</center></th>
									<th scope="col" width="20%"><center>Avg Waiting Time Passenger(s)</center></th>
									';
								}
								?>
							</tr>
						</thead>
						<tbody id="showdatabatch">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<p style="font-weight: bold;">Tabel Generate per Batch</p>
		<div class="row">
			<?php
			while ($row = mysqli_fetch_assoc($query_batch)) {
				$gen_drivers = mysqli_query($conn,"SELECT * FROM _generate_drivers WHERE id_simulation = $id_simulation AND id_batch <= ".$row['id_batch']." AND id NOT IN (SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
				$gen_pass = mysqli_query($conn,"SELECT * FROM _generate_passengers WHERE id_simulation = $id_simulation AND id_batch <= ".$row['id_batch']."  AND id NOT IN (SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
				echo '
				<div class="col-md-2">
				<p style="font-weight: bold;">Batch '.$row['batch_num'].'</p>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
				<table class="table table-hover table-light">
				<thead>
				<tr>
				<!-- <th scope="col" width="5%">#</th> -->
				<th scope="col" width="20%">Drivers</th>
				<th scope="col" width="20%">Passengers</th>
				</tr>
				</thead>
				<tbody><td>';
				while ($row2 = mysqli_fetch_assoc($gen_drivers)) {
					echo 'D'.$row2['id_driver'].'<br>';
				}
				echo '</td><td>';
				while ($row2 = mysqli_fetch_assoc($gen_pass)) {
					echo 'P'.$row2['id_passenger'].'<br>';
				}
				echo'</td>
				</tbody>
				</table>
				</div>
				</div>
				';
			}
			?>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-white mb-3">
					<div class="card-title" style="padding: 20px">
						<span style="font-family: 'PoppinsMedium';"><center>Total Assign Time Berdasarkan Batch</center></span><br>
					</div>
					<div class="card-body">
						<div style="height:55vh; width:53vw;margin: 0px auto;">
							<canvas id="chart4" style="overflow-x: scroll;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-white mb-3">
					<div class="card-title" style="padding: 20px">
						<span style="font-family: 'PoppinsMedium';"><center>Waiting Time Passenger</center></span><br>
					</div>
					<div class="card-body">
						<div style="height:55vh; width:53vw;margin: 0px auto;">
							<canvas id="chart5" style="overflow-x: scroll;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-white mb-3">
					<div class="card-title" style="padding: 20px">
						<span style="font-family: 'PoppinsMedium';"><center>Total Generate Time Berdasarkan Batch</center></span><br>
					</div>
					<div class="card-body">
						<div style="height:55vh; width:53vw;margin: 0px auto;">
							<canvas id="chart3" style="overflow-x: scroll;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-white mb-3">
					<div class="card-title" style="padding: 20px">
						<span style="font-family: 'PoppinsMedium';"><center>Total Driver Berdasarkan Batch</center></span><br>
					</div>
					<div class="card-body">
						<div style="height:55vh; width:53vw;margin: 0px auto;">
							<canvas id="chart1" style="overflow-x: scroll;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-white mb-3">
					<div class="card-title" style="padding: 20px">
						<span style="font-family: 'PoppinsMedium';"><center>Total Passenger Berdasarkan Batch</center></span><br>
					</div>
					<div class="card-body">
						<div style="height:55vh; width:53vw;margin: 0px auto;">
							<canvas id="chart2" style="overflow-x: scroll;"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	 
include 'footer.php';
?>
<script src="Scripts/jquery-1.6.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/0.6.6/chartjs-plugin-zoom.js"></script>
<script type="text/javascript">
	$( document ).ready(function() {
		var id =<?=$_GET['id'];?>;
		var id_main_simulation = $('#id_main').val();
		if(id_main_simulation == 0) id_main_simulation = id;
		var jumlah=document.getElementById("jumlah_method").value;
		showGenDriver(id_main_simulation);
		showGenPassenger(id_main_simulation);
		showAssignment(id);
		showComparison(id);
		showBatch(id);

		showChart(id_main_simulation,"chart1","Jumlah Driver Digenerate");
		showChart(id_main_simulation,"chart2","Jumlah Passenger Digenerate");
		showChart(id_main_simulation,"chart3","Total Generate Time (s)");
		showChart(id,"chart4","Total Assign Time (s)");
		showChart(id,"chart5","Avg Total Time (s)");
	});

	function showGenDriver(id_main_simulation) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id_main_simulation,
				getGenDriver : 1,
			},
			success : function(show) {
				$('#showdata').html(show);
			}
		});
	}
	function showGenPassenger(id_main_simulation) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id_main_simulation,
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
	function showComparison(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id,
				getCompare : 1,
			},
			success : function(show) {
				$('#showass_compare').html(show);
			}
		});
	}
	function showBatch(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id,
				getBatchData : 1,
			},
			success : function(show) {
				$('#showdatabatch').html(show);
			}
		});
	}

	function showChart(id,idChart,y){
		var id1 = $('#id_hungarian').val();
		var id2 = $('#id_random').val();
		var id3 = $('#id_goal').val();
		var id4 = $('#id_rfm').val();
		var id5 = $('#id_rfm_comparing').val();

		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(173, 53, 186, 1)");
		gradientFill.addColorStop(1, "rgba(173, 53, 186, 0.1)");

		var label =[];
		var data = [];
		var label2 =[];
		var data2 = [];
		var label3 =[];
		var data3 = [];
		var method1 = '';
		var method2 = '';
		var method3 = '';

		var jumlah_batch = $('#jumlah_batch').val();
		var jumlah_method = $('#jumlah_method').val();
		var type = 1;

		if(idChart == "chart1") type = 1;
		else if(idChart == "chart2") type = 2;
		else if(idChart == "chart3") type = 3;
		else if(idChart == "chart4") type = 4;
		else if(idChart == "chart5") type = 5;
		
		var id_simulation = 0;
		if (id1!='0') {id_simulation = id1; method1 = 'Hungarian Programming';}
		else if(id2 != '0') {id_simulation = id2; method1 = 'Random Assignment';}
		else if(id3 != '0'){id_simulation = id3; method1 = 'Goal Programming';}
		else if(id4 != '0'){id_simulation = id4; method1 = 'Modified RFM Comparison';}
		else {id_simulation = id5; method1 = 'RFM Comparison';}

		if(idChart == 'chart1' || idChart == 'chart2' || idChart == 'chart3') id_simulation = id;

		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				id : id_simulation,
				graph : type,
			},
			success : function(show) {
				var arrayObjects = JSON.parse(show);
				for (var i = 0; i < arrayObjects.length; i++) {
					label.push("Batch "+(i+1));
					data.push(arrayObjects[i]['x']);
				}

				if(jumlah_method == '1' || jumlah_method == '0' || idChart == 'chart1' || idChart == 'chart2' ||idChart =='chart3') makeChart(label,data,idChart,y);
				else if(jumlah_method != '1') {
					if (id1 != '0' && id_simulation != id1) {id_simulation = id1;method2='Hungarian Programming'}
					else if (id2 != '0' && id_simulation != id2) {id_simulation = id2;method2='Random Assignment'}
					else if (id3 != '0' && id_simulation != id3) {id_simulation = id3;method2='Goal Programming'}
					else if (id4 != '0' && id_simulation != id4) {id_simulation = id4;method2='Modified RFM Comparison';}
					else {id_simulation = id5; method2='RFM Comparison';}

					$.ajax({
						url     : "view.php",
						type    : "POST",
						async   : false,
						data    : {
							id : id_simulation,
							graph : type,
						},
						success : function(show) {
							var arrayObjects = JSON.parse(show);
							for (var i = 0; i < arrayObjects.length; i++) {
								data2.push(arrayObjects[i]['x']);
							}
							if(jumlah_method == '3'){
								if (id1 != '0' && id_simulation != id1 && method1 != 'Hungarian Programming' && method2 != 'Hungarian Programming') {id_simulation = id1;method3='Hungarian Programming'}
								else if (id2 != '0' && id_simulation != id2 && method1 != 'Random Assignment' &&  method2 != 'Random Assignment') {id_simulation = id2;method3='Random Assignment'}
								else if (id3 != '0' && id_simulation != id3 && method1 != 'Goal Programming' &&  method2 != 'Goal Programming') {id_simulation = id3;method3='Goal Programming'}
								else if (id4 != '0' && id_simulation != id4 && method1 != 'Modified RFM Comparison' &&  method2 != 'Modified RFM Comparison') {id_simulation = id4; method3='Modified RFM Comparison';}
								else {id_simulation = id5; method3='RFM Comparison';}

								// alert("method 1: "+method1+", method 2: "+method2+", method 3: "+method3);
								
								$.ajax({
									url     : "view.php",
									type    : "POST",
									async   : false,
									data    : {
										id : id_simulation,
										graph : type,
									},
									success : function(show) {
										var arrayObjects = JSON.parse(show);
										for (var i = 0; i < arrayObjects.length; i++) {
											data3.push(arrayObjects[i]['x']);
										}
										makeChart3(label,data,data2,data3,idChart,y,method1,method2,method3);
									}
								});
							}
							else makeChart2(label,data,data2,idChart,y,method1,method2);
						}
					});					
				} 
			}
		});
	}

	function makeChart(label,data, idChart,y){
		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(255, 167, 0, 1)");
		gradientFill.addColorStop(1, "rgba(255, 167, 0, 0.1)");

		var myChart = new Chart(ctxL, {
			type: 'line',
			data: {
				labels: label,
				datasets: [{
					"lineTension": 0,  
					label: y,
					data: data,
					pointRadius:7,
					pointBackgroundColor: "rgba(255, 167, 0, 1)",
					pointBorder: "rgba(255, 167, 0, 0.1)",
					backgroundColor: gradientFill,
					borderColor: ['rgba(255, 167, 0, 1)'],
					borderWidth: 3
				}]
			},
			maintainAspectRatio: false,
			responsive: true,
			options: {
				tooltips: 
				{
					titleFontSize: 16,
					titleFontStyle: "normal",
					titleFontFamily: "'PoppinsRegular'",
					bodyFontColor: "#000000",
					bodyFontSize: 14,
					bodyFontStyle: "normal",
					bodyFontColor: '#FFFFFF',
					bodyFontFamily: "'PoppinsRegular', 'Arial', sans-serif",
					callbacks: {
						label: function(tooltipItem, data) {
							return 'Jumlah: '+Number(tooltipItem.yLabel);
						}
					}
				},
				legend:{
					labels: {
						fontColor: 'white',
						fontFamily: 'PoppinsRegular'
					}
				},
				scales: {
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: y,
							fontColor:"white",
							fontFamily: "PoppinsRegular",
						},
						ticks: {
							fontColor: "white",
							fontFamily: "PoppinsRegular",
							beginAtZero:true,
							fontSize:14,
							callback: function(value, index, values) {
								if(parseInt(value) >= 1000){
									return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
								} else {
									return value;
								}

							}
						},
					}],
					xAxes: [{
						ticks: {
							fontColor: "white",
							fontFamily: "PoppinsRegular",
							fontSize:14
						}
					}]
				}
			}
		});
	}

	function makeChart2(label,data, data2,idChart,y, method1, method2){
		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(112, 203, 255, 1)");
		gradientFill.addColorStop(1, "rgba(112, 203, 255, 0.1)");
		
		var myChart = new Chart(ctxL, {
			type: 'line',
			data: {
				labels: label,
				datasets: [{
					"lineTension": 0,  
					"backgroundColor": "rgb(156, 39, 176)",
					"borderColor": "rgb(156, 39, 176)",
					"fill": false,
					"data": data,
					"id": "amount",
					"label": method1,
				},
				{
					"lineTension": 0,  
					"backgroundColor": "rgb(39, 176, 200)",
					"borderColor": "rgb(39, 176, 200)",
					"fill": false,
					"data": data2,
					"id": "amount",
					"label": method2,
				}]
			},
			maintainAspectRatio: false,
			responsive: true,
			options: {
				pan: {
					enabled: true,
					mode: 'xy',
				},
				zoom: {
					enabled: true,
      				mode: 'xy', // or 'x' for "drag" version
      			},
      			tooltips: 
      			{
      				titleFontSize: 16,
      				titleFontStyle: "normal",
      				titleFontFamily: "'PoppinsRegular'",
      				bodyFontColor: "#000000",
      				bodyFontSize: 14,
      				bodyFontStyle: "normal",
      				bodyFontColor: '#FFFFFF',
      				bodyFontFamily: "'PoppinsRegular', 'Arial', sans-serif",
      				callbacks: {
      					label: function(tooltipItem, data) {
      						return 'Jumlah: '+Number(tooltipItem.yLabel);
      					}
      				}
      			},
      			legend:{
      				labels: {
      					fontColor: 'white',
      					fontFamily: 'PoppinsRegular'
      				}
      			},
      			scales: {
      				yAxes: [{
      					scaleLabel: {
      						display: true,
      						labelString: y,
      						fontColor:"white",
      						fontFamily: "PoppinsRegular",
      					},
      					ticks: {
      						fontColor: "white",
      						fontFamily: "PoppinsRegular",
      						beginAtZero:true,
      						fontSize:14,
      						callback: function(value, index, values) {
      							if(parseInt(value) >= 1000){
      								return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      							} else {
      								return value;
      							}

      						}
      					},
      				}],
      				xAxes: [{
      					ticks: {
      						fontColor: "white",
      						fontFamily: "PoppinsRegular",
      						fontSize:14
      					}
      				}]
      			}
      		}
      	});
	}

	function makeChart3(label,data, data2,data3,idChart,y, method1, method2,method3){
		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(112, 203, 255, 1)");
		gradientFill.addColorStop(1, "rgba(112, 203, 255, 0.1)");
		
		var myChart = new Chart(ctxL, {
			type: 'line',
			data: {
				labels: label,
				datasets: [{
					"lineTension": 0,   
					"backgroundColor": "rgb(156, 39, 176)",
					"borderColor": "rgb(156, 39, 176)",
					"fill": false,
					"data": data,
					"id": "amount",
					"label": method1,
				},
				{
					"lineTension": 0,  
					"backgroundColor": "rgb(39, 176, 200)",
					"borderColor": "rgb(39, 176, 200)",
					"fill": false,
					"data": data2,
					"id": "amount",
					"label": method2,
				},
				{
					"lineTension": 0,  
					"backgroundColor": "rgb(255, 167, 0)",
					"borderColor": "rgb(255, 167, 0)",
					"fill": false,
					"data": data3,
					"id": "amount",
					"label": method3,
				}]
			},
			maintainAspectRatio: false,
			responsive: true,
			options: {
				pan: {
					enabled: true,
					mode: 'xy',
				},
				zoom: {
					enabled: true,
      				mode: 'xy', // or 'x' for "drag" version
      			},
      			tooltips: 
      			{
      				titleFontSize: 16,
      				titleFontStyle: "normal",
      				titleFontFamily: "'PoppinsRegular'",
      				bodyFontColor: "#000000",
      				bodyFontSize: 14,
      				bodyFontStyle: "normal",
      				bodyFontColor: '#FFFFFF',
      				bodyFontFamily: "'PoppinsRegular', 'Arial', sans-serif",
      				callbacks: {
      					label: function(tooltipItem, data) {
      						return 'Jumlah: '+Number(tooltipItem.yLabel);
      					}
      				}
      			},
      			legend:{
      				labels: {
      					fontColor: 'white',
      					fontFamily: 'PoppinsRegular'
      				}
      			},
      			scales: {
      				yAxes: [{
      					scaleLabel: {
      						display: true,
      						labelString: y,
      						fontColor:"white",
      						fontFamily: "PoppinsRegular",
      					},
      					ticks: {
      						fontColor: "white",
      						fontFamily: "PoppinsRegular",
      						beginAtZero:true,
      						fontSize:14,
      						callback: function(value, index, values) {
      							if(parseInt(value) >= 1000){
      								return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      							} else {
      								return value;
      							}

      						}
      					},
      				}],
      				xAxes: [{
      					ticks: {
      						fontColor: "white",
      						fontFamily: "PoppinsRegular",
      						fontSize:14
      					}
      				}]
      			}
      		}
      	});
	}
</script>
</body>
</html>