	<?php 	 
	session_start();
	$_SESSION['page'] = 'managepass';
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
	<div class="container" id="managepasspage" style="margin-top: 5%">
		<div class="content">
			<div class="header-patient">
				<form method="POST">
					<div class="wrapper">
						<button class="btn btn-primary" style="margin-right: 5%; width: 100%"><a href="add_passenger.php">Add Passenger</a></button>
						<input class="form-control searchbar"  type="text" name="search" id="search" onkeyup="cari()" placeholder="Search passenger...">
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
						<input name="submit_file_passenger" class="btn btn-primary" type="submit" value="Submit">
					</div>
				</div>
			</form>
			<br>
			<div class="table-responsive" id="tabel" style="overflow-x: auto;">
				<table class="table table-hover table-light">
					<thead>
						<tr>
							<th scope="col" width="5%">#</th>
							<th scope="col" width="20%">ID Passenger</th>
							<th scope="col" width="25%">Name</th>
							<th scope="col" width="10%">RFM Score</th>
							<th scope="col" width="10%">Status</th>
							<th scope="col" width="10%">Is Active</th>
							<th scope="col" width="25%">Action</th>
							<!-- <th scope="col" width="10%">Delete</th> -->
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
							<h5 class="modal-title" id="exampleModalLabel">Data Passenger</h5>
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
					getPassenger : 1,
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
					cariPassenger:1
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
				url     : "editpassenger.php",
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
						deletepassenger : 1,
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
			var status=$('#status').val();
			var rfm_score=$('#rfm_score').val();

			if(nama!=''&&status!=''&&rfm_score!=''){
				$.ajax({
					url     : "edit.php",
					type    : "POST",
					async   : false,
					data    : {
						editpassenger : 1,
						id : id,
						nama:nama,
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
			fileName: "Passenger"+"_"+today,  // (id, String), filename for the downloaded file
			bootstrap: true,                   // (Boolean), style buttons using bootstrap
			position: "well" ,                // (top, bottom), position of the caption element relative to table
			ignoreRows: null,                  // (Number, Number[]), row indices to exclude from the exported file
			ignoreCols: [6,7],                 // (Number, Number[]), column indices to exclude from the exported file
			ignoreCSS: ".tableexport-ignore"   // (selector, selector[]), selector(s) to exclude from the exported file
		});
	}
</script>
</body>
</html>