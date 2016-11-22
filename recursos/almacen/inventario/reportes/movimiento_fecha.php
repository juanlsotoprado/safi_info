<?php 
	ob_start();
	session_start();
	require_once("../../../../includes/conexion.php");
	include (dirname ( __FILE__ ) . '/../../../../init.php');
	require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');//ConstruirAccesosRapidosFechas
	 
	if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	{
		header('Location:../../index.php',false);
		ob_end_flush();   
		exit;
	}
	ob_end_flush();
	
	// Obtener las dependencias de gerencias, direccines y presidencia
	$sql = "
		SELECT
			dependencia.depe_id AS dependencia_id,
			dependencia.depe_nombre AS dependencia_nombre
		FROM
			sai_dependenci dependencia
		WHERE
			depe_nivel = 4
			OR depe_id = 150
		ORDER BY
			dependencia_nombre
	";
	
	$resultado = pg_query($conexion, $sql);
	$dependencias = array();
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$dependencias[$row['dependencia_id']] = $row;
		}
	}
	
	// Obtener la categoria de los artículos
	$sql = "
		SELECT
			arti_tipo.tp_id AS categoria_id,
			arti_tipo.tp_desc AS categoria_descripcion
		FROM
			sai_arti_tipo arti_tipo
		ORDER BY
			categoria_descripcion
	";
	
	$resultado = pg_query($conexion, $sql);
	$categoriasArticulos = array();
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$categoriasArticulos[$row['categoria_id']] = $row;
		}
	}
	
	// Obtener todos los artículos
	$query = "
		SELECT
			item.id AS item_id,
			item.nombre AS item_nombre
		FROM
			sai_item item
			INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = item.id)
		ORDER BY
			item.nombre
	";
	
	$resultado = pg_query($conexion, $query);
	$nombresArticulos = array();
	
	if($resultado === false){
		echo "Error al realizar la consulta.";
		error_log(pg_last_error());
	} else {
		while($row=pg_fetch_array($resultado))
		{
			$nombresArticulos[$row['item_id']] = $row['item_nombre'];
		}
	}
	
	
	/*********************************************************************************
	**  Verificar si se ha solicitado realizar una búsqueda con uno o más criterios **
	*********************************************************************************/
	if ( isset($_POST['hid_buscar']) && ($_POST['hid_buscar']) == 2 )
	{
		$fecha_in = trim($_POST['txt_inicio']);
		$fecha_fi = trim($_POST['hid_hasta_itin']);
		$fecha_ini = substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
		$fecha_fin = substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
		$movimiento = trim($_POST['tp_movimiento']);
		$tp_arti = trim($_POST['tp_arti']);
		$depe = trim($_POST['opt_depe']);
		$tipo_f = trim($_POST['tipo_f']);
		$nombre_arti = trim($_POST['des_articulo']);
		
		$criterio1 = "";
		$criterio2 = "";
		$criterio3 = "";
		$criterio4 = "";
		$criterio5 = "";
		$criterio6 = "";
			
		if (strlen($fecha_ini) > 2) {
			$criterio1 = " AND alm_fecha_recepcion >= '".$fecha_ini."' AND alm_fecha_recepcion <= '".$fecha_fin."' ";
			$criterio2 = " AND fecha_acta >= '".$fecha_ini."' AND fecha_acta <= '".$fecha_fin."' ";
		}
		
		if ($depe > 0) {
			$criterio3 = " AND t1.depe_solicitante = '".$depe."' ";
			$criterio4 = " AND depe_entregada LIKE '".substr($depe, 0, 2)."%' ";
		}
		
		if ($tp_arti > '0')
			$criterio5 = " AND t4.tipo = '".$tp_arti."' ";

		$sql = "
			SELECT
				item.id
			FROM
				sai_item item
			WHERE
				item.nombre= '".$nombre_arti."'
		";
		$resultado = pg_query($conexion, $sql);
		if($row = pg_fetch_array($resultado)){
			$codigo_arti=trim($row['id']);       	
		}
		if (strlen($codigo_arti) > 0) {
			$criterio6=" AND t2.id = '".$codigo_arti."' ";
		}
		
		$sql_entrada = "
				SELECT
					t1.acta_id,
					alm_fecha_recepcion AS fecha,
					t1.depe_solicitante AS dependencia,
					t2.id AS arti_id,
					cantidad,
					precio,
					nombre,
					'E' AS tipo,
					prov_id_rif,
					0 AS entregado_a
				FROM
					sai_arti_inco arti_inco
					INNER JOIN sai_arti_almacen t1 ON (t1.acta_id = arti_inco.acta_id)
					INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
					INNER JOIN sai_item_articulo t4 ON (t4.id = t2.id)
				WHERE
					arti_inco.esta_id <> 15
					".$criterio5."
					".$criterio1."
					".$criterio3."
					".$criterio6."
				 
			UNION
			
				SELECT
					amat_id AS acta_id,
					fecha_acta AS fecha,
					depe_entregada AS dependencia,
					t1.arti_id,
					cantidad,
					precio,
					nombre,
					t3.tipo,
					'--' AS prov_id_rif,
					0 AS entregado_a
				FROM
					sai_arti_acta_almacen t3
					INNER JOIN sai_arti_salida t1 ON (t1.n_acta = t3.amat_id)
					INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
					INNER JOIN sai_item_articulo t4 ON (t4.id = t2.id) 
				WHERE
					t3.tipo ='D'
					AND t3.esta_id <> 15 
					".$criterio5."
					".$criterio2."
					".$criterio4."
					".$criterio6."
		";
			
		$sql_salida = "
			SELECT
				t3.amat_id AS acta_id,
				t3.fecha_acta AS fecha,
				t3.depe_entregada AS dependencia,
				t1.arti_id,
				t1.cantidad,
				t1.precio,
				t2.nombre,
				t3.tipo,
				'--' AS prov_id_rif,
				t3.entregado_a
			FROM
				sai_arti_acta_almacen t3
				INNER JOIN sai_arti_salida t1 ON (t1.n_acta = t3.amat_id)
				INNER JOIN sai_item t2 ON (t2.id = t1.arti_id)
				INNER JOIN sai_item_articulo t4 ON (t2.id = t4.id)
			WHERE
				t3.tipo='S'
				AND t3.esta_id <> 15
				".$criterio5."
				".$criterio2."
				".$criterio4."
				".$criterio6."
		";
		
		if ($movimiento=='2'){ //ENTRADAS
			$sql = $sql_entrada." ORDER BY nombre, fecha";
		} elseif ($movimiento=='3') {//SALIDAS
			$sql = $sql_salida." ORDER BY nombre, fecha";
		} else {//ENTRADAS Y SALIDAS
			$sql = $sql_entrada." UNION ".$sql_salida." ORDER BY nombre, fecha";
		}
		
		$movimientosArticulos = array();
		$idsDependenciasDestinos = array();
		$idsProveedores = array();
		$idsActasUbicacion = array();
		$dependenciasDestinos = array();
		$proveedores = array();
		$actasUbicaciones = array();
		
		$resultado = pg_query($conexion, $sql);
		
		if($resultado === false){
			echo "Error al realizar la consulta.";
			error_log(pg_last_error());
		} else {
			while ($row = pg_fetch_array($resultado)){
      			$movimientosArticulos[] = $row;
      			$idsDependenciasDestinos[$row['dependencia']] = $row['dependencia'];
      			$idsProveedores[$row['prov_id_rif']] = $row['prov_id_rif'];
      			if($row['entregado_a'] == '-1') $idsActasUbicacion[$row['acta_id']] = $row['acta_id'];
			}
			
			// Obtener las dependencias
			if(count($idsDependenciasDestinos) > 0){
				
				$sql = "
					SELECT
						depe_id AS id_dependencia,
						depe_nombre AS nombre_dependencia
					FROM
						sai_dependenci
					WHERE
						depe_id IN ('".implode("' ,'", $idsDependenciasDestinos)."')
				";
			
				$resultado = pg_query($conexion, $sql);
				
				if($resultado === false){
					echo "Error al realizar la consulta.";
					error_log(pg_last_error());
				} else {
					while ($row = pg_fetch_array($resultado)){
						$dependenciasDestinos[$row['id_dependencia']] = $row;
					}
				}
			}
			
			// Obtener las ubicaciones
			if(count($idsActasUbicacion) > 0){
				
				$sql = "
					SELECT
						bien_asbi.acta_almacen AS id_acta,
						bien_asbi.ubicacion AS id_ubicacion,
						CASE
							WHEN bien_asbi.ubicacion <> 3 THEN
								bien_ubicacion.bubica_nombre
							WHEN bien_asbi.ubicacion = 3 AND infocentro IS NOT NULL THEN
								(	SELECT
										nemotecnico || ' \: '  ||  nombre
									FROM
										safi_infocentro
									WHERE
										nemotecnico = infocentro
								)
							ELSE
								''
						END AS nombre_ubicacion
					FROM
						sai_arti_acta_almacen arti_acta_almacen
						INNER JOIN sai_bien_asbi bien_asbi ON (bien_asbi.acta_almacen = arti_acta_almacen.amat_id)
						INNER JOIN sai_bien_ubicacion bien_ubicacion ON (bien_ubicacion.bubica_id = bien_asbi.ubicacion)
					WHERE						
						bien_asbi.acta_almacen IN ('".implode("', '", $idsActasUbicacion)."')
				";
				
				$resultado = pg_query($conexion, $sql);
				
				if($resultado === false){
					echo "Error al realizar la consulta.";
					error_log(pg_last_error());
				} else {
					while ($row = pg_fetch_array($resultado)){
						$actasUbicaciones[$row['id_acta']] = $row;
					}
				}
			}
			
			// Obtener los proveedores
			if(count($idsProveedores) > 0){
				
				$sql = "
					SELECT
						prov_id_rif AS id_proveedor,
						prov_nombre AS nombre_proveedor
					FROM
						sai_proveedor_nuevo
					WHERE
						prov_id_rif IN ('".implode("' ,'", $idsProveedores)."')
				";
				
				$resultado = pg_query($conexion, $sql);
				
				if($resultado === false){
					echo "Error al realizar la consulta.";
					error_log(pg_last_error());
				} else {
					while ($row = pg_fetch_array($resultado)){
						$proveedores[$row['id_proveedor']] = $row;
					}
				}
			}
		}
	}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Reporte de Movimientos de Material</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../../../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<script type="text/javascript" src="../../../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript">
		
			$().ready(function(){

				// Llenar el input de artículos
				<?php $arreglo_nombre_articulos = array_map( function($element)
				{
               		return addslashes(utf8_encode($element));
       			}, $nombresArticulos); ?>
				var nombresArticulos = new Array(<?php echo "'". implode("', '", $arreglo_nombre_articulos ) ."'" ?>);
				actb(document.getElementById('des_articulo'), nombresArticulos);
				
			});

			g_Calendar.setDateFormat('dd/mm/yyyy');

			function detalle(codigo1,codigo2,tp,tp_arti,depe,tp_fec)
			{
				url="alma_rep_e3.php?codigo1="+codigo1+"&codigo2="+codigo2+"&tp_mov="+tp+"&tp_arti="+tp_arti+"&depe="+depe
					+"&tipo_f="+tp_fec;
				var newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
				if (window.focus) {newwindow.focus();}
			}
			
			function ejecutar()
			{

				if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') &&
					(document.form.tp_movimiento.value=='0') && (document.form.tp_arti.value=='0') &&
					(document.form.opt_depe.value==0) && (document.form.des_articulo.value=='')
				){
					document.form.hid_buscar.value=1;
					alert("Debe seleccionar un criterio de b\u00fasqueda");
					return;
				}
				else{ document.form.hid_buscar.value=2; }

				document.form.submit();
			}

			function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
			{ 	
				var fecha_inicial=document.form.txt_inicio.value;
				var fecha_final=document.form.hid_hasta_itin.value;
				
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
					((anio1 == anio2) && (mes1==mes2) && (dia1>dia2))
				){
					alert("La fecha inicial no debe se mayor a la fecha final"); 
					document.form.hid_hasta_itin.value='';
					return;
				}
			}
		</script>
	</head>
	
	<body>
		<form name="form" action="movimiento_fecha.php" method="post">
			<br />
			<table width="700" align="center" background="../../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray" > 
					<td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Movimientos</td>
				</tr>
				<tr>
					<td height="5" colspan="3" align="right" style="padding-right: 25px;">
					  <!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
					</td>	
				</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Fecha:</td>
					<td width="406" class="normalNegrita">
						<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							value="<?php echo ( isset($fecha_in) && trim($fecha_in) != "" ? $fecha_in : "") ?>"
						/>
		 				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
		 					<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		 				</a>
		 				<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
		 					onfocus="javascript: comparar_fechas(this);" readonly="readonly"
		 					value="<?php echo ( isset($fecha_fi) && trim($fecha_fi) != "" ? $fecha_fi : "") ?>"
		 				/>
		 				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
		 					<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		 				</a>	
					</td>
	  			</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Movimiento:</td>
					<td>
						<select name="tp_movimiento" id="tp_movimiento"  class="normal">
							<option value="0">[Seleccione]</option>
							<option value="1"<?php echo ($movimiento == '1') ? ' selected="selected"' : '' ?>>Entradas/Salidas</option>
							<option value="2"<?php echo ($movimiento == '2') ? ' selected="selected"' : '' ?>>Entradas</option>
							<option value="3"<?php echo ($movimiento == '3') ? ' selected="selected"' : '' ?>>Salidas</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Dependencia:</td>
					<td>
						<select name="opt_depe"  class="normal" id="opt_depe">
							<option value="0">[Seleccione]</option>
							<?php
								foreach ($dependencias AS $dependencia)
								{
									echo '
							<option
								value="'.(trim($dependencia['dependencia_id'])).'"
								'.( (isset($depe) && $depe == $dependencia['dependencia_id']) ? ' selected="selected"' : '' ).'
							>
								'.(trim($dependencia['dependencia_nombre'])).'
							</option>
									';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Categor&iacute;a:</td>
					<td>
						<select name="tp_arti" id="tp_arti"  class="normal">
							<option value="0">[Seleccione]</option>
							<?php							 
								foreach ($categoriasArticulos AS $categoriaArticulo)
								{
									echo '
							<option
								value="'.$categoriaArticulo["categoria_id"].'"
								'.( (isset($tp_arti) && $tp_arti == $categoriaArticulo["categoria_id"]) ? ' selected="selected"' : '' ).'
							>
								'.$categoriaArticulo["categoria_descripcion"].'
							</option>
									';
								}
							?>
						</select>
						<input type="hidden" name="hid_buscar" id="hid_buscar" value="0" />
					</td>
				</tr>
				<tr>
					<td width="222" class="normalNegrita">Art&iacute;culo:</td>
					<td width="316">
						<input autocomplete="off" size="60" type="text" id="des_articulo" name="des_articulo"
							value="<?php echo $nombre_arti ?>" />
						<input type="hidden" name="txt_articulo" value="" />
		  				<input type="hidden" name="hid_articulo" value="" />
					</td>	
				</tr>
				<tr>
					<td height="52" colspan="3" align="center">
						<input type="button" class="normalNegro"  value="Buscar" onclick="javascript:ejecutar()" />
					</td>
				</tr>
			</table>
		</form>
		<br />
	
		<form name="form1" action="" method="post">
		<?php 
			if ( isset($_POST['hid_buscar']) && ($_POST['hid_buscar']) == 2 )
			{
				if(count($movimientosArticulos) == 0)
				{
		?>
			<center>
				<span style="color: #003399" class="normalNegrita">
					No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado
				</span>
			</center>
			<?php 
				}
				else {
			?>
			<table width="100%" background="../../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
				<tr>
					<td>
						<div align="center" class="normalNegroNegrita">
							Movimientos de art&iacute;culos  
							<?php
								if($movimiento == "2"){ echo "entrantes"; }
								elseif($movimiento == "3") { echo "salientes"; } 
								else{ echo "entrantes y salientes"; }
								
								echo " desde el " . $fecha_in . " al " . $fecha_fi;
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td height="49" colspan="7" align="center" class="normalNegrita">
						<table width="100%" border="0" class="tablaalertas">
							<tr class="td_gray" align="center">
								<td class="normalNegroNegrita" scope="col">Acta</td>
								<td class="normalNegroNegrita" scope="col">Tipo</td>
								<td class="normalNegroNegrita" scope="col">Fecha acta</td>
								<td class="normalNegroNegrita" scope="col">Art&iacute;culo</td>
								<td class="normalNegroNegrita" scope="col">Proveedor</td>
								<td class="normalNegroNegrita" scope="col">Dependencia/Destino</td>
								<td class="normalNegroNegrita" scope="col">Cantidad</td>
								<td class="normalNegroNegrita" scope="col">Costo unitario en Bs. </td>
								<td class="normalNegroNegrita" scope="col">Monto total en Bs. </td>
							</tr>
						<?php 
							$i=0;
							$total_entradas=0;
							$total_salidas=0;
							$monto=0;
							
							foreach ($movimientosArticulos AS $rowt1)
							{
								$i++;
								if(trim($rowt1['tipo'] == 'E')){
									$strMovimiento = 'Entrada';
									$total_entradas = $total_entradas+($rowt1['precio']*$rowt1['cantidad']);
									$pagina = "entradas";
								}
			  
								if(trim($rowt1['tipo'] == 'S')){
									$strMovimiento = 'Salida';
									$total_salidas = $total_salidas+($rowt1['precio']*$rowt1['cantidad']); 
									$pagina = "salidas";
								}
								if(trim($rowt1['tipo']=='D')){
									$strMovimiento = utf8_decode('Devolución');
									$total_entradas = $total_entradas+($rowt1['precio']*$rowt1['cantidad']);
									$pagina = "devoluciones";
								}
								
								$fec = substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
								$hora = substr($rowt1['fecha'],11,8);
								
								// Obtener el proveedor
								$objProveedor = (isset($proveedores[$rowt1['prov_id_rif']]))
									? ($proveedores[$rowt1['prov_id_rif']]) : null;
								
								$proveedor = ($objProveedor !== null)
									? $objProveedor['id_proveedor'] . " : " . $objProveedor['nombre_proveedor'] : "--";
									
								// Obtener la dependencia
								$dependencia = (isset($dependenciasDestinos[$rowt1['dependencia']]))
									? $dependenciasDestinos[$rowt1['dependencia']]['nombre_dependencia'] : "--";
								
								if ($rowt1['entregado_a'] == '-1')
								{
									$dependencia = (
											isset($actasUbicaciones[$rowt1['acta_id']])
											&& $actasUbicaciones[$rowt1['acta_id']]['id_ubicacion'] != "1"
										)
										? $actasUbicaciones[$rowt1['acta_id']]['nombre_ubicacion'] : "--";
								}
						?>
							<tr>
								<td class="normal" scope="row" align="center">
									<a href="javascript:abrir_ventana('../<?=$pagina?>_pdf.php?id=
										<?php echo trim($rowt1['acta_id']); ?>&codigo=<?php echo trim($rowt1['acta_id']);?>')"
										class="copyright"><?php echo $rowt1['acta_id'];?>
									</a>
								</td>
								<td class="normal"><?php echo $strMovimiento ?></td>
								<td class="normal" align="center"><?php echo $fec ?></td>
								<td class="normal"><?php echo mb_strtoupper($rowt1['nombre'], 'ISO-8859-1') ?></td> 
								<td class="normal"><?php echo $proveedor;?></td>
								<td height="24" class="normal" align="justify"><?php echo mb_strtoupper($dependencia, 'ISO-8859-1') ?></td>
								<td class="normal"><div align="right"><?php echo $rowt1['cantidad']; ?></div></td>
								<?php $precio = $rowt1['precio'] ?>
								<td class="normal"><div align="right"><?php	echo str_replace('.', ',', $rowt1['precio']);?></div></td>
								<?php $monto = $rowt1['precio'] * $rowt1['cantidad'];
									$monto_total = $monto_total + $monto;
								?>
								<td class="normal"><div align="right"><?php echo(number_format($monto,2,',','.')); ?></div></td>
							</tr>
							<?php
							} // Fin de foreach ($movimientosArticulos AS $rowt1)
							
							if ($movimiento == '2'){
							?>
							<tr>
								<td colspan="8" align="right" class="normalNegrita">Total entradas:</td>
								<td class="normal"><div align="right"><?php echo(number_format($total_entradas,2,',','.')); ?></div></td>
							</tr>
							<?php
							} elseif ($movimiento == '3') {
							?>
							<tr>
								<td colspan="8" align="right" class="normalNegrita">Total salidas:</td>
								<td class="normal"><div align="right"><?php echo(number_format($total_salidas,2,',','.')); ?></div></td>
							</tr>
							<?php
							} else {
							?>
							<tr>
								<td colspan="8" align="right" class="normalNegrita">Total entradas:</td>
								<td class="normal"><div align="right"><?php echo(number_format($total_entradas,2,',','.')); ?></div></td>
							</tr>
							<tr>
								<td colspan="8" align="right" class="normalNegrita">Total salidas:</td>
								<td class="normal"><div align="right"><?php echo(number_format($total_salidas,2,',','.')); ?></div></td>
							</tr>
							<?php
							}
							?>		
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center" class="normal">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="7" align="center" class="normalNegrita">
						<br />
						<span class="normal">
							<span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/Y")?> a las <?=date("h:i:s")?></span>
							<br /><br />
						</span>
						<br /><br />   
						<a href="movimiento_fecha_pdf.php?txt_inicio=<?php echo($_POST['txt_inicio']); ?>
							&hid_hasta_itin=<?php echo($_POST['hid_hasta_itin']); ?>&tp_movimiento=<?php echo($movimiento); ?>
							&tp_arti=<?php echo($_POST['tp_arti']); ?>&depe=<?php echo($_POST['opt_depe']); ?>
							&arti=<?php echo($_POST['des_articulo'])?>">
								<img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" />
						</a>
						<br /><br />
						<span class="link">
							<a href="javascript:window.print()" class="link">
								<img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" />
							</a>
						</span>
						<br />
						<span class="link">Imprimir Documento</span>
						<br /><br /><br /><br />
					</td>
				</tr>
			</table>
			<?php
				}
			}
			?>
		</form>
	</body>
</html>