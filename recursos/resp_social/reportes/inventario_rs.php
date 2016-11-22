<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  include (dirname ( __FILE__ ) . '/../../../init.php');
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
<style type="text/css">
.ui-autocomplete {
	max-height: 110px;
	font-size: 12px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 30px;
}
/* IE 6 doesn't support max-height
                        * we use height instead, but this forces the menu to always be this tall
                        */
* html .ui-autocomplete {
	font-size: 10px;
}

.ui-menu-item a {
	font-size: 10px;
}
</style>
<title>SAFI::Inventario de Materiales Existente</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link href="../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
 <?php require("../../../includes/fechas.php");?>
<link type="text/css"
	href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen"
	rel="stylesheet" />
<script type="text/javascript"
	src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript"
	src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript"
	src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script language="javascript">

var idArticulo;

$().ready(function(){

	var items = new Array();
	var index = 0;

	$('#id').attr('autocomplete','off');

	$.each(stringobj,function(id, params){
		items[index++] = {
				id: params.id,
				value: params.id+": "+params.nombre,
				nombre: params.nombre,

		};

	});
	

	$("#articulo" ).autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {
	   		idArticulo = ui.item.id;
	   		$("#idArticulo").val(idArticulo);
	    return true;
	            
	    }
	});

});

function imprSelec(impr)
{
	var ficha=document.getElementById(impr);
	var ventimp=window.open(' ','popimpr');
	ventimp.document.write(ficha.innerHTML);
	ventimp.document.close();
	ventimp.print();
	ventimp.close();
}


</script>
</head>
<body>

<?php /* 

<br>
<form name="form" action="" method="post">

<?php 
 //Tabla que muestra lista de articulos
$sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,unidad_medida',
't1.id=t2.id and esta_id=1 and t1.id_tipo=4','nombre',1) resultado_set(id varchar,nombre varchar,unidad_medida varchar)"; 
$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
if($row=pg_fetch_array($resultado_set_most_ar))
 {
?>
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<div align="center"><span class="normalNegroNegrita">Inventario de responsabilidad social a la fecha 
    <?echo date('d/m/Y'); ?>
	</span></div>
 <tr>
   <td colspan="5"><table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
 <tr class="td_gray">
   <td width="107" height="25" align="center" class="normalNegroNegrita">C&oacute;digo</td>
   <td width="321" align="center" class="normalNegroNegrita">Art&iacute;culo</td>
   <td width="321" align="center" class="normalNegroNegrita">Unidad de medida</td>
   <td width="70" align="center"class="normalNegroNegrita">Existencia</td>
   <td width="78" align="center" class="normalNegroNegrita">Galp&oacute;n</td>
   <td width="70" align="center" class="normalNegroNegrita">Torre</td>
 </tr>
 <?php
   $i=0;
   $fecha_fi=date('d/m/Y'); 
   $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
  
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

     	 $sql_e="select sum(cantidad) as entrada from 
     	 sai_arti_inco_rs_item where fecha_recepcion <='".$fecha_fin."' and arti_id='".$arti."' group by arti_id";
	     $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos");  
		 while($rowe=pg_fetch_array($resultado_entrada)) 
  		 {	
    	  $entrada=$rowe['entrada'];
	 	  $total_entrada=$total_entrada+$entrada;
		 }
		 
		 $sql_s="select sum (cantidad) as cantidad from sai_arti_salida_rs t1, sai_arti_salida_rs_item t2 where
		 t1.acta_id=t2.acta_id and esta_id<>15 and arti_id='".$arti."' and fecha_acta <= '".$fecha_fin."'  group by arti_id";
		 $resultado_salida=pg_query($conexion,$sql_s) or die("Error al consultar la salida de los articulos");  
		 if($rows=pg_fetch_array($resultado_salida)) 
  		 {	
	 	  $salida=$rows['cantidad'];
  	     }

		 $total=$total_entrada-$salida;

		 ?>
  <tr>
    <td height="21" bordercolor="1"><div align="center" class="normal"><?php echo $arti;?></div></td>
      <div align="right" class="normal"><td bordercolor="1"  class="normal"><?php echo $row['nombre'];?></td></div>
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
       } ?>
</table></td>
  </tr>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
		<!-- <a href="inventario_actualizado_pdf.php?hid_buscar=<?php echo($_GET['hid_buscar']); ?>&hid_hasta_itin=<?php echo ($_GET['hid_hasta_itin']);?>"><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a> -->
		<br><br />
        <a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br />
        <span class="link">Imprimir Documento</span> </span><br />
      <br /><br /><br />
    </div></td>
  </tr>
</table>
<?php }else{?>
<div align="center" class="normalNegrita">No existe informaci&oacute;n registrada</div>
<?php }?>
</form>
*/ 
$queryarticulo = "
 SELECT
 	id,
 	nombre
 FROM
 	sai_item
 WHERE
 	LENGTH(id)>3
 	and id>1685
 ORDER BY
 	id";
 
 $resultadoarticulo = pg_exec ( $conexion, $queryarticulo );
 $indice = 0;
 $stringobj;
 while ( $row = pg_fetch_array ( $resultadoarticulo ) ) {
 	 
 	$stringobj [$indice] ['id'] = $row ['id'];
 	$stringobj [$indice] ['nombre'] = utf8_encode ( $row ['nombre'] );
  
 	$indice ++;
 }
 ?>
 <script>
 var stringobj = <?php echo json_encode($stringobj,JSON_FORCE_OBJECT); ?>;

//alert(JSON.stringify(stringobj));
 </script>
 

<!-- nuevo inventario -->
	<!-- <form name="form2" action="inventario_rs.php" method="post">	
	<table align="center" border="1">
		<tr>
			<td><a id="general">General</a><a id="torre">Torre</a><a id="galpon">Galp&oacute;n</a>
			<input type="hidden" id="valor" /></td>
			<td><input type="submit" value="Buscar" class="normalNegro" /></td>
		</tr>
</table>
</form>-->
<br/>
	<form action="inventario_rs.php" method="post">
		<table width="300" align="center" class="tablaalertas fondoPantalla">
			<tr>
				<td height="21" colspan="2" class="normalNegroNegrita header" align="left" >Buscar inventario</td>
			</tr>
			<tr>
				<td class="normalNegrita" width="90">&emsp;Ubicaci&oacute;n:</td>
				<td class="normal"><select name="txt_ubica" id="txt_ubica"
					class="normalNegro">
						<option value="0" selected>Seleccione...</option>
						<option value="1">Torre</option>
						<option value="2">Galp&oacute;n</option>
				</select></td>
			</tr>
			<tr>
				<td class="normalNegrita">&emsp;Art&iacute;culo:</td>
				<td class="normal"><input type="text" id="articulo" />
				<input type="hidden" name="idArticulo" id="idArticulo" />
				</td>
			</tr>
			<tr>
				<td class="normalNegrita">&emsp;Serial:</td>
				<td class="normal"><input type="text" name="serial" id="serial" /></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="hidden" name="validar"
					value="1" /> <input type="submit" name="enviar" value="Buscar" /></td>
			</tr>
		</table>
	</form>

<?php
$validar=$_POST['validar'];
$ubicacion=$_POST['txt_ubica'];
$serial=$_POST['serial'];
$articulo=$_POST['idArticulo'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
//echo $articulo;

if($ubicacion==1)
{
	$seccion1="and t3.ubicacion=$ubicacion";
	$seccion2="and t5.ubicacion=$ubicacion";
}
if($ubicacion==2)
{
	$seccion1="and t3.ubicacion=$ubicacion";
	$seccion2="and t5.ubicacion=$ubicacion";
}
if($ubicacion==0)
{
	$seccion1=" ";
	$seccion2=" ";
}


if($serial!='')
{
		$queryserial="where tablas.serial='$serial'";
		if($articulo!=''){
			$queryArticulo="and tablas.arti_id=$articulo";
		}
}
else
{
	if($articulo!=''){
		$queryArticulo="where tablas.arti_id=$articulo";
	}
}


 //Tabla que muestra lista de articulos
 if($validar!=''){
$query=
"
  						SELECT
							tablas.id,
							tablas.nombre,
							tablas.modelo,
							tablas.bmarc_id,
							sum(tablas.cantidad) as disponible,
							tablas.bmarc_nombre,
							tablas.unidad_medida,
							tablas.arti_id,
							tablas.serial
  							
			
						FROM
						(
								SELECT
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t3.modelo,
								t4.bmarc_id,
								sum(t3.cantidad) as cantidad,
								t4.bmarc_nombre,
								t3.arti_id,
								t3.serial
					
								FROM
								sai_item  t1
								inner join sai_item_articulo t2 on(t1.id=t2.id)
								inner join sai_arti_inco_rs_item t3 on(t1.id=t3.arti_id)
								inner join sai_bien_marca t4 on(t3.marca_id=t4.bmarc_id)
								inner join sai_arti_inco_rs t7 on(t3.acta_id=t7.acta_id)
					
								WHERE
								t7.esta_id!=15 and
								t1.esta_id=1 and
								id_tipo=4 
  								" . $seccion1 . "

								group BY
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t3.modelo,
								t4.bmarc_id,
								t4.bmarc_nombre,
								t3.arti_id,
								t3.serial
			
						UNION
							SELECT
								t1.id,
								t1.nombre,
								t2.unidad_medida,
								t5.modelo,
								t4.bmarc_id,
								sum(-t5.cantidad) as cantidad,
								t4.bmarc_nombre,
								t5.arti_id,
								t5.serial 

			
							FROM
								sai_item  t1
								inner join sai_item_articulo t2 on(t1.id=t2.id)
								inner join sai_arti_salida_rs_item t5 on(t1.id=t5.arti_id)
								inner join sai_bien_marca t4 on(t5.marca_id=t4.bmarc_id)
								left join sai_arti_salida_rs t6 on(t5.acta_id=t6.acta_id)

							WHERE
								(t6.esta_id IS NULL or t6.esta_id!=15) and
								t1.esta_id=1 and
								id_tipo=4 
  								" . $seccion2 . "
   								


						GROUP BY
							t1.id,
							t1.nombre,
							t2.unidad_medida,
							t5.modelo,
							t4.bmarc_id,
							t4.bmarc_nombre, 
							t5.arti_id,
							t5.serial
						) AS tablas

 							" . $queryserial . "
 							" . $queryArticulo . "
 								
			
					     group by 
							tablas.nombre, 
							tablas.bmarc_nombre, 
							tablas.id, 
							tablas.modelo, 
							tablas.bmarc_id, 
							tablas.unidad_medida, 
							tablas.arti_id,
							tablas.serial
						order by 
							tablas.id
   			
   						"; 

//echo $query;
$resultado=pg_query($conexion,$query);
if($resultado === false){
	try
	{
		$preMsg = "Error al consultar lista de articulos";
		echo utf8_decode($preMsg);
		throw new Exception($preMsg . ". Detalle: " . pg_last_error($conexion));
	}
	catch(Exception $e)
	{
		error_log($e, 0);
	}
	exit;
}
if(pg_num_rows ( $resultado ) > 0)
 {

?>
<br />
<div id="imprimir">
	<table align="center" background="../../../imagenes/fondo_tabla.gif"
		class="tablaalertas">
		<div align="center">
			<?php if($ubicacion==0){?>
			<span class="normalNegroNegrita">Inventario de responsabilidad social
				<strong>(General)</strong> a la fecha 
    		<?echo date('d/m/Y'); ?>
			</span>
			<?php }elseif($ubicacion==1){?>
			<span class="normalNegroNegrita">Inventario de responsabilidad social
				<strong>(Torre)</strong> a la fecha 
    		<?echo date('d/m/Y'); ?>
			</span>
			<?php }elseif($ubicacion==2){?>
			<span class="normalNegroNegrita">Inventario de responsabilidad social
				<strong>(Galp&oacute;n)</strong> a la fecha 
    		<?echo date('d/m/Y'); ?>
			</span>
			<?php }?>
		</div>

		<tr>
			<td colspan="5"><table width="800" border="0" align="center"
					cellpadding="1" cellspacing="1" class="tablaalertas"
					id="factura_head">
					<tr class="td_gray">
						<td width="107" height="25" align="center"
							class="normalNegroNegrita">C&oacute;digo</td>
						<td width="321" align="center" class="normalNegroNegrita">Art&iacute;culo</td>
						<td width="321" align="center" class="normalNegroNegrita">Unidad
							de medida</td>
						<td width="321" align="center" class="normalNegroNegrita">Marca</td>
						<td width="321" align="center" class="normalNegroNegrita">Modelo</td>
						<?php if($serial!=''){?>
						<td width="321" align="center" class="normalNegroNegrita">Serial</td>
						<?php }?>
						<td width="70" align="center" class="normalNegroNegrita">Existencia</td>

					</tr>
 <?php
   $i=0;
   $fecha_fi=date('d/m/Y'); 
   $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
  
		while($row2=pg_fetch_array($resultado)) 
		{	
	 	 	
			/*echo "<pre>";
			echo print_r($row2);
			echo "</pre>";*/
	 	 $arti=$row2['id'];

 

		 ?>
  					<tr>
						<td height="21" bordercolor="1"><div align="center" class="normal"><?php echo $arti;?></div></td>
						<div align="right" class="normal">
							<td bordercolor="1" class="normal"><?php echo $row2['nombre'];?></td>
						</div>
						<td bordercolor="1"><div align="right">
								<span class="normal"><?php echo $row2['unidad_medida'];?></span>
							</div></td>
						<td bordercolor="1"><div align="right">
								<span class="normal"><?php echo $row2['bmarc_nombre'];?></span>
							</div></td>
						<td bordercolor="1"><div align="right">
								<span class="normal"><?php echo $row2['modelo'];?></span>
							</div></td>
						<?php if($serial!=''){?>
						<td bordercolor="1"><div align="right">
								<span class="normal"><?php echo $row2['serial'];?></span>
							</div></td>
						<?php }?>
						<td bordercolor="1"><div align="right">
								<span class="normal"><?php echo $row2['disponible'];?> </span>
							</div></td>


					</tr>
  <?php
       } ?>
</table></td>
		</tr>
		<tr>
			<td height="16" colspan="5" class="normal"><div align="center">
					<br /> <span class="peq_naranja">Detalle generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
		<!-- <a href="inventario_actualizado_pdf.php?hid_buscar=<?php echo($_GET['hid_buscar']); ?>&hid_hasta_itin=<?php echo ($_GET['hid_hasta_itin']);?>"><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a> -->
						<br> <br /> <a href="javascript:imprSelec('imprimir')" class="normal"><img
							src="../../../imagenes/boton_imprimir.gif" width="23" height="20"
							border="0" /></a><br /> <span class="link">Imprimir Documento</span>
					</span><br /> <br /> <br /> <br />
				</div></td>
		</tr>
	</table>
</div>
<?php $i++;	
 	}else{?>
 	<br />
	<div align="center" class="normalNegrita">No existe informaci&oacute;n
		registrada</div>
<?php }

}//fin de validador de variable por post

//error_log("mensaje de prueba de error log");
?>
</body>
</html>

