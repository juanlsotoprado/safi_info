<?php
  ob_start();
  require_once("includes/conexion.php");
	  
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
    header('Location:../index.php',false);
    ob_end_flush(); 
    exit;
  }
$cod_doc = $request_codigo_documento;
	$tp_imputacion = $_POST['chk_tp_imputa'];
 	$largo=trim($_POST['hid_largo']);
	$total_imputacion=$largo;
	$j=0;
	$monto_solicitado=0;


	for($i=0; $i<$largo; $i++)
	{  
		$matriz_imputacion[$j]=$tp_imputacion; 
		$matriz_acc_pp[$j]=$_POST['txt_id_p_ac'.$i]; 
		$matriz_acc_esp[$j]=$_POST['txt_id_acesp'.$i]; 
		$matriz_sub_esp[$j]=$_POST['txt_id_pda'.$i];  
		$matriz_uel[$j]=$_POST['txt_id_depe'.$i]; 
		$matriz_monto[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$i]);
		$monto_solicitado=$monto_solicitado+$_REQUEST['txt_monto_pda'.$i];
		$j++;
	}

	$total_imputacion=$j;

	 if ($largo >0)
   	{
	require_once("includes/arreglos_pg.php");

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);  
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp); 
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp); 
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_uel=convierte_arreglo($matriz_uel); 
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion); 
	}
  	else
  	{
 	 $arreglo_acc_pp = "{}";
	 $arreglo_acc_esp=  "{}";
	 $arreglo_sub_esp=  "{}";
	 $arreglo_monto= "{}";
	 $arreglo_uel="{}";
	 $arreglo_tipo_impu="{}";
        }
        
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
    
	for ($i=0; $i<$largo; $i++) {   
		$validar=1;
   		/*Incorporación resta de disponibilidad*/
		$sqla="select pcta_monto from sai_pcta_imputa where pcta_sub_espe='".$matriz_sub_esp[$i]."' and pcta_id='".$cod_doc."'";
	  	
	  	$resultado = pg_exec($conexion ,$sqla);
	  	if($row=pg_fetch_array($resultado)) {
			if($matriz_monto[$i]<=$row[0]){
			 	$validar=0;
			}
			else {
				$monto_validar=$matriz_monto[$i]-$row[0];
			}
	  	}
	  	else {
  			 $monto_validar=$matriz_monto[$i];
	  	} 	
	    if($validar==1){	   	
			$sqla="select * from sai_pres_consulta_disp(".$pres_anno.",'". $matriz_imputacion[$i]."','".$matriz_acc_pp[$i]."','".$matriz_acc_esp[$i]."','". $matriz_sub_esp[$i]."','". $matriz_uel[$i]."',".$monto_validar.") as monto_dispo ";
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
}

	
	$anno_pres=$_SESSION['an_o_presupuesto'];
	require_once("includes/fechas.php");
	$fecha=trim($_POST['fecha']);
    $fecha_pcta=cambia_ing($fecha);
        
  if ($disponibilidad && $valido)
  { 
    $descrip_sin_tags =$_POST['pcuenta_descripcionVal'];

   	$sql  = "select * from  sai_modificar_pcuenta('".$_POST['pcuenta_asunto']."', '".$descrip_sin_tags. "' , '";
	$sql .= $_POST['pcuenta_solicita']. "', '". $_POST['pcuenta_destino'] ."','" . $_SESSION['login'] ."', '" .$_SESSION['user_depe_id'] ."', '" .$_POST['slc_prioridad'] ."','".$cod_doc."','".$_POST['observaciones']."','".$_POST['justificacion']."','".$_POST['convenio']."','".$_POST['cond_pago']."','".$monto_solicitado."',
    '$anno_pres','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','".$arreglo_monto."','".$arreglo_uel."','".$_POST['garantia']."','".$_POST['presentado_por']."','".$fecha_pcta."','".$_POST['rif_sugerido']."','".$_POST['pcta_asociado']."','".$_POST['op_recursos']."') As resultado_set(varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die("Error al Modificar el Punto de Cuenta");
	
	$row = pg_fetch_array($resultado_set,0); 
	
	if ($row[0] <> null)
	{
		$codigo_pcuenta=$row[0];
		include("includes/respaldos_e1.php");

	 //Buscar las Imputaciones
	 $total_imputacion=0;
	 
	  $sql= " Select * from sai_buscar_pcta_imputacion('".trim($cod_doc)."') as result ";
	  $sql.= " (pcta_id varchar, pcta_acc_pp varchar, pcta_acc_esp varchar, depe_id varchar, pcta_sub_espe varchar, pcta_monto float8, tipo bit)";

	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
 	    $monto_solicitado=0;
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['pcta_acc_pp']); 
				$matriz_acc_esp[$i]=trim($row['pcta_acc_esp']); 
				$matriz_sub_esp[$i]=trim($row['pcta_sub_espe']);
				$matriz_uel[$i]=trim($row['depe_id']); 
				$matriz_monto[$i]=trim($row['pcta_monto']);
		 		$monto_solicitado=$monto_solicitado+$row['pcta_monto'];
				$i++;
			}
	        }
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
		if ($row = pg_fetch_array($resultado_solicitante)) {
			$cargo = $row["carg_fundacion"];
			$depe_solicitante= $row["depe_cosige"];
			$nombre_solicita =$row["empl_nombres"];
			$apellido_solicita =$row["empl_apellidos"];
		}		
			
		$psolicita=$_POST['presentado_por'];
        $sql_solicitante="SELECT * FROM  sai_seleccionar_campo('sai_empleado','empl_nombres, empl_apellidos, carg_fundacion, depe_cosige','empl_cedula ='||'''$psolicita''','',2) resultado_set(empl_nombres varchar, empl_apellidos varchar, carg_fundacion varchar, depe_cosige varchar)";
		$resultado_solicitante = pg_query($conexion,$sql_solicitante);
		if ($row = pg_fetch_array($resultado_solicitante)) {
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
			$grupo_particular_p = $cargo.$depe_solicitante;
			$grupo_particular = $cargo.$depe_solicitante ;	
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

		$sql = " SELECT * FROM	 sai_modificar_doc_genera('$cod_doc','$id_objeto_sig_p','$id_hijo_p','$grupo_particular_p') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
		if ($row = pg_fetch_array($resultado)) {
		   $modifico = $row["resultado"];
		}

  			$estado_doc = 10;
			$sql_doc =" SELECT * FROM sai_modificar_estado_doc_genera('$cod_doc',$estado_doc) as resultado ";			
			$resultado_doc = pg_query($conexion,$sql_doc) or die("Error al mostrar");
			if ($row_doc = pg_fetch_array($resultado_doc)) {
			  $modifico_doc = $row_doc["resultado"];
			}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Modificar Punto de Cuenta</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </script>
</head>
<body codigo_validacion();">
<table width="485" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr  class="td_gray"> 
	<td height="15" colspan="2" valign="midden" class="td_gray">
		<span class="normalNegroNegrita"><strong>PUNTO DE CUENTA</strong></span>		</td>
  </tr>
  <tr> 
	<td height="28" valign="midden" class="normal"><strong>Punto de Cuenta: </strong>	</td>
	<td class="normalNegro"><?php echo $cod_doc; ?></td>
  </tr>
  <tr>
	<td width="127"><div  class="normal"><strong> Fecha: </strong></div></td>
    <td width="346"><div align="left" class="normalNegro"><? echo($fecha);	?></div></td>
	</tr>
	<tr>
	   <td width="127"><div class="normal"><strong> Preparada para: </strong></div></td>
	   <!--  /////////////////OJO NO ESTA MOSTRANDO LOS NOMBRES -->
       <td width="346"><div align="left" class="normalNegro"><?	echo($_POST['pcuenta_destino']);	?></div>		</td>
	</tr>
   	<tr>
	  <td width="127"><div class="normal"><strong> Elaborado Por: </strong></div></td>
	  <td height="12" align="left"><div align="left" class="normalNegro"><? echo($_SESSION['solicitante']);?></div>		</td>
	</tr>
	<tr>
      <td height="32" align="left"><div align="left" class="normal"><strong>Solicitado por:</strong>
	  <?
		$id_depe = substr($_SESSION['user_depe_id'],0,2);
		$sql_solicitante="select empl_cedula, empl_nombres, empl_apellidos from sai_empleado where empl_cedula='".$_POST['pcuenta_solicita']."'";

		$result=pg_query($conexion,$sql_solicitante);
		if($row=pg_fetch_array($result))
		{
		 $solicitante= $row['empl_nombres']." ".$row['empl_apellidos'];?>
		<?}?></div>		</td>
	   <td height="12" align="left"><div align="left" class="normalNegro"><? echo $solicitante;?></div>		</td>
	</tr>
   	<tr>
  	  <td width="127"><div class="normal"><strong> Presentado Por: </strong></div></td>
	  <td height="12" align="left"><div align="left" class="normalNegro">
	  <?
		$id_depe = substr($_SESSION['user_depe_id'],0,2);
		$sql_presentado="select empl_cedula, empl_nombres, empl_apellidos from sai_empleado where empl_cedula='".$_POST['presentado_por']."'";

		$result=pg_query($conexion,$sql_presentado);
		if($row=pg_fetch_array($result))
		{
		
 		 $presentado= $row['empl_nombres']." ".$row['empl_apellidos'];?>
		<?}?>
		<? echo $presentado;?>
		</div>		</td>
	</tr>
   	<tr>
   	  <td><div class="normal"><strong>Dependencia que tramita:</strong></div></td>
	  <?php
			$dep=$_POST['gerencia'];
			$sql_str="SELECT * FROM  sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$dep''','',2) resultado_set(depe_nombre varchar)";
		    $res_q=pg_exec($sql_str);		  
	  ?>
	  <td><span class="normal"><?if ($depe_row=pg_fetch_array($res_q)) { 
	      echo $depe_row['depe_nombre'];}?></span></td></tr>
    <tr class="normal"> 
      <td valign="midden"><strong>Prioridad:</strong></td>
      <td valign="midden"> 
        <select name="slc_prioridad" class ="normalNegro" disabled>
         <option value="1" <?php if ($_POST['slc_prioridad']==1) { echo "selected"; } ?> >Baja</option>
          <option value="2" <?php if ($_POST['slc_prioridad']==2) { echo "selected"; } ?>>Media</option>
          <option value="3" <?php if ($_POST['slc_prioridad']==3) { echo "selected"; } ?>>Alta</option>
        </select></td>
    </tr>
	<tr>
	  <td height="35"><div align="left" class="normal"><strong>Asunto:</strong></div></td>
	  <td><span class="normalNegro">
	  <?  $asunto=$_POST['pcuenta_asunto'];
		 	$sql_asu="SELECT * FROM  sai_seleccionar_campo('sai_pcta_asunt','pcas_nombre,pcas_id','esta_id=1 and  pcas_id='||'''$asunto''','',2) resultado_set(pcas_nombre varchar,pcas_id varchar)";
			$result=pg_query($conexion,$sql_asu);
			if($row=pg_fetch_array($result)){
			$asunto_id_inicial=$row['pcas_nombre'];
		    $asunto_nomb_inicial=$row['pcas_nombre'];
			}
			
			echo $asunto_nomb_inicial; 
			if (($_POST['pcta_asociado']<>'0') && ($_POST['pcta_asociado']<>"")){
					echo " Asociado al "." ".$_POST['pcta_asociado'];
				}
			?></span></td>
	</tr>
	<tr>
	  <td height="35"><div align="left" class="normal"><strong>Descripci&oacute;n:</strong></div>		</td>
	  <td><span class="normalNegro"><? echo($_POST["pcuenta_descripcionVal"]);?></span></td>  	
	</tr>
	<tr>
	  <td><div class="normal"><strong>Justificaci&oacute;n:</strong></div></td>
	  <td><span class="normalNegro"><?echo $_POST['justificacion']?></span></td></tr>
    <tr>
      <td><div class="normal"><strong>Lapso de Convenio/Contrato:</strong></div></td>
	  <td><span class="normalNegro"><?echo $_POST['convenio']?></span></td></tr>
	<tr>
	  <td><div class="normal"><strong>Garant&iacute;a:</strong></div></td>
	  <td><span class="normalNegro"><?echo $_POST['garantia']?></span></td></tr>
    <tr>
      <td><div class="normal"><strong>Rif del Proveedor Sugerido:</strong></div></td>
	  <td><span class="normalNegro"><?echo $_POST['rif_sugerido']?></span></td></tr>	
	<tr>
	  <td><div class="normal"><strong>Condiciones de pago:</strong></div></td>
	  <td><span class="normalNegro"><?echo $_POST['cond_pago']?></span></td></tr>
	<tr>
	  <td><div class="normal"><strong>Monto solicitado:</strong></div></td>
	  <td><span class="normalNegro"><?echo $monto_solicitado;?></span></td></tr>
	<tr>
	  <td><div align="right" class="normal"><strong>Observaciones:</strong></div></td>
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
    		   {
		     ?>
               <td  class="peq" align="left" width="17%"><div align="center"><input name="<?php echo "txt_imputa_proyecto_accion".$ii;?>" type="text" class="normalNegro" id="<?php echo "txt_imputa_proyecto_accion".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/></div></td>
               <td  class="peq" align="left" width="19%"><div align="center"><input name="<?php echo "txt_imputa_accion_esp".$ii;?>"  type="texto" class="normalNegro" id="<?php echo "ctxt_imputa_accion_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/></div></td>
               <td  class="peq" align="left" width="10%"><div align="center"><input name="<?php echo "txt_imputa_unidad".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_unidad".$ii;?>" size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/></div></td>
               <td  class="peq" align="left" width="15%"><div align="center"><input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15" value="<?php echo $matriz_sub_esp[$ii];?>" /></div></td>
               <td class="peq" align="right" width="39%"><input name="<?php echo "txt_imputa_monto".$ii;?>" type="text" class="normalNegro" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" /></td>
             </tr>
             <?php 
		      }?>
           </table></td>
        </tr></table><br>
	<?
	include("includes/respaldos_mostrar.php");
	?>
	<tr> 
    	<td height="18"></td>
      	<td></td>
    </tr>
</table>
<?php }
if ($disponibilidad==false && $valido)
{ ?>
 <table width="76%" border="0" align="center"  background="imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr>
	<td height="18" colspan="3"><div align="center" class="normalNegrita">
	No se puede modificar el punto de cuenta, la dependencia debe tramitar un traspaso presupuestario con la Direcci&oacute;n de Planificaci&oacute;n, Presupuesto y Control. Las siguientes partidas no poseen disponibilidad:</div></td>
  </tr>
  <tr class="td_gray">
	<td width="89%" align="center"  class="peqNegrita"><div align="center">Imputaci&oacute;n presupuestaria </div></td>
  </tr>
  <tr>
	<td  class="normal" align="center" >
	<div align="center">
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
		  <td  class="normal" align="left" width="15%"><input type="text" class="normalNegro"  size="10" maxlength="15"   valign="right"  align="right" value="<?php echo trim($centrog);?>" readonly="true"/></td>
		  <td  class="normal" align="left" width="14%"><input type="text" class="normalNegro"  size="10" maxlength="15"   valign="right"  align="right" value="<?php echo trim($centroc);?>" readonly="true"/></td>
		  <td  class="normal" align="left" width="10%"><input type="text" class="normalNegro"  size="5" maxlength="5"   valign="right"  align="right" value="<?php echo trim($matriz_uel[$i]);?>" readonly="true"/></td>
		  <td  class="normal" align="left" width="27%"><input type="text" class="normalNegro"  size="15" maxlength="15"   valign="right"  align="right" value="<?php echo trim($matriz_sub_esp[$i]);?>" readonly="true"/></td>
		  <td  class="normal" align="left" width="34%"><input type="text" class="normalNegro"  size="15" maxlength="15"   valign="right"  align="right" value="<?php echo(number_format($matriz_monto[$i],2,'.',',')); ?>" readonly="true"/></td>
	    </tr>														
	   <?php }
		}?>
	</table></div></td>
  </tr>
  <tr>
	<td><div align="center"><a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></div></td>
  </tr>
</table>
	
<?php }?>
</body>
</html>
<?php pg_close($conexion);?>