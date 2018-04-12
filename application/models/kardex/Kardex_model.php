<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class kardex_model extends CI_Model
{

    private $table = 'movimiento_historico';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('unidades/unidades_model');
    }

    public function set_kardex($data)
    {
        if (!isset($data['fecha']))
            $data['fecha'] = date('Y-m-d H:i:s');

        if (!isset($data['usuario_id']))
            $data['usuario_id'] = isset($data['id_usuario']) ? $data['id_usuario'] : $this->session->userdata('nUsuCodigo');

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

    public function get_kardex($where)
    {
        $this->db->select('*')->from('kardex');
        $this->db->join('usuario', 'usuario.nUsuCodigo = kardex.usuario_id')
            ->where('producto_id', $where['producto_id'])
            ->where('local_id', $where['local_id']);

        if (isset($where['mes']) && isset($where['year']) && isset($where['dia_min']) && isset($where['dia_max'])) {
            $last_day = last_day($where['year'], sumCod($where['mes'], 2));
            if ($last_day > $where['dia_max'])
                $last_day = $where['dia_max'];

            $this->db->where('fecha >=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $where['dia_min']);
            $this->db->where('fecha <=', $where['year'] . '-' . sumCod($where['mes'], 2) . '-' . $last_day);
        }
        elseif (isset($where['fecha_ini']) && isset($where['fecha_fin'])){
            $this->db->where('fecha >=', $where['fecha_ini'] . ' ');
            $this->db->where('fecha <=', $where['fecha_fin'] . ' ');
        }

        return $this->db->get()->result();
    }


}
