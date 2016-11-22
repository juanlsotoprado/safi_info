<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';

	$empleados = $GLOBALS['SafiRequestVars']['empleados'];
	
	echo "<listaempleado>";
	if($empleados != null && is_array($empleados) && count($empleados) > 0){
		foreach($empleados as $empleado){
			echo "
				<empleado>
					<cedula>
						<![CDATA[". utf8_encode($empleado['empl_cedula']) ." ]]>
					</cedula>
					<nombres>
						<![CDATA[". utf8_encode($empleado['empl_nombres']) ." ]]>
					</nombres>
					<apellidos>
						<![CDATA[". utf8_encode($empleado['empl_apellidos']) ." ]]>
					</apellidos>
					<telefonooficina>
						<![CDATA[". utf8_encode($empleado['empl_tlf_ofic']) ." ]]>
					</telefonooficina>
					<nacionalidad>
						<![CDATA[". utf8_encode($empleado['nacionalidad']) ." ]]>
					</nacionalidad>
					<email>
						<![CDATA[". utf8_encode($empleado['empl_email']) ." ]]>
					</email>
					<iddependencia>
						<![CDATA[". utf8_encode($empleado['depe_cosige']) ." ]]>
					</iddependencia>
					<cargo>
						<![CDATA[". utf8_encode($empleado['carg_fundacion']) ." ]]>
					</cargo>
					<observacion>
						<![CDATA[". utf8_encode($empleado['empl_observa']) ." ]]>
					</observacion>
					<logincreador>
						<![CDATA[". utf8_encode($empleado['usua_login']) ." ]]>
					</logincreador>
					<idestatus>
						<![CDATA[". utf8_encode($empleado['esta_id']) ." ]]>
					</idestatus>
					<banconomina>
						<![CDATA[". ( isset($empleado['banco_nomina']) ? utf8_encode($empleado['banco_nomina']) : "" ) ." ]]>
					</banconomina>
					<tipocuentanomina>
						<![CDATA[". ( isset($empleado['tipo_cuenta_nomina']) ? utf8_encode($empleado['tipo_cuenta_nomina']) : "" ) ." ]]>
					</tipocuentanomina>
					<cuentanomina>
						<![CDATA[". ( isset($empleado['cuenta_nomina']) ? utf8_encode($empleado['cuenta_nomina']) : "" ) ." ]]>
					</cuentanomina>
				</empleado>
			";
		}
	}
	echo "</listaempleado>";
?>