<?php
/**
 * Created by PhpStorm.
 * User: Jhainey
 * Date: 04/04/2015
 * Time: 0:59
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
    function __construct()
    {
        parent::__construct();
    }
}