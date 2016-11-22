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
	<title>...:SAI:Categor&iacute;a program&aacute;tica</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css"/>
	<script language="JavaScript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script language="JavaScript" src="../../../js/funciones.js"></script>
	<script language="javascript">
		var TIPO_PROYECTO = 1;
		var TIPO_ACCION_CENTRALIZADA = 0;
		var proyectos = new Array();
		var accionesCentralizadas = new Array();
		
		function verDetalle(codigo, anioPresupuestario, tipo){
			location.href="detalle.php?codigo="+codigo+"&anioPress="+anioPresupuestario+"&tipo="+tipo;
		}
		function modificar(codigo, anioPresupuestario, tipo){
			location.href="modificar.php?codigo="+codigo+"&anioPress="+anioPresupuestario+"&tipo="+tipo;
		}
	
		function accionActivar(tipo,indice,activarCategoria){
			$.ajax({
		        url: "activar.php",
		        dataType: "text",
		        async: false,
		        type: "POST",
		        data: {
		                anioPres: ((tipo==TIPO_PROYECTO)?proyectos[indice][2]:accionesCentralizadas[indice][2]),
		                codigo: ((tipo==TIPO_PROYECTO)?proyectos[indice][3]:accionesCentralizadas[indice][3]),
		                tipo: tipo,
		                activar: ((activarCategoria && activarCategoria==true)?"true":"false")
		        },
		        success: function(text){
		        	if(text=="activo") {
			        	if(tipo==TIPO_PROYECTO){
			        		alert("El proyecto ha sido activado exitosamente.");	
				        }else if(tipo==TIPO_ACCION_CENTRALIZADA){
			        		alert("La acci"+oACUTE+"n centralizada ha sido activada exitosamente.");
				        }
						window.location.reload();
					}else if(text=="inactivo") {
						if(tipo==TIPO_PROYECTO){
			        		alert("El proyecto ha sido inactivado exitosamente.");	
				        }else if(tipo==TIPO_ACCION_CENTRALIZADA){
			        		alert("La acci"+oACUTE+"n centralizada ha sido inactivada exitosamente.");
				        }
						window.location.reload();
					}else{
						alert("La operaci"+oACUTE+"n no pudo ser realizada.");
					}
		        }
			});
		}

		function activar(indice, tipo){
			if(tipo==TIPO_PROYECTO && confirm(pACUTE+"Est"+aACUTE+" seguro que desea activar el proyecto "+proyectos[indice][1]+"?")){
				accionActivar(TIPO_PROYECTO,indice,true);
			}else if(tipo==TIPO_ACCION_CENTRALIZADA && confirm(pACUTE+"Est"+aACUTE+" seguro que desea activar la acci"+oACUTE+"n centralizada "+accionesCentralizadas[indice][1]+"?")){
				accionActivar(TIPO_ACCION_CENTRALIZADA,indice,true);
			}
			return;
		}
		function inactivar(indice, tipo){
			if(tipo==TIPO_PROYECTO && confirm(pACUTE+"Est"+aACUTE+" seguro que desea inactivar el proyecto "+proyectos[indice][1]+"?")){
				accionActivar(TIPO_PROYECTO,indice);
			}else if(tipo==TIPO_ACCION_CENTRALIZADA && confirm(pACUTE+"Est"+aACUTE+" seguro que desea inactivar la acci"+oACUTE+"n centralizada "+accionesCentralizadas[indice][1]+"?")){
				accionActivar(TIPO_ACCION_CENTRALIZADA,indice);
			}
			return;
		}
	</script>
</head>
<body class="normal">
	<?php
	$estadoActivo = 1;
	$estadoInactivo = 2;
	$query = 	"SELECT ".
					"sp.proy_id as id, ".
					"sp.pre_anno as pres_anno, ".
					"sp.proy_titulo as titulo, ".
					"sp.esta_id, ".
					"se.esta_nombre, ".
					"em.empl_nombres || ' ' || em.empl_apellidos as elaborado ".
				"FROM ".
					"sai_empleado em, ".
					"sai_proyecto sp, ".
					"sai_estado se ".
				"WHERE ".
					"sp.usua_login = em.empl_cedula AND ".
					"sp.esta_id = se.esta_id ".
				"ORDER BY sp.pre_anno, sp.proy_id ";
	$resultado=pg_query($conexion,$query);
	$numeroFilas = pg_numrows($resultado);
	?>
	<table width="900px" border="0" align="center">
		<tr>
			<td height="27" class="normal peq_verde_bold">
				<div align="center">Proyectos</div>
			</td>
		</tr>
	</table>
	<table width="900px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray normalNegroNegrita">
			<td width="15%" align="center">C&oacute;digo</td>
			<td width="15%" align="center">A&ntilde;o presupuestario</td>
			<td width="25%" align="center">T&iacute;tulo</td>
			<td width="15%" align="center">Elaborado por</td>
			<td width="15%" align="center">Estado</td>
			<td width="15%" align="center"></td>
		</tr>
		<?
		if($numeroFilas>0){
			$i=0;
			while($row=pg_fetch_array($resultado)){
				$beneficiario_nombre= "";
				$beneficiario = "";
			?>
			<tr class="resultados">
				<td height="28" align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_PROYECTO?>');"><?= $row['id']?></a>
					</span>
				</td>
				<td align="center"><?= $row['pres_anno'];?></td>
				<td><?= $row['titulo'];?></td>
				<td align="center"><?= $row['elaborado'];?></td>
				<td align="center"><?= $row['esta_nombre'];?></td>
				<td align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_PROYECTO?>');">Ver Detalle</a>
					</span>
					<?php
					if($user_perfil_id==PERFIL_JEFE_PRESUPUESTO){
					?>
					<br/>
					<span class="link">
						<a href="javascript:modificar('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_PROYECTO?>');">Modificar</a>
					</span>
					<br/>
					<span class="link">
						<?php if($row['esta_id']!=$estadoInactivo){?>
							<a href="javascript:inactivar(<?= $i?>,<?= TIPO_IMPUTACION_PROYECTO?>);">Inactivar</a>
						<?php }?>
						<?php if($row['esta_id']==$estadoInactivo){?>
							<a href="javascript:activar(<?= $i?>,<?= TIPO_IMPUTACION_PROYECTO?>);">Activar</a>
						<?php }?>
					</span>
					<?php
					}
					?>
				</td>
			</tr>
			<script>
				var registro = new Array(4);
				registro[0]=<?= $i?>;
				registro[1]="<?= $row['titulo']?>";
				registro[2]=<?= $row['pres_anno']?>;
				registro[3]="<?= $row['id']?>";
				proyectos[proyectos.length] = registro;
			</script>
			<?php
				$i++;
			}
		}else{
			echo "<tr><td align='center' valign='middle' height='50' colspan='5'>No existen proyectos</td></tr>";
		}
	?>
	</table>
	<br/>
	<?php
	$query = 	"SELECT ".
					"sac.acce_id as id, ".
					"sac.pres_anno as pres_anno, ".
					"sac.acce_denom as titulo, ".
					"sac.esta_id, ".
					"se.esta_nombre, ".
					"em.empl_nombres || ' ' || em.empl_apellidos as elaborado ".
				"FROM ".
					"sai_empleado em, ".
					"sai_ac_central sac, ".
					"sai_estado se ".
				"WHERE ".
					"sac.usua_login = em.empl_cedula AND ".
					"sac.esta_id = se.esta_id ".
				"ORDER BY sac.pres_anno, sac.acce_id ";
	$resultado=pg_query($conexion,$query);
	$numeroFilas = pg_numrows($resultado);
	?>
	<table width="900px" border="0" align="center">
		<tr>
			<td height="27" class="normal peq_verde_bold">
				<div align="center">Acciones centralizadas</div>
			</td>
		</tr>
	</table>
	<table width="900px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray normalNegroNegrita">
			<td width="15%" align="center">C&oacute;digo</td>
			<td width="15%" align="center">A&ntilde;o presupuestario</td>
			<td width="25%" align="center">T&iacute;tulo</td>
			<td width="15%" align="center">Elaborado por</td>
			<td width="15%" align="center">Estado</td>
			<td width="15%" align="center"></td>
		</tr>
		<?
		if($numeroFilas>0){
			$i=0;
			while($row=pg_fetch_array($resultado)){
				$beneficiario_nombre= "";
				$beneficiario = "";
			?>
			<tr class="resultados">
				<td height="28" align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>');"><?= $row['id']?></a>
					</span>
				</td>
				<td align="center"><?= $row['pres_anno'];?></td>
				<td><?= $row['titulo'];?></td>
				<td align="center"><?= $row['elaborado'];?></td>
				<td align="center"><?= $row['esta_nombre'];?></td>
				<td align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>');">Ver Detalle</a>
					</span>
					<?php
					if($user_perfil_id==PERFIL_JEFE_PRESUPUESTO){
					?>
					<br/>
					<span class="link">
						<a href="javascript:modificar('<?= trim($row['id'])?>','<?= trim($row['pres_anno'])?>','<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>');">Modificar</a>
					</span>
					<br/>
					<span class="link">
						<?php if($row['esta_id']!=$estadoInactivo){?>
							<a href="javascript:inactivar(<?= $i?>,<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>);">Inactivar</a>
						<?php }?>
						<?php if($row['esta_id']==$estadoInactivo){?>
							<a href="javascript:activar(<?= $i?>,<?= TIPO_IMPUTACION_ACCION_CENTRALIZADA?>);">Activar</a>
						<?php }?>
					</span>
					<?php
					}
					?>
				</td>
			</tr>
			<script>
				var registro = new Array(4);
				registro[0]=<?= $i?>;
				registro[1]="<?= $row['titulo']?>";
				registro[2]=<?= $row['pres_anno']?>;
				registro[3]="<?= $row['id']?>";
				accionesCentralizadas[accionesCentralizadas.length] = registro;
			</script>
			<?php
				$i++;
			}
		}else{
			echo "<tr><td align='center' valign='middle' height='50' colspan='5'>No existen acciones centralizadas</td></tr>";
		}
	?>
	</table>
</body>
</html>
<?php pg_close($conexion); ?>