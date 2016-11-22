<?php 
	header("Content-type: application/json; charset=UTF-8");

	$params = array();
	 
   //  error_log(print_r($GLOBALS['SafiRequestVars']['puntosCuentaImputa'],true));
   
		if($GLOBALS['SafiRequestVars']['puntosCuentaImputa']){
			
			 $listaPuntoCuenta[0]['puntoCuenta']['puntoCuentaImputa'] = $GLOBALS['SafiRequestVars']['puntosCuentaImputa'];
			 
		}
		
        //  error_log(print_r($listaPuntoCuenta,true));
          
	

	 echo json_encode($listaPuntoCuenta);
?>