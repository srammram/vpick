


<script>
$('form[class="add_from"]').bootstrapValidator({
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_help_sub'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/add_help_sub/".$id, $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <?php echo lang('instance_of_country', 'instance_of_country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country country_instance"  name="is_country" id="is_country">
                <option value="">Select Country </option>
                <?php
                foreach($AllCountrys as $AllCountry){
                ?>
                <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $countryCode ? $countryCode : $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                <?php
                }
                ?>
            </select>
            </div>
            
            
            <div class="col-md-6">
            <div class="form-group">
                <?= lang('help', 'help'); ?>
				<?php
                $c[''] = 'Select Help';
                foreach ($parent as $value) {
                    $c[$value->id] = $value->name;
                }
                echo form_dropdown('help_id', $c, $help_parent_id, 'class="form-control select-help select"  id="help_id" required="required"'); ?>
            </div>
           
            <div class="form-group">
                <?= lang("help_main", "help_main"); ?>
               <?php
			   $co[''] = 'Select help_main';
			   foreach ($help_main as $help_mains) {
                    $co[$help_mains->id] = $help_mains->name;
                }
                echo form_dropdown('parent_id', $co, $id, 'class="form-control select-country select" id="parent_id" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
            </div>
            
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_help_sub', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>
<script>
$(document).ready(function(){
	$('.select-help').change(function(){
		id = $(this).val();
		var is_country = $('.country_instance option:selected').val();
		if(is_country == ''){
			bootbox.alert('Please select country');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getHelp_main_byhelp')?>',
			data: {parent_id: id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Help Main</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-help_main").html($option);
			}
		})
	});
});
</script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script>
$(document).ready(function(){
	$(".country_instance").select2();
});
</script>