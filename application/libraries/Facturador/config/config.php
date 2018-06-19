<?php

//Servicio Factura  Electrónica
$config['soap_url_prod'] = "https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService";
$config['soap_url_beta'] = "https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService";

// WS de consulta de CDR y estado de envio
$config['soap_url_cdr'] = 'https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl';
