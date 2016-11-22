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
require("../../includes/perfiles/constantesPerfiles.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar entidad bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script>
function ejecutar(codigo1,codigo2,codigo3) {
	valor="0"
	for(i=0;i<document.form1.opcion.length;i++)
		if(document.form1.opcion[i].checked) valor=document.form1.opcion[i].value;
	document.form1.validar.value=valor;	
	document.form1.submit();
}
</script>
<script>
function deshabilitar_combo(valor) {
	if(valor=='1') { 
		document.form1.slc_id.disabled=false;
		document.form1.slc_nombre.disabled=true;
		document.form1.edo.disabled=true;
		document.form1.edo.value=0;
		document.form1.slc_nombre.value=0;
		document.form1.slc_id.value=0;
	}
	else if(valor=='2') { 
		document.form1.slc_id.disabled=true;
  		document.form1.slc_nombre.disabled=false;
  		document.form1.edo.disabled=true;
  		document.form1.edo.value=0;
  		document.form1.slc_nombre.value=0;
  		document.form1.slc_id.value=0;
	}
  	if(valor=='3') { 
		document.form1.slc_id.disabled=true;
		document.form1.slc_nombre.disabled=true;
		document.form1.edo.disabled=false;
		document.form1.edo.value=0;
		document.form1.slc_nombre.value=0;
		document.form1.slc_id.value=0;
	}
}
function detalle(codigo) {
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form name="form1" action="buscar.php" method="post">
  <br />
<table background="../../imagenes/fondo_tabla.gif" class="tablaalertas" align="center">
	<tr class="td_gray">
		<td colspan="3" class="normalNegroNegrita">Buscar entidad bancaria</td>
	</tr>
<tr>
  <td class="titularMedio"><input name="opcion" id="opcion" type="radio" value="1" onclick="javascript:deshabilitar_combo(1)" /></td>
  <td class="normalNegrita">C&oacute;digo:</td>
  <td class="titularMedio">
  <select name="slc_id" id="slc_id" class="normal" disabled="true">
  <option value="0">[Seleccione]</option>
  <?php
      $sql = "SELECT banc_id,
      			UPPER(banc_nombre) AS  banc_nombre
      		FROM sai_banco
      		WHERE esta_id = 1
      		ORDER BY banc_nombre"; 
      $resultado=pg_query($conexion,$sql) or die("Error al consultar los bancos");  
      while($row=pg_fetch_array($resultado)) {?>
      <option value="<?=$row['banc_id']?>"><?=$row['banc_id']?></option> 
<?php } ?>
  </select>
  </td>
  </tr>
  <tr>
  <td class="titularMedio"><input name="opcion" id="opcion" type="radio" value="2" onclick="javascript:deshabilitar_combo(2)" /></td>
  <td class="normalNegrita">Nombre:</td>
  <td class="normal">
  <select name="slc_nombre" id="slc_nombre" class="normal" disabled="true" >
  <option value="0">[Seleccione]</option>
  <?php
  		$sql = "SELECT banc_id,
      			UPPER(banc_nombre) AS  banc_nombre
      		FROM sai_banco
      		WHERE esta_id = 1
      		ORDER BY banc_nombre"; 
      $resultado=pg_query($conexion,$sql) or die("Error al consultar los bancos");  
      while($row = pg_fetch_array($resultado)) {?>
      <option value="<?=$row['banc_id']?>"><?=$row['banc_nombre'];?></option> 
<?php } ?>
  </select>
  </td>
  </tr>
  <tr>
    <td><input name="opcion" id="opcion" type="radio" value="3" onclick="javascript:deshabilitar_combo(3)" /></td>
    <td class="normalNegrita">Estado:</td>
    <td class="normal">
      <select name="edo" class="normal" id="edo" disabled="disabled">
        <option value="0">Seleccione</option>
        <option value="1">Activo</option>
        <option value="2">Inactivo</option>
      </select>
    </td>
  </tr>
  <tr>
  <td colspan="3" align="center">
  <input type="hidden" name="validar" id="validar" value="0"></input> 
  <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar(document.form1.slc_id.value,document.form1.slc_nombre.value,document.form1.edo.value);"/>
  </td>
  </tr>
</table>
</form>
<br/>
<?php
$condicion="";
if ($_POST['validar']<>0) {
	if (isset($_POST['slc_id']) && trim($_POST['slc_id'])!='0') $condicion=" and b.banc_id='".$_POST['slc_id']."'";
	else if (isset($_POST['slc_nombre']) && trim($_POST['slc_nombre'])!='0') $condicion=" and b.banc_id ='".$_POST['slc_nombre']."'";
	else $condicion=" and b.esta_id='".$_POST['edo']."'";
}		   

	$sql="SELECT b.banc_id AS id,
			UPPER(b.banc_nombre) AS nombre,
			b.banc_www AS www,
			e.esta_nombre AS estado
			FROM sai_banco b,
				sai_estado e
			WHERE b.esta_id = e.esta_id ".$condicion; 
	$resultado=pg_query($conexion,$sql) or die("Error al consultar las entidades bancarias");
?>
		  <table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
			  <tr class="td_gray">
			  <td class="normalNegroNegrita"><div align="center">C&oacute;digo</div></td>
			  <td class="normalNegroNegrita"><div align="center">Nombre </div></td>
			  <td class="normalNegroNegrita"><div align="center">P&aacute;gina Web </div></td>
			  <td class="normalNegroNegrita"><div align="center">Estado</div></td>
			  <td class="normalNegroNegrita"><div align="center">Opciones</div></td>
			  </tr>
		 <?php 	while($row=pg_fetch_array($resultado)) {  ?>
	
			  <tr class="normal">
			    <td><?=$row['id']?></td> 
			    <td><?=$row['nombre']?></td>
			    <td><?=$row['www']?></td>
 				<td><?=$row['estado']?></td>
 				<td>			    
				<a href="javascript:detalle('<?=$row['id']?>')" class="normal" > Ver Detalle</a><br/>
				<?php if ($_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>				
					<a href="modificar.php?codigo=<?=$row['id']?>" class="normal"> Modificar</a>
				<?php }?>	
				</td>
			  </tr>
 			
<?php  } 
pg_close($conexion);
?>
 </table>
</body>
</html>