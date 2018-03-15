<?php

/**
 * made by jaimeirazabal1@gmail.com
 * 02/06/2016
 * 14:34 vz
 */
class cotizar_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
        $this->load->model('local/local_model');
        $this->load->model('unidades/unidades_model');
    }

    function get_cotizaciones($where = array())
    {
        $this->db->select('
            c.id as id,
            c.fecha as fecha,
            c.created_at as created,
            c.local_id AS local_id,
            local.local_nombre AS local_nombre,
            c.documento_id as documento_id,
            c.tipo_impuesto as tipo_impuesto,
            documentos.des_doc as documento_nombre,
            c.cliente_id as cliente_id,
            cliente.razon_social as cliente_nombre,
            cliente.identificacion as ruc,
            cliente.ruc as tipo_cliente,
            cliente.direccion as cliente_direccion,
            cliente.telefono1 as telefono,
            c.vendedor_id as vendedor_id,
            usuario.nombre as vendedor_nombre,
            c.tipo_pago_id as condicion_id,
            condiciones_pago.nombre_condiciones as condicion_nombre,
            c.estado as estado,
            c.moneda_id as moneda_id,
            c.tasa_cambio as moneda_tasa,
            moneda.nombre as moneda_nombre,
            moneda.simbolo as moneda_simbolo,
            c.total as total,
            c.impuesto as impuesto,
            c.subtotal as subtotal,
            c.credito_periodo as credito_periodo,
            c.periodo_per as periodo_per,
            c.fecha_entrega as fecha_entrega,
            c.lugar_entrega as lugar_entrega
            ')
            ->from('cotizacion AS c')
            ->join('documentos', 'c.documento_id=documentos.id_doc')
            ->join('local', 'local.int_local_id=c.local_id')
            ->join('condiciones_pago', 'c.tipo_pago_id=condiciones_pago.id_condiciones')
            ->join('cliente', 'c.cliente_id=cliente.id_cliente')
            ->join('usuario', 'c.vendedor_id=usuario.nUsuCodigo')
            ->join('moneda', 'c.moneda_id=moneda.id_moneda')
            ->order_by('c.fecha', 'desc');

        if (isset($where['id'])) {
            $this->db->where('c.id', $where['id']);
            return $this->db->get()->row();
        }

        if (isset($where['estado']))
            $this->db->where('c.estado', $where['estado']);

        if (isset($where['local_id']))
            $this->db->where('c.local_id', $where['local_id']);

        if (isset($where['moneda_id']))
            $this->db->where('c.moneda_id', $where['moneda_id']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('c.created_at <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('c.created_at >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min'] . " 00:00:00");
            $this->db->where('c.created_at <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day . " 23:59:59");
        }

        return $this->db->get()->result();
    }

    function get_cotizaciones_totales($where = array())
    {
        $this->db->select('
            SUM(c.total) as total,
            SUM(c.impuesto) as impuesto,
            SUM(c.subtotal) as subtotal
            ')
            ->from('cotizacion as c');


        if (isset($where['id'])) {
            $this->db->where('c.id', $where['id']);
            return $this->db->get()->row();
        }


        if (isset($where['estado']))
            $this->db->where('c.estado', $where['estado']);

        if (isset($where['local_id']))
            $this->db->where('c.local_id', $where['local_id']);

        if (isset($where['moneda_id']))
            $this->db->where('c.moneda_id', $where['moneda_id']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('c.created_at >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('c.created_at <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('c.created_at >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min']);
            $this->db->where('c.created_at <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day);
        }

        return $this->db->get()->row();
    }

    function get_cotizar_detalle($id)
    {
        $cotizacion = $this->get_cotizaciones(array('id' => $id));

        $cotizacion->detalles = $this->db->select('
            cd.id as detalle_id,
            c.local_id AS local_id,
            local.local_nombre AS local_nombre,
            cd.producto_id as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            (cd.precio * uhp.unidades) as precio,
            cd.cantidad as cantidad,
            cd.unidad_id as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            SUM(cd.precio * uhp.unidades * cd.cantidad) as importe, 
            cd.precio_venta as precio_venta
            ')
            ->from('cotizacion_detalles as cd')
            ->join('cotizacion as c', 'c.id=cd.cotizacion_id')
            ->join('local', 'local.int_local_id=c.local_id')
            ->join('producto', 'producto.producto_id=cd.producto_id')
            ->join('unidades', 'unidades.id_unidad=cd.unidad_id')
            ->join('unidades_has_producto as uhp', 'uhp.producto_id=cd.producto_id AND uhp.id_unidad=cd.unidad_id')
            ->where('cd.cotizacion_id', $cotizacion->id)
            ->group_by('cd.id')
            ->get()->result();

        $cotizacion->descuento = 0;
        foreach ($cotizacion->detalles as $detalle) {
            if ($detalle->precio < $detalle->precio_venta) {
                $cotizacion->descuento += ($detalle->precio_venta * $detalle->cantidad) - ($detalle->precio * $detalle->cantidad);
            }

        }

        return $cotizacion;
    }

    function get_cotizar_validar($id)
    {

        $cotizacion = $this->get_cotizaciones(array('id' => $id));

        $cotizacion->detalles = $this->db->select('
            cd.id as detalle_id,
            c.local_id AS local_id,
            local.local_nombre AS local_nombre,
            cd.producto_id as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            (cd.precio * uhp.unidades) as precio,
            cd.cantidad as cantidad,
            (SELECT cantidad FROM producto_almacen WHERE id_producto = cd.producto_id AND id_local = c.local_id LIMIT 1) AS cantidad_almacen,
            (SELECT fraccion FROM producto_almacen WHERE id_producto = cd.producto_id AND id_local = c.local_id LIMIT 1) AS fraccion_almacen,
            cd.unidad_id as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            SUM(cd.precio * uhp.unidades * cd.cantidad) as importe
            ')
            ->from('cotizacion_detalles as cd')
            ->join('cotizacion as c', 'c.id=cd.cotizacion_id')
            ->join('local', 'local.int_local_id=c.local_id')
            ->join('producto', 'producto.producto_id=cd.producto_id')
            ->join('unidades', 'unidades.id_unidad=cd.unidad_id')
            ->join('unidades_has_producto as uhp', 'uhp.producto_id=cd.producto_id AND uhp.id_unidad=cd.unidad_id')
            ->where('cd.cotizacion_id', $cotizacion->id)
            ->group_by('cd.id')
            ->get()->result();

        foreach ($cotizacion->detalles as $detalle) {
            $detalle->cantidad_minima = $this->unidades_model->convert_minimo_by_um($detalle->producto_id, $detalle->unidad_id, $detalle->cantidad);
            $detalle->cantidad_almacen_minima = $this->unidades_model->convert_minimo_um($detalle->producto_id, $detalle->cantidad_almacen, $detalle->fraccion_almacen);
            $detalle->um_min = $this->unidades_model->get_um_min_by_producto($detalle->producto_id);
            $detalle->um_min_abr = $this->unidades_model->get_um_min_by_producto_abr($detalle->producto_id);
        }

        return $cotizacion;
    }

    function prepare_cotizacion($id)
    {

        $cotizacion = $this->get_cotizaciones(array('id' => $id));

        $cotizacion->detalles = $this->db->select('
            cd.id as detalle_id,
            c.local_id AS local_id,
            local.local_nombre AS local_nombre,
            cd.producto_id as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            cd.precio as precio,
            cd.cantidad as cantidad,
            (SELECT cantidad FROM producto_almacen WHERE id_producto = cd.producto_id AND id_local = c.local_id LIMIT 1) AS cantidad_almacen,
            (SELECT fraccion FROM producto_almacen WHERE id_producto = cd.producto_id AND id_local = c.local_id LIMIT 1) AS fraccion_almacen,
            cd.unidad_id as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            SUM(cd.precio * cd.cantidad) as importe,
            cd.impuesto as impuesto,
            cd.precio_venta as precio_venta
            ')
            ->from('cotizacion_detalles as cd')
            ->join('cotizacion as c', 'c.id=cd.cotizacion_id')
            ->join('local', 'local.int_local_id=c.local_id')
            ->join('producto', 'producto.producto_id=cd.producto_id')
            ->join('unidades', 'unidades.id_unidad=cd.unidad_id')
            ->where('cd.cotizacion_id', $cotizacion->id)
            ->group_by('cd.id')
            ->get()->result();

        $result = array();

        foreach ($cotizacion->detalles as $detalle) {

            if (!isset($result[$detalle->producto_id])) {
                $result[$detalle->producto_id] = new stdClass();
                $result[$detalle->producto_id]->producto_nombre = $detalle->producto_nombre;
                $result[$detalle->producto_id]->producto_id = $detalle->producto_id;
                $result[$detalle->producto_id]->impuesto = $detalle->impuesto;
                $result[$detalle->producto_id]->precio = $detalle->precio;
                $result[$detalle->producto_id]->um_min = $this->unidades_model->get_um_min_by_producto($detalle->producto_id);
                $result[$detalle->producto_id]->um_min_abr = $this->unidades_model->get_um_min_by_producto_abr($detalle->producto_id);
                $result[$detalle->producto_id]->total_min = 0;
                $result[$detalle->producto_id]->unidades = array();
                $unidades = $this->unidades_model->get_unidades_precios($detalle->producto_id, 3);
                foreach ($unidades as $unidad) {
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad] = new stdClass();
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_id = $unidad->id_unidad;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_nombre = $unidad->nombre_unidad;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidad_abr = $unidad->abr;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->cantidad = 0;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->unidades = $unidad->unidades;
                    $result[$detalle->producto_id]->unidades[$unidad->id_unidad]->orden = $unidad->orden;
                }
            }
            $result[$detalle->producto_id]->unidades[$detalle->unidad_id]->cantidad = $detalle->cantidad;
            $result[$detalle->producto_id]->total_min += $this->unidades_model->convert_minimo_by_um($detalle->producto_id, $detalle->unidad_id, $detalle->cantidad);

        }

        $cotizacion->detalles = $result;
        return $cotizacion;
    }

    public function save($cotizar, $detalles)
    {
        $this->db->trans_start();


        $this->db->insert('cotizacion', $cotizar);
        $id = $this->db->insert_id();

        $data = array();
        foreach ($detalles as $detalle) {
            $temp = array(
                'cotizacion_id' => $id,
                'producto_id' => $detalle->id_producto,
                'unidad_id' => $detalle->unidad_medida,
                'precio' => $detalle->precio,
                'cantidad' => $detalle->cantidad,
                'impuesto' => $detalle->producto_impuesto,
                'precio_venta' => $detalle->precio_venta
            );

            array_push($data, $temp);
        }
        $this->db->insert_batch('cotizacion_detalles', $data);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_off();
            return false;
        } else {
            $this->db->trans_off();
            return $id;
        }
    }


}