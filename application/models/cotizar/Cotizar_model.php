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
    }

    function get_cotizaciones($where = array())
    {
        $this->db->select('
            c.id as id,
            c.fecha as fecha,
            c.documento_id as documento_id,
            documentos.des_doc as documento_nombre,
            c.cliente_id as cliente_id,
            cliente.razon_social as cliente_nombre,
            cliente.identificacion as ruc,
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
            c.periodo_per as periodo_per
            ')
            ->from('cotizacion AS c')
            ->join('documentos', 'c.documento_id=documentos.id_doc')
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

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('c.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('c.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('c.fecha >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min'] . " 00:00:00");
            $this->db->where('c.fecha <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day . " 23:59:59");
        }

        return $this->db->get()->result();
    }

    function get_cotizaciones_totales($where = array())
    {
        $this->db->select('
            SUM(c.total * IF(c.tasa_cambio=0, 1 ,c.tasa_cambio)) as total,
            SUM(c.impuesto * IF(c.tasa_cambio=0, 1 ,c.tasa_cambio)) as impuesto,
            SUM(c.subtotal * IF(c.tasa_cambio=0, 1 ,c.tasa_cambio)) as subtotal
            ')
            ->from('cotizacion as c');


        if (isset($where['id'])) {
            $this->db->where('c.id', $where['id']);
            return $this->db->get()->row();
        }


        if (isset($where['estado']))
            $this->db->where('c.estado', $where['estado']);

        if (isset($where['fecha_ini']) && isset($where['fecha_fin'])) {
            $this->db->where('c.fecha >=', date('Y-m-d H:i:s', strtotime($where['fecha_ini'] . " 00:00:00")));
            $this->db->where('c.fecha <=', date('Y-m-d H:i:s', strtotime($where['fecha_fin'] . " 23:59:59")));
        }

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('c.fecha >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min']);
            $this->db->where('c.fecha <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day);
        }

        return $this->db->get()->row();
    }

    function get_cotizar_detalle($id)
    {
        $cotizacion = $this->get_cotizaciones(array('id' => $id));

        $cotizacion->detalles = $this->db->select('
            cd.id as detalle_id,
            cd.producto_id as producto_id,
            producto.producto_codigo_interno as producto_codigo_interno,
            producto.producto_nombre as producto_nombre,
            cd.precio as precio,
            cd.cantidad as cantidad,
            cd.unidad_id as unidad_id,
            unidades.nombre_unidad as unidad_nombre,
            unidades.abreviatura as unidad_abr,
            SUM(cd.precio * cd.cantidad) as importe
            ')
            ->from('cotizacion_detalles as cd')
            ->join('producto', 'producto.producto_id=cd.producto_id')
            ->join('unidades', 'unidades.id_unidad=cd.unidad_id')
            ->where('cd.cotizacion_id', $cotizacion->id)
            ->get()->result();

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
            return true;
        }
    }


}