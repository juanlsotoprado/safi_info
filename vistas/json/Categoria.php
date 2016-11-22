<?php 
	header("Content-type: application/json; charset=UTF-8");
	$param= array();

	 $GLOBALS['SafiRequestVars']['categorias'] = $categorias;  
	 
	  if(is_array($categorias)){
       foreach ($categorias as $paramas ){
       	
		$param[] = array(
		     
		    "tipo" => utf8_encode($paramas['tipo']),
			"id_especifica" => utf8_encode($paramas['id_especifica']),
			"id_proy_accion" => utf8_encode($paramas['id_proy_accion']),
		     "proy_titulo" => utf8_encode($paramas['proy_titulo']),
		    "centro" => utf8_encode($paramas['centro']),
			"nombre" => utf8_encode($paramas['nombre'])	
		     
		);	
		
       }

	  }
	echo json_encode($param);