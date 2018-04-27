<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function check_operaciones($id){
        $this->db->where('id_cliente', $id);
        $this->db->from('venta');
        $counter = $this->db->count_all_results();
        echo 'cantdad'.$counter;
        if($counter > 0) return FALSE;

        return TRUE;
    }

    function get_all()
    {

       // $this->db->join('usuario', 'usuario.nUsuCodigo=cliente.vendedor_a','left');
        $this->db->where('cliente_status', 1);
        //$this->db->join('ciudades', 'ciudades.ciudad_id=cliente.ciudad_id');
        //$this->db->join('estados', 'ciudades.estado_id=estados.estados_id');
        //$this->db->join('pais', 'pais.id_pais=estados.pais_id');
        $this->db->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id');
        $this->db->join('ciudades c', 'c.ciudad_id=cliente.ciudad_id', 'left');
        //$this->db->join('precios', 'precios.id_precio=cliente.categoria_precio','left');
        $this->db->order_by('id_cliente', 'DESC');
        $query = $this->db->get('cliente');
        return $query->result_array();
    }
    function get_by($campo, $valor)
    {
        $this->db->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id');
        $this->db->where($campo, $valor);
        //$this->db->join('usuario', 'usuario.nUsuCodigo=cliente.vendedor_a','left');
        $query = $this->db->get('cliente');
        return $query->row_array();
    }


    function get_by_valor($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $this->db->join('cliente_tipo_campo', 'cliente_tipo_campo.id_cliente=cliente.id_cliente');
        $this->db->join('cliente_campo_valor', 'cliente_campo_valor.campo_cliente=cliente_tipo_campo.valor');
        $this->db->join('usuario', 'usuario.nUsuCodigo=cliente.vendedor_a');
        $this->db->order_by('cliente_campo_valor.campo_cliente', 'ASC');
        $query = $this->db->get('cliente');
        return $query->result_array();
    }

    function get_all_user()
    {

        $this->db->order_by('id_cliente', 'ASC');
        $query = $this->db->get('cliente');
        return $query->result_array();

    }

    function insertar($cliente)
    {


            /*hago el insert de los datos del cliente*/
            $this->db->insert('cliente', $cliente);
            return $this->db->insert_id();

    }

    /*function insertarCM($campos, $option, $options, $cliente)
   {
       $identificacion = $cliente['ruc'];
       $dni = $cliente['dni'];
       $validar_nombre = sizeof($this->get_by('ruc', $identificacion));
       $validar_dni = sizeof($this->get_by('dni', $dni));
       //$validar_nombre = 2;
       if (($validar_nombre > 1) OR ($validar_dni > 1)) {
           $this->db->trans_start();
           $this->db->select('*');
           $this->db->from('cliente');
           $this->db->order_by("id_cliente", "desc");
           $this->db->limit(1);
           $query = $this->db->get();
           $vea = $query->row();
           $idd = $vea->id_cliente; // id dle cliente

           foreach ($campos as $campo) {
               if (!empty($campo)) {
                   $ven = array(
                       'id_cliente' => $idd,
                       'valor' => $campo

                   );
                   $this->db->insert('cliente_tipo_campo', $ven);

               }
           }


           foreach ($option as $campo) {
               if (!empty($campo)) {
                   $this->db->set('campo_cliente', $options);
                   $this->db->set('campo_valor', $campo);
                   $this->db->insert('cliente_campo_valor');

               }
           }


           try {
               $this->db->trans_complete();
           } catch (Exception $e) {
               return $this->db->_error_message();
           }

           if ($this->db->trans_status() === FALSE) {
               return $this->db->_error_message();
           } else {
               return TRUE;
           }
       } else {
           return CEDULA_EXISTE;
       }
   } */


    function update($cliente)
    {
        $identificacion = $cliente['dni'];

        $produc_exite_dni = sizeof($this->get_by('dni', $identificacion));
        $produc_exite_ruc=$this->cliente_model->get_by('ruc', $cliente['ruc']);

        if (((sizeof($this->cliente_model->get_by('dni', $cliente['dni'])) or
                (sizeof($this->cliente_model->get_by('ruc', $cliente['ruc'])))) < 1) OR (((sizeof($this->cliente_model->get_by('dni', $cliente['dni'])) or
                        (sizeof($this->cliente_model->get_by('ruc', $cliente['ruc'])))) > 0) AND (($produc_exite_dni['id_cliente'] or $produc_exite_ruc['id_cliente'] ) == $cliente['id_cliente']))) {

            $this->db->trans_start();
            $this->db->where('id_cliente', $cliente['id_cliente']);
            $this->db->update('cliente', $cliente);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return CEDULA_EXISTE;
        }
    }

    function eliminarCampo($id_campo){
        $this->db->trans_start();

        $this->db->where('id_campo', $id_campo);
        $this->db->delete('cliente_campo_valor');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }

    function updateD($cliente)
    {

        $this->db->trans_start();
        $this->db->where('id_cliente', $cliente['id_cliente']);
        $this->db->update('cliente', $cliente);
        $vendedr = $this->input->post('vendedor', true);
        /* $fech = date('y-m-d');
         if(!empty($vendedr) && $vendedr!=0) {
             $ven = array(
                 'f_asinacion' => $fech,
                 'id_cliente' => $cliente['id_cliente'],
                 'id_vendedor' => $vendedr,
             );
             $this->db->insert('cliente_v', $ven);
         }*/
        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
            return FALSE;
        } else {
            return TRUE;

        }
    }

    function updateR($id_campo, $campo_Editar)
    {

        if (count($campo_Editar) == count($id_campo)) {
            for ($i = 0; $i < count($campo_Editar); $i++) {
                $this->db->update('cliente_campo_valor', array('campo_valor' => $campo_Editar[$i]), "id_campo =" . $id_campo[$i]);
            }
        }

    }

    function get_total_cuentas_por_cobrar()
    {
        $sql = "SELECT SUM(dec_cronpago_pagocuota) as suma FROM `cronogramapago` WHERE dec_cronpago_pagorecibido = 0.00";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    function buscarRUC_DNI($data = array())
    {

        $this->db->where('dni', $data['dni_cliente']);
        $this->db->where_or('ruc', $data['ruc_cliente']);
        $resultado=$this->db->get('cliente');

        if ($resultado->num_rows() > 0)
        {
          return $resultado->num_rows() ;
        }
        else
          return 0;

    }


}