

<script>
$('form[class="edit_from"]').bootstrapValidator({
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_country'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'edit_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/edit_country/".$id, $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            
                        
            <div class="col-md-12">
            <div class="form-group  col-md-6 col-xs-12">
                <?= lang('continent', 'continent'); ?>
				<?php
                $c[''] = 'Select Continents';
                foreach ($parent as $value) {
                    $c[$value->id] = $value->name;
                }
                echo form_dropdown('continent_id', $c, $result->continent_id, 'class="form-control select"  id="continent_id" required="required"'); ?>
            </div>
            <div class="form-group col-md-6 col-xs-12">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', $result->name, 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
            </div>
            <div class="form-group  col-md-6 col-xs-12">
                <?= lang('phonecode', 'phonecode'); ?>
                <?php echo form_input('phonecode', $result->phonecode, 'class="form-control" id="phonecode" required="required"'); ?>
            </div>
            
            <div class="form-group all  col-md-6 col-xs-12">
				<?= lang("flag", "flag") ?>
                <input id="flag" type="file" data-browse-label="<?= lang('browse'); ?>" name="flag" data-show-upload="false"
                       data-show-preview="false" class="form-control file" accept="im/*">
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_country', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>