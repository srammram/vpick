function rating(element) {
    var ratingElement = '<span class="stars">' + '<i class="fa fa-star s1" data-score="1"></i>' + '<i class="fa fa-star s2" data-score="2"></i>' + '<i class="fa fa-star s3" data-score="3"></i>' + '<i class="fa fa-star s4" data-score="4"></i>' + '<i class="fa fa-star s5" data-score="5"></i>' + '</span>';
    if (!element) {
        element = '';
    }
    $.each($(element + ' .rating'), function(i) {
        $(this).append(ratingElement);
        if ($(this).hasClass('active')) {
            $(this).append('<input readonly hidden="" name="score_' + $(this).attr('data-name') + '" id="score_' + $(this).attr('data-name') + '">');
        }
        var rating = $(this).attr('data-rating');
        for (var e = 0; e < rating; e++) {
            var rate = e + 1;
            $(this).children('.stars').children('.s' + rate).addClass('active');
        }
    });
    var ratingActive = $('.rating.active i');
    ratingActive.on('hover', function() {
        for (var i = 0; i < $(this).attr('data-score'); i++) {
            var a = i + 1;
            $(this).parent().children('.s' + a).addClass('hover');
        }
    }, function() {
        for (var i = 0; i < $(this).attr('data-score'); i++) {
            var a = i + 1;
            $(this).parent().children('.s' + a).removeClass('hover');
        }
    });
    ratingActive.on('click', function() {
        $(this).parent().parent().children('input').val($(this).attr('data-score'));
        $(this).parent().children('.fa').removeClass('active');
        for (var i = 0; i < $(this).attr('data-score'); i++) {
            var a = i + 1;
            $(this).parent().children('.s' + a).addClass('active');
        }
        return false;
    });
}