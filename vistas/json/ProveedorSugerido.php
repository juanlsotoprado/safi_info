<?php 
	header("Content-type: application/json; charset=UTF-8");

	
   $ProveedorSugerido =	$GLOBALS['SafiRequestVars']['ProveedorSugerido'];
	
   $params = array();
   
      $i = 0;
      
      if($ProveedorSugerido){

       // error_log(print_r($ProveedorSugerido,true));
	foreach ($ProveedorSugerido as $index => $par){
     	
     $params [$i]['id'] = utf8_encode($par['id']);
     $params [$i]['nombre'] = utf8_encode($par['nombre']);
     $params [$i]['tipo'] = utf8_encode($par['tipo']);
      $i++;

     }
     
      }
   
	echo json_encode($params);
	