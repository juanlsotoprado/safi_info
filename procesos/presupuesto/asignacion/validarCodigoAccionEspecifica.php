<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require('../../../includes/constantes.php');
$tipo=trim($_REQUEST['tipo']);
$anioPres=trim($_REQUEST['anioPres']);
$codigo=trim($_REQUEST['codigo']);
$codigoAccionEspecifica=trim($_REQUEST['codigoAccionEspecifica']);
$formId=$_REQUEST['formId'];
$query = 	"SELECT ".
				"COUNT(sf.form_id) ".
			"FROM ".
				"sai_forma_1125 sf ".
			"WHERE ".
				"sf.form_id_p_ac = '".$codigo."' AND ".
				"sf.pres_anno = ".$anioPres." AND ".
				"sf.form_tipo = ".$tipo."::BIT(1) AND ".
				"sf.form_id_aesp = '".$codigoAccionEspecifica."'";
if($formId && $formId!=""){
	$query .= " AND sf.form_id <> '".$formId."'";
}
$resultado=pg_query($conexion,$query);
$row=pg_fetch_array($resultado);
if($row[0]>0){
	echo "true";
}else{
	echo "false";
}
ob_end_flush();
pg_close($conexion);
?>