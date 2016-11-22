<?php
   /* 
    @AUTOR: Christian Polychroniadis
	@FECHA: 22/05/2006
	@DESCRIPCION: "Convierte un arreglo de php, en uno de postgres"
	@PARAMETROS: "arreglo de php"
	@VALOR DEVUELTO: "cadena tipo string, en formato arreglo postgres"
	
	@MODIFICADO: Pedro Hernández
	@FECHA: 18/11/2009
	@MOTIVO: Se arregló el formato del codigo fuente de la función convierte_arreglo, entre otras cosas. Se agregaron otras funciones.
    */

function convierte_arreglo($arr_php){
	$num=sizeof($arr_php);
	if (is_array($arr_php) && $num>0){
		for($i = 0; $i < $num; $i++){
			$cadena .= "\"".$arr_php[$i]."\"";
			if($i < $num-1){
				$cadena .= ',';
			}
		}
		$cadena = '{'.$cadena.'}';
	}else{
		$cadena=""; 
	}	 	  
	return $cadena; 
}

function convierte_arreglo_numeros($arr_php){
	$num=sizeof($arr_php);
	if (is_array($arr_php) && $num>0){
		for($i = 0; $i < $num; $i++){
			if($arr_php[$i]!="" && $arr_php[$i]!=0){
				$cadena .= "\"".$arr_php[$i]."\"";
			} else {
				$cadena .= "\"0\"";
			}
			if($i < $num-1){
				$cadena .= ',';
			}
		}
		$cadena = '{'.$cadena.'}';
	}else{
		$cadena="";
	}
	return $cadena;
}

function matrizACadena($matriz){
	$num=sizeof($matriz);
	if (is_array($matriz) && $num>0){
		for($i = 0; $i < $num; $i++){
			$arreglo = $matriz[$i];
			$tamanoArreglo = sizeof($arreglo);
			for($j = 0; $j < $tamanoArreglo; $j++){
				if($arreglo[$j]!='' && $arreglo[$j]!=0){
					$cadena .= "\"".$arreglo[$j]."\"";
				} else {
					$cadena .= "\"0\"";						
				}
				if($j < $tamanoArreglo-1){
					$cadena .= ',';
				}
			}
			if($i < $num-1 && sizeof($matriz[$i+1])>0){
				if($cadena!=''){
					$cadena .= ',';
				}
			}
		}
		$cadena = '{'.$cadena.'}';
	}else{
		$cadena=""; 
	}
	return $cadena;
}
/*function matrizACadena($matriz){
	$num=sizeof($matriz);
	$cadena='{';
	if (is_array($matriz) && $num>0){
		for($i = 0; $i < $num; $i++){
			$arreglo = $matriz[$i];
			$tamanoArreglo = sizeof($arreglo);
			$cadena.="{";
			for($j = 0; $j < $tamanoArreglo; $j++){
				$cadena .= $arreglo[$j];
				if($j < $tamanoArreglo-1){
					$cadena .= ',';
				}else{
					$cadena .= "}";
				}
			}	
			if($i < $num-1){
				$cadena .= ',';
			}else{
				$cadena .= '}';
			}
		}
	}else{
		$cadena=""; 
	}	 	  
	return $cadena; 
}*/
?>