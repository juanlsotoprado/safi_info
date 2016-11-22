<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI:Ejecutar registro de  cuenta patrimonial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
</head>
<body>
<?php
$nivel=trim($_POST['txt_nivel']);
$nombre=trim($_POST['txt_nombre']);
$saldo=str_replace(',','.',trim($_POST['txt_saldo']));
$grupo=trim($_POST['txt_grupo']);
$subgrupo=trim($_POST['txt_subgrupo']);
$rubro=trim($_POST['txt_rubro']);
$usua_login=$_SESSION['login'];
$esta_id=1; //Estado del Recurso: Activo
$cpat_id='';
$fecha=date("Y").'/'.date("m").'/'.date("d");
if (strcmp($nivel, '1') ==0) $detalle=1;
else $detalle=0;  
 if (strcmp($nivel, '1') ==0) $cpat_id=trim($_REQUEST['txt_nivel1_1']).trim($_REQUEST['txt_nivel1_2']);
 if (strcmp($nivel, '2') ==0) $cpat_id=trim($_REQUEST['slc_nivel2_1']).trim($_REQUEST['txt_nivel2_1']).trim($_REQUEST['txt_nivel2_2']);
 if (strcmp($nivel, '3') ==0) $cpat_id=trim($_REQUEST['slc_nivel3_1']).trim($_REQUEST['txt_nivel3_1']).trim($_REQUEST['txt_nivel3_2']);
 if (strcmp($nivel, '4') ==0) $cpat_id=trim($_REQUEST['slc_nivel4_1']).trim($_REQUEST['txt_nivel4_1']).trim($_REQUEST['txt_nivel4_2']);
 if (strcmp($nivel, '5') ==0) $cpat_id=trim($_REQUEST['slc_nivel5_1']).trim($_REQUEST['txt_nivel5_1']).trim($_REQUEST['txt_nivel5_2']);
 if (strcmp($nivel, '6') ==0) $cpat_id=trim($_REQUEST['slc_nivel6_1']).trim($_REQUEST['txt_nivel6_1']).trim($_REQUEST['txt_nivel6_2']);
 if (strcmp($nivel, '7') ==0) $cpat_id=trim($_REQUEST['slc_nivel7_1']).trim($_REQUEST['txt_nivel7_1']); 

$valido=1;
$sql= "select cpat_id from sai_cue_pat where cpat_id='".$cpat_id."'";
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
if ($row=pg_fetch_array($resultado)) $valido=0;
if ($valido=='0')	{
?>
<script language="javascript">
		alert("La cuenta patrimonial que introdujo ya se encuentra registrada");
		location.href="ingresarCuenta.php";
</script>	
<? } 
else {  
		$query = "INSERT INTO sai_cue_pat (
								cpat_id, 
								cpat_nombre, 
								cpat_madre, 
								cpat_detalle, 
								cpat_fec_gene, 
								cpat_sal_ini, 
								usua_login, 
								esta_id, 
								cpat_nivel, 
								cpat_grupo, 
								cpat_sub_grupo, 
								cpat_rubro)
 				VALUES ('".$cpat_id."', 
						'".$nombre."', 
						'0', 
						'".$detalle."',
						'".$fecha."', 
						".$saldo.",
						'".$usua_login."',
						".$esta_id.", 
						".$nivel.",
						'".$grupo."', 
						'".$subgrupo."', 
						'".$rubro."')";
		if($resultado_reg=pg_query($conexion,$query)) {
			$query = "
				SELECT
					MAX(saldo_contable_mes.mes) AS mes,
					saldo_contable_mes.ano AS ano
				FROM
					safi_saldo_contable saldo_contable_mes
					INNER JOIN
					(
						SELECT
							MAX(ano) AS maximo_ano
						FROM
							safi_saldo_contable
					) as saldo_contable_ano ON (saldo_contable_ano.maximo_ano = saldo_contable_mes.ano)
				GROUP BY
					saldo_contable_mes.ano
			";
			
			$resultado = pg_exec($conexion, $query);
			while($row=pg_fetch_array($resultado)) {
				$mes = $row["mes"];
				$ano = $row["ano"];
			}
			$query = "INSERT INTO safi_saldo_contable (
									cpat_id,
									mes,
									ano,
									saldo
									)
	 					VALUES ('".$cpat_id."',".
								$mes.",".
								$ano.",
								".$saldo.")";
			if($resultado_reg=pg_query($conexion,$query)) {
?>
	 <table width="50%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
	  <td colspan="2" class="normalNegroNegrita">CUENTA PATRIMONIAL REGISTRADA </span></td>
	</tr>
	<tr>
	<td class="normalNegrita">C&oacute;digo:</td>
	<td class="normal"><?php echo $cpat_id; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Nombre:</td>
	<td class="normal"><?php echo $nombre; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Saldo inicial:</td>
	<td class="normal"><?php echo $saldo; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Grupo:</td>
	<td class="normal"><?php echo $grupo; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">SubGrupo:</td>
	<td class="normal"><?php echo $subgrupo; ?></td>
	</tr>
	<tr>
	<td class="normalNegrita">Rubro:</div></td>
	<td class="normal"><?php echo $rubro;?></td>
	</tr>
	<tr>
	<td colspan="2" align="center" class="normal">
	<br>
	Solicitud generada el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br><br>
	<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
	</td>
	</tr>
	</table>
<?php }
else {  ?>
		<table width="60%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td class="normalNegroNegrita">INGRESAR CUENTA PATRIMONIAL</td>
		</tr>
		<tr>
		<td class="normal" align="center">
		<img src="../../imagenes/vineta_azul.gif" width="11" height="7">
		Ha ocurrido un error al ingresar los datos 
		<br/>
		<img src="../../imagenes/mano_bad.gif" width="31" height="38">
		</td>
		</tr>
		</table>
	 <?php }}} pg_close($conexion); ?>
</body>
</html>