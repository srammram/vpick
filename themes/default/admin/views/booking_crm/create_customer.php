


<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
  
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

               

                <?php $attrib = array('class' => 'form-horizontal','class' => 'create_customer','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("booking_crm/create_customer", $attrib);
                ?>
                <div class="row">
                	
                    <div class="col-md-6">
                    	<div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
						<?php echo lang('instance_of_country', 'instance_of_country'); ?>
                        <select <?php if($this->session->userdata('group_id') == 1){ echo 'required'; } ?> class="form-control phonecode select is_country"  name="is_country" id="is_country">
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
                            <div class="form-group">
                            	<?php 
								if($this->session->userdata('group_id') != 1){
								$p = $this->site->getcountryCodeID($this->countryCode); 
								}
								?>
                                <?php echo lang('phonecode', 'phonecode'); ?>
                                <div class="controls">
                                    <input type="text" id="phonecode" name="phonecode" value="<?= $p->phonecode ?>" class="form-control" readonly
                                          />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('mobile', 'mobile'); ?>
                                <div class="controls">
                                    <input type="text" id="mobile" name="mobile" required class="form-control"  />
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <input type="text" id="name" name="name" required class="form-control" />
                                </div>
                            </div>
                          
                            
                        </div>
                       
                       
                    </div>                   
                </div>

                <p><?php echo form_submit('create_customer', lang('submit'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>

<script>
$('.create_customer').bootstrapValidator({
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
					
                   
                }
            },
            
            mobile:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The mobile'
                    },
					remote: {
						type: 'POST',
						url: '<?=admin_url('booking_crm/exituserRide')?>',
						data:{
							   phonecode: $('[name="phonecode"]').val(),
						   
						},
						
						message: 'Customer already riding. do not accept booking.',
						delay: 1000
                	}
                },
				
            },
			
			is_country:{
                validators: {
                    notEmpty: {
                        message: 'Please Select country'
                    }
                }
            },
			
           
            
        },
        submitButtons: 'input[type="submit"]'
    });
    </script>

<script>
$(document).on('change', '.phonecode', function(){
	
	var iso = $(this).val();
	$.ajax({
		type: 'POST',
		url: '<?=admin_url('masters/getCountrysAllData')?>',
		data: {iso: iso},
		dataType: "json",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			$('#phonecode').val(scdata.phonecode);
		}
	});
});

$(document).on('change', '#mobile', function(){
	var phonecode = $('#phonecode').val();
	var mobile = $(this).val();
	$.ajax({
		type: 'POST',
		url: '<?=admin_url('booking_crm/exitUser')?>',
		data: {mobile: mobile, phonecode: phonecode,},
		dataType: "json",
		cache: false,
		success: function (scdata) {
			console.log(scdata);
			if(scdata.name == ''){
				$('#name').attr('readonly', false);
			}else{
				$('#name').attr('readonly', true);
			}
			$('#name').val(scdata.name);
		}
	});
});

</script>

