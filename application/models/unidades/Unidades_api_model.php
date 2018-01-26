<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Unidades_api_model extends CI_Model
{
    private $table = 'unidades';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function convert_minimo_um($producto_id, $cantidad, $fraccion = 0)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();
        if ($orden_max->orden == 1)
            return $cantidad;

        $orden_min = $this->db->select_min('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $this->db->select('unidades_has_producto.id_unidad as um_id, unidades_has_producto.unidades as um_number, unidades_has_producto.orden as orden');
        $this->db->from('unidades_has_producto');
        $this->db->where('producto_id', $producto_id);
        $this->db->where('orden', $orden_min->orden);
        $unidad = $this->db->get()->row();

        return ($cantidad * $unidad->um_number) + $fraccion;
    }

    public function convert_minimo_by_um($producto_id, $um_id, $cantidad)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $this->db->select('unidades_has_producto.id_unidad as um_id, unidades_has_producto.unidades as um_number, unidades_has_producto.orden as orden');
        $this->db->from('unidades_has_producto');
        $this->db->where('producto_id', $producto_id);
        $this->db->where('id_unidad', $um_id);
        $unidad = $this->db->get()->row();

        if ($unidad->orden == $orden_max->orden) return $cantidad;

        return $unidad->um_number * $cantidad;
    }

    public function get_cantidad_fraccion($producto_id, $cantidad_minima, $local_id)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id,unidades as um_number')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        $maxima_unidad = $this->db->select('orden, id_unidad as um_id, unidades as um_number')
            ->where('producto_id', $producto_id)
            ->where('orden', 1)
            ->get('unidades_has_producto')->row();

        $result = array();
        if ($minima_unidad->um_id == $maxima_unidad->um_id) {
            $result['id_local'] = $local_id;
            $result['cantidad'] = $cantidad_minima;
            $result['fraccion'] = 0;
            $result['max_um_id'] = $maxima_unidad->um_id;
            $result['min_um_id'] = $minima_unidad->um_id;
            $result['max_um_nombre'] = $this->get_nombre_unidad($maxima_unidad->um_id);
            $result['min_um_nombre'] = $this->get_nombre_unidad($minima_unidad->um_id);
            $result['min_um_abrev'] = $this->get_abreviatura($minima_unidad->um_id);
            $result['max_um_abrev'] = $this->get_abreviatura($maxima_unidad->um_id);
            $result['max_unidades'] = 1;

            return $result;
        }

        $result['id_local'] = $local_id;
        $result['cantidad'] = intval($cantidad_minima / $maxima_unidad->um_number);
        $result['fraccion'] = $cantidad_minima % $maxima_unidad->um_number;
        $result['max_um_id'] = $maxima_unidad->um_id;
        $result['min_um_id'] = $minima_unidad->um_id;
        $result['max_um_nombre'] = $this->get_nombre_unidad($maxima_unidad->um_id);
        $result['min_um_nombre'] = $this->get_nombre_unidad($minima_unidad->um_id);
        $result['min_um_abrev'] = $this->get_abreviatura($minima_unidad->um_id);
        $result['max_um_abrev'] = $this->get_abreviatura($maxima_unidad->um_id);
        $result['max_unidades'] = $maxima_unidad->um_number;
        $result['min_unidades'] = $minima_unidad->um_number;

        return $result;
    }

    function get_nombre_unidad($id)
    {
        $temp = $this->get_by('id_unidad', $id);
        return $temp['nombre_unidad'];
    }

    function get_abreviatura($id)
    {
        $temp = $this->get_by('id_unidad', $id);
        return $temp['abreviatura'];
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }
}
