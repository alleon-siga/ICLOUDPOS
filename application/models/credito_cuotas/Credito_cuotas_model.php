
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class credito_cuotas_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	public function get_cuotas_by_venta_id($venta_id){
		$query = $this->db->select('*');
        $query = $this->db->from('credito_cuotas');
        $query = $this->db->where("id_venta",$venta_id);
       	$query = $this->db->get();
        return $query->result_array();
	}
	public function get_numero_de_creditos_by_local_actual($local_id = NULL){
		
		if (!$local_id) {
			$local_id = $this->session->userdata('id_local');
		}
		$query = $this->db->select('COUNT(*) as cuenta');
        $query = $this->db->from('venta');
        $query = $this->db->where(array('local_id'=>$local_id, 'condicion_pago'=>2));
       	$query = $this->db->get();
        return $query->result_array();


	}
	public function buscar_cuota_by_nro_letra($nro_letra){
		$query = $this->db->select('*');
        $query = $this->db->from('credito_cuotas');
        $query = $this->db->where(array('nro_letra' =>$nro_letra));
       	$query = $this->db->get();
        return $query->result_array();		
	}
	public function get_numero_letras_by_venta_id($venta_id){

		$query = $this->db->select('count(*) as cuenta');
        $query = $this->db->from('credito_cuotas');
        $query = $this->db->like('id_venta',$venta_id,'before');
       	$query = $this->db->get();
       	$result = $query->result_object();	
        return 	$result[0]->cuenta;
	}
	public function get_resumen_cuotas_by_venta_id($venta_id,$string = true){
		$query = $this->db->select('*');
        $query = $this->db->from('credito_cuotas');
        $query = $this->db->like('id_venta',$venta_id,'before');
        $query = $this->db->order_by('fecha_giro','asc');
       	$query = $this->db->get();
       	$result = $query->result_object();	
       	$cuotas = '';
		if ($string) {
			foreach ($result as $key => $value) {
				
				$nro_cuota = explode('-',$value->nro_letra);
				$fecha = explode(' ',$value->fecha_vencimiento);
				$fecha = $fecha[0];
				$fecha = explode('-',$fecha);
				$fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
				$cuotas.=" ".$nro_cuota[1]." cuota ".$fecha."   ".number_format($value->monto,2)."\\line ";
			}
			return $cuotas;
		}
		return $result;
	}

    /*esto lo hizo fernando*************************************/
    function get_cronograma_by_cuotas($venta)
    {
        /*este metodo busca todas las cuotas, y el ultimo pago que se le realizo a una cuota*/
        $query = "
            SELECT
                IF((d.ispagado = 1), '-', (DATEDIFF((d.fecha_vencimiento), CURDATE()) * -1)) as atraso,
                d.*, 
                du.monto_restante
            FROM
                credito_cuotas d
                    LEFT JOIN
                (SELECT 
                    t.monto_restante,
                        t.credito_cuota_id,
                        MAX(t.fecha_abono) AS fecha
                FROM
                    credito_cuotas_abono t
                JOIN credito_cuotas r ON r.id_credito_cuota = t.credito_cuota_id
                WHERE
                    r.id_venta = ".$venta." AND t.fecha_abono = r.ultimo_pago
                GROUP BY t.credito_cuota_id) du ON du.credito_cuota_id = d.id_credito_cuota
            WHERE
                d.id_venta = ".$venta."
            GROUP BY d.id_credito_cuota
        ";
        return $this->db->query($query)->result();;

    }

    public function update($where,$data){
        $this->db->trans_start();
        $this->db->where($where);
        $this->db->update('credito_cuotas', $data);

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();
    }

    public function update_bach($data,$when){
        $this->db->trans_start();
        $this->db->update_batch('credito_cuotas', $data,$when);

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            return true;
        }

        $this->db->trans_off();
    }


    public function get_pagocuotas_by_venta($where){
        /*este metodo trae todos los pagos por venta o  un pago en especifico*/

        $this->db->select('*, banco.banco_nombre as banco_nombre, tarjeta_pago.nombre as tarjeta_nombre');
        $this->db->from('credito_cuotas');
        $this->db->join('credito_cuotas_abono','credito_cuotas_abono.credito_cuota_id=credito_cuotas.id_credito_cuota');
        $this->db->join('metodos_pago','metodos_pago.id_metodo=credito_cuotas_abono.tipo_pago');
        $this->db->join('banco','banco.banco_id=credito_cuotas_abono.banco_id', 'left');
        $this->db->join('tarjeta_pago','tarjeta_pago.id=credito_cuotas_abono.banco_id', 'left');
        $this->db->where($where);
        $this->db->order_by('fecha_abono', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_cuotas($where){
        $this->db->select('*');
        $this->db->from('credito_cuotas');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_count($where){
        $this->db->select('count(id_credito_cuota) as pagadas');
        $this->db->from('credito_cuotas');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }



}