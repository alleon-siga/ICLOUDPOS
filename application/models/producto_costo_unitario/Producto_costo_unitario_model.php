<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto_costo_unitario_model extends CI_Model
{

    private $table = 'producto_costo_unitario';

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('unidades/unidades_model');
        $this->load->model('monedas/monedas_model');
    }

    public function save_costos($data = array(), $tasa = 0)
    {
        $this->db->where('producto_id', $data['producto_id']);
        $this->db->delete($this->table);

        $data['costo'] = $data['costo'];

        if (isset($data['contable_costo'])) {
            $contable_costo = $data['contable_costo'];
            $cont_act = $data['contable_activo'];
        } else {
            $c_contable = $this->db->get_where($this->table, array(
                'producto_id' => $data['producto_id'],
                'moneda_id' => $data['moneda_id']))->row();

            $contable_costo = $c_contable != NULL ? $c_contable->contable_costo : 0;
            $cont_act = $data['moneda_id'];
        }


        if ($cont_act == $data['moneda_id']) {
            $data['contable_costo'] = $contable_costo;
            $data['contable_activo'] = '1';
        } else {
            $data['contable_costo'] = 0;
            $data['contable_activo'] = '0';
        }

        $this->db->insert($this->table, $data);

        $costo = $this->db->get_where($this->table, array(
            'producto_id' => $data['producto_id'],
            'moneda_id' => $data['moneda_id']))->row();


        $monedas = $this->monedas_model->get_monedas_activas();
        $moneda_costo = $this->db->get_where('moneda', array('id_moneda' => $costo->moneda_id))->row();

        foreach ($monedas as $m) {
            $values = array(
                'producto_id' => $data['producto_id'],
                'contable_activo' => '0',
                'contable_costo' => 0,
                'tipo_impuesto_compra' => $data['tipo_impuesto_compra']
            );

            if ($m->id_moneda != $costo->moneda_id) {
                $values['moneda_id'] = $m->id_moneda;
                $values['activo'] = '0';

                if ($cont_act == $m->id_moneda) {
                    $values['contable_activo'] = '1';
                    $values['contable_costo'] = $contable_costo;
                }

                if ($m->id_moneda == MONEDA_DEFECTO) {
                    $values['costo'] = $data['costo'] * $moneda_costo->tasa_soles;
                } else {
                    if ($moneda_costo->id_moneda == MONEDA_DEFECTO) {
                        $values['costo'] = $data['costo'] / $m->tasa_soles;
                    } else {
                        $values['costo'] = $data['costo'] * $moneda_costo->tasa_soles / $m->tasa_soles;
                    }
                }

                $this->db->insert($this->table, $values);
            }
        }


        $costo = $this->db->get_where($this->table, array(
            'producto_id' => $data['producto_id'],
            'moneda_id' => $cont_act,
            'contable_activo' => '1'))->row();

        $moneda_costo = $this->db->get_where('moneda', array('id_moneda' => $costo->moneda_id))->row();

        foreach ($monedas as $m) {
            $values = array();
            if ($m->id_moneda != $costo->moneda_id) {

                if ($m->id_moneda == MONEDA_DEFECTO) {
                    $values['contable_costo'] = $data['contable_costo'] * $moneda_costo->tasa_soles;
                } else {
                    if ($moneda_costo->id_moneda == MONEDA_DEFECTO) {
                        $values['contable_costo'] = $data['contable_costo'] / $m->tasa_soles;
                    } else {
                        $values['contable_costo'] = $data['contable_costo'] * $moneda_costo->tasa_soles / $m->tasa_soles;
                    }
                }

                $this->db->where(array(
                    'producto_id' => $data['producto_id'],
                    'moneda_id' => $m->id_moneda));
                $this->db->update($this->table, $values);
            }
        }
    }


    public function get_costos($producto_id = 0)
    {
        $monedas = $this->monedas_model->get_monedas_activas();
        $monedas_cu = $this->db->get_where('producto_costo_unitario', array('producto_id' => $producto_id))->result();
        $result = array();

        if (($producto_id == 0 && count($monedas) != 0) && count($monedas_cu) == 0) {
            foreach ($monedas as $m) {
                $values = $this->prepare_costo($m);
                if ($m->id_moneda == MONEDA_DEFECTO) {
                    $values['cu_activo'] = '1';
                    $values['cu_contable_activo'] = '1';
                }

                $result[] = $values;
            }
            return $result;
        }

        if (count($monedas_cu) != 0) {
            foreach ($monedas as $m) {
                $values = $this->prepare_costo($m);
                foreach ($monedas_cu as $mcu) {
                    if ($mcu->moneda_id == $values['moneda_id']) {
                        $values['cu_costo'] = $mcu->costo;
                        $values['cu_contable_costo'] = $mcu->contable_costo;

                        $values['cu_activo'] = $mcu->activo;
                        $values['cu_contable_activo'] = $mcu->contable_activo;
                    }
                }
                $result[] = $values;
            }
            return $result;
        } elseif ($producto_id != 0) {
            $producto = $this->db->get_where('producto', array('producto_id' => $producto_id))->row();
            foreach ($monedas as $m) {
                $values = $this->prepare_costo($m);
                $values['cu_costo'] = $producto->producto_costo_unitario;
                $values['cu_contable_costo'] = 0;
                if ($m->id_moneda == MONEDA_DEFECTO) {
                    $values['cu_activo'] = '1';
                    $values['cu_contable_activo'] = '1';
                }

                $result[] = $values;
            }
            return $result;
        }

        return false;
    }

    public function get_costo_activo($producto_id)
    {
        $result = $this->get_costos($producto_id);
        foreach ($result as $costo_activo)
            if ($costo_activo['cu_activo'] == '1')
                return $costo_activo;

        return null;
    }

    public function get_costo_contable_activo($producto_id)
    {
        $result = $this->get_costos($producto_id);
        foreach ($result as $costo_contable_activo)
            if ($costo_contable_activo['contable_activo'] == '1')
                return $costo_contable_activo;

        return null;
    }

    private function prepare_costo($obj)
    {
        return array(
            'moneda_id' => $obj->id_moneda,
            'moneda_nombre' => $obj->nombre,
            'moneda_simbolo' => $obj->simbolo,
            'moneda_tasa' => $obj->tasa_soles,
            'moneda_oper' => $obj->ope_tasa,
            'cu_costo' => 0.00,
            'cu_contable_costo' => 0.00,
            'cu_activo' => 0,
            'cu_contable_activo' => 0
        );
    }

    function getProductoCostoUnitario($id_producto)
    {
        $this->db->select('pcu.costo, pcu.tipo_cambio, pcu.moneda_id, pcu.contable_costo, pcu.porcentaje_utilidad');
        $this->db->from('producto_costo_unitario pcu');
        $this->db->where('pcu.producto_id', $id_producto);
        $query = $this->db->get();
        $datos = $query->result();

        $arr = array();
        foreach($datos as $dato){
            $arr['costo'][$dato->moneda_id] = $dato->costo;
            $arr['tipo_cambio'] = $dato->tipo_cambio;
            $arr['contable_costo'][$dato->moneda_id] = $dato->contable_costo;
            $arr['porcentaje_utilidad'] = $dato->porcentaje_utilidad;
        }
        return $arr;
    }

    function getCostoUnitarioVenta($param)
    {
        $moneda_id = $param['moneda_id'];
        $producto_id = $param['producto_id'];

        $this->db->select('pcu.contable_costo, pcu.porcentaje_utilidad, pcu.producto_id');
        $this->db->from('producto_costo_unitario pcu');
        $this->db->where('pcu.moneda_id', $moneda_id);
        $this->db->where_in('pcu.producto_id', $producto_id);
        $query = $this->db->get();
        $datos = $query->result();
        $arr = array();
        foreach($datos as $dato){
            $arr['contable_costo'][$dato->producto_id] = $dato->contable_costo;
            $arr['porcentaje_utilidad'][$dato->producto_id] = $dato->porcentaje_utilidad;
        }
        return $arr;
    }

    function check_costo_unitario($datos)
    {
        foreach ($datos as $data) {
            $this->db->select('COUNT(*) AS num');
            $this->db->from('producto_costo_unitario');
            $this->db->where('producto_id', $data['producto_id']);
            $this->db->where('moneda_id', $data['moneda_id']);
            $row = $this->db->get()->row();
            if($row->num<1){
                return false;
            }
        }
        return true;
    }
}