<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Comprobante_api_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->error = null;
    }

    public $error;

    function get_comprobantes($id = false)
    {
        if ($id != false)
            return $this->db->get_where('comprobantes', array('id' => $id))->row();

        return $this->db->get('comprobantes')->result();
    }

    function save($comprobante)
    {
        $this->db->trans_start();
        if (!isset($comprobante['id'])) {
            $valid_name = $this->db->get_where('comprobantes', array('nombre' => $comprobante['nombre']))->row();
            if ($valid_name != NULL) {
                $this->error = 'El nombre que desea guardar ya existe';
                $this->db->trans_off();
                return false;
            }

            $this->db->insert('comprobantes', $comprobante);
            $id = $this->db->insert_id();
        } else {
            $id = $comprobante['id'];
            $valid_name = $this->db->get_where('comprobantes', array(
                'nombre' => $comprobante['nombre'],
                'id !=' => $id
            ))->row();
            if ($valid_name != NULL) {
                $this->error = 'El nombre que desea guardar ya existe';
                $this->db->trans_off();
                return false;
            }

            $this->db->where('id', $id);
            $this->db->update('comprobantes', $comprobante);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $id;
    }

    function facturar($venta_id, $comprobante_id)
    {
        $this->db->trans_start();
        $comprobante = $this->get_comprobantes($comprobante_id);
        $cv = $this->db
            ->order_by('id', 'desc')
            ->get_where('comprobante_ventas', array(
                'comprobante_id' => $comprobante_id))
            ->row();

        if ($cv == NULL) {
            $correlativo = $comprobante->desde;
        } else {
            $correlativo = $cv->numero + 1;
        }

        if ($correlativo <= $comprobante->hasta) {
            $this->db->insert('comprobante_ventas', array(
                'venta_id' => $venta_id,
                'comprobante_id' => $comprobante_id,
                'numero' => sumCod($correlativo, $comprobante->longitud)
            ));
        } else {
            $this->error = 'El nombre que desea guardar ya existe';
            $this->db->trans_off();
            return false;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
}
