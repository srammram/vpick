<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Profile'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-md-2 col-md-offset-10">
            	<?php
				if($this->session->userdata('group_id') == 3){
				?>
                <a href="<?= admin_url('users/edit_vendor/'.$this->session->userdata('user_id')); ?>" class="profile-edit-btn">Edit Profile</a>
                <?php					
				}elseif($this->session->userdata('group_id') == 4){
				?>
                <a href="<?= admin_url('users/edit_driver/'.$this->session->userdata('user_id')); ?>" class="profile-edit-btn">Edit Profile</a>
                <?php
				}elseif($this->session->userdata('group_id') == 6){
				?>
                <a href="<?= admin_url('users/edit_employee/'.$this->session->userdata('user_id')); ?>" class="profile-edit-btn">Edit Profile</a>
                <?php
				}
				?>
            	
			</div>
            <div class="col-md-3" style="margin-top: 30px;">
				<div class="profile-img">
                	<a href="<?= $user->photo_img ?>" class="without-caption image-link">
                        <img src="<?= $user->photo_img ?>" width="250" height="250" />  
                    </a>
                    <button type="button" class="btn btn-info pull-right"><i class="fa fa-rotate-right"></i></button>
                    
					
				</div>
			</div>
             <div class="col-md-9" style="margin-top: 30px;">
				  <ul id="myTabs" class="nav nav-pills nav-justified" role="tablist" data-tabs="tabs">
                  
					<li class="active"><a href="#General" data-toggle="tab">General</a></li>
                    <?php if($this->session->userdata('group_id') == 6 || $this->session->userdata('group_id') == 3 || $this->session->userdata('group_id') == 4){ ?>
					<li><a href="#Address" data-toggle="tab">Address</a></li>
                    <?php } ?>
                    <?php if($this->session->userdata('group_id') == 6 || $this->session->userdata('group_id') == 3 || $this->session->userdata('group_id') == 4){ ?>
					<li><a href="#Bank" data-toggle="tab">Bank</a></li>
                    <?php } ?>
                    <?php if($this->session->userdata('group_id') == 6 || $this->session->userdata('group_id') == 3 || $this->session->userdata('group_id') == 4){ ?>
					<li><a href="#Document" data-toggle="tab">Document</a></li>
                    <?php } ?>
                    <?php if($this->session->userdata('group_id') == 6){ ?>
                    <li><a href="#Roles" data-toggle="tab">Roles</a></li>
                    <?php } ?>
				  </ul>
				  <div class="tab-content">
                  
					<div role="tabpanel" class="tab-pane fade in active" id="General">
						<table class="table table_section">
							<tbody>
								
								<tr>
									<td width="150px">First Name</td>
									<td><?= $user->first_name ?></td>
								</tr>
								<tr>
									<td>Last Name</td>
									<td><?= $user->last_name ?></td>
								</tr>
								<tr>
									<td>Gender</td>
									<td><?= $user->gender ?></td>
								</tr>
								<tr>
									<td>DOB</td>
									<td><?= $user->dob == '0000-00-00' || $user->dob == NULL ? '' :$user->dob ?></td>
								</tr>
							</tbody>
						</table>
					</div>
                    
					<div role="tabpanel" class="tab-pane fade" id="Address">
						<table class="table table_section">
							<tbody>
								
								<tr>
									<td width="150px">Local Address</td>
									<td><?= $user->local_address ?></td>
								</tr>
								<tr>
									<td>Local Continent</td>
									<td><?= $user->local_continent_name ?></td>
								</tr>
								<tr>
									<td>Local Country</td>
									<td><?= $user->local_country_name ?></td>
								</tr>
								<tr>
									<td>Local Zone</td>
									<td><?= $user->local_zone_name ?></td>
								</tr>
                                <tr>
									<td>Local State</td>
									<td><?= $user->local_state_name ?></td>
								</tr>
                                <tr>
									<td>Local City</td>
									<td><?= $user->local_city_name ?></td>
								</tr>
                                <tr>
									<td>Local Area</td>
									<td><?= $user->local_area_name ?></td>
								</tr>
                                <tr>
									<td>Local Pincode</td>
									<td><?= $user->local_pincode ?></td>
								</tr>
                                <tr>
									<td width="150px">Permanent Address</td>
									<td><?= $user->permanent_address ?></td>
								</tr>
								<tr>
									<td>Permanent Continent</td>
									<td><?= $user->permanent_continent_name ?></td>
								</tr>
								<tr>
									<td>Permanent Country</td>
									<td><?= $user->permanent_country_name ?></td>
								</tr>
								<tr>
									<td>Permanent Zone</td>
									<td><?= $user->permanent_zone_name ?></td>
								</tr>
                                <tr>
									<td>Permanent State</td>
									<td><?= $user->permanent_state_name ?></td>
								</tr>
                                <tr>
									<td>Permanent City</td>
									<td><?= $user->permanent_city_name ?></td>
								</tr>
                                <tr>
									<td>Permanent Area</td>
									<td><?= $user->permanent_area_name ?></td>
								</tr>
                                <tr>
									<td>Permanent Pincode</td>
									<td><?= $user->permanent_pincode ?></td>
								</tr>
							</tbody>
						</table>
					</div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="Bank">
						<table class="table table_section">
							<tbody>
								
								<tr>
									<td width="150px">Account No</td>
									<td><?= $user->account_no ?></td>
								</tr>
								<tr>
									<td>Bank Name</td>
									<td><?= $user->bank_name ?></td>
								</tr>
								<tr>
									<td>Branch Name</td>
									<td><?= $user->branch_name ?></td>
								</tr>
								<tr>
									<td>IFSC Code</td>
									<td><?= $user->ifsc_code ?></td>
								</tr>
							</tbody>
						</table>
					</div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="Document">
						<table class="table table_section">
							<tbody>
								
								<tr>
									<td width="150px">Aadhaar No</td>
									<td><?= $user->aadhaar_no ?></td>
								</tr>
								<tr>
									<td>Pancard No</td>
									<td><?= $user->pancard_no ?></td>
								</tr>
								<tr>
									<td>License DOB</td>
									<td><?= $user->license_dob == '0000-00-00' || $user->license_dob == NULL ? '' :$user->license_dob ?></td>
								</tr>
								<tr>
									<td>License Ward Name</td>
									<td><?= $user->license_ward_name ?></td>
								</tr>
                                 <tr>
									<td>License Country</td>
									<td><?= $user->license_country_id ?></td>
								</tr>
                                <tr>
									<td>License Type</td>
									<td><?= $user->license_type ?></td>
								</tr>
                                <tr>
									<td>License Issuing Authority</td>
									<td><?= $user->license_issuing_authority ?></td>
								</tr>
                                <tr>
									<td>License Issued ON</td>
									<td><?= $user->license_issued_on == '0000-00-00' || $user->license_issued_on == NULL ? '' :$user->license_issued_on ?></td>
								</tr>
                                <tr>
									<td>License Validity</td>
									<td><?= $user->license_validity == '0000-00-00' || $user->license_validity == NULL ? '' :$user->license_validity ?></td>
								</tr>
                                <tr>
									<td>Police ON</td>
									<td><?= $user->police_on == '0000-00-00' || $user->police_on == NULL ? '' :$user->police_on ?></td>
								</tr>
                                <tr>
									<td>Police Til</td>
									<td><?= $user->police_til == '0000-00-00' || $user->police_til == NULL ? '' :$user->police_til ?></td>
								</tr>
                                <tr>
									<td>Loan Information</td>
									<td><?= $user->loan_information ?></td>
								</tr>
                                
							</tbody>
						</table>
					</div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="Roles">
						<table class="table table_section">
							<tbody>
								
								<tr>
									<td width="150px">Department</td>
									<td><?= $user->user_department ?></td>
								</tr>
								<tr>
									<td>Role</td>
									<td><?= $user->position ?></td>
								</tr>
								<tr>
									<td>Continent</td>
									<td><?= $user->continent_name ?></td>
								</tr>
								<tr>
									<td>Country</td>
									<td><?= $user->country_name ?></td>
								</tr>
                                <tr>
									<td>Zone</td>
									<td><?= $user->zone_name ?></td>
								</tr>
                                <tr>
									<td>State</td>
									<td><?= $user->state_name ?></td>
								</tr>
                                <tr>
									<td>City</td>
									<td><?= $user->city_name ?></td>
								</tr>
                                <tr>
									<td>Area</td>
									<td><?= $user->area_name ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					
				  </div>
			</div>
    	</div>
	</div>
</div>
