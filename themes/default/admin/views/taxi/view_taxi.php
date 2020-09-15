<div class="col-md-12 col-xs-12 box box_view_sec">
	<div class="row">
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Cab Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td><?= ?> </td>
						<td>:</td>
						<td><?= $taxi->name ?> </td>
					</tr>
                    <tr>
						<td>Model </td>
						<td>:</td>
						<td><?= $taxi->model ?> </td>
					</tr>
                    <tr>
						<td>Number </td>
						<td>:</td>
						<td><?= $taxi->number ?> </td>
					</tr>
                    <tr>
						<td>Engine Number </td>
						<td>:</td>
						<td><?= $taxi->engine_number ?> </td>
					</tr>
                    <tr>
						<td>Chassis Number </td>
						<td>:</td>
						<td><?= $taxi->chassis_number ?> </td>
					</tr>
                    <tr>
						<td>Make </td>
						<td>:</td>
						<td><?= $taxi->make ?> </td>
					</tr>
                    <tr>
						<td>Fuel Type </td>
						<td>:</td>
						<td><?= $taxi->fuel_type ?> </td>
					</tr>
                    <tr>
						<td>Type </td>
						<td>:</td>
						<td><?= $taxi->type_name ?> </td>
					</tr>
                    <tr>
						<td>Color </td>
						<td>:</td>
						<td><?= $taxi->color ?> </td>
					</tr>
                    <tr>
						<td>Manufacture Year </td>
						<td>:</td>
						<td><?= $taxi->manufacture_year ?> </td>
					</tr><tr>
						<td>Cab Mode </td>
						<td>:</td>
						<td>
						<?php
						if($taxi->mode == 1){
							echo 'AvailablewithDriver';
						}elseif($taxi->mode == 2){
							echo 'RidewithDriver';
						}else{
							echo 'AvailablewithoutDriver';
						}
						?> 
                        </td>
					</tr>
                    
					<tr>
						<td>Photo </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->photo_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->photo_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>                        
                        </td>
					</tr>
					<tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->is_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>

				</tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>User Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
              <tbody>
					<tr>
						<td>First Name </td>
						<td>:</td>
						<td><?= $taxi->first_name ?> </td>
					</tr>
                    <tr>
						<td>Last Name </td>
						<td>:</td>
						<td><?= $taxi->last_name ?> </td>
					</tr>
                    <tr>
						<td>Mobile </td>
						<td>:</td>
						<td>(+<?= $taxi->country_code ?>)<?= $taxi->mobile ?> </td>
					</tr>
                    <tr>
						<td>Email</td>
						<td>:</td>
						<td><?= $taxi->email ?> </td>
					</tr>
                    <tr>
						<td>Gender</td>
						<td>:</td>
						<td><?= $taxi->gender ?> </td>
					</tr>
                    
                  </tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>RC Book:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
              <tbody>
					<tr>
						<td>RC Date </td>
						<td>:</td>
						<td><?= $taxi->reg_date == '0000-00-00' || $taxi->reg_date == NULL ? '' :$taxi->reg_date ?></td>
					</tr>
                    <tr>
						<td>RC Due Date </td>
						<td>:</td>
						<td><?= $taxi->reg_due_date == '0000-00-00' || $taxi->reg_due_date == NULL ? '' :$taxi->reg_due_date ?></td>
					</tr>
                    <tr>
						<td>Owner Name </td>
						<td>:</td>
						<td><?= $taxi->reg_owner_name ?> </td>
					</tr>
                    <tr>
						<td>Owner Address </td>
						<td>:</td>
						<td><?= $taxi->reg_owner_address ?> </td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->reg_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->reg_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->reg_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                  </tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Taxation Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
              	<tbody>
                    <tr>
						<td>Amount Paid </td>
						<td>:</td>
						<td><?= $taxi->taxation_amount_paid ?> </td>
					</tr>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->taxation_due_date == '0000-00-00' || $taxi->taxation_due_date == NULL ? '' :$taxi->taxation_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->taxation_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->taxation_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->taxation_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>Ride Types:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td> City Ride</td>
						<td>:</td>
						<td><?= $taxi->is_daily == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Rental Ride </td>
						<td>:</td>
						<td><?= $taxi->is_rental == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Outstation  Ride</td>
						<td>:</td>
						<td><?= $taxi->is_outstation == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <!--<tr>
						<td> Hiring Driver</td>
						<td>:</td>
						<td><?= $taxi->is_hiring == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Corporate </td>
						<td>:</td>
						<td><?= $taxi->is_corporate == 1 ? 'Yes' : 'No' ?> </td>
					</tr>-->
                    
                   
                   
                   </tbody>
			  </table>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Insurance Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Policy NO </td>
						<td>:</td>
						<td><?= $taxi->insurance_policy_no ?> </td>
					</tr>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->insurance_due_date == '0000-00-00' || $taxi->insurance_due_date == NULL ? '' :$taxi->insurance_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->insurance_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->insurance_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->insurance_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Permit Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Permit NO </td>
						<td>:</td>
						<td><?= $taxi->permit_no ?> </td>
					</tr>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->permit_due_date == '0000-00-00' || $taxi->permit_due_date == NULL ? '' :$taxi->permit_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->permit_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->permit_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->permit_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Authorisation Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Authorisation NO </td>
						<td>:</td>
						<td><?= $taxi->authorisation_no ?> </td>
					</tr>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->authorisation_due_date == '0000-00-00' || $taxi->authorisation_due_date == NULL ? '' :$taxi->authorisation_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->authorisation_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->authorisation_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->authorisation_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
            </fieldset>
			<fieldset class="filed_sec">
			  <legend>Fitness Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->fitness_due_date == '0000-00-00' || $taxi->fitness_due_date == NULL ? '' :$taxi->fitness_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->fitness_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->fitness_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->fitness_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
            </fieldset>
			<fieldset class="filed_sec">
			  <legend>Speed Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->speed_due_date == '0000-00-00' || $taxi->speed_due_date == NULL ? '' :$taxi->speed_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->speed_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->speed_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->speed_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
            </fieldset>
			<fieldset class="filed_sec">
			  <legend>PUC Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
					<tbody>
                    <tr>
						<td>Due Date </td>
						<td>:</td>
						<td><?= $taxi->puc_due_date == '0000-00-00' || $taxi->puc_due_date == NULL ? '' :$taxi->puc_due_date ?></td>
					</tr>
                    <tr>
						<td>Document </td>
						<td>:</td>
						<td>
                        <a href="<?= $taxi->puc_image_img ?>" class="without-caption image-link">
                            <img src="<?= $taxi->puc_image_img ?>" width="250" height="250" />  
                        </a>
                        <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>  
                        </td>
					</tr>
                    <tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $taxi->puc_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
                   </tbody>
			  </table>
			</fieldset>
            
            
		</div>
	</div>
</div>

