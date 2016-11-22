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
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SAFI: Convertidor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" type="text/JavaScript">
function validar() {  
	if (document.form1.partida.value=='0') {
		alert ("Debe seleccionar la partida presupuestaria");
		return;
	}
	else if(trim(document.getElementById("partidaActb").value)!="") {
		tokens = document.getElementById("partidaActb").value.split( ":" );
		partida = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("partida").value = partida;
		document.form1.submit();
	}
}
function irPdf() {
	document.form1.action="buscarConvertidorPDF.php";
	document.form1.submit();
}
</script>	
</head>
<body>
<br />
<br />
<form name="form1" method="post" action="buscarConvertidor.php">
<table width="70%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td class="normalNegroNegrita" colspan="2">B&Uacute;SQUEDA CONVERTIDOR </td>
    </tr>
    <tr>
   		 <td class="normalNegrita">Partida presupuestaria: </td>
     	<td>	<input type="hidden" value="" name="partida" id="partida"/>
			<input autocomplete="off" size="70" type="text" id="partidaActb" name="partidaActb" value="" class="normal"/>
			<?php
			$query = "SELECT distinct(part_id), max(pres_anno) as pres_anno, part_nombre  from sai_partida where part_id in (select part_id from sai_convertidor) and part_id not like '%00.00' group by part_id,part_nombre order by part_id";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)) {
				$arreglo .= "'".$row["part_id"]."-".$row["pres_anno"]." : ".str_replace("\n"," ",$row["part_nombre"])."',";
			}
			$arreglo = substr($arreglo, 0, -1);
			?>
			<script>
			var cuentasAMostrar = new Array(<?= $arreglo?>);
			actb(document.getElementById('partidaActb'),cuentasAMostrar);
			</script>
		</td>    
  </tr>
<tr> 
<td align="center" colspan="2">
<input type="button" value="Buscar" onclick="javascript:validar();"/>
</td>
    </tr>
  </table>	
</form>
<?	
if (isset($_POST["partida"])){ 
	list($partida, $ano) = split('-', $_POST["partida"]);
	$condicion = " and c.part_id='".$partida."'";
}
	$presupuesto = $_POST["partida"];
	$contable="";
	$pasivo="";
	$transitoria="";
	$ano = "";
	$estado = "";
	$sql = "select c.part_id, c.cpat_id, c.cpat_pasivo_id, upper(cp.cpat_nombre) as nombre, c.pres_anno, case when cp.esta_id='1' then 'Activo' else 'Inactivo' end as esta_id
	from sai_convertidor c, sai_cue_pat cp
	where c.cpat_id=cp.cpat_id ".$condicion." 
	order by c.part_id";
	$resultado_set=pg_query($conexion,$sql);
?>
<br/> <br />
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td align="center" class="normalNegrita"><div align="center">Partida </div></td>
      <td align="center" class="normalNegrita"><div align="center">Cuenta contable </div></td>
      <td align="center" class="normalNegrita"><div align="center">Cuenta pasivo </div></td>
      <td align="center" class="normalNegrita"><div align="center">Ano Presupuestario </div></td>
       <td align="center" class="normalNegrita"><div align="center">Estado </div></td>

  </tr>
  <?php while ($row=pg_fetch_array($resultado_set))  {?>
<tr class="normal">
    <td><?echo $row['part_id'];;?></td>
      <td><?echo $row['cpat_id'].". ".$row['nombre'];?></td>
      <td><?echo $row['cpat_pasivo_id'];?></td>
      <td><?echo $row['pres_anno'];?></td>
      <td><?echo $row['esta_id'];?></td>
</tr>
<?php } pg_close($conexion);?>
</table>
<br>
<div align="center">
<input type="button" onClick="javascript:irPdf();" value="PDF"/>
</div>
</body>
</html>