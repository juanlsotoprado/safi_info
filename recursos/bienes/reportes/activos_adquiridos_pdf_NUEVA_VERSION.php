<?php
require("../../../includes/conexion.php");
require("../../../includes/reporteBasePdf.php"); 
require("../../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../../includes/html2ps/funciones.php");
require_once("../../../includes/fechas.php");

    $wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";
	$wheretipo8="";
	$wheretipo9="";		
	$criterio2="";
	$criterio3="";
	$criterio4="";
	$criterio5="";
	$group='';
    $from1="";
    $fecha_in=trim($_REQUEST['txt_inicio']); 
	$fecha_fi=trim($_REQUEST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
	$depe=$_REQUEST['opt_depe'];
	$marca=$_REQUEST['marca'];
	$descripcion=$_REQUEST['descripcion'];
	$tipo_bien=$_REQUEST['tp_bien'];
	$serial=$_REQUEST['serial'];
    $dia=substr($fecha_ini,8,2);
	$mes=substr($fecha_ini,5,2);
	$anno=substr($fecha_ini,0,4);
	$fec_ini=$dia.'/'.$mes.'/'.$anno;
		  
	$dia1=substr($fecha_fin,8,2);
	$mes1=substr($fecha_fin,5,2);
	$anno1=substr($fecha_fin,0,4);
	$fec_fin1=$dia1.'/'.$mes1.'/'.$anno1;
	
	  if ($_POST['sbn']<>""){
 	   $wheretipo8 = " and etiqueta = '".$_POST['sbn']."' ";
      }
      
      if ($serial<>""){
 	   $wheretipo9 = " and serial = '".$serial."' ";
      }
	
    if (strlen($fecha_ini)>2) {
     if ($_REQUEST['tipo_fec']==0){
 	   $wheretipo1 = " and fecha_entrada >= '".$fecha_ini."' and fecha_entrada <= '".$fecha_fin."' ";
 	   $group="fecha_entrada,";
      }else{
      	$wheretipo1 = " and asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' ";
 	    $group="asbi_fecha,";
      }
      	$criterio1=" desde el ".$fec_ini." al ".$fec_fin1;
    }
	
    if ($_REQUEST['opt_depe']>0) {
	  $wheretipo2 = " and depe_solicitante='".$depe."' ";
	  $query_depe="SELECT depe_nombre FROM sai_dependenci WHERE depe_id='".$depe."'";
	  $resultado_depe=pg_query($conexion,$query_depe);	
	  if ($row=pg_fetch_array($resultado_depe)){
	  	$criterio2=" de la dependencia ".utf8_encode(($row['depe_nombre']));
	  }
    }
	 
     if ($_REQUEST['marca']>0){ 
	  $wheretipo3 = " and marca_id='".$marca."' ";
      $query="SELECT bmarc_nombre FROM sai_bien_marca WHERE bmarc_id='".$_REQUEST['marca']."'";
	  $resultado=pg_query($conexion,$query);	
	  if ($row=pg_fetch_array($resultado)){
	  	$criterio3=" de la marca ".$row['bmarc_nombre'];
	  }
     }
     
      if ($_REQUEST['descripcion']<>0) {
	  $wheretipo4 = " and bien_id='".$descripcion."' ";
	  $criterio4=" del activo ".$descripcion;
      }
      
	  if ($_REQUEST['tp_bien']<>0){
	    $from1=",sai_item_bien t5,bien_categoria t6 ";
	  	$wheretipo5 = " and t5.tipo=t6.id and t5.id=t1.bien_id and t6.id='".$tipo_bien."'";
	    $query="SELECT nombre FROM bien_categoria WHERE id='".$tipo_bien."'";
	    $resultado=pg_query($conexion,$query);	
	    if ($row=pg_fetch_array($resultado)){
	  	 $criterio5=" de la clasificación ".utf8_encode($row['nombre']);
	    }
	  }
	  
	  if ($_REQUEST['estatus']>0) 
	   $wheretipo7 = " and t1.esta_id='".$_REQUEST['estatus']."' ";

	   $buscar=$_REQUEST['tp_reporte'];
	
	if (($_REQUEST['tp_reporte'])==1){//GENERAL
	 if ($_REQUEST['tipo_fec']==0){
     $sql_tabla1="SELECT fecha_entrada as fecha, depe_solicitante as dependencia,t1.bien_id,precio as precio,count(t1.bien_id) as cantidad,t3.acta_id
     FROM sai_biin_items t1, sai_bien_inco t3".$from1."
     WHERE t3.acta_id=t1.acta_id".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo9." group by ".$group."depe_solicitante,t1.bien_id,fecha_entrada,precio,t3.acta_id order by 1";
     	
	 }else {
	 $sql_tabla1="SELECT asbi_fecha as fecha, solicitante as dependencia,t1.bien_id,precio as precio,count(t1.bien_id) as cantidad,t3.asbi_id as acta_id
     FROM sai_biin_items t1, sai_bien_asbi_item t2, sai_bien_asbi t3".$from1."
     WHERE t3.asbi_id=t2.asbi_id and t2.clave_bien=t1.clave_bien".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo8.$wheretipo9." group by ".$group."solicitante,t1.bien_id,precio,t3.asbi_id order by 1";	 	
	 }
	}else{//DETALLADO
		
	if ($_REQUEST['tipo_fec']==0){
	   $sql_tabla1="SELECT fecha_entrada as fecha_e,0 as fecha_s, depe_solicitante as dependencia,t1.bien_id,t1.ubicacion,precio as precio,t1.esta_id,count(t1.bien_id) as cantidad,depe_nombre,
     modelo,etiqueta,serial,bmarc_nombre,t4.nombre as nombre_bien,esta_nombre,clave_bien,t1.acta_id as acta_e, 0 as acta_s
     FROM sai_biin_items t1, sai_bien_inco t3 ,sai_bien_marca t2,sai_item t4,sai_estado t7,sai_dependenci t8 ".$from1."
     WHERE t8.depe_id=t3.depe_solicitante and t4.id=t1.bien_id and marca_id=bmarc_id and t3.acta_id=t1.acta_id and t7.esta_id=t1.esta_id ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo7.$wheretipo8.$wheretipo9." group by ".$group."depe_solicitante,t1.bien_id,fecha_entrada,t1.ubicacion,precio,t1.esta_id,modelo,etiqueta,serial,bmarc_nombre,t4.nombre,esta_nombre,clave_bien,t1.acta_id,depe_nombre
     ORDER BY t1.bien_id,etiqueta";
	   	}else{
	   $sql_tabla1="SELECT fecha_registro as fecha_e,asbi_fecha as fecha_s, solicitante as dependencia,t1.bien_id,t1.ubicacion,precio as precio,t1.esta_id,count(t1.bien_id) as cantidad,depe_nombre,
     modelo,etiqueta,serial,bmarc_nombre,t4.nombre as nombre_bien,esta_nombre,t1.clave_bien,t10.acta_id as acta_e,t3.asbi_id as acta_s
     FROM sai_biin_items t1,sai_bien_asbi_item t9, sai_bien_asbi t3 ,sai_bien_marca t2,sai_item t4,sai_estado t7,sai_dependenci t8, sai_bien_inco t10 ".$from1."
     WHERE t10.acta_id=t1.acta_id and t8.depe_id=t3.solicitante and t4.id=t1.bien_id and marca_id=bmarc_id and 
     t9.asbi_id=t3.asbi_id and t9.clave_bien=t1.clave_bien and t7.esta_id=t1.esta_id ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo7.$wheretipo8.$wheretipo9." group by ".$group."solicitante,t1.bien_id,asbi_fecha,t1.ubicacion,precio,t1.esta_id,modelo,etiqueta,serial,bmarc_nombre,t4.nombre,esta_nombre,t1.clave_bien,t3.asbi_id,depe_nombre,fecha_registro,t10.acta_id
     ORDER BY 1,t1.bien_id,etiqueta";	   		
	}	
	}
	//echo $sql_tabla1;
	
	 $resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta2");
	 
	$contenido = "<style type='text/css'>
						.titulo{
							text-align:center;
							font-size: 14pt;
							font-weight:bold;
						}
						.subTitulo{
							text-align:center;
							font-size: 11pt;
							font-weight:normal;
						}
						.nombreCampo{
							vertical-align: middle;
							font-weight:bold;
						}
						.nombreCampoTitulo{
							font-family: arial;
							font-size: 10pt;
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.nombreCampoTituloSinFondo{
							font-family: arial;
							font-size: 10pt;
							vertical-align: middle;
							text-align:center;
							height: 25px;
						}
						.nombreCampoTituloPequeno{
							font-family: arial;
							vertical-align: middle;
							text-align:center;
							background-color: #CCCCCC;
							height: 25px;
						}
						.textoTabla{
							font-family: arial;
							font-size: 9pt;						
						}
						.textoPie{
							font-family: arial;
							font-size: 8pt;						
						}
						.alineadoAbajo{
							vertical-align: bottom;
						}
						.alineadoMedio{
							vertical-align: middle;
						}
						.alineadoCentro{
							text-align:center;
						}
						.alineadoDerecha{
							text-align:right;
						}
						.alturaMaxima{
							height: 40px;
						}
					</style>";
	 
	 

   	$contenido .="<p class='titulo'>Movimiento de activos ".utf8_decode($criterio1.$criterio2.$criterio3.$criterio4.$criterio5)."</p>";
	
	$contenido .="<table width='100%' border='1' align='center' class='textoTabla'>";
	if ($buscar==1){
	$contenido .="<tr>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Acta</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Activo</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Fecha</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Cantidad</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Valor unitario Bs.</b></span></td>";
	$contenido .="</tr>"; 
	}else{
	$contenido .="<tr>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>#</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Activo</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Estatus</b></span></td>";	
	$contenido .="<td align='center'><span class='nombreCampo'><b>Acta entrada</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Fecha ingreso</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Dependencia</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Acta salida</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Fecha salida</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Marca</b></span></td>";	
	$contenido .="<td align='center'><span class='nombreCampo'><b>Modelo</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Serial activo</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Serial Bien Nacional</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>Torre</b></span></td>";
	$contenido .="<td align='center'><span class='nombreCampo'><b>".utf8_decode('Galpón')."</b></span></td>";
	$contenido .="</tr>"; 		
	}
	
   $resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta");	
   while ($rowt1=pg_fetch_array($resultado_set_t1)) 
   {	
   	if ($buscar==1){
     $cantidad=$rowt1['cantidad'];
	 $id=$rowt1['bien_id'];
	 $monto=$rowt1['precio'];
	 if (strlen($rowt1['fecha'])>4)
	  $fec=substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
	 else
	  $fec="--";
   	
   	  $sql_d="SELECT * FROM sai_seleccionar_campo('sai_item','id,nombre','id=''$id''','',1) resultado_set(id varchar,nombre varchar)"; 
	  $resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar lista de articulos");
	  $rowd=pg_fetch_array($resultado_set_most_d);
	
 	  $contenido .="<tr>";
	  $contenido .="<td>".$rowt1['acta_id']."</td>";
	  $contenido .="<td>".strtoupper($rowd['nombre'])."</td>";
	  $contenido .="<td align='center'>".$fec."</td>";
	  $contenido .="<td align='right'>".$cantidad."</td>";
	  $contenido .="<td align='right'>".number_format($monto,2,',','.')."</td>";
	  $contenido .="</tr>"; 
   	}else{
   	  
   		$torre="-";
		$galpon="-";
		if ($rowt1['esta_id']=="41"){
		if ($rowt1['ubicacion']=="1")
		 $torre="X";
		 elseif ($rowt1['ubicacion']=="2")
		 $galpon="X";
		}
   		
   	   $i++;

 	   $id_acta=$rowt1['acta_e'];
	   $fec=substr($rowt1['fecha_e'],8,2).'/'.substr($rowt1['fecha_e'],5,2).'/'.substr($rowt1['fecha_e'],0,4);

   	   if ($_REQUEST['tipo_fec']==0){
	     if ($rowt1['esta_nombre']<>"Incorporado"){
	       $sql_acta="SELECT t1.asbi_id,asbi_fecha FROM sai_bien_asbi_item t1,sai_bien_asbi t2 where t1.asbi_id=t2.asbi_id and t2.esta_id<>15 and clave_bien='".$rowt1['clave_bien']."'";
	       $resultado_acta=pg_query($conexion,$sql_acta);
	       if ($row_acta=pg_fetch_array($resultado_acta))
	      	$id_acta_salida=$row_acta['asbi_id'];
	       	$fecha_salida=$row_acta['asbi_fecha'];
	       	if (strlen($row_acta['asbi_fecha'])>4)
		      $fecha_salida=substr($row_acta['asbi_fecha'],8,2).'/'.substr($row_acta['asbi_fecha'],5,2).'/'.substr($row_acta['asbi_fecha'],0,4);
		    else
		      $fecha_salida="";
	         }else{
	        	$id_acta_salida="";
	        	 $fecha_salida="";
	        }}else{
	        	$fecha_salida=$fec=substr($rowt1['fecha_s'],8,2).'/'.substr($rowt1['fecha_s'],5,2).'/'.substr($rowt1['fecha_s'],0,4);
	        	$id_acta_salida=$rowt1['acta_s'];
	        }
   	  $contenido .="<tr>";
	  $contenido .="<td>".$i."</td>";
	  $contenido .="<td>".strtoupper($rowt1['nombre_bien'])."</td>";
	  $contenido .="<td>".$rowt1['esta_nombre']."</td>";
	  $contenido .="<td>".$id_acta."</td>";
	  $contenido .="<td align='center'>".$fec."</td>";
	  $contenido .="<td>".$rowt1['depe_nombre']."</td>";
	  $contenido .="<td>".$id_acta_salida."</td>";
	  $contenido .="<td align='center'>".$fecha_salida."</td>";
	  $contenido .="<td>".strtoupper($rowt1['bmarc_nombre'])."</td>";
	  $contenido .="<td>".strtoupper($rowt1['modelo'])."</td>";
	  $contenido .="<td>".$rowt1['serial']."</td>";
	  $contenido .="<td align='center'>".$rowt1['etiqueta']."</td>";
	  $contenido .="<td align='center'>".$torre."</td>";
	  $contenido .="<td align='center'>".$galpon."</td>";
	  $contenido .="</tr>"; 
	  
   	}	
   }
	$contenido .="</table>"; 
	 
	if ($buscar==1)
		$properties = array("portrait" => true);
	else		
	    $properties = array("landscape" => true);
	 
	//    echo $contenido;
	 convert_to_pdf($contenido, $properties); 
	 pg_close($conexion);
?> 

