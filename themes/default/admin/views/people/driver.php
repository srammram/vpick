<script>
    /*function driver1_status(x) {
        var y = x.split("__");
        return y[0] == 1 ?
        '<a href="'+site.base_url+'people/driver_status/active/'+ y[1] +'"><span class="label label-success">  '+lang['active']+'</span></a>' :
        '<a href="'+site.base_url+'people/driver_status/deactive/'+ y[1] +'"><span class="label label-danger">  '+lang['inactive']+'</span><a/>';
    }*/
	
	/*function jointype_status(w) {
		
        var c = w.split("__");
		
        return c[1] == 1 ?
        '<a href="'+site.base_url+'people/driver_adminedit/'+ c[0] +'"><span class="label label-info">  Pending </span></a>' :
		
		 
		 '<a href="javascript:void(0);"><span class="label label-warning">  Complete</span></a>' :
		 
		
        '<a href="javascript:void(0);"><span class="label label-primary">  Complete</span><a/>';
    }*/
	
	function jointype_status(w) {
		
         var c = w.split("__");
		
        return c[1] == 1 ?
        '<a href="javascript:void(0);"><span class="label label-success"> Complete</span></a>' :
        '<a href="'+site.base_url+'people/driver_adminedit/'+ c[0] +'"><span class="label label-danger">Pending</span><a/>';
		
    }
	
	function active_status(x) {
		
        var y = x.split("__");
		
        return y[1] == 1 ?
        '<a href="'+site.base_url+'people/status/deactive/'+ y[0] +'"><span class="label label-success">  '+lang['active']+'</span></a>' :
        '<a href="'+site.base_url+'people/status/active/'+ y[0] +'"><span class="label label-danger">  '+lang['inactive']+'</span><a/>';
    }
	
	function verify_status(b) {
		
        var z = b.split("__");
		
        return z[1] == 1 ?
        '<a href="'+site.base_url+'people/driver_edit/'+ z[0] +'/view"><span class="label label-success">  Verified</span></a>' :
		 z[2] == 0 ?
		 '<a href="javascript:void(0)"><span class="text-warning"> Admin Status Pending</span></a>' :
        '<a href="'+site.base_url+'people/driver_status/deactive/'+ z[0] +'"><span class="label label-danger">  Not Verified</span><a/>';
    }
	
	
	function mobile_status(mob) {
		
		var mobile = mob.slice(-4);		
		return '******'+mobile;
    }
	
    $(document).ready(function () {
        'use strict';
        oTable = $('#UsrTable').dataTable({
            "aaSorting": [[0, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('people/getDriver?sdate='.$_GET['sdate'].'&edate='.$_GET['edate'].'&is_country='.$_GET['is_country'].'&cab='.$_GET['cab']) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [ {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": empty_status}, {"mRender": mobile_status},  {"mRender": empty_status},  {"mRender": verify_status}, {"mRender": jointype_status}, {"mRender": empty_status}, {"bSortable": true}]
        });
    });
</script>
<style>.table td:nth-child(6) {
        text-align: left;
        width: 7%;
    }

    .table td:nth-child(8) {
        text-align: center;
    }</style>
<?php if ($Owner) {
    echo admin_form_open('people/driver_actions', 'id="action-form"');
} ?>
<div class="box">
  <a href="<?= admin_url('people/add_driver'); ?>"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-plus-circle"></i> <?= lang("add_driver"); ?></button></a>
 
    <a href="javascript:void(0)" id="excel" data-action="export_excel"><button type="button" class="btn btn-primary add_se_btn center-block"><i class="fa fa-file-excel-o"></i> <?= lang("export_to_excel"); ?></button></a>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                
                <div class="col-lg-3">
            <div class="form-group">
                <?php echo lang('start_date', 'Start Date'); ?>
                <div class="controls">
                    <input type="text" id="start_date" name="start_date" class="form-control" onkeypress="dateCheck(this);" value="<?= $_GET['sdate'] ?>"/>
                </div>
             </div>
            </div>
            <div class="col-lg-3">        
                    <div class="form-group">
                        <?php echo lang('end_date', 'End Date'); ?>
                        <div class="controls">
                            <input type="text" id="end_date" name="end_date" class="form-control" onkeypress="dateCheck(this);"  value="<?= $_GET['edate'] ?>"/>
                        </div>
                    </div>
                    
            </div>
            <div class="col-lg-3 <?php if($this->session->userdata('group_id') != 1){ echo 'hidden'; } ?>">
            <div class="form-group">
            <?php echo lang('country', 'Country'); ?>
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
            
            <div class="col-lg-3 row">
            <div class="form-group col-lg-7">
                <?php echo lang('&nbsp;'); ?><br>
               <a href="javascript:void(0)" id="filte_ride" class="btn btn-primary btn-block"><?= lang('search') ?></a>
            </div>
           
            <div class="form-group col-lg-5">            	
                <?php echo lang('&nbsp;'); ?><br>
                 <?php
				$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
				?>
               <a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$uri_parts[0]; ?>" id="resetfilter"  class="btn btn-primary btn-block"><?= lang('reset') ?></a>
            </div>            
            </div>
            <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="UsrTable" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-hover table-striped display responsive nowrap">
                        <thead>
                        <tr>
                            <th ><?php echo lang('create_date'); ?></th>
                            <th ><?php echo lang('refer_code'); ?></th>
                            <th ><?php echo lang('first_name'); ?></th>
                            <th ><?php echo lang('last_name'); ?></th>
                            <th ><?php echo lang('email'); ?></th>
                            <th><?php echo lang('mobile'); ?></th>
                            <th ><?php echo lang('gender'); ?></th>
                           
                            <th ><?php echo lang('approved'); ?></th>
                            <th><?php echo lang('admin'); ?></th>
                            <th style="width: 33.33%!important;"><?php echo lang('instance'); ?></th>
                            <th><?php echo lang('actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                       
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>

    <script language="javascript">
        $(document).ready(function () {
            $('#set_admin').click(function () {
                $('#usr-form-btn').trigger('click');
            });

        });
    </script>

<?php } ?>

<script>
$(document).ready(function(){
	var m_new = new Date();
	var month_new = m_new.getMonth() - <?= $due_month ?>;
	m_new.setMonth(month_new);
	
	var yearRangeMin =  '-<?= $due_year ?>:+0';
	var yearRangeMax =  '-0:+<?= $due_year ?>';
	
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
		yearRange: '-100:+0',
		
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
	
	$('#filte_ride').click(function(e) {
        var site = '<?php echo site_url() ?>';
		var sdate = $('#start_date').val();
		var edate = $('#end_date').val();
		var is_country = $('#is_country').val();
		var approved = $('#approved').val();
		window.location.href = site+"admin/people/driver?sdate="+sdate+"&edate="+edate+"&approved="+approved+"&is_country="+is_country;
		
    });

});

</script>
<script>
//var date = document.getElementById('start_date');



</script>