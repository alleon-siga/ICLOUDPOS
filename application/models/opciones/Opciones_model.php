<?php

/**
 * Created by IntelliJ IDEA.
 * User: Jhainey
 * Date: 18/03/2015
 * Time: 11:56 AM
 */
class opciones_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function guardar_configuracion($configuraciones)
    {


        $this->db->trans_start();


        foreach ($configuraciones as $conf) {
            if (!empty($conf)) {
                if(count($this->get_opcion($conf['config_key']))==0){
                    $this->db->insert('configuraciones', array(
                        'config_key' => $conf['config_key'],
                        'config_value' => $conf['config_value']));
                }
                else {
                    $this->db->where('config_key', $conf['config_key']);
                    $this->db->update('configuraciones', $conf);
                }
            }
        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


    public function get_opciones($keys = array())
    {
        $this->db->select('*');
        $this->db->from('configuraciones');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_opcion($key)
    {
    	$where = array(
    		"config_key" => $key	
    	);
    	
    	$this->db->select('*');
    	$this->db->from('configuraciones');
    	$this->db->where($where);
    	$query = $this->db->get();
    	return $query->result_array();
    }

    public function get_version()
    {
        $this->db->select('*');
        $this->db->from('version');
        $query = $this->db->get();
        return $query->row();
    }
}
?>