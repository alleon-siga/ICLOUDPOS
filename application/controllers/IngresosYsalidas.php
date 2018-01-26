<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set("memory_limit", "250M");

class ingresosYsalidas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->login_model->verify_session();

        $this->load->model('ingresosYventas/ingresosYventas_model');
        $this->load->model('ingreso/ingreso_model');
        $this->load->model('local/local_model');

        $this->load->library('mpdf53/mpdf');

//pd producto pv proveedor
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');
    }
    

    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        //$data['locales']=$this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $data["productos"] = $this->ingreso_model->productos();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingresosYsalidas/ingresosYsalidas', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_ingresos(){

        $condicion = array();
        if ($this->input->post('producto') != "") {
            $condicion['producto_id'] = $this->input->post('producto');
            $data['id_producto'] = $this->input->post('producto');
        }
        if ($this->input->post('desde') != "") {
            $condicion['date >= '] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($this->input->post('hasta') != "") {
            $condicion['date <='] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
            $data['hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }



        $data['venta'] = $this->ingresosYventas_model->get_ventas_ingresos($condicion);
        $this->load->view('menu/ingresosYsalidas/tabla_salida_entrada', $data);

    }

    function pdf($id, $fecha_desde, $fecha_hasta)
    {


        if ($id != 0) {
            $condicion = array('producto_id' => $id);

        }
        if ($fecha_desde != 0) {

            $condicion['date >= '] = date('Y-m-d', strtotime($fecha_desde));
        }
        if ($fecha_hasta != 0) {
            $condicion['date <='] = date('Y-m-d', strtotime($fecha_hasta));

        }
        $data['ventas'] = $this->ingresosYventas_model->get_ventas_ingresos($condicion);



        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Ingresos y Salidas de Productos');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.•.G.•.D.•.G.•.A.•.D.•.U.•.<br>Gran Logia de la República de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>Ingresos y Salidas de Productos</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";




        if (isset($data['ventas'])) {

            $html .= "<table><tr><th>Fecha</th><th>C&oacute;digo Producto</th><th>Productos</th><th>Cantidad Ingresada</th> ";
            $html .= "<th>Precio Compra</th><th>Cantidad Salida</th><th>Precio Salida</th><th>Stock Final</th></tr>";


                foreach ($data['ventas'] as $venta) {
                    if ($venta['tipo_operacion'] == 'ENTRADA') {
                    $html .= " <tr><td >" . date('d-m-Y', strtotime($venta['date'])) . "</td><td >" . $venta['producto_id'] . "</td><td >" . $venta['producto_nombre'] . "</td>";
                    $html .= "<td>" . $venta['cantidad'] . "</td><td>Precio Entrada</td><td></td><td></td>";
                    $html .= "<td >Stock Entrada</td></tr>";


                }else{
                    $html .= " <tr><td >" . date('d-m-Y', strtotime($venta['date'])) . "</td><td >" . $venta['producto_id'] . "</td><td >" . $venta['producto_nombre'] . "</td>";
                    $html .= "<td></td><td></td><td>" . $venta['cantidad'] . " </td><td>Precio Salida</td>";
                    $html .= "<td >Stock Salida</td></tr>";


                }

            }
        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("Ingresos_y_Salidas_de_Productos.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel($id, $fecha_desde, $fecha_hasta)
    {
        if ($id != 0) {
            $condicion = array('producto_id' => $id);
        }
        if ($fecha_desde != 0) {

            $condicion['date >= '] = date('Y-m-d', strtotime($fecha_desde));

        }
        if ($fecha_hasta != 0) {
            $condicion['date <='] = date('Y-m-d', strtotime($fecha_hasta));

        }
        $data['ventas'] = $this->ingresosYventas_model->get_ventas_ingresos($condicion);

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Ventas")
            ->setSubject("Ventas")
            ->setDescription("Ventas")
            ->setKeywords("Ventas")
            ->setCategory("Ventas");


            $columna[0] = "Fecha";
            $columna[1] = "Codigo Producto";
            $columna[2] = "Productos";
            $columna[3] = "Cantidad Ingresada";
            $columna[4] = "Precio Compra";
            $columna[5] = "Cantidad Salida";
            $columna[6] = "Precio Salida";
            $columna[7] = "Stock Final";


        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;

        if (isset( $data['ventas'])) {

            foreach ($data['ventas'] as $venta) {
                if ($venta['tipo_operacion'] == 'ENTRADA') {

                    $col = 0;
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y', strtotime($venta['date'])));

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['producto_id']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['producto_nombre']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['cantidad']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, 'Precio Entrada');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, '');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, '');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, 'Stock Entrada');


                    $row++;
                } else {

                    $col = 0;
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y', strtotime($venta['date'])));

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['producto_id']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['producto_nombre']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, '');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, '');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $venta['cantidad']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, 'Precio Salida');

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, 'Stock Salida');


                    $row++;
                }
            }
        }
// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Ingreso_y_Salida_de_Producto');


// configuramos el documento para que la hoja
// de trabajo número 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Ingreso_y_Salida_de_Producto.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }
}