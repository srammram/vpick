
<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author    : Sheik
 *  Email     : info@srampos.com
 *  For       : SRAM POS
 *  Web       : http://srammram.com.com
 *  ==============================================================================
 */


require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Tcpdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
}

