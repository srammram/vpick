$(document).ready(function(){
	var socket = io.connect('http://13.233.9.134:9000');
	
	socket.on('admin_rides', function( data ) {
		console.log()
		getDrivermove(data);	
	});
		
});