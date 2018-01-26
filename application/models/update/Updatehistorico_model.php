<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class updatehistorico_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('unidades/unidades_model');
        $this->load->model('historico/historico_model');
    }

    function updateAjustesDetalles()
    {
        set_time_limit(0);

        $this->db->query('ALTER TABLE `ajustedetalle` DROP FOREIGN KEY `fk_ajustedetalle_2`');
        $this->db->query('ALTER TABLE `ajustedetalle` CHANGE COLUMN `id_inventario` `id_producto_almacen` BIGINT(20) NULL DEFAULT NULL;');

        $ajuste_detalles = $this->db->get('ajustedetalle')->result_array();

        foreach ($ajuste_detalles as $detalle) {
            $inv = $this->db->get_where('inventario', array('id_inventario' => $detalle['id_producto_almacen']))->row();

            $this->db->where('id_ajustedetalle', $detalle['id_ajustedetalle']);
            $this->db->update('ajustedetalle', array('id_producto_almacen' => $inv->id_producto));
        }

        echo '<p>Correccion de ajustes de detalles ejecutada.</p>';
    }

    function updatehistorico()
    {
        $this->updateAjuste();
        $this->updateIngreso();
        $this->updateVenta();

    }

    private function updateVenta()
    {

        $this->db->select('venta.id_vendedor, venta.local_id, venta.venta_status, venta.fecha, detalle_venta.*');
        $this->db->from('detalle_venta');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->order_by('id_producto,id_venta');
        $datos = $this->db->get()->result();

        $data = array();
        $cantidad_historico = 0;

        if (count($datos) > 0) {

            for ($i = 0; $i < count($datos); $i++) {


                /*pregunto por el estatus para saber la referencia*/
                if ($datos[$i]->venta_status == "ANULADO") {
                    $referencia_valor = "Se anulo la venta";
                    $movimiento = "ANULACION";
                    $operacion = "ENTRADA";
                }

                if ($datos[$i]->venta_status == "COMPLETADO") {
                    $referencia_valor = "Se realizo una venta";
                    $movimiento = "VENTA";
                    $operacion = "SALIDA";
                }

                if ($datos[$i]->venta_status == "DEVUELTO") {
                    $referencia_valor = "Se realizo una devolucion";
                    $movimiento = "DEVOLUCION";
                    $operacion = "ENTRADA";
                }
                /*calculo las unidades minimas*/
                $unidades_minimas = $this->unidades_model->convert_minimo_by_um($datos[$i]->id_producto, $datos[$i]->unidad_medida,
                    $datos[$i]->cantidad);


                /*voy sumando las unidades minimas*/
                $cantidad_historico += $unidades_minimas;


                if (isset($datos[$i + 1]) && $datos[$i]->id_venta == $datos[$i + 1]->id_venta &&
                    $datos[$i]->id_producto == $datos[$i + 1]->id_producto
                ) {

                    //esto es una bandera para que no haga el insert sobre la tabla historico
                    //sabiendo ya que los datos vienen ordenados por producto

                } else {

                    /*esto es para calcular lo de la old cantidad queda pendiente
                     * $almacen = $this->db->get_where('producto_almacen', array('id_producto' => $datos[$i]->id_producto,
                        'id_local' => $datos[$i]->local_id))->result();

                    $condicion=array(
                        'producto_id'=>$datos[$i]->id_producto
                    );
                    $historico_producto = $this->historico_model->get_historico_by($condicion);*/

                    /*busco la unidad*/
                    $unidad = $this->unidades_model->convert_maximo_um($datos[$i]->id_producto, $cantidad_historico);

                    $insert = array(
                        'producto_id' => $datos[$i]->id_producto,
                        'local_id' => $datos[$i]->local_id,
                        'usuario_id' => $datos[$i]->id_vendedor,
                        'um_id' => $unidad['um_id'],
                        'cantidad' => $cantidad_historico,
                        'date' => $datos[$i]->fecha,
                        'tipo_movimiento' => $movimiento,
                        'tipo_operacion' => $operacion,
                        'referencia_valor' => $referencia_valor,
                        'referencia_id' => $datos[$i]->id_venta,
                        'old_cantidad' => 0,
                    );
                    array_push($data, $insert);


                    /*reinicializo*/
                    $cantidad_historico = 0;
                }
            }

            /*hago el llamado al metodo que hace el insert*/
            $this->insert($data);

        }
    }

    private function updateIngreso()
    {

        $this->db->select('ingreso.nUsuCodigo, ingreso.local_id,ingreso.ingreso_status, ingreso.fecha_registro, detalleingreso.*');
        $this->db->from('detalleingreso');
        $this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->order_by('id_producto,id_ingreso');
        $datos = $this->db->get()->result();

        $data = array();
        $cantidad_historico = 0;
        if (count($datos) > 0) {

            for ($i = 0; $i < count($datos); $i++) {

                /*pregunto por el estatus para saber la referencia y el tipo de operacion*/
                if ($datos[$i]->ingreso_status == "COMPLETADO") {
                    $referencia_valor = "Se completo el ingreso";
                    $operacion = "ENTRADA";
                    $movimiento = "INGRESO";
                }
                if ($datos[$i]->ingreso_status == "DEVUELTO") {
                    $referencia_valor = "Se anulo el ingreso";
                    $operacion = "SALIDA";
                    $movimiento = "DEVOLUCION";
                }

                /*calculo las unidades minimas*/
                $unidades_minimas = $this->unidades_model->convert_minimo_by_um($datos[$i]->id_producto, $datos[$i]->unidad_medida,
                    $datos[$i]->cantidad);

                /*voy sumando las unidades minimas*/
                $cantidad_historico += $unidades_minimas;

                if (isset($datos[$i + 1]) && $datos[$i]->id_ingreso == $datos[$i + 1]->id_ingreso &&
                    $datos[$i]->id_producto == $datos[$i + 1]->id_producto
                ) {

                    //esto es una bandera para que no haga el insert sobre la tabla historico
                    //sabiendo ya que los datos vienen ordenados por producto

                } else {

                    /*busco la unidad*/
                    $unidad = $this->unidades_model->convert_maximo_um($datos[$i]->id_producto, $cantidad_historico);

                    $insert = array(
                        'producto_id' => $datos[$i]->id_producto,
                        'local_id' => $datos[$i]->local_id,
                        'usuario_id' => $datos[$i]->nUsuCodigo,
                        'um_id' => $unidad['um_id'],
                        'cantidad' => $cantidad_historico,
                        'date' => $datos[$i]->fecha_registro,
                        'tipo_movimiento' => $movimiento,
                        'tipo_operacion' => $operacion,
                        'referencia_valor' => $referencia_valor,
                        'referencia_id' => $datos[$i]->id_ingreso,
                        'old_cantidad' => 0,
                    );
                    array_push($data, $insert);

                    /*reinicializo*/
                    $cantidad_historico = 0;
                }
            }

            /*hago el llamado al metodo que hace el insert*/
            $this->insert($data);

        }
    }

    private function updateAjuste()
    {


        $this->db->select('*');
        $this->db->from('ajustedetalle');
        $this->db->join('ajusteinventario', 'ajusteinventario.id_ajusteinventario=ajustedetalle.id_ajusteinventario');
        $this->db->join('inventario', 'inventario.id_inventario=ajustedetalle.id_inventario');
        $this->db->order_by('id_producto,ajustedetalle.id_ajusteinventario');
        $datos = $this->db->get()->result();

        $data = array();
        $cantidad_historico = 0;
        if (count($datos) > 0) {

            for ($i = 0; $i < count($datos); $i++) {


                /*calculo las unidades minimas*/
                $unidades_minimas = $this->unidades_model->convert_minimo_by_um($datos[$i]->id_producto, $datos[$i]->id_unidad,
                    $datos[$i]->cantidad_detalle);

                /*voy sumando las unidades minimas*/
                $cantidad_historico += $unidades_minimas;

                if (isset($datos[$i + 1]) && $datos[$i]->id_ajusteinventario == $datos[$i + 1]->id_ajusteinventario &&
                    $datos[$i]->id_producto == $datos[$i + 1]->id_producto
                ) {

                    //esto es una bandera para que no haga el insert sobre la tabla historico
                    //sabiendo ya que los datos vienen ordenados por producto

                } else {

                    /*busco la unidad*/
                    $unidad = $this->unidades_model->convert_maximo_um($datos[$i]->id_producto, $cantidad_historico);

                    $insert = array(
                        'producto_id' => $datos[$i]->id_producto,
                        'local_id' => $datos[$i]->id_local,
                        'usuario_id' => $datos[$i]->usuario_encargado,
                        'um_id' => $unidad['um_id'],
                        'cantidad' => $cantidad_historico,
                        'date' => $datos[$i]->fecha,
                        'tipo_movimiento' => "AJUSTE",
                        'tipo_operacion' => "SALIDA",
                        'referencia_valor' => "Ajuste de inventario",
                        'referencia_id' => $datos[$i]->id_ajusteinventario,
                        'old_cantidad' => 0,
                    );
                    array_push($data, $insert);

                    /*reinicializo*/
                    $cantidad_historico = 0;
                }
            }

            /*hago el llamado al metodo que hace el insert*/
            $this->insert($data);

        }

    }

    function insert($data)
    {

        $this->db->insert_batch('movimiento_historico', $data);

    }


}