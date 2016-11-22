<?	
ob_start();
session_start();
include('includes/conexion.php');
	 
//Verificar si la session existe
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
	ob_end_flush(); 
	exit;
}	
ob_end_flush(); 

$pag=$_REQUEST["pag"];
	
$var1='';
$var2='';
$var3='';
	
$var1=$_REQUEST['var1'];
$var2=$_REQUEST['var2'];
$var3=$_REQUEST['var3'];	
	
//Buscar nombre del documento
$request_id_tipo_documento = "";		
if (isset($_REQUEST["tipo"])) {		
	$request_id_tipo_documento = $_REQUEST["tipo"];					
}

$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$nombre_documento = $row["resultado"];
}

//Buscar nombre del objeto (la accion)
$request_id_objeto = 0;
if (isset($_REQUEST["accion"])) {
	$request_id_objeto = $_REQUEST["accion"];	
}
$sql = " SELECT * FROM sai_buscar_objeto('$request_id_objeto') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$nombre_objeto = $row["resultado"];
}
	
$user_perfil_id = $_SESSION['user_perfil_id'];
$perfil_gral = substr($user_perfil_id,0,2) ."000";
	
//Verificar si el usuario tiene permiso para el objeto (accion) actual
$sql = " SELECT * FROM sai_permiso_accion('$request_id_objeto','$request_id_tipo_documento','$user_perfil_id') as resultado ";
	
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$tiene_permiso = $row["resultado"];
}

if ($tiene_permiso == 0) {
?>
	<script>
		document.location.href = "mensaje.php?pag=documentos&tipo=<? echo $request_id_tipo_documento; ?>";
	</script>
<?
	header('Location:index.php',false);	
}
	
	$user_grupos_id = "";
	
	$sql = "SELECT * FROM sai_buscar_grupos_perfil('$user_perfil_id') as resultado(grupo_id int4)";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	while ($row = pg_fetch_array($resultado)) {
		//$user_grupos_id .= ",".$row["wfgr_id"];
		$user_grupos_id .= ",''".trim($row["grupo_id"])."''";
	}
	//Borrar 1era coma
	$user_grupos_id = "{".substr($user_grupos_id,1)."}";

	//Buscar proximo registro, prox opcion
	$opciones_id_proyecto = "";
	$opciones_id = "";

	//Si el objeto es ingresar
	if ($request_id_objeto == 1) {
		//Buscar las opciones de la cadena para documento SIN PROYECTO	
			
		$sql_no_proy = "SELECT * FROM sai_buscar_opciones_cadena2('$request_id_tipo_documento','$request_id_objeto','$user_grupos_id',0,'','(8,4,0,5)') as resultado(wfop_id int4, wfob_id_sig int4, wfca_id_hijo int4, wfca_id_padre int4)";		
		$resultado = pg_query($conexion,$sql_no_proy) or die("Error al mostrar");
		$i = 0;
		while ($row = pg_fetch_array($resultado)) {
			$opcion_id = trim($row["wfop_id"]);
			$opcion_siguiente_id = trim($row["wfob_id_sig"]);
			$cadena_siguiente_id = trim($row["wfca_id_hijo"]);
			$cadena_padre_id = trim($row["wfca_id_padre"]);
			
			$opciones_doc_sin_proy[$i] = array($opcion_id,$opcion_siguiente_id,$cadena_padre_id,$cadena_siguiente_id);
			$i++;
		}
		
		//Buscar las opciones de la cadena para documento POR PROYECTO		
		$sql_proy = "SELECT * FROM sai_buscar_opciones_cadena2('$request_id_tipo_documento','$request_id_objeto','$user_grupos_id',1,'',0) as resultado(wfop_id int4, wfob_id_sig int4, wfca_id_hijo int4, wfca_id_padre int4)";		
		$resultado = pg_query($conexion,$sql_proy) or die("Error al mostrar $sql_proy");
		$i = 0;
		while ($row = pg_fetch_array($resultado)) {
			$opcion_id = trim($row["wfop_id"]);
			$opcion_siguiente_id = trim($row["wfob_id_sig"]);
			$cadena_siguiente_id = trim($row["wfca_id_hijo"]);
			$cadena_padre_id = trim($row["wfca_id_padre"]);
			
			$opciones_doc_por_proy[$i] = array($opcion_id,$opcion_siguiente_id,$cadena_padre_id,$cadena_siguiente_id);
			$i++;
		}
		$cantidad_por_proy = $i;
		
		//Guardar las opciones en un arreglo general que sera mostrado en cada plantilla
		$j = 0;
		foreach ($opciones_doc_sin_proy as $registro) {
			$id_opcion = $registro[0];	
			$opcion_siguiente_id = $registro[1];
			$cadena_padre_id = $registro[2];
			$cadena_siguiente_id = $registro[3];
			
			
			if ($cantidad_por_proy == 0 ) {
				$opcione_cp = NULL;
				$opcione_sp = array($id_opcion,$opcion_siguiente_id,$cadena_padre_id,$cadena_siguiente_id);				
				$opciones_doc_inicial[$j] = array($opcione_sp,$opcione_cp);				
			}
			else {
				foreach ($opciones_doc_por_proy as $registro_proy) {
					$id_opcion_proy = $registro_proy[0];
					$opcion_siguiente_id_proy = $registro_proy[1];
					$cadena_padre_id_proy = $registro_proy[2];
					$cadena_siguiente_id_proy = $registro_proy[3];
					if ($id_opcion == $id_opcion_proy) {
					
						$opcione_cp = array($id_opcion,$opcion_siguiente_id_proy,$cadena_padre_id_proy,$cadena_siguiente_id_proy);
						$opcione_sp = array($id_opcion,$opcion_siguiente_id,$cadena_padre_id,$cadena_siguiente_id);				
						
						$opciones_doc_inicial[$j] = array($opcione_sp,$opcione_cp);		
					}		
				}
			}
			$j++;	
		}
	
	}
	else {
		//El objeto es revisar,  modificar, etc.
		
		$request_codigo_documento = "";
		//Codigo del documento
		if (isset($_REQUEST["id"])) {
			$request_codigo_documento = $_REQUEST["id"];	
		}
		if ($request_codigo_documento == "") {
		}	
	
		//Buscar el registro actual del documento
		$sql_d = "SELECT * FROM sai_doc_genera WHERE docg_id='$request_codigo_documento' ";
		$resultado = pg_query($conexion,$sql_d) or die("Error al mostrar");
		if ($row = pg_fetch_array($resultado)) {
			$registro_actual = $row["wfca_id"];
		}

		
		$sql = "SELECT * FROM sai_buscar_opciones_cadena('$request_id_tipo_documento','$request_id_objeto','$user_grupos_id',0,'".$registro_actual."',0) as resultado(wfop_id int4, wfob_id_sig int4, wfca_id_hijo int4, wfca_id_padre int4)";
		$resultado = pg_query($conexion,$sql) or die("Error al mostrar $sql ");
		$i = 0;
		$opcion_id = "";
		while ($row = pg_fetch_array($resultado)) {
			$opcion_id = $row["wfop_id"];
			$opcion_siguiente_id = $row["wfob_id_sig"];
			$cadena_siguiente_id = $row["wfca_id_hijo"];
			$cadena_padre_id = $row["wfca_id_padre"];
			
			$opciones_doc[$i] = array($opcion_id,$opcion_siguiente_id,$cadena_padre_id,$cadena_siguiente_id);
			$i++;
		}
	   //No se va a pedir el codigo del respaldo, en el caso de MODIFICAR
	   if ($request_id_objeto<>2) {
	   
		//Verificar si el documento tiene respaldo fisico
		if ($pag<>1){
		
		$tipo=Fisico;
		
		$sql_resp0="SELECT * FROM sai_any_tabla('sai_respaldo','docg_id,resp_tipo','
		docg_id=''$request_codigo_documento'' and resp_tipo=''$tipo''')      resultado_set(docg_id varchar, resp_tipo varchar)"; 
		$resultado_resp0=pg_query($conexion,$sql_resp0) or die("Error al Mostrar Lista de Respaldos");
	  $total0=pg_num_rows($resultado_resp0);
	 
			if ($total0>0) {
	  			$row_resp0=pg_fetch_array($resultado_resp0);
	   	  	}
	   }
	}//agregada
}
	
	//Indica si es pagina de documento:1 o no:0
	$ventana_documento = 1;
	
	//Incluir la platilla segun el documento y el objeto
	//Cambios efectuados para realizar la devolución en sopg desde el Adjunto al Jefe de Ordenación de Pago y no a quien creo el sopg
	if (($request_id_objeto==2) && ($user_perfil_id=="42450") && ($request_id_tipo_documento=="sopg")) {
	 $plantilla = $request_id_tipo_documento."_5";
	}
	else{
	     $plantilla = $request_id_tipo_documento."_".$request_id_objeto;	
	}

	/*$doc_asociado=$_REQUEST['doc'];
	if (($request_id_objeto==1) && ($request_id_tipo_documento=="sopg")) 
		$directorio = "documentos/".$request_id_tipo_documento."/".$plantilla.".php?doc=".$doc_asociado;
	*/
	$directorio = "documentos/".$request_id_tipo_documento."/".$plantilla.".php";
	//echo "<div align='center'><span class='normalNegrita_naranja'> $nombre_documento </span></div>";
	include($directorio);
	
	//Si el objeto es revisar
	if (($request_id_objeto == 3) || ($request_id_objeto == 4)) {
		//Incluir la lista de las revisiones
		echo "<br>";
		include("includes/revisiones_mostrar.php");
	}
?>
