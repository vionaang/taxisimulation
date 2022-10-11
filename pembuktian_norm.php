<?php 	 
session_start();
$_SESSION['page'] = 'simulasi';
include 'nav.php';
include 'connect.php';

$batch_sql = mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = ".$_GET['id']);
$simul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM _simulation WHERE id =".$_GET['id']));

$list_driver=array();
$factor_used = mysqli_query($conn, "SELECT f.name, fu.precentage FROM _factor_used fu JOIN _factor f ON fu.id_factor = f.id WHERE fu.id_simulation = ".$_GET['id']);
?>
<div style="margin: 5%" id="pembuktian">
		<button class="btn btn-warning" onclick="history.back()"><i class="fas fa-arrow-left"></i></button>

	<h1 style="text-align:center;">Pembuktian Normalisasi Data</h1>
	<br>
	<h2 style="text-align:center;"><?=$simul['id'].' - '.$simul['simulation_name'];?></h2>

	<div class="content">
		<p> <b>Factor Used</b><br>
			<?php
			while ($row = mysqli_fetch_assoc($factor_used)) {
				echo $row['name'].' '.$row['precentage'].'%<br>';
			}
			?>
		</p>
		<?php
		$id=$_GET['id'];
		while($row = mysqli_fetch_assoc($batch_sql)){
			$id_batch = $row['id_batch'];
			$driver_sql = mysqli_query($conn,"SELECT DISTINCT(d.id_driver), gd.id FROM _normalisasi fd JOIN _generate_drivers gd ON fd.id_generate_driver = gd.id JOIN  _driver d ON gd.id_driver = d.id_driver WHERE gd.id_simulation = $id AND fd.id_batch = $id_batch ORDER BY gd.id");
			echo '
			<div class="row">
			<div class="col-md-12">
			<p style="font-weight: bold;">Batch '.$row['batch_num'].'</p>
			<div class="table-responsive" id="tabel" style="overflow-x: auto;">
			<table class="table table-hover table-light">
			<thead>';
			echo '<th>#</th>';
			$list_driver = array();
			while($row2=mysqli_fetch_assoc($driver_sql)){
				array_push($list_driver,$row2['id']);
				echo '<th>D'.$row2['id_driver'].'</th>';
			}
			echo'
			</thead>
			<tbody>';
			$pass_sql = mysqli_query($conn,"SELECT DISTINCT(p.id_passenger), gp.id FROM _normalisasi fd JOIN _generate_passengers gp ON fd.id_generate_passenger = gp.id JOIN _passenger p ON gp.id_passenger = p.id_passenger WHERE gp.id_simulation = $id AND fd.id_batch = $id_batch ORDER BY gp.id");
			while($row3=mysqli_fetch_assoc($pass_sql)){
				$id_generate_passenger = $row3['id'];
				echo '
				<tr>
				<td>P'.$row3['id_passenger'].'</td>';
				for ($x = 0; $x < count($list_driver); $x++) {
					$id_generate_driver = $list_driver[$x];
					$factor_data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM _normalisasi WHERE id_batch = $id_batch AND id_generate_driver = $id_generate_driver AND id_generate_passenger = ".$row3['id']));

					$is_assigned = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _assignment WHERE id_generate_driver = $id_generate_driver AND id_generate_passenger = $id_generate_passenger AND id_batch = $id_batch"));
					if($is_assigned['x']>0) echo '<td style="background-color: #ffc107">';
					else echo '<td>';
					
					// echo $id_generate_driver .' - '. $row3['id'];
					$jumlah = 0;
					if($factor_data['distance'] != null) {
						echo 'Distance: '.$factor_data['distance'].' <br>'; 
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'distance' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['distance']);
					}
					if($factor_data['duration'] != null) {
						echo 'Duration: '.$factor_data['duration'].' <br>'; 
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'duration' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['duration']);
					}
					if($factor_data['total_trip'] != null) {
						echo 'Total Trip: '.$factor_data['total_trip'].'<br>'; 
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'total_trip' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['total_trip']);
					}
					if($factor_data['cancellation_rate'] != null) {
						echo 'Cancellation Rate: '.$factor_data['cancellation_rate'].'<br>';
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'cancellation_rate' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['cancellation_rate']);
					}
					if($factor_data['rating'] != null) {
						echo 'Rating: '.$factor_data['rating'].'<br>';
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'rating' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['rating']);
					}
					if($factor_data['total_distance'] != null) {
						echo 'Total Distance: '.$factor_data['total_distance'].'<br>';
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'total_distance' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['total_distance']);
					}
					if($factor_data['rfm_score_driver'] != null) {
						echo 'RFM Driver: '.$factor_data['rfm_score_driver'].'<br>';
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'rfm_score_driver' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['rfm_score_driver']);
					}
					if($factor_data['rfm_score_pass'] != null) {
						echo 'RFM Passenger: '.$factor_data['rfm_score_pass'].'<br>';
						$percent = mysqli_fetch_assoc(mysqli_query($conn,"SELECT fu.precentage as x FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE f.name = 'rfm_score_pass' AND id_simulation =".$_GET['id']));
						$jumlah+=($percent['x']/100*$factor_data['rfm_score_pass']);
					}
					if($factor_data['waiting_time_p'] != null) {
						echo 'Waiting Time Passenger: '.$factor_data['waiting_time_p'].'<br>';
						$jumlah+=($factor_data['waiting_time_p']);
					}
					if($factor_data['waiting_time_d'] != null) {
						echo 'Waiting Time Driver: '.$factor_data['waiting_time_d'].'<br>';
						$jumlah+=($factor_data['waiting_time_d']);
					}
					echo '<b>Total: '.$jumlah.'</b></td>';
				}
				echo '</tr>	
				';
			}

			echo'
			</tbody>
			</table>
			</div>
			</div>
			</div>
			';
		}
		?>
	</div>
</div>
<?php 	 
include 'footer.php';
?>
<script type="text/javascript">
	$( document ).ready(function() {
		var id =<?=$_GET['id'];?>;
		// loadDriver(id);
	});

	// function loadDriver(id) {
	// 	$.ajax({
	// 		url     : "view.php",
	// 		type    : "POST",
	// 		async   : false,
	// 		data    : {
	// 			id : id,
	// 			id : id_batch,
	// 			getDriverPembuktian : 1,
	// 		},
	// 		success : function(show) {
	// 			$('#factor_header').html(show);
	// 		}
	// 	});
	// }
</script>
</body>
</html>