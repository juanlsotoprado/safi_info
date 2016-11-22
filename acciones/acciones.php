<?php

include_once(SAFI_FORMULARIOS_PATH . '/formmanager.php');

class Acciones
{
	public function __construct()
	{
		try
		{
			/*
			 * Uno de los errores que puede hacer que $_REQUEST["accion"] no se haya establecido
			 * es el hecho de que el archivo del informe exceda el tamaño establecido por las variables
			 * post_max_size o max_file_uploads en el php.ini. Refieráse al log de errores del php
			 * para verificar si esto fue la causa del error. La solución es aumentar el valor de esas
			 * variables.  
			 */
			if(!isset($_REQUEST["accion"]) || ($accion=trim($_REQUEST["accion"])) == '')
				throw new Exception('No se ha seleccionado ninguna acci&oacute;n');
			
			if(!method_exists($this, $accion))
				throw new Exception( sprintf("Acci&oacute;n \"%s\" no definida: ", $accion));
			
			$method = new ReflectionMethod($this, $accion);
			if(!$method->isPublic())
				throw new Exception( sprintf("Acceso denegado a la acci&oacute;n: \"%s\"", $accion));
								
			$this->$accion();
			exit;
		}
		catch(Exception $e)
		{
			$GLOBALS['SafiErrors']['general'] = array();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			
			include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			exit;
		}
	}
}