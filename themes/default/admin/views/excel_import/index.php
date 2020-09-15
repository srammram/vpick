<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Excel Import'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
        
            <div class="col-lg-12">
            	<?php 
                echo admin_form_open_multipart("excel_import/index/");
                ?>
                
                <div class="row">
                    <div class="col-md-6">
                    	<fieldset class="col-md-12">    	
                            <legend>Details</legend>
                                
                          
                             
                           	<div class="form-group all">
								<?= lang("excel_import", "excel_import") ?>
                                <input id="file" type="file" data-browse-label="<?= lang('browse'); ?>" name="import" data-show-upload="false"
                                       data-show-preview="false" class="form-control file" accept="im/*">
                            </div>
                           
                            
                          </fieldset>
                         
                       
                        
                     </div>
                     
                     
                 </div>
                <?php echo form_submit('submit', lang('submit'), 'class="btn btn-primary"'); ?>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
