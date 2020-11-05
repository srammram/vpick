<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_currency'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_currency'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("settings/add_currency", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('base_currency_name', 'base_currency_name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" name="name" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('symbol', 'symbol'); ?>
                                <div class="controls">
                                    <input type="text" id="symbol" name="symbol" class="form-control"
                                           required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('iso_code', 'iso_code'); ?>
                                <div class="controls">
                                    <input type="text" id="iso_code" name="iso_code" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('numeric_iso_code', 'numeric_iso_code'); ?>
                                <div class="controls">
                                    <input type="text" id="numeric_iso_code" name="numeric_iso_code" class="form-control"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_currency', lang('add_currency'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
