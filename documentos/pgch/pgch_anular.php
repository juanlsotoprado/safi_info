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
?>
<?php
$codigo_pgch= $codigo;


if ($request_id_opcion==5) $estado=7; /*Devuelto*/
else if ($user_perfil_id == "46450") $estado=13; /*Aprobado*/
else $estado=10; /*TRANSITO*/

$sql = "
	SELECT
		s.sopg_monto as sopg_monto,
		pch.pres_anno_docg as ano,
		d.depe_nombre as nombre_dependencia,
		pch.pgch_fecha as fecha_pgch,
		pch.nro_cuenta as numero_cuenta,
		pch.docg_id as sopg,
		pch.pgch_asunto as asunto,
		pch.pgch_obs as observaciones,
		b.banc_nombre as nombre_banco,
		upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante,
		em.empl_email as email,
		em.empl_tlf_ofic as telefono,
		ch.nro_cheque as numero_cheque,
		ch.monto_cheque as monto_cheque,
		upper(ch.beneficiario_cheque) as beneficiario_cheque,
		t.nombre_sol as tipo_solicitud
	FROM
		sai_pago_cheque pch
		INNER JOIN sai_sol_pago s ON (s.sopg_id = pch.docg_id)
		LEFT JOIN sai_cheque ch ON (pch.docg_id = ch.docg_id)
		INNER JOIN sai_ctabanco ctb ON (ctb.ctab_numero = pch.nro_cuenta)
		INNER JOIN sai_banco b ON (b.banc_id = ctb.banc_id)
		INNER JOIN sai_dependenci d ON (d.depe_id = pch.depe_id)
		INNER JOIN sai_doc_genera dg ON (dg.docg_id = pch.pgch_id)
		INNER JOIN sai_empleado em ON (em.empl_cedula = dg.usua_login)
		INNER JOIN sai_tipo_solicitud t ON (t.id_sol = s.sopg_tp_solicitud)
	WHERE
		pch.pgch_id = '".$codigo."'
";

$resultado=pg_query($conexion,$sql);

if ($row=pg_fetch_array($resultado)) {
	$sopg_monto=number_format(trim($row['sopg_monto']),2,',','.');
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
	if($monto_cheque != '') $monto_cheque = number_format(trim($row['monto_cheque']),2,',','.');
	$beneficiario_cheque = trim($row['beneficiario_cheque']);
	$anno_id_doc_imputacion = trim($row['ano']);
	//Buscar tipo de doc principal
	$tipo_doc = substr($pgch_docg_id,0,4);	
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
					 
	//Monto total del cheque descontando las retenciones es
	$monto_cheque_antes_ret = $monto_cheque;
	//$monto_cheque = $monto_cheque - $tt_retencion;					 
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

if ($request_id_objeto_sig == 99) { //Está aprobando el adjunto. Fin de las aprobaciones

	$sql= "Select * from sai_pres_crear_pagado(true, '". $codigo."',".$anno_id_doc_imputacion .",'";
	$sql.= $codigo."','Pagado del documento origen ". $sopg . "','".$arreglo_uel ."','";
	$sql.= $arreglo_tipo_impu."','".$arreglo_acc_pp."','".$arreglo_acc_esp."','";
	$sql.= $arreglo_sub_esp."','". $arreglo_monto ."','". $arreglo_apde_abono ."','";
	$sql.= $arreglo_apde_descr."') as pagado";
	$resultado_set = pg_exec($conexion ,$sql) ;
    $valido= $resultado_set;
	$disponibilidad= $resultado_set;
	if ($resultado_set) {
		$row = pg_fetch_array($resultado_set,0);
		$pagado=$row[0];
		$vector=explode ("*" ,   $pagado);
		$pagado= $vector[1];
	}

	$sql = " SELECT * FROM sai_modificar_estado_doc_genera('$sopg',13) as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");

	$sql_caus = "select cpat_id, cpat_nombre, rcomp_haber
	from sai_comp_diario c, sai_reng_comp r
	where c.comp_id=r.comp_id and c.comp_id like 'coda%' and c.comp_doc_id='".$sopg."' and c.esta_id<>15 and c.comp_comen not like 'A-%' 
	and oid in (
	select max(oid)
	from sai_comp_diario c, sai_reng_comp r
	where c.comp_id=r.comp_id and c.comp_id like 'coda%' and c.comp_doc_id='".$sopg."')";
	$resultado_set = pg_query($conexion ,$sql_caus) or die("Error al 
	Generar Pagado");
	if ($resultado_set) {
		$row = pg_fetch_array($resultado_set,0);
		$cpat_id = trim($row["cpat_id"]);
		$cpat_nombre=trim($row["cpat_nombre"]);
		$cpat_monto=trim($row["rcomp_haber"]);
	}

	$sql_caus = "SELECT * FROM sai_insert_comp_autopagado_actual('".$cpat_id."', '". $cpat_nombre."', '". $cpat_monto."','".$codigo."', '".$dependencia_solicitante."','".$numero_cheque."') AS resultado";
	$resultado_set = pg_exec($conexion ,$sql_caus) or die("Error al 
	Generar Pagado");

	$sql = " SELECT * FROM sai_modificar_estado_doc_pres('$codigo',47) as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");	

	$sql = " SELECT * FROM sai_modificar_estado_doc_pres('$sopg',47) as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al modificar 
	Estado Presupuestario");


	//REGISTRAR MOVIMIENTO EN LA CUENTA DEL BANCO
	$pgch_monto_float = $cheque_monto2 ;
	$sql_mov = " SELECT * FROM sai_insert_mov_cta_banc('".$numero_cuenta."','".$numero_cheque."','".$sopg."',".$monto_cheque_float.") as resultado ";	
	$resultado_mov = pg_query($conexion,$sql_mov) or die("Error al registrar movimiento bancario");
	if ($row_mov = pg_fetch_array($resultado_mov)) {
		$registro_mov = $row_mov["resultado"];	
	}
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
	$result_insert_comp_auto=pg_query($conexion,$sql_edo) or die("Error al CAMBIAR ESTADO ");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI: Pago cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" src="js/func_montletra.js"></script>
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script language="javascript">
function revisar() {   
	if(document.form.txt_motivo.value=="") {
	  alert('Debe especificar el motivo por el cual anula este pago con cheque');
	  document.form.txt_motivo.focus();
	  return;
	}
	else
	if(confirm("Est\u00e1 seguro que desea continuar?")) {
	document.form.action="documentos/pgch/pgch_eanular.php";    
	document.form.submit();
    }	
}

function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');
  	ver_monto_letra(monto ,'txt_monto_letras','');
}

</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body  onload="monto_en_letras(<? echo $monto_cheque;?>)">
<form name="form" method="post">
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
			<tr class="td_gray">
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
			<tr class="td_gray">
			<td class="normalNegrita">TOTAL OTRAS RETENCIONES:</td>
			<td class="normalNegrita"><input name="monto_rete_part" type="text" class="normal" id="monto_rete_part" value="<?php echo(number_format($total_otras_rete,2,'.',',')); ?>" size="25" maxlength="25" readonly="readonly" align="right"/> </td>
			</tr>
			</table>
		</td>
	</tr>
<?}?>
	<tr class="td_gray"> 
	<td colspan="2" class="normalNegroNegrita">ANULACI&Oacute;N DEL PAGO CON CHEQUE</td>
	</tr>	
	<tr>
	<td class="normalNegrita">Motivo:</td>
	<td><textarea name="txt_motivo" class="normal" rows="3" cols="80"></textarea></td>
	</tr>
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
	<td colspan="2">
	<div align="center">
	<input type="button" value="Anular" onclick="javascript:revisar();" />
	</div>
	<input type="hidden" name="pres_anno" value="<?echo $anno_id_doc_imputacion;?>">
	<input type="hidden" name="cod_sopg" value="<?echo $sopg;?>">
	<input type="hidden" name="cod_pgch" value="<?echo $codigo;?>">
	</td>
	</tr>
	</table>
	</form>
	</body>
</html>