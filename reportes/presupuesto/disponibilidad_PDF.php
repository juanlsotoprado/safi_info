<?php
require("../../includes/conexion.php");
require("../../includes/constantes.php");
require_once("../../includes/perfiles/constantesPerfiles.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");

$dependenciaUsuario = $_SESSION['user_depe_id'];
$user_perfil_id = $_SESSION['user_perfil_id'];
if((substr($user_perfil_id, 0, 2)=="62" || substr($user_perfil_id, 0, 2)=="80") && substr($user_perfil_id, 2, 3)!=substr(PERFIL_DIRECTOR_PRESUPUESTO, 2, 3)){
	$query = 	"SELECT depe_id_sup
				FROM sai_dependenci 
				WHERE 
					depe_id = '".$dependenciaUsuario."'";
	
	$resultado = pg_exec($conexion, $query);
	if($rowDependencia=pg_fetch_array($resultado)){
		$dependenciaUsuario = $rowDependencia["depe_id_sup"];
	}
}
//$mostrarTodasLasPartidas = $_POST['mostrarTodasLasPartidas'];
$mostrarTodasLasPartidas = "false";

$tipoImputacion=substr($_POST['categoriaProgramatica'],0,strrpos($_POST['categoriaProgramatica'], ";"));//antes del ;
$idProyectoAccion=substr($_POST['categoriaProgramatica'],strrpos($_POST['categoriaProgramatica'], ";")+1,strlen($_POST['categoriaProgramatica']));//despues del ;
$idAccionEspecifica=$_POST['accionEspecifica'];
$centroGestor=$_POST['centroGestor'];
$opcionConsolidar=$_POST['opcionConsolidar'];
if(!$opcionConsolidar){
	$opcionConsolidar=$_POST['opcionConsolidarProyectos'];
	if(!$opcionConsolidar){
		$opcionConsolidar=$_POST['opcionConsolidarOrganismo'];	
	}
}

$partida = $_POST['partida'];

$codigoPartida = "";
if(strlen($partida)>0){
	$codigoPartida = trim($partida);
	$codigoPartida = str_replace(".00", "", $codigoPartida);
	$tok = strtok(trim($codigoPartida), ":");
	$codigoPartida = trim($tok);
}

$anno_pres=$_SESSION['an_o_presupuesto'];
// Descomentar para que muestre el reporte de disponibilidad del año presupuestario especificado
//$anno_pres=2014;

$fechaInicio=$_POST['txt_inicio'];
$fechaFin=$_POST['hid_hasta_itin'];

if(!$fechaInicio || $fechaInicio==""){
	$fechaInicio = "01/01/".$anno_pres;
}

if(!$fechaFin || $fechaFin==""){
	$fechaFin = date('d/m/Y');
}

list($diaInicio,$mesInicio,$anoInicio) = split( '[/.-]', $fechaInicio);
list($diaFin,$mesFin,$anoFin) = split( '[/.-]', $fechaFin);
$estadoActivo = 1;

$contenido = "<style type='text/css'>
				.negrita{
					FONT-WEIGHT: BOLD;
				}
				.bordeTabla{
					border: solid 1px #000000;
				}
				.textoTabla{
					FONT-WEIGHT: normal; FONT-SIZE: 10px; FONT-FAMILY: Verdana, Geneva, Arial, Helvetica, sans-serif; TEXT-DECORATION: none;					
				}
			</style>";

$contenido .=	"<table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";

if($tipoImputacion!=null && $tipoImputacion!="" && $idProyectoAccion!=null && $idProyectoAccion!=""){
	if ( $tipoImputacion=="1" ) {
		$query = 	"SELECT ".
						"sp.proy_titulo as nombre ".
					"FROM sai_proyecto sp ".
					"WHERE ".
						"sp.pre_anno = ".$anno_pres." AND ".
						"sp.proy_id = '".$idProyectoAccion."' ";
		$resultado = pg_exec($conexion, $query);
		$row=pg_fetch_array($resultado);
		$descripcionProyectoAccion = $row["nombre"];	
	} else if ( $tipoImputacion=="0" ) {
		$query = 	"SELECT ".
						"sac.acce_denom as nombre ".
					"FROM sai_ac_central sac ".
					"WHERE ".
						"sac.pres_anno = ".$anno_pres." AND ".
						"sac.acce_id = '".$idProyectoAccion."' ";
		$resultado = pg_exec($conexion, $query);
		$row=pg_fetch_array($resultado);
		$descripcionProyectoAccion = $row["nombre"];
	}
	$contenido .= 	"<tr>".
						"<td class='negrita'>Proyecto/Acci&oacute;n centralizada:</td>".
						"<td>".trim($descripcionProyectoAccion)."</td>".
					"</tr>";
}else if($opcionConsolidar=="2"){
	$contenido .= 	"<tr>".
						"<td class='negrita' colspan='2'>Todos los proyectos consolidados</td>".
					"</tr>";
}else if($opcionConsolidar=="3"){
	$contenido .= 	"<tr>".
						"<td class='negrita' colspan='2'>Todo el organismo consolidado</td>".
					"</tr>";
}

if($centroGestor!=null && $centroGestor!=""){
	/*if ( $tipoImputacion=="1" ) {
		$query = "SELECT ".
					"spae.paes_nombre as nombre, ".
					"spae.centro_gestor ".
				"FROM sai_proy_a_esp spae ".
				"WHERE ".
					"spae.pres_anno = ".$anno_pres." AND ".
					"spae.centro_gestor = '".$centroGestor."' ".
				"GROUP BY spae.paes_nombre, spae.centro_gestor ";
		$resultado = pg_exec($conexion, $query);
		$row=pg_fetch_array($resultado);
		$descripcionCentroGestor = "(".$row["centro_gestor"].") ".$row["nombre"];
	} else if ( $tipoImputacion=="0" ) {
		$query = "SELECT ".
					"sae.aces_nombre as nombre, ".
					"sae.centro_gestor ".
				"FROM sai_acce_esp sae ".
				"WHERE ".
					"sae.pres_anno = ".$anno_pres." AND ".
					"sae.centro_gestor = '".$centroGestor."' ".
				"GROUP BY sae.aces_nombre, sae.centro_gestor ";
		$resultado = pg_exec($conexion, $query);
		$row=pg_fetch_array($resultado);
		$descripcionCentroGestor = "(".$row["centro_gestor"].") ".$row["nombre"];
	}*/
	$descripcionCentroGestor = $centroGestor;
	
	$contenido .= 	"<tr>".
						"<td class='negrita'>Centro gestor:</td>".
						"<td>".$descripcionCentroGestor."</td>".
					"</tr>";
}

$contenido .= 	"<tr>".
					"<td class='negrita'>A&ntilde;o Presupuesto:</td>".
					"<td>".$anno_pres."</td>".
				"</tr>".
				"<tr>".
					"<td class='negrita'>Ejecuci&oacute;n:</td>".
					"<td>".$fechaInicio." al ".$fechaFin."</td>".
				"</tr>".
			"</table>";

$query= "SELECT ".
			"sp.part_id, ".
			"sp.part_nombre AS partida ".	
		"FROM sai_partida sp ".
		"WHERE ".
			"sp.pres_anno = ".$anno_pres." AND ".
			"sp.part_id NOT LIKE '4.11.0%' AND ".
			"sp.part_id LIKE '%.00.00' ".
		"ORDER BY sp.part_id";
$resultadoPartidasPrimariasYSecundarias=pg_query($query) or die("Error en las partidas");
$tamanoPartidasPrimariasYSecundarias = pg_num_rows($resultadoPartidasPrimariasYSecundarias);
$arregloPartidas = array();
while($filaPartidasPrimariasYSecundarias=pg_fetch_array($resultadoPartidasPrimariasYSecundarias)) {
	$arregloPartidas[]= array($filaPartidasPrimariasYSecundarias["part_id"],$filaPartidasPrimariasYSecundarias["partida"]);
}

if(!$opcionConsolidar && $centroGestor==""){
	//MONTOS PROGRAMADOS
	if($mostrarTodasLasPartidas=="true"){
		$query=	"SELECT ".
					"id_accion_especifica, ".
					"nombre, ".
					"centro_gestor, ".
					"centro_costo, ".
					"part_id, ".
					"partida, ".
					"COALESCE(SUM(monto_programado),0) as monto_programado ".
				"FROM ".
				"(".
				"SELECT ".
					"s.id_accion_especifica, ".
					"s.nombre, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
					"sf1125d.part_id, ".
					"sp.part_nombre AS partida, ".
					"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
				"FROM sai_forma_1125 sf1125, sai_fo1125_det sf1125d, sai_partida sp, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.paes_nombre as nombre, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.aces_nombre as nombre, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sf1125.pres_anno = ".$anno_pres." AND ".
					"sf1125.form_tipo = '".$tipoImputacion."' AND ".
					"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
					"sf1125.form_id_aesp = s.id_accion_especifica AND ".
					"sf1125.form_id = sf1125d.form_id AND ".
					"sf1125.pres_anno = sf1125d.pres_anno AND ".
					"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
					"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
					"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
					"sf1125d.part_id = sp.part_id AND ".
					"sp.esta_id = ".$estadoActivo." AND ".
					"sf1125d.pres_anno = sp.pres_anno AND ".
					"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
				"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sf1125d.part_id, sp.part_nombre ".
				"UNION ".
				"SELECT ".
					"s.id_accion_especifica, ".
					"s.nombre, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
					"sp.part_id, ".
					"sp.part_nombre AS partida, ".
					"0 as monto_programado ".
				"FROM sai_partida sp, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.paes_nombre as nombre, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.aces_nombre as nombre, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.part_id LIKE '".$codigoPartida."%' AND ".
					"sp.part_id NOT LIKE '4.11.0%' AND ".
					"sp.esta_id = ".$estadoActivo." AND ".
					"sp.part_id NOT LIKE '%.00.00' ".
				"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sp.part_id, sp.part_nombre ".
				") AS s ".
				"GROUP BY id_accion_especifica, nombre, centro_gestor, centro_costo, part_id, partida ".
				"ORDER BY centro_gestor, centro_costo, id_accion_especifica, part_id";
		$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
	}else{
		$query=	"SELECT ".
					"s.id_accion_especifica, ".
					"s.nombre, ".
					"s.centro_gestor, ".
					"s.centro_costo, ".
					"sf1125d.part_id, ".
					"sp.part_nombre AS partida, ".
					"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
				"FROM sai_forma_1125 sf1125, sai_fo1125_det sf1125d, sai_partida sp, ".
					"(";
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica, ".
							"spae.paes_nombre as nombre, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ".
						"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica, ".
							"sae.aces_nombre as nombre, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($idAccionEspecifica && $idAccionEspecifica!=""){
				$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ".
						"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
		}
		$query.=	") as s ".
				"WHERE ".
					"sf1125.pres_anno = ".$anno_pres." AND ".
					"sf1125.form_tipo = '".$tipoImputacion."' AND ".
					"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
					"sf1125.form_id_aesp = s.id_accion_especifica AND ".
					"sf1125.form_id = sf1125d.form_id AND ".
					"sf1125.pres_anno = sf1125d.pres_anno AND ".
					"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
					"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
					"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
					"sf1125d.part_id = sp.part_id AND ".
					"sf1125d.pres_anno = sp.pres_anno AND ".
					"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
				"GROUP BY s.id_accion_especifica, s.nombre, s.centro_gestor, s.centro_costo, sf1125d.part_id, sp.part_nombre ".
				"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf1125d.part_id";
		$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
	}
	
	//MONTOS RECIBIDOS
	$query=	"SELECT ".
				"s.id_accion_especifica, ".
				"s.centro_gestor, ".
				"s.centro_costo, ".
				"sf0305d.part_id, ".
				"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_recibido ".
			"FROM sai_doc_genera sdg, sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as s ".
			"WHERE ".
				"sf0305.pres_anno = ".$anno_pres." AND ".
				"sf0305.f030_id = sdg.docg_id AND sdg.wfob_id_ini = 99 AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
				"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
				"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
				"sf0305d.f0dt_tipo='1' AND ".
				"sf0305d.part_id LIKE '".$codigoPartida."%' AND ".
				"sf0305d.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
			"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf0305d.part_id";
	$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");
	
	//MONTOS CEDIDOS
	$query=	"SELECT ".
				"s.id_accion_especifica, ".
				"s.centro_gestor, ".
				"s.centro_costo, ".		
				"sf0305d.part_id, ".
				"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_cedido ".
			"FROM sai_doc_genera sdg, sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as s ".
			"WHERE ".
				"sf0305.pres_anno = ".$anno_pres." AND ".
				"sf0305.f030_id = sdg.docg_id AND sdg.wfob_id_ini = 99 AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
				"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
				"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
				"sf0305d.f0dt_tipo='0' AND ".
				"sf0305d.part_id LIKE '".$codigoPartida."%' AND ".
				"sf0305d.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, sf0305d.part_id ".
			"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, sf0305d.part_id";
	$resultadoMontosCedidos=pg_query($query) or die("Error en los montos cedidos");

	//MONTOS DIFERIDOS
	$query=	"SELECT ".
				"se.id_accion_especifica, ".
				"se.centro_gestor, ".
				"se.centro_costo, ".	
				"spit.pcta_sub_espe as part_id, ".
				"COALESCE(SUM(spit.pcta_monto),0) as monto_diferido ".
			"FROM sai_doc_genera sdg, sai_pcuenta sp, sai_pcta_traza spt, sai_pcta_imputa_traza spit, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") AS se ".
			"WHERE ".
				"sdg.docg_id = spit.pcta_id AND ".
				"spit.pres_anno = ".$anno_pres." AND ".
				//"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
				"spt.pcta_id = spit.pcta_id AND ".
				"spit.pcta_id = sp.pcta_id AND ".
				"to_char(spit.pcta_fecha,'YYYY-MM-DD HH24:MI') = to_char(spt.pcta_fecha2,'YYYY-MM-DD HH24:MI') AND ".
				/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
				"sp.esta_id <> 2 AND ".
				"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
	
				/*"spt.pcta_id NOT IN ".
					"(SELECT pcta_id ".
					"FROM sai_pcta_traza ".
					"WHERE ".
						"(esta_id=15 OR esta_id=2) AND ".
						"pcta_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
				"(".
					"(sdg.wfob_id_ini = 99) OR ".
					"(spit.depe_id = '350' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_PRESIDENTE."')) OR ".
					"(spit.depe_id = '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."')) OR ".
					"(spit.depe_id <> '350' AND spit.depe_id <> '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."','".PERFIL_PRESIDENTE."')) ".
				") AND ".*/
				"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
				"spit.pcta_acc_pp = se.id_proyecto_accion AND ".
				"spit.pcta_acc_esp = se.id_accion_especifica AND ".
				"spit.pcta_sub_espe LIKE '".$codigoPartida."%' AND ".
				"spit.pcta_sub_espe NOT LIKE '4.11.0%' ".
			"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, spit.pcta_sub_espe ".
			"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, spit.pcta_sub_espe";
	$resultadoMontosDiferidos=pg_query($query) or die("Error en los montos diferidos");

	//MONTOS COMPROMETIDOS
	$query=	"SELECT ".
				"se.id_accion_especifica, ".
				"se.centro_gestor, ".
				"se.centro_costo, ".
				"scit.comp_sub_espe as part_id, ".
				"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido ".
			"FROM sai_comp_imputa_traza scit, ".
			"(".
				"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
				"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as si ".
				"WHERE ".
					"scit.pres_anno = ".$anno_pres." AND ".
					"length(sct.pcta_id) > 4 AND ".
					"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
					"sct.comp_id NOT IN ".
						"(SELECT comp_id ".
						"FROM sai_comp_traza ".
						"WHERE ".
							"(esta_id=15 OR esta_id=2) AND ".
							"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"sct.comp_id = scit.comp_id AND ".
					"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = si.id_proyecto_accion AND ".
					"scit.comp_acc_esp = si.id_accion_especifica ".
				"GROUP BY scit.comp_id ".
			") as s, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as se ".
			"WHERE ".
				"scit.comp_id = s.comp_id AND ".
				"scit.comp_fecha = s.fecha AND ".
				"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
				"scit.comp_acc_pp = se.id_proyecto_accion AND ".
				"scit.comp_acc_esp = se.id_accion_especifica AND ".
				"scit.comp_sub_espe LIKE '".$codigoPartida."%' AND ".
				"scit.comp_sub_espe NOT LIKE '4.11.0%' ".
			"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, scit.comp_sub_espe ".
			"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, scit.comp_sub_espe";
	$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");

	//MONTOS COMPROMETIDOS AISLADOS
	$query=	"SELECT ".
				"se.id_accion_especifica, ".
				"se.centro_gestor, ".
				"se.centro_costo, ".
				"scit.comp_sub_espe as part_id, ".
				"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido_aislado ".
			"FROM sai_comp_imputa_traza scit, ".
			"(".
				"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
				"FROM sai_comp_traza sct, sai_comp_imputa_traza scit,sai_comp sc , ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as si ".
				"WHERE ".
					"scit.pres_anno = ".$anno_pres." AND ".
					"length(sc.pcta_id) < 4 AND ".
	"sc.comp_id = scit.comp_id AND ".
					"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
					"sct.comp_id NOT IN ".
						"(SELECT comp_id ".
						"FROM sai_comp_traza ".
						"WHERE ".
							"(esta_id=15 OR esta_id=2) AND ".
							"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"sct.comp_id = scit.comp_id AND ".
					"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = si.id_proyecto_accion AND ".
					"scit.comp_acc_esp = si.id_accion_especifica ".
				"GROUP BY scit.comp_id ".
			") as s, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as se ".
			"WHERE ".
				"scit.comp_id = s.comp_id AND ".
				"scit.comp_fecha = s.fecha AND ".
				"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
				"scit.comp_acc_pp = se.id_proyecto_accion AND ".
				"scit.comp_acc_esp = se.id_accion_especifica AND ".
				"scit.comp_sub_espe LIKE '".$codigoPartida."%' AND ".
				"scit.comp_sub_espe NOT LIKE '4.11.0%' ".
			"GROUP BY se.id_accion_especifica, se.centro_gestor, se.centro_costo, scit.comp_sub_espe ".
			"ORDER BY se.centro_gestor, se.centro_costo, se.id_accion_especifica, scit.comp_sub_espe";
	$resultadoMontosComprometidosAislados=pg_query($query) or die("Error en los montos comprometidos aislados");

	//MONTOS CAUSADOS
	$query=	"SELECT ".
				"s.id_accion_especifica, ".
				"s.centro_gestor, ".
				"s.centro_costo, ".
				"scd.part_id, ".
				"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ".
			"FROM sai_causado sc, sai_causad_det scd, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as s ".
			"WHERE ".
				"sc.pres_anno = ".$anno_pres." AND ".
				"sc.esta_id <> 2 AND ".
				/*"sc.esta_id <> 15 AND ".*/
				"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
				"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
				"sc.caus_id = scd.caus_id AND ".
				"sc.pres_anno = scd.pres_anno AND ".
				"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
				"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
				"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
				"scd.cadt_abono='1' AND ".
				"scd.part_id LIKE '".$codigoPartida."%' AND ".
				"scd.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, scd.part_id ".
			"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, scd.part_id";
	$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");
	
	//MONTOS PAGADOS
	$query=	"SELECT ".
	"s.id_accion_especifica, ".
				"s.centro_gestor, ".
				"s.centro_costo, ".
				"spd.part_id, ".
				"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
			"FROM sai_pagado sp, sai_pagado_dt spd, ".
				"(";
	if($tipoImputacion=="1"){//proyecto
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica, ".
						"spae.centro_gestor, ".
						"spae.centro_costo ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.proy_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"spae.paes_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"spae.pres_anno = ".$anno_pres." ".
					"ORDER BY spae.centro_gestor, spae.centro_costo, spae.paes_id";
	}else if($tipoImputacion=="0"){//accion centralizada
		$query .= 	"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica, ".
						"sae.centro_gestor, ".
						"sae.centro_costo ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.acce_id = '".$idProyectoAccion."' AND ";
		if($idAccionEspecifica && $idAccionEspecifica!=""){
			$query .=	"sae.aces_id = '".$idAccionEspecifica."' AND ";
		}
		$query .=		"sae.pres_anno = ".$anno_pres." ".
					"ORDER BY sae.centro_gestor, sae.centro_costo, sae.aces_id";
	}
	$query.=	") as s ".
			"WHERE ".
				"sp.pres_anno = ".$anno_pres." AND ".
				"sp.esta_id <> 2 AND ".
				/*"sp.esta_id <> 15 AND ".*/
				"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
				"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
				"sp.paga_id = spd.paga_id AND ".
				"sp.pres_anno = spd.pres_anno AND ".
				"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
				"spd.padt_id_p_ac = s.id_proyecto_accion AND ".
				"spd.padt_cod_aesp = s.id_accion_especifica AND ".
				//"spd.padt_abono='1' AND ".
				"spd.part_id LIKE '".$codigoPartida."%' AND ".
				"spd.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY s.id_accion_especifica, s.centro_gestor, s.centro_costo, spd.part_id ".
			"ORDER BY s.centro_gestor, s.centro_costo, s.id_accion_especifica, spd.part_id";
	$resultadoMontosPagados=pg_query($query) or die("Error en los montos pagados");
	
	$totalProgramados = 0;
	$totalRecibidos = 0;
	$totalCedidos = 0;
	$totalDiferidos = 0;
	$totalComprometidos = 0;
	$totalComprometidosAislados = 0;
	$totalCausados = 0;
	$totalPagados = 0;
	$totalDisponible = 0;

	$totalPrimerOrdenProgramados = 0;
	$totalPrimerOrdenRecibidos = 0;
	$totalPrimerOrdenCedidos = 0;
	$totalPrimerOrdenDiferidos = 0;
	$totalPrimerOrdenComprometidos = 0;
	$totalPrimerOrdenComprometidosAislados = 0;
	$totalPrimerOrdenCausados = 0;
	$totalPrimerOrdenPagados = 0;
	$totalPrimerOrdenDisponible = 0;

	$totalSegundoOrdenProgramados = 0;
	$totalSegundoOrdenRecibidos = 0;
	$totalSegundoOrdenCedidos = 0;
	$totalSegundoOrdenDiferidos = 0;
	$totalSegundoOrdenComprometidos = 0;
	$totalSegundoOrdenComprometidosAislados = 0;
	$totalSegundoOrdenCausados = 0;
	$totalSegundoOrdenPagados = 0;
	$totalSegundoOrdenDisponible = 0;

	$programado = 0;
	$recibido = 0;
	$cedido = 0;
	$diferido = 0;
	$comprometido = 0;
	$comprometidoAislado = 0;
	$causado = 0;
	$pagado = 0;
	$montoAjustado = 0;
	$montoDisponible = 0;

	$accionEspecificaAnterior = "";
	$partidaAnteriorPrimerOrden = "";
	$partidaAnteriorSegundoOrden = "";
	$contenidoPartidaAnteriorPrimerOrden = "";
	$contenidoPartidaAnteriorSegundoOrden = "";

	$tamanoResultado = pg_num_rows($resultadoMontosProgramados);
	$diferencias = "";
	
	if($tamanoResultado){
		while($filaProgramados=pg_fetch_array($resultadoMontosProgramados)) {
			if($partidaAnteriorPrimerOrden==""){
				$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
			}else if(	$partidaAnteriorPrimerOrden!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00") ||
						$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				
				if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
					$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%";
				}else{
					$ejecucion = "0,00%";
				}
				
				if ( 	$totalPrimerOrdenProgramados!=0 ||
						$totalPrimerOrdenRecibidos!=0 ||
						$totalPrimerOrdenCedidos!=0 ||
						$totalPrimerOrdenDiferidos!=0 ||
						$totalPrimerOrdenComprometidos!=0 ||
						$totalPrimerOrdenComprometidosAislados!=0 ||
						$totalPrimerOrdenCausados!=0 ||
						$totalPrimerOrdenPagados!=0 ||
						$totalPrimerOrdenDisponible!=0) {
				
					$contenido .= "<tr style='color: #35519B;'>".
										"<td>".$nombrePartida."</td>".
										"<td>".$partidaAnteriorPrimerOrden."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenRecibidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDiferidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidosAislados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCausados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenPagados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDisponible,2,',','.')."</td>".
										"<td align='right'>".$ejecucion."</td>".
									"</tr>".$contenidoPartidaAnteriorPrimerOrden;
				}
				$contenidoPartidaAnteriorPrimerOrden = "";
				
				$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
				$totalPrimerOrdenProgramados = 0;
				$totalPrimerOrdenRecibidos = 0;
				$totalPrimerOrdenCedidos = 0;
				$totalPrimerOrdenDiferidos = 0;
				$totalPrimerOrdenComprometidos = 0;
				$totalPrimerOrdenComprometidosAislados = 0;
				$totalPrimerOrdenCausados = 0;
				$totalPrimerOrdenPagados = 0;
				$totalPrimerOrdenDisponible = 0;
			}
			
			if($partidaAnteriorSegundoOrden==""){
				$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
			}else if(	$partidaAnteriorSegundoOrden!=(substr($filaProgramados["part_id"], 0, 7).".00.00") ||
						$accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
				if($accionEspecificaAnterior==$filaCausados["id_accion_especifica"]){
					do{
						if(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaCausados["part_id"] && 
							$filaCausados["part_id"]==$filaPagados["part_id"]){
							//IMPRIMIR CAUSADO Y PAGADO
							$causado = $filaCausados["monto_causado"];
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalCausados += $causado;
							$totalPagados += $pagado;
							
							$diferencias .= "<tr style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaPagados["part_id"] &&
							$filaCausados["part_id"]>$filaPagados["part_id"]){
							//IMPRIMIR PAGADO
							$pagado = $filaPagados["monto_pagado"];
							if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenPagados += $pagado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenPagados += $pagado;
							}
							$totalPagados += $pagado;
							
							$diferencias .= "<tr style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaPagados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";							
							$filaPagados=pg_fetch_array($resultadoMontosPagados);
						}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
							$filaProgramados["part_id"]>$filaCausados["part_id"] && 
							$filaCausados["part_id"]<$filaPagados["part_id"]){
							//IMPRIMIR CAUSADO
							$causado = $filaCausados["monto_causado"];
							if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
								$totalSegundoOrdenCausados += $causado;
							}
							if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
								$totalPrimerOrdenCausados += $causado;
							}
							$totalCausados += $causado;
							
							$diferencias .= "<tr style='color: red;'>
												<td>".trim($filaProgramados['partida'])."</td>
												<td align='right'>".trim($filaCausados['part_id'])."</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>
													<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
												</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
												<td align='right'>0,00</td>
											</tr>";
							$filaCausados=pg_fetch_array($resultadoMontosCausados);
						}
					}while(	$accionEspecificaAnterior==$filaCausados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaCausados["part_id"]);
				}
				if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]){
					do{
						//IMPRIMIR PAGADO
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalPagados += $pagado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaPagados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}while(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]);
				}
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				
				if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
					$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}
				
				if ( (substr($partidaAnteriorSegundoOrden, 0, 4).".00.00.00")!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00") || $accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"] ) {
					
					if ( 	$totalSegundoOrdenProgramados!=0 ||
							$totalSegundoOrdenRecibidos!=0 ||
							$totalSegundoOrdenCedidos!=0 ||
							$totalSegundoOrdenDiferidos!=0 ||
							$totalSegundoOrdenComprometidos!=0 ||
							$totalSegundoOrdenComprometidosAislados!=0 ||
							$totalSegundoOrdenCausados!=0 ||
							$totalSegundoOrdenPagados!=0 ||
							$totalSegundoOrdenDisponible!=0) {
					
						$contenido .= 	"<tr style='color: #005E20;'>".
											"<td>".$nombrePartida."</td>".
											"<td>".$partidaAnteriorSegundoOrden."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
											"<td align='right'>".$ejecucion."</td>".
										"</tr>".$contenidoPartidaAnteriorSegundoOrden;	
					}
				} else {
					if ( 	$totalSegundoOrdenProgramados!=0 ||
							$totalSegundoOrdenRecibidos!=0 ||
							$totalSegundoOrdenCedidos!=0 ||
							$totalSegundoOrdenDiferidos!=0 ||
							$totalSegundoOrdenComprometidos!=0 ||
							$totalSegundoOrdenComprometidosAislados!=0 ||
							$totalSegundoOrdenCausados!=0 ||
							$totalSegundoOrdenPagados!=0 ||
							$totalSegundoOrdenDisponible!=0) {
						
						$contenidoPartidaAnteriorPrimerOrden .= 	"<tr style='color: #005E20;'>".
																		"<td>".$nombrePartida."</td>".
																		"<td>".$partidaAnteriorSegundoOrden."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
																		"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
																		"<td align='right'>".$ejecucion."</td>".
																	"</tr>".$contenidoPartidaAnteriorSegundoOrden;
					}
				}
				$contenidoPartidaAnteriorSegundoOrden = "";
				
				$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
				$totalSegundoOrdenProgramados = 0;
				$totalSegundoOrdenRecibidos = 0;
				$totalSegundoOrdenCedidos = 0;
				$totalSegundoOrdenDiferidos = 0;
				$totalSegundoOrdenComprometidos = 0;
				$totalSegundoOrdenComprometidosAislados = 0;
				$totalSegundoOrdenCausados = 0;
				$totalSegundoOrdenPagados = 0;
				$totalSegundoOrdenDisponible = 0;
				
				$contenido .= $diferencias;
				$diferencias = "";				
			}

			if($accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"]){
				if($accionEspecificaAnterior!=""){
					$contenido .= 		"<tr class='negrita'>".
											"<td>Total Bs.</td>".
											"<td></td>".
											"<td align='right'>".number_format($totalProgramados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalRecibidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalDiferidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalComprometidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalComprometidosAislados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalCausados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalPagados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalDisponible,2,',','.')."</td>".
											"<td align='right'>";
					if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
						$contenido .= 			number_format(($totalDiferidos+$totalComprometidosAislados)*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.')."%"; 
					}else{
						$contenido .=			"0,00%";	
					}					
					$contenido .= 			"</td>".
										"</tr>".
									"</table>";
					$totalProgramados = 0;
					$totalRecibidos = 0;
					$totalCedidos = 0;
					$totalDiferidos = 0;
					$totalComprometidos = 0;
					$totalComprometidosAislados = 0;
					$totalCausados = 0;
					$totalPagados = 0;
					$totalDisponible = 0;
				}
				$accionEspecificaAnterior=$filaProgramados["id_accion_especifica"];
				$descripcionProyecto=$filaProgramados["centro_gestor"]."/".$filaProgramados["centro_costo"].": ".$filaProgramados["nombre"];
				
				$contenido .= 	"<br/>".
								"<table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>".
									"<tr>".
										"<td class='negrita' colspan='13'>".$descripcionProyecto."</td>".
									"</tr>".
									"<tr>".
										"<td class='negrita' width='200px'>Denominaci&oacute;n</td>".
										"<td class='negrita' width='70px'>Partida</td>".
										"<td class='negrita' width='110px'>Presupuesto Ley</td>".
										"<td class='negrita' width='60px'>Recibido</td>".
										"<td class='negrita' width='60px'>Cedido</td>".
										"<td class='negrita' width='110px'>Presupuesto modif.</td>".
										"<td class='negrita' width='60px'>Apartado</td>".
										"<td class='negrita' width='90px'>Comprometido</td>".
										"<td class='negrita' width='90px'>Compr. aislado</td>".
										"<td class='negrita' width='60px'>Causado</td>".
										"<td class='negrita' width='60px'>Pagado</td>".
										"<td class='negrita' width='60px'>Disponible</td>".
										"<td class='negrita' width='70px'>% Ejecuci&oacute;n</td>".
									"</tr>";										
			}

			$programado = $filaProgramados['monto_programado'];
			$recibido = 0;
			$cedido = 0;
			$diferido = 0;
			$comprometido = 0;
			$comprometidoAislado = 0;
			$causado = 0;
			$pagado = 0;
			
			//se cambio id_accion_especifica por centro_costo.
			if($filaRecibidos==null){
				$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
			}
			if(	$filaProgramados["part_id"]==$filaRecibidos["part_id"] &&
				$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"]){
				$recibido = $filaRecibidos["monto_recibido"];
				$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
			}else if($filaProgramados["part_id"]>$filaRecibidos["part_id"] &&
					$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"]){
				do{
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}while(	$filaProgramados["centro_costo"]==$filaRecibidos["centro_costo"] &&
						$filaProgramados["part_id"]>$filaRecibidos["part_id"]);
				if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
					$recibido = $filaRecibidos["monto_recibido"];
					$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
				}
			}
			
			if($filaCedidos==null){
				$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
			}
			if($filaProgramados["part_id"]==$filaCedidos["part_id"] &&
			$filaProgramados["centro_costo"]==$filaCedidos["centro_costo"]){
				$cedido = $filaCedidos["monto_cedido"];
				$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
			}else if($filaProgramados["centro_costo"]==$filaCedidos["centro_costo"] &&
			$filaProgramados["part_id"]>$filaCedidos["part_id"]){
				do{
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}while(	$filaProgramados["centro_costo"]==$filaCedidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaCedidos["part_id"]);
				if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
					$cedido = $filaCedidos["monto_cedido"];
					$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
				}
			}

			$montoAjustado=($programado+$recibido)-$cedido;

			if($filaDiferidos==null){
				$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
			}
			if($filaProgramados["part_id"]==$filaDiferidos["part_id"] &&
			$filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"]){
				$diferido = $filaDiferidos["monto_diferido"];
				$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
			}else if($filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"] &&
			$filaProgramados["part_id"]>$filaDiferidos["part_id"]){
				do{
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}while(	$filaProgramados["centro_costo"]==$filaDiferidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaDiferidos["part_id"]);
				if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
					$diferido = $filaDiferidos["monto_diferido"];
					$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
				}
			}
			
			if($filaComprometidos==null){
				$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
			}
			if($filaProgramados["part_id"]==$filaComprometidos["part_id"] &&
			$filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"]){
				$comprometido = $filaComprometidos["monto_comprometido"];
				$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
			}else if($filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"] &&
			$filaProgramados["part_id"]>$filaComprometidos["part_id"]){
				do{
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}while(	$filaProgramados["centro_costo"]==$filaComprometidos["centro_costo"] &&
				$filaProgramados["part_id"]>$filaComprometidos["part_id"]);
				if($filaProgramados["part_id"]==$filaComprometidos["part_id"]){
					$comprometido = $filaComprometidos["monto_comprometido"];
					$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
				}
			}

			if($filaComprometidosAislados==null){
				$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
			}
			if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"] &&
			$filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"]){
				$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
				$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
			}else if($filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"] &&
			$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]){
				do{
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}while(	$filaProgramados["centro_costo"]==$filaComprometidosAislados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaComprometidosAislados["part_id"]);
				if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
					$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
					$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
				}
			}

			if($filaCausados==null){
				$filaCausados=pg_fetch_array($resultadoMontosCausados);
			}		
			if($filaPagados==null){
				$filaPagados=pg_fetch_array($resultadoMontosPagados);
			}
			if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
			$filaProgramados["part_id"]==$filaCausados["part_id"]){
				$causado = $filaCausados["monto_causado"];
				$filaCausados=pg_fetch_array($resultadoMontosCausados);
			}else if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
			$filaProgramados["part_id"]>$filaCausados["part_id"]){
				do{
					//IMPRIMIR CAUSADO CON CEROS EN LAS DEMAS COLUMNAS
					if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] && $filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
						$filaCausados["part_id"]==$filaPagados["part_id"]){
						//IMPRIMIR CAUSADO Y PAGADO
						$causado = $filaCausados["monto_causado"];
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenCausados += $causado;
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenCausados += $causado;
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalCausados += $causado;
						$totalPagados += $pagado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaCausados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
											</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaCausados=pg_fetch_array($resultadoMontosCausados);
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}else if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
						$filaProgramados["part_id"]>$filaPagados["part_id"]){
						//IMPRIMIR PAGADO
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalPagados += $pagado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaPagados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";							
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}else if($filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
						$filaProgramados["part_id"]>$filaCausados["part_id"]){
						//IMPRIMIR CAUSADO
						$causado = $filaCausados["monto_causado"];
						if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenCausados += $causado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenCausados += $causado;
						}
						$totalCausados += $causado;

						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaCausados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaCausados=pg_fetch_array($resultadoMontosCausados);
					}
				}while(	$filaProgramados["centro_costo"]==$filaCausados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaCausados["part_id"]);
				if($filaProgramados["part_id"]==$filaCausados["part_id"]){
					$causado = $filaCausados["monto_causado"];
					$filaCausados=pg_fetch_array($resultadoMontosCausados);
				}
			}

			if($filaProgramados["part_id"]==$filaPagados["part_id"] &&
			$filaProgramados["centro_costo"]==$filaPagados["centro_costo"]){
				$pagado = $filaPagados["monto_pagado"];
				$filaPagados=pg_fetch_array($resultadoMontosPagados);
			}else if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
			$filaProgramados["part_id"]>$filaPagados["part_id"]){
				do{
					if($filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
						$filaProgramados["part_id"]>$filaPagados["part_id"]){
						//IMPRIMIR PAGADO
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalPagados += $pagado;
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaPagados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}
				}while(	$filaProgramados["centro_costo"]==$filaPagados["centro_costo"] &&
				$filaProgramados["part_id"]>$filaPagados["part_id"]);
				if($filaProgramados["part_id"]==$filaPagados["part_id"]){
					$pagado = $filaPagados["monto_pagado"];
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}
			}

			if($diferido>0){
				$montoDisponible=($montoAjustado)-($diferido)-$comprometidoAislado;
			}else if($comprometidoAislado>0){
				$montoDisponible=($montoAjustado)-($comprometidoAislado);
			}else{
				$montoDisponible=($montoAjustado);
			}
			
			if ( 	$programado!=0 ||
					$recibido!=0 ||
					$cedido!=0 ||
					$diferido!=0 ||
					$comprometido!=0 ||
					$comprometidoAislado!=0 ||
					$causado!=0 ||
					$pagado!=0 ||
					$montoDisponible!=0) {
				
				$totalProgramados += $programado;
				$totalRecibidos +=  $recibido;
				$totalCedidos += $cedido;
				$totalDiferidos += $diferido;
				$totalComprometidos += $comprometido;
				$totalComprometidosAislados += $comprometidoAislado;
				$totalCausados += $causado;
				$totalPagados += $pagado;
				$totalDisponible += $montoDisponible;
	
				$totalPrimerOrdenProgramados += $programado;
				$totalPrimerOrdenRecibidos +=  $recibido;
				$totalPrimerOrdenCedidos += $cedido;
				$totalPrimerOrdenDiferidos += $diferido;
				$totalPrimerOrdenComprometidos += $comprometido;
				$totalPrimerOrdenComprometidosAislados += $comprometidoAislado;
				$totalPrimerOrdenCausados += $causado;
				$totalPrimerOrdenPagados += $pagado;
				$totalPrimerOrdenDisponible += $montoDisponible;
	
				$totalSegundoOrdenProgramados += $programado;
				$totalSegundoOrdenRecibidos +=  $recibido;
				$totalSegundoOrdenCedidos += $cedido;
				$totalSegundoOrdenDiferidos += $diferido;
				$totalSegundoOrdenComprometidos += $comprometido;
				$totalSegundoOrdenComprometidosAislados += $comprometidoAislado;
				$totalSegundoOrdenCausados += $causado;
				$totalSegundoOrdenPagados += $pagado;
				$totalSegundoOrdenDisponible += $montoDisponible;
				
				$contenidoPartidaAnteriorSegundoOrden .= 	"<tr>".
																"<td>".trim($filaProgramados['partida'])."</td>".
																"<td align='right'>".trim($filaProgramados['part_id'])."</td>".
																"<td align='right'>".number_format($programado,2,',','.')."</td>".
																"<td align='right'>".number_format($recibido,2,',','.')."</td>".
																"<td align='right'>".number_format($cedido,2,',','.')."</td>".
																"<td align='right'>".number_format($programado+$recibido-$cedido,2,',','.')."</td>".
																"<td align='right'>".number_format($diferido,2,',','.')."</td>".
																"<td align='right'>".number_format($comprometido,2,',','.')."</td>".
																"<td align='right'>".number_format($comprometidoAislado,2,',','.')."</td>".
																"<td align='right'>".number_format($causado,2,',','.')."</td>".
																"<td align='right'>".number_format($pagado,2,',','.')."</td>".
																"<td align='right'>".number_format($montoDisponible,2,',','.')."</td>".
																"<td align='right'>";
				if(round($programado+$recibido-$cedido)!=0){
					$contenidoPartidaAnteriorSegundoOrden .= 		number_format(($diferido+$comprometidoAislado)*100/($programado+$recibido-$cedido),2,',','.').'%'; 
				}else{
					$contenidoPartidaAnteriorSegundoOrden .= 	"0,00%";	
				}					
				$contenidoPartidaAnteriorSegundoOrden .= 		"</td>".
															"</tr>";
			}
		}
		
		if($partidaAnteriorPrimerOrden!=""){
			$nombrePartida = "";
			$iPartidasPrimariasYSecundarias = 0;						
			while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
				if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
					$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
					break;
				}
				$iPartidasPrimariasYSecundarias++;
			}
			
			if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
				$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%";
			}else{
				$ejecucion = "0,00%";	
			}
			if ( 	$totalPrimerOrdenProgramados!=0 ||
					$totalPrimerOrdenRecibidos!=0 ||
					$totalPrimerOrdenCedidos!=0 ||
					$totalPrimerOrdenDiferidos!=0 ||
					$totalPrimerOrdenComprometidos!=0 ||
					$totalPrimerOrdenComprometidosAislados!=0 ||
					$totalPrimerOrdenCausados!=0 ||
					$totalPrimerOrdenPagados!=0 ||
					$totalPrimerOrdenDisponible!=0) {
				
				$contenido .= 	"<tr style='color: #35519B;'>".
									"<td>".$nombrePartida."</td>".
									"<td>".$partidaAnteriorPrimerOrden."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenProgramados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenRecibidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenDiferidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenComprometidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenComprometidosAislados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenCausados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenPagados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalPrimerOrdenDisponible,2,',','.')."</td>".
									"<td align='right'>".$ejecucion."</td>".
								"</tr>".$contenidoPartidaAnteriorPrimerOrden;
			}
			$contenidoPartidaAnteriorPrimerOrden = "";
		}

		if($partidaAnteriorSegundoOrden!=""){
			if($accionEspecificaAnterior==$filaCausados["id_accion_especifica"]){
				do{
					if(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
						$filaProgramados["part_id"]>$filaCausados["part_id"] && 
						$filaCausados["part_id"]==$filaPagados["part_id"]){
						//IMPRIMIR CAUSADO Y PAGADO
						$causado = $filaCausados["monto_causado"];
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenCausados += $causado;
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenCausados += $causado;
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalCausados += $causado;
						$totalPagados += $pagado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaCausados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
											</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaCausados=pg_fetch_array($resultadoMontosCausados);
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
						$filaProgramados["part_id"]>$filaPagados["part_id"] &&
						$filaCausados["part_id"]>$filaPagados["part_id"]){
						//IMPRIMIR PAGADO
						$pagado = $filaPagados["monto_pagado"];
						if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenPagados += $pagado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenPagados += $pagado;
						}
						$totalPagados += $pagado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaPagados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";							
						$filaPagados=pg_fetch_array($resultadoMontosPagados);
					}else if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] &&
						$filaProgramados["part_id"]>$filaCausados["part_id"] && 
						$filaCausados["part_id"]<$filaPagados["part_id"]){
						//IMPRIMIR CAUSADO
						$causado = $filaCausados["monto_causado"];
						if($partidaAnteriorSegundoOrden==substr($filaCausados["part_id"], 0, 7).".00.00"){
							$totalSegundoOrdenCausados += $causado;
						}
						if($partidaAnteriorPrimerOrden==substr($filaCausados["part_id"], 0, 4).".00.00.00"){
							$totalPrimerOrdenCausados += $causado;
						}
						$totalCausados += $causado;
						
						$diferencias .= "<tr style='color: red;'>
											<td>".trim($filaProgramados['partida'])."</td>
											<td align='right'>".trim($filaCausados['part_id'])."</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>
												<a style='color: red;' target='_blank' href='detalleCausado.php?partida=".$filaCausados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$causado."&aesp=".$filaCausados['id_accion_especifica']."'>".number_format($causado,2,',','.')."</a>
											</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
											<td align='right'>0,00</td>
										</tr>";
						$filaCausados=pg_fetch_array($resultadoMontosCausados);
					}
				}while(	$accionEspecificaAnterior==$filaCausados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaCausados["part_id"]);
			}
			if($accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]){
				do{
					//IMPRIMIR PAGADO
					$pagado = $filaPagados["monto_pagado"];
					if($partidaAnteriorSegundoOrden==substr($filaPagados["part_id"], 0, 7).".00.00"){
						$totalSegundoOrdenPagados += $pagado;
					}
					if($partidaAnteriorPrimerOrden==substr($filaPagados["part_id"], 0, 4).".00.00.00"){
						$totalPrimerOrdenPagados += $pagado;
					}
					$totalPagados += $pagado;
					
					$diferencias .= "<tr style='color: red;'>
										<td>".trim($filaProgramados['partida'])."</td>
										<td align='right'>".trim($filaPagados['part_id'])."</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
										<td align='right'>
											<a style='color: red;' target='_blank' href='detallePagado.php?partida=".$filaPagados['part_id']."&proy=".$idProyectoAccion."&fecha_inicio=".$fechaInicio."&fecha_fin=".$fechaFin."&tipo=".$tipoImputacion."&consolidado=0&monto=".$pagado."&aesp=".$filaPagados['id_accion_especifica']."'>".number_format($pagado,2,',','.')."</a>
										</td>
										<td align='right'>0,00</td>
										<td align='right'>0,00</td>
									</tr>";
					$filaPagados=pg_fetch_array($resultadoMontosPagados);
				}while(	$accionEspecificaAnterior==$filaPagados["id_accion_especifica"] && $filaProgramados["part_id"]>$filaPagados["part_id"]);
			}
			$contenido .= $diferencias;
			$diferencias = "";
			$nombrePartida = "";
			$iPartidasPrimariasYSecundarias = 0;						
			while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
				if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
					$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
					break;
				}
				$iPartidasPrimariasYSecundarias++;
			}
			
			if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
				$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
			}else{
				$ejecucion = "0,00%";	
			}
			if ( 	$totalSegundoOrdenProgramados!=0 ||
					$totalSegundoOrdenRecibidos!=0 ||
					$totalSegundoOrdenCedidos!=0 ||
					$totalSegundoOrdenDiferidos!=0 ||
					$totalSegundoOrdenComprometidos!=0 ||
					$totalSegundoOrdenComprometidosAislados!=0 ||
					$totalSegundoOrdenCausados!=0 ||
					$totalSegundoOrdenPagados!=0 ||
					$totalSegundoOrdenDisponible!=0) {
				$contenido .=	"<tr style='color: #005E20;'>".
									"<td>".$nombrePartida."</td>".
									"<td align='center'>".$partidaAnteriorSegundoOrden."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
									"<td align='right'>".$ejecucion."</td>".
								"</tr>".$contenidoPartidaAnteriorSegundoOrden;
			}
			$contenidoPartidaAnteriorSegundoOrden = "";
		}
		$contenido .=		"<tr class='negrita'>".
								"<td>Total Bs.</td>".
								"<td></td>".
								"<td align='right'>".number_format($totalProgramados,2,',','.')."</td>".
								"<td align='right'>".number_format($totalRecibidos,2,',','.')."</td>".
								"<td align='right'>".number_format($totalCedidos,2,',','.')."</td>".
								"<td align='right'>".number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')."</td>".
								"<td align='right'>".number_format($totalDiferidos,2,',','.')."</td>".
								"<td align='right'>".number_format($totalComprometidos,2,',','.')."</td>".
								"<td align='right'>".number_format($totalComprometidosAislados,2,',','.')."</td>".
								"<td align='right'>".number_format($totalCausados,2,',','.')."</td>".
								"<td align='right'>".number_format($totalPagados,2,',','.')."</td>".
								"<td align='right'>".number_format($totalDisponible,2,',','.')."</td>".
								"<td align='right'>";
		if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
			$contenido .= 			number_format(($totalDiferidos+$totalComprometidosAislados)*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.').'%'; 
		}else{
			$contenido .=			'0,00%';	
		}					
		$contenido .=			"</td>".
							"</tr>".
						"</table>";
	}else{
		$contenido .= 	"<br/>".
						"<table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>".
							"<tr>".
								"<td align='center' height='40px' valign='middle'>No se encontraron resultados</td>".
							"</tr>".
						"</table>";
	}
}else{// if($opcionConsolidar=="1"){
	
	$contenido .= 	"<br/>".
						"<table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	
	//MONTOS PROGRAMADOS
	if($mostrarTodasLasPartidas=="true"){
		$query=	"SELECT ".
					"part_id, ".
					"partida, ".
					"COALESCE(SUM(monto_programado),0) as monto_programado ".
				"FROM ".
				"( ".
				"SELECT ".
					"sf1125d.part_id, ".
					"sp.part_nombre AS partida, ".
					"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
				"FROM sai_fo1125_det sf1125d, sai_forma_1125 sf1125, sai_partida sp, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"sf1125.form_tipo = '".$tipoImputacion."' AND ".
						"sf1125.form_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
					"sf1125.form_id_aesp = s.id_accion_especifica AND ".
					"sf1125.pres_anno = ".$anno_pres." AND ".
					"sf1125.form_id = sf1125d.form_id AND ".
					"sf1125.pres_anno = sf1125d.pres_anno AND ".
					"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
					"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
					"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
					"sf1125d.part_id = sp.part_id AND ".
					"sp.esta_id = ".$estadoActivo." AND ".
					"sf1125d.pres_anno = sp.pres_anno AND ".
					"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
				"GROUP BY sf1125d.part_id, sp.part_nombre ".
				"UNION ".
				"SELECT ".
					"sp.part_id, ".
					"sp.part_nombre AS partida, ".
					"0 as monto_programado ".
				"FROM sai_partida sp, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ".
					"sp.pres_anno = ".$anno_pres." AND ".
					"sp.part_id LIKE '".$codigoPartida."%' AND ".
					"sp.part_id NOT LIKE '4.11.0%' AND ".
					"sp.esta_id = ".$estadoActivo." AND ".
					"sp.part_id NOT LIKE '%.00.00' ".
				"GROUP BY sp.part_id, sp.part_nombre ".
				") AS s ".
				"GROUP BY s.part_id, s.partida ".
				"ORDER BY s.part_id";
		$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
	}else{
		$query=	"SELECT ".
					"sf1125d.part_id, ".
					"sp.part_nombre AS partida, ".
					"COALESCE(SUM(sf1125d.fodt_monto),0) as monto_programado ".
				"FROM sai_fo1125_det sf1125d, sai_forma_1125 sf1125, sai_partida sp, ".
					"(";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			if($tipoImputacion=="1"){//proyecto
				$query .= 	"SELECT ".
								"spae.proy_id as id_proyecto_accion, ".
								"spae.paes_id as id_accion_especifica ".
							"FROM sai_proy_a_esp spae ".
							"WHERE ".
								"spae.proy_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"spae.pres_anno = ".$anno_pres." ";
			}else if($tipoImputacion=="0"){//accion centralizada
				$query .= 	"SELECT ".
								"sae.acce_id as id_proyecto_accion, ".
								"sae.aces_id as id_accion_especifica ".
							"FROM sai_acce_esp sae ".
							"WHERE ".
								"sae.acce_id = '".$idProyectoAccion."' AND ";
				if($centroGestor && $centroGestor!=""){
					$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
				}
				$query .=		"sae.pres_anno = ".$anno_pres." ";
			}			
		}else if($opcionConsolidar=="2"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ";
		}else if($opcionConsolidar=="3"){
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.pres_anno = ".$anno_pres." ".
						"UNION ".
						"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.pres_anno = ".$anno_pres." ";
		}
		$query.=	") as s ".
				"WHERE ";
		if($opcionConsolidar=="1" || $centroGestor!=""){
			$query.=	"sf1125.form_tipo = '".$tipoImputacion."' AND ".
						"sf1125.form_id_p_ac = '".$idProyectoAccion."' AND ";
		}
		$query.=	"sf1125.form_id_p_ac = s.id_proyecto_accion AND ".
					"sf1125.form_id_aesp = s.id_accion_especifica AND ".
					"sf1125.pres_anno = ".$anno_pres." AND ".
					"sf1125.form_id = sf1125d.form_id AND ".
					"sf1125.pres_anno = sf1125d.pres_anno AND ".
					"sf1125.esta_id <> 15 AND sf1125.esta_id <> 2 AND ".
					"sf1125d.fodt_mes BETWEEN 1 AND 12 AND ".
					"sf1125d.part_id LIKE '".$codigoPartida."%' AND ".
					"sf1125d.part_id NOT LIKE '4.11.0%' AND ".
					"sf1125d.part_id = sp.part_id AND ".
					"sf1125d.pres_anno = sp.pres_anno AND ".
					"sf1125.form_fecha < to_date('".$fechaFin."', 'DD/MM/YYYY')+1 ".
				"GROUP BY sf1125d.part_id, sp.part_nombre ".
				"ORDER BY sf1125d.part_id";
		$resultadoMontosProgramados=pg_query($query) or die("Error en los montos programados");
	}

	//MONTOS RECIBIDOS
	$query=	"SELECT ".
				"sf0305d.part_id, ".
				"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_recibido ".
			"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
					"sf0305d.f0dt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=	"sf0305.pres_anno = ".$anno_pres." AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
				"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
				"sf0305d.f0dt_tipo='1' AND ".
				"sf0305d.part_id LIKE '".$codigoPartida."%' ".
			"GROUP BY sf0305d.part_id ".
			"ORDER BY sf0305d.part_id";
	$resultadoMontosRecibidos=pg_query($query) or die("Error en los montos recibidos");

	//MONTOS CEDIDOS
	$query=	"SELECT ".
				"sf0305d.part_id, ".
				"COALESCE(SUM(sf0305d.f0dt_monto),0) as monto_cedido ".
			"FROM sai_forma_0305 sf0305, sai_fo0305_det sf0305d, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"sf0305d.f0dt_proy_ac = '".$tipoImputacion."' AND ".
					"sf0305d.f0dt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=	"sf0305.pres_anno = ".$anno_pres." AND ".
				"sf0305.esta_id <> 15 AND sf0305.esta_id <> 2 AND ".
				"sf0305.f030_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"sf0305.f030_id = sf0305d.f030_id AND ".
				"sf0305.pres_anno = sf0305d.pres_anno AND ".
				"sf0305d.f0dt_id_p_ac = s.id_proyecto_accion AND ".
				"sf0305d.f0dt_id_acesp = s.id_accion_especifica AND ".
				"sf0305d.f0dt_tipo='0' AND ".
				"sf0305d.part_id LIKE '".$codigoPartida."%' ".
			"GROUP BY sf0305d.part_id ".
			"ORDER BY sf0305d.part_id";
	$resultadoMontosCedidos=pg_query($query) or die("Error en los montos cedidos");

	//MONTOS DIFERIDOS
	$query=	"SELECT ".
				"spit.pcta_sub_espe as part_id, ".
				"COALESCE(SUM(spit.pcta_monto),0) as monto_diferido ".
			"FROM sai_doc_genera sdg, sai_pcuenta sp, sai_pcta_traza spt, sai_pcta_imputa_traza spit, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") AS s ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"spit.pcta_tipo_impu = '".$tipoImputacion."' AND ".
					"spit.pcta_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=	"spit.pres_anno = ".$anno_pres." AND ".
				"sdg.docg_id = spit.pcta_id AND ".
				"spit.pcta_id = spt.pcta_id AND ".
				"spit.pcta_id = sp.pcta_id AND ".
				"to_char(spit.pcta_fecha,'YYYY-MM-DD HH24:MI') = to_char(spt.pcta_fecha2,'YYYY-MM-DD HH24:MI') AND ".
				//"length(spt.pcta_id) > 4 AND ".
				//"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
				"spit.pcta_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') + INTERVAL '1 days' AND ".
				/*"spt.esta_id <> 15 AND spt.esta_id <> 2 AND ".*/
				"sp.esta_id <> 2 AND ".
				"(sp.pcta_asunto <> '020' OR sp.esta_id <> 15) AND ".
	
				/*"spt.pcta_id NOT IN ".
					"(SELECT pcta_id ".
					"FROM sai_pcta_traza ".
					"WHERE ".
						"(esta_id=15 OR esta_id=2) AND ".
						"pcta_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
				"(".
					"(sdg.wfob_id_ini = 99) OR ".
					"(spit.depe_id = '350' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_PRESIDENTE."')) OR ".
					"(spit.depe_id = '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."')) OR ".
					"(spit.depe_id <> '350' AND spit.depe_id <> '150' AND sdg.perf_id_act IN ('".PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS."','".PERFIL_DIRECTOR_EJECUTIVO."','".PERFIL_PRESIDENTE."')) ".
				") AND ".*/
				"spit.pcta_acc_pp = s.id_proyecto_accion AND ".
				"spit.pcta_acc_esp = s.id_accion_especifica AND ".
				"spit.pcta_sub_espe LIKE '".$codigoPartida."%' ".
			"GROUP BY spit.pcta_sub_espe ".
			"ORDER BY spit.pcta_sub_espe";
	$resultadoMontosDiferidos=pg_query($query) or die("Error en los montos diferidos");

	//MONTOS COMPROMETIDOS
	$query=	"SELECT ".
				"scit.comp_sub_espe as part_id, ".
				"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido ".
			"FROM sai_comp_imputa_traza scit, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			", (".
				"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
				"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
					"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
				"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=		"scit.pres_anno = ".$anno_pres." AND ".
					"length(sct.pcta_id) > 4 AND ".
					"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
					"sct.comp_id NOT IN ".
						"(SELECT comp_id ".
						"FROM sai_comp_traza ".
						"WHERE ".
							"(esta_id=15 OR esta_id=2) AND ".
							"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"sct.comp_id = scit.comp_id AND ".
					"scit.comp_acc_pp = s.id_proyecto_accion AND ".
					"scit.comp_acc_esp = s.id_accion_especifica ".		
				"GROUP BY scit.comp_id ".
			") as ss ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=	"scit.comp_id = ss.comp_id AND ".
				"scit.comp_fecha = ss.fecha AND ".
				"scit.comp_acc_pp = s.id_proyecto_accion AND ".
				"scit.comp_acc_esp = s.id_accion_especifica AND ".
				"scit.comp_sub_espe LIKE '".$codigoPartida."%' ".
			"GROUP BY scit.comp_sub_espe ".
			"ORDER BY scit.comp_sub_espe";
	$resultadoMontosComprometidos=pg_query($query) or die("Error en los montos comprometidos");
	
	//MONTOS COMPROMETIDOS AISLADOS
	$query=	"SELECT ".
				"scit.comp_sub_espe as part_id, ".
				"COALESCE(SUM(scit.comp_monto),0) as monto_comprometido_aislado ".
			"FROM sai_comp_imputa_traza scit, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			", (".
				"SELECT scit.comp_id, MAX(scit.comp_fecha) as fecha ".
				"FROM sai_comp_traza sct, sai_comp_imputa_traza scit, ".
					"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
				"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=		"scit.pres_anno = ".$anno_pres." AND ".
					"length(sct.pcta_id) < 4 AND ".
					"scit.comp_fecha BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY')+1 AND ".
					"sct.esta_id <> 15 AND sct.esta_id <> 2 AND ".
					"sct.comp_id NOT IN ".
						"(SELECT comp_id ".
						"FROM sai_comp_traza ".
						"WHERE ".
							"(esta_id=15 OR esta_id=2) AND ".
							"comp_fecha2 < to_date('".$fechaFin."', 'DD/MM/YYYY')+1) AND ".
					"sct.comp_id = scit.comp_id AND ".
					"scit.comp_acc_pp = s.id_proyecto_accion AND ".
					"scit.comp_acc_esp = s.id_accion_especifica ".
				"GROUP BY scit.comp_id ".
			") as ss ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scit.comp_tipo_impu = '".$tipoImputacion."' AND ".
					"scit.comp_acc_pp = '".$idProyectoAccion."' AND ";
	}
	$query.=	"scit.comp_id = ss.comp_id AND ".
				"scit.comp_fecha = ss.fecha AND ".
				"scit.comp_acc_pp = s.id_proyecto_accion AND ".
				"scit.comp_acc_esp = s.id_accion_especifica AND ".
				"scit.comp_sub_espe LIKE '".$codigoPartida."%' ".
			"GROUP BY scit.comp_sub_espe ".
			"ORDER BY scit.comp_sub_espe";
	$resultadoMontosComprometidosAislados=pg_query($query) or die("Error en los montos comprometidos aislados");

	//MONTOS CAUSADOS
	$query= "SELECT ".
				"part_id, ".
				"monto_causado ".
			"FROM ".
			"(".
			"SELECT ".
				"scd.part_id, ".
				"COALESCE(SUM(scd.cadt_monto),0) AS monto_causado ".
			"FROM sai_causado sc, sai_causad_det scd, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"scd.cadt_tipo = '".$tipoImputacion."'::BIT AND ".
					"scd.cadt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=	"sc.pres_anno = ".$anno_pres." AND ".
				"sc.esta_id <> 2 AND ".
				/*"sc.esta_id <> 15 AND ".*/
				"CAST(sc.caus_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
				"(sc.fecha_anulacion IS NULL OR (CAST(sc.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
				"sc.caus_id = scd.caus_id AND ".
				"sc.pres_anno = scd.pres_anno AND ".
				"scd.cadt_id_p_ac = s.id_proyecto_accion AND ".
				"scd.cadt_cod_aesp = s.id_accion_especifica AND ".
				"scd.cadt_abono='1' AND ".
				"scd.part_id LIKE '".$codigoPartida."%' AND ".
				"scd.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY scd.part_id ".
			") AS s ".
			"WHERE s.monto_causado <> 0 ".
			"ORDER BY part_id";
	$resultadoMontosCausados=pg_query($query) or die("Error en los montos causados");

	//MONTOS PAGADOS
	$query= "SELECT ".
				"part_id, ".
				"monto_pagado ".
			"FROM ".
			"(".
			"SELECT ".
				"spd.part_id, ".
				"COALESCE(SUM(spd.padt_monto),0) AS monto_pagado ".
			"FROM sai_pagado sp, sai_pagado_dt spd, ".
				"(";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		if($tipoImputacion=="1"){//proyecto
			$query .= 	"SELECT ".
							"spae.proy_id as id_proyecto_accion, ".
							"spae.paes_id as id_accion_especifica ".
						"FROM sai_proy_a_esp spae ".
						"WHERE ".
							"spae.proy_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"spae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"spae.pres_anno = ".$anno_pres." ";
		}else if($tipoImputacion=="0"){//accion centralizada
			$query .= 	"SELECT ".
							"sae.acce_id as id_proyecto_accion, ".
							"sae.aces_id as id_accion_especifica ".
						"FROM sai_acce_esp sae ".
						"WHERE ".
							"sae.acce_id = '".$idProyectoAccion."' AND ";
			if($centroGestor && $centroGestor!=""){
				$query .=	"sae.centro_gestor = '".$centroGestor."' AND ";
			}
			$query .=		"sae.pres_anno = ".$anno_pres." ";
		}			
	}else if($opcionConsolidar=="2"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ";
	}else if($opcionConsolidar=="3"){
		$query .= 	"SELECT ".
						"spae.proy_id as id_proyecto_accion, ".
						"spae.paes_id as id_accion_especifica ".
					"FROM sai_proy_a_esp spae ".
					"WHERE ".
						"spae.pres_anno = ".$anno_pres." ".
					"UNION ".
					"SELECT ".
						"sae.acce_id as id_proyecto_accion, ".
						"sae.aces_id as id_accion_especifica ".
					"FROM sai_acce_esp sae ".
					"WHERE ".
						"sae.pres_anno = ".$anno_pres." ";
	}
	$query.=	") as s ".
			"WHERE ";
	if($opcionConsolidar=="1" || $centroGestor!=""){
		$query.=	"spd.padt_tipo = '".$tipoImputacion."'::BIT AND ".
					"spd.padt_id_p_ac = '".$idProyectoAccion."' AND ";
	}
	$query.=	"sp.pres_anno = ".$anno_pres." AND ".
				"sp.esta_id <> 2 AND ".
				/*"sp.esta_id <> 15 AND ".*/
				"CAST(sp.paga_fecha AS DATE) BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY') AND ".
				"(sp.fecha_anulacion IS NULL OR (CAST(sp.fecha_anulacion AS DATE) NOT BETWEEN to_date('".$fechaInicio."', 'DD/MM/YYYY') AND to_date('".$fechaFin."', 'DD/MM/YYYY'))) AND ".
				"sp.paga_id = spd.paga_id AND ".
				"sp.pres_anno = spd.pres_anno AND ".
				"spd.padt_id_p_ac = s.id_proyecto_accion AND ".
				"spd.padt_cod_aesp = s.id_accion_especifica AND ".
				//"spd.padt_abono='1' AND ".
				"spd.part_id LIKE '".$codigoPartida."%' AND ".
				"spd.part_id NOT LIKE '4.11.0%' ".
			"GROUP BY spd.part_id ".
			") AS s ".
			"WHERE s.monto_pagado <> 0 ".
			"ORDER BY part_id";
	$resultadoMontosPagados=pg_query($query) or die("Error en los montos pagados");
	
	$totalProgramados = 0;
	$totalRecibidos = 0;
	$totalCedidos = 0;
	$totalDiferidos = 0;
	$totalComprometidos = 0;
	$totalComprometidosAislados = 0;
	$totalCausados = 0;
	$totalPagados = 0;
	$totalDisponible = 0;

	$totalPrimerOrdenProgramados = 0;
	$totalPrimerOrdenRecibidos = 0;
	$totalPrimerOrdenCedidos = 0;
	$totalPrimerOrdenDiferidos = 0;
	$totalPrimerOrdenComprometidos = 0;
	$totalPrimerOrdenComprometidosAislados = 0;
	$totalPrimerOrdenCausados = 0;
	$totalPrimerOrdenPagados = 0;
	$totalPrimerOrdenDisponible = 0;

	$totalSegundoOrdenProgramados = 0;
	$totalSegundoOrdenRecibidos = 0;
	$totalSegundoOrdenCedidos = 0;
	$totalSegundoOrdenDiferidos = 0;
	$totalSegundoOrdenComprometidos = 0;
	$totalSegundoOrdenComprometidosAislados = 0;
	$totalSegundoOrdenCausados = 0;
	$totalSegundoOrdenPagados = 0;
	$totalSegundoOrdenDisponible = 0;

	$programado = 0;
	$recibido = 0;
	$cedido = 0;
	$diferido = 0;
	$comprometido = 0;
	$comprometidoAislado = 0;
	$causado = 0;
	$pagado = 0;
	$montoAjustado = 0;
	$montoDisponible = 0;

	$partidaAnteriorPrimerOrden = "";
	$partidaAnteriorSegundoOrden = "";
	$contenidoPartidaAnteriorPrimerOrden = "";
	$contenidoPartidaAnteriorSegundoOrden = "";

	$tamanoResultado = pg_num_rows($resultadoMontosProgramados);

	if($tamanoResultado>0){
		$contenido .= 	"<tr>".
							"<td class='negrita' width='200px'>Denominaci&oacute;n</td>".			
							"<td class='negrita' width='70px'>Partida</td>".
							"<td class='negrita' width='110px'>Presupuesto Ley</td>".
							"<td class='negrita' width='60px'>Recibido</td>".
							"<td class='negrita' width='60px'>Cedido</td>".
							"<td class='negrita' width='110px'>Presupuesto modif.</td>".
							"<td class='negrita' width='60px'>Apartado</td>".
							"<td class='negrita' width='90px'>Comprometido</td>".
							"<td class='negrita' width='90px'>Compr. aislado</td>".
							"<td class='negrita' width='60px'>Causado</td>".
							"<td class='negrita' width='60px'>Pagado</td>".
							"<td class='negrita' width='60px'>Disponible</td>".
							"<td class='negrita' width='70px'>% Ejecuci&oacute;n</td>".
						"</tr>";
		while($filaProgramados=pg_fetch_array($resultadoMontosProgramados)){
			if($partidaAnteriorPrimerOrden==""){
				$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
			}else if($partidaAnteriorPrimerOrden!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00")){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
					$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}

				if ( 	$totalPrimerOrdenProgramados!=0 ||
						$totalPrimerOrdenRecibidos!=0 ||
						$totalPrimerOrdenCedidos!=0 ||
						$totalPrimerOrdenDiferidos!=0 ||
						$totalPrimerOrdenComprometidos!=0 ||
						$totalPrimerOrdenComprometidosAislados!=0 ||
						$totalPrimerOrdenCausados!=0 ||
						$totalPrimerOrdenPagados!=0 ||
						$totalPrimerOrdenDisponible!=0) {
					
					$contenido .= 	"<tr style='color: #35519B;'>".
										"<td>".$nombrePartida."</td>".
										"<td>".$partidaAnteriorPrimerOrden."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenRecibidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDiferidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidosAislados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCausados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenPagados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDisponible,2,',','.')."</td>".
										"<td align='right'>".$ejecucion."</td>".
									"</tr>".$contenidoPartidaAnteriorPrimerOrden;
				}
				$contenidoPartidaAnteriorPrimerOrden = "";
				$partidaAnteriorPrimerOrden=substr($filaProgramados["part_id"], 0, 4).".00.00.00";
				$totalPrimerOrdenProgramados = 0;
				$totalPrimerOrdenRecibidos = 0;
				$totalPrimerOrdenCedidos = 0;
				$totalPrimerOrdenDiferidos = 0;
				$totalPrimerOrdenComprometidos = 0;
				$totalPrimerOrdenComprometidosAislados = 0;
				$totalPrimerOrdenCausados = 0;
				$totalPrimerOrdenPagados = 0;
				$totalPrimerOrdenDisponible = 0;
			}
			
			if($partidaAnteriorSegundoOrden==""){
				$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
			}else if($partidaAnteriorSegundoOrden!=(substr($filaProgramados["part_id"], 0, 7).".00.00")){
				$nombrePartida = "";
				$iPartidasPrimariasYSecundarias = 0;						
				while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
					if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
						$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
						break;
					}
					$iPartidasPrimariasYSecundarias++;
				}
				if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
					$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
				}else{
					$ejecucion = "0,00%";	
				}
				
				if ( (substr($partidaAnteriorSegundoOrden, 0, 4).".00.00.00")!=(substr($filaProgramados["part_id"], 0, 4).".00.00.00") || $accionEspecificaAnterior!=$filaProgramados["id_accion_especifica"] ) {
					if ( 	$totalSegundoOrdenProgramados!=0 ||
							$totalSegundoOrdenRecibidos!=0 ||
							$totalSegundoOrdenCedidos!=0 ||
							$totalSegundoOrdenDiferidos!=0 ||
							$totalSegundoOrdenComprometidos!=0 ||
							$totalSegundoOrdenComprometidosAislados!=0 ||
							$totalSegundoOrdenCausados!=0 ||
							$totalSegundoOrdenPagados!=0 ||
							$totalSegundoOrdenDisponible!=0) {
						$contenido .= 	"<tr style='color: #005E20;'>".
											"<td>".$nombrePartida."</td>".
											"<td align='center'>".$partidaAnteriorSegundoOrden."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
											"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
											"<td align='right'>".$ejecucion."</td>".
										"</tr>".$contenidoPartidaAnteriorSegundoOrden;
					}
				} else {
					if ( 	$totalSegundoOrdenProgramados!=0 ||
							$totalSegundoOrdenRecibidos!=0 ||
							$totalSegundoOrdenCedidos!=0 ||
							$totalSegundoOrdenDiferidos!=0 ||
							$totalSegundoOrdenComprometidos!=0 ||
							$totalSegundoOrdenComprometidosAislados!=0 ||
							$totalSegundoOrdenCausados!=0 ||
							$totalSegundoOrdenPagados!=0 ||
							$totalSegundoOrdenDisponible!=0) {
						$contenidoPartidaAnteriorPrimerOrden .= "<tr style='color: #005E20;'>".
																	"<td>".$nombrePartida."</td>".
																	"<td align='center'>".$partidaAnteriorSegundoOrden."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
																	"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
																	"<td align='right'>".$ejecucion."</td>".
																"</tr>".$contenidoPartidaAnteriorSegundoOrden;
					}						
				}
				
				$contenidoPartidaAnteriorSegundoOrden = "";
				$partidaAnteriorSegundoOrden=substr($filaProgramados["part_id"], 0, 7).".00.00";
				$totalSegundoOrdenProgramados = 0;
				$totalSegundoOrdenRecibidos = 0;
				$totalSegundoOrdenCedidos = 0;
				$totalSegundoOrdenDiferidos = 0;
				$totalSegundoOrdenComprometidos = 0;
				$totalSegundoOrdenComprometidosAislados = 0;
				$totalSegundoOrdenCausados = 0;
				$totalSegundoOrdenPagados = 0;
				$totalSegundoOrdenDisponible = 0;
			}
		
			$programado = $filaProgramados['monto_programado'];
			$recibido = 0;
			$cedido = 0;
			$diferido = 0;
			$comprometido = 0;
			$comprometidoAislado = 0;
			$causado = 0;
			$pagado = 0;
		
			if($filaRecibidos==null){
				$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
			}
			if($filaProgramados["part_id"]==$filaRecibidos["part_id"]){
				$recibido = $filaRecibidos["monto_recibido"];
				$filaRecibidos=pg_fetch_array($resultadoMontosRecibidos);
			}
				
			if($filaCedidos==null){
				$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
			}
			if($filaProgramados["part_id"]==$filaCedidos["part_id"]){
				$cedido = $filaCedidos["monto_cedido"];
				$filaCedidos=pg_fetch_array($resultadoMontosCedidos);
			}
				
			$montoAjustado=($programado+$recibido)-$cedido;
				
			if($filaDiferidos==null){
				$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
			}
			if($filaProgramados["part_id"]==$filaDiferidos["part_id"]){
				$diferido = $filaDiferidos["monto_diferido"];
				$filaDiferidos=pg_fetch_array($resultadoMontosDiferidos);
			}
				
			if($filaComprometidos==null){
				$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
			}
			if($filaProgramados["part_id"]==$filaComprometidos["part_id"]){
				$comprometido = $filaComprometidos["monto_comprometido"];
				$filaComprometidos=pg_fetch_array($resultadoMontosComprometidos);
			}
	
			if($filaComprometidosAislados==null){
				$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
			}
			if($filaProgramados["part_id"]==$filaComprometidosAislados["part_id"]){
				$comprometidoAislado = $filaComprometidosAislados["monto_comprometido_aislado"];
				$filaComprometidosAislados=pg_fetch_array($resultadoMontosComprometidosAislados);
			}
	
			if($filaCausados==null){
				$filaCausados=pg_fetch_array($resultadoMontosCausados);
			}
			if($filaProgramados["part_id"]==$filaCausados["part_id"]){
				$causado = $filaCausados["monto_causado"];
				$filaCausados=pg_fetch_array($resultadoMontosCausados);
			}
				
			if($filaPagados==null){
				$filaPagados=pg_fetch_array($resultadoMontosPagados);
			}
			if($filaProgramados["part_id"]==$filaPagados["part_id"]){
				$pagado = $filaPagados["monto_pagado"];
				$filaPagados=pg_fetch_array($resultadoMontosPagados);
			}
				
			if($diferido>0){
				$montoDisponible=($montoAjustado)-($diferido)-$comprometidoAislado;
			}else if($comprometidoAislado>0){
				$montoDisponible=($montoAjustado)-($comprometidoAislado);
			}else{
				$montoDisponible=($montoAjustado);
			}
			
			if ( 	$programado!=0 ||
					$recibido!=0 ||
					$cedido!=0 ||
					$diferido!=0 ||
					$comprometido!=0 ||
					$comprometidoAislado!=0 ||
					$causado!=0 ||
					$pagado!=0 ||
					$montoDisponible!=0) {
				
				$totalProgramados += $programado;
				$totalRecibidos +=  $recibido;
				$totalCedidos += $cedido;
				$totalDiferidos += $diferido;
				$totalComprometidos += $comprometido;
				$totalComprometidosAislados += $comprometidoAislado;
				$totalCausados += $causado;
				$totalPagados += $pagado;
				$totalDisponible += $montoDisponible;
					
				$totalPrimerOrdenProgramados += $programado;
				$totalPrimerOrdenRecibidos +=  $recibido;
				$totalPrimerOrdenCedidos += $cedido;
				$totalPrimerOrdenDiferidos += $diferido;
				$totalPrimerOrdenComprometidos += $comprometido;
				$totalPrimerOrdenComprometidosAislados += $comprometidoAislado;
				$totalPrimerOrdenCausados += $causado;
				$totalPrimerOrdenPagados += $pagado;
				$totalPrimerOrdenDisponible += $montoDisponible;
					
				$totalSegundoOrdenProgramados += $programado;
				$totalSegundoOrdenRecibidos +=  $recibido;
				$totalSegundoOrdenCedidos += $cedido;
				$totalSegundoOrdenDiferidos += $diferido;
				$totalSegundoOrdenComprometidos += $comprometido;
				$totalSegundoOrdenComprometidosAislados += $comprometidoAislado;
				$totalSegundoOrdenCausados += $causado;
				$totalSegundoOrdenPagados += $pagado;
				$totalSegundoOrdenDisponible += $montoDisponible;
				
				$contenidoPartidaAnteriorSegundoOrden .= 	"<tr>".
																"<td>".trim($filaProgramados['partida'])."</td>".
																"<td align='right'>".trim($filaProgramados['part_id'])."</td>".
																"<td align='right'>".number_format($programado,2,',','.')."</td>".
																"<td align='right'>".number_format($recibido,2,',','.')."</td>".
																"<td align='right'>".number_format($cedido,2,',','.')."</td>".
																"<td align='right'>".number_format($programado+$recibido-$cedido,2,',','.')."</td>".
																"<td align='right'>".number_format($diferido,2,',','.')."</td>".
																"<td align='right'>".number_format($comprometido,2,',','.')."</td>".
																"<td align='right'>".number_format($comprometidoAislado,2,',','.')."</td>".
																"<td align='right'>".number_format($causado,2,',','.')."</td>".
																"<td align='right'>".number_format($pagado,2,',','.')."</td>".				
																"<td align='right'>".number_format($montoDisponible,2,',','.')."</td>".
																"<td align='right'>";
				if(round($programado+$recibido-$cedido)!=0){
					$contenidoPartidaAnteriorSegundoOrden .= 		number_format(($diferido+$comprometidoAislado)*100/($programado+$recibido-$cedido),2,',','.').'%'; 
				}else{
					$contenidoPartidaAnteriorSegundoOrden .= 		'0,00%';	
				}
				$contenidoPartidaAnteriorSegundoOrden .= 		"</td>".
															"</tr>"; 
			}
		}

		if($partidaAnteriorPrimerOrden!=""){
			$nombrePartida = "";
			$iPartidasPrimariasYSecundarias = 0;						
			while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
				if($partidaAnteriorPrimerOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
					$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
					break;
				}
				$iPartidasPrimariasYSecundarias++;
			}
			if(round($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos)!=0){
				$ejecucion = number_format(($totalPrimerOrdenDiferidos+$totalPrimerOrdenComprometidosAislados)*100/($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos),2,',','.')."%"; 
			}else{
				$ejecucion = "0,00%";	
			}
			
			if ( 	$totalPrimerOrdenProgramados!=0 ||
					$totalPrimerOrdenRecibidos!=0 ||
					$totalPrimerOrdenCedidos!=0 ||
					$totalPrimerOrdenDiferidos!=0 ||
					$totalPrimerOrdenComprometidos!=0 ||
					$totalPrimerOrdenComprometidosAislados!=0 ||
					$totalPrimerOrdenCausados!=0 ||
					$totalPrimerOrdenPagados!=0 ||
					$totalPrimerOrdenDisponible!=0) {
				$contenido .= 	"<tr style='color: #35519B;'>".
										"<td>".$nombrePartida."</td>".
										"<td>".$partidaAnteriorPrimerOrden."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenRecibidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenProgramados+$totalPrimerOrdenRecibidos-$totalPrimerOrdenCedidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDiferidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidos,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenComprometidosAislados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenCausados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenPagados,2,',','.')."</td>".
										"<td align='right'>".number_format($totalPrimerOrdenDisponible,2,',','.')."</td>".
										"<td align='right'>".$ejecucion."</td>".
									"</tr>".$contenidoPartidaAnteriorPrimerOrden;
			}
			$contenidoPartidaAnteriorPrimerOrden = "";
		}
			
		if($partidaAnteriorSegundoOrden!=""){
			$nombrePartida = "";
			$iPartidasPrimariasYSecundarias = 0;						
			while($iPartidasPrimariasYSecundarias<$tamanoPartidasPrimariasYSecundarias) {
				if($partidaAnteriorSegundoOrden==$arregloPartidas[$iPartidasPrimariasYSecundarias][0]){
					$nombrePartida = $arregloPartidas[$iPartidasPrimariasYSecundarias][1];
					break;
				}
				$iPartidasPrimariasYSecundarias++;
			}
			if(round($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos)!=0){
				$ejecucion = number_format(($totalSegundoOrdenDiferidos+$totalSegundoOrdenComprometidosAislados)*100/($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos),2,',','.')."%"; 
			}else{
				$ejecucion = "0,00%";	
			}

			if ( 	$totalSegundoOrdenProgramados!=0 ||
					$totalSegundoOrdenRecibidos!=0 ||
					$totalSegundoOrdenCedidos!=0 ||
					$totalSegundoOrdenDiferidos!=0 ||
					$totalSegundoOrdenComprometidos!=0 ||
					$totalSegundoOrdenComprometidosAislados!=0 ||
					$totalSegundoOrdenCausados!=0 ||
					$totalSegundoOrdenPagados!=0 ||
					$totalSegundoOrdenDisponible!=0) {
				$contenido .= 	"<tr style='color: #005E20;'>".
									"<td>".$nombrePartida."</td>".
									"<td align='center'>".$partidaAnteriorSegundoOrden."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenProgramados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenRecibidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenProgramados+$totalSegundoOrdenRecibidos-$totalSegundoOrdenCedidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenDiferidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenComprometidos,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenComprometidosAislados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenCausados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenPagados,2,',','.')."</td>".
									"<td align='right'>".number_format($totalSegundoOrdenDisponible,2,',','.')."</td>".
									"<td align='right'>".$ejecucion."</td>".
								"</tr>".$contenidoPartidaAnteriorSegundoOrden;
			}
			$contenidoPartidaAnteriorSegundoOrden = "";
		}
		$contenido .=	"<tr class='negrita'>".
							"<td>Total Bs.</td>".
							"<td></td>".
							"<td align='right'>".number_format($totalProgramados,2,',','.')."</td>".
							"<td align='right'>".number_format($totalRecibidos,2,',','.')."</td>".
							"<td align='right'>".number_format($totalCedidos,2,',','.')."</td>".
							"<td align='right'>".number_format($totalProgramados+$totalRecibidos-$totalCedidos,2,',','.')."</td>".
							"<td align='right'>".number_format($totalDiferidos,2,',','.')."</td>".
							"<td align='right'>".number_format($totalComprometidos,2,',','.')."</td>".
							"<td align='right'>".number_format($totalComprometidosAislados,2,',','.')."</td>".
							"<td align='right'>".number_format($totalCausados,2,',','.')."</td>".
							"<td align='right'>".number_format($totalPagados,2,',','.')."</td>".
							"<td align='right'>".number_format($totalDisponible,2,',','.')."</td>".
							"<td align='right'>";
		if(round($totalProgramados+$totalRecibidos-$totalCedidos)!=0){
			$contenido .= 		number_format(($totalDiferidos+$totalComprometidosAislados)*100/($totalProgramados+$totalRecibidos-$totalCedidos),2,',','.').'%'; 
		}else{
			$contenido .= 		'0,00%';	
		}
		$contenido .=		"</td>".
						"</tr>";
	}else{
		$contenido .= 	"<tr class='titular'>".
							"<td colspan='13' align='center' height='40px' valign='middle'>".
								"No se encontraron resultados".
							"</td>".
						"</tr>";
	}
}

$contenido .="</table>";
		
$footer = 	"<style type='text/css'>
				@page {
			 		@bottom-right {
			 			font-size: 8pt;
			 			margin-top: 10mm;
			    		content: 'Página ' counter(page) ' de ' counter(pages);
			  		}
				}
			</style>".
			"<p align='center' style='font-size: 8pt;'>Av. Universidad, Esquina el Chorro, Torre Ministerial, Piso 11, La Hoyada, Caracas<br/>Teléfono: 0212-7718520/7718672 Fax: 0212-7718672<br/>www.infocentro.gob.ve</p><br/>".
			"<span style='align=center;font-family: arial;font-style:italic;font-weight:bold;font-size: 8pt;'>SAFI - Fundación Infocentro</span><br/>".
			"<span style='align=center;font-family: arial;font-size: 8pt;'>".fecha()."</span>";
$properties = array("marginBottom" => 28, "footerHtml" => $footer, "landscape" => true);
convert_to_pdf($contenido, $properties);
pg_close($conexion);
?>