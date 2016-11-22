<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SAFI: Gestion de pagos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" type="text/JavaScript">
function validar() {  
	if (document.form1.palabra.value=="") {
		alert ("Debe escribir una palabra clave del proveedor");
		return;
	}
	else { 
		document.form1.submit();
	}
}
function irPdf() {
	document.form1.action="buscarConvertidorPDF.php";
	document.form1.submit();
}
</script>	
</head>
<body>
<br />
<br />
<form name="form1" method="post" action="reporteTransferencias.php">
<table width="70%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td class="normalNegroNegrita" colspan="2">Gesti&oacute;n de pagos</td>
    </tr>
    <tr>
	 <td class="normalNegrita">Beneficiario: </td>
    	<td><input type="text" value="COMUNAL" name="palabra" id="palabra"/></td>    
  </tr>
<tr> 
<td align="center" colspan="2">
<input type="button" value="Buscar" onclick="javascript:validar();"/>
</td>
    </tr>
  </table>	
</form>
<?	
if (isset($_POST["palabra"])){ 
	$palabra = $_POST["palabra"];
}
else $palabra = "COMUNAL";
	$sql = "select comp.pcta_id as pcta, comp.comp_id as comp, nucleo.sopg_id as sopg, to_char(nucleo.fecha,'DD/MM/YYYY') as fecha, nucleo.rif as rif, upper(nucleo.proveedor) as proveedor, coalesce(pcta.pcta_monto_solicitado,0) as monto_acordado,nucleo.monto as monto_transferido,coalesce(pcta.pcta_monto_solicitado,0)-nucleo.monto as resta, upper(comp.comp_observacion) as observacion, nucleo.codi, nucleo.nro_compromiso, ts.nombre_sol as solicitud
	from (select s.sopg_fecha as fecha, s.sopg_bene_ci_rif as rif, pr.prov_nombre as proveedor, s.sopg_monto as monto, s.sopg_id as sopg_id, s.comp_id as comp_id, co.comp_id as codi, co.nro_compromiso, s.sopg_tp_solicitud as tipo_solicitud
	from sai_sol_pago s, sai_proveedor_nuevo pr, sai_mov_cta_banco ctb, sai_comp_diario cdi, sai_codi co
	where ctb.docg_id=s.sopg_id and s.esta_id<>15 and s.sopg_bene_ci_rif=pr.prov_id_rif and upper(pr.prov_nombre) like upper('%".$palabra."%') and s.sopg_id=cdi.comp_doc_id and cdi.comp_id like 'codi%' and cdi.esta_id<>15 and cdi.comp_id=co.comp_id
	) as nucleo
	left outer join sai_comp comp on (nucleo.comp_id=comp.comp_id or nucleo.nro_compromiso=comp.comp_id)
	left outer join sai_pcuenta pcta on (comp.pcta_id=pcta.pcta_id)
	left outer join sai_tipo_solicitud ts on (ts.id_sol=nucleo.tipo_solicitud)

	union

	select comp.pcta_id as pcta, comp.comp_id as comp, nucleo.sopg_id as sopg, to_char(nucleo.fecha,'DD/MM/YYYY') as fecha, nucleo.rif as rif, upper(nucleo.proveedor) as proveedor, coalesce(pcta.pcta_monto_solicitado,0) as monto_acordado,nucleo.monto as monto_transferido,coalesce(pcta.pcta_monto_solicitado,0)-nucleo.monto as resta, upper(comp.comp_observacion) as observacion, nucleo.codi, nucleo.nro_compromiso,ts.nombre_sol as solicitud
	from (select s.sopg_fecha as fecha, s.sopg_bene_ci_rif as rif, pr.prov_nombre as proveedor, s.sopg_monto as monto, s.sopg_id as sopg_id, s.comp_id as comp_id, '' as codi, '' as nro_compromiso, s.sopg_tp_solicitud as tipo_solicitud
	from sai_sol_pago s, sai_proveedor_nuevo pr, sai_mov_cta_banco ctb
	where ctb.docg_id=s.sopg_id and s.esta_id<>15 and s.sopg_bene_ci_rif=pr.prov_id_rif and upper(pr.prov_nombre) like upper('%".$palabra."%') and s.sopg_id not in (select coalesce(comp_doc_id,'') from sai_comp_diario where comp_id like 'codi%' and esta_id<>15 )
	) as nucleo
	left outer join sai_comp comp on (nucleo.comp_id=comp.comp_id)
	left outer join sai_pcuenta pcta on (comp.pcta_id=pcta.pcta_id)
	left outer join sai_tipo_solicitud ts on (ts.id_sol=nucleo.tipo_solicitud)
	";
	$resultado_set=pg_query($conexion,$sql);
?>
<br/> <br />
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td align="center" class="normalNegrita"><div align="center">Pcta </div></td>
      <td align="center" class="normalNegrita"><div align="center">Sopg </div></td>
  <td align="center" class="normalNegrita"><div align="center">Tipo Solicitud </div></td>      
      <td align="center" class="normalNegrita"><div align="center">Comp_sopg </div></td>
      <td align="center" class="normalNegrita"><div align="center">Codi </div></td>
       <td align="center" class="normalNegrita"><div align="center">Comp Codi </div></td>
       <td align="center" class="normalNegrita"><div align="center">Fecha </div></td>
       <td align="center" class="normalNegrita"><div align="center">RIF </div></td>
       <td align="center" class="normalNegrita"><div align="center">Proveedor </div></td>
       <td align="center" class="normalNegrita"><div align="center">Monto PCTA </div></td>
       <td align="center" class="normalNegrita"><div align="center">Monto Pago </div></td>       
       <td align="center" class="normalNegrita"><div align="center">Resta </div></td>                                   
       <td align="center" class="normalNegrita"><div align="center">Observaciones </div></td>
  </tr>
  <?php while ($row=pg_fetch_array($resultado_set))  {?>
<tr class="normal">
    <td><?echo $row['pcta'];;?></td>
      <td><?echo $row['sopg'];?></td>
      <td><?echo $row['solicitud'];?></td>      
      <td><?echo $row['comp'];?></td>
      <td><?echo $row['codi'];?></td>
      <td><?echo $row['nro_compromiso'];?></td>
      <td><?echo $row['fecha'];?></td>
      <td><?echo $row['rif'];?></td>
      <td><?echo $row['proveedor'];?></td>
      <td><?echo $row['monto_acordado'];?></td>
      <td><?echo $row['monto_transferido'];?></td>
      <td><?echo $row['resta'];?></td> 
      <td><?echo $row['observacion'];?></td>                                         
</tr>
<?php } pg_close($conexion);?>
</table>
<br>
<div align="center">
<!-- <input type="button" onClick="javascript:irPdf();" value="PDF"/>-->
</div>
</body>
</html>