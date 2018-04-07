<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ajuste_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('kardex/kardex_model');
        $this->load->model('unidades/unidades_model');
    }


    function save_ajuste($ajuste, $productos, $otros_val)
    {


        //inserto la venta
        $this->db->insert('ajuste', $ajuste);
        $ajuste_id = $this->db->insert_id();


        $this->save_producto_detalles($ajuste_id, $ajuste['local_id'], $productos, $otros_val);

        return $ajuste_id;

    }

    function getAjustes($params)
    {

        $query = "
            SELECT 
                a.id,
                DATE_FORMAT(a.fecha, '%d/%m/%Y') AS fecha,
                a.operacion,
                a.documento,
                a.io,
                a.serie,
                a.numero,
                l.local_nombre,
                a.estado,
                u.nombre
            FROM
                ajuste AS a
                    JOIN
                local AS l ON l.int_local_id = a.local_id
                    JOIN
                usuario AS u ON u.nUsuCodigo = a.usuario_id
            WHERE
                a.local_id = " . $params['local_id'] . "
                    AND a.moneda_id = " . $params['moneda_id'] . "
                    AND a.fecha >= '" . $params['fecha_ini'] . "'
                    AND a.fecha <= '" . $params['fecha_fin'] . "'
            ";

        if (isset($params['io']) && $params['io'] != 0) {
            $query .= " AND io = " . $params['io'];
        }

        return $this->db->query($query)->result();
    }


    private function save_producto_detalles($ajuste_id, $local_id, $productos, $otros_val)
    {
        //Preparo los detalles de la venta para insertarlo y sus historicos
        $ajuste = $this->db->get_where('ajuste', array('id' => $ajuste_id))->row();
        $cantidades = array();
        $ajuste_detalle = array();
        foreach ($productos as $producto) {

            //preparo los datos para el historico
            if (!isset($cantidades[$producto->id_producto]))
                $cantidades[$producto->id_producto] = 0;

            $cantidades[$producto->id_producto] += $this->unidades_model->convert_minimo_by_um(
                $producto->id_producto,
                $producto->unidad_medida,
                $producto->cantidad
            );


            //preparo el detalle de la venta
            $producto_detalle = array(
                'ajuste_id' => $ajuste_id,
                'producto_id' => $producto->id_producto,
                'costo_unitario' => $producto->costo,
                'cantidad' => $producto->cantidad,
                'unidad_id' => $producto->unidad_medida
            );
            array_push($ajuste_detalle, $producto_detalle);

        }

        //inserto los detalles del ajuste
        $this->db->insert_batch('ajuste_detalle', $ajuste_detalle);

        foreach ($cantidades as $key => $value) {

            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local_id,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $ajuste_cantidad = $old_cantidad_min - $value;
            if ($ajuste->io == 1)
                $ajuste_cantidad = $old_cantidad_min + $value;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $ajuste_cantidad);

            //CREAR EL HISTORICO DE LA VENTA *************************************
            $values = array(
                'local_id' => $local_id,
                'producto_id' => $key,
                'cantidad' => $value,
                'io' => $ajuste->io,
                'tipo' => $ajuste->documento == 0 ? -3 : $ajuste->documento,
                'operacion' => $ajuste->operacion,
                'serie' => $ajuste->serie,
                'numero' => $ajuste->numero,
                'ref_id' => $ajuste->id,
                'ref_val' => $otros_val
            );
            $this->kardex_model->set_kardex($values);

            if ($old_cantidad != NULL) {
                //Actualizo el almacen
                $this->db->where(array(
                    'id_local' => $local_id,
                    'id_producto' => $key
                ));
                $this->db->update('producto_almacen', array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            } else {
                $this->db->insert('producto_almacen', array(
                    'id_producto' => $key,
                    'id_local' => $local_id,
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']
                ));
            }
        }
    }

}
