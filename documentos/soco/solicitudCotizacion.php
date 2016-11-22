<?php
/*ob_start();
session_start();*/

include(dirname(__FILE__) . '/../../init.php');
include_once(SAFI_INCLUDE_PATH.'/validarSesion.php');

require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush(); 
	exit;
}
require("../../includes/constantes.php");
$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$codigoCR = "";
if (isset($_REQUEST['codigoCR']) && $_REQUEST['codigoCR'] != "") {
	$codigoCR = $_REQUEST['codigoCR'];
}
$idRequ = "";
if (isset($_REQUEST['idRequ']) && $_REQUEST['idRequ'] != "") {
	$idRequ = $_REQUEST['idRequ'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_REQUEST['tipoRequ']) && $_REQUEST['tipoRequ'] != "") {
	$tipoRequ = $_REQUEST['tipoRequ'];
}
$tipoBusq = TIPO_BUSQUEDA_REQUISICIONES;
if (isset($_REQUEST['tipoBusq']) && $_REQUEST['tipoBusq'] != "") {
	$tipoBusq = $_REQUEST['tipoBusq'];
}
$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
$proyAcc = "";
if (isset($_REQUEST['proyAcc']) && $_REQUEST['proyAcc'] != "") {
	$proyAcc = $_REQUEST['proyAcc'];
}
$radioProyAcc = "";
if (isset($_REQUEST['radioProyAcc']) && $_REQUEST['radioProyAcc'] != "") {
	$radioProyAcc = $_REQUEST['radioProyAcc'];
}
$proyecto = "";
$accionCentralizada = "";
if($radioProyAcc=="proyecto"){
	if (isset($_REQUEST['proyecto']) && $_REQUEST['proyecto'] != "") {
		$proyecto = $_REQUEST['proyecto'];
	}		
}else if($radioProyAcc=="accionCentralizada"){
	if (isset($_REQUEST['accionCentralizada']) && $_REQUEST['accionCentralizada'] != "") {
		$accionCentralizada = $_REQUEST['accionCentralizada'];
	}
}else{
	$proyAcc = "";
}
$dependencia = "";
if (isset($_REQUEST['dependencia']) && $_REQUEST['dependencia'] != "") {
	$dependencia = $_REQUEST['dependencia'];
}
$estado = ESTADO_REQUISICION_PENDIENTES;
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
$controlFechas = "";
if (isset($_REQUEST['controlFechas']) && $_REQUEST['controlFechas'] != "") {
	$controlFechas = $_REQUEST['controlFechas'];
}
$fechaInicio = "";
if (isset($_REQUEST['fechaInicio']) && $_REQUEST['fechaInicio'] != "") {
	$fechaInicio = $_REQUEST['fechaInicio'];
}
$fechaFin = "";
if (isset($_REQUEST['fechaFin']) && $_REQUEST['fechaFin'] != "") {
	$fechaFin = $_REQUEST['fechaFin'];
}
$bandeja = "";
if (isset($_REQUEST['bandeja']) && $_REQUEST['bandeja'] != "") {
	$bandeja = $_REQUEST['bandeja'];
}
$proveedores = "";
if (isset($_REQUEST['proveedores']) && $_REQUEST['proveedores'] != "") {
	$proveedores = $_REQUEST['proveedores'];	
}
if($proveedores!=""){
	$tok = strtok($proveedores, ",");
	$proveedores = "(";	
	while ($tok !== false) {
	    $proveedores = $proveedores."'".$tok."',";
	    $tok = strtok(",");
	}
	$proveedores = substr($proveedores, 0, -1).")";
}
$accion = "";
if (isset($_REQUEST['accion']) && $_REQUEST['accion'] != "") {
	$accion = $_REQUEST['accion'];
}
$memo = "";
if (isset($_REQUEST['memo']) && $_REQUEST['memo'] != "") {
	$memo = $_REQUEST['memo'];
}
if($accion==ACCION_DEVOLVER_REQUISICION){
	$error="";
	if($memo==""){
		$error = "0";//Debe indicar el memo de la devolucion
	}
	if($error==""){
		$user_perfil_id = $_SESSION['user_perfil_id'];
		$user_login = $_SESSION['login'];
		$devolver = 5;
		$estadoDevuelto = 7;
		$documentoRequisicion = "rqui";
		
		$accionDetalles = "devolver";
		//$queryCadena = "swfg.wfgr_perf = '".$user_perfil_id."' ";
		$queryCadena =	"(swfg.wfgr_perf = '".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '".$user_perfil_id."%' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."' ".
						"OR swfg.wfgr_perf like '%/".$user_perfil_id."%') ";
		
		$sql="SELECT rebms_tipo, depe_id FROM sai_req_bi_ma_ser WHERE rebms_id = '".$idRequ."'";
		$resultado = pg_exec($conexion ,$sql);
		$row = pg_fetch_array($resultado,0);
		$rebms_tipo = $row["rebms_tipo"];
		$depe_id = $row["depe_id"];
		
		$objetoSiguiente = 2;//Modificar
		
		$sql=	"SELECT ".
					"swc.wfca_id, ".
					"swc.wfob_id_ini, ".
					"swfgh.wfgr_perf ".
				"FROM sai_wfcadena swc, sai_wfgrupo swfg, sai_wfcadena swch, sai_wfgrupo swfgh ".
				"WHERE ".
					$queryCadena." AND ".
					"swfg.wfgr_id = swc.wfgr_id AND ".
					"swc.docu_id = '".$documentoRequisicion."' AND ".
					"swc.wfop_id = ".$devolver." AND ".
					(($depe_id=="350" || $depe_id=="150")?"swc.depe_id = '".$depe_id."' AND ":" (swc.depe_id IS NULL OR swc.depe_id = '') AND ").
					"swc.wfob_id_sig = ".$objetoSiguiente." AND ".
					"swc.wfca_id_hijo = swch.wfca_id AND ".
					"swch.wfgr_id = swfgh.wfgr_id";
		$resultado = pg_exec($conexion ,$sql);
		if($resultado){
			$row = pg_fetch_array($resultado,0);
			$wfca_id=trim($row["wfca_id"]);
			$wfob_id_ini=trim($row["wfob_id_ini"]);
			
			$wfgr_perf=trim($row["wfgr_perf"]);
			
			if($depe_id!="350" && $depe_id!="150"){
				$queryCargo = "(";
				$token = strtok($wfgr_perf, "/");
				while ($token !== false) {
				    $queryCargo.=$token.",";
				    $token = strtok("/");
				}
				$queryCargo = substr($queryCargo,0,-1).")";
			}else{
				$queryCargo = "(".substr($wfgr_perf, 0, 2)."000)";
			}
		
			$sql=	"SELECT substring(carg_id from 1 for 2)||depe_id as perfil ".
					"FROM sai_depen_cargo ".
					"WHERE ".
						"depe_id = '".$depe_id."' AND ".
						"carg_id IN ".$queryCargo;
			$resultado = pg_exec($conexion ,$sql);
			$row = pg_fetch_array($resultado,0);
			$perfil = $row["perfil"];
			
			//Se actualiza el documento generado con el nivel correspondiente en la cadena
			$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$perfil."', esta_id = ".$estadoDevuelto." WHERE docg_id = '".$idRequ."'";
			$resultado = pg_exec($conexion ,$sql);
			//Se actualiza el estado en la requisicion y se quita la salida de compra en caso de ser servicio
			
			$sql="UPDATE sai_req_bi_ma_ser SET esta_id = ".$estadoDevuelto." WHERE rebms_id = '".$idRequ."'";
			
			$resultado = pg_exec($conexion ,$sql);
			//Se inserta un memo por la devolucion de la requisicion
			$sql="select * from sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."','".$memo."','".utf8_decode("Devolucion de Requisición")."','0','0','0','',0, 0, '0','','".$idRequ."') as resultado_set(text)";
			$resultado = pg_exec($conexion ,$sql);
			$row = pg_fetch_array($resultado, 0);
			
			//Insertar la revision
			//$sql = " SELECT * FROM sai_insert_revision_doc('$idRequ', '$user_login', '$user_perfil_id', '$devolver', '') as resultado ";
			$sql = " SELECT * FROM sai_insert_revision_doc('".$idRequ."', '".$user_login."', '15456', '".$devolver."', '') as resultado ";
			$resultado = pg_query($conexion,$sql);
			
			/*
			//EMAIL
			$emailCargo = substr($perfil,0,2);
			$emailDependencia = substr($perfil,2);
			$sql="SELECT sem.empl_email, sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante FROM sai_empleado sem WHERE sem.carg_fundacion = '".$emailCargo."' AND sem.depe_cosige = '".$emailDependencia."'";
			$resultado = pg_exec($conexion ,$sql);
			$numeroFilas = pg_numrows($resultado);
			if($numeroFilas>0){
				require("../../includes/funciones.php");
				$de = "info@infocentro.gob.ve";
				$nombreDe = utf8_decode("SISTEMA ADMINISTRATIVO DE LA FUNDACION INFOCENTRO");
				$copiaOculta = "";
				$nombreCopiaOculta = "";
				$asunto = utf8_decode("Devolución de solicitud de requisición");
				$message = wordwrap(utf8_decode("Se ha devuelto una solicitud de requisición con código ".$idRequ." procedente de ").$_SESSION['user_depe'], 70);
				while($row = pg_fetch_array($resultado)){
					$para = $row['empl_email'];
					$nombrePara = utf8_decode($row['solicitante']);
					enviarEmail($de, $nombreDe, $para, $nombrePara, $copiaOculta, $nombreCopiaOculta, $asunto, $message, null);
				}
			}*/
		}
		header("Location:../rqui/detalleRequisicion.php?codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&codigoCR=".$codigoCR,false);
	}else{
		header("Location:../rqui/requisicionAnalistaCompras.php?msg=".$error."&codigo=".$codigo."&idRequ=".$idRequ."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&accion=".$accionDetalles."&proyAcc=".$proyAcc."&radioProyAcc=".$radioProyAcc."&proyecto=".$proyecto."&accionCentralizada=".$accionCentralizada."&dependencia=".$dependencia."&codigoCR=".$codigoCR,false);
	}
}
ob_end_flush();
/*$obj =  $GLOBALS['SafiRequestVars']['soco'];

if($obj){
	$obj->UTF8Encode();
	$objArray = $obj->ToArray();
}

$objArray = $obj->ToArray();*/
?>
<!-- <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>.:SAI:Solicitud de Cotizaci&oacute;n</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
	
<html>
<head>
<title>.:SAFI:. Solicitud de cotizaci&oacute;n</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="<?=GetConfig("siteURL").'/css/estilos.css';?>" 
 rel="stylesheet" type="text/css" charset="utf-8" />
<?php require(SAFI_JAVASCRIPT_PATH.'/init.php'); ?>
<link href="<?=GetConfig("siteURL").'/css/safi0.2.css';?>"
	rel="stylesheet" type="text/css" charset="utf-8" />

<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
	<script language="JavaScript" src="../../js/funciones.js"> </script>
<script type="text/javascript"
	src="../../js/lib/jquery/plugins/jquery.min.js"
	charset="utf-8"></script>
<script type="text/javascript"
	src="../../js/funciones.js"
	charset="utf-8"></script>		
<script type="text/javascript"
	src="../../js/lib/jquery/plugins/ui.min.js"
	charset="utf-8"></script>
	<script type="text/javascript"
	src="../../js/lib/uploadify/uploadify/jquery.uploadify.min.js"
	charset="utf-8"></script>

	<link type="text/css" href="../../js/lib/uploadify/uploadify/uploadify.css"
	media="screen" rel="stylesheet" />

<!-- jQuery and jQuery UI -->

<script type="text/javascript"
	src="../../js/editorlr/js/jquery-ui-1.8.13.custom.min.js"
	charset="utf-8"></script>
<link
	href="../../js/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css"
	rel="stylesheet" type="text/css" charset="utf-8" />	
		
<style>


    .uploadify-button {
     background: transparent;
        border: none;
        padding-left: 0;
        background-image:url('../../js/lib/uploadify/examinar.png');
        border:0;
    }
    .uploadify:hover .uploadify-button {
      background: transparent;
        border: none;
        background-image:url('../../js/lib/uploadify/examinar2.png');
        border:0;
    }
      
        
</style>	
	
	<script language="Javascript">
		var items=new Array();
		var itemsIndice = -1;
		var proveedores=new Array();
		var proveedoresIndice = -1;
		//$respaldo = $objArray != null ? $objArray['respaldo']  : null;

		 /**Respaldos digitales */
		var

		respaldosDigitales = 0,
		respaldosFisicos = 0,
		respaldo = null,
	    PHPSESSID = '';
	    regisFisDigiEli = new Array();
	    regisNombreDigital = new Array();
	    tamanoFile = 0;
			 
		$().ready(function() {	
			//if(respaldo){  
	
				$(function() {
					 
					$('#file_upload').uploadify({
						'formData'     : {
							'accion' : 'GuardarImg',
							'PHPSESSID' : PHPSESSID
						},
					    'swf'      	: '../../js/lib/uploadify/uploadify/uploadify.swf',
						'uploader' 	: '../../acciones/soco/soco.php',
						'buttonText' : '',
						   'fileTypeExts' : '*.gif; *.jpg; *.png; *.odt; *.pdf; *.jpeg; *.txt; *.ods; *.xls; *.bmp; *.pdf; *.pdt; *.odp',
						   'uploadLimit' : 5,
						   'queueSizeLimit' : 5,
						   'UploadLimit'  :  5, 
						   'progressData' : 'speed',
						   'width'    : 104,	
						   'height'   : 32,			   
						'auto'		: false,
						'onSelect' : function(file) {
							tamanoFile++ ;	
						//	alert(tamanoFile);
				        },
				       'onCancel' : function(file) {
				        	tamanoFile-- ;
				        },
						'onQueueComplete' : function(queueData) {
							$('#formSolicitudCotizacion')[0].submit();
							
				        }
						

					});
				});
				
			
				/*$.each(respaldo,function(id,val){
			       
					
					
				if(val.respTipo == 'Digital'){
					
					$('#trActualesDigital').show();
					
					
					
					var tbody = $('#tbodyRespDigital')[0];	
					var fila = document.createElement("tr");
				    fila.setAttribute("class","trDigitalEliminar");
					var columna = document.createElement("td");
					    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
					    columna.setAttribute("registrosEliminar","pcta[codProyAccEsp][]");
					    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
					   
			     var alink = document.createElement("a");
			         alink.setAttribute("href","descargarImagen.php?file="+val.respNombre+""); 
			         alink.appendChild(document.createTextNode(val.respNombre));
			         columna.appendChild(alink);	
			         
			       // OPCION DE ELIMINAR
				 		var columna8 = document.createElement("td");
				 		columna8.setAttribute("valign","top");
				 		columna8.setAttribute("align","left");
				 		columna8.className = 'link';
				 		deleteLink = document.createElement("a");
				 		deleteLink.setAttribute("href","javascript:void(0);");
				 		linkText = document.createTextNode("Eliminar");
				 		deleteLink.appendChild(linkText);
				 		columna8.appendChild(deleteLink);
			
			
				 		$(deleteLink).bind('click', function(){
				 			
				 			EliminarRegisDigitFisicActual(this,0,val.id,val.respNombre);
				 		
				 			
				 			
				 		});
				 		
				 		
			          
					 fila.appendChild(columna);	
					 fila.appendChild(columna8); 
				     tbody.appendChild(fila);
			
			
				}else{
					
					   $('#trActualesFis').show();
						
					 
						var tbody = $('#tbodyRespFisico')[0];	
						var fila = document.createElement("tr");
					    fila.setAttribute("class","trFisicoEliminar");
						var columna = document.createElement("td");
						    columna.setAttribute("style","border-bottom: 1px solid #D8D8D8");
			            var alink = document.createElement("a");
			             alink.setAttribute("href","javascript:void(0);"); 
			             alink.appendChild(document.createTextNode(val.respNombre));
			             columna.appendChild(alink);	
			             
			           // OPCION DE ELIMINAR
					 		var columna8 = document.createElement("td");
					 		columna8.setAttribute("valign","top");
					 		columna8.setAttribute("align","left");
					 		columna8.className = 'link';
					 		deleteLink = document.createElement("a");
					 		deleteLink.setAttribute("href","javascript:void(0);");
					 		linkText = document.createTextNode("Eliminar");
					 		deleteLink.appendChild(linkText);
					 		columna8.appendChild(deleteLink);
			
			
					 		$(deleteLink).bind('click', function(){
					 		
					 			EliminarRegisDigitFisicActual(this,1,val.id,val.respNombre);
			
					 			
					 		});
					 		
					 		
			              
						 fila.appendChild(columna);	
						 fila.appendChild(columna8); 
					     tbody.appendChild(fila);
			
			
			
					}
			
				function EliminarRegisDigitFisicActual(obj,tipo,id,nombre) {
					
					if(tipo == 0 ){
						
						  
						regisFisDigiEli[regisFisDigiEli.length] = id; 
						regisNombreDigital[regisNombreDigital.length] = nombre;
							
							
				
					objTrs = $(obj).parents("tr.trDigitalEliminar");
						objTrs.hide(100).remove();
						
						
						
					if($("#tbodyRespDigitales > tr").length < 1){
						
						$('#trActualesDigital').hide(300);
						
					}
						
					}else{
					
						regisFisDigiEli[regisFisDigiEli.length] = id; 
					
						
						objTrs = $(obj).parents("tr.trFisicoEliminar");
			 			objTrs.hide(100).remove();
			 			
			 			
			 			
						if($("#tbodyRespFisico > tr").length < 1){
			
							$('#trActualesFis').hide(300);
					}
					
					}
				}
				
				 });*/
				
			
			// }
			 
			});			 
			 
			 /*Fin de respaldos digitales*/
		

		function bandeja(){
			location.href = "../rqui/bandeja.php";
		}

		function revisar(){
			idRequ = '<?= $idRequ?>';
			location.href = "../rqui/requisicionAnalistaCompras.php?bandeja=true&idRequ="+idRequ;
		}
		
		function irARequisiciones(){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			tipoRequ = '<?= $tipoRequ?>';
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
			location.href = "../rqui/busquedas.php?codigo="+codigo+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia+"&codigoCR="+codigoCR;
		}
		
		function irAArticulos(){
			codigo = '<?= $codigo?>';
			codigoCR = '<?= $codigoCR?>';
			idRequ = '<?= $idRequ?>';
			tipoRequ = '<?= $tipoRequ?>';
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
			location.href = "../rqui/requisicionAnalistaCompras.php?codigo="+codigo+"&idRequ="+idRequ+"&tipoRequ="+tipoRequ+"&pagina="+pagina+"&estado="+estado+"&controlFechas="+controlFechas+"&fechaInicio="+fechaInicio+"&fechaFin="+fechaFin+"&tipoBusq="+tipoBusq+"&proyAcc="+proyAcc+"&radioProyAcc="+radioProyAcc+"&proyecto="+proyecto+"&accionCentralizada="+accionCentralizada+"&dependencia="+dependencia+"&codigoCR="+codigoCR;
		}
	
		function enviar() {
			cadenaItems = "";
			for(i=0; i<items.length; i++){
				if(items[i]!=null){
					cadenaItems += items[i]+",";
				}
			}
			if(cadenaItems!=""){
				cadenaItems = cadenaItems.substring(0,cadenaItems.length - 1);
				document.getElementById("items").value = cadenaItems;
			}else{
				alert("La cotizaci"+oACUTE+"n debe incluir al menos un (1) rubro");
				return;
			}
			cadenaProveedores = "";
			for(i=0; i<proveedores.length; i++){
				if(proveedores[i]!=null){
					cadenaProveedores += proveedores[i]+",";
				}
			}
			if(cadenaProveedores!=""){
				cadenaProveedores = cadenaProveedores.substring(0,cadenaProveedores.length - 1);
				document.getElementById("proveedores").value = cadenaProveedores;
			}else{
				alert("Debe indicar algun proveedor al cual se le enviar"+aACUTE+" la cotizaci"+oACUTE+"n");
				return;
			}
			if(trim(document.getElementById("tiempoEntrega").value)=="") {
				alert("Debe especificar el tiempo de entrega de la mercanc"+iACUTE+"a");
				document.getElementById("tiempoEntrega").focus();
				return;
			}
			if(trim(document.getElementById("sitioEntrega").value)=="") {
				alert("Debe especificar el sitio de entrega de la mercanc"+iACUTE+"a");
				document.getElementById("sitioEntrega").focus();
				return;
			}
			if(document.getElementById("beneficiario").value=="") {
				alert("Debe seleccionar un beneficiario");
				document.getElementById("beneficiario").focus();
				return;
			}
		 	if(trim(document.getElementById("anexos").value)=="") {
				alert("Debe especificar que recaudos deben acompa"+nTILDE+"ar a la cotizaci"+oACUTE+"n");
				document.getElementById("anexos").focus();
				return;
			}
			if(document.getElementById("observaciones").length>220) {
				alert("Las observaciones no deben exceder de 220 caracteres");
				document.getElementById("observaciones").focus();
				return;
			}
			if(confirm("Los datos han sido introducidos de manera correcta. "+pACUTE+"Desea continuar?.")) {
				if(tamanoFile > 0 ){
					
					$('#file_upload').uploadify('upload','*');

					}else{
						//alert("nooo");
						document.formSolicitudCotizacion.submit();
						
						}
				//document.formSolicitudCotizacion.submit();
				alert("Su solicitud de cotizaci"+oACUTE+"n ha sido enviada exitosamente a los proveedores seleccionados.");
			}			
		}

		function verificarCheckboxControlItems(){
			inputs = document.getElementsByTagName("input");
			todosMarcados = true;
			for(iVerificar = 0; iVerificar < inputs.length; iVerificar++) {
				if(inputs[iVerificar].getAttribute("type")=="checkbox"
					&& strStartsWith(inputs[iVerificar].getAttribute("name"),"items")==true
					&& inputs[iVerificar].checked == false){
					todosMarcados = false;
				}
			}
			checkboxControl = document.getElementById("controlItems");
			checkboxControl.checked = todosMarcados;
		}
		
		function verificarCheckboxControl(){
			inputs = document.getElementsByTagName("input");
			todosMarcados = true;
			for(iVerificar = 0; iVerificar < inputs.length; iVerificar++) {
				if(inputs[iVerificar].getAttribute("type")=="checkbox"
					&& strStartsWith(inputs[iVerificar].getAttribute("name"),"proveedores")==true
					&& inputs[iVerificar].checked == false){
					todosMarcados = false;
				}
			}
			checkboxControl = document.getElementById("controlProveedores");
			checkboxControl.checked = todosMarcados;
		}

		function marcarTodosNingunoItems(){
			checkbox = document.getElementById("controlItems");
			inputs = document.getElementsByTagName("input");
			for(iMarcar = 0; iMarcar < inputs.length; iMarcar++) {
				if(inputs[iMarcar].getAttribute("type")=="checkbox"
					&& strStartsWith(inputs[iMarcar].getAttribute("name"),"items")==true){
					inputs[iMarcar].checked = checkbox.checked;
					agregarQuitarItem(inputs[iMarcar]);
				}
			}
			verificarCheckboxControlItems();
		}
		
		function marcarTodosNinguno(){
			checkbox = document.getElementById("controlProveedores");
			inputs = document.getElementsByTagName("input");
			for(iMarcar = 0; iMarcar < inputs.length; iMarcar++) {
				if(inputs[iMarcar].getAttribute("type")=="checkbox"
					&& strStartsWith(inputs[iMarcar].getAttribute("name"),"proveedores")==true){
					inputs[iMarcar].checked = checkbox.checked;
					agregarQuitarProveedor(inputs[iMarcar]);
				}
			}
			verificarCheckboxControl();
		}

		function agregarQuitarItem(elemento){
			if(elemento.checked==true){
				if(existeItem(elemento.value+"")==-1){
					itemsIndice++;
					items[itemsIndice] = new String(elemento.value+'');
				}
			}else{
				items.splice(existeItem(elemento.value+""),1);
				//items[existeItem(elemento.value+"")] = null;
				itemsIndice--;
			}
		}
		
		function agregarQuitarProveedor(elemento){
			if(elemento.checked==true){
				if(existeProveedor(elemento.value+"")==-1){
					proveedoresIndice++;
					proveedores[proveedoresIndice] = new String(elemento.value+'');
				}
			}else{
				proveedores.splice(existeProveedor(elemento.value+""),1);
				//proveedores[existeProveedor(elemento.value+"")] = null;
				proveedoresIndice--;
			}
		}
		
		function existeItem(id){
			iExisteItem = 0;
			while(iExisteItem<items.length){
				if(items[iExisteItem]==id){
					return iExisteItem;	
				}
				iExisteItem++;
			}
			return -1;
		}

		function existeProveedor(rif){
			iExisteProveedor = 0;
			while(iExisteProveedor<proveedores.length){
				if(proveedores[iExisteProveedor]==rif){
					return iExisteProveedor;	
				}
				iExisteProveedor++;
			}
			return -1;
		}

		 


				 		
	</script>
</head>
<body class="normal">
<script type="text/javascript" charset="utf-8">
     PHPSESSID = '<?php echo $_COOKIE['PHPSESSID'];?>';
 
 </script>
<script type="text/javascript" charset="utf-8">
	  respaldo = <?php echo json_encode($respaldo,JSON_FORCE_OBJECT); ?>;
     </script>		
<p align="center">
<?php 
if ( $bandeja!="true" ) {
?>
	<a href='javascript: irAArticulos();'>Volver a requisici&oacute;n</a> <span class="normalNegro" style="margin-left: 10px;margin-right: 10px;">|</span>
	<a href='javascript: irARequisiciones();'>Volver a los resultados de la b&uacute;squeda</a>
<?php
} else {
?>
	<a href='javascript: revisar();'>Volver a requisici&oacute;n</a> <span class="normalNegro" style="margin-left: 10px;margin-right: 10px;">|</span>
	<a href='javascript: bandeja();'>Volver a la bandeja</a>
<?php	
}
?>
</p>
<table align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td colspan="2" class="normalNegroNegrita">
			Solicitud de cotizaci&oacute;n para requisici&oacute;n c&oacute;digo: <?=$idRequ?>
		</td>
	</tr>
	<tr>
		<td align="center">
			<form id="formSolicitudCotizacion" name="formSolicitudCotizacion" action="../../acciones/soco/soco.php?accion=Registrar" method="post">
				<input type="hidden" id="codigo" name="codigo" value="<?= $codigo?>"/>
				<input type="hidden" id="codigoCR" name="codigoCR" value="<?= $codigoCR?>"/>
				<input type="hidden" id="idRequ" name="idRequ" value="<?= $idRequ?>"/>
				<input type="hidden" id="tipoRequ" name="tipoRequ" value="<?= $tipoRequ?>"/>
				<input type="hidden" id="estado" name="estado" value="<?= $estado?>"/>
				<input type="hidden" id="controlFechas" name="controlFechas" value="<?= $controlFechas?>"/>
				<input type="hidden" id="fechaInicio" name="fechaInicio" value="<?= $fechaInicio?>"/>
				<input type="hidden" id="fechaFin" name="fechaFin" value="<?= $fechaFin?>"/>
				<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
				<input type="hidden" id="tipoBusq" name="tipoBusq" value="<?= $tipoBusq?>"/>
				<input type="hidden" id="proyAcc" name="proyAcc" value="<?= $proyAcc?>"/>
				<input type="hidden" id="radioProyAcc" name="radioProyAcc" value="<?= $radioProyAcc?>"/>
				<input type="hidden" id="proyecto" name="proyecto" value="<?= $proyecto?>"/>
				<input type="hidden" id="accionCentralizada" name="accionCentralizada" value="<?= $accionCentralizada?>"/>
				<input type="hidden" id="dependencia" name="dependencia" value="<?= $dependencia?>"/>
				<input type="hidden" id="bandeja" name="bandeja" value="<?= $bandeja?>"/>
				<input type="hidden" id="proveedores" name="proveedores" value=""/>
				<input type="hidden" id="items" name="items" value=""/>
<?php



if($proveedores!=""){
	$resultado = pg_exec($conexion,	"SELECT sscp.beneficiario_rif ".
									"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp ".
									"WHERE ".
										"ssc.rebms_id = '".$idRequ."' ".
										"AND ssc.soco_id = sscp.soco_id");
	$numeroFilas = pg_numrows($resultado);
	if($numeroFilas>0){
		$queryProveedoresGuardados = "AND sp.prov_id_rif NOT IN (";
		for($i = 0; $i<$numeroFilas; $i++){
			$row = pg_fetch_array($resultado, $i);
			$queryProveedoresGuardados .= "'".$row[0]."',";
		}
		$queryProveedoresGuardados = substr($queryProveedoresGuardados, 0, -1).")";
	}else{
		$queryProveedoresGuardados = "";
	}
	
	$resultado = pg_exec($conexion,	"SELECT prov_id_rif, prov_nombre, fecha, usua_login, solicitante ".
									"FROM ".
										"(".
										"SELECT sp.prov_id_rif, sp.prov_nombre, '' as fecha, '' as usua_login, '' as solicitante ".
										"FROM sai_proveedor_nuevo sp ".
										"WHERE ".
											"sp.prov_id_rif IN ".$proveedores." ".
											$queryProveedoresGuardados." ".
										"UNION ".
										"SELECT sp.prov_id_rif, sp.prov_nombre, to_char(sscp.fecha,'DD/MM/YYYY HH:MI am') as fecha, ssc.usua_login, sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ".
										"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_proveedor_nuevo sp, sai_usuario su, sai_empleado sem, ".
											"(".										
											"SELECT sscp.beneficiario_rif, MAX(sscp.fecha) as fecha ".
											"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp ".
											"WHERE ".
												"ssc.rebms_id = '".$idRequ."' ".
												"AND ssc.soco_id = sscp.soco_id ".
											"GROUP BY sscp.beneficiario_rif ".
											") as s ".
										"WHERE ".
											"ssc.rebms_id = '".$idRequ."' ".
											"AND ssc.soco_id = sscp.soco_id ".
											"AND sscp.beneficiario_rif = s.beneficiario_rif ".
											"AND sscp.fecha = s.fecha ".
											"AND sscp.beneficiario_rif = sp.prov_id_rif ".
											"AND ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
										") as s ".
									"ORDER BY prov_nombre ASC");
}else{
	$resultado = pg_exec($conexion,	"SELECT sp.prov_id_rif, sp.prov_nombre, to_char(sscp.fecha,'DD/MM/YYYY HH:MI am') as fecha, ssc.usua_login, sem.empl_nombres || ' ' || sem.empl_apellidos as solicitante ".
									"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp, sai_proveedor_nuevo sp, sai_usuario su, sai_empleado sem, ".
										"(".										
										"SELECT sscp.beneficiario_rif, MAX(sscp.fecha) as fecha ".
										"FROM sai_sol_coti ssc, sai_sol_coti_prov sscp ".
										"WHERE ".
											"ssc.rebms_id = '".$idRequ."' ".
											"AND ssc.soco_id = sscp.soco_id ".
										"GROUP BY sscp.beneficiario_rif ".
										") as s ".
									"WHERE ".
										"ssc.rebms_id = '".$idRequ."' ".
										"AND ssc.soco_id = sscp.soco_id ".
										"AND sscp.beneficiario_rif = s.beneficiario_rif ".
										"AND sscp.fecha = s.fecha ".
										"AND sscp.beneficiario_rif = sp.prov_id_rif ".
										"AND ssc.usua_login = su.usua_login AND su.empl_cedula = sem.empl_cedula ".
									"ORDER BY sp.prov_nombre ASC");
}
$numeroFilas = pg_numrows($resultado);
if($numeroFilas>0){
?>
		<table align="center" width= "900px">
			<tr>
				<td>
					<p class="normal peq_naranja">Se generar&aacute; un correo electr&oacute;nico con la siguiente informaci&oacute;n:</p>
				</td>
			</tr>
			<tr>
				<td>
					<table id="tbl_part6">
						<tr>
							<td> Asunto: </td>
							<td>
								<input type="text" id="asunto" name="asunto" value="Solicitud de cotizaci&oacute;n" size="95" maxlength="60" class="normalNegro" onkeyup="validarTexto(this);"/>
							</td>
						</tr>
						<tr>
							<td> Cuerpo: </td>
							<td>
								<textarea class="normalNegro" id="cuerpo" name="cuerpo" cols="93" rows="4" onkeyup="validarTexto(this);">
Solicitud de cotizaci&oacute;n.

Fundaci&oacute;n Infocentro (RIF G-200077280)</textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					Rubros de la Cotizaci&oacute;n:
					<table width="900px" class="tablaalertas">
						<tr class="td_gray">
							<th style="width: 20px;"><input type="checkbox" id="controlItems" name="controlItems" onclick="marcarTodosNingunoItems();"/></th>
							<th>C&oacute;digo</th>
							<th>Nombre</th>
							<th>Partida</th>
							<th>Denominaci&oacute;n</th>
							<th>Especificaciones</th>
							<th>Cantidad</th>
						</tr>
						<?php
						$query = 	"SELECT DISTINCT ".
										"sri.numero_item, ".
										"si.id, ".
										"si.nombre, ".
										"sri.rbms_item_cantidad as cantidad, ".
										"sri.rbms_item_desc as descripcion, ".
										"sp.part_id as id_partida, ".
										"sp.part_nombre as nombre_partida ".
									"FROM ".
										"sai_rqui_items sri, sai_item si, sai_item_partida sip, sai_partida sp ".
									"WHERE ".
										"sri.rebms_id = '".$idRequ."' AND ".
										"sri.rbms_item_arti_id = si.id AND ".
										"sri.rbms_item_arti_id = sip.id_item AND ".
										"sip.part_id = sp.part_id AND ".
										"sp.pres_anno = ".$_SESSION['an_o_presupuesto']." ";
						$resultadoItems = pg_exec($conexion, $query);
						$elementos = pg_numrows($resultadoItems);
						for($ri=0;$ri<$elementos;$ri++){
							$row = pg_fetch_array($resultadoItems, $ri);
	   						echo "<td align='center'><input type='checkbox' id='items".$row["numero_item"]."' name='items".$row["numero_item"]."' value='".$row["numero_item"]."' onclick='agregarQuitarItem(this);'/></td>
						   	<td align='center'>".$row["id"]."</td>
						   	<td>".$row["nombre"]."</td>
						   	<td align='center'>".$row["id_partida"]."</td>
						   	<td>".$row["nombre_partida"]."</td>
						   	<td>".$row["descripcion"]."</td>
						   	<td align='center'>".$row["cantidad"]."</td>
						   	</tr>\n";
						}
					?>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					Enviar a:
					<table width="900px" class="tablaalertas">
						<tr class="td_gray">
							<th style="width: 20px;"><input type="checkbox" id="controlProveedores" name="controlProveedores" onclick="marcarTodosNinguno();"/></th>
							<th>RIF</th>
							<th>Proveedor</th>
							<th>&Uacute;ltima solicitud de cotizaci&oacute;n enviada en fecha:</th>
						</tr>
<?
	for($ri = 0; $ri < $numeroFilas; $ri++) {
	   	echo "<tr class='normalNegro'>\n";
	   	$row = pg_fetch_array($resultado, $ri);
	   	$pos = strpos($proveedores, $row[0]);
	   	echo "<td align='center'><input type='checkbox' id='proveedores".$row[0]."' name='proveedores".$row[0]."' value='".$row[0]."' onclick='agregarQuitarProveedor(this);'".(($pos)?" checked='checked'":"")."/></td>
	   	<td style='text-align: center;'>", strtoupper(substr(trim($row[0]),0,1))."-".substr(trim($row[0]),1), "</td>
	   	<td>", $row[1], "</td>
	   	<td style='text-align: center;'>", ($row[2]!="")?$row[2]." por usuario ".$row[4]:"no enviada", "</td></td></tr>\n";
	    if ($pos) {
?>
			<script>
				proveedoresIndice++;
				proveedores[proveedoresIndice] = new String("<?= $row[0]?>");
			</script>
<?php
	    }
	}
?>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="text-align: left;width: 100%;">
						<p class="normal peq_naranja">Se generar&aacute; y adjuntar&aacute; al correo electr&oacute;nico un archivo PDF, el cual describir&aacute; los art&iacute;culos de la requisici&oacute;n junto con la siguiente informaci&oacute;n:</p>
					</div>
				</td>
			</tr>
		</table>
<?php
}
?>
		<table width="900px">
			<tr>
				<td width="15%"> Tiempo de entrega: </td>
				<td width="85%">
					<input class="normalNegro" type="text" id="tiempoEntrega" name="tiempoEntrega" size="75" maxlength="30" value="7 d&iacute;as" onkeyup="validarTexto(this);"/>
				</td>
			</tr>
			<tr>
				<td> Sitio de entrega: </td>
				<td>
					<textarea class="normalNegro" id="sitioEntrega" name="sitioEntrega" cols="73" rows="2" onkeyup="validarTexto(this);">Av. Universidad, Esquina El Chorro, Torre MCT (Antigua sede de Banesco), piso 11, La Hoyada, Oficina de compras, Caracas, Dtto. Capital</textarea>
				</td>
			</tr>
			<tr>
				<td> Cotizaci&oacute;n a nombre de: </td>
				<td>
					<?php
					$rifFundacion = "g200077280";
					$query = "SELECT prov_nombre FROM sai_proveedor_nuevo WHERE LOWER(prov_id_rif) like '%".$rifFundacion."%'";
					$resultado = pg_exec($conexion, $query);
					$elementos = pg_numrows($resultado);
					if($elementos>0){
						$row = pg_fetch_array($resultado, 0);
						$nombreFundacion = $row["prov_nombre"]." (RIF ".strtoupper(substr(trim($rifFundacion),0,1))."-".substr(trim($rifFundacion),1).")";
					}
					?>
					<input type="hidden" id="beneficiario" name="beneficiario" value="<?= $rifFundacion?>"/>
					<input value="<?= $nombreFundacion?>" readonly="readonly" class="normalNegro" size="75"/>				
				</td>
			</tr>
			<tr>
				<td> Anexos a incluir en la cotizaci&oacute;n: </td>
				<td>
					<textarea class="normalNegro" id="anexos" name="anexos" cols="73" rows="6" onkeyup="validarTexto(this);">
Validez de la oferta. 
Tiempo de entrega a tiempo determinado. 
Garant&iacute;a. 
Condiciones de pago (Preferiblemente 15 a 30 d&iacute;as). 
Si est&aacute; incluido o no el I.V.A en los precios cotizados. 
Disponibilidad. 
Especificaciones.</textarea>
				</td>
			</tr>
			<tr>
				<td> Observaciones: </td>
				<td>
					<textarea class="normalNegro" id="observaciones" name="observaciones" cols="73" rows="2" onkeyup="validarTexto(this);">Debe enviar v&iacute;a fax su oferta y/o correo electr&oacute;nico a la brevedad posible. (PREFERIBLEMENTE NO MAYOR A 48 HORAS).</textarea>
				</td>
			</tr>
<tr>
						 <td>
						 <br/>
						 <fieldset style="width: 400px;">
							<legend ><b>Respaldos digitales</b></legend>
							
								<table > 
								 <tr id="trActualesDigital">     	     
                              	        	<th class="normalNegroNegrita" style="color:#585858" align="left"> * Actuales</th>	
                              	    
                              	    </tr>
                              	 <tbody id="tbodyRespDigitales" >
                              	  
                                </tbody>
                                 <tr>
                              	        	<th class="normalNegroNegrita" align="left" style="color:#585858;"> * Por subir</th>	
                              	    
                              	    
                              	    </tr>
                              	   
                                 <tr>
                              	      <td>
                              	        <div align="left" class="normal">
								<strong style="margin-right: 136px;">Seleccione los archivos (m&aacute;ximo 5) : </strong>
								<br/><br/>
						     <input id="file_upload" name="file_upload" type="file" multiple="true">
		                     </div>
                              	      </td>
                              	    
                              	 </tr>
                                </table>
							
							</fieldset>
						 </td>
						</tr>
		</table>
	</form>
	
		</td>
	</tr>
</table>
<br/>
<div id="divAcciones" style="text-align: center;">
	<input type="button" class="normalNegro" value="Enviar" onclick="enviar();"/>
</div>
</body>
</html>
<?php pg_close($conexion); ?>