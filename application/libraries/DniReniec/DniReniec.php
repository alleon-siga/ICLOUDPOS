<?php
require(__DIR__ . "/lib/curl.php");
require(__DIR__ . "/lib/solver.php");
require(__DIR__ . "/lib/reniec.php");

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/11/2018
 * Time: 10:30 AM
 */
class DniReniec
{
    public function consultarDni($dni)
    {
        $reniec = new \Reniec\Reniec();

        if (strlen($dni) == 8 && $dni != "") {
            $result = $reniec->search($dni);
            if ($result->success)
                return (array)$result->result;
        }
        return false;
    }
}