<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
           <div class="row">
           <div class="col-lg-12">
			<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'settlement-form', 'role' => 'form', 'autocomplete' => "off");
            echo admin_form_open_multipart("account/settlement_branch/", $attrib);
            ?>
            
           <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
                <div class="form-group">
                <?php echo lang('country', 'Country'); ?>
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
            </div> 
            
            <div class="col-lg-3">
                <div class="form-group">
                    <?php echo lang('settlement_date', 'settlement_date'); ?>
                    <div class="controls">
                        <input type="text" id="settlement_date" name="settlement_date" value="<?= date('d/m/Y')?>" required class="form-control" onkeypress="dateCheck(this);" />
                    </div>
                 </div>
            </div>
            <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
				<?php echo lang('settlement_type', 'settlement_type'); ?>
                <?php
                $pm[0] = 'Bank';
                $pm[1] = 'Cash';
                

                echo form_dropdown('settlement_type', $pm, '', 'class="tip form-control" required id="settlement_type" data-placeholder="' . lang("select") . ' ' . lang("settlement_type") . '" ');
                ?>
            </div>
            <div class="form-group all col-sm-3 col-xs-12">
			<?= lang("bank_challan", "bank_challan") ?>
            <input id="bank_challan" type="file" data-browse-label="<?= lang('browse'); ?>" name="bank_challan" data-show-upload="false"
                   data-show-preview="false" class="form-control file" accept="im/*">
            </div>
            <div class="clearfix"></div>
            
            <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
				<?php echo lang('branch', 'branch'); ?>
                <?php
				$b[''] = 'Select Branch';
                foreach($branch_company as $branch){
					$b[$branch->id] = $branch->name;
				}
                echo form_dropdown('from_company_id', $b, '', 'class="tip form-control" required id="from_company_id" data-placeholder="' . lang("select") . ' ' . lang("branch") . '" ');
                ?>
            </div>
            
            <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
				<?php echo lang('branch_bank', 'branch_bank'); ?>
                <?php
				$bb[''] = 'Select Bank';
                
                echo form_dropdown('from_bank_id', $bb, '', 'class="tip form-control" required id="from_bank_id" data-placeholder="' . lang("select") . ' ' . lang("bank") . '" ');
                ?>
            </div>
            
            
            
            <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
				<?php echo lang('head_office', 'head_office'); ?>
                <?php
				$h[''] = 'Select HeadOffice';
                foreach($head_company as $headoffice){
					$h[$headoffice->id] = $headoffice->name;
				}
                echo form_dropdown('to_company_id', $h, '', 'class="tip form-control" required id="to_company_id" data-placeholder="' . lang("select") . ' ' . lang("headoffice") . '" ');
                ?>
            </div>
            
            <div class="form-group col-sm-3 col-xs-12 payment_gateway_id">
				<?php echo lang('head_office_bank', 'head_office_bank'); ?>
                <?php
               	$hh[''] = 'Select Bank';
                echo form_dropdown('to_bank_id', $hh, '', 'class="tip form-control" required id="to_bank_id" data-placeholder="' . lang("select") . ' ' . lang("bank") . '" ');
                ?>
            </div>
            
            <div class="col-lg-12" id="pandingBranch">
            	
            </div>
            
            <div class="col-sm-12 last_sa_se">
                <?php echo form_submit('settlement_branch', lang('submit'), 'class="btn btn-primary change_btn_save center-block settlement"'); ?>
             </div>
            <?php echo form_close(); ?>
            </div>
              	

        </div>
    </div>
</div>
<script>
$(document).ready(function(){
	
	var dateFormat =  "dd/mm/yy";
	var start_date = $("#settlement_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: false,
		changeYear: false,
		maxDate: 0,
		numberOfMonths: 1,
		yearRange: '-100:+0',
	});
	

});


$(document).on('change', '#from_company_id', function(){
	
	var is_country = $('#is_country').val();
	var settlement_type = $('#settlement_type').val();
	var settlement_date = $('#settlement_date').val();
	var company_id = $(this).val();
	$("#from_bank_id").select2("destroy");
	$.ajax({
		type: 'POST',
		url: '<?=admin_url('masters/getCompanyBank')?>',
		data: {is_country: is_country, settlement_type: settlement_type, settlement_date: settlement_date, company_id: company_id, company_type: 1},
		dataType: "json",
		cache: false,
		success: function (scdata) {
			
			$option = '<option value="">Select Bank</option>';
			$.each(scdata.bank,function(n,v){
				$option += '<option value="'+v.id+'">'+v.text+'</option>';
			});
			$pending = '';
			
			if(scdata.pending != false){
				$.each(scdata.pending,function(n,v){
					$pending += '<input type="checkbox" checked name="account_id[]" value="'+v.id+'"> "'+v.account_transaction_no+'" - "'+v.debit+'" ';
				});
				$("#pandingBranch").html($pending);	
			}else{
				
				$pending = '<h4>No Pending Data</h4>';
				$("#pandingBranch").html($pending);		
				$('.settlement').attr('disabled', true);
			}
			$("#from_bank_id").html($option);				
			$("#from_bank_id").select2();
			
		}
	});
});

$(document).on('change', '#to_company_id', function(){
	
	var is_country = $('#is_country').val();
	var settlement_type = $('#settlement_type').val();
	var settlement_date = $('#settlement_date').val();
	var company_id = $(this).val();
	$("#to_bank_id").select2("destroy");
	$.ajax({
		type: 'POST',
		url: '<?=admin_url('masters/getCompanyBank')?>',
		data: {is_country: is_country, settlement_type: settlement_type, settlement_date:settlement_date,  company_id: company_id, company_type: 0},
		dataType: "json",
		cache: false,
		success: function (scdata) {
			$option = '<option value="">Select Bank</option>';
			$.each(scdata.bank,function(n,v){
				$option += '<option value="'+v.id+'">'+v.text+'</option>';
			});
			
			$("#to_bank_id").html($option);				
			$("#to_bank_id").select2();
			
		}
	});
});

$(document).on('change', '#is_country', function(){
	var site = '<?php echo site_url() ?>';
	var is_country = $('#is_country').val();
	window.location.href = site+"admin/account/settlement_branch/?is_country="+is_country;
});
</script>
