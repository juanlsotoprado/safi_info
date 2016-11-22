<?php 
ob_start();
session_start();
require_once(dirname(__FILE__) . '/../../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
	 
$paramFechaInicio = trim($_GET['fechaInicio']);
$paramFechaFin = trim($_GET['fechaFin']);
$paramIdDependencia = trim($_GET['idDependencia']);
$idsMateriales = $_GET['idsMateriales'];
$paramIdCategoria = trim($_GET['idCategoria']);

$nombreCategoria = "";
if($paramIdCategoria != null && $paramIdCategoria != "" && $paramIdCategoria != "0")
{
	$query = "
		SELECT
			categoria.tp_desc
		FROM
			sai_arti_tipo categoria
		WHERE
			categoria.tp_id='" . $paramIdCategoria . "'
	";
	
	$resultado = pg_query($conexion, $query);
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$nombreCategoria = $row["tp_desc"];
		}
	}
}

$nombreItem = "";
if($idsMateriales != null && is_array($idsMateriales) && count($idsMateriales) > 0)
{
	
	$materiales = SafiModeloItem::GetItemsByIds($idsMateriales);
	
	if(is_array($materiales)){
		reset($materiales);
		$nombreItem = current($materiales)->GetNombre();
	}
}

$whereInterno = "";
if($idsMateriales !== null && is_array($idsMateriales) && count($idsMateriales) > 0)
{
	$whereInterno = "AND item.id IN ('".implode("', '", $idsMateriales)."')";
}

$whereExterno = "";
if($paramIdDependencia !== null && $paramIdDependencia != ""  && $paramIdDependencia != "0")
{
	$whereExterno = "AND dependencia.depe_id = '".$paramIdDependencia."'";
}
if($paramIdCategoria !== null && $paramIdCategoria != ""  && $paramIdCategoria != "0")
{
	$whereExterno .= "AND categoria.tp_id = '".$paramIdCategoria."'";
}
	     
$query = "
	SELECT
		salidas.id_acta,
		salidas.fecha_acta,
		TO_CHAR(salidas.fecha_acta, 'DD/MM/YYYY') AS fecha_acta_str,
		dependencia.depe_id AS depe_entregada,
		salidas.arti_id,
		salidas.cantidad,
		salidas.precio,
		salidas.nombre_item,
		salidas.tipo,
		dependencia.depe_nombre AS nombre_dependencia,
		dependencia.depe_nombrecort AS nombre_corto_dependencia,
		categoria.tp_id AS id_categoria,
		categoria.tp_desc AS nombre_categoria
	FROM
		(
				SELECT
					salida.amat_id AS id_acta,
					salida.fecha_acta,
					salida.depe_entregada AS id_dependencia,
					salida_detalle.arti_id,
					salida_detalle.cantidad,
					entrada_detalle.precio,
					item.nombre AS nombre_item,
					salida.tipo,
					item_articulo.tipo AS id_categoria
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a != '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
					
			UNION
			
				SELECT
					salida.amat_id,
					salida.fecha_acta,
					CASE
						WHEN (item_articulo.tipo = '1') THEN '450' 
						WHEN (item_articulo.tipo = '2') THEN '550' 
						WHEN (item_articulo.tipo = '7' or item_articulo.tipo = '11') THEN '453' 
						WHEN (item_articulo.tipo = '8') THEN '600'
						WHEN (item_articulo.tipo = '3') THEN '250'
						--ELSE '450'
					END AS id_dependencia,	
					salida_detalle.arti_id,
					salida_detalle.cantidad,
					entrada_detalle.precio,
					item.nombre,
					salida.tipo,
					item_articulo.tipo AS id_categoria
				FROM
					sai_arti_acta_almacen salida
					INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
					INNER JOIN sai_item item ON (item.id = item_articulo.id)
					INNER JOIN sai_arti_almacen entrada_detalle
						ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
				WHERE
					salida.esta_id <> 15
					AND salida.entregado_a = '-1'
					AND salida.fecha_acta BETWEEN
						TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
							AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					".$whereInterno."
		)AS salidas
		INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = salidas.id_categoria)
		INNER JOIN sai_dependenci dependencia ON (dependencia.depe_id = salidas.id_dependencia)
	WHERE
		salidas.id_dependencia IS NOT NULL
		".$whereExterno."
 	ORDER BY
 		".($paramIdDependencia == null || $paramIdDependencia == "" || $paramIdDependencia == '0' 
			? "dependencia.depe_nombre" : "categoria.tp_desc").",
 		salidas.nombre_item,
 		salidas.fecha_acta
";

$resultado = pg_query($conexion, $query);
$reporte = array();

if($resultado === false){
	echo "Error al consultar el reporte.";
	
	error_log(pg_last_error());
} else {
	while($row = pg_fetch_array($resultado))
	{
		$reporte[] = $row;
	}
}	  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo 'xmlns="http://www.w3.org/1999/xhtml"';?>>
	<head>
		<link type="text/css" href="../../../../css/plantilla.css" rel="stylesheet" />
	</head>
	
	<body>
	<?php
		if(count($reporte) == 0)
		{
	?>
		<center>
			<span class="titularMedio" style="color: #003399;">
				No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado
			</span>
		</center>
		<script language="javascript">
			document.form.tp_movimiento.value="<? echo $movimiento ?>";
			document.form.txt_inicio.value="<? echo $paramFechaInicio ?>";
			document.form.hid_hasta_itin.value="<? echo $paramFechaFin ?>";
			document.form.tp_arti.value="<? echo $tp_arti ?>";
			document.form.opt_depe.value="<? echo $paramIdDependencia ?>";
		 </script>
	<?php 
		} else {
	?>
		<table width="678" background="../../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
			<tr class="td_gray">
				<td width="670" colspan="7" align="center" class="normalNegroNegrita">
					Gastos por dependencia del <?php
						echo $paramFechaInicio . " al " . $paramFechaFin
						. (($nombreCategoria != null && $nombreCategoria != "") ? " de " . $nombreCategoria : "")
						. (($nombreItem != null && $nombreItem != "") ? " de " . $nombreItem : "")
					?>
				</td>
			</tr>
			<tr>
				<td height="49" colspan="7" align="center" class="normalNegrita"><table width="729" border="0" class="tablaalertas">
					<tr>
						<td width="42" bgcolor="#F0F0F0" class="normalNegrita">Acta</td>
						<td width="59" bgcolor="#F0F0F0" class="normalNegrita">Fecha del acta</td>
						<td width="203" bgcolor="#F0F0F0" class="normalNegrita">Art&iacute;culo</td>
						<td width="115" bgcolor="#F0F0F0" class="normalNegrita">Dependencia</td>
						<td width="71" bgcolor="#F0F0F0" class="normalNegrita">Cantidad</td>
						<td width="99" bgcolor="#F0F0F0" class="normalNegrita">Costo Unitario en Bs. </td>
						<td width="99" bgcolor="#F0F0F0" class="normalNegrita">Monto total en Bs. </td>
					</tr>
					<?php
					$depe_anterior = "";
					$clasificacion = '';
					$pri = 0;
					
					$montoTotalItem = 0;
					$montoSubtotal = 0;
					$montoTotal = 0;

					foreach ($reporte AS $row)
					{
						$clasificacion_actual = $row['nombre_categoria'];
						$depen = $row['depe_entregada'];

						if ( ($paramIdDependencia <> '0') && ($clasificacion <> $clasificacion_actual) )
						{
							if ( ($pri <> 0) && ($paramIdCategoria == '0') )
							{
					?>	 
					<tr>
						<td colspan="6" align="right" class="normal"><b>Subtotal:</b></td>
						<td align="right" class="normal">
							<b><?php echo(number_format($montoSubtotal, 2, ',', '.')); ?></b>
						</td>
					</tr>
					<?php
							}
					?>
					<tr>
						<td width="41" bgcolor="#F0F0F0" colspan="10"><div align="center" class="normal">
							<b><?php echo mb_strtoupper($row['nombre_categoria'], "ISO-8859-1");?></b>
						</div></td>
					</tr>          
					<?php 
							$montoSubtotal = 0;
							$clasificacion = $clasificacion_actual;
						}
					
						if ( ($paramIdDependencia == '0') && ($depen <> $depe_anterior) )
						{
							if ($pri <> 0)
							{
					?>   
					<tr>
						<td colspan="6" align="right"  class="normal"><b>Sub-total:</b></td>
						<td align="right"  class="normal">
							<b><?php echo(number_format($montoSubtotal, 2, ',', '.')); ?></b>
						</td>
					</tr>
					<?php
								$montoSubtotal = 0;
							}
							$depe_anterior = $depen;
					?>
					<tr>
						<td bgcolor="#F0F0F0" colspan="7" align="center" class="normalNegrita">
							<?php echo mb_strtoupper($row['nombre_dependencia'], "ISO-8859-1");?>
						</td>
					</tr>
					<?php
						}
					?>
					<tr>
						<td class="normal" scope="row" style="text-align: left; padding-right: 5px;">
							<?php echo $row['id_acta'] ?>
						</td>
						<td class="normal" style="padding-right: 5px;"><?php echo $row['fecha_acta_str'];?></td>
							<?$monto=$row['precio'];?>
							<td class="normal"><div align="left"><?php echo trim($row['nombre_item']);?></div></td>
							<td height="24" class="normal" style="text-align: center;">
								<?php echo $row['nombre_corto_dependencia']?>
							</td>
							<?php
						if(trim($row['tipo']) == 'D')
						{
							$cantidad = $row['cantidad'] * -1;
						} else {
							$cantidad = $row['cantidad'];
						}
						?>
						<td class="normal"><div align="right"><?php echo $cantidad; ?></div></td>         
						<td class="normal"><div align="right"><?php echo str_replace('.', ',', $monto); ?></div></td>
						<?php
						$pri++;
						
						$montoTotalItem = $monto * $cantidad;
						$montoSubtotal += $montoTotalItem;  
						$montoTotal += $montoTotalItem;
						?>
						<td class="normal" style="text-align: right;">
							<?php echo(number_format($montoTotalItem, 2, ',', '.')); ?>
						</td>
					</tr>
					<?php
					}
					?> 
					<tr>
						<td colspan="6" align="right" class="normal"><b>Subtotal:</b></td>
						<td align="right"  class="normal"><b><?php echo number_format($montoSubtotal, 2, ',', '.'); ?></b></td>
					</tr>
					<tr>
						<td colspan="6"  class="normal"><b>TOTAL:</b></td>
						<td align="right"  class="normal"><b><?php echo(number_format($montoTotal, 2, ',', '.')); ?></b></td>
					</tr>
				</table></td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normal">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7" align="center" class="normalNegrita">
					<br />
					<span class="normal"><span class="peq_naranja">Detalle generado  el d&iacute;a
						<?=date("d/m/y")?> a las <?=date("h:i:s")?></span>
						<br /><br />
					</span>
					<br /><br />   
					<a href="gastos_detalle_pdf.php?fechaInicio=<?php echo $paramFechaInicio ?>&fechaFin=<?php
						echo $paramFechaFin ?>&idDependencia=<?php echo $paramIdDependencia;?><?php
							if($idsMateriales != null && is_array($idsMateriales) && count($idsMateriales) > 0)
								echo "&idsMateriales[]=" . implode("&idsMateriales[]=", $idsMateriales) . "";
						?>&idCategoria=<?php echo $paramIdCategoria; ?>"><img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a> 
			  		<br /><br />
					<span class="link">
						<a href="javascript:window.print()" class="link"><img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>
					</span>
					<br />
					<span class="link">Imprimir Documento</span>
					<br /><br /><br />	
					<a href="gastos.php?txt_inicio=<?php echo $paramFechaInicio;?>&hid_hasta_itin=<?php echo $paramFechaFin;?>&depe=<?php echo $paramIdDependencia?><?php
			  			if($idsMateriales != null && is_array($idsMateriales) && count($idsMateriales) > 0)
							echo "&idsMateriales[]=" . implode("&idsMateriales[]=", $idsMateriales) . "";
			  		?>&tipo=<?php echo $paramIdCategoria;?>&hid_buscar=2" ><input type="button" value="Regresar" /></a>  
					<br />
				</td>
			</tr>
		</table>
		<?php
		}
		?>
	</body>
</html>