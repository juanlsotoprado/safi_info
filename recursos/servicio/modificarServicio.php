<?php 
ob_start();
session_start();
require("../../includes/conexion.php");
require("../../includes/constantes.php");
require("../../includes/perfiles/constantesPerfiles.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
$user_perfil_id = $_SESSION['user_perfil_id'];
$msg=$_GET["msg"];
$id=$_GET["id"];
$opcion = ($_GET["opcion"] && $_GET["opcion"]!="")? $_GET["opcion"]:"2";
$codigo = ($_GET["codigo"] && $_GET["codigo"]!="")? $_GET["codigo"]:"";
$estado = ($_GET["estado"] && $_GET["estado"]!="" && $_GET["estado"]!="0")? $_GET["estado"]:"";
$partida = ($_GET["partida"] && $_GET["partida"]!="" && $_GET["partida"]!="0")? $_GET["partida"]:"";
//$ano = ($_GET["ano"] && $_GET["ano"]!="")? $_GET["ano"]:"";
$palabraClave = ($_GET["palabraClave"] && $_GET["palabraClave"]!="")? trim($_GET["palabraClave"]):"";
ob_end_flush();
if($id!=""){
	$sql=	"SELECT ".
				"si.nombre, ".
				"si.esta_id ".
			"FROM sai_item si ".
			"WHERE ".
				"si.id = '".$id."' ";
	$resultado=pg_query($conexion,$sql);
	if($row=pg_fetch_array($resultado)){
		$servi_nombre = $row["nombre"];
		$esta_id = $row["esta_id"];
		
		$sql=	"SELECT ".
					"sp.part_id, ".
					"sp.part_nombre ".
				"FROM sai_item_partida sip, sai_partida sp ".
				"WHERE ".
					"sip.id_item = '".$id."' AND ".
					"sip.part_id = sp.part_id AND ".
					"sp.pres_anno = ".$_SESSION['an_o_presupuesto'];
		$resultado=pg_query($conexion,$sql);
		if($row=pg_fetch_array($resultado)){
			$part_id = $row["part_id"];
			$part_nombre = $row["part_nombre"];
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"/>
<title>.:SAI:MODIFICAR SERVICIO.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script>
function relacion(){
	document.form1.cod_partida.value=document.form1.partida.value;
}
function enviar(){
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
function regresar(){
<?php
	if($opcion=="1"){
		echo "location.href='buscar.php?opcion=".$opcion."&codigo=".$codigo."';";
	}else{
		echo "location.href='buscar.php?opcion=".$opcion."&estado=".$estado."&partida=".$partida."&palabraClave=".$palabraClave."';";
	}
?>
}
</script>
</head>
<body class="normal">
	<?php
	if($msg=="0"){
		echo "<br/><p style='color: red;text-align: center;'>El nombre del servicio que usted indic&oacute; ya est&aacute; registrado en el sistema para otro servicio. Por favor indique otro nombre.</p>";
	}
	?>
	<br/>
	<form name="form1" method="post" action="modificarServicioAccion.php">
		<input type="hidden" id="id" name="id" value="<?= $id?>"/>
		<input type="hidden" id="opcion" name="opcion" value="<?= $opcion?>"/>
		<input type="hidden" id="codigo" name="codigo" value="<?= $codigo?>"/>
		<input type="hidden" id="estado" name="estado" value="<?= $estado?>"/>
		<input type="hidden" id="palabraClave" name="palabraClave" value="<?= $palabraClave?>"/>
		<table width="900px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="21" colspan="3" class="normalNegroNegrita">
					Modificar servicio 
				</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<?php 
			if ( $user_perfil_id != PERFIL_ANALISTA_PRESUPUESTO && $user_perfil_id != PERFIL_JEFE_PRESUPUESTO ) {
			?>				
				<tr>
					<td width="20px" rowspan="4">&nbsp;</td>
					<td width="150px" height="30">P&aacute;rtida:</td>
					<td width="730px" height="30">
						<input name="cod_partida" type="text" class="normalNegro" size="80" readonly="readonly" value="<?=$part_id?>:<?= $part_nombre?>"/>
						<input type="hidden" id="partida" name="partida" value="<?= $partida?>"/>
					</td>
				</tr>
			<?php 			
			} else {
			?>
				<tr>
					<td width="20px" rowspan="5">&nbsp;</td>
					<td width="150px" height="30">P&aacute;rtida:</td>
					<td width="730px" height="30">
						<select name="partida" id="partida" onchange="relacion()" class="normalNegro">
							<option value="0">[Seleccione]</option>
							<?php
							$part="4.03.00.00.00";
							$part2="4.01.00.00.00";
							$sql_part=	"SELECT part_id, part_nombre ".
										"FROM sai_partida ".
										"WHERE ".
											"pres_anno='".$_SESSION['an_o_presupuesto']."' ".
							              	"AND (part_id LIKE '".substr(trim($part),0, 4)."%' ".
							              	"or part_id LIKE '".substr(trim($part2),0, 4)."%') ".
							              	"AND substring(trim(part_id)from 9 for 5)<>'00.00' ".
							              	"AND substring(trim(part_id)from 6 for 8)<>'00.00.00' ".
										"ORDER BY part_id";
							$resultado_part=pg_query($conexion,$sql_part);
							while($row_part=pg_fetch_array($resultado_part)){ 
								$sql_part_id=$row_part['part_id'];
								$sql_part_nombre=$row_part['part_nombre'];
								if ( $sql_part_id == $part_id ) {
							?>
							<option value="<?=$sql_part_id?>" selected="selected"><?=$sql_part_id?>:<?= $sql_part_nombre;?></option>
							<?php
								} else {
							?>
							<option value="<?=$sql_part_id?>"><?=$sql_part_id?>:<?= $sql_part_nombre;?></option>
							<?php		
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td height="30">C&oacute;digo de la P&aacute;rtida:</td>
					<td height="30"><input id="cod_partida" name="cod_partida" type="text" class="normalNegro" size="50" readonly="readonly" value="<?=$part_id?>"/><span class="peq_naranja">(*)</span></td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td height="30">C&oacute;digo del Servicio:</td>
				<td height="30"><input name="cod_servicio" type="text" class="normalNegro" size="8" readonly="readonly" value="<?=$id?>"/></td>
			</tr>
			<tr>
				<td height="30">Nombre del Servicio:</td>
				<td height="30">
					<input name="nombre" type="text" class="normalNegro" size="50" maxlength="100" onkeyup="validarTexto(this);" value="<?= $servi_nombre?>" <?php if ( $user_perfil_id == PERFIL_ANALISTA_PRESUPUESTO || $user_perfil_id == PERFIL_JEFE_PRESUPUESTO ) { echo 'readonly="readonly"'; } ?>/><span class="peq_naranja">(*)</span>
	  			</td>
			</tr>
			<?php if ( $user_perfil_id != PERFIL_ANALISTA_PRESUPUESTO && $user_perfil_id != PERFIL_JEFE_PRESUPUESTO ) {?>
				<tr>
					<td height="30">Estado:</td>
					<td height="30">
						<select name="estadoServicio" id="estadoServicio" class="normalNegro">
							<option value="1" <?php if($esta_id==ESTADO_ACTIVO){ echo "selected='selected'";}?>>Activo</option>
							<option value="2" <?php if($esta_id==ESTADO_INACTIVO){ echo "selected='selected'";}?>>Inactivo</option>
					    </select><span class="peq_naranja">(*)</span>
		  			</td>
				</tr>
			<?php 
			} else {
			?>
				<tr>
					<td height="30">Estado:</td>
					<td height="30">
						<input type="text" value="<? if($esta_id==ESTADO_ACTIVO){ echo "Activo";}else{ echo "Inactivo";}?>" readonly="readonly" class="normalNegro"/>
					    <input type="hidden" name="estadoServicio" id="estadoServicio" value="<?= $esta_id?>"/>
		  			</td>
				</tr>
			<?php 
			}
			?>
     		<tr>
      		<td height="15" colspan="4">
      			<br/>
				<div align="center">
					<input type="button" onclick="enviar();" value="Guardar" class="normalNegro"/>
					<input type="button" onclick="regresar();" value="Regresar" class="normalNegro"/>
					<br/><br/>
				</div>
			</td>
		</tr>
	</table>
	<br/>
	(*) Campo obligatorio
</form>
</body>
</html>
<?php
	}
}
pg_close($conexion);
?>