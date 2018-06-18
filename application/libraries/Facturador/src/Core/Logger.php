<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 6/14/2018
 * Time: 12:30 PM
 */

namespace Facturador\Core;


class Logger
{
    public static function write($level, $msg)
    {
        $msg = strtoupper($level)." - ".date('Y-m-d H:i:s').' --> '.$msg;
        $msg .= "\n";
        error_log($msg, 3, __DIR__ . '/../../logs/' . date('Y-m-d') . '.log');
    }
}