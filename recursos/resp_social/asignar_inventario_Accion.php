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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<?

$arrayRespSocial = $_POST['ArrayRespSocial'];

/*
echo "<pre>";
echo print_r($arrayRespSocial);
echo "</pre>";
*/
 
/*************
 * ARTICULOS
 *************/
  
	require_once("../../includes/arreglos_pg.php");
	if (count($arrayRespSocial["id"]) > 0 && count($arrayRespSocial["cantidad"]) > 0 && count($arrayRespSocial["unidad"]) > 0 && count($arrayRespSocial["modelo"]) > 0 && count($arrayRespSocial["idmarca"]) > 0){
	  $arreglo_articulo = convierte_arreglo ($arrayRespSocial["id"]);
	  $arreglo_cantidad = convierte_arreglo ($arrayRespSocial["cantidad"]);
	  $arreglo_medida   = convierte_arreglo ($arrayRespSocial["unidad"]);
	  $arreglo_modelo   = convierte_arreglo ($arrayRespSocial["modelo"]);
	  //marca
	  $arreglo_idmarca   = convierte_arreglo ($arrayRespSocial["idmarca"]);
	  //serial
	  $arreglo_serial   = convierte_arreglo ($arrayRespSocial["serial"]);
	  //ubicacion
	  $arreglo_ubicacion   = convierte_arreglo ($arrayRespSocial["idubicacion"]);
	}else{
		$arreglo_articulo = "{}";
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
	
	$sql="Select * From sai_insert_asignacion_rs(
			 '".$_SESSION['user_depe_id']."',
			 '".$_SESSION['login']."',
			 '".addslashes(addslashes($_POST['destino']))."',
			 '".$arreglo_articulo."', 
			 '".$arreglo_cantidad."',
			 '".$arreglo_medida."',
		 	 '".$arreglo_ubicacion."',
	  		 '".$arreglo_modelo."',
	    	 '".$arreglo_idmarca."',
	    	 '".$arreglo_serial."'
			 ) as codigo";
	

	
	$resultado_set = pg_exec($conexion ,$sql);
	
	if($resultado_set === false){
		echo utf8_decode("Error al generar la nota de entrega ");
		error_log(pg_last_error($conexion));
		exit;
	}
	
    if ($row=pg_fetch_array($resultado_set))
	{
      $codigo=trim($row['codigo']);
      
      /////////////////////////////////cadena/////////accion luego de generar codigo
      $params['ne_id'] = $codigo;
      if ($_SESSION ['user_perfil_id'] == PERFIL_ALMACENISTA) 
      {
      $params['PerfilSiguiente'] = PERFIL_ANALISTA_BIENES;
      }
      else
      {
      $params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
      }
      $params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSigiente']);
       
      $cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
       
      $params['CadenaGrupo'] = $cadenaIdGrupo;
      $params['DependenciaTramita'] = $param['Dependencia'];
      $params['presAnno'] = $_SESSION['an_o_presupuesto'];
       
       
       
      $dateTime = new DateTime();
       
      $fechax = (String) $dateTime->format('d/m/Y h:m:s');
       
      //echo $fechax;
      
      ////////////////////////ingreso en doc genera de los datos
      
      $data['docg_id'] = $params['ne_id'];
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
	
	//echo "Codigo=".$codigo;
?>
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
