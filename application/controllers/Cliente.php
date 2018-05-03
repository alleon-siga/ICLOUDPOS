<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('cliente/cliente_model');
            $this->load->model('clientesgrupos/clientes_grupos_model');
            $this->load->model('pais/pais_model');
            $this->load->model('estado/estado_model');
            $this->load->model('ciudad/ciudad_model');
            $this->load->model('distrito/distrito_model');
            $this->load->model('usuario/usuario_model');
            $this->load->model('cliente_tipo_campo_padre/cliente_tipo_campo_padre_model');
            $this->load->model('cliente_tipo_campo/cliente_tipo_campo_model');
            $this->load->model('cliente_campo_valor/cliente_campo_valor_model');
            $this->load->model('precio/precios_model');
            $this->load->library('Pdf');
            $this->load->library('phpExcel/PHPExcel.php');
        }else{
            redirect(base_url(), 'refresh');
        }
    }




    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['clientes'] = $this->cliente_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/cliente/cliente', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function get_all_ajax()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['clientes'] = $this->cliente_model->get_all();

        header('Content-Type: application/json');

        echo json_encode($data);

    }


    function form($id = FALSE)
    {

        $data = array();
        $data['grupos'] = $this->clientes_grupos_model->get_all();
        $data['distritos'] = $this->distrito_model->get_all();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        $data['precios']=$this->precios_model->get_precios();
        $data['operacion'] = TRUE;

        //$data['vendedores'] = $this->usuario_model->select_all_user();
        $data['clientes_tipo_padre'] = $this->cliente_tipo_campo_padre_model->get_all();
        empty($data['images']);

        if ($id != FALSE) {
            if($id == '-1'){
                $data['new_from_venta'] = '1';
            }
            else{
                $data['cliente'] = $this->cliente_model->get_by('cliente.id_cliente', $id);
                $data['images'] = $this->get_fotos($id);
                if($data['cliente']['ruc'] == '1')
                    $data['operacion'] = $this->cliente_model->check_operaciones($id);
            }
        }


        $this->load->view('menu/cliente/form', $data);
    }

    /*este metodo borra todas las imagenes en una carpeta*/
    function borrartodaimagen($cliente,$arreglo){

        for($i=0;$i<count($arreglo);$i++){
            $dir = './clientes/' . $cliente . '/'.$arreglo[$i];
            unlink($dir);
        }
    }

    function get_fotos($id)
    {
        $result = array();
        $dir = './clientes/' . $id . '/';
        if (!is_dir($dir)) return array();
        $temp = scandir($dir);
        foreach($temp as $img){
            if(is_file($dir.$img))
                $result[] = $img;
        }

        natsort($result);
        return $result;

    }

    function validar_ruc()
    {
        $ruc=$this->input->post('ruc');
       /// var_dump($ruc);
        $buscar=sizeof($this->cliente_model->get_by('identificacion', $ruc));
        $json['valor']=$buscar;
        echo json_encode($json);
    }

    function validar_razon_social()
    {
        $ruc=$this->input->post('razon_social');
        /// var_dump($ruc);
        $buscar=sizeof($this->cliente_model->get_by('razon_social', $ruc));
        $json['valor']=$buscar;
        echo json_encode($json);
    }

    function guardar()
    {
        $this->load->library('upload');

        $id = $this->input->post('idClientes');

        $cliente = array(
            'tipo_cliente' => $this->input->post('tipo_cliente'),
            'razon_social' => $this->input->post('razon_social_j')!=""?$this->input->post('razon_social_j') : trim($this->input->post('nombres').' '.$this->input->post('apellido_paterno').' '.$this->input->post('apellido_materno')),
            'identificacion' => $this->input->post('ruc_j')!=""?$this->input->post('ruc_j'): null,
            'ruc' => $this->input->post('tipo_iden'),
            'nombres' => $this->input->post('nombres'),
            'apellido_paterno' => $this->input->post('apellido_paterno'),
            'apellido_materno' => $this->input->post('apellido_materno'),
            'dni' => $this->input->post('apellidoPJuridico'),
            'email' => $this->input->post('correo'),
            'telefono1' => $this->input->post('telefono'),
            'genero' => $this->input->post('genero'),
            'direccion' => $this->input->post('direccion_j')?$this->input->post('direccion_j'):null,
            'distrito' => $this->input->post('distrito_id')!=""?$this->input->post('distrito_id'): null,
            'provincia' => $this->input->post('estado_id')!=""?$this->input->post('estado_id'): null,
            'ciudad' => $this->input->post('ciudad_id')!=""?$this->input->post('ciudad_id'): null,
            'cliente_status' => $this->input->post('estatus_j')?$this->input->post('estatus_j'):null,
            'grupo_id' => $this->input->post('grupo_id_juridico')!=""?$this->input->post('grupo_id_juridico'): null,
            'agente_retension' => $this->input->post('retencion')?$this->input->post('retencion'):0,
            'agente_retension_valor' =>$this->input->post('retencion_value')==0? null: $this->input->post('retencion_value'),
            'linea_credito' => $this->input->post('lineaC_j')?$this->input->post('lineaC_j'):null,
            'nota' => $this->input->post('tienda')?$this->input->post('tienda'):null
        );

            if (empty($id)) {
                $resultado = $this->cliente_model->insertar($cliente);

                if($resultado!=CEDULA_EXISTE and $resultado!=false ){
                $id = $resultado;

                $padre = $this->input->post('padre');
                $this->cliente_campo_valor_model->insertar($padre, $id);
                }
            } else {
                $cliente['id_cliente'] = $id;
                $resultado = $this->cliente_model->update($cliente);

                if($resultado==true){
                    $where=array(
                        'campo_cliente'=>$id
                    );
                    $this->cliente_campo_valor_model->eliminar_valor_cliente($where);

                    $padre = $this->input->post('padre');
                    $this->cliente_campo_valor_model->insertar($padre, $id);
                }
            }


        if ($resultado != FALSE) {
            /*esta carga la foto de la persona natural */
            if($cliente['tipo_cliente']==0){


                if (!empty($_FILES) and $_FILES['userfile']['size'][0] != '0') {
                    /*borro la imagen actual, ya que solo se permitira ingresar una imagen*/
                    $this->borrartodaimagen($id, $this->get_fotos($id));

                    $files = $_FILES;
                    $contador = 1;
                    $mayor = 0;

                    $directorio = './clientes/' . $id . '/';

                    if (is_dir($directorio)) {
                        $arreglo_img = scandir($directorio);
                        natsort($arreglo_img);
                        $mayor = array_pop($arreglo_img);
                        $mayor = substr($mayor, 0, -4);
                    } else {
                        $arreglo_img[0] = ".";
                    }
                    $sumando = 1;
                    for ($j = 0; $j < count($files['userfile']['name']); $j++) {

                        if ($files['userfile']['name'][$j] != "") {

                            if ($arreglo_img[0] == ".") {
                                $contador = $mayor + ($sumando);
                                $sumando++;
                            }
                            $_FILES ['userfile'] ['name'] = $files ['userfile'] ['name'][$j];
                            $_FILES ['userfile'] ['type'] = $files ['userfile'] ['type'][$j];
                            $_FILES ['userfile'] ['tmp_name'] = $files ['userfile'] ['tmp_name'][$j];
                            $_FILES ['userfile'] ['error'] = $files ['userfile'] ['error'][$j];
                            $_FILES ['userfile'] ['size'] = $files ['userfile'] ['size'][$j];

                            $size = getimagesize($_FILES ['userfile'] ['tmp_name']);

                            switch ($size['mime']) {
                                case "image/jpeg":
                                    $extension = "jpg";
                                    break;
                                case "image/png":
                                    $extension = "png";
                                    break;
                                case "image/bmp":
                                    $extension = "bmp";
                                    break;
                            }

                            $this->upload->initialize($this->set_upload_options($id, $contador, $extension));
                            $this->upload->do_upload();
                            $contador++;
                        } else {

                        }
                    }
                }

            }else{

                /*esta carga la imagen de persona juridica la empresa.*/
                if (!empty($_FILES) and $_FILES['userfile_je']['size'][0] != '0') {

                    /*borro la imagen actual, ya que solo se permitira ingresar una imagen*/
                    $this->borrartodaimagen($id, $this->get_fotos($id));

                    $files = $_FILES;
                    $contador = 1;
                    $mayor = 0;

                    $directorio = './clientes/' . $id . '/';

                    if (is_dir($directorio)) {
                        $arreglo_img = scandir($directorio);
                        natsort($arreglo_img);
                        $mayor = array_pop($arreglo_img);
                        $mayor = substr($mayor, 0, -4);
                    } else {
                        $arreglo_img[0] = ".";
                    }
                    $sumando = 1;

                    for ($j = 0; $j < count($files['userfile_je']['name']); $j++) {

                        if ($files['userfile_je']['name'][$j] != "") {

                            if ($arreglo_img[0] == ".") {
                                $contador = $mayor + ($sumando);
                                $sumando++;
                            }

                            $_FILES ['userfile'] ['name'] = $files ['userfile_je'] ['name'][$j];
                            $_FILES ['userfile'] ['type'] = $files ['userfile_je'] ['type'][$j];
                            $_FILES ['userfile'] ['tmp_name'] = $files ['userfile_je'] ['tmp_name'][$j];
                            $_FILES ['userfile'] ['error'] = $files ['userfile_je'] ['error'][$j];
                            $_FILES ['userfile'] ['size'] = $files ['userfile_je'] ['size'][$j];

                            $size = getimagesize($_FILES ['userfile'] ['tmp_name']);

                            switch ($size['mime']) {
                                case "image/jpeg":
                                    $extension = "jpg";
                                    break;
                                case "image/png":
                                    $extension = "png";
                                    break;
                                case "image/bmp":
                                    $extension = "bmp";
                                    break;
                            }

                            $this->upload->initialize($this->set_upload_options($id, $contador, $extension));
                            $this->upload->do_upload();
                            $contador++;
                        } else {

                        }
                    }
                }

            }

            if($resultado===CEDULA_EXISTE){

                $json['error']= CEDULA_EXISTE;
            }else{
                $json['success']='Solicitud Procesada con exito';
                $json['cliente']=$resultado;
            }

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }



        echo json_encode($json);

    }

    function set_upload_options($id,$contador,$extension)
    {

        // upload an image options
        $this->load->helper('path');
        $dir = './clientes/' . $id . '/';

        if (!is_dir('./clientes/')) {
            mkdir('./clientes/', 0755);
        }
        if (!is_dir($dir)) {
            mkdir($dir, 0755);
        }
        $config = array();
        $config ['upload_path'] = $dir;
        //$config ['file_path'] = './prueba/';
        $config ['allowed_types'] = $extension;
        $config ['max_size'] = '0';
        $config ['overwrite'] = TRUE;
        $config ['file_name'] = $contador;

        return $config;
    }

    /*este metodo borra una imagen especifica*/
    function eliminarimg(){
        $cliente = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        $dir = './clientes/' . $cliente . '/'.$nombre;
        $borrar = unlink($dir);

        if($borrar!=false){
            $json['success']="Se ha eliminado exitosamente";
        }else{
            $json['error']="Ha ocurrido un error al eliminar la imagen";
        }
        echo json_encode($json);
    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');
        // $identificacion = $this->input->post('identificacion');

        $cliente = array(
            'id_cliente' => $id,
            'razon_social' => $nombre . time(),
            //  'identificacion' => $identificacion,

            'cliente_status' => 0

        );

        $data['resultado'] = $this->cliente_model->updateD($cliente);

        if ($data['resultado'] != FALSE) {

            $json['success']= 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Cliente';
        }

        echo json_encode($json);
    }

    function pdf()
    {

        $clientes = $this->cliente_model->get_all();

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('CLIENTES');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.�?�.G.�?�.D.�?�.G.�?�.A.�?�.D.�?�.U.�?�.<br>Gran Logia de la República de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relación utilizada para ajustar la conversión de los píxeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
// Helvetica para reducir el tamaño del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// Añadir una página
// Este método tiene varias opciones, consulta la documentación para más información.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA DE CLIENTES</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<table><tr>";
        $html .= "<th>ID</th><th>Razon Social</th>";
        $html .= "<th>DNI o RUC</th>";
        $html .= "<th>Grupo</th>";
        $html .= "<th>Direccion</th>";
        $html .= "<th>Telefono</th>";
        $html .= "<th>Email</th>";
        $html .= "</tr>";
        foreach ($clientes as $familia) {
            $html .= "<tr><td>" . $familia['id_cliente'] . "</td>";
            if($familia['tipo_cliente']==1){
                $html .= "<td>" . $familia['razon_social'] . "</td>";
            }else{
                $html .= "<td>" . $familia['nombres']." ".$familia['apellido_paterno'] ." ".$familia['apellido_materno'] . "</td>";

            }

            if($familia['tipo_cliente']==1){
                $html .= "<td>" . $familia['ruc'] . "</td>";
            }else{
                $html .= "<td>" . $familia['dni']. "</td>";

            }
            $html .= "<td>" . $familia['nombre_grupos_cliente'] . "</td>";
            $html .= "<td>" . $familia['direccion'] . "</td>";
            $html .= "<td>" . $familia['telefono1'] . "</td>";
            $html .= "<td>" . $familia['email'] . "</td>";
            $html .= "</tr>";

        }
        $html .= "</table>";
// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("ListaFClientes.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel()
    {



        $clientes = $this->cliente_model->get_all();

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Reporte de Clientes")
            ->setSubject("Reporte de Clientes")
            ->setDescription("Reporte de Clientes")
            ->setKeywords("Reporte de Clientes")
            ->setCategory("Reporte de Clientes");


        $columna_pdf[0] = "ID";
        $columna_pdf[1] = "Raz�n social";
        $columna_pdf[2] = "DNI o RUC";
        $columna_pdf[3] = "Grupo";
        $columna_pdf[4] = "Direccion";
        $columna_pdf[5] = "Tel�fono ";
        $columna_pdf[6] = "Email";


        $col = 0;
        for ($i = 0; $i < count($columna_pdf); $i++) {
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna_pdf[$i]);
        }

        $row = 2;

        foreach ($clientes as $cliente) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['id_cliente']);
            $col++;

             if($cliente['tipo_cliente']==1){
                $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['razon_social']);
                $col++;
            }else{
                $nombre_apellido = $cliente['nombres']." ".$cliente['apellido_paterno']." ".$familia['apellido_paterno'];
                $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $nombre_apellido);
                $col++;

            }

            if($cliente['tipo_cliente']==1){
                $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['ruc']);
                $col++;
            }else{
                $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['dni']);
                $col++;
            }
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['nombre_grupos_cliente']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['direccion']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['telefono1']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['email']);
            $col++;

            $row++;
        }

        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Reporte Clientes');


        // configuramos el documento para que la hoja


        // de trabajo número 0 sera la primera en mostrarse
        // al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


        // redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteClientes.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    public function getDatosFromAPI_Reniec(){
        error_reporting(0);
        ini_set('display_errors', 0);
        $dni=$_POST['DNI'];
        require_once(APPPATH.'libraries/reniec/autoload.php');
        $cliente = new \Reniec\Reniec();
        $result = $cliente->search( $dni, true );
        $resultArray = json_decode($result, TRUE);
        if ($resultArray['success']==1) {
            print_r(json_encode($resultArray['result']));
        }else{
            echo "false";
        }
    }

    public function getDatosFromAPI_Sunac(){
        error_reporting(0);
        ini_set('display_errors', 0);
        $ruc=$_POST['RUC'];
        require_once(APPPATH.'libraries/sunat/autoload.php');
        $cliente = new \Sunat\Sunat(true,true);
        $result = $cliente->search( $ruc, true );
        $resultArray = json_decode($result, TRUE);
        if ($resultArray['success']==1) {
            print_r(json_encode($resultArray['result']));
        }else{
            echo "false";
        }
    }

}