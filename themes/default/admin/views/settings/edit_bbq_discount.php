<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript" src="<?=$assets ?>js/customer_discount.js"></script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_BBQ_discount'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><!-- <?php echo lang('enter_info'); ?> --></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','id'=>'edit-cus-dis-form');
        echo admin_form_open("system_settings/edit_bbq_discount/".$bbq_discounts->id, $attrib); ?>

                    <p><?= lang('enter_info'); ?></p>
            <div class="form-group col-lg-12">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', set_value('name', $bbq_discounts->name), 'class="form-control tip" id="name" required="required"'); ?>
                <input type="hidden" name="id" value="<?=$bbq_discounts->id?>">
            </div>
              <div class="form-group col-lg-6">
                <?= lang('from_date', 'from_date'); ?>
                    <div class="controls ">
                       
                      <input type="text" name="from_date" class="form-control" placeholder="From Date " id="from_date" required="required" value="<?= $bbq_discounts->from_date ?>" autocomplete="off">
                    </div>
                  </div>
            <div class="form-group col-lg-6">
                <?= lang('to_date', 'to_date'); ?>
               
              <div class="controls">
                <input type="text" name="to_date"  class="form-control" placeholder="To Date " id="to_date" required="required" value="<?= $bbq_discounts->to_date ?>" autocomplete="off">
              </div>
            </div>
            
            <div class="form-group col-md-6">
                <?= lang('Discount_percentage', 'Discount_percentage'); ?>
                <input type="text" name="discount" value="<?=$bbq_discounts->discount?>" class="form-control tip numberonly discount" required="required">
            </div>
               
          <input type="hidden" name="discount_type" value="percentage">
               
            
            <div style="clear: both;height: 10px;"></div>

                    <div class="form-group col-lg-12">
                        <?php echo form_submit('update_bbq_discount', $this->lang->line("update_bbq_discount"), 'class="btn btn-primary"'); ?>
                    </div>

                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

