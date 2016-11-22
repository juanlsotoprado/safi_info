<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaAccionEspecifica = array();
	
	foreach( $GLOBALS['SafiRequestVars']['proyectoAccionesEspecificas'] as $accionEspecifica ){
		$listaAccionEspecifica[] = array(
			"id" => array(
				"idproyecto" => utf8_encode($accionEspecifica['proy_id']),
				"idaccionespecifica" => utf8_encode($accionEspecifica['paes_id']),
				"anhopresupuesto" => utf8_encode($accionEspecifica['pres_anno'])
			),
			"nombre" => utf8_encode($accionEspecifica['paes_nombre']),
			"anhopresupuesto" => utf8_encode($accionEspecifica['pres_anno']),
			"fechainicio" => utf8_encode($accionEspecifica['paes_fecha_ini']),
			"fechafin" => utf8_encode($accionEspecifica['paes_fecha_fin']),
			"centrogestor" => utf8_encode($accionEspecifica['centro_gestor']),
			"centrocosto" => utf8_encode($accionEspecifica['centro_costo'])
		);	
	}
	
	echo json_encode(array("listaaccionespecifica" => $listaAccionEspecifica));
