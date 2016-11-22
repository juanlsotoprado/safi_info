<?php
ob_start();
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
		document.form1.action="mayorAnalitico.php";
	}else{
		document.form1.action="mayorAnaliticoPDF.php";
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
		alert("La fecha inicial no debe se mayor a la fecha final"); 
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
<br/>
<br/>
<form name="form1" method="post">
<table width="55%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">MAYOR ANAL&Iacute;TICO</td>
	</tr>				<tr>
					<td class="normalNegrita">Fecha Inicio:
						<input type="text" size="10" id="hid_desde_itin"
						name="hid_desde_itin" class="dateparse"
						onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar">
							<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/>
						</a>
					</td>
					<td class="normalNegrita">
						Fecha Fin:
						<input type="text" size="10" id="hid_hasta_itin"
						name="hid_hasta_itin" class="dateparse"
						onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
						<a href="javascript:void(0);"
						onclick="g_Calendar.show(event, 'hid_hasta_itin');"
						title="Show popup calendar">
							<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
						</a>
					</td>
				</tr>
				<tr>
					<td class="normalNegrita">Cuenta contable:</td>
					<td>
						<input type="hidden" value="" name="cuenta" id="cuenta"/>
						<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="" class="normal"/>
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
				<tr><td colspan="2" align="center">
				<input type="button" value="Listar" onclick="validar(1)"/>
				<input type="hidden" name="login" value="<?= $_SESSION['login']?>"/>
				<input type="button" value="PDF" onclick="validar(2)"/>
				</td></tr>
			</table>
	</div>
</form>
<br/>
<?php
if(isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])) {
	$fecha_inicio = $_POST["hid_desde_itin"];
	$dia = substr($fecha_inicio, 0, 2);
	$dia1 = $dia - 1;
	$resto = substr($fecha_inicio, 2, 8);
	$fecha_inicio_antes = date("d/m/Y",mktime(0,0,0,substr($fecha_inicio, 3, 2),(substr($fecha_inicio, 0, 2) - 1),substr($fecha_inicio, 6)));;
	$fecha_fin = $_POST["hid_hasta_itin"];
	$_SESSION['fecha_inicio'] = '30/06/2008';
	$_SESSION['fecha_fin'] = $fecha_inicio_antes;

	$login=$_SESSION['login'];
	$fechaIinicio=$_SESSION['fecha_inicio'];
	$fechaFfin=$_SESSION['fecha_fin'];
	require_once("saldoDiarioActualizadoMayorAnalitico.php");

	/*Búsqueda de movimientos en las fechas registradas*/
	if (isset($_POST["cuenta"]) && strlen($_POST["cuenta"]) > 1){
		$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, EXTRACT(DAY FROM comp_fec)||'/'||EXTRACT(month FROM comp_fec)||'/'||EXTRACT(Year FROM comp_fec) as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,scd.nro_referencia
				FROM sai_cue_pat sc 
				left outer join  sai_reng_comp src on (src.cpat_id = sc.cpat_id)
				left outer join  sai_cue_pat_saldodiario_".$_SESSION['login']." scp on (scp.cpat_id = sc.cpat_id )
				left outer join  sai_comp_diario scd on (scd.comp_id = src.comp_id)
				where scd.esta_id<>'15' and sc.cpat_id = '".$_POST["cuenta"].trim()."' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
				and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY') order by sc.cpat_id, scd.comp_fec";
	}else{
		$sql = "SELECT src.comp_id, src.reng_comp, sc.cpat_id, sc.cpat_nombre, src.rcomp_debe, src.rcomp_haber, src.rcomp_tot_db, src.rcomp_tot_hab, EXTRACT(DAY FROM comp_fec)||'/'||EXTRACT(month FROM comp_fec)||'/'||EXTRACT(Year FROM comp_fec) as fecha_emision, scd.comp_comen, scp.saldo,scd.comp_doc_id,scd.nro_referencia
				FROM sai_cue_pat sc 
				left outer join  sai_reng_comp src on (src.cpat_id = sc.cpat_id) 
				left outer join  sai_cue_pat_saldodiario_".$_SESSION['login']." scp on (scp.cpat_id = sc.cpat_id )
				left outer join  sai_comp_diario scd on (scd.comp_id = src.comp_id)
				where scd.esta_id<>'15' and substring(trim(sc.cpat_id) from 16 for 17) != '00' 
				and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') >= to_date('".$_POST["hid_desde_itin"]."', 'DD MM YYYY') and to_date(to_char(scd.comp_fec, 'DD MM YYYY'), 'DD MM YYYY') <= to_date('".$_POST["hid_hasta_itin"]."', 'DD MM YYYY')
				order by sc.cpat_id, scd.comp_fec";
	}

	$ctaAnt="0";
	$resultado_set_most_or=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
	$titulopdf="MAYOR ANALÍTICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"];
?>
<div align="center" class="normalNegroNegrita"><?= "MAYOR ANAL&Iacute;TICO ENTRE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"]?></div>
<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td class="normalNegroNegrita">Fecha</td>
		<td class="normalNegroNegrita">Identif.</td>
		<td class="normalNegroNegrita">Sopg</td>
		<td class="normalNegroNegrita">Ref.</td>
		<td class="normalNegroNegrita">Cta Contable</td>
		<td class="normalNegroNegrita">S. Inicial</td>
		<td class="normalNegroNegrita">Debe</td>
		<td class="normalNegroNegrita">Haber</td>
		<td class="normalNegroNegrita">S. Final</td>
		<td class="normalNegroNegrita">Comentario</td>
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
			//$imprimir_saldo_inicial = $saldo_inicial;
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
		<td colspan=10>&nbsp;&nbsp;&nbsp;
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
		$sopgg=substr($rowor['comp_doc_id'], 0, 1).substr($rowor['comp_doc_id'], 5);
		if(substr($rowor['comp_doc_id'], 0, 4)=='pgch'){
			$sql_sopg="select docg_id from sai_pago_cheque where pgch_id='".$rowor['comp_doc_id']."'";
			$resultado_sopg=pg_query($conexion,$sql_sopg) or die("Error al consultar el sopg del pgch");
   	 		if ($rowsopg=pg_fetch_array($resultado_sopg)) $sopgg=substr($rowsopg['docg_id'], 0, 1).substr($rowsopg['docg_id'], 5);
       }
       else  if(substr($rowor['comp_doc_id'], 0, 4)=='tran'){   			
			$sql_sopg="select docg_id from sai_pago_transferencia where trans_id='".$rowor['comp_doc_id']."'";
			$resultado_sopg=pg_query($conexion,$sql_sopg) or die("Error al consultar el sopg del tran");
       		if ($rowsopg=pg_fetch_array($resultado_sopg)) $sopgg=substr($rowsopg['docg_id'], 0, 1).substr($rowsopg['docg_id'], 5);
	   }
?>
	<tr>
		<td width="45" align="center" class="normal"><?= $fechaEmision?></td>
		<td>
			<div align="center" class="normal"><?= $rowor['comp_id']?></div>
		</td>
		<td>
			<div align="center" class="normal"><?= $sopgg?></div>
		</td>
		<td>
			<div align="right" class="normal"><?= $rowor['nro_referencia']?></div>
		</td>
			<td width="75" align="left" class="normal"><?= $rowor['cpat_id']?>
		</td>
		<td width="75" align="center" class="normal"><?= $saldoInicial?></td>
		<td width="65" align="center" class="normal"><?= $saldoDebe?></td>
		<td width="65" align="center" class="normal"><?= $saldoHaber?></td>
		<td width="75" align="center" class="normal"><?= $saldoFinal?></td>
		<td width="85" align="left" class="normal"><?= ucwords(strtolower(substr($rowor['comp_comen'], 0, 60)))?></td>
	</tr>
<?php
		$ctaAnt= $rowor['cpat_id'];
		}
	}
?>
<?php } ?>
</table>
</body>
</html>