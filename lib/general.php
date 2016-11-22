<?php

	define('MOSTRAR_TODAS_ACCIONES_CENTRALIZADAS', 1);
	define('MOSTRAR_TODOS_PROYECTOS', 2);

	/**
	 * Fetch a configuration variable from the store configuration file.
	 *
	 * @param string The name of the variable to fetch.
	 * @return mixed The value of the variable.
	 */
	function GetConfig($config)
	{
		if (array_key_exists($config, $GLOBALS['SAFI_CFG'])) {
			return $GLOBALS['SAFI_CFG'][$config];
		}
		return '';
	}
	
	/**
	 * Check if the passed string is indeed valid ID for an item.
	 *
	 * @param string The string to check that's a valid ID.
	 * @return boolean True if valid, false if not.
	 */
	function SafiIsId($id)
	{
		// If the type casted version fo the integer is the same as what's passed
		// and the integer is > 0, then it's a valid ID.
		if(SafiIsInt($id) && $id > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	* Robust integer check for all datatypes
	*
	* @param mixed $x
	*/
	function SafiIsInt($x)
	{
		if (is_numeric($x)) {
			return (intval($x+0) == $x);
		}

		return false;
	}
	
	function SafiEsCadenaDigitosNumericos($x){
		$arrNum = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		$esNumerico = true;
		
		for($i==0; $i<strlen($x); $i++){
			if(!in_array($x[$i], $arrNum)){
				$esNumerico = false;
			}
		}
		return $esNumerico;
	}
	
	function GetPerfilCargosGerenteDirectorByIdUserPerfil($idUserPerfil)
	{
		include_once(SAFI_ENTIDADES_PATH . '/cargo.php');
		
		$cargos = array();
		
		$idCargoSinDependencia = substr($idUserPerfil, 0, 2); 
		
		if(strcmp($idCargoSinDependencia, "37") == 0)
		{
			if(strcmp($idUserPerfil, "37200") != 0)
			{
				$cargo = new EntidadCargo();
				$cargo->SetId("46000");
				$cargos[] = $cargo;
				
				$cargo = new EntidadCargo();
				$cargo->SetId("60000");
				$cargos[] = $cargo;
			}
			else
			{
				$cargo = new EntidadCargo();
				$cargo->SetId("41000");
				$cargos[] = $cargo;
			}
		}else if(strcmp($idCargoSinDependencia, "38") == 0)
		{
			$cargo = new EntidadCargo();
				$cargo->SetId("47000");
				$cargos[] = $cargo;
		}
		else if(strcmp($idCargoSinDependencia, "68") == 0)
		{
			$cargo = new EntidadCargo();
			$cargo->SetId("65000");
			$cargos[] = $cargo;
		}
		
		return $cargos;
	}
	
	function GetPerfilCargoPesidente(){
		$cargo = new EntidadCargo();
		$cargo->SetId("65150");
		
		return $cargo; 
	}
	
	function GetPerfilCargoAdministracicionYFinanzas(){
		$cargo = new EntidadCargo();
		$cargo->SetId("46450");
		
		return $cargo; 
	}
	
	function GetCargoFundacionFromIdPerfil($idPerfil){
		return substr($idPerfil, 0, 2);
	}
	
	function GetIdDependenciaFromIdPerfil($idPerfil){
		return substr($idPerfil, 2, 3);
	}
	
	function DependenciaPuedeMostrarTodos($idDependencia, $recursoAMostrar)
	{
		$result = false;
		switch($recursoAMostrar){
			case MOSTRAR_TODAS_ACCIONES_CENTRALIZADAS:
				switch($idDependencia){
					case "150": // Presidencia
					case "350": // Dirección Ejecutiva
					case "400": // Oficina de Planificación Presupuesto y Control
					case "450": // Dirección de oficina de Administración Financiera
					case "452": // Dirección de oficina de Administración Financiera
					case "200": // Consultoría Jurídica
					case "500": // Talento humano
						$result = true;
						break;
				}
				break;
			case MOSTRAR_TODOS_PROYECTOS:
				switch($idDependencia){
					case "150": // Presidencia
					case "350": // Dirección Ejecutiva
					case "400": // Oficina de Planificación Presupuesto y Control
					case "450": // Dirección de oficina de Administración Financiera						
					case "452": // Dirección de oficina de Administración Financiera						
					case "500": // Talento humano
						$result = true;
						break;
				}
				break;
		}
		return $result;
	}
	
	function GetIdRendicionByIdViatico($idViatico)
	{
		$idRendicion = false;
		if(
			!is_null($idViatico) && 
			($idViatico=trim($idViatico)) != '' && 
			strlen($idViatico)>=11 && 
			strpos($idViatico, "vnac-") === 0 
		){
			$arrIdViatico = explode("-", $idViatico);
			$idRendicion = GetConfig("preCodigoRendicionViaticoNacional") . GetConfig("delimitadorPreCodigoDocumento") . $arrIdViatico[1];
		}
		
		return $idRendicion;
	}
	
	function CalcularMontoTotalAsignacionesViaticoNacional(array $viaticoRespAsignaciones = null)
	{
		$totalGastos = 0.0;
		if($viaticoRespAsignaciones != null){
			foreach($viaticoRespAsignaciones as $viaticoRespAsig){
				if($viaticoRespAsig instanceof EntidadViaticoResponsableAsignacion){
					$totalGastos += $viaticoRespAsig->GetMonto() * $viaticoRespAsig->GetUnidades();
				} 
			}
		}
		return $totalGastos;
	}
	
	function ToHtmlEncode($str)
	{
		$str = ($str != null) ? $str : "";
		//$str = str_replace(array(" "), "&nbsp;", $str);
		$str = str_replace(array("\r\n", "\n", "\r"), "<br/>", $str);
		return  $str;
	}
	
	function GetDocumentosSoportesMemos($idDocumentoFuente)
	{
		include_once(SAFI_MODELO_PATH. '/documentoSoporte.php');
		include_once(SAFI_MODELO_PATH. '/memo.php');
		
		// Para los documento de soporte
		$documentoSoporte = SafiModeloDocumentoSoporte::GetDocumentoSoporte(array("idDocumentoFuente" => $idDocumentoFuente));
		
		$memos = null;
		if(
			$documentoSoporte != null
			&& ($idsDocumentosSoportes=$documentoSoporte->GetIdsDocumentosSoportes()) != null
			&& is_array($idsDocumentosSoportes)
			&& count($idsDocumentosSoportes) > 0
		){
			$memos = SafiModeloMemo::GetMemos(array("idsMemos" => $idsDocumentosSoportes));
		}
		return $memos;
	}
	
	function GetDatosRevisionesDocumento($idDocumento)
	{
		include_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
		include_once(SAFI_MODELO_PATH. '/empleado.php');
		include_once(SAFI_MODELO_PATH. '/cargo.php');
		include_once(SAFI_MODELO_PATH. '/dependencia.php');
		include_once(SAFI_MODELO_PATH. '/wfopcion.php');
		
		$datosRevisionesDocumento = null;
		
		$arrRevisionesDoc = SafiModeloRevisionesDoc::GetRevisionesDoc(array('idDocumento' => $idDocumento));
				
		$usuaLogins = array();
		$cargoFundaciones = array();
		$idDependencias = array();
		$idWFOpciones = array();
		
		foreach ($arrRevisionesDoc AS $revisionesDoc)
		{
			$usuaLogins[] = $revisionesDoc->GetLoginUsuario();
			$cargoFundaciones[] = GetCargoFundacionFromIdPerfil($revisionesDoc->GetIdPerfil());
			$idDependencias[] = GetIdDependenciaFromIdPerfil($revisionesDoc->GetIdPerfil());
			$idWFOpciones[] = $revisionesDoc->GetIdWFOpcion();
		}
		
		$empleados = null;
		if(count($usuaLogins) > 0)
			$empleados = SafiModeloEmpleado::GetEmpleadosByUsuaLogins($usuaLogins);
			
		if(count($cargoFundaciones) > 0)
			$cargos = SafiModeloCargo::GetCargoByCargoFundaciones($cargoFundaciones);
			
		if(count($idDependencias) > 0)
			$dependencias = SafiModeloDependencia::GetDependenciaByIds($idDependencias);
			
		if(count($idWFOpciones) > 0)
			$wFOpciones = SafiModeloWFOpcion::GetWFOpciones(array('idWFOpciones' => $idWFOpciones));
		
		if($arrRevisionesDoc != null && is_array($arrRevisionesDoc) && count($arrRevisionesDoc) > 0){
			$datosRevisionesDocumento = array(
				'arrRevisionesDoc' => $arrRevisionesDoc,
				'empleadosRevisiones' => $empleados,
				'cargosRevisiones' => $cargos,
				'dependenciasRevisiones' => $dependencias,
				'wFOpcionesRevisiones' => $wFOpciones
			);
		}
		
		return $datosRevisionesDocumento;
	}
	
	function pg_array_parse( $text, &$output, $limit = false, $offset = 1 ){
		if( false === $limit )
		{
			$limit = strlen( $text )-1;
			$output = array();
		}
		if( '{}' != $text )
		do
		{
			if( '{' != $text{$offset} )
			{
				preg_match( "/(\\{?\"([^\"\\\\]|\\\\.)*\"|[^,{}]+)+([,}]+)/", $text, $match, 0, $offset );
				$offset += strlen( $match[0] );
				$output[] = ( '"' != $match[1]{0} ? $match[1] : stripcslashes( substr( $match[1], 1, -1 ) ) );
				if( '},' == $match[3] ) return $offset;
			}
			else  $offset = pg_array_parse( $text, $output[], $limit, $offset+1 );
		}
		while( $limit > $offset );
		return $output;
	}
	
	function GetNotaBienesSalidasReasignaciones()
	{
		return "
			<div align='justify' style='font-size: 14px; font-weight: bold;'>
				<span>NOTA:</span>
				Cualquier movimiento, cambio de activos o ingreso de un personal
				nuevo a su unidad, debe notificar a la unidad de bienes, ya que la
				pérdida o el daño voluntario de uno de los activos es responsabilidad
				del firmante en la casilla \"Recibido por\" y del usuario. Los activos descritos en el
				acta se encuentran en perfecto funcionamiento, tienen un lapso de 3 días
				desde el momento de la instalación para notificar cualquier desperfecto.
				Teléfonos de contacto en bienes y seguridad: 0212-7718558, 7718555,
				7718846, 7718512.
			</div>
		";
	}