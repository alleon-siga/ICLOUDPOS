<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class proveedor extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('local/local_model');
            $this->load->model('monedas/monedas_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }



    /** carga cuando listas los proveedores*/
    function index($action = '')
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $local_id = $this->session->userdata('id_local');
        switch ($action) {
            case 'pdf': {
                $data['lists'] = $this->proveedor_model->get_all();
                $local = $this->db->get_where('local', array('int_local_id' => $local_id))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;
                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/proveedor/proveedor_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $local = $this->db->get_where('local', array('int_local_id' => $local_id))->row();
                $data['local_nombre'] = $local->local_nombre;
                $data['local_direccion'] = $local->direccion;
                $data['lists'] = $this->proveedor_model->get_all();
                echo $this->load->view('menu/proveedor/proveedor_list_excel', $data, true);
                break;
            }
            default: {
                $data['proveedores'] = $this->proveedor_model->get_all();
                $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/proveedor', $data, true);

                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                }else{
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['proveedor'] = $this->proveedor_model->get_by('id_proveedor', $id);
        }
        $this->load->view('menu/proveedor/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $proveedor = array(
            'proveedor_nombre' => $this->input->post('proveedor_nombre'),
            'proveedor_direccion1' => $this->input->post('proveedor_direccion1'),
            'proveedor_ruc' => $this->input->post('proveedor_nrofax'),
            'proveedor_paginaweb' => $this->input->post('proveedor_paginaweb'),
            'proveedor_email' => $this->input->post('proveedor_email'),
            'proveedor_telefono1' => $this->input->post('proveedor_telefono1'),
            'proveedor_telefono2' => $this->input->post('proveedor_telefono2'),
            'proveedor_observacion' => $this->input->post('proveedor_observacion'),
            'proveedor_contacto' => $this->input->post('proveedor_direccion2'),
        );

        if (empty($id)) {
            $resultado = $this->proveedor_model->insertar($proveedor);
        }
        else{
            $proveedor['id_proveedor'] = $id;
            $resultado = $this->proveedor_model->update($proveedor);
        }

        if ($resultado == TRUE) {
            $json['id']=$resultado;
            $json['nombre']=$this->input->post('proveedor_nombre');
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        if($resultado===NOMBRE_EXISTE){
            //  $this->session->set_flashdata('error', NOMBRE_EXISTE);
            $json['error']= NOMBRE_EXISTE;
        }
        echo json_encode($json);

    }



    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $proveedor = array(
            'id_proveedor' => $id,
            'proveedor_nombre' => $nombre . time(),
            'proveedor_status' => 0

        );


        $data['resultado'] = $this->proveedor_model->verifProdIngr($proveedor);
        if($data['resultado'] == false){

            $data['resultado'] = $this->proveedor_model->update($proveedor);

            if ($data['resultado'] != FALSE) {

                $json['success']  = 'Se ha eliminado exitosamente';


            } else {

                $json['error'] = 'Ha ocurrido un error al eliminar el Proveedor';
            }
        }else{
                $json['warning']= 'No se puede eliminar el proveedor, tiene '.$data['resultado'].' relacionado';

        }
       echo json_encode($json);
    }


    public function cuentas_por_pagar(){
        $this->load->model('local/local_model');
        $this->load->model('monedas/monedas_model');

        $data["lstproveedor"] = $this->proveedor_model->get_all();
        $data['monedas'] = $this->monedas_model->get_monedas_activas();
        $data["locales"] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/cuentasporpagar', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function calendarioCuentasPagar($action = '')
    {
        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $params['proveedor'] = $this->input->post('proveedor');
                $params['moneda'] = $this->input->post('moneda');
                $params['tipo'] = $this->input->post('tipo');
                $data['lists'] = $this->proveedor_model->get_cronograma($params);
                $this->load->view('menu/proveedor/calendarioCuentasPagar_list', $data);
                break;
            }
            case 'pdf': {
            }
            case 'excel': {
            }
            default: {
                $usu = $this->session->userdata('nUsuCodigo');
                $data["lstproveedor"] = $this->proveedor_model->get_all();
                $data['monedas'] = $this->monedas_model->get_monedas_activas();
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }
                $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/calendarioCuentasPagar', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }
}