<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuario_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    
    function insertar_usu_almacen($usu,$local)
    {
    	$this->db->insert('usuario_almacen', array(
    			'usuario_id' => $usu,
    			'local_id' => $local
    	));
    }

    function insertar($usu,$local_array)
    {
        $nombre = $this->input->post('username');
        $validar_nombre = sizeof($this->get_by('username', $nombre));

        if ($validar_nombre < 1) {
        	$this->db->trans_start();
            if ($this->db->insert('usuario', $usu)) {
             if(!empty($local_array)) {
             	$usuario = $this->get_by('username', $nombre);
            	 foreach ($local_array as $item => $value) {
            	 	$this->insertar_usu_almacen($usuario["nUsuCodigo"], $value);
            	 }
            	 $this->db->trans_complete();
             	return true;
             }else{
             	return false;
             }
                
            } else {
                return false;
            }
        } else {
            return USERNAME_EXISTE;
        }
    }
    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get('usuario');
        return $query->row_array();
    }
    
    function eliminar_usuario_almacen($id_usu)
    {
    	$this->db->where('usuario_id',$id_usu);
    	$this->db->delete('usuario_almacen');
    }


    function update($usu, $local_array)
    {

        $produc_exite = $this->get_by('username', $usu['username']);
        $validar_nombre = sizeof($produc_exite);

        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['nUsuCodigo'] == $usu ['nUsuCodigo']))) {
            $this->db->trans_start();
            $this->db->where('usuario.nUsuCodigo', $usu['nUsuCodigo']);
            if ($this->db->update('usuario', $usu)) {

            	if(!empty($local_array)) {
            		$this->eliminar_usuario_almacen($usu['nUsuCodigo']);
            		foreach ($local_array as $item => $value) {
            			$this->insertar_usu_almacen($usu['nUsuCodigo'], $value);
            		}

            	}
                $this->db->trans_complete();
                $this->db->trans_off();
                return true;
            } else {
                $this->db->trans_complete();
                $this->db->trans_off();
                return false;
            }

        } else {

            return USERNAME_EXISTE;
        }
    }
    
    function update_estatus($usu)
    {
    
    	$produc_exite = $this->get_by('username', $usu['username']);
    	$validar_nombre = sizeof($produc_exite);
    	if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['nUsuCodigo'] == $usu ['nUsuCodigo']))) {
    		$this->db->where('usuario.nUsuCodigo', $usu['nUsuCodigo']);
    		if ($this->db->update('usuario', $usu)) 
    		{
    			return true;
    		} else {
    				 return false;
    			   }
    	} else {
    		return USERNAME_EXISTE;
    	}
   }
    


    function delete($id)
    {
        $data = array('activo' => '0');
        $this->db->where('nUsuCodigo', $id);
        if ($this->db->update('usuario', $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    function buscar_almacenes($id)
    {
    	$this->db->select('*');
    	$this->db->from('usuario_almacen');
    	$this->db->where('usuario_id', $id);
    	$query = $this->db->get();
    	return $query->result();
    }

    function buscar_id($id)
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $this->db->where('grupos_usuarios.nombre_grupos_usuarios !=', "CEO APLICATION");
       // $this->db->join('local', 'local.int_local_id=usuario.id_local');
        //$this->db->join('usuario_almacen', 'usuario_almacen.usuario_id=usuario.nUsuCodigo');
        $this->db->where('nUsuCodigo', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function select_all_user()
    {
        $this->db->select('usuario.*,grupos_usuarios.* ');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        //$this->db->where('usuario.esSuper != 1');
        $this->db->where('usuario.deleted', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function select_lista_local($id_user)
    {
        $query = $this->db->query("SELECT l.int_local_id as id_local, l.var_local_nombre as name_local,
								   IFNULL((select lu.var_detLocal_estado from local_has_usuario lu 
										   where l.int_local_id = lu.int_local_id and lu.nUsuCodigo =" . $id_user . "),0) as estado
								   FROM local l");
        return $query->result_array();
    }

   /* public function get_ultima_sol($where)
    {
        $this->db->select('ultima_sol');
        $this->db->from('usuario');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row_array();
    }*/

}
