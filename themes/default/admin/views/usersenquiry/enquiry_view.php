
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
           
		
        <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("enquiry/open/".$enquiry_id, $attrib);
				
                ?>
                
       <div class="col-lg-12 user_details_sec">
				
               
                <fieldset class="col-lg-6 scheduler-border">
					<legend class="scheduler-border"><?= lang('ticket_view') ?></legend>
                    <div class="form-group">
						<label class="control-label col-sm-4">Ticket Type</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->enquiry_type ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('ticket_code') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->enquiry_code ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('ticket_date') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->enquiry_date ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('customer_name') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->customer_name ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('help_services') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->help_services ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('help_main_cat') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->main_help_name ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('help_sub_cat') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->sub_help_name ?>" />
						</div>
					</div>
                    <h4 class="col-lg-12"><?= lang('help_question') ?></h4>
                    <div class="col-lg-12">
                    	<?= $enquiry_details->details; ?>
                    </div>
                    <?php
					//print_r($enquiry_details->help_message);
					$supported_image = array(
						'gif',
						'jpg',
						'jpeg',
						'png'
					);

					foreach(json_decode($enquiry_details->help_message) as $key => $val){
						$ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
						if (in_array($ext, $supported_image)){
					?>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= $key ?></label>
						<div class="col-sm-8">
                        	<img src="<?= site_url('assets/uploads/').$val ?>" width="100" height="100">
							
						</div>
					</div>
                    <?php	
						}else{
					?>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= $key ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $val ?>" />
						</div>
					</div>
                    <?php
						}
						
					}
					?>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('enq_status') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?php if($enquiry_details->enquiry_status == 0){echo 'Process';}elseif($enquiry_details->enquiry_status == 1){echo 'Open';}elseif($enquiry_details->enquiry_status == 2){echo 'Transfer';}elseif($enquiry_details->enquiry_status == 3){echo 'Close';}elseif($enquiry_details->enquiry_status == 4){echo 'Reopen';}?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('cus_status') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_mobile" name="user_mobile" value="<?php if($enquiry_details->customer_status == 1){echo 'Waiting for Customer';}elseif($enquiry_details->customer_status == 2){echo 'Customer Accepted';}elseif($enquiry_details->customer_status == 3){echo 'Customer Not Accepted ';}else{ echo 'Support Team is Processing'; }?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('group_name') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_groupname" name="user_groupname" value="<?= $enquiry_details->group_name ?>" />
						</div>
					</div>
					
<!--
					<div class="form-group">
						<label class="control-label col-sm-4" for="startTime">Date & Time</label>
						<div class="bootstrap-timepicker col-sm-8">
							<input type="text" class="datetime form-control" id="startTime" name="startTime" placeholder="Date & Time" />
						</div>
					</div>
-->
				</fieldset>
                
                <fieldset class="col-lg-6 scheduler-border">
					<legend class="scheduler-border"><?= lang('rides') ?></legend>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('booking_no') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->booking_no ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('booking_time') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->booked_on ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('pickup') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->start ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('drop') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->end ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('total_distance') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->distance_km ?>" />
						</div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-4"><?= lang('distance_price') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $enquiry_details->distance_price ?>" />
						</div>
					</div>
                    
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('cab_name') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_groupname" name="user_groupname" value="<?= $enquiry_details->taxi_name ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('cab_type') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_email" name="user_email" value="<?= $enquiry_details->taxi_type_name ?>" />
						</div>
					</div>
					
<!--
					<div class="form-group">
						<label class="control-label col-sm-4" for="startTime">Date & Time</label>
						<div class="bootstrap-timepicker col-sm-8">
							<input type="text" class="datetime form-control" id="startTime" name="startTime" placeholder="Date & Time" />
						</div>
					</div>
-->
				</fieldset>
                
 			</div>
             <?php
			
			if(!empty($follows_details)){
			?>
		   <div class="col-sm-12">
		   <h2 class="box_he_de"><?= lang('follow_up') ?></h2>
			</div>
			<div class="col-sm-12">
			 <div class="experience">
             	<?php
				foreach($follows_details as $follow){
				?>
				<div class="item">
				  <h5 class="company-name"><?= $follow->help_name ?></h5>
				  <div class="job-info">
					<div class="title"><?= $follow->created_on ?></div>
				  </div>
				  <div>
					<ul class="fa-ul">
					  <li><i class="fa-li fa fa-hand-o-right"></i>Admin Name: <?= $follow->first_name ?></li>
					  <li><i class="fa-li fa fa-hand-o-right"></i>Discussion:<?= $follow->discussion ?> </li>
                       <li><i class="fa-li fa fa-hand-o-right"></i>Remark:<?= $follow->remark ?> </li>
					  <li><i class="fa-li fa fa-hand-o-right"></i>Status: 
                      <?php if($follow->status == 0){echo 'Process';}elseif($follow->status == 1){echo 'Open';}elseif($follow->status == 2){echo 'Transfer';}elseif($follow->status == 3){echo 'Close';}elseif($follow->status == 4){echo 'Reopen';}?>
                       <li>
					  
					</ul>
				  </div>
				</div>
				<?php
				}
				?>
			</div>
		   </div>
           <?php
			}
		   ?>
                        
           <?php echo form_close(); ?>
           
        </div>
    </div>
</div>
