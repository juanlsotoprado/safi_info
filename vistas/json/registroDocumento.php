<?php 
	header("Content-type: application/json; charset=UTF-8");
	$vista = $GLOBALS['vista'];
	if ($vista ==1) {
		$listaCodigoDocumento = array();
		if(is_array($GLOBALS['SafiRequestVars']['codigoDocumento'])){
			foreach( $GLOBALS['SafiRequestVars']['codigoDocumento'] as $codigoDocumento1 ){
				$codigoDocumento1 = utf8_encode($codigoDocumento1);
				$data = array('idDocumento' => $codigoDocumento1);
				$listaCodigoDocumento[$codigoDocumento1] = $data;
			}
		}
		echo json_encode(array("listaCodigoDocumento" => $listaCodigoDocumento));
	}
	else if ($vista ==2) {
		$data = array(
			'idDocumento' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][0]),
			'beneficiario' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][1]),
			'monto' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][2]),
			'observaciones' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][3]),
			'compromiso' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][4]),
			'fecha' => utf8_encode($GLOBALS['SafiRequestVars']['codigoDocumento'][5])
		);
		echo json_encode(array("listaCodigoDocumento" => $data));
	}