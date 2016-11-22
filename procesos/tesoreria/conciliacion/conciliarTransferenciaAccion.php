<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Conciliaci&oacute;n Bancaria</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
</head>

<body>
<br />
<div align="center">
<?php 
$fecha = $_POST['fecha'];
$cuenta = $_POST['hid_cuenta'];
$error = 0;
$codigos = "";
if (isset($_POST['solicitud'])) {
	$cod = $_POST["solicitud"];
}
if (count($cod)>0) {
	for ($x=0; $x<count($cod); $x++) {
		$codigo = $cod[$x];
		$error=0;
		
		//Verificar la operación no haya sido conciliada
		$query =  "SELECT docg_id
				  FROM sai_ctabanco_saldo
			 	  WHERE docg_id='".$codigo."'
				 	AND ctab_numero = '".$cuenta."'";

		$resultado = pg_query($conexion,$query);
		$numero = pg_num_rows($resultado);
		
		if ($numero < 1) { // La operación no ha sido conciliada

			//Verificar que el pagado no se haya realizado en fecha posterior a la que se desea hacer la reservación
			$query = "SELECT pt.docg_id AS docg_id, 
						TO_CHAR(mb.fechaemision_cheque, 'DD/MM/YYYY') AS fecha, 
						pt.trans_id AS trans_id, 
						pt.nro_referencia, 
						pt.trans_monto AS monto,
						CASE WHEN (
								(TO_CHAR(mb.fechaemision_cheque, 'MM/YYYY') != substr('".$fecha."', 4)
								AND (TO_DATE(TO_CHAR(mb.fechaemision_cheque, 'DD/MM/YYYY'), 'DD/MM/YYYY') < TO_DATE('".$fecha."', 'DD/MM/YYYY')))
				 			OR
				 				TO_CHAR(mb.fechaemision_cheque, 'MM/YYYY') = substr('".$fecha."', 4)
				
			 	  		)
		
				 	  		THEN 0
							ELSE 1
						END AS fecha_mayor,		
						nro_cuenta_emisor AS numero_cuenta
					FROM sai_pago_transferencia pt
					INNER JOIN sai_mov_cta_banco mb ON (mb.docg_id = pt.docg_id)
					WHERE pt.esta_id != 15 AND
						mb.conciliado = 51 AND
						pt.nro_referencia = mb.nro_cheque AND
						pt.trans_id = '".$codigo."'";
		
			
			$resultado=pg_query($conexion,$query);
			if ($row = pg_fetch_array($resultado)) {
				$numero_cuenta = trim($row['numero_cuenta']);
				$monto = trim($row['monto']);
				$docg_id = trim($row['docg_id']);
				$fecha_mayor = $row['fecha_mayor'];
			} 
					
			if ($fecha_mayor == 0) { // La fecha del pagado es menor a la conciliación
				/*Se concatenan los códigos para mostrarlo en el detalle*/
				if ($codigos == "") 
					$codigos = $codigo;
				else 
					$codigos = $codigos . ", ". $codigo;
				
				/*Cambiar a 50 mov_cta_banco*/
				$query = "UPDATE
							sai_mov_cta_banco
						  SET conciliado = 50, 
							fecha_descon = TO_DATE('".$fecha."', 'DD/MM/YYYY')
						  WHERE docg_id = '".$docg_id."'
				 			AND ctab_numero = '".$numero_cuenta."'";
				
				$resultado=pg_query($conexion,$query) or die("No se actualizaron movimientos conciliados en banco");
				
				$query = "INSERT INTO 
							sai_ctabanco_saldo(ctab_numero, 
												monto_haber, 
												monto_debe, 
												fecha_saldo, 
												docg_id)
						 VALUES ('".$numero_cuenta."', 
									0,
								 ".$monto.", 
								TO_DATE('".$fecha."', 'DD/MM/YYYY'),
								'".$codigo."')";

				$resultado =pg_query($conexion,$query) or die("No se actualizaron movimientos conciliados en banco");
			}	
			else {
				$error=1;
				echo ("<div class='normalNegrita' align='center'>La fecha del pagado de ese documento es mayor a la fecha que ha seleccionado para el: ".$codigo. "</div>");
			}
		}
		else {
			$error=1;
			echo ("<div class='normalNegrita' align='center'>Ya el documento: ".$codigo. " fue conciliado</div>");
		}
	} //end for
if ($error==0)
	echo "<div class='normalNegrita' align='center'>Se proces&oacute; satisfactoriamente la conciliaci&oacute;n bancaria de los siguientes documentos: ".$codigos;
}
else echo "<div class='normalNegrita' align='center'>Error: Debe seleccionar al menos una transferencia para conciliar";
?>
</div>
</div>
</body>
</html>
<?php pg_close($conexion);?>