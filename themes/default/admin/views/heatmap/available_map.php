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
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('available_taxi'); ?></h2>
    </div><?php */?>
    <div class="box-content">
        <div class="row">
        	<div class="col-xs-12">
            	<h2 class="col-xs-12">Search Location</h2>
                
                <div class="form-group col-xs-4">
                    <input type="text" name="location" id="location" class="form-control">
                    <input type="hidden" name="cityLat" id="cityLat">
                    <input type="hidden" name="cityLng" id="cityLng">
                </div>
                <div class="form-group col-xs-2">
                    <input type="submit" name="find_location" id="find_location" class="btn btn-primary btn-block" value="GO">
                </div>
                
                
            </div>
            <div class="col-lg-12">
                <div id="map_canvas"></div>
            </div>

        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script>
var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var infowindow = new google.maps.InfoWindow();
var map;
var center;
center = new google.maps.LatLng(13.1081093, 80.2049305);
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
        zoom: 13,
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

var driverUpMarker = [];
function calcRoute(loc) {
	
	if(loc.length > 0){
		var i;
		for(i=0; i<loc.length; i++){
			
			var driver = new google.maps.LatLng(loc[i]['lat'],loc[i]['lng']);
			driverUpMarker[loc[i]['id']] = createMarker(driver,loc[i]['address'],loc[i]['icon']);
			
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
function createMarker(latlng,title,icon) {
    var marker = new google.maps.Marker({
        position: latlng,
        animation: google.maps.Animation.DROP,
        title: title,
        icon: icon,
        map: map
    });
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(title);
        infowindow.open(map, marker);
    });

    return marker;
}
function makeRequest(driver_id, driver_lat, driver_lng){
	 var myLatLng = { lat: driver_lat, lng: driver_lng};
	 driverUpMarker[driver_id].setPosition(myLatLng);
} 
$(document).ready(function(e) {
	initialize();
	initAutocomplete();
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
var socket = io('http://'+window.location.hostname+':5000');
 socket.on('connect', function(){
	console.log('Socket Connect');	 
});
socket.on('admin_drivers', function(data){
	var admin_driver_id = data.admin_driver_id;
	var admin_ride_id = data.admin_ride_id;
	var admin_lat = data.admin_lat;
	var admin_lng = data.admin_lng;
	if(admin_driver_id == 0 && admin_ride_id == 0){
		console.log('No driver');
	}else{
		if(json_loc.length > 0){
			for(k=0; k<json_loc.length; k++){
				console.log(json_loc[k]['driver_id']);
				if(json_loc[k]['driver_id'] == admin_driver_id){
					makeRequest(admin_driver_id, admin_lat, admin_lng);
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


socket.on('disconnect', function(){
	console.log('Socket Disconnect');	
});
</script>

    

