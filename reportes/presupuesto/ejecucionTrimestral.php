<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
	 
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 	
	exit;
}
ob_end_flush(); 
	  
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
	 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Momento Presupuestario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script>
function detalle(codigo) {
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar() { 
	document.form.hid_validar.value=2;
  	document.form.submit();
}

function validar(op){
	document.form1.hid_validar.value=2;
		if(op==1){
			document.form1.action="ejecucionTrimestral.php";		
		}else if(op==3){
			document.form1.action="momentoPresupuestarioPDF.php";
		}else if(op==4){
			document.form1.action="momentoPresupuestarioXLS.php";
		}
		document.form1.submit();
}
</script>
</head>
<body>
<form name="form1"  method="post">
 <input type="hidden" value="0" name="hid_validar" />
  <table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="21" colspan="2" class="normalNegroNegrita" align="left">Ejecuci&oacute;n Presupuestaria</td>
</tr>
<tr>
  <td height="10" colspan="3"></td>
</tr>
<tr>
	<td width="175" height="29" class="normalNegrita" align="left">Trimestre:</td>
	<td ><select name="trimestre" class="normal">
	<option value="0">--</option>
	<option value="1">1er Trimestre</option>
	<option value="2">2do Trimestre</option>
	<option value="3">3er Trimestre</option>
	<option value="4">4to Trimestre</option>
	</select>
</td></tr>
<tr>
	<td class="normalNegrita" align="left">Tipo de Reporte: </td>
	<td><select name="tipo_reporte" class="normal">
	<option value="0">--</option>
	<option value="1">Forma 0703</option>
	<option value="2">Forma 0704</option>
	</select>
	</td>
</tr>
<tr><td colspan=5 class="normal"><center><input type="button" value="Buscar" onclick="validar(1);"/>

</tr>
</table>

</form>
<br/>
<form name="form3" action="" method="post">
<?php 
if ($_POST['hid_validar']==2) {


?>
  <table width="100%" border="0" align="center">
        <tr>
		<?php
		    $ano1=substr($fecha_ini,0,4);
			$mes1=substr($fecha_ini,5,2);
			$dia1=substr($fecha_ini,8,2);
			
			$ano2=substr($fecha_fin,0,4);
			$mes2=substr($fecha_fin,5,2);
			$dia2=substr($fecha_fin,8,2);
		?>
          <td width="495" height="27" class="normalNegro"><div align="center">Resultado de la ejecuci&oacute;n del <b> X</b> trimestre </div></td>
        </tr>
  </table>
<table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray">
	<td width="137" rowspan="2" class="normalNegroNegrita" align="center">RAMO DE INGRESOS </td>
	<td width="102" rowspan="2" class="normalNegroNegrita" align="center">DENOMINACION</td>
	<td width="102" rowspan="2" class="normalNegroNegrita" align="center">PRESUPUESTO APROBADO</td>
    <td width="102" rowspan="2" class="normalNegroNegrita" align="center">PRESUPUESTO MODIFICADO</td>
    <td width="128" rowspan="2" class="normalNegroNegrita" align="center">PROGRAMADO EN EL TRIMESTRE N&deg;</td>
    <td width="128" class="normalNegroNegrita" colspan="3" align="center">EJECUTADO EN EL TRIMESTRE N&deg;</td>
   	<td width="102" class="normalNegroNegrita" colspan="4" align="center">ACUMULADO AL TRIMESTRE N&deg;</td>
    <td width="128" rowspan="2" class="normalNegroNegrita" align="center">INGRESOS POR RECIBIR</td>
  </tr>
  <tr class="td_gray">
	<td width="137" class="normalNegroNegrita" align="center">DEVENGADO</td>
	<td width="137" class="normalNegroNegrita" align="center">LIQUIDADO</td>
	<td width="137" class="normalNegroNegrita" align="center">RECAUDADO</td>
	<td width="137" class="normalNegroNegrita" align="center">PROGRAMADO</td>
	<td width="137" class="normalNegroNegrita" align="center">DEVENGADO</td>
	<td width="137" class="normalNegroNegrita" align="center">LIQUIDADO</td>
	<td width="137" class="normalNegroNegrita" align="center">RECAUDADO</td>
  </tr>
  <tr>
	<td width="137" class="normalNegro" align="center">3.05</td>
	<td width="102" class="normalNegro" align="center">Transferencias y Donaciones Corrientes</td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	</tr>
  <tr>
	<td width="137" class="normalNegro" align="center">3.06</td>
	<td width="102" class="normalNegro" align="center">Ingresos de Capital</td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	</tr>
	  <tr>
	<td width="137" class="normalNegro" align="center">3.11</td>
	<td width="102" class="normalNegro" align="center">Disminuci&oacute;n de Otros Activos Financieros</td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	<td width="102" class="normalNegroNegrita" align="center"></td>
	</tr>	
   

		<?
while ($rowor=pg_fetch_array($resultado_set_most_or))  {
		$beneficiario_nombre= "";
		$beneficiario = "";
		$comp=$rowor['comp_id'];
		$sql="select * from sai_seleccionar_campo('sai_comp','pcta_id','comp_id=''$comp''','',2) resultado_set (pcta_id varchar)";
		$resultado=pg_query($conexion,$sql);
		if ($row=pg_fetch_array($resultado)){
			$pcta=substr($row['pcta_id'],0,4);
		}?>
		<tr class="normal">
	  <td height="28" align="center"><span class="link">
<a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a>
</span></td>
						<td align="center"><span class="peq"><?php echo $rowor['centrogestor'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['centrocosto'];?></span></td>
						
						<td align="center"><span class="peq"><?php echo $rowor['comp_fecha'];?></span></td>
						<?php  if (($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)  || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO)){ ?>
						<td align="center"><span class="peq">
						<?php if($rowor['esta_id']==15) {?>
                          <a href="comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']);?>">Ver Detalle</a>
						
						<?php }else{?>
						
							<a href="comp_2.php?id=<?php echo trim($rowor['comp_id']);?>">Modificar/Anular</a>
						<?php }?>
						
						</span></td><?php }?>
						</tr>
<?php	 
				}//fin del while que obtiene los datos de la consulta
			
?> 
 </table> 
<?}
pg_close($conexion);
?>
</form>
</body>
</html>