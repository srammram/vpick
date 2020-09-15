<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_zone'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("settings/add_zone", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php
                        $countries_v[''] = '';
                        foreach ($countries as $key => $value) {
                            $countries_v[$value->id] = $value->country_name;
                        }
                        echo form_dropdown('country_id', $countries_v, '', 'class="form-control select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <?= lang('zone_name', 'zone_name'); ?>
                <?php echo form_input('zone_name', '', 'class="form-control" id="zone_name" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_zone', lang('add_zone'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>