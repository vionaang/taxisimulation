<?php
require "connect.php";
session_start();

if(isset($_POST['deletedriver'])){
	$sql = mysqli_query($conn,"UPDATE _driver SET is_active = 0 WHERE id_driver = '".$_POST['id_driver']."'");
}
else if(isset($_POST['deletepass'])){
	$sql = mysqli_query($conn,"UPDATE _passenger SET is_active = 0 WHERE id_passenger = '".$_POST['id_passenger']."'");
}
else if(isset($_POST['deletearea'])){
	$sql = mysqli_query($conn,"UPDATE _area SET is_active = 0 WHERE id = '".$_POST['id']."'");
}
else if(isset($_POST['deletesimulation'])){
	$sql3 = mysqli_query($conn,"DELETE FROM _area_used WHERE id_simulation = '".$_POST['id']."'");
	$sql4 = mysqli_query($conn,"DELETE FROM _assignment WHERE id_simulation = '".$_POST['id']."'");
	$sql6 = mysqli_query($conn,"DELETE FROM _factor_used WHERE id_simulation = '".$_POST['id']."'");
	$sql7 = mysqli_query($conn,"DELETE FROM _rfm_data WHERE id_simulation = '".$_POST['id']."'");
	$sql8 = mysqli_query($conn,"DELETE FROM _factor_data WHERE id_generate_driver IN (SELECT id FROM _generate_drivers WHERE id_simulation = '".$_POST['id']."') OR id_generate_passenger IN (SELECT id FROM _generate_passengers WHERE id_simulation = '".$_POST['id']."') ");
	$sql9 = mysqli_query($conn,"DELETE FROM _normalisasi WHERE id_generate_driver IN (SELECT id FROM _generate_drivers WHERE id_simulation = '".$_POST['id']."') OR id_generate_passenger IN (SELECT id FROM _generate_passengers WHERE id_simulation = '".$_POST['id']."') ");

	$sql1 = mysqli_query($conn,"DELETE FROM _generate_passengers WHERE id_simulation = '".$_POST['id']."'");
	$sql2 = mysqli_query($conn,"DELETE FROM _generate_drivers WHERE id_simulation = '".$_POST['id']."'");
	$sql5 = mysqli_query($conn,"DELETE FROM _batch WHERE id_simulation = '".$_POST['id']."'");
	$sql10 = mysqli_query($conn,"DELETE FROM _simulation WHERE id = '".$_POST['id']."'");

	if($sql1 && $sql2 && $sql3 && $sql4 && $sql5 && $sql6 && $sql7 && $sql8 && $sql9 && $sql10){
		echo 'Deleted!';
	}
	else {
		echo 'Sorry, error while deleting!';
	}
	
	exit();
}

if($sql){
	echo 'Deleted!';
}
else{
	echo 'Sorry, error while deleting!';
}
