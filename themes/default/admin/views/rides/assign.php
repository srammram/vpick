<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('assign_driver'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("rides/assign/".$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <?= lang("drivers", "drivers"); ?>
                <?php
                $drivers_v[''] = '';
                if(!empty($drivers)){
                foreach ($drivers as $key => $value) {
                    $drivers_v[$value->id] = $value->name;
                }
                }
                echo form_dropdown('driver_id', $drivers_v, '', 'class="form-control select" id="driver-id" required="required"'); ?>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('assign_driver', lang('assign_driver'), 'class="assign_driver btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script>
    $(document).ready(function(){
        $('.assign_driver').click(function(e){
            e.preventDefault();
            $id = $('#driver-id').val();
            if ($id!='') {
                $.ajax({
                                type: 'post',
                                url: site_url+'rides/assign/<?=$id?>',
                                data: {driver_id:$id},
                                success: function (data) {
                                    if (data==1) {
                                        
                                        window.location.reload();
                                    }
                                }
                });
            }
        });
    })
</script>
