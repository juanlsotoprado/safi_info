<?php 
  ob_start();
  session_start();
  require_once("../../../../includes/conexion.php");
  require("../../../../includes/fechas.php");
  include (dirname ( __FILE__ ) . '/../../../../init.php');
  require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');//ConstruirAccesosRapidosFechas
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SAFI::Inventario de Materiales Existente</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link href="../../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript" src="../../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../../js/lib/jquery/plugins/ui.min.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="javascript">
var radioinput = 2;
 
$().ready(function(){
	$("input[type='radio']").click(function() {
		  radioinput = $("input:checked").val();
	});
});
 
function ejecutar(codigo1,codigo2)
{
	if(document.form1.hid_hasta_itin.value=='')
	{
		alert(" Debe indicar la fecha");
	return;
	}
	if(document.form1.txt_inicio.value=='')
	{
		alert(" Debe indicar la fecha");
	return;
	}   

  var sw=0;

  for(i=0;i<2;i++){
   if(document.form1.hid_buscar[i].checked==true)
   {
	sw=1;
   }
  }

  if(sw==0){
   alert(" Debe seleccionar el tipo de totales para el reporte. ");
   return;
  }
	 
  if(document.form1.hid_buscar[0].checked==true){
   document.form1.hid_buscar.value=1;
  }
  else{
 	   document.form1.hid_buscar.value=2;
	  }

  document.form1.hid_hasta_itin.value=codigo1;
  window.location="inventario_actualizado.php?hid_hasta_itin="+codigo1+"&txt_inicio="+codigo2+"&hid_buscar="+radioinput;
}
</script>

</head>
<body>
<form name="form1" action="" method="post">
<br />
<table width="500" align="center" background="../../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray" > 
    <td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Inventario</td>
  </tr>
  	<tr>
		<td height="5" colspan="3" align="right">
		  <!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
			<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
		</td>	
	</tr>
  <tr>
    <td width="259" height="34" align="left" class="normalNegrita" style="padding-left: 15px;">Fecha de corte:</td>
    <td width="406" class="normalNegrita">
		<div align="left">
			<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" readonly />
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="-">
				<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
			<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" readonly />
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="-">
				<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
			</a>
		</div>
	</td>
  </tr>
  <tr>
  	<td width="259" height="34" align="left" class="normalNegrita" style="padding-left: 15px;">Totales por:</td>
	<td width="406" class="normalNegrita"><input type="radio" name="hid_buscar" value="1">Precio</input>&nbsp;<input type="radio" name="hid_buscar" value="2" checked>Cantidad</input>
	</td></tr>
  <tr>
    <td height="52" colspan="3" align="center">
	 <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar(document.form1.hid_hasta_itin.value,document.form1.txt_inicio.value);"></input>
	   <div align="right" class="peqNegrita"><a href="toma_inventario_pdf.php">
   <img src="../../../../imagenes/pdf_ico.jpg" width="32" height="28" border="0"></a>Toma de inventario</div>
    </td>
  </tr>
</table>
</form>
<br>
<?
// Si hay datos en botones radio
if (($_GET['hid_buscar']==1) || ($_GET['hid_buscar']==2)){?>
<form name="form" action="" method="post">
<?php 
$fecha_fi=trim($_GET['hid_hasta_itin']);
$fecha_in=trim($_GET['txt_inicio']);
$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2)." 23:59:59";
 //Tabla que muestra lista de articulos
 $sql_ar=
 "
 select 
	t1.id,nombre,
	t2.existencia_minima,
	t2.unidad_medida,
	t3.part_id
from
	sai_item t1,
	sai_item_articulo t2,
	sai_item_partida t3,
	sai_arti_almacen t4
Where 
	t1.id = t2.id and 
	t1.esta_id = 1 and 
	t3.id_item = t2.id and 
	t1.id = t3.id_item and
	t1.id = t4.arti_id and
	(t4.alm_fecha_recepcion >='".$fecha_ini."' and t4.alm_fecha_recepcion <= '".$fecha_fin."')
order by 
	t1.nombre asc
 ";
/*
 $sql_ar=
"SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2,sai_item_partida t3','t1.id,nombre,existencia_minima,unidad_medida,part_id',
't1.id=t2.id and esta_id=1 and id_item=t2.id and t1.id=id_item','nombre',1) resultado_set(id varchar,nombre varchar,existencia_minima int4,unidad_medida varchar,part_id varchar)";
*/ 
$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");
if($row=pg_fetch_array($resultado_set_most_ar))
 {
?>
<table width="651" align="center" background="../../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<div align="center"><span class="normalNegroNegrita">Inventario a partir <?echo(trim($_GET['txt_inicio'])); ?> hasta <?echo(trim($_GET['hid_hasta_itin'])); ?>
	<script language="javascript">
     var buscar="<? echo $_GET['hid_buscar'] ?>";
     if(buscar==1){
	  document.form1.hid_buscar[0].checked=true
     }
     else{
	     document.form1.hid_buscar[1].checked=true
	 }
    docment.form1.hid_hasta_itin.value="<? echo $_GET['hid_hasta_itin']; ?>";
    </script></span></div>
 <tr>
   <td colspan="5"><table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
 <tr class="td_gray">
   <td width="107" height="25" align="center" class="normalNegroNegrita">C&oacute;digo</td>
   <td width="107" height="25" align="center" class="normalNegroNegrita">Partida</td>
   <td width="321" align="center" class="normalNegroNegrita">Art&iacute;culo</td>
   <td width="321" align="center" class="normalNegroNegrita">Unidad de medida</td>
   <td width="70" align="center"class="normalNegroNegrita">Existencia</td>
   <?if (($_GET['hid_buscar'])==2){?>
   <td width="78" align="center" class="normalNegroNegrita">Galp&oacute;n al <?= date("d/m/y");?></td>
   <td width="70" align="center" class="normalNegroNegrita">Torre al <?= date("d/m/y");?></td>
 <?} if (($_GET['hid_buscar'])==1){?>
   <td width="70" align="center" class="normalNegroNegrita">Costo unitario en Bs.</td>
   <td width="70" align="center" class="normalNegroNegrita">Monto total en Bs. </td><?}?>
 </tr>
 <?php
   $i=0;
   if (($_GET['hid_buscar'])==1)
   {
	$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
    while($row=pg_fetch_array($resultado_set_most_ar)) 
	{	
	 $i++;		
	 $arti=$row['id'];
	 $total=0;
	 $precio=0;
	 $monto_total=0;

	 //Entradas
	$sql_e="
 		SELECT
			sum(cantidad) as entrada,
			precio,
			alm_id
		FROM
			sai_arti_almacen
			LEFT JOIN sai_arti_inco ON (sai_arti_inco.acta_id = sai_arti_almacen.acta_id)
		WHERE
			alm_fecha_recepcion <='".$fecha_fin."' AND 
			arti_id='".$arti."'
			AND
			(
			sai_arti_inco.esta_id <> 15
			OR sai_arti_inco.esta_id IS NULL
			)
		GROUP BY
			arti_id,
			precio,
			alm_id
 		";

    $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos");  
	while($rowe=pg_fetch_array($resultado_entrada)) 
  	{
  		$num_entradas=$rowe['entrada'];
  		$id_almacen=$rowe['alm_id'];
		$precio=$rowe['precio'];
  	  
  	  
	 	$num_salidas=0;
	  	$num_devoluciones=0;
  	
  	//SE LE SUMAN LAS DEVOLUCIONES Y SE RESTAN LAS SALIDAS HASTA LA FECHA, 
  		$sql_salidas=
  		"
		select 
			sum(cantidad) as canti_salida,
			tipo from sai_arti_salida,
			sai_arti_acta_almacen 
		where 
			n_acta=amat_id and 
			fecha_acta<'".$fecha_fin."' and 
			arti_id='".$arti."' and 
			esta_id<>15 and 
			alm_id='".$id_almacen."' 
		group 
			by tipo
		";
  		
  		$num_salidas=0;
  		$num_devoluciones=0;
  	
  		$resultado_salidas=pg_query($conexion,$sql_salidas) or die("Error al consultar salida de articulos5");
  	while($rowsal=pg_fetch_array($resultado_salidas)) 
  	{	
  		if ($rowsal['tipo']=='S'){
  		  $num_salidas=$rowsal['canti_salida'];	
  		}else{
   			  $num_devoluciones=$rowsal['canti_salida'];
  		}
    	 
  	}
  	
   	$inventario_fin=$num_entradas+$num_devoluciones-$num_salidas;
	$row['nombre'];
	?>
	<script>
	  codigo = <?php echo $arti;?>
	  descripcion = <?php echo $row['nombre'];?>
 	  document.form.hid_hasta_itin.value="<? echo $fecha_fi ?>";
    </script>
	
	<?php if ($inventario_fin>0){
	  $f_inicio='01/01/2009';
	?>
 <tr>
   <td height="21" bordercolor="1"><div align="right" class="normal"><?php echo $arti;?></div></td>
      <td bordercolor="1" ><div><span class="normal"><?php echo $row['part_id'];?></span></div></td>
    <script language="javascript">
 	  var arti="<? echo $arti ?>";
  	</script>																																   
    <div align="left" class="normal"><td bordercolor="1" class="normal"><a href="movimiento_articulo.php?des_articulo=<?php echo $row['nombre'];?>&hid_articulo=2&txt_inicio=<?php echo $f_inicio;?>&hid_hasta_itin=<?php echo $fecha_fi;?>&hid_buscar=1">
     <?php echo $row['nombre'];?></a></td></div>
   <td bordercolor="1" ><div align="right"><span class="normal"><?php echo $row['unidad_medida'];?></span></div></td>
   <td bordercolor="1"><div align="right"><span class="normal"><?php echo $inventario_fin;?> </span></div></td>
   <td bordercolor="1"><div align="right"><span class="normal"><?php echo str_replace('.',',',$precio);?></span></div></td>
     <?$monto_total=$precio*$inventario_fin;?>
   <td bordercolor="1"><div align="right"><span class="normal"><?php echo (number_format($monto_total,2,',','.'));?></span></div></td>
 </tr>
 <?php }}	
      }
   }
   else 
       if (($_GET['hid_buscar'])==2)
       {
		$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
		while($row=pg_fetch_array($resultado_set_most_ar)) 
		{	
	 	 $i++;		
	 	 $arti=$row['id'];
	 	 $total=0;
	 	 $devolucion=0;
	 	 $salida=0;
	 	 $entrada=0;
	 	 $precio_en=0;
     	 $total_entrada=0;

     	 // Entradas
		$sql_e="
			SELECT
				sum(cantidad) as entrada,
				precio
			FROM
				sai_arti_almacen
				LEFT JOIN sai_arti_inco ON (sai_arti_inco.acta_id = sai_arti_almacen.acta_id)
			WHERE
				alm_fecha_recepcion <='".$fecha_fin."'
				AND arti_id='".$arti."'
				AND
				(
				sai_arti_inco.esta_id <> 15
				OR sai_arti_inco.esta_id IS NULL
				)
			GROUP BY
				arti_id,
				precio
		";
		
		     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos");  
			 while($rowe=pg_fetch_array($resultado_entrada)) 
	  		 {	
	    	  $entrada=$rowe['entrada'];
		  	  $precio_en=$rowcan['precio'];
		 	  $total_entrada=$total_entrada+$entrada;
			 }
		 
		 	$sql_s=
		 	"
			select 
				sum (cantidad) as cantidad,
				tipo from sai_arti_salida t1, 
				sai_arti_acta_almacen
			where
		 		amat_id = n_acta and 
				esta_id <> 15 and
				arti_id = '".$arti."' and
				fecha_acta <= '".$fecha_fin."'
			group by 
				tipo";

		 $resultado_salida=pg_query($conexion,$sql_s) or die("Error al consultar la salida de los articulos");  
		 while($rows=pg_fetch_array($resultado_salida)) 
  		 {	
	 	  if ($rows['tipo']=='S'){
    	   $salida=$rows['cantidad'];
	 	  }else{
			    $devolucion=$rows['cantidad'];
	           }
  	     }
  	       	     
		 $total=$total_entrada+$devolucion-$salida;

		  $f_inicio='01/01/2009';
		 ?>
  <tr>
    <td height="21" bordercolor="1"><div align="center" class="normal"><?php echo $arti;?></div></td>
    <td bordercolor="1" ><div ><span class="normal"><?php echo $row['part_id'];?></span></div></td>
      <div align="right" class="normal"><td bordercolor="1"  class="normal"><a href="movimiento_articulo.php?des_articulo=<?php echo $row['nombre'];?>&hid_articulo=2&txt_inicio=<?php echo $f_inicio;?>&hid_hasta_itin=<?php echo $fecha_fi;?>&hid_buscar=1"><?php echo $row['nombre'];?></a></td></div>
    <td bordercolor="1"><div align="right"><span class="normal"><?php echo $row['unidad_medida'];?></span></div></td>
    <td bordercolor="1"><div align="right"><span class="normal"><?php if ($total>=0){?><?php echo $total;}else {?><?php echo '0'; }?> </span></div></td>
    
    
<?php    
$cantt=0;
$cantg=0;
$sql_torre="select cantidad from sai_item_distribucion where id='".$arti."' and ubicacion=1"; 

 		  $resultado_torre=pg_query($conexion,$sql_torre);
 		   if($rowt=pg_fetch_array($resultado_torre)) 
  		 {  $cantt=$rowt['cantidad'];	}
  		 
 		  $sql_galpon="select cantidad  from sai_item_distribucion where id='".$arti."' and ubicacion=2";
 		//  echo $sql_galpon."<br>"; 
		  $resultado_galpon=pg_query($conexion,$sql_galpon);
		if($rowg=pg_fetch_array($resultado_galpon)) 
  		 {  $cantg=$rowg['cantidad'];		}
?>
  <td bordercolor="1" <?php echo $fondo_str;?>><div align="right"><span class="normal"><?php echo $cantg;?></span></div></td>
    <td bordercolor="1" <?php echo $fondo_str;?>><div align="right"><span class="normal"><?php echo $cantt; ?> </span></div></td>
 
  </tr>
  <?php
       } }?>
</table></td>
  </tr>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
		 <br /><br /><br />
		<a href="inventario_actualizado_pdf.php?hid_buscar=<?php echo($_GET['hid_buscar']); ?>&hid_hasta_itin=<?php echo ($_GET['hid_hasta_itin']);?>"><img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>
		<br><br />
        <a href="javascript:window.print()" class="normal"><img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br />
        <span class="link">Imprimir Documento</span> </span><br />
      <br /><br /><br />
    </div></td>
  </tr>
</table>
<?php } ?>
</form><?}?>
</body>
</html>
<?php pg_close($conexion);?>
