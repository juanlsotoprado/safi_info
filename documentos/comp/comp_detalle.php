<?
ob_start();
	 require_once("../../includes/conexion.php");
     require_once("../../includes/fechas.php");	  
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
?>
<?
$codigo=$_REQUEST['codigo']; 
$longitud_comp=strlen($codigo);
//Consulta comp de cualquier año
$anno_pres="20".substr($codigo,$longitud_comp-2);//$_SESSION['an_o_presupuesto'];
$query="select t1.esta_id,t1.comp_estatus, t1.comp_prioridad, t1.comp_id, t1.pcta_id, 
t3.carg_nombre, t7.empl_nombres as nbelabora, t4.depe_nombre as dependencia,EXTRACT(DAY FROM t1.fecha_reporte)||'/'||EXTRACT(month FROM t1.fecha_reporte)||'/'||EXTRACT(Year FROM t1.fecha_reporte) as fecha_reporte,
t7.empl_apellidos as apelabora, EXTRACT(DAY FROM t1.comp_fecha)||'/'||EXTRACT(month FROM t1.comp_fecha)||'/'||EXTRACT(Year FROM t1.comp_fecha) as comp_fecha,t1.comp_observacion, t1.comp_descripcion, t1.comp_justificacion, t1.comp_monto_solicitado, t1.numero_reserva, t1.comp_lapso, t1.comp_cond_pago , t6.cpas_nombre as asunto,
t1.rif_sugerido,t1.usua_login,substr(t6.cpas_nombre,0,8) as asunto_pcta,comp_documento,id_actividad,localidad,beneficiario,id_evento,fecha_inicio,fecha_fin
from sai_comp t1, sai_cargo t3, sai_dependenci t4, sai_compromiso_asunt t6, sai_empleado t7
where t1.comp_id=trim('".$codigo."') and trim(t1.comp_asunto)=trim(t6.cpas_id) and t1.comp_gerencia=t4.depe_id and t1.usua_login=t7.empl_cedula ";

$result=pg_query($conexion,$query);
if($row=pg_fetch_array($result)) {
$elabora=$row["nbelabora"]." ".$row["apelabora"];
$depesolicitante=$row["depesolicitante"];
$fecha=$row["comp_fecha"];
$observacion=$row["comp_observacion"];
$descripcion=$row["comp_descripcion"];
$justificacion=$row["comp_justificacion"];
$lapso=$row['comp_lapso'];
$condicion=$row['comp_cond_pago'];
$monto=$row["comp_monto_solicitado"];
$pcta_id=$row["pcta_id"];
$estatus=$row["comp_estatus"];
$prioridad=$row["comp_prioridad"];
$asunto=$row["asunto"];
$pcta_asunto=$row["asunto_pcta"];
$subespecifica=$row["comp_sub_espe"];
$centrogestorac=$row["centrogestorac"];
$centrocostoac=$row["centrocostoac"];
$centrogestorpr=$row["centrogestorpr"];
$centrocostopr=$row["centrocostopr"];
$numeroreserva=$row["numero_reserva"];
$dependencia=$row["dependencia"];
$usua_login=$row['usua_login'];
$documento=$row['comp_documento'];
$actividad=$row['id_actividad'];
$ubicacion=$row['localidad'];
$id_evento=$row['id_evento'];
$fecha_i=$row['fecha_inicio'];
$fecha_f=$row['fecha_fin'];

$fecha_reporte=$row["fecha_reporte"];
	if ($pcta_id=="0"){
		$pcta_id="N/A";
	}

$sql_memo="SELECT memo_contenido FROM sai_docu_sopor,sai_memo WHERE doso_doc_fuente='".$codigo."' and
doso_doc_soport=memo_id";
$resultado_memo=pg_query($conexion,$sql_memo);
if ($row_memo=pg_fetch_array($resultado_memo)){
 $contenido_memo=$row_memo['memo_contenido'];
}


include("../../includes/monto_a_letra.php");

$montoletrasbase=monto_letra($monto_base, " BOLIVARES");
$montoletrasiva=monto_letra($monto_iva, " BOLIVARES");
$centralespnombre=$row["centalespnombre"];
$centralprinombre=$row["centralprinombre"];
$proyectoespnombre=$row["proyectoespnombre"];
$proyectoprinombre=$row["proyectoprinombre"];
$rif_sugerido=$row["rif_sugerido"];
$nombre_sugerido=$row["beneficiario"];
$edo=$row['esta_id'];
 if ($edo==15){
  $anulado=1;
 }	
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/><style type="text/css">
</style><script language="javascript">

function imprimir() {
	document.getElementById('noimprimir').style.visibility='hidden';
	window.print();
}

function generarPdf(){
	location.href = "comp_pdf.php?codigo=<?= $codigo?>";
}

</script>
</head>
<body>
<form name="form" method="post" action="">
<table align="center" width="400" bgcolor="#ffffff" border="0" bordercolor="#0099cc" cellpadding="0" cellspacing="0" background="../../imagenes/fondo_tabla.gif" class="tablaalertas"><br></br>
  <tr>
	<td colspan="4"></td>
  </tr>
  <tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">COMPROMISO <?php echo $codigo?>	</td>
  </tr>
	<?if ($anulado==1) {?>
    <tr>
    <td  colspan="5"><div align="center">
	  <font color="Red"><STRONG>ANULADO</STRONG>
	   </div></td>
    </tr>
    <?}?>
  <tr>
	<td class="normalNegrita">Fecha:</td>
	<td class="normalNegro" colspan="3"><?php echo $fecha?></td>
  </tr>
  <tr>
	<td class="normalNegrita"><?php if ($usua_login==$_SESSION['usua_presi']){?>Aprobado por: <?php }else{?>Elaborado por: <?php }?></td>
	<td class="normalNegro" colspan="3"><?php echo $elabora; ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Unidad/Dependencia: </td>
	<td class="normalNegro" colspan="3"><?php echo $dependencia; ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Punto de cuenta: </td>
	<td class="normalNegro" colspan="3"><?php echo $pcta_id; ?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Estatus: </td>
	<td class="normalNegro" colspan="3"><b><?php echo $estatus; ?></td>
  </tr>
<?if ($anulado==1) {?>
  <tr>
	<td class="normalNegrita">Motivo Anulaci&oacute;n: </b>
	<td class="normalNegro" colspan="3"><b><?php echo $contenido_memo; ?></b></td>
  </tr>
<?php }?>
  <tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">Elementos del compromiso&nbsp;</td>
  </tr>
  <tr> 
	<td class="normalNegrita">Asunto: </td>
	<td class="normalNegro" colspan="3"><?php echo $asunto; ?></td>
  </tr>
  <tr> 
	<td class="normalNegrita">Rif del Proveedor Sugerido: </td>
	<td class="normalNegro" colspan="3"><?php echo $rif_sugerido." : ".$nombre_sugerido; ?></td>
  </tr>
  <tr>
  	<td class="normalNegrita">Tipo Actividad:</td>
	<td class="normalNegro" colspan="3">
	<?php 
	$sql_asu = "SELECT * FROM sai_tipo_actividad where id='".$actividad."'";					
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	}  		
	?></td>
  </tr>
  <tr>
  	<td class="normalNegrita">Tipo Evento:</td>
	<td class="normalNegro" colspan="3">
	<?php 
	$sql_asu = "SELECT * FROM sai_tipo_evento where id='".$id_evento."'";					
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	}  		
	?></td>
  </tr>
  <tr>
  	<td class="normalNegrita">Duracci&oacute;n de la Actividad:</td>
	<td class="normalNegro" colspan="3"><?php echo cambia_esp($fecha_i)." - ".cambia_esp($fecha_f);	?></td>
  </tr>
  <tr>
	<td class="normalNegrita">C&oacute;digo Documento:</td>
	<td class="normalNegro" colspan="3"><?php echo $documento;?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Descripci&oacute;n:</td>
	<td class="normalNegro" colspan="3"><?php echo $descripcion;?></td>
  </tr>
  <?php if (strlen($fecha_reporte)>1){?>
  <tr>
  	<td class="normalNegrita">Fecha de Reporte:</td>
	<td class="normalNegro" colspan="3">	<?php echo $fecha_reporte;?></td>
  </tr>
  <?php } if (($ubicacion<>'') && ($ubicacion<>'0')){?>
  <tr>
    <td class="normalNegrita">Ubicaci&oacute;n Infocentro:</td>
	<td class="normalNegro" colspan="3">
	<?php 

	$sql_asu = "SELECT * FROM safi_edos_venezuela where id='".$ubicacion."'";	
	$result=pg_query($conexion,$sql_asu);
	if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	} ?></td>
  </tr><?php }?>
  <tr>
	<td class="normalNegrita">Observaci&oacute;n: </td>
	<td class="normalNegro" colspan="3"><?php echo $observacion; ?></td>
  </tr>
	<?php 
	  $sql= " Select * from sai_seleccionar_campo('sai_comp_imputa','comp_monto','comp_id='||'''$codigo''','',2) as resultado_set ";
	  $sql.= " (comp_monto float)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
	
		if ($resultado_set)
  		{
		$monto_compromiso=0;
		while($row=pg_fetch_array($resultado_set))	
		 {
			$monto_compromiso=$monto_compromiso+$row['comp_monto']; 
   		}}
   		
   		if ($monto_compromiso<0){
		$prefijo="menos ";
		$prefijo2="-";
		$monto_compromiso=$monto_compromiso*(-1);
	    }
   		$montoletras=monto_letra($monto_compromiso, " BOLIVARES");
	?>
  <tr>
	<td class="normalNegrita">Monto solicitado:</td> 
	<td class="normalNegro" colspan="3">El monto total es de <?php echo $prefijo.$montoletras;?> (BS. F. <?php echo ($prefijo2.number_format($monto_compromiso,2,',','.'));?>)</td>
  </tr>
  <tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">Datos de imputaci&oacute;n presupuestaria</td>
  </tr>
	<?php
	$query="select t7.comp_monto, t7.comp_tipo_impu,
	t7.comp_sub_espe, t8.aces_nombre as centralespnombre, t8.centro_gestor as centrogestorac, t8.centro_costo as centrocostoac, 
	t9.paes_nombre as proyectoespnombre, t9.centro_gestor as centrogestorpr, 
	t9.centro_costo as centrocostopr, t10.acce_denom as centralprinombre,t11.proy_titulo as proyectoprinombre  from sai_comp_imputa t7 
	left outer join  sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id and t8.pres_anno='".$anno_pres."' )
 	left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$anno_pres."')
	left outer join  sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id and t9.pres_anno='".$anno_pres."' )
	left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and t11.pre_anno='".$anno_pres."' )
	where t7.comp_id='".$codigo."' and t7.pres_anno='".$anno_pres."' order by comp_sub_espe";

	$result=pg_query($conexion,$query);
	while($row=pg_fetch_array($result)) {
		$centralespnombre=$row["centalespnombre"];
		$centralprinombre=$row["centralprinombre"];
		$proyectoespnombre=$row["proyectoespnombre"];
		$proyectoprinombre=$row["proyectoprinombre"];

		$montosubespecifica=$row['comp_monto'];
		$subespecifica=$row["comp_sub_espe"];
		$centrogestorac=$row["centrogestorac"];
		$centrocostoac=$row["centrocostoac"];
		$centrogestorpr=$row["centrogestorpr"];
		$centrocostopr=$row["centrocostopr"];
	    $tipo_p_ac=$row['comp_tipo_impu'];
	?>
  <tr>
	<td  class="normalNegrita">PP/ACC: </b><span class="normalNegro">
	<?php  
	if ($tipo_p_ac=='1') {
	 echo $centrogestorpr; 
	}else
	  echo $centrogestorac;
	?></span></td>
	<td><b class="normalNegrita">Acci&oacute;n Esp.: </b><span class="normalNegro">
	<?php 
	if ($tipo_p_ac=='1') {
		echo $centrocostopr; 
	}else{
		echo $centrocostoac;
	}	
	?></span></td>
	<td><b class="normalNegrita">Partida: </b><span class="normalNegro"><?php echo $subespecifica; ?></span></td>
	<td class="normalNegrita">
	Monto BsF.:<span class="normalNegro"><?php echo (number_format($montosubespecifica,2,',','.')) ?></span></td>
  </tr>
<? }?>
</table>
<br/>

<table width="570" align="center">
  <tr>
	<td align="center" class="normal"><a href="javascript: generarPdf();" >Generar documento en formato PDF</a>
	 <a href="comp_pdf.php?codigo=<?= $codigo; ?>"><img src="../../imagenes/pdf_ico.jpg" border="0"/></a></td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
	<td align="center"><a href="javascript:imprimir();" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0"/></a></td>
  </tr>
</table>
<div id="noimprimir" style="visibility:visible" align="center">
<table width="420" align="center">
	<? $cod_doc=$codigo;
	   include("../../includes/respaldos_mostrar.php");
	?>
</table>
</div>
</form>
</body>
</html>