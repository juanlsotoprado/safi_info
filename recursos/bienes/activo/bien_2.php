<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 require("../../../includes/perfiles/constantesPerfiles.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	
	ob_end_flush(); 
?>
<?php $codigo=trim($_GET['codigo']); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:MODIFICAR BIENES</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>

<script>
//Funci�n que permite verficar que la descripcion no se encuentre repetida
function verificar_descripcion()
{ 
   palabra=document.form1.txt_nombre.value;
   <?php
   $sql_p="SELECT * FROM sai_seleccionar_campo('sai_item','nombre','id_tipo=2','',2) resultado_set(nombre varchar)"; 
   $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
   while($row=pg_fetch_array($resultado_set_most_p)) 
   {?>
    palabra1="<?php echo trim($row['nombre']); ?>"
	if (palabra.toUpperCase()==palabra1.toUpperCase())
        {
	 alert("Esta descripci\u00F3n ya existe en la base de datos...");
	 document.form1.descrip.value='';
	}
    <?php
   }
  ?> 
}

//Funci�n que valida el llenado de todos los campos 
function revisar(tipo)
{
	if (tipo==1){
		document.form2.accion.value="presupuesto";
     //Verificamos que el usuario seleccione la partida
 	 if (document.form2.partida.value==0)
	 {
		alert("Debe seleccionar la partida presupuestaria.")
		document.form2.partida.focus();
		return;
	 }
	}else{
		document.form2.accion.value="bienes";
      //Verificamos que el usaurio ingrese la descripci�n del articulo
	if (document.form2.txt_nombre.value=="")
	{
		alert("Debe especificar el nombre del activo")
		document.form2.txt_nombre.select();
		document.form2.txt_nombre.focus();
		return;
	}	

	//Debe Ingresar la Existencia Minima
   	if (document.form2.txt_exist_bien.value=="")
	{
		alert("Debe especificar la existencia m\u00EDnima del activo")
		document.form2.txt_exist_bien.select();
		document.form2.txt_exist_bien.focus();
		return;
	}	
	
	//Debe Ingresar la Vida Ùtil
   	if (document.form2.txt_vida_util.value=="")
	{
		alert("Debe especificar la vida \u00FAtil del activo")
		document.form2.txt_vida_util.select();
		document.form2.txt_vida_util.focus();
		return;
	}
	}
	//Verificamos que el usuario este seguro de la operaci�n
	if(confirm("Estos datos ser\u00E1n registrados. \u00BFEst\u00E1 seguro que desea continuar?"))
	{
	  document.form2.submit()
	}
}		
/*Funcii�n que valida que solo se introduzcan digitos caracteres y numericos en el campo*/
function validar_digito(objeto)
{
	var checkOK = "ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����0123456789 -_.;,()";
	var checkStr = objeto.value;
	var allValid = true;
	for (i = 0;  i < checkStr.length;  i++)
	{
		ch = checkStr.charAt(i);
		for (j = 0;  j < checkOK.length;  j++)
		if (ch == checkOK.charAt(j))
			break;
		if (j == checkOK.length)
		{
			var cambio=checkStr.substring(-1,i) 
			objeto.value=cambio;
			alert("Estos caracteres no est\u00E1n permitidos");;
			break;
		}
	}
}
function rela() 
{
 document.form2.txt_cod_part.value=document.form2.partida.value;
  
}
</script>

</head>
<body>
<form name="form2" action="bien_e2.php" method="post">
<input type="hidden" name="accion" value=""></input>
<?php 
#Efectuamos la consulta SQL  

$sql_art="SELECT t1.id,nombre,descripcion,esta_id,existencia_minima,vida_util,tipo
FROM sai_item t1, sai_item_bien t2
WHERE t1.id=t2.id and t1.id='".$codigo."'"; 
$resultado_set_art=pg_query($conexion,$sql_art);
if($row=pg_fetch_array($resultado_set_art)) 
{ 
 	$bien_id=$row['id'];
	$bien_nombre=$row['nombre'];
	$bien_descripcion=$row['descripcion'];
	$esta_id=$row['esta_id'];
	$bien_exi_min=$row['existencia_minima'];
	$bien_vida_util=$row['vida_util'];
	$clasificacion=$row['tipo'];
	$partida="";		
	
 	//Buscamos el nombre de la Clasificación del artículo
	$sql_clasif = "select * from bien_categoria where id=".$clasificacion."";
	$resultado_clasif=pg_query($conexion,$sql_clasif) or die("Error al conseguir el nombre de la Clasificacion");
	$row_clasif=pg_fetch_array($resultado_clasif);
	
	$sql_art1="SELECT * FROM sai_seleccionar_campo('sai_item_partida','pres_anno, part_id','id_item=''$codigo''','',2) 
	resultado_set(pres_anno int4, part_id varchar)"; 
	$resultado_set_art1=pg_query($conexion,$sql_art1) or die("Error al mostrar los Bienes");
	if($row1=pg_fetch_array($resultado_set_art1)) {$partida=$row1['part_id'];}
	?>
	<input name="txt_articulo" type="hidden" value="<?=$row['arti_id']?>" />
	<?php
	//Buscamos nombre de partida
	$sql_parti = "select * from sai_seleccionar_campo('sai_partida','part_nombre','part_id='||'''$part_id''','',0) Resultado_set(part_nombre varchar)";
	$resultado_parti=pg_query($conexion,$sql_parti) or die("Error al conseguir el Nombre de la Partida");
	if($row_parti=pg_fetch_array($resultado_parti))
	{$part_nom=$row_parti['part_nombre']; }
	?><br />
<?php  if(($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO)){ ?>	
	
	 <table width="666" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
       <tr class="td_gray">
         <td height="15" colspan="2" valign="midden"   class="normalNegroNegrita">Modificar activos </td>
       </tr>
       <tr>
         <td height="34" valign="midden"><div align="left" class="normalNegrita">
             Partida:
         </div></td>
         <td width="75%" height="34" colspan="2" valign="midden">
		   <select name="partida" id="partida" class="normal">
       	<?php
	      $sql_part="select *  from sai_buscar_art_bien_comp(".$_SESSION['an_o_presupuesto'].",2,1) as resultado_set(partida_id varchar,partida_nombre varchar)"; 
	      $resultado_part=pg_query($conexion,$sql_part) or die("Error al mostrar");
	      if ($partida==""){?>
	      <option value="0">[Seleccione]</option> 
	 	  <?php }
	 	  while($row_part=pg_fetch_array($resultado_part))
	{ 
		$part_id=$row_part['partida_id'];
	    $part_nombre=$row_part['partida_nombre'];
	    $cad=substr($part_nombre,0,70);
				
						
		?><option value="<?=$part_id?>" <?php if($partida==$part_id){?> selected <?php }?> ><?=$part_id?>:<?php echo $cad;?></option> <?php 
	} 
	?>
	</select>
	     <span class="peq_naranja">(*)</span></td>
       </tr>
       <tr>
         <td height="35" valign="midden" class="normalNegrita"><b>C&oacute;digo:</b></td>
         <td height="35" valign="midden"><input name="txt_cod_bien" type="text" class="normal" id="txt_cod_bien" value="<?php  echo $bien_id;?>" size="26" readonly=""/></td>
       </tr>
       <tr>
         <td width="25%" height="36" class="normalNegrita">Nombre:</td>
         <td height="36" colspan="2"><input name="txt_nombre" type="text" class="normal" id="txt_nombre" value="<?=$bien_nombre?>" size="50" readonly/>
         </td>
       </tr>
       <tr>
         <td height="15" colspan="3" align="center"> <br />
         <input type="hidden" name="tp_activo"  value="<?=$clasificacion;?>">
         <input type="button" value="Modificar" onclick="javascript:revisar(1)" class="normalNegro"><br /><br /></div></td>
       </tr>
     </table>
     <?php }else{?>
      <table width="666" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
       <tr class="td_gray">
         <td height="15" colspan="2" valign="midden"   class="normalNegroNegrita">Modificar activos </td>
       </tr>
       <tr>
         <td height="35" valign="midden" class="normalNegrita"><b>C&oacute;digo:</b></td>
         <td height="35" valign="midden"><input name="txt_cod_bien" type="text" class="normal" id="txt_cod_bien" value="<?php  echo $bien_id;?>" size="26" readonly=""/></td>
       </tr>
       <tr>
         <td width="25%" height="36" class="normalNegrita">Nombre:</td>
         <td height="36" colspan="2"><input name="txt_nombre" type="text" class="normal" id="txt_nombre" onchange="verificar_descripcion()" onkeyup="validar_digito(txt_nombre_arti)" value="<?=$bien_nombre?>" size="26"/>
         <span class="peq_naranja">(*)</span></td>
       </tr>
       <tr>
         <td height="49"><span class="normalNegrita">Descripci&oacute;n:</span></td>
         <td height="49" colspan="2"> <span class="normalNegrita"><span class="peq_naranja"><span class="normal">
           <textarea name="txt_bien_descripcion" class="normal" rows="3" cols="32" onkeyup="validar_digito(txt_bien_descripcion)" ><?php echo $bien_descripcion;?></textarea>
           (*)</span></span></span></td>
       </tr>
       <tr> 
      <td height="36" class="normalNegrita">Clasificaci&oacute;n:</td>
      <td height="36" colspan="2"class="normal">
	  <select name="tp_activo" class="normal" id="tp_activo">
      <option value="<?=$clasificacion;?>"><?=$row_clasif['nombre'];?></option>
	  <?php
	  $sql="SELECT * FROM bien_categoria where id<>".$clasificacion.""; 

	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  while($row=pg_fetch_array($resultado))
	  { 
   	  	$id=$row['id'];
	  	$descrip=$row['nombre'];
	   	?>
	  	<option value="<?php echo $id;?>"><?php echo $descrip;?></option> 
	  	<?php 
	  } ?>
	  </select> <span class="peq_naranja">(*)</span> </td>
      </tr>
          <tr>
         <td height="36" valign="midden" class="normalNegrita">Existencia  m&iacute;nima:</div></td>
         <td height="36" colspan="2" valign="midden">
           <input name="txt_exist_bien" type="text" class="normal" size="26" value="<?=$bien_exi_min?>" onkeypress="return acceptNum(event);"/>
           <span class="peq_naranja">(*)</span></td>
       </tr>
       <tr>
		  <td height="33" valign="midden" class="normalNegrita">Vida &uacute;til(A&ntilde;os):</td>
		  <td height="33" valign="midden">
		  <input name="txt_vida_util" type="text" class="normal" size="10" maxlength="6"  value="<?=$bien_vida_util?>"  onkeypress="return acceptNum(event);" />
		  <span class="peq_naranja">(*)</span></td>
		</tr>
       <tr>
         <td class="normalNegrita">Estado:</td>
         <td height="37" colspan="4" class="normalNegrita">
           <?php if($esta_id==1){?>
           <input name="opt_estado" type="radio" value="1" checked="checked" />
           Activo
           <input name="opt_estado" type="radio" value="2"/>
           Inactivo
           <?php }else{?>
           <input name="opt_estado" type="radio" value="1"/>
           Activo
           <input name="opt_estado" type="radio" value="2" checked="checked"/>
           Inactivo
           <?php }?>         </td>
       </tr>
       
       <tr>
         <td height="15" colspan="3" align="center"> <br />
         <input type="button" value="Modificar" onclick="javascript:revisar(0)" class="normalNegro">
<br />
                 <br />
           </div></td>
       </tr>
     </table>
	 <?php }
}  //fin de la consulta principal
?>
</form>
</body>
</html>
