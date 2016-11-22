<?php
  header("Content-type: application/json; charset=UTF-8");
  require_once("../../includes/conexion.php");
  $pres_anno = $_SESSION['an_o_presupuesto'];
  $a_o="comp-%".substr($_SESSION['an_o_presupuesto'],2,2);
  if (isset($_POST['sendValue'])){
    $value = "comp-".$_POST['sendValue'];  
  }else{
    $value = "";
  }
  
  $listaPartida = array();

$i=0;

$sql_p="Select monto,partida,
		comp_sub_espe,t1.comp_acc_esp,t1.comp_acc_pp,comp_tipo_impu,substr(t1.comp_id,6) as comp_id,
		case comp_tipo_impu when CAST(1 AS BIT) then (select proy_titulo  from sai_proyecto where t1.comp_acc_pp=proy_id and pre_anno='".$pres_anno."') else
		(select acce_denom from sai_ac_central where t1.comp_acc_pp=acce_id and pres_anno='".$pres_anno."') end as titulo_proy,
		case comp_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
		(select aces_nombre from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as titulo_accion,
		case comp_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
		(select centro_gestor from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_gestor,
		case comp_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where t1.comp_acc_pp=proy_id and paes_id=t1.comp_acc_esp and pres_anno='".$pres_anno."') else
		(select centro_costo from sai_acce_esp where t1.comp_acc_pp=acce_id and t1.comp_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_costo,
		part_nombre,fuente_financiamiento
		from 
		sai_comp_imputa t1,sai_partida t2,sai_disponibilidad_comp t3, sai_forma_1125 t4
		where t1.comp_id='".$value."' and t4.pres_anno='".$pres_anno."' and form_id_p_ac=t1.comp_acc_pp and form_id_aesp=t1.comp_acc_esp 
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and t3.comp_acc_pp=t1.comp_acc_pp and t3.comp_acc_esp=t1.comp_acc_esp
		and 
		part_id=comp_sub_espe and t1.pres_anno=t2.pres_anno and 
		t3.comp_id=t1.comp_id and t3.partida=t2.part_id and t4.esta_id=1 and 
		t1.comp_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '".$a_o."' order by docg_fecha)
		order by partida";
//echo $sql_p;
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");

while($row=pg_fetch_array($resultado_set_most_p)) 
   {

	$partida = $row['comp_sub_espe'];
  	$acc_esp = $row['comp_acc_esp'];
  	$acc_pp = $row['comp_acc_pp'];
  	$imputacion = $row['comp_tipo_impu'];
  	$id_comp =  $row['comp_id'];
  	$titulo = $row['titulo_proy'];
  	$accion = $row['titulo_accion'];
  	$part_nombre = $row['part_nombre'];
  	$gestor = $row['centro_gestor'];
  	$costo = $row['centro_costo'];
  	$monto_comp=$row['monto'];
  	$fuente=$row['fuente_financiamiento'];
  
	$data = array(
				'id' => $i,
				'partida' => $partida,
				'acc_esp' =>  $acc_esp,
				'acc_pp' => $acc_pp,
				'imputacion' => $imputacion,
				'id_comp' => $id_comp,
				'titulo' => utf8_encode($titulo),
				'accion' => utf8_encode($accion),
				'part_nombre' => utf8_encode($part_nombre),
				'gestor' => $gestor,
				'costo' =>  $costo,
				'monto_comp' => $monto_comp,
				'fuente' => $fuente
			);
	
	$listaPartida[$i] = $data;
	$i++;
   
   }
   echo json_encode(array("listaPartida" => $listaPartida));
  
?>
