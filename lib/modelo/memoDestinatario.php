<?php
include_once(SAFI_ENTIDADES_PATH."/memoDestinatario.php");

class SafiModeloMemoDestinatario
{
	public static function GuardarMemoDestinatario(EntidadMemoDestinatario $memoDestinatario = null)
	{
		try
		{
			$preMsg = "Error al intentar guadar el memoDestinatario.";
			
			if($memoDestinatario === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario\" es nulo.");
			if(($idMemo=$memoDestinatario->GetIdMemo()) === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario idMemo\" es nulo.");
			if(($idMemo=trim($idMemo)) == "")
				throw new Exception($preMsg." El parámetro \"memoDestinatario idMemo\" está vacío.");
			if(($usuaLoginDestinatario=$memoDestinatario->GetUsuaLoginDestinatario()) === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario usuaLoginDestinatario\" es nulo.");
			if(($usuaLoginDestinatario=trim($usuaLoginDestinatario)) == "")
				throw new Exception($preMsg." El parámetro \"memoDestinatario usuaLoginDestinatario\" está vacío.");
			if(($tipoDestinatario=$memoDestinatario->GetTipoDestinatario()) === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario tipoDestinatario\" es nulo.");
			if(($tipoDestinatario=trim($tipoDestinatario)) == "")
				throw new Exception($preMsg." El parámetro \"memoDestinatario tipoDestinatario\" está vacío.");
			if(($fecha=$memoDestinatario->GetFecha()) === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario fecha\" es nulo.");
			if(($fecha=trim($fecha)) == "")
				throw new Exception($preMsg." El parámetro \"memoDestinatario fecha\" está vacío.");
			if(($usuaLoginRemitente=$memoDestinatario->GetUsuaLoginRemitente()) === null)
				throw new Exception($preMsg." El parámetro \"memoDestinatario usuaLoginRemitente\" es nulo.");
			if(($usuaLoginRemitente=trim($usuaLoginRemitente)) == "")
				throw new Exception($preMsg." El parámetro \"memoDestinatario usuaLoginRemitente\" está vacío.");
				
			// Iniciar la transacción
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception($preMsg."Error al iniciar la transacción. Detalles: "
					.utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
					
			// Guardar en la tabla sai_memo_desti
			$query = "
				INSERT INTO  sai_memo_desti
					(
						memo_id,
						usua_login,
						memd_desti_tp,
						memd_fecha,
						usua_login_remi,
						memd_reci_anex,
						esta_id
					)
				VALUES
					(
						'".$idMemo."',
						'".$usuaLoginDestinatario."',
						'".$tipoDestinatario."',
						to_timestamp('".$fecha."', 'DD/MM/YYYY HH24:MI:SS'),
						'".$usuaLoginRemitente."',
						".($memoDestinatario->GetRecibioAnexo() !== null && trim($memoDestinatario->GetRecibioAnexo()) != ""
							? "'".trim($memoDestinatario->GetRecibioAnexo()."'") : "NULL").",
						".($memoDestinatario->GetIdEstado() !== null && trim($memoDestinatario->GetIdEstado()) != ""
							? "'".trim($memoDestinatario->GetIdEstado()."'") : "NULL")."
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