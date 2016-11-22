<?php
  ob_start(); 
  require_once("includes/conexion.php");
  require("includes/fechas.php");
  	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
  }

$cod_doc = $request_codigo_documento;
$codigo = $cod_doc;

$sql="SELECT pcta_id, pcta_asunto,rif_sugerido,pcta_descripcion,pcta_fecha,pcta_id_remit,pcta_id_dest,esta_id,usua_login,depe_id,pcta_observacion,pcta_justificacion,pcta_lapso,pcta_cond_pago,pcta_monto_solicitado,pcta_prioridad,numero_reserva,pcta_gerencia,pcta_presentado_por,recursos,pcta_garantia,
observacion1,observacion2,pcta_asociado,descripcion_presupuesto FROM  sai_pcuenta WHERE pcta_id='".$codigo."'";
$result=pg_query($conexion,$sql);

if($row=pg_fetch_array($result))
{
	$descripcion=$row["pcta_descripcion"]; 
	$fecha2=$row["pcta_fecha"];
	$fecha=cambia_esp($fecha2);
	$destino=$row["pcta_id_dest"];
	$remitente=$row["pcta_id_remit"];
	$presentado=$row["pcta_presentado_por"];
	$elaborado=$row["usua_login"];
	$asunto_id=trim($row["pcta_asunto"]);
	$dependencia=trim($row['pcta_gerencia']);
	$prioridad=trim($row['pcta_prioridad']);
	$lapso =$row['pcta_lapso'];
	$cond_pago=$row['pcta_cond_pago'];
	$justificacion=trim($row['pcta_justificacion']);
	$observaciones=trim($row['pcta_observacion']);
	$reserva=trim($row['numero_reserva']);
	$garantia=$row["pcta_garantia"];
	$obs1=$row['observacion1'];
	$rif_sugerido=$row['rif_sugerido'];
	$pcta_asociado=$row['pcta_asociado'];
	$descripcion_presupuesto=$row['descripcion_presupuesto'];
	
	if (strpos($destino,"/")){
	  
	  $preparada_a=utf8_decode("Presidencia/Dirección Ejecutiva");	


	}else{

		 $sql="SELECT * FROM  sai_seleccionar_campo('sai_empleado','carg_fundacion as cargo','esta_id=1 and empl_cedula='||'''$destino''','',2) resultado_set(cargo varchar)";
 		 $result=pg_query($conexion,$sql);
					
		if($row=pg_fetch_array($result))
		{
  		 if ($row["usua_login"]==$_SESSION['presidente'])
  		 {
   		  $preparada_a = "Presidencia";
  		  }else{$preparada_a = utf8_decode("Dirección Ejecutiva");}
		 }
	}

		 $sql="SELECT * FROM  sai_seleccionar_campo('sai_pcta_asunt','pcas_nombre','pcas_id='||'''$asunto_id''','',2) resultado_set(pcas_nombre varchar)";
 		 $result=pg_query($conexion,$sql);
		
 if($row=pg_fetch_array($result))
 {
   $asunto=$row["pcas_nombre"];
	}
}


	$sql = "Select * from sai_buscar_usuario ('".$remitente."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar Requisición"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_remit=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}
	$sql = "Select * from sai_buscar_usuario ('".$presentado."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar Requisición"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_presentado=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
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
	 
  $sql_imp= " Select * from sai_buscar_pcta_imputacion('".trim($codigo)."') as result ";
  $sql_imp.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";
  $resultado_set= pg_exec($conexion ,$sql_imp);
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
	 		$monto_solicitado=$monto_solicitado+$row['pcta_monto'];
			$i++;
		}
        }
?>
	
<script LANGUAGE="JavaScript" SRC="includes/js/funciones.js"> </SCRIPT>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Revisi&oacute;n del Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="JavaScript" src="js/lib/actb.js"></script>
<script language="javascript">
function validarInfo(rif){
	var encuentra=0;
	for(j= 0; j < arreglo_id_info.length; j++){
		if(rif==arreglo_id_info[j]){
			return true;
			encuentra=1;
		}
	}
	/*if (encuentra==0){
	//return false;
	alert("Este RIF indicado no es v"+aACUTE+"lido");
	document.form1.rif_sugerido.focus();
			
	}*/
}

function revisar_doc(id_documento,id_tipo_documento,id_opcion,objeto_siguiente_id,cadena_siguiente_id,id_objeto_actual,nombre_opcion)
{ 

	if (confirm(" Est\u00E1 seguro que desea "+nombre_opcion+" ? ")) {
		
		//si la opcion es firmar, verificar que este la firma 
		/*if (id_opcion==3) {
		
			var firmaTextField = document.getElementById('firma');
			confirmado=0;
			confirmado=confirmar_firma(firmaTextField);			
			if (confirmado==0) {			
				return;			
			}
		
		}*/
		
		document.getElementById('form1').action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion+"&id="+id_documento;

		if (id_opcion != 5)
		{
		  document.form1.submit();	
		}
		else
		{

	/*	contenido=prompt("Indique el motivo de la devoluci\u00F3n: ","");
		document.getElementById('contenido_memo').value=contenido;*/
		contenido=prompt("Indique el motivo de la devoluci\u00F3n: ","");
  		 while (contenido==null){
    		  contenido=prompt("Debe especificar el motivo de la devoluci\u00F3n: ","");
    		 }
    		 if (contenido!=null){
     		   document.getElementById('contenido_memo').value=contenido;
     		   document.form1.submit();
    		}
		}
		
	}

}

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
<form action="" method="post" name="form1" id="form1">
<table width="600" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="titularMedio"><strong>PUNTO DE CUENTA </strong></span>		</td>
  </tr>
  <tr> 
	<td height="28" colspan="2" class="normal" align="center"><strong>Punto de Cuenta: <font size="2">&nbsp;&nbsp;<?php echo $cod_doc; ?></font></strong></td>
  </tr>
  <tr>
    <td width="150"><div class="normal"><b>Fecha:</b></div></td>
    <td class="normalNegro"><?echo($fecha);?></div></td>
  </tr>
  <tr>
    <td width="150"><div class="normal"><b>Elaborado por:</b></div></td>
    <td class="normalNegro"><?echo($nomape_elaborado);?></div></td>
  </tr>
  <tr>
	<td height="32" align="left"><div  class="normal"><b>Solicitado por:</b></div></td>
	<td height="32" align="left"><div align="left" class="normalNegro"><? echo($nomape_remit);?></div></td>
  </tr>
  <tr>
	<td height="15" ><div class="normal"><b>Presentado por:</b></div></td>
	<td height="15" align="left" class="normalNegro"><? echo($nomape_presentado);?></div></td>
  </tr>
  <tr>
    <td><div class="normal" height="20"><strong>Gerencia:</strong></div></td>
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
       </select></td>
  </tr>
  <tr>
	<td height="15"><div align="left" class="normal"><strong>Asunto:</strong></div></td>
	<td><span class="normalNegro">
	<?
	  echo($asunto);
	
	  if (($pcta_asociado<>"") && ($pcta_asociado<>"0")){
		echo " asociado al "."".$pcta_asociado;
	  }?></span></td>
  </tr>
  <tr>
	<td height="25" class="Estilo1"><div class="normal" ><strong>Descripci&oacute;n:</strong></div></td>
	<td><span class="normalNegro"><?php echo($descripcion);?></span></td>  	
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
	<td><span class="normalNegro"><?echo(number_format($monto_solicitado,2,',','.')); ?></span></td></tr>
  <tr>
    <td><div class="normal"><strong>Observaciones:</strong></div></td>
	<td><span class="normalNegro"><?echo $observaciones?></span></td></tr>
    <?php     if ($user_perfil_id==$_SESSION['perfil_jp']){?>
     <tr>
	   	<td><div align="left" class="normal"><strong> Tipo Obra: </strong></div></td>
	    <td><span class="normal">
	      <select name="tipo_obra" class ="normalNegro">
            <option value="N/A">N/A</option>
            <option value="Obra Civil">Obra Civil</option>
            <option value="Obra Extra">Obra Extra</option>
          </select></span></td>
	  </tr>
 		<tr>
	    <td><div align="left" class="normal"><strong>Estado/Infocentro:</strong></div></td>
		<td><input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70" onChange="validarInfo(this)" value="<?= $idInfo?>">
       <?php 	
			
			$query = "SELECT nemotecnico,nombre FROM safi_infocentro ORDER BY nemotecnico,nombre";
			
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arregloProveedores = "";
			$cedulasProveedores = "";
			$nombresProveedores = "";
			$indice=0;
			while($row=pg_fetch_array($resultado)){
				$arregloProveedores .= "'".$row["nemotecnico"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
				$cedulasProveedores .= "'".$row["nemotecnico"]."',";
				$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
				$indice++;
			}
			$arregloProveedores = substr($arregloProveedores, 0, -1);
			$cedulasProveedores = substr($cedulasProveedores, 0, -1);
			$nombresProveedores = substr($nombresProveedores, 0, -1);
			?> <script>
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					this.actb_delimiter = new Array(' ',',');
					obj = new actb(document.getElementById('infocentro'),proveedor);
				</script>
		</td></tr>
		<tr>
   <td><div align="right" class="normal"><strong>Descripci&oacute;n:</strong></div></td>
   <td><span class="normal">&nbsp;<textarea cols="60" rows="5" name="descripcion_presupuesto"><?php echo $descripcion_presupuesto;?></textarea></td></tr>
<?
}?> 
  <tr>
	<td colspan="3"><br>
	  <table width="60%" border="0"  class="tablaalertas" align="center">
        <tr class="td_gray">
          <td  align="center" class="peqNegrita">Imputaci&oacute;n presupuestaria </td>
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
               <td  class="peq" align="left" width="17%"><div align="center"><input name=<?php echo "txt_imputa_proyecto_accion".$ii;?> type="text" class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
               <td  class="peq" align="left" width="19%"><div align="center"><input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="texto" class="normalNegro" id=<?php echo "ctxt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
               <td  class="peq" align="left" width="10%"><div align="center"><input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
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
   <td><span class="normal">&nbsp;<textarea cols="60" rows="5" name="obs1"></textarea></td></tr>	
<?}?>	
<? if ($user_perfil_id==$_SESSION['perfil_p']){?>
 <tr>
   <td><div align="right" class="normal"><strong>Observaciones 1:</strong></div></td>
   <td><span class="normal"><textarea cols="60" rows="5" readonly="true" name="obs1"><?echo $obs1;?></textarea></td></tr>	
 <tr>
   <td><div align="right" class="normal"><strong>Observaciones 2:</strong></div></td>
   <td><span class="normal"><textarea cols="60" rows="5" name="obs2"></textarea></td></tr>	
<?}?>
</table>
<?
require_once('includes/respaldos_mostrar.php');
?>
<tr> 
  <td height="18">		</td>
  <td><input name="codigo" type="hidden" id="codigo" value="<? echo($codigo); ?>">	
	  <input type="hidden" name="contenido_memo" id="contenido_memo">	</td>
</tr>
<tr>
   <td height="18" colspan="3">
    <?
  	  include("documentos/opciones_3y4.php");
	?> 
</tr>
</table>
</form>
</body>
</html>
