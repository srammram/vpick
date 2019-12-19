
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_cab_type'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from','role' => 'form');
        echo admin_form_open_multipart("masters/add_taxi_type", $attrib); ?>
        <div class="modal-body">
		<div class="col-md-12">
            	<h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <?php echo lang('instance_of_country', 'instance_of_country'); ?>
            <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country country_instance"  name="is_country" id="is_country">
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
            
            
             <div class="form-group col-sm-6 col-xs-12">
                <?= lang('category', 'category_id'); ?>
				<?php
                $t[''] = 'Select Category';
                foreach ($parent as $type) {
                    $t[$type->id] = $type->name;
                }
                echo form_dropdown('category_id', $t, '', 'class="form-control select"  id="category_id" required="required"'); ?>
            </div>
            
            
            
            <div class="form-group col-sm-6 col-xs-12">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
            </div>
            <div class="form-group col-sm-6">
            <?php echo lang('image', 'image'); ?>
            <select  required class="form-control select taxi_image_id"  name="taxi_image_id" id="taxi_image_id">
                <option value="">Select Image</option>
                <?php
                foreach($typeimage as $timage){
                ?>
                <option value="<?= $timage->id ?>"><?= $timage->name ?></option>
                <?php
                }
                ?>
            </select>
            </div>
            
            
            <!--<input type="hidden" name="category_id" value="1">-->
            
            <!--<div class="form-group all col-sm-6 col-xs-12">
				<?= lang("image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="image" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">
            </div>
            
            <div class="form-group all col-sm-6 col-xs-12">
				<?= lang("image_hover", "image_hover") ?>
                <input id="image_hover" type="file" data-browse-label="<?= lang('browse'); ?>" name="image_hover" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">
            </div>
            
            <div class="form-group all col-sm-6 col-xs-12">
				<?= lang("mapcar", "mapcar") ?>
                <input id="mapcar" type="file" data-browse-label="<?= lang('browse'); ?>" name="mapcar" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">
            </div>-->
            <!-- <div class="form-group all col-sm-6 col-xs-12">
				<?= lang("outstation_image", "outstation_image") ?>
                <input id="outstation_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="outstation_image" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">
            </div>-->
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_taxi_type', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
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
	$(".country_instance").select2();
	$(".taxi_image_id").select2();
});
</script>
<script>
$(document).ready(function(){
	
	$('.country_instance').change(function(){
		$('#category_id').select2('destroy');
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getTaxicategory_byCountry')?>',
			data: {is_country: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option1 = '<option value="">Select Category</option>';
				if (scdata.category && scdata.category.length > 0) {
					$.each(scdata.category,function(n,v){
						$option1 += '<option value="'+v.id+'">'+v.text+'</option>';
					});
				}
				
				$("#category_id").html($option1);
				$("#category_id").select2();
				
				
			}
		})
	});
});
</script>