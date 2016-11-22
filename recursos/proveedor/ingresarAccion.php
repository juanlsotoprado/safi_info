<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Ingresar Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
/*echo "<pre>";
echo print_r($_REQUEST);
echo "</pre>";
*/
//Datos de provinientes de prov_1.php
$nombre= trim($_POST['txt_nombre']);
$rif= strtolower(trim($_POST['txt_rif']));
$tipo= trim($_POST['cmb_tipo']);
$idEstado= trim($_POST['estado']);
$tipo_pers= trim($_POST['cmb_tipo_pers']);  
$solvencia_laboral= trim($_POST['cmb_solvencia_laboral']);
$test= $_POST['test'];
$domicilio= trim($_POST['txt_domicilio']);
$deposito= trim($_POST['txt_deposito']);
$telefonos= trim($_POST['txt_telefonos']);
$fax= trim($_POST['txt_fax']);
$postal= trim($_POST['txt_postal']);
$email= trim($_POST['txt_email']);
$pagina_web= trim($_POST['txt_paginaweb']);
$marcado= trim($_POST['cmb_marcado']);
$tipo_contribuyente= trim($_POST['cmb_contribuyente']);
if($tipo_contribuyente==""){$tipo_contribuyente=0;}

$codigo_rnc= trim($_POST['txt_codigo_rnc']);
$fecha_inscripcion_rnc= trim($_POST['txt_fecha_inscripcion']);
if (strlen($fecha_inscripcion_rnc) >5) 
$fecha_inscripcion_rnc2=substr($fecha_inscripcion_rnc,6,4)."-".substr($fecha_inscripcion_rnc,3,2)."-".substr($fecha_inscripcion_rnc,0,2);
else
$fecha_inscripcion_rnc2=null;
//cambiando formato de fecha 	
$fecha_vencimiento_rnc= trim($_POST['txt_fecha_vencimiento']);
if (strlen($fecha_vencimiento_rnc) >5)
$fecha_vencimiento_rnc2=substr($fecha_vencimiento_rnc,6,4)."-".substr($fecha_vencimiento_rnc,3,2)."-".substr($fecha_vencimiento_rnc,0,2);
else 
$fecha_vencimiento_rnc2=null;
//cambiando formato de fecha RIF
$fecha_vencimiento_rif= trim($_POST['txt_fecha_vencimiento3']);
if(strlen($fecha_vencimiento_rif) >5)
{$fecha_vencimiento_rif2="'".substr($fecha_vencimiento_rif,6,4)."-".substr($fecha_vencimiento_rif,3,2)."-".substr($fecha_vencimiento_rif,0,2)."'";}

//cambiando formato de fecha Solvencia labora
$fecha_vencimiento_solab= trim($_POST['txt_fecha_vencimiento2']);
if (strlen($fecha_vencimiento_solab) >5)
{$fecha_vencimiento_solab2="'".substr($fecha_vencimiento_solab,6,4)."-".substr($fecha_vencimiento_solab,3,2)."-".substr($fecha_vencimiento_solab,0,2)."'";}

////////fin formatos de fechas
$actualizado_rnc= trim($_POST['cmb_actualizado_rnc']);
$suspendida_rnc= trim($_POST['cmb_suspendida_rnc']);    
$nivel_financiamiento= trim($_POST['cmb_nivel_financiamiento']);

$nombre_representante= trim($_POST['txt_nombre_representante']);
$ci_representante= trim($_POST['txt_ci_representante']);
$tel_representante= trim($_POST['txt_tel_representante']);
$email_representante= trim($_POST['txt_email_representante']);

$nombre_contacto= trim($_POST['txt_nombre_contacto']);
$tel_contacto= trim($_POST['txt_tel_contacto']);
$email_contacto= trim($_POST['txt_email_contacto']);

$usua_login=$_SESSION['login'];
$comentario=$_POST['txt_comentario'];
$ramo_secundario=$_POST['ramo_secundario']; //ramo_secundario

/*Estado*/
if ( $idEstado != null && $idEstado != "" ) {
	$sql= "Select nombre from safi_edos_venezuela where id=".$idEstado."";
	$resultado_set = pg_exec($conexion ,$sql);
	if  ($row = pg_fetch_array($resultado_set) ) $nombreEstado=$row["nombre"];
} else {
	$nombreEstado = "";
}
	
/*Tipo de proveedor*/	
$sql= "Select * from sai_tipo_proveedor where prtp_id='".$tipo."'";
$resultado_set = pg_exec($conexion ,$sql);
if  ($row = pg_fetch_array($resultado_set) ) $nombre_tp=$row["prtp_nombre"];

/*Tipo de contribuyente*/	
$sql= "Select * from sai_provee_tc where id_tc='".$tipo_contribuyente."'";
$resultado_set = pg_exec($conexion ,$sql);
if  ($row = pg_fetch_array($resultado_set) ) $nombre_tc=$row["descripcion"];

/*Tipo de persona*/	
$sql= "Select * from sai_provee_tpers where ptpers_id='".$tipo_pers."'";
$resultado_set = pg_exec($conexion ,$sql);
if  ($row = pg_fetch_array($resultado_set) ) $nombre_tpers=$row["nombre"];

$sw = false; 
// Verificacion del que el rif del proveedor no exista 
$sql= "Select * from sai_any_tabla ('sai_proveedor_nuevo','prov_id_rif','lower(trim(prov_id_rif)) = ''". $rif ."''') resultado_set (rif varchar)";
$resultado_set = pg_exec($conexion ,$sql);
while($row = pg_fetch_array($resultado_set) ){ $sw = true; }
$error="";
if($sw){ 
	$error="Proveedor no incluido, el RIF ya esta registrado";
}
if (!$sw){
	$sql  = "select * from  sai_insert_proveedor_nuevo('". $rif ."', '";
	$sql .= $nombre ."' , ". $tipo .",'". $solvencia_laboral."','". $nivel_financiamiento."','". $domicilio ."','". $deposito. "' , '" . $telefonos."','". $fax ."','";
	$sql .= $postal . "','" . $email."','". $pagina_web."','". $marcado ."'," . $tipo_contribuyente.",'";
	$sql .= $codigo_rnc."','".$fecha_inscripcion_rnc2."','".$fecha_vencimiento_rnc2."','". $actualizado_rnc."','";
	$sql .= $nombre_representante."','".$ci_representante ."','".$tel_representante."','". $email_representante."','";
	$sql .= $nombre_contacto . "', '". $tel_contacto ."', '". $email_contacto . "','". $usua_login ."','". $comentario."','".$tipo_pers."','".$suspendida_rnc."',".(($idEstado!=null && $idEstado!="")?$idEstado:"null").") As resultado_set(varchar)";
	$resultado_set = pg_exec($conexion ,$sql);
	$row = pg_fetch_array($resultado_set,0);
	if ($row[0] <> null) { 
		$codigo=$row[0];
		if($fecha_vencimiento_rif2==""){$fecha_vencimiento_rif2 = "null";}if($fecha_vencimiento_solab2==""){$fecha_vencimiento_solab2 = "null";}
		$fechasquery = "UPDATE sai_proveedor_nuevo SET fecha_venc_rif = ".$fecha_vencimiento_rif2." , fecha_venc_solab = ".$fecha_vencimiento_solab2.", temporal = ".$_POST['temporal']." WHERE prov_codigo = ".$codigo."";
		$resul = pg_exec($conexion ,$fechasquery);
		?>
		<br/>
		<br/>
	    <table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="16" colspan="4" class="normalNegroNegrita">PROVEEDOR REGISTRADO </td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita"> C&oacute;digo: </td>
				<td colspan="3"><?=$codigo?></td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita" width="25%"> RIF: </td>
				<td width="25%"><?=$rif?></td>
				<td class="normalNegrita" width="25%"> Fecha vencimiento(Rif):</td>
				<td width="25%"><?=$fecha_vencimiento_rif?></td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita" width="25%"> Raz&oacute;n social o Nombre:</td>
				<td width="25%"><?=$nombre?></td>
				<td class="normalNegrita"> Tipo constribuyente: </td>
				<td ><?=$nombre_tc?></td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita"> Solvencia laboral: </td>
				<td><?=$solvencia_laboral?></td>
				<td class="normalNegrita">Fecha vencimiento(Solvencia Laboral): </td>
				<td ><?=$fecha_vencimiento_solab?></td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita"> Tipo persona: </td>
				<td ><?=$nombre_tpers?></td>
				<td class="normalNegrita"> Estado: </td>
				<td ><?=$nombreEstado?></td>
			</tr>
			<tr class="normal"> 
				<td class="normalNegrita">Ramo(s): </td>
				<td >
				<?php
				if ($ramo_secundario){
					foreach ($ramo_secundario as $ramo_s){ 
						$sql_partida="select part_nombre from sai_partida where part_id='".$ramo_s."'";
						$resultado_partida = pg_exec($conexion ,$sql_partida);
						if  ($row_partida = pg_fetch_array($resultado_partida) )
						echo $ramo_s.": ".$row_partida[0].";<br/> ";
					}
				}
				?>
				</td>
				<td class="normalNegrita"> Tipo de Proveedor: </td>
				<td><?=$nombre_tp?></td>
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
				<td><?=$nivel_financiamiento?></td>
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
				<td class="normal" colspan="3"> <?=$comentario?> </td>
			</tr>
			<tr>
				<td height="40" colspan="4" align="center" class="normal">
					<br/>
					Registro generado el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
					<br/>
					<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
					<br/>
				</td>
			</tr>
		</table>
		<?php
		if ($ramo_secundario){
			foreach ($ramo_secundario as $ramo_s){
				$sql= "insert into sai_prov_ramo_secundario (id_ramo, prov_id_rif) values ('".$ramo_s."', '".$rif."')";
				$resultado_set = pg_exec($conexion ,$sql);
			}
		}
	}
}else{
		?>
		<table width="500" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="normal"> 
				<td height="15" colspan="3">
					<div align="left" class="normalNegroNegrita">
						<strong>REGISTRO PROVEEDOR </strong>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="normal">
					<br/>
					<div align="center">
						<img src="../../imagenes/vineta_azul.gif" width="11" height="7"/>
						Ha ocurrido un error al ingresar los datos 
						<br/>
							<?= (pg_errormessage($conexion)); ?>
						<br/>
						<?= $error;?>
						<br/><br/>
						<img src="../../imagenes/mano_bad.gif" width="31" height="38"/>
						<br/><br/>
						<br/><br/>
					</div>
				</td>
			</tr>
		</table>
<?php
   }
?>
</body>
</html>