<?php 
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<listaParroquia>
	<?php
		foreach($GLOBALS['SafiRequestVars']['parroquias'] as $parroquia){
			echo "<parroquia id='" . $parroquia['id'] . "'>";
			echo "<nombre>";
			echo "<![CDATA[". utf8_encode($parroquia['nombre']) ." ]]>";
			echo "</nombre>";
			echo "</parroquia>";
		}
	?>
</listaParroquia>