<?php 
	header("Content-type: application/json; charset=UTF-8");
	if($GLOBALS['SafiRequestVars']['compImputa']){
			 $listaCompImputa = $GLOBALS['SafiRequestVars']['compImputa'];
	}

	 echo json_encode($listaCompImputa);

?>