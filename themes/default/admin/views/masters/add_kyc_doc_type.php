<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_kyc_doc_type'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'id'=>'kyc-type-form','role' => 'form');
                echo admin_form_open_multipart("masters/add_kyc_doc_type", $attrib);
                ?>
                <div class="row">
                <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country"  name="is_country" id="is_country">
                            <option value="">Select Country</option>
                            <?php
                            foreach($AllCountrys as $AllCountry){
                            ?>
                            <option value="<?= $AllCountry->iso ?>" <?php if($AllCountry->iso == $_GET['is_country']){ echo 'selected'; }else{ echo ''; } ?>><?= $AllCountry->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        </div>
                    <div class="col-md-6">
                        <div class="col-md-12">                                   
                            <div class="form-group">
                                <?php echo lang('document_type', 'document_type'); ?>
                                <div class="controls">
                                    <input type="text" id="type" name="type" class="form-control" required="required"/>
                                </div>
                            </div>
                            <div class="form-group">
                                    <?php echo lang('users', 'users'); ?>
                                    <div class="controls">
                                        <label><input type="radio" id="user_type" name="user_type" value="1" class="form-control"/>Drivers</label>
                                        <label><input type="radio" id="user_type" name="user_type" value="2" class="form-control"/>Operators</label>
                                        <label><input type="radio" id="user_type" name="user_type" value="0" class="form-control"/>Both</label>
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
                <div class="row">
                    <div class="col-md-12">Document type fields</div>
                    <div class="fields-container">
                        <div class="col-md-12 form-inline field-container">
                            <div class="col-md-3 form-group">
                                    <?php echo lang('label_name', 'label_name'); ?>
                                    <div class="controls">
                                        
                                        <input type="text" id="label_name" name="label_name[]" class="form-control" required="required"/>
                                    </div>
                            </div>
                            <div class="col-md-3 form-group">
                                    <?= lang('input_type', 'input_type'); ?>
                                    <?php
                                    $opt = array('text' => 'text','dropdown' => 'dropdown','radio' => 'radio','checkbox' => 'checkbox','datepicker' => 'datepicker','datetimepicker' => 'datetimepicker','yearpicker' => 'yearpicker','yearmonthpicker' => 'yearmonthpicker','numeric'=>'numeric');
                                    echo form_dropdown('input_type[]', $opt, '', ' required="required" class="input-type form-control select" style="width:80%;"');
                                    ?>
                            </div>
                            <div class="col-md-3 form-group options-container" style="display:none;">
                                    <?php echo lang('options', 'options'); ?>
                                    <div class="controls">
                                        <textarea id="options" name="options[]"required="required"/></textarea>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix" style="clear:both;height:10px"></div>
                    <div class="col-md-12"><button type="button" class="btn btn-primary" id="add-more-fields">Add More fields</button></div>
                </div>
                <div class="clearfix" style="clear:both;height:10px"></div>
                <p><?php echo form_submit('add_doc_type', lang('submit'), 'class="add-kyc-type btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.add-kyc-type').click(function(e){
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
        $length = 1;
        $('#add-more-fields').click(function(){
            $html = '<div class="col-md-12 form-inline field-container">'+
                        '<div class="col-md-3 form-group">'+
                                '<?php echo lang('label_name', 'label_name'); ?>*'+
                                '<div class="controls">'+
                                    '<input type="text" id="label_name" name="label_name[]" class="form-control" required="required"/>'+
                                '</div>'+
                        '</div>'+
                        '<div class="col-md-3 form-group">'+
                                '<?= lang('input_type', 'input_type'); ?>*'+
                                '<select name="input_type[]" required="required" class="input-type form-control select" style="width: 80%;">'+
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
                                    '<textarea id="options" name="options[]"required="required"/></textarea>'+
                                '</div>'+
                        '</div>'+
                    '</div>';
            $('.fields-container').append($html);
             
            var $form = $('form[data-toggle="validator"]');            
            var validator = $('#kyc-type-form').data('bootstrapValidator');
            validator.addField($form.find('[name="label_name['+$length+']"]'));
            validator.addField($form.find('[name="input_type['+$length+']"]'));
            validator.addField($form.find('[name="options['+$length+']"]'));
            $length++;
        });
    })
</script>