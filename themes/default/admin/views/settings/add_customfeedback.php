<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= lang('add_custom_feedback'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open("system_settings/add_customfeedback", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang('question', 'question'); ?>
                <?= form_input('question', set_value('question'), 'class="form-control tip" id="question" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang('question_type', 'question_type'); ?>
                <?php
                $opts = array('' => lang('select_type'), '1' => lang('input'), '2' => lang('textarea'), '3' => lang('radio'), '4' => lang('checkbox'), '5' => lang('select'));
				echo form_dropdown('question_type', $opts, '', 'class="form-control" id="question_type" required="required"');
				?>
            </div>

            <div class="form-group">
                <?= lang('number_answer', 'number_answer'); ?>
                <?= form_input('number_answer', set_value('number_answer'), 'class="form-control tip" id="number_answer" required="required"'); ?>
            </div>

            <div id="answer">
            	
                
            </div>
            
        </div>
        <div class="modal-footer">
            <?= form_submit('add_customfeedback', lang('add_custom_feedback'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<?= $modal_js ?>

<script>
$(document).on('change', '#number_answer', function(){
	var question_type = $('#question_type').val();
	var num = $(this).val();
	var html_data = '';
	if(question_type == 3 || question_type == 4 || question_type == 5){
		 for (var x = 1; x <= num; x++) {
			html_data += '<div class="form-group"><label>Answer'+x+'</label><input type="text" name="answer[]" class="form-control tip" required="required"></div>';
		}
	}
	$("#answer").html(html_data);
});
</script>
