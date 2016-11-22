<div align="center">
<?
foreach ($opciones_doc as $registro) {
	
	$id_opcion = trim($registro[0]);
	$objeto_siguiente_id = trim($registro[1]);
	$cadena_padre_id = trim($registro[2]);
	$cadena_siguiente_id = trim($registro[3]);
	
	$sql = "select * from sai_buscar_opcion('$id_opcion') as (nombre_opcion varchar, desc_opcion varchar)";	
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$nombre_opcion = $row["nombre_opcion"];
		$nombre_boton = strtolower($row["desc_opcion"]);
	}
	$cod_doc = trim($cod_doc);
	$opciones_def = "'$cod_doc','$request_id_tipo_documento',$id_opcion,$objeto_siguiente_id,$cadena_siguiente_id,$request_id_objeto";
	?>	
	<input type="button" value="Procesar" onclick="javascript:revisar_doc(<? echo $opciones_def; ?>);" />
<? } ?>
</div>