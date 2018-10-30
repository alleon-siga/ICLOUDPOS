<?php

class MY_Log extends CI_Log
{

    // Sobrescribo para que el log guarde la base de datos de donde se origino el error
    function write_log($level, $message)
    {
        $_db = DATABASE_HOST != false ? DATABASE_HOST : 'localhost';
        parent::write_log($level, 'DATABASE: ' . $_db . ' | ' . $message);
    }
}