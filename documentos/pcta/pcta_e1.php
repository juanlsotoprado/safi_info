<?php
  session_start();
  require_once("includes/conexion.php");
	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
	header('Location:../index.php',false);
   	ob_end_flush(); 
	exit;
  }
	 
  require_once("includes/fechas.php");
  $fecha=trim($_POST['fecha']);
  $prioridad = $_POST['slc_prioridad'];
  $dependencia = $_POST['opt_depe'];
  $tp_imputacion = $_POST['chk_tp_imputa'];
  $imp_acc_pp=trim($_POST['txt_cod_imputa']);  //Cod. del Proyecto o de la Accion Central
  $acc_esp=trim($_POST['txt_cod_accion']);   //Cod. de la Accion Especifica
  $reserva=trim($_POST['num_reserva']);
  $rif_sugerido=trim($_POST['rif_sugerido']);
  $fecha_pcta=cambia_ing($fecha);

  //Para las imputaciones
  $largo=trim($_POST['hid_largo']);
  $total_imputacion=$largo;
  $j=0;
  $monto_solicitado=0;
  for($i=1; $i<$largo; $i++)
  {  
   $matriz_imputacion[$j]=$_POST['rb_ac_proy'.$i]; 
   $matriz_acc_pp[$j]=$_POST['txt_id_p_ac'.$i];//$imp_acc_pp; 
   $matriz_acc_esp[$j]=$_POST['txt_id_acesp'.$i];//$acc_esp; 
   $matriz_sub_esp[$j]=$_POST['txt_id_pda'.$i];  
   $matriz_uel[$j]=$_POST['txt_id_depe'.$i]; 
   $matriz_monto[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$i]);
   $monto_solicitado=$monto_solicitado+$_REQUEST['txt_monto_pda'.$i];
   $j++;
  }
    $total_imputacion=$j;

	require_once("includes/arreglos_pg.php");

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); 
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); 
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_uel=convierte_arreglo($matriz_uel); 
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); 
	

	 if ($tp_imputacion==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
	 }else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

	$resultado_query= pg_exec($conexion,$query);
	if ($resultado_query){
	  while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
	  }
	}
		 
    $disponible=array($largo);
    $pres_anno=$_SESSION['an_o_presupuesto'];
    $disponibilidad=true;
    $valido=true;
    
	for ($i=0; $i<$total_imputacion; $i++) {   
		$sqla="select * from sai_pres_consulta_disp(".$pres_anno.",'". $matriz_imputacion[$i]."','".$matriz_acc_pp[$i]."','".$matriz_acc_esp[$i]."','". $matriz_sub_esp[$i]."','". $matriz_uel[$i]."',".$matriz_monto[$i].") as monto_dispo ";
		$resultado_dispo = pg_exec($conexion ,$sqla);
		$valido=$resultado_dispo;
		if ($valido)
		{
			$row = pg_fetch_array($resultado_dispo,0); 
			$disponible_monto=$row[0];
			if (round($disponible_monto*100)/100<0)
			{
				$disponible[$i]=false;
				$disponibilidad=false;
			}
			else
				{
				   $disponible[$i]=true;
				}
		}
	} 

if ($disponibilidad && $valido)
{ 

$login_destino="";
$presi=$_SESSION['presidente'];
$dir_ej=$_SESSION['director_ej'];
 switch($_POST["pcuenta_destino"])
{
	case "01"://Presidencia
		$sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_cedula as usua_login','esta_id=1 and carg_fundacion='||'''$presi''','',2) resultado_set(usua_login varchar)";
		$result=pg_query($conexion,$sql_solicitante);
		$destino="Presidencia";
		break;
	case "02":
		$sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_cedula as usua_login','esta_id=1 and carg_fundacion='||'''$dir_ej''','',2) resultado_set(usua_login varchar)";
		$result=pg_query($conexion,$sql_solicitante);
		$destino=utf8_decode("Dirección Ejecutiva");
		break;
	case "03":
		$sql_solicitante="SELECT * FROM  sai_any_tabla('sai_empleado','empl_cedula as usua_login','esta_id=1 and (carg_fundacion= '||'''$presi'''||' or carg_fundacion='||'''$dir_ej'')') resultado_set(usua_login varchar)";
	    $result=pg_query($conexion,$sql_solicitante);
		$destino=utf8_decode("Presidencia/Dirección Ejecutiva");	
		break;
}

		if($row=pg_fetch_array($result))
		{
		 $login_destino=$row["usua_login"];
		}

		if (strpos($destino,"/")){
		 $row=pg_fetch_array($result);
		 $login_destino=$login_destino."/".$row["usua_login"];
		}

     $descrip_sin_tags =$_POST['pcuenta_descripcionVal'];
     

     $sql  = "select * from  sai_insert_pcuenta('".trim($_POST['pcuenta_asunto'])."', '"; 
	 $sql .= $descrip_sin_tags. "' , '".$_POST['pcuenta_solicita']."','";
	 $sql .= trim($login_destino) . "','" . $_SESSION['login'] ."', '" .$_POST['opt_depe'] ."', '";
	 $sql .= $_POST['observaciones']."','".$_POST['justificacion']."','".$_POST['convenio']."', '";
	 $sql .= $_POST['cond_pago']."', '".$monto_solicitado."', '";
	 $sql .= $_POST['slc_prioridad'] ."','".$_POST['num_reserva']."','".$_SESSION['user_depe_id']."','".$_POST['presentado_por']."','".$_POST['op_recursos']."','".$_POST['garantia']."','".$fecha_pcta."','".$rif_sugerido."','".$_POST['pcta_asociado']."') As resultado_set(varchar)";

	 $resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar el Punto de Cuenta");
	
	$row = pg_fetch_array($resultado_set); 
	$codigo_pcta=$row[0];	
	$cod_doc = $codigo_pcta;

	if ($_POST['op_recursos']==$_SESSION['uno'])
	{
		$anno_pres=$_SESSION['an_o_presupuesto'];
        $sql_imputa = "select * from sai_insert_pcta_imputa('".$codigo_pcta."','$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','".$arreglo_uel."') as resultado_ser(varchar)";
		$resultado_set = pg_exec($conexion ,$sql_imputa) or die("Error al ingresar las Imputaciones Presupuestarias");
	}
	$estado_doc=10;
	$prioridad_doc=$prioridad;
		
	$sql = "SELECT * FROM sai_buscar_grupos_perfil('$user_perfil_id') as resultado(grupo_id int4)";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	while ($row = pg_fetch_array($resultado)) {
		$user_grugin_destinopos_id .= ",''".trim($row['grupo_id'])."''";
		$user_grupos_id .= ",''".trim($row['grupo_id'])."''";
	}
	//Borrar 1era coma
	$user_grupos_id = "{".substr($user_grupos_id,1)."}";

	//Buscar las opciones seg�n la cadena 			
	$sql_op = "SELECT * FROM sai_buscar_opciones_cadena('$request_id_tipo_documento','1','".$user_grupos_id."',0,'',0) as resultado(wfop_id int4, wfob_id_sig int4, wfca_id_hijo int4, wfca_id_padre int4)";
	$resultado = pg_query($conexion,$sql_op) or die("Error al mostrar");			
	if ($row = pg_fetch_array($resultado)) {				
	  $id_objeto_sig_p = trim($row['wfob_id_sig']);
	}

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
	
	$psolicita=$_POST['presentado_por'];
    $sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_nombres, empl_apellidos, carg_fundacion, depe_cosige','empl_cedula ='||'''$psolicita''','',2) resultado_set(empl_nombres varchar, empl_apellidos varchar, carg_fundacion varchar, depe_cosige varchar)";
    $resultado_solicitante = pg_query($conexion,$sql_solicitante);
	if ($row = pg_fetch_array($resultado_solicitante)) 
	{
		$cargo = $row["carg_fundacion"];
		$depe_solicitante= $row["depe_cosige"];
		//$nombre_solicita =$row["empl_nombres"];
		//$apellido_solicita =$row["empl_apellidos"];
	}
	if (($cargo==$_SESSION['jefe']) || ($cargo==$_SESSION['coord_nac'])  || ($cargo==$_SESSION['coordinador'])){
	  $cargo_solicitante=$cargo."000";
	}else{
 		  $cargo_solicitante=$cargo.$depe_solicitante;
		 }
		
    $sql_grupo="SELECT * FROM sai_seleccionar_campo('sai_wfgrupo','wfgr_id','wfgr_perf='||'''$cargo_solicitante''','',2) resultado_set(wfgr_id int4)";
	$resultado_set=pg_query($conexion,$sql_grupo) or die("Error al consultar perfil");
	if($row=pg_fetch_array($resultado_set))
	{
	 $grupo_general_p=$row['wfgr_id'];
	}

	$sql_id_cadena="SELECT wfca_id FROM sai_wfgrupo t1, sai_wfcadena t2 WHERE wfgr_perf='".$cargo_solicitante."' and docu_id='pcta' and wfob_id_sig='4' and t2.wfgr_id='".$grupo_general_p."' and depe_id='".$_SESSION['user_depe_id'] ."'";
	$resultado=pg_query($conexion,$sql_id_cadena);
	if ($row=pg_fetch_array($resultado)){
	 $id_hijo_p=$row['wfca_id'];
	}

	//Buscar grupo que debe realizar la accion siguiente
	$sql = " SELECT * FROM sai_buscar_grupo_obj('$request_id_tipo_documento','$id_objeto_sig_p','$id_hijo_p') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar el grupo");
	if ($row = pg_fetch_array($resultado)) {
	 $grupo_general_p = $row["resultado"];
	}
	
	//Buscar perfiles del grupo
	$perfiles_general_p = "";
	$sql = " SELECT * FROM sai_buscar_perfil_grupo('$grupo_general_p') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$perfiles_general_p = $row["resultado"];
	}

	$presentado=$_POST['presentado_por'];
    $sql_presentado="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_nombres, empl_apellidos, carg_fundacion, depe_cosige','empl_cedula ='||'''$presentado''','',2) resultado_set(empl_nombres varchar, empl_apellidos varchar, carg_fundacion varchar, depe_cosige varchar)";
	$resultado_presentado = pg_query($conexion,$sql_presentado);
	if ($rowp = pg_fetch_array($resultado_presentado)) {
		$nombre_presentado =$rowp["empl_nombres"];
		$apellido_presentado =$rowp["empl_apellidos"];
	}


	if (($cargo==$_SESSION['gerente']) || ($cargo==$_SESSION['director_ej']) || ($cargo==$_SESSION['director'])){
		$grupo_particular_p = $cargo.$dependencia_solicitante;
		$grupo_particular = $cargo.$dependencia_solicitante ;	

	}else{
		$grupo_particular_p = substr($perfiles_general_p,0,2).$depe_solicitante;
		$grupo_particular = substr($perfiles_general_p,0,2).$depe_solicitante ;	
	      }
	      
	/*********************************************************************** 
	 * Borrar cuando se cree el director de talento humano
	 ***********************************************************************/
	
	if(substr($_SESSION['user_perfil_id'],0,2) == "37" && substr($_SESSION['user_perfil_id'],2,3) == "500")
	{
		$id_hijo_p = "115";
		$grupo_particular_p = "46500";
	}
	
	/*********************************************************************** 
	 * Fin Borrar cuando se cree el director de talento humano
	 ***********************************************************************/

	$sql = " SELECT * FROM sai_insert_doc_generado('$cod_doc','$id_objeto_sig_p','$id_hijo_p','$user_login','$user_perfil_id',$estado_doc,$prioridad_doc,'$grupo_particular_p','$reserva') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$inserto_doc = $row["resultado"];
	}	
	

	include("includes/respaldos_e1.php");
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Ingresar Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </script>
</head>
<body>
<table width="485" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita" align="center">PUNTO DE CUENTA</td>
  </tr>
  <tr> 
	<td height="28" colspan="2" valign="midden" class="normal" align="left"> <strong>Punto de cuenta: <?php echo $cod_doc; ?></strong>			</td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> 
			    Fecha: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><? echo($fecha);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong>Preparada para: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><?	echo($destino);	?></div>		</td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Elaborado por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo($_SESSION['solicitante']);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Solicitado por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo ($nombre_solicita." ".$apellido_solicita);?></div></td>
  </tr>
  <tr>
	<td width="127"><div align="left" class="normal"><strong> Presentado por: </strong></div></td>
	<td height="12" align="left"><div align="left" class="normalNegro"><? echo ($nombre_presentado." ".$apellido_presentado);?></div></td>
  </tr>
  <tr>
    <td height="12"><div align="left" class="normal"><strong>Dependencia que tramita:</strong></div></td>
    <?php
	  $sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$dependencia''','',2) resultado_set(depe_nombre varchar)";
	  $res_q=pg_exec($sql_str);		  
    ?>
	<td><span class="normalNegro"><?if ($depe_row=pg_fetch_array($res_q)) { 
	   echo $depe_row['depe_nombre'];}?></span></td></tr>
  <tr class="normal"> 
    <td valign="midden" align="left"><strong>Prioridad:</strong></td>
    <td valign="midden" class="normalNegro"> 
     <select name="slc_prioridad" class ="normal" disabled>
       <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
       <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
       <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
     </select>
    </td>
  </tr>
  <tr>
	<td height="35">
	 <div align="left" class="normal"><strong> Asunto:</strong></div>
	 <div align="center" class="normal"></div>		</td>
	<td><span class="normalNegro">
	<?
	  echo($_POST["nom_asunto"]);
	  if ($_POST['pcta_asociado']<>"0"){
		echo " Asociado al "."".$_POST['pcta_asociado'];
	  }
    ?></span></td>
  </tr>
  <tr>
	<td height="35">
	  <div align="left" class="normal"><strong>Descripci&oacute;n:</strong></div></td>
	<td><span class="normalNegro">
	  <? echo($_POST["pcuenta_descripcionVal"]);?> </span></td>  	
  </tr>
  <tr>
    <td><div align="left" class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
    <td><span class="normalNegro"><?echo $_POST['justificacion']?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Lapso de Convenio/Contrato:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['convenio']?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Garant&iacute;a:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['garantia']?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['rif_sugerido']?></span></td></tr>
  <tr>
    <td><div align="left" class="normal"><strong>Condiciones de Pago:</strong></div></td>
	<td><span class="normalNegro"><?echo $_POST['cond_pago']?></span></td></tr>
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
    	       {?>
                 <td  class="peq" align="left" width="17%"><div align="center"><input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
                 <td  class="peq" align="left" width="19%"><div align="center"><input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
                 <td  class="peq" align="left" width="10%"><div align="center"><input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
                 <td  class="peq" align="left" width="15%"><div align="center"><input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_sub_esp[$ii];?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al valor agregado";}?>"/></div></td>
                 <td  class="peq" align="left" width="39%"><div align="center"><input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" /></div></td>
             </tr>
             <?php 
		       }
		     ?>
           </table></td>
           </tr>
     </table>
<tr><td class="normal" align="center" colspan="2">
<a href="javascript:abrir_ventana('documentos/pcta/pcta_detalle.php?codigo=<?echo $cod_doc;?>&esta_id=En Transito')">Ver detalle</a>
</td></tr>
<br>
<?
require_once('includes/respaldos_mostrar.php');
?>
<tr>
  <td height="18" colspan="2">&nbsp;</td>
</tr>
</table>
<?php }
if ($disponibilidad==false && $valido)
{ ?>
<table width="76%" border="0" align="center"  background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr>
	<td height="18" colspan="3" class="normal"><div align="center" class="normalNegro">No se puede registrar el punto de Cuenta,la dependencia debe tramitar un traspaso presupuestario con la Direcci&oacute;n de Planificaci&oacute;n, Presupuesto y Control. Las siguientes partidas no poseen disponibilidad:</div></td>
  </tr>
  <tr class="td_gray">
	<td width="89%" align="center"  class="peqNegrita"><div align="center">Imputaci&oacute;n Presupuestaria </div></td>
  </tr>
  <tr>
	<td  class="normal" align="center" ><div align="center">
	  <table width="72%" border="0" align="center" >
		<tr>
		  <td  class="peqNegrita"align="left">Proy/Acc</td>
		  <td  class="peqNegrita" align="left">Acc Esp. </td>
		  <td width="10%"align="left"  class="peqNegrita">Dependencia</td>
		  <td width="27%"align="left"  class="peqNegrita">Partida</td>
		  <td width="34%"align="left"  class="peqNegrita">Monto</td>
		</tr>
		<tr>
		<?php  
		for ($i=0; $i<$total_imputacion; $i++)
		{ 
			if ($disponible[$i]==false)
			{?>
				  <td  class="normal" align="left" width="15%">
					<input type="text" class="peq"  size="10" maxlength="15"   valign="right"  align="right" value="<?php echo trim($matriz_acc_pp[$i]);?>" readonly="true"/>
				  </td>
				  <td  class="normal" align="left" width="14%">
					<input type="text" class="peq"  size="10" maxlength="15"   valign="right"  align="right" value="<?php echo trim($matriz_acc_esp[$i]);?>" readonly="true"/>
				  </td>
				  <td  class="normal" align="left" width="10%">
					<input type="text" class="peq"  size="5" maxlength="5"   valign="right"  align="right" value="<?php echo trim($matriz_uel[$i]);?>" readonly="true"/>
				  </td>
				  <td  class="normal" align="left" width="27%">
					<input type="text" class="peq"  size="15" maxlength="15"   valign="right"  align="right" value="<?php echo trim($matriz_sub_esp[$i]);?>" readonly="true"/>
				  </td>
				  <td  class="normal" align="left" width="34%">
					<input type="text" class="peq"  size="15" maxlength="15"   valign="right"  align="right" value="<?php echo(number_format($matriz_monto[$i],2,'.',',')); ?>" readonly="true"/>
				  </td>
	    </tr>														
	   <?php }
		}?>
	</table>
	  </div></td>
  </tr>
  <tr>
    <td><div align="center"><a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></div></td>
  </tr>
  <tr>
	<td></td>
  </tr>
</table>
	
<?php }?>

</body>
</html>

