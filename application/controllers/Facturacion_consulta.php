<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturacion_consulta extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('facturacion/facturacion_model');
        $this->load->model('local/local_model');
    }


    function consulta($md5_id)
    {
        $fact = $this->db->get_where('facturacion', array('md5(id)' => $md5_id))->row();
        if ($fact != NULL) {
            $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $fact->id));
            $data['emisor'] = $this->facturacion_model->get_emisor();


            $this->load->library('mpdf53/mpdf');
            $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
            $html = $this->load->view('menu/facturacion/impresion_a4', $data, true);
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

    }
}