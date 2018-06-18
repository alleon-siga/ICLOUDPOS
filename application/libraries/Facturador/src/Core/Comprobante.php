<?php

namespace Facturador\Core;

use Facturador\Emisor;

abstract class Comprobante
{
    protected $xml;
    protected $emisor;


    public function __construct($emisor)
    {
        $this->xml = new \DOMDocument("1.0", "ISO-8859-1");
        $this->emisor = new Emisor($emisor);

    }

    abstract public function crearXml($cabecera, $detalle);

    protected function saveXml($file)
    {
        $this->xml->formatOutput = TRUE;
        $this->xml->preserveWhiteSpace = TRUE;
        $this->xml->saveXML();
        if (!file_exists($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'))) {
            mkdir($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'));
        }

        $path = $this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . $file . '.XML';
        $this->xml->save($path);

        return $this->emisor->sign($file);
    }

    protected function generateQr($cabecera)
    {
        require __DIR__ . '/../../lib/phpqrcode/qrlib.php';
        $ruta = $this->emisor->getPathQr() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO');
        if (!is_dir($ruta)) {
            mkdir($ruta);
        }
        $name = $cabecera['TIPO_DOCUMENTO'] . '-' . $cabecera['NUMERO_DOCUMENTO'];
        $ruta .= DIRECTORY_SEPARATOR . $name . '.png';

        $string =
            $this->emisor->get('NRO_DOCUMENTO') . '|' .
            $cabecera['TIPO_DOCUMENTO'] . '|' .
            str_replace('-', '|', $cabecera['NUMERO_DOCUMENTO']) . '|' .
            $cabecera['TOTAL_TRIBUTO_IGV'] . '|' .
            $cabecera['TOTAL_VENTA'] . '|' .
            $cabecera['FECHA_EMISION'] . '|' .
            $cabecera['CLIENTE_TIPO_IDENTIDAD'] . '|' .
            $cabecera['CLIENTE_NRO_DOCUMENTO'] . '|';

        \QRcode::png($string, $ruta, 'Q', 15, 0);
    }

    protected function createEmisorXml()
    {
        $data = $this->emisor->getData();

        $emisor = $this->xml->createElement('cac:AccountingSupplierParty');
        $emisor->appendChild($this->xml->createElement('cbc:CustomerAssignedAccountID', $data['NRO_DOCUMENTO']));
        $emisor->appendChild($this->xml->createElement('cbc:AdditionalAccountID', '6'));

        $party = $this->xml->createElement('cac:Party');
        $nombre_comercial = $this->xml->createElement('cac:PartyName');
        $nombre_comercial->appendChild($this->xml->createElement('cbc:Name'))
            ->appendChild($this->xml->createCDATASection($data['NOMBRE_COMERCIAL']));

        $party->appendChild($nombre_comercial);

        $direccion = $this->xml->createElement('cac:PostalAddress');
        $direccion->appendChild($this->xml->createElement('cbc:ID', $data['UBIGEO']));
        $direccion->appendChild($this->xml->createElement('cbc:StreetName'))
            ->appendChild($this->xml->createCDATASection($data['DIRECCION']));
        $direccion->appendChild($this->xml->createElement('cbc:CitySubdivisionName'))
            ->appendChild($this->xml->createCDATASection($data['URBANIZACION']));
        $direccion->appendChild($this->xml->createElement('cbc:CityName'))
            ->appendChild($this->xml->createCDATASection($data['DEPARTAMENTO']));
        $direccion->appendChild($this->xml->createElement('cbc:CountrySubentity'))
            ->appendChild($this->xml->createCDATASection($data['PROVINCIA']));
        $direccion->appendChild($this->xml->createElement('cbc:District'))
            ->appendChild($this->xml->createCDATASection($data['DISTRITO']));

        $pais = $this->xml->createElement('cac:Country');
        $pais->appendChild($this->xml->createElement('cbc:IdentificationCode', $data['PAIS_CODIGO']));
        $direccion->appendChild($pais);

        $party->appendChild($direccion);

        $razon_social = $this->xml->createElement('cac:PartyLegalEntity');
        $razon_social->appendChild($this->xml->createElement('cbc:RegistrationName', $data['RAZON_SOCIAL']));

        $party->appendChild($razon_social);

        $emisor->appendChild($party);

        return $emisor;
    }

    protected function createClienteXml($data)
    {
        $cliente = $this->xml->createElement('cac:AccountingCustomerParty');
        $cliente->appendChild($this->xml->createElement('cbc:CustomerAssignedAccountID', $data['CLIENTE_NRO_DOCUMENTO']));
        $cliente->appendChild($this->xml->createElement('cbc:AdditionalAccountID', $data['CLIENTE_TIPO_IDENTIDAD']));
        $cliente_nombre = $this->xml->createElement('cac:Party');
        $nombre = $this->xml->createElement('cac:PartyLegalEntity');
        $nombre->appendChild($this->xml->createElement('cbc:RegistrationName'))
            ->appendChild($this->xml->createCDATASection($data['CLIENTE_NOMBRE']));
        $cliente_nombre->appendChild($nombre);
        $cliente->appendChild($cliente_nombre);

        return $cliente;
    }

    protected function ventaValoresUbl($data)
    {
        $ubl_extension = $this->xml->createElement('ext:UBLExtension');
        $venta_totales = $ubl_extension->appendChild($this->xml->createElement('ext:ExtensionContent'))
            ->appendChild($this->xml->createElement('sac:AdditionalInformation'));

        //total gravadas
        $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
        $total->appendChild($this->xml->createElement('cbc:ID', '1001'));
        $total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_GRAVADAS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $venta_totales->appendChild($total);

        //total inafectas
        $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
        $total->appendChild($this->xml->createElement('cbc:ID', '1002'));
        $total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_INAFECTAS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $venta_totales->appendChild($total);

        //total exoneradas
        $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
        $total->appendChild($this->xml->createElement('cbc:ID', '1003'));
        $total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_EXONERADAS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $venta_totales->appendChild($total);

        //total gratuitas
        $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
        $total->appendChild($this->xml->createElement('cbc:ID', '1004'));
        $total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_GRATUITAS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $venta_totales->appendChild($total);

        //total gratuitas
        if (isset($data['TOTAL_DESCUENTOS'])) {
            $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
            $total->appendChild($this->xml->createElement('cbc:ID', '2005'));
            $total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_DESCUENTOS']))
                ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
            $venta_totales->appendChild($total);
        }


        //Importe de la percepción en moneda nacional. Referencia Pag. 32 "Guia para realizar facturas"
//        $total = $this->xml->createElement('sac:AdditionalMonetaryTotal');
//        $total->appendChild($this->xml->createElement('cbc:ID', '2001'));
//        $total->appendChild($this->xml->createElement('sac:ReferencAmount', '0.00'))
//            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
//        $total->appendChild($this->xml->createElement('cbc:PayableAmount', '0.00'))
//            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
//        $total->appendChild($this->xml->createElement('sac:TotalAmount', '0.00'))
//            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
//        $venta_totales->appendChild($total);

        //Leyenda del total en letras
        if (isset($data['TOTAL_VENTA_LETRAS']) && $data['TOTAL_VENTA_LETRAS'] != '') {
            $total = $this->xml->createElement('sac:AdditionalProperty');
            $total->appendChild($this->xml->createElement('cbc:ID', '1000'));
            $total->appendChild($this->xml->createElement('cbc:Value', $data['TOTAL_VENTA_LETRAS']));
            $venta_totales->appendChild($total);
        }


        return $ubl_extension;
    }

    protected function createTributoXml($data, $id, $name, $type_code)
    {
        $tributo = $this->xml->createElement('cac:TaxTotal');
        $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $data['TOTAL_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);

        $tributo_subtotal = $this->xml->createElement('cac:TaxSubtotal');
        $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxAmount', $data['TOTAL_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);

        $tributo_categoria = $tributo_subtotal->appendChild($this->xml->createElement('cac:TaxCategory'))
            ->appendChild($this->xml->createElement('cac:TaxScheme'));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:ID', $id));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:Name', $name));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:TaxTypeCode', $type_code));
        $tributo->appendChild($tributo_subtotal);

        return $tributo;
    }

    protected function createImporteTotalXml($data)
    {
        $importe_total = $this->xml->createElement('cac:LegalMonetaryTotal');
        $importe_total->appendChild($this->xml->createElement('cbc:AllowanceTotalAmount', $data['TOTAL_DESCUENTO_GLOBAL']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement('cbc:ChargeTotalAmount', $data['TOTAL_OTROS_CARGOS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement('cbc:PayableAmount', $data['TOTAL_VENTA']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);

        return $importe_total;
    }

    /* Opcional.
    Referencia a las guías de remisión remitente o transportista, según corresponda,
    autorizadas por la SUNAT para sustentar el traslado de los bienes. Pueden existir múltiples
    guías de remisión, por lo que el número de elementos de este tipo es ilimitado. Se utilizará
    el Catálogo N° 01: “Código de Tipo de Documento”. */
    protected function createGuiaRemisionXml($data)
    {
        $remision = $this->xml->createElement('cac:DespatchDocumentReference');
        $remision->appendChild($this->xml->createElement('cbc:ID', $data['GUIA_REMISION_NRO']));
        $remision->appendChild($this->xml->createElement('cbc:DocumentTypeCode', $data['GUIA_REMISION_CODIGO']));
        return $remision;
    }

    /* Opcional
    Repetible. Referencia a cualquier otro documento, distintos a los señalados en el numeral
    anterior, asociado a la factura. Podrán especificarse documentos como comprobantes de
    retención, percepción, código SCOP, etc. Puede existir documentos de distintos tipos
    asociados a una misma factura, por lo que el número de elementos de este tipo es ilimitado.
    Se utilizará el Catálogo No. 12: “Códigos - Documentos Relacionados Tributarios”. */
    protected function createReferenciaDocXml($data)
    {
        $doc = $this->xml->createElement('cac:AdditionalDocumentReference');
        $doc->appendChild($this->xml->createElement('cbc:ID', $data['NRO_DOCUMENTO_REF']));
        $doc->appendChild($this->xml->createElement('cbc:DocumentTypeCode', $data['NRO_DOCUMENTO_REF_CODIGO']));
        return $doc;
    }

    protected function createDetalleXml($cabecera, $detalle, $tipo_doc)
    {
        $root_tag = 'cac:InvoiceLine';
        $quantity_tag = 'cbc:InvoicedQuantity';

        if ($tipo_doc == \TIPO_COMPROBANTE::$NOTA_CREDITO) {
            $root_tag = 'cac:CreditNoteLine';
            $quantity_tag = 'cbc:CreditedQuantity';
        } elseif ($tipo_doc == \TIPO_COMPROBANTE::$NOTA_DEBITO) {
            $root_tag = 'cac:DebitNoteLine';
            $quantity_tag = 'cbc:DebitedQuantity';
        }

        $invoce_line = $this->xml->createElement($root_tag);
        $invoce_line->appendChild($this->xml->createElement('cbc:ID', $detalle['ID']));
        $invoce_line->appendChild($this->xml->createElement($quantity_tag, $detalle['CANTIDAD']))
            ->setAttribute('unitCode', $detalle['UNIDAD_MEDIDA']);

        $invoce_line
            ->appendChild($this->xml->createElement(
                'cbc:LineExtensionAmount',
                number_format($detalle['PRECIO_VALOR'] * $detalle['CANTIDAD'], 2, '.', ''))
            )->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $p = $this->xml->createElement('cac:PricingReference');
        $precio = $p->appendChild($this->xml->createElement('cac:AlternativeConditionPrice'));
        $precio->appendChild($this->xml->createElement('cbc:PriceAmount', $detalle['PRECIO_VENTA']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        // (Catálogo No. 16).
        $precio->appendChild($this->xml->createElement('cbc:PriceTypeCode', $detalle['TIPO_PRECIO']));
        $invoce_line->appendChild($p);

        //Tributo IGV
        $tributo = $this->xml->createElement('cac:TaxTotal');
        $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $tributo_subtotal = $this->xml->createElement('cac:TaxSubtotal');
        $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $tributo_categoria = $this->xml->createElement('cac:TaxCategory');
        // (Catálogo No. 07).
        $tributo_categoria->appendChild($this->xml->createElement('cbc:TaxExemptionReasonCode', $detalle['TIPO_TRIBUTO_IGV']));

        $tax = $this->xml->createElement('cac:TaxScheme');
        $tax->appendChild($this->xml->createElement('cbc:ID', '1000'));
        $tax->appendChild($this->xml->createElement('cbc:Name', 'IGV'));
        $tax->appendChild($this->xml->createElement('cbc:TaxTypeCode', 'VAT'));
        $tributo_categoria->appendChild($tax);
        $tributo_subtotal->appendChild($tributo_categoria);

        $tributo->appendChild($tributo_subtotal);

        $invoce_line->appendChild($tributo);


        //Tributo ISC
        if (isset($detalle['DETALLE_TRIBUTO_ISC'])) {
            $tributo = $this->xml->createElement('cac:TaxTotal');
            $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_ISC']))
                ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

            $tributo_subtotal = $this->xml->createElement('cac:TaxSubtotal');
            $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_ISC']))
                ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

            $tributo_categoria = $this->xml->createElement('cac:TaxCategory');
            // (Catálogo No. 08).
            $tributo_categoria->appendChild($this->xml->createElement('cbc:TierRange', '02'));

            $tax = $this->xml->createElement('cac:TaxScheme');
            $tax->appendChild($this->xml->createElement('cbc:ID', '2000'));
            $tax->appendChild($this->xml->createElement('cbc:Name', 'ISC'));
            $tax->appendChild($this->xml->createElement('cbc:TaxTypeCode', 'EXC'));
            $tributo_categoria->appendChild($tax);
            $tributo_subtotal->appendChild($tributo_categoria);

            $tributo->appendChild($tributo_subtotal);

            $invoce_line->appendChild($tributo);
        }

        $descripcion = $this->xml->createElement('cac:Item');
        $descripcion->appendChild($this->xml->createElement('cbc:Description'))
            ->appendChild($this->xml->createCDATASection($detalle['DESCRIPCION']));

        if (isset($detalle['CODIGO'])) {
            $codigo = $this->xml->createElement('cac:SellersItemIdentification');
            $codigo->appendChild($this->xml->createElement('cbc:ID', $detalle['CODIGO']));
            $descripcion->appendChild($codigo);
        }

        $invoce_line->appendChild($descripcion);

        $precio = $this->xml->createElement('cac:Price');
        $precio->appendChild($this->xml->createElement('cbc:PriceAmount', $detalle['PRECIO_VALOR']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        $invoce_line->appendChild($precio);

        return $invoce_line;
    }
}