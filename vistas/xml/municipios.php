<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<listaMunicipio>
	<?php
		foreach($GLOBALS['SafiRequestVars']['municipios'] as $municipio){
			echo "<municipio id='" . $municipio['id'] . "'>";
			echo "<nombre>";
			echo "<![CDATA[". utf8_encode($municipio['nombre']) ." ]]>";
			echo "</nombre>";
			echo "</municipio>";
		}
	?>
	
	
</listaMunicipio>