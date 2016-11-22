<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SAFI: Buscar cuenta contable</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" type="text/JavaScript">
function validar(){
	if(trim(document.getElementById("cuentaActb").value)!=""){
		tokens = document.getElementById("cuentaActb").value.split( ":" );
		cuenta = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("cuenta").value = cuenta;
	}
	document.form1.submit();
}
function irPdf() {
	document.form1.action="buscarCuentaPDF.php";
	document.form1.submit();
}
</script>	
</head>
<body>
<br />
<br />
<form name="form1" method="post" action="buscarCuenta.php">
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td class="normalNegroNegrita">B&Uacute;SQUEDA CUENTA PATRIMONIAL</td>
    </tr>
	<tr>
   		<td align="center"><span class="normal">
			<input type="hidden" value="" name="cuenta" id="cuenta"/>
			<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="" class="normal"/>
			<?php
			$query = "SELECT scp.cpat_id, scp.cpat_nombre FROM sai_cue_pat scp ORDER BY scp.cpat_id";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)) {
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
<td align="center"><input type="button" value="Buscar" onclick="javascript:validar();"/></td>
 </tr>
  </table>	
</form>
<?	
if (isset($_POST["cuenta"])) $condicion = "where cpat_id='".$_POST["cuenta"]."'";

$sql="select cpat_id, upper(cpat_nombre) as cpat_nombre, cpat_nivel, upper(cpat_grupo) as cpat_grupo, upper(cpat_sub_grupo) as cpat_sub_grupo, upper(cpat_rubro) as cpat_rubro, case when esta_id='1' then 'Activo' else 'Inactivo' end as esta_id,
CASE cpat_nivel 
           WHEN 4 THEN '*'
           END  as cuenta,
           CASE cpat_nivel 
           WHEN 5 THEN '*'
           END  as subcuenta1,
           CASE cpat_nivel 
           WHEN 6 THEN '*'
           END  as subcuenta2,
           CASE cpat_nivel 
           WHEN 7 THEN '*'
           END  as subcuenta3,
           CASE cpat_nivel 
           WHEN 7 THEN 'Si'
           ELSE 'No'
           END  as movimiento
from sai_cue_pat ".$condicion."
 order by cpat_id";
$resultado_set=pg_query($conexion,$sql);	
?>
<br /><br />
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaCentral">
    <tr align="center" class="td_gray">
      <td class="normalNegroNegrita">C&oacute;digo</td>
      <td class="normalNegroNegrita">Nombre</td>
    <td class="normalNegroNegrita">Nivel</td>      
      <td class="normalNegroNegrita">Grupo</td>
      <td class="normalNegroNegrita">Sub Grupo</td>
      <td class="normalNegroNegrita">Rubro</td>
	<td class="normalNegroNegrita">Cuenta  </td>
	<td class="normalNegroNegrita">Subcuenta 1  </td>
	<td class="normalNegroNegrita">Subcuenta 2  </td>
	<td class="normalNegroNegrita">Subcuenta 3  </td>
	<td class="normalNegroNegrita">Movimiento  </td>            

   
  </tr>
  <?php while ($row=pg_fetch_array($resultado_set))  {?>
  
<tr class="normal">
      <td><?echo $row['cpat_id'];?></td>
      <td><?echo $row['cpat_nombre'];?></td>
      <td><?echo $row['cpat_nivel'];?></td>      
      <td><?echo $row['cpat_grupo'];?></td>
      <td><?echo $row['cpat_sub_grupo'];?></td>
      <td><?echo $row['cpat_rubro'];?></td>
	  <td><?echo $row['cuenta'];?></span></td>      
	  <td><?echo $row['subcuenta1'];?></span></td>
	  <td><?echo $row['subcuenta2'];?></span></td>
	  <td><?echo $row['subcuenta3'];?></span></td>
	 <td><?echo $row['movimiento'];?></span></td>      
</tr> 
<?php } pg_close($conexion);?> 
  </table>
<br>
<div align="center">
<input type="button" onClick="javascript:irPdf();" value="PDF"/>
</div>
</body>
</html>