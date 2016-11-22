<?php 
ob_start();
session_start();
require_once("../../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Chequera</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function regresar() {
	history.back();
}

function detalle(codigo, banco, cuenta) {
    window.location="detalleChequera.php?codigo="+codigo+"&banco="+banco+"&cuenta="+cuenta;
}

function actinact(codigo, cuenta, estatus) {
if(confirm("Est\u00e1 seguro que desea cambiar el estado de la chequera?")) 
    window.location="desactivarChequera.php?codigo="+codigo+"&cuenta="+cuenta+"&estatus="+estatus;
}

function activar(codigo, cuenta, estatus) {
    window.location="activarChequera.php?codigo="+codigo+"&cuenta="+cuenta+"&estatus="+estatus;
}
</script>
</head>
<body>
<form name="form4" method="post" action="">
<?php 
$banco = $_GET['banco'];
$tipo = $_GET['tipo'];
$ano = $_GET['ano'];

$codigo=trim($_GET["codigo"]);  
$sql="SELECT cq.nro_chequera,
		cq.cheq_cantidad,
		CASE WHEN (cq.cheq_cantidad::integer > 1) 
		THEN (
				SELECT COUNT(nro_cheque)
				FROM sai_cheque
				WHERE estatus_cheque<>1 
				AND nro_chequera = cq.nro_chequera
			) END AS cheque_emitido,
		cq.banc_id,
		cq.cheq_activa,
		cq.cheq_activa,
		cq.cheq_activa,
		cq.ctab_numero,
		b.banc_nombre,
		e.esta_nombre
    FROM sai_chequera cq,
		sai_banco b,
		sai_estado e
	WHERE cq.cheq_activa = e.esta_id
	AND cq.banc_id = b.banc_id
	AND cq.ctab_numero='".$codigo."'";
$resultado=pg_query($conexion,$sql) or die("Error al realizar la consulta");
$nroFilas = pg_num_rows($resultado);
if (($nroFilas<=0) && (($_GET['codigo']!=0))) {
	echo "<br><div align='center' class='normalNegrita'>"."Actualmente no existen chequeras asociadas a la cuenta ".$_GET['codigo']."</div></br>"; ?>
 <div align="center">
 <input class="normalNegro" type="button" value="Crear" onclick="javascript:location.href='ingresarChequera.php?codigo=<?=$codigo?>';"/>
 </div>
<?php
}
else
    if ($nroFilas>0) {	
?>
			<br />
			<div align="center" class="normalNegroNegrita">Chequeras de la cuenta <?php echo $tipo;?> nro:<?=$codigo;?> del <?php echo $banco;?></div>
			<br></br>
			<table width="70%" align="center" background="../../../imagenes/fondo_tabla.gif" bgcolor="#FFFFFF" class="tablaalertas" >
			  <tr align="center" class="td_gray">
			  <td class="normalNegroNegrita">Nro. Chequera</td>
			  <td class="normalNegroNegrita">Nro. Total cheques</td>
			  <td class="normalNegroNegrita">Nro. Cheques emitidos</td>
			   <td class="normalNegroNegrita">Nro. Cheques disponibles</td>			  
			  <td class="normalNegroNegrita">Estado</td>
			  <td class="normalNegroNegrita">Opciones</td>
			  </tr>
				<?php
  				while($row=pg_fetch_array($resultado)) {   
				    $cantidad=trim($row['cheq_cantidad']);
					$chequera=$row['nro_chequera'];
					$estado=$row['esta_nombre'];
					$esta_id=$row['cheq_activa'];					
					$banco=$row['banc_nombre'];
					$cuenta=$row['ctab_numero'];
					$cheque_emitido=$row['cheque_emitido'];
					$resta=$cantidad-$cheque_emitido;
			 		?>
					<tr class="normal"> 
  					<td align="center"><?=$chequera;?></td>
  					<td align="center"><?=$cantidad;?></td>
  					<td align="center"><?=$cheque_emitido;?></td>
  					<td align="center"><?=$resta;?></td>
					<td align="center"><?=$estado;?></td>
  					<td><br/>
			    	<a href="javascript:detalle('<?=$chequera?>,<?=$banco?>,<?=$cuenta?> ')" class="copyright"> Ver Detalle</a>	
					
					
			 		<?if ($resta>0) {?>
					<br/>
					
					<a href="javascript:actinact('<?=$chequera?>','<?=$cuenta?>', '<?=$esta_id?>')" class="copyright">Activar/Inactivar</a>
					<?}?>
					</td>
					</tr>
			<?php } ?>  
<tr>
  <td colspan="6"><div align="center">
<input class="normalNegro" type="button" value="Crear" onclick="javascript:location.href='ingresarChequera.php?codigo=<?=$codigo?>';"/>
 <input class="normalNegro" type="button" value="Regresar" onclick="javascript:regresar();"/>
</div></td> 
</tr>
  </table>
   <?php } //fin del else de que si se existen registros dada la condicion?>
<br />
<? pg_close($conexion);?>
</form>
</body>
</html>