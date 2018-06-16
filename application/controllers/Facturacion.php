<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturacion extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('facturacion/facturacion_model');
            $this->load->model('local/local_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function test()
    {
//        var_dump($this->facturacion_model->emitir(24));
    }

    function emision($action = '')
    {
        switch ($action) {
            case 'filter': {
                $data['local_id'] = $this->input->post('local_id');
                $data['estado'] = $this->input->post('estado');

                $date_range = explode(" - ", $this->input->post('fecha'));
                $data['fecha_ini'] = str_replace("/", "-", $date_range[0]);
                $data['fecha_fin'] = str_replace("/", "-", $date_range[1]);


                $data['facturaciones'] = $this->facturacion_model->get_facturacion($data);
                $data['emisor'] = $this->facturacion_model->get_emisor();


                echo $this->load->view('menu/facturacion/facturacion_list', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();

                $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/index', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function get_facturacion_detalle()
    {
        $id = $this->input->post('id');
        $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
        $data['emisor'] = $this->facturacion_model->get_emisor();

        echo $this->load->view('menu/facturacion/facturacion_list_detalle', $data, TRUE);
    }

    function emitir_comprobante()
    {
        $id = $this->input->post('id');

        $resp = $this->facturacion_model->emitir($id);
        $data['facturacion'] = $this->db->get_where('facturacion', array('id' => $id))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function imprimir_ticket($id)
    {

        $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
        $data['emisor'] = $this->facturacion_model->get_emisor();
        $this->load->view('menu/facturacion/impresion_ticket', $data);
    }

    function imprimir($id)
    {
        $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
        $data['emisor'] = $this->facturacion_model->get_emisor();


        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/facturacion/impresion_a4', $data, true);
//        echo $html;
//        return false;
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function emisor()
    {
        $data['emisor'] = $this->db->get('facturacion_emisor')->row();
        $data['departamentos'] = $this->db->get('estados')->result();
        $data['provincias'] = $this->db->get('ciudades')->result();
        $data['distritos'] = $this->db->get('distrito')->result();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/emisor', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_emisor()
    {
        $params = array(
            'ruc' => $this->input->post('ruc'),
            'razon_social' => $this->input->post('razon_social'),
            'nombre_comercial' => $this->input->post('nombre_comercial'),
            'direccion' => $this->input->post('direccion'),
            'departamento_id' => $this->input->post('departamento'),
            'provincia_id' => $this->input->post('provincia'),
            'distrito_id' => $this->input->post('distrito'),
            'ubigeo' => $this->input->post('codigo_ubigeo'),
            'moneda' => $this->input->post('moneda'),
            'env' => 'BETA',
            'user_sol' => $this->input->post('user_sol'),
            'pass_sol' => $this->input->post('pass_sol'),
            'pass_sign' => $this->input->post('pass_sign')
        );

        $this->facturacion_model->save_emisor($params);

        $pfx = $this->upload_image($params['ruc']);
        if ($pfx == 1) {

        }

        echo $params['ruc'];
    }

    function consultarRuc()
    {
        require_once(APPPATH . 'libraries/RucSunat/RucSunat.php');
        $ruc = $this->input->post('ruc');
        $sunat = new RucSunat();
        $emisor = $sunat->consultarRuc($ruc);


        header('Content-Type: application/json');
        if ($emisor != false) {
            echo json_encode(array('emisor' => $emisor));
        } else {
            echo json_encode(array('error' => 1));
        }
    }

    function upload_image($ruc)
    {
        if (isset($_FILES['certificado']) && $_FILES['certificado']['size'] != 0) {
            $extension = "pfx";
            $dir = './application/libraries/Facturador/files/certificates/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755);
            }

            $config = array();
            $config ['upload_path'] = $dir;
            $config['allowed_types'] = $extension;
            //$config ['file_path'] = './prueba/';
            $config ['max_size'] = '0';
            $config ['overwrite'] = TRUE;
            $config ['file_name'] = $ruc;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('certificado')) {
                echo $this->upload->display_errors();
            } else {

            }

            return $ruc . '.' . $extension;

        }
    }

}