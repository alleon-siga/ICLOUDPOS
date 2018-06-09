<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class opciones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('opciones/opciones_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }


    function index($action = 'get')
    {
//        var_dump($_FILES['userfile']);
        $keys = array(
            'EMPRESA_NOMBRE',
            'EMPRESA_CORREO',
            'EMPRESA_CONTACTO',
            'EMPRESA_TELEFONO',
            'EMPRESA_IDENTIFICACION',
            'CODIGO_DEFAULT',
            'VALOR_UNICO',
            'PRECIO_INGRESO',
            'PRODUCTO_SERIE',
            'PAGOS_ANTICIPADOS',
            'ACTIVAR_FACTURACION_VENTA',
            'ACTIVAR_FACTURACION_INGRESO',
            'ACTIVAR_SHADOW',
            'INGRESO_COSTO',
            'INGRESO_UTILIDAD',
            'HOST_IMPRESION'
        );

        if ($action == 'get') {
            $data['configuraciones'] = $this->opciones_model->get_opciones($keys);
            $dataCuerpo['cuerpo'] = $this->load->view('menu/opciones/opciones', $data, true);

            if ($this->input->is_ajax_request()) {
                echo $dataCuerpo['cuerpo'];
            } else {
                $this->load->view('menu/template', $dataCuerpo);
            }
        } elseif ($action == 'save') {

            $configuraciones = array();
            foreach ($keys as $key) {
                $configuraciones[] = array(
                    'config_key' => $key,
                    'config_value' => $this->input->post($key)
                );
            }

            $logo = $this->upload_image();
            if ($logo != '') {
                $configuraciones[] = array(
                    'config_key' => 'EMPRESA_LOGO',
                    'config_value' => $logo
                );
            }
            $result = $this->opciones_model->guardar_configuracion($configuraciones);
            $configuraciones = $this->opciones_model->get_opciones($keys);


            if (count($configuraciones) > 0) {
                foreach ($configuraciones as $configuracion) {
                    $data[$configuracion['config_key']] = $configuracion['config_value'];
                }
                $this->session->set_userdata($data);
            }

            if ($result)
                $json['success'] = 'Las configuraciones se han guardado exitosamente';
            else
                $json['error'] = 'Ha ocurido un error al guardar las configuraciones';

            echo json_encode($json);
        }


    }

    function upload_image()
    {

        if (!empty($_FILES['userfile']) && $_FILES['userfile']['size'] != '0') {
//            var_dump($_FILES['userfile']);
            $directorio = './recursos/img/logo/';

            $size = getimagesize($_FILES ['userfile'] ['tmp_name']);

            switch ($size['mime']) {
                case "image/jpeg":
                    $extension = "jpg";
                    break;
                case "image/png":
                    $extension = "png";
                    break;
                case "image/bmp":
                    $extension = "bmp";
                    break;
            }

            $config = array();
            $config ['upload_path'] = $directorio;
            $config['allowed_types'] = 'jpg|png|bmp|jpeg';
            //$config ['file_path'] = './prueba/';
            $config ['max_size'] = '0';
            $config ['overwrite'] = TRUE;
            $config ['file_name'] = md5(SERVER_NAME) . '.' . $extension;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {
                log_message('error', $this->upload->display_errors());
                echo $this->upload->display_errors();
            } else {
                return md5(SERVER_NAME) . '.' . $extension;
            }
        }
    }

}