<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_BBQ_Groups'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("system_settings/add_bbqcategory", $attrib); ?>
	
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

          
            
            <div class="form-group">
                <?= lang('code', 'code'); ?>
               
                <div class="input-group col-md-12">
                	<?= form_input('code', '', 'class="form-control numberonly" id="code" required="required" maxlength="9" '); ?>
                     <span class="" id="random_num" style="    padding: 6px 10px;
    background: #efefef;
    position: relative;
    margin-top: -34px;
    border: 1px solid #ccc;
    float: right;
    z-index: 99;
    cursor: pointer;">
                        <i class="fa fa-random"></i>
                    </span>
                   
                </div>
                
                
            </div>

            <div class="form-group">
                <?= lang('category_name', 'name'); ?>
                <?= form_input('name', set_value('name'), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
            
           

           
            <div class="form-group">
                <?= lang("category_image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
            
            <div class="form-group">
                <?= lang('adult_price', 'adult_price'); ?>
                <?= form_input('adult_price', set_value('adult_price'), 'class="form-control numberonly" maxlength="10" id="adult_price" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('child_price', 'child_price'); ?>
                <?= form_input('child_price', set_value('child_price'), 'class="form-control numberonly" maxlength="10" id="child_price" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang("Active Days & Discount") ?>
                
            </div>
            
         	             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Monday" class="checkbox" name="active_day[]" id="active_day_Monday">
                <?= lang("Monday", "Monday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Monday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Monday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Tuesday" class="checkbox" name="active_day[]" id="active_day_Tuesday">
                <?= lang("Tuesday", "Tuesday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Tuesday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Tuesday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Wednesday" class="checkbox" name="active_day[]" id="active_day_Wednesday">
                <?= lang("Wednesday", "Wednesday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Wednesday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Wednesday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Thursday" class="checkbox" name="active_day[]" id="active_day_Thursday">
                <?= lang("Thursday", "Thursday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Thursday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Thursday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Friday" class="checkbox" name="active_day[]" id="active_day_Friday">
                <?= lang("Friday", "Friday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Friday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Friday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Saturday" class="checkbox" name="active_day[]" id="active_day_Saturday">
                <?= lang("Saturday", "Saturday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Saturday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Saturday" disabled style="width:100%;" '); ?>
                </div>
             </div>
             
             <div class="form-group row">
            	<div class="col-lg-4">
            	<input type="checkbox" value="Sunday" class="checkbox" name="active_day[]" id="active_day_Sunday">
                <?= lang("Sunday", "Sunday") ?>
                </div>
                <div class="col-lg-4">
                <?= form_input('discount[]', '', 'class="form-control numberonly" placeholder="Discount" id="discount_Sunday" disabled '); ?>
                </div>
                <div class="col-lg-4">
                <?php $opts = array(1 => lang('percentage'), 2 => lang('fixed')); ?>
				<?= form_dropdown('discount_type[]', $opts, '', 'class="form-control" id="discount_type_Sunday" style="width:100%;" disabled '); ?>
                </div>
             </div>
             
           

        </div>
        <div class="modal-footer">
	    <input type="hidden" name="add_bbqcategory" value="<?=lang('add_bbq_category')?>">
            <?php echo form_submit('add_bbqcategory', lang('add_bbq_category'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
  $(document).ready(function(){

$(document).on('click', '#random_num', function(event){
	event.preventDefault();
			$(this).parent('.input-group').children('input').val(generateCardNo(8));
		//	 $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $('#code'));
			 
			
		});
		$(".numberonly").keypress(function (event){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
});

$(document).on('ifChanged', '.checkbox', function(){
	var v = $(this).val();
	if ($(this).is(':checked')) {
		$(this).iCheck('check');
		$('#discount_'+v).prop("disabled", false);
		$('#discount_type_'+v).prop("disabled", false);
	}else{
		$(this).iCheck('uncheck');
		$('#discount_'+v).prop("disabled", true);
		$('#discount_type_'+v).prop("disabled", true);
	}
});
</script>