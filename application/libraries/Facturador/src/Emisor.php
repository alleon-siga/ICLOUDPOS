<?php

namespace Facturador;

use Facturador\Core\Config;
use Facturador\Core\Logger;

require_once __DIR__ . '/../lib/signature/XMLSecurityKey.php';
require_once __DIR__ . '/../lib/signature/XMLSecurityDSig.php';
require_once __DIR__ . '/../lib/signature/XMLSecEnc.php';

class Emisor
{
    protected $data;
    protected $path_xml;
    protected $path_cert;
    protected $path_qr;
    protected $soap_url;

    public function __construct($emisor)
    {
        $emisor = Validador::prepareEmisor($emisor);
        $this->data = $emisor;

        $default_root = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        if (!isset($emisor['PATH_XML']))
            $this->path_xml = $default_root . 'files' . DIRECTORY_SEPARATOR . 'xmls';
        else
            $this->path_xml = $emisor['PATH_XML'];

        if (!isset($emisor['PATH_CERT']))
            $this->path_cert = $default_root . 'files' . DIRECTORY_SEPARATOR . 'certificates';
        else
            $this->path_cert = $emisor['PATH_CERT'];

        if (!isset($emisor['PATH_QR']))
            $this->path_qr = $default_root . 'files' . DIRECTORY_SEPARATOR . 'qr_codes';
        else
            $this->path_qr = $emisor['PATH_QR'];
    }

    public function sign($file, $node = 1)
    {
        $file_xml = $this->path_xml . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . $file . '.XML';
        if (!file_exists($file_xml)) {
            Logger::write('error', '-1: Archivo no encontrado. ' . $file_xml);
            return array(
                'CODIGO' => '-1',
                'MENSAJE' => 'Archivo no encontrado. ' . $file_xml,
                'HASH_CPE' => NULL
            );
        }
        $doc = new \DOMDocument();
        try {
            $doc->formatOutput = TRUE;
            $doc->preserveWhiteSpace = TRUE;
            $doc->load($file_xml);

            $objDSig = new \XMLSecurityDSig(FALSE);
            $objDSig->setCanonicalMethod(\XMLSecurityDSig::C14N);
            $options['force_uri'] = TRUE;
            $options['id_name'] = 'ID';
            $options['overwrite'] = FALSE;

            $objDSig->addReference($doc, \XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
            $objKey = new \XMLSecurityKey(\XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

            if (file_exists($this->path_cert . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . '.pfx'))
                $pfx = file_get_contents($this->path_cert . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . '.pfx');
            else{
                Logger::write('error', '-1: Archivo no encontrado. '. $this->path_cert . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . '.pfx');
                return array(
                    'CODIGO' => '-1',
                    'MENSAJE' => 'Certificado no encontrado',
                    'HASH_CPE' => NULL
                );
            }
            $key = array();

            if (openssl_pkcs12_read($pfx, $key, $this->get('CERT_PASS'))) {
                $objKey->loadKey($key["pkey"]);
                $objDSig->add509Cert($key["cert"], TRUE, FALSE);
                $objDSig->sign($objKey, $doc->documentElement->getElementsByTagName("ExtensionContent")->item($node));

                $atributo = $doc->getElementsByTagName('Signature')->item(0);
                if ($atributo != NULL) {
                    $atributo->setAttribute('Id', 'SignatureSP');
                } else {
                    Logger::write('error', '-1: No se pudo recuperar el HASH_CPE');
                    return array(
                        'CODIGO' => '-1',
                        'MENSAJE' => 'No se pudo recuperar el HASH_CPE',
                        'HASH_CPE' => NULL
                    );
                }

                //===================rescatamos Codigo(HASH_CPE)==================
                $hash_cpe = $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue;

                $doc->save($file_xml);

                $comp = explode('-', $file);
                $txt = '';

                if ($comp[1] == '01') {
                    $txt = 'La Factura ' . $comp[2] . '-' . $comp[3] . ' ha sido generada correctamente';
                } elseif ($comp[1] == '03') {
                    $txt = 'La Boleta ' . $comp[2] . '-' . $comp[3] . ' ha sido generada correctamente';
                } elseif ($comp[1] == '07') {
                    $txt = 'La Nota de Credito ' . $comp[2] . '-' . $comp[3] . ' ha sido generada correctamente';
                } elseif ($comp[1] == '08') {
                    $txt = 'La Nota de Debito ' . $comp[2] . '-' . $comp[3] . ' ha sido generada correctamente';
                } else {
                    $txt = 'El comprobante ' . $file . ' ha sido generado correctamente';
                }

                return array(
                    'CODIGO' => '0',
                    'MENSAJE' => $txt,
                    'HASH_CPE' => $hash_cpe
                );
            } else {
                Logger::write('error', '-1: Ha ocurrido un error al firmar el certificado. openssl_pkcs12_read() return FALSE;');
                return array(
                    'CODIGO' => '-1',
                    'MENSAJE' => 'Ha ocurrido un error al firmar el certificado. openssl_pkcs12_read() return FALSE;',
                    'HASH_CPE' => NULL
                );
            }

        } catch (\Exception $e) {
            Logger::write('error', 'Ha ocurrido un error. ' . $e->getMessage());
            return array(
                'CODIGO' => '-1',
                'MENSAJE' => 'Ha ocurrido un error. ' . $e->getMessage(),
                'HASH_CPE' => NULL
            );
        }


    }

    public function sendBill($file_name)
    {
        $zip = new \ZipArchive();
        $file = $this->path_xml . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($file . '.XML')) {
            Logger::write('error', '-1: Archivo no encontrado. ' . $file . '.XML');
            return array(
                'CODIGO' => '-1',
                'MENSAJE' => 'Archivo no encontrado. ' . $file . '.XML',
                'HASH_CPE' => NULL
            );
        }

        if ($zip->open($file . '.ZIP', \ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($file . '.XML', $file_name . '.XML'); //ORIGEN, DESTINO
            $zip->close();
        }

        $xml = new \DOMDocument("1.0", "ISO-8859-1");
        $root = $xml->createElement("soapenv:Envelope");
        $root->setAttribute("xmlns:soapenv", "http://schemas.xmlsoap.org/soap/envelope/");
        $root->setAttribute("xmlns:SOAP-ENV", "http://schemas.xmlsoap.org/soap/envelope/");
        $root->setAttribute("xmlns:ser", "http://service.sunat.gob.pe");
        $root->setAttribute("xmlns:wsse", "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd");
        $xml->appendChild($root);

        //Header
        $header = $xml->createElement('soapenv:Header');

        $user_token = $header->appendChild($xml->createElement('wsse:Security'))
            ->appendChild($xml->createElement('wsse:UsernameToken'));

        $user_token->appendChild(
            $xml->createElement('wsse:Username', $this->get('NRO_DOCUMENTO') . $this->get('SOL_USER')));
        $user_token->appendChild(
            $xml->createElement('wsse:Password', $this->get('SOL_PASS')));

        $root->appendChild($header);

        //Body
        $body = $xml->createElement('soapenv:Body');

        $content = $body->appendChild($xml->createElement('ser:sendBill'));
        $content->appendChild($xml->createElement('fileName', $file_name . '.ZIP'));
        $content->appendChild($xml->createElement('contentFile', base64_encode(file_get_contents($file . '.ZIP'))));

        $root->appendChild($body);

        $xml_post_string = $xml->saveXML();
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-length: " . strlen($xml_post_string),
        );

        if ($this->get('ENV') === 'PROD')
            $soap_url = Config::get('soap_url_prod');
        else
            $soap_url = Config::get('soap_url_beta');

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $soap_url);
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

        if ($httpcode == 200) {
            $doc = new \DOMDocument();
            $doc->loadXML($response);

            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $xmlCDR = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                $path_response = $this->path_xml . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . 'R-' . $file_name;
                file_put_contents($path_response . '.ZIP', base64_decode($xmlCDR));

                $zip = new \ZipArchive;
                if ($zip->open($path_response . '.ZIP') === TRUE) {
                    $zip->extractTo($this->path_xml . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO'), 'R-' . $file_name . '.XML');
                    $zip->close();
                }

                unlink($file . '.ZIP');
                unlink($path_response . '.ZIP');

                $response_xml = $this->path_xml . DIRECTORY_SEPARATOR . $this->get('NRO_DOCUMENTO') . DIRECTORY_SEPARATOR . 'R-' . $file_name . '.XML';
                if (file_exists($response_xml)) {
                    $doc_cdr = new \DOMDocument();
                    $doc_cdr->load($response_xml);
                    return array(
                        'CODIGO' => $doc_cdr->getElementsByTagName('ResponseCode')->item(0)->nodeValue,
                        'MENSAJE' => $doc_cdr->getElementsByTagName('Description')->item(0)->nodeValue,
                        'HASH_CDR' => $doc_cdr->getElementsByTagName('DigestValue')->item(0)->nodeValue,
                    );
                } else {
                    Logger::write('warning', '9999: El comprobante ' . $file_name . ' fue emitido pero no recibio respuesta.');
                    return array(
                        'CODIGO' => '9999',
                        'MENSAJE' => 'El comprobante ' . $file_name . ' fue emitido pero no recibio respuesta.',
                        'HASH_CDR' => NULL,
                    );
                }
            } else {
                unlink($file . '.ZIP');
                if (isset($doc->getElementsByTagName('faultcode')->item(0)->nodeValue)) {
                    $sunat_codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                    $error = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                    Logger::write('error', $sunat_codigo . ': ' . $error);
                    return array(
                        'CODIGO' => $sunat_codigo,
                        'MENSAJE' => $error,
                        'HASH_CDR' => NULL,
                    );
                } else {
                    Logger::write('error', '-3: SUNAT FUERA DE SERVICIO');
                    return array(
                        'CODIGO' => '-3',
                        'MENSAJE' => 'SUNAT FUERA DE SERVICIO',
                        'HASH_CDR' => NULL,
                    );
                }

            }

        } else {
            unlink($file . '.ZIP');
            $doc = new \DOMDocument();
            @$doc->loadXML($response);
            if (isset($doc->getElementsByTagName('faultcode')->item(0)->nodeValue)) {

                $sunat_codigo = $doc->getElementsByTagName('faultcode')->item(0)->nodeValue;
                $error = $doc->getElementsByTagName('faultstring')->item(0)->nodeValue;
                Logger::write('error', $sunat_codigo . ': ' . $error);
                return array(
                    'CODIGO' => $sunat_codigo,
                    'MENSAJE' => $error,
                    'HASH_CDR' => NULL,
                );
            } else {
                Logger::write('error', '-3: SUNAT FUERA DE SERVICIO');
                return array(
                    'CODIGO' => '-3',
                    'MENSAJE' => 'SUNAT FUERA DE SERVICIO',
                    'HASH_CDR' => NULL,
                );
            }

        }

    }

    public function getPathXml()
    {
        return $this->path_xml;
    }

    public function getPathCert()
    {
        return $this->path_cert;
    }

    public function getPathQr()
    {
        return $this->path_qr;
    }

    public function getData()
    {
        return $this->data;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

}