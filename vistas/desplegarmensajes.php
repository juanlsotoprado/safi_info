<html>
	<head>
		<title>.:SAFI:. Mensajes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
	</head>
	
	<body>
	<?php
		$anyMessage = false;
	
		if(is_array($GLOBALS['SafiErrors']['general']) && count($GLOBALS['SafiErrors']['general']) > 0){
			$anyMessage = true;
		}
		if(is_array($GLOBALS['SafiInfo']['general']) && count($GLOBALS['SafiInfo']['general']) > 0){
			$anyMessage = true;
		}
		
		if($anyMessage == false){
			echo '
			<ul class= "mensajeError">
				<li>
					'."Se ha producido un error. P&oacute;ngase en contacto con el administrador del sistema.".'
				</li>
			</ul>	
			';
		} else {
			include(SAFI_VISTA_PATH . "/mensajes.php");
		}
	?>		
	</body>
</html>

