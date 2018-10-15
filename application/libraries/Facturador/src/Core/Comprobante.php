<?php

namespace Facturador\Core;
require __DIR__ . '/../../lib/phpqrcode/qrlib.php';
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

    protected function saveXml($file, $sign_node = 1)
    {
        $this->xml->formatOutput = TRUE;
        $this->xml->preserveWhiteSpace = TRUE;
        $this->xml->saveXML();
        if (!file_exists($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'))) {
            mkdir($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'));
        }

        $path = $this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . $file . '.XML';
        $this->xml->save($path);

        return $this->emisor->sign($file, $sign_node);
    }

    protected function generateQr($cabecera)
    {
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

        $party = $this->xml->createElement('cac:Party');

        $PartyIdentification = $this->xml->createElement('cac:PartyIdentification');
        $ID = $this->xml->createElement('cbc:ID', $data['NRO_DOCUMENTO']);
        $ID->setAttribute('schemeID', '6');
        $ID->setAttribute('schemeName', 'Documento de Identidad');
        $ID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $ID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $PartyIdentification->appendChild($ID);
        $party->appendChild($PartyIdentification);


        $nombre_comercial = $this->xml->createElement('cac:PartyName');
        $nombre_comercial->appendChild($this->xml->createElement('cbc:Name'))
            ->appendChild($this->xml->createCDATASection($data['NOMBRE_COMERCIAL']));
        $party->appendChild($nombre_comercial);


        $PartyTaxScheme = $this->xml->createElement('cac:PartyTaxScheme');

        $PartyTaxScheme->appendChild($this->xml->createElement('cbc:RegistrationName'))
            ->appendChild($this->xml->createCDATASection($data['RAZON_SOCIAL']));

        $CompanyID = $this->xml->createElement('cbc:CompanyID', $data['NRO_DOCUMENTO']);
        $CompanyID->setAttribute('schemeID', '6');
        $CompanyID->setAttribute('schemeName', 'SUNAT:Identificador de Documento de Identidad');
        $CompanyID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $CompanyID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $PartyTaxScheme->appendChild($CompanyID);

        $TaxScheme = $this->xml->createElement('cac:TaxScheme');
        $ID = $this->xml->createElement('cbc:ID', $data['NRO_DOCUMENTO']);
        $ID->setAttribute('schemeID', '6');
        $ID->setAttribute('schemeName', 'SUNAT:Identificador de Documento de Identidad');
        $ID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $ID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $TaxScheme->appendChild($ID);
        $PartyTaxScheme->appendChild($TaxScheme);

        $party->appendChild($PartyTaxScheme);


        $PartyLegalEntity = $this->xml->createElement('cac:PartyLegalEntity');

        $PartyLegalEntity->appendChild($this->xml->createElement('cbc:RegistrationName'))
            ->appendChild($this->xml->createCDATASection($data['RAZON_SOCIAL']));

        $RegistrationAddress = $this->xml->createElement('cac:RegistrationAddress');
        $ID = $this->xml->createElement('cbc:ID');
        $ID->setAttribute('schemeName', 'Ubigeos');
        $ID->setAttribute('schemeAgencyName', 'PE:INEI');
        $RegistrationAddress->appendChild($ID);

        $AddressTypeCode = $this->xml->createElement('cbc:AddressTypeCode', $data['UBIGEO']);
        $AddressTypeCode->setAttribute('listAgencyName', 'PE:SUNAT');
        $AddressTypeCode->setAttribute('listName', 'Establecimientos anexos');
        $RegistrationAddress->appendChild($AddressTypeCode);

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:CityName'))
            ->appendChild($this->xml->createCDATASection($data['DEPARTAMENTO']));

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:CountrySubentity'))
            ->appendChild($this->xml->createCDATASection($data['PROVINCIA']));

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:District'))
            ->appendChild($this->xml->createCDATASection($data['DISTRITO']));

        $AddressLine = $this->xml->createElement('cac:AddressLine');
        $Line = $this->xml->createElement('cbc:Line');
        $Line->appendChild($this->xml->createCDATASection($data['DIRECCION']));
        $AddressLine->appendChild($Line);
        $RegistrationAddress->appendChild($AddressLine);

        $pais = $this->xml->createElement('cac:Country');
        $IdentificationCode = $this->xml->createElement('cbc:IdentificationCode', $data['PAIS_CODIGO']);
        $IdentificationCode->setAttribute('listID', 'ISO 3166-1');
        $IdentificationCode->setAttribute('listAgencyName', 'United Nations Economic Commission for Europe');
        $IdentificationCode->setAttribute('listName', 'Country');
        $pais->appendChild($IdentificationCode);
        $RegistrationAddress->appendChild($pais);

        $PartyLegalEntity->appendChild($RegistrationAddress);
        $party->appendChild($PartyLegalEntity);

        $Contact = $this->xml->createElement('cac:Contact');
        $Name = $this->xml->createElement('cbc:Name');
        $Name->appendChild($this->xml->createCDATASection('-'));
        $Contact->appendChild($Name);
        $party->appendChild($Contact);

        $emisor->appendChild($party);

        return $emisor;
    }


    protected function createClienteXml($data)
    {
        $cliente = $this->xml->createElement('cac:AccountingCustomerParty');

        $party = $this->xml->createElement('cac:Party');

        $PartyIdentification = $this->xml->createElement('cac:PartyIdentification');
        $ID = $this->xml->createElement('cbc:ID', $data['CLIENTE_NRO_DOCUMENTO']);
        $ID->setAttribute('schemeID', $data['CLIENTE_TIPO_IDENTIDAD']);
        $ID->setAttribute('schemeName', 'Documento de Identidad');
        $ID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $ID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $PartyIdentification->appendChild($ID);
        $party->appendChild($PartyIdentification);


        $nombre_comercial = $this->xml->createElement('cac:PartyName');
        $nombre_comercial->appendChild($this->xml->createElement('cbc:Name'))
            ->appendChild($this->xml->createCDATASection($data['CLIENTE_NOMBRE']));
        $party->appendChild($nombre_comercial);


        $PartyTaxScheme = $this->xml->createElement('cac:PartyTaxScheme');

        $PartyTaxScheme->appendChild($this->xml->createElement('cbc:RegistrationName'))
            ->appendChild($this->xml->createCDATASection($data['CLIENTE_NOMBRE']));

        $CompanyID = $this->xml->createElement('cbc:CompanyID', $data['CLIENTE_NRO_DOCUMENTO']);
        $CompanyID->setAttribute('schemeID', $data['CLIENTE_TIPO_IDENTIDAD']);
        $CompanyID->setAttribute('schemeName', 'SUNAT:Identificador de Documento de Identidad');
        $CompanyID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $CompanyID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $PartyTaxScheme->appendChild($CompanyID);

        $TaxScheme = $this->xml->createElement('cac:TaxScheme');
        $ID = $this->xml->createElement('cbc:ID', $data['CLIENTE_NRO_DOCUMENTO']);
        $ID->setAttribute('schemeID', $data['CLIENTE_TIPO_IDENTIDAD']);
        $ID->setAttribute('schemeName', 'SUNAT:Identificador de Documento de Identidad');
        $ID->setAttribute('schemeAgencyName', 'PE:SUNAT');
        $ID->setAttribute('schemeURI', 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06');
        $TaxScheme->appendChild($ID);
        $PartyTaxScheme->appendChild($TaxScheme);

        $party->appendChild($PartyTaxScheme);


        $PartyLegalEntity = $this->xml->createElement('cac:PartyLegalEntity');

        $PartyLegalEntity->appendChild($this->xml->createElement('cbc:RegistrationName'))
            ->appendChild($this->xml->createCDATASection($data['CLIENTE_NOMBRE']));

        $RegistrationAddress = $this->xml->createElement('cac:RegistrationAddress');
        $ID = $this->xml->createElement('cbc:ID');
        $ID->setAttribute('schemeName', 'Ubigeos');
        $ID->setAttribute('schemeAgencyName', 'PE:INEI');
        $RegistrationAddress->appendChild($ID);

        $AddressTypeCode = $this->xml->createElement('cbc:AddressTypeCode', '000000');
        $AddressTypeCode->setAttribute('listAgencyName', 'PE:SUNAT');
        $AddressTypeCode->setAttribute('listName', 'Establecimientos anexos');
        $RegistrationAddress->appendChild($AddressTypeCode);

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:CityName'))
            ->appendChild($this->xml->createCDATASection('-'));

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:CountrySubentity'))
            ->appendChild($this->xml->createCDATASection('-'));

        $RegistrationAddress->appendChild($this->xml->createElement('cbc:District'))
            ->appendChild($this->xml->createCDATASection('-'));

        $AddressLine = $this->xml->createElement('cac:AddressLine');
        $Line = $this->xml->createElement('cbc:Line');
        $Line->appendChild($this->xml->createCDATASection('-'));
        $AddressLine->appendChild($Line);
        $RegistrationAddress->appendChild($AddressLine);

        $pais = $this->xml->createElement('cac:Country');
        $IdentificationCode = $this->xml->createElement('cbc:IdentificationCode', 'PE');
        $IdentificationCode->setAttribute('listID', 'ISO 3166-1');
        $IdentificationCode->setAttribute('listAgencyName', 'United Nations Economic Commission for Europe');
        $IdentificationCode->setAttribute('listName', 'Country');
        $pais->appendChild($IdentificationCode);
        $RegistrationAddress->appendChild($pais);

        $PartyLegalEntity->appendChild($RegistrationAddress);
        $party->appendChild($PartyLegalEntity);

        $cliente->appendChild($party);

        return $cliente;
    }

    /* Catálogo No. 05: Códigos de Tipos de Tributos
     * codigo - tipo - categoria => descripcion
     * 1000 - VAT - S => IGV impuesto general a las ventas
     * 2000 - EXC - S => ISC impuesto selectivo al consumo
     * 9995 - FRE - G => Exportacion
     * 9996 - FRE - Z => Gratuito
     * 9997 - VAT - E => Exonerado
     * 9998 - FRE - O => Inafecto
     * 9999 - OTH - S => Otros conceptos de pago
     *
     * PARAMS
     * $total_impuesto - TaxableAmount: Monto base sobre el cual se esta aplicando el impuesto
     * $base_total - TaxAmount: impuesto aplicado
     * $moneda: Codigo de la moneda
     * $id: Codigo de tipos de tributos
     * $name: descripcion de tipos de tributos
     * $type_code: tipo de tributos
     * $category: categoria de tipos de tributos
     * */
    protected function createTributoXml($total_impuesto, $base_total, $moneda, $id, $name, $type_code, $category)
    {

        $tributo = $this->xml->createElement('cac:TaxSubtotal');

        $tributo->appendChild($this->xml->createElement('cbc:TaxableAmount', $base_total))
            ->setAttribute('currencyID', $moneda);

        $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $total_impuesto))
            ->setAttribute('currencyID', $moneda);

        $tributo_categoria = $this->xml->createElement('cac:TaxCategory');
        $ID = $this->xml->createElement('cbc:ID', $category);
        $ID->setAttribute('schemeID', "UN/ECE 5305");
        $ID->setAttribute('schemeName', "Tax Category Identifier");
        $ID->setAttribute('schemeAgencyName', "United Nations Economic Commission for Europe");
        $tributo_categoria->appendChild($ID);

        $TaxScheme = $this->xml->createElement('cac:TaxScheme');
        $ID = $this->xml->createElement('cbc:ID', $id);
        $ID->setAttribute('schemeID', "UN/ECE 5153");
        $ID->setAttribute('schemeAgencyID', "6");
        $TaxScheme->appendChild($ID);

        $TaxScheme->appendChild($this->xml->createElement('cbc:Name', $name));
        $TaxScheme->appendChild($this->xml->createElement('cbc:TaxTypeCode', $type_code));
        $tributo_categoria->appendChild($TaxScheme);

        $tributo->appendChild($tributo_categoria);

        return $tributo;
    }

    /*
     * LineExtensionAmount: El importe total de la venta sin cosiderar descuentos, impuestos u otros atributos
     * TaxInclusiveAmount: El importe total de la venta con impuestos incluidos
     * AllowanceTotalAmount: Total de los descuentos globales
     * ChargeTotalAmount: Sumatoria de otros cargos
     * PayableAmount: TaxInclusiveAmount - AllowanceTotalAmount + ChargeTotalAmount
     * */
    protected function createImporteTotalXml($data)
    {
        $importe_total = $this->xml->createElement('cac:LegalMonetaryTotal');
        $importe_total->appendChild($this->xml->createElement('cbc:LineExtensionAmount', $data['SUBTOTAL_VENTA']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement('cbc:TaxInclusiveAmount', $data['TOTAL_VENTA']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement('cbc:AllowanceTotalAmount', $data['TOTAL_DESCUENTO_GLOBAL']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement('cbc:ChargeTotalAmount', $data['TOTAL_OTROS_CARGOS']))
            ->setAttribute('currencyID', $data['CODIGO_MONEDA']);
        $importe_total->appendChild($this->xml->createElement(
            'cbc:PayableAmount',
            number_format($data['TOTAL_VENTA'] - $data['TOTAL_DESCUENTO_GLOBAL'] + $data['TOTAL_OTROS_CARGOS'])
        ))->setAttribute('currencyID', $data['CODIGO_MONEDA']);

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
        $DocumentTypeCode = $this->xml->createElement('cbc:DocumentTypeCode', $data['GUIA_REMISION_CODIGO']);
        $DocumentTypeCode->setAttribute('listAgencyName', "PE:SUNAT");
        $DocumentTypeCode->setAttribute('listName', "SUNAT:Identificador de guia relacionada");
        $DocumentTypeCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01");
        $remision->appendChild($DocumentTypeCode);
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
        $DocumentTypeCode = $this->xml->createElement('cbc:DocumentTypeCode', $data['NRO_DOCUMENTO_REF_CODIGO']);
        $DocumentTypeCode->setAttribute('listAgencyName', "PE:SUNAT");
        $DocumentTypeCode->setAttribute('listName', "SUNAT:Identificador de guia relacionada");
        $DocumentTypeCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01");
        $doc->appendChild($DocumentTypeCode);
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

        // Orden correlativo de los items agregados
        $invoce_line->appendChild($this->xml->createElement('cbc:ID', $detalle['ID']));

        // Cantidad y Unidad de Medida del item. Cuando es serivio o item no cuantificable sera 1
        // Valor de Códigos de unidades de medida Catálogo N° 03
        $InvoicedQuantity = $this->xml->createElement($quantity_tag, $detalle['CANTIDAD']);
        $InvoicedQuantity->setAttribute('unitCode', $detalle['UNIDAD_MEDIDA']);
        $InvoicedQuantity->setAttribute('unitCodeListID', "UN/ECE rec 20");
        $InvoicedQuantity->setAttribute('unitCodeListAgencyName', "United Nations Economic Commission for Europe");
        $invoce_line->appendChild($InvoicedQuantity);

        // Valor de la venta por item. Es la cantidad * el precio_valor. Este importe no incluye los impuestos ni decuentos
        $invoce_line
            ->appendChild($this->xml->createElement(
                'cbc:LineExtensionAmount',
                number_format($detalle['PRECIO_VALOR'] * $detalle['CANTIDAD'], 2, '.', ''))
            )->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        /* Precio de venta unitario por item. Incluye impuestos y descuentos por items
         * PriceTypeCode (Catálogo N° 16)
         * 01 => Precio unitario (incluye IGV)
         * 02 => Valor referencial unitario en operaciones no onerosas. Cuando se hacen operaciones o servicios gratuitos
         * */
        $p = $this->xml->createElement('cac:PricingReference');
        $precio = $p->appendChild($this->xml->createElement('cac:AlternativeConditionPrice'));
        $precio->appendChild($this->xml->createElement('cbc:PriceAmount', $detalle['PRECIO_VENTA']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        // (Catálogo No. 16).
        $PriceTypeCode = $this->xml->createElement('cbc:PriceTypeCode', $detalle['TIPO_PRECIO']);
        $PriceTypeCode->setAttribute('listName', "Tipo de Precio");
        $PriceTypeCode->setAttribute('listAgencyName', "PE:SUNAT");
        $PriceTypeCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16");
        $precio->appendChild($PriceTypeCode);
        $invoce_line->appendChild($p);

        // TODO descuentos por items

        //Tributo IGV
        $tributo = $this->xml->createElement('cac:TaxTotal');
        $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $tributo_subtotal = $this->xml->createElement('cac:TaxSubtotal');
        $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxableAmount', number_format($detalle['PRECIO_VALOR'] * $detalle['CANTIDAD'], 2, '.', '')))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxAmount', $detalle['DETALLE_TRIBUTO_IGV']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);

        $tributo_categoria = $this->xml->createElement('cac:TaxCategory');
        $ID = $this->xml->createElement('cbc:ID', 'S');
        $ID->setAttribute('schemeID', 'UN/ECE 5305');
        $ID->setAttribute('schemeName', 'Tax Category Identifier');
        $ID->setAttribute('schemeAgencyName', 'United Nations Economic Commission for Europe');
        $tributo_categoria->appendChild($ID);

        $tributo_categoria->appendChild($this->xml->createElement('cbc:Percent', '18.00'));

        // (Catálogo No. 07).
        $TaxExemptionReasonCode = $this->xml->createElement('cbc:TaxExemptionReasonCode', $detalle['TIPO_TRIBUTO_IGV']);
        $TaxExemptionReasonCode->setAttribute('listAgencyName', "PE:SUNAT");
        $TaxExemptionReasonCode->setAttribute('listName', "SUNAT:Codigo de Tipo de Afectación del IGV");
        $TaxExemptionReasonCode->setAttribute('listURI', "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07");
        $tributo_categoria->appendChild($TaxExemptionReasonCode);

        $tax = $this->xml->createElement('cac:TaxScheme');
        $ID = $this->xml->createElement('cbc:ID', '1000');
        $ID->setAttribute('schemeID', "UN/ECE 5153");
        $ID->setAttribute('schemeName', "Tax Scheme Identifier");
        $ID->setAttribute('schemeAgencyName', "United Nations Economic Commission for Europe");
        $tax->appendChild($ID);

        $tax->appendChild($this->xml->createElement('cbc:Name', 'IGV'));

        $tax->appendChild($this->xml->createElement('cbc:TaxTypeCode', 'VAT'));

        $tributo_categoria->appendChild($tax);
        $tributo_subtotal->appendChild($tributo_categoria);

        $tributo->appendChild($tributo_subtotal);

        $invoce_line->appendChild($tributo);

        // TODO tributo ISC

        /*
         * Description: Descripcion del item
         * ID: Codigo del producto
         * ItemClassificationCode: (CATALOGO No. 25)
         * */
        $descripcion = $this->xml->createElement('cac:Item');
        $descripcion->appendChild($this->xml->createElement('cbc:Description'))
            ->appendChild($this->xml->createCDATASection($detalle['DESCRIPCION']));

        if (isset($detalle['CODIGO'])) {
            $codigo = $this->xml->createElement('cac:SellersItemIdentification');
            $codigo->appendChild($this->xml->createElement('cbc:ID', $detalle['CODIGO']));
            $descripcion->appendChild($codigo);
        }

        // TODO Codigo de productos de SUNAT (ItemClassificationCode)
        // TODO Propiedades adicionales del item (AdditionalItemProperty)

        $invoce_line->appendChild($descripcion);

        // Valor unitario por item. Este importe no incluye los impuestos ni decuentos
        $precio = $this->xml->createElement('cac:Price');
        $precio->appendChild($this->xml->createElement('cbc:PriceAmount', $detalle['PRECIO_VALOR']))
            ->setAttribute('currencyID', $cabecera['CODIGO_MONEDA']);
        $invoce_line->appendChild($precio);

        return $invoce_line;
    }


}