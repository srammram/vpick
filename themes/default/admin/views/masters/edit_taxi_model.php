
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
         
              myClass: {
            selector: '.country_instance',
            validators: {
                notEmpty: {
                    message: 'Select Instance of country'
                }
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_cab_model'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'edit_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/edit_taxi_model/".$id, $attrib); ?>
        <div class="modal-body">
             
            <div class="col-md-12">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <?php echo lang('instance_of_country', 'instance_of_country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country country_instance" name="is_country" id="is_country">
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
            
          
            
            <div class="form-group col-sm-6 col-xs-12">
                <?= lang('cab_type', 'taxi_type'); ?>
				<?php
                $t[''] = 'Select Type';
                foreach ($types as $type) {
                    $t[$type->id] = $type->name;
                }
                echo form_dropdown('type_id', $t, $result->type_id, 'class="form-control select"  id="type_id" required="required"'); ?>
            </div>
            
            <div class="form-group col-sm-6 col-xs-12">
                <?= lang('cab_make', 'taxi_make'); ?>
				<?php
                $c[''] = 'Select Make';
                foreach ($makes as $make) {
                    $c[$make->id] = $make->name;
                }
                echo form_dropdown('make_id', $c, $result->make_id, 'class="form-control select"  id="make_id" required="required"'); ?>
            </div>
          
          
            <div class="form-group col-sm-6 col-xs-12">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', $result->name, 'class="form-control" onkeyup="inputFirstUpper(this)" id="name" required="required"'); ?>
            </div>
            
            
            
            
            
            
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_taxi_model', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script>
$(document).ready(function(){
	$('.is_country').change(function(){
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getTaxitype_byCountry')?>',
			data: {is_country: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option1 = '<option value="">Select Type</option>';
				$.each(scdata.type,function(n,v){
					$option1 += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$("#type_id").html($option1);
				$option2 = '<option value="">Select Make</option>';
				$.each(scdata.make,function(n,v){
					$option2 += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$("#make_id").html($option2);
				
			}
		})
	});
});
</script>
<?= @$modal_js ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script>
$(document).ready(function(){
	$(".country_instance").select2();
});
</script>