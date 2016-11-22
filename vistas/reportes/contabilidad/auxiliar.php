<?php
include(dirname(__FILE__) . '/../../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');
$form = FormManager::GetForm(FORM_CUENTA_BANCARIA_PAGADO_CONTABILIDAD);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Auxiliar Contable</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script>
function validar(op){
	if(trim(document.getElementById("cuentaActb").value)!=""){
		tokens = document.getElementById("cuentaActb").value.split( ":" );
		cuenta = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("cuenta").value = cuenta;
	}
	if(op==1){
		document.form1.action="auxiliar.php";
	}
	document.form1.submit();
}

function comparar_fechas(fecha_inicial,fecha_final){//Formato dd/mm/yyyy
	var fecha_inicial=document.getElementById("fechaInicio").value;//document.form.fechaInicio.value;
	var fecha_final=document.getElementById("fechaFin").value;//document.form.fechaFin.value;

	var dia1 =fecha_inicial.substring(0,2);
	var mes1 =fecha_inicial.substring(3,5);
	var anio1=fecha_inicial.substring(6,10);
	
	var dia2 =fecha_final.substring(0,2);
	var mes2 =fecha_final.substring(3,5);
	var anio2=fecha_final.substring(6,10);

	dia1 = parseInt(dia1,10);
	mes1 = parseInt(mes1,10);
	anio1= parseInt(anio1,10);

	dia2 = parseInt(dia2,10);
	mes2 = parseInt(mes2,10);
	anio2= parseInt(anio2,10); 
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
		alert("La fecha inicial no debe ser mayor a la fecha final");
		document.form.fechaInicio.value='';
		document.form.fechaFin.value='';
		return;
	}
}

function habiDesabiFechas(elemento){
	fechaInicio = document.getElementById("fechaInicio");
	fechaFin = document.getElementById("fechaFin");
	if(elemento.checked==true){
		fechaInicio.disabled=false;
		fechaFin.disabled=false;
    }else{
    	fechaInicio.disabled=true;
		fechaFin.disabled=true; 
		fechaInicio.value="";
		fechaFin.value="";
    }
}
//-->
</script>
</head>
<body>
<br/>
<br/>
<form name="form1" method="post">
	<table width="55%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">AUXILIAR CONTABLE</td>
		</tr>
		<tr>
			<td class="normalNegrita" colspan="2">
				Fecha Inicio:
				<input type="text" size="10" id="fechaInicio"
				name="fechaInicio" class="dateparse"
				onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= $_POST["fechaInicio"];?>"/>
				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fechaInicio');" title="Show popup calendar"><img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/></a>
				&nbsp;
				Fecha Fin:
				<input type="text" size="10" id="fechaFin"
				name="fechaFin" class="dateparse"
				onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= $_POST["fechaFin"];?>"/>
				<a href="javascript:void(0);"
				onclick="g_Calendar.show(event, 'fechaFin');"
				title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
				</a>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Cuenta contable:</td>
			<td>
				<input type="hidden" value="" name="cuenta" id="cuenta"/>
				<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="<?= $_POST["cuenta"];?>" class="normal" />
				<?php
					$query = 	"SELECT ".
									"scp.cpat_id, ".
									"scp.cpat_nombre ".
								"FROM ".
									"sai_cue_pat scp where scp.cpat_id not like '%.00' ".
								"ORDER BY scp.cpat_id";
					$resultado = pg_exec($conexion, $query);
					$numeroFilas = pg_num_rows($resultado);
					$arreglo = "";
					while($row=pg_fetch_array($resultado)){
						$arreglo .= "'".$row["cpat_id"]." : ".str_replace("\n"," ",$row["cpat_nombre"])."',";
					}
					$arreglo = substr($arreglo, 0, -1);
				?>
				<script>
					var cuentasAMostrar = new Array(<?= $arreglo?>);
					actb(document.getElementById('cuentaActb'),cuentasAMostrar);
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="button" value="Listar" onclick="validar(1)"/>
				<input type="hidden" name="login" value="<?= $_SESSION['login']?>"/>
			</td>
		</tr>
	</table>
</form>
<br/>
<?php
if(isset($_POST["fechaInicio"]) && isset($_POST["fechaFin"])) {
	$fecha_inicio = $_POST["fechaInicio"];
	$fecha_fi = $_POST["fechaFin"];
	$login=$_SESSION['login'];
	/*Búsqueda de movimientos en las fechas registradas*/
	$sql= "	
			SELECT
				s.fecha, 
				s.comp_id,
				s.comentario,
				s.monto,
				s.cpat_id,
				s.sopg,
				s.fuente_financiamiento 
			FROM
				(SELECT 
					max(scd.comp_fec) AS fecha, 
					max(scd.comp_id) AS comp_id,
					max(scd.comp_comen) AS comentario,
					SUM(src.rcomp_debe) - SUM(src.rcomp_haber) AS monto,
					src.cpat_id AS cpat_id,
					CASE 
						WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
							comp_doc_id
						WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
							(SELECT
							 	docg_id
							  FROM sai_pago_cheque
							  WHERE pgch_id=comp_doc_id)
						WHEN substring(comp_doc_id, 1, 4)='tran' THEN
							(SELECT 
								docg_id
							 FROM sai_pago_transferencia
							 WHERE trans_id=comp_doc_id)
					END AS sopg,
					sf1125.fuente_financiamiento 
				FROM 
					sai_reng_comp src
					INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					LEFT OUTER JOIN sai_forma_1125 sf1125 ON (src.pr_ac_tipo = sf1125.form_tipo AND src.pr_ac = sf1125.form_id_p_ac AND src.a_esp = sf1125.form_id_aesp)
				WHERE
					scd.esta_id<>'15' AND 
					src.cpat_id = '".$_POST["cuenta"]."' AND
					scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') 
				GROUP BY 
					src.cpat_id,
					sopg,
					sf1125.fuente_financiamiento
				) AS s
			WHERE
				s.sopg IS NOT NULL AND s.sopg <> ''
			UNION
			SELECT
				s.fecha, 
				s.comp_id,
				s.comentario,
				s.monto,
				s.cpat_id,
				s.sopg,
				s.fuente_financiamiento 
			FROM
				(SELECT 
					scd.comp_fec AS fecha, 
					scd.comp_id AS comp_id,
					scd.comp_comen AS comentario,
					SUM(src.rcomp_debe)- SUM(src.rcomp_haber) AS monto,
					src.cpat_id AS cpat_id,
					CASE 
						WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
							comp_doc_id
						WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
							(SELECT
							 	docg_id
							  FROM sai_pago_cheque
							  WHERE pgch_id=comp_doc_id)
						WHEN substring(comp_doc_id, 1, 4)='tran' THEN
							(SELECT 
								docg_id
							 FROM sai_pago_transferencia
							 WHERE trans_id=comp_doc_id)
					END AS sopg,
					sf1125.fuente_financiamiento 
				FROM 
					sai_reng_comp src
					INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					LEFT OUTER JOIN sai_forma_1125 sf1125 ON (src.pr_ac_tipo = sf1125.form_tipo AND src.pr_ac = sf1125.form_id_p_ac AND src.a_esp = sf1125.form_id_aesp)
				WHERE
					scd.esta_id<>'15' AND 
					src.cpat_id = '".$_POST["cuenta"]."' AND
					scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') 
				GROUP BY
					scd.comp_fec,
					scd.comp_id,
					scd.comp_comen,
					src.cpat_id,
					sopg,
					sf1125.fuente_financiamiento
				) AS s
			WHERE
				s.sopg IS NULL OR s.sopg = ''
			ORDER by fecha";
	
/*
 * Casos CODI
 * 	$sql= "	SELECT 
					max(scd.comp_fec) as fecha, 
					max(scd.comp_id) as comp_id,
					max(scd.comp_comen) as comentario,
					SUM(src.rcomp_debe)- SUM(src.rcomp_haber) as monto,
					src.cpat_id as cpat_id,
					max(nro_referencia) AS nro_referencia,
					CASE 
						WHEN (substring(comp_doc_id, 1, 4)='sopg') then
							comp_doc_id
						WHEN (substring(comp_doc_id, 1, 4)='pgch') then
							(SELECT
							 	docg_id
							  FROM sai_pago_cheque
							  WHERE pgch_id=comp_doc_id)
						WHEN substring(comp_doc_id, 1, 4)='tran' then
							(SELECT 
								docg_id
							 FROM sai_pago_transferencia
							 WHERE trans_id=comp_doc_id)
					END as sopg 
				FROM sai_reng_comp src
				INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
				WHERE
					scd.esta_id<>'15' AND 
					src.cpat_id = '".$_POST["cuenta"]."' AND
					scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') AND
					length(scd.comp_doc_id) >5 
				GROUP BY 
					src.cpat_id,
					sopg
					
			UNION
				SELECT 
					scd.comp_fec as fecha, 
					scd.comp_id as comp_id,
					scd.comp_comen as comentario,
					SUM(src.rcomp_debe)- SUM(src.rcomp_haber) as monto,
					src.cpat_id as cpat_id,
					scd.nro_referencia AS nro_referencia,
					CASE 
						WHEN (substring(comp_doc_id, 1, 4)='sopg') then
							comp_doc_id
						WHEN (substring(comp_doc_id, 1, 4)='pgch') then
							(SELECT
							 	docg_id
							  FROM sai_pago_cheque
							  WHERE pgch_id=comp_doc_id)
						WHEN substring(comp_doc_id, 1, 4)='tran' then
							(SELECT 
								docg_id
							 FROM sai_pago_transferencia
							 WHERE trans_id=comp_doc_id)
					END as sopg 
				FROM sai_reng_comp src
				INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
				WHERE
					scd.esta_id<>'15' AND 
					src.cpat_id = '2.1.1.03.09.01.01' AND
					scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY') AND
					length(scd.comp_doc_id) <5 
				GROUP BY 
					src.cpat_id,
					sopg,
					comp_fec,
					scd.comp_id,
					comp_comen,
					nro_referencia				
				ORDER by fecha";

 * */
$resultado=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
?>
<div align="center" class="normalNegroNegrita"><?= "AUXILIAR CONTABLE ".$_POST["fechaInicio"]." Y ".$_POST["fechaFin"]?></div>
<br></br>
<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td width="6%" class="normalNegroNegrita">Fecha</td>
		<td width="6%" class="normalNegroNegrita">Asiento</td>			
		<td width="13%" class="normalNegroNegrita">Sopg</td>
		<td width="6%" class="normalNegroNegrita">Fuente Financiamiento</td>		
		<td width="6%" class="normalNegroNegrita">Cuenta</td>
		<td width="13%" class="normalNegroNegrita">Debe</td>
		<td width="13%" class="normalNegroNegrita">Haber</td>		
		<td width="13%" class="normalNegroNegrita">Comentario</td>		
	</tr>
<?php
	while($row=pg_fetch_array($resultado)) {
		if ($row['monto']!=0) {
?>
	<tr>
		<td><div class="normal"><?= $row['fecha']?></div></td>
		<td><div class="normal"><?= $row['comp_id']?></div></td>
		<td><div class="normal"><?= $row['sopg']?></div></td>
		<td><div class="normal"><?= $row['fuente_financiamiento']?></div></td>				
		<td width="45" align="center" class="normal"><?= $row['cpat_id']?></td>
		<?php 
		if ($row['monto']>0) {$monto_debe = $row['monto']; $monto_haber=null;}
	    else {$monto_haber = $row['monto']*-1; $monto_debe=null;}
		?>
		<td><div align="center" class="normal"><?= number_format($monto_debe,2,',','.');?></div></td>
		<td><div align="center" class="normal"><?= number_format($monto_haber,2,',','.');?></div></td>
		<td><div class="normal"><?= $row['comentario']?></div></td>		
	</tr>
<?php
		}
	}
?>
</table>
<?php }?>
</body>
</html>
<?php pg_close($conexion); ?>