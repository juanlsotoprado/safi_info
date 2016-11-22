<?php   
ob_start();
session_start();
require_once("includes/conexion.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
}
ob_end_flush();

$error=0;
$valido=0;
$forma_pago = trim($_POST['cmb_forma_pago']);
$vector=explode ("-" , $_POST['banco_emisor']);
$nombre_banco_emisor=$vector[0];
$cuenta_banco_emisor=$vector[1];
$vector=explode ("**" , $_POST['hid_beneficiario']);
$id_beneficiario = $vector[0];
$nombre_beneficiario = $vector[1];
$tipo_solicitud = trim($_POST['hid_tipo_solicitud']);
$numero_reserva = trim($_POST['numero_reserva']);
$monto = trim($_POST['hid_monto']);
$monto_cheque_antes_ret = trim($_POST['hid_monto_cheque_antes_ret']);
$monto_float = str_replace(",","",$monto);
$monto_letras = trim($_POST['txt_monto_letras']);
$concepto_pago = trim($_POST['txt_concepto']);	
$observacion = trim($_POST['txt_observa']);
$depe_id=trim($_SESSION['user_depe_id']);
$prioridad=trim($_POST['slc_prioridad']); 
$sopg = trim($_POST['hid_codigo_doc']); 
$anno_sopg= trim($_POST['hid_press_anno']); //Ano del presupuesto del doc principal
$referencia_bancaria = trim($_POST['txt_referencia']);
$a_oPresupuesto = $_SESSION['an_o_presupuesto'];

/*Parche para que se puedan emitir pgch del año anterior, comentar para pgch del año actual*/
//$a_oPresupuesto = '2015';

/*Banco Receptor*/

$sql="select p.part_id, upper(p.part_nombre), c.cpat_id, upper(cpat.cpat_nombre), cb.ctab_numero, cb.ctab_descripcion, b.banc_nombre
from sai_partida p, sai_convertidor c, sai_ctabanco cb, sai_cue_pat cpat, sai_sol_pago_imputa sp, sai_banco b
where p.part_id=c.part_id and p.pres_anno=".$a_oPresupuesto." and c.cpat_id=cb.cpat_id and c.cpat_id=cpat.cpat_id
and sp.sopg_id='".$sopg."' and sp.sopg_sub_espe =p.part_id and b.banc_id=cb.banc_id";
$resultado=pg_query($conexion,$sql);
$numero_resultado=pg_num_rows($resultado);
if ($numero_resultado>0) {	
	$row = pg_fetch_array($resultado);
	$nombre_banco_receptor=$vector[0];
	$cuenta_banco_receptor=$row['ctab_numero'];
}

//VERIFICAR LA DISPONIBILIDAD DE LA CUENTA	
$disponibilidad = 0;
$sql="SELECT monto_haber AS dispone FROM sai_ctabanco_saldo WHERE ctab_numero='".$cuenta_banco_emisor."' and docg_id='sb-".$a_oPresupuesto."'";
$resultado=pg_query($conexion,$sql);
$numero_resultado=pg_num_rows($resultado);
if ($numero_resultado>0) {	
	$row = pg_fetch_array($resultado);		
	$monto_cuenta = $row['dispone'];
}
$sql="SELECT (SUM(monto_haber) - SUM(monto_debe)) AS dispone FROM sai_ctabanco_saldo WHERE ctab_numero='".$cuenta_banco_emisor."'";
$resultado=pg_query($conexion,$sql);
$numero_resultado=pg_num_rows($resultado);
if ($numero_resultado>0) {			
	$row = pg_fetch_array($resultado);	
	$monto_cuenta += $row['dispone'];
	if ($monto_cheque <= $monto_cuenta)	{ //Si el monto a pagar es < que el disponible
		$disponibilidad = 1; 
	}
}
if ($disponibilidad<1) {	
	$error = 1; 
	$mensaje_error_pgch = "No hay disponibilidad en la cuenta ".$cuenta_banco_emisor;
}
else {
	/***********************************************************************
	 *  Pago con cheque (pgch)                                             *
	 **********************************************************************/
	if ($forma_pago==1) { //Forma de pago cheque
		//Buscar el siguiente cheque autorizado
		$id_nro_cheque = "";
		$nro_cheque = "";
		$nro_chequera = "";
				
		//Verificar que la cuenta tiene chequera activa
		$sql="select * from sai_verificar_cuenta_chequera_activa('$cuenta_banco_emisor') as nro_chequera ";
		$nro_chequera="0";
		$resultado=pg_query($conexion,$sql);
		$numero_resultado=pg_num_rows($resultado);
		if ($numero_resultado>0) {	
			$row = pg_fetch_array($resultado);			
			$nro_chequera = trim($row['nro_chequera']);	
		}
		
		//Si no tiene chequera
		if ($nro_chequera == "0") {	
			$error = 1; 
			$mensaje_error_pgch = "La cuenta ".$cuenta_banco_emisor." no tiene chequeras activas";
		}
		else {
			$sql="select * from sai_buscar_cheque_activo('".$nro_chequera."') resultado_set (id_cheque varchar, nro_cheque varchar) ";
			$resultado=pg_query($conexion,$sql);
			$nro_cheque = 0;
			$numero_resultado=pg_num_rows($resultado);
			if ($numero_resultado>0) {
				$row = pg_fetch_array($resultado);					
				$id_nro_cheque = trim($row['id_cheque']);
				$nro_cheque = trim($row['nro_cheque']);
			}
			if ($nro_cheque == "0") {	
				$error = 1; 
				$mensaje_error_pgch = "La cuenta ".$cuenta_banco_emisor." no tiene cheques activos";
			}
			else {
				$sql = "select pgch_id from sai_pago_cheque where docg_id='".$sopg."' and esta_id<>15 and esta_id<>2";
				$resultado=pg_query($conexion,$sql);
				$numero_resultado=pg_num_rows($resultado);
				if ($numero_resultado>0) {
					$row = pg_fetch_array($resultado);					
					$pgch_resultado = trim($row['pgch_id']);
					$error=1;
					$mensaje_error_pgch = "Esa solicitud de pago ya posee el siguiente pago con cheque asociado: ". $pgch_resultado;
				}
				$sql = "select trans_id from sai_pago_transferencia where docg_id='".$sopg."' and esta_id<>15";
				$resultado=pg_query($conexion,$sql);
				$numero_resultado=pg_num_rows($resultado);
				if ($numero_resultado>0) {	
					$row = pg_fetch_array($resultado);						
					$pgch_resultado = trim($row['trans_id']);
					$error=1;
					$mensaje_error_pgch = "Esa solicitud de pago ya posee el siguiente pago con transferencia asociado: ". $pgch_resultado;
				}				
				if ($error<1) {								 
					$sql = "select * from sai_insert_pago_cheque('".$depe_id."', '".$cuenta_banco_emisor."', '".$id_nro_cheque."', '".$sopg."', '".$concepto_pago."',".$anno_sopg.",'".$id_beneficiario."','".$observacion."') ";
					$resultado_insert = pg_exec($conexion ,$sql);
					if($resultado_insert) 	{
						$rowa = pg_fetch_array($resultado_insert,0); 
						if ($rowa[0] <> null) {
							$codigo=trim($rowa[0]);
							//Emitir cheque, guardar datos en tabla sai_cheque
							$sql_ch = " SELECT * FROM sai_emitir_cheque('$id_nro_cheque',$monto_float,'$nombre_beneficiario','$id_beneficiario','$sopg', ' ') as resultado ";
							$resultado_ch = pg_query($conexion,$sql_ch) or die("Error al emitir el cheque");
							$numero_resultado=pg_num_rows($resultado_ch);
							if ($numero_resultado>0) {	
								$row = pg_fetch_array($resultado_ch);	
								$inserto_cheque = $row_ch["resultado"];	
								$valido=1;	
				
								$mensaje_cheque = ""; // Verificar si es el ultimo cheque de la chequera
								$sql="select * from sai_ultimo_cheque('$nro_chequera', '$id_nro_cheque') as es_ultimo  ";
								$resultado=pg_query($conexion,$sql);
								$numero_resultado=pg_num_rows($resultado);
								if ($numero_resultado>0) {
									$row = pg_fetch_array($resultado);										
									$es_ultimo_cheque = $row['es_ultimo']; //ultimo y se activo la prox chequera
									if ($es_ultimo_cheque == 1) { 					
										$mensaje_cheque = utf8_decode(" El cheque $nro_cheque es el último de la chequera. Se activó la siguiente chequera. ");
										echo $mensaje_cheque;
									}						
									else {	//Es el ultimo y no hay mas chequeras activas		
										if ($es_ultimo_cheque == 2) { 
											$mensaje_cheque = utf8_decode(" El cheque $nro_cheque es el último de la chequera. NO hay mas chequeras activas. ");
											echo $mensaje_cheque;
										}					
									}									
								}								
							} 
						}
					}
				}
			} 
		} // fin si tiene chequera
	}// fin si forma pago es cheque
	if ($forma_pago==2) { //Forma de pago transferencia en línea
		$sql = "select trans_id from sai_pago_transferencia where docg_id='".$sopg."' and esta_id<>15";
		$resultado=pg_query($conexion,$sql);
		$numero_resultado=pg_num_rows($resultado);
		if ($numero_resultado>0) {
			$row = pg_fetch_array($resultado);							
			$pgch_resultado = trim($row['trans_id']);
			$error=1;
			$mensaje_error_pgch = "Esa solicitud de pago ya posee el siguiente pago con transferencia asociado: ". $pgch_resultado;
		}
		$sql = "select pgch_id from sai_pago_cheque where docg_id='".$sopg."' and esta_id<>15 and esta_id<>2";
		$resultado=pg_query($conexion,$sql);
		$numero_resultado=pg_num_rows($resultado);
		if ($numero_resultado>0) {	
			$row = pg_fetch_array($resultado);						
			$pgch_resultado = trim($row['pgch_id']);
			$error=1;
			$mensaje_error_pgch = "Esa solicitud de pago ya posee el siguiente pago con cheque asociado: ". $pgch_resultado;
		}		
		
		if ($error<1) {							
			$sql = "select * from sai_insert_pago_transferencia('".$depe_id."', '".$cuenta_banco_emisor."', '".$cuenta_banco_receptor."', '".$referencia_bancaria."', '".$sopg."', '".$concepto_pago."',".$anno_sopg.",'".$id_beneficiario."','".$nombre_beneficiario."','".$observacion."', ".$monto_float.") ";
			$resultado_insert = pg_exec($conexion ,$sql);
			if($resultado_insert) 	{
				$rowa = pg_fetch_array($resultado_insert,0); 
				if ($rowa[0] <> null) $codigo=trim($rowa[0]);
				$valido=1;
			}
		}
	}	
    if ($valido ==1) {
    	$cod_doc=$codigo;
		$estado_doc=10; 
		//	Se agrega el registro en la tabla sai_doc_genera
		if ($forma_pago==2) {$request_id_hijo=260;}
		$sql = " UPDATE sai_doc_genera set esta_id=13 where docg_id ='".$sopg."'";
		$resultado_set= pg_exec($conexion ,$sql) or die("Error al modificar el documento");
		
	
		$sql = " SELECT * FROM sai_insert_doc_generado('".$codigo."','".$request_id_objeto_sig."','$request_id_hijo','$user_login','$user_perfil_id',$estado_doc,1,'".$grupo_particular."','".$numero_reserva."') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al insertar el documento");
		if ($row = pg_fetch_array($resultado)) {
			$inserto_doc = $row["resultado"];
			include("includes/respaldos_e1.php");
			$codigo_doc_pendiente = $codigo_doc_principal;
			if ($forma_pago==2) $mensaje_siguiente_inst = "Transferencia asociada al ".$sopg.", Beneficiario: ".$nombre_beneficiario.", por un monto de ".$_POST['hid_monto']."\t";
			else $mensaje_siguiente_inst = " El documento ha siso procesado exitosamente y está en espera de aprobaci&oacute;n";
		}

		//Mostrar las retenciones
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
  		
		require_once("includes/arreglos_pg.php");
		$arreglo_cod_retencion=convierte_arreglo($id_impuesto_doc);
		$arreglo_monto_retenciones=convierte_arreglo($rete_monto_doc);
		}

		/*Consulto las OTRAS retenciones del documento */
 		$sql_be="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$sopg."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$a_oPresupuesto."'"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	
		if ($resultado_set_most_be) {
			$elem_otras_rete=pg_num_rows($resultado_set_most_be);
			$nombre_part=array($elem_otras_rete);
			$id_partida=array($elem_otras_rete);
			$monto_rete=array($elem_otras_rete);

		$ii=0;
		$total_otras_rete=0;
		while($rowbe=pg_fetch_array($resultado_set_most_be))  {
			$nombre_part[$ii]=$rowbe['part_nombre'];
			$id_partida[$ii]=$rowbe['sopg_partida_rete'];
			$monto_rete[$ii]=$rowbe['sopg_ret_monto'];
			$total_otras_rete=$total_otras_rete+$monto_rete[$ii];
			$ii++;
		}
   		require_once("includes/arreglos_pg.php");
		$arreglo_monto_otras_retenciones=convierte_arreglo($monto_rete);
	}


	//Buscar las Imputaciones
	$total_imputacion=0;
	$monto_temp_vi=0;
	if ($valido <>"") {
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
				$matriz_abono[$i]=1; 
				$matriz_detalle[$i]=''; 
				$i++;
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
	} // fin valido
}	// fin disponibilidad
		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Procesar Pago</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script language="javascript" src="js/func_montletra.js"></script>
<script language="JavaScript" src="js/funciones.js"> </script>
<link rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
</head>
<body>
<?php 
if($valido) {
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
		<td colspan="2" class="normalNegroNegrita">DETALLES PARA LA SOLICITUD DE PAGO NRO.<a href="javascript:abrir_ventana('documentos/sopg/sopg_detalle.php?codigo=<?php echo trim($sopg);?>')"><?php echo $sopg;?></a></td>
	</tr>
	<tr>
	  <td class="normalNegrita">Tipo de solicitud:</td>
	  <td class="normal"><?php echo $tipo_solicitud;?>
	  </td>
	</tr>	
	<tr>
		  <td class="normalNegrita">Beneficiario:</td>
		  <td class="normal"><?php echo $nombre_beneficiario;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto sin retenci&oacute;n Bs.:</td>
		<td class="normal"><?php echo $monto_cheque_antes_ret; ?>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto total a pagar Bs.:</td>
		<td class="normal"><?php echo $monto; ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto en letras:</td>
		<td class="normal"><?php echo $monto_letras; ?></td>
	</tr>		
	<tr>
		<td class="normalNegrita">Forma pago:</td>
		<td class="normal"> <?php if ($forma_pago==1) echo "Cheque"; else echo "Transferencia";?></td>
	</tr>
	<tr>
  		<td class="normalNegrita">Nro. Referencia:</td>
		<td class="normal"><?php echo $nro_cheque." ".$referencia_bancaria;?></td>
	</tr>
	<tr>	
		<td class="normalNegrita">Banco emisor:</td>
		<td class="normal"><?php echo $nombre_banco_emisor."-".$cuenta_banco_emisor;?></td>
	</tr>
	<?php if ($id_tipo_solicitud==28) {?>
	<tr>	
		<td class="normalNegrita">Banco receptor:</td>
		<td class="normal"><?php echo $nombre_banco_receptor."-".$cuenta_banco_receptor;?></td>
	</tr>
<?php }?>		
	<tr> 
		<td class="normalNegrita">Concepto del pago:</td>
		<td class="normal"><?php echo $concepto_pago;?></td>
	</tr>
	<tr> 
		<td class="normalNegrita">Observaciones del pago:</td>
		<td class="normal"><?php echo $observacion;?></td>
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
			<tr><td class="normal">
			<input name="<?php echo "part_".$ii?>" type="text"  class="normal" value="<?php echo  $id_partida[$ii].":".$nombre_part[$ii];?>" size="25" maxlength="25" align="right" id="<?php echo "part_".$ii?>" disabled="disabled"/></td>
			<td class="normal">
			<input name="<?php echo "monto_rete".$ii?>" type="text"  class="normal" value="<?php echo  number_format($monto_rete[$ii],2,'.',',');?>" size="25" maxlength="25" align="right" id="<?php echo "monto_rete".$ii?>" disabled="disabled"/></td></tr>
			<?}?>
			<tr >
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
	</table>
<?php
}
else
	{?>
	    <table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr> 
        <td height="21" colspan="4"><div align="left" class="normalNegrita"> 
       PAGO CON CHEQUE/TRANSFERENCIA</div></td>
        </tr>
  		<tr>
    	<td colspan="4" class="normal" align="center"><br/>
		<img src="imagenes/vineta_azul.gif" width="11" height="7"/>
		<?php if ($error==1) echo $mensaje_error_pgch; ?>
		<br/><br/>
		<img src="imagenes/mano_bad.gif" width="31" height="38"/>
		<br/><br/></td>
  		</tr>
	    </table> 
<?php }}?>
</body>
</html>