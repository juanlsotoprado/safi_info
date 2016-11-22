<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/constantes.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 

$tipoReporte = "0";
if (isset($_REQUEST['tipoReporte']) && $_REQUEST['tipoReporte'] != "") {
	$tipoReporte = $_REQUEST['tipoReporte'];
}

$fecha = "";
if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
	$fecha = $_REQUEST['fecha'];
}

$nivel = BALANCE_GENERAL_NIVEL_TODOS;
if (isset($_REQUEST['nivel']) && $_REQUEST['nivel'] != "") {
	$nivel = $_REQUEST['nivel'];
}

/*$posicion1=strpos($nivel, "1");
$posicion2=strpos($nivel, "2");
$posicion3=strpos($nivel, "3");
$posicion4=strpos($nivel, "4");
$posicion5=strpos($nivel, "5");
$posicion6=strpos($nivel, "6");
$posicion7=strpos($nivel, "7");*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Balance de Comprobaci&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script>
function validar(op){
	if(op==1){
		document.form1.action="balanceGeneral.php";		
	}else{
		document.form1.action="balanceGeneralPDF.php";
	}
	document.form1.submit();
}
</script>
</head>
<body>
<br />
<br />
<form name="form1" method="post" >
	<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="3" class="normalNegroNegrita">BALANCE GENERAL / EDO. RESULTADOS</td>
		</tr>
		<tr>
			<td class="normalNegrita">Tipo de Reporte:</td>
			<td colspan="2" class="normal">
				<select name="tipoReporte" class="normal">
					<option value="0" <?php if($tipoReporte=="0"){echo 'selected="selected"';}?>>Balance general</option>
					<option value="1" <?php if($tipoReporte=="1"){echo 'selected="selected"';}?>>Estado de resultados</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Fecha:</td>
			<td colspan="2" class="normal">
				<input value="<?= ($fecha!="")?$fecha:date('d/m/Y')?>" type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly"/>
				<a 	href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fecha');"
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/>
				</a>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Nivel del reporte:</td>
			<td colspan="2" class="localizadornegrita">
				<select name="nivel" class="normal">
					<option value="<?= BALANCE_GENERAL_NIVEL_TODOS?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_TODOS){echo 'selected="selected"';}?>>Todos</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_UNO?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_UNO){echo 'selected="selected"';}?>>1</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_DOS?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_DOS){echo 'selected="selected"';}?>>1,2</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_TRES?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_TRES){echo 'selected="selected"';}?>>1,2,3</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_CUATRO?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_CUATRO){echo 'selected="selected"';}?>>1,2,3,4</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_CINCO?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_CINCO){echo 'selected="selected"';}?>>1,2,3,4,5</option>
					<option value="<?= BALANCE_GENERAL_NIVEL_SEIS?>" <?php if($nivel == BALANCE_GENERAL_NIVEL_SEIS){echo 'selected="selected"';}?>>1,2,3,4,5,6</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input type="button" value="Listar" onclick="validar(1);" />
				<input type="button" value="PDF" onclick="validar(2);" />
			</td>
		</tr>
	</table>
</form>
<br />
<?php
if (isset($_POST["fecha"])) {
	$fecha_inicio = $_POST["fecha"];
	$dia = substr($fecha_inicio, 0, 2);
	$mes = substr($fecha_inicio, 3, 2)+1-1;
	$ano = substr($fecha_inicio, 6, 4);
	$ano_antes = $ano-1;
	$max_mes = 0;
	$max_mes_antes = 0;	
	$error="0";
	$consulta_basica=0;
	$nivel = $_POST["nivel"]; 
	$condicion= "1==1";

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
			} else {
				$max_mes_antes=$nro;
				$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes_antes." and ano=".$ano_antes;
			}
		} else {
			$max_mes=$row["mes"];
			$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes." and ano=".$ano;
		}
	} else {
		
		$max_mes = $mes;
		
		//if ($dia=="01") $consulta_basica=0;
	}

	if ($max_mes!=0) {
		if ($max_mes == 1 || $max_mes == 2 || $max_mes == 3 || $max_mes == 4 || $max_mes == 5 || $max_mes == 6 || $max_mes == 7 || $max_mes == 8 || $max_mes == 9) {
			$mes_x=$max_mes;
			$max_mes= "0".$max_mes;

		}
		$mes_total = $max_mes;
		$ano_total = $ano;  
	} else {
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
		$fechaIinicio = "01/".$mes_total."/".$ano_total;
		$fechaFfin = $fecha_inicio;
		
		if ($consulta_basica==1) {
			$sql_or = "	SELECT 
							substring(trim(sc.cpat_id) from 1 for 2), 
							sc.cpat_id as cpat_id, 
							sc.cpat_nombre as cpat_nombre, 
							sc.cpat_nivel as cpat_nivel,
							scs.saldo as saldo
						FROM sai_cue_pat sc, (".$sql_saldo.") scs 
						WHERE sc.cpat_id = scs.cpat_id 
						ORDER BY sc.cpat_id ";
		} else {
			$login=$_SESSION['login'];
			require_once("saldoDiarioActualizadoGenericas.php");	
	
			/*Búsqueda de movimientos en las fechas registradas*/
			$sql_or = "	SELECT 
							substring(trim(sc.cpat_id) from 1 for 2), 
							sc.cpat_id as cpat_id, 
							sc.cpat_nombre as cpat_nombre, 
							sc.cpat_nivel as cpat_nivel,
							scs.saldo as saldo
						FROM sai_cue_pat sc, (".$sql_total.") scs 
						WHERE sc.cpat_id = scs.cpat_id 
						ORDER BY sc.cpat_id ";
		}	
			
		$resultado_set_most_or=pg_query($conexion,$sql_or) ;
?>
<table width="80%" border="0" align="center" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td class="normalNegroNegrita">CUENTAS</td>
		<td class="normalNegroNegrita">DESCRIPCI&Oacute;N</td>
		<td class="normalNegroNegrita">SALDO ACTUAL</td>
	</tr>
<?php //Inicio del While 
		while($rowor=pg_fetch_array($resultado_set_most_or)) {
if (floatval($rowor['saldo']) > 0.009 OR floatval($rowor['saldo']) < -0.009) {
			$nivelCuenta=$rowor['cpat_nivel'];
			$clase="class='normal'";
			$fondo_str="";
			$ancho = '';
			
			switch ($nivelCuenta) {
				case 1:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;";
					$ancho = "style='width:190px;float: left;'";
					$fondo_str="bgcolor='#E4E4E4'";
					$clase="class='normalNegrita'";
					break;
				case 2:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;";
					$ancho = "style='width:160px;float: left;'";
					break;
				case 3:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$ancho = "style='width:130px;float: left;'";
					break;
				case 4:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$ancho = "style='width:100px;float: left;'";
					break;
				case 5:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					$ancho = "style='width:70px;float: left;'";
					break;
				case 6:
					$espaciado = "&nbsp;&nbsp;&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";				
					$ancho = "style='width:40px;float: left;'";
					break;
				case 7:
					$espaciado = "&nbsp;&nbsp;";
					$espaciado_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";				
					$ancho = "style='width:10px;float: left;'";
					break;
			}
		
			if($rowor['cpat_id']=='2.0.0.00.00.00.00') 	$pasivo = $rowor['saldo'];
			if($rowor['cpat_id']=='3.0.0.00.00.00.00') 	$patrimonio = $rowor['saldo'];	
			if($rowor['cpat_id']=='5.0.0.00.00.00.00') 	$ingresos = $rowor['saldo'];
			if($rowor['cpat_id']=='6.0.0.00.00.00.00') 	$gastos = $rowor['saldo'];
			$subtotal_pasivo_patrimonio = $pasivo + $patrimonio;
			$resultados = $ingresos - $gastos;
			$total = $subtotal_pasivo_patrimonio - $resultados;
	  
			if ($nivel == BALANCE_GENERAL_NIVEL_UNO) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00');";
			else if ($nivel == BALANCE_GENERAL_NIVEL_DOS) $condicion = "return (substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00');";
			else if ($nivel == BALANCE_GENERAL_NIVEL_TRES) $condicion = "return (substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00');";
			else if ($nivel == BALANCE_GENERAL_NIVEL_CUATRO) $condicion = "return (substr('".$rowor['cpat_id']."',9,8) == '00.00.00');";
			else if ($nivel == BALANCE_GENERAL_NIVEL_CINCO) $condicion = "return (substr('".$rowor['cpat_id']."',12,5) == '00.00');";
			else if ($nivel == BALANCE_GENERAL_NIVEL_SEIS) $condicion = "return (substr('".$rowor['cpat_id']."',15,2) == '00');";
			else $condicion = "return (1==1);";
	
			if( $tipoReporte=="0" && substr($rowor['cpat_id'],0,1)!='5' && substr($rowor['cpat_id'],0,1)!='6' && eval($condicion)==1) {
?>
			<tr <?=$fondo_str;?>>
				<td align="center" <?=$clase;?>>
				<?
					echo $rowor['cpat_id'];
				?></td>
				<td <?=$clase;?>>
					<div align="left" class="normal">
						<img src="../../imagenes/pixel_blanco.gif" height="1"/>
						<?php
							if ( $nivelCuenta == 1 ) {
								echo $espaciado_nombre.strtoupper($rowor['cpat_nombre']);
							} else {
								echo $espaciado_nombre.ucwords(strtolower($rowor['cpat_nombre']));
							}
						?>
					</div>
				</td>
				<td align="right" class="normal" <?=$clase;?> <?=""/*$ancho*/;?>>
					<img src="../../imagenes/pixel_blanco.gif" height="1"/>
					<? 
						echo number_format($rowor['saldo'],2,',','.');
					?>
				</td>
		  	</tr>
<?php
			} else if( $tipoReporte=="1" && (substr($rowor['cpat_id'],0,1)=='5' || substr($rowor['cpat_id'],0,1)=='6') && eval($condicion)==1) {
?>
			<tr <?=$fondo_str;?>>
				<td align="center" <?=$clase;?> >
				<?
					echo $rowor['cpat_id'];
				?></td>
	    		<td <?=$clase;?>>
	    			<div align="left" class="normal">
						<img src="../../imagenes/pixel_blanco.gif"  height="1"/>
						<?php
							if ( $nivelCuenta == 1 ) {
								echo $espaciado_nombre.strtoupper($rowor['cpat_nombre']);
							} else {
								echo $espaciado_nombre.ucwords(strtolower($rowor['cpat_nombre']));
							}
						?>
					</div>
				</td>
	    		<td align="right" class="normal" <?=$clase;?> <?=""/*$ancho*/;?>>
					<img src="../../imagenes/pixel_blanco.gif" height="1"/>
					<? /*$espaciado.*/
						echo number_format($rowor['saldo'],2,',','.');
					?>
				</td>
	  		</tr>
<?php
			}
		}
}
?>
</table>
<?php 
	}
?>
<br/>
<table width="80%" border="0" class="normalNegrita" align="center">
<?php if( $tipoReporte=="0") {?>
	<tr>
		<td>Sub Total Pasivo y Patrimonio Bs.F <?= number_format($subtotal_pasivo_patrimonio,2,'.','.');?></td>
	</tr>
	<tr>
		<td>Resultados del mes Bs.F<?= number_format($resultados,2,'.','.');?></td>
	</tr>	
	<tr>
		<td>Total Pasivo y Patrimonio: Bs.F.<?= number_format($subtotal_pasivo_patrimonio + $resultados,2,'.','.');?></td>
	</tr>
	
	<!-- <tr>
		<td>Total Pasivo y Patrimonio: Bs.F. <?= number_format($total,2,',','.');?></td>
	</tr> -->
<?php } else {?>
	<tr>
		<td>Resultados del mes: Bs.F. <?= number_format($resultados,2,',','.');?></td>
	</tr>
<?php }?>
</table>
</body>
</html>
<?
}
pg_close($conexion);
?>