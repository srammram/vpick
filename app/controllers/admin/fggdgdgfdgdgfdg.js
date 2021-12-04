// JavaScript Document
var socket  = require( 'socket.io' );
var express = require('express');
var app     = express();
var server  = require('http').createServer(app);
var io      = socket.listen( server );
var schedule = require('node-schedule');
var mysql = require("mysql");
var distance = require('google-distance');
distance.apiKey = 'AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8';

//var Distance = require('geo-distance');
var port    = process.env.PORT || 9000;

var pool   = mysql.createPool({
  host     : 'localhost',
  user     : 'root',
  password : 'ZIpMXQ',
  database : 'kapp_test'
});

server.listen(port, function () {
  console.log('Server listening at port %d', port);
});


io.on('connection', function (socket) {

		console.log('Socket Connect');
		console.log(socket.id);
		
	schedule.scheduleJob('0 * * * * *', function(){
		console.log('Socket Cron Job');
		var today = new Date();
		var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		var time = today.getHours() + ":" + today.getMinutes() + ":00";
		var dateTime = date+' '+time;
		
		pool.query("SELECT * FROM kapp_rides WHERE ride_type = 2 AND emit_status = 0 AND booking_timing >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)   ", function (error, results, fields){
			if(error){
				//console.log('Ride later empty');
				return;
			}
			if(results.length == 0){
				console.log('ride later data empty');
				return;
			}else{
				console.log('ride later records');
				//console.log(results[0]);
				//console.log(results[0].start_lat);
				var booked_type_text;
				if(results[0].booked_type == 1){
					 booked_type_text = 'City Ride';
				}else if(results[0].booked_type == 2){
					 booked_type_text = 'Rental Ride';
				}else if(results[0].booked_type == 3){
					 booked_type_text = 'Outstation Ride';
				}else{
					 booked_type_text = 'No Ride';
				}
			
				
				pool.query("SELECT  d.id, d.mobile, d.country_code, d.oauth_token, dcs.current_latitude latitude, dcs.current_longitude longitude, dcs.mode, d.first_name, up.last_name, up.photo as driver_photo, t.name as taxi_name, t.model, t.number, t.type, t.photo as taxi_photo,  tt.name type_name, tt.image, tt.image_hover, tt.mapcar type_image,  g.name as group_name,   ( 6371 * acos( cos( radians("+results[0].start_lat+") ) * cos( radians( dcs.current_latitude ) ) * cos( radians( dcs.current_longitude ) - radians("+results[0].start_lng+") ) + sin( radians("+results[0].start_lat+") ) * sin( radians( dcs.current_latitude ) ) ) ) AS distance FROM kapp_users  AS d LEFT JOIN kapp_driver_current_status AS dcs ON dcs.driver_id = d.id  LEFT JOIN kapp_user_profile AS up ON up.user_id = d.id   JOIN kapp_taxi AS t ON t.id = dcs.taxi_id   JOIN kapp_taxi_type AS tt ON tt.id = t.type LEFT JOIN kapp_groups AS g ON g.id = d.group_id WHERE dcs.mode = 1 AND dcs.allocated_status = 1 AND tt.id = "+results[0].cab_type_id+"  GROUP BY d.id HAVING distance <= 100 ORDER BY distance ASC", function (error, dresults, fields){
					if(dresults.length == 0){
						console.log('ride empty  driver');
						pool.query("UPDATE kapp_rides SET emit_status = 0 WHERE id = '"+results[0].id+"' " , function (error){
							if(error){
								//console.log('Ride Not Updated');
								return;
							}
							console.log('Driver Length 0');
						});
					}else{
						console.log('ride driver ');
						//console.log(dresults[0]);
						var socket_user_id = 0;
						console.log(dresults[0].id);
						//console.log("SELECT * FROM kapp_user_socket WHERE user_id = '"+dresults[0].id+"' ");
						pool.query("SELECT * FROM kapp_user_socket WHERE user_id = '"+dresults[0].id+"'   ", function (error, uresults, fields){
							if(uresults.length == 0){
								//console.log('ride driver socket empty length');
								socket_user_id = 0;
							}else{
								//console.log(uresults[0]);
								console.log(uresults[0].socket_id);
								if(uresults[0].socket_id != null){
									//socket_user_id = uresults[0].socket_id;
									console.log('ride driver socket not empty data');
									
									var users = 
									{ "booked_type_text": booked_type_text, "pick_up": results[0].start, "from_latitude": results[0].start_lat,
									"from_longitude" : results[0].start_lng, "drop_off": results[0].end, "to_latitude": results[0].end_lat, "to_longitude": results[0].end_lng, "cab_type_id": ''+dresults[0].type+'', "ride_id": ''+results[0].id+'', 
									"driver_id": dresults[0].id, "driver_oauth_token": dresults[0].oauth_token, "socket_id": socket_user_id}
									;
									//console.log(users);
									
										
										//socket.to(results[0].socket_id).emit('vendor_tracking_driver', res);
										io.to(uresults[0].socket_id).emit('driver_available_check', users);
										
										console.log('Updated ride emit');
										
										pool.query("UPDATE kapp_rides SET emit_status = 1 WHERE id = '"+results[0].id+"' " , function (error){
											if(error){
												//console.log('Not Updated Ride driver 1');
												return;
											}
											console.log('Updated ride driver 1');
										});
									
									
								}else{
									socket_user_id = 0;
									console.log('ride driver socket empty data');
									pool.query("UPDATE kapp_rides SET emit_status = 0 WHERE id = '"+results[0].id+"' " , function (error){
											if(error){
												//console.log('Not Updated ride driver 2');
												return;
											}
											//console.log('Updated ride driver 2');
										});
								}
								
							}
						});
						
						
						
						
					}
				});
				
			}
			
		});
	});
	
	socket.on('vendor_tracking', (data, callback) => {
		console.log('Socket Vendor Tracking');
		//console.log('Vendor Tracking ');
		var user_id = data.user_id;
		if(user_id == ''){
			//console.log('Mandotory fields missing');
			return;
		}else{
			pool.query("SELECT u.id, u.oauth_token, us.socket_id FROM kapp_users AS u LEFT JOIN kapp_user_socket AS us ON us.user_id = u.id WHERE u.id = '"+user_id+"' ", function (error, results, fields){
				if(results.length == 0){
					//console.log('Vendor Tracking Empty');
					var responseData = 'vendor_tracking_empty';
					return callback(responseData);
				}else{
					if(results[0].socket_id != null){
						//var bimage = "http://13.233.9.134/assets/uploads/";
						
						pool.query("SELECT u.id, u.first_name, dc.taxi_id, dc.mode, dc.current_latitude, dc.current_longitude, t.name as taxi_name, tt.name as taxi_type, CONCAT('http://13.233.9.134/assets/uploads/', '', tt.mapcar) as mapcar FROM kapp_users AS u JOIN kapp_driver_current_status AS dc ON dc.driver_id = u.id  AND dc.allocated_status != 2 AND dc.mode != 0 LEFT JOIN kapp_user_profile AS up ON up.user_id = u.id 	LEFT JOIN kapp_taxi AS t ON t.id = dc.taxi_id 						LEFT JOIN kapp_taxi_type AS tt ON tt.id = t.type WHERE u.parent_id = '"+user_id+"' ", function (error, res, fields){
							if(res.length == 0){
								//console.log('Vendor Tracking Data');
								var responseData = 'vendor_tracking_data';
								
								io.to(results[0].socket_id).emit('vendor_tracking_driver', res);
								return callback(responseData);
							}else{
								//console.log('Vendor Tracking Empty');
								var responseData1 = 'vendor_tracking_empty';
								return callback(responseData1);
							}
						});
						
					}else{
						//console.log('Vendor Tracking Empty');
						var responseData2 = 'vendor_tracking_empty';
						return callback(responseData2);
					}
				}
			});
		}
	});
	
	socket.on('server_other_login', function(data){
		console.log('Socket Other Login user');
		io.to(data.socket_id).emit('other_login', data);
	});
	
	socket.on('user_socket', (data, callback) => {
		console.log('Socket User');
		//console.log('User Socket');
		var user_id = data.user_id;
		var device_imei = data.device_imei;
		var user_type = data.user_type;
		var device_token = data.device_token;
		var socket_id = socket.id;	
			
		var table_name = 'kapp_users';
		
		if(user_id == '' || device_token == '' || user_type == ''){
			//console.log('Mandotory fields missing');
			return;
		}else{
			
			if(user_type == 2){
				
				pool.query("UPDATE kapp_driver_current_status SET is_connected = 1 WHERE  driver_id="+user_id+" ORDER BY id DESC LIMIT 1  ");
				
				console.log("Driver mode update ");	
			}

			 pool.query("SELECT u.id, u.oauth_token, u.devices_imei, u.mode, us.socket_id FROM "+table_name+" AS u LEFT JOIN kapp_user_socket AS us ON us.user_id = u.id AND user_type = "+user_type+" WHERE u.oauth_token = '"+device_token+"' ", function (error, results, fields){
				
				if(error){
					//console.log('Data is empty');
					return;
				}
				//console.log('user data socket: '+results);
				
				if(results.length == 0){
					//console.log('empty user');
					var responseData = 'user_truncate';
					return callback(responseData);
				}else{
					//console.log('not empty user');
					if(results[0].socket_id != null){
						
						if(results[0].devices_imei != device_imei){
							var responseData_notmatch = 'user_truncate';
							return callback(responseData_notmatch);
						}else{
							pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE  id="+user_id+"  ");
							pool.query("UPDATE kapp_user_socket SET device_imei = '"+device_imei+"',device_token = '"+device_token+"',socket_id = '"+socket_id+"' WHERE user_id = '"+user_id+"'  AND user_type = '"+user_type+"' " , function (error){
								if(error){
									//console.log('Not Updated');
									return;
								}
								//console.log('Updated');
							});
						}
						
						
					}else{
						pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE  id="+user_id+"  ");
						pool.query("INSERT INTO kapp_user_socket  (user_id, user_type, socket_id, device_imei, device_token) VALUES ('"+user_id+"', '"+user_type+"', '"+socket_id+"', '"+device_imei+"', '"+device_token+"') " , function (error){
							if(error){
								//console.log('Not Insert');
								return;
							}
							//console.log('Inserted');
						});
					}
					
					pool.query("UPDATE kapp_users SET device_imei = '"+device_imei+"' WHERE user_id = '"+user_id+"'  " , function (error){
							if(error){
								//console.log(error);
								//console.log(this.sql);
								//console.log('User Not Updated');
								return;
							}
							//console.log('User Updated');
						});
						
						
					var responseUData = results[0].mode;
					return callback(responseUData);
				}
				
			});
		}
	});
	
	socket.on('notification', function(data){
		console.log('Socket Notification');
		var test;
		var test1 = 37.772886;
		var test2 = -122.423771;
		var test3 = 37.871601;
		var test4 = -122.269104;
		var origin1 = ''+test1+','+test2+'';
		var destination1 = ''+test3+','+test4+'';
		distance.get(
		{
		  index: 1,
		  origin: origin1,
		  destination: destination1
		},
		function(err, res) {
		  console.log(res.distanceValue);
		});
 		
		//var fromlat = [13.1323152, 13.131846, 13.1314802];
		//var fromlng = [80.1956207, 80.1987644, 80.2006825];
		//var tolat = [13.1318739, 13.1315293, 13.127505];
		//var tolng = [80.1984593, 80.2005381, 80.2015351];
		
		//var dd;
		//var dd;
		/*distance.get(
		{
		  origins: ['13.1323152,80.1956207'],
		  destinations: ['13.1318739, 80.1984593']
		},function(err, data) {
		  if (err){ return console.log(err);}else{
		 	dd = data[0].distanceValue;
			console.log(dd);
		 	 console.log(data[0].distanceValue);
		  }
		});*/
		/*var Oslo = {
	  lat: 13.1323152,
	  lon: 80.1956207
	};
	var Berlin = {
	  lat: 13.1318739,
	  lon: 80.1984593
	};
	var OsloToBerlin = Distance.between(Oslo, Berlin);
console.log(OsloToBerlin);*/
		//d = dd['distanceValue'];
		/*function(err, data) {
		  if (err) return console.log(err);
		 d = data.distanceValue;
		  console.log(data);
		});	*/
		
		socket.emit('notification', data);
	});
	
	socket.on('server_reached_destination', function(data){
		console.log('Socket Reached Destination');
		//console.log('Server Reached Destination');
		console.log(data.socket_id);
		io.to(data.socket_id).emit('driver_reached_destination', data);
	});
	
	socket.on('server_ride_complete', function(data){
		console.log('Socket Ride Complete');
		//console.log('Server Ride Complete');
		io.to(data.socket_id).emit('driver_ride_complete', data);
	});
	
	socket.on('server_ride_cancel', function(data){
		console.log('Socket Ride Cancel');
		//console.log('Server Ride Cancel');
		io.to(data.socket_id).emit('driver_ride_cancel', data );
	});
	
	socket.on('server_chat_join', function(data){
		console.log('Socket Chat Join');
		//console.log('Server Chat Join');
		socket.to(data.driver_socket_id).emit('driver_user_join', data.driver_name );
		socket.to(data.customer_socket_id).emit('customer_user_join', data.customer_name );
	});
	
	socket.on('server_chat_complete', function(data){
		console.log('Socket Chat Complete');
		//console.log('Server Chat Complete');
		socket.to(data.driver_socket_id).emit('driver_chat_complete', data.driver_name );
		socket.to(data.customer_socket_id).emit('customer_chat_complete', data.customer_name );
	});
	
	socket.on('server_booking_accept', function(data){
		console.log('Socket Driver Accept Ride');
		//console.log('Server Booking Accepts');
		console.log(data.socket_id);
		console.log(data);
		io.to(data.socket_id).emit('driver_accept_ride', data );
	});
	
	socket.on('server_not_accept_driver', function(data){
		console.log('Socket Driver Not Accept Ride');
		//console.log('Server Booking Accepts');
		console.log(data.socket_id);
		console.log(data);
		io.to(data.socket_id).emit('driver_not_accept_ride', data );
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
		io.to(data.socket_id).emit('driver_available_check', data);
	});
	
	socket.on('new_message', function(data){
		console.log('Socket Chat New Message');
		//console.log('New Message');
		var new_msg = data.msg;
	    var ride_id = data.ride_id;
		var user_type = data.user_type;
		var today = new Date();
		var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
		var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
		var dateTime = date+' '+time;
		if(new_msg == '' || ride_id == '' || user_type == ''){
			//console.log('Mandotory fields missing');
			return;
		}else{
			pool.query("SELECT r.id, r.driver_id, r.customer_id, d.socket_id AS driver_socket_id, c.socket_id AS customer_socket_id FROM kapp_rides AS r LEFT JOIN kapp_user_socket AS d ON d.user_id = r.driver_id LEFT JOIN kapp_user_socket AS c ON c.user_id = r.customer_id  WHERE r.id = '"+ride_id+"' ", function (error, results, fields){
				if(error){
					//console.log('Data is empty');
					return;
				}
				
				if(user_type == 1){
					pool.query("INSERT INTO kapp_chat_ride  (user_id, user_type, ride_id, chat_message, chat_date) VALUES ('"+results[0].customer_id+"', '"+user_type+"', '"+ride_id+"', '"+new_msg+"', '"+dateTime+"') ");
					socket.to(results[0].driver_socket_id).emit('recived_message', data );
				}else if(user_type == 2){
					pool.query("INSERT INTO kapp_chat_ride  (user_id, user_type, ride_id, chat_message, chat_date) VALUES ('"+results[0].driver_id+"', '"+user_type+"', '"+ride_id+"', '"+new_msg+"', '"+dateTime+"') ");
					socket.to(results[0].customer_socket_id).emit('recived_message', data );	
				}
				
			});
		}
	});
		
	socket.on('driver_location', function(data){
		console.log('Socket Driver Location');
		//console.log('driver_location');
		var oauth_token = data.oauth_token;
		var latitude = data.latitude;
		var longitude = data.longitude;
		//console.log(latitude);
		//console.log(longitude);
		
		if(oauth_token == '' || latitude == '' || longitude == ''){
			//console.log('Mandotory fields missing');
			return;
		}else{
			
			
			
			//io.sockets.emit('notification', 'Hello');
			//AND dcs.is_connected = 1 AND dcs.mode = 3 AND dcs.allocated_status = 1
			
			//"SELECT d.id, dcs.current_latitude, dcs.current_longitude, r.start_lat, r.start_lng, r.end_lat, r.end_lng, r.customer_id, r.id AS ride_id, df.location, df.final_distance, df.final_distance_total, r.status, us.socket_id, us.id AS usid FROM kapp_users AS d LEFT JOIN kapp_driver_current_status dcs ON dcs.driver_id = d.id  LEFT JOIN kapp_rides AS r ON r.driver_id = d.id AND (r.status = 2 OR  r.status = 3) LEFT JOIN kapp_driver_frequency AS df ON df.ride_id = r.id AND df.driver_id = d.id LEFT JOIN kapp_user_socket AS us ON us.user_id = r.customer_id AND us.user_type = 1 WHERE d.oauth_token = '"+oauth_token+"' ORDER BY usid DESC LIMIT 1"
						
			pool.query("SELECT d.id, dcs.current_latitude as cu_latitude, dcs.current_longitude as cu_longitude,  r.start_lat, r.start_lng, r.end_lat, r.end_lng, r.customer_id, r.id AS ride_id, df.location, df.final_distance, df.final_distance_total, r.status, us.socket_id, us.id AS usid FROM kapp_users AS d JOIN kapp_driver_current_status dcs ON dcs.driver_id = d.id AND dcs.is_connected = 1 AND dcs.mode = 3 AND dcs.allocated_status = 1  JOIN kapp_rides AS r ON r.driver_id = d.id AND (r.status = 2 OR  r.status = 3) JOIN kapp_driver_frequency AS df ON df.ride_id = r.id AND df.driver_id = d.id JOIN kapp_user_socket AS us ON us.user_id = r.customer_id AND us.user_type = 1 WHERE d.oauth_token = '"+oauth_token+"' ORDER BY usid DESC LIMIT 1", function (error, results, fields){
				if(error){
					
					//console.log('Data is empty');
					return;
				}
				
				if(results.length == 0){
					
					console.log('Data is empty');
					
					
					return;
				}else{
				
				
							
							
					if(results[0].ride_id != null){
						
						
						
						if(results[0].location != null){
							
							//schedule.scheduleJob('0 * * * * *', function(){
								
								var object2 = [[results[0].location], [latitude, longitude, results[0].status]]; 
								
								var dfu_test1 = results[0].cu_latitude;
								var dfu_test2 = results[0].cu_longitude;
								var dfu_test3 = latitude;
								var dfu_test4 = longitude;
								var dfu_origin2 = ''+dfu_test1+','+dfu_test2+'';
								var dfu_destination2 = ''+dfu_test3+','+dfu_test4+'';
								
								console.log(dfu_origin2);
							console.log(dfu_destination2);
							
							
							if(dfu_test1 != null && dfu_test2 != null){
								console.log('up');
								distance.get(
								{
								  index: 1,
								  origin: dfu_origin2,
								  destination: dfu_destination2
								},
								function(err, res) {
								  console.log(res);
								  var distanceValue2 = [[results[0].final_distance_total], [res.distanceValue]];
								  pool.query("UPDATE kapp_driver_frequency SET location = '"+object2+"', final_distance_total = '"+distanceValue2+"'  WHERE ride_id = '"+results[0].ride_id+"' AND driver_id = '"+results[0].id+"'  " );
								});
							}else{
								console.log('upppp');
								pool.query("UPDATE kapp_driver_frequency SET location = '"+object2+"'  WHERE ride_id = '"+results[0].ride_id+"' AND driver_id = '"+results[0].id+"'  " );
							}
							
								//pool.query("UPDATE kapp_driver_frequency SET location = '"+object2+"'  WHERE ride_id = '"+results[0].ride_id+"' AND driver_id = '"+results[0].id+"'  " );
							
							//});
							
							//console.log("Ride Updated");
						}else{
							var object1 = [[latitude, longitude, results[0].status]]; 
							
							var df_test1 = results[0].cu_latitude;
							var df_test2 = results[0].cu_longitude;
							var df_test3 = latitude;
							var df_test4 = longitude;
							var df_origin1 = ''+df_test1+','+df_test2+'';
							var df_destination1 = ''+df_test3+','+df_test4+'';
							
							console.log(df_origin1);
							console.log(df_destination1);
							
							if(df_test1 != null && df_test2 != null){
								console.log('in'); 
								distance.get(
								{
								  index: 1,
								  origin: df_origin1,
								  destination: df_destination1
								},
								function(err, res) {
								  console.log(res);
								  var distanceValue1 = res.distanceValue;
								  pool.query("INSERT INTO kapp_driver_frequency  (driver_id, ride_id, location, final_distance_total) VALUES ('"+results[0].id+"', '"+results[0].ride_id+"', '"+object1+"', '"+distanceValue1+"') ");
								});
							}else{
								console.log('innn'); 
								pool.query("INSERT INTO kapp_driver_frequency  (driver_id, ride_id, location, final_distance_total) VALUES ('"+results[0].id+"', '"+results[0].ride_id+"', '"+object1+"', 0) ");
							}
							
							
							//console.log("Ride Insert");
						}		
									
						pool.query("UPDATE kapp_driver_current_status SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE allocated_status = 1 AND driver_id="+results[0].id );
						pool.query("UPDATE taxi SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE driver_id="+results[0].id );
						pool.query("UPDATE users SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE id="+results[0].id );
						//console.log("Location Updated");
						//console.log("Location Ride Updated");
						
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
						
						io.to(results[0].socket_id).emit('get_driver_location', { ride_id: cus_ride_id, lat: cus_lat, lng: cus_lng, pickup_lat: cus_pickup_lat, pickup_lng: cus_pickup_lng, drop_lat: cus_drop_lat, drop_lng: cus_drop_lng, actual_lat: cus_actual_lat, actual_lng: cus_actual_lng });
						
						var admin_driver_id = results[0].id;
						var admin_ride_id = results[0].ride_id;
						var admin_lat = latitude;
						var admin_lng = longitude;
						
						
						
						io.sockets.emit('admin_drivers', {'admin_driver_id': admin_driver_id, 'admin_ride_id': admin_ride_id, 'admin_lat': admin_lat, 'admin_lng': admin_lng });
						
					}else{
						var admin_driver_id1= results[0].id;
						var admin_ride_id1 = 0;
						var admin_lat1 = latitude;
						var admin_lng1 = longitude;
						io.sockets.emit('admin_drivers_location', {'admin_driver_id': admin_driver_id1, 'admin_ride_id': admin_ride_id1, 'admin_lat': admin_lat1, 'admin_lng': admin_lng1 });
						
						pool.query("UPDATE kapp_driver_current_status SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE allocated_status = 1 AND driver_id="+results[0].id );
						pool.query("UPDATE taxi SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE driver_id="+results[0].id );
						pool.query("UPDATE users SET current_latitude = '"+latitude+"',current_longitude = '"+longitude+"' WHERE id="+results[0].id );
						//console.log("Location Updated");
					}
					
					
				}
				
				
				
			});
		}
	});
	
	socket.on('admin_drivers_location', function(data){
		console.log('Socket Admin');
		console.log(data);
		//socket.emit('notification', data);
	});
	
	socket.on('disconnect', function(reason) {
		 console.log('User 1 disconnected because '+reason);
		console.log('Socket Disconnect');
		console.log(socket.id);
		//console.log(socket.id);
      //console.log('Got disconnect!');
	  pool.query("SELECT * FROM kapp_user_socket WHERE socket_id = '"+socket.id+"' ORDER BY id DESC LIMIT 1", function (error, results, fields){
		  if(error){
					
			//console.log('Data is empty');
			return;
		}
		if(results.length != 0){
			//console.log(results);
			//console.log(results[0].user_id);
			if(results[0].user_id != null){
				//console.log('inside if');
				pool.query("UPDATE kapp_driver_current_status SET is_connected = 0 WHERE  driver_id="+results[0].user_id+" ORDER BY id DESC LIMIT 1 " );
				//console.log("Driver mode update ");
			}else{
				//console.log('not inside if');
			}
		}else{
			//console.log('user socket data is empty');
		}
		  
	  });
	  
   });
});
   
   
 
