<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
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
		document.form1.action="balanceComprobacion.php";		
	}else{
		document.form1.action="balanceComprobacionPDF.php";
	}
	document.form1.submit();
}
</script>
</head>
<body>
<br />
<br />
<form name="form1" method="post" >
<table width="30%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="3" class="normalNegroNegrita">BALANCE DE COMPROBACI&Oacute;N 
		<?php 
			if (isset($_POST["txt_inicio"]) ||  $_GET["txt_inicio"]=='1') {
				echo ' AL '.$_REQUEST["txt_inicio"];
			}
		?>
		</td>
	</tr>
<tr>

<td class="normalNegrita">Fecha:</td>
<td class="normalNegrita" colspan="2">
	<input type="text"
			size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
			onfocus="javascript: comparar_fechas(this);" readonly="readonly" /> <a
			href="javascript:void(0);"
			onclick="g_Calendar.show(event, 'txt_inicio');"
			title="Show popup calendar"> <img
			src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
			alt="Open popup calendar" 
			/> </a></td>
</tr>			
<tr>					
<td colspan="3" align="center"><input type="button" value="Listar" onclick="validar(1);"/><input type="hidden" name="login" value="<?php echo $_SESSION['login']?>">
<input type="button" value="PDF" onclick="validar(2);"/></td>
</tr>
</table>
</font>
</div>
</form>
<br />
<?php 
if (isset($_POST["txt_inicio"]) ||  $_GET["txt_inicio"]=='1') {
	$fecha_inicio = $_POST["txt_inicio"];
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
		//if ($dia=="01") $consulta_basica=1;
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
	$sql_or="SELECT sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
	case substring(trim(sc.cpat_id) from 1 for 1) when '1' then  scs.saldo when '6' then  scs.saldo else   case substring(trim(sc.cpat_id) from 1 for 1) when '4' then  scs.saldo else 0 end  end as debe,
	case substring(trim(sc.cpat_id) from 1 for 1) when '2' then  scs.saldo when '5' then  scs.saldo when '3' then  scs.saldo  else 0  end as haber  
	FROM sai_cue_pat sc, (".$sql_saldo.") scs 
	where sc.cpat_id = scs.cpat_id and substring(trim(sc.cpat_id) from 16 for 17) != '00'   order by sc.cpat_id ";
	}
	else {
		$login=$_SESSION['login'];
		require_once("saldoDiarioActualizado.php");	

		/*Búsqueda de movimientos en las fechas registradas*/
		$sql_or="SELECT sc.cpat_id as cpat_id, sc.cpat_nombre as cpat_nombre, sc.cpat_nivel as cpat_nivel,
		case substring(trim(sc.cpat_id) from 1 for 1) when '1' then  scs.saldo when '6' then  scs.saldo else   case substring(trim(sc.cpat_id) from 1 for 1) when '4' then  scs.saldo else 0 end  end as debe,
		case substring(trim(sc.cpat_id) from 1 for 1) when '2' then  scs.saldo when '5' then  scs.saldo when '3' then  scs.saldo  else 0  end as haber  
		FROM sai_cue_pat sc, (".$sql_total.") scs 
		where sc.cpat_id = scs.cpat_id and substring(trim(sc.cpat_id) from 16 for 17) != '00'   order by sc.cpat_id ";
	}	
		
	$resultado_set_most_or=pg_query($conexion,$sql_or) ;
?>
<table width="90%" border="0" align="center" class="tablaalertas">
   <tr class="td_gray" align="center">
     <td class="normalNegroNegrita">CUENTAS</td>
     <td class="normalNegroNegrita">DESCRIPCI&Oacute;N</td>
     <td class="normalNegroNegrita">D&Eacute;BITO</td>
     <td class="normalNegroNegrita">CR&Eacute;DITO</td>
   </tr>
    <?php //Inicio del While 
    $totalDebe=0;
    $totalHaber=0;
    $debe=0;
    $haber=0;
   while($rowor=pg_fetch_array($resultado_set_most_or)) {
    	if(substr($rowor['debe'],0,1)=='-'){
    		$debe=0;
    		$haber=(-1)*$rowor['debe'];
    	}else{
    		$debe=$rowor['debe'];
    		if(substr($rowor['haber'],0,1)=='-'){    			
    			$debe=(-1)*$rowor['haber'];
    			$haber=0;
    		}else{
    			$haber=$rowor['haber'];
    		}
    	}
	$totalDebe=$totalDebe+$debe;
	$totalHaber=$totalHaber+$haber;
	
	if(number_format($debe,2,',','.')=='0,00'&&number_format($haber,2,',','.')=='0,00'){
    	}else{
   ?>
  <tr>
    <td align="center" class="normal"><?php echo $rowor['cpat_id'];?></div></td>
    <td width="350"> <div align="left" class="normal">
	<img src="../../imagenes/pixel_blanco.gif"  height="1"/>
	<?php echo ucwords(strtolower($rowor['cpat_nombre']));?></div></td>
    <td align="right" class="normal" >
	<img src="../../imagenes/pixel_blanco.gif" height="1"/>
	<?php echo number_format($debe,2,',','.');?></td>
	<td align="right" class="normal">
	<img src="../../imagenes/pixel_blanco.gif" height="1"/>
	<?php echo number_format($haber,2,',','.');?></td>
  </tr>
  <?php }} ?>
  <tr><td></td><td class="normal" align="right">Total</td><td class="normal" align="right"><b><?php echo number_format($totalDebe,2,',','.');?></b></td><td class="normal" align="right"><b><?php echo number_format($totalHaber,2,',','.');?></b></td></tr>
</table>
<?php }?>
<p>&nbsp;</p>
</body>
</html>
<? }pg_close($conexion);?>