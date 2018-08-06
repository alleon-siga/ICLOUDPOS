<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require_once APPPATH . 'libraries' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';

class Mailer extends PHPMailer
{

    public function __construct($params = array())
    {
        parent::__construct(null);

        $this->isHtml(true);
        $this->CharSet = 'UTF-8';

        if (isset($params['from']) && isset($params['name']))
            $this->setFrom($params['from'], $params['name']);

        if (isset($params['protocol'])) {
            if (strtoupper($params['protocol']) == 'SMTP') {
                $this->SMTPDebug = 0;

                if (isset($params['smtp_host']))
                    $this->isSMTP();
                $this->Host = $params['smtp_host'];
                $this->SMTPAuth = true;

                if (isset($params['smtp_user']))
                    $this->Username = $params['smtp_user'];

                if (isset($params['smtp_pass']))
                    $this->Password = $params['smtp_pass'];

                if (isset($params['smtp_port']))
                    $this->Port = $params['smtp_port'];

                $this->SMTPSecure = "tls";
            }

        }


    }
}