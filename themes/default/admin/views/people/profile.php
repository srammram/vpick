<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('edit_currency'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("masters/edit_currency/".$currency->id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="col-md-12">    	
                            <legend>Login Details</legend>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo lang('name', 'name'); ?>
                                    <div class="controls">
                                        <input type="text" id="name" name="name" class="form-control"  value="<?=(isset($_POST['name']) ? $_POST['name'] : ($currency ? $currency->name : ''))?>" required="required"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo lang('symbol', 'symbol'); ?>
                                    <div class="controls">
                                        <input type="text" id="symbol" name="symbol" value="<?=(isset($_POST['symbol']) ? $_POST['symbol'] : ($currency ? $currency->symbol : ''))?>" class="form-control"
                                               required="required""/>
                                    </div>
                                </div>
                                <div class="form-group">
                                <?php echo lang('iso_code', 'iso_code'); ?>
                                    <div class="controls">
                                        <input type="text" id="iso_code" name="iso_code" value="<?=(isset($_POST['iso_code']) ? $_POST['iso_code'] : ($currency ? $currency->iso_code : ''))?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo lang('numeric_iso_code', 'numeric_iso_code'); ?>
                                    <div class="controls">
                                        <input type="text" id="numeric_iso_code" name="numeric_iso_code" value="<?=(isset($_POST['numeric_iso_code']) ? $_POST['numeric_iso_code'] : ($currency ? $currency->numeric_iso_code : ''))?>" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= lang('status', 'status'); ?>
                                    <?php
                                    $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                    echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ($currency ? $currency->status : '')), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                       </fieldset>
                      
                    </div>
                </div>
                   

                <p><?php echo form_submit('update_currency', lang('update_currency'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<style>
    .input-group .form-control{
        z-index:1 !important;
    }
</style>
