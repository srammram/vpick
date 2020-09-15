<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('edit_store'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/edit_store/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('store_code', 'code'); ?>
                <?= form_input('code', set_value('code', $store->code), 'class="form-control tip" id="code" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('store_name', 'name'); ?>
                <?= form_input('name', set_value('name', $store->name), 'class="form-control tip" id="name" required="required"'); ?>
            </div>
            
            <div class="form-group">
                <input type="checkbox" value="1" name="is_defalut" id="is_defalut" <?= $store->is_default_store ? 'checked="checked"' : ''; ?>>
                <label class="padding-left-10" for="is_defalut"><?= lang("is_defalut"); ?></label>
            </div>
        </div>
        <div class="modal-footer">
            <?= form_submit('edit_store', lang('edit_store'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
$(".numberonly").keypress(function (event){
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
  
	});
</script>
<?= $modal_js ?>
