<?php
	ob_start();
	session_start();
	require_once("../../../../includes/conexion.php");
	if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	{
		header('Location:../../../../index.php',false);
		ob_end_flush();
		exit;
	}
	ob_end_flush();
		
	function redondear_dos_decimal($valor) {
		$float_redondeado=round($valor * 100) / 100;
		return $float_redondeado;
	}
	
	$buscar = "";
	$fecha_in = "";
	$fecha_fi = "";
	
	if (isset($_GET['hid_buscar']) && (($_GET['hid_buscar']==1) || ($_GET['hid_buscar']==2)))
	{
		$buscar = $_GET['hid_buscar'];
		$fecha_in = trim($_GET['txt_inicio']);
		$fecha_fi = trim($_GET['hid_hasta_itin']);
		
		// Obtener el reporte
		$sql = "
			SELECT
				categoria.tp_id AS categoria_id,
				categoria.tp_desc AS categoria_descripcion,
				item.id AS item_id,
				item.nombre AS item_nombre,
				item_articulo.existencia_minima AS item_existencia_minima,
				item_articulo.unidad_medida AS item_unidad_medida,
				".($buscar == "1" ? "/*entradas.precio*/ entrada_detalle.precio AS entradas_precio," : "")."
				SUM (
					entradas.total_entradas_anteriores - salidas.total_salidas_anteriores
				) AS inventario,
				SUM (
					entradas.total_entradas_entre_fechas
				) AS total_entradas,
				SUM (
					salidas.total_salidas_entre_fechas
				) AS total_salidas,
				SUM (
					(entradas.total_entradas_anteriores - salidas.total_salidas_anteriores + entradas.total_entradas_entre_fechas)
						* entradas.precio
				) AS total_costo,
				SUM (
					entradas.total_entradas_anteriores - salidas.total_salidas_anteriores + entradas.total_entradas_entre_fechas
						- salidas.total_salidas_entre_fechas
				) AS total_inventario,
				SUM (
					/*salidas.total_salidas_entre_fechas * entradas.precio*/
					salidas.total_salidas_entre_fechas * entrada_detalle.precio
				) AS total_gastos
			FROM
				sai_arti_tipo categoria
				INNER JOIN sai_item_articulo item_articulo ON (item_articulo.tipo = categoria.tp_id)
				INNER JOIN sai_item item ON (item_articulo.id = item.id)
				INNER JOIN sai_arti_almacen entrada_detalle ON (entrada_detalle.arti_id = item.id)
				
				INNER JOIN (
					SELECT
						entrada_detalle.arti_id AS id_item,
						entrada_detalle.alm_id AS id_almacen,
						entrada_detalle.precio AS precio,
						SUM(
							CASE
								WHEN entrada_detalle.alm_fecha_recepcion < TO_DATE('".$fecha_in."', 'DD/MM/YYYY') THEN
									entrada_detalle.cantidad
								ELSE
									0
							END 
						) AS total_entradas_anteriores,
						SUM(
							CASE
								WHEN
									entrada_detalle.alm_fecha_recepcion
										BETWEEN TO_DATE('".$fecha_in."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') THEN
									entrada_detalle.cantidad
								ELSE
									0
							END
						) AS total_entradas_entre_fechas,
						SUM(entrada_detalle.cantidad) AS total_entrada
					FROM
						sai_arti_inco entrada
						INNER JOIN sai_arti_almacen entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
					WHERE
						entrada.esta_id <> 15
						AND entrada_detalle.alm_fecha_recepcion <= TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
					GROUP BY
						id_item,
						id_almacen,
						precio
				) AS entradas ON (entradas.id_item = entrada_detalle.arti_id AND entradas.id_almacen = entrada_detalle.alm_id)
				
				INNER JOIN (
					SELECT
						salida_detalle.alm_id AS id_almacen,
						salida_detalle.arti_id AS id_item,
						SUM(
							CASE
								WHEN salida.fecha_acta < TO_DATE('".$fecha_in."', 'DD/MM/YYYY') THEN
									CASE
										WHEN salida.tipo = 'S' THEN
											salida_detalle.cantidad
										ELSE
											salida_detalle.cantidad * -1
									END
								ELSE
									0
							END 
						) AS total_salidas_anteriores,
						SUM(
							CASE
								WHEN
									salida.fecha_acta
										BETWEEN TO_DATE('".$fecha_in."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') THEN
									CASE
										WHEN salida.tipo = 'S' THEN
											salida_detalle.cantidad
										ELSE
											salida_detalle.cantidad * -1
									END
								ELSE
									0
							END
						) AS total_salidas_entre_fechas
					FROM
						sai_arti_acta_almacen salida
						INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
					WHERE
						salida.esta_id <> 15
						AND salida.fecha_acta <= TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
					GROUP BY
						id_almacen,
						id_item
				) AS salidas ON (salidas.id_almacen = entrada_detalle.alm_id AND salidas.id_item = entrada_detalle.arti_id)
			GROUP BY
				categoria_id,
				categoria_descripcion,
				item_id,
				item_nombre,
				item_existencia_minima,
				item_unidad_medida
				".($buscar == "1" ? ", entradas.id_almacen, entradas_precio" : "")."
			HAVING
				SUM (entradas.total_entradas_entre_fechas) > 0
				OR SUM ( salidas.total_salidas_entre_fechas) > 0
			ORDER BY
				categoria_descripcion,
				item_nombre
				".($buscar == "1" ? ", entradas.id_almacen" : "")."
		";
		
		$resultado = pg_query($conexion, $sql);
		$reporte = array();
	
		if($resultado == false){
			echo "Error al realizar la consulta.";
			error_log(pg_last_error());
		}
		else {
			while($row = pg_fetch_array($resultado))
			{
				$reporte[] = $row;
			}
		}
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Inventario de Materiales Existente</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../../../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../../../includes/js/funciones.js"> </script>
		<?php require("../../../../includes/fechas.php");?>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript">
			g_Calendar.setDateFormat('dd/mm/yyyy');
	
			function detalle(codigo,nombre)
			{
				url="alma_rep_e1.php?codigo="+codigo+"&nombre="+nombre;
				newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
				if (window.focus) {newwindow.focus();}
			}
	
			function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
			{ 	
				var fecha_inicial=document.form1.txt_inicio.value;
				var fecha_final=document.form1.hid_hasta_itin.value;
					
				var dia1 =fecha_inicial.substring(0,2);
				var mes1 =fecha_inicial.substring(3,5);
				var anio1=fecha_inicial.substring(6,10);
				
				var dia2 =fecha_final.substring(0,2);
				var mes2 =fecha_final.substring(3,5);
				var anio2=fecha_final.substring(6,10);
			
				dia1 = parseInt(dia1,10);
				mes1 = parseInt(mes1,10);
				anio1= parseInt(anio1,10);
			
				dia2 = parseInt(dia2,10);
				mes2 = parseInt(mes2,10);
				anio2= parseInt(anio2,10); 
					
				if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
				 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
				{
				  alert("La fecha inicial no debe se mayor a la fecha final"); 
				  document.form1.hid_hasta_itin.value='';
				  return;
				}
			}
	
			function ejecutar(codigo1,codigo2)
			{
	  			if(document.form1.txt_inicio.value==''){
					alert(" Debe indicar la fecha inicial del rango para el reporte. ");
					return;
				}   
	
				if(document.form1.hid_hasta_itin.value==''){
					alert(" Debe indicar la fecha de final del rango para el reporte. ");
					return;
				}
				
				var sw=0;
				for(i=0;i<2;i++){
					
					if(document.form1.hid_buscar[i].checked==true)
					{
						sw=1;
					}
				}
				
				if(sw==0){
					alert(" Debe seleccionar el tipo de totales para el reporte. ");
					return;
				}
				
				if(document.form1.hid_buscar[0].checked==true){
					document.form1.hid_buscar.value=1;
				}
				else{
					document.form1.hid_buscar.value=2;
				}
		  
				document.form1.hid_hasta_itin.value=codigo2;
				document.form1.txt_inicio.value=codigo1;
				window.location="compras_consumo.php?txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&hid_buscar="
					+document.form1.hid_buscar.value;
			}
		</script>
	</head>
	<body>
	<form name="form1" action="" method="post">
		<br />
		<table width="677" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="15" colspan="3" valign="middle"
					class="normalNegroNegrita">Compras y consumo</td>
			</tr>
			<tr>
				<td width="240" height="34" align="right" class="normalNegrita">Fecha
					de corte:</td>
				<td width="406" class="normalNegrita">
					<div align="left">
						<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $fecha_in; ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
							<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" />
						</a>
						<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $fecha_fi; ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
							<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" />
						</a>
					</div>
				</td>
			</tr>
			<tr>
				<td width="259" height="34" align="right" class="normalNegrita">Tipo:</td>
				<td width="406" class="normalNegrita">
					<input type="radio" name="hid_buscar" value="1"<?php
						echo ($buscar == "1" ? ' checked="checked"' : "")
					?> />Detallado&nbsp;
					<input type="radio" name="hid_buscar" value="2"<?php
						echo ($buscar != "" && $buscar != "1" ? ' checked="checked"' : "")
					?> />General
				</td>
			</tr>
			<tr>
				<td height="52" colspan="3" align="center">
					<input type="button" class="normalNegro" value="Buscar"
						onclick="javascript:ejecutar(document.form1.txt_inicio.value,document.form1.hid_hasta_itin.value)"
				/>
			</td>
			</tr>
		</table>
	</form>
	<br />
	
	<?php
	
		if(count($reporte) > 0)
		{
	?>
		<table width="651" align="center" background="../../../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr>
				<td>
					<div align="center">
						<span class="normalNegroNegrita">Balance <?
							echo ($buscar == "1" ? "detallado" : "general") . " de compras y consumo del ". $fecha_in." al " . $fecha_fi;
						?></span>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
						<tr class="td_gray">
							<td class="normalNegroNegrita" style="width: 107px; height: 25px; text-align: center;">C&oacute;digo</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 321px;">Art&iacute;culo</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 78px;">Unidad de medida</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Inventario</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Entradas</td>
							<?if ($buscar == "1"){?>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Costo unitario Bs.</td>
							<?}?>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Costo total Bs.</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Salidas</td>
							<td class="normalNegroNegrita" style="text-align: center;" width="70">Total inventario</td>
							<td class="normalNegroNegrita" style="text-align: center; width: 70px;">Total gastos Bs.</td>
						</tr>
						<?php

			$idCategoriaActual = null;
			$tdBgColor = "";
			$subtotalGastos = 0;
			$totalGastos = 0;
			
			foreach ($reporte AS $row)
			{
				if($idCategoriaActual != $row['categoria_id'])
				{
					if ($idCategoriaActual != null)
					{
						?>
						<tr>
							<td class="normal" style="text-align: right; width: 41px;"
								colspan="<?php echo ($buscar == "1" ? "9" : "8") ?>"
							>
								<b>Subtotal Gastos:</b>
							</td>
							<td class="normal" style="text-align: right; width: 41px;">
								<b><?php echo number_format($subtotalGastos, 2, ',', '.') ?></b>
							</td>
						</tr>
						<?php
						
					}
						?>
						<tr>
							<td class="normal" style="background-color: #F0F0F0; text-align: center; width: 41px;"
								colspan="<?php echo ($buscar == "1" ? "10" : "9")?>"
							>
								<b><?php echo mb_strtoupper($row['categoria_descripcion'], "ISO-8859-1") ?></b>
							</td>
						</tr>
						<?php
						
					$idCategoriaActual = $row['categoria_id'];
					$tdBgColor = "";
					$subtotalGastos = 0;
				}
				
				$subtotalGastos += $row['total_gastos']; 
				$totalGastos += $row['total_gastos'];
				
						?>
						<tr>
							<td class="normal" style="height: 21px; text-align: center;<?php echo $tdBgColor ?>">
								<?php echo $row['item_id'] ?>
							</td>
							<td class="normal" style="text-align: left;<?php echo $tdBgColor ?>">
								<?php echo $row['item_nombre'] ?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo mb_strtoupper($row['item_unidad_medida'], "ISO-8859-1") ?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo $row['inventario'] ?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo $row['total_entradas'] ?>
							</td>
							<?php if($buscar == "1") {?>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo number_format($row['entradas_precio'], 2, ',', '.') ?>
							</td>
							<?php } ?>
							<?php $costoTotal = ($row['inventario'] + $row['total_entradas']) * $row['entradas_precio'] ?>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo number_format($row['total_costo'], 2, ',', '.') ?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo $row['total_salidas'] ?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo $row['total_inventario'];?>
							</td>
							<td class="normal" style="text-align: right;<?php echo $tdBgColor ?>">
								<?php echo number_format($row['total_gastos'], 2, ',', '.') ?>
							</td>
						</tr>
						<?php
				
				$tdBgColor = ($tdBgColor == "") ? " background-color: #E9F3F3;" : '';
			}
						?>
						<tr>
							<td class="normal" style="text-align: right; width: 41px;"
								colspan="<?php echo ($buscar == "1" ? "9" : "8") ?>"
							>
								<b>Subtotal Gastos:</b>
							</td>
							<td class="normal" style="text-align: right; width: 41px;">
								<b><?php echo number_format($subtotalGastos, 2, ',', '.') ?></b>
							</td>
						</tr>
						<tr>
							<td class="normal" style="text-align: right; width: 41px;"
								colspan="<?php echo ($buscar == "1" ? "9" : "8") ?>"
							>
								<b>Total Gastos:</b>
							</td>
							<td class="normal" style="text-align: right; width: 41px;">
								<b><?php echo (number_format($totalGastos, 2, ',', '.'));?></b>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="16" colspan="5" class="normal">
					<div align="center">
						<br />
						<span class="peq_naranja">Detalle generado el d&iacute;a <?php echo date("d/m/y") ?> a las <?=date("H:i:s")?>
						<br /><br /><br />
						<a href="compras_consumo_pdf.php?txt_inicio=<?php echo($_GET['txt_inicio']); ?>
							&hid_hasta_itin=<?php echo ($_GET['hid_hasta_itin']);?>&hid_buscar=<?php echo($_GET['hid_buscar']);?>"
						>
							<img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" />
						</a>
						<br /><br />
						<a href="javascript:window.print()" class="normal">
							<img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" />
						</a>
						<br />
						<span class="link">Imprimir Documento</span></span>
						<br /><br /><br />
					</div>
				</td>
			</tr>
		</table>
		
		<br /><br /><br /><br />
	<?php
		}
	?>
	</body>

</html>