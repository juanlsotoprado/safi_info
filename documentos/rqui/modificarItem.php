<?php
$indice=$_REQUEST['indice'];
$id=$_REQUEST['id'];
$nombre=$_REQUEST['nombre'];
$partida=$_REQUEST['partida'];
$denominacion=$_REQUEST['denominacion'];
$especificaciones=$_REQUEST['especificaciones'];
$cantidad=$_REQUEST['cantidad'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Modificar Rubro</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"></script>
<script>
function cancelar(){
	window.close();
}
function modificar(){
	if(confirm('Datos introducidos de manera correcta. '+pACUTE+'Desea Continuar?.')){
		window.opener.partidas[<?= ($indice-1)?>][4]=document.getElementById("articuloEspecificaciones").value;
		window.opener.partidas[<?= ($indice-1)?>][5]=document.getElementById("cantidad").value;
		window.opener.document.getElementById("txt_prod<?= ($indice-1)?>").value=document.getElementById("articuloEspecificaciones").value;
		if(window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").firstChild){
			window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").removeChild(window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").firstChild);
		}
		window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").appendChild(document.createTextNode(document.getElementById("articuloEspecificaciones").value));
		window.opener.document.getElementById("txt_cantidad<?= ($indice-1)?>").value=document.getElementById("cantidad").value;
		if(window.opener.document.getElementById("divCantidad<?= ($indice-1)?>").firstChild){
			window.opener.document.getElementById("divCantidad<?= ($indice-1)?>").removeChild(window.opener.document.getElementById("divCantidad<?= ($indice-1)?>").firstChild);
		}
		window.opener.document.getElementById("divCantidad<?= ($indice-1)?>").appendChild(document.createTextNode(document.getElementById("cantidad").value));
		window.close();
	}
}
function cargarDatos(){
	document.getElementById("articuloEspecificaciones").value=window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").innerHTML;
	document.getElementById("articuloEspecificacionesLen").value=10000-window.opener.document.getElementById("divEspecificaciones<?= ($indice-1)?>").innerHTML.length;
}
</script>
</head>
<body class="normal" onload="cargarDatos();">
	<form name="form" method="post" id="form">
		<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td colspan="2" class="normalNegroNegrita">
					MODIFICAR RUBRO
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table class="tablaalertas" width="500px">
						<tr>
							<td width="20%">
								C&oacute;digo
							</td>
							<td width="80%" class="normalNegro">
								<?= $id?>
							</td>
						</tr>
						<tr>
							<td width="20%">
								Nombre
							</td>
							<td width="80%" class="normalNegro">
								<?= $nombre?>
							</td>
						</tr>
						<tr>
							<td width="20%">
								Partida
							</td>
							<td width="80%" class="normalNegro">
								<?= $partida?>
							</td>
						</tr>
						<tr>
							<td width="20%">
								Denominaci&oacute;n
							</td>
							<td width="80%" class="normalNegro">
								<?= $denominacion?>
							</td>
						</tr>
						<tr>
							<td width="20%">
								Cantidad<span class="peq_naranja">(*)</span>
							</td>
							<td width="80%" class="normalNegro">
								<input value="<?= $cantidad?>" maxlength="10" type="text" id="cantidad" name="cantidad" onkeyup="validarInteger(this);" size="10" class="normalNegro"/>
							</td>
						</tr>
						<tr>
							<td width="20%">
								Especificaciones<span class="peq_naranja">(*)</span>
							</td>
							<td width="80%" class="normalNegro">
								<textarea class="normalNegro" id="articuloEspecificaciones" name="articuloEspecificaciones" cols="52" rows="8"
									onkeydown="textCounter(this,'articuloEspecificacionesLen',10000);"
									onkeyup="textCounter(this,'articuloEspecificacionesLen',10000);validarTexto(this);"></textarea><br/>
								<div style="text-align: right;"><input type="text" value="" class="normalNegro" maxlength="5" size="5" id="articuloEspecificacionesLen" name="articuloEspecificacionesLen" readonly="readonly"/></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>
		<div id="divAcciones" style="text-align: center;">
			<input class="normalNegro" type="button" value="Modificar" onclick="modificar();"/>
		</div>
		<br/>
		<div class="ptotal"><span class="peq_naranja">(*)</span> Campo obligatorio</div>
	</form>
</body>
</html>