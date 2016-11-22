<?php 
  ob_start();
  require_once("includes/conexion.php");
  require("includes/perfiles/constantesPerfiles.php");
  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }

 require("includes/fechas.php");
 $user_perfil_id = $_SESSION['user_perfil_id'];

 $memo_contenido=trim($_POST['contenido_memo']);
 if ($memo_contenido==""){
 	$memo_contenido="No Especificado";
 }
 $cod_doc = $request_codigo_documento;
 $codigo= $cod_doc;


if ($user_perfil_id==$_SESSION['perfil_jp']) {
 if ($_POST['infocentro']<>''){	
  $infocentro=explode(":",$_POST['infocentro']); 
  $id_info=trim($infocentro[0]);		
  $sql = "update sai_pcuenta set infocentro_id='".$id_info."' where pcta_id='".$codigo."'";
  $resultado_set= pg_exec($conexion ,$sql);
 }
 $sql = "update sai_pcuenta set tipo_obra='".$_POST['tipo_obra']."',descripcion_presupuesto='".$_POST['descripcion_presupuesto']."' where pcta_id='".$codigo."'";
 $resultado_set= pg_exec($conexion ,$sql);
}

if ($user_perfil_id==$_SESSION['perfil_de']){
$sql = "update sai_pcuenta set observacion1='".$_POST['obs1']."' where pcta_id='".$codigo."'";
$resultado_set= pg_exec($conexion ,$sql);
}
 if ($user_perfil_id==$_SESSION['perfil_p']){
$sql = "update sai_pcuenta set observacion2='".$_POST['obs2']."' where pcta_id='".$codigo."'";
$resultado_set= pg_exec($conexion ,$sql);
}

$sql="SELECT  pcta_id, pcta_asunto,rif_sugerido,pcta_descripcion,pcta_fecha,pcta_id_remit,pcta_id_dest,esta_id,usua_login,depe_id,pcta_observacion,pcta_justificacion,pcta_lapso,pcta_cond_pago,pcta_monto_solicitado,pcta_prioridad,numero_reserva,pcta_gerencia,pcta_presentado_por,recursos,pcta_garantia,
observacion1,observacion2,pcta_asociado FROM  sai_pcuenta WHERE pcta_id='".$codigo."'";
  $result=pg_query($conexion,$sql);

if($row=pg_fetch_array($result))
{
	$descripcion=$row["pcta_descripcion"]; 
	$fecha2=$row["pcta_fecha"];
    $fecha=cambia_esp($fecha2);
	$destino=$row["pcta_id_dest"];
	$elaborado=$row["usua_login"];
	$remitente=$row["pcta_id_remit"];
	$asunto_id=trim($row["pcta_asunto"]);
	$dependencia=trim($row['pcta_gerencia']);
	$prioridad=trim($row['pcta_prioridad']);
	$lapso =$row['pcta_lapso'];
	$cond_pago=$row['pcta_cond_pago'];
	$justificacion=trim($row['pcta_justificacion']);
	$observaciones=trim($row['pcta_observacion']);
    $monto_solicitado=$row['monto_solicitado'];
	$reserva=trim($row['numero_reserva']);
    $presentado=$row["pcta_presentado_por"];
    $rif_sugerido=$row['rif_sugerido'];
    $pcta_asociado=$row['pcta_asociado'];
    $recursos=$row['recursos'];
    $rif_sugerido=$row['rif_sugerido'];
    $garantia=$row['pcta_garantia'];
					
     $sql="SELECT * FROM  sai_seleccionar_campo('sai_pcta_asunt','pcas_nombre','pcas_id='||'''$asunto_id''','',2) resultado_set(pcas_nombre varchar)";
     $result=pg_query($conexion,$sql);

if($row=pg_fetch_array($result))  {
   $asunto=$row["pcas_nombre"];
 }

}

if (strpos($destino,"/"))  {$preparada_a = "Presidencia/".utf8_decode("Dirección Ejecutiva");}
else {
	
     $sql="SELECT * FROM  sai_seleccionar_campo('sai_empleado','carg_fundacion as cargo','esta_id=1 and empl_cedula='||'''$destino''','',2) resultado_set(cargo varchar)";
     $result=pg_query($conexion,$sql);
     	
	if($row=pg_fetch_array($result)) {
  		if ($row["usua_login"]==$_SESSION['presidente']) $preparada_a = "Presidencia";
  		else $preparada_a = utf8_decode("Dirección Ejecutiva");
	}
}
    $sql = "Select * from sai_buscar_usuario ('".$elaborado."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar Requisición"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_elaborado=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}

	 //Buscar las Imputaciones
	 $total_imputacion=0;
	 
	  $sql= " Select * from sai_buscar_pcta_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";

	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
 	    $monto_solicitado=0;
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['pcta_acc_pp']); 
				$matriz_acc_esp[$i]=trim($row['pcta_acc_esp']);
				$matriz_sub_esp[$i]=trim($row['pcta_sub_espe']);
				$matriz_uel[$i]=trim($row['depe_id']);
				$matriz_monto[$i]=trim($row['pcta_monto']);
				$matriz_monto_negativo[$i]=$row['pcta_monto']*-1;  
		 		$monto_solicitado=$monto_solicitado+$row['pcta_monto'];
				$i++;
			}
	        }
	
	$sql = "Select * from sai_buscar_usuario ('".$presentado."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar datos del usuario"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_presentado=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}

	$sql = "Select * from sai_buscar_usuario ('".$remitente."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar detos del usuario"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_remit=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}
	
    $anno_pres=$_SESSION['an_o_presupuesto'];	
    require_once("includes/arreglos_pg.php");
 	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); 
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); 
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_monto_negativo=convierte_arreglo($matriz_monto_negativo);
	$arreglo_uel=convierte_arreglo($matriz_uel); 
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); 
	
/*CUANDO SE RECHAZA EL PCTA*/
if ($request_id_opcion==$_SESSION['cuatro']) 
{ 
$query = "UPDATE sai_pcuenta set esta_id=7 WHERE LOWER(trim(pcta_id))='".$codigo."'";
$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar Punto de Cuenta"));

$query = "UPDATE sai_doc_genera set esta_id=7 WHERE LOWER(trim(docg_id))= '".$codigo."'";
$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar Punto de Cuenta"));

if ($recursos==1) { 
  $sqlt  = "select * from  sai_insert_pcta_traza('".trim($asunto_id)."', '"; 
  $sqlt .= $descripcion."' , '";
  $sqlt .= $_SESSION['login']."', '".$dependencia ."', '";
  $sqlt .= $destino."','".$justificacion."','".$lapso."', '";
  $sqlt .= $cond_pago."', '".$monto_solicitado."', '";
  $sqlt .= $prioridad ."','".$reserva."','400','".$recursos."','".$garantia."','";
  $sqlt .= $dependencia."','".$fecha."', '" .$codigo."','".$rif_sugerido."','";
  $sqlt .= $remitente."', '".$pcta_asociado."','".$presentado."','".$observaciones."') As resultado_set(varchar)";
  $resultado_set = pg_exec($conexion ,$sqlt) or die("Error al ingresar la traza del punto de cuenta");
 	
  $sql_imputa = "select * from sai_insert_pcta_imputa_traza('".trim($asunto_id)."','".$codigo."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto_negativo."','".$arreglo_uel."') as resultado_ser(varchar)";
  $resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias de la traza");
  
 }
}
		

/*CUANDO SE DEVUELVE EL PCTA*/
 if ($request_id_opcion==$_SESSION['cinco']) 
 { 
	$l=strlen($codigo);
	$ao=substr($codigo,$l-2,$l);
	
$sql="SELECT perf_id from sai_doc_genera where docg_id='".$codigo."'";
$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al obtener perfil"));
if ($resultado_set) {   
	$row = pg_fetch_array($resultado_set,0); 
	$perfil_elaborado=$row["perf_id"];
}

$sql="select * from sai_wfcadena where docu_id like 'pcta%' and wfob_id_ini=1 and wfgr_id in (select wfgr_id from sai_wfgrupo where wfgr_perf ='".$perfil_elaborado."')";
$resultado_set = pg_exec($conexion ,$sql);
if ($resultado_set) {   
	$row = pg_fetch_array($resultado_set,0); 
	$cadena=$row["wfca_id"];
}

    $request_id_opcion = $_REQUEST["opcion"];	
	$sql  = "select * from sai_devolver_pcta('".trim($_SESSION['login'])."','".trim($codigo)."','";
	$sql  .= trim($memo_contenido)."','";
	$sql  .= trim($_SESSION['user_depe_id'])."','".$cadena."','".$perfil_elaborado."') as memo_id";
	$resultado_set = pg_exec($conexion ,$sql);

 //Siempre que se haya reservado recursos y lo devuelvan despues que haya pasado por presupuesto, 
 //y no sea la cadena de aprobación de la Direcciòn Ejecutiva se guarda la traza del reverso del pcta 

    if (($recursos==1) && (($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO)  || ($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS)))
    {
      if (((substr($codigo,5,3) != '350') && ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO)) ||($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) || 
      	  ((substr($codigo,5,3) != '450') && ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS))){

     $sqlt  = "select * from  sai_insert_pcta_traza('".trim($asunto_id)."', '"; 
	  $sqlt .= $descripcion."' , '";
	  $sqlt .= $_SESSION['login']."', '".$dependencia ."', '";
	  $sqlt .= $destino."','".$justificacion."','".$lapso."', '";
	  $sqlt .= $cond_pago."', '".$monto_solicitado."', '";
	  $sqlt .= $prioridad ."','".$reserva."','400','".$recursos."','".$garantia."','";
	  $sqlt .= $dependencia."','".$fecha."', '" .$codigo."','".$rif_sugerido."','";
	  $sqlt .= $remitente."', '".$pcta_asociado."','".$presentado."','".$observaciones."') As resultado_set(varchar)";
	  $resultado_set = pg_exec($conexion ,$sqlt) or die("Error al ingresar la traza del punto de cuenta");
 	
 	$sql_imputa = "select * from sai_insert_pcta_imputa_traza('".trim($asunto_id)."','".$codigo."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto_negativo."','".$arreglo_uel."') as resultado_ser(varchar)";
 	$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias de la traza");
      }  
 }
}

/*CUANDO SE APRUEBA EL PCTA POR PRESIDENCIA, SE GUARDA LA DISPONIBILIDAD DEL PCTA*/
 if (   (($request_id_opcion==3) && ($_SESSION['user_perfil_id']==$_SESSION['perfil_p'])) || 
 (($request_id_opcion==3) && ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) && (substr($codigo,5,3) == '150') )    )
 {
 	//GUARDAR EN UNA TABLA LA DISPONIBILIDAD DEL PCTA
    for ($i=0; $i<$total_imputacion; $i++)
	{
	  if (($pcta_asociado=="0")||($pcta_asociado==""))
	   $pcta_asociado=$codigo;
	  $query = "INSERT into sai_disponibilidad_pcta (partida,monto,pcta_id,pcta_asociado) values ('".$matriz_sub_esp[$i]."','".$matriz_monto[$i]."','".$codigo."','".$pcta_asociado."')";
      $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al ingresar Disponibilidad Punto de Cuenta"));
	}
 }
 
 

 /*CUANDO SE APRUEBA EL PCTA POR PRESUPUESTO, SE GUARDA LA TRAZA DEL PCTA*/
  if (($request_id_opcion==6) && ($_SESSION['user_perfil_id']==$_SESSION['perfil_jp']))
 {
 	if ($recursos==1){
    $sqlt  = "select * from  sai_insert_pcta_traza('".trim($asunto_id)."', '"; 
    $sqlt .= $descripcion."' , '";
    $sqlt .= $_SESSION['login']."', '".$dependencia ."', '";
    $sqlt .= $destino."','".$justificacion."','".$lapso."', '";
    $sqlt .= $cond_pago."', '".$monto_solicitado."', '";
    $sqlt .= $prioridad ."','".$reserva."','400','".$recursos."','".$garantia."','";
    $sqlt .= $dependencia."','".$fecha."', '" .$codigo."','".$rif_sugerido."','";
    $sqlt .= $remitente."', '".$pcta_asociado."','".$presentado."','".$observaciones."') As resultado_set(varchar)";
    $resultado_set = pg_exec($conexion ,$sqlt) or die("Error al ingresar la traza del punto de cuenta");
  		
 		
 	$sql_imputa = "select * from sai_insert_pcta_imputa_traza('".trim($asunto_id)."','".$codigo."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','".$arreglo_uel."') as resultado_ser(varchar)";
 	$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias de la traza");
   }
    if (($asunto_id=='039') || ($asunto_id=='040')) {
     $query = "UPDATE sai_doc_genera set wfob_id_ini=99, wfca_id=0, esta_id=13,perf_id_act='' WHERE docg_id= '".$codigo."'";
     $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al finalizar el Punto de Cuenta"));
  
     //GUARDAR EN UNA TABLA LA DISPONIBILIDAD DEL PCTA
     for ($i=0; $i<$total_imputacion; $i++)
	 {
	  if (($pcta_asociado=="0")||($pcta_asociado==""))
	   $pcta_asociado=$codigo;
	  $query = "INSERT into sai_disponibilidad_pcta (partida,monto,pcta_id,pcta_asociado) values ('".$matriz_sub_esp[$i]."','".$matriz_monto[$i]."','".$codigo."','".$pcta_asociado."')";
      $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al ingresar Disponibilidad Punto de Cuenta"));
	 }
    }
 
 }
?>

<script language="JavaScript" src="js/funciones.js"> </script>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Revisi&oacute;n del Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>
<script language="javascript">

function revisar()
{
if (!(document.form1.opc_aprobar[0].checked || document.form1.opc_aprobar[1].checked))
	{
	   alert ("Debe Aprobar o Rechazar el Documento");
	   return
	}
    document.form1.submit()
  
}

</script>
<body>
<table width="485" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"><strong>PUNTO DE CUENTA </strong></span></td>
  </tr>
  <tr> 
	<td height="28" valign="midden" class="normal" > <strong>Punto de Cuenta:<?php echo $pcta_asociado;?></strong></td>
    <td class="normalNegro"> 	<?php echo $cod_doc;?></td>
  </tr>
  <tr>
    <td width="150"><div class="normal"><b>Fecha:</b></div></td>
    <td class="normalNegro"><?echo($fecha);?></div></td>
  </tr>
  <tr>
    <td width="150"><div  class="normal"><b>Elaborado por:</b></div></td>
    <td class="normalNegro"><?echo($nomape_elaborado);?></div></td>
  </tr>
  <tr>
	<td height="32" ><div class="normal"><b>Solicitado por:</b></div></td>
	<td height="32" align="left"><div align="left" class="normalNegro"><? echo($nomape_remit);?></div></td>
  </tr>
  <tr>
	<td height="15"><div class="normal"><b>Presentado por:</b></div></td>
	<td height="15" align="left" class="normalNegro"><? echo($nomape_presentado);?></div></td>
  </tr>
  <tr>
  	<td><div  class="normal" height="20"><strong>Gerencia:</strong></div></td>
	<?php
	  $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$dependencia''','',2) resultado_set(depe_nombre varchar)";
	  $res_q=pg_exec($sql_str);		  
	?>
	<td><span class="normalNegro"><?if ($depe_row=pg_fetch_array($res_q)) { 
	  echo $depe_row['depe_nombre'];}?></span></td></tr>
  <tr class="normal"> 
    <td valign="midden" height="25"><strong>Prioridad:</strong></td>
    <td valign="midden"> 
      <select name="slc_prioridad" class ="normalNegro" disabled>
        <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
         <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
         <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
       </select>
    </td>
  </tr>
  <tr>
	<td height="15"><div align="left" class="normal"><strong>Asunto:</strong></div></td>
	<td><span class="normalNegro">
	<?
		echo($asunto);
		if (($pcta_asociado<>"") && ($pcta_asociado<>"0")){
			echo " Asociado al "."".$pcta_asociado;
		}
	?></span></td>
  </tr>
  <tr>
	<td height="25"><div align="left" class="normal"><strong>Descripci&oacute;n:</strong></div>		</td>
	<td><span class="normalNegro"><?echo($descripcion);?></span></td>  	
  </tr>
  <tr>
    <td><div class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
	<td><span class="normalNegro"><?echo $justificacion?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Lapso de Convenio/Contrato:</strong></div></td>
	<td><span class="normalNegro"><?echo $lapso?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Garant&iacute;a:</strong></div></td>
	<td><span class="normalNegro"><?echo $garantia?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></td>
	<td><span class="normalNegro"><?echo $rif_sugerido?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Condiciones de Pago:</strong></div></td>
	<td><span class="normalNegro"><?echo $cond_pago?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Monto Solicitado:</strong></div></td>
	<td><span class="normalNegro"><?echo $monto_solicitado?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Observaciones:</strong></div></td>
	<td><span class="normalNegro"><?echo $observaciones?></span></td></tr>
	<br>
  <tr>
    <td colspan="3"><br>
	 <table width="60%" border="0"  class="tablaalertas" align="center">
       <tr class="td_gray">
         <td  align="center" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria </td>
       </tr>
	   <tr>
          <td class="normal" align="center">
            <table width="100%" border="0">
              <tr>
                 <td  class="peqNegrita" align="left"><div align="center">ACC.C/PP</div></td>
                 <td  class="peqNegrita" align="left"><div align="center">Acci&oacute;n espec&iacute;fica </div></td>
                 <td width="10%" align="left"  class="peqNegrita"><div align="center">Dependencia</div></td>
                 <td width="15%" align="left"  class="peqNegrita"><div align="center">Partida</div></td>
                 <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto </div></td>
              </tr>
              <tr>
              <?php
				for ($ii=0; $ii<$total_imputacion; $ii++)
				{
				?>
                  <td  class="peq" align="left" width="17%"><div align="center"><input name="<?php echo "txt_imputa_proyecto_accion".$ii;?>" type="text" class="normalNegro" id="<?php echo "txt_imputa_proyecto_accion".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="19%"><div align="center"><input name="<?php echo "txt_imputa_accion_esp".$ii;?>"  type="texto" class="normalNegro" id="<?php echo "ctxt_imputa_accion_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="10%"><div align="center"><input name="<?php echo "txt_imputa_unidad".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_unidad".$ii;?>" size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
                  <td  class="peq" align="left" width="15%"><div align="center"><input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15" value="<?php echo $matriz_sub_esp[$ii];?>" /></div></td>
                  <td class="peq" align="right" width="39%"><input name="<?php echo "txt_imputa_monto".$ii;?>" type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" /></td>
              </tr>
             <?php 
		 }
		 ?>
          </table></td>
   </tr></table><br>
<table>

<? if ($user_perfil_id==$_SESSION['perfil_de']){?>
 <tr>
   <td><div align="right" class="normal"><strong>Observaciones 1:</strong></div></td>
   <td><span class="normal">&nbsp;<textarea cols="60" rows="5" name="obs1" readonly="true"><?echo $_POST['obs1']?></textarea></td></tr>	
<?}?>	
<? if ($user_perfil_id==$_SESSION['perfil_p']){?>
 <tr>
   <td ><div align="right" class="normal"><strong>Observaciones 1:</strong></div></td>
   <td ><span class="normal"><textarea cols="60" rows="5" readonly="true" name="obs1"><?echo $_POST['obs1']?></textarea></td></tr>	
 <tr>
   <td><div align="right" class="normal"><strong>Observaciones 2:</strong></div></td>
   <td><span class="normal"><textarea cols="60" rows="5" name="obs2" readonly="true"><?echo $_POST['obs2']?></textarea></td></tr>	
<?}?>
</table>
	
<?
require_once('includes/respaldos_mostrar.php');
?>
<tr> 
  <td height="18">		</td>
  <td>&nbsp;		</td>
</tr>
<div align="center">
<a href="documentos.php?tipo=pcta">Regresar</a>
</div>	
</table>
</body>
</html>
