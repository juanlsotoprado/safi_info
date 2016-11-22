<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD);
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
	<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/reportesContabilidad.js'?>"></script>
</head>

<body>
<form name="form" id="form" action="" method="post">
	<table class="tablaPequena">
		<tr> 
		  <th colspan="2">Movimientos bancarios/presupuestarios</th>
		</tr>
		<tr>
			<td class="normalNegrita">Cuenta bancaria:</td>
			<td>
				<select name="cuenta" class="normal" id="cuenta">
			 		<option value="-1" selected="selected">Seleccione</option>
 					<?php
 						$selected = "";
  						if(is_array($GLOBALS['SafiRequestVars']['cuentasBancarias'])){
	  						foreach ($GLOBALS['SafiRequestVars']['cuentasBancarias'] AS $cuentaBancaria){
	  							echo '
	  								<option value="'.$cuentaBancaria->GetId().'".$selected.">'.$cuentaBancaria->GetId()." - ".$cuentaBancaria->GetDescripcion().'</option>';
	  						}
  						}
  					?>			 
 				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Fecha:</td>
			<td class="normalNegrita">
				<!-- <table>
				<tr>
				<td>
				<div id="fecha" class="normal"  style="display:none">-->
					<input type="text" size="10" id="fecha_inicio" name="fecha_inicio" class="dateparse" readonly="readonly" value="01/01/<?php echo date("Y");?>"/>
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_inicio');" 	title="Show popup calendar" >
						<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
					</a>
				<!-- </div>-->	

					<input type="text" size="10" id="fecha_fin" name="fecha_fin" class="dateparse"	readonly="readonly"/>
					<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha_fin');" title="Show popup calendar" >
						<img src="<?=GetConfig("siteURL");?>/js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
					</a>

					<!-- </td>
					</tr>
					</table>-->	
			</td>
		</tr>		
		
		<tr>
			<td class="normalNegrita">Tipo de reporte:</td>
			<td class="normalNegrita">
			Causado: <input type="radio" name="reporte" id="reporte" value="C" onClick="javascript:deshabilitar_combo(1)"></input> 
			Pagado: <input type="radio" name="reporte" id="reporte" value="P" onClick="javascript:deshabilitar_combo(2)"></input>
			</td>
		</tr>
		<tr class="normalNegrita">
			<td>&nbsp;</td>
			<td>
				<div id="seleccion_causado" class="normal"  style="display:none">
					Causado: <input type="radio" name="estado" id="estado" value="C" checked></input>
				</div>
				<div id="seleccion_pagado" class="normal"  style="display:none">
					En tr&aacute;nsito: <input type="radio" name="estado" id="estado" value="Tr"></input> 
					Conciliado: <input type="radio" name="estado" id="estado" value="Co"></input>
					Todos: <input type="radio" name="estado" id="estado" value="To"></input>
				</div>
			</td>
		</tr>
		
		<tr align="center">
			<td colspan="2">
				<input type="hidden" value="0" name="hid_validar" value="0"/>	
				<input type="button" value="Buscar" onclick="ejecutar()" /> 
			</td>
		</tr> 
	</table>
</form>

<br/>
<div class="normalNegroNegrita" align="center">
	Movimientos <?php echo $GLOBALS['SafiRequestVars']['descripcion_tipo'] . $GLOBALS['SafiRequestVars']['descripcion_fecha'];?> 
</div>
<table class="tabla">
	<tr>
		<th class="normalNegroNegrita" align="center">Fecha Factura</th>
		<th class="normalNegroNegrita" align="center">Factura</th>
		<th class="normalNegroNegrita" align="center">Tipo Sol.</th>
		<th class="normalNegroNegrita" align="center">Nro. Documento </th>
		<th class="normalNegroNegrita" align="center">Nro. Asiento </th>					
		<th class="normalNegroNegrita" align="center">Nro. Reserva  </th>
		<th class="normalNegroNegrita" align="center">Beneficiario </th>
		<th class="normalNegroNegrita" align="center">Proy/Acc </th>
		<th class="normalNegroNegrita" align="center">Gasto</th>
		<th class="normalNegroNegrita" align="center">Fecha Cheque</th>
		<th class="normalNegroNegrita" align="center">Ref. </th>
		<th class="normalNegroNegrita" align="center">Monto Bs. </th>
		<th class="normalNegroNegrita" align="center">Partida/Cuenta  </th>
		<th class="normalNegroNegrita" align="center">Timbre </th>
		<th class="normalNegroNegrita" align="center">IVA </th>
		<th class="normalNegroNegrita" align="center">ISLR </th>
		<th class="normalNegroNegrita" align="center">Fianza</th>
     </tr>
     <?php
     foreach ($GLOBALS['SafiRequestVars']['listaMovimientos'] as $listaMovimiento) {
     ?>
     <tr class="normal" align="left">
		<td><?php echo $listaMovimiento["fecha_sopg"] ;?></td>
		<td><?php echo $listaMovimiento["factura"] ;?></td>
		<td><?php echo $listaMovimiento["tipo_solicitud"] ;?></td>
		<td>
		<?php echo $listaMovimiento["codigo_sopg"] ;	?>	</td>
		<td><?php echo $listaMovimiento["asiento"] ;?></td>
		<td><?php echo $listaMovimiento["reserva"]."-".$listaMovimiento["compromiso"] ;?></td>
		<td><?php echo $listaMovimiento["beneficiario"];?></td>
		<td><?php echo $listaMovimiento["proy_acc"] ;?></td>
		<td><?php echo number_format($listaMovimiento["monto_causado"],2,',','.');?></td>
		<td><?php echo $listaMovimiento["fecha_emision_pago"] ;?></td>
		<td><?php echo $listaMovimiento["referencia"] ;?></td>
		<td><?php echo number_format($listaMovimiento["monto"],2,',','.') ;?></td>				
		<td><?php echo $listaMovimiento["cuenta"] ;?></td>
		<td><?php echo number_format($listaMovimiento["rltf"],2,',','.') ;?></td>
		<td><?php echo number_format( $listaMovimiento["riva"],2,',','.') ;?></td>
		<td><?php echo number_format( $listaMovimiento["rislr"],2,',','.') ;?></td>				
		<td><?php echo number_format($listaMovimiento["rfza"],2,',','.') ;?></td>		
						
	</tr>
    <?php }?>
	<tr class="normalNegroNegrita">
		<td colspan="8"><b>Totales (exceptuando partidas 4.11.0)</b></td>
		<td>&nbsp;<b><?=number_format($listaMovimiento["sum_montoP"],2,',','.');?></b></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><b><?=number_format($listaMovimiento["sum_montoCH"],2,',','.');?></b></td>
		<td>&nbsp;</td>
		<td><b><?=number_format($listaMovimiento["sum_montoLTF"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoIVA"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoISLR"],2,',','.');?></b></td>
		<td><b><?=number_format($listaMovimiento["sum_montoFIANZA"],2,',','.');?></b></td>
	</tr>    
</table>
</body>
</html>     