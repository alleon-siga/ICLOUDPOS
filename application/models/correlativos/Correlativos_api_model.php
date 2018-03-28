<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Correlativos_api_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function update_nota_pedido($local_id, $venta_id)
    {
        $this->update_correlativo($local_id, 6, array('correlativo' => $venta_id));
    }

    public function update_correlativo($local_id, $documento_id, $data = array())
    {
        $this->db->where(array(
            'id_local' => $local_id,
            'id_documento' => $documento_id
        ));
        $this->db->update('correlativos', $data);
    }

    public function get_correlativo($local_id, $documento_id)
    {
        $correlativo = $this->db->get_where('correlativos', array(
            'id_local' => $local_id,
            'id_documento' => $documento_id
        ))->row();

        if ($correlativo == NULL) {
            $this->db->insert('correlativos', array(
                'id_local' => $local_id,
                'id_documento' => $documento_id,
                'serie' => '0001',
                'correlativo' => '1'
            ));

            $correlativo = $this->db->get_where('correlativos', array(
                'id_local' => $local_id,
                'id_documento' => $documento_id
            ))->row();
        }

        if ($documento_id == 6){
            $next = $this->db->query("SHOW TABLE STATUS LIKE 'venta'")->row();
            $correlativo->correlativo = $next->Auto_increment;
        }

        return $correlativo;
    }

    public function sumar_correlativo($local_id, $documento_id)
    {
        $correlativo = $this->get_correlativo($local_id, $documento_id);
        $this->update_correlativo($local_id, $documento_id, array('correlativo' => $correlativo->correlativo + 1));
    }
}