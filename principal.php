<?php
ob_start();
session_start();
include(dirname(__FILE__) . '/init.php');
include('includes/conexion.php');
include('includes/constantes.php');
include('includes/perfiles/cambiarPerfil.php');
if(isset($_POST['contrasena']) && isset($_POST['usuario'])){
	include('includes/sesiones.php');
}
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

$perfilActual=$_SESSION['user_perfil_id'];

require_once("includes/tabmenu/tabmenuItems.php");
require("includes/perfiles/constantesPerfiles.php");
require("includes/tabmenu/tabmenuItemsStructure.php");

?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>.:SAFI:Sistema Administrativo de la Fundaci&oacute;n Infocentro</title>
<link rel="stylesheet" type="text/css" href="css/safi0.2.css"/>
<link rel="stylesheet" type="text/css" href="js/menu/ddsmoothmenu.css"/>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
	charset="utf-8"></script>

<script type="text/javascript"
	src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
	charset="utf-8"></script>
	
<link rel="stylesheet" type="text/css" href="js/menu/ddsmoothmenu.css"/>
<script type="text/javascript" src="js/ddtabmenufiles/ddtabmenu.js"></script>
<link rel="stylesheet" type="text/css" href="js/ddtabmenufiles/ddtabmenu.css"/>	
	
<link
	href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />


<script type="text/javascript" src="js/menu/ddsmoothmenu.js">

ddtabmenu.definemenu("ddtabs1", 0);
/***********************************************
* Smooth Navigational Menu- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/
</script>


<link rel="stylesheet" type="text/css" href="js/perfiles/perfiles.css"/>
<script type="text/javascript" src="js/perfiles/dropdown.js"></script>
<script type="text/javascript" src="js/constantes.js"></script>

<script type="text/javascript">
	ddsmoothmenu.init({
		mainmenuid: "smoothmenu",
		orientation: 'h',
		classname: 'ddsmoothmenu',
		contentsource: "markup"
	});
</script>


   <script type="text/javascript">
	
	$().ready(function() {

		});

	</script>



<script language="javascript">


	var menuActual = "";
	var tabmenuItemActual = "";
	function abrir(url,menu,tabmenu,tabmenuItem){

		new Ajax.Request('includes/validarSesionAjax.php',
				{
					method:'get',
					onSuccess: function(transport){
						var response = transport.responseText;

						
						if(response == "false"){

							location.reload();

					 }
					}
				}); 
		
		if(url!='#'){
			window.open(url,'contenido');
		}else{
			window.open('bienvenida.php','contenido');
		}
		seleccionarMenu(menu);
		new Ajax.Request('includes/migajas/migajas.php',
				{
					method:'get',
					parameters: {tabmenu: tabmenu},
					onSuccess: function(transport){
						var response = transport.responseText;
						$("migajas").innerHTML = response;
					}
				});
		
		new Ajax.Request('includes/tabmenu/tabmenu.php',
			{
				method:'get',
				parameters: {tabmenu: tabmenu,tabmenuItem: tabmenuItem},
				onSuccess: function(transport){
					var response = transport.responseText;
					$("tabmenu").innerHTML = response;
					if(tabmenuItem && tabmenuItem!=""){
						tabmenuItemActual = tabmenuItem;
					}
				}
			});
	}
	function abrirTabmenuItem(url,tabmenuItem){

		new Ajax.Request('includes/validarSesionAjax.php',
				{
					method:'get',
					onSuccess: function(transport){
						var response = transport.responseText;

						
						if(response == "false"){

							location.reload();

					 }
					}
				}); 
		
		if(url!='#'){
			window.open(url,'contenido');
		}else{
			window.open('bienvenida.php','contenido');
		}
		seleccionarTabmenuItem(tabmenuItem);
	}
	function cambiarPerfil(perfil/*elemento*/){
		//perfil = elemento.options[elemento.selectedIndex].id;
		if(confirm(pACUTE+'Est'+aACUTE+' seguro que desea cambiar su perfil?')){
			$("perfil").value = perfil;
			$("form1").submit();
		}
	}
	function seleccionarMenu(menu){
		if(menuActual!=""){
			$(menuActual).style.borderTop="3px solid  #E0EFFC";
			
		}
		menuActual = menu;

		//$(menuActual).css('color','#52865A');
		
		$(menuActual).style.borderTop="3px solid  #0772B9";

		
	}
	function seleccionarTabmenuItem(tabmenuItem){
		if(tabmenuItemActual!=""){
		$(tabmenuItemActual).style.color="#848484";
		$(tabmenuItemActual).style.webkitBorderRadius="0";
		$(tabmenuItemActual).style.mozBorderRadius="0";
		$(tabmenuItemActual).style.borderRadius="0";
		$(tabmenuItemActual).style.border="1px solid #C2C2C2";

		}
		tabmenuItemActual = tabmenuItem;
		$(tabmenuItemActual).style.color="#575757";
		$(tabmenuItemActual).style.webkitBorderRadius="6px";
		$(tabmenuItemActual).style.mozBorderRadius="6px";
		$(tabmenuItemActual).style.borderRadius="6px";
		$(tabmenuItemActual).style.border="1px solid #575757";

	}
	function salir(){
		location.href = 'salir.php';
	}
</script>

<script type="text/javascript" src="js/lib/prototype.js"></script>
</head>
	

<body>
<img style="  width:100%;
    height: 100%;
    top:0;
    left:0;
    position:fixed;
    z-index: -1;" src="imagenes/bienvenida-index.jpg" alt="background image" />
<form action="principal.php" method="post" id="form1">
	<input type="hidden" id="perfil" name="perfil" value=""/>
</form>


<!-- 
<img src="imagenes/banner-safi0.2.jpg" width="100%"/>
 -->
 
<img src="imagenes/Banner-Safi-Modificacion.png" width="100%" style="height: 70px;" />

<div class="ddsmoothmenu" id="smoothmenu">

<div class="sesiondiv" style="float: right;margin-top: 8px; margin-right:5px;">

	<span  style="font-size: 11px;color: #6E6E6E; font-family: monospace;"><?echo $_SESSION['solicitante'] ;?></span>&nbsp;|&nbsp;<a  style="font-size: 12px;color: #2E2E2E;" href="javascript: salir(); "> <span >Salir</span></a>

</div>
<ul>
<?php  if ($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_ADMINISTRATIVO_ADMINISTRACION || $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_ADMINISTRATIVO_OTH || $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESUPUESTO) {
	?>
  <li style="z-index: 2000; height: 28px;">
	<a id="menuProcesosAdmin" href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PROCESOS_ADMINISTRATIVOS][TABMENUITEM_PROCESOS_ADMINISTRATIVOS_REGISTRO]["URL"]?>','menuProcesosAdmin','<?= TABMENU_PROCESOS_ADMINISTRATIVOS?>','<?= TABMENUITEM_PROCESOS_ADMINISTRATIVOS_REGISTRO?>');">Procesos Administrativos</a>
  </li>
<?php } if (($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) || ($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_ORDENACION_PAGOS) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) || ($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_COORDINADOR) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_JEFE) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) || ($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE)){
	?>
	<li style="z-index: 2000; height: 28px;">
	<?php  if (($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) || ($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_ORDENACION_PAGOS) ||  ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS)){?>
		<a id="menuSolicitudPagos" href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_SOLICITUD_PAGOS][TABMENUITEM_SOLICITUD_PAGOS_BANDEJA]["URL"]?>','menuSolicitudPagos','<?= TABMENU_SOLICITUD_PAGOS?>','<?= TABMENUITEM_SOLICITUD_PAGOS_BANDEJA?>');">Solicitud de pagos</a>
	<?php }else { if ($_SESSION['user_perfil_id'] ==PERFIL_TESORERO){?>
	<a id="menuSolicitudPagos" href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_SOLICITUD_PAGOS][TABMENUITEM_SOLICITUD_PAGOS_ANULACION]["URL"]?>','menuSolicitudPagos','<?= TABMENU_SOLICITUD_PAGOS?>','<?= TABMENUITEM_SOLICITUD_PAGOS_ANULACION?>');">Solicitud de pagos</a>
	<?php }else {?>
	<a id="menuSolicitudPagos" href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_SOLICITUD_PAGOS][TABMENUITEM_SOLICITUD_PAGOS_ESTADIA]["URL"]?>','menuSolicitudPagos','<?= TABMENU_SOLICITUD_PAGOS?>','<?= TABMENUITEM_SOLICITUD_PAGOS_ESTADIA?>');">Solicitud de pagos</a>
	</li><?php }}
}
	if(($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE)  || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_ORDENACION_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) || ($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_NACIONAL) || ($_SESSION['user_perfil_id'] == PERFIL_AUDITOR) || ($_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) || ($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)){?>
	<li style="z-index: 1900; height: 28px;">
		<a id="menuContabilidad" href="#" style="padding-right: 23px;" class="">
			Contabilidad<img class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
			<?php if(($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_ORDENACION_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS)){?>
			<li style="z-index: 1899;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPROBANTE_DIARIO][TABMENUITEM_COMPROBANTE_DIARIO_INSERTAR]["URL"]?>','menuContabilidad','<?= TABMENU_COMPROBANTE_DIARIO?>','<?= TABMENUITEM_COMPROBANTE_DIARIO_INSERTAR?>');">Comprobante diario</a></li>
			<?php }
			if(($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_ORDENACION_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD))
			{ ?>
			<li style="z-index: 1897;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_CONTABILIDAD_RETENCIONES][TABMENUITEM_CONTABILIDAD_RETENCIONES]["URL"]?>','menuContabilidad','<?= TABMENU_CONTABILIDAD_RETENCIONES?>','<?= TABMENUITEM_CONTABILIDAD_RETENCIONES?>');">Retenciones</a></li>
			<?php }
			if(($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_ORDENACION_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) || ($_SESSION['user_perfil_id'] == PERFIL_AUDITOR) || ($_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) ){								 			
			?>
			<li style="z-index: 1896;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_CONTABILIDAD_REPORTES][TABMENUITEM_REPORTES_CONTABILIDAD_DOCUMENTO]["URL"]?>','menuContabilidad','<?= TABMENU_CONTABILIDAD_REPORTES?>','<?= TABMENUITEM_REPORTES_CONTABILIDAD_DOCUMENTO?>');">Reportes</a></li>
			<?php }
			if(($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)){
				?>
						<li style="z-index: 1896;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_CONTABILIDAD_REPORTES][TABMENUITEM_REPORTES_FUENTE_FINANCIAMIENTO2]["URL"]?>','menuContabilidad','<?= TABMENU_CONTABILIDAD_REPORTES?>','<?= TABMENUITEM_REPORTES_FUENTE_FINANCIAMIENTO2?>');">Reportes</a></li>
			<?php }?>
						
			
			
		</ul>
	</li>
	<?php }
	 if (	(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) ||
	 		($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) ||  
	 		(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_JEFE) || 
	 		(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) || 
	 		(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) || 
	 		(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_COORDINADOR) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || 
	 		($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || 
	 		($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO) || 
	 		($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL) || 
	 		($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_CONTABLE)|| 
	 		($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL_MOVIL) ||
	 		($_SESSION['user_perfil_id'] ==PERFIL_AUDITOR) || 
	 		($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_ORDENACION_PAGOS) ||  
	 		($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_ORDENACION_PAGOS)
	 					    ){?>
	<li style="z-index: 1800; height: 28px;">
		<a id="menuPresupuesto" href="#" style="padding-right: 23px;" class="">
			Presupuesto<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul class="ulMenu">
		<?php
		if ((substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) || ($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) || ($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_JEFE) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) || ($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) || ($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) || (substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_COORDINADOR) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL) || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_CONTABLE)|| ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL_MOVIL) || ($_SESSION['user_perfil_id'] == PERFIL_AUDITOR) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS)){
			if (($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) ||
					($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_CONTABLE)){?>
			<li style="z-index: 1799;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PRESUPUESTO_PUNTO_CUENTA][TABMENUITEM_PUNTO_CUENTA_BUSQUEDA]["URL"]?>','menuPresupuesto','<?= TABMENU_PRESUPUESTO_PUNTO_CUENTA?>','<?= TABMENUITEM_PUNTO_CUENTA_BUSQUEDA?>');">Punto de cuenta</a></li>
			<?
			}else if ((substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
					($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) ||
					($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) ||
					(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_COORDINADOR) ||
					(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_JEFE) ||
					(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) ||
					(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) ||
					 ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL_MOVIL)||
					 ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_NACIONAL)||
					($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) ||
					($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) ){
?>
   <li style="z-index: 1799;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PRESUPUESTO_PUNTO_CUENTA][TABMENUITEM_PUNTO_CUENTA_BANDEJA]["URL"]?>','menuPresupuesto','<?= TABMENU_PRESUPUESTO_PUNTO_CUENTA?>','<?= TABMENUITEM_PUNTO_CUENTA_BANDEJA?>');">Punto de cuenta</a></li>	
<?php
} 
if(($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_AUDITOR) || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_CONTABLE) || ($_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_ORDENACION_CONTABILIDAD) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS)){
?>
<li style="z-index: 1798;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PRESUPUESTO_COMPROMISO][TABMENUITEM_COMPROMISO_BUSQUEDA]["URL"]?>','menuPresupuesto','<?= TABMENU_PRESUPUESTO_COMPROMISO?>','<?= TABMENUITEM_COMPROMISO_BUSQUEDA?>');">Compromiso</a></li>
<?php  if (($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_PRESUPUESTO)){?>
<li style="z-index: 1698;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PRESUPUESTO_MODIFICACIONES_PRESUPUESTARIAS][TABMENUITEM_MODIFICACIONES_PRESUPUESTARIAS_BANDEJA]["URL"]?>','menuPresupuesto','<?= TABMENU_PRESUPUESTO_MODIFICACIONES_PRESUPUESTARIAS?>','<?= TABMENUITEM_MODIFICACIONES_PRESUPUESTARIAS_BANDEJA?>');">Modificaciones presupuestarias</a></li>
<?php }			}
			//	($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) 
			if(	(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) ||
				($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_JEFE) ||
				($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) || 
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) || 
				($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) || 
				($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_NACIONAL_MOVIL) ||
				($_SESSION['user_perfil_id'] == PERFIL_AUDITOR) ||
				($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_CONTABILIDAD) ||
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) ||  
				($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_ORDENACION_PAGOS)){?>
			<li style="z-index: 1794;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_PRESUPUESTO_REPORTES][TABMENUITEM_DISPONIBILIDAD_PRESUPUESTARIA]["URL"]?>','menuPresupuesto','<?= TABMENU_PRESUPUESTO_REPORTES?>','<?= TABMENUITEM_DISPONIBILIDAD_PRESUPUESTARIA?>');">Reportes</a></li>
			<?php
			}
		}?>			
		</ul>
	</li>
	<?php }
	
	if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA || $_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE || $_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_CONTABILIDAD || $_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS){?>
	
	<li style="z-index: 1700; height: 28px;">
		<a id="menuTesoreria" href="#" style="padding-right: 23px;" class="">
			Tesorer&iacute;a<img class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA) {?>		
			<li style="z-index: 1698;"><a href="javascript: abrir('acciones/sopg/sopg.php?accion=IniciarPago','menuTesoreria','<?= TABMENU_TESORERIA_INICIAR_PAGO?>','');">Iniciar Pago</a></li>
			
			<?php }?>	
			<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA || $_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>
			<li style="z-index: 1697;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_TESORERIA_PAGO_CHEQUE][TABMENUITEM_TESORERIA_PAGO_CHEQUE_BANDEJA]["URL"]?>','menuTesoreria','<?= TABMENU_TESORERIA_PAGO_CHEQUE?>','<?= TABMENUITEM_TESORERIA_PAGO_CHEQUE_BANDEJA?>');">Pago con cheque</a></li>
			<?php }?>
			<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA || $_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>			
			<li style="z-index: 1697;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_TESORERIA_PAGO_TRANSFERENCIA][TABMENUITEM_TESORERIA_PAGO_TRANSFERENCIA_BANDEJA]["URL"]?>','menuTesoreria','<?= TABMENU_TESORERIA_PAGO_TRANSFERENCIA?>','<?= TABMENUITEM_TESORERIA_PAGO_TRANSFERENCIA_BANDEJA?>');">Pago con Transferencia</a></li>
			<?php }?>			
			<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_CONTABILIDAD) {?>			
			<li style="z-index: 1697;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_TESORERIA_CONCILIACION_BANCARIA][TABMENUITEM_CONCILIACION_BANCARIA_CONCILIAR]["URL"]?>','menuTesoreria','<?= TABMENU_TESORERIA_CONCILIACION_BANCARIA?>','<?= TABMENUITEM_CONCILIACION_BANCARIA_CONCILIAR?>');">Conciliaci&oacute;n bancaria</a></li>
			<?}?>
			<li style="z-index: 1696;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_TESORERIA_REPORTES][TABMENUITEM_REPORTES_BUSQUEDA_MULTIPLE]["URL"]?>','menuTesoreria','<?= TABMENU_TESORERIA_REPORTES?>','<?= TABMENUITEM_REPORTES_BUSQUEDA_MULTIPLE?>');">Reportes</a></li>
		</ul>
	</li>
	<?php  }
	if	(	(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
			($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) ||
			($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) ||
			(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) ||
			(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) ||
			($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) ||
			($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE) ||
			($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) ||
			($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_COMPRAS) ||
			($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_COMPRAS) ||
			($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_ORDENACION_PAGOS)			
				){
	?>
	<li style="z-index: 1600; height: 28px;">
		<a id="menuCompras" href="#" style="padding-right: 23px;" class="">
			Compras<img class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
		<?php
			//REQUISICIONES
			if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BANDEJA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BANDEJA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BANDEJA]["PERFILES"])){
		?>
				<li style="z-index: 1599;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BANDEJA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_REQUISICION?>','<?= TABMENUITEM_REQUISICION_BANDEJA?>');">Requisici&oacute;n</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BUSQUEDA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BUSQUEDA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BUSQUEDA]["PERFILES"])){
		?>
				<li style="z-index: 1599;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_BUSQUEDA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_REQUISICION?>','<?= TABMENUITEM_REQUISICION_BUSQUEDA?>');">Requisici&oacute;n</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_INSERTAR]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_INSERTAR]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_INSERTAR]["PERFILES"])){
		?>
				<li style="z-index: 1599;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_INSERTAR]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_REQUISICION?>','<?= TABMENUITEM_REQUISICION_INSERTAR?>');">Requisici&oacute;n</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_ESTADIA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_ESTADIA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_ESTADIA]["PERFILES"])){
		?>
				<li style="z-index: 1599;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_REQUISICION][TABMENUITEM_REQUISICION_ESTADIA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_REQUISICION?>','<?= TABMENUITEM_REQUISICION_ESTADIA?>');">Requisici&oacute;n</a></li>
		<?php
			}
			
			//SOLICITUD COTIZACION
			if(	sizeof($tabmenuItemsArray[TABMENU_COMPRAS_SOLICITUD_COTIZACION][TABMENUITEM_SOLICITUD_COTIZACION_BUSQUEDA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_SOLICITUD_COTIZACION][TABMENUITEM_SOLICITUD_COTIZACION_BUSQUEDA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_SOLICITUD_COTIZACION][TABMENUITEM_SOLICITUD_COTIZACION_BUSQUEDA]["PERFILES"])){
		?>
				<li style="z-index: 1598;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_SOLICITUD_COTIZACION][TABMENUITEM_SOLICITUD_COTIZACION_BUSQUEDA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_SOLICITUD_COTIZACION?>','<?= TABMENUITEM_SOLICITUD_COTIZACION_BUSQUEDA?>');">Solicitud de cotizaci&oacute;n</a></li>
		<?php
			}
			
			//ORDEN DE COMPRA
			if(	sizeof($tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BANDEJA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BANDEJA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BANDEJA]["PERFILES"])){
		?>
				<li style="z-index: 1597;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BANDEJA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_ORDEN_COMPRA?>','<?= TABMENUITEM_ORDEN_COMPRA_BANDEJA?>');">Orden de compra</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BUSQUEDA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BUSQUEDA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BUSQUEDA]["PERFILES"])){
		?>
				<li style="z-index: 1597;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_BUSQUEDA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_ORDEN_COMPRA?>','<?= TABMENUITEM_ORDEN_COMPRA_BUSQUEDA?>');">Orden de compra</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_INSERTAR]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_INSERTAR]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_INSERTAR]["PERFILES"])){
		?>
				<li style="z-index: 1597;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_INSERTAR]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_ORDEN_COMPRA?>','<?= TABMENUITEM_ORDEN_COMPRA_INSERTAR?>');">Orden de compra</a></li>
		<?php
			}else if(sizeof($tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_ESTADIA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_ESTADIA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_ESTADIA]["PERFILES"])){
		?>
				<li style="z-index: 1597;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMPRAS_ORDEN_COMPRA][TABMENUITEM_ORDEN_COMPRA_ESTADIA]["URL"]?>','menuCompras','<?= TABMENU_COMPRAS_ORDEN_COMPRA?>','<?= TABMENUITEM_ORDEN_COMPRA_ESTADIA?>');">Orden de compra</a></li>
		<?php
			}
		?>
		</ul>
	</li>
	<?php
		}
		/****************************************************************************************************************
		 * Menú de viaticos
		 ****************************************************************************************************************/

		if	((substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) ||
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_COMPRAS) ||
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE) ||
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_ORDENACION_PAGOS) ||
				($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_COMPRAS) ||
				($_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) ||
				($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO) ||
				($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO) ||
				($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA) ||
				($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_ORDENACION_PAGOS) ||
				($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO) ||
				($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) ||
				($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO) ||
				($_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE)
			)
		{
			// Establecer la pestaña por defecto (bandeja, insertar, buscar) para cada perfil 
			if(	(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_ASISTENTE_ADMINISTRATIVO) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_DIRECTOR) ||
				(substr($_SESSION['user_perfil_id'],0,2)."000" == PERFIL_GERENTE) ||
				$_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO ||
				$_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_EJECUTIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_EJECUTIVO ||
				$_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESIDENCIA ||
				$_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE
			){
				$tabMenuItemViaticosNacionalesDisplay = TABMENUITEM_VIATICOS_NACIONALES_BANDEJA;
				$tabMenuItemRendicionViaticosNacionalesDisplay = TABMENUITEM_RENDICIONES_BANDEJA;
				$tabMenuItemAvancesDisplay = TABMENUITEM_AVANCES_BANDEJA;
				$tabMenuItemRendicionAvancesDisplay = TABMENUITEM_RENDICIONES_AVANCES_BANDEJA;
				
			} else {
				$tabMenuItemViaticosNacionalesDisplay = TABMENUITEM_VIATICOS_NACIONALES_BUSQUEDA;
				$tabMenuItemRendicionViaticosNacionalesDisplay = TABMENUITEM_RENDICIONES_BUSQUEDA;
				$tabMenuItemAvancesDisplay = TABMENUITEM_AVANCES_BUSQUEDA;
				$tabMenuItemRendicionAvancesDisplay = TABMENUITEM_RENDICIONES_AVANCES_BUSQUEDA;
				
			}
	?>
	<li style="z-index: 1500; height: 28px;">
		<a id="menuViaticosRendiciones" href="#" style="padding-right: 23px;" class="">
			Vi&aacute;ticos/Avances<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
			<li style="z-index: 1499;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_VIATICOS_RENDICIONES_VIATICOS_NACIONALES][$tabMenuItemViaticosNacionalesDisplay]["URL"]?>','menuViaticosRendiciones','<?= TABMENU_VIATICOS_RENDICIONES_VIATICOS_NACIONALES?>','<?= $tabMenuItemViaticosNacionalesDisplay?>');">Vi&aacute;ticos nacionales</a></li>
			<?php if ($_SESSION['user_perfil_id'] != PERFIL_ANALISTA_COMPRAS && $_SESSION['user_perfil_id'] != PERFIL_COORDINADOR_COMPRAS) {?>
			<li style="z-index: 1498;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_VIATICOS_RENDICIONES_RENDICIONES][$tabMenuItemRendicionViaticosNacionalesDisplay]["URL"]?>','menuViaticosRendiciones','<?= TABMENU_VIATICOS_RENDICIONES_RENDICIONES?>','<?= $tabMenuItemRendicionViaticosNacionalesDisplay?>');">Rendicion de vi&aacute;ticos nacionales</a></li>
			<li style="z-index: 1497;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_VIATICOS_RENDICIONES_AVANCES][$tabMenuItemAvancesDisplay]["URL"]?>','menuViaticosRendiciones','<?= TABMENU_VIATICOS_RENDICIONES_AVANCES?>','<?= $tabMenuItemAvancesDisplay?>');">Avances</a></li>
			<li style="z-index: 1496;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_VIATICOS_RENDICIONES_RENDICIONES_AVANCES][$tabMenuItemRendicionAvancesDisplay]["URL"]?>','menuViaticosRendiciones','<?= TABMENU_VIATICOS_RENDICIONES_RENDICIONES_AVANCES?>','<?= $tabMenuItemRendicionAvancesDisplay?>');">Rendici&oacute;n de avances</a></li>
			<?php }?>
			<!--
			<li style="z-index: 1495;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_VIATICOS_RENDICIONES_VIATICOS_INTERNACIONALES][TABMENUITEM_VIATICOS_INTERNACIONALES_BANDEJA]["URL"]?>','menuViaticosRendiciones','<?= TABMENU_VIATICOS_RENDICIONES_VIATICOS_INTERNACIONALES?>','<?= TABMENUITEM_VIATICOS_INTERNACIONALES_BANDEJA?>');">Vi&aacute;ticos internacionales</a></li>
			 -->
		</ul>
	</li>
	<?php
		}
		/****************************************************************************************************************
		 * Fin de Menú de viaticos
		 ****************************************************************************************************************/
	?>
	<?php if ($_SESSION['user_perfil_id']==PERFIL_CONTRATADO_COORDINACION_RED_NACIONAL){?>
	<li style="z-index: 1450;height: 28px;">
		<a id="menuMemos" href="#" style="padding-right: 23px;" class="">
			Memos<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
			<li style="z-index: 1449;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_MEMO][TABMENUITEM_MEMO]["URL"]?>','menuMemos','<?= TABMENU_MEMO?>','<?= TABMENUITEM_MEMO?>');">Memo</a></li>
		</ul>
	</li>
	<?php }?>
	<li style="z-index: 1400;height: 28px;">
		<a id="menuComunicaciones" href="#" style="padding-right: 23px;" class="">
			Comunicaciones<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
			<?php 
			if(	sizeof($tabmenuItemsArray[TABMENU_COMUNICACIONES_MEMORANDOS][TABMENUITEM_COMUNICACIONES_MEMORANDOS_BANDEJA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMUNICACIONES_MEMORANDOS][TABMENUITEM_COMUNICACIONES_MEMORANDOS_BANDEJA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMUNICACIONES_MEMORANDOS][TABMENUITEM_COMUNICACIONES_MEMORANDOS_BANDEJA]["PERFILES"])){
			?>
			<li style="z-index: 1399;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMUNICACIONES_MEMORANDOS][TABMENUITEM_COMUNICACIONES_MEMORANDOS_BANDEJA]["URL"]?>','menuComunicaciones','<?= TABMENU_COMUNICACIONES_MEMORANDOS?>','<?= TABMENUITEM_COMUNICACIONES_MEMORANDOS_BANDEJA?>');">Memorandos</a></li>
			<?php
			}
			/*if(	sizeof($tabmenuItemsArray[TABMENU_COMUNICACIONES_OFICIOS][TABMENUITEM_COMUNICACIONES_OFICIOS_BANDEJA]["PERFILES"])==0
				|| in_array($perfilActual, $tabmenuItemsArray[TABMENU_COMUNICACIONES_OFICIOS][TABMENUITEM_COMUNICACIONES_OFICIOS_BANDEJA]["PERFILES"])
				|| in_array((substr($perfilActual, 0, 2)."000"), $tabmenuItemsArray[TABMENU_COMUNICACIONES_OFICIOS][TABMENUITEM_COMUNICACIONES_OFICIOS_BANDEJA]["PERFILES"])){
			?>
			<li style="z-index: 1398;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_COMUNICACIONES_OFICIOS][TABMENUITEM_COMUNICACIONES_OFICIOS_BANDEJA]["URL"]?>','menuComunicaciones','<?= TABMENU_COMUNICACIONES_OFICIOS?>','<?= TABMENUITEM_COMUNICACIONES_OFICIOS_BANDEJA?>');">Oficios</a></li>
			<?php
			}*/
			?>
		</ul>
	</li>
	<!--
	<li style="z-index: 1500; height: 28px;">
		<a id="menuCajaChica" href="#" style="padding-right: 23px;" class="">
			Caja chica<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul	class="ulMenu">
			<li style="z-index: 1499;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_CAJA_CHICA_APERTURA][TABMENUITEM_CAJA_CHICA_APERTURA_INSERTAR]["URL"]?>','menuCajaChica','<?= TABMENU_CAJA_CHICA_APERTURA?>','<?= TABMENUITEM_CAJA_CHICA_APERTURA_INSERTAR?>');">Apertura</a></li>
		</ul>
	</li>
	-->
<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] == PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES
|| $_SESSION['user_perfil_id'] == PERFIL_PRESIDENTE || $_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA || $_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_EJECUTIVO
|| $_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO
|| $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_I_TECNOLOGIA || $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_II_TECNOLOGIA || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_SEG_Y_CONTROL
|| $_SESSION['user_perfil_id'] ==PERFIL_GERENTE_TECNOLOGIA) {?>	
	
<li style="z-index: 1400;height: 28px;"><a id="menuActivos" href="#" style="padding-right: 23px;" class=""> 
	Activos<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/></a>
  <ul class="ulMenu">
	<?php 	
	  if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] == PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES)
	  {
	  	$tabMenuItemRecursosManejoBienesDisplay = TABMENUITEM_MANEJO_BIEN_INGRESAR;
	  	$tabMenuItemRecursosAccionesBienDisplay = TABMENUITEM_ACCIONES_BANDEJA_BIEN;
	  	$tabMenuItemRecursosReaccionBienDisplay = TABMENUITEM_RECURSOS_REASIGNACION_BIEN;
	  	$tabMenuItemRecursosGarantiaBienDisplay = TABMENUITEM_BIENES_REPORTES_GARANTIA;
	  	
	  	if($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES){
	  		$tabMenuItemRecursosManejoBienesDisplay = TABMENUITEM_MANEJO_BIEN_BUSCAR;
	  		$tabMenuItemRecursosAccionesBienDisplay = TABMENUITEM_MANEJO_INVENTARIO_REPORTES_ACTAS;
	  		$tabMenuItemRecursosReaccionBienDisplay = TABMENUITEM_RECURSOS_REASIGNACION_BIEN_BUSCAR;
	  		$tabMenuItemRecursosGarantiaBienDisplay = TABMENUITEM_GARANTIA_BIEN_BUSCAR;
	  	}
	?>
	    <li style="z-index: 1278;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_BIENES][$tabMenuItemRecursosManejoBienesDisplay]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_MANEJO_BIENES?>','<?=$tabMenuItemRecursosManejoBienesDisplay ?>');">Administrar</a></li>
	    <li style="z-index: 1277;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_ACCIONES_BIEN][$tabMenuItemRecursosAccionesBienDisplay]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_ACCIONES_BIEN?>','<?=$tabMenuItemRecursosAccionesBienDisplay ?>');">Entradas/Salidas</a></li>
        <li style="z-index: 1277;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_REASIGNACION_BIEN][$tabMenuItemRecursosReaccionBienDisplay]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_REASIGNACION_BIEN?>','<?=$tabMenuItemRecursosReaccionBienDisplay ?>');">Re-asignar</a></li> 		 
	 	<?php if($_SESSION['user_perfil_id'] != PERFIL_ANALISTA_I_PASANTE_BIENES) {?>
	 	<li style="z-index: 1276;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CUSTODIA_BIEN][TABMENUITEM_CUSTODIA_BIEN]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_CUSTODIA_BIEN?>','<?=TABMENUITEM_CUSTODIA_BIEN ?>');">Custodia</a></li>   
	 	<?php }?>
	 	<li style="z-index: 1276;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_GARANTIA_BIEN][$tabMenuItemRecursosGarantiaBienDisplay]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_GARANTIA_BIEN?>','<?=$tabMenuItemRecursosGarantiaBienDisplay ?>');">Garant&iacute;a</a></li>
	 	<!-- Menú Desincorporación  quitar php para montar en produccion--> 
	 	<li style="z-index: 1276;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_DESINCORPORACION_BIEN][TABMENUITEM_RECURSOS_DESINCORPORACION_BIEN_BANDEJA]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_DESINCORPORACION_BIEN?>','<?=TABMENUITEM_RECURSOS_DESINCORPORACION_BIEN_BANDEJA ?>');">Desincorporaci&oacute;n</a></li>
	<?php } 
	  if (($_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO) || ($_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO)){?>
	<li style="z-index: 1278;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_BIENES][TABMENUITEM_MANEJO_BIEN_BUSCAR]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_MANEJO_BIENES?>','<?=TABMENUITEM_MANEJO_BIEN_BUSCAR?>');">Administrar</a></li>
	<?php } 
	
	/* if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES){?>
	    <li style="z-index: 1276;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_GARANTIA_BIEN][TABMENUITEM_BIENES_REPORTES_GARANTIA]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_GARANTIA_BIEN?>','<?=TABMENUITEM_BIENES_REPORTES_GARANTIA ?>');">Garant&iacute;a</a></li>
	  <?php }*/
	 if (($_SESSION['user_perfil_id'] != PERFIL_JEFE_PRESUPUESTO ) && ($_SESSION['user_perfil_id'] !=PERFIL_ANALISTA_PRESUPUESTO)){?>
	    <li style="z-index: 1273;"><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_BIENES_REPORTES][TABMENUITEM_BIENES_REPORTES_INVENTARIO_ACTIVOS]["URL"]?>','menuActivos','<?= TABMENU_RECURSOS_BIENES_REPORTES?>','<?= TABMENUITEM_BIENES_REPORTES_INVENTARIO_ACTIVOS?>');">Reportes</a></li> 
	<?php }
	?>
  </ul>
</li>

<?php if ($_SESSION['user_perfil_id'] !=PERFIL_ANALISTA_I_TECNOLOGIA && $_SESSION['user_perfil_id'] !=PERFIL_ANALISTA_II_TECNOLOGIA && $_SESSION['user_perfil_id'] !=PERFIL_JEFE_SEG_Y_CONTROL && $_SESSION['user_perfil_id'] !=PERFIL_GERENTE_TECNOLOGIA){?>
<li style="z-index: 1400;height: 28px;"><a id="menuMateriales" href="#" style="padding-right: 23px;" class="">
	Materiales<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/></a>
   <ul	class="ulMenu">
	<?php
		
		$tabMenuItemRecursosManejoArticulosDisplay = TABMENUITEM_MANEJO_ARTICULO_GESTIONAR;
		$tabMenuItemRecursosManejoInventarioDisplay = TABMENUITEM_MANEJO_INVENTARIO_CARGA;//para cuando se coloque bandeja de materiales colocar TABMENUITEM_MANEJO_INVENTARIO_CARGA_BANDEJA
		
		if($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES)
		{
			$tabMenuItemRecursosManejoArticulosDisplay = TABMENUITEM_MANEJO_ARTICULO_BUSCAR;
			$tabMenuItemRecursosManejoInventarioDisplay = TABMENUITEM_MANEJO_INVENTARIO_REPORTES_ACTAS;
		}
	
		if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id']==PERFIL_ALMACENISTA || $_SESSION['user_perfil_id']==PERFIL_ANALISTA_I_PASANTE_BIENES /*|| $_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO*/) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_ARTICULOS][$tabMenuItemRecursosManejoArticulosDisplay]["URL"]?>','menuMateriales','<?= TABMENU_RECURSOS_MANEJO_ARTICULOS?>','<?=$tabMenuItemRecursosManejoArticulosDisplay ?>');">Administrar</a></li>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_INVENTARIO][$tabMenuItemRecursosManejoInventarioDisplay]["URL"]?>','menuMateriales','<?= TABMENU_RECURSOS_MANEJO_INVENTARIO?>','<?=$tabMenuItemRecursosManejoInventarioDisplay ?>');">Inventario</a></li><?php }
			if ($_SESSION['user_perfil_id'] ==PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_PRESUPUESTO){?>
			  <li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_ARTICULOS][TABMENUITEM_MANEJO_ARTICULO_BUSCAR]["URL"]?>','menuMateriales','<?= TABMENU_RECURSOS_MANEJO_ARTICULOS?>','<?=TABMENUITEM_MANEJO_ARTICULO_BUSCAR ?>');">Administrar</a></li>
			<?}	
			if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_ANALISTA_I_PASANTE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_PRESIDENTE ||
			    $_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA  || $_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_EJECUTIVO) {?>
	    	  <li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MANEJO_INVENTARIO_REPORTES][TABMENUITEM_MANEJO_INVENTARIO_REPORTES_INVENTARIO_ACTUALIZADO]["URL"]?>','menuMateriales','<?= TABMENU_RECURSOS_MANEJO_INVENTARIO_REPORTES?>','<?=TABMENUITEM_MANEJO_INVENTARIO_REPORTES_INVENTARIO_ACTUALIZADO ?>');">Reportes</a></li>
			<?php } ?>
	</ul>
</li>	<?php }}
    
	if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] == PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES ||  $_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA || $_SESSION['user_perfil_id'] ==PERFIL_PRESIDENTE || $_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_EJECUTIVO ) {?>	
 	<li style="z-index: 1400;height: 28px;"><a id="menuResponsabilidad" href="#" style="padding-right: 23px;" class="">
	 Responsabilidad Social<img	class="downarrowclass imagenesMenu" src="js/menu/down.gif"/></a>
	 <ul	class="ulMenu">
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_PRESIDENTE || $_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_EJECUTIVO || $_SESSION['user_perfil_id'] ==PERFIL_ALMACENISTA) {
		       if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_ALMACENISTA ) {?>
				<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_RESP_SOCIAL_ADMINISTRAR][TABMENUITEM_RESP_SOCIAL_GESTIONAR]["URL"]?>','menuResponsabilidad','<?= TABMENU_RECURSOS_RESP_SOCIAL_ADMINISTRAR?>','<?=TABMENUITEM_RESP_SOCIAL_GESTIONAR ?>');">Administrar</a></li>
		 		<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_RESP_SOCIAL_INVENTARIO][TABMENUITEM_RESP_SOCIAL_BANDEJA]["URL"]?>','menuResponsabilidad','<?= TABMENU_RECURSOS_RESP_SOCIAL_INVENTARIO?>','<?=TABMENUITEM_RESP_SOCIAL_BANDEJA ?>');">Inventario</a></li>
		 		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA){?>
		 		<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_RESP_SOCIAL_CUSTODIA][TABMENUITEM_RESP_SOCIAL_CUSTODIA]["URL"]?>','menuResponsabilidad','<?= TABMENU_RECURSOS_RESP_SOCIAL_CUSTODIA?>','<?=TABMENUITEM_RESP_SOCIAL_CUSTODIA ?>');">Custodia</a></li>
				<?php }} if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_PRESIDENTE || $_SESSION['user_perfil_id'] ==PERFIL_DIRECTOR_EJECUTIVO || $_SESSION['user_perfil_id'] == PERFIL_ALMACENISTA) {?>
				<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_RESP_SOCIAL_REPORTES][TABMENUITEM_RESP_SOCIAL_REPORTES_INVENTARIO]["URL"]?>','menuResponsabilidad','<?= TABMENU_RECURSOS_RESP_SOCIAL_REPORTES?>','<?=TABMENUITEM_RESP_SOCIAL_REPORTES_INVENTARIO ?>');">Reportes</a></li> 
			<?php } }?>
	</ul>
	</li>
	<?php }?>
	
	<!-- <li style="z-index: 1350;height: 28px;"><a id="menuBusqueda" href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_BUSQUEDA][TABMENUITEM_BUSQUEDA_UBICAR_DOCUMENTO]["URL"]?>','menuBusqueda','<?= TABMENU_BUSQUEDA?>','<?= TABMENUITEM_BUSQUEDA_UBICAR_DOCUMENTO?>');">B&uacute;squeda</a></li> -->
	<li style="z-index: 1300; height: 28px;">
		<a id="menuRecursos" href="#" style="padding-right: 23px;" class="">
			Recursos<img class="downarrowclass imagenesMenu" src="js/menu/down.gif"/>
		</a>
		<ul class="ulMenu">
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ADMINISTRADOR || $_SESSION['user_perfil_id'] == PERFIL_ESPECIALISTA_PERSONAL || $_SESSION['user_perfil_id'] == PERFIL_JEFE_PERSONAL || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_TALENTO_HUMANO || $_SESSION['user_perfil_id'] ==PERFIL_ASISTENTE_ADMINISTRATIVO_EDUCACION || PERFIL_ASISTENTE_ADMINISTRATIVO_REDES) { 
		     if  ($_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_ADMINISTRATIVO_EDUCACION || $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_ADMINISTRATIVO_REDES){?>
				<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_EMPLEADO][TABMENUITEM_EMPLEADO_GESTIONAR_OTROS_TRABAJADORES]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_EMPLEADO?>','<?= TABMENUITEM_EMPLEADO_GESTIONAR_OTROS_TRABAJADORES?>');">Empleado</a></li>
		 <?php } else {?>
							<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_EMPLEADO][TABMENUITEM_EMPLEADO_GESTIONAR_EMPLEADO]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_EMPLEADO?>','<?= TABMENUITEM_EMPLEADO_GESTIONAR_EMPLEADO?>');">Empleado</a></li>	 
		<?php } }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ADMINISTRADOR) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_USUARIO][TABMENUITEM_USUARIO_GESTIONAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_USUARIO?>','<?= TABMENUITEM_USUARIO_GESTIONAR?>');">Usuario</a></li>
		<?php } else {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_USUARIO][TABMENUITEM_USUARIO_CONTRASENA]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_USUARIO?>','<?= TABMENUITEM_USUARIO_CONTRASENA?>');">Usuario</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_BANCOS][TABMENUITEM_BANCOS_GESTIONAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_BANCOS?>','<?= TABMENUITEM_BANCOS_GESTIONAR?>');">Bancos</a></li>
		<?php } elseif ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_BANCOS][TABMENUITEM_BANCOS_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_BANCOS?>','<?= TABMENUITEM_BANCOS_BUSCAR?>');">Bancos</a></li>
		<?php }?>

		<?php if ($_SESSION['user_perfil_id'] == PERFIL_TESORERO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_FINANZAS) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CUENTAS_BANCARIAS][TABMENUITEM_CUENTAS_BANCARIAS_GESTIONAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CUENTAS_BANCARIAS?>','<?= TABMENUITEM_CUENTAS_BANCARIAS_GESTIONAR?>');">Cuentas bancarias</a></li>
		<?php } elseif ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_TESORERIA) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CUENTAS_BANCARIAS][TABMENUITEM_CUENTAS_BANCARIAS_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CUENTAS_BANCARIAS?>','<?= TABMENUITEM_CUENTAS_BANCARIAS_BUSCAR?>');">Cuentas bancarias</a></li>
		<?php }?>			
					
					
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_COMPRAS || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_COMPRAS) {?>					
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_PROVEEDORES][TABMENUITEM_PROVEEDORES_GESTIONAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_PROVEEDORES?>','<?= TABMENUITEM_PROVEEDORES_GESTIONAR?>');">Proveedor</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] == PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES) {?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_SERVICIOS][TABMENUITEM_SERVICIOS_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_SERVICIOS?>','<?= TABMENUITEM_SERVICIOS_BUSCAR?>');">Servicio</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) { ?>			
			<li style="z-index: 1292;">
				<a href="#">Presupuesto</a>
				<ul	class="ulMenu">
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD) { ?>				
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_PARTIDAS_PRESUPUESTARIAS][TABMENUITEM_PARTIDAS_PRESUPUESTARIAS_INSERTAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_PARTIDAS_PRESUPUESTARIAS?>','<?= TABMENUITEM_PARTIDAS_PRESUPUESTARIAS_INSERTAR?>');">Partida presupuestaria</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESUPUESTO) { ?>
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_PARTIDAS_PRESUPUESTARIAS][TABMENUITEM_PARTIDAS_PRESUPUESTARIAS_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_PARTIDAS_PRESUPUESTARIAS?>','<?= TABMENUITEM_PARTIDAS_PRESUPUESTARIAS_BUSCAR?>');">Partida presupuestaria</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) { ?>
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CATEGORIA_PROGRAMATICA][TABMENUITEM_CATEGORIA_PROGRAMATICA_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CATEGORIA_PROGRAMATICA?>','<?= TABMENUITEM_CATEGORIA_PROGRAMATICA_BUSCAR?>');">Categor&iacute;a program&aacute;tica</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_ASISTENTE_PRESUPUESTO) { ?>
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CATEGORIA_PROGRAMATICA][TABMENUITEM_CATEGORIA_PROGRAMATICA_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CATEGORIA_PROGRAMATICA?>','<?= TABMENUITEM_CATEGORIA_PROGRAMATICA_BUSCAR?>');">Categor&iacute;a program&aacute;tica</a></li>
		<?php }?>
		<?php if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_JEFE_PRESUPUESTO || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_PRESUPUESTO) { ?>
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_ASIGNACION_PRESUPUESTO][TABMENUITEM_ASIGNACION_PRESUPUESTO_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_ASIGNACION_PRESUPUESTO?>','<?= TABMENUITEM_ASIGNACION_PRESUPUESTO_BUSCAR?>');">Asignaci&oacute;n presupuesto</a></li>
		<?php }?>
				</ul>
			</li>
			<?php }

			if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_CONTABLE || $_SESSION['user_perfil_id'] == PERFIL_COORDINADOR_CONTABILIDAD || $_SESSION['user_perfil_id'] == PERFIL_JEFE_ORDENACION_CONTABILIDAD || $_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS) { ?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CONTABILIDAD][TABMENUITEM_CONTABILIDAD_BUSCAR_CUENTAS_CONTABLES]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CONTABILIDAD?>','<?= TABMENUITEM_CONTABILIDAD_BUSCAR_CUENTAS_CONTABLES?>');">Contabilidad</a></li>
			<?php }?>
			<!--<li><a href="javascript: abrir('#','menuRecursos','<?= TABMENU_RECURSOS_MEMOS?>','');">Memos</a></li>-->
			<?php if ($_SESSION['user_perfil_id'] == PERFIL_ADMINISTRADOR) { ?>
			<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_CARGO][TABMENUITEM_CARGO_GESTIONAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_CARGO?>','<?= TABMENUITEM_CARGO_GESTIONAR?>');">Cargo</a></li>
			<?php }
			
			
			 if ($_SESSION['user_perfil_id'] == PERFIL_ANALISTA_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_JEFE_BIENES || $_SESSION['user_perfil_id'] ==PERFIL_COORDINADOR_BIENES || $_SESSION['user_perfil_id'] == PERFIL_ANALISTA_I_PASANTE_BIENES) {
			?>
			<li style="z-index: 1270;">
				<a href="#">Activos</a>
				<ul	class="ulMenu">
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_ACTIVOS_CATEGORIA][TABMENUITEM_MANEJO_CATEGORIA_ACTIVOS_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_ACTIVOS_CATEGORIA?>','<?= TABMENUITEM_MANEJO_CATEGORIA_ACTIVOS_BUSCAR?>');">Categor&iacute;as</a></li>
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_ACTIVOS_MARCA][TABMENUITEM_MANEJO_MARCA_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_ACTIVOS_MARCA?>','<?= TABMENUITEM_MANEJO_MARCA_BUSCAR?>');">Marcas</a></li>
				</ul>
			</li>
			<li style="z-index: 1270;">
				<a href="#">Materiales</a>
				<ul	class="ulMenu">
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_MATERIALES_CATEGORIA][TABMENUITEM_MANEJO_CATEGORIA_MATERIALES_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_MATERIALES_CATEGORIA?>','<?= TABMENUITEM_MANEJO_CATEGORIA_MATERIALES_BUSCAR?>');">Categor&iacute;as</a></li>
				</ul>
			</li>
			
			 <li style="z-index: 1270;">
				<a href="#">Responsabilidad Social</a>
				<ul	class="ulMenu">
					<li><a href="javascript: abrir('<?= $tabmenuItemsArray[TABMENU_RECURSOS_RESP_SOCIAL_CATEGORIA][TABMENUITEM_RESP_SOCIAL_CATEGORIA_BUSCAR]["URL"]?>','menuRecursos','<?= TABMENU_RECURSOS_RESP_SOCIAL_CATEGORIA?>','<?= TABMENUITEM_RESP_SOCIAL_CATEGORIA_BUSCAR?>');">Categor&iacute;as</a></li>
				</ul>
			</li>
			<?php }?>
		</ul>
	</li>
</ul>

<div class="opciones"  style="margin-top: 20px;">

<div class="migajas" id="migajas">
	<?php  require("includes/migajas/migajas.php");?>
</div>
	<?php  require("includes/perfiles/perfiles.php");?>
</div>




<!-- ruta en donde esta -->


<div class="submenu" id="submenu">
	
</div>


</div></br>

		<div class="tabmenu" id="tabmenu">
			<?php require("includes/tabmenu/tabmenu.php");?>
		</div>
		
		
<div class="franja"></div>

<iframe name="contenido" style=background-image:url('imagenes/transparente.png') repeat;" src="bienvenida.php" width="100%" height="608px" scrolling="auto" frameborder="0"></iframe>
<!-- PIE DE PAGINA -->
<div style="margin-top: 0.3%;" align="center"></div>
<img src="imagenes/linea-safi0.2.jpg" width="100%" height="3px;"/>
<div style="background-color: #EBF5EC;color: #63666B;font-weight: bold;font-size: 9px;padding-top: 2px;padding-bottom: 2px;" align="center">
Sistema Administrativo de la Fundaci&oacute;n Infocentro - SAFI v.1.0 2013.
Basado en el Sistema Administrativo Integrado - SAI 2006 - de la Fundaci&oacute;n Instituto de Ingenier&iacute;a
</div>
<img src="imagenes/linea-safi0.2.jpg" width="100%" height="3px;"/>
<!-- FIN PIE DE PAGINA -->
</body>
</html>
<?php pg_close($conexion); //session_destroy(); ?>
	
