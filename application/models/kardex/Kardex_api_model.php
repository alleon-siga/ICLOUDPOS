<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Kardex_api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('unidades/unidades_api_model');
    }

    public function set_kardex($data, $id_usuario)
    {
        if (!isset($data['fecha']))
            $data['fecha'] = date('Y-m-d H:i:s');

        $data['usuario_id'] = $id_usuario;

        if(!isset($data['unidad_id'])){
            $orden_max = $this->db->select_max('orden', 'orden')
                ->where('producto_id', $data['producto_id'])->get('unidades_has_producto')->row();

            $minima_unidad = $this->db->select('id_unidad as um_id')
                ->where('producto_id', $data['producto_id'])
                ->where('orden', $orden_max->orden)
                ->get('unidades_has_producto')->row();

            $data['unidad_id'] = $minima_unidad->um_id;
        }

        $last = $this->db->order_by('fecha', 'DESC')
            ->get_where('kardex', array(
                'producto_id' => $data['producto_id'],
                'local_id' => $data['local_id']
            ))->row();

        $cantidad_saldo = 0;
        if ($last != NULL)
            $cantidad_saldo = $last->cantidad_saldo;

        if ($data['io'] == 1) {
            $data['cantidad_saldo'] = $data['cantidad'] + $cantidad_saldo;
        } elseif ($data['io'] == 2) {
            $data['cantidad_saldo'] = $cantidad_saldo - $data['cantidad'];
        }

        $this->db->insert('kardex', $data);
        return $this->db->insert_id();
    }

}
