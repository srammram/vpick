<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('add_taxi'); ?></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("taxi/add", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?>
            </div>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("type", "type"); ?>
                        <?php
                        $types_v[''] = '';
                        foreach ($types as $key => $value) {
                            $types_v[$value->id] = $value->name;
                        }
                        echo form_dropdown('type', $types_v, '', 'class="form-control select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("brand", "brand"); ?>
                        <?php
                        $brands_v[''] = '';
                        foreach ($brands as $key => $value) {
                            $brands_v[$value->id] = $value->name;
                        }
                        echo form_dropdown('brand', $brands_v, '', 'class="form-control taxi-brands select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("model", "model"); ?>
                       
                        <?php echo form_dropdown('model', '', '', 'class="form-control taxi-models select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("color", "color"); ?>
                        <?php
                        $colors_v[''] = '';
                        foreach ($colors as $key => $value) {
                            $colors_v[$value->id] = $value->name;
                        }
                        echo form_dropdown('color', $colors_v, '', 'class="form-control select" required="required"'); ?>
                    </div>
            </div>
            <div class="col-md-5 col-md-offset-1">
                                <div class="form-group all">
                                <?= lang("photo", "photo") ?>
                                <input id="photo" type="file" data-browse-label="<?= lang('browse'); ?>" name="photo" data-show-upload="false"
                                       data-show-preview="false" accept="image/*" class="form-control file">
                            </div>
                        </div>
            <div style="clear: both;height: 10px;"></div>
            
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_taxi', lang('add_taxi'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
 </div>

        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.taxi-brands').change(function(){
            $brand = $(this).val();
            console.log($brand)
            $.ajax({
                        type: 'POST',
                        url: '<?=admin_url('taxi/getModels_bybrand')?>',
                        data: {brand: $brand},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            console.log(data);
                            $('.taxi-models').html('');
                            $option = '<option value="">select model</option>';
                            $.each(data,function(n,v){
                                $option += '<option value="'+v.id+'">'+v.model_name+'</option>';
                            });
                            $('.taxi-models').append($option);
                        }
                })
        });
    })
</script>