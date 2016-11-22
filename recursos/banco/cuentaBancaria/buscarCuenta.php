<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
require("../../../includes/perfiles/constantesPerfiles.php"); 
$an=trim($_SESSION['an_o_presupuesto']);
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Cuenta Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function detalle(codigo) {
    url="detalleCuenta.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function consultar(codigo, banco, tipo, ano) {
  window.location="buscarChequera.php?codigo="+codigo+"&banco="+banco+"&tipo="+tipo+"&ano="+ano;
}

function cerrar(codigo, banco, tipo, ano) {
  window.location="desactivarCuenta.php?codigo="+codigo+"&banco="+banco+"&tipo="+tipo+"&ano="+ano;
}

function activar(codigo) {
  window.location="activarCuenta.php?codigo="+codigo;
}
</script>
<script language="javascript" type="text/javascript" src="../../../js/funciones.js"> </script>
<script>
//Función que permite validar que sólo se escriban números y puntos en el campo
function validar_cod(objeto) {
	var checkOK = "0123456789. ";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++) {
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length) {
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Escriba solo digitos caracteres y puntos...");
			break;
		}
	}
} 

function ejecutar() {
  document.form1.submit();
}
</script>
</head>
<body>
<form name="form1" method="post" action="buscarCuenta.php">
<table width="40%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
<td colspan="2" class="normalNegroNegrita">Buscar cuentas bancarias</td>
</tr>
<tr>
  <td class="normalNegrita">Banco:</td>
  <td class="normal">
  <select name="slc_banco" class="normal">
	<option value="0">[Seleccione]</option>
	<?php
	    $sql="SELECT banc_id,banc_nombre FROM sai_banco";
		$resultado=pg_query($conexion,$sql) or die("Error al consultar");  
		while($row=pg_fetch_array($resultado)) {?>
   	     <option value="<?=$row['banc_id']?>"><?=$row['banc_nombre']?></option> 
   <?php } ?>
	</select>
  </td>
</tr>
<tr>
  <td class="normalNegrita">Tipo de cuenta:</td>
  <td class="normal">
  <select name="slc_tipo" id="slc_tipo" class="normal">
	   <option value="0">[Seleccione]</option>
	   <?php
	    $sql="SELECT tipo_id,tipo_nombre FROM sai_tipocuenta"; 
		$resultado=pg_query($conexion,$sql) or die("Error al consultar");  
		while($row=pg_fetch_array($resultado)) { 
		?>
   	     <option value="<?=$row['tipo_id']?>"><?=$row['tipo_nombre']?></option> 
  <?php } ?>
  </select>
  </td>
</tr>
<tr>
  <td class="normalNegrita">A&ntilde;o de apertura:</td>
  <td class="normal">
  <input type="text" name="txt_pres_anno" maxlength="4" class="normal" size="6" onkeypress="return acceptNum(event)" value=""/> 
  </td>
</tr>
<tr>
  <td align="center" colspan="2">
    <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar();"/>
   </td>
</tr>
</table>
</form>
<br/>
<form name="form3" action="" method="post">		
<?php

$banco = $_POST['slc_banco'];
$tipo = $_POST['slc_tipo'];
$ano = $_POST['txt_pres_anno'];
if ($ano>2000) $condicion =" AND cb.ctab_ano='".$ano."'";
if ($_POST['slc_banco']!='0' && $_POST['slc_banco']!='') $condicion .= " AND cb.banc_id='".$banco."'";
if ($_POST['slc_tipo']!='0' && $_POST['slc_tipo']!='') $condicion .= " AND cb.tipo_id='".$tipo."'";

$sql="SELECT cb.ctab_numero, 
			cb.banc_id,
			UPPER(b.banc_nombre) AS banc_nombre,
			e.esta_nombre,
			cb.ctab_ano,
			UPPER(cb.ctab_descripcion) AS ctab_descripcion,
			cb.ctab_estatus,
			tc.tipo_nombre,
			cb.tipo_id
	 FROM sai_ctabanco cb,
		sai_banco b,
		sai_estado e,
		sai_tipocuenta tc
	WHERE cb.tipo_id = tc.tipo_id
		AND cb.banc_id = b.banc_id
		AND e.esta_id = cb.ctab_estatus ".$condicion ."
	ORDER BY cb.ctab_numero";
$resultado=pg_query($conexion,$sql) or die("Error al buscar las cuentas bancarias");
$nroFila= pg_num_rows($resultado);
if($nroFila<=0) { 
	echo "<br><div align='center' class='normalNegrita'>";
	echo "Actualmente no existen cuentas bancarias con ese criterio de b&uacute;squeda";
	echo "</div>";
}
else { ?>
		 <table width="80%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
		  <tr align="center" class="td_gray"> 
		  <td width="20%" class="normalNegroNegrita">N&uacute;mero de cuenta</td>
	      <td width="8%" class="normalNegroNegrita">Tipo</td>	
	      <td width="4%" class="normalNegroNegrita">A&ntilde;o</td>		      	  
		  <td width="20%"  class="normalNegroNegrita">Banco</td>
		  <td width="20%"  class="normalNegroNegrita">Descripci&oacute;n</td>		  
		  <td width="10%"  class="normalNegroNegrita">Estado</td>
		  <td width="38%"  class="normalNegroNegrita">Opciones</td>
		  </tr>
	<?php while($row=pg_fetch_array($resultado)) {
			if (($row['tipo_id']=='1') and ($row['ctab_estatus']=='2')) {
				$correlativa="";
				$alterna="";
				$chequera="";
				$estado="Inactiva";
				$accion="";
			} 
			if (($row['tipo_id']=='1') and ($row['ctab_estatus']=='1')) {
				$correlativa="";
				$alterna="";
				$chequera="";
				$estado="Activa";
				$accion="Inactivar Cuenta";
			}
			if (($row['tipo_id']=='2') and ($row['ctab_estatus']=='1')) {
				$correlativa="Agregar Chequera Correlativa";
				$alterna="Agregar Chequera Alterna";
				$chequera="Consultar Chequeras";
				$estado="Activa";
				$accion="Inactivar Cuenta";
			}
			if (($row['tipo_id']=='2') and ($row['ctab_estatus']=='2')) {
				$correlativa="";
				$alterna="";
				$chequera="";
				$estado="Inactiva";
				$accion="";
			}?>
					<tr class="normal"> 
  					<td><?=$row['ctab_numero']?></td>
					<td><?=$row['tipo_nombre']?></td>  					
					<td><?=$row['ctab_ano']?></td>					
  					<td><?=$row['banc_nombre']?></td>
  					<td><?=$row['ctab_descripcion']?></td>
					<td><?=$row['esta_nombre']?></td>
  					<td>
					<?if ($row['ctab_estatus']!=1 && ($_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS)) {?>
					<a href="javascript:activar('<?=$row['ctab_numero']?>')" class="copyright">Activar</a><br/>
                   <?}?>
					<a href="javascript:detalle('<?=$row['ctab_numero']?>')" class="copyright">Ver Detalle</a><br/>
					<?php if ($_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>
					<a href="javascript:cerrar('<?=$row['ctab_numero']?>','<?=$row['banc_nombre']?>', '<?=$row['tipo_cuenta']?>', '<?=$ano?>')" class="copyright"><?=$accion?></a><br/>
			    	<a href="javascript:consultar('<?=$row['ctab_numero']?>','<?=$row['banc_nombre']?>', '<?=$row['tipo_nombre']?>', '<?=$ano?>')" class="copyright"><?=$chequera?></a><br/>
			    	<?php }?>	
					</td>
					</tr>
  <?php } ?>
    </table> 
<?php } ?>
</form>
</body>
</html>