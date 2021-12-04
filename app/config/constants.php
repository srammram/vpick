<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
define('EXIT_SUCCESS', 0); // no errors
define('EXIT_ERROR', 1); // generic error
define('EXIT_CONFIG', 3); // configuration error
define('EXIT_UNKNOWN_FILE', 4); // file not found
define('EXIT_UNKNOWN_CLASS', 5); // unknown class
define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
define('EXIT_USER_INPUT', 7); // invalid user input
define('EXIT_DATABASE', 8); // database error
define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

define('CUSTOMER', 3); // customer role id
define('SALE', 5); // sale role id
define('KITCHEN', 6); // kitchen role id
define('WAITER', 7); // waiter role id
define('CASHIER', 8); // cashier role id
define('DELIVERYPERSON', 9); // delivery person role id
define('PRINTER_SOCKET', 'ws://127.0.0.1:6441');
define('PRINTER_HOST', '127.0.0.1');
define('PRINTER_PORT', '6441');

//define( 'API_FIREBASE_ACCESS_KEY', 'AIzaSyAckibIMT_vgf0R3Y7U_SnDKg6nWi-BONY' );#for push notification
define( 'API_FIREBASE_ACCESS_KEY', 'AIzaSyAQggnzNxn0UFplcovbvhXQPsA8-zUsDk8' );#for push notification
define( 'POST_SECURE', 'YZ23tlyEoGVfz3TY5wGsB6Hid9gdd0WnQAuFs2D9Zcltt8HQH294QR3XRV7gXEdDtrzO9YuO03G4GYQhr0qIzPVrTOsQa5UfYqDS' );#check oauth key
define('ENCRYPT_API_KEY', 'Jy8fXejLdN1HPwpNIWfGk4rVd2s683fISwsSkFCNodmjIKWDRP6b5z90xOo7bPdiyrQVCQv2B5yniL1EfVavxfiPCK1UQta09iBpVk7AqBb2M9DbiiyCEoR4n2iGN4qomPiVsO10AeyGpOTVMUn34L');


define('STRIPE_KEY_ID', 'pk_test_maagmgE7CTbjFsoXLEUObrKm00fL6by7fG');
define('STRIPE_KEY_SECRET', 'sk_test_lOLgjctdpeOc132LkIIlgYBu0099Ts6rv1');

define('RAZOR_KEY_ID', 'rzp_test_fk5NJi3Xs7ZEhM');
define('RAZOR_KEY_SECRET', 'EM4am0FxCkwy9qBWxq1ix4Cx');

//K-Payment
define('KPAYMENT_KEY_ID', 'pkey_test_20370ZIGzMnyYXT59pGmKvvwNyHU1XbE6qx06');
define('KPAYMENT_KEY_SECRET', 'skey_test_20370aJQiM7h5ep0V8nbjDzAg1rwxMTLaPx9h');

define('ICICI_KEY_ID', 'l7xx8310349a8b8042c9abb8b0271f86fc72');

//test

define('CCAVENU_MERCHANT_ID', '744402');
define('CCAVENU_ACCESS_CODE', 'AVMI61IL16AB78IMBA');
define('CCAVENU_WORKING_KEY', '109EB8A79470DB1C6CAC941D013E72BC');

//live
//define('RAZOR_KEY_ID', 'rzp_live_17VlkaSCFQoo6s');
//define('RAZOR_KEY_SECRET', '2rsPEhITjABpquCNS2yAcBkd');
