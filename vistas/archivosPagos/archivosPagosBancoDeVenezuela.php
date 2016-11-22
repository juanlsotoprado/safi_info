<?php
	// Establecer los encabezados para indicar al navegador que se va a enviar un archivo de texto plano.
	header ('Content-type: text/plain; charset=ISO-8859-1');
	header('Content-Disposition: attachment; filename="pago_avance.txt"');
	
	// Para evitar la cache del navegador o proxy
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
	
	$datosArchivosPagos = $GLOBALS['SafiRequestVars']['datosArchivosPagos'];
	
	try {
		if($datosArchivosPagos == null)
			throw new Exception("El paŕametro datosArchivosPagos es nulo");
		
		if(!is_array($datosArchivosPagos))
			throw new Exception("El paŕametro datosArchivosPagos no es un array");
			
		if(count($datosArchivosPagos) == 0)
			throw new Exception("El paŕametro datosArchivosPagos está vacío");

		if(!isset($datosArchivosPagos["personas"]))
			throw new Exception("El paŕametro datosArchivosPagos[\"personas\"] es nulo");
			
		if(!isset($datosArchivosPagos["personas"]))
			throw new Exception("El paŕametro datosArchivosPagos[\"personas\"] no es un array");
			
		if(count($datosArchivosPagos["personas"]) == 0)
			throw new Exception("El paŕametro datosArchivosPagos[\"personas\"] está vacío");
		
		if(!isset($datosArchivosPagos["fechaAbono"]))
			throw new Exception("El paŕametro datosArchivosPagos[\"fechaAbono\"] es nullo");
			
		if(trim($datosArchivosPagos["fechaAbono"]) == '')
			throw new Exception("El paŕametro datosArchivosPagos[\"fechaAbono\"] está vacío");
			
		// Obtener cada componente de la fecha de abono en un array
		$arrFechaAbono = explode('/', $datosArchivosPagos["fechaAbono"]);
		// Obtener la lista de las personas objetos de la transferencia
		$personas = $datosArchivosPagos["personas"];
		
		$strDetalles = "";
		
		foreach ($personas as $persona)
		{
			$arrApellidos = explode(' ', trim($persona["apellidos"]));
			$arrNombres = explode(' ', trim($persona["nombres"]));
			$nombre = mb_strtoupper($arrApellidos[0] . ' ' . $arrNombres[0], "ISO-8859-1");
			
			$strDetalles .= sprintf("%1.1s","0");  // Campo fijo
			$strDetalles .= sprintf("%'020.20s", $persona["numeroCuenta"]); // Número de cuenta del responsable
			$strDetalles .= sprintf("%'011.11s", floor($persona["montoTotal"] * 100));  // Monto del responsable
			$strDetalles .= sprintf("%'04.4s","0770"); // Campo fijo
			$strDetalles .= utf8_encode(sprintf("%-40.40s", $nombre));  // Apellidos y nombres del responsable
			$strDetalles .= sprintf("%'010.10s", $persona["id"]);  // Cédula del responsable
			$strDetalles .= sprintf("%'06.6s","003291");  // Campo fijo
			$strDetalles .= "\r\n";
		}
		
		//Construir el encabezado del archivo de pago
		$strEncabezado = sprintf("%1.1s","H");  // Campo fijo
	    $strEncabezado .= sprintf("%-40.40s","FUNDACION INFOCENTRO");  // Nombre de la empresa. Longitud=40
	    $strEncabezado .= sprintf("%'020.20s","01020552270000032382"); // Número de cuenta. Longitud=20
	    $strEncabezado .= sprintf("%2.2s","01");  // Campo fijo
		// Fecha de abono dd/mm/aa
	    $strEncabezado .= sprintf("%8.8s",$arrFechaAbono[0]."/".$arrFechaAbono[1]."/".substr($arrFechaAbono[2], -2));
	    $strEncabezado .= sprintf("%'013.13s", floor($datosArchivosPagos["montoTotal"] * 100));  // Monto total del pago
	    $strEncabezado .= sprintf("%'05.5s","03291");  // Campo fijo
	    $strEncabezado .= "\r\n";  // Salto de línea
		
	    // Escribir los datos en el archivo
		$handle = fopen("php://output", 'w');
					    
		fwrite($handle, $strEncabezado);
		fwrite($handle, $strDetalles);
		
		fclose($handle);
		
	} catch (Exception $e) {
		error_log($e, 0);
	}
?>