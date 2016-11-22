<?php
include_once(SAFI_ENTIDADES_PATH . '/requisicion.php');

class SafiModeloRequisicion
{
	public static function GuardarRequisicion($params)
	{
		try {
			
			$query = "
				SELECT
					* 
				FROM
					sai_ingresar_rqui
		        	(
						".$params['tipo'].",
						'".$params['idDependenciaCreador']."',
						'".$params['loginUsuarioCreador']."',
						".$params['annoPresupuestario'].",
						'".$params['tipoProyectoAccionCentralizada']."',
						'".$params['idProyectoAccionCentralizada']."',
						'".$params['idAccionEspecifica']."',
						'".$params['proveedorSugerido1']."',
						'".$params['proveedorSugerido2']."',
						'".$params['proveedorSugerido3']."',
						'".$params['calidad']."',
						'".$params['tiempoDeEntrega']."',
						'".$params['garantia']."',
						'".$params['observaciones']."',
						'".$params['idPuntoCuenta']."',
						'".$params['justificacionPuntoCuenta']."',
						'".$params['idGerenciaAdscripcion']."',
						'".$params['descripcionGeneral']."',
						'".$params['justificacion']."',
						'".$params['idViatico']."',
						".$params['idEstatus'].",
						'".$params['fechaUsuario']."',
						'".$params['idItems']."',
						'".$params['itemCantidades']."',
						'".$params['itemEspecificaciones']."'
		        	) AS resultado_set(TEXT)
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					if(count($row)>0){
						reset($row);
						$idRequisicion = current($row);
						return $idRequisicion; 
					}
				}
			}
			
			return false;
			
		} catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
	}
	
	public static function GetRequisicionesByIdViaticoNacional($idViaticoNacional)
	{
		$requisiciones = null;
		
		$query = "
			SELECT
				rebms_id,
				vnac_id
			FROM
				sai_req_bi_ma_ser
			WHERE
				vnac_id = '".$idViaticoNacional."'
		";
		
		if($result = $GLOBALS['SafiClassDb']->Query($query)){
			$requisiciones = array();
			if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$requisicion = self::LlenarRequisicion($row);
				$requisiciones[] = $requisicion; 
			}
		}
		
		return $requisiciones;
	}
	
	private static function LlenarRequisicion($row)
	{
		$requisicion = new EntidadRequisicion();
		$requisicion->SetId($row['rebms_id']);
		$requisicion->SetIdViaticoNacional($row['vnac_id']);
		
		return $requisicion;
	}
	
	public static function BuscarIdsRequisicion($codigoDocumento, $idDependencia, $numLimit) {
		$idsViaticos = null;
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
			throw new Exception("Error al buscar los ids de requisicion. Detalles: El código del documento o la dependencia es nulo o vacío");
	
			$query = "
		            SELECT
		               r.rebms_id AS id
		            FROM
		                sai_req_bi_ma_ser r, sai_doc_genera d
		            WHERE
		               r.rebms_id = d.docg_id AND
		               r.rebms_id LIKE '%".$codigoDocumento."%' AND
		               r.depe_id = '".$idDependencia."' AND
		               d.esta_id<>15 AND 
		               r.rebms_id NOT IN (
		               					SELECT nro_documento
		               					FROM registro_documento
		               					WHERE tipo_documento = 'rqui'
		               					AND id_estado=1
		               					AND user_depe='".$_SESSION['user_depe_id']."'
		               					) AND 
		               r.rebms_fecha LIKE '".$_SESSION['an_o_presupuesto']."%'  
		            ORDER BY r.rebms_id 
					LIMIT
					".$numLimit."
		        ";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de requisicion. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$ids = array();
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[] = $row['id'];
			}
	
		}catch(Exception $e){
			error_log($e, 0);
		}
	
		return $ids;
	}
	
	public static function BuscarInfoRequisicion($codigoDocumento) {
		try  {
			if($codigoDocumento == null || trim($codigoDocumento)=='')
			throw new Exception("Error al buscar la información de la requisicion");
	
			$query = "
				SELECT TO_CHAR(rebms_fecha, 'dd-mm-yyyy') AS fecha,
					rebms_id AS id, 
					justificacion AS objetivos
				FROM sai_req_bi_ma_ser
				WHERE rebms_id='".$codigoDocumento."'";
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los ids de las requisiciones. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$ids = array();
	
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$ids[0] = $row['id'];
				$ids[1] = '';
				$ids[2] = 0;
				$ids[3] = utf8_encode($row['objetivos']);
				$ids[4] = "comp-400";
				$ids[5] = $row['fecha'];
			}
	
		}catch(Exception $e){
			error_log($e, 0);
		}
	
		return $ids;
	}
}
