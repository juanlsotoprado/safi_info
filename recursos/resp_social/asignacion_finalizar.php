<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
include(dirname(__FILE__) . '/../../init.php');
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}


$key = trim(utf8_decode($_REQUEST["ne"]));
	
//query de los detalles

$querydetalle=
"
		SELECT
		distinct(arti_id) as articulos,
		to_char(t3.fecha_acta, 'DD/MM/YYYY') AS fecha_acta,
		t2.ubicacion,
		t3.destino,
		nombre,
		t2.cantidad,
		t5.bmarc_nombre,
		modelo,
		serial
		FROM
		sai_arti_salida_rs t3,
		sai_arti_salida_rs_item t2,
		sai_item t1,
		sai_proveedor_nuevo t4,
		sai_bien_marca t5
		WHERE
		t3.acta_id='".$key."' and
		t3.acta_id=t2.acta_id and
		arti_id=t1.id and
  		t5.bmarc_id=t2.marca_id
		order by
		nombre
		";



if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){

	$indice = 0;
	while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
	{
			
		 
		$stringobj  ['acta'] = $key;
		$stringobj  ['fecha_acta'] = $row['fecha_acta'];
		$stringobj  ['destino'] = utf8_encode($row['destino']);
		if($row ['ubicacion']==1){
			$ubicacionarray = "Torre";
		}else{
			$ubicacionarray = "Galpón";
		}
		$stringobj  ['ubicacion'] = $ubicacionarray;
			
			
			
		$stringobj  ['idarticulo']  [$indice] = $row['articulos'];
		$stringobj  ['nombre'] [$indice] = utf8_encode($row['nombre']);
		$stringobj  ['cantidad'] [$indice] = $row['cantidad'];
		$stringobj  ['marca_id'] [$indice] = utf8_encode($row['bmarc_nombre']);
		$stringobj  ['modelo'] [$indice] = utf8_encode($row['modelo']);
		$stringobj  ['serial'] [$indice] = utf8_encode($row['serial']);
		$indice ++;
			
	}


		

}


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script type="text/javascript">
function verificarinput()
{
	if (document.form.entregado.value == '')
	{
		alert("Indique a quien se le hara la entrega");
		return;		
	}
	if (document.form.hid_desde_itin.value == '')
	{
		alert("Indique la fecha de la entrega");
		return;		
	}
	url = "../../recursos/resp_social/acciones/respsocialsal.php?accion=ProcesarNe&ne=<?php echo $key; ?>&entregado="+document.form.entregado.value+"&fecha="+document.form.hid_desde_itin.value+"&accRealizar=Fin";
	alert("\u00A1Datos ingresados de manera correcta!");
	window.location = url;
}
</script>
</head>
<body>
<?php if ($result){?>
<form action="" name="form" id="form" method="post" >
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via" style="padding-bottom: 10px;" border="0">
   <tr>
      <td height="15" colspan="5" valign="middle" class="td_gray"><span class="normalNegroNegrita"> Nota de entrega responsabilidad social</span> </td>
   </tr>
   	<tr>
	  <td class="normal"><strong>N&deg; nota de entrega:</strong></td>
	  <td class="normalNegro"><b><?php echo($key); ?></b></td>
	</tr>
	  <tr>
	  <td class="normal"><strong>Fecha:</strong></td>
	  <td class="normalNegro"><?php echo($stringobj['fecha_acta']);?></td>
	  </tr>
	<tr>
	  <td class="normal"><strong>Destino:</strong></td>
	  <td class="normalNegro"><?php echo($stringobj['destino']); ?></td>
	  </tr>
	<tr>
	  <td height="5" class="normal" colspan="2"></td>
	  </tr>
  <tr>
    <td colspan="2">
	<table width="99.5%" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" border="0">
	<tr>
	  <td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Nombre</div></td>
    	<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Cantidad</div></td>
   		<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Marca</div></td>
   		<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Modelo</div></td>
   		<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Serial</div></td>
   		<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Ubicaci&oacuten</div></td>
	  </tr>

	  <?php 
	  	for ($i=0;$i<$indice;$i++){
         echo("<tr><td class='normalNegro'>".$stringobj['nombre'][$i]."</td><td class='normalNegro'>".$stringobj['cantidad'][$i]."</td><td class='normalNegro'>".$stringobj['marca_id'] [$i]."</td><td class='normalNegro'>".$stringobj['modelo'][$i]."</td><td class='normalNegro'	>".$stringobj['serial'][$i]."</td><td class='normalNegro'	>".utf8_decode($stringobj['ubicacion']))."</td></tr>";  
	   }
        ?>
        
  </table>
  </td>
  </tr>
  <tr>
  <td ><div align="center" class="normalNegrita">Entregado a:</div></td>
  <td ><div align="left" class="normalNegrita"><input type="text" id = "entregado" /></div></td>
  </tr>
  <tr>
  	<td ><div align="center" class="normalNegrita">Fecha de Entrega:</div></td>
  	<td>
  	<div align="left" class="normalNegrita">
  		<input type="text" size="8" id="hid_desde_itin" name="hid_desde_itin" class="normalNegro" readonly /> 
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Mostrar Calendario" style="display: on" id="fecha"> 
		<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" width="25" height="20" alt="Open popup calendar" />
		</a>
	</div>
  	</td>
  </tr>
  <br><br>
  <tr>
  	<td colspan="2">
  		<center><input type="button" value="Finalizar" onclick="verificarinput()" /></center>
  	</td>
  </tr>
	</table>
  <br><br>
  <!-- <div align='center'><a target="_blank" class="normal" href="nota_entrega_pdf.php?id=<?/*=$key;*/?>">Nota de entrega<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div><br>-->
  	
</form>
<?php } else{?>
<br></br><div  style="color:#FF0000;" align="center"><b>No se puede emitir la nota de entrega </b>
<br><br>Enviar esta informacion al Departamento de Sistemas: <?php echo $sql;?></br></div>
<?php }?>
</body>
</html>

			
<?php pg_close($conexion);?>
