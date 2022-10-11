<?php 	 
session_start();
$_SESSION['page'] = 'pembuktian';
include 'nav.php';
$id = $_GET['id'];
$simul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM _simulation WHERE id =".$_GET['id']));

$rfm = FALSE
?>

<div style="margin: 5%" id="pembuktian">
	<button class="btn btn-warning" onclick="location.href='simulasi.php'"><i class="fas fa-arrow-left"></i></button>	
	<h1 style="text-align:center;">Pembuktian RFM</h1>	<br>
		<h2 style="text-align:center;"><?=$simul['id'].' - '.$simul['simulation_name'];?></h2>

	<div class="text-center">
		<?php
		$factor = mysqli_query($conn, "SELECT rfm1 FROM _factor_used WHERE id_simulation = ".$id);
		while($row = mysqli_fetch_assoc($factor)){
			if(!is_null($row['rfm1']) and ($row['rfm1']!=0)){
				$rfm=TRUE;
			}
			else{
				$rfm=FALSE;
			}
		}
		echo '<script>console.log('. $rfm .'); </script>';
		if($rfm){
		echo'<button class="btn btn-warning"><a style="color: black" onclick="location.href=\'pembuktian_modif.php?id='.$id.'\'">Perhitungan Modifikasi</a></button>';
		}
		?>
	</div>
	<br><br>
		<div class="content">
			<div class="row">
				<div class="col-md-6">
				<h4><b>Tabel Passenger</b></h4>
					<div class="table-responsive" id="tabel" style="overflow-x: auto;">
						<table class="table table-hover table-light">
							<thead>
								<tr>
									<th scope="col" width="5%">#</th>
									<th scope="col" width="5%">ID Passenger</th>
									<th scope="col" width="10%">Recency</th>
									<th scope="col" width="10%">Frequency</th>
									<th scope="col" width="10%">Monetary</th>
									<th scope="col" width="10%">R Score</th>
									<th scope="col" width="10%">F Score</th>
									<th scope="col" width="10%">M Score</th>
									<th scope="col" width="10%">RFM Score</th>
								</tr>
							</thead>
							<tbody id="showdatapass">
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-6">
					<h4><b>Tabel Driver</b></h4>
					<div class="table-responsive" id="tabel" style="overflow-x: auto;">
						<table class="table table-hover table-light">
							<thead>
								<tr>
									<th scope="col" width="5%">#</th>
									<th scope="col" width="5%">ID Driver</th>
									<th scope="col" width="10%">Recency</th>
									<th scope="col" width="10%">Frequency</th>
									<th scope="col" width="10%">Monetary</th>
									<th scope="col" width="10%">R Score</th>
									<th scope="col" width="10%">F Score</th>
									<th scope="col" width="10%">M Score</th>
									<th scope="col" width="10%">RFM Score</th>
								</tr>
							</thead>
							<tbody id="showdatadriver">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	 
include 'footer.php';
?>
<script type="text/javascript">
	$( document ).ready(function() {
		var id =<?=$_GET['id'];?>;
		showdataPass(id);
		showdataDriver(id);
	});
	function showdataPass(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getRFMDataPass : 1,
				simulasi : id
			},
			success : function(show) {
				$('#showdatapass').html(show);
			}
		});
	}
	function showdataDriver(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getRFMDataDriver : 1,
				simulasi : id
			},
			success : function(show) {
				$('#showdatadriver').html(show);
			}
		});
	}
	// function cari(){
	// 	var text = $("#search").val();
	// 	$.ajax({
	// 		url: "view.php",
	// 		type: "POST",
	// 		async:false,
	// 		data:{
	// 			nama:text,
	// 			cariDriver:1
	// 		},
	// 		success: function(show){
	// 			$('#showdata').html(show);
	// 		}
	// 	});

	// 	if(text == ''){
	// 		showdata();
	// 	}
	// }
</script>
</body>
</html>