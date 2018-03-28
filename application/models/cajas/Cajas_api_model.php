<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cajas_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_cuenta($id)
    {
        return $this->db->get_where('caja_desglose', array('id' => $id))->row();
    }

    function get_cuenta_id($data)
    {
        $cuenta = $this->db->select('caja_desglose.id as id')->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->where('caja_desglose.principal', 1)
            ->where('caja.moneda_id', $data['moneda_id'])
            ->where('caja.local_id', $data['local_id'])
            ->get()->row();

        return $cuenta != NULL ? $cuenta->id : NULL;
    }

    function update_saldo($id, $saldo, $ingreso = TRUE)
    {
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

    function save_pendiente($data, $id_usuario){

        $this->db->insert('caja_pendiente', array(
            'caja_desglose_id'=>$this->get_valid_cuenta_id($data['moneda_id'], $data['local_id'], $id_usuario),
            'usuario_id'=>$id_usuario,
            'tipo'=>$data['tipo'],
            'monto'=> $data['monto'],
            'estado'=>0,
            'IO'=>$data['IO'],
            'ref_id'=>$data['ref_id']
        ));
    }

    function get_valid_cuenta_id($moneda, $local, $id_usuario){
        $cuenta = $this->db->select('caja_desglose.id as id')->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->where('caja_desglose.principal', 1)
            ->where('caja.moneda_id', $moneda)
            ->where('caja.local_id', $local)
            ->get()->row();

        if($cuenta == NULL){
            $this->db->insert('caja', array(
                'local_id'=>$local,
                'moneda_id'=>$moneda,
                'responsable_id'=>$id_usuario,
                'estado'=>1
            ));
            $caja_id = $this->db->insert_id();

            $this->db->insert('caja_desglose', array(
                'caja_id'=>$caja_id,
                'responsable_id'=>$id_usuario,
                'descripcion'=>'Caja Temporal Principal',
                'saldo'=>0,
                'principal'=>1,
                'retencion'=>0,
                'estado'=>1,

            ));

            $cuenta_id = $this->db->insert_id();

            $this->db->where('caja_id', $caja_id);
            $this->db->where('id !=', $cuenta_id);
            $this->db->update('caja_desglose', array('principal'=>0));

            return $cuenta_id;
        }

        return $cuenta->id;
    }
}
