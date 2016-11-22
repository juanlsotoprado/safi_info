<?php  
ob_start();
session_start();
require_once("includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
?>

<?php
$cod_doc = $request_codigo_documento;
$codigo = $cod_doc;

$sql="select s.sopg_monto as sopg_monto, d.depe_nombre as nombre_dependencia, pch.pgch_fecha as fecha_pgch, pch.nro_cuenta as numero_cuenta, pch.docg_id as sopg, pch.pgch_asunto as asunto, pch.pgch_obs as observaciones, b.banc_nombre as nombre_banco,  upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante, em.empl_email as email, em.empl_tlf_ofic as telefono, ch.nro_cheque as numero_cheque, ch.monto_cheque as monto_cheque, upper(ch.beneficiario_cheque) as beneficiario_cheque, t.nombre_sol as tipo_solicitud, ch.id_cheque as id_cheque, ch.ci_rif_beneficiario_cheque as id_beneficiario_cheque
from sai_pago_cheque pch, sai_cheque ch, sai_banco b, sai_dependenci d, sai_ctabanco ctb, sai_doc_genera dg, sai_empleado em, sai_tipo_solicitud t, sai_sol_pago s
where s.sopg_id=pch.docg_id and s.sopg_tp_solicitud=t.id_sol and pch.docg_id=ch.docg_id and pch.depe_id=d.depe_id and pch.nro_cuenta=ctb.ctab_numero and ctb.banc_id=b.banc_id and pch.depe_id=d.depe_id and dg.docg_id=pch.pgch_id and dg.usua_login=em.empl_cedula and pch.pgch_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
	$dependencia_solicitante = trim($row['nombre_dependencia']);
	$fecha_pgch = trim($row['fecha_pgch']);
	$fecha_cheque = trim($row['fechaemision_cheque']);	
	$numero_cuenta = trim($row['numero_cuenta']);
	$id_cheque = trim($row['id_cheque']);
	$numero_cheque = trim($row['numero_cheque']);
	$tipo_solicitud=trim($row['tipo_solicitud']);		
	$sopg = trim($row['sopg']);	
	$asunto = trim($row['asunto']);
	$usuario_solicitante = trim($row['usuario_solicitante']);
    $observaciones=trim($row['observaciones']);
	$email_solicitante=trim($row['email']);
	$telefono_solicitante= trim($row['telefono']);
	$nombre_banco = trim($row['nombre_banco']);
	$monto_cheque = number_format(trim($row['monto_cheque']),2,',','.');
	$monto_cheque_float = trim($row['monto_cheque']);
	$beneficiario_cheque = trim($row['beneficiario_cheque']);
	$id_beneficiario_cheque = trim($row['id_beneficiario_cheque']);	
}			

	//Buscar las retenciones
	$sql= "select * from sai_buscar_sopg_reten ('".trim($sopg)."') as resultado ";
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
	} 

	/*Consulto las OTRAS retenciones del documento */
	$sql="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$sopg."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'"; 
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
		//$monto_cheque =	$tt_neto;
	}
	

	$anno_id_doc_imputacion=$press_anno;

	//Buscar las Imputaciones
	$total_imputacion=0;
	$monto_temp_vi=0;
	 
	$sql= " Select * from sai_buscar_sopg_imputacion('".trim($sopg)."') as result ";
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
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI: Revisar Pago con Cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript">
//Validar Datos
function enviar() {   
	if(document.form.txt_concepto.value=="") {
		alert('Debe especificar el asunto del pago con cheque');
		document.form.txt_concepto.focus();
		return;
	}
	
	if(confirm("Datos introducidos de manera correcta. Est\u00e1 seguro que desea continuar?."))  {
		var texto=crear();
		document.form.txt_arreglo_f.value=texto;
		var texto=crear_digital();
		document.form.txt_arreglo_d.value=texto;
        document.form.submit();
    }	
}

function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');
  	ver_monto_letra(monto ,'txt_monto_letras','');
}

function revisar_doc(id_documento,id_tipo_documento,id_opcion,objeto_siguiente_id,cadena_siguiente_id,id_objeto_actual) { 
	document.form.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion+"&id="+id_documento;

	enviar();
}

function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');

}</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="monto_en_letras(<? echo str_replace(",",".",str_replace(".","",$monto_cheque));?>)">
<form name="form" method="post" action="" enctype="multipart/form-data" id="form1">
<table width="100%" cellspacing="10" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">PAGO CON CHEQUE NRO.: <a href="javascript:abrir_ventana('documentos/pgch/pgch_detalle.php?codigo=<?php echo trim($codigo);?>')"><?php echo $codigo;?></a></td>
	</tr>
	<tr> 
		<td colspan="2" class="normalNegroNegrita">DATOS DEL SOLICITANTE</td>
	</tr>			
	<tr> 
		<td class="normalNegrita">Solicitante:</td>
		<td class="normal"><?php echo $usuario_solicitante;	?></td>
	</tr>			
	<tr> 
		<td class="normalNegrita">Correo electr&oacute;nico:</td>
		<td class="normal"><?php echo $email_solicitante;?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Dependencia:</td>
		<td class="normal"><?php echo $dependencia_solicitante;?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Tel&eacute;fono de oficina:</td>
		<td class="normal" colspan="2"><?php echo $telefono_solicitante;?></td>
	</tr>
	<tr> 
		<td colspan="2" class="normalNegroNegrita">DETALLES PARA LA SOLICITUD DE PAGO NRO.<a href="javascript:abrir_ventana('documentos/sopg/sopg_detalle.php?codigo=<?php echo trim($sopg);?>')"><?php echo $sopg;?></a></td>
	</tr>
	<tr>
	  <td class="normalNegrita">Tipo de solicitud:</td>
	  <td class="normal"><?php echo $tipo_solicitud;?>
	  </td>
	</tr>	
	<tr>
		  <td class="normalNegrita">Beneficiario:</td>
		  <td class="normal"><?php echo $beneficiario_cheque;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto sin retenci&oacute;n Bs.:</div></td>
		<td class="normal"><?php echo $sopg_monto; ?>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto total a pagar Bs.:</td>
		<td class="normal"><?php echo $monto_cheque; ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto en letras:</td>
		<td class="normal"><input type="text" name="txt_monto_letras" id="txt_monto_letras" size="100" class="normal" readonly></td>
	</tr>		
	<tr>
		<td class="normalNegrita">Forma pago:</td>
		<td class="normal"> Cheque</td>
	</tr>
	<tr>
  		<td class="normalNegrita">Nro. Referencia:</td>
		<td class="normal"><?php echo $numero_cheque;?></td>
	</tr>
	<tr>	
		<td class="normalNegrita">Banco:</td>
		<td class="normal">
		
		<select name="banco_emisor" class="normal" id="banco_emisor">
 		<option value="<?php echo $nombre_banco."-".$numero_cuenta;?>" selected="selected"><?php echo $nombre_banco;?>-<?php echo trim($numero_cuenta);?></option>			
 		  <?
			$sql="SELECT b.banc_id, upper(b.banc_nombre) as nombre_banco, c.ctab_numero as cuenta_banco from sai_banco b, sai_ctabanco c where b.banc_id=c.banc_id and c.ctab_estatus=1 and ctab_numero<>'".$numero_cuenta."'";
			$resultado_banco=pg_query($conexion,$sql);
			while ($row_banco=pg_fetch_array($resultado_banco)){
			    ?>
			     <option value="<?php echo trim($row_banco["nombre_banco"])."-".$row_banco["cuenta_banco"];?>"><?php echo $row_banco["nombre_banco"];?> - <?php echo trim($row_banco["cuenta_banco"]);?></option>
 			 <?}?>
			</select>
		</td>
	</tr>
	<tr> 
		<td class="normalNegrita">Concepto del pago:</div></td>
		<td class="normal"><textarea name="txt_concepto" cols="80" rows="3" class="normal" onblur="javascript:LimitText(this,300)"><?php echo $asunto;?></textarea></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Observaciones del pago:</div></td>
		<td class="normal"><textarea name="txt_observa" cols="80" rows="3" class="normal" onblur="javascript:LimitText(this,300)"><?php echo $observaciones;?></textarea>
		<input type="hidden" name="id_cheque_anterior" value="<?php echo $id_cheque;?>">	
		<input type="hidden" name="cuenta_anterior" value="<?php echo $numero_cuenta;?>">
		<input type="hidden" name="nombre_beneficiario" value="<?php echo $beneficiario_cheque;?>">
		<input type="hidden" name="id_beneficiario" value="<?php echo $id_beneficiario_cheque;?>">
		<input type="hidden" name="sopg" value="<?php echo $sopg;?>">
		<input type="hidden" name="monto_cheque" value="<?php echo $monto_cheque_float;?>">
		</td>
	</tr>
	<?php if ($total_imputacion>0) { ?>
	<tr>
	  <td height="18">&nbsp;</td>
	  </tr>
	<tr>
	<td colspan="2">
		<table width="100%" border="0"  class="tablaalertas">
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
		    		<input name=<?php echo "centro_gestor".$ii;?> type="text" class="normal" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="readonly"/>
                  </td>
                  <td  class="normal" align="center">
                    <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="normal" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="readonly"/>
		   			<input name=<?php echo "centro_costo".$ii;?>  type="text" class="normal" id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="readonly"/>
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
		<table width="90%" class="tablaalertas" align="center" id="tbl_retenciones" border="0" >
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
			<table width="100%" class="tablaalertas" align="center" id="tbl_otras_retenciones" border="0">
			<tr class="td_gray">
			<td colspan="2" align="center" class="normalNegroNegrita"> OTRAS RETENCIONES </td>
			</tr>
			<tr>
			<td class="normalNegrita" align="center">Partida</td>
			<td class="normalNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<? for ($ii=0; $ii<$elem_otras_rete; $ii++)  {	?>
			<tr><td lass="normal">
			<input name="<?php echo "part_".$ii?>" type="text"  class="normal" value="<?php echo  $id_partida[$ii].":".$nombre_part[$ii];?>" size="25" maxlength="25" align="right" id="<?php echo "part_".$ii?>" disabled="disabled"/></td>
			<td class="normal">
			<input name="<?php echo "monto_rete".$ii?>" type="text"  class="normal" value="<?php echo  number_format($monto_rete[$ii],2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$ii?>" disabled="disabled"/></td></tr>
			<?}?>
			<tr>
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
		  <td colspan="2" class="normal">
		 <?	include("documentos/opciones_2.php");?>		 
		 </td>
    </tr>	
	</table>
</form>
</body>
</html>