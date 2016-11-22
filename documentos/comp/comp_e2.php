<?php

  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/arreglos_pg.php");
  require_once("../../includes/fechas.php");
  $codigo = $_POST['cod'];
  $cod_doc= $_POST['cod'];
  $prioridad = $_POST['slc_prioridad'];
  $tp_imputacion = $_POST['chk_tp_imputa'];
  $imp_acc_pp=trim($_POST['txt_cod_imputa']);  //Cod. del Proyecto o de la Accion Central
  $acc_esp=trim($_POST['txt_cod_accion']);   //Cod. de la Accion Especifica
  $reserva=trim($_POST['num_reserva']);
  $pcta=trim($_POST['pcta_id']);
  $fecha = $_POST['fecha'];
  $rif_sugerido=$_POST['rif_sugerido'];
  
  $beneficiario=explode(":",$_POST['rif_sugerido']);
  //$rif_sugerido=$beneficiario[0].":".$beneficiario[1];	
	
  $fecha_reporte =cambia_ing($_POST['fecha_reporte']);	
  $fecha_i=cambia_ing($_POST['fecha_i']);
  $fecha_f=cambia_ing($_POST['fecha_f']);
  //Para las imputaciones Arreglo de Partidas
  $largo=trim($_POST['hid_largo']);
  $total_imputacion=$largo;
	

  $j=0;
  $monto_solicitado=0;
  $anno_pres=$_SESSION['an_o_presupuesto'];
  $anno_pres=2014;
  $pasodisponibilidad=1;
  
  $sql_comp_imputa="SELECT comp_sub_espe,comp_acc_pp,comp_acc_esp,comp_tipo_impu,depe_id,comp_monto FROM sai_comp_imputa WHERE comp_id='".$codigo."' order by comp_sub_espe";
  $resul_comp_imputa=pg_query($conexion,$sql_comp_imputa);
	  
  $i=0;
  if ($resul_comp_imputa)
   $num_partidas=pg_num_rows($resul_comp_imputa);
   $matriz_monto_negativo=array();
   while($row=pg_fetch_array($resul_comp_imputa))
   {
  	$matriz_acc_pp_anterior[$i]=$row['comp_acc_pp']; 
	$matriz_acc_esp_anterior[$i]=$row['comp_acc_esp']; 
	$matriz_imputacion_anterior[$i]=$row['comp_tipo_impu']; 
	$matriz_uel_anterior[$i]=$row['depe_id']; 
	$matriz_sub_esp_anterior[$i]=trim($row['comp_sub_espe']); 
	$matriz_monto_anterior[$i]=$row['comp_monto'];
	$matriz_monto_cero[$i]=0; 
	array_push($matriz_monto_negativo, $row['comp_monto']*-1);
	$i++;
   }
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp_anterior);
	$arreglo_monto_cero=convierte_arreglo($matriz_monto_cero);
	$arreglo_monto_negativo=convierte_arreglo($matriz_monto_negativo);
	$arreglo_monto_comp=convierte_arreglo($matriz_monto_anterior);
    $arreglo_acc_pp=convierte_arreglo($matriz_acc_pp_anterior);
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp_anterior);
	$arreglo_uel=convierte_arreglo($matriz_uel_anterior);
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion_anterior);
  
  
  if (strcmp($_POST['operacion'],"anular")=="0") 
  {
	$valido_sopg=0;
	$valido_codi=0;
	
	//Verificar si existe alguna solicitud de pago para evitar la anulación del comp
	$sql_p="SELECT * FROM sai_seleccionar_campo('sai_sol_pago','sopg_id','esta_id<>15 and comp_id='||'''$cod_doc''','',2) resultado_set(sopg_id varchar)";
	$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
	if($row=pg_fetch_array($resultado_set_most_p)){
	  $valido_sopg=1;
	  $sopg=$row['sopg_id'];
	}

	//Verificar si existe algun codi para evitar la anulación del comp      
	$sql_p="SELECT * FROM sai_seleccionar_campo('sai_comp_diario','comp_id','esta_id <>15 and comp_doc_id like lower('||'''$cod_doc%'')','',2) resultado_set(comp_id varchar)";
	$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
	if($row=pg_fetch_array($resultado_set_most_p)){
	  $valido_codi=1;
	  $codi=$row['comp_id'];
	}
	
	if (($valido_sopg<>1)&&($valido_codi<>1)){
		
      $sql  = "select * from  sai_anular_compromiso('".trim($_POST['pcuenta_asunto'])."', '";
	  $sql .= $_POST['pcuenta_descripcionVal']. "' , '";
	  $sql .= $_SESSION['login'] ."', '" .$_POST['opt_depe'] ."', '";
	  $sql .= $_POST['observaciones']."','".$_POST['justificacion']."', '";
	  $sql .= $monto_solicitado."', '";
	  $sql .= $_POST['slc_prioridad'] ."','".$_SESSION['user_depe_id']."', '";
	  $sql .= $fecha."', '" .$_POST['pcta_id'] ."','".$rif_sugerido."','".$cod_doc."','$anno_pres','";
	  $sql .= $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto_comp."','".$arreglo_uel."' ,'".$arreglo_monto_negativo."') As resultado_set(varchar)";
	  $resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar el compromiso anulado".$sql);
	  $row = pg_fetch_array($resultado_set);
	
	  $memo_contenido=trim($_POST['contenido_memo']);
	  if ($memo_contenido==""){
 	   $memo_contenido="No Especificado";
      }
      
	  $query_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$memo_contenido."', 'Anulación Compromiso','0', '0','0','',0, 0, '0', '','".$cod_doc."') as memo_id");
	  $resultado_set = pg_exec($conexion ,$query_memo);
	   
	  $valido=resultado_set;
	  if($resultado_set)
	  {
		$row = pg_fetch_array($resultado_set,0); 
		if ($row[0] <> null)
		{
		  $memo_id=$row[0];
		}
	  }
	 echo "El compromiso ". $cod_doc. " se ha anulado satisfactoriamente";?>
      <div class="normal" align="center" >
         <img src="../../imagenes/pdf_ico.jpg" border="0"/><br>
	     <a href="javascript:abrir_ventana('../../documentos/comp/comp_pdf.php?codigo=<?echo $cod_doc;?>&esta_id=En Transito')">Ver detalle</a>
	   </div>
    <?php 
		 }else{
		 	echo "El compromiso ". $cod_doc. " NO PUEDE SER ANULADO, existe al menos una solicitud de pago ".$sopg. " o un codi asociado ".$codi;
		 }
	
}//fin anular
else 
{     
	 //EL COMP PUEDE ESTAR O NO ASOCIADO A UN PCTA
	 $monto_disponible=array();
  	 for($i=0; $i<$largo; $i++) 
  	 {  
 	  $matriz_imputacion[$i]=$_POST['txt_tipo_p_ac'.$i]; 
	  $matriz_acc_pp[$i]=$_POST['txt_id_p_ac'.$i];
	  $matriz_acc_esp[$i]=$_POST['txt_id_acesp'.$i]; 
	  $matriz_sub_esp[$i]=$_POST['txt_id_pda'.$i];  
	  $matriz_uel[$i]=$_POST['txt_id_depe'.$i]; 
	  $matriz_monto[$i]=str_replace(",","",$_REQUEST['txt_monto_pda'.$i]);
	  $monto_solicitado=$monto_solicitado+$_REQUEST['txt_monto_pda'.$i];
  	 }
	
	if ($pcta=="0"){//NO TIENE PCTA ASOCIADO, POR LO QUE SE VALIDA DISPONIBILIDAD CONTRA DISPONIBILIDAD TOTAL
	 if (strcmp($_POST['operacion'],"modificar")=="0"){	
	 for($i=0; $i<$largo; $i++) 
  	 {  
	  $validar=1;
	  $sqla="select comp_monto from sai_comp_imputa where comp_sub_espe='".$matriz_sub_esp[$i]."' and comp_id='".$codigo."'";
	  $resultado = pg_exec($conexion ,$sqla);
	  if($row=pg_fetch_array($resultado)) {
		if($matriz_monto[$i]<=$row[0])
		 $validar=0;
		else
		 $monto_validar=$matriz_monto[$i]-$row[0];
	  }else {
  			 $monto_validar=$matriz_monto[$i];
		 	}
	
	  if($validar==1){
		$sqla="select round(cast(monto_dispo as numeric),2) from sai_pres_consulta_disp(".$anno_pres.",'". $matriz_imputacion[$i]."','".$matriz_acc_pp[$i]."','".$matriz_acc_esp[$i]."','".$matriz_sub_esp[$i]."','". $matriz_uel[$i]."',".$monto_validar.") as monto_dispo ";
		//echo $sqla."<br>";
		$resultado_dispo = pg_exec($conexion ,$sqla);
		$valido=$resultado_dispo;
		if($valido){
		  $row = pg_fetch_array($resultado_dispo,0); 
		  $disponible_monto=$row[0];
		  if ($disponible_monto<0)
		  {
		   $pasodisponibilidad=-1;//SI VALIDA DISPONIBILIDAD
		   //$pasodisponibilidad=0; //NO VALIDA DISPONIBILIDAD
		  }
		}
	  }
	
     }//fin for
	}	else{
			 $pasodisponibilidad=1;
	}	
   }
   else{//SE VALIDA DISPONIBILIDAD CONTRA EL PCTA
	   $monto_disponible=array();
	   
   	  //PRIMERO SE LIBERA LAS PARTIDAS DEL COMP ANTES DE LA MODIFICACION,XQ AL MODIFICAR PUEDEN SER MENOS PARTIDAS Y A SU VEZ DIFERENTES
   	  for($j=0; $j<$num_partidas; $j++)
	  {  
	   $query_disponible="SELECT SUM(monto) as disponible FROM sai_disponibilidad_pcta WHERE pcta_asociado='".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
	   $resultado_query = pg_exec($conexion,$query_disponible);
	   
	   if ($row=pg_fetch_array($resultado_query)){
	  	 $disponible=$row['disponible']+$matriz_monto_anterior[$j];
		 $query = "UPDATE sai_disponibilidad_pcta set monto=0 WHERE pcta_asociado='".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
		 $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al aumentar disponibilidad del Pcta"));

		 $query = "UPDATE sai_disponibilidad_pcta set monto='".$disponible."' WHERE pcta_id='".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
	     $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al aumentar disponibilidad del Pcta"));
	      if (strcmp($_POST['operacion'],"reintegrar")=="0"){
	      	array_push($monto_disponible, $disponible);
	      }
	    }
	    $cod_pcta = '';
		$query_disp="SELECT * FROM sai_disponibilidad_pcta WHERE pcta_asociado='".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
	    $resultado_query_disp = pg_exec($conexion,$query_disp);
	  	while ($row_disp=pg_fetch_array($resultado_query_disp))
	  	{
	     $query_update="UPDATE sai_disponibilidad_pcta set monto='".$disponible."' WHERE pcta_id='".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
	     $resultado_set = pg_exec($conexion ,$query_update) or die(utf8_decode("Error al aumentar disponibilidad del Pcta"));
	
	     $query_update="UPDATE sai_disponibilidad_pcta set monto=0 WHERE pcta_asociado='".$pcta."' and pcta_id<>'".$pcta."' and partida='".$matriz_sub_esp_anterior[$j]."'";
	     $resultado_set = pg_exec($conexion ,$query_update) or die(utf8_decode("Error al aumentar disponibilidad del Pcta"));
	
	  	}
	   }//fin for
	   
	   
	 if (strcmp($_POST['operacion'],"modificar")=="0"){
  	   for($i=0; $i<$largo; $i++) 
  	   {  	
	    //ACTUALIZAR DISP_PCTA
	    $query_disponible="SELECT SUM(monto) as disponible FROM sai_disponibilidad_pcta WHERE pcta_asociado='".$pcta."' and partida='".$matriz_sub_esp[$i]."'";
	    $resultado_query = pg_exec($conexion,$query_disponible);
	    if ($row=pg_fetch_array($resultado_query))
	      $disponible=$row['disponible']-$matriz_monto[$i];
  	      if ($disponible<0)
	   	  {			
			$pasodisponibilidad=-1;   //SI VALIDA DISPONIBILIDAD 
			//$pasodisponibilidad=0; //NO VALIDA DISPONIBILIDAD
			$pda_sob[$ind]=$matriz_sub_esp[$j]; //Partidas sin disponibilidad
			$ind=$ind+1;
	   	   }else{
	   	   	array_push($monto_disponible, $disponible);
	   	   }
	   	   
	   	   
  	    }
	 }
  	}//FIN ELSE PCTA=0
  	
 }//FIN CASO MODIFICAR COMP

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<body>
<?php
if($pasodisponibilidad>-1)
{
  $total_imputacion=$largo;
  $arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);
  $arreglo_acc_esp=convierte_arreglo($matriz_acc_esp);
  $arreglo_sub_esp=convierte_arreglo($matriz_sub_esp);
  $arreglo_monto=convierte_arreglo($matriz_monto);
  $arreglo_uel=convierte_arreglo($matriz_uel);
  $arreglo_tipo_impu=convierte_arreglo($matriz_imputacion);
  if ($pcta<>"0"){
	$arreglo_monto_disp=convierte_arreglo($monto_disponible);
	} else{
	$arreglo_monto_disp="{}";
	}
	
  if ((strcmp($_POST['operacion'],"modificar")=="0") || (strcmp($_POST['operacion'],"reintegrar")=="0"))
  {
	$registrar=0;
	$partidas_guardar=array();
	$montos_guardar=array();
	$proyecto_guardar=array();
	$accion_guardar=array();
	$imputacion_guardar=array();
	$dependencia_guardar=array();
 	for($i=0; $i<$num_partidas; $i++)
 	{
  	 $valor=$matriz_sub_esp_anterior[$i];
  	 $clave = array_search($valor, $matriz_sub_esp);
 	 if (is_numeric($clave)){
 	  if ($matriz_monto[$clave]-$matriz_monto_anterior[$i]<>0)
 	  {
		array_push($partidas_guardar, $valor);
		array_push($montos_guardar, $matriz_monto[$clave]-$matriz_monto_anterior[$i]);
 		$registrar=1;
	  }
 	 }else{
 		   array_push($partidas_guardar, $valor);
 		   array_push($montos_guardar, $matriz_monto_anterior[$i]*-1);
 		   $registrar=1;
  		  }
  	
  	 if ($registrar==1){
  	   array_push($proyecto_guardar, $matriz_acc_pp_anterior[$i]);
  	   array_push($accion_guardar, $matriz_acc_esp_anterior[$i]);
  	   array_push($imputacion_guardar, $matriz_imputacion_anterior[$i]);
  	   array_push($dependencia_guardar, $matriz_uel_anterior[$i]);
	} 
   }

  for($j=0; $j<$largo; $j++)
   {
  	$valor=$matriz_sub_esp[$j];
  	$clave = array_search($valor, $matriz_sub_esp_anterior);
 	if (!is_numeric($clave))
 	{
 	  array_push($partidas_guardar, $valor);
 	  array_push($montos_guardar, $matriz_monto[$j]);
 	  array_push($proyecto_guardar, $matriz_acc_pp[$j]);
 	  array_push($accion_guardar, $matriz_acc_esp[$j]);
      array_push($imputacion_guardar, $matriz_imputacion[$j]);
      array_push($dependencia_guardar, $matriz_uel[$j]);
      $registrar=1;
 	  $i++;
  	}
  }

  require_once("../../includes/arreglos_pg.php"); 
  $arreglo_proyecto_guardar=convierte_arreglo($proyecto_guardar);
  $arreglo_accion_guardar=convierte_arreglo($accion_guardar);
  $arreglo_partidas_guardar=convierte_arreglo($partidas_guardar);
  $arreglo_montos_guardar=convierte_arreglo($montos_guardar);
  $arreglo_dependencia_guardar=convierte_arreglo($dependencia_guardar);
  $arreglo_imputacion_guardar=convierte_arreglo($imputacion_guardar);
		
  if ($registrar==0)
  {
 	$arreglo_proyecto_guardar = "{}";
	$arreglo_accion_guardar=  "{}";
	$arreglo_partidas_guardar=  "{}";
	$arreglo_montos_guardar= "{}";
	$arreglo_dependencia_guardar="{}";
	$arreglo_imputacion_guardar="{}";
  }
 
  //txt_monto_tot
  $descrip_sin_tags =$_POST['pcuenta_descripcionVal'];

  
  if (strcmp($_POST['operacion'],"modificar")=="0") 
  {
  
  $sql  = "select * from  sai_modificar_compromiso('".$_POST['pcuenta_asunto']."', '".$descrip_sin_tags. "' , '";
  $sql .= $_SESSION['login'] ."', '" .$_SESSION['user_depe_id'] ."', '" .$_POST['slc_prioridad'] ."','".$cod_doc."','";
  $sql .= $_POST['observaciones']."','".$_POST['justificacion']."','".$monto_solicitado."','$anno_pres','";
  $sql .= $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','";
  $sql .= $arreglo_uel."','".$_POST['estatus']."','".$_POST['fecha']."','".trim($beneficiario[0])."','";
  $sql .= $_POST['opt_depe']."','".$_POST['tipo_act']."','".$_POST['localidad']."','".$_POST['documento']."','";
  $sql .= $fecha_reporte."','".$_POST['pcta_id']."','".$arreglo_proyecto_guardar."','".$arreglo_accion_guardar."','";
  $sql .= $arreglo_imputacion_guardar."','".$arreglo_partidas_guardar."','".$arreglo_montos_guardar."','";
  $sql .= $arreglo_dependencia_guardar."','".$arreglo_monto_disp."','".$_POST['operacion']."','".trim($beneficiario[1])."','".$fecha_i."','".$fecha_f."','".$_POST['tipo_evento']."','".$_POST['control_interno']."') As resultado_set(varchar)";
  }else{//REINTEGRO TOTAL
  	
  $sql  = "select * from  sai_modificar_compromiso('".$_POST['pcuenta_asunto']."', '".$descrip_sin_tags. "' , '";
  $sql .= $_SESSION['login'] ."', '" .$_SESSION['user_depe_id'] ."', '" .$_POST['slc_prioridad'] ."','".$cod_doc."','";
  $sql .= $_POST['observaciones']."','".$_POST['justificacion']."','".$monto_solicitado."','$anno_pres','";
  $sql .= $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto_cero."','";
  $sql .= $arreglo_uel."','".$_POST['estatus']."','".$_POST['fecha']."','".trim($beneficiario[0])."','";
  $sql .= $_POST['opt_depe']."','".$_POST['tipo_act']."','".$_POST['localidad']."','".$_POST['documento']."','";
  $sql .= $fecha_reporte."','".$_POST['pcta_id']."','".$arreglo_acc_pp."','".$arreglo_acc_esp."','";
  $sql .= $arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto_negativo."','";
  $sql .= $arreglo_uel."','".$arreglo_monto_disp."','".$_POST['operacion']."','".trim($beneficiario[1])."','".$fecha_i."','".$fecha_f."','".$_POST['tipo_evento']."','".$_POST['control_interno']."') As resultado_set(varchar)";
  	
  }

  $resultado_set = pg_exec($conexion ,$sql) or die("Error al Modificar el compromiso ".$sql);
  $row = pg_fetch_array($resultado_set,0);
	if ($row[0] <> null) {
	  if (strcmp($_POST['operacion'],"modificar")=="0"){
	  	//ACTUALIZAR SAI_DISPONIBILIDAD_COMP
   	    for($k=0; $k<$largo; $k++) {
	  	  $disponible=0;
	  	  $resta=0;
	
	  	  $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$codigo."' and partida='".$matriz_sub_esp[$k]."' and comp_acc_pp='".$matriz_acc_pp[$k]."' and comp_acc_esp='".$matriz_acc_esp[$k]."'";
	  	  $resultado_query = pg_exec($conexion,$query_disponible);
	      if ($row_disp=pg_fetch_array($resultado_query)){
	      	$disponible=$row_disp['disponible'];
	      	
	      	$encuentra=0;
	      	for($h=0; $h<$num_partidas; $h++) {
	      		if ($matriz_sub_esp_anterior[$h]== $matriz_sub_esp[$k]){
	      	       $resta=$matriz_monto_anterior[$h]-$matriz_monto[$k];
	      	       $encuentra=1;		
	      		}
	      	}
	      
	      	 if ($encuentra==0){
      	       $resta=$matriz_monto[$k];
      	      // $disponible=$disponible-$resta; 
	      	 }
	      	   $disponible=$disponible-$resta;

	      	 if ($disponible<0)
	      	  $disponible=$disponible*(-1);
	      	  
	       	 $query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$codigo."' and partida='".$matriz_sub_esp[$k]."' and comp_acc_pp='".$matriz_acc_pp[$k]."' and comp_acc_esp='".$matriz_acc_esp[$k]."'";
	       	 $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al disminuir disponibilidad del Compromiso"));
	  	
	      }else{
	      	$query = "INSERT INTO sai_disponibilidad_comp (partida,monto,comp_id,comp_acc_pp,comp_acc_esp) VALUES ('".$matriz_sub_esp[$k]."','".$matriz_monto[$k]."','".$codigo."','".$matriz_acc_pp[$k]."','".$matriz_acc_esp[$k]."')";	  
	      	$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al agregar partida a la disponibilidad del Compromiso"));
            }
  	      }
  	      
	    $num_diferencias=0;
        $num_diferencias=count(array_diff($matriz_sub_esp_anterior,$matriz_sub_esp));
        $diferencias=array($num_diferencias);
        $diferencias=array_values(array_diff($matriz_sub_esp_anterior,$matriz_sub_esp));
         
        if ($num_diferencias>0){
         $indice=$largo;
         for($k=0; $k<$num_diferencias; $k++){ //LIBERAR PARTIDAS QUE YA NO SE ESTEN USANDO
          $valor=array_keys($matriz_sub_esp_anterior, $diferencias[$k]);
          $valor_clave=$valor[0];
               
          $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$codigo."' and partida='".$diferencias[$k]."'";
	  	  $resultado_query = pg_exec($conexion,$query_disponible);
	      if ($row_disp=pg_fetch_array($resultado_query))
	      {
	       $query = "DELETE FROM sai_disponibilidad_comp WHERE comp_id='".$codigo."' and partida='".$diferencias[$k]."'";
	       $resultado_set = pg_query($conexion ,$query) or die(utf8_decode("Error al eliminar partida del Compromiso"));
  		   $matriz_imputacion[$indice]=$matriz_imputacion_anterior[$valor_clave]; 
	  	   $matriz_acc_pp[$indice]=$matriz_acc_pp_anterior[$valor_clave];
	  	   $matriz_acc_esp[$indice]=$matriz_acc_esp_anterior[$valor_clave]; 
	  	   $matriz_sub_esp[$indice]=$diferencias[$k];  
	  	   $matriz_uel[$indice]=$matriz_uel_anterior[$valor_clave]; 
	  	   $matriz_monto[$indice]=0;
	  	   $indice++;

	      }	     
         }
        }
	}else{ //Reintegro total
	 	for($i=0; $i<$num_partidas; $i++)
 	    {
  		 $query = "UPDATE sai_disponibilidad_comp set monto='".$matriz_monto_anterior[$i]."' WHERE comp_id='".$codigo."' and partida='".$matriz_sub_esp_anterior[$i]."' and comp_acc_pp='".$matriz_acc_pp_anterior[$i]."' and comp_acc_esp='".$matriz_acc_esp_anterior[$i]."'";
	   	 $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al disminuir disponibilidad del Compromiso"));
        }
	}
	 $codigo_pcuenta=$row[0];
	 include("../../includes/respaldos_e1.php");
	 $total_imputacion=0;
	
	 $sql= " Select * from sai_buscar_comp_imputacion('".trim($cod_doc)."') as result ";
	 $sql.= " (comp_id varchar, comp_acc_pp varchar, comp_acc_esp varchar, depe_id varchar, comp_sub_espe varchar, comp_monto float8, tipo bit)";
	 $resultado_set= pg_exec($conexion ,$sql);
	 $valido=$resultado_set;
	  if ($resultado_set) {
	   $total_imputacion=pg_num_rows($resultado_set);
	   $monto_solicitado=0;
	   $i=0;
	   while($row=pg_fetch_array($resultado_set)) 
	   {
		$matriz_imputacion[$i]=trim($row['tipo']);
		$matriz_acc_pp[$i]=trim($row['comp_acc_pp']); 
		$matriz_acc_esp[$i]=trim($row['comp_acc_esp']);
		$matriz_sub_esp[$i]=trim($row['comp_sub_espe']);
		$matriz_uel[$i]=trim($row['depe_id']); 
		$matriz_monto[$i]=trim($row['comp_monto']); 
		$monto_solicitado=$monto_solicitado+$row['comp_monto'];
		$i++;
	   }
	  }
	}
	
	$estado_doc=10;
	$prioridad_doc=$prioridad;?>
	
<table width="485" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td colspan="2" class="normalNegroNegrita" align="center">MODIFICAR COMPROMISO</td>
  </tr>
  <tr>
	<td class="normalNegrita">N&uacute;mero:</td>
	<td class="normalNegro"><?php echo $cod_doc;?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Fecha del compromiso:</td>
	<td class="normalNegro"><?php echo $fecha;?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Elaborado por:</td>
	<td class="normalNegro"><? echo($_SESSION['solicitante']);?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Unidad/Dependencia:</td>
	<td class="normalNegro"><?php
	 $sql_str="SELECT depe_nombre FROM sai_dependenci WHERE depe_id='".$_POST['opt_depe']."'";
	 $res_q=pg_exec($sql_str);
	 if ($depe_row=pg_fetch_array($res_q)) {
	   echo $depe_row['depe_nombre'];}
	 ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Asunto:</td>
	<td class="normalNegro"><?	echo($_POST["nom_asunto"]); ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Rif del Proveedor Sugerido:</td>
	<td class="normalNegro"><?	echo($_POST["rif_sugerido"]); ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Tipo Actividad:</td>
	<td class="normalNegro"><?php 
	 $sql_asu = "SELECT * FROM sai_tipo_actividad where id='".$_POST['tipo_act']."'";
	 $result=pg_query($conexion,$sql_asu);
	 if($row=pg_fetch_array($result))	{
	   echo $row['nombre'];
	 }
	?></td>
  </tr>
    <tr class="normal"><td><b>Tipo Evento:</b></td>
<td class="normalNegro" colspan="3">
<?php 
	$sql_asu = "SELECT * FROM sai_tipo_evento where id='".$_POST['tipo_evento']."'";					
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	}  		
?>
</td>
</tr>
<tr class="normal"><td><b>Duracci&oacute;n de la Actividad:</b></td>
<td class="normalNegro" colspan="3">
<?php 
	echo $_POST['fecha_i']." - ".$_POST['fecha_f'];	
?>
</td>
</tr>
  <tr>
	<td class="normalNegrita">Tipo Documento:</td>
	<td class="normalNegro"><?php 
	 $sql_asu = "SELECT * FROM sai_tipo_documento where id='".$_POST['tipo_doc']."'";
	 $result=pg_query($conexion,$sql_asu);
	 if($row=pg_fetch_array($result))	{
	  echo $row['nombre']." - ".$_POST['documento'];
	 }
	 ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Estatus:</td>
	<td class="normalNegro"><b><?echo $_POST['estatus']?></b></td>
  </tr>
  <tr>
	<td class="normalNegrita">Descripci&oacute;n:</td>
	<td class="normalNegro"><? echo($_POST["pcuenta_descripcionVal"]); ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Justificaci&oacute;n:</td>
	<td class="normalNegro"><?echo $_POST['justificacion']?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Condiciones de pago:</td>
	<td class="normalNegro"><?echo $_POST['cond_pago']?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Observaciones:</td>
	<td class="normalNegro"><?echo $_POST['observaciones']?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Monto solicitado:</td>
	<td class="normalNegro"><?echo $monto_solicitado;?></td>
  </tr>
  <tr class="normal">
	<td colspan="2">
	  <table width="60%" border="0" class="tablaalertas" align="center">
		<tr class="td_gray">
		  <td align="center" class="normal"><strong>Imputaci&oacute;n presupuestaria</strong></td>
		</tr>
		<tr>
		  <td class="normal" align="center">
			<table border="0">
			  <tr>
				<td class="normalNegro" align="left"><div align="center">ACC.C/PP</div></td>
				<td class="normalNegro" align="left"><div align="center">Acci&oacute;n espec&iacute;fica</div></td>
				<td align="left" class="normalNegro"><div align="center">Dependencia</div></td>
				<td align="left" class="normalNegro"><div align="center">Partida</div></td>
				<td align="left" class="normalNegro"><div align="center">Monto</div></td>
	  		  </tr>
			  <tr>
			  <?php
				for ($ii=0; $ii<$total_imputacion; $ii++)
				{
				?>
				<td class="peq" align="left" width="17%">
				  <div align="center"><input name="<?php echo "txt_imputa_proyecto_accion".$ii;?>" type="text" class="peq" id="<?php echo "txt_imputa_proyecto_accion".$ii;?>" size="6" maxlength="15" align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly /></div></td>
				<td class="peq" align="left" width="19%">
  				  <div align="center"><input name="<?php echo "txt_imputa_accion_esp".$ii;?>" type="text" class="peq" id="<?php echo "txt_imputa_accion_esp".$ii;?>" size="6" maxlength="15" align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly /></div></td>
				<td class="peq" align="left" width="10%">
				  <div align="center"><input name="<?php echo "txt_imputa_unidad".$ii;?>" type="text" class="peq" id="<?php echo "txt_imputa_unidad".$ii;?>" size="6" maxlength="10" align="right" value="<?php echo $matriz_uel[$ii];?>" readonly /></div></td>
				<td class="peq" align="left" width="15%">
				  <div align="center"><input name="<?php echo "txt_imputa_sub_esp".$ii;?>" type="text" class="peq" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="12" maxlength="15" align="right" value="<?php echo $matriz_sub_esp[$ii];?>" readonly title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al valor agregado";}?>" /></div></td>
				<td class="peq" align="left" width="39%">
				  <div align="center"><input name="<?php echo "txt_imputa_monto".$ii;?>" type="text" class="peq" id="<?php echo "txt_imputa_monto".$ii;?>" size="10" maxlength="25" align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly /></div></td>
			   </tr>
			   <?php
				}
			   ?>
			</table>
		 </td>
	   </tr>
	</table>
	  <tr>
		<td class="normal" align="center" colspan="2"><a href="javascript:abrir_ventana('../../documentos/comp/comp_detalle.php?codigo=<?echo $cod_doc;?>&esta_id=En Transito')">Ver detalle</a></td>
 	  </tr>
	  <tr>
		<td colspan="2"><? require_once('../../includes/respaldos_mostrar.php');?></td>
	  </tr>
	  <tr>
		<td height="18" colspan="2">&nbsp;</td>
	  </tr>
</table>
	
<?php
	}
  } else{?>
	
  <table width="485" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr>
	  <td colspan="3" class="normalNegrita">
		<div align="center"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"> No existe la disponibilidad presupuestaria para registrar dicha solicitud <br>
		<?php echo(pg_errormessage($conexion)); ?><br><br><img src="../../imagenes/mano_bad.gif" width="31" height="38"></div>
	</tr>
  </table>
  <?php }?>
	</body>
	</html>
