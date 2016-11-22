<?php
include_once(SAFI_ENTIDADES_PATH . '/rendicionAvance.php');

include_once(SAFI_MODELO_PATH . '/responsableRendicionAvancePartidas.php' );
include_once(SAFI_MODELO_PATH . '/wfcadena.php');
include_once(SAFI_MODELO_PATH . '/docgenera.php');

class SafiModeloRendicionAvance
{
	public static function ResponsablesAvancePartidasToResponsablesRendicionAvancePartidas(array $responsablesAvancePartidas = null)
	{
		$responsablesRendicionAvancePartidas = array();
		if(is_array($responsablesAvancePartidas))
		{
			foreach ($responsablesAvancePartidas as $responsableAvancePartidas)
			{
				if($responsableAvancePartidas instanceof EntidadResponsableAvancePartidas)
				{
					$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
					$avancePartidas = $responsableAvancePartidas->GetAvancePartidas();
					
					$empleado = $responsableAvance->GetEmpleado() != null ? clone $responsableAvance->GetEmpleado() : null;
					$beneficiario = $responsableAvance->GetBeneficiario() != null ? clone $responsableAvance->GetBeneficiario() : null;
					$estado = $responsableAvance->GetEstado() != null ? clone $responsableAvance->GetEstado() : null;
					
					$responsableRendicionAvance = new EntidadResponsableRendicionAvance();
					$responsableRendicionAvance->SetIdResponsableAvance($responsableAvance->GetId());
					$responsableRendicionAvance->SetTipoResponsable($responsableAvance->GetTipoResponsable());
					$responsableRendicionAvance->SetEmpleado($empleado);
					$responsableRendicionAvance->SetBeneficiario($beneficiario);
					$responsableRendicionAvance->SetEstado($estado);
					
					$rendicionAvancePartidas = array();
					if(is_array($avancePartidas)){
						foreach ($avancePartidas as $avancePartida)
						{
							$partida = $avancePartida->GetPartida() != null ? clone $avancePartida->GetPartida() : null;
							
							$rendicionAvancePartida = new EntidadRendicionAvancePartida();
							$rendicionAvancePartida->SetPartida($partida);
							$rendicionAvancePartida->SetMonto($avancePartida->GetMonto());
							
							$rendicionAvancePartidas[] = $rendicionAvancePartida; 
						}
					}
					
					$responsableRendicionAvancePartidas = new EntidadResponsableRendicionAvancePartidas();
					$responsableRendicionAvancePartidas->SetResponsableRendicionAvance($responsableRendicionAvance);
					$responsableRendicionAvancePartidas->SetRendicionAvancePartidas($rendicionAvancePartidas);
					// Obtener el monto total de anticipo del responsable actual
					$responsableRendicionAvancePartidas->SetMontoAnticipo($responsableAvancePartidas->GetMontoTotal());
					
					$responsablesRendicionAvancePartidas[] = $responsableRendicionAvancePartidas;
				}
			}
		}
		return $responsablesRendicionAvancePartidas;
	}
	
	public static function GuardarRendicionAvance(EntidadRendicionAvance $rendicionAvance = null, array $params = null)
	{
		try
		{
			if($rendicionAvance == null)
				throw new Exception('Error al guardar la rendición avance. Detalles: '.
					'El parámetro rendicionAvance es nulo');
				
			if($rendicionAvance->GetAvance() == null)
				throw new Exception('Error al guardar la rendición de avance. Detalles: '.
					'El parámetro rendicionAvance avance es nulo');
			
			if(
				$rendicionAvance->GetDependencia() == null
				|| !(($dependencia=$rendicionAvance->GetDependencia()) instanceof EntidadDependencia)
			)
				throw new Exception('Error al guardar la rendición avance. Detalles: El parámetro rendicionAvance '.
					'dependencia es nulo o es incorrecto');
				
			if($dependencia->GetId() == null || $dependencia->GetId() == '')
				throw new Exception('Error al guardar la rendición de avance. Detalles: '.
					'El parámetro rendicionAvance dependencia id es nulo');
			
			if ($params == null || !is_array($params))
				throw new Exception('Error al guardar la rendición de avance. Detalles: El parámetro params es nulo o es incorrecto.');
				
			if (!isset($params['perfilRegistrador']))
				throw new Exception('Error al guardar la rendición de avance. Detalles: El parámetro params[\'perfilRegistrador\'] '.
					'no pudo ser encontrado');
			
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			// Generar el id del documento rendición de avance
			// 
			$query = "
				SELECT
					sai_generar_codigo(
							'".GetConfig("preCodigoRendicionAvance")."',
							'" . $dependencia->GetId() . "',
							'fecha_rendicion',
							'id'
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar. No se puede generar el id de la  rendición de avance. Detalles: '
				. $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			if (!($idRendicionAvance = $GLOBALS['SafiClassDb']->FetchOne($result))) {
				throw new Exception('Error al guardar. No se puede generar el id de la rendición de avance. Detalles: '
					. $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			$horaFechaActual = date("d/m/Y H:i:s");
			
			/* Parche para que funcionen las rendiciones de avances del año anterior */
			//$horaFechaActual = "30/12/2014" . date(" H:i:s");
			
			/*************************************************************************
			*    Guardar información referente a la flujo de trabajo de la cadena    *
			**************************************************************************/
			
			$documento = new EntidadDocumento();
			// Establecer el id del documento de rendición del avance
			$documento->SetId(GetConfig("preCodigoRendicionAvance"));
			
			$wFObjetoInicial = new EntidadWFObjeto();
			$wFObjetoInicial->SetId(1);	// Establecer el id del objeto inicial que siempre es 1
			
			$dependenciaFind = new EntidadDependencia();
				
			switch ($dependencia->GetId()){
				case '150': // Presidencia
				case '350': // Dirección Ejecutiva
					$dependenciaFind->SetId($dependencia->GetId());
					break;
				default:
					$dependenciaFind->SetId(null);
					break;
			}
			
			// Establecer los parámetros para buscar la cadena inicial de viaticos nacionales
			$cadenaFind = new EntidadWFCadena();
			$cadenaFind->SetWFObjetoInicial($wFObjetoInicial);
			$cadenaFind->SetDocumento($documento);
			$cadenaFind->SetDependencia($dependenciaFind);
			
			// Obtener la cadena inicial de viaticos nacionales
			$cadena = SafiModeloWFCadena::GetWFCadena($cadenaFind);
			
			if($cadena == null)
				throw new Exception('Error al guardar la rendición del avance. Detalles: WFCadena inicial no encontrada');
				
			if($cadena->GetWFCadenaHijo() == null)
				throw new Exception('Error al guardar la rendición del avance. Detalles: WFCadena hija no encontrada');
			
			// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
			$cadenaHijo = SafiModeloWFCadena::GetWFCadena($cadena->GetWFCadenaHijo());
			
			if($cadenaHijo == null)
				throw new Exception('Error al guardar la rendición del avance. Detalles: WFCadena hija no encontrada');
				
			if($cadenaHijo->GetWFGrupo() == null)
				throw new Exception('Error al guardar la rendición del avance. Detalles: WFGrupo de WFCadena hija no encontrado');
			
			// Obtener el siguiente cargo al que será enviado el documento
			$perfilActual = SafiModeloDependenciaCargo::
				GetSiguienteCargoDeCadena($dependencia->GetId(), $cadenaHijo->GetWFGrupo()->GetPerfiles());
			
			if($perfilActual == null)
				throw new Exception('Error al guardar la rendición del avance. '.
					'Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena');
			
			// Guardar los datos en la tabla doc_genera
			 
			$docGenera = new EntidadDocGenera();
			
			$docGenera->SetId($idRendicionAvance);
			$docGenera->SetIdWFObjeto(1); // 1 = Iniciar
			$docGenera->SetIdWFCadena($cadena->GetId());
			$docGenera->SetUsuaLogin($rendicionAvance->GetUsuaLogin());
			$docGenera->SetIdPerfil($params['perfilRegistrador']);
			$docGenera->SetFecha($horaFechaActual);
			$docGenera->SetIdEstatus(59); // 59 = estado "por enviar"
			$docGenera->SetPrioridad(1);
			$docGenera->SetIdPerfilActual($perfilActual->GetId());
			
			// Guardar el registro del documento en docGenera (estado de la cadena)
			if(SafiModeloDocGenera::GuardarDocGenera($docGenera) === false)
				throw new Exception('Error al guardar la rendción de avance. Detalles: No se pudo guardar docGenera');
			
			/*******************************************************************************************
			* Guardar información referente a la rendición de avance                                   *
			********************************************************************************************/
			
			// Guardar los campos de la tabla safi_rendicion_avance
			$query = "
				INSERT INTO safi_rendicion_avance
					(
						id,
						avance_id,
						fecha_rendicion,
						fecha_registro,
						fecha_ultima_modificacion,
						fecha_inicio_actividad,
						fecha_fin_actividad,
						objetivos,
						descripcion,
						nro_participantes,
						observaciones,
						usua_login,
						depe_id
					)
				VALUES
					(
						'".$idRendicionAvance."',
						".(($rendicionAvance->GetAvance()->GetId() != null && trim($rendicionAvance->GetAvance()->GetId()) != '') ?
							"'".$rendicionAvance->GetAvance()->GetId()."'" : "NULL").",
						to_date('".$rendicionAvance->GetFechaRendicion()."', 'DD/MM/YYYY'),
						to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
						to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
						to_date('".$rendicionAvance->GetFechaInicioActividad()."', 'DD/MM/YYYY'),
						to_date('".$rendicionAvance->GetFechaFinActividad()."', 'DD/MM/YYYY'),
						".($rendicionAvance->GetObjetivos() != null && trim($rendicionAvance->GetObjetivos()) != '' ?
							"'".$GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetObjetivos())."'" : "NULL").",
						".($rendicionAvance->GetDescripcion() != null && trim($rendicionAvance->GetDescripcion()) != '' ?
							 "'".$GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetDescripcion())."'" : "NULL" ).",
						".(($rendicionAvance->GetNroParticipantes() != null && trim($rendicionAvance->GetNroParticipantes()) != '') ?
							"'".trim($GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetNroParticipantes()))."'" : "NULL").",
						".(($rendicionAvance->GetObservaciones() != null && trim($rendicionAvance->GetObservaciones()) != '') ?
							"'".trim($GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetObservaciones()))."'" : "NULL").",
						'".$rendicionAvance->GetUsuaLogin()."',
						'".$dependencia->GetId()."'
					)
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al guardar la rendición de  avance. ".
					"Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Guardar los responsables de la rendición y sus partidas
			self::__GuardarResponsablesRendicionAvancePartidas(
				$rendicionAvance->GetResponsablesRendicionAvancePartidas(), $idRendicionAvance);
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return $idRendicionAvance;
				
		}
		catch (Exception $e) 
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
		
		return false;
	}
	
	private static function __GuardarResponsablesRendicionAvancePartidas($responsablesRendicionAvancePartidas, $idRendicionAvance)
	{
		// Guardar la información relacionada con los responsables y las partidas
		if(is_array($responsablesRendicionAvancePartidas))
		{
			$indexResponsable = 0;
			
			foreach ($responsablesRendicionAvancePartidas as $responsableRendicionAvancePartidas)
			{
				if($responsableRendicionAvancePartidas->GetResponsableRendicionAvance() == null)
					throw new Exception('Error al guardar la rendicion de avance. El parámetro responsableRendicionAvance '.
						'del responsable['.($indexResponsable+1).'] es nulo');
					
					$responsableRendicionAvancePartidas->GetResponsableRendicionAvance()->SetIdRendicionAvance($idRendicionAvance);
					
					if(SafiModeloResponsableRendicionAvancePartidas::
						GuardarResponsableRendicionAvancePartidas($responsableRendicionAvancePartidas) === false
					)
						throw new Exception('Error al guardar la rendición de avance. No se pudo guardar la informacion del '.
							'responsable['.($indexResponsable+1).'].');
						
				$indexResponsable++;
			} // Fin de foreach ($rendicionAvance->GetResponsablesRendicionAvancePartidas() as $responsablesRendicionAvancePartidas)
		}
	}
	
	/****************************************************************************************
	 *                                                                                      *
	 *   Función GetResponsablesDisponiblesRendicionAvance(...)                             *
	 *                                                                                      *
	 * - Se encarga de obtener los responsables de un avance que aún no han sido incluidos  *
	 *   en ninguna rendición de avance. Esto abarca tanto los responsables que están en    *
	 *   una rendición de avance guardada en base de datos como la rendición de avance que  *
	 *   se está creando al momento de llamar a esta función.                               *
	 *                                                                                      *
	 * - Parámetros:                                                                        *
	 *                                                                                      *
	 * + $avance: Una referencia a la clase EntidadAvance, que representa en avance al cual *
	 *   se le está realizando la rendición de avance. Será utilizada para buscar los       *
	 *   responsables incluidos en otras rendiciones de avances realizadas con antelación   *
	 *   para el mencionado avance.                                                         *
	 *                                                                                      *
	 * + $responablesSeleccionados: Es un arreglo de referencias a la clase                 *
	 *   EntidadResponsableRendicionAvancePartidas. Cada objeto representa a un responsable *
	 *   incluido en la rendición de avance actual al momento de realizar la llamada a      *
	 *   esta función                                                                       *
	 *                                                                                      *
	 ****************************************************************************************/
	
	public static function GetResponsablesDisponiblesRendicionAvance(
		EntidadAvance $avance = null,
		array $responsablesSeleccionados = null
	){
		try
		{
			$idResponsablesSeleccionados = array();
			$idResponsablesDisponibles = array();
			$responsablesRendicionAvancePartidas = array();
		
			$commonMsg = "Error al obtener los responsables disponibles para incluir en la rendición del avance. ";
			if($avance == null)
				throw new Exception($commonMsg."El parámetro avance es nulo");
			
			if($avance->GetId() == null)
				throw new Exception($commonMsg."El parámetro avance id es nulo");
				
			if(($idAvance=trim($avance->GetId())) == '')
				throw new Exception($commonMsg."El parámetro avance id está vacío");
			
			// Obtener el id de los responsables seleccionados en la rendición de avance actual
			if(is_array($responsablesSeleccionados))
			{
				foreach ($responsablesSeleccionados as $responsableRendicionAvancePartidas)
				{
					$responsableRendicionAvance = $responsableRendicionAvancePartidas->GetResponsableRendicionAvance();
					$idResponsablesSeleccionados[] = $responsableRendicionAvance->GetIdResponsableAvance();
				}
			}
			
			if(count($idResponsablesSeleccionados) > 0)
				$queryResponsablesSeleccionados = 
					"AND responsable_avance.id NOT IN ('".implode("', '", $idResponsablesSeleccionados)."')";
			else
				$queryResponsablesSeleccionados = "";
			
			// Buscar los id's de los responsables de avance disponibles para rendir 
			$query = "
				SELECT DISTINCT
					responsable_avance.id as id_responsable
				FROM
					safi_responsable_avance responsable_avance
				WHERE
					responsable_avance.avance_id = '".$idAvance."'
					AND responsable_avance.id NOT IN (
						SELECT DISTINCT
							responsable_rendicion.responsable_avance_id
						FROM
							safi_rendicion_avance rendicion
							INNER JOIN safi_responsable_rendicion_avance responsable_rendicion
								ON (responsable_rendicion.rendicion_avance_id = rendicion.id)
							INNER JOIN sai_doc_genera documento ON (documento.docg_id = rendicion.id)
						WHERE
							documento.esta_id != 15
							AND rendicion.avance_id = '".$idAvance."'
					)
					".$queryResponsablesSeleccionados."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los responsables de la rendición de avance disponibles para rendir. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$idResponsablesDisponibles[] = $row['id_responsable']; 
			}
			
			if (count($idResponsablesDisponibles) > 0)
			{
				$responsablesAvancePartidas = SafiModeloResponsableAvancePartidas::
					GetResponsablesAvancePartidasByIdsResponsables($idResponsablesDisponibles);
					
				$responsablesRendicionAvancePartidas = SafiModeloRendicionAvance::
					ResponsablesAvancePartidasToResponsablesRendicionAvancePartidas($responsablesAvancePartidas);
			}
			
			return $responsablesRendicionAvancePartidas;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
		return false;
	}
	
	public static function GetRendicionAvance(EntidadRendicionAvance $findRendicion = null)
	{
		$rendicionAvance = null;
		try
		{
			$preMsg = "Error al intentar obtener la rendición de avance.";
			$where = array();
			$existeCriterio = false;
			$arrMsg = array();
			
			if($findRendicion == null)
				$arrMsg[] = "El parámetro findRendicion es nulo.";
			else if(($idRendicion=$findRendicion->GetId()) == null)
				$arrMsg[] = "El parámetro findRendicion id es nulo.";
			else if(($idRendicion=trim($idRendicion)) == '')
				$arrMsg[] = "El parámetro findRendicion id es está vacío.";
			else {
				$existeCriterio = true;
				$where[] = "rendicion.id = '".$idRendicion."'";
			}
			
			if(!$existeCriterio)
			{
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			}
			
			$query = "
				SELECT
					".self::GetSelectFildsRendicion()."
				FROM
					safi_rendicion_avance rendicion
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = rendicion.depe_id)
				WHERE
					".implode(" AND ", $where)."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$rendicion = self::LlenarRendicion($row);
			}
					
			return $rendicion;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
		return null;
	}
	
	public static function GetRendicionAvanceEnBandeja($params)
	{
		try
		{
			$preMsg = "Error al intentar obtener la bandeja principal/devueltos de rendición de avance.";
			$idPerfilActual = $params['idPerfilActual'];
			$estatus = $params['estatus'];
			$idDependencia = $params['idDependencia'];
			
			if($idPerfilActual == null)
				throw new Exception($preMsg." El parámetro params['idPerfilActual'] es nulo.");
			if(($idPerfilActual=trim($idPerfilActual)) == '')
				throw new Exception($preMsg." El parámetro params['idPerfilActual'] está vacío.");
			if($estatus == null)
				throw new Exception($preMsg." El parámetro params['estatus'] es nulo.");
			if(!is_array($estatus))
				throw new Exception($preMsg." El parámetro params['estatus'] no es un arreglo.");
			if(count($estatus)==0)
				throw new Exception($preMsg." El parámetro params['estatus'] está vacío.");
			if($idDependencia != null && ($idDependencia=trim($idDependencia)) == '')
				throw new Exception($preMsg." El parámetro params['idDependencia'] está vacío.");
			
			// Obtener el prefijo del código del documento de rendición de avance
			$idTipoDocumento = GetConfig("preCodigoRendicionAvance");
			
			$where = "
				doc_genera.docg_id LIKE '".$idTipoDocumento."%'
				AND doc_genera.perf_id_act = '".$idPerfilActual."'
				AND doc_genera.esta_id IN (" . implode(", ", $estatus) . ")
			";
			
			if($idDependencia != null){
				$where .= "
				AND rendicion.depe_id = '" . $idDependencia . "'
				";
			}
				
			return self::__GetRendicionAvanceBadejasByWhere($where);
		}	
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetRendicionAvancePorEnviar($params = null)
	{
		$usuaLogin = isset($params['usuaLogin']) ? $params['usuaLogin'] : '';
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : '';
		
		$idTipoDocumento = GetConfig("preCodigoRendicionAvance");
		$estadoPorEnviar = 59;
		
		if($usuaLogin != '' && $idPerfilActual != ''){
			$where = "
	  			doc_genera.docg_id LIKE '".$idTipoDocumento."%' AND
	  			doc_genera.usua_login = '".$usuaLogin."' AND
	  			doc_genera.esta_id = ".$estadoPorEnviar." AND
	  			doc_genera.perf_id_act = '".$idPerfilActual."'
			";
			
			return self::__GetRendicionAvanceBadejasByWhere($where);
		}
		
		return null;

	}
	
	public static function GetRendicionAvanceEnTransito($params = array())
	{
		try
		{
			$preMsg = "Error al intentar obtener la bandeja en transito de rendición de avance.";
			$idDependencia = $params['idDependencia'];
			
			if($idDependencia != null && ($idDependencia=trim($idDependencia)) == '')
				throw new Exception($preMsg." El parámetro params['idDependencia'] está vacío.");
			
			$idTipoDocumento = GetConfig("preCodigoRendicionAvance");
			$estadoEnTransito = 10;
			
			$where = "
	  			doc_genera.docg_id LIKE '".$idTipoDocumento."%'
	  			AND doc_genera.esta_id = ".$estadoEnTransito."
			";
			
			$whereEnPerfiles = "";
			if(is_array(($enPerfiles=$params['enPerfiles'])) && count($enPerfiles) > 0)
			{
				$whereEnPerfiles .= "
					doc_genera.perf_id_act IN ('".implode("', '", $enPerfiles)."')
				";
			}
			
			$whereEnCargosFundacion = "";
			if(is_array(($enCargosFundacion=$params['enCargosFundacion'])) && count($enCargosFundacion) > 0)
			{
				$whereEnCargosFundacion .= "
					substring(doc_genera.perf_id_act FROM 1 FOR 2)  IN ('".implode("', '", $enCargosFundacion)."')
				";
			}
			
			if($whereEnPerfiles != "" || $whereEnCargosFundacion != "")
			{
				$where .= "
					AND (
						".$whereEnPerfiles."
						". ($whereEnPerfiles != "" && $whereEnCargosFundacion != "" ? "OR " : "") . $whereEnCargosFundacion."
					)
				";
			}
			
			if(is_array(($noEnDependencia=$params['noEnDependencia'])) && count($noEnDependencia) > 0)
			{
				$where .= "
					AND rendicion.depe_id NOT IN ('".implode("' ,'", $noEnDependencia)."')
				";
			}
			
			if($idDependencia != null){
				$where .= "
					AND rendicion.depe_id = '" . $idDependencia . "'
				";
			}
			
			return self::__GetRendicionAvanceBadejasByWhere($where);
		}	
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function BuscarRendicionAvance($params = null)
	{
		try
		{
			$preMsg = "Error al intentar realizar una búsqueda de rendiciones de avances.";
			
			$where = "";
			$fechaInicio = null;
			$fechaFin = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			// Validar las fechas de inicio
			if(isset($params['fechaInicio']))
			{
				if(($__fechaInicio=trim($params['fechaInicio'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaInicio'] está vacío.");
					
				$fechaInicio = $__fechaInicio;
			}
			
			// Validar las fechas de fin
			if(isset($params['fechaFin']))
			{
				if(($__fechaFin=trim($params['fechaFin'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaFin'] está vacío.");
					
				$fechaFin = $__fechaFin; 
			}
			
			
			if($fechaInicio != null && $fechaFin != null){
				$where = "
					rendicion.fecha_rendicion BETWEEN
						to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
						to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaInicio != null){
				$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') <= rendicion.fecha_rendicion";
			} else if ($fechaFin != null) {
				$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)
					." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= rendicion.fecha_rendicion";
			}
			
			if(isset($params['idRendicion'])){
				if(($idRendicion = trim($params['idRendicion'])) == '')
					throw new Exception($preMsg. " El parámetro params['idRendicion'] está vacío.");
				
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "lower(rendicion.id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($idRendicion)) . "'";
			}
			
			if(isset($params['idAvance'])){
				if(($idAvance = trim($params['idAvance'])) == '')
					throw new Exception($preMsg. " El parámetro params['idAvance'] está vacío.");
				
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "lower(rendicion.avance_id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($idAvance)) . "'";
			}
			
			if($where == "")
				throw new Exception($preMsg." No existen criterios de búsqueda.");
				
			if(
				isset($params['dependencia']) && ($dependencia = $params['dependencia']) != null && $dependencia->GetId() != null
				&& ($idDependencia=trim($dependencia->GetId())) != ''
			){
					$where .= " AND rendicion.depe_id = '" . $idDependencia . "'";
			}
			
			return self::__GetRendicionAvanceBadejasByWhere($where);	
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	private static function __GetRendicionAvanceBadejasByWhere($where)
	{
		$dataRendiciones = array();
		
		try
		{
			$preMsg = "Error al intentar obtener las rendciones de avances en bandeja \"__GetRendicionAvanceBadejasByWhere(\$where)\".";
			
			if($where == null)
				throw new Exception($preMsg." EL parámetro where es nulo.");
				
			if(trim($where) == '')
				throw new Exception($preMsg." EL parámetro where está vacío.");
				
			$query = "
				SELECT
					" . SafiModeloDocGenera::GetSelectFieldsDocGenera() . ",
	  				" . self::GetSelectFildsRendicion() . "
				FROM
					sai_doc_genera doc_genera
					INNER JOIN safi_rendicion_avance rendicion ON (rendicion.id = doc_genera.docg_id)
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = rendicion.depe_id)
				" . ($where != '' ? " WHERE " . $where : '') . "
				ORDER BY
  					doc_genera.docg_fecha
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$idsAvances = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$docGenera = SafiModeloDocGenera::LlenarDocGenera($row);
				$rendicion = self::LlenarRendicion($row); 
				
				$dataRendiciones[$row['docg_id']]['ClassDocGenera'] =  $docGenera;
				$dataRendiciones[$row['docg_id']]['ClassRendicionAvance'] = $rendicion;
				
				if($rendicion != null && $rendicion->GetAvance() != null
					&& ($idAvance=$rendicion->GetAvance()->GetId()) != null && ($idAvance=trim($idAvance)) != ''
				){
					$idsAvances[$row['docg_id']] = $idAvance;
				}
			}
			
			if(count($idsAvances) > 0)
				$avances = SafiModeloAvance::GetAvances(array('idsAvances' =>  $idsAvances));
				
			if(is_array($avances) && count($avances) > 0)
			{
				foreach ($dataRendiciones as $idRendicion => $dataRendicion)
				{
					$rendicion = $dataRendicion['ClassRendicionAvance'];
					if(isset($idsAvances[$idRendicion]) && isset($avances[$idsAvances[$idRendicion]]))
						$rendicion->SetAvance($avances[$idsAvances[$idRendicion]]);
				}
			}
			
			return $dataRendiciones;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function ActualizarRendicionAvance(EntidadRendicionAvance $rendicionAvance = null)
	{
		try
		{
			$preMsg = 'Error al intentar actualizar la rendición de avance.';
			if($rendicionAvance == null)
				throw new Exception($preMsg.' Detalles: El parámetro rendicionAvance es nulo');
				
			if(($idRendicionAvance = $rendicionAvance->GetId()) == null)
				throw new Exception($preMsg.' Detalles: El parámetro rendicionAvance id es nulo');
				
			if(($idRendicionAvance = trim($idRendicionAvance)) == '')
				throw new Exception($preMsg.' Detalles: El parámetro rendicionAvance id está vacío.');
				
			if($rendicionAvance->GetAvance() == null)
				throw new Exception($preMsg.' Detalles: El parámetro rendicionAvance avance es nulo');
			
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacción. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$horaFechaActual = date("d/m/Y H:i:s");
			
			/***********************************************************************
			 ******* Actualizar los campos de la tabla safi_rendicion_avance *******
			 ***********************************************************************/
			$query = "
				UPDATE
					safi_rendicion_avance
				SET
					fecha_rendicion = to_date('".$rendicionAvance->GetFechaRendicion()."', 'DD/MM/YYYY'),
					fecha_ultima_modificacion = to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
					fecha_inicio_actividad = to_date('".$rendicionAvance->GetFechaInicioActividad()."', 'DD/MM/YYYY'),
					fecha_fin_actividad = to_date('".$rendicionAvance->GetFechaFinActividad()."', 'DD/MM/YYYY'),
					objetivos = ".($rendicionAvance->GetObjetivos() != null && trim($rendicionAvance->GetObjetivos()) != '' ?
						"'".$GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetObjetivos())."'" : "NULL").",
					descripcion = ".($rendicionAvance->GetDescripcion() != null && trim($rendicionAvance->GetDescripcion()) != '' ?
						 "'".$GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetDescripcion())."'" : "NULL" ).",
					nro_participantes = ".(($rendicionAvance->GetNroParticipantes() != null && trim($rendicionAvance->GetNroParticipantes()) != '') ?
						"'".trim($GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetNroParticipantes()))."'" : "NULL").",
					observaciones = ".(($rendicionAvance->GetObservaciones() != null && trim($rendicionAvance->GetObservaciones()) != '') ?
						"'".trim($GLOBALS['SafiClassDb']->Quote($rendicionAvance->GetObservaciones()))."'" : "NULL")."
				WHERE
					id = '".$idRendicionAvance."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg.' Detalles: '.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			/*****************************************************************************************
			 ******* Actualizar la información relacionada con los responsables y las partidas *******
			 *****************************************************************************************/
				
			// Borrar toda la información de los responsables de la rendición de avance para sustituirla
			// por la nueva información en el objeto $rendicionAvance
			if(SafiModeloResponsableRendicionAvancePartidas::
				BorrarResponsableRendicionAvancePartidasByIdRendicion($idRendicionAvance) === false
			)
				throw new Exception($preMsg.' Detalles: '.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			self::__GuardarResponsablesRendicionAvancePartidas(
				$rendicionAvance->GetResponsablesRendicionAvancePartidas(), $idRendicionAvance);
				
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg.' No se pudo realizar el commit. Detalles: '
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetSelectFildsRendicion()
	{
		return "
			rendicion.id AS rendicion_id,
			rendicion.avance_id AS rendicion_avance_id,
			to_char(rendicion.fecha_rendicion, 'DD/MM/YYYY') AS rendicion_fecha_rendicion,
			to_char(rendicion.fecha_registro, 'DD/MM/YYYY HH24:MI:SS') AS rendicion_fecha_resgistro,
			to_char(rendicion.fecha_ultima_modificacion, 'DD/MM/YYYY HH24:MI:SS') AS rendicion_fecha_ultima_modificacion,
			to_char(rendicion.fecha_inicio_actividad, 'DD/MM/YYYY') AS rendicion_fecha_inicio_actividad,
			to_char(rendicion.fecha_fin_actividad, 'DD/MM/YYYY') AS rendicion_fecha_fin_actividad,
			rendicion.objetivos AS rendicion_objetivos,
			rendicion.descripcion AS rendicion_descripcion,
			rendicion.nro_participantes AS rendicion_nro_participantes,
			rendicion.observaciones AS rendicion_observaciones,
			rendicion.usua_login AS rendicion_usua_login,
			rendicion.depe_id AS rendicion_depe_id,
			dependencia.depe_nombre AS rendicion_dependencia_nombre,
			dependencia.depe_nombrecort AS rendicion_dependencia_nombrecort,
			dependencia.depe_id_sup AS rendicion_dependencia_id_sup,
			dependencia.depe_nivel AS rendicion_dependencia_nivel,
			dependencia.depe_cosige AS rendicion_dependencia_cosige,
			dependencia.usua_login AS rendicion_dependencia_usua_login,
			dependencia.depe_observa AS rendicion_dependencia_observa,
			dependencia.esta_id AS rendicion_dependencia_esta_id
		";
	}
	
	public static function LlenarRendicion(array $row, array $excluir = null)
	{
		$rendicion = null;
		$avance = null;
		$dependencia = null;
		
		$excluirResponsables = false;
		
		if($excluir != null && is_array($excluir) && count($excluir) > 0){
			if(in_array("responsables", $excluir)) $excluirResponsables = true;
		}
		
		if($row['rendicion_depe_id']!=null && trim($row['rendicion_depe_id'])!=''){
			$dependencia = new EntidadDependencia();
			$dependencia->SetId(trim($row['rendicion_depe_id']));
			
			if(isset($row['rendicion_dependencia_nombre'])){
				$dependencia->SetNombre($row['rendicion_dependencia_nombre']);
				$dependencia->SetNombreCorto($row['rendicion_dependencia_nombrecort']);
				$dependencia->SetIdDependenciaPadre($row['rendicion_dependencia_id_sup']);
				$dependencia->SetNivel($row['rendicion_dependencia_nivel']);
				$dependencia->SetCodigoSigecof($row['rendicion_dependencia_cosige']);
				$dependencia->SetLoginUsuario($row['rendicion_dependencia_usua_login']);
				$dependencia->SetObservaciones($row['rendicion_dependencia_observa']);
				$dependencia->SetIdEstatus($row['rendicion_dependencia_esta_id']);
			}
		}
		
		if($row['rendicion_avance_id'] != null && ($idAvance=trim($row['rendicion_avance_id'])) != ''){
			$avance = new EntidadAvance();
			$avance->SetId($idAvance);
		}
		
		// Establecer los datos de los responsables de la rendición de avance
		if($excluirResponsables !== true)
		$responsablesRendicionAvancePartidas =
			SafiModeloResponsableRendicionAvancePartidas::GetResponsableRendicionAvancePartidasByIdRendicion($row['rendicion_id']);
		
		$rendicion = new EntidadRendicionAvance();
		$rendicion->SetId($row['rendicion_id']);
		$rendicion->SetAvance($avance);
		$rendicion->SetFechaRendicion($row['rendicion_fecha_rendicion']);
		$rendicion->SetFechaRegistro($row['rendicion_fecha_resgistro']);
		$rendicion->SetFechaUltimaModificacion($row['rendicion_fecha_ultima_modificacion']);
		$rendicion->SetFechaInicioActividad($row['rendicion_fecha_inicio_actividad']);
		$rendicion->SetFechaFinActividad($row['rendicion_fecha_fin_actividad']);
		$rendicion->SetObjetivos($row['rendicion_objetivos']);
		$rendicion->SetDescripcion($row['rendicion_descripcion']);
		$rendicion->SetNroParticipantes($row['rendicion_nro_participantes']);
		$rendicion->SetResponsablesRendicionAvancePartidas($responsablesRendicionAvancePartidas);
		$rendicion->SetObservaciones($row['rendicion_observaciones']);
		$rendicion->SetUsuaLogin($row['rendicion_usua_login']);
		$rendicion->SetDependencia($dependencia);
		
		return $rendicion;
	}

	public static function BuscarIdsRendicionAvances($codigoDocumento, $idDependencia, $numLimit) {
		$idsViaticos = null;
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
			throw new Exception("Error al buscar los ids de avances. Detalles: El código del documento o la dependencia es nulo o vacío");
	
			$query = "
				            SELECT
				               a.id
				            FROM
				                safi_rendicion_avance a, sai_doc_genera d
				            WHERE
				               a.id=d.docg_id AND
				               a.id LIKE '%".$codigoDocumento."%' AND
				               a.depe_id = '".$idDependencia."' AND
				               d.esta_id<>15 AND 
				               a.id NOT IN (
				               				SELECT nro_documento
				               				FROM registro_documento
				               				WHERE tipo_documento = 'rvna'
				               				AND id_estado=1
				               				AND user_depe='".$_SESSION['user_depe_id']."'
				               				) AND 
				               a.fecha_registro LIKE '".$_SESSION['an_o_presupuesto']."%'  
				            ORDER BY a.id 
							LIMIT
							".$numLimit."
				        ";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de rendiciones de avances. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$ids = array();
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[] = $row['id'];
			}
	
		}catch(Exception $e){
			error_log($e, 0);
		}
	
		return $ids;
	}	
	public static function BuscarInfoRendicionAvance($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='')
			throw new Exception("Error al buscar la información del avance");
	
			$query = "
					SELECT TO_CHAR(ra.fecha_rendicion, 'dd-mm-yyyy') AS fecha,
						r.avance_id AS id,
						e.empl_cedula AS id_beneficiario, 
						e.empl_nombres || ' ' ||e.empl_apellidos AS beneficiario,
						ra.objetivos AS objetivos
					FROM safi_responsable_avance r, sai_empleado e, safi_avance a, safi_rendicion_avance ra
					WHERE r.empleado_cedula = e.empl_cedula
						AND r.avance_id = a.id 
						AND ra.avance_id = a.id
						AND ra.id='".$codigoDocumento."' 
					UNION
					SELECT TO_CHAR(ra.fecha_rendicion, 'dd-mm-yyyy') AS fecha,
						r.avance_id AS id,
						v.benvi_cedula AS id_beneficiario,
						v.benvi_nombres || ' ' || v.benvi_apellidos AS beneficiario,
						ra.objetivos AS objetivos
					FROM safi_responsable_avance r, sai_viat_benef v, safi_avance a, safi_rendicion_avance ra
					WHERE r.beneficiario_cedula = v.benvi_cedula
						AND r.avance_id = a.id
						AND ra.avance_id = a.id
						AND ra.id='".$codigoDocumento."'";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de rendicion de avances. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$ids = array();
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[0] = $row['id'];
				$ids[1] = $row['id_beneficiario'].":".$row['beneficiario'];
				$ids[2] = 0;
				$ids[3] = utf8_encode($row['objetivos']);
				if(SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0]))
				$ids[4] = SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0])->GetId();
				else
				$ids[4] = "comp-400";
				$ids[5] = $row['fecha'];
			}
	
		}catch(Exception $e){
			error_log($e, 0);
		}
	
		return $ids;
	}

}