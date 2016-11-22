<?
ob_start();
require_once("../../../includes/conexion.php");
require_once("../../../includes/fechas.php");
//require_once("../../includes/arreglos_pg.php");
	  
 if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
 {
   header('Location:../../../index.php',false);
   ob_end_flush(); 
   exit;
 }
	
  ob_end_flush(); 

  $fec=trim($_POST['fecha_notificacion']);
  $valido=false;
  $arreglo_vacio='{""}';
   
  if ($_POST['accion']==1){//Reparado
   	if ($_POST['paso5']=="custodia"){
  	  $activos="{".$_POST['sbn']."}";   		
   	  $arreglo_cantidad = '{0}';
  	  $arreglo_medida='{"1"}';
  	  $motivo="";
  	 
   	  $sql =  "Select * from sai_insert_bienes_custodia ('".$arreglo_vacio."','".$arreglo_cantidad."','";
	  $sql .= $motivo."','".$_SESSION['login']."','".$arreglo_medida."', '".$_SESSION['user_depe_id']."','".$activos."',1) as ingresado ";
	  $resultado = pg_query($conexion,$sql);
	  $valido=$resultado;
	  if ($row=pg_fetch_array($resultado))
	  {
       $codigo_custodia=$row['ingresado'];
	  }
	  //BUSCAR EL ACTA DONDE DIGA I ASOCIADO AL ACTA DE SALIDA Y CLAVE_BIEN DEL ACTIVO
      $consulta="select t1.asbi_id from sai_bien_asbi_item t1,sai_bien_asbi t2 where t1.asbi_id=t2.asbi_id and t2.esta_id<>15 and clave_bien =".$_POST['clave_bien']."";
      $resultado_consulta = pg_query($conexion,$consulta);
   	  if ($rowc=pg_fetch_array($resultado_consulta))
	  {
       $acta_salida=$rowc['asbi_id'];
	  }
	  $sql="UPDATE sai_bien_asbi_item SET acta_asociada='".$_POST['id_acta']."', estado_activo='D' WHERE asbi_id='".$acta_salida."' and clave_bien=".$_POST['clave_bien']."";
	  $resultado = pg_query($conexion,$sql);
	  
	  $sql="UPDATE sai_biin_items SET esta_id=41, ubicacion=2 WHERE clave_bien=".$_POST['clave_bien']."";
	  $resultado = pg_query($conexion,$sql);
   	}
	  $hoy = cambia_fecha_iso($_POST['fecha_cierre']); 
	  $sql="UPDATE sai_bien_garantia SET esta_id=35,usua_login_cierre='".$_SESSION['login']."', fecha_cierre='".$hoy."', accion_tomada='REPARADO', actas_asociadas='".$codigo_custodia."'
	  , observaciones_cierre='".$_POST['observaciones']."'
	  WHERE acta_id='".$_POST['id_acta']."'";
	  $resultado = pg_query($conexion,$sql);
   	  $valido=true;
  }else{ //REEMPLAZO DEL EQUIPO
  	
  	  $activos="{".$_POST['txt_bien_nacional']."}";

  	  //1. Desincorporar SBN, BUSCAR EL ACTA DONDE DIGA I ASOCIADO ALACTA DE SALIDA Y CLAVE_BIEN DEL ACTIVO
      $consulta="select t1.asbi_id from sai_bien_asbi_item t1,sai_bien_asbi t2 where t1.asbi_id=t2.asbi_id and t2.esta_id<>15 and clave_bien =".$_POST['clave_bien']."";
      $resultado_consulta = pg_query($conexion,$consulta);
   	  if ($rowc=pg_fetch_array($resultado_consulta))
	  {
       $acta_salida=$rowc['asbi_id'];
	  }
	  $sql="UPDATE sai_bien_asbi_item SET acta_asociada='".$_POST['id_acta']."', estado_activo='D' WHERE asbi_id='".$acta_salida."' and clave_bien=".$_POST['clave_bien']."";
	  $resultado = pg_query($conexion,$sql);
    	
  	  //2. Ingresar nuevo activo,Generar Acta de entrada
  		$consulta_activo="SELECT * FROM sai_bien_inco t1, sai_biin_items t2 WHERE t1.acta_id=t2.acta_id and clave_bien=".$_POST['clave_bien']."";
  		$resultado_activo = pg_query($conexion,$consulta_activo);
  		 if ($rowa=pg_fetch_array($resultado_activo)){
  		 	$obs="Reemplazo segun acta de garantia No. ".$_POST['id_acta'];
		    $arreglo_obs='{"'.$obs.'"}';
			$arreglo_bien_id='{"'.$rowa['bien_id'].'"}';
			$arreglo_marca_id='{"'.$rowa['marca_id'].'"}';
			$arreglo_valor='{"'.$rowa['precio'].'"}';
			$arreglo_garantia='{"'.$rowa['garantia'].'"}';
		    $arreglo_fecha_ent="";
		    $arreglo_fecha_ing="{".cambia_ing($_POST['fecha_ing'])."}";
		    $dos='{"2"}';
		    $modelo="{".$_POST['txt_modelo']."}";
		    $serial="{".$_POST['txt_serial']."}";
		    
  		 	$sql="	Select * From sai_insert_inco_bienes(
			'".$_SESSION['user_depe_id']."','".$_SESSION['login']."','".$arreglo_vacio."','6',
			'".$activos."','".$arreglo_bien_id."','".$dos."','".$arreglo_marca_id."',
			'".$modelo."','".$serial."','".$arreglo_valor."',
			'".$arreglo_obs."','".$arreglo_garantia."','".$arreglo_fecha_ing."',
			'".$rowa['depe_solicitante']."','".$rowa['num_licitacion']."','".$rowa['pcta_id']."','".$rowa['proveedor']."')";
  		 	
		    $resultado_set = pg_exec($conexion ,$sql);
			$row = pg_fetch_array($resultado_set,0); 
			if ($row[0] <> null)
	 		{
			 $codigo_entrada=$row[0];
			 $valido=true;
			}else{$msj_entrada="No pudo generarse el acta de entrada";}
		}
  		 
   	//3. Generar acta de salida
  	if ($_POST['paso3']=="salida"){
  		$consulta_clave="SELECT clave_bien FROM sai_biin_items WHERE etiqueta='".$_POST['txt_bien_nacional']."'";
  		$result_clave=pg_query($conexion,$consulta_clave);
  		if ($rowc=pg_fetch_array($result_clave)){
  			$clave=$rowc['clave_bien'];
  		}
  		
  		$consulta_activo="SELECT * FROM sai_bien_asbi t1, sai_bien_asbi_item t2 WHERE t1.asbi_id=t2.asbi_id and clave_bien=".$_POST['clave_bien']."";
  		$resultado_activo = pg_query($conexion,$consulta_activo);
  		 if ($rowa=pg_fetch_array($resultado_activo)){
  		 	 $destino=$rowa['asbi_destino'];
  		 	 $info=$rowa['infocentro'];
  		 	 $arreglo_vacio='{}';
  		     $activos="{".$clave."}";
  		 	 $ubicacion=$rowa['ubicacion'];
  		 	 
  		 	 $sql="Select * From sai_insert_asbi(
			 '".$_SESSION['user_depe_id']."',
			 '".$_SESSION['login']."',
			 '".$destino."',
			 '".$info."',
			 '".$arreglo_vacio."', 
			 '".$arreglo_vacio."',
			 '".$arreglo_vacio."',
			 '".$activos."',
			 '".$rowa['solicitante']."',
			 '".$ubicacion."',2
			 ) as codigo";

	$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar los activos, verifique que el número del activo no se encuentre registrado"));
    if ($row=pg_fetch_array($resultado_set))
	{
	  $codigo_clave=explode ("*" , $row['codigo']);
      $codigo_salida=trim($codigo_clave[0]);}
	 }else{
  		 	$msj_salida="No se registró la salida por no tener un acta previa";
  		 }
  		
  	}
  		 
  	//4. Generar custodia
  	if ($_POST['paso4']=="custodia"){
   		
   	  $arreglo_cantidad = '{0}';
  	  $arreglo_medida='{"1"}';
  	  $motivo="";
  	 
   	  $sql =  "Select * from sai_insert_bienes_custodia ('".$arreglo_vacio."','".$arreglo_cantidad."','";
	  $sql .= $motivo."','".$_SESSION['login']."','".$arreglo_medida."', '".$_SESSION['user_depe_id']."','".$activos."',1) as ingresado ";
	  $resultado = pg_query($conexion,$sql);
	  $valido=$resultado;
	  if ($row=pg_fetch_array($resultado))
	  {
       $codigo_custodia=$row['ingresado'];
	  }
   	}
   	 if ($codigo_salida<>null)
   	  $actas_asociadas=$codigo_entrada.",".$codigo_salida;
   	 
   	 if ($codigo_custodia<>null)
   	  $actas_asociadas=$codigo_entrada.",".$codigo_custodia;
   	  
  	  $hoy = cambia_fecha_iso($_POST['fecha_cierre']); 
	  $sql="UPDATE sai_bien_garantia SET esta_id=35,usua_login_cierre='".$_SESSION['login']."', fecha_cierre='".$hoy."', accion_tomada='REEMPLAZADO', actas_asociadas='".$actas_asociadas."',
	  observaciones_cierre='".$_POST['observaciones']."' WHERE acta_id='".$_POST['id_acta']."'";
	  $resultado = pg_query($conexion,$sql);
   	  $valido=true;
  	
  	
  }
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<script languaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?if ($valido){?>
<form action="" name="form1" id="form1" method="post">
  <table width="550" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
    <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"> <span class="normalNegroNegrita">Registro de caso</span> </td>
    </tr>
    <tr>
      <td class="normalNegrita">N&deg; acta:</td>
	  <td><div align="left" class="normalNegro"><?echo $_POST['id_acta'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Serial del activo:</td>
	  <td><div align="left" class="normalNegro"><?php echo $_POST['serial'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita"> Fecha de reporte:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['fecha_reporte'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">N&deg; de ticket o reporte:</td>
	  <td><div align="left" class="normalNegro"><?echo $_POST['ticket'];?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Datos del servicio t&eacute;cnico: </td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['servicio_tecnico'];?></div></td>
    </tr>
    <tr>
	  <td class="normalNegrita">Fecha de visita del soporte t&eacute;cnico:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['fecha_visita'];?></div></td>
    </tr>
        <tr>
	  <td class="normalNegrita">Fecha de cierre:</td>
      <td><div align="left" class="normalNegro"><?php echo $_POST['fecha_cierre'];?></div></td>
    </tr>
     <?php if ($codigo_entrada<>null){?>
    <tr>
	  <td class="normalNegrita">Acta de entrada:</td>
      <td><div align="left">
    <span class="normalNegrita"> <a href="javascript:abrir_ventana('../inco_pdf.php?codigo=<?php echo $codigo_entrada; ?>')" class="copyright"><?php echo $codigo_entrada; ?> </a> </span><br>  
    </div></td>
    </tr><?php }else{?>
       <tr>
	  <td class="normalNegrita">Acta de entrada:</td>
      <td><div align="left">
    <span class="normalNegrita"><?php echo $msj_entrada;?></span><br>  
    </div></td>
    </tr>
     <?php } if ($codigo_salida<>null){?>
    <tr>
	  <td class="normalNegrita">Acta de asignaci&oacute;n y salida:</td>
      <td><div align="left">
    <span class="normalNegrita"> <a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php echo $codigo_salida; ?>&tipo=a')" class="copyright"><?php echo $codigo_salida; ?> </a> </span>
    <span class="normalNegrita"> <a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php echo $codigo_salida; ?>&tipo=s')" class="copyright"><?php echo "s-".substr($codigo_salida, 2); ?> </a> </span><br>  
    </div></td>
    </tr><?php }else{?>
    <tr>
	  <td class="normalNegrita">Acta de asignaci&oacute;n y salida:</td>
      <td><div align="left" class="normalNegrita"><?php echo $msj_salida;?></div></td>
    </tr>    
    <?php } if ($codigo_custodia<>null){?>
    <tr>
	  <td class="normalNegrita">Acta de custodia:</td>
      <td><div align="left">
    <span class="normalNegrita"> <a href="javascript:abrir_ventana('../custodia_pdf.php?codigo=<?php echo $codigo_custodia; ?>')" class="copyright"><?php echo $codigo_custodia; ?> </a> </span><br>  
    </div></td>
    </tr><?php }?>
    
  </table>
<?}
   if ($valido==false)	
    {?> <br><br><br>
   <table width="76%" border="0" align="center"  class="tablaalertas">
    <tr>
       <td height="18" colspan="3" class="normal"><div align="center">
       <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
       <img src="imagenes/vineta_azul.gif" width="11" height="7" />No se puede efectuar el reporte del caso.
    	</tr>
    <tr><tr><TD height="10"></TD>
    </tr>
  </table>
<?php }?>

</form>
</body>
</html>
