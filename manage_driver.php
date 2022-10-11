<?php 	 
session_start();
$_SESSION['page'] = 'managedriver';
include 'nav.php';

if (isset($_GET['stat'])) {
	if ($_GET['stat'] == 1) {
		echo "<script>alert('Save changes successfully!');</script>";
	}
	else if($_GET['stat']==2){
		echo"<script>alert('Deleted successfully!');</script>";
	}
	else if($_GET['stat']==3){
		echo"<script>alert('Uploaded successfully!');</script>";
	}
}
?>
<div class="container" style="margin-top: 5%" id="managedriverpage">
	<div class="content">
		<div class="header-patient">
			<form method="POST">
				<div class="wrapper">
					<button class="btn btn-primary" style="margin-right: 5%; width: 100%"><a href="add_driver.php">Add Driver</a></button>
					<input class="form-control searchbar"  type="text" name="search" id="search" onkeyup="cari()" placeholder="Search driver...">
					<i class="fas fa-search search_icon" style="position: absolute;"></i>
				</div>
			</form>
		</div>
		<br><br>
 		<form method="post" action="import.php" enctype="multipart/form-data">
			<label>Pilih File Excel*:</label>
			<div class="row">
				<div class="col-md-6">
					<input name="file" class="form-control" type="file"> 
				</div>
				<div class="col-md-6">
					<input name="submit_file_driver" class="btn btn-primary" type="submit" value="Submit">
				</div>
			</div>
		</form>
		<br>
		<div class="table-responsive" id="tabel" style="overflow-x: auto;">
			<table class="table table-hover table-light">
				<thead>
					<tr>
						<th scope="col" width="5%">#</th>
						<th scope="col" width="5%">ID Driver</th>
						<th scope="col" width="15%">Name</th>
						<th scope="col" width="5%">Rating</th>
						<th scope="col" width="10%">Total Trip</th>
						<th scope="col" width="5%">Cancel Rate</th>
						<th scope="col" width="10%">Total Distance</th>
						<th scope="col" width="10%">RFM Score</th>
						<th scope="col" width="10%">Status</th>
						<th scope="col" width="10%">Is Active</th>
						<th scope="col" width="15%">Action</th>
					</tr>
				</thead>
				<tbody id="showdata">
				</tbody>
			</table>
		</div>

		<!-- EDIT -->
		<div class="modal fade bd-example-modal-lg" id="editmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document" style="overflow-y: initial;">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Data Driver</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="showmodal" style="height: 500px; overflow-y: auto;">
						<div id="showres2">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-tosca" data-dismiss="modal">Close</button>
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
		showdata();
		ExportTable();
	});
	function showdata() {
		$.ajax({
			url     : "view.php",
			type    : "POST",
			async   : false,
			data    : {
				getDriver : 1,
			},
			success : function(show) {
				$('#showdata').html(show);
				$('#search').val("");
			}
		});
	}
	function cari(){
		var text = $("#search").val();
		$.ajax({
			url: "view.php",
			type: "POST",
			async:false,
			data:{
				nama:text,
				cariDriver:1
			},
			success: function(show){
				$('#showdata').html(show);
			}
		});

		if(text == ''){
			showdata();
		}
	}
	function edit(v_id){
		$.ajax({
			url     : "editdriver.php",
			type    : "POST",
			async   : false,
			data    : {
				id : v_id
			},
			success : function(result) {
				$('#showres2').html(result);
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
					deletedoctor : 1,
					id : v_id
				},
				success : function(result) {
					alert(result);
					showdata();
				}
			});
		}
	}
	function saveedit(){
		var id = $('#id').val();
		var nama=$('#nama').val();
		var rating=$('#rating').val();
		var total_trip=$('#total_trip').val();
		var total_distance=$('#total_distance').val();
		var cancel_rate=$('#cancel_rate').val();
		var status=$('#status').val();
		var rfm_score=$('#rfm_score').val();

		if(nama!=''&&rating!=''&&total_trip!=''&&total_distance!=''&&cancel_rate!=''&&status!=''&&rfm_score!=''){
			$.ajax({
				url     : "edit.php",
				type    : "POST",
				async   : false,
				data    : {
					editdriver : 1,
					id : id,
					nama:nama,
					rating:rating,
					total_trip:total_trip,
					total_distance:total_distance,
					cancel_rate : cancel_rate,
					status : status,
					rfm_score : rfm_score
				},
				success : function(show) {
					alert(show);
					showdata();
					$('#editmodal').hide(); 
					$('body').removeClass('modal-open');
					$('body').css('padding-right', '0px');
					$('.modal-backdrop').remove();
				}
			});
		}
		else{
			alert("Data incomplete");
		}
	}
	function ExportTable(){
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();

		today = dd + '-' + mm + '-' + yyyy;
		$("#tabel").tableExport({
			headings: true,                    // (Boolean), display table headings (th/td elements) in the <thead>
			footers: true, 
			formats: ["xls"],    // (String[]), filetypes for the export
			fileName: "Driver"+"_"+today,  // (id, String), filename for the downloaded file
			bootstrap: true,                   // (Boolean), style buttons using bootstrap
			position: "well" ,                // (top, bottom), position of the caption element relative to table
			ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
			ignoreCols: [10],                 // (Number, Number[]), column indices to exclude from the exported file
			ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
		});
	}
</script>
</body>
</html>