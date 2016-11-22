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
	
	//Incluir la platilla segun el documento y el objeto
	$plantilla = $request_id_tipo_documento."_anular";
	$directorio = "documentos/".$request_id_tipo_documento."/".$plantilla.".php";
	//echo "<div align='center'><span class='normalNegrita_naranja'> $nombre_documento </span></div>";
	include($directorio);

?>