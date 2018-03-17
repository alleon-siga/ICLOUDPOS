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
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept: application/json', 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZmY2YzZjE5MTgzM2E3YmZmNmQ2ZDkzODM4ZTViMjkyMTk4ZDEwN2FjMGE4MDU2YjZlYTkwY2U3MzU0MzkyN2M2N2Q4ZmU2ZTQwZGNlN2QxIn0.eyJhdWQiOiIxIiwianRpIjoiZmZjZjNmMTkxODMzYTdiZmY2ZDZkOTM4MzhlNWIyOTIxOThkMTA3YWMwYTgwNTZiNmVhOTBjZTczNTQzOTI3YzY3ZDhmZTZlNDBkY2U3ZDEiLCJpYXQiOjE1MjExOTQxOTAsIm5iZiI6MTUyMTE5NDE5MCwiZXhwIjoxNTUyNzMwMTkwLCJzdWIiOiIxNjM4Iiwic2NvcGVzIjpbInVzZS1zdW5hdCIsInVzZS1yZW5pZWMiXX0.0ekczenYQLjxAQhvtcDI7ax6vssTBrGlcszn4ndIE-rnty_8jhARzq4Y8mXR5vbVo3vNwS_rCFHm-p86O1E237Akw6XwMjeUYeVQTMqjrZnbe1FJyWyqncf_R5limUVEUElRB4YuiTZLzPnNfnLRgKPEi6x7HQhHVZjgOl5iyy4jSTI0IPpWUH3jKj1ccGLJvDeZzDcCggVhnBRyENkeXMTkLsZPpIcMOKu4rFqYFiCNwYSrQBDFigQUS8GUXurQuVNE_oqSjqoSYrBu3kwlN_FCpY-klBiOUpF_HUhdRb1keUqS4WGZUzEFsiXUDvlOvuSzImuMaZ_yquKiqcSkszwmvjVbuHVU5x2VYQLcIU6R6oAoWNgfLwsAWDQ-aXC_6i7apW31lH4J1rQdv1KRDWJovglrn0j_jFqliG59cTc5qUICELJAeA-HAGPxDgEXaXpeKJrhf193KC4lBxADnARS44s-Y0DSQuFnqsTO2ZHIbzsyAHAzinL0TGd6A8sYw96PCLy5-Ms4NVb_NKRk1SvnYpn9EAHswXHJnoH7cs_W1ee0atRHLJsD7YQ_kJ99zhlchLBJxTl_XRdHZE6yjn_ejHyfJQC5WPqMCBnzfJXsLen6_2mrTQjnDdBlgwGa1_D2BofTPc931gjtXMBEzsKdQHGM3hbMo1hq5WHHDVI'));
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
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept: application/json', 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImZmY2YzZjE5MTgzM2E3YmZmNmQ2ZDkzODM4ZTViMjkyMTk4ZDEwN2FjMGE4MDU2YjZlYTkwY2U3MzU0MzkyN2M2N2Q4ZmU2ZTQwZGNlN2QxIn0.eyJhdWQiOiIxIiwianRpIjoiZmZjZjNmMTkxODMzYTdiZmY2ZDZkOTM4MzhlNWIyOTIxOThkMTA3YWMwYTgwNTZiNmVhOTBjZTczNTQzOTI3YzY3ZDhmZTZlNDBkY2U3ZDEiLCJpYXQiOjE1MjExOTQxOTAsIm5iZiI6MTUyMTE5NDE5MCwiZXhwIjoxNTUyNzMwMTkwLCJzdWIiOiIxNjM4Iiwic2NvcGVzIjpbInVzZS1zdW5hdCIsInVzZS1yZW5pZWMiXX0.0ekczenYQLjxAQhvtcDI7ax6vssTBrGlcszn4ndIE-rnty_8jhARzq4Y8mXR5vbVo3vNwS_rCFHm-p86O1E237Akw6XwMjeUYeVQTMqjrZnbe1FJyWyqncf_R5limUVEUElRB4YuiTZLzPnNfnLRgKPEi6x7HQhHVZjgOl5iyy4jSTI0IPpWUH3jKj1ccGLJvDeZzDcCggVhnBRyENkeXMTkLsZPpIcMOKu4rFqYFiCNwYSrQBDFigQUS8GUXurQuVNE_oqSjqoSYrBu3kwlN_FCpY-klBiOUpF_HUhdRb1keUqS4WGZUzEFsiXUDvlOvuSzImuMaZ_yquKiqcSkszwmvjVbuHVU5x2VYQLcIU6R6oAoWNgfLwsAWDQ-aXC_6i7apW31lH4J1rQdv1KRDWJovglrn0j_jFqliG59cTc5qUICELJAeA-HAGPxDgEXaXpeKJrhf193KC4lBxADnARS44s-Y0DSQuFnqsTO2ZHIbzsyAHAzinL0TGd6A8sYw96PCLy5-Ms4NVb_NKRk1SvnYpn9EAHswXHJnoH7cs_W1ee0atRHLJsD7YQ_kJ99zhlchLBJxTl_XRdHZE6yjn_ejHyfJQC5WPqMCBnzfJXsLen6_2mrTQjnDdBlgwGa1_D2BofTPc931gjtXMBEzsKdQHGM3hbMo1hq5WHHDVI'));
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