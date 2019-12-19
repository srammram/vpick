



<script>
$('form[class="edit_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                   
                }
            },
            
           
            
        },
        submitButtons: 'input[type="submit"]'
    });
	
	
	$(document).ready(function(){
	/*Local address*/
	$('.select-local-continent').change(function(){
		
		$(".select-local-country").select2("destroy");
		$(".select-local-zone").select2("destroy");
		$(".select-local-state").select2("destroy");
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		
		var id = $(this).val();
	
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getCountry_bycontinent')?>',
			data: {continent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Country</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				
				
				$(".select-local-country").html($option);
				$(".select-local-zone").html('<option value="">Select Zone</option>');
				$(".select-local-state").html('<option value="">Select State</option>');
				$(".select-local-city").html('<option value="">Select City</option>');
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-country").select2();
				$(".select-local-zone").select2();
				$(".select-local-state").select2();
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	$('.select-local-country').change(function(){
		$(".select-local-zone").select2("destroy");
		$(".select-local-state").select2("destroy");
		$(".select-local-city").select2("destroy");
		$(".select-local-area").select2("destroy");
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Zone</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-local-zone").html($option);
				$(".select-local-state").html('<option value="">Select State</option>');
				$(".select-local-city").html('<option value="">Select City</option>');
				$(".select-local-area").html('<option value="">Select Area</option>');
				$(".select-local-zone").select2();
				$(".select-local-state").select2();
				$(".select-local-city").select2();
				$(".select-local-area").select2();
			}
		})
	});
	
	});
    </script>





<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_license_type'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'edit_from','role' => 'form');
        echo admin_form_open_multipart("masters/edit_license_type/".$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $result->is_country){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                        
            <div class="col-md-6">
            
            <div class="form-group">
				<?= lang("continent", "continent"); ?>
                
               <?php
               $con[''] = 'Select Continent';
                foreach ($continents as $continent) {
                    $con[$continent->id] = $continent->name;
                }
                echo form_dropdown('continent_id', $con, $result->continent_id, 'class="form-control select-local-continent " id="continent_id" '); ?>
            </div>
            
           
            <div class="form-group">
                <?= lang("country", "country"); ?>
               <?php
               $lcou[''] = 'Select Country';
                foreach ($lcountrys as $lcountry) {
                    $lcou[$lcountry->id] = $lcountry->name;
                }
                echo form_dropdown('country_id', $lcou, $result->country_id, 'class="form-control select-local-country " id="country_id" '); ?>
            </div>
            
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', $result->name, 'class="form-control" onkeyup="inputFirstUpper(this)" id="name" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('description', 'description'); ?>
                <?php echo form_input('details',  $result->details, 'class="form-control" id="details" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_license_type', lang('submit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>