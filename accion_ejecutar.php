<? 
ob_start();
session_start();
include('includes/conexion.php');
include('includes/funciones.php');
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
	ob_end_flush(); 
	exit;
}	
ob_end_flush(); 
	
//Valores de Sesion
$user_perfil_id = $_SESSION['user_perfil_id'];
$user_login = $_SESSION['login'] ;	
$user_depe_id = substr($_SESSION['user_perfil_id'],2,3);

//Tipo de documento
$request_id_tipo_documento = "";
if (isset($_REQUEST["tipo"])) $request_id_tipo_documento = $_REQUEST["tipo"];	
		
//Buscar nombre del documento
$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar el nombre del documento");
if ($row = pg_fetch_array($resultado)) $nombre_documento = $row["resultado"];
	
//Objeto actual del documento (Insertar, Modificar o Revisar)
$request_id_objeto = "";
if (isset($_REQUEST["accion"])) $request_id_objeto = $_REQUEST["accion"];	
	
//Buscar nombre del objeto actual (la accion)
$sql = " SELECT * FROM sai_buscar_objeto('$request_id_objeto') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al buscar el objeto");
if ($row = pg_fetch_array($resultado)) $nombre_objeto = $row["resultado"];

//Id de la opcion seleccionada (sai_wfopcion)
$request_id_opcion = "";
if (isset($_REQUEST["opcion"])) $request_id_opcion = $_REQUEST["opcion"];	
	
//Buscar nombre de la opcion 
$sql = "select * from sai_buscar_opcion('$request_id_opcion') as (nombre_opcion varchar, desc_opcion varchar)";	
$resultado = pg_query($conexion,$sql) or die("Error al buscar la opcion");
if ($row = pg_fetch_array($resultado)) $nombre_opcion = $row["nombre_opcion"];		
	
//Id del registro hijo (proximo registro)
$request_id_hijo = "";
if (isset($_REQUEST["hijo"])) $request_id_hijo = $_REQUEST["hijo"];	
	
//Id de la siguiente accion
$request_id_objeto_sig = "";
if (isset($_REQUEST["accion_sig"])) $request_id_objeto_sig = $_REQUEST["accion_sig"];	
	
//Verificar si el usuario tiene permiso para el objeto (accion) actual
$sql = " SELECT * FROM sai_permiso_accion('$request_id_objeto','$request_id_tipo_documento','$user_perfil_id') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al buscar los permisos por rol");
if ($row = pg_fetch_array($resultado)) $tiene_permiso = $row["resultado"];
		
if ($tiene_permiso == 0) { //Enviar mensaje de error ?>
	<script>
		document.location.href = "mensaje.php?pag=documentos&tipo=<? echo $request_id_tipo_documento; ?>";
	</script>
<?
}

//Si el objeto es ingresar
if ($request_id_objeto == 1) {
	
	//Buscar dependencia del solicitante
	$dependencia_solicitante = $user_depe_id;
		
	//Si el documento es sopg o pgch o tran
	if (($request_id_tipo_documento == "sopg") || ($request_id_tipo_documento=="pgch") || ($request_id_tipo_documento=="tran")) {

		//Buscar el grupo general que conforma la proxima instancia a la cual va el documento segun el objeto siguiente
		$sql = " SELECT * FROM sai_buscar_grupo_obj('$request_id_tipo_documento','$request_id_objeto_sig','$request_id_hijo') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al buscar_grupo_objeto");
		if ($row = pg_fetch_array($resultado)) $grupo_general = $row["resultado"];

		//Buscar los perfiles del grupo general
		$perfiles_general = "";
		$sql = " SELECT * FROM sai_buscar_perfil_grupo('$grupo_general') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al buscar grupo_perfil");
		if ($row = pg_fetch_array($resultado)) $perfiles_general = $row["resultado"];
			
		// Busco el grupo particular al cual va el documento en su proxima instancia
		$grupo_particular=buscar_grupo_particular_dependencia($dependencia_solicitante,$perfiles_general);
	}
}
else {	//El objeto es revisar o modificar
		
	//Codigo del documento
	$request_codigo_documento = "";	
	if (isset($_REQUEST["id"])) $request_codigo_documento = $_REQUEST["id"];
	if ($request_codigo_documento == "") {//Si el codigo es vacio , mostrar mensaje de error ?>
		<script>
			document.location.href = "error.php?nro=1&tipo=<? echo $request_id_tipo_documento; ?>";
		</script>
	<?}
		
	//Buscar el perfil que genero el documento para conocer la dependencia
	$sql_d = "SELECT * FROM sai_doc_genera WHERE docg_id='$request_codigo_documento' ";
	$resultado = pg_query($conexion,$sql_d) or die("Error al consultar doc_genera");
	if ($row = pg_fetch_array($resultado)) {
		$perfil_actual = $row["perf_id_act"];
		$perfil_creador = $row["perf_id"]; //Perfil del usuario que gener� el documento
		$dependencia_creador = substr($perfil_creador,2,3);
	}
			
	//Buscar el grupo general que conforma la proxima instancia a la cual va el documento segun el objeto siguiente
	$sql = " SELECT * FROM sai_buscar_grupo_obj('$request_id_tipo_documento','$request_id_objeto_sig','$request_id_hijo') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al buscar grupo_obj");
	if ($row = pg_fetch_array($resultado)) $grupo_general = $row["resultado"];
			
		//Buscar los perfiles del grupo general
	$perfiles_general = "";
	$sql = " SELECT * FROM sai_buscar_perfil_grupo('$grupo_general') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al buscar grupo_perfil");
	if ($row = pg_fetch_array($resultado)) $perfiles_general = $row["resultado"];
			
	//Buscar el grupo particular

	if ($request_id_objeto_sig == 2) { //Si el objeto es modificar
		//Cambios efectuados para realizar la devolución en sopg desde el Adjunto al Coordinador de Ordenación de Pago y no a quien creo el sopg
		if (($user_perfil_id=="46450") && ($request_id_tipo_documento=="sopg")) $grupo_particular=buscar_grupo_particular_dependencia($dependencia_creador,$perfiles_general);
		else $grupo_particular = $perfil_creador;
	}
	else { 		//si no termino la cadena de revision, buscar el siguiente grupo
		if (($request_id_objeto_sig != 99) && ($request_id_objeto_sig != 98)) $grupo_particular=buscar_grupo_particular_dependencia($dependencia_creador,$perfiles_general);
	}
}
	
$ventana_ejecutar = 1; //Indica si es pagina de ejecutar:1 o no:0 
$mensaje_adicional = ""; 
$estado_cambiado_pendiente = 0; 	//Para indicar que ya cambio el estado del doc pendiente
$estado_pendiente = 0; 	//Id del estado para el doc pendiente
$mensaje_pendiente = ""; 	//Mensaje para indicar el paso pendiente a iniciar (compras y servicios)
$perfil_mensaje_pendiente = ""; 	//Indica el perfil que realiza la accion del mensaje_pendiente
$grupo_particular_p = ""; //Usando en algunas plantillas, donde se define el grupo particular
$codigo_doc_pendiente = ""; 	//Usado para el codigo de los doc que son inicio para generar otros docs
$operacion_exitosa = 0; 	//Para indicar si hay la operacion es exitosa (1) o no (0)
$mensaje_siguiente_inst = ""; 	//Mensaje que indica la siguiente instancia. Se asigna en las plantillas
$disponibilidad = true; //Para indicar si hay disponibilidad presupuestaria
	
//Incluir la platilla segun el documento y el objeto
if (($request_id_objeto==2) && ($user_perfil_id=="42450") && ($request_id_tipo_documento=="sopg")) $plantilla = $request_id_tipo_documento."_e5";
else $plantilla = $request_id_tipo_documento."_e".$request_id_objeto;
	
$directorio = "documentos/".$request_id_tipo_documento."/".$plantilla.".php";
echo "<div align='center'><span class='normalNegrita'> $nombre_documento </span></div>";
include($directorio);
		//echo $request_id_tipo_documento;
//Si el objeto es ingresar 
if ($request_id_objeto == 1) { 	//si se ingreso en doc_genera
	if ($inserto_doc == 1) {
		$operacion_exitosa = 1;		
		//Para los documentos que dependen de otros cambiar el estado de "Pendiente" a "Aprobado"			
		if ($codigo_doc_pendiente != "") {
			if ($estado_cambiado_pendiente == 1) $nuevo_estado_pendiente = $estado_pendiente;			
			else {	
					//Buscar el objeto final del doc pendiente
				if ($request_id_tipo_documento=="pgch" || $request_id_tipo_documento=="tran") {
					$codigo_doc_pendiente = $sopg;
					$sql_obj_pend =" SELECT wfob_id_ini FROM sai_doc_genera WHERE docg_id='$codigo_doc_pendiente'";	
					$resultado_obj_pend = pg_query($conexion,$sql_obj_pend) or die("Error al mostrar OBJETO");
					if ($row_obj_pend = pg_fetch_array($resultado_obj_pend)) $id_objeto_cod_pend = $row_obj_pend["wfob_id_ini"];

					//Si el doc pendiente finalizo y es aprobado
					if ($id_objeto_cod_pend == 99) $nuevo_estado_pendiente = 13; //Estado Aprobado
					else { //Si el doc pendiente finalizo y es rechazado
						if ($id_objeto_cod_pend == 98) $nuevo_estado_pendiente = 14; //Estado Rechazado
						else if ($id_objeto_cod_pend == 4) $nuevo_estado_pendiente = 10;
					}
				}	
					
			}
			$sql_pend ="SELECT * FROM sai_modificar_estado_doc_genera('$codigo_doc_pendiente', $nuevo_estado_pendiente) as resultado ";
			$resultado_pend = pg_query($conexion,$sql_pend) or die("Error al mmodificar estado_doc_generado");
			if ($row_pend = pg_fetch_array($resultado_pend)) $modifico_pend = $row_pend["resultado"];
		}
	}	
}
else {
	    if (($request_id_tipo_documento=="pcta") && ($request_id_objeto!=2)) $operacion_exitosa = 1;
		if ($request_id_tipo_documento!="pcta") $operacion_exitosa = 1;
		//Si no hay disponibilidad, no deberia hacer mas nada 
		if ($disponibilidad==false) $operacion_exitosa = 0;
		if ($operacion_exitosa == 1) {	
			//Cambiar la posicion actual del documento
			$sql = " SELECT * FROM sai_modificar_doc_genera('$request_codigo_documento','$request_id_objeto_sig','$request_id_hijo','$grupo_particular') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al modificar documento generado");
			if ($row = pg_fetch_array($resultado)) $modifico = $row["resultado"];
			
			//Si se modifico el documento colocar el doc en estado "En Transito"
			if ($request_id_objeto == 2) {	
				if (($user_perfil_id=="42450") && ($request_id_opcion==5)) $estado_doc = 7;
				else $estado_doc = 10;
				
				$sql_doc =" SELECT * FROM sai_modificar_estado_doc_genera('$request_codigo_documento',$estado_doc) as resultado ";			
				$resultado_doc = pg_query($conexion,$sql_doc) or die("Error al modifica el estado del documento");
				if ($row_doc = pg_fetch_array($resultado_doc)) $modifico_doc = $row_doc["resultado"];
			}
			
			//Indica la firma del usuario
			$firma_doc = "";

			//Si el objeto es revisar y la opcion es Aprobar (3)			
			if ((($request_id_objeto == 3) || ($request_id_objeto == 4)) && ($request_id_opcion==3)) {			
				//Verificar si el usuario tiene la clave publica, 
				$sql = " SELECT * FROM sai_buscar_clave_publica_usuario('$user_login') as clave ";
				$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
				if ($row = pg_fetch_array($resultado)) $tiene_clave = $row["clave"];			
				$clave_publica = $_POST["publicKey"];
				
				//si no existe la clave publica insertarla en BD
				if  ($tiene_clave == "0") {
					$sql = " SELECT * FROM sai_insert_clave_publica('$user_login', '$clave_publica') as resultado ";
					$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
					if ($row = pg_fetch_array($resultado)) {
						$inserto_clave = $row["resultado"];
						$tiene_clave = $inserto_clave;
					}				
				}
				else {
					//Si ya existe, compararla con la actual
					//Si son distintas , actualizarla
					if ($clave_publica!=$tiene_clave) {
						$sql = " SELECT * FROM sai_modificar_clave_publica('$user_login', '$clave_publica') as resultado ";
						$resultado = pg_query($conexion,$sql) or die("Error al mostrar");			
						if ($row = pg_fetch_array($resultado)) {
							$modifico_clave = $row["resultado"];							
						}									
					}				
				}
				
				$firma_doc = $_POST["firma"];		
			}

			//Insertar la aprobacion
			$sql = " SELECT * FROM sai_insert_revision_doc('$request_codigo_documento', '$user_login', '$user_perfil_id', '$request_id_opcion', '$firma_doc') as resultado ";
			$resultado = pg_query($conexion,$sql) or die("Error al insertar la revision");
		
			if ($row = pg_fetch_array($resultado)) $inserto_aprobacion = $row["resultado"];
		}	
		//Caso puntos de cuenta consejo directivo
		if( ($user_perfil_id=="62400") && ($request_id_opcion==6) && (($asunto_id=='040') || ($asunto_id=='039'))){
			$estado_doc=10;
			$sql = "update sai_doc_genera set wfob_id_ini=99, esta_id=13, perf_id_act='' where docg_id='".$codigo."'";
	    	$resultado_set= pg_exec($conexion ,$sql);
	    	$request_id_objeto_sig=99;
		}
	} //fin else objeto != 1
	
	//Para indicar la accion que acaba de realizar el usuario
	$mensaje_accion = "";	
			
	if ($operacion_exitosa == 1) {
		//Accion del documento
		switch ($request_id_objeto) {
		
			case 1:
				$accion_doc = "inserta";
				$mensaje_accion = utf8_decode("Usted ha generado con éxito el documento: $cod_doc. ");
				break;
			case 2:
				$accion_doc = "modifica";
				$mensaje_accion = utf8_decode("Usted ha modificado con éxito el documento: $cod_doc. ");
				break;
			case 3:
				$accion_doc = "revisar";
				$mensaje_accion = utf8_decode("Usted ha revisado el documento $cod_doc, con la opción: $nombre_opcion ");
				break;
			case 4:
				$accion_doc = "revisar";
				$mensaje_accion = utf8_decode("Usted ha revisado el documento $cod_doc, con la opción: $nombre_opcion ");
				break;	
		}

		$desc_accion_doc = $nombre_objeto ." ". $nombre_documento;	
		$user_perfil_id_grl = substr($user_perfil_id,0,2)."000";
		
		//Insertar la accion
		$sql = " SELECT * FROM sai_insert_accion_docu('$request_codigo_documento', '$user_login', '$user_perfil_id', '$accion_doc', '$user_depe_id', '$desc_accion_doc', '0') as resultado ";
		$resultado = pg_query($conexion,$sql) or die("Error al insertar_accion_documento");
		if ($row = pg_fetch_array($resultado)) $cod_accion = $row["resultado"]; 
		
		//Si el objeto siguiente es fin (aprobado o rechazado)
		if (($request_id_objeto_sig == 99) || ($request_id_objeto_sig == 98)) {
			$mensaje= "Documento finalizado";
			if ($mensaje_adicional == "") $mensaje_adicional = $perfil_mensaje_pendiente.$mensaje_pendiente;
			
			$estado_final = 13; //Aprobado
			if ($request_id_objeto_sig == 98) $estado_final = 14;	//Rechazado

			//Si hay mensaje pendiente el estado es "Pendiente" para generar el siguiente doc
			if ($mensaje_pendiente!="") $estado_final = 39;
			
			if ($estado_cambiado_pendiente == 1) $estado_final = $estado_pendiente;			
			
			$sql_doc =" SELECT * FROM sai_modificar_estado_doc_genera('$request_codigo_documento',$estado_final) as resultado ";			
			$resultado_doc = pg_query($conexion,$sql_doc) or die("Error al modificar el estado del doc generado");
			if ($row_doc = pg_fetch_array($resultado_doc)) $modifico_doc = $row_doc["resultado"];
		}
		else {
			//Indicar la proxima instancia de revision
			//Buscar el nombre del cargo y dependencia
			$sql = " SELECT * FROM sai_buscar_cargo_depen('$grupo_particular') as resultado ";	
			$resultado = pg_query($conexion,$sql) or die("Error al buscar el cargo asociado a la dependencia");
			if ($row = pg_fetch_array($resultado)) $cargo_depen_grupo_particular = $row["resultado"];
			
			if ($mensaje_siguiente_inst == "") $mensaje=  " El Documento fue enviado a la instancia:". $cargo_depen_grupo_particular; /* . "grupopart".$grupo_particular. "grupogeneral: ".$grupo_general. "perfiles general: " .$perfiles_general . "perfil creador: ". $perfil_creador . "Dependencia creador: ". $dependencia_creador. "11: ". $grupo_particularp11 . "12: ". $grupo_particularp12. "13: ". $grupo_particularp13. "14: ". $grupo_particularp14. "15: ". $grupo_particularp15."aja";*/
			else $mensaje = $mensaje_siguiente_inst.$cargo_depen_grupo_particular;
		}

		echo "<br><div align='center'><span class='normalNegrita'> $mensaje_accion </span></div>";
		echo "<br><div align='center'><span class='normalNegrita'> $mensaje </span></div>";
		if ($mensaje_adicional != "") {
			echo "<br><div align='center'><span class='normalNegrita'> $mensaje_adicional </span></div>";
		}
		//Incluir la lista de las revisiones
		echo "<br>";
		include("includes/revisiones_mostrar.php");
	}
?>

<script language="javascript">
function codigo_validacion() {
	<?php
	$tipo=Fisico;
	$sql_resp0="SELECT * FROM sai_any_tabla('sai_respaldo','docg_id,resp_tipo','
	docg_id=''$cod_doc'' and resp_tipo=''$tipo''')      resultado_set(docg_id varchar, resp_tipo varchar)"; 
	//$resultado_resp0=pg_query($conexion,$sql_resp0) or die("Error al Mostrar Lista de Respaldos");
	$resultado_resp0=pg_query($conexion,$sql_resp0);
	if($resultado_resp0 === false){
		 echo ("Error al Mostrar Lista de Respaldos");
		try {
			throw new Exception("Error en accion ejecutar. Detalles :".utf8_encode(pg_last_error($conexion) . "Query: ".$sql_resp0));
		} catch (Exception $e) {
			error_log($e, 0);
		}
	}
	$total0=pg_num_rows($resultado_resp0);
	?>
}
</script>