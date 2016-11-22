<html>
	<head>
		<title>.:SAFI:. Detalles de Sopg</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="../../js/funciones.js"></script>
		<script type="text/javascript" src="../../js/constantes.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		
	</head>
	
	<body class="normal">
	<?php 	foreach($val as $index => $valor){?>
		<table align="center" class="tablaalertas" style="width: 90%;" border = "0">
			<tr>
				<th class="header normalNegroNegrita" colspan="6">
				<?php 
				/*echo "<pre>";
				echo print_r($val);
				echo "</pre>";*/
				echo $valor['sopg_id'];
				?>
				</th>
			</tr>
			<tr>
				<td class="normalNegrita">
				Codigo Documento:
				</td>
				<td colspan="3">
				<?php echo $valor['documento_asociado']; //bien?>
				</td>
				<td class="normalNegrita">
				Dependecia solicitante: 
				</td>
				<td>
				<?php echo $valor['depe_nombre']; ?>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">
				Categoria del pago:
				</td>
				<td colspan="3">
				<?php echo $valor['nombre_sol']; ?>
				</td>
				<td class="normalNegrita">
				Motivo: 
				</td>
				<td>
				<?php echo $valor['sopg_detalle']; ?>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">
				Observaciones:
				</td>
				<td colspan="5">
				<?php echo $valor['sopg_observacion']; ?>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="6" style="background-color: #F0F0F0;" class="normalNegroNegrita">
				Datos del Benificiario
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">
				Cedula/Rif:
				</td>
				<td>
				<?php echo $valor['sopg_bene_ci_rif']; ?>
				</td>
				<td class="normalNegrita">
				Nombre:
				</td>
				<td>
				<?php echo $valor['nombre_bene']; ?>
				</td>
				<td class="normalNegrita">
				Tipo:
				</td>
				<td>
				<?php echo $valor['tipo_nombre']; ?>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">
				Observacion:
				</td>
				<td colspan="5">
				<?php echo $valor['sopg_observacion']; ?>
				</td>
			</tr>
			<?php if($valor['facturas']){?>
			<tr>
				<td align="center" colspan="6" style="background-color: #F0F0F0;" class="normalNegroNegrita">
				Facturas
				</td>
			</tr>
			<tr>
				<td colspan="6">		
				<table border="0" style="width: 100%;">
					<tr>
					<td class="normalNegrita">N Factura:</td>
					<td class="normalNegrita">N Control:</td>
					<td class="normalNegrita">Monto Sujeto:</td>
					<td class="normalNegrita">Monto Exento:</td>
					<td class="normalNegrita">Fecha:</td>
					<td class="normalNegrita">Iva:</td>
					<td class="normalNegrita">Monto iva:</td>
					</tr>
					<?php foreach($valor['facturas'] as $index => $valor2){?>
					<tr>						
						<td><?php echo $valor2['sopg_factura']; ?></td>
						<td><?php echo $valor2['sopg_factu_num_cont']; ?></td>
						<td><?php echo $valor2['monto_sujeto']; ?></td>
						<td><?php echo $valor2['monto_exento']; ?></td>
						<td><?php echo $valor2['factura_fecha']; ?></td>				
						<td><?php echo $valor2['factura_iva']; ?></td>	
						<td><?php echo $valor2['factura_iva_monto']; ?></td>
					</tr>
					<?php }?>
				</table>
				</td>
			</tr>
			<?php }?>
		</table>
		<br><br>
		<?php 	}?>
	</body>
</html>