<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas_mov_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function save_mov($data = array())
    {

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert('caja_movimiento', $data);
    }

    function get_movimientos_today($cuenta_id, $data = array())
    {
        if (!isset($data['fecha_ini'])) $data['fecha_ini'] = date('Y-m-d') . ' 00:00:00';
        if (!isset($data['fecha_fin'])) $data['fecha_fin'] = date('Y-m-d') . ' 23:59:59';

        $this->db->select('
            caja_movimiento.*,
            moneda.*,
            usuario.username as usuario_nombre,
            caja.moneda_id as moneda_id,
            local.local_nombre as local_nombre
        ')
            ->from('caja_movimiento')
            ->join('caja_desglose', 'caja_desglose.id = caja_movimiento.caja_desglose_id')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
            ->join('local', 'caja.local_id = local.int_local_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja_movimiento.usuario_id')
            ->where('caja_movimiento.caja_desglose_id', $cuenta_id)
            ->where('caja_movimiento.created_at >=', $data['fecha_ini'])
            ->where('caja_movimiento.created_at <=', $data['fecha_fin'])
            ->order_by('caja_movimiento.id', 'ASC');

        $movimientos = $this->db->get()->result();

        foreach ($movimientos as $mov) {
            $mov->numero = '';
            $mov->operacion_nombre = $mov->operacion;
            $mov->usuario_registra = $mov->usuario_nombre;

            $metodo = $this->db->get_where('metodos_pago', array('id_metodo' => $mov->medio_pago))->row();
            $mov->medio_pago_nombre = $metodo != NULL ? $metodo->nombre_metodo : '';

            if ($mov->operacion == 'VENTA') {

                $venta = $this->db->get_where('venta', array('venta_id' => $mov->ref_id))->row();
                $doc = 'NV ';
                if ($venta->id_documento == 1) $doc = 'FA ';
                if ($venta->id_documento == 3) $doc = 'BO ';
                if ($venta->numero != "") {
                    $mov->ref_val = $doc . $venta->serie . ' - ' . sumCod($venta->numero, 6);
                } else {
                    $mov->ref_val = 'NO EMITIDO';
                }
                $mov->numero = 'NV ' . $mov->ref_id . ' (' . date('d/m/Y', strtotime($venta->fecha)) . ')';
            }

            if ($mov->operacion == 'COMPRA') {

                $caja_pendiente = $this->db->get_where('caja_pendiente', array('id' => $mov->ref_id))->row();
                $ingreso = $this->db->join('usuario', 'usuario.nUsuCodigo = ingreso.nUsuCodigo')
                    ->get_where('ingreso', array('id_ingreso' => $caja_pendiente->ref_id))->row();

                $doc = 'NV ';
                if ($ingreso->tipo_documento == 'BOLETA DE VENTA') $doc = 'BO ';
                if ($ingreso->tipo_documento == 'FACTURA') $doc = 'FA ';

                $mov->numero = $doc . $ingreso->documento_serie . ' - ' . $ingreso->documento_numero;
                $mov->ref_val = 'Registro: ' . date('d/m/Y', strtotime($ingreso->fecha_registro));

                $mov->usuario_registra = $ingreso->username;
            }

            if ($mov->operacion == 'VENTA_ANULADA' || $mov->operacion == 'VENTA_DEVUELTA') {
                $caja_pendiente = $this->db->get_where('caja_pendiente', array('id' => $mov->ref_id))->row();
                $venta = $this->db->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor')
                    ->get_where('venta', array('venta_id' => $caja_pendiente->ref_id))->row();

                $kardex = $this->db->get_where('kardex', array(
                    'io' => 2,
                    'tipo' => 7,
                    'operacion' => 5,
                    'ref_id' => $caja_pendiente->ref_id
                ))->row();

                $mov->operacion_nombre = $mov->operacion == 'VENTA_ANULADA' ? 'VENTA ANULADA' : 'VENTA DEVUELTA';
                if (isset($kardex->serie) && isset($kardex->numero)) {
                $mov->numero = 'NC ' . $kardex->serie . ' - ' . $kardex->numero;
                }else
                $mov->numero = 'NO DEFINIDO';
                $mov->ref_val = 'NV ' . $caja_pendiente->ref_id . ' (' . date('d/m/Y', strtotime($venta->fecha)) . ')';

                $mov->usuario_registra = $venta->username;
            }

            if ($mov->operacion == 'INGRESO_ANULADO') {
                $mov->operacion_nombre = 'COMPRA ANULADA';
                $kardex = $this->db->get_where('kardex', array(
                    'io' => 1,
                    'tipo' => 7,
                    'operacion' => 6,
                    'ref_id' => $mov->ref_id
                ))->row();

                $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $mov->ref_id))->row();

                $mov->numero = 'NC ' . $kardex->serie . ' - ' . $kardex->numero;
                $doc = 'NV ';
                if ($ingreso->tipo_documento == 'BOLETA DE VENTA') $doc = 'BO ';
                if ($ingreso->tipo_documento == 'FACTURA') $doc = 'FA ';

                $mov->ref_val = $doc . $ingreso->documento_serie . ' - ' . $ingreso->documento_numero;
            }

            if ($mov->operacion == 'GASTOS') {
                $caja_pendiente = $this->db->get_where('caja_pendiente', array('id' => $mov->ref_id))->row();
                $gasto = $this->db->join('usuario', 'usuario.nUsuCodigo = gastos.gasto_usuario')
                    ->join('tipos_gasto', 'tipos_gasto.id_tipos_gasto = gastos.tipo_gasto')
                    ->get_where('gastos', array('id_gastos' => $caja_pendiente->ref_id))->row();

                $mov->operacion_nombre = $mov->operacion_nombre . ' (' . $gasto->nombre_tipos_gasto . ')';
                $mov->ref_val = $gasto->descripcion;

                $mov->numero = 'Registro: ' . date('d/m/Y', strtotime($gasto->fecha_registro));

                $mov->usuario_registra = $gasto->username;
            }

            if ($mov->operacion == 'CUOTA') {
                $mov->operacion_nombre = 'CUENTA X COBRAR';

                $venta = $this->db->get_where('venta', array('venta_id' => $mov->ref_id))->row();
                $doc = 'NV ';
                if ($venta->id_documento == 1) $doc = 'FA ';
                if ($venta->id_documento == 3) $doc = 'BO ';
                if ($venta->numero != "") {
                    $mov->ref_val = $doc . $venta->serie . ' - ' . sumCod($venta->numero, 6);
                } else {
                    $mov->ref_val = 'NO EMITIDO';
                }

                $mov->numero = 'NV ' . $mov->ref_id;
            }

            if ($mov->operacion == 'PAGOS_CUOTAS') {
                $mov->operacion_nombre = 'PAGO PROVEEDORES';

                $caja_pendiente = $this->db->get_where('caja_pendiente', array('id' => $mov->ref_id))->row();
                $pago_ingreso = $this->db->join('usuario', 'usuario.nUsuCodigo = pagos_ingreso.pagoingreso_usuario')
                    ->get_where('pagos_ingreso', array('pagoingreso_id' => $caja_pendiente->ref_id))->row();
                $ingreso_credito_cuotas = $this->db->get_where('ingreso_credito_cuotas', array('id' => $pago_ingreso->pagoingreso_ingreso_id))->row();
                $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $ingreso_credito_cuotas->ingreso_id))->row();

                $doc = 'NV ';
                if ($ingreso->tipo_documento == 'BOLETA DE VENTA') $doc = 'BO ';
                if ($ingreso->tipo_documento == 'FACTURA') $doc = 'FA ';

                $mov->numero = $doc . $ingreso->documento_serie . ' - ' . $ingreso->documento_numero . ' (' . date('d/m/Y', strtotime($ingreso->fecha_registro)) . ')';

                $mov->ref_val = 'Monto: ' . $ingreso_credito_cuotas->monto . ' | Letra: ' . $ingreso_credito_cuotas->letra;

                $mov->usuario_registra = $pago_ingreso->username;
            }

            if ($mov->operacion == 'AJUSTE') {
                $mov->numero = 'Operacion Interna';
            }

            if ($mov->operacion == 'TRASPASO') {
                $mov->numero = 'Operacion Interna';

                $cuenta = $this->db->get_where('caja_desglose', array('id' => $mov->ref_id))->row();
                if ($mov->movimiento == 'INGRESO') {
                    $mov->numero = 'Desde ' . $cuenta->descripcion;
                    $mov->ref_val = "";
                } else {
                    $mov->numero = 'Hacia ' . $cuenta->descripcion;
                }
            }
        }


        return $movimientos;
    }

}