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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Registrar Inventario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

  $fec=trim($_POST['hid_desde_itin']);
  $prov=$_POST['proveedor'];
  $ubicacion=$_POST['txt_ubica'];
  
  
  if($ubicacion==1){
  	$txtubicacion="Torre";
  }else
  {
  	$txtubicacion="Galp&oacute;n";
  }

  $facturas_partida=trim($_POST['txt_arreglo_factura_head']);
  $vector=explode ("�" , $facturas_partida);
  $elem_vector=count($vector);
  $tt_factura= ($elem_vector/8);
  $id=array($elem_vector);
  $nombre=array($elem_vector);
  $cantidad=array($elem_vector);
  $id_marca=array($elem_vector);
  $modelo=array($elem_vector);
  $serial=array($elem_vector);
  $fecha=array($elem_vector);
  $nombre_marca=array($elem_vector);

  $sql_rif="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_id_rif','prov_nombre='||'''$prov''','',2) resultado_set (prov_id_rif varchar)";
  $resul_rif=pg_query($conexion,$sql_rif);
  if($rowa=pg_fetch_array($resul_rif)){
	$codigo_rif=trim($rowa['prov_id_rif']);       	
  }	
  
  $x=0;
  $tt=0;
  while ($x< $elem_vector)
  {	
	$id[$tt]=trim($vector[$x]);
	$nombre[$tt]=trim($vector[++$x]);
	$cantidad[$tt]=trim($vector[++$x]);
	$id_marca[$tt]=trim($vector[++$x]);
	$modelo[$tt]=trim($vector[++$x]);
	$serial[$tt]=trim($vector[++$x]);
	$fecha[$tt]=trim($vector[++$x]);
	$nombre_marca[$tt]=trim($vector[++$x]);
	$tt++;
	$x++;
  }
	require_once("../../includes/arreglos_pg.php");
	$arreglo_id = convierte_arreglo ($id);
	$arreglo_cantidad = convierte_arreglo ($cantidad);
	$arreglo_marca = convierte_arreglo ($id_marca);
	$arreglo_modelo = convierte_arreglo ($modelo);
    $arreglo_serial=convierte_arreglo ($serial);
    $arreglo_fecha=convierte_arreglo ($fecha);

     $sql =  "Select * from sai_insert_inven_resp_social('".$arreglo_id."','".$arreglo_cantidad."','".$arreglo_marca."',
    '".$arreglo_modelo."','".$arreglo_serial."','".$_POST['observaciones']."','".$arreglo_fecha."','".$_SESSION['login']."',
    '".$_SESSION['user_depe_id']."','".$codigo_rif."','".$ubicacion."','".$_POST['monto_recibido']."') as ingresado ";
	//echo $sql;
    $resultado = pg_query($conexion,$sql);
	$valido=$resultado;
  if ($row=pg_fetch_array($resultado))
   {$codigo=$row['ingresado'];

/////////////////////////////////cadena/////////accion luego de generar codigo   
   $params['ers_id'] = $codigo;
   
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
    
   $data['docg_id'] = $params['ers_id'];
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
<table width="820" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td height="15" colspan="5" valign="middle" class="normalNegroNegrita">Registro de Inventario: Acta N&deg; <?=$codigo;?></td>
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
	<td height="48" colspan="5"><table width="819" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
  <tr>
    <td width="37" style="background-color:#C3ECCC;"><div align="center"><span class="normalNegrita">#</span></div></td>
    <td width="55" height="15" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">C&oacute;digo</div></td>
    <td width="400" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Nombre</div></td>
    <td width="37" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Cantidad</div></td>
    <td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Marca</div></td>
    <td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Modelo</div></td>
    <td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Serial</div></td>
    <td width="100" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Fecha recepci&oacute;n</div></td>
  </tr>
  <?php
	for ($i=0; $i< $tt_factura; $i++){	?>
  <tr>
    <td <?php echo $fondo_str;?>><div align="center" class="normal"><?php echo $i+1;?></div></td>
   <?php
	 $arti=$id[$i];	
	 $sql_ar="SELECT * FROM sai_seleccionar_campo('sai_item t1,sai_item_articulo t2','t1.id,nombre,existencia_minima','t1.id=t2.id and t1.id=''$arti''','',1) resultado_set(id varchar,nombre varchar,existencia_minima int4)"; 
     $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  

     
     //echo $sql_ar;
     
     
	 if($row=pg_fetch_array($resultado_set_most_ar))
	 {	
      ?>
     <td><div align="center" class="normal" ><?php echo $id[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $nombre[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $cantidad[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $nombre_marca[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $modelo[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $serial[$i];?></div></td>
     <td><div align="center" class="normal"><?php echo $fecha[$i];?></div></td>
  </tr>
      <?php
	} 
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
     <a target="_blank" class="normal" href="entradas_rs_pdf.php?id=<?=$codigo;?>">
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
<?php pg_close($conexion);?>
