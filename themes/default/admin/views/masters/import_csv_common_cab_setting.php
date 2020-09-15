<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('import_csv'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <a href="<?php echo base_url(); ?>assets/csv/sample_cab.csv" class="btn btn-primary pull-right">
                               <i class="fa fa-download"></i> <?= lang("download_sample_file") ?>
                            </a>
                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("masters/import_csv_common_cab_setting", $attrib)
                ?>
                
                    <div class="col-md-12">
                    	
						<div class="well well-small">
                            
                            <span class="text-warning"><?= lang("csv1"); ?></span>
                            <br/><?= lang("csv2"); ?> <span class="text-info">(<?= lang("cab_type") . ', ' . lang("cab_image") . ', ' . lang("cab_make") . ', ' .  lang("cab_model") . ', ' . lang("base_min_distance") . ', '. lang("base_min_distance_price") . ', '. lang("base_price_type") . ', '. lang("base_price_value") . ', '. lang("base_waiting_minute") . ', '. lang("base_waiting_price") . ', '. lang("package_name") . ', '. lang("package_price") . ', '. lang("option_type") . ', '. lang("option_price") . ', '. lang("package_distance") . ', '. lang("package_time") . ', '. lang("per_distance") . ', '. lang("per_distance_price") . ', '. lang("time_type") . ', '. lang("per_time") . ', '. lang("per_time_price") . ', '. lang("day_allowance") . ', '. lang("overnight_allowance") . ', '. lang("outstation_package_name") . ', '. lang("is_oneway") . ', '. lang("is_twoway") . ', '. lang("oneway_package_price") . ', '. lang("oneway_distance") . ', '. lang("twoway_package_price") . ', '. lang("twoway_distance") . ', '. lang("outstation_per_distance") . ', '. lang("outstation_per_distance_price") . ', '. lang("driver_allowance_per_day") . ', '. lang("driver_night_per_day") . ', '. lang("instance"); ?>
                                )</span> <?= lang("csv3"); ?>
                                <p><?= lang('images_location_tip'); ?></p>

                        </div>
                       

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="csv_file1"><?= lang("upload_file"); ?></label>
                                <input type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" class="form-control file" data-show-upload="false" data-show-preview="false" id="csv_file1" required="required"/>
                            </div>

                            <div class="form-group">
                                <?php echo form_submit('import_bank', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
                            </div>
                        </div>
                    </div>
                
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    
</div>
<style>
    .import-csv-karea li{
        list-style:none;
    }
    .kitchen_area label,.currency_area label,.warehouse_area label{
        font-weight: bold;
    }
</style>

