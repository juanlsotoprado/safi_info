<?php
/*include('includes/conexion.php');

//CREAR LOS CAMPOS numero_item EN sai_rqui_items Y EN sai_cotizacion_item COMO INTEGER NULL
//CORRER EL SCRIPT
//COLOCAR numero_item EN AMBOS CASOS COMO NOT NULL
//BORRAR LOS CONSTRAINTS DE CLAVES PRIMARIAS PARA ESTAS DOS TABLAS Y AGREGAR numero_item EN AMBOS CASOS
//SOBREESCRIBIR LOS STORE PROCEDURES sai_ingresar_rqui, sai_modificar_rqui, sai_insert_orden_de_compra, sai_modificar_orden_de_compra

$query = "update sai_rqui_items set numero_item = 1";
$resultadoUpdate = pg_exec($conexion, $query);

$query = "update sai_cotizacion_item set numero_item = 1";
$resultadoUpdate = pg_exec($conexion, $query);

$query = 	"SELECT ".
				"sri.rebms_id, ".
				"sri.rbms_item_arti_id ".
			"FROM ".
				"sai_rqui_items sri ".
			"ORDER BY sri.rebms_id, sri.rbms_item_arti_id ";

$resultado = pg_exec($conexion, $query);
$elementos = pg_numrows($resultado);
for($i=0;$i<$elementos;$i++){
	$row = pg_fetch_array($resultado, $i);
	$query = "UPDATE sai_rqui_items SET numero_item = ".($i+1)." WHERE rebms_id = '".$row["rebms_id"]."' AND rbms_item_arti_id = ".$row["rbms_item_arti_id"]." ";
	$resultadoUpdate = pg_exec($conexion, $query);
	
	$query =	"SELECT ".
					"sci.id_cotizacion, ".
					"sci.id_item ".
				"FROM ".
					"sai_orden_compra soc, ".
					"sai_cotizacion sc, ".
					"sai_cotizacion_item sci ".
				"WHERE ".
					"soc.rebms_id = '".$row["rebms_id"]."' AND ".
					"soc.ordc_id = sc.ordc_id AND ".
					"sc.id_cotizacion = sci.id_cotizacion AND ".
					"sci.id_item = ".$row["rbms_item_arti_id"];
	
	$resultadoCotizaciones = pg_exec($conexion, $query);
	$elementosCotizaciones = pg_numrows($resultadoCotizaciones);
	$cotizaciones = "";
	if($elementosCotizaciones>0){
		$cotizaciones = "(";
		for($j=0;$j<$elementosCotizaciones;$j++){
			$rowCotizacion = pg_fetch_array($resultadoCotizaciones, $j);
			$cotizaciones .= "'".$rowCotizacion["id_cotizacion"]."',";
		}
		$cotizaciones = substr($cotizaciones, 0, -1).")";
		
		$query = "UPDATE sai_cotizacion_item SET numero_item = ".($i+1)." WHERE id_cotizacion IN ".$cotizaciones." AND id_item = ".$row["rbms_item_arti_id"]." ";
		$resultadoUpdate = pg_exec($conexion, $query);
	}
}

pg_close($conexion);
*/
$enlace = $_REQUEST['id'];
header ("Content-Disposition: attachment; filename=$enlace ");
header ("Content-Type: application/force-download");
header ("Content-Length: ".filesize($enlace));
readfile("/var/www/safi0.2/".$enlace);
?>