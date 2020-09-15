"use strict";
var heatmap;
var taxiData;
var gradient = [

                    'rgba(0, 255, 255, 0)',

                    'rgba(0, 255, 255, 1)',

                    'rgba(0, 191, 255, 1)',

                    'rgba(0, 127, 255, 1)',

                    'rgba(0, 63, 255, 1)',

                    'rgba(0, 0, 255, 1)',

                    'rgba(0, 0, 223, 1)',

                    'rgba(0, 0, 191, 1)',

                    'rgba(0, 0, 159, 1)',

                    'rgba(0, 0, 127, 1)',

                    'rgba(63, 0, 91, 1)',

                    'rgba(127, 0, 63, 1)',

                    'rgba(191, 0, 31, 1)',

                    'rgba(255, 0, 0, 1)'

                ]

                
function createHomepageGoogleMap(a, b, c,$type=false) {
    taxiData = [];
    $.each(c.data,function(n,v){
        taxiData.push(new google.maps.LatLng(v.latitude, v.longitude));
    })
   

    function d() {
        function v(a) {
            var b = new google.maps.LatLng(a.coords.latitude, a.coords.longitude);
            g.setCenter(b), g.setZoom(14);
            var c = document.createElement("DIV");
            c.innerHTML = '<div class="map-marker"><div class="icon"></div></div>';
            var d = new RichMarker({
                position: b,
                map: g,
                draggable: !1,
                content: c,
                flat: !0
            });
            
  
            d.content.className = "marker-loaded";
            var e = new google.maps.Geocoder;
            e.geocode({
                latLng: b
            }, function(a, b) {
                if (b == google.maps.GeocoderStatus.OK) {
                    var c = a[0].geometry.location.lat(),
                        d = a[0].geometry.location.lng();
                    a[0].address_components[0].long_name, new google.maps.LatLng(c, d);
                    $("#location").val(a[0].formatted_address)
                }
            })
        }
        c.latitude && (a = c.latitude), c.longitude && (b = c.longitude);
        var d = new google.maps.LatLng(a, b),
            e = {
                zoom: 14,
                center: d,
                disableDefaultUI: !1,
                scrollwheel: !0,
                //styles: mapStyles,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.BOTTOM_CENTER
                },
                panControl: !1,
                zoomControl: !0,
                zoomControlOptions: {
                    style: google.maps.ZoomControlStyle.LARGE,
                    position: google.maps.ControlPosition.RIGHT_TOP
                }
            },
            f = document.getElementById("map"),
            g = new google.maps.Map(f, e),
            h = [];
                    
            if (c.data==undefined) {
                c.data = {};
            }
        newMarkers = [], h = c.data;
        for (var i = 0, j = !1, k = !1, l = !1, m = !1, n = 0; n < c.data.length; n++) {
            if (c.data[n].color) var o = c.data[n].color;
            else o = "";
            var p = document.createElement("DIV");
            1 == c.data[n].featured ? p.innerHTML = '<img src="' + c.data[n].type_icon + '">' : p.innerHTML = '<img src="' + c.data[n].type_icon + '">';
            var q = new RichMarker({
                position: new google.maps.LatLng(c.data[n].latitude, c.data[n].longitude),
                map: g,
                draggable: !1,
                content: p,
                flat: !0
            });
            var pointArray = new google.maps.MVCArray(taxiData);
             heatmap = new google.maps.visualization.HeatmapLayer({
                data: pointArray,
                radius: 20
            });
            // placing the heatmap on the map
            heatmap.setMap(g);
            heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
            
            if ($type=='traffic') {
                var trafficLayer = new google.maps.TrafficLayer();
                trafficLayer.setMap(g);
                console.log(trafficLayer)
            }
            newMarkers.push(q);
            var r = document.createElement("div"),
                s = {
                    content: r,
                    disableAutoPan: !1,
                    pixelOffset: new google.maps.Size(-18, -42),
                    zIndex: null,
                    alignBottom: !0,
                    boxClass: "infobox",
                    enableEventPropagation: !0,
                    closeBoxMargin: "0px 0px -30px 0px",
                    closeBoxURL: theme_url + "images/map/close.png",
                    infoBoxClearance: new google.maps.Size(1, 1)
                },
                t = c.data[n].category;
            r.innerHTML = drawInfobox(t, r, c, n), newMarkers[n].infobox = new InfoBox(s), google.maps.event.addListener(q, "mouseover", function(a, b) {
                return function() {
                    l = newMarkers[b], m != l && (m = newMarkers[b], newMarkers[b].content.innerHTML = '<img src="' + h[b].type_hover_icon + '">', newMarkers[b].content.className = "marker-active marker-loaded")
                }
            }(q, n)), google.maps.event.addListener(q, "mouseout", function(a, b) {
                return function() {
                    m != k && (newMarkers[b].content.innerHTML = '<img src="' + h[b].type_icon + '">', newMarkers[b].content.className = "marker-loaded", m = !1)
                }
            }(q, n)), google.maps.event.addListener(q, "click", function(a, b) {
                return function() {
                    if (google.maps.event.addListener(g, "click", function(a) {
                            k = newMarkers[b]
                        }), j = newMarkers[b], j != k) {
                        for (var a = 0; a < newMarkers.length; a++) newMarkers[a].content.className = "marker-loaded", newMarkers[a].infobox.close();
                        newMarkers[b].infobox.open(g, this), newMarkers[b].infobox.setOptions({
                            boxClass: "fade-in-marker"
                        }), newMarkers[b].content.innerHTML = '<img src="' + h[b].type_hover_icon + '">', newMarkers[b].content.className = "marker-active marker-loaded", i = 1
                    }
                }
            }(q, n)), google.maps.event.addListener(newMarkers[n].infobox, "closeclick", function(a, b) {
                return function() {
                    j = 0, newMarkers[b].content.className = "marker-loaded", newMarkers[b].infobox.setOptions({
                        boxClass: "fade-out-marker"
                    }), newMarkers[b].content.innerHTML = '<img src="' + h[b].type_icon + '">', k = 0
                }
            }(q, n))
        }
        google.maps.event.addListener(g, "click", function(a) {
            0 != j && 0 != k && (1 == i ? (j.infobox.open(g), j.infobox.setOptions({
                boxClass: "fade-in-marker"
            }), j.content.innerHTML = '<img src="' + h[newMarkers.indexOf(j)].type_hover_icon + '">', j.content.className = "marker-active marker-loaded") : (i = 0, j.infobox.setOptions({
                boxClass: "fade-out-marker"
            }), j.content.innerHTML = '<img src="' + h[newMarkers.indexOf(j)].type_icon + '">', j.content.className = "marker-loaded", setTimeout(function() {
                j.infobox.close()
            }, 350)), i = 0), 0 != j && google.maps.event.addListener(j, "click", function(a) {
                i = 1
            }), i = 0
        }), google.maps.event.addListener(g, "idle", function() {
            var b = [];
            if ($(".results .last-row").length > 0 && $(".results li").removeClass("last-row"), $.each(c.data, function(a) {
                    var d = "";
                    pushItemsToArray(c, a, d, b)
                }), "" != b) {
                $(".items-list .results").html(b);
                var d = 0;
                $(".loc-store-rating").each(function(a, b) {
                    d = $(this).attr("data-rating"), $(this).html(new Ratings({
                        value: d
                    }))
                })
            } else $l = $("#locations").val(), $(".results-near").html('Hummm, your search was not found. Please click <a href="' + theme_url + '/add_store_front" class="send-store-request-link">here</a> to add your location.</br><div class="send-store-con"><button type="button" class="send-store-request">Add Store</button></div>'), $(".items-list .results").html("");
            $.each(c.data, function(a) {
                g.getBounds().contains(new google.maps.LatLng(c.data[a].latitude, c.data[a].longitude))
            }), rating(".results .item");
            var e = $(".results .item");
            e.hover(function() {
                newMarkers[$(this).attr("id") - 1].content.innerHTML = '<img src="' + h[$(this).attr("id") - 1].type_hover_icon + '">', newMarkers[$(this).attr("id") - 1].content.className = "marker-active marker-loaded", g.getBounds().contains(newMarkers[$(this).attr("id") - 1].getPosition()) || g.panToWithOffset(newMarkers[$(this).attr("id") - 1].position, 0, -20)
            }, function() {
                newMarkers[$(this).attr("id") - 1].content.innerHTML = '<img src="' + h[$(this).attr("id") - 1].type_icon + '">', newMarkers[$(this).attr("id") - 1].content.className = "marker-loaded"
            })
        }), $(".geolocation").on("click", function() {
            navigator.geolocation ? navigator.geolocation.getCurrentPosition(v) : console.log("Geo Location is not supported")
        })
    }
    $.get(theme_url + "js/map/_infobox.js", function() {
        d()
    })
}

function createHomepageOSM(a, b, c, d) {
    function e() {
        var e = L.map("map", {
            center: [a, b],
            zoom: 14,
            scrollWheelZoom: !1
        });
        L.tileLayer.provider(d).addTo(e);
        for (var f = L.markerClusterGroup({
                showCoverageOnHover: !1,
                zoomToBoundsOnClick: !1
            }), g = [], h = 0; h < c.data.length; h++) {
            if (c.data[h].type_icon) var i = '<img src="' + c.data[h].type_icon + '">';
            else i = "";
            if (c.data[h].color) var j = c.data[h].color;
            else j = "";
            var k = i,
                m = (L.divIcon({
                    html: k,
                    iconSize: [36, 46],
                    iconAnchor: [18, 32],
                    popupAnchor: [130, -28],
                    className: ""
                }), c.data[h].title),
                n = L.marker(new L.LatLng(c.data[h].latitude, c.data[h].longitude), {
                    title: m,
                    icon: i
                });
            g.push(n);
            var o = c.data[h].category,
                p = document.createElement("div");
            n.bindPopup(drawInfobox(o, p, c, h)), f.addLayer(n), n.on("popupopen", function() {
                this._icon.className += " marker-active"
            }), n.on("popupclose", function() {
                this._icon.className = "leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded"
            })
        }
        e.addLayer(f), animateOSMMarkers(e, g, c), e.on("moveend", function() {
            animateOSMMarkers(e, g, c)
        }), f.on("clusterclick", function(a) {
            for (var b = a.layer.getAllChildMarkers(), d = [], e = [], f = 0; f < b.length; f++) {
                var g = parseFloat(b[f]._latlng.lat).toFixed(6),
                    h = parseFloat(b[f]._latlng.lng).toFixed(6);
                d.push(g), e.push(h)
            }
            Array.prototype.allValuesSame = function() {
                for (var a = 1; a < this.length; a++)
                    if (this[a] !== this[0]) return !1;
                return !0
            }, d.allValuesSame() && e.allValuesSame() ? multiChoice(d[0], e[0], c) : a.layer.zoomToBounds()
        }), $(".results .item").hover(function() {
            g[$(this).attr("id") - 1]._icon.className = "leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded marker-active"
        }, function() {
            g[$(this).attr("id") - 1]._icon.className = "leaflet-marker-icon leaflet-zoom-animated leaflet-clickable marker-loaded"
        }), $(".geolocation").on("click", function() {
            e.locate({
                setView: !0
            })
        }), $("body").addClass("loaded"), setTimeout(function() {
            $("body").removeClass("has-fullscreen-map")
        }, 1e3), $("#map").removeClass("fade-map")
    }
    $.get(theme_url + "js/map/_infobox.js", function() {
        e()
    })
}

function itemDetailMap(a) {
    var b = new google.maps.LatLng(a.latitude, a.longitude),
        c = {
            zoom: 14,
            center: b,
            disableDefaultUI: !0,
            scrollwheel: !0,
            styles: mapStyles,
            panControl: !1,
            zoomControl: !1,
            draggable: !0
        },
        d = document.getElementById("map-detail"),
        e = new google.maps.Map(d, c);
    if (a.type_icon) var f = '<img src="' + a.type_icon + '">';
    else f = "";
    var g = document.createElement("DIV");
    g.innerHTML = f;
    var h = new RichMarker({
        position: new google.maps.LatLng(a.latitude, a.longitude),
        map: e,
        draggable: !1,
        content: g,
        flat: !0
    });
    h.content.className = "marker-loaded"
}

function simpleMap(a, b, c) {
    var d = new google.maps.LatLng(a, b),
        e = {
            zoom: 14,
            center: d,
            disableDefaultUI: !0,
            scrollwheel: !0,
            styles: mapStyles,
            panControl: !1,
            zoomControl: !1,
            draggable: !0
        },
        f = document.getElementById("map-simple"),
        g = new google.maps.Map(f, e),
        h = document.createElement("DIV");
    h.innerHTML = '<div class="map-marker"><div class="icon"></div></div>';
    var i = new RichMarker({
        position: new google.maps.LatLng(a, b),
        map: g,
        draggable: c,
        content: h,
        flat: !0
    });
    i.content.className = "marker-loaded"
}

function pushItemsToArray(a, b, c, d) {
    function v(a) {
        return a ? f = '<div class="price">' + a + "</div>" : ""
    }
    var e = a.data.length;
    $listLocationids.push(a.data[b].id);
    var f, g = a.data[b].title;
    g = g.replace(/ /g, "-");
    var h = a.data[b].url,
        k = (100 * a.data[b].average / 5 + 1 + "%", "");
    0 != a.data[b].total && (k = " by ");
    var l = a.data[b].url + "#deals",
        m = "";
    a.data[b].deals && (m = '<div class="loc-deal-wrapper" title="Deals" onclick="location.href=\'' + l + "'\"></div>");
    var n = "";
    a.data[b].labtested && (n = '<div class="lap-test-wrapper" title="Lap Tested"></div>');
    var o = "";
    a.data[b].licensed && (o = '<div class="licensed-business-wrapper" title="Licensed business"></div>');
    var p = "Reviewers";
    a.data[b].total <= 1 && (p = "Reviewer");
    var q = "",
        r = "",
        s = "";
    q = a.data[b].followTitle ? '<a href="#" title="' + a.data[b].followTitle + '"  class="follow-store followstore-loc ' + a.data[b].followClass + '" data-action="' + a.data[b].followaction + '"></a>' : '<a href="#" title="Follow" class="login-popup follow-store"></a>', a.data[b].follows_count && (0 == a.data[b].follows_count && $(".followcnt-container").hide(), s = 1 == a.data[b].follows_count ? " Follower" : " Followers", r = '<div class="followcnt-container"><span class="follows_count">' + a.data[b].follows_count + "</span>" + s + "</div>");
    var t = "";
    e == b + 1 && (t = "last-row"), e == b + 1 && a.laststore && (t = "end-row");
    var u = "";
    1 == a.data[b].verified && (u = '<div class="follow-info">' + q + r + "</div>"), d.push('<li class="' + t + '"><div class="item" id="' + (b + 1) + '" data-service="' + a.data[b].selected_service + '" data-id="' + a.data[b].unique_id + '"><a href="' + h + '" class="image" target="_blank"><div class="inner"><img  src="' + a.data[b].gallery[0] + '" alt="' + a.data[b].list_title + '" title="' + a.data[b].list_title + '"><div class="loc-open-close"><p class="open-close">' + a.data[b].open_status + '</p><i class="loc-ser-icon fa ' + a.data[b].img_class + '" aria-hidden="true"></i></div></div></a><div class="wrapper"><a href="' + h + '" id="' + a.data[b].id + '" target="_blank" class="store-title"><h3>' + a.data[b].list_title + "</h3></a><figure>" + a.data[b].city_state_zip + '</figure><div class="loc-store-rating" data-rating="' + a.data[b].average + '"></div><a href="' + h + '#rating-reviews"><span>' + a.data[b].total + "</span> " + p + "</a>" + u + v(a.data[b].price) + '<div class="info"><div class="distance" >' + a.data[b].distance + ' </div><a href="#" class="store-quick-view">Quick View</a><a href="' + h + '">Details</a></div></div>' + m + n + o + "</div></li>")
}

function centerMapToMarker() {
    $.each(json.data, function(a) {
        if (json.data[a].id == id) {
            var b = json.data[a].latitude,
                c = json.data[a].longitude,
                d = new google.maps.LatLng(b, c);
            map.setCenter(d)
        }
    })
}

function multiChoice2(a, b, c) {
    var d = [];
    "" != c.data && $.each(c.data, function(e) {
        c.data[e].latitude == a && c.data[e].longitude == b && pushItemsToArray(c, e, c.data[e].category, d)
    }), $("body").append('<div class="modal-window multichoice fade_in"></div>'), $(".modal-window").load(theme_url + "assets/external/_modal-multichoice.html", function() {
        $(".modal-window .modal-wrapper .items").html(d), rating(".modal-window")
    }), $(".modal-window .modal-background, .modal-close").live("click", function(a) {
        $(".modal-window").addClass("fade_out"), setTimeout(function() {
            $(".modal-window").remove()
        }, 300)
    })
}

function animateOSMMarkers(a, b, c) {}

function redrawMap(a, b) {
    $(".map .toggle-navigation").click(function() {
        $(".map-canvas").toggleClass("results-collapsed"), $(".map-canvas .map").one("transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd", function() {
            "osm" == a ? b.invalidateSize() : "google" == a && google.maps.event.trigger(b, "resize")
        })
    })
}

function ImageExist(a) {
    var b = new Image;
    return b.src = a, 0 != b.height
}
var $ = jQuery.noConflict(),
    $body = $("body");
$body.hasClass("map-fullscreen") && ($(window).width() > 768 ? $(".map-canvas").height($(window).height() - $(".header").height()) : $(".map-canvas #map").height($(window).height() - $(".header").height()));
var newMarkers = [];
google.maps.Map.prototype.panToWithOffset = function(a, b, c) {
    var d = this,
        e = new google.maps.OverlayView;
    e.onAdd = function() {
        var e = this.getProjection(),
            f = e.fromLatLngToContainerPixel(a);
        f.x = f.x + b, f.y = f.y + c, d.panTo(e.fromContainerPixelToLatLng(f))
    }, e.draw = function() {}, e.setMap(this)
};