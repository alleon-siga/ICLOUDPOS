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

    function traspasar_productos_traspaso($productos, $localdestino, $fecha = 0, $motivo)
    {
        //Registro en tabla traspaso
        $id_usuario = $this->session->userdata('nUsuCodigo');
        $values = array(
            'ref_id' => '0', //Por ser traslado
            'usuario_id' => $id_usuario,
            'local_destino' => $localdestino,
            'fecha' => date('Y-m-d H:i:s'),
            'motivo' => $motivo
        );
        $this->db->insert('traspaso', $values);
        $idTraslado = $this->db->insert_id();

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
            //OBTENIENDO AFECTACION DE IMPUESTO E IMPUESTO
            $this->db->select('p.producto_afectacion_impuesto, i.porcentaje_impuesto, p.producto_costo_unitario, pcu.moneda_id');
            $this->db->from('producto p');
            $this->db->join('impuestos i', 'p.producto_impuesto = i.id_impuesto');
            $this->db->join('producto_costo_unitario pcu', 'p.producto_id = pcu.producto_id');
            $this->db->where('pcu.activo','1');
            $this->db->where('p.producto_id', $productos[$i]->producto_id);
            $datosP = $this->db->get()->row();

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
                'ref_val' => $local2->local_nombre,
                'costo' => ($datosP->producto_afectacion_impuesto=='1')? $datosP->producto_costo_unitario / (($datosP->porcentaje_impuesto / 100) + 1) : $datosP->producto_costo_unitario,
                'moneda_id' => $datosP->moneda_id
            );
            $this->kardex_model->set_kardex($values);

            $values['local_id'] = $localdestino;
            $values['io'] = 1;
            $values['ref_id'] = $productos[$i]->local_id;
            $values['ref_val'] = $local1->local_nombre;

            $aIdKardex = $this->kardex_model->set_kardex($values);
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

            //Registro en tabla traspaso detalle
            $values = array(
                'traspaso_id' => $idTraslado,
                'kardex_id' => $aIdKardex,
                'local_origen' => $productos[$i]->local_id
            );
            $this->db->insert('traspaso_detalle', $values);
        }
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
        //OBTENIENDO AFECTACION DE IMPUESTO E IMPUESTO
        $this->db->select('p.producto_afectacion_impuesto, i.porcentaje_impuesto, p.producto_costo_unitario, pcu.moneda_id');
        $this->db->from('producto p');
        $this->db->join('impuestos i', 'p.producto_impuesto = i.id_impuesto');
        $this->db->join('producto_costo_unitario pcu', 'p.producto_id = pcu.producto_id');
        $this->db->where('pcu.activo','1');
        $this->db->where('p.producto_id', $producto_id);
        $datosP = $this->db->get()->row();
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
            'usuario_id' => $id_usuario,
            'costo' => ($datosP->producto_afectacion_impuesto=='1')? $datosP->producto_costo_unitario / (($datosP->porcentaje_impuesto / 100) + 1) : $datosP->producto_costo_unitario,
            'moneda_id' => $datosP->moneda_id
        );
        $salida_id = $this->kardex_model->set_kardex($values);

        $values['local_id'] = $local2;
        $values['io'] = 1;
        $values['ref_id'] = $data['venta_id'];
        $values['ref_val'] = $local_nombre1->local_nombre;

        $entrada_id = $this->kardex_model->set_kardex($values);
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

            return array(
                "salida_id" => $salida_id,
                "entrada_id" => $entrada_id,
                "local_origen" => $local1
            );
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

    function get_traspaso_detalle($id)
    {
        $this->db->select('l1.local_nombre as origen, l2.local_nombre as destino, ref_val, t.fecha, username, producto_nombre, cantidad, nombre_unidad');
        $this->db->from('traspaso t');
        $this->db->join('traspaso_detalle AS d', 't.id = d.traspaso_id');
        $this->db->join('kardex k', 'd.kardex_id = k.id');
        $this->db->join('producto p', 'k.producto_id = p.producto_id');
        $this->db->join('unidades u', 'k.unidad_id = u.id_unidad');
        $this->db->join('local AS l1', 't.local_origen = l1.int_local_id');
        $this->db->join('local AS l2', 't.local_destino = l2.int_local_id');
        $this->db->join('usuario us', 't.usuario_id = us.nUsuCodigo');
        $this->db->where('t.id', $id);
        return $this->db->get()->result();
    }

    function traspasar_detalle($id)
    {
        $this->db->select("t.id, p.producto_nombre, k.cantidad, u.nombre_unidad AS um, t.fecha, l1.local_nombre as origen, l2.local_nombre as destino, us.username, t.motivo, k.producto_id, p.producto_codigo_interno");
        $this->db->from('traspaso AS t');
        $this->db->join('traspaso_detalle AS d', 't.id = d.traspaso_id');
        $this->db->join('kardex AS k', 'd.kardex_id = k.id');
        $this->db->join('producto AS p', 'p.producto_id = k.producto_id');
        $this->db->join('unidades AS u', 'u.id_unidad = k.unidad_id');
        $this->db->join('local AS l2', 't.local_destino = l2.int_local_id');
        $this->db->join('local AS l1', 'd.local_origen = l1.int_local_id');
        $this->db->join('usuario AS us', 't.usuario_id = us.nUsuCodigo');
        //$this->db->where('k.tipo = 0 AND k.operacion = 11');
        $this->db->where('t.id', $id);
        $result = $this->db->get()->result();
        return $result;
    }

    function exportar($where)
    {
        $this->db->select("t.id, p.producto_nombre, k.cantidad, u.nombre_unidad AS um, t.fecha, l1.local_nombre as origen, l2.local_nombre as destino, us.username, t.motivo, k.producto_id, p.producto_codigo_interno, IF(t.ref_id>0, CONCAT('VENTA',' (',t.ref_id,')'),'TRASPASO') AS ref_id");
        $this->db->from('traspaso AS t');
        $this->db->join('traspaso_detalle AS d', 't.id = d.traspaso_id');
        $this->db->join('kardex AS k', 'd.kardex_id = k.id');
        $this->db->join('producto AS p', 'p.producto_id = k.producto_id');
        $this->db->join('unidades AS u', 'u.id_unidad = k.unidad_id');
        $this->db->join('local AS l2', 't.local_destino = l2.int_local_id');
        $this->db->join('local AS l1', 'd.local_origen = l1.int_local_id');
        $this->db->join('usuario AS us', 't.usuario_id = us.nUsuCodigo');
        //$this->db->where('k.tipo = 0 AND k.operacion = 11');
        $this->db->where($where);
        $result = $this->db->get()->result();
        return $result;
    }

    function getUnidadesProducto($id)
    {
        $this->db->select("up.id_unidad, unidades, nombre_unidad, abreviatura");
        $this->db->from('unidades_has_producto up');
        $this->db->join('unidades u', 'up.id_unidad = u.id_unidad');
        $this->db->where('producto_id', $id);
        $this->db->order_by('orden');
        $result = $this->db->get()->result();
        return $result;
    }    
}
