<?php
require("../../../includes/constantes.php");
require("../../../includes/conexion.php");
$codigo=trim($_REQUEST['codigo']);
$anioPresupuestario=trim($_REQUEST['anioPress']);
$tipo=trim($_REQUEST['tipo']);
$accion=trim($_REQUEST['accion']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:. Modificar Categor&iacute;a Program&aacute;tica</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body class="normal">
<?php
if($codigo && $codigo!="" && $anioPresupuestario && $anioPresupuestario!="" && $tipo!=""){
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		$query = 	"SELECT ".
						"sp.proy_titulo as titulo, ".
						"sp.proy_desc as descripcion, ".
						"sp.proy_obj as objetivos, ".
						"sp.proy_resultado as resultado, ".
						"sp.proy_observa as observaciones ".
					"FROM ".
						"sai_proyecto sp ".
					"WHERE ".
						"sp.proy_id = '".$codigo."' AND sp.pre_anno = ".$anioPresupuestario;
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		$query = 	"SELECT ".
						"sac.acce_denom as titulo, ".
						"sac.acce_observa as observaciones ".
					"FROM ".
						"sai_ac_central sac ".
					"WHERE ".
						"sac.acce_id = '".$codigo."' AND sac.pres_anno = ".$anioPresupuestario;
	}
	$resultadoQuery=pg_query($conexion,$query);
	$row=pg_fetch_array($resultadoQuery);
	
	$titulo = $row["titulo"];
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		$descripcion = $row["descripcion"];
		$objetivos = $row["objetivos"];
		$resultado = $row["resultado"];		
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		$descripcion = "";
		$objetivos = "";
		$resultado = "";
	}
	$observaciones = $row["observaciones"];	
	?>
	<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">
				DETALLE DE CATEGOR&Iacute;A PROGRAM&Aacute;TICA
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">
							Tipo de categor&iacute;a<span class="peq_naranja"></span>
						</td>
						<td width="80%" class="normalNegro">
							<?php
								if($tipo==TIPO_IMPUTACION_PROYECTO){
									echo "Proyecto";
								}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
									echo "Acci&oacute;n centralizada";
								}
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table class="tablaalertas" width="900px">
					<tr>
						<td width="20%">A&ntilde;o presupuestario<span class="peq_naranja"></span></td>
						<td width="80%" class="normalNegro">
							<?= $anioPresupuestario?>
						</td>
					</tr>
					<tr>
						<td width="20%">C&oacute;digo<span class="peq_naranja"></span></td>
						<td width="80%" class="normalNegro">
							<?= $codigo?>
						</td>
					</tr>
					<tr>
						<td width="20%">T&iacute;tulo<span class="peq_naranja"></span></td>
						<td width="80%" class="normalNegro">
							<?= $titulo?>
						</td>
					</tr>
					<?php
					if($tipo==TIPO_IMPUTACION_PROYECTO){
					?>
					<tr>
						<td width="20%">Descripci&oacute;n<span class="peq_naranja"></span></td>
						<td width="80%" class="normalNegro">
							<?= $descripcion?>
						</td>
					</tr>
					<tr>
						<td width="20%">Objetivos<span class="peq_naranja"></span></td>
						<td width="80%" class="normalNegro">
							<?= $objetivos?>
						</td>
					</tr>
					<tr>
						<td width="20%">Resultado</td>
						<td width="80%" class="normalNegro">
							<?= $resultado?>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td width="20%">Observaciones</td>
						<td width="80%" class="normalNegro">
							<?= $observaciones?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
					if($tipo==TIPO_IMPUTACION_PROYECTO){
						$query = 	"SELECT ".
										"to_char(spae.paes_fecha_ini,'DD/MM/YYYY') as fecha_inicio, ".
										"spae.paes_nombre as titulo, ".
										"spae.paes_id as id, ".
										"spae.centro_gestor, ".
										"spae.centro_costo ".
									"FROM ".
										"sai_proy_a_esp spae ".
									"WHERE ".
										"spae.proy_id = '".$codigo."' AND spae.pres_anno = ".$anioPresupuestario." ".
									"ORDER BY spae.paes_id";
					}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
						$query = 	"SELECT ".
										"to_char(sae.aces_fecha_ini,'DD/MM/YYYY') as fecha_inicio, ".
										"sae.aces_nombre as titulo, ".
										"sae.aces_id as id, ".
										"sae.centro_gestor, ".
										"sae.centro_costo ".
									"FROM ".
										"sai_acce_esp sae ".
									"WHERE ".
										"sae.acce_id = '".$codigo."' AND sae.pres_anno = ".$anioPresupuestario." ".
									"ORDER BY sae.aces_id";
					}
					$resultadoQuery=pg_query($conexion,$query);
					$numeroFilas = pg_numrows($resultadoQuery);
				?>
				<input type="hidden" name="tamanoAccionesEspecificas" id="tamanoAccionesEspecificas" value="<?= $numeroFilas?>"/>
				<table width="900px" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr>
						<td colspan="5" class="normal peq_verde_bold">
							Acciones espec&iacute;ficas
						</td>
					</tr>
					<tr>
						<td colspan="5">
							<table width="100%" class="tablaalertas" id="tbl_mod">
								<tr class="td_gray normalNegrita">
									<td align="center" width="14%">Fecha inicio</td>
									<td align="center" width="40%">Nombre</td>
									<td align="center" width="12%">C&oacute;digo</td>
									<td align="center" width="12%">Centro gestor</td>
									<td align="center" width="12%">Centro costos</td>
								</tr>									
								<tbody id="accionEspecifica">
								<?php
								if($numeroFilas>0){
									while($row=pg_fetch_array($resultadoQuery)){
								?>
									<tr class="normalNegro">
										<td align="center" valign="top">
											<?= $row["fecha_inicio"]?>
										</td>
										<td align="left" valign="top">
											<?= $row["titulo"]?>
										</td>
										<td align="center" valign="top">
											<?= $row["id"]?>
										</td>
										<td align="center" valign="top">
											<?= $row["centro_gestor"]?>
										</td>
										<td align="center" valign="top">
											<?= $row["centro_costo"]?>
										</td>
									</tr>
								<?php
									}
								}
								?>
								</tbody>
								<tr>
									<td height="19" colspan="5">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br/>
	<center>
	<span class="normalNegrita">
	<?php
	if($tipo==TIPO_IMPUTACION_PROYECTO){
		if($accion=="generar"){
			echo "Usted ha creado con &eacute;xito el proyecto <span class='resultados'>\"".$titulo."\"</span>";
		}else if($accion=="modificar"){
			echo "Usted ha modificado con &eacute;xito el proyecto <span class='resultados'>\"".$titulo."\"</span>";
		}
	}else if($tipo==TIPO_IMPUTACION_ACCION_CENTRALIZADA){
		if($accion=="generar"){
			echo "Usted ha creado con &eacute;xito la acci&oacute;n centralizada <span class='resultados'>\"".$titulo."\"</span>";
		}else if($accion=="modificar"){
			echo "Usted ha modificado con &eacute;xito la acci&oacute;n centralizada <span class='resultados'>\"".$titulo."\"</span>";
		}
	}
	?>
	</span>
	</center>
<?php
}
?>
</body>
</html>