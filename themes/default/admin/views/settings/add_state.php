<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_state'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator','class' => 'add_from', 'role' => 'form');
        echo admin_form_open_multipart("settings/add_state", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php
                        $countries_v[''] = '';
                        foreach ($countries as $key => $value) {
                            $countries_v[$value->id] = $value->country_name;
                        }
                        echo form_dropdown('country_id', $countries_v, '', 'class="form-control change-country select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("zone", "zone"); ?>
                        <?php
                        $zones_v[''] = '';                       
                        echo form_dropdown('zone_id', $zones_v, '', 'class="change-zone form-control select" id="change-country"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <?= lang('state_name', 'state_name'); ?>
                <?php echo form_input('state_name', '', 'class="form-control" id="state_name" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_state', lang('add_state'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>
<script>
    $(document).ready(function(){
          $('.change-country').change(function(){
            $country = $(this).val();
            console.log($country)
            $.ajax({
                        type: 'POST',
                        url: site_url+'settings/getZones_bycountry',
                        data: {country: $country},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            $option = '<option value="">Select zone</option>';
                            $.each(data,function(n,v){
                                $option += '<option value="'+v.id+'">'+v.text+'</option>';
                            });
                            $(".change-zone").html($option);
                        }
                })
        });
    })
</script>