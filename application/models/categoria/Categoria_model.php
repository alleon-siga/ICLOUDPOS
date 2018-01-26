<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class categoria_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_all_tipo()
    {
      $query = $this->db->get('pl_tipo');
      return $query->result_array();
    }

    function get_all()
    {
        $query = $this->db->select('pl_propiedad.id,pl_tipo.tipo,pl_propiedad.valor');
        $query = $this->db->from('pl_propiedad');
        $query = $this->db->join('pl_tipo', 'pl_propiedad.pl_tipo_id=pl_tipo.id');
        $query = $this->db->where('pl_propiedad.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
      $this->db->where($campo, $valor);
      $this->db->where('pl_propiedad.estado', 1);
      $query = $this->db->get('pl_propiedad');
      return $query->row_array();
    }

    function get_all_by($campo, $valor)
    {
      $this->db->where($campo, $valor);
      $this->db->where('pl_propiedad.estado', 1);
      $query = $this->db->get('pl_propiedad');
      return $query->result_array();
    }

    function insertar($categoria)
    {
      $this->db->trans_start();
      $this->db->insert('pl_propiedad', $categoria);

      $this->db->trans_complete();

      if ($this->db->trans_status() === FALSE)
          return FALSE;
      else
          return TRUE;
    }

    function update($categoria)
    {
        $this->db->trans_start();
        $this->db->where('id', $categoria['id']);
        $this->db->update('pl_propiedad', $categoria);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
}
