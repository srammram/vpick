<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_country'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("settings/add_country", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang("country", "country"); ?>
                    <?php
                    $continents_v[''] = '';
                    foreach ($continents as $key => $value) {
                        $continents_v[$value->id] = $value->continent_name;
                    }
                    echo form_dropdown('continent_id', $continents_v, '', 'class="form-control select" required="required"'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang('country_name', 'country_name'); ?>
                    <?php echo form_input('country_name', '', 'class="form-control" id="country_name" required="required"'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang('ISO_code', 'ISO_code'); ?>
                    <?php echo form_input('country_code', '', 'class="form-control" id="country_code" required="required"'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang('call_prefix', 'call_prefix'); ?>
                    <?php echo form_input('call_prefix', '', 'class="form-control" id="call_prefix" required="required"'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?= lang("default_currency", "default_currency"); ?>
                    <?php
                    $currencies_v[''] = '';
                    foreach ($currencies as $key => $value) {
                        $currencies_v[$value->id] = $value->name;
                    }
                    echo form_dropdown('currency', $currencies_v, '', 'class="form-control select" required="required"'); ?>
                </div>
            </div>
            <div class="col-md-6">
                <?= lang("photo", "photo") ?>
                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                       data-show-preview="false" accept="image/*" class="form-control file">
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_country', lang('add_country'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>