<?php

/**
 * Created by PhpStorm.
 * User: toni
 * Date: 5/10/2018
 * Time: 12:08 PM
 */
class SunatWS
{

    // Crea el xml emisor para Factura o Boleta
    public function crearXmlFB($data_comprobante, $items_detalle, $ruta)
    {
        $validacion = new Validacion();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';

        $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?>
                    <Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <ext:UBLExtensions>
                    <ext:UBLExtension>
                    <ext:ExtensionContent>
                    <sac:AdditionalInformation>
                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1001</cbc:ID>
                            <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_GRAVADAS"] . '</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1002</cbc:ID>
                            <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_INAFECTA"] . '</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1003</cbc:ID>
                            <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_EXONERADAS"] . '</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>
                        <sac:AdditionalMonetaryTotal>
                            <cbc:ID>1004</cbc:ID>
                            <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_GRATUITAS"] . '</cbc:PayableAmount>
                        </sac:AdditionalMonetaryTotal>';
        if ($data_comprobante["TOTAL_LETRAS"] <> "") {
            $xmlCPE = $xmlCPE .
                '<sac:AdditionalProperty>
                            <cbc:ID>1000</cbc:ID>
                            <cbc:Value>' . $data_comprobante["TOTAL_LETRAS"] . '</cbc:Value>
                        </sac:AdditionalProperty>';
        }
        if ($data_comprobante["TOTAL_GRATUITAS"] > 0) {
            $xmlCPE = $xmlCPE .
                '<sac:AdditionalProperty>
                            <cbc:ID>1002</cbc:ID>
                            <cbc:Value>TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE</cbc:Value>
                        </sac:AdditionalProperty>';
        }
        $xmlCPE = $xmlCPE . '</sac:AdditionalInformation>
                    </ext:ExtensionContent>
                    </ext:UBLExtension>
                    <ext:UBLExtension>
                    <ext:ExtensionContent>
                    </ext:ExtensionContent>
                    </ext:UBLExtension>
                    </ext:UBLExtensions>
                    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
                    <cbc:CustomizationID>1.0</cbc:CustomizationID>
                    <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
                    <cbc:IssueDate>' . $data_comprobante["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
                    <cbc:InvoiceTypeCode>' . $data_comprobante["COD_TIPO_DOCUMENTO"] . '</cbc:InvoiceTypeCode>
                    <cbc:DocumentCurrencyCode>' . $data_comprobante["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>                    
                    <cac:Signature>
                        <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
                        <cac:SignatoryParty>
                            <cac:PartyIdentification>
                                <cbc:ID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
                            </cac:PartyIdentification>                        
                            <cac:PartyName>
                                <cbc:Name><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
                            </cac:PartyName>                        
                        </cac:SignatoryParty>
                        <cac:DigitalSignatureAttachment>
                            <cac:ExternalReference>
                                <cbc:URI>#' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:URI>
                            </cac:ExternalReference>
                        </cac:DigitalSignatureAttachment>
                    </cac:Signature>                
                    <cac:AccountingSupplierParty>
                        <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
                        <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
                        <cac:Party>
                            <cac:PartyName>
                                <cbc:Name><![CDATA[' . $validacion->limpiar_caracteres($data_comprobante["NOMBRE_COMERCIAL_EMPRESA"]) . ']]></cbc:Name>
                            </cac:PartyName>
                            <cac:PostalAddress>
                                <cbc:ID>' . $data_comprobante["CODIGO_UBIGEO_EMPRESA"] . '</cbc:ID>
                                <cbc:StreetName><![CDATA[' . $data_comprobante["DIRECCION_EMPRESA"] . ']]></cbc:StreetName>
                                <cbc:CitySubdivisionName/>
                                <cbc:CityName><![CDATA[' . $data_comprobante["DEPARTAMENTO_EMPRESA"] . ']]></cbc:CityName>
                                <cbc:CountrySubentity><![CDATA[' . $data_comprobante["PROVINCIA_EMPRESA"] . ']]></cbc:CountrySubentity>
                                <cbc:District><![CDATA[' . $data_comprobante["DISTRITO_EMPRESA"] . ']]></cbc:District>
                                <cac:Country>
                                    <cbc:IdentificationCode>' . $data_comprobante["CODIGO_PAIS_EMPRESA"] . '</cbc:IdentificationCode>
                                </cac:Country>
                            </cac:PostalAddress>
                            <cac:PartyLegalEntity>
                            <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:AccountingSupplierParty>
                    <cac:AccountingCustomerParty>
                        <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CustomerAssignedAccountID>
                        <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_CLIENTE"] . '</cbc:AdditionalAccountID>
                        <cac:Party>
                            <cac:PhysicalLocation>
                                <cbc:Description><![CDATA[' . $data_comprobante["DIRECCION_CLIENTE"] . ']]></cbc:Description>
                            </cac:PhysicalLocation>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
                                <cac:RegistrationAddress>
                                    <cbc:StreetName><![CDATA[' . $data_comprobante["CIUDAD_CLIENTE"] . ']]></cbc:StreetName>
                                    <cac:Country>
                                        <cbc:IdentificationCode>' . $data_comprobante["COD_PAIS_CLIENTE"] . '</cbc:IdentificationCode>
                                    </cac:Country>
                                </cac:RegistrationAddress>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:AccountingCustomerParty>
                    <cac:TaxTotal>
                        <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                            <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
                            <cac:TaxCategory>
                                <cac:TaxScheme>
                                    <cbc:ID>1000</cbc:ID>
                                    <cbc:Name>IGV</cbc:Name>
                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                                </cac:TaxCategory>
                        </cac:TaxSubtotal>
                    </cac:TaxTotal>
                    <cac:LegalMonetaryTotal>
                        <cbc:LineExtensionAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["SUB_TOTAL"] . '</cbc:LineExtensionAmount>
                        <cbc:TaxExclusiveAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxExclusiveAmount>
                        <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL"] . '</cbc:PayableAmount>
                    </cac:LegalMonetaryTotal>';

        foreach ($items_detalle as $item) {
            $xmlCPE = $xmlCPE . '<cac:InvoiceLine>
                                    <cbc:ID>' . $item->txt_item . '</cbc:ID>
                                    <cbc:InvoicedQuantity unitCode="' . $item->txt_unidad_medida . '">' . $item->txt_cantidad . '</cbc:InvoicedQuantity>
                                    <cbc:LineExtensionAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_importe . '</cbc:LineExtensionAmount>
                                    <cac:PricingReference>
                                        <cac:AlternativeConditionPrice>
                                            <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
                                            <cbc:PriceTypeCode>' . $item->txt_precio_tipo_codigo . '</cbc:PriceTypeCode>
                                        </cac:AlternativeConditionPrice>
                                    </cac:PricingReference>
                                    <cac:TaxTotal>
                                        <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
                                        <cac:TaxSubtotal>
                                            <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
                                            <cac:TaxCategory>
                                                <cbc:TaxExemptionReasonCode>' . $item->txt_cod_tipo_operacion . '</cbc:TaxExemptionReasonCode>
                                                <cac:TaxScheme>
                                                    <cbc:ID>1000</cbc:ID>
                                                    <cbc:Name>IGV</cbc:Name>
                                                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                                </cac:TaxScheme>
                                            </cac:TaxCategory>
                                        </cac:TaxSubtotal>
                                    </cac:TaxTotal>
                                    <cac:Item>
                                        <cbc:Description><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_descripcion)) ? $item->txt_descripcion : "") . ']]></cbc:Description>
                                        <cac:SellersItemIdentification>
                                            <cbc:ID><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_codigo)) ? $item->txt_codigo : "") . ']]></cbc:ID>
                                        </cac:SellersItemIdentification>
                                    </cac:Item>
                                    <cac:Price>
                                        <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
                                    </cac:Price>
                                </cac:InvoiceLine>';
        }

        $xmlCPE = $xmlCPE . '</Invoice>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');
        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    // Crea el xml emisor para Notas de Credito
    function crearXmlNotaCredito($data_comprobante, $items_detalle, $ruta)
    {
        $validacion = new Validacion();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><CreditNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ext:UBLExtensions>
    <ext:UBLExtension>
    <ext:ExtensionContent>
        <sac:AdditionalInformation>
            <sac:AdditionalMonetaryTotal>
                <cbc:ID>1001</cbc:ID>
                <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_GRAVADAS"] . '</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
        </sac:AdditionalInformation>
    </ext:ExtensionContent>
    </ext:UBLExtension>
    <ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
    </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
    <cbc:CustomizationID>1.0</cbc:CustomizationID>
    <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
    <cbc:IssueDate>' . $data_comprobante["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
    <cbc:DocumentCurrencyCode>' . $data_comprobante["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>' . $data_comprobante["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ReferenceID>
        <cbc:ResponseCode>' . $data_comprobante["COD_TIPO_MOTIVO"] . '</cbc:ResponseCode>
        <cbc:Description><![CDATA[' . $data_comprobante["DESCRIPCION_MOTIVO"] . ']]></cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>' . $data_comprobante["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ID>
            <cbc:DocumentTypeCode>' . $data_comprobante["TIPO_COMPROBANTE_MODIFICA"] . '</cbc:DocumentTypeCode>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:Signature>
        <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <cac:AccountingSupplierParty>
        <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyName>
                <cbc:Name><![CDATA[' . $data_comprobante["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
            </cac:PartyName>
            <cac:PostalAddress>
                <cbc:ID>' . $data_comprobante["CODIGO_UBIGEO_EMPRESA"] . '</cbc:ID>
                <cbc:StreetName><![CDATA[' . $data_comprobante["DIRECCION_EMPRESA"] . ']]></cbc:StreetName>
                <cbc:CitySubdivisionName/>
                <cbc:CityName><![CDATA[' . $data_comprobante["DEPARTAMENTO_EMPRESA"] . ']]></cbc:CityName>
                <cbc:CountrySubentity><![CDATA[' . $data_comprobante["PROVINCIA_EMPRESA"] . ']]></cbc:CountrySubentity>
                <cbc:District><![CDATA[' . $data_comprobante["DISTRITO_EMPRESA"] . ']]></cbc:District>
                <cac:Country>
                    <cbc:IdentificationCode>' . $data_comprobante["CODIGO_PAIS_EMPRESA"] . '</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CustomerAssignedAccountID>
        <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_CLIENTE"] . '</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PhysicalLocation>
                <cbc:Description><![CDATA[' . $data_comprobante["DIRECCION_CLIENTE"] . ']]></cbc:Description>
            </cac:PhysicalLocation>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
                <cac:RegistrationAddress>
                    <cbc:StreetName><![CDATA[' . $data_comprobante["CIUDAD_CLIENTE"] . ']]></cbc:StreetName>
                    <cac:Country>
                        <cbc:IdentificationCode>' . $data_comprobante["COD_PAIS_CLIENTE"] . '</cbc:IdentificationCode>
                    </cac:Country>
                </cac:RegistrationAddress>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL"] . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>';

        foreach ($items_detalle as $item) {
            $xmlCPE = $xmlCPE . '<cac:CreditNoteLine>
                                    <cbc:ID>' . $item->txt_item . '</cbc:ID>
                                    <cbc:CreditedQuantity unitCode="' . $item->txt_unidad_medida . '">' . $item->txt_cantidad . '</cbc:CreditedQuantity>
                                    <cbc:LineExtensionAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_importe . '</cbc:LineExtensionAmount>
                                    <cac:PricingReference>
                                        <cac:AlternativeConditionPrice>
                                            <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
                                            <cbc:PriceTypeCode>' . $item->txt_precio_tipo_codigo . '</cbc:PriceTypeCode>
                                        </cac:AlternativeConditionPrice>
                                    </cac:PricingReference>
                                    <cac:TaxTotal>
                                        <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
                                        <cac:TaxSubtotal>
                                        <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
                                        <cac:TaxCategory>
                                            <cbc:TaxExemptionReasonCode>' . $item->txt_cod_tipo_operacion . '</cbc:TaxExemptionReasonCode>
                                            <cac:TaxScheme>
                                                <cbc:ID>1000</cbc:ID>
                                                <cbc:Name>IGV</cbc:Name>
                                                <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                                            </cac:TaxScheme>
                                        </cac:TaxCategory>
                                        </cac:TaxSubtotal>
                                    </cac:TaxTotal>
                                    <cac:Item>
                                        <cbc:Description><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_descripcion)) ? $item->txt_descripcion : "") . ']]></cbc:Description>
                                        <cac:SellersItemIdentification>
                                            <cbc:ID><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_codigo)) ? $item->txt_codigo : "") . ']]></cbc:ID>
                                        </cac:SellersItemIdentification>
                                    </cac:Item>
                                    <cac:Price>
                                        <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
                                    </cac:Price>
                                    </cac:CreditNoteLine>';
        }

        $xmlCPE = $xmlCPE . '</CreditNote>';
        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');

        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    // Crea el xml emisor para Notas de Debito
    public function crearXmlNotaDebito($data_comprobante, $items_detalle, $ruta)
    {
        $validacion = new Validacion();
        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'ISO-8859-1';
        $xmlCPE = '<?xml version="1.0" encoding="ISO-8859-1" standalone="no"?><DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <ext:UBLExtensions>
            <ext:UBLExtension>
            <ext:ExtensionContent>
            <sac:AdditionalInformation>
            <sac:AdditionalMonetaryTotal>
            <cbc:ID>1001</cbc:ID>
            <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_GRAVADAS"] . '</cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
            </sac:AdditionalInformation>
            </ext:ExtensionContent>
            </ext:UBLExtension>
            <ext:UBLExtension>
            <ext:ExtensionContent>
            </ext:ExtensionContent>
            </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.0</cbc:UBLVersionID>
            <cbc:CustomizationID>1.0</cbc:CustomizationID>
            <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
            <cbc:IssueDate>' . $data_comprobante["FECHA_DOCUMENTO"] . '</cbc:IssueDate>
            <cbc:DocumentCurrencyCode>' . $data_comprobante["COD_MONEDA"] . '</cbc:DocumentCurrencyCode>
            <cac:DiscrepancyResponse>
                <cbc:ReferenceID>' . $data_comprobante["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ReferenceID>
                <cbc:ResponseCode>' . $data_comprobante["COD_TIPO_MOTIVO"] . '</cbc:ResponseCode>
                <cbc:Description><![CDATA[' . $data_comprobante["DESCRIPCION_MOTIVO"] . ']]></cbc:Description>
            </cac:DiscrepancyResponse>
            <cac:BillingReference>
                <cac:InvoiceDocumentReference>
                    <cbc:ID>' . $data_comprobante["NRO_DOCUMENTO_MODIFICA"] . '</cbc:ID>
                    <cbc:DocumentTypeCode>' . $data_comprobante["TIPO_COMPROBANTE_MODIFICA"] . '</cbc:DocumentTypeCode>
                </cac:InvoiceDocumentReference>
            </cac:BillingReference>
            <cac:Signature>
                <cbc:ID>' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:ID>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#' . $data_comprobante["NRO_COMPROBANTE"] . '</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
            <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_EMPRESA"] . '</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_EMPRESA"] . '</cbc:AdditionalAccountID>
                <cac:Party>
                    <cac:PartyName>
                        <cbc:Name><![CDATA[' . $data_comprobante["NOMBRE_COMERCIAL_EMPRESA"] . ']]></cbc:Name>
                    </cac:PartyName>
                    <cac:PostalAddress>
                        <cbc:ID>' . $data_comprobante["CODIGO_UBIGEO_EMPRESA"] . '</cbc:ID>
                        <cbc:StreetName><![CDATA[' . $data_comprobante["DIRECCION_EMPRESA"] . ']]></cbc:StreetName>
                        <cbc:CitySubdivisionName/>
                        <cbc:CityName><![CDATA[' . $data_comprobante["DEPARTAMENTO_EMPRESA"] . ']]></cbc:CityName>
                        <cbc:CountrySubentity><![CDATA[' . $data_comprobante["PROVINCIA_EMPRESA"] . ']]></cbc:CountrySubentity>
                        <cbc:District><![CDATA[' . $data_comprobante["DISTRITO_EMPRESA"] . ']]></cbc:District>
                        <cac:Country>
                            <cbc:IdentificationCode>' . $data_comprobante["CODIGO_PAIS_EMPRESA"] . '</cbc:IdentificationCode>
                        </cac:Country>
                    </cac:PostalAddress>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_EMPRESA"] . ']]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
                <cbc:CustomerAssignedAccountID>' . $data_comprobante["NRO_DOCUMENTO_CLIENTE"] . '</cbc:CustomerAssignedAccountID>
                <cbc:AdditionalAccountID>' . $data_comprobante["TIPO_DOCUMENTO_CLIENTE"] . '</cbc:AdditionalAccountID>
                <cac:Party>
                    <cac:PhysicalLocation>
                        <cbc:Description><![CDATA[' . $data_comprobante["DIRECCION_CLIENTE"] . ']]></cbc:Description>
                    </cac:PhysicalLocation>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA[' . $data_comprobante["RAZON_SOCIAL_CLIENTE"] . ']]></cbc:RegistrationName>
                        <cac:RegistrationAddress>
                            <cbc:StreetName><![CDATA[' . $data_comprobante["CIUDAD_CLIENTE"] . ']]></cbc:StreetName>
                            <cac:Country>
                                <cbc:IdentificationCode>' . $data_comprobante["COD_PAIS_CLIENTE"] . '</cbc:IdentificationCode>
                            </cac:Country>
                        </cac:RegistrationAddress>
                    </cac:PartyLegalEntity>
                </cac:Party>
            </cac:AccountingCustomerParty>
            <cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL_IGV"] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cac:TaxScheme>
                            <cbc:ID>1000</cbc:ID>
                            <cbc:Name>IGV</cbc:Name>
                            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>
            </cac:TaxTotal>
            <cac:RequestedMonetaryTotal>
               <cbc:PayableAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $data_comprobante["TOTAL"] . '</cbc:PayableAmount>
            </cac:RequestedMonetaryTotal>';

        foreach ($items_detalle as $item) {
            $xmlCPE = $xmlCPE . '<cac:DebitNoteLine>
            <cbc:ID>' . $item->txt_item . '</cbc:ID>
            <cbc:DebitedQuantity unitCode="' . $item->txt_unidad_medida . '">' . $item->txt_cantidad . '</cbc:DebitedQuantity>
            <cbc:LineExtensionAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_importe . '</cbc:LineExtensionAmount>
            <cac:PricingReference>
            <cac:AlternativeConditionPrice>
            <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
            <cbc:PriceTypeCode>' . $item->txt_precio_tipo_codigo . '</cbc:PriceTypeCode>
            </cac:AlternativeConditionPrice>
            </cac:PricingReference>
            <cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
            <cac:TaxSubtotal>
            <cbc:TaxAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_igv . '</cbc:TaxAmount>
            <cac:TaxCategory>
            <cbc:TaxExemptionReasonCode>' . $item->txt_cod_tipo_operacion . '</cbc:TaxExemptionReasonCode>
            <cac:TaxScheme>
            <cbc:ID>1000</cbc:ID>
            <cbc:Name>IGV</cbc:Name>
            <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
            </cac:TaxScheme>
            </cac:TaxCategory>
            </cac:TaxSubtotal>
            </cac:TaxTotal>
            <cac:Item>
            <cbc:Description><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_descripcion)) ? $item->txt_descripcion : "") . ']]></cbc:Description>
            <cac:SellersItemIdentification>
            <cbc:ID><![CDATA[' . $validacion->limpiar_caracteres((isset($item->txt_codigo)) ? $item->txt_codigo : "") . ']]></cbc:ID>
            </cac:SellersItemIdentification>
            </cac:Item>
            <cac:Price>
            <cbc:PriceAmount currencyID="' . $data_comprobante["COD_MONEDA"] . '">' . $item->txt_precio . '</cbc:PriceAmount>
            </cac:Price>
            </cac:DebitNoteLine>';
        }

        $xmlCPE = $xmlCPE . '</DebitNote>';

        $doc->loadXML($xmlCPE);
        $doc->save($ruta . '.XML');

        $resp['respuesta'] = 'ok';
        $resp['url_xml'] = $ruta . '.XML';
        return $resp;
    }

    // Envia el documento a la SUNAT
    public function enviarDocumento($ruc, $usuario_sol, $pass_sol, $ruta_archivo, $ruta_archivo_cdr, $archivo, $ruta_ws)
    {
        //=================ZIPEAR ================
        $zip = new ZipArchive();
        $filenameXMLCPE = $ruta_archivo . '.ZIP';

        if ($zip->open($filenameXMLCPE, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($ruta_archivo . '.XML', $archivo . '.XML'); //ORIGEN, DESTINO
            $zip->close();
        }

        //===================ENVIO FACTURACION=====================
        $soapUrl = $ruta_ws;
        // xml post structure
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
        xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <soapenv:Header>
            <wsse:Security>
                <wsse:UsernameToken>
                    <wsse:Username>' . $ruc . $usuario_sol . '</wsse:Username>
                    <wsse:Password>' . $pass_sol . '</wsse:Password>
                </wsse:UsernameToken>
            </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
            <ser:sendBill>
                <fileName>' . $archivo . '.ZIP</fileName>
                <contentFile>' . base64_encode(file_get_contents($ruta_archivo . '.ZIP')) . '</contentFile>
            </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

//        var_dump($response);
        if ($httpcode == 200) {
            $doc = new DOMDocument();
            $doc->loadXML($response);

            //===================VERIFICAMOS SI HA ENVIADO CORRECTAMENTE EL COMPROBANTE=====================
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                file_put_contents($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP', base64_decode($xmlCDR));

                //extraemos archivo zip a xml
                $zip = new ZipArchive;
                if ($zip->open($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP') === TRUE) {
                    $zip->extractTo($ruta_archivo_cdr, 'R-' . $archivo . '.XML');
                    $zip->close();
                }

                //eliminamos los archivos Zipeados
                unlink($ruta_archivo . '.ZIP');
                unlink($ruta_archivo_cdr . 'R-' . $archivo . '.ZIP');

                //=============hash CDR=================
                $doc_cdr = new DOMDocument();
                $doc_cdr->load($ruta_archivo_cdr . 'R-' . $archivo . '.XML');
                $resp['respuesta'] = 'ok';
                $resp['cod_sunat'] = $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue;
                $resp['mensaje'] = $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue;
                $resp['hash_cdr'] = $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            } else {
                $resp['respuesta'] = 'error';
                $resp['cod_sunat'] = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $resp['mensaje'] = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                $resp['hash_cdr'] = "";
            }
        } else {
            //echo "no responde web";
            $resp['respuesta'] = 'error';
            $resp['cod_sunat'] = "0000";
            $resp['mensaje'] = $response == 0 ? 'SUNAT FUERA DE SERVICIO' : $response;
            $resp['hash_cdr'] = "";
        }
        return $resp;
    }
}