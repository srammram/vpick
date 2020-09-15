<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('import_files'); ?></h2>
    </div>
    <div class="box-content">
        
            <div class="col-lg-12">
                
                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("account/bank_excel", $attrib)
                ?>
                
                   
                    	
						
                       
					
                        	<div class="form-group col-sm-4 col-xs-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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
                       
                        <input type="hidden" name="payment_mode" value="1">
                       
                        
                        <div class="form-group col-sm-4 col-xs-12">
							<?php echo lang('payment_gateway', 'Payment Gateway'); ?>
							<?php
							foreach ($payment_gateway as $pgateway) {
								$pg[$pgateway->id] = $pgateway->name;
							}

							echo form_dropdown('payment_gateway', $pg, '', 'class="tip form-control" required id="payment_gateway" data-placeholder="' . lang("select") . ' ' . lang("payment gateway") . '" ');
							?>
						</div>
                        
                       
                        
                        	
                            
                            <div class="form-group col-sm-4 col-xs-12">
                                <label for="csv_file1"><?= lang("upload_file"); ?></label>
                                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" class="form-control file" data-show-upload="false" data-show-preview="false" id="csv_file1" required="required"/>
                            </div>
							
                       
                        
                        <div class="clearfix"></div>
                            <div class="form-group col-lg-12">
                                <?php echo form_submit('import_files', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
                            </div>
                    
                
                <?= form_close(); ?>
            </div>
        
    </div>
    
</div>
<script>

$(document).on('change', '#is_country', function(){
		var group_id = '<?php echo $group_id ?>';
        var site = '<?php echo site_url() ?>';
		var is_country = $('#is_country').val();
	  window.location.href = site+"admin/account/bank_excel/?is_country="+is_country;
		
    
})
</script>