<?php 
    ob_start();
	session_start();
	require_once("../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
	   	   ob_end_flush(); 
		   exit;
	  }
	ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>

</script>
</head>
<body>
<br />
<br />
<div align="center">
<?php 


$codigos = "";
if (isset($_POST['solicitud'])) {
	$cod = $_POST["solicitud"];
}
if (count($cod)>0) {

 for ($x=0;$x<count($cod);$x++) {
   $codigo = $cod[$x];


$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");

if($row=pg_fetch_array($resultado_set_most_p))
{
 $pres_anno=trim($row['pres_anno']);
 $compromiso=trim($row['comp_id']);
 $beneficiario=trim($row['sopg_bene_ci_rif']);
 $sopg_monto=trim($row['sopg_monto']); 
}


$valido = false;


  /*Busqueda del Causado*/
  $sql="SELECT * FROM sai_seleccionar_campo('sai_causado','caus_id','caus_docu_id='||'''$codigo''','',2) resultado_set(caus_id varchar)";
  $resultado_set = pg_exec($conexion ,$sql) ;
  $valido= $resultado_set;
	if ($resultado_set)
	{
	  $row = pg_fetch_array($resultado_set,0);
	  $causado=trim($row[0]);
	}

    $memo_contenido=trim($_POST['contenido_memo']);
    if ($memo_contenido==""){
 	 $memo_contenido="No Especificado";
    }
	//ANULACION SOPG
	$ano = $_SESSION['an_o_presupuesto'];
	$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);
	
	/*Caso causado*/
	$sql_caus="select max(substr(trim(c.comp_id),6,length(trim(c.comp_id))-5)) as maximo
	from sai_comp_diario c, sai_doc_genera g 
	where c.comp_doc_id=g.docg_id and
	c.comp_doc_id='".$codigo."' and comp_comen like 'C-%'
	and g.esta_id<>15";
	$resultado_caus=pg_query($conexion, $sql_caus);
	if ($row_caus=pg_fetch_array($resultado_caus)) 
	
	$coda_causado="coda-".$row_caus["maximo"];

	$sql_fecha = "select substring(comp_fec,1,4) as ano from sai_comp_diario where comp_id='".$coda_causado."'";
	$resultado_caus=pg_query($conexion, $sql_fecha);
	if ($row_caus=pg_fetch_array($resultado_caus)) 
	$ano_causado=$row_caus["ano"];

	/*Anulación gasto $coda_causado*/
	if ($ano_causado==$_SESSION['an_o_presupuesto']-1) {
		$cuenta_resultado="3.2.5.02.01.01.01";
		$nombre_resultado="Resultados del Ejercicio";
	}
	else if ($ano_causado!=$_SESSION['an_o_presupuesto']-1) {
		$cuenta_resultado="3.2.5.01.01.01.01";
		$nombre_resultado="Resultados Acumulados";
	}
	$sql_gasto = "select src.comp_id, src.cpat_id as cpat_id, trim(src.rcomp_debe) as monto_debe, trim(src.rcomp_haber) as monto_haber, src.fecha_emis,part_id,pr_ac,a_esp,pr_ac_tipo 
	from sai_reng_comp src, sai_comp_diario sc 
	where src.comp_id = sc.comp_id and sc.comp_id = '".$coda_causado."'";

	$resultado_set = pg_exec($conexion ,$sql_gasto) or die("Error al mostrar");
	$i=0;
	while($row=pg_fetch_array($resultado_set)) {
		/*if (trim($row['cpat_id'])<>'2.1.1.03.04.01.03' && trim($row['cpat_id'])<>'2.1.1.03.04.01.01' && trim($row['cpat_id'])<>'2.1.1.03.04.01.02') {*/
		if ($ano_causado!=$_SESSION['an_o_presupuesto'] && substr(trim($row['cpat_id']),0,1)==6) 
		$cta_ano= $cuenta_resultado;
		else  $cta_ano= trim($row['cpat_id']);

		$matriz_ctas[$i]=$cta_ano;
		if ($ano_causado!=$_SESSION['an_o_presupuesto'] && substr(trim($row['cpat_id']),0,1)==6) { 
			$matriz_partidas[$i] = null;
			$matriz_pr_ac[$i] = null;
			$matriz_a_esp[$i] = null;
			$matriz_pr_ac_tipo[$i] = null;			
		}
		else {
			$matriz_partidas[$i]=trim($row['part_id']);
			$matriz_pr_ac[$i] = trim($row['pr_ac']);
			$matriz_a_esp[$i] = trim($row['a_esp']);
			$matriz_pr_ac_tipo[$i] = trim($row['pr_ac_tipo']);
		}

		$matriz_monto[$i]=$row['monto_debe']+$row['monto_haber'];
		if ($row["monto_debe"]>0) $matriz_tipo[$i]=1; /*1 va por el debe*/
			else $matriz_tipo[$i]=0;
			$i++;
	}

	require_once("../../includes/arreglos_pg.php");
	$arreglo_ctas=convierte_arreglo($matriz_ctas);
	$arreglo_partidas=convierte_arreglo($matriz_partidas);
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_tipo=convierte_arreglo($matriz_tipo);
	$arreglo_pr_ac=convierte_arreglo($matriz_pr_ac);	
	$arreglo_a_esp=convierte_arreglo($matriz_a_esp);
	$arreglo_pr_ac_tipo=convierte_arreglo($matriz_pr_ac_tipo);		
	/*Fin anulación causado*/
	
	//LLAMAR A OTRA FUNCION PERO QUE SOLO REVERSE EL CAUSADO
	$sql = "SELECT * FROM sai_anular_causado 
	('".$codigo."', '".$user_depe_id."', '".$arreglo_ctas."', '".$arreglo_monto."', '".$arreglo_tipo."' ,
	'".$arreglo_partidas."', '".$arreglo_pr_ac."', '".$arreglo_a_esp."','".$arreglo_pr_ac_tipo."',".$ano.") AS resultado";
	$resultado = pg_exec($conexion ,$sql) or die("Error al intentar realizar la anulaci\u00F3n total");
	
		
	$cero="0";
	$sql  = "select * from sai_anular_sopg('".trim($_SESSION['login'])."','".trim($codigo)."','";
	$sql  .= trim($memo_contenido)."','";
	$sql  .= trim($_SESSION['user_depe_id'])."','";
    $sql  .= $cero."','".$cero."','".$causado;
    $sql  .= "') as memo_id";
	$resultado_set = pg_exec($conexion ,$sql);
	/******************CODIGO ANTERIOR QUE HAY QUE CAMBIAR********************/
	//SI EXISTE UN REGISTRO CONTABLE HACER EL REVERSO//	
//	$sql_reverso = "SELECT * FROM sai_insert_reverso_contable('".$codigo."', '".$_SESSION['user_depe_id']."') AS resultado";	
	//$result_insert_comp_auto=pg_query($conexion,$sql_reverso) or die("Error al reversar el asiento ");

	/************************************************************************/
		
	if ($codigos == "") $codigos = $codigo;
	else $codigos = $codigos . ", ". $codigo;

	  $total_imputacion=0;
 	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); 
				$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); 
				$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']);
				$matriz_uel[$i]=trim($row['depe_id']); 
				$matriz_monto_partida[$i]=$row['sopg_monto']+$row['sopg_monto_exento'];
				$i++;
			}
		}
	

	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$tt_retencion=0;
 			while($row_rete_doc=pg_fetch_array($resultado_set))	
			 {
				$tt_retencion=$row_rete_doc['rete_monto']+$tt_retencion;
			 }
			
		} 
		
		
	//Consulto las OTRAS retenciones del documento 
    $sql_be="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$codigo."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'"; 
	$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	
	if ($resultado_set_most_be)
	{

	  $total_otras_rete=0;
	  while($rowbe=pg_fetch_array($resultado_set_most_be))
	  {
		  $total_otras_rete=$total_otras_rete+$rowbe['sopg_ret_monto'];
	   }
 		
	 }
	 
	$tt_neto=$sopg_monto-$tt_retencion-$total_otras_rete;
	
  if($compromiso<>'N/A'){
	//ACTUALIZAR MONTO DISPONIBLE DEL COMP
	for($j=0; $j<$total_imputacion; $j++)
	  {  
	  $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
	  $resultado_query = pg_exec($conexion,$query_disponible);
	  if ($row=pg_fetch_array($resultado_query)){
		$disponible=$row['disponible']+$matriz_monto_partida[$j];
		$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
		$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
	    $cod_pcta=$pcta_id;
 	  }
			
	 }
 }
 
 //REVERSAR LOS MONTOS DE LAS RETENCIONES DEL ISLR
 $sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$beneficiario."'";
 $resultado_query = pg_exec($conexion,$sql_islr);
 if ($row_islr=pg_fetch_array($resultado_query)){
  $monto_actualizado=$row_islr['monto_pagado']-$tt_neto ;
  $sql="UPDATE sai_retenciones_islr SET monto_pagado=".$monto_actualizado." WHERE cedula='".$beneficiario."'";
  $result=pg_query($conexion,$sql);
 }
	
} //end for


echo "<div class='normalNegrita' align='center'><strong>Se proces&oacute; satisfactoriamente la anulaci&oacute;n de la(s) solicitud(es) de pago siguiente: ".$codigos."</strong>";
echo "<br><br><a href='sopg_aprobados.php'>Volver </a></div>";

}
else {
	echo "<div class='normalNegrita' align='center'><strong>Error: Debe seleccionar al menos una solicitud de pago para anular</strong>";
	echo "<br><br><a href='sopg_aprobados.php'>Volver </a></div>";
}
?>

</div>
</body>
</html>
<?php pg_close($conexion);?>