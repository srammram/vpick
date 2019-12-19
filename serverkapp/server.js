// JavaScript Document
var express = require('express');
var app = express();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var mysql = require("mysql");

var port = process.env.PORT || 5000;

var pool   = mysql.createPool({
  connectionLimit : 100000,
  host     : 'localhost',
  user     : 'root',
  password : 'ZIpMXQ',
  //password : '',
  database : 'heyycab_truck'
});
var schedule = require('node-schedule');

io.sockets.on('connection', function (socket){
	
	console.log('Socket Connect');
	console.log(socket.id);
	
	
		
	socket.on('server_other_login', function(data){
		console.log('other login');
		//console.log('Server Reached Destination');
		console.log(data.socket_id);
		console.log(data);
		socket.to(data.socket_id).emit('other_login', data);
	});
		
	socket.on('user_socket', (data, callback) => {
		console.log('Socket User');
		var user_id = data.user_id;
		var device_imei = data.device_imei;
		var user_type = data.user_type;
		var device_token = data.device_token;
		var socket_id = socket.id;	
		var table_name = 'kapp_users';
		
		console.log(user_type);
		if(user_id == '' || device_token == '' || user_type == ''){
			console.log('Mandotory fields missing');
			return;
		}else{
			
			if(user_type == 2){
				pool.query("UPDATE kapp_driver_current_status SET is_connected = 1 WHERE  driver_id="+user_id+" ORDER BY id DESC LIMIT 1  ");
				console.log("Driver mode update ");	
			}
			//console.log("SELECT u.id, u.oauth_token, u.devices_imei, u.mode, us.socket_id, us.user_type as socket_type FROM "+table_name+" AS u LEFT JOIN kapp_user_socket AS us ON us.user_id = u.id AND user_type = "+user_type+" WHERE u.oauth_token = '"+device_token+"' ");
			 pool.query("SELECT u.id, u.oauth_token, u.devices_imei, u.mode, us.socket_id, us.user_type as socket_type FROM "+table_name+" AS u LEFT JOIN kapp_user_socket AS us ON us.user_id = u.id AND user_type = "+user_type+" WHERE u.oauth_token = '"+device_token+"' ", function (error, results, fields){
				
				if(error){
					console.log('Data is empty');
					return;
				}
				console.log('user data socket: '+results);
				
				if(results.length == 0){
					console.log('empty user');
					var responseData = 'user_truncate1';
					return callback(responseData);
				}else{
					//console.log('not empty user');
					if(results[0].socket_id != null && results[0].socket_type != ''){
						
						if(results[0].devices_imei != device_imei){
							console.log('Not Updated');
							var responseData_notmatch = 'user_truncate';
							return callback(responseData_notmatch);
							
						}else{
							pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE  id="+user_id+"  ");
							pool.query("UPDATE kapp_user_socket SET device_imei = '"+device_imei+"',device_token = '"+device_token+"',socket_id = '"+socket_id+"' WHERE user_id = '"+user_id+"'  AND user_type = '"+user_type+"' " , function (error){
								if(error){
									//console.log('Not Updated');
									return;
								}
								console.log('Updated');
							});
						}
					}else if(results[0].socket_id == null && results[0].socket_type != null){
							pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE  id="+user_id+"  ");
							pool.query("UPDATE kapp_user_socket SET device_imei = '"+device_imei+"',device_token = '"+device_token+"',socket_id = '"+socket_id+"' WHERE user_id = '"+user_id+"'  AND user_type = '"+user_type+"' " , function (error){
								if(error){
									//console.log('Not Updated');
									return;
								}
								console.log('Updated1');
							});
					}else{
						pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE  id="+user_id+"  ");
						pool.query("INSERT INTO kapp_user_socket  (user_id, user_type, socket_id, device_imei, device_token) VALUES ('"+user_id+"', '"+user_type+"', '"+socket_id+"', '"+device_imei+"', '"+device_token+"') " , function (error){
							if(error){
								//console.log('Not Insert');
								return;
							}
							console.log('Inserted');
						});
					}
					console.log('f');
					//pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE user_id = '"+user_id+"'  " , function (error){
							//if(error){
								//console.log(error);
								//console.log(this.sql);
								//console.log('User Not Updated');
								//return;
							//}
							//console.log('User Updated');
						//});
						
						
					//var responseUData = results[0].mode;
					//return callback(responseUData);
				}
				
			});
		}
	});
	
	socket.on('notification', function(data){
		
		console.log('Notification');
		socket.emit('notification', data);
	});
	
	socket.on('server_notification', function(data){
		
		console.log('Notification Check');
		
	});
	
	
	socket.on('server_reached_destination', function(data){
		console.log('Socket Reached Destination');
		//console.log('Server Reached Destination');
		console.log(data.socket_id);
		console.log(data);
		socket.to(data.socket_id).emit('driver_reached_destination', data);
	});
	
	socket.on('server_ride_complete', function(data){
		console.log('Socket Ride Complete');
		//console.log('Server Ride Complete');
		console.log(data);
		socket.to(data.socket_id).emit('driver_ride_complete', data);
	});
	
	socket.on('server_ride_cancel', function(data){
		console.log('Socket Ride Cancel');
		//console.log('Server Ride Cancel');
		console.log(data);
		socket.to(data.socket_id).emit('driver_ride_cancel', data );
	});
	
	socket.on('server_booking_accept', function(data){
		console.log('Socket Driver Accept Ride');
		//console.log('Server Booking Accepts');
		console.log(data.socket_id);
		console.log(data);
		socket.to(data.socket_id).emit('driver_accept_ride', data );
	});
	
	socket.on('server_not_accept_driver', function(data){
		console.log('Socket Driver Not Accept Ride');
		//console.log('Server Booking Accepts');
		console.log(data.socket_id);
		console.log(data);
		socket.to(data.socket_id).emit('driver_not_accept_ride', data );
	});
	
	socket.on('server_ride_otp_verify', function(data){
		console.log('Socket Ride Verify OTP');
		//console.log('Server Ride OTP Verify');
		//console.log(data.socket_id);
		io.to(data.socket_id).emit('ride_otp_verify', data );
	});
	
	socket.on('server_booking_checking', function(data){
		console.log('Socket Booking Checking New Drivers');
		//console.log('Server Booking Checking');
		console.log(data);
		console.log(data.socket_id);
		socket.to(data.socket_id).emit('driver_available_check', data);
	});
	
	socket.on('driver_location', function(data){
		console.log('Socket Driver Location');
		//console.log(data);
		//console.log('driver_location');
		var oauth_token = data.oauth_token;
		var latitude = data.latitude;
		var longitude = data.longitude;
		console.log(latitude);
		console.log(longitude);
		
		if(oauth_token == '' || latitude == '' || longitude == ''){
			console.log('Mandotory fields missing');
			return;
		}else{
			pool.query("SELECT d.id, dcs.is_connected, dcs.current_latitude as cu_latitude, dcs.current_longitude as cu_longitude,  r.start_lat, r.start_lng, r.end_lat, r.end_lng, r.customer_id, r.id AS ride_id, df.location, df.final_distance, df.final_distance_total, r.status, us.socket_id, us.id AS usid FROM kapp_users AS d LEFT JOIN kapp_driver_current_status dcs ON dcs.driver_id = d.id AND dcs.allocated_status = 1  LEFT  JOIN kapp_rides AS r ON r.driver_id = d.id AND (r.status = 2 OR  r.status = 3 OR r.status = 9 ) LEFT  JOIN kapp_driver_frequency AS df ON df.ride_id = r.id AND df.driver_id = d.id LEFT  JOIN kapp_user_socket AS us ON us.user_id = r.customer_id AND us.user_type = 1 WHERE d.oauth_token = '"+oauth_token+"' ORDER BY d.id DESC LIMIT 1", function (error, results, fields){
				if(error){
					console.log('Data is error');
					return;
				}else{
					if(results.length == 0){
						console.log('Data is empty');
						return;
					}else{
						if(results[0].ride_id != null){
							var admin_driver_id = results[0].id;
							var admin_ride_id = results[0].ride_id;
							var admin_status = results[0].status;
							var admin_lat = latitude;
							var admin_lng = longitude;
							var admin_is_connected = results[0].is_connected;						
							console.log('admin emit ride');
							io.sockets.emit('admin_drivers_location', {'admin_driver_id': admin_driver_id, 'admin_ride_id': admin_ride_id, 'admin_lat': admin_lat, 'admin_lng': admin_lng, 'admin_is_connected' : admin_is_connected, 'admin_status' : admin_status });
							
							if(results[0].location != null){
								var object2 = [[results[0].location], [latitude, longitude, results[0].status]]; 
								pool.query("UPDATE kapp_driver_frequency SET location = '"+object2+"'  WHERE ride_id = '"+results[0].ride_id+"' AND driver_id = '"+results[0].id+"'  " );
							}else{
								var object1 = [[latitude, longitude, results[0].status]]; 
								pool.query("INSERT INTO kapp_driver_frequency  (driver_id, ride_id, location, final_distance_total) VALUES ('"+results[0].id+"', '"+results[0].ride_id+"', '"+object1+"', 0) ");
								console.log("Ride Insert");
							}
							
							//update
							pool.query("UPDATE kapp_driver_current_status SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE allocated_status = 1 AND driver_id="+results[0].id );
							pool.query("UPDATE taxi SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE driver_id="+results[0].id );
							pool.query("UPDATE users SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE id="+results[0].id );
							
							var actual_lat = 0;
							var actual_lng = 0;
							pool.query("SELECT a.latitude, a.longitude  FROM kapp_ride_route AS a WHERE a.ride_id = '"+results[0].ride_id+"' LIMIT 1", function (error, act_results_, fields){
								
								actual_lat =  act_results_[0].latitude;
								actual_lng =  act_results_[0].longitude;
								
							});
							
							var cus_ride_id = results[0].ride_id;
							var cus_lat = latitude;
							var cus_lng = longitude;
							var cus_pickup_lat = results[0].start_lat ? results[0].start_lat : 0;
							var cus_pickup_lng = results[0].start_lng ? results[0].start_lng : 0;
							var cus_drop_lat = results[0].end_lat ? results[0].end_lat : 0;
							var cus_drop_lng = results[0].end_lng ? results[0].end_lng : 0;
							var cus_actual_lat = actual_lat ? actual_lat : 0;
							var cus_actual_lng = actual_lng ? actual_lng : 0;
							
							socket.to(results[0].socket_id).emit('get_driver_location', { ride_id: cus_ride_id, lat: cus_lat, lng: cus_lng, pickup_lat: cus_pickup_lat, pickup_lng: cus_pickup_lng, drop_lat: cus_drop_lat, drop_lng: cus_drop_lng, actual_lat: cus_actual_lat, actual_lng: cus_actual_lng });
						
						}else{
							var admin_driver_id4= results[0].id;
							var admin_ride_id4 = 0;
							var admin_status4 = 0;
							var admin_lat4 = latitude;
							var admin_lng4 = longitude;
							var admin_is_connected4 = results[0].is_connected;
							console.log('admin emit not ride');
							io.sockets.emit('admin_drivers_location', {'admin_driver_id': admin_driver_id4, 'admin_ride_id': admin_ride_id4, 'admin_lat': admin_lat4, 'admin_lng': admin_lng4, 'admin_is_connected' : admin_is_connected4, 'admin_status' : admin_status4 });
							
							//update
							pool.query("UPDATE kapp_driver_current_status SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE allocated_status = 1 AND driver_id="+results[0].id );
							pool.query("UPDATE taxi SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE driver_id="+results[0].id );
							pool.query("UPDATE users SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE id="+results[0].id );
						}
					}
				}
			});
		}
	});
	
	socket.on('admin_drivers_location', function(data){
		
		console.log('admin_drivers_location');
		console.log(data);
		//socket.emit('notification', data);
	});
	
	
	
	socket.on('disconnect', function(reason) {
		 console.log('User 1 disconnected because '+reason);
		console.log('Socket Disconnect');
		console.log(socket.id);
		
		
	  pool.query("SELECT * FROM kapp_user_socket WHERE socket_id = '"+socket.id+"' ORDER BY id DESC LIMIT 1", function (error, results, fields){
		  if(error){
			//console.log('Data is empty');
			return;
		}
		if(results.length != 0){
			//console.log(results);
			//console.log(results[0].user_id);
			if(results[0].user_id != null){
				
				var admin_driver_id5= results[0].user_id;
				var admin_ride_id5 = 0;
				var admin_lat5 = 0;
				var admin_lng5 = 0;
				var admin_lng5 = 0;
				var admin_is_connected5 = 0;
				
				io.sockets.emit('admin_drivers_location', {'admin_driver_id': admin_driver_id5, 'admin_ride_id': admin_ride_id5, 'admin_lat': admin_lat5, 'admin_lng': admin_lng5, 'admin_is_connected' : admin_is_connected5 });
				
				//console.log('inside if');
				pool.query("UPDATE kapp_driver_current_status SET is_connected = '0' WHERE  driver_id="+results[0].user_id+" ORDER BY id DESC LIMIT 1 " );
				pool.query("UPDATE kapp_user_socket SET socket_id = '' WHERE  user_id="+results[0].user_id+" ORDER BY id DESC LIMIT 1 " );
				console.log("Driver mode update ");
			}else{
				//console.log('not inside if');
			}
		}else{
			//console.log('user socket data is empty');
		}
		  
	  });
	  
   });
		
});

http.listen(port, function () {
   
    var addr = http.address();
	
    console.log('listening on http://' + addr.address + ':' + addr.port);
	
	
});