
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <?php $attrib = array('class' => 'form-horizontal','class' => 'add_from','data-toggle' => 'validator', 'role' => 'form', 'autocomplete' => "off");
                echo admin_form_open_multipart("enquiry/create_customer/", $attrib);
				
                ?>
                
                <div class="row">
					  <div class="col-md-12">  
						<h2 class="box_he_de">Enquiry Details</h2>
						
                        <div class="form-group col-md-3 col-xs-12">
							<?= lang('type', 'type'); ?>
                            <?php
                            $c[''] = 'Select type';
							$c['5'] = 'Customer';
							$c['4'] = 'Driver';
							$c['3'] = 'Vendor';
                            
                            echo form_dropdown('customer_type', $c, '', 'class="form-control select-customer_type select"  id="customer_type" required="required"'); ?>
                        </div>
                   
                        <div class="form-group col-md-3 col-xs-12">
                            <?= lang("User", "User"); ?>
                           <?php
                            echo form_dropdown('customer_id', '', '', 'class="form-control select-customer_id select" id="customer_id" required="required"'); ?>
                        </div>
                    	
                        <div class="form-group col-md-3 col-xs-12">
							<?php echo lang('start_date', 'Start Date'); ?>
                            <div class="controls">
                                <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                            </div>
                         </div>
                         
                         <div class="form-group col-md-3 col-xs-12">
							<?php echo lang('end_date', 'End Date'); ?>
                            <div class="controls">
                                <input type="text" id="end_date" name="end_date" class="form-control" onkeypress="dateCheck(this);"  value="<?= $_GET['edate'] ?>"/>
                            </div>
                        </div>
                         
                        <div id="rides"></div>
                        
                        

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
	
	
	function getDate(element) {
     var date;
     try {
       date = $.datepicker.parseDate(dateFormat, element.value);
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
	
	$('.select-customer_id').change(function(){
		var customer_type = $('#customer_type').val();
		var id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('enquiry/getRide_byuser')?>',
			data: {user_id: id, customer_type: customer_type},
			dataType: "html",
			cache: false,
			success: function (scdata) {
				console.log(scdata);
				$('#rides').html(scdata);
			}
		})
	});
	
	
});
</script>
