<style>
@page {
            size: 80mm 200mm;
            width: 80mm;
            max-width:80mm;
            min-height: 200mm;
            margin: 0;

            font-family: georgia, serif;
            font-size: 2px;
            color: blue;
            height: auto;
            border: 1px #000000;
            /* width: 80mm;*/
            min-height: 200mm;
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-inside : avoid;
        }
        
  @media print {


            table {
                page-break-inside : avoid
            }

            #tabla_resumen_productos thead tr{
                border-top: 1px #000 dashed;
                border-bottom: 1px #000 dashed;
            }
            #tabla_resumen_productos thead tr th{
                border-top: 1px #000 dashed;
                border-bottom: 1px #000 dashed;
                font-size: 12px!important;
            }
            #tabla_resumen_productos tbody tr td{
                border-top: 0px #000 dashed;
                border-bottom: 0px #000 dashed;
                font-size: 10px!important;
            }

            #totales_{
                font-size: 10px!important;
            }

        }
        
  #header_imp { 
     position: absolute; 
     top: <?php echo $gotoxy[0]["Y"]; ?>px; 
     right: 20px;
     left: <?php echo $gotoxy[0]["X"]; ?>px; 
     width: 80%; 
     height: 120px; 
   }
  #details_imp { 
    position: absolute; 
    top: <?php echo $gotoxy[1]["Y"]; ?>px;
    left:<?php echo $gotoxy[1]["X5"]; ?>px; 
    right:0px; 
    width: 80%; 
    height: 250px;   
   }
   
   #sub_imp { 
    position: absolute; 
    top: <?php echo $gotoxy[2]["Y"]; ?>px;
    left:<?php echo $gotoxy[2]["X"]; ?>px; 
    right:0px; 
    width: 20%; 
    height: 100px;   
   }
   
 .Table  {  
    	display: table;  
    	border-collapse: collapse;
    	margin-left:5%;
  
        }  
  
        .Title  
  
        {  
  
            display: table-caption;  
  
            text-align: center;  
  
            font-weight: bold;  
  
            font-size: larger;  
  
        }  
  
        .Heading  
  
        {  
  
            display: table-row;  
  
            font-weight: bold;  
  
            text-align: center;  
  
        }  
  
        .Row  
  
        {  
  
            display: table-row; 
            height: 35px;
  
        }  
  
        .Cell  
  
        {  
  
            display: table-cell;  
  
            padding-left: 5px;  
  
            padding-right: 5px;
            
            min-width: <?php echo $gotoxy[0]["colWidthLarge"]; ?>px;
            
            text-align: left;
            
            vertical-align: middle;            
  
        }  
        
         .CellShort  
  
        {  
  
            display: table-cell;  
  
            padding-left: 5px;  
  
            padding-right: 5px;
            
            min-width: <?php echo $gotoxy[0]["colWidthShort"]; ?>px;
            
            text-align: left;
            
            vertical-align: middle;            
  
        }  
        
    .colspan2 {    
       display: table-row;
       min-width:100%;
       width:100%;
       height: 35px;
       text-align: left;
       vertical-align: middle;
    }
    
    
</style>

<div id="header_imp">
  <div class="Table">
    <div class="Row">
      <div class="Cell colspan2"><?php echo $ventas[0]['cliente'] ?></div>
   </div>
   <div class="Row">
      <div class="Cell"><?php echo $ventas[0]['RazonSocialEmpresa'] ?></div>
      <div class="Cell"><?= date('Y-m-d', strtotime($ventas[0]['fechaemision'])) ?></div>
      <div class="Cell"></div>
   </div>
    <div class="Row">
      <div class="Cell"><?php /*echo $ventas[0]['direccion_cliente']*/ ?></div>
   </div>

</div>

<div id="details_imp">
   <div class="Table">
   <?php for($i=0;$i<count($ventas); $i++) {    
   
   	$um = isset($ventas[$i]['abreviatura']) ? $ventas[$i]['abreviatura'] : $ventas[$i]['nombre_unidad'];
   	$cantidad_entero = intval($ventas[$i]['cantidad'] / 1) > 0 ? intval($ventas[$i]['cantidad'] / 1) :'';
   	$cantidad_decimal = number_format (fmod($ventas[$i]['cantidad'], 1),3);
   	
   	$cantidad = $cantidad_entero ;
   	
   	if ($cantidad_decimal > 0) {
   	
   		if(!empty($cantidad_entero)) {
   			$cantidad = $ventas[$i]['cantidad'] ;
   	
   		}
   	
   		else
   			$cantidad=strval($cantidad_decimal);
   	
   			if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
   				$cantidad = $cantidad_entero . " " . '1/4';
   				if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal ==0.500)
   					$cantidad = $cantidad_entero . " " . '1/2';
   					if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
   						$cantidad = $cantidad_entero . " " . '3/4';
   						if ($cantidad_decimal == 0.125)
   							$cantidad = $cantidad_entero . " " . '1/8';
   	}
   	
   	
   	if ($ventas[$i]['producto_cualidad'] == 'MEDIBLE') {
   	
   		if ($ventas[$i]['unidades'] == 12 or $ventas[$i]['orden']==1 ) {
   			$cantidad =floatval($ventas[$i]['cantidad']);
   	
   		} else {
   			$cantidad = floatval($ventas[$i]['cantidad'] * $ventas[$i]['unidades']);
   			$um=$ventas[$i]['unidad_minima'];
   		}
   	}
   	
   	
   	?>
    <div class="Row">
      <div class="CellShort"><?= $ventas[$i]['producto_id'] ?></div>
      <div class="CellShort"><?php  echo $cantidad ." ".$um  ?></div>
      <div class="CellShort"><?php  echo $um  ?></div>
      <div class="Cell"><?= $ventas[$i]['nombre'] ?></div>   
      <div class="CellShort"><?= $ventas[$i]['preciounitario'] ?></div>
      <div class="CellShort"><?= $ventas[$i]['importe'] ?></div>
   </div>
   <?php } ?>
</div>

<div id="sub_imp">
  <?php echo $ventas[0]['subTotal']; ?><br>
  <?php echo $ventas[0]['impuesto']; ?><br>
  <?php echo $ventas[0]['montoTotal']; ?><br>
</div>