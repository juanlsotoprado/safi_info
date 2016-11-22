<?php
include_once(SAFI_ENTIDADES_PATH . '/docgenera.php');
include_once(SAFI_ENTIDADES_PATH . '/revisionesdoc.php');
include_once(SAFI_ENTIDADES_PATH . '/memo.php');

include_once(SAFI_MODELO_PATH . '/memo.php');

class SafiModeloDocGenera
{
	public static function GuardarDocGenera($docGenera)
	{
		if($docGenera instanceof EntidadDocGenera){
				
			$query = "
				INSERT INTO
					sai_doc_genera
					(
						docg_id,
						wfob_id_ini,
						wfca_id,
						usua_login,
						perf_id,
						docg_fecha,
						esta_id,
						docg_prioridad,
						perf_id_act,
						estado_pres,
  						numero_reserva,
  						fuente_finan
					)
				VALUES
					(
						'".$docGenera->GetId()."',
						".$docGenera->GetIdWFObjeto().",
						".$docGenera->GetIdWFCadena().",
						'".$docGenera->GetUsuaLogin()."',
						'".$docGenera->GetIdPerfil()."',
						to_timestamp('".$docGenera->GetFecha()."', 'DD/MM/YYYY HH24:MI:SS'),
						".$docGenera->GetIdEstatus().",
						".$docGenera->GetPrioridad().",
						'".$docGenera->GetIdPerfilActual()."',
						".($docGenera->GetEstadoPres() == null ? "NULL" : $docGenera->GetEstadoPres()).",
						".($docGenera->GetNumeroreserva() == null ? "NULL" : "'".$docGenera->GetNumeroreserva()."'").",
						".($docGenera->GetFuenteFinanciamiento() == null ? "NULL" : "'".$docGenera->GetFuenteFinanciamiento()."'")."
					)
			";
			
			//error_log(print_r($query,true));
			if($GLOBALS['SafiClassDb']->Query($query) === false){
				echo 'Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg();
				return false;
			} else {
				return true;
			}
		}

		return false;
	}

	public static function ActualizarDocGenera($docGenera,$fecha = false)
	{
		if($docGenera instanceof EntidadDocGenera){
			$query = "
				UPDATE
					sai_doc_genera
				SET
					wfob_id_ini = ".$docGenera->GetIdWFObjeto().",
					wfca_id = ".$docGenera->GetIdWFCadena().",
					usua_login = '".$docGenera->GetUsuaLogin()."',
					perf_id = '".$docGenera->GetIdPerfil()."',";
					
					if(!$fecha){
					
						$query .= "	docg_fecha = to_timestamp('".$docGenera->GetFecha()."', 'DD/MM/YYYY HH24:MI:SS'),";

					}
							
					
				$query .= "esta_id = ".$docGenera->GetIdEstatus().",
					docg_prioridad = ".$docGenera->GetPrioridad().",
					perf_id_act = ".($docGenera->GetIdPerfilActual() == null ? "NULL" : "'".$docGenera->GetIdPerfilActual()."'").",
					estado_pres = ".($docGenera->GetEstadoPres() == null ? "NULL" : $docGenera->GetEstadoPres()).",
  					numero_reserva = ".($docGenera->GetNumeroreserva() == null ? "NULL" : "'".$docGenera->GetNumeroreserva()."'").",
  					fuente_finan = ".($docGenera->GetFuenteFinanciamiento() == null ? "NULL" : "'".$docGenera->GetFuenteFinanciamiento()."'")."
				WHERE
					docg_id = '".$docGenera->Getid()."' 
			";
			if($GLOBALS['SafiClassDb']->Query($query) === false){
				//echo 'Error al guardar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg();
				return false;
			} else {
				return true;
			}
		}

		return false;
	}


	public static function ActualizarDocGeneras($valor,$params = null)
	{
		try
		{


			$insertoRevision = true;
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
			throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetrrMsg());
				
			if($valor){
				foreach ($valor as $docGenera ){

					if($insertoRevision != false){

						if($docGenera instanceof EntidadDocGenera){

							$query = "
			
				UPDATE
					sai_doc_genera
				SET
					wfob_id_ini = ".$docGenera->GetIdWFObjeto().",
					wfca_id = ".$docGenera->GetIdWFCadena().",
					usua_login = '".$docGenera->GetUsuaLogin()."',
					perf_id = '".$docGenera->GetIdPerfil()."',
					docg_fecha = to_timestamp('".$docGenera->GetFecha()."', 'DD/MM/YYYY HH24:MI:SS'),
					esta_id = ".$docGenera->GetIdEstatus().",
					docg_prioridad = ".$docGenera->GetPrioridad().",
					perf_id_act = ".($docGenera->GetIdPerfilActual() == null ? "NULL" : "'".$docGenera->GetIdPerfilActual()."'").",
					estado_pres = ".($docGenera->GetEstadoPres() == null ? "NULL" : $docGenera->GetEstadoPres()).",
  					numero_reserva = ".($docGenera->GetNumeroreserva() == null ? "NULL" : "'".$docGenera->GetNumeroreserva()."'").",
  					fuente_finan = ".($docGenera->GetFuenteFinanciamiento() == null ? "NULL" : "'".$docGenera->GetFuenteFinanciamiento()."'")."
				WHERE
					docg_id = '".$docGenera->Getid()."' 
			";
								
							 

							if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){

								throw new Exception('Error al  anular pcuenta('.$docGenera->Getid().')');
								$insertoRevision =  $result;

							}
								


						}else{

							$insertoRevision = false;
							 
						}
					}
				}
			}
			 
			 
			$resultado = SafiModeloPuntoCuenta::UpdatePcuentasEstaId($params['PuntoCuenta']);

				

			 
			if($resultado == false){
					
				$insertoRevision = false;

			}



			if($params['revisiones_doc_wfopcion_id']){

				foreach ($params['PuntoCuenta'] as $val){
						
					$params['revisiones_doc_documento_id'] = $val['idPcta'];
					$params['idPcta'] = $val['idPcta'];
					 
					$insertoRevision =  SafiModeloObservacionesDoc::InsertarObservacionesDoc($params);
					 
					 
					if($insertoRevision != true){
							
						throw new Exception('Error al anular pcuenta' . $GLOBALS['SafiClassDb']->GetErrorMsg());

						break;
					}
					 
					 
					 


					$objRevision = SafiModeloRevisionesDoc::LlenarRevisionesDoc($params);
					$insertoRevision =  SafiModeloRevisionesDoc::InsertarRevisionesDoc($objRevision);

					 
					 
					if($insertoRevision != true){
							
						throw new Exception('Error al anular pcuenta' . $GLOBALS['SafiClassDb']->GetErrorMsg());

						break;
					}
					 
					 
					 
				}
				 
		 }
		  
		  
		  
		  



			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
			throw new Exception('Error al intentar hacer commit. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());


			return true;
				
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return false;
		}
	}



	public static function GetDocGeneraByIdDocument($idDocument = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el docgenera dado el id del documento.";
			$docGenera = null;
				
			if($idDocument == null)
			throw new Exception($preMsg." El parámetro \"idDocument\" es nulo.");

			if(($idDocument=trim($idDocument)) == '')
			throw new Exception($preMsg." El parámetro \"idDocument\" está vacío.");
				
			$query = "
				SELECT
	  				".self::GetSelectFieldsDocGenera()."
	  			FROM
					sai_doc_genera doc_genera
				WHERE
					doc_genera.docg_id = '".$idDocument."'
			";
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$docGenera = self::LlenarDocGenera($row);
			}
				
			return $docGenera;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}

	public static function GetDocGeneraByIdsDocuments(array $idDocument = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener el docgenera dado el id del documento.";
			$docGenera = null;
				
			if($idDocument == null)
			throw new Exception($preMsg." El parámetro \"idDocument\" es nulo.");

				
			$query = "
				SELECT
	  				".self::GetSelectFieldsDocGenera()."
	  			FROM
					sai_doc_genera doc_genera
				WHERE
					doc_genera.docg_id IN ('".implode("', '",$idDocument)."')";
				
				
				
				
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

				$arrDocGenera[$row['docg_id']] = self::LlenarDocGenera($row);
				$arrIdActual[$row['docg_perf_id_act']] = $row['docg_perf_id_act'];

			}
				
			$docGenera['docGenera'] =  $arrDocGenera;
			$docGenera['arrIdActual'] =  $arrIdActual;
			 
				
			return $docGenera;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}






	public static function GetSelectFieldsDocGenera()
	{
		return "
					doc_genera.docg_id as docg_id,
					doc_genera.wfob_id_ini as docg_wfob_id_ini,
					doc_genera.wfca_id as docg_wfca_id,
					doc_genera.usua_login as docg_usua_login,
					doc_genera.perf_id as docg_perf_id,
					to_char(doc_genera.docg_fecha, 'DD/MM/YYYY HH24:MI:SS') AS docg_fecha,
					doc_genera.esta_id as docg_esta_id,
					doc_genera.docg_prioridad as docg_prioridad,
					doc_genera.perf_id_act as docg_perf_id_act,
					doc_genera.estado_pres as docg_estado_pres,
	  				doc_genera.numero_reserva as docg_numero_reserva,
	  				doc_genera.fuente_finan as docg_fuente_finan
		";
	}

	public static function LlenarDocGenera($row)
	{
		$docGenera = new EntidadDocGenera();

		$docGenera->SetId($row['docg_id']);
		$docGenera->SetIdWFObjeto($row['docg_wfob_id_ini']);
		$docGenera->SetIdWFCadena($row['docg_wfca_id']);
		$docGenera->SetUsuaLogin($row['docg_usua_login']);
		$docGenera->SetIdPerfil($row['docg_perf_id']);
		$docGenera->SetFecha($row['docg_fecha']);
		$docGenera->SetIdEstatus($row['docg_esta_id']);
		$docGenera->SetPrioridad($row['docg_prioridad']);
		$docGenera->SetIdPerfilActual($row['docg_perf_id_act']);
		$docGenera->SetEstadoPres($row['docg_estado_pres']);
		$docGenera->SetNumeroReserva($row['docg_numero_reserva']);
		$docGenera->SetFuenteFinanciamiento($row['docg_fuente_finan']);

		return $docGenera;
	}

	public static function EnviarDocumento(EntidadDocGenera $docGenera, EntidadRevisionesDoc $revisiones = null)
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
		}

		return false;
	}

	public static function DevolverDocumento(EntidadDocGenera $docGenera, EntidadMemo $memo, EntidadRevisionesDoc $revisiones)
	{
		try{
			$preMsg = "Error al intentar devolver un documento.";
				
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

	public static  function AnularDocumento(EntidadDocGenera $docGenera, EntidadRevisionesDoc $revisiones)
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



	public static function GetRegistrosEnBandeja($lugar = null)
	{

		$data = array();
		if($idCadena = self::BuscarIdCadenasPerfil($lugar,$_SESSION['user_perfil_id']))
		 

		 
		$query = "
	    		SELECT 
	    			dg.*";

		if($lugar == 'pcta'){

			$query .= "

	    		,pc.pcta_justificacion,
	    	    pc.pcta_asunto
	    		";

		}
		 
		 
		if($lugar == 'pmod'){
			 
			$query .= "
	    		,fo.f030_motivo,
	    	     fo.f030_tipo
	    		";	
		}
		
		if($lugar == 'desi'){
		
			$query .= "
	    		,observaciones
	    		";
		}
		 
		 


		$query .= "	FROM
 					sai_doc_genera dg
					left outer join sai_wfcadena ca on(dg.wfca_id = ca.wfca_id)";
			
		if($lugar == 'pcta'){
			 
			$query .= "  left outer join sai_pcuenta pc on(dg.docg_id = pc.pcta_id)	";

		}
		 
		if($lugar == 'pmod'){
			 
			$query .= "  left outer join sai_forma_0305 fo on(dg.docg_id = fo.f030_id)";

		}
		
		if($lugar == 'desi'){
		
			$query .= "  left outer join sai_desincorporar de on(dg.docg_id = de.acta_id)";
		
		}

		$query .= "  WHERE  ";
		 
		
			$cargo = substr($_SESSION['user_perfil_id'],0,2);

		
		if($lugar == 'pmod' || $lugar == 'pcta' )
		{
			if
			(
			($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
			($cargo !== substr(PERFIL_DIRECTOR,0,2)) &&
			($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
			($cargo !== substr(PERFIL_PRESIDENTE,0,2)) &&
			($cargo !== substr(PERFIL_GERENTE,0,2))	
			)
			{
					
				$query .= "dg.usua_login='".$_SESSION['login']."' AND ";

			}
		
		}
		
		if($lugar == 'ers' || $lugar == 'ne' || $lugar == 'desi')
		{
			if
			(
			($cargo !== substr(PERFIL_COORDINADOR_BIENES,0,2)) &&
			($cargo !== substr(PERFIL_JEFE_BIENES,0,2))
			)
			{
				$query .= "dg.usua_login='".$_SESSION['login']."' AND ";
			}
		}
		
		$query .= "
			  	    dg.esta_id != 15 AND 
			  	    dg.esta_id != 13 AND
					dg.wfob_id_ini != 2 AND
				
			  	 --  dg.perf_id_act =  '".$_SESSION['user_perfil_id']."' AND
			  	  		
			  	   POSITION('".$_SESSION['user_perfil_id']."' IN dg.perf_id_act) > 0 AND
			  	    				
			  	    dg.wfca_id IN ('".implode("', '",array_keys($idCadena))."')
			   
		     ORDER BY 
		     		docg_fecha DESC";
		
		 
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			$data[] = $row;

		}


		return  $data;

		 
	}
	
	public static function GetRegistrosEnTransitoDesincorporacion($lugar)
	{
		$query="
		select
			dg.*,
			de.observaciones
		from
			sai_doc_genera dg
			inner join sai_wfcadena cadena on(cadena.wfca_id=dg.wfca_id)
			left outer join sai_desincorporar de on(dg.docg_id = de.acta_id)
		where
			--dg.perf_id='".$_SESSION['user_perfil_id']."' and
			--dg.perf_id_act <> '".$_SESSION['user_perfil_id']."' and
			 POSITION('".$_SESSION['user_perfil_id']."' IN dg.perf_id_act) = 0 AND
			cadena.docu_id='".$lugar."' and
			cadena.wfca_proyecto=1
		order by dg.docg_fecha DESC
					";
	
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
	
			$data[] = $row;
			$ers[$row['docg_id']] = $row['docg_id'];
	
	
	
		}
	
	
	
		return $data;
	}
	
	public static function GetRegistrosEnviadosSudebip($lugar)
	{
		$query="
		select
			dg.*,
			de.observaciones
		from
			sai_doc_genera dg
			inner join sai_wfcadena cadena on(cadena.wfca_id=dg.wfca_id)
			left outer join sai_desincorporar de on(dg.docg_id = de.acta_id)
		where
			cadena.docu_id='".$lugar."' and
			cadena.wfca_proyecto=1 and
			dg.wfob_id_ini = 2
		order by dg.docg_fecha DESC
					";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
		
			$data[] = $row;
			$ers[$row['docg_id']] = $row['docg_id'];
				
				
		
		}
		
		return $data;
		
	}

	public static function GetRegistrosEnTransitoRespSocial($lugar)
	{
		$query="
		select 
			dg.* 
		from 
			sai_doc_genera dg
			inner join sai_wfcadena cadena on(cadena.wfca_id=dg.wfca_id) 
		where 
			--dg.perf_id='".$_SESSION['user_perfil_id']."' and
			--dg.perf_id_act <> '".$_SESSION['user_perfil_id']."' and
			 POSITION('".$_SESSION['user_perfil_id']."' IN dg.perf_id_act) = 0 AND
			cadena.docu_id='".$lugar."' and
			cadena.wfca_proyecto=1		
					";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
		
			$data[] = $row;
			$ers[$row['docg_id']] = $row['docg_id'];
			
			
		
		}


			  	    
			  	    return $data;
	}
	

	
	
	public static function GetRegistrosEnTransito($lugar)
	{
		$data = array();
		$data2 = array();
		$pcta = array();



		if($idsCadena  =  self::BuscarIdCadenasPerfil($lugar,$_SESSION['user_perfil_id'])){
				

			foreach ($idsCadena AS  $index => $val){



				if($params = self::GetCadenasSigientes($index)){

					if($val == 0){
							


						foreach ($params AS $valor){

							if($condicion == false){

								$condicion = "  AND (( dg.wfca_id = ".$valor['idcadenasigiente'];

							}else{

								if($bandera){
									$condicion .= " OR ( dg.wfca_id = ".$valor['idcadenasigiente'];
									$bandera = false;

								}else{

									$condicion .= " OR dg.wfca_id = ".$valor['idcadenasigiente'];

								}

							}

						}


						$condicion == false ? $condicion =  "" : $condicion .= ")";

						$bandera = true;

					}else{
							

						foreach ($params AS $valor){

							if($condicion == false){
									
								$condicion = "  AND ((( dg.wfca_id = ".$valor['idcadenasigiente'];
									
							}else{
								if($bandera){
									$condicion .= " OR ( dg.wfca_id = ".$valor['idcadenasigiente'];
									$bandera = false;

								}else{

									$condicion .= " OR dg.wfca_id = ".$valor['idcadenasigiente'];

								}

							}

						}

						$condicion == false ? $condicion =  "" : $condicion .= " )AND (docg_id LIKE 'pcta-".substr($_SESSION['user_perfil_id'],2,3)."%'))";
						$bandera = true;


					}
				}

			}
			$condicion == false ? $condicion =  "" : $condicion .= ")";

		}





		$query = "
	    		SELECT 
	    			dg.*";

		if($lugar == 'pcta'){

			$query .= "

	    		,pc.pcta_justificacion,
	    	    pc.pcta_asunto
	    		";

		}
		 
		 
		if($lugar == 'pmod'){
			 
			$query .= "
	    		,fo.f030_motivo,
	    	     fo.f030_tipo
	    		";	
		}
		 
		 


		$query .= "	FROM
 					sai_doc_genera dg
					left outer join sai_wfcadena ca on(dg.wfca_id = ca.wfca_id)";
			
		if($lugar == 'pcta'){
			 
			$query .= "  left outer join sai_pcuenta pc on(dg.docg_id = pc.pcta_id)	";

		}
		 
		if($lugar == 'pmod'){
			 
			$query .= "  left outer join sai_forma_0305 fo on(dg.docg_id = fo.f030_id)";

		}

		$query .= "	   WHERE  ";



		$cargo = substr($_SESSION['user_perfil_id'],0,2);
		 
		if(
		($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
		($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
		($cargo !== substr(PERFIL_DIRECTOR,0,2)) &&
		($cargo !== substr(PERFIL_PRESIDENTE,0,2)) &&
		($cargo !== substr(PERFIL_GERENTE_TECNOLOGIA,0,2))
			
		){
				
			$query .= "	dg.usua_login='".$_SESSION['login']."' AND  ";

		}

		$query .= "

			  	    dg.esta_id != 15 AND
			  	    dg.esta_id != 13 AND
			  	    dg.wfob_id_ini != 99"
			  	    .$condicion."
			  	    

			  	    

		     ORDER BY docg_fecha DESC";

			  	    	


			  	    $result = $GLOBALS['SafiClassDb']->Query($query);
			  	    while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

			  	    	$data[] = $row;
			  	    	$pcta[$row['docg_id']] = $row['docg_id'];

			  	    }


			  	    if($pcta){
			  	    	$params = SafiModeloRevisionesDoc::GetRevisionesDocPerfil($pcta);

			  	    	if($params){

			  	    		foreach($data as $valor){
			  	    				
			  	    			if($params[$valor['docg_id']] === true){
			  	    					
			  	    				$data2[] = $valor;

			  	    			}
			  	    		}
			  	    	}
			  	    }


			  	    return $data2;
			  	    	
			  	    	
	}

	public static function BuscarIdCadenaPerfil($lugar,$perfil){
		 
		$perfil2 = substr ($_SESSION['user_perfil_id'],0,2).'000';


		$query = "
       
              SELECT
       				sc.wfca_id AS wfca_id
		       FROM 
		       		sai_wfcadena sc
		       		INNER JOIN sai_wfgrupo sg ON (sc.wfgr_id = sg.wfgr_id)  
		       WHERE 
		       		sc.docu_id = '".$lugar."' AND 
		       		sc.wfca_proyecto = 1 AND
		       		(POSITION('".$perfil."' IN sg.wfgr_perf) > 0 OR 
				POSITION('".$perfil2."' IN sg.wfgr_perf) > 0) AND
				sc.wfca_id NOT IN (SELECT MIN(sc.wfca_id) FROM sai_wfcadena sc WHERE sc.docu_id = '".$lugar."' AND sc.wfca_proyecto = 1)
                ORDER BY LENGTH(sg.wfgr_perf)
                ";


		$result = $GLOBALS['SafiClassDb']->Query($query);
		if ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				
			return $row['wfca_id'];
	   
		}

		return false;
	}
	 
	 
	public static function BuscarIdCadenasPerfil($lugar,$perfil){

		 
		$perfil2 = substr ($_SESSION['user_perfil_id'],0,2).'000';
		$params = array();
		 

		$query = "
       
              SELECT
       				sc.wfca_id AS wfca_id
		       FROM 
		       		sai_wfcadena sc
		       		INNER JOIN sai_wfgrupo sg ON (sc.wfgr_id = sg.wfgr_id)  
		       WHERE 
		       		sc.docu_id = '".$lugar."' AND 
		       		sc.wfca_proyecto = 1 AND
		       		(POSITION('".$perfil."' IN sg.wfgr_perf) > 0 OR 
				POSITION('".$perfil2."' IN sg.wfgr_perf) > 0) AND
				sc.wfca_id NOT IN (SELECT MIN(sc.wfca_id) FROM sai_wfcadena sc WHERE sc.docu_id = '".$lugar."' AND sc.wfca_proyecto = 1)
                ORDER BY sc.wfca_id asc
                ";


		if( $result = $GLOBALS['SafiClassDb']->Query($query)){
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
					
				$params[$row['wfca_id']] = $row['wfca_id'];
				 
			}
			 
			$data = SafiModeloWFCadena::GetCadenasIdGrupos($params);
			$data2 =  SafiModeloWFGrupo::GetIdPerfilWFGrupoBy($data);

			if($data){

				foreach($data as $index => $valor){

					$cadena = $data2[$valor];
					$caracter = "/";
					 
					if(strpos($cadena, $caracter) !== false){

						$params[$index] = 1;

					}else{
						 
						$params[$index] = 0;
						 
					}
				}
			}


			return  $params;
		}
		return false;
	}

	 
	public static function GetCadenasSigientes($idCadena){

		$data = array();
		$params = array();


		$query ="  SELECT
	                     * 
	                 FROM 
	                     safi_get_grupo_siguiente(".$idCadena.") AS idCadenaSigiente 
	                     
	                 WHERE
	                     idCadenaSigiente !=".$idCadena."
	                     
	                 ORDER BY idCadenaSigiente ASC
	               ";


		$result = $GLOBALS['SafiClassDb']->Query($query);
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

			$data[] = $row;
			 
		}
			

		return $data;
	}
	 
	public static function GetCedulaDocGenera(array $params = null)
	{

		$param =array();
		$query = "
				SELECT
	  				doc_genera.usua_login as docg_usua_login,
	  				doc_genera.docg_id  as id,
	  				em.empl_nombres AS empl_nombres,
                    em.empl_apellidos AS empl_apellidos
	  				 
	  			FROM
					sai_doc_genera doc_genera
					INNER JOIN sai_empleado em ON (em.empl_cedula = doc_genera.usua_login)	
					
				WHERE
					 doc_genera.docg_id IN ('".implode("', '",$params)."')";

		if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false){
				

			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
					
				$param[$row['id']] =  $row['empl_nombres']." ".$row['empl_apellidos'];

			}
			
			
			return $param;

		}else{
				
			
			return false;
		}

	}
}