<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ajustedetalle_model extends CI_Model {

    private $table = 'ajustedetalle';

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_ajuste_detalle($local=false){

        if($local!=false) {
            $query=$this->db->where('local_id',$local);
            $query=$this->db->get('ajustedetalle');
            return $query->result();
        }
    }

    function get_by($campos,$filas){
//si filas es igual a falso se ejecuta row. sino ejecuta row_array
        $this->db->where($campos);
        $query=$this->db->get('ajustedetalle');

        if($filas!=false) {
            return $query->result();
        }else{
            return $query->row_array();

        }
    }

    function get_ajuste_by_inventario($id_inventario){

        return $this->db->select(
            'ajustedetalle.id_producto_almacen as producto_id, 
            producto.producto_nombre as producto_nombre,
            producto.producto_codigo_interno,
            unidades.nombre_unidad as nombre_unidad,
            cantidad_detalle, 
            fraccion_detalle')
            ->from('ajustedetalle')
            ->join('producto', 'ajustedetalle.id_producto_almacen=producto.producto_id')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.producto_id')
            ->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad')
            ->where('ajustedetalle.id_ajusteinventario', $id_inventario)
            ->where('orden', '1')
            ->get()->result();
    }

    function set_ajuste_detalle($campos){


        $this->db->trans_start ();
        $this->db->insert('ajustedetalle',$campos);
        $ultimo_id= $this->db->insert_id();
        $this->db->trans_complete ();
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return $ultimo_id;
    }


    public function traer_by($select = false, $from =false,  $join = false, $campos_join = false,$tipo_join, $where = false,  $group = false,
                             $order = false,$retorno = false){
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if($select !=false){
            $this->db->select($select);
            $this->db->from($from);


        }

        if($join != false and $campos_join != false){

            for($i=0;$i<count($join);$i++) {

                if($tipo_join!=false){

                    for($t=0;$t<count($tipo_join);$t++) {

                        if($tipo_join[$t]!=""){

                            $this->db->join($join[$i], $campos_join[$i],$tipo_join[$t]);
                        }

                    }

                }else{

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if($where!=false){
            $this->db->where($where);

        }
        if($group!=false){
            $this->db->group_by($group);
        }

        if($order!=false){
            $this->db->order_by($group);
        }

        $query=$this->db->get();

        if($retorno=="RESULT_ARRAY"){

            return $query->result_array();
        }elseif($retorno=="RESULT"){
            return $query->result();

        }else{
            return $query->row_array();
        }

    }


}
