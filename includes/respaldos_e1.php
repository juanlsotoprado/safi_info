<?
//Se agregan los respaldos digitales
if($request_id_objeto==2) {
	$tipo_respal="Digital";
	$valida=0;
	require_once("includes/arreglos_pg.php");
	$txt_arreglo=trim($_POST['txt_arreglo_d']);
	if($txt_arreglo!="") {
		$resp = explode("/", $txt_arreglo);
		$matriz_digital=array(count($resp));
		for($i=0;$i<count($resp);$i++) {
			$matriz_digital[$i]=$resp[$i];
		}
		$arreglo_digital = convierte_arreglo ($matriz_digital);
		
		// Se guarda en la tabla de respaldos
		$sql  = "select * from  sai_modifi_respaldo('" . $arreglo_digital. "', '".$user_perfil_id. "','".$valida."', '".$tipo_respal. "','" .$cod_doc. "','" .$_SESSION['login']."')";
	
		$resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar respaldo");
		$row = pg_fetch_array($resultado_set,0); 

		if ($row[0] <> null) {
			$codigo_respal=$row[0];
		}
	}
}
//Se agregan los respaldos digitales
	$largo = $_POST['largo_dig'];
					
	   	if (isset ($_FILES["archivos"])) { # Si es que se subio algun archivo
			$msg .= "<ul>";
			foreach ($_FILES["archivos"]["error"] as $key => $error) {# Iterar sobre la colecci�n de archivos
				if ($error == UPLOAD_ERR_OK) { // Si no hay error
					
					$nombre_archivo = $HTTP_POST_FILES["archivos"]["name"][$key];
		            $tipo_archivo = $HTTP_POST_FILES["archivos"]["type"][$key]; 
		            $tamano_archivo = $HTTP_POST_FILES["archivos"]["size"][$key];
					//compruebo si las caracteristicas del archivo son las que deseo 
					
					if ($tamano_archivo > 4200000) { 
						echo utf8_decode("Se permiten archivos de 4MB máximo."); 
					}
					else {			
						$directorio = "documentos/tmp/".$_SESSION["login"]."/";
							
						if (!file_exists($directorio)) {
							mkdir($directorio); 			
						}
						if (move_uploaded_file($HTTP_POST_FILES["archivos"]["tmp_name"][$key], $directorio.$nombre_archivo)) {
						 $tipo_respal="Digital";
						 $valida=0;
				                 $tipo_respal="Digital";
				
						// Se guarda en la tabla de respaldos
						$sql  = "select * from  sai_insert_respaldo('" . $nombre_archivo. "', '".$user_perfil_id. "','".$valida."', '".$tipo_respal. "','" .$cod_doc. "','" .$_SESSION['login']."')";
						$resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar Respaldo");
						$row = pg_fetch_array($resultado_set,0); 
		
						if ($row[0] <> null) {
								$codigo_respal=$row[0];
						}
						$agregado = true;
						$flag = 2;
						//Insetar accion 
						}
						else { 
			  				 echo utf8_decode("Ocurrió algún error al subir el fichero. No pudo guardarse."); 
						} 
			}
					
		}
	}
			
}
//se agregan los respaldos fisicos
if($request_id_objeto==2)
{
	$tipo_respal="Fisico";
	$valida=0;
	require_once("includes/arreglos_pg.php");
	$txt_arreglo=trim($_POST['txt_arreglo_f']);
	if($txt_arreglo!="")
	{
		$resp = explode("/", $txt_arreglo);
		$matriz_fisico=array(count($resp));
		for($i=0;$i<count($resp);$i++)
		{
			$matriz_fisico[$i]=$resp[$i][0];
		}
		$arreglo_fisico = convierte_arreglo ($matriz_fisico);
		
		 
		// Se guarda en la tabla de respaldos
		$sql  = "select * from  sai_modifi_respaldo('" . $arreglo_fisico. "', '".$user_perfil_id. "','".$valida."', '".$tipo_respal. "','" .$cod_doc. "','" .$_SESSION['login']."')";
	//echo $sql;
		$resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar respaldo");
		$row = pg_fetch_array($resultado_set,0); 

		if ($row[0] <> null)
		{
			$codigo_respal=$row[0];
						
		}
	}
}

$largo=$_POST["largo_fis"];
$txt_arreglo=trim($_POST['txt_arreglo_f']);//echo($txt_arreglo);
if($txt_arreglo!="") {
		
	$resp = explode("/", $txt_arreglo);
	foreach ($resp as $arreglo_resp) { 
		$valida=0;
		$tipo_respal="Fisico";
		$codr_valida=0;
		
	
		$sql  = "select * from  sai_insert_respaldo('" . $arreglo_resp. "','" .$user_perfil_id. "','".$valida."', '".$tipo_respal. "','" .$cod_doc. "','" .$_SESSION['login']."')";
		$resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar respaldo");
		$row = pg_fetch_array($resultado_set,0); 
		
		if ($row[0] <> null) {
			 $codigo_respal=$row[0];
		}
	}
} 
?>