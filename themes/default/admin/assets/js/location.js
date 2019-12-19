$(document).ready(function(){
    
        $('.select-country').change(function(){
            $country = $(this).val();
            console.log($country)
            $.ajax({
                        type: 'POST',
                        url: site_url+'masters/getStates_bycountry',
                        data: {country: $country},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                            $('select.select-state').html('');
                            $('select#select-state').select2('val', '');
                            $('select.select-city').html('');
                            $('select#select-city').select2('val', '');
                            $.each(data,function(n,v){
                            console.log(v.text+v.id)
                            var newOption = new Option(v.text, v.id, true, true);
                            // Append it to the select
                            $('select#select-state').append(newOption);
                            });
                        }
                })
        });

       $("#select-state").on("change", function(e) {
            $state = $("#select-state").select2("val");
            console.log($state)
            $.ajax({
                        type: 'POST',
                        url: site_url+'masters/getcities_byStates',
                        data: {state: $state},
                        dataType: "json",
                        cache: false,
                        success: function (data) {
                           $('select.select-city').html('');
                            $('select#select-city').select2('val', '');
                            $.each(data,function(n,v){
                            
                            var newOption = new Option(v.text, v.id, true, true);
                            // Append it to the select
                            $('select#select-city').append(newOption);
                            });
                        }
                })
        });
});
