let listArea = [];
let driverMarkers = [];
let passengerMarkers = [];
let originMarkers = [];
let destMarkers = [];
let routePickup = [];
let routeOTW = [];
let cekstatus
let map;
var id_simulation = $('#id').val();

var state = 'on';
var intervalInMs = 10000;
// console.log("refresh");
$( document ).ready(function() {
    getState();
    getInfo();
    getBatch();
    showAssignment();
    showOnlineDriver();
    showOnlinePassenger();
    setInterval(function() {
      getBatch();
      showAssignment();
      showOnlineDriver();
      showOnlinePassenger();
      update();
  }, intervalInMs);
});

function getState() {
    var id = $('#id').val();
    $.ajax({
        url     : "main.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getState: 1,
        },
        success : function(show) {
            state = show; 
            if(state == 'on'){
                $("#playpause").attr('class', "fa fa-pause"); 
                $("#status").attr('class', "status on");   
                $("#status").text('ON');   
            }
            else if(state=='off'){
                $("#playpause").attr('class', "fa fa-play"); 
                $("#status").attr('class', "status off");  
                $("#status").text('OFF');    
            }
        }
    });
}



function buttonPlayPress() {
    if(state == 'on'){
        state = 'off';
        $("#playpause").attr('class', "fa fa-play"); 
        $("#status").attr('class', "status off");   
        $("#status").text('OFF');   
    }
    else if(state=='off'){
        state = 'on';
        $("#playpause").attr('class', "fa fa-pause"); 
        $("#status").attr('class', "status on");  
        $("#status").text('ON');    
    }

    var id = $('#id').val();
    $.ajax({
        url     : "main.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            state: state,
            setStatus : 1
        },
        success : function(show) {
        }
    });
}

function prev(){
    var id = $('#id').val();
    getBatch();
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            prev : 1,
        },
        success : function(show) {
            update();
        }
    });
}

function next(){
    var id = $('#id').val();
    getBatch();
     $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            next : 1,
        },
        success : function(show) {
            alert(show);
            // if(show == 'isNew'){
            //     $.ajax({
            //       url: "system.py",
            //       context: document.body
            //     }).done(function() {
            //         update();
            //         // alert('finished python script');;
            //     });
            // } 
            // else update();
        }
    });
}

function showAssignment() {
    var id = $('#id').val();
    $.ajax({
        url     : "view.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getTableAssignment : 1,
        },
        success : function(show) {
            $('#assignment').html(show);
        }
    });
}

function showOnlineDriver() {
    var id = $('#id').val();
    $.ajax({
        url     : "view.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getTableOnlineDriver : 1,
        },
        success : function(show) {
            $('#gendriver').html(show);
        }
    });
}

function showOnlinePassenger() {
    var id = $('#id').val();
    $.ajax({
        url     : "view.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getTableOnlinePassenger : 1,
        },
        success : function(show) {
            $('#genpassenger').html(show);
        }
    });
}

function getInfo() {
    var id = $('#id').val();
    $.ajax({
        url     : "view.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getInfo : 1,
        },
        success : function(show) {
            $('#info').html(show);
        }
    });
}

function getBatch() {
    var id = $('#id').val();
    $.ajax({
        url     : "view.php",
        type    : "POST",
        async   : false,
        data    : {
            id:id,
            getBatch : 1,
        },
        success : function(show) {
            $('#batch').html(show);
        }
    });
}


// function run() {
    //console.log("CEK SIMULASI JALAN ATAU NGGA");
    // db.ref('event').once('value').then(function(snapshot) {
    //     var status = snapshot.child('status').val();
    //     if (status == 1) {
    //         cekstatus = true;
    //         removeAllArea();
    //         generateArea();
    //         setmarker();
    //         drawrouteorder();
    //         drawroutedriver();
    //     } else {
    //         cekstatus = false;
    //     }
    // });

    // console.log(cekstatus);


    // if (cekstatus == true) {
    //   removeAllArea();
    //   generateArea();
    //   setmarker();
    //   drawroute();
    // }

    // generateArea();
// }

function update(){
    finishPickup();
    finishOrder();
    setMarkerDriver();
    setMarkerPassenger();
    setMarkerOTW();
    drawRoutePickup();
    drawRouteOTW();
}

//DRAW MAPS
function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: {
            lat: -7.3550141,
            lng: 112.655943
        },
        zoom: 11.5,
    });
    console.log('tes');

    generateArea();
    finishPickup();
    finishOrder();
    setMarkerDriver();
    setMarkerPassenger();
    setMarkerOTW();
    drawRoutePickup();
    drawRouteOTW();
}

//DRAW AREA
function generateArea() {
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getArea : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);
            for (var i = 0; i < arrayObjects.length; i++) {

            //TYPE = 1 -> AREA PUSAT
            if(arrayObjects[i]['type'] == '1'){
                var area = new google.maps.Rectangle({
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 1,
                    fillColor: "#FF0000",
                    fillOpacity: 0.1,
                    map,
                    bounds: {
                        north: parseFloat(arrayObjects[i]['upper_lat']),
                        south: parseFloat(arrayObjects[i]['bottom_lat']),
                        west: parseFloat(arrayObjects[i]['upper_long']),
                        east: parseFloat(arrayObjects[i]['bottom_long'])
                    },
                });
                listArea.push(area);
            }
            //TYPE = 2 -> AREA PINGGIR
            else{
                var area = new google.maps.Rectangle({
                    strokeColor: "#0000FF",
                    strokeOpacity: 0.8,
                    strokeWeight: 1,
                    fillColor: "#0000FF",
                    fillOpacity: 0.1,
                    map,
                    bounds: {
                        north: parseFloat(arrayObjects[i]['upper_lat']),
                        south: parseFloat(arrayObjects[i]['bottom_lat']),
                        west: parseFloat(arrayObjects[i]['upper_long']),
                        east: parseFloat(arrayObjects[i]['bottom_long'])
                    },
                });
                listArea.push(area);
            }
        }
    }
});
}

//DRAW MARKER FOR ONLINE DRIVERS
function setMarkerDriver() {
    removeMarkers(0);

    var blueIcon = {
        url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png", // url
        scaledSize: new google.maps.Size(40, 40), // scaled size
    };
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getDriver : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);
            for (var i = 0; i < arrayObjects.length; i++) {
                var myLatLng = { lat: parseFloat(arrayObjects[i]['lat']), lng: parseFloat(arrayObjects[i]['lng']) }
                const marker = new google.maps.Marker({
                    position: myLatLng,
                    map,
                    label: { color: '#000000', fontWeight: 'bold', fontSize: '12px', text: 'D'+arrayObjects[i]['id_driver'] },
                    icon: blueIcon,
                    //scaledSize: new google.maps.Size(25, 25), // scaled size
                });
                driverMarkers.push(marker)
            }
        }
    });
}

//DRAW MARKER FOR ONLINE PASSENGERS
function setMarkerPassenger(){
    removeMarkers(1);

    var redIcon = {
        url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png", // url
        scaledSize: new google.maps.Size(40, 40), // scaled size
    };

    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getPassenger : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);

            for (var i = 0; i < arrayObjects.length; i++) {
                var myLatLng = { lat: parseFloat(arrayObjects[i]['lat_origin']), lng: parseFloat(arrayObjects[i]['lng_origin']) }
                const marker = new google.maps.Marker({
                    position: myLatLng,
                    map,
                    label: { color: '#000000', fontWeight: 'bold', fontSize: '12px', text: 'P'+arrayObjects[i]['id_passenger'] },
                    icon: redIcon
                    // scaledSize: new google.maps.Size(25, 25), // scaled size
                });
                passengerMarkers.push(marker)
            }
        }
    });
}

//DRAW MARKER FOR OTW ORIGIN POINT
function setMarkerOTW() {
    removeMarkers(2);

    var greenIcon = {
        url: "http://maps.google.com/mapfiles/ms/icons/green-dot.png", // url
        scaledSize: new google.maps.Size(40, 40), // scaled size
    };

    var yellowIcon = {
        url: "https://www.nicepng.com/png/full/253-2534170_clip-art-at-clker-com-vector-online-green.png",
        // url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png", // url
        scaledSize: new google.maps.Size(30, 30), // scaled size
    };
    
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getOTW : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);
            for (var i = 0; i < arrayObjects.length; i++) {
                var myLatLng = { lat: parseFloat(arrayObjects[i]['lat_origin']), lng: parseFloat(arrayObjects[i]['lng_origin']) }
                const marker = new google.maps.Marker({
                    position: myLatLng,
                    map,
                    label: { color: '#000000', fontWeight: 'bold', fontSize: '12px', text: 'D'+arrayObjects[i]['id_driver']+', '+'P'+arrayObjects[i]['id_passenger'] },
                    icon: yellowIcon,
                    // scaledSize: new google.maps.Size(150, 150), // scaled size
                });
                originMarkers.push(marker)

                var myLatLng2 = { lat: parseFloat(arrayObjects[i]['lat_destination']), lng: parseFloat(arrayObjects[i]['lng_destination']) }
                const marker2 = new google.maps.Marker({
                    position: myLatLng2,
                    map,
                    label: { color: '#000000', fontWeight: 'bold', fontSize: '12px', text: 'D'+arrayObjects[i]['id_driver']+', '+'P'+arrayObjects[i]['id_passenger'] },
                    icon: greenIcon
                    //scaledSize: new google.maps.Size(25, 25), // scaled size
                });
                destMarkers.push(marker2)
            }
        }
    });
}

function drawRoutePickup() {
    removeRoute(1);
    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer = new google.maps.DirectionsRenderer({
        polylineOptions: {
            strokeColor: "red"
        }
    });
    directionsRenderer.setMap(map);

    //const geocoder = new google.maps.Geocoder();

    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getPickup : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);
           // alert(show);
           for (var i = 0; i < arrayObjects.length; i++) {
            // print(arrayObjects[i]['id_driver'] +", "+arrayObjects[i]['id_passenger']);

            var point_origin = parseFloat(arrayObjects[i]['lat']) + ',' + parseFloat(arrayObjects[i]['lng']);
            var point_destination = parseFloat(arrayObjects[i]['lat_origin']) + ',' + parseFloat(arrayObjects[i]['lng_origin']);

            var request = {
                origin: point_origin,
                destination: point_destination,
                travelMode: 'DRIVING'
            };
                //directionsRenderer.setDirections(null);
                directionsService.route(request, function(result, status) {
                    if (status == 'OK') {
                        directionsDisplay = new google.maps.DirectionsRenderer({
                            polylineOptions: {
                                strokeColor: "red"
                            },
                            suppressBicyclingLayer: true,
                            suppressMarkers: true,
                          preserveViewport: true // don't zoom to fit the route
                      });
                        directionsDisplay.setMap(map);
                        directionsDisplay.setDirections(result);
                        routePickup.push(directionsDisplay);
                        // combine the bounds of the responses
                        // bounds.union(result.routes[0].bounds);
                        // zoom and center the map to show all the routes
                        // map.fitBounds(bounds);
                    } else {
                        console.log(status);
                    }
                });
            }
        }
    });
}

function drawRouteOTW() {
    removeRoute(2);

    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    //const geocoder = new google.maps.Geocoder();

    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            getOTW : 1,
        },
        success : function(show) {
            var arrayObjects = JSON.parse(show);

            for (var i = 0; i < arrayObjects.length; i++) {
                var point_origin = parseFloat(arrayObjects[i]['lat_origin']) + ',' + parseFloat(arrayObjects[i]['lng_origin']);
                var point_destination = parseFloat(arrayObjects[i]['lat_destination']) + ',' + parseFloat(arrayObjects[i]['lng_destination']);

                var request = {
                    origin: point_origin,
                    destination: point_destination,
                    travelMode: 'DRIVING'
                };
                //directionsRenderer.setDirections(null);
                directionsService.route(request, function(result, status) {
                    if (status == 'OK') {
                        directionsDisplay = new google.maps.DirectionsRenderer({
                            suppressBicyclingLayer: true,
                            suppressMarkers: true,
                          preserveViewport: true // don't zoom to fit the route
                      });
                        directionsDisplay.setMap(map);
                        directionsDisplay.setDirections(result);
                        routeOTW.push(directionsDisplay);
                        // combine the bounds of the responses
                        // bounds.union(result.routes[0].bounds);
                        // zoom and center the map to show all the routes
                        // map.fitBounds(bounds);
                    } else {
                        console.log(status);
                    }
                });
            }
        }
    });
}

function finishPickup() {
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            setFinishPickup : 1,
        },
        success : function(show) {
        }
    });
}

function finishOrder() {
    $.ajax({
        url     : "query.php",
        type    : "POST",
        async   : false,
        data    : {
            id_simulation: id_simulation,
            setFinishOrder : 1,
        },
        success : function(show) {
            console.log('Debug Objects: finish pickup');
        }
    });
}

function End() {
        $.ajax({
            url     : "query.php",
            type    : "POST",
            async   : false,
            data    : {
                id_simulation: id_simulation,
                endSimulation : 1,
            },
            success : function(show) {
                window.location.href = "history.php?id="+id_simulation;
                console.log('Debug Objects: End Simulation');
            }
        });
}

function removeAllArea() {
    rectangle.forEach((rectangle) => {
        rectangle.setMap(null);
        rectangle.setMap(null);

    });
    rectangle = [];
}

// Deletes all markers in the array by removing references to them.
function removeMarkers(kode) {
    if (kode == 0) {
        for (var i = 0; i < driverMarkers.length; i++)
            driverMarkers[i].setMap(null)
        driverMarkers = [];
    } else if (kode == 1) {
        for (var i = 0; i < passengerMarkers.length; i++)
            passengerMarkers[i].setMap(null)
        passengerMarkers = [];
    } else if (kode == 2) {
        for (var i = 0; i < originMarkers.length; i++){
            originMarkers[i].setMap(null)
            destMarkers[i].setMap(null)
        }
        originMarkers = [];
        destMarkers = [];
    } 
}

function removeRoute(kode){
    if (kode == 1) {
        for (var i = 0; i < routePickup.length; i++){
            routePickup[i].setMap(null)
        }
        routePickup = [];
    }
    else if (kode == 2) {
        for (var i = 0; i < routeOTW.length; i++){
            routeOTW[i].setMap(null)
        }
        routeOTW = [];
    }
}



// function drawrouteorder() {
//     var directionsService = new google.maps.DirectionsService();
//     var directionsRenderer = new google.maps.DirectionsRenderer();
//     directionsRenderer = new google.maps.DirectionsRenderer();
//     directionsRenderer.setMap(map);
//     //const geocoder = new google.maps.Geocoder();
//     db.ref('order_history').once('value', snapshot => {
//         snapshot.forEach(function(childSnapshot) {
//             var driver = childSnapshot.val().driver;
//             var customer = childSnapshot.val().customer;
//             var customer_target = firebase.database().ref('customer/' + customer);
//             //console.log(customer_target);
//             if (childSnapshot.val().status == 1) {
//                 var point_origin = childSnapshot.val().lat_asal + ',' + childSnapshot.val().long_asal;
//                 var point_destination = childSnapshot.val().lat_tujuan + ',' + childSnapshot.val().long_tujuan;
//                 //console.log(point_origin);
//                 //console.log(point_destination);

//                 var request = {
//                     origin: point_origin,
//                     destination: point_destination,
//                     travelMode: 'DRIVING'
//                 };
//                 //directionsRenderer.setDirections(null);
//                 directionsService.route(request, function(result, status) {
//                     if (status == 'OK' && childSnapshot.val().status == 1) {
//                         directionsRenderer.setDirections(result);
//                     } else {
//                         console.log(status);
//                     }
//                 });
//             } else {
//                 directionsRenderer.setDirections(null);
//             }
//         })
//     });
// }

// function drawroutedriver() {
//     var directionsService = new google.maps.DirectionsService();
//     var directionsRenderer = new google.maps.DirectionsRenderer();
//     directionsRenderer = new google.maps.DirectionsRenderer({
//         polylineOptions: {
//             strokeColor: "red"
//         }
//     });
//     directionsRenderer.setMap(map);
//     //const geocoder = new google.maps.Geocoder();

//     db.ref('driver_history').once('value', snapshot => {
//         snapshot.forEach(function(childSnapshot) {
//             // var driver = childSnapshot.val().driver;
//             // var customer = childSnapshot.val().customer;
//             // var customer_target = firebase.database().ref('customer/' + customer);
//             //console.log(customer_target);
//             if (childSnapshot.val().status == 1) {
//                 var point_origin = childSnapshot.val().lat_asal + ',' + childSnapshot.val().long_asal;
//                 var point_destination = childSnapshot.val().lat_tujuan + ',' + childSnapshot.val().long_tujuan;
//                 // console.log(point_origin);
//                 // console.log(point_destination);

//                 var request = {
//                     origin: point_origin,
//                     destination: point_destination,
//                     travelMode: 'DRIVING'
//                 };
//                 //directionsRenderer.setDirections(null);
//                 directionsService.route(request, function(result, status) {
//                     if (status == 'OK') {
//                         directionsRenderer.setDirections(result);
//                     } else {
//                         directionsRenderer.setDirections(null);
//                     }
//                 });

//             } else {
//                 directionsRenderer.setDirections(null);

//             }

//         })

//     })
// }


//Auto Update 
// db.ref('order_history').on('value', snapshot => {
//     //console.log("on");
//     var directionsServiceOrder = new google.maps.DirectionsService();
//     var directionsRendererOrder = new google.maps.DirectionsRenderer();

//     directionsRendererOrder.setMap(null);
//     directionsRendererOrder.setDirections({ routes: [] });
//     directionsRendererOrder = null;

//     directionsRendererOrder = new google.maps.DirectionsRenderer();
//     directionsRendererOrder.setMap(map);

//     const geocoder = new google.maps.Geocoder();

//     snapshot.forEach(function(childSnapshot) {
//         var driver = childSnapshot.val().driver;
//         var customer = childSnapshot.val().customer;
//         var customer_target = firebase.database().ref('customer/' + customer);
//         //console.log(customer_target);
//         if (childSnapshot.val().status == 1) {
//             var point_origin = childSnapshot.val().lat_asal + ',' + childSnapshot.val().long_asal;
//             var point_destination = childSnapshot.val().lat_tujuan + ',' + childSnapshot.val().long_tujuan;
//             //console.log(point_origin);
//             //console.log(point_destination);


//             var request = {
//                 origin: point_origin,
//                 destination: point_destination,
//                 travelMode: 'DRIVING'
//             };
//             //directionsRenderer.setDirections(null);
//             directionsServiceOrder.route(request, function(result, status) {
//                 if (status == 'OK') {
//                     directionsRendererOrder.setMap(map);
//                     directionsRendererOrder.setDirections(result);
//                 }
//             });
//         }

//     })
// });
// db.ref('driver_history').on('value', snapshot => {
//     var directionsServiceDriver = new google.maps.DirectionsService();
//     var directionsRendererDriver = new google.maps.DirectionsRenderer({
//         polylineOptions: {
//             strokeColor: "red"
//         }
//     });
//     directionsRendererDriver.setMap(map);
//     const geocoder = new google.maps.Geocoder();

//     snapshot.forEach(function(childSnapshot) {
//         var driver = childSnapshot.val().driver;
//         var customer = childSnapshot.val().customer;
//         var customer_target = firebase.database().ref('customer/' + customer);
//         //console.log(customer_target);
//         if (childSnapshot.val().status == 1) {
//             var point_origin = childSnapshot.val().lat_asal + ',' + childSnapshot.val().long_asal;
//             var point_destination = childSnapshot.val().lat_tujuan + ',' + childSnapshot.val().long_tujuan;
//             //console.log(point_origin);
//             //console.log(point_destination);


//             var request = {
//                 origin: point_origin,
//                 destination: point_destination,
//                 travelMode: 'DRIVING'
//             };
//             //directionsRenderer.setDirections(null);
//             directionsServiceDriver.route(request, function(result, status) {
//                 if (status == 'OK') {
//                     directionsRendererDriver.setDirections(result);
//                 }
//             });
//         }

//     })

// });