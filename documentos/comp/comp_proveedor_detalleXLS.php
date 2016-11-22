<?php 

  ob_start();
  session_start();
  require_once("../../includes/excel.php");
  require_once("../../includes/excel-ext.php");
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");

  if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
  }
  
  $usuario = $_SESSION['login'];
  $user_perfil_id = $_SESSION['user_perfil_id'];
  $pres_anno = $_SESSION['an_o_presupuesto'];
  $press_anno = $_SESSION['an_o_presupuesto'];
  //$pres_anno = 2014;
  //$press_anno = 2014;
if ($_POST['hid_validar']==2) {

	$fecha_ini=substr($_POST['txt_inicio'],6,4)."-".substr($_POST['txt_inicio'],3,2)."-".substr($_POST['txt_inicio'],0,2);
	$fecha_fin=substr($_POST['hid_hasta_itin'],6,4)."-".substr($_POST['hid_hasta_itin'],3,2)."-".substr($_POST['hid_hasta_itin'],0,2)." 23:59:59";

	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo7="";
	$wheretipo8="";
	$wheretipo9="";

if (strlen($_POST['txt_cod'])>5) {
	$wheretipo7 = " and sc.comp_id='".$_POST['txt_cod']."' ";
}
	
if (strlen($_POST['tipo_compromiso'])>2) {
	$wheretipo2 = " and sc.comp_asunto='".$_POST['tipo_compromiso']."' ";
}

if ($_POST['tipo_actividad']<>"--") {
	$wheretipo8 = " and sc.id_actividad='".$_POST['tipo_actividad']."' ";
}

if (strlen($_POST['proyac'])>8) {
	list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	$wheretipo3 = " and t1.comp_acc_pp='".$proy."' and comp_acc_esp='".$especif."' ";
}

if (strlen($_POST['rif_proveedor'])>2) {
	$wheretipo4 = " and (upper(sc.rif_sugerido) like upper('%".$_POST['rif_proveedor']."%') or upper(sc.beneficiario) like upper('%".$_POST['rif_proveedor']."%'))";
}

if (strlen($_POST['txt_clave'])>0) {
	$wheretipo5 = " and upper(sc.comp_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
}

if ($_POST['estatus_compromiso']<>'0'){
	$wheretipo6 = " and sc.comp_estatus='".$_POST['estatus_compromiso']."' ";
}
	
if (strlen($fecha_ini)>3) {
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' ";
	$wheretipo9=" AND caus_fecha>='".$fecha_ini."' AND caus_fecha<='".$fecha_fin."' ";

	$comp_asociados="select t1.comp_id,max(t1.comp_fecha) as fecha_mod
from sai_comp_imputa_traza t1, sai_comp sc 
where t1.comp_id=sc.comp_id and 
to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and
to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' 
".$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." group by t1.comp_id 
order by fecha_mod";

}else{
$comp_asociados=" 
select distinct(t1.comp_id) as comp_id,sc.comp_fecha as fecha_doc,
to_date(to_char(sc.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,0 as infocentro, '' as tipo_obra,comp_sub_espe,comp_monto as monto,comp_acc_pp,comp_acc_esp,
cpas_nombre as asunto,empl_nombres,empl_apellidos,t4.depe_nombre as dependencia,t5.nombre as actividad,comp_estatus,rif_sugerido,sc.esta_id,comp_observacion,comp_monto_solicitado,localidad,sc.comp_documento,sc.comp_descripcion
,t6.nombre as evento,sc.beneficiario,
to_date(to_char(sc.fecha_inicio, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_i,to_date(to_char(sc.fecha_fin, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_f
from sai_comp_imputa t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id ".$wheretipo4.")
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$press_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$press_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$press_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$press_anno."')
left outer join sai_compromiso_asunt t2 on (sc.comp_asunto=t2.cpas_id)
left outer join sai_empleado t3 on (sc.usua_login=t3.empl_cedula)
left outer join sai_dependenci t4 on (sc.comp_gerencia=t4.depe_id )
left outer join sai_tipo_actividad t5 on (t5.id=sc.id_actividad)
left outer join sai_tipo_evento t6 on (t6.id=sc.id_evento)
where sc.esta_id=10 ".$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." 
order by comp_sub_espe";
}

$resultado_set_most_or=pg_query($conexion,$comp_asociados) or die("Error al consultar la descripcion del compromiso");  



	$f = 0;
	$c = 0;
	
	$contenido[$f][0]=utf8_decode("RESULTADO DE LA BÙSQUEDA DE COMPROMISOS");
	$f++;
	
	$contenido[$f][$c]=utf8_decode("Código del Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Fecha");$c++;
	$contenido[$f][$c]=utf8_decode("Elaborado Por");$c++;
	$contenido[$f][$c]=utf8_decode("Unidad Solicitante");$c++;
	$contenido[$f][$c]=utf8_decode("Punto de Cuenta");$c++;
	$contenido[$f][$c]=utf8_decode("Asunto");$c++;
	$contenido[$f][$c]=utf8_decode("Estatus");$c++;
	$contenido[$f][$c]=utf8_decode("Nº Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Proveedor");$c++;
	$contenido[$f][$c]=utf8_decode("CI/RIF");$c++;
	$contenido[$f][$c]=utf8_decode("Centro Gestor");$c++;
	$contenido[$f][$c]=utf8_decode("Cestro de Costo");$c++;
	$contenido[$f][$c]=utf8_decode("Partida");$c++;
	$contenido[$f][$c]=utf8_decode("Monto Solicitado");$c++;
	$contenido[$f][$c]=utf8_decode("Descripción");$c++;
	$contenido[$f][$c]=utf8_decode("Tipo Evento");$c++;	
	$contenido[$f][$c]=utf8_decode("Tipo Actividad");$c++;
	$contenido[$f][$c]=utf8_decode("Estado");$c++;
	$contenido[$f][$c]=utf8_decode("Infocentro");$c++;
	$contenido[$f][$c]=utf8_decode("Nº Participantes");$c++;
    $contenido[$f][$c]=utf8_decode("Duración de la actividad");$c++;	
	$contenido[$f][$c]=utf8_decode("Observación");$c++;
	$contenido[$f][$c]=utf8_decode("Fecha de Reporte");$c++;	
	$contenido[$f][$c]=utf8_decode("Monto Causado");$c++;
	$contenido[$f][$c]=utf8_decode("Documentos Causado");$c++;

	$f++;
	$c=0;
	$imprimir="";
	 if (strlen($fecha_ini)<3) {
  while ($rowor=pg_fetch_array($resultado_set_most_or))  {
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];

	 if ($rowor['localidad']>0){
	   $edo_vzla="select nombre from safi_edos_venezuela where id='".$rowor['localidad']."'";
	   $resultado_info=pg_exec($conexion,$edo_vzla);
	   if ($rowi=pg_fetch_array($resultado_info)){
	      $edo_id=$rowi['nombre'];
	   }
	  }else{$edo_id="N/A";}
	  $proveedor=explode(":",$rowor['rif_sugerido']);
      $rif=$rowor['rif_sugerido'];
	  $nombre=$rowor['beneficiario'];
	  $info_adicional=$rowor['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  $observacion=substr($info_adicional,$posicion+1);
	
		$contenido[$f][$c]=$rowor['comp_id'];$c++;
 	 	$contenido[$f][$c]=cambia_esp(trim($rowor['fecha_doc']));$c++;
 	 	$contenido[$f][$c]=$rowor['empl_nombres']." ".$rowor['empl_apellidos'];$c++;
 	 	$contenido[$f][$c]=$rowor['dependencia'];$c++;
	 	if ($rowor['pcta']=='0'){
	 	$contenido[$f][$c]="N/A";$c++;
	 	}else{
	 		 $contenido[$f][$c]=$row['pcta'];$c++;
	 	}
		$contenido[$f][$c]=$rowor['asunto'];$c++;
		$contenido[$f][$c]=$rowor['comp_estatus'];$c++;
		$contenido[$f][$c]=$rowor['comp_documento'];$c++;
		$contenido[$f][$c]=$nombre;$c++;
		$contenido[$f][$c]=$rif;$c++;
		$contenido[$f][$c]=$rowor['centrogestor'];$c++;
		$contenido[$f][$c]=$rowor['centrocosto'];$c++;
		$contenido[$f][$c]=$rowor['comp_sub_espe'];$c++;

	    $contenido[$f][$c]=(double)($rowor['monto']);$c++;
	    $contenido[$f][$c]=$rowor['comp_descripcion'];$c++;
	    $contenido[$f][$c]=$rowor['evento'];$c++;
		$contenido[$f][$c]=$rowor['actividad'];$c++;
		$contenido[$f][$c]=$edo_id;$c++;
		$contenido[$f][$c]=$infocentro;$c++;
		$contenido[$f][$c]=$participante;$c++;
		$contenido[$f][$c]=cambia_esp($rowor['fecha_i'])."-".cambia_esp($rowor['fecha_f']);$c++;
		$contenido[$f][$c]=$observacion;$c++;
		$contenido[$f][$c]=$rowor['fecha_reporte'];$c++;
	
	
      $monto_causado=0;
	  $anno_pres=$press_anno;
	  $sopgs_id="";
  		//MONTOS CAUSADOS
		$query_causado_sopg="SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sopg_id ".	
					"FROM sai_sol_pago sp ".
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sopg_id=caus_docu_id ".$wheretipo9.") ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' 
					AND scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."') ".
					"WHERE  sp.esta_id<>15 and comp_id='".$rowor['comp_id']."' GROUP BY sopg_id ";
	
        $resultadoMontosCausados=pg_query($query_causado_sopg) or die("Error en los montos causados sopg");
	    while ($row_causado=pg_fetch_array($resultadoMontosCausados)){
	      $sopgs_id=  $sopgs_id." ".$row_causado['sopg_id'];	   
	      $monto_causado=$monto_causado+$row_causado['monto_causado'];
	    }
		
		$query_causado_codi="SELECT monto_causado,documento FROM (SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sci.comp_id as documento ".
					"FROM sai_codi sci ".	
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sci.comp_id=caus_docu_id ".$wheretipo9.") ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' AND 
					 scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."' ) ".
					" WHERE  sci.esta_id<>15  and nro_compromiso='".$rowor['comp_id']."' 
					GROUP BY sci.comp_id ) AS A WHERE monto_causado<>0";
			
		$resultadoMontosCausadosCodi=pg_query($query_causado_codi) or die("Error en los montos causados codi");
	    while ($row_causadoCodi=pg_fetch_array($resultadoMontosCausadosCodi)){
	      $sopgs_id=  $sopgs_id." ".$row_causadoCodi['documento'];
       	  $monto_causado=$monto_causado+$row_causadoCodi['monto_causado'];
	    }
	
		$contenido[$f][$c]=$monto_causado;$c++;
		$contenido[$f][$c]=$sopgs_id;$c++;

		$f++;
	    $c=0;
	    $vacio='';
	    
  }
	
 }else{
  	
   while ($row=pg_fetch_array($resultado_set_most_or))  {
   	$fechacomp=$row['fecha_mod'];
   	$idcomp=$row['comp_id'];
   	$comp_asociados=" 
select distinct(t1.comp_id) as comp_id,t1.comp_fecha as fecha_doc,
to_date(to_char(t1.comp_fecha, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,0 as infocentro, '' as tipo_obra,comp_sub_espe,comp_monto as monto,comp_acc_pp,comp_acc_esp,
cpas_nombre as asunto,empl_nombres,empl_apellidos,t4.depe_nombre as dependencia,t5.nombre as actividad,comp_estatus,rif_sugerido,sc.esta_id,comp_observacion,comp_monto_solicitado,localidad,sc.comp_documento,sc.comp_descripcion
,t6.nombre as evento,sc.beneficiario,
to_date(to_char(sc.fecha_inicio, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_i,to_date(to_char(sc.fecha_fin, 'YYYY/MM/DD'),'YYYY/MM/DD') as fecha_f
from sai_comp_imputa_traza t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id ".$wheretipo4.")
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$press_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$press_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$press_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$press_anno."')
left outer join sai_compromiso_asunt t2 on (sc.comp_asunto=t2.cpas_id)
left outer join sai_empleado t3 on (sc.usua_login=t3.empl_cedula)
left outer join sai_dependenci t4 on (sc.comp_gerencia=t4.depe_id )
left outer join sai_tipo_actividad t5 on (t5.id=sc.id_actividad)
left outer join sai_tipo_evento t6 on (t6.id=sc.id_evento)
where sc.esta_id=10  and t1.comp_fecha='".$fechacomp."' and t1.comp_id='".$idcomp."'".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo5.$wheretipo6.$wheretipo7.$wheretipo8." 
order by comp_sub_espe";
   	 	//echo $comp_asociados."<br>";
   	$resultado_comp_asociados=pg_query($conexion,$comp_asociados) or die("Error al consultar detalle del compromiso");
  	
 while ($rowor=pg_fetch_array($resultado_comp_asociados))  {
   	
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];

	 if ($rowor['localidad']>0){
	   $edo_vzla="select nombre from safi_edos_venezuela where id='".$rowor['localidad']."'";
	   $resultado_info=pg_exec($conexion,$edo_vzla);
	   if ($rowi=pg_fetch_array($resultado_info)){
	      $edo_id=$rowi['nombre'];
	   }
	  }else{$edo_id="N/A";}
	  $proveedor=explode(":",$rowor['rif_sugerido']);
      $rif=$rowor['rif_sugerido'];
	  $nombre=$rowor['beneficiario'];
	  $info_adicional=$rowor['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  $observacion=substr($info_adicional,$posicion+1);
	  

		$fecha_doc=$rowor['fecha_doc'];
		$contenido[$f][$c]=$rowor['comp_id'];$c++;
 	 	$contenido[$f][$c]=cambia_esp(trim($fecha_doc));$c++;
 	 	$contenido[$f][$c]=$rowor['empl_nombres']." ".$rowor['empl_apellidos'];$c++;
 	 	$contenido[$f][$c]=$rowor['dependencia'];$c++;
	 	if ($rowor['pcta']=='0'){
	 	$contenido[$f][$c]="N/A";$c++;
	 	}else{
	 		 $contenido[$f][$c]=$row['pcta'];$c++;
	 	}
	 	
		$contenido[$f][$c]=$rowor['asunto'];$c++;
		$contenido[$f][$c]=$rowor['comp_estatus'];$c++;
		$contenido[$f][$c]=$rowor['comp_documento'];$c++;
		$contenido[$f][$c]=$nombre;$c++;
		$contenido[$f][$c]=$rif;$c++;
		$contenido[$f][$c]=$rowor['centrogestor'];$c++;
		$contenido[$f][$c]=$rowor['centrocosto'];$c++;
		$contenido[$f][$c]=$rowor['comp_sub_espe'];$c++;

	    $contenido[$f][$c]=(double)($rowor['monto']);$c++;
	    $contenido[$f][$c]=$rowor['comp_descripcion'];$c++;
	    $contenido[$f][$c]=$rowor['evento'];$c++;
		$contenido[$f][$c]=$rowor['actividad'];$c++;
		$contenido[$f][$c]=$edo_id;$c++;
		$contenido[$f][$c]=$infocentro;$c++;
		$contenido[$f][$c]=$participante;$c++;
		$contenido[$f][$c]=cambia_esp($rowor['fecha_i'])."-".cambia_esp($rowor['fecha_f']);$c++;
		$contenido[$f][$c]=$observacion;$c++;
		$contenido[$f][$c]=$rowor['fecha_reporte'];$c++;
		
 	    $monto_causado=0;
	    $anno_pres=$pres_anno;
	  	$sopgs_id="";
   		//MONTOS CAUSADOS
		$query_causado_sopg="SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sopg_id ".	
					"FROM sai_sol_pago sp ".
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sopg_id=caus_docu_id ".$wheretipo9." ) ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' 
					AND scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."') ".
					"WHERE  sp.esta_id<>15 and comp_id='".$rowor['comp_id']."' GROUP BY sopg_id ";
	
        $resultadoMontosCausados=pg_query($query_causado_sopg) or die("Error en los montos causados sopg");
	    while ($row_causado=pg_fetch_array($resultadoMontosCausados)){
	      $sopgs_id=  $sopgs_id." ".$row_causado['sopg_id'];
	      $monto_causado=$monto_causado+$row_causado['monto_causado'];
	    }
		
		$query_causado_codi="SELECT monto_causado,documento FROM (SELECT ".
					"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado,sci.comp_id as documento ".
					"FROM sai_codi sci ".	
					"left outer join sai_causado sc on (sc.pres_anno = ".$anno_pres." AND sc.esta_id <> 15 AND sc.esta_id <> 2 AND sci.comp_id=caus_docu_id ".$wheretipo9." ) ".
					"left outer join sai_causad_det scd on (sc.caus_id = scd.caus_id AND sc.pres_anno = scd.pres_anno AND scd.cadt_id_p_ac = '".$rowor['comp_acc_pp']."' AND scd.cadt_cod_aesp = '".$rowor['comp_acc_esp']."' AND 
					 scd.cadt_abono='1' AND scd.part_id NOT LIKE '4.11.0%' AND scd.part_id ='".$rowor['comp_sub_espe']."' ) ".
					" WHERE  sci.esta_id<>15  and nro_compromiso='".$rowor['comp_id']."' 
					GROUP BY sci.comp_id
					) AS A WHERE monto_causado<>0";
			//echo $query_causado_codi;
		$resultadoMontosCausadosCodi=pg_query($query_causado_codi) or die("Error en los montos causados codi");
	    while ($row_causadoCodi=pg_fetch_array($resultadoMontosCausadosCodi)){
	      $sopgs_id=  $sopgs_id." ".$row_causadoCodi['documento'];
       	  $monto_causado=$monto_causado+$row_causadoCodi['monto_causado'];
	    }
		$contenido[$f][$c]=$monto_causado;$c++;
		$contenido[$f][$c]=$sopgs_id;$c++;
		$f++;
	    $c=0;
	    $vacio='';

   
  }//fin del while que obtiene los datos de la consulta
   }
   
 }}

	      	
	     
createExcel("reporte-proveedor.xls",$contenido);
?>
