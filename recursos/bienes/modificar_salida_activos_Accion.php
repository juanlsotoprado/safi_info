<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
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
$acta_almacen=$_REQUEST['acta_almacen'];
$fecha_acta=$_REQUEST['fecha_acta'];
$opcion=$_POST['opcion'];
$titulo=$_POST['titulo'];

// Borrar
/*
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
exit();
*/

$memo_contenido=trim($_POST['contenido_memo']);
 if ($memo_contenido==""){
 	$memo_contenido="No Especificado";
 }

if (($acta_almacen=='') || ($acta_almacen == null))
$acta_almacen=0;
 
for ($i=0;$i<count($unidad);$i++)    
{     
 $listado_depe=$listado_depe.$unidad[$i]."','";
 
 if ($i==0)
  $listado_solicitante=$unidad[$i];
 else
  $listado_solicitante=$listado_solicitante.",".$unidad[$i];
} 

 $listado_depe="'".$listado_depe."'";

 
 
 
/**********
 * ACTIVOS
***********/
  $num_bienes=0;
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

	     //  $listado_bienes[$j]=$rowdetalle['bien_id'];
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
 * ARTICULOS
 *************/
  
  $articulos=trim($_POST['txt_arreglo_articulos']);
  $vector=explode ("�" , $articulos);
  $elem_vector=count($vector);
  $tt_articulos= ($elem_vector/3);
  
  $x=0;
  $tt=0;

  
if ($tt_articulos>=1){
  $id=array($elem_vector);
  $nombre=array($elem_vector);
  $cantidad=array($elem_vector);
  $medida=array($elem_vector);
  while ($x< $elem_vector)
  {	
	$id[$tt]=trim($vector[$x]);
	$nombre[$tt]=trim($vector[++$x]);
	$cantidad[$tt]=trim($vector[++$x]);
	$id_art=$id[$tt];
	$sql_d="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','unidad_medida','t1.id=t2.id and t1.id='||'''$id_art''','',2) resultado_set(unidad_medida varchar)";
	$resultado_set_d=pg_query($conexion,$sql_d) or die("Error al mostrar");
	      
	if($rowd=pg_fetch_array($resultado_set_d)) 
	{ 
	 $medida[$tt]=$rowd['unidad_medida'];
	}
	
	$tt++;
	$x++;
   }  
}
	    
/*************
 * MOBILIARIOS
 *************/
  $mobiliarios=trim($_POST['txt_arreglo_mobiliario']);
  $vector=explode ("�" , trim($mobiliarios));
  $elem_vector=count($vector);
  $mobiliarios="'".ereg_replace( "�", "','", trim($mobiliarios) )."'";

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
 
	 //Pasar listado de activos a eliminar
	if (isset($_POST['activos_eliminar'])) {
	 $cod = $_POST["activos_eliminar"];
    }
    if (count($cod)>0) {
      for ($x=0;$x<count($cod);$x++) {
         $claves_eliminar[$x] = $cod[$x];
      }
        $arreglo_activo_eliminar   = convierte_arreglo ($claves_eliminar);
    }else{
    	$arreglo_activo_eliminar = "{}";
    }  

     if (substr($_POST['codigo'],0,1)=='a')
      $tipo='A';//ASIGNACIÓN
      else $tipo='R'; //REASIGNACIÓN
	//SI PASA TODAS LAS VALIDACIONES SE ALMACENA LA SALIDA
	
	$sql="Select * From sai_modificar_asbi(
			 '".$_SESSION['user_depe_id']."',
			 '".$_SESSION['login']."',
	         '".$_POST['codigo']."',
			 '".addslashes(addslashes($_POST['destino']))."',
			 '".$id_infocentro."',
			 '".$listado_solicitante."','".$_POST['ubicacion']."',
			 '".$arreglo_bienes."',
			 '".$arreglo_articulo."', 
			 '".$arreglo_cantidad."',
			 '".$arreglo_medida."','".$acta_almacen."','".$fecha_acta."','".$arreglo_activo_eliminar."','".$tipo."'
			 ) as codigo";
	
	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar los activos, verifique que el número del activo no se encuentre registrado"));
    if ($row=pg_fetch_array($resultado_set))
	{
      $codigo_clave=explode ("*" , $row['codigo']);
      $codigo=trim($codigo_clave[0]);
      $clave_activos=trim($codigo_clave[1]);//"'".ereg_replace( "*", "','", $row['codigo'] )."'";
      $claves=explode ("/" , $codigo_clave[1]);

	  $listado_claves='0';	   
	  for ($i=1;$i<count($claves);$i++)    
	  {     
 	   if ($i==1)
  		$listado_claves=$claves[$i];
 	   else
  		$listado_claves=$listado_claves.",".$claves[$i];
	  } 	  
	  
	//PARA CASOS DE REASIGNACION SE DEBE MOSTRAR
	//LOS DETALLES DE LOS ACTIVOS Q NO SE REASIGNARON XQ NO ESTABAN ASIGNADOS
	//CASO CONTRARIO A LA ASIGNACION NORMAL
	if ($tipo=='A'){ 
		$buscar_datos="SELECT t2.asbi_id,etiqueta,serial 
			FROM sai_biin_items t1, sai_bien_asbi_item t2 
			WHERE t1.clave_bien=t2.clave_bien and t1.clave_bien in (".$listado_claves.")";
	}else{
		$buscar_datos="SELECT 'N/A' as asbi_id,etiqueta,serial 
			FROM sai_biin_items t1  
			WHERE t1.clave_bien in (".$listado_claves.")";
	}
	
	$resultado_busqueda=pg_query($conexion,$buscar_datos);
	}

	if (
		$_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES
		|| $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES
	){
		if ($opcion==15)  //Anulación
		{
			$accion=" ANULADA ";
			$codigo_memo = utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."', '".$memo_contenido."', 'Anulación de Acta de Salida','0', '0','0','',0, 0, '0', '','".$codigo."') as resultado");
			$result_memo=pg_query($conexion,$codigo_memo) or die ("Error al registrar el memo");
			
			if ($tipo == 'A'){  // Asignación
				$edo_revision = 41;
			} else {  // Reasignación
				$edo_revision = 53;
			}
			
			if ($acta_almacen <>''){
				$sql = "select * from sai_acta_anulacion('".$acta_almacen."','S') as ingresado ";
				$resultado=pg_query($conexion,$sql) or die(utf8_decode("No se generó ninguna devolución"));
			}  
	}else{
		$accion="  ";
		$sql = " SELECT * FROM sai_insert_revision_doc('$codigo', '".$_SESSION['login']."', '".$_SESSION['user_perfil_id']."', '6', '$firma_doc') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al insertar la revision");
		$edo_revision=12;
		$opcion=12;
	}

	if ($tipo=='A'){  // Asignación
		$query="UPDATE sai_bien_asbi SET esta_id='".$opcion."' where asbi_id='".$codigo."'";
		$resultado_acta=pg_query($conexion,$query);
   
		if($opcion == 15) // Anulación
		{
			$query = "
				UPDATE
					sai_biin_items
				SET
					esta_id = '41',
					ubicacion = datos.ubicacion
				FROM
					(
						-- Entradas
						SELECT
							entrada.acta_id AS acta_id,
							entrada.esta_id,
							entrada_detalle.clave_bien,
							entrada.fecha_registro AS fecha,
							entrada.ubicacion
						FROM
							sai_bien_inco entrada
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
				
						UNION
						-- Salidas
						SELECT
							salida.asbi_id AS acta_id,
							salida.esta_id,
							entrada_detalle.clave_bien,
							salida.asbi_fecha AS fecha,
							salida.ubicacion
						FROM
							sai_bien_asbi salida
							INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
				
						UNION
						--Reasignaciones
				
						SELECT
							reasignacion.acta_id AS acta_id,
							reasignacion.esta_id,
							entrada_detalle.clave_bien,
							reasignacion.fecha_acta AS fecha,
							reasignacion.ubicacion
						FROM
							sai_bien_reasignar reasignacion
							INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
				
				
					) AS datos
					INNER JOIN
					(
				
						SELECT
							entrada_detalle.clave_bien AS clave_bien,
							MAX(reporte.fecha) AS fecha
						FROM
							sai_bien_asbi salida
							INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
							INNER JOIN
							(
								-- Entradas
								SELECT
									entrada.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									entrada.fecha_registro AS fecha,
									entrada.ubicacion
								FROM
									sai_bien_inco entrada
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
				
								UNION
								-- Salidas
								SELECT
									salida.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									salida.asbi_fecha AS fecha,
									salida.ubicacion
								FROM
									sai_bien_asbi salida
									INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
				
								UNION
								--Reasignaciones
				
								SELECT
									reasignacion.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									reasignacion.fecha_acta AS fecha,
									reasignacion.ubicacion
								FROM
									sai_bien_reasignar reasignacion
									INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
								
									
							) AS reporte ON (reporte.clave_bien = entrada_detalle.clave_bien)
						WHERE
							reporte.esta_id <> 15
							AND reporte.fecha < salida.asbi_fecha
							AND salida.asbi_id = '".$codigo."'
						GROUP BY
							entrada_detalle.clave_bien,
							salida.asbi_fecha
				
				
					) AS fecha_maxima ON (
						fecha_maxima.clave_bien = datos.clave_bien
						AND fecha_maxima.fecha = datos.fecha
					)
				WHERE
					datos.esta_id <> 15
					AND sai_biin_items.clave_bien = datos.clave_bien
			";
			
			$resultado = pg_query($conexion, $query);
			
		} else {
			//Actualizar el Edo de los Activos según lo indique las revisiones
			$query_activos="SELECT * FROM  sai_bien_asbi_item t1, sai_biin_items t2 
				WHERE t1.clave_bien=t2.clave_bien and asbi_id='".$codigo."' ";
			$resultado_activos=pg_query($conexion,$query_activos);
			
			while ($rows=pg_fetch_array($resultado_activos))
			{
				$act_activo="UPDATE sai_biin_items SET esta_id='".$edo_revision."' WHERE clave_bien='".$rows['clave_bien']."'";
				$resultado_actualiza=pg_query($conexion,$act_activo);
			}
		}
   
	} else {  // Reasignación
		$query="UPDATE sai_bien_reasignar SET esta_id='".$opcion."' where acta_id='".$codigo."'";
		$resultado_acta=pg_query($conexion,$query);

		if ($opcion==15)  //Anulación
		{
			$query = "
				UPDATE
					sai_biin_items
				SET
					esta_id = '53',
					ubicacion = datos.ubicacion
				FROM
					(
						-- Entradas
						SELECT
							entrada.acta_id AS acta_id,
							entrada.esta_id,
							entrada_detalle.clave_bien,
							entrada.fecha_registro AS fecha,
							entrada.ubicacion
						FROM
							sai_bien_inco entrada
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
				
						UNION
						-- Salidas
						SELECT
							salida.asbi_id AS acta_id,
							salida.esta_id,
							entrada_detalle.clave_bien,
							salida.asbi_fecha AS fecha,
							salida.ubicacion
						FROM
							sai_bien_asbi salida
							INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
				
						UNION
						--Reasignaciones
				
						SELECT
							reasignacion.acta_id AS acta_id,
							reasignacion.esta_id,
							entrada_detalle.clave_bien,
							reasignacion.fecha_acta AS fecha,
							reasignacion.ubicacion
						FROM
							sai_bien_reasignar reasignacion
							INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
							INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
				
				
					) AS datos
					INNER JOIN
					(
				
						SELECT
							entrada_detalle.clave_bien AS clave_bien,
							MAX(reporte.fecha) AS fecha
						FROM
							sai_bien_reasignar reasignacion
							INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
							INNER JOIN sai_biin_items entrada_detalle
								ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
							INNER JOIN
							(
								-- Entradas
								SELECT
									entrada.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									entrada.fecha_registro AS fecha,
									entrada.ubicacion
								FROM
									sai_bien_inco entrada
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.acta_id = entrada.acta_id)
				
								UNION
								-- Salidas
								SELECT
									salida.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									salida.asbi_fecha AS fecha,
									salida.ubicacion
								FROM
									sai_bien_asbi salida
									INNER JOIN sai_bien_asbi_item salida_detalle ON (salida_detalle.asbi_id = salida.asbi_id)
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = salida_detalle.clave_bien)
				
								UNION
								--Reasignaciones
				
								SELECT
									reasignacion.esta_id,
									entrada_detalle.clave_bien AS clave_bien,
									reasignacion.fecha_acta AS fecha,
									reasignacion.ubicacion
								FROM
									sai_bien_reasignar reasignacion
									INNER JOIN sai_bien_reasignar_item reasignacion_detalle ON (reasignacion_detalle.acta_id = reasignacion.acta_id)
									INNER JOIN sai_biin_items entrada_detalle ON (entrada_detalle.clave_bien = reasignacion_detalle.clave_bien)
								
									
							) AS reporte ON (reporte.clave_bien = entrada_detalle.clave_bien)
						WHERE
							reporte.esta_id <> 15
							AND reporte.fecha < reasignacion.fecha_acta
							AND reasignacion_detalle.acta_id = '".$codigo."'
						GROUP BY
							entrada_detalle.clave_bien,
							reasignacion.fecha_acta
				
				
					) AS fecha_maxima ON (
						fecha_maxima.clave_bien = datos.clave_bien
						AND fecha_maxima.fecha = datos.fecha
					)
				WHERE
					datos.esta_id <> 15
					AND sai_biin_items.clave_bien = datos.clave_bien
			";
			
			$resultado = pg_query($conexion, $query);
			
		} else {
			//Actualizar el Edo de los Activos según lo indique las revisiones
			$query_activos="SELECT * FROM  sai_bien_reasignar_item t1, sai_biin_items t2 
				WHERE t1.clave_bien=t2.clave_bien and t1.acta_id='".$codigo."' ";
			$resultado_activos=pg_query($conexion,$query_activos);
			while ($rows=pg_fetch_array($resultado_activos)){
				$act_activo="UPDATE sai_biin_items SET esta_id='".$edo_revision."' WHERE clave_bien='".$rows['clave_bien']."'";
				$resultado_actualiza=pg_query($conexion,$act_activo);
			}
		}
	}
}
  

?>
<body>
<?php if ($codigo<>"0"){?>
<form action="" name="form" id="form" method="post" >
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
   <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"><?=$titulo;?></span> </td>
   </tr>
   	<tr>
	  <td class="normal"><strong>N&deg; Acta:</strong></td>
	  <td class="normalNegro"><b><?php echo($codigo); ?></b><font color='Red'><STRONG><?php echo $accion;?></STRONG></font></td></tr>
	  	<tr>
	  <td class="normal"><strong>Fecha:</strong></td>
	  <td class="normalNegro"><?php echo(date('d/m/Y')); ?></td></tr>
   <tr>
	 <td class="normal"><strong>Unidad Solicitante:</strong></td>
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
	  <td class="normal"><strong>Elaborado por:</strong></td>
	  <td class="normalNegro"><?php echo($_SESSION['solicitante']); ?></td></tr>
	<tr>
	  <td class="normal"><strong>Ubicaci&oacute;n:</strong></td>
	  <td class="normalNegro">
	  <?php 
   		 $sql_p="Select * from sai_bien_ubicacion where bubica_id='".$_POST['ubicacion']."'";
		 $resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al mostrar");
		 if($row=pg_fetch_array($resultado_set_most_p))
		 { 
           echo $row['bubica_nombre'];  
	   }?>
	  </td></tr>
	<tr>
	  <td class="normal"><strong>Infocentro:</strong></td>
	  <td class="normalNegro"><?php echo($id_infocentro." : ".$nombre_infocentro); ?></td></tr>
	<tr>
	  <td class="normal"><strong>Destino:</strong></td>
	  <td class="normalNegro"><?php echo($_POST['destino']); ?></td></tr>
		   <?php if (count($claves)>1){?>
	   <tr>
	    <td class="normal"><div style="color:#FF0000;"><strong>Activos <?php if ($tipo!='A'){?> NO <?php }?>Asignados Previamente:</strong></div></td>
	    <td class="normalNegro">
	    <? while($row_buscar=pg_fetch_array($resultado_busqueda)){
	        if ($tipo=='A') 
	    	 echo("Serial Bien Nacional: ".$row_buscar['etiqueta']." Serial Art&iacute;culo: ".$row_buscar['serial']." Acta de Salida: ".$row_buscar['asbi_id'])."<br>";
	    	else 
	    	 echo("Serial Bien Nacional: ".$row_buscar['etiqueta']." Serial Art&iacute;culo: ".$row_buscar['serial'])."<br>";
	     }?></td></tr><?php  }?>
</table>
  <br><br> <div align='center'>
  <?php
  if ($tipo=='A'){?>
  <a target="_blank" class="normal" href="salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Salida<img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" class="normal" href="salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=a&solicitante=<?=$listado_depe;?>">Asignaci&oacute;n<img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>
  <?php } else{ ?>
   <a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Salida<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=a">Asignaci&oacute;n<img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>
   <?php }?></div><br> 	
</form>
<?php } else{
?>
<br></br><div class="normal" align="center"><b>No se puede emitir el Acta de Salida con la informaci&oacute;n suministrada,<br> 
<br><br>Enviar esta información al Departamento de Sistemas: <?php echo $sql;?></br></div>
<?php }?>
</body>
</html>

			
<?php pg_close($conexion);?>
