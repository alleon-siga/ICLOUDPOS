<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto_serie_model extends CI_Model
{

    private $table = 'producto_series';

    function __construct()
    {
        parent::__construct();
    }

    function get_all()
    {

        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    function get_by($where = array())
    {

        $query = $this->db->get_where($this->table, $where);
        return $query->result_array();
    }

    function insertar($campos)
    {
        if (isset($campos->serie)) {
            if (trim($campos->serie) != "")
                $this->db->insert($this->table, $campos);
        } elseif (isset($campos['serie'])) {
        
            if (trim($campos['serie']) != "")
                $this->db->insert($this->table, $campos);
        }
    }

    function update($data)
    {

        foreach ($data as $d) {
            if (isset($d->serie) && trim($d->serie) != "") {
                $this->db->where(array('id' => $d->id));
                $this->db->update($this->table, array('serie' => $d->serie));
            } elseif (trim($d->serie) == '')
                $this->db->delete($this->table, array('id' => $d->id));
        }
    }

    function delete($serie)
    {
        $this->db->where('id', $serie['id']);
        $this->db->delete($this->table, $serie);
        return TRUE;
    }
}
