<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class credito_cuotas_abono_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }


    public function update($data, $when)
    {
        $this->db->trans_start();

        $this->db->update_batch('credito_cuotas_abono', $data, $when);

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();
    }

    public function insert($data)
    {
        $this->db->trans_start();

        $this->db->insert_batch('credito_cuotas_abono', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();
    }

    public function get_max_fecha($where, $id_cuota)
    {
        $this->db->select('*');
        $this->db->from('credito_cuotas_abono');
        $this->db->where($where);
        $this->db->where('fecha_abono = (SELECT MAX(fecha_abono) AS fecha  FROM  credito_cuotas_abono WHERE credito_cuotas_abono.credito_cuota_id=' . $id_cuota . ')');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function registrar($idCuota, $montodescontar, $metodo_pago, $idVenta, $anticipado, $numero_ope, $banco)
    {


        /*cargo el modelo de credito_cuotas y el de creditos*/
        $this->load->model('credito_cuotas/credito_cuotas_model');
        $this->load->model('credito/credito_model');
        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');

        $venta = $this->db->get_where('venta', array('venta_id' => $idVenta))->row();


        if ($metodo_pago == 4) {
            $banco_selected = $this->db->get_where('banco', array('banco_id' => $banco))->row();
            $cuenta_id = $banco_selected->cuenta_id;
        } else {
            $cuenta_id = $this->cajas_model->get_cuenta_id(array(
                'moneda_id' => $venta->id_moneda,
                'local_id' => $venta->local_id));
        }

        $cuenta_old = $this->cajas_model->get_cuenta($cuenta_id);

        $this->cajas_model->update_saldo($cuenta_id, $montodescontar);

        $this->cajas_mov_model->save_mov(array(
            'caja_desglose_id' => $cuenta_id,
            'usuario_id' => $this->session->userdata('nUsuCodigo'),
            'fecha_mov' => date('Y-m-d H:i:s'),
            'movimiento' => 'INGRESO',
            'operacion' => 'CUOTA',
            'medio_pago' => $metodo_pago,
            'saldo' => $montodescontar,
            'saldo_old' => $cuenta_old->saldo,
            'ref_id' => $idVenta,
            'ref_val' => '',
        ));


        /*declaro la variable $retorno*/
        $retorno = false;

        /*declaro el arreglo por si acaso es un pago anticipado o por si ya se pago el monto total de la cuota*/
        $arreglo_cuota = array();

        /*declaro la variable que va a ir sumando el total restante de toda la venta*/
        $total_restante = 0;

        if ($idCuota != false) {
            $where = array(
                'id_credito_cuota' => $idCuota
            );
        } else {
            $where = array(
                'id_venta' => $idVenta,
                'ispagado' => 0
            );
        }
        /*busco todas las cuotas o una sola cuota, dependiendo de la condicion de arriba*/
        $buscar_monto_cuota = $this->credito_cuotas_model->get_cuotas($where);

        /*pregunto su fue un pago anticipado para cobrarle la comision al cliente*/
        if ($anticipado == true) {
            $where = array(
                'credito_cuotas.id_venta' => $idVenta,
                'ispagado' => 0
            );
            $pagadas = $this->credito_cuotas_model->get_count($where);

            $comision_anticipado = $this->session->userdata('ADELANTO_PAGO_CUOTA') * $pagadas[0]['pagadas'];
        } else {
            $comision_anticipado = 0.00;
        }

        $fecha = date('Y-m-d H:i:s');


        if (count($buscar_monto_cuota) > 0) {
            for ($i = 0; $i < count($buscar_monto_cuota); $i++) {

                $where = array(
                    'credito_cuota_id' => $buscar_monto_cuota[$i]['id_credito_cuota']
                );

                /*busco el ultimo pago para esta cuota*/
                $buscar_ultimo_pago = $this->get_max_fecha($where, $buscar_monto_cuota[$i]['id_credito_cuota']);

                /*declaro monto restane igual a cero por defecto*/
                $monto_restante = $buscar_monto_cuota[$i]['monto'];

                /*pregunto si hay algun pago para esta cuota*/
                if (count($buscar_ultimo_pago) > 0) {

                    /*si $anticipado = true entonces es un pago anticipado, por lo tanto el restante siempre va a ser 0*/
                    if ($anticipado == true) {
                        /*coloco esta cuota con el ultimo monto restante*/
                        $montodescontar = $buscar_ultimo_pago[0]['monto_restante'];
                        $monto_restante = 0;

                    } else {
                        /*hago la resta*/
                        if (($buscar_ultimo_pago[0]['monto_restante'] - $montodescontar) > 0) {
                            $monto_restante = $buscar_ultimo_pago[0]['monto_restante'] - $montodescontar;
                        } else {
                            /*le dejo lo ultimo pendiente, para que no me de negativo*/
                            $montodescontar = $buscar_ultimo_pago[0]['monto_restante'];
                            $monto_restante = 0;
                        }
                    }
                } else {
                    /*aqui entra cuando no hay ningun pago hecho para esta cuota*/

                    /*si es un pago anticipado el monto a descontar es el monto de la cuota completa*/
                    if ($anticipado == true) {

                        $monto_restante = 0;
                        $montodescontar = $buscar_monto_cuota[$i]['monto'];

                    } else {
                        /*si no es un pago anticipado pregunto por el monto de la cuota menos la cantidad que ingrese,
                        para poder restar*/
                        /*hago la resta*/
                        if (($buscar_monto_cuota[$i]['monto'] - $montodescontar) > 0) {
                            $monto_restante = $buscar_monto_cuota[$i]['monto'] - $montodescontar;
                        } else {
                            $montodescontar = $buscar_monto_cuota[$i]['monto'];
                            $monto_restante = 0;
                        }
                    }
                }

                $total_restante += $monto_restante;
                /*lleno el arreglo a insertar*/


                if ($banco == '') {
                    $banco = null;
                }

                if ($numero_ope == '') {
                    $numero_ope = null;
                }

                $arreglo_abono[$i] = array(
                    'credito_cuota_id' => $buscar_monto_cuota[$i]['id_credito_cuota'],
                    'monto_abono' => $montodescontar,
                    'fecha_abono' => $fecha,
                    'tipo_pago' => $metodo_pago,
                    'monto_restante' => $monto_restante,
                    'usuario_pago' => $this->session->userdata('nUsuCodigo'),
                    'banco_id' => $banco,
                    'nro_operacion' => $numero_ope
                );

                /*si $monto_restante ==0 quiere decir que ya esta pagada la cuota completa
                por lo tanto lleno el arreglo para ctualizar a ispagado, en la tabla credito_cuotas*/
                if ($monto_restante == 0) {
                    $arreglo_cuota[$i] = array(
                        'ispagado' => 1,
                        'id_credito_cuota' => $buscar_monto_cuota[$i]['id_credito_cuota'],
                        'ultimo_pago' => $fecha
                    );
                } else {
                    /*para actualizar la ultima fecha insertada para la cuota*/
                    $arreglo_cuota[$i] = array(
                        'id_credito_cuota' => $buscar_monto_cuota[$i]['id_credito_cuota'],
                        'ultimo_pago' => $fecha
                    );
                }
            }

            /*hago el insert en la tabla credito_cuotas_abono*/
            $retorno = $this->insert($arreglo_abono);
            if ($retorno == true and count($arreglo_cuota) > 0) {
                /*si entra aqui, hago el update sobre la tabla credito_cuotas*/
                $this->credito_cuotas_model->update_bach($arreglo_cuota, 'id_credito_cuota');
            }

        }

        /*busco el total acumulado hasta ahora de el credito*/
        $where = array(
            'credito_cuotas.id_venta' => $idVenta
        );
        $suma = $this->get_suma_cuotas($where);

        /*busco si ya todas las cuotas esta pagadas*/
        $where['ispagado'] = 0;
        $pagadas = $this->credito_cuotas_model->get_count($where);


        $data = array(
            'dec_credito_montodebito' => $suma[0]['suma'],
            'pago_anticipado' => $comision_anticipado
        );
        /*si pagadas es menor que 1 quiere decir que ya pago todas las cuotas*/
        if ($pagadas[0]['pagadas'] < 1) {
            $data['var_credito_estado'] = "PagoCancelado";
            $data['fecha_cancelado'] = $fecha;
        }

        $where = array(
            'id_venta' => $idVenta
        );
        /*actualizo el campo dec_credito_montodebito que es el total acumulado*/
        $this->credito_model->update($where, $data);

        return $retorno;


    }

    public function get_suma_cuotas($where)
    {
        /*este metodo es usado en el cuadre de caja normal "PDF", y al momento de pagar las cuotas*/
        $this->db->select('sum(monto_abono) as suma, venta.id_moneda, total, inicial, simbolo,pago_anticipado');
        $this->db->from('credito_cuotas_abono');
        $this->db->join('credito_cuotas', 'credito_cuotas.id_credito_cuota=credito_cuotas_abono.credito_cuota_id');
        $this->db->join('credito', 'credito.id_venta=credito_cuotas.id_venta');
        $this->db->join('venta', 'venta.venta_id=credito_cuotas.id_venta');
        $this->db->join('moneda', 'moneda.id_moneda=venta.id_moneda');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_suma_cuotas_usuarios($where, $group)
    {
        /*este metodo es usado en el cuadre de caja por usuarios*/
        $this->db->select('sum(monto_abono) as suma,username,usuario_pago,pago_anticipado');
        $this->db->from('credito_cuotas_abono');
        $this->db->join('credito_cuotas', 'credito_cuotas.id_credito_cuota=credito_cuotas_abono.credito_cuota_id');
        $this->db->join('credito', 'credito.id_venta=credito_cuotas.id_venta');
        $this->db->join('usuario', 'usuario.nUsuCodigo=credito_cuotas_abono.usuario_pago');
        $this->db->where($where);
        if ($group != false) {
            $this->db->group_by($group);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
}
