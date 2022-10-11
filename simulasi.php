<?php 	 
session_start();
include 'connect.php';
$_SESSION['page'] = 'simulasi';

if(isset($_POST['getData'])){
	$sql = mysqli_query($conn, "SELECT * FROM _simulation ORDER BY id DESC");

	$i = 1;
	$factor_used = '';
	$rfm = FALSE;
	while ($row=mysqli_fetch_assoc($sql)) {
		$factor = mysqli_query($conn, "SELECT * FROM _factor f JOIN _factor_used fu ON f.id = fu.id_factor WHERE fu.id_simulation = ".$row['id']);
		$factor_used = '';
		while($row2 = mysqli_fetch_assoc($factor)){
			$factor_used = $factor_used.$row2['name'].'<br>';
			if(!is_null($row2['recency1'])){
				$rfm=TRUE;
			}
			else{
				$rfm=FALSE;
			}
		}

		if($row['status'] == 0) $status = 'OFF';
		else $status = 'ON';

		$main = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_main_simulation as id FROM _simulation WHERE id = ".$row['id']));


		echo '
		<tr>
		<td>'.$i++.'</td>
		<td>'.$row['simulation_name'].'</td>
		<td>'.$row['method'].'</td>
		<td>'.$factor_used.'</td>
		<td><i class="fa fa-calendar"></i> '.$row['start_date'].'<br><i class="fa fa-clock"></i> '.$row['start_hour'].'</td>
		<td><i class="fa fa-calendar"></i> '.$row['end_date'].'<br><i class="fa fa-clock"></i> '.$row['end_hour'].'</td>
		<td>'.$status.'</td>
		<td>'.$row['id_main_simulation'].'</td>
		<td><a style="color: white" target="_blank" href=\'history.php?id='.$row['id'].'&&id_main='.$main['id'].'\'><button class="btn btn-success"><i class="fas fa-history"></i></button></a>';
		if($row['method'] != 'Random Assignment') echo '
		<a style="color: white" target="_blank" href=\'pembuktian.php?id='.$row['id'].'\'><button class="btn btn-warning"><i class="fas fa-calculator"></i></button></a> ';

		if($rfm){
			echo '<button class="btn btn-success" onclick="location.href=\'pembuktian_rfm.php?id='.$row['id'].'\'"><i class="fas fa-users"></i></button>&nbsp;';
		}
		echo '
		<button class="btn btn-danger" onclick="deletedata('.$row['id'].');"><i class="far fa-trash-alt"></i></button>';
		echo '</td></tr>';
	}
	exit();
}

include 'nav.php';
?>

<div style="margin: 5%" id="simulasi">
	<p style="font-weight: bold;">Daftar Simulasi</p>
	<div class="table-responsive" id="tabel" style="overflow-x: auto;">
		<table class="table table-hover table-light">
			<thead><tr id = "factor_header">
				<th scope="col" width="5%">#</th>
				<th scope="col" width="10%">Name</th>
				<th scope="col" width="15%">Method</th>
				<th scope="col" width="10%">Factor Used</th>
				<th scope="col" width="10%">Start Date</th>
				<th scope="col" width="10%">End Date</th>
				<th scope="col" width="5%">Status</th>
				<th scope="col" width="5%">Id Comparison</th>
				<th scope="col" width="20%">Action</th>
			</tr></thead>
			<tbody id="showdata">
			</tbody>
		</table>
	</div>
</div>
<?php 	 
include 'footer.php';
?>
<script type="text/javascript">
	$( document ).ready(function() {
		loadData();
	});

	function loadData() {
		$.ajax({
			url     : "simulasi.php",
			type    : "POST",
			async   : false,
			data    : {
				getData : 1,
			},
			success : function(show) {
				$('#showdata').html(show);
			}
		});
	}
	function deletedata(v_id) {
		if (confirm('Are You Sure?')){
			$.ajax({
				url     : "delete.php",
				type    : "POST",
				async   : false,
				data    : {
					deletesimulation : 1,
					id : v_id
				},
				success : function(result) {
					alert(result);
					loadData();
				}
			});
		}
	}
</script>
</body>
</html>