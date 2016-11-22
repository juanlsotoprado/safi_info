<?php 
require_once("../../includes/conexion.php");

/*Borrar data de sai_cue_pat_saldo y sai_cue_pat_saldo2*/
$sql = "drop table sai_cue_pat_saldo_".$login."  ";
 $resultado =pg_query($conexion,$sql) ; 

$sql = " drop table sai_cue_pat_saldo2_".$login."  ";
 $resultado =pg_query($conexion,$sql) ; 

$sql = " drop table  sai_cue_pat_saldodiario_".$login."  ";
 $resultado =pg_query($conexion,$sql) ;

$sql = "drop table  sai_cue_pat_saldo3_".$login." ";
$resultado = pg_query($conexion,$sql) ;

/*Llenado de sumatorias en sai_cue_pat_saldo de montos de debe y haber por cuenta desde el 30/06/2008 hasta la fecha dada*/

$sql = " CREATE  TEMPORARY TABLE sai_cue_pat_saldo_".$login." as  (
SELECT cpat_id, sum(rcomp_debe) as rcomp_debe, sum(rcomp_haber) as rcomp_haber
FROM sai_reng_comp src,sai_comp_diario scd
where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY')  group by src.cpat_id, esta_id

union

SELECT (substring(trim(cpat_id) from 1 for 15)) || '00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union

SELECT (substring(trim(cpat_id) from 1 for 12)) || '00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union

SELECT (substring(trim(cpat_id) from 1 for 9)) || '00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	union
	
	SELECT (substring(trim(cpat_id) from 1 for 6)) || '00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	union
	
	SELECT (substring(trim(cpat_id) from 1 for 4)) || '0.00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union 

SELECT (substring(trim(cpat_id) from 1 for 2)) || '0.0.00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$fechaIinicio."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	
)";


$resultado = pg_query($conexion,$sql) or die("Error al calcular sumatorias de debe y haber"); 




/*Llenado de saldo diario por dia de cuentas que tuvieron movimiento en sai_cue_pat_saldo2*/
/*SI las cuentas comienzan por 1 o 6 se suma por el debe y se resta por el haber*/
$sql = " cREATE  TEMPORARY TABLE sai_cue_pat_saldo2_".$login." as 	(

select scp.cpat_id, sd.cpat_sal_ini + scp.rcomp_debe - scp.rcomp_haber as cpat_sal_actual
	from sai_cue_pat sd, sai_cue_pat_saldo_".$login." scp
	where scp.cpat_id = sd.cpat_id and (position('6' in scp.cpat_id) = 1 or position('1' in scp.cpat_id) =1 or position('4' in scp.cpat_id) =1) 
	
	union
	
	select scp.cpat_id, sd.cpat_sal_ini - scp.rcomp_debe + scp.rcomp_haber as cpat_sal_actual
	from sai_cue_pat sd, sai_cue_pat_saldo_".$login." scp
	where scp.cpat_id = sd.cpat_id and (position('6' in scp.cpat_id) !=1 and position('1' in scp.cpat_id) != 1 and position('4' in scp.cpat_id) != 1)
	
	)"; //Dos días anteriores saldo

$resultado = pg_query($conexion,$sql) or die("Error al registrar saldos iniciales en sai_cue_pat_saldo2"); 





$sql = " cREATE  TEMPORARY TABLE sai_cue_pat_saldo3_".$login." as 
	 (select sc2.cpat_id, sc2.cpat_sal_actual from sai_cue_pat_saldo2_".$login." sc2
	 
	 union
	 
	 select cpat_id, cpat_sal_ini from sai_cue_pat where cpat_id not in (select cpat_id from sai_cue_pat_saldo2_".$login.")
	 
	 )";

$resultado = pg_query($conexion,$sql) or die("Error al registrar saldos iniciales en sai_cue_pat_saldo3"); 




	/*Llenado de saldo diario por dia de cuentas que tuvieron movimiento en sai_cue_pat_diario*/
	$sql = "  cREATE  TEMPORARY TABLE sai_cue_pat_saldodiario_".$login."    as  (select to_date(to_char(now(), 'YYYY MM DD'), 'YYYY MM DD') as fecha, cpat_id, cpat_sal_actual as saldo from sai_cue_pat_saldo3_".$login.") ";

	$resultado = pg_query($conexion,$sql) or die("Error al registrar saldos iniciales en sai_cue_pat_saldodiario, cuentas con movimiento"); 




//echo "Operacion realizada. Probar!";

?>