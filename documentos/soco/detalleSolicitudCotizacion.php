<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
require("../../includes/constantes.php");
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$codigoCR = "";
if (isset($_GET['codigoCR']) && $_GET['codigoCR'] != "") {
	$codigoCR = $_GET['codigoCR'];
}
$idSoco = "";
if (isset($_GET['idSoco']) && $_GET['idSoco'] != "") {
	$idSoco = $_GET['idSoco'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequ = $_GET['tipoRequ'];
}
$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;
if (isset($_GET['tipoBusq']) && $_GET['tipoBusq'] != "") {
	$tipoBusq = $_GET['tipoBusq'];
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>.:SAI:Solicitud de Cotizaci&oacute;n</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="../../js/funciones.js"> </script>
	<script language="Javascript">
		var proveedores=new Array();
		var proveedoresIndice = -1;
		
		function irARequisiciones(){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequ?>';
			controlFechas = '<?= $controlFechas?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			proyAcc = '<?= $proyAcc?>';
			radioProyAcc = '<?= $radioProyAcc?>';
			proyecto = '<?= $proyecto?>';
			accionCentralizada = '<?= $accionCentralizada?>';
			dependencia = '<?= $dependencia?>';
			pagina = '<?= $pagina?>';
			tipoBusq = '<?= $tipoBusq?>';
			location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia+"&codigoCR="+codigoCR;
		}

		function verArticulos(idRequ){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
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
			tipoBusq = '<?= $tipoBusq?>';
			location.href = "../rqui/requisicionAnalistaCompras.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc+"&codigoCR="+codigoCR;
		}
	</script>
</head>
<body class='normal'>
	<p align="center">
		<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
	</p>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td class="normalNegroNegrita">
				Solicitud de cotizaci&oacute;n c&Oacute;digo: <?=$idSoco?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
					$query = 	"SELECT ".
									"ssc.rebms_id ".
								"FROM ".
									"sai_sol_coti ssc ".
								"WHERE ".
									"ssc.soco_id = '".$idSoco."' ";
					$resultado = pg_exec($conexion,$query);
					$numeroFilas = pg_numrows($resultado);
					$row = pg_fetch_array($resultado, 0);
					$idRequ = $row["rebms_id"];
				?>
				<br/>C&oacute;digo de requisici&oacute;n: <a href="javascript: verArticulos('<?=$idRequ?>');"><?=$idRequ?></a>
			</td>
		</tr>
		<tr>
			<td>
				<?php
					$query = 	"SELECT ".
									"sp.prov_nombre, ".				
									"sp.prov_id_rif, ".
									"to_char(sscp.fecha,'DD/MM/YYYY HH:MI:SS am') as fecha, ".
									"to_char(sscp.fecha,'DD/MM/YYYY:HH24:MI:SS') as fecha_parametro, ".
									"sscp.tiempo_entrega, ".
									"sscp.sitio_entrega, ".
									"sscp.anexos, ".
									"sscp.observaciones, ".
									"sscp.asunto, ".
									"sscp.cuerpo ".
								"FROM ".
									"sai_sol_coti_prov sscp, sai_proveedor_nuevo sp ".
								"WHERE ".
									"sscp.soco_id = '".$idSoco."' ".
									"AND sscp.beneficiario_rif = sp.prov_id_rif ".
								"ORDER BY sscp.fecha DESC, sp.prov_nombre ASC ";
					$resultado = pg_exec($conexion,$query);
					$numeroFilas = pg_numrows($resultado);
					$fechaAnterior = "";
					$sitioEntrega = "";
					$tiempoEntrega = "";
					$anexos = "";
					$observaciones = "";
					$asunto = "";
					$cuerpo = "";
					if($numeroFilas>0){
						for($ri = 0; $ri < $numeroFilas; $ri++) {
							$row = pg_fetch_array($resultado, $ri);
							if($fechaAnterior!=$row["fecha_parametro"]){
								if($fechaAnterior!=""){
									echo "</td></tr>".
									"<tr><td>Asunto:</td><td class='normalNegro'>".$asunto."</td></tr>".
									"<tr><td>Cuerpo:</td><td class='normalNegro'>".$cuerpo."</td></tr>".
									"<tr><td width='120px'>Tiempo de entrega:</td><td class='normalNegro'>".$tiempoEntrega."</td></tr>".
									"<tr><td>Sitio de entrega:</td><td class='normalNegro'>".$sitioEntrega."</td></tr>".
									"<tr><td>Anexos:</td><td class='normalNegro'>".$anexos."</td></tr>".
									"<tr><td>Observaciones:</td><td class='normalNegro'>".$observaciones."</td></tr>".
									"<tr><td colspan='2'>&nbsp;</td></tr>".
									"<tr>
										<td>Rubros:</td>
										<td>
										<table border='1' cellspacing='0' cellpadding='0'>
											<tr class='td_gray normalNegrita' style='text-align: center;'>
												<td width='300px'>Compra/Servicio</td>
												<td width='100px'>Cantidad</td>
												<td width='400px'>Especificaciones</td>
											</tr>";									
									$resultadoItem = pg_exec($conexion, "SELECT si.nombre, ssci.cantidad, ssci.especificaciones AS descripcion ".
																	"FROM sai_sol_coti_item ssci, sai_item si ".
																	"WHERE ".
																		"ssci.soco_id = '".$idSoco."' AND ".
																		"to_char(ssci.fecha,'DD/MM/YYYY:HH24:MI:SS') = '".$fechaAnterior."' AND ".
																		"ssci.id_item = si.id ".
																	"ORDER BY si.nombre ASC");
									$numeroFilasItem = pg_numrows($resultadoItem);									
									if($numeroFilasItem>0){
										$ii=0;
										while($rowItem=pg_fetch_array($resultadoItem))  {
											echo "<tr class='normalNegro'>".
													"<td>".$rowItem["nombre"]."</td>".
													"<td align='center'>".$rowItem["cantidad"]."</td>".
													"<td>".(($rowItem["descripcion"]!="no")?$rowItem["descripcion"]:"")."</td>".
												"</tr>";
											$ii++;
										}
									}									
									echo "</table>
									</td></tr>".
									"</table>";
								}
								$fechaMostrar=$row["fecha"];
								$fechaAnterior=$row["fecha_parametro"];
								$tiempoEntrega = $row["tiempo_entrega"];
								$sitioEntrega = $row["sitio_entrega"];
								$anexos = $row["anexos"];
								$observaciones = $row["observaciones"];
								$asunto = $row["asunto"];
								$cuerpo = $row["cuerpo"];
								echo "<br/><table class='tablaalertas' width='100%'><tr class='td_gray'><td colspan='2' class='normalNegrita'>Enviado en fecha: ".$fechaMostrar."&nbsp;".
									"<a href='detalleSolicitudCotizacion_PDF.php?codigo=".$idSoco."&fecha=".$fechaAnterior."'>".
										"<img src='../../imagenes/pdf_ico.jpg' border='0' align='center'/>".
									"</a>(Todos los proveedores)"."</td></tr>".
								"<tr><td>Para: </td><td class='normalNegro'>";
							}
							echo $row["prov_nombre"]." (Rif ".strtoupper(substr(trim($row["prov_id_rif"]),0,1))."-".substr(trim($row["prov_id_rif"]),1).")<a href='detalleSolicitudCotizacion_PDF.php?codigo=".$idSoco."&fecha=".$fechaAnterior."&proveedor=".$row["prov_id_rif"]."'>".
										"<img src='../../imagenes/pdf_ico.jpg' border='0' align='center'/>".
									"</a><br/>";
							if(($ri+1)==$numeroFilas){
								echo "</td></tr>".
								"<tr><td>Asunto:</td><td class='normalNegro'>".$asunto."</td></tr>".
								"<tr><td>Cuerpo:</td><td class='normalNegro'>".$cuerpo."</td></tr>".
								"<tr><td width='120px'>Tiempo de entrega:</td><td class='normalNegro'>".$tiempoEntrega."</td></tr>".
								"<tr><td>Sitio de entrega:</td><td class='normalNegro'>".$sitioEntrega."</td></tr>".
								"<tr><td>Anexos:</td><td class='normalNegro'>".$anexos."</td></tr>".
								"<tr><td>Observaciones:</td><td class='normalNegro'>".$observaciones."</td></tr>".
								"<tr><td colspan='2'>&nbsp;</td></tr>".
								"<tr>
									<td>Rubros:</td>
									<td>
									<table border='1' cellspacing='0' cellpadding='0'>
										<tr class='td_gray normalNegrita' style='text-align: center;'>
											<td width='300px'>Compra/Servicio</td>
											<td width='100px'>Cantidad</td>
											<td width='400px'>Especificaciones</td>
										</tr>";										
								$resultadoItem = pg_exec($conexion, "SELECT si.nombre, ssci.cantidad, ssci.especificaciones AS descripcion ".
																"FROM sai_sol_coti_item ssci, sai_item si ".
																"WHERE ".
																	"ssci.soco_id = '".$idSoco."' AND ".
																	"to_char(ssci.fecha,'DD/MM/YYYY:HH24:MI:SS') = '".$fechaAnterior."' AND ".
																	"ssci.id_item = si.id ".
																"ORDER BY si.nombre ASC");
								$numeroFilasItem = pg_numrows($resultadoItem);									
								if($numeroFilasItem>0){
									$ii=0;
									while($rowItem=pg_fetch_array($resultadoItem))  {
										echo "<tr class='normalNegro'>".
												"<td>".$rowItem["nombre"]."</td>".
												"<td align='center'>".$rowItem["cantidad"]."</td>".
												"<td>".(($rowItem["descripcion"]!="no")?$rowItem["descripcion"]:"")."</td>".
											"</tr>";
										$ii++;
									}
								}									
								echo "</table>
								</td></tr>".
								"</table>";
							}
						}
					}else{ echo "No se encontraron solicitudes a proveedores";}?>
			</td>
		</tr>
	</table>
</body>
</html>
<?php pg_close($conexion); ?>