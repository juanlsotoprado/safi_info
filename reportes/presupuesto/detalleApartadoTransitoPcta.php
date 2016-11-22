<?
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/perfiles/constantesPerfiles.php");

if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 

$idProyectoAccion=$_GET['proy'];
$idAccionEspecifica=$_GET['aesp'];
$codigoPartida=$_GET['pcta'];
$fechaInicio=$_GET['fecha_inicio'];
$fechaFin=$_GET['fecha_fin'];	
$codigoPartida = $_GET['partida'];	
$tipoImputacion=$_GET['tipo'];
$monto=$_GET['monto'];

$anno_pres=$_SESSION['an_o_presupuesto'];

$opcionConsolidar = $_GET['consolidado']

?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI.:Detalle Apartado en Transito</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
</head>
<body>
<?

if($opcionConsolidar=="2" || $opcionConsolidar=="3"){

	$query ="
			         
				 SELECT 

				       b.pcta_sub_espe AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido,
				       a.pcta_id
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
			   
                   if($opcionConsolidar=="2"){
			   	
						   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			  
			$query .=") AS se";
			   	
			   }else  if($opcionConsolidar=="3"){
			   	
			   		   	 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
			          spae.paes_id AS id_accion_especifica
			
			   FROM sai_proy_a_esp spae
			   WHERE  spae.pres_anno = ".$anno_pres;
			   		   	 
			   		   	  $query .=" UNION SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica
				         
				   FROM sai_acce_esp sae
				   WHERE  sae.pres_anno = ".$anno_pres;
			  
			$query .="
			
			) AS se";
			   	
			   }
				
				 $query .="
				WHERE ";
				
				
             	  $query .="   a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id";
				 
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY b.pcta_sub_espe,
				a.pcta_id
				     
				ORDER BY 
				         b.pcta_sub_espe";	

}else{
	
			 
		  $query ="
			   
				            SELECT b.pcta_acc_esp AS id_accion_especifica,
				       se.centro_gestor,
				       se.centro_costo,
				       b.pcta_sub_espe AS part_id,
				       COALESCE(SUM(b.pcta_monto),0) AS monto_diferido,
				        a.pcta_id
				FROM sai_pcuenta AS a,
				     sai_pcta_imputa AS b,
				     sai_doc_genera d,";
				     
			   if($tipoImputacion=="1"){
			 $query .="(SELECT spae.proy_id AS id_proyecto_accion,
          spae.paes_id AS id_accion_especifica,
          spae.centro_gestor,
          spae.centro_costo
   FROM sai_proy_a_esp spae
   WHERE spae.proy_id =  '".$idProyectoAccion."'
     AND spae.pres_anno = ".$anno_pres."
   ORDER BY spae.centro_gestor,
            spae.centro_costo,
            spae.paes_id ) AS se";
			 
			   }
			 
			  if($tipoImputacion=="0"){
			 $query .="(SELECT sae.acce_id AS id_proyecto_accion,
				          sae.aces_id AS id_accion_especifica,
				          sae.centro_gestor,
				          sae.centro_costo
				   FROM sai_acce_esp sae
				   WHERE sae.acce_id = '".$idProyectoAccion."'
				     AND sae.pres_anno = ".$anno_pres."
				   ORDER BY sae.centro_gestor,
				            sae.centro_costo,
				            sae.aces_id) AS se";
			  }   
				
				 $query .="
				WHERE a.pcta_id=d.docg_id
				  AND a.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days'
				  AND se.id_proyecto_accion = b.pcta_acc_pp
				  AND se.id_accion_especifica = b.pcta_acc_esp
				  AND b.pres_anno=".$anno_pres."
				  AND a.esta_id <>15
				  AND d.esta_id <>15
				  AND d.esta_id <>13
				  AND d.perf_id_act not in ('".PERFIL_PRESIDENTE."','".PERFIL_DIRECTOR_EJECUTIVO."')
				  
				  AND a.esta_id<>14
				  AND a.esta_id<>2
				  AND a.pcta_id=b.pcta_id
				  AND b.pcta_tipo_impu=".$tipoImputacion."::BIT
				  AND b.pcta_acc_pp=  '".$idProyectoAccion."'";
			   
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"AND b.pcta_acc_esp = '".$idAccionEspecifica."' ";
			}
			
			$query .= "
				AND b.pcta_sub_espe LIKE '".$codigoPartida."%'
				
				GROUP BY b.pcta_sub_espe,
				         pcta_acc_esp,
				         se.centro_gestor,
				         se.centro_costo,
				          a.pcta_id
				ORDER BY se.centro_gestor,
				         se.centro_costo,
				         b.pcta_acc_esp,
				         b.pcta_sub_espe";
		
}
      //    echo $query;

		$resultadoMontoApartado=pg_query($query) or die("Error en el monto apartado");	
			
?>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
<td class="normalNegroNegrita">C&oacute;digo</td>
<td class="normalNegroNegrita">Monto Bs.</td>
<td class="normalNegroNegrita">Partida</td>
</tr>

<?php
$sumatoria=0; 
while ($filaProgramadostransito=pg_fetch_array($resultadoMontoApartado)) {
	$codigo=$filaProgramadostransito['pcta_id'];
	$monto=$filaProgramadostransito['monto_diferido'];
	$sumatoria=$sumatoria+$monto;
?>
<tr class="normal">
<td><a href="javascript:abrir_ventana('../../documentos/pcta/pcta_detalle.php?codigo=<?php echo trim($codigo); ?>&amp;esta_id=10')" class="copyright"><?=$codigo;?></a></td>	
<td><? echo number_format($monto,2,',','.');?></td>
<td><?=$codigoPartida;?></td>
</tr>
<?php 
}
pg_close($conexion);
?>
<tr class="td_gray">
<td class="normalNegroNegrita">Total Bs.:</td>
<td colspan="2" class="normalNegroNegrita"><?echo number_format($sumatoria,2,',','.');?></td>
</tr>
</table>

