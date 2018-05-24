<?php
require_once './RucSunat.php';
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/11/2018
 * Time: 10:17 AM
 */

$ruc_sunat = new RucSunat();
var_dump($ruc_sunat->consultarRuc('15602744393'));