<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('edit_kyc_doc_type'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('edit_kyc_doc_type'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'class' => 'edit_from','data-toggle' => 'validator', 'role' => 'form' ,'id'=>'kyc-type-form');
                echo admin_form_open_multipart("settings/edit_kyc_doc_type/".$doc->id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-6">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo lang('document_type', 'document_type'); ?>
                                    <div class="controls">
                                        <input type="text" id="type" name="type" value="<?=(isset($_POST['type']) ? $_POST['type'] : ($doc ? $doc->type : ''))?>" class="form-control" required="required"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?php echo lang('users', 'users'); ?>
                                    <div class="controls">
                                        <label><input type="radio" id="user_type" name="user_type" value="1" <?php if($doc->user_type==1){ echo 'checked="checked"';} ?> class="form-control"/>Drivers</label>
                                        <label><input type="radio" id="user_type" name="user_type" value="2" <?php if($doc->user_type==2){ echo 'checked="checked"';} ?> class="form-control"/>Operators</label>
                                        <label><input type="radio" id="user_type" name="user_type" value="0" <?php if($doc->user_type==0){ echo 'checked="checked"';} ?> class="form-control"/>Both</label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <?= lang('status', 'status'); ?>
                                    <?php
                                    $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                    echo form_dropdown('status', $opt, (isset($_POST['status']) ? $_POST['status'] : ($doc ? $doc->status : '')), 'id="status" required="required" class="form-control select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                      
                      
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">Document type fields</div>
                    <div class="fields-container">
                        <?php foreach($fields as $k => $row) : ?>
                        <div class="col-md-12 form-inline field-container">
                             <div class="col-md-1 form-group">
                                    <?php echo lang('Active', 'Active'); ?>
                                    <div class="controls">
                                        <input type="hidden" name="field_status[<?=$k?>]" value="0">
                                        <input type="checkbox" name="field_status[<?=$k?>]" value="1" <?php if($row->status==1) echo 'checked="checked"'; ?>>
                                        
                                    </div>
                             </div>
                            <div class="col-md-3 form-group">
                                    <?php echo lang('label_name', ''); ?>*
                                    <div class="controls">
                                        <input type="hidden" name="field_id[<?=$k?>]" value="<?=$row->id?>">
                                        <input type="text" id="label_name" name="label_name[<?=$k?>]" value="<?=$row->label_name?>" class="form-control" required="required"/>
                                    </div>
                            </div>
                            <div class="col-md-3 form-group">
                                    <?= lang('input_type', ''); ?>*
                                    <?php
                                    $opt = array('text' => 'text','dropdown' => 'dropdown','radio' => 'radio','checkbox' => 'checkbox','datepicker' => 'datepicker','datetimepicker' => 'datetimepicker','yearpicker' => 'yearpicker','yearmonthpicker' => 'yearmonthpicker','numeric'=>'numeric');
                                    echo form_dropdown('input_type['.$k.']', $opt, $row->input_type, ' required="required" class="input-type form-control select" style="width:80%;"');
                                    ?>
                            </div>
                            <?php $display='none';if($row->input_type=="dropdown" || $row->input_type=="radio" || $row->input_type=="checkbox") {
                                $display = 'block';
                            }?>
                            <div class="col-md-3 form-group options-container" style="display:<?=$display?>;">
                                    <?php echo lang('options', 'options'); ?>*
                                    <div class="controls">
                                        <textarea id="options" name="options[<?=$k?>]" value="<?=$row->options?>"required="required"/><?=$row->options?></textarea>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                    </div>
                    <div class="clearfix" style="clear:both;height:10px"></div>
                    <div class="col-md-12"><button type="button" class="btn btn-primary" id="add-more-fields">Add More fields</button></div>
                </div>
                <div class="clearfix" style="clear:both;height:10px"></div>
                   

                <p><?php echo form_submit('update_doc_type', lang('update_doc_type'), 'class="update-kyc-type btn btn-primary"'); ?></p>

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
<script>
    $(document).ready(function(){
        $('.update-kyc-type').click(function(e){
           $('.options-container').each(function(){
                $obj = $(this);
                if (!$obj.is(':visible')) {
                    
                   $obj.find('textarea').val('');
                }
            });
            return true;
        })
        $(document).on('change','.input-type',function(){
            $obj = $(this);
            console.log($obj.val())
            if ($obj.val()=='dropdown' || $obj.val()=='checkbox' || $obj.val()=='radio') {
                $obj.parents('.field-container').find('.options-container').show();
            }else{
                $obj.parents('.field-container').find('.options-container').hide();
            }
        });
        $length = '<?=count($fields)?>';
        $('#add-more-fields').click(function(){
            $html = '<div class="col-md-12 form-inline field-container">'+
                        '<div class="col-md-1 form-group">'+
                                '<?php echo lang('Active', 'Active'); ?>'+
                                '<div class="controls">'+
                                    '<input type="hidden" name="field_status['+$length+']" value="0">'+
                                    '<input type="checkbox" name="field_status['+$length+']" value="1" checked>'+
                                '</div>'+
                        '</div>'+
                        '<div class="col-md-3 form-group">'+
                                '<?php echo lang('label_name', 'label_name'); ?>*'+
                                '<div class="controls">'+
                                    '<input type="hidden" name="field_id['+$length+']" value="0">'+
                                    '<input type="text" id="label_name" name="label_name['+$length+']" class="form-control" required="required"/>'+
                                '</div>'+
                        '</div>'+
                        '<div class="col-md-3 form-group">'+
                                '<?= lang('input_type', 'input_type'); ?>*'+
                                '<select name="input_type['+$length+']" required="required" class="input-type form-control select" style="width: 80%;">'+
                                    '<option value="text">text</option>'+
                                    '<option value="dropdown">dropdown</option>'+
                                    '<option value="radio">radio</option>'+
                                    '<option value="checkbox">checkbox</option>'+
                                    '<option value="datepicker">datepicker</option>'+
                                    '<option value="datetimepicker">datetimepicker</option>'+
                                    '<option value="yearpicker">yearpicker</option>'+
                                    '<option value="yearmonthpicker">yearmonthpicker</option>'+
                                    '<option value="numeric">numeric</option>'+
                                '</select>'+
                        '</div>'+
                        '<div class="col-md-3 form-group options-container" style="display:none;">'+
                                '<?php echo lang('options', 'options'); ?>*'+
                                '<div class="controls">'+
                                    '<textarea id="options" name="options['+$length+']"required="required"/></textarea>'+
                                '</div>'+
                        '</div>'+
                    '</div>';
            $('.fields-container').append($html);
             
            var $form = $('form[data-toggle="validator"]');            
            var validator = $('#kyc-type-form').data('bootstrapValidator');
            validator.addField($form.find('[name="label_name['+$length+']"]'));
            validator.addField($form.find('[name="input_type['+$length+']"]'));
            validator.addField($form.find('[name="options['+$length+']"]'));
            $('input[name="field_status['+$length+']"]').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%'
		});
            $length++;
        });
    })
</script>
