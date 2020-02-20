<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box">
    <?php /*?><div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('driver_payment_status'); ?></h2>
    </div><?php */?>
    <div class="box-content">
           <div class="row">
           <div class="col-lg-12">
			<?php $attrib = array('class' => 'form-horizontal','class' => 'edit_from', 'data-toggle' => 'validator', 'id' => 'settlement-form', 'role' => 'form', 'autocomplete' => "off");
            echo admin_form_open_multipart("account/reconcilation/", $attrib);
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
            
           
            
            <div class="clearfix"></div>
            
            
            <div class="col-lg-12" id="pandingReconcilation_date">
            	<table class="table">
                	<thead>
                    	<tr>
                        	<th>CheckBox</th>
                            <th>Transaction NO</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php
						if(!empty($reconcilation)){
							$disabled = '';
							foreach($reconcilation as $row){
						?>
                    	<tr>
                        	<td><input type="checkbox" checked name="account_id[]" value="<?= $row->id ?>"></td>
                            <td><?= $row->account_transaction_no ?></td>
                            <td><?= $row->debit ?></td>
                            
                            <td><?= $row->account_transaction_date ?></td>
                        </tr>
                        <?php
							}
						}else{
						?>
                        <tr>
                        	<td colspan="3">No data</td>
                        </tr>
                        <?php
							$disabled = 'disabled';
						}
						?>
                    </tbody>
                </table>
            </div>
            
            <div class="col-sm-12 last_sa_se">
                <?php echo form_submit('reconcilation', lang('submit'), ' '.$disabled.' class="btn btn-primary change_btn_save center-block reconcilation"'); ?>
             </div>
            <?php echo form_close(); ?>
            </div>
              	

        </div>
    </div>
</div>
<script>

$(document).on('change', '#is_country', function(){
	var site = '<?php echo site_url() ?>';
	var is_country = $('#is_country').val();
	window.location.href = site+"admin/account/reconcilation/?is_country="+is_country;
});
</script>
