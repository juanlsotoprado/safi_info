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
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css"
	media="all" />
<title>.:SAFI: Anular Transferencia</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript" src="../../js/func_montletra.js"> </script>
<script language="JavaScript" type="text/JavaScript">

function anular() {
	document.form.action="anularAccion.php";
	document.form.submit();
}
</script>
</head>
<body>
<form name="form" method="post" action="anularTran.php">
<table width="40%" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" align="center">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">Anular Transferencia</td>
	</tr>
	<tr>
		<td class="normalNegrita">N&uacute;mero de transferencia:</td>
		<td class="normal">
		<input type="text" class="normal"  value="tran-" name="numeroTransferencia" id="numeroTransferencia"></input>		
		</td>
	</tr>
    <tr align="center">
		<td colspan="2" class="normal">
		<div align="center"><br></br><input type="submit" value="Buscar"/></div>
		</td>
	</tr>	
</table>
<br></br>
		<div class="normalNegrita">
		Nota: <br />
		La anulaci&oacute;n de una transferencia anula definitivamente la
		solicitud de pago (sopg) y la transferencia como tal (tran). Adicionalmente se
		anulan los movimientos contables y presupuestarios generados.</div>
		
	</div>
<?php 
if (strlen($_POST["numeroTransferencia"])>10) {
	$sql= "SELECT
			t.trans_id AS trans_id,			
			t.docg_id AS sopg_id,
			t.nro_referencia AS nro_referencia,
			t.nro_cuenta_emisor AS nro_cuenta,
			t.beneficiario AS beneficiario,
			t.rif_ci AS rif_ci,
			t.trans_monto AS monto,
			t.trans_asunto AS asunto,
			TO_CHAR(t.trans_fecha, 'DD/MM/YYYY') AS fecha
			FROM
				sai_pago_transferencia t
			WHERE 
				t.esta_id<>15 
				AND t.esta_id<>2 
				AND t.trans_id NOT IN (SELECT docg_id FROM sai_ctabanco_saldo WHERE docg_id LIKE '%tran%')
				AND t.trans_id='".$_POST["numeroTransferencia"]."'";	
	$resultado=pg_query($conexion,$sql) or die("Error al consultar la transferencia");
	?>
		<br></br>
			 <table width="70%" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" align="center">
			   <tr class="td_gray">
			   <td class="normalNegroNegrita">Detalle de la transferencia</td>
			<?php 
			if($row=pg_fetch_array($resultado)) {
			?>
				<tr class="normalNegroNegrita">
				<td>Nro. Tran: <input type="text" class="normal" id="tran_id" name="tran_id"  value="<?php echo $row['trans_id'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Nro. sopg: <input type="text" class="normal"  id="sopg_id" name="sopg_id" value="<?php echo $row['sopg_id'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita"> 
				<td> Fecha: <input type="text" class="normal" id="fecha" name="fecha" value="<?php echo $row['fecha'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Nro. Referencia: <input type="text" class="normal" id="nro_referencia" name="nro_referencia" value="<?php echo $row['nro_referencia'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Nro. Cuenta: <input type="text" class="normal" id="nro_cuenta" name="nro_cuenta" size="60" value="<?php echo $row['nro_cuenta'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Beneficiario: <input type="text" class="normal" id="beneficiario" name="beneficiario" size="60" value="<?php echo $row['rif_ci'].":  ".$row['beneficiario'];?>" readonly></td>
				<input type="hidden" class="normal" id="beneficiarioId" name="beneficiarioId" value="<?php echo $row['rif_ci'];?>">
				<input type="hidden" class="normal" id="beneficiarioNombre" name="beneficiarioNombre" value="<?php echo $row['beneficiario']?>">
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Monto: <input type="text" class="normal" id="monto" name="monto" value="<?php echo $row['monto'];?>" readonly></td>
				</tr>
				<tr class="normalNegroNegrita">				
				<td>Asunto: <input type="text" class="normal" id="asunto" name="asunto" size="60" value="<?php echo $row['asunto'];?>" readonly></td>			
				</tr>
				<tr class="normalNegroNegrita">	
				<td>Motivo anulaci&oacute;n:
				<select name="motivo" id="motivo" class="normal">
				<option value="AT-Partida">AT - Error en partida</option>
				<option value="AT-Monto">AT - Error en monto</option>
				<option value="AT-Beneficiario">AT - Error en proveedor o
				beneficiario</option>
				<option value="AT-Cuenta">AT - Error en cuentas bancarias</option>
				<option value="AT-Banco">AT - Error bancario</option>
				<option value="AT-Otro">AT - Otro</option>
				</select></td>	
				</tr>	
				<tr align="center">
				<td colspan="2" class="normal">
				<div align="center"><br></br><input type="button" value="Anular" onClick="javascript:anular();"/></div>
				</td>
				</tr>
		<?php }
		else {?>
			<tr>
			<td class="normalNegroNegrita">Ese n&uacute;mero de transferencia: (1) no se encuentra registrada, (2) ya fue anulada o (3) ya fue conciliada.</td>
			</tr>
		<?php }
		?>
		</table>
<?php 
}
?>
</form>
</body>
</html>
