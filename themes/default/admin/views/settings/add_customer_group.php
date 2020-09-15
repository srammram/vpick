<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_customer_group'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_customer_group'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("settings/add_customer_group", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('group_name', 'group_name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" name="name" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('description', 'description'); ?>
                                <div class="controls">
                                    <textarea id="description" name="description" class="form-control"></textarea>
                                </div>
                            </div> 
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('add_customer_group', lang('add_customer_group'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
