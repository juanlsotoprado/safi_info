<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();

$ano = $_SESSION['an_o_presupuesto'];
$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);
$observacionesAnulacion = trim($_POST['observacionesAnulacion']);
$motivo = $_REQUEST["motivo"]." ".$observacionesAnulacion;
$idPgch = $_REQUEST["pg"]; //Id del pgch
$otroCheque = $_REQUEST["otro"];
$sopg =  trim($_POST['idSopg']); 
$idCheque =  trim($_POST['idCheque']);
$numeroCheque =  trim($_POST['numeroCheque']); 
$numeroCuenta = trim($_POST['numeroCuenta']);
$observaciones = trim($_POST['observaciones']);
$asunto = trim($_POST['asunto']);
$banco = trim($_POST['banco']);
	$beneficiarioCheque = trim($_POST['beneficiarioCheque']);
	$beneficiarioId = trim($_POST['beneficiarioId']);
	$montoCheque = trim($_POST['montoCheque']);

/*Inserción del memo*/
if (isset($_REQUEST["pg"])) {
	$sql="select * from sai_insert_memo('".trim($_SESSION['login'])."', '".$user_depe_id."', '".$motivo."', 'Anulacion del Pago - ".$idPgch."','0', '0','0','',0, 0, '0', '', '".$idPgch."')";
	$resultado=pg_query($conexion,$sql);
}

/*Validar si el pago esta conciliado*/
$conciliado="";
$sql="select docg_id from sai_ctabanco_saldo where docg_id='".$idPgch."'";
$resultado=pg_query($conexion,$sql);
if ($row=pg_fetch_array($resultado)) {
	$conciliado = trim($row['docg_id']);
}
/*Si el pago no esta conciliado*/
if (strlen($conciliado)<1 && $otroCheque == 1) { //Indica si genera o no otro cheque con los mismos datos
	$mensaje = "";
	$idCheque = trim($_POST['idCheque']);
		
	

	/*Verificar que la cuenta tiene chequera activa*/
	$sql="select * from sai_verificar_cuenta_chequera_activa('".$numeroCuenta."') as nro_chequera ";
	$resultado=pg_query($conexion,$sql);
	if ($row=pg_fetch_array($resultado)) {
		$numeroChequera = trim($row['nro_chequera']);
	}
	/*Si no tiene chequera*/
	if ($numeroChequera == "0") {
		$error = 1;
		$mensaje= "La cuenta ".$numeroCuenta." no tiene chequeras activas";
	}
	else {
		$sql="select * from sai_buscar_cheque_activo('".$numeroChequera."') resultado_set (id_cheque varchar, nro_cheque varchar) ";
		$resultado=pg_query($conexion,$sql);
		if ($row=pg_fetch_array($resultado)) {
			$idNumeroChequeActivo = trim($row['id_cheque']);
			$numeroChequeActivo = trim($row['nro_cheque']);
		}
	}
	/*Fin búsqueda nuevo numero de cheque*/
	if (strlen($numeroChequeActivo<1)) {
		$error = 1;
		$mensaje= "No hay cheques activos. El pago no puede ser anulado parcialmente";
	}
	else { /*Actualización tabla sai_pago_cheque. Asignándole al pago el nuevo nro de cheque*/
		$sql = " SELECT * FROM reemplazar_cheque('".$idNumeroChequeActivo."','".$montoCheque."','".$beneficiarioCheque."','".$beneficiarioId."','".$sopg."', '".$motivo."', '".$idPgch."','".$numeroChequeActivo."', '".$idCheque."') as resultado ";
		$resultado=pg_query($conexion,$sql);
		$mensaje = "El cheque ".$cheque_numero." ha sido anulado y reemplazado por el cheque nro. ".$numeroChequeActivo."<br> El documento ".$sopg." se encuentra nuevamente en el buz&oacute;n de cheques preemitidos";
	}
}
else if (strlen($conciliado)<1 && $otroCheque == 0) { //No genera otro cheque. El pago no sera procesado
		/*Caso causado*/
	$sql_caus="select max(substr(trim(c.comp_id),6,length(trim(c.comp_id))-5)) as maximo
	from sai_comp_diario c, sai_doc_genera g 
	where c.comp_doc_id=g.docg_id and
	c.comp_doc_id='".$sopg."' and comp_comen like 'C-%'
	and g.esta_id<>15";
	$resultado_caus=pg_query($conexion, $sql_caus);
	if ($row_caus=pg_fetch_array($resultado_caus)) $coda_causado="coda-".$row_caus["maximo"];

	$sql_fecha = "select substring(comp_fec,1,4) as ano from sai_comp_diario where comp_id='".$coda_causado."'";
	$resultado_caus=pg_query($conexion, $sql_fecha);
	if ($row_caus=pg_fetch_array($resultado_caus)) $ano_causado=$row_caus["ano"];

	/*Caso transitoria. (Hasta julio 2009)*/
	$sql_tran="select c.comp_id as comp_id
	from sai_comp_diario c, sai_doc_genera g 
	where c.comp_doc_id=g.docg_id and
	c.comp_doc_id='".$idPgch."' and comp_comen like 'T-%'
	and g.esta_id<>15";
	$resultado_tran=pg_query($conexion, $sql_tran);
	if ($row_tran=pg_fetch_array($resultado_tran)) $coda_transitoria=$row_tran["comp_id"];

	/*Caso pagado*/
	$sql_pag="select c.comp_id as comp_id
	from sai_comp_diario c, sai_doc_genera g 
	where c.comp_doc_id=g.docg_id and
	c.comp_doc_id='".$idPgch."' and comp_comen like 'P-%'
	and g.esta_id<>15";
	$resultado_pag=pg_query($conexion, $sql_pag);
	if ($row_pag=pg_fetch_array($resultado_pag)) $coda_pagado=$row_pag["comp_id"];

		/*Se debe verificar el pago no tenga retenciones. De lo contrario, no se podrá anular en una primera fase*/
		/*$sql_gasto = "select src.comp_id, src.cpat_id as cpat_id, src.rcomp_debe as monto_debe, src.rcomp_haber as monto_haber, src.fecha_emis, to_char(src.fecha_emis, 'MM') as mes, to_char(src.fecha_emis, 'YYYY') as ano from sai_reng_comp src, sai_comp_diario sc where src.comp_id = sc.comp_id and sc.comp_doc_id = '".$pgch_docg_id."'";
		 $resultado_set = pg_exec($conexion ,$sql_gasto) or die("Error al mostrar cuentas para la verificacion de retenciones");
		 $valido = 1;
		 while($row=pg_fetch_array($resultado_set)) {
		 if (trim($row['cpat_id'])=='2.1.1.03.04.01.03' || trim($row['cpat_id'])=='2.1.1.03.04.01.01' || trim($row['cpat_id'])=='2.1.1.03.04.01.02') $valido = 0; //tiene retenciones
		 $mes_sopg=$row['mes'];
		 $ano_sopg=$row['ano'];
		 }
		 if ($mes_sopg==date("m") && $ano_sopg==date("Y")) //La anulación se está realizando en el mismo mes en que se registraron las retenciones.-
		$valido = 1;
		if ($valido>0) {/*No existen retenciones o la anulación se está realizando en el mismo mes que el del registro de la solicitud*/ 


	/*Reverso obligatorio del pagado*/
	$sql="select src.comp_id, src.cpat_id, src.rcomp_debe, src.rcomp_haber, src.fecha_emis, sp.docg_id as docg_id
		from sai_reng_comp src, sai_comp_diario sc, sai_pago_cheque sp, sai_cheque c
		where src.comp_id = sc.comp_id and sc.comp_doc_id = sp.pgch_id and sp.docg_id = c.docg_id
		and sc.comp_id='".$coda_pagado."' and sc.comp_comen like 'P%' order by src.fecha_emis desc";

	$resultado=pg_query($conexion,$sql);
	$i=1;
	$cta1_pagado = "";
	$cta2_pagado = "";
	$mto1_pagado = 0;
	$mto2_pagado = 0;
	$tipo1_pagado = 0;
	while ($row=pg_fetch_array($resultado)) {
		if ($i<2) {
			$cta1_pagado = $row["cpat_id"];
			$mto1_pagado = $row["rcomp_debe"] + $row["rcomp_haber"];
			if ($row["rcomp_debe"]>0) $tipo1_pagado = 1;
			else $tipo1_pagado = 0;
		}
		if ($i<3 && $i>1) {
			$cta2_pagado = $row["cpat_id"];
			$mto2_pagado = $row["rcomp_debe"] + $row["rcomp_haber"];
		}
		$i++;
	}
	
	$mto1_transitoria = 0;

	/*Reverso opcional transitoria (Casos antes de Agosto de 2009)*/
	if (strcmp($coda_transitoria, '')!=0 || $$coda_transitoria!="" || $coda_transitoria!=null ) {
		$sql="select src.comp_id, src.cpat_id, src.rcomp_debe, src.rcomp_haber, src.fecha_emis, sp.docg_id as docg_id
		from sai_reng_comp src, sai_comp_diario sc, sai_pago_cheque sp, sai_cheque c
		where src.comp_id = sc.comp_id and sc.comp_doc_id = sp.pgch_id and sp.docg_id = c.docg_id
		and sc.comp_id='".$coda_transitoria."' and sc.comp_comen like 'T%' order by src.fecha_emis desc";
		/*reverso transitoria*/

		$resultado=pg_query($conexion,$sql);
		$i=1;
		$cta1_transitoria = "";
		$cta2_transitoria = "";
		$mto2_transitoria = 0;
		$tipo1_transitoria = 0;
		while ($row=pg_fetch_array($resultado)) {
			if ($i<2) {
				$cta1_transitoria = $row["cpat_id"];
				$mto1_transitoria = $row["rcomp_debe"] + $row["rcomp_haber"];
				if ($row["rcomp_debe"]>0) $tipo1_transitoria = 1;
				else $tipo1_transitoria = 0;
			}
			if ($i<3 && $i>1) {
				$cta2_transitoria = $row["cpat_id"];
				$mto2_transitoria = $row["rcomp_debe"] + $row["rcomp_haber"];
			}
			$i++;
		}

	} /*Fin reverso opcional transitoria*/

	/*Anulación gasto $coda_causado*/
	if ($ano_causado==$_SESSION['an_o_presupuesto']-1) {
		$cuenta_resultado="3.2.5.02.01.01.01";
		$nombre_resultado="Resultados del Ejercicio";
	}
	else if ($ano_causado!=$_SESSION['an_o_presupuesto']-1) {
		$cuenta_resultado="3.2.5.01.01.01.01";
		$nombre_resultado="Resultados Acumulados";
	}
	$sql_gasto = "select src.comp_id, src.cpat_id as cpat_id, src.rcomp_debe as monto_debe, src.rcomp_haber as monto_haber, src.fecha_emis,part_id,pr_ac,a_esp,pr_ac_tipo from sai_reng_comp src, sai_comp_diario sc where src.comp_id = sc.comp_id and sc.comp_id = '".$coda_causado."'";
	$resultado_set = pg_exec($conexion ,$sql_gasto) or die("Error al mostrar");
	$i=0;
	while($row=pg_fetch_array($resultado_set)) {
		/*if (trim($row['cpat_id'])<>'2.1.1.03.04.01.03' && trim($row['cpat_id'])<>'2.1.1.03.04.01.01' && trim($row['cpat_id'])<>'2.1.1.03.04.01.02') {*/
		if ($ano_causado!=$_SESSION['an_o_presupuesto'] && substr(trim($row['cpat_id']),0,1)==6) $cta_ano= $cuenta_resultado;
		else  $cta_ano= trim($row['cpat_id']);

		$matriz_ctas[$i]=$cta_ano;
		if ($ano_causado!=$_SESSION['an_o_presupuesto'] && substr(trim($row['cpat_id']),0,1)==6) { 
			$matriz_partidas[$i] = null;
			$matriz_pr_ac[$i] = null;
			$matriz_a_esp[$i] = null;
			$matriz_pr_ac_tipo[$i] = null;			
		}
		else {
			$matriz_partidas[$i]=trim($row['part_id']);
			$matriz_pr_ac[$i] = trim($row['pr_ac']);
			$matriz_a_esp[$i] = trim($row['a_esp']);
			$matriz_pr_ac_tipo[$i] = trim($row['pr_ac_tipo']);
		}

		$matriz_monto[$i]=$row['monto_debe']+$row['monto_haber'];
		if ($row["monto_debe"]>0) $matriz_tipo[$i]=1; /*1 va por el debe*/
			else $matriz_tipo[$i]=0;
			$i++;
	}

	require_once("../../includes/arreglos_pg.php");
	$arreglo_ctas=convierte_arreglo($matriz_ctas);
	$arreglo_partidas=convierte_arreglo($matriz_partidas);
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_tipo=convierte_arreglo($matriz_tipo);
	$arreglo_pr_ac=convierte_arreglo($matriz_pr_ac);	
	$arreglo_a_esp=convierte_arreglo($matriz_a_esp);
	$arreglo_pr_ac_tipo=convierte_arreglo($matriz_pr_ac_tipo);		
	/*Fin anulación causado*/

	$sql = "SELECT * FROM anulacion_total ('".$sopg."', '".$idPgch."', '".$user_depe_id."', '".$cta1_pagado."', ".$mto1_pagado.", '".$tipo1_pagado."', '".$cta2_pagado."', '".$numeroCheque."', '".$coda_transitoria."','".$cta1_transitoria."', ".$mto1_transitoria.", '".$tipo1_transitoria."', '".$cta2_transitoria."', '".$arreglo_ctas."', '".$arreglo_monto."', '".$arreglo_tipo."' ,'".$arreglo_partidas."', '".$idCheque."', '".$motivo."', '".$arreglo_pr_ac."', '".$arreglo_a_esp."','".$arreglo_pr_ac_tipo."',".$ano.") AS resultado";
	$resultado = pg_exec($conexion ,$sql) or die("Error al intentar realizar la anulaci\00f3n total");
	
	//yo
	$row = pg_fetch_array($resultado,0);
	if ($row[0] <> null) {
	 //REVERSAR LOS MONTOS DE LAS RETENCIONES DEL ISLR
     $sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$beneficiarioId."'";
     $resultado_query = pg_exec($conexion,$sql_islr);
     if ($row_islr=pg_fetch_array($resultado_query)){
      $monto_actualizado=$row_islr['monto_pagado']-$montoCheque ;
      $sql="UPDATE sai_retenciones_islr SET monto_pagado=".$monto_actualizado." WHERE cedula='".$beneficiarioId."'";
      $result=pg_query($conexion,$sql);
     }
	}
	
	$row = pg_fetch_array($resultado,0);
	if ($row[0] <> null) {
	 //REVERSAR LOS MONTOS DE LAS RETENCIONES DEL ISLR
     $sql_islr="SELECT * FROM sai_retenciones_islr WHERE cedula='".$beneficiarioId."'";
     $resultado_query = pg_exec($conexion,$sql_islr);
     if ($row_islr=pg_fetch_array($resultado_query)){
      $monto_actualizado=$row_islr['monto_pagado']-$montoCheque ;
      $sql="UPDATE sai_retenciones_islr SET monto_pagado=".$monto_actualizado." WHERE cedula='".$beneficiarioId."'";
      $result=pg_query($conexion,$sql);
     }
	}	
	$mensaje = "El cheque ".$cheque_numero." ha sido anulado <br> Los movimientos presupuestarios y contables asociados al documento ".$id_doc_imputacion." igualmente han sido anulados";			

	
/*************************************************************************************************/	
	//LIBERAR DISPONIBILIDAD DEL COMP AL ANULAR TOTALMENTE UN SOPG ELABORADO A PARTIR DEL 2011
	//SIEMPRE QUE EL SOPG ESTE ASOCIADO A UN COMP
	$sql_sopg="select comp_id from sai_sol_pago where sopg_id='".$sopg."'";
	$resultado_comp=pg_exec($conexion,$sql_sopg);
	if ($row_sopg=pg_fetch_array($resultado_comp)){
		$l=strlen($idPgch);
		$ao=substr($idPgch,$l-2,$l);
		$comp_id=$row_sopg['comp_id'];
		if (($comp_id<>'') && ($row_sopg['comp_id']<>'N/A') && ($ao<>'08') && ($ao<>'09') && ($ao<>'10')){
					
			$sql= " Select * from sai_buscar_sopg_imputacion('".trim($sopg)."') as result ";
			$sql.= " (sopg_id varchar, sopg_acc_pp varchar, sopg_acc_esp varchar, depe_id varchar, sopg_sub_espe varchar, sopg_monto float8, tipo bit, sopg_monto_exento float8)";
			$resultado_set= pg_exec($conexion ,$sql);
			$valido=$resultado_set;
			if ($resultado_set) {
					$total_imputacion=pg_num_rows($resultado_set);
					$i=0;
					while($row=pg_fetch_array($resultado_set)) {
						//$matriz_imputacion[$i]="'".trim($row['tipo'])."'";
						$matriz_imputacion[$i]=trim($row['tipo']);
						$matriz_acc_pp[$i]=trim($row['sopg_acc_pp']);
						$matriz_acc_esp[$i]=trim($row['sopg_acc_esp']);
						$matriz_sub_esp[$i]=trim($row['sopg_sub_espe']);
						$matriz_uel[$i]=trim($row['depe_id']);
						$matriz_monto_partida[$i]=$row['sopg_monto']+$row['sopg_monto_exento'];
						$i++;
		   			}
			}
			for($j=0; $j<$total_imputacion; $j++)  {
				$query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$comp_id."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
				$resultado_query = pg_exec($conexion,$query_disponible);
		  		if ($row=pg_fetch_array($resultado_query)){
		  			$disponible=$row['disponible']+$matriz_monto_partida[$j];
		  			$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$comp_id."' and partida='".$matriz_sub_esp[$j]."' and comp_acc_pp='".$matriz_acc_pp[$j]."' and comp_acc_esp='".$matriz_acc_esp[$j]."'";
		  			$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
		  		}
			}
		}
	}//fin liberación de disponibilidad del compromiso
} //fin if ($otro_cheque==0)
else if (strlen($conciliado)>1) {
	$mensaje = "El cheque no puede anularse pues ya fue conciliado";
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>.:SAFI: Anular Cheque</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/func_montletra.js"> </script>
</head>
<body>
<form name="form" method="post" action="">
<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">ANULAR CHEQUE</td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="normalNegrita">
			<?
			$condicion = "";
			if ($error==1) $condicion = "NO ";
			?>
			El cheque <? echo $valor;?><?=$condicion;?> ha
			sido anulado <? echo $concepto;?>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">Documento asociado:</td>
		<td class="normal"><a
			href="javascript:abrir_ventana('../sopg/sopg_detalle.php?codigo=<?php echo trim($sopg);?>')"
			class="link"><?php echo $sopg;?></a>
		</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cheque:</td>
		<td class="normal"><?php echo $numeroCheque;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de cuenta:</td>
		<td class="normal"><?php echo $numeroCuenta;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Banco:</td>
		<td class="normal"><?php echo $banco;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Beneficiario:</td>
		<td class="normal"><?php echo $beneficiarioCheque;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">CI o RIF del Beneficiario:</td>
		<td class="normal"><?php echo $beneficiarioId;?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Monto Bs.:</td>
		<td class="normal"><?php echo $montoCheque; ?></td>
	</tr>
	<tr>
		<td class="normalNegrita">Concepto del Pago:</td>
		<td class="normal"><?php echo $asunto;?></td>
	</tr>
	<tr>
		<td class="normalNegrita" width="18%" >Observaciones del Pago:</td>
		<td class="normal"><?php echo $observaciones;?></td>
	</tr>
	<tr>
		<td class="normalNegrita" width="18%" >Motivo:</td>
		<td class="normal"><?php echo $motivo;?></td>
	</tr>	
</table>
<br></br>
<div align="center" class="normalNegrita_naranja">
	<?php echo $mensaje; ?>
	<br /><br /><br />
	<a href="javascript:window.print()"><img
		src="../../imagenes/boton_imprimir.gif" width="23" height="20"
		border="0" alt="Imprimir Detalle" /></a>
</div>
</form>
</body>
</html>
<?php pg_close($conexion);?>