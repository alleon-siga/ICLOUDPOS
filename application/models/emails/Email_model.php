<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class email_model extends CI_Model
{
    protected $_error;

    public function __construct()
    {
        parent::__construct();
        $this->_error = null;
        $this->config->load('email');
        $email_cotizacion = $this->config->item('email_cotizacion');
        $this->load->model('venta_new/venta_new_model');
        $this->load->model('facturacion/facturacion_model');
        $this->load->library('mailer', $email_cotizacion);
    }

    public function enviarCotizacion($param)
    {
        foreach($param['correo'] as $correo){
            $this->mailer->addAddress($correo, 'prueba');    
        }
        $this->mailer->Subject = $param['asunto'];
        $this->mailer->SMTPSecure = 'TLS';
        $this->mailer->msgHTML($this->load->view('menu/emails/enviarCotizacion', array(), TRUE));
        $this->mailer->AltBody = "This is the plain text version of the email content";

        $data['tipo_cliente'] = $param['tipoCliente'];
        $data['cotizar'] = $this->cotizar_model->get_cotizar_detalle($param['idCotizacion']);
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/cotizar/cotizar_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $url = $mpdf->Output("temporal.pdf", 'S');

        $this->mailer->addStringAttachment($url, 'COTIZACION_'.$param['idCotizacion'].'.pdf');

        if (!$this->mailer->send()){
            $datos['error'] = true;
            $datos['mensaje'] = 'Mensaje no pudo ser enviado: ' . $this->mailer->ErrorInfo;
        }else{
            $datos['error'] = false;
            $datos['mensaje'] = 'Cotizacion enviada con exito.';
        }
        return $datos;
    }

    public function enviarVenta($param)
    {
        foreach($param['correo'] as $correo){
            $this->mailer->addAddress($correo, 'prueba');    
        }
        $this->mailer->Subject = $param['asunto'];
        $this->mailer->SMTPSecure = 'TLS';

        $datos['razon_social'] = $param['razon_social'];

        $this->mailer->msgHTML($this->load->view('menu/emails/enviarVenta', $datos, TRUE));
        $this->mailer->AltBody = "This is the plain text version of the email content";

        $data['tipo_cliente'] = $param['tipoCliente'];
        $data['venta'] = $this->venta_new_model->get_venta_detalle($param['idVenta']);
        $data['identificacion'] = $this->db->get_where('configuraciones', array('config_key' => 'EMPRESA_IDENTIFICACION'))->row();
        $total = $data['venta']->total;
        $data['totalLetras'] = numtoletras($total, $moneda->nombre);

        $this->load->library('mpdf53/mpdf');
        foreach($param['tipo'] as $tipo){
            if($tipo == 'NV'){ //nota de venta
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                if (SERVER_NAME == SERVER_CRDIGITAL) {
                    $html = $this->load->view('menu/venta/impresiones/nota_pedido_crdigital', $data, true);
                } else {
                    $html = $this->load->view('menu/venta/impresiones/nota_pedido_a4', $data, true);
                }
                $mpdf->WriteHTML($html);
                $url = $mpdf->Output("temporal.pdf", 'S');

                $this->mailer->addStringAttachment($url, 'NV'.$data['venta']->serie.'-'.sumCod($param['idVenta'], 6).'.pdf');
            }elseif($tipo=='CE'){ //comprobante electronico
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $param['idFacturacion']));
                $data['emisor'] = $this->facturacion_model->get_emisor();

                $html = $this->load->view('menu/facturacion/impresion_a4', $data, true);
                $mpdf->WriteHTML($html);
                $url = $mpdf->Output("temporal.pdf", 'S');
                $this->mailer->addStringAttachment($url, $data['facturacion']->documento_numero_ceros.'.pdf');

                //En formato xml
                $emisor = $this->db->get('facturacion_emisor')->row();
                $f = $this->db->get_where('facturacion', array('id' => $param['idFacturacion']))->row();
                $name = $emisor->ruc . '-' . $f->documento_tipo . '-' . $f->documento_numero . '.XML';
                $this->mailer->addAttachment('./application/libraries/Facturador/files/xmls/' . $emisor->ruc . '/' . $name, $data['facturacion']->documento_numero_ceros.".xml");
            }
        }

        if (!$this->mailer->send()){
            $datos['error'] = true;
            $datos['mensaje'] = 'Mensaje no pudo ser enviado: ' . $this->mailer->ErrorInfo;
        }else{
            $datos['error'] = false;
            $datos['mensaje'] = 'Cotizacion enviada con exito.';
        }
        return $datos;
    }

    public function setError($msg)
    {
        $this->_error = $msg;
    }

    public function getError()
    {
        return $this->_error;
    }
}