<?php
class SafiModeloRegistroDocumento {
	public static function Buscar($codigoDocumento, $dependencia, $tipoDocumento, $numItems){
		if ($tipoDocumento=='ordc') {
			$ids = SafiModeloOrdenCompra::BuscarIdsOrdenCompra($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='vnac') {
			$ids = SafiModeloViaticoNacional::BuscarIdsViaticosNacionales($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='rvna') {
			$ids = SafiModeloRendicionViaticoNacional::BuscarIdsRendicionViaticosNacionales($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='avan') {
			$ids = SafiModeloAvance::BuscarIdsAvances($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='rava') {
			$ids = SafiModeloRendicionAvance::BuscarIdsRendicionAvances($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='pcta') {
			$ids = SafiModeloPuntoCuenta::BuscarIdsPuntoCuenta($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='comp') {
			$ids = SafiModeloCompromiso::BuscarIdsCompromiso($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='rqui') {
			$ids = SafiModeloRequisicion::BuscarIdsRequisicion($codigoDocumento, $dependencia, $numItems);
		}
		else if ($tipoDocumento=='memr') {
			$ids = SafiModeloMemorando::BuscarIdsMemorando($codigoDocumento, $dependencia, $numItems);
		}
		return $ids;
	}
	public static function Completar($codigoDocumento, $tipoDocumento, $numItems){
		if ($tipoDocumento=='ordc') {
			$ids = SafiModeloOrdenCompra::BuscarInfoOrdenCompra($codigoDocumento);
		}
		else if ($tipoDocumento=='vnac') {
			$ids = SafiModeloViaticoNacional::BuscarInfoViaticoNacional($codigoDocumento);
		}
		else if ($tipoDocumento=='rvna') {
			$ids = SafiModeloRendicionViaticoNacional::BuscarInfoRendicionViaticoNacional($codigoDocumento);
		}
		else if ($tipoDocumento=='avan') {
			$ids = SafiModeloAvance::BuscarInfoAvance($codigoDocumento);
		}
		else if ($tipoDocumento=='rava') {
			$ids = SafiModeloRendicionAvance::BuscarInfoRendicionAvance($codigoDocumento);
		}
		else if ($tipoDocumento=='rqui') {
			$ids = SafiModeloRequisicion::BuscarInfoRequisicion($codigoDocumento);
		}		
		else if ($tipoDocumento=='pcta') {
			$ids = SafiModeloPuntoCuenta::BuscarInfoPuntoCuenta($codigoDocumento);
		}
		else if ($tipoDocumento=='comp') {
			$ids = SafiModeloCompromiso::BuscarInfoCompromiso($codigoDocumento);
		}
		else if ($tipoDocumento=='memr') {
			$ids = SafiModeloMemorando::BuscarInfoMemorando($codigoDocumento);
		}
		
		return $ids;
	}
	
}