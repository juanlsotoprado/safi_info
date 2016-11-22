<?php
  ob_start();
  include("includes/fechas.php");
  require_once("includes/conexion.php");
	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../index.php',false);
   ob_end_flush(); 
   exit;
  }
		 
  $sql="SELECT  pcta_id, pcta_asunto,rif_sugerido,pcta_descripcion,pcta_fecha,pcta_id_remit,pcta_id_dest,esta_id,usua_login,depe_id,pcta_observacion,pcta_justificacion,pcta_lapso,pcta_cond_pago,pcta_monto_solicitado,pcta_prioridad,numero_reserva,pcta_gerencia,pcta_presentado_por,recursos,pcta_garantia,
observacion1,observacion2,pcta_asociado FROM  sai_pcuenta WHERE pcta_id='".$codigo."'";
  $result=pg_query($conexion,$sql);
  
  if($row=pg_fetch_array($result))
  {
	$relacion=$row["pcta_descripcion"]; 
	$fecha=$row["pcta_fecha"];
	$destino=$row["pcta_id_dest"];
	$remitente=$row["pcta_id_remit"];
	$asunto=$row["pcta_asunto"];
	$gerencia=$row["pcta_gerencia"];
	$observaciones=trim($row['pcta_observacion']);
	$justificacion=trim($row['pcta_justificacion']);
	$lapso=$row['pcta_lapso'];
	$cond_pago=$row['pcta_cond_pago'];
	$monto_solicitado=$row['monto_solicitado'];
	$prioridad=trim($row['pcta_prioridad']);
	$reserva=trim($row['numero_reserva']);
	$recursos=$row['recursos'];
	$garantia=$row['pcta_garantia'];
	$rif_sugerido=$row['rif_sugerido'];
	$descripcion=$row["pcta_descripcion"]; 
	$fecha2=$row["pcta_fecha"];
    $fecha=cambia_esp($fecha2);
	$destino=$row["pcta_id_dest"];
	$elaborado=$row["usua_login"];
	$dependencia=trim($row['pcta_gerencia']);
    $presentado=$row["pcta_presentado_por"];
    $rif_sugerido=$row['rif_sugerido'];
    $pcta_asociado=$row['pcta_asociado'];
   }

    $sql = "Select * from sai_buscar_usuario ('".$remitente."','') resultado_set(email varchar, login varchar, activo bool,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al visualizar Requisición"));
	
	if ($resultado_set)
	{   
	 $row_user = pg_fetch_array($resultado_set,0); 
	 $nomape_remit=trim($row_user['nombres'])." ".trim($row_user['apellidos']);
	}

	$sql_asu="SELECT * FROM  sai_seleccionar_campo('sai_pcta_asunt','pcas_id,pcas_nombre','pcas_id='||'''$asunto''','',2) resultado_set(pcas_id varchar,pcas_nombre varchar)";	
	$result=pg_query($conexion,$sql_asu);

	if($row=pg_fetch_array($result)){
	 $asunto_id_inicial=$row['pcas_nombre'];
	 $asunto_nomb_inicial=$row['pcas_nombre'];
	}
?>

<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Anulacion del Punto de Cuenta Numero <? echo($codigo); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
  $motivo=$_POST['txt_memo'];
  $fecha_actual=time ();
  $fa=date("Y-m-d",$fecha_actual);
  $fc=cambia_esp($fa);
  $anno_pres=$_SESSION['an_o_presupuesto'];
  $l=strlen($codigo);
  $ao=substr($codigo,$l-2,$l);
	
   //Buscar las Imputaciones
   $total_imputacion=0;
   $sql= " Select * from sai_buscar_pcta_imputacion('".trim($codigo)."') as result ";
   $sql.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";
   $resultado_set= pg_exec($conexion ,$sql);
   $valido=$resultado_set;
   if ($resultado_set)
   {
	$monto_solicitado=0;
	while($row=pg_fetch_array($resultado_set))	
	{  
	 $monto_solicitado=$monto_solicitado+$row['pcta_monto'];
	}
   }

$sql  = "select * from sai_anular_pcuenta('" .  $_SESSION['login'] . "', '". $codigo. "' , '";
$sql .= $motivo . "','" . $_SESSION['user_depe_id'] . " ' ) as memo_id";
$resultado_set = pg_exec($conexion ,$sql) or die("Error al Anular el Punto de Cuenta");
$row = pg_fetch_array($resultado_set,0); 
if ($row[0] <> null)
{
	$memo_id=$row[0];
}
?>
<table width="420" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="normalNegroNegrita">PUNTO DE CUENTA(Anulaci&oacute;n) </td>
  </tr>
  <tr> 
	<td height="28" colspan="2" valign="midden" class="normalNegrita" align="left"> Punto de Cuenta: 
			<?php echo $codigo; ?></td>
  </tr>
  <tr>
	<td width="50" rowspan="2"><div align="left" class="normalNegrita">Preparada: </div>		</td>
    <td width="200"><div align="left" class="normalNegro"> A:. <? echo($_POST['destino']); ?></div></td>
  </tr>
  <tr>
	<td align="left"><div align="left" class="normalNegro">Por:. <? echo($nomape_remit); ?></div></td>
  </tr>
  <tr>
	<td ><div align="left" class="normalNegrita">Asunto:</td>
	<td class="normalNegro"><? echo($asunto_nomb_inicial);?></div>    </td> 
  </tr>
  <tr>
	<td colspan="2"><div align="left" class="normalNegrita">Descripci&oacute;n:</div></td>
  </tr>
  <tr>
	<td colspan="2" class="normalNegro"><? echo($relacion);?> </td>
  </tr>
  <tr class="normal"> 
	<td height="28" valign="midden"  class="normalNegrita"> Motivo: </td>
    <td valign="midden">  
	  <textarea name="txt_memo"   cols="50" rows="8" class="normalNegro" readonly><? echo($motivo); ?></textarea>
			<input name="codigo" type="hidden" id="codigo" value="<? echo($codigo); ?>">
		  </td>
  </tr>
  <?
	$cod_doc=$codigo;
	include("includes/respaldos_mostrar.php");
  ?>
  <tr> 
   	<td height="18"></td>
    <td></td>
  </tr>
</table>
</body>
</html>
