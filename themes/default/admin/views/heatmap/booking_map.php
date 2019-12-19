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
        <h2 class="blue"><i class="fa-fw fa fa-taxi"></i><?= lang('booking_location'); ?></h2>
    </div><?php */?>
    <div class="box-content">
        <div class="row">
        	<div class="col-xs-12">
            	<h2 class="col-xs-12">Search Location</h2>
                <div class="form-group col-xs-2">
                	<select class="form-control" name="status" id="status">
                    	<option value="">All</option>
                        <option value="booked">Booked</option>
                        <option value="onride">On Ride</option>
                        <option value="completed">Complete</option>
                        <option value="cancelled">Cancel</option>
                    </select>
                </div>
                <div class="form-group col-xs-2">
                	<input type="text" name="start_date" id="start_date" onkeypress="dateCheck(this);" placeholder="Start Date" class="form-control" autocomplete="off">
                </div>
                <div class="form-group col-xs-2">
                	<input type="text" name="end_date" id="end_date" onkeypress="dateCheck(this);" placeholder="End Date" class="form-control" autocomplete="off">
                </div>
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
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="Stylesheet"type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script>
$(document).ready(function(e) {
    $("#start_date").datepicker({
        numberOfMonths: 1,
		maxDate: 0,
		dateFormat: 'yy-mm-dd',
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() + 1);
            $("#end_date").datepicker("option", "minDate", dt);
        }
    });
    $("#end_date").datepicker({
        numberOfMonths: 1,
		maxDate: 0,
		dateFormat: 'yy-mm-dd',
        onSelect: function (selected) {
            var dt = new Date(selected);
            dt.setDate(dt.getDate() - 1);
            $("#start_date").datepicker("option", "maxDate", dt);
        }
    });
});
</script>
<!--<script>
var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var start = new google.maps.LatLng(<?= $rides->pickup_lat ?>, <?= $rides->pickup_lng ?>);
var end = new google.maps.LatLng(<?= $rides->dropoff_lat ?>, <?= $rides->dropoff_lng ?>);

function initialize() {

	directionsDisplay = new google.maps.DirectionsRenderer();

	var center = new google.maps.LatLng(0, 0);

	var myOptions = {
		zoom: 7,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: center
	};

	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	var rendererOptions = {
		map: map,
		suppressMarkers: true,
		polylineOptions: {
		  strokeColor: "block"
		}
	};

	directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);

	directionsDisplay.setMap(map);
			
	

	var request = {
		origin: start,
		destination: end,
		travelMode: google.maps.DirectionsTravelMode.DRIVING
	};

	directionsService.route(request, function (response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			
			var route = response.routes[0].legs[0]; 
			
			startMarker(route.start_location);
			endMarker(route.end_location);

			directionsDisplay.setDirections(response);
		}
	});
	
}
function startMarker(position) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
	});
}
function endMarker(position) {
	var marker = new google.maps.Marker({
		position: position,
		map: map,
		icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
	});
}
initialize();
</script>-->
<script>
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var locations1 = [];

function initAutocomplete() {
  /* new google.maps.places.Autocomplete(
          (document.getElementById('location')),
          {types: ['geocode']}
		 
   );*/
  		var input = document.getElementById('location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('cityLat').value = place.geometry.location.lat();
            document.getElementById('cityLng').value = place.geometry.location.lng();
        });
  
  
}

function initialize(lat, lng) {
		
	
  
  directionsDisplay = new google.maps.DirectionsRenderer();
  var current_center_lat = lat ? lat : 13.123189;
  var current_center_lng = lng ? lng : 80.191205;
  var awal = new google.maps.LatLng(current_center_lat, current_center_lng);
  var mapOptions = {
    zoom: 15,
   center: awal
  }
  map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
   
   
		
  directionsDisplay.setMap(map);
  
    directionsServices = [];
    directionsDisplays = [];
}
	
function update(){
	
	var colorVariable = ["black","","blue","yellow","black"];
	var d = 0;
	var i;
	var k =0;
  
	for (i = 0; i < locations1.length; i++) {
		
      directionsServices[i] = new google.maps.DirectionsService();
	 
      var start = new google.maps.LatLng(locations1[i][2], locations1[i][3]);
      var end = new google.maps.LatLng(locations1[i][4], locations1[i][5]);
    
      var request = {
          origin: start,
          destination: end,
          optimizeWaypoints: true,
          travelMode: google.maps.TravelMode.DRIVING
      };
      
	  
	  	if(locations1[i][0] == 'booked'){
        directionsServices[i].route(request, function(response, status) {
			startMarker(response.routes[0].legs[0].start_location);
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplays[directionsDisplays.length-1].setMap(map);
			  directionsDisplays[directionsDisplays.length-1].setDirections(response);
			} else alert("Directions request failed:"+status);
     	});
		}else if(locations1[i][0] == 'onride'){
        directionsServices[i].route(request, function(response, status) {
			startMarker(response.routes[0].legs[0].start_location);
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplays[directionsDisplays.length-1].setMap(map);
			  directionsDisplays[directionsDisplays.length-1].setDirections(response);
			} else alert("Directions request failed:"+status);
     	});
		}else if(locations1[i][0] == 'completed'){
        directionsServices[i].route(request, function(response, status) {
			startMarker(response.routes[0].legs[0].start_location);
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplays[directionsDisplays.length-1].setMap(map);
			  directionsDisplays[directionsDisplays.length-1].setDirections(response);
			} else alert("Directions request failed:"+status);
     	});
		}else if(locations1[i][0] == 'cancelled'){
        directionsServices[i].route(request, function(response, status) {
			startMarker(response.routes[0].legs[0].start_location);
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplays[directionsDisplays.length-1].setMap(map);
			  directionsDisplays[directionsDisplays.length-1].setDirections(response);
			} else alert("Directions request failed:"+status);
     	});
		}else{
		directionsServices[i].route(request, function(response, status) {
			carMarker(response.routes[0].legs[0].start_location);
			if (status == google.maps.DirectionsStatus.OK) {
			  directionsDisplays[directionsDisplays.length-1].setMap(map);
			  directionsDisplays[directionsDisplays.length-1].setDirections(response);
			} else alert("Directions request failed:"+status);
     	});	
		}
	  
	  
	  k++;
    }	
}

function startMarker(position) {
	
    var marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
    });
	
}
function endMarker(position) {
    var marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
    });
}
function carMarker(position) {
    var marker = new google.maps.Marker({
        position: position,
        map: map,
        //icon: 'https://cdn1.iconfinder.com/data/icons/iconbeast-lite/30/map-pin.png'
		icon: 'https://cdn3.iconfinder.com/data/icons/transport-icons-2/512/BT_c3top-20.png'
    });
	
}




$(document).ready(function(e) {
	google.maps.event.addDomListener(window, 'load', initialize());
	initAutocomplete();
	$('#find_location').click(function(){
		var lat = $('#cityLat').val();
		var lng = $('#cityLng').val();
		var status = $('#status').val();
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		if(lat !='' && lng !=''){
		getDrivermove(lat, lng, start_date, end_date, status);
		}else{
			$.alert('Your location is empty. please find location');	
		}
	});
	function getDrivermove(lat, lng, start_date, end_date, status) {
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('heatmap/getBookingLocation')?>',
			data: {lat: lat, lng: lng, start_date: start_date, end_date: end_date, status: status},
			dataType: "json",
			cache: false,
			async: false,
			success: function (data) {
				if(data == ''){
					$.alert('find location is customer empty');		
				}
				locations1 = data; 
				update();
				initialize(lat, lng);
			}
         });
	}
	 //setInterval(function () { 
		//locations1 = $.merge( $.merge( [], locations1 ), third ); 
	 	//update();
	// }, 5000);
});
</script>
    

