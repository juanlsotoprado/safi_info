<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "<listaestado>";
	foreach($GLOBALS['SafiRequestVars']['estados'] as $estado){
		echo "
			<estado id = '" . $estado['id'] . "'>
				<nombre>
					<![CDATA[". utf8_encode($estado['nombre']) ." ]]>
				</nombre>
			</estado>
		";
	}
	echo "</listaestado>";