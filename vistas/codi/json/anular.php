<?php
header("Content-type: application/json; charset=UTF-8");
$param= array();
$param2= array();
$param3= array();
//$param4= array();

if(is_array($GLOBALS['SafiRequestVars']['listaCodiDetalle'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiDetalle'] as $listaCodi){
		foreach ($listaCodi as $listaCodi2) {

			$param[] = array(
					"comp_id" => utf8_encode($listaCodi2['comp_id']),
					"reng_comp" => utf8_encode($listaCodi2['reng_comp']),
					"cpat_id" => utf8_encode($listaCodi2['cpat_id']),
					"cpat_nombre" => utf8_encode($listaCodi2['cpat_nombre']),
					"rcomp_debe" => utf8_encode($listaCodi2['rcomp_debe']),
					"rcomp_haber" => utf8_encode($listaCodi2['rcomp_haber']),
					"pr_ac" => utf8_encode($listaCodi2['pr_ac']),
					"a_esp" => utf8_encode($listaCodi2['a_esp']),
					"part_id" => utf8_encode($listaCodi2['part_id']),
					"pr_ac_tipo" => utf8_encode($listaCodi2['pr_ac_tipo']),
					"centros" => utf8_encode($listaCodi2['centros']),
					"a_esp_nombre" => utf8_encode($listaCodi2['a_esp_nombre']),
					"p_acc_nombre" => utf8_encode($listaCodi2['p_acc_nombre'])
			);
		}
	}
}

/*if(is_array($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'])){
	foreach ($GLOBALS['SafiRequestVars']['listaCodiPresupuesto'] as $listaCodi){
		foreach ($listaCodi as $listaCodi2) {

			$param2[] = array(
					"comp_id" => utf8_encode($listaCodi2['comp_id']),
					"centro_gestor" => utf8_encode($listaCodi2['centro_gestor']),
					"centro_costo" => utf8_encode($listaCodi2['centro_costo']),
					"part_id" => utf8_encode($listaCodi2['part_id']),
					"cpat_id" => utf8_encode($listaCodi2['cpat_id']),
					"cadt_monto" => utf8_encode($listaCodi2['cadt_monto'])
			);
		}
	}
}*/

if(is_array($GLOBALS['SafiRequestVars']['listaCodi'])){
 foreach ($GLOBALS['SafiRequestVars']['listaCodi'] as $listaCodi){


	$param2[] = array(
			"comp_id" => utf8_encode($listaCodi['comp_id']),
			"comp_fec" => utf8_encode($listaCodi['comp_fec']),
			"comp_comen" => utf8_encode($listaCodi['comp_comen']),
			"esta_id" => utf8_encode($listaCodi['esta_id']),
			"comp_doc_id" => utf8_encode($listaCodi['comp_doc_id']),
			"nro_referencia" => utf8_encode($listaCodi['nro_referencia']),
			"nro_compromiso" => utf8_encode($listaCodi['nro_compromiso']),
			"fuente_financiamiento" => utf8_encode($listaCodi['fuente_financiamiento'])
	);
}
}

//$param4 = array_merge($param3, $param, $param2);
$param3[0] = $param2;
$param3[1] = $param;
//$param4[2] = $param2;

//error_log(print_r($param4,true));

echo json_encode($param3);
?>