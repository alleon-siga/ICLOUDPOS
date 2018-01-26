<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plantilla_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

	public function get_plantilla_select(){
		$plantillas = $this->get_plantillas();

		$result = array();

		foreach ($plantillas as $plantilla) {
			$temp = new stdClass();
			$temp->id = $plantilla->id;
			$temp->nombre = $plantilla->nombre;

			foreach ($plantilla->propiedades as $prop) {
			 	if($prop->propiedad_valor != "")
			 		$temp->nombre .= ', '.$prop->propiedad_valor;
			 } 

			 $result[] = $temp;
		}
		return $result;

	}

	public function get_plantillas($id = FALSE){
		$headers = $this->get_header();

		if($id == FALSE)
			$productos = $this->db->get('pl_producto')->result();
		else
			$productos = $this->db->get_where('pl_producto', array('id'=>$id))->result();

		foreach ($productos as $producto) {
			$producto->propiedades = array();

			$props = $this->db->select('
				t.id AS tipo_id,
				t.tipo AS tipo,
				prop.id AS propiedad_id,
				prop.valor AS propiedad_valor
				')
				->from('pl_tipo AS t')
				->join('pl_propiedad AS prop', 'prop.pl_tipo_id = t.id')
				->join('pl_producto_propiedad AS pp', 'pp.pl_propiedad_id = prop.id')
				->where('pp.pl_producto_id', $producto->id)
				->get()->result();

			$i = 0;
			foreach ($headers as $header) {
				$temp = new stdClass();
				$temp->tipo_id = $header->id;
				$temp->tipo = $header->tipo;
				$temp->propiedad_id = '';
				$temp->propiedad_valor = '';

				$producto->propiedades[$i] = $temp;
				foreach ($props as $prop) {
					if($header->id == $prop->tipo_id){
						$producto->propiedades[$i] = $prop;
						break;
					}
				}
				$i++;
			}
		}

		if($id != FALSE){
			foreach ($productos as $producto){
				foreach ($producto->propiedades as $prop){
					$prop->options = $this->db->get_where('pl_propiedad', array(	
						'pl_tipo_id'=>$prop->tipo_id,
						'estado'=>1
						))->result();
				}
				return $producto;
			}
		}
		else
			return $productos;

	}

	public function get_header(){
		$headers = $this->db->get_where('pl_tipo', array('estado'=>'1'))->result();
		foreach ($headers as $h) {
			$h->options = $this->db->get_where('pl_propiedad', array(
				'pl_tipo_id'=>$h->id,
				'estado'=>1
				))->result();
		}
		return $headers;
	}

	public function save($producto, $propiedades){

		if(isset($producto['id'])){
			$this->db->where('id', $producto['id']);
			$this->db->update('pl_producto', $producto);
		}
		else{
			$this->db->insert('pl_producto', $producto);
			$producto['id'] = $this->db->insert_id();
		}

		$this->db->where('pl_producto_id', $producto['id']);
		$this->db->delete('pl_producto_propiedad');

		foreach ($propiedades as $prop) {
			$this->db->insert('pl_producto_propiedad', array(
				'pl_producto_id'=>$producto['id'],
				'pl_propiedad_id'=>$prop,
				'estado'=>'1'
				));
		}

		return $producto['id'];
	}


}

/* End of file plantilla_model.php */
/* Location: ./application/models/plantilla/plantilla_model.php */