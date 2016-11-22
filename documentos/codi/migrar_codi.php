<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<?php
require_once("../../includes/conexion.php");

/*
,CASE WHEN substr(comp_doc_id,0,5)='sopg' THEN (select comp_id from sai_sol_pago where sopg_id = comp_doc_id) 
     when substr(comp_doc_id,0,5)='comp' then comp_doc_id  
     when substr(comp_doc_id,0,4)='n/a' then comp_doc_id 
     when substr(comp_doc_id,0,4)='N/A' then comp_doc_id
     END   as compromiso*/
/*CODI
 * 
 * $sql_ff="select t1.oid,comp_id,reng_comp,t1.cpat_id,rcomp_debe,rcomp_haber,t4.part_id
from sai_reng_comp t1,sai_convertidor t2,sai_causado t3, sai_causad_det t4 
where comp_id like 'codi%09' and (t1.cpat_id like '1.%' or t1.cpat_id like '6.%')
and t2.cpat_id=t1.cpat_id and t3.caus_id=t4.caus_id and t4.part_id=t2.part_id and caus_docu_id=t1.comp_id
and ((cadt_monto=rcomp_debe)or (cadt_monto=rcomp_haber*-1))
order by comp_id,reng_comp "; */
/*codas anulados*/
/*$sql_ff="select t1.oid,t1.comp_id,reng_comp,t1.cpat_id,t1.cpat_nombre,rcomp_debe,rcomp_haber,cadt_monto,t4.part_id,caus_docu_id
from sai_reng_comp t1,sai_causado t3, sai_causad_det t4, sai_comp_diario t5 ,sai_convertidor t2
where t1.comp_id like 'coda%10' and (t1.cpat_id like '1.%' or t1.cpat_id like '6.%')
and t5.comp_id=t1.comp_id and caus_docu_id=comp_doc_id  and t3.caus_id=t4.caus_id and t1.part_id is null
and comp_comen like 'A-%'
and comp_fec_emis like '2009-12%'
and t2.cpat_id=t1.cpat_id and t4.part_id=t2.part_id 
and cadt_monto=rcomp_haber
order by comp_id,reng_comp "; */
/*codas */
/*$sql_ff="select t1.oid,t1.comp_id,reng_comp,t1.cpat_id,t1.cpat_nombre,rcomp_debe,rcomp_haber,cadt_monto,t4.part_id,caus_docu_id
from sai_reng_comp t1,sai_causado t3, sai_causad_det t4, sai_comp_diario t5 ,sai_convertidor t2
where t1.comp_id like 'coda%10' and (t1.cpat_id like '1.%' or t1.cpat_id like '6.%')
and t5.comp_id=t1.comp_id and caus_docu_id=comp_doc_id  and t3.caus_id=t4.caus_id and t1.part_id is null
and comp_comen like 'C-%'
and comp_fec_emis like '2009-12%'
and t2.cpat_id=t1.cpat_id and t4.part_id=t2.part_id 
and ((cadt_monto=rcomp_debe)or (cadt_monto=rcomp_haber*-1))
order by comp_id,reng_comp "; 
echo $sql_ff."<br>";*/
/*$sql_info="select t4.id as info,t4.nombre,t3.id as edo,t3.nombre from safi_infocentro t4,safi_parroquia t1,safi_municipio t2,safi_edos_venezuela t3
where parroquia_id=t1.id and municipio_id=t2.id and t2.edo_id=t3.id order by t3.nombre";*/

//$sql_comp_aislados="select * from sai_pcta_traza where pcta_id like '%11' and pcta_fecha like '2011-01%'";
//$sql_comp_aislados="select * from sai_comp where comp_monto_solicitado=0 and comp_id like '%11' order by comp_id";
$sql_comp_aislados="select fecha_inicio,fecha_fin,  to_date(fecha_fin,'yyyy-mm-dd') - to_date(fecha_inicio,'yyyy-mm-dd') as dias,observaciones,t.id_tipo_personal as tp,
p.cedula,primer_apellido,segundo_apellido,primer_nombre,segundo_nombre,nombre
from ausencia a,personal p,trabajador t,tipopersonal tp
where 
tp.id_tipo_personal=t.id_tipo_personal and (t.id_tipo_personal=1 OR t.id_tipo_personal=10) and
a.id_personal=p.id_personal and t.id_personal=p.id_personal and
fecha_inicio like '2011%' and a.id_personal=t.id_personal and clase='P' order by tp.id_tipo_personal,p.cedula";
$res_ff=pg_exec($sql_comp_aislados);

$cont=0;
?>
<div align="center">LISTADO DE PERMISOS</div>
<table width="100%" border=1 align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
    <td class="normalNegroNegrita" align="center">C&eacute;dula</td>
	<td class="normalNegroNegrita" align="center">Apellido(s) y Nombres(s)</td>
	<td class="normalNegroNegrita" align="center">Tipo Personal</td>
	<td class="normalNegroNegrita" align="center">Fecha de Inicio</td>
	<td class="normalNegroNegrita" align="center">Fecha de Terminaci&oacute;n </td>
	<td class="normalNegroNegrita" align="center">D&iacute;as</td>
	<td class="normalNegroNegrita" align="center">Fecha de Reintegro</td>
	<td class="normalNegroNegrita" align="center">Motivo</td></tr><?
while($row=pg_fetch_array($res_ff)){
	$info_adicional=$row['observaciones'];
	list( $proy, $especif ) = split( 'MOTIVO:', $info_adicional );
	list( $a, $b ) = split( 'FECHA DE REINTEGRO:', $info_adicional );
//	echo $especif ."<br>"; 
	?>
	<tr>
	<td class="normalNegro" align="center"><?php echo $row['cedula'];?></td>
	<td class="normalNegro" align="center">
	<?php 
	echo $row['primer_apellido']." ".$row['segundo_apellido']." ".$row['primer_nombre']." ".$row['segundo_nombre'];?>
    <td class="normalNegro" align="center"><?php echo $row['nombre'];?></td>
	<td class="normalNegro" align="center"><?php echo $row['fecha_inicio'];?></td>
	<td class="normalNegro" align="center"><?php echo $row['fecha_fin'];?></td>
	<td class="normalNegro" align="center"><?php echo $row['dias'];?></td>
	<td class="normalNegro" align="center"><?php echo substr($b,0,10);?></td>
	<td class="normalNegro" align="center"><?php echo $especif;?></td></tr>
	<?php 
	
	 
	//  $longitud=strlen($info_adicional);
	 //
	//  $posicion = strpos($info_adicional, "MOTIVO:");
	//   $info_adicional=substr($info_adicional,$posicion+7,$longitud);
	 // $posicion2 = strpos($info_adicional, "*");
	  
	
//	echo $comp."<br>";
	/*$monto=0;
    $sql_comp="select sum(comp_monto) as solicitado from sai_comp_imputa where comp_id='".$comp."' ";
    echo $sql_comp;
    $res_comp=pg_exec($sql_comp);
   if($row_comp=pg_fetch_array($res_comp)){
   	$monto=$row_comp['solicitado'];
   } */
  $cont++;
	/*$f1=substr($row['pcta_fecha'],0,10);
	$fecha_anterior=$row['pcta_fecha2'];
	$f2=substr($row['pcta_fecha2'],11,8);
	$f3=$f1." ".$f2;
	echo "F1:".$f1."F2:".$f2."F3:".$f3."<br><br>";*/
	
	/*$up3="update sai_comp set comp_monto_solicitado='".$monto."' where comp_id='".$comp."' ";
	//$up3="update sai_pcta_traza set pcta_fecha2='".$f3."' where pcta_id='".$row['pcta_id']."' and pcta_fecha2='".$row['pcta_fecha2']."'";
	echo $up3."<br><br>";
	$res_in3=pg_exec($up3);*/
	
/*	$up1="update sai_pcta_imputa_traza set pcta_fecha='".$f3."' where pcta_id='".$row['pcta_id']."' and pcta_fecha like '".$fecha_anterior."%'";
	echo $up1."<br><br>";
	$res_in1=pg_exec($up1);*/
	//$up="update sai_reng_comp set part_id='".$row['part_id']."' where oid='".$row['oid']."'";
/*	$in="insert into sai_codi (comp_id,comp_fec,comp_comen,comp_fec_emis ,esta_id,depe_id,comp_doc_id,nro_referencia,nro_compromiso,fte_financiamiento) 
	values('".$row['comp_id']."','".$row['comp_fec']."','".$row['comp_comen']."','".$row['comp_fec_emis']."' ,'".$row['esta_id']."','".$row['depe_id']."',
	'".$row['comp_doc_id']."','".$row['nro_referencia']."','N/A','".$row['numero_reserva']."')";
	echo $in."<br>";//'".$row['comp']."'
	$res_in=pg_exec($in);*/
	}
	//echo(utf8_decode("LISTADO DE PERMISOS Nº ").$cont);
$sql_comp_aislados="select fecha_inicio,fecha_fin,  to_date(fecha_fin,'yyyy-mm-dd') - to_date(fecha_inicio,'yyyy-mm-dd') as dias,observaciones,t.id_tipo_personal as tp,
p.cedula,primer_apellido,segundo_apellido,primer_nombre,segundo_nombre,nombre
from ausencia a,personal p,trabajador t,tipopersonal tp
where 
tp.id_tipo_personal=t.id_tipo_personal and (t.id_tipo_personal=1 OR t.id_tipo_personal=10) and
a.id_personal=p.id_personal and t.id_personal=p.id_personal and
fecha_inicio like '2011%' and a.id_personal=t.id_personal and clase='R' order by tp.id_tipo_personal,p.cedula";
$res_ff=pg_exec($sql_comp_aislados);
	
$cont=0;?>
</table><br><br><br></br>
<div align="center">LISTADO DE REPOSOS MEDICOS</div>
<table width="100%" border=1 align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
    <td class="normalNegroNegrita" align="center">C&eacute;dula</td>
	<td class="normalNegroNegrita" align="center">Apellido(s) y Nombres(s)</td>
	<td class="normalNegroNegrita" align="center">Tipo Personal</td>
	<td class="normalNegroNegrita" align="center">Centro de Salud</td>
	<td class="normalNegroNegrita" align="center">Fecha de Inicio</td>
	<td class="normalNegroNegrita" align="center">Fecha de Terminaci&oacute;n </td>
	<td class="normalNegroNegrita" align="center">D&iacute;as</td>
	<td class="normalNegroNegrita" align="center">Fecha de Reintegro</td>
	<td class="normalNegroNegrita" align="center">Motivo</td>
	<td class="normalNegroNegrita" align="center">Convalidado</td></tr><?
while($row=pg_fetch_array($res_ff)){
	$info_adicional=$row['observaciones'];
	list( $proy, $especif ) = split( 'MOTIVO:', $info_adicional );
	list( $a, $b ) = split( 'FECHA DE REINTEGRO:', $info_adicional );
	list( $c, $d ) = split( 'CENTRO DE SALUD:', $info_adicional );
	list( $e, $f ) = split( 'CONVALIDADO:', $info_adicional );
	  $posicion1 = strpos($especif, "/");
	  $especif=substr($especif,0,$posicion1);
	  $posicion = strpos($d, "/");
	  $d=substr($d,0,$posicion-1);
	?>
	<tr >
		<td class="normalNegro" align="center"><?php echo $row['cedula'];?></td>
	<td class="normalNegro" align="center">
	<?php 
	echo $row['primer_apellido']." ".$row['segundo_apellido']." ".$row['primer_nombre']." ".$row['segundo_nombre'];?>
<td class="normalNegro" align="center"><?php echo $row['nombre'];?></td>
	<td class="normalNegro" align="center"><?php echo $d;?></td>
	<td class="normalNegro" align="center"><?php echo $row['fecha_inicio'];?></td>
	<td class="normalNegro" align="center"><?php echo $row['fecha_fin'];?></td>
	<td class="normalNegro" align="center"><?php echo $row['dias'];?></td>
	<td class="normalNegro" align="center"><?php echo substr($b,0,10);?></td>
	<td class="normalNegro" align="center"><?php echo $especif;?></td>
	<td class="normalNegro" align="center"><?php echo $f;?></td></tr>
	<?php 
	

  $cont++;

	}
//	echo(utf8_decode("LISTADO DE REPOSOS MEDICOS Nº ").$cont);
	
?>