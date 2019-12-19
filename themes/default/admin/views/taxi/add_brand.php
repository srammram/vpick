<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_brand'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("taxi/add_brand", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="col-md-5 col-md-offset-1">
                                <div class="form-group all">
                                <?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" accept="image/*" class="form-control file">
                            </div>
                        </div>
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_brand', lang('add_brand'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>