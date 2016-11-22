<?php
ob_start();
session_start();
require("../../includes/constantes.php");
require_once("../../includes/funciones.php");
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$codigoCR = "";
if (isset($_GET['codigoCR']) && $_GET['codigoCR'] != "") {
	$codigoCR = $_GET['codigoCR'];
}
$user_perfil_id = $_SESSION['user_perfil_id'];

/**************************************/
/*if($user_perfil_id!="42456"){//COORDINADOR DE COMPRAS
	$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;
}else{
	$tipoBusq = TIPO_BUSQUEDA_ORDENES_DE_COMPRA;
}*/
$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;
/**************************************/

if (isset($_GET['tipoBusq']) && $_GET['tipoBusq'] != "") {
	$tipoBusq = $_GET['tipoBusq'];
}
$tipoRequisicion = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequisicion = $_GET['tipoRequ'];
}
require("../../includes/conexion.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush();
$pagina = "1";
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
	$pagina = $_GET['pagina'];
}
$proyAcc = "";
if (isset($_GET['proyAcc']) && $_GET['proyAcc'] != "") {
	$proyAcc = $_GET['proyAcc'];
}
$radioProyAcc = "";
if (isset($_GET['radioProyAcc']) && $_GET['radioProyAcc'] != "") {
	$radioProyAcc = $_GET['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_GET['proyecto']) && $_GET['proyecto'] != "") {
		$proyecto = $_GET['proyecto'];
	}		
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_GET['accionCentralizada']) && $_GET['accionCentralizada'] != "") {
		$accionCentralizada = $_GET['accionCentralizada'];
	}		
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}

if ( $tipoBusq == TIPO_BUSQUEDA_REQUISICIONES ) {
	if ( substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150" ) {
		$estado = ESTADO_REQUISICION_DEVUELTAS;
	} else if ( substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150" ) {
		$estado = ESTADO_REQUISICION_NO_REVISADAS;	
	} else if ( $user_perfil_id == "30400" ) {
		$estado = ESTADO_REQUISICION_NO_REVISADAS;
	} else if ( $user_perfil_id == "15456" || $user_perfil_id == "42456" ) {
		$estado = ESTADO_REQUISICION_PENDIENTES;
	}
} else if ( $tipoBusq == TIPO_BUSQUEDA_ORDENES_DE_COMPRA ) {
	$estado = ESTADO_REQUISICION_TODAS;
	if ( $user_perfil_id == "15456" ) {
		$estado = ESTADO_REQUISICION_DEVUELTAS;
	} else if ( $user_perfil_id == "42456" ) {
		$estado = ESTADO_REQUISICION_NO_REVISADAS;
	} else if ( $user_perfil_id == "46450" ) {
		$estado = ESTADO_REQUISICION_NO_REVISADAS;
	} else if ( $user_perfil_id == "30400" ) {
		$estado = ESTADO_REQUISICION_NO_REVISADAS;
	} else if ( $user_perfil_id == "46400" ) {
		$estado = ESTADO_REQUISICION_PENDIENTES;
	}
}
/*if($user_perfil_id == "15456" && $tipoBusq == TIPO_BUSQUEDA_REQUISICIONES){
	$estado = ESTADO_REQUISICION_PENDIENTES;
}else if(($user_perfil_id == "30400" || $user_perfil_id == "46400") && $tipoBusq == TIPO_BUSQUEDA_ORDENES_DE_COMPRA){
	$estado = ESTADO_REQUISICION_PENDIENTES;
}else if($tipoBusq == TIPO_BUSQUEDA_ORDENES_DE_COMPRA){
	$estado = ESTADO_REQUISICION_TODAS;
}else if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){
	$estado = ESTADO_REQUISICION_DEVUELTAS;
}else if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){
	$estado = ESTADO_REQUISICION_NO_REVISADAS;
}else if($user_perfil_id == "42456"){
	$estado = ESTADO_REQUISICION_NO_REVISADAS;
}else if($user_perfil_id == "30400"){
	$estado = ESTADO_REQUISICION_NO_REVISADAS;
}*/
if (isset($_GET['estado']) && $_GET['estado'] != "") {
	$estado = $_GET['estado'];
}
$controlFechas = "";
if (isset($_GET['controlFechas']) && $_GET['controlFechas'] != "") {
	$controlFechas = $_GET['controlFechas'];
}
$fechaInicio = "";
if (isset($_GET['fechaInicio']) && $_GET['fechaInicio'] != "") {
	$fechaInicio = $_GET['fechaInicio'];
}
$fechaFin = "";
if (isset($_GET['fechaFin']) && $_GET['fechaFin'] != "") {
	$fechaFin = $_GET['fechaFin'];
}
$rifProveedor = "";
if (isset($_REQUEST['rifProveedor']) && $_REQUEST['rifProveedor'] != "") {
	$rifProveedor = $_REQUEST['rifProveedor'];
}
$nombreProveedor = "";
if (isset($_REQUEST['nombreProveedor']) && $_REQUEST['nombreProveedor'] != "") {
	$nombreProveedor = $_REQUEST['nombreProveedor'];
}
$idItem = "";
if (isset($_REQUEST['idItem']) && $_REQUEST['idItem'] != "") {
	$idItem = $_REQUEST['idItem'];
}
$nombreItem = "";
if (isset($_REQUEST['nombreItem']) && $_REQUEST['nombreItem'] != "") {
	$nombreItem = $_REQUEST['nombreItem'];
}
$idViatico = "";
if (isset($_REQUEST['idViatico']) && $_REQUEST['idViatico'] != "") {
	$idViatico = $_REQUEST['idViatico'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>...:SAFI:B&uacute;squedas</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<link href="../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
	<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
	<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>	
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<script language="Javascript">
		function comparar_fechas(elemento, tipoBusq){
			if(tipoBusq=="<?= TIPO_BUSQUEDA_REQUISICIONES?>"){
				var fecha_inicial=document.getElementById("txt_inicioR").value;
				var fecha_final=document.getElementById("hid_hasta_itinR").value;
			}
			if(tipoBusq=="<?= TIPO_BUSQUEDA_COTIZACIONES?>"){
				var fecha_inicial=document.getElementById("txt_inicioC").value;
				var fecha_final=document.getElementById("hid_hasta_itinC").value;
			}
			if(tipoBusq=="<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>"){
				var fecha_inicial=document.getElementById("txt_inicioO").value;
				var fecha_final=document.getElementById("hid_hasta_itinO").value;
			}	
			var dia1 =fecha_inicial.substring(0,2);
			var mes1 =fecha_inicial.substring(3,5);
			var anio1=fecha_inicial.substring(6,10);
			
			var dia2 =fecha_final.substring(0,2);
			var mes2 =fecha_final.substring(3,5);
			var anio2=fecha_final.substring(6,10);
	
			dia1 = parseInt(dia1,10);
			mes1 = parseInt(mes1,10);
			anio1= parseInt(anio1,10);
	
			dia2 = parseInt(dia2,10);
			mes2 = parseInt(mes2,10);
			anio2= parseInt(anio2,10); 
				
			if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
			 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
			  alert("La fecha inicial no debe ser mayor a la fecha final"); 
			  elemento.value='';
			  return;
			}
		}

		function habiDesabiFechas(elemento){
			if(elemento.id=="controlFechasR"){
				txt_inicio = document.getElementById("txt_inicioR");
				hid_hasta_itin = document.getElementById("hid_hasta_itinR");
			}
			if(elemento.id=="controlFechasC"){
				txt_inicio = document.getElementById("txt_inicioC");
				hid_hasta_itin = document.getElementById("hid_hasta_itinC");
			}
			if(elemento.id=="controlFechasO"){
				txt_inicio = document.getElementById("txt_inicioO");
				hid_hasta_itin = document.getElementById("hid_hasta_itinO");
			}
			if (elemento.checked==true){ 
				txt_inicio.disabled=false;
				hid_hasta_itin.disabled=false;
			}else{ 
				txt_inicio.disabled=true;
				hid_hasta_itin.disabled=true; 
			}
		}

		function habiDesabiProyAcc(elemento){
			radioProyecto = document.getElementById("radioProyectoR");
			radioAccionCentralizada = document.getElementById("radioAccionR");
			proyecto = document.getElementById("proyectoR");
			accionCentralizada = document.getElementById("accionCentralizadaR");
			if (elemento.checked==true){ 
				radioProyecto.disabled=false;
				radioAccionCentralizada.disabled=false;
			}else{
				radioProyecto.disabled=true;
				radioAccionCentralizada.disabled=true;
				radioProyecto.checked=false;
				radioAccionCentralizada.checked=false;
				proyecto.disabled=true;
				accionCentralizada.disabled=true;
			}
		}

		function habiProyecto(elemento){
			proyecto = document.getElementById("proyectoR");
			accionCentralizada = document.getElementById("accionCentralizadaR");
			proyecto.disabled=false;
			accionCentralizada.disabled=true;
		}
		
		function habiAccionCentralizada(elemento){
			proyecto = document.getElementById("proyectoR");
			accionCentralizada = document.getElementById("accionCentralizadaR");
			accionCentralizada.disabled=false;
			proyecto.disabled=true;
		}

		function listarRequisiciones(pagina){
			codigo = document.getElementById("codigoR").value;
			tipoRequ = document.getElementById("tipoRequR").value;
			proyAcc = document.getElementById("proyAccR").checked;
			radioProyAcc = (document.getElementById("radioProyectoR").checked==true)?document.getElementById("radioProyectoR").value:((document.getElementById("radioAccionR").checked==true)?document.getElementById("radioAccionR").value:"");
			proyecto = document.getElementById("proyectoR").value;
			accionCentralizada = document.getElementById("accionCentralizadaR").value;
			dependencia = (document.getElementById("dependenciaR"))?document.getElementById("dependenciaR").value:"";
			estado = (document.getElementById("estado"))?document.getElementById("estado").value:"";
			fechaInicio = document.getElementById("txt_inicioR").value;
			fechaFin = document.getElementById("hid_hasta_itinR").value;
			controlFechas = document.getElementById('controlFechasR').checked;
			tipoBusq = document.getElementById('tipoBusquedaReq').value;
			var idViatico = document.getElementById('idViatico').value;

			location.href = "busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc+"&idViatico="+idViatico;
		}
		
		<?php if($user_perfil_id == "15456" || $user_perfil_id == "42456"){//Analista de compras, Coordinador de compras?>
		function listarCotizaciones(pagina){
			codigo = document.getElementById("codigoC").value;
			codigoCR = document.getElementById("codigoCR").value;
			tipoRequ = document.getElementById("tipoRequC").value;
			dependencia = (document.getElementById("dependenciaC"))?document.getElementById("dependenciaC").value:"";
			fechaInicio = document.getElementById("txt_inicioC").value;
			fechaFin = document.getElementById("hid_hasta_itinC").value;
			controlFechas = document.getElementById('controlFechasC').checked;
			tipoBusq = document.getElementById('tipoBusquedaCot').value;

			location.href = "busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR;
		}
		function verDetalles(idSoco){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequisicion?>';
			dependencia = '<?= $dependencia?>';
			controlFechas = '<?= $controlFechas?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			pagina = '<?= $pagina?>';
			tipoBusq = '<?= $tipoBusq?>';
			location.href = "../soco/detalleSolicitudCotizacion.php?codigo="+codigo+"&idSoco="+idSoco+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR;
		}

		function verSolicitudesDeCotizacion(idRequ){
			tipoBusq = '<?= TIPO_BUSQUEDA_COTIZACIONES?>';
			location.href = "busquedas.php?codigoCR="+idRequ+"&tipoBusq="+tipoBusq;
		}
		<?php }?>
		<?php if($user_perfil_id == "15456" || $user_perfil_id == "42456" || $user_perfil_id == "46450" || $user_perfil_id == "30400" || $user_perfil_id == "46400"){//Analista de compras, Coordinador de compras, Director Admin y Finanzas, Analista de Presupuesto, Director de Presupuesto?>
		function ordenDeCompra(idRequ){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequisicion?>';
			dependencia = '<?= $dependencia?>';
			estado = '<?= $estado?>';
			controlFechas = '<?= $controlFechas?>';
			nombreProveedor = '<?= $nombreProveedor?>';
			rifProveedor = '<?= $rifProveedor?>';
			nombreItem = '<?= $nombreItem?>';
			idItem = '<?= $idItem?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			pagina = '<?= $pagina?>';
			tipoBusq = '<?= $tipoBusq?>';
			location.href = "../ordc/ordenDeCompra.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&nombreProveedor="+nombreProveedor+"&rifProveedor="+rifProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
		}

		function listarOrdenesDeCompra(pagina){
			codigo = document.getElementById("codigoO").value;
			codigoCR = document.getElementById("codigoOR").value;
			tipoRequ = document.getElementById("tipoRequO").value;
			dependencia = (document.getElementById("dependenciaO"))?document.getElementById("dependenciaO").value:"";
			estado = document.getElementById("estadoO").value;
			nombreProveedor = document.getElementById("inputSelectNombreProveedorO").value;
			rifProveedor = document.getElementById("rifProveedorO").value;
			nombreItem = document.getElementById("inputSelectNombreItemO").value;
			idItem = document.getElementById("idItemO").value;
			fechaInicio = document.getElementById("txt_inicioO").value;
			fechaFin = document.getElementById("hid_hasta_itinO").value;
			controlFechas = document.getElementById('controlFechasO').checked;
			tipoBusq = document.getElementById('tipoBusquedaOrd').value;
			location.href = "busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
		}

		function verOrdenDeCompra(idOrdc, accion){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequisicion?>';
			dependencia = '<?= $dependencia?>';
			estado = '<?= $estado?>';
			controlFechas = '<?= $controlFechas?>';
			nombreProveedor = '<?= $nombreProveedor?>';
			rifProveedor = '<?= $rifProveedor?>';
			nombreItem = '<?= $nombreItem?>';
			idItem = '<?= $idItem?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			pagina = '<?= $pagina?>';
			tipoBusq = '<?= $tipoBusq?>';
			<?php if($user_perfil_id == "15456"){?>
			if(accion){
				location.href = "../ordc/modificarOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}else{
				location.href = "../ordc/detalleOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}
			<?php }else if($user_perfil_id == "42456"){?>
			if(accion){
				location.href = "../ordc/coordinadorOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}else{
				location.href = "../ordc/detalleOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}
			<?php }else if($user_perfil_id == "46450"){?>
			if(accion){
				location.href = "../ordc/gerenteOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}else{
				location.href = "../ordc/detalleOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}
			<?php }else if($user_perfil_id == "30400"){?>
			if(accion){
				location.href = "../ordc/analistaPresupuestoOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}else{
				location.href = "../ordc/detallePresupuestoOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}
			<?php }else if($user_perfil_id == "46400"){?>
			if(accion){
				location.href = "../ordc/directorPresupuestoOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}else{
				location.href = "../ordc/detallePresupuestoOrdenDeCompra.php?codigo="+codigo+"&idOrdc="+idOrdc+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&dependencia="+dependencia+"&codigoCR="+codigoCR+"&estado="+estado+"&rifProveedor="+rifProveedor+"&nombreProveedor="+nombreProveedor+"&idItem="+idItem+"&nombreItem="+nombreItem;
			}
			<?php } ?>	
		}
		<?php }?>
		
		function verArticulos(idRequ, accion){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequisicion?>';
			proyAcc = '<?= $proyAcc?>';
			radioProyAcc = '<?= $radioProyAcc?>';
			proyecto = '<?= $proyecto?>';
			accionCentralizada = '<?= $accionCentralizada?>';
			dependencia = '<?= $dependencia?>';
			estado = '<?= $estado?>';
			controlFechas = '<?= $controlFechas?>';
			fechaInicio = '<?= $fechaInicio?>';
			fechaFin = '<?= $fechaFin?>';
			pagina = '<?= $pagina?>';
			tipoBusq = '<?= $tipoBusq?>';
			<?php if($user_perfil_id == "15456" || $user_perfil_id == "42456"){?>
				location.href = "requisicionAnalistaCompras.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc+"&codigoCR="+codigoCR;
			<?php }else	if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150"){?>
			if(accion){
				location.href = "modificarRequisicion.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}else{
				location.href = "detalleRequisicion.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}
			<?php }else	if(substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){?>
			if(accion){
				location.href = "requisicionGerenteDirector.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}else{
				location.href = "detalleRequisicion.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}
			<?php }else	if($user_perfil_id=="30400"){?>
			if(accion){
				location.href = "requisicionAnalistaPresupuesto.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}else{
				location.href = "detalleRequisicion.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			}
			<?php }/*else if($user_perfil_id=="42456"){?>
				location.href = "detalleRequisicion.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&dependencia="+dependencia+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&radioProyAcc="+radioProyAcc;
			<?php }*/?>
		}

		function cambiarTipoBusqueda(){
			if(document.getElementById("tipoBusquedaReq").checked==true){
				document.getElementById("busquedaReq").style.display="block";
				if(document.getElementById("busquedaCot")){
					document.getElementById("busquedaCot").style.display="none";					
				}
				if(document.getElementById("busquedaOrd")){
					document.getElementById("busquedaOrd").style.display="none";					
				}				
			}else if(document.getElementById("tipoBusquedaCot").checked==true){
				document.getElementById("busquedaCot").style.display="block";
				if(document.getElementById("busquedaReq")){
					document.getElementById("busquedaReq").style.display="none";					
				}
				if(document.getElementById("busquedaOrd")){
					document.getElementById("busquedaOrd").style.display="none";					
				}
			}else if(document.getElementById("tipoBusquedaOrd").checked==true){
				document.getElementById("busquedaOrd").style.display="block";
				if(document.getElementById("busquedaCot")){
					document.getElementById("busquedaCot").style.display="none";					
				}
				if(document.getElementById("busquedaReq")){
					document.getElementById("busquedaReq").style.display="none";					
				}
			}
			return;
		}
		function cambiarCriterioReq(){		
			if(document.getElementById("criterioCodigoR").checked==true){
				document.getElementById("codigoR").disabled = false;
				if(document.getElementById("codigoR").value==""){
					document.getElementById("codigoR").value = "rqui-";
				}
				document.getElementById("tipoRequR").disabled = true;
				if(document.getElementById("proyAccR").checked==true){
					document.getElementById("proyAccR").checked = false;
					habiDesabiProyAcc(document.getElementById("proyAccR"));
				}
				document.getElementById("proyAccR").disabled = true;
				document.getElementById("dependenciaR").disabled = true;
				document.getElementById("estado").disabled = true;
				if(document.getElementById("controlFechasR").checked==true){
					document.getElementById("controlFechasR").checked = false;
					habiDesabiFechas(document.getElementById("controlFechasR"));
				}
				document.getElementById("controlFechasR").disabled = true;
				document.getElementById("idViatico").disabled = true;
			}else if(document.getElementById("criteriosMultiplesR").checked==true){
				document.getElementById("codigoR").disabled = true;
				document.getElementById("codigoR").value = "";
				document.getElementById("tipoRequR").disabled = false;
				document.getElementById("proyAccR").disabled = false;
				document.getElementById("dependenciaR").disabled = false;
				document.getElementById("estado").disabled = false;
				document.getElementById("controlFechasR").disabled = false;
				document.getElementById("idViatico").disabled = false;					
			}
			return;
		}
		function cambiarCriterioCot(){
			if(document.getElementById("criterioCodigoC").checked==true){
				document.getElementById("codigoC").disabled = false;
				if(document.getElementById("codigoC").value=="" && document.getElementById("codigoCR").value==""){
					document.getElementById("codigoC").value = "soco-";
				}
				document.getElementById("codigoCR").disabled = false;
				document.getElementById("tipoRequC").disabled = true;
				document.getElementById("dependenciaC").disabled = true;
				if(document.getElementById("controlFechasC").checked==true){
					document.getElementById("controlFechasC").checked = false;
					habiDesabiFechas(document.getElementById("controlFechasC"));
				}
				document.getElementById("controlFechasC").disabled = true;
			}else if(document.getElementById("criteriosMultiplesC").checked==true){
				document.getElementById("codigoC").disabled = true;
				document.getElementById("codigoCR").disabled = true;
				document.getElementById("codigoC").value = "";
				document.getElementById("codigoCR").value = "";
				document.getElementById("tipoRequC").disabled = false;
				document.getElementById("dependenciaC").disabled = false;
				document.getElementById("controlFechasC").disabled = false;					
			}
			return;
		}
		function cambiarCriterioOrd(){
			if(document.getElementById("criterioCodigoO").checked==true){
				document.getElementById("codigoO").disabled = false;
				/*if(document.getElementById("codigoO").value=="" && document.getElementById("codigoOR").value==""){
					document.getElementById("codigoO").value = "ordc-";					
				}*/
				document.getElementById("codigoOR").disabled = false;
				document.getElementById("tipoRequO").disabled = true;
				document.getElementById("dependenciaO").disabled = true;
				document.getElementById("estadoO").disabled = true;
				document.getElementById("inputSelectNombreProveedorO").disabled = true;
				document.getElementById("rifProveedorO").disabled = true;
				document.getElementById("inputSelectNombreItemO").disabled = true;
				document.getElementById("idItemO").disabled = true;
				if(document.getElementById("controlFechasO").checked==true){
					document.getElementById("controlFechasO").checked = false;
					habiDesabiFechas(document.getElementById("controlFechasO"));
				}
				document.getElementById("controlFechasO").disabled = true;

				document.getElementById("tipoRequO").selectedIndex = 0;
				document.getElementById("dependenciaO").selectedIndex = 0;
				document.getElementById("estadoO").value = '<?= ESTADO_REQUISICION_TODAS?>';
				document.getElementById("inputSelectNombreProveedorO").value = '';
				document.getElementById("rifProveedorO").value = '';
				document.getElementById("inputSelectNombreItemO").value = '';
				document.getElementById("idItemO").value = '';
				
			}else if(document.getElementById("criteriosMultiplesO").checked==true){
				document.getElementById("codigoO").disabled = true;
				document.getElementById("codigoOR").disabled = true;
				document.getElementById("codigoO").value = "";
				document.getElementById("codigoOR").value = "";
				document.getElementById("tipoRequO").disabled = false;
				document.getElementById("dependenciaO").disabled = false;
				document.getElementById("estadoO").disabled = false;
				document.getElementById("inputSelectNombreProveedorO").disabled = false;
				document.getElementById("rifProveedorO").disabled = false;
				document.getElementById("inputSelectNombreItemO").disabled = false;
				document.getElementById("idItemO").disabled = false;
				document.getElementById("controlFechasO").disabled = false;					
			}
			return;
		}

		function onLoad(){
			var objInputNombreItem = $("#inputSelectNombreItemO");
			var sendInputIdItem = "#idItemO";
			var errorIdNombreItem = "#errorNombreItem";

			objInputNombreItem.autocomplete({
				source: function(request, response){
					$.ajax({
						url: "../../acciones/ordc/ordc.php",
						dataType: "json",
						data: {
							accion: "SearchItems",
							key: request.term
						},
						success: function(json){
							var index = 0;
							var items = new Array();
							$(sendInputIdItem)[0].value="";
							$(errorIdNombreItem)[0].innerHTML="";
							
							$.each(json.listaItems, function(idNombreItem, objNombreItem){
								items[index++] = {
										id: idNombreItem,
										label: objNombreItem.nombre,
										value: objNombreItem.nombre
								};
							});
							if(items.length==0){
								$(errorIdNombreItem)[0].innerHTML="Rubro inv&aacute;lido.";
							}
							response(items);
						}
					});
				},
				minLength: 1,
				select: function(event, ui) {
					seleccionarItem({
						id: ui.item.id,
						nombre: ui.item.value,
						sendInputId: sendInputIdItem,
						objInput: objInputNombreItem
					});
					return false;
				}
			});

			var objInputNombreProveedor = $("#inputSelectNombreProveedorO");
			var sendInputIdRifProveedor = "#rifProveedorO";
			var errorIdNombreProveedor = "#errorNombreProveedor";

			objInputNombreProveedor.autocomplete({
				source: function(request, response){
					$.ajax({
						url: "../../acciones/ordc/ordc.php",
						dataType: "json",
						data: {
							accion: "SearchProveedores",
							key: request.term
						},
						success: function(json){
							var index = 0;
							var proveedores = new Array();
							$(sendInputIdRifProveedor)[0].value="";
							$(errorIdNombreProveedor)[0].innerHTML="";
							
							$.each(json.listaProveedores, function(rifProveedor, objProveedor){
								proveedores[index++] = {
										id: rifProveedor,
										label: objProveedor.nombre,
										value: objProveedor.nombre
								};
							});
							if(proveedores.length==0){
								$(errorIdNombreProveedor)[0].innerHTML="Proveedor inv&aacute;lido.";
							}
							response(proveedores);
						}
					});
				},
				minLength: 1,
				select: function(event, ui) {
					seleccionarItem({
						id: ui.item.id,
						nombre: ui.item.value,
						sendInputId: sendInputIdRifProveedor,
						objInput: objInputNombreProveedor
					});
					return false;
				}
			});

			function seleccionarItem(params){
				$(params['sendInputId'])[0].value=params['id'];
				$(params['objInput'])[0].value=params['nombre'];
			}
		}
	</script>
</head>
<body class="normal" onload="onLoad();">
<table width="100%" align="center">
	<tr>
		<td align="center">			
	<?php
		if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
	?>
	<div style="text-align: center;display: none;" class="normal peq_naranja">
		<input type="radio" id="tipoBusquedaReq" name="tipoBusq" value="<?= TIPO_BUSQUEDA_REQUISICIONES?>" <?php if($tipoBusq==TIPO_BUSQUEDA_REQUISICIONES){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>Requisiciones
		<input type="radio" id="tipoBusquedaCot" name="tipoBusq" value="<?= TIPO_BUSQUEDA_COTIZACIONES?>" <?php if($tipoBusq==TIPO_BUSQUEDA_COTIZACIONES){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>Solicitudes de Cotizaci&oacute;n
		<input type="radio" id="tipoBusquedaOrd" name="tipoBusq" value="<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>" <?php if($tipoBusq==TIPO_BUSQUEDA_ORDENES_DE_COMPRA){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>&Oacute;rdenes de Compra
	</div>
	<?php
		}/*else if($user_perfil_id == "42456"){
	?>
	<div style="text-align: center;display: none;" class="normal peq_naranja">
		&nbsp;
		<div style="display: none;">
			<input type="radio" id="tipoBusquedaReq" name="tipoBusq" value="<?= TIPO_BUSQUEDA_REQUISICIONES?>" />
			<input type="radio" id="tipoBusquedaCot" name="tipoBusq" value="<?= TIPO_BUSQUEDA_COTIZACIONES?>" />
			<input type="radio" id="tipoBusquedaOrd" name="tipoBusq" value="<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>" checked='checked'/>
		</div>
	</div>
	<?php
		}*/else if($user_perfil_id == "46450" || $user_perfil_id == "46400" || $user_perfil_id == "30400"){
	?>
	<div style="text-align: center;display: none;" class="normal peq_naranja">
		<input type="radio" id="tipoBusquedaReq" name="tipoBusq" value="<?= TIPO_BUSQUEDA_REQUISICIONES?>" <?php if($tipoBusq==TIPO_BUSQUEDA_REQUISICIONES){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>Requisiciones
		<div style="display: none;">
			<input type="radio" id="tipoBusquedaCot" name="tipoBusq" value="<?= TIPO_BUSQUEDA_COTIZACIONES?>" <?php if($tipoBusq==TIPO_BUSQUEDA_COTIZACIONES){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>Solicitudes de Cotizaci&oacute;n
		</div>
		<input type="radio" id="tipoBusquedaOrd" name="tipoBusq" value="<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>" <?php if($tipoBusq==TIPO_BUSQUEDA_ORDENES_DE_COMPRA){echo "checked='checked'";}?> onclick="cambiarTipoBusqueda();"/>&Oacute;rdenes de Compra
	</div>
	<?php
		}else{
	?>
	<div style="text-align: center;" class="normal peq_naranja">
		&nbsp;
		<div style="display: none;">
			<input type="radio" id="tipoBusquedaReq" name="tipoBusq" value="<?= TIPO_BUSQUEDA_REQUISICIONES?>" checked='checked'/>
			<input type="radio" id="tipoBusquedaCot" name="tipoBusq" value="<?= TIPO_BUSQUEDA_COTIZACIONES?>" />
			<input type="radio" id="tipoBusquedaOrd" name="tipoBusq" value="<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>" />
		</div>
	</div>
	<?php
		}
	/*?>
	<?php 
		if($user_perfil_id != "42456"){//COORDINADOR DE COMPRAS*/
	?>
	<div id="busquedaReq" <?php if($tipoBusq!=TIPO_BUSQUEDA_REQUISICIONES){echo "style='display: none;'";}?>>
		<form id="formRequisiciones" name="formRequisiciones">
			<input type="hidden" id="paginaR" name="pagina" value="<?= $pagina?>"/>
			<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="1" name="criterioR" id="criterioCodigoR" onclick="cambiarCriterioReq();" <?php if($codigo && $codigo!=""){echo "checked='checked'";}?>/>
						B&uacute;squeda por c&oacute;digo
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>C&oacute;digo de requisici&oacute;n:</td>
					<td>
						<input class="normalNegro" type="text" id="codigoR" name="codigo" <?php if($tipoBusq==TIPO_BUSQUEDA_REQUISICIONES){echo "value='".$codigo."'";}?> onkeyup="validarCodigo(this);"/>
					</td>
				</tr>
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="2" name="criterioR" id="criteriosMultiplesR" onclick="cambiarCriterioReq();" <?php if(!$codigo || $codigo==""){echo "checked='checked'";}?>/>
						B&uacute;squeda por criterios m&uacute;ltiples
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Tipo de requisici&oacute;n:</td>
					<td>
						<select class="normalNegro" id="tipoRequR" name="tipoRequ">
							<option value="<?=TIPO_REQUISICION_TODAS?>" <?php if($tipoRequisicion == TIPO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>Todas</option>
							<option value="<?=TIPO_REQUISICION_COMPRA?>" <?php if($tipoRequisicion == TIPO_REQUISICION_COMPRA){ echo "selected='selected'";} ?>>Compra</option>
							<option value="<?=TIPO_REQUISICION_SERVICIO?>" <?php if($tipoRequisicion == TIPO_REQUISICION_SERVICIO){ echo "selected='selected'";} ?>>Servicio</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td><input type="checkbox" id="proyAccR" name="proyAccR" onclick="habiDesabiProyAcc(this);" <?= ($proyAcc=="true")?"checked='checked'":""?>/>Imputaci&oacute;n</td>
					<td>
						<input value="proyecto" type="radio" id="radioProyectoR" name="radioProyAccR" onclick="habiProyecto(this);" <?= ($proyAcc!="true")?"disabled='true'":""?> <?= ($radioProyAcc=="proyecto")?"checked='checked'":""?>/>Proyecto
						<select class="normalNegro" id="proyectoR" name="proyectoR" <?= ($proyAcc!="true" || $radioProyAcc!="proyecto")?"disabled='true'":""?>>
							<option value="" <?php if($proyecto == ""){ echo "selected='selected'";} ?>>todos</option>
							<?php
								$query = 	"SELECT sp.proy_titulo,spae.proy_id,spae.paes_id,spae.paes_nombre,spae.centro_gestor,spae.centro_costo ".
											"FROM sai_proyecto sp,sai_proy_a_esp spae ".
											"WHERE ".
												"sp.proy_id = spae.proy_id AND ".
												"pres_anno = ".$_SESSION['an_o_presupuesto']." ".
											"ORDER BY spae.paes_nombre";
								$resultado = pg_exec($conexion, $query);
								$numeroFilas = pg_numrows($resultado);
								for($i = 0; $i < $numeroFilas; $i++) {
									$row = pg_fetch_array($resultado, $i);
							?>
									<option value="<?= $row["proy_id"]."-".$row["paes_id"]?>" <?php if($proyecto == $row["proy_id"]."-".$row["paes_id"]){ echo "selected='selected'";} ?>><?= "(".$row["centro_gestor"]."-".$row["centro_costo"].")".((strlen($row["paes_nombre"])>50)?substr($row["paes_nombre"],0,50):$row["paes_nombre"])?></option>
							<?php 
								}
							?>
						</select>
						<br/>
						<input value="accionCentralizada" type="radio" id="radioAccionR" name="radioProyAccR" onclick="habiAccionCentralizada(this);" <?= ($proyAcc!="true")?"disabled='true'":""?> <?= ($radioProyAcc=="accionCentralizada")?"checked='checked'":""?>/>Acci&oacute;n Centralizada
						<select class="normalNegro" id="accionCentralizadaR" name="accionCentralizadaR" <?= ($proyAcc!="true" || $radioProyAcc!="accionCentralizada")?"disabled='true'":""?>>
							<option value="" <?php if($accionCentralizada == ""){ echo "selected='selected'";} ?>>todas</option>
							<?php
							$query = 	"SELECT aces_nombre,acce_id,aces_id,centro_gestor,centro_costo ".
										"FROM sai_acce_esp ".
										"WHERE pres_anno = ".$_SESSION['an_o_presupuesto']." ".
										"ORDER BY aces_nombre";
							$resultado = pg_exec($conexion, $query);
							$numeroFilas = pg_numrows($resultado);
							for($i = 0; $i < $numeroFilas; $i++) {
								$row = pg_fetch_array($resultado, $i);
							?>
									<option value="<?= $row["acce_id"]."-".$row["aces_id"]?>" <?php if($accionCentralizada == $row["acce_id"]."-".$row["aces_id"]){ echo "selected='selected'";} ?>><?= $row["acce_id"]."-".((strlen($row["aces_nombre"])>50)?substr($row["aces_nombre"],0,50):$row["aces_nombre"])?></option>
							<?php 
								}
							?>
						</select>
					</td>
				</tr>
				<?php
				if($user_perfil_id == "15456" || $user_perfil_id == "42456" || $user_perfil_id == "30400"){
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Dependencia:</td>
					<td>
						<select class="normalNegro" id="dependenciaR" name="dependenciaR">
							<option value="" <?php if($dependencia == ""){ echo "selected='selected'";} ?>>todas</option>
							<?php
							$nivelOficinaGerencia = "4";
							$query = 	"SELECT depe_id,depe_nombre ".
										"FROM sai_dependenci ".
										"WHERE depe_nivel = ".$nivelOficinaGerencia." ".
										"ORDER BY depe_nombre";
							$resultado = pg_exec($conexion, $query);
							$numeroFilas = pg_numrows($resultado);
							for($i = 0; $i < $numeroFilas; $i++) {
								$row = pg_fetch_array($resultado, $i);
							?>
								<option value="<?= $row["depe_id"]?>" <?php if($dependencia == $row["depe_id"]){ echo "selected='selected'";} ?>><?=$row["depe_nombre"]?></option>
							<?php 
								}
							?>
						</select>
					</td>
				</tr>
				<?php
				}else{
				?>
				<div style="display: none;">
					<select class="normalNegro" id="dependenciaR" name="dependenciaR">
						<option value="" selected='selected'>todas</option>
					</select>
				</div>
				<?php
				}
				?>
				<?php
				if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estado" name="estado">
							<option value="<?=ESTADO_REQUISICION_PENDIENTES?>" <?php if($estado == ESTADO_REQUISICION_PENDIENTES){ echo "selected='selected'";} ?>>pendientes (sin solicitudes de cotizaci&oacute;n)</option>
							<option value="<?=ESTADO_REQUISICION_ENVIADAS?>" <?php if($estado == ESTADO_REQUISICION_ENVIADAS){ echo "selected='selected'";} ?>>con solicitudes de cotizaci&oacute;n enviadas por <?=$_SESSION['solicitante']?></option>
							<option value="<?=ESTADO_REQUISICION_ENVIADAS_POR_OTROS?>" <?php if($estado == ESTADO_REQUISICION_ENVIADAS_POR_OTROS){ echo "selected='selected'";} ?>>con solicitudes de cotizaci&oacute;n enviadas por otros analistas</option>					
						</select>
					</td>
				</tr>
				<?php
				}else if(substr($user_perfil_id, 0, 2)=="37" || $user_perfil_id == "38350" || $user_perfil_id == "68150" || substr($user_perfil_id, 0, 2)=="60" || substr($user_perfil_id, 0, 2)=="46" || $user_perfil_id == "47350" || $user_perfil_id == "65150"){//ASISTENTE ADMINISTRATIVO, GERENTE O DIRECTOR 
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estado" name="estado">
							<option value="<?=ESTADO_REQUISICION_NO_REVISADAS?>" <?php if($estado == ESTADO_REQUISICION_NO_REVISADAS){ echo "selected='selected'";} ?>>en tr&aacute;nsito</option>
							<option value="<?=ESTADO_REQUISICION_APROBADAS?>" <?php if($estado == ESTADO_REQUISICION_APROBADAS){ echo "selected='selected'";} ?>>aprobadas</option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS){ echo "selected='selected'";} ?>>devueltas</option>
							<option value="<?=ESTADO_REQUISICION_ANULADAS?>" <?php if($estado == ESTADO_REQUISICION_ANULADAS){ echo "selected='selected'";} ?>>anuladas</option>
							<option value="<?=ESTADO_REQUISICION_EN_BORRADOR?>" <?php if($estado == ESTADO_REQUISICION_EN_BORRADOR){ echo "selected='selected'";} ?>>borrador</option>
							<option value="<?=ESTADO_REQUISICION_TODAS?>" <?php if($estado == ESTADO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>					
						</select>
					</td>
				</tr>
				<?php
				}else if($user_perfil_id == "30400"){//ANALISTA DE PRESUPUESTO
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estado" name="estado">
							<option value="<?=ESTADO_REQUISICION_NO_REVISADAS?>" <?php if($estado == ESTADO_REQUISICION_NO_REVISADAS){ echo "selected='selected'";} ?>>no revisadas</option>
							<option value="<?=ESTADO_REQUISICION_APROBADAS?>" <?php if($estado == ESTADO_REQUISICION_APROBADAS){ echo "selected='selected'";} ?>>aprobadas</option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){ echo "selected='selected'";} ?>>devueltas por <?=$_SESSION['solicitante']?></option>
							<option value="<?=ESTADO_REQUISICION_ANULADAS?>" <?php if($estado == ESTADO_REQUISICION_ANULADAS){ echo "selected='selected'";} ?>>anuladas</option>
							<option value="<?=ESTADO_REQUISICION_TODAS?>" <?php if($estado == ESTADO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>
						</select>
					</td>
				</tr>
				<?php
				}else{
				?>
				<div style="display: none;">
					<select class="normalNegro" id="estado" name="estado">
						<option value="<?=ESTADO_REQUISICION_PENDIENTES?>" <?php if($estado == ESTADO_REQUISICION_PENDIENTES){ echo "selected='selected'";} ?>>pendientes (sin solicitudes de cotizaci&oacute;n)</option>
						<option value="<?=ESTADO_REQUISICION_ENVIADAS?>" <?php if($estado == ESTADO_REQUISICION_ENVIADAS){ echo "selected='selected'";} ?>>con solicitudes de cotizaci&oacute;n enviadas por <?=$_SESSION['solicitante']?></option>
						<option value="<?=ESTADO_REQUISICION_ENVIADAS_POR_OTROS?>" <?php if($estado == ESTADO_REQUISICION_ENVIADAS_POR_OTROS){ echo "selected='selected'";} ?>>con solicitudes de cotizaci&oacute;n enviadas por otros analistas</option>					
					</select>
				</div>
				<?php
				}
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Vi&aacute;tico:</td>
					<td>
						<input class="normalNegro" type="text" id="idViatico" name="idViatico"/>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td><input type="checkbox" id="controlFechasR" name="controlFechas" <?= ($controlFechas=="true")?"checked='checked'":""?> onclick="habiDesabiFechas(this);"/>Elaboradas entre:</td>
					<td>
						<input type="text" size="10" id="txt_inicioR" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_REQUISICIONES?>');" readonly="readonly" value="<?= $fechaInicio?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasR').checked==true){g_Calendar.show(event, 'txt_inicioR');}" 
							title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
						<input type="text" size="10" id="hid_hasta_itinR" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_REQUISICIONES?>');" readonly="readonly" value="<?= $fechaFin?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasR').checked==true){g_Calendar.show(event, 'hid_hasta_itinR');}" 
							title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input class="normalNegro" type="button" value="Buscar" onclick="listarRequisiciones(1);"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php
		/*}
	?>
	<?php */
		if($user_perfil_id == "15456" || $user_perfil_id == "42456"){
	?>
	<div id="busquedaCot" <?php if($tipoBusq!=TIPO_BUSQUEDA_COTIZACIONES){echo "style='display: none;'";}?>>
		<form id="formCotizaciones" name="formCotizaciones">
			<input type="hidden" id="paginaC" name="pagina" value="<?= $pagina?>"/>
			<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="1" name="criterioC" id="criterioCodigoC" onclick="cambiarCriterioCot();" <?php if(($codigo && $codigo!="") || ($codigoCR && $codigoCR!="")){echo "checked='checked'";}?>/>
						B&uacute;squeda por c&oacute;digo
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>C&oacute;digo de solicitud:</td>
					<td>
						<input class="normalNegro" type="text" id="codigoC" name="codigo" <?php if($tipoBusq==TIPO_BUSQUEDA_COTIZACIONES){echo "value='".$codigo."'";}?> onkeyup="validarCodigo(this);"/>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>C&oacute;digo de requisici&oacute;n:</td>
					<td>
						<input class="normalNegro" type="text" id="codigoCR" name="codigoCR" <?php if($tipoBusq==TIPO_BUSQUEDA_COTIZACIONES){echo "value='".$codigoCR."'";}?> onkeyup="validarCodigo(this);"/>
					</td>
				</tr>
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="2" name="criterioC" id="criteriosMultiplesC" onclick="cambiarCriterioCot();" <?php if((!$codigo || $codigo=="") && (!$codigoCR || $codigoCR=="")){echo "checked='checked'";}?>/>
						B&uacute;squeda por criterios m&uacute;ltiples
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Tipo de requisici&oacute;n:</td>
					<td>
						<select class="normalNegro" id="tipoRequC" name="tipoRequ">
							<option value="<?=TIPO_REQUISICION_TODAS?>" <?php if($tipoRequisicion == TIPO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>
							<option value="<?=TIPO_REQUISICION_COMPRA?>" <?php if($tipoRequisicion == TIPO_REQUISICION_COMPRA){ echo "selected='selected'";} ?>>compra</option>
							<option value="<?=TIPO_REQUISICION_SERVICIO?>" <?php if($tipoRequisicion == TIPO_REQUISICION_SERVICIO){ echo "selected='selected'";} ?>>servicio</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Dependencia:</td>
					<td>
						<select class="normalNegro" id="dependenciaC" name="dependenciaC">
							<option value="" <?php if($dependencia == ""){ echo "selected='selected'";} ?>>todas</option>
							<?php
							$nivelOficinaGerencia = "4";
							$query = 	"SELECT depe_id,depe_nombre ".
										"FROM sai_dependenci ".
										"WHERE depe_nivel = ".$nivelOficinaGerencia." ".
										"ORDER BY depe_nombre";
							$resultado = pg_exec($conexion, $query);
							$numeroFilas = pg_numrows($resultado);
							for($i = 0; $i < $numeroFilas; $i++) {
								$row = pg_fetch_array($resultado, $i);
							?>
								<option value="<?= $row["depe_id"]?>" <?php if($dependencia == $row["depe_id"]){ echo "selected='selected'";} ?>><?=$row["depe_nombre"]?></option>
							<?php 
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td><input type="checkbox" id="controlFechasC" name="controlFechas" <?= ($controlFechas=="true")?"checked='checked'":""?> onclick="habiDesabiFechas(this);"/>Elaboradas entre:</td>
					<td>
						<input type="text" size="10" id="txt_inicioC" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_COTIZACIONES?>');" readonly="readonly" value="<?= $fechaInicio?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasC').checked==true){g_Calendar.show(event, 'txt_inicioC');}" 
							title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
						<input type="text" size="10" id="hid_hasta_itinC" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_COTIZACIONES?>');" readonly="readonly" value="<?= $fechaFin?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasC').checked==true){g_Calendar.show(event, 'hid_hasta_itinC');}" 
							title="Show popup calendar">
								<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input class="normalNegro" type="button" value="Buscar" onclick="listarCotizaciones(1);"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php 
		}
	?>
	<?php 
		if($user_perfil_id == "15456" || $user_perfil_id == "42456" || $user_perfil_id == "46450" || $user_perfil_id == "30400" || $user_perfil_id == "46400"){
	?>
	<div id="busquedaOrd" <?php if($tipoBusq!=TIPO_BUSQUEDA_ORDENES_DE_COMPRA){echo "style='display: none;'";}?>>
		<form id="formOrdenesDeCompra" name="formOrdenesDeCompra">
			<input type="hidden" id="paginaO" name="pagina" value="<?= $pagina?>"/>
			<table class="tablaalertas" background="../../imagenes/fondo_tabla.gif">
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="1" name="criterioO" id="criterioCodigoO" onclick="cambiarCriterioOrd();" <?php if(($codigo && $codigo!="") || ($codigoCR && $codigoCR!="")){echo "checked='checked'";}?>/>
						B&uacute;squeda por c&oacute;digo
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>C&oacute;digo de orden de compra:</td>
					<td>
						<input class="normalNegro" type="text" id="codigoO" name="codigo" <?php if($tipoBusq==TIPO_BUSQUEDA_ORDENES_DE_COMPRA){echo "value='".$codigo."'";}?> onkeyup="validarCodigo(this);"/>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>C&oacute;digo de requisici&oacute;n:</td>
					<td>
						<input class="normalNegro" type="text" id="codigoOR" name="codigoOR" <?php if($tipoBusq==TIPO_BUSQUEDA_ORDENES_DE_COMPRA){echo "value='".$codigoCR."'";}?> onkeyup="validarCodigo(this);"/>
					</td>
				</tr>
				<tr class="td_gray">
					<td class="normalNegroNegrita" colspan="3">
						<input type="radio" value="2" name="criterioO" id="criteriosMultiplesO" onclick="cambiarCriterioOrd();" <?php if((!$codigo || $codigo=="") && (!$codigoCR || $codigoCR=="")){echo "checked='checked'";}?>/>
						B&uacute;squeda por criterios m&uacute;ltiples
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Tipo de requisici&oacute;n:</td>
					<td>
						<select class="normalNegro" id="tipoRequO" name="tipoRequ">
							<option value="<?=TIPO_REQUISICION_TODAS?>" <?php if($tipoRequisicion == TIPO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>
							<option value="<?=TIPO_REQUISICION_COMPRA?>" <?php if($tipoRequisicion == TIPO_REQUISICION_COMPRA){ echo "selected='selected'";} ?>>compra</option>
							<option value="<?=TIPO_REQUISICION_SERVICIO?>" <?php if($tipoRequisicion == TIPO_REQUISICION_SERVICIO){ echo "selected='selected'";} ?>>servicio</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Dependencia:</td>
					<td>
						<select class="normalNegro" id="dependenciaO" name="dependenciaO">
							<option value="" <?php if($dependencia == ""){ echo "selected='selected'";} ?>>todas</option>
							<?php
							$nivelOficinaGerencia = "4";
							$query = 	"SELECT depe_id,depe_nombre ".
										"FROM sai_dependenci ".
										"WHERE depe_nivel = ".$nivelOficinaGerencia." ".
										"ORDER BY depe_nombre";
							$resultado = pg_exec($conexion, $query);
							$numeroFilas = pg_numrows($resultado);
							for($i = 0; $i < $numeroFilas; $i++) {
								$row = pg_fetch_array($resultado, $i);
							?>
								<option value="<?= $row["depe_id"]?>" <?php if($dependencia == $row["depe_id"]){ echo "selected='selected'";} ?>><?=$row["depe_nombre"]?></option>
							<?php 
								}
							?>
						</select>
					</td>
				</tr>
				<?php
				if($user_perfil_id=="15456" || $user_perfil_id=="42456" || $user_perfil_id=="46450"){//ANALISTA DE COMPRAS, COORDINACION DE COMPRAS, DIRECTOR DE ADMIN Y FINAN
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estadoO" name="estadoO">
							<option value="<?=ESTADO_REQUISICION_NO_REVISADAS?>" <?php if($estado == ESTADO_REQUISICION_NO_REVISADAS){ echo "selected='selected'";} ?>>en transito</option>
							<option value="<?=ESTADO_REQUISICION_APROBADAS?>" <?php if($estado == ESTADO_REQUISICION_APROBADAS){ echo "selected='selected'";} ?>>aprobadas</option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS){ echo "selected='selected'";} ?>>devueltas</option>
							<option value="<?=ESTADO_REQUISICION_ANULADAS?>" <?php if($estado == ESTADO_REQUISICION_ANULADAS){ echo "selected='selected'";} ?>>anuladas</option>
							<option value="<?=ESTADO_REQUISICION_TODAS?>" <?php if($estado == ESTADO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>					
						</select>
					</td>
				</tr>
				<?php
				}else if($user_perfil_id=="30400"){//ANALISTA DE PRESUPUESTO
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estadoO" name="estadoO">
							<option value="<?=ESTADO_REQUISICION_NO_REVISADAS?>" <?php if($estado == ESTADO_REQUISICION_NO_REVISADAS){ echo "selected='selected'";} ?>>no revisadas</option>
							<option value="<?=ESTADO_REQUISICION_PENDIENTES?>" <?php if($estado == ESTADO_REQUISICION_PENDIENTES){ echo "selected='selected'";} ?>>en transito</option>
							<option value="<?=ESTADO_REQUISICION_APROBADAS?>" <?php if($estado == ESTADO_REQUISICION_APROBADAS){ echo "selected='selected'";} ?>>aprobadas</option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS_POR_USUARIO){ echo "selected='selected'";} ?>>devueltas por <?=$_SESSION['solicitante']?></option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS){ echo "selected='selected'";} ?>>devueltas desde Direcci&oacute;n de Presupuesto</option>
							<option value="<?=ESTADO_REQUISICION_ANULADAS?>" <?php if($estado == ESTADO_REQUISICION_ANULADAS){ echo "selected='selected'";} ?>>anuladas</option>
							<option value="<?=ESTADO_REQUISICION_TODAS?>" <?php if($estado == ESTADO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>
						</select>
					</td>
				</tr>
				<?php
				}else if($user_perfil_id=="46400"){//DIRECTOR DE PRESUPUESTO
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Estado:</td>
					<td>
						<select class="normalNegro" id="estadoO" name="estadoO">
							<option value="<?=ESTADO_REQUISICION_PENDIENTES?>" <?php if($estado == ESTADO_REQUISICION_PENDIENTES){ echo "selected='selected'";} ?>>en transito</option>
							<option value="<?=ESTADO_REQUISICION_APROBADAS?>" <?php if($estado == ESTADO_REQUISICION_APROBADAS){ echo "selected='selected'";} ?>>aprobadas</option>
							<option value="<?=ESTADO_REQUISICION_DEVUELTAS?>" <?php if($estado == ESTADO_REQUISICION_DEVUELTAS){ echo "selected='selected'";} ?>>devueltas</option>
							<option value="<?=ESTADO_REQUISICION_TODAS?>" <?php if($estado == ESTADO_REQUISICION_TODAS){ echo "selected='selected'";} ?>>todas</option>
						</select>
					</td>
				</tr>
				<?php
				}
				if($user_perfil_id!="30400" && $user_perfil_id!="46400"){
				?>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Proveedor:</td>
					<td>
						<input id="inputSelectNombreProveedorO" class="normalNegro" size="50" maxlength="200" value="<?= $nombreProveedor?>"/>
						<input id="rifProveedorO" name="rifProveedorO" type="hidden" value="<?= $rifProveedor?>"/>
						<div style="width: 340px; color: red; float: right;margin-top: 4px;" id="errorNombreProveedor"></div>
					</td>
				</tr>
				<tr>
					<td width="40">&nbsp;</td>
					<td>Rubro:</td>
					<td>
						<input id="inputSelectNombreItemO" class="normalNegro" size="50" maxlength="200" value="<?= $nombreItem?>"/>
						<input id="idItemO" name="idItemO" type="hidden" value="<?= $idItem?>"/>
						<div style="width: 340px; color: red; float: right;margin-top: 4px;" id="errorNombreItem"></div>
					</td>
				</tr>
				<?php 
				}else{
					echo '<input type="hidden" id="inputSelectNombreProveedorO" name="inputSelectNombreProveedorO" value=""/>';
					echo '<input type="hidden" id="rifProveedorO" name="rifProveedorO" value=""/>';
					echo '<input type="hidden" id="inputSelectNombreItemO" name="inputSelectNombreItemO" value=""/>';
					echo '<input type="hidden" id="idItemO" name="idItemO" value=""/>';
				} ?>
				<tr>
					<td width="40">&nbsp;</td>
					<td><input type="checkbox" id="controlFechasO" name="controlFechas" <?= ($controlFechas=="true")?"checked='checked'":""?> onclick="habiDesabiFechas(this);"/>Elaboradas entre:</td>
					<td>
						<input type="text" size="10" id="txt_inicioO" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>');" readonly="readonly" value="<?= $fechaInicio?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasO').checked==true){g_Calendar.show(event, 'txt_inicioO');}" 
							title="Show popup calendar">
							<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
						<input type="text" size="10" id="hid_hasta_itinO" class="dateparse" onfocus="javascript: comparar_fechas(this,'<?= TIPO_BUSQUEDA_ORDENES_DE_COMPRA?>');" readonly="readonly" value="<?= $fechaFin?>" <?= ($controlFechas!="true")?"disabled='true'":""?>/>
						<a href="javascript:void(0);" 
							onclick="if(document.getElementById('controlFechasO').checked==true){g_Calendar.show(event, 'hid_hasta_itinO');}" 
							title="Show popup calendar">
							<img src="../../js/lib/calendarPopup/img/calendar.gif" 
								class="cp_img" 
								alt="Open popup calendar"/>
						</a>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<input class="normalNegro" type="button" value="Buscar" onclick="listarOrdenesDeCompra(1);"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php 
		}
		//if($user_perfil_id != "42456"){
	?>
	<script>
		cambiarCriterioReq();
	</script>
	<?php 
		//}
		if($user_perfil_id == "15456"){
	?>
	<script>
		cambiarCriterioCot();
	</script>
	<?php
		}
		if($user_perfil_id == "15456" || $user_perfil_id == "42456" || $user_perfil_id == "46450" || $user_perfil_id == "30400" || $user_perfil_id == "46400"){
	?>
	<script>
		cambiarCriterioOrd();
	</script>
	<?php
		}
		if($tipoBusq==TIPO_BUSQUEDA_REQUISICIONES){
	?>
	<div id="divRequisiciones">
	<?php
		include("listadoRequisiciones.php");
	?>
	</div>
	<?php
		}
		if(($user_perfil_id == "15456" || $user_perfil_id == "42456") && $tipoBusq==TIPO_BUSQUEDA_COTIZACIONES){
	?>
	<div id="divCotizaciones">
	<?php
		include("listadoSolicitudesCotizacion.php");
	?>
	</div>
	<?php
		}
		if(($user_perfil_id == "15456" || $user_perfil_id == "42456" || $user_perfil_id == "46450" || $user_perfil_id == "30400" || $user_perfil_id == "46400") && $tipoBusq==TIPO_BUSQUEDA_ORDENES_DE_COMPRA){
	?>
	<div id="divOrdenesDeCompra">
	<?php
		include("listadoOrdenesCompra.php");
	?>
	</div>
	<?php
		}
	?>
		</td>
	</tr>
</table>
</body>
</html>
<?php pg_close($conexion); ?>