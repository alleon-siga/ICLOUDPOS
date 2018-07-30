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
        $this->load->library('mailer', $email_cotizacion);
    }

    public function enviarCotizacion($param)
    {
        $this->mailer->addAddress($param['correo'], 'prueba');
        $this->mailer->Subject = valueOption('EMPRESA_NOMBRE')." COTIZACION";

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

        if (!$this->mailer->send()) {
            $this->setError('EL correo no pudo ser enviado');
            log_message('error', 'Message could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo);
            var_dump(log_message('error', 'Message could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo));
            return FALSE;
        } else {
            log_message('info', 'El correo de confirmacion ha sido enviado');
            return TRUE;
        }
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