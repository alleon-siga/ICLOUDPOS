<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 6/15/2018
 * Time: 12:04 PM
 */

namespace Facturador;


class ComunicacionBaja
{
    protected $emisor;

    public function __construct($emisor)
    {
        $this->emisor = new Emisor($emisor);
    }

    public function enviarBaja($cabecera, $detalles)
    {

        $xml = new \DOMDocument("1.0", "ISO-8859-1");
        $root = $xml->createElement('VoidedDocuments');
        $root->setAttribute("xmlns", "urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1");
        $root->setAttribute("xmlns:cac", "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2");
        $root->setAttribute("xmlns:cbc", "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2");
        $root->setAttribute("xmlns:ds", "http://www.w3.org/2000/09/xmldsig#");
        $root->setAttribute("xmlns:ext", "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2");
        $root->setAttribute("xmlns:sac", "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents1");
        $root->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");

        $root = $xml->appendChild($root);

        //UBL extensions
        $ubl_extension = $xml->createElement('ext:UBLExtensions');

        //Firma
        $firma = $xml->createElement('ext:UBLExtension');
        $firma->appendChild($xml->createElement('ext:ExtensionContent'));
        $root->appendChild($firma);
        $ubl_extension->appendChild($firma);

        $root->appendChild($ubl_extension);

        //Version del UBL
        $root->appendChild($xml->createElement('cbc:UBLVersionID', '2.0'));
        //Version de la estructura del Documento
        $root->appendChild($xml->createElement('cbc:CustomizationID', '1.0'));

        //Numero de documento
        $root->appendChild($xml->createElement(
            'cbc:ID',
            'RA-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['NUMERO_DOCUMENTO']));

        //Fecha de referencia
        $root->appendChild($xml->createElement('cbc:ReferenceDate', $cabecera['FECHA_REFERENCIA']));

        //Fecha de generado
        $root->appendChild($xml->createElement('cbc:IssueDate', $cabecera['FECHA_EMISION']));


        //Referencia de la firma digital
        $firma_ref = $xml->createElement('cac:Signature');
        $firma_ref->appendChild($xml->createElement(
            'cbc:ID',
            'RA-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['NUMERO_DOCUMENTO']));

        $sign_party = $xml->createElement('cac:SignatoryParty');
        $sign_party->appendChild($xml->createElement('cac:PartyIdentification'))
            ->appendChild($xml->createElement('cbc:ID', $this->emisor->get('NRO_DOCUMENTO')));

        $sign_party->appendChild($xml->createElement('cac:PartyName'))
            ->appendChild($xml->createElement('cbc:Name'))
            ->appendChild($xml->createCDATASection($this->emisor->get('RAZON_SOCIAL')));

        $firma_ref->appendChild($sign_party);

        $ext_ref = $xml->createElement('cac:DigitalSignatureAttachment');
        $ext_ref->appendChild($xml->createElement('cac:ExternalReference'))
            ->appendChild($xml->createElement('cbc:URI', '#' . 'RA-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['NUMERO_DOCUMENTO']));
        $firma_ref->appendChild($ext_ref);

        $root->appendChild($firma_ref);


        //Emisor
        $emisor = $xml->createElement('cac:AccountingSupplierParty');
        $emisor->appendChild($xml->createElement('cbc:CustomerAssignedAccountID', $this->emisor->get('NRO_DOCUMENTO')));
        $emisor->appendChild($xml->createElement('cbc:AdditionalAccountID', '6'));
        $party = $xml->createElement('cac:Party');

        $razon_social = $xml->createElement('cac:PartyLegalEntity');
        $razon_social->appendChild($xml->createElement('cbc:RegistrationName', $this->emisor->get('RAZON_SOCIAL')));
        $party->appendChild($razon_social);

        $emisor->appendChild($party);

        $root->appendChild($emisor);


        $n = 1;
        foreach ($detalles as $detalle) {
            $line = $xml->createElement('sac:VoidedDocumentsLine');

            $line->appendChild($xml->createElement('cbc:LineID', $n++));
            $line->appendChild($xml->createElement('cbc:DocumentTypeCode', $detalle['TIPO_DOCUMENTO']));
            $line->appendChild($xml->createElement('sac:DocumentSerialID', $detalle['DOCUMENTO_BAJA_SERIE']));
            $line->appendChild($xml->createElement('sac:DocumentNumberID', $detalle['DOCUMENTO_BAJA_NUMERO']));
            $line->appendChild($xml->createElement('sac:VoidReasonDescription', $detalle['BAJA_DESCRIPCION']));

            $root->appendChild($line);
        }


        $xml->formatOutput = TRUE;
        $xml->preserveWhiteSpace = TRUE;
        $xml->saveXML();
        if (!file_exists($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'))) {
            mkdir($this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO'));
        }

        $file = $this->emisor->get('NRO_DOCUMENTO') . '-RA-' . date('Ymd', strtotime($cabecera['FECHA_EMISION'])) . '-' . $cabecera['NUMERO_DOCUMENTO'];
        $path = $this->emisor->getPathXml() . DIRECTORY_SEPARATOR . $this->emisor->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . $file . '.XML';
        $xml->save($path);

        $response = $this->emisor->sign($file, 0);
        if ($response['CODIGO'] == 0) {
            $hash_cpe = $response['HASH_CPE'];
            $response = $this->emisor->sendBill($file);
            $response['HASH_CPE'] = $hash_cpe;
        }
        return $response;
    }


}