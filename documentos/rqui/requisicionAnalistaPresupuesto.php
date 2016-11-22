<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
require("../../includes/constantes.php");
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$idRequ = "";
if (isset($_GET['idRequ']) && $_GET['idRequ'] != "") {
	$idRequ = $_GET['idRequ'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequ = $_GET['tipoRequ'];
}
$pagina = "1";
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
	$pagina = $_GET['pagina'];
}
$proyAcc = "";
if (isset($_GET['proyAcc']) && $_GET['proyAcc'] != "") {
	$proyAcc = $_GET['proyAcc'];
}
$radioProyAcc = "";
if (isset($_GET['radioProyAcc']) && $_GET['radioProyAcc'] != "") {
	$radioProyAcc = $_GET['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_GET['proyecto']) && $_GET['proyecto'] != "") {
		$proyecto = $_GET['proyecto'];
	}		
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_GET['accionCentralizada']) && $_GET['accionCentralizada'] != "") {
		$accionCentralizada = $_GET['accionCentralizada'];
	}		
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$estado = ESTADO_REQUISICION_NO_REVISADAS;
if (isset($_GET['estado']) && $_GET['estado'] != "") {
	$estado = $_GET['estado'];
}
$controlFechas = "";
if (isset($_GET['controlFechas']) && $_GET['controlFechas'] != "") {
	$controlFechas = $_GET['controlFechas'];
}
$fechaInicio = "";
if (isset($_GET['fechaInicio']) && $_GET['fechaInicio'] != "") {
	$fechaInicio = $_GET['fechaInicio'];
}
$fechaFin = "";
if (isset($_GET['fechaFin']) && $_GET['fechaFin'] != "") {
	$fechaFin = $_GET['fechaFin'];
}
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
$sql="SELECT perf_id_act FROM sai_doc_genera WHERE docg_id = '".$idRequ."'";
$resultado = pg_exec($conexion ,$sql);
$row = pg_fetch_array($resultado,0);
if($row["perf_id_act"]!=$_SESSION['user_perfil_id']){
	header('Location:detalleRequisicion.php?codigo='.$codigo.'&idRequ='.$idRequ.'&tipoRequ='.$tipoRequ.'&tipoBusq='.$tipoBusq.'&pagina='.$pagina.'&estado='.$estado.'&controlFechas='.$controlFechas.'&fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia,false);
	exit;
}
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>.:SAFI:. Detalle de Requisici&oacute;n</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<script language="Javascript">
		function bandeja(){
			location.href = "bandeja.php";
		}
		function irARequisiciones(){
			codigo = '<?= $codigo?>';
			tipoRequ = '<?= $tipoRequ?>';
			proyAcc = '<?= $proyAcc?>';
			radioProyAcc = '<?= $radioProyAcc?>';
			proyecto = '<?= $proyecto?>';
			accionCentralizada = '<?= $accionCentralizada?>';
			dependencia = '<?= $dependencia?>';
			estado = '<?= $estado?>';
			controlFechas = '<?= $controlFechas?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			pagina = '<?= $pagina?>';
			location.href = "busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia;
		}

		function aprobar(){
			if(confirm(pACUTE+'Est'+aACUTE+' seguro que desea aprobar esta requisici'+oACUTE+'n?.')){
				document.getElementById("accion").value = '<?= ACCION_APROBAR_REQUISICION?>';
				document.formArticulosAccion.submit();
			}
		}

		function devolver(){
			if(confirm(pACUTE+'Est'+aACUTE+' seguro que desea devolver esta requisici'+oACUTE+'n?.')){
				var memo=prompt('Por favor indique el motivo de devoluci'+oACUTE+'n');
				memo = validarTexto(String(memo));
				if(memo!=null && memo!="" && memo!="null" && trim(memo)!=""){
					memo = trim(memo);
					if(memo.length>220){
						memo = memo.substring(0, 220);
					}
					document.getElementById("accion").value = '<?= ACCION_DEVOLVER_REQUISICION?>';
					document.getElementById("memo").value = memo;
					document.formArticulosAccion.submit();
				}
			}
		}
	</script>
</head>
<body class="normal">
<?php
if($idRequ && $idRequ!=""){
	$query = 	"SELECT ".
					"srbms.rebms_tipo, ".
					"srbms.rebms_tipo_imputa, ".
					"srbms.rebms_imp_p_c, ".
					"srbms.rebms_imp_esp, ".
					"srbms.rebms_prov_sugerido1, ".
					"srbms.rebms_prov_sugerido2, ".
					"srbms.rebms_prov_sugerido3, ".
					"srbms.rebms_calidad_sugerida, ".
					"srbms.rebms_tiempo_entrega_sugerida, ".
					"srbms.rebms_garantia_sugerida, ".
					"srbms.rebms_observaciones, ".
					"srbms.observaciones_almacen, ".
					"se.esta_nombre, ".
					"se.esta_id, ".
					"sd.depe_nombre, ".
					"sd.depe_id, ".
					"srbms.pcta_id, ".
					"to_char(srbms.fecha,'DD/MM/YYYY') as fecha, ".
					"srbms.pcta_justificacion, ".
					"srbms.descripcion_general, ".
					"srbms.justificacion, ".
					"sda.depe_nombre as gerencia_adscripcion ".
				"FROM sai_req_bi_ma_ser srbms, sai_estado se, sai_dependenci sd, sai_dependenci sda ".
				"WHERE ".
				"srbms.rebms_id = '".$idRequ."' AND ".
				"srbms.esta_id = se.esta_id AND ".
				"srbms.depe_id = sd.depe_id AND ".
				"srbms.gerencia_adscripcion = sda.depe_id ";
	
	$resultado = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultado, 0);
	$rebms_tipo = $row["rebms_tipo"];
	$rebms_tipo_imputa = $row["rebms_tipo_imputa"];
	$rebms_imp_p_c = $row["rebms_imp_p_c"];
	$rebms_imp_esp = $row["rebms_imp_esp"];
	$rebms_prov_sugerido1 = $row["rebms_prov_sugerido1"];
	$rebms_prov_sugerido2 = $row["rebms_prov_sugerido2"];
	$rebms_prov_sugerido3 = $row["rebms_prov_sugerido3"];
	$rebms_calidad_sugerida = $row["rebms_calidad_sugerida"];
	$rebms_tiempo_entrega_sugerida = $row["rebms_tiempo_entrega_sugerida"];
	$rebms_garantia_sugerida = $row["rebms_garantia_sugerida"];
	$rebms_observaciones = $row["rebms_observaciones"];
	$rebms_observaciones_almacen = $row["observaciones_almacen"];
	$esta_nombre = $row["esta_nombre"];
	$esta_id = $row["esta_id"];
	$depe_id = $row["depe_id"];
	$depe_nombre_req = $row["depe_nombre"];
	$pcta_id = $row["pcta_id"];
	$fecha = $row["fecha"];
	$pcta_justificacion = $row["pcta_justificacion"];
	$descripcion_general = $row["descripcion_general"];
	$justificacion = $row["justificacion"];
	$gerencia_adscripcion = $row["gerencia_adscripcion"];
	
	if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){//Proyecto
		$query = "SELECT proy_titulo FROM sai_proyecto WHERE proy_id = '".$rebms_imp_p_c."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$proy_titulo = $row["proy_titulo"];
		$query = "SELECT paes_nombre,centro_gestor,centro_costo FROM sai_proy_a_esp WHERE proy_id = '".$rebms_imp_p_c."' AND paes_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["paes_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
	}else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){//Accion centralizada
		$query = "SELECT aces_nombre,centro_gestor,centro_costo FROM sai_acce_esp WHERE acce_id = '".$rebms_imp_p_c."' AND aces_id = '".$rebms_imp_esp."'";
		$resultado = pg_exec($conexion, $query);
		$row = pg_fetch_array($resultado, 0);
		$aces_nombre = $row["aces_nombre"]."(".$row["centro_gestor"]."-".$row["centro_costo"].")";
		$proy_titulo = $rebms_imp_p_c."-".$row["aces_nombre"];
	}
if ( $bandeja!="true" ) {
?>
<p align="center">
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
</p>
<?php
}
$msg=$_GET['msg'];
if($msg=="0"){
	echo "<p class='normal' style='color: red;text-align: center;'>Debe indicar el memo de la devoluci&oacute;n.</p>";
}
?>
<form action="requisicionAnalistaPresupuestoAccion.php" method="post" id="formArticulosAccion" name="formArticulosAccion">
	<input type="hidden" id="codigo" name="codigo" value="<?= $codigo?>"/>
	<input type="hidden" id="idRequ" name="idRequ" value="<?= $idRequ?>"/>
	<input type="hidden" id="tipoRequ" name="tipoRequ" value="<?= $tipoRequ?>"/>
	<input type="hidden" id="controlFechas" name="controlFechas" value="<?= $controlFechas?>"/>
	<input type="hidden" id="fechaInicio" name="fechaInicio" value="<?= $fechaInicio?>"/>
	<input type="hidden" id="fechaFin" name="fechaFin" value="<?= $fechaFin?>"/>
	<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
	<input type="hidden" id="proyAcc" name="proyAcc" value="<?= $proyAcc?>"/>
	<input type="hidden" id="radioProyAcc" name="radioProyAcc" value="<?= $radioProyAcc?>"/>
	<input type="hidden" id="proyecto" name="proyecto" value="<?= $proyecto?>"/>
	<input type="hidden" id="accionCentralizada" name="accionCentralizada" value="<?= $accionCentralizada?>"/>
	<input type="hidden" id="dependencia" name="dependencia" value="<?= $dependencia?>"/>
	<input type="hidden" id="estado" name="estado" value="<?= $estado?>"/>
	<input type="hidden" id="bandeja" name="bandeja" value="<?=$bandeja?>"/>
	<input type="hidden" id="accion" name="accion" value=""/>
	<input type="hidden" id="memo" name="memo" value=""/>
</form>
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">
			DETALLE DE REQUISICI&Oacute;N C&Oacute;DIGO: <?= $idRequ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr>
					<td width="20%">
						Procedente de:
					</td>
					<td width="80%" class="normalNegro">
						<?= $depe_nombre_req?>
					</td>
				</tr>
				<tr>
					<td>
						Tipo de requisici&oacute;n:
					</td>
					<td class="normalNegro">
						<?php
							if($rebms_tipo == TIPO_REQUISICION_COMPRA){ echo "Compra";}
							else if($rebms_tipo == TIPO_REQUISICION_SERVICIO){ echo "Servicio";}
						?>
					</td>
				</tr>
				<tr>
					<td>
						Fecha:
					</td>
					<td class="normalNegro">
						<?= $fecha?>
					</td>
				</tr>
				<tr>
					<td>
						Estado:
					</td>
					<td class="normalNegro">
						<?= $esta_nombre?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr class="td_gray normalNegrita">
					<td align="center">
						Categor&iacute;a
					</td>
					<td align="center">
						C&oacute;digo
					</td>
					<td align="center">
						Denominaci&oacute;n
					</td>
				</tr>
				<tr>
					<td align="left" class="normalNegro">
						<?php
							if($rebms_tipo_imputa==TIPO_IMPUTACION_PROYECTO){echo "Proyecto";}
							else if($rebms_tipo_imputa==TIPO_IMPUTACION_ACCION_CENTRALIZADA){echo "Acci&oacute;n Cent.";}
						?>
					</td>
					<td align="left" class="normalNegro">
						<?= $rebms_imp_p_c?>
					</td>
					<td align="left" class="normalNegro">
						<?= $proy_titulo?>
					</td>
				</tr>
				<tr>
					<td align="left" class="normalNegro">
						Acci&oacute;n Espec&iacute;fica
					</td>
					<td align="left" class="normalNegro">
						<?= $rebms_imp_esp?>
					</td>
					<td align="left" class="normalNegro">
						<?= $aces_nombre?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr>
					<td width="20%">
						Unidad de adscripci&oacute;n
					</td>
					<td width="80%" align="left" class="normalNegro">
						<?= $gerencia_adscripcion?>
					</td>
				</tr>
				<tr>
					<td>
						Descripci&oacute;n del producto o servicio solicitado
					</td>
					<td class="normalNegro">
						<?= $descripcion_general?>
					</td>
				</tr>
				<tr>
					<td>
						Justificaci&oacute;n:
					</td>
					<td class="normalNegro">
						<?= $justificacion?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="tablaalertas" width="900px">
				<tr>
					<td width="20%">
						Punto de cuenta:
					</td>
					<td width="80%" class="normalNegro">
						<?= (($pcta_id!="")?$pcta_id:"no aplica")?>
					</td>
				</tr>
				<tr>
					<td>
						Justificaci&oacute;n:
					</td>
					<td class="normalNegro">
						<?= $pcta_justificacion?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table width="900px" class="tablaalertas">
				<tr class="td_gray normalNegrita">
					<td align="center" width="10%">C&oacute;digo</td>
					<td align="center" width="20%">Nombre</td>
					<td align="center" width="10%">Partida</td>
					<td align="center" width="20%">Denominaci&oacute;n</td>
					<td align="center" width="30%">Especificaciones</td>
					<td align="center" width="10%">Cantidad</td>
				</tr>
				<tbody id="item">
				<?php
					$query = 	"SELECT DISTINCT ".
									"si.id, ".
									"si.nombre, ".
									"sri.rbms_item_cantidad as cantidad, ".
									"sri.rbms_item_desc as descripcion, ".
									"sp.part_id as id_partida, ".
									"sp.part_nombre as nombre_partida ".
								"FROM ".
									"sai_rqui_items sri, sai_item si, sai_item_partida sip, sai_partida sp ".
								"WHERE ".
									"sri.rebms_id = '".$idRequ."' AND ".
									"sri.rbms_item_arti_id = si.id AND ".
									"sri.rbms_item_arti_id = sip.id_item AND ".
									"sip.part_id = sp.part_id AND ".
									"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ";
				
					$resultado = pg_exec($conexion, $query);
					$elementos = pg_numrows($resultado);
					for($i=0;$i<$elementos;$i++){
						$row = pg_fetch_array($resultado, $i);
				?>
				<tr>
					<td valign="top" align="center" class="normalNegro">
						<?=$row["id"]?>
					</td>
					<td valign="top" align="left" class="normalNegro">
						<?=$row["nombre"]?>
					</td>
					<td valign="top" align="center" class="normalNegro">
						<?=$row["id_partida"]?>
					</td>
					<td valign="top" align="left" class="normalNegro">
						<?=$row["nombre_partida"]?>
					</td>
					<td valign="top" align="left" class="normalNegro">
						<?=$row["descripcion"]?>
					</td>
					<td valign="top" align="center" class="normalNegro">
						<?=$row["cantidad"]?>
					</td>
				</tr>
				<?php
					}
				?>
				</tbody>
				<tr>
					<td height="19" colspan="6">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="900px" class="tablaalertas">
				<tr>
					<td style="width: 260px;">Proveedor sugerido (RIF o nombre)</td>
					<td>
						<table>
							<tr>
								<td style="width: 80px;">sugerencia 1: </td>
								<?php
								$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido1."'";
								$resultado = pg_exec($conexion, $query);
								$elementos = pg_numrows($resultado);
								if($elementos>0){
									$row = pg_fetch_array($resultado, 0);
									$rebms_prov_sugerido1 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
								}
								?>									
								<td class="normalNegro"><?=$rebms_prov_sugerido1?></td>
							</tr>
							<tr>
								<td>sugerencia 2: </td>
								<?php
								$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido2."'";
								$resultado = pg_exec($conexion, $query);
								$elementos = pg_numrows($resultado);
								if($elementos>0){
									$row = pg_fetch_array($resultado, 0);
									$rebms_prov_sugerido2 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
								}
								?>
								<td class="normalNegro"><?=$rebms_prov_sugerido2?></td>
							</tr>
							<tr>
								<td>sugerencia 3: </td>
								<?php
								$query = "SELECT prov_id_rif,prov_nombre FROM sai_proveedor_nuevo WHERE prov_id_rif = '".$rebms_prov_sugerido3."'";
								$resultado = pg_exec($conexion, $query);
								$elementos = pg_numrows($resultado);
								if($elementos>0){
									$row = pg_fetch_array($resultado, 0);
									$rebms_prov_sugerido3 = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")";
								}
								?>
								<td class="normalNegro"><?=$rebms_prov_sugerido3?></td>
							</tr>
						</table>								
					</td>
				</tr>
				<tr>
					<td>Caracter&iacute;sticas sugeridas para seleccionar proveedor</td>
					<td >
						<table>
							<tr>
								<td>Tiempo de Entrega sugerido: </td>
								<td class="normalNegro">
									<?php
										if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_7_DIAS) echo "Menor a 7 D&iacute;as";
										else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_2_SEMANAS) echo "Menor a 2 Semanas";
										else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MENOR_1_MES) echo "Menor a 1 Mes";
										else if($rebms_tiempo_entrega_sugerida==TIEMPO_ENTREGA_MAYOR_1_MES) echo "Mayor a 1 Mes";
									?>
								</td>
							</tr>
							<tr>
								<td>Garant&iacute;a sugerida: </td>
								<td class="normalNegro">
									<?php
										if($rebms_garantia_sugerida==GARANTIA_SI) echo "Si";
										else if($rebms_garantia_sugerida==GARANTIA_NO) echo "No";
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Observaciones:</td>
					<td class="normalNegro">
						<?=$rebms_observaciones?>
					</td>
				</tr>
				<?php if($rebms_observaciones_almacen && $rebms_observaciones_almacen!=""){ ?>
				<tr>
					<td>Observaciones Almac&eacute;n:</td>
					<td class="normalNegro">
						<?=$rebms_observaciones_almacen?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</td>
	</tr>
</table>
<br/>
<div id="divAcciones" style="text-align: center;">
	<input type="button" class="normalNegro" value="Aprobar" onclick="aprobar();"/>
	<input type="button" class="normalNegro" value="Devolver" onclick="devolver();"/>
	<?php 
		  if ( $bandeja!="true" ) {
	?>
				<input type="button" class="normalNegro" value="Cancelar" onclick="irARequisiciones();"/>
	<?php } else { ?>
				<input type="button" class="normalNegro" value="Cancelar" onclick="bandeja();"/>
	<?php } ?>
</div>
<?php
}
?>
</body>
</html>
<?php pg_close($conexion); ?>