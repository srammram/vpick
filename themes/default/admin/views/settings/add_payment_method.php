<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>
    $(document).ready(function(){
      $('#add-payment-method')
        .bootstrapValidator(
                            {
                                message: 'Please enter/select a value',
                            }
                        )
       
        
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_payment_method'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form'); ?>
	<form  data-toggle="validator" action="<?=admin_url('system_settings/add_payment_method')?>" role="form" enctype="multipart/form-data" method="post" accept-charset="utf-8" novalidate class="bv-form" id="add-payment-method">
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
			
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('payment_type', '', 'class="form-control gen_slug" id="payment_type" required="required"'); ?>
              <input type="hidden" name="add_payment_method" value="add payment method">
            </div>
            <div class="form-group">
                <?= lang('display_name', 'display_name'); ?>
                <?= form_input('display_name', '', 'class="form-control gen_slug" id="display_name" required="required"'); ?>              
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_payment_method', lang('add_payment_method'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
