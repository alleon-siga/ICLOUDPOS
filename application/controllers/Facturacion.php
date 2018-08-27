<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class facturacion extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('facturacion/facturacion_model');
            $this->load->model('local/local_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function test()
    {
        $this->load->model('facturacion/picado_model');
        $productos = array();
        $temp = new stdClass();
        $temp->id = 1;
        $temp->um_id = 1;
        $temp->precio = 50;
        $temp->cantidad = 100;
        $productos[] = $temp;

        $temp = new stdClass();
        $temp->id = 2;
        $temp->um_id = 1;
        $temp->precio = 50;
        $temp->cantidad = 100;
        $productos[] = $temp;

        $temp = new stdClass();
        $temp->id = 3;
        $temp->um_id = 1;
        $temp->precio = 50;
        $temp->cantidad = 100;
        $productos[] = $temp;

        $temp = new stdClass();
        $temp->id = 41;
        $temp->um_id = 1;
        $temp->precio = 50;
        $temp->cantidad = 100;
        $productos[] = $temp;

        $temp = new stdClass();
        $temp->id = 5;
        $temp->um_id = 1;
        $temp->precio = 50;
        $temp->cantidad = 100;
        $productos[] = $temp;

        $response = $this->picado_model->split($productos);
        $n = 0;
        foreach ($response['BOLETAS'] as $boleta) {

            echo '<br>BOLETA: ' . ++$n;
            $importe = 0;
            foreach ($boleta as $detalle) {
                echo '<br>ID: ' . $detalle['id'];
                echo '<br>UM: ' . $detalle['um_id'];
                echo '<br>PRECIO: ' . $detalle['precio'];
                echo '<br>CANTIDAD: ' . $detalle['cantidad'];
                echo '<br>IMPORTE: ' . $detalle['cantidad'] * $detalle['precio'];
                $importe += $detalle['cantidad'] * $detalle['precio'];
            }
            echo '<hr><br>TOTAL: ' . $importe;

            echo '<hr>';

        }

//        $this->facturacion_model->getEstadoResumen();
//        header('Content-Type: text/xml');
//        var_dump($this->facturacion_model->enviarResumenBoletas());

//        $this->db->update('facturacion', array('estado' => 0));
//        $fact = $this->db->get('facturacion')->result();
//        foreach ($fact as $f) {
//            $this->facturacion_model->crearXml($f->id);
//        }
//        $this->db->update('facturacion', array(
//            'estado' => 2,
//            'hash_cdr' => NULL
//        ));
//        foreach ($fact as $f) {
//            $this->facturacion_model->emitirXml($f->id);
//        }
    }

    function enviar($action = '')
    {
        switch ($action) {
            case 'filter': {
                $data['local_id'] = $this->input->post('local_id');
                $data['estado'] = $this->input->post('estado');

                $data['fecha'] = str_replace('/', '-', $this->input->post('fecha'));


                $data['tipo_documento'] = '01';
                $data['facturas'] = $this->facturacion_model->get_comprobantes_generados($data);
                $data['tipo_documento'] = '03';
                $data['boletas'] = $this->facturacion_model->get_comprobantes_generados($data);

                $data['resumenes_pendientes'] = $this->db->get_where('facturacion_resumen', array(
                    'estado' => 2
                ))->result();

                $resumen = $this->db->order_by('id', 'desc')->get_where('facturacion_resumen', array(
                    'fecha' => date('Y-m-d')
                ))->row();

                if ($resumen != NULL) {
                    $data['resumen_numero'] = 'RC-' . date('Ymd') . '-' . ($resumen->correlativo + 1);
                } else {
                    $data['resumen_numero'] = 'RC-' . date('Ymd') . '-' . 1;
                }

                $data['emisor'] = $this->facturacion_model->get_emisor();
                echo $this->load->view('menu/facturacion/enviar_list', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();


                $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/enviar', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function emision($action = '')
    {
        switch ($action) {
            case 'filter': {
                $data['local_id'] = $this->input->post('local_id');
                $data['estado'] = $this->input->post('estado');

                $date_range = explode(" - ", $this->input->post('fecha'));
                $data['fecha_ini'] = str_replace("/", "-", $date_range[0]);
                $data['fecha_fin'] = str_replace("/", "-", $date_range[1]);


                $data['facturaciones'] = $this->facturacion_model->get_facturacion($data);
                $data['emisor'] = $this->facturacion_model->get_emisor();
                echo $this->load->view('menu/facturacion/facturacion_list', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();


                $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/index', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function notas($action = '')
    {
        switch ($action) {
            case 'filter': {
                $data['local_id'] = $this->input->post('local_id');

                $date_range = explode(" - ", $this->input->post('fecha'));
                $data['fecha_ini'] = str_replace("/", "-", $date_range[0]);
                $data['fecha_fin'] = str_replace("/", "-", $date_range[1]);


                $data['ventas'] = $this->db->select('v.*, c.razon_social, m.simbolo')->from('venta AS v')
                    ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
                    ->join('moneda AS m', 'm.id_moneda = v.id_moneda')
                    ->where('v.fecha >=', date('Y-m-d H:i:s', strtotime($data['fecha_ini'] . " 00:00:00")))
                    ->where('v.fecha <=', date('Y-m-d H:i:s', strtotime($data['fecha_fin'] . " 23:59:59")))
                    ->where('v.local_id', $data['local_id'])
                    ->where('v.id_documento = 6')
                    ->where('v.numero != ', NULL)
                    ->where('v.nota_facturada', 0)
                    ->where("v.venta_status = 'COMPLETADO'")
                    ->get()->result();

                echo $this->load->view('menu/facturacion/notas_list', $data, true);
                break;
            }
            case 'declarar': {
                $data['venta'] = $this->db->get_where('venta', array('venta_id' => $this->input->post('venta_id')))->row();

                echo $this->load->view('menu/facturacion/notas_declarar', $data, true);
                break;
            }
            case 'crear_comprobante': {

                $venta_id = $this->input->post('venta_id');
                $tipo_documento = $this->input->post('tipo_documento');
                $descuento = $this->input->post('descuento');
                $fecha_facturacion = date('Y-m-d H:i:s', strtotime(
                        str_replace('/', '-', $this->input->post('fecha_facturacion')))
                );

                $resp = $this->facturacion_model->convertirNotaPedido($venta_id, $tipo_documento, $fecha_facturacion, $descuento);

                if ($tipo_documento == '03' || $tipo_documento == '01') {
                    $data['facturacion'] = $this->db->get_where('facturacion', array(
                        'documento_tipo' => $tipo_documento,
                        'ref_id' => $venta_id
                    ))->row();
                } else {
                    $boletas_multiples = $this->db->get_where('facturacion', array(
                        'documento_tipo' => '03',
                        'ref_id' => $venta_id
                    ))->result();

                    if (count($boletas_multiples) > 0) {
                        if ($boletas_multiples[0]->estado == 1) {
                            $data['bm_msg'] = array(
                                'estado' => 1,
                                'nota' => 'Las boletas ' . $boletas_multiples[0]->documento_numero .
                                    ' hasta la ' . $boletas_multiples[count($boletas_multiples) - 1]->documento_numero .
                                    ' fueron generadas correctamente'
                            );
                        } else {
                            $data['bm_msg'] = array(
                                'estado' => 0,
                                'nota' => 'Ha occurido un error al generar picado de boletas multiples'
                            );
                        }
                    } else {
                        $data['bm_msg'] = array(
                            'estado' => 0,
                            'nota' => 'No se crearon boletas a partir de la nota de venta ' . $venta_id
                        );
                    }
                }

                header('Content-Type: application/json');
                echo json_encode($data);

                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }

                $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();


                $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/notas', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function get_facturacion_detalle($action = '')
    {
        switch ($action) {
            case 'boleta': {
                $id = $this->input->post('id');
                $data['resumen'] = $this->db->get_where('facturacion_resumen', array('id' => $id))->row();
                $data['emisor'] = $this->facturacion_model->get_emisor();
                $data['boletas'] = $this->db
                    ->join('facturacion_resumen_comprobantes AS frc', 'frc.comprobante_id = facturacion.id')
                    ->get_where('facturacion', array('frc.resumen_id' => $id))
                    ->result();

                echo $this->load->view('menu/facturacion/facturacion_boleta_detalle', $data, TRUE);
                break;
            }
            default: {

                $id = $this->input->post('id');
                $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
                $data['emisor'] = $this->facturacion_model->get_emisor();

                echo $this->load->view('menu/facturacion/facturacion_list_detalle', $data, TRUE);
            }
        }
    }

    function generar_comprobante()
    {
        $id = $this->input->post('id');

        $resp = $this->facturacion_model->crearXml($id);
        $data['facturacion'] = $this->db->get_where('facturacion', array('id' => $id))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_comprobantes()
    {
        $data['fecha'] = str_replace('/', '-', $this->input->post('fecha'));
        $data['local_id'] = $this->input->post('local_id');
        $data['estado'] = $this->input->post('estado');

        $data['tipo_documento'] = '01';
        $data['facturas'] = $this->facturacion_model->get_comprobantes_generados($data);
        $data['tipo_documento'] = '03';
        $data['boletas'] = $this->facturacion_model->get_comprobantes_generados($data);
        $data['resumen_pendiente'] = $this->db->get_where('facturacion_resumen', array('estado' => 2))->result();

        foreach ($data['resumen_pendiente'] as $resumen) {
            $resumen->numero = 'RC-' . date('Ymd', strtotime($resumen->fecha)) . '-' . $resumen->correlativo;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function emitir_comprobante()
    {
        $id = $this->input->post('id');

        $resp = $this->facturacion_model->emitirXml($id);
        $data['facturacion'] = $this->db->get_where('facturacion', array('id' => $id))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function emitir_resumen()
    {
        $data['fecha'] = str_replace('/', '-', $this->input->post('fecha'));
        $data['local_id'] = $this->input->post('local_id');
        $data['estado'] = $this->input->post('estado');

        $data['resp'] = $this->facturacion_model->enviarResumenBoletas($data);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function actualizar_resumen()
    {
        $id = $this->input->post('id');

        $response = $this->facturacion_model->getEstadoResumen($id);
        $data['resumen'] = $this->db->get_where('facturacion_resumen', array('id' => $id))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function reemitir_comprobante()
    {
        $id = $this->input->post('id');

        $resp = $this->facturacion_model->crearXml($id);
        $resp = $this->facturacion_model->emitirXml($id);
        $data['facturacion'] = $this->db->get_where('facturacion', array('id' => $id))->row();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function descargar_xml($id)
    {
        $emisor = $this->db->get('facturacion_emisor')->row();
        $f = $this->db->get_where('facturacion', array('id' => $id))->row();
        $name = $emisor->ruc . '-' . $f->documento_tipo . '-' . $f->documento_numero . '.XML';
        header('Content-Description: File Transfer');
        header('Content-Type: xml');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('./application/libraries/Facturador/files/xmls/' . $emisor->ruc . '/' . $name));
        ob_clean();
        flush();
        readfile('./application/libraries/Facturador/files/xmls/' . $emisor->ruc . '/' . $name) or die('error!');

    }

    function imprimir_ticket($id)
    {

        $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
        $data['emisor'] = $this->facturacion_model->get_emisor();
        $this->load->view('menu/facturacion/impresion_ticket', $data);
    }

    function imprimir($id)
    {
        $data['facturacion'] = $this->facturacion_model->get_facturacion(array('id' => $id));
        $data['emisor'] = $this->facturacion_model->get_emisor();


        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/facturacion/impresion_a4', $data, true);
//        echo $html;
//        return false;
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function emisor()
    {
        $data['emisor'] = $this->db->get('facturacion_emisor')->row();
        $data['departamentos'] = $this->db->get('estados')->result();
        $data['provincias'] = $this->db->get('ciudades')->result();
        $data['distritos'] = $this->db->get('distrito')->result();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/emisor', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_emisor()
    {
        $params = array(
            'ruc' => $this->input->post('ruc'),
            'razon_social' => $this->input->post('razon_social'),
            'nombre_comercial' => $this->input->post('nombre_comercial'),
            'direccion' => $this->input->post('direccion'),
            'departamento_id' => $this->input->post('departamento'),
            'provincia_id' => $this->input->post('provincia'),
            'distrito_id' => $this->input->post('distrito'),
            'ubigeo' => $this->input->post('codigo_ubigeo'),
            'moneda' => $this->input->post('moneda'),
            'env' => 'BETA',
            'user_sol' => $this->input->post('user_sol'),
            'pass_sol' => $this->input->post('pass_sol'),
            'pass_sign' => $this->input->post('pass_sign')
        );

        $this->facturacion_model->save_emisor($params);

        $pfx = $this->upload_image($params['ruc']);
        if ($pfx == 1) {

        }

        echo $params['ruc'];
    }

    function reporte_venta($action = '')
    {

        $data['emisor'] = $this->facturacion_model->get_emisor();

        switch ($action) {
            case 'filter': {
                $params['local_id'] = $this->input->post('local_id');
                $data['lists'] = $this->facturacion_model->get_ventas_emitidas($params);

                $this->load->view('menu/facturacion/reportes/venta_list', $data);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $data = array();

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
                $html = $this->load->view('menu/facturacion/reportes/venta_list_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;
            }
            case 'excel': {
                $params = json_decode($this->input->get('data'));
                $date_range = explode(' - ', $params->fecha);
                $data = array();

                echo $this->load->view('menu/facturacion/reportes/venta_list_excel', $data, true);
                break;
            }
            default: {
                if ($this->session->userdata('esSuper') == 1) {
                    $data['locales'] = $this->local_model->get_all();
                } else {
                    $usu = $this->session->userdata('nUsuCodigo');
                    $data['locales'] = $this->local_model->get_all_usu($usu);
                }


                $dataCuerpo['cuerpo'] = $this->load->view('menu/facturacion/reportes/venta', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }
    }

    function consultarRuc()
    {
        require_once(APPPATH . 'libraries/RucSunat/RucSunat.php');
        $ruc = $this->input->post('ruc');
        $sunat = new RucSunat();
        $emisor = $sunat->consultarRuc($ruc);


        header('Content-Type: application/json');
        if ($emisor != false) {
            echo json_encode(array('emisor' => $emisor));
        } else {
            echo json_encode(array('error' => 1));
        }
    }

    function upload_image($ruc)
    {
        if (isset($_FILES['certificado']) && $_FILES['certificado']['size'] != 0) {
            $extension = "pfx";
            $dir = './application/libraries/Facturador/files/certificates/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755);
            }

            $config = array();
            $config ['upload_path'] = $dir;
            $config['allowed_types'] = $extension;
            //$config ['file_path'] = './prueba/';
            $config ['max_size'] = '0';
            $config ['overwrite'] = TRUE;
            $config ['file_name'] = $ruc;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('certificado')) {
                echo $this->upload->display_errors();
            } else {

            }

            return $ruc . '.' . $extension;

        }
    }

}