<?php
  ob_start();
  require_once("includes/conexion.php");
	  
   if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
   {
	 header('Location:../index.php',false);
	 ob_end_flush(); 
	 exit;
   }

  $sql="SELECT * FROM  sai_seleccionar_campo('sai_pcuenta','pcta_descripcion as pcta_relacion,pcta_fecha as pcta_fecha,pcta_id_dest as pcta_id_dest,pcta_id_remit as pcta_id_remit,pcta_asunto as pcta_asunto','pcta_id='||'''$codigo''','',2) resultado_set
  (pcta_relacion text,pcta_fecha date,pcta_id_dest varchar,pcta_id_remit varchar,pcta_asunto varchar)";
  $result=pg_query($conexion,$sql);	     
	     
  if($row=pg_fetch_array($result))
  {
	$relacion=$row["pcta_relacion"]; 
	$fecha=$row["pcta_fecha"];
	$destino=$row["pcta_id_dest"];
	$remitente=$row["pcta_id_remit"];
	$asunto=$row["pcta_asunto"];
	$asunto_id=$row["pcas_id"];
	$asunto_id=trim($row["pcta_asunto"]);
   }

	$sql_asu="SELECT * FROM  sai_seleccionar_campo('sai_pcta_asunt','pcas_nombre','pcas_id='||'''$asunto_id''','',2) resultado_set(pcas_nombre varchar)";
	$result=pg_query($conexion,$sql_asu);

	if($row=pg_fetch_array($result)){
	  $asunto_id_inicial=$row['pcas_nombre'];
	  $asunto_nomb_inicial=$row['pcas_nombre'];
	}


	if (strpos($destino,"/")){
	 $nomape_destino=utf8_decode("Presidencia/Dirección Ejecutiva");	
	}else{
	
		$sql="SELECT * FROM  sai_seleccionar_campo('sai_empleado','carg_fundacion as cargo','esta_id=1 and empl_cedula='||'''$destino''','',2) resultado_set(cargo varchar)";
		$result=pg_query($conexion,$sql);

		if($row=pg_fetch_array($result))
		{
  		 if ($row["usua_login"]==$_SESSION['presidente'])
  		 {
   		  $nomape_destino = "Presidencia";
  		  }else{$nomape_destino = utf8_decode("Dirección Ejecutiva");}
		 }
	}

    $sql = "Select * from sai_buscar_usuario  ('".$remitente."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar Requisición"));
	
	if ($resultado_set)
	{   
		$row_user = pg_fetch_array($resultado_set,0); 
		$nomape_remit=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}
?>

<script language="JavaScript" src="js/funciones.js"> </script>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Anulacion del Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript">

function revisar()
{
	if (trim(document.form1.txt_memo.value)=="")
	{
	   alert ("Debe colocar el motivo de la anulaci\u00F3n");
	   document.form1.txt_memo.select();
	   document.form1.txt_memo.focus();
	   return
	}
    document.form1.submit()
  
}
</script>
<body>
<form action="anular_edocumento.php?tipo=<? echo $request_id_tipo_documento; ?>&id=<?php echo trim($request_codigo_documento); ?>" method="post" name="form1" id="form1">
<table width="420" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita" align="center">	PUNTO DE CUENTA(Anulaci&oacute;n) </td>
  </tr>
  <tr> 
	<td height="28" valign="midden" class="normal" align="left"> <strong>Punto de Cuenta : </strong></td>
	<td class="normalNegro"><?php echo $codigo; ?></td>
  </tr>
  <tr>
	<td width="50" rowspan="2"><div align="left" class="normal"><strong> Preparada:</strong></div>		</td>
    <td width="200"><div align="left" class="normalNegro"> A:. 
	  <? echo($nomape_destino);?>	
	  <input type="hidden" value="<?=$nomape_destino?>" name="destino">	</div></td></tr>
  <tr>
	<td align="left"><div align="left" class="normalNegro">	Por:. 
	  <? echo($nomape_remit); ?></div>    </td>
  </tr> 
  <tr>
	<td><div align="left" class="normal"><strong> Asunto:</strong></div></td>
	<td><div align="left" class="normalNegro"><? echo($asunto_nomb_inicial); ?></div></td>
  </tr>
  <tr>
	<td><div align="left" class="normal"><strong>  Descripci&oacute;n:</strong></div></td>
	<td class="normalNegro"><? echo($relacion); ?> </td>
  </tr>
  <tr class="normal"> 
	<td height="28" valign="midden"> <div align="left"><b>Motivo: </b></div></td>
    <td valign="midden">  <span class="Estilo1"><strong>
	  <textarea name="txt_memo"   cols="50" rows="8" class="normalNegro"></textarea>
	  <input name="codigo" type="hidden" id="codigo" value="<? echo($codigo); ?>"></strong></span></td>
  </tr>
  <?
	$cod_doc=$codigo;
	include("includes/respaldos_mostrar.php");
  ?>
  <tr> 
   	<td height="18"></td>
    <td></td>
  </tr>
  <tr> 
	<td height="18" colspan="3">
	  <div align="center"><input type="button" value="Anular" onclick="javascript: revisar()"></div></td>
  </tr>
</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>