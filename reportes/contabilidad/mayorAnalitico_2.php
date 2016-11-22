<?php
ob_start();//
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	ob_end_flush();
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Mayor Anal&iacute;tico</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
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
		document.form1.action="mayorAnalitico_2.php";
	}else{
		document.form1.action="mayorAnaliticoPDF_2.php";
	}
	document.form1.submit();
}

function comparar_fechas(fecha_inicial,fecha_final){//Formato dd/mm/yyyy
	var fecha_inicial=document.getElementById("hid_desde_itin").value;//document.form.hid_desde_itin.value;
	var fecha_final=document.getElementById("hid_hasta_itin").value;//document.form.hid_hasta_itin.value;

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
		document.form.hid_desde_itin.value='';
		document.form.hid_hasta_itin.value='';
		return;
	}
}

function habiDesabiFechas(elemento){
	hid_desde_itin = document.getElementById("hid_desde_itin");
	hid_hasta_itin = document.getElementById("hid_hasta_itin");
	if(elemento.checked==true){
		hid_desde_itin.disabled=false;
		hid_hasta_itin.disabled=false;
    }else{
    	hid_desde_itin.disabled=true;
		hid_hasta_itin.disabled=true; 
		hid_desde_itin.value="";
		hid_hasta_itin.value="";
    }
}
//-->
</script>
</head>
<body>
<br />
<br />
<form name="form1" method="post">
<table width="55%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">MAYOR ANAL&Iacute;TICO</td>
	</tr>
	<tr>
		<td class="normalNegrita" width="40%">
			Fecha Inicio: 
			<input type="text" size="10" id="hid_desde_itin" name="hid_desde_itin" class="dateparse"
			onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= ((isset($_POST["hid_desde_itin"]) && $_POST["hid_desde_itin"]!="")?$_POST["hid_desde_itin"]:"01/01/".date("Y"))?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</td>
		<td class="normalNegrita" width="60%">
			Fecha Fin: 
			<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
			onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= ((isset($_POST["hid_hasta_itin"]) && $_POST["hid_hasta_itin"]!="")?$_POST["hid_hasta_itin"]:"")?>"/>
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
				<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita" colspan="2">
			Cuenta contable:
			<input type="hidden" value="<?= ((isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="" && isset($_POST["cuenta"]) && $_POST["cuenta"]!="")?$_POST["cuenta"]:"")?>" name="cuenta" id="cuenta"/>
			<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="<?= ((isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="")?$_POST["cuentaActb"]:"")?>" class="normal"/>
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
			<input type="button" value="Listar"	onclick="validar(1)"/>
			<input type="hidden" name="login" value="<?= $_SESSION['login']?>"/>
			<input type="button" value="PDF" onclick="validar(2)"/>
		</td>
	</tr>
</table>
</form>
<br/>
<?php
if(isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])) {
	$fecha_inicio = $_POST["hid_desde_itin"];
	$dia = substr($fecha_inicio, 0, 2);
	$mes = substr($fecha_inicio, 3, 2)+1-1;
	$ano = substr($fecha_inicio, 6, 4);
	$ano_antes = $ano-1;
	$max_mes = 0;
	$max_mes_antes = 0;
	$error="0";
	$consulta_basica=0;

	$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$mes." and ano=".$ano;
	$resultado=pg_query($conexion,$sql_saldo);
	$nro = 0;
	$nro = pg_num_rows($resultado);
	if ($nro<1) {
		$sql_saldo= "SELECT COALESCE(MAX(mes),0) AS mes FROM safi_saldo_contable WHERE ano=".$ano." AND mes<".$mes;
		$resultado=pg_query($conexion,$sql_saldo);
		$row=pg_fetch_array($resultado);
		$nro = $row["mes"];
		if ($nro<1) {
			$sql_saldo= "select coalesce(max(mes),0)  as mes from safi_saldo_contable where ano=".$ano_antes;
			$resultado=pg_query($conexion,$sql_saldo);
			$row=pg_fetch_array($resultado);
			$nro = $row["mes"];
			if ($nro<1) {
				$max_mes_antes=0;
				$error=1;
			}else {
				$max_mes_antes=$nro;
				$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes_antes." and ano=".$ano_antes;
			}
		}else {
			$max_mes=$row["mes"];
			$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes." and ano=".$ano;
		}
	}else {
		$max_mes = $mes;
	//	if ($dia=="01") $consulta_basica=1;
	}

	if ($max_mes!=0) {
		if ($max_mes == 1 || $max_mes == 2 || $max_mes == 3 || $max_mes == 4 || $max_mes == 5 || $max_mes == 6 || $max_mes == 7 || $max_mes == 8 || $max_mes == 9) {
			$mes_x=$max_mes;
			$max_mes= "0".$max_mes;
		}
		$mes_total = $max_mes;
		$ano_total = $ano;
	}else {
		if ($max_mes_antes!=0) {
			if ($max_mes_antes == 1 || $max_mes_antes == 2 || $max_mes_antes == 3 || $max_mes_antes == 4 || $max_mes_antes == 5 || $max_mes_antes == 6 || $max_mes_antes == 7 || $max_mes_antes == 8 || $max_mes_antes == 9) {
				$mes_x=$max_mes_antes;
				$max_mes_antes= "0".$max_mes_antes;

			}
			$mes_total = $max_mes_antes;
			$ano_total = $ano-1;
		}
	}
	if ($error!=1) {
		/*if ($mes_total!=12) {
		 $mes_total=$mes_total+1;
		 if ($mes_total == 1 || $mes_total == 2 || $mes_total == 3 || $mes_total == 4 || $mes_total == 5 || $mes_total == 6 || $mes_total == 7 || $mes_total == 8 || $mes_total == 9) $mes_total="0".$mes_total;
		 }*/
		/*else {
		 $mes_total="01";
		 $ano_total=$ano_total+1;
		 }*/
		$fechaIinicio = "01/".$mes_total."/".$ano_total;
		$fechaFfin = $fecha_inicio;
		//	echo "Fecha inicio:".$fechaIinicio." Fecha fin:".$fechaFfin;

		if ($consulta_basica==1) {
			if (isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="" && isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){
				$sql = "SELECT 
							src.comp_id, 
							src.reng_comp, 
							sc.cpat_id, 
							sc.cpat_nombre, 
							src.rcomp_debe, 
							src.rcomp_haber, 
							src.rcomp_tot_db, 
							src.rcomp_tot_hab, 
							to_char(comp_fec, 'DD/MM/YYYY') as fecha_emision, 
							scd.comp_comen, 
							scp.saldo, 
							scd.comp_doc_id, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg' and substring(scd.comp_id, 1, 4)!='codi') then
									(select 
										numero_reserva as fuente 
									from sai_sol_pago 
									where sopg_id = comp_doc_id) 
								when (substring(scd.comp_id, 1, 4)='codi') then 
									(select 
										fte_financiamiento as fuente 
									from sai_codi 
									where comp_id=src.comp_id) 
							else '' end as numero_reserva, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg') then 
									comp_doc_id 
								when (substring(comp_doc_id, 1, 4)='pgch') then 
									(select 
										docg_id 
									from sai_pago_cheque 
									where pgch_id=comp_doc_id) 
								when substring(comp_doc_id, 1, 4)='tran' then 
									(select 
										docg_id 
									from sai_pago_transferencia 
									where trans_id=comp_doc_id) 
							end as sopg, 
							scd.nro_referencia
						FROM 
							sai_cue_pat sc, 
							sai_reng_comp src, 
							(".$sql_saldo.") scp, 
							sai_comp_diario scd 
						where 
							src.cpat_id = sc.cpat_id and 
							scp.cpat_id = sc.cpat_id and 
							scd.comp_id = src.comp_id and 
							scd.esta_id <> '15' and 
							sc.cpat_id = '".trim($_POST["cuenta"])."' and 
							substring(trim(sc.cpat_id) from 16 for 17) != '00' and 
							scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') 
						order by sc.cpat_id, scd.comp_fec";
			}else{
				$sql = "SELECT 
							src.comp_id, 
							src.reng_comp, 
							sc.cpat_id, 
							sc.cpat_nombre, 
							src.rcomp_debe, 
							src.rcomp_haber, 
							src.rcomp_tot_db, 
							src.rcomp_tot_hab, 
							to_char(comp_fec, 'DD/MM/YYYY') as fecha_emision, 
							scd.comp_comen, 
							scp.saldo, 
							scd.comp_doc_id, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg' and substring(scd.comp_id, 1, 4)!='codi') then 
									(select 
										numero_reserva as fuente 
									from sai_sol_pago 
									where sopg_id = comp_doc_id) 
								when (substring(scd.comp_id, 1, 4)='codi') then 
									(select 
										fte_financiamiento as fuente 
									from sai_codi 
									where comp_id=src.comp_id) 
							else '' end as numero_reserva, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg') then 
									comp_doc_id 
								when (substring(comp_doc_id, 1, 4)='pgch') then 
									(select 
										docg_id 
									from sai_pago_cheque 
									where pgch_id=comp_doc_id) 
								when substring(comp_doc_id, 1, 4)='tran' then 
									(select 
										docg_id 
									from sai_pago_transferencia 
									where trans_id=comp_doc_id) 
							end as sopg, 
							scd.nro_referencia
						FROM 
							sai_cue_pat sc, 
							sai_reng_comp src, 
							(".$sql_saldo.") scp, 
							sai_comp_diario scd	
						where 
							src.cpat_id = sc.cpat_id and 
							scp.cpat_id = sc.cpat_id and 
							scd.comp_id = src.comp_id and 
							scd.esta_id<>'15' and 
							substring(trim(sc.cpat_id) from 16 for 17) != '00' and 
							scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."','DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."','DD MM YYYY') 
						order by sc.cpat_id, scd.comp_fec";
			}
		}else{
			$login=$_SESSION['login'];
			require_once("saldoDiarioActualizadoMayorAnalitico_2.php");

			/*Búsqueda de movimientos en las fechas registradas*/
			if (isset($_POST["cuentaActb"]) && $_POST["cuentaActb"]!="" && isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){
				$sql = "SELECT 
							src.comp_id, 
							src.reng_comp, 
							sc.cpat_id, 
							sc.cpat_nombre, 
							src.rcomp_debe, 
							src.rcomp_haber, 
							src.rcomp_tot_db, 
							src.rcomp_tot_hab, 
							to_char(scd.comp_fec, 'DD/MM/YYYY') as fecha_emision, 
							scd.comp_comen, 
							scp.saldo, 
							scd.comp_doc_id, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg' and substring(scd.comp_id, 1, 4)!='codi') then 
									(select 
										numero_reserva as fuente 
									from sai_sol_pago 
									where sopg_id = comp_doc_id) 
								when (substring(scd.comp_id, 1, 4)='codi') then 
									(select 
										fte_financiamiento as fuente 
									from sai_codi 
									where comp_id=src.comp_id) else '' 
							end as numero_reserva, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg') then 
									comp_doc_id 
								when (substring(comp_doc_id, 1, 4)='pgch') then 
									(select 
										docg_id 
									from sai_pago_cheque 
									where pgch_id=comp_doc_id) 
								when substring(comp_doc_id, 1, 4)='tran' then 
									(select 
										docg_id 
									from sai_pago_transferencia 
									where trans_id=comp_doc_id) 
							end as sopg, 
							scd.nro_referencia  
						FROM
							(SELECT 
								src.comp_id, 
								src.reng_comp, 
								src.rcomp_debe, 
								src.rcomp_haber, 
								src.rcomp_tot_db, 
								src.rcomp_tot_hab,
								src.cpat_id 
							FROM sai_reng_comp src
							WHERE 
								src.comp_id IN 
									(SELECT 
										scd.comp_id
									FROM sai_comp_diario scd 
									WHERE 
										scd.esta_id<>'15' and scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD/MM/YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD/MM/YYYY')
									)
							) src
							INNER JOIN sai_cue_pat sc ON (src.cpat_id = sc.cpat_id)
							INNER JOIN (".$sql_total.") scp ON (sc.cpat_id = scp.cpat_id)
							INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
						WHERE 
							src.cpat_id = '".trim($_POST["cuenta"])."' and 
							substring(src.cpat_id from 16 for 17) <> '00'
						ORDER BY sc.cpat_id, scd.comp_fec";
			}else{
				$sql = "SELECT 
							src.comp_id, 
							src.reng_comp, 
							sc.cpat_id, 
							sc.cpat_nombre, 
							src.rcomp_debe, 
							src.rcomp_haber, 
							src.rcomp_tot_db, 
							src.rcomp_tot_hab, 
							to_char(scd.comp_fec,'DD/MM/YYYY') as fecha_emision, 
							scd.comp_comen, 
							scp.saldo, 
							scd.comp_doc_id, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg' and substring(scd.comp_id, 1, 4)!='codi') then 
									(select 
										numero_reserva as fuente 
									from sai_sol_pago 
									where sopg_id = comp_doc_id) 
								when (substring(scd.comp_id, 1, 4)='codi') then 
									(select 
										fte_financiamiento as fuente 
									from sai_codi 
									where comp_id=src.comp_id) 
							else '' end as numero_reserva, 
							case 
								when (substring(comp_doc_id, 1, 4)='sopg') then 
									comp_doc_id 
								when (substring(comp_doc_id, 1, 4)='pgch') then 
									(select 
										docg_id 
									from sai_pago_cheque 
									where pgch_id=comp_doc_id) 
								when substring(comp_doc_id, 1, 4)='tran' then 
									(select 
										docg_id 
									from sai_pago_transferencia 
									where trans_id=comp_doc_id) 
							end as sopg, 
							scd.nro_referencia 
						FROM 
							sai_cue_pat sc, 
							sai_reng_comp src, 
							(".$sql_total.") scp, 
							sai_comp_diario scd	
						where 
							src.cpat_id = sc.cpat_id and 
							scp.cpat_id = sc.cpat_id and 
							scd.esta_id<>'15' and 
							scd.comp_id = src.comp_id and 
							scd.esta_id<>'15' and 
							substring(trim(sc.cpat_id) from 16 for 17) != '00' and 
							scd.comp_fec between to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') 
						order by sc.cpat_id, scd.comp_fec";
			}
		}
		/*$sql1 = "SELECT nucleo.*, f.fuente_financiamiento FROM (".$sql.") as nucleo
		LEFT OUTER JOIN sai_forma_1125 f ON (f.form_id_p_ac=nucleo.pr_ac and f.form_id_aesp=nucleo.a_esp)
		";*/
		//echo $sql;
		$ctaAnt="0";
		$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
		$titulopdf="MAYOR ANALÍTICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"];
?>
<div align="center" class="normalNegroNegrita"><?= "MAYOR ANAL&Iacute;TICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"]?></div>
<table width="90%" align="center"
	background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td width="6%" class="normalNegroNegrita">Fecha</td>
		<td width="13%" class="normalNegroNegrita">Identif.</td>
		<td width="13%" class="normalNegroNegrita">Sopg</td>
		<td width="13%" class="normalNegroNegrita">Cta Financiera</td>
		<td width="5%" class="normalNegroNegrita">Fte Finan</td>
		<td width="10%" class="normalNegroNegrita">Ref.</td>
		<td width="8%" class="normalNegroNegrita">Cta Contable</td>
		<td width="7%" class="normalNegroNegrita">S. Inicial</td>
		<td width="7%" class="normalNegroNegrita">Debe</td>
		<td width="7%" class="normalNegroNegrita">Haber</td>
		<td width="7%" class="normalNegroNegrita">S. Final</td>
		<td width="17%" class="normalNegroNegrita">Comentario</td>
	</tr>
	<?php
	//Inicio del While
	$cuenta_actual = ""; //Mantiene cada grupo de cuenta dentro del while
	$cambio = true;
	$saldo_calc_inicial = 0;
	//$saldo_inicial = 0;
	$saldo_final = 0;

	while($rowor=pg_fetch_array($resultado_set_most_or)){
		if ($cuenta_actual != $rowor['cpat_id']) {
			$cambio = true;
			$cuenta_actual = $rowor['cpat_id'];
		}

		if ($cambio) {
			$saldo_calc_inicial = $rowor['saldo'];
			$_SESSION['rcomp_debe'] = $rowor['rcomp_debe'];
			$_SESSION['rcomp_haber'] = $rowor['rcomp_haber'];
			if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1  || substr($rowor['cpat_id'], 0, 1)==4)
			$saldo_final =  $saldo_calc_inicial + $rowor['rcomp_debe'] - $rowor['rcomp_haber'];
			else
			$saldo_final =  $saldo_calc_inicial - $rowor['rcomp_debe'] + $rowor['rcomp_haber'];

		} else {
			if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1 || substr($rowor['cpat_id'], 0, 1)==4){
				$saldo_calc_inicial = $saldo_calc_inicial + $_SESSION['rcomp_debe'] - $_SESSION['rcomp_haber'];
			}else{
				$saldo_calc_inicial = $saldo_calc_inicial - $_SESSION['rcomp_debe'] + $_SESSION['rcomp_haber'];
			}
			$_SESSION['rcomp_debe'] = $rowor['rcomp_debe'];
			$_SESSION['rcomp_haber'] = $rowor['rcomp_haber'];

			if (substr($rowor['cpat_id'], 0, 1)==6 || substr($rowor['cpat_id'], 0, 1)==1 || substr($rowor['cpat_id'], 0, 1)==4){
				$saldo_final =  $saldo_calc_inicial + $rowor['rcomp_debe'] - $rowor['rcomp_haber'];
			}else{
				$saldo_final =  $saldo_calc_inicial - $rowor['rcomp_debe'] + $rowor['rcomp_haber'];
			}
		}

		$cambio = false;
		$posSaldoFinal=$saldo_final;

		if($posSaldoFinal=="") $posSaldoFinal=0;

		$posSaldoFinal=str_replace("-",'',$posSaldoFinal);
		$posHaber=$rowor['rcomp_haber'];

		if($posHaber=="") $posHaber=0;

		$posHaber=str_replace("-",'',$posHaber);
		$posDebe=$rowor['rcomp_debe'];

		if($posDebe=="") $posDebe=0;

		$posDebe=str_replace("-",'',$posDebe);
		$posCalcInicial=$saldo_calc_inicial;

		if($posCalcInicial=="") $posCalcInicial=0;

		$posCalcInicial=str_replace("-",'',$posCalcInicial);
		$sumatoria=$posCalcInicial+$posDebe+$posHaber+$posSaldoFinal;
		if(($sumatoria)!=0){
			if ($ctaAnt != $rowor['cpat_id']) {
				?>
	<tr class="td_gray">
		<td colspan=12 height="25">
		<div align="left" class="normalNegroNegrita"><strong><?=ucwords(strtolower($rowor['cpat_nombre']))?></strong></div>

		</td>
	</tr>
	<?php
			}
			$saldoInicial=number_format($saldo_calc_inicial,2,',','.');
			if($saldoInicial=="0,00"){
				$saldoInicial="";
			}
			$saldoDebe=number_format($rowor['rcomp_debe'],2,',','.');
			if($saldoDebe=="0,00"){
				$saldoDebe="";
			}
			$saldoHaber=number_format($rowor['rcomp_haber'],2,',','.');
			if($saldoHaber=="0,00"){
				$saldoHaber="";
			}
			$saldoFinal=number_format($saldo_final,2,',','.');
			if($saldoFinal=="0,00"){
				$saldoFinal="";
			}
			$fechaEmision=$rowor['fecha_emision'];;
			if($fechaEmision==null|$fechaEmision==""){
				$fechaEmision=$_POST["hid_desde_itin"];
			}
			$fte_finaciamiento="";
			/*	if (substr($rowor['sopg'], 0, 4)=='sopg') {
			 $sql_fte= "select f.fuente_financiamiento as fuente from sai_forma_1125 f, sai_sol_pago_imputa s
			 where f.form_id_p_ac=s.sopg_acc_pp and f.form_id_aesp=s.sopg_acc_esp
			 and s.sopg_id= '".$rowor['sopg']."'";
			 //	$sql_fte= "select numero_reserva as fuente from sai_sol_pago where sopg_id= '".$rowor['sopg']."'";
			 }
			 else {
			 //	$sql_fte="select fte_financiamiento as fuente from sai_codi where comp_id='".$rowor['comp_id']."'";
			 }
			 $resultado_fte=pg_query($conexion,$sql_fte) or die("Error al consultar la fuente de financiamiento");
			 if ($row_fte=pg_fetch_array($resultado_fte)) $fte=$row_fte['fuente'];*/
			$fte=$rowor['numero_reserva'];
			?>
	<tr>
		<td width="45" align="center" class="normal"><?=$fechaEmision?></td>
		<td>
		<div align="center" class="normal">
		<?if (substr($rowor['comp_id'],0,4) == 'codi') {?>
		<!-- <a href="javascript:abrir_ventana('<?//=GetConfig("siteURL");?>/acciones/codi/codi.php?accion=buscarAccion&idCodi=<?php //echo $rowor['comp_id']; ?>')" class="copyright">	<?= $rowor['comp_id']?></a>-->
<a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/acciones/codi/codi.php?accion=generarPDF&codis=<?php echo $rowor['comp_id']; ?>')" class="copyright">	<?= $rowor['comp_id']?></a>		
		<?php } 
			else { 
				echo $rowor['comp_id'];
		}?>
		</div>
		</td>
		<td>
		<div align="center" class="normal"><a target="_blank" href="contabilidadDocumento.php?control=1&cod_doc=<?echo $rowor['sopg']?>"><?= $rowor['sopg']?></a></div>
		</td>
		<td>
		<?php
		$sql_p="SELECT monto_cheque as sopg_cheque,ctab_numero as cuenta_banco
		FROM sai_cheque c1 ,sai_chequera c2
		WHERE docg_id='".$rowor['sopg']."'
		and c1.nro_chequera=c2.nro_chequera
		union
		SELECT trans_monto as sopg_cheque,nro_cuenta_emisor as cuenta_banco
		FROM sai_pago_transferencia
		WHERE docg_id='".$rowor['sopg']."'
		";
			
		$resultadosql_p=pg_query($conexion,$sql_p);
		if ($rowpg=pg_fetch_array($resultadosql_p))
		{
			$cuenta=($rowpg['cuenta_banco']);
		}
		else{
			$cuenta="";
		}
		?>
		<div align="center" class="normal"><?= $cuenta;?></div>
		</td>
		<td>
		<div align="center" class="normal"><?= $fte;?></div>
		</td>
		<td>
		<div align="right" class="normal"><?= $rowor['nro_referencia']?></div>
		</td>
		<td align="left" class="normal"><?= $rowor['cpat_id']?></td>
		<td align="center" class="normal"><?= $saldoInicial?></td>
		<td align="center" class="normal"><?= $saldoDebe?></td>
		<td align="center" class="normal"><?= $saldoHaber?></td>
		<td align="center" class="normal"><?= $saldoFinal?></td>
		<td align="left" class="normal"><?= ucwords(strtolower(substr($rowor['comp_comen'], 0, 60)))?></td>
	</tr>
	<?php
		$ctaAnt= $rowor['cpat_id'];
		}
	}
?>
	<?php } }?>
</table>
</body>
</html>
<?php pg_close($conexion); ?>