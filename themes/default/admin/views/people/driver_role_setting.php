<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("people/driver_role_setting/".$user_id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
						<input type="hidden" name="user_id" value="<?= $user_id ?>">
						<div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped reports-table">

                                <thead>
                                
                                <tr>
                                    <th class="text-center"><?= lang("module_name"); ?></th>
                                    <th class="text-center"><?= lang("permissions"); ?></th>
                                </tr>
                                
                                </thead>
                                <tbody>
                                <tr>
                                    <td><?= lang("incentive"); ?></td>
                                    <td class="text-left">
                                        <ul style="list-style:none;">
                                            <li>
                                            <input type="checkbox" value="1" id="incentive_auto_enable" class="checkbox" name="incentive_auto_enable" <?php echo $role_settings->{'incentive_auto_enable'} ? "checked" : ''; ?>>
                                            <label for="incentive_auto_enable" class="padding05"><?= lang('auto_enable') ?></label>
                                            </li>
                                            
                                    	</ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= lang("ride"); ?></td>
                                    <td class="text-left">
                                        <ul style="list-style:none;">
                                            <li>
                                            <input type="checkbox" value="1" id="ride_stop" class="checkbox" name="ride_stop" <?php echo $role_settings->{'ride_stop'} ? "checked" : ''; ?>>
                                            <label for="ride_stop" class="padding05"><?= lang('ride_stop ') ?></label>
                                            </li>
                                            
                                    	</ul>
                                    </td>
                                </tr>

								</tbody>
                            </table>
                        </div>
                            
                    </div> 
                    
                    
                </div>

               <div class="col-sm-12 last_sa_se"><?php echo form_submit('driver_role_setting', lang('submit'), 'class="btn btn-primary  change_btn_save center-block"'); ?></div>

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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getCountry_bycontinent_rep')?>',
			data: {continent_id: id, designation_id:designation_id, department_id:department_id},
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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getZone_bycountry_rep')?>',
			data: {country_id: id, designation_id:designation_id, department_id:department_id},
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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getState_byzone_rep')?>',
			data: {zone_id: id, designation_id:designation_id, department_id:department_id},
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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getCity_bystate_rep')?>',
			data: {state_id: id, designation_id:designation_id, department_id:department_id},
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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getArea_bycity_rep')?>',
			data: {city_id: id, designation_id:designation_id, department_id:department_id},
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
		var designation_id = $('#designation_id').val();
		var department_id = $('#department_id').val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('people/getReporter_byarea_rep')?>',
			data: {area_id: id, designation_id:designation_id, department_id:department_id},
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