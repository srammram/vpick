<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_recipe_Groups'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("system_settings/edit_recipecategory/".$category->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

          
            
            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <div class="input-group col-md-12">
                	<?= form_input('code',  $category->code, 'class="form-control numberonly" maxlength="9"  id="code" required="required"' ); ?>
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
                <?= form_input('name', set_value('name', $category->name), 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <?= lang('khmer_name', 'khmer_name'); ?>
                <?= form_input('khmer_name', set_value('khmer_name', $category->khmer_name), 'class="form-control" id="khmer_name" required="required"'); ?>
            </div>

           

            <div class="form-group">
                <?= lang("category_image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false" class="form-control file">
            </div>
            <div class="form-group">
                <?= lang("parent_category", "parent") ?>
                <?php
                $cat[''] = lang('select').' '.lang('parent_category');
                foreach ($categories as $pcat) {
                    $cat[$pcat->id] = $pcat->name;
                }
                echo form_dropdown('parent', $cat, (isset($_POST['parent']) ? $_POST['parent'] : $category->parent_id), 'class="form-control select" id="parent" style="width:100%"')
                ?>
            </div>
            <div class="form-group">
                <?= lang("Kitchen Type", "Kitchen Type"); ?>
                <?php
                $rk[''] = '';
                foreach ($reskitchen as $kitchen) {
                    $rk[$kitchen->id] = $kitchen->name;
                }
                echo form_dropdown('kitchens_id', $rk, $category->kitchens_id, 'class="form-control" data-placeholder="' . lang("select") . ' ' . lang("Kitchen Type") . '" style="width:100%;" required="required" ');
                ?>
                
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_recipecategory', lang('edit_recipe_category'), 'class="btn btn-primary"'); ?>
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
			 $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $('#code'));
			 
			
		});
		$(".numberonly").keypress(function (event){
	
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	  
		});
});
</script>
