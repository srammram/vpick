$(window).load(function () {
    $("#loading").fadeOut("slow");
});

function cssStyle() {
    if ($.cookie('sma_style') == 'light') {
        $('link[href="' + site.assets + 'styles/blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/blue.css"]').remove();

        $('link[href="' + site.assets + 'styles/dark_blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/dark_blue.css"]').remove();
        $('<link>')
            .appendTo('head')
            .attr({
                type: 'text/css',
                rel: 'stylesheet'
            })
            .attr('href', site.assets + 'styles/light.css');
    } else if ($.cookie('sma_style') == 'blue') {
        $('link[href="' + site.assets + 'styles/light.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/light.css"]').remove();

        $('link[href="' + site.assets + 'styles/dark_blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/dark_blue.css"]').remove();
        $('<link>')
            .appendTo('head')
            .attr({
                type: 'text/css',
                rel: 'stylesheet'
            })
            .attr('href', '' + site.assets + 'styles/blue.css');
    } else if ($.cookie('sma_style') == 'dark_blue') {
        $('link[href="' + site.assets + 'styles/light.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/light.css"]').remove();

        $('link[href="' + site.assets + 'styles/blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/blue.css"]').remove();
        $('<link>')
            .appendTo('head')
            .attr({
                type: 'text/css',
                rel: 'stylesheet'
            })
            .attr('href', '' + site.assets + 'styles/dark_blue.css');
    } else {
        $('link[href="' + site.assets + 'styles/light.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/dark_blue.css"]').attr('disabled', 'disabled');
        $('link[href="' + site.assets + 'styles/light.css"]').remove();
        $('link[href="' + site.assets + 'styles/blue.css"]').remove();
        $('link[href="' + site.assets + 'styles/dark_blue.css"]').remove();
    }

    if ($('#sidebar-left').hasClass('minified')) {
        $.cookie('sma_theme_fixed', 'no', {
            path: '/'
        });
        $('#content, #sidebar-left, #header').removeAttr("style");
        $('#sidebar-left').removeClass('sidebar-fixed');
        $('#content').removeClass('content-with-fixed');
        $('#fixedText').text('Fixed');
        $('#main-menu-act').addClass('full visible-md visible-lg').show();
        $('#fixed').removeClass('fixed');
    } else {
        if (site.settings.rtl == 1) {
            $.cookie('sma_theme_fixed', 'no', {
                path: '/'
            });
        }
        if ($.cookie('sma_theme_fixed') == 'yes') {
            $('#content').addClass('content-with-fixed');
            $('#sidebar-left').addClass('sidebar-fixed').css('height', $(window).height() - 80);
            $('#header').css('position', 'fixed').css('top', '0').css('width', '100%');
            $('#fixedText').text('Static');
            $('#main-menu-act').removeAttr("class").hide();
            $('#fixed').addClass('fixed');
            $("#sidebar-left").css("overflow", "hidden");
            $('#sidebar-left').perfectScrollbar({
                suppressScrollX: true
            });
        } else {
            $('#content, #sidebar-left, #header').removeAttr("style");
            $('#sidebar-left').removeClass('sidebar-fixed');
            $('#content').removeClass('content-with-fixed');
            $('#fixedText').text('Fixed');
            $('#main-menu-act').addClass('full visible-md visible-lg').show();
            $('#fixed').removeClass('fixed');
            $('#sidebar-left').perfectScrollbar('destroy');
        }
    }
    widthFunctions();
}
$('#csv_file').change(function (e) {
    v = $(this).val();
    if (v != '') {
        var validExts = new Array(".csv");
        var fileExt = v;
        fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
        if (validExts.indexOf(fileExt) < 0) {
            e.preventDefault();
            bootbox.alert("Invalid file selected. Only .csv file is allowed.");
            $(this).val('');
            $(this).fileinput('clear');
            $('form[data-toggle="validator"]').bootstrapValidator('updateStatus', 'csv_file', 'NOT_VALIDATED');
            return false;
        } else
            return true;
    }
});

$(document).ready(function () {
    $("#suggest_product").autocomplete({
        source: site.base_url + 'reports/suggestions',
        select: function (event, ui) {
            $('#report_product_id').val(ui.item.id);
        },
        minLength: 1,
        autoFocus: false,
        delay: 250,
        response: function (event, ui) {
            if (ui.content.length == 1 && ui.content[0].id != 0) {
                ui.item = ui.content[0];
                $(this).val(ui.item.label);
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
                $(this).removeClass('ui-autocomplete-loading');
            }
        },
    });
    $(document).on('blur', '#suggest_product', function (e) {
        if (!$(this).val()) {
            $('#report_product_id').val('');
        }
    });
    $('#random_num').click(function () {
        $(this).parent('.input-group').children('input').val(generateCardNo(8));
    });
    $('#toogle-customer-read-attr').click(function () {
        var icus = $(this).closest('.input-group').find("input[name='customer']");
        var nst = icus.is('[readonly]') ? false : true;
        icus.select2("readonly", nst);
        return false;
    });
    $('.top-menu-scroll').perfectScrollbar();
    $('#fixed').click(function (e) {
        e.preventDefault();
        if ($('#sidebar-left').hasClass('minified')) {
            bootbox.alert('Unable to fix minified sidebar');
        } else {
            if ($(this).hasClass('fixed')) {
                $.cookie('sma_theme_fixed', 'no', {
                    path: '/'
                });
            } else {
                $.cookie('sma_theme_fixed', 'yes', {
                    path: '/'
                });
            }
            cssStyle();
        }
    });
});

function widthFunctions(e) {
    var l = $("#sidebar-left").outerHeight(true),
        c = $("#content").height(),
        co = $("#content").outerHeight(),
        h = $("header").height(),
        f = $("footer").height(),
        wh = $(window).height(),
        ww = $(window).width();
    if (ww < 992) {
        $("#main-menu-act").removeClass("minified").addClass("full").find("i").removeClass("fa-angle-double-right").addClass("fa-angle-double-left");
        $("body").removeClass("sidebar-minified");
        $("#content").removeClass("sidebar-minified");
        $("#sidebar-left").removeClass("minified")
        if ($.cookie('sma_theme_fixed') == 'yes') {
            $.cookie('sma_theme_fixed', 'no', {
                path: '/'
            });
            $('#content, #sidebar-left, #header').removeAttr("style");
            $("#sidebar-left").css("overflow-y", "visible");
            $('#fixedText').text('Fixed');
            $('#main-menu-act').addClass('full visible-md visible-lg').show();
            $('#fixed').removeClass('fixed');
            $('#sidebar-left').perfectScrollbar('destroy');
        }
    }
    if (ww < 998 && ww > 750) {
        $('#main-menu-act').hide();
        $("body").addClass("sidebar-minified");
        $("#content").addClass("sidebar-minified");
        $("#sidebar-left").addClass("minified");
        $(".dropmenu > .chevron").removeClass("opened").addClass("closed");
        $(".dropmenu").parent().find("ul").hide();
        $("#sidebar-left > div > ul > li > a > .chevron").removeClass("closed").addClass("opened");
        $("#sidebar-left > div > ul > li > a").addClass("open");
        $('#fixed').hide();
    }
    if (ww > 1024 && $.cookie('sma_sidebar') != 'minified') {
        $('#main-menu-act').removeClass("minified").addClass("full").find("i").removeClass("fa-angle-double-right").addClass("fa-angle-double-left");
        $("body").removeClass("sidebar-minified");
        $("#content").removeClass("sidebar-minified");
        $("#sidebar-left").removeClass("minified");
        $("#sidebar-left > div > ul > li > a > .chevron").removeClass("opened").addClass("closed");
        $("#sidebar-left > div > ul > li > a").removeClass("open");
        $('#fixed').show();
    }
    if ($.cookie('sma_theme_fixed') == 'yes') {
        $('#content').addClass('content-with-fixed');
        $('#sidebar-left').addClass('sidebar-fixed').css('height', $(window).height() - 80);
    }
    if (ww > 767) {
        wh - 80 > l && $("#sidebar-left").css("min-height", wh - h - f - 30);
        wh - 80 > c && $("#content").css("min-height", wh - h - f - 30);
    } else {
        $("#sidebar-left").css("min-height", "0px");
        $(".content-con").css("max-width", ww);
    }
    //$(window).scrollTop($(window).scrollTop() + 1);
}

jQuery(document).ready(function (e) {
    window.location.hash ? e('#myTab a[href="' + window.location.hash + '"]').tab('show') : e("#myTab a:first").tab("show");
    e("#myTab2 a:first, #dbTab a:first").tab("show");
    e("#myTab a, #myTab2 a, #dbTab a").click(function (t) {
        t.preventDefault();
        e(this).tab("show");
    });
    e('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();
    e("#toggle-fullscreen").button().click(function () {
        var t = e(this),
            n = document.documentElement;
        if (!t.hasClass("active")) {
            e("#thumbnails").addClass("modal-fullscreen");
            n.webkitRequestFullScreen ? n.webkitRequestFullScreen(window.Element.ALLOW_KEYBOARD_INPUT) : n.mozRequestFullScreen && n.mozRequestFullScreen()
        } else {
            e("#thumbnails").removeClass("modal-fullscreen");
            (document.webkitCancelFullScreen || document.mozCancelFullScreen || e.noop).apply(document)
        }
    });
    e(".btn-close").click(function (t) {
        t.preventDefault();
        e(this).parent().parent().parent().fadeOut()
    });
    e(".btn-minimize").click(function (t) {
        t.preventDefault();
        var n = e(this).parent().parent().next(".box-content");
        n.is(":visible") ? e("i", e(this)).removeClass("fa-chevron-up").addClass("fa-chevron-down") : e("i", e(this)).removeClass("fa-chevron-down").addClass("fa-chevron-up");
        n.slideToggle("slow", function () {
            widthFunctions();
        })
    });
});

jQuery(document).ready(function (e) {
    e("#main-menu-act").click(function () {
        if (e(this).hasClass("full")) {
            $.cookie('sma_sidebar', 'minified', {
                path: '/'
            });
            e(this).removeClass("full").addClass("minified").find("i").removeClass("fa-angle-double-left").addClass("fa-angle-double-right");
            e("body").addClass("sidebar-minified");
            e("#content").addClass("sidebar-minified");
            e("#sidebar-left").addClass("minified");
            e(".dropmenu > .chevron").removeClass("opened").addClass("closed");
            e(".dropmenu").parent().find("ul").hide();
            e("#sidebar-left > div > ul > li > a > .chevron").removeClass("closed").addClass("opened");
            e("#sidebar-left > div > ul > li > a").addClass("open");
            $('#fixed').hide();
        } else {
            $.cookie('sma_sidebar', 'full', {
                path: '/'
            });
            e(this).removeClass("minified").addClass("full").find("i").removeClass("fa-angle-double-right").addClass("fa-angle-double-left");
            e("body").removeClass("sidebar-minified");
            e("#content").removeClass("sidebar-minified");
            e("#sidebar-left").removeClass("minified");
            e("#sidebar-left > div > ul > li > a > .chevron").removeClass("opened").addClass("closed");
            e("#sidebar-left > div > ul > li > a").removeClass("open");
            $('#fixed').show();
        }
        return false;
    });
    e(".dropmenu").click(function (t) {
        t.preventDefault();
        if (e("#sidebar-left").hasClass("minified")) {
            if (!e(this).hasClass("open")) {
                e(this).parent().find("ul").first().slideToggle();
                e(this).find(".chevron").hasClass("closed") ? e(this).find(".chevron").removeClass("closed").addClass("opened") : e(this).find(".chevron").removeClass("opened").addClass("closed")
            }
        } else {
            e(this).parent().find("ul").first().slideToggle();
            e(this).find(".chevron").hasClass("closed") ? e(this).find(".chevron").removeClass("closed").addClass("opened") : e(this).find(".chevron").removeClass("opened").addClass("closed")
        }
    });
    if (e("#sidebar-left").hasClass("minified")) {
        e("#sidebar-left > div > ul > li > a > .chevron").removeClass("closed").addClass("opened");
        e("#sidebar-left > div > ul > li > a").addClass("open");
        e("body").addClass("sidebar-minified")
    }
});

$(document).ready(function () {
    cssStyle();
    $('select, .select').select2({
        minimumResultsForSearch: 7
    });
    $('#customer, #rcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
    $('#supplier, #rsupplier, .rsupplier').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });

    $('#recipe_product, #rrecipe_product, .rrecipe_product').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "recipe/product_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });

    $('#recipe_addon, #rrecipe_addon, .rrecipe_addon').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "recipe/addon_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });

    $('#raw_product, #rraw_product, .rraw_product').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "products/raw_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });


    $('.recipe_category_item').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "system_settings/recipe_category_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });

    $('.recipe_item').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "system_settings/recipe_item_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });

    $('.input-tip').tooltip({
        placement: 'top',
        html: true,
        trigger: 'hover focus',
        container: 'body',
        title: function () {
            return $(this).attr('data-tip');
        }
    });
    $('.input-pop').popover({
        placement: 'top',
        html: true,
        trigger: 'hover',
        container: 'body',
        content: function () {
            return $(this).attr('data-tip');
        },
        title: function () {
            return '<b>' + $('label[for="' + $(this).attr('id') + '"]').text() + '</b>';
        }
    });
});

$(document).on('click', '*[data-toggle="lightbox"]', function (event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});
$(document).on('click', '*[data-toggle="popover"]', function (event) {
    event.preventDefault();
    $(this).popover();
});

$(document).ajaxStart(function () {
    $('#ajaxCall').show();
}).ajaxStop(function () {
    $('#ajaxCall').hide();
});

$(document).ready(function () {
    $('input[type="checkbox"],[type="radio"]').not('.skip').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    $('textarea').not('.skip,#options').redactor({
        buttons: ['formatting', '|', 'alignleft', 'aligncenter', 'alignright', 'justify', '|', 'bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', '|', /*'image', 'video',*/ 'link', '|', 'html'],
        formattingTags: ['p', 'pre', 'h3', 'h4'],
        minHeight: 100,
        changeCallback: function (e) {
            var editor = this.$editor.next('textarea');
            if ($(editor).attr('required')) {
                $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', $(editor).attr('name'));
            }
        }
    });
    $(document).on('click', '.file-caption', function () {
        $(this).next('.input-group-btn').children('.btn-file').children('input.file').trigger('click');
    });
});

function suppliers(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "suppliers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}

function recipe_products(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "recipe/product_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}

function recipe_addon(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "recipe/addon_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}

function raw_products(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "products/raw_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}


function recipe_category_item(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "system_settings/recipe_category_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}



function recipe_item(ele) {
    $(ele).select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "system_settings/recipe_item_suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {
                        results: data.results
                    };
                } else {
                    return {
                        results: [{
                            id: '',
                            text: 'No Match Found'
                        }]
                    };
                }
            }
        }
    });
}




$(function () {
    $('.datetime').datetimepicker({
        format: site.dateFormats.js_ldate,
        fontAwesome: true,
        language: 'sma',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0
    });
    $('.date').datetimepicker({
        format: 'dd/mm/yyyy',
        fontAwesome: true,
        language: 'sma',
        todayBtn: 1,
        autoclose: 1,
        minView: 2
    });



    $('.time,.timepicker').datetimepicker({
        format: "HH:00",
        fontAwesome: true,
        useCurrent: false,
        language: 'sma',
        autoclose: 1,
        showMeridian: true,
        startView: 1,
        maxView: 1,
        minView: 1
    });

    $('.timewaiting').datetimepicker({
        format: "HH:mm:00",
        pickTime: false,
        fontAwesome: true,
        collapse: false,
        sideBySide: true,
        useCurrent: false,
        startView: 1,
        minView: 2,
        forceParse: 0,
        showClose: true,
        language: 'sma',
        autoclose: 1,
        showMeridian: false,
    });



    $(document).on('focus', '.date', function (t) {
        $(this).datetimepicker({
            format: site.dateFormats.js_sdate,
            fontAwesome: true,
            todayBtn: 1,
            autoclose: 1,
            minView: 2
        });
    });
    $(document).on('focus', '.totay_date', function (t) {
        $(this).datetimepicker({
            format: site.dateFormats.js_sdate,
            fontAwesome: true,
            todayBtn: 1,
            autoclose: 1,
            minView: 2,
            startDate: new Date()
        });
    });
    $(document).on('focus', '.datetime', function () {
        $(this).datetimepicker({
            format: site.dateFormats.js_ldate,
            fontAwesome: true,
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0
        });
    });
    $(document).on('focus', '.time', function () {
        $(this).datetimepicker({
            format: "HH:00",
            fontAwesome: true,
            useCurrent: false,
            autoclose: 1,
            forceParse: 0,
            showMeridian: true,
            startView: 1,
            maxView: 1,
            minView: 1
        });
    });
    $(document).on('focus', '.timepicker', function () {
        $(this).datetimepicker({
            format: "HH:00",
            fontAwesome: true,
            useCurrent: false,
            language: 'sma',
            autoclose: 1,
            showMeridian: true,
            startView: 1,
            maxView: 1,
            minView: 1
        });
    });
    /*$(document).on('focus','.timewaiting', function() {
        $(this).datetimepicker({format: "00:mm:00", pickTime: false,  fontAwesome: true,collapse:false,
sideBySide:true,
useCurrent:false,
showClose:true, language: 'sma',  autoclose: 1, showMeridian: false, startView: 1});
    });*/
});

$(document).ready(function () {
    $('#dbTab a').on('shown.bs.tab', function (e) {
        var newt = $(e.target).attr('href');
        var oldt = $(e.relatedTarget).attr('href');
        $(oldt).hide();
        //$(newt).hide().fadeIn('slow');
        $(newt).hide().slideDown('slow');
    });
    $('.dropdown').on('show.bs.dropdown', function (e) {
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown('fast');
    });
    $('.dropdown').on('hide.bs.dropdown', function (e) {
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp('fast');
    });
    $('.hideComment').click(function () {
        $.ajax({
            url: site.base_url + 'welcome/hideNotification/' + $(this).attr('id')
        });
    });
    $('.tip').tooltip();
    $('body').on('click', '#delete', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form').submit();
    });
    $('body').on('click', '#sync_quantity', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
    $('body').on('click', '#excel', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
    $('body').on('click', '#pdf', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
    $('body').on('click', '#labelProducts', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
    $('body').on('click', '#barcodeProducts', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
    $('body').on('click', '#combine', function (e) {
        e.preventDefault();
        $('#form_action').val($(this).attr('data-action'));
        $('#action-form-submit').trigger('click');
    });
});






// form validators



$(document).ready(function () {
    $('#product-search').click(function () {
        $('#product-search-form').submit();
    });
    //feedbackIcons:{valid: 'fa fa-check',invalid: 'fa fa-times',validating: 'fa fa-refresh'},
	
    $('form[class="add_from"]').bootstrapValidator({
		 excluded: ':disabled',
        fields: {
			vendor_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Vendor'
                    },
					stringLength: {
                        min: 1,
                        max: 10,
                        message: 'The Vendor Code must be 3 characters country code,1 (for vendor),6 random digits'
                    },
				}
			},
			
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital not Numbers'
                    }
                }
            },
            last_name: {
                 validators: {
                    notEmpty: {
                        message: 'Please Enter the Last Name'
                    },
                   /* stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },*/
                    /*regexp: {
                        regexp: /^[a-z][a-z0-9_-]{0,19}$/,
                       
                    }*/
                }
            },

            first_name: {
                 validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital not numbers'
                    }
                }
            },


            model: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Model'
                    },

                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'The model can only consist of alphabetical, number and underscore'
                    }
                }
            },
            number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the number'
                    },
                   
                    regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The only consist of number'
                    }
                }
            },
            engine_number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Engine number'
                    },

                    regexp: {
                         regexp: /^[0-9 ]+$/,
						message: 'The Engine number can only consist of number'
                    }
                }
            },
            chassis_number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the chassis Number'
                    },

                    regexp: {
                        regexp: /^[0-9 ]+$/,
						message: 'The chassis Number can only consist of number'
                    }
                }
            },
            make: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Make'
                    },

                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                    }
                }
            },
            color: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the color'
                    }
                }
            },
            manufacture_year: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Manufacture Year'
                    },
					
                    regexp: {
                        regexp: /^[0-9 ]+$/,
						message: 'The Manufacture Year can only consist of number'
                    }
                }
            },
            capacity: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the capacity'
                    }
                }
            },
            fuel_type: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the fuel type'
                    }
                }
            },
            photo: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Photo'
                    }
                }
            },

            permanent_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the permanent Address Doc'
                    }
                }
            },

            local_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the local Address Doc'
                    }
                }
            },

            pancard_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the Pancard Doc'
                    }
                }
            },

            reg_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Register Image'
                    }
                }
            },

            loan_doc: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the loan_doc'
                    }
                }
            },

            taxi_photo: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the taxi_Image'
                    }
                }
            },
            
            license_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Upload the license_Doc'
                    }
                }
            },

            police_image:{
                validators: {
                    notEmpty: {
                        message: 'Please Upload the  Police_Doc'
                    }
                }
            },


            reg_owner_name: {
                validators: {
                    
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital'
                    }
                }
            },

            license_ward_name: {
                validators: {
					 regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'invalid license ward name'
                    }
			
                }
            },

            license_type: {
                validators: {
                    regexp: {
						 regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'Please Enter the license class'
                    }
                }
            },

//            license_issuing_authority: {
//                validators: {
//                    notEmpty: {
//
//                        message: 'Please Enter the license_issuing_authority'
//                    }
//                }
//            },

            reg_owner_address: {
                validators: {
                    regexp: {
					 	regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter the Valid Register Owner Address'
                    }
					
                }
            },
            taxation_amount_paid: {
                validators: {
                    regexp: {
						regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'Please Enter the Taxation Amount Paid'
                    }
                }
            },
            taxation_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Taxation Img'
                    }
                }
            },

            insurance_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Insurance Image'
                    }

                }
            },
            insurance_policy_no: {
                validators: {
                    regexp: {
						regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'Please Enter the Insurance Policy No'
                    }

                }
            },
            permit_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Permit Image'
                    }

                }
            },
            permit_no: {
                validators: {
                    
                    regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
            authorisation_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Authorisation Image'
                    }

                }
            },
            authorisation_no: {
                validators: {
                    
                    regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The Authorisation number can only consist of number'
                    }
                }
            },
            fitness_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Fitness Image'
                    }

                }
            },
            speed_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Speed Image'
                    }

                }
            },
            puc_image: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Puc Image'
                    }

                }
            },
            aadhaar_image: {
                validators: {
                    notEmpty: {
                        message: 'Please upload the aadhaar Image'
                    }

                }
            },

            symbol: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Symbol'
                    }

                }
            },
            iso_code: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your ISO code'
                    }

                }
            },
            numeric_iso_code: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Numeric ISO code'
                    }

                }
            },

            form_action: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Numeric ISO code'
                    }

                }
            },

            password: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your password'
                    },
					 stringLength: {
                        min: 8,
                        max: 255,
                        message: 'Minimum 8 characters long '
                    },

                }
            },

            confirm_password: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your confirm_password'
                    }

                }
            },

            email: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter The Email'
                    },
					emailAddress: {
                        message: 'Please Enter a valid email address'
                    }

                }
            },
            
           
            photo: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Photo'
                    }
                }
            },

            mobile:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter Mobile Number'
                    },
					regexp: {
                        regexp: /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)([0|\+[0-9]{1,5})?([1-9][0-9]{9})$/,
                        message: 'The Mobile number can only consist of number'
                    }
                }
            },
			gender:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the Gender'
                    }
                }
			},

            local_address:{
                validators: {
					notEmpty: {
                        message: 'Please enter address'
                    },
                    regexp: {
						regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter The local_address'
                    },
					/* stringLength: {
                        min: 3,
                        max: 255,
                        message: 'Please enter at least 10 characters and no more than 200'
                    }*/
                }
            },

//            permanent_address:{
//                validators: {
//					 notEmpty: {
//                        message: 'Please Enter The permanent_address'
//                    },
//                    
//					 stringLength: {
//                        min: 2,
//                        max: 255,
//                        message: 'Please enter at least 10 characters and no more than 200'
//                    }
//                }
//            },

            account_no:{
                validators: {
                   
					 stringLength: {
                        min: 0,
                        max: 15,
                        message: 'invalid account_number'
                    },
					 notEmpty: {
                        message: 'Please Enter The account number'
                    },
					regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The account number can only consist of number'
                    }
                }
            },

            bank_name:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The Bank Name'
                    },
					regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'The Bank Name can only consist of Alphabets'
                    }
                }
            },

            branch_name:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The branch_name'
                    },
					regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'The branch_name can only consist of Alphabets'
                    }
                }
            },          
			ifsc_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The ifsc_code'
                    },
					 
                    regexp: {
                        regexp: /^[A-Za-z]{4}[0-9]{6,7}$/,
						message: 'Please Enter The Valid ifsc code'
                    }
                }
            },
            

           
            base_min_distance:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The base_min_distance'
                    }
                }
            },
            base_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            base_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_price_value'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
            base_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
            night_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_price_value'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_min_distance: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_min_distance'
                    },

                    regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
            peak_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_price_value'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_min_distance: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_min_distance'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
         
			type: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Type'
                    }

                }
            },
			 authorisation_due_date: {
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
            },

             taxation_due_date:{
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             insurance_due_date:{
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             

             puc_due_date:{
               validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             speed_due_date:{
                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             fitness_due_date:{
                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },
             permit_due_date:{
                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },
			reg_date:{
				 validators: {
					
                    date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			reg_due_date:{
				 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			reg_due_date:{
				validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			
			//Rendal fare sec
			
			taxi_type:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Taxi Type'
                    }
				}
			},
			continent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Continents'
                    }
				}
			},
			permanent_continent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Continent id'
                    }
				}	
			},
			country_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Country'
                    }
				}
			},
			
			zone_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Zonal'
                    }
				}
			},
			state_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	State'
                    }
				}
			},
			city_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	City'
                    }
				}
			},
			package_name:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Package Name'
                    },
					regexp: {
					 	regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter the Valid Package Name consist of alphabets'
                    }
				}
			},
			package_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Package price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Package price can only consist of number'
                    }
                }
			},
			per_distance:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Distance'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Distance can only consist of number'
                    }
                }
			},
			per_distance_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Distance Price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Distance Price can only consist of number'
                    }
				}
                },
				
			per_time_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Time Price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Time Price can only consist of number'
                    }
				}	
				},
			driver_allowance_per_day:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Driver Allowance/day'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Driver Allowance/day can only consist of number'
                    }
				}	
			},
			driver_night_per_day:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Driver night/day'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Driver night/day can only consist of number'
                    }
				}	
			},
				
			country_code:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Country code'
                    }
				}
			},
			department_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Department'
                    }
				}
			},
			designation_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Designation'
                    }
				}
			},
			operator:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Operator'
                    }
				}
			},

				telephone_number:{
				 validators: {
                    notEmpty: {
                        message: 'Please Enter Telephone Number'
                    },
					regexp: {
                        regexp: /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)([0|\+[0-9]{1,5})?([1-9][0-9]{9})$/,
                        message: 'The Mobile number can only consist of number'
                    },
                }
					
			},
			 legal_entity:{
			 validators: {
                    notEmpty: {
                        message: 'Please Enter the legal entity'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'The legal entity can only consist of number & AlphaPets'
                    }
                }
		 },

			gst:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the GST'
                    },

                    regexp: {
                        regexp: /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/,
                        message: 'Please Enter Valid GSTIN Number'
                    }
                }
			},
			dob:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the DOB'
                    },

                }
			},
//			loan_information:{
//				validators: {
//				 notEmpty: {
//                        message: 'Please Enter the Loan Information'
//                    },
//
//				regexp: {
//                       regexp: /^[a-zA-Z ]+$/,
//                        message: 'Please Enter the Valid Loan Information'
//                    }
//				}
//			},
			/*local_continent_id:{
				validators: {
				regexp: {
                        message: 'Please Select the continents'
                    }
				}
			},*/
			permanent_country_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Country '
                    }

                }
			},
			parent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the Vendor '
                    },

                }
			},
			license_dob:{
				validators: {
				regexp: {
                        message: 'Please Select the License DOB'
                    }
				}
			},
			local_country_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the Country '
                    },

                }
			},
			/*local_continent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the continent '
                    },

                }
			},*/

        	},
        submitButtons: 'input[type="submit"]'
    });
	



     
$('form[class="edit_from"]').bootstrapValidator({
        excluded: ':disabled',
        fields: {
			vendor_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Vendor'
                    },
					stringLength: {
                        min: 1,
                        max: 10,
                        message: 'The Vendor Code must be 3 characters country code,1 (for vendor),6 random digits'
                    },
				}
			},
			
            name: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital not Numbers'
                    }
                }
            },
            last_name: {
                 validators: {
                    notEmpty: {
                        message: 'Please Enter the Last Name'
                    },
                    /*stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },*/
                    /*regexp: {
                        regexp: /^[a-z][a-z0-9_-]{0,19}$/,
                        
                    }*/
                }
            },

            first_name: {
                 validators: {
                    notEmpty: {
                        message: 'Please Enter the Name'
                    },
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital not numbers'
                    }
                }
            },


            model: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Model'
                    },

                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'The model can only consist of alphabetical, number and underscore'
                    }
                }
            },
            number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the number'
                    },
                   
                    regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The only consist of number'
                    }
                }
            },
            engine_number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Engine number'
                    },

                    regexp: {
                         regexp: /^[0-9 ]+$/,
						message: 'The Engine number can only consist of number'
                    }
                }
            },
            chassis_number: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the chassis Number'
                    },

                    regexp: {
                        regexp: /^[0-9 ]+$/,
						message: 'The chassis Number can only consist of number'
                    }
                }
            },
            make: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Make'
                    },

                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                    }
                }
            },
            color: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the color'
                    }
                }
            },
            manufacture_year: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Manufacture Year'
                    },
					
                    regexp: {
                        regexp: /^[0-9 ]+$/,
						message: 'The Manufacture Year can only consist of number'
                    }
                }
            },
            capacity: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the capacity'
                    }
                }
            },
            fuel_type: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the fuel type'
                    }
                }
            },
            reg_owner_name: {
                validators: {
                    
                    stringLength: {
                        min: 1,
                        max: 90,
                        message: 'Each min 2 and max 90 characters'
                    },
                    regexp: {
                        regexp: /^[A-Z][a-z0-9_-]{0,19}$/,
                        message: 'First character of each name should be capital'
                    }
                }
            },

            license_ward_name: {
                validators: {
					 regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'invalid license ward name'
                    }
			
                }
            },

            license_type: {
                validators: {
                    regexp: {
						 regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'Please Enter the license class'
                    }
                }
            },

            license_issuing_authority: {
                validators: {
                    notEmpty: {

                        message: 'Please Enter the license_issuing_authority'
                    }
                }
            },

            reg_owner_address: {
                validators: {
					notEmpty: {
                        message: 'Please enter address'
                    },
                    regexp: {
					 	regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter the Valid Register Owner Address'
                    }
					
                }
            },
            taxation_amount_paid: {
                validators: {
                    regexp: {
						regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'Please Enter the Taxation Amount Paid'
                    }
                }
            },
          
            insurance_policy_no: {
                validators: {
                    regexp: {
						regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'Please Enter the Insurance Policy No'
                    }

                }
            },
          
            permit_no: {
                validators: {
                    
                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
          
            authorisation_no: {
                validators: {
                    
                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Authorisation number can only consist of number'
                    }
                }
            },
            

            symbol: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Symbol'
                    }

                }
            },
            iso_code: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your ISO code'
                    }

                }
            },
            numeric_iso_code: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Numeric ISO code'
                    }

                }
            },

            form_action: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your Numeric ISO code'
                    }

                }
            },

            password: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your password'
                    },
					 stringLength: {
                        min: 8,
                        max: 255,
                        message: 'Minimum 8 characters long '
                    },

                }
            },

            confirm_password: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter Your confirm_password'
                    }

                }
            },

            email: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter The Email'
                    },
					emailAddress: {
                        message: 'Please Enter a valid email address'
                    }

                }
            },
            
           
            mobile:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter Mobile Number'
                    },
					
					regexp: {
                        regexp: /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)([0|\+[0-9]{1,5})?([1-9][0-9]{9})$/,
                        message: 'The Mobile number can only consist of number'
                    }

                }
            },
			gender:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the Gender'
                    }
                }
			},

            local_address:{
                validators: {
					notEmpty: {
                        message: 'Please enter address'
                    },
                    regexp: {
						regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter The local_address'
                    },
					 /*stringLength: {
                        min: 3,
                        max: 255,
                        message: 'Please enter at least 10 characters and no more than 200'
                    }*/
                }
            },

            permanent_address:{
                validators: {
                    regexp: {
                        message: 'Please Enter The address'
                    },
					 /*stringLength: {
                        min: 2,
                        max: 255,
                        message: 'Please enter at least 10 characters and no more than 200'
                    }*/
                }
            },

            account_no:{
                validators: {
                   
					 stringLength: {
                        min: 0,
                        max: 15,
                        message: 'invalid account_number'
                    },
					 notEmpty: {
                        message: 'Please Enter The account number'
                    },
					regexp: {
                        regexp: /^[0-9 ]+$/,
                        message: 'The account number can only consist of number'
                    }
                }
            },

            bank_name:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The Bank Name'
                    },
					regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'The Bank Name can only consist of Alphabets'
                    }
                }
            },

            branch_name:{

                validators: {
                    notEmpty: {
                        message: 'Please Enter The branch_name'
                    },
					regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                        message: 'The branch_name can only consist of Alphabets'
                    }
                }
            },          
			ifsc_code:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The ifsc_code'
                    },
					 
                    regexp: {
                        regexp: /^[A-Za-z]{4}[0-9]{6,7}$/,
						message: 'Please Enter The Valid ifsc code'
                    }
                }
            },
            pancard_no:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Pancard_number'
                    },
                    stringLength: {
                        min: 0,
                        max: 10,
                        message: 'invalid pan card number'
                    },
                    regexp: {
                        regexp:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/,
                        message: 'First 5 characters,Then 4 digits,Then 1 character'
                    }
                }
            },

            aadhaar_no:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter the Aadhaar_number'
                    },
                    stringLength: {
                        min: 0,
                        max: 12,
                        message: 'Please Enter Valid Aadhaar card number'
                    },
                    regexp: {
//                        regexp: /^[2-9]{1}[0-9]{11}$/,
						 regexp: /^(\d{12}|\d{16})$/,
                    }
                }
            },
            base_min_distance:{
                validators: {
                    notEmpty: {
                        message: 'Please Enter The base_min_distance'
                    }
                }
            },
            base_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            base_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_price_value'
                    },

                    regexp: {
                        regexp:/^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
            base_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the base_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
            night_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_price_value'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            night_min_distance: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the night_min_distance'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_waiting_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_waiting_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
            peak_price_value: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_price_value'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_min_distance_price: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_min_distance_price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },

            peak_min_distance: {
                validators: {
                    notEmpty: {
                        message: 'Please Enter the peak_min_distance'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Permit number can only consist of number'
                    }
                }
            },
         
         
			type: {
                validators: {
                    notEmpty: {
                        message: 'Please Select the Type'
                    }

                }
            },
			 authorisation_due_date: {
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
            },

             taxation_due_date:{
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             insurance_due_date:{
                validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             

             puc_due_date:{
               validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             speed_due_date:{
                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },

             fitness_due_date:{

                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },
             permit_due_date:{
                 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
             },
			reg_date:{
				 validators: {
					
                    date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			reg_due_date:{
				 validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			reg_due_date:{
				validators: {
					 date: {
                        format: 'YYYY-MM-DD',
						message: 'Please Select the Valid Date'
                    }
                }
			},
			
			//Rendal fare sec
			
			taxi_type:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Taxi Type'
                    }
				}
			},
			continent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Continents'
                    }
				}
			},
			permanent_continent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Continent id'
                    }
				}	
			},
			country_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Country'
                    }
				}
			},
			
			zone_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Zonal'
                    }
				}
			},
			state_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	State'
                    }
				}
			},
			city_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	City'
                    }
				}
			},
			package_name:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Package Name'
                    },
					regexp: {
					 	regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter the Valid Package Name consist of alphabets'
                    }
				}
			},
			package_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Package price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Package price can only consist of number'
                    }
                }
			},
			per_distance:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Distance'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Distance can only consist of number'
                    }
                }
			},
			per_distance_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Distance Price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Distance Price can only consist of number'
                    }
				}
                },
				
			per_time_price:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Time Price'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Time Price can only consist of number'
                    }
				}	
				},
			driver_allowance_per_day:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Driver Allowance/day'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Driver Allowance/day can only consist of number'
                    }
				}	
			},
			driver_night_per_day:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Driver night/day'
                    },

                    regexp: {
                        regexp: /^[1-9]\d*(\.\d+)?$/,
                        message: 'The Driver night/day can only consist of number'
                    }
				}	
			},
				
			country_code:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Country code'
                    }
				}
			},
			department_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Department'
                    }
				}
			},
			designation_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Designation'
                    }
				}
			},
			operator:{
				validators: {
                    notEmpty: {
                        message: 'Please Select	Operator'
                    }
				}
			},

				telephone_number:{
				 validators: {
                    notEmpty: {
                        message: 'Please Enter Telephone Number'
                    },
					
					regexp: {
                        regexp: /^(?:(?:\+|0{0,2})91(\s*[\-]\s*)?|[0]?)([0|\+[0-9]{1,5})?([1-9][0-9]{9})$/,
                        message: 'The Mobile number can only consist of number'
                    }
                }
					
			},
			 legal_entity:{
			 validators: {
                    notEmpty: {
                        message: 'Please Enter the legal entity'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: 'The legal entity can only consist of number & AlphaPets'
                    }
                }
		 },

			gst:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the GST'
                    },

                    regexp: {
                        regexp: /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/,
                        message: 'The GST can only consist of 14 Digit Number'
                    }
                }
			},
			dob:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the DOB'
                    },

                }
			},
			loan_information:{
				validators: {
				 notEmpty: {
                        message: 'Please Enter the Loan Information'
                    },

				regexp: {
                       regexp: /^[a-zA-Z ]+$/,
                        message: 'Please Enter the Valid Loan Information'
                    }
				}
			},
			/*local_continent_id:{
				validators: {
				regexp: {
                        message: 'Please Select the continents'
                    }
				}
			},*/
			permanent_country_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Enter the Country '
                    },

                }
			},
			parent_id:{
				validators: {
                    notEmpty: {
                        message: 'Please Select the Vendor '
                    },

                }
			},
			license_dob:{
				validators: {
				regexp: {
                        message: 'Please Select the License DOB'
                    }
				}
			},
			
			

        	},
        submitButtons: 'input[type="submit"]'
    });

    
    fields = $('.form-control');
    $.each(fields, function () {
        var id = $(this).attr('id');
        var iname = $(this).attr('name');
        var iid = '#' + id;
        if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
            $("label[for='" + id + "']").append(' *');
            $(document).on('change', iid, function () {
                $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
            });
        }
    });
    $('body').on('click', 'label', function (e) {
        var field_id = $(this).attr('for');
        if (field_id) {
            if ($("#" + field_id).hasClass('select')) {
                $("#" + field_id).select2("open");
                return false;
            }
        }
    });
    $('body').on('focus', 'select', function (e) {
        var field_id = $(this).attr('id');
        if (field_id) {
            if ($("#" + field_id).hasClass('select')) {
                $("#" + field_id).select2("open");
                return false;
            }
        }
    });
    $('#myModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-dialog').empty();
        //$(this).find('#myModalLabel').empty().html('&nbsp;');
        //$(this).find('.modal-body').empty().text('Loading...');
        //$(this).find('.modal-footer').empty().html('&nbsp;');
        $(this).removeData('bs.modal');
    });
    $('#myModal2').on('hidden.bs.modal', function () {
        $(this).find('.modal-dialog').empty();
        //$(this).find('#myModalLabel').empty().html('&nbsp;');
        //$(this).find('.modal-body').empty().text('Loading...');
        //$(this).find('.modal-footer').empty().html('&nbsp;');
        $(this).removeData('bs.modal');
        $('#myModal').css('zIndex', '1050');
        $('#myModal').css('overflow-y', 'scroll');
    });
    $('#myModal2').on('show.bs.modal', function () {
        $('#myModal').css('zIndex', '1040');
    });
    $('.modal').on('show.bs.modal', function () {
        $('#modal-loading').show();
        $('.blackbg').css('zIndex', '1041');
        $('.loader').css('zIndex', '1042');
    }).on('hide.bs.modal', function () {
        $('#modal-loading').hide();
        $('.blackbg').css('zIndex', '3');
        $('.loader').css('zIndex', '4');
    });
    $(document).on('click', '.po', function (e) {
        e.preventDefault();
        $('.po').popover({
            html: true,
            placement: 'left',
            trigger: 'manual'
        }).popover('show').not(this).popover('hide');
        return false;
    });
    $(document).on('click', '.po-close', function () {
        $('.po').popover('hide');
        return false;
    });
    $(document).on('click', '.po-delete', function (e) {
        var row = $(this).closest('tr');
        e.preventDefault();
        $('.po').popover('hide');
        var link = $(this).attr('href');
        var return_id = $(this).attr('data-return-id');
        $.ajax({
            type: "get",
            url: link,
            dataType: 'json',
            success: function (data) {
                if (data.error == 1) {
                    addAlert(data.msg, 'danger');
                } else {
                    addAlert(data.msg, 'success');
                    if (oTable != '') {
                        oTable.fnDraw();
                    }
                }
            },
            error: function (data) {
                addAlert('Ajax call failed', 'danger');
            }
        });
        return false;
    });
    $(document).on('click', '.po-delete1', function (e) {
        e.preventDefault();
        $('.po').popover('hide');
        var link = $(this).attr('href');
        var s = $(this).attr('id');
        var sp = s.split('__')
        $.ajax({
            type: "get",
            url: link,
            dataType: 'json',
            success: function (data) {
                if (data.error == 1) {
                    addAlert(data.msg, 'danger');
                } else {
                    addAlert(data.msg, 'success');
                    if (oTable != '') {
                        oTable.fnDraw();
                    }
                }
            },
            error: function (data) {
                addAlert('Ajax call failed', 'danger');
            }
        });
        return false;
    });
    $('body').on('click', '.bpo', function (e) {
        e.preventDefault();
        $(this).popover({
            html: true,
            trigger: 'manual'
        }).popover('toggle');
        return false;
    });
    $('body').on('click', '.bpo-close', function (e) {
        $('.bpo').popover('hide');
        return false;
    });
    $('#genNo').click(function () {
        var no = generateCardNo();
        $(this).parent().parent('.input-group').children('input').val(no);
        return false;
    });
    $('#inlineCalc').calculator({
        layout: ['_%+-CABS', '_7_8_9_/', '_4_5_6_*', '_1_2_3_-', '_0_._=_+'],
        showFormula: true
    });
    $('.calc').click(function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.sname', function (e) {
        var row = $(this).closest('tr');
        var itemid = row.find('.rid').val();
        $('#myModal').modal({
            remote: site.base_url + 'products/modal_view/' + itemid
        });
        $('#myModal').modal('show');
    });
});

function addAlert(message, type) {
    $('.alerts-con').empty().append(
        '<div class="alert alert-' + type + '">' +
        '<button type="button" class="close" data-dismiss="alert">' +
        '&times;</button>' + message + '</div>');
}

$(document).ready(function () {
    if ($.cookie('sma_sidebar') == 'minified') {
        $('#main-menu-act').removeClass("full").addClass("minified").find("i").removeClass("fa-angle-double-left").addClass("fa-angle-double-right");
        $("body").addClass("sidebar-minified");
        $("#content").addClass("sidebar-minified");
        $("#sidebar-left").addClass("minified");
        $(".dropmenu > .chevron").removeClass("opened").addClass("closed");
        $(".dropmenu").parent().find("ul").hide();
        $("#sidebar-left > div > ul > li > a > .chevron").removeClass("closed").addClass("opened");
        $("#sidebar-left > div > ul > li > a").addClass("open");
        $('#fixed').hide();
    } else {

        $('#main-menu-act').removeClass("minified").addClass("full").find("i").removeClass("fa-angle-double-right").addClass("fa-angle-double-left");
        $("body").removeClass("sidebar-minified");
        $("#content").removeClass("sidebar-minified");
        $("#sidebar-left").removeClass("minified");
        $("#sidebar-left > div > ul > li > a > .chevron").removeClass("opened").addClass("closed");
        $("#sidebar-left > div > ul > li > a").removeClass("open");
        $('#fixed').show();
    }
});

$(document).ready(function () {
    $('#daterange').daterangepicker({
            timePicker: true,
            format: (site.dateFormats.js_sdate).toUpperCase() + ' HH:mm',
            ranges: {
                'Today': [moment().hours(0).minutes(0).seconds(0), moment()],
                'Yesterday': [moment().subtract('days', 1).hours(0).minutes(0).seconds(0), moment().subtract('days', 1).hours(23).minutes(59).seconds(59)],
                'Last 7 Days': [moment().subtract('days', 6).hours(0).minutes(0).seconds(0), moment().hours(23).minutes(59).seconds(59)],
                'Last 30 Days': [moment().subtract('days', 29).hours(0).minutes(0).seconds(0), moment().hours(23).minutes(59).seconds(59)],
                'This Month': [moment().startOf('month').hours(0).minutes(0).seconds(0), moment().endOf('month').hours(23).minutes(59).seconds(59)],
                'Last Month': [moment().subtract('month', 1).startOf('month').hours(0).minutes(0).seconds(0), moment().subtract('month', 1).endOf('month').hours(23).minutes(59).seconds(59)]
            }
        },
        function (start, end) {
            refreshPage(start.format('YYYY-MM-DD HH:mm'), end.format('YYYY-MM-DD HH:mm'));
        });
});

function refreshPage(start, end) {
    window.location.replace(CURI + '/' + encodeURIComponent(start) + '/' + encodeURIComponent(end));
}

function retina() {
    retinaMode = window.devicePixelRatio > 1;
    return retinaMode
}

$(document).ready(function () {
    $('#cssLight').click(function (e) {
        e.preventDefault();
        $.cookie('sma_style', 'light', {
            path: '/'
        });
        cssStyle();
        return true;
    });
    $('#cssBlue').click(function (e) {
        e.preventDefault();
        $.cookie('sma_style', 'blue', {
            path: '/'
        });
        cssStyle();
        return true;
    });
    $('#cssBlack').click(function (e) {
        e.preventDefault();
        $.cookie('sma_style', 'black', {
            path: '/'
        });
        cssStyle();
        return true;
    });
    $('#cssdarkBlue').click(function (e) {
        e.preventDefault();
        $.cookie('sma_style', 'dark_blue', {
            path: '/'
        });
        cssStyle();
        return true;
    });

    $("#toTop").click(function (e) {
        e.preventDefault();
        $("html, body").animate({
            scrollTop: 0
        }, 100);
    });
    $(document).on('click', '.delimg', function (e) {
        e.preventDefault();
        var ele = $(this),
            id = $(this).attr('data-item-id');
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result == true) {
                $.get(site.base_url + 'products/delete_image/' + id, function (data) {
                    if (data.error === 0) {
                        addAlert(data.msg, 'success');
                        ele.parent('.gallery-image').remove();
                    }
                });
            }
        });
        return false;
    });
});
$(document).ready(function () {
    $(document).on('click', '.row_status', function (e) {
        e.preventDefault;
        var row = $(this).closest('tr');
        var id = row.attr('id');
        if (row.hasClass('invoice_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'sales/update_status/' + id
            });
            $('#myModal').modal('show');
        } else if (row.hasClass('purchase_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'purchases/update_status/' + id
            });
            $('#myModal').modal('show');
        } else if (row.hasClass('quote_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'quotes/update_status/' + id
            });
            $('#myModal').modal('show');
        } else if (row.hasClass('material_request_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'material_request/update_status/' + id
            });
            $('#myModal').modal('show');
        } else if (row.hasClass('transfer_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'transfers/update_status/' + id
            });
            $('#myModal').modal('show');
        } else if (row.hasClass('purchases_order_link')) {
            $('#myModal').modal({
                remote: site.base_url + 'purchases_order/update_status/' + id
            });
            $('#myModal').modal('show');
        }
        return false;
    });
});
/*
 $(window).scroll(function() {
    if ($(this).scrollTop()) {
        $('#toTop').fadeIn();
    } else {
        $('#toTop').fadeOut();
    }
 });
*/
$(document).on('ifChecked', '.checkth, .checkft', function (event) {
    $('.checkth, .checkft').iCheck('check');
    $('.multi-select').each(function () {
        $(this).iCheck('check');
    });
});
$(document).on('ifUnchecked', '.checkth, .checkft', function (event) {
    $('.checkth, .checkft').iCheck('uncheck');
    $('.multi-select').each(function () {
        $(this).iCheck('uncheck');
    });
});
$(document).on('ifUnchecked', '.multi-select', function (event) {
    $('.checkth, .checkft').attr('checked', false);
    $('.checkth, .checkft').iCheck('update');
});

function check_add_item_val() {
    $('#add_item').bind('keypress', function (e) {
        if (e.keyCode == 13 || e.keyCode == 9) {
            e.preventDefault();
            $(this).autocomplete("search");
        }
    });
}

function fld(oObj) {
    if (oObj != null) {
        var aDate = oObj.split('-');
        var bDate = aDate[2].split(' ');
        year = aDate[0], month = aDate[1], day = bDate[0], time = bDate[1];
        if (site.dateFormats.js_sdate == 'dd-mm-yyyy')
            return day + "-" + month + "-" + year + " " + time;
        else if (site.dateFormats.js_sdate === 'dd/mm/yyyy')
            return day + "/" + month + "/" + year + " " + time;
        else if (site.dateFormats.js_sdate == 'dd.mm.yyyy')
            return day + "." + month + "." + year + " " + time;
        else if (site.dateFormats.js_sdate == 'mm/dd/yyyy')
            return month + "/" + day + "/" + year + " " + time;
        else if (site.dateFormats.js_sdate == 'mm-dd-yyyy')
            return month + "-" + day + "-" + year + " " + time;
        else if (site.dateFormats.js_sdate == 'mm.dd.yyyy')
            return month + "." + day + "." + year + " " + time;
        else
            return oObj;
    } else {
        return '';
    }
}

function fsd(oObj) {
    if (oObj != null) {
        var aDate = oObj.split('-');
        if (site.dateFormats.js_sdate == 'dd-mm-yyyy')
            return aDate[2] + "-" + aDate[1] + "-" + aDate[0];
        else if (site.dateFormats.js_sdate === 'dd/mm/yyyy')
            return aDate[2] + "/" + aDate[1] + "/" + aDate[0];
        else if (site.dateFormats.js_sdate == 'dd.mm.yyyy')
            return aDate[2] + "." + aDate[1] + "." + aDate[0];
        else if (site.dateFormats.js_sdate == 'mm/dd/yyyy')
            return aDate[1] + "/" + aDate[2] + "/" + aDate[0];
        else if (site.dateFormats.js_sdate == 'mm-dd-yyyy')
            return aDate[1] + "-" + aDate[2] + "-" + aDate[0];
        else if (site.dateFormats.js_sdate == 'mm.dd.yyyy')
            return aDate[1] + "." + aDate[2] + "." + aDate[0];
        else
            return oObj;
    } else {
        return '';
    }
}

function generateCardNo(x) {
    if (!x) {
        x = 16;
    }
    chars = "1234567890";
    no = "";
    for (var i = 0; i < x; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        no += chars.substring(rnum, rnum + 1);
    }
    return no;
}

function roundNumber(num, nearest) {
    if (!nearest) {
        nearest = 0.05;
    }
    return Math.round((num / nearest) * nearest);
}

function getNumber(x) {
    return accounting.unformat(x);
}

function formatQuantity(x) {
    return (x != null) ? '<div class="text-center">' + formatNumber(x, site.settings.qty_decimals) + '</div>' : '';
}

function formatQuantity2(x) {
    return (x != null) ? formatQuantityNumber(x, site.settings.qty_decimals) : '';
}

function formatQuantityNumber(x, d) {
    if (!d) {
        d = site.settings.qty_decimals;
    }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}

function formatQty(x) {
    return (x != null) ? formatNumber(x, site.settings.qty_decimals) : '';
}

function formatNumber(x, d) {
    if (!d && d != 0) {
        d = site.settings.decimals;
    }
    if (site.settings.sac == 1) {
        return formatSA(parseFloat(x).toFixed(d));
    }
    return accounting.formatNumber(x, d, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep);
}

function formatMoney(x, symbol) {
    if (!symbol) {
        symbol = "";
    }
    if (site.settings.sac == 1) {
        return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
            '' + formatSA(parseFloat(x).toFixed(site.settings.decimals)) +
            (site.settings.display_symbol == 2 ? site.settings.symbol : '');
    }
    var fmoney = accounting.formatMoney(x, symbol, site.settings.decimals, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep, "%s%v");
    if (symbol) {
        return fmoney;
    }
    return (site.settings.display_symbol == 1 ? site.settings.symbol : '') +
        fmoney +
        (site.settings.display_symbol == 2 ? site.settings.symbol : '');
}

/*function is_valid_discount(mixed_var) {
    return (is_numeric(mixed_var) || (/([0-9]%)/i.test(mixed_var))) ? true : false;
}*/

function is_numeric(mixed_var) {
    var whitespace =
        " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
    return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
        1)) && mixed_var !== '' && !isNaN(mixed_var);
}

function is_float(mixed_var) {
    return +mixed_var === mixed_var && (!isFinite(mixed_var) || !!(mixed_var % 1));
}

function decimalFormat(x) {
    return '<div class="text-center">' + formatNumber(x != null ? x : 0) + '</div>';
}

function currencyFormat(x) {
    return '<div class="text-right">' + formatMoney(x != null ? x : 0) + '</div>';
}

function formatDecimal(x, d) {
    if (!d) {
        d = site.settings.decimals;
    }
    return parseFloat(accounting.formatNumber(x, d, '', '.'));
}

function formatDecimals(x, d) {
    if (!d) {
        d = site.settings.decimals;
    }
    return parseFloat(accounting.formatNumber(x, d, '', '.')).toFixed(d);
}

function pqFormat(x) {
    if (x != null) {
        var d = '',
            pqc = x.split("___");
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split("__");
            d += v[0] + ' (' + formatQuantity2(v[1]) + ')<br>';
        }
        return d;
    } else {
        return '';
    }
}

function checkbox(x) {
    return '<div class="text-center"><input class="checkbox multi-select" type="checkbox" name="val[]" value="' + x + '" /></div>';
}

function decode_html(value) {
    return $('<div/>').html(value).text();
}

function img_hl(x) {
    var image_link = (x == null || x == '') ? 'no_image.png' : x;
    return '<div class="text-center"><a href="' + site.url + 'assets/uploads/' + image_link + '" data-toggle="lightbox"><img src="' + site.url + 'assets/uploads/thumbs/' + image_link + '" alt="" style="width:30px; height:30px;" /></a></div>';
}

function attachment(x) {
    return x == null ? '' : '<div class="text-center"><a href="' + site.base_url + 'welcome/download/' + x + '" class="tip" title="' + lang.download + '"><i class="fa fa-file"></i></a></div>';
}

function attachment2(x) {
    return x == null ? '' : '<div class="text-center"><a href="' + site.base_url + 'welcome/download/' + x + '" class="tip" title="' + lang.download + '"><i class="fa fa-file-o"></i></a></div>';
}

function user_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="javascript:void(0)"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="javascript:void(0)"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function user_status_old(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'auth/deactivate/' + y[1] + '" data-toggle="modal" data-target="#myModal"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'auth/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}
/*function driver_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<a href="'+site.base_url+'drivers/driver_status/deactivate/'+ y[1] +'"><span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span></a>' :
    '<a href="'+site.base_url+'drivers/driver_status/activate/'+ y[1] +'"><span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span><a/>';
}*/

function driver_status(x) {
    var y = x.split("__");
    var d;
    if (y[0] == 1) {
        d = '<a href="' + site.base_url + 'drivers/driver_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i>Active</span></a>';
    } else if (y[0] == 2) {
        d = '<a href="' + site.base_url + 'drivers/approved_status/' + y[1] + '"><span class="label label-warning"><i class="fa fa-check"></i>Approved </span></a>';
    } else if (y[0] == 0) {
        d = '<a href="' + site.base_url + 'drivers/driver_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-check"></i>Inactive</span></a>';
    }
    return d;
}

function operator_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'operators/operator_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'operators/operator_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

/*function taxi_brand_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<a href="'+site.base_url+'taxi/brand_status/deactivate/'+ y[1] +'"><span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span></a>' :
    '<a href="'+site.base_url+'taxi/brand_status/activate/'+ y[1] +'"><span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span><a/>';
}*/
function taxi_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'taxi/taxi_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'taxi/taxi_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function taxi_category_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/taxi_category_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/taxi_category_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function taxi_fuel_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/taxi_fuel_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/taxi_fuel_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function taxi_type_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/taxi_type_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/taxi_type_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function common_img(x) {
    var image_link = (x == null || x == '') ? 'no_image.png' : x;
    return '<div class="text-center"><a href="' + site.url + 'assets/uploads/' + image_link + '" data-toggle="lightbox"><img src="' + site.url + 'assets/uploads/' + image_link + '" alt="" style="width:30px;" /></a></div>';
}


function continent_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/continent_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/continent_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function country_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/country_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/country_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function zone_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/zone_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/zone_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function state_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/state_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/state_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function city_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/city_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/city_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

function area_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
        '<a href="' + site.base_url + 'masters/area_status/deactivate/' + y[1] + '"><span class="label label-success"><i class="fa fa-check"></i> ' + lang['active'] + '</span></a>' :
        '<a href="' + site.base_url + 'masters/area_status/activate/' + y[1] + '"><span class="label label-danger"><i class="fa fa-times"></i> ' + lang['inactive'] + '</span><a/>';
}

/*function taxi_color_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<a href="'+site.base_url+'taxi/color_status/deactivate/'+ y[1] +'"><span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span></a>' :
    '<a href="'+site.base_url+'taxi/color_status/activate/'+ y[1] +'"><span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span><a/>';
}
function taxi_category_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<a href="'+site.base_url+'taxi/category_status/deactivate/'+ y[1] +'"><span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span></a>' :
    '<a href="'+site.base_url+'taxi/category_status/activate/'+ y[1] +'"><span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span><a/>';
}
*/
function row_status(x) {
    if (x == null) {
        return '';
    } else if (x == 'pending') {
        return '<div class="text-center"><span class="row_status label label-warning">' + lang[x] + '</span></div>';
    } else if (x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="row_status label label-success">' + lang[x] + '</span></div>';
    } else if (x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="row_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'due' || x == 'returned') {
        return '<div class="text-center"><span class="row_status label label-danger">' + lang[x] + '</span></div>';
    } else {
        return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
    }
}

function pay_status(x) {
    if (x == null) {
        return '';
    } else if (x == 'pending') {
        return '<div class="text-center"><span class="payment_status label label-warning">' + lang[x] + '</span></div>';
    } else if (x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="payment_status label label-success">' + lang[x] + '</span></div>';
    } else if (x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="payment_status label label-info">' + lang[x] + '</span></div>';
    } else if (x == 'due' || x == 'returned') {
        return '<div class="text-center"><span class="payment_status label label-danger">' + lang[x] + '</span></div>';
    } else {
        return '<div class="text-center"><span class="payment_status label label-default">' + x + '</span></div>';
    }
}

function formatSA(x) {
    x = x.toString();
    var afterPoint = '';
    if (x.indexOf('.') > 0)
        afterPoint = x.substring(x.indexOf('.'), x.length);
    x = Math.floor(x);
    x = x.toString();
    var lastThree = x.substring(x.length - 3);
    var otherNumbers = x.substring(0, x.length - 3);
    if (otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree + afterPoint;

    return res;
}

function unitToBaseQty(qty, unitObj) {
    switch (unitObj.operator) {
        case '*':
            return parseFloat(qty) * parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty) / parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty) + parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty) - parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}

function baseToUnitQty(qty, unitObj) {
    switch (unitObj.operator) {
        case '*':
            return parseFloat(qty) / parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty) * parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty) - parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty) + parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}

function set_page_focus() {
    if (site.settings.set_focus == 1) {
        $('#add_item').attr('tabindex', an);
        $('[tabindex=' + (an - 1) + ']').focus().select();
    } else {
        $('#add_item').attr('tabindex', 1);
        $('#add_item').focus();
    }
    $('.rquantity').bind('keypress', function (e) {
        if (e.keyCode == 13) {
            $('#add_item').focus();
        }
    });
}

function calculateTax(tax, amt, met) {
    if (tax && tax_rates) {
        tax_val = 0;
        tax_rate = '';
        $.each(tax_rates, function () {
            if (this.id == tax) {
                tax = this;
                return false;
            }
        });
        if (tax.type == 1) {
            if (met == '0') {
                tax_val = formatDecimal(((amt) * parseFloat(tax.rate)) / (100 + parseFloat(tax.rate)), 4);
                tax_rate = formatDecimal(tax.rate) + '%';
            } else {
                tax_val = formatDecimal(((amt) * parseFloat(tax.rate)) / 100, 4);
                tax_rate = formatDecimal(tax.rate) + '%';
            }
        } else if (tax.type == 2) {
            tax_val = parseFloat(tax.rate);
            tax_rate = formatDecimal(tax.rate);
        }
        return [tax_val, tax_rate];
    }
    return false;
}

function calculateDiscount(val, amt) {
    if (val.indexOf("%") !== -1) {
        var pds = val.split("%");
        return formatDecimal((parseFloat(((amt) * parseFloat(pds[0])) / 100)), 4);
    }
    return formatDecimal(val);
}

$(document).ready(function () {
    $('#view-customer').click(function () {
        $('#myModal').modal({
            remote: site.base_url + 'customers/view/' + $("input[name=customer]").val()
        });
        $('#myModal').modal('show');
    });
    $('#view-supplier').click(function () {
        $('#myModal').modal({
            remote: site.base_url + 'suppliers/view/' + $("input[name=supplier]").val()
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.customer_details_link td:not(:first-child, :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'customers/view/' + $(this).parent('.customer_details_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.supplier_details_link td:not(:first-child, :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'suppliers/view/' + $(this).parent('.supplier_details_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.product_link td:not(:first-child, :nth-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'products/modal_view/' + $(this).parent('.product_link').attr('id')
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'products/view/' + $(this).parent('.product_link').attr('id');
    });
    $('body').on('click', '.product_link2 td:first-child, .product_link2 td:nth-child(2)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'products/modal_view/' + $(this).closest('tr').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.purchase_link td:not(:first-child, :nth-child(5), :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases/modal_view/' + $(this).parent('.purchase_link').attr('id')
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'purchases/view/' + $(this).parent('.purchase_link').attr('id');
    });
    $('body').on('click', '.purchase_link2 td', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases/modal_view/' + $(this).closest('tr').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.transfer_link td:not(:first-child, :nth-last-child(3), :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'transfers/view/' + $(this).parent('.transfer_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.transfer_link2', function () {
        $('#myModal').modal({
            remote: site.base_url + 'transfers/view/' + $(this).attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.invoice_link td:not(:first-child, :nth-child(6), :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'sales/modal_view/' + $(this).parent('.invoice_link').attr('id')
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'sales/view/' + $(this).parent('.invoice_link').attr('id');
    });
    $('body').on('click', '.invoice_link2 td:not(:first-child, :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'sales/modal_view/' + $(this).closest('tr').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.receipt_link td:not(:first-child, :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'pos/view/' + $(this).parent('.receipt_link').attr('id') + '/1'
        });
    });
    $('body').on('click', '.return_link td', function () {
        // window.location.href = site.base_url + 'sales/view_return/' + $(this).parent('.return_link').attr('id');
        $('#myModal').modal({
            remote: site.base_url + 'sales/view_return/' + $(this).parent('.return_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.return_purchase_link td', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases/view_return/' + $(this).parent('.return_purchase_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.payment_link td', function () {
        $('#myModal').modal({
            remote: site.base_url + 'sales/payment_note/' + $(this).parent('.payment_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.payment_link2 td', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases/payment_note/' + $(this).parent('.payment_link2').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.expense_link2 td:not(:last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases/expense_note/' + $(this).closest('tr').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.quote_link td:not(:first-child, :nth-last-child(3), :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'quotes/modal_view/' + $(this).parent('.quote_link').attr('id')
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'quotes/view/' + $(this).parent('.quote_link').attr('id');
    });
    $('body').on('click', '.purchases_order_link td:not(:first-child, :nth-last-child(3), :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'purchases_order/modal_view/' + $(this).parent('.purchases_order_link').attr('id')
        });
        $('#myModal').modal('show');
        //window.location.href = site.base_url + 'purchases_order/view/' + $(this).parent('.purchases_order_link').attr('id');
    });

    $('body').on('click', '.quote_link2', function () {
        $('#myModal').modal({
            remote: site.base_url + 'quotes/modal_view/' + $(this).attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.delivery_link td:not(:first-child, :nth-last-child(2), :nth-last-child(3), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'sales/view_delivery/' + $(this).parent('.delivery_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.customer_link td:not(:first-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'customers/edit/' + $(this).parent('.customer_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.supplier_link td:not(:first-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'suppliers/edit/' + $(this).parent('.supplier_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.adjustment_link td:not(:first-child, :nth-last-child(2), :last-child)', function () {
        $('#myModal').modal({
            remote: site.base_url + 'products/view_adjustment/' + $(this).parent('.adjustment_link').attr('id')
        });
        $('#myModal').modal('show');
    });
    $('body').on('click', '.adjustment_link2', function () {
        $('#myModal').modal({
            remote: site.base_url + 'products/view_adjustment/' + $(this).attr('id')
        });
        $('#myModal').modal('show');
    });
    $('#clearLS').click(function (event) {
        bootbox.confirm(lang.r_u_sure, function (result) {
            if (result == true) {
                localStorage.clear();
                location.reload();
            }
        });
        return false;
    });
    $(document).on('click', '[data-toggle="ajax"]', function (e) {
        e.preventDefault();
        var href = $(this).attr('href');
        $.get(href, function (data) {
            $("#myModal").html(data).modal();
        });
    });
    $(".sortable_rows").sortable({
        items: "> tr",
        appendTo: "parent",
        helper: "clone",
        placeholder: "ui-sort-placeholder",
        axis: "x",
        update: function (event, ui) {
            var item_id = $(ui.item).attr('data-item-id');
            console.log(ui.item.index());
        }
    }).disableSelection();
});

function fixAddItemnTotals() {
    var ai = $("#sticker");
    var aiTop = (ai.position().top) + 250;
    var bt = $("#bottom-total");
    $(window).scroll(function () {
        var windowpos = $(window).scrollTop();
        if (windowpos >= aiTop) {
            ai.addClass("stick").css('width', ai.parent('form').width()).css('zIndex', 2);
            if ($.cookie('sma_theme_fixed') == 'yes') {
                ai.css('top', '40px');
            } else {
                ai.css('top', 0);
            }
            $('#add_item').removeClass('input-lg');
            $('.addIcon').removeClass('fa-2x');
        } else {
            ai.removeClass("stick").css('width', bt.parent('form').width()).css('zIndex', 2);
            if ($.cookie('sma_theme_fixed') == 'yes') {
                ai.css('top', 0);
            }
            $('#add_item').addClass('input-lg');
            $('.addIcon').addClass('fa-2x');
        }
        if (windowpos <= ($(document).height() - $(window).height() - 120)) {
            bt.css('position', 'fixed').css('bottom', 0).css('width', bt.parent('form').width()).css('zIndex', 2);
        } else {
            bt.css('position', 'static').css('width', ai.parent('form').width()).css('zIndex', 2);
        }
    });
}

function ItemnTotals() {
    fixAddItemnTotals();
    $(window).bind("resize", fixAddItemnTotals);
}

function getSlug(title, type) {
    var slug_url = site.base_url + 'welcome/slug';
    $.get(slug_url, {
        title: title,
        type: type
    }, function (slug) {
        $('#slug').val(slug).change();
    });
}

function openImg(img) {
    var imgwindow = window.open('', 'sma_pos_img');
    imgwindow.document.write('<html><head><title>Screenshot</title>');
    imgwindow.document.write('<link rel="stylesheet" href="' + site.assets + 'styles/helpers/bootstrap.min.css" type="text/css" />');
    imgwindow.document.write('</head><body style="display:flex;align-items:center;justify-content:center;">');
    imgwindow.document.write('<img src="' + img + '" class="img-thumbnail"/>');
    imgwindow.document.write('</body></html>');
    return true;
}

if (site.settings.auto_detect_barcode == 1) {
    $(document).ready(function () {
        var pressed = false;
        var chars = [];
        $(window).keypress(function (e) {
            chars.push(String.fromCharCode(e.which));
            if (pressed == false) {
                setTimeout(function () {
                    if (chars.length >= 8) {
                        var barcode = chars.join("");
                        $("#add_item").focus().autocomplete("search", barcode);
                    }
                    chars = [];
                    pressed = false;
                }, 200);
            }
            pressed = true;
        });
    });
}
$('.sortable_table tbody').sortable({
    containerSelector: 'tr'
});
$(window).bind("resize", widthFunctions);
$(window).load(widthFunctions);

$(document).ready(function () {
    $("#suggest_recipe").autocomplete({
        source: site.base_url + 'reports/recipesearch',
        select: function (event, ui) {
            $('#report_recipe_id').val(ui.item.id);
        },
        minLength: 1,
        autoFocus: false,
        delay: 250,
        response: function (event, ui) {

            if (ui.content.length == 1 && ui.content[0].id != 0) {
                ui.item = ui.content[0];
                $(this).val(ui.item.label);
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
                $(this).removeClass('ui-autocomplete-loading');
            }
        },
    });
    $(document).on('blur', '#suggest_recipe', function (e) {

        if (!$(this).val()) {
            $('#report_recipe_id').val('');
        }
    });
});
$(document).ready(function () {
    $('[data-target=#myModal]').attr('data-backdrop', "static");
    $('.img-popup').click(function (e) {
        e.preventDefault();
        if ($(this).hasClass('has-image')) {
            $src = $(this).attr('href');
            $html = '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">x</i></button>' +
                '<h4 class="modal-title" id="myModalLabel"></h4>' + '</div>' +
                '<div class="modal-body">' + '<img style="width:100%" src="' + $src + '">' + '</div>' +

                '</div>' +
                '</div>';
            $('#myModal').append($html); // here asign the image to the modal when the user click the enlarge link
            $('#myModal').modal('show');
        }
    });
    $('#confirm_password').blur(function () {
        $('.update_profile').attr('disabled', false);
    })
    $('.update_profile').click(function (e) { //e.preventDefault();
        if ($('#password').val() != '' && $('#confirm_password').val() == '') {
            $('#confirm_password').parents('.form-group').addClass('has-error')
            $('#confirm_password').parents('.form-group').find('.help-block').css('display', 'block');
            $('#confirm_password').parents('.form-group').find('.help-block').attr('data-bv-result', "INVALID");
            e.preventDefault();
        }
        return true;

    })
});

function render_img(x) {
    console.log(x)
    x = x.split("__");
    var image = (x[0] == null || x[0] == '') ? 'no_image.png' : x[0];

    var image_link = x[1];
    return '<div class="text-center"><a href="' + image_link + image + '" data-toggle="lightbox"><img src="' + image_link + image + '" alt="" style="width:30px; height:30px;" /></a></div>';
}



