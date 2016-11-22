<?php

echo "No hace nada";
exit();

/********************************************************************
 *   Se borran todos los registros de las rendiciones de avance     *
 ********************************************************************/

header("Content-type: text/html; charset=UTF-8");

include(dirname(__FILE__) . "/../../init.php");

class RendicionAvanceEliminar
{
	public function __construct()
	{
		$GLOBALS['SafiInfo']['general'] = array();
		$GLOBALS['SafiErrors']['general'] = array();
		
		try
		{
			if(isset($_REQUEST["accion"]))
			{
				if(($accion=trim($_REQUEST["accion"])) == '')
					throw new Exception('No se ha seleccionado ninguna acci&oacute;n');
				
				if(!method_exists($this, $accion))
					throw new Exception( sprintf("Acci&oacute;n \"%s\" no definida", $accion));
				
				$method = new ReflectionMethod($this, $accion);
				if(!$method->isPublic())
					throw new Exception( sprintf("Acceso denegado a la acci&oacute;n: \"%s\"", $accion));
					
				$this->$accion();
			}
		}
		catch (Exception $e)
		{
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			error_log($e, 0);
		}
	}
	
	public function EliminarRendicionAvances()
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Eliminar los datos de la tabla safi_responsable_rendicion_avance_reintegro
			$query = "DELETE FROM safi_responsable_rendicion_avance_reintegro";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al intentar borrar los datos de la tabla: "
					."tabla safi_responsable_rendicion_avance_reintegro. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
					
			// Eliminar los datos de la tabla safi_responsable_rendicion_avance_partida
			$query = "DELETE FROM safi_responsable_rendicion_avance_partida";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al intentar borrar los datos de la tabla: "
					."safi_responsable_rendicion_avance_partida. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
					
			// Eliminar los datos de la tabla safi_responsable_rendicion_avance
			$query = "DELETE FROM safi_responsable_rendicion_avance";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al intentar borrar los datos de la tabla: "
					."safi_responsable_rendicion_avance. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Eliminar los datos de la tabla safi_rendicion_avance
			$query = "DELETE FROM safi_rendicion_avance";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al intentar borrar los datos de la tabla: "
					."safi_rendicion_avance. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
		
			// Eliminar los datos de la tabla sai_doc_genera
			$query = "
				DELETE FROM
					sai_doc_genera
				WHERE
					docg_id LIKE '".GetConfig("preCodigoRendicionAvance").GetConfig("delimitadorPreCodigoDocumento")."%'
			";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al intentar borrar los datos de la tabla: "
					."sai_doc_genera. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
					
			$GLOBALS['SafiInfo']['general'][] = "Finalizado con &eacute;xito";
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
			$GLOBALS['SafiClassDb']->RollbackTransaction();
		}
		catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
}

new RendicionAvanceEliminar();

?>

<html>
	<head>
		<title>.:SAFI:. Ingresar Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<?php
			if(isset($_REQUEST["accion"]))
			{
				include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			}
		?>
	</body>
</html>