<?php 	 
session_start();
$_SESSION['page'] = 'pembuktian';
include 'nav.php';
$id = $_GET['id'];
$simul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM _simulation WHERE id =".$_GET['id']));

?>

<div style="margin: 5%" id="pembuktian">
	<h1 style="text-align:center;">Pembuktian Filtered FM</h1><br>
		<h2 style="text-align:center;"><?=$simul['id'].' - '.$simul['simulation_name'];?></h2>	
	<div class="text-center">
	<?php
	echo '<button class="btn btn-warning"><a style="color: black" onclick="location.href=\'pembuktian_rfm.php?id='.$id.'\'">Perhitungan RFM</a></button>'
	?>
	</div>
	<div class="content">
		<div id="Kategori">
			<?php
				$factor = mysqli_query($conn, "SELECT rfm1, rfm2, rfm3 FROM _factor_used WHERE id_simulation = ". $id ." ORDER by rfm1 DESC LIMIT 1");
				while($row = mysqli_fetch_assoc($factor)){
					echo'<p style="font-size=16">Kategori 1 : <b>'. $row['rfm1'] . ' hari </b><br>';
					echo'Kategori 2 : <b>'. $row['rfm2'] . ' hari</b><br>';
					echo'Kategori 3 : <b>'. $row['rfm3'] . ' hari</b>';
					echo '<script>console.log('. $row['rfm1'] .'); </script>';
				}
			?>
		</div>
		<br>
		<p style="font-size=16">Perhitungan Frequency dilakukan dengan memfilter data sesuai kategori di atas, lalu menjumlahkan data frequency dengan rumus: <br>
		<b>Frequency Total = (Frequency1*10%)+(Frequency2*30%)+(Frequency3*60%)</b></p>
		<br>
		<p style="font-size=16">Perhitungan Monetary dilakukan dengan memfilter data sesuai kategori di atas, lalu menjumlahkan data monetary dengan rumus: <br>
		<b>Monetary Total = (Monetary1*10%)+(Monetary2*30%)+(Monetary3*60%)</b></p>
		<br>
		
		<div class="row">
				<div class="col-md-6">
					<h4><b>Tabel Perhitungan Frequency Passenger</b></h4>
					<div class="table-responsive" id="tabel" style="overflow-x: auto;">
						<table class="table table-hover table-light">
							<thead>
								<tr>
									<th scope="col" width="5%">#</th>
									<th scope="col" width="10%">ID Passenger</th>
									<th scope="col" width="15%">Frequency 1</th>
									<th scope="col" width="15%">Frequency 2</th>
									<th scope="col" width="15%">Frequency 3</th>
									<th scope="col" width="10%">Hasil Frequency</th>
									<th scope="col" width="10%">Skor Frequency</th>
								</tr>
							</thead>
							<tbody id="show1">
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-6">
					<h4><b>Tabel Perhitungan Frequency Driver</b></h4>
					<div class="table-responsive" id="tabel" style="overflow-x: auto;">
						<table class="table table-hover table-light">
							<thead>
								<tr>
									<th scope="col" width="5%">#</th>
									<th scope="col" width="10%">ID Driver</th>
									<th scope="col" width="15%">Frequency 1</th>
									<th scope="col" width="15%">Frequency 2</th>
									<th scope="col" width="15%">Frequency 3</th>
									<th scope="col" width="10%">Hasil Frequency</th>
									<th scope="col" width="10%">Skor Frequency</th>
								</tr>
							</thead>
							<tbody id="show2">
							</tbody>
						</table>
					</div>
				</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-6">
				<h4><b>Tabel Perhitungan Quantile Passenger</b></h4>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="5%">Quantile</th>
								<th scope="col" width="5%">Frequency</th>
								<th scope="col" width="10%">Monetary</th>
							</tr>
						</thead>
						<tbody id="show3">
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<h4><b>Tabel Perhitungan Quantile Driver</b></h4><br>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="5%">Quantile</th>
								<th scope="col" width="5%">Frequency</th>
								<th scope="col" width="10%">Monetary</th>
							</tr>
						</thead>
						<tbody id="show4">
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<br>
		<div class="row">
				<div class="col-md-6">
					<h4><b>Tabel Perhitungan Monetary Passenger</b></h4>
					<div class="table-responsive" id="tabel" style="overflow-x: auto;">
						<table class="table table-hover table-light">
							<thead>
								<tr>
									<th scope="col" width="5%">#</th>
									<th scope="col" width="10%">ID Passenger</th>
									<th scope="col" width="15%">Monetary 1</th>
									<th scope="col" width="15%">Monetary 2</th>
									<th scope="col" width="15%">Monetary 3</th>
									<th scope="col" width="10%">Hasil Monetary</th>
									<th scope="col" width="10%">Skor Monetary</th>
								</tr>
							</thead>
							<tbody id="show5">
							</tbody>
						</table>
				</div>
			</div>
		<br>
			<div class="col-md-6">
				<h4><b>Tabel Perhitungan Monetary Driver</b></h4>
				<div class="table-responsive" id="tabel" style="overflow-x: auto;">
					<table class="table table-hover table-light">
						<thead>
							<tr>
								<th scope="col" width="5%">#</th>
								<th scope="col" width="10%">ID Driver</th>
								<th scope="col" width="15%">Monetary 1</th>
								<th scope="col" width="15%">Monetary 2</th>
								<th scope="col" width="15%">Monetary 3</th>
								<th scope="col" width="10%">Hasil Monetary</th>
								<th scope="col" width="10%">Skor Monetary</th>
							</tr>
						</thead>
						<tbody id="show6">
						</tbody>
					</table>
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
		showdataFreqp(id);
		showdataFreqd(id);
		showdataQuantileP(id);
		showdataQuantileD(id);
		showdataMonetP(id);
		showdataMonetD(id);
	});
	function showdataFreqp(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getFreqP : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show1').html(show);
			}
		});
	}
	function showdataFreqd(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getFreqD : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show2').html(show);
			}
		});
	}
	function showdataQuantileP(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getQuantileP : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show3').html(show);
			}
		});
	}
	function showdataQuantileD(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getQuantileD : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show4').html(show);
			}
		});
	}
	function showdataMonetP(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getMonetP : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show5').html(show);
			}
		});
	}
	function showdataMonetD(id) {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getMonetD : 1,
				simulasi : id
			},
			success : function(show) {
				$('#show6').html(show);
			}
		});
	}
</script>
</body>
</html>