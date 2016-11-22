<?php
/***********************************************************************/
$conexionSafi=pg_pconnect("host=150.188.85.33 port=5432 dbname=sai user=postgres password=123");
//$conexionSafi=pg_pconnect("host=150.188.84.32 port=5432 dbname=safi_03042012 user=sistemas password=d3s4rr0ll0");
/***********************************************************************/
pg_set_client_encoding($conexionSafi, "ISO-8859-1");

$query = 	"
			SELECT 
				ssc.soco_id,
				sscp.fecha,
				ssc.rebms_id,
				sri.rbms_item_arti_id AS id_item,
				sri.numero_item,
				sri.rbms_item_cantidad AS cantidad,
				sri.rbms_item_desc AS especificaciones
			FROM 
				sai_sol_coti ssc
				INNER JOIN sai_sol_coti_prov sscp ON (ssc.soco_id = sscp.soco_id)
				INNER JOIN sai_rqui_items sri ON (ssc.rebms_id = sri.rebms_id) 
			GROUP BY
				ssc.soco_id,
				sscp.fecha,
				ssc.rebms_id,
				sri.rbms_item_arti_id,
				sri.numero_item,
				sri.rbms_item_cantidad,
				sri.rbms_item_desc
			ORDER BY sscp.fecha, ssc.rebms_id, sri.numero_item
			";
$resultado = pg_exec($conexionSafi, $query);

while($row=pg_fetch_array($resultado)){
	$query = 	"INSERT INTO sai_sol_coti_item 
				(soco_id, id_item, numero_item, fecha, especificaciones, cantidad) 
				VALUES ('".$row["soco_id"]."',".$row["id_item"].",".$row["numero_item"].",'".$row["fecha"]."','".$row["especificaciones"]."',".$row["cantidad"].")";
	echo $query."<br/>";
	$resultadoInsert = pg_exec($conexionSafi, $query);
}

pg_close($conexionSafi);
?>