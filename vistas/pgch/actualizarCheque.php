<?php
include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
?>
<!DOCTYPE html>
<html> 
<head>
	<title>SAFI</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/funciones.js';?>"></script>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/actb.js';?>"></script>
	<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
	<?php require(SAFI_INCLUDE_PATH.'/fechaJs.php'); ?>
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/pgch.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post">
<div class="normalNegrita">
<?php echo $GLOBALS['SafiRequestVars']['mensaje']."<br/><br/>";?>
</div>
  <table class="tablaPequena">
<tr> 
  <th colspan="2" class="normalNegroNegrita">Actualizar beneficiario cheque</th>
</tr>
<tr>
	
	 <td class="normalNegrita">Beneficiario:</td>
  <td>
  <input type="text" name="beneficiario" id="beneficiario" class="normalNegro" size="70" autocomplete="off"/>
<?php 	
				$arregloProveedores = "";
				$cedulasProveedores = "";
				$nombresProveedores = "";
				$indice=0;
				/**************************/
				foreach ($GLOBALS['SafiRequestVars']['beneficiarios'] As $idBeneficiario => $beneficiario){
					$arregloProveedores .= "'".$idBeneficiario." : ".strtoupper($beneficiario->GetNombres()).' '.strtoupper($beneficiario->GetApellidos())."',";
					$cedulasProveedores .= "'".$idBeneficiario."',";
					$nombresProveedores .= "'".str_replace("\n"," ",$beneficiario->GetNombres().' '.$beneficiario->GetApellidos())."',";
					$indice++;
					
				}
				/*************************/
				$arregloProveedores = substr($arregloProveedores, 0, -1);
				$cedulasProveedores = substr($cedulasProveedores, 0, -1);
				$nombresProveedores = substr($nombresProveedores, 0, -1);
			?>
				<script>			
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					actb(document.getElementById('beneficiario'),proveedor);
				</script>	  	
  	
  	<!-- Fin Nuevo -->
  </td>
</tr>

<tr>
<td class="normal" colspan="2">	<b>Nota:</b> Se actualizar&aacute; nombre y apellido/raz&oacute;n social del beneficiario/proveedor. La CI/RIF no ser&aacute; actualizada.</td>
</tr>
<tr align="center">
		<td colspan="2" class="normal" align="center">
			<input type="button" value="Buscar" onClick="buscarBeneficiario()"/>
		</td>
</tr>	
</table>
<br/>

 <div class="normalNegroNegrita" align="center"> Beneficiario: <?php echo $GLOBALS['SafiRequestVars']['nombreBeneficiario'];?>
 <input type="hidden" name="beneficiarioBusqueda" id="beneficiarioBusqueda" value="<?php echo $GLOBALS['SafiRequestVars']['nombreBeneficiario'];?>"/>
 </div>
	  <table class="tablaPequena">
	   <tr class="td_gray">
	       <th class="normalNegroNegrita">#</th>
		   <th class="normalNegroNegrita">Nro. cheque</th>
		   <th class="normalNegroNegrita">Documento asociado</th>
		   <th class="normalNegroNegrita">Fecha</th>
		   <th class="normalNegroNegrita">Monto</th>
		  </tr> 		   		   		   
		<?php
			$i = 0; 
			foreach ($GLOBALS['SafiRequestVars']['cheques'] as $cheques) {
			$i++;
		?>			
		<tr class="normalNegro">
			<td><input type="checkbox" name="idCheque[]" id="idCheque[]" value="<?php echo $cheques->GetId()?>"></td>		
			<td><?php echo $cheques->GetNumero();?>
			<input type="hidden" name="numeroCheque" id="numeroCheque" value="<?php echo $cheques->GetNumero()?>"/>			
			</td>
			<td><?php echo $cheques->GetIdDocumento();?></td>
			<td><?php echo $cheques->GetFechaEmision();?></td>
			<td><?php echo number_format($cheques->GetMonto(),2,',','.');?></td>
		</tr>	
		<?php }	
		if ($i>0) {
		?>
		<tr>
		<td colspan="5" align="center"><input type="button" value="Actualizar" onClick="actualizarBeneficiario()"/> </td>
		</tr>		
		<?php }?>	  
	</table>
</form>	
</body>
</html>


