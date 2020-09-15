
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12 user_details_sec">
			
                <fieldset class="col-lg-6 scheduler-border">
					<legend class="scheduler-border"><?= lang('user_details') ?></legend>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('name') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_name" name="user_name" value="<?= $user_details->first_name ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('mobile') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_mobile" name="user_mobile" value="<?= '+'.$user_details->country_code.' '.$user_details->mobile ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4"><?= lang('group_name') ?></label>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="user_groupname" name="user_groupname" value="<?= $user_details->group_name ?>" />
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

        </div>
        <div class="row">
        	<div class="col-sm-12 user_datatables">
        		<h2 class="box_he_de"><?= lang('enquiry_list') ?></h2>
        		 <div class="table-responsive col-sm-12">
                    <table class="table table-bordered table-hover table-striped dataTable">
                        <thead>
                        <tr>
                        	<th> <?= lang('type') ?> </th>
                            <th ><?= lang('code') ?></th>
                            <th ><?=  lang('date') ?></th>
                            <th ><?=  lang('type') ?></th>
                            <th ><?= lang('status') ?></th>
                            <th ><?= lang('cus_status') ?></th>
                            <th><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
						foreach($enquiry as $row){
						?>
                        <tr>
                        <td><?= $row->enquiry_type ?></td>
                        <td><?= $row->enquiry_code ?></td>
                        <td><?= $row->enquiry_date ?></td>
                        <td><?= $row->help ?></td>
                        <td>
                        <?php if($row->enquiry_status == 0){echo '<a href="'.admin_url('enquiry/open/'.$row->id).'">Process</a>';}elseif($row->enquiry_status == 1){echo '<a href="'.admin_url('enquiry/close_transfer/'.$row->id).'">Open</a>';}elseif($row->enquiry_status == 2){echo '<a href="'.admin_url('enquiry/open/'.$row->id).'">Transfer</a>';}elseif($row->enquiry_status == 3){echo '<a href="'.admin_url('enquiry/reopen/'.$row->id).'">Close</a>';}elseif($row->enquiry_status == 4){echo '<a href="'.admin_url('enquiry/close_transfer/'.$row->id).'">Reopen</a>';}?>
                        </td>
                        <td>
                        <?php if($row->customer_status == 1){echo 'Waiting for Customer';}elseif($row->customer_status == 2){echo 'Customer Accepted';}elseif($row->customer_status == 3){echo 'Customer Not Accepted ';}else{ echo 'Support Team is Processing'; }?>
                        </td>
                        <td><a href='<?= admin_url('enquiry/enquiry_view/'.$row->id) ?>' data-toggle='tooltip'  data-original-title='' aria-describedby='tooltip' title='Click here to full details'  ><div class='kapplist-view1'></div></a></td>
                        </tr>
                        <?php 
						}
						?>
                        </tbody>
                       
                    </table>
                </div>
        	</div>
        </div>
    </div>
</div>
