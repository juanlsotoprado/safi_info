<div align="center">
<?php
//Mostrar las opciones disponibles 
foreach ($opciones_doc_inicial as $opcion_doc) {
	
	$opcion_doc_sp = $opcion_doc[0];
	$opcion_doc_cp = $opcion_doc[1];
	
	$id_opcion =  $opcion_doc_sp[0];	
	//Si es sin proyecto
	$objeto_siguiente_id = $opcion_doc_sp[1];
	$cadena_padre_id = $opcion_doc_sp[2];
	$cadena_siguiente_id = $opcion_doc_sp[3];
	
	//Por proyecto
	$objeto_siguiente_id_proy = 0;
	$cadena_siguiente_id_proy = 0;
	$cadena_padre_id_proy = 0;
	if ($opcion_doc_cp != NULL) {
		$objeto_siguiente_id_proy = $opcion_doc_cp[1];
		$cadena_padre_id_proy = $opcion_doc_cp[2];
		$cadena_siguiente_id_proy = $opcion_doc_cp[3];
	}
	
	$sql = "select * from sai_buscar_opcion('$id_opcion') as (nombre_opcion varchar, desc_opcion varchar)";	
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$nombre_opcion = $row["nombre_opcion"];
		$nombre_boton = strtolower($row["desc_opcion"]);
	}
	$opciones_def = "'$request_id_tipo_documento',$id_opcion,$objeto_siguiente_id,$objeto_siguiente_id_proy,$cadena_siguiente_id,$cadena_siguiente_id_proy,$request_id_objeto";
?>
	<input type="button" value="Procesar" onclick="javascript:revisar_doc(<? echo $opciones_def; ?>);" /> 
<? } ?>
</div>	