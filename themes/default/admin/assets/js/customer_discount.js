$(document).ready(function(){
    $dis_applied = {};
    
    /**** recipe group hide.show - both add/edit *****/ 
    
    $(document).on('click','.list-group',function(e){
            e.preventDefault();
            $obj = $(this);
            //console.log(55)
            $thislist = $obj.parents('.discount-container').find('.recipe-group-list').toggle();
            $('.recipe-group-list').not($obj.parents('.discount-container').find('.recipe-group-list')).hide();
            //if ($obj.parent('div').find('.recipe-group-list')) {
            //    //code
            //}
            $obj.parents('.discount-container').find('.recipe-group-list input').each(function(){
                $thisobj = $(this);
                $v = $thisobj.val();
                $i = $thisobj.attr('data-index');
                $thisobj.attr('disabled',false);
                $thisobj.parent('div').removeAttr('title'); 
                if($dis_applied[$v] != undefined && $dis_applied[$v]!=$i){
                 $thisobj.parent('div').css('background-position','-97px 0');
                 $dis_val = $('input[name="group['+$dis_applied[$v]+'][discount]"]').val();
                 $thisobj.parent('div').attr('title','Applied '+$dis_val+'%');   
                 $thisobj.attr('disabled','disabled');
                 //  $(this).iCheck('disable')
                }
            });
    });
    /****** Recipe group select/unselect *****/
    $(document).on('ifChanged','.icheckbox_square-blue input.recipe-group', function (e) {
    //$(".icheckbox_square-blue input").on('ifChanged', function (e) {
      $this = $(this);
      if (($this).is(':checked')) {
          $val = $this.val();
          $index = $this.attr('data-index');
          $dis_applied[$val] = $index;
      }else{
          $val = $this.val();
          delete $dis_applied[$val];
      }
      if ($(".discount-container:first .recipe-group").length==$(".discount-container:first .recipe-group:checked").length) {
          $('#apply-all').iCheck('check');
      }else{
          $('#apply-all').iCheck('uncheck');
      }
    });
    /****** Remove discount *****/
    $(document).on('click','.remove-discount',function(e){
        e.preventDefault();
        $obj = $(this);//console.log(22);
        $('.recipe-group-list').not($obj.parents('.discount-container').find('.recipe-group-list')).hide();
        $index = $obj.attr('data-index');
        bootbox.confirm({
            message: "Do You want to remove this discount?",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
                    callback: function (result) {
                        $existing = false;
                       if($obj.parents('.discount-container').hasClass('existing-discount')){
                        $existing = true;
                       }
                       $obj.parents('.discount-container').remove();
                       console.log($dis_applied);
                        for(var i in $dis_applied){
                            if($dis_applied[i]==$index){
                                delete $dis_applied[i];
                            }
                        }
                       console.log($dis_applied);
                       if ($existing) {
                        bootbox.alert('Discount has been removed successfully.Please submit form');
                       }else{
                        bootbox.alert('Discount has been removed successfully.');
                       }
                      
                    }
        });
    });
    //$(document).bind('submit','#edit-cus-dis-form',function(e){
    //    $index = 1;
    //    $dis_error = [];
    //    $group_error = [];
    //    $('.discount-container:visible').each(function(i,v){
    //        if($(this).find('input[type="text"].discount').val()==''){
    //            $dis_error.push($index);
    //        }
    //        if ($(this).find('.recipe-group:checked').length==0) {
    //            $group_error.push($index);
    //        }
    //        $index++;
    //    });
    //    if ($dis_error.length>0 || $group_error.length>0) {
    //        e.preventDefault(); e.stopImmediatePropagation();
    //        bootbox.alert('Discount field should not be empty. Please select atleast one group or else remove');
    //        $('#edit-cus-dis-form input[type="submit"]').attr('disabled',false);
    //        return false;
    //    }else{
    //        //console.log(55)
    //        //$('#edit-cus-dis-form').submit();
    //        return true;
    //    }
    //    return false;
    //    
    //});
})