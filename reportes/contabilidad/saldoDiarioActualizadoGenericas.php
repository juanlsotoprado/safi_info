<?php 
$nucleo = " SELECT cpat_id, sum(rcomp_debe) as rcomp_debe, sum(rcomp_haber) as rcomp_haber
FROM sai_reng_comp src,sai_comp_diario scd
where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by src.cpat_id, esta_id

union

SELECT (substring(trim(cpat_id) from 1 for 15)) || '00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union

SELECT (substring(trim(cpat_id) from 1 for 12)) || '00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union

SELECT (substring(trim(cpat_id) from 1 for 9)) || '00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	union
	
	SELECT (substring(trim(cpat_id) from 1 for 6)) || '00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	union
	
	SELECT (substring(trim(cpat_id) from 1 for 4)) || '0.00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
union 

SELECT (substring(trim(cpat_id) from 1 for 2)) || '0.0.00.00.00.00' as subs , sum(rcomp_debe), sum(rcomp_haber)
	FROM sai_reng_comp src,sai_comp_diario scd
	where src.comp_id=scd.comp_id and scd.esta_id<>'15' and scd.comp_fec BETWEEN to_date('".$fechaIinicio."', 'DD MM YYYY') AND	to_date('".$fechaFfin."', 'DD MM YYYY') group by subs, esta_id
	
	
";



$nucle2 = "
	SELECT
		scp.cpat_id,
		CASE
			WHEN position('6' in scp.cpat_id) = 1 or position('1' in scp.cpat_id) = 1 or position('4' in scp.cpat_id) = 1 THEN
				sd.saldo + scp.rcomp_debe - scp.rcomp_haber
			ELSE
				sd.saldo - scp.rcomp_debe + scp.rcomp_haber
			END AS cpat_sal_actual
	FROM
			(".$sql_saldo.") sd
			INNER JOIN (".$nucleo.") scp ON (scp.cpat_id = sd.cpat_id)
		
			
";

$nucleo3 = "
	select
		sc2.cpat_id,
		sc2.cpat_sal_actual
	from
		(".$nucle2.") sc2
	 union
	 ".$sql_saldo." and cpat_id not in (select cpat_id from (".$nucle2.") sc)";

/*Llenado de saldo diario por dia de cuentas que tuvieron movimiento*/
$sql_total = "
	select
		to_date(to_char(now(), 'YYYY MM DD'), 'YYYY MM DD') as fecha,
		cpat_id,
		cpat_sal_actual as saldo
	from
		(".$nucleo3.") scp ";

?>
