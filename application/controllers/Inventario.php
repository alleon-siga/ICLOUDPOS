<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class inventario extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('inventario/inventario_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('producto/producto_model');
            $this->load->model('ajusteinventario/ajusteinventario_model');
            $this->load->model('ajustedetalle/ajustedetalle_model');
            $this->load->model('local/local_model');
            $this->load->model('marca/marcas_model');
            $this->load->model('grupos/grupos_model');
            $this->load->model('linea/lineas_model');
            $this->load->model('familia/familias_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('columnas/columnas_model');
            $this->load->model('venta/venta_model');
            $this->load->model('detalle_ingreso/detalle_ingreso_model');
            $this->load->model('precio/precios_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('unidades_has_precio/unidades_has_precio_model');
            $this->load->model('ingreso/ingreso_model');
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('usuario/usuario_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
            $this->load->helper('form');
            $this->columnas = $this->columnas_model->get_by('tabla', 'producto');
            $this->load->library('Pdf');
            $this->load->library('phpExcel/PHPExcel.php');
        }else{
            redirect(base_url(), 'refresh');
        }


    }


    function ajuste()
    {


        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data['ajustes'] = array();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        //$data['locales'] = $this->local_model->get_all();
        //$data['ajustes'] = $this->ajusteinventario_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/ajuste', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function ajusteinventario_by_local()
    {



        if ($this->input->is_ajax_request()) {
            if ($this->input->post('locales') != "seleccione") {

                $condicion = array('local_id' => $this->input->post('locales'));

                if ($this->input->post('fecIni') != "") {

                    $condicion['fecha >= '] = date('Y-m-d', strtotime($this->input->post('fecIni'))) . " " . date('H:i:s', strtotime('0:0:0'));
                    //$data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('fecIni'))) . " " . date('H:i:s', strtotime('0:0:0'));
                }
                if ($this->input->post('hasta') != "") {

                    $condicion['fecha <='] = date('Y-m-d', strtotime($this->input->post('fecFin'))) . " " . date('H:i:s', strtotime('23:59:59'));
                    //$data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('fecFin'))) . " " . date('H:i:s', strtotime('23:59:59'));
                }
                $data['ajustes'] = $this->ajusteinventario_model->get_ajuste_inventario($condicion);

                $this->load->view('menu/inventario/lista_ajustes', $data);
            }
        } else {
            redirect(base_url() . 'inicio/', 'refresh');
        }
    }

    function addajuste($local_id = FALSE)
    {

        $data['locales'] = $this->local_model->get_all();
        $data['productos'] = $this->producto_model->select_all_producto();
        $data['id_local_sel'] = $local_id;

        $this->load->view('menu/inventario/addajuste', $data);
    }

    function movimiento($id_local = "")
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        //$data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $local = $id_local == "" ? $this->session->userdata('id_local') : $id_local;

        if ($local != "TODOS")
            $data["lstProducto"] = $this->producto_model->get_all_by_local_producto($local);
        else
            $data["lstProducto"] = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'));
        $data["local_selected"] = $local;

        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/movimiento', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function _getMovimientos($id, $local, $operacion)
    {
        if ($id != false) {
            $this->load->model('historico/historico_model');

            $data['producto'] = $this->producto_model->get_by('producto_id', $id);

            $condicion = array(
                'movimiento_historico.producto_id' => $id,

            );
            if ($local != "TODOS") {
                $data['local'] = $this->local_model->get_by('int_local_id', $local);
                $condicion['movimiento_historico.local_id'] = $data['local']['int_local_id'];
            } else {
                $data['local'] = "TODOS";
            }

            if ($operacion != "TODOS") {
                $data['operacion'] = $operacion;
                $condicion['movimiento_historico.tipo_operacion'] = $operacion;

            }
            $data['movimientos'] = $this->historico_model->get_historico($condicion);

            return $data;
        }
    }

    function formMovimiento($id = false, $local = false)
    {
        $data = $this->_getMovimientos($id, $local, 'TODOS');
        $this->load->view('menu/inventario/formMovimiento', $data);
    }

    function existencia_producto()
    {

        $this->load->view('menu/inventario/existencia_producto');
    }

    function buscarproducto()
    {
        $id = $this->input->post('id');
        $data = $this->producto_model->get_by_id($id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function verajuste($id = FALSE, $local_id)
    {

        $data['detalles'] = $this->ajustedetalle_model->get_ajuste_by_inventario($id);


        $this->load->view('menu/inventario/verajuste', $data);
    }

    function get_refresh_stock()
    {
        $ids = $this->input->post('ids');
        $ids = json_decode($ids);
        if (count($ids) > 0) {
            $producto_ids = array();
            foreach ($ids as $id)
                $producto_ids[] = $id->id;

            $this->db->where_in('id_producto', $producto_ids);
            $data['result'] = $this->db->get('unidades_has_precio')->result();
        } else {
            $data['result'] = "";
        }
        echo json_encode($data);
    }

    function get_unidades_has_producto()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = $this->unidades_model->get_by_producto($id_producto);

        $data['costo_activo'] = $this->producto_costo_unitario_model->get_costo_activo($id_producto);
        $data['moneda_id'] = $this->monedas_model->get_moneda_default();
        $data['moneda_id'] = $data['moneda_id']->id_moneda;

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_ajuste_unidades()
    {
        $producto_id = $this->input->post('producto_id');
        $local_id = $this->input->post('local_id');

        $data['unidades'] = $this->unidades_model->get_unidades_cantidad($producto_id, $local_id);

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min);
        $data['moneda'] = $this->unidades_model->get_moneda_default($producto_id);
        $data['stock_minimo'] = $old_cantidad_min;

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function guardar()
    {
        $this->load->model('historico/historico_model');
        $productos = $this->input->post('productos');
        $local_id = $this->input->post('local_id');
        $descripcion = $this->input->post('descripcion');
        $productos = json_decode($productos);


        //guardo ene ajuste inventario
        $ajuste_id = $this->ajusteinventario_model->set_ajuste(array(
            'fecha' => date("Y-m-d H:s:i"),
            'descripcion' => $descripcion,
            "local_id" => $local_id,
            "usuario_encargado" => $this->session->userdata('nUsuCodigo')
        ));

        foreach ($productos as $prod) {

            $cantidad_fraccion = $this->unidades_model->get_cantidad_fraccion($prod->producto_id, $prod->total);
            $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $prod->producto_id, 'id_local' => $local_id))->row();


            //guardo en ajustes detalles
            $ajuste_detalle_value = array(
                'id_ajusteinventario' => $ajuste_id,
                'cantidad_detalle' => $cantidad_fraccion['cantidad'],
                'fraccion_detalle' => $cantidad_fraccion['fraccion'],
                'id_unidad' => $cantidad_fraccion['max_um_id'],
                'old_cantidad' => 0,
                'old_fraccion' => 0,
                'id_producto_almacen' => $prod->producto_id
            );

            if ($old_cantidad != NULL) {
                $ajuste_detalle_value['old_cantidad'] = $old_cantidad->cantidad;
                $ajuste_detalle_value['old_fraccion'] = $old_cantidad->fraccion;
            }

            $ajuste_detalle_id = $this->ajustedetalle_model->set_ajuste_detalle($ajuste_detalle_value);


            //guardo el costo unitario
            $costo_actual = $this->db->get_where('producto_costo_unitario', array(
                'producto_id' => $prod->producto_id,
                'contable_activo' => '1'
            ))->row();

            $this->producto_costo_unitario_model->save_costos(array(
                'producto_id' => $prod->producto_id,
                'moneda_id' => $prod->moneda_id,
                'costo' => $prod->costo,
                'contable_costo' => $costo_actual->contable_costo,
                'activo' => '1',
                'contable_activo' => $costo_actual->moneda_id
            ), $prod->tasa);


            //guardo el historico
            $values = array(
                'producto_id' => $prod->producto_id,
                'local_id' => $local_id,
                'cantidad' => $prod->total,
                'cantidad_actual' => $this->unidades_model->convert_minimo_um($prod->producto_id, $cantidad_fraccion['cantidad'], $cantidad_fraccion['fraccion']),
                'tipo_movimiento' => "AJUSTE",
                'referencia_valor' => 'Se realizo un Ajuste',
                'referencia_id' => $ajuste_detalle_id,
            );

            $this->historico_model->set_historico($values);


            //actualizo el almacen
            if ($old_cantidad != NULL) {
                $this->inventario_model->update_producto_almacen($prod->producto_id, $local_id, array(
                    'cantidad' => $cantidad_fraccion['cantidad'],
                    'fraccion' => $cantidad_fraccion['fraccion'],
                ));
            } else {
                $this->inventario_model->insert_producto_almacen($prod->producto_id, $local_id, $cantidad_fraccion['cantidad'], $cantidad_fraccion['fraccion']);
            }
        }

        $this->session->set_flashdata('success', 'Operación realizada exitosamente');
        $json['success'] = 'Operación realizada exitosamente';

        header('Content-Type: application/json');
        echo json_encode($json);
    }


    function get_existencia_producto()
    {
        $producto = $this->input->post('producto');
        $id_cliente = $this->input->post('id_cliente');
        $local = $this->input->post('local');

        $existencia = $this->inventario_model->get_by_existencia($producto, $local);
        $unidades = $this->unidades_model->get_by_producto($producto);

        if ($id_cliente != "") {
            $data['precios_cliente'] = $this->cliente_model->get_by('id_cliente', $id_cliente);
        }

        $data["precios_normal"] = $this->precios_model->get_precios();
        if ($this->input->is_ajax_request()) {

            $minimo = $this->unidades_model->convert_minimo_um($producto, $existencia['cantidad'], $existencia['fraccion']);
            $data['um'] = $this->unidades_model->get_cantidad_fraccion($producto, $minimo);
            $data['minimo_um'] = $minimo;

            $data['nombre'] = $unidades[0]['producto_nombre'];
            $data['cualidad'] = $unidades[0]['producto_cualidad'];


            echo json_encode($data);


        } else {
            redirect('productos');
        }

    }

    function view_reporte()
    {

        if ($this->input->post('id_local') != "seleccione") {
            $local = $this->input->post('id_local');
            $tipo = $this->input->post('tipo');
            $porcentaje = 30;
            if ($tipo == "MINIMA") {
                $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
 JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad <= producto_stockminimo";
            } elseif ($tipo == "ALTA") {

                $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
            } elseif ($tipo == "BAJA") {
                $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
            }


            $data['inventarios'] = $this->inventario_model->get_all_by_array($arreglo);;
            $data['tipo_reporte'] = $tipo;
            $data['local'] = $local;
            $this->load->view('menu/inventario/lista_reporte', $data);

        }
    }

    function existencia_minima()
    {
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['tipo'] = "MINIMA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function existencia_alta()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['tipo'] = "ALTA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function existencia_baja()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['tipo'] = "BAJA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function valorizacion_inventario()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['locales'] = $this->local_model->get_all();
        $data['marcas'] = $this->marcas_model->get_marcas();
        $data['grupos'] = $this->grupos_model->get_grupos();
        $data['lineas'] = $this->lineas_model->get_lineas();
        $data['familias'] = $this->familias_model->get_familias();
        $data['monedas'] = $this->monedas_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/valorizacion_inventario', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function get_valorizacion_inventario()
    {

        $condicion = array();


        $local = $this->input->post('local');
        $marca = $this->input->post('marca');
        $grupo = $this->input->post('grupo');
        $linea = $this->input->post('linea');
        $familia = $this->input->post('familia');
        $monedas = $this->input->post('monedas');

        if (!empty($local)) {
            $condicion['inventario.id_local'] = $local;
            $data['local'] = $local;

        }
        if (!empty($marca)) {
            $condicion['producto.producto_marca'] = $marca;
            $data['marca'] = $marca;
        }
        if (!empty($grupo)) {
            $condicion['producto.produto_grupo'] = $grupo;
            $data['grupo'] = $grupo;
        }
        if (!empty($linea)) {
            $condicion['producto.producto_linea'] = $linea;
            $data['linea'] = $linea;
        }
        if (!empty($familia)) {
            $condicion['producto.producto_familia'] = $familia;
            $data['familia'] = $familia;
        }

        if (!empty($monedas)) {
            $moneda = $this->monedas_model->get_by('id_moneda', $monedas);

            if (intval($moneda['tasa_soles']) > 0) {
                $data['operacion'] = $moneda['ope_tasa'];
                $data['tasa_soles'] = $moneda['tasa_soles'];

            }


            $data['moneda_nombre'] = $moneda['nombre'];
            $data['moneda'] = $monedas;
            $data['moneda_simbolo'] = $moneda['simbolo'];
        }
        $data['usar'] = $this->input->post('usar');


        $data['productos'] = $this->inventario_model->get_valorizacion($condicion);

        $all_stock = false;
        if ($local == "")
            $all_stock = true;

        $data["productos"] = $this->_getUnidMinima($data['productos'], $data['usar'], $all_stock);
        $this->load->view('menu/inventario/tabla_valorizacion', $data);

    }


    //Creo la consulta para asignarle las unidades y la fraccion a los productos
    function _getUnidMinima($productos, $usar, $all_stock = false)
    {
        $temp = $productos;

        $select = 'unidades.nombre_unidad, unidades_has_producto.*';
        $from = "unidades";
        $join = array('unidades_has_producto');
        $campos_join = array('unidades_has_producto.id_unidad=unidades.id_unidad');
        $order = "orden ASC";

        for ($i = 0; $i < count($temp); $i++) {
            $where = array('unidades_has_producto.producto_id' => $temp[$i]['producto_id']);
            $buscar = $this->unidades_model->traer_by($select, $from, $join, $campos_join, $where, false, $order, "RESULT_ARRAY");

            if (!empty($buscar)) {
                //  var_dump($buscar);

                foreach ($buscar as $unidad) {

                    if ($unidad['orden'] == sizeof($buscar)) {
                        $unidad_minima = $unidad;
                        //var_dump($unidad_minima);
                    }
                }
                if ($all_stock == true) {
                    $all_cantidades = $this->db->get_where('producto_almacen', array('id_producto' => $temp[$i]['producto_id']))->result();
                    $cantidad_unidad_maxima = 0;
                    $cantidad_fraccion = 0;
                    foreach ($all_cantidades as $cantidad) {
                        $cantidad_unidad_maxima += $cantidad->cantidad;
                        $cantidad_fraccion += $cantidad->fraccion;
                    }


                } else {
                    $cantidad_unidad_maxima = $temp[$i]['cantidad'];
                    $cantidad_fraccion = $temp[$i]['fraccion'];
                }

                $unidades_unidad_maxima = $buscar[0]['unidades'];
                $total_unidades_minimas = $cantidad_unidad_maxima * $unidades_unidad_maxima;
                $total_unidades_minimas = $total_unidades_minimas + $cantidad_fraccion;


                $precio = $this->precios_model->get_by_unidad_and_producto($temp[$i]['producto_id'], $unidad_minima['id_unidad']);

                //  var_dump($precio);
                $temp[$i]['precio'] = $precio[0]['precio'];

                $temp[$i]['nombre_unidad'] = $unidad_minima['nombre_unidad'];
                $temp[$i]['stock'] = $total_unidades_minimas;
            }

            if ($usar == 'ultimo_costo') {
                /*obtengo las unidades minimas*/
                $unidades_minimas = $this->unidades_model->convert_minimo_um($temp[$i]['producto_id'], $temp[$i]['cantidad'], $temp[$i]['fraccion']);

                /*obtengo el id de la unidad minima*/
                $unidad_minima = $this->unidades_model->get_cantidad_fraccion($temp[$i]['producto_id'], $unidades_minimas);
                /*obtengo el costo unitario convertido a unidad minima*/
                $temp[$i]['producto_costo_unitario'] = $this->unidades_model->get_costo_unitario_by_um($temp[$i]['producto_id'], $unidad_minima['min_um_id'], $temp[$i]['producto_costo_unitario']);
            } else {
                $query_compra = $this->db->query("SELECT AVG(detalleingreso.precio/(unidades_has_producto.unidades * detalleingreso.cantidad)) AS promedio
             FROM detalleingreso
            JOIN `unidades_has_producto` ON unidades_has_producto.`id_unidad`=detalleingreso.`unidad_medida` AND unidades_has_producto.`producto_id`=" . $temp[$i]['producto_id'] . "
            JOIN ingreso ON ingreso.id_ingreso = detalleingreso.id_ingreso
            WHERE id_producto=" . $temp[$i]['producto_id'] . "  ORDER BY id_detalle_ingreso ASC LIMIT 1");

                $result_compra = $query_compra->row_array();


                $promedio_compra = $result_compra['promedio'];


                $temp[$i]['producto_costo_unitario'] = $promedio_compra;
            }
        }

        return $temp;
    }


    function pdf_valorizacion($local, $marca, $grupo, $linea, $familia, $monedas, $usar)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $condicion = array();
        if ($local != 0) {
            $condicion['inventario.id_local'] = $local;
            $data['local'] = $local;

        }
        if ($marca != 0) {
            $condicion['producto.producto_marca'] = $marca;
            $data['marca'] = $marca;
        }
        if ($grupo != 0) {
            $condicion['producto.produto_grupo'] = $grupo;
            $data['grupo'] = $grupo;
        }
        if ($linea != 0) {
            $condicion['producto.producto_linea'] = $linea;
            $data['linea'] = $linea;
        }
        if ($familia != 0) {
            $condicion['producto.producto_familia'] = $familia;
            $data['familia'] = $familia;
        }

        if (!empty($monedas)) {
            $moneda = $this->monedas_model->get_by('id_moneda', $monedas);
            if (intval($moneda['tasa_soles']) > 0) {
                $operacion = $moneda['ope_tasa'];

            }
            $tasa_soles = $moneda['tasa_soles'];
            $moneda_nombre = $moneda['nombre'];
        }

        $data['productos'] = $this->inventario_model->get_valorizacion($condicion);
        $all_stock = false;
        if ($local == 0)
            $all_stock = true;
        $data["productos"] = $this->_getUnidMinima($data['productos'], $usar, $all_stock);

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Valorizacion de Inventario');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.•.G.•.D.•.G.•.A.•.D.•.U.•.<br>Gran Logia de la República de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relación utilizada para ajustar la conversión de los píxeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
// Helvetica para reducir el tamaño del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// Añadir una página
// Este método tiene varias opciones, consulta la documentación para más información.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b>" . $this->session->userdata('EMPRESA_NOMBRE') . "</b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<b><u>Valorizacion de Inventario</u></b><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<b>Fecha Emision " . date('d-m-Y') . "</b><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'L', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        if (isset($data['productos'])) {

            $html .= "<table><tr><th>Id Producto</th><th>Nombre</th><th>Marca</th><th>Grupo</th> ";
            $html .= "<th>Unidad</th><th>Moneda</th><th>Precio de venta</th><th>Costo de compra</th><th>Stock Actual</th><th>Total</th></tr>";

            $total = 0;
            foreach ($data['productos'] as $producto) {

                $subtotal = $producto['stock'] * $producto['producto_costo_unitario'];
                $total = $subtotal + $total;

                $precio = $producto['precio'];
                $producto_costo_unitario = $producto['producto_costo_unitario'];
                if (isset($operacion)) {
                    $string = '$precio$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");
                    eval("\$precio = ($string);");

                    $string = '$producto_costo_unitario$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");;
                    eval("\$producto_costo_unitario = ($string);");

                    $string = '$subtotal$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");
                    eval("\$subtotal = ($string);");
                }


                $html .= " <tr><td >" . sumCod($producto['producto_id']) . "</td><td >" . $producto['producto_nombre'] . "</td><td >" . $producto['nombre_marca'] . "</td>";
                $html .= "<td>" . $producto['nombre_grupo'] . "</td><td> " . $producto['nombre_unidad'] . "</td><td>" . $moneda_nombre . "</td><td>" . number_format($precio, 2, '.', ',') . "</td><td>" . number_format($producto_costo_unitario, 2, '.', ',') . "</td>";
                $html .= "<td >" . number_format($producto['stock'], 2, '.', ',') . "</td><td>" . number_format($subtotal, 2, '.', ',') . "</td></tr>";

            }
        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("Reporte-Valorizacion-Inventario.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel_valorizacion($local, $marca, $grupo, $linea, $familia, $monedas, $usar)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        $condicion = array();
        if ($local != 0) {
            $condicion['inventario.id_local'] = $local;
            $data['local'] = $local;
        }
        if ($marca != 0) {
            $condicion['producto.producto_marca'] = $marca;
            $data['marca'] = $marca;
        }
        if ($grupo != 0) {
            $condicion['producto.produto_grupo'] = $grupo;
            $data['grupo'] = $grupo;
        }
        if ($linea != 0) {
            $condicion['producto.producto_linea'] = $linea;
            $data['linea'] = $linea;
        }
        if ($familia != 0) {
            $condicion['producto.producto_familia'] = $familia;
            $data['familia'] = $familia;
        }

        $data['productos'] = $this->inventario_model->get_valorizacion($condicion);
        $all_stock = false;
        if ($local == 0)
            $all_stock = true;
        $data["productos"] = $this->_getUnidMinima($data['productos'], $usar, $all_stock);
        $columna[0] = "Id Producto";
        $columna[1] = "Nombre";
        $columna[2] = "Marca";
        $columna[3] = "Grupo";
        $columna[4] = "Unidad";
        $columna[5] = "Moneda";
        $columna[6] = "Precio de venta";
        $columna[7] = "Costo de compra";
        $columna[8] = "Stock Actual";
        $columna[9] = "Total";
        $data['columna'] = $columna;
        $moneda = $this->monedas_model->get_by('id_moneda', $monedas);
        if (intval($moneda['tasa_soles']) > 0) {
            $operacion = $moneda['ope_tasa'];
            $data['operacion'] = $operacion;
        }
        $tasa_soles = $moneda['tasa_soles'];
        $moneda_nombre = $moneda['nombre'];

        $data['tasa_soles'] = $tasa_soles;
        $data['moneda_nombre'] = $moneda_nombre;
        /* if (!empty($monedas)) {
             $moneda = $this->monedas_model->get_by('id_moneda', $monedas);
             if (intval($moneda['tasa_soles']) > 0) {
                 $operacion = $moneda['ope_tasa'];

             }
             $tasa_soles = $moneda['tasa_soles'];
             $moneda_nombre = $moneda['nombre'];
         }
         // configuramos las propiedades del documento
         $this->phpexcel->getProperties()
             //->setCreator("Arkos Noem Arenom")
             //->setLastModifiedBy("Arkos Noem Arenom")
             ->setTitle("Ventas")
             ->setSubject("Ventas")
             ->setDescription("Ventas")
             ->setKeywords("Ventas")
             ->setCategory("Ventas");




         $this->phpexcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(0, 1, $this->session->userdata('EMPRESA_NOMBRE'));
         $sheet = $this->phpexcel->getActiveSheet(0);
         $sheet->setCellValueByColumnAndRow(0, 2, 'Valorizacion de inventario');
         $sheet->setCellValueByColumnAndRow(0, 4, "Feha de emision ".date('d-m-Y'));
         $sheet->getStyle('A2:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


         $sheet->mergeCells('A1:J1');
         $sheet->mergeCells('J1:J2');
         $sheet->mergeCells('A4:J4');


         $col = 0;
         for ($i = 0; $i < count($columna); $i++) {

             $this->phpexcel->getActiveSheet(0)
                 ->setCellValueByColumnAndRow($i, 5, $columna[$i]);
             $this->phpexcel->getActiveSheet()
                 ->getColumnDimension($i)
                 ->setAutoSize(true);
         }

         $row = 6;

         if (isset($data['productos'])) {
             $total = 0;
             foreach ($data['productos'] as $producto) {


                 $subtotal = $producto['stock'] * $producto['producto_costo_unitario'];

                 $total = $subtotal + $total;


                 $precio = $producto['precio'];
                 $producto_costo_unitario = $producto['producto_costo_unitario'];
                 if (isset($operacion)) {
                     $string = '$precio$operacion$tasa_soles ';
                     eval("\$string = \"$string\";");
                     eval("\$precio = ($string);");

                     $string = '$producto_costo_unitario$operacion$tasa_soles ';
                     eval("\$string = \"$string\";");;
                     eval("\$producto_costo_unitario = ($string);");

                     $string = '$subtotal$operacion$tasa_soles ';
                     eval("\$string = \"$string\";");
                     eval("\$subtotal = ($string);");
                 }


                 $col = 0;
                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, sumCod($producto['producto_id']));

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, $producto['producto_nombre']);

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, $producto['nombre_marca']);

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, $producto['nombre_grupo']);

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, $producto['nombre_unidad']);

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, $moneda_nombre);

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, number_format($precio,2,',','.'));

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, number_format($producto_costo_unitario, 2,',','.'));

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, number_format($producto['stock'],2,',','.'));

                 $this->phpexcel->setActiveSheetIndex(0)
                     ->setCellValueByColumnAndRow($col++, $row, number_format($subtotal, 2,',','.'));


                 $row++;

             }
         }
 // Renombramos la hoja de trabajo
         $this->phpexcel->getActiveSheet()->setTitle('Valorizaciondeinventario');


 // configuramos el documento para que la hoja
 // de trabajo número 0 sera la primera en mostrarse
 // al abrir el documento
         $this->phpexcel->setActiveSheetIndex(0);


 // redireccionamos la salida al navegador del cliente (Excel2007)
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment;filename="Ingreso_y_Salida_de_Producto.xlsx"');
         header('Cache-Control: max-age=0');

         $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
         $objWriter->save('php://output');

     */
        $this->load->view('menu/reportes/excelValorizacionInventario', $data);
    }

    function pdf($id, $local)
    {
        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        if ($id == "MINIMA") {
            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
 JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo";
        } elseif ($id == "ALTA") {

            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
        } elseif ($id == "BAJA") {
            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
        }


        $result['inventarios'] = $this->inventario_model->get_all_by_array($arreglo);

        $html = $this->load->view('menu/reportes/pdfExistenciaMinima', $result, true);


        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("ExistenciaMinima.pdf");
        $pdf->Output($nombre_archivo, 'I');

    }

    function pdfMovimiento($id, $local, $operacion = "TODOS")
    {

        $mpdf = new mPDF('utf-8', 'A4-L');
        $data = $this->_getMovimientos($id, $local, $operacion);

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfMovimientoInventario', $data, true));
        $mpdf->Output();

    }


    function excelMovimiento($id, $local, $tipo_operacion = "TODOS")
    {
        $data = $this->_getMovimientos($id, $local, $tipo_operacion);
        $this->load->view('menu/reportes/excelMovimientoInventario', $data);

    }


    function excel($id, $local)
    {

        $porcentaje = 30;
        if ($id == "MINIMA") {
            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
 JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo";
        } elseif ($id == "ALTA") {

            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
        } elseif ($id == "BAJA") {
            $arreglo = "SELECT * FROM producto_almacen JOIN producto ON producto.`producto_id`=producto_almacen.`id_producto`
  JOIN local ON local.`int_local_id`=producto_almacen.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
        }


        $result['inventarios'] = $this->inventario_model->get_all_by_array($arreglo);
        $this->load->view('menu/reportes/excelExistenciaMinima', $result);

    }

}
