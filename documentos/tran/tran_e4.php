<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();

$a_oPresupuesto = $_SESSION['an_o_presupuesto'];

/*Parche para que se puedan aprobar tran del año anterior, comentar para tran del año actual*/
//$a_oPresupuesto = '2014';
?>
<?php
$cod_doc = $request_codigo_documento;
$codigo= $cod_doc;
$obs=trim($_POST['txt_observa']);

if ($request_id_opcion==5) $estado=7; /*Devuelto*/
else if ($user_perfil_id == "71450") $estado=13; /*Aprobado*/
else $estado=10; /*TRANSITO*/

$sql="SELECT 
			ptr.depe_id AS depe_id,
			ptr.trans_fecha AS fecha_tran, 
			ptr.nro_cuenta_emisor AS numero_cuenta_emisor, 
			--ptr.nro_cuenta_receptor AS numero_cuenta_receptor,  
			ptr.docg_id AS sopg, 
			ptr.trans_asunto AS asunto, 
			ptr.trans_obs AS observaciones, 
			ptr.nro_referencia AS numero_referencia, 
			ptr.trans_monto AS monto_tran, 
			ptr.pres_anno_docg AS ano,
			UPPER(ptr.beneficiario) AS beneficiario,				
			s.sopg_monto AS sopg_monto, 
			d.depe_nombre AS nombre_dependencia,
 			ctbe.cpat_id as cpat_id,		 
			be.banc_nombre AS nombre_banco_emisor,
			cpat.cpat_nombre as cpat_nombre_emisor,		  
			UPPER(em.empl_nombres) || UPPER(em.empl_apellidos) AS usuario_solicitante, 
			em.empl_email AS email, 
			em.empl_tlf_ofic AS telefono, 
 			t.nombre_sol AS tipo_solicitud 
 		FROM sai_pago_transferencia ptr
			INNER JOIN sai_sol_pago s ON (s.sopg_id = ptr.docg_id)
			INNER JOIN sai_dependenci d ON (ptr.depe_id = d.depe_id)
			INNER JOIN sai_ctabanco ctbe ON (ptr.nro_cuenta_emisor = ctbe.ctab_numero)
			INNER JOIN sai_banco be ON (ctbe.banc_id = be.banc_id) 
			INNER JOIN sai_doc_genera dg ON (dg.docg_id = ptr.trans_id) 
			INNER JOIN sai_empleado em ON (dg.usua_login = em.empl_cedula) 
			INNER JOIN sai_tipo_solicitud t ON (s.sopg_tp_solicitud=t.id_sol) 
 			INNER JOIN sai_cue_pat cpat ON (ctbe.cpat_id=cpat.cpat_id)
		WHERE  ptr.trans_id = '".$codigo."'";

$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
	$id_dependencia = trim($row['depe_id']);
	$dependencia_solicitante = trim($row['nombre_dependencia']);
	$fecha_tran = trim($row['fecha_tran']);
	$numero_referencia = trim($row['numero_referencia']);
	$tipo_solicitud=trim($row['tipo_solicitud']);		
	$sopg = trim($row['sopg']);	
	$asunto = trim($row['asunto']);
	$usuario_solicitante = trim($row['usuario_solicitante']);
    $observaciones=trim($row['observaciones']);
	$email_solicitante=trim($row['email']);
	$telefono_solicitante= trim($row['telefono']);
	$numero_cuenta_emisor = trim($row['numero_cuenta_emisor']);	
	$cpat_id_emisor = trim($row['cpat_id']);	
	$cpat_nombre_emisor = trim($row['cpat_nombre_emisor']);
	$numero_cuenta_receptor=trim($row['numero_cuenta_receptor']);
	$nombre_banco_emisor = trim($row['nombre_banco_emisor']);
	$monto_tran_float = trim($row['monto_tran']);
	$monto_tran = number_format(trim($row['monto_tran']),2,',','.');
	$beneficiario = trim($row['beneficiario']);
	$anno_id_doc_imputacion= trim($row['ano']);
	//Buscar tipo de doc principal
	$tipo_doc = substr($pgch_docg_id,0,4);		
}

/*Este query era para cuando se concebía la idea de banco receptor. En la acutalidad no es así
$sql="SELECT
			ptr.nro_cuenta_receptor AS numero_cuenta_receptor, 
			br.banc_nombre as nombre_banco_receptor, 
			ctbr.cpat_id as cpat_id, 
			cpat.cpat_nombre as cpat_nombre_receptor
	FROM sai_pago_transferencia ptr 
			sai_ctabanco ctbr ON (ptr.nro_cuenta_receptor=ctbr.ctab_numero)
			sai_banco br ON (ctbr.banc_id=br.banc_id)
			sai_cue_pat cpat ON (cpat.cpat_id = ctbr.cpat_id)
	WHERE   ptr.trans_id='".$codigo."'";
$resultado=pg_query($conexion,$sql);
if ($row=pg_fetch_array($resultado)) {
	$nombre_banco_receptor = trim($row['nombre_banco_receptor']);
	$cpat_id_receptor = trim($row['cpat_id']);
	$cpat_nombre_receptor = trim($row['cpat_nombre_receptor']);
}		
*/

//Buscar las retenciones
/*SELECT * 
		FROM sai_buscar_sopg_reten ('".trim($sopg)."') AS resultado
		(docu_id varchar, 
		impu_id varchar, 
		rete_monto float8,  
		por_rete float4,";
por_imp float4,servicio varchar,monto_base float8)";
$resultado_set= pg_exec($conexion ,$sql);
$valido=$resultado_set;
if ($resultado_set) {*/
$sql= "SELECT sopg_id,
			impu_id,
			sopg_ret_monto,
			sopg_por_rete,
			sopg_por_imp,
			sopg_servicio,
			sopg_monto_base
		FROM sai_sol_pago_retencion
		WHERE lower(trim(sopg_id)) = '".trim($sopg)."'";

$resultado_set=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
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
	//$monto_cheque = $monto_cheque - $tt_retencion;					 
} 

/*Consulto las OTRAS retenciones del documento */
$sql="SELECT sopg_partida_rete,
			sopg_ret_monto,
			part_nombre 
		FROM sai_sol_pago_otra_retencion t1
		INNER JOIN sai_partida t2 ON (t1.sopg_partida_rete = t2.part_id) 
		WHERE sopg_id='".$sopg."'   
			AND t2.pres_anno='".$a_oPresupuesto."'"; 
//echo $sql;
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

//Buscar las Imputaciones
$total_imputacion=0;
$monto_temp_vi=0;
	 
/*$sql= "SELECT  * from sai_buscar_sopg_imputacion('".trim($sopg)."') as result ";
$sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";*/

$sql = "SELECT
			sai_sol_pago_imputa.sopg_id,
			sai_sol_pago_imputa.sopg_acc_pp,
			sai_sol_pago_imputa.sopg_acc_esp,
			sai_sol_pago_imputa.depe_id, 
			sai_sol_pago_imputa.sopg_sub_espe, 
			sai_sol_pago_imputa.sopg_monto,
			sai_sol_pago_imputa.sopg_tipo_impu,
			sai_sol_pago_imputa.sopg_monto_exento
 		FROM sai_sol_pago_imputa
 		INNER JOIN sai_sol_pago ON (sai_sol_pago.sopg_id = sai_sol_pago_imputa.sopg_id)
		WHERE  trim(sai_sol_pago_imputa.sopg_id)='".trim($sopg)."'";

$resultado_set= pg_exec($conexion ,$sql);
$valido=$resultado_set;
if ($resultado_set)	{
	$total_imputacion=pg_num_rows($resultado_set);
	$i=0;
	while($row=pg_fetch_array($resultado_set)) {
			$matriz_imputacion[$i]=trim($row['sopg_tipo_impu']);
			$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']); // proy o acc
			$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']); // acc esp
			$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']); //sub-part
			$matriz_uel[$i]=trim($row['depe_id']); //depe
			$matriz_monto[$i]=$row['sopg_monto'] + $row['sopg_monto_exento']; //monto
			$matriz_monto_exento[$i]=trim($row['sopg_monto_exento']); //monto
			$matriz_abono[$i]='0'; //monto
			$matriz_detalle[$i]="Pagado del documento origen ". $sopg; 
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

	if ( $total_imputacion>0) {
			require_once("includes/arreglos_pg.php");
			$arreglo_uel=convierte_arreglo($matriz_uel);
			$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion);
			$arreglo_acc_pp=convierte_arreglo( $matriz_acc_pp);
			$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp);
			$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp);
			$arreglo_monto=convierte_arreglo($matriz_monto);
			$arreglo_apde_abono=convierte_arreglo($matriz_abono);
			$arreglo_apde_descr=convierte_arreglo($matriz_detalle);
	}		
}

if ($request_id_objeto_sig == 99) { //Está aprobando el tesorero. Fin de las aprobaciones

	$sql_caus = "SELECT
					cpat_id, 
					cpat_nombre, 
					rcomp_haber
				FROM sai_reng_comp
				WHERE comp_id LIKE 'coda%' 
					AND comp_id IN (
							SELECT comp_id
							FROM sai_comp_diario
							WHERE comp_doc_id='".$sopg."'
								AND esta_id!=15
								AND comp_comen LIKE 'C-%' 
								AND oid IN (
									SELECT MAX(OID)
									FROM sai_reng_comp r2
									WHERE r2.comp_id LIKE 'coda-%' AND
										r2.comp_id IN (SELECT 
															comp_id 
														FROM sai_comp_diario 
														WHERE comp_doc_id='".$sopg."'
															AND esta_id !=15
														) 
										)
					)";
	$resultado_set = pg_query($conexion ,$sql_caus) or die("Error al seleccionar el maximo coda del causado");
	if ($resultado_set) {
		$row = pg_fetch_array($resultado_set,0);
		$cpat_id = trim($row["cpat_id"]);
		$cpat_nombre=trim($row["cpat_nombre"]);
		$cpat_monto=trim($row["rcomp_haber"]);
	}


	$sql = "SELECT * FROM generar_pagado ('".$cpat_id."', '". $cpat_nombre."', '". $cpat_monto."','".$codigo."', '".$id_dependencia."','".$numero_referencia."', '".$numero_cuenta_emisor."','".$sopg."',".$monto_tran_float.",'".$numero_cuenta_receptor."',".$anno_id_doc_imputacion .",'".$arreglo_uel ."','".$arreglo_tipo_impu."','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_sub_esp."','". $arreglo_monto ."','". $arreglo_apde_abono ."','".$arreglo_apde_descr."') as pagado";
	//echo $sql;	
	$resultado = pg_query($conexion,$sql) or die("Error al generar el pagado");
	
	$sql = "UPDATE sai_pago_transferencia 
			SET esta_id=13 
			WHERE trans_id = '".$codigo."'";
	$resultado = pg_query($conexion,$sql) or die("Error al cambiar estatus de la transferencia");
	
}

/*CUANDO DEVUELVE EL PGCH*/
if ($request_id_opcion==5)  {  
   	$memo_contenido=$_POST['contenido_memo'];	
   	$query_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$memo_contenido."', 'Devolución del Pago con Cheque','0', '0','0','',0, 0, '0', '','".$codigo."') as memo_id");
   	//echo $query_memo;
	
   	$resultado_set = pg_exec($conexion ,$query_memo);
	$valido=$resultado_set;
	if($resultado_set) 	{
		$row = pg_fetch_array($resultado_set,0); 
		if ($row[0] <> null) {
		  $memo_id=$row[0];
		}
	}
	$sql_edo = "SELECT * FROM sai_modificar_estado_doc_genera('".trim($codigo)."','7') AS RESULTADO";
	$result_insert_comp_auto=pg_query($conexion,$sql_edo) or die("Error al cambiar el estado");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI: Pago con Transferencia</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" src="js/func_montletra.js"></script>
<script language="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript">
function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');
}
</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="monto_en_letras(<? echo str_replace(",",".",str_replace(".","",$monto_tran));?>)">
<table width="100%" cellspacing="10" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" >
<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita">PAGO CON TRANSFERENCIA NRO.: <a href="javascript:abrir_ventana('documentos/tran/tran_detalle.php?codigo=<?php echo trim($codigo);?>')"><?php echo $codigo;?></a></td>
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
		<td class="normal"><?php echo $monto_tran; ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto en letras:</td>
		<td class="normal"><input type="text" name="txt_monto_letras" id="txt_monto_letras" size="100" class="normal" readonly></td>
	</tr>		
	<tr>
		<td class="normalNegrita">Forma pago:</td>
		<td class="normal"> Transferencia</td>
	</tr>
	<tr>
  		<td class="normalNegrita">Nro. Referencia:</td>
		<td class="normal"><?php echo $numero_referencia;?></td>
	</tr>
	<tr>	
		<td class="normalNegrita">Banco:</td>
		<td class="normal"><?php echo $nombre_banco_emisor;?></td>
	</tr>
	<tr>	
		<td class="normalNegrita">Banco receptor:</td>
		<td class="normal"><?php echo $nombre_banco_receptor;?></td>
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
			<?include("includes/respaldos_mostrar.php");?>
		</table>	
	</td>
	</tr>
		<tr>
		<td height="66" colspan="3" class="normal">
		<div align="center">
		<br><br>Este pago con transferencia fue revisado el d&iacute;a: 
		<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?><br><br>
		<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
		<br><br>
		</tr>
	</table>
	</body>
</html>