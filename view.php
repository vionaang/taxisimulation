<?php
require "connect.php";
session_start();

if(isset($_POST['getDriver'])){
	// var_dump($_POST);
	$kueri=mysqli_query($conn,"SELECT * FROM _driver WHERE is_active = 1");

	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Online';
		else $status = 'Offline';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_driver'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rating'].'</td>
		<td>'.$row['total_trip'].'</td>
		<td>'.$row['cancellation_rate'].'</td>
		<td>'.$row['total_distance'].'</td>
		<td>'.$row['rfm_score_driver'].'</td>
		<td>'.$status.'</td>
		<td>'.$is_active.'</td>
		<td><button class="btn btn-warning" value="'.$row["id_driver"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id_driver"].'" onclick="deletedata('.$row["id_driver"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['cariDriver'])){
	$nama = $_POST['nama'];
	$kueri=mysqli_query($conn,"SELECT * FROM _driver WHERE is_active = 1 AND name LIKE '%".$nama."%'");

	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Online';
		else $status = 'Offline';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_driver'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rating'].'</td>
		<td>'.$row['total_trip'].'</td>
		<td>'.$row['cancellation_rate'].'</td>
		<td>'.$row['total_distance'].'</td>
		<td>'.$row['rfm_score_driver'].'</td>
		<td>'.$status.'</td>
		<td>'.$is_active.'</td>
		<td><button class="btn btn-warning" value="'.$row["id_driver"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id_driver"].'" onclick="deletedata('.$row["id_driver"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['getPassenger'])){
	// var_dump($_POST);
	$kueri=mysqli_query($conn,"SELECT * FROM _passenger WHERE is_active = 1");

	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Online';
		else $status = 'Offline';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_passenger'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rfm_score_pass'].'</td>
		<td>'.$status.'</td>
		<td>'.$is_active.'</td>
		<td><button class="btn btn-warning" value="'.$row["id_passenger"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id_passenger"].'" onclick="deletedata('.$row["id_passenger"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['cariPassenger'])){
	$nama = $_POST['nama'];
	$kueri=mysqli_query($conn,"SELECT * FROM _passenger WHERE is_active = 1 AND name LIKE '%".$nama."%'");

	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Online';
		else $status = 'Offline';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_passenger'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rfm_score_pass'].'</td>
		<td>'.$status.'</td>
		<td>'.$is_active.'</td>
		<td><button class="btn btn-warning" value="'.$row["id_passenger"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id_passenger"].'" onclick="deletedata('.$row["id_passenger"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['getArea'])){
	$kueri=mysqli_query($conn,"SELECT * FROM _area WHERE is_active = 1");
	var_dump($kueri);
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['type'] == 1) $type = 'Pusat';
		elseif($row['type'] == 2) $type = 'Pinggir';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id'].'</td>
		<td>'.$row['upper_lat'].'</td>
		<td>'.$row['upper_long'].'</td>
		<td>'.$row['bottom_lat'].'</td>
		<td>'.$row['bottom_long'].'</td>
		<td>'.$type.'</td>
		<td>'.$is_active.'</td>
		<td><button class="btn btn-warning" value="'.$row["id"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id"].'" onclick="deletedata('.$row["id"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['cariArea'])){
	$nama = $_POST['nama'];
	$kueri=mysqli_query($conn,"SELECT * FROM _area WHERE is_active = 1 AND id=$nama");
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['type'] == 1) $type = 'Pusat';
		elseif($row['type'] == 0) $type = 'Pinggir';

		if($row['is_active'] == 1) $is_active = 'Y';
		else $is_active = 'N';
		
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id'].'</td>
		<td>'.$row['upper_lat'].'</td>
		<td>'.$row['upper_long'].'</td>
		<td>'.$row['bottom_lat'].'</td>
		<td>'.$row['bottom_long'].'</td>
		<td>'.$type.'</td>
		<td>'.$isactive.'</td>
		<td><button class="btn btn-warning" value="'.$row["id"].'" data-toggle="modal" data-target="#editmodal" onclick="edit(this.value)"><i class="fas fa-edit"></i></button>&nbsp;&nbsp;
		<button class="btn btn-danger"  value="'.$row["id"].'" onclick="deletedata('.$row["id"].')"><i class="far fa-trash-alt"></i></button></td>
		</tr>';
	}
}
else if(isset($_POST['getTableAssignment'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT a.status, b.batch_num, gd.id_driver, gp.id_passenger FROM _assignment a JOIN _batch b ON a.id_batch = b.id_batch
		JOIN _generate_drivers gd ON a.id_generate_driver = gd.id 
		JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id 
		WHERE (a.status = 1 OR a.status = 2) AND a.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ORDER BY a.id DESC");
	// var_dump($kueri);
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Pick up';
		else if($row['status' == 2]) $status = 'OTW';

		echo 
		'<tr>
		<td>'.$row['batch_num'].'</td>
		<td>D'.$row['id_driver'].'</td>
		<td>P'.$row['id_passenger'].'</td>
		<td>'.$status.'</td>
		</tr>';
	}
}
else if(isset($_POST['getTableOnlineDriver'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT * FROM _generate_drivers gp JOIN _batch b ON gp.id_batch = b.id_batch
		JOIN _driver d ON gp.id_driver = d.id_driver WHERE gp.id NOT IN 
		(SELECT id_generate_driver FROM _assignment WHERE id_simulation = $id AND status != 4 AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) )
		AND gp.id_simulation = $id AND gp.status = 1 ORDER BY b.batch_num");
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		echo 
		'<tr>
		<td>'.$row['batch_num'].'</td>
		<td>D'.$row['id_driver'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rfm_score_driver'].'</td>
		</tr>';
	}
}
else if(isset($_POST['getTableOnlinePassenger'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT * FROM _generate_passengers gp JOIN _batch b ON gp.id_batch = b.id_batch 
		JOIN _passenger p ON gp.id_passenger = p.id_passenger WHERE gp.id NOT IN 
		(SELECT id_generate_passenger FROM _assignment WHERE id_simulation = $id AND status != 4 AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) )
		AND gp.id_simulation = $id AND gp.status = 1  ORDER BY b.batch_num");
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		echo 
		'<tr>
		<td>'.$row['batch_num'].'</td>
		<td>P'.$row['id_passenger'].'</td>
		<td>'.$row['name'].'</td>
		<td>'.$row['rfm_score_pass'].'</td>
		</tr>';
	}
}
else if(isset($_POST['getInfo'])){
	$id = $_POST['id'];

	$simul=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM _simulation 
		WHERE id = $id LIMIT 1"));
	echo "<h4>".$simul['id']." - ".$simul['simulation_name']."</h4><h5 style='font-size: 17px'><br>Method: ".$simul['method']."</h5><p>
	Date: ".$simul['start_date']." - ".$simul['end_date']."<br>Time: ".$simul['start_hour']." - ".$simul['end_hour']."<br>
	</p>";
}
else if(isset($_POST['getBatch'])){
	$id = $_POST['id'];

	$batch=mysqli_fetch_assoc(mysqli_query($conn,"SELECT batch_num FROM _batch 
		WHERE id_simulation = $id AND is_current_batch = 1 LIMIT 1"));
	if($batch==NULL){
		$batch['batch_num']=0;
	}
	echo "<h2>".$batch['batch_num']."</h2>";
}

else if(isset($_POST['getGenDriver'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT * FROM _generate_drivers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ORDER BY b.id_batch, gd.id_driver");

	var_dump($_POST);
	$i=1;
	$batch = '';
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=3>Batch '.$batch.'</td></tr>';
		}

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>D'.$row['id_driver'].'</td>
		</tr>';
	}
}
else if(isset($_POST['getGenPassenger'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT * FROM _generate_passengers gp JOIN _batch b ON gp.id_batch = b.id_batch WHERE gp.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ORDER BY b.id_batch, gp.id_passenger");
	// var_dump($kueri);
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Pick up';
		else if($row['status' == 2]) $status = 'OTW';
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=3>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>P'.$row['id_passenger'].'</td>
		</tr>';
	}
}
else if(isset($_POST['getAssignment'])){
	$id = $_POST['id'];
	$simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT method FROM _simulation WHERE id=".$id));
	$kueri=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver LEFT JOIN _driver d ON gd.id_driver = d.id_driver JOIN _batch b ON a.id_batch = b.id_batch WHERE a.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ");
	$i=1;
	$jumlah_dist = 0;
	$jumlah_dur = 0;
	while ($row = mysqli_fetch_assoc($kueri)) {
		// if($row['status'] == 1) $status = 'Pick up';
		// else $status = 'OTW';
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
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
		$row3 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_driver as x FROM _driver WHERE id_driver = ".$row['id_driver']));
		$row4 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT rfm_score_pass as x FROM _passenger WHERE id_passenger = ".$row['id_passenger']));
		$jumlah_dur += $row['pickup_duration'];
		$jumlah_dist += $row['pickup_distance'];

		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>D'.$row['id_driver'].'</td>
		<td>P'.$row['id_passenger'].'</td>
		<td>'.number_format((float)$row['pickup_duration'], 3, '.', '').' mins</td>
		<td>'.number_format((float)$row['pickup_distance'], 3, '.', '').' km</td>
		<td>'.$row['rating'].'</td>';
		if($simul['method']!='Goal Programming' && $simul['method']!='Random Assignment' && $simul['method']!= 'Hungarian Programming'){
			echo '<td>'.$row3['x'].'</td>
			<td>'.$row4['x'].'</td>';
		}
		echo '<td>'.$row['price'].'</td>
		</tr>';
	}

	echo '<tr><td colspan="3"><b>Total Duration and Distance: </b></td>
				<td><b>'.number_format($jumlah_dur,3,'.','').' mins</b></td>
				<td><b>'.number_format($jumlah_dist,3,'.','').' km</b></td>
				</tr>';

	// $id = $_POST['id'];
	// $kueri=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver JOIN _batch b ON gp.id_batch = b.id_batch WHERE a.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ");
	// $x = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _simulation WHERE id_main_simulation = ".$id));

	// if($x['x'] > 0){
	// 	while ($row = mysqli_fetch_assoc($kueri)) {
	// 		$comparison = mysqli_query($conn,"SELECT id FROM _simulation WHERE id_main_simulation = $id OR id = $id ORDER BY id");
	// 		if($batch != $row['batch_num']){
	// 			$i=1;
	// 			$batch = $row['batch_num'];
	// 			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
	// 		}
	// 		echo 
	// 		'<tr>
	// 		<td>'.$i++.'</td>
	// 		<td>D'.$row['id_driver'].'</td>
	// 		<td>P'.$row['id_passenger'].'</td>
	// 		<td>'.$row['pickup_duration'].'</td>
	// 		<td>'.$row['pickup_distance'].'</td>
	// 		<td>'.$row['trip_duration'].'</td>
	// 		<td>'.$row['trip_distance'].'</td>
	// 		<td>'.$row['price'].'</td>
	// 		<td>'.$row['status'].'</td>
	// 		</tr>';

	// 		while($row2 = mysqli_fetch_assoc($comparison)){
	// 			$simul2=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver JOIN _batch b ON gp.id_batch = b.id_batch WHERE a.id_simulation = $row2['id'] AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $row2['id'] AND is_current_batch = 1)");
	// 			while ($row3 = mysqli_fetch_assoc($simul2)) {
	// 				if($batch != $row['batch_num']){
	// 					$i=1;
	// 					$batch = $row['batch_num'];
	// 					echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
	// 				}
	// 				echo 
	// 				'<tr>
	// 				<td>'.$i++.'</td>
	// 				<td>D'.$row['id_driver'].'</td>
	// 				<td>P'.$row['id_passenger'].'</td>
	// 				<td>'.$row['pickup_duration'].'</td>
	// 				<td>'.$row['pickup_distance'].'</td>
	// 				<td>'.$row['trip_duration'].'</td>
	// 				<td>'.$row['trip_distance'].'</td>
	// 				<td>'.$row['price'].'</td>
	// 				<td>'.$row['status'].'</td>
	// 				</tr>';
	// 			}
	// 		}
	// 	}
	// }
	// else{
	// $i=1;
	// while ($row = mysqli_fetch_assoc($kueri)) {
	// 	// if($row['status'] == 1) $status = 'Pick up';
	// 	// else $status = 'OTW';
	// 	if($batch != $row['batch_num']){
	// 		$i=1;
	// 		$batch = $row['batch_num'];
	// 		echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
	// 	}
	// 	echo 
	// 	'<tr>
	// 	<td>'.$i++.'</td>
	// 	<td>D'.$row['id_driver'].'</td>
	// 	<td>P'.$row['id_passenger'].'</td>
	// 	<td>'.$row['pickup_duration'].'</td>
	// 	<td>'.$row['pickup_distance'].'</td>
	// 	<td>'.$row['trip_duration'].'</td>
	// 	<td>'.$row['trip_distance'].'</td>
	// 	<td>'.$row['price'].'</td>
	// 	<td>'.$row['status'].'</td>
	// 	</tr>';
	// }
}
else if(isset($POST['getCompare'])){
	$id = $_POST['id'];
	$comparison = mysqli_query($conn,"SELECT id FROM _simulation WHERE id_main_simulation = $id ORDER BY id");
	$i=1;
	while($row2 = mysqli_fetch_assoc($comparison)){
		$simul2=mysqli_query($conn,"SELECT * FROM _assignment a JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id JOIN _generate_drivers gd ON gd.id = a.id_generate_driver JOIN _batch b ON gp.id_batch = b.id_batch WHERE a.id_simulation = ".$row2['id']. "AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = ".$row2['id']. "AND is_current_batch = 1) ");
		while ($row3 = mysqli_fetch_assoc($simul2)) {
			if($batch != $row3['batch_num']){
				$i=1;
				$batch = $row3['batch_num'];
				echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
			}
			echo 
			'<tr>
			<td>'.$i++.'</td>
			<td>D'.$row3['id_driver'].'</td>
			<td>P'.$row3['id_passenger'].'</td>
			<td>'.$row3['pickup_duration'].'</td>
			<td>'.$row3['pickup_distance'].'</td>
			<td>'.$row3['trip_duration'].'</td>
			<td>'.$row3['trip_distance'].'</td>
			<td>'.$row3['price'].'</td>
			<td>'.$row3['status'].'</td>
			</tr>';
		}
	}
}
else if(isset($_POST['getBatchData'])){
	$id = $_POST['id'];
	$x = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _simulation WHERE id_main_simulation = ".$id));

	$kueri=mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = $id ORDER BY id_batch");

	if($x['x'] > 0){
		$main_batch=mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = $id ORDER BY id_batch");
		
		while ($row = mysqli_fetch_assoc($main_batch)) {
			$comparison = mysqli_query($conn,"SELECT id FROM _simulation WHERE id_main_simulation = $id OR id = $id ORDER BY id");
			echo '<tr><td>'.$row['batch_num'].'</td>';
		// 	// echo '<td>'.number_format((float)$row['generate_time'], 3, '.', '').'</td>';

			while($row2 = mysqli_fetch_assoc($comparison)){
				$batch=mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = ".$row2['id']." AND batch_num = ".$row['batch_num']);
				$average = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.batch_num, AVG(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
					JOIN _batch c ON c.id_batch = b.id_batch
					WHERE b.id_simulation = ".$row2['id']." AND c.batch_num = ".$row['batch_num']));
				$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.batch_num, SUM(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
					JOIN _batch c ON c.id_batch = b.id_batch
					WHERE b.id_simulation = ".$row2['id']." AND c.batch_num = ".$row['batch_num']));
				while ($row3 = mysqli_fetch_assoc($batch)) {
					$jum_assign = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _assignment WHERE id_simulation = '".$row2['id']."' AND id_batch = '".$row3['id_batch']."'"));
					echo '<td>'.number_format((float)$row3['assign_time'], 3, '.', '').'</td>';
					echo '<td>'.$jum_assign['x'].'</td>';
					echo '<td>'.number_format($total['x']).'</td>';
					echo '<td>'.number_format($average['x']).'</td>';
				}
			}
			echo '</tr>';
		}
	}
	else{
		while ($row = mysqli_fetch_assoc($kueri)) {
			$total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.batch_num, SUM(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
				JOIN _batch c ON c.id_batch = b.id_batch
				WHERE b.id_simulation = $id AND c.batch_num = ".$row['batch_num']));
			$avg = mysqli_fetch_assoc(mysqli_query($conn,"SELECT c.batch_num, AVG(TIMESTAMPDIFF(SECOND,a.timestamp,b.pickup_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
				JOIN _batch c ON c.id_batch = b.id_batch
				WHERE b.id_simulation = $id AND c.batch_num = ".$row['batch_num']));
			$jum_assign = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as x FROM _assignment WHERE id_simulation = '$id' AND id_batch = '".$row['id_batch']."'"));

			echo 
			'<tr>
			<td>'.$row['batch_num'].'</td>
			<td>'.number_format((float)$row['assign_time'], 3, '.', '').'</td>
			<td>'.$jum_assign['x'].'</td>
			<td>'.number_format($total['x']).'</td>
			<td>'.number_format($avg['x']).'</td>
			</tr>';
		}
	}	
}
// else if(isset($_POST['getBatchData2'])){
// 	$id = $_POST['id'];
// 	$x = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS x FROM _simulation WHERE id_main_simulation = ".$id));

// 	$kueri=mysqli_query($conn,"SELECT * FROM _batch 
// 		WHERE id_simulation = $id ORDER BY id_batch");

// 	if($x['x'] > 0){
// 		$main_batch=mysqli_query($conn,"SELECT * FROM _batch WHERE id_simulation = $id ORDER BY id_batch");

// 		while ($row = mysqli_fetch_assoc($main_batch)) {
// 			$comparison = mysqli_query($conn,"SELECT id FROM _simulation WHERE id_main_simulation = $id OR id = $id ORDER BY id");
// 			echo '<tr><td>'.$row['batch_num'].'</td>';

// 			while($row2 = mysqli_fetch_assoc($comparison)){
// 				$total = (mysqli_query($conn,"SELECT c.batch_num, SUM(TIMESTAMPDIFF(SECOND,a.timestamp,b.arrived_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
// 					JOIN _batch c ON c.id_batch = b.id_batch
// 					WHERE b.id_simulation = ".$row2['id']." AND c.batch_num = ".$row['batch_num']));
// 				while ($row3 = mysqli_fetch_assoc($total)) {
// 					echo '<td>'.number_format($row3['x']).'</td>';
// 				}
// 			}
// 			echo '</tr>';
// 		}
// 	}
// 	else{
// 		while ($row = mysqli_fetch_assoc($kueri)) {
// 			$total = (mysqli_query($conn,"SELECT c.batch_num, SUM(TIMESTAMPDIFF(SECOND,a.timestamp,b.arrived_timestamp)) as x from _generate_passengers a JOIN _assignment b ON a.id = b.id_generate_passenger 
// 					JOIN _batch c ON c.id_batch = b.id_batch
// 					WHERE b.id_simulation = $id AND c.batch_num = ".$row['batch_num']));
// 			echo 
// 			'<tr>
// 			<td>'.$row['batch_num'].'</td>
// 			<td>'.number_format((float)$row['x'], 3, '.', '').'</td>
// 			</tr>';
// 		}
// 	}	
// }
else if(isset($_POST['getFactorUsed'])){
	$factor = (mysqli_query($conn, "SELECT * FROM _factor_used fu JOIN _factor f ON fu.id_factor = f.id WHERE fu.id_simulation = ".$_POST['id']));
	while($row = mysqli_fetch_assoc($factor)){
		echo '
		<th scope="col">'.$factor['name'].' ('.$factor['precentage'].')</th>
		';
	}
}
else if(isset($_POST['graph'])){
	$id_simulation = $_POST['id'];
	$type = $_POST['graph'];
	$result_array = Array();

	if($type == 1){
		$query = (mysqli_query($conn,"SELECT b.batch_num, COUNT(*) AS x FROM _generate_drivers gd JOIN _batch b ON gd.id_batch = b.id_batch WHERE gd.id_simulation = $id_simulation GROUP BY gd.id_batch"));
	}
	else if($type == 2){
		$query = (mysqli_query($conn,"SELECT b.batch_num, COUNT(*) AS x FROM _generate_passengers gp JOIN _batch b ON gp.id_batch = b.id_batch WHERE gp.id_simulation = $id_simulation GROUP BY gp.id_batch"));
	}
	else if($type == 3){
		$query = (mysqli_query($conn,"SELECT generate_time as x FROM _batch WHERE id_simulation = $id_simulation ORDER BY id_batch"));
	}
	else if($type == 4){
		// $query = (mysqli_query($conn,"SELECT assign_time as x FROM _batch WHERE id_simulation = $id_simulation ORDER BY id_batch"));
		$j = $_POST['j'];
		$other_simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id LIMIT 1 OFFSET $j"));
		$id_now = $other_simul['id'];
		$query = (mysqli_query($conn,"SELECT assign_time as x FROM _batch WHERE id_simulation = $id_now ORDER BY id_batch"));
	}
	else if($type == 5){
		$j = $_POST['j'];
		$other_simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id LIMIT 1 OFFSET $j"));
		$id_now = $other_simul['id'];
		$query = (mysqli_query($conn,"SELECT b.batch_num,COALESCE(AVG(TIMESTAMPDIFF(SECOND,gp.timestamp,a.pickup_timestamp)),0) as x 
			from _batch b 
			LEFT JOIN _assignment a ON b.id_batch = a.id_batch
			LEFT JOIN _generate_passengers gp ON gp.id = a.id_generate_passenger
			WHERE b.id_simulation = $id_now GROUP BY b.id_batch order by batch_num"));
		// $query = (mysqli_query($conn,"SELECT b.batch_num,COALESCE(AVG(b.assign_time+a.pickup_duration),0) as x 
		// 	from _batch b 
		// 	LEFT JOIN _assignment a ON b.id_batch = a.id_batch
		// 	LEFT JOIN _generate_passengers gp ON gp.id = a.id_generate_passenger
		// 	WHERE b.id_simulation = $id_simulation GROUP BY b.id_batch"));
	}
	else if($type == 6){
		$j = $_POST['j'];
		$other_simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id LIMIT 1 OFFSET $j"));
		$id_now = $other_simul['id'];
		$query = (mysqli_query($conn,"SELECT 
			(case when a.pickup_duration is null then 0 else COUNT(*) end) as x 
			FROM _assignment a 
			RIGHT JOIN _batch b ON b.id_batch = a.id_batch WHERE b.id_simulation = $id_now GROUP BY b.id_batch order by b.id_batch"));
	}
	else if($type == 7){
		$j = $_POST['j'];
		$other_simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id LIMIT 1 OFFSET $j"));
		$id_now = $other_simul['id'];
		$query = (mysqli_query($conn,"SELECT b.batch_num,COALESCE(SUM(TIMESTAMPDIFF(SECOND,gp.timestamp,a.pickup_timestamp)),0) as x 
			from _batch b 
			LEFT JOIN _assignment a ON b.id_batch = a.id_batch
			LEFT JOIN _generate_passengers gp ON gp.id = a.id_generate_passenger
			WHERE b.id_simulation = $id_now GROUP BY b.id_batch order by batch_num"));
	}
	else if($type == 8){
		$j = $_POST['j'];
		$other_simul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id LIMIT 1 OFFSET $j"));
		$id_now = $other_simul['id'];
		$query = (mysqli_query($conn,"SELECT b.batch_num, (case when a.pickup_distance is NULL then 0 else AVG(a.pickup_distance)end) as x FROM _assignment a 
			RIGHT JOIN _batch b ON b.id_batch = a.id_batch WHERE b.id_simulation = $id_now  GROUP BY b.id_batch order by b.batch_num;"));

		// $query = (mysqli_query($conn,"SELECT SUM(SUBSTRING(pickup_distance,1,length(pickup_distance)-3)) as x FROM _assignment WHERE id_simulation = $id_now GROUP BY id_batch"));
	}

	while ($row = mysqli_fetch_assoc($query)) {
		array_push($result_array, $row);
	}
	echo $json_array = json_encode($result_array);
}

if(isset($_POST['getnamasimul'])){
	$id_simulation = $_POST['id'];
	$result_array = Array();
	$query = (mysqli_query($conn,"SELECT simulation_name FROM _simulation WHERE id= $id_simulation OR id_main_simulation = $id_simulation ORDER BY id"));
	while ($row = mysqli_fetch_assoc($query)) {
		array_push($result_array, $row);
	}
	echo $json_array = json_encode($result_array);
}

if(isset($_POST['getRFMDataPass'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT * FROM _rfm_data rd join _batch b on rd.id_batch=b.id_batch where rd.id_simulation=$id AND id_pass is not NULL order by rd.id_batch ASC");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=10>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_pass'].'</td>
		<td>'.$row['recency'].'</td>
		<td>'.$row['frequency'].'</td>
		<td>'.$row['monetary'].'</td>
		<td>'.$row['r_score'].'</td>
		<td>'.$row['f_score'].'</td>
		<td>'.$row['m_score'].'</td>
		<td>'.$row['rfm_score'].'</td>
		</tr>';
	}
}

if(isset($_POST['getRFMDataDriver'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT * FROM _rfm_data rd join _batch b on rd.id_batch=b.id_batch where rd.id_simulation=$id AND id_driver is not NULL order by rd.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=10>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_driver'].'</td>
		<td>'.$row['recency'].'</td>
		<td>'.$row['frequency'].'</td>
		<td>'.$row['monetary'].'</td>
		<td>'.$row['r_score'].'</td>
		<td>'.$row['f_score'].'</td>
		<td>'.$row['m_score'].'</td>
		<td>'.$row['rfm_score'].'</td>
		</tr>';
	}
}

if(isset($_POST['getFreqP'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT b.batch_num, rq.id_simulation, rq.id_batch, rq.id_passenger, rq.freq1, rq.freq2, rq.freq3, rq.freq_total, rd.f_score FROM `_rfm_freq` rq join _rfm_data rd on rq.id_batch = rd.id_batch join _batch b on rq.id_batch=b.id_batch and rq.id_passenger = rd.id_pass where rq.id_simulation =$id and rq.id_passenger is not null order by rq.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'
		<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_passenger'].'</td>
		<td>'.$row['freq1'].'</td>
		<td>'.$row['freq2'].'</td>
		<td>'.$row['freq3'].'</td>
		<td>'.$row['freq_total'].'</td>
		<td>'.$row['f_score'].'</td>
		</tr>';
	}
}

if(isset($_POST['getFreqD'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT b.batch_num, rq.id_simulation, rq.id_batch, rq.id_driver, rq.freq1, rq.freq2, rq.freq3, rq.freq_total, rd.f_score FROM `_rfm_freq` rq join _rfm_data rd on rq.id_batch = rd.id_batch join _batch b on rq.id_batch= b.id_batch and rq.id_driver = rd.id_driver where rq.id_simulation =$id and rq.id_batch is not null order by rq.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_driver'].'</td>
		<td>'.$row['freq1'].'</td>
		<td>'.$row['freq2'].'</td>
		<td>'.$row['freq3'].'</td>
		<td>'.$row['freq_total'].'</td>
		<td>'.$row['f_score'].'</td>
		</tr>';
	}
}

if(isset($_POST['getQuantileP'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT * FROM `_rfm_quantile` rq join _batch b on rq.id_batch= b.id_batch where rq.id_simulation=$id and type='Passenger' order by rq.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['quantile'].'</td>
		<td>'.$row['frequency'].'</td>
		<td>'.$row['monetary'].'</td>
		</tr>';
	}
}

if(isset($_POST['getQuantileD'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT * FROM `_rfm_quantile` rq join _batch b on rq.id_batch= b.id_batch where rq.id_simulation=$id and type='Driver' order by rq.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['quantile'].'</td>
		<td>'.$row['frequency'].'</td>
		<td>'.$row['monetary'].'</td>
		</tr>';
	}
}
if(isset($_POST['getMonetP'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT b.batch_num, rq.id_simulation, rq.id_batch, rq.id_passenger, rq.monet1, rq.monet2, rq.monet3, rq.monet_total, rd.m_score FROM `_rfm_monet` rq join _rfm_data rd on rq.id_batch = rd.id_batch join _batch b on rq.id_batch= b.id_batch and rq.id_passenger = rd.id_pass where rq.id_simulation =$id and rq.id_passenger is not null order by rq.id_batch");
	$i=1;
	$batch=0;

	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_passenger'].'</td>
		<td>'.$row['monet1'].'</td>
		<td>'.$row['monet2'].'</td>
		<td>'.$row['monet3'].'</td>
		<td>'.$row['monet_total'].'</td>
		<td>'.$row['m_score'].'</td>
		</tr>';
	}
}
if(isset($_POST['getMonetD'])){
	$id=$_POST['simulasi'];
	$kueri=mysqli_query($conn,"SELECT b.batch_num, rq.id_simulation, rq.id_batch, rq.id_driver, rq.monet1, rq.monet2, rq.monet3, rq.monet_total, rd.m_score FROM `_rfm_monet` rq join _rfm_data rd on rq.id_batch = rd.id_batch join _batch b on rq.id_batch= b.id_batch and rq.id_driver = rd.id_driver where rq.id_simulation =$id and rq.id_driver is not null order by rq.id_batch");
	$i=1;
	$batch=0;
	
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($batch != $row['batch_num']){
			$i=1;
			$batch = $row['batch_num'];
			echo '<tr style="background-color: lightgrey"><td colspan=9>Batch '.$batch.'</td></tr>';
		}
		echo 
		'
		<tr>
		<td>'.$i++.'</td>
		<td>'.$row['id_driver'].'</td>
		<td>'.$row['monet1'].'</td>
		<td>'.$row['monet2'].'</td>
		<td>'.$row['monet3'].'</td>
		<td>'.$row['monet_total'].'</td>
		<td>'.$row['m_score'].'</td>
		</tr>';
	}
}

?>