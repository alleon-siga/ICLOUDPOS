<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class impresion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function create_xml($documents)
    {
        $xml = new SimpleXMLElement('<DocumentElement/>');

        foreach ($documents as $doc) {
            $page = $xml->addChild('documento');

            $page->addChild('empresa_nombre', valueOption('EMPRESA_NOMBRE', ''));
            $page->addChild('empresa_direccion', valueOption('EMPRESA_DIRECCION', ''));
            $page->addChild('empresa_correo', valueOption('EMPRESA_CORREO', ''));
            $page->addChild('empresa_telefono', valueOption('EMPRESA_TELEFONO', ''));

            $page->addChild('cliente_nombre', isset($doc->cliente_nombre) ? $doc->cliente_nombre : '');
            $page->addChild('cliente_tipo', isset($doc->cliente_tipo) ? $doc->cliente_tipo : '');
            $page->addChild('cliente_identificacion', isset($doc->cliente_identificacion) ? $doc->cliente_identificacion : '');
            $page->addChild('cliente_direccion', isset($doc->cliente_direccion) ? $doc->cliente_direccion : '');
            $page->addChild('cliente_grupo', isset($doc->cliente_grupo) ? $doc->cliente_grupo : '');

            $page->addChild('proveedor_nombre', isset($doc->proveedor_nombre) ? $doc->proveedor_nombre : '');
            $page->addChild('proveedor_ruc', isset($doc->proveedor_ruc) ? $doc->proveedor_ruc : '');
            $page->addChild('proveedor_direccion', isset($doc->proveedor_direccion) ? $doc->proveedor_direccion : '');
            $page->addChild('proveedor_telefono', isset($doc->proveedor_telefono) ? $doc->proveedor_telefono : '');
            $page->addChild('proveedor_correo', isset($doc->proveedor_correo) ? $doc->proveedor_correo : '');

            $page->addChild('vendedor_nombre', isset($doc->vendedor_nombre) ? $doc->vendedor_nombre : '');

            $page->addChild('local', isset($doc->local) ? $doc->local : '');
            $page->addChild('documento_nombre', isset($doc->documento_nombre) ? $doc->documento_nombre : '');
            $page->addChild('numero', isset($doc->numero) ? $doc->numero : '');
            $page->addChild('fecha_emision', isset($doc->fecha_emision) ? $doc->fecha_emision : '');
            $page->addChild('tipo_pago', isset($doc->tipo_pago) ? $doc->tipo_pago : '');
            $page->addChild('estado', isset($doc->estado) ? $doc->estado : '');
            $page->addChild('moneda', isset($doc->moneda) ? $doc->moneda : '');
            $page->addChild('moneda_simbolo', isset($doc->moneda_simbolo) ? $doc->moneda_simbolo : '');
            $page->addChild('importe', isset($doc->importe) ? $doc->moneda_simbolo . ' ' . $doc->importe : '');
            $page->addChild('importe_deuda', isset($doc->importe_deuda) ? $doc->moneda_simbolo . ' ' . $doc->importe_deuda : '');
            $page->addChild('subtotal', isset($doc->subtotal) ? $doc->moneda_simbolo . ' ' . $doc->subtotal : '0.00');
            $page->addChild('impuesto', isset($doc->impuesto) ? $doc->moneda_simbolo . ' ' . $doc->impuesto : '');
            $page->addChild('descuento', isset($doc->descuento) ? $doc->moneda_simbolo . ' ' . $doc->descuento : '');
            $page->addChild('vuelto', isset($doc->vuelto) ? $doc->moneda_simbolo . ' ' . $doc->vuelto : '');
            $page->addChild('pagado', isset($doc->pagado) ? $doc->moneda_simbolo . ' ' . $doc->pagado : '');
            $page->addChild('importe_letra', isset($doc->importe_letra) ? $doc->importe_letra : '');
            $page->addChild('inicial', isset($doc->inicial) ? $doc->moneda_simbolo . ' ' . $doc->inicial : '');


            $productos = $page->addChild('productos');
            foreach ($doc->productos as $prod) {
                $producto = $productos->addChild('producto');
                $producto->addAttribute('id', $prod->id);
                $producto->addChild('codigo', isset($prod->codigo) ? $prod->codigo : '');
                $producto->addChild('nombre', isset($prod->nombre) ? $prod->nombre : '');
                $producto->addChild('presentacion', isset($prod->presentacion) ? $prod->presentacion : '');
                $producto->addChild('unidad', isset($prod->unidad) ? $prod->unidad : '');
                $producto->addChild('unidad_abr', isset($prod->unidad_abr) ? $prod->unidad_abr : '');
                $producto->addChild('cantidad', isset($prod->cantidad) ? $prod->cantidad : '');
                $producto->addChild('precio', isset($prod->precio) ? $doc->moneda_simbolo . ' ' . $prod->precio : '');
                $producto->addChild('importe', isset($prod->importe) ? $doc->moneda_simbolo . ' ' . $prod->importe : '');
                $producto->addChild('precio_venta', isset($prod->precio_venta) ? $doc->moneda_simbolo . ' ' . $prod->precio_venta : '');
            }

            if (isset($doc->cuotas)) {
                $cuotas = $page->addChild('cuotas');
                foreach ($doc->cuotas as $c) {
                    $cuota = $cuotas->addChild('cuota');
                    $cuota->addAttribute('id', $c->id);
                    $cuota->addChild('letra', isset($c->letra) ? $prod->letra : '');
                    $cuota->addChild('fecha', isset($c->fecha) ? $prod->fecha : '');
                    $cuota->addChild('monto', isset($c->monto) ? $doc->moneda_simbolo . '' . $prod->monto : '');
                }
            }

        }

        return $xml->asXML();

    }


    function getVenta($id)
    {
        $query = "
            SELECT 
                c.razon_social AS cliente_nombre,
                IF(c.tipo_cliente = 0,
                    'Natural',
                    'Juridico') AS cliente_tipo,
                c.identificacion AS cliente_identificacion,
                c.direccion AS cliente_direccion,
                gc.nombre_grupos_cliente AS cliente_grupo,
                u.nombre AS vendedor_nombre,
                l.local_nombre AS local,
                d.des_doc AS documento_nombre,
                CONCAT(v.serie, ' - ', LPAD(v.numero, 6, '0')) AS numero,
                DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha_emision,
                cp.nombre_condiciones AS tipo_pago,
                v.venta_status AS estado,
                m.nombre AS moneda,
                m.simbolo AS moneda_simbolo,
                FORMAT(v.total, 2) AS importe,
                IF(v.condicion_pago = 2,
                    FORMAT(v.total - IFNULL(v.inicial, 0),
                        2),
                    FORMAT(0, 2)) AS importe_deuda,
                FORMAT(v.subtotal, 2) AS subtotal,
                FORMAT(v.total_impuesto, 2) AS impuesto,
                FORMAT(v.vuelto, 2) AS vuelto,
                FORMAT(v.pagado, 2) AS pagado,
                FORMAT(IFNULL(v.inicial, 0), 2) AS inicial
            FROM
                venta AS v
                    JOIN
                cliente AS c ON c.id_cliente = v.id_cliente
                    JOIN
                grupos_cliente AS gc ON gc.id_grupos_cliente = c.grupo_id
                    JOIN
                usuario AS u ON u.nUsuCodigo = v.id_vendedor
                    JOIN
                local AS l ON l.int_local_id = v.local_id
                    JOIN
                documentos AS d ON d.id_doc = v.id_documento
                    JOIN
                condiciones_pago AS cp ON cp.id_condiciones = v.condicion_pago
                    JOIN
                moneda AS m ON m.id_moneda = v.id_moneda
            WHERE
                v.venta_id = " . $id . "
        ";

        $venta = $this->db->query($query)->result();


        foreach ($venta as $v) {

            $v->importe_letra = numtoletras($v->importe);

            $query = "
            SELECT 
                dv.id_producto AS id,
                IF((SELECT 
                            COUNT(*)
                        FROM
                            configuraciones
                        WHERE
                            config_key = 'CODIGO_DEFAULT'
                                AND config_value = 'AUTO'
                        LIMIT 1) > 0,
                    LPAD(dv.id_producto, 4, '0'),
                    p.producto_codigo_interno) AS codigo,
                p.producto_nombre AS nombre,
                u.nombre_unidad AS unidad,
                u.abreviatura AS unidad_abr,
                FORMAT(dv.cantidad, 0) AS cantidad,
                dv.precio AS precio,
                dv.detalle_importe AS importe,
                dv.precio_venta AS precio_venta
            FROM
                detalle_venta AS dv
                    JOIN
                producto AS p ON p.producto_id = dv.id_producto
                    JOIN
                unidades AS u ON u.id_unidad = dv.unidad_medida
            WHERE
                dv.id_venta = " . $id . "
        ";

            $v->productos = $this->db->query($query)->result();
        }

        return $venta;
    }
}
