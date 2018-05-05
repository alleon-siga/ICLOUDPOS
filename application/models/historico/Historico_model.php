<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class historico_model extends CI_Model
{

    private $table = 'movimiento_historico';

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('unidades/unidades_model');
    }

    public function set_historico($data = array(), $fecha = 0)
    {
        $values = $data;

        //fraccion hasta ahora la esta usando en caso de AJUSTE
        $fraccion = isset($values['fraccion']) ? $values['fraccion'] : 0;
        $fraccion = $fraccion != "" || $fraccion < 0 ? $fraccion : 0;

        $values['date'] = $fecha == 0 ? date("Y-m-d H:i:s") : $fecha;

        //calculo la cantidad antes de realizar la operacion
        $values['old_cantidad'] = $this->old_cantidad($values['producto_id'], $values['local_id']);
        $values['usuario_id'] = $this->session->userdata('nUsuCodigo');


        if ($values['tipo_movimiento'] == "AJUSTE") {
            $values = $this->prepare_ajuste($values);
            if($values['tipo_operacion']=="ENTRADA"){
                $values['referencia_valor']= "Se agregaron mas productos por ajuste";
            }else{
                $values['referencia_valor']= "Se sacaron alguno(s) producto(s) por ajuste";
            }

        } else {
            //Restructuro la cantida para intentar convertirla en la mayor expresion
            $convert_um = $this->unidades_model->convert_maximo_um($values['producto_id'], $values['cantidad']);
            $values['cantidad'] = $convert_um['cantidad'];
            $values['um_id'] = $convert_um['um_id'];
        }

        unset($values['fraccion']);
        //$this->db->trans_start();
        $this->db->insert($this->table, $values);
        //$this->db->trans_complete();
    }

    private function prepare_ajuste($values)
    {
        $temp = $values;

        $cantidad_minima = $temp['cantidad'];

        if ($temp['old_cantidad'] >= $cantidad_minima) {
            $temp['tipo_operacion'] = "SALIDA";
            $cantidad_minima = $temp['old_cantidad'] - $cantidad_minima;
        } elseif ($temp['old_cantidad'] < $cantidad_minima) {
            $temp['tipo_operacion'] = "ENTRADA";
            $cantidad_minima = $cantidad_minima - $temp['old_cantidad'];
        }

        $convert_um = $this->unidades_model->convert_maximo_um($temp['producto_id'], $cantidad_minima);
        $temp['cantidad'] = $convert_um['cantidad'];
        $temp['um_id'] = $convert_um['um_id'];

        return $temp;
    }

    public function old_cantidad($producto_id, $local_id)
    {
        $almacen = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();

        if ($almacen == NULL)
            return 0;

        return $this->unidades_model->convert_minimo_um($producto_id, $almacen->cantidad, $almacen->fraccion);
    }

    public function get_historico($where)
    {
        $this->db->select('
            movimiento_historico.id as id,
            movimiento_historico.date as date,
            movimiento_historico.tipo_movimiento as tipo,
            movimiento_historico.referencia_id as numero,
            movimiento_historico.referencia_valor as referencia,
            usuario.username as encargado,
            unidades.nombre_unidad as um,
            movimiento_historico.tipo_operacion as operacion,
            movimiento_historico.cantidad,
            movimiento_historico.local_id,
            movimiento_historico.producto_id,
            local.local_nombre as local_nombre,
            producto.producto_codigo_interno');
        $this->db->from('movimiento_historico');
        $this->db->join('usuario', 'movimiento_historico.usuario_id = usuario.nUsuCodigo');
        $this->db->join('unidades', 'movimiento_historico.um_id = unidades.id_unidad');
        $this->db->join('local', 'movimiento_historico.local_id = local.int_local_id');
        $this->db->join('producto', 'movimiento_historico.producto_id = producto.producto_id');
        $this->db->where($where);
        if(isset($where['movimiento_historico.local_id'])){
            $this->db->order_by('movimiento_historico.date', 'DESC');
        }else{
            $this->db->order_by('movimiento_historico.date, movimiento_historico.local_id', 'DESC');
        }
        $result = $this->db->get()->result();

        $temp = $result;
        $n = 0;
        foreach ($result as $row){
            $temp[$n]->importe="";
            switch ($row->tipo){
                case 'VENTA':{

                    $this->db->select(' 
                        venta.total,moneda.simbolo,nombre_condiciones');
                    $this->db->from('venta');
                    $this->db->join('moneda', 'moneda.id_moneda = venta.id_moneda','left');
                    $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones = venta.condicion_pago','left');
                    $this->db->where('venta.venta_id', $row->numero);
                    $venta = $this->db->get()->row();

                    $importe_detalle = $this->db->select_sum('detalle_importe', 'total')
                        ->from('detalle_venta')
                        ->where('id_venta', $row->numero)
                        ->where('id_producto', $row->producto_id)
                        ->get()->row();

                    $temp[$n]->numero = $venta != NULL ? sumCod($row->numero, 6) : 'ID: '.$row->numero;


                    $temp[$n]->importe = $importe_detalle != NULL ? $importe_detalle->total: '';
                    $temp[$n]->simbolo = $venta != NULL ? $venta->simbolo: "";
                    $row->referencia = $venta != NULL ? $row->referencia." al ".$venta->nombre_condiciones: "";
                    break;
                }
                case 'DEVOLUCION':{
                    $this->db->select('
                        venta.total,moneda.simbolo,nombre_condiciones');
                    $this->db->from('venta');
                    $this->db->join('moneda', 'moneda.id_moneda = venta.id_moneda','left');
                    $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones = venta.condicion_pago','left');
                    $this->db->where('venta.venta_id', $row->numero);
                    $venta = $this->db->get()->row();

                    $importe_detalle = $this->db->select_sum('detalle_importe', 'total')
                        ->from('detalle_venta')
                        ->where('id_venta', $row->numero)
                        ->where('id_producto', $row->producto_id)
                        ->get()->row();

                    $temp[$n]->numero = $venta != NULL ? sumCod($row->numero, 6) : 'ID: '.$row->numero;
                    $temp[$n]->importe = $importe_detalle != NULL ? $importe_detalle->total : '';
                    $temp[$n]->simbolo = $venta != NULL ? $venta->simbolo: "";
                    break;
                }
                case 'ANULACION':{
                    if($row->operacion == "ENTRADA") {
                        $this->db->select(' 
                            venta.total,moneda.simbolo,nombre_condiciones');
                        $this->db->from('venta');
                        $this->db->join('moneda', 'moneda.id_moneda = venta.id_moneda','left');
                        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones = venta.condicion_pago','left');
                        $this->db->where('venta.venta_id', $row->numero);
                        $venta = $this->db->get()->row();
                        $temp[$n]->numero = $venta != NULL ? sumCod($row->numero, 6) : 'ID: ' . $row->numero;
                        $temp[$n]->importe = $venta != NULL ? $venta->total: '';
                        $temp[$n]->simbolo = $venta != NULL ? $venta->simbolo: "";
                    }
                    elseif($row->operacion == "SALIDA"){
                        $this->db->select('documento_serie as doc_ser, documento_numero as doc_num, total_ingreso,moneda.simbolo');
                        $this->db->from('ingreso');
                        $this->db->join('moneda', 'moneda.id_moneda = ingreso.id_moneda','left');
                        $this->db->where('id_ingreso', $row->numero);
                        $ingreso = $this->db->get()->row();
                        $temp[$n]->numero = $ingreso != NULL ? $ingreso->doc_ser.' - '.$ingreso->doc_num : 'ID: '.$row->numero;
                        $temp[$n]->importe = $ingreso != NULL ? $ingreso->total_ingreso: '';
                        $temp[$n]->simbolo = $ingreso != NULL ? $ingreso->simbolo: "";
                    }
                    break;
                }
                case 'INGRESO':{
                    $this->db->select('documento_serie as doc_ser, documento_numero as doc_num, total_ingreso,moneda.simbolo,
                    ingreso.pago, ingreso.tipo_ingreso');
                    $this->db->from('ingreso');
                    $this->db->join('moneda', 'moneda.id_moneda = ingreso.id_moneda','left');
                    $this->db->where('id_ingreso', $row->numero);
                    $ingreso = $this->db->get()->row();

                    $importe_detalle = $this->db->select_sum('total_detalle', 'total')
                        ->from('detalleingreso')
                        ->where('id_ingreso', $row->numero)
                        ->where('id_producto', $row->producto_id)
                        ->get()->row();

                    $temp[$n]->importe = $importe_detalle != NULL ? $importe_detalle->total: '';
                    $temp[$n]->numero = $ingreso != NULL ? $ingreso->doc_ser.' - '.$ingreso->doc_num : 'ID: '.$row->numero;
                    $temp[$n]->simbolo = $ingreso != NULL ? $ingreso->simbolo: "";
                    $row->referencia = $ingreso != NULL ? $row->referencia." por ".$ingreso->tipo_ingreso." al ".$ingreso->pago: "";
                    break;
                }
                case 'TRASPASO':{
                    $this->db->select('movimiento_historico.local_id,movimiento_historico.referencia_id,
                    l.local_nombre as localuno, t.local_nombre as localreferencia, producto.producto_id, producto.producto_codigo_interno,
                    producto.producto_nombre');
                    $this->db->from('movimiento_historico');
                    $this->db->join('local as l', 'l.int_local_id = movimiento_historico.local_id','left');
                    $this->db->join('local as t', 't.int_local_id = movimiento_historico.referencia_id','left');
                    $this->db->join('producto', 'producto.producto_id = movimiento_historico.producto_id','left');
                    $this->db->where('movimiento_historico.id', $row->id);
                    $traspaso = $this->db->get()->row();
                    $temp[$n]->localuno = $traspaso->localuno;
                    $temp[$n]->localreferencia = $traspaso->localreferencia;
                    $temp[$n]->producto_nombre = $traspaso->producto_nombre;
                    $temp[$n]->producto_codigo_interno = $traspaso->producto_codigo_interno;
                    $temp[$n]->numero = 'ID: '.$row->id;
                    break;

                }

            }
            $n++;
        }
        return $temp;
    }

    public function get_historico2($where)
    {
        $this->db->select("k.id, p.producto_nombre, k.cantidad, u.nombre_unidad AS um, k.fecha, l.local_nombre, us.nombre, k.io, k.ref_val");
        $this->db->from('kardex AS k');
        $this->db->join('producto AS p', 'p.producto_id = k.producto_id');
        $this->db->join('unidades AS u', 'u.id_unidad = k.unidad_id');
        $this->db->join('local AS l', 'k.local_id = l.int_local_id');
        $this->db->join('usuario AS us', 'k.usuario_id = us.nUsuCodigo');
        $this->db->where('k.tipo = 0 AND k.operacion = 11');
        $this->db->where($where);
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_historico_by($condicion)
    {
        $this->db->select('*');
        $this->db->from('movimiento_historico');
        $this->db->where($condicion);
        $this->db->order_by('date', 'asc');

        $result = $this->db->get()->result();

        return $result;
    }


}
