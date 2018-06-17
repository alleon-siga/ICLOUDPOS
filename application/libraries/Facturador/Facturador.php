<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/colecciones.php';

use Facturador\Comprobantes\Factura;
use Facturador\Comprobantes\Boleta;
use Facturador\Comprobantes\NotaCredito;
use Facturador\Comprobantes\NotaDebito;
use Facturador\Emisor;

class Facturador
{

    protected $emisor_data;
    protected $response;

    public function __construct($emisor)
    {
        $this->emisor_data = $emisor;
    }

    public function crearComprobante($tipo, $cabecera, $detalles)
    {
        $validar = \Facturador\Validador::validarComprobante($tipo, $cabecera, $detalles);
        if ($validar !== FALSE) {
            return $validar;
        }

        $cabecera = \Facturador\Validador::prepareCabecera($cabecera);
        $detalles = \Facturador\Validador::prepareDetalles($detalles);

        switch ($tipo) {
            case TIPO_COMPROBANTE::$FACTURA : {
                $comprobante = new Factura($this->emisor_data);
                return $comprobante->crearXml($cabecera, $detalles);
            }
            case TIPO_COMPROBANTE::$BOLETA : {
                $comprobante = new Boleta($this->emisor_data);
                return $comprobante->crearXml($cabecera, $detalles);
            }
            case TIPO_COMPROBANTE::$NOTA_CREDITO : {
                $comprobante = new NotaCredito($this->emisor_data);
                return $comprobante->crearXml($cabecera, $detalles);
            }
            case TIPO_COMPROBANTE::$NOTA_DEBITO : {
                $comprobante = new NotaDebito($this->emisor_data);
                return $comprobante->crearXml($cabecera, $detalles);
            }
            default: {
                \Facturador\Core\Logger::write('error', '-1: Comprobante no valido. ' . $tipo);
                return array(
                    'CODIGO' => '-1',
                    'MENSAJE' => 'Comprobante no valido. ' . $tipo,
                    'HASH_CPE' => NULL
                );
            }
        }
    }

    public function enviarComprobante($tipo_documento, $data)
    {
        $emisor = new Emisor($this->emisor_data);
        $file_name = $emisor->get('NRO_DOCUMENTO') . '-' . $tipo_documento . '-' . $data['NUMERO_DOCUMENTO'];
        return $emisor->sendBill($file_name);
    }

    public function enviarBaja($cabecera, $detalles)
    {

        $baja = new \Facturador\ComunicacionBaja($this->emisor_data);
        return $baja->enviarBaja($cabecera, $detalles);
    }

}