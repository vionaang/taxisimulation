<?php  
include 'connect.php';

$id = $_GET['id'];

if(isset($_POST['getState'])){
    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM _simulation WHERE id = ".$_POST['id']));
    if($sql['status'] == 1 ) $status = 'on';
    else $status = 'off';

    echo $status;

    exit();
}

if(isset($_POST['setStatus'])){
    if($_POST['state'] == 'on'){
        $status = 1;
    }
    else $status = 0;

    echo $status;
    $sql = (mysqli_query($conn, "UPDATE _simulation SET status = $status WHERE id = ".$_POST['id']));
    
    exit();
}

include 'header.php';
?>
<html>
<head>
    <title>Simple Map</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>

    <style type="text/css">
        #map {
            height: 100%;
            width: 100%;
            position: fixed;
            top: 0;
            bottom: 0;
        }
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .on{
            background-color: #00cc88;
        }
        .off{
            background-color: #ff2244;
        }
        .status{
            color: white;
            right: 0;
            position: absolute;
            padding: 5px 50px;
            font-weight: bold;
        }
        .content{
            width: 100%;
            padding: 0;
            margin: 0 auto;
            top: 10%;
            position: absolute;
        }
        p{
            font-size: 16px;
        }
    </style>
</head>
<body>
    <input type="hidden" name="id" id="id" value="<?= $id; ?>">
    <main class="container-fluid d-flex h-100 flex-column">
        <div class="row flex-grow-1">
            <div class="col-md-9">
                <div style="overflow: hidden, position: fixed;"id="map"></div>
            </div>
            <div class="col-md-3" style="overflow-x: hidden;overflow-y: auto;">
                <br>
                <span class="status" id="status" style="background-color: #00cc88;">ON</span>
                <span class="content">
                    <div class="row">
                        <div class="col-md-8">
                            <span id="info"></span>
                        </div>
                        <div class="col-md-4">
                            <div style="background-color: #ffda01; padding: 10px; margin-top: 40%; margin-right: 5%">Batch <span id="batch"></span></div>
                        </div>
                    </div>
                    <!-- <br> -->
                    <h5>Assignment Table</h5>
                    <div class="table-responsive" id="tabel" style="overflow-x: auto;">
                        <table class="table table-hover table-light">
                            <thead>
                                <tr>
                                    <th scope="col" width="5%">#</th>
                                    <th scope="col" width="25%">ID Driver</th>
                                    <th scope="col" width="25%">ID Pass</th>
                                    <th scope="col" width="30%">Status</th>
                                </tr>
                            </thead>
                            <tbody id="assignment">
                            </tbody>
                        </table>
                    </div><br>
                    <h5>Online Driver</h5>
                    <div class="table-responsive" id="tabel" style="overflow-x: auto;">
                        <table class="table table-hover table-light">
                            <thead>
                                <tr>
                                    <th scope="col" width="25%">Batch</th>
                                    <th scope="col" width="25%">ID Driver</th>
                                    <th scope="col" width="30%">Name</th>
                                    <th scope="col" width="20%">RFM Score</th>
                                </tr>
                            </thead>
                            <tbody id="gendriver">
                            </tbody>
                        </table>
                    </div><br>
                    <h5>Online Passenger</h5>
                    <div class="table-responsive" id="tabel" style="overflow-x: auto; margin-bottom: 20%">
                        <table class="table table-hover table-light">
                            <thead>
                                <tr>
                                    <th scope="col" width="25%">Batch</th>
                                    <th scope="col" width="25%">ID Passenger</th>
                                    <th scope="col" width="30%">Name</th>
                                    <th scope="col" width="20%">RFM Score</th>
                                </tr>
                            </thead>
                            <tbody id="genpassenger">
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="row" style="position: fixed; margin-left: 3%; bottom: 5%;">
                        <div class="col-md-4">
                            <button class="btn btn-primary" style="width: 110%" onclick="prev()">Prev</button>
                        </div>
                        <div class="col-md-4">
                            <center><button class="btn btn-warning" id = "button_play" style="width: 90%" onclick="buttonPlayPress()"><i class="fa fa-play" id="playpause"></i></span></button></center>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" style="width: 110%" onclick="next()">Next</button>
                        </div>
                    </div> -->
                </span>
                <button onClick="End()" class="btn btn-warning">End Simulation</button>
                <!-- <button class="btn btn-warning" onclick="End()">End Simulation</a></button> -->
            </div>
        </div>     
    </main>

    <script type="text/javascript" src="simulation.js"></script>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAcry1zS7lnCeHzlBKYGXaaasfSHhvfRCc&callback=initMap&libraries=&v=weekly" async></script>