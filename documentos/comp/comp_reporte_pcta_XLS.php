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

if ($_POST['hid_validar']==2) {

	$fecha_ini=substr($_POST['txt_inicio'],6,4)."-".substr($_POST['txt_inicio'],3,2)."-".substr($_POST['txt_inicio'],0,2);
	$fecha_fin=substr($_POST['hid_hasta_itin'],6,4)."-".substr($_POST['hid_hasta_itin'],3,2)."-".substr($_POST['hid_hasta_itin'],0,2)." 23:59:59";

	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	$wheretipo7="";
	$wheretipo8="";
	$wheretipo9="";
    $where1="";
    $where2="";
if (strlen($fecha_ini)>2) {
	$wheretipo1 = "and to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' and to_date(to_char(t1.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin."' ";
}

if (strlen($_POST['txt_cod'])>5) {
	$wheretipo2 = " and sc.pcta_id='".$_POST['txt_cod']."' ";
}

if (strlen($_POST['proyac'])>8) {
	list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
	$wheretipo3 = " and t1.comp_acc_pp='".$proy."' and comp_acc_esp='".$especif."' ";
}

if (strlen($_POST['txt_partida'])>2) {
	$wheretipo4 = " and t1.comp_sub_espe='".$_POST['txt_partida']."' ";
}

if (strlen($_POST['tipo_compromiso'])>2) {
	$wheretipo5 = " and sc.comp_asunto='".$_POST['tipo_compromiso']."' ";
}

if (strlen($_POST['rif_proveedor'])>2) {
	$wheretipo8 = " and upper(sc.rif_sugerido) like upper('%".$_POST['rif_proveedor']."%') ";
}
if (strlen($_POST['txt_clave'])>0) {
	$wheretipo6 = " and upper(sc.comp_descripcion) like '%".cadenaAMayusculas($_POST['txt_clave'])."%' ";
}


//Deberìa separarse en Compromisos Aislados y no aislados
//los comp no aislados o asociados a pcta (sc.pcta_id=t1.pcta_id)
$comp_asociados="select distinct t1.comp_id as comp_id, EXTRACT(DAY FROM t1.comp_fecha)||'/'||EXTRACT(month FROM t1.comp_fecha)||'/'||EXTRACT(Year FROM t1.comp_fecha) as fecha_doc, t1.comp_fecha as fecha,sc.pcta_id as pcta,
coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as centrogestor, to_char(sc.fecha_reporte, 'DD/MM/YYYY') as fecha_reporte,
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as centrocosto,sc.esta_id,0 as infocentro, '' as tipo_obra,
comp_sub_espe as partida,comp_monto as monto,localidad, t12.nombre as evento
from sai_comp_traza_reporte t1 
left outer join sai_comp sc on (t1.comp_id=sc.comp_id and length(sc.pcta_id)>2)
left outer join sai_acce_esp t8 on (t1.comp_acc_pp=t8.acce_id and t1.comp_acc_esp=t8.aces_id and t8.pres_anno='".$pres_anno."')
left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id and t10.pres_anno='".$pres_anno."') 
left outer join sai_proy_a_esp t9 on(t1.comp_acc_pp=t9.proy_id and t1.comp_acc_esp=t9.paes_id and t9.pres_anno='".$pres_anno."') 
left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id and pre_anno='".$pres_anno."')
left outer join sai_tipo_evento t12 on (t12.id=sc.id_evento)
where sc.esta_id<>2 ".$wheretipo1.$wheretipo2.$wheretipo3.$wheretipo4.$wheretipo5.$wheretipo6.$wheretipo8." 
order by fecha"; 
$resultado_set_most_or=pg_query($conexion,$comp_asociados) or die("Error al consultar la descripcion del compromiso");  
    
	$f = 0;
	$c = 0;
	
	$contenido[$f][0]=utf8_decode("RESULTADO DE LA BÙSQUEDA DE LOS COMPROMISOS PROVENIENTES DE PUNTOS DE CUENTA");
	$f++;
	
	$contenido[$f][$c]=utf8_decode("Código del Documento");$c++;
	$contenido[$f][$c]=utf8_decode("Estatus Documento");$c++;
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
	$f++;
	$c=0;
	 while($rowor=pg_fetch_array($resultado_set_most_or))  
	 {
	$pcta=$rowor['pcta'];
	$comp=$rowor['comp_id'];
	$sql="select t1.*,cpas_nombre as asunto,t3.nombre as tipo_doc,empl_nombres,empl_apellidos,t5.depe_nombre as dependencia,t7.nombre as actividad,comp_estatus,rif_sugerido,t1.esta_id,comp_observacion,t8.nombre as evento
	 	  from sai_comp t1,sai_compromiso_asunt,sai_tipo_documento t3, sai_empleado t4,sai_dependenci t5,sai_tipo_actividad t7,sai_tipo_evento t8
		  WHERE comp_id='".$comp."' and comp_asunto=cpas_id and t3.id=t1.comp_tipo_doc and t1.usua_login=t4.empl_cedula and comp_gerencia=t5.depe_id and t7.id=id_actividad and t8.id=id_evento";
	//echo $sql;
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado))
	{
	  $proveedor=explode(":",$row['rif_sugerido']);
      $rif=$row['rif_sugerido'];//$proveedor[0];
	  $nombre=$row['beneficiario'];//$proveedor[1];
	  $info_adicional=$row['comp_observacion'];
	  $longitud=strlen($info_adicional);
	  $info_adicional=substr($info_adicional,1,$longitud);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $edo_id=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $infocentro=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  $posicion2 = strpos($info_adicional, "*");
	  
	  $participante=substr($info_adicional,$posicion+1,($posicion2-$posicion-1));
	  $info_adicional=substr($info_adicional,$posicion2+1);
	  $posicion = strpos($info_adicional, ":");
	  
	  $observacion=substr($info_adicional,$posicion+1);

	  }else{
	  	    $sql="select t1.*,pcas_nombre as asunto,empl_nombres,empl_apellidos,t5.depe_nombre as dependencia,
	  	    'N/A' as comp_estatus,rif_sugerido,t1.esta_id,pcta_observacion, 'Punto de Cuenta' as tipo_doc,
	  	     '".$pcta."' as comp_documento,tipo_obra as actividad,pcta_justificacion as comp_descripcion,esta_nombre
	 	    from sai_pcuenta t1,sai_pcta_asunt,sai_empleado t4,sai_dependenci t5,sai_estado t6
		     WHERE pcta_id='".$pcta."' and pcta_asunto=pcas_id and t1.usua_login=t4.empl_cedula and pcta_gerencia=t5.depe_id and t6.esta_id=t1.esta_id";
	  	    $resultado=pg_query($conexion,$sql);
	        if ($row=pg_fetch_array($resultado))
			{
			 $proveedor=explode(":",$row['rif_sugerido']);
	  		 $rif=$proveedor[0];
	  	 	 $nombre=$proveedor[1];
	  	    if($rowor['infocentro']<>''){
	 	      $query_infocentro="select t1.nombre as nombre_info,t2.nombre as nombre_edo from safi_infocentro t1,safi_edos_venezuela t2 where edo_id=t2.id and t1.id='".$rowor['infocentro']."'";
	  	      $resultado_info=pg_exec($conexion,$query_infocentro);
	  	      if ($rowi=pg_fetch_array($resultado_info)){
	  	      $edo_id=$rowi['nombre_edo'];
	  	      $infocentro=$rowi['nombre_info'];
	  	     
	  	      }
	  	    }else{
	  	    	 $estado="N/A";
	  	         $infocentro="N/A";
	  	    }
	  	     $observacion="";
	         }
	  }
	  

	     $query_partidas="select comp_sub_espe as partida,comp_monto as monto from sai_comp_traza_reporte t1 where comp_id='".$rowor['comp_id']."' and comp_fecha='".$rowor['fecha']."' ".$wheretipo4." order by comp_fecha,comp_sub_espe";
	     $descripcion=strip_tags($row['comp_descripcion']);
	 	if ($row['esta_id']==15){$estado="Anulado";}else {$estado="Activo";}

		$fecha_doc=$rowor['fecha_doc'];
		$contenido[$f][$c]=$rowor['comp_id'];$c++;
 	 	$contenido[$f][$c]=$estado;$c++;
 	 	$contenido[$f][$c]=$fecha_doc;$c++;
	 	$contenido[$f][$c]=$row['empl_nombres']." ".$row['empl_apellidos'];$c++;
	 	$contenido[$f][$c]=$row['dependencia'];$c++;
	 	if ($row['pcta_id']=='0'){
	 	$contenido[$f][$c]="N/A";$c++;
	 	}else{
	 		 $contenido[$f][$c]=$row['pcta_id'];$c++;
	 	}
	 	
		$contenido[$f][$c]=$row['asunto'];$c++;
		$contenido[$f][$c]=$row['comp_estatus'];$c++;
		$contenido[$f][$c]=$row['comp_documento'];$c++;
		$contenido[$f][$c]=$nombre;$c++;
		$contenido[$f][$c]=$rif;$c++;
		$contenido[$f][$c]=$rowor['centrogestor'];$c++;
		$contenido[$f][$c]=$rowor['centrocosto'];$c++;

	    $contador=0;
        $contenido[$f][$c]=$rowor['partida'];$c++;
	    $contenido[$f][$c]=(double)($rowor['monto']);$c++;
		$contenido[$f][$c]=$descripcion;$c++;
		$contenido[$f][$c]=$rowor['evento'];$c++;
		$contenido[$f][$c]=$row['actividad'];$c++;
		$contenido[$f][$c]=$edo_id;$c++;
		$contenido[$f][$c]=$infocentro;$c++;
		$contenido[$f][$c]=$participante;$c++;
		$contenido[$f][$c]=cambia_esp($row['fecha_inicio'])."-".cambia_esp($row['fecha_fin']);$c++;
		$contenido[$f][$c]=$observacion;$c++;
		$contenido[$f][$c]=$rowor['fecha_reporte'];;$c++;
		
		$f++;
	    $c=0;
	    $vacio='';
	    while($row_partidas=pg_fetch_array($resultado_partidas)){
    	
	   	$contenido[$f][$c]=$rowor['comp_id'];$c++;
	 	$contenido[$f][$c]=$estado;$c++;
	 	$fecha_doc=$rowor['fecha_doc'];
	 	$contenido[$f][$c]=$fecha_doc;$c++;
	 	$contenido[$f][$c]=$row['empl_nombres']." ".$row['empl_apellidos'];$c++;
	 	$contenido[$f][$c]=$row['dependencia'];$c++;
	 	if ($row['pcta_id']=='0'){
	 	$contenido[$f][$c]="N/A";$c++;
	 	}else{
	 		 $contenido[$f][$c]=$row['pcta_id'];$c++;
	 	}
	 	$contenido[$f][$c]=$row['asunto'];$c++;
		$contenido[$f][$c]=$row['tipo_doc'];$c++;
		$contenido[$f][$c]=$row['comp_estatus'];$c++;
		$contenido[$f][$c]=$row['comp_documento'];$c++;
		$contenido[$f][$c]=$nombre;$c++;
		$contenido[$f][$c]=$rif;$c++;
		$contenido[$f][$c]=$rowor['centrogestor'];$c++;
		$contenido[$f][$c]=$rowor['centrocosto'];$c++;
	  
	   $contenido[$f][$c]=$row_partidas['partida'];$c++;
	   $contenido[$f][$c]=(double)($row_partidas['monto']);$c++;
	   $f++;
	   $c=0;
	
	 }
   
  }//fin del while que obtiene los datos de la consulta



}
 
createExcel("control-interno.xls",$contenido);
?>
