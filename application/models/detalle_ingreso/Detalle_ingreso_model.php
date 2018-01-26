<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class detalle_ingreso_model extends CI_Model {

    private $table = 'detalleingreso';
    function __construct() {
        parent::__construct();
        $this->load->database();

    }


    function get_by($select = false, $join = false, $campos_join = false, $where = false,  $group = false,$order = false, $retorno){
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if($select !=false){
            $this->db->select($select);
            $this->db->from($this->table);


        }
        if($join != false and $campos_join != false){

            for($i=0;$i<count($join);$i++) {
                $this->db->join($join[$i], $campos_join[$i]);
            }
        }
        if($where!=false){
            $this->db->where($where);

        }
        if($group!=false){
            $this->db->group_by($group);
        }
        if($order!=false){
            $this->db->order_by($order);
        }

        $query=$this->db->get();

    if($retorno!=false){
        return $query->row_array();

        }else{

        return $query->result();

    }

    }

    function get_by_result($campo, $valor){
        $this->db->select('*');
        $this->db->from('detalleingreso');
        $this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda','left');
        $this->db->where($campo,$valor);
        $query=$this->db->get();
        return $query->result();
    }

    function get_by_result_contable($campo, $valor){
        $this->db->select('*');
        $this->db->from('detalleingreso_contable');
        $this->db->join('ingreso_contable', 'ingreso_contable.id_ingreso=detalleingreso_contable.id_ingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso_contable.id_producto');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso_contable.unidad_medida');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso_contable.id_moneda','left');
        $this->db->where($campo,$valor);
        $query=$this->db->get();
        return $query->result();
    }

    function get_by_result_array($where){
        $this->db->select('*');
        $this->db->from('detalleingreso');
        //$this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida');
        $this->db->where($where);
        $query=$this->db->get();
        return $query->result_array();
    }

    function get_detalleingresodetallado($where,$order){
        $this->db->select('producto.`producto_codigo_interno`,producto.`producto_nombre`,detalleingreso.*,ingreso.`id_ingreso`,ingreso.`local_id`, ingreso.`fecha_registro`, ingreso.`int_Proveedor_id`,
ingreso.`tasa_cambio`, ingreso.`documento_numero`, ingreso.`documento_serie`, ingreso.`tipo_documento`,
ingreso.pago,moneda.`id_moneda`,moneda.`nombre`,moneda.`simbolo`,
 MIN(pagoingreso_restante) AS restante, unidades.`nombre_unidad`,
unidades_has_precio.`precio` AS precio_venta, usuario.`username`,local.`local_nombre`, proveedor.`proveedor_nombre`');
        $this->db->from('detalleingreso');
        $this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id');
        $this->db->join('pagos_ingreso', 'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso','LEFT');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida');
        $this->db->join('unidades_has_precio', 'unidades_has_precio.id_producto=detalleingreso.id_producto and unidades_has_precio.id_unidad=detalleingreso.unidad_medida','left');
        $this->db->join('local', 'local.int_local_id=ingreso.local_id','left');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso.nUsuCodigo', 'left');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda', 'left');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->where($where);
        $this->db->order_by($order);
        $this->db->group_by('detalleingreso.id_detalle_ingreso');
        $query=$this->db->get();
        return $query->result();
    }


    public function traer_by($id,$retorno){
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila
        $sql="SELECT unidades_has_producto.unidades, fecha_registro, precio, unidades, nombre_unidad, id_producto
FROM detalleingreso JOIN ingreso ON ingreso.id_ingreso=detalleingreso.`id_ingreso`
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=unidades.id_unidad
 WHERE  id_producto=$id AND
        fecha_registro=(SELECT MAX(fecha_registro) AS fecha
FROM ingreso JOIN detalleingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso WHERE id_producto=$id )
GROUP BY id_producto";

        $query=$this->db->query($sql);

        if($retorno=="RESULT_ARRAY"){

            return $query->result_array();
        }elseif($retorno=="RESULT"){
            return $query->result();

        }else{
            return $query->row_array();
        }

    }



}
