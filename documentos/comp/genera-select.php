<?php
  header("Content-type: application/json; charset=UTF-8");
  require_once("../../includes/conexion.php");
  $pres_anno = $_SESSION['an_o_presupuesto'];
  $ultimos_dig_ao=substr($pres_anno,2,2);

  
  if (isset($_POST['sendValue'])){
    $value = $_POST['sendValue'];  
  }else{
    $value = "";
  }
  
  $listaPartida = array();

$i=0;

$sql_p="Select  sum(monto) as monto, b.partida, acc_esp, acc_pp, acc_tipo, pcta_id, gerencia,asunto,rif,asunto_nombre, depe_nombre,titulo_proy, titulo_accion, centro_gestor, centro_costo, part_nombre
from (Select t1.pcta_sub_espe as partida,t1.pcta_acc_esp as acc_esp,t1.pcta_acc_pp as acc_pp,t1.pcta_tipo_impu as acc_tipo, t6.pcta_asociado as pcta_id,t6.monto as monto,pcta_gerencia as gerencia,pcta_asunto as asunto,rif_sugerido as rif,pcas_nombre as asunto_nombre,depe_nombre as depe_nombre, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select proy_titulo from sai_proyecto where pcta_acc_pp=proy_id and pre_anno='".$pres_anno."') 
else (select acce_denom from sai_ac_central where pcta_acc_pp=acce_id and pres_anno='".$pres_anno."') end as titulo_proy, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select aces_nombre from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as titulo_accion, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select centro_gestor from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_gestor, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select centro_costo from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_costo, part_nombre 
from sai_pcta_imputa t1, sai_partida t2,sai_pcuenta t3,sai_pcta_asunt,sai_dependenci t5,sai_disponibilidad_pcta t6
where t1.pcta_id='".$value."' and t2.pres_anno='".$pres_anno."' and t1.pcta_id=t6.pcta_id and t1.pcta_sub_espe=t2.part_id and t6.pcta_asociado=t3.pcta_id and t2.part_id=t6.partida and t5.depe_id=pcta_gerencia and pcas_id=pcta_asunto and t3.pcta_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '%".$ultimos_dig_ao."' order by docg_fecha) 
group by t1.pcta_sub_espe,t1.pcta_acc_esp,t1.pcta_acc_pp,t1.pcta_tipo_impu, t6.pcta_asociado,partida,t6.monto,pcta_gerencia,pcta_asunto,rif_sugerido,pcas_nombre,depe_nombre,part_nombre 

union all
(Select  t1.pcta_sub_espe as partida,t1.pcta_acc_esp as acc_esp,t1.pcta_acc_pp as acc_pp,t1.pcta_tipo_impu as acc_tipo, t6.pcta_asociado as pcta_id,t6.monto as monto,pcta_gerencia as gerencia,pcta_asunto as asunto,rif_sugerido as rif,pcas_nombre as asunto_nombre,depe_nombre as depe_nombre, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select proy_titulo from sai_proyecto where pcta_acc_pp=proy_id and pre_anno='".$pres_anno."') 
else (select acce_denom from sai_ac_central where pcta_acc_pp=acce_id and pres_anno='".$pres_anno."') end as titulo_proy, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select paes_nombre from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select aces_nombre from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as titulo_accion, 
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_gestor from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select centro_gestor from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_gestor,
case pcta_tipo_impu when CAST(1 AS BIT) then (select centro_costo from sai_proy_a_esp where pcta_acc_pp=proy_id and paes_id=pcta_acc_esp and pres_anno='".$pres_anno."') 
else (select centro_costo from sai_acce_esp where pcta_acc_pp=acce_id and pcta_acc_esp=aces_id and pres_anno='".$pres_anno."') end as centro_costo, 
part_nombre from sai_pcta_imputa t1, sai_partida t2,sai_pcuenta t3,sai_pcta_asunt,sai_dependenci t5,sai_disponibilidad_pcta t6 
where t1.pcta_id='".$value."' and t2.pres_anno='".$pres_anno."' and t1.pcta_id=t6.pcta_asociado and t1.pcta_sub_espe=t2.part_id and t6.pcta_asociado=t3.pcta_id and t2.part_id=t6.partida and t5.depe_id=pcta_gerencia and pcas_id=pcta_asunto and t3.pcta_id in (select docg_id from sai_doc_genera where esta_id<>15 and docg_id like '%".$ultimos_dig_ao."' order by docg_fecha) 
group by t1.pcta_sub_espe,t1.pcta_acc_esp,t1.pcta_acc_pp,t1.pcta_tipo_impu, t6.pcta_asociado,partida,t6.monto,pcta_gerencia,pcta_asunto,rif_sugerido,pcas_nombre,depe_nombre,part_nombre order by t6.pcta_asociado )
) as b
group by pcta_id, b.partida, acc_esp, acc_pp, acc_tipo, pcta_id, gerencia,asunto,rif,asunto_nombre, depe_nombre,titulo_proy, titulo_accion, centro_gestor, centro_costo, part_nombre";
//echo $sql_p;
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");

while($row=pg_fetch_array($resultado_set_most_p)) 
   {

   	$partida = $row['partida'];
	$monto_pcta=$row['monto'];
  	$acc_esp = $row['acc_esp'];
  	$acc_pp = $row['acc_pp'];
  	$imputacion = $row['acc_tipo'];
  	$id_pcta =  $row['pcta_id'];
  	$titulo = $row['titulo_proy'];
  	$accion = $row['titulo_accion'];
  	$gestor = $row['centro_gestor'];
  	$costo = $row['centro_costo'];
  	$gerencia_id=$row['gerencia'];
  	$asunto_id=$row['asunto'];
  	$rif=$row['rif'];
  	$asunto_desc=$row['asunto_nombre'];
  	$gerencia_nombre=$row['depe_nombre'];
  	$part_nombre=$row['part_nombre'];
  
	$data = array(
				'id' => $i,
				'id_pcta' => $id_pcta,
				'acc_esp' =>  $acc_esp,
				'acc_pp' => $acc_pp,
				'imputacion' => $imputacion,
				'partida' => $partida,
				'titulo' => utf8_encode($titulo),
				'accion' => utf8_encode($accion),
				'part_nombre' => utf8_encode($part_nombre),
				'gestor' => $gestor,
				'costo' =>  $costo,
				'monto_pcta' => $monto_pcta,
				'gerencia_id' => $gerencia_id,
				'asunto_id' => $asunto_id,
				'rif' => $rif,
				'asunto_desc' => utf8_encode($asunto_desc),
				'gerencia_nombre' => utf8_encode($gerencia_nombre)
			);
	
	$listaPartida[$i] = $data;
	$i++;
   
   }
   echo json_encode(array("listaPartida" => $listaPartida));
  
?>
