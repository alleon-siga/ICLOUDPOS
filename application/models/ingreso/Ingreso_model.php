<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingreso_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('inventario/inventario_model');
        $this->load->model('traspaso/traspaso_model');
        $this->load->model('historico/historico_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('producto_serie/producto_serie_model');
        $this->load->model('cajas/cajas_model');
        $this->error = null;
    }

    function get_deuda_detalle($ingreso_id)
    {
        $consulta = "
            SELECT 
                ingreso.id_ingreso AS ingreso_id, 
                ingreso.total_ingreso AS total,
                ingreso.tipo_documento AS documento,
                CONCAT( ingreso.documento_serie, ' - ', ingreso.documento_numero) AS documento_numero, 
                moneda.simbolo as simbolo,
                proveedor.proveedor_nombre as proveedor,
                (SELECT 
                        SUM(pagos_ingreso.pagoingreso_monto)
                    FROM
                        pagos_ingreso
                    WHERE
                        pagos_ingreso.pagoingreso_ingreso_id = ingreso.id_ingreso) AS monto_pagado
            FROM 
                ingreso 
            JOIN 
                moneda ON moneda.id_moneda = ingreso.id_moneda 
            JOIN 
                proveedor ON ingreso.int_Proveedor_id = proveedor.id_proveedor  
            WHERE 
                ingreso.id_ingreso = " . $ingreso_id . " 
        ";

        $ingreso = $this->db->query($consulta)->row();

        $ingreso->detalles = $this->db->get_where('pagos_ingreso', array('pagoingreso_ingreso_id' => $ingreso_id))->result();

        return $ingreso;
    }

    function get_compras($data)
    {
        $this->db->select("
            ingreso.id_ingreso as id,
            ingreso.fecha_emision as fecha_emision,
            ingreso.tipo_documento as documento,
            CONCAT(ingreso.documento_serie, ' - ', ingreso.documento_numero) as documento_numero,
            proveedor.proveedor_ruc as proveedor_ruc,
            proveedor.proveedor_nombre as proveedor_nombre,
            ingreso.pago as tipo_pago,
            moneda.nombre as moneda_nombre,
            ingreso.tasa_cambio as tasa,
            ingreso.sub_total_ingreso as subtotal,
            ingreso.impuesto_ingreso as impuesto,
            ingreso.total_ingreso as total,
            ingreso.ingreso_status as estado,
            usuario.username as usuario_nombre,
            ingreso.fecha_registro as fecha_registro,
            ")
            ->from('ingreso')
            ->join('proveedor', 'proveedor.id_proveedor = ingreso.int_Proveedor_id')
            ->join('moneda', 'moneda.id_moneda = ingreso.id_moneda', 'left')
            ->join('usuario', 'usuario.nUsuCodigo = ingreso.nUsuCodigo');

        $this->set_where_compras($data);

        return $this->db->get()->result();
    }

    function get_totales_compra($data)
    {
        $this->db->select("
            SUM(ingreso.sub_total_ingreso) as subtotal,
            SUM(ingreso.impuesto_ingreso) as impuesto,
            SUM(ingreso.total_ingreso) as total
            ")
            ->from('ingreso');

        $this->set_where_compras($data, true);

        return $this->db->get()->row();
    }

    private function set_where_compras($data, $totales = false)
    {
        if (isset($data['local_id']))
            $this->db->where('ingreso.local_id', $data['local_id']);

        if (isset($data['estado']))
            $this->db->where('ingreso.ingreso_status', $data['estado']);
        else {
            if ($totales == false)
                $this->db->where("(ingreso.ingreso_status = 'COMPLETADO' OR ingreso.ingreso_status = 'ANULADO')");
            else
                $this->db->where("(ingreso.ingreso_status = 'COMPLETADO')");
        }

        if (isset($data['moneda_id']))
            $this->db->where('ingreso.id_moneda', $data['moneda_id']);

        if (isset($data['fecha_ini']) && isset($data['fecha_fin'])) {
            $this->db->where('ingreso.fecha_registro >=', date('Y-m-d H:i:s', strtotime($data['fecha_ini'] . " 00:00:00")));
            $this->db->where('ingreso.fecha_registro <=', date('Y-m-d H:i:s', strtotime($data['fecha_fin'] . " 23:59:59")));
        }

        if (isset($data['mes']) && isset($data['year']) && isset($data['dia_min']) && isset($data['dia_max'])) {
            $last_day = last_day($data['year'], sumCod($data['mes'], 2));
            if ($last_day > $data['dia_max'])
                $last_day = $data['dia_max'];

            $this->db->where('ingreso.fecha_registro >=', $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $data['dia_min'] . " 00:00:00");
            $this->db->where('ingreso.fecha_registro <=', $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $last_day . " 23:59:59");
        }
    }

    function get_monedas($id_moneda = false)
    {
        if ($id_moneda == false)
            return $this->db->get('vw_monedas_cajas')->result_array();
        else
            return $this->db->get_where('vw_monedas_cajas', array('id_moneda' => $id_moneda))->row_array();
    }

    function get_all()
    {

        $this->db->join('local', 'local.int_local_id = ingreso.local_id');
        $this->db->join('proveedor', 'proveedor.id_proveedor = ingreso.int_Proveedor_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo = ingreso.nUsuCodigo');
        $query = $this->db->get('ingreso');
        return $query->result_array();


    }


    function productos()
    {

        $this->db->join('producto', 'producto.producto_id = detalleingreso.id_producto');
        $this->db->group_by("id_producto");
        $query = $this->db->get('detalleingreso');
        return $query->result_array();

    }


    function insertar_compra($cab_pie, $detalle, $credito, $cuotas)
    {

        $this->load->model('unidades_has_precio/unidades_has_precio_model');
        $this->load->model('precio/precios_model');

        $valid_doc = $this->db->get_where('ingreso', array(
            'tipo_documento' => $cab_pie['cboTipDoc'],
            'documento_serie' => $cab_pie['doc_serie'],
            'documento_numero' => $cab_pie['doc_numero'],
        ))->row();

        if ($valid_doc != NULL) {
            $this->error = 'El numero de documento ya existe';
            return false;
        }

        $this->db->trans_start();

        $precio_de_venta = $this->precios_model->get_by('nombre_precio', "Precio Venta");


        $compra = array(
            'fecha_registro' => $cab_pie['fecReg'],
            'int_Proveedor_id' => $cab_pie['cboProveedor'],
            'nUsuCodigo' => $this->session->userdata('nUsuCodigo'),
            'fecha_emision' => $cab_pie['fecEmision'],
            'local_id' => $cab_pie['local_id'],
            'tipo_documento' => $cab_pie['cboTipDoc'],
            'documento_serie' => $cab_pie['doc_serie'],
            'documento_numero' => $cab_pie['doc_numero'],
            'ingreso_status' => $cab_pie['status'],
            'impuesto_ingreso' => $cab_pie['montoigv'],
            'tipo_ingreso' => $cab_pie['tipo_ingreso'],
            'sub_total_ingreso' => $cab_pie['subTotal'],
            'total_ingreso' => $cab_pie['totApagar'],
            'pago' => $cab_pie['pago'],
            'ingreso_observacion' => $cab_pie['ingreso_observacion'],
            'id_moneda' => $cab_pie['id_moneda'],
            'tasa_cambio' => $cab_pie['tasa_cambio'],
            'costo_por' => isset($cab_pie['costo_por']) ? $cab_pie['costo_por'] : 0,
            'utilidad_por' => isset($cab_pie['utilidad_por']) ? $cab_pie['utilidad_por'] : 0,
            'tipo_impuesto' => $cab_pie['tipo_impuesto']
        );

        $this->db->insert('ingreso', $compra);
        $insert_id = $this->db->insert_id();

        if ($compra['ingreso_status'] == 'COMPLETADO' && $compra['total_ingreso'] > 0 && $compra['pago'] == 'CONTADO') {
            $moneda_id = $compra['id_moneda'];
            $this->cajas_model->save_pendiente(array(
                'monto' => $compra['total_ingreso'],
                'tipo' => 'COMPRA',
                'IO' => 2,
                'ref_id' => $insert_id,
                'moneda_id' => $moneda_id,
                'local_id' => $compra['local_id']
            ));
        } else if ($compra['ingreso_status'] == 'COMPLETADO' && $compra['total_ingreso'] > 0 && $compra['pago'] == 'CREDITO') {
            if ($credito['c_inicial'] > 0) {
                $moneda_id = $compra['id_moneda'];
                $this->cajas_model->save_pendiente(array(
                    'monto' => $credito['c_inicial'],
                    'tipo' => 'COMPRA',
                    'IO' => 2,
                    'ref_id' => $insert_id,
                    'moneda_id' => $moneda_id,
                    'local_id' => $compra['local_id']
                ));
            }

            $this->save_credito($insert_id, $credito, $cuotas);
        }

        $data = array();

        $local_id = $cab_pie['local_id'];


        $cantidad_minima = array();
        $costo_unitario = array();

        if ($detalle != null) {
            foreach ($detalle as $row) {

                //INSERTO LAS SERIES DE LOS PRODUCTOS
                if (getProductoSerie() == "SI") {
                    $serie = array(
                        'producto_id' => $row->producto_id,
                        'local_id' => $local_id);

                    foreach ($row->series as $s) {
                        if (isset($s) && $s != "") {
                            $serie['serie'] = $s;
                            $this->producto_serie_model->insertar($serie);
                        }
                    }
                }

                //LLEVO TODAS LAS cantidadES DE LOS PRODCUTOS A SU MINIMA EXPRESION
                if (!isset($cantidad_minima[$row->producto_id]))
                    $cantidad_minima[$row->producto_id] = 0;
                $cantidad_minima[$row->producto_id] += $this->unidades_model->convert_minimo_by_um($row->producto_id, $row->unidad, $row->cantidad);

                //AGREGO MIS COSTOS
                if (!isset($costo_unitario[$row->producto_id]))
                    $costo_unitario[$row->producto_id] = array();
                $costo_unitario[$row->producto_id][] = array(
                    'costo' => $row->importe / $row->cantidad,
                    'um_id' => $row->unidad
                );

                /*se guarda el precio de venta, para poder sacar el reporte de Ingreso Detalle,
            se guarda el precio de venta que tenia para ese momento*/
                $precio_venta = 0;

                if (!empty($precio_de_venta)) {
                    $where = array(
                        'id_producto' => $row->producto_id,
                        'id_unidad' => $row->unidad,
                        'id_precio' => $precio_de_venta['id_precio']
                    );
                    $query_precio = $this->unidades_has_precio_model->get_precio($where);

                    if (count($query_precio) > 0) {
                        $precio_venta = $query_precio[0]['precio'];

                    }
                }

                $p = $this->db
                    ->join('impuestos', 'impuestos.id_impuesto=producto.producto_impuesto')
                    ->get_where('producto', array('producto_id' => $row->producto_id))->row();

                //INSERTO EL INGRESO EN DETALLE INGRESO (LO INSERTO TAL CUAL SE INSERTABA PARA EVITAR PROBLEMAS DE COMPATIBILIDAD)
                $data = array(
                    'id_ingreso' => $insert_id,
                    'id_producto' => $row->producto_id,
                    'cantidad' => $row->cantidad,
                    'precio' => ($row->costo_unitario === 'null') ? 0 : $row->costo_unitario,
                    'unidad_medida' => $row->unidad,
                    'total_detalle' => (!isset($row->importe)) ? 0 : $row->importe,
                    'status' => 1,
                    'precio_venta' => $precio_venta,
                    'impuesto_id' => $p->id_impuesto,
                    'impuesto_porciento' => $p->porcentaje_impuesto
                );
                $this->db->insert('detalleingreso', $data);

            }


            //ACTUALIZO LOS INGRESOS EN ALMACEN y guardos sus historicos
            $this->load->model('historico/historico_model');
            foreach ($cantidad_minima as $key => $value) {

                $old_cantidad = $this->db->get_where('producto_almacen', array(
                    "id_local" => $local_id,
                    "id_producto" => $key
                ))->row();

                //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
                $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

                //Aqui devuelvo el desglose de la cantidad mayor con UM mayor y cantidad menor (fraccion) con UM menos
                $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);

                /*esto dependiendo de si es registro de existencia o un ingreso normal*/
                /*si costo es true, es ingreso normal.*/
                if ($cab_pie['costos'] === 'true') {
                    $tipo_movimiento = "INGRESO";
                    $tipo_operacion = "ENTRADA";
                    $referencia_valor = 'Se realizo un Ingreso';
                } else {
                    $tipo_movimiento = "REGISTRO";
                    $tipo_operacion = "ENTRADA";
                    $referencia_valor = 'Se realizo un registro de existencia';
                }


                //CREAR EL HISTORICO DEL INGRESO *************************************
                if ($compra['ingreso_status'] == 'COMPLETADO') {
                    $tipo = 0;
                    if ($compra['tipo_documento'] == 'FACTURA')
                        $tipo = 1;
                    if ($compra['tipo_documento'] == 'BOLETA DE VENTA')
                        $tipo = 3;

                    $values = array(
                        'fecha' => $compra['fecha_emision'],
                        'local_id' => $local_id,
                        'producto_id' => $key,
                        'cantidad' => $value,
                        'io' => 1,
                        'tipo' => $tipo,
                        'operacion' => 2,
                        'serie' => $compra['documento_serie'],
                        'numero' => $compra['documento_numero'],
                        'ref_id' => $insert_id
                    );
                    $this->kardex_model->set_kardex($values);
                }


                if ($old_cantidad != NULL) {
                    $this->inventario_model->update_producto_almacen($key, $local_id, array(
                        'cantidad' => $result['cantidad'],
                        'fraccion' => $result['fraccion']));
                } else
                    $this->inventario_model->insert_producto_almacen($key, $local_id, $result['cantidad'], $result['fraccion']);
            }

            //AQUI VOY HACER EL COSTO UNITARIO
            /*pregunto si existe la moneda, ya que en el caso de registro de existencia no se coloca la moneda*/

            if (isset($cab_pie['id_moneda']) and $cab_pie['id_moneda'] != false) {

                $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
                foreach ($costo_unitario as $key => $value) {
                    $temp = 0;
                    $n = 0;

                    foreach ($value as $costo) {
                        $n++;
                        $temp += $this->unidades_model->get_maximo_costo($key, $costo['um_id'], $costo['costo']);
                    }

                    $new_costo_unitario = number_format(($temp / $n), 2);

                    $this->db->where(array('producto_id' => $key));
                    $this->db->update('producto', array('producto_costo_unitario' => $new_costo_unitario));

                    $this->producto_costo_unitario_model->save_costos(array(
                        'producto_id' => $key,
                        'moneda_id' => $compra['id_moneda'],
                        'costo' => $new_costo_unitario,
                        'activo' => '1'
                    ), $compra['tasa_cambio']);
                }

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return $insert_id;
        }

    }

    function actualizar_detalle($detalle, $ingreso_id)
    {
        /*este metodo guarda el detalle del ingreso*/

        $this->load->model('unidades_has_precio/unidades_has_precio_model');
        $this->load->model('precio/precios_model');

        $this->db->trans_start();

        $precio_de_venta = $this->precios_model->get_by('nombre_precio', "Precio Venta");


        $costo_unitario = array();
        $cantidad_minima = array();

        foreach ($detalle as $row) {

            //LLEVO TODAS LAS CANTIDADES DE LOS PRODCUTOS A SU MINIMA EXPRESION
            if (!isset($cantidad_minima[$row->producto_id]))
                $cantidad_minima[$row->producto_id] = 0;
            $cantidad_minima[$row->producto_id] += $this->unidades_model->convert_minimo_by_um($row->producto_id, $row->unidad, $row->cantidad);

            //AGREGO MIS COSTOS
            if (!isset($costo_unitario[$row->producto_id]))
                $costo_unitario[$row->producto_id] = array();
            $costo_unitario[$row->producto_id][] = array(
                'costo' => $row->importe / $row->cantidad,
                'um_id' => $row->unidad
            );


            /*se guarda el precio de venta, para poder sacar el reporte de Ingreso Detalle,
            se guarda el precio de venta que tenia para ese momento*/
            $precio_venta = 0;

            if (!empty($precio_de_venta)) {
                $where = array(
                    'id_producto' => $row->producto_id,
                    'id_unidad' => $row->unidad,
                    'id_precio' => $precio_de_venta['id_precio']
                );
                $query_precio = $this->unidades_has_precio_model->get_precio($where);

                if (count($query_precio) > 0) {
                    $precio_venta = $query_precio[0]['precio'];

                }
            }

            //ACTUALIZO EL INGRESO EN DETALLEINGRESO
            $data = array(
                'id_ingreso' => $ingreso_id,
                'precio' => ($row->costo_unitario === 'null') ? 0 : $row->costo_unitario,
                'total_detalle' => (!isset($row->importe)) ? 0 : $row->importe,
                'status' => 1,
                'precio_venta' => $precio_venta
            );

            $condicion = array(
                'id_ingreso' => $ingreso_id,
                'id_producto' => $row->producto_id,
                'unidad_medida' => $row->unidad,
            );
            $this->db->where($condicion);
            $this->db->update('detalleingreso', $data);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return array($cantidad_minima, $costo_unitario);
        }


    }

    function save_credito($compra_id, $credito, $cuotas)
    {

        $this->db->insert('ingreso_credito', array(
            'ingreso_id' => $compra_id,
            'numero_cuotas' => count($cuotas),
            'monto_cuota' => $credito['c_precio_credito'],
            'monto_debito' => 0,
            'estado' => 'PENDIENTE',
            'inicial' => $credito['c_inicial'],
            'periodo_gracia' => $credito['c_periodo_gracia'],
            'ultima_fecha_pago' => null
        ));

        foreach ($cuotas as $cuota) {
            $this->db->insert('ingreso_credito_cuotas', array(
                'ingreso_id' => $compra_id,
                'monto' => $cuota->monto,
                'letra' => $cuota->letra,
                'fecha_vencimiento' => date('Y-m-d', strtotime(str_replace('/', '-', $cuota->fecha))),
                'pagado' => 0,
                'fecha_cancelada' => null
            ));
        }

    }

    function guardar_detalle_contable($detalle, $ingreso_id)
    {
        /*este metodo guarda el detalle del ingreso_contable*/
        $this->db->trans_start();


        $costo_unitario = array();
        $cantidad_minima = array();

        foreach ($detalle as $row) {

            //LLEVO TODAS LAS CANTIDADES DE LOS PRODCUTOS A SU MINIMA EXPRESION
            if (!isset($cantidad_minima[$row->producto_id]))
                $cantidad_minima[$row->producto_id] = 0;
            $cantidad_minima[$row->producto_id] += $this->unidades_model->convert_minimo_by_um($row->producto_id, $row->unidad, $row->cantidad);

            //AGREGO MIS COSTOS
            if (!isset($costo_unitario[$row->producto_id]))
                $costo_unitario[$row->producto_id] = array();
            $costo_unitario[$row->producto_id][] = array(
                'costo' => $row->importe / $row->cantidad,
                'um_id' => $row->unidad
            );


            //ACTUALIZO EL INGRESO EN DETALLEINGRESO
            $data = array(
                'id_ingreso' => $ingreso_id,
                'precio' => ($row->costo_unitario === 'null') ? 0 : $row->costo_unitario,
                'total_detalle' => (!isset($row->importe)) ? 0 : $row->importe,
                'id_producto' => $row->producto_id,
                'unidad_medida' => $row->unidad,
                'cantidad' => $row->cantidad,
                'status' => 1
            );

            $this->db->insert('detalleingreso_contable', $data);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return array($cantidad_minima, $costo_unitario);
        }
        $this->db->trans_off();

    }

    function update_compra($cab_pie, $detalle, $credito, $cuotas)
    {

        $this->db->trans_start();

        $compra_id = $cab_pie['id_ingreso'];

        /*$cab_pie['facturar'] define si se va a facturar el ingreso o no*/
        if ($cab_pie['facturar'] == "NO") {
            /*aqui entra solo, para registrar los costos o valorizar el ingreso*/

            $compra = array(
                'pago' => $cab_pie['pago'],
                'fecha_registro' => date('Y-m-d H:i:s'),
                'fecha_emision' => $cab_pie['fecEmision'],
                'ingreso_observacion' => $cab_pie['ingreso_observacion'],
                'tipo_documento' => $cab_pie['cboTipDoc'],
                'documento_serie' => $cab_pie['doc_serie'],
                'documento_numero' => $cab_pie['doc_numero'],
                'ingreso_status' => $cab_pie['status'],
                'impuesto_ingreso' => $cab_pie['montoigv'],
                'sub_total_ingreso' => $cab_pie['subTotal'],
                'total_ingreso' => $cab_pie['totApagar'],
                'id_moneda' => $cab_pie['id_moneda'],
                'tasa_cambio' => $cab_pie['tasa_cambio'],
                'tipo_impuesto' => $cab_pie['tipo_impuesto']
            );


            /*actualizo el ingreso*/
            $this->db->where('id_ingreso', $compra_id);
            $this->db->update('ingreso', $compra);

            $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $compra_id))->row();
            if ($compra['total_ingreso'] > 0 && $ingreso->pago == 'CONTADO') {
                $moneda_id = $compra['id_moneda'];
                $this->cajas_model->update_pendiente(array(
                    'monto' => $compra['total_ingreso'],
                    'tipo' => 'COMPRA',
                    'ref_id' => $compra_id,
                    'moneda_id' => $moneda_id,
                    'local_id' => $ingreso->local_id
                ));
            } else if ($compra['total_ingreso'] > 0 && $ingreso->pago == 'CREDITO') {
                if ($credito['c_inicial'] > 0) {
                    $moneda_id = $compra['id_moneda'];
                    $this->cajas_model->save_pendiente(array(
                        'monto' => $credito['c_inicial'],
                        'tipo' => 'COMPRA',
                        'IO' => 2,
                        'ref_id' => $compra_id,
                        'moneda_id' => $moneda_id,
                        'local_id' => $compra['local_id']
                    ));
                }

                $this->save_credito($compra_id, $credito, $cuotas);
            }

            $local_id = $cab_pie['local_id'];
            $compra_id = $cab_pie['id_ingreso'];

            /**********SUMOO AL INVETARIO TODOS LOS ITEMS DE LA COMPRA***********/
            //quito esto siempre .- jhainey
            // if ($venta_cabecera['devolver'] == 'true') {
            $sql_detalle_ingreso = $this->db->query("SELECT * FROM detalleingreso
JOIN producto ON producto.producto_id=detalleingreso.id_producto
LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
WHERE detalleingreso.id_ingreso='$compra_id'");

            $query_detalle_ingreso = $sql_detalle_ingreso->result_array();


            // Detalle de la Venta
            foreach ($query_detalle_ingreso as $row) {
                $cantidad_venta = $row['cantidad'];
                $unidad_medida_venta = $row['unidad_medida'];
                $id_producto = $row['id_producto'];
                $precio = $row['precio'];
                $importe = $row['total_detalle'];

                $query = $this->db->query('SELECT *
				FROM producto_almacen where id_producto=' . $id_producto . ' and id_local=' . $cab_pie['local_id']);
                $inventario_existente = $query->row_array();
                $cantidad_vieja = 0;
                $fraccion_vieja = 0;

                if (isset($inventario_existente['cantidad'])) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                }
                if (isset($inventario_existente['fraccion'])) {
                    $fraccion_vieja = $inventario_existente['fraccion'];
                }

                // CALCULOS DE UNDIAD DE MEDIDA
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                $unidades_producto = $query->result_array();

                $unidad_maxima = $unidades_producto[0];
                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                $unidad_form = 0;
                foreach ($unidades_producto as $up) {
                    if ($up['id_unidad'] == $unidad_medida_venta) {
                        $unidad_form = $up;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
                $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

                $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

                if ($suma_cantidades >= $unidad_maxima['unidades']) {
                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division);
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {
                    if ($suma_cantidades < $unidad_maxima['unidades']) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = +$suma_cantidades;
                    } else {
                        $cantidad_nueva = $cantidad_vieja;
                        $fraccion_nueva = +$suma_cantidades;
                    }
                }

                if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                    $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                    $fraccion_nueva = $fraccion_vieja;
                }

                if ($unidad_medida_venta == $unidad_minima['id_unidad']) {
                    if ($suma_cantidades >= $unidad_maxima['unidades']) {
                        $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                        $cantidad_nueva = intval($resultado_division);
                        $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                        $fraccion_nueva = $resto_division;
                    } else {
                        if ($suma_cantidades < $unidad_maxima['unidades']) {
                            $cantidad_nueva = 0;
                            $fraccion_nueva = +$suma_cantidades;
                        } else {
                            if ($cantidad_vieja > 0) {
                                $cantidad_nueva = $cantidad_vieja;
                                $fraccion_nueva = $suma_cantidades;
                            } else {
                                if (count($unidades_producto) > 1) {
                                    $cantidad_nueva = 0;
                                    $fraccion_nueva = $cantidad_venta;
                                } else {
                                    $cantidad_nueva = $cantidad_venta;
                                    $fraccion_nueva = 0;
                                }
                            }
                        }
                    }
                }


                if (count($inventario_existente) > 0) {
                    $inventario_nuevo = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );
                    $this->inventario_model->update_producto_almacen($id_producto, $cab_pie['local_id'], $inventario_nuevo);
                } else {
                    $inventario_nuevo = array(
                        'id_local' => $cab_pie['local_id'],
                        'id_producto' => $id_producto,
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );
                    $this->db->insert('producto_almacen', $inventario_nuevo);
                }
            }

            /*esats dos variables son los valores retornados de este metodo guardar_detalle*/
            $guardar_detalle = $this->actualizar_detalle($detalle, $compra_id);
            $cantidad_minima = $guardar_detalle[0];
            $costo_unitario = $guardar_detalle[1];


            //ACTUALIZO LOS INGRESOS EN ALMACEN y guardos sus historicos
            $this->load->model('historico/historico_model');
            foreach ($cantidad_minima as $key => $value) {

                $old_cantidad = $this->db->get_where('producto_almacen', array(
                    "id_local" => $local_id,
                    "id_producto" => $key
                ))->row();

                //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
                $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

                //Aqui devuelvo el desglose de la cantidad mayor con UM mayor y cantidad menor (fraccion) con UM menos
                $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);

                //CREAR EL HISTORICO DEL INGRESO *************************************
                $tipo = 0;
                if ($compra['tipo_documento'] == 'FACTURA')
                    $tipo = 1;
                if ($compra['tipo_documento'] == 'BOLETA DE VENTA')
                    $tipo = 3;

                $values = array(
                    'fecha' => $compra['fecha_emision'],
                    'local_id' => $local_id,
                    'producto_id' => $key,
                    'cantidad' => $value,
                    'io' => 1,
                    'tipo' => $tipo,
                    'operacion' => 2,
                    'serie' => $compra['documento_serie'],
                    'numero' => $compra['documento_numero'],
                    'ref_id' => $compra_id
                );
                $this->kardex_model->set_kardex($values);

                if ($old_cantidad != NULL) {
                    $this->inventario_model->update_producto_almacen($key, $local_id, array(
                        'cantidad' => $result['cantidad'],
                        'fraccion' => $result['fraccion']));
                } else
                    $this->inventario_model->insert_producto_almacen($key, $local_id, $result['cantidad'], $result['fraccion']);
            }

            //AQUI VOY HACER EL COSTO UNITARIO
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
            foreach ($costo_unitario as $key => $value) {
                $temp = 0;
                $n = 0;

                foreach ($value as $costo) {
                    $n++;
                    $temp += $this->unidades_model->get_maximo_costo($key, $costo['um_id'], $costo['costo']);
                }

                $new_costo_unitario = number_format(($temp / $n), 2);

                $this->db->where(array('producto_id' => $key));
                $this->db->update('producto', array('producto_costo_unitario' => $new_costo_unitario));

                var_dump($new_costo_unitario);
                $this->producto_costo_unitario_model->save_costos(array(
                    'producto_id' => $key,
                    'moneda_id' => $compra['id_moneda'],
                    'costo' => $new_costo_unitario,
                    'activo' => '1'
                ), $compra['tasa_cambio']);
            }

        } else {
            /*aqui entra solo cuando es facturacion*/

            $compra = array(
                'fecha_registro' => $cab_pie['fecReg'],
                'int_Proveedor_id' => $cab_pie['cboProveedor'],
                'nUsuCodigo' => $this->session->userdata('nUsuCodigo'),
                'fecha_emision' => $cab_pie['fecEmision'],
                'local_id' => $cab_pie['local_id'],
                'tipo_documento' => $cab_pie['cboTipDoc'],
                'documento_serie' => $cab_pie['doc_serie'],
                'documento_numero' => $cab_pie['doc_numero'],
                'ingreso_status' => $cab_pie['status'],
                'impuesto_ingreso' => $cab_pie['montoigv'],
                'tipo_ingreso' => $cab_pie['tipo_ingreso'],
                'sub_total_ingreso' => $cab_pie['subTotal'],
                'total_ingreso' => $cab_pie['totApagar'],
                'pago' => $cab_pie['pago'],
                'ingreso_observacion' => $cab_pie['ingreso_observacion'],
                'id_moneda' => $cab_pie['id_moneda'],
                'tasa_cambio' => $cab_pie['tasa_cambio'],
                'factura_ingreso_id' => $compra_id,
                'facturado' => 1
            );

            /*guardo el ingreso contable*/
            $ingreso_cotable = $this->insertar_ingreso_contable($compra);

            /*actualizo el ingreso, sobreel cual se hizo la facturacion, pero este queda con estatus ingreso_completado*/
            $facturado = array(
                'facturado' => 1
            );
            $this->db->where('id_ingreso', $compra_id);
            $this->db->update('ingreso', $facturado);

            $this->guardar_detalle_contable($detalle, $ingreso_cotable);
            $compra_id = $ingreso_cotable;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return $compra_id;
        }
        $this->db->trans_off();

    }


    function insertar_ingreso_contable($campos)
    {
        /*este metodo guarda en la tabla ingreso_contable, solo cuando es facturacion*/
        $this->db->trans_start();
        $this->db->insert('ingreso_contable', $campos);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return $insert_id;
        }
        $this->db->trans_off();


    }

    function update_inventario($campos, $wheres)
    {


        $this->db->trans_start();
        $this->db->where($wheres);
        $this->db->update('inventario', $campos);
        $this->db->trans_complete();


    }


    public $error = 'Ha ocurrido un error al procesar la solicitud';

    private function validar_anulacion($local, $cantidad_anulada)
    {
        $flag = false;

        foreach ($cantidad_anulada as $key => $value) {
            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            if ($old_cantidad_min >= $value)
                $flag = true;
            else {
                $this->error = 'Stock insuficiente en algun producto. No puede realizar la anulacion';
                $flag = false;
                break;
            }
        }

        return $flag;
    }

    function anular_ingreso()
    {

        $id = $this->input->post('id');
        $local = $this->input->post('local');
        $serie = $this->input->post('serie');
        $numero = $this->input->post('numero');

        $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $id))->row();

        $detalle_ingresos = $this->db->get_where('detalleingreso', array('id_ingreso' => $id))->result();
        $cantidad_anulada = array();
        foreach ($detalle_ingresos as $detalle) {

            //Agrupo las cantidades minimas por producto
            if (!isset($cantidad_anulada[$detalle->id_producto]))
                $cantidad_anulada[$detalle->id_producto] = 0;
            $cantidad_anulada[$detalle->id_producto] += $this->unidades_model->convert_minimo_by_um(
                $detalle->id_producto,
                $detalle->unidad_medida,
                $detalle->cantidad);
        }

        if (!$this->validar_anulacion($local, $cantidad_anulada))
            return false;

        //Cambio el status a 0 para saber que es un detalle de ingreso anulado
        $this->db->where('id_ingreso', $id);
        $this->db->update('detalleingreso', array('status' => '0'));

        foreach ($cantidad_anulada as $key => $value) {
            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            //Aqui devuelvo el desglose de la cantidad mayor con UM mayor y cantidad menor (fraccion) con UM menos
            if ($old_cantidad_min >= $value)
                $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min - $value);
            else {
                $result = NULL;
            }


            if ($old_cantidad != NULL && $result != NULL) {

                $tipo = "NP";
                if ($ingreso->tipo_documento == "BOLETA DE VENTA")
                    $tipo = "BO";
                if ($ingreso->tipo_documento == "FACTURA")
                    $tipo = "FA";

                $values = array(
                    'local_id' => $local,
                    'producto_id' => $key,
                    'cantidad' => $value * -1,
                    'io' => 1,
                    'tipo' => 7,
                    'operacion' => 6,
                    'serie' => $serie,
                    'numero' => $numero,
                    'ref_id' => $id,
                    'ref_val' => $tipo . " " . $ingreso->documento_serie . "-" . $ingreso->documento_numero
                );
                $this->kardex_model->set_kardex($values);

                $this->inventario_model->update_producto_almacen($key, $local, array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']));
            }
        }


        $ing = $this->db->get_where('ingreso', array('id_ingreso' => $id))->row();
        $condicion = array("id_ingreso" => $id);
        $campos = array('ingreso_status' => "ANULADO", 'fecha_registro' => $ing->fecha_registro);
        $this->db->where($condicion);
        $this->db->update('ingreso', $campos);


        $moneda_id = $ingreso->id_moneda;
        $this->cajas_model->delete_pendiente(array(
            'tipo' => 'COMPRA',
            'ref_id' => $ingreso->id_ingreso,
            'moneda_id' => $moneda_id,
            'local_id' => $ingreso->local_id
        ));

        return true;
    }

    function select_compra($fecInicio, $fecFin)
    {
        $this->db->select('*');
        $this->db->from('v_consulta_compras c');
        $this->db->where('c.FecRegistro >= ', $fecInicio);
        $this->db->where('c.FecRegistro <= ', $fecFin);
        $query = $this->db->get();
        return $query->result();
    }

    function get_detalles_by($ingreso_id)
    {
        $this->db->select(
            'producto.producto_id as id_producto,
            producto.producto_nombre as producto_nombre,
            detalleingreso.cantidad as cantidad,
            detalleingreso.precio as precio,
            detalleingreso.total_detalle as total_detalle,
            detalleingreso.unidad_medida as unidad_medida,
            unidades.nombre_unidad as nombre_unidad,
            unidades_has_producto.unidades as unidades
            ');

        $this->db->from('detalleingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->join('unidades', 'detalleingreso.unidad_medida=unidades.id_unidad');
        $this->db->join('unidades_has_producto', 'unidades.id_unidad=unidades_has_producto.id_unidad');

        $this->db->where('detalleingreso.id_ingreso', $ingreso_id);
        $this->db->where("`unidades_has_producto`.`producto_id` = `producto`.`producto_id`");
        $this->db->group_by('detalleingreso.id_detalle_ingreso');

        return $this->db->get()->result_array();
    }

    function get_ingresos_by($condicion)
    {
        $this->db->select('*');
        $this->db->from('ingreso');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id');
        $this->db->join('local', 'local.int_local_id=ingreso.local_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso.nUsuCodigo');
        //$this->db->join('detafecha_emisionlleingreso', 'detalleingreso.id_ingreso=ingreso.id_ingreso');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda', 'left');
        $this->db->where($condicion);
        $this->db->group_by('ingreso.id_ingreso');
        $query = $this->db->get();
        return $query->result();
    }

    function get_ingresos_by_estatus($condicion, $where_in)
    {
        $this->db->select('*');
        $this->db->from('ingreso');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id');
        $this->db->join('local', 'local.int_local_id=ingreso.local_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso.nUsuCodigo');
        //$this->db->join('detafecha_emisionlleingreso', 'detalleingreso.id_ingreso=ingreso.id_ingreso');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda', 'left');
        $this->db->where($condicion);
        if ($where_in != false) {
            $this->db->where_in('ingreso_status', $where_in);
        }
        $this->db->order_by('ingreso.fecha_registro');
        $this->db->group_by('ingreso.id_ingreso');
        $query = $this->db->get();
        return $query->result();
    }

    function get_ingresocontable_by_estatus($condicion, $where_in)
    {
        $this->db->select('*');
        $this->db->from('ingreso_contable');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso_contable.int_Proveedor_id');
        $this->db->join('local', 'local.int_local_id=ingreso_contable.local_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso_contable.nUsuCodigo');
        //$this->db->join('detafecha_emisionlleingreso', 'detalleingreso.id_ingreso=ingreso.id_ingreso');
        $this->db->join('moneda', 'moneda.id_moneda=ingreso_contable.id_moneda', 'left');
        $this->db->where($condicion);
        if ($where_in != false) {
            $this->db->where_in('ingreso_status', $where_in);
        }
        $this->db->order_by('ingreso_contable.fecha_registro');
        $this->db->group_by('ingreso_contable.id_ingreso');
        $query = $this->db->get();
        return $query->result();
    }

    function update_ingreso($tabla, $campos, $where)
    {
        $this->db->trans_start();
        $this->db->update($tabla, $campos);
        $this->db->where($where);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return true;
        }
    }


    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $group = false,
                             $order = false, $retorno = false)
    {
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);


        }

        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);

        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($group);
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

    function estadistica($query)
    {
        $sql = $this->db->query($query);
        return $sql->result_array();
    }


    function get_cronograma_by_cuotas($ingreso_id)
    {
        /*este metodo busca todas las cuotas, y el ultimo pago que se le realizo a una cuota*/
        $query = "
            SELECT
                IF((d.pagado = 1), '-', (DATEDIFF((d.fecha_vencimiento), CURDATE()) * -1)) as atraso,
                d.*,
                IFNULL((SELECT SUM(pagoingreso_monto) FROM pagos_ingreso WHERE pagoingreso_ingreso_id = d.id), 0) as monto_pagado
            FROM
                ingreso_credito_cuotas d
            WHERE
                d.ingreso_id = " . $ingreso_id . " 
            GROUP BY d.id
        ";
        return $this->db->query($query)->result();

    }

    public function pagar_cuota($idCuota, $montodescontar, $metodo_pago, $ingreso_id, $anticipado, $numero_ope, $banco, $tipo_metodo, $cuenta)
    {

        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');

        $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $ingreso_id))->row();
        $cuota = $this->db->get_where('ingreso_credito_cuotas', array('id' => $idCuota))->row();

        if ($tipo_metodo == 'BANCO') {
            $banco_selected = $this->db->get_where('banco', array('banco_id' => $banco))->row();
            $cuenta_id = $banco_selected->cuenta_id;
        } else {
            $cuenta_id = $cuenta;
        }

        $this->db->insert('pagos_ingreso', array(
            'pagoingreso_ingreso_id' => $idCuota,
            'pagoingreso_fecha' => date('Y-m-d H:i:s'),
            'pagoingreso_monto' => $montodescontar,
            'pagoingreso_restante' => 0,
            'pagoingreso_usuario' => $this->session->userdata('nUsuCodigo'),
            'medio_pago_id' => $metodo_pago,
            'banco_id' => $banco,
            'operacion' => $numero_ope,
            'estado' => 'PENDIENTE'
        ));
        $id = $this->db->insert_id();

        $this->cajas_model->save_pendiente(array(
            'monto' => $montodescontar,
            'tipo' => 'PAGOS_CUOTAS',
            'IO' => 2,
            'ref_id' => $id,
            'cuenta_id' => $cuenta_id
        ));

        $pagado = $this->db->select_sum('pagoingreso_monto', 'monto_total')
            ->from('pagos_ingreso')
            ->where('pagoingreso_ingreso_id', $cuota->id)
            ->get()->row();

        $restante = $cuota->monto - ($pagado->monto_total);
        if ($restante <= 0) {
            $this->db->where('id', $cuota->id);
            $this->db->update('ingreso_credito_cuotas', array(
                'fecha_cancelada' => date('Y-m-d H:i:s'),
                'pagado' => 1
            ));
        }

        $credito = $this->db->get_where('ingreso_credito', array('ingreso_id' => $ingreso_id))->row();
        $this->db->where('ingreso_id', $ingreso_id);
        if ($credito->monto_cuota > $credito->monto_debito + $montodescontar + 0.10) {
            $this->db->update('ingreso_credito', array(
                'monto_debito' => $credito->monto_debito + $montodescontar,
                'ultima_fecha_pago' => date('Y-m-d H:i:s')
            ));
        } else {
            $this->db->update('ingreso_credito', array(
                'monto_debito' => $credito->monto_debito + $montodescontar,
                'ultima_fecha_pago' => date('Y-m-d H:i:s'),
                'estado' => 'CANCELADA'
            ));
        }

    }
}
