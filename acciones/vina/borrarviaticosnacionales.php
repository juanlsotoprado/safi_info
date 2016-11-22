<?php
echo "No disponible";
exit;

include(dirname(__FILE__) . '/../../init.php');

// Borrar información de doc_genera de la requisición de los viáticos nacionales
$query = "
	DELETE FROM
		sai_doc_genera
	WHERE
		docg_id LIKE 'rqui%' AND
		docg_id IN
			(
				SELECT
					rebms_id
				FROM
					sai_req_bi_ma_ser
				WHERE
					vnac_id like 'vnac%'
			)
";
if(!$GLOBALS['SafiClassDb']->Query($query)){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar información de los items de la requisición de los viáticos nacionales
$query = "
	DELETE FROM
		sai_rqui_items
	WHERE
		rebms_id IN
			(
				SELECT
					rebms_id
				FROM
					sai_req_bi_ma_ser
				WHERE
					vnac_id like 'vnac-%'
			)
";
if(!$GLOBALS['SafiClassDb']->Query($query)){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar información de la requisición de viáticos nacionales
$query = "
	
	DELETE FROM
		sai_req_bi_ma_ser
	WHERE
		vnac_id like 'vnac-%'

";
if(!$GLOBALS['SafiClassDb']->Query($query)){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar información de la relación entre los víaticos, las cagtegorías y las redes
if(!$GLOBALS['SafiClassDb']->Query("DELETE FROM safi_viatico_categoria_red")){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

/******************************************************************
 * Borrar información de los memos de viaticos nacionales
 ******************************************************************/

// Obtener el Id de los memos de viaticos nacionales
$query = "
	SELECT
		doso_doc_soport
	FROM
		sai_docu_sopor
	WHERE
		doso_doc_fuente LIKE 'vnac%'
";

$idMemos = array();
if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false){ 
	while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
		$idMemos[] = "'".$row['doso_doc_soport']."'";
	}
} else {
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

if(count($idMemos)>0){
	// Borrar la información de la relación de memos y viaticos nacionales
	if(!$GLOBALS['SafiClassDb']->Query("DELETE FROM sai_docu_sopor WHERE doso_doc_fuente LIKE 'vnac%'")){
		echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
	}
	
	// Boorrar la información de los memos de los viaticos nacionales
	if(!$GLOBALS['SafiClassDb']->Query("DELETE FROM sai_memo where memo_id IN (".implode(', ', $idMemos).")")){
		echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
	}
}

/******************************************************************
 * Fin de borrar información de los memos de viaticos nacionales
 ******************************************************************/

// Borrar información de las revisiones de viaticos nacionales
if(!$GLOBALS['SafiClassDb']->Query("DELETE FROM sai_revisiones_doc WHERE revi_doc LIKE 'vnac%'")){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información de doc_genera de los viáticos nacionales
if(!$GLOBALS['SafiClassDb']->Query("DELETE FROM sai_doc_genera WHERE docg_id LIKE 'vnac%'")){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información relacionada con las asignaciones para el responsable del viático
if(!$GLOBALS['SafiClassDb']->Query('DELETE FROM safi_viatico_responsable_asignacion')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información relacionada con el responsable del viatico
if(!$GLOBALS['SafiClassDb']->Query('DELETE FROM safi_responsable_viatico')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Reiniciar la secuencia de id de los responsable
if(!$GLOBALS['SafiClassDb']->Query('SELECT setval(\'safi_responsable_viatico__id__seq\', 1, false)')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información de las rutas de los viaticos
if(!$GLOBALS['SafiClassDb']->Query('DELETE FROM safi_ruta_viatico')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Reiniciar la secuencia de id de las rutas
if(!$GLOBALS['SafiClassDb']->Query('SELECT setval(\'safi_ruta_viatico__id__seq\', 1, false)')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información de los infocentros de los viaticos
if(!$GLOBALS['SafiClassDb']->Query('DELETE FROM safi_viatico_infocentro')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

// Borrar la información del viatico
if(!$GLOBALS['SafiClassDb']->Query('DELETE FROM safi_viatico')){
	echo $GLOBALS['SafiClassDb']->GetErrorMsg() . '</br></br>';
}

