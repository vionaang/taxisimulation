<?php
require('library/php-excel-reader/excel_reader2.php');
require('library/SpreadsheetReader.php');
require('connect.php');

if(isset($_POST["submit_file_driver"])){
	$mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet'];
	if(in_array($_FILES["file"]["type"],$mimes)){
		$uploadFilePath = 'uploads/'.basename($_FILES['file']['tmp_name']);
		move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

		$Reader = new SpreadsheetReader($uploadFilePath);
		$totalSheet = count($Reader->sheets());

		echo "You have total ".$totalSheet." sheets";
		
		for($i=0;$i<$totalSheet;$i++){
			$Reader->ChangeSheet($i);
			$i = 0;
			foreach ($Reader as $Row){
				if($i!=0){
					var_dump($Row);
					$name = $Row[0];
					$rating = (float)$Row[1];
					$total_trip = (int)$Row[2];
					$cancel_rate = (float)$Row[3];
					$total_dist = (float)$Row[4];
					$rfm_score =  (int)$Row[5];
					$status =  (int)$Row[6];
					$is_active = (int)$Row[7];
					
					// -- mysqli_query($conn,"INSERT INTO _driver VALUES (DEFAULT,'$name',$rating,$total_trip, $cancel_rate, $total_dist,$status,$is_active,$rfm_score)");

				}
				$i++;
			}
		}
		// header('Location: manage_driver.php?stat=3');
	}
	else { 
		die("<br/>Sorry, File type is not allowed. Only Excel file."); 
	}
}
else if(isset($_POST["submit_file_passenger"])){
	$mimes = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.oasis.opendocument.spreadsheet'];
	if(in_array($_FILES["file"]["type"],$mimes)){
		$uploadFilePath = 'uploads/'.basename($_FILES['file']['tmp_name']);
		move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);

		$Reader = new SpreadsheetReader($uploadFilePath);
		$totalSheet = count($Reader->sheets());

		// echo "You have total ".$totalSheet." sheets";
		
		for($i=0;$i<$totalSheet;$i++){
			$Reader->ChangeSheet($i);
			$i = 0;
			foreach ($Reader as $Row){
				if($i!=0){
					$name = $Row[0];
					$rfm_score =  (int)$Row[1];
					$status =  (int)$Row[2];
					$is_active = (int)$Row[3];
					
					mysqli_query($conn,"INSERT INTO _passenger VALUES (DEFAULT,'$name',$rfm_score,$status,$is_active)");
				}
				$i++;
			}
		}
		header('Location: manage_passenger.php?stat=3');
	}
	else { 
		die("<br/>Sorry, File type is not allowed. Only Excel file."); 
	}
}
?>