<?php
include_once(SAFI_ENTIDADES_PATH . '/viaticonacional.php');
include_once(SAFI_ENTIDADES_PATH . '/docgenera.php');
include_once(SAFI_ENTIDADES_PATH . '/memo.php');
include_once(SAFI_ENTIDADES_PATH . '/revisionesdoc.php');

// Modelo
include_once (SAFI_MODELO_PATH . '/accioncentralizadaespecifica.php');
include_once (SAFI_MODELO_PATH . '/memo.php');
include_once (SAFI_MODELO_PATH . '/proyectoespecifica.php');
include_once (SAFI_MODELO_PATH . '/viaticoresponsableasignacion.php');
include_once (SAFI_MODELO_PATH . '/rendicionViaticoNacional.php');


class SafiModeloViaticoNacional
{
	public static function GuardarViaticoNacional($params)
	{
		try{
		
			$result = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($result === true)
			{
				$proyectoAccionCentralizadaFields = array();
				$proyectoAccionCentralizadaValues = array();

				if(strcmp($params['tipoProyectoAccionCentralizada'], 'proyecto') == 0){
					$proyectoAccionCentralizadaFields[] = 'proyecto_id';
					$proyectoAccionCentralizadaFields[] = 'proyecto_anho';
					$proyectoAccionCentralizadaFields[] = 'proyecto_especifica_id';
					
					$proyectoAccionCentralizadaValues[] = "'".$params['idProyectoAccionCentralizada']."'";
					$proyectoAccionCentralizadaValues[] = $params['annoPresupuesto'];
					$proyectoAccionCentralizadaValues[] = "'".$params['idAccionEspecifica']."'";
					
				} else if(strcmp($params['tipoProyectoAccionCentralizada'], 'accionCentralizada') == 0){
					$proyectoAccionCentralizadaFields[] = 'accion_central_id';
					$proyectoAccionCentralizadaFields[] = 'accion_central_anho';
					$proyectoAccionCentralizadaFields[] = 'accion_central_especifica_id';
					
					$proyectoAccionCentralizadaValues[] = "'".$params['idProyectoAccionCentralizada']."'";
					$proyectoAccionCentralizadaValues[] = $params['annoPresupuesto'];
					$proyectoAccionCentralizadaValues[] = "'".$params['idAccionEspecifica']."'";
				}
				
				// Generar el id del documento viatico nacional
				$query = "
					SELECT
						sai_generar_codigo(
								'vnac',
								'".$params['idDependencia']."',
								'fecha_viatico',
								'id'
						)
				";
				
				$result = $GLOBALS['SafiClassDb']->Query($query);
				
				if($result === false) throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
				if (!($idViatico = $GLOBALS['SafiClassDb']->FetchOne($result))) {
					throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
				$categoriaViatico = $params['idCategoriaViatico'] != null && trim($params['idCategoriaViatico']) != '' ? 
					trim($params['idCategoriaViatico']) : null;
					
				// Guardar los campos de la tabla safi_viatico
				$query = "
					INSERT INTO safi_viatico
						(
							id,
							fecha_viatico,
							fecha_inicio_viaje,
							fecha_fin_viaje,
							objetivos_viaje,
							usua_login,
							".implode(',' , $proyectoAccionCentralizadaFields).",
							depe_id,
							observaciones,
							categoria_id,
							edo_id
						)
					VALUES
						(
							'".$idViatico."',
							to_timestamp('".$params['fechaViatico']."', 'DD/MM/YYYY HH24:MI:SS'),
							to_date('".$params['fechaInicioViaje']."', 'DD/MM/YYYY'),
							to_date('".$params['fechaFinViaje']."', 'DD/MM/YYYY'),
							'".$GLOBALS['SafiClassDb']->Quote($params['objetivosViaje'])."',
							'".$params['loginRegistrador']."',
							".implode(',' , $proyectoAccionCentralizadaValues).",
							'".$params['idDependencia']."',
							".(($params['observaciones'] != null && trim($params['observaciones']) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($params['observaciones']))."'" : "NULL").",
							".($categoriaViatico != null ?
								"'" . $categoriaViatico . "'" : "NULL").",
							".(($params['idEstado'] != null && trim($params['idEstado']) != '') ?
								"'".trim($params['idEstado'])."'" : "NULL")."
						)
				";
				
				$result = $GLOBALS['SafiClassDb']->Query($query);
				
				if($result === false) throw new Exception('Error al guardar 3. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
				// Guardar la informacion relacionada con las redes
				if(	$categoriaViatico != null && $params['idRed'] != null &&
				 	trim($params['idRed']) != '' && strcmp($params['idRed'], "0") != 0
				 ){
					$query = "
							INSERT INTO safi_viatico_categoria_red
								(
									viatico_id,
									categoria_id,
									red_id
		  						)
		  					VALUES
		  						(
		  							'".$idViatico."',
		  							".$categoriaViatico.",
		  							".trim($params['idRed'])."
		  						)
						";
					
					$result = $GLOBALS['SafiClassDb']->Query($query);
				
					if($result === false) throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
				// Guardar la información relacionada con los infocentros del viatico
				if(isset($params['infocentros']) && is_array($params['infocentros'])){
					foreach($params['infocentros'] as $infocentro){
						$query = "
							INSERT INTO safi_viatico_infocentro
								(
									viatico_id,
		  							infocentro_id
		  						)
		  					VALUES
		  						(
		  							'".$idViatico."',
		  							'".$infocentro->GetId()."'
		  						)
						";
						
						$result = $GLOBALS['SafiClassDb']->Query($query);
					
						if($result === false) throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					}
				}
				
				// Guardar informacion relacionada con las rutas
				if(isset($params['rutas']) && is_array($params['rutas'])){
					foreach($params['rutas'] as $ruta){
						$query = "
							INSERT INTO safi_ruta_viatico
								(
									viatico_id,
									fecha_inicio,
									fecha_fin,
									dias_alimentacion,
									dias_hospedaje,
									dias_transporte_interurbano,
									tipo_transporte,
									pasaje_ida_vuelta,
									transporte_residencia_aeropuerto,
									transporte_aeropuerto_residencia,
									tasa_aeroportuaria_ida,
									tasa_aeroportuaria_vuelta,
									origen_parroquia_id,
									origen_municipio_id,
									origen_ciudad_id,
									origen_edo_id,
									origen_direccion,
									destino_parroquia_id,
									destino_municipio_id,
									destino_ciudad_id,
									destino_edo_id,
									destino_direccion,
									observaciones
								)
							VALUES
								(
									'".$idViatico."',
									to_date('".$ruta->GetFechaInicio()."', 'DD/MM/YYYY'),
									to_date('".$ruta->GetFechaFin()."', 'DD/MM/YYYY'),
									'".$ruta->GetDiasAlimentacion()."',
									'".$ruta->GetDiasHospedaje()."',
									'".$ruta->GetUnidadTransporteInterurbano()."',
									".(SafiIsId($ruta->GetIdTipoTransporte()) ? $ruta->GetIdTipoTransporte() : "NULL").",
									".($ruta->GetPasajeIdaVuelta() ? "TRUE" : "FALSE").",
									".($ruta->GetResidenciaAeropuerto() ? "TRUE" : "FALSE").",
									".($ruta->GetAeropuertoResidencia() ? "TRUE" : "FALSE").",
									".($ruta->GetTasaAeroportuariaIda() ? "TRUE" : "FALSE").",
									".($ruta->GetTasaAeroportuariaVuelta() ? "TRUE" : "FALSE").",
									".(SafiIsId($ruta->GetIdFromParroquia()) ? $ruta->GetIdFromParroquia() : "NULL").",
									".(SafiIsId($ruta->GetIdFromMunicipio()) ? $ruta->GetIdFromMunicipio() : "NULL").",
									".(SafiIsId($ruta->GetIdFromCiudad()) ? $ruta->GetIdFromCiudad() : "NULL").",
									".(SafiIsId($ruta->GetIdFromEstado()) ? $ruta->GetIdFromEstado() : "NULL").",
									".(trim($ruta->GetFromDireccion())
										? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetFromDireccion()))."'" : "NULL").",
									".(SafiIsId($ruta->GetIdToParroquia()) ? $ruta->GetIdToParroquia() : "NULL").",
									".(SafiIsId($ruta->GetIdToMunicipio()) ? $ruta->GetIdToMunicipio() : "NULL").",
									".(SafiIsId($ruta->GetIdToCiudad()) ? $ruta->GetIdToCiudad() : "NULL").",
									".(SafiIsId($ruta->GetIdToEstado()) ? $ruta->GetIdToEstado() : "NULL").",
									".(trim($ruta->GetToDireccion())
										? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetToDireccion()))."'" : "NULL").",
									".(trim($ruta->GetObservaciones())
										? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetObservaciones()))."'" : "NULL")."
								)
						";
						
						$result = $GLOBALS['SafiClassDb']->Query($query);
					
						if($result === false) throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					}
				} 
				
				// Guardar la informacióin relacionada con el responsable del viatico
				if(isset($params['responsable']) && ($params['responsable'] instanceof EntidadResponsableViatico)) {
					$responsable = $params['responsable']; 
					
					$responsableFields = array();
					$responsableValues = array();
	
					if(strcmp($responsable->GetTipoResponsable(), 'empleado') == 0){
						$responsableFields[] = 'empleado_cedula';
						$responsableValues[] = "'".$responsable->GetCedula()."'";
					} if(strcmp($responsable->GetTipoResponsable(), 'beneficiario') == 0){
						$responsableFields[] = 'beneficiario_viatico_cedula';
						$responsableValues[] = "'".$responsable->GetCedula()."'";
					}
					
					$idResponsable = $GLOBALS['SafiClassDb']->NextId('safi_responsable_viatico__id__seq');
					
					if($idResponsable == false || !SafiIsId($idResponsable)){
						throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					}
					
					$query = "
						INSERT INTO safi_responsable_viatico
							(	id,
								viatico_id,
								".implode(',', $responsableFields).",
								numero_cuenta_bancaria,
								tipo_cuenta_bancaria,
								banco_cuenta_bancaria
							)
						VALUES
							(
								".$idResponsable.",
								'".$idViatico."',
								".implode(',', $responsableValues).",
								".(trim($responsable->GetNumeroCuenta()) ? "'".trim($responsable->GetNumeroCuenta())."'" : "NULL").",
								".(trim($responsable->GetTipoCuenta()) ? "'".trim($responsable->GetTipoCuenta())."'" : "NULL").",
								".(trim($responsable->GetBanco())
									? "'".$GLOBALS['SafiClassDb']->Quote(trim($responsable->GetBanco()))."'" : "NULL")."
							)
					";
					
					$result = $GLOBALS['SafiClassDb']->Query($query);
						
					if($result === false) throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
				// Guardar la información relacionada con las asignaciones del viatico
				if(isset($params['viaticoResponsableAsignaciones']) &&  is_array($params['viaticoResponsableAsignaciones']))
				{
					foreach($params['viaticoResponsableAsignaciones'] as $responsableAsignaciones){
						if($responsableAsignaciones instanceof EntidadViaticoResponsableAsignacion){
							$query = "
								INSERT INTO safi_viatico_responsable_asignacion
									(
										viatico_id,
										responsable_id,
										asignacion_viatico_id,
										monto,
										unidad_medida,
										unidades
									)
										VALUES
									(
										'".$idViatico."',
										".$idResponsable.",
										".$responsableAsignaciones->GetAsignacionViaticoId().",
										".$responsableAsignaciones->GetMonto().",
										".$responsableAsignaciones->GetUnidadMedida().",
										".$responsableAsignaciones->GetUnidades()."
									)
							";
							
							$result = $GLOBALS['SafiClassDb']->Query($query);
						
							if($result === false)
								throw new Exception('Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						}
					}	
				}
				
				/************************************************************************
				* Guardar información referente a la flujo de trabajo de la cadena
				*************************************************************************/
				
				include_once(SAFI_MODELO_PATH . '/wfcadena.php');
				
				$documento = new EntidadDocumento();
				$documento->SetId('vnac');  // Establecer el id del documento de viatico nacional = vnac
				
				$wFObjetoInicial = new EntidadWFObjeto();
				$wFObjetoInicial->SetId(1);	// Establecer el id del objeto inicial que siempre es 1
				
				$dependencia = new EntidadDependencia();
				
				switch ($params['idDependencia']){
					case '150': // Presidencia
					case '350': // Dirección Ejecutiva
						$dependencia->SetId($params['idDependencia']);
						break;
					default:
						$dependencia->SetId(null);
						break;
				}
				
				// Establecer los parámetros para buscar la cadena inicial de viaticos nacionales
				$cadenaFind = new EntidadWFCadena();
				$cadenaFind->SetWFObjetoInicial($wFObjetoInicial);
				$cadenaFind->SetDocumento($documento);
				$cadenaFind->SetDependencia($dependencia);
				
				
				// Obtener la cadena inicial de viaticos nacionales
				$cadena = SafiModeloWFCadena::GetWFCadena($cadenaFind);
				
				if($cadena == null)
					throw new Exception('Error al guardar. Detalles: WFCadena inicial no encontrada');
					
				if($cadena->GetWFCadenaHijo() == null)
					throw new Exception('Error al guardar. Detalles: WFCadena hija no encontrada');
					
				// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
				$cadenaHijo = SafiModeloWFCadena::GetWFCadena($cadena->GetWFCadenaHijo());
				
				if($cadenaHijo == null)
					throw new Exception('Error al guardar. Detalles: WFCadena hija no encontrada');
					
				if($cadenaHijo->GetWFGrupo() == null)
					throw new Exception('Error al guardar. Detalles: WFGrupo de WFCadena hija no encontrado');
				
				include_once(SAFI_MODELO_PATH . '/dependenciacargo.php');
				
				// Obtener el siguiente cargo al que será enviado el documento
				$perfilActual = SafiModeloDependenciaCargo::
					GetSiguienteCargoDeCadena($params['idDependencia'], $cadenaHijo->GetWFGrupo()->GetPerfiles());
				
				if($perfilActual == null)	throw new Exception(
					'Error al guardar. Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena');
				
				include_once(SAFI_MODELO_PATH . '/docgenera.php');
				
				$docGenera = new EntidadDocGenera();
				
				$docGenera->SetId($idViatico);
				$docGenera->SetIdWFObjeto(1); // 1 = Iniciar
				$docGenera->SetIdWFCadena($cadena->GetId());
				$docGenera->SetUsuaLogin($params['loginRegistrador']);
				$docGenera->SetIdPerfil($params['perfilRegistrador']);
				$docGenera->SetFecha($params['fechaViatico']);
				$docGenera->SetIdEstatus(59); // 59 = estado "por enviar"
				$docGenera->SetPrioridad(1);
				$docGenera->SetIdPerfilActual($perfilActual->GetId());
				
				// Guardar el registro del documento en docGenera (estado de la cadena)
				if(SafiModeloDocGenera::GuardarDocGenera($docGenera) === false)
					throw new Exception('Error al guardar. Detalles: No se pudo guardar docGenera');
				
				$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
				return $idViatico;
				
			} else {
				throw new Exception('Error al iniciar la transacci&oacute;n');
			}
			
		}catch(Exception $e){
			$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarViaticoNacional(EntidadViaticoNacional $viatico)
	{
		if($viatico instanceof EntidadViaticoNacional){
			try{
		
				$result = $GLOBALS['SafiClassDb']->StartTransaction();
				
				if($result === true)
				{
					// Obtener el viatico, a actualizar, desde la base de datos
					$viaticoSaved = self::GetViaticoNacionalById($viatico->GetId());
					
					// Borrar la información que relaciona viatico, categoria y red
					$query = "
						DELETE FROM
							safi_viatico_categoria_red
						WHERE
							viatico_id = '".$viatico->GetId()."'
					";
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					$idEstado = 
						(
							$viatico->GetEstado() != null && ($idEstado=$viatico->GetEstado()->GetId()) != null &&
							($idEstado=trim($idEstado)) != ''
						) ? $idEstado : null;
					
					$idCategoriaViatico = 
						(
							$viatico->GetCategoriaViatico() != null &&
							($idCategoriaViatico=$viatico->GetCategoriaViatico()->GetId()) != null &&
							($idCategoriaViatico=trim($idCategoriaViatico)) != ''
						) ? $idCategoriaViatico : null;
						
					$proyectoAccionCentralizadaQuery = "";
					
					if(
						$viatico->GetProyectoId() != null && $viatico->GetProyectoId() != '' &&
						$viatico->GetProyectoAnho() != null && $viatico->GetProyectoAnho() != '' &&
						$viatico->GetProyectoEspecificaId() != null && $viatico->GetProyectoEspecificaId() != ''
					){
						$proyectoAccionCentralizadaQuery = "
							proyecto_id = '".$viatico->GetProyectoId()."',
							proyecto_anho = ".$viatico->GetProyectoAnho().",
							proyecto_especifica_id = '".$viatico->GetProyectoEspecificaId()."',
							accion_central_id = NULL,
							accion_central_anho = NULL,
							accion_central_especifica_id = NULL
						";		
					}
					else if(
						$viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaId() != '' &&
						$viatico->GetAccionCentralizadaAnho() != null && $viatico->GetAccionCentralizadaAnho() != '' &&
						$viatico->GetAccionCentralizadaEspecificaId() != null && $viatico->GetAccionCentralizadaEspecificaId() != ''
					){
						$proyectoAccionCentralizadaQuery = "
							proyecto_id = NULL,
							proyecto_anho = NULL,
							proyecto_especifica_id = NULL,
							accion_central_id = '".$viatico->GetAccionCentralizadaId()."',
							accion_central_anho = ".$viatico->GetAccionCentralizadaAnho().",
							accion_central_especifica_id = '".$viatico->GetAccionCentralizadaEspecificaId()."'
						";
					} else {
						throw new Exception('Falta información del proyecto/accion centralizada. '.
							'Id del proyecto: ' . $viatico->GetProyectoId() . '
							, Id Acción específica del proyecto: ' . $viatico->GetProyectoEspecificaId() . '
							, Anho Proyecto: ' . $viatico->GetAccionCentralizadaAnho() . '
							, Id de la acción centralizada: ' . $viatico->GetAccionCentralizadaId() . '
							, Id de la acción específica  acción centralizada: ' . $viatico->GetAccionCentralizadaEspecificaId() . '
							, Anho de la acción centralizada: ' . $viatico->GetAccionCentralizadaAnho() . '
						');
					}
						
					// Actualizar los campos de la tabla safi_viatico
					$query = "
						UPDATE
							safi_viatico
						SET
							fecha_viatico = to_date('".$viatico->GetFechaViatico()."', 'DD/MM/YYYY'),
							fecha_inicio_viaje = to_date('".$viatico->GetFechaInicioViaje()."', 'DD/MM/YYYY'),
							fecha_fin_viaje = to_date('".$viatico->GetFechaFinViaje()."', 'DD/MM/YYYY'),
							objetivos_viaje = '". $GLOBALS['SafiClassDb']->Quote($viatico->GetObjetivosViaje())."',
							observaciones = ".(($viatico->GetObservaciones() != null && trim($viatico->GetObservaciones()) != '') ?
								"'".$GLOBALS['SafiClassDb']->Quote(trim($viatico->GetObservaciones()))."'" : "NULL").",
							categoria_id = ".($idCategoriaViatico != null ? $idCategoriaViatico : "NULL").",
							edo_id = ".($idEstado != null ? $idEstado : "NULL").",
							".$proyectoAccionCentralizadaQuery."
    					WHERE
    						id = '".$viatico->GetId()."'
					";
				
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					/***********************************************************************
					 Actualizar la información que relaciona viatico, categoria y red
					************************************************************************/
						
					// Actualizar la información que relaciona viatico, categoria y red
					if(	$idCategoriaViatico != null && $viatico->GetRed() != null && ($idRed=$viatico->GetRed()->GetId()) != null &&
						 ($idRed=trim($idRed)) != '' && strcmp($idRed, "0") != 0
					 ){
						$query = "
								INSERT INTO safi_viatico_categoria_red
									(
										viatico_id,
										categoria_id,
										red_id
			  						)
			  					VALUES
			  						(
			  							'".$viatico->GetId()."',
			  							".$idCategoriaViatico.",
			  							".$idRed."
			  						)
							";
						
						$result = $GLOBALS['SafiClassDb']->Query($query);
					
						if($result === false) throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					}
						
						
					/***********************************************************************
					 Actualizar la información relacionada con los infocentros del viatico
					************************************************************************/
						
					// borrar los infocentros asociados al viatico
					$query = "
						DELETE FROM
							safi_viatico_infocentro
						WHERE
							viatico_id = '".$viatico->GetId()."'
					";
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					// insertar los infocentros del viatico
					if(is_array($viatico->GetInfocentros())){
						foreach($viatico->GetInfocentros() as $infocentro){
							if($infocentro instanceof EntidadInfocentro){
								$query = "
									INSERT INTO safi_viatico_infocentro
										(
											viatico_id,
				  							infocentro_id
				  						)
				  					VALUES
				  						(
				  							'".$viatico->GetId()."',
				  							'".$infocentro->GetId()."'
				  						)
								";
							
								if($GLOBALS['SafiClassDb']->Query($query) === false)
									throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
							}
						}
					}
					
					/***********************************************
					Actualizar informacion relacionada con las rutas
					************************************************/
					
					// Borrar las rutas, del viatico guardado, que nmo sean necesarias
					
					// borrar las rutas asociadas al viatico
					$query = "
							DELETE FROM
								safi_ruta_viatico
							WHERE
								viatico_id = '".$viatico->GetId()."'
						";
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());

					// guardar la información de las rutas del viatico
					if(is_array($viatico->GetRutas())){
						foreach($viatico->GetRutas() as $ruta){
							$query = "
								INSERT INTO safi_ruta_viatico
									(
										viatico_id,
										fecha_inicio,
										fecha_fin,
										dias_alimentacion,
										dias_hospedaje,
										dias_transporte_interurbano,
										tipo_transporte,
										pasaje_ida_vuelta,
										transporte_residencia_aeropuerto,
										transporte_aeropuerto_residencia,
										tasa_aeroportuaria_ida,
										tasa_aeroportuaria_vuelta,
										origen_parroquia_id,
										origen_municipio_id,
										origen_ciudad_id,
										origen_edo_id,
										origen_direccion,
										destino_parroquia_id,
										destino_municipio_id,
										destino_ciudad_id,
										destino_edo_id,
										destino_direccion,
										observaciones
									)
								VALUES
									(
										'".$viatico->GetId()."',
										to_date('".$ruta->GetFechaInicio()."', 'DD/MM/YYYY'),
										to_date('".$ruta->GetFechaFin()."', 'DD/MM/YYYY'),
										'".$ruta->GetDiasAlimentacion()."',
										'".$ruta->GetDiasHospedaje()."',
										'".$ruta->GetUnidadTransporteInterurbano()."',
										".(SafiIsId($ruta->GetIdTipoTransporte()) ? $ruta->GetIdTipoTransporte() : "NULL").",
										".($ruta->GetPasajeIdaVuelta() ? "TRUE" : "FALSE").",
										".($ruta->GetResidenciaAeropuerto() ? "TRUE" : "FALSE").",
										".($ruta->GetAeropuertoResidencia() ? "TRUE" : "FALSE").",
										".($ruta->GetTasaAeroportuariaIda() ? "TRUE" : "FALSE").",
										".($ruta->GetTasaAeroportuariaVuelta() ? "TRUE" : "FALSE").",
										".(SafiIsId($ruta->GetIdFromParroquia()) ? $ruta->GetIdFromParroquia() : "NULL").",
										".(SafiIsId($ruta->GetIdFromMunicipio()) ? $ruta->GetIdFromMunicipio() : "NULL").",
										".(SafiIsId($ruta->GetIdFromCiudad()) ? $ruta->GetIdFromCiudad() : "NULL").",
										".(SafiIsId($ruta->GetIdFromEstado()) ? $ruta->GetIdFromEstado() : "NULL").",
										".(trim($ruta->GetFromDireccion())
											? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetFromDireccion()))."'" : "NULL").",
										".(SafiIsId($ruta->GetIdToParroquia()) ? $ruta->GetIdToParroquia() : "NULL").",
										".(SafiIsId($ruta->GetIdToMunicipio()) ? $ruta->GetIdToMunicipio() : "NULL").",
										".(SafiIsId($ruta->GetIdToCiudad()) ? $ruta->GetIdToCiudad() : "NULL").",
										".(SafiIsId($ruta->GetIdToEstado()) ? $ruta->GetIdToEstado() : "NULL").",
										".(trim($ruta->GetToDireccion())
											? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetToDireccion()))."'" : "NULL").",
										".(trim($ruta->GetObservaciones())
											? "'".$GLOBALS['SafiClassDb']->Quote(trim($ruta->GetObservaciones()))."'" : "NULL")."
									)
							";
						
							if($GLOBALS['SafiClassDb']->Query($query) === false)
								throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						}
					}
					
					// borrar las información relacionada con las asignaciones del viatico
					$query = "
							DELETE FROM
								safi_viatico_responsable_asignacion
							WHERE
								viatico_id = '".$viatico->GetId()."'
					";
					
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					/********************************************************************
					Actualizar la informacióin relacionada con el responsable del viatico
					*********************************************************************/
					
					// Actualizar la información relacionada con el/los responsables del viatico
					if($viaticoSaved->GetResponsable() != null && $viaticoSaved->GetResponsable() instanceof EntidadResponsableViatico)
					{
						// Actualizar información del responsable
						if(($viatico->GetResponsable() instanceof EntidadResponsableViatico)) {
							$responsable = $viatico->GetResponsable();
							
							$empleadoCedula = 'NULL';
							$beneficiarioCedula = 'NULL';
							
							if(strcmp($responsable->GetTipoResponsable(), EntidadResponsableViatico::TIPO_EMPLEADO) == 0){
								$empleadoCedula = "'".$responsable->GetCedula()."'";
							} if(strcmp($responsable->GetTipoResponsable(), EntidadResponsableViatico::TIPO_BENEFICIARIO) == 0){
								$beneficiarioCedula = "'".$responsable->GetCedula()."'";
							}
							
							$query = "
								UPDATE
									safi_responsable_viatico
								SET
									empleado_cedula = ".$empleadoCedula.",
									beneficiario_viatico_cedula = ".$beneficiarioCedula.",
									numero_cuenta_bancaria = ".(trim($responsable->GetNumeroCuenta()) ? 
										"'".trim($responsable->GetNumeroCuenta())."'" : "NULL").",
									tipo_cuenta_bancaria = ".(trim($responsable->GetTipoCuenta()) ?
										 "'".trim($responsable->GetTipoCuenta())."'" : "NULL").",
									banco_cuenta_bancaria = ".(trim($responsable->GetBanco()) ?
										 "'".$GLOBALS['SafiClassDb']->Quote(trim($responsable->GetBanco()))."'" : "NULL")."
								WHERE
									id = " . $viaticoSaved->GetResponsable()->GetId() . "
							";
				
							if($GLOBALS['SafiClassDb']->Query($query) === false)
								throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
						}
						
						/*********************************************************************	
						Actualizar la información relacionada con las asignaciones del viatico
						**********************************************************************/
			
						// guardar las información relacionada con las asignaciones del viatico
						if(is_array($viatico->GetViaticoResponsableAsignaciones()))
						{
							foreach($viatico->GetViaticoResponsableAsignaciones() as $responsableAsignaciones){
								if($responsableAsignaciones instanceof EntidadViaticoResponsableAsignacion){
									$query = "
										INSERT INTO safi_viatico_responsable_asignacion
											(
												viatico_id,
												responsable_id,
												asignacion_viatico_id,
												monto,
												unidad_medida,
												unidades
											)
												VALUES
											(
												'".$viatico->GetId()."',
												".$viaticoSaved->GetResponsable()->GetId().",
												".$responsableAsignaciones->GetAsignacionViaticoId().",
												".$responsableAsignaciones->GetMonto().",
												".$responsableAsignaciones->GetUnidadMedida().",
												".$responsableAsignaciones->GetUnidades()."
											)
									";
								
									if($GLOBALS['SafiClassDb']->Query($query) === false)
										throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
								}
							}	
						}
						
					} else { // Insertar el responsable
						// guardar la información relacionada con el/los responsables del viatico
						if(($viatico->GetResponsable() instanceof EntidadResponsableViatico)) {
							$responsable = $viatico->GetResponsable(); 
							
							$responsableFields = array();
							$responsableValues = array();
			
							if(strcmp($responsable->GetTipoResponsable(), EntidadResponsableViatico::TIPO_EMPLEADO) == 0){
								$responsableFields[] = 'empleado_cedula';
								$responsableValues[] = "'".$responsable->GetCedula()."'";
							} if(strcmp($responsable->GetTipoResponsable(), EntidadResponsableViatico::TIPO_BENEFICIARIO) == 0){
								$responsableFields[] = 'beneficiario_viatico_cedula';
								$responsableValues[] = "'".$responsable->GetCedula()."'";
							}
							
							$idResponsable = $GLOBALS['SafiClassDb']->NextId('safi_responsable_viatico__id__seq');
							
							if($idResponsable == false || !SafiIsId($idResponsable)){
								throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
							}
							
							$query = "
								INSERT INTO safi_responsable_viatico
									(	id,
										viatico_id,
										".implode(',', $responsableFields).",
										numero_cuenta_bancaria,
										tipo_cuenta_bancaria,
										banco_cuenta_bancaria
									)
								VALUES
									(
										".$idResponsable.",
										'".$viatico->GetId()."',
										".implode(',', $responsableValues).",
										".(trim($responsable->GetNumeroCuenta()) ? "'".trim($responsable->GetNumeroCuenta())."'" : "NULL").",
										".(trim($responsable->GetTipoCuenta()) ? "'".trim($responsable->GetTipoCuenta())."'" : "NULL").",
										".(trim($responsable->GetBanco()) ? "'".trim($responsable->GetBanco())."'" : "NULL")."
									)
							";
								
							if($GLOBALS['SafiClassDb']->Query($query) === false)
								throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
								
							/*********************************************************************	
							Actualizar la información relacionada con las asignaciones del viatico
							**********************************************************************/
				
							// guardar las información relacionada con las asignaciones del viatico
							if(is_array($viatico->GetViaticoResponsableAsignaciones()))
							{
								foreach($viatico->GetViaticoResponsableAsignaciones() as $responsableAsignaciones){
									if($responsableAsignaciones instanceof EntidadViaticoResponsableAsignacion){
										$query = "
											INSERT INTO safi_viatico_responsable_asignacion
												(
													viatico_id,
													responsable_id,
													asignacion_viatico_id,
													monto,
													unidad_medida,
													unidades
												)
													VALUES
												(
													'".$viatico->GetId()."',
													".$idResponsable.",
													".$responsableAsignaciones->GetAsignacionViaticoId().",
													".$responsableAsignaciones->GetMonto().",
													".$responsableAsignaciones->GetUnidadMedida().",
													".$responsableAsignaciones->GetUnidades()."
												)
										";
									
										if($GLOBALS['SafiClassDb']->Query($query) === false)
											throw new Exception('Error al actualizar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
									}
								}	
							}
						}
					}
					
					$result = $GLOBALS['SafiClassDb']->CommitTransaction();
					
					return $viatico->GetId();
					
				} else {
					throw new Exception("Error al iniciar la transacción. Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} 
			}catch(Exception $e){
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
				error_log($e, 0);
				return false;
			}
		}
	}
	
	public static function GetViaticoNacionalById($idViaticoNacional){
		
		include_once(SAFI_MODELO_PATH . '/infocentro.php' );
		include_once(SAFI_MODELO_PATH . '/rutaviatico.php' );
		include_once(SAFI_MODELO_PATH . '/responsableviatico.php' );
		include_once(SAFI_MODELO_PATH . '/viaticoresponsableasignacion.php' );
		include_once(SAFI_MODELO_PATH . '/proyecto.php' );
		include_once(SAFI_MODELO_PATH . '/proyectoespecifica.php' );
		include_once(SAFI_MODELO_PATH . '/accioncentralizada.php' );
		include_once(SAFI_MODELO_PATH . '/accioncentralizadaespecifica.php' );
		include_once(SAFI_MODELO_PATH . '/requisicion.php');
		
		try {
			$viaticoNacional = null;
			
			$query = "
				SELECT
					v.id,
					to_char(v.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS fecha_viatico,
					to_char(v.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
					to_char(v.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
					v.objetivos_viaje,
					v.partida_id,
					v.partida_anho,
					v.accion_central_id,
					v.accion_central_anho,
					v.accion_central_especifica_id,
					v.proyecto_id,
					v.proyecto_anho,
					v.proyecto_especifica_id,
					v.usua_login,
					v.depe_id,
					v.observaciones,
					cv.id AS categoria_id,
					cv.nombre AS categoria_nombre,
					cv.descripcion AS categoria_descripcion,
					cv.estatus_actividad AS categoria_estatus_actividad,
					r.id AS red_id,
					r.nombre AS red_nombre,
					r.estatus_actividad AS red_estatus_actividad,
					edo.id AS estado_id,
					edo.nombre AS estado_nombre,
					edo.estatus_actividad AS estado_estatus_actividad
				FROM
					safi_viatico v
					LEFT JOIN safi_categoria_viatico cv ON (cv.id = v.categoria_id)
					LEFT JOIN safi_viatico_categoria_red vcr ON (vcr.viatico_id = v.id AND vcr.categoria_id = v.categoria_id)
					LEFT JOIN safi_red r ON (r.id = vcr.red_id)
					LEFT JOIN safi_edos_venezuela edo ON (edo.id = v.edo_id)
				WHERE
					v.id = '".$GLOBALS['SafiClassDb']->Quote($idViaticoNacional)."'
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false){
				throw new Exception("Error al intentar obtener el viático nacianal dado su id. Detalles: "
					.$GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				
				$viaticoNacional = new EntidadViaticoNacional();
				
				$viaticoNacional->SetId($row['id']);
				$viaticoNacional->SetFechaViatico($row['fecha_viatico']);
				$viaticoNacional->SetFechaInicioViaje($row['fecha_inicio_viaje']);
				$viaticoNacional->SetFechaFinViaje($row['fecha_fin_viaje']);
				$viaticoNacional->SetObjetivosViaje($row['objetivos_viaje']);
				$viaticoNacional->SetPartidaId($row['partida_id']);
				$viaticoNacional->SetPartidaAnho($row['partida_anho']);
				$viaticoNacional->SetAccionCentralizadaId($row['accion_central_id']);
				$viaticoNacional->SetAccionCentralizadaAnho($row['accion_central_anho']);
				$viaticoNacional->SetAccionCentralizadaEspecificaId($row['accion_central_especifica_id']);
				$viaticoNacional->SetProyectoId($row['proyecto_id']);
				$viaticoNacional->SetProyectoAnho($row['proyecto_anho']);
				$viaticoNacional->SetProyectoEspecificaId($row['proyecto_especifica_id']);
				$viaticoNacional->SetUsuaLogin($row['usua_login']);
				$viaticoNacional->SetDependenciaId($row['depe_id']);
				$viaticoNacional->SetObservaciones($row['observaciones']);
				
				if($row['estado_id'] != null){
					$estado = new EntidadEstado();
					$estado->SetId($row['estado_id']);
					$estado->SetNombre($row['estado_nombre']);
					$estado->SetEstatusActividad($row['estado_estatus_actividad']);
					
					$viaticoNacional->SetEstado($estado);
				}
				
				if($row['categoria_id'] != null){
					$categoria = new EntidadCategoriaViatico();
					$categoria->SetId($row['categoria_id']);
					$categoria->SetNombre($row['categoria_nombre']);
					$categoria->SetDescripcion($row['categoria_descripcion']);
					$categoria->SetEstatusActividad($row['categoria_estatus_actividad']);
					
					$viaticoNacional->SetCategoriaViatico($categoria);
				}
				
				if($row['red_id'] != null){
					$red = new EntidadRed();
					$red->SetId($row['red_id']);
					$red->SetNombre($row['red_nombre']);
					$red->SetEstatusActividad($row['red_estatus_actividad']);
					
					$viaticoNacional->SetRed($red);
				}
				
				if($viaticoNacional->GetProyectoId() != null && $viaticoNacional->GetProyectoAnho() != null && 
					$viaticoNacional->GetProyectoEspecificaId() != null)
				{
					$proyecto = SafiModeloProyecto::GetProyectoById($viaticoNacional->GetProyectoId(),
						$viaticoNacional->GetProyectoAnho());
					$viaticoNacional->SetProyecto($proyecto);
					
					$proyectoEspecifica = SafiModeloProyectoEspecifica::GetProyectoEspecificaById(
						$viaticoNacional->GetProyectoEspecificaId(), $viaticoNacional->GetProyectoId(),
							$viaticoNacional->GetProyectoAnho()  
					);
					$viaticoNacional->SetProyectoEspecifica($proyectoEspecifica);
					
				} else if($viaticoNacional->GetAccionCentralizadaId() != null && $viaticoNacional->GetAccionCentralizadaAnho() != null &&
					$viaticoNacional->GetAccionCentralizadaEspecificaId() != null)
				{
					$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById(
						$viaticoNacional->GetAccionCentralizadaId(),$viaticoNacional->GetAccionCentralizadaAnho());
					$viaticoNacional->SetAccionCentralizada($accionCentralizada);
					
					$centralizadaEspecifica = SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById(
						$viaticoNacional->GetAccionCentralizadaEspecificaId(), $viaticoNacional->GetAccionCentralizadaId(),
						$viaticoNacional->GetAccionCentralizadaAnho()
					);
					$viaticoNacional->SetAccionCentralizadaEspecifica($centralizadaEspecifica);
				}
				
				$infocentros = SafiModeloInfocentro::GetInfocentrosByIdViaticoNacional($viaticoNacional->GetId());
				$viaticoNacional->SetInfocentros($infocentros);
				
				$rutas = SafiModeloRutaViatico::GetRutasByIdViaticoNacional($viaticoNacional->GetId());
				$viaticoNacional->SetRutas($rutas);
				
				$responsable = SafiModeloResponsableViatico::GetResponsableByIdViaticoNacional($viaticoNacional->GetId());
				$viaticoNacional->SetResponsable($responsable);
				
				$viaticoResponsableAsignaciones = SafiModeloViaticoResponsableAsignacion::
					GetAsignacionesByIdViatico($viaticoNacional->GetId());
				$viaticoNacional->SetViaticoResponsableAsignaciones($viaticoResponsableAsignaciones);
				
				$requisiciones = SafiModeloRequisicion::GetRequisicionesByIdViaticoNacional($viaticoNacional->GetId());
				$viaticoNacional->SetRequisiciones($requisiciones);
			}
			
			return $viaticoNacional;
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetListViaticosNacionales($params = array())
	{
		include_once(SAFI_MODELO_PATH . '/responsableviatico.php' );
		
		$viaticos = null;
		$where = "";
		
		try {
			if(isset($params['idsViaticos']) && !is_array($params['idsViaticos'])){
				throw new Exception("El parámetro idsViaticos debe ser un arreglo");
			} else {
				$where = " viatico.id IN ('".implode("' ,'", $params['idsViaticos'])."')";
			}
			
			if($where == ""){
				throw new Exception("No se ha encontrado ningún criterio de búsqueda al intentar obtener la lista de viáticos.");
			}
				
			$query = "
				SELECT
					viatico.id AS viatico_id,
					to_char(viatico.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS viatico_fecha_viatico,
					to_char(viatico.fecha_inicio_viaje, 'DD/MM/YYYY') AS viatico_fecha_inicio_viaje,
					to_char(viatico.fecha_fin_viaje, 'DD/MM/YYYY') AS viatico_fecha_fin_viaje,
					viatico.objetivos_viaje AS viatico_objetivos_viaje,
					viatico.partida_id AS viatico_partida_id,
					viatico.partida_anho AS viatico_partida_anho,
					viatico.accion_central_id AS viatico_accion_central_id,
					viatico.accion_central_anho AS viatico_accion_central_anho,
					viatico.accion_central_especifica_id AS viatico_accion_central_especifica_id,
					acce_esp.centro_gestor as accion_central_centro_gestor,
					acce_esp.centro_costo as accion_central_centro_costo,
					proy_a_esp.centro_gestor as proyecto_centro_gestor,
					proy_a_esp.centro_costo as proyecto_centro_costo,
					viatico.proyecto_id AS viatico_proyecto_id,
					viatico.proyecto_anho AS viatico_proyecto_anho,
					viatico.proyecto_especifica_id AS viatico_proyecto_especifica_id,
					viatico.usua_login AS viatico_usua_login,
					viatico.depe_id AS viatico_dependencia_id,
					viatico.observaciones AS viatico_observaciones,
					(SELECT depe_nombre FROM sai_dependenci depen WHERE depen.depe_id = viatico.depe_id) as depe_nombre,
					responsable.id AS responsable_viatico_id,
					empleado.empl_cedula AS empleado_cedula,
					empleado.empl_nombres as empleado_nombres,
					empleado.empl_apellidos as empleado_apellidos,
					empleado.nacionalidad as empleado_nacionalidad,
					empleado.depe_cosige as empleado_dependencia,
					beneficiario_viatico.benvi_cedula AS beneficiario_viatico_cedula,
					beneficiario_viatico.benvi_nombres as beneficiario_nombres,
					beneficiario_viatico.benvi_apellidos as beneficiario_apellidos,
					beneficiario_viatico.nacionalidad as beneficiario_nacionalidad,
					beneficiario_viatico.depe_id as beneficiario_dependencia,
					beneficiario_viatico.tipo as beneficiario_tipo_empleado,
					responsable.numero_cuenta_bancaria AS responsable_numero_cuenta_bancaria,
					responsable.tipo_cuenta_bancaria AS responsable_tipo_cuenta_bancaria,
					responsable.banco_cuenta_bancaria AS responsable_banco_cuenta_bancaria
				FROM
	  				safi_viatico viatico
					LEFT JOIN sai_acce_esp acce_esp ON
						acce_esp.acce_id = viatico.accion_central_id AND
						acce_esp.pres_anno = viatico.accion_central_anho AND
						acce_esp.aces_id = viatico.accion_central_especifica_id
					LEFT JOIN sai_proy_a_esp proy_a_esp ON
						proy_a_esp.proy_id = viatico.proyecto_id AND
						proy_a_esp.pres_anno = viatico.proyecto_anho AND
						proy_a_esp.paes_id = viatico.proyecto_especifica_id
					LEFT JOIN safi_responsable_viatico responsable ON (responsable.viatico_id = viatico.id)
					LEFT JOIN sai_empleado empleado ON (empleado.empl_cedula = responsable.empleado_cedula)
					LEFT JOIN sai_viat_benef beneficiario_viatico ON
						(beneficiario_viatico.benvi_cedula = responsable.beneficiario_viatico_cedula)
				WHERE
					" . $where . "
				ORDER BY
					viatico.fecha_viatico
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener la lista de viáticos nacionales. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$idsViaticosNacionales = array();
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$idsViaticosNacionales[] = $row['viatico_id']; 
				$viaticos[$row['viatico_id']] = self::LlenarViaticoNacional($row);
			}
			
			if(count($idsViaticosNacionales) > 0)
			{
				$asignacionesByIdsViaticos = SafiModeloViaticoResponsableAsignacion::GetAsignacionesByIdsViaticos($idsViaticosNacionales);
				
				if(is_array($asignacionesByIdsViaticos) && count($asignacionesByIdsViaticos) > 0){
					foreach ($viaticos as $viatico){
						$viatico->SetViaticoResponsableAsignaciones($asignacionesByIdsViaticos[$viatico->GetId()]);
					}
				}
			}
			
		} catch (Exception $e) {
			error_log($e, 0);
		}
		
		return $viaticos;
	}
	
	public static function BuscarViaticoNacional($params = array())
	{
		include_once(SAFI_ENTIDADES_PATH . '/responsableviatico.php' );
		
		try
		{
			$viaticosNacionales = array();
			
			if(is_array($params) && count($params) > 0){
				
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
						viatico.fecha_viatico BETWEEN
							to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
							to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					";
				} else if ($fechaInicio != ''){
					$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($params['fechaInicio'])
						."', 'DD/MM/YYYY') <= viatico.fecha_viatico";
				} else if ($fechaFin != '') {
					$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($params['fechaFin'])
						." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= viatico.fecha_viatico";
				}
				
				if(isset($params['idViaticoNacional']) && trim($params['idViaticoNacional']) != ''){
					if($where != ''){
						$where .= " AND ";
					}
					$where = "lower(viatico.id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($params['idViaticoNacional'])) . "'";
					
				}
				
				if($where == ''){
					return $viaticosNacionales;
				}
				
				if(isset($params['idDependencia']) && $params['idDependencia'] != null && $params['idDependencia'] != ''){
					$where .= " AND viatico.depe_id = '" . $params['idDependencia'] . "'";
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
						viatico.id,
						to_char(viatico.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS fecha_viatico,
						to_char(viatico.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
						to_char(viatico.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
						viatico.objetivos_viaje,
						viatico.partida_id,
						viatico.partida_anho,
						viatico.accion_central_id,
						viatico.accion_central_anho,
						viatico.accion_central_especifica_id,
						acce_esp.centro_gestor as accion_central_centro_gestor,
						acce_esp.centro_costo as accion_central_centro_costo,
						proy_a_esp.centro_gestor as proyecto_centro_gestor,
						proy_a_esp.centro_costo as proyecto_centro_costo,
						viatico.proyecto_id,
						viatico.proyecto_anho,
						viatico.proyecto_especifica_id,
						viatico.usua_login,
						viatico.depe_id,
						viatico.observaciones,
						(SELECT depe_nombre FROM sai_dependenci depen WHERE depen.depe_id = viatico.depe_id) as depe_nombre,
						rv.id AS responsable_viatico_id,
						e.empl_cedula AS empleado_cedula,
						e.empl_nombres as empleado_nombres,
						e.empl_apellidos as empleado_apellidos,
						e.nacionalidad as empleado_nacionalidad,
						e.depe_cosige as empleado_dependencia,
						vb.benvi_cedula AS beneficiario_viatico_cedula,
						vb.benvi_nombres as beneficiario_nombres,
						vb.benvi_apellidos as beneficiario_apellidos,
						vb.nacionalidad as beneficiario_nacionalidad,
						vb.depe_id as beneficiario_dependencia,
						vb.tipo as beneficiario_tipo_empleado,
						rv.numero_cuenta_bancaria,
						rv.tipo_cuenta_bancaria,
						rv.banco_cuenta_bancaria
					FROM
						sai_doc_genera dg
		  				INNER JOIN safi_viatico viatico ON (viatico.id = dg.docg_id)
						LEFT JOIN sai_acce_esp acce_esp ON
							acce_esp.acce_id = viatico.accion_central_id AND
							acce_esp.pres_anno = viatico.accion_central_anho AND
							acce_esp.aces_id = viatico.accion_central_especifica_id
						LEFT JOIN sai_proy_a_esp proy_a_esp ON
							proy_a_esp.proy_id = viatico.proyecto_id AND
							proy_a_esp.pres_anno = viatico.proyecto_anho AND
							proy_a_esp.paes_id = viatico.proyecto_especifica_id
						LEFT JOIN safi_responsable_viatico rv ON rv.viatico_id = viatico.id
						LEFT JOIN sai_empleado e ON e.empl_cedula = rv.empleado_cedula
						LEFT JOIN sai_viat_benef vb ON vb.benvi_cedula = rv.beneficiario_viatico_cedula
					" . ($where != '' ? " WHERE " . $where : '') . "
					ORDER BY
						dg.docg_fecha
				";
				
				$result = $GLOBALS['SafiClassDb']->Query($query);
			
				if($result === false){
					throw new Exception($GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
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
					
					$viaticosNacionales[$row['docg_id']]['ClassDocGenera'] = $docGenera;
					
					$viaticoNacional = new EntidadViaticoNacional();
					
					$viaticoNacional->SetId($row['id']);
					$viaticoNacional->SetFechaViatico($row['fecha_viatico']);
					$viaticoNacional->SetFechaInicioViaje($row['fecha_inicio_viaje']);
					$viaticoNacional->SetFechaFinViaje($row['fecha_fin_viaje']);
					$viaticoNacional->SetObjetivosViaje($row['objetivos_viaje']);
					$viaticoNacional->SetPartidaId($row['partida_id']);
					$viaticoNacional->SetPartidaAnho($row['partida_anho']);
					$viaticoNacional->SetAccionCentralizadaId($row['accion_central_id']);
					$viaticoNacional->SetAccionCentralizadaAnho($row['accion_central_anho']);
					$viaticoNacional->SetAccionCentralizadaEspecificaId($row['accion_central_especifica_id']);
					$viaticoNacional->SetProyectoId($row['proyecto_id']);
					$viaticoNacional->SetProyectoAnho($row['proyecto_anho']);
					$viaticoNacional->SetProyectoEspecificaId($row['proyecto_especifica_id']);
					$viaticoNacional->SetUsuaLogin($row['usua_login']);
					$viaticoNacional->SetDependenciaId($row['depe_id']);
					$viaticoNacional->SetObservaciones($row['observaciones']);
					
					if($row['accion_central_id'] != null && $row['accion_central_anho'] != null && 
						$row['accion_central_especifica_id'] != null)
					{
						$viaticoNacional->SetCentroGestor($row['accion_central_centro_gestor']);
						$viaticoNacional->SetCentroCosto($row['accion_central_centro_costo']);
					} else {
						$viaticoNacional->SetCentroGestor($row['proyecto_centro_gestor']);
						$viaticoNacional->SetCentroCosto($row['proyecto_centro_costo']);
					}
					
					$dependencia = new EntidadDependencia();
					$dependencia->SetId($row['depe_id']);
					$dependencia->SetNombre($row['depe_nombre']);
					$viaticoNacional->SetDependencia($dependencia);
					
					// Establecer los datos del responsable
					$responsable = new EntidadResponsableViatico();
					$responsable->SetId($row['responsable_viatico_id']);
					$responsable->SetIdViatico($viaticoNacional->GetId());
					if($row['empleado_cedula'] != null){
						$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
						$responsable->SetCedula($row['empleado_cedula']);
						$responsable->SetNombres($row['empleado_nombres']);
						$responsable->SetApellidos($row['empleado_apellidos']);
						$responsable->SetNacionalidad($row['empleado_nacionalidad']);
						$responsable->SetIdDependencia($row['empleado_dependencia']);
						$responsable->SetTipoEmpleado(EntidadResponsableViatico::TIPO_EMPLEADO);
					} else if($row['beneficiario_viatico_cedula']) {
						$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
						$responsable->SetCedula($row['beneficiario_viatico_cedula']);
						$responsable->SetNombres($row['beneficiario_nombres']);
						$responsable->SetApellidos($row['beneficiario_apellidos']);
						$responsable->SetNacionalidad($row['beneficiario_nacionalidad']);
						$responsable->SetIdDependencia($row['beneficiario_dependencia']);
						$responsable->SetTipoEmpleado($row['beneficiario_tipo_empleado']);
						
					}
					$responsable->SetNumeroCuenta($row['numero_cuenta_bancaria']);
					$responsable->SetTipoCuenta($row['tipo_cuenta_bancaria']);
					$responsable->SetBanco($row['banco_cuenta_bancaria']);
					$viaticoNacional->SetResponsable($responsable);
					
					//$viaticosNacionales[] = $viaticoNacional;
					$viaticosNacionales[$row['docg_id']]['ClassVaiticoNacional'] = $viaticoNacional;
				}
			}
			
			return $viaticosNacionales;
		}
		catch(Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public function GetReporte1ViaticoNacional($params)
	{
		include_once(SAFI_ENTIDADES_PATH . '/responsableviatico.php' );
		include_once(SAFI_ENTIDADES_PATH . '/compromiso.php' );
		include_once(SAFI_ENTIDADES_PATH . '/fuenteFinanciamiento.php' );
		include_once(SAFI_ENTIDADES_PATH . '/estatus.php' );
		
		include_once(SAFI_MODELO_PATH . '/rutaviatico.php' );
		
		$viaticosNacionales = array();
		$idsViaticosNacionales = array();
		
		if(is_array($params) && count($params) > 0){
			
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
					viatico.fecha_viatico BETWEEN
						to_date('".$fechaInicio."', 'DD/MM/YYYY') AND 
						to_timestamp('".$fechaFin." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaInicio != ''){
				$where = "to_date('".$params['fechaInicio']."', 'DD/MM/YYYY') <= viatico.fecha_viatico";
			} else if ($fechaFin != '') {
				$where = "to_timestamp('".$params['fechaFin']." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= viatico.fecha_viatico";
			}
			
			if(isset($params['idViaticoNacional']) && trim($params['idViaticoNacional']) != ''){
				if($where != ''){
					$where .= " AND ";
				}
				$where = "viatico.id = '" . $params['idViaticoNacional'] . "'";
				
			}
			
			if($where == ''){
				return $viaticosNacionales;
			}
			
			if(isset($params['idDependencia']) && $params['idDependencia'] != null && $params['idDependencia'] != ''){
				$where .= " AND viatico.depe_id = '" . $params['idDependencia'] . "'";
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
					viatico.id,
					to_char(viatico.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS fecha_viatico,
					to_char(viatico.fecha_inicio_viaje, 'DD/MM/YYYY') AS fecha_inicio_viaje,
					to_char(viatico.fecha_fin_viaje, 'DD/MM/YYYY') AS fecha_fin_viaje,
					viatico.objetivos_viaje,
					viatico.partida_id,
					viatico.partida_anho,
					viatico.accion_central_id,
					viatico.accion_central_anho,
					viatico.accion_central_especifica_id,
					acce_esp.centro_gestor as accion_central_centro_gestor,
					acce_esp.centro_costo as accion_central_centro_costo,
					proy_a_esp.centro_gestor as proyecto_centro_gestor,
					proy_a_esp.centro_costo as proyecto_centro_costo,
					viatico.proyecto_id,
					viatico.proyecto_anho,
					viatico.proyecto_especifica_id,
					CASE
						WHEN
							viatico.proyecto_id IS NOT NULL AND
							viatico.proyecto_anho IS NOT NULL  AND
							viatico.proyecto_especifica_id IS NOT NULL
							THEN
								(
									SELECT
										f1125.fuente_financiamiento
									FROM
										sai_forma_1125 f1125
									WHERE
										f1125.form_id_p_ac = viatico.proyecto_id AND
										f1125.pres_anno = viatico.proyecto_anho AND
										f1125.form_id_aesp = viatico.proyecto_especifica_id
									LIMIT
										1
								)
						WHEN
							viatico.accion_central_id IS NOT NULL AND
							viatico.accion_central_anho IS NOT NULL AND
							viatico.accion_central_especifica_id IS NOT NULL
							THEN
								(
									SELECT
										f1125.fuente_financiamiento
									FROM
										sai_forma_1125 f1125
									WHERE
										f1125.form_id_p_ac = viatico.accion_central_id AND
										f1125.pres_anno = viatico.accion_central_anho AND
										f1125.form_id_aesp = viatico.accion_central_especifica_id
									LIMIT
										1
								)
						ELSE
							NULL
					END AS fuente_financiamiento,
					viatico.usua_login,
					viatico.depe_id,
					viatico.observaciones,
					cv.id AS categoria_id,
					cv.nombre AS categoria_nombre,
					cv.descripcion AS categoria_descripcion,
					cv.estatus_actividad AS categoria_estatus_actividad,
					(SELECT depe_nombre FROM sai_dependenci depen WHERE depen.depe_id = viatico.depe_id) as depe_nombre,
					rv.id AS responsable_viatico_id,
					e.empl_cedula AS empleado_cedula,
					e.empl_nombres as empleado_nombres,
					e.empl_apellidos as empleado_apellidos,
					e.nacionalidad as empleado_nacionalidad,
					e.depe_cosige as empleado_dependencia,
					vb.benvi_cedula AS beneficiario_viatico_cedula,
					vb.benvi_nombres as beneficiario_nombres,
					vb.benvi_apellidos as beneficiario_apellidos,
					vb.nacionalidad as beneficiario_nacionalidad,
					vb.depe_id as beneficiario_dependencia,
					vb.tipo as beneficiario_tipo_empleado,
					rv.numero_cuenta_bancaria,
					rv.tipo_cuenta_bancaria,
					rv.banco_cuenta_bancaria,
					asignaciones.monto_total,
					comp.comp_id,
					estaus.esta_nombre
				FROM
					sai_doc_genera dg
	  				INNER JOIN safi_viatico viatico ON (viatico.id = dg.docg_id)
					LEFT JOIN sai_acce_esp acce_esp ON
						acce_esp.acce_id = viatico.accion_central_id AND
						acce_esp.pres_anno = viatico.accion_central_anho AND
						acce_esp.aces_id = viatico.accion_central_especifica_id
					LEFT JOIN sai_proy_a_esp proy_a_esp ON
						proy_a_esp.proy_id = viatico.proyecto_id AND
						proy_a_esp.pres_anno = viatico.proyecto_anho AND
						proy_a_esp.paes_id = viatico.proyecto_especifica_id
					LEFT JOIN safi_responsable_viatico rv ON rv.viatico_id = viatico.id
					LEFT JOIN sai_empleado e ON e.empl_cedula = rv.empleado_cedula
					LEFT JOIN sai_viat_benef vb ON vb.benvi_cedula = rv.beneficiario_viatico_cedula
					LEFT JOIN
						(
							SELECT
								vra.viatico_id,
								SUM(vra.monto*vra.unidades) AS monto_total
							FROM
								safi_viatico_responsable_asignacion vra
							GROUP BY
							vra.viatico_id
						) asignaciones ON (asignaciones.viatico_id = viatico.id)
					LEFT JOIN 
						(
							SELECT
								comp_id,
								comp_documento
							FROM
								sai_comp
							WHERE
								comp_tipo_doc = 'vnac'
						) comp ON comp.comp_documento = viatico.id
					LEFT JOIN safi_categoria_viatico cv ON (cv.id = viatico.categoria_id)
					INNER JOIN sai_estado estaus ON (estaus.esta_id = dg.esta_id)
				" . ($where != '' ? " WHERE " . $where : '') . "
				ORDER BY
					dg.docg_fecha
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
		
			if($result === false){
				echo $GLOBALS['SafiClassDb']->GetErrorMsg();
			}
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$idsViaticosNacionales[] = $row['id'];
				
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
				
				$viaticosNacionales[$row['docg_id']]['ClassDocGenera'] = $docGenera;
				
				$viaticoNacional = new EntidadViaticoNacional();
				
				$viaticoNacional->SetId($row['id']);
				$viaticoNacional->SetFechaViatico($row['fecha_viatico']);
				$viaticoNacional->SetFechaInicioViaje($row['fecha_inicio_viaje']);
				$viaticoNacional->SetFechaFinViaje($row['fecha_fin_viaje']);
				$viaticoNacional->SetObjetivosViaje($row['objetivos_viaje']);
				$viaticoNacional->SetPartidaId($row['partida_id']);
				$viaticoNacional->SetPartidaAnho($row['partida_anho']);
				$viaticoNacional->SetAccionCentralizadaId($row['accion_central_id']);
				$viaticoNacional->SetAccionCentralizadaAnho($row['accion_central_anho']);
				$viaticoNacional->SetAccionCentralizadaEspecificaId($row['accion_central_especifica_id']);
				$viaticoNacional->SetProyectoId($row['proyecto_id']);
				$viaticoNacional->SetProyectoAnho($row['proyecto_anho']);
				$viaticoNacional->SetProyectoEspecificaId($row['proyecto_especifica_id']);
				$viaticoNacional->SetUsuaLogin($row['usua_login']);
				$viaticoNacional->SetDependenciaId($row['depe_id']);
				$viaticoNacional->SetObservaciones($row['observaciones']);
				
				if($row['accion_central_id'] != null && $row['accion_central_anho'] != null && 
					$row['accion_central_especifica_id'] != null)
				{
					$viaticoNacional->SetCentroGestor($row['accion_central_centro_gestor']);
					$viaticoNacional->SetCentroCosto($row['accion_central_centro_costo']);
				} else {
					$viaticoNacional->SetCentroGestor($row['proyecto_centro_gestor']);
					$viaticoNacional->SetCentroCosto($row['proyecto_centro_costo']);
				}
				
				$dependencia = new EntidadDependencia();
				$dependencia->SetId($row['depe_id']);
				$dependencia->SetNombre($row['depe_nombre']);
				$viaticoNacional->SetDependencia($dependencia);
				
				// Establecer los datos del responsable
				$responsable = new EntidadResponsableViatico();
				$responsable->SetId($row['responsable_viatico_id']);
				$responsable->SetIdViatico($viaticoNacional->GetId());
				if($row['empleado_cedula'] != null){
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
					$responsable->SetCedula($row['empleado_cedula']);
					$responsable->SetNombres($row['empleado_nombres']);
					$responsable->SetApellidos($row['empleado_apellidos']);
					$responsable->SetNacionalidad($row['empleado_nacionalidad']);
					$responsable->SetIdDependencia($row['empleado_dependencia']);
					$responsable->SetTipoEmpleado(EntidadResponsableViatico::TIPO_EMPLEADO);
				} else if($row['beneficiario_viatico_cedula']) {
					$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
					$responsable->SetCedula($row['beneficiario_viatico_cedula']);
					$responsable->SetNombres($row['beneficiario_nombres']);
					$responsable->SetApellidos($row['beneficiario_apellidos']);
					$responsable->SetNacionalidad($row['beneficiario_nacionalidad']);
					$responsable->SetIdDependencia($row['beneficiario_dependencia']);
					$responsable->SetTipoEmpleado($row['beneficiario_tipo_empleado']);
					
				}
				$responsable->SetNumeroCuenta($row['numero_cuenta_bancaria']);
				$responsable->SetTipoCuenta($row['tipo_cuenta_bancaria']);
				$responsable->SetBanco($row['banco_cuenta_bancaria']);
				$viaticoNacional->SetResponsable($responsable);
				$viaticoNacional->SetMontoTotal($row['monto_total']);
				
				if($row['categoria_id'] != null){
					$categoria = new EntidadCategoriaViatico();
					$categoria->SetId($row['categoria_id']);
					$categoria->SetNombre($row['categoria_nombre']);
					$categoria->SetDescripcion($row['categoria_descripcion']);
					$categoria->SetEstatusActividad($row['categoria_estatus_actividad']);
					
					$viaticoNacional->SetCategoriaViatico($categoria);
				}
				
				//$viaticosNacionales[] = $viaticoNacional;
				$viaticosNacionales[$row['docg_id']]['ClassVaiticoNacional'] = $viaticoNacional;
				
				/********************************************
				 ***** Cargar los datos del compromiso ******
				 ********************************************/
				$compromiso = new EntidadCompromiso();
				$compromiso->SetId($row['comp_id']);
				
				$viaticosNacionales[$row['docg_id']]['ClassCompromiso'] = $compromiso;
				
				/************************************************************
				 ***** Cargar los datos de la fuente de financiamiento ******
				 ************************************************************/
				$fuenteFinanciamiento = new EntidadFuenteFinanciamiento();
				$fuenteFinanciamiento->SetId($row['fuente_financiamiento']);
				
				$viaticosNacionales[$row['docg_id']]['ClassFuenteFinanciamiento'] = $fuenteFinanciamiento;
				
				/****************************************
				 ***** Cargar los datos del estaus ******
				 ****************************************/
				$estatus = new EntidadEstatus();
				$estatus->SetId($row['esta_id']);
				$estatus->SetNombre($row['esta_nombre']);
				
				$viaticosNacionales[$row['docg_id']]['ClassEstatus'] = $estatus;
				
				
			}//while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			
			if(count($idsViaticosNacionales)>0){
				$rutasByViaticos = SafiModeloRutaViatico::GetRutasByIdsViaticoNacionales($idsViaticosNacionales);
				
				foreach($viaticosNacionales as $idViatico => $dataViaticos){
					if(isset($rutasByViaticos[$idViatico])){
						$dataViaticos['ClassVaiticoNacional']->SetRutas($rutasByViaticos[$idViatico]);
					}
				}
			}
		}//if(is_array($params) && count($params) > 0)
		
		return $viaticosNacionales;
	}
	
	public static function GetViaticoNacionalEnbandeja($params)
	{
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : null;
		$estatus = is_array($params['estatus']) ? $params['estatus'] : null;
		$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;  
		
		$idTipoDocumento = "vnac";
		
		$where = " dg.docg_id LIKE '".$idTipoDocumento."%'";
		
		if($idPerfilActual == null && $idPerfilActual == '')
			return array();
			
		$where .= " AND dg.perf_id_act = '".$idPerfilActual."'";
		
		if($estatus == null || count($estatus)==0)
			return array();
		
		$where .= " AND ( dg.esta_id = " . implode('OR dg.esta_id = ', $estatus) . ")";
		
		if($idDependencia != null){
			$where .= " AND v.depe_id = '" . $idDependencia . "'";
		}
			
		return self::__GetViaticoNacionalBadejasByWhere($where);
		
	}
	
	public static function GetViaticoNacionalPorEnviar($params)
	{
		$usuaLogin = isset($params['usuaLogin']) ? $params['usuaLogin'] : '';
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : '';
		
		$idTipoDocumento = "vnac";
		$estadoPorEnviar = 59;
		
		if($usuaLogin != '' && $idPerfilActual != ''){
			$where = "
	  			dg.docg_id LIKE '".$idTipoDocumento."%' AND
	  			dg.usua_login = '".$usuaLogin."' AND
	  			dg.esta_id = ".$estadoPorEnviar." AND
	  			dg.perf_id_act = '".$idPerfilActual."'
			";
			
			return self::__GetViaticoNacionalBadejasByWhere($where);
		}
		
		return array();
	}
	
	public static function GetViaticoNacionalEnTransito($params = array())
	{
		try
		{
			$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;
			
			$idTipoDocumento = "vnac";
			$estadoEnTransito = 10;
			
			$where = "
	  			dg.docg_id LIKE '".$idTipoDocumento."%'
	  			AND dg.esta_id = ".$estadoEnTransito."
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
					AND v.depe_id NOT IN ('".implode("' ,'", $noEnDependencia)."')
				";
			}
			
			if($idDependencia != null){
				$where .= " AND v.depe_id = '" . $idDependencia . "'";
			}
			
			return self::__GetViaticoNacionalBadejasByWhere($where);
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	private static function __GetViaticoNacionalBadejasByWhere($where)
	{
		try
		{
			include_once(SAFI_ENTIDADES_PATH . '/docgenera.php' );
			include_once(SAFI_ENTIDADES_PATH . '/responsableviatico.php' );

			$preMsg = "Error al intentar obtener una bandeja de viáticos nacionales.";
			$DataViaticosNacionales = array();
	
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
		  				v.id AS v_id,
						to_char(v.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS v_fecha_viatico,
						to_char(v.fecha_inicio_viaje, 'DD/MM/YYYY') AS v_fecha_inicio_viaje,
						to_char(v.fecha_fin_viaje, 'DD/MM/YYYY') AS v_fecha_fin_viaje,
						v.objetivos_viaje AS v_objetivos_viaje,
						v.partida_id AS v_partida_id,
						v.partida_anho AS v_partida_anho,
						v.accion_central_id AS v_accion_central_id,
						v.accion_central_anho AS v_accion_central_anho,
						v.accion_central_especifica_id AS v_accion_central_especifica_id,
						ace.centro_gestor AS ace_accion_central_centro_gestor,
						ace.centro_costo AS ace_accion_central_centro_costo,
						pe.centro_gestor AS pe_proyecto_centro_gestor,
						pe.centro_costo AS pe_proyecto_centro_costo,
						v.proyecto_id AS v_proyecto_id,
						v.proyecto_anho AS v_proyecto_anho,
						v.proyecto_especifica_id AS v_proyecto_especifica_id,
						v.usua_login AS v_usua_login,
						v.depe_id AS v_depe_id,
						v.observaciones AS v_observaciones,
						(SELECT depe_nombre FROM sai_dependenci d WHERE d.depe_id = v.depe_id) as d_depe_nombre,
						rv.id AS rv_responsable_viatico_id,
						e.empl_cedula AS e_empleado_cedula,
						e.empl_nombres AS e_empleado_nombres,
						e.empl_apellidos AS e_empleado_apellidos,
						e.nacionalidad AS e_empleado_nacionalidad,
						e.depe_cosige AS e_empleado_dependencia,
						vb.benvi_cedula AS vb_beneficiario_viatico_cedula,
						vb.benvi_nombres AS vb_beneficiario_nombres,
						vb.benvi_apellidos AS vb_beneficiario_apellidos,
						vb.nacionalidad AS vb_beneficiario_nacionalidad,
						vb.depe_id AS vb_beneficiario_dependencia,
						vb.tipo AS vb_beneficiario_tipo_empleado,
						rv.numero_cuenta_bancaria AS rv_numero_cuenta_bancaria,
						rv.tipo_cuenta_bancaria AS rv_tipo_cuenta_bancaria,
						rv.banco_cuenta_bancaria AS rv_banco_cuenta_bancaria
		  			FROM
		  				sai_doc_genera dg
		  				INNER JOIN safi_viatico v ON (v.id = dg.docg_id)
		  				LEFT JOIN sai_acce_esp ace ON
							ace.acce_id = v.accion_central_id AND
							ace.pres_anno = v.accion_central_anho AND
							ace.aces_id = v.accion_central_especifica_id
						LEFT JOIN sai_proy_a_esp pe ON
							pe.proy_id = v.proyecto_id AND
							pe.pres_anno = v.proyecto_anho AND
							pe.paes_id = v.proyecto_especifica_id
						LEFT JOIN safi_responsable_viatico rv ON rv.viatico_id = v.id
						LEFT JOIN sai_empleado e ON e.empl_cedula = rv.empleado_cedula
						LEFT JOIN sai_viat_benef vb ON vb.benvi_cedula = rv.beneficiario_viatico_cedula
		  			WHERE
		  				".$where."
		  			ORDER BY
		  				dg.docg_fecha
				";
				
				if(($result=$GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg." Detalles: ".utf8_decode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
				$idsViaticosNacionales = array();
				while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
				{
					$idsViaticosNacionales[] = $row['v_id'];
					
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
					
					$DataViaticosNacionales[$row['docg_id']]['ClassDocGenera'] = $docGenera;
					
					$viaticoNacional = new EntidadViaticoNacional();
					
					$viaticoNacional->SetId($row['v_id']);
					$viaticoNacional->SetFechaViatico($row['v_fecha_viatico']);
					$viaticoNacional->SetFechaInicioViaje($row['v_fecha_inicio_viaje']);
					$viaticoNacional->SetFechaFinViaje($row['v_fecha_fin_viaje']);
					$viaticoNacional->SetObjetivosViaje($row['v_objetivos_viaje']);
					$viaticoNacional->SetPartidaId($row['v_partida_id']);
					$viaticoNacional->SetPartidaAnho($row['v_partida_anho']);
					$viaticoNacional->SetAccionCentralizadaId($row['v_accion_central_id']);
					$viaticoNacional->SetAccionCentralizadaAnho($row['v_accion_central_anho']);
					$viaticoNacional->SetAccionCentralizadaEspecificaId($row['v_accion_central_especifica_id']);
					$viaticoNacional->SetProyectoId($row['v_proyecto_id']);
					$viaticoNacional->SetProyectoAnho($row['v_proyecto_anho']);
					$viaticoNacional->SetProyectoEspecificaId($row['v_proyecto_especifica_id']);
					$viaticoNacional->SetUsuaLogin($row['v_usua_login']);
					$viaticoNacional->SetDependenciaId($row['v_depe_id']);
					$viaticoNacional->SetObservaciones($row['v_observaciones']);
					
					if($row['v_accion_central_id'] != null && $row['v_accion_central_anho'] != null && 
						$row['v_accion_central_especifica_id'] != null)
					{
						$viaticoNacional->SetCentroGestor($row['ace_accion_central_centro_gestor']);
						$viaticoNacional->SetCentroCosto($row['ace_accion_central_centro_costo']);
					} else {
						$viaticoNacional->SetCentroGestor($row['pe_proyecto_centro_gestor']);
						$viaticoNacional->SetCentroCosto($row['pe_proyecto_centro_costo']);
					}
					
					$dependencia = new EntidadDependencia();
					$dependencia->SetId($row['v_depe_id']);
					$dependencia->SetNombre($row['d_depe_nombre']);
					$viaticoNacional->SetDependencia($dependencia);
					
					// Establecer los datos del responsable
					$responsable = new EntidadResponsableViatico();
					$responsable->SetId($row['rv_responsable_viatico_id']);
					$responsable->SetIdViatico($viaticoNacional->GetId());
					if($row['e_empleado_cedula'] != null){
						$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
						$responsable->SetCedula($row['e_empleado_cedula']);
						$responsable->SetNombres($row['e_empleado_nombres']);
						$responsable->SetApellidos($row['e_empleado_apellidos']);
						$responsable->SetNacionalidad($row['e_empleado_nacionalidad']);
						$responsable->SetIdDependencia($row['e_empleado_dependencia']);
						$responsable->SetTipoEmpleado(EntidadResponsableViatico::TIPO_EMPLEADO);
					} else if($row['vb_beneficiario_viatico_cedula']) {
						$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
						$responsable->SetCedula($row['vb_beneficiario_viatico_cedula']);
						$responsable->SetNombres($row['vb_beneficiario_nombres']);
						$responsable->SetApellidos($row['vb_beneficiario_apellidos']);
						$responsable->SetNacionalidad($row['vb_beneficiario_nacionalidad']);
						$responsable->SetIdDependencia($row['vb_beneficiario_dependencia']);
						$responsable->SetTipoEmpleado($row['vb_beneficiario_tipo_empleado']);
						
					}
					$responsable->SetNumeroCuenta($row['rv_numero_cuenta_bancaria']);
					$responsable->SetTipoCuenta($row['rv_tipo_cuenta_bancaria']);
					$responsable->SetBanco($row['rv_banco_cuenta_bancaria']);
					$viaticoNacional->SetResponsable($responsable);
					
					$DataViaticosNacionales[$row['docg_id']]['ClassVaiticoNacional'] = $viaticoNacional;
				}
			
				if(count($idsViaticosNacionales) > 0)
				{
					$asignacionesByIdsViaticos = SafiModeloViaticoResponsableAsignacion::
						GetAsignacionesByIdsViaticos($idsViaticosNacionales);
					
					if(is_array($asignacionesByIdsViaticos) && count($asignacionesByIdsViaticos) > 0){
						foreach ($DataViaticosNacionales as $dataViaticoNacional)
						{
							$viatico = $dataViaticoNacional['ClassVaiticoNacional'];
							$viatico->SetViaticoResponsableAsignaciones($asignacionesByIdsViaticos[$viatico->GetId()]);
						}
					}
				}
			}
			
			return $DataViaticosNacionales;
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function DevolverViaticoNacional(EntidadDocGenera $docGenera, EntidadMemo $memo, EntidadRevisionesDoc $revisiones)
	{		
		try
		{
			$preMsg = "Error al intentar devolver el viático nacional.";
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Guardar el registro del documento en docGenera (estado de la cadena)
			if(SafiModeloDocGenera::ActualizarDocGenera($docGenera) === false)
				throw new Exception('Error al actualizar docgenera. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Guardar el memo
			if(($idMemo = SafiModeloMemo::GuardarMemo($memo, $docGenera->GetId())) === false)
				throw new Exception($preMsg." El memo no pudo ser guardado.");
			
			$query = "
				SELECT
					*
				FROM
					sai_insert_revision_doc(
						'".$revisiones->GetIdDocumento()."',
						'".$revisiones->GetLoginUsuario()."',
						'".$revisiones->GetIdPerfil()."',
						'".$revisiones->GetIdWFOpcion()."',
						".($firma_doc != null && trim($firma_doc) != '' ? "'".trim($firma_doc)."'" : "NULL")."
					) AS resultado
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception('Error al insertar la revision. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
	
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$insertoRevision = $row["resultado"];
				if($insertoRevision != 1)
					throw new Exception('Error al insertar la revision. Detalles: Resultado obtenido invalido');
			} else
				throw new Exception('Error al insertar la revision. Detalles: Imposible encontrar el resultado');
		
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			return $idMemo;
			
		}catch(Exception $e){
			if($resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
		
		return false;
	}
	
	public static function EnviarViaticoNacional(EntidadDocGenera $docGenera, EntidadRevisionesDoc $revisiones = null, $requisicion = null)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Guardar el registro del documento en docGenera (estado de la cadena)
			if(SafiModeloDocGenera::ActualizarDocGenera($docGenera) === false)
				$GLOBALS['SafiErrors']['general'][] = 'Error. No se pudo actualizar docGenera. ';
				
			if($revisiones != null)
			{
				$query = "
					SELECT
						*
					FROM
						sai_insert_revision_doc(
							'".$revisiones->GetIdDocumento()."',
							'".$revisiones->GetLoginUsuario()."',
							'".$revisiones->GetIdPerfil()."',
							'".$revisiones->GetIdWFOpcion()."',
							".($firma_doc != null && trim($firma_doc) != '' ? "'".trim($firma_doc)."'" : "NULL")."
						) AS resultado
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception('Error al insertar la revision. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
		
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$insertoRevision = $row["resultado"];
					if($insertoRevision != 1)
						throw new Exception('Error al insertar la revision. Detalles: Resultado obtenido invalido');
				} else
					throw new Exception('Error al insertar la revision. Detalles: Imposible encontrar el resultado');
					
				if($requisicion != null){
					include_once(SAFI_MODELO_PATH . '/requisicion.php');
					include_once(SAFI_MODELO_PATH . '/wfcadena.php');
					
					
					/***********************************
					 ***** Ingreso de la requisición****
					 ***********************************/
					
					if(($idRequisicion = SafiModeloRequisicion::GuardarRequisicion($requisicion)) === false){
						throw new Exception('Error al guardar la requisicion. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					} else {
						$documento = new EntidadDocumento();
						$documento->SetId('rqui');  // Establecer el id del documento de requisicion = rqui
						
						$wFObjetoInicial = new EntidadWFObjeto();
						// $wFObjetoInicial->SetId(1);	// Establecer el id del objeto inicial que siempre es 1
						$wFObjetoInicial->SetId(4);	// Establecer el id del objeto inicial a 4 = revisar (directo a compras)
						
						$dependencia = new EntidadDependencia();
						if($requisicion['idDependenciaCreador']=="350" || $requisicion['idDependenciaCreador']=="150"){
							$dependencia->SetId($requisicion['idDependenciaCreador']);
						} else {
							$dependencia->SetId(null);
						}
						
						$wFOpcion = new EntidadWFOpcion();
						$wFOpcion->SetId(6); // Establecer la opción a 6 = Darle visto bueno
						
						$wFGrupo = new EntidadWFGrupo();
						$wFGrupo->SetId(44); // Establecer el grupo a 44 = Analista de presupuesto, grupo antes de compras
						
						// Establecer los parámetros para buscar la cadena inicial de viaticos nacionales
						$cadenaFind = new EntidadWFCadena();
						$cadenaFind->SetWFObjetoInicial($wFObjetoInicial);
						$cadenaFind->SetDocumento($documento);
						$cadenaFind->SetDependencia($dependencia);
						$cadenaFind->SetWFOpcion($wFOpcion);
						$cadenaFind->SetWFGrupo($wFGrupo);
						
						// Obtener la cadena inicial de viaticos nacionales
						$cadena = SafiModeloWFCadena::GetWFCadena($cadenaFind);
						
						if($cadena == null)
							throw new Exception('Error al guardar. Detalles: WFCadena inicial no encontrada');
						
						if($cadena->GetWFCadenaHijo() == null)
							throw new Exception('Error al guardar. Detalles: WFCadena hija no encontrada');
							
						// Obtener la cadena siguiente, a la inicial, de vaiticos nacionales
						$cadenaHijo = SafiModeloWFCadena::GetWFCadena($cadena->GetWFCadenaHijo());
						
						if($cadenaHijo == null)
							throw new Exception('Error al guardar. Detalles: WFCadena hija no encontrada');
						
						if($cadenaHijo->GetWFGrupo() == null)
							throw new Exception('Error al guardar. Detalles: WFGrupo de WFCadena hija no encontrado');
						
						include_once(SAFI_MODELO_PATH . '/dependenciacargo.php');
						
						// Obtener el siguiente cargo al que será enviado el documento
						$perfilActual = SafiModeloDependenciaCargo::
							GetSiguienteCargoDeCadena($requisicion['idDependenciaCreador'], $cadenaHijo->GetWFGrupo()->GetPerfiles());
						
						if($perfilActual == null)	throw new Exception(
							'Error al guardar. Detalles: No se puede encontrar el perfil de la siguiente instancia en la cadena de la'
							. ' requisición');
						
						include_once(SAFI_MODELO_PATH . '/docgenera.php');
						
						$docGeneraRequisicion = new EntidadDocGenera();
									
						$docGeneraRequisicion->SetId($idRequisicion);
						$docGeneraRequisicion->SetIdWFObjeto(1); // 1 = Iniciar
						$docGeneraRequisicion->SetIdWFCadena($cadena->GetId());
						$docGeneraRequisicion->SetUsuaLogin($requisicion['loginUsuarioCreador']);
						$docGeneraRequisicion->SetIdPerfil($docGenera->GetIdPerfil());
						$docGeneraRequisicion->SetFecha($requisicion['fechaRequisicion']);
						$docGeneraRequisicion->SetIdEstatus(10); // 10 = estado "en transito"
						$docGeneraRequisicion->SetPrioridad(1);
						$docGeneraRequisicion->SetIdPerfilActual($perfilActual->GetId());
						
						// Guardar el registro del documento en docGenera (estado de la cadena)
						if(SafiModeloDocGenera::GuardarDocGenera($docGeneraRequisicion) === false)
							throw new Exception('Error al guardar. Detalles: No se pudo guardar la requisición en docGenera');
					}
					/***************************************
					 ***** FIN Ingreso de la requisición****
					 ***************************************/
				}
			}
			
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				if($idRequisicion !== null && $idRequisicion !== false) return array("idRequisicion" => $idRequisicion);
				else return true;
				
		}catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function AnularViaticoNacional(EntidadDocGenera $docGenera, EntidadRevisionesDoc $revisiones)
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Guardar el registro del documento en docGenera (estado de la cadena)
			if(SafiModeloDocGenera::ActualizarDocGenera($docGenera) === false)
				$GLOBALS['SafiErrors']['general'][] = 'Error. No se pudo actualizar docGenera. ';
				
			if($revisiones != null)
			{
				$query = "
					SELECT
						*
					FROM
						sai_insert_revision_doc(
							'".$revisiones->GetIdDocumento()."',
							'".$revisiones->GetLoginUsuario()."',
							'".$revisiones->GetIdPerfil()."',
							'".$revisiones->GetIdWFOpcion()."',
							".($firma_doc != null && trim($firma_doc) != '' ? "'".trim($firma_doc)."'" : "NULL")."
						) AS resultado
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception('Error al insertar la revision. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
		
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$insertoRevision = $row["resultado"];
					if($insertoRevision != 1)
						throw new Exception('Error al insertar la revision. Detalles: Resultado obtenido invalido');
				} else
					throw new Exception('Error al insertar la revision. Detalles: Imposible encontrar el resultado');
			}
				
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			return true;
				
		}catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}

	public static function BuscarIdsViaticosNacionales($codigoDocumento, $idDependencia, $numLimit) {
	    $idsViaticos = null;
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
	            throw new Exception("Error al buscar los ids de viaticos nacionales. Detalles: El código del documento o la dependencia es nulo o vacío");
	
	        $query = "
	            SELECT
	               v.id
	            FROM
	                safi_viatico v, sai_doc_genera d
	            WHERE
	               v.id = d.docg_id AND
	               v.id LIKE '%".$codigoDocumento."%' AND
	               v.depe_id = '".$idDependencia."' AND
	               d.esta_id <> 15 AND 
	               v.id NOT IN (
	               				SELECT nro_documento 
	               				FROM registro_documento
	               				WHERE tipo_documento = 'vnac' 
	               					AND id_estado=1
	               					AND user_depe='".$_SESSION['user_depe_id']."'
	               				) 	AND 
	               v.fecha_viatico LIKE '".$_SESSION['an_o_presupuesto']."%'  
	            ORDER BY v.id 
				LIMIT
				".$numLimit."
	        ";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de viáticos nacionales. Detalles: ".
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

	public static function BuscarInfoViaticoNacional($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/viaticoresponsableasignacion.php' );
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar la información del viático nacional");
	
	        $query = "
			SELECT  TO_CHAR(vi.fecha_viatico, 'dd-mm-yyyy') AS fecha, 
					r.viatico_id AS id,
					e.empl_cedula AS id_beneficiario,
					e.empl_nombres || ' ' ||e.empl_apellidos AS beneficiario,
					vi.objetivos_viaje AS objetivos
			FROM safi_responsable_viatico r, sai_empleado e, safi_viatico vi
			WHERE r.empleado_cedula = e.empl_cedula 
				AND r.viatico_id=vi.id
				AND r.viatico_id='".$codigoDocumento."' 
			UNION
			SELECT TO_CHAR(vi.fecha_viatico, 'dd-mm-yyyy') AS fecha,
				r.viatico_id AS id,
				v.benvi_cedula AS id_beneficiario,
				v.benvi_nombres || ' ' || v.benvi_apellidos AS beneficiario,
				vi.objetivos_viaje AS objetivos
			FROM safi_responsable_viatico r, sai_viat_benef v, safi_viatico vi 
			WHERE r.empleado_cedula = v.benvi_cedula
	        	AND r.viatico_id = vi.id and r.viatico_id='".$codigoDocumento."'";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de ordenes de compra. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $idsViaticos = array();
	
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

	public static function ReporteResponsables(array $params = null)
	{
		try
		{
			$datosViaticos = null;
			$preMsg = "Error al intentar realizar una búsqueda del reporte de responsables de viáticos nacionales.";
			
			$where = "";
			$fechaInicio = null;
			$fechaFin = null;
			$fechaRendicionInicio = null;
			$fechaRendicionFin = null;
			$datosViaticos = array();
			$idsViaticos = array();
			$idsResponsablesViaticos = array();
			$idsEstados = array();
			$responsablesViaticos = null;
			$asignacionesByIdsViaticos = null;
			$estados = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			// Validar la fecha de inicio del viático
			if(isset($params['fechaInicio']))
			{
				if(($__fechaInicio=trim($params['fechaInicio'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaInicio'] está vacío.");
					
				$fechaInicio = $__fechaInicio;
			}
			
			// Validar la fecha de fin del viático
			if(isset($params['fechaFin']))
			{
				if(($__fechaFin=trim($params['fechaFin'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaFin'] está vacío.");
					
				$fechaFin = $__fechaFin; 
			}
			
			if($fechaInicio != null && $fechaFin != null){
				$where = "
					viatico.fecha_viatico BETWEEN
						to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
						to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaInicio != null){
				$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') <= viatico.fecha_viatico";
			} else if ($fechaFin != null) {
				$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)
					." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= viatico.fecha_viatico";
			}
			
			// Validar la fecha de inicio de la rendición de viático
			if(isset($params['fechaRendicionInicio']))
			{
				if(($__fechaRendicionInicio=trim($params['fechaRendicionInicio'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaRendicionInicio'] está vacío.");
					
				$fechaRendicionInicio = $__fechaRendicionInicio;
			}
			
			// Validar la fecha de fin de la rendición de viático
			if(isset($params['fechaRendicionFin']))
			{
				if(($__fechaRendicionFin=trim($params['fechaRendicionFin'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaRendicionFin'] está vacío.");
					
				$fechaRendicionFin = $__fechaRendicionFin; 
			}
			
			if($fechaRendicionInicio != null && $fechaRendicionFin != null){
				$where = "
					rendicion.fecha_rendicion BETWEEN
						to_date('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionInicio)."', 'DD/MM/YYYY') AND 
						to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaRendicionInicio != null){
				$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionInicio)."', 'DD/MM/YYYY') <= rendicion.fecha_rendicion";
			} else if ($fechaRendicionFin != null) {
				$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionFin)
					." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= rendicion.fecha_rendicion";
			}
			
			// Validar el estatus de la rendicion del viático nacional
			if(isset($params['estatusRendicion']))
			{
				if(($estatusRendicion = trim($params['estatusRendicion'])) == '')
					throw new Exception($preMsg. " El parámetro params['estatusRendicion'] está vacío.");
				if($where != ""){
					$where .= " AND ";
				}
				switch ($estatusRendicion){
					case "1": // Rendidos
						$where .= "
							rendicion.id IS NOT NULL
						";   
						break;
					case "2": // No rendidos
						$where .= "
							rendicion.id IS NULL
						";
						break;
				}
			}
			
			// Validar el estado del viático
			if(isset($params['idEstado']))
			{
				if(($idEstado = trim($params['idEstado'])) == '')
					throw new Exception($preMsg. " El parámetro params['idEstado'] está vacío.");
					
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "viatico.edo_id = '" . $GLOBALS['SafiClassDb']->Quote($idEstado) . "'";
			}
			
			// Validar la región del viático
			if(isset($params['idRegionReporte']))
			{
				if(($idRegionReporte = trim($params['idRegionReporte'])) == '')
					throw new Exception($preMsg. " El parámetro params['idRegionReporte'] está vacío.");
					
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "
					viatico.edo_id IN (
						SELECT
							region_reporte_estado.edo_id
						FROM
							safi_region_reporte_estado region_reporte_estado
						WHERE
							region_reporte_estado.region_id = '".$GLOBALS['SafiClassDb']->Quote($idRegionReporte)."'
					)
				";
			}
			
			/*
			 * if($form->GetTipoProyectoAccionCentralizada() != null) $params['tipoProyectoAccionCentralizada'] = $form->GetTipoProyectoAccionCentralizada();
				if($form->GetIdProyectoAccionCentralizada() != null) $params['idProyectoAccionCentralizada'] = $form->GetIdProyectoAccionCentralizada();
				if($form->GetIdAccionEspecifica() != null) $params['idAccionEspecifica'] = $form->GetIdAccionEspecifica();
			 * */
			
			// validar la categoría programática
			if(isset($params['tipoProyectoAccionCentralizada']) && isset($params['idProyectoAccionCentralizada']) && isset($params['idAccionEspecifica']))
			{
				if($where != ""){
					$where .= " AND ";
				}
				
				if(strcmp($params['tipoProyectoAccionCentralizada'], 'proyecto') == "0"){
					$where .= "
						proyecto_especifica.proy_id = '".$params['idProyectoAccionCentralizada']."'
						AND proyecto_especifica.paes_id = '".$params['idAccionEspecifica']."'
					";	
				} else if(strcmp($params['tipoProyectoAccionCentralizada'], 'accionCentralizada') == "0"){
					$where .= "
						centralizada_especifica.acce_id = '".$params['idProyectoAccionCentralizada']."'
						AND centralizada_especifica.aces_id = '".$params['idAccionEspecifica']."'
					";
				}
			}
			
			if($where == "")
				throw new Exception($preMsg." No existen criterios de búsqueda.");
				
			if(
				isset($params['dependencia']) && ($dependencia = $params['dependencia']) != null && $dependencia->GetId() != null
				&& ($idDependencia=trim($dependencia->GetId())) != ''
			){
					$where .= " AND viatico.depe_id = '" . $idDependencia . "'";
			}
			
			$query = "
				SELECT
					".self::GetSelectFildsViaticos().",
					responsable_viatico.id AS responsable_viatico_id,
					centralizada_especifica.aces_nombre as centralizada_especifica_nombre,
					centralizada_especifica.centro_gestor as centralizada_especifica_centro_gestor,
					centralizada_especifica.centro_costo as centralizada_especifica_centro_costo,
					proyecto_especifica.paes_nombre as proyecto_nombre,
					proyecto_especifica.centro_gestor as proyecto_centro_gestor,
					proyecto_especifica.centro_costo as proyecto_centro_costo,
					".SafiModeloRendicionViaticoNacional::GetSelectFildsRendicion()."
				FROM
					safi_viatico viatico
					INNER JOIN safi_responsable_viatico responsable_viatico ON (responsable_viatico.viatico_id = viatico.id)
					LEFT JOIN sai_acce_esp centralizada_especifica ON
						centralizada_especifica.acce_id = viatico.accion_central_id AND
						centralizada_especifica.pres_anno = viatico.accion_central_anho AND
						centralizada_especifica.aces_id = viatico.accion_central_especifica_id
					LEFT JOIN sai_proy_a_esp proyecto_especifica ON
						proyecto_especifica.proy_id = viatico.proyecto_id AND
						proyecto_especifica.pres_anno = viatico.proyecto_anho AND
						proyecto_especifica.paes_id = viatico.proyecto_especifica_id
					LEFT JOIN safi_rendicion_viatico_nacional as rendicion ON (rendicion.viatico_nacional_id = viatico.id)
				" . ($where != '' ? " WHERE " . $where : '') . "
				ORDER BY
					viatico.fecha_viatico
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$datosViatico = array();
				$proyectoEspcifica = null;
				$centralizadaEspecifica = null;
				
				$viatico = self::LlenarViaticoNacional($row);
				
				$rendicion = $row["rendicion_id"] != null
					? SafiModeloRendicionViaticoNacional::LlenarRendicion2($row) : null;
				
				$idsViaticos[$row['viatico_id']] = $row['viatico_id'];
				$idsResponsablesViaticos[$row['responsable_viatico_id']] = $row['responsable_viatico_id'];
				$idsEstados[$row['viatico_estado_id']] = $row['viatico_estado_id'];
				
				if($viatico->GetProyectoId() != null && $viatico->GetProyectoAnho() != null && 
					$viatico->GetProyectoEspecificaId() != null)
				{
					$proyectoEspcifica = new EntidadProyectoEspecifica();
					
					$proyectoEspcifica->SetId($row['viatico_proyecto_especifica_id']);
					$proyectoEspcifica->SetIdProyecto($row['viatico_proyecto_id']);
					$proyectoEspcifica->SetAnho($row['viatico_proyecto_anho']);
					$proyectoEspcifica->SetNombre($row['proyecto_nombre']);
					$proyectoEspcifica->SetCentroGestor($row['proyecto_centro_gestor']);
					$proyectoEspcifica->SetCentroCosto($row['proyecto_centro_costo']);
					$viatico->SetProyectoEspecifica($proyectoEspcifica);
					
				} else if($viatico->GetAccionCentralizadaId() != null && $viatico->GetAccionCentralizadaAnho() != null &&
					$viatico->GetAccionCentralizadaEspecificaId() != null)
				{
					$centralizadaEspecifica = new EntidadAccionCentralizadaEspecifica();

					$centralizadaEspecifica->SetId($row['viatico_accion_central_especifica_id']);
					$centralizadaEspecifica->SetIdAccionCentralizada($row['viatico_accion_central_id']);
					$centralizadaEspecifica->SetAnho($row['viatico_accion_central_anho']);
					$centralizadaEspecifica->SetNombre($row['centralizada_especifica_nombre']);
					$centralizadaEspecifica->SetCentroGestor($row['centralizada_especifica_centro_gestor']);
					$centralizadaEspecifica->SetCentroCosto($row['centralizada_especifica_centro_costo']);
					$viatico->SetAccionCentralizadaEspecifica($centralizadaEspecifica);
					
				}
				
				$datosViatico['ClassViaticoNacional'] = $viatico;
				$datosViatico['ClassRendicionViatico'] = $rendicion;
				
				$datosViaticos[$row['responsable_viatico_id']] = $datosViatico;
			}
			
			// Obtener los responsables de los viáticos
			if(is_array($idsResponsablesViaticos) && count($idsResponsablesViaticos) > 0)
				$responsablesViaticos = SafiModeloResponsableViatico::GetResponsableViaticos(array('idsResponsables' => $idsResponsablesViaticos));

			// Obtener las asignaciones para los viáticos
			if(is_array($idsViaticos) && count($idsViaticos) > 0)
				$asignacionesByIdsViaticos = SafiModeloViaticoResponsableAsignacion::GetAsignacionesByIdsViaticos($idsViaticos);
			
			// Obtener los estados de los viáticos
			if(is_array($idsEstados) && count($idsEstados) > 0)
				$estados = SafiModeloEstado::GetEstados(array("idsEstados" => $idsEstados));
			
			// Obtener el doc_genera de los avances
			$documentosViatico = null;
			if(is_array($idsViaticos) && count($idsViaticos) > 0){
				$documentosViatico = SafiModeloDocGenera::GetDocGeneraByIdsDocuments($idsViaticos);
				$documentosViatico = $documentosViatico != null && is_array($documentosViatico) ? $documentosViatico['docGenera'] : null;
			}
			
			foreach ($datosViaticos as $idResponsableViatico => &$datosViatico)
			{
				$viatico = $datosViatico['ClassViaticoNacional'];
				
				// Establecer el responsable del viático
				if(is_array($responsablesViaticos) && isset($responsablesViaticos[$idResponsableViatico]))
					$viatico->SetResponsable($responsablesViaticos[$idResponsableViatico]);
				
				// Establecer las asignaciones del viático
				if(is_array($asignacionesByIdsViaticos) && isset($asignacionesByIdsViaticos[$viatico->GetId()]))
					$viatico->SetViaticoResponsableAsignaciones($asignacionesByIdsViaticos[$viatico->GetId()]);
				
				// Establecer los estado del viático
				if(is_array($estados) && ($estado = $viatico->GetEstado()) != null && isset($estados[$estado->GetId()]))
					$viatico->SetEstado(clone $estados[$estado->GetId()]);
				
				// Establecer el doc genera del viático
				$datosViatico['documentoViatico'] =
					is_array($documentosViatico) && isset($documentosViatico[$viatico->GetId()]) ? $documentosViatico[$viatico->GetId()] : null;
				
			}
			unset($datosViatico);
			unset($viatico);
			
			return $datosViaticos;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;	
		}
	}
	
	private static function GetSelectFildsViaticos()
	{
		return  "
			viatico.id AS viatico_id,
			to_char(viatico.fecha_viatico, 'DD/MM/YYYY HH24:MI:SS') AS viatico_fecha_viatico,
			to_char(viatico.fecha_inicio_viaje, 'DD/MM/YYYY') AS viatico_fecha_inicio_viaje,
			to_char(viatico.fecha_fin_viaje, 'DD/MM/YYYY') AS viatico_fecha_fin_viaje,
			viatico.objetivos_viaje AS viatico_objetivos_viaje,
			viatico.partida_id AS viatico_partida_id,
			viatico.partida_anho AS viatico_partida_anho,
			viatico.accion_central_id AS viatico_accion_central_id,
			viatico.accion_central_anho AS viatico_accion_central_anho,
			viatico.accion_central_especifica_id AS viatico_accion_central_especifica_id,
			viatico.proyecto_id AS viatico_proyecto_id,
			viatico.proyecto_anho AS viatico_proyecto_anho,
			viatico.proyecto_especifica_id AS viatico_proyecto_especifica_id,
			viatico.edo_id AS viatico_estado_id,
			viatico.depe_id AS viatico_dependencia_id
		";
	}
	
	private static function LlenarViaticoNacional($row)
	{
		$viaticoNacional = new EntidadViaticoNacional();
				
		$viaticoNacional->SetId($row['viatico_id']);
		$viaticoNacional->SetFechaViatico($row['viatico_fecha_viatico']);
		$viaticoNacional->SetFechaInicioViaje($row['viatico_fecha_inicio_viaje']);
		$viaticoNacional->SetFechaFinViaje($row['viatico_fecha_fin_viaje']);
		$viaticoNacional->SetObjetivosViaje($row['viatico_objetivos_viaje']);
		$viaticoNacional->SetPartidaId($row['viatico_partida_id']);
		$viaticoNacional->SetPartidaAnho($row['viatico_partida_anho']);
		$viaticoNacional->SetAccionCentralizadaId($row['viatico_accion_central_id']);
		$viaticoNacional->SetAccionCentralizadaAnho($row['viatico_accion_central_anho']);
		$viaticoNacional->SetAccionCentralizadaEspecificaId($row['viatico_accion_central_especifica_id']);
		$viaticoNacional->SetProyectoId($row['viatico_proyecto_id']);
		$viaticoNacional->SetProyectoAnho($row['viatico_proyecto_anho']);
		$viaticoNacional->SetProyectoEspecificaId($row['viatico_proyecto_especifica_id']);
		$viaticoNacional->SetUsuaLogin($row['viatico_usua_login']);
		$viaticoNacional->SetDependenciaId($row['viatico_dependencia_id']);
		$viaticoNacional->SetObservaciones($row['viatico_observaciones']);
		
		// Establecer los datos del responsable
		if(isset($row['responsable_viatico_id'])){
			$responsable = new EntidadResponsableViatico();
			$responsable->SetId($row['responsable_viatico_id']);
			$responsable->SetIdViatico($viaticoNacional->GetId());
			if($row['empleado_cedula'] != null){
				$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_EMPLEADO);
				$responsable->SetCedula($row['empleado_cedula']);
				$responsable->SetNombres($row['empleado_nombres']);
				$responsable->SetApellidos($row['empleado_apellidos']);
				$responsable->SetNacionalidad($row['empleado_nacionalidad']);
				$responsable->SetIdDependencia($row['empleado_dependencia']);
				$responsable->SetTipoEmpleado(EntidadResponsableViatico::TIPO_EMPLEADO);
			} else if($row['beneficiario_viatico_cedula']) {
				$responsable->SetTipoResponsable(EntidadResponsableViatico::TIPO_BENEFICIARIO);
				$responsable->SetCedula($row['beneficiario_viatico_cedula']);
				$responsable->SetNombres($row['beneficiario_nombres']);
				$responsable->SetApellidos($row['beneficiario_apellidos']);
				$responsable->SetNacionalidad($row['beneficiario_nacionalidad']);
				$responsable->SetIdDependencia($row['beneficiario_dependencia']);
				$responsable->SetTipoEmpleado($row['beneficiario_tipo_empleado']);
			}
			$responsable->SetNumeroCuenta($row['responsable_numero_cuenta_bancaria']);
			$responsable->SetTipoCuenta($row['responsable_tipo_cuenta_bancaria']);
			$responsable->SetBanco($row['responsable_banco_cuenta_bancaria']);
			$viaticoNacional->SetResponsable($responsable);
			
			if($row['viatico_dependencia_id'] != null){
				$dependencia = new EntidadDependencia();
				$dependencia->SetId($row['viatico_dependencia_id']);
				$viaticoNacional->SetDependencia($dependencia);
			}
			
			if($row['viatico_estado_id'] != null){
				$estado = new EntidadEstado();
				$estado->SetId($row['viatico_estado_id']);
				$viaticoNacional->SetEstado($estado);
			}
		}
		
		return $viaticoNacional;
	}
}