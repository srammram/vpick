

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <?php $attrib = array('class' => 'form-horizontal','class' => 'bank_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/add_company", $attrib);
                ?>
                <div class="row">
               	  <div class="instance_country col-sm-12">
                	  <div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select  class="form-control select is_country"  name="is_country" id="is_country">
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
						<h2 class="box_he_de">Details</h2>
                        <div class="form-group col-sm-3 col-xs-12">
                            <?php echo lang('office', 'office'); ?>
                            <div class="controls">
                                <select class="form-control select is_office"  name="is_office" id="is_office">
                                    
                                    
                                    <option value="0">Head Office</option>
                                    <option value="1">Branch Office</option>
                                   
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-3 col-xs-12 office hidden">
                            <?= lang("branch", "branch"); ?>
                           <?php
                            echo form_dropdown('branch_id', '', '', 'class="form-control select" id="branch_id" required="required"'); ?>
                        </div>
                        
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('company_name', 'company_name'); ?>
							<div class="controls">
								<input type="text" id="name" name="name"  class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('register_number', 'register_number'); ?>
							<div class="controls">
								<input type="text" id="register_number" name="register_number"  class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('starting_year', 'starting_year'); ?>
							<div class="controls">
								<input type="text" id="starting_year" name="starting_year"  class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('email', 'email'); ?>
							<div class="controls">
								<input type="text" id="email" name="email"  class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('telephone', 'telephone'); ?>
							<div class="controls">
								<input type="text" id="telephone" name="telephone"  class="form-control"/>
							</div>
						</div>
                        <div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('fax', 'fax'); ?>
							<div class="controls">
								<input type="text" id="fax" name="fax"  class="form-control"/>
							</div>
						</div>
                            
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('bank', 'bank'); ?>
							<?php
						   $b[''] = 'Select Bank';
							foreach ($AllBanks as $value) {
								if($value->account_type == 0){
									$bank_name = 'Bank -'.$value->account_no.'-'.$value->bank_name;
									$b[$value->id] = $bank_name;
								}else{
									$bank_name = 'Cash -'.$value->account_no;
									$b[$value->id] = $bank_name;
								}
								
							}
                            echo form_dropdown('bank_id[]', $b, '', 'class="form-control select" id="bank_id" multiple required="bank_id"'); ?>
						</div>
						
						

					</div>    
                    
					  <div class="col-md-12">  
						<h2 class="box_he_de">Location</h2>
                        
                        
                        
                            
						<div class="form-group col-sm-3 col-xs-12">
							<?php echo lang('address', 'address'); ?>
							<div class="controls">
								<input type="text" id="address" name="address"  class="form-control"/>
                                <input type="hidden" name="lat" id="lat">
								<input type="hidden" name="lng" id="lng">
							</div>
						</div>
						
						

					</div>         
                      
                       
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('add_company', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBxxnqAHBqmceXMT1YwJsuEvx40yXPqG3M&sensor=false&libraries=places"></script>
<script>
function initAutocomplete() {
	var input = document.getElementById('address');
	var autocomplete = new google.maps.places.Autocomplete(input);
	google.maps.event.addListener(autocomplete, 'place_changed', function () {
		var place = autocomplete.getPlace();
		$('#lat').val(place.geometry.location.lat());
		$('#lng').val(place.geometry.location.lng());
	});
}
$(document).ready(function(e) {
	initAutocomplete();
});
</script>
<script language="javascript">
	$(document).ready(function () {
		$('#is_country').change(function(e) {
			var site = '<?php echo site_url() ?>';
			var is_country = $('#is_country').val();
			
			window.location.href = site+"admin/masters/add_company?is_country="+is_country;
			
		});

	});
</script>
<script>
$(document).on('change', '#account_type', function(){
	var b = $(this).val();
	if(b == 0){
		$('.bank').removeClass('hidden');
	}else{
		$('.bank').addClass('hidden');
	}
	//$('.add_from').bootstrapValidator('resetForm', true);
});
</script>
<script>
$(document).ready(function(){
	$("#branch_id").select2();
	$('.is_office').change(function(){
		var is_office = $(this).val();
		var is_country = $('#is_country').val();
		if(is_office == 0){
			$('.office').addClass('hidden');
			$('#register_number').prop('readonly', false);
		}else{
			$('#register_number').prop('readonly', true);
			$('.office').removeClass('hidden');
			$("#branch_id").select2("destroy");
			$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getBranch')?>',
			data: {is_country: is_country, is_office: is_office},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Branch</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$("#branch_id").html($option);				
				$("#branch_id").select2();
				
			}
		})
		}
	});
	
	$('#branch_id').change(function(){
		var branch_id = $(this).val();
		
			$('.office').removeClass('hidden');
			$("#branch_id").select2("destroy");
			$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getBranchID')?>',
			data: {branch_id: branch_id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$('#register_number').prop('readonly', true);
				$('#name').val(scdata.name);
				$('#register_number').val(scdata.register_number);
				$('#starting_year').val(scdata.starting_year);
				$('#email').val(scdata.email);
				$('#telephone').val(scdata.telephone);
				$('#fax').val(scdata.fax);
			}
		})
		
	});
	
	
});
</script>
