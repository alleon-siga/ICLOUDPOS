<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class update extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {        
            $this->load->model('update/updatehistorico_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }


    function run()
    {

        $almacen = $this->db->get_where('movimiento_historico', 'id is not null')->num_rows();

        if ($almacen < 1) {
            $this->updatehistorico_model->updatehistorico();
        } else {
            echo "hay datos en historico";
        }


    }

    function inventario()
    {
        $this->updatehistorico_model->updateAjustesDetalles();

    }

    function precio_unitario()
    {
        set_time_limit(0);
        $precios_unitarios = $this->db->select(
            'unidades_has_precio.precio as precio, 
            unidades_has_precio.id_unidad as unidad_id, 
            unidades_has_precio.id_producto as producto_id, 
            unidades_has_producto.unidades as unidades, 
            unidades_has_producto.orden as orden, 
            ')
            ->from('unidades_has_precio')
            ->join('unidades_has_producto', 'unidades_has_precio.id_producto = unidades_has_producto.producto_id')
            ->where('unidades_has_precio.id_precio', '3')
            ->where('unidades_has_precio.id_unidad = unidades_has_producto.id_unidad')
            ->order_by('unidades_has_producto.producto_id, unidades_has_producto.orden')
            ->get()->result();

        foreach ($precios_unitarios as $pu) {
            $this->db->where(array(
                'id_precio' => '1',
                'id_unidad' => $pu->unidad_id,
                'id_producto' => $pu->producto_id
            ));
            $this->db->update('unidades_has_precio', array('precio' => $pu->precio * $pu->unidades));
        }
    }
}