<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 

$fecha = "";
if (isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != "") {
	$fecha = $_REQUEST['fecha'];
}
$nivel = BALANCE_GENERAL_NIVEL_TODOS;
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
function validar(){
	document.form1.action="actualizarSaldoContable.php";		
	document.form1.submit();
}
</script>
</head>
<body>
<br />
<br />
<div class="normalNegro">
<b>NOTA:</b> Este proceso permite actualizar la tabla <b>SAFI_SALDO_CONTABLE</b>.
Esta tabla almacena el saldo <b>INICIAL</b> del mes y a&ntilde;o para una cuenta contable. Ejemplo: <br></br>
<table border="1">
<tr>
<td colspan="4" align="center">SAFI_SALDO_CONTABLE</td>
</tr>
<tr>
<td>CPAT_ID</td>
<td>MES</td>
<td>A&Ntilde;O</td>
<td>MONTO</td>
</tr>
<tr>
<td>1.1.1.01.02.01.03</td>
<td>6</td>
<td>2011</td>
<td>100</td>
</tr>
</table>
<br></br>
De acuerdo al ejemplo, la cuenta contable 1.1.1.01.02.01.03 <b>cerr&oacute; el saldo</b> de mayo 2011 con Bs.100, lo cual impl&iacute;citamente significa que el <b>saldo inicial</b> del mes de junio 2011 es Bs. 100 <br></br>
Es importante para correr este proceso, seleccionar <b>SIEMPRE el &uacute;ltimo d&iacute;a del mes anterior</b> que deseen registrar.<br></br>
Por ejemplo si se desea registrar el mes de <b>MAYO 2011</b>, entonces se debe seleccionar de fecha <b>30/04/2011</b>. 
 </div>
 <br></br>
<form name="form1" method="post" >
	<table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="3" class="normalNegroNegrita">ACTUALIZAR SALDOS CONTABLES</td>
		</tr>
		<tr>
			<td class="normalNegrita">Fecha:</td>
			<td colspan="2" class="normal">
				<input value="<?= (($fecha!="")?$fecha:date('d/m/Y'))?>" type="text" size="10" id="fecha" name="fecha" class="dateparse" readonly="readonly"/>
				<a 	href="javascript:void(0);"
					onclick="g_Calendar.show(event, 'fecha');"
					title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/>
				</a>
			</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input type="button" value="Llenar" onclick="validar();" />
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
	if ($mes<>12) {
		$mesCalculo = $mes +1;
		$anoCalculo= $ano;
	}
	else {
		$mesCalculo=1;
		$anoCalculo=$ano+1;
	}
	
	$ano_antes = $ano-1;
	$max_mes = 0;
	$max_mes_antes = 0;	
	$error="0";
	$consulta_basica=0;
	$condicion= "1==1";
	
	
	$sql = "SELECT cpat_id FROM safi_saldo_contable WHERE mes=".$mesCalculo." AND ano=".$anoCalculo;
	$resultado=pg_query($conexion,$sql);
	$nro = pg_num_rows($resultado);
	if ($nro<1) $valido =1;
	else {
		$valido = 0;
		echo "YA SE ENCUENTRA REGISTRADO EN SALDO CONTABLE ESE MES Y ESE A&Ntilde;O";
	}
	

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
				} 	
				else {
					$max_mes_antes=$nro;
					$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes_antes." and ano=".$ano_antes;
				}
			}
			else {
				$max_mes=$row["mes"];
				$sql_saldo= "select cpat_id, saldo from safi_saldo_contable where mes=".$max_mes." and ano=".$ano;
			}
	}
	else {
		
		$max_mes = $mes;
		if ($dia=="01") $consulta_basica=1;
	}

	if ($max_mes!=0) {
		if ($max_mes == 1 || $max_mes == 2 || $max_mes == 3 || $max_mes == 4 || $max_mes == 5 || $max_mes == 6 || $max_mes == 7 || $max_mes == 8 || $max_mes == 9) {
			$mes_x=$max_mes;
			$max_mes= "0".$max_mes;

		}
		$mes_total = $max_mes;
		$ano_total = $ano;  
	}
	else {
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
		$sql_or="SELECT substring(trim(sc.cpat_id) from 1 for 2), sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
		scs.saldo as saldo
		FROM sai_cue_pat sc, (".$sql_saldo.") scs 
		where sc.cpat_id = scs.cpat_id   order by sc.cpat_id ";
	}
	else {
		$login=$_SESSION['login'];
		require_once("saldoDiarioActualizadoGenericas.php");	

		/*Búsqueda de movimientos en las fechas registradas*/
		$sql_or="SELECT substring(trim(sc.cpat_id) from 1 for 2), sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
		scs.saldo as saldo
		FROM sai_cue_pat sc, (".$sql_total.") scs 
		where sc.cpat_id = scs.cpat_id    order by sc.cpat_id ";
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
	else if ($nivel == BALANCE_GENERAL_NIVEL_DOS) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00' || substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
	else if ($nivel == BALANCE_GENERAL_NIVEL_TRES) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',6,11) == '00.00.00.00' || substr('".$rowor['cpat_id']."',9,8) == '00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
	else if ($nivel == BALANCE_GENERAL_NIVEL_CUATRO) $condicion = "return (substr('".$rowor['cpat_id']."',2,15) == '0.0.00.00.00.00' || substr('".$rowor['cpat_id']."',4,13) == '0.00.00.00.00' || substr('".$rowor['cpat_id']."',9,8) == '00.00.00' || substr('".$rowor['cpat_id']."',15,2) != '00');";
	else $condicion = "return (1==1);";		

	if ($valido ==1) {
		$saldo = number_format($rowor['saldo'],2,',','.');
		$saldo = str_replace(".","",$saldo);
		$saldo = str_replace(",",".",$saldo);
		
		$sql = "INSERT INTO safi_saldo_contable (cpat_id, mes, ano, saldo) values ('".$rowor['cpat_id']."',".$mesCalculo.",".$anoCalculo.", '".$saldo."')";
		$resultado=pg_query($conexion,$sql);		
	}
		
   ?>
   <tr <?=$fondo_str;?>>
    <td align="center" <?=$clase;?> ><?php echo $rowor['cpat_id'];?></div></td>
    <td  <?=$clase;?>> <div align="left" class="normal">
	<img src="../../imagenes/pixel_blanco.gif"  height="1"/>
	<?php echo $espaciado_nombre.strtolower($rowor['cpat_nombre']);?></div></td>
    <td align="right" class="normal" <?=$clase;?> <?=$ancho;?>>
	<img src="../../imagenes/pixel_blanco.gif" height="1"/>
	<?php echo $espaciado.number_format($rowor['saldo'],2,',','.');?></td>
  </tr>
   <?php   } ?>
</table>
<?php }?>
<br></br>
<table width="80%" border="0" class="normalNegrita" align="center">

</table>
</body>
</html>
<? }pg_close($conexion);?>