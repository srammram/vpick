<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Framework routes
$route['default_controller'] = 'main';
$route['sos'] = 'main/sos';


$route['privacy_policy'] = 'main/privacy_policy';
$route['aboutus'] = 'main/aboutus';
$route['drivewithus'] = 'main/drivewithus';
$route['franchisee'] = 'main/franchisee';
$route['book_ride'] = 'main/book_ride';
$route['faq'] = 'main/faq';
$route['contact'] = 'main/contact';
$route['news'] = 'main/news';
$route['terms_conditions'] = 'main/terms_conditions';
$route['health_report'] = 'main/health_report';
$route['activity_report'] = 'main/activity_report';
$route['health_report'] = 'main/health_report';

$route['help'] = 'main/help';
$route['login'] = 'main/login';
$route['success'] = 'main/success';
$route['404_override'] = 'notify/error_404';
$route['translate_uri_dashes'] = TRUE;

//$route['admin'] = 'admin/welcome';
$route['admin/login'] = 'admin/auth/login';
$route['admin/login/(:any)'] = 'admin/auth/login/$1';
$route['admin/logout'] = 'admin/auth/logout';
$route['admin/logout/(:any)'] = 'admin/auth/logout/$1';
?>



