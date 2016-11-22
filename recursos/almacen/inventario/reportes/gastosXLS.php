<?php 

ob_start();
session_start();
require_once(dirname(__FILE__) . '/../../../../init.php');
require_once(SAFI_MODELO_PATH . '/item.php');
require_once(SAFI_INCLUDE_PATH . "/excel.php");
require_once(SAFI_INCLUDE_PATH . "/excel-ext.php");
require_once(SAFI_INCLUDE_PATH . "/conexion.php");
require_once(SAFI_INCLUDE_PATH . "/fechas.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../../index.php',false);
	ob_end_flush();
	exit;
}
  
if (($_GET['hid_buscar']) == 1)
{
	echo utf8_decode("<SCRIPT LANGUAGE='JavaScript'>"."alert ('Especifique un rango de fechas...');"."</SCRIPT>");
}
else if (($_GET['hid_buscar']) == 2)
{
	$paramFechaInicio = trim($_GET['fechaInicio']);
	$paramFechaFin = trim($_GET['fechaFin']);
	$paramIdDependencia = trim($_GET['idDependencia']);
	$paramIdCategoria = $_GET['idCategoria'];
	$idsMateriales = $_GET['idsMateriales'];
	
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
			//echo "Error al realizar la consulta.";
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
  
	$sql_tabla1 = "
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
	
	$contenido = array();
	$fila = 0;
	$columna = 0;
	
	$resultado_set_t1 = pg_query($conexion, $sql_tabla1) or die("Error al mostrar consulta");
	
	if (($rowt1=pg_fetch_array($resultado_set_t1)) == null)
	{
		$contenido[$fila][$columna++] = utf8_decode("No existen registros que cumplan con el criterio de búsqueda seleccionado");
	} else {
		
		$contenido[$fila][$columna++] = utf8_decode("GASTOS POR DEPENDENCIA DEL ")
			. $paramFechaInicio . utf8_decode(" AL ") . $paramFechaFin
	    	. (($nombreCategoria != null && $nombreCategoria != "") ? utf8_decode(" DE ") . $nombreCategoria : "")
	    	. (($nombreItem != null && $nombreItem != "") ? utf8_decode(" DE ") . $nombreItem : "");
	    	
	    
		$fila++;
		$columna = 0;
		
		$contenido[$fila][$columna++] = utf8_decode("DEPENDENCIA");
		$contenido[$fila][$columna++] = utf8_decode("MONTO TOTAL EN BS.");
	
		$resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta");
  
		while($row = pg_fetch_array($resultado_set_t1))  
		{
			$fila++;
			$columna = 0;
		
			$totalGastos += $row['monto'];
			$contenido[$fila][$columna++] = $row['dependencia_nombre'];
			$contenido[$fila][$columna++]=(double)number_format($row['monto'], 2, '.', '');
		} //fin del while que obtiene los datos de la consulta
	
		$fila++;
		$columna = 0;
	
		$contenido[$fila][$columna++]="Total";;
		$contenido[$fila][$columna++]=(double)number_format($totalGastos, 2, '.', '');
	} 
}

createExcel("control-gastos.xls", $contenido);

?>
