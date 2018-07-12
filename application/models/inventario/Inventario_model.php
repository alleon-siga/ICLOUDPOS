<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class inventario_model extends CI_Model
{

    private $table = 'inventario';

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('unidades/unidades_model');
    }

    function check_stock($datas)
    {
        $result = array();
        $data_temp = array();

        foreach ($datas as $data) {

            $cantidad_min_out = $this->unidades_model->convert_minimo_by_um($data['producto_id'], $data['unidad_id'], $data['cantidad']);

            $index = $data['producto_id'];
            if (isset($data_temp[$index])) {
                $data_temp[$index]['cantidad_min_out'] += $cantidad_min_out;
            } else {
                $data_temp[$index]['producto_id'] = $data['producto_id'];
                $data_temp[$index]['cantidad_min_out'] = $cantidad_min_out;
            }
        }

        foreach ($data_temp as $data) {
            $temp = $this->db->get_where('producto_almacen', array(
                'id_producto' => $data['producto_id'],
            ))->result();

            $cantidad_min_stock = 0;
            if ($temp != null) {
                foreach ($temp as $t)
                    $cantidad_min_stock += $this->unidades_model->convert_minimo_um($data['producto_id'], $t->cantidad, $t->fraccion);
            }


            if($cantidad_min_stock < $data['cantidad_min_out']) {
                $prod = $this->db->select('*')
                    ->from('producto')
                    ->where('producto_id', $data['producto_id'])
                    ->get()->row();

                $result[] = array(
                    'producto_id' => $data['producto_id'],
                    'producto_codigo' => $prod->producto_codigo_interno,
                    'cantidad_actual' => $cantidad_min_stock,
                    'cantidad_vender' => $data['cantidad_min_out'],
                    'producto_nombre' => $prod->producto_nombre
                );
            }
        }


        return $result;
    }

    function get_by($campos)
    {
        $this->db->where($campos);
        $query = $this->db->get('inventario');
        return $query->row_array();
    }

    function get_all_by_array($campos)
    {


        $query = $this->db->query($campos);
        return $query->result();
    }

    function get_valorizacion($wheres)
    {
        // $this->db->distinct();
        $this->db->select(' producto.producto_nombre as nombre, producto.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, local.local_nombre, inventario.id_local, inventario.cantidad, inventario.fraccion ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, grupos.id_grupo,unidades.nombre_unidad as nombre_fraccion');
        $this->db->from('producto');
        $this->db->join('lineas', 'lineas.id_linea=producto.producto_linea', 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.producto_marca', 'left');
        $this->db->join('familia', 'familia.id_familia=producto.producto_familia', 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.produto_grupo', 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.producto_proveedor', 'left');
        $this->db->join('(SELECT inventario.id_local, inventario.id_producto, inventario.cantidad, inventario.fraccion  FROM producto_almacen inventario  ) as inventario', 'inventario.id_producto=producto.producto_id', 'left');
        $this->db->join('local', 'local.int_local_id=inventario.id_local', 'left');

        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.producto_id' . ' and unidades_has_producto.orden=1', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'left');
        $this->db->where(array('producto_estado' => '1'));
        $this->db->group_by('producto_id');
        $this->db->order_by('producto_id', 'desc');

        //$where_in = array('0', '1');
        $where = array(
            'producto_estatus' => '1'
        );
        //$this->db->where_in($this->estado, $where_in);
        $this->db->where($where);
        if (isset($wheres['inventario.id_local']) && $wheres['inventario.id_local'] == '')
            unset($wheres['inventario.id_local']);

        if (count($wheres) > 0)
            $this->db->where($wheres);

        $query = $this->db->get();
        return $query->result_array();

    }

    function get_by_id_row($producto, $local)
    {
        $sql = ("SELECT * FROM inventario WHERE id_producto='$producto' AND id_local='$local' ORDER by id_inventario DESC LIMIT 1");
        $query = $this->db->query($sql);


        return $query->row_array();
    }

    function get_by_existencia($producto, $local)
    {
        $sql = ("SELECT cantidad,fraccion FROM producto_almacen WHERE id_producto='$producto' AND id_local='$local' LIMIT 1");
        $query = $this->db->query($sql);

        if (count($query) > 0)
            return $query->row_array();
    }


    function set_inventario($campos)
    {


        $this->db->trans_start();
        $this->db->insert('inventario', $campos);
        $ultimo_id = $this->db->insert_id();
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $ultimo_id;
    }

    function insert_producto_almacen($producto, $local, $cantidad, $fraccion)
    {
        $campos = array(
            'id_local' => $local,
            'id_producto' => $producto,
            'cantidad' => $cantidad,
            'fraccion' => $fraccion
        );

        $this->db->trans_start();
        $this->db->insert('producto_almacen', $campos);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update_producto_almacen($producto, $local, $campos)
    {
        $this->db->trans_start();
        $where = array(
            'id_local' => $local,
            'id_producto' => $producto
        );

        $this->db->where($where);
        $this->db->update('producto_almacen', $campos);
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update_inventario($campos, $wheres)
    {


        $this->db->trans_start();
        $this->db->where($wheres);
        $this->db->update('inventario', $campos);
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function getIventarioProducto($wheres)
    {

        $this->db->select('*');
        $this->db->from('inventario');
        $this->db->join('producto', 'producto.producto_id=inventario.id_producto');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.producto_id');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad');
        $this->db->where($wheres);
        $query = $this->db->get();
        return $query->result_array();
    }


}
