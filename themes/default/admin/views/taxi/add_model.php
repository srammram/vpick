<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_model'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("taxi/add_model", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("brand", "brand"); ?>
                        <?php
                        $brands_v[''] = '';
                        foreach ($brands as $key => $value) {
                            $brands_v[$value->id] = $value->name;
                        }
                        echo form_dropdown('brand_id', $brands_v, '', 'class="form-control select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <?= lang('model_name', 'model_name'); ?>
                <?php echo form_input('model_name', '', 'class="form-control" id="model_name" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_model', lang('add_model'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>