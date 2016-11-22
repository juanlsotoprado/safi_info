<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
require("includes/perfiles/constantesPerfiles.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {  
	header('Location: index.php',false); ;
	ob_end_flush(); 
	exit;
}
	
ob_end_flush(); 
	
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$a_o_actual="pcta-%".substr($_SESSION['an_o_presupuesto'],2,2);

//Buscar nombre del tipo de documento
$request_id_tipo_documento = "";
if (isset($_REQUEST["tipo"])) {
	$request_id_tipo_documento = $_REQUEST["tipo"];	
}
$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$nombre_documento = $row["resultado"];
}

//Actualizar compromiso
if ($request_id_tipo_documento=="comp") {
	$sql2 = " update sai_doc_genera set wfob_id_ini=2, wfca_id=186, perf_id_act='' where wfob_id_ini=4 and wfca_id=187";
	$resultado = pg_query($conexion,$sql2) or die("Error al mostrar");
}

//Verificar si el usuario tiene permiso para el objeto (accion) actual
$sql = " SELECT * FROM sai_permiso_documento('$request_id_tipo_documento','$user_perfil_id') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$tiene_permiso = $row["resultado"];
}
if (($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)){
	$tiene_permiso=1;
}
if ($tiene_permiso == 0) {
	//Enviar mensaje de error
?>
	<script>
	document.location.href = "mensaje.php?pag=principal.php";
</script>
<?
	header('Location:index.php',false);	
}

$estado_doc = 10;
	
	
if ($request_id_tipo_documento=="sopg") {
	$opcion_anular=1;
	//Nueva incorporación
	$sql = "SELECT d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, s.sopg_fecha as fecha, s.sopg_bene_ci_rif as ci_rif, ts.nombre_sol as tipo_solicitud, upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) || upper(coalesce(p.prov_nombre,'')) as nombre_beneficiario, upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiariov, upper(substring(s.sopg_detalle from 0 for 45))  as detalle
	FROM sai_doc_genera d
	left outer join sai_sol_pago s on (trim(s.sopg_id)=trim(d.docg_id)) 
	left outer join sai_tipo_solicitud ts on (s.sopg_tp_solicitud=ts.id_sol)
	left outer join sai_empleado em on (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif))
	left outer join sai_proveedor_nuevo p on (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif))
	left outer join sai_viat_benef v on (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif))
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))	
	WHERE d.docg_id=s.sopg_id and d.docg_id like 'sopg%' and d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY d.docg_fecha desc limit 300";
}
else if ($request_id_tipo_documento=="pgch") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, ch.fechaemision_cheque as fecha, s.sopg_bene_ci_rif as ci_rif, ts.nombre_sol as tipo_solicitud, upper(beneficiario_cheque) as nombre_beneficiario, ch.monto_cheque as monto
	FROM sai_doc_genera d
	left outer join sai_pago_cheque pch on (d.docg_id=pch.pgch_id)
	left outer join sai_cheque ch on (ch.docg_id=pch.docg_id)
	left outer join sai_sol_pago s on (s.sopg_id=pch.docg_id)	
	left outer join sai_tipo_solicitud ts on (trim(s.sopg_tp_solicitud)=trim(ts.id_sol))			
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=pch.pgch_id and ch.estatus_cheque != 15 and ch.docg_id=pch.docg_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY s.sopg_fecha";
}
else if ($request_id_tipo_documento=="tran") {
	//$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, pt.trans_fecha as fecha, s.sopg_bene_ci_rif as ci_rif, 'Transferencia' as tipo_solicitud, upper(coalesce(pt.beneficiario,'')) as nombre_beneficiario, pt.trans_monto as monto
	FROM sai_doc_genera d
	left outer join sai_pago_transferencia pt on (d.docg_id=pt.trans_id)
	left outer join sai_sol_pago s on (s.sopg_id=pt.docg_id)
	left outer join sai_tipo_solicitud ts on (trim(s.sopg_tp_solicitud)=trim(ts.id_sol))	
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=pt.trans_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY s.sopg_fecha";
}
else if ($request_id_tipo_documento=="pcta") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, p.pcta_fecha as fecha, pa.pcas_nombre as asunto, upper(substring(p.pcta_justificacion from 0 for 45))  as justificacion 
	FROM sai_doc_genera d
	left outer join sai_pcuenta p on (d.docg_id=p.pcta_id)
	left outer join sai_pcta_asunt pa on (pa.pcas_id=p.pcta_asunto)			
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=p.pcta_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY p.pcta_fecha, d.docg_id";
}
else if ($request_id_tipo_documento=="comp") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, c.comp_fecha as fecha, ca.cpas_nombre as asunto, upper(substring(c.comp_justificacion from 0 for 45))  as justificacion 
	FROM sai_doc_genera d
	left outer join sai_comp c on (d.docg_id=c.comp_id)
	left outer join sai_compromiso_asunt ca on (ca.cpas_id=c.comp_asunto)			
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=c.comp_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY c.comp_fecha";
}
else if ($request_id_tipo_documento=="pmod") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, f.f030_id as codigo, f.f030_motivo as motivo, f.f030_fecha as fecha,  upper(em.empl_nombres)||' '||upper(em.empl_apellidos) as usuario 
	FROM sai_doc_genera d
	left outer join sai_forma_0305 f on (f.f030_id=d.docg_id)
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	left outer join sai_empleado em on (trim(em.empl_cedula)=trim(d.usua_login))
	where d.usua_login=em.empl_cedula and d.docg_id=f.f030_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY f.f030_fecha";
}
else{
     //Buscar documentos en bandeja
	$sql = "SELECT docg_id, wfob_id_ini, wfca_id, usua_login, perf_id, docg_fecha as fecha, esta_id, docg_prioridad, perf_id_act 
	FROM sai_doc_genera WHERE docg_id like '$request_id_tipo_documento%' AND esta_id=$estado_doc AND perf_id_act='$user_perfil_id'   
	ORDER BY docg_fecha desc limit 100";	
} 
	
$resultado_doc_bandeja=pg_query($conexion,$sql) or die("Error al Mostrar Lista de Documentos");
$total_doc_bandeja=pg_num_rows($resultado_doc_bandeja);
	

$estado_doc_dev = 7;	//Estado de doc devueltos
$estado_aprobado = 13;
$estado_rechazado = 14;
$estado_pendiente = 39;
$estado_transito = 10;

//Cambios efectuados para realizar la devolución en sopg desde el Adjunto al Jefe de Ordenación de Pago y no a quien creo el sopg

if ($request_id_tipo_documento=="sopg") {
	if ($user_perfil_id=="42450"){
		/*Nueva incorporación*/
		$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, s.sopg_fecha as fecha, s.sopg_bene_ci_rif as ci_rif, ts.nombre_sol as tipo_solicitud, upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) || upper(coalesce(p.prov_nombre,'')) as nombre_beneficiario, upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiariov, upper(substring(s.sopg_detalle from 0 for 45))  as detalle
		FROM sai_doc_genera d
		left outer join sai_sol_pago s on (trim(s.sopg_id)=trim(d.docg_id)) 
		left outer join sai_tipo_solicitud ts on (s.sopg_tp_solicitud=ts.id_sol)
		left outer join sai_empleado em on (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif))
		left outer join sai_proveedor_nuevo p on (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif))
		left outer join sai_viat_benef v on (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif))
		left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))	
		WHERE d.docg_id=s.sopg_id and d.docg_id like 'sopg%' AND (d.esta_id=$estado_transito OR d.esta_id=$estado_doc_dev) AND d.perf_id_act='".$user_perfil_id."'  and wfca_id=7 
		ORDER BY d.docg_fecha desc limit 300";
	}
	else {
		$sql="SELECT d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, s.sopg_fecha as fecha, s.sopg_bene_ci_rif as ci_rif, ts.nombre_sol as tipo_solicitud, upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) || upper(coalesce(p.prov_nombre,'')) as nombre_beneficiario, upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiariov, upper(substring(s.sopg_detalle from 0 for 45))  as detalle
		FROM sai_doc_genera d
		left outer join sai_sol_pago s on (trim(s.sopg_id)=trim(d.docg_id)) 
		left outer join sai_tipo_solicitud ts on (s.sopg_tp_solicitud=ts.id_sol)
		left outer join sai_empleado em on (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif))
		left outer join sai_proveedor_nuevo p on (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif))
		left outer join sai_viat_benef v on (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif))
		left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))	
		WHERE d.docg_id=s.sopg_id and d.docg_id like 'sopg%' AND (d.esta_id=$estado_transito OR d.esta_id=$estado_doc_dev) AND d.usua_login='".$usuario."'  and wfca_id<>7 
		ORDER BY d.docg_fecha desc limit 300";

		//AÑO ACTUAL PARA LAS ORDENES DE COMPRA, 
		//AL INICIAR EL PRÓXIMO AÑO HABILITAR ORDC AÑO ANTERIOR Y ACTUAL
		$ordc="SELECT d.docg_id as docg_id, to_char(oc.fecha,'DD/MM/YYYY') as fecha, oc.rif_proveedor_seleccionado as ci_rif, upper(coalesce(p.prov_nombre,'')) as nombre_beneficiario, upper(substring(oc.justificacion from 0 for 60))  as detalle
		FROM sai_doc_genera d
		left outer join sai_orden_compra oc on (trim(oc.ordc_id)=trim(d.docg_id) and oc.esta_id<>15) 
		left outer join sai_proveedor_nuevo p on (trim(oc.rif_proveedor_seleccionado)=trim(p.prov_id_rif))
		WHERE docg_id like 'ordc%".substr($_SESSION['an_o_presupuesto'],2,2)."' and d.wfob_id_ini=99 and d.esta_id<>15
		and oc.ordc_id not in (select documento_asociado from sai_sol_pago sp where sp.esta_id<>15 AND documento_asociado=d.docg_id) 
		ORDER BY d.docg_fecha desc limit 50";
	}//		and d.docg_id not in (select documento_asociado from sai_sol_pago sp where esta_id<>15 AND documento_asociado=d.docg_id)
}
else if ($request_id_tipo_documento=="pgch") {
	$sql = "
		SELECT
			d.usua_login,
			d.perf_id_act,
			d.esta_id,
			d.wfob_id_ini,
			e.esta_nombre as estado,
			d.docg_prioridad,
			d.docg_id as docg_id,
			ch.fechaemision_cheque as fecha,
			s.sopg_bene_ci_rif as ci_rif,
			ts.nombre_sol as tipo_solicitud,
			upper(beneficiario_cheque) as nombre_beneficiario,
			ch.monto_cheque as monto
		FROM
			sai_doc_genera d
			LEFT JOIN sai_pago_cheque pch ON (d.docg_id=pch.pgch_id)
			LEFT JOIN sai_cheque ch ON (ch.docg_id=pch.docg_id)
			LEFT JOIN sai_sol_pago s ON (s.sopg_id=pch.docg_id)	
			LEFT JOIN sai_tipo_solicitud ts ON (trim(s.sopg_tp_solicitud)=trim(ts.id_sol))			
			LEFT JOIN sai_estado e ON (trim(e.esta_id)=trim(d.esta_id))		
		WHERE
			d.docg_id = pch.pgch_id
			--and ch.docg_id = pch.docg_id
			AND d.docg_id LIKE '".$request_id_tipo_documento."%'
			AND d.esta_id = ".$estado_doc_dev." 
			AND d.perf_id_act = '".$user_perfil_id."' 
		ORDER BY
			s.sopg_fecha
	";
	
}
	else if ($request_id_tipo_documento=="tran") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, pt.trans_fecha as fecha, s.sopg_bene_ci_rif as ci_rif, ts.nombre_sol as tipo_solicitud, upper(coalesce(pt.beneficiario,'')) as nombre_beneficiario, pt.trans_monto as monto
	FROM sai_doc_genera d
	left outer join sai_pago_transferencia pt on (d.docg_id=pt.trans_id)
	left outer join sai_sol_pago s on (s.sopg_id=pt.docg_id)
	left outer join sai_tipo_solicitud ts on (trim(s.sopg_tp_solicitud)=trim(ts.id_sol))	
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=pt.trans_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc_dev AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY s.sopg_fecha";
}	
else if ($request_id_tipo_documento=="pcta") {
	if ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS){
	 $sql="SELECT * FROM sai_doc_genera WHERE docg_id like '?%?'";	
	}else{
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, p.pcta_fecha as fecha, pa.pcas_nombre as asunto, upper(substring(p.pcta_justificacion from 0 for 45)) as justificacion  
	FROM sai_doc_genera d
	left outer join sai_pcuenta p on (d.docg_id=p.pcta_id)
	left outer join sai_pcta_asunt pa on (pa.pcas_id=p.pcta_asunto)			
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where p.depe_id='".substr($user_perfil_id,2,3)."' and d.docg_id=p.pcta_id and d.docg_id like '".$request_id_tipo_documento."%' AND (d.esta_id=".$estado_transito." or d.esta_id=".$estado_doc_dev.")  
	ORDER BY p.pcta_fecha, d.docg_id";
	}
	$fec = date("d/m/Y");
	list($dia,$mes,$an_o) = explode ("/",$fec);
	$fecha_devueltos=$an_o."-".$mes."%";
	
	$pcta_devueltos="select * from sai_revisiones_doc t1,sai_doc_genera 
	where revi_doc=docg_id and revi_doc like 'pcta%' and esta_id<>15 and wfop_id=5 and wfob_id_ini <>99
	and revi_fecha like '".$fecha_devueltos."' and numero_reserva is not null  and numero_reserva<>'' and t1.perf_id in ('46450','47350','65150') order by docg_fecha";
	
	$pcta_anulados="select doso_doc_fuente,memo_fecha_crea,pcta_fecha,pcas_nombre from sai_memo,sai_docu_sopor,sai_pcuenta, sai_pcta_asunt where doso_doc_soport=memo_id and memo_asunto='Devolucion Punto de Cuenta' and memo_fecha_crea like '".$fecha_devueltos."' and doso_doc_fuente=pcta_id and pcta_asunto=pcas_id order by memo_fecha_crea";
}	
else if ($request_id_tipo_documento=="comp") {
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, c.comp_fecha as fecha, ca.cpas_nombre as asunto, upper(substring(c.comp_justificacion from 0 for 45))  as justificacion 
	FROM sai_doc_genera d
	left outer join sai_comp c on (d.docg_id=c.comp_id)
	left outer join sai_compromiso_asunt ca on (ca.cpas_id=c.comp_asunto)			
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	where d.docg_id=c.comp_id and d.docg_id like '".$request_id_tipo_documento."%' AND d.esta_id=$estado_doc_dev AND d.perf_id_act='".$user_perfil_id."' 
	ORDER BY c.comp_fecha";
}
else if ($request_id_tipo_documento=="pmod") {
	$opcion_anular=1;	
	$sql= "SELECT  d.usua_login, d.perf_id_act, d.esta_id, d.wfob_id_ini, e.esta_nombre as estado, d.docg_prioridad, d.docg_id as docg_id, f.f030_id as codigo, f.f030_motivo as motivo, f.f030_fecha as fecha,  upper(em.empl_nombres)||' '||upper(em.empl_apellidos) as usuario 
	FROM sai_doc_genera d
	left outer join sai_forma_0305 f on (f.f030_id=d.docg_id)
	left outer join sai_estado e on (trim(e.esta_id)=trim(d.esta_id))		
	left outer join sai_empleado em on (trim(em.empl_cedula)=trim(d.usua_login))
	where d.usua_login=em.empl_cedula and d.docg_id=f.f030_id and d.docg_id like '".$request_id_tipo_documento."%' AND (d.esta_id=$estado_transito or d.esta_id=$estado_doc_dev)  
	ORDER BY f.f030_fecha";
}

else {
	$sql = "SELECT docg_id as documento, wfob_id_ini, wfca_id, usua_login, perf_id, docg_fecha as fecha, esta_id as estado, docg_prioridad, perf_id_act 
	FROM sai_doc_genera WHERE docg_id like '$request_id_tipo_documento%' AND (esta_id=$estado_transito OR esta_id=$estado_doc_dev) AND usua_login='$usuario' 
	ORDER BY docg_fecha desc limit 150";
}

$resultado_doc_transito=pg_query($conexion,$sql) or die("Error al Mostrar Lista de Documentos");
$total_doc_transito=pg_num_rows($resultado_doc_transito);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="js/funciones.js"> </script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
	 <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center">
		<table width="326" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" >
          <tr>
            <td colspan="4"><img src="imagenes/bandeja_principal.jpg" width="326" height="29"></td>
            </tr>
			  <tr>
				<td width="20"></td>
				<td width="10">&nbsp;</td>
				<td width="200" class="normalNegroNegrita"><div align="left">
                        Documentos en bandeja:
                      </div></td>
				<td width="96" class="normalNegroNegrita"><div align="center"><?php echo $total_doc_bandeja; ?></div></td>
			  </tr>
		   <tr>
				<td width="20"></td>
				<td width="10">&nbsp;</td>
				<td width="200" class="normalNegroNegrita"><div align="left">Documentos en tr&aacute;nsito:</div></td>
				<td width="96" class="normalNegroNegrita"><div align="center"><?php echo $total_doc_transito; ?></div></td>
			  </tr>	
       </table>
   </td>
        <td width="42%">
		 <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="30">&nbsp;</td>
            <td width="170">&nbsp;</td>
          </tr>
          <tr>
            <td></td>
            <td class="GrandeNeg"><?php echo $nombre_documento; ?></td>
          </tr>
        </table>
		</td>
      </tr>
     </table>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center" class="normalNegroNegrita"> Bandeja de entrada </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <table width="100%" height="25" border="0" cellpadding="4" cellspacing="2" background="imagenes/fondo_tabla.gif" class="tablaalertas">
         <?php
		 if ($total_doc_bandeja>0) {
		 ?>
		  <tr class="td_gray" align="center">
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha</td>
            <?if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
				<td class="normalNegroNegrita">Beneficiario</td>
				<td class="normalNegroNegrita">Tipo solicitud</td>
			<?} if ($request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
				<td class="normalNegroNegrita">Monto Bs.</td>
			<?} else if ($request_id_tipo_documento=="pcta" || $request_id_tipo_documento=="comp") {?>
				<td class="normalNegroNegrita">Asunto</td>
				<td class="normalNegroNegrita">Justificaci&oacute;n</td>			
		   <?} else if ($request_id_tipo_documento=="pmod") {?>
				<td class="normalNegroNegrita">Motivo</td>
				<td class="normalNegroNegrita">Usuario</td>			
 	   		<?php }?> 	
            <td class="normalNegroNegrita">Opciones</td>
          </tr>	  
	        <?php while($row_doc_bandeja=pg_fetch_array($resultado_doc_bandeja))   {
			    	if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {
						$beneficiario_nombre = $row_doc_bandeja['nombre_beneficiario'];
						if (strlen($row_doc_bandeja['beneficiariov'])>2) $beneficiario_nombre = $row_doc_bandeja['beneficiariov'];
						$sol_nombre=$row_doc_bandeja['tipo_solicitud'];
						$detalle = $row_doc_bandeja['detalle'];
		  			 }
					$ano=substr($row_doc_bandeja['fecha'],0,4);
					$mes=substr($row_doc_bandeja['fecha'],5,2);
					$dia=substr($row_doc_bandeja['fecha'],8,2);
					$fecha=$dia."-".$mes."-".$ano;
		 ?>
          <tr class="normal">
            <td align="center" class="link">
				<!-- <a title="<? //echo $detalle; ?>" class="link"><?php //echo $row_doc_bandeja['docg_id']; ?></a>  -->
				<a href="accion_documento.php?accion=<? echo trim($row_doc_bandeja['wfob_id_ini']);  ?>&tipo=<? echo $request_id_tipo_documento;  ?>&id=<?php echo trim($row_doc_bandeja['docg_id']); ?>&esta_id=<?php echo $row_doc_bandeja['esta_id'];?>" class="copyright"><?php echo $row_doc_bandeja['docg_id']; ?></a>
			</td>
            <td><?php echo $fecha;?></td>
            <?php if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
            	<td><?php echo $beneficiario_nombre;?></td>
            	<td><?php echo $sol_nombre;?></td>
			<?} if ($request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>            	
            	<td  align="right"><?php echo number_format($row_doc_bandeja['monto'],2,',','.');?></td>
            <?php } else if ($request_id_tipo_documento=="pcta" || $request_id_tipo_documento=="comp") {?>
				<td><?php echo $row_doc_bandeja['asunto'];?></td>
            	<td><?php echo $row_doc_bandeja['justificacion'];?></td>            
             <?php } else if ($request_id_tipo_documento=="pmod") {?>
			  	<td><?php echo $row_doc_bandeja['motivo'];?></td>
            	<td><?php echo $row_doc_bandeja['usuario'];?></td>   
            	<?}?>
            <td>
            <a href="accion_documento.php?accion=<? echo trim($row_doc_bandeja['wfob_id_ini']);  ?>&tipo=<? echo $request_id_tipo_documento;  ?>&id=<?php echo trim($row_doc_bandeja['docg_id']); ?>&esta_id=<?php echo $row_doc_bandeja['esta_id'];?>" class="copyright"><?php echo("Revisar");?></a></td>
          </tr>
		 <?php
		    } //END WHILE
		   }
		   else  {
		 ?> 
		  <tr>
            <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
          </tr>
		  <?php }  ?>
        </table>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><span class="normalNegroNegrita"> Documentos en tr&aacute;nsito</span></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" height="25" border="0" align="center" cellpadding="2" cellspacing="2" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <?php
		//Documentos en transito 
		 if ($total_doc_transito>0) {
		 ?>
		  <tr class="td_gray" align="center" >
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha</td>
            <?if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
			<td class="normalNegroNegrita">Beneficiario</td>
			<td class="normalNegroNegrita">Tipo solicitud</td>
			<?php } if ($request_id_tipo_documento=="sopg") {?>
			<td class="normalNegroNegrita">Instancia actual</td>
			<?php } if ($request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
			<td class="normalNegroNegrita">Monto Bs.</td>
			<?} else if ($request_id_tipo_documento=="pcta" || $request_id_tipo_documento=="comp") {?>
			<td class="normalNegroNegrita">Asunto</td>
			<td class="normalNegroNegrita">Justificaci&oacute;n</td>	
			<td class="normalNegroNegrita">Instancia actual</td>			
	<?} else if ($request_id_tipo_documento=="pmod") {?>
			<td class="normalNegroNegrita">Motivo</td>
			<td class="normalNegroNegrita">Usuario</td>			
			
 	<?php }?> 	
        <!--      <td class="normalNegroNegrita">Estado</td>
            <td class="normalNegroNegrita">Prioridad</td>
  			<td class="normalNegroNegrita">Instancia actual</td>-->            
            <td class="normalNegroNegrita">Opciones</td>
          </tr>	  

<?$i=1; 
while($row_doc_transito=pg_fetch_array($resultado_doc_transito))  {
	$instancia_actual = "";
	$sql = " SELECT * FROM sai_buscar_cargo_depen('".$row_doc_transito['perf_id_act']."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$instancia_actual = $row["resultado"];
	}
	if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {
		$beneficiario_nombre = $row_doc_transito['nombre_beneficiario'];
		if (strlen($row_doc_transito['beneficiariov'])>2) $beneficiario_nombre = $row_doc_transito['beneficiariov'];
		$sol_nombre=$row_doc_transito['tipo_solicitud'];
		$detalle = $row_doc_transito['detalle'];
		$estado = $row_doc_transito['estado'];
  	 }
	//Buscar nombre de la prioridad
	$id_prioridad = $row_doc_transito['docg_prioridad'];			
	switch ($id_prioridad) {
	 case 1: 
	     $nombre_prioridad="Baja";
		 $campo_prioridad= "<div align='center' class='peqNegrita'>".$nombre_prioridad."</div>";
		 break;
             case 2: 
                 $nombre_prioridad="Media";
		 $campo_prioridad= "<div align='center' class='peqNegrita'>".$nombre_prioridad."</div>";
		 break;
	 case 3: 
	     $nombre_prioridad="Alta";
		 $campo_prioridad= "<div align='center' class='error'>".$nombre_prioridad."</div>";
		 break;
	}
	$ano=substr($row_doc_transito['fecha'],0,4);
	$mes=substr($row_doc_transito['fecha'],5,2);
	$dia=substr($row_doc_transito['fecha'],8,2);
	$fecha=$dia."-".$mes."-".$ano;
?>
    	<tr class="normal">
        	<td class="link"><a href="javascript:abrir_ventana('documentos/<? echo $request_id_tipo_documento;  ?>/<? echo $request_id_tipo_documento;  ?>_detalle.php?codigo=<?php echo trim($row_doc_transito['docg_id']); ?>&esta_id=<?php echo($row_doc_transito['esta_id']);?>')" class="copyright"><?php echo $row_doc_transito['docg_id'];?></a></td>
        	<td><?php echo $fecha;?></td>
              <?php if ($request_id_tipo_documento=="sopg" || $request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
            <td><?php echo $beneficiario_nombre;?></td>
            <td><?php echo $sol_nombre;?></td>
			<?php } if ($request_id_tipo_documento=="sopg") {?>
			 <td><?php echo  strtoupper($instancia_actual);?></td>
			<?php } if ($request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {?>
            <td align="right"><?php echo number_format($row_doc_transito['monto'],2,',','.');?></td>
            <?php } else if ($request_id_tipo_documento=="pcta" || $request_id_tipo_documento=="comp") {?>
			  <td><?php echo $row_doc_transito['asunto'];?></td>
            <td><?php echo $row_doc_transito['justificacion'];?></td>   
            <td><?php echo strtoupper($instancia_actual);?></td>                   
                 
                 <?php } else if ($request_id_tipo_documento=="pmod") {	?>
			  <td><?php echo $row_doc_transito['motivo'];?></td>
            <td><?php echo $row_doc_transito['usuario'];?></td>   
            <?}?>
            <!--<td><?php echo $estado;?></td>
			<td><?php echo $campo_prioridad;?></td>       
		 	<td><?php echo $instancia_actual;?></td>-->      
	        <td align="center">
			
			<a href="javascript:abrir_ventana('documentos/<? echo $request_id_tipo_documento;  ?>/<? echo $request_id_tipo_documento;  ?>_detalle.php?codigo=<?php echo trim($row_doc_transito['docg_id']); ?>&esta_id=<?php echo($row_doc_transito['esta_id']);?>')" class="copyright"><?php echo "Ver Detalle"; ?></a>
			<?		
			if (($user_perfil_id=="42450") && ($request_id_tipo_documento=="sopg")) {
		  		if ($row_doc_transito['wfob_id_ini']==2) {	
				//CAMBIAR LA PAGINA A DIRECCIONAR	
			?>
				<br>
				<a href="accion_documento.php?accion=2&tipo=<? echo $request_id_tipo_documento;  ?>&id=<?php echo trim($row_doc_transito['docg_id']); ?>&esta_id=<?php echo $row_doc_transito['estado'];?>" class="copyright"><?php echo "Modificar"; ?></a>
		<? 		}
			}
			else {
  				if (($row_doc_transito['wfob_id_ini']==2) && $request_id_tipo_documento!="comp" && trim($row_doc_transito['usua_login'])==trim($usuario) && ($row_doc_transito['esta_id']!=14) && $user_perfil_id==$row_doc_transito['perf_id_act']) {		
		?>
				<br>
				<a href="accion_documento.php?accion=2&tipo=<? echo $request_id_tipo_documento;  ?>&id=<?php echo trim($row_doc_transito['docg_id']); ?>&esta_id=<?php echo $row_doc_transito['estado'];?>" class="copyright"><?php echo "Modificar"; ?></a>
		<?  	}
			}
		if ($opcion_anular==1 && $request_id_tipo_documento!="comp") {
		  $perfil_doc=$row_doc_transito['perf_id_act'];

		 if (($request_id_tipo_documento=="sopg")||($request_id_tipo_documento=="pgch") ){
		 	if (($user_perfil_id==$perfil_doc) && $row_doc_transito['esta_id']==$estado_doc_dev){
                   echo "<br>
					<a href='anular_documento.php?tipo=".$request_id_tipo_documento."&id=".trim($row_doc_transito['docg_id'])."' class='copyright'>Anular</a>	";
		   }
		}
		else if ($request_id_tipo_documento!="tran" && $row_doc_transito['esta_id']==$estado_doc_dev && $user_perfil_id==$row_doc_transito['perf_id_act']) {	
			echo "<br>
			<a href='anular_documento.php?tipo=".$request_id_tipo_documento."&id=".trim($row_doc_transito['docg_id'])."' class='copyright'>Anular</a>	";
		}
	}
?>	
</td>
      </tr>
      <?php
	} }//END WHILE
		   else {
		 ?>
      <tr>
        <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
      </tr>
      <?php } ?>
    </table></td>
  </tr>
  
  
  
  
    
      
   <?php

   if (($request_id_tipo_documento=="pcta") && (($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)) ){?>   
   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><span class="normalNegroNegrita">Documentos Devueltos</span></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" height="25" border="0" align="center" cellpadding="2" cellspacing="2" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <?php
      $resultado_devueltos=pg_query($conexion,$pcta_devueltos) or die("Error al Mostrar Lista de Documentos Devueltos");
      $total_devueltos=pg_num_rows($resultado_devueltos);

		 if ($total_devueltos>0) {
		 ?>
		  <tr class="td_gray" align="center" >
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha Documento</td>
			<td class="normalNegroNegrita">Fecha Devoluci&oacute;n</td>
			<td class="normalNegroNegrita">Instancia actual</td>		
			<td class="normalNegroNegrita">Compromiso</td>
	        <td class="normalNegroNegrita">Opciones</td>
          </tr>	  

<?$i=1; 
while($row_doc_devueltos=pg_fetch_array($resultado_devueltos))  {
	$instancia_actual = "";
	$sql = " SELECT * FROM sai_buscar_cargo_depen('".$row_doc_devueltos['perf_id_act']."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$instancia_actual = $row["resultado"];
	}


	$ano=substr($row_doc_devueltos['docg_fecha'],0,4);
	$mes=substr($row_doc_devueltos['docg_fecha'],5,2);
	$dia=substr($row_doc_devueltos['docg_fecha'],8,2);
	$fecha=$dia."-".$mes."-".$ano;
	$anod=substr($row_doc_devueltos['revi_fecha'],0,4);
	$mesd=substr($row_doc_devueltos['revi_fecha'],5,2);
	$diad=substr($row_doc_devueltos['revi_fecha'],8,2);
	$fechad=$diad."-".$mesd."-".$anod;
?>
    	<tr class="normal">
        	<td class="link"><?php echo $row_doc_devueltos['docg_id'];?></td>
        	<td align="center"><?php echo $fecha;?></td>      
            <td align="center"><?php echo $fechad;?></td>
            <td><?php echo strtoupper($instancia_actual);?></td>        
            <td><?php echo $row_doc_devueltos['numero_reserva'];?></td>
                       
                 

		 	      
	        <td align="center">
			<a href="javascript:abrir_ventana('documentos/<? echo $request_id_tipo_documento;  ?>/<? echo $request_id_tipo_documento;  ?>_detalle.php?codigo=<?php echo trim($row_doc_devueltos['docg_id']); ?>&esta_id=<?php echo($row_doc_devueltos['esta_id']);?>')" class="copyright"><?php echo "Ver Detalle"; ?></a>

</td>
      </tr>
      <?php
	} }//END WHILE
		   else {
		 ?>
      <tr>
        <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
      </tr>
      <?php } 
      
	?>
	 </table></td>
  </tr>
	<?php 	   }?>
     
  
    
     <?php /* if (($request_id_tipo_documento=="sopg") && ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS)) {?>   
   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><span class="normalNegroNegrita">&Oacute;rdenes de compra</span></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" height="25" border="0" align="center" cellpadding="2" cellspacing="2" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <?php
     //echo $ordc;
      $resultado_ordc=pg_query($conexion,$ordc) or die("Error al Mostrar Lista de Documentos Anulados");
      $total_ordc=pg_num_rows($resultado_ordc);

		 if ($total_ordc>0) {
		 ?>
		  <tr class="td_gray" align="center" >
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha Documento</td>
			<td class="normalNegroNegrita">RIF Beneficiario</td>
			<td class="normalNegroNegrita">Nombre Beneficiario</td>
			<td class="normalNegroNegrita">Justificaci&oacute;n</td>		
	        <td class="normalNegroNegrita">Opciones</td>
          </tr>	  

<?$i=1; 
while($row_ordc=pg_fetch_array($resultado_ordc))  {

?>
   	<tr class="normal">
        	<td class="link"><?php echo $row_ordc['docg_id'];?></td>
        	<td align="center"><?php echo $row_ordc['fecha'];?></td>      
        	<td><?php echo $row_ordc['ci_rif'];?></td>
            <td ><?php echo $row_ordc['nombre_beneficiario'];?></td>
             <td><?php echo $row_ordc['detalle'];?></td>
	        <td align="center">
			<a href="documentos/sopg/sopg_v1.php?doc=<?=$row_ordc['docg_id'];?>" class="copyright"><?php echo "Ver Detalle"; ?></a>

</td>
      </tr> 
      <?php
	} }//END WHILE
		   else {
		 ?>
      <tr>
        <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen ordenes de compra en bandeja</div></td>
      </tr>
      <?php } 
      
	?>
	 </table></td>
  </tr>
	<?php 	   } */?>
  
  
     <?php if (($request_id_tipo_documento=="pcta") && ((($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO))) ) {?>   
   <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center"><span class="normalNegroNegrita">Documentos Anulados</span></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" height="25" border="0" align="center" cellpadding="2" cellspacing="2" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <?php
      $resultado_anulados=pg_query($conexion,$pcta_anulados) or die("Error al Mostrar Lista de Documentos Anulados");
      $total_devueltos=pg_num_rows($resultado_devueltos);

		 if ($total_devueltos>0) {
		 ?>
		  <tr class="td_gray" align="center" >
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha Documento</td>
			<td class="normalNegroNegrita">Asunto</td>
			<td class="normalNegroNegrita">Fecha Anulaci&oacute;n</td>		
	        <td class="normalNegroNegrita">Opciones</td>
          </tr>	  

<?$i=1; 
while($row_doc_anulados=pg_fetch_array($resultado_anulados))  {
	/*$instancia_actual = "";
	$sql = " SELECT * FROM sai_buscar_cargo_depen('".$row_doc_devueltos['perf_id_act']."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$instancia_actual = $row["resultado"];
	}*/


	$ano=substr($row_doc_anulados['memo_fecha_crea'],0,4);
	$mes=substr($row_doc_anulados['memo_fecha_crea'],5,2);
	$dia=substr($row_doc_anulados['memo_fecha_crea'],8,2);
	$fecha=$dia."-".$mes."-".$ano;
	$anoe=substr($row_doc_anulados['pcta_fecha'],0,4);
	$mese=substr($row_doc_anulados['pcta_fecha'],5,2);
	$diae=substr($row_doc_anulados['pcta_fecha'],8,2);
	$fechae=$diae."-".$mese."-".$anoe;
?>
    	<tr class="normal">
        	<td class="link"><?php echo $row_doc_anulados['doso_doc_fuente'];?></td>
        	<td align="center"><?php echo $fechae;?></td>      
        	<td><?php echo $row_doc_anulados['pcas_nombre'];?></td>
            <td align="center"><?php echo $fecha;?></td>
	        <td align="center">
			<a href="javascript:abrir_ventana('documentos/<? echo $request_id_tipo_documento;  ?>/<? echo $request_id_tipo_documento;  ?>_detalle.php?codigo=<?php echo trim($row_doc_anulados['doso_doc_fuente']); ?>')" class="copyright"><?php echo "Ver Detalle"; ?></a>

</td>
      </tr>
      <?php
	} }//END WHILE
		   else {
		 ?>
      <tr>
        <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
      </tr>
      <?php } 
      
	?>
	 </table></td>
  </tr>
	<?php 	   }?>
  
  
  
</table>
</body>
</html>
<?php pg_close($conexion);?>