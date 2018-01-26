<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class historial_cronograma_model extends CI_Model
{

    private $tabla="historial_cronograma";
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function traer_by($select = false, $from =false,  $join = false, $campos_join = false, $where = false,  $group = false,$order,$retorno = false){

        if($select !=false){
            $this->db->select($select);
            $this->db->from($from);
        }

        if($join != false and $campos_join != false){

            for($i=0;$i<count($join);$i++) {

                $this->db->join($join[$i], $campos_join[$i]);
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

    function guardar($lista_cronogramapago)
    {
        $this->db->trans_start();




        foreach ($lista_cronogramapago as $row) {

            $list_cp = array(
                'cronogramapago_id'=>$row->id_cronograma,
                'historialcrono_fecha' => date("Y-m-d H:i:s"),
                'historialcrono_monto'=>$row->cuota,
                'historialcrono_tipopago' => $row->metodo,
                'monto_restante'=>$row->monto_restante,
                'historialcrono_usuario'=>$this->session->userdata('nUsuCodigo'),
            	'id_caja'=>$row->id_moneda,
            	'tasa_cambio'=>$row->tasa_cambio
            );

            $this->db->insert($this->tabla,$list_cp);


            }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return true;
        }

        $this->db->trans_off();

    }

    function update($condicionespago)
    {

        $produc_exite=$this->get_by('nombre_condiciones', $condicionespago['nombre_condiciones']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_condiciones']==$condicionespago ['id_condiciones']))) {
            $this->db->trans_start();
            $this->db->where('id_condiciones', $condicionespago['id_condiciones']);
            $this->db->update('condiciones_pago', $condicionespago);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }


}