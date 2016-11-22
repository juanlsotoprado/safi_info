<?php 
// buscar y borrar des_articulo
ob_start();
session_start();
require_once(dirname(__FILE__) . '/../../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();   exit;
}
ob_end_flush();

// Obtener las dependencias de gerencias, direcciones y presidencia
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

// Obtener las categorías de los materiales
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

$paramBuscar = trim($_REQUEST['hid_buscar']);
$paramFechaInicio = trim($_REQUEST['txt_inicio']); 
$paramFechaFin = trim($_REQUEST['hid_hasta_itin']);
$paramIdDependencia = trim($_REQUEST['opt_depe']);
$paramIdCategoria = trim($_REQUEST['tipo_art']);
$idsMateriales = $_REQUEST['idsMateriales'];

/*********************************************************************************
**  Verificar si se ha solicitado realizar una búsqueda con uno o más criterios **
*********************************************************************************/
if ($paramBuscar == 2)
{	
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
	if($paramIdCategoria !== null && $paramIdCategoria != ""  && $paramIdCategoria != "0")
	{
		$whereInterno = "AND categoria.tp_id = '".$paramIdCategoria."'";
	}
	if($idsMateriales !== null && is_array($idsMateriales) && count($idsMateriales) > 0)
	{
		$whereInterno .= "AND item.id IN ('".implode("', '", $idsMateriales)."')";
	}
	
	$whereExterno = "";
	if($paramIdDependencia !== null && $paramIdDependencia != ""  && $paramIdDependencia != "0")
	{
		$whereExterno = "AND dependencia.depe_id = '".$paramIdDependencia."'";
	}
	
	$query = "
		SELECT
			dependencia.depe_id AS dependencia_id,
			dependencia.depe_nombre AS dependencia_nombre,
			dependencia.depe_id_sup AS dependencia_id_padre,
			dependencia.depe_nivel AS dependencia_nivel,
			SUM(salidas.monto) AS monto
		FROM
			(
					SELECT
						SUM (
							CASE
								WHEN salida.tipo = 'S' THEN
									(salida_detalle.cantidad * entrada_detalle.precio)
								ELSE
									(salida_detalle.cantidad * entrada_detalle.precio) * -1
							END
						) AS monto,
						salida.depe_entregada AS id_dependencia
					FROM
						sai_arti_acta_almacen salida
						INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
						INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
						INNER JOIN sai_item item ON (item.id = item_articulo.id)
						INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = item_articulo.tipo)
						INNER JOIN sai_arti_almacen entrada_detalle
							ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
					WHERE
						salida.esta_id <> 15
						AND salida.entregado_a != '-1'
						AND salida.fecha_acta BETWEEN
							TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
								AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
						".$whereInterno."
					GROUP BY
						salida.depe_entregada
						
				UNION
				
					SELECT
						SUM (
							CASE
								WHEN salida.tipo = 'S' THEN
									(salida_detalle.cantidad * entrada_detalle.precio)
								ELSE
									(salida_detalle.cantidad * entrada_detalle.precio) * -1
							END
						) AS monto,
						CASE
							WHEN (categoria.tp_id='1') THEN '450' 
							WHEN (categoria.tp_id='2') THEN '550' 
							WHEN (categoria.tp_id='7' or categoria.tp_id='11') THEN '453' 
							WHEN (categoria.tp_id='8') THEN '600'
							WHEN (categoria.tp_id='3') THEN '250'
							--ELSE '450'
						END AS id_dependencia
					FROM
						sai_arti_acta_almacen salida
						INNER JOIN sai_arti_salida salida_detalle ON (salida_detalle.n_acta = salida.amat_id)
						INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = salida_detalle.arti_id)
						INNER JOIN sai_item item ON (item.id = item_articulo.id)
						INNER JOIN sai_arti_tipo categoria ON (categoria.tp_id = item_articulo.tipo)
						INNER JOIN sai_arti_almacen entrada_detalle
							ON (entrada_detalle.alm_id = salida_detalle.alm_id AND entrada_detalle.arti_id = salida_detalle.arti_id)
					WHERE
						salida.esta_id <> 15
						AND salida.entregado_a = '-1'
						AND salida.fecha_acta BETWEEN
							TO_DATE('".$paramFechaInicio."', 'DD/MM/YYYY')
								AND TO_TIMESTAMP('".$paramFechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
						".$whereInterno."
					GROUP BY
						categoria.tp_id
						
			) salidas
			INNER JOIN sai_dependenci dependencia ON (dependencia.depe_id = salidas.id_dependencia)
		WHERE
			salidas.id_dependencia IS NOT NULL
			".$whereExterno."
		GROUP BY
			dependencia.depe_id,
			dependencia.depe_nombre,
			dependencia.depe_id_sup,
			dependencia.depe_nivel
		ORDER BY
			dependencia_nombre
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
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php echo 'xmlns="http://www.w3.org/1999/xhtml"';?>>
	<head>
		<title>Reporte de Movimientos de Material</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		
		<link type="text/css" href="../../../../css/plantilla.css" rel="stylesheet" />
		<link type="text/css" href="../../../../css/safi0.2.css" rel="stylesheet" />
		<link type="text/css" href="../../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		<link href="../../../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
		<style>
			.nombreResponsable{
				background-color: inherit;
			}
			.ui-autocomplete {
				max-height: 110px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				/* add padding to account for vertical scrollbar */
				padding-right: 20px;
			}
			/* IE 6 doesn't support max-height
			 * we use height instead, but this forces the menu to always be this tall
			 */
			* html .ui-autocomplete {
				height: 120px;
			}
		</style>
		
		<script type="text/javascript" src="../../../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript">
			<?php
				$arrMateriales = array();
				if(is_array($materiales) && count($materiales) > 0){
					reset($materiales);
					foreach ($materiales AS $material)
					{
						$materialUTF8 = clone $material;
						$materialUTF8->UTF8Encode();
						$arrMateriales[$materialUTF8->GetId()] = $materialUTF8->ToArray();
					}
				}
				
				
				echo "
			var materiales = ".json_encode($arrMateriales).";
				";
			?>

			g_Calendar.setDateFormat('dd/mm/yyyy');

			$().ready(function()
			{
				var objInputMateriales = $("#inputMateriales");
				var idUlMateriales = "ulListaMateriales";
				var sendInputNameMateriales = "idsMateriales[]";

				// Configurar el autocomplete de materiales
				objInputMateriales.autocomplete({
					source: function(request, response){
						var seleccionados = new Array();
						$('#ulListaMateriales input[type="hidden"][name="idsMateriales\[\]"]').each(function(index, objInput){
							seleccionados[index] = objInput.value;
						});
						$.ajax({
							url: "<?php /* echo GetConfig("siteURL")."/acciones/" */ ?>../../../../acciones/item.php",
							dataType: "json",
							data: {
								accion: "SearchItems",
								key: request.term,
								tipoItem: <?php echo EntidadItem::TIPO_MATERIAL ?>,
								seleccionados: seleccionados
							},
							success: function(json){
								var index = 0;
								var items = new Array();

								$.each(json.listaItems, function(idItem, objItem){
									items[index++] = {
											id: idItem,
											label: idItem + ": " + objItem.nombre,
											value: idItem + ": " + objItem.nombre
									};
								});
								response(items);
							}
						});
					},
					minLength: 1,
					select: function(event, ui)
					{
						// Eliminar el material que se encuentre dentro de la lista
						$('#'+idUlMateriales).empty();
						
						llenarListaDiamante({
							id: ui.item.id,
							label: ui.item.label,
							idUl: idUlMateriales,
							sendInputName: sendInputNameMateriales
						});
						objInputMateriales.val('');
						return false;
					}
				});


				// Llenar la lista de materiales
				if(materiales != null){
					$.each(materiales, function(idMaterial, objMaterial){
						var label = idMaterial + ": " + objMaterial.nombre;
						
						llenarListaDiamante({
							id: idMaterial,
							label: label,
							idUl: idUlMateriales,
							sendInputName: sendInputNameMateriales
						});
						
					});
				}
				
			});

			function llenarListaDiamante(params)
			{
				var objUl = $('#' + params['idUl']);
				var sendInputName = params['sendInputName'];
				var idItem = params['id'];
				var labelItem = params['label'];

				var objLi = document.createElement("li");

				// crear el tag input hidden con el id del item, que será enviado al servidor
				var objInputHidden = document.createElement("input");
				objInputHidden.setAttribute('type', 'hidden');
				objInputHidden.setAttribute('name', sendInputName);
				objInputHidden.setAttribute('value', idItem);
				objLi.appendChild(objInputHidden);

				// crear el div con el label del item
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'col1');
				objDiv.appendChild(document.createTextNode(labelItem));
				objLi.appendChild(objDiv);

				// crear el div con el link eliminar
				var objA = document.createElement('a');
				objA.setAttribute('href', 'javascript:void(0);');
				objA.appendChild(document.createTextNode('Eliminar'));

				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'col2');
				objDiv.appendChild(objA);
				objLi.appendChild(objDiv);

				// asociar el evento click(eliminar)
				objA = $(objA);
				objA.unbind('click');
				objA.bind('click', function(){
					$(objLi).remove();
				});
				
				// div con las clase clear
				var objDiv = document.createElement('div');
				objDiv.setAttribute('class', 'clear');
				objLi.appendChild(objDiv);
				
				// agregar el tag li al tag ul
				objUl.append(objLi);
			}
		
			function ejecutar()
			{
				var inputsMateriales = $('[name="idsMateriales\[\]"]');
				if((inputsMateriales.length > 0) && (document.form.tipo_art.value!='0') )
				{
					window.alert("Seleccione la categoria o descripci\u00F3n del art\u00EDculo, no ambos");
					$('#inputMateriales').focus();
					return;
				}
				if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value==''))
				{
					document.form.hid_buscar.value=1;
				}
				else{
					document.form.hid_buscar.value=2;
				}
				//window.location="gastos.php?txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&hid_buscar="
				//+document.form.hid_buscar.value+"&depe="+codigo3+"&desc_arti="+codigo4+"&tipo="+codigo5
				document.form.submit();
			}
		
			function exportar(codigo1,codigo2,codigo3,codigo5)
			{
				var inputsMateriales = $('[name="idsMateriales\[\]"]');
				if((inputsMateriales.length > 0) && (document.form.tipo_art.value!='0') )
				{
					window.alert("Seleccione la categoria o descripci\u00F3n del art\u00EDculo, no ambos");
					$('#inputMateriales').focus();
					return;
				}
				
				if ((codigo1=='') && (codigo2==''))
				{
					document.form.hid_buscar.value=1;
				}
				else{
					document.form.hid_buscar.value=2;
				}

				var strIdsMateriales = "";
				
				$('input[name="idsMateriales\[\]"]').each(function(index, objInputIdMaterial){
					strIdsMateriales += "&idsMateriales[]=" + $(objInputIdMaterial).val();
				});

				window.location="gastosXLS.php?fechaInicio="+codigo1+"&fechaFin="+codigo2+"&hid_buscar="
					+document.form.hid_buscar.value+"&idDependencia="+codigo3+strIdsMateriales
					+"&idCategoria="+document.form.tipo_art.value;
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
					((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
				{
					alert("La fecha inicial no debe se mayor a la fecha final"); 
					document.form.hid_hasta_itin.value='';
					return;
				}
			}
		</script>
	</head>
	
	<body class="normal">
		<form name="form" action="gastos.php" method="post">
			<br />
			<table width="677" align="center" background="../../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray" > 
					<td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Gastos por dependencia</td>
				</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Fecha:</td>
					<td width="406" class="normalNegrita">
						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
						<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							value="<?php echo $paramFechaInicio == "" ? "" : $paramFechaInicio ?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
							<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
						</a>
						<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							value="<?php echo $paramFechaFin == "" ? "" : $paramFechaFin?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
							<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
						</a>
						<span class="peq_naranja">  (*)</span>
					</td>
				</tr>
				<tr>
					<td width="259" height="34" class="normalNegrita">Dependencia:</td><td>
						<select name="opt_depe" class="normal" id="opt_depe">
							<option value="0">[Todas]</option>
							<?php
							foreach ($dependencias AS $dependencia)
							{
								echo '
							<option value="'.$dependencia['dependencia_id'].'"
								'.(($dependencia['dependencia_id'] == $paramIdDependencia) ? ' selected="selected"' : '').'
							>
								'.$dependencia['dependencia_nombre'].'
							</option>
								';
							}
							?>
						</select>
						<input type="hidden" name="hid_buscar" id="hid_buscar" value="0" />
					</td>
				</tr>
				<tr>
					<td height="32" valign="middle"><div class="normalNegrita">Categor&iacute;a</div></td>
					<td height="32" colspan="3" valign="middle">
						<select name="tipo_art" class="normal" id="tipo_art">
							<option value="0">[Seleccione]</option>
							<?php							 
									foreach ($categoriasArticulos AS $categoriaArticulo)
									{
										echo '
							<option value="'.$categoriaArticulo["categoria_id"].'"
								'.(($categoriaArticulo['categoria_id'] == $paramIdCategoria) ? ' selected="selected"' : '').'
							>
								'.$categoriaArticulo["categoria_descripcion"].'
							</option>
										';
									}
							?>
						</select> 
					</td>
				</tr>
				<tr>
					<td width="222" class="normalNegrita" style="vertical-align: top;">Material:</td>
					<td width="316" style="font-family: Verdana,Geneva,Arial,Helvetica,sans-serif; font-size: 11px;">
						<input <?php echo 'autocomplete="off"';?> type="text" id="inputMateriales"
							class="normalNegro" style="width: 494px;"
						/>
						<div class="listaDiamante" style="height: 25px;">
							<ul id="ulListaMateriales"></ul>
						</div>
					</td>
				</tr>
				<tr>
					<td height="52" colspan="3" align="center">
						<div align="center">
							<input type="button" class="normalNegro" value="Buscar" onclick="javascript:ejecutar()" />
							<input type="button" class="normalNegro"  value="Exportar"
								onclick="javascript:exportar(document.form.txt_inicio.value,document.form.hid_hasta_itin.value, 
									document.form.opt_depe.value,document.form.tipo_art.value)" />
						</div>  
					</td>
				</tr>
			</table>
		</form>
		<br />
		
		<?php
			if(isset($paramBuscar) && $paramBuscar == 2)
			{
				if (count($reporte) == 0)
				{
					echo '
		<table width="677" align="center">
			<tr>
				<td>
					<span class="normalNegrita" style="color: #003399">
						No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado.
					</span>
				</td>
			</tr>
		
		</table>
					';
				} else {
					
		?>
		<table style="width: 678px;" background="../../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
			<tr class="td_gray">
				<td class="normalNegrita" style="width: 670px; text-align: center;">
					Gastos por dependencia del <?php
						echo $paramFechaInicio . " al " . $paramFechaFin
						. (($paramIdCategoria != null && $paramIdCategoria != "" && isset($categoriasArticulos[$paramIdCategoria]))
							? " de " . $categoriasArticulos[$paramIdCategoria]['categoria_descripcion']  : "")
						. (($nombreItem != null && $nombreItem != "") ? " de " . $nombreItem : "")
					?>
				</td>
			</tr>
			<tr>
				<td class="normalNegrita" style="text-align: center;">&nbsp;</td>
			</tr>
			<tr>
				<td class="normalNegrita" style="height: 49px; text-align: center;">
					<table class="tablaalertas" style="width: 500px;" border="0" align="center">
						<tr>
							<th style="background-color: #F0F0F0; width: 135px;" scope="col">Dependencia</th>
							<th style="background-color: #F0F0F0; width: 79px;" scope="col">Monto total en Bs.</th>
						</tr>
						<?php
					
					$totalGastos = 0;
					
					foreach ($reporte AS $row)
					{
						$totalGastos += $row['monto'];
						?>
						<tr>
							<td class="normal" style="text-align: left;">
								<a href="gastos_detalle.php?fechaInicio=<?php echo $paramFechaInicio;?>
									&fechaFin=<?php echo $paramFechaFin;?>&idDependencia=<?php echo $row['dependencia_id'];
									?><?php
										if(is_array($idsMateriales) && count($idsMateriales) > 0)
											echo "&idsMateriales[]=" . implode("&idsMateriales[]=", $idsMateriales) . "";
									?>&idCategoria=<?php echo $paramIdCategoria;?>"
								>
									<?php echo $row['dependencia_nombre'];?>
								</a>
							</td>      
							<td class="normal" style="text-align: right; width: 80px;">
								<?php echo number_format($row['monto'], 2, ',', '.') ?>
							</td>
						</tr>
						<?php	
					}
						?>
						<tr>		 
							<td style="text-align: left;">
								<a href="gastos_detalle.php?fechaInicio=<?php echo $paramFechaInicio;?>&fechaFin=<?php
									echo $paramFechaFin;?>&idDependencia=<?php echo $paramIdDependencia;?><?php
										if(is_array($idsMateriales) && count($idsMateriales) > 0)
											echo "&idsMateriales[]=" . implode("&idsMateriales[]=", $idsMateriales) . "";
									?>&idCategoria=<?php echo $paramIdCategoria;?>">Total</a>
							</td>
							
							<td style="text-align: right;"><?php echo number_format($totalGastos, 2, ',', '.') ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" class="normalNegrita" colspan="2">
					<br/>
					<span class="peq_naranja normal">Detalle generado  el d&iacute;a
						<?=date("d/m/y")?> a las <?=date("h:i:s")?>
					</span>
					<br /><br /><br /><br />
					<a href="gastos_pdf.php?fechaInicio=<?php echo $paramFechaInicio;?>&fechaFin=<?php
						echo $paramFechaFin;?>&idDependencia=<?php echo $paramIdDependencia;
						?><?php
							if($idsMateriales != null && is_array($idsMateriales) && count($idsMateriales) > 0)
								echo "&idsMateriales[]=" . implode("&idsMateriales[]=", $idsMateriales) . "";
						?>&idCategoria=<?php echo $paramIdCategoria;?>">
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
	</body>
</html>