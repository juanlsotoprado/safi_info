<?php
class SafiModeloProveedor{
	public static function Search($key, $numItems){
		$proveedores = array();
		$query = "
			SELECT
				sp.prov_id_rif as rif,
				sp.prov_nombre as nombre
			FROM
				sai_proveedor_nuevo sp 
			WHERE
				LOWER(sp.prov_nombre) LIKE '%".utf8_decode(mb_strtolower($key, 'UTF-8'))."%' 
			LIMIT
				".$numItems."
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){
			echo $GLOBALS['SafiClassDb']->GetErrorMsg();
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$proveedor = new EntidadProveedor();
			$proveedor->SetRif($row['rif']);
			$proveedor->SetNombre(utf8_encode($row['nombre']));
			$proveedores[] = $proveedor;
		}
		return $proveedores;
	}
	public static function SearchByRifNombre($rifNombre, $numItems){
		$proveedores = array();
		$query = "
			SELECT
				sp.prov_id_rif as rif,
				sp.prov_nombre as nombre
			FROM
				sai_proveedor_nuevo sp 
			WHERE
				LOWER(sp.prov_nombre||' ('||sp.prov_id_rif||')') LIKE '%".utf8_decode(mb_strtolower($rifNombre, 'UTF-8'))."%'
			LIMIT
				".$numItems."
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){
			echo $GLOBALS['SafiClassDb']->GetErrorMsg();
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$proveedor = new EntidadProveedor();
			$proveedor->SetRif($row['rif']);
			$proveedor->SetNombre(utf8_encode($row['nombre']));
			$proveedores[] = $proveedor;
		}
		return $proveedores;
	}	
}