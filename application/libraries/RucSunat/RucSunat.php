<?php
require_once(__DIR__ . "/lib/curl.php");
require_once(__DIR__ . "/lib/sunat.php");

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/11/2018
 * Time: 10:12 AM
 */
class RucSunat
{

    public function consultarRuc($ruc)
    {
        error_reporting(0);
        ini_set('display_errors', 0);
        $sunat = new \RucSunat\Sunat(true, true);
        if (strlen($ruc) == 11 && $sunat->valid($ruc)) {
            $result = $sunat->search($ruc);
            if ($result['success'])
                return $result['result'];
        }
        return false;
    }
}