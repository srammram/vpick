function drawInfobox(a,i,e,d){if(e.data[d].color)var t=e.data[d].color;else t="";if(e.data[d].price)var l='<div class="price">'+e.data[d].price+"</div>";else l="";if(e.data[d].id)var r=e.data[d].id;else r="";if(e.data[d].url)var s=e.data[d].url;else s="";if(e.data[d].type)var c=e.data[d].type;else c="";if(e.data[d].title)var v=e.data[d].title;else v="";if(e.data[d].location)var f=e.data[d].location;else f="";if(e.data[d].gallery[1])var o=e.data[d].gallery[1];var n="";return n='<div class="infobox '+t+'"><div class="inner"><div class="image"><div class="overlay"><div class="wrapper"><hr><a href="'+s+'" class="detail">Go to Detail</a></div></div><a href="'+s+'" class="description"><div class="meta">'+l+"<h2>"+v+"</h2><figure>"+f+'</figure><i class="fa fa-angle-right"></i></div></a><img src="'+o+'" alt="'+ v +'" title="'+ v +'"></div></div></div>'}