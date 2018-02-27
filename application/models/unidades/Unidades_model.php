<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class unidades_model extends CI_Model
{

    private $table = 'unidades';

    function __construct()
    {
        parent::__construct();
        $this->load->database();


    }

    //$cantidad es la cantidad normal y su fraccion
    //return la cantidad minima de expresion de un producto
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

    //return la cantidad minima basado en su um_id
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

    //$minimo es la cantidad del producto en su minima expresion
    //return array la cantidad maxima en la que pudo convertir con su unidad_id
    public function convert_maximo_um($producto_id, $minimo)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        $maxima_unidad = $this->db->select('orden, id_unidad as um_id, unidades as um_number')
            ->where('producto_id', $producto_id)
            ->where('orden', 1)
            ->get('unidades_has_producto')->row();


        $result = array();
        if ($minima_unidad->um_id == $maxima_unidad->um_id) {
            $result['cantidad'] = $minimo;
            $result['um_id'] = $minima_unidad->um_id;
            return $result;
        }

        if ($minimo % $maxima_unidad->um_number == 0) {
            $result['cantidad'] = $minimo / $maxima_unidad->um_number;
            $result['um_id'] = $maxima_unidad->um_id;
        } else {
            $result['cantidad'] = $minimo;
            $result['um_id'] = $minima_unidad->um_id;
        }
        return $result;
    }

    //$cantidad_minima es la cantidad en su minima expresion
    //return array con la cantidad max, um_id max, cantidad_min, um_id min
    public function get_cantidad_fraccion($producto_id, $cantidad_minima)
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

    function get_cantida_desglose($producto_id, $cantidad_minima)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $unidades = $this->db->select('orden, unidades_has_producto.id_unidad as um_id, unidades as um_number')
            ->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad')
            ->where('producto_id', $producto_id)
            ->where('presentacion', '1')
            ->order_by('orden', 'asc')
            ->get('unidades_has_producto')->result();

        $result = array();
        $resto = $cantidad_minima;
        foreach ($unidades as $um) {

            if($orden_max->orden == $um->orden){
                $result[$um->um_id] = $resto;
                break;
            }

            if ($um->um_number > $resto)
                $result[$um->um_id] = 0;
            else {
                $result[$um->um_id] = floor($resto / $um->um_number);
                $resto = $resto % $um->um_number;
            }
        }

        return $result;
    }

    function get_um_min_by_producto($producto_id)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        return $this->get_nombre_unidad($minima_unidad->um_id);
    }

    function get_nombre_unidad($id)
    {
        $temp = $this->get_by('id_unidad', $id);
        return $temp['nombre_unidad'];
    }

    function get_um_min_by_producto_abr($producto_id)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        return $this->get_abreviatura($minima_unidad->um_id);
    }

    function get_abreviatura($id)
    {
        $temp = $this->get_by('id_unidad', $id);
        return $temp['abreviatura'];
    }

    public function count_by_producto_id($id)
    {
        return $this->db->from('unidades_has_producto')
            ->where('producto_id', $id)
            ->count_all_results();
    }

    //Convierte
    public function get_maximo_costo($producto_id, $um_id, $costo)
    {
        if ($this->count_by_producto_id($producto_id) == 1) return $costo;

        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $unidad = $this->db->get_where('unidades_has_producto',
            array('producto_id' => $producto_id, 'id_unidad' => $um_id))->row();

        if ($unidad->orden != $orden_max)
            return $costo / $unidad->unidades;
        else
            return $costo;
    }

    public function get_costo_unitario_by_um($producto_id, $um_id, $costo_unitario)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $unidad_min = $this->db->get_where('unidades_has_producto',
            array('producto_id' => $producto_id, 'orden' => $orden_max->orden))->row();


        $unidad = $this->db->get_where('unidades_has_producto',
            array('producto_id' => $producto_id, 'id_unidad' => $um_id))->row();

        if ($unidad_min->orden == $unidad->orden) return $costo_unitario;

        if ($orden_max->orden == $unidad->orden)
            return $costo_unitario * $unidad_min->unidades;

        return $costo_unitario * $unidad->unidades;
    }

    public function get_unidades_costos($producto_id, $moneda_id = 0)
    {

        $result = $this->db->select('costo, unidades_has_producto.*, unidades.nombre_unidad, producto.producto_cualidad')
            ->from('producto')
            ->join('unidades_has_producto', 'producto.producto_id = unidades_has_producto.producto_id')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->join('producto_costo_unitario', 'producto_costo_unitario.producto_id = producto.producto_id')
            ->where('producto.producto_id', $producto_id)
            ->where('moneda_id', $moneda_id)
            ->where('unidades.presentacion', '1')
            ->group_by('id_unidad')
            ->order_by('orden', 'ASC')->get()->result();

        if (count($result) == 0) {
            $result = $this->db->select('producto.producto_costo_unitario as costo, unidades_has_producto.*, unidades.nombre_unidad, unidades.abreviatura as abr, producto.producto_cualidad')
                ->from('producto')
                ->join('unidades_has_producto', 'producto.producto_id = unidades_has_producto.producto_id')
                ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
                ->where('producto.producto_id', $producto_id)
                ->where('unidades.presentacion', '1')
                ->group_by('id_unidad')
                ->order_by('orden', 'ASC')->get()->result();
        }

        foreach ($result as $r) {
            $r->costo = $this->get_costo_unitario_by_um($r->producto_id, $r->id_unidad, $r->costo);
        }

        return $result;
    }

    public function get_unidades_precios($producto_id, $precio_id)
    {

        $result = $this->db->select('unidades_has_precio.precio as precio, unidades_has_producto.*, unidades.nombre_unidad, producto.producto_cualidad, unidades.abreviatura as abr, unidades.presentacion')
            ->from('producto')
            ->join('unidades_has_producto', 'producto.producto_id = unidades_has_producto.producto_id')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->join('unidades_has_precio', 'unidades_has_precio.id_producto = producto.producto_id')
            ->where('producto.producto_id', $producto_id)
            ->where('unidades_has_precio.id_precio', $precio_id)
            ->where('unidades_has_precio.id_unidad = unidades.id_unidad')
            ->group_by('id_unidad')
            ->order_by('orden', 'ASC')->get()->result();

        return $result;
    }

    public function get_moneda_default($producto_id){
        $moneda = $this->db->get_where('producto_costo_unitario', array('producto_id' => $producto_id, 'activo' => '1'))->row();
        $moneda_id = $moneda != null ? $moneda->moneda_id : MONEDA_DEFECTO;
        return $this->db->get_where('moneda', array('id_moneda' => $moneda_id))->row();
    }

    public function get_unidades_cantidad($producto_id, $local_id)
    {
        $moneda_default = $this->get_moneda_default($producto_id);

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

        $cantidad_desglose = $this->get_cantida_desglose($producto_id, $old_cantidad_min);


        $unidades_costos = $this->get_unidades_costos($producto_id, $moneda_default->id_moneda);

        $unidades_cantidad = array();
        foreach ($unidades_costos as $um_costo) {
            $um_costo->cantidad = $cantidad_desglose[$um_costo->id_unidad];
            $unidades_cantidad[] = $um_costo;
        }

        return $unidades_cantidad;
    }

    function get_unidades()
    {
        //$this->db->where('estatus_unidad', 1);
        $this->db->order_by('nombre_unidad', 'asc');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    function set_unidades($grupo)
    {
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_unidad', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert($this->table, $grupo);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update_unidades($grupo)
    {


        $produc_exite = $this->get_by('nombre_unidad', $grupo['nombre_unidad']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_unidad'] == $grupo ['id_unidad']))) {

            $this->db->trans_start();
            $this->db->where('id_unidad', $grupo['id_unidad']);
            $this->db->update($this->table, $grupo);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function get_by_producto($producto)
    {
        $this->db->select('*');
        $this->db->from('unidades_has_producto');
        $this->db->where('unidades_has_producto.producto_id', $producto);
        $this->db->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad');
        $this->db->join('producto', 'producto.producto_id=unidades_has_producto.producto_id');

        $this->db->order_by('orden', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $where = false, $group = false, $order, $retorno = false)
    {
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }

        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                $this->db->join($join[$i], $campos_join[$i]);
            }
        }
        if ($where != false) {
            $this->db->where($where);

        }
        if ($group != false) {
            $this->db->group_by($group);
        }
        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }

    function get_unidades_has_producto()
    {
        $this->db->select('unidades_has_producto.*,unidades.nombre_unidad, unidades.abreviatura');
        $this->db->from('unidades_has_producto');
        $this->db->join('unidades','unidades.id_unidad=unidades_has_producto.id_unidad');
        $this->db->order_by('orden','desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function verifProdUnidad($unidad)
    {
        $this->db->where('id_unidad', $unidad['id_unidad']);
        $this->db->from('unidades_has_producto');

        if ($this->db->count_all_results() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function activarUnidad($unidad)
    {
        $this->db->where('id_unidad',$unidad);
        $this->db->update('unidades', array('estatus_unidad' => 1));

        if($this->db->affected_rows()>0){
            return true;
        } else {
            return false;
        }
    }
}
