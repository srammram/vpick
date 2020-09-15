function Ratings(a) {
    "use strict";

    function d(a) {
        if ("mousemove" !== a.type || 0 !== a.buttons) {
            var c = this.getBoundingClientRect().width,
                d = 0;
            switch (a.type) {
                case "mouseup":
                case "mousemove":
                    d = a.layerX;
                    break;
                case "touchend":
                case "touchmove":
                    d = a.changedTouches[0].pageX - parseInt(this.getBoundingClientRect().left, 10)
            }
            var e = parseInt(100 / c * d, 10),
                f = e / 20;
            b.rating.setValue(f)
        }
    }
    if (!(this instanceof Ratings)) return new Ratings(a);
    "undefined" == typeof a && (a = {});//, void 0 === a.value && (a.value = 1);
    var b = document.createElement("div");
    b.classList.add("rating-stars");
    var c = '<div class="bg"><i class="material-icons">star_rate</i><i class="material-icons">star_rate</i><i class="material-icons">star_rate</i><i class="material-icons">star_rate</i><i class="material-icons">star_rate</i></div><div class="value"><i class="material-icons" data-n=1>star_rate</i><i class="material-icons" data-n=2>star_rate</i><i class="material-icons" data-n=3>star_rate</i><i class="material-icons" data-n=4>star_rate</i><i class="material-icons" data-n=5>star_rate</i></div>';
    switch (b.insertAdjacentHTML("afterbegin", c), b.rating = {
        _value: 0,
        setValue: function(c) {
            var d = b;
            if ("int" === a.input) {
                c = parseInt(c);
                var $p = .5;
                if(c!=0){
                    c = c+$p;
                }
        
            }
            "int" === a.input && (c = Math.round(c)), c = Math.max(c, 0), c = Math.min(c, 5);//, "int" === a.input && 0 === c && (c = 1);
           // "int" === a.input, c = Math.max(c, 0), c = Math.min(c, 5), "int" === a.input && 0 === c && (c = 1);
           var e = 20 * c;
            d.children[1].style.width = e + "%", this._value = c,
            d.children[1].setAttribute('data-value', c);
        },
        getValue: function() {
            return this._value
        }
    }, b.rating.setValue(a.value), a.input) {
        case "int":
        case "float":
            b.classList.add("rating-stars--input"), b.addEventListener("mouseup", d), b.addEventListener("mousemove", d), b.addEventListener("touchend", d), b.addEventListener("touchmove", d)
    }
    return b
}