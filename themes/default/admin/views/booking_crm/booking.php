<style>
#map_canvas {
height: 400px;
width:100%;
position:relative;
float:left;
}
</style>

<?php
$new_arr[]= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
//echo "Latitude:".$new_arr[0]['geoplugin_latitude']." and Longitude:".$new_arr[0]['geoplugin_longitude'];
?>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
  
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

               

                <?php $attrib = array('class' => 'form-horizontal','class' => 'create_booking','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("booking_crm/book_ride", $attrib);
                ?>
                <div class="row">
                	<input type="hidden" name="is_country" value="<?= $_GET['is_country'] ?>">
                    <input type="hidden" name="user_id" value="<?= $_GET['user_id'] ?>">
                    <input type="hidden" name="payment_id" value="1">
                	<div class="col-sm-6 col-xs-12 ">
                    	<div class="form-group">
                                <?php echo lang('call_agent_id', 'evalution_number'); ?>
                                <div class="controls">
                                    <input type="text" id="evalution_number" name="evalution_number" required class="form-control"  />
                                </div>
                            </div>
                        <div class="form-group">
                                <?php echo lang('pickup_location', 'pickup'); ?>
                                <div class="controls">
                                    <input type="text" id="pickup" name="pickup" required class="form-control"  />
                                    <input type="hidden" name="pickupLat" id="pickupLat">
                            		<input type="hidden" name="pickupLng" id="pickupLng">
                                </div>
                            </div>
                         <div class="form-group">
                                <?php echo lang('drop_location', 'drop'); ?>
                                <div class="controls">
                                    <input type="text" id="drop" name="drop" required class="form-control"  />
                                    <input type="hidden" name="dropLat" id="dropLat">
                            		<input type="hidden" name="dropLng" id="dropLng">
                                </div>
                            </div>
                            
                            <div class="form-group">
							<?php echo lang('cab_type', 'cab_type_id'); ?>
                            <!--<select   class="form-control select cab_type_id"  required name="cab_type_id" id="cab_type_id">
                                <option value="">All</option>
                                <?php
                                foreach($cabtypes as $types){
                                ?>
                                <option value="<?= $types->id ?>"><?= $types->name ?></option>
                                <?php
                                }
                                ?>
                            </select>-->
                            	<div class="controls">
                            <?php
                                foreach($cabtypes as $types){
                                ?>
                           		<input type="radio" name="cab_type_id" required class="cab_type_id" value="<?= $types->id ?>"><?= $types->name ?>
                            <?php
                                }
                                ?>
                                </div>
                            </div>
                            
                            
                            <div class="col-lg-12">
                                <div id="map_canvas"></div>
                            </div>
                    </div>
                    
                    <div class="col-sm-6 col-xs-12 ">
                    	<h2>Available Cabs</h2>
                        <div id="availablecab">
                        	
                        </div>
                      
                </div>

                <p><?php echo form_submit('create_booking', lang('submit'), 'class="btn btn-primary" id="create_booking"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script>

$('.create_booking').bootstrapValidator({
        fields: {
            evalution_number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
					
                   
                }
            },
			 pickup: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the pickup'
                    },
					
                   
                }
            },
			 drop: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the drop'
                    },
					
                   
                }
            },
			 cab_type_id: {
                validators: {
                    notEmpty: {
                        message: 'Please choose cab type'
                    },
					
                   
                }
            },
            
           
           
            
        },
        submitButtons: 'input[type="submit"]'
    });
	
</script>
  
  
  <script>
  var directionDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var center_lat = '<?= $new_arr[0]['geoplugin_latitude'] ?>';
var center_lng = '<?= $new_arr[0]['geoplugin_longitude'] ?>';
var marker;
var markers = [];
var Drivermarkers = [];
var start = '';
var end = '';

var zoom = 7;
var center = new google.maps.LatLng(center_lat, center_lng);

function initialize(zoom, center) {
	initAutocomplete();
	directionsDisplay = new google.maps.DirectionsRenderer();

	

	var myOptions = {
		zoom: zoom,
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
	
	action(start, end);
	
	
	

	/*var request = {
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
	});*/
	
}

function action(start, end){
	deleteMarkers();
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
	marker = new google.maps.Marker({
		position: position,
		map: map,
		icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
	});
	markers.push(marker);
}
function endMarker(position) {
	marker = new google.maps.Marker({
		position: position,
		map: map,
		icon: 'http://13.233.9.134/themes/default/admin/assets/images/track.png'
	});
	markers.push(marker);
}
function driversetMarker(position) {
	marker = new google.maps.Marker({
		position: position,
		map: map,
		icon: 'http://13.233.9.134/themes/default/admin/assets/images/Completed.png'
	});
	Drivermarkers.push(marker);
	google.maps.event.addListener(marker, 'click', function() {
   		map.panTo(this.getPosition());
    	map.setZoom(18);
    });  
}
function setMapOnAll(map) {
	for (var i = 0; i < markers.length; i++) {
	  markers[i].setMap(map);
	}
}
function clearMarkers() {
setMapOnAll(null);
}

function deleteMarkers() {
clearMarkers();
markers = [];
}

function DriversetMapOnAll(map) {
	for (var i = 0; i < Drivermarkers.length; i++) {
	  Drivermarkers[i].setMap(map);
	}
}
function DriverclearMarkers() {
DriversetMapOnAll(null);
}

function DriverdeleteMarkers() {
DriverclearMarkers();
Drivermarkers = [];
}

function stopHighlightMarker(i) {
  if (Drivermarkers[i].getAnimation() !== null) {
    Drivermarkers[i].setAnimation(null);
  }
}

function highlightMarker(i) {
  if (Drivermarkers[i].getAnimation() !== null) {
    Drivermarkers[i].setAnimation(null);
  } else {
    Drivermarkers[i].setAnimation(google.maps.Animation.BOUNCE);
  }
}
  </script>
  <script>
function initAutocomplete() {
	var pickup = document.getElementById('pickup');
	var pickup_autocomplete = new google.maps.places.Autocomplete(pickup);
	google.maps.event.addListener(pickup_autocomplete, 'place_changed', function () {
	var pickup_place = pickup_autocomplete.getPlace();
	$('#pickupLat').val(pickup_place.geometry.location.lat());
	$('#pickupLng').val(pickup_place.geometry.location.lng());
		start = new google.maps.LatLng(pickup_place.geometry.location.lat(), pickup_place.geometry.location.lng());
		action(start, end);
		var pickupLat = $('#pickupLat').val();
		var pickupLng = $('#pickupLng').val();
		var dropLat = $('#dropLat').val();
		var dropLng = $('#dropLng').val();
		var cab_type_id = $('#cab_type_id').val();
		var is_country = '<?= $_GET['is_country'] ?>';
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('booking_crm/getAvailable')?>',
			data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
			dataType: "html",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var res = scdata.split('###');
				var j = JSON.parse(res[1]);
				if(res[1] != null){
					DriverdeleteMarkers();
					$.each($(j),function(key,value){
					   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
						driversetMarker(driver_location);
					});
				}
				$('#availablecab').html(res[0]);
				console.log(Drivermarkers);
			}
		});
	});
	
	var drop = document.getElementById('drop');
	var drop_autocomplete = new google.maps.places.Autocomplete(drop);
	google.maps.event.addListener(drop_autocomplete, 'place_changed', function () {
	var drop_place = drop_autocomplete.getPlace();
	$('#dropLat').val(drop_place.geometry.location.lat());
	$('#dropLng').val(drop_place.geometry.location.lng());
		end = new google.maps.LatLng(drop_place.geometry.location.lat(), drop_place.geometry.location.lng());
		action(start, end);
		var pickupLat = $('#pickupLat').val();
		var pickupLng = $('#pickupLng').val();
		var dropLat = $('#dropLat').val();
		var dropLng = $('#dropLng').val();
		var cab_type_id = $('#cab_type_id').val();
		var is_country = '<?= $_GET['is_country'] ?>';
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('booking_crm/getAvailable')?>',
			data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
			dataType: "html",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var res = scdata.split('###');
				var j = JSON.parse(res[1]);
				if(res[1] != null){
					DriverdeleteMarkers();
					$.each($(j),function(key,value){
					   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
						driversetMarker(driver_location);
					});
				}
				$('#availablecab').html(res[0]);
				console.log(Drivermarkers);
			}
		});
	});
	
}
$(document).ready(function(e) {
    initialize(zoom, center);
	
	var pickupLat = $('#pickupLat').val();
	var pickupLng = $('#pickupLng').val();
	var dropLat = $('#dropLat').val();
	var dropLng = $('#dropLng').val();
	var cab_type_id = $('#cab_type_id').val();
	var is_country = '<?= $_GET['is_country'] ?>';
	$.ajax({
		type: 'GET',
		url: '<?=admin_url('booking_crm/getAvailable')?>',
		data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
		dataType: "html",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			var res = scdata.split('###');
			var j = JSON.parse(res[1]);
			if(res[1] != null){
				$.each($(j),function(key,value){
				   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
					driversetMarker(driver_location);
				});
			}
			$('#availablecab').html(res[0]);
			console.log(Drivermarkers);
		}
	});
});

/*$(document).on('change', '#pickup', function(e){
	alert('pickup');
	var pickupLat = $('#pickupLat').val();
	var pickupLng = $('#pickupLng').val();
	var dropLat = $('#dropLat').val();
	var dropLng = $('#dropLng').val();
	var cab_type_id = $('#cab_type_id').val();
	var is_country = '<?= $_GET['is_country'] ?>';
	$.ajax({
		type: 'GET',
		url: '<?=admin_url('booking_crm/getAvailable')?>',
		data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
		dataType: "html",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			var res = scdata.split('###');
			var j = JSON.parse(res[1]);
			if(res[1] != null){
				$.each($(j),function(key,value){
				   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
					driversetMarker(driver_location);
				});
			}
			$('#availablecab').html(res[0]);
			console.log(Drivermarkers);
		}
	});
});*/



/*$(document).on('change', '#drop', function(e){
	alert('drop');
	var pickupLat = $('#pickupLat').val();
	var pickupLng = $('#pickupLng').val();
	var dropLat = $('#dropLat').val();
	var dropLng = $('#dropLng').val();
	var cab_type_id = $('#cab_type_id').val();
	var is_country = '<?= $_GET['is_country'] ?>';
	$.ajax({
		type: 'GET',
		url: '<?=admin_url('booking_crm/getAvailable')?>',
		data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
		dataType: "html",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			var res = scdata.split('###');
			var j = JSON.parse(res[1]);
			if(res[1] != null){
				
				$.each($(j),function(key,value){
				   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
					driversetMarker(driver_location);
				});
			}
			$('#availablecab').html(res[0]);
			console.log(Drivermarkers);
		}
	});
});*/

	$('.cab_type_id').on('ifChecked', function(){
	  $('.create_booking').bootstrapValidator('revalidateField', 'cab_type_id');
	  
	 
	    var pickupLat = $('#pickupLat').val();
		var pickupLng = $('#pickupLng').val();
		var dropLat = $('#dropLat').val();
		var dropLng = $('#dropLng').val();
		var cab_type_id = $(this).val();
		
		var is_country = '<?= $_GET['is_country'] ?>';
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('booking_crm/getAvailable')?>',
			data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
			dataType: "html",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				var res = scdata.split('###');
				var j = JSON.parse(res[1]);
				if(res[1] != null){
					DriverdeleteMarkers();
					$.each($(j),function(key,value){
					   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
						driversetMarker(driver_location);
					});
				}
				$('#availablecab').html(res[0]);
				console.log(Drivermarkers);
				
			}
		});
    });

	
	
/*$(document).on('change', '#cab_type_id', function(e){
	
	var pickupLat = $('#pickupLat').val();
	var pickupLng = $('#pickupLng').val();
	var dropLat = $('#dropLat').val();
	var dropLng = $('#dropLng').val();
	var cab_type_id = $(this).val();
	var is_country = '<?= $_GET['is_country'] ?>';
	$.ajax({
		type: 'GET',
		url: '<?=admin_url('booking_crm/getAvailable')?>',
		data: {pickupLat: pickupLat, pickupLng: pickupLng, dropLat: dropLat, dropLng: dropLng, cab_type_id: cab_type_id, is_country: is_country},
		dataType: "html",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			var res = scdata.split('###');
			var j = JSON.parse(res[1]);
			if(res[1] != null){
				DriverdeleteMarkers();
				$.each($(j),function(key,value){
				   var driver_location = new google.maps.LatLng(value.latitude, value.longitude);
					driversetMarker(driver_location);
				});
			}
			$('#availablecab').html(res[0]);
			console.log(Drivermarkers);
			
		}
	});
});
*/

$(document).on('click', '.marker-link', function(e){
	var val = $(this).attr('data-id');
	google.maps.event.trigger(Drivermarkers[val], 'click');
});


$(document).on('mouseenter','.marker-link', function (event) {
    var val = $(this).attr('data-id');	
	highlightMarker(val);
}).on('mouseleave','.marker-link',  function(){
    var val = $(this).attr('data-id');
	stopHighlightMarker(val);	
});

  </script>
  



