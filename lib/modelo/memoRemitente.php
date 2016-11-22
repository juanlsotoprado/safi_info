<?php
include_once(SAFI_ENTIDADES_PATH."/memoRemitente.php");

class SafiModeloMemoRemitente
{
	public static function GuardarMemoRemitente(EntidadMemoRemitente $memoRemitente = null)
	{
		try
		{
			$preMsg = "Error al intentar guardar el memoRemitente.";

			if($memoRemitente === null)
				throw new Exception($preMsg." El parámetro \"memoRemitente\" es nulo.");
			if(($idMemo=$memoRemitente->GetIdMemo()) === null)
				throw new Exception($preMsg." El parámetro \"memoRemitente idMemo es nulo.\"");
			if(($idMemo=trim($idMemo)) == "")
				throw new Exception($preMsg." El parámetro \"memoRemitente\" idMemo está vacío.");
			if(($usuaLoginRemitente=$memoRemitente->GetUsuaLoginRemitente()) === null)
				throw new Exception($preMsg." El parámetro \"memoRemitente usuaLoginRemitente\" es nulo.");
			if(($idEstado=$memoRemitente->GetIdEstado()) === null)
				throw new Exception($preMsg." El parámetro \"memoRemitente idEstado\" es nulo.");
			if(($idEstado=trim($idEstado)) == "")
				throw new Exception($preMsg." El parámetro \"memoRemitente idEstado\" está vacío.");
			if(($fecha=$memoRemitente->GetFecha()) === null)
				throw new Exception($preMsg." El parámetro \"memoRemitente fecha\" es nulo.");
			if(($fecha=trim($fecha)) == "")
				throw new Exception($preMsg." El parámetro \"memoRemitente\" fecha está vacío.");
			
			
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg."Error al iniciar la transacción. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			$query = "
				INSERT INTO sai_memo_rem
					(
						memo_id,
						usua_login,
						esta_id,
						memr_fecha
					)
				VALUES
					(
						'".$idMemo."',
						'".$usuaLoginRemitente."',
						'".$idEstado."',
						to_timestamp('".$fecha."', 'DD/MM/YYYY HH24:MI:SS')
					)
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception($preMsg." Detalles: ".utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
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
}