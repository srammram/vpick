<style>
.cycleTimerBox {
  display: inline-block;
  width:200px;
  height: 200px;
  border-radius: 50%;
  background-color: #fff;
  float: left;
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
		
        <?php $attrib = array('class' => 'form-horizontal','class' => 'timeout', 'name' => 'timeout', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("booking_crm/waiting_driver", $attrib);
                ?>
                
                <input type="hidden" name="is_country" value="<?= $_GET['is_country'] ?>">
                    <input type="hidden" name="user_id" value="<?= $_GET['user_id'] ?>">
                     <input type="hidden" name="booking_id" value="<?= $_GET['booking_id'] ?>">
                    <input type="hidden" name="timeout" id="timeout" value="1">
                    
                <div class="row">
        	<div class="col-xs-6">
            	<h2 class="col-xs-12"><?= lang('details') ?> <?php if($rides->status == 5){ ?><a href="<?= admin_url('rides/pdf/').$rides->id.'/v' ?>" class="btn btn-primary pull-right"><?= lang('invoice_pdf') ?></a> <?php } ?></h2>
                <p class="col-xs-12"><strong><?= lang('booking_no') ?> : <?= $rides->booking_no ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('booking_time') ?> : <?= $rides->booking_timing ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('type') ?> :
                 <?php if($rides->booked_type== 1){ echo 'Cityride'; }elseif($rides->booked_type== 2){ echo 'Rental'; }elseif($rides->booked_type== 3){ echo 'Outstation'; } ?></strong></p>
                <p class="col-xs-12"><strong><?= lang('status') ?> :
                 <?php
                if($rides->status == 1){
                    $msg = 'Request Ride';
                }elseif($rides->status == 2){
                    $msg = 'Booked Ride';
                }elseif($rides->status == 3){
                    $msg = 'Onride Ride';
                }elseif($rides->status == 4){
                    $msg = 'Waiting Ride';
                }elseif($rides->status == 5){
                    $msg = 'Completed Ride';
                }elseif($rides->status == 6){
                    $msg = 'Cancelled Ride';
                }elseif($rides->status == 7){
                    $msg = 'Ride Later Ride';
                }elseif($rides->status == 8){
                    $msg = 'Ride Rejected';
                }elseif($rides->status == 9){
                    $msg = 'Incomplete';
                }elseif($rides->status == 10){
                    $msg = 'Next Ride';
                }
				echo $msg;
                ?></strong></p>
                <?php if($rides->status == 8 || $rides->status == 6){ ?>
                <p class="col-xs-12"><strong><?= lang('cancel_detail') ?> :</strong><br>
                 <?= lang('cancel_location') ?> : <?= $rides->cancel_location ?><br>
                 <?= lang('cancel_msg') ?> : <?= $rides->cancel_msg ?>
                 </p>
                <?php } ?>
                
                <p class="col-xs-12">
                <strong><?= lang('customer') ?> </strong><br>
                <?= $rides->cfname ?> <?= $rides->clname ?><br>
                <?= $rides->cccode.$rides->cmobile ?>
                </p>

                <div class="clearfix"></div>
                <p class="col-xs-12">
                <strong><?= lang('pickup') ?></strong><br>
                
                <?php  
					$pick = $this->site->findLocationWEB($rides->start_lat, $rides->start_lng); 
					echo $pick;
				?>
                <br>
                </p>
                <p class="col-xs-12">
                <strong><?= lang('drop') ?></strong><br>
                
                <?php 
					$drop = $this->site->findLocationWEB($rides->end_lat, $rides->end_lng); 
					echo $drop;
				?>
                <br>
                <!--<strong>Actual</strong><br>-->
                
                <?php 
				//$actual = $this->site->findLocationWEB($rides->actual_lat, $rides->actual_lng); 
				//echo $actual;
				?>
                </p>
                <div class="clearfix"></div>
                
                <div class="col-xs-12">
                	<p>
                    	<strong><?= lang('payment') ?> : </strong>
                        <p><?= $rides->payment_name ?></p>
                    </p>
                </div>
                
            </div>
            
                <div class="col-lg-6">              
                    <span class="cycleTimerBox">
                    <canvas height="200" width="200" id="cycleTimer"/>
                    </span>
                    
                    <div class="clearfix"></div>
                    
                    <button type="button" class="btn btn-primary btn-lg" id="cancel_ride" data-toggle="modal" data-target="#myModal">Cancel Ride</button>
                    
                </div>
            </div>
            
           
            
             <?php echo form_close(); ?>
                
			</div>

        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cancel Ride</h4>
      </div>
      <?php $attrib = array('class' => 'form-horizontal','class' => 'create_customer','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
		echo admin_form_open_multipart("booking_crm/waiting_driver", $attrib);
		?>
      <div class="modal-body">
        <p><strong><?= lang('booking_no') ?> : <?= $rides->booking_no ?></strong></p>
         <input type="hidden" name="is_country" value="<?= $_GET['is_country'] ?>">
        <input type="hidden" name="user_id" value="<?= $_GET['user_id'] ?>">
         <input type="hidden" name="booking_id" value="<?= $_GET['booking_id'] ?>">
        <input type="hidden" name="timeout" id="timeout" value="2">
        <div class="form-group">
			<?php echo lang('Reason', 'cancel_msg'); ?>
            <div class="controls">
            	<textarea id="cancel_msg" name="cancel_msg" required class="form-control"></textarea>
                
            </div>
            
            <p><?php echo form_submit('cancel_ride', lang('submit'), 'class="btn btn-primary"'); ?></p>
        </div>
      </div>
      <?php echo form_close(); ?>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script>
function createTimer(time) {
var counter = document.getElementById('cycleTimer').getContext('2d');
var no = time;
var pointToFill = 4.72;
var cw = counter.canvas.width;
var ch = counter.canvas.height;
var diff;
  
function fillCounter() {
    diff = ((no / time) * Math.PI * 2 * 10);
    counter.clearRect(0, 0, cw, ch);
    counter.lineWidth = 3;
    counter.fillStyle = '#000';
    counter.strokeStyle = '#F5E0A9';
    counter.textAlign = 'center';
    counter.font = "25px monospace";
    counter.fillText(no + 'sec', 100, 110);
    counter.beginPath();
    counter.arc(110, 110, 70, pointToFill, diff / 10 + pointToFill);
    counter.stroke();

    if (no == 0) {
		
        clearTimeout(fill);
		//$("form").submit(); 
    }
    no--;
}

var fill = setInterval(fillCounter, 1000);
}

createTimer(90);
	</script>
    
    
    <script src="<?=base_url('serverkapp/node_modules/socket.io/node_modules/socket.io-client/dist/socket.io.js')?>"></script>

<script>
var socket = io.connect('http://'+window.location.hostname+':7000');
socket.on('connect', function(){
	 console.log('S Connect');	 
});

var booking_id = '<?= $_GET['booking_id'] ?>';

socket.on('admin_drivers_accept', function(data){
	var admin_driver_id = data.driver_id;
	var admin_ride_id = data.ride_id;
	
	console.log(data);
	if(admin_driver_id == 0 && admin_ride_id == 0){
		console.log('No driver');
	}else{
		console.log('Data driver');
		if(admin_ride_id == booking_id){	
			window.location = "<?= admin_url('booking_crm/tracking/?booking_id='.$_GET['booking_id'].'&is_country='.$_GET['is_country']) ?>";
		}
	}
});


</script>

