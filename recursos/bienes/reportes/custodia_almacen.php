<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script lenguage="javascript">

function detalle(codigo,nombre)
{
    url="alma_rep_e1.php?codigo="+codigo+"&nombre="+nombre
	newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar()
{ 
   
  //if ((document.form.tipo_acti.value=='') && (document.form.tipo_arti.value==''))
  if (document.form.tipo_acti.value=='')
   {
   	 document.form.hid_validar.value=1;
   }
   else {document.form.hid_validar.value=2;
   }
   document.form.submit();
 //window.location="actas.php?&codigo="+codigo+"&txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&tipo_acta="+codigo3

  
}
</script>

</head>
<body>
<form action="custodia_almacen.php" name="form" id="form" method="post">
<input type="hidden" value="0" name="hid_validar" />

<table width="700" align="center" background="imagenes/fondo_tabla.gif"
	class="tablaalertas" id="sol_via">
	<tr>
		<td height="15" colspan="2" valign="midden" class="td_gray"><span
			class="normalNegroNegrita"> Custodia</span></td>
	</tr>
	<!-- <tr>
		<td class="normal"><strong>Clasificaci&oacute;n Art&iacute;culos</strong></td>
		<td><select name="tipo_arti[]" class="normalNegro" id="tipo_arti"
			multiple>
			<option value="" class="normalNegrita"><b>[Selecci&oacute;n M&uacute;ltiple]</b></option>
			    <?php
	  $sql_arti="SELECT * FROM sai_arti_tipo order by tp_desc"; 
	  $resultado=pg_query($conexion,$sql_arti) or die("Error al mostrar");
	  while($row_arti=pg_fetch_array($resultado))
	    { 
	  ?>
	  <option value="<?=$row_arti['tp_id'];?>"><?php echo $row_arti['tp_desc'];?></option> 
     <?php 
	  }
	  ?>
		</select></td>
	</tr>-->
	<tr>
		<td class="normal"><strong>Clasificaci&oacute;n</strong></td>
		<td><select name="tipo_acti[]" class="normalNegro" id="tipo_acti" multiple>
			<option value="" class="normalNegrita"><b>[Selecci&oacute;n M&uacute;ltiple]</b></option>
    		<?php
	 		 $sql="SELECT * FROM bien_categoria order by nombre"; 
	  		$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	 		 $actual=trim($_REQUEST['unidad_med']);
	  		while($row=pg_fetch_array($resultado))
	  		  { 
			  ?>
			  <option value="<?=$row['id'];?>"><?php echo $row['nombre'];?></option> 
   			  <?php 
			  }
	 		 ?>
		</select></td>
	</tr> 
	<tr height="15"></tr>
	<tr><td colspan="2" align="center"><input type="button" value="Buscar" onclick="ejecutar();" class="normalNegro"></input></td></tr>
</table>
</form>

<form name="form1" action="" method="post">
<?php 

if ($_POST['hid_validar']==1)
{
   echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe seleccionar al menos una clasificaci\u00F3n del activo');"."</SCRIPT>";
}else{
	// if ( (($_POST['tipo_arti'])!='') or (($_POST['tipo_acti'])!='') ){
	 if (($_POST['tipo_acti'])!='') {
	 /* 	$articulos=$_POST['tipo_arti'];
		$listado_articulos='';

		for ($i=0;$i<count($articulos);$i++)    
		{     
 		if ($i==0)
  		  $listado_articulos=$articulos[$i];
 		else
  		  $listado_articulos=$listado_articulos."','".$articulos[$i];
		} 

 		$listado_articulos="'".$listado_articulos."'";*/
	 	
 
 		$activos=$_POST['tipo_acti'];
		$listado_activos='';

		for ($i=0;$i<count($activos);$i++)    
		{     
 		if ($i==0)
  		  $listado_activos=$activos[$i];
 		else
  		  $listado_activos=$listado_activos."','".$activos[$i];
		} 

 		$listado_activos="'".$listado_activos."'";
 				
//Almacen
$sql_arti_activos="SELECT t1.id,nombre
FROM sai_item t1,sai_item_distribucion t2 
WHERE t1.id=t2.id and id_tipo=1 and t1.esta_id=1
UNION
SELECT t1.id,nombre
FROM sai_item t1,sai_item_distribucion t2 
WHERE t1.id=t2.id and id_tipo=2 and t1.esta_id=1
order by nombre";
$resultado_set_arti_activos=pg_query($conexion,$sql_arti_activos) or die("Error al mostrar consulta en sai_articulo");


if (($rowta=pg_fetch_array($resultado_set_arti_activos)) == null)
{  
?><center>
 <span class="normalNegrita_naranja">No existen art&iacute;culos en el almac&eacute;n </span>
</center>
<?php 
}
else
  {
   
//Almacen Torre Y //Almacen Galpon
/*$sql_arti="SELECT t1.id,nombre,cantidad,tp_desc,ubicacion
FROM sai_item t1,sai_item_distribucion t2 ,sai_item_articulo t3,sai_arti_tipo
WHERE t1.id=t2.id and id_tipo=1 and t3.id=t1.id and t3.id=t2.id and t1.esta_id=1 and tp_id=tipo
and ubicacion=1 and tp_id in (".$listado_articulos.")
UNION ALL
SELECT t1.id,nombre,cantidad,tp_desc,ubicacion
FROM sai_item t1,sai_item_distribucion t2 ,sai_item_articulo t3,sai_arti_tipo
WHERE t1.id=t2.id and id_tipo=1 and t3.id=t1.id and t3.id=t2.id and t1.esta_id=1 and tp_id=tipo
and ubicacion=2 and tp_id in (".$listado_articulos.") 
group by tipo,tp_desc,ubicacion, t1.id,nombre,cantidad order by tp_desc,nombre";*/
//echo $sql_arti;

//Activos Torre Y //Activos Galpon
$sql_acti="
SELECT etiqueta,serial,t4.nombre as tp_desc,t1.nombre, ubicacion 
FROM sai_item t1,sai_biin_items t2 , sai_item_bien t3,bien_categoria t4
WHERE t1.id=t3.id and t1.id=t2.bien_id and t2.bien_id=t3.id and 
t3.tipo=t4.id and t4.id in  (".$listado_activos.") 
and ubicacion=1 and t1.esta_id=1 and t1.id_tipo=2 and t2.esta_id=41

UNION ALL

SELECT etiqueta,serial,t4.nombre as tp_desc,t1.nombre, ubicacion 
FROM sai_item t1,sai_biin_items t2 , sai_item_bien t3,bien_categoria t4
WHERE t1.id=t3.id and t1.id=t2.bien_id and t2.bien_id=t3.id and 
t3.tipo=t4.id and t4.id in  (".$listado_activos.") 
and ubicacion=2 and t1.esta_id=1 and t1.id_tipo=2 and t2.esta_id=41
group by t4.nombre,t1.nombre,ubicacion,etiqueta,serial order by tp_desc,nombre";
//echo $sql_acti;
?>
<br/><br/>
<table width="615" background="../../../imagenes/fondo_tabla.gif" align="center" class="tablaalertas" border="0">
  
    <div align="center" class="normalNegroNegrita">Custodia activos para la fecha
      <?=date("d/m/y")?> a las <?=date("h:i:s")?></div>
  
  <?php 

  if ($_POST['tipo_acti']!=''){?>
  <tr class="td_gray">
	<td width="137" align="center" class="normalNegroNegrita">Serial Bien Nacional</td>
	<td width="176" align="center" class="normalNegroNegrita">Nombre</td>
	<td width="176" align="center" class="normalNegroNegrita">Serial Activo</td>
	<td width="142" align="center" class="normalNegroNegrita">Existencia Torre </td>
	<td width="140" align="center" class="normalNegroNegrita">Existencia Galp&oacute;n </td>
  </tr>
  <?php
    }
    $resultado_set_acti=pg_query($conexion,$sql_acti) or die("Error al mostrar consulta en sai_activo");
    $cont=1;$torre=0;$galpon=0;$titulo='';
    while($rowta=pg_fetch_array($resultado_set_acti))
	{
		$categoria=$rowta['tp_desc'];
		if ($categoria<>$titulo){?>
	  <tr bgcolor="#F0F0F0">
    	<td align="center" class="normalNegrita" colspan="5"><?php echo $categoria; ?></td>	
  </tr> 		
			
		<?
		$titulo=$categoria;
		}
		$torre="-";
		$galpon="-";
		if ($rowta['ubicacion']=="1")
		 $torre="X";
		 else
		 $galpon="X";
		 
	?>
  <tr>
	<td height="25" align="center" class="normal"><div align="center"><?php echo $rowta['etiqueta'];?></div></td>
	<td align="left" class="normal"><?php echo $rowta['nombre'];?></td>
	<td align="left" class="normal"><?php echo $rowta['serial'];?></td>
	<td align="center"class="normal"><?php echo $torre; ?></td>
	<td align="center"class="normal"><?php echo $galpon; ?></td>	
  </tr> 
	<?php }?>
	  
</table>
	  <div align="center"><br />   
	  <span class="peq_naranja">Detalle generado  el d&iacute;a
      <?=date("d/m/y")?> a las <?=date("h:i:s")?> </span><br /><br />   
	 <!--   <a href="existencia_minima_pdf.php"><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>
	  <br><br>
     <a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br />
      <span class="normalNegrita">Imprimir Documento</span> <br><br> -->
      <br /><br />
     <?php
	  }
	 	
	 	
	 }//fin if


	 		 


 }
   ?>
      </div>
</form>
</body>
</html>
<?php pg_close($conexion);?>
