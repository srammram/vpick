
var port = 6441;
$(document).ready(function(){
    
    if (typeof curr_func == 'undefined') {
        curr_func = '';
    }
    
    var socket = io.connect( 'http://'+window.location.hostname+':'+port );
	
        socket.on( 'notification', function( data ) {
            console.log(socket.id);
            show_nofication();	
        });
		
		
		
	socket.emit( 'testing', {
	    title:'test',
	    msg:'hiii',
	    bbqcode:'ttt'
	},function(confirmation){
	  console.log(confirmation)
	}
      
    );
  
   /******************************* tables list ****************************/
    if (curr_func=='refresh_tables') {
        var socket = io.connect( 'http://'+window.location.hostname+':'+port );
	//console.log(socket)
        socket.on( 'update_table', function( data ) {
            
            console.log('data.update'+data.update)
            if (data.update==1) {
                console.log('ordertable'+data.tableid);
                ajaxData_table(data.tableid);
                
            }         
        });
    }
    if (curr_func=='refresh_bbqtables') {
        var socket = io.connect( 'http://'+window.location.hostname+':'+port );
        socket.on( 'update_bbqtable', function( data ) {
            
            console.log('data.update'+data.update)
            if (data.update==1) {
                console.log(data.tableid)
                ajaxData_table(data.tableid);
                
            }         
        });
    }
    /******************************* tables list - End****************************/
    
    /******************************* tables list - emit function ****************************/
    if (curr_func=="update_tables") {
            $tid = tableid;
            var socket = io.connect( 'http://'+window.location.hostname+':'+port );

            socket.emit('update_table', { 
              update: 1,
              tableid:$tid
            });
            removeParam('tid');
    }
    /******************************* tables list - emit function - end****************************/
    
    
    /******************************* bbqtables list - emit function ****************************/
        
         if (curr_func=='update_bbqtables') {
            $tid = tableid;
                var socket = io.connect( 'http://'+window.location.hostname+':'+port );
    console.log($tid)
                socket.emit('update_bbqtable', { 
                  update: 1,
                  tableid:$tid
                });
                removeParam('bbqtid');
        }
    /******************************* bbqtables list - emit function -end ****************************/
    
});
function urlParam(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results==null){
           return null;
        }
        else{
           return decodeURI(results[1]) || 0;
        }
    }
function removeParam(parameter)
        {
          var url=document.location.href;
          var urlparts= url.split('?');
        
         if (urlparts.length>=2)
         {
          var urlBase=urlparts.shift(); 
          var queryString=urlparts.join("?"); 
        
          var prefix = encodeURIComponent(parameter)+'=';
          var pars = queryString.split(/[&;]/g);
          for (var i= pars.length; i-->0;)               
              if (pars[i].lastIndexOf(prefix, 0)!==-1)   
                  pars.splice(i, 1);
          url = urlBase+'?'+pars.join('&');
          window.history.pushState('',document.title,url); // added this line to push the new url directly to url bar .
        
        }
        return url;
    }