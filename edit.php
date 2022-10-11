<?php
require "connect.php";
session_start();

if(isset($_POST['editdriver'])){
	$id=$_POST['id'];
	$nama = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['nama']));
	$rating = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['rating']));
	$total_trip = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['total_trip']));
	$total_distance = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['total_distance']));
	$cancel_rate = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['cancel_rate']));
	$status = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['status']));
	$rfm_score = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['rfm_score']));
	
	$sql=mysqli_query($conn,"UPDATE _driver SET name='$nama', rating = $rating, total_trip = $total_trip, total_distance = $total_distance, cancellation_rate = $cancel_rate, status = $status, rfm_score = $rfm_score WHERE id_driver=$id");
}
else if(isset($_POST['editpassenger'])){
	$id=$_POST['id'];
	$nama = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['nama']));
	$status = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['status']));
	$rfm_score = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['rfm_score']));
	
	$sql=mysqli_query($conn,"UPDATE _passenger SET name='$nama', status = $status, rfm_score = $rfm_score WHERE id_passenger=$id");
}
else if(isset($_POST['editarea'])){
	$id=$_POST['id'];
	$upper_lat = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['upper_lat']));
	$upper_long = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['upper_long']));
	$bottom_lat = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['bottom_lat']));
	$bottom_long = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['bottom_long']));
	$type = str_replace(array("'", '"'), array("&#39;", "&quot;"), htmlspecialchars($_POST['type']));
	
	$sql=mysqli_query($conn,"UPDATE _area SET upper_lat='$upper_lat', upper_long = $upper_long, bottom_lat = $bottom_lat, bottom_long = $bottom_long, type = $type WHERE id=$id");
}

if($sql){
	echo 'Save changes successfully!';
}
else{
	echo 'Sorry, error while uploading!';
}