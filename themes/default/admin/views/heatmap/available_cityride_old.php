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
var map, marker;
var myMarkers = new Array();
var markers = new Array();;
var center;
center = new google.maps.LatLng(13.1081093, 80.2049305);

function initAutocomplete() {
	var input = document.getElementById('location');
	var autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var place = autocomplete.getPlace();
		$('#cityLat').val(place.geometry.location.lat());
		$('#cityLng').val(place.geometry.location.lng());
	});
}

function calcRoute(markers) {
	console.log(markers);
	var i;
	if(markers.length > 0){
		for(i=0; i<markers.length; i++){
			var myLatLng = new google.maps.LatLng(markers[i]['lat'],markers[i]['lng']);
			myMarkers[markers[i]['driver_id']] = new google.maps.Marker( {icon: markers[i]['icon'], position: myLatLng} );
			myMarkers[markers[i]['driver_id']].setMap( map );
		}
	}
}

function moveBus( map, marker ) {
	//setTimeout(function(){
		//myMarkers[246].setPosition( new google.maps.LatLng(13.119573, 80.199083) );
		//myMarkers[217].setPosition( new google.maps.LatLng(13.124045, 80.195350) );
		//map.panTo( new google.maps.LatLng(13.119573, 80.199083) );
	//}, 1000)
};

function initialize() {
	var myOptions = {
        zoom: 15,
        center: center,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	calcRoute(markers);
	//moveBus( map, marker );
}
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
			markers.push(data);		
			calcRoute(markers); 
		}
	 });
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
	getDrivermove('13.121593', '80.199929');
	
});
</script>

    

