<script src="https://maps.googleapis.com/maps/api/js?libraries=places&&sensor=false&&key=AIzaSyCsauSmyPB9PXLDoGhY7_QTx1ORZsVPY1k"></script>
<script type="text/javascript" src="<?=$assets?>js/map/map_styles.js"></script>




<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('Tracking'); ?></h2>
       
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
               <div id="map_canvas"></div><div id="routes" style="display: none;"></div>            
                
 </div>
        </div>
    </div>
</div>

<script>
    
    var json = {};
    getdrivers();
    function getdrivers() {
		alert('a');
         $.ajax({
                        type: 'POST',
                        url: '<?=admin_url('map/getTrackingDetails')?>',
                        data: {id: 8,type:'onride'},
                        dataType: "json",
                        cache: false,
                        async: false,
                        success: function (data) {
                            console.log(data)
                            json  = data;
                        }
         });
    }
   
/************** search location ******************/
    //createHomepageGoogleMap(_latitude,_longitude,json);
    $(document).ready(function(){
        $height = $('.content-con').height()+'px';
        $('#map_canvas').css({'height':$height});
    })
</script>
<script>
    var map;
    var icons = {
			  start: new google.maps.MarkerImage(
			   // URL
			   'http://cabily.zoplay.com/images/pickup_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  ),
			  end: new google.maps.MarkerImage(
			   // URL
			   'http://cabily.zoplay.com/images/drop_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  )
			 };

        
$(document).ready(function(){
        var directionsService = new google.maps.DirectionsService;
        var map;
        drawRoute();
        
        function drawRoute() {
            var directionDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map;
            //directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
            var center = new google.maps.LatLng(0, 0);
            var myOptions = {
                zoom: 7,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: center,
                gestureHandling: 'greedy',
                //styles:mapStyles
            }
         
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            
            directionsDisplay.setMap(map);
            var start = {lat: 13.039463, lng: 80.155387};
            var end = {lat: 13.067477, lng: 80.205697};
            var method = 'DRIVING';
            var request = {
                origin: start,
                destination: end,
              
                travelMode: google.maps.DirectionsTravelMode[method],
                provideRouteAlternatives: true
            };

            directionsService.route(request, function (response, status) {

                if (status == google.maps.DirectionsStatus.OK) {
                    var leg = response.routes[ 0 ].legs[ 0 ];
						
                    makeMarker(map, leg.start_location, icons.start, "title" );
                    makeMarker(map, leg.end_location, icons.end, 'title' );
                    var routesSteps = [];
                    var routes = response.routes;
                    //var colors = ['red', 'green', 'blue', 'orange', 'yellow', 'black'];
        
                    for (var i = 0; i < routes.length; i++) {
        
                                                        // Display the routes summary
                    document.getElementById('routes').innerHTML += 'Route ' + i + ': via ' + routes[i].summary + '<br />';
        
                        new google.maps.DirectionsRenderer({
                            map: map,
                            directions: response,
                            routeIndex: i,
                            suppressMarkers: true,
                            markerOptions:icons.start,
                            polylineOptions: {
        
                                strokeColor: '#00b3fd',
                                strokeWeight: 4,
                                strokeOpacity: .8
                            }
                        });

                        var steps = routes[i].legs[0].steps;
                        var stepsCoords = [];
        
                        for (var j = 0; j < steps.length; j++) {
        
                            stepsCoords[j] = new google.maps.LatLng(steps[j].start_location.lat(), steps[j].start_location.lng());
        
                            //new google.maps.Marker({
                            //    position: stepsCoords[j],
                            //    map: map,
                            //    icon: {
                            //        path: 'M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0',
                            //        scale: .5,
                            //        //fillColor: colors[i],
                            //        fillOpacity: .3,
                            //        strokeWeight: 0
                            //    },
                            //    title: steps[j].maneuver
                            //});
                        }
        
                        //routesSteps[i] = stepsCoords;
                    }
                    
                }
           });
            drawRouteByWaypoints(map);
        }
        
        function drawRouteByWaypoints(map) {
            //covering Location Array
            
            var otherLocations = [
                                  {latitude: 13.045238,longitude: 80.186204},
                                 // {latitude:13.041991, longitude: 80.187942},
                                 // {latitude:13.043663, longitude:80.191203},
                                 // {latitude: 13.048666, longitude: 80.192298},
                                 // {latitude: 13.062128, longitude: 80.196504}
                                ];
            var wayPoints = [];
            $cnt = otherLocations.length - 1;
            console.log($cnt)
            $.each(otherLocations, function (key,waypoint) {
                console.log('key:'+key)
                wayPoints.push({
                    location: new google.maps.LatLng(waypoint.latitude, waypoint.longitude),
                    stopover: true
                });
                icon = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                if ($cnt == key) {
                     var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
                     var icon = {
                       path: car,
                       scale: .7,
                       strokeColor: 'white',
                       strokeWeight: .10,
                       fillOpacity: 1,
                       rotation:0,
                       fillColor: 'blue',
                       offset: '50%',
                       // rotation: parseInt(heading[i]),
                       anchor: new google.maps.Point(10,25) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
                     };	
                }
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(waypoint.latitude, waypoint.longitude),
                    map: map,
                    title: 'Hello World!',
                    icon:icon
                });
            });
            //var m = new google.maps.LatLng(13.045238, 80.186204);
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(13.045238, 80.186204),
                map: map,
                title: 'Hello World!',
                icon:'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
              });
            var start = {lat: 13.039463, lng: 80.155387};
            var end = {lat: 13.067477, lng: 80.205697};
            directionsService.route({
                origin: start,
                destination: end,
                waypoints: wayPoints,
                //optimizeWaypoints: true,
                travelMode: 'DRIVING'
              }, function(response, status) {
                if (status === 'OK') {
                     
                  //directionsDisplay = new google.maps.DirectionsRenderer();
                  //directionsDisplay.setMap(map);
                  directionsDisplay.setDirections(response);
                  
                }
            });
            //var request = {
            //    origin: 'Sector 127, Noida',
            //    destination: 'Sector 18, Noida',
            //    waypoints: wayPoints,
            //    optimizeWaypoints: true,
            //    travelMode: 'DRIVING',//provideRouteAlternatives : true
            //};
            //directionsService.route(request, function(response, status) {
            //    console.log(response)
            //    if (status === 'OK') {
            //        directionsDisplay = new google.maps.DirectionsRenderer();
            //        directionsDisplay.setMap(map);
            //        directionsDisplay.setDirections(response);
            //        // For each route, display summary information.
            //    } else {
            //        console.log('Directions request failed due to ' + status, response);
            //    }
            //});
            //createSimpleMarker(map);
            //createMarkerWithLabel(map);
           // createMarkerWithCustomColor(map);
           // createMarkerWithImage(map);
           // createMarkerWithLabelJS(map);
            //createMarkerWithMapIcon(map);


        }
         function makeMarker( map,position, icon, title ) {
	 new google.maps.Marker({
	  position: position,
	  map: map,
	  icon: icon,
	  title: title
	 });			 
}
        function createMap(map) {
            var mapCenter = new google.maps.LatLng(28.535891, 77.345700);
            var myOptions =
                {
                    zoom: 12,
                    gestureHandling: 'greedy',
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: mapCenter
                };
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);

        }

        function createSimpleMarker(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.533938, lng: 77.348235}
                //icon: pinSymbol("#FFF")
            });
        }

        function createMarkerWithLabel(map) {
            var marker = new google.maps.Marker({
                map: map,
                label: '1',
                position: {lat: 28.543792, lng: 77.331007}
                //icon: pinSymbol("#FFF")
            });
        }
        function createMarkerWithCustomColor(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.570317, lng: 77.321820},
                icon: pinSymbol("#FFF")
            });
        }
        function createMarkerWithImage(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.573405, lng: 77.371203},
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
            });
        }

        function createMarkerWithLabelJS(map) {
            var marker = new MarkerWithLabel({
                position: {lat: 28.529377, lng: 77.391295},
                map: map,
                labelContent: 1,
                labelAnchor: new google.maps.Point(7, 35),
                labelClass: "labels", // the CSS class for the label
                labelInBackground: false,
                icon: pinSymbol('red')
            });
        }

        function createMarkerWithMapIcon(map) {
            var marker = new google.maps.Marker({
                position: {lat: 28.533938, lng: 77.348235},
                map: map,
                icon: {
                    path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                    fillColor: '#0000FF',
                    fillOpacity: 1,
                    strokeColor: '',
                    strokeWeight: 0
                },
                map_icon_label: "<span class='map-icon map-icon-bus-station'></span>"
            });
        }

        function pinSymbol(color) {
            return {
                path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                fillColor: color,
                fillOpacity: 1,
                strokeColor: '#000',
                strokeWeight: 2,
                scale: 1
            };
        }
        
});
</script> 
<!--<script>
    var map;
    var icons = {
			  start: new google.maps.MarkerImage(
			   // URL
			   'http://cabily.zoplay.com/images/pickup_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  ),
			  end: new google.maps.MarkerImage(
			   // URL
			   'http://cabily.zoplay.com/images/drop_marker.png',
			   // (width,height)
			   new google.maps.Size( 44, 32 ),
			   // The origin point (x,y)
			   new google.maps.Point( 0, 0 ),
			   // The anchor point (x,y)
			   new google.maps.Point( 22, 32 )
			  )
			 };

        
$(document).ready(function(){
        var directionsService = new google.maps.DirectionsService;
        var map;
        drawRoute();
        
        function drawRoute() {
            var directionDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map;
            //directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
            var center = new google.maps.LatLng(0, 0);
            var myOptions = {
                zoom: 7,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: center,
                gestureHandling: 'greedy',
                //styles:mapStyles
            }
         
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            
            directionsDisplay.setMap(map);
            var start = {lat: 13.039463, lng: 80.155387};
            var end = {lat: 13.067477, lng: 80.205697};
            var method = 'DRIVING';
            var request = {
                origin: start,
                destination: end,
              
                travelMode: google.maps.DirectionsTravelMode[method],
                provideRouteAlternatives: true
            };

            directionsService.route(request, function (response, status) {

                if (status == google.maps.DirectionsStatus.OK) {
                    var leg = response.routes[ 0 ].legs[ 0 ];
						
                    makeMarker(map, leg.start_location, icons.start, "title" );
                    makeMarker(map, leg.end_location, icons.end, 'title' );
                    var routesSteps = [];
                    var routes = response.routes;
                    //var colors = ['red', 'green', 'blue', 'orange', 'yellow', 'black'];
        
                    for (var i = 0; i < routes.length; i++) {
        
                                                        // Display the routes summary
                    document.getElementById('routes').innerHTML += 'Route ' + i + ': via ' + routes[i].summary + '<br />';
        
                        new google.maps.DirectionsRenderer({
                            map: map,
                            directions: response,
                            routeIndex: i,
                            suppressMarkers: true,
                            markerOptions:icons.start,
                            polylineOptions: {
        
                                strokeColor: '#00b3fd',
                                strokeWeight: 4,
                                strokeOpacity: .8
                            }
                        });

                        var steps = routes[i].legs[0].steps;
                        var stepsCoords = [];
        
                        for (var j = 0; j < steps.length; j++) {
        
                            stepsCoords[j] = new google.maps.LatLng(steps[j].start_location.lat(), steps[j].start_location.lng());
        
                            //new google.maps.Marker({
                            //    position: stepsCoords[j],
                            //    map: map,
                            //    icon: {
                            //        path: 'M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0',
                            //        scale: .5,
                            //        //fillColor: colors[i],
                            //        fillOpacity: .3,
                            //        strokeWeight: 0
                            //    },
                            //    title: steps[j].maneuver
                            //});
                        }
        
                        //routesSteps[i] = stepsCoords;
                    }
                    
                }
           });
            drawRouteByWaypoints(map);
        }
        
        function drawRouteByWaypoints(map) {
            //covering Location Array
            
            var otherLocations = [
                                  {latitude: 13.045238,longitude: 80.186204},
                                 // {latitude:13.041991, longitude: 80.187942},
                                 // {latitude:13.043663, longitude:80.191203},
                                 // {latitude: 13.048666, longitude: 80.192298},
                                 // {latitude: 13.062128, longitude: 80.196504}
                                ];
            var wayPoints = [];
            $cnt = otherLocations.length - 1;
            console.log($cnt)
            $.each(otherLocations, function (key,waypoint) {
                console.log('key:'+key)
                wayPoints.push({
                    location: new google.maps.LatLng(waypoint.latitude, waypoint.longitude),
                    stopover: true
                });
                icon = 'http://maps.google.com/mapfiles/ms/icons/green-dot.png';
                if ($cnt == key) {
                     var car = "M17.402,0H5.643C2.526,0,0,3.467,0,6.584v34.804c0,3.116,2.526,5.644,5.643,5.644h11.759c3.116,0,5.644-2.527,5.644-5.644 V6.584C23.044,3.467,20.518,0,17.402,0z M22.057,14.188v11.665l-2.729,0.351v-4.806L22.057,14.188z M20.625,10.773 c-1.016,3.9-2.219,8.51-2.219,8.51H4.638l-2.222-8.51C2.417,10.773,11.3,7.755,20.625,10.773z M3.748,21.713v4.492l-2.73-0.349 V14.502L3.748,21.713z M1.018,37.938V27.579l2.73,0.343v8.196L1.018,37.938z M2.575,40.882l2.218-3.336h13.771l2.219,3.336H2.575z M19.328,35.805v-7.872l2.729-0.355v10.048L19.328,35.805z";
                     var icon = {
                       path: car,
                       scale: .7,
                       strokeColor: 'white',
                       strokeWeight: .10,
                       fillOpacity: 1,
                       rotation:0,
                       fillColor: 'blue',
                       offset: '50%',
                       // rotation: parseInt(heading[i]),
                       anchor: new google.maps.Point(10,25) // orig 10,50 back of car, 10,0 front of car, 10,25 center of car
                     };	
                }
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(waypoint.latitude, waypoint.longitude),
                    map: map,
                    title: 'Hello World!',
                    icon:icon
                });
            });
            //var m = new google.maps.LatLng(13.045238, 80.186204);
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(13.045238, 80.186204),
                map: map,
                title: 'Hello World!',
                icon:'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
              });
            var start = {lat: 13.039463, lng: 80.155387};
            var end = {lat: 13.067477, lng: 80.205697};
            directionsService.route({
                origin: start,
                destination: end,
                waypoints: wayPoints,
                //optimizeWaypoints: true,
                travelMode: 'DRIVING'
              }, function(response, status) {
                if (status === 'OK') {
                     
                  //directionsDisplay = new google.maps.DirectionsRenderer();
                  //directionsDisplay.setMap(map);
                  directionsDisplay.setDirections(response);
                  
                }
            });
            //var request = {
            //    origin: 'Sector 127, Noida',
            //    destination: 'Sector 18, Noida',
            //    waypoints: wayPoints,
            //    optimizeWaypoints: true,
            //    travelMode: 'DRIVING',//provideRouteAlternatives : true
            //};
            //directionsService.route(request, function(response, status) {
            //    console.log(response)
            //    if (status === 'OK') {
            //        directionsDisplay = new google.maps.DirectionsRenderer();
            //        directionsDisplay.setMap(map);
            //        directionsDisplay.setDirections(response);
            //        // For each route, display summary information.
            //    } else {
            //        console.log('Directions request failed due to ' + status, response);
            //    }
            //});
            //createSimpleMarker(map);
            //createMarkerWithLabel(map);
           // createMarkerWithCustomColor(map);
           // createMarkerWithImage(map);
           // createMarkerWithLabelJS(map);
            //createMarkerWithMapIcon(map);


        }
         function makeMarker( map,position, icon, title ) {
	 new google.maps.Marker({
	  position: position,
	  map: map,
	  icon: icon,
	  title: title
	 });			 
}
        function createMap(map) {
            var mapCenter = new google.maps.LatLng(28.535891, 77.345700);
            var myOptions =
                {
                    zoom: 12,
                    gestureHandling: 'greedy',
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: mapCenter
                };
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            directionsDisplay = new google.maps.DirectionsRenderer();
            directionsDisplay.setMap(map);

        }

        function createSimpleMarker(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.533938, lng: 77.348235}
                //icon: pinSymbol("#FFF")
            });
        }

        function createMarkerWithLabel(map) {
            var marker = new google.maps.Marker({
                map: map,
                label: '1',
                position: {lat: 28.543792, lng: 77.331007}
                //icon: pinSymbol("#FFF")
            });
        }
        function createMarkerWithCustomColor(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.570317, lng: 77.321820},
                icon: pinSymbol("#FFF")
            });
        }
        function createMarkerWithImage(map) {
            var marker = new google.maps.Marker({
                map: map,
                //label: '1',
                position: {lat: 28.573405, lng: 77.371203},
                icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'
            });
        }

        function createMarkerWithLabelJS(map) {
            var marker = new MarkerWithLabel({
                position: {lat: 28.529377, lng: 77.391295},
                map: map,
                labelContent: 1,
                labelAnchor: new google.maps.Point(7, 35),
                labelClass: "labels", // the CSS class for the label
                labelInBackground: false,
                icon: pinSymbol('red')
            });
        }

        function createMarkerWithMapIcon(map) {
            var marker = new google.maps.Marker({
                position: {lat: 28.533938, lng: 77.348235},
                map: map,
                icon: {
                    path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                    fillColor: '#0000FF',
                    fillOpacity: 1,
                    strokeColor: '',
                    strokeWeight: 0
                },
                map_icon_label: "<span class='map-icon map-icon-bus-station'></span>"
            });
        }

        function pinSymbol(color) {
            return {
                path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
                fillColor: color,
                fillOpacity: 1,
                strokeColor: '#000',
                strokeWeight: 2,
                scale: 1
            };
        }
        
});
</script> -->
<style>
    #map,.map{
           /* height: 100%;
    width: 100%;*/
    }
    .map-icon{
        width:40px;
    }
    .box .box-content{
        padding: 0px !important;
    }    
</style>
//https://developers.google.com/maps/documentation/javascript/reference?hl=zh-tw#MarkerOptions