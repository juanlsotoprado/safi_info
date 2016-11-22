<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../../init.php');
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../../index.php',false);
   ob_end_flush();    exit;
  }
  ob_end_flush();
  
  $arrayalmid=$_POST['arrayalm_id'];
  $total=$_POST['total'];
  $key=$_POST['key'];
  
  $fec =$_POST['fecha'];
  $fecha = explode ('/',$fec);
  $fecha2 = $fecha[2].''.$fecha[1].''.$fecha[0].' '.strftime('%H:%M:%S');
  
  
  $arraymodi = $_POST['arraymodi'];
  $nuevos =$arraymodi['alm_id'];
  $nuevos = array_values(array_diff($nuevos, array('')));

  $queryborrar =
  "
	delete from
		sai_arti_salida
	where
		alm_id NOT IN (".implode(", ",$nuevos).") and
		n_acta = '".$key."'
";
  $resultado = pg_query($conexion,$queryborrar);
	//echo $queryborrar;
	
	
  //echo "<pre>";echo print_r($_POST);echo "</pre>";
	
  $sqldatosgenerales="
		UPDATE
			sai_arti_acta_almacen
		SET	
			depe_entregada ='".$_POST['dependenciaid']."',
			entregado_a = ".$_POST['cedula'].",
			fecha_acta = '".$fecha2."',
			observaciones = '".$_POST['observaciones']."'
		WHERE
			amat_id='".$key."'";
  
  pg_query($conexion,$sqldatosgenerales);
  //echo $sqldatosgenerales;
  for($t=0;$t<$total;$t++){
  	if($arraymodi[alm_id][$t]!=""){
  		$disponible=
  		"
  			select
  				cantidad
  			from
  				sai_arti_salida
  			where
  				alm_id=".$arraymodi['alm_id'][$t]."
  				and n_acta = '".$key."'
  		";
  		$res = pg_query($conexion,$disponible);
  		$row = pg_fetch_array ( $res );  		
  		$dispo = $row['cantidad'] - $arraymodi[cantidad][$t];
  		$disponible2="
  			select
  				disponible
  			from
  				sai_arti_almacen
  			where
  				alm_id=".$arraymodi['alm_id'][$t]."
  			";
  		$res2 = pg_query($conexion,$disponible2);
  		$row2 = pg_fetch_array ( $res2 );
  		$dispo2 = $row2['disponible'] + $dispo; 
  		$updatedisponible=
  		"
  		UPDATE
			sai_arti_almacen
		SET
			disponible =".$dispo2."
		WHERE
    		alm_id=".$arraymodi['alm_id'][$t]."
  		";
  		pg_query($conexion,$updatedisponible);
  		//echo $updatedisponible; 
  		$sqlarticulos="
		UPDATE
			sai_arti_salida
		SET
			arti_id=".$arraymodi[idarticulo][$t].",
			medida='".$arraymodi[unidad][$t]."',
			cantidad=".$arraymodi[cantidad][$t].",
			precio = ".$arraymodi[precio][$t]."
		WHERE
			n_acta='".$key."' and
    		alm_id=".$arraymodi['alm_id'][$t]."
			";
  	}
  	else
  	{
  		$sqlarticulos=
  		"
		INSERT INTO
			sai_arti_salida(alm_id,n_acta,arti_id,cantidad,precio,medida)
		VALUES (".$_POST['arrayalm_id'][$t].",'".$key."',".$arraymodi[idarticulo][$t].",".$arraymodi[cantidad][$t].",".$arraymodi[precio][$t].",'".$arraymodi[unidad][$t]."')
	";
  	}
  	//echo $sqlarticulos;
  	//echo $sqldatosgenerales;
  	pg_query($conexion,$sqlarticulos);
  }
  
  
  $valido=$resultado;
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Modificar Inventario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<br /><br />
<?php 
if ($valido){ ?>
<table width="695" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="15" colspan="5" valign="middle" class="normalNegroNegrita">Modificacion de Acta N&deg; <?=$key;?></td>
  </tr>
  <tr>
	<td height="11" colspan="5"></td>
  </tr>
  <tr>
  	<td valign="middle" class="normalNegrita" style="padding-left: 10px;">Dependencia: <?=$_POST['dependencia'];?></td>
  	<td width="400" valign="middle" class="normalNegrita">Entregado a: <?=$_POST['entregadoa'];?></td>
  </tr>
  <tr>
  	<td valign="middle" class="normalNegrita" style="padding-left: 10px;">Fecha: <?=$_POST['fecha'];?></td>
  	<td valign="middle" class="normalNegrita">Observaciones: <?=$_POST['observaciones'];?></td>
  </tr>
  <tr>
  	<td width="500" valign="middle" class="normalNegrita" style="padding: 10px;"> </td>
  </tr>

  <tr>
	<td height="48" colspan="5"><table width="682" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
  <tr>
    <td width="37" style="background-color:#C3ECCC;"><div align="center"><span class="normalNegrita">#</span></div></td>
    <td width="55" height="15" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
    <td width="200" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Nombre</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Cantidad</div></td>
    <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Precio</div></td>
  </tr>
  <?php
	for ($i=0; $i< $total; $i++){	?>
  <tr>
    <td <?php echo $fondo_str;?>><div align="center" class="normal"><?php echo $i+1;?></div></td>
     <td><div align="center" class="normal" ><?php echo $arraymodi[idarticulo][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[articulo][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[cantidad][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arraymodi[precio][$i];?></div></td>
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
     <a target="_blank" class="normal" href="entradas_pdf.php?id=<?=$key;?>">
     <img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
     <br><br>
     <a href="javascript:window.print()" class="link"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></a></div><br>
	
	<br /><br /><br /><br><br></div>	</td>
	</tr>
</table>	<br />
<?php
 } /*Del Valido*/
?>
</body>
</html>

