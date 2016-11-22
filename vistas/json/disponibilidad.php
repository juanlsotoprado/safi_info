<?php 
	header("Content-type: application/json; charset=UTF-8");

	
	  $monto_disp = $GLOBALS['SafiRequestVars']['monto_disp'];
      //error_log(print_r($monto_disp,true));
        
	   echo json_encode($monto_disp);
?>