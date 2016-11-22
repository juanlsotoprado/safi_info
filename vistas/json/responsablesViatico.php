<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaResponsables = array();
	$responsables = $GLOBALS['SafiRequestVars']['responsablesViatico'];
	
	if($responsables != null && is_array($responsables) && count($responsables) > 0)
	{
		foreach( $responsables as $responsable )
		{
			if($responsable instanceof EntidadResponsableViatico)
			{
				$responsable->UTF8Encode();
				$listaResponsables[$responsable->GetId()] = $responsable->ToArray();
			}
		}
	}
   
	echo json_encode(array("listaResponsables" => $listaResponsables));
?>