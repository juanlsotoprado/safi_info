<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();    exit;
}ob_end_flush(); 
//Variables provinientes de usua_agregar.php
$txt_usuario=trim($_REQUEST['txt_usuario']);
$txt_cedula=trim($_REQUEST['txt_cedula']);

if($txt_cedula!="") {
	$sql="select sai_empleado.depe_cosige, sai_cargo.carg_id, sai_cargo.carg_nombre, sai_dependenci.depe_nombre
	from sai_empleado, sai_cargo, sai_dependenci
	where sai_empleado.empl_cedula='$txt_cedula' and sai_cargo.carg_fundacion=sai_empleado.carg_fundacion 
	and sai_dependenci.depe_id=sai_empleado.depe_cosige";
	$resultado=pg_query($conexion,$sql);
	$row=pg_fetch_array($resultado);
	$dependencia_id=trim($row['depe_cosige']);
	$dependencia_nombre=trim($row['depe_nombre']);
	$cargo_id=trim($row['carg_id']);
	$cargo_nombre=trim($row['carg_nombre']);  
}	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>Ingresar Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" type="text/JavaScript">
/*Funcion que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto) {
	var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
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
			alert("Escriba s\u00F3lo digitos caracteres(no acentuados) y num\u00E9ricos, adem\u00E1s no debe contener la letra");
			break;
		}
	}
}
//Funcion para validar los campos del formulario
function revisar() { 
	document.form.txt_usuario.value=trim(document.form.txt_usuario.value);
	document.form.txt_login.value=trim(document.form.txt_login.value);
	document.form.txt_clave.value=trim(document.form.txt_clave.value);
	document.form.txt_clave2.value=trim(document.form.txt_clave2.value);
	document.form.txt_cedula.value=trim(document.form.txt_cedula.value);
	if(document.form.txt_usuario.value=="") {
		alert("Debe buscar el nombre del empleado");
		return;
	}
	else if(document.form.txt_login.value=="") {
		alert("Debe colocar un nuevo usuario");
		document.form.txt_login.focus();
		return;
	}
	else if(document.form.txt_clave.value=="")  {
		alert("Debe colocar la contrase\u00f1a");
		document.form.txt_clave.focus();
		return;
	}
	else if(document.form.txt_clave2.value=="") {
		alert("Debe repetir la contrase\u00f1a");
		document.form.txt_clave2.focus();
		return;
	}
	else if (document.form.txt_clave2.value != document.form.txt_clave.value) { 
		alert("Las contrase\u00f1as no coinciden");
		document.form.txt_clave2.focus();
		return;
	}
	else if(document.form.txt_cedula.value=="") {
		alert("Debe colocar el documento de identidad");
		document.form.txt_cedula.focus();
		return;
	}
	else if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?.")) {  
		var texto_partida=crear_txt_partida();
		document.form.txt_arreglo.value=texto_partida;
		var texto_rol=crear_txt_rol();
		document.form.txt_arreglo2.value=texto_rol;	
		document.form.submit();
	}
}	
 
function comprobar_cadena() { 
	var usuario = trim(document.form.txt_login.value);
    <?php
	$sql_p="SELECT usua_login FROM sai_usuario"; 
	$resultado_set_most_p=pg_query($conexion,$sql_p);
	while($row=pg_fetch_array($resultado_set_most_p)) {?>
		nom_login1="<?php echo trim($row['usua_login']); ?>"
		if (usuario==nom_login1) {
			alert("El nombre de usuario ya se encuentra registrado");
			document.form.txt_login.value='';
			return;
		}
		<?php } ?> 
   largo = (usuario.length);
   //compruebo que el tamano del string sea valido. 
   if(document.form.txt_login.value!="")  {
	   if (largo<3 || largo>20)  { 
		  alert("El nombre de usuario no es v\u00E1lido, debe contener entre 3 y 20 caracteres"); 
		  document.form.txt_login.value='';
		  document.form.txt_login.focus();
		  return; 
	   } 
   }
   
   //compruebo que los caracteres sean los permitidos 
   permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_'; 
   
   for (i=0; i<largo; i++) { 
       variable1 = usuario.substring(i,i+1);
       variable2 = permitidos.lastIndexOf(variable1);
       if (variable2 < 0) {
		  mensaje = "El nombre de usuario no es v\u00E1lido, no debe contener el caracter '" + variable1 +"'";
          alert (mensaje); 
		  document.form.txt_login.value='';
		  document.form.txt_login.focus();
          return; 
	   }
   } 
}

function extraer_login() {  
		nombre_usuario = trim(document.form.txt_usuario.value);
   		largo = (nombre_usuario.length);
   		//extraer nombre y apellido
   		for (i=0; i<largo; i++) { 
       		variable1 = nombre_usuario.substring(i,i+1);
	   		if (variable1 == " ") {
	      		nombre = nombre_usuario.substring(0,i+1);
		  		apellido = nombre_usuario.substring(i+1,largo);
		  		login = nombre.substring(0,1) + apellido;
		  		document.form.txt_login.value = login;
		  		document.form.txt_clave.focus();
		  		return;
			}
    	} 
		document.form.txt_login.value = nombre_usuario;
}   

var arreglo_part=new Array()
var arreglo_partm=new Array()


function agregar_cargo(id,tipo) {
	if(tipo==0){
	if(document.form.slc_roles.value==0) 	{
		alert("Debe seleccionar un perfil...");
		return;
	}
	cod_perfil=document.form.slc_roles.value;
	<?php
	$sql_part="select substring(dc.carg_id from 0 for 3)||dc.depe_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia from sai_depen_cargo dc, sai_cargo c, sai_dependenci d
			where dc.carg_id=c.carg_id and dc.depe_id=d.depe_id";
	$resultado_set_part=pg_query($conexion,$sql_part);
	while($row_part=pg_fetch_array($resultado_set_part)){ 
	?>
		if (cod_perfil =="<?php echo trim($row_part['codigo']); ?>")  {		
			nombre_perfil ="<?php echo trim($row_part['codigo']).": ".$row_part['cargo']."- ".$row_part['dependencia']; ?>"
		}
	<?php } ?>
	for(i=0;i<arreglo_part.length;i++) {
		if (arreglo_part[i]==cod_perfil) {
			alert("Perfil ya seleccionado...");
			document.form.slc_roles.value=0
			return;
		}
	}
	
	var registro_part=new Array(1)
	registro_part[0]=document.form.slc_roles.value
	
	var registro_partm=new Array(1)
	registro_partm[0]=nombre_perfil
	
	arreglo_part[arreglo_part.length]=registro_part
	arreglo_partm[arreglo_partm.length]=registro_partm
	}
	var tbody=document.getElementById('bodyp')
	var tbody2=document.getElementById(id)
	for(i=0;i<arreglo_part.length-1;i++) {
		tbody2.deleteRow(1);
	}
	if(tipo!=0) {
		tbody2.deleteRow(1);
		for(i=tipo;i<arreglo_part.length;i++) {
			arreglo_part[i-1]=arreglo_part[i];
			arreglo_partm[i-1]=arreglo_partm[i];
		}
		arreglo_part.pop();
		arreglo_partm.pop();
	}
	for(i=0;i<arreglo_part.length;i++) {
		var row=document.createElement("tr")
		var td1=document.createElement("td")
		td1.appendChild(document.createTextNode(arreglo_partm[i][0]))
		var td2=document.createElement("td")
		td2.setAttribute("align","left")
		td2.className = 'normal';
		editLink=document.createElement("a")
		linkText=document.createTextNode("Eliminar")
		editLink.setAttribute("href","javascript:agregar_cargo('"+id+"','"+(i+1)+"')")
		editLink.className='normal';
		editLink.appendChild(linkText)
		td2.appendChild(editLink)
		row.appendChild(td1)
		row.appendChild(td2)
		tbody.appendChild(row)
	}
	document.form.slc_roles.value=0
	
	if (arreglo_part==0)
		document.getElementById('existepart').style.visibility="visible";
	else
		document.getElementById('existepart').style.visibility="hidden";
}
<!--DESCARGAR LAS PARTIDAS AGREGADAS EN UN TXT TIPO HIDDEN DEL FORMULARIO -->
function crear_txt_partida() {
	document.form.txt_arreglo.value=''
	for(i=0;i<arreglo_part.length;i++) {
			document.form.txt_arreglo.value+=arreglo_partm[i][0];
			if(i!=(arreglo_part.length-1))
				document.form.txt_arreglo.value+="/";
			else
				document.form.txt_arreglo.value;
	}
	return document.form.txt_arreglo.value
}
function crear_txt_rol() {
	document.form.txt_arreglo2.value=''
	for(i=0;i<arreglo_part.length;i++) {
			document.form.txt_arreglo2.value+=arreglo_part[i][0];
			if(i!=(arreglo_part.length-1))
				document.form.txt_arreglo2.value+="/";
			else
				document.form.txt_arreglo2.value;
	}
	return document.form.txt_arreglo2.value
}
</script>
</head>
<body>
<form name="form" method="post" action="ingresarAccion.php">
<br />
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	<td colspan="2">  <div class="normalNegroNegrita"><strong>INGRESAR USUARIO</strong></div>	</td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre:</td>
	<td class="normal">
	<input name="txt_usuario" type="text" id="txt_usuario" value="<?php echo $txt_usuario; ?>" size="31" maxlength="30" class="normal"  onchange="extraer_login()"  onblur="extraer_login()" disabled="disabled" />
	<a href="usuarioAgregar.php"><img src="../../imagenes/buscar_ico.gif" width="24" height="20" border="0" /></a>	</td>
	</tr>
	<tr>
	<td class="normalNegrita">Usuario:</td>
	<td class="normal">
	<input name="txt_login" type="text" id="txt_login" value="" size="31" maxlength="20" onkeypress="validar_login()" onkeyup="validar_digito(txt_login)" class="normal" onchange="comprobar_cadena()"/>	</td> 
	</tr>
	<tr>
	<td class="normalNegrita">Contrase&ntilde;a:</td>
	<td class="normal">
	<input name="txt_clave" type="password" id="txt_clave" size="20" maxlength="20" class="normal" /></td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Confirmar contrase&ntilde;a:</td>
	<td> 
	<input name="txt_clave2" type="password" id="txt_clave2" size="20" maxlength="20" class="normal" />
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Documento de Identidad:</td>
	<td height="42"> 
	<input name="txt_cedula2" type="text" id="txt_cedula2" value="<?php echo $txt_cedula;?>" size="31" maxlength="30" class="normal" disabled="disabled" />	</td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Dependencia:</td>
	<td class="normal">
	<input type="text" name="slc_depen_nom" id="slc_depen_nom" value="<?=$dependencia_nombre?>" class="peq" size="60" readonly="">
	<input type="hidden" name="slc_depen" id="slc_depen" value="<?=$dependencia_id?>">	</td>
	</tr>
	<tr class="normal"> 
	<td class="normalNegrita">Cargo Principal:</td>
	<td>
	<input type="text" name="slc_perf_principal_nom" id="slc_perf_principal_nom" value="<?=$cargo_nombre?>" class="normal" size="30" readonly="">
	<input type="hidden" name="slc_perf_principal" id="slc_depen" value="<?=$cargo_id?>">	</td>
	</tr>
	<tr class="normal">
		<td class="normalNegrita">Cargo Temporal:</td>
		<td class="normalNegrita">
		<select name="slc_roles" class="normal">
			<option value="0">[Seleccione]</option>
			<?php  
$sql="select substring(dc.carg_id from 0 for 3)||dc.depe_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia from sai_depen_cargo dc, sai_cargo c, sai_dependenci d
			where dc.carg_id=c.carg_id and dc.depe_id=d.depe_id order by cargo"; 
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar la relacion cargo dependencia"); 
			$filas = pg_NumRows($resultado);
		
			while($row=pg_fetch_array($resultado)) { 
			$id_perfil=trim($row['codigo']);
			$descrip_perfil=trim($row['codigo']).": ".$row['cargo']."- ".$row['dependencia'];
			?><option value="<?php echo $id_perfil;?>"><?php echo $descrip_perfil;?></option>
			<?php } ?>
		</select>
		<a href="javascript:agregar_cargo('slc_rolesp','0')" class="normal" id="agregarpart">Agregar</a></td>
		</tr>
		<tr><td colspan="2"></td></tr>
		<tr align="center">
		<td colspan="2">
		<table border="0" align="center" bgcolor="#E4F0FC" class="normal" id="slc_rolesp">
          <tr class="td_gray">
            <td class="normalNegrita" colspan="2"> <span class="normal"><strong>Cargo(s) Temporal(es) </strong>
                  <label id="existepart">(No Posee)</label>
            </span></td>
          </tr>
           <tbody id="bodyp">
          </tbody>
        </table>
        </td>
		</tr>
	<tr class="normal">
	<td colspan="2">
	<br/><div align="center">
		<input class="normalNegro" type="button" value="Ingresar" onclick="javascript:revisar();"/>
	</div>	</td>
	</tr>
  </table>
	<input type="hidden" name="txt_arreglo" value="" />
	<input type="hidden" name="hid_principal" value="" />
	<input type="hidden" name="txt_nombre" value=" <? echo $txt_usuario ?>" />
	<input type="hidden" name="txt_arreglo2" value="" />
	<input type="hidden" name="txt_cedula" value=" <? echo $txt_cedula ?>"  />
</form>
</body>
</html>
 <?php pg_close($conexion); ?>