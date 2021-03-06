<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('cajas/cajas_model');
            $this->load->model('cajas/cajas_mov_model');
            $this->load->model('local/local_model');
            $this->load->model('usuario/usuario_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function index($local_id = false)
    {

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $local = $local_id != false ? $local_id : $this->session->userdata('id_local');
        $data['cajas'] = $this->cajas_model->get_all($local);
        $data['local_selected'] = $local;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/cajas/cajas', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function caja_form($id = FALSE)
    {

        $data['header_text'] = 'Nueva Caja';
        if ($id != FALSE) {
            $data['caja'] = $this->cajas_model->get($id);
            $data['header_text'] = 'Editar Caja';
        }

        $data['locales'] = $this->local_model->get_all();
        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form', $data);
    }

    function caja_guardar($id = FALSE)
    {
        $data = array(
            'local_id' => $this->input->post('local_id'),
            'moneda_id' => $this->input->post('moneda_id'),
            'responsable_id' => $this->input->post('responsable_id'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja($data, $id)) {
            $result = $this->cajas_model->save($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

    function caja_cuenta_form($caja_id, $id = FALSE)
    {
        $data['header_text'] = 'Nueva Cuenta de Caja';
        if ($id != FALSE) {
            $data['cuenta'] = $this->cajas_model->get_cuenta($id);
            $data['header_text'] = 'Editar Cuenta de Caja';
        }

        $data['caja_id'] = $caja_id;

        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form_cuenta', $data);
    }

    function caja_cuenta_guardar($id = FALSE)
    {
        $data = array(
            'caja_id' => $this->input->post('caja_id'),
            'descripcion' => $this->input->post('descripcion'),
            'responsable_id' => $this->input->post('responsable_id'),
            'principal' => $this->input->post('principal'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja_cuenta($data, $id)) {
            $result = $this->cajas_model->save_cuenta($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

    function caja_ajustar_form($caja_id, $id, $type)
    {

        $data['header_text'] = 'Ajustar Cuenta de Caja';
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['caja_id'] = $caja_id;
        $data['caja_actual'] = $this->cajas_model->get($caja_id);

        $data['locales'] = $this->local_model->get_all();
        $data['type'] = $type == 'dolares' ? '$' : 'S';
        $data['cajas'] = $this->db->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->get_where('caja', array('estado' => 1))->result();
        $data['caja_cuentas'] = $this->db->get_where('caja_desglose', array('estado' => 1))->result();

        $this->load->view('menu/cajas/form_ajustar_cuenta', $data);

    }

    function caja_ajustar_guardar($id)
    {
        $data = array(
            'fecha' => $this->input->post('fecha'),
            'motivo' => $this->input->post('motivo'),
            'tipo_ajuste' => $this->input->post('tipo_ajuste'),
            'cuenta_id' => $this->input->post('cuenta_id'),
            'tasa' => $this->input->post('tasa'),
            'importe' => $this->input->post('importe'),
            'subimporte' => $this->input->post('subimporte')
        );
        $this->cajas_model->ajustar_cuenta($data, $id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function caja_ajustar_retencion_form($caja_id, $id)
    {
        $data['header_text'] = 'Realizar pago por retencion';
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['caja_id'] = $caja_id;
        $data['caja_actual'] = $this->cajas_model->get($caja_id);

        $data['cajas'] = $this->db->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->get_where('caja', array('estado' => 1))->result();
        $data['caja_cuentas'] = $this->db->get_where('caja_desglose', array('estado' => 1))->result();

        $data['retenciones'] = $this->db->get_where('caja_movimiento', array(
            'caja_desglose_id' => $id,
            'medio_pago' => 7,
            'operacion' => 'COBRANZA',
            'movimiento' => 'INGRESO'
        ))->result();

        $data['year'] = $this->db->query("
            SELECT date_format(fecha_mov, '%Y') as year
            FROM caja_movimiento
            GROUP BY date_format(fecha_mov, '%Y')
            ")->result();

        $this->load->view('menu/cajas/form_ajustar_cuenta_retencion', $data);
    }

    function caja_ajustar_retencion_guardar($id)
    {
        $data = array(
            'fecha' => $this->input->post('fecha'),
            'importe' => $this->input->post('importe'),
            'retenciones' => json_decode($this->input->post('retenciones'))
        );
        $this->cajas_model->ajustar_retencion($data, $id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function caja_detalle_form($id)
    {
        $data['desglose_id'] = $id;
        $data['cuenta'] = $this->db->select('caja_desglose.*, caja.*, moneda.*, usuario.nombre AS usuario_nombre')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja_desglose.responsable_id')
            ->where('caja_desglose.id', $id)->get()->row();

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($this->input->post('fecha_ini') . "00:00:00")),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($this->input->post('fecha_fin') . "23:59:59")),
        );

        $data['fecha_ini'] = str_replace('-', '/', $this->input->post('fecha_ini'));
        $data['fecha_fin'] = str_replace('-', '/', $this->input->post('fecha_fin'));

        $data['cuenta_movimientos'] = $this->cajas_mov_model->get_movimientos_today($id, $params);

        $this->load->view('menu/cajas/form_detalle', $data);
    }

    function caja_detalle_excel($id)
    {
        $data['desglose_id'] = $id;
        $params = json_decode($this->input->get('data'));

        $data['cuenta'] = $this->db->select('caja_desglose.*, caja.*, moneda.*, usuario.nombre AS usuario_nombre')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja_desglose.responsable_id')
            ->where('caja_desglose.id', $id)->get()->row();

        $data['fecha_ini'] = str_replace('-', '/', $params->fecha_ini);
        $data['fecha_fin'] = str_replace('-', '/', $params->fecha_fin);

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($params->fecha_ini . "00:00:00")),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($params->fecha_fin . "23:59:59")),
        );


        $data['cuenta_movimientos'] = $this->cajas_mov_model->get_movimientos_today($id, $params);

        echo $this->load->view('menu/cajas/form_detalle_excel', $data, TRUE);
    }

    function caja_detalle_pdf($id)
    {
        $data['desglose_id'] = $id;
        $params = json_decode($this->input->get('data'));

        $data['cuenta'] = $this->db->select('caja_desglose.*, caja.*, moneda.*, usuario.nombre AS usuario_nombre')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja_desglose.responsable_id')
            ->where('caja_desglose.id', $id)->get()->row();

        $data['fecha_ini'] = str_replace('-', '/', $params->fecha_ini);
        $data['fecha_fin'] = str_replace('-', '/', $params->fecha_fin);

        $params = array(
            'fecha_ini' => date('Y-m-d H:i:s', strtotime($params->fecha_ini . "00:00:00")),
            'fecha_fin' => date('Y-m-d H:i:s', strtotime($params->fecha_fin . "23:59:59")),
        );


        $data['cuenta_movimientos'] = $this->cajas_mov_model->get_movimientos_today($id, $params);

        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/cajas/form_detalle_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();

    }

    function caja_pendiente_form($id)
    {
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);
        $data['saldos_pendientes'] = $this->cajas_model->getSaldosPendientes($id);
        $this->load->view('menu/cajas/form_pendiente', $data);
    }

    function confirmar_saldo($id)
    {
        header('Content-Type: application/json');

        $caja_pendiente = $this->db->get_where('caja_pendiente', array('id' => $id))->row();

        $caja_desglose = $this->db->get_where('caja_desglose', array('id' => $caja_pendiente->caja_desglose_id))->row();

        $traspaso_flag = false;
        if ($caja_pendiente->tipo == 'TRASPASO') {
            $caja_desglose_d = $this->db->get_where('caja_desglose', array('id' => $caja_pendiente->ref_id))->row();
            if ($caja_pendiente->monto > $caja_desglose_d->saldo)
                $traspaso_flag = true;
        }

        if (($caja_pendiente->IO == 2 && $caja_pendiente->monto > $caja_desglose->saldo) || $traspaso_flag == true) {
            echo json_encode(array('error' => 1));
        } else {
            $data_mov = array(
                'caja_desglose_id' => $caja_desglose->id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => date('Y-m-d H:i:s'),
                'operacion' => $caja_pendiente->tipo,
                'medio_pago' => 3,
                'saldo' => $caja_pendiente->monto,
                'saldo_old' => $caja_desglose->saldo,
                'ref_id' => $caja_pendiente->id
            );

            if ($caja_pendiente->tipo == 'VENTA_ANULADA' || $caja_pendiente->tipo == 'VENTA_DEVUELTA') {
                $movimiento = $this->db->get_where('caja_movimiento', array(
                    'ref_id' => $caja_pendiente->ref_id,
                    'operacion' => 'VENTA',
                    'movimiento' => 'INGRESO'
                ))->row();

                $data_mov['medio_pago'] = $caja_pendiente->ref_val != "" ? $caja_pendiente->ref_val : $movimiento->medio_pago;
            }

            if ($caja_pendiente->tipo == 'PAGOS_CUOTAS') {
                $movimiento = $this->db->get_where('pagos_ingreso', array(
                    'pagoingreso_id' => $caja_pendiente->ref_id
                ))->row();

                $data_mov['medio_pago'] = $movimiento->medio_pago_id;
            }

            $new_saldo = 0;
            if ($caja_pendiente->IO == 2) {
                $data_mov['movimiento'] = 'EGRESO';
                $new_saldo = $caja_desglose->saldo - $caja_pendiente->monto;
            } else {
                $data_mov['movimiento'] = 'INGRESO';
                $new_saldo = $caja_desglose->saldo + $caja_pendiente->monto;
            }

            

            $this->db->where('id', $caja_desglose->id);
            $this->db->update('caja_desglose', array('saldo' => $new_saldo));

            if ($caja_pendiente->tipo == 'PAGOS_CUOTAS') {
                $this->db->where('pagoingreso_id', $caja_pendiente->ref_id);
                $this->db->update('pagos_ingreso', array(
                    'estado' => 'COMPLETADO'
                ));
            }

            if ($caja_pendiente->tipo == 'GASTOS') {
                $this->db->where('id_gastos', $caja_pendiente->ref_id);
                $this->db->update('gastos', array(
                    'status_gastos' => 0
                ));

                $this->db->where('id_gastos', $caja_pendiente->ref_id);
                $this->db->update('ingreso', array('ingreso_status' => 'COMPLETADO'));
                $movimiento = $this->db->get_where('pagos_ingreso', array(
                    'pagoingreso_id' => $caja_pendiente->ref_id
                ))->row();
                $resgastos=$this->db->get_where('gastos', array('id_gastos'=> $caja_pendiente->ref_id))->row();
                $data_mov['medio_pago'] = $resgastos->medio_pago;
            }
            $this->cajas_mov_model->save_mov($data_mov);
            if ($caja_pendiente->tipo == 'TRASPASO') {

                $caja_desglose_d = $this->db->get_where('caja_desglose', array('id' => $caja_pendiente->ref_id))->row();

                $this->cajas_mov_model->save_mov(array(
                    'caja_desglose_id' => $caja_desglose_d->id,
                    'usuario_id' => $this->session->userdata('nUsuCodigo'),
                    'fecha_mov' => date('Y-m-d H:i:s'),
                    'operacion' => $caja_pendiente->tipo,
                    'medio_pago' => 'INTERNO',
                    'saldo' => $caja_pendiente->monto,
                    'saldo_old' => $caja_desglose_d->saldo,
                    'ref_id' => $caja_desglose->id,
                    'movimiento' => 'EGRESO'
                ));

                $this->db->where('id', $caja_desglose_d->id);
                $this->db->update('caja_desglose', array('saldo' => $caja_desglose_d->saldo - $caja_pendiente->monto));
            }

            $this->db->where('id', $caja_pendiente->id);
            $this->db->update('caja_pendiente', array('estado' => 1));

            echo json_encode(array('success' => 1));
        }


    }

}
