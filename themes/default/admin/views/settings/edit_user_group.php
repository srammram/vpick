<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_user_group'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('edit_user_group'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("settings/edit_user_group/".$group->id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="form-group">
                                <?php echo lang('group_name', 'group_name'); ?>
                                    <div class="controls">
                                        <input type="text" id="name" name="name" value="<?=(isset($_POST['name']) ? $_POST['name'] : ($group ? $group->name : ''))?>" class="form-control" required="required"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo lang('description', 'description'); ?>
                                    <div class="controls">
                                        <textarea id="description" name="description" class="form-control"><?=(isset($_POST['description']) ? $_POST['description'] : ($group ? $group->description : ''))?></textarea>
                                    </div>
                                </div> 
                            </div>
                      
                      
                    </div>
                </div>
                   

                <p><?php echo form_submit('update_group', lang('update_group'), 'class="btn btn-primary"'); ?></p>

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
