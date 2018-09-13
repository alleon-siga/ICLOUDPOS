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
         responsable.username as responsable, trabajador.nombre as trabajador,
         gastos.total as total, condiciones_pago.nombre_condiciones, documentos.des_doc, gastos.subtotal, gastos.impuesto');
        $this->db->join('tipos_gasto', 'tipos_gasto.id_tipos_gasto=gastos.tipo_gasto');
        $this->db->join('local', 'gastos.local_id=local.int_local_id');
        $this->db->join('moneda', 'moneda.id_moneda=gastos.id_moneda');
        $this->db->join('documentos', 'gastos.id_documento = documentos.id_doc');
        $this->db->join('usuario as trabajador', 'gastos.usuario_id=trabajador.nUsuCodigo', 'left');
        $this->db->join('usuario as responsable', 'gastos.responsable_id=responsable.nUsuCodigo');
        $this->db->join('proveedor', 'gastos.proveedor_id=proveedor.id_proveedor', 'left');
        $this->db->join('condiciones_pago', 'gastos.condicion_pago=condiciones_pago.id_condiciones', 'left');
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
        if(!empty($data['status_gastos'])){
            $this->db->where('status_gastos', $data['status_gastos']);    
        }
        return $this->db->get()->row();
    }

    function get_by($campo, $valor)
    {
        $this->db->select('gastos.*, caja_pendiente.monto, caja_pendiente.caja_desglose_id');
        $this->db->join('caja_pendiente', 'gastos.id_gastos = caja_pendiente.ref_id');
        $this->db->where($campo, $valor);
        $query = $this->db->get('gastos');
        return $query->row_array();
    }

    function insertar($data, $detalle)
    {

        $this->db->trans_start();

        $gastos = array(
            'fecha' => $data['fecha'],
            'fecha_registro' => $data['fecha_registro'],
            'descripcion' => $data['descripcion'],
            'total' => $data['total'],
            'tipo_gasto' => $data['tipo_gasto'],
            'local_id' => $data['local_id'],
            'status_gastos' => $data['status_gastos'],
            'gasto_usuario' => $data['gasto_usuario'],
            'id_moneda' => $data['moneda_id'],
            'tasa_cambio' => $data['tasa_cambio'],
            'proveedor_id' => $data['proveedor_id'],
            'usuario_id' => $data['usuario_id'],
            'responsable_id' => $data['responsable_id'],
            'gravable' => $data['gravable'],
            'id_documento' => $data['id_documento'],
            'serie' => $data['serie'],
            'numero' => $data['numero'],
            'id_impuesto' => $data['id_impuesto'],
            'subtotal' => ($data['gravable']=='0')? $data['total'] : $data['subtotal'],
            'impuesto' => $data['impuesto'],
            'condicion_pago' => $data['tipo_pago']
        );

        $this->db->insert('gastos', $gastos);
        $id = $this->db->insert_id();

        for($x=0; $x<count($detalle); $x++){
            $gastosDetalle = array(
                'id_gastos' => $id,
                'descripcion' => $detalle[$x]['descripcion'],
                'cantidad' => $detalle[$x]['cantidad'],
                'precio' => $detalle[$x]['precio'],
                'impuesto' => $detalle[$x]['impuesto'],
                'subtotal' => $detalle[$x]['subtotal'],
                'total' => $detalle[$x]['total']
            );
            $this->db->insert('gastos_detalle', $gastosDetalle);
        }

        if($data['tipo_pago']=='1'){
            $this->cajas_model->save_pendiente(array(
                'monto' => $data['total'],
                'tipo' => 'GASTOS',
                'IO' => 2,
                'ref_id' => $id,
                'cuenta_id' => $data['cuenta_id'],
                'local_id' => $data['local_id']
            ));
        }else{
            $tipo_gasto = $this->db->get_where('tipos_gasto', array('id_tipos_gasto' => $data['tipo_gasto']))->row();
            if($tipo_gasto->nombre_tipos_gasto == 'PRESTAMO BANCARIO'){
                $this->cajas_model->save_pendiente(array(
                    'monto' => $data['capital'],
                    'tipo' => 'GASTOS',
                    'IO' => 1,
                    'ref_id' => $id,
                    'cuenta_id' => $data['cuenta_id'],
                    'local_id' => $data['local_id']
                ));
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $id;
    }

    function update($data, $detalle)
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
            'numero' => $data['numero'],
            'id_impuesto' => $data['id_impuesto'],
            'subtotal' => $data['subtotal'],
            'impuesto' => $data['impuesto']
        );        
        $this->db->where('id_gastos', $data['id_gastos']);
        $this->db->update('gastos', $gastos);

        $this->cajas_model->editar_pendiente(array(
            'id' => $data['id_gastos'],
            'cuenta_id' => $data['cuenta_id'],
            'monto' => $data['total']
        ));

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

    function get_detalle($campo, $valor)
    {
        $this->db->select('id, descripcion, cantidad, precio, impuesto, subtotal, total');
        $this->db->where($campo, $valor);
        $query = $this->db->get('gastos_detalle');
        return $query->result();
    }

    function editarDetalle($detalle)
    {
        $this->db->trans_start();
        for($x=0; $x<count($detalle); $x++){
            $gastosDetalle = array(
                'descripcion' => $detalle[$x]['descripcion'],
                'cantidad' => $detalle[$x]['cantidad'],
                'precio' => $detalle[$x]['precio'],
                'impuesto' => $detalle[$x]['impuesto'],
                'subtotal' => $detalle[$x]['subtotal'],
                'total' => $detalle[$x]['total']
            );
            $this->db->where('id', $detalle[$x]['id']);
            $this->db->update('gastos_detalle', $gastosDetalle);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    function deleteDetalle($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('gastos_detalle');
        return true;
    }

    function get_totales_gasto2($params)
    {
        $this->db->select("SUM(g.subtotal) as subtotal, SUM(g.impuesto) as impuesto, SUM(g.total) as total, m.simbolo");
        $this->db->join('moneda m', 'g.id_moneda = m.id_moneda');
        $this->db->from('gastos g');
        $this->db->where('g.status_gastos', '0');
        $this->db->where("DATE(g.fecha) >= '".$params['fecha_ini']."' AND DATE(g.fecha) <= '".$params['fecha_fin']."'");
        
        if($params['local_id']>0){
            $this->db->where('g.local_id = '.$params['local_id']);
        }
        if($params['moneda_id']>0){
            $this->db->where('g.id_moneda = '.$params['moneda_id']);
        }
        if($params['doc_id']>0){
            $this->db->where('g.id_documento', $params['doc_id']);
        }
        return $this->db->get()->row();
    }
}
