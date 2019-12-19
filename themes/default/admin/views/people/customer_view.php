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
										<img src="<?= $user->photo_img ?>" id="thumb"  data-large-img-url="<?= $user->photo_img ?>" data-large-img-wrapper="preview" >  
									</a>
								</div>
							</div>
						   <button type="button" class="btn btn-info pull-right change_btn_sec" style="width: 100%;">
								<span class="pull-left">
								<input type="file" id="selectedFile" style="display: none;" />
								<input type="button" value="Change" onclick="document.getElementById('selectedFile').click();" class="change_btn_s" />
							   </span> <i class="fa fa-rotate-right pull-right"></i>
							</button>
							<div class="magnifier-preview" id="preview" style="width: 300px; height:300px;position: absolute;right: -50%;"></div>
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
			
		</div>
	</div>
</div>

