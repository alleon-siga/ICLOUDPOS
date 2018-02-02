<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gastos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {   
            $this->load->model('gastos/gastos_model');
            $this->load->model('tiposdegasto/tipos_gasto_model');
            $this->load->model('local/local_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('cajas/cajas_model');
            $this->load->model('proveedor/proveedor_model');
        }else{
            redirect(base_url(), 'refresh');
        }  
    }


    /** carga cuando listas los proveedores*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['tipos_gastos'] = $this->tipos_gasto_model->get_all();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["usuarios"] = $this->db->get_where('usuario', array('activo'=>1))->result();
        

        $dataCuerpo['cuerpo'] = $this->load->view('menu/gastos/gastos', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lista_gasto(){
        
        $params = array(
            'local_id' => $this->input->post('local_id'),
            'mes' => $this->input->post('mes'),
            'year' => $this->input->post('year'),
            'dia_min' => $this->input->post('dia_min'),
            'dia_max' => $this->input->post('dia_max'),
            'persona_gasto' => $this->input->post('persona_gasto'),
        );

        $tipo_gasto = $this->input->post('tipo_gasto');
        if($tipo_gasto != "-")
            $params['tipo_gasto'] = $tipo_gasto;

        $persona_gasto = $this->input->post('persona_gasto');
        if($persona_gasto == 1){
            $proveedor = $proveedor = $this->input->post('proveedor');
                if($proveedor != "-")
                    $params['proveedor'] = $proveedor;
        }
        if($persona_gasto == 2){
            $usuario = $usuario = $this->input->post('usuario');
                if($usuario != "-")
                    $params['usuario'] = $usuario;
        }
        
        $data['gastoss'] = $this->gastos_model->get_all($params);
        $data['gastos_totales'] = $this->gastos_model->get_totales_gasto($params);

        $this->load->view('menu/gastos/gasto_lista', $data);
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['gastos']= array();
        $data['tiposdegasto'] = $this->tipos_gasto_model->get_all();
        $data['local'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data["monedas"] = $this->monedas_model->get_all();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["usuarios"] = $this->db->get_where('usuario', array('activo'=>1))->result();

        if ($id != FALSE) {
            $data['gastos'] = $this->gastos_model->get_by('id_gastos', $id);
        }
        $this->load->view('menu/gastos/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $persona_gasto = $this->input->post('persona_gasto');
        if($persona_gasto == 1){
            $proveedor = $this->input->post('proveedor');
            $usuario = NULL;
        }elseif($persona_gasto == 2){
            $proveedor = NULL;
            $usuario = $this->input->post('usuario');
        }
        

        $gastos = array(
            'fecha' => date('Y-m-d',strtotime($this->input->post('fecha')))." ".date("H:i:s"),
            'fecha_registro'=>date('Y-m-d H:i:s'),
            'descripcion' => $this->input->post('descripcion'),
            'total' => $this->input->post('total'),
            'tipo_gasto' => $this->input->post('tipo_gasto'),
            'local_id' => $this->input->post('local_id'),
            'gasto_usuario'=>$this->session->userdata('nUsuCodigo'),
        	'id_moneda'=>$this->input->post('monedas'),
        	'tasa_cambio'=>$this->input->post('tasa_cambio'),
            'proveedor_id'=>$proveedor,
            'usuario_id'=>$usuario,
            'responsable_id'=>$this->session->userdata('nUsuCodigo')
        );

        $moneda_id = 1;
        if($gastos['id_moneda'] == 1030)
            $moneda_id = 2;

        if (empty($id)) {
            $resultado = $this->gastos_model->insertar($gastos);

            $this->cajas_model->save_pendiente(array(
                'monto'=>$gastos['total'],
                'tipo'=>'GASTOS',
                'IO'=>2,
                'ref_id'=>$resultado,
                'moneda_id'=>$moneda_id,
                'local_id'=>$gastos['local_id']
            ));
        }
        else{
            $gastos['id_gastos'] = $id;
            $resultado = $this->gastos_model->update($gastos);

            $this->cajas_model->update_pendiente(array(
                'monto'=>$gastos['total'],
                'tipo'=>'GASTOS',
                'ref_id'=>$id,
                'moneda_id'=>$moneda_id,
                'local_id'=>$gastos['local_id']
            ));
        }

        if ($resultado != FALSE) {
            $json['success']='Solicitud Procesada con exito';
        } else {
            $json['error']= 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }



    function eliminar()
    {
        $id = $this->input->post('id');

        $gastos = array(
            'id_gastos' => $id,
            'motivo_eliminar'=>$this->input->post('motivo'),
            'status_gastos' => 0

        );

        $gasto = $this->db->get_where('gastos', array('id_gastos'=>$id))->row();

        $moneda_id = 1;
        if($gasto->id_moneda == 1030)
            $moneda_id = 2;

        $this->cajas_model->delete_pendiente(array(
            'tipo'=>'GASTOS',
            'ref_id'=>$id,
            'moneda_id'=>$moneda_id,
            'local_id'=>$gasto->local_id
        ));

        $data['resultado'] = $this->gastos_model->update($gastos);

        if ($data['resultado'] != FALSE) {

            $json['success'] ='Se ha eliminado exitosamente';


        } else {

            $json['error']= 'Ha ocurrido un error al eliminar el Gasto';
        }

        echo json_encode($json);
    }

    function historial_pdf($local_id, $tipo_gasto, $mes, $year, $dia_min, $dia_max, $persona_gasto, $proveedor, $usuario)
    {
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4-L');

        $params = array(
            'local_id' => $local_id,
            'mes' => $mes,
            'year' => $year,
            'dia_min'=>$dia_min,
            'dia_max'=>$dia_max,
            'persona_gasto'=>$persona_gasto
        );

        if($tipo_gasto != "-")
            $params['tipo_gasto'] = $tipo_gasto;

        if($persona_gasto == 1){
                if($proveedor != "-")
                    $params['proveedor'] = $proveedor;
        }
        if($persona_gasto == 2){
                if($usuario != "-")
                    $params['usuario'] = $usuario;
        }



        $data['gastoss'] = $this->gastos_model->get_all($params);
        $data['gastos_totales'] = $this->gastos_model->get_totales_gasto($params);



        $data['local'] = $this->local_model->get_by('int_local_id', $local_id);
        $data['fecha_ini'] = $dia_min.'/'.$mes.'/'.$year;
        $data['fecha_fin'] = $dia_max.'/'.$mes.'/'.$year;

        $mpdf->setFooter('{PAGENO}');
        $mpdf->WriteHTML($this->load->view('menu/gastos/gasto_lista_pdf', $data, true));
        $nombre_archivo = utf8_decode('Gastos ' . $fecha_ini . ' : ' . $fecha_fin . '.pdf');
        $mpdf->Output($nombre_archivo, 'I');
    }


}
