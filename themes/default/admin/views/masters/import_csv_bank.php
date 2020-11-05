<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('import_csv'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <a href="<?php echo base_url(); ?>assets/csv/sample_bank.csv" class="btn btn-primary pull-right">
                               <i class="fa fa-download"></i> <?= lang("download_sample_file") ?>
                            </a>
                <?php
                $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("masters/import_csv_bank", $attrib)
                ?>
                
                    <div class="col-md-12">
                    	
						<div class="well well-small">
                            
                            <span class="text-warning"><?= lang("csv1"); ?></span>
                            <br/><?= lang("csv2"); ?> <span class="text-info">(<?= lang("account_holder_name") . ', ' . lang("account_no") . ', ' . lang("bank_name") . ', ' .  lang("branch_name") . ', ' . lang("ifsc_code") . ', ' . lang("instance"); ?>
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

