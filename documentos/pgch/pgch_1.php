<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
	 
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}

ob_end_flush(); 
$a_oPresupuesto = $_SESSION['an_o_presupuesto'];

/*Parche para que se puedan emitir pgch del año anterior, comentar para pgch del año actual*/
//$a_oPresupuesto = '2015';

$codigo=trim($_REQUEST['codigo']);
$id_doc_imputacion = $codigo;
$error = 0;
$tipo_doc = substr($codigo,0,4);
if ($codigo!="" && $tipo_doc == "sopg") {
	$sql="SELECT s.sopg_monto,
			s.numero_reserva AS numero_reserva,
			s.sopg_tp_solicitud AS id_tipo_solicitud,
			s.sopg_bene_ci_rif AS sopg_bene_ci_rif, 
			s.sopg_bene_tp AS sopg_bene_tp, 
			s.sopg_detalle, 
			s.pres_anno, 
			s.numero_reserva, 
			s.sopg_observacion, 
			UPPER(COALESCE(em.empl_nombres,''))||' '||UPPER(COALESCE(em.empl_apellidos,'')) AS nombre_empleado,
			UPPER(COALESCE(p.prov_nombre,'')) as nombre_proveedor,
			UPPER(COALESCE(v.benvi_nombres,'')) ||' '|| UPPER(COALESCE(v.benvi_apellidos,'')) AS nombre_viat, 
			t.nombre_sol AS tipo_solicitud
	 	  FROM sai_sol_pago s
	      LEFT OUTER JOIN sai_proveedor_nuevo p ON (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif) AND prov_esta_id<>2)
	 	  LEFT OUTER JOIN sai_empleado em ON (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif) AND trim(em.empl_cedula) NOT IN (SELECT prov_id_rif FROM sai_proveedor_nuevo WHERE prov_esta_id=1) )
	      LEFT OUTER JOIN sai_viat_benef v ON (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif) AND trim(v.benvi_cedula) NOT IN (SELECT prov_id_rif FROM sai_proveedor_nuevo WHERE prov_esta_id=1))
	      LEFT OUTER JOIN sai_tipo_solicitud t ON (s.sopg_tp_solicitud=t.id_sol)
	      WHERE s.sopg_id='".$codigo."'";      
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)) {
		$sopg_monto=trim($row['sopg_monto']); 
		$id_tipo_solicitud=trim($row['id_tipo_solicitud']);	
		$tipo_solicitud=trim($row['tipo_solicitud']);		
		$sopg_bene_ci_rif=trim($row['sopg_bene_ci_rif']);
		$sopg_bene_tp=trim($row['sopg_bene_tp']);
		$sopg_detalle=trim($row['sopg_detalle']);
		$numero_reserva=trim($row['numero_reserva']);
		$press_anno=trim($row['pres_anno']);
		$reserva=trim($row['numero_reserva']);
		$sopg_obs=trim($row['sopg_observacion']);
		if ($row['sopg_bene_tp']==1)
			$nombre_bene=trim($row['nombre_empleado']);
		else if ($row['sopg_bene_tp']==2)
			$nombre_bene=trim($row['nombre_proveedor']);
		else 		
			$nombre_bene=trim($row['nombre_viat']);
	}
				
	$concepto_cheque = $sopg_detalle;					
	$monto_cheque = $sopg_monto;
	$beneficiario = $nombre_bene;
	$identificacion_beneficiario = $sopg_bene_ci_rif;
				
	//Buscar las retenciones
	$sql= "select * from sai_buscar_sopg_reten ('".trim($codigo)."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
				
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
	if ($resultado_set) {
		$elem_existe=pg_num_rows($resultado_set);
		$id_impuesto_doc=array($elem_existe);
		$rete_monto_doc=array($elem_existe);
		$por_rete_doc=array($elem_existe);
		$por_imp_doc=array($elem_existe);
		$servicio_doc=array($elem_existe);
		$tt_retencion=0;
		$tt_neto=0;
		$ii=0;
		while($row_rete_doc=pg_fetch_array($resultado_set)) {
			$id_impuesto_doc[$ii]=trim($row_rete_doc['impu_id']);
			$rete_monto_doc[$ii]=trim($row_rete_doc['rete_monto']);
			$por_rete_doc[$ii]=trim($row_rete_doc['por_rete']);
			$por_imp_doc[$ii]=trim($row_rete_doc['por_imp']);
			$servicio_doc[$ii]=trim($row_rete_doc['servicio']);
			$tt_retencion=$rete_monto_doc[$ii]+$tt_retencion;
		   $ii++; 
		}
		$tt_neto=$sopg_monto-$tt_retencion;
					 
		//Monto total del cheque descontando las retenciones es
		$monto_cheque_antes_ret = $monto_cheque;
		$monto_cheque = $monto_cheque - $tt_retencion;					 
	} 

	/*Consulto las OTRAS retenciones del documento */
	$sql="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$codigo."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$a_oPresupuesto."'"; 
	$resultado_set_most_be=pg_query($conexion,$sql) or die("Error al consultar partida");
	
	if ($resultado_set_most_be) {
		$elem_otras_rete=pg_num_rows($resultado_set_most_be);
		$nombre_part=array($elem_otras_rete);
		$id_partida=array($elem_otras_rete);
		$monto_rete=array($elem_otras_rete);

		$ii=0;
		$total_otras_rete=0;
		while($rowbe=pg_fetch_array($resultado_set_most_be)) {
			$nombre_part[$ii]=$rowbe['part_nombre'];
			$id_partida[$ii]=$rowbe['sopg_partida_rete'];
			$monto_rete[$ii]=$rowbe['sopg_ret_monto'];
			$total_otras_rete=$total_otras_rete+$monto_rete[$ii];
			$ii++;
		}
 		$tt_neto=$tt_neto-$total_otras_rete;
		$monto_cheque_antes_ret = $sopg_monto;
		$monto_cheque =	$tt_neto;
	}
} 
else { //if codigo!=""
	$error =1;
	$mensaje_error_pgch = "No existe un documento para generar un Pago con Cheque o Transferencia";
}	

//Si no hay error, buscar imputaciones
if ($error==0) {

	$anno_id_doc_imputacion=$press_anno;

	//Buscar las Imputaciones
	$total_imputacion=0;
	$monto_temp_vi=0;
	 
	$sql= " Select * from sai_buscar_sopg_imputacion('".trim($codigo)."') as result ";
	$sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
	if ($resultado_set)	{
		$total_imputacion=pg_num_rows($resultado_set);
		$i=0;
		while($row=pg_fetch_array($resultado_set)) {
				$matriz_imputacion[$i]=trim($row['tipo']);
				$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
				$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
				$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
				$matriz_uel[$i]=trim($row['depe_id']); //depe
				$matriz_monto[$i]=trim($row['sopg_monto']); //monto
				$matriz_monto_exento[$i]=trim($row['sopg_monto_exento']); //monto
				$monto_temp_vi= $monto_temp_vi + $matriz_monto[$i];
				$i++;
		}
	
		if ($matriz_imputacion[0]==1) {//Por Proyecto
			$query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else {//Por Accion Centralizada
			$query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}
	
		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query) {
			while($row=pg_fetch_array($resultado_query)) {
				$centrog = trim($row['centro_gestor']);
				$centroc = trim($row['centro_costo']);
			}
		}
	}
/* FIN DE IMPUTACION*/
}
?>
 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html> 
<head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<title>SAFI: Iniciar Pago</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="JavaScript" src="js/funciones.js"> </script>
<script language="JavaScript" src="js/func_montletra.js"> </script>

<script language="JavaScript" type="text/JavaScript">
var iva_ajuste;

function revisar() {
	var mensaje="";	
	if (document.form.cmb_forma_pago.value==0) {
		alert ("Debe seleccionar una forma de pago");
  		document.form.cmb_forma_pago.focus();
	return;
 	}	
	if (document.form.banco_emisor.value==0) {
		alert ("Debe seleccionar un banco emisor");
		document.form.banco_emisor.focus();
	return;
	}	
	if ((document.form.cmb_forma_pago.value==2 || document.form.cmb_forma_pago.value==3) && trim(document.form.txt_referencia.value)=="") {
		alert ("Debe colocar un n\u00famero de referencia");
 		document.form.cmb_forma_pago.focus();
 		return;
	}
	if (document.form.cmb_forma_pago.value==1) mensaje_pago="CHEQUE";
	else mensaje_pago="TRANSFERENCIA";
	if (document.form.id_tipo_solicitud.value!=28) mensaje="Est\u00e1 seguro de la operaci\u00F3n con "+mensaje_pago+" del banco emisor que seleccion\u00F3:  "+document.form.banco_emisor.value+"?";
	else mensaje="Est\u00e1 seguro de la operaci\u00F3n con "+mensaje_pago+" del banco emisor:"+document.form.banco_emisor.value+"?";
	if (confirm(mensaje)) {
		document.form.submit()
	}
}

function habilitar_referencia(campo) {
	if (campo.value==2) document.form.txt_referencia.disabled=false;
	else document.form.txt_referencia.disabled=true;
}

function revisar_doc(id_tipo_documento,id_opcion,objeto_siguiente_id,objeto_siguiente_id_proy,cadena_siguiente_id,cadena_siguiente_id_proy,id_objeto_actual) {
	document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion;
	revisar();
}

function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');
  	ver_monto_letra(monto ,'txt_monto_letras','');
}
</script>
</head>
<? 
	$monto_cheque_para_letras = "";
	$monto_cheque_load = number_format($monto_cheque,2,'.',',');
	$string_monto_cheque = "".$monto_cheque_load."";
	$parte_decimal = strstr($monto_cheque_load, ".");
	//Si los decimales se indican con punto (.)
	if (strlen($parte_decimal) <= 3) $monto_cheque_para_letras = str_replace(",","",$monto_cheque_load);	
	//Los decimales se indican con coma (,)	
	else $monto_cheque_para_letras = str_replace(",",".",str_replace(".","",$monto_cheque_load));	
?>
<body onload="monto_en_letras('<? echo $monto_cheque_para_letras; ?>')";>
<form name="form" method="post" action=""  enctype="multipart/form-data" id="form">
<?php 
//Si no hay error
if ($error==0) {
?>
<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita" align="center">PAGO CON CHEQUE/TRANSFERENCIA</td>
	</tr>	
	<tr> 
		<td colspan="2" class="normalNegroNegrita">DATOS DEL SOLICITANTE</td>
	</tr>			
	<tr> 
		<td class="normalNegrita">Solicitante:</td>
		<td class="normal"><?php echo $_SESSION['solicitante'];	?></td>
	</tr>			
	<tr> 
		<td class="normalNegrita">Correo electr&oacute;nico:</td>
		<td class="normal"><?php echo $_SESSION['email'];?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Cargo:</td>
		<td class="normal"><?php echo $_SESSION['cargo'];?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Dependencia:</td>
		<td class="normal"><?php echo $_SESSION['user_depe'];?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Tel&eacute;fono de oficina:</td>
		<td class="normal" colspan="2"><?php echo $_SESSION['tlf_ofic'];?></td>
	</tr>
	<tr> 
		<td colspan="2" class="normalNegroNegrita">DETALLES PARA LA SOLICITUD DE PAGO NRO.<a href="javascript:abrir_ventana('documentos/<? echo $tipo_doc; ?>/<? echo $tipo_doc; ?>_detalle.php?codigo=<?php echo trim($codigo);?>')"><?php echo $codigo;?></a></td>
	</tr>
	<tr>
	  <td class="normalNegrita">Tipo de solicitud:</td>
	  <td class="normal"><?php echo $tipo_solicitud;?>
	    <input type="hidden" name="hid_tipo_solicitud" value="<?php echo $tipo_solicitud;?>" />
	  <input type="hidden" name="hid_codigo_doc" value="<?php echo $codigo;?>"/>
	 	 <input type="hidden" name="id_tipo_solicitud" value="<?php echo $id_tipo_solicitud;?>" />
	  </td>
	</tr>	
	<tr>
		  <td class="normalNegrita">Beneficiario:</td>
		  <td class="normal"><?php echo $identificacion_beneficiario." - ".$beneficiario;?>
		      <input type="hidden" name="hid_beneficiario" value="<?php echo $identificacion_beneficiario."**".$beneficiario;?>" />
		       <input type="hidden" name="numero_reserva" value="<?php echo $numero_reserva;?>" />
		  </td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto sin retenci&oacute;n Bs.:</td>
		<td class="normal">
		<?php echo(number_format($monto_cheque_antes_ret,2,'.',',')); ?>
		<input type="hidden" name="hid_monto_cheque_antes_ret" value="<?php echo(number_format($monto_cheque_antes_ret,2,'.',',')); ?>"></input>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto total a pagar Bs.:</td>
		<td class="normal"><?php echo(number_format($monto_cheque,2,'.',',')); ?>
			<input type="hidden" name="hid_monto" value="<?php echo number_format($monto_cheque,2,'.',',');?>" />
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto en letras:</td>
		<td class="normal"><input type="text" class="normal" id="txt_monto_letras" name="txt_monto_letras" size="90" readonly="readonly"></td>
	</tr>		
	<tr>
		<td class="normalNegrita">Forma pago:</td>
		<td> 
		<select name="cmb_forma_pago" class="normal" id="cmb_forma_pago" onchange="javascript: habilitar_referencia(this)">
		 <option value="0" selected="selected">--Seleccione--</option>
		 <option value="1">Cheque</option>
			<option value="2">Transferencia</option>
			</select>
   		</td>
	</tr>
	<tr>
  		<td class="normalNegrita">Nro. Referencia:</td>
		<td class="normal"><input type="text" name="txt_referencia" id="txt_referencia" disabled="disabled"/></td>
	</tr>
	<tr>	
		<td class="normalNegrita">Banco emisor:</td>
		<td> 
			<select name="banco_emisor" class="normal" id="banco_emisor">
				<option value="0" selected="selected">--</option>
			 	<?
			 	$sql="SELECT b.banc_id, upper(b.banc_nombre) as nombre_banco, c.ctab_numero as cuenta_banco from sai_banco b, sai_ctabanco c where b.banc_id=c.banc_id and c.ctab_estatus=1";
			 	$resultado_banco=pg_query($conexion,$sql);
				while ($row_banco=pg_fetch_array($resultado_banco)) {
			    ?>
			     <option value="<?php echo trim($row_banco["nombre_banco"])."-".$row_banco["cuenta_banco"];?>"><?php echo $row_banco["nombre_banco"];?> - <?php echo trim($row_banco["cuenta_banco"]);?></option>
 			  <?}?>
			</select>
           </td>
	</tr>
	<tr> 
		<td class="normalNegrita">Concepto del pago:</td>
		<td class="normal">
			<textarea name="txt_concepto" cols="80" rows="3" class="normal" onblur="javascript:LimitText(this,300)"><?php echo $concepto_cheque;?></textarea>
		</td>
	</tr>
	<tr> 
		<td class="normalNegrita">Observaciones del pago:</td>
		<td class="normal">
			<textarea name="txt_observa" cols="80" rows="3" class="normal" onblur="javascript:LimitText(this,300)"><?php echo $sopg_obs;?></textarea>
		</td>
	</tr>
	<?php if ($total_imputacion>0) { ?>
	<tr>
	  <td height="18">&nbsp;</td>
	  </tr>
	<tr>
	<td colspan="2">
		<table width="90%" border="0"  class="tablaalertas">
          <tr class="td_gray">
            <td align="center" colspan="6" class="normalNegroNegrita">IMPUTACI&Oacute;N PRESUPUESTARIA </td>
          </tr>
          <tr class="normal" align="center">
                  <td>ACC.C/PP</td>
                  <td>Acc Espec&iacute;fica</td>
                  <td>Dependencia</td>
                  <td>Partida</td>
                  <td>Monto Sujeto</td>
		 		  <td>Monto Exento</td>
               	</tr>
                <tr>
               <?php for ($ii=0; $ii<$total_imputacion; $ii++)  {	?>
                  <td  class="normal" align="center">
                  	<input name=<?php echo "txt_imputa_proyecto_accion".$ii;?> type="hidden" class="normal" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="readonly"/>
		    		<input name=<?php echo "centro_gestor".$ii;?> type="text" class="normal" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo  $centrog;?>" readonly="readonly"/>
                  </td>
                  <td  class="normal" align="center">
                    <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="normal" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="readonly"/>
		   			<input name=<?php echo "centro_costo".$ii;?>  type="text" class="normal" id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $centroc;?>" readonly="readonly"/>
                  </td>
                  <td  class="normal" align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="normal" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   align="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="readonly"/>
                  </td>
                  <td  class="normal" align="center">
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="normal" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_sub_esp[$ii];?>" readonly="readonly" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                  </td>
                  <td  class="normal" align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="normal" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   align="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="readonly" />
                  </td>
                  <td  class="normal" align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="normal" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   align="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="readonly" />
                  </td>
                </tr>
               <?php }	 ?>
            </table>
	  </td>
	  </tr>
	<?php } ?>
	<tr>
	  <td height="18" colspan="2">&nbsp;</td>
	  </tr>
	<tr>
	<td colspan="2">
		<table width="90%" class="tablaalertas" id="tbl_retenciones" border="0" >
			<tr class="td_gray">
			<td height="19" colspan="5" align="center" class="normalNegroNegrita">
			RETENCIONES <span class="normal">
			</span></td>
			</tr>
			
			<tr>
			<td width="63%" class="normalNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegrita" align="center">Tipo de Retenci&oacute;n</td>
			<td width="10%" class="normalNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?php for ($ii=0; $ii<$elem_existe; $ii++)  {?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii?>" type="text"  class="normal" value="<?php echo $servicio_doc[$ii];?>" size="60" maxlength="60" readonly="readonly"/> </td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii?>" id= "<?php echo "txt_impuesto".$ii?>" value="<?php echo $id_impuesto_doc[$ii];?>" class="normal" size="10" readonly="readonly"/></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii?>" id= "<?php echo "txt_iva_".$ii?>" value="<?php if ($por_imp_doc[$ii]==0) {echo "s/n";} else {echo $por_imp_doc[$ii];}?>" class="normal" size="6" readonly="readonly"/></td>
			<td width="12%" class="normal" align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo $por_rete_doc[$ii]; ?>" class="normal" size="6" readonly="readonly"/></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"  class="normal" value="<?php echo  (number_format($rete_monto_doc[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="readonly" align="right" id="<?php echo "txt_monto_impu".$jj?>"/>			</td>
			</tr>
			<?php
			} //-->Del For 
		?>
			<tr>
			<td height="19" colspan="1" align="left" class="normalNegrita">TOTAL RETENCIONES:</td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text" class="normal" id="txt_monto_retenciones_tt" value="<?php echo(number_format($tt_retencion,2,'.',',')); ?>" size="25" maxlength="25" readonly="readonly" align="right"/> </td>
			</tr>
			</table>
	</td>
	</tr>
<? if ($elem_otras_rete>0) {?>
<tr>
		<td class="normal" colspan="5" align="center" ><br/>
			<table width="90%" class="tablaalertas" id="tbl_otras_retenciones" border="0">
			<tr class="td_gray">
			<td colspan="2" align="center" class="normalNegroNegrita"> OTRAS RETENCIONES </td>
			</tr>
			<tr class="td_gray">
			<td class="normalNegrita" align="center">Partida</td>
			<td class="normalNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<? for ($ii=0; $ii<$elem_otras_rete; $ii++)  {	?>
			<tr><td class="normal">
			<input name="<?php echo "part_".$ii?>" type="text"  class="normal" value="<?php echo  $id_partida[$ii].":".$nombre_part[$ii];?>" size="25" maxlength="25" align="right" id="<?php echo "part_".$ii?>" disabled="disabled"/></td>
			<td class="normal">
			<input name="<?php echo "monto_rete".$ii?>" type="text"  class="normal" value="<?php echo  number_format($monto_rete[$ii],2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$ii?>" disabled="disabled"/></td></tr>
			<?}?>
			<tr class="td_gray">
			<td class="normalNegrita">TOTAL OTRAS RETENCIONES:</td>
			<td class="normalNegrita"><input name="monto_rete_part" type="text" class="normal" id="monto_rete_part" value="<?php echo(number_format($total_otras_rete,2,'.',',')); ?>" size="25" maxlength="25" readonly="readonly" align="right"/> </td>
			</tr>
			</table>
		</td>
	</tr>
<?}?>
	<tr>
	  <td height="18" colspan="2">&nbsp;</td>
    </tr>
	 <tr> 
      <td colspan="2" class="normal" >
	   <table width="420" align="center">
			<?include("includes/respaldos.php");?>
		</table>	
	</td>
	</tr> 
<tr>
		<td height="18" colspan="3">
			<?
				include("documentos/opciones_1.php");
			?>
		</td>
	</tr>
	</table>
<?php
}
else
	{?>
	    <table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr> 
        <td height="21" colspan="4"><div align="left" class="normalNegrita_naranja"> 
       PAGO CON CHEQUE/TRANSFERENCIA</div></td>
        </tr>
  		<tr>
    	<td colspan="4" class="normal" align="center"><br/>
		<img src="imagenes/vineta_azul.gif" width="11" height="7"/>
		<?php if ($error==1) { echo $mensaje_error_pgch; }?>
		<br/><br/>
		<img src="imagenes/mano_bad.gif" width="31" height="38"/>
  		</tr>
	    </table> 
<?php }?>
  <input type="hidden" name="hid_press_anno" value="<?php echo $anno_id_doc_imputacion;?>" />
  <input type="hidden" name="hid_reserva" value="<?php echo $reserva;?>" />
</form>
</body>
</html>
<?php pg_close($conexion);?>