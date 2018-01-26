<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class traspaso_model extends CI_Model
{

    private $tabla = 'inventario';
    //private $id = 'id_producto';
    // private $id_local = 'id_local';


    function __construct()
    {
        parent::__construct();

        $this->load->model('unidades/unidades_model');
        $this->load->model('historico/historico_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('inventario/inventario_model');
    }

    function traspasar_productos_traspaso($productos, $localdestino, $fecha = 0)
    {

        for ($i = 0; $i < count($productos); $i++) {

            $old_cantidad_1 = $this->db->get_where('producto_almacen', array(
                "id_local" => $productos[$i]->local_id,
                "id_producto" => $productos[$i]->producto_id
            ))->row();

            $old_cantidad_2 = $this->db->get_where('producto_almacen', array(
                "id_local" => $localdestino,
                "id_producto" => $productos[$i]->producto_id
            ))->row();

            $old_cantidad_min_1 = $old_cantidad_1 != NULL ? $this->unidades_model->convert_minimo_um($productos[$i]->producto_id, $old_cantidad_1->cantidad, $old_cantidad_1->fraccion) : 0;
            $old_cantidad_min_2 = $old_cantidad_2 != NULL ? $this->unidades_model->convert_minimo_um($productos[$i]->producto_id, $old_cantidad_2->cantidad, $old_cantidad_2->fraccion) : 0;


            $cantidad_nueva = $this->unidades_model->convert_minimo_um($productos[$i]->producto_id, $productos[$i]->cantidad, $productos[$i]->fraccion);

            $result_1 = $this->unidades_model->get_cantidad_fraccion($productos[$i]->producto_id, $old_cantidad_min_1 - $cantidad_nueva);
            $result_2 = $this->unidades_model->get_cantidad_fraccion($productos[$i]->producto_id, $old_cantidad_min_2 + $cantidad_nueva);

            /* GUARDO EL HISTORICO ***********************************************************************/
            /*$values = array(
                'producto_id' => $productos[$i]->producto_id,
                'local_id' =>  $productos[$i]->local_id,
                'cantidad' => $cantidad_nueva,
                'cantidad_actual' => $this->unidades_model->convert_minimo_um($productos[$i]->producto_id, $result_1['cantidad'], $result_1['fraccion']),
                'tipo_movimiento' => "TRASPASO",
                'tipo_operacion' => "SALIDA",
                'referencia_valor' => 'Se realizo un traspaso de Almacen',
                'referencia_id' => $localdestino,
            );

            $this->historico_model->set_historico($values, $fecha == 0 ? date("Y-m-d H:i:s") : $fecha);

            $values['local_id'] = $localdestino;
            $values['cantidad_actual'] = $this->unidades_model->convert_minimo_um($productos[$i]->producto_id, $result_2['cantidad'], $result_2['fraccion']);
            $values['tipo_operacion'] = 'ENTRADA';
            $values['referencia_id'] = $productos[$i]->local_id;

            $this->historico_model->set_historico($values, $fecha == 0 ? date("Y-m-d H:i:s") : $fecha);
            */

            $local1 = $this->db->get_where('local', array('int_local_id' => $productos[$i]->local_id))->row();
            $local2 = $this->db->get_where('local', array('int_local_id' => $localdestino))->row();
            $values = array(
                'local_id' => $productos[$i]->local_id,
                'producto_id' => $productos[$i]->producto_id,
                'cantidad' => $cantidad_nueva,
                'io' => 2,
                'tipo' => 0,
                'operacion' => 11,
                'serie' => '-',
                'numero' => '-',
                'ref_id' => $localdestino,
                'ref_val' => $local2->local_nombre
            );
            $this->kardex_model->set_kardex($values);

            $values['local_id'] = $localdestino;
            $values['io'] = 1;
            $values['ref_id'] = $productos[$i]->local_id;
            $values['ref_val'] = $local1->local_nombre;

            $this->kardex_model->set_kardex($values);
            /**************************************************************************************/

            //ACTUALIZO LOS ALMACENES
            $this->inventario_model->update_producto_almacen($productos[$i]->producto_id, $productos[$i]->local_id, array(
                'cantidad' => $result_1['cantidad'],
                'fraccion' => $result_1['fraccion']));

            if ($old_cantidad_2 != NULL) {
                $this->inventario_model->update_producto_almacen($productos[$i]->producto_id, $localdestino, array(
                    'cantidad' => $result_2['cantidad'],
                    'fraccion' => $result_2['fraccion']));
            } else
                $this->inventario_model->insert_producto_almacen($productos[$i]->producto_id, $localdestino, $result_2['cantidad'], $result_2['fraccion']);
        }
    }


    function traspasar_productos($producto_id, $local1, $local2, $data)
    {

        $old_cantidad_1 = $this->db->get_where('producto_almacen', array(
            "id_local" => $local1,
            "id_producto" => $producto_id
        ))->row();

        $old_cantidad_2 = $this->db->get_where('producto_almacen', array(
            "id_local" => $local2,
            "id_producto" => $producto_id
        ))->row();

        $old_cantidad_min_1 = $old_cantidad_1 != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad_1->cantidad, $old_cantidad_1->fraccion) : 0;
        $old_cantidad_min_2 = $old_cantidad_2 != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad_2->cantidad, $old_cantidad_2->fraccion) : 0;

        if (!isset($data['um_id']))
            $cantidad_nueva = $this->unidades_model->convert_minimo_um($producto_id, $data['cantidad'], $data['fraccion']);
        else
            $cantidad_nueva = $this->unidades_model->convert_minimo_by_um($producto_id, $data['um_id'], $data['cantidad']);

        $result_1 = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min_1 - $cantidad_nueva);
        $result_2 = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min_2 + $cantidad_nueva);

        /* GUARDO EL HISTORICO ***********************************************************************/
        /*$values = array(
            'producto_id' => $producto_id,
            'local_id' => $local1,
            'cantidad' => $cantidad_nueva,
            'cantidad_actual' => $this->unidades_model->convert_minimo_um($producto_id, $result_1['cantidad'], $result_1['fraccion']),
            'tipo_movimiento' => "TRASPASO",
            'tipo_operacion' => "SALIDA",
            'referencia_valor' => 'Se realizo un traspaso de Almacen',
            'referencia_id' => $local2,
        );
        $this->historico_model->set_historico($values);

        $values['local_id'] = $local2;
        $values['cantidad_actual'] = $this->unidades_model->convert_minimo_um($producto_id, $result_2['cantidad'], $result_2['fraccion']);
        $values['tipo_operacion'] = 'ENTRADA';
        $values['referencia_id'] = $local1;

        $this->historico_model->set_historico($values);*/

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
        $this->kardex_model->set_kardex($values);

        $values['local_id'] = $local2;
        $values['io'] = 1;
        $values['ref_id'] = $data['venta_id'];
        $values['ref_val'] = $local_nombre1->local_nombre;

        $this->kardex_model->set_kardex($values);
        /**************************************************************************************/

        //ACTUALIZO LOS ALMACENES
        $this->inventario_model->update_producto_almacen($producto_id, $local1, array(
            'cantidad' => $result_1['cantidad'],
            'fraccion' => $result_1['fraccion']));

        if ($old_cantidad_2 != NULL) {
            $this->inventario_model->update_producto_almacen($producto_id, $local2, array(
                'cantidad' => $result_2['cantidad'],
                'fraccion' => $result_2['fraccion']));
        } else
            $this->inventario_model->insert_producto_almacen($producto_id, $local2, $result_2['cantidad'], $result_2['fraccion']);

    }

    function checkEmpty($id_local, $producto_id)
    {
        $this->db->where(array(
            "id_local" => $id_local,
            "id_producto" => $producto_id
        ));
        $this->db->from('producto_almacen');
        if ($this->db->count_all_results() == 0) {
            $this->db->insert('producto_almacen', array(
                "id_local" => $id_local,
                "id_producto" => $producto_id,
                'cantidad' => 0,
                'fraccion' => 0
            ));
        }
    }

    function get_cantidad($id_local, $producto_id)
    {
        $where = array(
            "id_local" => $id_local,
            "id_producto" => $producto_id
        );
        $this->db->where($where);
        $query = $this->db->get('producto_almacen');
        $row = $query->row_array();
        if (isset($row))
            return $row;
        else
            return 0;

    }

    function update_almacen($inventario, $producto_id, $id_local)
    {

        $where = array(
            "id_local" => $id_local,
            "id_producto" => $producto_id
        );

        $this->db->where($where);
        $this->db->update("producto_almacen", $inventario);
        return true;

    }

}
