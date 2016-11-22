<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$prov_rif=trim($_GET['codigo']); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Detalle proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form name="form" method="post" action="">
<?php
$sql_p="SELECT *, to_char(prov_rnc_fecha_inscripcion, 'DD/MM/YYYY') as fechai,to_char(prov_rnc_fecha_vencimiento, 'DD/MM/YYYY') as fechav FROM sai_proveedor_nuevo where prov_id_rif like '%".$prov_rif."%'"; 
$resultado_set_most_p=pg_query($conexion,$sql_p);
if($row=pg_fetch_array($resultado_set_most_p)){
	$codigo= trim($row['prov_codigo']);
	$nombre= trim($row['prov_nombre']);
	$rif= trim($row['prov_id_rif']);
	$tipo= trim($row['prov_prtp_id']);  
	$tipo_pers= trim($row['prov_id_tp']);  
	$solvencia_laboral= trim($row['prov_solvencia_laboral']);
	$nivel_financiamiento= trim($row['prov_rnc_nivel_financ']);
	$domicilio= trim($row['prov_domicilio']);
	$deposito= trim($row['prov_deposito']);
	$telefonos= trim($row['prov_telefonos']);
	$fax= trim($row['prov_fax']);
	$postal= trim($row['prov_codi_post']);
	$email= trim($row['prov_email']);
	$pagina_web= trim($row['prov_web']);
	$marcado= trim($row['prov_marcado']);
	$tipo_contribuyente= trim($row['prov_id_tc']);
	
	$codigo_rnc= trim($row['prov_rnc_id']);
	$fecha_inscripcion_rnc= trim($row['fechai']);
	$fecha_vencimiento_rnc= trim($row['fechav']);
	$actualizado_rnc= trim($row['prov_rnc_actualizacion']);
	$suspendida_rnc= trim($row['prov_rnc_suspendida']);
	
	$nombre_representante= trim($row['prov_nombre_rl']);
	$ci_representante= trim($row['prov_ci_rl']);
	$tel_representante= trim($row['prov_tel_rl']);
	$email_representante= trim($row['prov_email_rl']);
	
	$nombre_contacto= trim($row['prov_nombre_c']);
	$tel_contacto= trim($row['prov_tel_c']);
	$email_contacto= trim($row['prov_email_c']);
	
	$usua_login=$row['usua_login'];
	$comentario=$row['prov_observaciones'];
	$esta_id = $row['prov_esta_id'];
	
	/*Tipo de proveedor*/	
	$sql= "Select * from sai_tipo_proveedor where prtp_id='".$tipo."'";
	$resultado_set = pg_exec($conexion ,$sql);
	if($row = pg_fetch_array($resultado_set) ) $nombre_tp=$row["prtp_nombre"];
	
	/*Tipo de contribuyente*/	
	$sql= "Select * from sai_provee_tc where id_tc='".$tipo_contribuyente."'";
	$resultado_set = pg_exec($conexion ,$sql);
	if($row = pg_fetch_array($resultado_set) ) $nombre_tc=$row["descripcion"];
	
	/*Tipo de persona*/	
	$sql= "Select * from sai_provee_tpers where ptpers_id='".$tipo_pers."'";
	$resultado_set = pg_exec($conexion ,$sql);
	if($row = pg_fetch_array($resultado_set) ) $nombre_tpers=$row["nombre"];

	?>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td height="16" colspan="4" class="normalNegroNegrita">DETALLE PROVEEDOR</td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> C&oacute;digo: </td>
			<td colspan="3"><?=$codigo?></td>
		</tr>
			<tr class="normal"> 
			<td class="normalNegrita" width="25%"> RIF: </td>
			<td width="25%"><?=$rif?></td>
			<td class="normalNegrita" width="25%"> Raz&oacute;n social o Nombre:</td>
			<td width="25%"><?=$nombre?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Tipo constribuyente: </td>
			<td><?=$nombre_tc?></td>
			<td class="normalNegrita"> Tipo persona: </td>
			<td><?=$nombre_tpers?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Tipo de Proveedor: </td>
			<td><?=$nombre_tp?></td>
			<td class="normalNegrita"> Solvencia laboral: </td>
			<td><?=$solvencia_laboral?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Ramo(s): </td>
			<td colspan="3">
			<?php
			$id_ramo="";
			$sql="select * from sai_prov_ramo_secundario where prov_id_rif='".$rif."'"; 
			$resultado=pg_query($conexion,$sql) or die("Error al mostrar los ramos del proveedor");
			while($row=pg_fetch_array($resultado)){ 
				$id_ramo=trim($row['id_ramo']);
				$sql_partida="select part_nombre from sai_partida where part_id='".$id_ramo."'";
				$resultado_partida = pg_exec($conexion ,$sql_partida);
				if ($row_partida = pg_fetch_array($resultado_partida)) echo $id_ramo.": ".$row_partida[0].";<br/> ";
			} 
			?>
			</td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Domicilio fiscal: </td>
		 	<td><?=$domicilio?></td>
			<td class="normalNegrita"> Direcci&oacute;n de dep&oacute;sito: </td>
		 	<td><?=$deposito?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Tel&eacute;fonos: </td>
		 	<td><?=$telefonos?></td>
			<td class="normalNegrita"> Fax: </td>
		 	<td><?=$fax?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> C&oacute;digo postal: </td>
		 	<td><?=$postal?></td>
			<td class="normalNegrita"> Correo electr&oacute;nico: </td>
		 	<td><?=$email?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> P&aacute;gina Web: </td>
		 	<td><?=$pagina_web?></td>
			<td class="normalNegrita"> Marcado: </td>
		 	<td><?=$marcado?></td>
		</tr>
		<tr class="normalNegrita"><td height="40" colspan="4">Registro Nacional de Contratista (R.N.C)</td></tr>
		<tr class="normal"> 
			<td class="normalNegrita"> C&oacute;digo: </td>
		 	<td><?=$codigo_rnc?></td>
	 		<td class="normalNegrita"> Nivel de financiamiento: </td>
		 	<td><?=($nivel_financiamiento!="0")?$nivel_financiamiento:""?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Fecha inscripci&oacute;n: </td>
		 	<td><?=$fecha_inscripcion_rnc?></td>
			<td class="normalNegrita"> Fecha vencimiento: </td>
		 	<td><?=$fecha_vencimiento_rnc?></td>
		</tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Actualizaci&oacute;n: </td>
		 	<td><?=$actualizado_rnc?></td>
			<td class="normalNegrita"> Suspendida: </td>
		 	<td><?=$suspendida_rnc?></td>
 		</tr>
		<tr class="normalNegrita"><td height="40" colspan="4">Representante(s) legal(es) y Contacto(s)</td></tr>
		<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los RL: </td>
		 	<td><?=$nombre_representante?></td>
			<td class="normalNegrita"> CI de los RL: </td>
		 	<td><?=$ci_representante?></td>
		</tr>
	 	<tr class="normal"> 
			<td class="normalNegrita"> Tel&eacute;fonos de los RL: </td>
		 	<td><?=$tel_representante?></td>
			<td class="normalNegrita"> Correo electr&oacute;nico de los RL: </td>
		 	<td><?=$email_representante?></td>
		</tr>
	 	<tr class="normal"> 
			<td class="normalNegrita"> Nombre de los C: </td>
		 	<td><?=$nombre_contacto?></td>
			<td class="normalNegrita"> Tel&eacute;fonos de los C: </td>
		 	<td><?=$tel_contacto?></td>
		</tr>
	 	<tr class="normal"> 
			<td class="normalNegrita"> Correo electr&oacute;nico de los C: </td>
		 	<td colspan="3"><?=$email_contacto?></td>
		</tr>
		<tr class="normalNegrita">
			<td height="40">Comentarios/Observaciones:</td>
		 	<td colspan="3" class="normal"><?=$comentario?></td>
		</tr>
		<tr>
			<td height="40" colspan="4" align="center" class="normal">
				<br/>
				Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
				<br/>
				<br/>	
				<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>
			</td>
		</tr>
		</table>
		<?php
	}else{
        ?>
	    <table width="500" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray"> 
				<td height="15" colspan="3">
					<div align="left" class="normalNegroNegrita">
						<strong>ADMINISTRAR PROVEEDOR</strong>
					</div>
				</td>
			</tr>
		<tr>
			<td colspan="4" class="normal"><br/>
				<div align="center">
					<img src="../../imagenes/vineta_azul.gif" width="11" height="7"/>
					Ha ocurrido al consultar detalles
					<br/>
					<?= (pg_errormessage($conexion)); ?>
					<br/>
					<img src="../../imagenes/mano_bad.gif" width="31" height="38"/>
					<br/><br/>
	    		</div>
			</td>
		</tr>
	</table>
	<?php
	}
	?>
</form>
</body>
</html>