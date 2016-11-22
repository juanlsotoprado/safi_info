<?
ob_start();
	 require_once("../../includes/conexion.php");
	  
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }


$codigo=$_REQUEST['codigo'];
$cod_doc=$codigo;
$_SESSION["pcta"]=$codigo;

$query="select t1.esta_id,t1.pcta_garantia, t1.pcta_prioridad, t1.pcta_id, t1.pcta_id_dest, t2.empl_nombres as nbpresenta, pcta_asociado,t1.pcta_asunto,
t2.empl_apellidos as appresenta, t3.carg_nombre, t4.depe_nombre as depesolicitante, t5.empl_nombres  as nbsolicitante, t5.empl_apellidos as apsolicitante, t7.empl_nombres as nbelabora, 
t7.empl_apellidos as apelabora, EXTRACT(DAY FROM t1.pcta_fecha)||'/'||EXTRACT(month FROM t1.pcta_fecha)||'/'||EXTRACT(Year FROM t1.pcta_fecha) as pcta_fecha,t1.pcta_observacion, t1.pcta_descripcion, t1.pcta_justificacion, t1.pcta_monto_solicitado, t1.numero_reserva, t1.pcta_lapso, t1.pcta_cond_pago , t6.pcas_nombre as asunto,rif_sugerido
from sai_pcuenta t1, sai_empleado t2, sai_cargo t3, sai_dependenci t4, sai_empleado t5, sai_pcta_asunt t6, sai_empleado t7
where t1.pcta_id=trim('".$codigo."') and t1.pcta_gerencia=t4.depe_id and t1.pcta_id_remit=t5.empl_cedula and trim(t1.pcta_asunto)=trim(t6.pcas_id) and t1.pcta_presentado_por=t2.empl_cedula and t2.carg_fundacion=t3.carg_fundacion and t1.usua_login=t7.empl_cedula ";


$result=pg_query($conexion,$query);
if($row=pg_fetch_array($result))
{
	$ci_destinatario=$row["pcta_id_dest"]; 

	if (strpos($ci_destinatario,"/")){
     $ci_1=substr($ci_destinatario,0,strpos($ci_destinatario,"/"));
	 $ci_2=substr($ci_destinatario,strpos($ci_destinatario,"/")+1);
 	 $query_destino="select t2.empl_nombres as nbdestinatario,t2.empl_apellidos as apdestinatario,t3.carg_nombre from sai_empleado t2, sai_cargo t3 where t2.carg_fundacion=t3.carg_fundacion and (empl_cedula='".$ci_1."' or empl_cedula='".$ci_2."')";

	}else{
	      $query_destino="select t2.empl_nombres as nbdestinatario,t2.empl_apellidos as apdestinatario,t3.carg_nombre from sai_empleado t2, sai_cargo t3 where t2.carg_fundacion=t3.carg_fundacion and empl_cedula='".$ci_destinatario."'";
	}

	$result_destino=pg_query($conexion,$query_destino);
	if ($rowd=pg_fetch_array($result_destino))
	{
	 $destinatario=$rowd["nbdestinatario"]." ".$rowd["apdestinatario"]."   "; 
	 $cargodestinatario=$rowd["carg_nombre"]."   ";
	}

	if  ($rowd=pg_fetch_array($result_destino))
	{
	  $destinatario2=$rowd["nbdestinatario"]." ".$rowd["apdestinatario"]."   "; 
	  $cargodestinatario2=$rowd["carg_nombre"]."   ";
	}
	
	$remitente=$row["nbsolicitante"]." ".$row["apsolicitante"];
	$elabora=$row["nbelabora"]." ".$row["apelabora"];
	$presentado=$row["nbpresenta"]." ".$row["appresenta"];
	$depesolicitante=$row["depesolicitante"];
	$fecha=$row["pcta_fecha"];
	$observacion=$row["pcta_observacion"];
	$descripcion=$row["pcta_descripcion"];
	$justificacion=$row["pcta_justificacion"];
	$lapso=$row['pcta_lapso'];
	$condicion=$row['pcta_cond_pago'];
    $monto=$row["pcta_monto_solicitado"];
	$garantia=$row["pcta_garantia"];
	$prioridad=$row["pcta_prioridad"];
	$asunto=$row["asunto"];
	$subespecifica=$row["pcta_sub_espe"];
	$centrogestorac=$row["centrogestorac"];
	$centrocostoac=$row["centrocostoac"];
	$centrogestorpr=$row["centrogestorpr"];
	$centrocostopr=$row["centrocostopr"];
	$numeroreserva=$row["numero_reserva"];
	$rif_sugerido=$row['rif_sugerido'];
    $pcta_asociado=$row['pcta_asociado'];
    $pcta_asunto=$row['pcta_asunto'];
    $edo=$row['esta_id'];
    
include("../../includes/monto_a_letra.php");
$montoletras=monto_letra($monto, " BOLIVARES");
$montoletrasbase=monto_letra($monto_base, " BOLIVARES");
$montoletrasiva=monto_letra($monto_iva, " BOLIVARES");
$centralespnombre=$row["centalespnombre"];
$centralprinombre=$row["centralprinombre"];
$proyectoespnombre=$row["proyectoespnombre"];
$proyectoprinombre=$row["proyectoprinombre"];
}
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="javascript">

function imprimir()
  {
document.getElementById('noimprimir').style.visibility='hidden';
  window.print();
  }
</script>
</head>
<body>
<form name="form" method="post" action="">
<table align="center" width="100%" bgcolor="#ffffff" border="0" bordercolor="#0099cc" cellpadding="0" cellspacing="0" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr  class="td_gray" > 
<td colspan="2" align="center" class="normalNegroNegrita">
	PUNTO DE CUENTA <?php echo $codigo?>
</td>
</tr>
  <?php 
	if ($edo==15) { ?>
  <tr>
	<td colspan="5">
	  <div align="center"><font color="Red"><STRONG>ANULADO</STRONG></font></div></td>
  </tr>
  <?}?>
<tr class="normal2"> 
<td class="normalNegrita">Preparado a: <font class="normalNegro">
	<?php 
		echo $cargodestinatario.": ".$destinatario; 
		if ($destinatario2<>''){ echo ", ".$cargodestinatario2.": ".$destinatario2; }
	?></font>
</td>
</tr>
<tr class="normalNegrita">
<td>Elaborado por:<font class="normalNegro"><?php echo $elabora;?></font></td></tr>
<tr><td class="normalNegrita">Solicitado por:<font class="normalNegro"><?php echo $remitente;?></font></td>
</tr>
<tr >
<td class="normalNegrita"> Presentado por:	 <font class="normalNegro"> <?php echo $presentado;?></font></td>
</tr>
<tr>
<td  class="normalNegrita">Unidad/Dependencia:<font class="normalNegro"><?php echo $depesolicitante;?></font></td>
</tr>
<tr >
<td class="normalNegrita">Fecha:<font class="normalNegro"><?php echo $fecha?></font></td></tr>
<tr>
<td class="normalNegrita">Prioridad: 
<font class="normalNegro">	<?php
	  if ($prioridad==1) { echo "Baja"; } 
      if ($prioridad==2) { echo "Media";} 
      if ($prioridad==3) { echo "Alta"; }?></font>
</td>
</tr>
<tr class="td_gray"> 
<td align="center" class="normalNegroNegrita" >
	ELEMENTOS DEL PUNTO DE CUENTA
</td>
</tr>
<tr > 
<td class="normalNegrita">Asunto:
<font class="normalNegro"> <?php echo $asunto;?>
<?php if (($pcta_asunto=="013") && ($pcta_asociado<>"") && ($pcta_asociado<>"0")){
		echo " ".$pcta_asociado;
	  }?></font></td>
</tr>
<tr class="normal2">
<td class="normalNegrita">Descripci&oacute;n:
    <font class="normalNegro">  <?php echo $descripcion;?></font>
      </td>
      
<tr/>
<tr><td class="normalNegrita" align="justify">
	Justificaci&oacute;n:
	
    <font class="normalNegro">  <?php echo $justificacion;?></font>
      </td>	
<tr/>
<tr><td class="normalNegrita" align="justify">
	Lapso de Convenio/Contrato:
	
    <font class="normalNegro"> <?php echo $lapso;?></font>
      </td>	
<tr/>
<tr><td class="normalNegrita" align="justify">
	Garant&iacute;a:
	
   <font class="normalNegro"><?php echo $garantia;?></font>
      </td>		
<tr/>
<tr><td class="normalNegrita">
	Rif del Proveedor Sugerido:
	
 <font class="normalNegro">  <?php echo $rif_sugerido;?></font>
      </td>		
<tr/>
<tr><td class="normalNegrita" align="justify">
	Condiciones de Pago:
	
  <font class="normalNegro"> <?php echo $condicion;?></font>
      </td>	
</tr>

<tr>
<td colspan="2" class="normalNegrita" align="justify">Observaci&oacute;n:
  <font class="normalNegro"> <?php echo $observacion;?></font>

</td>
</tr>
<tr>
<td colspan="2"  class="normalNegrita">Monto solicitado:
  <font class="normalNegro">
 <?php echo $montoletras;?> (BS. <?php echo (number_format($monto,2,'.',','));?>)</font>
      </td>		
</td></tr>
<tr class="td_gray"> 
		<td colspan="2" align="center" class="normalNegroNegrita">
		DATOS DE IMPUTACI&Oacute;N PRESUPUESTARIA
</td>
</tr>
<tr class="normal2"><td colspan="2">
<table align="center" width="70%" border="1">
<tr class="normalNegro" align="center">
<td>Proyecto/Acci&oacute;n Centralizada</td>
<td>Acci&oacute;n espec&iacute;fica</td>
<td>Partida</td>
<td>Monto (Bs.)</td>
</tr>
	<?php
	$query="select t7.pcta_monto, 
	t7.pcta_sub_espe, t8.aces_nombre as centralespnombre, t8.centro_gestor as centrogestorac, t8.centro_costo as centrocostoac, 
	t9.paes_nombre as proyectoespnombre, t9.centro_gestor as centrogestorpr, 
	t9.centro_costo as centrocostopr, t10.acce_denom as centralprinombre,t11.proy_titulo as proyectoprinombre  from sai_pcta_imputa t7 
	left outer join  sai_acce_esp t8 on (t7.pcta_acc_pp=t8.acce_id and t7.pcta_acc_esp=t8.aces_id and t7.pres_anno=t8.pres_anno)
 	left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t7.pres_anno=t10.pres_anno)
	left outer join  sai_proy_a_esp t9 on(t7.pcta_acc_pp=t9.proy_id and t7.pcta_acc_esp=t9.paes_id and t7.pres_anno=t9.pres_anno)
	left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and t7.pres_anno=t11.pre_anno)
	where t7.pcta_id='".$codigo."' order by t7.pcta_sub_espe";
	

	$result=pg_query($conexion,$query);
	while($row=pg_fetch_array($result)) {
		$centralespnombre=$row["centalespnombre"];
		$centralprinombre=$row["centralprinombre"];
		$proyectoespnombre=$row["proyectoespnombre"];
		$proyectoprinombre=$row["proyectoprinombre"];

		$montosubespecifica=$row['pcta_monto'];
		$subespecifica=$row["pcta_sub_espe"];
		$centrogestorac=$row["centrogestorac"];
		$centrocostoac=$row["centrocostoac"];
		$centrogestorpr=$row["centrogestorpr"];
		$centrocostopr=$row["centrocostopr"];
	?>
<tr class="normalNegro">
<td><?echo ($centrogestorac+' '+ $centrogestorpr);?></td>
<td><?echo ($centrocostoac+' '+ $centrocostopr);?></td>
<td><?echo ($subespecifica);?></td>
<td><?echo (number_format($montosubespecifica,2,'.',','));?></td>
</tr>
<?}?>
</table></td></tr>
<?php 
	$l=strlen($codigo);
	$ao=substr($codigo,$l-2,$l);
if ($ao<11){?>
<tr >
<td colspan="2" class="normalNegrita">N&uacute;mero de reserva:<font class="normalNegro"><?php echo $numeroreserva; ?></font></td>
</tr>
<?php }?>
</table>
<?php 
if (substr($asunto,0,4)<>"Libe"){?>
<table align="center" width="100%" bgcolor="#ffffff" border="1" bordercolor="#0099cc" cellpadding="0" cellspacing="0" background="../../imagenes/fondo_tabla.gif">
<tr class="td_gray"> 
<td colspan="2" align="center"  class="normalNegroNegrita">FIRMAS </td></tr>
<tr>
<td colspan="2">
				<table width="100%" border="1" bordercolor="#003366" cellpadding="3" cellspacing="0">
				<tr>
				<td class="normalNegro"><br/><br/><br/><br/><center>
				_______________________________<br/>
					Solicitado por: <br/><?php echo $remitente; ?></center> <?php $_SESSION["remitente"]=$remitente;?>
				</td>
			<td class="normalNegro"><br/><br/><br/><br/><center>_______________________________<br/>
				Presentado por: <br/><?php echo $presentado;?> <?php $_SESSION["presentado"]=$presentado;?>
			</center>
			</td>
<td class="normalNegro"><br/><br/><br/><br/><center>_______________________________<br/>
				Jefatura de Planificaci&oacute;n y <br/>Presupuesto
			</center>
			</td>
			</tr></table>
</td>
</tr>
<tr class="td_gray" > 
<td colspan="2" align="center" class="normalNegroNegrita">DATOS DEL RESULTADO</td>
</tr>
<tr> 
<td colspan="2" class="normalNegro" > 
				<table width="100%" border=1>
				<tr>
				<td class="normalNegro"><center>Direcci&oacute;n Ejecutiva:<br/><br/>
				<br/>
				____________________<br/>Sandino Marcano
				</center>
				</td>
				<td class="normalNegro">
				Sugerido:<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Aprobado<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Negado<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Diferido<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Visto
				</td>
				<td class="Estilo2Previa"><center>Presidencia:<br/><br/>
				<br/>
				____________________<br/>
				Ram&oacute;n David Parra</center>
				</td>
				<td class="Estilo2Previa">
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Aprobado<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Negado<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Diferido<br/>
				<input name="checkbox2" disabled="disabled" value="checkbox" type="checkbox"/>
				Visto
				</td>
				</tr>
				<tr>
				<td colspan=2 class="Estilo2Previa"><center>Instrucciones u observaciones</center><br/><br/><br/>
				</td>
				<td colspan=2 class="Estilo2Previa"><center>Instrucciones u observaciones</center><br/><br/><br/>
				</td>
				</tr>
				</table>
</td>
</tr>
</table>
<?php }?>
<div id="noimprimir" style="visibility:visible" align="center">
<table width="40%">
<tr align="center">
<td class="normal"><img src="../../imagenes/pdf_ico.jpg" width="32" height="32"/><a href="pcta_detalle_PDF.php?codigo=<?= (trim($codigo)); ?>&tipo=L"><br>Imprimir PDF</a></td>
<!-- <td class="normal"><a href="pcta_detalle_PDF.php?codigo=<?= (trim($codigo)); ?>&tipo=F">Formato 2 (firmas x hoja) </a><img src="../../imagenes/pdf_ico.jpg" width="32" height="32"/></td> -->
</td>
</tr>
</table>
<br/>
<table width="60%" align="center">
	<?   $cod_doc=$codigo;
	   include("../../includes/respaldosMostrarDetalle.php");
	?>
</table>
</div>
</form>
</body>
</html>
<?php pg_close($conexion);?>