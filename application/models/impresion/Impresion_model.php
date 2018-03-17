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
            $page->addChild('importe', isset($doc->importe) ? $doc->importe : '');
            $page->addChild('importe_deuda', isset($doc->inicial) ? $doc->inicial : '');
            $page->addChild('subtotal', isset($doc->subtotal) ? $doc->subtotal : '');
            $page->addChild('impuesto', isset($doc->impuesto) ? $doc->impuesto : '');
            $page->addChild('descuento', isset($doc->descuento) ? $doc->descuento : '');
            $page->addChild('vuelto', isset($doc->vuelto) ? $doc->vuelto : '');
            $page->addChild('pagado', isset($doc->pagado) ? $doc->pagado : '');
            $page->addChild('importe_letra', isset($doc->documento_nombre) ? $doc->importe_letra : '');
            $page->addChild('inicial', isset($doc->inicial) ? $doc->inicial : '');


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
                $producto->addChild('precio', isset($prod->precio) ? $prod->precio : '');
                $producto->addChild('importe', isset($prod->importe) ? $prod->importe : '');
                $producto->addChild('precio_venta', isset($prod->precio_venta) ? $prod->precio_venta : '');
            }

            $cuotas = $page->addChild('cuotas');
            foreach ($doc->cuotas as $c) {
                $cuota = $cuotas->addChild('cuota');
                $cuota->addAttribute('id', $c->id);
                $cuota->addChild('letra', isset($c->letra) ? $prod->letra : '');
                $cuota->addChild('fecha', isset($c->fecha) ? $prod->fecha : '');
                $cuota->addChild('monto', isset($c->monto) ? $prod->monto : '');
            }

        }

        return $xml->asXML();

    }


    function getVenta($id){

    }
}
