<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
   <?php /*?> <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('create_user'); ?></h2>
    </div><?php */?>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('create_user'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from', 'data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open("auth/create_user", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('first_name', 'first_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('first_name', '', 'class="form-control" id="first_name" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('last_name', 'last_name'); ?>
                                <div class="controls">
                                    <?php echo form_input('last_name', '', 'class="form-control" id="last_name" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= lang('gender', 'gender'); ?>
                                <?php
                                $ge[''] = array('male' => lang('male'), 'female' => lang('female'));
                                echo form_dropdown('gender', $ge, (isset($_POST['gender']) ? $_POST['gender'] : ''), 'class="tip form-control" id="gender" data-placeholder="' . lang("select") . ' ' . lang("gender") . '" required="required"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('company', 'company'); ?>
                                <div class="controls">
                                    <?php echo form_input('company', '', 'class="form-control" id="company" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('phone', 'phone'); ?>
                                <div class="controls">
                                    <?php echo form_input('phone', '', 'class="form-control" id="phone" required="required" maxlength="15" onkeydown="return(event.which == 8 && event.which == 0 || (event.charCode >= 48 || event.which <= 57))"'); ?>
   
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="email" id="email" name="email" class="form-control"
                                           required="required"/>
                                    <?php /* echo form_input('email', '', 'class="form-control" id="email" required="required"'); */ ?>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('username', 'username'); ?>
                                <div class="controls">
                                    <input type="text" id="username" name="username" class="form-control"
                                           required="required" pattern=".{4,20}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('password', 'password'); ?>
                                <div class="controls">
                                    <?php echo form_password('password', '', 'class="form-control tip" id="password" required="required" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" data-bv-regexp-message="'.lang('pasword_hint').'"'); ?>
                                    <span class="help-block"><?= lang('pasword_hint') ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('confirm_password', 'confirm_password'); ?>
                                <div class="controls">
                                    <?php echo form_password('confirm_password', '', 'class="form-control" id="confirm_password" required="required" data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="' . lang('pw_not_same') . '"'); ?>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-5 col-md-offset-1">

                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            
                            
                           
                            
                        <div class="form-group">
                            <?= lang("group", "group"); ?>
                            <?php
                            foreach ($groups as $group) {
                                if ($group['name'] != 'customer' && $group['name'] != 'admin' && $group['name'] != 'supplier') {
                                    $gp[$group['id']] = $group['name'];
                                }
                            }
                            echo form_dropdown('group', $gp, (isset($_POST['group']) ? $_POST['group'] : ''), 'id="group" required="required" class="form-control group select" style="width:100%;"');
                            ?>
                        </div>
                        
                        <div class="form-group">
                            <?= lang("country", "country"); ?>
                            <?php
                            $countries_v[''] = '';
                            $countries_v[''] = 'Select Country';
                            foreach ($countries as $key => $value) {
                                $countries_v[$value->id] = $value->name;
                            }
                            echo form_dropdown('country', $countries_v, '', 'class="form-control select-country select" required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang("state", "state"); ?>
                           <?php
                            $states_v[''] = '';
                            $states_v[''] = 'Select State';
                            
                            echo form_dropdown('state', $states_v, '', 'class="form-control select-state select" id="select-state"  required="required"'); ?>
                        </div>
                        <div class="form-group">
                            <?= lang("city", "city"); ?>
                           <?php
                            $cities_v[''] = '';
                            $cities_v[''] = 'Select City';
                            echo form_dropdown('city', $cities_v, '', 'class="form-control select-city select" id="select-city" required="required"'); ?>
                        </div>

                     
                        
                    </div>
                </div>

                <p><?php echo form_submit('add_user', lang('add_user'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.no').slideUp();
        $('.group').change(function (event) {
            var group = $(this).val();
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
        $('#allow_discount').change(function (event) {
            if ($(this).val() == 1) {
                $('.max-discount').slideDown();
            } else {
                $('.max-discount').slideUp();
            }
        });
    });
	
		var su = 1;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });
</script>
