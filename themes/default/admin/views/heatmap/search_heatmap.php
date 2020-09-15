<style>
#map_canvas {
      	 height: 400px;
		 width:100%;
		 position:relative;
		 float:left;
      }
</style>
<script>
function showPosition() {
	if(navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var current_lat = position.coords.latitude;
			var current_lng = position.coords.longitude;
			alert(current_lat+','+current_lng);
			return current_lat+','+current_lng;
		});
	} else {
		alert("Sorry, your browser does not support HTML5 geolocation.");
	}
}
</script>
<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('city_ride_cab_available'); ?></h2>
    </div><?php */?>
    <div class="box-content city_ride_s">
        <div class="row">
        	<div class="col-sm-12 col-xs-12">
            	<h2 class="col-xs-12"><?= lang('search_location') ?></h2>
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

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
    <!--<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places">
	</script>-->
	
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>

    <script>
	  var locations = [];
	  var lat = 13.119573;
	var lng = 80.199083;
	  var zooming = 5;
	  var center = new google.maps.LatLng(13.119573, 80.199083);
	  function initAutocomplete() {
			var input = document.getElementById('location');
			var autocomplete = new google.maps.places.Autocomplete(input);
			google.maps.event.addListener(autocomplete, 'place_changed', function () {
				var place = autocomplete.getPlace();
				$('#cityLat').val(place.geometry.location.lat());
				$('#cityLng').val(place.geometry.location.lng());
			});
		}
      function initMap(center, zooming, locations) {
		//alert('a');
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
          zoom: zooming,
          center: center
        });
		var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			// Add some markers to the map.
			// Note: The code uses the JavaScript Array.prototype.map() method to
			// create an array of markers based on a given "locations" array.
			// The map() method here has nothing to do with the Google Maps API.
			var markers = locations.map(function(location, i) {
			return new google.maps.Marker({
				position: location,
				label: labels[i % labels.length]
			});
			});

			// Add a marker clusterer to manage the markers.
			var markerCluster = new MarkerClusterer(map, markers,
				{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
		//mapCluster(locations, map);
        
      }
	  function mapCluster(locations, map){
		  // Create an array of alphabetical characters used to label the markers.
		  var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			// Add some markers to the map.
			// Note: The code uses the JavaScript Array.prototype.map() method to
			// create an array of markers based on a given "locations" array.
			// The map() method here has nothing to do with the Google Maps API.
			var markers = locations.map(function(location, i) {
			return new google.maps.Marker({
				position: location,
				label: labels[i % labels.length]
			});
			});

			// Add a marker clusterer to manage the markers.
			var markerCluster = new MarkerClusterer(map, markers,
				{imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
	  }
      
	 
    </script>
    
	<script>
	function getCurrent(lat, lng, center, zooming){
		
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('heatmap/getSearch')?>',
			data: {lat: lat, lng: lng},
			dataType: "json",
			cache: false,
			async: false,
			success: function (data) {
				center = new google.maps.LatLng(lat, lng);
				
				initMap(center, zooming, data);
				
			}
		});
	}
	$(document).ready(function(){
		
		getCurrent(lat, lng, center, zooming);
		//initMap(center, zooming, locations);
		initAutocomplete();
	});
	$(document).on('click', '#find_location', function(){
		
		var lat = $('#cityLat').val();
		var lng = $('#cityLng').val();
		zooming = 13;
		center = new google.maps.LatLng(lat, lng);
		
		getCurrent(lat, lng, center, zooming);
		initAutocomplete();
		
	});
	</script>