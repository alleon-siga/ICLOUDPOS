<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_campo_valor_model extends CI_Model
{

    protected  $table='cliente_campo_valor';
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function insertar($padre,$cliente)
    {
        $this->load->model('cliente_tipo_campo/cliente_tipo_campo_model');


        $this->db->trans_start();

            for($i=0;$i<count($padre);$i++) {
            $where = array(
                'padre_id' =>$padre[$i]
            );
            /*obtengo los campos de la tabla cliente_campo_valor con el id del padre*/
            $valores_padre=$this->cliente_tipo_campo_model->get_by($where);


            if(count($valores_padre)>0){

                /*recorro los valores traidos de la tabla cliente_tipo_campo para poder usarlos de referencia*/
                for($j=0;$j<count($valores_padre);$j++){


                    /*tomo los datos que vienen por post*/
                    $arreglo_por_campo=$this->input->post($valores_padre[$j]['slug']);


                    /*recorro el item traido del formulario*/
                    for($t=0;$t<count($arreglo_por_campo);$t++){


                        $insertar=array(
                            'campo_cliente'=>$cliente,
                            'tipo_campo'=>$valores_padre[$j]['id_tipo'],
                            'campo_valor'=>$arreglo_por_campo[$t],
                            'referencia'=>$t
                        );
                        $this->db->insert('cliente_campo_valor',$insertar);

                    }
                }
            }
        }

            try {
                $this->db->trans_complete();
            } catch (Exception $e) {
                return $this->db->_error_message();
            }

            if ($this->db->trans_status() === FALSE) {
                return $this->db->_error_message();
            } else {
                return TRUE;
            }

    }


    function get_with_cliente_tipo_campo($where)
    {
        $this->db->select('*');
        $this->db->from('cliente_campo_valor');
        $this->db->join('cliente_tipo_campo','cliente_tipo_campo.id_tipo=cliente_campo_valor.tipo_campo');
        $this->db->join('cliente_tipo_campo_padre','cliente_tipo_campo_padre.tipo_campo_padre_id=cliente_tipo_campo.padre_id');
        $this->db->where($where);
        $this->db->order_by('padre_id,referencia,tipo_campo');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_with_cliente_tipo_group($where,$group)
    {
        $this->db->select('*');
        $this->db->from('cliente_campo_valor');
        $this->db->join('cliente_tipo_campo','cliente_tipo_campo.id_tipo=cliente_campo_valor.tipo_campo');
        $this->db->join('cliente_tipo_campo_padre','cliente_tipo_campo_padre.tipo_campo_padre_id=cliente_tipo_campo.padre_id');
        $this->db->where($where);
        $this->db->group_by($group);
        $query = $this->db->get();
        return $query->result_array();
    }


    function eliminar_valor_cliente($where)
    {
        $this->db->trans_start();

        $this->db->where($where);
        $this->db->delete('cliente_campo_valor');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }



}