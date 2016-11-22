<?php 
	header("Content-type: application/json; charset=UTF-8");

	$params = array();
	 

		if($puntoCuentaFiltro  =  $GLOBALS['SafiRequestVars']['puntoCuentaFiltro']){

			foreach ($puntoCuentaFiltro as $index => $valor){

	        $valor->UTF8Encode();
			
			
		//	 error_log(print_r($valor,true));
		
	        
			
			
		    $listaPuntoCuenta[$index]['puntoCuenta'] = $valor->ToArray();
		    $listaPuntoCuenta[$index]['instActual'] = $GLOBALS['SafiRequestVars']['instActual'][$index];
		    
				
			}
			  
			  	  
        
		}

	   echo json_encode($listaPuntoCuenta);
?>