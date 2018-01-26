<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class unidades_has_precio_model extends CI_Model {

    private $table = 'unidades_has_precio';

    function __construct() {
        parent::__construct();
        $this->load->database();


    }

    function get_precio($where)
    {

        $this->db->select('*');
        $this->db->from('unidades_has_precio');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_all()
    {

        $this->db->select('unidades_has_precio.*,unidades.nombre_unidad, unidades.abreviatura');
        $this->db->from('unidades_has_precio');
        $this->db->join('unidades','unidades.id_unidad=unidades_has_precio.id_unidad');
        $query = $this->db->get();
        return $query->result_array();
    }

    function precios_por_unidad($where)
    {
        $this->db->select('unidades_has_precio.*,unidades_has_producto.orden, unidades.nombre_unidad, unidades.abreviatura');
        $this->db->from('unidades_has_precio');
        $this->db->join('unidades', 'unidades_has_precio.id_unidad = unidades.id_unidad');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.id_unidad = unidades_has_precio.id_unidad and
         unidades_has_producto.producto_id=unidades_has_precio.id_producto');
        $this->db->where($where);
        $this->db->group_by('unidades_has_precio.id_unidad,unidades_has_precio.id_precio');
        $this->db->order_by('unidades_has_producto.orden');
        $query=$this->db->get();

        return $query->result_array();
    }


    function get_precio_has_producto(){
        

        $sql=$this->db->query("SELECT precios.nombre_precio,precios.id_precio, precios.orden as precio_orden,
 unidades_has_precio.*, producto.producto_nombre, unidades.nombre_unidad, unidades_has_producto.orden,grupos.id_grupo, grupos.nombre_grupo FROM precios JOIN unidades_has_precio
ON unidades_has_precio.`id_precio`= precios.`id_precio`
JOIN producto ON producto.`producto_id`=unidades_has_precio.`id_producto`
left JOIN grupos ON grupos.`id_grupo`=producto.`produto_grupo`
JOIN unidades ON unidades.`id_unidad`=unidades_has_precio.`id_unidad`
 JOIN unidades_has_producto ON  unidades_has_producto.`id_unidad`=unidades_has_precio.`id_unidad` AND
unidades_has_producto.`producto_id`=unidades_has_precio.`id_producto`
WHERE mostrar_precio=1 AND estatus_precio=1 AND producto_estatus=1
GROUP BY id_producto, precios.id_precio,unidades_has_precio.`id_unidad` ORDER BY unidades_has_producto.orden asc, grupos.nombre_grupo asc");
        return $sql->result_array();
    }


    public function traer_by($select = false, $from =false,  $join = false, $campos_join = false,$tipo_join, $where = false,  $group = false,
                             $order = false,$retorno = false){


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
