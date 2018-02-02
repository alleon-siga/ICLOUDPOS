<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_tipo_campo extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('cliente_tipo_campo/cliente_tipo_campo_model');
            $this->load->model('cliente_campo_valor/cliente_campo_valor_model');
            $this->load->model('distrito/distrito_model');
            $this->load->model('ciudad/ciudad_model');
            $this->load->model('estado/estado_model');
        }else{
            redirect(base_url(), 'refresh');
        }    
    }


    /*este metodo es llamado al presionar sobre el boton de anadir, para ir buscando los datos en la tabla cliente_tipo_campo que esten
    asociados a la tabla padre*/
    function get_by()
    {
        $data['distritos'] = $this->distrito_model->get_all();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        //$data['paises'] = $this->pais_model->get_all();
        $id =  $this->input->post('id');
        $where=array(
            'padre_id'=>$id
        );
        $data['clientes'] = $this->cliente_tipo_campo_model->get_by_with_padre($where);

        echo json_encode($data);
    }


    /*este metodo es trae todos los datos */
    function get_by_with_valor()
    {

        $data['distritos'] = $this->distrito_model->get_all();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();

        $id_padre=  $this->input->post('id');
        $id_cliente=  $this->input->post('cliente_id');
        $where=array(
            'padre_id'=>$id_padre,
            'campo_cliente'=>$id_cliente
        );
        $data['clientes'] = $this->cliente_campo_valor_model->get_with_cliente_tipo_campo($where);

        echo json_encode($data);
    }


    /*este metodo trae todos los padres que estan asociados a un cliente, razon social, direccion, correo, fecha de nac, etc*/
    function get_padres_por_cliente()
    {

        $id_cliente=  $this->input->post('id');
        $where=array(
            'campo_cliente'=>$id_cliente
        );
        $group="padre_id";
        $data['padre'] = $this->cliente_campo_valor_model->get_with_cliente_tipo_group($where,$group);

        echo json_encode($data);
    }

}