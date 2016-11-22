<?php
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");
  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
	
 $fecha=trim($_POST['fecha']);
 $prioridad = $_POST['slc_prioridad'];
 $dependencia = $_POST['opt_depe'];
 $tp_imputacion = $_POST['chk_tp_imputa'];
 $imp_acc_pp=trim($_POST['txt_cod_imputa']);  //Cod. del Proyecto o de la Accion Central
 $acc_esp=trim($_POST['txt_cod_accion']);   //Cod. de la Accion Especifica
 $reserva=trim($_POST['num_reserva']);
 $fecha_pcta=cambia_ing($fecha);
 $cg=$_POST['centro_gestor'];
 $cc=$_POST['centro_costo'];
 $anno_pres=$_SESSION['an_o_presupuesto'];
 //$anno_pres = 2013;

//Arreglo de Partidas
 $largo=trim($_POST['hid_largo']);
 $total_imputacion=$largo;
 $j=0;
 $monto_solicitado=0;
	for($i=0; $i<$largo; $i++)
	{  
		$matriz_imputacion[$j]=$tp_imputacion; 
		 
		if ($tp_imputacion==1){
		  $sql_acc="SELECT proy_id  as proy_acc,paes_id as accion FROM  sai_proy_a_esp WHERE centro_gestor ='".$cg."' and centro_costo='".$cc."' and pres_anno=".$anno_pres."";
		}else{
		  $sql_acc="SELECT acce_id as proy_acc,aces_id as accion FROM  sai_acce_esp WHERE centro_gestor ='".$cg."'  and centro_costo='".$cc."' and pres_anno=".$anno_pres."";			
		}
  		$result = pg_query($conexion,$sql_acc);		
		if ($row = pg_fetch_array($result)) {
		 $matriz_acc_pp[$j]=$row["proy_acc"];
		 $matriz_acc_esp[$j]=$row["accion"];//$cg;
		}
	//	$matriz_acc_esp[$j]=$cg;
		$matriz_sub_esp[$j]=$_POST['txt_id_pda'.$j];  
		$matriz_uel[$j]=$dependencia; 
	  	$matriz_monto[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$j])*(-1);
		$monto_solicitado=$monto_solicitado+$_REQUEST['txt_monto_pda'.$j]*(-1);
		$j++;
	  }
		$total_imputacion=$j;
	
	require_once("../../includes/arreglos_pg.php");

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); 
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); 
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_uel=convierte_arreglo($matriz_uel); 
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); 
	
	$psolicita=$_POST['pcuenta_solicita'];
    $sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_nombres, empl_apellidos, carg_fundacion, depe_cosige','empl_cedula ='||'''$psolicita''','',2) resultado_set(empl_nombres varchar, empl_apellidos varchar, carg_fundacion varchar, depe_cosige varchar)";
	$resultado_solicitante = pg_query($conexion,$sql_solicitante);
	if ($row = pg_fetch_array($resultado_solicitante)) 
	{
	 $cargo = $row["carg_fundacion"];
	 $depe_solicitante= $row["depe_cosige"];
	 $nombre_solicita =$row["empl_nombres"];
	 $apellido_solicita =$row["empl_apellidos"];
	}

	$presi=$_SESSION['presidente'];
	$dir_ej=$_SESSION['director_ej'];
	$login_destino="";

	$sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_cedula as usua_login','esta_id=1 and carg_fundacion='||'''$presi''','',2) resultado_set(usua_login varchar)";
	$result=pg_query($conexion,$sql_solicitante);
	$destino="Presidencia";

	if($row=pg_fetch_array($result))
	{
	 $login_destino=$row["usua_login"];
	}
	
  	 $vacio="";
     $uno=1;
     $sql  = "select * from  sai_insert_pcuenta('020','','".$_POST['pcuenta_solicita']."', '"; 
	 $sql .= trim($login_destino) . "','" . $_SESSION['login'] ."', '" .$_POST['opt_depe'] ."', '";
	 $sql .= $_POST['observaciones']."','".$_POST['justificacion']."','".$vacio."', '";
	 $sql .= $vacio."', '".$monto_solicitado."','1', '";
	 $sql .= $_POST['num_reserva']."','".$_SESSION['user_depe_id']."','".$_POST['pcuenta_solicita']."','1','".$vacio."','".$fecha_pcta."','','".$_POST['pcta_asociado']."') As resultado_set(varchar)";
	 $resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar el Punto de Cuenta");
	
	 $row = pg_fetch_array($resultado_set); 
	 $codigo_pcta=$row[0];	
	 $cod_doc = $codigo_pcta;

     $sql_imputa = "select * from sai_insert_pcta_imputa('".$codigo_pcta."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','".$arreglo_uel."') as resultado_ser(varchar)";
     $resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias");
      
     for($i=0; $i<$largo; $i++)
	{
	  if (($_POST['pcta_asociado']=="0")||($_POST['pcta_asociado']==""))
	  $pcta_asociado=$cod_doc;
	  else
	  $pcta_asociado=$_POST['pcta_asociado'];
	  $query = "INSERT into sai_disponibilidad_pcta (partida,monto,pcta_id,pcta_asociado) values ('".$matriz_sub_esp[$i]."','".$matriz_monto[$i]."','".$cod_doc."','".$pcta_asociado."')";
	  $resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al ingresar Disponibilidad Punto de Cuenta"));
	}
	 $estado_doc=10;
	
     $sql = " SELECT * FROM sai_insert_doc_generado('".$codigo_pcta."',99,0,'".$_SESSION['login']."','".$_SESSION['user_perfil_id']."',13,$uno,'','".$reserva."') as resultado ";
	 $resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	 if ($row = pg_fetch_array($resultado)) {
		$inserto_doc = $row["resultado"];
	 }	
	 
	$sqlt  = "select * from  sai_insert_pcta_traza('020','', '"; 
    $sqlt .= $_SESSION['login']."', '".$_POST['opt_depe'] ."', '";
    $sqlt .= $destino."','".$_POST['justificacion']."','".$vacio."','".$vacio."', '";
    $sqlt .= $monto_solicitado."','1', '";
    $sqlt .= $_POST['num_reserva']."','".$_SESSION['user_depe_id']."','1','".$vacio."','";
    $sqlt .= $dependencia."','".$fecha_pcta."', '" .$codigo_pcta."','','";
    $sqlt .= $_POST['pcuenta_solicita']."', '".$_POST['pcta_asociado']."','".$_POST['presentado_por']."','".$_POST['observaciones']."') As resultado_set(varchar)";
    $resultado_set = pg_exec($conexion ,$sqlt) or die("Error al ingresar la traza del punto de cuenta");
  		
  	$sql_imputa = "select * from sai_insert_pcta_imputa_traza('020','".$codigo_pcta."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','".$arreglo_uel."') as resultado_ser(varchar)";
 	$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias de la traza");
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::LIBERACI&Oacute;N PUNTO DE CUENTA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
</head>
<body>
<table width="485" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="normal"> 
	<td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"><strong>PUNTO DE CUENTA</strong></span>		</td>
  </tr>
  <tr> 
	<td  width="127" valign="midden" class="normal" align="left"><strong>Punto de Cuenta:</strong>			
	</td>
	<td width="346"><div align="left" class="normalNegro"><?	echo($cod_doc);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Fecha: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><?	echo($fecha);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Preparada a: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><?	echo($destino);	?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Elaborado Por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo($_SESSION['solicitante']);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Solicitado Por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo ($nombre_solicita." ".$apellido_solicita);?></div></td>
  </tr>
  <tr>
	<td height="12"><div align="left" class="normal"><strong>Unidad/Dependencia:</strong></div></td>
	<?php
	  $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$dependencia''','',2) resultado_set(depe_nombre varchar)";
	  $res_q=pg_exec($sql_str);		  
	?>
	<td><span class="normalNegro"><?if ($depe_row=pg_fetch_array($res_q)) { 
	echo $depe_row['depe_nombre'];}?></span></td></tr>
  <tr class="normal"> 
    <td valign="midden" align="left"><strong>Prioridad:</strong></td>
    <td valign="midden"><span class="normalNegro">Baja</span></td>
  </tr>
  <tr>
	<td height="35"><div align="left" class="normal"> <div align="left"><strong>Asunto:</strong></div></div></td>
	<td><span class="normalNegro">Liberaci&oacute;n Punto de Cuenta</span></td>
  </tr>
  <tr>
    <td><div align="left" class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['justificacion']?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Monto Solicitado:</strong></div></td>
	<td><span class="normalNegro"><?echo $monto_solicitado;?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Observaciones:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['observaciones']?></span></td></tr>
  	<br>
  <tr>
	<td colspan="3"><br>
	 <table width="60%" border="0"  class="tablaalertas" align="center">
       <tr class="td_gray">
         <td  align="center" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria </td>
       </tr>
	   <tr>
         <td class="normal" align="center">
           <table width="100%" border="0">
             <tr>
                <td  class="peqNegrita" align="left"><div align="center">ACC.C/PP</div></td>
                <td  class="peqNegrita" align="left"><div align="center">Acci&oacute;n espec&iacute;fica </div></td>
                <td width="10%" align="left"  class="peqNegrita"><div align="center">Dependencia</div></td>
                <td width="15%" align="left"  class="peqNegrita"><div align="center">Partida</div></td>
                <td width="39%" align="left"  class="peqNegrita"><div align="center">Monto </div></td>
			 </tr>
             <tr>
             <?php
		       for ($ii=0; $ii<$total_imputacion; $ii++)
    	       { ?>
                <td  class="peq" align="left" width="17%"><div align="center"><input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
                <td  class="peq" align="left" width="19%"><div align="center"><input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
                <td  class="peq" align="left" width="10%"><div align="center"><input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
                <td  class="peq" align="left" width="15%"><div align="center"><input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_sub_esp[$ii];?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al valor agregado";}?>"/></div></td>
                <td  class="peq" align="left" width="39%"><div align="center"><input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" /></div></td>
             </tr>
             <?php 
		       }?>
           </table></td>
          </tr>
      </table>

<tr><TD class="normal" align="center" colspan="2">
<a href="javascript:abrir_ventana('../../documentos/pcta/pcta_detalle.php?codigo=<?echo $cod_doc;?>&esta_id=En Transito')">Ver detalle</a>
</TD></tr>
<br>
<tr>
  <td height="18" colspan="2">&nbsp;</td>
</tr>
</table>
</body>
</html>
<?php pg_close($conexion);?>