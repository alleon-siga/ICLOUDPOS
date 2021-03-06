<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto extends MY_Controller
{

    private $columnas = array();

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('producto/producto_model');
            $this->load->model('local/local_model');
            $this->load->model('columnas/columnas_model');
            $this->load->model('marca/marcas_model');
            $this->load->model('linea/lineas_model');
            $this->load->model('familia/familias_model');
            $this->load->model('grupos/grupos_model');
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('impuesto/impuestos_model');
            $this->load->model('inventario/inventario_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('detalle_ingreso/detalle_ingreso_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('precio/precios_model');
            $this->load->model('unidades_has_precio/unidades_has_precio_model');
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
            $this->load->library('Pdf');
            $this->load->library('phpExcel/PHPExcel.php');
        } else {
            redirect(base_url(), 'refresh');
        }


        $this->columnas = $this->columnas_model->get_by('tabla', 'producto');
    }


    //Creo la consulta para asignarle las unidades y la fraccion a los productos
    function _getUnidades($productos, $empty = true)
    {

        /*esta forma de hacerlo es traido de crdigitall, el cual funciona.
        ya que con el anterior codigo, No mostraba los productos que solo contenian fraccion*/
        $temp = $productos;

        $select = 'unidades.nombre_unidad, unidades_has_producto.*';
        $from = "unidades";
        $join = array('unidades_has_producto');
        $campos_join = array('unidades_has_producto.id_unidad=unidades.id_unidad');
        $order = "orden desc";

        for ($i = 0; $i < count($temp); $i++) {
            $where = array('unidades_has_producto.producto_id' => $temp[$i]['producto_id']);
            $buscar = $this->unidades_model->traer_by($select, $from, $join, $campos_join, $where, false, $order, "RESULT_ARRAY");
            if (isset($temp[$i]['fraccion']))
                if ($temp[$i]['fraccion'] == "") $temp[$i]['fraccion'] = "0";
            if (!empty($buscar)) {
                if ($buscar[0]['orden'] > 1) {

                    $temp[$i]['nombre_fraccion'] = $buscar[0]['nombre_unidad'];
                } else {
                    $temp[$i]['nombre_fraccion'] = "";
                }
            }
        }

        return $temp;


    }

    function _sinDetalles($productos)
    {
        $cantidad = 0;
        $fraccion = 0;
        $cantidad_minima = 0;
        $temp = array();
        for ($i = 0; $i < count($productos); $i++) {
            if ($i > 0) {
                if ($productos[$i]['producto_id'] != $productos[$i - 1]['producto_id']) {
                    $cantidad_maxima = $this->unidades_model->get_cantidad_fraccion($productos[$i - 1]['producto_id'], $cantidad_minima);
                    $productos[$i - 1]['cantidad'] = $cantidad_maxima['cantidad'];
                    $productos[$i - 1]['fraccion'] = $cantidad_maxima['fraccion'];
                    $temp[] = $productos[$i - 1];

                    $cantidad_minima = $this->unidades_model->convert_minimo_um(
                        $productos[$i]['producto_id'], $productos[$i]['cantidad'], $productos[$i]['fraccion']
                    );

                    if ($i == count($productos) - 1) {
                        $cantidad_maxima = $this->unidades_model->get_cantidad_fraccion($productos[$i]['producto_id'], $cantidad_minima);
                        $productos[$i]['cantidad'] = $cantidad_maxima['cantidad'];
                        $productos[$i]['fraccion'] = $cantidad_maxima['fraccion'];
                        $temp[] = $productos[$i];
                    }
                } else {
                    $cantidad_minima += $this->unidades_model->convert_minimo_um(
                        $productos[$i]['producto_id'], $productos[$i]['cantidad'], $productos[$i]['fraccion']
                    );
                    if ($i == count($productos) - 1) {
                        $cantidad_maxima = $this->unidades_model->get_cantidad_fraccion($productos[$i]['producto_id'], $cantidad_minima);
                        $productos[$i]['cantidad'] = $cantidad_maxima['cantidad'];
                        $productos[$i]['fraccion'] = $cantidad_maxima['fraccion'];
                        $temp[] = $productos[$i];
                    }
                }
            } else {
                if (count($productos) == 1) {
                    $temp[] = $productos[$i];
                } else {
                    $cantidad_minima = $this->unidades_model->convert_minimo_um(
                        $productos[$i]['producto_id'], $productos[$i]['cantidad'], $productos[$i]['fraccion']
                    );
                }
            }
        }

        return $temp;


    }

    //Preparo el flashdata inicial y se lo asigno al $data.
    // Nota: esto debe ir al principio de los controllers para no sobrescribir lo que se agrega despues
    function _prepareFlashData()
    {
        $data = array();

        if ($this->session->flashdata('success') != FALSE) {
            $data['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data['error'] = $this->session->flashdata('error');
        }

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        return $data;
    }

    function index($action = '')
    {
        switch ($action) {
            case 'excel': {
                $data = $this->_prepareFlashData();
                $data["lstProducto"] = $this->producto_model->get_all_productos();
                $data['columnas'] = $this->columnas;
                echo $this->load->view('menu/producto/producto_list_excel', $data, true);
                break;
            }
            default: {
                $data = $this->_prepareFlashData();

                $data["lstProducto"] = $this->producto_model->get_all_productos();
                $data['columnas'] = $this->columnas;
                //$data["lstProducto"] = $this->_getUnidades($data['lstProducto']);

                $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/producto', $data, true);

                if ($this->input->is_ajax_request())
                    echo $dataCuerpo['cuerpo'];
                else
                    $this->load->view('menu/template', $dataCuerpo);
            }
        }
    }

    function stock($local = 0, $detalle = 0, $unidadMinima = 0)
    {
        $data = $this->_prepareFlashData();

        if ($local == 0)
            $local = false;

        $data["lstProducto"] = $this->producto_model->get_stock_productos($local);
        if ($local == false && $detalle == 0) {
            $data["lstProducto"] = $this->_sinDetalles($data["lstProducto"]);
        }
        $data['columnas'] = $this->columnas;
        $data['local_selected'] = $local;
        $data['detalle_checked'] = $detalle;
        $data['unidadMinima'] = $unidadMinima;

        $data["lstProducto"] = $this->_getUnidades($data['lstProducto'], false);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/stock', $data, true);

        if ($this->input->is_ajax_request())
            echo $dataCuerpo['cuerpo'];
        else
            $this->load->view('menu/template', $dataCuerpo);


    }

    function getbylocal()
    {
        $local = $this->input->post('local');
        $stock = $this->input->post('stock');

        if ($stock == '1')
            $this->stock($local);
        else
            $this->index($local);
    }

    function ver_serie($id, $local, $read_only = false)
    {
        $this->load->model('producto_serie/producto_serie_model');

        $data['producto'] = $this->producto_model->getProductoIdLocal($id, $local);
        $data['series'] = $this->producto_serie_model->get_by(array('producto_id' => $id, 'local_id' => $local));
        $data["read_only"] = $read_only;

        $this->load->view('menu/producto/series', $data);
    }

    function validar_nombre()
    {
        if ($this->input->is_ajax_request()) {
            $nombre = $this->input->post('nombre');

            $select = 'producto_nombre';
            $from = "producto";
            $where = array(
                'producto_nombre' => $nombre
            );
            $result = $this->producto_model->traer_by($select, $from, false, false, false, $where, false, false, "ROW_ARRAY");

            if (isset($result['producto_nombre'])) {
                $data['encontro'] = true;

            } else {
                $data['encontro'] = false;
            }

            echo json_encode($data);
        }
    }

    function validar_codigo_interno()
    {
        if ($this->input->is_ajax_request()) {
            $producto_codigo_interno = $this->input->post('producto_codigo_interno');

            $select = 'producto_codigo_interno';
            $from = "producto";
            $where = array(
                'producto_codigo_interno' => $producto_codigo_interno
            );
            $result = $this->producto_model->traer_by($select, $from, false, false, false, $where, false, false, "ROW_ARRAY");
            $data = array();
            if (isset($result['producto_codigo_interno'])) {
                $data['encontro'] = true;

            } else {
                $data['encontro'] = false;
            }

            echo json_encode($data);
        }
    }

    function update_series()
    {

        $data = $this->input->post("data");
        $new = $this->input->post("new_data");
        $this->load->model('producto_serie/producto_serie_model');
        $this->producto_serie_model->update(json_decode($data));

        foreach (json_decode($new) as $n)
            $this->producto_serie_model->insertar($n);

        $json['success'] = 'Se han actualizados los numeros de series';

        echo json_encode($json);
    }

    function agregar($id = FALSE)
    {

        $data["marcas"] = $this->marcas_model->get_marcas();
        $data["lineas"] = $this->lineas_model->get_lineas();
        $data["familias"] = $this->familias_model->get_familias();
        $data["grupos"] = $this->grupos_model->get_grupos();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["unidades"] = $this->unidades_model->get_unidades();
        $data["precios"] = $this->precios_model->get_all_by('mostrar_precio', 1, array('campo' => 'orden', 'tipo' => 'ASC'));
        $data['columnas'] = $this->columnas;
        $data['duplicar'] = $this->input->post('duplicar');
        $data['costos_unitario'] = $id != FALSE ? $this->producto_costo_unitario_model->get_costos($id) : $this->producto_costo_unitario_model->get_costos();

        $data["monedas"] = $this->monedas_model->get_all();
        // var_dump($data['columnas']);
        $data['precios_producto'] = array();
        $data['operaciones'] = TRUE;

        if ($id != FALSE) {


            $verificar_costo = $this->detalle_ingreso_model->traer_by($id, "ROW_ARRAY");
            if (count($verificar_costo) > 0) {

                $data['costo'] = $verificar_costo['precio'] / $verificar_costo['unidades'];

                $select = '*';
                $from = "unidades_has_producto";
                $join = array('unidades');
                $campos_join = array('unidades.id_unidad=unidades_has_producto.id_unidad');
                $where = array('producto_id' => $id);
                $group = "producto_id";
                $order = "orden";

                $data['unidad_minima'] = $this->unidades_model->traer_by($select, $from, $join, $campos_join, $where, $group, $order, "ROW_ARRAY");


            }

            $orden_max = $this->db->select_max('orden', 'orden')
                ->where('producto_id', $id)->get('unidades_has_producto')->row();

            $this->db->select('unidades.nombre_unidad as um_nombre');
            $this->db->from('unidades_has_producto');
            $this->db->join('unidades', 'unidades_has_producto.id_unidad = unidades.id_unidad');
            $this->db->where('producto_id', $id);
            $this->db->where('orden', $orden_max->orden);
            $unidad = $this->db->get()->row();

            $data['um_minimo'] = isset($unidad->um_nombre) ? $unidad->um_nombre : '';

            if ($this->input->post('duplicar') != 1) {

                $data['images'] = $this->get_fotos($id);
            }


            $data['producto'] = $this->producto_model->get_by_id($id);
            $data['operaciones'] = $this->producto_model->check_operaciones(array('producto_id' => $id));

            $data['unidades_producto'] = $this->unidades_model->get_by_producto($id);

            $countunidad = 0;

            $duplicar = $this->input->post('duplicar');
            if (!empty($duplicar)) {
                $data['duplicar'] = 1;
            }
            foreach ($data['unidades_producto'] as $unidad) {

                $precios = $this->precios_model->get_by_unidad_and_producto($id, $unidad['id_unidad']);
                $countprecio = 0;

                if (sizeof($precios) > 0) {
                    foreach ($precios as $precio) {

                        $preciodata[$countprecio] = $precio;

                        $countprecio++;
                    }
                    $data['precios_producto'][$countunidad] = $preciodata;
                }

                $countunidad++;
            }
            //var_dump( $data['precios_producto']);
        }

        $this->load->view('menu/producto/form', $data);


    }

    function eliminarimg()
    {
        $producto = $this->input->post('producto_id');
        $nombre = $this->input->post('nombre');
        $dir = './uploads/' . $producto . '/' . $nombre;
        $borrar = unlink($dir);

        if ($borrar != false) {
            $json['success'] = "Se ha eliminado exitosamente";
        } else {
            $json['error'] = "Ha ocurrido un error al eliminar la imagen";
        }

        echo json_encode($json);


    }


    function ver_imagen($id = false)
    {
        if ($id != FALSE) {
            $data['producto'] = $this->producto_model->get_by_id($id);
            $data['images'] = $this->get_fotos($id);
            $this->load->view('menu/producto/ver_imagen', $data);
            //var_dump( $data['precios_producto']);
        }


    }

    function borrarimagen()
    {
        $id = $this->input->get('id');
        $ruta_imagen = "uploads/" . $id . "/foto" . $id . ".jpg";
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }

        echo 'success';
    }

    function verunidades($id = FALSE)
    {

        $data["marcas"] = $this->marcas_model->get_marcas();
        $data["lineas"] = $this->lineas_model->get_lineas();
        $data["familias"] = $this->familias_model->get_familias();
        $data["grupos"] = $this->grupos_model->get_grupos();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["unidades"] = $this->unidades_model->get_unidades();
        $data["precios"] = $this->precios_model->get_all_by('mostrar_precio', 1, array('campo' => 'orden', 'tipo' => 'ASC'));
        $data['columnas'] = $this->columnas;

        // var_dump($data['columnas']);
        $data['precios_producto'] = array();
        if ($id != FALSE) {


            $verificar_costo = $this->detalle_ingreso_model->traer_by($id, "ROW_ARRAY");
            if (count($verificar_costo) > 0) {

                $data['costo'] = $verificar_costo['precio'] / $verificar_costo['unidades'];

                $select = '*';
                $from = "unidades_has_producto";
                $join = array('unidades');
                $campos_join = array('unidades.id_unidad=unidades_has_producto.id_unidad');
                $where = array('producto_id' => $id);
                $group = "producto_id";
                $order = "orden";

                $data['unidad_minima'] = $this->unidades_model->traer_by($select, $from, $join, $campos_join, $where, $group, $order, "ROW_ARRAY");

            }


            $data['producto'] = $this->producto_model->get_by_id($id);
            $data['unidades_producto'] = $this->unidades_model->get_by_producto($id);
            $data['precios_producto'] = array();
            $countunidad = 0;

            $duplicar = $this->input->post('duplicar');
            if (!empty($duplicar)) {
                $data['duplicar'] = 1;
            }
            foreach ($data['unidades_producto'] as $unidad) {

                $precios = $this->precios_model->get_by_unidad_and_producto($id, $unidad['id_unidad']);
                $countprecio = 0;

                if (sizeof($precios) > 0) {
                    foreach ($precios as $precio) {

                        $preciodata[$countprecio] = $precio;

                        $countprecio++;
                    }
                    $data['precios_producto'][$countunidad] = $preciodata;
                }

                $countunidad++;
            }
            //var_dump( $data['precios_producto']);
        }

        $this->load->view('menu/producto/formunidades', $data);


    }


    function registrar()
    {

        $this->load->helper('form');
        $this->load->helper('date');
        $this->load->helper('url');
        $this->load->library('upload');

        $id = $this->input->post('id');
        $marca = $this->input->post('producto_marca');
        $linea = $this->input->post('producto_linea');
        $modelo = $this->input->post('producto_modelo');
        $familia = $this->input->post('producto_familia');
        $grupo = $this->input->post('produto_grupo');
        $proveedor = $this->input->post('producto_proveedor');
        $afectacion_impuesto = $this->input->post('afectacion_impuesto');
        $impuesto = $this->input->post('producto_impuesto');
        $incluir_precio = $this->input->post('incluir_precio');
        $cualidad = $this->input->post('producto_cualidad');
        $codigo_barra = $this->input->post('producto_codigo_barra');
        $descripcion = $this->input->post('producto_descripcion');
        $producto_codigo_interno = $this->input->post('producto_codigo_interno');
        $producto_vencimiento = $this->input->post('producto_vencimiento');
		$valor_importe = $this->input->post('valor_importe');
        $producto_nom_original = $this->input->post('producto_nombre_unico');

        if ($codigo_barra != "") {
            if (empty($id)) {
                $val = $this->db->get_where('producto', array(
                    'producto_codigo_barra' => $codigo_barra,
                    'producto_estatus' => 1
                ))->result();
            } else {
                $val = $this->db->get_where('producto', array(
                    'producto_codigo_barra' => $codigo_barra,
                    'producto_estatus' => 1,
                    'producto_id !=' => $id
                ))->result();
            }

            if (sizeof($val) > 0) {
                $json['error'] = 'El codigo de barra ya existe';
                echo json_encode($json);
                return false;
            }
        }

        $producto = array(
            'producto_codigo_barra' => !empty($codigo_barra) ? $codigo_barra : null,
            'producto_nombre' => $this->input->post('producto_nombre'),
            'producto_descripcion' => !empty($descripcion) ? $descripcion : null,
            'producto_marca' => !empty($marca) ? $marca : null,
            'producto_linea' => !empty($linea) ? $linea : null,
            'producto_familia' => !empty($familia) ? $familia : null,
            'produto_grupo' => !empty($grupo) ? $grupo : null,
            'producto_proveedor' => !empty($proveedor) ? $proveedor : null,
            'producto_stockminimo' => $this->input->post('producto_stockminimo') != "" ? $this->input->post('producto_stockminimo') : 0,
            'producto_afectacion_impuesto' => !empty($afectacion_impuesto) ? $afectacion_impuesto : 1,
            'producto_impuesto' => !empty($impuesto) ? $impuesto : null,
            'producto_largo' => $this->input->post('producto_largo'),
            'producto_ancho' => $this->input->post('producto_ancho'),
            'producto_alto' => $this->input->post('producto_alto'),
            'producto_peso' => $this->input->post('producto_peso'),
            'producto_nota' => $this->input->post('producto_nota'),
            'producto_cualidad' => !empty($cualidad) ? $cualidad : null,
            'producto_estado' => $this->input->post('estado'),
            'producto_costo_unitario' => $this->input->post('costo_unitario') ? $this->input->post('costo_unitario') : $this->input->post('calculado_costo_unitario'),
            'producto_modelo' => !empty($modelo) ? $modelo : null,
            'contable_costo' => $this->input->post('contable_costo') ? $this->input->post('contable_costo') : 0.00,
            'cu_moneda_contable' => $this->input->post('cu_moneda_contable') ? $this->input->post('cu_moneda_contable') : $this->input->post('moneda_id_calculo'),
            'cu_moneda' => $this->input->post('cu_moneda') ? $this->input->post('cu_moneda') : $this->input->post('moneda_id_calculo'),
            'tasa_convert' => $this->input->post('tasa_convert') ? $this->input->post('tasa_convert') : $this->input->post('tasa_id'),
            'tasa_convert_contable' => $this->input->post('tasa_convert_contable') ? $this->input->post('tasa_convert_contable') : $this->input->post('tasa_id'),
            'producto_titulo_imagen' => $this->input->post('titulo_imagen'),
            'producto_codigo_interno' => $producto_codigo_interno,
            'producto_descripcion_img' => $this->input->post('descripcion_imagen'),
            'producto_vencimiento' => $producto_vencimiento != null ? date('Y-m-d', strtotime($producto_vencimiento)) : null,
            'producto_nombre_original' => $producto_nom_original
        );


        $medidas = $this->input->post('medida');
        $unidades = $this->input->post('unidad');

        $cod = getCodigo();
        if (empty($id)) {

            $rs = $this->producto_model->insertar($producto, $medidas, $unidades);
            $id = $rs;
            if ($rs) {
                //GENERO EL CODIGO INTERNO SI NO LO ESTAN USANDO
                if ($cod == "INTERNO")
                    $producto['producto_codigo_interno'] = !empty($producto_codigo_interno) ? $producto_codigo_interno : $this->producto_model->calcCodigo($rs);
                if ($cod == "AUTO")
                    $producto['producto_codigo_interno'] = $this->producto_model->calcCodigo($rs);

                $up = $this->producto_model->actualizar_producto(array('producto_id' => $rs), array('producto_codigo_interno' => $producto['producto_codigo_interno']));
            }
            $json['producto_id'] = $id;
            $json['id'] = $producto['producto_codigo_interno'];
            $json['nombre'] = $producto['producto_nombre'];
			$json['impuesto'] = $valor_importe;
        } else {
            $producto['producto_id'] = $id;

            if ($cod == "INTERNO")
                $producto['producto_codigo_interno'] = !empty($producto_codigo_interno) ? $producto_codigo_interno : $this->producto_model->calcCodigo($id);
            if ($cod == "AUTO") {
                $producto['producto_codigo_interno'] = $this->producto_model->calcCodigo($id);
            }

            $rs = $this->producto_model->update($producto, $medidas, $unidades);
        }

        if ($rs) {
            if (!empty($_FILES) and $_FILES['userfile']['size'] != '0') {

                $files = $_FILES;
                $contador = 1;
                $mayor = 0;

                $directorio = './uploads/' . $id . '/';

                if (is_dir($directorio)) {
                    $arreglo_img = scandir($directorio);
                    natsort($arreglo_img);
                    $mayor = array_pop($arreglo_img);
                    $mayor = substr($mayor, 0, -4);
                } else {
                    $arreglo_img[0] = ".";
                }
                $sumando = 1;
                for ($j = 0; $j < count($files['userfile']['name']); $j++) {

                    if ($files['userfile']['name'][$j] != "") {

                        if ($arreglo_img[0] == ".") {
                            $contador = $mayor + ($sumando);
                            $sumando++;
                        }
                        $_FILES ['userfile'] ['name'] = $files ['userfile'] ['name'][$j];
                        $_FILES ['userfile'] ['type'] = $files ['userfile'] ['type'][$j];
                        $_FILES ['userfile'] ['tmp_name'] = $files ['userfile'] ['tmp_name'][$j];
                        $_FILES ['userfile'] ['error'] = $files ['userfile'] ['error'][$j];
                        $_FILES ['userfile'] ['size'] = $files ['userfile'] ['size'][$j];

                        $size = getimagesize($_FILES ['userfile'] ['tmp_name']);

                        switch ($size['mime']) {
                            case "image/jpeg":
                                $extension = "jpg";
                                break;
                            case "image/png":
                                $extension = "png";
                                break;
                            case "image/bmp":
                                $extension = "bmp";
                                break;
                        }

                        $this->upload->initialize($this->set_upload_options($id, $contador, $extension));
                        $this->upload->do_upload();
                        $contador++;
                    } else {

                    }
                }
            }

            $json['success'] = 'El producto se ha guardado de forma exitosa, por favor espere';
        } else {
            $json['error'] = $this->producto_model->getError();
            $this->session->set_flashdata('error', 'Ocurrió un error al guardar el producto');
        }


        echo json_encode($json);

    }

    function get_fotos($id)
    {
        $result = array();
        $dir = './uploads/' . $id . '/';
        if (!is_dir($dir)) return array();
        $temp = scandir($dir);
        foreach ($temp as $img) {
            if (is_file($dir . $img))
                $result[] = $img;
        }
        natsort($result);

        return $result;

    }

    function set_upload_options($id, $contador, $extension)
    {
        // upload an image options
        $this->load->helper('path');
        $dir = './uploads/' . $id . '/';

        if (!is_dir('./uploads/')) {
            mkdir('./uploads/', 0755);
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0755);
        }
        $config = array();
        $config ['upload_path'] = $dir;
        //$config ['file_path'] = './prueba/';
        $config ['allowed_types'] = $extension;
        $config ['max_size'] = '0';
        $config ['overwrite'] = TRUE;
        $config ['file_name'] = $contador;

        return $config;
    }

    function eliminar()
    {
        $id = $this->input->post("id");
        $product = $this->producto_model->get_by_id($id);
        $nombre = $product['producto_nombre'];
        $operaciones = $this->producto_model->check_operaciones(array('producto_id' => $id));
        if ($operaciones == TRUE) {

            $producto = array(
                'producto_id' => $id,
                'producto_nombre' => $nombre . time(),
                'producto_estatus' => 0
            );

            $data['resultado'] = $this->producto_model->delete($producto);

            if ($data['resultado'] != FALSE) {
                if (!empty($id)) {
                    $ruta_imagen = "uploads/" . $id . "/foto" . $id . ".jpg";
                    if (file_exists($ruta_imagen)) {
                        unlink($ruta_imagen);
                    }
                }
                $json['success'] = 'Se ha eliminado exitosamente';
            } else {
                $json['error'] = 'ha ocurrido un error al eliminar el producto';
            }
        } else {
            $json['error'] = 'Este producto tiene operaciones asociadas y no puede ser eliminado';
        }

        echo json_encode($json);
    }

    function uproducto_modelate()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('producto_id', true);
            $producto = array(
                'var_producto_nombre' => $this->input->post('nombre_udp', true),
                'dec_producto_cantidad' => $this->input->post('stock_udp', true),
                'dec_producto_preciocompra' => $this->input->post('precio_comp_udp', true),
                'dec_producto_precioventa' => $this->input->post('precio_vent_udp', true),
                'dec_producto_utilidad' => $this->input->post('utilidad_udp', true),
                'var_producto_descripcion' => $this->input->post('desc_udp', true),
                'int_producto_unidmed' => $this->input->post('cboUnidMed_udp', true),
                'nCatCodigo' => $this->input->post('cboCategoria_udp', true),
                'var_producto_marca' => $this->input->post('cboTipoTelf_udp', true),
                'var_producto_codproveedor' => $this->input->post('codprodprov_udp', true),
                'dec_producto_stockminimo' => $this->input->post('stockmin_udp', true),
                'var_producto_estado' => '1'
            );
            $rs = $this->producto_model->uproducto_modelate($id, $producto);
            if ($rs) {
                echo "actualizo";
            } else {
                echo "no actualizo";
            }
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function buscar_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $producto_model = $this->producto_model->buscar_id($id);
            echo json_encode($producto_model);
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function autocomplete_marca()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->producto_model->autocomplete_marca($this->input->get('term', true)));
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function editcolumnas()
    {
        $data['columnas'] = $this->columnas;
        $this->load->view('menu/producto/columnas', $data);
    }

    function exportar_precios_excel()
    {
        $productos = $this->db
            ->join('marcas', 'marcas.id_marca=producto.producto_marca', 'left')
            ->join('lineas', 'lineas.id_linea=producto.producto_linea', 'left')
            ->join('familia', 'familia.id_familia=producto.producto_familia', 'left')
            ->join('grupos', 'grupos.id_grupo=producto.produto_grupo', 'left')
            ->get_where('producto', array('producto_estatus' => 1))->result();
        foreach ($productos as $producto) {
            $producto->precios = $this->db->select('
                unidades.*, unidades_has_precio.precio as precio, unidades_has_producto.unidades as unidades')
                ->from('unidades_has_precio')
                ->join('unidades', 'unidades.id_unidad=unidades_has_precio.id_unidad')
                ->join('unidades_has_producto', 'unidades_has_producto.id_unidad=unidades_has_precio.id_unidad')
                ->where('id_producto', $producto->producto_id)
                ->where('id_precio', 3)
                ->group_by('unidades.id_unidad')
                ->order_by('unidades_has_producto.unidades', 'DESC')
                ->get()->result();
        }

        $data['productos'] = $productos;
        echo $this->load->view('menu/producto/precios_excel', $data, true);
    }

    function listaprecios()
    {
        $data['locales'] = $this->local_model->get_all();
        $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
        $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
        $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
        $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
        $data['proveedores'] = $this->db->get_where('proveedor', array('proveedor_status' => 1))->result();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/listaprecios', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function listaprecios_list()
    {
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data['lstProducto'] = $this->producto_model->get_producto_lista_precios(array(
            'filter' => $this->input->post('filter'),
            'limit' => $this->input->post('limit'),
            'marca_id' => $this->input->post('marca_id'),
            'grupo_id' => $this->input->post('grupo_id'),
            'familia_id' => $this->input->post('familia_id'),
            'linea_id' => $this->input->post('linea_id'),
            'proveedor_id' => $this->input->post('proveedor_id'),
            'con_stock' => $this->input->post('con_stock')
        ));
        $this->load->view('menu/producto/listaprecios_list', $data);
    }

    function listaprecios_list_detalles()
    {
        $ids = json_decode($this->input->post('ids'));
        $stock = $this->input->post('stock');
        $data['detalles'] = $this->producto_model->get_producto_lista_precios_detalles($ids, $stock);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function preciosporproducto()
    {
        $producto = $this->input->post('producto');
        $precio = $this->input->post('precio');

        if ($this->input->is_ajax_request()) {
            echo json_encode($this->precios_model->get_by_precio_and_producto($producto, $precio));
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function guardarcolumnas()
    {
        $columnas_id = $this->input->post('columna_id');

        $result = $this->columnas_model->insert($columnas_id);

        if ($result != FALSE) {

            $json['success'] = 'Se ha guardado exitosamente';

        } else {

            $json['error'] = 'ha ocurrido un error al guardar';
        }

        echo json_encode($json);

    }

    function validar_codigo_de_barra()
    {

        if ($this->input->is_ajax_request()) {
            $codigo = $this->input->post('codigo');
            $id_cliente = $this->input->post('id_cliente');

            if ($id_cliente != "") {
                $data['precios_cliente'] = $this->cliente_model->get_by('id_cliente', $id_cliente);
            }
            $data["precios_normal"] = $this->precios_model->get_precios();


            $select = '*';
            $from = "producto";
            $where = array(
                'producto_codigo_barra' => $codigo,
                'producto_estado' => 1,
                'producto_estatus' => 1
            );
            $result = $this->producto_model->traer_by($select, $from, false, false, false, $where, false, false, "ROW_ARRAY");

            if (isset($result['producto_id'])) {
                //var_dump($result);
                $data['success'] = true;
                $producto = $result['producto_id'];

                $data['producto'] = $producto;

                $local = $this->session->userdata('id_local');

                $existencia = $this->inventario_model->get_by_id_row($producto, $local);

                $unidades = $this->unidades_model->get_by_producto($producto);

                $data['unidad_maxima'] = "";
                $data['existencia_unidad'] = 0;
                $data['maxima_unidades'] = 0;
                $data['unidad_minima'] = "";
                $data['existencia_fraccion'] = 0;

                //  var_dump($unidades);
                if (sizeof($unidades) > 0) {
                    $data['unidad_maxima'] = $unidades[0]['nombre_unidad'];
                    $data['maxima_unidades'] = $unidades[0]['unidades'];
                    $data['unidad_minima'] = $unidades[sizeof($unidades) - 1]['nombre_unidad'];

                    if (sizeof($existencia) > 1) {
                        $data['existencia_fraccion'] = $existencia['fraccion'];
                        $data['existencia_unidad'] = $existencia['cantidad'];

                    }
                    $data['nombre'] = $unidades[0]['producto_nombre'];
                    $data['cualidad'] = $unidades[0]['producto_cualidad'];
                }


            } else {
                $data = false;
            }

            echo json_encode($data);
        }

    }


    function pdf($condicion)
    {
        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        $result['lstProducto'] = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'));
        $result['precios'] = $this->precios_model->get_precios();
        $result['productos'] = $this->unidades_has_precio_model->get_precio_has_producto();


        $html = $this->load->view('menu/reportes/pdfListaDePrecios', $result, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("Lista de Precios.pdf");
        $pdf->Output($nombre_archivo, 'D');

    }

    function pdf_stock($local = 0, $detalle = 0, $unidadMinima = 0)
    {

        $this->load->library('mpdf53/mpdf');

        if ($local == 0)
            $local = false;

        $data["lstProducto"] = $this->producto_model->get_stock_productos($local);
        if ($local == false && $detalle == 0) {
            $data["lstProducto"] = $this->_sinDetalles($data["lstProducto"]);
        }
        $data['columnas'] = $this->columnas;
        $data["lstProducto"] = $this->_getUnidades($data['lstProducto'], false);
        $data["local"] = $this->local_model->get_by('int_local_id', $local);
        $data['local_selected'] = $local;
        $data['detalle_checked'] = $detalle;
        $data['unidadMinima'] = $unidadMinima;

        $mpdf = new mPDF('utf-8', 'A4-L');
        $condicion = null;

        $mpdf->WriteHTML($this->load->view('menu/producto/reporte_stock', $data, true));
        $mpdf->Output("stock.pdf", "I");


    }

    function excel($condicion)
    {


        $result['lstProducto'] = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'));

        $result['precios'] = $this->precios_model->get_precios();
        $result['productos'] = $this->unidades_has_precio_model->get_precio_has_producto();

        $this->load->view('menu/reportes/excelListaDePrecios', $result);

    }

    function excel_stock($local = 0, $detalle = 0, $unidadMinima = 0)
    {
        if ($local == 0)
            $local = false;

        $data["lstProducto"] = $this->producto_model->get_stock_productos($local);
        if ($local == false && $detalle == 0) {
            $data["lstProducto"] = $this->_sinDetalles($data["lstProducto"]);
        }
        $data['columnas'] = $this->columnas;
        $data["lstProducto"] = $this->_getUnidades($data['lstProducto'], false);
        $data["local"] = $this->local_model->get_by('int_local_id', $local);
        $data['local_selected'] = $local;
        $data['detalle_checked'] = $detalle;
        $data['unidadMinima'] = $unidadMinima;
        echo $this->load->view('menu/producto/reporte_stock_excel', $data, true);
    }

}
