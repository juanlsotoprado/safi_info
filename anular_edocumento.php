<?php 
   ob_start();
   session_start();
   require_once("includes/conexion.php");
	 
   if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
   {
     header('Location:index.php',false);
	 ob_end_flush(); 
	 exit;
   }
	
	ob_end_flush(); 
	
	$user_login = $_SESSION['login'] ;	
	$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);
	
	$usuario = $_SESSION['login'];
	$user_perfil_id = $_SESSION['user_perfil_id'];
	
	//Buscar nombre del tipo de documento
	$request_id_tipo_documento = "";
	if (isset($_REQUEST["tipo"])) {
		$request_id_tipo_documento = $_REQUEST["tipo"];	
	}
	$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$nombre_documento = $row["resultado"];
	}	
	
	$request_codigo_documento = "";
	//C�digo del documento
	if (isset($_REQUEST["id"])) {
		$request_codigo_documento = $_REQUEST["id"];	
	}	
	$codigo = $request_codigo_documento;
	
	$anular = 1; //Indica si (1) se puede anular o no (0) el doc
	
	//Si es orden de compra u orden de servicios 
	if (($request_id_tipo_documento=="ordc") || ($request_id_tipo_documento=="modc") || ($request_id_tipo_documento=="ords") || ($request_id_tipo_documento=="mods")) {
		//Verificar si tiene compromiso para no permitir anular el doc
		
		//An�o del documento
		$anno_codigo_documento= "20".substr($request_codigo_documento, -2);
		//Buscar el Expediente
		$expediente = "";
		if (trim($request_codigo_documento) <>"") {
			$sql= " Select * from sai_pres_busca_expediente (".$anno_codigo_documento.",'". $request_codigo_documento."') as expediente";
			$resultado_set = pg_exec($conexion ,$sql) ;
			if ($resultado_set) {
				$row = pg_fetch_array($resultado_set);
				$expediente=trim($row[0]);
			}
		}
		// Buscar el Compromiso
		$compromiso = "";
		if ($expediente<>"" &&  $expediente<>"N/A") {
			$sql= " Select * from sai_pres_busca_op_exp (".$anno_codigo_documento.",2,'". $expediente."') as compromiso";
			$resultado_set = pg_exec($conexion ,$sql);
			if ($resultado_set) {
				$row = pg_fetch_array($resultado_set,0);
				$compromiso=trim($row[0]);
			}
		}		
		//Si tiene compromiso no se puede anular
		if ($compromiso != "") {
			$anular = 0;
		}	
	}
	
//Si se puede anular
if ($anular == 1) {
	
		//Incluir la platilla segun el documento y el objeto
		$plantilla = $request_id_tipo_documento."_eanular";
		$directorio = "documentos/".$request_id_tipo_documento."/".$plantilla.".php";
		//echo "<div align='center'><span class='normalNegrita_naranja'> $nombre_documento </span></div>";
		include($directorio);	
		
		echo "<br><div align='center'><span class='normalNegrita'>El documento ".$request_codigo_documento." ha sido anulado. </span></div>";
		
		if ($request_id_tipo_documento<>"codi"){
		//Verificar si el doc que se va anular tienen un doc soporte para habilitarlo nuevamente.
		$cod_doc_soporte = "";
		$sql_sopor="SELECT * FROM sai_buscar_doc_soporte_directo('".$request_codigo_documento."') as doc_soporte";
		$resultado_sopor=pg_query($conexion,$sql_sopor) or die("Error en sai_buscar_doc_soporte_directo");
		if ($row_sopor = pg_fetch_array($resultado_sopor)) {
		
			$cod_doc_soporte = $row_sopor["doc_soporte"];				
		}}
		//Si hay doc soporte cambiar el estado a "Pendiente" (39) nuevamente para hacer el nuevo doc del tipo que se va anular 
		if ($cod_doc_soporte != "") {
		
			$estado_doc_soporte = 39;	
			//Si el doc actual es sopg 
			if ($request_id_tipo_documento == "sopg") {			
				//El estado de los docs con que se genera sopg es "Pendiente pago" (38)
				$estado_doc_soporte = 38;			
			}
				
			$sql_pend =" SELECT * FROM sai_modificar_estado_doc_genera('".$cod_doc_soporte."',".$estado_doc_soporte.") as resultado ";
			$resultado_pend = pg_query($conexion,$sql_pend) or die("Error al mostrar");
			if ($row_pend = pg_fetch_array($resultado_pend)) {
				$modifico_pend = $row_pend["resultado"];
				
				$mensaje = "El documento ".$cod_doc_soporte." asociado al ".$request_codigo_documento." se encuentra nuevamente en bandeja para generar otro documento de ".$nombre_documento.".";
				
				echo "<br><div align='center'><span class='normalNegrita_naranja'>". $mensaje ." </span></div>";
			}
		
		}
		
}
else {
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Anular Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body >
	<table width="500" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray"> 
		<td height="21" colspan="3" valign="midden"><div align="left" class="normalNegrita">ANULAR DOCUMENTO</div></td>
		</tr>
  		<tr>
    	<td colspan="4" class="normal"><br>
    	<div align="center">
		No se puede anular el documento <?php echo $request_codigo_documento; ?> <br>
		porque tienen un compromiso presupuestario.
		<br>
		<br>
		<img src="imagenes/mano_bad.gif" width="31" height="38">
		<br><br>
		
		</div>
		</td>
  		</tr>
	</table>
</body>
</html>
<?php
}
?>