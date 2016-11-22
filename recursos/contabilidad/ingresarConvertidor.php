<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SAFI: Ingresar convertidor</title>
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
	else if (document.form1.contable.value=='0') {
		alert ("Debe seleccionar la cuenta contable");
		return;
	}
	else if (document.form1.pasivo.value=='0') {
		alert ("Debe seleccionar la cuenta del pasivo");
		return;
	}
	else if(trim(document.getElementById("partidaActb").value)!="") {
		tokens = document.getElementById("partidaActb").value.split( ":" );
		partida = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("partida").value = partida;
	}
	if (confirm("Est\u00e1 seguro que desea ingresar un nuevo registro en el convertidor?")) 
		document.form1.submit();
}
</script>	
</head>
<body>
<br />
<br />
<form name="form1" method="post" action="ingresarConvertidor.php">
<table width="70%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
    <tr class="td_gray">
      <td class="normalNegroNegrita" colspan="2">INGRESAR CONVERTIDOR</td>
    </tr>
    <tr class="normal">
   		 <td class="normalNegrita">Partida presupuestaria: </td>
     	<td>	<input type="hidden" value="" name="partida" id="partida"/>
			<input autocomplete="off" size="70" type="text" id="partidaActb" name="partidaActb" value="" class="normal"/>
			<?php
			$query = "SELECT distinct(part_id), max(pres_anno) as pres_anno, part_nombre  from sai_partida where part_id not in (select part_id from sai_convertidor) and part_id not like '%00.00' group by part_id,part_nombre order by part_id";
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
    	<td class="normalNegrita">Cuenta contable: </td>
     	<td>
 		<select name="contable" class="normal" id="contable">
     	<option value="0">::: Seleccione :::</option>
      	<?php 
			$sql="SELECT cpat_id, cpat_nombre from sai_cue_pat where  cpat_id not like '%.00' order by cpat_id";
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar cuenta contable"); 
			while($row=pg_fetch_array($resultado)) { ?>
      	<option value="<?php echo $row['cpat_id'];?>"><?php echo $row['cpat_id'].": ".$row['cpat_nombre'];?></option>
      	<?php } ?>
    	</select>
      </td>    
  </tr>
    <tr>
    <td class="normalNegrita">Cuenta pasivo: </td>
    <td>
 		<select name="pasivo" class="normal" id="pasivo">
     	<option value="0">::: Seleccione :::</option>
     	 <?php 
				$sql="SELECT cpat_id, cpat_nombre from sai_cue_pat where cpat_id like '2.%' and cpat_id not like '%.00' order by cpat_id"; 
				$resultado=pg_query($conexion,$sql) or die("Error al mostrar cuentas de pasivos"); 
				while($row=pg_fetch_array($resultado)) { ?>
     	 <option value="<?php echo $row['cpat_id'];?>"><?php echo $row['cpat_id'].": ".$row['cpat_nombre'];?></option>
      	<?php } ?>
		 </select>
     </td>    
   </tr>
 <tr> 
<td align="center" colspan="2">
    	<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:validar();"/>
</td>
    </tr>
  </table>	
</form>
<?	
if (isset($_POST["partida"])) {
	$presupuesto = $_POST["partida"];
	list($partida, $ano) = split('-', $presupuesto);
	$contable = $_POST["contable"];
	$pasivo = $_POST["pasivo"];
	$fec = date("d/m/Y ");
	if (strlen($ano)>2) {
	$sql = "INSERT INTO sai_convertidor (part_id, cpat_id, cpat_pasivo_id, usua_login, pres_anno, fec_emision) values ('".$partida."','".$contable."','".$pasivo."','".$_SESSION['login']."', ".$ano.", to_date('".$fec."', 'DD MM YYYY'))";
	$resultado_set = pg_exec($conexion ,$sql);
	$valido= $resultado_set;
	}
	else $valido=false;
	if ($valido) {
		echo "<br/></br><div class='normal' align='center'><strong>Se ingres&oacute; satisfactoriamente el nuevo registro en el convertidor, que contiene los siguientes elementos:<br> Partida presupuestaria: ".$partida.", Cuenta contable: ".$contable." y Cuenta pasivo: ".$pasivo."</strong></div>";
	}
	else echo "<br/></br><div class='normal' align='center'><strong>Error, no se pudo ingresar en el convertidor una partida presupuestaria que no se encuentra asignada a alg&uacute;n a&ntilde;o presupuestario</strong></div>";
}
pg_close($conexion);
?>
</body>
</html>