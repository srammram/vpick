


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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_zone'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/add_zone", $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            
            <div class="col-md-6">
            
            <div class="form-group">
                <?= lang('continent', 'continent'); ?>
				<?php
                $c[''] = 'Select Continents';
                foreach ($parent as $value) {
                    $c[$value->id] = $value->name;
                }
                echo form_dropdown('continent_id', $c, '', 'class="form-control select-continent select"  id="continent_id" required="required"'); ?>
            </div>
           
            <div class="form-group">
                <?= lang("country", "country"); ?>
               <?php
                echo form_dropdown('country_id', '', '', 'class="form-control select-country select country_instance" id="country_id" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
            </div>
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_zone', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script>
$(document).ready(function(){
	$(".select-continent").select2();
	$('.select-continent').change(function(){
		$(".select-country").select2("destroy");
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
				$(".select-country").select2();
			}
		})
	});
});
</script>
