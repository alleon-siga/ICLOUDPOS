<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 6/14/2018
 * Time: 12:09 PM
 */

namespace Facturador\Core;

class Config
{
    public static function get($key)
    {
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        if (isset($config[$key]))
            return $config[$key];
        return null;
    }
}