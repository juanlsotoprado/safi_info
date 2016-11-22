<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../init.php');
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush();    exit;
  }
  ob_end_flush();
  
  $arraysecid=$_POST['arraysec_id'];
  $total=$_POST['total'];
  $key=$_POST['key'];
  $fec=trim($_POST['hid_desde_itin']);
  if($fec){
  	$fecha = explode ('/',$fec);
  	$fecha2  =  $fecha[2].''.$fecha[1].''.$fecha[0].' '.strftime('%H:%M:%S');
  
  }
  $ubicacion=$_POST['txt_ubica'];
  $monto=$_POST['monto_recibido'];
  $prov=$_POST['proveedor'];
  $observaciones=$_POST['observaciones'];
  
  $arraymodi = $_POST['arraymodi'];
  $nuevos =$arraymodi['sec_id'];
  $nuevos = array_values(array_diff($nuevos, array('')));
  
  /*
   echo "<pre>";
  echo print_r($arraysecid);
  echo "</pre>";
  
  echo "<pre>";
  echo print_r($arraymodi['sec_id']);
  echo "</pre>";
  
  echo "<pre>";
  echo print_r($_POST);
  echo "</pre>";
  */
  
  $queryborrar =
  "
	delete from
		sai_arti_inco_rs_item
	where
		sec_id NOT IN (".implode(", ",$nuevos).") and
		acta_id = '".$key."'
";
  
  //echo $queryborrar;
  pg_query($conexion,$queryborrar);
  
  
  
  if($ubicacion==1){
  	$txtubicacion="Torre";
  }else
  {
  	$txtubicacion="Galp&oacute;n";
  }
  
  
  /*
   require_once("../../includes/arreglos_pg.php");
  $arreglo_id = convierte_arreglo ($arraymodi["idarticulo"]);
  $arreglo_cantidad = convierte_arreglo ($arraymodi);
  $arreglo_marca = convierte_arreglo ($arraymodi);
  $arreglo_modelo = convierte_arreglo ($arraymodi);
  $arreglo_serial=convierte_arreglo ($arraymodi);
  $arreglo_fecha=convierte_arreglo ($arraymodi);
  */
  $sqlproveedor="select prov_id_rif from sai_proveedor_nuevo where prov_nombre='".$prov."'";
  $resultado=pg_query($conexion,$sqlproveedor);
  $row = pg_fetch_array($resultado);
  $provid=$row['prov_id_rif'];
  $sqldatosgenerales="
		UPDATE
			sai_arti_inco_rs
		SET
			fecha_registro='".$fecha2."',
			proveedor='".$provid."',
			observaciones='".$observaciones."',
			monto_recibido=".$monto."
		
		WHERE
			acta_id='".$key."'";
  
  pg_query($conexion,$sqldatosgenerales);
  
  //echo $sqldatosgenerales;
  for($t=0;$t<$total;$t++){
  	if($arraymodi[sec_id][$t]!=""){
  		$sqlarticulos="
		UPDATE
			sai_arti_inco_rs_item
		SET
			fecha_recepcion='".$fecha2."',
			arti_id=".$arraymodi[idarticulo][$t].",
			cantidad=".$arraymodi[cantidad][$t].",
			ubicacion=".$ubicacion.",
			marca_id=".$arraymodi[marca_id][$t].",
			modelo='".$arraymodi[modelo][$t]."',
			serial='".$arraymodi[serial][$t]."'
		WHERE
			acta_id='".$key."' and
    		sec_id=".$arraymodi[sec_id][$t]."
			";
  	}
  	else
  	{
  		$sqlarticulos=
  		"
		INSERT INTO
			sai_arti_inco_rs_item(acta_id,fecha_recepcion,arti_id,cantidad,disponible,ubicacion,marca_id,modelo,serial)
		VALUES ('".$key."','".$fecha2."',".$arraymodi[idarticulo][$t].",".$arraymodi[cantidad][$t].",".$arraymodi[cantidad][$t].",".$ubicacion.",".$arraymodi[marca_id][$t].",'".$arraymodi[modelo][$t]."','".$arraymodi[serial][$t]."')
	";
  	}
  	pg_query($conexion,$sqlarticulos);
  }
  
  $valido=$resultado;
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Modificar Inventario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<br /><br />
<?php 
if ($valido){ ?>
<table width="695" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="15" colspan="5" valign="middle" class="normalNegroNegrita">Modificacion de Inventario: Acta N&deg; <?=$key;?></td>
  </tr>
  <tr>
	<td height="11" colspan="5"></td>
  </tr>
  <tr>
  	<td valign="middle" class="normalNegrita" style="padding-left: 10px;">Proveedor:&ensp; <?=$prov;?></td>
  	<td valign="middle" class="normalNegrita">Ubicaci&oacute;n:&ensp; <?=$txtubicacion;?></td>
  </tr>
  <tr>
  	<td width="500" valign="middle" class="normalNegrita" style="padding: 10px;">Observaciones:&ensp; <?=$_POST['observaciones'];?></td>
  	<td valign="middle" class="normalNegrita">Monto recibido:&ensp; <?=$_POST['monto_recibido'];?></td>
  </tr>

  <tr>
	<td height="48" colspan="5"><table width="682" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
  <tr>
    <td width="37" style="background-color:#C3ECCC;"><div align="center"><span class="normalNegrita">#</span></div></td>
    <td width="55" height="15" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
    <td width="200" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Nombre</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Cantidad</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Marca</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Modelo</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Serial</div></td>
    <td width="100" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Fecha recepci&oacute;n</div></td>
  </tr>
  <?php
	for ($i=0; $i< $total; $i++){	?>
  <tr>
    <td <?php echo $fondo_str;?>><div align="center" class="normal"><?php echo $i+1;?></div></td>
     <td><div align="center" class="normal" ><?php echo $arraymodi[idarticulo][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[articulo][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[cantidad][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[marca_nombre][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[modelo][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[serial][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $fec;?></div></td>
  </tr>
      <?php
	
  }	?>
</table>
    </td>
  </tr>
  <tr>
	<td height="85" colspan="5" class="normal">
	<div align="center"><br /><br />
	  Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
	<br>
	<br><br>
     <div align='center'>
     <a target="_blank" class="normal" href="entradas_rs_pdf.php?id=<?=$key;?>">
     <img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
     <br><br>
     <a href="javascript:window.print()" class="link"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></a></div><br>
	
	<br /><br /><br /><br><br></div>	</td>
	</tr>
</table>	<br />
<?php
 } /*Del Valido*/
?>
</body>
</html>
