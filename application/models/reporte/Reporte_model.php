<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reporte_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('unidades/unidades_model');
        $this->load->database();
    }

    function getProductoVendido($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";

        if($params['tipo']==1){ // Productos con ventas
            $where = "HAVING ventas IS NOT NULL";
        }elseif($params['tipo']==2){ //Productos sin ventas
            $where = "HAVING ventas IS NULL";
        }else{ //Todos
            $where = "";
        }
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        //Limitar top
        $limit = '';
        if(isset($params['limit'])){
            $limit = "LIMIT 0, ".$params['limit'];
        }
        $query = "SELECT p.producto_id AS producto_id, p.producto_codigo_interno AS producto_codigo_interno, p.producto_nombre AS producto_nombre,
            (
                SELECT SUM(up.unidades * dv.cantidad)
                FROM detalle_venta AS dv
                INNER JOIN venta v ON v.venta_id=dv.id_venta
                INNER JOIN producto p2 ON dv.id_producto=p2.producto_id
                INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
                AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad
                INNER JOIN unidades u ON up2.id_unidad=u.id_unidad
                WHERE dv.id_producto = p.producto_id AND v.venta_status='COMPLETADO' AND v.local_id = '".$params['local_id']."' AND v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'
            ) AS ventas,
            (
                SELECT SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion)
                FROM producto_almacen pa
                WHERE pa.id_producto=p.producto_id AND pa.id_local = '".$params['local_id']."'
            ) AS stock,
            (
                SELECT u.nombre_unidad
                FROM unidades u, producto p2, unidades_has_producto up, unidades_has_producto up2
                WHERE p2.producto_id = p.producto_id AND p2.producto_id=up.producto_id AND u.id_unidad=up.id_unidad AND p2.producto_id=up2.producto_id AND (SELECT id_unidad FROM unidades_has_producto WHERE unidades_has_producto.producto_id = p2.producto_id  ORDER BY orden DESC LIMIT 1) = up2.id_unidad 
                LIMIT 1
            ) AS nombre_unidad
            FROM producto p
            WHERE p.producto_estado='1' ".$search." ".$where." ORDER BY ventas DESC ".$limit;

        return $this->db->query($query)->result();
    }

    function getVentaSucursal($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $query = "SELECT p.producto_id, p.producto_codigo_interno, p.producto_nombre, u.nombre_unidad";
        
        $sqlLocal = $this->db->select('`l`.`int_local_id` AS `int_local_id`,`l`.`local_nombre` AS `local_nombre`');
        $sqlLocal = $this->db->from('(`local` `l`)');
        $sqlLocal = $this->db->where('`l`.`local_status` = 1');
        $sqlLocal = $this->db->get();
        $x=1;
        foreach ($sqlLocal->result() as $row)
        {
            $local = $row->int_local_id;
            $query .= ",
                (
                    SELECT 
                        IF(SUM(up.unidades * dv.cantidad) IS NULL, '0', SUM(up.unidades * dv.cantidad))
                    FROM venta v
                    INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                    INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                    WHERE v.venta_status='COMPLETADO' AND v.local_id='$local' AND dv.id_producto=p.producto_id
                ) AS cantVend$x,
                (
                    SELECT 
                        IF(SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion) IS NULL, 0, SUM((pa.cantidad * (SELECT unidades FROM unidades_has_producto WHERE producto_id=pa.id_producto AND orden=1)) + pa.fraccion))
                    FROM producto_almacen pa
                    WHERE pa.id_local='$local' AND pa.id_producto=p.producto_id
                ) AS stock$x,
                (
                    SELECT 
                        SUM(dv.precio * dv.cantidad)
                    FROM venta v
                    INNER JOIN detalle_venta dv ON v.venta_id=dv.id_venta 
                    INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                    WHERE v.venta_status='COMPLETADO' AND v.local_id='$local' AND dv.id_producto=p.producto_id
                ) AS total$x
            ";
            $x++;
        }

        $query .= "
            FROM 
                producto AS p
            INNER JOIN 
                detalle_venta dv ON p.producto_id=dv.id_producto
            INNER JOIN 
                venta v ON v.venta_id=dv.id_venta
            INNER JOIN
                unidades_has_producto up3 ON dv.id_producto=up3.producto_id AND dv.unidad_medida=up3.id_unidad
            INNER JOIN 
                unidades_has_producto up4 ON dv.id_producto=up4.producto_id 
                AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up4.id_unidad 
            INNER JOIN
                unidades u ON up4.id_unidad=u.id_unidad
            WHERE 
                p.producto_estado='1'
                AND v.venta_status='COMPLETADO'
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                AND v.id_moneda = ".$params['moneda_id']."
                $search
            GROUP BY
                dv.id_producto
            ORDER BY
                p.producto_id";

        return $this->db->query($query)->result_array();
    }

    function getVentaEmpleado($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = '';

        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id;
        $tipo = $params['tipo'];
        $orden = ($tipo=='1')? "cantidad":"total";
        $local_id = ($params['local_id']>0)? " AND v.local_id = ".$params['local_id'] : "";
        //Limitar top
        $limit = '';
        if(isset($params['limit'])){
            $limit = "LIMIT 0, ".$params['limit'];
        }
        $query = "
            SELECT
                p.producto_id AS producto_id, 
                v.id_vendedor AS id_vendedor, 
                u.nombre AS nombre,
                $tipo AS tipo,
                SUM(up.unidades * dv.cantidad) AS cantidad, 
                SUM(dv.precio * dv.cantidad) AS total,
                (
                    SELECT COUNT(*) FROM venta WHERE venta_status='ANULADO' AND id_vendedor=u.nUsuCodigo
                ) AS anulado
            FROM 
                detalle_venta dv
                INNER JOIN 
                    venta v ON v.venta_id=dv.id_venta
                INNER JOIN 
                    usuario u ON v.id_vendedor=u.nUsuCodigo
                INNER JOIN 
                    producto p ON p.producto_id = dv.id_producto
                INNER JOIN 
                    unidades_has_producto up ON dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad
                INNER JOIN 
                    unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
                    AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad 
                WHERE
                    v.venta_status='COMPLETADO'
                    AND v.id_moneda = ".$params['moneda_id']." 
                    $local_id
                    AND v.fecha >= '".$params['fecha_ini']."'
                    AND v.fecha <= '".$params['fecha_fin']."'
                    $search
            GROUP BY
                v.id_vendedor
            ORDER BY 
                $orden DESC $limit
        ";

        return $this->db->query($query)->result();
    }

    function getMargenUtilidad($params)
    {
        $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = $local_id = '';
        $local_id .= ($params['local_id']>0)? " AND v.local_id=".$params['local_id'] : "";
        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $search = $marca_id.$grupo_id.$familia_id.$linea_id.$producto_id.$local_id;

        $query = "SELECT l.local_nombre, p.producto_nombre, u.nombre_unidad, SUM(up.unidades * dv.cantidad) AS cantidad, 
            dv.detalle_costo_promedio, 
            SUM(IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe)) AS detalle_importe,
            AVG(IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1))) AS costoVentaSi,
            AVG(IF(v.tipo_impuesto='3', IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1)), (IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1)))*((dv.impuesto_porciento / 100) + 1))) AS costoVenta,
            AVG(dv.detalle_costo_ultimo) AS detalle_costo_ultimo, 
            AVG( dv.detalle_costo_ultimo / ((dv.impuesto_porciento / 100) + 1) ) AS costoCompraSi,
            SUM((up.unidades * dv.cantidad) * dv.detalle_costo_ultimo) AS costoTotal,
            SUM((up.unidades * dv.cantidad) * IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1))) AS subtotal,
            SUM( (IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1))) - (dv.detalle_costo_ultimo / ((dv.impuesto_porciento / 100) + 1)) ) AS utilidadXund,
            SUM( ((IF(v.tipo_impuesto='3', (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)), (IF(v.tipo_impuesto='2', dv.detalle_importe * ((dv.impuesto_porciento / 100) + 1), dv.detalle_importe) / (up.unidades * dv.cantidad)) / ((dv.impuesto_porciento / 100) + 1))) - (dv.detalle_costo_ultimo / ((dv.impuesto_porciento / 100) + 1))) * (up.unidades * dv.cantidad) ) AS utilidadTotal,
            dv.impuesto_porciento, v.tipo_impuesto
            FROM detalle_venta dv
                INNER JOIN venta v ON v.venta_id=dv.id_venta
                INNER JOIN `local` l ON v.local_id = l.int_local_id
                INNER JOIN producto p ON p.producto_id=dv.id_producto 
                INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id 
                AND dv.unidad_medida=up.id_unidad
                INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
                AND (
                    select id_unidad from unidades_has_producto 
                    where unidades_has_producto.producto_id = dv.id_producto
                    ORDER BY orden DESC LIMIT 1
                ) = up2.id_unidad 
                INNER JOIN unidades u ON u.id_unidad=up2.id_unidad
                INNER JOIN producto_costo_unitario pcu ON  p.producto_id = pcu.producto_id AND v.id_moneda = pcu.moneda_id AND activo=1 
            WHERE 
                v.venta_status='COMPLETADO'
                AND v.id_moneda = ".$params['moneda_id']."
                AND v.fecha >= '".$params['fecha_ini']."'
                AND v.fecha <= '".$params['fecha_fin']."'
                $search";

        $query .= " GROUP BY p.producto_id";

        return $this->db->query($query)->result();
    }

    function getStockVentas($params)
    {
        $this->db->select("p.producto_id, p.producto_codigo_interno, f.nombre_familia, p.producto_nombre, m.nombre_marca, l.nombre_linea");
        $this->db->from("producto p");
        $this->db->join("familia f", "p.producto_familia = f.id_familia", "left");
        $this->db->join("marcas m", "p.producto_marca = m.id_marca", "left");
        $this->db->join("lineas l", "p.producto_linea = l.id_linea", "left");
        $this->db->where("p.producto_estado", "1");

        if($params['marca_id'] > 0){
            $this->db->where("p.producto_marca", $params['marca_id']);
        }

        if($params['grupo_id'] > 0){
            $this->db->where("p.produto_grupo", $params['grupo_id']);
        }

        if($params['familia_id'] > 0){
            $this->db->where("p.producto_familia", $params['familia_id']);
        }

        if($params['linea_id'] > 0){
            $this->db->where("p.producto_linea", $params['linea_id']);
        }

        if($params['producto_id'] != ''){
            $this->db->where("p.producto_id IN(".implode(",", $params['producto_id']).")");
        }
        $this->db->order_by("p.producto_nombre");
        $datos = $this->db->get()->result_array();

        $x=0;
        foreach ($datos as $dato) {
            $datos[$x]['nombre_unidad'] = $this->unidades_model->get_um_min_by_producto($dato['producto_id']);

            if($params['tipo']=='1'){ //cantidad
                $this->db->select("IF(SUM(up.unidades * dv.cantidad) IS NULL, '0', SUM(up.unidades * dv.cantidad)) AS total, DATE(v.fecha) AS fecha, l.local_nombre, v.local_id, m.simbolo");
            }else{ //importe
                $this->db->select("IF(SUM(dv.precio * dv.cantidad) IS NULL, '0', SUM(dv.precio * dv.cantidad)) AS total, DATE(v.fecha) AS fecha, l.local_nombre, v.local_id, m.simbolo");
            }

            $this->db->from("venta v");
            $this->db->join("detalle_venta dv", "v.venta_id=dv.id_venta");
            $this->db->join("unidades_has_producto up", "dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad");
            $this->db->join("local l", "v.local_id = l.int_local_id");
            $this->db->join("moneda m", "v.id_moneda = m.id_moneda");
            $this->db->where("v.venta_status = 'COMPLETADO' AND dv.id_producto = ".$dato['producto_id']." AND v.local_id IN(".implode(",", $params['local_id']).")");
            switch ($params['tipo_periodo']){
                case '1': //dia
                    $this->db->where("v.fecha >= '".$params['rangos'][0]."' AND v.fecha <= '".$params['rangos'][1]."'");
                    break;
                case '2': //mes
                    $this->db->where("v.fecha >= '".$params['rangos'][0]."' AND v.fecha <= '".$params['rangos'][1]."'");
                    break;
                case '3': //anio
                    $this->db->where("YEAR(v.fecha) IN(".implode(",", $params['rangos']).")");
                    break;
            }
            $this->db->group_by("v.local_id, DATE(v.fecha)");
            $datosVentas = $this->db->get()->result_array();

            $detalle = array();
            $i=0;
            foreach($datosVentas as $datosVenta){
                switch ($params['tipo_periodo']){
                    case '1': //dia
                        $detalle['fecha'][$datosVenta['local_id'].'_'.$datosVenta['fecha']] = $datosVenta['total'];
                        break;
                    case '2': //mes
                        $parte = explode("-", $datosVenta['fecha']);
                        $detalle['fecha'][$datosVenta['local_id'].'_'.$parte[0].'-'.$parte[1]] = $datosVenta['total'];
                        break;
                    case '3': //año
                        $parte = explode("-", $datosVenta['fecha']);
                        $detalle['fecha'][$datosVenta['local_id'].'_'.$parte[0]] = $datosVenta['total'];
                        break;
                }
                
                if(!isset($detalle['local'][$datosVenta['local_id']])){
                    $detalle['local'][$datosVenta['local_id']] = $datosVenta['total'];    
                }else{
                    $detalle['local'][$datosVenta['local_id']] += $datosVenta['total'];
                }
                $detalle['moneda'] = $datosVenta['simbolo'];
                $i++;
            }

            //Para el stock
            if($params['tipo']=='1'){
                foreach($params['local_id'] as $locale){
                    $this->db->select('cantidad, fraccion');
                    $this->db->from("producto_almacen");
                    $this->db->where("id_local", $locale);
                    $this->db->where("id_producto", $dato['producto_id']);
                    $datoStock = $this->db->get()->row_array();
                    $detalle['stock'][$locale.'_'.$dato['producto_id']] = $this->unidades_model->convert_minimo_um($dato['producto_id'], $datoStock['cantidad'], $datoStock['fraccion']);
                }
            }
            $datos[$x]['detalle'] = $detalle;
            $x++;
        }
        return $datos;
    }

    function getHojaColecta($params, $count = false)
    {
        $local_id = $marca_id = $grupo_id = $familia_id = $linea_id = $producto_id = $operador_id = $usuario_id = '';
        $usu = $this->session->userdata('nUsuCodigo');
        $local_id .= ($params['local_id']>0)? " AND v.local_id=".$params['local_id'] : "";
        $marca_id .= ($params['marca_id']>0)? " AND p.producto_marca=".$params['marca_id'] : "";
        $grupo_id .= ($params['grupo_id']>0)? " AND p.produto_grupo=".$params['grupo_id'] : "";
        $familia_id .= ($params['familia_id']>0)? " AND p.producto_familia=".$params['familia_id'] : "";
        $linea_id .= ($params['linea_id']>0)? " AND p.producto_linea=".$params['linea_id'] : "";
        $operador_id .= ($params['operador_id']>0)? " AND r.rec_ope=".$params['operador_id'] : "";
        $producto_id .= ($params['producto_id']!='')? " AND p.producto_id IN(".implode(",", $params['producto_id']).")" : "";
        $usuario_id .= ($params['usuario_id']>0)? " AND v.id_vendedor=".$params['usuario_id'] : "";
        $search = $local_id.$marca_id.$grupo_id.$familia_id.$linea_id.$operador_id.$producto_id.$usuario_id;
        if($count == false){
            $this->db->select("v.venta_id, c.razon_social, v.serie, v.numero, p.producto_nombre, dv.cantidad, dv.precio, dv.detalle_importe, l.local_nombre, d.abr_doc, m.simbolo, v.fecha, v.nota, dt.valor, u.username AS nombre, IF(v.condicion_pago=2,'CREDITO', 'CONTADO') AS condicion, v.condicion_pago, (cr.dec_credito_montocuota - cr.dec_credito_montodebito) AS monto_restante, v.total, dv.id_producto, 
                (
                    SELECT SUM(up.unidades * dv2.cantidad)
                    FROM detalle_venta AS dv2
                    INNER JOIN venta v2 ON v2.venta_id=dv2.id_venta
                    INNER JOIN producto p2 ON dv2.id_producto=p2.producto_id
                    INNER JOIN unidades_has_producto up ON dv2.id_producto=up.producto_id AND dv2.unidad_medida=up.id_unidad
                    INNER JOIN unidades_has_producto up2 ON dv2.id_producto=up2.producto_id 
                    AND (select id_unidad from unidades_has_producto where unidades_has_producto.producto_id = dv2.id_producto  ORDER BY orden DESC LIMIT 1) = up2.id_unidad
                    INNER JOIN unidades u ON up2.id_unidad=u.id_unidad
                    WHERE dv2.id_detalle = dv.id_detalle AND v2.venta_status='COMPLETADO' AND v2.fecha >= '".$params['fecha_ini']."' AND v2.fecha <= '".$params['fecha_fin']."'
                ) AS cantidad2
                ");
        }else{
            $this->db->select("dv.detalle_importe");
        }        
        $this->db->from('detalle_venta dv');
        $this->db->join('venta v', 'v.venta_id=dv.id_venta');
        $this->db->join('recarga r', 'v.venta_id = r.id_venta', 'left');
        $this->db->join('diccionario_termino dt', 'r.rec_ope = dt.id', 'left');
        $this->db->join('documentos d', 'v.id_documento = d.id_doc');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->join('producto p', 'dv.id_producto=p.producto_id');
        $this->db->join('cliente c', 'v.id_cliente = c.id_cliente');
        $this->db->join('`local` l', 'v.local_id = l.int_local_id');
        $this->db->join('usuario_almacen ua', "v.local_id = ua.local_id AND ua.usuario_id = $usu");
        $this->db->join('usuario u', 'v.id_vendedor = u.nUsuCodigo');
        $this->db->join('credito cr', 'v.venta_id = cr.id_venta', 'left');
        $this->db->join('credito_cuotas cru', 'v.venta_id = cru.id_venta', 'left');
        $this->db->where("v.venta_status='COMPLETADO' AND v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."' $search");
        if($count == false){
            if($params['estado_pago']==1){ //deben
                $this->db->where('ispagado = 0');
            }elseif($params['estado_pago']==2){ //Cancelado
                $this->db->where('(ispagado = 1 OR ispagado IS NULL)');
                $this->db->where("((cr.dec_credito_montocuota - cr.dec_credito_montodebito) IS NULL OR (cr.dec_credito_montocuota - cr.dec_credito_montodebito)=0)");
            }
            $this->db->group_by("v.venta_id, dv.id_producto");
            $this->db->order_by('v.local_id, v.venta_id DESC');
        }else{
            $this->db->group_by("dv.id_detalle");
        }
        return $this->db->get()->result();
    }

    function getRecargaDia($params)
    {
        $this->db->select("v.venta_id, v.fecha, c.razon_social, c.nota, r.rec_nro, r.rec_trans, v.total, cru.ultimo_pago AS fecha_abono, cr.dec_credito_montodebito AS monto_abono, l.local_nombre, IF(v.condicion_pago=2,'CREDITO', 'CONTADO') AS condicion, v.condicion_pago, cru.ispagado, dt.valor, (cr.dec_credito_montocuota - cr.dec_credito_montodebito) AS monto_restante, u.username AS nombre");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('cliente c', 'c.id_cliente = v.id_cliente');
        $this->db->join('local l', 'v.local_id = l.int_local_id');
        $this->db->join('recarga r', 'v.venta_id = r.id_venta');
        $this->db->join('diccionario_termino dt', 'r.rec_ope = dt.id');
        $this->db->join('usuario u', 'v.id_vendedor = u.nUsuCodigo');
        $this->db->join('credito cr', 'v.venta_id = cr.id_venta', 'left');
        $this->db->join('credito_cuotas cru', 'v.venta_id = cru.id_venta', 'left');
        $this->db->where("v.venta_status='COMPLETADO'");
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            $this->db->where("v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'");
        }
        if($params['estado_pago']==1){ //deben
            $this->db->where('ispagado = 0');
        }elseif($params['estado_pago']==2){ //Cancelado
            $this->db->where('(ispagado = 1 OR ispagado IS NULL)');
        }
        if($params['poblado_id']>0){
            $this->db->where('rec_pob = ', $params['poblado_id']);
        }
        if($params['usuario_id']>0){
            $this->db->where('v.id_vendedor = ', $params['usuario_id']);   
        }
        return $this->db->get()->result();
    }

    function getRecargaCobranza($params)
    {
        $this->db->select("v.venta_id, v.fecha, c.razon_social, c.nota, r.rec_nro, r.rec_trans, v.total, cru.ultimo_pago AS fecha_abono, cr.dec_credito_montodebito AS monto_abono, l.local_nombre, IF(v.condicion_pago=2,'CREDITO', 'CONTADO') AS condicion, v.condicion_pago, cru.ispagado, dt.valor, (cr.dec_credito_montocuota - cr.dec_credito_montodebito) AS monto_restante, u.username AS nombre");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('cliente c', 'c.id_cliente = v.id_cliente');
        $this->db->join('local l', 'v.local_id = l.int_local_id');
        $this->db->join('recarga r', 'v.venta_id = r.id_venta', 'left');
        $this->db->join('diccionario_termino dt', 'r.rec_ope = dt.id', 'left');
        $this->db->join('credito cr', 'v.venta_id = cr.id_venta');
        $this->db->join('credito_cuotas cru', 'v.venta_id = cru.id_venta');
        $this->db->join('credito_cuotas_abono cca', 'cru.id_credito_cuota = cca.credito_cuota_id','left');
        $this->db->join('usuario u', 'cca.usuario_pago = u.nUsuCodigo', 'left');
        $this->db->where("v.venta_status='COMPLETADO'");
        $this->db->where('v.condicion_pago = 2');
        $this->db->where('v.fecha <> cru.ultimo_pago');
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            $this->db->where("DATE(cru.ultimo_pago) >= '".$params['fecha_ini']."' AND DATE(cru.ultimo_pago) <= '".$params['fecha_fin']."'");
        }
        if($params['estado_pago']==1){ //deben
            $this->db->where('ispagado = 0');
        }elseif($params['estado_pago']==2){ //Cancelado
            $this->db->where('(ispagado = 1 OR ispagado IS NULL)');
        }
        if($params['poblado_id']>0){
            $this->db->where('rec_pob = ', $params['poblado_id']);
        }
        if($params['usuario_id']>0){
            $this->db->where('u.nUsuCodigo = ', $params['usuario_id']);   
        }
        $this->db->group_by("v.venta_id");
        return $this->db->get()->result();
    }

    function getRecargaCuentasC($params)
    {
        $this->db->select("v.venta_id, v.fecha, c.razon_social, c.nota, r.rec_nro, r.rec_trans, v.total, cru.ultimo_pago AS fecha_abono, cr.dec_credito_montodebito AS monto_abono, l.local_nombre, IF(v.condicion_pago=2,'CREDITO', 'CONTADO') AS condicion, v.condicion_pago, cru.ispagado, dt.valor, (cr.dec_credito_montocuota - cr.dec_credito_montodebito) AS monto_restante, u.username AS nombre");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('cliente c', 'c.id_cliente = v.id_cliente');
        $this->db->join('local l', 'v.local_id = l.int_local_id');
        $this->db->join('recarga r', 'v.venta_id = r.id_venta', 'left');
        $this->db->join('diccionario_termino dt', 'r.rec_ope = dt.id', 'left');
        $this->db->join('usuario u', 'v.id_vendedor = u.nUsuCodigo');
        $this->db->join('credito cr', 'v.venta_id = cr.id_venta');
        $this->db->join('credito_cuotas cru', 'v.venta_id = cru.id_venta');
        $this->db->where("v.venta_status='COMPLETADO'");
        $this->db->where('ispagado = 0');
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        /*if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            $this->db->where("v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'");
        }*/
        if($params['poblado_id']>0){
            $this->db->where('rec_pob = ', $params['poblado_id']);
        }
        if($params['usuario_id']>0){
            $this->db->where('v.id_vendedor = ', $params['usuario_id']);   
        }
        $this->db->group_by("v.venta_id");
        return $this->db->get()->result();
    }
    function getSumMedioPago($params, $condicion_pago)
    {
        $usu = $this->session->userdata('nUsuCodigo');
        $this->db->select('cm.medio_pago, SUM(cm.saldo) as saldo');
        $this->db->from('venta v');
        //$this->db->from('detalle_venta dv');
        //$this->db->join('venta v', 'v.venta_id=dv.id_venta');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        //$this->db->join('producto p', 'dv.id_producto=p.producto_id');
        $this->db->join('usuario_almacen ua', "v.local_id = ua.local_id AND ua.usuario_id = $usu");
        $this->db->join('recarga r', 'v.venta_id = r.id_venta', 'left');
        $this->db->join('caja_movimiento cm', 'v.venta_id = cm.ref_id');
        $this->db->where("v.venta_status='COMPLETADO' AND v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'");
        $this->db->where("v.condicion_pago=", $condicion_pago);
        $this->db->where("cm.operacion=", 'VENTA');
        if($params['local_id']>0)
            $this->db->where("v.local_id=", $params['local_id']);
        /*if($params['marca_id']>0)
            $this->db->where("p.producto_marca=", $params['marca_id']);
        if($params['grupo_id']>0)
            $this->db->where("p.produto_grupo=", $params['grupo_id']);
        if($params['familia_id']>0)
            $this->db->where("p.producto_familia=", $params['familia_id']);
        if(($params['linea_id']>0))
            $this->db->where("p.producto_linea=", $params['linea_id']);*/
        if($params['operador_id']>0)
            $this->db->where("r.rec_ope=", $params['operador_id']);
        /*if($params['producto_id']!='')
            $this->db->where("p.producto_id IN(".implode(",", $params['producto_id']).")");*/
        if($params['usuario_id']>0)
            $this->db->where("v.id_vendedor=", $params['usuario_id']);
        $this->db->group_by("v.condicion_pago, cm.medio_pago");
        return $this->db->get()->result();
    }

    function getUtilidadProducto($params)
    {
        $where = "v.venta_status='COMPLETADO'";
        if($params['local_id']>0){
            $where .= " AND v.local_id = ".$params['local_id'];
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            if(!empty($where)){
                $where .= " AND ";
            }
            $where .= "v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'";
        }

        $query = "SELECT v.venta_id, DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha, pr.proveedor_nombre, p.producto_nombre, u.nombre_unidad, SUM(up.unidades * dv.cantidad) AS cantidad, dv.detalle_costo_promedio, dv.detalle_importe, l.local_nombre, dv.detalle_costo_ultimo, dv.impuesto_porciento, v.tipo_impuesto
            FROM detalle_venta dv
            INNER JOIN venta v ON v.venta_id=dv.id_venta 
            INNER JOIN producto p ON p.producto_id=dv.id_producto 
            INNER JOIN `local` l ON v.local_id = l.int_local_id
            LEFT JOIN proveedor pr ON p.producto_proveedor=pr.id_proveedor
            INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id 
            AND dv.unidad_medida=up.id_unidad
            INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
            AND (
                select id_unidad from unidades_has_producto 
                where unidades_has_producto.producto_id = dv.id_producto
                ORDER BY orden DESC LIMIT 1
            ) = up2.id_unidad 
            INNER JOIN unidades u ON u.id_unidad=up2.id_unidad
            INNER JOIN producto_costo_unitario pcu ON  p.producto_id = pcu.producto_id AND v.id_moneda = pcu.moneda_id AND activo=1 ";
        if(!empty($where)){
            $query .= " WHERE ". $where;
        }
        $query .= " GROUP BY v.venta_id, dv.id_detalle ORDER BY v.venta_id";
        return $this->db->query($query)->result();
    }

    function getGastosDia($params)
    {
        $where = "v.venta_status='COMPLETADO' AND ";
        if($params['local_id']>0){
            $where .= "v.local_id = ".$params['local_id'];
        }
        if(!empty($params['fecha_ini']) && !empty($params['fecha_fin'])){
            if(!empty($where)){
                $where .= " AND ";
            }
            $where .= "v.fecha >= '".$params['fecha_ini']."' AND v.fecha <= '".$params['fecha_fin']."'";
        }

        $query = "SELECT v.venta_id, DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha, pr.proveedor_nombre, p.producto_nombre, u.nombre_unidad, SUM(up.unidades * dv.cantidad) AS cantidad, dv.detalle_costo_promedio, dv.detalle_importe, l.local_nombre, dv.detalle_costo_ultimo, dv.impuesto_porciento, v.tipo_impuesto
            FROM detalle_venta dv
            INNER JOIN venta v ON v.venta_id=dv.id_venta 
            INNER JOIN producto p ON p.producto_id=dv.id_producto 
            INNER JOIN `local` l ON v.local_id = l.int_local_id
            LEFT JOIN proveedor pr ON p.producto_proveedor=pr.id_proveedor
            INNER JOIN unidades_has_producto up ON dv.id_producto=up.producto_id 
            AND dv.unidad_medida=up.id_unidad
            INNER JOIN unidades_has_producto up2 ON dv.id_producto=up2.producto_id 
            AND (
                select id_unidad from unidades_has_producto 
                where unidades_has_producto.producto_id = dv.id_producto
                ORDER BY orden DESC LIMIT 1
            ) = up2.id_unidad 
            INNER JOIN unidades u ON u.id_unidad=up2.id_unidad";
        if(!empty($where)){
            $query .= " WHERE ". $where;
        }
        $query .= " GROUP BY v.venta_id, dv.id_detalle ORDER BY v.venta_id";
        return $this->db->query($query)->result();
    }

    function getEstadoResultado($params)
    {
        // Ventas
        $this->db->select("SUM(dv.detalle_importe) / ((dv.impuesto_porciento / 100) + 1) AS detalle_importe, SUM(dv.detalle_costo_ultimo / ((dv.impuesto_porciento / 100) + 1) * dv.cantidad) AS costo_venta, m.simbolo");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->where("v.venta_status='COMPLETADO'");
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        if($params['moneda_id']>0){
            $this->db->where('v.id_moneda = '.$params['moneda_id']);
        }
        if($params['mes'] != '' && $params['year'] != ''){
            $this->db->where('YEAR(v.fecha) = '.$params['year'].' AND MONTH(v.fecha) = '.$params['mes']);
        }
        $ventas = $this->db->get()->row();

        //Grupo de gasto
        $this->db->select('id_grupo_gastos, nom_grupo_gastos');
        $this->db->from('grupo_gastos');
        $grupos = $this->db->get()->result_array();

        $x=0;
        foreach ($grupos as $grupo){
            //Tipo de gasto
            $this->db->select('id_tipos_gasto, nombre_tipos_gasto');
            $this->db->from('tipos_gasto');
            $this->db->where('status_tipos_gasto', '1');
            $this->db->where('id_grupo_gastos', $grupo['id_grupo_gastos']);
            $this->db->where("nombre_tipos_gasto != 'PRESTAMO BANCARIO'");
            $tipo_gastos = $this->db->get()->result_array();

            $a = 0;
            $totSubtotal = 0;
            foreach ($tipo_gastos as $tipo_gasto) {
                //Sumas los gastos deacuerdo al tipo y grupo
                $this->db->select('SUM(subtotal) AS subtotal');
                $this->db->from('gastos');
                $this->db->where('status_gastos', '0'); //Gasto confirmado
                $this->db->where('tipo_gasto', $tipo_gasto['id_tipos_gasto']);
                if($params['local_id']>0){
                    $this->db->where('local_id = '.$params['local_id']);
                }
                if($params['moneda_id']>0){
                    $this->db->where('id_moneda = '.$params['moneda_id']);
                }
                if($params['mes'] != '' && $params['year'] != ''){
                    $this->db->where('YEAR(fecha) = '.$params['year'].' AND MONTH(fecha) = '.$params['mes']);
                }
                $suma = $this->db->get()->row_array();

                $tipo_gastos[$a]['suma'] = $suma['subtotal'];

                //Prestamo bancario
                if($tipo_gasto['nombre_tipos_gasto']=='INTERES' || $tipo_gasto['nombre_tipos_gasto']=='COMISION'){
                    if($tipo_gasto['nombre_tipos_gasto'] == 'INTERES'){
                        $this->db->select('SUM(interes) AS subtotal');
                    }else{
                        $this->db->select('SUM(comision) AS subtotal');
                    }
                    $this->db->from('ingreso i');
                    $this->db->join('ingreso_credito ic', 'i.id_ingreso = ic.ingreso_id');
                    $this->db->where("i.tipo_ingreso='GASTO' AND tipo_documento='CRONOGRAMA DE PAGOS'");
                    if($params['local_id']>0){
                        $this->db->where('local_id = '.$params['local_id']);
                    }
                    if($params['moneda_id']>0){
                        $this->db->where('id_moneda = '.$params['moneda_id']);
                    }
                    if($params['mes'] != '' && $params['year'] != ''){
                        $this->db->where('YEAR(fecha_emision) = '.$params['year'].' AND MONTH(fecha_emision) = '.$params['mes']);
                    }
                    $suma = $this->db->get()->row_array();
                    $tipo_gastos[$a]['suma'] += $suma['subtotal'];
                }

                $totSubtotal += $tipo_gastos[$a]['suma'];
                $a++;
            }
            $grupos[$x]['nom'] = $tipo_gastos;
            $grupos[$x]['suma'] = $totSubtotal;
            $x++;
        }

        $datos['simbolo'] = $ventas->simbolo;
        $datos['ventas'] = $ventas->detalle_importe;
        $datos['costo'] = $ventas->costo_venta;
        $datos['margen_bruto'] = $datos['ventas'] - $datos['costo'];
        $datos['gastos'] = $grupos;
        //utilidad operativa = margen bruto - gasto de venta - gasto administrativo - planilla - gastos de servicio
        $datos['utilidad'] = $datos['margen_bruto'] - $grupos[0]['suma'] - $grupos[1]['suma'] - $grupos[3]['suma'] - $grupos[5]['suma'];
        //UTILIDAD ANTES DE IMPUESTOS = utilidad operativa - gasto financiero
        $datos['utilidad_si'] = $datos['utilidad'] - $grupos[2]['suma'];
        //IMPUESTO A LA RENTA  = UTILIDAD ANTES DE IMPUESTOS * 0.3
        $datos['impuesto'] = $datos['utilidad_si'] * 0.3;
        //UTILIDAD NETA = UTILIDAD ANTES DE IMPUESTOS - IMPUESTO A LA RENTA
        $datos['utilidad_neta'] = $datos['utilidad_si'] - $datos['impuesto'];
        return $datos;
    }

    function getkardexValorizado($where)
    {
        $this->db->select("kardex.id, kardex.fecha, kardex.tipo, kardex.serie, kardex.numero, kardex.operacion, usuario.username, kardex.ref_val, kardex.io, kardex.cantidad, kardex.cantidad_saldo, producto.producto_cualidad, kardex.costo, moneda.simbolo")->from('kardex');
        $this->db->join('usuario', 'usuario.nUsuCodigo = kardex.usuario_id');
        $this->db->join('producto', 'kardex.producto_id = producto.producto_id');
        $this->db->join('moneda', 'kardex.moneda_id = moneda.id_moneda')
            ->where('kardex.producto_id', $where['producto_id'])
            ->where('local_id', $where['local_id'])
            ->order_by('id');

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('date(fecha) >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min']);
            $this->db->where('date(fecha) <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day);
        }
        elseif (isset($where['fecha_ini']) && isset($where['fecha_fin'])){
            $this->db->where('date(fecha) >=', $where['fecha_ini'] . ' ');
            $this->db->where('date(fecha) <=', $where['fecha_fin'] . ' ');
        }

        return $this->db->get()->result();
    }

    function getCreditoFiscal($params)
    {
        $this->db->select("v.venta_id, DATE(v.fecha) AS fecha, DATE(v.fecha) AS created_at, d.abr_doc, c.razon_social, cp.nombre_condiciones, l.local_nombre, v.serie, v.numero, SUM(up.unidades * dv.cantidad) AS cantidad, impuesto_porciento, v.tipo_impuesto, SUM(dv.detalle_importe) AS detalle_importe, m.simbolo");
        $this->db->from('venta v');
        $this->db->join('detalle_venta dv', 'v.venta_id = dv.id_venta');
        $this->db->join('moneda m', 'v.id_moneda = m.id_moneda');
        $this->db->join('cliente c', 'v.id_cliente = c.id_cliente');
        $this->db->join('condiciones_pago cp', 'v.condicion_pago = cp.id_condiciones');
        $this->db->join('documentos d', 'v.id_documento = d.id_doc');
        $this->db->join('local l', 'v.local_id = l.int_local_id');
        $this->db->join('unidades_has_producto up', 'dv.id_producto=up.producto_id AND dv.unidad_medida=up.id_unidad');
        $this->db->where("v.venta_status='COMPLETADO'");
        $this->db->where("DATE(v.fecha) >= '".$params['fecha_ini']."' AND DATE(v.fecha) <= '".$params['fecha_fin']."'");
        $this->db->group_by('v.venta_id');
        if($params['local_id']>0){
            $this->db->where('v.local_id = '.$params['local_id']);
        }
        if($params['moneda_id']>0){
            $this->db->where('v.id_moneda = '.$params['moneda_id']);
        }
        if($params['doc_id']>0){
            $this->db->where('v.id_documento = '.$params['doc_id']);
        }
        return $this->db->get()->result();
    }
}