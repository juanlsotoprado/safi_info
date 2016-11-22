<?php 
$nucleo = "
	SELECT
		cpat_id, sum(rcomp_debe) as rcomp_debe,
		sum(rcomp_haber) as rcomp_haber
	FROM
		sai_reng_comp src
		INNER JOIN sai_comp_diario scd ON (src.comp_id = scd.comp_id )
	where
		scd.esta_id<>'15' AND
		scd.comp_fec BETWEEN 		
			to_date('".$fechaIinicio."', 'DD MM YYYY') AND
			to_date('".$fechaFfin."', 'DD MM YYYY')-1
	group by
		src.cpat_id
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
