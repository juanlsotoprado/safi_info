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

$sql=" SELECT nacionalidad, empl_cedula, upper(empl_nombres) as empl_nombres, upper(empl_apellidos) as empl_apellidos FROM sai_empleado WHERE empl_cedula NOT IN (SELECT empl_cedula FROM sai_usuario) and esta_id=1 order by empl_apellidos";	
	$ejecuta_ced = pg_query($conexion,$sql);
	$nroFilas_ced = pg_num_rows($ejecuta_ced);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script>
function buscar() {
	if(document.form1.txt_cedula.value=="" && document.form1.txt_nombre.value=="" && document.form1.txt_apellido.value=="") {
		alert("Debe incluir el Nro. de identidad o el Nombre en la b\u00fasqueda");
		document.form1.txt_cedula.focus();
		return;
	} 
	document.form1.submit();
}
</script>
</head>
<body>
<div align="center">
<?php if ($nroFilas_ced) {?>
  <br />
  <span class="normalNegroNegrita"><img src="../../imagenes/vineta_azul.gif" width="11" height="7" /> Empleados ingresados hasta el momento sin asignar Usuario </span><br /> 
  <br />
</div>
<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center" >
		<td class="normalNegroNegrita">Documento de Identidad</td>
		<td class="normalNegroNegrita">Apellido(s)</td>
		<td class="normalNegroNegrita">Nombre(s)</td>
		<td class="normalNegroNegrita">Opci&oacute;n</td>
	</tr>
	<? while($row = pg_fetch_array($ejecuta_ced)) {
		$nacionalidad = $row['nacionalidad'];		
		$cedula = $row['empl_cedula'];
		$nombre = $row['empl_nombres'];
		$apellido = $row['empl_apellidos'];
		$pos_nombre = strpos($nombre, " ");
		$pos_apellido = strpos($apellido, " ");
		if ($pos_nombre =="") $nombre1 = $nombre;
		else $nombre1 = substr($nombre,0,$pos_nombre);
		if ($pos_apellido =="") $apellido1 = $apellido;
		else	$apellido1 = substr($apellido,0,$pos_apellido);
	 ?>
	 <tr class="<? echo $claseFila; ?>">
		 <td align="center" class="normal"><?php echo $nacionalidad."-".$cedula;?>
			  <input name="textfield3" type="hidden" class="peq" value="<?php echo $cedula ?>" /></td>
        		 <td class="normal">
          			 <?php echo $apellido;?>
					 <input name="textfield22" type="hidden" class="peq" value="<?php echo $apellido;?>" size="28" /></td>
        			 <td class="normal">
					 <?php echo $nombre; ?>
					 <input name="textfield222" type="hidden" class="peq" value="<?php echo $nombre;?>" size="28" /></td>
          			 <td align="left" class="normal">
					 <img src="../../imagenes/vineta_azul.gif" width="11" height="7">
					 <a href="ingresar.php?txt_usuario=<?php echo $nombre1." ".$apellido1;?>&txt_cedula=<?php echo $cedula;?>"> Agregar</a> </td>
					 <?php } } pg_close($conexion); ?>
      				 </tr>	
		</table>
</body>
</html>