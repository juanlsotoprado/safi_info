<?php
include_once(SAFI_ENTIDADES_PATH . '/documentoSoporte.php');

class SafiModeloDocumentoSoporte
{	
	public static function GuardarDocumentoSoporte(EntidadDocumentoSoporte $documentoSoporte = null)
	{
		try
		{
			$preMsg = "Error al intentar guardar el documentoSoporte.";
			
			if($documentoSoporte === null)
				throw new Exception($preMsg." El parámetro \"documentoSoporte\" es nulo.");
			if(($idDocumentoFuente=$documentoSoporte->GetIdDocumentoFuete()) === null)
				throw new Exception($preMsg." El parámetro \"documentoSoporte idDocumentoFuente\" es nulo.");
			if(($idDocumentoFuente=trim($idDocumentoFuente)) == "")
				throw new Exception($preMsg." El parámetro \"documentoSoporte idDocumentoFuente\" está vacío.");
			if(($idsDocumentosSoportes=$documentoSoporte->GetIdsDocumentosSoportes()) === null)
				throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes\" es nulo.");
			if(!is_array($idsDocumentosSoportes))
				throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes\" no es un arreglo.");
			if(count($idsDocumentosSoportes) == 0)
				throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes\" está vacío.");
			
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg."Error al iniciar la transacción. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$index = 0;
			foreach ($idsDocumentosSoportes AS $idDocumentoSoporte)
			{
				if($idDocumentoSoporte === null)
					throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes[".$index."]\" es nulo.");
				if(($idDocumentoSoporte=trim($idDocumentoSoporte)) == "")
					throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes[".$index."]\" está vacío.");
				
				$query = "
					INSERT INTO sai_docu_sopor
						(
							doso_doc_fuente,
							doso_doc_soport
						)
					VALUES
						(
							'".$idDocumentoFuente."',
							'".$idDocumentoSoporte."'
						)
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception($preMsg." El parámetro \"documentoSoporte idsDocumentosSoportes[".$index
						."]\" no pudo ser guardado. Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
				$index++;
			}
			
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception($preMsg." No se pudo realizar el commit. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			return true;
		}
		catch(Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true) $GLOBALS['SafiClassDb']->RollbackTransaction();
			error_log($e, 0);
			return false;
		}
	}
	public static function GetDocumentoSoporte(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener los documentos de soporte.";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			$documentoSoporte = null;
			$idsDocumentosSoportes = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			if(!isset($params['idDocumentoFuente']))
				$arrMsg[] = "El parámetro params['idDocumentoFuente'] no pudo ser encontrado.";
			else if(($idDocumentoFuente=$params['idDocumentoFuente']) == null)
				$arrMsg[] = "El parámetro params['idDocumentoFuente'] es nulo.";
			else if(($idDocumentoFuente=trim($idDocumentoFuente)) == '')
				$arrMsg[] = "El parámetro params['idDocumentoFuente'] está vacío.";
			else {
				$existeCriterio = true;
				$queryWhere = "LOWER(documento_soporte.doso_doc_fuente) = '".mb_strtolower($idDocumentoFuente, "ISO-8859-1")."'";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
				
			$query = "
				SELECT
					documento_soporte.doso_doc_fuente AS documento_fuente,
					documento_soporte.doso_doc_soport AS documento_soporte
				FROM
					sai_docu_sopor documento_soporte
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				if($documentoSoporte === null){
					$documentoSoporte = new EntidadDocumentoSoporte();
					$documentoSoporte->SetIdDocumentoFuente($row['documento_fuente']);
					
					$idsDocumentosSoportes = array();
				}
				
				$idsDocumentosSoportes[] = $row['documento_soporte'];
			}
			
			if($documentoSoporte !== null)
				$documentoSoporte->SetIdsDocumentosSoportes($idsDocumentosSoportes);
			
			return $documentoSoporte;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
}