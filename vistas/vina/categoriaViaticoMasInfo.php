<?php 
	$categoriaViaticos = $GLOBALS['SafiRequestVars']['categoriaViaticos'];
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>Categor&iacute;as</title>
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	</head>
	<body class="normal">
		<?php
			if($categoriaViaticos != null && count($categoriaViaticos)>0){
				echo '
					<ul>
				';
				foreach ($categoriaViaticos as $categoria){
					echo '
						<li class="normalNegrita">
							'.$categoria->GetNombre().'
					';
					if($categoria->GetDescripcion() != null && trim($categoria->GetDescripcion()) != ''){
						echo '
							<ul>
								<li class="normal">
									'.trim($categoria->GetDescripcion()).'
								</li>
							</ul>
						';
					}
					echo '
						</li>
					';
					
				}
				echo '
					</ul>
				';
			}
		?>
	</body>
</html>
