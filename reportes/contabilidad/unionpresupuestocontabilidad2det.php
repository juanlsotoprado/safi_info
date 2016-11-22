<?php
require_once("../../includes/conexion.php");

/*Borrar data de sai_cue_pat_saldo y sai_cue_pat_saldo2*/

//echo "dddddd".$fechaFfin." ggg ".$fechaIinicio;

$fechaIinicio= $_REQUEST["hid_desde_itin"];
$fechaFfin=$_REQUEST["hid_hasta_itin"];
$login=$_SESSION['login'];

$sql = "drop table sai_prescontableinicial2_".$login."  ";
$resultado =pg_query($conexion,$sql) ;

$sql = "drop table sai_prescontable4_".$login."  ";
$resultado =pg_query($conexion,$sql) ;

$sql = "drop table sai_prescontable5_".$login."  ";
$resultado =pg_query($conexion,$sql) ;






$sql = " CREATE  TEMPORARY TABLE sai_prescontableinicial2_".$login."   (caus_id varchar(50),caus_docu_id varchar(50),part_id varchar(50),
monto_pres float8,proyecto varchar(50),especifica varchar(50),comp_id varchar(50), cpat_id varchar(50),monto_ctble float8,
comp_fec date,fecha_anulacion date, esta_id varchar(2), fechaactual int4,fechaentrada int4, fechaanulacion varchar(20), posicion int2,numero_reserva varchar(100),doc2 varchar(50))";

/*$sql = " CREATE  TEMPORARY TABLE sai_prescontable_".$login."   
(part_id varchar(50), posicion int,
monto_pres float8,proyecto varchar(50),especifica varchar(50),monto_ctble float8, comp_fec date)";*/
//echo $sql."<br>";

$resultado = pg_query($conexion,$sql) or die("Error al crear temporal");






$sql = " CREATE  TEMPORARY TABLE sai_prescontable4_".$login."   (caus_id varchar(50),caus_docu_id varchar(50),part_id varchar(50),
monto_pres float8,proyecto varchar(50),especifica varchar(50),comp_id varchar(50), cpat_id varchar(50),monto_ctble float8,
comp_fec date,numero_reserva varchar(100),doc2 varchar(50),fecha_anulacion date, pos integer)";

/*$sql = " CREATE  TEMPORARY TABLE sai_prescontable_".$login."   
(part_id varchar(50), posicion int,
monto_pres float8,proyecto varchar(50),especifica varchar(50),monto_ctble float8, comp_fec date)";*/
//echo $sql."<br>";

$resultado = pg_query($conexion,$sql) or die("Error al crear temporal");







/*los activos-----nunca anulados*/





$sql = " select ca.paga_id as caus_id, ca.paga_docu_id as caus_docu_id, 
case substr(ca.paga_docu_id,1,4) when 'codi' then ca.paga_docu_id when 'tran' then pt.docg_id
when 'pgch' then  pc.docg_id else '' end as docg_id,
 substr(c.part_id,1,13) as part_id, cd.padt_monto as totalpres, 
 cd.padt_id_p_ac as proyecto, cd.padt_cod_aesp as especifica, 
 c.part_id as part_id_completa,ca.esta_id as estatuspres, 
 ca.paga_fecha,
 ''
 as fechatext,ca.fecha_anulacion,c.cpat_id ,dg.numero_reserva,s.comp_id, s.comp_fec,substring(s.comp_id,5),s.comp_fec_emis

from  sai_doc_genera dg,sai_doc_genera dg2, sai_comp_diario s ,sai_pagado_dt cd, sai_pagado ca left outer join sai_pago_transferencia pt on(pt.trans_id=ca.paga_docu_id) 
left outer join sai_pago_cheque pc on (pc.pgch_id=ca.paga_docu_id),sai_convertidor c 
where  
dg.docg_id=ca.paga_docu_id and 
 cd.paga_id=ca.paga_id  and 
 cd.pres_anno=ca.pres_anno and 
 cd.part_id=c.part_id and
 (dg2.docg_id=pt.docg_id or dg2.docg_id=pc.docg_id or dg2.docg_id=ca.paga_docu_id
 ) and
 
 
ca.esta_id='1'  and
ca.esta_id<>2 and
c.esta_id<>15 and  
((pt.docg_id=s.comp_doc_id and s.comp_id not like 'codi%') or 
  (pc.docg_id=s.comp_doc_id and s.comp_id not like 'codi%') or  
  (ca.paga_docu_id=s.comp_id )
  ) 
  and s.esta_id<>15  and
 cd.part_id not like '4.11%' 
 
 and dg2.docg_id in (select docg_id from sai_pago_transferencia where trans_id in 
      (select comp_doc_id from sai_comp_diario where comp_comen like
'T-%'  or comp_comen like 'P-%')
 union select docg_id from sai_pago_cheque where pgch_id in (select comp_doc_id from sai_comp_diario where comp_comen like
'T-%'  or comp_comen like 'P-%')
union select docg_id from sai_doc_genera where docg_id like 'codi%'
) 

 ";

$sql=$sql." and ca.paga_docu_id not in (select paga_docu_id from sai_pagado where esta_id=15)  ";

if($_REQUEST['proyac']!=null && $_REQUEST['proyac']!='0') {
	list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
	$sql=$sql." and cd.padt_id_p_ac='".$proy."' and cd.padt_cod_aesp='".$especif."'";
}
if($_REQUEST["partida"]!=null && $_REQUEST["partida"]!='' ){
	$sql=$sql." and substr(cd.part_id,1,13)= '".$_REQUEST["partida"]."'";
}
$sql=$sql." and  (c.cpat_id like '1.%' or c.cpat_id like '6.%')  and
ca.paga_fecha >= to_date('".$fechaIinicio."', 'DD MM YYYY') and ca.paga_fecha < to_date('".$fechaFfin."', 'DD MM YYYY')+1 ";


$sql=$sql." order by  4,3,2,18 desc,length(s.comp_id), substr(s.comp_id,3,length(s.comp_id)-2) ";



//echo $sql."<br>";
$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas1");
$caus_id="";
$caus_docu_id="";
$part_id="";
$montopres=0;
$proyecto="";
$especifica="";

$comp_fec="";
$comp_id="";

$actual="";
$anterior="";
while($rowor=pg_fetch_array($resultado_set_most_or))
{
	//if($actual!=''&&$anterior!='')
	if(($actual==''&&$anterior=='')||$actual!=$anterior){
		
		
	}
	$caus_id=$rowor['caus_id'];
	$caus_docu_id2=trim($rowor['caus_docu_id']);
	$part_id=$rowor['part_id'];
	$montopres=$rowor['totalpres'];
	$montopres2=$montopres;
	if(substr_count($montopres, '-')>0){
		$montopres2=(-1)*$montopres;
	}
	
	$caus_docu_id=$rowor['docg_id'];

	//echo $caus_docu_id."<br>";
	$proyecto=$rowor['proyecto'];
	$especifica=$rowor['especifica'];
	$vuelta=0;
	$proyecto=$rowor['proyecto'];
	$especifica=$rowor['especifica'];
	$vuelta=1;
	
	$sopg=$rowor['docg_id'];
	$actual=$caus_id.$caus_docu_id2.$part_id.$montopres.$caus_docu_id;
	//if($sopganterior==''||$sopganterior!=$caus_docu_id2){
	if(($actual==''&&$anterior=='')||$actual!=$anterior){
		//echo substr($caus_docu_id, 0, 4)."----".$caus_docu_id."<br>";
	
		/*$query1=" select s.comp_id, s.comp_fec,substring(s.comp_id,5) from sai_comp_diario s where s.esta_id<>15 and (s.comp_id='".$caus_docu_id."' or s.comp_doc_id='".$caus_docu_id."' or s.comp_doc_id in(select pgch_id from sai_pago_cheque where docg_id ='".$caus_docu_id."') or s.comp_doc_id in(select trans_id from sai_pago_transferencia where docg_id ='".$caus_docu_id."') )   ";

	$query1=$query1. "  order by length(s.comp_id), substr(s.comp_id,3,length(s.comp_id)-2) ";
	
	echo $query1." ;--NNN1 <br>";;
	$resultado_set_most=pg_query($conexion,$query1) or die("Error al consultar las Cuentas");

	while($rowo=pg_fetch_array($resultado_set_most)) {		*/
		
	$fecha_actual = substr($rowor['paga_fecha'], 0, 4).substr($rowor['paga_fecha'], 5, 2).substr($rowor['paga_fecha'], 8, 2);  
	 $fecha_entrada = substr($fechaFfin, 6, 4).substr($fechaFfin, 3, 2).substr($fechaFfin, 0, 2);;  
	 
	// echo $fecha_actual." ".$fecha_entrada ." lll".$rowor['caus_docu_id']."<br>";
		$comp_fec=$rowor['paga_fecha'];
		$comp_id=$rowor['comp_id'];
		
			
		$query2="select count(part_id) as tt from sai_convertidor where cpat_id=(select	cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' and  (cpat_id like '1.%' or cpat_id like '6.%')) ";
		//echo $query2."<br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
$tt=0;

/*	if($sopg==$sopganterior&&$rowor['estatuspres']=='15'&&$van==2){
						$van=0;
					}*/
$totalp=0;
			if($row=pg_fetch_array($resultado_set)) {
				$totalp=$row['tt'];
			}
		if($totalp==1){
		$query2="select rc.comp_id,sum(COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00))  as total  from sai_reng_comp rc where rc.comp_id ='".$rowor['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) ";
		//if(substr($caus_docu_id, 0, 4) =='codi' ){
			$query2=$query2." and rc.cpat_id in( select	cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."')";
		//}
		$query2=$query2." group by comp_id ";
	//	echo $query2." a<br>";
		}else{
			
			$query2="select count(rc.comp_id) as tt  from sai_reng_comp rc where rc.comp_id ='".$rowor['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."' or COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres*(-1)."')";
		//	echo $query2." b<br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
		if($row=pg_fetch_array($resultado_set)) {
				$totalp=$row['tt'];
			}
			if($totalp>0){
				if($totalp>1){
			$query2="select rc.comp_id,COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)  as total  from sai_reng_comp rc where rc.comp_id ='".$rowor['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."')";
				}else{
					$query2="select rc.comp_id,COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)  as total  from sai_reng_comp rc where rc.comp_id ='".$rowor['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."' or COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres*(-1)."')";
			
				}
		
		//	echo $query2." c<br>";
			}else{
				$query2="select rc.comp_id,sum(COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00))  as total  from sai_reng_comp rc where rc.comp_id ='".$rowor['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%'))";
		//}
		$query2=$query2." group by comp_id ";
				//echo $query2." d<br>";
			}
		}
		//echo $query2."final<br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
$tt=0;
			if($row=pg_fetch_array($resultado_set)) {
				
				if(substr_count($copiado, $comp_id.":".$part_id)<1) {	
					
					$queryins="insert into sai_prescontableinicial2_".$login."  values('".$caus_id."','".$caus_docu_id."','".$part_id."',".$montopres*1.0.",'".$proyecto."','".$especifica."','".$comp_id."','".$cpat_id."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'),
					null,".$rowor['estatuspres'].",".$fecha_actual.",".$fecha_entrada.",'".$rowor['fechatext']."',".$vuelta.",'".$rowor['numero_reserva']."','".$caus_docu_id2."')";
					
		$copiado=$copiado.$comp_id.":".$part_id.",";
				//$queryins="insert into sai_prescontable_".$login."  values('".$part_id."',".$rowor['posicion'].",".$montopres*1.0.",'".$proyecto."','".$especifica."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'))";
			//	echo $queryins.";<br>";;
				$resultado = pg_query($conexion,$queryins) or die("Error al registrar saldos".$queryins); 
				
				$tt=$row['total']+$tt;
				}
						
			}
			
			
			
	
		

	//}
	}
	$anterior=$caus_id.$caus_docu_id2.$part_id.$montopres.$caus_docu_id;
$sopganterior=$rowor['caus_docu_id'];


}




/******************************************** los alguna vez anulados ******************************/






/*

$sql = " select ca.paga_id as caus_id, ca.paga_docu_id as caus_docu_id, substr(c.part_id,1,13) as part_id, 
cd.padt_monto as totalpres, cd.padt_id_p_ac as proyecto, cd.padt_cod_aesp as especifica,
c.part_id as part_id_completa,ca.esta_id as estatuspres,
ca.paga_fecha,substr(ca.fecha_anulacion,1,4)||substr(ca.fecha_anulacion,6,2)||substr(ca.fecha_anulacion,9,2)  as fechatext,ca.fecha_anulacion,c.cpat_id
,dg.numero_reserva,s.comp_id, s.comp_fec,substring(s.comp_id,5)
from  sai_doc_genera dg,sai_comp_diario s , sai_pagado_dt cd, sai_pagado ca  ,sai_convertidor c
where  
dg.docg_id=ca.paga_docu_id and 
cd.paga_id=ca.paga_id and 
cd.pres_anno=ca.pres_anno and 
cd.part_id=c.part_id and
((ca.paga_docu_id=s.comp_id and s.comp_comen like 'T-%') or 
  (ca.paga_docu_id=s.comp_doc_id and s.comp_comen like 'T-%') or  
  (ca.paga_docu_id=s.comp_id  and s.comp_id like 'codi%')
  ) 
  and s.esta_id<>15  and
cd.pres_anno=".$_SESSION['an_o_presupuesto']." and 
ca.esta_id<>15  and
c.esta_id<>15 and  
 cd.part_id not like '4.11%'  
 ";

$sql=$sql." and ca.paga_docu_id  in (select paga_docu_id from sai_pagado where esta_id=15)   and ca.esta_id!='2' ";

if($_REQUEST['proyac']!=null && $_REQUEST['proyac']!='0') {
	list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
	$sql=$sql." and cd.padt_id_p_ac='".$proy."' and cd.padt_cod_aesp='".$especif."'";
}
if($_REQUEST["partida"]!=null && $_REQUEST["partida"]!='' ){
	$sql=$sql." and substr(cd.part_id,1,13)= '".$_REQUEST["partida"]."'";
}
$sql=$sql." and  (c.cpat_id like '1.%' or c.cpat_id like '6.%')  ";


$sql=$sql." order by 3,2 ";
*/





$sql = " select ca.paga_id as caus_id, ca.paga_docu_id as caus_docu_id, 
case substr(ca.paga_docu_id,1,4) when 'codi' then ca.paga_docu_id when 'tran' then pt.docg_id
when 'pgch' then  pc.docg_id else '' end as docg_id,
 substr(c.part_id,1,13) as part_id, cd.padt_monto as totalpres, 
 cd.padt_id_p_ac as proyecto, cd.padt_cod_aesp as especifica, 
 c.part_id as part_id_completa,ca.esta_id as estatuspres, 
 ca.paga_fecha,
 substr(ca.fecha_anulacion,1,4)||substr(ca.fecha_anulacion,6,2)||
substr(ca.fecha_anulacion,9,2) 
 as fechatext,ca.fecha_anulacion,c.cpat_id ,dg.numero_reserva,s.comp_id, s.comp_fec,substring(s.comp_id,5),s.comp_fec_emis

from  sai_doc_genera dg,sai_doc_genera dg2, sai_comp_diario s ,sai_pagado_dt cd, sai_pagado ca left outer join sai_pago_transferencia pt on(pt.trans_id=ca.paga_docu_id) 
left outer join sai_pago_cheque pc on (pc.pgch_id=ca.paga_docu_id),sai_convertidor c 
where  
dg.docg_id=ca.paga_docu_id and 
 cd.paga_id=ca.paga_id  and 
 cd.pres_anno=ca.pres_anno and 
 cd.part_id=c.part_id and
 (dg2.docg_id=pt.docg_id or dg2.docg_id=pc.docg_id or dg2.docg_id=ca.paga_docu_id
 ) and
 
ca.esta_id<>2  and
c.esta_id<>15 and  
((pt.docg_id=s.comp_doc_id and s.comp_id not like 'codi%') or 
  (pc.docg_id=s.comp_doc_id and s.comp_id not like 'codi%') or  
  (ca.paga_docu_id=s.comp_id )
  ) 
  and s.esta_id<>15  and
 cd.part_id not like '4.11%' 
 
 and dg2.docg_id in (select docg_id from sai_pago_transferencia where trans_id in (select comp_doc_id from sai_comp_diario where comp_comen like
'T-%'  or comp_comen like 'P-%')
 union select docg_id from sai_pago_cheque where pgch_id in (select comp_doc_id from sai_comp_diario where comp_comen like
'T-%'  or comp_comen like 'P-%')
union select docg_id from sai_doc_genera where docg_id like 'codi%'
union select docg_id from sai_pago_transferencia where trans_id in (select comp_doc_id from sai_comp_diario where comp_comen like
'A_T-%' or comp_comen like 'A_P-%')
 union select docg_id from sai_pago_cheque where pgch_id in (select comp_doc_id from sai_comp_diario where comp_comen like
'A_T-%' or comp_comen like 'A_P-%' )
union select docg_id from sai_doc_genera where docg_id like 'codi%'
)

 ";

$sql=$sql." and ca.paga_docu_id  in (select paga_docu_id from sai_pagado where esta_id=15) ";

if($_REQUEST['proyac']!=null && $_REQUEST['proyac']!='0') {
	list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
	$sql=$sql." and cd.padt_id_p_ac='".$proy."' and cd.padt_cod_aesp='".$especif."'";
}
if($_REQUEST["partida"]!=null && $_REQUEST["partida"]!='' ){
	$sql=$sql." and substr(cd.part_id,1,13)= '".$_REQUEST["partida"]."'";
}
$sql=$sql." and  (c.cpat_id like '1.%' or c.cpat_id like '6.%')  and
((ca.paga_fecha >= to_date('".$fechaIinicio."', 'DD MM YYYY') and ca.paga_fecha < to_date('".$fechaFfin."', 'DD MM YYYY')+1 )
or (ca.fecha_anulacion >= to_date('".$fechaIinicio."', 'DD MM YYYY') and ca.fecha_anulacion < to_date('".$fechaFfin."', 'DD MM YYYY')+1 )) ";


$sql=$sql." order by 4,3,2,18 desc,length(s.comp_id), substr(s.comp_id,3,length(s.comp_id)-2) ";





//echo $sql."<br>";
$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas1");
$caus_id="";
$caus_docu_id="";
$part_id="";
$montopres=0;
$proyecto="";
$especifica="";

$comp_fec="";
$comp_id="";
$commm="";

while($rowor=pg_fetch_array($resultado_set_most_or))
{
	if($commm<>$rowor['caus_id']){
		$commm=$rowor['caus_id'];
	//echo $commm."  ".$rowor['caus_id']."<br>";
	$caus_id=$rowor['caus_id'];
	$caus_docu_id2=trim($rowor['caus_docu_id']);
	$part_id=$rowor['part_id'];
	$montopres=$rowor['totalpres'];
	$montopres2=$montopres;
	
	//echo "bbbb".$montopres."  ".substr_count($montopres, '-')."aaaa<br>";
	if(substr_count($montopres, '-')>0){
		$montopres2=(-1)*$montopres;
	}
	$caus_docu_id=$rowor['docg_id'];

	//echo $caus_docu_id."<br>";
	$proyecto=$rowor['proyecto'];
	$especifica=$rowor['especifica'];
	$vuelta=0;
	$proyecto=$rowor['proyecto'];
	$especifica=$rowor['especifica'];
	$vuelta=1;
	
	$sopg=$rowor['docg_id'];
	$query1=" select s.comp_id, s.comp_fec,substring(s.comp_id,5) from sai_comp_diario s where s.esta_id<>15 and (s.comp_id='".$caus_docu_id."' or s.comp_doc_id='".$caus_docu_id."' or s.comp_doc_id in(select pgch_id from sai_pago_cheque where docg_id ='".$caus_docu_id."') or s.comp_doc_id in(select trans_id from sai_pago_transferencia where docg_id ='".$caus_docu_id."') )   ";

	$query1=$query1. " and (s.comp_comen like 'A_P%' or s.comp_id ='".$rowor['comp_id']."'  or s.comp_comen like 'A_C%') and (comp_comen like 'C-%' or comp_comen like 'A_C%') order by 2,length(s.comp_id), substr(s.comp_id,3,length(s.comp_id)-2)";
	//echo $query1." ;--NNN1 <br>";;
	$resultado_set_most=pg_query($conexion,$query1) or die("Error al consultar las Cuentas");
$van=0;
	while($rowo=pg_fetch_array($resultado_set_most)) {		
		
	$fecha_actual = substr($rowor['paga_fecha'], 0, 4).substr($rowor['paga_fecha'], 5, 2).substr($rowor['paga_fecha'], 8, 2);  
	 $fecha_entrada = substr($fechaFfin, 6, 4).substr($fechaFfin, 3, 2).substr($fechaFfin, 0, 2);;  
	 $fecha_entradaIni = substr($fechaIinicio, 6, 4).substr($fechaIinicio, 3, 2).substr($fechaIinicio, 0, 2);;
	 
	// echo $fecha_actual." ".$fecha_entrada ." lll".$rowor['caus_docu_id']."<br>";
		$comp_fec=$rowor['paga_fecha'];
		$comp_id=$rowo['comp_id'];
		
			
		$query2="select count(part_id) as tt from sai_convertidor where cpat_id=(select	cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' and  (cpat_id like '1.%' or cpat_id like '6.%')) ";
		//echo $query2."<br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
$tt=0;

/*	if($sopg==$sopganterior&&$rowor['estatuspres']=='15'&&$van==2){
						$van=0;
					}*/
$totalp=0;
			if($row=pg_fetch_array($resultado_set)) {
				$totalp=$row['tt'];
			}
		if($totalp==1){
		$query2="select rc.comp_id,sum(COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00))  as total  from sai_reng_comp rc where rc.comp_id ='".$rowo['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%'))";
		//}
		$query2=$query2." group by comp_id ";
		//echo $query2." a<br>";
		}else{
			
			$query2="select count(rc.comp_id) as tt  from sai_reng_comp rc where rc.comp_id ='".$rowo['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."' or COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres*(-1)."')";
		//	echo $query2." b<br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
		if($row=pg_fetch_array($resultado_set)) {
				$totalp=$row['tt'];
			}
			if($totalp>0){
				if($totalp>1){
			$query2="select rc.comp_id,COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)  as total  from sai_reng_comp rc where rc.comp_id ='".$rowo['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."' )";
				}else{
					$query2="select rc.comp_id,COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)  as total  from sai_reng_comp rc where rc.comp_id ='".$rowo['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%')) and  (COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres."' or COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00)='".$montopres*(-1)."')";
			
				}
		//$query2=$query2+" group by comp_id ";
		//	echo $query2." c<br>";
			}else{
				$query2="select rc.comp_id,sum(COALESCE((rc.rcomp_debe-rc.rcomp_haber),0.00))  as total  from sai_reng_comp rc where rc.comp_id ='".$rowo['comp_id']."' 
			 and rc.cpat_id in(select cpat_id from sai_convertidor where part_id='".$rowor['part_id_completa']."' 
			 and  (cpat_id like '1.%' or cpat_id like '6.%'))";
		//}
		$query2=$query2." group by comp_id ";
				//echo $query2." d<br>";
			}
		}
		//echo $query2." <br>";
			$resultado_set=pg_query($conexion,$query2) or die("Error al consultar las Cuentas");
$tt=0;

			if($row=pg_fetch_array($resultado_set)) {
			//	echo $rowor['estatuspres']."  dd".substr_count($copiado, $comp_id.":".$part_id)."hhh<br>";
			//	echo "<br>aaa".$copiado."  ". $comp_id.":".$part_id."bbbb<br>";
			if($rowor['estatuspres']=='15'){
						
					if($van<2){
					if(substr_count($copiado, $comp_id.":".$part_id)<1) {	
					//	echo substr_count($row['total'], '-')."  vv ".$vuelta."   ".$van."   ". $row['total']*1.0."   " . $montopres*1.0."   "."<br>";
						if($vuelta==1){
							/*if(substr_count($row['total'], '-')>0){
								$queryins="insert into sai_prescontableinicial2_".$login."  values('".$caus_id."','".$caus_docu_id."','".$part_id."',".$montopres*-1.0.",'".$proyecto."','".$especifica."','".$comp_id."','".$cpat_id."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'),
					to_date('".$rowor['fecha_anulacion']."', 'YYYY MM DD'),".$rowor['estatuspres'].",".$fecha_actual.",".$fecha_entrada.",'".$rowor['fechatext']."',".$vuelta.",'".$rowor['numero_reserva']."','".$caus_docu_id2."')";
				
							}else{*/
								$queryins="insert into sai_prescontableinicial2_".$login."  values('".$caus_id."','".$caus_docu_id."','".$part_id."',".$montopres*1.0.",'".$proyecto."','".$especifica."','".$comp_id."','".$cpat_id."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'),
					to_date('".$rowor['fecha_anulacion']."', 'YYYY MM DD'),".$rowor['estatuspres'].",".$fecha_actual.",".$fecha_entrada.",'".$rowor['fechatext']."',".$vuelta.",'".$rowor['numero_reserva']."','".$caus_docu_id2."')";	
							//}
							
							
						}else{
							
							$queryins="insert into sai_prescontableinicial2_".$login."  values('".$caus_id."','".$caus_docu_id."','".$part_id."',".$montopres*-1.0.",'".$proyecto."','".$especifica."','".$comp_id."','".$cpat_id."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'),
					to_date('".$rowor['fecha_anulacion']."', 'YYYY MM DD'),".$rowor['estatuspres'].",".$fecha_actual.",".$fecha_entrada.",'".$rowor['fechatext']."',".$vuelta.",'".$rowor['numero_reserva']."','".$caus_docu_id2."')";
							
						
						}
						
						//	echo $queryins."3333;<br>";;
				$resultado = pg_query($conexion,$queryins) or die("Error al registrar saldosa"); 
				
				$tt=$row['total']+$tt;
				
				$copiado=$copiado.$comp_id.":".$part_id.",";
				$van=$van+1;
					}
					$vuelta=$vuelta+1;
					}
				}else{
					
					
					if($rowor['estatuspres']=='1'){
						if(substr_count($copiado, $comp_id.":".$part_id)<1) {
							$queryins="insert into sai_prescontableinicial2_".$login."  values('".$caus_id."-".$vuelta."','".$caus_docu_id."','".$part_id."',".$montopres*1.0.",'".$proyecto."','".$especifica."','".$comp_id."','".$cpat_id."',".$row['total']*1.0.",to_date('".$comp_fec."', 'YYYY MM DD'),
					null,".$rowor['estatuspres'].",".$fecha_actual.",".$fecha_entrada.",'".$rowor['fechatext']."',".$vuelta.",'".$rowor['numero_reserva']."','".$caus_docu_id2."')";
						//	echo $queryins."4444;<br>";;
				$resultado = pg_query($conexion,$queryins) or die("Error al registrar saldosb"); 
				
				$tt=$row['total']+$tt;
				$vuelta=$vuelta+1;
				$copiado=$copiado.$comp_id.";".$part_id.",";
							
						}
					}
				}
						
			}
			
			
			
	
		

	}

$sopganterior=$rowor['caus_docu_id'];
}

}






/********************************************** llenando la tabla con los definitivos************************/





$query1as=" select  caus_id, caus_docu_id, part_id, monto_pres, proyecto, especifica, comp_id, cpat_id, monto_ctble, comp_fec, fecha_anulacion, esta_id, fechaactual,fechaentrada, fechaanulacion, posicion ,numero_reserva,doc2,posicion
from sai_prescontableinicial2_".$login." where  ( (comp_fec >= to_date('".$fechaIinicio."', 'DD MM YYYY') and comp_fec <= to_date('".$fechaFfin."', 'DD MM YYYY')) or (fecha_anulacion >= to_date('".$fechaIinicio."', 'DD MM YYYY') and fecha_anulacion <= to_date('".$fechaFfin."', 'DD MM YYYY')))";
	$resultado_set_most=pg_query($conexion,$query1as) or die("Error al consultar las Cuentas");
	while($rowo=pg_fetch_array($resultado_set_most)) {
		if($rowo['fechaanulacion']=='' || $rowo['fechaanulacion']<=$rowo['fechaentrada'] || ($rowo['fechaanulacion']>=$rowo['fechaentrada']&&$rowo['posicion']==1&&$rowo['fechaactual']>=$rowo['fechaentrada'])){
			if($rowo['fechaanulacion']==''){
				$queryins="insert into sai_prescontable4_".$login."  values('".$rowo['caus_id']."','".$rowo['caus_docu_id']."','".$rowo['part_id']."',".$rowo['monto_pres']*1.0.",'".$rowo['proyecto']."','".$rowo['especifica']."','".$rowo['comp_id']."','".$rowo['cpat_id']."',".$rowo['monto_ctble']*1.0.",'".$rowo['comp_fec']."','".$rowo['numero_reserva']."','".$rowo['doc2']."',NULL,1)";	
			}else{
					if($rowo['posicion']=='1'){
						$queryins="insert into sai_prescontable4_".$login."  values('".$rowo['caus_id']."','".$rowo['caus_docu_id']."','".$rowo['part_id']."',".$rowo['monto_pres']*1.0.",'".$rowo['proyecto']."','".$rowo['especifica']."','".$rowo['comp_id']."','".$rowo['cpat_id']."',".$rowo['monto_ctble']*1.0.",'".$rowo['comp_fec']."','".$rowo['numero_reserva']."','".$rowo['doc2']."',NULL,1)";
					}else{
						$queryins="insert into sai_prescontable4_".$login."  values('".$rowo['caus_id']."','".$rowo['caus_docu_id']."','".$rowo['part_id']."',".$rowo['monto_pres']*1.0.",'".$rowo['proyecto']."','".$rowo['especifica']."','".$rowo['comp_id']."','".$rowo['cpat_id']."',".$rowo['monto_ctble']*1.0.",'".$rowo['comp_fec']."','".$rowo['numero_reserva']."','".$rowo['doc2']."','".$rowo['fecha_anulacion']."',2)";	
					}
					
				
			}
			
			
					
				$resultado = pg_query($conexion,$queryins) or die("Error al registrar saldos".$queryins); 
		}
		
		
	}





?>
