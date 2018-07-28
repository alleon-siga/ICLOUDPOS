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
        //$this->load->model('token_model');
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

    public function sendResetPassword($usuario_id)
    {
        $usuario = $this->usuario_model->findOne(array(
            'id' => $usuario_id,
            'estado' => 2
        ));

        if ($usuario !== FALSE) {
            $token = $this->token_model->findOne(array(
                'usuario_id' => $usuario->id,
                'tipo' => 'RESET_PASSWORD'
            ));

            if ($token !== FALSE) {
                $this->mailer->addAddress($usuario->email, $usuario->nombre . ' ' . $usuario->apellidos);
                $this->mailer->Subject = "Reinicio de contraseña";
                $this->mailer->msgHTML($this->load->view('emails/reset_password', array(
                    'token' => $token,
                    'usuario' => $usuario
                ), TRUE));
                $this->mailer->AltBody = "This is the plain text version of the email content";

                if (!$this->mailer->send()) {
                    $this->setError('No pudo enviarse el correo de reinicio de contraseña');
                    log_message('error', 'Message could not be sent. Mailer Error: ' . $this->mailer->ErrorInfo);
                    $this->usuario_model->log_user($usuario->id, 'error', 'No se pudo enviar el correo de reinicio de password');
                    return FALSE;
                } else {
                    $this->token_model->setByArray(array(
                        'id' => $token->id,
                        'estado' => 1
                    ));
                    $this->token_model->update();
                    $this->usuario_model->log_user($usuario->id, 'info', 'El correo de reinicio de password ha sido enviado');
                    return TRUE;
                }
            }
            $this->setError('Token no valido');
            return FALSE;
        }
        $this->setError('Usuario no valido');
        return FALSE;
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