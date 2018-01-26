<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_devolucion_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_detalle_by_venta_id($venta_id)
    {
        $q = "SELECT * from detalle_venta where id_venta='$venta_id'";
        $result = $this->db->query($q);
        foreach ($result->result() as $row) {
            $filas[] = $row;
        }
        return $filas;
    }

    public function get_devolucion_by_venta_id($venta_id)
    {
        $q = "SELECT * from venta_devolucion
      inner join producto on venta_devolucion.id_producto = producto.producto_id
      inner join unidades on venta_devolucion.unidad_medida = unidades.id_unidad
       where id_venta='$venta_id'";

        $result = $this->db->query($q);

        foreach ($result->result() as $row) {


            $filas[] = (array)$row;
        }

        return isset($filas) ? $filas : NULL;
    }

    public function comparar_devolucion($venta_id, $detalle_cambiando = array())
    {
        $detalle_actual = $this->get_detalle_by_venta_id($venta_id);
        if (count($detalle_actual) != count($detalle_cambiando)) {
            //cambio la cantidad de productos diferentes
            $resultado = $this->buscar_producto_que_falta($detalle_cambiando, $detalle_actual);
            $resultado2 = $this->buscar_producto_que_falta($detalle_cambiando, $detalle_actual, 1);
            $resultado = array_merge($resultado, $resultado2);
            // print_r($resultado);
            // die;
        } else {
            //no cambio la cantidad de productos diferentes pero toda analizar la cantidad de productos iguales
            $resultado = $this->buscar_producto_que_falta($detalle_cambiando, $detalle_actual, 1);

            if ($resultado === false) {
                return false;
            }
        }
        return $resultado;

    }

    public function buscar_producto_que_falta($cambiando, $actual, $cantidad = NULL)
    {
        $no_encontrados = array();
        if (is_array($cambiando) and is_array($actual)) {
            for ($i = 0; $i < count($actual); $i++) {
                for ($j = 0; $j < count($cambiando); $j++) {
                    if ($cantidad != NULL) {
                        if ($actual[$i]->id_producto == $cambiando[$j]->id_producto and (int)$cambiando[$j]->cantidad != (int)$actual[$i]->cantidad) {

                            // var_dump( (int)$actual[$j]->cantidad);
                            // var_dump( (int)$cambiando[$j]->cantidad);


                            //devolvio algo
                            if ($actual[$i]->cantidad > $cambiando[$j]->cantidad) {
                                $cantidad_devuelta = $actual[$i]->cantidad - $cambiando[$j]->cantidad;
                                $obj = new \stdClass;
                                $obj->id_producto = $cambiando[$j]->id_producto;
                                $obj->nombre = $cambiando[$j]->nombre;
                                $obj->precio = $cambiando[$j]->precio;
                                $obj->cantidad = $cantidad_devuelta;
                                $obj->unidad_medida = $cambiando[$j]->unidad_medida;
                                $obj->unidad_nombre = $cambiando[$j]->unidad_nombre;
                                $obj->detalle_importe = $cambiando[$j]->precio * $cantidad_devuelta;
                                $obj->unidades = $cambiando[$j]->unidades;
                                $obj->producto_cualidad = $cambiando[$j]->producto_cualidad;
                                $obj->porcentaje_impuesto = $cambiando[$j]->porcentaje_impuesto;
                                $obj->count = $cambiando[$j]->count;
                                $obj->subtotal = $cambiando[$j]->precio * $cantidad_devuelta;
                                $obj->otros = json_encode($actual[$i]);
                                $no_encontrados[] = $obj;
                                $encontro = 1;
                            } else {
                                return false;
                            }
                        }
                    } else {
                        if ($actual[$i]->id_producto == $cambiando[$j]->id_producto) {
                            $encontro = 1;
                        }
                    }
                }
                if (!$cantidad) {
                    if (isset($encontro) and $encontro == 1) {
                        $encontro = 0;
                    } else {
                        $no_encontrados[] = $actual[$i];
                    }
                } else {
                    if (isset($encontro) and $encontro == 1) {
                        $encontro = 0;
                    }
                }
            }
        }
        return $no_encontrados;
    }

    //Este metodo de devolucion hay que hacerle algunos ajustes
    public function insertar_devolucion($devoluciones, $id_venta, $local = 1)
    {
        //die($id_venta);
        $this->load->model('historico/historico_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('producto/producto_model');

        $cantidad_devuelta = array();

        for ($i = 0; $i < count($devoluciones); $i++) {
            # code...
            $data = array(
                'id_venta' => $id_venta,
                'id_producto' => $devoluciones[$i]->id_producto,
                'precio' => $devoluciones[$i]->precio,
                'cantidad' => $devoluciones[$i]->cantidad,
                'unidad_medida' => $devoluciones[$i]->unidad_medida,
                'detalle_importe' => '0',
                'detalle_costo_promedio' => '0'
            );

            if (!isset($cantidad_devuelta[$devoluciones[$i]->id_producto]))
                $cantidad_devuelta[$devoluciones[$i]->id_producto] = 0;
            $cantidad_devuelta[$devoluciones[$i]->id_producto] += $this->unidades_model->convert_minimo_by_um(
                $devoluciones[$i]->id_producto,
                $devoluciones[$i]->unidad_medida,
                $devoluciones[$i]->cantidad);

            $this->db->insert('venta_devolucion', $data);
        }

        foreach ($cantidad_devuelta as $key => $value) {
            $old_cantidad = $this->db->get_where('producto_almacen', array(
                "id_local" => $local,
                "id_producto" => $key
            ))->row();

            //Llevo la cantidad vieja tambien a la minima expresion y la sumo con la minima expresion
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($key, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

            $result = $this->unidades_model->get_cantidad_fraccion($key, $old_cantidad_min + $value);


            if ($old_cantidad != NULL && $result != NULL) {
                //CREAR EL HISTORICO DE LA DEVOLUCION DE LA VENTA *************************************
                $this->historico_model->set_historico(array(
                    'producto_id' => $key,
                    'local_id' => $local,
                    'usuario_id' => $this->session->userdata('nUsuCodigo'),
                    'cantidad' => $value,
                    'cantidad_actual' => $this->unidades_model->convert_minimo_um($key, $result['cantidad'], $result['fraccion']),
                    'tipo_movimiento' => "DEVOLUCION",
                    'tipo_operacion' => 'ENTRADA',
                    'referencia_valor' => 'Devolucion de Ventas',
                    'referencia_id' => $id_venta
                ));


                $this->inventario_model->update_producto_almacen($key, $local, array(
                    'cantidad' => $result['cantidad'],
                    'fraccion' => $result['fraccion']));
            }
        }


        $fecha = $this->db->get_where('venta', array('venta_id' => $id_venta))->row();
        $campos = array('venta_status' => 'DEVUELTO', 'fecha' => $fecha->fecha);

        $this->db->where('venta_id', $id_venta);
        $this->db->update('venta', $campos);
        // var_dump($this->db->update('venta', $campos));
        // die;

    }

}
