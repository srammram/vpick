<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/add_employee", $attrib);
                ?>
                <div class="row">
                <div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
					</div>
                </div>
                	
                    <div class="col-md-12">
						<h2 class="box_he_de"><?= lang('login_details') ?></h2>  
						<div class="form-group col-sm-12">
							 <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('password', 'password'); ?>
                                <div class="controls">
                                    <?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" '); ?>
                                </div>
                            </div>

                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('confirm_password', 'confirm_password'); ?>
                                <div class="controls">
                                    <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                </div>
                            </div>
						</div>
                          
						<h2 class="box_he_de"><?= lang('user_details') ?></h2>  
						<div class="form-group col-sm-12">
							 <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <input type="text" id="first_name" onkeyup="inputFirstUpper(this)" name="first_name" class="form-control" required="required"/>
                                </div>
                                
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <input type="text" id="last_name" onkeyup="inputFirstUpper(this)" name="last_name" class="form-control"/>
                                </div>
                            </div>
                                                        
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('country_code', 'country_code'); ?>
                                <?php
								
                                $cc[''] = 'Select Country Code';
								foreach ($country_code as $cc_row) {
									$cc[$cc_row->phonecode] = '(+'.$cc_row->phonecode.') '.$cc_row->name;
								}
								
                                echo form_dropdown('country_code', $cc, $commoncountry->phonecode ? $commoncountry->phonecode : '', 'class="tip form-control" id="country_code" data-placeholder="' . lang("select") . ' ' . lang("country_code") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('mobile', 'mobile'); ?>
                                <div class="controls">
                                    <input type="text" id="mobile" name="mobile" class="form-control" required="required"/>
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'), 'others' => lang('others'));
                                echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>
                             <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="text" id="email" name="email" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_email') ?></p>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <input type="text" id="dob" name="dob" onkeypress="dateCheck(this);" class="form-control" required="required"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_date') ?></p>
                            </div>
						</div>
                    
						<h2 class="box_he_de"><?= lang('local_address') ?></h2>  
						<div class="form-group col-sm-12">
                       		
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('address', 'local_address'); ?>
                                <div class="controls">
                                    <input type="text" id="local_address" name="local_address" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'local_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="local_pincode" onkeyup="inputUpper(this)" name="local_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("address_doc", "local_image") ?>
                                <input id="local_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="local_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                        </div> 
                        
						<h2 class="box_he_de"><?= lang('permanent_address') ?></h2>  
						<div class="form-group col-sm-12">
                            <div class="form-group  col-sm-3 col-xs-12">
                                <?php echo lang('address', 'permanent_address'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_address" name="permanent_address" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group  col-sm-3 col-xs-12">
                                <?php echo lang('pincode', 'permanent_pincode'); ?>
                                <div class="controls">
                                    <input type="text" id="permanent_pincode" onkeyup="inputUpper(this)" name="permanent_pincode" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("address_doc", "permanent_image") ?>
                                <input id="permanent_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="permanent_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
						</div>
                            
                    </div> 
                    
                    <div class="col-md-12">
						<h2 class="box_he_de"><?= lang('work_details') ?></h2>
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('department', 'department'); ?>
                                <?php
                                $dep[''] = 'Select Department';
								foreach ($user_department as $department) {
									$dep[$department->id] = $department->name;
								}
								
                                echo form_dropdown('department_id', $dep, '', 'class="tip form-control" id="department_id" data-placeholder="' . lang("select") . ' ' . lang("department") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('designation', 'designation'); ?>
                                <?php
                                $des[''] = 'Select Designation';
								foreach ($user_designation as $designation) {
									$des[$designation->access_area] = $designation->position;
								}
								
                                echo form_dropdown('designation_id', $des, '', 'class="tip form-control select-designation" id="designation_id" data-placeholder="' . lang("select") . ' ' . lang("designation") . '" required="required"');
                                ?>
                            </div>
                            
                            <div class="col-sm-3 col-xs-12 form-group location continents" >
                                <?= lang("continent", "continent"); ?>
                               <?php
                                echo form_dropdown('continent_id', '', '', 'class="form-control select-continent " id="continent_id" '); ?>
                            </div>
                            
                           
                            <div class="col-sm-3 col-xs-12 form-group location countries">
                                <?= lang("country", "country"); ?>
                               <?php
                                echo form_dropdown('country_id', '', '', 'class="form-control select-country " id="country_id" '); ?>
                            </div>
                            
                            <div class="col-sm-3 col-xs-12 form-group location zones">
                                <?= lang("zone", "zone"); ?>
                               <?php
                                echo form_dropdown('zone_id', '', '', 'class="form-control select-zone " id="zone_id" '); ?>
                            </div>
                            
                            <div class=" col-sm-3 col-xs-12 form-group location states">
                                <?= lang("state", "state"); ?>
                               <?php
                                echo form_dropdown('state_id', '', '', 'class="form-control select-state " id="state_id" '); ?>
                            </div>
                            
                            <div class="col-sm-3 col-xs-12 form-group location cities">
                                <?= lang("city", "city"); ?>
                               <?php
                                echo form_dropdown('city_id', '', '', 'class="form-control select-city " id="city_id" '); ?>
                            </div>
                            <div class=" col-sm-3 col-xs-12form-group location areas">
                                <?= lang("area", "area"); ?>
                               <?php
                                echo form_dropdown('area_id', '', '', 'class="form-control select-area " id="area_id" '); ?>
                            </div>
                            <div class=" col-sm-3 col-xs-12form-group location reporter">
                                <?= lang("reporter", "reporter"); ?>
                               <?php
                                echo form_dropdown('reporter_id', '', '', 'class="form-control select-reporter " id="reporter_id" required="required"'); ?>
                            </div>
                            
						</div>
                            
						<h2 class="box_he_de"><?= lang('bank_details') ?></h2>
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_holder_name', 'account_holder_name'); ?>
                                <div class="controls">
                                    <input type="text" id="account_holder_name" onkeyup="inputFirstUpper(this)" name="account_holder_name"  class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('account_no', 'account_no'); ?>
                                <div class="controls">
                                    <input type="text" id="account_no" name="account_no" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('bank_name', 'bank_name'); ?>
                                <div class="controls">
                                    <input type="text" id="bank_name" onkeyup="inputFirstUpper(this)" name="bank_name" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('branch_name', 'branch_name'); ?>
                                <div class="controls">
                                    <input type="text" id="branch_name" onkeyup="inputFirstUpper(this)" name="branch_name" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('ifsc_code', 'ifsc_code'); ?>
                                <div class="controls">
                                    <input type="text" id="ifsc_code" onkeyup="inputUpper(this)" name="ifsc_code" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_ifsc') ?></p>
                                
                            </div>
						</div>
                            
                            
						<h2 class="box_he_de"><?= lang('doc_details') ?></h2> 
						<div class="form-group col-sm-12">
							<div class="form-group col-sm-3 col-xs-12 all">
								<?= lang("aadhaar_doc", "aadhaar_image") ?>
                                <input id="aadhaar_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="aadhaar_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                                     
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('aadhaar_no', 'aadhaar_no'); ?>
                                <div class="controls">
                                    <input type="text" id="aadhaar_no" name="aadhaar_no" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_aadhaar') ?></p> 
                            </div>
                            
                            <div class="form-group all col-sm-3 col-xs-12">
								<?= lang("pancard_doc", "pancard_image") ?>
                                <input id="pancard_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="pancard_image" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                            
                            <div class="form-group col-sm-3 col-xs-12">
                                <?php echo lang('pancard_no', 'pancard_no'); ?>
                                <div class="controls">
                                    <input type="text" id="pancard_no" onkeyup="inputUpper(this)" name="pancard_no" class="form-control"/>
                                </div>
                                <p class="help-block-small"><?= lang('ex_pancard') ?></p> 
                            </div>
						</div>
                    </div>
                </div>

               <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_employee', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>

	
$(document).ready(function(){
	
	$('.location').hide();
	$('.select-designation').change(function(){
		$(".select-continent").select2("destroy");
		$(".select-country").select2("destroy");
		$(".select-zone").select2("destroy");
		$(".select-state").select2("destroy");
		$(".select-city").select2("destroy");
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		
		if(id == 'continents'){
			$('.location').hide();
			$('.continents').show();
			$('.reporter').show();
		}else if(id == 'countries'){
			$('.location').hide();
			$('.continents').show();
			$('.countries').show();
			$('.reporter').show();
		}else if(id == 'zones'){
			$('.location').hide();
			$('.continents').show();
			$('.countries').show();
			$('.zones').show();
			$('.reporter').show();
		}else if(id == 'states'){
			$('.location').hide();
			$('.continents').show();
			$('.countries').show();
			$('.zones').show();
			$('.states').show();
			$('.reporter').show();
		}else if(id == 'cities'){
			$('.location').hide();
			$('.continents').show();
			$('.countries').show();
			$('.zones').show();
			$('.states').show();
			$('.cities').show();
			$('.reporter').show();
		}else if(id == 'areas'){
			$('.location').hide();
			$('.continents').show();
			$('.countries').show();
			$('.zones').show();
			$('.states').show();
			$('.cities').show();
			$('.areas').show();
			$('.reporter').show();
		}else{
			$('.location').hide();
		}
		
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		if(designation_id == '' || department_id == ''){
			$(".select-designation").select2("val", "");
			bootbox.alert('please check designation');
       		return;
		}
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getContinent_byuser_rep')?>',
			data: {designation_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Continent</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-continent").html($option);
				$(".select-country").html('<option value="">Select Country</option>');
				$(".select-zone").html('<option value="">Select Zone</option>');
				$(".select-state").html('<option value="">Select State</option>');
				$(".select-city").html('<option value="">Select City</option>');
				$(".select-area").html('<option value="">Select Area</option>');
				$(".select-reporter").html('<option value="">Select Reporter</option>');
				$(".select-continent").select2();
				$(".select-country").select2();
				$(".select-zone").select2();
				$(".select-state").select2();
				$(".select-city").select2();
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-continent').change(function(){
		$(".select-country").select2("destroy");
		$(".select-zone").select2("destroy");
		$(".select-state").select2("destroy");
		$(".select-city").select2("destroy");
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		
		var id = $(this).val();
		var location_id = 0;
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getCountry_bycontinent_rep')?>',
			data: {continent_id: id, location_id:location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				
				$(".select-country").html($loc);
				$(".select-zone").html('<option value="">Select Zone</option>');
				$(".select-state").html('<option value="">Select State</option>');
				$(".select-city").html('<option value="">Select City</option>');
				$(".select-area").html('<option value="">Select Area</option>');
				$(".select-reporter").html($rep);
				$(".select-country").select2();
				$(".select-zone").select2();
				$(".select-state").select2();
				$(".select-city").select2();
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-country').change(function(){
		
		$(".select-zone").select2("destroy");
		$(".select-state").select2("destroy");
		$(".select-city").select2("destroy");
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		var location_id = $('#continent_id').val();
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getZone_bycountry_rep')?>',
			data: {country_id: id, location_id: location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$(".select-zone").html($loc);
				$(".select-state").html('<option value="">Select State</option>');
				$(".select-city").html('<option value="">Select City</option>');
				$(".select-area").html('<option value="">Select Area</option>');
				$(".select-reporter").html($rep);
				$(".select-zone").select2();
				$(".select-state").select2();
				$(".select-city").select2();
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-zone').change(function(){
		$(".select-state").select2("destroy");
		$(".select-city").select2("destroy");
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		var location_id = $('#country_id').val();
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getState_byzone_rep')?>',
			data: {zone_id: id, location_id: location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$(".select-state").html($loc);
				$(".select-city").html('<option value="">Select City</option>');
				$(".select-area").html('<option value="">Select Area</option>');
				$(".select-reporter").html($rep);
				$(".select-state").select2();
				$(".select-city").select2();
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-state').change(function(){
		$(".select-city").select2("destroy");
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		var location_id = $('#zone_id').val();
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getCity_bystate_rep')?>',
			data: {state_id: id, location_id: location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$(".select-city").html($loc);
				$(".select-area").html('<option value="">Select Area</option>');
				$(".select-reporter").html($rep);
				$(".select-city").select2();
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-city').change(function(){
		$(".select-area").select2("destroy");
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		var location_id = $('#state_id').val();
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getArea_bycity_rep')?>',
			data: {city_id: id, location_id: location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$(".select-area").html($loc);
				$(".select-reporter").html($rep);
				$(".select-area").select2();
				$(".select-reporter").select2();
			}
		})
	});
	
	$('.select-area').change(function(){
		$(".select-reporter").select2("destroy");
		var id = $(this).val();
		var location_id = $('#city_id').val();
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		var is_country = $('#is_country').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getReporter_byarea_rep')?>',
			data: {area_id: id, location_id: location_id, designation_id:designation_id, department_id:department_id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$rep = '<option value="">Select Reporter</option>';
				$.each(scdata.rep,function(n,v){
					$rep += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				$loc = '<option value="">Select Country</option>';
				$.each(scdata.loc,function(n,v){
					$loc += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-reporter").html($rep);
				$(".select-reporter").select2();
			}
		})
	});
	
});
</script>
<script>
$(document).ready(function(){
	$('.is_country').change(function(){
		
		var site = '<?php echo site_url() ?>';
		var id = '<?= $id ?>';
		var is_country = $(this).val();
		window.location.href = site+"admin/people/add_employee/?is_country="+is_country;
		
	});
});
</script>