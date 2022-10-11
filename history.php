<?php 	 
session_start();
$_SESSION['page'] = 'simulasi';
include 'nav.php';

$id_simulation = $_GET['id'];
$id_main_simulation = $_GET['id_main'];
if($id_main_simulation == 0)$id_main_simulation = $id_simulation;

$main_method = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM _simulation WHERE id= ".$id_simulation));
$factor_used = mysqli_query($conn, "SELECT f.name, fu.precentage FROM _factor_used fu JOIN _factor f ON fu.id_factor = f.id WHERE fu.id_simulation = ".$id_simulation);
$query_batch = mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = ".$id_simulation);
$jumlah_batch = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _batch WHERE id_simulation = ".$id_main_simulation));
$get_method = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _simulation WHERE id_main_simulation = ".$id_main_simulation));
$jumlah_method = $get_method['x']+1;
?>
<style type="text/css">
	.card{
		background-color: white;
		color: black;
		border-radius: 25px;
	}
</style>
<div style="margin: 5%" id="history">
	<input type="hidden" id="id_main" value="<?=$id_main_simulation;?>">
	<input type="hidden" id="jumlah_method" value="<?=$jumlah_method;?>">
	<input type="hidden" id="jumlah_batch" value="<?=$jumlah_batch['x'];?>">
	<button class="btn btn-warning" onclick="location.href='simulasi.php'"><i class="fas fa-arrow-left"></i></button>	

	<div class="content">
		<br>	
		<h1 style="text-align:center;">History <?=$id_simulation.' - '.$main_method['simulation_name'];?></h1><br>
		<p> <b>Factor Used</b><br>
			<?php
			while ($row = mysqli_fetch_assoc($factor_used)) {
				echo $row['name'].' '.$row['precentage'].'%<br>';
			}
			?>
		</p>
		<div class="row">
			<?php

			$other_simul = mysqli_query($conn,"SELECT * FROM _simulation WHERE id =  ".$_GET['id']." OR id_main_simulation = ".$_GET['id']." ORDER BY id");

			while($row = mysqli_fetch_assoc($other_simul)){
				$x = $row['id'];
				$kueri=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver LEFT JOIN _driver d ON gd.id_driver = d.id_driver JOIN _batch b ON a.id_batch = b.id_batch  WHERE a.id_simulation = $x");

				echo '

				<div class="col-md-6">
				<p style="font-weight: bold;">Assignment '.$row['simulation_name'].'</p>
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
				$jumlah_dist = 0;
				$jumlah_dur = 0;
				while ($row2 = mysqli_fetch_assoc($kueri)) {
					if($batch != $row2['batch_num']){
						$i=1;
						$batch = $row2['batch_num'];
						if($batch != 1) {
							echo '<tr><td colspan="3"><b>Total Duration and Distance: </b></td>
							<td><b>'.number_format($jumlah_dur,3,'.','').' mins</b></td>
							<td><b>'.number_format($jumlah_dist,3,'.','').' km</b></td>
							</tr>';
							$jumlah_dist = 0;
							$jumlah_dur = 0;
						}
						echo '<tr style="background-color: lightgrey"><td colspan=11>Batch '.$batch.'</td></tr>';
					}
					$row3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_driver as x FROM _driver WHERE id_driver = ".$row2['id_driver']));
					$row4 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_pass as x FROM _passenger WHERE id_passenger = ".$row2['id_passenger']));
					$jumlah_dur += $row2['pickup_duration'];
					$jumlah_dist += $row2['pickup_distance'];
					echo 
					'<tr>
					<td>'.$i++.'</td>
					<td>D'.$row2['id_driver'].'</td>
					<td>P'.$row2['id_passenger'].'</td>
					<td>'.number_format((float)$row2['pickup_duration'], 3, '.', '').' mins</td>
					<td>'.number_format((float)$row2['pickup_distance'], 3, '.', '').' km</td>
					<td>'.$row2['rating'].'</td>';
					if($row['method']!='Goal Programming' && $row['method']!='Random Assignment' && $row['method']!= 'Hungarian Programming'){
						echo '<td>'.$row3['x'].'</td>
						<td>'.$row4['x'].'</td>';
					}
					echo '<td>'.$row2['price'].'</td>
					</tr>';
				}

				echo '<tr><td colspan="3"><b>Total Duration and Distance: </b></td>
				<td><b>'.number_format($jumlah_dur,3,'.','').' mins</b></td>
				<td><b>'.number_format($jumlah_dist,3,'.','').' km</b></td>
				</tr>';
				echo '</tbody>
				</table>
				</div>
				</div>

				';
			}
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<p style="font-weight: bold;">Basic Information</p>
			<div class="table-responsive" id="tabel" style="overflow-x: auto;">
				<table class="table table-hover table-light">
					<thead>
						<tr>
							<th width="20%" style="vertical-align: middle; "></th>
							<?php
							$comparison = mysqli_query($conn,"SELECT id,simulation_name FROM _simulation WHERE id_main_simulation = $id_simulation OR id = $id_simulation ORDER BY id");
							$arr_id = array();
							while($row = mysqli_fetch_assoc($comparison)){
								echo '<th>'.$row['simulation_name'].'</th>';
								array_push($arr_id,$row['id']);
							}

							echo '<tr><td><b>Jumlah Batch<b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _batch WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.$res['x'].'</td>';
							}
							echo '<tr><td><b>Jumlah Assignment</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _assignment WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.$res['x'].'</td>';
							}
							echo '<tr><td><b>Jumlah Tidak Terpasang</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _generate_passengers gp left join _assignment a on gp.id=a.id_generate_passenger WHERE a.id is NULL and gp.id_simulation =".$arr_id[$i]));
								echo '<td>'.$res['x'].'</td>';
							}
							// echo '<tr><td><b>Jumlah Cancelled Order<b></td>';
							// for($i = 0; $i<count($arr_id); $i++){
							// 	$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _assignment WHERE status = 4 AND id_simulation = ".$arr_id[$i]));
							// 	echo '<td>'.$res['x'].'</td>';
							// }
							echo '<tr><td><b>Total Assign Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(assign_time) as x FROM _batch WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' s</td>';
							}
							echo '<tr><td><b>Avg Assign Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(assign_time) as x FROM _batch WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' s</td>';
							}
							echo '<tr><td><b>Standar Deviasi Assign Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT STDDEV(assign_time) as x FROM _batch WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' s</td>';
							}
							// echo '<tr><td><b>Min Waiting Time</b></td>';
							// for($i = 0; $i<count($arr_id); $i++){
							// 	$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT (MIN(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp))) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger WHERE b.id_simulation= ".$arr_id[$i]));
							// 	echo '<td>'.number_format((float)($res['x']/60), 3, '.', '').' menit</td>';
							// }
							// echo '<tr><td><b>Max Waiting Time</b></td>';
							// for($i = 0; $i<count($arr_id); $i++){
							// 	$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT (MAX(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp))) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger WHERE b.id_simulation= ".$arr_id[$i]));
							// 	echo '<td>'.number_format((float)($res['x']/60), 3, '.', '').' menit</td>';
							// }
							echo '<tr><td><b>Avg Waiting Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT (AVG(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp))) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger WHERE b.id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)($res['x']/60), 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Standard Deviasi Waiting Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT STDDEV(diff) as x FROM (Select (TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp)) as diff from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger WHERE b.id_simulation=$arr_id[$i]) as x"));
								echo '<td>'.number_format((float)($res['x']/60), 3, '.', '').' menit</td>';
							}

							echo '<tr><td><b>Total Waiting Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT (SUM(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp))) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger WHERE b.id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)($res['x']/60), 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Min Pick Up Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT MIN(pickup_duration) as x FROM _assignment WHERE id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Max Pick Up Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT MAX(pickup_duration) as x FROM _assignment WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Avg Pick Up Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(pickup_duration) as x FROM _assignment WHERE id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Standard Deviasi Pickup Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT STDDEV(pickup_duration) as x FROM _assignment WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' menit</td>';
							}
							echo '<tr><td><b>Total Pick Up Time</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(pickup_duration) as x FROM _assignment WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' menit</td>';
							}

							echo '<tr><td><b>Min Pick Up Distance</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT MIN(pickup_distance) as x FROM _assignment WHERE id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' km</td>';
							}
							echo '<tr><td><b>Max Pick Up Distance</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT MAX(pickup_distance) as x FROM _assignment WHERE id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' km</td>';
							}
							echo '<tr><td><b>Avg Pick Up Distance</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT AVG(pickup_distance) as x FROM _assignment WHERE id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' km</td>';
							}
							echo '<tr><td><b>Standard Deviasi Pickup Distance</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT STDDEV(pickup_distance) as x FROM _assignment WHERE id_simulation = ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' km</td>';
							}
							echo '<tr><td><b>Total Pick Up Distance</b></td>';
							for($i = 0; $i<count($arr_id); $i++){
								$res = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(pickup_distance) as x FROM _assignment WHERE 	id_simulation= ".$arr_id[$i]));
								echo '<td>'.number_format((float)$res['x'], 3, '.', '').' km</td>';
							}
							?>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<p style="font-weight: bold;">Unassigned Passenger</p>
			<div class="table-responsive" id="tabel" style="overflow-x: auto;">
				<table class="table table-hover table-light">
					<thead>
						<tr>
							<th>Batch Number</th>
							<th>Id Passenger</th>
							<th>Assign Time</th>
							<th>Pickup Duration</th>
							<th>Pickup Distance</th>
							<th>Waiting Time</th>
							<th>RFM Score</th>
						</tr>
						<tr>
							<?php
								$kueri=mysqli_query($conn,"SELECT gp.id, gp.id_simulation,  b.batch_num, gp.id_passenger, b.assign_time, COALESCE(a.pickup_distance,0) as distance, COALESCE(a.pickup_duration,0) as duration, COALESCE(TIMESTAMPDIFF(Second, gp.timestamp, a.pickup_timestamp),0) as difference, p.rfm_score_pass FROM _generate_passengers gp join _batch b on b.id_batch=gp.id_batch join _passenger p on p.id_passenger=gp.id_passenger left join _assignment a on a.id_generate_passenger=gp.id WHERE gp.id_simulation= $id_simulation and a.id is NULL order by b.batch_num");
								while ($res = mysqli_fetch_assoc($kueri)) {
								echo '
									  <td>'. $res['batch_num']. '</td>
									  <td>'.$res['id_passenger']. '</td>
									  <td>' . $res['assign_time']. '</td>
									  <td>' . $res['duration']. '</td>
									  <td>' . $res['distance']. '</td>
									  <td>' . $res['difference']. '</td>
									  <td>' . $res['rfm_score_pass']. '</td></tr>';
								}
							?>
						</tr>
					</thead>
				</table>
			</div>
		</div>
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
							if($id_simulation == $id_main_simulation){
								$comparison = mysqli_query($conn,"SELECT simulation_name FROM _simulation WHERE id_main_simulation = $id_simulation OR id = $id_simulation ORDER BY id");
							}
							else{
								$comparison = mysqli_query($conn,"SELECT simulation_name FROM _simulation WHERE id = $id_simulation ORDER BY id");
							}
							
							while($row = mysqli_fetch_assoc($comparison)){
								echo '<th colspan = "4"><center>'.$row['simulation_name'].'</center></th>';
							}
							?>
						</tr>
						<tr>
							<?php
							if($id_simulation == $id_main_simulation){
								for ($i=0; $i < $jumlah_method ; $i++) { 
									echo '
									<th scope="col"><center>Assign Time (s)</center></th>
									<th scope="col"><center>Jumlah Assignment</center></th>
									<th scope="col"><center>Total Waiting Time Passenger(s)</center></th>
									<th scope="col"><center>Avg Waiting Time Passenger(s)</center></th>
									';
								}	
							}
							else{
								echo '
									<th scope="col"><center>Assign Time (s)</center></th>
									<th scope="col"><center>Jumlah Assignment</center></th>
									<th scope="col"><center>Total Waiting Time Passenger(s)</center></th>
									<th scope="col"><center>Avg Waiting Time Passenger(s)</center></th>
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
	<p style="font-weight: bold;">Tabel Driver dan Passenger per Batch</p>
	<div class="row">
		<?php
		while ($row = mysqli_fetch_assoc($query_batch)) {
			if($main_method['is_limit'] == 1){
				$gen_drivers = mysqli_query($conn,"SELECT * FROM _generate_drivers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id_simulation AND gd.id_batch <= ".$row['id_batch']." AND b.batch_num > ".($row['batch_num']-3)." AND gd.id NOT IN (SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
				$gen_pass = mysqli_query($conn,"SELECT * FROM _generate_passengers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id_simulation AND gd.id_batch <= ".$row['id_batch']." AND b.batch_num > ".($row['batch_num']-3)." AND gd.id NOT IN (SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
			}
			else if($main_method['is_limit'] == 2){
				$gen_drivers = mysqli_query($conn,"SELECT * FROM _generate_drivers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id_simulation AND gd.id_batch <= ".$row['id_batch']." AND b.batch_num = ".($row['batch_num'])." AND gd.id NOT IN (SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
				$gen_pass = mysqli_query($conn,"SELECT * FROM _generate_passengers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id_simulation AND gd.id_batch <= ".$row['id_batch']." AND b.batch_num = ".($row['batch_num'])." AND gd.id NOT IN (SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
			}
			else{
				$gen_drivers = mysqli_query($conn,"SELECT * FROM _generate_drivers WHERE id_simulation = $id_simulation AND id_batch <= ".$row['id_batch']." AND id NOT IN (SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
				$gen_pass = mysqli_query($conn,"SELECT * FROM _generate_passengers WHERE id_simulation = $id_simulation AND id_batch <= ".$row['id_batch']."  AND id NOT IN (SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id_simulation AND id_batch < ".$row['id_batch'].")");
			}

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
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Jumlah Assignment</center></span><br>
				</div>
				<div class="card-body">
					<div style="height:55vh; width:53vw;margin: 0px auto;">
						<canvas id="chart6" style="overflow-x: scroll;"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
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
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Total Waiting Time Passenger</center></span><br>
				</div>
				<div class="card-body">
					<div style="height:55vh; width:53vw;margin: 0px auto;">
						<canvas id="chart7" style="overflow-x: scroll;"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Avg Waiting Time Passenger</center></span><br>
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
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Avg Distance Pickup</center></span><br>
				</div>
				<div class="card-body">
					<div style="height:55vh; width:53vw;margin: 0px auto;">
						<canvas id="chart8" style="overflow-x: scroll;"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
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
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Jumlah Generate Driver Berdasarkan Batch</center></span><br>
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
			<div class="card mb-3">
				<div class="card-title" style="padding: 20px">
					<span style="font-family: 'PoppinsMedium';"><center>Jumlah Generate Passenger Berdasarkan Batch</center></span><br>
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
		showChart(id,"chart5","Avg Waiting Time (s)");
		showChart(id,"chart6","Jumlah Assignment");
		showChart(id,"chart7","Total Waiting Time (s)");
		showChart(id,"chart8","Avg Distance Pickup (s)");
	});

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
		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(173, 53, 186, 1)");
		gradientFill.addColorStop(1, "rgba(173, 53, 186, 0.1)");

		var label =[];
		var data = [];
		var jumlah_batch = $('#jumlah_batch').val();
		var jumlah_method = $('#jumlah_method').val();
		var type = 1;

		if(idChart == "chart1") type = 1;
		else if(idChart == "chart2") type = 2;
		else if(idChart == "chart3") type = 3;
		else if(idChart == "chart4") type = 4;
		else if(idChart == "chart5") type = 5;
		else if(idChart == "chart6") type = 6;
		else if(idChart == "chart7") type = 7;
		else if(idChart == "chart8") type = 8;

		var id_simulation = 0;
		
		if(idChart == 'chart1' || idChart == 'chart2' || idChart == 'chart3') {
			id_simulation = id;
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
					makeChart(label,data,idChart,y);
				}
			});
		}
		else{
			var label = [];
			var color = [];
			var nama = [];
			//kuning
			color.push("rgb(255, 167, 0)");
			//ungu
			color.push("rgb(156, 39, 176)");
			//biru
			color.push("rgb(39, 176, 200)");
			//merah
			color.push("rgb(229, 20, 37)");
			//ijo tua
			color.push("rgb(0, 138, 0)");
			//ijo muda
			color.push("rgb(164, 196, 0)");;
			color.push("rgb(27, 161, 226)");
			color.push("rgb(204, 225, 225)");
			color.push("rgb(225, 225, 204)");
			color.push("rgb(153, 204, 255)");

			for(var j = 0; j< jumlah_method; j++){
				data.push([]);

				$.ajax({
					url     : "view.php",
					type    : "POST",
					async   : false,
					data    : {
						id : id,
						j: j,
						graph : type,
					},
					success : function(show) {
						var arrayObjects = JSON.parse(show);
						
						data[j].push( new Array(arrayObjects.length));
						for (var i = 0; i < arrayObjects.length; i++) {
							if(j==0)label.push("Batch "+(i+1));
							data[j][i] = arrayObjects[i]['x'];
						}
					}
				});
				$.ajax({
					url     : "view.php",
					type    : "POST",
					async   : false,
					data    : {
						id : id,
						getnamasimul : 1,
					},
					success : function(show) {
						var arrayObjects = JSON.parse(show);
						
						for (var i = 0; i < arrayObjects.length; i++) {
							nama.push(arrayObjects[i]['simulation_name']);
						}
					}
				});

				var datasets=[];
				for (var i = 0; i<jumlah_method; i++){
					datasets.push({
						"lineTension": 0,  
						"backgroundColor": color[i],
						"borderColor": color[i],
						"fill": false,
						"data": data[i],
						"id": "amount",
						"label": nama[i],
					});
				}

				if(idChart == 'chart4') makeChartMulti("Total Assign Time (s)",label,idChart,datasets);
				else if(idChart == 'chart5') makeChartMulti("Avg Waiting Time Passenger (s)",label,idChart,datasets);
				else if(idChart == 'chart6') makeChartMulti("Jumlah Assignment",label,idChart,datasets);
				else if(idChart == 'chart7') makeChartMulti("Total Waiting Time Passenger (s)",label,idChart,datasets);
				else if(idChart == 'chart8') makeChartMulti("Avg Distance Pickup",label,idChart,datasets);
			}
		}	
	}

	function makeChartMulti(y,label,idChart,datasets){
		var ctxL = document.getElementById(idChart).getContext('2d');
		var gradientFill = ctxL.createLinearGradient(0, 0, 0, 290);
		gradientFill.addColorStop(0, "rgba(255, 167, 0, 1)");
		gradientFill.addColorStop(1, "rgba(255, 167, 0, 0.1)");

		var myChart = new Chart(ctxL, {
			type: 'line',
			data: {
				labels: label,
				datasets: datasets
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
      				bodyFontColor: "#FFFFFF",
      				bodyFontSize: 14,
      				bodyFontStyle: "normal",
      				bodyFontFamily: "'PoppinsRegular', 'Arial', sans-serif",
      				callbacks: {
      					label: function(tooltipItem, data) {
      						return 'Jumlah: '+Number(tooltipItem.yLabel);
      					}
      				}
      			},
      			legend:{
      				labels: {
      					fontColor: 'black',
      					fontFamily: 'PoppinsRegular'
      				}
      			},
      			scales: {
      				yAxes: [{
      					scaleLabel: {
      						display: true,
      						labelString: y,
      						fontColor:"black",
      						fontFamily: "PoppinsRegular",
      					},
      					ticks: {
      						fontColor: "black",
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
      						fontColor: "black",
      						fontFamily: "PoppinsRegular",
      						fontSize:14
      					}
      				}]
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
					bodyFontColor: "#FFFFFF",
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
						fontColor: 'black',
						fontFamily: 'PoppinsRegular'
					}
				},
				scales: {
					yAxes: [{
						scaleLabel: {
							display: true,
							labelString: y,
							fontColor:"black",
							fontFamily: "PoppinsRegular",
						},
						ticks: {
							fontColor: "black",
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
							fontColor: "black",
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