<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('facturador/facturador_model');
        if ($this->facturador_model->verify_session()) {
            $this->load->model('usuario_facturador/usuario_facturador_model');
            $this->load->model('local/local_model');
            $this->load->library('session');
        }else{
            redirect(base_url(), 'refresh');
        }
    }


    function index()
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data["lstUsuario"] = $this->usuario_facturador_model->select_all_user();
        /*foreach ($data["lstUsuario"] as $valor){
            $data['locales_usuario'][] = $this->local_model->get_all_usu($valor->id);
        }*/
        
        $dataCuerpo['cuerpo'] = $this->load->view('facturador/usuario/usuario', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('facturador/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        //$data['grupos'] = $this->usuarios_grupos_model->get_all();
        //$data['locales'] = $this->local_model->get_all();
        $idusu = $this->session->userdata('id');
        $usu = $this->usuario_facturador_model->get_by('id',$idusu);
        if ($id != FALSE) {
            $data['usuario'] = $this->usuario_facturador_model->buscar_id($id);
            //$data['usu_almacen'] = $this->usuario_model->buscar_almacenes($id);
        }

        $this->load->view('facturador/usuario/form', $data);
    }

    /*function guardarsession()
    {
        if ($this->input->is_ajax_request()) {

            $json=array();
            //datos que recibo por post
            $password = $this->input->post('var_usuario_clave');
            $id = $this->input->post('nUsuCodigo');
            $local = $this->session->userdata('id_local');

            $usuario = array(
                'username' => $this->input->post('username', true),
                'nombre' => $this->input->post('nombre', true),

            );
            if (!empty($local))
                $usuario['id_local'] = $local;
            if (!empty($password)) {
                $usuario['var_usuario_clave'] = md5($password);
            }
            $usuario['nUsuCodigo'] = $id;

            $resultado = $this->usuario_model->update($usuario, "");

            $this->session->set_userdata($usuario);

            if ($resultado == true) {

                $json['exito']="Se han guardado los cambios";

            } elseif($resultado==false) {

                $json['falla']="Ocurrio un error durante el registro";
            }else{
                $json['nombre_existe']="El username ingresado ya existe";

            }

            echo json_encode($json);

        }else{

        }
    }*/


    function registrar()
    {
        $this->form_validation->set_rules('username', 'username', 'required');
      //  $this->form_validation->set_rules('var_usuario_clave', 'var_usuario_clave', 'required');
        if ($this->form_validation->run() == false):
            $this->session->set_flashdata('error', validation_errors());
        else:
            $password = $this->input->post('var_usuario_clave');
            $activo = $this->input->post('activo');
            //$admin = $this->input->post('admin');
            $id = $this->input->post('nUsuCodigo');
            $usuario = array(
                'username' => $this->input->post('username', true),
                'nombre' => $this->input->post('nombre', true),
                // 'admin' => !empty($admin) ? true : false,
            );
            $activo = !empty($activo) ? 1 : 0;
            $usuario['activo'] = $activo;
            if (!empty($password)) {
                $usuario['var_usuario_clave'] = md5($password);
            }
            if (empty($id)) {
                $resultado = $this->usuario_facturador_model->insertar($usuario);
            } else {
                $usuario['id'] = $id;
                $resultado = $this->usuario_facturador_model->update($usuario);
            }
        endif;

        if ($resultado != FALSE) {
            $json['success'] = 'Solicitud Procesada con exito';

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        if ($resultado === USERNAME_EXISTE) {
            $json['error'] = USERNAME_EXISTE;
        }

        echo json_encode($json);
    }

    /*function buscar_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $pd = $this->usu->buscar_id($id);
            echo json_encode($pd);
        } else {
            redirect(base_url() . 'usuario/', 'refresh');
        }
    }

    function buscar_lstlocal_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $pd = $this->usu->select_lista_local($id);
            echo json_encode($pd);
        } else {
            redirect(base_url() . 'usuario/', 'refresh');
        }
    }*/

    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $grupo = array(
            'id' => $id,
            'username' => $nombre,
            'deleted' => 1
        );
        $data['resultado'] = $this->usuario_facturador_model->update_estatus($grupo);
        if ($data['resultado'] != FALSE) {
            $json['success'] = 'Se ha Eliminado exitosamente';
        } else {
            $json['error'] = 'Ha ocurrido un error al eliminar el impuesto';
        }
        echo json_encode($json);
    }
}
