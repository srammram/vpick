<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_city'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'class' => 'add_from','role' => 'form');
        echo admin_form_open_multipart("settings/add_city", $attrib); ?>
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
                        echo form_dropdown('country_id', $countries_v, '', 'class="form-control select-country select" required="required"'); ?>
                    </div>
            </div>
             <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("state", "state"); ?>
                       <?php
                        //$models_v[''] = '';
                        //foreach ($models as $key => $value) {
                        //    $models_v[$value->id] = $value->model_name;
                        //}
                        echo form_dropdown('state_id', '', '', 'class="form-control select-state select"  required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-12">
            <div class="form-group">
                <?= lang('city_name', 'city_name'); ?>
                <?php echo form_input('city_name', '', 'class="form-control" id="city_name" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('latitude', 'latitude'); ?>
                <?php echo form_input('latitude', '', 'class="form-control" id="latitude" required="required"'); ?>
            </div>
            <div class="form-group">
                <?= lang('longitude', 'longitude'); ?>
                <?php echo form_input('longitude', '', 'class="form-control" id="longitude" required="required"'); ?>
            </div>
            </div>
            
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_city', lang('add_city'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= @$modal_js ?>
<script>
    $(document).ready(function(){
        $('.select-country').change(function(){
            $country = $(this).val();
            console.log($country)
            $.ajax({
                        type: 'POST',
                        url: '<?=admin_url('settings/getStates_bycountry')?>',
                        data: {country: $country},
                        dataType: "json",
                        cache: false,
                        success: function (scdata) {
                            console.log(scdata);
                            $option = '<option value="">Select state</option>';
                            $.each(scdata,function(n,v){
                                $option += '<option value="'+v.id+'">'+v.text+'</option>';
                            });
                            $(".select-state").html($option);
                        }
                })
        });
    })
</script>