<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_bbq_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'add-cus-dis-form');
        echo admin_form_open("system_settings/add_bbq_discounts/", $attrib); ?>

                    <p><?= lang('enter_info'); ?></p>
            <div class="col-lg-12">
                <div class="form-group">
                    <?= lang('name', 'name'); ?>
                    <?= form_input('name', set_value('name'), 'class="form-control tip" id="name" required="required"'); ?>
                </div>
            </div>
             <div class="form-group col-lg-6">
                <?= lang('from_date', 'from_date'); ?>
                    <div class="controls ">
                      <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required" value="" autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6">
                <?= lang('to_date', 'to_date'); ?>
              <div class="controls">
                <input type="text" name="to_date" class="form-control" placeholder="To Date " id="to_date" required="required" value="" autocomplete="off">
              </div>
            </div>
            
           
            <div class="form-group col-md-6">
                <?= lang('Discount_percentage', 'Discount_percentage'); ?>
                <input type="text" name="discount" value="" class="form-control tip numberonly discount" required="required">
            </div>
               
          
            
            <input type="hidden" name="discount_type" value="percentage">
               
           
            <div style="clear: both;height: 10px;"></div>

                    <div class="form-group col-lg-12">
                        
                        <?php echo form_submit('add_bbq_discounts', $this->lang->line("add_bbq_discount"), 'class="btn btn-primary"'); ?>
                    </div>

                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>



<?php //$modal_js ?>
<script>
$(".numberonly").keypress(function (event){
    
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
  
    });
</script>



