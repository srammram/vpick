<style>
#map_canvas {
      	 height: 400px;
		 width:100%;
		 position:relative;
		 float:left;
      }
</style>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('city_ride_cab_available'); ?></h2>
    </div><?php */?>
    <div class="box-content city_ride_s">
        <div class="row">
        	<div class="col-sm-12 col-xs-12">
            	<h2 class="col-xs-12">Search Location</h2>
            </div>
			<div class="col-sm-12 col-xs-12 ">
				<div class="form-group location_se col-sm-6 col-xs-6">
					<input type="text" name="location" id="location" class="form-control">
					<input type="hidden" name="cityLat" id="cityLat">
					<input type="hidden" name="cityLng" id="cityLng">
				</div>
				<div class="form-group location_se col-sm-3 col-xs-6">
					<input type="submit" name="find_location" id="find_location" class="btn btn-primary btn-block" value="GO">
				</div>
			</div>
            <div class="col-lg-12">
                <div id="map_canvas"></div>
            </div>
        </div>
    </div>
</div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script>
var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var infowindow = new google.maps.InfoWindow();
var map;
var marker, driver_id, driver_lat, driver_lng;
var center;
var markers = [];
var multiple_driver = [];
center = new google.maps.LatLng(22.322534, 79.471117);
var loc = [];

function initAutocomplete() {
	var input = document.getElementById('location');
	var autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var place = autocomplete.getPlace();
		$('#cityLat').val(place.geometry.location.lat());
		$('#cityLng').val(place.geometry.location.lng());
	});
}
function initialize() {

    directionsDisplay = new google.maps.DirectionsRenderer({
        suppressMarkers: true
    });
	
    var mapOptions = {
        zoom: 4,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: center
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	var rendererOptions = {
		map: map,
		suppressMarkers: true,
		polylineOptions: {
		  strokeColor: "block",
		  strokeOpacity:0,
		  strokeWeight: 4
		}
	};
	
	

	 

	directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
	
    directionsDisplay.setMap(map);
    calcRoute(loc);
	
}

var json_loc = [];


var driverUpMarker = new Array();
function calcRoute(loc) {
	
	if(loc.length > 0){
		var i;
		for(i=0; i<loc.length; i++){
			//console.log(driverUpMarker[loc[i]['id']]);
			var driver = new google.maps.LatLng(loc[i]['lat'],loc[i]['lng']);
			driverUpMarker[loc[i]['driver_id']] = createMarker(driver,loc[i]['address'],loc[i]['icon'], loc[i]['driver_id']);
			
			var request = {
				origin: driver,
				optimizeWaypoints: true,
				travelMode: google.maps.DirectionsTravelMode.DRIVING
			};
		
			directionsService.route(request, function (response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					directionsDisplay.setDirections(response);
					var route = response.routes[0];
				}
			});
			
			
		}		
	}
	
	
}
function createMarker(latlng,title,icon, driver_id) {
    var marker = new google.maps.Marker({
		id: driver_id,
        position: latlng,
        //animation: google.maps.Animation.DROP,
        title: title,
        icon: icon,
        map: map
    });
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(title);
        infowindow.open(map, marker);
		 map.setZoom(8);
    	map.setCenter(marker.getPosition());
    });
	
	 multiple_driver.push(marker);
	 
    return marker;
}

 
 function removeA(arr, d) {
    var index = arr.indexOf(d);
	if (index > -1) {
	  arr.splice(index, 1);
	}
}

function DeleteMarker(multiple_driver, admin_driver_id) {
        //Find and remove the marker from the Array
        //for (var i = 0; i < markers.length; i++) {
           // if (markers[i].id == id) {
                //Remove the marker from Map                  
               // markers[id].remove();
 				//directionsDisplay.setMap(null);
                //Remove the marker from array.
               // markers.splice(i, 1);
			  // markers[id].setMap(null);
			   //markers[id]=null;
			   // markers = [];
			   removeA(multiple_driver, admin_driver_id);
			   multiple_driver[admin_driver_id].setMap(null);
			   
                return true;
           // }
        //}
    };
	
//setInterval(makeRequest(246, 13.121593, 80.199929), 3000);
function makeRequest(map, marker, driver_id, driver_lat, driver_lng){
	console.log('time');
//setTimeout(function(){
	
	driverUpMarker[driver_id].setPosition( new google.maps.LatLng(driver_lat, driver_lng) );
	//driverUpMarker[248].setPosition( new google.maps.LatLng(13.124045, 80.195350) );
	//map.panTo( new google.maps.LatLng(13.119573, 80.199083) );
//}, 5000);
};

			
/*function makeRequest(driver_id, driver_lat, driver_lng){
	 var myLatLng = new google.maps.LatLng(driver_lat, driver_lng);
	 console.log(myLatLng);
	 driverUpMarker[driver_id].setPosition(myLatLng);
} */
$(document).ready(function(e) {
	initialize();
	initAutocomplete();
	getDrivermove(13.119573, 80.199083);
	$('#find_location').click(function(){
		var lat = $('#cityLat').val();
		var lng = $('#cityLng').val();
		center = new google.maps.LatLng(lat, lng);
		getDrivermove(lat, lng);
		
	});
	function getDrivermove(lat, lng) {
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('heatmap/getTracking')?>',
			data: {lat: lat, lng: lng},
			dataType: "json",
			cache: false,
			async: false,
			success: function (data) {
				initialize();	
				calcRoute(data); 
				json_loc.push(data);			
			}
         });
	}
});
</script>
<script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>
<script>
var socket = io.connect('http://'+window.location.hostname+':7000');


 socket.on('connect', function(){
 console.log('S Connect');	 
});
 //socket.on('connect', function(){});


socket.on('admin_drivers_location', function(data){
	var admin_driver_id = data.admin_driver_id;
	var admin_ride_id = data.admin_ride_id;
	var admin_lat = data.admin_lat;
	var admin_lng = data.admin_lng;
	var is_connected = data.admin_is_connected;
	console.log(data);
	if(admin_driver_id == 0 && admin_ride_id == 0){
		console.log('No driver');
	}else{
		console.log(json_loc);
		if(json_loc.length > 0){
			
			console.log(driverUpMarker);
			
			//console.log(driverUpMarker);
			$(driverUpMarker).each(function(idx, item){
				
				multiple_driver.push(this.id);
			
			});
				if(is_connected == 1){
					if(multiple_driver.includes(admin_driver_id)){
						//multiple_driver.remove(admin_driver_id);
						
						
						
					}else{
						var driver = new google.maps.LatLng(admin_lat,admin_lng);
						driverUpMarker[admin_driver_id] = createMarker(driver,'test','http://13.233.9.134/themes/default/admin/assets/images/track.png', admin_driver_id);
					}
				}else{
					DeleteMarker(multiple_driver, admin_driver_id);
					
				}
			
			
			//driverUpMarker[json_loc[k][0]['driver_id']] = createMarker(driver,'test','http://13.233.9.134/themes/default/admin/assets/images/track.png');
				
			for(k=0; k<json_loc.length; k++){
				//console.log(json_loc[k][0]['driver_id']);
				if(json_loc[k][0]['driver_id'] == admin_driver_id){
					
					console.log('ride emit');
					makeRequest( map, marker, admin_driver_id, admin_lat, admin_lng);
				}else{
					console.log('No ride');
				}
			}
		}else{
			console.log('No driver');
		}
	}
	console.log(data);	
});


  //socket.on('disconnect', function(){});
</script>

    

