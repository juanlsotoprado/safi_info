<?php
// Borrar
/*
"'$codigo','sopg',$id_opcion,$objeto_siguiente_id,$cadena_siguiente_id,$request_id_objeto,'$nombre_opcion'";
'sopg-450231414','sopg',5,2,2,2,'Devolver Documento'

$codigo = 'sopg-450231414'
'sopg'
$id_opcion = 5 => Devolver documento
$objeto_siguiente_id = 2 => Modificar
$cadena_siguiente_id = 2 =>  
$request_id_objeto = 2 => Modificar
$nombre_opcion => 'Devolver Documento'

*/

ob_start();
session_start();
require_once("includes/conexion.php");
include('includes/arreglos_pg.php');
	 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
	

	
$memo_contenido=trim($_POST['contenido_memo']);
if ($memo_contenido==""){
 	$memo_contenido="No Especificado";
 }
$cod_doc = $request_codigo_documento;
$codigo= $cod_doc;
$partida_IVA=trim($_SESSION['part_iva']);

	$elem_imp_iva=trim($_POST['hid_elem_imp_iva']);
	$iva_porce_2=array($elem_imp_iva);
	$iva_monto_2=array($elem_imp_iva);
	for ($i=0; $i<$elem_imp_iva; $i++)
	{
	 $iva_porce_2[$i]=str_replace(",","",$_POST['txt_iva%'.$i]);
	 $iva_monto_2[$i]=str_replace(",","",$_POST['txt_iva_monto%'.$i]);
	}
	$monto_sin_iva=str_replace(",","",$_POST['txt_monto_subtotal']);
	
	$elem_retencion=trim($_POST['hid_elem_retencion']);
	$servicio=array($elem_retencion);
	$id_impuesto=array($elem_retencion);
	$iva_porce=array($elem_retencion);
	$porce_impuesto=array($elem_retencion);
	$monto_reten=array($elem_retencion);
	$monto_base=array($elem_retencion);
	$jj=0;
	for ($i=0; $i<$elem_retencion; $i++)
	{
	   $mon_temp=str_replace(",","",$_POST['txt_monto_impu'.$i]);
	 if ($mon_temp >0)
	    {
		 $servicio[$jj]=trim($_POST['txt_servicio'.$i]);
		 $id_impuesto[$jj]=trim($_POST['txt_impuesto'.$i]);
		 if ($_POST['txt_iva_'.$i] <> "s/n")
		   {
		     $iva_porce[$jj]=str_replace(",","", $_POST['txt_iva_'.$i]);
		   }
		  else
		  { 
		   $iva_porce[$jj]=0;
		  }
		 
		 if ($id_impuesto[$jj]=="IVA")
		 { //Se busca el monto base

		   for ($yy=0;  $yy<$elem_imp_iva; $yy++)
		   		{
						$monto_base[$jj]= $iva_monto_2[$yy];
						$yy=$elem_imp_iva;
				}//Del For
		  }
		  else
		  {
	          $monto_base[$jj]= $monto_sin_iva;
		  }
		   $porce_impuesto[$jj]=str_replace(",","", $_POST['opc_'.$i]);
		   $monto_reten[$jj]=str_replace(",","",$_POST['txt_monto_impu'.$i]);
		   $jj++;
		 } //Del  if $mon_temp
	} //Del For
	
	$elem_retencion=$jj;


	//Otras Retenciones  
	$max_otra_retencion=$_POST['hid_max_otra_retencion'];
	$elem_otra_retencion=trim($_POST['hid_elem_otra_retencion']);
	$j=0;
	for ($i=0; $i<$max_otra_retencion; $i++)
	{
	 $monto_ret=str_replace(",","",$_POST['monto_rete'.$i]);
	 $partida=trim($_POST['part_'.$i]);
	 if ($monto_ret>0){
  	  $id_partida[$j]=$partida;
  	  $monto_partida_reten[$j]=$monto_ret;
	  $j++;
	 }
	}


if ($elem_otra_retencion>0)
{
 $arreglo_part_rete = convierte_arreglo ($id_partida);
 $arreglo_monto_otras_retenciones = convierte_arreglo ($monto_partida_reten);

}else{
      $arreglo_part_rete ="{}";
      $arreglo_monto_otras_retenciones ="{}";
      }

	
$obs=trim($_POST['txt_observa']);
$sql_obs="select * from sai_modificar_sopg_obs('".$codigo."','".$obs."')";
//Si la Devolución ahora va a ser enviada a quien la originó, no se deben modificar las observaciones
if ( ($user_perfil_id!="42450") || ($request_id_opcion!=5)){
	$resultado_obs=pg_query($conexion,$sql_obs);
	if($resultado_obs === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
}
//Buscar c�digo en sai_sol_pago
$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p);
if($resultado_set_most_p === false){
	error_log(pg_last_error(), 0);
	echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
	exit;
}
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
	require_once("includes/fechas.php");
	$depe_id=trim($row['depe_solicitante']); //Solicitante
	$tp_sol=trim($row['sopg_tp_solicitud']);
	$sopg_monto=trim($row['sopg_monto']); 
	$sopg_fecha=trim($row['sopg_fecha']);
	$pres_anno=trim($row['pres_anno']);
	$esta_id=trim($row['esta_id']);
	$usua_login=trim($row['usua_login']); //Solicitante
	$sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
	$sopg_bene_tp=trim($row['sopg_bene_tp']);
	$sopg_detalle=trim($row['sopg_detalle']);
	$sopg_observacion=trim($row['sopg_observacion']);
	$fecha_crea=trim($row['sopg_fecha']);
	$factura_num=trim($row['sopg_factura']);
	$factura_control=trim($row['sopg_factu_num_cont']);
	$factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
	$fecha_sol=cambia_esp(trim($row['sopg_fecha']));
	$numero_reserva=trim($row['numero_reserva']);
	$comp_id=trim($row['comp_id']);
  
	$sql = "SELECT depe_nombre FROM sai_dependenci WHERE depe_id = '".$depe_id."'";
	$resultado_set_most_p=pg_query($conexion,$sql);
	if($resultado_set_most_p === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	$valido=$resultado_set_most_p;
	if($row=pg_fetch_array($resultado_set_most_p))
	{
		$dependencia=trim($row['depe_nombre']); //Solicitante
	}

	//Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
		resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
		empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so);
	if($sql_so === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$email=trim($rowso['empl_email']);
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
		$cargo=trim($rowso['carg_nombre']);
		//$dependencia=trim($rowso['depe_nombre']);
		$telefono=trim($rowso['empl_tlf_ofic']);
	}
	
	//Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	$sql_d="select * from sai_buscar_datos_sopg('',4,'','','$codigo','sopg','',0) 
	resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
	$resultado_set_most_d=pg_query($conexion,$sql_d);
	if($resultado_set_most_d === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	if($rowd=pg_fetch_array($resultado_set_most_d))
	{
		$prioridad=$rowd['docg_prioridad'];
	}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2:sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
		$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
			resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be);
		if($resultado_set_most_be === false){
			error_log(pg_last_error(), 0);
			echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
			exit;
		}
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
			$nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
			$depe_nombre_bene=trim($rowbe['depe_nombre']);
		}
	}
	else if($sopg_bene_tp==2) //Proveedor
	{
		$sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
			resultado_set(prov_nombre varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be);
		if($resultado_set_most_be === false){
			error_log(pg_last_error(), 0);
			echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
			exit;
		}
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
			$nombre_bene=$rowbe['prov_nombre'];
			//La dependencia es la del solicitante (buscarla por el usua_login registrado)
			$depe_nombre_bene=$dependencia;
		}
	}
	else if($sopg_bene_tp==3) //Otro beneficiario
	{
		$sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be);
		if($resultado_set_most_be === false){
			error_log(pg_last_error(), 0);
			echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
			exit;
		}
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
			$nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
			//La dependencia es la del solicitante (buscarla por el usua_login registrado)
			$depe_nombre_bene=$dependencia;
		}
	}
	
}//fin de consultar solicitud de pago

/*****************************
* Buscar los soportes
****************************/
$sql= "select * from sai_buscar_sopg_anexos ('".$codigo ."') as resultado ";
$sql.= "(sopg_id varchar,soan_factura bit , soan_ordc bit , soan_contrato bit, soan_certificacion bit, ";
$sql.= " soan_recibo bit, soan_ords bit, soan_pcta bit, soan_gaceta bit , soan_informe bit, soan_estimacion bit, soan_otro bit, soan_otro_deta varchar )"; 
$resultado_set = pg_query($conexion ,$sql);
if($resultado_set === false){
	error_log(pg_last_error(), 0);
	echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
	exit;
}

$valido=resultado_set;
if ($resultado_set)
{
	$anexos=array(11);
	for ($i=0; $i<11; $i++)
	{		 
		$anexos[$i]=0;
	}
	
	if (pg_num_rows($resultado_set) >0)
	{
		$row = pg_fetch_array($resultado_set,0); 
		$anexos_otros=trim($row['soan_otro_deta']);
		$anexos[0]=trim($row['soan_factura']);
		$anexos[1]=trim($row['soan_ordc']);
		$anexos[2]=trim($row['soan_contrato']);
		$anexos[3]=trim($row['soan_certificacion']);
		$anexos[4]=trim($row['soan_recibo']);
		$anexos[5]=trim($row['soan_ords']);
		$anexos[6]=trim($row['soan_pcta']);
		$anexos[7]=trim($row['soan_gaceta']);
		$anexos[8]=trim($row['soan_informe']);
		$anexos[9]=trim($row['soan_estimacion']);
		$anexos[10]=trim($row['soan_otro']);
	}
}
 
$otro=0;

/********************
 * Retenciones
 *******************/

if ($elem_retencion >0)
{
	// require_once("includes/arreglos_pg.php");
	$arreglo_id_impuesto = convierte_arreglo ($id_impuesto);
	$arreglo_iva_porce= convierte_arreglo($iva_porce);
	$arreglo_porce_impuesto= convierte_arreglo($porce_impuesto);
	$arreglo_monto_reten= convierte_arreglo($monto_reten);
	$arreglo_monto_base= convierte_arreglo($monto_base);
	$arreglo_servicio=convierte_arreglo($servicio);
}
else {
	$arreglo_id_impuesto = "{}";
	$arreglo_iva_porce=  "{}";
	$arreglo_porce_impuesto=  "{}";
	$arreglo_monto_reten= "{}";
	$arreglo_monto_base="{}";
	$arreglo_servicio="{}";
}

if ( ($user_perfil_id=="42450") && ($request_id_opcion==13))
{
	$sql= "select * from sai_modificar_retencion('".$codigo."','".$arreglo_id_impuesto."','";
	$sql.= $arreglo_iva_porce."','".$arreglo_porce_impuesto."','".$arreglo_monto_reten."','";
	$sql.= $arreglo_monto_base."','".$arreglo_servicio."','".$arreglo_part_rete."','";
	$sql.= $arreglo_monto_otras_retenciones."')"; 
	$resultado_retencion= pg_query($conexion,$sql);
	if($resultado_set === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
}

$expediente=$_POST['hid_expediente'];
$vector_expe=explode ("*" , $expediente);
$expediente=$vector_expe[0];
$apartado=$vector_expe[1];
$compromiso=trim($vector_expe[2]);
$causado=trim($vector_expe[3]);

/**************************************************
 * Consulto las retenciones previas del documento *
 *************************************************/

if ($valido)
{
	$elem_existe=0;
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set=  pg_query($conexion ,$sql);
	if($resultado_set === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	$valido=$resultado_set;
	if ($resultado_set)
	{
		$elem_existe=pg_num_rows($resultado_set);
		$id_impuesto_doc=array($elem_existe);
		$rete_monto_doc=array($elem_existe);
		$por_rete_doc=array($elem_existe);
		$por_imp_doc=array($elem_existe);
		$servicio_doc=array($elem_existe);
		$tt_retencion=0;
		$tt_neto=0;
		$ii=0;
		while($row_rete_doc=pg_fetch_array($resultado_set))	
		{
			$id_impuesto_doc[$ii]=trim($row_rete_doc['impu_id']);
			$rete_monto_doc[$ii]=trim($row_rete_doc['rete_monto']);
			$por_rete_doc[$ii]=trim($row_rete_doc['por_rete']);
			$por_imp_doc[$ii]=trim($row_rete_doc['por_imp']);
			$servicio_doc[$ii]=trim($row_rete_doc['servicio']);
			$tt_retencion=$rete_monto_doc[$ii]+$tt_retencion;
			$ii++; 
		}
		$tt_neto=$sopg_monto-$tt_retencion;
		
		require_once("includes/arreglos_pg.php");
		$arreglo_cod_retencion=convierte_arreglo($id_impuesto_doc);
		$arreglo_monto_retenciones=convierte_arreglo($rete_monto_doc);
	} 
}

/***************************
 * Buscar las Imputaciones *
 * *************************/

$total_imputacion=0;
if ($valido){
	$sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	$sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	if($resultado_set === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	$valido=$resultado_set;
	if ($resultado_set)
	{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
		{
			$matriz_depe[$i]=$row['depe_id'];
			$matriz_imputacion[$i]=trim($row['tipo']);
			$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); 
			$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); 
			$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); 
			$matriz_uel[$i]=trim($row['depe_id']); 
			$matriz_monto[$i]=trim($row['sopg_monto']); 
			$matriz_monto_exento[$i]=trim($row['sopg_monto_exento']); 
			$monto_partidas[$i]=trim($row['sopg_monto'])+trim($row['sopg_monto_exento']);
			$matriz_abono[$i]=1;
			$matriz_descr[$i]=("Causado para la Solicitud de Pago ". $codigo);
			$i++;
		}
		
		for ($j=0; $j<$max_otra_retencion; $j++)
		{
			$monto_ret=str_replace(",","",$_POST['monto_rete'.$j]);
			if ($monto_ret>0){
				$matriz_depe[$i]=$matriz_depe[$i-1];
				$matriz_imputacion[$i]=$matriz_imputacion[$i-1];
				$matriz_acc_pp[$i]=$matriz_acc_pp[$i-1]; 
				$matriz_acc_esp[$i]=$matriz_acc_esp[$i-1];
				$matriz_uel[$i]=$matriz_uel[$i-1];
				$matriz_abono[$i]=1;
				$matriz_descr[$i]=("Causado para la Solicitud de Pago ". $codigo);
				//$matriz_sub_esp[$i]=$id_partida[$j];				OJOJ
				//$monto_partidas[$i]=$monto_partida_reten[$j];
				$i++;
			}
		}
	}
	
	require_once("includes/arreglos_pg.php");
	$arreglo_uel=convierte_arreglo($matriz_depe);
	$arreglo_imputacion=convierte_arreglo($matriz_imputacion);
	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp);
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp);
	$arreglo_monto=convierte_arreglo($monto_partidas);
	$arreglo_abono=convierte_arreglo($matriz_abono);
	$arreglo_descr=convierte_arreglo($matriz_descr);
}
 
//Crear causado
//Si se realiza la modificación y ahora va a ser enviada al Adjunto, se debe generar nuevamente el causado y el asiento contable
if ( ($user_perfil_id=="42450") && ($request_id_opcion==13)) 
{
    $sql_caus = "SELECT * FROM sai_actualizar_causado('".$arreglo_sub_esp."', '". $arreglo_monto ."', '".$codigo."',
		'".$_SESSION['user_depe_id'] ."','". $elem_existe ."','".$arreglo_cod_retencion."','".$arreglo_monto_retenciones."','".$arreglo_part_rete."','".$arreglo_monto_otras_retenciones."','".$arreglo_sub_esp."',
		".$pres_anno .",'Causado para la Solicitud de Pago','".$arreglo_imputacion."','".$arreglo_acc_pp."','".$arreglo_acc_esp."','". $arreglo_abono ."','".$arreglo_descr."',
		'".$depe_id."','".$arreglo_uel ."') AS resultado";	
	$resultado_set = pg_query($conexion ,$sql_caus);
	if($resultado_set === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
}

//Si la Devolución ahora va a ser enviada a quien la originó, se deverá anular el apartado y compromiso
if ( ($user_perfil_id=="42450") && ($request_id_opcion==5)) 
{
	$sql = "UPDATE sai_doc_genera set estado_pres = 7 WHERE docg_id = '".$codigo."'";
	$resultado = pg_query($conexion,$sql);
	if($resultado === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}
	
	$sql_edo = "UPDATE sai_doc_genera set esta_id = 7 WHERE docg_id = '".$codigo."'";
	$result_insert_comp_auto=pg_query($conexion,$sql_edo);
	if($result_insert_comp_auto === false){
		error_log(pg_last_error(), 0);
		echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
		exit;
	}

	$codigo_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."', '".$memo_contenido."', 'Devolución de Solicitud de Pago','0', '0','0','',0, 0, '0', '','".$codigo."') as resultado");
	$result_memo=pg_query($conexion,$codigo_memo) or die ("Error al registrar el memo");

	/*
	// No eliminar las retenciones
	$query = "DELETE FROM sai_sol_pago_retencion WHERE sopg_id='".$codigo."'";
    $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al eliminar retenciones"));
    */
	
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Revisar SOPG</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>

</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body>

<?PHP
	if ($valido)
	{
 ?>
	<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr>
		<td colspan="4">
			<table width="100%">
				<tr class="td_gray"> 
					<td height="21" colspan="4" valign="middle"><div align="left" class="normalNegroNegrita"> 
						DATOS DEL SOLICITANTE </div></td>
				</tr>
				<tr> 
					<td height="28" colspan="3"><div class="normalNegrita" >Solicitud de Pago N&uacute;mero: <?php echo $codigo;?></div></td>
				</tr>
				<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">Solicitante:</div></td>
					<td width="421" class="normalNegro"> <?php echo $solicitante;?></td>
				</tr>
				<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">C&eacute;dula de Identidad:</div></td>
					<td class="normalNegro" width="581"> <?php echo $cedula;?></td>
				</tr>
				<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">Email:</div></td>
					<td class="normalNegro"><?php echo $email;?></td>
				</tr>
				<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">Cargo:</div></td>
					<td class="normalNegro"><?php echo $cargo;?></td>
				</tr>
				<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Dependencia Solicitante:</div></td>
					<td class="normalNegro"><?php echo $dependencia;?></td>
				</tr>
				<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Tipo de Solicitud: </div></td>
					<?
						$sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
							resultado_set(nombre_sol varchar)"; 
						$resultado_set_most_be=pg_query($conexion,$sql);
						if($resultado_set_most_be === false){
							error_log(pg_last_error(), 0);
							echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
							exit;
						}
						$valido= $resultado_set_most_be;
						if($rowbe=pg_fetch_array($resultado_set_most_be))
						{
							$nombre_sol=$rowbe['nombre_sol'];
						}
					?>
					<td class="normalNegro"><?echo $nombre_sol;?></td>
				</tr>
				<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
					<td class="normalNegro"><?php echo $telefono;?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<table width="100%">
				<tr class="td_gray"> 
					<td height="21" colspan="3" valign="middle"><div align="left" class="normalNegroNegrita"> 
						DATOS DEL BENEFICIARIO
					</div></td>
				</tr>
				<tr> 
					<td height="29" colspan="2"><div class="normalNegrita">Beneficiario:</div></td>
					<td width="581" class="normalNegro"><?php echo $nombre_bene;?></td>
				</tr>
				<tr> 
					<td height="28" colspan="2"><div class="normalNegrita">C.I. o RIF:</div></td>
					<td class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
				</tr>
				<tr> 
					<td height="30" colspan="2"><div class="normalNegrita">Dependencia:</div></td>
					<td class="normalNegro"><?php echo $depe_nombre_bene;?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<table width="100%" class="tablaalertas">
				<tr > 
        			<td height="21" colspan="8" valign="middle" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        				DOCUMENTOS ANEXOS
        			</div></td>
        		</tr>
				<tr  class="normalNegro"> 
        			<td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  disabled="disabled" <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 			<td height="21" >Factura</td>
        			<td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc" disabled="disabled" <?php if ($anexos[1]==1){echo "checked";}?>/></td>
					<td height="21" >Orden de Compra</td>
        			<td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato" disabled="disabled" <?php if ($anexos[2]==1){echo "checked";}?>/></td>
					<td height="21" >Contrato</td>
        			<td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion" disabled="disabled" <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        			<td height="21" >Certificaci&oacute;n del Control Perceptivo</td>
  				</tr>
				<tr  class="normalNegro"> 
			        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe" disabled="disabled" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
					<td height="21" >Informe o Solicitud de Pago a Cuentas</td>
			        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" disabled="disabled" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
			        <td height="21" >Orden de Servicio</td>
			        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="disabled" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
			        <td height="21" >Punto de Cuenta</td>
			    </tr>
				<tr  class="normalNegro"> 
			        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" disabled="disabled" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
			        <td height="21" >Otro (Especifique)</td>
					<td height="21"  ></td>
			        <td height="21"  ><input type="text" name="txt_otro" id="txt_otro" size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="disabled"></td>
		        </tr>
			 </table>
	 	</td>
	</tr>
	<tr>
		<td  colspan="4">
			<table width="100%">
				<tr class="td_gray"> 
					<td height="21" colspan="4" valign="middle"><div align="left" class="normalNegroNegrita"> 
						DETALLES DE LA SOLICITUD
					</div></td>
				</tr>
				<?php
					if (trim( $factura_num<>"")){
				?>
				<tr class="normal"> 
					<td height="28" valign="middle" colspan="2"> <div  class="normalNegrita">Factura N&deg;</div></td>
					<td>
						<input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20" align="right"  readonly="readonly" value="<?php echo $factura_num;?>"/>
						Fecha:
						<input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="readonly"  value="<?php echo $factura_fecha;?>" />
						N&deg; de Control:
						<input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10" align="right"  readonly="readonly"  value="<?php echo $factura_control;?>"/>				  	
					</td>
				</tr>
	  			<?php } ?>
				<tr class="normal"> 
					<td height="28" class="normalNegrita" colspan="2"> 
						<div >Prioridad:</div>
					</td>
					<td class="normal">
						<select name="slc_prioridad" class ="normalNegro" disabled>
        		 			<option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
       			  			<option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
        		 			<option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
				 		</select>
				 	</td>
				</tr>
				<tr class="normal"> 
					<td height="33" colspan="2" class="normalNegrita">
						<div >Fuente de Financiamiento:</div>
					</td>
					<td  class="normalNegro"><?
						$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id ='||'''$numero_reserva''','',2) resultado_set(fuef_descripcion varchar)";
						$resultado_set_most_p=pg_query($conexion,$sql);
						if($resultado_set_most_p === false){
							error_log(pg_last_error(), 0);
							echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
							exit;
						}
						if($row=pg_fetch_array($resultado_set_most_p))
						{
 				 			$fuente=trim($row['fuef_descripcion']); //Solicitante
						}
						echo $fuente;
					?></td>
				</tr>
				<tr class="normalNegrita"><td height="28" colspan="2" valign="midden" >N&uacute;mero del Compromiso</td>
					<td class="normalNegro"><?echo $comp_id;?></td></tr>
				<tr> 
					<td height="27" colspan="2"><div  class="normalNegrita">Motivo del Pago:</div></td>
					<td class="normalNegro" width="581"><?php echo $sopg_detalle;?></td>
				</tr>
				<tr> 
					<td height="21" colspan="2"><div class="normalNegrita">Fecha de Solicitud:</div></td>
					<td class="normalNegro"><?php echo $fecha_sol;?>			</td>
				</tr>
				<tr> 
					<td height="21" colspan="2"><div class="normalNegrita">Observaci&oacute;n:</div></td>
					<td class="normalNegro">
						<?php if($sopg_observacion==""){echo "No Posee";} else {echo $sopg_observacion;}?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<table width="60%" border="0"  class="tablaalertas" align="center">
          					<tr class="td_gray">
				            	<td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
							</tr>
							<tr>
								<td  class="normal" align="center" >
              						<table width="100%" border="0" >
										<tr>
											<td  class="peqNegrita" align="left"><div align="center">Centro Gestor</div></td>
											<td  class="peqNegrita" align="left"><div align="center">Centro Costo </div></td>
											<td width="10%" align="left"  class="peqNegrita"><div align="center">UEL.</div></td>
											<td width="15%" align="left"  class="peqNegrita"><div align="center">Partida/Cuenta contable</div></td>
											<td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Sujeto</div></td>
											<td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Exento</div></td>
										</tr>
                <tr>
                  <?php
		for ($ii=0; $ii<$total_imputacion; $ii++)
    	{
    		
        if ($matriz_imputacion[0]==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_query($conexion,$query);
    	if($resultado_query === false){
			error_log(pg_last_error(), 0);
			//echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
			//exit;
		}
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }
		?>
                  <td  class="peq" align="left" width="17%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="hidden" class="peq" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/>
 		      <input name=<?php echo "centro_gestor".$ii;?> type="text"  class="normalNegro" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $centrog;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="19%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="peq" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/>
		    <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $centroc;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="10%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center">
                    <?php 	if (substr($matriz_sub_esp[$ii],0,6)=="4.11.0"){
      				$convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$matriz_sub_esp[$ii]."'";
     				$res_convertidor=pg_query($conexion,$convertidor);
                    if($res_convertidor === false){
						error_log(pg_last_error(), 0);
						//echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
						//exit;
					}
      				if($row_conv=pg_fetch_array($res_convertidor)){
					 $cuenta= $row_conv['cpat_id'];
      				}          	
     			   }else{
     			   $cuenta=$matriz_sub_esp[$ii];
   				  }?>
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                </tr>
                <?php 
		 }
		 ?>
            </table></td>
          </tr>
		
		
  
		</table>			</td>
		</tr>
		
		</table>		</td>
	</tr>
		
	
	 <tr>
		<td class="normal" colspan="5" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_detalle_iva" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="2" align="center" class="normalNegroNegrita">
			DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA) 
			</td>
			</tr>
			<tr>
			<td height="19"  align="center" class="normalNegrita">Monto Base: 
			  <input name="txt_monto_subtotal" type="text"  class="normalNegro" id="txt_monto_subtotal" value="<?php echo $_POST['txt_monto_subtotal'];?>" size="25" maxlength="25" readonly="" align="right"> </td>
			<td height="19"  align="center" class="normalNegrita">Monto IVA: 
			  <input name="txt_monto_iva_tt" type="text"  class="normalNegro" id="txt_monto_iva_tt" value="<?php echo $_POST['txt_monto_iva_tt'];?>" size="25" maxlength="25" readonly="" align="right"> </td>
			</tr>
			<?php 
			if ($elem_imp_iva>0) 
			{
			?>
			<tr >
			<td width="50%" class="normalNegrita" align="center">Impuesto %</td>
			<td width="50%" class="normalNegrita" align="center">Monto (Bs.)</td>
			</tr>
			<?php 
			for ($xt=0; $xt<$elem_imp_iva; $xt++)
			{
			?> 
			<tr >
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text"  class="normalNegro" id=<?php echo "txt_iva%".$xt;?> value="<?php echo $iva_porce_2[$xt];?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text"  class="normalNegro" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo $iva_monto_2[$xt];?>"  size="25" maxlength="25" readonly="" align="right"></td>
			</tr>
			<?php
			} //Del For
			} //Del If
			?>
		</table>
		</td>	 
	</tr>	
	<tr> 
	<td height="21" colspan="4" valign="midden" class="td_gray" align="left">
	<div align="left"class="normalNegroNegrita">TOTAL A PAGAR</div></td>
	</tr>
	<tr> 
	<td height="21" valign="midden" align="left" class="normal"  colspan="4">
	Bs.:
	  <input type="text" name="txt_monto_pagar" size="20"  class="normalNegro" readonly="" value="<?php echo trim($_POST['txt_monto_pagar']); ?>">
	<input type="hidden" value="<?php echo $sopg_monto;?>" name="hid_monto_original">	</td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normal">
	En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70"  class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras']);?></textarea></td>
	</tr>

		<tr>
		<td class="normal" colspan="4" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_retenciones" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
			RETENCIONES <a href="javascript:abrir_ventana('http://www.seniat.gov.ve',700)">www.seniat.gov.ve</a></td>
			</tr>
			
			<tr>
			<tr class="td_gray">
			<td width="63%" class="normalNegroNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegroNegrita" align="center">Tipo de Retencion</td>
			<td width="10%" class="normalNegroNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegroNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
		   <?php
			 for ($ii=0; $ii<$elem_retencion; $ii++) 
			  {
			?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii;?>" type="text"  class="normalNegro" value="<?php echo $servicio[$ii];?>" size="60" maxlength="60" readonly="true"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii;?>" id= "<?php echo "txt_impuesto".$ii;?>" value="<?php echo $id_impuesto[$ii];?>"  class="normalNegro" size="10" readonly="true"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii;?>" id= "<?php echo "txt_iva_".$ii;?>" value="<?php if ($iva_porce[$ii] >0) { echo (number_format($iva_porce[$ii],2,'.',',')); } else {echo "s/n";} ?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="12%" class="normal" align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo  (number_format($porce_impuesto[$ii],2,'.',',')); ?>"  class="normalNegro" size="6" readonly="true"></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"   class="normalNegro" value="<?php echo  (number_format($monto_reten[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="true" align="right" id="<?php echo "txt_monto_impu".$jj?>">			</td>
			</tr>
			<?php
			} //-->Del For 
		?>
		
			<tr>
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL RETENCIONES :		      </td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text"  class="normalNegro" id="txt_monto_retenciones_tt" value="<?php echo number_format($tt_retencion,2,'.',','); ?>" size="25" maxlength="25" readonly="" align="right"> </td>
			</tr>
			</table>
		</td>
		</tr>

<tr>
		<td class="normal" colspan="4" align="center" ><br>
			<table width="100%" class="tablaalertas" align="center" id="tbl_otras_retenciones" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
			OTRAS RETENCIONES </td>
			</tr>
			
			<tr>
			<tr class="td_gray">
			<td width="12%" class="normalNegroNegrita" align="center">Cuenta</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
		   <?php
			 
			 for ($ii=0; $ii<$elem_otra_retencion; $ii++) 
			  {
			   $p=$id_partida[$ii];
			   
			   $sql_part="SELECT  t1.cpat_id,cpat_nombre FROM sai_convertidor t1,  sai_partida t2 ,sai_cue_pat t3
             WHERE t1.part_id=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'  and t2.part_id='".$p."' 
		     and t1.cpat_id=t3.cpat_id";
		  	 $resultado_set_part=pg_query($conexion,$sql_part);
			  if($resultado_set_part === false){
				error_log(pg_last_error(), 0);
				echo "Error en la consulta de la solicitud de pago. Por favor comun&iacute;quese con el administrador del sistema.";
				exit;
			}
		   	 if($rowbe=pg_fetch_array($resultado_set_part))
		   	 {
		   	  $nombre_part=$rowbe['cpat_nombre'];
		   	  $id_cuepat=$rowbe['cpat_id'];
		     }
			?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "part_".$ii?>" id="<?php echo "part_".$ii;?>" type="text"   class="normalNegro"value="<?php echo  $id_cuepat.":".$nombre_part;?>" size="60" maxlength="60" readonly="true"></td>

			<td width="15%" align="center" class="normal">
			<input name="<?php echo "monto_rete".$ii?>" type="text"  class="normalNegro" value="<?php echo  (number_format($monto_partida_reten[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="true" align="right" id="<?php echo "monto_rete".$jj?>">			</td>
			</tr>
			<?php
			} //-->Del For 
		?>

		<tr> 
	
	<td height="21" colspan="4" valign="midden" class="td_gray" align="left">
	<div align="left"class="normalNegroNegrita">MONTO NETO DEL PAGO</div></td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">	En Bs.
	  <input type="text" name="txt_monto_neto" size="20"  class="normalNegro" readonly="true" value="<?php echo (number_format($_POST['txt_monto_neto'],2,'.',','));  ?>"></td>
	</tr> 
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">
	En Letras:<textarea name="txt_monto_letras_neto" id="txt_monto_letras_neto" rows="2" cols="65"  class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras_neto']);?></textarea></td>
	
	</tr>
	<tr>
		  <td colspan="4" class="normal" align="center">&nbsp;</td>
		  </tr>
		<tr>
		  <td colspan="4" class="normal" align="center">
		  <table width="420" align="center">
			<?  		   
           include("includes/respaldos_mostrar.php");
			?>
		</table>
		  
		  </td>
		  </tr>
	<tr>
		<td height="128" colspan="4" class="normal" align="center"><br>
		  <br>
		<div align="center">
			Esta Solicitud de Pago fue Revisada por la Coordinaci&oacute;n de Asistencia Administrativa<br>
			el d&iacute;a: 
			<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?>
			<br>
			<br>
			<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
			<br><br>
			 
		  </div>	
	  </td>
	</tr>
	</table>
<?php
}
else
   {
     ?>
	  		<table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="middle"><div align="left" class="normalNegrita"> 
			SOLICITUD DE PAGO</div></td>
			</tr>
  			<tr>
    		<td colspan="4" class="normal"><br>
    		<div align="center">
			
			Ha ocurrido un error al revisar los datos
			<br><?php echo(pg_errormessage($conexion)); ?><br>
			<img src="imagenes/mano_bad.gif" width="31" height="38">
			<br><br>
</div>
			</td>
  			</tr>
			</table>
	 <?php
   }
   ?>
</body>
</html>


