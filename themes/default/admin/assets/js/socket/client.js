console.log(socket_enable)
if (socket_enable==1) {
    

var socket = io.connect( 'http://'+window.location.hostname+':'+socket_port );


$(document).ready(function(){
    console.log(socket.id)
    socket.on( 'notification', function( data ) {
        show_nofication();	
    });
    
	socket.on('pro_notification', function( data ) {
		
		console.log(socket.id);
		console.log('dddd');
		procurment_notification();	
	});
    
    //socket.on("new_msg", function(data) {
    //    socket.join('BBQ123');
    //    alert(data.msg);
    //});
});


$(document).ready(function(){
    
    if (typeof curr_func == 'undefined') {
        curr_func = '';
    }
    
    //socket.on( 'bbq_cover_confirmed', function( data) {
    //        console.log(data);
    //        console.log( socket.id)
    //        fn('Cover_confirmed_recieved');       
    //    });
    
//    socket.emit('read_notification', { 
//	user_id : 5,
//        table_id:9,
//        notify_id:1,
//	bbq_code:'BBQ20181002195136',
//        msg:'TABLE 22 - Customer has sent BBQ Covers.',
//        type:'BBQ Covers validation request - BBQ20181002195136'
//    });

//    socket.emit('user_socket', { 
//	user_id : 5,
//        group_id:5,
//	device_imei:'356480080289816',
//	user_type:1,
//	device_token:'dawpZCksI8s:APA91bEA-nF1eAvncdaAVMj-Ikt3KfxIS_qmFS8fBYobjix5TTkkr2897TE8QaGsAi7ynyhONFX_HBoNQvjtmwc4nI5I1g4wxoXonpshPcTGRPRyDcvOn1_kjC6s7eFuLmYC2yYZ3ZMw',
//	socket_id:socket.id
//    });
//    socket.emit('customer_socket', { 
//	table_id : 9,
//	device_imei:'351896051869747',
//	device_type:1,
//	//device_token:'dawpZCksI8s:APA91bEA-nF1eAvncdaAVMj-Ikt3KfxIS_qmFS8fBYobjix5TTkkr2897TE8QaGsAi7ynyhONFX_HBoNQvjtmwc4nI5I1g4wxoXonpshPcTGRPRyDcvOn1_kjC6s7eFuLmYC2yYZ3ZMw',
//	socket_id:socket.id
//    });
//    socket.emit('push_notification', { 
//	title : 5,
//        msg:5,
//	token:'356480080289816',
//	socket_id:socket.id
//    });
//     socket.on( 'push_notification', function( data ) {
//        console.log('push');
//        console.log(data);
//     });
   /******************************* tables list ****************************/
    if (curr_func=='refresh_tables') {
        console.log(curr_func)
        socket.on( 'update_table', function( data ) {
            
            console.log(data)
            if (data.update==1) {
                console.log('ordertable'+data.tableid);
                ajaxData_table(data.tableid);
                
            }         
        });
    }
    if (curr_func=='refresh_bbqtables') {
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
}