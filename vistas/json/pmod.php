<?php
header("Content-type: application/json; charset=UTF-8");

$Mpresupuestaria = array();

if($Pmod  = $GLOBALS['SafiRequestVars']['pmod']){
	
	
	
	$Pmod->UTF8Encode();
	
	$Mpresupuestaria['pmod'] =  $Pmod->ToArray();
    $Mpresupuestaria['pmod']['revisiones'] = $GLOBALS['SafiRequestVars']['revisiones'];
	$Mpresupuestaria['pmod']['observacionesDoc'] =  $GLOBALS['SafiRequestVars']['observacionesDoc'];

}
  // error_log(print_r($Mpresupuestaria['pmod'],true));
  
	//  error_log(print_r($pmod,true));

  echo json_encode($Mpresupuestaria);
?>