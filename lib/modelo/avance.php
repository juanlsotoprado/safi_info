<?php
include_once(SAFI_ENTIDADES_PATH . '/avance.php');

include_once(SAFI_MODELO_PATH . '/proyecto.php' );
include_once(SAFI_MODELO_PATH . '/infocentro.php' );
include_once(SAFI_MODELO_PATH . '/proyectoespecifica.php' );
include_once(SAFI_MODELO_PATH . '/accioncentralizada.php' );
include_once(SAFI_MODELO_PATH . '/accioncentralizadaespecifica.php' );
include_once(SAFI_MODELO_PATH . '/rutaAvance.php' );
include_once(SAFI_MODELO_PATH . '/responsableAvancePartidas.php' );
include_once(SAFI_MODELO_PATH . '/wfcadena.php');
include_once(SAFI_MODELO_PATH . '/dependenciacargo.php');
include_once(SAFI_MODELO_PATH . '/docgenera.php');
include_once(SAFI_MODELO_PATH . '/rendicionAvance.php');

class SafiModeloAvance
{
	public static function GuardarAvance(EntidadAvance $avance, array $params = null)
	{
		try
		{
			if($avance == null)
				throw new Exception('Error al guardar el avance. Detalles: El parametro avance es nulo');
				
			if($avance->GetDependencia() == null || !(($dependencia=$avance->GetDependencia()) instanceof EntidadDependencia))
				throw new Exception('Error al guardar el avance. Detalles: '. 
					'El parametro avance dependencia es nulo o es incorrecto');
				
			if($dependencia->GetId() == null || $dependencia->GetId() == '')
				throw new Exception('Error al guardar el avance. Detalles: '. 
					'El parametro avance dependencia id es nulo');
				
			if ($params == null || !is_array($params))
				throw new Exception('Error al guardar el avance. Detalles: El parámetro params es nulo o es incorrecto.');
				
			if (!isset($params['perfilRegistrador']))
				throw new Exception('Error al guardar el avance. Detalles: El parámetro params[\'perfilRegistrador\'] '.
					'no pudo ser encontrado');
				
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			// Generar el id del documento avance
			$query = "
				SELECT
					sai_generar_codigo(
							'avan',
							'" . $dependencia->GetId() . "',
							'fecha_avance',
							'id'
					)
			";
			
			$result = $GLOBALS['SafiClassDb']->Query($query);
			
			if($result === false) throw new Exception('Error al guardar. No se puede generar el id del avance. Detalles: '
				. $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			if (!($idAvance = $GLOBALS['SafiClassDb']->FetchOne($result))) {
				throw new Exception('Error al guardar. No se puede generar el id del avance. Detalles: '
					. $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			$horaFechaActual = date("d/m/Y H:i:s");
			
			/*************************************************************************
			**** Guardar información referente a la flujo de trabajo de la cadena ****
			**************************************************************************/
			
			$documento = new EntidadDocumento();
			// Establecer el id del documento de avance
			$documento->SetId(GetConfig("preCodigoAvance"));
			
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
			
			// Guardar los datos en la tabla doc_genera
			 
			$docGenera = new EntidadDocGenera();
			
			$docGenera->SetId($idAvance);
			$docGenera->SetIdWFObjeto(1); // 1 = Iniciar
			$docGenera->SetIdWFCadena($cadena->GetId());
			$docGenera->SetUsuaLogin($avance->GetUsuaLogin());
			$docGenera->SetIdPerfil($params['perfilRegistrador']);
			$docGenera->SetFecha($horaFechaActual);
			$docGenera->SetIdEstatus(59); // 59 = estado "por enviar"
			$docGenera->SetPrioridad(1);
			$docGenera->SetIdPerfilActual($perfilActual->GetId());
			
			// Guardar el registro del documento en docGenera (estado de la cadena)
			if(SafiModeloDocGenera::GuardarDocGenera($docGenera) === false)
				throw new Exception('Error al guardar. Detalles: No se pudo guardar docGenera');
			
			
			/******************************************************
			******* Guardar información referente al avance *******
			*******************************************************/
			
			$proyectoAccionCentralizadaFields = array();
			$proyectoAccionCentralizadaValues = array();

			if (
				$avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_PROYECTO
				&& $avance->GetProyecto() != null && $avance->GetProyectoEspecifica() != null
			){
				$proyectoAccionCentralizadaFields[] = 'proyecto_id';
				$proyectoAccionCentralizadaFields[] = 'proyecto_anho';
				$proyectoAccionCentralizadaFields[] = 'proyecto_especifica_id';
				
				$proyectoAccionCentralizadaValues[] = "'".$avance->GetProyecto()->GetId()."'";
				$proyectoAccionCentralizadaValues[] = $avance->GetProyecto()->GetAnho();
				$proyectoAccionCentralizadaValues[] = "'".$avance->GetProyectoEspecifica()->GetId()."'";
				
			} else if (
				$avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA
				&& $avance->GetAccionCentralizada() != null && $avance->GetAccionCentralizadaEspecifica() != null
			){
				$proyectoAccionCentralizadaFields[] = 'accion_central_id';
				$proyectoAccionCentralizadaFields[] = 'accion_central_anho';
				$proyectoAccionCentralizadaFields[] = 'accion_central_especifica_id';
				
				$proyectoAccionCentralizadaValues[] = "'".$avance->GetAccionCentralizada()->GetId()."'";
				$proyectoAccionCentralizadaValues[] = $avance->GetAccionCentralizada()->GetAnho();
				$proyectoAccionCentralizadaValues[] = "'".$avance->GetAccionCentralizadaEspecifica()->GetId()."'";
			}
			
			// Guardar los campos de la tabla safi_avance
			$query = "
				INSERT INTO safi_avance
					(
						id,
						fecha_avance,
						fecha_registro,
						fecha_ultima_modificacion,
						categoria_id,
						".implode(',' , $proyectoAccionCentralizadaFields).",
						fecha_inicio_actividad,
						fecha_fin_actividad,
						objetivos,
						descripcion,
						justificacion,
						nro_participantes,
						observaciones,
						usua_login,
						depe_id,
						punto_cuenta_id
					)
				VALUES
					(
						'".$idAvance."',
						to_date('".$avance->GetFechaAvance()."', 'DD/MM/YYYY'),
						to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
						to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
						".(
							(
								$avance->GetCategoria() != null && $avance->GetCategoria()->GetId() != null 
								&& trim($avance->GetCategoria()->GetId()) != ''
							)
							? "'".trim($avance->GetCategoria()->GetId())."'" : "NULL"
						).",
						".implode(',' , $proyectoAccionCentralizadaValues).",
						to_date('".$GLOBALS['SafiClassDb']->Quote($avance->GetFechaInicioActividad())."', 'DD/MM/YYYY'),
						to_date('".$GLOBALS['SafiClassDb']->Quote($avance->GetFechaFinActividad())."', 'DD/MM/YYYY'),
						'".$GLOBALS['SafiClassDb']->Quote($avance->GetObjetivos())."',
						".(($avance->GetDescripcion() != null && trim($avance->GetDescripcion()) != '') ?
							"'".$GLOBALS['SafiClassDb']->Quote(trim($avance->GetDescripcion()))."'" : "NULL").",
						".(($avance->GetJustificacion() != null && trim($avance->GetJustificacion()) != '') ?
							"'".$GLOBALS['SafiClassDb']->Quote(trim($avance->GetJustificacion()))."'" : "NULL").",
						".(($avance->GetNroParticipantes() != null && trim($avance->GetNroParticipantes()) != '') ?
							"'".$GLOBALS['SafiClassDb']->Quote(trim($avance->GetNroParticipantes()))."'" : "NULL").",
						".(($avance->GetObservaciones() != null && trim($avance->GetObservaciones()) != '') ?
							"'".$GLOBALS['SafiClassDb']->Quote(trim($avance->GetObservaciones()))."'" : "NULL").",
						'".$avance->GetUsuaLogin()."',
						'".$dependencia->GetId()."',
						".(
							(
								$avance->GetPuntoCuenta() != null && $avance->GetPuntoCuenta()->GetId() != null 
								&& trim($avance->GetPuntoCuenta()->GetId()) != ''
							)
							? "'".trim($avance->GetPuntoCuenta()->GetId())."'" : "NULL"
						)."
					)
			";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al guardar el avance. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
						
			// Guardar la informacion relacionada con las redes
			if(
				$avance->GetCategoria() != null && $avance->GetCategoria()->GetId() != null
				&& trim($avance->GetCategoria()->GetId()) != '' && $avance->GetRed() != null && $avance->GetRed()->GetId() != null 
				&& trim($avance->GetRed()->GetId()) != '' && strcmp(trim($avance->GetRed()->GetId()), "0") != 0
			){
				$query = "
						INSERT INTO safi_avance_categoria_red
							(
								avance_id,
								categoria_id,
								red_id
	  						)
	  					VALUES
	  						(
	  							'".$idAvance."',
	  							".trim($avance->GetCategoria()->GetId()).",
	  							".trim($avance->GetRed()->GetId())."
	  						)
					";
				
				$result = $GLOBALS['SafiClassDb']->Query($query);
			
				if($result === false) throw new Exception('Error al guardar el avance. No se pudo guardar la informacion de la '.
					'categoria-red. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			// Guardar la información relacionada con los responsables y las partidas
			if(is_array($avance->GetResponsablesAvancePartidas()))
			{
				$indexResponsable = 0;
				foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
				{
					if($responsableAvancePartidas->GetResponsableAvance() == null)
						throw new Exception('Error al guardar el avance. El parametro responsableAvance del '.
							'responsable['.($indexResponsable+1).'] es nulo.');
					
					$responsableAvancePartidas->GetResponsableAvance()->SetIdAvance($idAvance);
					
					if(SafiModeloResponsableAvancePartidas::GuardarResponsableAvancePartidas($responsableAvancePartidas) === false)
						throw new Exception('Error al guardar el avance. No se pudo guardar la informacion del '.
							'responsable['.($indexResponsable+1).'].');
					
					$indexResponsable++;
				} // Fin de foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
			} // fin de 
			
			// Guardar informacion de las rutas
			if(is_array($avance->GetRutasAvance()))
			{
				$indexRuta = 0;
				foreach ($avance->GetRutasAvance() as $rutaAvance)
				{
					// Establecer el id del avance en la ruta del avance
					$rutaAvance->SetIdAvance($idAvance);
					
					// Guardar la ruta del avance
					if(SafiModeloRutaAvance::GuardarRutaAvance($rutaAvance) === false)
						throw new Exception('Error al guardar el avance. No se pudo guardar la informacion de '.
							'la ruta ['.($indexRuta+1).'].');
					
					$indexRuta++;
				}
			}
			
			// Guardar información de los infocentros
			if(is_array($avance->GetInfocentros()))
			{
				$indexInfocentro = 0;
				foreach ($avance->GetInfocentros() as $infocentro)
				{
					$query = "
						INSERT INTO safi_avance_infocentro
							(
								avance_id,
								infocentro_id
							)
						VALUES
							(
								'".$idAvance."',
								".(
									($infocentro != null && $infocentro->GetId() != null && trim($infocentro->GetId()) != '' )
									? trim($infocentro->GetId()) : "NULL"
								)."
							)
					";
					
					$result = $GLOBALS['SafiClassDb']->Query($query);
				
					if($result === false) throw new Exception('Error al guardar el avance. No se pudo guardar la informacion del '.
						'infocentro['.($indexInfocentro+1).']. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					$indexInfocentro++;
				}
			}
			
			//return false;
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception("Error al realizar el commit en la funcion de guardado del avance. Detalles: ".
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			return $idAvance;
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function ActualizarAvance(EntidadAvance $avance = null)
	{
		try
		{
			if($avance == null)
				throw new Exception("El parámetro avance es nulo");
			
			if($avance->GetId() == null || ($idAvance=trim($avance->GetId())) == '')
				throw new Exception("El parámetro avance->GetId() es nulo o está vacío.");
				
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			if($resultTransaction === false)
				throw new Exception("Error al iniciar la transacción. Detalles: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Obtener el avance, que se desea actualizar, desde la base de datos.
			$avanceSaved = self::GetAvance($avance);
			if($avanceSaved == null)
				throw new Exception("El avance " . $idAvance . " no existe en la base de datos.");
				
			/***************************************************************************
			 ******* Borrar la información que relaciona avance, categoria y red *******
			 ***************************************************************************/
			$query = "
				DELETE FROM
					safi_avance_categoria_red
				WHERE
					avance_id = '" . $idAvance . "'
			";
			if($GLOBALS['SafiClassDb']->Query($query) === false)
				throw new Exception('Error al actualizar. No se puede eliminar la relacion en safi_avance_categoria_red para el avance '.
					$idAvance . '. Detalles: '. $GLOBALS['SafiClassDb']->GetErrorMsg());

			$horaFechaActual = date("d/m/Y H:i:s");
			
			// Establecer el id de la categoría
			$idCategoria = 
				(
					$avance->GetCategoria() != null && ($idCategoria=$avance->GetCategoria()->GetId()) != null
					&& ($idCategoria=trim($idCategoria)) != ''
				) ? $idCategoria : null;
			
			// Establecer la información del proyecto/acción centralizada y de la acción específica
			$proyectoAccionCentralizadaQuery = null;
			
			if(
				$avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_PROYECTO
				&& $avance->GetProyecto() != null 
				&& $avance->GetProyecto()->GetId() != null && ($idProyecto=trim($avance->GetProyecto()->GetId())) != ''
				&& $avance->GetProyecto()->GetAnho() != null && ($anhoProyecto=trim($avance->GetProyecto()->GetAnho())) != ''
				&& $avance->GetProyectoEspecifica() != null
				&& $avance->GetProyectoEspecifica()->GetId() != null
				&& ($idProyectoEspecifica=trim($avance->GetProyectoEspecifica()->GetId())) != ''
			){
				$proyectoAccionCentralizadaQuery = "
					proyecto_id = '".$idProyecto."',
					proyecto_anho = ".$anhoProyecto.",
					proyecto_especifica_id = '".$idProyectoEspecifica."',
					accion_central_id = NULL,
					accion_central_anho = NULL,
					accion_central_especifica_id = NULL
				";
			}
			else if(
				$avance->GetTipoProyectoAccionCentralizada() == EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA
				&& $avance->GetAccionCentralizada() != null
				&& $avance->GetAccionCentralizada()->GetId() != null && ($idAccion=trim($avance->GetAccionCentralizada()->GetId())) != ''
				&& $avance->GetAccionCentralizada()->GetAnho() != null
				&& ($anhoAccion=trim($avance->GetAccionCentralizada()->GetAnho())) != ''
				&& $avance->GetAccionCentralizadaEspecifica() != null
				&& $avance->GetAccionCentralizadaEspecifica()->GetId() != null
				&& ($idAccionEspecifica=trim($avance->GetAccionCentralizadaEspecifica()->GetId())) != ''
			){
				$proyectoAccionCentralizadaQuery = "
					proyecto_id = NULL,
					proyecto_anho = NULL,
					proyecto_especifica_id = NULL,
					accion_central_id = '".$idAccion."',
					accion_central_anho = ".$anhoAccion.",
					accion_central_especifica_id = '".$idAccionEspecifica."'
				";
			}
			else {
				throw new Exception('Falta información del proyecto/accion centralizada. '.
					'Id del proyecto: ' . $idProyecto . '
					, Id Acción específica del proyecto: ' . $idProyectoEspecifica . '
					, Anho Proyecto: ' . $anhoProyecto . '
					, Id de la acción centralizada: ' . $idAccion . '
					, Id de la acción específica  acción centralizada: ' . $idAccionEspecifica . '
					, Anho de la acción centralizada: ' . $anhoAccion . '
				');
			}
			
			if ($proyectoAccionCentralizadaQuery == null)
				throw new Exception("No se pudo establecer la información de Proyecto/Acción centralizada y/o Acción Específica.");
			
			/*************************************************************
			 ******* Actualizar los campos de la tabla safi_avance *******
			 *************************************************************/
			
			$query = "
				UPDATE
					safi_avance
				SET
					fecha_avance = to_date('".$avance->GetFechaAvance()."', 'DD/MM/YYYY'),
					fecha_ultima_modificacion = to_timestamp('".$horaFechaActual."', 'DD/MM/YYYY HH24:MI:SS'),
					categoria_id = ".($idCategoria != null ? $idCategoria : "NULL").",
					" . $proyectoAccionCentralizadaQuery . ",
					fecha_inicio_actividad = to_date('".$avance->GetFechaInicioActividad()."', 'DD/MM/YYYY'),
					fecha_fin_actividad = to_date('".$avance->GetFechaFinActividad()."', 'DD/MM/YYYY'),
					objetivos = ".(
						($avance->GetObjetivos() != null && trim($avance->GetObjetivos()) != '')
						? "'".trim($GLOBALS['SafiClassDb']->Quote($avance->GetObjetivos())."'") : "NULL").",
					descripcion = ".(
						($avance->GetDescripcion() != null && trim($avance->GetDescripcion()) != '')
						? "'".trim($GLOBALS['SafiClassDb']->Quote($avance->GetDescripcion())."'") : "NULL").",
					justificacion = ".(
						($avance->GetJustificacion() != null && trim($avance->GetJustificacion()) != '')
						? "'".trim($GLOBALS['SafiClassDb']->Quote($avance->GetJustificacion())."'") : "NULL").",
					nro_participantes = ".(
						($avance->GetNroParticipantes() != null && trim($avance->GetNroParticipantes()) != '')
						? trim($GLOBALS['SafiClassDb']->Quote($avance->GetNroParticipantes())) : "NULL").",
					observaciones = ".(
						($avance->GetObservaciones() != null && trim($avance->GetObservaciones()) != '')
						? "'".trim($GLOBALS['SafiClassDb']->Quote($avance->GetObservaciones())."'") : "NULL").",
					punto_cuenta_id = ".(
							(
								$avance->GetPuntoCuenta() != null && $avance->GetPuntoCuenta()->GetId() != null 
								&& trim($avance->GetPuntoCuenta()->GetId()) != ''
							)
							? "'".trim($avance->GetPuntoCuenta()->GetId())."'" : "NULL"
					)."
				WHERE
					id = '" . $idAvance . "'
			";
				
			if($GLOBALS['SafiClassDb']->Query($query) === false)
				throw new Exception('Error al guardar el avance. No se pudo guardar la informacion en '.
					'la tabla safi_avance. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg() . " Query: " .$query);
				
			/*******************************************************************************
			 ******* Actualizar la información que relaciona avance, categoria y red *******
			 *******************************************************************************/
			
			if(	$idCategoria != null && $avance->GetRed() != null && ($idRed=$avance->GetRed()->GetId()) != null &&
				 ($idRed=trim($idRed)) != '' && strcmp($idRed, "0") != 0
			 ){
				$query = "
						INSERT INTO safi_avance_categoria_red
							(
								avance_id,
								categoria_id,
								red_id
	  						)
	  					VALUES
	  						(
	  							'".$idAvance."',
	  							".$idCategoria.",
	  							".$idRed."
	  						)
					";
				
				$result = $GLOBALS['SafiClassDb']->Query($query);
			
				if($result === false) throw new Exception('Error al actualizar el avance. La información que relaciona el avance, '.
					'la categoria y la red no pudo ser guardada. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			/***********************************************************************************
			 ****** Actualizar la información relacionada con los infocentros del avance *******
			 ***********************************************************************************/
				
			// borrar los infocentros asociados al avance
			$query = "
				DELETE FROM
					safi_avance_infocentro
				WHERE
					avance_id = '".$idAvance."'
			";
			if($GLOBALS['SafiClassDb']->Query($query) === false)
				throw new Exception('Error al actualizar. No se pudo eliminar la información de los infocentros del avance. '.
					'Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// insertar los infocentros del avance
			if(is_array($avance->GetInfocentros())){
				foreach($avance->GetInfocentros() as $infocentro){
					$query = "
						INSERT INTO safi_avance_infocentro
							(
								avance_id,
	  							infocentro_id
	  						)
	  					VALUES
	  						(
	  							'".$idAvance."',
	  							'".$infocentro->GetId()."'
	  						)
					";
				
					if($GLOBALS['SafiClassDb']->Query($query) === false)
						throw new Exception('Error al actualizar. No se pudo guardar la información de los infocentros del avance. '.
							'Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
			/*************************************************************************************
			 ******* Actualizar la información relacionada con los responsables del avance *******
			 *************************************************************************************/
			
			// El avance guardado no tiene responsables
			if(!is_array($avanceSaved->GetResponsablesAvancePartidas()) || count($avanceSaved->GetResponsablesAvancePartidas()) == 0)
			{
				// El avance actualizado tiene al menos un responsable
				if(is_array($avance->GetResponsablesAvancePartidas()) && count($avance->GetResponsablesAvancePartidas()) > 0 )
				{
					// Insertar los responsables del avance actualizado en la base de datos
					$indexResponsable = 0;
					foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidasUpdated)
					{
						$responsableAvanceUpdated = $responsableAvancePartidasUpdated->GetResponsableAvance();
						
						if($responsableAvanceUpdated == null)
							throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
								'responsable['.($indexResponsable+1).'] es nulo.');
						
						// Establecer el id del avance en el avance actualizado
						$responsableAvanceUpdated->SetIdAvance($idAvance);
						
						if(SafiModeloResponsableAvancePartidas::
							GuardarResponsableAvancePartidas($responsableAvancePartidasUpdated) === false
						)
							throw new Exception('Error al actualizar el avance. No se pudo guardar la información del '.
								'responsable['.($indexResponsable+1).'].');
					}
				}
			}
			// El avance guardado tiene al menos un responsable
			else
			{
				// El avance actualizado no tiene responsables
				if(!is_array($avance->GetResponsablesAvancePartidas()) || count($avance->GetResponsablesAvancePartidas()) == 0 )
				{
					// Eliminar los responsables del avance guardado
					if(
						SafiModeloResponsableAvancePartidas::
							EliminarResponsableAvancePartidasByIdAvance($idAvance) === false
					)
					throw new Exception('Error al eliminar los responsables del avance ' . $idAvance);
				}
				else // El avance actualizado tiene al menos un responsable
				{
					// El número de responsables es igual tanto en el avance guardado como en el avance actualizado
					if(count($avance->GetResponsablesAvancePartidas()) == count($avanceSaved->GetResponsablesAvancePartidas()))
					{
						// Actualizar cada uno de los responsables guardados para el avance en la base de datos
						$indexResponsable = 0;
						$responsablesAvancePartidasSaved = $avanceSaved->GetResponsablesAvancePartidas();
						reset($responsablesAvancePartidasSaved);
						$responsableAvancePartidasSaved = current($responsablesAvancePartidasSaved);
						foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidasUpdated)
						{
							if($responsableAvancePartidasSaved->GetResponsableAvance() == null)
								throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
									'responsable['.($indexResponsable+1).'] guardado es nulo.');
							
							$responsableAvanceUpdated = $responsableAvancePartidasUpdated->GetResponsableAvance();
							
							if($responsableAvanceUpdated == null)
								throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
									'responsable['.($indexResponsable+1).'] es nulo.');
							
							// Establecer el id del responsable en el responsable del avance actualizado
							$responsableAvanceUpdated->SetId($responsableAvancePartidasSaved->GetResponsableAvance()->GetId());	
							// Establecer el id del avance en el avance actualizado
							$responsableAvanceUpdated->SetIdAvance($idAvance);
							
							if(
								SafiModeloResponsableAvancePartidas::
									ActualizarResponsableAvancePartidas($responsableAvancePartidasUpdated) === false
							)
								throw new Exception('Error al actualizar el avance. No se pudo actualizar la informacion del '.
									'responsable['.($indexResponsable+1).'].');
								
							$indexResponsable++;
							$responsableAvancePartidasSaved = next($responsablesAvancePartidasSaved);
						}
					}
					// El número de responsables en el avance actualizado es mayor que en el avance guardado
					else if(count($avance->GetResponsablesAvancePartidas()) > count($avanceSaved->GetResponsablesAvancePartidas()))
					{
						$indexResponsable = 0;
						$responsablesAvancePartidasSaved = $avanceSaved->GetResponsablesAvancePartidas();
						reset($responsablesAvancePartidasSaved);
						$responsableAvancePartidasSaved = current($responsablesAvancePartidasSaved);
						foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidasUpdated)
						{
							$responsableAvanceUpdated = $responsableAvancePartidasUpdated->GetResponsableAvance();
							
							if($responsableAvanceUpdated == null)
								throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
									'responsable['.($indexResponsable+1).'] es nulo.');
							
							// - Insertar los nuevos responsables para el avance guardado
							if($responsableAvancePartidasSaved === false)
							{
								// Establecer el id del avance en el avance actualizado
								$responsableAvanceUpdated->SetIdAvance($idAvance);
								
								if(SafiModeloResponsableAvancePartidas::
									GuardarResponsableAvancePartidas($responsableAvancePartidasUpdated) === false
								)
									throw new Exception('Error al actualizar el avance. No se pudo guardar la información del '.
										'responsable['.($indexResponsable+1).'].');
							}
							// - Actualizar cada una de los responsables guardados para el avance en la base de datos
							else 
							{
								if($responsableAvancePartidasSaved->GetResponsableAvance() == null)
									throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
										'responsable['.($indexResponsable+1).'] guardado es nulo.');
								
								// Establecer el id del responsable en el responsable del avance actualizado
								$responsableAvanceUpdated->SetId($responsableAvancePartidasSaved->GetResponsableAvance()->GetId());	
								// Establecer el id del avance en el avance actualizado
								$responsableAvanceUpdated->SetIdAvance($idAvance);
								
								if(
									SafiModeloResponsableAvancePartidas::
										ActualizarResponsableAvancePartidas($responsableAvancePartidasUpdated) === false
								)
									throw new Exception('Error al actualizar el avance. No se pudo actualizar la informacion del '.
										'responsable['.($indexResponsable+1).'].');
								
								$responsableAvancePartidasSaved = next($responsablesAvancePartidasSaved);
							}
							
							$indexResponsable++;
						}
					}
					// El número de responsables en el avance actualizado es menor que en el avance guardado
					else if(count($avance->GetResponsablesAvancePartidas()) < count($avanceSaved->GetResponsablesAvancePartidas()))
					{
						$indexResponsable = 0;
						$responsablesAvancePartidasUpdated = $avance->GetResponsablesAvancePartidas();
						reset($responsablesAvancePartidasUpdated);
						$responsableAvancePartidasUpdated = current($responsablesAvancePartidasUpdated);
						foreach ($avanceSaved->GetResponsablesAvancePartidas() as $responsableAvancePartidasSaved)
						{
							if($responsableAvancePartidasSaved->GetResponsableAvance() == null)
								throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
									'responsable['.($indexResponsable+1).'] guardado es nulo.');
								
							// - Eliminar los responsables excedentes en el avance guardado
							if($responsableAvancePartidasUpdated === false)
							{
								if(
									SafiModeloResponsableAvancePartidas::
										EliminarResponsableAvancePartidas($responsableAvancePartidasSaved) === false
								)
									throw new Exception('Error al actualizar el avance. No se pudo eliminar la información del '.
										'responsable['.($indexResponsable+1).'].');
							}
							// - Actualizar cada una de los responsables guardados para el avance en la base de datos
							else
							{
								$responsableAvanceUpdated = $responsableAvancePartidasUpdated->GetResponsableAvance();
							
								if($responsableAvanceUpdated == null)
									throw new Exception('Error al actualizar el avance. El parámetro responsableAvance del '.
										'responsable['.($indexResponsable+1).'] es nulo.');
								
								// Establecer el id del responsable en el responsable del avance actualizado
								$responsableAvanceUpdated->SetId($responsableAvancePartidasSaved->GetResponsableAvance()->GetId());	
								// Establecer el id del avance en el avance actualizado
								$responsableAvanceUpdated->SetIdAvance($idAvance);
								
								if(
									SafiModeloResponsableAvancePartidas::
										ActualizarResponsableAvancePartidas($responsableAvancePartidasUpdated) === false
								)
									throw new Exception('Error al actualizar el avance. No se pudo actualizar la información del '.
										'responsable['.($indexResponsable+1).'].');
								
								$responsableAvancePartidasUpdated = next($responsablesAvancePartidasUpdated);
							}
							
							$indexResponsable = 0;
						}
					}
				}
			}
			
			/******************************************************************************
			 ******* Actualizar la información relacionada con las rutas del avance *******
			 ******************************************************************************/
			
			// El avance guardado no tiene rutas
			if(!is_array($avanceSaved->GetRutasAvance()) || count($avanceSaved->GetRutasAvance()) == 0)
			{
				// El avance actualizado tiene al menos una ruta
				if(is_array($avance->GetRutasAvance()) && count($avance->GetRutasAvance()) > 0 )
				{
					// Insertar las rutas del avance actualizado en la base de datos
					$indexRuta = 0;
					foreach ($avance->GetRutasAvance() as $rutaAvance)
					{
						// Establecer el id del avance en la ruta del avance
						$rutaAvance->SetIdAvance($idAvance);
						
						// Guardar la ruta del avance
						if(SafiModeloRutaAvance::GuardarRutaAvance($rutaAvance) === false)
							throw new Exception('Error al actualizar el avance. No se pudo guardar la información de '.
								'la ruta ['.($indexRuta+1).'].');
						
						$indexRuta++;
					}
				}
			}
			else // El avance guardado tiene al menos una rutas
			{
				// El avance actualizado no tienes rutas
				if(!is_array($avance->GetRutasAvance()) || count($avance->GetRutasAvance()) == 0 )
				{
					// Eliminar las rutas del avance guardado
					if(SafiModeloRutaAvance::EliminarRutasAvanceByIdAvance($idAvance) === false)
						throw new Exception('Error al eliminar las rutas del avance ' . $idAvance);
					
				}
				else // El avance actualizado tiene al menos una ruta
				{
					// El número de rutas es igual tanto en el avance guardado como en el avance actualizado
					if(count($avance->GetRutasAvance()) == count($avanceSaved->GetRutasAvance()))
					{
						// Actualizar cada una de las rutas guardadas para el avance en la base de datos
						$indexRuta = 0;
						$rutasAvanceSaved = $avanceSaved->GetRutasAvance();
						reset($rutasAvanceSaved);
						$rutaAvanceSaved = current($rutasAvanceSaved);
						foreach ($avance->GetRutasAvance() as $rutaAvance)
						{
							// Establecer el id de la ruta en la ruta del avance a guardar
							$rutaAvance->SetId($rutaAvanceSaved->GetId());
							// Establecer el id del avance en la ruta del avance a guardar
							$rutaAvance->SetIdAvance($idAvance);
							
							// Actualizar la ruta del avance
							if(SafiModeloRutaAvance::ActualizarRutaAvance($rutaAvance) === false)
								throw new Exception('Error al actualizar el avance. No se pudo actualizar la información de '.
									'la ruta ['.($indexRuta+1).'].');
							
							$indexRuta++;
							$rutaAvanceSaved = next($rutasAvanceSaved);
						}
					}
					// El número de rutas en el avance actualizado es mayor que en el avance guardado
					else if(count($avance->GetRutasAvance()) > count($avanceSaved->GetRutasAvance()))
					{
						$indexRuta = 0;
						$rutasAvanceSaved = $avanceSaved->GetRutasAvance();
						reset($rutasAvanceSaved);
						$rutaAvanceSaved = current($rutasAvanceSaved);
						foreach ($avance->GetRutasAvance() as $rutaAvanceUpdated)
						{
							// - Insertar la nuevas rutas para el avance guardado
							if($rutaAvanceSaved === false)
							{
								// Establecer el id del avance en la ruta del avance
								$rutaAvanceUpdated->SetIdAvance($idAvance);
								
								// Guardar la ruta del avance
								if(SafiModeloRutaAvance::GuardarRutaAvance($rutaAvanceUpdated) === false)
									throw new Exception('Error al actualizar el avance. No se pudo guardar la información de '.
										'la ruta ['.($indexRuta+1).'].');
							}
							// - Actualizar cada una de las rutas guardadas para el avance en la base de datos
							else
							{
								// Establecer el id de la ruta en la ruta del avance a guardar
								$rutaAvanceUpdated->SetId($rutaAvanceSaved->GetId());
								// Establecer el id del avance en la ruta del avance a guardar
								$rutaAvanceUpdated->SetIdAvance($idAvance);
								
								// Actualizar la ruta del avance
								if(SafiModeloRutaAvance::ActualizarRutaAvance($rutaAvanceUpdated) === false)
									throw new Exception('Error al actualizar el avance. No se pudo actualizar la información de '.
										'la ruta ['.($indexRuta+1).'].');
									
								$rutaAvanceSaved = next($rutasAvanceSaved);
							}
							
							$indexRuta++;
						}
					}
					// El número de rutas en el avance actualizado es menor que en el avance guardado
					else if(count($avance->GetRutasAvance()) < count($avanceSaved->GetRutasAvance()))
					{
						$indexRuta = 0;
						$rutasAvanceUpdated = $avance->GetRutasAvance();
						reset($rutasAvanceUpdated);
						$rutaAvanceUpdated = current($rutasAvanceUpdated);
						foreach ($avanceSaved->GetRutasAvance() as $rutaAvanceSaved)
						{
							// - Eliminar las rutas excedentes en el avance guardado
							if($rutaAvanceUpdated === false)
							{
								if(SafiModeloRutaAvance::EliminarRutaAvance($rutaAvanceSaved) === false)
									throw new Exception('Error al actualizar el avance. No se pudo eliminar la la ruta ['
										.($indexRuta+1).'] guardada en la base de datos, con id ' . $rutaAvanceSaved->GetId() .'.');
							}
							// - Actualizar cada una de las rutas guardadas para el avance en la base de datos
							else
							{
								// Establecer el id de la ruta en la ruta del avance a guardar
								$rutaAvanceUpdated->SetId($rutaAvanceSaved->GetId());
								// Establecer el id del avance en la ruta del avance a guardar
								$rutaAvanceUpdated->SetIdAvance($idAvance);
								
								// Actualizar la ruta del avance
								if(SafiModeloRutaAvance::ActualizarRutaAvance($rutaAvanceUpdated) === false)
									throw new Exception('Error al actualizar el avance. No se pudo actualizar la información de '.
										'la ruta ['.($indexRuta+1).'].');
								
								$rutaAvanceUpdated = next($rutasAvanceUpdated);
							}
							
							$indexRuta++;
						}
					}
				}
			}
				
			if(($result = $GLOBALS['SafiClassDb']->CommitTransaction()) === false)
				throw new Exception("Error al intentar hacer commit sobre la transacción. Detalles: ".
					$GLOBALS['SafiClassDb']->GetErrorMsg());
				
			return true;
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
		}
		
		return false;
	}
	
	public static function GetAvance(EntidadAvance $findAvance)
	{
		$avance = null;
		$where = array();
		
		try {
			if($findAvance == null)
				throw new Exception("El parametro avance es nulo");
				
			if($findAvance->GetId() == null || trim($findAvance->GetId()) == ''){
				throw new Exception("El parametro avance->GetId() es nulo o esta vacio.");
			} else {
				$where[] = "avance.id = '".$GLOBALS['SafiClassDb']->Quote($findAvance->GetId())."'";
			} 
			
			$query = "
				SELECT
					" . self::GetSelectFildsAvance() . "
				FROM
					safi_avance avance
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = avance.depe_id)
					LEFT JOIN safi_categoria_viatico categoria ON (categoria.id = avance.categoria_id)
					LEFT JOIN safi_avance_categoria_red avance_categoria_red
						ON (avance_categoria_red.avance_id = avance.id AND avance_categoria_red.categoria_id = avance.categoria_id)
					LEFT JOIN safi_red red ON (red.id = avance_categoria_red.red_id)
				WHERE
					".implode(" AND ", $where)."
			";
			//echo $query;
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener el avance. Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$avance = self::LlenarAvance($row);
			}
					
			return $avance;
			
		} catch (Exception $e) {
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetAvanceEnBandeja($params)
	{
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : null;
		$estatus = is_array($params['estatus']) ? $params['estatus'] : null;
		$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;
		
		$idTipoDocumento = GetConfig("preCodigoAvance");
		
		$where = " doc_genera.docg_id LIKE '".$idTipoDocumento."%'";
		
		if($idPerfilActual == null && $idPerfilActual == '')
			return null;
			
		$where .= " AND doc_genera.perf_id_act = '".$idPerfilActual."'";
		
		if($estatus == null || count($estatus)==0)
			return null;
		
		$where .= " AND ( doc_genera.esta_id = " . implode('OR doc_genera.esta_id = ', $estatus) . ")";
		
		if($idDependencia != null){
			$where .= " AND avance.depe_id = '" . $idDependencia . "'";
		}
			
		return self::__GetAvanceBadejasByWhere($where);
	}
	
	public static function GetAvancePorEnviar($params)
	{
		$usuaLogin = isset($params['usuaLogin']) ? $params['usuaLogin'] : '';
		$idPerfilActual = isset($params['idPerfilActual']) ? $params['idPerfilActual'] : '';
		
		$idTipoDocumento = GetConfig("preCodigoAvance");
		$estadoPorEnviar = 59;
		
		if($usuaLogin != '' && $idPerfilActual != ''){
			$where = "
	  			doc_genera.docg_id LIKE '".$idTipoDocumento."%' AND
	  			doc_genera.usua_login = '".$usuaLogin."' AND
	  			doc_genera.esta_id = ".$estadoPorEnviar." AND
	  			doc_genera.perf_id_act = '".$idPerfilActual."'
			";
			
			return self::__GetAvanceBadejasByWhere($where);
		}
		
		return null;
	}
	
	public static function GetAvanceEnTransito($params = array())
	{
		try
		{
			$idDependencia = isset($params['idDependencia']) ? $params['idDependencia'] : null;
			
			$idTipoDocumento = GetConfig("preCodigoAvance");
			$estadoEnTransito = 10;
			
			$where = "
	  			doc_genera.docg_id LIKE '".$idTipoDocumento."%' AND
	  			doc_genera.esta_id = ".$estadoEnTransito."
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
					AND avance.depe_id NOT IN ('".implode("' ,'", $noEnDependencia)."')
				";
			}
			
			if($idDependencia != null){
				$where .= " AND avance.depe_id = '" . $idDependencia . "'";
			}
			
			return self::__GetAvanceBadejasByWhere($where);
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}
	
	public static function BuscarAvance(EntidadAvance $avance = null, EntidadDocGenera $docGenera = null, array $params = null)
	{
		try
		{
			$where = '';
			$fechaInicio = '';
			$fechaFin = '';
			
			if($params != null){
				if(isset($params['fechaInicio']) && trim($params['fechaInicio']) != ''){
					$fechaInicio = $params['fechaInicio']; 
				}
				
				if(isset($params['fechaFin']) && trim($params['fechaFin']) != ''){
					$fechaFin = $params['fechaFin']; 
				}
				
				
				if($fechaInicio != '' && $fechaFin != ''){
					$where = "
						avance.fecha_avance BETWEEN
							to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
							to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
					";
				} else if ($fechaInicio != ''){
					$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($params['fechaInicio'])
						."', 'DD/MM/YYYY') <= avance.fecha_avance";
				} else if ($fechaFin != '') {
					$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($params['fechaFin'])
						." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= avance.fecha_avance";
				}
			}
			
			if($avance != null)
			{
				if($avance->GetId() != null && ($idAvance=trim($avance->GetId())) != '')
				{
					if($where != ''){
						$where .= " AND ";
					}
					$where = "lower(avance.id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($idAvance)) . "'";
				}
			}
			
			if($where == '')
				throw new Exception("Error al buscar los avances. Detalles: No existen criterios de búsqueda.");
			
			if($avance != null && $avance->GetDependencia() != null && $avance->GetDependencia()->GetId() != null
				&& ($idDependencia=trim($avance->GetDependencia()->GetId())) != ''
			){
				$where .= " AND avance.depe_id = '" . $idDependencia . "'";
			}
			
			
			$query = "
				SELECT
	  				" . SafiModeloDocGenera::GetSelectFieldsDocGenera() . ",
	  				" . self::GetSelectFildsAvance() . "
				FROM
					sai_doc_genera doc_genera
					INNER JOIN safi_avance avance ON (avance.id = doc_genera.docg_id)
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = avance.depe_id)
					LEFT JOIN safi_categoria_viatico categoria ON (categoria.id = avance.categoria_id)
					LEFT JOIN safi_avance_categoria_red avance_categoria_red
						ON (avance_categoria_red.avance_id = avance.id AND avance_categoria_red.categoria_id = avance.categoria_id)
					LEFT JOIN safi_red red ON (red.id = avance_categoria_red.red_id)
				" . ($where != '' ? " WHERE " . $where : '') . "
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error en la búsqueda de avances. Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$dataAvances[$row['docg_id']]['ClassDocGenera'] = SafiModeloDocGenera::LlenarDocGenera($row);
				$dataAvances[$row['docg_id']]['ClassAvance'] = self::LlenarAvance($row);
			}
			
			return $dataAvances;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	private static function __GetAvanceBadejasByWhere($where)
	{
		$dataAvances = array();
		
		try
		{
			if($where == null || trim($where) == '')
				throw new Exception("EL parámetro where es nulo o está vacío.");
			
			$query = "
				SELECT
					" . SafiModeloDocGenera::GetSelectFieldsDocGenera() . ",
	  				" . self::GetSelectFildsAvance() . "
				FROM
					sai_doc_genera doc_genera
					INNER JOIN safi_avance avance ON (avance.id = doc_genera.docg_id)
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = avance.depe_id)
					LEFT JOIN safi_categoria_viatico categoria ON (categoria.id = avance.categoria_id)
					LEFT JOIN safi_avance_categoria_red avance_categoria_red
						ON (avance_categoria_red.avance_id = avance.id AND avance_categoria_red.categoria_id = avance.categoria_id)
					LEFT JOIN safi_red red ON (red.id = avance_categoria_red.red_id)
				" . ($where != '' ? " WHERE " . $where : '') . "
				ORDER BY
  					doc_genera.docg_fecha
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los avances de alguna de las bandejas. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$dataAvances[$row['docg_id']]['ClassDocGenera'] =  SafiModeloDocGenera::LlenarDocGenera($row);
				$dataAvances[$row['docg_id']]['ClassAvance'] = self::LlenarAvance($row);
			}
			
			return $dataAvances;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	private static function GetSelectFildsAvance()
	{
		return  "
					avance.id AS avance_id,
					to_char(avance.fecha_avance, 'DD/MM/YYYY') AS avance_fecha_avance,
					to_char(avance.fecha_registro, 'DD/MM/YYYY HH24:MI:SS') AS avance_fecha_registro,
					to_char(avance.fecha_ultima_modificacion, 'DD/MM/YYYY HH24:MI:SS') AS avance_fecha_ultima_modificacion,
					avance.categoria_id AS avance_categoria_id,
					avance.proyecto_id AS avance_proyecto_id,
					avance.proyecto_anho AS avance_proyecto_anho,
					avance.proyecto_especifica_id AS avance_proyecto_especifica_id,
					avance.accion_central_id AS avance_accion_central_id,
					avance.accion_central_anho AS avance_accion_central_anho,
					avance.accion_central_especifica_id AS avance_accion_central_especifica_id,
					to_char(avance.fecha_inicio_actividad, 'DD/MM/YYYY') AS avance_fecha_inicio_actividad,
					to_char(avance.fecha_fin_actividad, 'DD/MM/YYYY') AS avance_fecha_fin_actividad,
					avance.objetivos AS avance_objetivos,
					avance.descripcion AS avance_descripcion,
					avance.justificacion AS avance_justificacion,
					avance.nro_participantes AS avance_nro_participantes,
					avance.observaciones AS avance_observaciones,
					avance.usua_login AS avance_usua_login,
					avance.depe_id AS avance_depe_id,
					categoria.id AS categoria_id,
					categoria.nombre AS categoria_nombre,
					categoria.descripcion AS categoria_descripcion,
					categoria.estatus_actividad AS categoria_estatus_actividad,
					red.id AS red_id,
					red.nombre AS red_nombre,
					red.estatus_actividad AS red_estatus_actividad,
					dependencia.depe_nombre AS dependencia_nombre,
					dependencia.depe_nombrecort AS dependencia_nombrecort,
					dependencia.depe_id_sup AS dependencia_id_sup,
					dependencia.depe_nivel AS dependencia_nivel,
					dependencia.depe_cosige AS dependencia_cosige,
					dependencia.usua_login AS dependencia_usua_login,
					dependencia.depe_observa AS dependencia_observa,
					dependencia.esta_id AS dependencia_esta_id,
					avance.punto_cuenta_id AS avance_punto_cuenta_id
		";
	}
	
	private static function LlenarAvance(array $row, array $excluir = null)
	{
		$avance = new EntidadAvance();
		$categoria = null;
		$red = null;
		$tipoProyectoAccionCentralizada = null;
		$proyecto = null;
		$proyectoEspecifica = null;
		$accionCentralizada = null;
		$accionCentralizadaEspecifica = null;
		$infocentros = null;
		$rutasAvance = null;
		$responsablesAvancePartidas = null;
		
		$excluirInfocentros = false;
		$excluirResponsables = false;
		$excluirRutas = false;
		
		if($excluir != null && is_array($excluir) && count($excluir) > 0){
			if(in_array("infocentros", $excluir)) $excluirInfocentros = true;
			if(in_array("responsables", $excluir)) $excluirResponsables = true;
			if(in_array("rutas", $excluir)) $excluirRutas = true;
		}

		if($row['avance_proyecto_id'] != null && $row['avance_proyecto_anho'] != null && $row['avance_proyecto_especifica_id'])
		{
			$tipoProyectoAccionCentralizada = EntidadProyectoAccionCentralizada::TIPO_PROYECTO;
			
			$proyecto = SafiModeloProyecto::GetProyectoById($row['avance_proyecto_id'], $row['avance_proyecto_anho']);
			
			$proyectoEspecifica = SafiModeloProyectoEspecifica::GetProyectoEspecificaById
				($row['avance_proyecto_especifica_id'], $row['avance_proyecto_id'], $row['avance_proyecto_anho']);
			
		}
		else if(
			$row['avance_accion_central_id'] != null && $row['avance_accion_central_anho'] != null
			&& $row['avance_accion_central_especifica_id']
		){
			$tipoProyectoAccionCentralizada = EntidadProyectoAccionCentralizada::TIPO_ACCION_CENTRALIZADA;
			
			$accionCentralizada = SafiModeloAccionCentralizada::GetAccionCentralizadaById
				($row['avance_accion_central_id'], $row['avance_accion_central_anho']);
			
			$accionCentralizadaEspecifica = SafiModeloAccionCentralizadaEspecifica::GetAccionCentralizadaEspecificaById
				($row['avance_accion_central_especifica_id'], $row['avance_accion_central_id'], $row['avance_accion_central_anho']);
		}
		
		// Establecer los datos de la categoría
		if($row['categoria_id'] != null)
		{
			$categoria = new EntidadCategoriaViatico();
			$categoria->SetId($row['categoria_id']);
			
			if($row['categoria_nombre'] != null)
			{
				$categoria->SetNombre($row['categoria_nombre']);
				$categoria->SetDescripcion($row['categoria_descripcion']);
				$categoria->SetEstatusActividad($row['categoria_estatus_actividad']);
			}
		}
		
		// Establecer los datos de la red
		if($row['red_id'] != null)
		{
			$red = new EntidadRed();
			$red->SetId($row['red_id']);
			
			if($row['red_nombre'] != null)
			{
				$red->SetNombre($row['red_nombre']);
				$red->SetEstatusActividad($row['red_estatus_actividad']);
			}
		}
		
		// Establecer los datos de los infocentros del avance
		if($excluirInfocentros !== true)
			$infocentros = SafiModeloInfocentro::GetInfocentrosByIdAvance($row['avance_id']);
		
		// Establecer los datos de las rutas del avance
		if($excluirRutas !== true)
			$rutasAvance = SafiModeloRutaAvance::GetRutasByIdAvance($row['avance_id']);
		
		// Establecer los datos de los responsables del avance
		if($excluirResponsables !== true)	
			$responsablesAvancePartidas = SafiModeloResponsableAvancePartidas::GetResponsableAvancePartidasByIdAvance($row['avance_id']);

		// Establecer los datos de la red
		if($row['avance_depe_id'] != null)
		{
			$dependencia = new EntidadDependencia();
			$dependencia->SetId($row['avance_depe_id']);
			
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
		
		// Eatablecer los datos del punto de cuenta
		$puntoCuenta = null;
		if($row['avance_punto_cuenta_id'] != null)
		{
			$puntoCuenta = new EntidadPuntoCuenta();
			$puntoCuenta->SetId($row['avance_punto_cuenta_id']);
		}
		
		$avance->SetId($row['avance_id']);
		$avance->SetFechaAvance($row['avance_fecha_avance']);
		$avance->SetFechaRegistro($row['avance_fecha_registro']);
		$avance->SetFechaUltimaModificacion($row['avance_fecha_ultima_modificacion']);
		$avance->SetCategoria($categoria);
		$avance->SetRed($red);
		$avance->SetTipoProyectoAccionCentralizada($tipoProyectoAccionCentralizada);
		$avance->SetProyecto($proyecto);
		$avance->SetProyectoEspecifica($proyectoEspecifica);
		$avance->SetAccionCentralizada($accionCentralizada);
		$avance->SetAccionCentralizadaEspecifica($accionCentralizadaEspecifica);
		$avance->SetFechaInicioActividad($row['avance_fecha_inicio_actividad']);
		$avance->SetFechaFinActividad($row['avance_fecha_fin_actividad']);
		$avance->SetObjetivos($row['avance_objetivos']);
		$avance->SetDescripcion($row['avance_descripcion']);
		$avance->SetJustificacion($row['avance_justificacion']);
		$avance->SetNroParticipantes($row['avance_nro_participantes']);
		$avance->SetInfocentros($infocentros);
		$avance->SetResponsablesAvancePartidas($responsablesAvancePartidas);
		$avance->SetRutasAvance($rutasAvance);
		$avance->SetObservaciones($row['avance_observaciones']);
		$avance->SetUsuaLogin($row['avance_usua_login']);
		$avance->SetDependencia($dependencia);
		$avance->SetPuntoCuenta($puntoCuenta);
		
		return $avance;
	}
	
	public static function GetAvances(array $params = null)
	{
		try
		{
			$avances = null;
			$preMsg = "Error al intentar obtener los avances.";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			if(!isset($params['idsAvances']))
				$arrMsg[] = "El parámetro params['idsAvances'] no pudo ser encontrado.";
			else if(($idsAvances=$params['idsAvances']) == null)
				$arrMsg[] = "El parámetro params['idsAvances'] es nulo.";
			else if(!is_array($idsAvances))
				$arrMsg[] = "El parámetro params['idsAvances'] no es un arreglo.";
			else if(count($idsAvances) == 0)
				$arrMsg[] = "El parámetro params['idsAvances'] está vacío.";
			else{
				$existeCriterio = true;
				$queryWhere = "avance.id IN ('".implode("' ,'", $idsAvances)."')";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					".self::GetSelectFildsAvance()."
				FROM
					safi_avance avance
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = avance.depe_id)
					LEFT JOIN safi_categoria_viatico categoria ON (categoria.id = avance.categoria_id)
					LEFT JOIN safi_avance_categoria_red avance_categoria_red
						ON (avance_categoria_red.avance_id = avance.id AND avance_categoria_red.categoria_id = avance.categoria_id)
					LEFT JOIN safi_red red ON (red.id = avance_categoria_red.red_id)
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$avances[$row['avance_id']] = self::LlenarAvance($row);
			}
			
			return $avances;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function ReporteResponsables(array $params = null)
	{
		try
		{
			$dataAvances = null;
			$preMsg = "Error al intentar realizar una búsqueda del reporte de responsables de avances.";
			
			$where = "";
			$fechaInicio = null;
			$fechaFin = null;
			$fechaRendicionInicio = null;
			$fechaRendicionFin = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			// Validar las fechas de inicio del avance
			if(isset($params['fechaInicio']))
			{
				if(($__fechaInicio=trim($params['fechaInicio'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaInicio'] está vacío.");
					
				$fechaInicio = $__fechaInicio;
			}
			
			// Validar las fechas de fin del avance
			if(isset($params['fechaFin']))
			{
				if(($__fechaFin=trim($params['fechaFin'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaFin'] está vacío.");
					
				$fechaFin = $__fechaFin; 
			}
			
			
			if($fechaInicio != null && $fechaFin != null){
				$where = "
					avance.fecha_avance BETWEEN
						to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') AND 
						to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaInicio != null){
				$where = "to_date('".$GLOBALS['SafiClassDb']->Quote($fechaInicio)."', 'DD/MM/YYYY') <= avance.fecha_avance";
			} else if ($fechaFin != null) {
				$where = "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaFin)
					." 23:59:59', 'DD/MM/YYYY HH24:MI:SS') >= avance.fecha_avance";
			}
			
			// Validar las fechas de inicio de la rendición del avance
			if(isset($params['fechaRendicionInicio']))
			{
				if(($__fechaRendicionInicio=trim($params['fechaRendicionInicio'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaRendicionInicio'] está vacío.");
					
				$fechaRendicionInicio = $__fechaRendicionInicio;
			}
			
			// Validar las fechas de fin de la rendición del avance
			if(isset($params['fechaRendicionFin']))
			{
				if(($__fechaRendicionFin=trim($params['fechaRendicionFin'])) == '')
					throw new Exception($preMsg." El parámetro params['fechaRendicionFin'] está vacío.");
					
				$fechaRendicionFin = $__fechaRendicionFin; 
			}
			
			if($fechaRendicionInicio != null && $fechaRendicionFin != null){
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "
					rendicion.fecha_rendicion BETWEEN
						to_date('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionInicio)."', 'DD/MM/YYYY') AND 
						to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionFin)." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
				";
			} else if ($fechaRendicionInicio != null){
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "to_date('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionInicio)."', 'DD/MM/YYYY') <= rendicion.fecha_rendicion";
			} else if ($fechaRendicionFin != null) {
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "to_timestamp('".$GLOBALS['SafiClassDb']->Quote($fechaRendicionFin)
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
				$where .= "lower(avance.id) = '" . mb_strtolower($GLOBALS['SafiClassDb']->Quote($idAvance)) . "'";
			}
			
			if(isset($params['cedulaResponsable']))
			{
				if(($cedulaResponsable = trim($params['cedulaResponsable'])) == '')
					throw new Exception($preMsg. " El parámetro params['cedulaResponsable'] está vacío.");
					
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "
					(
						responsable_avance.empleado_cedula = '" . $GLOBALS['SafiClassDb']->Quote($cedulaResponsable) . "'
						OR responsable_avance.beneficiario_cedula = '" . $GLOBALS['SafiClassDb']->Quote($cedulaResponsable) . "'
					)";
			}
			
			if(isset($params['idEstado']))
			{
				if(($idEstado = trim($params['idEstado'])) == '')
					throw new Exception($preMsg. " El parámetro params['idEstado'] está vacío.");
					
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "responsable_avance.edo_id = '" . $GLOBALS['SafiClassDb']->Quote($idEstado) . "'";
			}
			
			if(isset($params['idRegionReporte']))
			{
				if(($idRegionReporte = trim($params['idRegionReporte'])) == '')
					throw new Exception($preMsg. " El parámetro params['idRegionReporte'] está vacío.");
					
				if($where != ""){
					$where .= " AND ";
				}
				$where .= "
					responsable_avance.edo_id IN (
						SELECT
							region_reporte_estado.edo_id
						FROM
							safi_region_reporte_estado region_reporte_estado
						WHERE
							region_reporte_estado.region_id = '".$GLOBALS['SafiClassDb']->Quote($idRegionReporte)."'
					)
				";
			}
			
			// Validar el estatus de la rendición del avance
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
			
			if($where == "")
				throw new Exception($preMsg." No existen criterios de búsqueda.");
				
			if(
				isset($params['dependencia']) && ($dependencia = $params['dependencia']) != null && $dependencia->GetId() != null
				&& ($idDependencia=trim($dependencia->GetId())) != ''
			){
					$where .= " AND avance.depe_id = '" . $idDependencia . "'";
			}
			
			$query = "
				SELECT
	  				" . self::GetSelectFildsAvance() . ",
	  				"  . SafiModeloRendicionAvance::GetSelectFildsRendicion() . ",
	  				responsable_avance.id AS responsable_avance_id
				FROM
					safi_avance avance
					LEFT JOIN sai_dependenci dependencia ON (dependencia.depe_id = avance.depe_id)
					LEFT JOIN safi_categoria_viatico categoria ON (categoria.id = avance.categoria_id)
					LEFT JOIN safi_avance_categoria_red avance_categoria_red
						ON (avance_categoria_red.avance_id = avance.id AND avance_categoria_red.categoria_id = avance.categoria_id)
					LEFT JOIN safi_red red ON (red.id = avance_categoria_red.red_id)
					INNER JOIN safi_responsable_avance responsable_avance ON (responsable_avance.avance_id = avance.id)
					LEFT JOIN (
						SELECT DISTINCT
							rendicion.*,
							responsable_rendicion_avance.*
						FROM
							safi_rendicion_avance rendicion
							INNER JOIN safi_responsable_rendicion_avance responsable_rendicion_avance
								ON (responsable_rendicion_avance.rendicion_avance_id = rendicion.id)
							INNER JOIN sai_doc_genera documento ON (documento.docg_id = rendicion.id)
						WHERE
							documento.esta_id != 15
					) rendicion
							ON (rendicion.avance_id = avance.id AND rendicion.responsable_avance_id = responsable_avance.id)
				" . ($where != '' ? " WHERE " . $where : '') . "
				ORDER BY
					avance.fecha_avance, responsable_avance.edo_id
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			$dataAvances = array();
			
			$idsAvances = array();
			$idsResponsablesAvances = array();
			$idsResponsablesRendicionAvances = array();
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$dataAvance = array();
				$responsableAvancePartidas = new EntidadResponsableAvancePartidas();
				$avance = self::LlenarAvance($row, array("infocentros", "rutas", "responsables"));
				$responsableAvance = SafiModeloResponsableAvance::LlenarResponsable($row);
				$responsableAvancePartidas->SetResponsableAvance($responsableAvance);
				
				$idsAvances[$row['avance_id']] = $row['avance_id'];
				
				$rendicion = $row["rendicion_id"] != null
					? SafiModeloRendicionAvance::LlenarRendicion($row, array("responsables")) : null;
				
				$idsResponsablesAvances[] = $row['responsable_avance_id'];
				
				if($row["rendicion_id"] != null)
					$idsResponsablesRendicionAvances[] = $row['responsable_avance_id'];
				
				$dataAvance['ClassAvance'] = $avance;
				$dataAvance['ClassRendicionAvance'] = $rendicion;
				
				$dataAvances[$row['responsable_avance_id']] = $dataAvance;
			}
			
			// Obtener los responsables de los avances
			if(is_array($idsResponsablesAvances) && count($idsResponsablesAvances) > 0)
				$responsablesAvancePartidas = SafiModeloResponsableAvancePartidas::
					GetResponsableAvancePartidas(array('idsResponsables' => $idsResponsablesAvances));
			
			// Obtener los responsables de la rendición de avances
			if(is_array($idsResponsablesRendicionAvances) && count($idsResponsablesRendicionAvances) > 0)
				$responsablesRendicionAvancePartidas = SafiModeloResponsableRendicionAvancePartidas::
					GetResponsableRendicionAvancePartidas(array('idsResponsables' => $idsResponsablesRendicionAvances));
					
			// Obtener el doc_genera de los avances
			$documentosAvance = null;
			if(is_array($idsAvances) && count($idsAvances) > 0){
				$documentosAvance = SafiModeloDocGenera::GetDocGeneraByIdsDocuments($idsAvances);
				$documentosAvance = $documentosAvance != null && is_array($documentosAvance) ? $documentosAvance['docGenera'] : null;
			}
			
			if(is_array($responsablesAvancePartidas))
			{
				foreach ($dataAvances AS $idResponsableAvance => &$dataAvance)
				{
					$dataAvance['ClassResponsableAvancePartidas'] = $responsablesAvancePartidas[$idResponsableAvance];
					$dataAvance['ClassResponsableRendicionAvancePartidas'] = $responsablesRendicionAvancePartidas[$idResponsableAvance];
					$dataAvance['documentoAvance'] =
						is_array($documentosAvance) && isset($documentosAvance[$dataAvance['ClassAvance']->GetId()])
						? $documentosAvance[$dataAvance['ClassAvance']->GetId()] : null;
				}
				unset($dataAvance);
			}
			
			return $dataAvances;				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}

	public static function BuscarIdsAvances($codigoDocumento, $idDependencia, $numLimit) {
		$idsViaticos = null;
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
			throw new Exception("Error al buscar los ids de avances. Detalles: El código del documento o la dependencia es nulo o vacío");
	
			$query = "
			            SELECT
			               a.id
			            FROM
			                safi_avance a, sai_doc_genera d
			            WHERE
			               a.id = d.docg_id AND
			               a.id LIKE '%".$codigoDocumento."%' AND
			               a.depe_id = '".$idDependencia."' AND
			               d.esta_id <> 15 AND 
			               a.id NOT IN (
			               				SELECT nro_documento
			               				FROM registro_documento
			               				WHERE tipo_documento = 'avan'
			               				AND id_estado=1
			               				AND user_depe='".$_SESSION['user_depe_id']."'
			               				) AND 
			               	a.fecha_avance LIKE '".$_SESSION['an_o_presupuesto']."%'  
			            ORDER BY a.id 
						LIMIT
						".$numLimit."
			        ";

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de avances. Detalles: ".
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

	/*public static function BuscarInfoAvance($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='')
			throw new Exception("Error al buscar la información del avance");
			
			$query = "
				SELECT TO_CHAR(a.fecha_avance, 'dd-mm-yyyy') AS fecha,
					r.avance_id AS id,
					e.empl_cedula AS id_beneficiario,
					e.empl_nombres || ' ' ||e.empl_apellidos AS beneficiario,
					a.objetivos AS objetivos
				FROM safi_responsable_avance r, sai_empleado e, safi_avance a
				WHERE r.empleado_cedula = e.empl_cedula 
					AND r.avance_id=a.id 
					AND r.avance_id='".$codigoDocumento."' 
				UNION
				SELECT TO_CHAR(a.fecha_avance, 'dd-mm-yyyy') AS fecha,
					r.avance_id AS id,
					v.benvi_cedula AS id_beneficiario,
					v.benvi_nombres || ' ' || v.benvi_apellidos AS beneficiario,
					a.objetivos AS objetivos
				FROM safi_responsable_avance r, sai_viat_benef v, safi_avance a 
				WHERE r.beneficiario_cedula = v.benvi_cedula
					AND r.avance_id = a.id 
					AND r.avance_id='".$codigoDocumento."'";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de avances. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			$avance = new EntidadAvance();
			$avance2 = new EntidadAvance();
	
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[0] = $row['id'];
				$ids[1] = $row['id_beneficiario'].":".$row['beneficiario'];
				$avance->SetId($ids[0]);
				$avance2 = self::GetAvance($avance);
				$ids[2] = $avance2->GetMontoTotal();
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
	}*/
	
	public static function BuscarInfoAvance($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='')
			throw new Exception("Error al buscar la información del avance");
			$avance = new EntidadAvance();
			$avance->SetId($codigoDocumento);
			$avance2 = new EntidadAvance();
			$avance2 = self::GetAvance($avance);
				
			$query = "
					SELECT TO_CHAR(a.fecha_avance, 'dd-mm-yyyy') AS fecha,
						r.avance_id AS id,
						e.empl_cedula AS id_beneficiario,
						e.empl_nombres || ' ' ||e.empl_apellidos AS beneficiario,
						a.objetivos AS objetivos
					FROM safi_responsable_avance r, sai_empleado e, safi_avance a
					WHERE r.empleado_cedula = e.empl_cedula 
						AND r.avance_id=a.id 
						AND r.avance_id='".$codigoDocumento."' 
					UNION
					SELECT TO_CHAR(a.fecha_avance, 'dd-mm-yyyy') AS fecha,
						r.avance_id AS id,
						v.benvi_cedula AS id_beneficiario,
						v.benvi_nombres || ' ' || v.benvi_apellidos AS beneficiario,
						a.objetivos AS objetivos
					FROM safi_responsable_avance r, sai_viat_benef v, safi_avance a 
					WHERE r.beneficiario_cedula = v.benvi_cedula
						AND r.avance_id = a.id 
						AND r.avance_id='".$codigoDocumento."'";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de avances. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			$avance = new EntidadAvance();
			$responsables = array();
			$responsables = $avance2->GetResponsablesAvancePartidas();
			
	
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[0] = $avance2->GetId();

//				foreach ($avance->GetResponsablesAvancePartidas() as $responsableAvancePartidas)
	//			{
					//$responsableAvance = $responsableAvancePartidas->GetResponsableAvance();
					$responsableAvance = current($responsables)->GetResponsableAvance();
					$id = "";
					$nombre = "";
					$tipoCuenta = "";
				
					// Obtener los datos del empleado/beneficiario
					if(
					$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_EMPLEADO
					&& $responsableAvance->GetEmpleado() != null
					){
						$empleado = $responsableAvance->GetEmpleado();
				
						$id = $empleado->GetId();
						$nombre = $empleado->GetNombres() . ' ' .$empleado->GetApellidos();
					}
					else if (
					$responsableAvance->GetTipoResponsable() == EntidadResponsable::TIPO_BENEFICIARIO
					&& $responsableAvance->GetBeneficiario() != null
					){
						$beneficiario = $responsableAvance->GetBeneficiario();
							
						$id = $beneficiario->GetId();
						$nombre = $beneficiario->GetNombres() . ' ' .$beneficiario->GetApellidos();
					}
		//		}				
				
				
				$ids[1] = $id.":".$nombre;
				$ids[2] = $avance2->GetMontoTotal();
				$ids[3] = $avance2->GetObjetivos();
				if(SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0]))
				$ids[4] = SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0])->GetId();
				else
				$ids[4] = "comp-400";
					
				$ids[5] = str_replace("/","-",$avance2->GetFechaAvance());
			}
	
		}catch(Exception $e){
			error_log($e, 0);
		}
	
		return $ids;
	}
	

}