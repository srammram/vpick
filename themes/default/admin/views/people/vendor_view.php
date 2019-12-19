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
									<a href="<?= $user->photo_img ?>" class="without-caption image-link">
										<img src="<?= $user->photo_img ?>" />  
									</a>
								</div>
							</div>
                            <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
						</td>
					</tr>
                    <tr>
						<td>GST *</td>
						<td>:</td>
						<td><?= $user->gst ?></td>
					</tr>
                    <tr>
						<td>Telephone *</td>
						<td>:</td>
						<td><?= $user->telephone_number ?></td>
					</tr>
                    <tr>
						<td>Legal Entity *</td>
						<td>:</td>
						<td><?= $user->legal_entity ?></td>
					</tr>
                    <tr>
						<td>Associated *</td>
						<td>:</td>
						<td><?= $user->associated_name ?></td>
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
									<a href="<?= $user->aadhaar_image_img ?>" class="without-caption image-link">
									<img src="<?= $user->aadhaar_image_img ?>" />  
									</a>
								</div>
							</div>
                            <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
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
									<a href="<?= $user->pancard_image_img ?>" class="without-caption image-link">
										<img src="<?= $user->pancard_image_img ?>"/>  
									</a>
								</div>
							</div>
                            <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
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
			  <legend>Loan Details:</legend>
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
									<a href="<?= $user->loan_doc_img ?>" class="without-caption image-link">
										<img src="<?= $user->loan_doc_img ?>"/>  
									</a>
								</div>
							</div>
                            <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
						</td>
					</tr>
					<tr>
						<td>Loan Information</td>
						<td>:</td>
						<td><?= $user->loan_information ?></td>
					</tr>
					<tr>
						<td>is Verify</td>
						<td>:</td>
						<td><?= $user->loan_verify == 1 ? '<p class="btn btn-success">Active</p>' : '<p class="btn btn-danger">Inactive</p>' ?></td>
					</tr>
					

				</tbody>
			  </table>
			</fieldset>
		</div>
	</div>
</div>

