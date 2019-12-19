<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" type="text/css" media="all">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script type="text/javascript" src="<?=$assets?>js/bootstrapValidator.min.js"></script>

<script>
	$(document).ready(function () {

        $('form[class="add_from1"]').bootstrapValidator({
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                customer_type: {
                    validators: {
                        notEmpty: {
                            message: 'please select <?= lang('type') ?>'
                        }
                    }
                },
				 customer_id: {
                    validators: {
                        notEmpty: {
                            message: 'please select User'
                        }
                    }
                },
             start_date: {
                    validators: {
                        notEmpty: {
                            message: 'The date is required'
                        },
                     
                    }
                }, 
				end_date: {
                    validators: {
                        notEmpty: {
                            message: 'The date is required'
                        },
                    }
                },
			
            },
			submitButtons: 'input[type="submit"]'
		  
			
   
        });
	  $('#start_date').datepicker({
			
			autoclose: true,
			onSelect: function (date, inst) {
			
				$('.add_from1').bootstrapValidator('revalidateField', 'start_date'); // her, feil navn på plugin
			}
		});
		$('#end_date').datepicker({
			
			autoclose: true,
			onSelect: function (date, inst) {
			
				$('.add_from1').bootstrapValidator('revalidateField', 'end_date'); // her, feil navn på plugin
			}
		});
		
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
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from1', 'role' => 'form');
        echo admin_form_open_multipart("enquiry/create_customer", $attrib); ?>
        <div class="modal-body">
            <h2 class="box_he_de"><?= lang('enter_info'); ?></h2>
            <div class="form-group <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
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
            
            <div class="col-md-12">
            
            	<div class="form-group col-md-6 col-xs-12">
							<?= lang('type', 'type'); ?>
                            <?php
                            $c[''] = 'Select type';
							$c['5'] = 'Customer';
							$c['4'] = 'Driver';
							$c['3'] = 'Vendor';
                            
                            echo form_dropdown('customer_type', $c, '', 'class="form-control select-customer_type select"  id="customer_type" name="customer_type" required="required"'); ?>
                        </div>
                   
                        <div class="form-group col-md-6 col-xs-12">
                            <?= lang("user", "User"); ?>
                           <?php
						 $u[''] = 'Select User';
                            echo form_dropdown('customer_id', $u, '', 'class="form-control select-customer_id select" id="customer_id" name="customer_id" required="required"'); ?>
                        </div>
                    	
                        <div class="form-group col-md-6 col-xs-12">
							<?php echo lang('start_date', 'Start Date'); ?>
                            <div class="controls">
                                <input type="text" id="start_date1" name="start_date" class="form-control" onkeypress="dateCheck(this);"  value="<?= $_GET['sdate'] ?>"/>
                            </div>
                         </div>
                         
                         <div class="form-group col-md-6 col-xs-12">
							<?php echo lang('end_date', 'End Date'); ?>
                            <div class="controls">
                                <input type="text" id="end_date1" name="end_date" class="form-control" onkeypress="dateCheck(this);"   value="<?= $_GET['edate'] ?>"/>
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
		
	var start_date = $("#start_date1") .datepicker({
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
	
	var end_date = $("#end_date1") .datepicker({
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
		var is_country = $('#is_country').val();
		if(is_country == ''){
			bootbox.alert('Please select country');
			return false;
		}
		
		$(".select-customer_id").select2("destroy");
		id = $(this).val();
		$.ajax({
			type: 'POST',
			url: '<?=admin_url('enquiry/getUser_bygroup')?>',
			data: {group_id: id, is_country: is_country},
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