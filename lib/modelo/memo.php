<?php
include_once (SAFI_ENTIDADES_PATH . '/memo.php');

include_once (SAFI_MODELO_PATH . '/historialAccion.php');
include_once (SAFI_MODELO_PATH . '/memoDestinatario.php');
include_once (SAFI_MODELO_PATH . '/memoRemitente.php');
include_once (SAFI_MODELO_PATH . '/documentoSoporte.php');

class SafiModeloMemo
{
	public static function GuardarMemo(EntidadMemo $memo = null, $idDocumento = null)
	{
		try
		{
			$preMsg = "Error al intentar guardar el memo.";
			
			if($memo == null)
				throw new Exception($preMsg." El parámetro memo es nulo.");
			if(($loginUsuario=$memo->GetLoginUsuario()) === null)
				throw new Exception($preMsg. " El parámetro \"memo loginUsuario (solicitante)\" es nulo.");
			if(($loginUsuario=trim($loginUsuario)) == "")
				throw new Exception($preMsg. " El parámetro \"memo loginUsuario (solicitante)\" está vacío.");
			if(($idDependencia=$memo->GetIdDependencia()) === null)
				throw new Exception($preMsg. " El parámetro \"memo idDependencia\" es nulo.");
			if(($idDependencia=trim($idDependencia)) == "")
				throw new Exception($preMsg. " El parámetro \"memo idDependencia\" está vacío.");
			if(($contenido=$memo->GetContenido()) === null)
				throw new Exception($preMsg. " El parámetro \"memo contenido\" es nulo.");				
			if(($contenido=trim($contenido)) == "")
				throw new Exception($preMsg. " El parámetro \"memo contenido\" está vacío.");
			if(($asunto=$memo->GetAsunto()) === null)
				throw new Exception($preMsg. " El parámetro \"memo asusto\" es nulo.");
			if(($asunto=trim($asunto)) == "")
				throw new Exception($preMsg. " El parámetro \"memo asuto\" está vacío.");
			if(($revision=$memo->GetRevision()) === null)
				throw new Exception($preMsg. " El parámetro \"memo revision\" es nulo.");
			if(($revision=trim($revision)) == "")
				throw new Exception($preMsg. " El parámetro \"memo revision\" está vacío.");
			if(($publicar=$memo->GetPublicar()) === null)
				throw new Exception($preMsg. " El parámetro \"memo publicar\" es nulo.");
			if(($publicar=trim($publicar)) == "")
				throw new Exception($preMsg. " El parámetro \"memo publicar\" está vacío.");
			if(($firmado=$memo->GetFirmado()) === null)
				throw new Exception($preMsg. " El parámetro \"memo firmado\" es nulo.");
			if(($firmado=trim($firmado)) == "")
				throw new Exception($preMsg. " El parámetro \"memo firmado\" está vacío.");
			if(($fechaCreacion=$memo->GetFechaCreacion()) === null)
				throw new Exception($preMsg. " El parámetro \"memo fechaCreacion\" es nulo.");
			if(($fechaCreacion=trim($fechaCreacion)) == "")
				throw new Exception($preMsg. " El parámetro \"memo fechaCreacion\" está vacío.");
			if($idDocumento === null)
				throw new Exception($preMsg." El parámetro \"\idDocumento\" es nulo.");
			if(($idDocumento=trim($idDocumento)) == "")
				throw new Exception($preMsg." El parámetro \"\idDocumento\" está vacío.");
				
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg."Error al iniciar la transacción. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			// Generar el id del documento memo
			$query = "
				SELECT
					sai_generar_codigo(
							'memo',
							'".$idDependencia."',
							'memo_fecha_crea',
							'memo_id'
					)
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." No se puede generar el id del memo. Detalles: "
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			if (!($idMemo = $GLOBALS['SafiClassDb']->FetchOne($result)))
				throw new Exception($preMsg." No se puede generar el id del memo. Detalles: "
					. utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Guardar en la table sai_memo
			$query  = "
				INSERT INTO sai_memo
					(
						memo_id,
						usua_login,
						memo_asunto,
						memo_contenido,
						memo_revision,
						memo_publicar,
						memo_firmado,
						memo_fecha_crea,
						memo_memo_padre,
						memo_num,
						memo_valida_id,
						memo_grupo,
						memo_grupo_deta,
						depe_id
					)
				VALUES
					(
						'".$idMemo."',
						'".$loginUsuario."',
						'".$GLOBALS['SafiClassDb']->Quote($asunto)."',
						'".$GLOBALS['SafiClassDb']->Quote($contenido)."',
						'".$revision."',
						'".$publicar."',
						'".$firmado."',
						to_timestamp('".$fechaCreacion."', 'DD/MM/YYYY HH24:MI:SS'),
						".($memo->GetPadre() !== null && trim($memo->GetPadre()) != "" ? "'".trim($memo->GetPadre())."'" : "NULL").",
						".($memo->GetNumero() !== null && trim($memo->GetNumero()) != "" ? "'".trim($memo->GetNumero())."'" : "NULL").",
						".($memo->GetIdValida() !== null && trim($memo->GetIdValida()) != ""
							? "'".trim($memo->GetIdValida())."'" : "NULL").",
						".($memo->GetGrupo() !== null && trim($memo->GetGrupo()) != "" ? "'".trim($memo->GetGrupo())."'" : "NULL").",
						".($memo->GetGrupoDeta() !== null && trim($memo->GetGrupoDeta()) != ""
							? "'".trim($memo->GetGrupoDeta())."'" : "NULL").",
						'".$idDependencia."'
					)
			";
			
			if($GLOBALS['SafiClassDb']->Query($query) === false)
					throw new Exception($preMsg." Detalles: ". utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Buscar los usuarios que generaron acciones sobre el documento
			$historialAcccionesUsuaLogins = SafiModeloHistorialAccion::GetUsuaLoginsByIdDocumento($idDocumento);
			
			if($historialAcccionesUsuaLogins === false)
				throw new Exception($preMsg." No se pudieron obtener los usuarios que generaron acciones sobre el documento.");
					
			// Guardar los destinatarios del memo (Basado en los usaurios que generaron acciones sobre el memo)
			if(is_array($historialAcccionesUsuaLogins))
			{
				foreach ($historialAcccionesUsuaLogins As $usuaLoginDestinatario)
				{
					$memoDestinatario = new EntidadMemoDestinatario();
					
					$memoDestinatario->SetIdMemo($idMemo);
					$memoDestinatario->SetUsuaLoginDestinatario($usuaLoginDestinatario);
					$memoDestinatario->SetFecha($fechaCreacion);
					$memoDestinatario->SetUsuaLoginRemitente($loginUsuario);
					
					if(SafiModeloMemoDestinatario::GuardarMemoDestinatario($memoDestinatario) === false)
						throw new Exception($preMsg." No se pudo guardar el destiantario del memo \"".$usuaLoginDestinatario."\".");
				}
			}
			
			// Guarda el remitente del memo
			$memoRemitente = new EntidadMemoRemitente();
			$memoRemitente->SetIdMemo($idMemo);
			$memoRemitente->SetUsuaLoginRemitente($loginUsuario);
			$memoRemitente->SetIdEstado(3);
			$memoRemitente->SetFecha($fechaCreacion);
			
			if(SafiModeloMemoRemitente::GuardarMemoRemitente($memoRemitente) === false)
				throw new Exception($preMsg." No de pudo guardar el remitente del memo \"".$loginUsuario."\"");
				
			// Guarda la relación entre el documento funte y el memo
			$documentoSoporte = new EntidadDocumentoSoporte();
			$documentoSoporte->SetIdDocumentoFuente($idDocumento);
			$documentoSoporte->SetIdsDocumentosSoportes(array($idMemo));
			
			if(SafiModeloDocumentoSoporte::GuardarDocumentoSoporte($documentoSoporte) === false)
				throw new Exception($preMsg." No se pudo guardar el documento soporte.");
				
			// Borrar
			//throw new Exception($preMsg. "\n\n\n\n Todo fino \n\n\n\n");
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			if($result === false)
				throw new Exception($preMsg." No se pudo realizar el commit. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			return $idMemo;
		}
		catch(Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true) $GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetMemos(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los memos.";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			$memos = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			if(!isset($params['idsMemos']))
				$arrMsg[] = "El parámetro params['idsMemos'] no pudo ser encontrado.";
			else if(($idsMemos=$params['idsMemos']) == null)
				$arrMsg[] = "El parámetro params['idsMemos'] es nulo.";
			else if(!is_array($idsMemos))
				$arrMsg[] = "El parámetro params['idsMemos'] no es un arreglo.";
			else if(count($idsMemos) == 0)
				$arrMsg[] = "El parámetro params['idsMemos'] está vacío.";
			else {
				$existeCriterio = true;
				$queryWhere = "memo.memo_id IN ('".implode("', '", $idsMemos)."')";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					memo.memo_id,
					memo.usua_login AS memo_usua_login,
					memo.memo_asunto,
					memo.memo_contenido,
					memo.memo_revision,
					memo.memo_publicar,
					memo.memo_firmado,
					to_char(memo.memo_fecha_crea, 'DD/MM/YYYY HH24:MI:SS') AS memo_fecha_crea,
					memo.memo_memo_padre,
					memo.memo_num,
					memo.memo_valida_id,
					memo.memo_grupo,
					memo.memo_grupo_deta,
					memo.depe_id AS memo_depe_id
				FROM
					sai_memo memo
				WHERE
					".$queryWhere."
				ORDER BY
					memo.memo_fecha_crea
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg() . " -- " . $query));
			
			$memos = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$memos[$row['memo_id']] = self::LlenarMemo($row);
			}
			
			return $memos;
			
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function LlenarMemo($row)
	{
		$memo = new EntidadMemo();
		
		$memo->SetId($row['memo_id']);
		$memo->SetLoginUsuario($row['memo_usua_login']);
		$memo->SetAsunto($row['memo_asunto']);
		$memo->SetContenido($row['memo_contenido']);
		$memo->SetRevision($row['memo_revision']);
		$memo->SetPublicar($row['memo_publicar']);
		$memo->SetFirmado($row['memo_firmado']);
		$memo->SetFechaCreacion($row['memo_fecha_crea']);
		$memo->SetPadre($row['memo_memo_padre']);
		$memo->SetNumero($row['memo_num']);
		$memo->SetIdValida($row['memo_valida_id']);
		$memo->SetGrupo($row['memo_grupo']);
		$memo->SetGrupoDeta($row['memo_grupo_deta']);
		$memo->SetIdDependencia($row['memo_depe_id']);
		
		return $memo;
	}
}