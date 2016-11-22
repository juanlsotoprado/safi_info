<?php
class SafiModeloOrdenCompra {
	public static function BuscarIdsOrdenCompra($codigoDocumento, $idDependencia, $numLimit) {
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='' || trim($idDependencia) == '' || trim($idDependencia) == null)
	            throw new Exception("Error al buscar los ids de ordenes de compra. Detalles: El código del documento o la dependencia es nulo o vacío");
	
	        $query = "
	            SELECT
	                ordc_id AS id
	            FROM
	                sai_orden_compra
	            WHERE
	                ordc_id LIKE '%".$codigoDocumento."%' AND
	                esta_id<>15 AND
	                ordc_id NOT IN (
	                				SELECT nro_documento
	                				FROM registro_documento
	                				WHERE tipo_documento = 'ordc' 
	                				AND id_estado=1
	                				AND user_depe='".$_SESSION['user_depe_id']."'
	                				) AND 
	                SUBSTRING(rebms_id, 6,2)=SUBSTRING('".$idDependencia."',1,2)
	            ORDER BY ordc_id 
				LIMIT
				".$numLimit."
	        ";
	        //echo $query;
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de ordenes de compra. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        while($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[] = $row['id'];
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	
	    return $ids;
	} 	
	public static function BuscarInfoOrdenCompra($codigoDocumento) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
	    try  {
	        if($codigoDocumento == null || trim($codigoDocumento)=='')
	            throw new Exception("Error al buscar la información de la orden de compra. Detalles: El código del documento es nulo o vacío");
	
	        $query = "
			SELECT to_char(oc.fecha, 'dd-mm-yyyy') AS fecha, 
			oc.ordc_id AS id,
			p.prov_id_rif AS rif, 
			p.prov_nombre AS beneficiario, 
			oc.observaciones AS observaciones
			FROM sai_proveedor_nuevo p,
			 	sai_orden_compra oc
			WHERE oc.rif_proveedor_seleccionado = p.prov_id_rif AND 
				oc.ordc_id ='".$codigoDocumento."'";
	        if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
	            throw new Exception("Error al obtener los ids de ordenes de compra. Detalles: ".
	                utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
	        $ids = array();
	
	        if($row = $GLOBALS['SafiClassDb']->Fetch($result))
	        {
	            $ids[0] = $row['id'];
	            $ids[1] = $row['rif'].":".$row['beneficiario'];
	            $ids[2] = SafiModeloOrdenCompra::GetMontoTotal($codigoDocumento);
	            $ids[3] = utf8_encode($row['observaciones']);
	            if(SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0]))
	            	$ids[4] = SafiModeloCompromiso::GetCompromisoByIdDocumento($ids[0])->GetId();
	            else 
	            	$ids[4] = "comp-400";
				$ids[5] = $row['fecha'];	            	
	        }
	
	    }catch(Exception $e){
	        error_log($e, 0);
	    }
	    return $ids;
	} 	

	public static function GetMontoTotal($codigoOrdc) {
		include_once(SAFI_MODELO_PATH . '/compromiso.php' );
		try  {
			if($codigoOrdc == null || trim($codigoOrdc)=='')
			throw new Exception("Error al buscar la información de la orden de compra. Detalles: El código del documento es nulo o vacío");
	
			$query = "SELECT SUM(scb.iva*scb.base/100) AS iva,
					soc.rif_proveedor_seleccionado AS proveedor
					FROM sai_orden_compra soc
					INNER JOIN sai_cotizacion sc ON (soc.ordc_id = sc.ordc_id AND soc.rif_proveedor_seleccionado = sc.rif_proveedor) 
					INNER JOIN sai_cotizacion_base scb ON (sc.id_cotizacion = scb.id_cotizacion) 
					WHERE soc.ordc_id = '".$codigoOrdc."'
					GROUP BY soc.rif_proveedor_seleccionado";
			//echo $query;
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener el IVA: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			$iva = 0;
			$proveedor = '';
	
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$iva = $row['iva'];
				$proveedor = $row['proveedor'];
			}

			$query =	"SELECT s.redondear,
						SUM(s.subtotal) AS subtotal
						FROM
							(SELECT sc.redondear,
									SUM(sci.cantidad_cotizada*sci.precio*sci.unidad) AS subtotal 
							 FROM sai_cotizacion sc
								INNER JOIN sai_cotizacion_item sci ON (sc.id_cotizacion = sci.id_cotizacion) 
								WHERE 
									sc.ordc_id = '".$codigoOrdc."' AND 
									sc.rif_proveedor = '".$proveedor."' 
								GROUP BY sc.redondear 
								UNION 
								SELECT 
									sc.redondear,
									SUM(scia.cantidad_cotizada*scia.precio*scia.unidad) AS subtotal 
								FROM 
									sai_cotizacion sc 
									INNER JOIN sai_cotizacion_item_adicional scia ON (sc.id_cotizacion = scia.id_cotizacion) 
								WHERE 
									sc.ordc_id = '".$codigoOrdc."' AND 
									sc.rif_proveedor = '".$proveedor."' 
								GROUP BY sc.redondear 
							) AS s 
							GROUP BY s.redondear ";		
			//echo $query;
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener los subtotales de la orden de compra. Detalles: ".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			$subtotal = 0;
			
			if($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
			$subtotal = $row['subtotal'];
			}				
			
	
		}catch(Exception $e){
			error_log($e, 0);
		}
		return round($iva+$subtotal,2);
	}	
}