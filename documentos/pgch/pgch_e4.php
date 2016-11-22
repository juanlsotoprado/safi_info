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

/*Parche para que se puedan aprobar pgch del año anterior, 
 * comentar para pgch del año actual*/
//$a_oPresupuesto = '2014';
?>
<?php
$cod_doc = $request_codigo_documento;
$codigo= $cod_doc;
$obs=trim($_POST['txt_observa']);

if ($request_id_opcion==5) $estado=7; /*Devuelto*/
else if ($user_perfil_id == "71450") $estado=13; /*Aprobado*/
else $estado=10; /*TRANSITO*/

/*$sql="SELECT pch.depe_id as depe_id, s.sopg_monto as sopg_monto, pch.pres_anno_docg as ano, d.depe_nombre as nombre_dependencia, pch.pgch_fecha as fecha_pgch, pch.nro_cuenta as numero_cuenta, pch.docg_id as sopg, pch.pgch_asunto as asunto, pch.pgch_obs as observaciones, b.banc_nombre as nombre_banco,  upper(em.empl_nombres) || upper(em.empl_apellidos) as usuario_solicitante, em.empl_email as email, em.empl_tlf_ofic as telefono, ch.nro_cheque as numero_cheque, ch.monto_cheque as monto_cheque, upper(ch.beneficiario_cheque) as beneficiario_cheque, t.nombre_sol as tipo_solicitud
from sai_pago_cheque pch, sai_cheque ch, sai_banco b, sai_dependenci d, sai_ctabanco ctb, sai_doc_genera dg, sai_empleado em, sai_tipo_solicitud t, sai_sol_pago s
where s.sopg_id=pch.docg_id and s.sopg_tp_solicitud=t.id_sol and pch.docg_id=ch.docg_id and pch.depe_id=d.depe_id and pch.nro_cuenta=ctb.ctab_numero and ctb.banc_id=b.banc_id and pch.depe_id=d.depe_id and dg.docg_id=pch.pgch_id and dg.usua_login=em.empl_cedula and pch.pgch_id='".$codigo."'";*/

$sql = "SELECT 
			pch.depe_id AS depe_id, 
			s.sopg_monto AS sopg_monto, 
			pch.pres_anno_docg AS ano, 
			d.depe_nombre AS nombre_dependencia, 
			pch.pgch_fecha AS fecha_pgch, 
			pch.nro_cuenta AS numero_cuenta, 
			pch.docg_id AS sopg, 
			pch.pgch_asunto AS asunto, 
			pch.pgch_obs AS observaciones, 
			b.banc_nombre AS nombre_banco,  
			UPPER(em.empl_nombres) || UPPER(em.empl_apellidos) AS usuario_solicitante, 
			em.empl_email AS email, 
			em.empl_tlf_ofic AS telefono, 
			ch.nro_cheque AS numero_cheque, 
			ch.monto_cheque AS monto_cheque, 
			UPPER(ch.beneficiario_cheque) AS beneficiario_cheque, 
			t.nombre_sol AS tipo_solicitud
		FROM sai_pago_cheque pch
		INNER JOIN sai_sol_pago s ON (s.sopg_id = pch.docg_id) 
		INNER JOIN sai_cheque ch ON (pch.docg_id = ch.docg_id)
		INNER JOIN sai_ctabanco ctb ON (pch.nro_cuenta = ctb.ctab_numero)		 
		INNER JOIN sai_banco b ON (ctb.banc_id = b.banc_id) 
		INNER JOIN sai_dependenci d ON (pch.depe_id = d.depe_id) 
		INNER JOIN sai_doc_genera dg ON (dg.docg_id = pch.pgch_id)  
		INNER JOIN sai_empleado em ON (dg.usua_login = em.empl_cedula)  
		INNER JOIN sai_tipo_solicitud t ON (s.sopg_tp_solicitud = t.id_sol)  
		WHERE pch.pgch_id='".$codigo."'";

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

//Buscar las retenciones
/*$sql= "select * from sai_buscar_sopg_reten ('".trim($sopg)."') as resultado ";
$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
$sql.= "por_imp float4,servicio varchar,monto_base float8)";*/

$sql= "SELECT sopg_id,
			impu_id,
			sopg_ret_monto,
			sopg_por_rete,
			sopg_por_imp,
			sopg_servicio,
			sopg_monto_base
		FROM sai_sol_pago_retencion
		WHERE lower(trim(sopg_id)) = '".trim($sopg)."'";
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
		$rete_monto_doc[$ii]=trim($row_rete_doc['sopg_rete_monto']);
		$por_rete_doc[$ii]=trim($row_rete_doc['sopg_por_rete']);
		$por_imp_doc[$ii]=trim($row_rete_doc['sopg_por_imp']);
		$servicio_doc[$ii]=trim($row_rete_doc['sopg_servicio']);
		$tt_retencion=$rete_monto_doc[$ii]+$tt_retencion;
	   $ii++; 
	}
	$tt_neto=$sopg_monto-$tt_retencion;
					 
	//Monto total del cheque descontando las retenciones es
	$monto_cheque_antes_ret = $monto_cheque;
	//$monto_cheque = $monto_cheque - $tt_retencion;					 
} 

/*Consulto las OTRAS retenciones del documento */
/*$sql="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$sopg."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$a_oPresupuesto."'";*/
$sql="SELECT sopg_partida_rete,
			sopg_ret_monto,
			part_nombre
		FROM sai_sol_pago_otra_retencion t1
		INNER JOIN sai_partida t2 ON (t1.sopg_partida_rete = t2.part_id)
		WHERE sopg_id='".$sopg."'
			AND t2.pres_anno='".$a_oPresupuesto."'";
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
}

//Buscar las Imputaciones
$total_imputacion=0;
$monto_temp_vi=0;
	 
/*$sql= " Select * from sai_buscar_sopg_imputacion('".trim($sopg)."') as result ";
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
			$matriz_detalle[$i]='Pagado del documento origen '. $sopg; 
			$monto_temp_vi= $monto_temp_vi + $matriz_monto[$i];
			$i++;
	}

	if ($matriz_imputacion[0]==1) {//Por Proyecto
		$query ="SELECT * FROM sai_buscar_centro_gestor_costo_proy('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') AS result (centro_gestor varchar, centro_costo varchar)";
	}else {//Por Accion Centralizada
		$query ="SELECT * FROM  sai_buscar_centro_gestor_costo_acc('".trim($matriz_acc_pp[0])."','".trim($matriz_acc_esp[0])."') AS result (centro_gestor varchar, centro_costo varchar)";
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
$error=0;
if ($request_id_objeto_sig == 99) { //Está aprobando el adjunto. Fin de las aprobaciones
	$sql = "SELECT
				paga_docu_id 
			FROM sai_pagado
			WHERE paga_docu_id='".$codigo."'";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)) {					
			$pgch_resultado = trim($row['paga_docu_id']);
			$error=1;
			$mensaje_error_pgch = "Ese pago con cheque ya se proces&oacute;.<br>";
			echo $mensaje_error_pgch;
	}	
	$sql = "SELECT
				comp_id 
			FROM sai_comp_diario 
			WHERE comp_doc_id='".$codigo."'";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)) {					
			$pgch_resultado = trim($row['comp_id']);
			$error=1;
			$mensaje_error_pgch = "Esa solicitud de pago ya posee una contabilidad asociada: ". $pgch_resultado;
			echo $mensaje_error_pgch;
	}
	if ($error < 1) {		
		/*$sql_caus = "select cpat_id,cpat_nombre,rcomp_haber FROM sai_comp_diario c, sai_reng_comp r
		where c.comp_id=r.comp_id and c.comp_id like 'coda%' and c.comp_doc_id='".$sopg."' and c.esta_id<>15 and c.comp_comen not like 'A-%' 
		and oid in (
		select max(oid)
		from sai_comp_diario c, sai_reng_comp r
		where c.esta_id<>15 and c.comp_id=r.comp_id and c.comp_id like 'coda%' and c.comp_doc_id='".$sopg."')";*/
		
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
		$resultado_set = pg_query($conexion ,$sql_caus) or die("Error al 
		consultar ultimo causado activo");

		if ($resultado_set) {
			$row = pg_fetch_array($resultado_set,0);
			$cpat_id = trim($row["cpat_id"]);
			$cpat_nombre=trim($row["cpat_nombre"]);
			$cpat_monto=trim($row["rcomp_haber"]);
		}
		$pgch_monto_float = $cheque_monto2 ;

	$sql = "SELECT
				p.part_id, 
				UPPER(p.part_nombre), 
				c.cpat_id, 
				UPPER(cpat.cpat_nombre), 
				cb.ctab_numero, 
				cb.ctab_descripcion, 
				b.banc_nombre
			FROM sai_partida p 
			INNER JOIN	sai_convertidor c ON (p.part_id = c.part_id)
			INNER JOIN sai_ctabanco cb ON (c.cpat_id = cb.cpat_id)
			INNER JOIN sai_cue_pat cpat ON (c.cpat_id = cpat.cpat_id)
			INNER JOIN sai_sol_pago_imputa sp ON (sp.sopg_sub_espe = p.part_id)
			INNER JOIN sai_banco b ON (b.banc_id=cb.banc_id)
			WHERE p.pres_anno=".$a_oPresupuesto." 
				AND sp.sopg_id='".$sopg."'";
	$resultado=pg_query($conexion,$sql);
	$numero_resultado=pg_num_rows($resultado);
	if ($numero_resultado>0) {	
		$row = pg_fetch_array($resultado);
		$nombre_banco_receptor=$vector[0];
		$cuenta_banco_receptor=$row['ctab_numero'];
	}		
		
	$sql = "SELECT * FROM generar_pagado ('".$cpat_id."', '". $cpat_nombre."', '". $cpat_monto."','".$codigo."', '".$id_dependencia."','".$numero_cheque."', '".$numero_cuenta."','".$sopg."',".$monto_cheque_float.",'".$cuenta_banco_receptor."',".$anno_id_doc_imputacion .",'".$arreglo_uel ."','".$arreglo_tipo_impu."','".$arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_sub_esp."','". $arreglo_monto ."','". $arreglo_apde_abono ."','".$arreglo_apde_descr."') as pagado";
	$resultado = pg_query($conexion,$sql) or die("Error al generar el pagado");
	
	$sql = "UPDATE 
				sai_pago_cheque 
			SET esta_id = 13 
			WHERE pgch_id='".$codigo."'";
	$resultado = pg_query($conexion,$sql) or die("Error al cambiar estatus del pago con cheque");
	
	}
}		

/*CUANDO DEVUELVE EL PGCH*/
if ($request_id_opcion==5)  {  
   	$memo_contenido=$_POST['contenido_memo'];	
   	$query_memo=utf8_decode("SELECT * FROM sai_insert_memo('".$_SESSION['login']."','".$_SESSION['user_depe_id']."','".$memo_contenido."', 'Devolución del Pago con Cheque','0', '0','0','',0, 0, '0', '','".$codigo."') AS memo_id");
	
   	$resultado_set = pg_exec($conexion ,$query_memo);
	$valido=$resultado_set;
	if($resultado_set) 	{
		$row = pg_fetch_array($resultado_set,0); 
		if ($row[0] <> null) {
		  $memo_id=$row[0];
		}
	}
	$sql_edo = "SELECT * FROM sai_modificar_estado_doc_genera('".trim($codigo)."','7') AS RESULTADO";
	$result_insert_comp_auto=pg_query($conexion,$sql_edo) or die("Error al cambiar el estado del documento");
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
function monto_en_letras(monto_1) {
	var monto= number_format(monto_1,2,'.','');	
  	ver_monto_letra(monto ,'txt_monto_letras','');
}
</script>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onload="monto_en_letras(<? echo str_replace(",",".",str_replace(".","",$monto_cheque));?>)">
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
		<td class="normal"><?php echo $nombre_banco."-".$numero_cuenta;?></td>
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
			<input name="<?php echo "txt_servicio".$ii?>" id="<?php echo "txt_servicio".$ii?>" type="text"  class="normal" value="<?php echo $servicio_doc[$ii];?>" size="60" maxlength="60" readonly="readonly"/> </td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_impuesto".$ii?>" id= "<?php echo "txt_impuesto".$ii?>" value="<?php echo $id_impuesto_doc[$ii];?>" class="normal" size="10" readonly="readonly"/></td>
			<td width="10%" class="normal" align="center">			
			<input type="text" name="<?php echo "txt_iva_".$ii?>" id= "<?php echo "txt_iva_".$ii?>" value="<?php if ($por_imp_doc[$ii]==0) {echo "s/n";} else {echo $por_imp_doc[$ii];}?>" class="normal" size="6" readonly="readonly"/></td>
			<td width="12%" class="normal" align="center">
			<input type="text" name="<?php echo "txt_porcentaje_".$ii?>" id= "<?php echo "txt_porcentaje_".$ii?>" value="<?php echo $por_rete_doc[$ii]; ?>" class="normal" size="6" readonly="readonly"/></td>
			<td width="15%" align="center" class="normal">
			<input name="<?php echo "txt_monto_impu".$ii?>" type="text"  class="normal" value="<?php echo  $rete_monto_doc[$ii]; ?>"   size="25" maxlength="25" readonly="readonly" align="right" id="<?php echo "txt_monto_impu".$jj?>"/>			</td>
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
			<tr class="td_gray">
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
		<td height="66" colspan="3" class="normal" align="center">
		<br><br>Este pago con cheque fue revisado el d&iacute;a: 
		<?php   echo date ("d/m/Y ") ." a las ". date ("h:i:s a ");?><br><br>
		<a href="javascript:window.print()" class="normal"><img src="imagenes/boton_imprimir.gif" width="23" height="20" border="0"></a>
		</tr>
	</table>
	</body>
</html>
<?//php pg_close($conexion);?>