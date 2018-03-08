<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto_model extends CI_Model
{

    private $tabla = 'producto';
    private $id = 'producto_id';
    private $codigo_barra = 'producto_codigo_barra';
    private $status = 'producto.producto_estatus';
    private $estado = 'producto_estado';
    private $nombre = 'producto_nombre';
    private $descripcion = 'producto_descripcion';
    private $proveedor = 'producto_proveedor';
    private $marca = 'producto_marca';
    private $linea = 'producto_linea';
    private $familia = 'producto_familia';
    private $grupo = 'produto_grupo';
    private $stock_min = 'producto_stockminimo';
    private $impuesto = 'producto_impuesto';

    private $error = '';


    function __construct()
    {
        parent::__construct();


        $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('unidades_has_precio/unidades_has_precio_model');
    }

    public function get_productos($data = array()){
        $query = "
            SELECT 
                l.int_local_id AS local_id,
                l.local_nombre AS local_nombre,
                p.producto_id AS producto_id,
                p.producto_nombre AS producto_nombre,
                p.producto_codigo_interno AS producto_ci,
                p.producto_codigo_barra AS barra,
                pa.cantidad AS cantidad,
                pa.fraccion AS fraccion,
                um_max.id_unidad AS unidad_max_id,
                um_max.nombre_unidad AS unidad_max_nombre,
                um_max.abreviatura AS unidad_max_abr,
                um_min.id_unidad AS unidad_min,
                um_min.nombre_unidad AS unidad_min_nombre,
                um_min.abreviatura AS unidad_min_abr,
                IFNULL((SELECT 
                                unidades
                            FROM
                                unidades_has_producto
                            WHERE
                                unidades_has_producto.producto_id = p.producto_id AND um_max.id_unidad = unidades_has_producto.id_unidad) * pa.cantidad + pa.fraccion,
                        0) AS cantidad_min
            FROM
                producto AS p
                    JOIN
                producto_almacen AS pa ON pa.id_producto = p.producto_id
                    JOIN
                local AS l ON l.int_local_id = pa.id_local
                    JOIN
                unidades AS um_max ON um_max.id_unidad = (SELECT 
                        unidades_has_producto.id_unidad
                    FROM
                        unidades_has_producto
                    WHERE
                        unidades_has_producto.producto_id = p.producto_id
                    ORDER BY unidades_has_producto.orden ASC
                    LIMIT 1)
                    JOIN
                unidades AS um_min ON um_min.id_unidad = (SELECT 
                        unidades_has_producto.id_unidad
                    FROM
                        unidades_has_producto
                    WHERE
                        unidades_has_producto.producto_id = p.producto_id
                    ORDER BY unidades_has_producto.orden DESC
                    LIMIT 1) 

        ";

        if(isset($data['local_id']))
            $query .= " WHERE l.int_local_id = ".$data['local_id'];

        return $this->db->query($query)->result();
    }

    public function check_operaciones($data){

        if(!isset($data['producto_id']))
            return FALSE;

        //compras
        $this->db->where('id_producto', $data['producto_id']);
        if(isset($data['unidad_id']))
            $this->db->where('unidad_medida', $data['unidad_id']);
        $this->db->from('detalleingreso');
        $counter = $this->db->count_all_results();
        if($counter > 0) return FALSE;

        //ventas
        $this->db->where('id_producto', $data['producto_id']);
        if(isset($data['unidad_id']))
            $this->db->where('unidad_medida', $data['unidad_id']);
        $this->db->from('detalle_venta');
        $counter = $this->db->count_all_results();
        if($counter > 0) return FALSE;

        //ajustes
        $this->db->where('producto_id', $data['producto_id']);
        if(isset($data['unidad_id']))
            $this->db->where('unidad_id', $data['unidad_id']);
        $this->db->from('ajuste_detalle');
        $counter = $this->db->count_all_results();
        if($counter > 0) return FALSE;

        return TRUE;
    }

    public function get_productos_list()
    {
        return $this->db->select(
            'producto_id, producto_codigo_interno as codigo, producto_nombre, producto_codigo_barra as barra')
            ->from('producto')
            ->where('producto_estatus', '1')
            ->where('producto_estado', '1')
            ->get()->result();
    }

    public function getCodigo($cod, $id)
    {
        $query = $this->get_where($this->tabla, array($this->id => $id))->row(0);
        if ($cod == "AUTO")
            return $query->producto_id;
        elseif ($cod == "INTERNO")
            return $query->producto_codigo_interno;
    }

    public function hasCodigo($id)
    {
        $query = $this->db->get_where($this->tabla, array('producto_id' => $id))->row();
        if ($query->producto_codigo_interno == "") return FALSE;
        else return TRUE;
    }

    public function calcCodigo($id)
    {
        return sumCod($id, 4);
    }

    public function get_all_producto_almacen($where)
    {
        $this->db->select('producto_almacen.*');
        $this->db->from('producto_almacen');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_total_stock($where)
    {
        $this->db->select('SUM(cantidad) AS suma_cantidad, SUM(fraccion) AS suma_fraccion');
        $this->db->from('producto_almacen');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Esta funcion hay que validarla
    public function updateAlmacenCantidad($data = array())
    {
        $item = $this->db->get_where('producto_almacen', array('id_producto' => $data['id_producto'], 'id_local' => $data['id_local']))->row();
        $data['cantidad'] += $item->cantidad;
        $this->db->where(array('id_producto' => $data['id_producto'], 'id_local' => $data['id_local']));
        $this->db->update('producto_almacen', $data);
    }

    public function getError()
    {
        return $this->error;
    }

    function insertar($pe, $medidas, $unidades)
    {
        $this->load->model('columnas/columnas_model');
        $col = $this->columnas_model->getColumn('producto_modelo');
        //$valor = getValorUnico();

        $prod = $this->get_by('producto_codigo_interno', $pe['producto_codigo_interno'], true);
        $validar = sizeof($prod);
        $valor = "CODIGO_INTERNO";


        if ($validar == 0) {
            $valor = getValorUnico();
            if ($valor == "NOMBRE") {
                $prod = $this->get_by('producto_nombre', $pe['producto_nombre'], true);
                $validar = sizeof($prod);
            } elseif ($valor == "MODELO" && $col->activo == '1') {
                $prod = $this->get_by('producto_modelo', $pe['producto_modelo'], true);
                if ($pe['producto_modelo'] == '')
                    $validar = 0;
                else
                    $validar = sizeof($prod);
            } else
                $validar = -1;

        }



        if ($validar == 0) {
            $this->db->trans_start();

            $moneda_id = $pe['cu_moneda'];
            $moneda_contable_id = $pe['cu_moneda_contable'];
            $tasa = $pe['tasa_convert'];
            $tasa_contable = $pe['tasa_convert_contable'];
            $contable_costo = $pe['contable_costo'];

            unset($pe['cu_moneda']);
            unset($pe['cu_moneda_contable']);
            unset($pe['tasa_convert']);
            unset($pe['tasa_convert_contable']);
            unset($pe['contable_costo']);

            $this->db->insert($this->tabla, $pe);
            $id_producto = $this->db->insert_id();

            //guardo los costos unitarios
            $this->producto_costo_unitario_model->save_costos(array(
                'producto_id' => $id_producto,
                'moneda_id' => $moneda_id,
                'costo' => $pe['producto_costo_unitario'],
                'contable_costo' => $contable_costo,
                'activo' => '1',
                'contable_activo' => $moneda_contable_id
            ), $tasa);

            $countunidad = 0;
            $this->db->where('estatus_precio', 1);
            $this->db->where('mostrar_precio', 1);
            $query = $this->db->get('precios');
            $precios_existentes = $query->result_array();


            $unidad_has_precio = array();
            if ($medidas != false) {
                foreach ($medidas as $medida) {

                    /*notese que dentro del arreglo $unidad_has_producto el indice unidades, pregunto si el contador es igual al ultimo
                    item, que seria en este caso la unidad minima, o si tiene solo una unidad, que tambien seria la unidad minima y maxima
                    a la vez, entonces siempre valido que se coloque 1 como unidad minima*/
                    $unidad_has_producto = array(
                        "id_unidad" => $medidas[$countunidad],
                        "producto_id" => $id_producto,
                        "unidades" => $countunidad == count($unidades) - 1 ? 1 : $unidades[$countunidad],
                        "orden" => $countunidad + 1
                    );

                    $countprecio = 0;

                    $precios_valor = $this->input->post('precio_valor_' . $countunidad);
                    $precios_id = $this->input->post('precio_id_' . $countunidad);


                    foreach ($precios_existentes as $pe) {

                        $unidad_has_precio[$countprecio] = array(
                            "id_precio" => $precios_id[$countprecio],
                            "id_unidad" => $medidas[$countunidad],
                            "id_producto" => $id_producto,
                            "precio" => $precios_valor[$countprecio]

                        );

                        $countprecio++;
                    }
                    $this->db->insert('unidades_has_producto', $unidad_has_producto);
                    $this->db->insert_batch('unidades_has_precio', $unidad_has_precio);

                    $countunidad++;
                }
            }


            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return false;
            else
                return $id_producto;
        } else {
            if ($valor == "NOMBRE")
                $this->error = NOMBRE_EXISTE;
            elseif ($valor == "MODELO" && $col->activo == '1')
                $this->error = MODELO_EXISTE;
            elseif ($valor == "CODIGO_INTERNO")
                $this->error = CODIGO_EXISTE;
            elseif ($validar == -1)
                $this->error = MODELO_ACTIVAR;

            return false;
        }
    }

    function update($producto, $medidas, $unidades)
    {
        $this->load->model('columnas/columnas_model');
        $col = $this->columnas_model->getColumn('producto_modelo');
        $valor = getValorUnico();

        $produc_exite = $this->get_by('producto_codigo_interno', $producto['producto_codigo_interno'], true);

        if ($producto['producto_codigo_interno'] == "") {
            $validar = 0;
        } else {
            $validar = sizeof($produc_exite);
            $valor = "CODIGO_INTERNO";
        }

        if ($validar == 0 or ($validar > 0 and ($produc_exite ['producto_id'] == $producto ['producto_id']))) {
            $valor = getValorUnico();
            if ($valor == "NOMBRE") {

                $produc_exite = $this->get_by('producto_nombre', $producto['producto_nombre'], true);

                if ($producto['producto_nombre'] == "")
                    $validar = 0;
                else
                    $validar = sizeof($produc_exite);

            } elseif ($valor == "MODELO" && $col->activo == '1') {
                $produc_exite = $this->get_by('producto_modelo', $producto['producto_modelo'], true);
                if ($producto['producto_modelo'] == "")
                    $validar = 0;
                else
                    $validar = sizeof($produc_exite);
            } else {
                $validar = -1;
            }
        }


        if ($validar == 0 or ($validar > 0 and ($produc_exite ['producto_id'] == $producto ['producto_id']))) {
            $this->db->trans_start();

            $moneda_id = $producto['cu_moneda'];
            $moneda_contable_id = $producto['cu_moneda_contable'];
            $tasa = $producto['tasa_convert'];
            $tasa_contable = $producto['tasa_convert_contable'];
            $tasa_contable = $producto['tasa_convert_contable'];
            $contable_costo = $producto['contable_costo'];

            unset($producto['cu_moneda']);
            unset($producto['cu_moneda_contable']);
            unset($producto['tasa_convert']);
            unset($producto['tasa_convert_contable']);
            unset($producto['contable_costo']);

            $this->db->where($this->id, $producto['producto_id']);
            $this->db->update($this->tabla, $producto);

            $this->producto_costo_unitario_model->save_costos(array(
                'producto_id' => $producto['producto_id'],
                'moneda_id' => $moneda_id,
                'costo' => $producto['producto_costo_unitario'],
                'contable_costo' => $contable_costo,
                'activo' => '1',
                'contable_activo' => $moneda_contable_id
            ), $tasa);


            $countunidad = 0;
            $this->db->where('estatus_precio', 1);
            $this->db->where('mostrar_precio', 1);
            $query = $this->db->get('precios');
            $preciose = $query->result_array();

            $id_producto = $producto['producto_id'];

            $this->db->where('producto_id', $id_producto);
            $query = $this->db->get('unidades_has_producto');
            $unidadesexistentes = $query->result_array();

            if ($medidas != false) {
                foreach ($medidas as $medida) {


                    if (isset($medidas[$countunidad])) {

                        /*notese que dentro del arreglo $unidad_has_producto el indice unidades, pregunto si el contador es igual al ultimo
                        item, que seria en este caso la unidad minima, o si tiene solo una unidad, que tambien seria la unidad minima y maxima
                        a la vez, entonces siempre valido que se coloque 1 como unidad minima*/
                        $unidad_has_producto = array(
                            "id_unidad" => $medidas[$countunidad],
                            "producto_id" => $id_producto,
                            "unidades" => $countunidad == (count($unidades)) - 1 || count($unidades) == 1 ? 1 : $unidades[$countunidad],
                            "orden" => $countunidad + 1
                        );

                        $this->db->where('id_unidad', $medidas[$countunidad]);
                        $this->db->where('producto_id', $id_producto);
                        $query = $this->db->get('unidades_has_producto');
                        $unidadexiste = $query->num_rows();


                        if ($unidadexiste < 1) {
                            $this->db->insert('unidades_has_producto', $unidad_has_producto);
                        } else {
                            $this->db->where('id_unidad', $medidas[$countunidad]);
                            $this->db->where('producto_id', $id_producto);
                            $this->db->update('unidades_has_producto', $unidad_has_producto);
                        }


                        $countprecio = 0;

                        $precios_valor = $this->input->post('precio_valor_' . $countunidad);
                        $precios_id = $this->input->post('precio_id_' . $countunidad);


                        foreach ($preciose as $pe) {

                            if (isset($precios_id[$countprecio])) {
                                $unidad_has_precio = array(
                                    "id_precio" => $precios_id[$countprecio],
                                    "id_unidad" => $medidas[$countunidad],
                                    "id_producto" => $id_producto,
                                    "precio" => $precios_valor[$countprecio]

                                );


                                $this->db->where('id_precio', $precios_id[$countprecio]);
                                $this->db->where('id_unidad', $medidas[$countunidad]);
                                $this->db->where('id_producto', $id_producto);
                                $query = $this->db->get('unidades_has_precio');
                                $existeprecio = $query->num_rows();
                                if ($existeprecio < 1) {
                                    $this->db->insert('unidades_has_precio', $unidad_has_precio);
                                } else {
                                    $this->db->where('id_precio', $precios_id[$countprecio]);
                                    $this->db->where('id_unidad', $medidas[$countunidad]);
                                    $this->db->where('id_producto', $id_producto);
                                    $this->db->update('unidades_has_precio', $unidad_has_precio);
                                }
                            }
                            $countprecio++;
                        }
                    }

                    $countunidad++;


                }
            }

            foreach ($unidadesexistentes as $ue) {
                $borrarunidad = TRUE;
                $countunidad = 0;
                if ($medidas != false) {
                    foreach ($medidas as $medida) {
                        if (isset($medidas[$countunidad])) {
                            if ($ue['id_unidad'] == $medidas[$countunidad] && $ue['producto_id'] == $id_producto) {
                                $borrarunidad = FALSE;
                            }
                        }
                        $countunidad++;
                    }

                }

                if ($borrarunidad == TRUE or $medidas == false) {
                    $this->db->where('id_unidad', $ue['id_unidad']);
                    $this->db->where('producto_id', $id_producto);
                    $this->db->delete('unidades_has_producto');

                    $this->db->where('id_unidad', $ue['id_unidad']);
                    $this->db->where('id_producto', $id_producto);
                    $this->db->delete('unidades_has_precio');

                }

            }


            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {

            if ($valor == "NOMBRE")
                $this->error = NOMBRE_EXISTE;
            elseif ($valor == "MODELO" && $col->activo == '1')
                $this->error = MODELO_EXISTE;
            elseif ($valor == "CODIGO_INTERNO")
                $this->error = CODIGO_EXISTE;
            /* elseif ($validar == -1)
                 $this->error = MODELO_ACTIVAR;*/

            return false;

        }
    }

    function actualizar_producto($where, $arreglo)
    {

        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update($this->tabla, $arreglo);
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;


    }

    function getProductoIdLocal($id, $local)
    {
        $this->db->select('producto_nombre, producto_id, cantidad');
        $this->db->from('producto');
        $this->db->join('producto_almacen', 'producto.producto_id=producto_almacen.id_producto');
        $this->db->where('producto_id', $id);
        $this->db->where('id_local', $local);
        $this->db->where('producto_estatus', '1');
        $query = $this->db->get();

        if (count($query->row_array()) > 0)
            return $query->row_array();
        else {
            $temp = $this->get_by('producto_id', $id);
            $temp['cantidad'] = 0;
            return $temp;
        }
    }

    function get_by($campo, $valor, $status = false)
    {
        $this->db->where($campo, $valor);
        if ($status)
            $this->db->where('producto_estatus', '1');
        $query = $this->db->get('producto');
        return $query->row_array();
    }

    function delete($producto)
    {
        $this->db->trans_start();
        $this->db->where($this->id, $producto['producto_id']);
        $this->db->update($this->tabla, $producto);
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }


    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $group = false,
                             $order = false, $retorno = false)
    {


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
            $this->db->order_by($order);
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

    function get_by_id($id)
    {
        $query = $this->db->where('producto_id', $id);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $query = $this->db->get('producto');
        return $query->row_array();
    }

    function getUmById($id)
    {
        $this->db->select('nombre_unidad');
        $this->db->from('unidades_has_producto');
        $this->db->join('unidades', 'unidades_has_producto.id_unidad = unidades.id_unidad');
        $this->db->where(array('producto_id' => $id, 'orden' => '1'));
        $query = $this->db->get()->row();
        return $query->nombre_unidad;
    }


    function select_all_producto()
    {
        $this->db->select($this->tabla . '.* ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre,
		  impuestos.nombre_impuesto, impuestos.porcentaje_impuesto');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');

        $where = array($this->status => '1', $this->estado => '1');
        $this->db->where($where);
        $this->db->order_by($this->nombre, 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }


    function productosporlocal_venta($local)
    {
        /*este metodo es creado para el modulo de ventas a modo de optimizacion*/
        $this->db->select(' producto.producto_nombre, producto.producto_id, producto.producto_codigo_interno, producto.producto_descripcion,inventario.id_local');
        $this->db->from($this->tabla);
        //$this->db->join('(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->join('(SELECT inventario.id_local, inventario.id_producto FROM producto_almacen inventario WHERE inventario.id_local=' . $local . '  ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->group_by('producto_id');
        $where = array($this->status => '1', $this->estado => '1');
        $this->db->where($where);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    function get_stock_productos($local = false)
    {
        $query = '
        SELECT
        `producto`.`producto_nombre` AS `nombre`,
        `unidades_has_producto`.`id_unidad`,
        `unidades`.`nombre_unidad`,
        `inventario`.`id_local`,
        `inventario`.`local_nombre`,
        `inventario`.`cantidad`,
        `inventario`.`fraccion`,
        `producto`.*,
        `lineas`.`nombre_linea`,
        `marcas`.`nombre_marca`,
        `familia`.`nombre_familia`,
        `grupos`.`nombre_grupo`,
        `proveedor`.`proveedor_nombre`,
        `impuestos`.`nombre_impuesto`
        FROM
        `producto`
            LEFT JOIN
        `lineas` ON `lineas`.`id_linea` = `producto`.`producto_linea`
            LEFT JOIN
        `marcas` ON `marcas`.`id_marca` = `producto`.`producto_marca`
            LEFT JOIN
        `familia` ON `familia`.`id_familia` = `producto`.`producto_familia`
            LEFT JOIN
        `grupos` ON `grupos`.`id_grupo` = `producto`.`produto_grupo`
            LEFT JOIN
        `proveedor` ON `proveedor`.`id_proveedor` = `producto`.`producto_proveedor`
            LEFT JOIN
        `impuestos` ON `impuestos`.`id_impuesto` = `producto`.`producto_impuesto`
            LEFT JOIN
        `unidades_has_producto` ON `unidades_has_producto`.`producto_id` = `producto`.`producto_id`
            AND `unidades_has_producto`.`orden` = 1
            LEFT JOIN
        `unidades` ON `unidades`.`id_unidad` = `unidades_has_producto`.`id_unidad`
        ';

        if($local != false){
            $query .= '
            JOIN
            (SELECT
                inventario.id_local,
                local.local_nombre,
                inventario.id_producto,
                inventario.cantidad,
                inventario.fraccion
            FROM
                producto_almacen inventario
            JOIN local ON local.int_local_id = inventario.id_local
            WHERE
                inventario.id_local = '.$local.' AND
                inventario.cantidad + inventario.fraccion > 0)
            AS inventario ON `inventario`.`id_producto` = `producto`.`producto_id`
            ';
        }
        else{
            $query .= '
            JOIN
            (SELECT
                inventario.id_local,
                local.local_nombre,
                inventario.id_producto,
                inventario.cantidad,
                inventario.fraccion
            FROM
                producto_almacen inventario
            JOIN local ON local.int_local_id = inventario.id_local
            WHERE
                inventario.cantidad + inventario.fraccion > 0)
            AS inventario ON `inventario`.`id_producto` = `producto`.`producto_id`
            ';
        }

        $query .= 'WHERE
            `producto`.`producto_estatus` = 1
            AND `producto_estado` = 1 ';

        if($local != false)
            $query .= 'GROUP BY `producto_id` ';
        else
            $query .= 'ORDER BY `producto_nombre` ';


        $result = $this->db->query($query);
        return $result->result_array();
    }

    function get_all_by_local($local)
    {
        $this->db->distinct();
        $this->db->select('producto.producto_nombre as nombre, ' . $this->tabla . '.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, inventario.id_local, inventario.cantidad, inventario.fraccion ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        //$this->db->join('(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->join('(SELECT inventario.id_local, inventario.id_producto, inventario.cantidad, inventario.fraccion  FROM producto_almacen inventario WHERE inventario.id_local=' . $local . '  ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'left');
        $this->db->group_by('producto_id');

        $where = array($this->status => '1', $this->estado => '1');
        $this->db->where('inventario.cantidad >', '0');
        $this->db->or_where('inventario.fraccion >', '0');

        $this->db->where($where);

        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    function get_all_productos()
    {
        $this->db->distinct();
        $this->db->select(' producto.producto_nombre as nombre, ' . $this->tabla . '.*,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto,grupos.id_grupo');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->group_by('producto_id');
        $this->db->order_by('producto_id', 'desc');
        $this->db->order_by('nombre_grupo', 'asc');

        $where_in = array('0', '1');
        $where = array(
            $this->status => '1'
        );
        $this->db->where_in($this->estado, $where_in);
        $this->db->where($where);
        $query = $this->db->get();

        return $query->result_array();
    }

    function get_all_by_local_producto($local)
    {
        if ($this->session->userdata('esSuper') == 1){
            $query = $this->db->where('local_status', 1)->get('local')->row();
            $local = $query->int_local_id;
        }

        $this->db->distinct();
        $this->db->select(' producto.producto_nombre as nombre, ' . $this->tabla . '.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, producto_almacen.id_local, producto_almacen.cantidad, producto_almacen.fraccion ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto,grupos.id_grupo,unidades.nombre_unidad as nombre_fraccion');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->join('producto_almacen', 'producto_almacen.id_local = '.$local.' and producto_almacen.id_producto = producto.producto_id', 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'left');
        $this->db->group_by('producto_id');
        $this->db->order_by('producto_id', 'desc');
        $this->db->order_by('nombre_grupo', 'asc');

        $where_in = array('0', '1');
        $where = array(
            $this->status => '1'
        );
        $this->db->where_in($this->estado, $where_in);
        $this->db->where($where);
        $query = $this->db->get();
        $where = array(
            "id_local" => $local
        );

        $result = array();


        foreach ($query->result_array() as $r) {


            $temp = $r;
            $this->db->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad');
            $this->db->order_by('orden', 'asc');
            $temp['embalajes'] = $this->db->get_where('unidades_has_producto', array('producto_id' => $r['producto_id']))->result_array();
            $result[] = $temp;


        }

        return $result;
    }


    function get_producto_lista_precios($data)
    {
        $filter = $data['filter'];
        $limit = $data['limit'];

        $this->db->select(
            "producto_nombre,
            producto_id,
            producto_codigo_interno,
            producto_codigo_barra as barra,
            stock_min,
            descripcion");
        $this->db->from('v_lista_precios');

        if(isset($data['marca_id']) && $data['marca_id'] != 0)
            $this->db->where('marca_id', $data['marca_id']);

        if(isset($data['grupo_id']) && $data['grupo_id'] != 0)
            $this->db->where('grupo_id', $data['grupo_id']);

        if(isset($data['familia_id']) && $data['familia_id'] != 0)
            $this->db->where('familia_id', $data['familia_id']);

        if(isset($data['linea_id']) && $data['linea_id'] != 0)
            $this->db->where('linea_id', $data['linea_id']);

        if(isset($data['proveedor_id']) && $data['proveedor_id'] != 0)
            $this->db->where('proveedor_id', $data['proveedor_id']);

        if(strlen($filter) >= 3){
            $query = "(producto_codigo_interno LIKE '%".$filter."%' ESCAPE '!' OR 
                producto_codigo_barra LIKE '%".$filter."%' ESCAPE '!'";

            //$this->db->like('producto_codigo_interno', $filter);

            $n = 0;
            foreach (preg_split('/[\s,]+/', $filter) as $word) {
                if ($word != "") {
                    if ($n++ == 0)
                        $query .= " OR criterio LIKE '%".$word."%' ESCAPE '!'";
                    else
                        $query .= " AND criterio LIKE '%".$word."%' ESCAPE '!'";
                }
            }
            $query .= ')';
            $this->db->where($query);
        }

        if ($limit != 0)
            $this->db->limit($limit);


        $query = $this->db->get();
        $query_listo = $query->result_array();


        return $query_listo;
    }

    function get_producto_lista_precios_detalles($ids = array(), $stock = 0)
    {
        $result = array();
        foreach ($ids as $id) {
            $temp = array('id' => $id);

            $this->db->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad');
            $this->db->order_by('orden', 'asc');
            $temp['embalajes'] = $this->db->get_where('unidades_has_producto', array('producto_id' => $id))->result_array();


            $where = array(
                'id_producto' => $id
            );
            $temp['stock_por_local'] = $this->get_all_producto_almacen($where);

            $temp['total_stock'] = $this->get_total_stock($where);

            $where = array(
                'id_producto' => $id
            );
            $temp['unidades_y_precios'] = $this->unidades_has_precio_model->precios_por_unidad($where);

            $result[] = $temp;

        }

        return $result;
    }


    function autocomplete_marca($term)
    {
        $this->db->select('var_producto_marca as label');
        $this->db->from('producto');
        $this->db->like('var_producto_marca', $term);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_stock_normal($where)
    {
        $this->db->select('producto.producto_id,producto.producto_codigo_interno, producto.producto_nombre,producto_almacen.*, 
            producto.producto_codigo_barra as barra');
        $this->db->from('producto');
        $this->db->join('producto_almacen', 'producto_almacen.id_producto=producto.producto_id');
        $this->db->where($where);
        $this->db->where('( producto_almacen.cantidad>0 or  producto_almacen.fraccion>0)');
        $this->db->group_by('producto_id');
        $query = $this->db->get();
        return $query->result_array();
    }
}
