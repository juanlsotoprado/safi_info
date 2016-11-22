<?php
	if(
		is_array($GLOBALS['SafiErrors']['general']) && count($GLOBALS['SafiErrors']['general']) > 0
		|| is_array($GLOBALS['SafiInfo']['general']) && count($GLOBALS['SafiInfo']['general']) > 0
	){
		echo '<a name="mensajes"></a>';
	}
	
	if(is_array($GLOBALS['SafiErrors']['general']) && count($GLOBALS['SafiErrors']['general']) > 0){
		echo '<ul class= "mensajeError">';
		foreach($GLOBALS['SafiErrors']['general'] as $error){
			echo '<li>' .$error . '</li>';
		}
		echo '</ul>';
	}
	if(is_array($GLOBALS['SafiInfo']['general']) && count($GLOBALS['SafiInfo']['general']) > 0){
		echo '<ul class= "mensajeInformacion">';
		foreach($GLOBALS['SafiInfo']['general'] as $error){
			echo '<li>' .$error . '</li>';
		}
		echo '</ul>';
	}