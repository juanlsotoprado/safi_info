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

$codigo=$_POST['codigo'];
$total=$_POST['total'];
$arraymodi = $_POST['arraymodi'];
/*
echo "<pre>";
echo print_r($arraymodi);
echo "</pre>";
*/

$queryborrar =
"
	delete from
		sai_arti_salida_rs_item
	where
		acta_id = '".$codigo."'
";

require_once("../../includes/arreglos_pg.php");
if (count($arraymodi["idarticulo"]) > 0 && count($arraymodi["cantidad"]) > 0 && count($arraymodi["unidad"]) > 0 && count($arraymodi["modelo"]) > 0 && count($arraymodi["marca_id"]) > 0){
	$arreglo_id = convierte_arreglo ($arraymodi["idarticulo"]);
	$arreglo_cantidad = convierte_arreglo ($arraymodi["cantidad"]);
	$arreglo_medida   = convierte_arreglo ($arraymodi["unidad"]);
	$arreglo_modelo   = convierte_arreglo ($arraymodi["modelo"]);
	//marca
	$arreglo_idmarca   = convierte_arreglo ($arraymodi["marca_id"]);
	//serial
	$arreglo_serial   = convierte_arreglo ($arraymodi["serial"]);
	//ubicacion
	$arreglo_ubicacion   = convierte_arreglo ($arraymodi["ubicacion"]);
}else{
	$arreglo_id = "{}";
	$arreglo_cantidad = "{}";
	$arreglo_medida   = "{}";
	$arreglo_modelo   = "{}";
	$arreglo_idmarca   = "{}";
	$arreglo_serial = "{}";
	$arreglo_ubicacion = "{}";
}

/*if ($_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA)
 $ubicacion="2";
else
	$ubicacion="1";*/

$sql="Select * From sai_modi_asignacion_rs(
			 '".$codigo."',
			 '".$_SESSION['user_depe_id']."',
			 '".$_SESSION['login']."',
			 '".addslashes(addslashes($_POST['destino']))."',
			 '".$arreglo_id."',
			 '".$arreglo_cantidad."',
			 '".$arreglo_medida."',
		 	 '".$arreglo_ubicacion."',
	  		 '".$arreglo_modelo."',
	    	 '".$arreglo_idmarca."',
	    	 '".$arreglo_serial."'
			 ) as codigo";

//echo $sql;

$resultado_set = pg_exec($conexion ,$sql);

if($resultado_set === false){
	echo utf8_decode("Error al generar la nota de entrega ");
	error_log(pg_last_error($conexion));
	exit;
}

/*
echo "<pre>";
echo print_r($_POST);
echo "</pre>";
*/
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php if ($codigo<>"0"){?>
<form action="" name="form" id="form" method="post" >
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via" style="padding-bottom: 10px;">
   <tr>
      <td height="15" colspan="5" valign="middle" class="td_gray"><span class="normalNegroNegrita"> Nota de entrega responsabilidad social</span> </td>
   </tr>
   	<tr>
	  <td class="normal"><strong>N&deg; nota de entrega:</strong></td>
	  <td class="normalNegro"><b><?php echo($codigo); ?></b></td>
	</tr>
	  <tr>
	  <td class="normal"><strong>Fecha:</strong></td>
	  <td class="normalNegro"><?php echo(date('d/m/Y')); ?></td>
	  </tr>
  	<tr>
	  <td class="normal"><strong>Elaborado por:</strong></td>
	  <td class="normalNegro"><?php echo($_SESSION['solicitante']); ?></td>
	  </tr>
	<tr>
	  <td class="normal"><strong>Destino:</strong></td>
	  <td class="normalNegro"><?php echo($_POST['destino']); ?></td>
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
       $sql_salida="SELECT * 
       FROM sai_arti_salida_rs t1,sai_arti_salida_rs_item t2,sai_item t3, sai_bien_marca t4
       WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and id=arti_id and t2.marca_id=t4.bmarc_id";
       $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
       
      // echo $sql_salida;
       
	   while($rowd=pg_fetch_array($resultado_salida)) 
	   {
	   	if($rowd['ubicacion']==1){
	   		$ubica="Torre";
	   	}elseif($rowd['ubicacion']==2){
	   		$ubica="Galpón";
	   	}  
        echo("<tr><td class='normalNegro'>".$rowd['nombre']."</td><td class='normalNegro'>".$rowd['cantidad']."</td><td class='normalNegro'>".$rowd['bmarc_nombre']."</td><td class='normalNegro'>".$rowd['modelo']."</td><td class='normalNegro'	>".$rowd['serial']."</td><td class='normalNegro'	>".utf8_decode($ubica))."</td></tr>";  
	   }?>
  </table>
  </td>
  </tr>
	</table>
  <br><br>
  <div align='center'><a target="_blank" class="normal" href="nota_entrega_pdf.php?id=<?=$codigo;?>">Nota de entrega<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div><br>
  	
</form>
<?php } else{?>
<br></br><div  style="color:#FF0000;" align="center"><b>No se puede emitir la nota de entrega </b>
<br><br>Enviar esta informacion al Departamento de Sistemas: <?php echo $sql;?></br></div>
<?php }?>
</body>
</html>

			
<?php pg_close($conexion);?>
