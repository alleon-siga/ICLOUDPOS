<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Traspaso_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('unidades/unidades_api_model');
        $this->load->model('kardex/kardex_api_model');
        $this->load->model('inventario/inventario_api_model');
    }

    function traspasar_productos($producto_id, $local1, $local2, $id_usuario, $data)
    {
        $old_cantidad_1 = $this->db->get_where('producto_almacen', array(
            "id_local" => $local1,
            "id_producto" => $producto_id
        ))->row();

        $old_cantidad_2 = $this->db->get_where('producto_almacen', array(
            "id_local" => $local2,
            "id_producto" => $producto_id
        ))->row();

        $old_cantidad_min_1 = $old_cantidad_1 != NULL ? $this->unidades_api_model->convert_minimo_um($producto_id, $old_cantidad_1->cantidad, $old_cantidad_1->fraccion) : 0;
        $old_cantidad_min_2 = $old_cantidad_2 != NULL ? $this->unidades_api_model->convert_minimo_um($producto_id, $old_cantidad_2->cantidad, $old_cantidad_2->fraccion) : 0;

        if (!isset($data['um_id']))
            $cantidad_nueva = $this->unidades_api_model->convert_minimo_um($producto_id, $data['cantidad'], $data['fraccion']);
        else
            $cantidad_nueva = $this->unidades_api_model->convert_minimo_by_um($producto_id, $data['um_id'], $data['cantidad']);

        $result_1 = $this->unidades_api_model->get_cantidad_fraccion($producto_id, $old_cantidad_min_1 - $cantidad_nueva, $local1);
        $result_2 = $this->unidades_api_model->get_cantidad_fraccion($producto_id, $old_cantidad_min_2 + $cantidad_nueva, $local2);

        $local_nombre1 = $this->db->get_where('local', array('int_local_id' => $local1))->row();
        $local_nombre2 = $this->db->get_where('local', array('int_local_id' => $local2))->row();

        $values = array(
            'local_id' => $local1,
            'producto_id' => $producto_id,
            'cantidad' => $cantidad_nueva,
            'io' => 2,
            'tipo' => -1,
            'operacion' => 11,
            'serie' => '-',
            'numero' => '-',
            'ref_id' => $data['venta_id'],
            'ref_val' => $local_nombre2->local_nombre,
        );
        $this->kardex_api_model->set_kardex($values, $id_usuario);

        $values['local_id'] = $local2;
        $values['io'] = 1;
        $values['ref_id'] = $data['venta_id'];
        $values['ref_val'] = $local_nombre1->local_nombre;

        $this->kardex_api_model->set_kardex($values, $id_usuario);
        /**************************************************************************************/

        //ACTUALIZO LOS ALMACENES
        $this->inventario_api_model->update_producto_almacen($producto_id, $local1, array(
            'cantidad' => $result_1['cantidad'],
            'fraccion' => $result_1['fraccion']));

        if ($old_cantidad_2 != NULL) {
            $this->inventario_api_model->update_producto_almacen($producto_id, $local2, array(
                'cantidad' => $result_2['cantidad'],
                'fraccion' => $result_2['fraccion']));
        } else
            $this->inventario_api_model->insert_producto_almacen($producto_id, $local2, $result_2['cantidad'], $result_2['fraccion']);

    }
}
