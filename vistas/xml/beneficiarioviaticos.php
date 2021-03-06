<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	$beneficiarios = $GLOBALS['SafiRequestVars']['beneficiarioviaticos'];
	
	echo "<listabeneficiarioviatico>";
	if($beneficiarios != null && is_array($beneficiarios) && count($beneficiarios) > 0){
		foreach($beneficiarios as $empleado){
			echo "
				<beneficiarioviatico>
					<cedula>
						<![CDATA[". utf8_encode($empleado['benvi_cedula']) ." ]]>
					</cedula>
					<nombres>
						<![CDATA[". utf8_encode($empleado['benvi_nombres']) ." ]]>
					</nombres>
					<apellidos>
						<![CDATA[". utf8_encode($empleado['benvi_apellidos']) ." ]]>
					</apellidos>
					<nacionalidad>
						<![CDATA[". utf8_encode($empleado['nacionalidad']) ." ]]>
					</nacionalidad>
					<iddependencia>
						<![CDATA[". utf8_encode($empleado['depe_id']) ." ]]>
					</iddependencia>
					<tipo>
						<![CDATA[". utf8_encode($empleado['tipo']) ." ]]>
					</tipo>
					<idestatus>
						<![CDATA[". utf8_encode($empleado['benvi_esta_id']) ." ]]>
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
				</beneficiarioviatico>
			";
		}
	}
	echo "</listabeneficiarioviatico>";
?>