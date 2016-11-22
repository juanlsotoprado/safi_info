<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaproyecto = array();
	
	foreach( $GLOBALS['SafiRequestVars']['proyectos'] as $proyecto ){
		$listaproyecto[] = array(
			"id" => array(
				"idproyecto" => utf8_encode($proyecto['proy_id']),
				"anhopresupuesto" => utf8_encode($proyecto['pre_anno'])
			),
			"nombre" => utf8_encode($proyecto['proy_titulo']),
			"descripcion" => utf8_encode($proyecto['proy_desc']),
			"resultado" => utf8_encode($proyecto['proy_resultado']),
			"objetivos" => utf8_encode($proyecto['proy_obj']),
			"anhopresupuesto" => utf8_encode($proyecto['pre_anno']),
			"idestatus" => utf8_encode($proyecto['esta_id']),
			"observacion" => utf8_encode($proyecto['proy_observa']),
			"loginregistrador" => utf8_encode($proyecto['usua_login']),
			"loginresponsable" => utf8_encode($proyecto['usua_log_resp']),
			"codigoonapre" => utf8_encode($proyecto['proy_cod_onapre'])
		);	
	}
	
	echo json_encode(array("listaproyecto" => $listaproyecto));