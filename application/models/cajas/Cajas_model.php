<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class cajas_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('cajas/cajas_mov_model');
    }

    function get_caja() {
        return $this->db->select('caja.*, moneda.*, local.local_nombre as local_nombre')
                        ->from('caja')
                        ->join('local', 'local.int_local_id = caja.local_id')
                        ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                        ->where('estado', 1)
                        ->get()->result();
    }

    function sync_cajas($data = array()) {

        $locales = $this->db->get_where('local', array('local_status' => 1))->result();
        $monedas = $this->db->get_where('moneda', array('status_moneda' => 1))->result();

        foreach ($locales as $local) {
            foreach ($monedas as $moneda) {
                $caja = $this->db->get_where('caja', array(
                            'local_id' => $local->int_local_id,
                            'moneda_id' => $moneda->id_moneda
                        ))->row();

                if ($caja == NULL) {
                    $this->db->insert('caja', array(
                        'local_id' => $local->int_local_id,
                        'moneda_id' => $moneda->id_moneda,
                        'responsable_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                        'estado' => 1
                    ));
                }
            }
        }
    }

    function get_all($local = false) {
        $this->db->select('caja.*, moneda.*, usuario.nombre AS usuario_nombre, usuario.nUsuCodigo AS nUsuCodigo')
                ->join('local', 'local.int_local_id = caja.local_id')
                ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                ->join('usuario', 'usuario.nUsuCodigo = caja.responsable_id');

        if ($local != false) {
            $this->db->where('caja.local_id', $local);
        }

        $this->db->where('moneda.status_moneda', '1');


        $result = $this->db->order_by('caja.moneda_id')->get('caja')->result();

        foreach ($result as $desglose) {
            $desglose->desgloses = $this->db->where('caja_id', $desglose->id)
                            ->join('usuario', 'usuario.nUsuCodigo = caja_desglose.responsable_id')
                            ->get('caja_desglose')->result();

            foreach ($desglose->desgloses as $detalle) {
                $detalle->pendientes = $this->db->get_where('caja_pendiente', array(
                            'estado' => 0,
                            'caja_desglose_id' => $detalle->id
                        ))->result();
            }
        }

        return $result;
    }

    function getCajasSelect() {
        return $this->db->select('
            caja_desglose.id as cuenta_id,
            caja.moneda_id as moneda_id,
            caja_desglose.principal as principal,
            caja_desglose.descripcion as descripcion
            ')
                        ->from('caja_desglose')
                        ->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->where('caja.estado', 1)
                        ->where('caja_desglose.estado', 1)
                        ->where('caja_desglose.retencion', 0)
                        ->get()->result();
    }
    //Se Realiza una Busqueda de (Solo) Cajas activas segun Moneda Carlos Camargo (29-10-2018)
    function getCajasSelectCaja($where = array()) {
        $this->db->select('
            caja_desglose.id as cuenta_id,
            caja.moneda_id as moneda_id,
            caja_desglose.principal as principal,
            caja_desglose.descripcion as descripcion,
            local.local_nombre as local_nombre
            ')
            ->from('caja_desglose')
            ->join('caja', 'caja.id=caja_desglose.caja_id')   
            ->join('banco', 'banco.cuenta_id!=caja_desglose.id') 
            ->join('local', 'local.int_local_id = caja.local_id')
            ->where('caja_desglose.estado', 1)
            ->where('caja_desglose.retencion', 0)
            ->group_by('caja_desglose.id');
                       
        if (isset($where['moneda_id'])) {
            $this->db->where('caja.moneda_id', $where['moneda_id']);
        }
        return $this->db->get()->result_array();
     
         
    }


    function get($id) {
        return $this->db->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                        ->get_where('caja', array('id' => $id))->row();
    }

    function get_cuenta($id) {
        return $this->db->get_where('caja_desglose', array('id' => $id))->row();
    }

    function get_cuenta_id($data) {
        $cuenta = $this->db->select('caja_desglose.id as id')->from('caja_desglose')
                        ->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->where('caja_desglose.principal', 1)
                        ->where('caja.moneda_id', $data['moneda_id'])
                        ->where('caja.local_id', $data['local_id'])
                        ->get()->row();

        return $cuenta != NULL ? $cuenta->id : NULL;
    }

    function get_cierre($id) {
        return $this->db->get_where('caja_cuadre', array('id' => $id))->row();
    }

    function save($caja, $id = FALSE) {

        if ($id != FALSE) {
            $this->db->where('id', $id);
            $this->db->update('caja', $caja);
            return $id;
        } else {
            $this->db->insert('caja', $caja);
            return $this->db->insert_id();
        }
    }

    function update_saldo($id, $saldo, $ingreso = TRUE) {
        $cuenta = $this->get_cuenta($id);

        if ($ingreso == TRUE) {
            $new_saldo = $cuenta->saldo + $saldo;
        } elseif ($ingreso == FALSE) {
            $new_saldo = $cuenta->saldo - $saldo;
        }

        if ($new_saldo >= 0) {
            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array('saldo' => $new_saldo));
        }
    }

    function save_cuenta($caja, $id = FALSE) {
        $this->db->where('caja_id', $caja['caja_id']);
        $this->db->from('caja_desglose');
        if ($this->db->count_all_results() == 0) {
            $caja['principal'] == 1;
        }
        if ($caja['principal'] == 1) {
            $caja['estado'] == 1;
            $this->db->where('principal', 1);
            $this->db->where('caja_id', $caja['caja_id']);
            $this->db->update('caja_desglose', array('principal' => 0));
        }

        if ($id != FALSE) {
            $this->db->where('id', $id);
            $this->db->update('caja_desglose', $caja);
            return $id;
        } else {
            $data['saldo'] = 0;
            $this->db->insert('caja_desglose', $caja);
            return $this->db->insert_id();
        }
    }

    function ajustar_cuenta($data, $id) {
        $fecha = date('Y-m-d H:i:s', strtotime($data['fecha'] . ' ' . date('H:i:s')));
        $cuenta = $this->get_cuenta($id);

        if ($data['tipo_ajuste'] == 'TRASPASO')
            $cuenta_destino = $this->get_cuenta($data['cuenta_id']);

        if ($data['tipo_ajuste'] == 'INGRESO' || $data['tipo_ajuste'] == 'EGRESO') {
            $saldo = $data['tipo_ajuste'] == 'EGRESO' ? $cuenta->saldo - $data['importe'] : $cuenta->saldo + $data['importe'];
            $saldo_old = $cuenta->saldo;

            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => $data['tipo_ajuste'],
                'operacion' => 'AJUSTE',
                'medio_pago' => 3,
                'saldo' => $data['importe'],
                'saldo_old' => $saldo_old,
                'ref_id' => '',
                'ref_val' => $data['motivo'],
            ));
        } else if ($data['tipo_ajuste'] == 'TRASPASO' && $cuenta->responsable_id == $cuenta_destino->responsable_id) {
            //HAGO EL EGRESO
            $saldo = $cuenta->saldo - $data['importe'];
            $saldo_old = $cuenta->saldo;

            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => 'EGRESO',
                'operacion' => 'TRASPASO',
                'medio_pago' => 3,
                'saldo' => $data['importe'],
                'saldo_old' => $saldo_old,
                'ref_id' => $data['cuenta_id'],
                'ref_val' => $data['motivo'],
            ));

            //HAGO EL INGRESO
            $saldo = $cuenta_destino->saldo + $data['subimporte'];
            $saldo_old = $cuenta_destino->saldo;

            $tasa = "";
            if ($cuenta->caja_id != $cuenta_destino->caja_id)
                $tasa = $data['tasa'];

            $this->db->where('id', $data['cuenta_id']);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $cuenta_destino->id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => 'INGRESO',
                'operacion' => 'TRASPASO',
                'medio_pago' => 3,
                'saldo' => $data['subimporte'],
                'saldo_old' => $saldo_old,
                'ref_id' => $id,
                'ref_val' => $tasa,
            ));
        } else if ($data['tipo_ajuste'] == 'TRASPASO' && $cuenta->responsable_id != $cuenta_destino->responsable_id) {

            $this->db->insert('caja_pendiente', array(
                'caja_desglose_id' => $cuenta_destino->id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'tipo' => 'TRASPASO',
                'IO' => 1,
                'monto' => $data['importe'],
                'estado' => 0,
                'ref_id' => $id
            ));
        }
    }

    function ajustar_retencion($data, $id) {
        $fecha = date('Y-m-d H:i:s', strtotime($data['fecha'] . ' ' . date('H:i:s')));
        $cuenta = $this->get_cuenta($id);

        $saldo = $cuenta->saldo - $data['importe'];
        $saldo_old = $cuenta->saldo;

        $this->db->where('id', $id);
        $this->db->update('caja_desglose', array(
            'saldo' => $saldo
        ));

        $this->cajas_mov_model->save_mov(array(
            'caja_desglose_id' => $id,
            'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
            'fecha_mov' => $fecha,
            'movimiento' => 'EGRESO',
            'operacion' => 'SUNAT',
            'medio_pago' => 3,
            'saldo' => $data['importe'],
            'saldo_old' => $saldo_old,
            'ref_id' => '',
            'ref_val' => implode('|', $data['retenciones']),
        ));

        foreach ($data['retenciones'] as $ret_id) {
            $this->db->where('id', $ret_id);
            $this->db->update('caja_movimiento', array(
                'operacion' => 'SUNAT'
            ));
        }
    }

    function valid_caja($data, $id = FALSE) {
        $this->db->where('local_id', $data['local_id']);
        $this->db->where('moneda_id', $data['moneda_id']);
        if ($id != FALSE)
            $this->db->where('id !=', $id);
        $this->db->from('caja');

        if ($this->db->count_all_results() == 0)
            return TRUE;
        else
            return FALSE;
    }

    function valid_caja_cuenta($data, $id = FALSE) {

        $this->db->where('descripcion', $data['descripcion']);
        $this->db->where('responsable_id', $data['responsable_id']);
        if ($id != FALSE)
            $this->db->where('id !=', $id);
        $this->db->from('caja_desglose');

        if ($this->db->count_all_results() == 0)
            return TRUE;
        else
            return TRUE;
    }

    function get_valid_cuenta_id($moneda, $local, $data = array()) {
        $cuenta = $this->db->select('caja_desglose.id as id')->from('caja_desglose')
                        ->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->where('caja_desglose.principal', 1)
                        ->where('caja.moneda_id', $moneda)
                        ->where('caja.local_id', $local)
                        ->get()->row();

        if ($cuenta == NULL) {
            $this->db->insert('caja', array(
                'local_id' => $local,
                'moneda_id' => $moneda,
                'responsable_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'estado' => 1
            ));
            $caja_id = $this->db->insert_id();

            $this->db->insert('caja_desglose', array(
                'caja_id' => $caja_id,
                'responsable_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'descripcion' => 'Caja Temporal Principal',
                'saldo' => 0,
                'principal' => 1,
                'retencion' => 0,
                'estado' => 1,
            ));

            $cuenta_id = $this->db->insert_id();

            $this->db->where('caja_id', $caja_id);
            $this->db->where('id !=', $cuenta_id);
            $this->db->update('caja_desglose', array('principal' => 0));

            return $cuenta_id;
        }

        return $cuenta->id;
    }

    function save_pendiente($data) {

        $this->db->insert('caja_pendiente', array(
            'caja_desglose_id' => isset($data['cuenta_id']) ? $data['cuenta_id'] : $this->get_valid_cuenta_id($data['moneda_id'], $data['local_id']),
            'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
            'tipo' => $data['tipo'],
            'monto' => $data['monto'],
            'estado' => 0,
            'IO' => $data['IO'],
            'ref_id' => $data['ref_id'],
            'ref_val' => isset($data['ref_val']) ? $data['ref_val'] : ''
        ));
    }

    function editar_pendiente($data) {
        $this->db->where('ref_id', $data['id']);
        $this->db->where('tipo', 'GASTOS');
        $this->db->update('caja_pendiente', array(
            'caja_desglose_id' => $data['cuenta_id'],
            'usuario_id' => $this->session->userdata('nUsuCodigo'),
            'monto' => $data['monto'],
            'estado' => 0,
        ));
    }

    function update_pendiente($data) {

        $cuenta = $this->db->get_where('caja_desglose', array(
                    'id' => $this->get_valid_cuenta_id($data['moneda_id'], $data['local_id']
            )))->row();

        $caja_pendiente = $this->db->get_where('caja_pendiente', array(
                    'tipo' => $data['tipo'],
                    'ref_id' => $data['ref_id']
                ))->row();

        if ($caja_pendiente != NULL) {
            if ($caja_pendiente->estado == 1) {
                $new_saldo = $cuenta->saldo + $caja_pendiente->monto;
                $this->db->where('id', $cuenta->id);
                $this->db->update('caja_desglose', array('saldo' => $new_saldo));

                $this->db->insert('caja_movimiento', array(
                    'caja_desglose_id' => $cuenta->id,
                    'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                    'fecha_mov' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'movimiento' => 'INGRESO',
                    'operacion' => 'INGRESO_ANULADO',
                    'medio_pago' => 3,
                    'saldo' => $caja_pendiente->monto,
                    'saldo_old' => $cuenta->saldo,
                    'ref_id' => $data['ref_id']
                ));
            }

            $this->db->where('id', $caja_pendiente->id);
            $this->db->update('caja_pendiente', array(
                'caja_desglose_id' => $cuenta->id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'monto' => $data['monto'],
                'estado' => 0,
            ));
        } else {
            if (!isset($data['IO']))
                $data['IO'] = 2;

            $this->db->insert('caja_pendiente', array(
                'caja_desglose_id' => $cuenta->id,
                'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                'tipo' => $data['tipo'],
                'monto' => $data['monto'],
                'estado' => 0,
                'IO' => $data['IO'],
                'ref_id' => $data['ref_id']
            ));
        }
    }

    function delete_pendiente($data) {

        $cuenta = $this->db->get_where('caja_desglose', array(
                    'id' => $this->get_valid_cuenta_id($data['moneda_id'], $data['local_id']
            )))->row();

        if ($data['tipo'] == 'COMPRA')
            $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $data['ref_id']))->row();

        $caja_pendiente = $this->db->get_where('caja_pendiente', array(
                    'tipo' => $data['tipo'],
                    'ref_id' => $data['ref_id']
                ))->row();

        if ($caja_pendiente != NULL) {
            if ($caja_pendiente->estado == 1) {
                if (isset($ingreso) && $ingreso->pago == 'CREDITO') {
                    $ingreso_credito = $this->db->get_where('ingreso_credito', array('ingreso_id' => $ingreso->id_ingreso))->row();
                    if ($ingreso_credito->inicial > 0) {
                        $cuenta = $this->db->get_where('caja_desglose', array(
                                    'id' => $this->get_valid_cuenta_id($data['moneda_id'], $data['local_id']
                            )))->row();

                        $new_saldo = $cuenta->saldo + $ingreso_credito->inicial;
                        $this->db->where('id', $cuenta->id);
                        $this->db->update('caja_desglose', array('saldo' => $new_saldo));

                        $this->db->insert('caja_movimiento', array(
                            'caja_desglose_id' => $cuenta->id,
                            'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                            'fecha_mov' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s'),
                            'movimiento' => 'INGRESO',
                            'operacion' => 'INGRESO_ANULADO',
                            'medio_pago' => 3,
                            'saldo' => $ingreso_credito->inicial,
                            'saldo_old' => $cuenta->saldo,
                            'ref_id' => $data['ref_id']
                        ));
                    }

                    $pagos = $this->db->select('pagos_ingreso.*')->from('pagos_ingreso')
                                    ->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id = pagos_ingreso.pagoingreso_ingreso_id')
                                    ->where('ingreso_credito_cuotas.ingreso_id', $ingreso->id_ingreso)
                                    ->group_by('pagos_ingreso.pagoingreso_id')
                                    ->get()->result();

                    foreach ($pagos as $pago) {

                        $caja_pendiente = $this->db->get_where('caja_pendiente', array(
                                    'tipo' => 'PAGOS_CUOTAS',
                                    'ref_id' => $pago->pagoingreso_id
                                ))->row();

                        if ($caja_pendiente != null) {
                            if ($caja_pendiente->estado == 1) {
                                $cuenta = $this->db->get_where('caja_desglose', array(
                                            'id' => $this->get_valid_cuenta_id($data['moneda_id'], $data['local_id']
                                    )))->row();

                                $new_saldo = $cuenta->saldo + $pago->pagoingreso_monto;
                                $this->db->where('id', $cuenta->id);
                                $this->db->update('caja_desglose', array('saldo' => $new_saldo));

                                $this->db->insert('caja_movimiento', array(
                                    'caja_desglose_id' => $cuenta->id,
                                    'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                                    'fecha_mov' => date('Y-m-d H:i:s'),
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'movimiento' => 'INGRESO',
                                    'operacion' => 'INGRESO_ANULADO',
                                    'medio_pago' => $pago->medio_pago_id,
                                    'saldo' => $pago->pagoingreso_monto,
                                    'saldo_old' => $cuenta->saldo,
                                    'ref_id' => $data['ref_id']
                                ));
                            } else {
                                $this->db->where('id', $caja_pendiente->id);
                                $this->db->delete('caja_pendiente');
                            }
                        }
                    }
                } else {
                    $new_saldo = $cuenta->saldo + $caja_pendiente->monto;
                    $this->db->where('id', $cuenta->id);
                    $this->db->update('caja_desglose', array('saldo' => $new_saldo));

                    $this->db->insert('caja_movimiento', array(
                        'caja_desglose_id' => $cuenta->id,
                        'usuario_id' => isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo'),
                        'fecha_mov' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'movimiento' => 'INGRESO',
                        'operacion' => 'INGRESO_ANULADO',
                        'medio_pago' => 3,
                        'saldo' => $caja_pendiente->monto,
                        'saldo_old' => $cuenta->saldo,
                        'ref_id' => $data['ref_id']
                    ));
                }
            } else {
                $this->db->where('id', $caja_pendiente->id);
                $this->db->delete('caja_pendiente');
            }
        }
    }

    function getSaldosPendientes($id) {
        $saldos_pendientes = $this->db->select('caja_pendiente.*, moneda.*, caja.moneda_id, usuario.nombre')
                        ->from('caja_pendiente')
                        ->join('usuario', 'usuario.nUsuCodigo = caja_pendiente.usuario_id')
                        ->join('caja_desglose', 'caja_desglose.id = caja_pendiente.caja_desglose_id')
                        ->join('caja', 'caja.id = caja_desglose.caja_id')
                        ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                        ->where(array(
                            'caja_pendiente.estado' => 0,
                            'caja_desglose_id' => $id
                        ))->get()->result();

        for ($x = 0; $x < count($saldos_pendientes); $x++) {
            if ($saldos_pendientes[$x]->tipo == 'GASTOS') {
                $this->db->select('proveedor_id, usuario_id');
                $this->db->from('gastos g');
                $this->db->where('id_gastos', $saldos_pendientes[$x]->ref_id);
                $gastos = $this->db->get()->row();

                if (!empty($gastos->proveedor_id)) {
                    $this->db->select('proveedor_nombre');
                    $this->db->from('proveedor p');
                    $this->db->where('id_proveedor', $gastos->proveedor_id);
                    $proveedor = $this->db->get()->row();
                    $saldos_pendientes[$x]->proveedor = $proveedor->proveedor_nombre;
                } else {
                    $this->db->select('username');
                    $this->db->from('usuario u');
                    $this->db->where('nUsuCodigo', $gastos->usuario_id);
                    $trabajador = $this->db->get()->row();
                    $saldos_pendientes[$x]->proveedor = $trabajador->username;
                }
                $saldos_pendientes[$x]->cliente = '';
            } elseif ($saldos_pendientes[$x]->tipo == 'COMPRA') {
                $this->db->select('proveedor_nombre');
                $this->db->from('ingreso i');
                $this->db->join('proveedor p', 'i.int_Proveedor_id = p.id_proveedor');
                $this->db->where('id_ingreso', $saldos_pendientes[$x]->ref_id);
                $proveedor = $this->db->get()->row();
                $saldos_pendientes[$x]->cliente = '';
                $saldos_pendientes[$x]->proveedor = $proveedor->proveedor_nombre;
            } elseif ($saldos_pendientes[$x]->tipo == 'PAGOS_CUOTAS') {
                $this->db->select('IF(p.proveedor_nombre IS NULL, u.username, p.proveedor_nombre) AS proveedor_nombre');
                $this->db->from('pagos_ingreso pi');
                $this->db->join('ingreso_credito_cuotas icc', 'pi.pagoingreso_ingreso_id = icc.id');
                $this->db->join('ingreso i', 'icc.ingreso_id = i.id_ingreso');
                $this->db->join('proveedor p', 'i.int_Proveedor_id = p.id_proveedor', 'left');
                $this->db->join('usuario u', 'i.int_usuario_id = u.nUsuCodigo', 'left');
                $this->db->where('pi.pagoingreso_id', $saldos_pendientes[$x]->ref_id);
                $proveedor = $this->db->get()->row();
                $saldos_pendientes[$x]->cliente = '';
                $saldos_pendientes[$x]->proveedor = $proveedor->proveedor_nombre;
            } elseif ($saldos_pendientes[$x]->tipo == 'VENTA_ANULADA' || $saldos_pendientes[$x]->tipo == 'VENTA_DEVUELTA') {
                $this->db->select('razon_social');
                $this->db->from('venta v');
                $this->db->join('cliente c', 'v.id_cliente = c.id_cliente');
                $this->db->where('v.venta_id', $saldos_pendientes[$x]->ref_id);
                $cliente = $this->db->get()->row();
                $saldos_pendientes[$x]->cliente = $cliente->razon_social;
                $saldos_pendientes[$x]->proveedor = '';
            } else {
                $saldos_pendientes[$x]->cliente = '';
                $saldos_pendientes[$x]->proveedor = '';
            }
        }
        return $saldos_pendientes;
    }

}
