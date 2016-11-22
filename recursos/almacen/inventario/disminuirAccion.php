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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Disminuir Inventario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
/*
echo "<pre>";
echo print_r($_POST);
echo "</pre>";
*/

	$arraydisminucion = $_POST['ArrayDisminucion'];

	$fec=trim($_POST['hid_desde_itin']);
	$fec = $fec.' '.strftime('%H:%M:%S');
	  
	$motivo=trim($_POST['motivo']);
	$destino=trim($_POST['opt_depe']);
	$solicita=trim($_POST['solicita']);

/*  $facturas_partida=trim($_POST['txt_arreglo_factura_head']);
  $vector=explode ("�" , $facturas_partida);
  $elem_vector=count($vector);
  $tt_factura= ($elem_vector/3);
  
  $id=array($elem_vector);
  $nombre=array($elem_vector);
  $cantidad=array($elem_vector);
  $motivo=trim($_POST['motivo']);
  $destino=trim($_POST['opt_depe']);
  $medida=array($elem_vector);
  $solicita=trim($_POST['solicita']);

  $x=0;
  $tt=0;
  while   ($x< $elem_vector)
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
	}  */
	require_once("../../../includes/arreglos_pg.php");
	$arreglo_id = convierte_arreglo ($arraydisminucion["id"]);
	$arreglo_cantidad = convierte_arreglo ($arraydisminucion["cantidad"]);
	$arreglo_medida = convierte_arreglo ($arraydisminucion["unidad"]);
	$arreglo_precio = convierte_arreglo ($arraydisminucion["precio"]);
        
    $sql =  "Select * from sai_insert_inven_dismi2 ('".$arreglo_id."','".$arreglo_cantidad."','";
	$sql .= $motivo."','".$destino."','".$_SESSION['login']."','".$arreglo_medida."','".$_SESSION['user_depe_id']."','".$solicita."','".$fec."',1,'".$arreglo_precio."') as ingresado ";
	
	/*
	echo "<pre>";
	echo print_r($arreglo_precio);
	echo "</pre>";
	echo $sql;
	*/
	$resultado = pg_query($conexion,$sql);
	$valido=$resultado;
	
    if ($row=pg_fetch_array($resultado))
	{ $codigo_retorno=explode ("*" , $row['ingresado']);
      $codigo=trim($codigo_retorno[0]);
      
      /////////////////////////////////cadena/////////accion luego de generar codigo
      $params['amat_id'] = $codigo;
      
      $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
      $params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSigiente']);
      
      $cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
      
      $params['CadenaGrupo'] = $cadenaIdGrupo;
      $params['DependenciaTramita'] = $param['Dependencia'];
      $params['presAnno'] = $_SESSION['an_o_presupuesto'];     
       
      ////////////////////////ingreso en doc genera de los datos
       
	  $data['docg_id'] = $params['amat_id'];
      $data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
      $data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
      $data['docg_usua_login'] = $_SESSION['login'];
      $data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
      $data['docg_fecha'] = $fec;
      $data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
      $data['docg_prioridad'] = 1 ;
      $data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
      $data['docg_estado_pres'] = '' ;
      $data['docg_numero_reserva'] =  '' ;
      $data['docg_fuente_finan'] = '' ;
      
      $docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
      
      //error_log(print_r($params,true));
      //error_log(print_r($docGenera,true));

      $result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
      
	}
	?>
<br /><br />
<?php

if ($valido)
{ 
if (strlen($codigo)>1){
?>
<table width="814" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <tr  class="td_gray">
    <td height="15" colspan="5" valign="middle" class="normalNegroNegrita">Inventario Disminuido acta: <?php echo $codigo;?></td>
  </tr>
  <tr>
    <td height="11" colspan="5"></td>
  </tr>
  <tr>
    <td height="11" colspan="5"></td>
  </tr>
  <tr>
    <td height="48" colspan="5">
    
    
    
    
    <table width="709" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
   	<tr>
      <td height="15" colspan="5" valign="middle" class="td_gray"><span class="normalNegroNegrita">Informac&oacute;n General</span> </td>
   </tr>
	  <tr>
	  <td class="normal"><strong>Fecha:</strong></td>
	  <td class="normalNegro"><?php echo(date('d/m/Y')); ?></td>
	  </tr>
	<tr>
	  <td class="normal"><strong>Observaciones:</strong></td>
	  <td class="normalNegro"><?php echo($_POST['motivo']); ?></td>
	  </tr>
	<tr>
	<tr>
	  <td class="normal"><strong>Entregado a:</strong></td>
	  <td class="normalNegro"><?php echo($_POST['entregado_a']); ?></td>
	  </tr>
	<tr>
	  <td height="5" class="normal" colspan="2"></td>
	  </tr>
  <tr>
    <td colspan="2">
	<table width="99.5%" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" border="0">
	<tr>
	<td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Id</div></td>
	  <td width="150" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Nombre</div></td>
	  <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Medida</div></td>
	  <td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Precio</div></td>
    	<td width="77" style="background-color:#C3ECCC;"><div align="center" class="normalNegrita">Cantidad</div></td>
	  </tr>

	  <?php 
       $sql_salida=
       "
  	SELECT
		t2.arti_id,
		t3.nombre,
		t2.precio,
		t2.medida,
		t2.cantidad_solicitada
	FROM
		sai_arti_acta_almacen t1
		inner join sai_arti_salida t2 on (t1.amat_id= t2.n_acta)
		inner join sai_item t3 on (t3.id= t2.arti_id)
	WHERE
		t1.amat_id='".$codigo."'";
       $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
       
      // echo $sql_salida;
       
	   while($rowd=pg_fetch_array($resultado_salida)) 
	   { 
        echo("<tr><td class='normalNegro'>".$rowd['arti_id']."</td><td class='normalNegro'>".$rowd['nombre']."</td><td class='normalNegro'>".$rowd['medida']."</td><td class='normalNegro'>".$rowd['precio']."</td><td class='normalNegro'	>".$rowd['cantidad_solicitada']."</td></tr>");  
	   }?>
  </table>
  </td>
  </tr>
   	
    </table>
    
    </td>
  </tr>
  <tr align="center">
    <td height="85" colspan="5" class="normal"><div align="center"></div><br />
            <br />
      Registro generado el d&iacute;a
      <?=date("d/m/y")?>
      a las
      <?=date("h:i:s")?>
      <br />
      <br />
      
      <a target="_blank" href="salidas_pdf.php?consulta=1&id=<?=$codigo;?>&arti_id=<?=$arreglo_id;?>&cant=<?=$arreglo_cantidad;?>)" >
      <img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br>
	 </td>
    </tr><br />
      <br /><br /><br/><br />
 </table>
 <br />
<?php
	} /*Del Valido*/
}
  
   if (($valido==false) ||($codigo==false)){
   ?>
<br />
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="normal"> 
		<td width="643" height="15" colspan="3" valign="middle" bgcolor="#0099CC" ><div align="left" class="titularMedio style1">ADMINISTRAR INVENTARIO </div></td>
		</tr>
		<tr>
		<td colspan="4" class="normal" align="center"><br>
		<div align="center">
		<?if (strlen($codigo)<2){?>
		No existe(n) el(los) art&iacute;culo(s) en el inventario
		<?}
          else{
		       if ($valido==false){?>
		  		Ha ocurrido un error al ingresar los datos 
		  	  <?}
                 }?>
		<br><?php echo(pg_errormessage($conexion)); ?><br>
		<img src="../../../imagenes/mano_bad.gif" width="31" height="38">
		<br><br><br><br></div>
		</td>
		</tr>
</table>
		<?php
  }
?>
</body>
</html>
<?php pg_close($conexion);?>