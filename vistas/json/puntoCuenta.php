<?php 
	header("Content-type: application/json; charset=UTF-8");

	$params = array();
	 
   
		if($GLOBALS['SafiRequestVars']['puntosCuenta']){
			

			
			  $puntoCuenta  = $GLOBALS['SafiRequestVars']['puntosCuenta'];
			  
			  		
         //	error_log(print_r($puntoCuenta,true));

			  $puntoCuenta->UTF8Encode();
			  

			  $listaPuntoCuenta[0]['puntoCuenta'] = $puntoCuenta->ToArray();
			  
			   $listaPuntoCuenta[0]['puntoCuenta']['revisiones'] = $GLOBALS['SafiRequestVars']['revisiones'];
			   
			  if(strpos($listaPuntoCuenta[0]['puntoCuenta']['destinatario'],'/') == false){
			  
		      $destinatario = SafiModeloCargo::GetCargoByEmpleado($listaPuntoCuenta[0]['puntoCuenta']['destinatario']);
		 
		      $listaPuntoCuenta[0]['puntoCuenta']['destinatario'] = $destinatario->GetNombre();
		      
			  }else{
			  	
			  $lista = explode('/',$listaPuntoCuenta[0]['puntoCuenta']['destinatario']);
			  $destinatario = SafiModeloCargo::GetCargoByEmpleado($lista[0]);
			  
			  $destinatario1 = $destinatario->GetNombre();
			  
			  $destinatario = SafiModeloCargo::GetCargoByEmpleado($lista[1]);
			  
			  $destinatario2 = $destinatario->GetNombre();

              $listaPuntoCuenta[0]['puntoCuenta']['destinatario'] =  $destinatario1."/".$destinatario2;
            
			  }
			  
			  
			  
			 $listaPuntoCuenta[0]['puntoCuenta']['observacionesDoc'] =  $GLOBALS['SafiRequestVars']['observacionesDoc'];
			 
			  $listaPuntoCuenta[0]['puntoCuenta']['alcances'] = $GLOBALS['SafiRequestVars']['alcances'] != false? $GLOBALS['SafiRequestVars']['alcances'] : '' ;
			  
			 $listaPuntoCuenta[0]['puntoCuenta']['devueltoFinalizado'] = $GLOBALS['SafiRequestVars']['devueltoFinalizado'];
			 
		}
		
         // error_log(print_r($listaPuntoCuenta,true));
	
	 
		
		
	   echo json_encode($listaPuntoCuenta);
?>