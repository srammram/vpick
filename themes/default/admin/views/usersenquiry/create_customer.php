
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_ticket'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("usersenquiry/create_customer", $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group col-sm-12 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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
            
            <div class="col-md-12">
            	
                <input type="hidden" name="customer_id" value="<?= $this->session->userdata('user_id') ?>">
                <input type="hidden" name="customer_type" value="<?= $this->session->userdata('group_id') ?>">
            	
                    	
                        <div class="form-group col-md-6 col-xs-12">
							<?php echo lang('start_date', 'Start Date'); ?>
                            <div class="controls">
                                <input type="text" id="start_date" name="start_date" required class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                            </div>
                         </div>
                         
                         <div class="form-group col-md-6 col-xs-12">
							<?php echo lang('end_date', 'End Date'); ?>
                            <div class="controls">
                                <input type="text" id="end_date" name="end_date" required class="form-control" onkeypress="dateCheck(this);"  value="<?= $_GET['edate'] ?>"/>
                            </div>
                        </div>
                
            
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('ticket', lang('submit'), 'class="btn btn-primary change_btn_save center-block"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
$(document).ready(function(){
	
	
	function getDate(element) {
     var date;
     try {
       date = element.value;
     } catch (error) {
       date = null;
     }

     return date;
   }

	var dateFormat =  "dd/mm/yy";
		
	var start_date = $("#start_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		maxDate: 0,
		numberOfMonths: 1,
		
		
	})
	.on("change", function() {
		end_date.datepicker("option", "minDate", getDate(this));
	});
	
	var end_date = $("#end_date") .datepicker({
       defaultDate: "+1w",
	   
	   dateFormat: "dd/mm/yy" ,
		changeMonth: true,
		changeYear: true,
		
		maxDate: 0,
		numberOfMonths: 1
	})
	.on("change", function() {
		start_date.datepicker("option", "maxDate", getDate(this));
	});
	
	/*$('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var sdate = $('#start_date').val();
		var edate = $('#end_date').val();
		var is_country = $('#is_country').val();
		var approved = $('#approved').val();
		window.location.href = site+"people/driver?sdate="+sdate+"&edate="+edate+"&approved="+approved;
		
    });*/

});

</script>
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
	
	
	
});
</script>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>

<?= @$modal_js ?>