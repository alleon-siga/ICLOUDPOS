<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingreso_calzado_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('cajas/cajas_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('historico/historico_model');
        $this->load->model('ingreso/ingreso_model');


    }

    public function save($ingreso, $plantillas)
    {

        $compra = array(
            'fecReg' => date('Y-m-d H:i:s'),
            'cboProveedor' => $ingreso['proveedor_id'],
            'fecEmision' => date('Y-m-d H:i:s', strtotime($ingreso['fecha_emision'])),
            'local_id' => $ingreso['local_id'],
            'cboTipDoc' => $ingreso['tipo_documento'],
            'doc_serie' => $ingreso['documento_serie'],
            'doc_numero' => $ingreso['documento_numero'],
            'status' => 'COMPLETADO',
            'montoigv' => $ingreso['impuesto'],
            'tipo_ingreso' => 'COMPRA',
            'subTotal' => $ingreso['subtotal'],
            'totApagar' => $ingreso['total'],
            'pago' => $ingreso['pago'],
            'ingreso_observacion' => '',
            'id_moneda' => $ingreso['moneda_id'],
            'tasa_cambio' => $ingreso['tasa'] != 0 ? $ingreso['tasa'] : NULL,
            'costos' => 'true',
            'facturar' => 0,
            'costo_por' => $ingreso['costo'],
            'utilidad_por' => $ingreso['utilidad'],
        );

        //AQUI HAY QUE DEFINIR EL ID DE LA UNIDAD DE MEDIDA
        $unidad_id = 1;
        //AQUI HAY QUE DEFINIR EL ID DEL PRECIO
        $precio_venta_id = 1;
        $precio_descuento_id = 2;
        $precio_unitario_id = 3;

        $detalles = array();
        foreach ($plantillas as $plantilla) {
            $producto = $this->db->get_where('producto', array('producto_codigo_interno' => $plantilla->id))->row();

            if ($producto == NULL) {
                $this->db->insert('producto', array(
                    'producto_codigo_interno' => $plantilla->id,
                    'producto_nombre' => $plantilla->nombre,
                    'producto_cualidad' => 'MEDIBLE',
                    'producto_estado' => 1,
                    'producto_costo_unitario' => $plantilla->costo_unitario
                ));
                $producto_id = $this->db->insert_id();

                //inserto las unidades
                $this->db->insert('unidades_has_producto', array(
                    'id_unidad' => $unidad_id,
                    'producto_id' => $producto_id,
                    'unidades' => 1,
                    'orden' => 1
                ));

                //inserto sus precios. ahora esta insertando el maximo ya que el minimo puede ser calculado
                $this->db->insert('unidades_has_precio', array(
                    'id_precio' => $precio_venta_id,
                    'id_unidad' => $unidad_id,
                    'id_producto' => $producto_id,
                    'precio' => $plantilla->precio_max
                ));

                $this->db->insert('unidades_has_precio', array(
                    'id_precio' => $precio_descuento_id,
                    'id_unidad' => $unidad_id,
                    'id_producto' => $producto_id,
                    'precio' => $plantilla->precio_min
                ));

                $this->db->insert('unidades_has_precio', array(
                    'id_precio' => $precio_unitario_id,
                    'id_unidad' => $unidad_id,
                    'id_producto' => $producto_id,
                    'precio' => $plantilla->precio_max
                ));
            }
            else{
                $producto_id = $producto->producto_id;

                //actualizo los precios de venta si etos cambiaron
                $this->db->where('id_unidad', $unidad_id);
                $this->db->where('id_producto', $producto_id);
                $this->db->update('unidades_has_precio', array(
                    'precio' => $plantilla->precio_max
                ));

                $this->db->where('id_unidad', $unidad_id);
                $this->db->where('id_producto', $producto_id);
                $this->db->where('id_precio', $precio_descuento_id);
                $this->db->update('unidades_has_precio', array(
                    'precio' => $plantilla->precio_min
                ));
            }

            $temp = new stdClass();
            $temp->producto_id = $producto_id;
            $temp->importe = number_format($plantilla->cantidad * $plantilla->costo_unitario);
            $temp->cantidad = $plantilla->cantidad;
            $temp->unidad = $unidad_id;
            $temp->costo_unitario = $plantilla->costo_unitario;
            $temp->series = array();

            array_push($detalles, $temp);
        }

        return $this->ingreso_model->insertar_compra($compra, $detalles);
    }


}
