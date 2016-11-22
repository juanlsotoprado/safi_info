<?php
include_once(SAFI_ENTIDADES_PATH . '/rendicionViaticoNacional.php');

class SafiModeloRendicionViaticoNacional
{
	public static function GuardarRendicion(EntidadRendicionViaticoNacional $rendicion, $params, $informeTmpPath = null)
	{
		include_once(SAFI_MODELO_PATH . '/docgenera.php');
		include_once(SAFI_MODELO_PATH . '/wfcadena.php');
		include_once(SAFI_MODELO_PATH . '/dependenciacargo.php');
		
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				if ($rendicion->GetIdViaticoNacional() == null)
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: '. 
						'$rendicion->GetIdViaticoNacional() es null');
					
				if (!isset($params['perfilRegistrador']))
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: '. 
						'La variable $params[\'perfilRegistrador\'] no esta definida');
					
				if($rendicion->GetDependencia() == null || !(($dependencia=$rendicion->GetDependencia()) instanceof EntidadDependencia))
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: '. 
						'La variable $rendicion->Dependencia es nula o es incorrecta');
					
				if($dependencia->GetId() == null || $dependencia->GetId() == '')
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: '. 
						'La variable $rendicion->Dependencia->id es nula');
					
				// Obtener el número de rendición a partir del número del viático
				$idRendicion = GetIdRendicionByIdViatico($rendicion->GetIdViaticoNacional());
				
				if($idRendicion === false)
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: No se pudo generar el 
						id (Código) de la rendición para el víatico nacional: "' . $rendicion->GetIdViaticoNacional()) . '"';
				
				$horaFechaActual = date("d/m/Y H:i:s");
				
				// Guardar los campos de la tabla safi_rendicion_viatico_nacional
				$query = "
					INSERT INTO safi_rendicion_viatico_nacional
						(
							id,
							viatico_nacional_id,
							fecha_rendicion,
							fecha_registro,
							fecha_ultima_modificacion,
							fecha_inicio_viaje,
							fecha_fin_viaje,
							objetivos_viaje,
							monto_anticipo,
							total_gastos,
							monto_reintegro,
							reintegro_banco_id,
							reintegro_referencia,
							reintegro_fecha,
							observaciones,
							informe_file_name,
							usua_login,
							depe_id
						)
					VALUES
						(
							'".$idRendicion."',
							'".$rendicion->GetIdViaticoNacional()."',
							to_date('".$rendicion->GetFechaRendicion()."', 'DD/MM/YYYY'),
							to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
							to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
							to_date('".$rendicion->GetFechaInicioViaje()."', 'DD/MM/YYYY'),
							to_date('".$rendicion->GetFechaFinViaje()."', 'DD/MM/YYYY'),
							'".$GLOBALS['SafiClassDb']->Quote($rendicion->GetObjetivosViaje())."',
							".$rendicion->GetMontoAnticipo().",
							".$rendicion->GetTotalGastos().",
							".$rendicion->GetMontoReintegro().",
							".((($banco=$rendicion->GetReintegroBanco()) != null && $banco->GetId()!=null && trim($banco->GetId()) != '') ?
								"'".trim($banco->GetId())."'" : "NULL").",
							".(($rendicion->GetReintegroReferencia() != null && trim($rendicion->GetReintegroReferencia()) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($rendicion->GetReintegroReferencia()))."'" : "NULL").",
							".(($rendicion->GetReintegroFecha() != null && trim($rendicion->GetReintegroFecha()) != '') ?
								"to_date('".trim($rendicion->GetReintegroFecha())."', 'DD/MM/YYYY')" : "NULL").",
							".(($rendicion->GetObservaciones() != null && trim($rendicion->GetObservaciones()) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($rendicion->GetObservaciones()))."'" : "NULL").",
							".(($rendicion->GetInformeFileName() != null && trim($rendicion->GetInformeFileName()) != '') ?
								"'".trim($rendicion->GetInformeFileName())."'" : "NULL").",
							'".$rendicion->GetUsuaLogin()."',
							'".$dependencia->GetId()."'
						)
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al guardar la rendición del viático nacional. Detalles: ". 
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				/*************************************************************************
				**** Guardar información referente a la flujo de trabajo de la cadena ****
				**************************************************************************/
				
				$documento = new EntidadDocumento();
				// Establecer el id del documento de rendición de viático nacional
				$documento->SetId(GetConfig("preCodigoRendicionViaticoNacional"));
				
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
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: WFCadena inicial no encontrada');
					
				if($cadena->GetWFCadenaHijo() == null)
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: WFCadena hija no encontrada');
					
				// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
				$cadenaHijo = SafiModeloWFCadena::GetWFCadena($cadena->GetWFCadenaHijo());
				
				if($cadenaHijo == null)
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: WFCadena hija no encontrada');
					
				if($cadenaHijo->GetWFGrupo() == null)
					throw new Exception('Error al guardar la rendición del viático nacional. Detalles: WFGrupo de WFCadena hija no encontrado');
				
				// Obtener el siguiente cargo al que será enviado el documento
				$perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($dependencia->GetId(), $cadenaHijo->GetWFGrupo()->GetPerfiles());
				
				if($perfilActual == null)
					throw new Exception('Error al guardar la rendición del viático nacional. '.
						'Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena');
				
				/****************************************************
				 ***** Guardar los datos en la tabla doc_genera *****
				 ****************************************************/
				$docGenera = new EntidadDocGenera();
				
				$docGenera->SetId($idRendicion);
				$docGenera->SetIdWFObjeto(1); // 1 = Iniciar
				$docGenera->SetIdWFCadena($cadena->GetId());
				$docGenera->SetUsuaLogin($rendicion->GetUsuaLogin());
				$docGenera->SetIdPerfil($params['perfilRegistrador']);
				$docGenera->SetFecha($horaFechaActual);
				$docGenera->SetIdEstatus(59); // 59 = estado "por enviar"
				$docGenera->SetPrioridad(1);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(SafiModeloDocGenera::GuardarDocGenera($docGenera) === false)
					throw new Exception('Error al guardar. Detalles: No se pudo guardar docGenera');
						
				/******************************************
				 ***** Guardar el archivo del informe *****
				 *****************************************/
						
				if($informeTmpPath != null && trim($informeTmpPath) != ""){
					if(is_file($informeTmpPath) === false)
						throw new Exception("Error al guardar la rendición del viático nacional. Detalles: ". 
							"No se puede encontrar el archivo del informe en la ubicación temporal");
						
					if(is_readable($informeTmpPath) === false)
						throw new Exception("Error al guardar la rendición del viático nacional. Detalles: ". 
							"No se tienen permisos de lectura para el archivo del informe en la ubicación temporal");
						
					if(is_writable(SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH) === false)
						throw new Exception("Error al guardar la rendición del viático nacional. Detalles: ". 
							"No se tienen permisos de escritura sobre el directorio de uploads de la " . 
							"rendición del viático nacional: " . SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH);
						
					if(rename($informeTmpPath, SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH."/".$rendicion->GetInformeFileName()) === false)
						throw new Exception("Error al guardar la rendición del viático nacional. Detalles: ". 
							"No se pudo copiar el archivo del informe desde la ubicación temporal");
				}
				/*
				throw new Exception('Error de prueba al guardar la rendición del viático nacional. Detalles: 
						' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				*/
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				return $idRendicion;
				
		}catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarRendicion(EntidadRendicionViaticoNacional $rendicion)
	{
		try {
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			if($resultTransaction === false)
				throw new Exception("Error al iniciar la transacción. Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$horaFechaActual = date("d/m/Y H:i:s");
				
			$query = "
					UPDATE
							safi_rendicion_viatico_nacional
					SET
						fecha_rendicion = to_date('".$rendicion->GetFechaRendicion()."', 'DD/MM/YYYY'),
						fecha_ultima_modificacion = to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
						fecha_inicio_viaje = to_date('".$rendicion->GetFechaInicioViaje()."', 'DD/MM/YYYY'),
						fecha_fin_viaje = to_date('".$rendicion->GetFechaFinViaje()."', 'DD/MM/YYYY'),
						objetivos_viaje = '".$GLOBALS['SafiClassDb']->Quote($rendicion->GetObjetivosViaje())."',
						monto_anticipo = ".$rendicion->GetMontoAnticipo().",
						total_gastos = ".$rendicion->GetTotalGastos().",
						monto_reintegro = ".$rendicion->GetMontoReintegro().",
						reintegro_banco_id = ".((($banco=$rendicion->GetReintegroBanco()) != null && $banco->GetId()!=null && trim($banco->GetId()) != '') ?
								"'".trim($banco->GetId())."'" : "NULL").",
						reintegro_referencia = ".(($rendicion->GetReintegroReferencia() != null && trim($rendicion->GetReintegroReferencia()) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($rendicion->GetReintegroReferencia()))."'" : "NULL").",
						reintegro_fecha = ".(($rendicion->GetReintegroFecha() != null && trim($rendicion->GetReintegroFecha()) != '') ?
								"to_date('".trim($rendicion->GetReintegroFecha())."', 'DD/MM/YYYY')" : "NULL").",
						observaciones = ".(($rendicion->GetObservaciones() != null && trim($rendicion->GetObservaciones()) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($rendicion->GetObservaciones()))."'" : "NULL")."
					WHERE
						id = '" . $rendicion->GetId() . "'
				";	
				
				if($GLOBALS['SafiClassDb']->Query($query) === false)
					throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			return $rendicion->GetId();
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetRendicionById($idRendicion)
	{
		$rendicion = null;
		
		try{
			$query = "
				SELECT
					rendicion.id,
					rendicion.viatico_nacional_id,
					to_char(rendicion.fecha_rendicion, 'DD/MM/YYYY') AS fecha_rendicion,
					to_char(rendicion.fecha_registro, 'DD/MM/YYYY HH24:MI:SS') AS fecha_registro,
					to_char(rendicion.fecha_ultima_modificacion, 'DD/MM/YYYY HH24:MI:SS') AS fecha_ultima_modificacion,
					to_char(rendicion.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
					to_char(rendicion.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
					rendicion.objetivos_viaje,
					rendicion.monto_anticipo,
					rendicion.total_gastos,
					rendicion.monto_reintegro,
					rendicion.reintegro_banco_id,
					rendicion.reintegro_referencia,
					to_char(rendicion.reintegro_fecha, 'DD/MM/YYYY') AS reintegro_fecha,
					rendicion.observaciones,
					rendicion.informe_file_name,
					rendicion.depe_id,
					rendicion.usua_login,
					banco.banc_nombre AS banco_nombre,
					banco.banc_www AS banco_www,
					banco.esta_id AS banco_esta_id,
					banco.usua_login AS banco_usua_login,
					dependencia.depe_nombre AS dependencia_nombre,
					dependencia.depe_nombrecort AS dependencia_nombrecort,
					dependencia.depe_id_sup AS dependencia_id_sup,
					dependencia.depe_nivel AS dependencia_nivel,
					dependencia.depe_cosige AS dependencia_cosige,
					dependencia.usua_login AS dependencia_usua_login,
					dependencia.depe_observa AS dependencia_observa,
					dependencia.esta_id AS dependencia_esta_id
				FROM
					safi_rendicion_viatico_nacional rendicion
					LEFT JOIN sai_banco banco ON (banco.banc_id = rendicion.reintegro_banco_id)
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = rendicion.depe_id)
				WHERE
					rendicion.id = '".$idRendicion."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener la rendición del viático nacional. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$rendicion = self::LlenarRendicion($row);
			}
					
			return $rendicion;
			
		}catch(Exception $e){
			error_log($e, 0);
			return null;
		}
	}
	
	public static function BuscarRendicion($params = array())
	{
		$dataRendiciones = array();
		
		try
		{
			if(is_array($params) && count($params)>0)
			{
				$where = '';
				$fechaInicio = '';
				$fechaFin = '';
				
				if(isset($params['fechaInicio']) && trim($params['fechaInicio']) != ''){
					$fechaInicio = $params['fechaInicio']; 
				}
				
				if(isset($params['fechaFin']) && trim($params['fechaFin']) != ''){
					$fechaFin = $params['fechaFin']; 
				}
				
				if($fechaInicio != '' && $fechaFin != ''){
					$where = "
						rendicion.fecha_rendicion BETWEEN
							to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
							to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					";
				} else if ($fechaInicio != ''){
					$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($params['fechaInicio'])
						."', 'DD/MM/YYYY') <= rendicion.fecha_rendicion";
				} else if ($fechaFin != '') {
					$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($params['fechaFin'])
						." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= rendicion.fecha_rendicion";
				}
				
				if(isset($params['idRendicion']) && trim($params['idRendicion']) != ''){
					if($where != ''){
						$where .= " AND ";
					}
					$where = "lower(rendicion.id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($params['idRendicion'])) . "'";
				}
				
				if($where == ''){
					return $viaticosNacionales;
				}
				
				if(isset($params['idDependencia']) && $params['idDependencia'] != null && $params['idDependencia'] != ''){
					$where .= " AND rendicion.depe_id = '" . $params['idDependencia'] . "'";
				}
				
				$query = "
					SELECT
						dg.docg_id,
						dg.wfob_id_ini,
						dg.wfca_id,
						dg.usua_login,
						dg.perf_id,
						to_char(dg.docg_fecha, 'DD/MM/YYYY HH24:MI:SS') AS docg_fecha,
						dg.esta_id,
						dg.docg_prioridad,
						dg.perf_id_act,
						dg.estado_pres,
		  				dg.numero_reserva,
		  				dg.fuente_finan,
						rendicion.id,
						rendicion.viatico_nacional_id,
						to_char(rendicion.fecha_rendicion, 'DD/MM/YYYY') AS fecha_rendicion,
						to_char(rendicion.fecha_registro, 'DD/MM/YYYY HH24:MI:SS') AS fecha_registro,
						to_char(rendicion.fecha_ultima_modificacion, 'DD/MM/YYYY HH24:MI:SS') AS fecha_ultima_modificacion,
						to_char(rendicion.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
						to_char(rendicion.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
						rendicion.objetivos_viaje,
						rendicion.monto_anticipo,
						rendicion.total_gastos,
						rendicion.monto_reintegro,
						rendicion.reintegro_banco_id,
						rendicion.reintegro_referencia,
						to_char(rendicion.reintegro_fecha, 'DD/MM/YYYY') AS reintegro_fecha,
						rendicion.observaciones,
						rendicion.informe_file_name,
						rendicion.depe_id,
						rendicion.usua_login
					FROM
						sai_doc_genera dg
						INNER JOIN safi_rendicion_viatico_nacional rendicion ON (rendicion.id = dg.docg_id)
					" . ($where != '' ? " WHERE " . $where : '') . "
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al obtener las rendición de viáticos nacionales. Detalles: ". 
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				while($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$dataRendiciones[$row['docg_id']]['ClassDocGenera'] = self::LlenarDocGenera($row);
					$dataRendiciones[$row['docg_id']]['ClassRendicionViaticoNacional'] = self::LlenarRendicion($row);
				}
				
			}
		}catch(Exception $e){
			error_log($e, 0);
			return false;
		}
		
		return $dataRendiciones;
	}
	
	private static function LlenarRendicion($row)
	{
		$banco = null;
		$dependencia = null;
		
		if($row['reintegro_banco_id']!=null && trim($row['reintegro_banco_id'])!=''){
			$banco = new EntidadBanco();
			$banco->SetId(trim($row['reintegro_banco_id']));
			
			if(isset($row['banco_nombre'])){
				$banco->SetNombre($row['banco_nombre']);
				$banco->SetSitioWeb($row['banco_www']);
				$banco->SetIdEstatus($row['banco_esta_id']);
				$banco->SetUsuaLogin($row['banco_usua_login']);
			}
		}
		
		if($row['depe_id']!=null && trim($row['depe_id'])!=''){
			$dependencia = new EntidadDependencia();
			$dependencia->SetId(trim($row['depe_id']));
			
			if(isset($row['dependencia_nombre'])){
				$dependencia->SetNombre($row['dependencia_nombre']);
				$dependencia->SetNombreCorto($row['dependencia_nombrecort']);
				$dependencia->SetIdDependenciaPadre($row['dependencia_id_sup']);
				$dependencia->SetNivel($row['dependencia_nivel']);
				$dependencia->SetCodigoSigecof($row['dependencia_cosige']);
				$dependencia->SetLoginUsuario($row['dependencia_usua_login']);
				$dependencia->SetObservaciones($row['dependencia_observa']);
				$dependencia->SetIdEstatus($row['dependencia_esta_id']);
			}
		}
		
		$rendicion = new EntidadRendicionViaticoNacional();
		
		$rendicion->SetId($row['id']);
		$rendicion->SetIdViaticoNacional($row['viatico_nacional_id']);
		$rendicion->SetFechaRendicion($row['fecha_rendicion']);
		$rendicion->SetFechaRegistro($row['fecha_registro']);
		$rendicion->SetFechaUltimaModificacion($row['fecha_ultima_modificacion']);
		$rendicion->SetFechaInicioViaje($row['fecha_inicio_viaje']);
		$rendicion->SetFechaFinViaje($row['fecha_fin_viaje']);
		$rendicion->SetObjetivosViaje($row['objetivos_viaje']);
		$rendicion->SetMontoAnticipo($row['monto_anticipo']);
		$rendicion->SetTotalGastos($row['total_gastos']);
		$rendicion->SetMontoReintegro($row['monto_reintegro']);
		$rendicion->SetReintegroBanco($banco);
		$rendicion->SetReintegroReferencia($row['reintegro_referencia']);
		$rendicion->SetReintegroFecha($row['reintegro_fecha']);
		$rendicion->SetObservaciones($row['observaciones']);
		$rendicion->SetInformeFileName($row['informe_file_name']);
		$rendicion->SetDependencia($dependencia);
		$rendicion->SetUsuaLogin($row['usua_login']);
		
		return $rendicion;
	}
	
	private static function LlenarDocGenera($row)
	{
		include_once(SAFI_MODELO_PATH . '/docgenera.php');
		
		$docGenera = new EntidadDocGenera();
		
		$docGenera->SetId($row['docg_id']);
		$docGenera->SetIdWFObjeto($row['wfob_id_ini']);
		$docGenera->SetIdWFCadena($row['wfca_id']);
		$docGenera->SetUsuaLogin($row['usua_login']);
		$docGenera->SetIdPerfil($row['perf_id']);
		$docGenera->SetFecha($row['docg_fecha']);
		$docGenera->SetIdEstatus($row['esta_id']);
		$docGenera->SetPrioridad($row['docg_prioridad']);
		$docGenera->SetIdPerfilActual($row['perf_id_act']);
		$docGenera->SetEstadoPres($row['estado_pres']);
		$docGenera->SetNumeroReserva($row['numero_reserva']);
		$docGenera->SetFuenteFinanciamiento($row['fuente_finan']);
		
		return $docGenera;
	}
	
	public static function ExistsRendicion($idRendicion)
	{
		$exists = false;
		
		try{
			if($idRendicion == null || ($idRendicion=trim($idRendicion))=='')
				return $exists;
				
			$query = "
				SELECT count(*) FROM safi_rendicion_viatico_nacional WHERE id = '".$GLOBALS['SafiClassDb']->Quote($idRendicion)."'
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al verificar si existe la rendición. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			if($GLOBALS['SafiClassDb']->FetchOne($result) == 1){
				$exists = true;
			}
			
			return $exists;
			
		} catch (Exception $e){
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetRendicionEnBandeja($params)
	{
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : null;
		$estatus = is_array($params['estatus']) ? $params['estatus'] : null;
		$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;
		
		$idTipoDocumento = GetConfig("preCodigoRendicionViaticoNacional");
		
		$where = " dg.docg_id LIKE '".$idTipoDocumento."%'";
		
		if($idPerfilActual == null && $idPerfilActual == '')
			return array();
			
		$where .= " AND dg.perf_id_act = '".$idPerfilActual."'";
		
		if($estatus == null || count($estatus)==0)
			return array();
		
		$where .= " AND ( dg.esta_id = " . implode('OR dg.esta_id = ', $estatus) . ")";
		
		if($idDependencia != null){
			$where .= " AND rendicion.depe_id = '" . $idDependencia . "'";
		}
			
		return self::__GetRendicionBadejasByWhere($where);
	}
	
	public static function GetRendicionPorEnviar($params)
	{
		$usuaLogin = isset($params['usuaLogin']) ? $params['usuaLogin'] : '';
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : '';
		
		$idTipoDocumento = GetConfig("preCodigoRendicionViaticoNacional");
		$estadoPorEnviar = 59;
		
		if($usuaLogin != '' && $idPerfilActual != ''){
			$where = "
	  			dg.docg_id LIKE '".$idTipoDocumento."%' AND
	  			dg.usua_login = '".$usuaLogin."' AND
	  			dg.esta_id = ".$estadoPorEnviar." AND
	  			dg.perf_id_act = '".$idPerfilActual."'
			";
			
			return self::__GetRendicionBadejasByWhere($where);
		}
		
		return array();
	}
	
	public static function GetRendicionEnTransito($params = array())
	{
		try
		{
			$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;
			
			$idTipoDocumento = GetConfig("preCodigoRendicionViaticoNacional");
			$estadoEnTransito = 10;
			
			$where = "
	  			dg.docg_id LIKE '".$idTipoDocumento."%' AND
	  			dg.esta_id = ".$estadoEnTransito."
			";
			
			$whereEnPerfiles = "";
			if(is_array(($enPerfiles=$params['enPerfiles'])) && count($enPerfiles) > 0)
			{
				$whereEnPerfiles .= "
					dg.perf_id_act IN ('".implode("', '", $enPerfiles)."')
				";
			}
			
			$whereEnCargosFundacion = "";
			if(is_array(($enCargosFundacion=$params['enCargosFundacion'])) && count($enCargosFundacion) > 0)
			{
				$whereEnCargosFundacion .= "
					substring(dg.perf_id_act FROM 1 FOR 2)  IN ('".implode("', '", $enCargosFundacion)."')
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
				$where .= " AND rendicion.depe_id = '" . $idDependencia . "'";
			}
			
			return self::__GetRendicionBadejasByWhere($where);
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
		
	}
	
	private static function __GetRendicionBadejasByWhere($where)
	{
		include_once(SAFI_ENTIDADES_PATH . '/docgenera.php' );

		$dataRendiciones = array();
		
		try {
			if($where != null && $where != ''){
				$query = "
					SELECT
						dg.docg_id,
						dg.wfob_id_ini,
						dg.wfca_id,
						dg.usua_login,
						dg.perf_id,
						to_char(dg.docg_fecha, 'DD/MM/YYYY HH24:MI:SS') AS docg_fecha,
						dg.esta_id,
						dg.docg_prioridad,
						dg.perf_id_act,
						dg.estado_pres,
		  				dg.numero_reserva,
		  				dg.fuente_finan,
						rendicion.id,
						rendicion.viatico_nacional_id,
						to_char(rendicion.fecha_rendicion, 'DD/MM/YYYY') AS fecha_rendicion,
						to_char(rendicion.fecha_registro, 'DD/MM/YYYY HH24:MI:SS') AS fecha_registro,
						to_char(rendicion.fecha_ultima_modificacion, 'DD/MM/YYYY HH24:MI:SS') AS fecha_ultima_modificacion,
						to_char(rendicion.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
						to_char(rendicion.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
						rendicion.objetivos_viaje,
						rendicion.monto_anticipo,
						rendicion.total_gastos,
						rendicion.monto_reintegro,
						rendicion.reintegro_banco_id,
						rendicion.reintegro_referencia,
						to_char(rendicion.reintegro_fecha, 'DD/MM/YYYY') AS reintegro_fecha,
						rendicion.observaciones,
						rendicion.informe_file_name,
						rendicion.depe_id,
						rendicion.usua_login
					FROM
						sai_doc_genera dg
						INNER JOIN safi_rendicion_viatico_nacional rendicion ON (rendicion.id = dg.docg_id)
					" . ($where != '' ? " WHERE " . $where : '') . "
					ORDER BY
	  					dg.docg_fecha
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al obtener las rendiciones de viáticos nacionales de alguna de las bandejas. Detalles: ". 
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$dataRendiciones[$row['docg_id']]['ClassDocGenera'] = self::LlenarDocGenera($row);
					$dataRendiciones[$row['docg_id']]['ClassRendicionViaticoNacional'] = self::LlenarRendicion($row);
				}
			}
			
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
		
		return $dataRendiciones;
	}

	public static function BuscarIdsRendicionViaticosNacionales($codigoDocumento, $idDependencia, $numLimit) {
		$idsViaticos = null;
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
			throw new Exception("Error al buscar los ids de rendiciones de viaticos nacionales. Detalles: El código del documento o la dependencia es nulo o vacío");
	
			$query = "
		            SELECT
		               v.id
		            FROM
		                safi_rendicion_viatico_nacional v, sai_doc_genera d
		            WHERE
		               v.id = d.docg_id AND
		               v.id LIKE '%".$codigoDocumento."%' AND
		               v.depe_id = '".$idDependencia."' AND
		               d.esta_id <> 15 AND 
		               v.id NOT IN (
		               			SELECT nro_documento 
		               			FROM registro_documento
		               			WHERE tipo_documento = 'rvna'
		               			AND id_estado=1
		               			AND user_depe='".$_SESSION['user_depe_id']."'
		               			) AND 
		               v.fecha_registro LIKE '".$_SESSION['an_o_presupuesto']."%'  
		            ORDER BY v.id 
					LIMIT
					".$numLimit."
		        ";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de rendicion de viáticos nacionales. Detalles: ".
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

	public static function BuscarInfoRendicionViaticoNacional($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/viaticoresponsableasignacion.php' );
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='')
			throw new Exception("Error al buscar la información de la rendicion del viático nacional");
	
			$query = "
				SELECT TO_CHAR(vi.fecha_viatico, 'dd-mm-yyyy') AS fecha, 
					r.viatico_id AS id,
					e.empl_cedula AS id_beneficiario,
					e.empl_nombres || ' ' ||e.empl_apellidos AS beneficiario,
					vi.objetivos_viaje AS objetivos
				FROM safi_responsable_viatico r, sai_empleado e, safi_viatico vi, safi_rendicion_viatico_nacional rvn
				WHERE r.empleado_cedula = e.empl_cedula 
					AND r.viatico_id = vi.id
					AND rvn.viatico_nacional_id = r.viatico_id
					AND rvn.id= '".$codigoDocumento."' 
				UNION
				SELECT TO_CHAR(vi.fecha_viatico, 'dd-mm-yyyy') AS fecha,
					r.viatico_id AS id,
					v.benvi_cedula AS id_beneficiario,
					v.benvi_nombres || ' ' || v.benvi_apellidos AS beneficiario,
					vi.objetivos_viaje AS objetivos
				FROM safi_responsable_viatico r, sai_viat_benef v, safi_viatico vi, safi_rendicion_viatico_nacional rvn 
				WHERE r.beneficiario_viatico_cedula = v.benvi_cedula
					AND r.viatico_id = vi.id
					AND rvn.viatico_nacional_id = r.viatico_id
					AND rvn.id='".$codigoDocumento."'";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de rendicion de viaticos nacionales. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$ids = array();
	
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[0] = $row['id'];
				$ids[1] = $row['id_beneficiario'].":".$row['beneficiario'];
				$asignaciones = SafiModeloViaticoResponsableAsignacion::GetAsignacionesByIdViatico($ids[0]);
				$ids[2] = CalcularMontoTotalAsignacionesViaticoNacional( $asignaciones);
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
	
	public static function GetSelectFildsRendicion()
	{
		return  "
			rendicion.id AS rendicion_id,
			rendicion.viatico_nacional_id AS rendicion_viatico_nacional_id,
			to_char(rendicion.fecha_rendicion, 'DD/MM/YYYY') AS rendicion_fecha_rendicion,
			rendicion.monto_anticipo AS rendicion_monto_anticipo,
			rendicion.total_gastos AS rendicion_total_gastos,
			rendicion.monto_reintegro AS rendicion_monto_reintegro
		";
	}
	
	public static function LlenarRendicion2($row)
	{
		$rendicion = new EntidadRendicionViaticoNacional();
		
		$rendicion->SetId($row['rendicion_id']);
		$rendicion->SetIdViaticoNacional($row['rendicion_viatico_nacional_id']);
		$rendicion->SetFechaRendicion($row['rendicion_fecha_rendicion']);
		$rendicion->SetMontoAnticipo($row['rendicion_monto_anticipo']);
		$rendicion->SetTotalGastos($row['rendicion_total_gastos']);
		$rendicion->SetMontoReintegro($row['rendicion_monto_reintegro']);

		return $rendicion;
	}
}