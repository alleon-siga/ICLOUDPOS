<?php

/**
 * made by jaimeirazabal1@gmail.com
 * 02/06/2016
 * 14:34 vz
 */
class correlativos_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
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

    function save_correlativos($local_id, $correlativos)
    {
        $this->db->where('id_local', $local_id);
        $this->db->delete('correlativos');

        foreach ($correlativos as $correlativo) {
            $this->db->insert('correlativos', $correlativo);
        }

        return true;
    }

    public function sumar_correlativo($local_id, $documento_id)
    {
        $correlativo = $this->get_correlativo($local_id, $documento_id);
        $this->update_correlativo($local_id, $documento_id, array('correlativo' => $correlativo->correlativo + 1));
    }

    public function update_correlativo($local_id, $documento_id, $data = array())
    {
        $this->db->where(array(
            'id_local' => $local_id,
            'id_documento' => $documento_id
        ));
        $this->db->update('correlativos', $data);
    }

    public function update_nota_pedido($local_id, $venta_id)
    {
        $this->update_correlativo($local_id, 6, array('correlativo' => $venta_id));
    }

    public function get_by($where)
    {
        return $this->db->get_where('correlativos', $where)->result();
    }


}