<?php
// sai_insert_reasignacion
ob_start();
require("../../includes/conexion.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<?
$codigo=$_REQUEST['codigo'];
$vector=explode (":" , $_REQUEST['infocentro']);
$id_infocentro=trim($vector[0]);
$nombre_infocentro=trim($vector[1]);
$unidad=$_POST['opt_depe'];
$listado_depe='';

for ($i=0;$i<count($unidad);$i++)    
{     
 $listado_depe=$listado_depe.$unidad[$i]."','";
 
 if ($i==0)
  $listado_solicitante=$unidad[$i];
 else
  $listado_solicitante=$listado_solicitante.",".$unidad[$i];
} 

 $listado_depe="'".$listado_depe."'";

$sql_str="SELECT t1.*,t3.empl_cedula, empl_nombres, empl_apellidos FROM sai_dependenci t1,sai_empleado t3,sai_usuario t2 WHERE 
t3.empl_cedula=t2.empl_cedula and usua_activo=true and t1.depe_id in (".$listado_depe.") and t3.depe_cosige=t1.depe_id and	
t3.esta_id='1' and (carg_fundacion='".$_SESSION['gerente']."' or carg_fundacion='".$_SESSION['consultor']."' or carg_fundacion='".$_SESSION['director']."' 
or carg_fundacion='".$_SESSION['director_ej']."' or carg_fundacion='".$_SESSION['presidente']."') order by empl_nombres";
$res_q=pg_exec($sql_str) or die("Error al mostrar");	  
$i=0;
   while($depe_row=pg_fetch_array($res_q)){ 
	 $depe_nombre[$i]=$depe_row['depe_nombre'];
     $gerente[$i]= $depe_row['empl_nombres']." ".$depe_row['empl_apellidos'];
     $i++;
   }

   $num_firmas=$i;
   $num_bienes=0;

/**********
 * ACTIVOS
***********/
  $activos='';
  $activos=trim($_POST['txt_arreglo_activos']);
  $vector=explode ("�" , $activos);
  $elem_vector=count($vector);
  $activos="'".ereg_replace( "�", "','", $activos )."'";
  
  $x=0;
  $tt=0;
  while ($x< $elem_vector)
	 {	
		$lista_activos[$tt]=trim($vector[$x]);
	    $tt++;
		$x++;
	 } 
	 
		$sql_d="SELECT count(serial) as cantidad,bien_id,nombre FROM sai_biin_items,sai_item WHERE id=bien_id and serial in (".$activos.") group by bien_id,nombre";
	    $resultado_d=pg_query($conexion,$sql_d) or die("Error al consultar totales");
	    $j=0;
		$num_activos=0;
  	
	    while($rowd=pg_fetch_array($resultado_d)) 
	    {
	       
	      $cantidad_bien[$num_activos]=$rowd['cantidad'];
	      $bien_grupo[$num_activos]=$rowd['bien_id'];
	      $bien_nombre[$num_activos]=$rowd['nombre'];
	      
	      $sql_detalle="SELECT * FROM sai_biin_items,sai_bien_marca WHERE bmarc_id=marca_id and serial in (".$activos.") and bien_id='".$rowd['bien_id']."'";
		  $resultado_detalle=pg_query($conexion,$sql_detalle) or die("Error al mostrar");
		 
		   while($rowdetalle=pg_fetch_array($resultado_detalle)) 
	       { 

	       $listado_bienes[$j]=$rowdetalle['bien_id'];
		   $marca_bienes[$j]=$rowdetalle['bmarc_nombre'];
		   $modelo_bienes[$j]=$rowdetalle['modelo'];
		   $serial_bienes[$j]=$rowdetalle['serial'];
		   $listado_bienes[$num_bienes]=$rowdetalle['clave_bien'];
		   $num_bienes++;
		   $j++;
	      }
	      $num_activos++;
	    }
	    $total_detalle=$j;
  
	
/*************
 * MOBILIARIOS
 *************/
  $mobiliarios=trim($_POST['txt_arreglo_mobiliario']);
  $vector=explode ("�" , $mobiliarios);
  $elem_vector=count($vector);
  $mobiliarios="'".ereg_replace( "�", "','", $mobiliarios )."'";
  $x=0;
  $tt=0;
  while ($x< $elem_vector)
	 {	
		$lista_mobiliarios[$tt]=trim($vector[$x]);
	    $tt++;
		$x++;
	 } 
		$sql_d="SELECT count(serial) as cantidad,bien_id,nombre FROM sai_biin_items,sai_item WHERE id=bien_id and etiqueta in (".$mobiliarios.") group by bien_id,nombre";
	    $resultado_d=pg_query($conexion,$sql_d) or die("Error al consultar totales");
	    $j=0;
		$num_muebles=0;
  	
	    while($rowd=pg_fetch_array($resultado_d)) 
	    {
	       
	      $cantidad_muebles[$num_muebles]=$rowd['cantidad'];
	      $muebles_grupo[$num_muebles]=$rowd['bien_id'];
	      $muebles_nombre[$num_muebles]=$rowd['nombre'];
	      $sql_detalle="SELECT * FROM sai_biin_items,sai_bien_marca WHERE bmarc_id=marca_id and etiqueta in (".$mobiliarios.") and bien_id='".$rowd['bien_id']."'";
		  $resultado_detalle=pg_query($conexion,$sql_detalle) or die("Error al mostrar");
		 
		   while($rowdetalle=pg_fetch_array($resultado_detalle)) 
	       { 

	       $listado_muebles[$j]=$rowdetalle['bien_id'];
		   $marca_muebles[$j]=$rowdetalle['bmarc_nombre'];
		   $modelo_muebles[$j]=$rowdetalle['modelo'];
		   $serial_muebles[$j]=$rowdetalle['serial'];
		   $listado_bienes[$num_bienes]=$rowdetalle['clave_bien'];
		   $j++;
		   $num_bienes++;
	      }
	      $num_muebles++;
	    }
	    $total_detalle_muebles=$j;
	
	
	require_once("../../includes/arreglos_pg.php");
	if ($tt_articulos>=1){
	  $arreglo_articulo = convierte_arreglo ($id);
	  $arreglo_cantidad = convierte_arreglo ($cantidad);
	  $arreglo_medida   = convierte_arreglo ($medida);
	}else{
		$arreglo_articulo = "{}";
	    $arreglo_cantidad = "{}";
	    $arreglo_medida   = "{}";
	}
	$arreglo_activo   = convierte_arreglo ($lista_activos);
	$arreglo_mobiliario= convierte_arreglo ($lista_mobiliarios);
	$arreglo_dependencia= convierte_arreglo($unidad);
	if ($num_bienes>0)
	  $arreglo_bienes = convierte_arreglo($listado_bienes);
	else
	  $arreglo_bienes = "{}";


	//SI PASA TODAS LAS VALIDACIONES SE ALMACENA LA SALIDA
	
	$sql="Select * From sai_insert_reasignacion(
			 '".$_SESSION['user_depe_id']."',
			 '".$_SESSION['login']."',
			 '".$_POST['destino']."',
			 '".$id_infocentro."',
			 '".$arreglo_bienes."',
			 '".$listado_solicitante."','".$_POST['ubicacion']."','".$_POST['tipo_r']."') as codigo";
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar los activos, verifique que el número del activo no se encuentre registrado"));
    if ($row=pg_fetch_array($resultado_set))
	{
	  $codigo_clave=explode ("*" , $row['codigo']);
      $codigo=trim($codigo_clave[0]);
      $clave_activos=trim($codigo_clave[1]);//"'".ereg_replace( "*", "','", $row['codigo'] )."'";
      $claves=explode ("/" , $codigo_clave[1]);
      $id_articulos=trim($codigo_clave[3]);
      $arti_sin_salida=explode ("/" , $codigo_clave[3]);
      

	$listado_claves='0';	   
	for ($i=1;$i<count($claves);$i++)    
	{     
	 if ($i==1)
	  $listado_claves=$claves[$i];
	 else
	  $listado_claves=$listado_claves.",".$claves[$i];
	} 

	$buscar_datos="SELECT etiqueta,serial FROM sai_biin_items t1 WHERE t1.clave_bien in (".$listado_claves.")";
	$resultado_busqueda=pg_query($conexion,$buscar_datos);

}
?>
<body>
<?php if ($codigo<>"0"){?>
<form action="" name="form" id="form" method="post" >
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
   <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"> Re-asignaci&oacute;n de activos</span> </td>
   </tr>
   	<tr>
	  <td class="normal"><strong>N&deg; acta</strong></td>
	  <td class="normalNegro"><b><?php echo($codigo); ?></b></td></tr>
	  	<tr>
	  <td class="normal"><strong>Fecha</strong></td>
	  <td class="normalNegro"><?php echo(date('d/m/Y')); ?></td></tr>
   <tr>
	 <td class="normal"><strong>Dependencia solicitante</strong></td>
     <td class="normalNegro">
		<?php
		    $sql_str="SELECT * FROM sai_dependenci WHERE depe_id in (".$listado_depe.")";	
	        $res_q=pg_exec($sql_str) or die("Error al mostrar");	  
	   	    $i=0;
	        $depe_nombre='';
	   		while($depe_row=pg_fetch_array($res_q)){ 
		    if ($i==0)
		 	  $depe_nombre=$depe_row['depe_nombre'];
		    else
		 	  $depe_nombre=$depe_nombre.",".$depe_row['depe_nombre'];
	        
	   		$i++;
	   		}
             echo($depe_nombre); 
             ?>
	  </td></tr>
	<tr>
	  <td class="normal"><strong>Elaborado por</strong></td>
	  <td class="normalNegro"><?php echo($_SESSION['solicitante']); ?></td></tr>
	  <?php if ($id_infocentro<>""){?>
	<tr>
	  <td class="normal"><strong>Infocentro</strong></td>
	  <td class="normalNegro"><?php echo($id_infocentro." : ".$nombre_infocentro); ?></td></tr>
	  <?php }?>
	<tr>
	  <td class="normal"><strong>Destino</strong></td>
	  <td class="normalNegro"><?php echo($_POST['destino']); ?></td></tr>
	<tr>
	  <td class="normal"><strong>Activos</strong></td>
	  <td class="normalNegro">
	  <?php 
       $sql_salida="SELECT t3.etiqueta,t3.serial 
       FROM sai_bien_reasignar t1,sai_bien_reasignar_item t2,sai_biin_items t3
       WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and t2.clave_bien=t3.clave_bien";
       $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
	   while($rowd=pg_fetch_array($resultado_salida)) 
	   { 
        echo("Serial Bien Nacional: <b>".$rowd['etiqueta']."</b> Serial Art&iacute;culo: <b>".$rowd['serial'])."</b><br>";  
	   }?>
	  </td></tr>
	   <?php if (count($claves)>1){?>
	   <tr>
	    <td class="normal" hight="20"><div style="color:#FF0000;"><strong>Activos NO re-asignados:</strong></div></td>
	    <td class="normalNegro"><div style="color:#FF0000;"><strong>
	    <? while($row_buscar=pg_fetch_array($resultado_busqueda)){
	         echo("Serial Bien Nacional: ".$row_buscar['etiqueta']." Serial Art&iacute;culo: ".$row_buscar['serial'])."<br>";
	     }?></strong></div></td></tr><?php  }?>
	   
	   
</table>
  <br><br>
  <div align='center'><a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Salida<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=a">Asignaci&oacute;n<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div><br>
  	
</form>
<?php } else{?>
<br></br><div  style="color:#FF0000;" align="center"><b>No se puede emitir el acta de re-asignaci&oacute;n con la informaci&oacute;n suministrada<br>
<br><br>Enviar esta informacion al Departamento de Sistemas: <?php echo $sql;?></br></div>
<?php }?>
</body>
</html>

			
<?php pg_close($conexion);?>