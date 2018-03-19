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
            if ($logo == 1) {
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

        if (!empty($_FILES) and $_FILES['userfile']['size'] != '0') {

            $this->load->library('upload');
            $files = $_FILES;
            $contador = 1;
            $mayor = 0;

            $directorio = './recursos/img/logo/';

            if (is_dir($directorio)) {
                $arreglo_img = scandir($directorio);
                natsort($arreglo_img);
                $mayor = array_pop($arreglo_img);
                $mayor = substr($mayor, 0, -4);
            } else {
                $arreglo_img[0] = ".";
            }
            $sumando = 1;
            for ($j = 0; $j < count($files['userfile']['name']); $j++) {

                if ($files['userfile']['name'][$j] != "") {

                    if ($arreglo_img[0] == ".") {
                        $contador = $mayor + ($sumando);
                        $sumando++;
                    }
                    $_FILES ['userfile'] ['name'] = $files ['userfile'] ['name'][$j];
                    $_FILES ['userfile'] ['type'] = $files ['userfile'] ['type'][$j];
                    $_FILES ['userfile'] ['tmp_name'] = $files ['userfile'] ['tmp_name'][$j];
                    $_FILES ['userfile'] ['error'] = $files ['userfile'] ['error'][$j];
                    $_FILES ['userfile'] ['size'] = $files ['userfile'] ['size'][$j];

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

                    $this->upload->initialize($this->set_upload_options($contador, $extension));
                    $this->upload->do_upload();
                    return $contador . '.' . $extension;
                    $contador++;
                } else {

                }
            }
        }
    }

    function set_upload_options($name, $extension)
    {
        // upload an image options
        $this->load->helper('path');
        $dir = './recursos/img/logo/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755);
        }
        $config = array();
        $config ['upload_path'] = $dir;
        //$config ['file_path'] = './prueba/';
        $config ['allowed_types'] = $extension;
        $config ['max_size'] = '0';
        $config ['overwrite'] = TRUE;
        $config ['file_name'] = $name;

        return $config;
    }

}