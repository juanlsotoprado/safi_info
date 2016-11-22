<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 require("../../../includes/fechas.php");
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	  }
	ob_end_flush();

	function diasEntreFechas($fechainicio, $fechafin){
     return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
    }
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Garant&iacute;a de Activos </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="../../../js/funciones.js"> </script>
<script language="javascript">
function revisar()
{
   
  if ((document.form.marca.value=='0') && (document.form.descripcion.value=='0')){
		  alert("Debe seleccionar un criterio de b\u00fasqueda");
		  return;
	}

	document.form.hid_buscar.value=2;
	document.form.submit();
	
}
</script>

</head>
<body>
<br>
<form name="form" action="garantia_activos.php" method="post">
 <input type="hidden" name="hid_buscar" id="hid_buscar" value="0">
<br />
<table width="500" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
   <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">Garant&iacute;a</td>
  </tr>
  <tr><td class="normalNegrita">Activo:</td>
      <td> <select name="descripcion" class="normalNegro" id="descripcion">
	  <option value="0">[Seleccione]</option>
      <?php 	
	  $sql_b="Select id,nombre from sai_item WHERE id_tipo=2 and esta_id=1 order by nombre"; 
	  $resultado_set_most_b=pg_query($conexion,$sql_b) or die("Error al mostrar");
	  while($rowb=pg_fetch_array($resultado_set_most_b)) 
	  {
	  ?>
 	  <option value="<?php echo(trim($rowb['id'])); ?>"><?php echo(strtoupper(trim($rowb['nombre']))); ?></option>
  	  <?php  }?>
 	  </select></td>
  </tr>
  <tr><td class="normalNegrita">Marca:</td>
	  <td>
	  <select name="marca" class="normalNegro" id="marca">
	  <option value="0">[Seleccione]</option>
		<?php $sql_p="Select * from sai_bien_marca order by bmarc_nombre";
		$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
		while($row=pg_fetch_array($resultado_set_most_p)) 
		   {
		?>
	 <option value="<?php echo(trim($row['bmarc_id'])); ?>"><?php echo(trim($row['bmarc_nombre'])); ?></option>
  	 <?php  }?>
 	 </select></td>
  </tr>
  <tr>
  <tr><td height="52" colspan="3" align="center">
   <input type="button" value="Buscar" onclick="javascript:revisar()" class="normalNegro"></td>
  </tr>
</table>
</form>
<br>
<?php
if (($_POST['hid_buscar'])==2)
{?>
<form name="form1" method="post">
<?php 

 $marca=$_POST['marca'];
 $descripcion=$_POST['descripcion'];
 $fecha_hoy= date("Y-m-d");

 $wheretipo1="";
 $wheretipo2="";
 
 if ($_POST['marca']>0){ 
  $wheretipo1 = " and marca_id='".$marca."' ";
  $query="SELECT bmarc_nombre FROM sai_bien_marca WHERE bmarc_id='".$_POST['marca']."'";
  $resultado=pg_query($conexion,$query);	
   if ($row=pg_fetch_array($resultado)){
	  	$criterio1=" de la Marca ".$row['bmarc_nombre'];
	  }
  }
     
  if ($_POST['descripcion']<>0) {
	$wheretipo2 = " and bien_id='".$descripcion."' ";
	$criterio2=" del Activo ".$descripcion;
  }
 
 
 $sql_ar="SELECT t1.*,bmarc_nombre,t2.nombre FROM sai_biin_items t1,sai_bien_marca,sai_item t2 WHERE garantia>0 AND garantia_vencida=0 AND bmarc_id=marca_id AND T2.id=bien_id ".$wheretipo1.$wheretipo2." order by t2.nombre,marca_id,modelo,etiqueta";
 $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de activos");  
if($row=pg_fetch_array($resultado_set_most_ar))
  {
?>
<table width="1000" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<div align="center"><span class="normalNegroNegrita">Activos en Garant&iacute;a al <?=date("d/m/Y")?> </span></div>
  <tr>
    <td colspan="5">
    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr class="td_gray">
        <td width="41"><div align="center"><span class="normalNegroNegrita">#</span></div></td>
        <td width="107" height="25"><div align="center" class="normalNegroNegrita">Serial Bien Nacional</div></td>
        <td width="321"><div align="center" class="normalNegroNegrita">Nombre</div></td>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Marca</span></div></td>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Modelo </span></div></td>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Serial activo</span></div></td>    
        <td width="70"><div align="center"><span class="normalNegroNegrita">Fecha de entrada</span></div></td>
        <td width="70"><div align="center"><span class="normalNegroNegrita">Garant&iacute;a</span></div></td>
        <td width="70"><div align="center"><span class="normalNegroNegrita">Fecha de vencimiento</span></div></td>
        <td width="70"><div align="center"><span class="normalNegroNegrita">D&iacute;as restantes</span></div></td>
        <td width="100"><div align="center"><span class="normalNegroNegrita">Opci&oacute;n</span></div></td>
        </tr>
      <?php
   	   $j=1;
       $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
	   while($row=pg_fetch_array($resultado_set_most_ar)) 
	   {	
	   	$garantia=$row['garantia'];
		 ?>
      <tr>
        <td bordercolor="1"><div align="right" class="normal"><?php echo $j;?></div></td>
        <td height="21" bordercolor="1"><div align="center" class="normal"><?php echo $row['etiqueta'];?></div></td>
        <td bordercolor="1"><div align="left" class="normal"><?php echo strtoupper($row['nombre']);?></div></td>
	    <td bordercolor="1"><div align="left" class="normal"><?php echo $row['bmarc_nombre'];?></div></td>
        <td bordercolor="1"><div align="left"><span class="normal"><?php echo $row['modelo'];?> </span></div></td>        
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo $row['serial'];?> </span></div></td>
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo cambia_esp($row['fecha_entrada']);?> </span></div></td>
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo $garantia." meses";?></span></div></td>
        <td bordercolor="1"><div align="right"><span class="normal">
    	<?php     
    		$fecha_entrada=$row['fecha_entrada'];
			$dia = substr($fecha_entrada,8,2);
			$mes = substr($fecha_entrada,5,2);
			$ano = substr($fecha_entrada,0,4);
			for ($i=1; $i<=$garantia; $i++) {
			 $mes++;
			 if ($mes > 12) {
			  $mes = 1;
			  $ano++;
			 }
			
			}
			echo $dia."/".$mes."/".$ano; 
  		    $fecha_vencimiento=$ano."-".$mes."-".$dia;

		    $dias_garantia=diasEntreFechas($row['fecha_entrada'], $fecha_vencimiento);
		    $dias_transcurridos=diasEntreFechas($row['fecha_entrada'], $fecha_hoy);
		    if ($dias_transcurridos>$dias_garantia)
		  	 $dias_restantes=0;
		    else
		  	 $dias_restantes=$dias_garantia-$dias_transcurridos;
		  ?></span></div></td>
		  <td bordercolor="1"><div align="right"><span class="normal"><?=$dias_restantes;?></span></div></td>
		  <?php $reporte_transito="SELECT * FROM sai_bien_garantia WHERE sbn='".$row['etiqueta']."' and esta_id<>'53'";
		  		$resultado_transito=pg_query($conexion,$reporte_transito);
		  		if($row_transito=pg_fetch_array($resultado_transito)){?>
		  <td bordercolor="1"><div align="right"><span class="normal"><b>Reportado</b></span></div></td>
		  		<?php }else{?>
		  <td bordercolor="1"><div align="right"><span class="normal"><b><a href="../garantia/ingresar_caso.php?sbn=<?php echo $row['etiqueta'];?>&serial=<?php echo $row['serial'];?>&clave_bien=<?php echo $row['clave_bien'];?>">Reportar caso</a></b></span></div></td>
		  <?php }?>
		  </tr>
		  <?php 
		      if ($dias_restantes==0){
		      	$query="UPDATE sai_biin_items SET garantia_vencida=1 WHERE clave_bien='".$row['clave_bien']."'";
		      	$resultado_acta=pg_query($conexion,$query);
		      }
		      $j++;
		 }?>
    </table></td>
  </tr>
  <?php //}?>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("H:i:s")?>
<br /><br /><br />
<a href="javascript:window.print()"><img src="../../../imagenes/boton_imprimir.gif"></a> <br><br>
<span class="link">Imprimir Documento</span> </span><br />
<br /><br /></div></td>
  </tr>
</table>
<?php } else{?>
<div align="center" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</div>
<?php }?>
</form><?php }?>


</body>
</html>
