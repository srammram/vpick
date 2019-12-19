<div class="col-md-12 col-xs-12 box box_view_sec">
	<div class="row">
		<div class="col-md-6">
        	<?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/vendor_allocate/".$id, $attrib);
                ?>
               <input type="hidden" name="id" value="<?= $id ?>">
               <div class="col-xs-12">
               		<h2 class="row">Zone Users</h2>
                   <div class="form-group">
                        <?= lang("zone_associated", "associated_id"); ?>
                        
                       <?php
                       $con[''] = 'Select Associated';
                        foreach ($zones as $zone) {
                            $con[$zone->user_id] = $zone->first_name;
                        }
                        echo form_dropdown('associated_id', $con, '', 'class="form-control  " id="associated_id" '); ?>
                    </div>
                </div>            
               <?php echo form_submit('zone_allocate', lang('zone_allocate'), 'class="btn btn-primary pull-right"'); ?> 
             <?php echo form_close(); ?>
             
			
		</div>
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>User Details & Address:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
                	<tr>
						<td>First Name *</td>
						<td>:</td>
						<td><?= $user->first_name ?> </td>
					</tr>
					<tr>
						<td>Email *</td>
						<td>:</td>
						<td><?= $user->email ?></td>
					</tr>
					
					<tr>
						<td>mobile *</td>
						<td>:</td>
						<td>(+<?= $user->country_code ?>) ****** <?= substr($user->mobile, -4) ?></td>
					</tr>
                     <tr>
						<td>Gender</td>
						<td>:</td>
						<td><?= $user->gender ?> </td>
					</tr>
					<tr>
						<td>Address *</td>
						<td>:</td>
						<td><?= $user->local_address ?></td>
					</tr>
					<tr>
						<td>continent</td>
						<td>:</td>
						<td><?= $user->local_continent_name ?> </td>
					</tr>
					<tr>
						<td>Country</td>
						<td>:</td>
						<td><?= $user->local_country_name?></td>
					</tr>
					<tr>
						<td>zone</td>
						<td>:</td>
						<td><?= $user->local_zone_name ?></td>
					</tr>
					<tr>
						<td>State</td>
						<td>:</td>
						<td><?= $user->local_state_name ?></td>
					</tr>
					<tr>
						<td>City</td>
						<td>:</td>
						<td><?= $user->local_city_name ?></td>
					</tr>
					<tr>
						<td>area</td>
						<td>:</td>
						<td><?= $user->local_area_name ?></td>
					</tr>
				</tbody>
			  </table>
			</fieldset>
            
		</div>
	</div>
</div>

