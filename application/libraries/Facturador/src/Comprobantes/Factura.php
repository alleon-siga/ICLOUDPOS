<?php

namespace Facturador\Comprobantes;

use Facturador\Core\Comprobante;

class Factura extends Comprobante
{
    public function __construct($emisor)
    {
        parent::__construct($emisor);
    }

    public function crearXml($cabecera, $detalles)
    {
        //Crea el Xml raiz de la factura
        $root = $this->xml->createElement('Invoice');
        $root->setAttribute("xmlns", "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2");
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $root->setAttribute("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
        $root->setAttribute("xmlns:cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $root->setAttribute("xmlns:cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $root->setAttribute("xmlns:ccts", "urn:un:unece:uncefact:documentation:2");
        $root->setAttribute("xmlns:ds", "http://www.w3.org/2000/09/xmldsig#");
        $root->setAttribute("xmlns:ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $root->setAttribute("xmlns:qdt", "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2");
        $root->setAttribute("xmlns:udt", "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $root = $this->xml->appendChild($root);

        // Creacion del UBLExtensions para firmar el comprobante
        $ubl_extension = $this->xml->createElement('ext:UBLExtensions');
        $firma = $this->xml->createElement('ext:UBLExtension');
        $firma->appendChild($this->xml->createElement('ext:ExtensionContent'));
        $root->appendChild($firma);
        $ubl_extension->appendChild($firma);
        $root->appendChild($ubl_extension);

        //Version del UBL
        $root->appendChild($this->xml->createElement('cbc:UBLVersionID', '2.1'));

        //Version de la estructura del Documento
        $root->appendChild($this->xml->createElement('cbc:CustomizationID', '2.0'))
            ->setAttribute('schemeAgencyName', "PE:SUNAT");

        /* Tipo de operacion para identificar la transaccion
         * catálogo  N° 51 del  Anexo  8
         * 0101 => Venta Interna (Por defecto)
         * 0102 => Exportacion
         * 0103 => No Domiciliados
         * 0104 => Venta Interna - Anticipos
         * 0105 => Venta Itinerante
         * 0106 => Factura Guia
         * 0107 => Venta Arroz Pilado (IVAP)
         * 0108 => Factura Comprobante de Percepcion
         * 0110 => Factura Guia Remitente
         * */
        $profile = $this->xml->createElement(
            'cbc:ProfileID',
            isset($cabecera['TIPO_OPERACION']) ? $cabecera['TIPO_OPERACION'] : "0101");
        $profile->setAttribute('schemeName', "Tipo de Operacion");
        $profile->setAttribute('schemeAgencyName', "PE:SUNAT");
        $profile->setAttribute('schemeURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51");
        $root->appendChild($profile);


        /* Numero de Documento
         * FA01-10 => Empieza con F seguido de tres caracteres alfanumericos, guion y un numero correlativo
         * */
        $root->appendChild($this->xml->createElement('cbc:ID', $cabecera['NUMERO_DOCUMENTO']));

        //Fecha y Hora de emision
        $root->appendChild($this->xml->createElement('cbc:IssueDate', $cabecera['FECHA_EMISION']));
        $root->appendChild($this->xml->createElement('cbc:IssueTime', isset($cabecera['HORA_EMISION']) ? $cabecera['HORA_EMISION'] : date("H:i:s")));

        // Fecha de vencimiento
        if (isset($cabecera['FECHA_VENCIMIENTO']))
            $root->appendChild($this->xml->createElement('cbc:DueDate', $cabecera['FECHA_VENCIMIENTO']));

        //Tipo de documento => 01 - FACTURA
        $InvoiceTypeCode = $this->xml->createElement('cbc:InvoiceTypeCode', $cabecera['TIPO_DOCUMENTO']);
        $InvoiceTypeCode->setAttribute('listAgencyName', "PE:SUNAT");
        $InvoiceTypeCode->setAttribute('listName', "Tipo de Documento");
        $InvoiceTypeCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01");
        $InvoiceTypeCode->setAttribute('listID', isset($cabecera['TIPO_OPERACION']) ? $cabecera['TIPO_OPERACION'] : "0101");
        $InvoiceTypeCode->setAttribute('name', "Tipo de Operacion");
        $InvoiceTypeCode->setAttribute('listSchemeURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51");
        $root->appendChild($InvoiceTypeCode);

        // Tipo de Moneda
        // ISO 4217 - Currency
        $DocumentCurrencyCode = $this->xml->createElement('cbc:DocumentCurrencyCode', $cabecera['CODIGO_MONEDA']);
        $DocumentCurrencyCode->setAttribute('listID', "ISO 4217 Alpha");
        $DocumentCurrencyCode->setAttribute('listName', "Currency");
        $DocumentCurrencyCode->setAttribute('listAgencyName', "United Nations Economic Commission for Europe");
        $root->appendChild($DocumentCurrencyCode);

        //Referencia de la firma digital
        $firma_ref = $this->xml->createElement('cac:Signature');
        $firma_ref->appendChild($this->xml->createElement('cbc:ID', $cabecera['NUMERO_DOCUMENTO']));

        $sign_party = $this->xml->createElement('cac:SignatoryParty');
        $sign_party->appendChild($this->xml->createElement('cac:PartyIdentification'))
            ->appendChild($this->xml->createElement('cbc:ID', $this->emisor->get('NRO_DOCUMENTO')));

        $sign_party->appendChild($this->xml->createElement('cac:PartyName'))
            ->appendChild($this->xml->createElement('cbc:Name'))
            ->appendChild($this->xml->createCDATASection($this->emisor->get('RAZON_SOCIAL')));

        $firma_ref->appendChild($sign_party);

        $ext_ref = $this->xml->createElement('cac:DigitalSignatureAttachment');
        $ext_ref->appendChild($this->xml->createElement('cac:ExternalReference'))
            ->appendChild($this->xml->createElement('cbc:URI', '#' . $cabecera['NUMERO_DOCUMENTO']));
        $firma_ref->appendChild($ext_ref);

        $root->appendChild($firma_ref);

        //Datos del emisor
        $root->appendChild($this->createEmisorXml());

        //Datos del cliente
        $root->appendChild($this->createClienteXml($cabecera));

        /* DESCUENTO GLOBALES
         * ChargeIndicator: Dado que no es un cargo, se debe asignar el indicador a false
         * AllowanceChargeReasonCode (Catálogo No. 53: Códigos de cargos o descuentos)
         * 00 => Otros descuentos (Por defecto)
         * 50 => Otros cargos
         * 51 => Percepcion venta interna
         * 52 => Percepcion a la adquisicion de combustible
         * 53 => Percepcion realizada al agente de percepcion con tasa especial
         * 54 => Otros cargos relacionado al servicio
         * 55 => Otros cargos no relacionados al servicio
         * MultiplierFactorNumeric: Porcentaje del descuento en numeros decimales para el 5% se ingresa el 0.05 (Defecto 0.00)
         * Amount: Importe del descuento global
         * BaseAmount: Importe sobre el cual se esta aplicando el descuento global
         * */
        $AllowanceCharge = $this->xml->createElement('cac:AllowanceCharge');
        $AllowanceCharge->appendChild($this->xml->createElement('cbc:ChargeIndicator', 'false'));

        $AllowanceChargeReasonCode = $this->xml->createElement(
            'cbc:AllowanceChargeReasonCode',
            isset($cabecera['CARGO_DESCUENTO_CODIGO']) ? $cabecera['CARGO_DESCUENTO_CODIGO'] : "00");
        $AllowanceChargeReasonCode->setAttribute('listName', "Cargo/descuento");
        $AllowanceChargeReasonCode->setAttribute('listAgencyName', "PE:SUNAT");
        $AllowanceChargeReasonCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo53");
        $AllowanceCharge->appendChild($AllowanceChargeReasonCode);

        $AllowanceCharge->appendChild($this->xml->createElement(
            'cbc:MultiplierFactorNumeric',
            isset($cabecera['PORCIENTO_DESCUENTO_CODIGO']) ? $cabecera['PORCIENTO_DESCUENTO_CODIGO'] : "0.00"));

        $AllowanceCharge->appendChild($this->xml->createElement(
            'cbc:Amount',
            isset($cabecera['IMPORTE_DESCUENTO_CODIGO']) ? $cabecera['IMPORTE_DESCUENTO_CODIGO'] : "0.00"))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $AllowanceCharge->appendChild($this->xml->createElement(
            'cbc:BaseAmount',
            isset($cabecera['BASE_DESCUENTO_CODIGO']) ? $cabecera['BASE_DESCUENTO_CODIGO'] : "0.00"))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $root->appendChild($AllowanceCharge);


        /* Monto totales de impuesto
         * Corresponde al importe total de impuestos ISC, IGV e IVAP de Corresponder
         * TaxAmount: Sumatoria total de los impuestos
         * */
        $TaxTotal = $this->xml->createElement('cac:TaxTotal');

        $total_impuestos = $cabecera['TOTAL_TRIBUTO_IGV'] + $cabecera['TOTAL_TRIBUTO_ISC'] + $cabecera['TOTAL_TRIBUTO_OTROS'];
        $TaxTotal->appendChild($this->xml->createElement('cbc:TaxAmount',
            number_format($total_impuestos, 2, '.', '')
        ))->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);


        // Total de operaciones gravadas
        if (isset($cabecera['TOTAL_GRAVADAS']) && $cabecera['TOTAL_GRAVADAS'] > 0){
            $TaxTotal->appendChild($this->createTributoXml(
                $cabecera['TOTAL_TRIBUTO_IGV'],
                $cabecera['TOTAL_GRAVADAS'],
                $cabecera['CODIGO_MONEDA'],
                '1000',
                'IGV',
                'VAT',
                'S'));
        }

        //Total de operaciones exoneradas
        if (isset($cabecera['TOTAL_EXONERADAS']) && $cabecera['TOTAL_EXONERADAS'] > 0){
            $TaxTotal->appendChild($this->createTributoXml(
                '0.00',
                $cabecera['TOTAL_EXONERADAS'],
                $cabecera['CODIGO_MONEDA'],
                '9997',
                'EXONERADO',
                'VAT',
                'E'));
        }

        //Total de operaciones inafectas
        if (isset($cabecera['TOTAL_INAFECTAS']) && $cabecera['TOTAL_INAFECTAS'] > 0){
            $TaxTotal->appendChild($this->createTributoXml(
                '0.00',
                $cabecera['TOTAL_INAFECTAS'],
                $cabecera['CODIGO_MONEDA'],
                '9998',
                'INAFECTO',
                'FRE',
                'O'));
        }

        //Total de operaciones gratuitas
        if (isset($cabecera['TOTAL_GRATUITAS']) && $cabecera['TOTAL_GRATUITAS'] > 0){
            $igv = isset($cabecera['TOTAL_TRIBUTO_IGV']) && $cabecera['TOTAL_TRIBUTO_IGV'] > 0 ? $cabecera['TOTAL_TRIBUTO_IGV'] : 0;
            $TaxTotal->appendChild($this->createTributoXml(
                number_format($igv, 2, '.', ''),
                $cabecera['TOTAL_GRATUITAS'],
                $cabecera['CODIGO_MONEDA'],
                '9996',
                'GRATUITO',
                'FRE',
                'Z'));
        }

        // TODO sumatoria del IGV, ISC y Otros tributos

        $root->appendChild($TaxTotal);

        //IMPORTE TOTAL DE LA VENTA
        $root->appendChild($this->createImporteTotalXml($cabecera));

        $n = 1;
        foreach ($detalles as $detalle) {
            $detalle['ID'] = $n++;
            $root->appendChild($this->createDetalleXml($cabecera, $detalle, $cabecera['TIPO_DOCUMENTO']));
        }

        $file = $this->emisor->get('NRO_DOCUMENTO') . '-' . $cabecera['TIPO_DOCUMENTO'] . '-' . $cabecera['NUMERO_DOCUMENTO'];
        $this->generateQr($cabecera);
        return $this->saveXml($file, 0);

    }
}