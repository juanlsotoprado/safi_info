<?php 
    ob_start();
	session_start();
	 require_once("includes/conexion.php");
	 include('includes/arreglos_pg.php');
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	ob_end_flush(); 
	
	//Valores de Sesi�n
	$user_perfil_id = $_SESSION['user_perfil_id'];
	$user_login = $_SESSION['login'] ;	
	$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);
	
	$cod_doc = $request_codigo_documento;
	$codigo= $cod_doc;
	$memo_contenido=$_POST['txt_motivo'];
	$partida_IVA=trim($_SESSION['part_iva']);
	
$sql_p="SELECT * FROM sai_sol_pago WHERE sopg_id='".$codigo."'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
  require_once("includes/fechas.php");
  $depe_id=trim($row['depe_solicitante']); //Solicitante
  $tp_sol=trim($row['sopg_tp_solicitud']);
  $sopg_monto=trim($row['sopg_monto']); 
  $sopg_fecha=trim($row['sopg_fecha']);
  $pres_anno=trim($row['pres_anno']);
  $esta_id=trim($row['esta_id']);
  $usua_login=trim($row['usua_login']); //Solicitante
  $sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
  $sopg_bene_tp=trim($row['sopg_bene_tp']);
  $sopg_detalle=trim($row['sopg_detalle']);
  $sopg_observacion=trim($row['sopg_observacion']);
  $factura_num=trim($row['sopg_factura']);
  $factura_control=trim($row['sopg_factu_num_cont']);
  $factura_fecha=cambia_esp(trim($row['sopg_factu_fecha']));
  $compromiso=trim($row['comp_id']);
  
 $sql="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_nombre','depe_id='||'''$depe_id''','',2)
resultado_set(depe_nombre varchar)"; 
$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
if($row=pg_fetch_array($resultado_set_most_p))
{
 $dependencia=trim($row['depe_nombre']); //Solicitante
} 
    
    //Datos del Solicitante
	$sql_so="select * from sai_buscar_usuario('$usua_login','')
	resultado_set(empl_email varchar, usua_login varchar, usua_activo bool,empl_cedula varchar, empl_nombres varchar,
	empl_apellidos varchar,empl_tlf_ofic varchar,carg_nombre varchar,depe_nombre varchar,depe_id varchar,carg_id varchar)";
	$resultado_set_most_so=pg_query($conexion,$sql_so) or die("Error al consultar partida");
	if($rowso=pg_fetch_array($resultado_set_most_so))
	{
		$email=trim($rowso['empl_email']);
		$cedula=$rowso['empl_cedula'];
		$solicitante=$rowso['empl_nombres'].' '.$rowso['empl_apellidos'];
		$cargo=trim($rowso['carg_nombre']);
		$telefono=trim($rowso['empl_tlf_ofic']);
	}
	
	    //Buscar Nombre del Documento al cual se le asocia la solicitud de pago y el nombre del estado actual
	    $sql_d="select * from sai_buscar_datos_sopg('1',4,'','','$codigo','','',0) 
		resultado_set(docu_nombre varchar, esta_id int4, docg_prioridad int2, esta_nombre varchar)"; 
		$resultado_set_most_d=pg_query($conexion,$sql_d) or die("Error al consultar documento");
		if($rowd=pg_fetch_array($resultado_set_most_d))
		{ 
		    $prioridad=$rowd['docg_prioridad'];
		}
		
	//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
		   $nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		   $depe_nombre_bene=trim($rowbe['depe_nombre']);
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
		      $depe_nombre_bene=$dependencia;
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				  $depe_nombre_bene=$dependencia;
			   }
		   }
	
	
}//fin de consultar solicitud de pago

   //buscar los soportes
   $sql= "select * from sai_buscar_sopg_anexos ('".$codigo ."') as resultado ";
   $sql.= "(sopg_id varchar,soan_factura bit , soan_ordc bit , soan_contrato bit, soan_certificacion bit, ";
   $sql.= " soan_recibo bit, soan_ords bit, soan_pcta bit, soan_gaceta bit , soan_informe bit, soan_estimacion bit, soan_otro bit, soan_otro_deta varchar)"; 
   $resultado_set = pg_exec($conexion ,$sql);
   $valido=resultado_set;
		if ($resultado_set)
		{
		 $anexos=array(11);
		  for ($i=0; $i<11; $i++)
   			{		 
    	 		$anexos[$i]=0;
   		     }
		  if (pg_num_rows($resultado_set) >0)
		  {
   		 $row = pg_fetch_array($resultado_set); 
		 $anexos_otros=trim($row['soan_otro_deta']);
		 $anexos[0]=trim($row['soan_factura']);
		 $anexos[1]=trim($row['soan_ordc']);
		 $anexos[2]=trim($row['soan_contrato']);
		 $anexos[3]=trim($row['soan_certificacion']);
		 $anexos[4]=trim($row['soan_recibo']);
	     $anexos[5]=trim($row['soan_ords']);
	     $anexos[6]=trim($row['soan_pcta']);
		 $anexos[7]=trim($row['soan_gaceta']);
		 $anexos[8]=trim($row['soan_informe']);
	     $anexos[9]=trim($row['soan_estimacion']);
	     $anexos[10]=trim($row['soan_otro']);
		 }
 		}

 /*Consulta la tabla de impuestos IVA por documento*/
if ($valido)
  {
   $docu_base=  $codigo;
   $elem_imp_iva=0;
   
   $sql=  " select * from sai_buscar_docu_iva('".trim($docu_base)."') as result  ";
   $sql.= "( docg_id varchar,ivap_porce float4, docg_monto_base float8, docg_monto_iva float8)";
   $resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_imp_iva=pg_num_rows($resultado_set);
			$iva_porce=array($elem_imp_iva);
			$iva_monto=array($elem_imp_iva);
			$subtotal_xx=0;
			$iva_xx=0;
			$ii=0;
 			while($row_iva=pg_fetch_array($resultado_set))	
			 {
			  if  ( $row_iva['ivap_porce']> 0)
			   {
			   $iva_monto[$ii]=trim($row_iva['docg_monto_iva']);
			   $iva_porce[$ii]=trim($row_iva['ivap_porce']);
			   $subtotal_xx=trim($row_iva['docg_monto_base']);
			   $iva_xx=$iva_xx + $iva_monto[$ii];
			   $ii++;
			  }//Del if del porcentaje
			 } //Del While
			 $elem_imp_iva=$ii;
		} //Del set
		if ($iva_xx==0)
		{
		 $subtotal_xx=$sopg_monto;
		}
	}	//Del valido

 /*Consulto las retenciones previas del documento */
if ($valido)
  {
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$elem_existe=pg_num_rows($resultado_set);
			$id_impuesto_doc=array($elem_existe);
			$rete_monto_doc=array($elem_existe);
			$por_rete_doc=array($elem_existe);
			$por_imp_doc=array($elem_existe);
			$servicio_doc=array($elem_existe);
			$tt_retencion=0;
			$tt_neto=0;
			$ii=0;
 			while($row_rete_doc=pg_fetch_array($resultado_set))	
			 {
				$id_impuesto_doc[$ii]=trim($row_rete_doc['impu_id']);
				$rete_monto_doc[$ii]=trim($row_rete_doc['rete_monto']);
				$por_rete_doc[$ii]=trim($row_rete_doc['por_rete']);
				$por_imp_doc[$ii]=trim($row_rete_doc['por_imp']);
				$servicio_doc[$ii]=trim($row_rete_doc['servicio']);
				$tt_retencion=$rete_monto_doc[$ii]+$tt_retencion;
			   $ii++; 
			 }
			 $tt_neto=$sopg_monto-$tt_retencion;
		} 
	}	

 /*Busqueda del Expediente*/

	$docu_final=$codigo;
	$doc_anno_press=$pres_anno;
   
	if($valido) 
    {
	  $sql= " Select * from sai_pres_busca_expediente (".$doc_anno_press.",'". $docu_final."') as expediente";
	  $resultado_set = pg_exec($conexion ,$sql) ;
	  $valido= $resultado_set;
	  if ($resultado_set)
	  {
	   $row = pg_fetch_array($resultado_set,0);
	   $expediente=trim($row[0]);
	  }
	}

 /*Busqueda del CAUSADO*/

if ($valido)
  {
  $sql= " Select * from sai_pres_busca_op_exp (".$doc_anno_press.",3,'". $expediente."') as causado";
  $resultado_set = pg_exec($conexion ,$sql) ;
  $valido= $resultado_set;
	if ($resultado_set)
	{
	  $row = pg_fetch_array($resultado_set,0);
	  $causado=trim($row[0]);
	}
	}

	  //Buscar las Imputaciones
	 $total_imputacion=0;
	 if ($valido){
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	  $sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
	  $resultado_set= pg_exec($conexion ,$sql);
	  $valido=$resultado_set;
		if ($resultado_set)
  		{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set))	
			 {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); 
				$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); 
				$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']);
				$matriz_uel[$i]=trim($row['depe_id']); 
				$matriz_monto_partida[$i]=$row['sopg_monto']+$row['sopg_monto_exento'];
				$i++;
			}


		}
		}	


if ($valido)
{   $cero="0";
	$sql  = "select * from sai_anular_sopg('".trim($_SESSION['login'])."','".trim($codigo)."','";
	$sql  .= trim($memo_contenido)."','";
	$sql  .= trim($_SESSION['user_depe_id'])."','";
	$sql  .= $cero."','".$cero."','".$causado;
	$sql  .= "') as memo_id";
      
	$resultado_set = pg_exec($conexion ,$sql);
	$valido=resultado_set;
	if($resultado_set)
	{
			$row = pg_fetch_array($resultado_set,0); 
			if ($row[0] <> null)
			{
			  $memo_id=$row[0];
			}
	}
}	

 if($compromiso<>'N/A'){
	//ACTUALIZAR MONTO DISPONIBLE DEL COMP
	for($j=0; $j<$total_imputacion; $j++)
	  {  
	  $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
	  $resultado_query = pg_exec($conexion,$query_disponible);
	  if ($row=pg_fetch_array($resultado_query)){
		$disponible=$row['disponible']+$matriz_monto_partida[$j];
		$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
		$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
	    $cod_pcta=$pcta_id;
 	  }
			
	 }
 }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAI:Revisar SOPG</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript">

function revisar()
{
  if(document.form.txt_motivo.value=='')
  {
    alert('Especifique el motivo de anulaci\u00F3n');
	document.form.txt_motivo.focus();
	return;
  }
  if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?"))
  {
	 document.form.submit();
  }	
}

</SCRIPT>

</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="ver_monto_letra(<?php echo  number_format($sopg_monto,2,'.','');?>, 'txt_monto_letras','');ver_monto_letra(<?php echo  number_format($tt_neto,2,'.','');?>, 'txt_monto_letras_neto','');">


<form name="form" action="anular_edocumento.php?tipo=<? echo $request_id_tipo_documento; ?>&id=<?php echo trim($request_codigo_documento); ?>" method="post">
<table  align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
<?php if ($valido)
{
?>
	<tr>
		<td colspan="4">
			<table width="100%">
			<tr class="td_gray"> 
	<td colspan="4" class="normalNegroNegrita" align="center">ANULAR SOLICITUD DE PAGO</td>
  </tr>
			<tr class="td_gray"> 
			<td height="21" colspan="4" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DATOS DEL SOLICITANTE </div></td>
			</tr>
			<tr> 
			<td height="28" colspan="2"><div class="normalNegrita" >Solicitud de Pago:</div></td>
			 <td class="normalNegro">  <?php echo $codigo;?></td>
			</tr>
			<tr> 
			<td height="28" colspan="2"><div class="normalNegrita">Solicitante:</div></td>
			<td  class="normalNegro" width="555"> <?php echo $solicitante;?></td>
			</tr>
			<tr> 
			<td height="28"colspan="2"><div class="normalNegrita">C&eacute;dula de Identidad:</div></td>
			<td class="normalNegro"> <?php echo $cedula;?></td>
			</tr>
			<tr> 
		    <td height="28"colspan="2"><div  class="normalNegrita">Email:</div></td>
		    <td class="normalNegro"><?php echo $email;?></td>
		    </tr>
			<tr> 
			<td height="28" colspan="2"><div  class="normalNegrita">Cargo:</div></td>
			<td class="normalNegro"> <?php echo $cargo;?></td>
			</tr>
			<tr> 
			<td height="30" colspan="2"><div  class="normalNegrita">Dependencia Solicitante:</div></td>
			<td class="normalNegro"><?php echo $dependencia;?></td>
			</tr>
			<tr> 
			<td height="30" colspan="2"><div class="normalNegrita">Tipo de Solicitud: </div></td>
			<?
			   $sql="SELECT * FROM sai_seleccionar_campo('sai_tipo_solicitud','nombre_sol','id_sol='||'''$tp_sol''','',2) 
			   resultado_set(nombre_sol varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar tipo de solicitud");
			   $valido= $resultado_set_most_be;
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				$nombre_sol=$rowbe['nombre_sol'];
			   }
			?>
			<td class="normalNegro"><?echo $nombre_sol;?></td>
			</tr>

			<tr> 
			<td height="30" colspan="2"><div  class="normalNegrita">Tel&eacute;fono de Ofic.:</div></td>
			<td  class="normalNegro"><?php echo $telefono;?></td>
			</tr>
			</table>		</td>
		   </tr>
	       <tr>
		<td colspan="4">
			<table width="100%">
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DATOS DEL BENEFICIARIO</div></td>
			</tr>
			<tr> 
			<td height="29" colspan="2"><div  class="normalNegrita">Beneficiario:</div></td>
			<td width="555" class="normalNegro"><?php echo $nombre_bene;?></td>
			</tr>
			<tr> 
			<td height="28" colspan="2"><div class="normalNegrita">C.I. o RIF:</div></td>
			<td  class="normalNegro"><?php echo $sopg_bene_ci_rif;?></td>
			</tr>
			</table>		</td>
	</tr>
	<tr>
	<td colspan="4">
		<table width="100%" class="tablaalertas">
		<tr > 
        <td height="21" colspan="8" valign="midden" class="td_gray"><div align="left" class="normalNegroNegrita"> 
        DOCUMENTOS ANEXOS</div></td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_factura" type="checkbox" id="chk_factura"  disabled="true" <?php if ($anexos[0]==1){echo "checked";}?> /></td>
		 <td height="21" >Factura</td>
        <td height="21" ><input name="chk_ordc" type="checkbox" id="chk_ordc" disabled="true" <?php if ($anexos[1]==1){echo "checked";}?>/></td>
		<td height="21" >Orden de Compra</td>
        <td height="21" ><input name="chk_contrato" type="checkbox" id="chk_contrato" disabled="true" <?php if ($anexos[2]==1){echo "checked";}?>/></td>
		<td height="21" >Contrato</td>
        <td height="21" ><input name="chk_certificacion" type="checkbox" id="chk_certificacion" disabled="true" <?php if ($anexos[3]==1){echo "checked";}?>/></td>
        <td height="21" >Certificaci&oacute;n del Control Perceptivo</td>
        </tr>
		
		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_recibo" type="checkbox" id="chk_recibo" disabled="true" <?php if ($anexos[4]==1){echo "checked";}?>/></td>
        <td height="21" >Recibo</td>
        <td height="21" ><input name="chk_ords" type="checkbox" id="chk_ords" disabled="true" <?php if ($anexos[5]==1){echo "checked";}?>/></td>
        <td height="21" >Orden de Servicio</td>
        <td height="21" ><input name="chk_pcta" type="checkbox" id="chk_pcta" disabled="true" <?php if ($anexos[6]==1){echo "checked";}?>/></td>
        <td height="21" >Punto de Cuenta</td>
        <td height="21" ><input name="chk_gaceta" type="checkbox" id="chk_gaceta" disabled="true" <?php if ($anexos[7]==1){echo "checked";}?>/></td>
        <td height="21" >Gaceta Oficial</td>
	    </tr>

		<tr class="normalNegro"> 
        <td height="21" ><input name="chk_informe" type="checkbox" id="chk_informe" disabled="true" <?php if ($anexos[8]==1){echo "checked";}?>/></td>
		<td height="21" >Informe o Solicitud de Pago a Cuentas</td>
        <td height="21" ><input name="chk_estimacion" type="checkbox" id="chk_estimacion" disabled="true" <?php if ($anexos[9]==1){echo "checked";}?>/></td>
        <td height="21" >Estimaci&oacute;n o C&aacute;lculo seg&uacute;n RAC</td>
        <td height="21" ><input name="chk_otro" type="checkbox" id="chk_otro" disabled="true" <?php if ($anexos[10]==1){echo "checked";}?>/></td>
        <td height="21" >Otro (Especifique)</td>
		<td height="21" ></td>
        <td height="21" ><input type="text" name="txt_otro" id="txt_otro" class="normal" size="25" maxlength="25" value="<?php echo $anexos_otros;?>"   disabled="true"></td>
        </tr>
	 </table>
	 </td>
	 </tr> 

	<tr>
		<td height="176" colspan="4">
			<table width="100%">
			<tr class="td_gray"> 
			<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegroNegrita"> 
			DETALLES DE LA SOLICITUD </div></td>
			</tr>
			<?php
				if (trim( $factura_num<>"")){ ?>

				<tr class="normal"> 
				<td height="28" valign="midden" colspan="2"> <div class="normalNegrita">Factura N&deg;
				</td><td><input name="txt_factura"  type="text" class="normalNegro" id="txt_factura" size="20" maxlength="20"   valign="right"  align="right"  readonly="true" value="<?php echo $factura_num;?>"/>
				  <font class="normalNegrita">Fecha:</font>
				  <input name="txt_fecha_factura" type="text" class="normalNegro" id="txt_fecha_factura" size="12" readonly="true"  value="<?php echo $factura_fecha;?>" />
				  <font class="normalNegrita">N&deg; de Control:</font>
				  <input name="txt_factura_num_control"  type="text" class="normalNegro" id="txt_factura_num_control" size="11" maxlength="10"   valign="right"  align="right"  readonly="true"  value="<?php echo $factura_control;?>"/>
				  </div></td>
					  </tr>
	  		<?php } ?>

			<tr class="normal"> 
			<td height="28" class="normalNegrita" colspan="2"> 
			<div >Prioridad:</div>			</td>
			<td class="normalNegro"> 
			<select name="slc_prioridad" class ="normalNegro" disabled>
         <option value="1" <?php if ($prioridad==1) { echo "selected"; } ?> >Baja</option>
          <option value="2" <?php if ($prioridad==2) { echo "selected"; } ?>>Media</option>
          <option value="3" <?php if ($prioridad==3) { echo "selected"; } ?>>Alta</option>
        </select>		</td>
			</tr>
			<tr> 
			<td height="27" colspan="2"><div class="normalNegrita">Motivo del Pago:</div></td>
			<td class="normalNegro"><?php echo $sopg_detalle;?></td>
			</tr>
			<tr> 
			<td height="21" colspan="2"><div class="normalNegrita">Fecha de Solicitud:</div></td>
			<td class="normalNegro"><?php echo $sopg_fecha;?>			</td>
			</tr>
			<tr> 
			<td height="21" colspan="2"><div class="normalNegrita">Observaci&oacute;n:</div></td>
			<td class="normalNegro" width="555">
			<textarea name="txt_observa" rows="3" cols="60" disabled><?php echo  $sopg_observacion;?></textarea>			</td>
			</tr>
		</table>		</td>
	</tr>
	<tr>
		<td colspan="4">
		<table width="60%" border="0"  class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="normalNegroNegrita">Imputaci&oacute;n Presupuestaria </td>
          </tr>
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td  class="peqNegrita"align="left"><div align="center">Centro Gestor</div></td>
                  <td  class="peqNegrita" align="left"><div align="center">Centro Costo </div></td>
                  <td width="10%"align="left"  class="peqNegrita"><div align="center">UEL.</div></td>
                  <td width="15%"align="left"  class="peqNegrita"><div align="center">Partida/Cuenta contable</div></td>
                  <td width="39%"align="left"  class="peqNegrita"><div align="center">Monto Sujeto</div></td>
		  <td width="39%"align="left"  class="peqNegrita"><div align="center">Monto Exento</div></td>
                </tr>
                <tr class="normalNegro">
                  <?php
		          for ($ii=0; $ii<$total_imputacion; $ii++)
    		      {
    		      	if ($matriz_imputacion[0]==1){ //Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{ //Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[$ii])."','".trim($matriz_acc_esp[$ii])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }
				   ?>
                  <td  align="left" width="17%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_proyecto_accion".$ii;?>  type="hidden" class="peq" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="true"/>
		              <input name=<?php echo "centro_gestor".$ii;?> type="text" class="normalNegro"  id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo  $centrog;?>" readonly="true"/>
                    </div></td>
                  <td  align="left" width="19%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="peq" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="true"/>
		              <input name=<?php echo "centro_costo".$ii;?>  type="texto" class="normalNegro"  id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $centroc;?>" readonly="true"/>
                    </div></td>
                  <td  align="left" width="10%">
                    <div align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normalNegro"  id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   valign="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="true"/>
                    </div></td>
                  <td   align="left" width="15%">
                    <div align="center">
                   <?php 	if (substr($matriz_sub_esp[$ii],0,6)=="4.11.0"){
      				$convertidor="SELECT cpat_id FROM sai_convertidor WHERE part_id='".$matriz_sub_esp[$ii]."'";
     				$res_convertidor=pg_query($conexion,$convertidor);
      				if($row_conv=pg_fetch_array($res_convertidor)){
					 $cuenta= $row_conv['cpat_id'];
      				}          	
     			   }else{
     			   $cuenta=$matriz_sub_esp[$ii];
   				  }?>
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normalNegro"  id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   valign="right"  align="right" value="<?php echo $cuenta;?>" readonly="true" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                    </div></td>
                  <td  align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normalNegro"  id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
		          <td  align="left" width="39%">
                    <div align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normalNegro"  id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   valign="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="true" />
                    </div></td>
                </tr>
                <?php 
		 }
		 ?>
            </table></td>
          </tr>
		
		<tr> 
		  <?php 
		  if (trim($causado<>"")) 
		  {
		  ?>
		  <tr >
            <td colspan="3"  align="left"  class="normalNegrita"><img src="imagenes/vineta_azul.gif" width="11" height="7" />Causado N&deg;:<?php echo $causado;?></td>
          </tr>
		  <?php
		  }
		  ?>
   
		</table>			</td>
		</tr>
	
	 <tr>
		<td class="normal" colspan="5" align="center" >
			<table width="76%" class="tablaalertas" align="center" id="tbl_detalle_iva" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="2" align="center" class="normalNegroNegrita">
			DETALLE DEL IMPUESTO AL VALOR AGREGADO (IVA) 
			</td>
			</tr>
			<tr >
			<td height="19"  align="center" class="normalNegrita">Monto Base: 
			  <input name="txt_monto_subtotal" type="text" class="normalNegro" id="txt_monto_subtotal" value="<?php echo (number_format($subtotal_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			<td height="19"  align="center" class="normalNegrita">Monto IVA: 
			  <input name="txt_monto_iva_tt" type="text" class="normalNegro" id="txt_monto_iva_tt" value="<?php echo (number_format($iva_xx,2,'.',','));?>" size="25" maxlength="25" readonly="" align="right"> </td>
			</tr>
			<?php 
			if ($elem_imp_iva>0) 
			{
			?>
			<tr >
			<td width="50%" class="normalNegrita" align="center">Impuesto %</td>
			<td width="50%" class="normalNegrita" align="center">Monto (Bs.)</td>
			</tr>
			<?php 
			for ($xt=0; $xt<$elem_imp_iva; $xt++)
			{
			?> 
			<tr >
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva%".$xt;?>" type="text" class="normalNegro" id=<?php echo "txt_iva%".$xt;?> value="<?php echo (number_format($iva_porce[$xt],2,'.',','));?>" size="6" maxlength="6" readonly="" align="right"></td>
			<td width="50%" class="normal" align="center"><input name="<?php echo "txt_iva_monto%".$xt;?>" type="text" class="normalNegro" id="<?php echo "txt_iva_monto%".$xt;?>" value="<?php echo  (number_format($iva_monto[$xt],2,'.',',')); ?>"  size="25" maxlength="25" readonly="" align="right"></td>
			</tr>
			<?php
			} //Del For
			} //Del If
			?>
		</table>
		</td>	 
	</tr>	
	<tr class="td_gray"> 
	<td height="21" colspan="4" valign="midden"><div align="left" class="normalNegroNegrita"> 
	TOTAL A PAGAR</div></td>
	</tr>
	<tr>
	<td height="31" colspan="4">
	<div align="left"><span class="normalNegrita">
	 Bs.
	 <input type="text" name="txt_monto_pagar" size="20" class="normalNegro" readonly="true" value="<?php echo(number_format($sopg_monto,2,'.',',')); ?>">
    </span></div>	</td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normal">
	En Letras:<textarea name="txt_monto_letras" id="txt_monto_letras" rows="2" cols="65" class="normalNegro" readonly></textarea></td>
	</tr>
    <tr>
		<td class="normal" colspan="5" align="center" >
			<table width="100%" class="tablaalertas" align="center" id="tbl_retenciones" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
			RETENCIONES <a href="javascript:abrir_ventana('http://www.seniat.gov.ve',700)">www.seniat.gov.ve</a></td>
			</tr>
			
			<tr>
			<tr class="td_gray">
			<td width="63%" class="normalNegroNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegroNegrita" align="center">Tipo de Retencion</td>
			<td width="10%" class="normalNegroNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegroNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegroNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?php
			  
			  for ($ii=0; $ii<$elem_existe; $ii++) 
			  {
			?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii?>" type="text"  class="normalNegro" value="<?php echo $servicio_doc[$ii];?>" size="60" maxlength="60" readonly="true"> </td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii?>" id= "<?php echo "txt_impuesto".$ii?>" value="<?php echo $id_impuesto_doc[$ii];?>" class="normalNegro" size="10" readonly="true"></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii?>" id= "<?php echo "txt_iva_".$ii?>" value="<?php if ($por_imp_doc[$ii]==0) {echo "s/n";} else {echo $por_imp_doc[$ii];}?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="12%" class="normal" align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo $por_rete_doc[$ii]; ?>" class="normalNegro" size="6" readonly="true"></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"  class="normalNegro" value="<?php echo  (number_format($rete_monto_doc[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="true" align="right" id="<?php echo "txt_monto_impu".$jj?>">			</td>
			</tr>
			<?php
			} //-->Del For 
		?>
		
			<tr class="td_gray">
			<td height="19" colspan="1" align="left" class="normalNegroNegrita">TOTAL RETENCIONES :		      </td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text" class="normalNegro" id="txt_monto_retenciones_tt" value="<?php echo(number_format($tt_retencion,2,'.',',')); ?>" size="25" maxlength="25" readonly="true" align="right"> </td>
			</tr>
			</table>		</td>
		</tr>
		
	<tr> 
			<td height="21" colspan="4" valign="midden" class="td_gray" align="left">
			<div align="left"class="normalNegroNegrita">MONTO NETO DEL PAGO</div></td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">	En Bs.
	  <input type="text" name="txt_monto_neto" size="20" class="normalNegro" readonly="true" value="<?php echo(number_format($tt_neto,2,'.',',')); ?>"></td>
	</tr>
	<tr> 
	<td height="21" colspan="4" valign="midden" align="left" class="normalNegrita">
	En Letras:<textarea name="txt_monto_letras_neto" id="txt_monto_letras_neto" rows="2" cols="65" class="normalNegro" readonly></textarea></td>
	</tr>
	 <tr> 
	<td height="21" colspan="4" valign="midden" class="td_gray">
	<div align="left" class="normalNegroNegrita"> 
	ANULACI&Oacute;N DE LA SOLICTUD DE PAGO</div></td>
	</tr>
	<tr class="normal">
	<td height="20" class="normalNegrita" align="right">Motivo:</td>
	<td height="20" colspan="3">
	<textarea name="txt_motivo" rows="3" cols="75" class="normalNegro"  disabled><?php echo $memo_contenido;?></textarea>	</td>
	</tr>
	
  <tr>
      <td colspan="5">
	  <table width="420" align="center">
					<?
				   $cod_doc=$codigo;
				   include("includes/respaldos_mostrar.php");
					?>
				</table>
	  </td>
    </tr>
    <?php
	} /*Del Valido*/
	 if ($valido==false){
	     ?>
    <tr>
      <td height="18" colspan="4" class="normal"><div align="center">Ha ocurrido un error al registrar los datos , <?php echo(pg_errormessage($conexion)); ?></div></td>
    </tr>
    <tr>
      <td height="40" colspan="4"><div align="center"><img src="imagenes/mano_bad.gif" width="31" height="38" /></div></td>
    </tr>
    <?php
	 } 
	?>
    <tr>
		<td  colspan="4" class="normal" align="center">	<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
				<br>
		    </td>
		</tr>
  </table>
		
</form>
</body>
</html>



