<?php

namespace Facturador\Comprobantes;

use Facturador\Core\Comprobante;

class Resumen extends Comprobante
{
    public function __construct($emisor)
    {
        parent::__construct($emisor);
    }

    public function crearXml($cabecera, $detalles)
    {
        //Crea el Xml raiz del resumen
        $root = $this->xml->createElement('p:SummaryDocuments');
        $root->setAttribute("xmlns:p", "urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1");
        $root->setAttribute("xmlns:ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $root->setAttribute("xmlns:cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $root->setAttribute("xmlns:cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $root->setAttribute("xmlns:sac", "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1");
        $root = $this->xml->appendChild($root);

        //UBL extensions
        $ubl_extension = $this->xml->createElement('ext:UBLExtensions');

        $firma = $this->xml->createElement('ext:UBLExtension');
        $firma->appendChild($this->xml->createElement('ext:ExtensionContent'));
        $root->appendChild($firma);
        $ubl_extension->appendChild($firma);

        $root->appendChild($ubl_extension);

        //Version del UBL
        $root->appendChild($this->xml->createElement('cbc:UBLVersionID', '2.0'));
        //Version de la estructura del Documento
        $root->appendChild($this->xml->createElement('cbc:CustomizationID', '1.1'));

        //Numero de Documento
        $root->appendChild($this->xml->createElement(
            'cbc:ID',
            'RC-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['CORRELATIVO']));

        //Tipo de documento
        $root->appendChild($this->xml->createElement('cbc:ReferenceDate', $cabecera['FECHA_REFERENCIA']));

        //Fecha de emision
        $root->appendChild($this->xml->createElement('cbc:IssueDate', $cabecera['FECHA_EMISION']));


        //Referencia de la firma digital
        $firma_ref = $this->xml->createElement('cac:Signature');
        $firma_ref->appendChild($this->xml->createElement('cbc:ID', 'RC-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['CORRELATIVO']));

        $sign_party = $this->xml->createElement('cac:SignatoryParty');
        $sign_party->appendChild($this->xml->createElement('cac:PartyIdentification'))
            ->appendChild($this->xml->createElement('cbc:ID', $this->emisor->get('NRO_DOCUMENTO')));

        $sign_party->appendChild($this->xml->createElement('cac:PartyName'))
            ->appendChild($this->xml->createElement('cbc:Name'))
            ->appendChild($this->xml->createCDATASection($this->emisor->get('RAZON_SOCIAL')));

        $firma_ref->appendChild($sign_party);

        $ext_ref = $this->xml->createElement('cac:DigitalSignatureAttachment');
        $ext_ref->appendChild($this->xml->createElement('cac:ExternalReference'))
            ->appendChild($this->xml->createElement('cbc:URI', '#' . 'RC-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['CORRELATIVO']));
        $firma_ref->appendChild($ext_ref);

        $root->appendChild($firma_ref);

        //Datos del emisor
        $root->appendChild($this->createEmisorXml());


        $n = 1;
        foreach ($detalles as $detalle) {

            $doc_line = $this->xml->createElement('sac:SummaryDocumentsLine');

            $doc_line->appendChild($this->xml->createElement('cbc:LineID', $n++));
            $doc_line->appendChild($this->xml->createElement('cbc:DocumentTypeCode', $detalle['TIPO_DOCUMENTO']));
            $doc_line->appendChild($this->xml->createElement('cbc:ID', $detalle['NUMERO_DOCUMENTO']));
            $doc_line->appendChild($this->createClienteXml($detalle));

            //Billing reference
            if (isset($detalle['NOTA_NUMERO_DOCUMENTO']) && $detalle['NOTA_NUMERO_DOCUMENTO'] != '') {
                $billing_ref = $this->xml->createElement('cac:BillingReference');
                $p = $billing_ref->appendChild($this->xml->createElement('cac:InvoiceDocumentReference'));
                $p->appendChild($this->xml->createElement('cbc:ID', $detalle['NOTA_NUMERO_DOCUMENTO']));
                $p->appendChild($this->xml->createElement('cbc:DocumentTypeCode', $detalle['NOTA_TIPO_DOCUMENTO']));

                $doc_line->appendChild($billing_ref);
            }


            $estado_item = $this->xml->createElement('cac:Status');
            $estado_item->appendChild($this->xml->createElement('cbc:ConditionCode', $detalle['ESTADO_ITEM']));
            $doc_line->appendChild($estado_item);

            $doc_line->appendChild($this->xml->createElement('sac:TotalAmount', $detalle['TOTAL_VENTA']))
                ->setAttribute('currencyID', $detalle['CODIGO_MONEDA']);



            if ($detalle['TOTAL_GRAVADAS'] > 0) {
                $totales_venta = $this->xml->createElement('sac:BillingPayment');
                $totales_venta->appendChild($this->xml->createElement('cbc:PaidAmount', $detalle['TOTAL_GRAVADAS']))
                    ->setAttribute('currencyID', $detalle['CODIGO_MONEDA']);
                $totales_venta->appendChild($this->xml->createElement('cbc:InstructionID', '01'));

                $doc_line->appendChild($totales_venta);
            }

            if ($detalle['TOTAL_EXONERADAS'] > 0) {
                $totales_venta = $this->xml->createElement('sac:BillingPayment');
                $totales_venta->appendChild($this->xml->createElement('cbc:PaidAmount', $detalle['TOTAL_EXONERADAS']))
                    ->setAttribute('currencyID', $detalle['CODIGO_MONEDA']);
                $totales_venta->appendChild($this->xml->createElement('cbc:InstructionID', '02'));

                $doc_line->appendChild($totales_venta);
            }

            if ($detalle['TOTAL_INAFECTAS'] > 0) {
                $totales_venta = $this->xml->createElement('sac:BillingPayment');
                $totales_venta->appendChild($this->xml->createElement('cbc:PaidAmount', $detalle['TOTAL_INAFECTAS']))
                    ->setAttribute('currencyID', $detalle['CODIGO_MONEDA']);
                $totales_venta->appendChild($this->xml->createElement('cbc:InstructionID', '03'));

                $doc_line->appendChild($totales_venta);
            }

            if ($detalle['TOTAL_OTROS_CARGOS'] > 0) {
                $totales_venta = $this->xml->createElement('cac:AllowanceCharge');
                $totales_venta->appendChild($this->xml->createElement('cbc:ChargeIndicator', 'true'));
                $totales_venta->appendChild($this->xml->createElement('cbc:Amount', $detalle['TOTAL_OTROS_CARGOS']))
                    ->setAttribute('currencyID', $detalle['CODIGO_MONEDA']);

                $doc_line->appendChild($totales_venta);
            }

            //TRIBUTOS
            if (isset($detalle['TOTAL_TRIBUTO_IGV']))
                $doc_line->appendChild($this->TributoXml($detalle['TOTAL_TRIBUTO_IGV'], $detalle['CODIGO_MONEDA'], '1000', 'IGV', 'VAT'));
            if (isset($detalle['TOTAL_TRIBUTO_ISC']))
                $doc_line->appendChild($this->TributoXml($detalle['TOTAL_TRIBUTO_ISC'], $detalle['CODIGO_MONEDA'], '2000', 'ISC', 'EXC'));
            if (isset($detalle['TOTAL_TRIBUTO_OTROS']))
                $doc_line->appendChild($this->TributoXml($detalle['TOTAL_TRIBUTO_OTROS'], $detalle['CODIGO_MONEDA'], '9999', 'OTROS', 'OTH'));


            $root->appendChild($doc_line);

        }

        $file = $this->emisor->get('NRO_DOCUMENTO') . '-RC-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['CORRELATIVO'];
        return $this->saveXml($file, 0);

    }

    // sobrescribiendo los metodos ya que los que estan en el core son 2.1
    public function createClienteXml($data)
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

    // sobrescribiendo los metodos ya que los que estan en el core son 2.1
    public function createEmisorXml()
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

    // sobrescribiendo los metodos ya que los que estan en el core son 2.1
    public function TributoXml($total, $moneda, $id, $name, $type_code)
    {
        $tributo = $this->xml->createElement('cac:TaxTotal');
        $tributo->appendChild($this->xml->createElement('cbc:TaxAmount', $total))
            ->setAttribute('currencyID', $moneda);

        $tributo_subtotal = $this->xml->createElement('cac:TaxSubtotal');
        $tributo_subtotal->appendChild($this->xml->createElement('cbc:TaxAmount', $total))
            ->setAttribute('currencyID', $moneda);

        $tributo_categoria = $tributo_subtotal->appendChild($this->xml->createElement('cac:TaxCategory'))
            ->appendChild($this->xml->createElement('cac:TaxScheme'));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:ID', $id));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:Name', $name));
        $tributo_categoria->appendChild($this->xml->createElement('cbc:TaxTypeCode', $type_code));
        $tributo->appendChild($tributo_subtotal);

        return $tributo;
    }

    public function send($file)
    {

        return $this->emisor->sendSummary($file);
    }
}