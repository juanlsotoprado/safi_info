<?
session_start();
require_once("../../includes/conexion.php");
require_once("../../includes/fechas.php");

if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ) {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}

$dependencia = $_POST['opt_depe'];
$imp_acc_pp=trim($_POST['txt_cod_imputa']);  //Cod. del Proyecto o de la Accion Central
$acc_esp=trim($_POST['txt_cod_accion']);   //Cod. de la Accion Especifica
$reserva='';
$pcta=trim($_POST['pcta_id']);
$fecha =cambia_ing($_POST['fecha']);
if ($_POST['fecha_i']<>null)
	$fecha_i =cambia_ing($_POST['fecha_i']);
if ($_POST['fecha_f']<>null)
	$fecha_f =cambia_ing($_POST['fecha_f']);
$beneficiario=explode(":",$_POST['rif_sugerido']);
$rif_sugerido=$beneficiario[0].":".$beneficiario[1];
$j=0;
$monto_solicitado=0;
$pres_anno=$_SESSION['an_o_presupuesto'];
$pres_anno=2014;

if ( isset($_POST['hid_largo']) && trim($_POST['hid_largo'])!='' && is_numeric(trim($_POST['hid_largo'])) ) {
	$largo=trim($_POST['hid_largo']);
	$pasodisponibilidad=((int)trim($_POST['hid_largo']))*-1;
} else {
	$i=0;
	$largo = 0;
	while ( isset($_POST['txt_id_pda'.$i]) ) {
		$largo++;
		$i++;
	}
	$pasodisponibilidad=((int)$largo)*-1;
}
$pasodisponibilidad++;
$total_imputacion=$largo;

$ind=0;
$monto_disponible=array();

for($i=1; $i<$largo; $i++) {
	$matriz_imputacion[$j]=$_POST['txt_tipo_p_ac'.$j];//$tp_imputacion;
	$matriz_acc_pp[$j]=$_POST['txt_id_p_ac'.$j];//$imp_acc_pp;
	$matriz_acc_esp[$j]=$_POST['txt_id_acesp'.$j];//$acc_esp;
	$matriz_sub_esp[$j]=$_POST['txt_id_pda'.$j];
	$matriz_uel[$j]=$_POST['txt_id_depe'.$j];
	$matriz_monto[$j]=str_replace(",","",$_REQUEST['txt_monto_pda'.$j]);
	$monto_solicitado=$monto_solicitado+$_REQUEST['txt_monto_pda'.$j];
	$disponible=0;
	$disponible_monto=0;
	if ($pcta=="0"){//NO TIENE PCTA ASOCIADO, POR LO QUE SE VALIDA DISPONIBILIDAD CONTRA EL POTE
		$sqla="select round(cast(monto_dispo as numeric),2) from sai_pres_consulta_disp(".$pres_anno.",'".$matriz_imputacion[$j]."','".$matriz_acc_pp[$j]."','".$matriz_acc_esp[$j]."','".$matriz_sub_esp[$j]."','". $matriz_uel[$j]."',".$matriz_monto[$j].") as monto_dispo ";
		$resultado_dispo = pg_exec($conexion ,$sqla);
		/*
		 //  Primero consulta los montos programados para esta partida (FORMA 1125)
		$query = "
		SELECT
		COALESCE(SUM(sf1125d.fodt_monto),0) AS monto
		FROM
		sai_forma_1125 AS sf1125,
		sai_fo1125_det AS sf1125d
		WHERE
		sf1125.pres_anno = ".$pres_anno." AND
		sf1125.form_id_p_ac = '".$matriz_acc_pp[$j]."' AND
		sf1125.form_tipo = '".$matriz_imputacion[$j]."' AND
		sf1125.form_id_aesp = '".$matriz_acc_esp[$j]."' AND
		sf1125.esta_id <> 15 AND
		sf1125.esta_id <> 2 AND
		sf1125d.form_id = sf1125.form_id AND
		sf1125d.pres_anno = sf1125.pres_anno AND
		sf1125d.part_id LIKE SUBSTRING(TRIM('".$matriz_sub_esp[$j]."') FROM 1 FOR 13)||'%'";
		$resultado = pg_exec($conexion ,$query);
		$row = pg_fetch_array($resultado,0);
		$montoProgramado=$row[0];

		//  Luego consulta las modificaciones realizadas sobre dicha partida
		//  Montos recibidos
		$query = "
		SELECT
		COALESCE(SUM(sf0305d.f0dt_monto),0) AS monto
		FROM
		sai_forma_0305 AS sf0305,
		sai_fo0305_det AS sf0305d
		WHERE
		sf0305.pres_anno = ".$pres_anno." AND
		sf0305.esta_id <> 15 AND
		sf0305.esta_id <> 2 AND
		sf0305d.f030_id = sf0305.f030_id AND
		sf0305d.pres_anno = sf0305.pres_anno AND
		sf0305d.f0dt_proy_ac = '".$matriz_imputacion[$j]."' AND
		sf0305d.f0dt_id_p_ac = '".$matriz_acc_pp[$j]."' AND
		sf0305d.f0dt_id_acesp = '".$matriz_acc_esp[$j]."' AND
		sf0305d.part_id LIKE SUBSTRING(TRIM('".$matriz_sub_esp[$j]."') FROM 1 FOR 13)||'%' AND
		sf0305d.f0dt_tipo = '1'";
		$resultado = pg_exec($conexion ,$query);
		$row = pg_fetch_array($resultado,0);
		$montoRecibido=$row[0];

		//  Montos cedidos
		$query = "
		SELECT
		COALESCE(SUM(sf0305d.f0dt_monto),0) AS monto
		FROM
		sai_forma_0305 AS sf0305,
		sai_fo0305_det AS sf0305d
		WHERE
		sf0305.pres_anno = ".$pres_anno." AND
		sf0305.esta_id <> 15 AND
		sf0305.esta_id <> 2 AND
		sf0305d.f030_id = sf0305.f030_id AND
		sf0305d.pres_anno = sf0305.pres_anno AND
		sf0305d.f0dt_proy_ac = '".$matriz_imputacion[$j]."' AND
		sf0305d.f0dt_id_p_ac = '".$matriz_acc_pp[$j]."' AND
		sf0305d.f0dt_id_acesp = '".$matriz_acc_esp[$j]."' AND
		sf0305d.part_id LIKE SUBSTRING(TRIM('".$matriz_sub_esp[$j]."') FROM 1 FOR 13)||'%' AND
		sf0305d.f0dt_tipo = '0'";
		$resultado = pg_exec($conexion ,$query);
		$row = pg_fetch_array($resultado,0);
		$montoCedido=$row[0];

		//Montos diferidos
		"
		SELECT
		COALESCE(SUM(spi.pcta_monto),0) as monto_c
		FROM
		sai_pcuenta as sp,
		sai_pcta_imputa as spi,
		sai_doc_genera sd
		WHERE
		sp.pcta_id=d.docg_id AND
		spi.pres_anno=pres_ano AND
		sp.esta_id <> 15 AND
		sp.esta_id <> 14 AND
		sp.esta_id <> 2 AND
		sp.pcta_id = spi.pcta_id and
		spi.pcta_tipo_impu = sw_ac_proy AND
		spi.pcta_acc_pp = id_proy_ac AND
		spi.pcta_acc_esp = id_aesp AND
		spi.pcta_sub_espe LIKE SUBSTRING(TRIM('".$matriz_sub_esp[$j]."') FROM 1 FOR 13)||'%' ";*/

		$valido=$resultado_dispo;
		if($valido) {
		 	$row = pg_fetch_array($resultado_dispo,0);
		 	$disponible_monto=$row[0];
	 		if ($disponible_monto<0) {
		 		//$pasodisponibilidad=-1;   //SI VALIDA DISPONIBILIDAD
		 		$pda_sob[$ind]=$matriz_sub_esp[$j]; //Partidas sin disponibilidad
		 		$ind=$ind+1;
		 	} else {
		 		$pasodisponibilidad++;
		 	}
		}
	}else{ // SE VALIDA DISPONIBILIDAD CONTRA EL PUNTO DE CUENTA
		$query_disponible="SELECT SUM(monto) as disponible FROM sai_disponibilidad_pcta WHERE pcta_asociado='".$pcta."' and partida='".$matriz_sub_esp[$j]."'";
		$resultado_query = pg_exec($conexion,$query_disponible);
		if ($row=pg_fetch_array($resultado_query))
			$disponible=$row['disponible']-$matriz_monto[$j];
		if ($disponible<0) {
			//$pasodisponibilidad=-1;   //SI VALIDA DISPONIBILIDAD
			$pda_sob[$ind]=$matriz_sub_esp[$j]; //Partidas sin disponibilidad
			$ind=$ind+1;
		} else {
			$pasodisponibilidad++;//
			array_push($monto_disponible, $disponible);
		}
	}
	$j++;
}

if ($pasodisponibilidad>-1) {
	$total_imputacion=$j;

	require_once("../../includes/arreglos_pg.php");

	$arreglo_acc_pp=convierte_arreglo($matriz_acc_pp);
	$arreglo_acc_esp=convierte_arreglo($matriz_acc_esp);
	$arreglo_sub_esp=convierte_arreglo($matriz_sub_esp);
	$arreglo_monto=convierte_arreglo($matriz_monto);
	$arreglo_uel=convierte_arreglo($matriz_uel);
	$arreglo_tipo_impu=convierte_arreglo($matriz_imputacion);
	if ($pcta<>"0"){
		$arreglo_monto_disp=convierte_arreglo($monto_disponible);
	} else{
		$arreglo_monto_disp="{}";
	}

	$descrip_sin_tags =$_POST['pcuenta_descripcionVal'];

	if (($_POST['pcuenta_asunto']=='001') || ($_POST['pcuenta_asunto']=='002') || ($_POST['pcuenta_asunto']=='023')){
		$estatus="Por Rendir";
	}else{
		$estatus="N/A";
	}

	$sql  = "select * from  sai_insert_compromiso('".trim($_POST['pcuenta_asunto'])."', '";
	$sql .= $descrip_sin_tags. "' , '".$_SESSION['login']."', '";
	$sql .= $_POST['opt_depe'] ."', '".$_POST['observaciones']."','".$_POST['justificacion']."', '";
	$sql .= $monto_solicitado."', '".$_POST['slc_prioridad'] ."','".$_SESSION['user_depe_id']."','";
	$sql .= $estatus."','".$_POST['tipo_act']."', '".$fecha."', '" .$_POST['pcta_id'] ."','";
	$sql .= trim($beneficiario[0])."','".$_POST['documento']."','".$_POST['ubica_info']."','".$pres_anno."','";
	$sql .= $arreglo_acc_pp."','".$arreglo_acc_esp."','".$arreglo_tipo_impu."','".$arreglo_sub_esp."','";
	$sql .= $arreglo_monto."','".$arreglo_uel."','".$_SESSION['user_perfil_id']."','".$arreglo_monto_disp."',
			'".trim($beneficiario[1])."','".$fecha_i."','".$fecha_f."','".$_POST['tipo_evento']."',
					'".$_POST['control_interno']."') As resultado_set(varchar)";
	$resultado_set = pg_exec($conexion ,$sql) or die("Error al ingresar el compromiso " .$sql);

	$row = pg_fetch_array($resultado_set);
	$codigo_comp=$row[0];
	$cod_doc = $codigo_comp;

	include("../../includes/respaldos_e1.php");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
</head>
<body>
	<?php if($pasodisponibilidad>-1){?>
	<table width="485" align="center"
		background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="normal">
			<td height="15" colspan="2" class="td_gray"
				class="normalNegroNegrita">COMPROMISO</td>
		</tr>
		<tr>
			<td class="normalNegrita">N&uacute;mero:</td>
			<td class="normalNegro"><b><?php echo $cod_doc;?> </b></td>
		</tr>
		<tr>
			<td class="normalNegrita">Fecha del compromiso:</td>
			<td class="normalNegro"><?php echo $fecha;?></td>
		</tr>
		<tr>
			<td class="normalNegrita">Elaborado por:</td>
			<td class="normalNegro"><? echo($_SESSION['solicitante']);?></td>
		</tr>
		<tr>
			<td class="normalNegrita">Unidad/Dependencia:</td>
			<td class="normalNegro"><?php
			$sql_str="SELECT depe_nombre FROM sai_dependenci WHERE depe_id='".$dependencia."'";
			$res_q=pg_exec($sql_str);
			if ($depe_row=pg_fetch_array($res_q)) {
	echo $depe_row['depe_nombre'];
}
?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Punto de cuenta asociado:</td>
			<td class="normalNegro"><? 
			if ($pcta=="0"){
	 $pcta="N/A";
	}
	echo($pcta);?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Asunto:</td>
			<td class="normalNegro"><?	
			$sql_asu = "SELECT * FROM sai_compromiso_asunt where cpas_id='".$_POST['pcuenta_asunto']."'";
			$result=pg_query($conexion,$sql_asu);
			if($row=pg_fetch_array($result))	{
	  $docu_nombre=$row["cpas_nombre"];
	}
	echo $docu_nombre; ?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Rif del Proveedor Sugerido:</td>
			<td class="normalNegro"><?	echo($_POST["rif_sugerido"]); ?></td>
		</tr>
		<tr>
			<td class="normalNegrita">Tipo Actividad:</td>
			<td class="normalNegro"><?php 
			$sql_asu = "SELECT * FROM sai_tipo_actividad where id='".$_POST['tipo_act']."'";
			$result=pg_query($conexion,$sql_asu);
			if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	}
	?>
			</td>
		</tr>
		<tr class="normal">
			<td><b>Tipo Evento:</b></td>
			<td class="normalNegro" colspan="3"><?php 
			$sql_asu = "SELECT * FROM sai_tipo_evento where id='".$_POST['tipo_evento']."'";
			$result=pg_query($conexion,$sql_asu);
			if($row=pg_fetch_array($result))	{
	 echo $row['nombre'];
	}
	?>
			</td>
		</tr>
		<tr class="normal">
			<td><b>Duracci&oacute;n de la Actividad:</b></td>
			<td class="normalNegro" colspan="3"><?php 
			echo cambia_esp($_POST['fecha_i'])." - ".cambia_esp($_POST['fecha_f']);
			?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Tipo Documento:</td>
			<td class="normalNegro"><?php 
			$sql_asu = "SELECT * FROM sai_tipo_documento where id='".$_POST['tipo_doc']."'";
			$result=pg_query($conexion,$sql_asu);
			if($row=pg_fetch_array($result))	{
	 echo $row['nombre']." - ".$_POST['documento'];
	}
	?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Descripci&oacute;n:</td>
			<td class="normalNegro"><? echo($_POST["pcuenta_descripcionVal"]); ?>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Observaciones:</td>
			<td class="normalNegro"><?echo $_POST['observaciones']?></td>
		</tr>
		<tr>
			<td class="normalNegrita">Monto solicitado:</td>
			<td class="normalNegro"><?echo $monto_solicitado;?></td>
		</tr>
		<tr class="normal">
			<td colspan="2">
				<table width="60%" border="0" class="tablaalertas" align="center">
					<tr class="td_gray">
						<td align="center" class="normalNegroNegrita">Imputaci&oacute;n
							presupuestaria</td>
					</tr>
					<tr>
						<td align="center">
							<table width="722" border="0">
								<tr>
									<td class="normal" align="left"><div align="center">ACC.C/PP</div>
									</td>
									<td class="normal" align="left"><div align="center">Acci&oacute;n
											espec&iacute;fica</div></td>
									<td class="normal"><div align="center">Dependencia</div></td>
									<td class="normal"><div align="center">Partida</div></td>
									<td class="normal"><div align="center">Monto</div></td>
								</tr>
								<tr>
									<?php for ($ii=0; $ii<$total_imputacion; $ii++){ ?>
									<td class="normalNegro" align="left"><div align="center">
											<input name="<?php echo "txt_imputa_proyecto_accion".$ii;?>"
												type="text"
												id="<?php echo "txt_imputa_proyecto_accion".$ii;?>" size="6"
												maxlength="15" class="normalNegro" align="right"
												value="<?php echo  $matriz_acc_pp[$ii];?>" readonly />
										</div></td>
									<td class="normalNegro" align="left"><div align="center">
											<input name="<?php echo "txt_imputa_accion_esp".$ii;?>"
												type="text" id="<?php echo "txt_imputa_accion_esp".$ii;?>"
												size="6" maxlength="15" class="normalNegro" align="right"
												value="<?php echo $matriz_acc_esp[$ii];?>" readonly />
										</div></td>
									<td class="normalNegro" align="left"><div align="center">
											<input name="<?php echo "txt_imputa_unidad".$ii;?>"
												type="text" id="<?php echo "txt_imputa_unidad".$ii;?>"
												size="5" maxlength="10" class="normalNegro" align="right"
												value="<?php echo $matriz_uel[$ii];?>" readonly />
										</div></td>
									<td class="normalNegro" align="left"><div align="center">
											<input name="<?php echo "txt_imputa_sub_esp".$ii;?>"
												type="text" id="<?php echo "txt_imputa_sub_esp".$ii;?>"
												size="12" maxlength="15" class="normalNegro" align="right"
												value="<?php echo $matriz_sub_esp[$ii];?>" readonly
												title="<?php if (trim( $matriz_sub_esp[$ii])== $partida_IVA){echo "Impuesto al valor agregado";}?>" />
										</div></td>
									<td class="normalNegro" align="left"><div align="center">
											<input name="<?php echo "txt_imputa_monto".$ii;?>"
												type="text" id="<?php echo "txt_imputa_monto".$ii;?>"
												size="10" maxlength="25" class="normalNegro" align="right"
												value="<?php echo  number_format($matriz_monto[$ii],2,'.',',');?>"
												readonly />
										</div></td>
								</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		<tr>
			<td class="normal" align="center" colspan="2"><a
				href="javascript:abrir_ventana('../../documentos/comp/comp_detalle.php?codigo=<?echo $cod_doc;?>&esta_id=En Transito')">Ver
					detalle</a></td>
		</tr>
		<tr>
			<td colspan="2"><? require_once('../../includes/respaldos_mostrar.php');?>
			</td>
		</tr>
		<tr>
			<td height="18" colspan="2">&nbsp;</td>
		</tr>
	</table>

	<?php } else{?>

	<table width="485" align="center" background="imagenes/fondo_tabla.gif"
		class="tablaalertas">
		<tr>
			<td colspan="3" class="normalNegrita"><div align="center">
					No existe la disponibilidad presupuestaria para registrar dicha
					solicitud <br>
					<?php echo(pg_errormessage($conexion)); ?>
					<br> <br> <img src="../../imagenes/mano_bad.gif" width="31"
						height="38">
				</div>
		
		</tr>
		<tr>
			<td colspan="4">
				<div align="center" class="peqNegrita_naranja">
					<?php echo('Las siguientes partidas no poseen fondos suficientes: ');  ?>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<?php
		$largo_arr=count($pda_sob);
	for($i=0;$i<$largo_arr;$i++){ ?>
		<tr>
			<td colspan="4">
				<div align="center" class="peqNegrita_naranja">
					<?php echo($pda_sob[$i]);  ?>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>
	<?php }?>
</body>
</html>
