<?php

namespace Facturador\Comprobantes;

use Facturador\Core\Comprobante;

class NotaDebito extends Comprobante
{
    public function __construct($emisor)
    {
        parent::__construct($emisor);
    }

    public function crearXml($cabecera, $detalles)
    {
        //Crea el Xml raiz de la factura
        $root = $this->xml->createElement('DebitNote');
        $root->setAttribute("xmlns", "urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2");
        $root->setAttribute("xmlns:cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $root->setAttribute("xmlns:cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $root->setAttribute("xmlns:ccts", "urn:un:unece:uncefact:documentation:2");
        $root->setAttribute("xmlns:ds", "http://www.w3.org/2000/09/xmldsig#");
        $root->setAttribute("xmlns:ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $root->setAttribute("xmlns:qdt", "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2");
        $root->setAttribute("xmlns:sac", "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1");
        $root->setAttribute("xmlns:udt", "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2");
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
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
        $root->appendChild($this->xml->createElement('cbc:CustomizationID', '2.0'));

        /* Numero de Documento
         * FA01-10 => Empieza con F o B dependiendo del tipo de documento qu eafecta seguido de tres caracteres alfanumericos, guion y un numero correlativo
         * */
        $root->appendChild($this->xml->createElement('cbc:ID', $cabecera['NUMERO_DOCUMENTO']));

        //Fecha y Hora de emision
        $root->appendChild($this->xml->createElement('cbc:IssueDate', $cabecera['FECHA_EMISION']));
        $root->appendChild($this->xml->createElement('cbc:IssueTime', isset($cabecera['HORA_EMISION']) ? $cabecera['HORA_EMISION'] : date("H:i:s")));

        // Tipo de Moneda
        // ISO 4217 - Currency
        $root->appendChild($this->xml->createElement('cbc:DocumentCurrencyCode', $cabecera['CODIGO_MONEDA']));

        // Motivo por el cual se emite la nota de crédito
        /*
         * ResponseCode | Description:
         * 01 => Interes por mora
         * 02 => Aumento en el valor
         * */
        $documento_modifica = $this->xml->createElement('cac:DiscrepancyResponse');
        $documento_modifica->appendChild($this->xml->createElement('cbc:ReferenceID', $cabecera['NOTA_NUMERO_DOCUMENTO']));
        $documento_modifica->appendChild($this->xml->createElement('cbc:ResponseCode', $cabecera['NOTA_MOTIVO_CODIGO']));
        $documento_modifica->appendChild($this->xml->createElement('cbc:Description'))
            ->appendChild($this->xml->createCDATASection($cabecera['NOTA_MOTIVO_DESCRIPCION']));
        $root->appendChild($documento_modifica);

        //Asocia la nota de crédito al comprobante de pago modificado
        /* DocumentTypeCode
         * 01 => Factura
         * 03 => Boleta de venta
         * 12 => Ticket de maquina registradora
         * */
        $billing_ref = $this->xml->createElement('cac:BillingReference');
        $p = $billing_ref->appendChild($this->xml->createElement('cac:InvoiceDocumentReference'));
        $p->appendChild($this->xml->createElement('cbc:ID', $cabecera['NOTA_NUMERO_DOCUMENTO']));
        $p->appendChild($this->xml->createElement('cbc:DocumentTypeCode', $cabecera['NOTA_TIPO_DOCUMENTO']));
        $root->appendChild($billing_ref);

        // TODO caso para guia de remision

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
        $importe_total = $this->xml->createElement('cac:RequestedMonetaryTotal');
        $importe_total->appendChild($this->xml->createElement('cbc:AllowanceTotalAmount', $cabecera['TOTAL_DESCUENTO_GLOBAL']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement(
            'cbc:PayableAmount', $cabecera['TOTAL_VENTA']
        ))->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        $root->appendChild($importe_total);

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