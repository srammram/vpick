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
var map;    
var markers = []; 
var loc = []; 
var marker;
var multiple_driver = [];  
var center;
center = new google.maps.LatLng(3.033965, 102.714050);
var zoom = 5;
var infowindow = new google.maps.InfoWindow();
var geocoder;
var delete_id;
var directionsDisplay = new google.maps.DirectionsRenderer;;
var directionsService = new google.maps.DirectionsService;
		
function initAutocomplete() {
	var input = document.getElementById('location');
	var autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var place = autocomplete.getPlace();
		$('#cityLat').val(place.geometry.location.lat());
		$('#cityLng').val(place.geometry.location.lng());
	});
}

function multipleLocation(loc) {
	if(loc.length > 0){
		for(i=0; i<loc.length; i++){
			var lat_lng = new google.maps.LatLng(loc[i]['lat'],loc[i]['lng']);  
    		markers[loc[i]['driver_id']] = addMarker(lat_lng, loc[i]['address'],loc[i]['icon'],loc[i]['driver_id']);  
			multiple_driver.push(loc[i]['driver_id']);
			
		}
	}
}

function initMap(center, zoom) {    
 geocoder = new google.maps.Geocoder();
 initAutocomplete();
   //geocoder = new google.maps.Geocoder();   
  var markers = [];  
  map = new google.maps.Map(document.getElementById('map_canvas'), {    
    zoom: zoom,    
    center: center,    
    mapTypeId: google.maps.MapTypeId.ROADMAP   
  });    
   multipleLocation(loc); 
   deleteMarkers(delete_id);
  // Adds a marker at the center of the map.    
}    

function codeLatLng(lat, lng) {
    var latlng = new google.maps.LatLng(lat, lng);
    geocoder.geocode({'latLng': latlng}, function(results, status) {
      if(status == google.maps.GeocoderStatus.OK) {
          //console.log(results)
          if(results[0]) {
              //formatted address
			  //console.log(results[0].formatted_address);
              //var address = results[0].formatted_address;
              address =  results[0].formatted_address;
          } else {
              address = 'No address';
          }
      } else {
          address =  status;
      }
    });
	return address;
}
 


// Adds a marker to the map and push to the array.    
function addMarker(lat_lng,title,icon, driver_id) {    
  var marker = new google.maps.Marker({    
    position: lat_lng,    
	id: driver_id,
    map: map,
	title: title,
	icon: icon,  
  }); 
	google.maps.event.addListener(marker, 'click', function (evt) {
		
		var url_google = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + evt.latLng.lat().toFixed(3) + "," + evt.latLng.lng().toFixed(3) + "&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8";
		
		$.getJSON(url_google,function (data, textStatus) {
			
			infowindow.setContent(data.results[0].formatted_address);
		});
		
    	infowindow.open(map, marker);
		//infowindow.setContent(title);
		//infowindow.open(map, marker);
		//infowindow.close();
		 map.setZoom(13);
		//map.setCenter(marker.getPosition());
	});
  return marker;
}   
function changeMarkerPosition(map, marker, driver_id, driver_lat, driver_lng) {
    var latlng = new google.maps.LatLng(driver_lat, driver_lng);
    markers[driver_id].setPosition(latlng);
	
}
function deleteMarkers(admin_driver_id) {
    markers[admin_driver_id].setMap(null);
	markers.length-1;
} 




</script>
<script>

$(document).ready(function(e) {
	initMap(center, zoom);
	initAutocomplete();
	//getDrivermove(13.119573, 80.199083);
	
});

$(document).on('click', '#find_location', function(){
		
		var lat = $('#cityLat').val();
		var lng = $('#cityLng').val();
		zoom = 13;
		center = new google.maps.LatLng(lat, lng);
		
		multiple_driver = [];
		initMap(center, zoom);
		
	});

function getUnique(array){
	var uniqueArray = [];
	
	// Loop through array values
	for(var value of array){
		if(uniqueArray.indexOf(value) === -1){
			uniqueArray.push(value);
		}
	}
	return uniqueArray;
}
	

</script>
<script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>

<script>
var socket = io.connect('http://'+window.location.hostname+':7000');
socket.on('connect', function(){
	 console.log('S Connect');	 
});
var addresslatlng = [];


 
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
		console.log('Data driver');
		//console.log(markers);
		if(is_connected == 1){	
			console.log(getUnique(multiple_driver));
			
				
			if(getUnique(multiple_driver).includes(admin_driver_id)){
				console.log('Exit Driver');
				if(admin_ride_id != 0){
										
					//markers[admin_driver_id].setIcon('http://13.233.9.134/themes/default/admin/assets/images/Completed.png');
					
					//var url_google = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + admin_lat + "," + admin_lng + "&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8";
				//console.log(url_google);
				 //var add = '';
					//$.getJSON(url_google,function (data, textStatus) {
					   // add = data.results[0].formatted_address;
					   //console.log(data.results[0].formatted_address+'test');
					  // markers[admin_driver_id].setContent(data.results[0].formatted_address);
					   //markers[admin_driver_id] =  addMarker(lat_lng1, data.results[0].formatted_address,'http://13.233.9.134/themes/default/admin/assets/images/track.png', admin_driver_id);
					//});
					
				}else{
					
				}
			}else{
				console.log('New Driver');	
						
				//console.log(addresslatlng);
				console.log(admin_driver_id+'Driver');
				console.log(admin_lat);
				console.log(admin_lng);
				var lat_lng1 = new google.maps.LatLng(admin_lat,admin_lng);
				
				//geocoder = new google.maps.Geocoder();
				//addresslatlng[admin_driver_id] = codeLatLng(admin_lat, admin_lng);
				//console.log(addresslatlng);
				
				
				//var url_google = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + admin_lat + "," + admin_lng + "&key=AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8";
				//console.log(url_google);
				 //var add = '';
				//$.getJSON(url_google,function (data, textStatus) {
				   // add = data.results[0].formatted_address;
				  // console.log(data.results[0].formatted_address+'test');
				   markers[admin_driver_id] =  addMarker(lat_lng1, '','http://13.233.9.134/themes/default/admin/assets/images/track.png', admin_driver_id);
				//});
				//console.log('tttt'+add);
				
				
				
				
				multiple_driver.push(admin_driver_id);	
				
			}
		
			changeMarkerPosition(map, marker, admin_driver_id, admin_lat, admin_lng);
		}else{
			var index = multiple_driver.indexOf(admin_driver_id);
			if (index > -1) {
				multiple_driver.splice(index, 1);
			}
			deleteMarkers(admin_driver_id);
		}
	}
});
</script>

    

