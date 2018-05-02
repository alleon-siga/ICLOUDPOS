<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class gastos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('cajas/cajas_model');
    }

    function set_gastos_where($data)
    {
        if (isset($data['local_id']))
            $this->db->where('gastos.local_id', $data['local_id']);

        if (isset($data['id_moneda']))
            $this->db->where('gastos.id_moneda', $data['id_moneda']);

        if (isset($data['status_gastos']) && $data['status_gastos'] != '')
            $this->db->where('gastos.status_gastos', $data['status_gastos']);

        if (isset($data['proveedor']))
            $this->db->where('gastos.proveedor_id', $data['proveedor']);

        if (isset($data['usuario']))
            $this->db->where('gastos.usuario_id', $data['usuario']);

        if (isset($data['persona_gasto']) && $data['persona_gasto'] == 1)
            $this->db->where('gastos.proveedor_id !=', NULL);

        if (isset($data['persona_gasto']) && $data['persona_gasto'] == 2)
            $this->db->where('gastos.usuario_id !=', NULL);

        if (isset($data['tipo_gasto']))
            $this->db->where('gastos.tipo_gasto', $data['tipo_gasto']);


        $this->db->where('gastos.fecha >=', $data['fecha_ini']);
        $this->db->where('gastos.fecha <=', $data['fecha_fin']);

    }

    function get_all($data = array())
    {
        $this->db->select('*, moneda.*,
         responsable.nombre as responsable, trabajador.nombre as trabajador,
         gastos.total as total');
        $this->db->join('tipos_gasto', 'tipos_gasto.id_tipos_gasto=gastos.tipo_gasto');
        $this->db->join('local', 'gastos.local_id=local.int_local_id');
        $this->db->join('moneda', 'moneda.id_moneda=gastos.id_moneda');
        $this->db->join('usuario as trabajador', 'gastos.usuario_id=trabajador.NusuCodigo', 'left');
        $this->db->join('usuario as responsable', 'gastos.responsable_id=responsable.NusuCodigo');
        $this->db->join('proveedor', 'gastos.proveedor_id=proveedor.id_proveedor', 'left');


        $this->set_gastos_where($data);


        return $this->db->get('gastos')->result_array();
    }

    function get_totales_gasto($data)
    {
        $this->db->select("
            SUM(gastos.total) as total
            ")
            ->from('gastos');

        $this->set_gastos_where($data);
        $this->db->where('status_gastos', 0);

        return $this->db->get()->row();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('gastos');
        return $query->row_array();
    }

    function insertar($data)
    {

        $this->db->trans_start();

        $cuenta = $this->db->join('caja', 'caja.id = caja_desglose.caja_id')
            ->get_where('caja_desglose', array('caja_desglose.id' => $data['cuenta_id']))->row();

        $gastos = array(
            'fecha' => $data['fecha'],
            'fecha_registro' => $data['fecha_registro'],
            'descripcion' => $data['descripcion'],
            'total' => $data['total'],
            'tipo_gasto' => $data['tipo_gasto'],
            'local_id' => $data['local_id'],
            'gasto_usuario' => $data['gasto_usuario'],
            'id_moneda' => $cuenta->moneda_id,
            'tasa_cambio' => 0,
            'proveedor_id' => $data['proveedor_id'],
            'usuario_id' => $data['usuario_id'],
            'responsable_id' => $data['responsable_id'],
            'gravable' => $data['gravable'],
            'id_documento' => $data['id_documento'],
            'serie' => $data['serie'],
            'numero' => $data['numero']
        );

        $this->db->insert('gastos', $gastos);
        $id = $this->db->insert_id();

        $this->cajas_model->save_pendiente(array(
            'monto' => $data['total'],
            'tipo' => 'GASTOS',
            'IO' => 2,
            'ref_id' => $id,
            'cuenta_id' => $data['cuenta_id'],
            'local_id' => $data['local_id']
        ));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $id;
    }

    function update($gastos)
    {

        $this->db->trans_start();
        $this->db->where('id_gastos', $gastos['id_gastos']);
        $this->db->update('gastos', $gastos);

        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    public
    function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $group = false,
                      $order = false, $retorno = false)
    {


        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);


        }

        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);

        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }
}