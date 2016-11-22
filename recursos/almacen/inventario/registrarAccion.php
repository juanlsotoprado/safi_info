<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
  include(dirname(__FILE__) . '/../../../init.php');
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush();    exit;
  }
  ob_end_flush();
  
  ////////////////////////////////////////////cadena
  
  //Modelos
  
  include(SAFI_MODELO_PATH. '/estado.php');
  require_once(SAFI_MODELO_PATH. '/dependencia.php');
  require_once(SAFI_MODELO_PATH. '/empleado.php');
  require_once(SAFI_MODELO_PATH. '/general.php');
  require_once(SAFI_MODELO_PATH. '/docgenera.php');
  require_once(SAFI_MODELO_PATH. '/estatus.php');
  require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/wfgrupo.php');
  require_once(SAFI_MODELO_PATH. '/wfopcion.php');
  require_once(SAFI_MODELO_PATH. '/wfcadena.php');
  require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
  require_once(SAFI_MODELO_PATH. '/cargo.php');
  
  /*
  echo "<pre>";
  echo print_r($_POST);
  echo "</pre>";
  */
  $arrayregistro = $_POST['ArrayRegistro'];
  $total = $_POST['total'];
  
  $fec=trim($_POST['hid_desde_itin']);
  $depe=trim($_POST['opt_depe']);
  $prov=$_POST['nombre'];
  $ubicacion=$_POST['txt_ubica'];
  if($ubicacion == 1)
  {
  	$ubicaciontxt = "Torre";
  }
  else
  {
  	$ubicaciontxt = "Galp&oacute;n";
  }
  
  $sql_rif="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_id_rif','prov_nombre='||'''$prov''','',2) resultado_set (prov_id_rif varchar)";
  $resul_rif=pg_query($conexion,$sql_rif);
  if($rowa=pg_fetch_array($resul_rif)){
  	$codigo_rif=trim($rowa['prov_id_rif']);
  }
  
  $sqldepen = 
  "
  	SELECT
		depe_nombre
	FROM
		sai_dependenci dependencia
	WHERE
		depe_id = ".$depe."		
	ORDER BY
		depe_nombre
  ";
  $resuldepe = pg_query($conexion,$sqldepen);  
  $depe_row=pg_fetch_array($resuldepe);
  $depe_nombre=$depe_row['depe_nombre'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Registrar Inventario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php


  /*
  $vector=explode ("�" , $facturas_partida);
  $elem_vector=count($vector);
  $tt_factura= ($elem_vector/7);
  $id=array($elem_vector);
  $nombre=array($elem_vector);
  $cantidad=array($elem_vector);
  $precio=array($elem_vector);
  $medida=array($elem_vector);
  $fecha=array($elem_vector);


  
  $x=0;
  $tt=0;
  while ($x< $elem_vector)
  {	
	$id[$tt]=trim($vector[$x]);
	$nombre[$tt]=trim($vector[++$x]);
	$cantidad[$tt]=trim($vector[++$x]);
	$precio[$tt]=trim($vector[++$x]);
	$depe_id[$tt]=trim($vector[++$x]);
	$nombre_depe[$tt]=trim($vector[++$x]);
	$arti=$id[$tt];	

 */
	require_once("../../../includes/arreglos_pg.php");
	$arreglo_id = convierte_arreglo ($arrayregistro["id"]);
	$arreglo_cantidad = convierte_arreglo ($arrayregistro["cantidad"]);
	$arreglo_precio = convierte_arreglo ($arrayregistro["precio"]);
    $arreglo_medida=convierte_arreglo ($arrayregistro["unidad"]);
    $arreglo_depe=convierte_arreglo ($arrayregistro["depe"]);
    $arreglo_fecha=convierte_arreglo ($arrayregistro["fecha"]);

   //}
    $sql =  "Select * from sai_insert_inven_ini ('".$arreglo_id."','".$arreglo_cantidad."','".$arreglo_precio."','".$arreglo_depe."','".$arreglo_fecha."','".$arreglo_medida."','".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$codigo_rif."','".$ubicacion."') as ingresado ";
    $resultado = pg_query($conexion,$sql);
	$valido=$resultado;
   if ($row=pg_fetch_array($resultado))
   {$codigo=$row['ingresado'];
   
   /////////////////////////////////cadena/////////accion luego de generar codigo
   $params['emat_id'] = $codigo;
    
   $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
   $params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSigiente']);
    
   $cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
    
   $params['CadenaGrupo'] = $cadenaIdGrupo;
   $params['DependenciaTramita'] = $param['Dependencia'];
   $params['presAnno'] = $_SESSION['an_o_presupuesto'];
    
    
    
   $dateTime = new DateTime();
    
   $fechax = (String) $dateTime->format('d/m/Y h:m:s');
    
   //echo $fechax;
   
   ////////////////////////ingreso en doc genera de los datos
   
   $data['docg_id'] = $params['emat_id'];
   $data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
   $data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
   $data['docg_usua_login'] = $_SESSION['login'];
   $data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
   $data['docg_fecha'] = $fechax;
   $data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
   $data['docg_prioridad'] = 1 ;
   $data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
   $data['docg_estado_pres'] = '' ;
   $data['docg_numero_reserva'] =  '' ;
   $data['docg_fuente_finan'] = '' ;
    
   $docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
    
    
   /*error_log(print_r($params,true));
   error_log(print_r($docGenera,true));*/
   
    
    
   $result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
    
   
   }
	
	
	?>
	<br /><br />
<?php 
if ($valido){ ?>
<table width="720" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr class="td_gray"> 
	<td height="15" colspan="5" valign="middle" class="normalNegroNegrita">Registro de Inventario: Acta N&deg; <?=$codigo;?></td>
  </tr>
  <tr>
	<td height="11" colspan="5"></td>
  </tr>
  <tr>
	<td height="11" colspan="5"></td>
  </tr>
  <tr>
	<td height="48" colspan="5">
	<table width="682" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
	<tr>
		<td style="padding-left: 10px;" height="15" colspan="2" class="normalNegrita">Ubicaci&oacute;n:</td>
		<td style="padding-left: 10px;" height="15" colspan="6" class="normalNegrita"><?=$ubicaciontxt;?></td>
	</tr>
	<tr>
		<td style="padding-left: 10px;" height="15" colspan="2" class="normalNegrita">Proveedor:</td>
		<td style="padding-left: 10px;" height="15" colspan="6" class="normalNegrita"><?=$prov;?></td>
	</tr>
	<tr>
		<td style="padding-left: 10px;" height="15" colspan="2" class="normalNegrita">Dependencia Solicitante:</td>
		<td style="padding-left: 10px;" height="15" colspan="6" class="normalNegrita"><?=$depe_nombre;?></td>
	</tr>
	<tr>
		<td height="11" colspan="5" class="normalNegrita"></td>
	</tr>
	<tr align="center">
		<td height="11" colspan="8" class="normalNegrita">Art&iacute;culos</td>
	</tr>
  <tr class="td_gray">
    <td width="37"><div align="center"><span class="normalNegrita">#</span></div></td>
    <td width="55" height="21"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
    <td width="200"><div align="center" class="normalNegrita">Nombre</div></td>
    <td width="77"><div align="center" class="normalNegrita">Cantidad</div></td>
    <td width="57"><span class="normalNegrita">Existencia actual </span></td>
    <td width="80"><div align="center" class="normalNegrita">Precio</div></td>
    <td width="80"><div align="center" class="normalNegrita">Fecha recepci&oacute;n</div></td>
  </tr>
  <?php 
  for ($i=0; $i < $total; $i++){

		//Entradas
		$num_entradas=0;
		$sql_e="
  		select 
			sum(cantidad) as entrada 
		from 
			sai_arti_almacen esp,
			sai_arti_inco gen
		where 
			esp.acta_id = gen.acta_id and
			esp.arti_id='".$arrayregistro["id"][$i]."' and 
			esp.ubicacion = ".$ubicacion." and 
			esp.precio = ".$arrayregistro["precio"][$i]." and
			gen.esta_id <> 15
		group by 
			arti_id
  		";
		$resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos3");
		if($rowe=pg_fetch_array($resultado_entrada))
		{
			$num_entradas=$rowe['entrada'];
		}
		
		//echo "query entradas:   ".$sql_e;
		
		$num_salidas=0;
		$num_devoluciones=0;
		
		//SE LE SUMAN LAS DEVOLUCIONES Y SE RESTAN LAS SALIDAS HASTA LA FECHA,
		$sql_salidas="
  		select 
			sum(cantidad) as canti_salida,
			tipo 
		from 
			sai_arti_salida,
			sai_arti_acta_almacen
		where 
			n_acta=amat_id and 
			arti_id='".$arrayregistro["id"][$i]."' and 
			esta_id<>15 and 
			ubicacion = ".$ubicacion." and
			precio = ".$arrayregistro["precio"][$i]."
		group by tipo
  		";
		$num_salidas=0;
		$num_devoluciones=0;
		$resultado_salidas=pg_query($conexion,$sql_salidas) or die("Error al consultar salida de articulos5");
		
		//echo "query salidas:   ".$sql_salidas;
		
		while($rowsal=pg_fetch_array($resultado_salidas))
		{
			if ($rowsal['tipo']=='S'){
				$num_salidas=$rowsal['canti_salida'];
			}else{
				$num_devoluciones=$rowsal['canti_salida'];
			}
		
		}
		//echo "entradas   ".$num_entradas;
		//echo "???devoluciones  ".$num_devoluciones;
		//echo "salidas   ".$num_salidas;
		
		$inventario_total=$num_entradas+$num_devoluciones-$num_salidas;
	 	
      ?>
   <tr>
   <td><div align="center" class="normal" ><?php echo $i+1;?></div></td>
     <td><div align="center" class="normal" ><?php echo $arrayregistro["id"][$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $arrayregistro["nombre"][$i]?></div></td>
	 <td><div align="center" class="normal"><?php echo $arrayregistro["cantidad"][$i]?></div></td>
     <td><div align="center" class="normal"> <?php echo $inventario_total ?></div></td>
     <td><div align="center" class="normal"><?php echo $arrayregistro["precio"][$i]?></div></td>
     <td><div align="center" class="normal"><?php echo $arrayregistro["fecha"][$i]?></div></td>
  </tr>
      <?php
	} 
  
  ?>
  
  
  
  <?php
	/*for ($i=0; $i<  $tt_factura; $i++){	?>
  <tr>
    <td <?php echo $fondo_str;?>><div align="center" class="normal"><?php echo $i+1;?></div></td>
   <?php
   	 //Entradas
   	 $num_entradas=0;
	 $sql_e="select sum(cantidad) as entrada from sai_arti_almacen where arti_id='".$arti."' and ubicacion = ".$ubicacion." group by arti_id";
	 $resultado_entrada=pg_query($conexion,$sql_e) or die("Error al consultar entrada de articulos3");  
	 if($rowe=pg_fetch_array($resultado_entrada)) 
  	 {
  	  $num_entradas=$rowe['entrada'];
  	 }
  	  
	  $num_salidas=0;
	  $num_devoluciones=0;
    
     //SE LE SUMAN LAS DEVOLUCIONES Y SE RESTAN LAS SALIDAS HASTA LA FECHA, 
  	 $sql_salidas="select sum(cantidad) as canti_salida,tipo from sai_arti_salida,sai_arti_acta_almacen where n_acta=amat_id and arti_id='".$arti."' and esta_id<>15 and ubicacion = ".$ubicacion." group by tipo";
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
  	
   	$inventario_total=$num_entradas+$num_devoluciones-$num_salidas;
//   ************
	 $arti=$id[$i];	
	 $sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,existencia_minima','t1.id=t2.id and t1.id=''$arti''','',1) resultado_set(id varchar,nombre varchar,existencia_minima int4)"; 
     $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  

	 if($row=pg_fetch_array($resultado_set_most_ar))
	 {	
      ?>
     <td><div align="center" class="normal" ><?php echo $id[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $nombre[$i];?></div></td>
	 <td><div align="center" class="normal"><?php echo $nombre_depe[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $cantidad[$i];?></div></td>
     <td><div align="center" class="normal"> <?php echo $inventario_total; ?></div></td>
     <td><div align="center" class="normal"><?php echo $precio[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $fecha[$i];?></div></td>
  </tr>
      <?php
	} 
  }	*/?>
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
     <a target="_blank" class="normal" href="entradas_pdf.php?id=<?=$codigo;?>">
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
<?php pg_close($conexion);?>
