<?php
ob_start();
session_start();
require("../../../includes/conexion.php");
require("../../../includes/constantes.php");
require("../../../includes/perfiles/constantesPerfiles.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
$user_perfil_id = $_SESSION['user_perfil_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>...:SAFI:Asignaci&oacute;n presupuestaria</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css"/>
	<script language="JavaScript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script language="JavaScript" src="../../../js/funciones.js"></script>
	<script language="javascript">
		function verDetalle(codigo){
			location.href="detalle.php?codigo="+codigo;
		}
		function modificar(codigo){
			location.href="modificar.php?codigo="+codigo;
		}
	</script>
</head>
<body class="normal">
	<?php
	$query = 	"SELECT ".
					"s.id, ".
					"s.nombre_categoria, ".
					"s.tipo_imputacion, ".
					"s.nombre_accion_especifica, ".
					"sd.depe_nombre AS nombre_dependencia, ".
					"TO_CHAR(s.fecha,'DD/MM/YYYY') AS fecha_cadena, ".
					"s.fecha, ".
					"sff.fuef_descripcion AS fuente_financiamiento, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
				    "s.esta_nombre ".
				"FROM ".
					"sai_dependenci sd, ".
					"sai_fuente_fin sff, ".
					"(".
						"SELECT ".
							"sf.form_id AS id, ".
							"sp.proy_titulo AS nombre_categoria, ".	
							"sf.form_tipo AS tipo_imputacion, ".
							"spae.paes_nombre AS nombre_accion_especifica, ".
							"sf.form_fecha AS fecha, ".
							"sf.fuente_financiamiento, ".
							"sf.depe_cosige, ".
							"spae.centro_gestor, ".
							"spae.centro_costo, ".
							"se.esta_nombre ".
						"FROM ".
							"sai_forma_1125 sf, ".
							"sai_proyecto sp, ".
							"sai_proy_a_esp spae, ".
						    "sai_estado se ".
						"WHERE ".
							"sf.form_id_p_ac = sp.proy_id AND ".
							"sf.pres_anno = sp.pre_anno AND ".
							"sf.form_id_p_ac = spae.proy_id AND ".
							"sf.pres_anno = spae.pres_anno AND ".
							"sf.form_id_aesp = spae.paes_id AND ".
						    "se.esta_id=sf.esta_id ".
						"UNION ".
						"SELECT ".
							"sf.form_id AS id, ".
							"sac.acce_denom AS nombre_categoria, ".	
							"sf.form_tipo AS tipo_imputacion, ".
							"sae.aces_nombre AS nombre_accion_especifica, ".
							"sf.form_fecha AS fecha, ".
							"sf.fuente_financiamiento, ".
							"sf.depe_cosige, ".
							"sae.centro_gestor, ".
							"sae.centro_costo, ".
							"se.esta_nombre ".
						"FROM ".
							"sai_forma_1125 sf, ".
							"sai_ac_central sac, ".
							"sai_acce_esp sae, ".
							"sai_estado se ".
						"WHERE ".
							"sf.form_id_p_ac = sac.acce_id AND ".
							"sf.pres_anno = sac.pres_anno AND ".
							"sf.form_id_p_ac = sae.acce_id AND ".
							"sf.pres_anno = sae.pres_anno AND ".
							"sf.form_id_aesp = sae.aces_id AND ".
							"se.esta_id=sf.esta_id ".
					") AS s ".
				"WHERE ".
					"s.depe_cosige = sd.depe_id AND ".
					"s.fuente_financiamiento = sff.fuef_id ".
				"ORDER BY s.fecha DESC ";
	$resultado=pg_query($conexion,$query);
	$numeroFilas = pg_numrows($resultado);
	?>
	<table width="100%" border="0" align="center">
		<tr>
			<td height="27" class="normal peq_verde_bold">
				<div align="center">Asignaciones presupuestarias</div>
			</td>
		</tr>
	</table>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray normalNegroNegrita">
			<td width="8%" align="center">C&oacute;digo</td>
			<td width="8%" align="center">Fecha</td>
			<td width="8%" align="center">Tipo</td>
			<td width="22%" align="center">Categor&iacute;a program&aacute;tica</td>
			<td width="21%" align="center">Acci&oacute;n espec&iacute;fica</td>
			<td width="5%" align="center">Centro gestor</td>
			<td width="5%" align="center">Centro costo</td>
			<td width="5%" align="center">Estatus</td>
			<td width="8%" align="center">Dependencia</td>
			<td width="8%" align="center">Fuente financiamiento</td>
			<td width="7%" align="center"></td>
		</tr>
		<?
		if($numeroFilas>0){
			while($row=pg_fetch_array($resultado)){
			?>
			<tr class="resultados">
				<td height="28" align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>');"><?= $row['id']?></a>
					</span>
				</td>
				<td align="center"><?= $row['fecha_cadena'];?></td>
				<td align="center"><?= (($row['tipo_imputacion']==TIPO_IMPUTACION_PROYECTO)?"Proyecto":"Acci&oacute;n centralizada")?></td>
				<td valign="top"><?= $row['nombre_categoria'];?></td>
				<td valign="top"><?= $row['nombre_accion_especifica'];?></td>
				<td align="center"><?= $row['centro_gestor'];?></td>
				<td align="center"><?= $row['centro_costo'];?></td>
				<td align="center"><?= $row['esta_nombre'];?></td>
				<td align="center"><?= $row['nombre_dependencia'];?></td>
				<td align="center"><?= $row['fuente_financiamiento'];?></td>
				<td align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>');">Ver Detalle</a>
					</span>
					<?php
					if($user_perfil_id==PERFIL_ANALISTA_PRESUPUESTO || $user_perfil_id==PERFIL_JEFE_PRESUPUESTO){
					?>
					<br/>
					<span class="link">
						<a href="javascript:modificar('<?= trim($row['id'])?>');">Modificar</a>
					</span>
					<?php
					}
					?>
				</td>
			</tr>
			<?php
			}
		}else{
			echo "<tr><td align='center' valign='middle' height='50' colspan='10'>No se encontr&oacute; asignaciones presupuestarias</td></tr>";
		}
	?>
	</table>
</body>
</html>
<?php pg_close($conexion); ?>