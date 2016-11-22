<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
?>
<html>
<head>
<title>.:SAFI:. Comprobante diario manual</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" 
 rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<body>
<form name="formCodi" id="formCodi" method="post" action="">
 <table style="width: 100%; align:center; background-image: url('../../imagenes/fondo_tabla.gif')" class="tablaalertas" >
    <tr class="td_gray">
      <th class="header" colspan='5' ><span class="normalNegroNegrita">Anular comprobante diario codi-</span></th>
    </tr>
	<?php
	if(is_array($GLOBALS['SafiRequestVars']['listaCodi'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodi'] as $listaCodi) { 
		$idCodi = $listaCodi['comp_id'];
	?>   
    <tr>
      <td class="normalNegrita">Documento asociado: <?php echo $listaCodi['comp_id'];?></td>
      <td class="normalNegrita" colspan="4">Nro. Referencia bancaria: <?php echo $listaCodi['nro_referencia'];?></td>
    </tr>
    <tr>
	<td class="normalNegrita">Fuente de financiamiento: <?php echo $listaCodi['fuente_financiamiento'];?></td>
    <td width="81" class="normalNegrita" colspan="4">Nro. compromiso:<?php echo $listaCodi['nro_compromiso'];?></td>
    </tr>
    <tr>
    <td colspan="5" class="normalNegrita">Justificaci&oacute;n: <?php echo $listaCodi['comp_comen'];?></td>
    </tr>    
	<?php 
		}
	}
	?>

<tr>
<td colspan="5" class="normalNegroNegrita">Registro contable</td>
</tr>    
	<tr>
	<td class="normalNegrita">Nro. Registro</td>
	<td class="normalNegrita">Cuenta contable</td>	
	<td class="normalNegrita">Descripci&oacute;n</td>	
	<td class="normalNegrita">Debe</td>
	<td class="normalNegrita">Haber</td>		
	</tr>
	<?php
	if(is_array($GLOBALS['SafiRequestVars']['listaCodiDetalle'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiDetalle'] as $listaCodi) {
		foreach ($listaCodi as $listaCodi2) {
			$total = $listaCodi2['rcomp_tot_db'];
?>   	
	<tr>
	<td class="normalNegrita"><?php echo $listaCodi2['reng_comp'];?></td>
	<td class="normalNegrita"> <?php echo $listaCodi2['cpat_id'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['cpat_nombre'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['rcomp_debe'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['rcomp_haber'];?></td>
	</tr>
<?php 
		}
	}
	}
	?>
	<tr>
	<td colspan="3" align="right">Total:</td>
	<td colspan="2"><?php echo $total;?></td>	
	</tr>

<tr>
<td colspan="5" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria</td>
</tr>
<tr>
<td class="normalNegrita">Proyecto/Acc</td>
<td class="normalNegrita">Acci&oacute;n espec&iacute;fica</td>
<td class="normalNegrita">Partida</td>
<td class="normalNegrita">Cuenta</td>
<td class="normalNegrita">Monto</td>
</tr>
<?php 
if(is_array($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] as $listaCodi) {
		foreach ($listaCodi as $listaCodi2) {?>
	<tr>
	<td class="normalNegrita"><?php echo $listaCodi2['centro_gestor'];?></td>
	<td class="normalNegrita"> <?php echo $listaCodi2['centro_costo'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['part_id'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['cpat_id'];?></td>
	<td class="normalNegrita"><?php echo $listaCodi2['cadt_monto'];?></td>
	</tr>		

<?php 
		}
	}
	}
	?>		
<tr> 
	<td colspan="5" class="normalNegrita"><b>Motivo anulaci&oacute;n:</b> 
		<textarea name="motivo" cols="90" rows="4" class="normalNegro" 
		></textarea>
		<input type="hidden" value="<?php echo $idCodi;?>" id="idCodi" name="idCodi"/>
	</td>
  </tr>
  <tr>
  <td colspan="5" align="center"><input type="button" value="Anular" onClick="javascript:anularAccion(this.formCodi.motivo.value)"/> </td>
  </tr>
 </table>
</form>
</body>
</html>