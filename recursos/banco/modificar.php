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
<title>.:SAFI:Modificar Entidad Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="javascript">
//Buscar nombre para evaluar que solo exista uno en la base de datos
function buscar_denominacion() { 
   var nombre;
   var nombre1; 
   nombre=document.form.txt_nombre_enti.value;
   <?php
   $sql_p="SELECT banc_nombre FROM sai_banco"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p);
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
      nombre1="<?php echo trim($row['banc_nombre']); ?>"
	  if (nombre.toUpperCase()==nombre1.toUpperCase()) {
	 	  alert("La entidad bancaria ya se encuentra registrada");
		  document.form.txt_nombre_enti.value='';
		   document.form.txt_nombre_enti.focus();
		  return;
	  }
    <?php }?>
}

//Funcion que permite revisar si los datos estan correctos
function revisar() {
  if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?.")) {
	 document.form.submit();
  }	
}
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
$id=trim($_GET['codigo']); ?>
<form name="form" action="modificarAccion.php" method="post">
<input type="hidden" name="codigo" value="<?=$id?>" />
<?php
$sql="SELECT * FROM sai_banco where banc_id='".$id."'"; 
$resultado=pg_query($conexion,$sql);  
if($row=pg_fetch_array($resultado)) {
	$banc_id=trim($row['banc_id']);
	$banc_nombre=$row['banc_nombre'];	
	$banc_www=$row['banc_www'];
	$esta_id=trim($row['esta_id']);
 ?>
<br />
<br />
<table width="600" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
		  <td height="15" colspan="2" class="normalNegroNegrita">Modificar entidad bancaria</td>
		</tr>
		<tr> 
		<td ><div class="normalNegrita">C&oacute;digo:</div></td>
		<td class="normal">
		<input name="txt_codigo_enti" type="text" disabled class="normal" value="<?=$banc_id?>" size="4" maxlength="4"/>
		<input type="hidden" name="txt_codigo_enti"/>		</td>
		</tr>
		<tr> 
		<td><div class="normalNegrita">Nombre:</div></td>
		<td class="peq_naranja"><input type="text" name="txt_nombre_enti" id="txt_nombre_enti" class="normal" maxlength="200" size="40" value="<?=$banc_nombre?>" readonly=""/></td>
		</tr>
		<tr> 
		<td><div class="normalNegrita">P&aacute;gina Web :</div></td>
		<td class="peq_naranja">
		<input type="text" name="txt_pagina_enti" id="txt_pagina_enti" class="normal" maxlength="50" size="40" value="<?=$banc_www?>" />		</td>
		</tr>
		<tr> 
		<td><div class="normalNegrita">Estado actual:</div></td>
		<td class="normal"><span class="normalNegrita">
		  <?php if($esta_id==1){?>
          <input name="opt_estado" type="radio" value="1" checked="checked" />
          Activo
          <input name="opt_estado" type="radio" value="2"/>
          No activo
          <?php }else{?>
          <input name="opt_estado" type="radio" value="1"/>
          Activo
          <input name="opt_estado" type="radio" value="2" checked="checked"/>
          No activo
          <?php }?>
		</span></td>
		</tr>
		<tr>
		<td height="16" colspan="2">
		<br/><div align="center">
		 	<input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>
		</div>
		</td>
		</tr>
  </table>
<?php } pg_close($conexion);?>
</form>
</body>
</html>