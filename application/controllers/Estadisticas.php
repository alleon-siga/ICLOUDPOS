<?php

class estadisticas extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->login_model->verify_session();
        
        $this->load->model('venta/venta_model');
        $this->load->model('condicionespago/condiciones_pago_model');
        $this->load->model('ingreso/ingreso_model');

    }




    function index()
    {


        $data = array();
        $data['numero_dias']=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        $dataCuerpo['cuerpo'] = $this->load->view('menu/estadisticas/utilidades', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }

    }


    function utilidades()
    {

        $data['venta']=array();
        $data['utilidad']=array();

$campofecha=false;
        $condicion= "venta_status='COMPLETADO' ";
        $condiciondemargen="";
        $condicion_especial=" 1 ";
         $data['tabla_condiciones'] = $this->condiciones_pago_model->get_all();
        if ($this->input->post('desde') != "") {
            $fechadesde=date('Y',strtotime($this->input->post('desde')));
            $mesdesde=date('m',strtotime($this->input->post('desde')));
            $diadesde=date('d',strtotime($this->input->post('desde')));

            $fecha=date('Y-m-d', strtotime($this->input->post('desde',true)));
            $condicion_especial.=" and  fecha >= '".$fecha."'  ";
            $condicion.=" and  fecha >= '".$fecha."'  ";
            $condiciondemargen.=" and fecha_registro >= '".$fecha."'  ";
        }
        if ($this->input->post('hasta') != "") {
            $fechahasta=date('Y',strtotime($this->input->post('hasta')));
            $meshasta=date('m',strtotime($this->input->post('hasta')));
            $diashasta=date('d',strtotime($this->input->post('hasta')));

            $fecha=date('Y-m-d', strtotime($this->input->post('hasta',true)));
            $fechadespues= strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
            $condicion_especial.=" and  fecha < '".date('Y-m-d',$fechadespues)."'  ";
            $condicion.=" and  fecha <= '".date('Y-m-d',$fechadespues)."'  ";
            $condiciondemargen.=" and  fecha_registro <= '".date('Y-m-d',$fechadespues)."'  ";
        }


             if ($this->input->post('periodo') == "fecha") {

                 $group=" GROUP BY ciclo, mes ";
                 $data['venta'] = $this->venta_model->estadistica_porfecha($condicion,$group);

                  $group=" GROUP BY `ciclo`,mes, venta_status";

                  $data['estatus'] = $this->venta_model->estadistica_porfecha($condicion_especial,$group);

                  $data['totales'] = $this->venta_model->estadistica_porfecha($condicion,false);
                  $ciclo=12;

                  $group=" GROUP BY `ciclo`,mes, venta.`condicion_pago`";
                  $condicion.=" and status_condiciones=1 ";
                  $data['condicion_pago'] = $this->venta_model->condicion_pago_porfecha($condicion,$group);

                 $query="  SELECT SUM(detalleingreso.`precio`) AS suma FROM ingreso JOIN detalleingreso ON detalleingreso.`id_ingreso`=
            ingreso.`id_ingreso` WHERE ingreso.ingreso_status='COMPLETADO' ";
                 $query.=$condiciondemargen;
                 $sumadeingreso = $this->ingreso_model->estadistica($query);

                 $query="SELECT COUNT(id_ingreso) AS contador FROM ingreso  WHERE ingreso.ingreso_status='COMPLETADO' ";
                 $query.=$condiciondemargen;
                 $contadoringreso = $this->ingreso_model->estadistica($query);

                 if($data['totales'][0]['total_utilidad']==null){
                     $data['margen']=0;
                 }else{
                     if($contadoringreso[0]['contador']==0){
                         $con_ingreso=1;
                     }else{
                         $con_ingreso=$contadoringreso[0]['contador'];
                     }

                     if($data['totales'][0]['total_utilidad']==0){
                         $total_utilidad=1;
                     }else{
                         $total_utilidad=$data['totales'][0]['total_utilidad'];
                     }

                     if($sumadeingreso[0]['suma']==0){
                         $suma=1;
                     }else{
                         $suma=$sumadeingreso[0]['suma'];
                     }
                     $data['margen']=number_format( $total_utilidad/($suma/
                             $con_ingreso),2);
                 }

                 $campofecha=true;

              }

        if ($this->input->post('periodo') == "semana") {

            $group=" GROUP BY `ciclo`  ";
            $result['ventas'] = $this->venta_model->estadistica_semanaactual($condicion,$group);

            $group=" GROUP BY `ciclo`, venta_status ";
            $data['estatus'] = $this->venta_model->estadistica_semanaactual(" 1 ",$group);

            $data['totales'] = $this->venta_model->estadistica_semanaactual($condicion,false);

            $group=" GROUP BY `ciclo`  ";
            $condicion.=" and status_condiciones=1 ";
            $data['condicion_pago'] = $this->venta_model->condicion_pago_semanaactual($condicion,$group);

            $query="  SELECT SUM(detalleingreso.`precio`) AS suma FROM ingreso JOIN detalleingreso ON detalleingreso.`id_ingreso`=
            ingreso.`id_ingreso` WHERE ingreso.ingreso_status='COMPLETADO'
             and YEARWEEK (fecha_registro)= YEARWEEK(CURDATE())";
            $sumadeingreso = $this->ingreso_model->estadistica($query);

            $query="SELECT COUNT(id_ingreso) AS contador FROM ingreso  WHERE ingreso.ingreso_status='COMPLETADO'
       and YEARWEEK (fecha_registro)= YEARWEEK(CURDATE())";
            $contadoringreso = $this->ingreso_model->estadistica($query);

            if($data['totales'][0]['total_utilidad']==null){
                $data['margen']=0;
            }else{
                if($contadoringreso[0]['contador']==0){
                    $con_ingreso=1;
                }else{
                    $con_ingreso=$contadoringreso[0]['contador'];
                }

                if($data['totales'][0]['total_utilidad']==0){
                    $total_utilidad=1;
                }else{
                    $total_utilidad=$data['totales'][0]['total_utilidad'];
                }

                if($sumadeingreso[0]['suma']==0){
                    $suma=1;
                }else{
                    $suma=$sumadeingreso[0]['suma'];
                }
                $data['margen']=number_format( $total_utilidad/($suma/
                        $con_ingreso),2);
            }

            $ciclo=7;
        }

        if ($this->input->post('periodo') == "mesactual") {
            $group=" GROUP BY `ciclo`  ";
            $result['ventas'] = $this->venta_model->estadistica_mesactual($condicion,$group);

            $group=" GROUP BY `ciclo`, venta_status ";
            $data['estatus'] = $this->venta_model->estadistica_mesactual(" 1 ",$group);

            $data['totales'] = $this->venta_model->estadistica_mesactual($condicion,false);

            $group=" GROUP BY `ciclo` ";
            $condicion.=" and status_condiciones=1 ";
            $data['condicion_pago'] = $this->venta_model->condicion_pago_mesactual($condicion,$group);

            $query="  SELECT SUM(detalleingreso.`precio`) AS suma FROM ingreso JOIN detalleingreso ON detalleingreso.`id_ingreso`=
            ingreso.`id_ingreso` WHERE ingreso.ingreso_status='COMPLETADO'
            AND MONTH (fecha_registro)= MONTH(CURDATE() ) AND YEAR (fecha_registro)=YEAR(CURDATE())";
            $sumadeingreso = $this->ingreso_model->estadistica($query);

            $query="SELECT COUNT(id_ingreso) AS contador FROM ingreso  WHERE ingreso.ingreso_status='COMPLETADO'
      and MONTH (fecha_registro)= MONTH(CURDATE() ) AND YEAR (fecha_registro)=YEAR(CURDATE())";
            $contadoringreso = $this->ingreso_model->estadistica($query);

            if($data['totales'][0]['total_utilidad']==null){
                $data['margen']=0;
            }else{
                if($contadoringreso[0]['contador']==0){
                    $con_ingreso=1;
                }else{
                    $con_ingreso=$contadoringreso[0]['contador'];
                }

                if($data['totales'][0]['total_utilidad']==0){
                    $total_utilidad=1;
                }else{
                    $total_utilidad=$data['totales'][0]['total_utilidad'];
                }

                if($sumadeingreso[0]['suma']==0){
                    $suma=1;
                }else{
                    $suma=$sumadeingreso[0]['suma'];
                }
                $data['margen']=number_format( $total_utilidad/($suma/
                        $con_ingreso),2);
            }

            $numero_dias=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            $ciclo=$numero_dias;
        }

        if ($this->input->post('periodo') == "mesanterior") {
           $anodemesanterior=date('Y');
            $condicion_especial=" 1 ";
            if(date('m')==01){
                $anodemesanterior=$anodemesanterior-1;
                $condicion.="  AND YEAR (fecha)='".$anodemesanterior."' ";
                $condicion_especial.="  AND YEAR (fecha)='".$anodemesanterior."' ";
                $anoaingresar="  AND YEAR (fecha_registro)='".$anodemesanterior."' ";
            }else{

                $condicion.="  AND YEAR (fecha)=YEAR(CURDATE()) ";
                $condicion_especial.="  AND YEAR (fecha)=YEAR(CURDATE()) ";
                $anoaingresar="  AND YEAR (fecha_registro)=YEAR(CURDATE()) ";
            }

            $group=" GROUP BY `ciclo`  ";
            $result['ventas'] = $this->venta_model->estadistica_mesanterior($condicion,$group);

            $group=" GROUP BY `ciclo`, venta_status ";
            $data['estatus'] = $this->venta_model->estadistica_mesanterior ($condicion_especial,$group);

            $data['totales'] = $this->venta_model->estadistica_mesanterior($condicion,false);

            $query="  SELECT SUM(detalleingreso.`precio`) AS suma FROM ingreso JOIN detalleingreso ON detalleingreso.`id_ingreso`=
            ingreso.`id_ingreso` WHERE ingreso.ingreso_status='COMPLETADO' and
            MONTH(fecha_registro) = MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH))
             ";
            $query.=$anoaingresar;
            $sumadeingreso = $this->ingreso_model->estadistica($query);

            $query="SELECT COUNT(id_ingreso) AS contador FROM ingreso  WHERE ingreso.ingreso_status='COMPLETADO'
      and MONTH(fecha_registro) = MONTH(DATE_ADD(CURDATE(),INTERVAL -1 MONTH)) ";
            $query.=$anoaingresar;
            $contadoringreso = $this->ingreso_model->estadistica($query);


            $group=" GROUP BY `ciclo` ";
            $condicion.=" and status_condiciones=1 ";
            $data['condicion_pago'] = $this->venta_model->condicion_pago_mesanterior($condicion,$group);



            if($data['totales'][0]['total_utilidad']==null){
                $data['margen']=0;
            }else{
                if($contadoringreso[0]['contador']==0){
                    $con_ingreso=1;
                }else{
                    $con_ingreso=$contadoringreso[0]['contador'];
                }

                if($data['totales'][0]['total_utilidad']==0){
                    $total_utilidad=1;
                }else{
                    $total_utilidad=$data['totales'][0]['total_utilidad'];
                }

                if($sumadeingreso[0]['suma']==0){
                    $suma=1;
                }else{
                    $suma=$sumadeingreso[0]['suma'];
                }
                $data['margen']=number_format( $total_utilidad/($suma/
                        $con_ingreso),2);
            }



            $numero_dias=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
            $ciclo=$numero_dias;
        }

        if ($this->input->post('periodo') == "annoactual") {
            $group=" GROUP BY `ciclo` ";
            $anio=$this->input->post('anno') ;

            $result['ventas'] = $this->venta_model->estadistica_annoactual($condicion,$group,$anio);

            $group=" GROUP BY `ciclo`, venta_status ";
            $data['estatus'] = $this->venta_model->estadistica_annoactual (" 1 ",$group,$anio);


            $data['totales'] = $this->venta_model->estadistica_annoactual($condicion,false,$anio);

            $group=" GROUP BY `ciclo` ";
            $condicion.=" and status_condiciones=1 ";
            $data['condicion_pago'] = $this->venta_model->condicion_pago_annoactual($condicion,$group,$anio);

            $query="  SELECT SUM(detalleingreso.`precio`) AS suma FROM ingreso JOIN detalleingreso ON detalleingreso.`id_ingreso`=
            ingreso.`id_ingreso` WHERE ingreso.ingreso_status='COMPLETADO' AND  YEAR(fecha_registro)='".$anio."' ";
            $sumadeingreso = $this->ingreso_model->estadistica($query);

            $query="SELECT COUNT(id_ingreso) AS contador FROM ingreso  WHERE ingreso.ingreso_status='COMPLETADO'
          AND YEAR(fecha_registro)='".$anio."' ";
            $contadoringreso = $this->ingreso_model->estadistica($query);


            if($data['totales'][0]['total_utilidad']==null){
                $data['margen']=0;
            }else{
                if($contadoringreso[0]['contador']==0){
                    $con_ingreso=1;
                }else{
                    $con_ingreso=$contadoringreso[0]['contador'];
                }

                if($data['totales'][0]['total_utilidad']==0){
                    $total_utilidad=1;
                }else{
                    $total_utilidad=$data['totales'][0]['total_utilidad'];
                }

                if($sumadeingreso[0]['suma']==0){
                    $suma=1;
                }else{
                    $suma=$sumadeingreso[0]['suma'];
                }
                $data['margen']=number_format( $total_utilidad/($suma/
                        $con_ingreso),2);
            }
            $ciclo=12;
        }


        $validar=false;

        if($campofecha==false) {
            for ($i = 1; $i <= $ciclo; $i++) {


                for ($j = 0; $j < count($result['ventas']); $j++) {

                    if ($result['ventas'][$j]['ciclo'] == $i) {

                        $newData['data'] = array(array(1, $ciclo));
                        $newData = array();
                        $newData[0] = intval($result['ventas'][$j]['ciclo']);
                        $newData[1] = $result['ventas'][$j]['total_venta'];
                        //$newData[] = $result['ventas'][$i]['total_venta'];// ESto es el valor del eje X
                        $data['venta'][] = $newData;
                        $newData[1] = $result['ventas'][$j]['total_utilidad'];
                        $data['utilidad'][] = $newData;
                        $validar = true;
                    }

                }
                if ($validar == false) {
                    $newData['data'] = array(array(1, $ciclo));
                    $newData = array();
                    $newData[0] = $i;
                    $newData[1] = intval(0);// ESto es el valor del eje X
                    $data['venta'][] = $newData;
                    $newData[1] = 0;
                    $data['utilidad'][] = $newData;

                }
                $validar = false;

            }
        }else{


            if(isset($fechadesde)){
                if(isset($fechahasta)){

                    if($fechadesde<$fechahasta){
                        $fechaInicio="".$fechadesde."-"."$mesdesde"."-".$diadesde."";

                        $fechaFin="".$fechahasta."-"."$meshasta"."-".$diashasta."";

                    }elseif($fechadesde==$fechahasta){

                        $fechaInicio="".$fechadesde."";
                        $fechaFin="".$fechahasta."";
                        if($mesdesde<=$meshasta){
                            $fechaInicio.="-".$mesdesde."";
                            $fechaFin.="-".$meshasta."";
                        }

                        if($diadesde<=$diashasta){

                            $fechaInicio.="-".$diadesde."";
                            $fechaFin.="-".$diashasta."";
                        }

                    }
                }else{
                    $fechaInicio="".$fechadesde."-"."$mesdesde"."-".$diadesde."";

                        $fechaFin=date('Y-m-d');
                }
            }else{
                $fechaFin="".$fechahasta."-"."$meshasta"."-".$diashasta."";
                if(count($result['ventas'])<1){
                    $fechaInicio=date('Y-m-d');

                }else{
                    $fechaInicio="".$result['ventas'][0]['anio']."-".$result['ventas'][0]['mes']."-".$result['ventas'][0]['ciclo']."";

                }
            }

$fechaInicio=strtotime($fechaInicio);
$fechaFin=strtotime($fechaFin);
        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            $data['fecha'][]= date("Y-m-d", $i);
}
        }

        echo json_encode($data);

    }


}

?>
