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
<title>.:SAFI:Cargo - Dependencia</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script>
//Buscar nombre para evaluar que solo exista uno en la base de datos
function revisar() {
	if (document.form1.dependencia.value=='') {
     alert("Debe seleccionar la dependencia");
	 return;
  }
	else if(trim(document.getElementById("cargoActb").value)!="") {
		tokens = document.getElementById("cargoActb").value.split( ":" );
		cargo = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("cargo").value = cargo;
		document.form1.submit();
	}
  else if (confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?.")) 
	 document.form1.submit();
}
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form name="form1" action="ingresar.php" method="post">
  <br />
  <br />
  <table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
 <td colspan="2" class="normalNegroNegrita">ASOCIAR CARGO A DEPENDENCIA </td>
</tr>
<tr>
<td class="normalNegrita">Dependencia:</td>
  <td class="peq">
  <select name="dependencia" id="dependencia" class="normal">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sql="SELECT * FROM sai_dependenci order by depe_nombre"; 
		$resultado=pg_query($conexion,$sql) or die("Error al consultar las dependencias");  
		while($row=pg_fetch_array($resultado)) { ?>
   	     <option value="<?=$row['depe_id'].":".$row['depe_nombre']?>"><?=$row['depe_nombre']?></option> 
  		<?php } ?>
  </select>
  </td>
</tr>
<tr>
<td class="normalNegrita">Cargo:</td>
    	<td class="peq"><input type="hidden" value="" name="cargo" id="cargo"/>
			<input autocomplete="off" size="70" type="text" id="cargoActb" name="cargoActb" value="" class="normal"/>
			<?php
			$query = "SELECT carg_id, carg_nombre from sai_cargo";
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arreglo = "";
			while($row=pg_fetch_array($resultado)) {
				$arreglo .= "'".$row["carg_id"]."-".$row["carg_nombre"]." : ".str_replace("\n"," ",$row["carg_nombre"])."',";
			}
			$arreglo = substr($arreglo, 0, -1);
			?>
			<script>
			var cargosAMostrar = new Array(<?= $arreglo?>);
			actb(document.getElementById('cargoActb'),cargosAMostrar);
			</script>
		</td>    
</tr>
<tr>
  <td height="16" colspan="2"><br />
    <div align="center">   	<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/></div></td>
</tr>
</table>
</form>
<?	
$valido=1;
if (isset($_POST["cargo"])) {
	$cargo = $_POST["cargo"];
	$dependencia = $_POST["dependencia"];
	list($cargo_id, $cargo_nombre) = split('-', $cargo);
	list($depe_id, $depe_nombre) = split(':', $dependencia);
	
	$sql= "select carg_id from sai_depen_cargo where carg_id='".$cargo_id."' and depe_id='".$depe_id."'";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar los cargos asociados a las dependencias");
	$numero_resultado=pg_num_rows($resultado);
	if ($numero_resultado>0) $valido=0;		 
	if ($valido=='0')	{
?>
		<script language="javascript">
			alert("El cargo y dependencia que introdujo ya se encuentran registrados en el sistema");
		</script>	
<? }
	else { 
		$sql= "select carg_id from sai_cargo where carg_id='".$cargo_id."'";
		$resultado=pg_query($conexion,$sql) or die("Error al consultar el cargo");
		$numero_resultado=pg_num_rows($resultado);
		if ($numero_resultado>0) {
			$sql = "INSERT INTO sai_depen_cargo (carg_id, depe_id, esta_id, usua_login) values ('".$cargo_id."','".$depe_id."',1,'".$_SESSION['login']."')";
			$resultado_set = pg_exec($conexion ,$sql) or die("Error al asociar cargo con dependencia"); 
			$valido= $resultado_set;
			echo "<br/></br><div class='normal' align='center'><strong>Se ingres&oacute; satisfactoriamente la asociaci&oacute;n entre el cargo: ".$cargo_nombre." y la dependencia: ".$depe_nombre." </strong>";
		}	
		else echo "<br/></br><div class='normal' align='center'><strong>Ese cargo no se encuentra registrado en el safi";	
	}
}	
pg_close($conexion);
?>
</body>
</html>