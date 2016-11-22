<?php
session_name('sai_mct');
session_start();
session_cache_limiter('nocache,private');
session_set_cookie_params(0, "/", $HTTP_SERVER_VARS["HTTP_HOST"], 0);
if (isset($_POST['contrasena']) && isset($_POST['usuario'])){
	$_SESSION['login'] ="";
	$_SESSION['registrado']="";
	$_SESSION['an_o_presupuesto'] =0;
	//$_SESSION['fecha_inicio'] ='30/06/2008';
	//$_SESSION['fecha_fin'] = '';

	$pass= $_POST['contrasena'];
	$user =$_POST['usuario'];
		
	$usario_valido_ldap ="true";
	include("recursos/usuario/encripta_desencripta.php");
	$palabra="nodigitarnada";
	$pass=crypt_md5($pass,$palabra);

	//$_SESSION['objeto_finalizado']=99;

	$_SESSION['perfil_jp']="62400";	 //Jefatura de Presupuesto
	$_SESSION['perfil_de']="47350";  //Dirección Ejecutiva
	$_SESSION['perfil_p']="65150";	 //Presidencia
	$_SESSION['perfil_ae']="38350";  //Asistente Ejecutivo
	$_SESSION['perfil_aop']="26450"; //Analista de Ordenacion de Pago
	$_SESSION['perfil_cop']="42450"; //Coordinador de Ordenacion de Pago
	$_SESSION['perfil_ath']="37500"; //Asistente Administrativo de Talento Humano
	$_SESSION['perfil_aad']="37450"; //Asistente Administrativo de Administración y Finanzas

	$_SESSION['jefe_presu']="14196354";
	$_SESSION['admin1']="14045453";
	$_SESSION['admin2']="15586921";
	$_SESSION['admin3']="14344794";
	$_SESSION['admin4']="17286920";
	//$_SESSION['usua_presi']='2981419';
	$_SESSION['usua_presi']='15316498';

	$_SESSION['cero']=0;
	$_SESSION['uno']=1;
	$_SESSION['dos']=2;
	$_SESSION['tres']=3;
	$_SESSION['cuatro']=4;
	$_SESSION['cinco']=5;
	$_SESSION['ocho']=8;
	$_SESSION['once']=11;

	$_SESSION['presidencia']="150";
	$_SESSION['dir_ejecutiva']="350";

	$_SESSION['adjunto']="03";		//Adjunto
	$_SESSION['consultor']="41"; 	//Consultor Jurídico
	$_SESSION['coordinador']="42";	//Coordinador
	$_SESSION['coord_nac']="44";	//Coordinador Nacional
	$_SESSION['director']="46";		//Director
	$_SESSION['director_ej']="47";	//Director Ejecutivo
	$_SESSION['gerente']="60";		//Gerente
	$_SESSION['jefe']="62";			//Jefe
	$_SESSION['presidente']="65";	//Presidencia

	$sql = "Select * from sai_buscar_usuario  ('".$user."','".$pass."') resultado_set(email varchar, login varchar, activo boolean,cedula varchar,nombres varchar,apellidos varchar, tlf_ofic varchar, cargo varchar, dependencia varchar, depe_id varchar, perfil_id varchar)";

	$resultado_set = pg_exec($conexion ,$sql);
	$filas=0;
	$row = pg_fetch_array($resultado_set);
	$filas = pg_num_rows($resultado_set);

	if ($filas==0){
		header("Location: index.php?error=1");
		exit;
	}
	if ($filas >0 )	{
		if ($row["activo"] <> true){
			//echo "<CENTER> Cuenta Inactiva. </CENTER>";
			exit;
		}
		$_SESSION['email'] =trim($row["email"]);
		$_SESSION['login'] =trim($row["login"]);
		$_SESSION['solicitante'] =trim($row["nombres"]) . " " . trim($row["apellidos"]) ;
		$_SESSION['tlf_ofic']=trim($row["tlf_ofic"]);
		$_SESSION['cargo']=  trim ($row["cargo"]);
		$_SESSION['cedula']= trim ($row["cedula"]);
		$_SESSION['user_depe']= trim ($row["dependencia"]); /*Nombre de la depencia*/
		$_SESSION['user_depe_id']= trim ($row["depe_id"]);  /*ID de la dependencia*/
		$_SESSION['user_perfil_id']= trim ($row["perfil_id"]);  /*ID del perfil*/
		$_SESSION['user_perfil']=''; /*nombre del perfil*/
		
		$perfiles = array();		
		$sql="SELECT * FROM sai_usua_perfil WHERE usua_login= '".$_SESSION['login']."' AND uspe_fin is null ORDER by uspe_tp desc, carg_id ";
		$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
		$num_reg=pg_num_rows($resultado); 
		$cont=0;
		while($row=pg_fetch_array($resultado)){ 
			$sql_perf_tmp="SELECT * FROM sai_buscar_cargo_depen('".$row['carg_id']."') as carg_nombre ";
			$resultado_perf_tmp=pg_query($conexion,$sql_perf_tmp) or die("Error al mostrar");
			$row_perf_tmp=pg_fetch_array($resultado_perf_tmp);
			$perfiles[sizeof($perfiles)]=array();
			$perfiles[sizeof($perfiles)-1][0]=trim($row['carg_id']);
			$perfiles[sizeof($perfiles)-1][1]=trim($row_perf_tmp['carg_nombre']);
		}
		$_SESSION['perfiles'] = $perfiles;
		
		/* Se graba en la tabla sai_logs el ingreso de la persona*/
		$sql = "Select * from sai_insert_logs('$user','$_SERVER[HTTP_USER_AGENT]','$_SERVER[REMOTE_ADDR]','$_SERVER[HTTP_REFERER]') as logs_id (int4)";
		/* Ejecuta y almacena el resultado de la orden SQL en $resultado_set */
		$resultado_set = pg_exec($conexion ,$sql);
		$row = pg_fetch_array($resultado_set);
	//	$_SESSION['logs_id'] = $row[0]; /*Id del Log generado por el usuario*/
		$_SESSION['registrado'] = "registrado";

		/*Se busca el a�o presupuestario Activo Estatus = 23*/
		$sql = "SELECT * from  sai_any_tabla('sai_presupuest','pres_anno,esta_id','esta_id=23 OR esta_id=26') as resultado_set (an_o int2,estado int2)";
		$resultado_set = pg_exec($conexion ,$sql);
		$row = pg_fetch_array($resultado_set);
		$_SESSION['an_o_presupuesto'] = $row[0]; /*A�o del Presupuesto*/
		//$_SESSION['PRES_EDO']= $row[1]; /* Estado del presupuesto */

		/*Se busca el a�o presupuestario en formulaci�n */
		$sql = "SELECT * from  sai_any_tabla('sai_presupuest','pres_anno','esta_id=20') as resultado_set (an_o int2)";
		$resultado_set = pg_exec($conexion ,$sql);

		if($row_pre = pg_fetch_array($resultado_set)){
			$sql = "SELECT * from  sai_any_tabla('sai_poan','poan_anno,esta_id','poan_anno=".$row_pre[0]."') as resultado_set (an_o int2,estado int4)";
			$resultado_set = pg_exec($conexion ,$sql);

			if($row = pg_fetch_array($resultado_set)){
				$_SESSION['POA'] = $row[0]; /*A�o del Plan Operativo*/
				//$_SESSION['POA_ESTADO'] = $row[1]; /*Estado del POA*/
			}else{
				$_SESSION['POA']=0;
				//$_SESSION['POA_ESTADO']=0;
			}
		}else{
			$_SESSION['POA']=0;
			//$_SESSION['POA_ESTADO']=0;
		}
		$_SESSION['part_iva']='4.03.18.01.00'; //Partida de Iva
		$_SESSION['part_iva_ano_anterior']='4.11.05.02.00'; //Partida de Iva Años anteriores
		
		//$_SESSION['part_via_nacio']='4.03.09.01.00'; //Partida de Viaticos y Pasajes dentro del Pais
		//$_SESSION['part_via_inter']='4.03.09.02.00'; //Partida de Viaticos y Pasajes fuera del pais
		//$_SESSION['part_km_recor']='4.03.09.03.00'; //Partida de Kilometros Recorridos
		//$_SESSION['part_gast_trasn']='4.03.99.01.00'; //Gastos de Transporte
	}

	$fechaGuardada = $_SESSION["ultimoAcceso"];
	$ahora = date("Y-n-j H:i:s");

	if(isset($fechaGuardada)){
		$tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));
	}else{
		$tiempo_transcurrido=0;
	}

	//comparamos el tiempo transcurrido
	if($tiempo_transcurrido >= 5400) {
		//si pasaron 90 minutos o más
		session_destroy(); // destruyo la sesión
		header("Location: index.php"); //envío al usuario a la pag. de autenticación
	}else {
		$_SESSION["ultimoAcceso"] = $ahora;
	}
}
?>