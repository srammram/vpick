
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("usersenquiry/create_ticket/?customer_id=".$this->session->userdata('user_id')."&ride_id=".$ride_id."", $attrib);
				
                ?>
                
                <div class="row">
                	<div class="instance_country col-sm-12">
                	<div class="form-group col-sm-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control select is_country" name="is_country" id="is_country">
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
                        </div>
					  <div class="col-md-12">  
						<h2 class="box_he_de"><?= lang('enquiry_details') ?></h2>
						
                        
                        <input type="hidden" name="ride_id" value="<?= $ride_id ?>">
                    	<input type="hidden" name="customer_id" value="<?php echo $this->session->userdata('user_id') ?>" >
                        <div class="form-group col-md-6 col-xs-12">
							<?= lang('support_services', 'help_department'); ?>
                            <?php
                            $h[''] = 'Select Services';
                            foreach ($helps as $help) {
                                $h[$help->id] = $help->name;
                            }
                            echo form_dropdown('help_department', $h, '', 'class="form-control select-help_department select"  id="help_department" required="required"'); ?>
                        </div>
                    
                        <div class="form-group col-md-6 col-xs-12">
                            <?= lang("category", "category"); ?>
                           <?php
                            echo form_dropdown('help_main_id', '', '', 'class="form-control select-help_main_id select" id="help_main_id" required="required"'); ?>
                        </div>
                    
                        <div class="form-group col-md-6 col-xs-12">
                            <?= lang("sub_category", "sub_category"); ?>
                           <?php
                            echo form_dropdown('help_sub_id', '', '', 'class="form-control select-help_sub_id select" id="help_sub_id" required="required"'); ?>
                        </div>
                        
                        

					</div> 
                    
                    <div class="col-lg-12" id="help_form">
                    
                    </div>        
                </div>

                <div class="col-sm-12 last_sa_se"><?php echo form_submit('ticket', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?></div>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function(){
	
	$('.select-customer_type').change(function(){
		$(".select-customer_id").select2("destroy");
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('enquiry/getUser_bygroup')?>',
			data: {group_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select User</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-customer_id").html($option);
				$(".select-customer_id").select2();
			}
		})
	});
	
	$('.select-help_department').change(function(){
		$(".select-help_main_id").select2("destroy");
		$(".select-help_sub_id").select2("destroy");
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('enquiry/getHelp_main_byhelp')?>',
			data: {parent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Category</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-help_main_id").html($option);
				$(".select-help_sub_id").html('<option value="">Select Sub category</option>');
				$(".select-help_main_id").select2();
				$(".select-help_sub_id").select2();
			}
		})
	});
	
	$('.select-help_main_id').change(function(){
		$(".select-help_sub_id").select2("destroy");
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('enquiry/getHelp_sub_byhelp_main')?>',
			data: {parent_id: id},
			dataType: "json",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$option = '<option value="">Select Sub Category</option>';
				$.each(scdata,function(n,v){
					$option += '<option value="'+v.id+'">'+v.text+'</option>';
				});
				$(".select-help_sub_id").html($option);
				$(".select-help_sub_id").select2();
			}
		})
	});
	
	$('.select-help_sub_id').change(function(){
		
		id = $(this).val();
		$.ajax({
			type: 'GET',
			url: '<?=admin_url('enquiry/getHelp_form_byhelp_sub')?>',
			data: {parent_id: id},
			dataType: "html",
			cache: false,
			success: function (scdata) {
				$('#help_form').html(scdata);
			}
		})
	});
	
	
	
});
</script>
