<?php 
	header("Content-type: application/json; charset=UTF-8");
	
	$listaEmpleado = array();
	$empleados = $GLOBALS['SafiRequestVars']['empleados'];
	if($empleados != null && is_array($empleados) && count($empleados) > 0){
		foreach( $GLOBALS['SafiRequestVars']['empleados'] as $empleado ){
			$listaEmpleado[trim(utf8_encode($empleado['empl_cedula']))] = array(
				"cedula" => trim(utf8_encode($empleado['empl_cedula'])),
				"nombres" => trim(utf8_encode($empleado['empl_nombres'])),
				"apellidos" => trim(utf8_encode($empleado['empl_apellidos'])),
				"telefonooficina" => utf8_encode($empleado['empl_tlf_ofic']),
				"nacionalidad" => utf8_encode($empleado['nacionalidad']),
				"email" => utf8_encode($empleado['empl_email']),
				"iddependencia" => utf8_encode($empleado['depe_cosige']),
				"cargo" => utf8_encode($empleado['carg_fundacion']),
				"observacion" => utf8_encode($empleado['empl_observa']),
				"loginregistrador" => utf8_encode($empleado['usua_login']),
				"idestatus" => utf8_encode($empleado['esta_id'])
			);	
		}
	}
	
	echo json_encode(array("listaempleado" => $listaEmpleado));