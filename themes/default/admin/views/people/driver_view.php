<div class="col-md-12 col-xs-12 box box_view_sec">
	<div class="row">
		<div class="col-md-6">
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
						<td>First Name *</td>
						<td>:</td>
						<td><?= $user->first_name ?> </td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td>:</td>
						<td><?= $user->last_name ?> </td>
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
						<td>DOB</td>
						<td>:</td>
						<td><?= $user->dob == '0000-00-00' || $user->dob == NULL ? '' : $user->dob ?> </td>
					</tr>
                    <tr>
						<td>Photo</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->photo_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->photo_img ?>" class="img" data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview">  
									</a>
								</div>
							</div>
							<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
					<tr>
						<td>approved</td>
						<td>:</td>
						<td><?= $user->user_approved == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>

				</tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Local Address:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
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
                    <tr>
						<td>pincode</td>
						<td>:</td>
						<td><?= $user->local_pincode ?></td>
					</tr>
					<tr>
						<td>local Verify</td>
						<td>:</td>
						<td><?= $user->local_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
				</tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Permanent Address:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Address *</td>
						<td>:</td>
						<td><?= $user->permanent_address ?> </td>
					</tr>
					<tr>
						<td>continent</td>
						<td>:</td>
						<td><?= $user->permanent_continent_name ?> </td>
					</tr>
					<tr>
						<td>Country</td>
						<td>:</td>
						<td><?= $user->permanent_country_name ?></td>
					</tr>
					<tr>
						<td>zone</td>
						<td>:</td>
						<td><?= $user->permanent_zone_name ?></td>
					</tr>
					<tr>
						<td>State</td>
						<td>:</td>
						<td><?= $user->permanent_state_name ?></td>
					</tr>
					<tr>
						<td>City</td>
						<td>:</td>
						<td><?= $user->permanent_city_name ?></td>
					</tr>
					<tr>
						<td>area</td>
						<td>:</td>
						<td><?= $user->permanent_area_name ?></td>
					</tr>
                    <tr>
						<td>pincode</td>
						<td>:</td>
						<td><?= $user->permanent_pincode ?></td>
					</tr>
					<tr>
						<td>permanent Verify</td>
						<td>:</td>
						<td><?= $user->permanent_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
				</tbody>
			  </table>
			</fieldset>
		</div>
		<div class="col-md-6">
			<fieldset class="filed_sec">
			  <legend>Bank Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Account Holder Name</td>
						<td>:</td>
						<td><?= $user->account_holder_name ?></td>
					</tr>
					<tr>
						<td>account No</td>
						<td>:</td>
						<td><?= $user->account_no ?></td>
					</tr>
					<tr>
						<td>bank Name</td>
						<td>:</td>
						<td><?= $user->bank_name ?></td>
					</tr>
					<tr>
						<td>branch Name</td>
						<td>:</td>
						<td><?= $user->branch_name ?></td>
					</tr>
					<tr>
						<td>ifsc Code</td>
						<td>:</td>
						<td><?= $user->ifsc_code ?></td>
					</tr>
                    <tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->account_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>

				</tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Aadhar Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Document</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->aadhaar_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->aadhaar_image_img ?>" class="img"  data-large-img-url="<?= $user->aadhaar_image_img ?>" data-large-img-wrapper="preview1">  
									</a>
								</div>
							</div>
						   <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview1" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
					<tr>
						<td>Aadhaar Number</td>
						<td>:</td>
						<td><?= $user->aadhaar_no ?></td>
					</tr>
					<tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->aadhar_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
					

				</tbody>
			  </table>
			</fieldset>
			<fieldset class="filed_sec">
			  <legend>Pancard Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Document</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->pancard_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->pancard_image_img ?>" class="img"  data-large-img-url="<?= $user->pancard_image_img ?>" data-large-img-wrapper="preview2">  
									</a>
								</div>
							</div>
							<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview2" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
					<tr>
						<td>Pancard Number</td>
						<td>:</td>
						<td><?= $user->pancard_no ?></td>
					</tr>
					<tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->pancard_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
					

				</tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>License_image Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Document</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->license_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->license_image_img ?>" class="img"  data-large-img-url="<?= $user->license_image_img ?>" data-large-img-wrapper="preview3">  
									</a>
								</div>
							</div>
								
						   	<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview3" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
                    <tr>
						<td>License DOB</td>
						<td>:</td>
						<td><?= $user->license_dob == '0000-00-00' || $user->license_dob == NULL ? '' : $user->license_dob ?> </td>
					</tr>
					<tr>
						<td>License No</td>
						<td>:</td>
						<td><?= $user->license_no ?></td>
					</tr>
                    <tr>
						<td>Ward Name</td>
						<td>:</td>
						<td><?= $user->license_ward_name ?></td>
					</tr>
                    <!--<tr>
						<td>License Country</td>
						<td>:</td>
						<td><?= $user->license_country_id ?></td>
					</tr>-->
                    <tr>
						<td>License Type</td>
						<td>:</td>
						<td><?= $user->license_type ?></td>
					</tr>
                    <tr>
						<td>Issuing Authority</td>
						<td>:</td>
						<td><?= $user->license_issuing_authority ?></td>
					</tr>
                    <tr>
						<td>Issued ON</td>
						<td>:</td>
						<td><?= $result->dob == '0000-00-00' || $result->dob == NULL ? '' : $user->license_issued_on ?></td>
					</tr>
                    <tr>
						<td>Validity</td>
						<td>:</td>
						<td><?= $user->license_validity == '0000-00-00' || $user->license_validity == NULL ? '' : $user->license_validity ?></td>
					</tr>
					<tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->license_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
					

				</tbody>
			  </table>
			</fieldset>
            <fieldset class="filed_sec">
			  <legend>Police Details:</legend>
			  <table class="table table_section">
			  <colgroup>
				<col style="width:48%">
				<col style="width:2%">
				<col style="width:50%">
			  </colgroup>
				<tbody>
					<tr>
						<td>Document</td>
						<td>:</td>
						<td>
							<div class="col-sm-12 img_box_se_head">
								<div class="img_box_se">
									<a href="<?= $user->police_image_img ?>" class="without-caption image-link magnifier-thumb-wrapper">
										<img src="<?= $user->police_image_img ?>" class="img"  data-large-img-url="<?= $result->permanent_image_img ?>" data-large-img-wrapper="preview4">  
									</a>
								</div>
							</div>
							<button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview4" style="width: 300px; height:300px;position: absolute;right: -80%;"></div>
						</td>
					</tr>
					<tr>
						<td>Police ON</td>
						<td>:</td>
						<td><?= $user->police_on == '0000-00-00' || $user->police_on == NULL ? '' : $user->police_on ?> </td>
					</tr>
                    <tr>
						<td>Police Til</td>
						<td>:</td>
						<td><?= $user->police_til == '0000-00-00' || $user->police_til == NULL ? '' : $user->police_til ?> </td>
					</tr>
                    
					<tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->police_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
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
						<td><?= $user->is_daily == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Rental Ride </td>
						<td>:</td>
						<td><?= $user->is_rental == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Outstation  Ride</td>
						<td>:</td>
						<td><?= $user->is_outstation == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Hiring Driver</td>
						<td>:</td>
						<td><?= $user->is_hiring == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                    <tr>
						<td> Corporate </td>
						<td>:</td>
						<td><?= $user->is_corporate == 1 ? 'Yes' : 'No' ?> </td>
					</tr>
                    
                   
                   
                   </tbody>
			  </table>
			</fieldset>
		</div>
	</div>
</div>

