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

//Buscar datos del pago con cheque
$codigo=trim($_REQUEST['codigo']);
$sql="select s.sopg_monto as sopg_monto, pch.pres_anno_docg as ano, d.depe_nombre as nombre_dependencia, pch.pgch_fecha as fecha_pgch, pch.nro_cuenta as numero_cuenta, pch.docg_id as sopg, pch.pgch_asunto as asunto, pch.pgch_obs as observaciones, b.banc_nombre as nombre_banco,  upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante, em.empl_email as email, em.empl_tlf_ofic as telefono, ch.nro_cheque as numero_cheque, ch.monto_cheque as monto_cheque, upper(ch.beneficiario_cheque) as beneficiario_cheque, t.nombre_sol as tipo_solicitud, ch.id_cheque as id_cheque, ctb.cpat_id as cpat_id, ch.estatus_cheque as estatus_cheque
from sai_pago_cheque pch, sai_cheque ch, sai_banco b, sai_dependenci d, sai_ctabanco ctb, sai_doc_genera dg, sai_empleado em, sai_tipo_solicitud t, sai_sol_pago s
where ch.estatus_cheque<>15 and s.sopg_id=pch.docg_id and s.sopg_tp_solicitud=t.id_sol and pch.docg_id=ch.docg_id and pch.depe_id=d.depe_id and pch.nro_cuenta=ctb.ctab_numero and ctb.banc_id=b.banc_id and pch.depe_id=d.depe_id and dg.docg_id=pch.pgch_id and dg.usua_login=em.empl_cedula and pch.pgch_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
	$dependencia_solicitante = trim($row['nombre_dependencia']);
	$fecha_pgch = trim($row['fecha_pgch']);
	$fecha_cheque = trim($row['fechaemision_cheque']);	
	$numero_cuenta = trim($row['numero_cuenta']);
	$id_cheque = trim($row['id_cheque']);
	$cpat_id_banco= trim($row['cpat_id']);
	$cheque_estado=trim($row['estatus_cheque']);
	$numero_cheque = trim($row['numero_cheque']);
	$sopg = trim($row['sopg']);	
	$tipo_solicitud=trim($row['tipo_solicitud']);		
	$asunto = trim($row['asunto']);
	$usuario_solicitante = trim($row['usuario_solicitante']);
    $observaciones=trim($row['observaciones']);
	$email_solicitante=trim($row['email']);
	$telefono_solicitante= trim($row['telefono']);
	$nombre_banco = trim($row['nombre_banco']);
	$monto_cheque_float = $row['monto_cheque'];
	$monto_cheque = number_format(trim($row['monto_cheque']),2,',','.');
	$beneficiario_cheque = trim($row['beneficiario_cheque']);
	$anno_id_doc_imputacion = trim($row['anno']);
	//Buscar tipo de doc principal
	$tipo_doc = substr($pgch_docg_id,0,4);	
}			
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI: Pago con Cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/func_montletra.js"> </script>
<script language="JavaScript" type="text/JavaScript">
function imprimir() {
	if (confirm('Prepare el cheque correspondiente en la impresora')) {
		window.open('cheque_pdf.php?codigo=<?php echo trim($codigo); ?>','chequep','height=20,width=1,left=6000,top=8000,scrollbars=yes');
		window.document.location.href = "pgch_cheque.php?codigo=<?php echo trim($codigo); ?>&imp=1";
	}
}
function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');
  	ver_monto_letra(monto ,'txt_monto_letras','');
}

</script>
</head>
<body onload="monto_en_letras(<? echo $monto_cheque;?>)">
<form name="form" method="post" action="">
  <table width="700" border="0">
    <tr>
      <td><table width="100%" class="tablaalertas" border="0" background="../../imagenes/fondo_tabla.gif">
        <tr>
          <td width="38%" class="normal"><div align="center">C&Oacute;DIGO CUENTA CLIENTE </div></td>
          <td width="15%">&nbsp;</td>
          <td width="16%" class="normal"><div align="center">CHEQUE N&ordm; </div></td>
          <td width="31%">&nbsp;</td>
        </tr>
        <tr>
          <td><div align="center"><span class="normalNegrita"><?php echo $numero_cuenta;?></span></div></td>
          <td>&nbsp;</td>
          <td><div align="center"><span class="normalNegrita"><?php echo $numero_cheque;?></span></div></td>
          <td class="normal">Bs. <span class="normalNegrita"><?php echo $monto_cheque; ?></span></td>
        </tr>
        <tr>
          <td class="normalNegrita">FUNDACI&Oacute;N INFOCENTRO </td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" class="normal">P&Aacute;GUESE A LA ORDEN DE: <span class="normalNegrita"><?php echo $beneficiario_cheque;?></span></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><span class="normal">LA SUMA DE: <label id="label_monto_letras"></label></span> <span class="normal"><span class="normalNegrita">
           <span class="normalNegrita">
		   <?php 
		   include("../../includes/monto_a_letra.php");
		   $monto_en_letras = monto_letra($monto_cheque_float, " BOLIVARES");
		   echo $monto_en_letras;
		   ?></span>
          </span></span> </td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" class="normalNegrita"> <?php echo $nombre_banco;?></td>
          <td>&nbsp;</td>
          <td class="normal"><div align="center">FIRMA AUTORIZADA </div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><table width="100%" border="0" class="tablaCentral">
        <tr>
          <td colspan="2"><span class="normalNegrita">
		  FUNDACI&Oacute;N INFOCENTRO
		  <br/>
		  <?php echo $nombre_banco." - ".$numero_cuenta;?>
		  <br/>
		  EJERCICIO FISCAL <?php echo date("Y");?>		  
		  </span></td>
          <td width="25%" class="normalNegrita"><div align="center">COMPROBANTE DE EGRESO </div></td>
          <td width="25%" class="normalNegrita">N&ordm; <?php echo $codigo;?> </td>
        </tr>
        <tr>
          <td width="25%">&nbsp;</td>
          <td width="25%">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		
        <tr>
          <td colspan="4"><table width="100%" class="tablaalertas" border="0" background="../../imagenes/fondo_tabla.gif" align="center">
            <tr class="normal">
              <td><div align="center">CONTABILIDAD</div></td>
              <td><div align="center">DETALLE</div></td>
              <td><div align="center">D&Eacute;BITO</div></td>
			  <td><div align="center">CR&Eacute;DITO</div></td>
          </tr>
            <?php

/*Buscar cta pasivo y cta transitoria*/
	$sql="select * from sai_sol_pago_imputa where sopg_id='".$sopg."'";
	$resultado_proc = pg_query($conexion,$sql) or die("Error al mostrar partidas");
	if ($row_pc = pg_fetch_array($resultado_proc)) {
		$part_id = $row_pc["sopg_sub_espe"];
	}

	$sql="select cpat_pasivo_id, cpat_transitoria_id from sai_convertidor where part_id='".$part_id."'";
	$resultado_proc = pg_query($conexion,$sql) or die("Error al mostrar partidas");
	if ($row_pc = pg_fetch_array($resultado_proc)) {
		$cpat_id_prov = $row_pc["cpat_pasivo_id"];
		$cpat_id_transitoria = $row_pc["cpat_transitoria_id"];
	}
	$sql="select cpat_nombre from sai_cue_pat where cpat_id = '".$cpat_id_prov."'";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");	
	if ($row = pg_fetch_array($resultado)) {
		$cpat_nombre_prov = $row["cpat_nombre"];			
	}
?>
		<tr><td colspan="4">&nbsp;</td></tr>
            <tr>
            	 <td align="left" class="normal"><?php echo $cpat_id_prov?></td>
          	 <td width="338" valign="top" class="normal"><div align="justify"><?php echo $cpat_nombre_prov?></div>            </td>
         		 <td valign="top" class="normal"><?php echo $monto_cheque; ?></td>
         		 <td colspan="7" align="right" valign="top" class="normal"><?php echo number_format("0",2,'.',',')?></td>
            </tr>
		<tr><td colspan="4">&nbsp;</td></tr>
			<tr>
            	 <td align="left" class="normal"><?php echo $cpat_id_banco;?></td>
          		<td width="338" valign="top" class="normal"><div align="justify"><?php echo $nombre_banco?></div>            </td>
         		 <td valign="top" class="normal"><?php echo number_format("0",2,'.',',')?></td>
         		 <td colspan="7" align="right" valign="top" class="normal"><?php echo $monto_cheque; ?></td>
            </tr>
			<tr>
              <td class="2">&nbsp;</td>
              <td colspan="3" class="normal"><div align="right">_________________________</div></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><div align="right" class="normalNegrita">Diferencia Bs. </div></td>
              <td colspan="1" class="normal"><?php //echo number_format($total_db,2,'.',',');?></td>
			  <td colspan="1" class="normal"><?php echo number_format($total_haber,2,'.',',');?></td>
            </tr>
          </table></td>
          </tr>
<tr>
<td colspan="4">
<table width="100%" class="tablaalertas" border="0" background="../../imagenes/fondo_tabla.gif" align="center">
            <tr class="normal">
              <td><div align="center">MOTIVO</div></td>
          </tr>
            <tr>
            	 <td align="left" class="normal"><? echo $asunto;?></td>
            </tr>
</table></td>
</tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><table width="100%" class="tablaalertas">
            <tr>
              <td width="31%" valign="top" class="normalNegrita">CHEQUE N&ordm; <span class="normal">
			  <br/><?php echo $numero_cheque;?></span></td>
              <td width="35%" valign="top" class="normalNegrita">BANCO <span class="normal">
			  <br/>
			  <?php echo $nombre_banco;?></span></td>
            </tr>
          </table></td>
          <td colspan="2"><table width="100%" border="0" class="tablaalertas">
            <tr>
              <td colspan="2" class="td_gray"><span class="normalNegrita" >FIRMA Y SELLO DEL BENEFICIARIO </span></td>
          </tr>
            <tr>
              <td height="60" colspan="2">&nbsp;</td>
              </tr>
        <tr>
          <td width="22%"><span class="normalNegrita" >C.I./RIF.</span></td>
          <td width="78%"><span class="normal"><?php echo $beneficiario_cheque;?></span></td>
            </tr>
          </table></td>
          </tr>
         <tr>
          <td class="normalNegrita">ELABORADO POR:</td>
          <td class="normalNegrita">REVISADO:</td>
          <td class="normalNegrita">APROBADO:</td>
          <td class="normalNegrita">CONTABILIZADO:</td>
        </tr>
        <tr>
          <td height="30" class="normalNegrita">&nbsp;</td>
          <td height="30" class="normalNegrita">&nbsp;</td>
          <td height="30" class="normalNegrita">&nbsp;</td>
          <td height="30" class="normalNegrita">&nbsp;</td>
        </tr>
        
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><div align="center">
	  <a href="javascript:window.print()"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" alt="Imprimir Detalle"></a>
<?
	$sql="select wfob_id_ini from sai_doc_genera where docg_id='".$codigo."'";
	$resultado_s = pg_query($conexion,$sql) or die("Error al mostrar documento");
	if ($row_pc = pg_fetch_array($resultado_s)) {
		$objeto = $row_pc["wfob_id_ini"];
	}

 if ($cheque_estado==44 && $objeto==99) {
 ?>
  <a href="javascript:imprimir()" class="normal" alt="Generar cheque">Generar Cheque</a>
<?}?>
	<br><br>
	  </div></td>
    </tr>
  </table>
</form>
</body>
</html>
<?php pg_close($conexion);?>