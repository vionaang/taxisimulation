<?php 	 
include 'connect.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>SIMULATION</title>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<!-- ICONS -->
	<link href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" rel="stylesheet">

	<!-- EXPORT EXCEL -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/TableExport/3.2.5/css/tableexport.min.css">

	<style type="text/css">
		html,body{
			overflow-x: hidden;
			background-color: #F5E2C8;
		}
		nav{
			background-color: #18206F;

		}

		/*FONT*/
		@font-face {          
			src: url('fonts/Montserrat-Bold.otf');
			font-family: MonserratBold;
		}
		@font-face {          
			src: url('fonts/Montserrat-Regular.otf');
			font-family: MonserratRegular;
		}
		@font-face {          
			src: url('fonts/Poppins-Bold.ttf');
			font-family: PoppinsBold;
		}
		@font-face {          
			src: url('fonts/Poppins-ExtraBold.ttf');
			font-family: PoppinsExtraBold;
		}
		@font-face {          
			src: url('fonts/Poppins-Bold.ttf');
			font-family: PoppinsBold;
		}
		@font-face {          
			src: url('fonts/Poppins-SemiBold.ttf');
			font-family: PoppinsSemiBold;
		}
		@font-face {          
			src: url('fonts/Poppins-Italic.ttf');
			font-family: PoppinsItalic;
		}
		@font-face {          
			src: url('fonts/Poppins-Light.ttf');
			font-family: PoppinsLight;
		}
		@font-face {          
			src: url('fonts/Poppins-LightItalic.ttf');
			font-family: PoppinsLightItalic;
		}
		@font-face {          
			src: url('fonts/Poppins-Medium.ttf');
			font-family: PoppinsMedium;
		}
		@font-face {          
			src: url('fonts/Poppins-Regular.ttf');
			font-family: PoppinsRegular;
		}

		.navbar-light .navbar-nav .nav-link{
			font-family: 'PoppinsMedium';
			color: rgba(255,255,255,0.5);
		}
		.navbar-light .navbar-nav .nav-link:hover{
			color: rgba(255,255,255,1);
		}
		.navbar-light .navbar-nav .active>.nav-link, .navbar-light .navbar-nav .nav-link.active, .navbar-light .navbar-nav .nav-link.show, .navbar-light .navbar-nav .show>.nav-link{
			color: rgba(255,255,255,1);
		}

		h1,h2,h3,h4,h5{
			font-family: 'PoppinsSemiBold';
		}
		label{
			font-family: 'PoppinsRegular';
		}

		#managedriverpage a, #managepasspage a, #manageareapage a{
			color: white;
		}
		.form{
			background-color: white;
			border-radius: 25px;
			padding: 5%;
		}

		.xls{
			margin: auto;
			background-color: white;
		}


		/*SEARCH BAR*/
		.searchbar{
			width: 250px;
			background-color: white;
			border:none;
			opacity: 0.7;
		}

		.searchbar:hover{
			opacity: 1;
		}

		.search_icon{
			height: 40px;
			width: 40px;
			float: right;
			display: flex;
			justify-content: center;
			align-items: center;
			border-radius: 50%;
			text-decoration:none;
			right: 3px !important;
			left: auto !important;
		}

		.wrapper {
			display: flex;
			min-width: 100px;
		}

		.header-patient{
			position: absolute;
			right:0;
		}
		.content{
			position: relative;
		}

		.table{
			border-radius: 20px;
			border-collapse: collapse;
			border-style: hidden;
			border:none !important;
		}
	</style>
</head>
<body>