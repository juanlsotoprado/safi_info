<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush();    exit;
  }
  ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Modificar Ingreso Almac&eacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript">

function revisar(filas)
{ 
 for (t=0;  t<filas; t++)
 {
	 var cantidad=document.getElementById('cantidad_'+t).value;
	 var precio=(document.getElementById('precio_'+t).value);
	  if (cantidad==0)
	  	{
		 alert("Debe indicar la cantidad art\u00EDculos a ingresar");
		 document.getElementById('cantidad_'+t).focus();
		 return;
		}
  	if ((precio <=0) || (precio ='')){
		 alert("Debe indicar el precio del art\u00EDculo");
		 document.getElementById('precio_'+t).focus();
		 return;
	}
 }
 if(confirm("Datos introducidos de manera correcta. \u00BFEst\u00E1 seguro que desea continuar?."))
 {  	
   document.form.submit();
  }	
}	 
</script>

</head>
<body>
<form name="form" action="modificarAccion.php" method="post">
<?php
$hoy=date("d/m/YY");
$fecha_ini=substr($hoy,6,4)."-".substr($hoy,3,2)."-".substr($hoy,0,2);
$fecha_fin=substr($hoy,6,4)."-".substr($hoy,3,2)."-".substr($hoy,0,2)." 23:59:59";
?>
	<br /><br />
<table width="850" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
 <tr class="td_gray"> 
   <td height="15" colspan="5" valign="midden" class="normalNegroNegrita">Inventario registrado el dia <?=date("d/m/y")?></td>
 </tr>
 <tr>
   <td height="11" colspan="5"></td>
 </tr>
 <tr>
   <td height="11" colspan="5"></td>
 </tr>
 <tr>
   <td height="48" colspan="5"><table width="750" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
 <tr>
   <td width="55" height="21"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
   <td width="200"><div align="center" class="normalNegrita">Nombre</div></td>
   <td width="200"><div align="center" class="normalNegrita">Dependencia Solicitante</div></td>
   <td width="77"><div align="center" class="normalNegrita">Cantidad</div></td>
   <td width="59"><span align="center" class="normalNegrita">Unidad de Medida</span></td>
   <td width="80"><div align="center" class="normalNegrita">Precio</div></td>
   <td width="100"><div align="center" class="normalNegrita">Fecha recepci&oacute;n</div></td>
 </tr>
 <?php
   $sql_or="SELECT * FROM sai_seleccionar_campo('sai_arti_almacen','alm_id,alm_fecha_recepcion,depe_solicitante,arti_id,cantidad,precio','fecha_proceso >= '||'''$fecha_ini'' and fecha_proceso <= '||'''$fecha_fin'' ORDER BY fecha_proceso DESC','',2) resultado_set(alm_id int,fecha_recepcion date,depe_solicitante varchar,arti_id varchar,cantidad int,precio float)";
   $resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar1");  
   $i=1;
   $tt=0;
   while($row=pg_fetch_array($resultado_set_most_or))
   {
	$almacen=$row['alm_id'];
	$sql="SELECT * FROM sai_seleccionar_campo('sai_arti_salida','alm_id','alm_id=''$almacen''','',2) resultado_set(alm_id int)";        
	
	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar2");  
    if ($resultado_set){$total=pg_num_rows($resultado_set); }

	if($total==0){
  	$arti=trim($row['arti_id']);
    $dependencia=trim($row['depe_solicitante']);
 	$sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','unidad_medida,nombre','t1.id=t2.id and t1.id=''$arti''','',1) resultado_set(unidad_medida varchar, nombre varchar)"; 
 	$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  

  	 if($rowa=pg_fetch_array($resultado_set_most_ar))
   	 {	
 	  $medida[$tt]=trim($rowa['unidad_medida']);
	  $nombre[$tt]=trim($rowa['nombre']);
   	 } 

	$alm_id[$tt]=trim($row['alm_id']);
	$id[$tt]=trim($arti);
	$fecha_recep[$tt]=$row['fecha_recepcion'];
	$cantidad[$tt]=trim($row['cantidad']);
	$precio[$tt]=trim($row['precio']);
	$depe_id[$tt]=trim($dependencia);
	$tt++;
    }
   }

  for ($i=0; $i<$tt; $i++){?>
  <tr>
    <td><div align="center" class="normalNegro"><?php echo $id[$i];?>
     <input type="hidden" name="<?php echo "arti_id_".$i?>" value="<?php echo $id[$i];?>" />
 	 <input type="hidden" name="<?php echo "alm_id_".$i?>" value="<?php echo $alm_id[$i];?>" />
	 <input type="hidden" name="<?php echo "fecha_".$i?>" value="<?php echo $fecha_recep[$i];?>" />
	 </div></td>
    <td><div align="center" class="normalNegro"><?php echo $nombre[$i];?></div></td>
	<td><div align="center">
	  <select name="<?php echo "opt_depe_".$i?>" id="<?php echo "opt_depe_".$i?>" class="normalNegro">
	  <?php
	    $sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','','',1) resultado_set(depe_id varchar, depe_nombre varchar)"; 
 		$resultado_part=pg_query($conexion,$sql) or die("Error al mostrar");
		while($row_part=pg_fetch_array($resultado_part))
		{ 
	     $id_depe=$row_part['depe_id'];
	     $depe_nombre=$row_part['depe_nombre'];
	  ?><option value="<?=$id_depe?>" <?php if($depe_id[$i]==$id_depe){?> selected <?php }?> ><?=$depe_nombre?></option> <?php 
	    } 
	  ?>
	 </select></div></td>
    <td><div align="right" class="normalNegro"><input type="text" name="<?php echo "cantidad_".$i?>" size="2" value="<?php echo $cantidad[$i]?>" id="<?php echo "cantidad_".$i?>" /></div></td>
    <td><div align="center" class="normalNegro"><?php echo $medida[$i];?></div></td>
    <td><div align="right" class="normalNegro"><input type="text" name="<?php echo "precio_".$i?>" size="8" id="<?php echo "precio_".$i?>" value="<?php echo str_replace(",",".",$precio[$i]);//$precio[$i];// ?>" / onKeyPress="return acceptFloat(event)"></div></td>
    <td><div align="center" class="normalNegro"><?php echo $fecha_recep[$i];?></div></td>
  </tr>
  <?php
	}
  ?>
</table>
   </td>
  </tr>
  <tr>
	<td height="85" colspan="5" class="normal">
	 <input type="hidden" name="num_filas" value="<?=$tt?>" />
	 <div align="center"><br />
	 <br>
	 <input class="normalNegro" type="button" value="Modificar" onclick="javascript:revisar(<? echo $tt;?>);"/>
	 <br /><br /><br />
	 <br><br></div>	</td>
	</tr>
</table>
<br />
</form>
</body>
</html>
<?php pg_close($conexion);?>
