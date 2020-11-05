<script>
$('form[class="add_from"]').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    
                }
            },
           
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>


  
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_help_form'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("masters/add_help_form/".$id, $attrib); ?>
        <div class="modal-body">
        <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country country_instance"  name="is_country" id="is_country">
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
            <div class="col-md-12">  
						<h2 class="box_he_de">Help Details</h2>
                 		<div class="form-group col-md-4 col-xs-12">
						<?= lang('help', 'help'); ?>
                        <?php
                        $c[''] = 'Select help';
                        foreach ($parent as $value) {
                            $c[$value->id] = $value->name;
                        }
                        echo form_dropdown('help_id', $c, '', 'class="form-control select-help select"  id="help_id" required="required"'); ?>
                    </div>
                   
                    <div class="form-group col-md-4 col-xs-12">
                        <?= lang("help_main", "help_main"); ?>
                       <?php
					   $co[''] = 'Select help_main';
					   foreach ($help_main as $help_mains) {
							$co[$help_mains->id] = $help_mains->name;
						}
						
                        echo form_dropdown('help_main_id', $co, '', 'class="form-control select-help_main select" id="help_main_id" required="required"'); ?>
                    </div>
                    
                    <div class="form-group col-md-4 col-xs-12">
                        <?= lang("help_sub", "help_sub"); ?>
                       <?php
					   $so[''] = 'Select help_sub';
					   foreach ($help_sub as $help_subs) {
							$so[$help_subs->id] = $help_subs->name;
						}
                        echo form_dropdown('parent_id', $so, '', 'class="form-control select-help_sub select" id="parent_id" required="required"'); ?>
                    </div>
                    <div class="form-group col-md-12 col-xs-12">
                    	<textarea id="summernote" name="details"></textarea>
                    	
                    </div>
                 </div>
            <div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('form_details') ?></h2>
						<div id="field">
                            <div id="field0">
                            
                            
                                <div class="form-group col-md-3 col-xs-12">
                                    <?= lang('label', 'label'); ?>
                                    <?php echo form_input('name[]', '', 'class="form-control" id="name" onkeyup="inputFirstUpper(this)" required="required"'); ?>
                                </div>
                               
                                
                                <div class="form-group col-md-3 col-xs-12">
                                    <?= lang('form_type', 'form_type'); ?>
                                    <?php
                                    $f[''] = 'Select form_type';
                                    $f['1'] = 'Short Text';
                                    $f['2'] = 'Long Text';
									$f['3'] = 'Files';
									$f['4'] = 'Date';
									$f['5'] = 'Time';
									$f['6'] = 'Email';
									$f['7'] = 'Location Search';
									$f['8'] = 'Radio';
									$f['9'] = 'Checkbox';
									
                                    echo form_dropdown('form_type[]', $f, '', 'class="form-control select"  id="form_type" required="required"'); ?>
                                </div>
                                
                                <div class="form-group col-md-3 col-xs-12">
                                    <?= lang('form_name', 'form_name'); ?>
                                    <?php echo form_input('form_name[]', '', 'class="form-control" id="form_name" onkeyup="inputlower(this)" required="required"'); ?>
                                </div>
                                <div class="form-group col-md-3 col-xs-12">
                                    
                                    <button type="button" style="margin-top:30px;" id="add-more" name="add-more" class="btn btn-primary btn-block">Add More</button>
                                </div>
                       			
                            
                            </div>
                            <div class="clearfix"></div>
                        </div>
            			</div>

            <div style="clear: both;height: 10px;"></div>
            
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_help_form', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= @$modal_js ?>
<script>
$(document).ready(function () {
    //@naresh action dynamic childs
    var next = 0;
    $("#add-more").click(function(e){
        e.preventDefault();
        var addto = "#field" + next;
        var addRemove = "#field" + (next);
        next = next + 1;
        var newIn = ' <div id="field'+ next +'" name="field'+ next +'"><div class="form-group col-md-3 col-xs-12"><label>Label</label><input type="text" name="name[]" class="form-control" id="name" onkeyup="inputFirstUpper(this)" ></div><div class="form-group col-md-3 col-xs-12"><label>form_type</label><select name="form_type[]" class="form-control select"><option value="">Select Type</option><option value="1">Short Text</option><option value="2">Long Text</option><option value="3">Files</option><option value="4">Date</option><option value="5">Time</option><option value="6">Email</option><option value="7">Location Search</option><option value="8">Radio</option><option value="9">Checkbox</option></select></div><div class="form-group col-md-3 col-xs-12"><label>form_name</label><input type="text" name="form_name[]" class="form-control" id="form_name" onkeyup="inputlower(this)" ></div>';
        var newInput = $(newIn);
        var removeBtn = '<div class="form-group col-md-3 col-xs-12"><button type="button" style="margin-top:30px;"  id="remove' + (next - 1) + '" class="btn btn-danger btn-block remove-me" >Remove</button></div></div></div><div id="field">';
        var removeButton = $(removeBtn);
        $(addto).before(newInput);
        $(addRemove).before(removeButton);
        $("#field" + next).attr('data-source',$(addto).attr('data-source'));
        $("#count").val(next);  
        
            $('.remove-me').click(function(e){
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length-1);
                var fieldID = "#field" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });
    });

});
$(document).ready(function(){
	$('.select-help').change(function(){
		id = $(this).val();
		var is_country = $('.country_instance option:selected').val();
		alert(is_country);
		if(is_country == ''){
			bootbox.alert('Please select country');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getHelp_main_byhelp')?>',
			data: {parent_id: id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Help</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-help_main").html($option);
				$(".select-help_sub").html('<option value="">Select Help sub</option>');
			}
		})
	});
	
	$('.select-help_main').change(function(){
		id = $(this).val();
		var is_country = $('.country_instance option:selected').val();
		if(is_country == ''){
			bootbox.alert('Please select country');
			return false;
		}
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('masters/getHelp_sub_byhelp_main')?>',
			data: {parent_id: id, is_country: is_country},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select help sub</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-help_sub").html($option);
			}
		})
	});
	
	
	
});
</script>
 <script>
    $(document).ready(function() {
        $('#summernote').summernote();
    });
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
<script>
$(document).ready(function(){
	$(".country_instance").select2();
});
</script>