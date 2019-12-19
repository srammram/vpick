
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
    </script>


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_state'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'edit_from','role' => 'form');
        echo admin_form_open_multipart("masters/edit_state/".$id, $attrib); ?>
        <div class="modal-body">
           
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            
                        
            <div class="col-md-12">
				
				<div class="form-group col-md-6 col-xs-12">
					<?= lang('continent', 'continent'); ?>
					<?php
					$c[''] = 'Select Continents';
					foreach ($parent as $value) {
						$c[$value->id] = $value->name;
					}
					echo form_dropdown('continent_id', $c, $continent->continent_id, 'class="form-control select-continent select"  id="continent_id" required="required"'); ?>
				</div>
           
				<div class="form-group col-md-6 col-xs-12">
					<?= lang("country", "country"); ?>
				   <?php
				   $co[''] = 'Select Country';
				   foreach ($countrys as $cou) {
						$co[$cou->id] = $cou->name;
					}
					echo form_dropdown('country_id', $co, $country->country_id, 'class="form-control select-country select" id="country_id" required="required"'); ?>
				</div>
            
				<div class="form-group col-md-6 col-xs-12">
					<?= lang("zone", "zone"); ?>
				   <?php
				   $zo[''] = 'Select Zone';
				   foreach ($zones as $zone_row) {
						$zo[$zone_row->id] = $zone_row->name;
					}
					echo form_dropdown('zone_id', $zo, $result->zone_id, 'class="form-control select-zone select" id="zone_id" required="required"'); ?>
				</div>
                <div class="form-group col-md-6 col-xs-12">
					<?= lang('name', 'name'); ?>
					<?php echo form_input('name', $result->name, 'class="form-control" onkeyup="inputFirstUpper(this)" id="name" required="required"'); ?>
				</div>
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_state', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>
<script>
$(document).ready(function(){
	$(".select-continent").select2();
	$('.select-continent').change(function(){
		$(".select-country").select2("destroy");
		$(".select-zone").select2("destroy");
		id = $(this).val();
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
				$(".select-country").html($option);
				$(".select-zone").html('<option value="">Select Zone</option>');
				$(".select-country").select2();
				$(".select-zone").select2();
			}
		})
	});
	
	$('.select-country').change(function(){
		id = $(this).val();
		$(".select-zone").select2("destroy");
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getZone_bycountry')?>',
			data: {country_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select State</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-zone").html($option);
				$(".select-zone").select2();
			}
		})
	});
	
});
</script>
<script>
$(document).ready(function(){
	$(".select-country").select2();
	$(".select-zone").select2();
});
</script>