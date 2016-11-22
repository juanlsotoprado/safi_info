<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
$msg=$_GET["msg"];
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"/>
<title>.:SAI:INGRESAR SERVICIO.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script>
/*function relacion(){
	document.form1.cod_partida.value=document.form1.partida.value;
}*/
function enviar(){
	/*if(document.form1.partida.value==0){
		alert("Debe seleccionar la partida presupuestaria.");
		document.form1.partida.focus();
		return;
	}*/
	if(document.form1.nombre.value==""){
		alert("Debe especificar el nombre del servicio");
		document.form1.nombre.select();
		document.form1.nombre.focus();
		return;
	}
	if(confirm('Datos introducidos de manera correcta. '+pACUTE+'Desea Continuar?.')){
	  document.form1.submit();
	}
}
</script>
</head>
<body class="normal">
	<?php
	if($msg=="0"){
		echo "<br/><p class='normal' style='color: red;text-align: center;'>El nombre del servicio que usted indic&oacute; ya est&aacute; registrado en el sistema. Por favor indique otro.</p>";
	}
	?>
	<br/>
	<form name="form1" method="post" action="ingresarServicioAccion.php">
		<table width="900px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="3" class="normalNegroNegrita">
					Nuevo servicio
				</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<!-- ***********MOSTRAR SOLO LAS PARTIDAS A LAS CUALES SE PUEDE ASOCIAR UN SERVICIO********** -->
			<?php
			/*
			<tr>
				<td width="20px" rowspan="3">&nbsp;</td>
				<td height="30" width="150px">Partida:</td>
				<td height="30" width="730px">
					<select name="partida" id="partida" onchange="relacion()" class="normalNegro">
						<option value="0">[Seleccione]</option>
						<?php
						$part="4.03.00.00.00";
						
						$sql_part=	"SELECT part_id, part_nombre ".
									"FROM sai_partida ".
									"WHERE ".
										"pres_anno='".$_SESSION['an_o_presupuesto']."' ".
						              	"AND part_id LIKE '".substr(trim($part),0, 4)."%' ".
						              	"AND substring(trim(part_id)from 9 for 5)<>'00.00' ".
						              	"AND substring(trim(part_id)from 6 for 8)<>'00.00.00' ".
									"ORDER BY part_id";
						$resultado_part=pg_query($conexion,$sql_part);
						while($row_part=pg_fetch_array($resultado_part)){ 
							$part_id=$row_part['part_id'];
							$part_nombre=$row_part['part_nombre'];
						?>
						<option value="<?=$part_id?>"><?=$part_id?>:<?= $part_nombre;?></option>
						<?php
						}
						?>
					</select>
					<span class="peq_naranja">(*)</span>
				</td>
			</tr>
			<tr>
				<td height="30">C&oacute;digo de la P&aacute;rtida:</td>
				<td height="30"><input name="cod_partida" type="text" class="normalNegro" size="50" readonly="readonly"/><span class="peq_naranja">(*)</span></td>
			</tr>
			*/
			?>
			<tr>
				<td height="30">Nombre del Servicio:</td>
				<td height="30">
					<input name="nombre" type="text" class="normalNegro" size="50" maxlength="100" onkeyup="validarTexto(this);"/><span class="peq_naranja">(*)</span>
	  			</td>
			</tr>
     		<tr>
      			<td height="15" colspan="3">
	      			<br/>
					<div align="center">
						<input type="button" id="guardar" name="guardar" onclick="enviar();" value="Guardar" class="normalNegro"/>
					</div>
					<br/>
				</td>
			</tr>
		</table>
		<br/>
		(*) Campo obligatorio
	</form>
</body>
</html>
<?php
pg_close($conexion);
?>