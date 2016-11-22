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

$cod_doc = $request_codigo_documento;
$codigo= $cod_doc;
$valido=0;
	
$vector=explode ("-" , $_POST['banco_emisor']);
$nombre_banco_emisor=$vector[0];
$cuenta_banco_emisor=trim($vector[1]);
$cuenta_anterior = trim($_POST['cuenta_anterior']);	
$concepto_pago = trim($_POST['txt_concepto']);	
$observacion = trim($_POST['txt_observa']);
$monto_cheque=trim($_POST['monto_cheque']);
$id_cheque_ant=trim($_POST['id_cheque_anterior']);
$nombre_beneficiario=trim($_POST['nombre_beneficiario']);
$id_beneficiario=trim($_POST['id_beneficiario']);
$sopg=trim($_POST['sopg']);

$sql="UPDATE sai_pago_cheque set pgch_asunto='".$concepto_pago."', pgch_obs='".$observacion."' WHERE pgch_id='".$codigo."'";
$resultado= pg_exec($conexion ,$sql);
if ($resultado) {
      $valido=1;
      include("includes/respaldos_e1.php");
}

if (strcmp($cuenta_anterior,$cuenta_banco_emisor)!=0) {
	$disponibilidad = 0;
	$sql="SELECT monto_haber AS dispone FROM sai_ctabanco_saldo WHERE ctab_numero='".$cuenta_banco_emisor."' and docg_id='sb-".$_SESSION['an_o_presupuesto']."'";
	$resultado=pg_query($conexion,$sql);
	if($row=pg_fetch_array($resultado)) $monto_cuenta = $row['dispone'];

	$sql="SELECT (SUM(monto_haber) - SUM(monto_debe)) AS dispone FROM sai_ctabanco_saldo WHERE ctab_numero='".$cuenta_banco_emisor."'";
	$resultado=pg_query($conexion,$sql);
	if($row=pg_fetch_array($resultado)) {		
		$monto_cuenta += $row['dispone'];
		if ($monto_cheque <= $monto_cuenta)	{ //Si el monto a pagar es < que el disponible
			$disponibilidad = 1; 
		}
	}

	if ($disponibilidad<1) {
		$error = 1; 
	 	$mensaje_error_pgch = "No hay disponibilidad en la cuenta ".$cuenta_banco_emisor;
	 	$valido =0;
	}
	else {	
		//Buscar el siguiente cheque autorizado
		$id_nro_cheque = "";
		$nro_cheque = "";
		$nro_chequera = "";
				
		//Verificar que la cuenta tiene chequera activa
		$sql="select * from sai_verificar_cuenta_chequera_activa('".$cuenta_banco_emisor."') as nro_chequera ";
		$nro_chequera="0";
		$resultado=pg_query($conexion,$sql);
		if ($row=pg_fetch_array($resultado)) {
		 $nro_chequera = trim($row['nro_chequera']);		
		}
	
		//Si no tiene chequera
		if ($nro_chequera == "0") {
		 $error = 1; 
		 $mensaje_error_pgch = "La cuenta ".$cuenta_banco_emisor." no tiene chequeras activas";
		 $disponibilidad = 0;
		 $valido =0;
		}
		else { 
			$sql="select * from sai_buscar_cheque_activo('$nro_chequera') resultado_set (id_cheque varchar, nro_cheque varchar) ";
			$resultado=pg_query($conexion,$sql);
			if ($row=pg_fetch_array($resultado)) {					
			  $id_nro_cheque = trim($row['id_cheque']);
			  $nro_cheque = trim($row['nro_cheque']);				
			} 
			//LIBERAR CHEQUE UTILIZADO , BORRAR DATOS DE LA TABLA SAI_CHEQUE_ESTADOS Y ASIGNAR NUEVO CHEQUE//
			$sql="DELETE FROM sai_cheque_estados where id_cheque=".$id_cheque_ant;
			$resultado=pg_query($conexion,$sql) or die("Error al borrar cheque estados");
			$sql="UPDATE sai_cheque SET estatus_cheque=1, monto_cheque=null, fechaemision_cheque=null, beneficiario_cheque=null, docg_id=null, ci_rif_beneficiario_cheque=null where id_cheque=".$id_cheque_ant;
			$resultado=pg_query($conexion,$sql) or die("Error al iniciar cheque");
			//emitir nuevo cheque
			$sql = " SELECT * FROM sai_emitir_cheque('$id_nro_cheque',$monto_cheque,'$nombre_beneficiario','$id_beneficiario','$sopg', ' ') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al emitir el cheque");
			$sql="UPDATE sai_pago_cheque set nro_cuenta='".$cuenta_banco_emisor."', id_nro_cheque='".$id_nro_cheque."' WHERE pgch_id='".$codigo."'";
			$resultado = pg_query($conexion,$sql) or die("Error al actualiar pago cheque");
	
		}
	}
}//FIN
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Modificar pago con cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript">
function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');

}</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="monto_en_letras(<? echo str_replace(",",".",str_replace(".","",$monto_cheque));?>)">
<?php
if ($valido==1) {

$sql="select pch.depe_id as depe_id, s.sopg_monto as sopg_monto, pch.pres_anno_docg as ano, d.depe_nombre as nombre_dependencia, pch.pgch_fecha as fecha_pgch, pch.nro_cuenta as numero_cuenta, pch.docg_id as sopg, pch.pgch_asunto as asunto, pch.pgch_obs as observaciones, b.banc_nombre as nombre_banco,  upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante, em.empl_email as email, em.empl_tlf_ofic as telefono, ch.nro_cheque as numero_cheque, ch.monto_cheque as monto_cheque, upper(ch.beneficiario_cheque) as beneficiario_cheque, t.nombre_sol as tipo_solicitud
from sai_pago_cheque pch, sai_cheque ch, sai_banco b, sai_dependenci d, sai_ctabanco ctb, sai_doc_genera dg, sai_empleado em, sai_tipo_solicitud t, sai_sol_pago s
where s.sopg_id=pch.docg_id and s.sopg_tp_solicitud=t.id_sol and pch.docg_id=ch.docg_id and pch.depe_id=d.depe_id and pch.nro_cuenta=ctb.ctab_numero and ctb.banc_id=b.banc_id and pch.depe_id=d.depe_id and dg.docg_id=pch.pgch_id and dg.usua_login=em.empl_cedula and pch.pgch_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
	$id_dependencia = trim($row['depe_id']);
	$dependencia_solicitante = trim($row['nombre_dependencia']);
	$fecha_pgch = trim($row['fecha_pgch']);
	$fecha_cheque = trim($row['fechaemision_cheque']);	
	$numero_cuenta = trim($row['numero_cuenta']);
	$numero_cheque = trim($row['numero_cheque']);
	$sopg = trim($row['sopg']);	
	$tipo_solicitud=trim($row['tipo_solicitud']);		
	$asunto = trim($row['asunto']);
	$usuario_solicitante = trim($row['usuario_solicitante']);
    $observaciones=trim($row['observaciones']);
	$email_solicitante=trim($row['email']);
	$telefono_solicitante= trim($row['telefono']);
	$nombre_banco = trim($row['nombre_banco']);
	$monto_cheque_float = $row['monto_cheque'];
	$monto_cheque = number_format(trim($row['monto_cheque']),2,',','.');
	$beneficiario_cheque = trim($row['beneficiario_cheque']);
	$anno_id_doc_imputacion = trim($row['ano']);
	//Buscar tipo de doc principal
	$tipo_doc = substr($pgch_docg_id,0,4);	
}			
	
//Buscar las Imputaciones
$total_imputacion=0;
$monto_temp_vi=0;

 
	  $sql= " Select * from sai_buscar_sopg_imputacion('".trim($sopg)."') as result ";
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
				$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
				$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
				$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
				$matriz_uel[$i]=trim($row['depe_id']); //depe
				$matriz_monto[$i]=trim($row['sopg_monto']); //monto
				$matriz_monto_exento[$i]=trim($row['sopg_monto_exento']); //monto
				$monto_temp_vi= $monto_temp_vi + $matriz_monto[$i];
				$i++;
			}


		if ($matriz_imputacion[0]==1){//Por Proyecto
		 $query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		}else{//Por Accion Centralizada
		 $query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') as result (centro_gestor varchar, centro_costo varchar)";
		 }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   while($row=pg_fetch_array($resultado_query)){
		   $centrog = trim($row['centro_gestor']);
		   $centroc = trim($row['centro_costo']);
		   }
		 }

		}
?>
<table width="100%" cellspacing="10" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">PAGO CON CHEQUE NRO.: <a href="javascript:abrir_ventana('documentos/pgch/pgch_detalle.php?codigo=<?php echo trim($codigo);?>')"><?php echo $codigo;?></a></td>
	</tr>
	<tr class="td_gray"> 
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
	<tr class="td_gray"> 
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
		<td class="normal"><input type="text" name="txt_monto_letras" id="txt_monto_letras" size="100" class="peq" readonly></td>
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
		<td class="normal"><?php echo $nombre_banco;?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Concepto del pago:</div></td>
		<td class="normal"><?php echo $asunto;?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Observaciones del pago:</div></td>
		<td class="normal"><?php echo $observaciones;?></td>
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
          <tr class="peqNegrita" align="center">
                  <td>ACC.C/PP</td>
                  <td>Acc Espec&iacute;fica</td>
                  <td>Dependencia</td>
                  <td>Partida</td>
                  <td>Monto Sujeto</td>
		 		  <td>Monto Exento</td>
               	</tr>
                <tr>
               <?php for ($ii=0; $ii<$total_imputacion; $ii++)  {	?>
                  <td  class="peq" align="center">
                  	<input name=<?php echo "txt_imputa_proyecto_accion".$ii;?> type="hidden" class="peq" id=<?php echo "txt_imputa_proyecto_accion".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="readonly"/>
		    		<input name=<?php echo "centro_gestor".$ii;?> type="text" class="peq" id=<?php echo "centro_gestor".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo  $matriz_acc_pp[$ii];?>" readonly="readonly"/>
                  </td>
                  <td  class="peq" align="center">
                    <input name=<?php echo "txt_imputa_accion_esp".$ii;?>  type="hidden" class="peq" id=<?php echo "txt_imputa_accion_esp".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="readonly"/>
		   			<input name=<?php echo "centro_costo".$ii;?>  type="text" class="peq" id=<?php echo "centro_costo".$ii;?> size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_acc_esp[$ii];?>" readonly="readonly"/>
                  </td>
                  <td  class="peq" align="center">
                      <input name=<?php echo "txt_imputa_unidad".$ii;?>  type="text" class="peq" id=<?php echo "txt_imputa_unidad".$ii;?> size="10" maxlength="10"   align="right"  align="right" value="<?php echo $matriz_uel[$ii];?>" readonly="readonly"/>
                  </td>
                  <td  class="peq" align="center">
                      <input name="<?php echo "txt_imputa_sub_esp".$ii;?>"  type="text" class="peq" id="<?php echo "txt_imputa_sub_esp".$ii;?>" size="15" maxlength="15"   align="right"  align="right" value="<?php echo $matriz_sub_esp[$ii];?>" readonly="readonly" title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al Valor Agregado";}?>"/>
                  </td>
                  <td  class="peq" align="center">
                      <input name="<?php echo "txt_imputa_monto".$ii;?>"  type="text" class="peq" id="<?php echo "txt_imputa_monto".$ii;?>" size="25" maxlength="25"   align="right"  align="right" value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>" readonly="readonly" />
                  </td>
                  <td  class="peq" align="center">
                      <input name="<?php echo "txt_imputa_monto_exento".$ii;?>"  type="text" class="peq" id="<?php echo "txt_imputa_monto_exento".$ii;?>" size="25" maxlength="25"   align="right"  align="right" value="<?php echo  number_format($matriz_monto_exento[$ii],2,'.',',');?>" readonly="readonly" />
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
			
			<tr class="td_gray">
			<td width="63%" class="normalNegrita" align="center">Tipo de Servicio</td>
			<td width="10%" class="normalNegrita" align="center">Tipo de Retenci&oacute;n</td>
			<td width="10%" class="normalNegrita" align="center">Impuesto %</td>
			<td width="12%" class="normalNegrita" align="center">% de Retenci&oacute;n</td>
			<td width="15%" class="normalNegrita" align="center">Monto Retenido(Bs.)</td>
			</tr>
			<?php for ($ii=0; $ii<$elem_existe; $ii++)  {?>
			<tr>
			<td width="64%" class="normal" align="center">
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii?>" type="text"  class="peq" value="<?php echo $servicio_doc[$ii];?>" size="60" maxlength="60" readonly="readonly"/> </td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii?>" id= "<?php echo "txt_impuesto".$ii?>" value="<?php echo $id_impuesto_doc[$ii];?>" class="peq" size="10" readonly="readonly"/></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii?>" id= "<?php echo "txt_iva_".$ii?>" value="<?php if ($por_imp_doc[$ii]==0) {echo "s/n";} else {echo $por_imp_doc[$ii];}?>" class="peq" size="6" readonly="readonly"/></td>
			<td width="12%" class="normal" align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo $por_rete_doc[$ii]; ?>" class="peq" size="6" readonly="readonly"/></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"  class="peq" value="<?php echo  (number_format($rete_monto_doc[$ii],2,'.',',')); ?>"   size="25" maxlength="25" readonly="readonly" align="right" id="<?php echo "txt_monto_impu".$jj?>"/>			</td>
			</tr>
			<?php
			} //-->Del For 
		?>
			<tr class="td_gray">
			<td height="19" colspan="1" align="left" class="normalNegrita">TOTAL RETENCIONES:</td>
			<td height="19" colspan="4" align="right" class="normalNegrita"><input name="txt_monto_retenciones_tt" type="text" class="peq" id="txt_monto_retenciones_tt" value="<?php echo(number_format($tt_retencion,2,'.',',')); ?>" size="25" maxlength="25" readonly="readonly" align="right"/> </td>
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
			<?include("includes/respaldos_mostrar.php");?>
		</table>	
	</td>
	</tr>
		<tr>
		<td height="66" colspan="3" class="normal">
		<div align="center">
		<br><br>Este pago con cheque fue revisado el d&iacute;a: 
		<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?><br><br>
		<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
		</div></td>
		</tr>
	</table>
	<?php }
	else echo $mensaje_error_pgch;
	?>	
</body>
</html>