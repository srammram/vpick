<script src="https://maps.googleapis.com/maps/api/js?libraries=places,visualization&&sensor=false&&key=AIzaSyCsauSmyPB9PXLDoGhY7_QTx1ORZsVPY1k"></script>
<!--<script type="text/javascript" src="<?=$assets?>js/map/map_styles.js"></script>
<script type="text/javascript" src="<?=$assets?>js/map/richmarker.js"></script>
<script type="text/javascript" src="<?=$assets?>js/map/infobox.js"></script>
<script type="text/javascript" src="<?=$assets?>js/rating.js"></script>-->
<script src="<?=$assets?>js/jquery.autocomplete.min.js"></script>

<!--<script type="text/javascript" src="<?=$assets?>js/map/maps.js"></script>-->

<div class="box">
    <div class="box-header">
        <?php /*?><h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('traffic_locations'); ?></h2><?php */?>
        <input type="text" name="location" id="location" placeholder="search location"><button type="submit" id="find-location">Go</button>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                    <div class="map"><div id="map"></div></div>            
                
 </div>
        </div>
    </div>
</div>

<script>

    var  _latitude = 13.062415;
    var  _longitude = 80.162210;
    var $points = [];
    var map;
     if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
              _latitude = position.coords.latitude;
              console.log(_latitude)
              _longitude = position.coords.longitude;
          });
     }
    function initMap() {
        console.log($points)
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
          center: {lat: _latitude, lng:_longitude},
          mapTypeId: 'satellite',
          gestureHandling: 'greedy'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: $points,
          map: map
        });
         var trafficLayer = new google.maps.TrafficLayer();
                trafficLayer.setMap(map);
    }

    
    getlocations();
    function getlocations() {
         $.ajax({
                        type: 'POST',
                        url: '<?=admin_url('heatmap/getTraffic_locations')?>',
                        data: {lat: _latitude,lng:_longitude},
                        dataType: "json",
                        cache: false,
                        async: false,
                        success: function (data) {
                            $points = [];
                            console.log(data)
                            $.each(data.data,function(n,v){
                                console.log(v.pickup_lat)
                                $points.push(new google.maps.LatLng(v.pickup_lat, v.pickup_lng));
                            });
                            initMap();
                        }
         });
    }
   
    /************** search location ******************/
    var autocompleteOptions = {}
    var placesAutocomplete;			
    var autocompleteInput = document.getElementById('location');
    //var s_map = new google.maps.Map(document.getElementById('map'), {
    //  center: {lat: _latitude, lng: _longitude},
    //  zoom: 13
    //});
    placesAutocomplete = new google.maps.places.Autocomplete(autocompleteInput, autocompleteOptions);
   
    placesAutocomplete.bindTo('bounds', map);
    placesAutocomplete.addListener('place_changed', function() {        
        var place = placesAutocomplete.getPlace();
        if (!place.geometry) {
            window.alert("placesAutocomplete's returned place contains no geometry");
            return;
        }
        _latitude = place.geometry.location.lat();
        _longitude = place.geometry.location.lng();
        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            //s_map.fitBounds(place.geometry.viewport);
            
        } else {
            //s_map.setCenter(place.geometry.location);
            //s_map.setZoom(17);
            
        }
    });
/************** search location ******************/
    //createHomepageGoogleMap(_latitude,_longitude,json);
    $(document).ready(function(){
        $height = $('.content-con').height()+'px';
        $('#map').css({'height':$height});
        $('#find-location').click(function(){
            getlocations();
            
        })
    })
</script>
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
    .infobox {
    font-family: 'Arial',sans-serif;
    display: block;
    width: 295px;
    position: relative;
        background: #fff;
        z-index: -1;
    }
    
</style>