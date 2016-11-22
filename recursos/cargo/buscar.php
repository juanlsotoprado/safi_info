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
	else  document.form1.submit();
}
function eliminar(depe_id,dependencia, cargo_id, cargo) {
	if(confirm("Est\u00e1 seguro que desea eliminar el registro de cargo: "+cargo+" y dependencia: "+dependencia+"?")) {
		document.form1.cargo_elim.value=cargo_id;
		document.form1.depe_elim.value=depe_id;
		document.form1.cargon_elim.value=cargo;
		document.form1.depen_elim.value=dependencia;
		document.form1.submit();
	}		
}
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form name="form1" action="buscar.php" method="post">
  <br />
  <br />
  <table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
 <td colspan="2" class="normalNegroNegrita">BUSCAR DEPENDENCIA Y CARGO </td>
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
  <input type="hidden" name="cargo_elim" value=""></input>
    <input type="hidden" name="depe_elim" value=""></input>
  <input type="hidden" name="cargon_elim" value=""></input>
    <input type="hidden" name="depen_elim" value=""></input>
    
    <div align="center"> <input class="normalNegro" type="button" value="Buscar" onclick="javascript:revisar();"/></div></td>
</tr>
</table>
<?	
$valido=1;
if (!isset ($_POST["cargo_elim"]) || strlen($_POST["cargo_elim"])<3) {
if (isset($_POST["cargo"])) {
	$cargo = $_POST["cargo"];
	list($cargo_id, $cargo_nombre) = split('-', $cargo);	
	$condicion = " and dc.carg_id='".$cargo_id."'";
}	
if (isset($_POST["dependencia"]) &&  $_POST["dependencia"]!=0) {
	$dependencia = $_POST["dependencia"];
	list($depe_id, $depe_nombre) = split(':', $dependencia);
	$condicion = " and dc.depe_id='".$depe_id."'";
}
	
	$sql= "select dc.carg_id, dc.depe_id, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia from sai_depen_cargo dc, sai_cargo c, sai_dependenci d where dc.carg_id=c.carg_id and dc.depe_id = d.depe_id".$condicion." order by dc.depe_id";
	$resultado=pg_query($conexion,$sql) or die("Error al consultar los cargos asociados a las dependencias"); 
?>
<br /><br />
	<table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	  <td colspan="3" class="normalNegroNegrita"> DEPENDENCIA - CARGOS </span></td>
	</tr>
	<tr>
	<td class="normalNegrita">Dependencia</td>
	<td class="normalNegrita">Cargo</td>
	<td class="normalNegrita">Opci&oacute;n</td>	
	</tr>
<?php while ($row=pg_fetch_array($resultado)) {?>
	<tr>
	<td class="normal"><?php echo $row["dependencia"];?></td>
	<td class="normal"><?php echo $row["cargo"];?></td>
	<td class="normal"><a href="javascript:eliminar('<?=$row['depe_id']?>','<?=$row['dependencia']?>','<?=$row['carg_id']?>','<?=$row['cargo']?>');">Eliminar</a></td>	
	</tr>	
<?php }?>
</table>
<?php 
}
else {
	$sql="delete from sai_depen_cargo where depe_id='".$_POST["depe_elim"]."' and carg_id='".$_POST["cargo_elim"]."'";
	$resultado=pg_query($conexion,$sql);
	echo "<div class='normal' align='center'><br>Se elimin&oacute; satisfactoriamente el registro con la asociaci&oacute;n de cargo: ".$_POST["cargon_elim"]." y dependencia: ".$_POST["depen_elim"];
}
?>
<? pg_close($conexion);?>
</form>
</body>
</html>