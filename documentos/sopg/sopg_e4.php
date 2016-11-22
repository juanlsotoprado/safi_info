
<?php 
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
	
$obs=trim($_POST['txt_observa']);
$sql_obs="select * from sai_modificar_sopg_obs('".$codigo."','".$obs."')";
$resultado_obs=pg_query($conexion,$sql_obs);


if ($request_id_opcion==5){
 $estado=7; /*Devuelto*/
}else{
	$estado=13; /*Aprobado*/

}

$partida_IVA=trim($_SESSION['part_iva']);
/****************************/
$sql  = "select * from sai_revisar_sopg('". $codigo. "' , '" .$obs . "', " .$estado . " ) As valor";
$resultado_reg=pg_query($conexion ,$sql);
if($resultado_reg)
{
	if($row_reg=pg_fetch_array($resultado_reg))
	{
		//Buscar c�digo en sai_sol_pago
		$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
		$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
		if($row=pg_fetch_array($resultado_set_most_p))
		{
	      require_once("includes/fechas.php");
		  $depe_id=trim($row['depe_solicitante']); //Solicitante
		  $dependencia_id=trim($row['depe_id']); //Solicitante
		  $tp_sol=trim($row['sopg_tp_solicitud']);
		  $sopg_monto=trim($row['sopg_monto']); 
		  $sopg_fecha=trim($row['sopg_fecha']);
		  $pres_anno=trim($row['pres_anno']);
		  $esta_id=trim($row['esta_id']);
		  $usua_login=trim($row['usua_login']); //Solicitante
		  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
		  $sopg_bene_tp=trim($row['sopg_bene_tp']);
		  $sopg_detalle=trim($row['sopg_detalle']);
		  $sopg_sustitucion=trim($row['sopg_sustitucion']);
		  $sopg_observacion=trim($row['sopg_observacion']);
		  $sopg_imp_p_c=trim($row['sopg_imp_p_c']); //proyecto o accion central
		  $sopg_imputa=trim($row['sopg_imputa']); //acciones especificas
		  $sopg_tp_imputacion=trim($row['sopg_tp_imputacion']); // 1:proyecto 0:accion central
		  $factura_num=trim($row['sopg_factura']);
		  $factura_control=trim($row['sopg_factu_num_cont']);
		  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
		  $fecha_sol=cambia_esp(trim($row['sopg_fecha']));
		  $numero_reserva=trim($row['numero_reserva']);
	      $comp_id=trim($row['comp_id']);
		  
$sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2)
resultado_set(depe_nombre varchar)"; 
$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
 $dependencia=trim($row['depe_nombre']); //Solicitante
}

			
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
			   resultado_set(nombre_sol varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
				 
			   }

			//Datos del Solicitante
			$sql_so="select * from sai_buscar_usuario('$usua_login','')
			resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
			empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
			$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
			if($rowso=pg_fetch_array($resultado_set_most_so))
			{
				$email=trim($rowso['empl_email']);
				$cedula=$rowso['empl_cedula'];
				$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
				$cargo=trim($rowso['carg_nombre']);
				//$dependencia=trim($rowso['depe_nombre']);
				$telefono=trim($rowso['empl_tlf_ofic']);
			}
		        //Actualizar la Prioridad del Documento 
			$nueva_prioridad=$_POST['slc_prioridad'];
			$sql_prioridad="select * from sai_modificar_prioridad_doc_genera('$codigo','$nueva_prioridad') as resultado";
			$resultado_prioridad=pg_query($conexion,$sql_prioridad) or die ("Error al Modificar Prioridad del Documento");
			//Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
			$sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','sopg','',0) 
			resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
			$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
			if($rowd=pg_fetch_array($resultado_set_most_d))
			{
		    	$prioridad=$rowd['docg_prioridad'];
			 
			}
			
			//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
			if($sopg_bene_tp==1) //Empleado
			{
				$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
				resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
				$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
				if($rowbe=pg_fetch_array($resultado_set_most_be))
				{
				   $nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
				   $depe_nombre_bene=trim($rowbe['depe_nombre']);
				}
			}
			else
			   if($sopg_bene_tp==2) //Proveedor
			   {
				   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
				   resultado_set(prov_nombre varchar)"; 
				   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
				   if($rowbe=pg_fetch_array($resultado_set_most_be))
				   {
					  $nombre_bene=$rowbe['prov_nombre'];
					 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
					 $depe_nombre_bene=$dependencia;
				   }
			   }
			   else
				   if($sopg_bene_tp==3) //Otro beneficiario
				   {
					   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
					   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
					   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
					   if($rowbe=pg_fetch_array($resultado_set_most_be))
					   {
						  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
						 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
						  $depe_nombre_bene=$dependencia;
					   }
				   }
		
			
		}//fin de consultar solicitud de pago
	}

//Si se aprob� la sopg
if ($request_id_objeto_sig == 99) {	

		$mensaje_pendiente = " Iniciar Pago con Cheque. ";
		$perfil_mensaje_pendiente = utf8_decode("El Departamento de Tesorería debe ");
		
		$sql_comp="SELECT pcta_id FROM sai_comp WHERE comp_id='".$comp_id."'";
		$result_comp=pg_exec($conexion,$sql_comp);
		if ($row_comp=pg_fetch_array($result_comp)){
			$pcta_actual=$row_comp['pcta_id'];
		} 
		
		//if ($tp_sol=="19"){
		if (substr($nombre_sol,0,10)=="Honorarios"){//HONORARIOS DE CUALQUIER GERENCIA
			
			$sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$sopg_bene_ci_rif."'";
			$result_islr=pg_exec($conexion,$sql_islr);
			if ($row_islr=pg_fetch_array($result_islr)){
			 $monto_actualizado=$_POST['monto_neto']+$row_islr['monto_pagado'];
			 $validar_islr=$row_islr['validar'];
			 $pcta=$row_islr['pcta_id'];
			
			 if ($pcta==$pcta_actual){
			  if (($monto_actualizado<6333) && ($validar_islr==1) )
			   $sql="UPDATE sai_retenciones_islr SET monto_pagado=".$monto_actualizado." WHERE cedula='".$sopg_bene_ci_rif."'";
			  else
 			   $sql="UPDATE sai_retenciones_islr SET monto_pagado=0, validar=0 WHERE cedula='".$sopg_bene_ci_rif."'";			 
			   $result=pg_query($conexion,$sql);
			 }else{
			 	   $sql="UPDATE sai_retenciones_islr SET monto_pagado=".$_POST['monto_neto'].", validar=1,pcta_id='".$pcta_actual."' WHERE cedula='".$sopg_bene_ci_rif."'";
			 	   $result=pg_query($conexion,$sql);
			      }
			}else{
			$sql="INSERT INTO sai_retenciones_islr VALUES ('".$sopg_bene_ci_rif."',".$_POST['monto_neto'].",1,'".$pcta_actual."')";
			$result=pg_query($conexion,$sql);
			
			}
		}
}
	?>


<?php //buscar los soportes
   $sql= "select * from sai_buscar_sopg_anexos ('".$codigo ."') as resultado ";
   $sql.= "(sopg_id varchar,soan_factura bit , soan_ordc bit , soan_contrato bit, soan_certificacion bit, ";
   $sql.= " soan_recibo bit, soan_ords bit, soan_pcta bit, soan_gaceta bit , soan_informe bit, soan_estimacion bit, soan_otro bit, soan_otro_deta varchar )"; 
   $resultado_set = pg_exec($conexion ,$sql);
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

  $expediente=$_POST['hid_expediente'];
  $vector_expe=explode ("*" , $expediente);
  $expediente=$vector_expe[0];
  $apartado=$vector_expe[1];
  $compromiso=trim($vector_expe[2]);
  $causado=trim($vector_expe[3]);

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
				  if (  $iva_porce[$jj] ==$iva_porce_2[$yy] )
						{
						$monto_base[$jj]= $iva_monto_2[$yy];
						$yy=$elem_imp_iva;
						}  //Del if iva_porce 
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
?>	

<?php /*Buscar las imputaciones*/
if ($valido )
{
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (codigo varchar, acc_pp varchar, acc_esp varchar, depe_id varchar, sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
 	  $resultado_set = pg_exec($conexion ,$sql);
				$valido= $resultado_set ;
				if ($resultado_set)
				{
					$total_imputacion=pg_num_rows($resultado_set);
					$acc_pp=array($total_imputacion);
					$acc_esp=array($total_imputacion);
					$depe_id=array($total_imputacion);
					$sub_esp=array($total_imputacion);
					$monto_especial=array($total_imputacion);
					$tipos_acc_pp=array($total_imputacion);
					$comp_abono=array($total_imputacion);
					$comp_descr=array($total_imputacion);
					$i=0;
					while($row = pg_fetch_array($resultado_set))
					{
					   $acc_pp[$i]=$row['acc_pp'];
					   $acc_esp[$i]=$row['acc_esp'];
				  	   $depe_id[$i]=$row['depe_id'];
					   $sub_espe[$i]=$row['sub_espe'];
					   $monto_especial[$i]=$row['sopg_monto'];
					   $monto_exento[$i]=$row['sopg_monto_exento'];
					   $monto_partida[$i]=$row['sopg_monto']+$row['sopg_monto_exento'];
					   $tipos_acc_pp[$i]=$row['tipo'];
					   $comp_abono[$i]=1;
					   $comp_descr[$i]=("Compromiso para la Solicitud de Pago ". $codigo);
					   $i++;
							   }

		if ($tipos_acc_pp[0]==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($acc_pp[0])."','".trim($acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($acc_pp[0])."','".trim($acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }
				}
		require_once("includes/arreglos_pg.php");
		$arreglo_uel=convierte_arreglo($depe_id);
		$arreglo_tipo_impu=convierte_arreglo($tipos_acc_pp);
		$arreglo_acc_pp=convierte_arreglo( $acc_pp);
		$arreglo_acc_esp=convierte_arreglo($acc_esp);
		$arreglo_sub_esp=convierte_arreglo($sub_espe);
		
		$arreglo_monto=convierte_arreglo($monto_partida);
		$arreglo_apde_abono=convierte_arreglo($comp_abono);
		$arreglo_apde_descr=convierte_arreglo($comp_descr);
		}		

/*Consulto las retenciones previas del documento */
if ($valido)
  {
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
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
		} 
	}	

 /*Consulto las OTRAS retenciones del documento */
  $sql_be="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre, t3.cpat_id,cpat_nombre
 FROM sai_sol_pago_otra_retencion t1, sai_partida t2 ,sai_convertidor t3,sai_cue_pat t4
 WHERE sopg_id='".$codigo."' AND t1.sopg_partida_rete=t2.part_id and 
 t2.pres_anno='".$_SESSION['an_o_presupuesto']."' 
 AND t3.part_id=t2.part_id AND t3.cpat_id=t4.cpat_id"; 
    
	$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	$resultado_part_rete=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	
	if ($resultado_set_most_be)
	{
	 
	  $elem_otras_rete=pg_num_rows($resultado_set_most_be);
	  $nombre_part=array($elem_otras_rete);
	  $id_partida=array($elem_otras_rete);
	  $monto_rete=array($elem_otras_rete);
	  $id_cta=array($elem_otras_rete);

	$ii=0;
	$total_otras_rete=0;
	  while($rowbe=pg_fetch_array($resultado_part_rete))
	  {
	   	  $nombre_part[$ii]=$rowbe['cpat_nombre'];
	      $id_partida[$ii]=$rowbe['sopg_partida_rete'];
		  $monto_rete[$ii]=$rowbe['sopg_ret_monto'];
		  $id_cta[$ii]=$rowbe['cpat_id'];
		  $total_otras_rete=$total_otras_rete+$monto_rete[$ii];
		  $ii++;
	   }
 		$tt_neto=$tt_neto-$total_otras_rete;
	}
if ($elem_otras_rete>0)
{
 $arreglo_part_rete = convierte_arreglo ($id_partida);
 $arreglo_monto_otras_retenciones = convierte_arreglo ($monto_rete);

}else{
      $arreglo_part_rete ="{}";
      $arreglo_monto_otras_retenciones ="{}";
      }

/*CUANDO ANULA LA SOPG EL ADJUNTO HAY QUE REALIZAR LOS REVERSOS*/
 if (($user_perfil_id=="46450") && ($request_id_opcion==5)) 
 {  $cero="0";
	
    $sql  = "select * from sai_devolver_sopg('".trim($_SESSION['login'])."','".trim($codigo)."','";
    $sql  .= trim($memo_contenido)."','".trim($_SESSION['user_depe_id'])."','";
    $sql  .= $arreglo_part_rete."','".$arreglo_monto_otras_retenciones."','";
    $sql  .= $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$cero."') as memo_id";

   	$resultado_set = pg_exec($conexion ,$sql);
	$valido=resultado_set;
	if($resultado_set)
	{
			$row = pg_fetch_array($resultado_set,0); 
			if ($row[0] <> null)
			{
			  $memo_id=$row[0];
			}
	}
	} //Coordinador de Asistencia Administrativa	


    $elem_imp_iva=trim($_POST['hid_elem_imp_iva']);
	$iva_porce_2=array($elem_imp_iva);
	$iva_monto_2=array($elem_imp_iva);
	for ($i=0; $i<$elem_imp_iva; $i++)
	{
	 $iva_porce_2[$i]=str_replace(",","",$_POST['txt_iva%'.$i]);
	 $iva_monto_2[$i]=str_replace(",","",$_POST['txt_iva_monto%'.$i]);
	}
	$monto_sin_iva=str_replace(",","",$_POST['txt_monto_subtotal']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Revisar SOPG</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript">

function revisar()
{
   if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?"))
   {
	 document.form.submit();
   }	
}
</script>

</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body >
 <?php 
 if  ($valido) {?>
		<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
			<td colspan="4">
				<table width="100%">
					  <tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">REVISAR SOLICITUD DE PAGO</td>
  </tr>
				<tr class="td_gray"> 
				<td height="21" colspan="4" valign="midden"><div align="left" class="normalNegroNegrita"> 
				DATOS DEL SOLICITANTE </div></td>
				</tr>
				<tr> 
				<td height="28"  colspan="2"><div class="normalNegrita">Solicitud de Pago</div></td> 
				<td class="normalNegro" width="531"><?php echo $codigo;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2"><div  class="normalNegrita">Solicitante:</div></td>
				<td class="normalNegro"> <?php echo $solicitante;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2"><div class="normalNegrita">C&eacute;dula de Identidad:</div></td>
				<td class="normalNegro"> <?php echo $cedula;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2"><div class="normalNegrita">Email:</div></td>
				<td class="normalNegro"><?php echo $email;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2"><div  class="normalNegrita">Cargo:</div></td>
				<td class="normalNegro"> <?php echo $cargo;?></td>
				</tr>
				<tr> 
				<td height="30" colspan="2"><div  class="normalNegrita">Dependencia Solicitante:</div></td>
				<td class="normalNegro"><?php echo $dependencia;?></td>
				</tr>
			<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Tipo de Solicitud: </div></td>
			<td class="normalNegro"><?echo $nombre_sol;?></td>
			</tr>
				<tr> 
				<td height="30" colspan="2"><div  class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
				<td class="normalNegro"><?php echo $telefono;?></td>
				</tr>
				</table>			</td>
		</tr>
		
	
		<tr>
			<td colspan="4">
				<table width="100%">
				<tr class="td_gray"> 
				<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
				DATOS DEL BENEFICIARIO</div></td>
				</tr>
				<tr> 
				<td height="29" colspan="2"><div class="normalNegrita">Beneficiario:</div></td>
				<td width="531" class="normalNegro"><?php echo $nombre_bene;?></td>
				</tr>
				<tr> 
				<td height="28" colspan="2"><div  class="normalNegrita">C.I. o RIF:</div></td>
				<td class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
				</tr>
				<tr> 
				<td height="26" colspan="2"><div class="normalNegrita">Dependencia:</div></td>
				<td  class="normalNegro"><?php echo $depe_nombre_bene;?></td>
				</tr>
				</table>			</td>
		</tr>
		<tr>
	<td colspan="4">
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        DOCUMENTOS ANEXOS</div></td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  disabled="true" <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 <td height="21">Factura</td>
        <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc" disabled="true" <?php if ($anexos[1]==1){echo "checked";}?>/></td>
		<td height="21" >Orden de Compra</td>
        <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato" disabled="true" <?php if ($anexos[2]==1){echo "checked";}?>/></td>
		<td height="21" >Contrato</td>
        <td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion" disabled="true" <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        <td height="21" >Certificaci&oacute;n del Control Perceptivo</td>
  </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe" disabled="true" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
		<td height="21" >Informe o Solicitud de Pago a Cuentas</td>
        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" disabled="true" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21" >Orden de Servicio</td>
        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="true" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21" >Punto de Cuenta</td>
	    </tr>

		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" disabled="true" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
        <td height="21" >Otro (Especifique)</td>
		<td height="21" ></td>
        <td height="21" ><input type="text" name="txt_otro" id="txt_otro" class="normal" size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="true"></td>
        </tr>

	 </table>
	 </td>
	 </tr> 
	
		<tr>
			<td height="176" colspan="4">
				<table width="100%">
				<tr class="td_gray"> 
				<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
				DETALLES DE LA SOLICITUD </div></td>
				</tr>
				<?php
				if (trim( $factura_num<>"")){ ?>

				<tr class="normal"> 
					  <td height="28" valign="midden" colspan="2"> <div class="normalNegrita">Factura N&deg;
					</td><td>		<input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20"   valign="right"  align="right"  readonly="true" value="<?php echo $factura_num;?>"/>
				  <font class="normalNegrita">Fecha:</font>
				  <input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="true"  value="<?php echo $factura_fecha;?>" />
				  <font class="normalNegrita">N&deg; de Control:</font>
				  <input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10"   valign="right"  align="right"  readonly="true"  value="<?php echo $factura_control;?>"/>
				  </div></td>
					  </tr>
	  		<?php } ?>
				<tr class="normal"> 
				<td height="28" class="normalNegrita" colspan="2"> 
				<div>Prioridad:</div>				</td>
				<td class="normal"><select name="slc_prioridad" class ="normalNegro" disabled>
        		 <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
       			  <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
        		 <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
				 </select>						</td>
				</tr>
</tr>
		<tr class="normal"> 
		<td height="33" colspan="2" class="normalNegrita">
		<div >Fuente de Financiamiento: 
		</div></td>
		<td  class="normalNegro" width="531"><?
		$sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id = '||'''$numero_reserva''','',2) resultado_set(fuef_descripcion varchar)"; 
				$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
				if($row=pg_fetch_array($resultado_set_most_p))
				{
 				 $fuente=trim($row['fuef_descripcion']); //Solicitante
				}
	
	echo $fuente;?></td>
		</tr>
				<tr><td height="28" colspan="2" valign="midden" class="normalNegrita">N&uacute;mero del Compromiso</td>
			<td  class="normalNegro"><?echo $comp_id;?></td></tr>	
				<tr> 
				<td height="27" colspan="2"><div  class="normalNegrita">Motivo del Pago:</div></td>
				<td class="normalNegro"><?php echo $sopg_detalle;?></td>
				</tr>
				<tr> 
				<td height="21" colspan="2"><div class="normalNegrita">Fecha de Solicitud:</div></td>
				<td class="normalNegro"><?php echo $fecha_sol;?>				</td>
				</tr>
				<tr> 
				<td height="21" colspan="2"><div class="normalNegrita">Observaci&oacute;n:</div></td>
				<td class="normalNegro">
				<?php if($sopg_observacion==""){echo "No posee";}else {echo $sopg_observacion; }?>				</td>
				</tr>
			</table>			</td>
		</tr>
		<tr>
		<td colspan="4">
		<table width="60%" border="0"  class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
          </tr>
		  
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td  class="peqNegrita" align="left"><div align="center">Centro Gestor</div></td>
                  <td  class="peqNegrita" align="left"><div align="center">Centro Costo</div></td>
                  <td width="10%" align="left"  class="peqNegrita"><div align="center">UEL.</div></td>
                  <td width="15%" align="left"  class="peqNegrita"><div align="center">Partida/Cuenta contable</div></td>
                  <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Sujeto</div></td>
		 <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto Exento</div></td>
                </tr>
                <tr>
                  <?php
		for ($ii=0; $ii<$total_imputacion; $ii++)
    	{
		?>
                  <td  class="peq" align="left" width="17%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="hidden"  class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $acc_pp[$ii];?>" readonly="true"/>
                     <input name=<?php echo "centro_gestor".$ii;?> type="text"  class="normalNegro" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $centrog;?>" readonly="true"/>    
		</div></td>
                  <td  class="peq" align="left" width="19%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden"  class="normalNegro" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $acc_esp[$ii];?>" readonly="true"/>
		     <input name=<?php echo "centro_costo".$ii;?>  type="texto"  class="normalNegro" id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $centroc;?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="10%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text"  class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $depe_id[$ii];?>" readonly="true"/>
                    </div></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center">
                   <?php 	if (substr($sub_espe[$ii],0,6)=="4.11.0"){
      				$convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$sub_espe[$ii]."'";
     				$res_convertidor=pg_query($conexion,$convertidor);
      				if($row_conv=pg_fetch_array($res_convertidor)){
					 $cuenta= $row_conv['cpat_id'];
      				}          	
     			   }else{
     			   $cuenta=$sub_espe[$ii];
   				  }?>                    
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text"  class="normalNegro"id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text"  class="normalNegro"id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($monto_especial[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                  <td  class="peq" align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text"  class="normalNegro"id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($monto_exento[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>

                </tr>
                <?php 
		 }
		 ?>
            </table></td>
          </tr>
		
		
		<tr> 
   
		</table>			</td>
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
			  <input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="<?php echo $_POST['txt_monto_subtotal'];?>" size="25" maxlength="25" readonly="" align="right"> </td>
			<td height="19"  align="center" class="normalNegrita">Monto IVA: 
			  <input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="<?php echo $_POST['txt_monto_iva_tt'];?>" size="25" maxlength="25" readonly="" align="right"> </td>
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
			<td width="50%" class="normalNegro" align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text" class="peq" id=<?php echo "txt_iva%".$xt;?> value="<?php echo $iva_porce_2[$xt];?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%" class="normalNegro" align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text" class="peq" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo $iva_monto_2[$xt];?>"  size="25" maxlength="25" readonly="" align="right"></td>
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
	  <input type="text" name="txt_monto_pagar" size="20" class="normalNegro" readonly="true" value="<?php echo trim($_POST['txt_monto_pagar']); ?>">
	<input type="hidden" value="<?php echo $sopg_monto;?>" name="hid_monto_original">	</td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normal">
	En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="70" class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras']);?></textarea></td>
	</tr>

		<tr>
		<td class="normal" colspan="5" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_retenciones" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
			RETENCIONES <a href="javascript:abrir_ventana('http://www.seniat.gov.ve',700)">www.seniat.gov.ve</a></td>
			</tr>
			
			<tr>
			<tr class="td_gray">
			<td width="63%" class="normalNegroNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegroNegrita" align="center">Tipo de Retenci&oacute;n</td>
			<td width="10%" class="normalNegroNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegroNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?php
			  
			  for ($ii=0; $ii<$elem_existe; $ii++) 
			  {
			?>
			<tr>
			<td width="64%"  align="center">
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii?>" type="text"  class="normalNegro" value="<?php echo $servicio_doc[$ii];?>" size="60" maxlength="60" readonly="true"> </td>
			<td width="10%"  align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii?>" id= "<?php echo "txt_impuesto".$ii?>" value="<?php echo $id_impuesto_doc[$ii];?>" class="normalNegro" size="10" readonly="true"></td>
			<td width="10%" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii?>" id= "<?php echo "txt_iva_".$ii?>" value="<?php if ($por_imp_doc[$ii]==0) {echo "s/n";} else {echo $por_imp_doc[$ii];}?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="12%"  align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo $por_rete_doc[$ii]; ?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="15%" align="center" >
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"  class="normalNegro" value="<?php echo  (number_format($rete_monto_doc[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="true" align="right" id="<?php echo "txt_monto_impu".$jj?>">			</td>
			</tr>
			<?php
			} //-->Del For 
		?>
		
		
			<tr>
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL RETENCIONES :		      </td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text" class="normalNegro" id="txt_monto_retenciones_tt" value="<?php echo(number_format($tt_retencion,2,'.',',')); ?>" size="25" maxlength="25" readonly="true" align="right"> </td>
			</tr>
			</table>		</td>
		</tr>
	
<?if ($elem_otras_rete>0){?>
<tr>
		<td class="normal" colspan="5" align="center" ><BR>
			<table width="100%" class="tablaalertas" align="center" id="tbl_otras_retenciones" border="0">
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
                         OTRAS RETENCIONES </td>
			</tr>
			<tr class="td_gray">
			<td width="12%" class="normalNegrita" align="center">Cuenta</td>
			<td width="15%" class="normalNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?  for ($ii=0; $ii<$elem_otras_rete; $ii++) 
			  {
 			?>
			<tr><td width="15%" align="center" class="normal">
			<input name="<?php echo "part_".$ii?>" type="text"  class="normalNegro" value="<?php echo  $id_cta[$ii].":".$nombre_part[$ii];?>" size="35" maxlength="35" align="right" id="<?php echo "part_".$ii?>" disabled="true"></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "monto_rete".$ii?>" type="text"  class="normalNegro" value="<?php echo  number_format($monto_rete[$ii],2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$ii?>" disabled="true"></td></tr>
			<?}?>
			<tr>
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL OTRAS RETENCIONES :		      </td>
			<td height="19" colspan="4" align="center" class="normalNegrita"><input name="monto_rete_part" type="text" class="normalNegro" id="monto_rete_part" value="<?php echo(number_format($total_otras_rete,2,'.',',')); ?>" size="25" maxlength="25" readonly="true" align="right"> </td>
			</tr>
			   
			</table></td></tr>
<?}?>


		<tr  > 
	<td height="21" colspan="4" valign="midden" class="td_gray" align="left">
	<div align="left"class="normalNegroNegrita">MONTO NETO DEL PAGO</div></td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">	En Bs.
	  <input type="text" name="txt_monto_neto" size="20" class="normalNegro" readonly="true" value="<?php echo $_POST['txt_monto_neto'];  ?>"></td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">
	En Letras:<textarea name="txt_monto_letras_neto" id="txt_monto_letras_neto" rows="2" cols="65" class="normalNegro" readonly><?php echo trim($_POST['txt_monto_letras_neto']);?></textarea></td>
	</tr>
    <tr>
      <td height="18" colspan="4">&nbsp;</td>
    </tr>
    <tr>
      <td height="18" colspan="4">
	  <table width="420" align="center">
			<?  		   
           include("includes/respaldos_mostrar.php");
			?>
		</table>
	  
	  </td>
    </tr>
		<tr>
		<td  colspan="4" class="normal" align="center">			 <div align="center">
				Esta Solicitud de Pago fue Revisada el d&iacute;a: 
				<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?>
				<br><br>
				<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
				<br><br>
		  		</div>		  </td>
		</tr>
	  </table>
        <?php
}
}//Del If $valido
?>
<?php 
	if ($valido==false)
   {
   	?>	 		
        <table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray"> 
			<td height="23" colspan="3" valign="midden"><span class="normalNegrita">
			SOLICITUD DE PAGO</span></td>
			</tr>
  			<tr>
    		<td colspan="4" class="normal"><br><div align="center">
			<img src="imagenes/vineta_azul.gif" width="11" height="7">
			Ha ocurrido un error al registrar la acci&oacute;n de revisar
			<br><?php echo(pg_errormessage($conexion)); ?><br><br>
			<img src="imagenes/mano_bad.gif" width="31" height="38">
			<br><br>
			
			</div>
			</td>
  			</tr>
</table>
	<?php
   } ?>
</body>
</html>
