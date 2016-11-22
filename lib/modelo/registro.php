<?php
class SafiModeloCodigoDocumento {
	public static function Search($codigoDocumento, $dependencia, $tipoDocumento, $numItems){
		$codigoDocumentos = array();
		if ($tipoDocumento=='ordc') {
		$query = "
			SELECT c.ordc_id as id, p.prov_nombre as beneficiario, oc.observaciones as observaciones,  cb.base+(cb.base*cb.iva) as monto
			FROM sai_proveedor_nuevo p, sai_orden_compra oc, sai_cotizacion c, sai_cotizacion_base cb
			WHERE oc.rif_proveedor_seleccionado=p.prov_id_rif and oc.rif_proveedor_seleccionado=c.rif_proveedor
			and c.id_cotizacion=cb.id_cotizacion and c.ordc_id=oc.ordc_id and substring(rebms_id, 6,3)='".$dependencia."' and c.ordc_id like '%".$codigoDocumento."%' 
			 order by monto 
			LIMIT
				".$numItems."
		";
		}
		else if ($tipoDocumento=='vnac') {
			
			$idsViaticos = SafiModeloViaticoNacional::SearchListaIdsViaticos($codigoDocumento, $dependencia);
			
			$query = "
			SELECT c.ordc_id as id, p.prov_nombre as beneficiario, oc.observaciones as observaciones,  cb.base+(cb.base*cb.iva) as monto
			FROM sai_proveedor_nuevo p, sai_orden_compra oc, sai_cotizacion c, sai_cotizacion_base cb
			WHERE oc.rif_proveedor_seleccionado=p.prov_id_rif and oc.rif_proveedor_seleccionado=c.rif_proveedor
			and c.id_cotizacion=cb.id_cotizacion and c.ordc_id=oc.ordc_id and substring(rebms_id, 6,3)='".$dependencia."' and c.ordc_id like '%".$codigoDocumento."%' 
			 order by monto 
			LIMIT
				".$numItems."
		";
		}
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){
			echo $GLOBALS['SafiClassDb']->GetErrorMsg();
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$codigoDocumento = new EntidadCodigoDocumento();
			$codigoDocumento->SetId($row['id']);
			$codigoDocumento->SetBeneficiario(utf8_encode($row['beneficiario']));
			$codigoDocumento->SetObservaciones(utf8_encode($row['observaciones']));
			$codigoDocumento->SetMonto(utf8_encode($row['monto']));			
			//$codigoDocumento->SetEstaId($row['esta_id']);
			$codigoDocumentos[] = $codigoDocumento;
		}
		return $codigoDocumentos;
	}
}