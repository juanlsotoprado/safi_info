<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	
	$infocentros = $GLOBALS['SafiRequestVars']['infocentros'];
	
	echo "<listainfocentro>";
	
	if($infocentros != null && is_array($infocentros) && count($infocentros) > 0){
		foreach($infocentros as $infocentro){
			echo "
				<infocentro id='" . $infocentro['id'] . "'>
					<nombre>
						<![CDATA[". utf8_encode($infocentro['nombre']) ." ]]>
					</nombre>
					<direccion>
						<![CDATA[". utf8_encode($infocentro['direccion']) ." ]]>
					</direccion>
					<anho>
						". utf8_encode($infocentro['anho']) . "
					</anho>
					<idparroquia>
						". utf8_encode($infocentro['parroquia_id']) ."
					</idparroquia>
					<estatusactividad>
						<![CDATA[". utf8_encode($infocentro['estatus_actividad']) ." ]]>
					</estatusactividad>
					<idestatus>
						". utf8_encode($infocentro['estatus_id']) ."
					</idestatus>
					<nemotecnico>
						<![CDATA[". utf8_encode($infocentro['nemotecnico']) ." ]]>
					</nemotecnico>
					<etapa>
						<![CDATA[". utf8_encode($infocentro['nemotecnico']) ." ]]>
					</etapa>
				</infocentro>
			";
		}
	}
	echo "</listainfocentro>";
?>