<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/perfiles/constantesPerfiles.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
?>
<?php
include("encripta_desencripta.php");	
$user_perfil_id=$_SESSION['user_perfil_id'];

if (isset($_REQUEST['txt_usuario'])) 
	$txt_usuario=trim($_REQUEST['txt_usuario']);
else
	$txt_usuario=trim($_SESSION['login']);

$sql = "select * from sai_buscar_usuario_empleado('4','','$txt_usuario')
resultado_set(usua_login varchar,nombre text,empl_cedula varchar,usua_activo boolean,
depe_id varchar,nom_depen varchar,usua_clave varchar)";
	$ejecuta = pg_query($conexion,$sql);	
	$row = pg_fetch_array($ejecuta);
	$nombres = trim($row['nombre']);
//	echo $noombres;
	$txt_login = trim($row['usua_login']);
	$txt_cedula = trim($row['empl_cedula']);
	$activo = trim($row['usua_activo']);
	$cod_depen = trim($row['depe_id']);
	$nom_depen = trim($row['nom_depen']);
	$txt_clave = trim($row['usua_clave']);
// desencriptar password
$palabra = "nodigitarnada";
$password = decrypt_md5($txt_clave,$palabra);
$sqlper="SELECT * FROM  sai_buscar_usuario_perfil('$txt_usuario','1')
resultado_set(perfil_id varchar,uspe_tp bit, nomb_perfil varchar)"; 	
$resp_per=pg_query($conexion,$sqlper) or die("Error al buscar perfil principal"); 
$i = 0;
if($rowp=pg_fetch_array($resp_per)) {
	 $codigo_perfil=trim($rowp['perfil_id']);  //orginal es $codigo_perf
	 //$cargo=substr($codigo_perfil,0,2).'000';
	 $cargo=$codigo_perfil;
	 //$codigo_perf=substr($codigo_perfil,0,2).'000';  
	 $codigo_perf=$codigo_perfil;
	 $nombre_perf=trim($rowp['nomb_perfil']);  
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Modificar Usuario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" type="text/JavaScript">
var arreglo_part=new Array();
var arreglo_partm=new Array();
var itinerario = new Array();

<!--FUNCION QUE PERMITE AGREGAR O ELIMINAR PERFILES A UN USUARIO -->
function add_perfil(id,tipo) {
	if(tipo==0) {
	    if(document.form.slc_roles.value==0) {
			alert("Debe seleccionar un perfil...");
			return;
		}
		var roles = new Array()
		var registro = new Array(2);
		roles=document.form.slc_roles.value.split("/");
		registro[0] = roles[0];
		registro[1] = roles[1];	
		cod=trim(roles[0]);
	    for(t=0; t<itinerario.length; t++) {  
			codi=trim(itinerario[t][0]);
			if (codi==cod) {
				alert("Rol ya seleccionado...");
				document.form.slc_roles.value=0;
				return;
			}
		}
		itinerario[itinerario.length]=registro;
	}
	var tbody = document.getElementById('bodyp');
	var tbody2 = document.getElementById(id);
	for(i=0;i<itinerario.length-1;i++) {
		tbody2.deleteRow(1);
	}
	if(tipo!=0) {
		tbody2.deleteRow(1);
		for(i=tipo;i<itinerario.length;i++) {
			itinerario[i-1]=itinerario[i];
		}
		itinerario.pop();
	}

	for(i=0;i<itinerario.length;i++) {
		var row = document.createElement("tr")
		
		var td4 = document.createElement("td")
		td4.setAttribute("align","Center");
		td4.appendChild (document.createTextNode(itinerario[i][0]))
		var td6 = document.createElement("td")
		td6.setAttribute("align","left");
		td6.appendChild (document.createTextNode(itinerario[i][1]))
		var td26 = document.createElement("td")
		td26.setAttribute("align","right");
		td26.className = 'links';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:add_perfil('"+id+"','"+(i+1)+"')");
		editLink.appendChild(linkText);
		td26.appendChild (editLink);
		row.appendChild(td4);
		row.appendChild(td6);
		row.appendChild(td26);
		tbody.appendChild(row);
	}	
}


<!--DESCARGAR LAS PARTIDAS AGREGADAS EN UN TXT TIPO HIDDEN DEL FORMULARIO -->
function pasar_codigo_rol() {
	document.form.txt_arreglo.value='';
	for(i=0;i<itinerario.length;i++) {
			document.form.txt_arreglo.value+=itinerario[i][0];
			if(i!=(itinerario.length-1))
				document.form.txt_arreglo.value+="/";
			else
				document.form.txt_arreglo.value;
	}
	return document.form.txt_arreglo.value;
}

function pasar_nombre_rol() {
	document.form.txt_arreglo2.value='';
	for(i=0;i<itinerario.length;i++) {
		document.form.txt_arreglo2.value+=itinerario[i][1];
		if(i!=(itinerario.length-1))
			document.form.txt_arreglo2.value+="/";
		else
			document.form.txt_arreglo2.value;
	}
	return document.form.txt_arreglo2.value
}

function crear_arreglo(codigo,descripcion,id,i) {
 		var registro = new Array(2);
	    var tbody2 = document.getElementById(id);
	    var tbody = document.getElementById('bodyp');
		registro[0] = codigo;
		registro[1] = descripcion;
		itinerario[itinerario.length]=registro;
		var row = document.createElement("tr");
  	
		var td4 = document.createElement("td");
		td4.setAttribute("align","Center");
		td4.appendChild(document.createTextNode(trim(registro[0])));

		
		var td6 = document.createElement("td");
		td6.setAttribute("align","left");
		td6.appendChild (document.createTextNode(trim(registro[1])));
		var td26 = document.createElement("td");
		td26.setAttribute("align","right");
		td26.className = 'links';
		editLink = document.createElement("a");
		linkText = document.createTextNode("Eliminar");
		editLink.setAttribute("href", "javascript:add_perfil('"+id+"','"+(i+1)+"')");
		editLink.className='link';
		editLink.appendChild(linkText);
		td26.appendChild(editLink);
		row.appendChild(td4);
		row.appendChild(td6);
		row.appendChild(td26);
		tbody.appendChild(row);
}

function revisar() { 
	  var validar
	  document.form.txt_login.value  = trim(document.form.txt_login.value);
	  document.form.txt_clave.value = trim(document.form.txt_clave.value);
	  document.form.txt_clave2.value = trim(document.form.txt_clave2.value);
	  document.form.txt_cedula.value=trim(document.form.txt_cedula.value);
	  
	  if(document.form.txt_clave.value=="")   {
		  alert("Debe colocar la contrase\u00f1a");
		  document.form.txt_clave.focus();
		  return;
	  }
	  else if(document.form.txt_clave2.value=="") {
		 alert("Debe repetir la contrase\u00f1a");
		 document.form.txt_clave2.focus();
	  }
	  else if((document.form.txt_clave2.value!="") && (document.form.txt_clave.value!="")) {
	     if (document.form.txt_clave2.value!=document.form.txt_clave.value) { 
			 alert("Las contrase\u00f1as no coinciden");
			 document.form.txt_clave2.focus();
		     return;
		 } 
	  }
	  else if(document.form.slc_depen.value=="") {
		 alert("Debe seleccionar la dependencia");
		 document.form.slc_depen.focus();
		 return;
	  }  
	if (confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?.")) {  
		var texto_rol=pasar_codigo_rol()
		document.form.txt_arreglo.value=texto_rol;
		var texto_rol=pasar_nombre_rol()
		document.form.txt_arreglo2.value=texto_rol;	
		if (document.form.chk_activo.checked) document.form.txt_activo.value=1
		else document.form.txt_activo.value=0
		document.form.submit()
	}
}	 

</script>
</head>
<body>
<form name="form" method="post" action="modificarAccion.php">
<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray" > 
<td colspan="2" class="normal"><div class="normalNegroNegrita"><strong>MODIFICAR USUARIO</strong></div></td>
</tr>
<tr>
<td class="normalNegrita">Nombre(s) y Apellido(s):</td>
<td class="normal">
<input name="txt_nombre" type="text" id="txt_nombre" value="<?php echo $nombres;?>" size="40" maxlength="40" class="normal" disabled />
</td>
</tr>
<tr>
<td class="normalNegrita">Usuario:</td>
<td class="normal">
<input name="txt_login" type="text" id="txt_login" value="<?php echo $txt_login;?>" size="31" maxlength="20" class="normal" disabled="disabled"/></td> 
</tr>
<tr>
<td class="normalNegrita">Contrase&ntilde;a:</td>
<td class="normal">
<input name="txt_clave" type="password" id="txt_clave" size="12" maxlength="8" class="normal" value=" <?php echo $password;?>" />
</td>
</tr> 
<tr class="normal"> 
<td class="normalNegrita">Confirmar Contrase&ntilde;a:</td>
<td class="normal"> 
<input name="txt_clave2" type="password" id="txt_clave2" size="12" maxlength="8" class="normal" value=" <? echo $password ?>"/>
	<?if ($user_perfil_id == PERFIL_ADMINISTRADOR) {?>	
	Activo:
	<?php if ($activo) { ?> 
	   <input name="chk_activo" type="checkbox" id="chk_activo" value="checkbox" checked="checked" / >
	<?php } else {  ?>
	<input name="chk_activo" type="checkbox" id="chk_activo" value="checkbox" / >
<?php } } else { if ($activo) { ?> 
	   <input name="chk_activo" type="checkbox" id="chk_activo" value="checkbox" checked="checked" /  disabled="true">
	   <?php } else {   ?>
		<input name="chk_activo" type="checkbox" id="chk_activo" value="checkbox" /  disabled="true">
		<?php }}?>
</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Documento de Identidad:</td>
<td class="normal"> 
<input name="txt_cedula" type="text" id="txt_cedula" value="<?php echo $txt_cedula;?>" size="31" maxlength="30" class="normal"  disabled />      
</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Dependencia:</td>
<td class="normal">
<input type="text" name="slc_depen_nom" id="slc_depen_nom" value="<?=$nom_depen?>" class="peq" size="60" readonly="">
<input type="hidden" name="slc_depen" id="slc_depen" value="<?=$cod_depen?>">
</td>
</tr>
<tr class="normal"> 
<td class="normalNegrita">Cargo Principal:</td>
<td class="normal">
<input type="text" name="slc_perf_principal_nom" id="slc_perf_principal_nom" value="<?=$nombre_perf?>" class="normal" size="30" readonly="">
<input type="hidden" name="slc_perf_principal" id="slc_depen" value="<?=$codigo_perf?>">
</td>
</tr>
<?if ($user_perfil_id == PERFIL_ADMINISTRADOR) {?>
<tr class="normal">
<td colspan="2"> 
	<table border="0">
	<tr>
	<td class="normalNegrita">Cargo Temporal:
      <select name="slc_roles" class="normal">
        <option value="0">[Seleccione]</option>
        <?php  
        	$sql = "select substring(dc.carg_id from 0 for 3)||dc.depe_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia from sai_depen_cargo dc, sai_cargo c, sai_dependenci d
			where dc.carg_id=c.carg_id and dc.depe_id=d.depe_id order by cargo";
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar los cargos por dependencia"); 
			$filas = pg_NumRows($resultado);
			while($row=pg_fetch_array($resultado)) {         	
				$perfil=trim($row['codigo']);
				$descrip_perfil=$row['cargo']."- ".$row['dependencia'];
			?>
         <option value="<?php echo $perfil."/".$descrip_perfil;?>"><?php echo $descrip_perfil;?></option>
        <?php }	?>
      </select>		
      <a href="javascript:add_perfil('slc_rolesp','0')" class="link" id="agregar">Agregar</a>	</td>
	</tr>
	<tr>
	<td>
		<table border="0" align="center" bgcolor="#E4F0FC" class="peq" id="slc_rolesp">
		<tr class="td_gray">
		<td colspan="3"  class="normal" align="center"> Cargo Temporal</td> 
	</tr>
		<tbody id="bodyp" class="normal">
		</tbody>		    
		<label id="existepart"><font color="#666666"><br />	
			<?php
			$sql="select up.carg_id as codigo, upper(c.carg_nombre) as cargo, upper(d.depe_nombre) as dependencia 
			from sai_usua_perfil up, sai_cargo c, sai_dependenci d where uspe_tp<>'1' and substring(up.carg_id from 0 for 3)=c.carg_fundacion and substring(up.carg_id from 3 for 3)=d.depe_id and up.usua_login='".$txt_usuario."' order by cargo"; 
			$respuesta=pg_query($conexion,$sql) or die("Error al buscar perfiles temporales"); 
			$i = 0; 
			while($rowc=pg_fetch_array($respuesta)) {	
				$perfil=trim($rowc['codigo']);
				$descrip_perfil=$rowc['cargo']."- ".$rowc['dependencia'];
				?><script language='JavaScript' type='text/JavaScript'>
				crear_arreglo('<?php echo $perfil;?>','<?php echo $descrip_perfil;?>','slc_rolesp',<?=$i;?>)
				</script>		 				  
				<?php $i = $i+1;
			}?>
		</font></label>
		</table>	
	  </td>
	</tr>
	</table>
</td>
</tr>
<?}?>
<tr class="normal">
<td height="24" colspan="2">
<br><div align="center">
	<input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar();"/>
</div>
</td>
</tr>
</table>
<input type="hidden" name="txt_arreglo" value="" />
<input type="hidden" name="txt_usuario" value=" <?php echo $txt_usuario;?>" />
<input type="hidden" name="txt_activo" value="" />
<input type="hidden" name="txt_arreglo2" value="" />
<input type="hidden" name="txt_nombres" value=" <?php echo $nombres;?>" />
<input type="hidden" name="txt_cedula_rif" value=" <?php echo $txt_cedula;?>" />
<input type="hidden" name="hid_principal" value="<?php echo $nombre_perf;?>" />
</form>
</body>
</html>
<?php pg_close($conexion); ?>