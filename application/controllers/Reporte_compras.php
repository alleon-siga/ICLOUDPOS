<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_compras extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('reporte_venta/reporte_venta_model');
            $this->load->model('local/local_model');
            $this->load->model('reporte_compra/rcuentas_model');
            $this->load->model('reporte_compra/rproveedor_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function proveedor_estado($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Estado de Pago del Proveedor';

        switch ($action) {
            case 'filter': {
                $data['proveedores'] = $this->rproveedor_model->get_estado_pago(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'moneda_id' => $this->input->post('moneda_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'estado' => $this->input->post('estado')
                ));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $this->input->post('moneda_id')))->row();


                echo $this->load->view('menu/reporte_compra/proveedor_estado/tabla', $data, true);
                break;
            }
            default: {
                $data['proveedores'] = $this->rproveedor_model->get_estado_pago(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1,
                    'moneda_id' => MONEDA_DEFECTO
                ));

                $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => MONEDA_DEFECTO))->row();

                $data['reporte_filtro'] = $this->load->view('menu/reporte_compra/proveedor_estado/filtros', array(
                    'proveedores' => $this->db->get_where('proveedor', array('proveedor_status' => 1))->result(),
                    'monedas' => $this->db->get_where('moneda', array('status_moneda' => 1))->result()
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reporte_compra/proveedor_estado/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_venta/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }


    }

    function cuentas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Cuentas por Pagar';

        switch ($action) {
            case 'filter': {
                $data['cuentas'] = $this->rcuentas_model->get_cuentas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'atraso' => $this->input->post('atraso'),
                    'dif_deuda' => $this->input->post('dif_deuda'),
                    'dif_deuda_value' => $this->input->post('dif_deuda_value')
                ));

                $data['mostrar_detalles'] = $this->input->post('mostrar_detalles');

                echo $this->load->view('menu/reporte_compra/cuentas/tabla', $data, true);
                break;
            }
            default: {

                $data['cuentas'] = $this->rcuentas_model->get_cuentas(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
                ));

                $data['mostrar_detalles'] = 0;

                $data['reporte_filtro'] = $this->load->view('menu/reporte_compra/cuentas/filtros', array(
                    'proveedores' => $this->db->get_where('proveedor', array('proveedor_status' => 1))->result()
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reporte_compra/cuentas/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reporte_venta/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }


    }


}