<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gastos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function set_gastos_where($data){
        if(isset($data['local_id']))
            $this->db->where('gastos.local_id', $data['local_id']);

        if(isset($data['proveedor']))
            $this->db->where('gastos.proveedor_id', $data['proveedor']);

        if(isset($data['usuario']))
            $this->db->where('gastos.usuario_id', $data['usuario']);

        if(isset($data['persona_gasto']) && $data['persona_gasto'] == 1)
            $this->db->where('gastos.proveedor_id !=', NULL);

        if(isset($data['persona_gasto']) && $data['persona_gasto'] == 2)
            $this->db->where('gastos.usuario_id !=', NULL);

        if(isset($data['tipo_gasto']))
            $this->db->where('gastos.tipo_gasto', $data['tipo_gasto']);

        if(isset($data['mes']) && isset($data['year']) && isset($data['dia_min']) && isset($data['dia_max'])){
            $last_day = last_day($data['year'], sumCod($data['mes'], 2));
            if($last_day > $data['dia_max'])
                $last_day = $data['dia_max'];


                $this->db->where('gastos.fecha >=' ,$data['year'] . '-' . sumCod($data['mes'], 2) . '-'. $data['dia_min']. " 00:00:00");
                $this->db->where('gastos.fecha <=', $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $last_day. " 23:59:59");
        }
    }

    function get_all($data = array())
    {
        $this->db->select('*, moneda.nombre as moneda_nombre,
         responsable.nombre as responsable, trabajador.nombre as trabajador,
         gastos.total * IF(gastos.tasa_cambio=0, 1 ,gastos.tasa_cambio) as total');
        $this->db->join('tipos_gasto', 'tipos_gasto.id_tipos_gasto=gastos.tipo_gasto');
        $this->db->join('local', 'gastos.local_id=local.int_local_id');
        $this->db->join('moneda', 'moneda.id_moneda=gastos.id_moneda');
        $this->db->join('usuario as trabajador', 'gastos.usuario_id=trabajador.NusuCodigo', 'left');
        $this->db->join('usuario as responsable', 'gastos.responsable_id=responsable.NusuCodigo');
        $this->db->join('proveedor', 'gastos.proveedor_id=proveedor.id_proveedor', 'left');
        

        $this->set_gastos_where($data);



        return $this->db->get('gastos')->result_array();
    }

    function get_totales_gasto($data){
        $this->db->select("
            SUM(gastos.total * IF(gastos.tasa_cambio=0, 1 ,gastos.tasa_cambio)) as total
            ")
        ->from('gastos');

        $this->set_gastos_where($data);
        $this->db->where('status_gastos', 1);

        return $this->db->get()->row();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('gastos');
        return $query->row_array();
    }

    function insertar($gastos)
    {

        $this->db->trans_start();
        $this->db->insert('gastos', $gastos);
        $id = $this->db->insert_id();

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

    public function traer_by($select = false, $from =false,  $join = false, $campos_join = false,$tipo_join, $where = false,  $group = false,
                             $order = false,$retorno = false){


        if($select !=false){
            $this->db->select($select);
            $this->db->from($from);


        }

        if($join != false and $campos_join != false){

            for($i=0;$i<count($join);$i++) {

                if($tipo_join!=false){

                    for($t=0;$t<count($tipo_join);$t++) {

                        if($tipo_join[$t]!=""){

                            $this->db->join($join[$i], $campos_join[$i],$tipo_join[$t]);
                        }

                    }

                }else{

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if($where!=false){
            $this->db->where($where);

        }
        if($group!=false){
            $this->db->group_by($group);
        }

        if($order!=false){
            $this->db->order_by($order);
        }

        $query=$this->db->get();

        if($retorno=="RESULT_ARRAY"){

            return $query->result_array();
        }elseif($retorno=="RESULT"){
            return $query->result();

        }else{
            return $query->row_array();
        }

    }
}