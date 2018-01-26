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
}