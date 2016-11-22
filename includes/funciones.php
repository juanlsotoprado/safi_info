<?php

/* Funcion para el envio de email con class.phpmailer.php */
function enviarEmail($de, $nombreDe, $para, $nombrePara, $copia, $nombreCopia, $copiaOculta, $nombreCopiaOculta, $asunto, $cuerpo, $attachList){
	$mail = new PHPMailer();
	$mail->SetLanguage('es');
	$mail->From     = $de;
	$mail->FromName = $nombreDe;
	$mail->Host     =  "correos.infocentro.gob.ve";
	$mail->Mailer   =  "smtp";
	$mail->AddAddress($para,$nombrePara);
	if($copia && $copia!=""){
		$mail->AddCC($copia,($nombreCopia && $nombreCopia!="")?$nombreCopia:"");
	}
	if($copiaOculta && $copiaOculta!=""){
		$mail->AddBCC($copiaOculta,($nombreCopiaOculta && $nombreCopiaOculta!="")?$nombreCopiaOculta:"");
	}
	$mail->Subject  =  $asunto;
	$mail->Body     = $cuerpo;
	$mail->isHTML(false);
	//error_log(print_r($attachList,true));
	if($attachList && sizeof($attachList)>0){
		$i = 0;
			
			/*
		 * foreach($attachList as $arreglos) { //error_log(print_r($arreglos,true)); foreach($arreglos as $index => $valor) { //error_log("aqui"); //error_log(print_r($valor,true)); //error_log(print_r($valor[$index],true)); $mail->AddAttachment($valor[$index][0], $valor[$index][1]); }
		 */
		foreach ( $attachList as $arreglos ) {
			
			foreach ( $arreglos as $valor ) {
				
				// aqui los otros
				
				if (is_array ( $valor )) {
					foreach ( $valor as $index2 => $valor2 ) {
						
						$mail->AddAttachment($valor2[0], $valor2[1]);
						
						//error_log ($valor2[0] . " , " . $valor2[1]);
					}
				}
			}

			$i ++;
		}	
	
	}
	/*if($attachList && sizeof($attachList)>0){
		$i = 0;
		while($i<sizeof($attachList)){
			$mail->AddAttachment($attachList[$i][0], $attachList[$i][1]);
			$i++;
		}
	}*/
	$mail->Send();
}

//Para buscar el grupo particular 
function buscar_grupo_particular($dependencia_solicitante_in,$perfiles_general_in){
	global $conexion;
	$grupo_particular_gd = "";
	if(strpos($perfiles_general_in,"/") !== false){
		$existe = 0;
		$array_perfiles_gral = split("/",$perfiles_general_in);
		foreach($array_perfiles_gral as $perfil_gral) {
			//Buscar si el cargo pertenece a la dependencia
			$sql_g = " SELECT carg_id FROM sai_depen_cargo WHERE depe_id='$dependencia_solicitante_in' AND carg_id='$perfil_gral' ";
			$resultado_g = pg_exec($sql_g) or die("Error al mostrar");
			if ($row_g = pg_fetch_array($resultado_g)) {
				$cargo_general = $row_g["carg_id"];
				$existe = 1;
			}		
			if($existe == 1){			
				if(strpos($cargo_general,"000") !== false){		
					$grupo_particular_gd = substr($cargo_general,0,2).$dependencia_solicitante_in ;			
				}else{							
					$grupo_particular_gd = $cargo_general;				
				}		
				break;			
			}			
		}			
	}else{
		if(strpos($perfiles_general_in,"000") !== false){
			//Buscar si el cargo pertenece a la dependencia
			$sql_g = " SELECT carg_id FROM sai_depen_cargo WHERE depe_id='$dependencia_solicitante_in' AND carg_id='$perfiles_general_in' ";		
			$resultado_g = pg_exec($sql_g) or die("Error al mostrar");
			if ($row_g = pg_fetch_array($resultado_g)) {
				$cargo_general = $row_g["carg_id"];
				$existe = 1;
			}
			$grupo_particular_gd = substr($perfiles_general_in,0,2).$dependencia_solicitante_in ;
		}else {		
			$grupo_particular_gd = $perfiles_general_in;				
		}		
	}
	return 	$grupo_particular_gd;
}

//Para buscar el grupo particular en la dependencia
function buscar_grupo_particular_dependencia($dependencia_solicitante_in,$perfiles_general_in){
	global $conexion;
	$existe = 0;
	while($existe==0){
		$grupo_part = buscar_grupo_particular($dependencia_solicitante_in,$perfiles_general_in);
		if ($grupo_part!="") {
			$existe = 1;
		}else{
			//Buscar el nivel del cargo
			$sql = " SELECT depe_id_sup FROM sai_dependenci WHERE depe_id='$dependencia_solicitante_in' ";
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if($row = pg_fetch_array($resultado)){
				$dependencia_solicitante_in = $row["depe_id_sup"];
			}
		}		
	}
	return $grupo_part;
}

//Para buscar el nombre de todos los perfiles de un grupo particular usa para imprimir la cadena)
function buscar_nombre_perfiles_grupo($grupo_general){
	global $conexion;			
	$nombres_perfiles = "";
	$perfiles_general = "";
	$sql = " SELECT * FROM sai_buscar_perfil_grupo('".$grupo_general."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$perfiles_general = $row["resultado"];
	}
	$cont = 0;
	if (strpos($perfiles_general,"/") !== false) {
		$array_perfiles_gral = split("/",$perfiles_general);
		foreach($array_perfiles_gral as $perfil_gral) {
			$sql = " SELECT * FROM sai_buscar_cargo_depen('".$perfil_gral."') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
			if ($row = pg_fetch_array($resultado)) {
				if ($cont>0) {
					$nombres_perfiles .= " / ";				
				}
				$nombres_perfiles .= $row["resultado"];
			}
			$cont++;	
		}
	}else{
		$sql = " SELECT * FROM sai_buscar_cargo_depen('".$perfiles_general."') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
		if ($row = pg_fetch_array($resultado)) {
			$nombres_perfiles .= $row["resultado"];
		}
	}
	return $nombres_perfiles;
}

function limpiarTemporalesPdf($directorio) {
	$t=time();
	$h=opendir($directorio);
	while ($file=readdir($h)) {
		if ( (substr($file,0,3)=="tmp") && (substr($file,-4)==".pdf") ) {
			$path = $directorio."/".$file;
			if ( ($t - filemtime($path)) >3600) {
				chmod($path,0777); 
   				unlink($path);
			}		
		}
	}
	closedir($h);
}

function cadenaAMayusculas($cadena){
	$cadena = str_replace(utf8_decode("á"),utf8_decode("Á"),$cadena);
	$cadena = str_replace(utf8_decode("é"),utf8_decode("É"),$cadena);
	$cadena = str_replace(utf8_decode("í"),utf8_decode("Í"),$cadena);
	$cadena = str_replace(utf8_decode("ó"),utf8_decode("Ó"),$cadena);
	$cadena = str_replace(utf8_decode("ú"),utf8_decode("Ú"),$cadena);
	$cadena = str_replace(utf8_decode("à"),utf8_decode("À"),$cadena);
	$cadena = str_replace(utf8_decode("è"),utf8_decode("È"),$cadena);
	$cadena = str_replace(utf8_decode("ì"),utf8_decode("Ì"),$cadena);
	$cadena = str_replace(utf8_decode("ò"),utf8_decode("Ò"),$cadena);
	$cadena = str_replace(utf8_decode("ù"),utf8_decode("Ù"),$cadena);
	$cadena = str_replace(utf8_decode("û"),utf8_decode("Û"),$cadena);
	$cadena = str_replace(utf8_decode("ñ"),utf8_decode("Ñ"),$cadena);
	return strtoupper($cadena);
}

function cadenaAMinusculas($cadena){
	$cadena = str_replace(utf8_decode("Á"),utf8_decode("á"),$cadena);
	$cadena = str_replace(utf8_decode("É"),utf8_decode("é"),$cadena);
	$cadena = str_replace(utf8_decode("Í"),utf8_decode("í"),$cadena);
	$cadena = str_replace(utf8_decode("Ó"),utf8_decode("ó"),$cadena);
	$cadena = str_replace(utf8_decode("Ú"),utf8_decode("ú"),$cadena);
	$cadena = str_replace(utf8_decode("À"),utf8_decode("à"),$cadena);
	$cadena = str_replace(utf8_decode("È"),utf8_decode("è"),$cadena);
	$cadena = str_replace(utf8_decode("Ì"),utf8_decode("ì"),$cadena);
	$cadena = str_replace(utf8_decode("Ò"),utf8_decode("ò"),$cadena);
	$cadena = str_replace(utf8_decode("Ù"),utf8_decode("ù"),$cadena);
	$cadena = str_replace(utf8_decode("Û"),utf8_decode("û"),$cadena);
	$cadena = str_replace(utf8_decode("Ñ"),utf8_decode("ñ"),$cadena);
	return strtolower($cadena);
}

function validarTexto($campo){
	$caracteres = utf8_decode(" abcdefghijklmnopqrstuvwxyzáéíóúñABCDEFGHIJKLMNOPQRSTUVWXYZÁÉÍÓÚÑ0123456789<>|@;:,.º/!¡$%&()=+-*%¿?_\n");
	$ubicacion = "";

	$resultado = $campo;
	for ($indice=0; $indice < strlen($campo); $indice++) {
		$ubicacion = substr($campo,$indice,1);
		if(strpos($caracteres, $ubicacion) === false){
			$resultado = str_replace($ubicacion,"",$resultado);
		}
	}
	return $resultado;
}

function quitarAcentosMayuscula($cadena){
	$cadena = str_replace(utf8_decode("á"),utf8_decode("A"),$cadena);
	$cadena = str_replace(utf8_decode("é"),utf8_decode("E"),$cadena);
	$cadena = str_replace(utf8_decode("í"),utf8_decode("I"),$cadena);
	$cadena = str_replace(utf8_decode("ó"),utf8_decode("O"),$cadena);
	$cadena = str_replace(utf8_decode("ú"),utf8_decode("U"),$cadena);
	$cadena = str_replace(utf8_decode("Á"),utf8_decode("A"),$cadena);
	$cadena = str_replace(utf8_decode("É"),utf8_decode("E"),$cadena);
	$cadena = str_replace(utf8_decode("Í"),utf8_decode("I"),$cadena);
	$cadena = str_replace(utf8_decode("Ó"),utf8_decode("O"),$cadena);
	$cadena = str_replace(utf8_decode("Ú"),utf8_decode("U"),$cadena);
	$cadena = str_replace(utf8_decode("ñ"),utf8_decode("Ñ"),$cadena);
	$cadena = str_replace(utf8_decode("Ñ"),utf8_decode("Ñ"),$cadena);
	return strtoupper($cadena);
}

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle){
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

function dontTouchThePrecision($val, $pre = 0){
    return (int) ($val * pow(10, $pre)) / pow(10, $pre);
}

?>
