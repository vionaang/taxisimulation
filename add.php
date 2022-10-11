<?php
require "connect.php";
session_start();
if(isset($_POST['adddriver'])){
	$name=$_POST['nama'];
	$rating=$_POST['rating'];
	$total_trip=$_POST['total_trip'];
	$total_distance=$_POST['total_distance'];
	$cancellation_rate=$_POST['cancel_rate'];
	$status=$_POST['status'];
	$rfm_score=$_POST['rfm_score'];

	mysqli_query($conn,"INSERT INTO _driver VALUES(DEFAULT,'$name',$rating, $total_trip, $cancellation_rate, $total_distance, $status, 1, $rfm_score)");
	header('Location: add_driver.php?stat=1');
}
else if(isset($_POST['addpassenger'])){
	$name=$_POST['nama'];
	$status=$_POST['status'];
	$rfm_score=$_POST['rfm_score'];

	mysqli_query($conn,"INSERT INTO _passenger VALUES(DEFAULT,'$name',$rfm_score, $status, 1)");
	header('Location: add_passenger.php?stat=1');
}
else if(isset($_POST['addsimulation'])){
	$name=$_POST['nama'];
	$startdate=$_POST['startdate'];
	$enddate=$_POST['enddate'];
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$rfm1=$_POST['rfm_filter1'];
	$rfm2=$_POST['rfm_filter2'];
	$rfm3=$_POST['rfm_filter3'];
	$recency1=$_POST['recency1'];
	$recency2=$_POST['recency2'];
	$recency3=$_POST['recency3'];
	$recency4=$_POST['recency4'];
	$recency5=$_POST['recency5'];
	$rat_prec = $_POST['rat_prec'];
	$dur_prec = $_POST['dur_prec'];
	$dis_prec = $_POST['dis_prec'];
	$rfmd_prec = $_POST['rfmd_prec'];
	$ttrip_prec = $_POST['ttrip_prec'];
	$cancel_prec = $_POST['cancel_prec'];
	$tdist_prec = $_POST['tdist_prec'];
	$rfmp_prec = $_POST['rfmp_prec'];
	$goal_totaltrip = $_POST['goal_totaltrip'];
	$goal_totaldist = $_POST['goal_totaldist'];

	echo '<script>console.log('. $rfm1 .'); </script>';
	echo '<script>console.log('. $rfm2 .'); </script>';
	echo '<script>console.log('. $rfm3 .'); </script>';

	if($rfm1 == "" || $rfm2 == "" || $rfm3 == ""){
		$rfm1=0;
		$rfm2=0;
		$rfm3=0;
		$method="RFM Comparison";
	}
	else{
		$method="Modified RFM Comparison";
	}

	mysqli_query($conn,"INSERT INTO _simulation VALUES(DEFAULT,'$name','$method', '$startdate', '$enddate', '$starttime', '$endtime', 1, 0,NULL)");
	$cek = true;



	$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM _simulation ORDER BY id DESC LIMIT 1"));
	$simul_id=$res['id'];


	$n=count($_POST['checkbox']);
	$n_area = count($_POST['checkarea']);

	for($i=0;	$i<$n;	$i++) {
	    if($_POST['checkbox'][$i]=="rating" && $_POST['rat_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 1, $simul_id, $rat_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_trip" && $_POST['ttrip_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 2, $simul_id, $ttrip_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="cancellation_rate" && $_POST['cancel_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 3, $simul_id, $cancel_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_distance" && $_POST['tdist_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 4, $simul_id, $tdist_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT, '$goal_totaldist')");
		}
		else if($_POST['checkbox'][$i]=="rfm_driver" && $_POST['rfmd_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 5, $simul_id, $rfmd_prec, $recency1,$recency2,$recency3,$recency4,$recency5, $rfm1, $rfm2, $rfm3,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="distance" && $_POST['dis_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 6, $simul_id, $dis_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="duration" && $_POST['dur_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 7, $simul_id, $dur_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="rfm_pass" && $_POST['rfmp_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 8, $simul_id, $rfmp_prec, $recency1,$recency2,$recency3,$recency4,$recency5, $rfm1, $rfm2, $rfm3,DEFAULT)");
		}
	  }

	for($i=0;$i<$n_area;$i++){
		$id_area = $_POST['checkarea'][$i];
		mysqli_query($conn,"INSERT INTO _area_used VALUES(DEFAULT, $id_area, $simul_id)");
	}

	header('Location: main.php?id='.$res['id']);
}

else if(isset($_POST['addRFMcomparison'])){
	$name=$_POST['nama'];
	$comparison=$_POST['comparison'];
	$startdate=$_POST['startdate'];
	$enddate=$_POST['enddate'];
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$rfm1=$_POST['rfm_filter1'];
	$rfm2=$_POST['rfm_filter2'];
	$rfm3=$_POST['rfm_filter3'];
	$recency1=$_POST['recency1'];
	$recency2=$_POST['recency2'];
	$recency3=$_POST['recency3'];
	$recency4=$_POST['recency4'];
	$recency5=$_POST['recency5'];
	$rfmd_prec = $_POST['rfmd_prec'];
	$rfmp_prec = $_POST['rfmp_prec'];

	if($rfm1 == "" || $rfm2 == "" || $rfm3 == ""){
		$rfm1=0;
		$rfm2=0;
		$rfm3=0;
		$method="RFM Comparison";
	}
	else{
		$method="Modified RFM Comparison";
	}

	mysqli_query($conn,"INSERT INTO _simulation VALUES(DEFAULT,'$name','$method', '$startdate', '$enddate', '$starttime', '$endtime', 1, $comparison,null)");
	$cek = true;

	$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM _simulation ORDER BY id DESC LIMIT 1"));
	$simul_id=$res['id'];


	$n=count($_POST['checkbox']);

	for($i=0;	$i<$n;	$i++) {
	    if($_POST['checkbox'][$i]=="rating"){
	    	$rat_prec = $_POST['ratingprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 1, $simul_id, $rat_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_trip"){
	    	$ttrip_prec = $_POST['total_tripprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 2, $simul_id, $ttrip_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="cancellation_rate"){
			$cancel_prec =  $_POST['cancellation_rateprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 3, $simul_id, $cancel_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_distance"){
			$tdist_prec =  $_POST['total_distanceprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 4, $simul_id, $tdist_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="rfm_driver"){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 5, $simul_id, $rfmd_prec, $recency1,$recency2,$recency3,$recency4,$recency5, $rfm1, $rfm2, $rfm3, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="distance"){
			$dis_prec =  $_POST['distanceprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 6, $simul_id, $dis_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="duration"){
			$dur_prec =  $_POST['durationprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 7, $simul_id, $dur_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="rfm_pass"){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 8, $simul_id, $rfmp_prec, $recency1,$recency2,$recency3,$recency4,$recency5, $rfm1, $rfm2, $rfm3, DEFAULT)");
		}
	  }

	$sql = mysqli_query($conn, "SELECT * FROM _area_used where id_simulation=$comparison");

	while ($row = $sql->fetch_assoc()){
		$id_area = $row['id_area'];
		mysqli_query($conn,"INSERT INTO _area_used VALUES(DEFAULT, $id_area, $simul_id)");
	}

	header('Location: main.php?id='.$res['id']);
}

else if(isset($_POST['addComparison'])){
	$name=$_POST['nama'];
	$comparison=$_POST['comparison'];
	$startdate=$_POST['startdate'];
	$enddate=$_POST['enddate'];
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$method=$_POST['method'];

	mysqli_query($conn,"INSERT INTO _simulation VALUES(DEFAULT,'$name','$method', '$startdate', '$enddate', '$starttime', '$endtime', 1, $comparison,null)");
	$cek = true;

	$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM _simulation ORDER BY id DESC LIMIT 1"));
	$simul_id=$res['id'];


	$n=count($_POST['checkbox']);
	for($i=0;	$i<$n;	$i++) {
	    if($_POST['checkbox'][$i]=="rating"){
	    	$rat_prec = $_POST['ratingprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 1, $simul_id, $rat_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_trip"){
	    	$ttrip_prec = $_POST['total_tripprec'];
	    	$goal = $_POST['goal_totaltrip'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 2, $simul_id, $ttrip_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,'$goal')");
		}
		else if($_POST['checkbox'][$i]=="cancellation_rate"){
			$cancel_prec =  $_POST['cancellation_rateprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 3, $simul_id, $cancel_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_distance"){
			$tdist_prec =  $_POST['total_distanceprec'];
			$goal = $_POST['goal_totaldist'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 4, $simul_id, $tdist_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,'$goal')");
		}
		else if($_POST['checkbox'][$i]=="distance"){
			$dis_prec =  $_POST['distanceprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 6, $simul_id, $dis_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="duration"){
			$dur_prec =  $_POST['durationprec'];
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 7, $simul_id, $dur_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
	  }

	$sql = mysqli_query($conn, "SELECT * FROM _area_used where id_simulation=$comparison");

	while ($row = $sql->fetch_assoc()){
		$id_area = $row['id_area'];
		mysqli_query($conn,"INSERT INTO _area_used VALUES(DEFAULT, $id_area, $simul_id)");
	}

	header('Location: main.php?id='.$res['id']);
}

else if(isset($_POST['addsimulation2'])){
	$name=$_POST['nama'];
	$method=$_POST['method'];
	$startdate=$_POST['startdate'];
	$enddate=$_POST['enddate'];
	$starttime=$_POST['starttime'];
	$endtime=$_POST['endtime'];
	$rat_prec = $_POST['rat_prec'];
	$dur_prec = $_POST['dur_prec'];
	$dis_prec = $_POST['dis_prec'];
	$ttrip_prec = $_POST['ttrip_prec'];
	$cancel_prec = $_POST['cancel_prec'];
	$tdist_prec = $_POST['tdist_prec'];
	$goal_totaltrip = $_POST['goal_totaltrip'];
	$goal_totaldist = $_POST['goal_totaldist'];

	mysqli_query($conn,"INSERT INTO _simulation VALUES(DEFAULT,'$name','$method', '$startdate', '$enddate', '$starttime', '$endtime', 1, 0,null)");
	$cek = true;

	$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM _simulation ORDER BY id DESC LIMIT 1"));
	$simul_id=$res['id'];


	$n=count($_POST['checkbox']);
	$n_area = count($_POST['checkarea']);

	for($i=0;$i<$n;	$i++) {
	    if($_POST['checkbox'][$i]=="rating" && $_POST['rat_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 1, $simul_id, $rat_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_trip" && $_POST['ttrip_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 2, $simul_id, $ttrip_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT, '$goal_totaltrip')");
		}
		else if($_POST['checkbox'][$i]=="cancellation_rate" && $_POST['cancel_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 3, $simul_id, $cancel_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="total_distance" && $_POST['tdist_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 4, $simul_id, $tdist_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,'$goal_totaldist')");
		}
		else if($_POST['checkbox'][$i]=="distance" && $_POST['dis_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 6, $simul_id, $dis_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
		else if($_POST['checkbox'][$i]=="duration" && $_POST['dur_prec']!=""){
			mysqli_query($conn,"INSERT INTO _factor_used VALUES(DEFAULT, 7, $simul_id, $dur_prec, DEFAULT, DEFAULT, DEFAULT, DEFAULT, DEFAULT,  DEFAULT, DEFAULT, DEFAULT,DEFAULT)");
		}
	  }

	for($i=0;$i<$n_area;$i++){
		$id_area = $_POST['checkarea'][$i];
		mysqli_query($conn,"INSERT INTO _area_used VALUES(DEFAULT, $id_area, $simul_id)");
	}

	header('Location: main.php?id='.$res['id']);
}
else if(isset($_POST['addarea'])){
	$upper_lat=$_POST['upper_lat'];
	$upper_long=$_POST['upper_long'];
	$bottom_lat=$_POST['bottom_lat'];
	$bottom_long=$_POST['bottom_long'];
	$type=$_POST['type'];

	mysqli_query($conn,"INSERT INTO _area VALUES(DEFAULT,$upper_lat,$upper_long, $bottom_lat, $bottom_long, $type, 1)");
	header('Location: add_area.php?stat=1');
}
?>