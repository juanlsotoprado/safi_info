<?php
include_once(SAFI_ENTIDADES_PATH . '/revisionesdoc.php');

class SafiModeloRevisionesDoc
{
	
	
public static function InsertarRevisionesDoc($revisionesDoc)
	{
		if($revisionesDoc instanceof EntidadRevisionesDoc){
			
			$query = "
				INSERT INTO
					sai_revisiones_doc
					(
						revi_doc,
						usua_login,
						perf_id,
						revi_fecha,
						wfop_id,
						revi_firma
					)
				VALUES
					(
					
		
						trim('".$revisionesDoc->GetIdDocumento()."'),
						'".$revisionesDoc->GetLoginUsuario()."',
						'".$revisionesDoc->GetIdPerfil()."',
						now(),
						".$revisionesDoc->GetIdWFOpcion().",
						'".$revisionesDoc->GetFirmaRevision()."'
					
					)
			";

			if($GLOBALS['SafiClassDb']->Query($query) === false){
				echo 'Error al insertar. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg();
				return false;
			} else {
				return true;
			}
				
		}
		
	
		return false;
	}
	
	
	public static function GetRevisionesDocPerfil(array $params = null)
	{
	
		$data =array();
		
	$query = "	
		SELECT
			revi_doc AS revi_doc
			
		FROM
		    sai_revisiones_doc
					
		WHERE
		   revi_doc IN ('".implode("', '",$params)."')

	   GROUP BY 
	       revi_doc ";
		   
	
	$result = $GLOBALS['SafiClassDb']->Query($query);
	while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
	
	$data[$row['revi_doc']] = true;
	
	}

	return $data;

	}
	
	
	
	public static function GetRevisionesDoc(array $params = null)
	{
		try
		{
			$preMsg = "Error al intentar obtener las revisiones de documentos (revisionesDoc).";
			$arrMsg = array();
			$queryWhere = "";
			$existeCriterio = false;
			$arrRevisionesDoc = null;
			
			if($params == null)
				throw new Exception($preMsg." El parámetro params es nulo.");
				
			if(!is_array($params))
				throw new Exception($preMsg." El parámetro params no es un arreglo.");
				
			if(count($params) == 0)
				throw new Exception($preMsg." El parámetro params está vacío.");
				
			if(!isset($params['idDocumento']))
				$arrMsg[] = "El parámetro params['idDocumento'] no pudo ser encontrado.";
			else if(($idDocumento=$params['idDocumento']) == null)
				$arrMsg[] = "El parámetro params['idDocumento'] es nulo.";
			else if(($idDocumento=trim($idDocumento)) == '')
				$arrMsg[] = "El parámetro params['idDocumento'] está vacío.";
			else {
				$existeCriterio = true;
				$queryWhere = "LOWER(revisiones_doc.revi_doc) = '".mb_strtolower($idDocumento, "ISO-8859-1")."'";
			}
			
			if(!$existeCriterio)
				throw new Exception($preMsg." No existe nigún criterio de búsqueda. Detalles:\n  - " .implode("\n  - ", $arrMsg)."\n");
			
			$query = "
				SELECT
					revisiones_doc.revi_id AS revisiones_doc_id,
					revisiones_doc.revi_doc AS revisiones_doc_documento_id,
					revisiones_doc.usua_login AS revisiones_doc_usua_login,
					revisiones_doc.perf_id AS revisiones_doc_perfil_id,
					to_char(revisiones_doc.revi_fecha, 'DD/MM/YYYY HH24:MI:SS') AS revisiones_doc_fecha_revision,
					revisiones_doc.wfop_id AS revisiones_doc_wfopcion_id,
					revisiones_doc.revi_firma AS revisiones_doc_firma_revision
				FROM
					sai_revisiones_doc revisiones_doc
				WHERE
					".$queryWhere."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$arrRevisionesDoc = array();	
				
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$arrRevisionesDoc[$row['revisiones_doc_id']] = self::LlenarRevisionesDoc($row);
			}
			
			return $arrRevisionesDoc;
		}
		catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function LlenarRevisionesDoc($row)
	{
		$revisionesDoc = new EntidadRevisionesDoc();
		$revisionesDoc->SetId($row['revisiones_doc_id']);
		$revisionesDoc->SetIdDocumento($row['revisiones_doc_documento_id']);
		$revisionesDoc->SetLoginUsuario($row['revisiones_doc_usua_login']);
		$revisionesDoc->SetIdPerfil($row['revisiones_doc_perfil_id']);
		$revisionesDoc->SetFechaRevision($row['revisiones_doc_fecha_revision']);
		$revisionesDoc->SetIdWFOpcion($row['revisiones_doc_wfopcion_id']);
		$revisionesDoc->SetFirmaRevision($row['revisiones_doc_firma_revision']);
		
		return $revisionesDoc;
	}
}