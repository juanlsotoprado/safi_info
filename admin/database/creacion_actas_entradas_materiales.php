<?php

echo "No hace nada";
exit();

include(dirname(__FILE__) . "/../../init.php");

$GLOBALS['SafiErrors']['general'] = array();
$GLOBALS['SafiInfo']['general']  = array();

$creacionActasSalidasMateriales = new CreacionActasSalidasMateriales();

class CreacionActasSalidasMateriales
{
	public function __construct()
	{
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
			
		} catch(Exception $e){
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			
			include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
			exit;
		}
	}
	
	public function CrearActasSalidas()
	{
		$detallesSalidas = array();
		
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Actualizar los rif de los proveedores de la forma '' a NULL en los detalles de las entradas (sai_arti_almacen)
			$query = "
				UPDATE
					sai_arti_almacen
				SET
					prov_id_rif = NULL
				WHERE
					TRIM(both FROM prov_id_rif) = ''
			";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al actualizar los rif de los proveedores a NULL. Detalles: "
					.$GLOBALS['SafiClassDb']->GetErrorMsg());
					
			$query = "
				UPDATE
					sai_arti_inco
				SET
					proveedor = NULL
				WHERE
					TRIM(both FROM proveedor) = ''
			";
					
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al actualizar los rif de los proveedores a NULL. Detalles: "
					.$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			// Obtener los detalles de las entradas (sai_arti_almacen) que no tienen un apta de entrada asociado
			$query = "
				SELECT
					entrada_detalle.usua_login,
					entrada_detalle.depe_solicitante,
					entrada_detalle.fecha_proceso,
					TO_CHAR(entrada_detalle.fecha_proceso, 'YY') AS año,
					entrada_detalle.prov_id_rif
				FROM
					sai_arti_almacen entrada_detalle
				WHERE
					entrada_detalle.acta_id IS NULL
				GROUP BY
					entrada_detalle.usua_login,
					entrada_detalle.depe_solicitante,
					entrada_detalle.fecha_proceso,
					entrada_detalle.prov_id_rif
				ORDER BY
					entrada_detalle.fecha_proceso
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception("Error al obtener los detalles de las salidas. Detalles: ". 
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$detallesSalidas[] = $row;
			}
			
			foreach ($detallesSalidas AS $detalleSalida)
			{
				// Generar el próximo código de acta de entrada
				$query = "
					SELECT
						COALESCE(MAX((SUBSTRING(acta_id FROM 9 FOR LENGTH(acta_id)-10)) :: INT),0) + 1 AS max_id
					FROM 
						sai_arti_inco
					WHERE
						SUBSTRING(acta_id FROM 1 FOR 4) = 'emat'
						AND SUBSTRING(acta_id FROM 6 FOR 3) = '453'
						AND SUBSTRING(acta_id FROM LENGTH(acta_id) -1 FOR 2) = '".$detalleSalida['año']."'
				";
				
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al obtener el proximo id. Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg());
				
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result))
					$maxId = $row['max_id'];
				else
					throw new Exception("Correlativo de pr&oacute;ximo id no encontrado.");
				
				if(!is_numeric($maxId))
					throw new Exception("Correlativo de pr&oacute;ximo id no es un num&eacute;rico.");
					
				$proximoId = "emat-453".$maxId.$detalleSalida['año'];
					
				// Ingresar el acta de entrada (sai_arti_inco)
				$query = "
					INSERT INTO sai_arti_inco
						(
							acta_id,
							esta_id,
							usua_login,
							depe_solicitante,
							fecha_registro,
							depe_id,
							proveedor,
							observaciones
						)
					VALUES
						(
							'".$proximoId."',
							'1',
							'".$detalleSalida['usua_login']."',
							'".$detalleSalida['depe_solicitante']."',
							'".$detalleSalida['fecha_proceso']."',
							'453',
							".($detalleSalida['prov_id_rif'] !== null 
								? "'".trim($detalleSalida['prov_id_rif'])."'" : "NULL").",
							'".utf8_decode(
								"Registros de actas de entrada creados automáticamente el 30/10/2012 porque existían artículos en ".
								"salidas detalles (sai_arti_almacen) que no tenían un acta asociada en el maestro de salidas ".
								"(sai_arti_inco)."
							)."'
						)
				";
		
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al ingresar el acta de entrada. Detalles: "
						.$GLOBALS['SafiClassDb']->GetErrorMsg());
						
				// Actualizar el detalle de la entrada (sai_arti_almacen)
				$query = "
					UPDATE
						sai_arti_almacen
					SET
						acta_id = '".$proximoId."'
					WHERE
						usua_login = '".$detalleSalida['usua_login']."'
						AND depe_solicitante = '".$detalleSalida['depe_solicitante']."'
						AND fecha_proceso = '".$detalleSalida['fecha_proceso']."'
						AND prov_id_rif ".(
							$detalleSalida['prov_id_rif'] !== null ? "= '".trim($detalleSalida['prov_id_rif'])."'" : "IS NULL"
						)."
				";
						
				if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
					throw new Exception("Error al actualizar el detalle del acta de entrada. Detalles: "
						.$GLOBALS['SafiClassDb']->GetErrorMsg());
				
			}
			
			//throw new Exception("Quitar al finalizar.");
			
			if($GLOBALS['SafiClassDb']->CommitTransaction() === false)
				throw new Exception('Error al ejecutar el commit de la transacci&oacute. Detalles: '
					. $GLOBALS['SafiClassDb']->GetErrorMsg());
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			include(SAFI_VISTA_PATH . "/desplegarmensajes.php");
		}
		
		
	}
}

?>