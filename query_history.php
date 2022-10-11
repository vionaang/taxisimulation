<?php  
include 'connect.php';

if(isset($_POST['getTableAssignment'])){
	$id = $_POST['id'];
	$kueri=mysqli_query($conn,"SELECT a.status, b.batch_num, gd.id_driver, gp.id_passenger FROM _assignment a JOIN _batch b ON a.id_batch = b.id_batch
		JOIN _generate_drivers gd ON a.id_generate_driver = gd.id 
		JOIN _generate_passengers gp ON a.id_generate_passenger = gp.id 
		WHERE (a.status = 1 OR a.status = 2) AND a.id_simulation = $id AND b.batch_num <= (SELECT batch_num FROM _batch WHERE id_simulation = $id AND is_current_batch = 1) ORDER BY a.id DESC");
	// var_dump($kueri);
	$i=1;
	while ($row = mysqli_fetch_assoc($kueri)) {
		if($row['status'] == 1) $status = 'Pick up';
		else $status = 'OTW';

		echo 
		'<tr>
		<td>'.$row['batch_num'].'</td>
		<td>D'.$row['id_driver'].'</td>
		<td>P'.$row['id_passenger'].'</td>
		<td>'.$status.'</td>
		</tr>';
	}
}
else if(isset($_POST['getBatch'])){
	$id = $_POST['id'];

	$batch=$_POST['batch_num']+1;

	echo "<h2>".$batch."</h2>";
}
?>