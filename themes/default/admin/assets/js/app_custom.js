

/*!
 * accounting.js v0.4.1, copyright 2014 Open Exchange Rates, MIT license, http://openexchangerates.github.io/accounting.js
 */
(function (p, z) {
	function q(a) {
		return !!("" === a || a && a.charCodeAt && a.substr)
	}

	function m(a) {
		return u ? u(a) : "[object Array]" === v.call(a)
	}

	function r(a) {
		return "[object Object]" === v.call(a)
	}

	function s(a, b) {
		var d, a = a || {},
			b = b || {};
		for (d in b) b.hasOwnProperty(d) && null == a[d] && (a[d] = b[d]);
		return a
	}

	function j(a, b, d) {
		var c = [],
			e, h;
		if (!a) return c;
		if (w && a.map === w) return a.map(b, d);
		for (e = 0, h = a.length; e < h; e++) c[e] = b.call(d, a[e], e, a);
		return c
	}

	function n(a, b) {
		a = Math.round(Math.abs(a));
		return isNaN(a) ? b : a
	}

	function x(a) {
		var b = c.settings.currency.format;
		"function" === typeof a && (a = a());
		return q(a) && a.match("%v") ? {
			pos: a,
			neg: a.replace("-", "").replace("%v", "-%v"),
			zero: a
		} : !a || !a.pos || !a.pos.match("%v") ? !q(b) ? b : c.settings.currency.format = {
			pos: b,
			neg: b.replace("%v", "-%v"),
			zero: b
		} : a
	}
	var c = {
			version: "0.4.1",
			settings: {
				currency: {
					symbol: "$",
					format: "%s%v",
					decimal: ".",
					thousand: ",",
					precision: 2,
					grouping: 3
				},
				number: {
					precision: 0,
					grouping: 3,
					thousand: ",",
					decimal: "."
				}
			}
		},
		w = Array.prototype.map,
		u = Array.isArray,
		v = Object.prototype.toString,
		o = c.unformat = c.parse = function (a, b) {
			if (m(a)) return j(a, function (a) {
				return o(a, b)
			});
			a = a || 0;
			if ("number" === typeof a) return a;
			var b = b || ".",
				c = RegExp("[^0-9-" + b + "]", ["g"]),
				c = parseFloat(("" + a).replace(/\((.*)\)/, "-$1").replace(c, "").replace(b, "."));
			return !isNaN(c) ? c : 0
		},
		y = c.toFixed = function (a, b) {
			var b = n(b, c.settings.number.precision),
				d = Math.pow(10, b);
			return (Math.round(c.unformat(a) * d) / d).toFixed(b)
		},
		t = c.formatNumber = c.format = function (a, b, d, i) {
			if (m(a)) return j(a, function (a) {
				return t(a, b, d, i)
			});
			var a = o(a),
				e = s(r(b) ? b : {
					precision: b,
					thousand: d,
					decimal: i
				}, c.settings.number),
				h = n(e.precision),
				f = 0 > a ? "-" : "",
				g = parseInt(y(Math.abs(a || 0), h), 10) + "",
				l = 3 < g.length ? g.length % 3 : 0;
			return f + (l ? g.substr(0, l) + e.thousand : "") + g.substr(l).replace(/(\d{3})(?=\d)/g, "$1" + e.thousand) + (h ? e.decimal + y(Math.abs(a), h).split(".")[1] : "")
		},
		A = c.formatMoney = function (a, b, d, i, e, h) {
			if (m(a)) return j(a, function (a) {
				return A(a, b, d, i, e, h)
			});
			var a = o(a),
				f = s(r(b) ? b : {
					symbol: b,
					precision: d,
					thousand: i,
					decimal: e,
					format: h
				}, c.settings.currency),
				g = x(f.format);
			return (0 < a ? g.pos : 0 > a ? g.neg : g.zero).replace("%s", f.symbol).replace("%v", t(Math.abs(a), n(f.precision), f.thousand, f.decimal))
		};
	c.formatColumn = function (a, b, d, i, e, h) {
		if (!a) return [];
		var f = s(r(b) ? b : {
				symbol: b,
				precision: d,
				thousand: i,
				decimal: e,
				format: h
			}, c.settings.currency),
			g = x(f.format),
			l = g.pos.indexOf("%s") < g.pos.indexOf("%v") ? !0 : !1,
			k = 0,
			a = j(a, function (a) {
				if (m(a)) return c.formatColumn(a, f);
				a = o(a);
				a = (0 < a ? g.pos : 0 > a ? g.neg : g.zero).replace("%s", f.symbol).replace("%v", t(Math.abs(a), n(f.precision), f.thousand, f.decimal));
				if (a.length > k) k = a.length;
				return a
			});
		return j(a, function (a) {
			return q(a) && a.length < k ? l ? a.replace(f.symbol, f.symbol + Array(k - a.length + 1).join(" ")) : Array(k - a.length + 1).join(" ") + a : a
		})
	};
	if ("undefined" !== typeof exports) {
		if ("undefined" !== typeof module && module.exports) exports = module.exports = c;
		exports.accounting = c
	} else "function" === typeof define && define.amd ? define([], function () {
		return c
	}) : (c.noConflict = function (a) {
		return function () {
			p.accounting = a;
			c.noConflict = z;
			return c
		}
	}(p.accounting), p.accounting = c)
})(this);
jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function (oSettings, iDelay) {
	var _that = this;
	if (iDelay === undefined) {
		iDelay = 500;
	}
	this.each(function (i) {
		$.fn.dataTableExt.iApiIndex = i;
		var
			$this = this,
			oTimerId = null,
			sPreviousSearch = null,
			anControl = $('input', _that.fnSettings().aanFeatures.f);

		anControl.unbind('keyup search input').bind('keyup search input', function () {
			var $$this = $this;

			if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
				window.clearTimeout(oTimerId);
				sPreviousSearch = anControl.val();
				oTimerId = window.setTimeout(function () {
					$.fn.dataTableExt.iApiIndex = i;
					_that.fnFilter(anControl.val());
				}, iDelay);
			}
		});
		return this;
	});
	return this;
};

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
function currencyFormat(x) {
    return '<div class="text-right">'+formatMoney(x != null ? x : 0)+'</div>';
}
function decimalFormat(x) {
    return '<div class="text-center">'+formatNumber(x != null ? x : 0)+'</div>';
}
function formatNumber(x, d) {
    if(!d && d != 0) { d = site.settings.decimals; }
    if(site.settings.sac == 1) {
        return formatSA(parseFloat(x).toFixed(d));
    }
    return accounting.formatNumber(x, d, site.settings.thousands_sep == 0 ? ' ' : site.settings.thousands_sep, site.settings.decimals_sep);
}
function pqFormat(x) {
    if (x != null) {
        var d = '', pqc = x.split("___");
        for (index = 0; index < pqc.length; ++index) {
            var pq = pqc[index];
            var v = pq.split("__");
            d += v[0]+' ('+formatQuantity2(v[1])+')<br>';
        }
        return d;
    } else {
        return '';
    }
}
function decode_html(value){
    return $('<div/>').html(value).text();
}
function formatQuantity2(x) {
    return (x != null) ? formatQuantityNumber(x, site.settings.qty_decimals) : '';
}
function user_status(x) {
    var y = x.split("__");
    return y[0] == 1 ?
    '<a href="'+site.base_url+'auth/deactivate/'+ y[1] +'" data-toggle="modal" data-target="#myModal"><span class="label label-success"><i class="fa fa-check"></i> '+lang['active']+'</span></a>' :
    '<a href="'+site.base_url+'auth/activate/'+ y[1] +'"><span class="label label-danger"><i class="fa fa-times"></i> '+lang['inactive']+'</span><a/>';
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
function row_status(x) {
    if(x == null) {
        return '';
    } else if(x == 'pending') {
        return '<div class="text-center"><span class="row_status label label-warning">'+lang[x]+'</span></div>';
    } else if(x == 'completed' || x == 'paid' || x == 'sent' || x == 'received') {
        return '<div class="text-center"><span class="row_status label label-success">'+lang[x]+'</span></div>';
    } else if(x == 'partial' || x == 'transferring' || x == 'ordered') {
        return '<div class="text-center"><span class="row_status label label-info">'+lang[x]+'</span></div>';
    } else if(x == 'due' || x == 'returned') {
        return '<div class="text-center"><span class="row_status label label-danger">'+lang[x]+'</span></div>';
    } else {
        return '<div class="text-center"><span class="row_status label label-default">'+x+'</span></div>';
    }
}
function img_hl(x) {
    var image_link = (x == null || x == '') ? 'no_image.png' : x;
    return '<div class="text-center"><a href="'+site.url+'assets/uploads/' + image_link + '" data-toggle="lightbox"><img src="'+site.url+'assets/uploads/thumbs/' + image_link + '" alt="" style="width:30px; height:30px;" /></a></div>';
}
function formatQuantity(x) {
    return (x != null) ? '<div class="text-center">'+formatNumber(x, site.settings.qty_decimals)+'</div>' : '';
}
$(document).ready(function(e) {
    window.location.hash ? e('#myTab a[href="' + window.location.hash + '"]').tab('show') : e("#myTab a:first").tab("show");
    e("#myTab2 a:first, #dbTab a:first").tab("show");
    e("#myTab a, #myTab2 a, #dbTab a").click(function(t) {
        t.preventDefault();
        e(this).tab("show");
    });
});
