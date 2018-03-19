<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


define('COMPLETADO',		'COMPLETADO');
define('ESPERA',		'EN ESPERA');

define('NOTAVENTA',		'NOTA DE PEDIDO');
define('BOLETAVENTA',		'BOLETA DE VENTA');
define('FACTURA',		'FACTURA');

define('DONACION',		'DONACION');
define('COMPRA',		'COMPRA');


define('PESABLE',		'PESABLE');
define('MEDIBLE',		'MEDIBLE');

// DEV para desarrollo y PROD para produccion
define('ENV', 'DEV');
define('MONEDA_DEFECTO', '1029');
//define('MONEDA',		'S/.');
//define('DOLAR',		'$');

define('IMPUESTO', 18);
/* End of file constants.php */
/* Location: ./application/config/constants.php */


/******CONFIGURACIONES************/

define('EMPRESA_NOMBRE',		'EMPRESA_NOMBRE');
define('EMPRESA_DIRECCION',		'EMPRESA_DIRECCION');
define('EMPRESA_TELEFONO',		'EMPRESA_TELEFONO');
define('DATABASE_IP',		'DATABASE_IP');
define('DATABASE_NAME',		'DATABASE_NAME');
define('DATABASE_USERNAME',		'DATABASE_USERNAME');
define('DATABASE_PASWORD',		'DATABASE_PASWORD');


define('NOMBRE_EXISTE',		'El nombre ingresado ya existe');
define('MODELO_EXISTE',		'El Modelo de este producto ya existe');
define('MODELO_ACTIVAR',		'Modelo esta configurado como campo unico. Por favor activelo en columnas');
define('CODIGO_EXISTE',		'El Codigo Interno ya existe');
define('CEDULA_EXISTE',		'La identificacion ingresada ya existe');
define('USERNAME_EXISTE',		'El username ingresado ya existe');
define('RAZON_SOCIAL_EXISTE',		'La Razon Social ingresada ya existe');


define('CREDITO_DEBE', 'PagoPendiente');

