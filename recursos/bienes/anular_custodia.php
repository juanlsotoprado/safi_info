<?
ob_start();
require_once("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
require("../../includes/fechas.php");
 
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

ob_end_flush();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="javascript">

function enviar(tipo){
	 document.form.opcion.value=tipo;
	 contenido=prompt("Indique el motivo de la anulaci\u00F3n: ","");
	 if (contenido!=null){
  		document.getElementById('contenido_memo').value=contenido;
  		document.form.submit();
 	}
 	 
}
</script>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php 
$acta=$_GET['codigo'];
$sql_custodia="SELECT cantidad,medida,nombre,to_char(fecha_acta,'DD/MM/YYYY') as fecha FROM sai_bien_item_custodia t1,sai_item t2,sai_bien_custodia t3 WHERE t1.acta_id=t3.acta_id and t1.acta_id='".$acta."' and t1.id=t2.id";
$resultado_custodia=pg_query($conexion,$sql_custodia) or die("Error al consultar el detalle de la custodia");
if ($resultado_custodia){
  $num_articulos=pg_num_rows($resultado_custodia);	
}

/*MUESTRA LOS ARTICULOS*/
if ($num_articulos>0){
$contenido .="<tr><td><table border=1 width='80%' align='center'>".
  "<tr class='normalNegro' align='center'>
	<td><b>ITEM</b></td>
	<td><b>UNIDAD MEDIDA</b></td>
	<td><b>CANTIDAD</b></td></tr>";

while($rowcustodia=pg_fetch_array($resultado_custodia)) 
{ 
$fecha=$rowcustodia['fecha'];
$contenido .="<tr class='textoTabla' align='center'>
				<td class='normalNegro'>".$rowcustodia['nombre']."</td>
			    <td class='normalNegro'>".$rowcustodia['medida']."</td>
				<td class='normalNegro'>".$rowcustodia['cantidad']."</td></tr>";
}
$contenido .=" </table></td></tr><br/>";

}

$sql_custodia="SELECT * 
FROM sai_bien_item_custodia t1,sai_item t2, sai_biin_items t3
WHERE 
t1.acta_id='".$acta."' and t3.bien_id=t2.id and t1.clave_bien=t3.clave_bien";
$resultado_custodia=pg_query($conexion,$sql_custodia) or die("Error al consultar el detalle de la custodia");
if ($resultado_custodia){
  $num_activos=pg_num_rows($resultado_custodia);	
}

/*MUESTRA LOS ACTIVOS*/
if ($num_activos>0){
$contenido .="<tr><td><table border=1 width='50%' align='center'>".
  "<tr class='normalNegro' align='center'>
	<td><b>ITEM</b></td>
	<td><b>SERIAL BIEN NACIONAL</b></td>
	<td><b>SERIAL ARTICULO</b></td></tr>";

while($rowcustodia=pg_fetch_array($resultado_custodia)) 
{ 
$contenido .="<tr class='textoTabla' align='center'>
				<td class='normalNegro'>".$rowcustodia['nombre']."</td>
			    <td class='normalNegro'>".$rowcustodia['etiqueta']."</td>
				<td class='normalNegro'>".$rowcustodia['serial']."</td></tr>";
 }
 $contenido .=" </table></td></tr><br/>";
}
?>
<form action="anular_custodia_Accion.php" name="form" id="form" method="post">
<input type="hidden" value="<?=$acta;?>" name="codigo">
<input type="hidden" value="0" name="hid_validar" />
<input type="hidden" value="<?=$acta_almacen;?>" name="acta_almacen">
<input type="hidden" name="opcion"></input>
<input type="hidden" name="contenido_memo" id="contenido_memo">
<table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
  <tr>
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita">ANULAR CUSTODIA DE ALMAC&Eacute;N   </span></td>
  </tr>
  <tr>
	<td class="normal"><strong>ACTA No: </strong><?php echo $acta;?></td>
  </tr>
  <tr>
	<td class="normal"><strong>FECHA ACTA: </strong><?php echo $fecha;?></td>
  </tr>
  <tr>
	<td class="normalNegro">Los siguientes art&iacute;culos fueron enviados a la Torre para su custodia:</td>
	
  </tr><?php echo $contenido;?>
  <tr>
	<td colspan="2" align="center">
	<br><input type="button" value="Anular" class="normalNegro" onclick="enviar(15)"></td>
	</tr>
		</table>
	  </td>
	</tr>
	
	<input type="hidden" name="txt_arreglo_activos" id="txt_arreglo_activos" />
	<input type="hidden" name="txt_arreglo_articulos" id="txt_arreglo_articulos" />
	<input type="hidden" name="txt_arreglo_mobiliario" id="txt_arreglo_mobiliario" />
	</form>
</body>
</html>