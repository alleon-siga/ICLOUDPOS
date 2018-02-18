<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class proveedor extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {        
            $this->load->model('proveedor/proveedor_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }



    /** carga cuando listas los proveedores*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['proveedores'] = $this->proveedor_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/proveedor', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
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

    public function getDatosFromAPI_RUC_DNI(){
        $value=$_POST['RUC_DNI'];
        $lenght=strlen($value);
        //print_r($value);
        //print_r($lenght);
        //die();
        if ($lenght==11) {
            $data = array('ruc' => $value);
            $jsonOutput = json_encode($data);
            $url = 'https://tecactus.com/api/sunat/query/ruc';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonOutput);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept: application/json', 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijg1ODBhYThjMzdkMWI3NTIyOWM5MTc4MWE5YTBhZmMzYmI4OGQ5ZDkxOWY3ZTY0N2I3MzFmYjcxNDBlOWM1MzgxYjhhM2MyMDYyM2MwYjU5In0.eyJhdWQiOiIxIiwianRpIjoiODU4MGFhOGMzN2QxYjc1MjI5YzkxNzgxYTlhMGFmYzNiYjg4ZDlkOTE5ZjdlNjQ3YjczMWZiNzE0MGU5YzUzODFiOGEzYzIwNjIzYzBiNTkiLCJpYXQiOjE1MTg5MDUyOTUsIm5iZiI6MTUxODkwNTI5NSwiZXhwIjoxNTUwNDQxMjk1LCJzdWIiOiIxNDEzIiwic2NvcGVzIjpbInVzZS1yZW5pZWMiLCJ1c2Utc3VuYXQiXX0.MGsW1gEFG0639V7tcWSTMWI-5ecTRbh70nIuVIr5bgxdL2m3aDtHnMTaJaEtXnZ81ZrcT7VEcEAZMmsYOd9nzPv5EZb0rTPH3-uakYmk3GEeMD9ajuuKKZccbwNj1ySZB97vXcIpYBig4L4OLCday08zNlYlwmRWlPfmrwCai-fYo2xASG-fw3oJVp3hUnCdOsylAZp5j5ZYywqwP_j513cad6WGfVliuzW_2wISb4L5_euZFM9fwyQ7Zj3M0s2hR42Qr_d1n7-s86VIBGJhNZrjIZKFhupl0BymgjgCxrwoSRLEXg0URYjtIwAxWxlBdhv3w9PS9pNBRxG-y8hCLEPjb6t-U95D6MuSGTih1M3LRMbnSw4MKXIL_tXCH0TDLqHvSauzLJHIKKn0uJJuPsnI7bdPRJASqipwm6vanDPMcyikP7AEKRYOzCIqgS5g6T7u6pZ-WNkIT9I06WNXEKdk7hYPNnNUwLRRx3E7xN6N7pSlJVEtkkLf6PjQg0F4kRH4FgnLmf_Xd7uD-TqYEiA8jpj-D_fsFiLxIH4vro8wGHKGD4kkJUYTVu3AHV36tddWycKvyNteprcBxtzW9is1a31A0lF2DUY05QPBdnrS0B7Fb2PsWdlrsa2ratqZ_LjlvZADAAoOG5BGz0AKVvj1cj_hfzMnhvIc6XX5Sv8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $resultArray = json_decode($result, TRUE);
            curl_close($ch);
            if (isset($resultArray['razon_social'])) {
                print_r($result);
            }else{
                echo "No existe";
            }
        }elseif ($lenght==8) {
            $data = array('dni' => $value);
            $jsonOutput = json_encode($data);
            $url = 'https://tecactus.com/api/sunat/query/dni';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonOutput);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept: application/json', 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijg1ODBhYThjMzdkMWI3NTIyOWM5MTc4MWE5YTBhZmMzYmI4OGQ5ZDkxOWY3ZTY0N2I3MzFmYjcxNDBlOWM1MzgxYjhhM2MyMDYyM2MwYjU5In0.eyJhdWQiOiIxIiwianRpIjoiODU4MGFhOGMzN2QxYjc1MjI5YzkxNzgxYTlhMGFmYzNiYjg4ZDlkOTE5ZjdlNjQ3YjczMWZiNzE0MGU5YzUzODFiOGEzYzIwNjIzYzBiNTkiLCJpYXQiOjE1MTg5MDUyOTUsIm5iZiI6MTUxODkwNTI5NSwiZXhwIjoxNTUwNDQxMjk1LCJzdWIiOiIxNDEzIiwic2NvcGVzIjpbInVzZS1yZW5pZWMiLCJ1c2Utc3VuYXQiXX0.MGsW1gEFG0639V7tcWSTMWI-5ecTRbh70nIuVIr5bgxdL2m3aDtHnMTaJaEtXnZ81ZrcT7VEcEAZMmsYOd9nzPv5EZb0rTPH3-uakYmk3GEeMD9ajuuKKZccbwNj1ySZB97vXcIpYBig4L4OLCday08zNlYlwmRWlPfmrwCai-fYo2xASG-fw3oJVp3hUnCdOsylAZp5j5ZYywqwP_j513cad6WGfVliuzW_2wISb4L5_euZFM9fwyQ7Zj3M0s2hR42Qr_d1n7-s86VIBGJhNZrjIZKFhupl0BymgjgCxrwoSRLEXg0URYjtIwAxWxlBdhv3w9PS9pNBRxG-y8hCLEPjb6t-U95D6MuSGTih1M3LRMbnSw4MKXIL_tXCH0TDLqHvSauzLJHIKKn0uJJuPsnI7bdPRJASqipwm6vanDPMcyikP7AEKRYOzCIqgS5g6T7u6pZ-WNkIT9I06WNXEKdk7hYPNnNUwLRRx3E7xN6N7pSlJVEtkkLf6PjQg0F4kRH4FgnLmf_Xd7uD-TqYEiA8jpj-D_fsFiLxIH4vro8wGHKGD4kkJUYTVu3AHV36tddWycKvyNteprcBxtzW9is1a31A0lF2DUY05QPBdnrS0B7Fb2PsWdlrsa2ratqZ_LjlvZADAAoOG5BGz0AKVvj1cj_hfzMnhvIc6XX5Sv8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $resultArray = json_decode($result, TRUE);
            curl_close($ch);
            if (isset($resultArray['razon_social'])) {
                print_r($result);
            }else{
                echo "No existe";
            }
        }
    }


}