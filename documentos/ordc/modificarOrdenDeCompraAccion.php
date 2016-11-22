<?php
ob_start();
session_start();
require("../../includes/conexion.php");
require('../../includes/arreglos_pg.php');
require("../../includes/constantes.php");
require("../../includes/funciones.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
$error = "";
$idOrdc=trim($_REQUEST['idOrdc']);
if($idOrdc==""){
	$error = "1";//Debe indicar el id de la Orden de Compra.
}
$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}
$codigoCR = "";
if (isset($_GET['codigoCR']) && $_GET['codigoCR'] != "") {
	$codigoCR = $_GET['codigoCR'];
}
$tipoBusq = TIPO_BUSQUEDA_ORDENES_DE_COMPRA;	
if (isset($_GET['tipoBusq']) && $_GET['tipoBusq'] != "") {
	$tipoBusq = $_GET['tipoBusq'];
}
$tipoRequ = TIPO_REQUISICION_TODAS;
if (isset($_GET['tipoRequ']) && $_GET['tipoRequ'] != "") {
	$tipoRequ = $_GET['tipoRequ'];
}
$pagina = "1";
if (isset($_GET['pagina']) && $_GET['pagina'] != "") {
	$pagina = $_GET['pagina'];
}
$dependencia = "";
if (isset($_GET['dependencia']) && $_GET['dependencia'] != "") {
	$dependencia = $_GET['dependencia'];
}
$estado = ESTADO_REQUISICION_NO_REVISADAS;
if (isset($_GET['estado']) && $_GET['estado'] != "") {
	$estado = $_GET['estado'];
}
$rifProveedor = "";
if (isset($_POST['rifProveedor']) && $_POST['rifProveedor'] != "") {
	$rifProveedor = $_POST['rifProveedor'];
}
$nombreProveedor = "";
if (isset($_POST['nombreProveedor']) && $_POST['nombreProveedor'] != "") {
	$nombreProveedor = $_POST['nombreProveedor'];
}
$idItem = "";
if (isset($_GET['idItem']) && $_GET['idItem'] != "") {
	$idItem = $_GET['idItem'];
}
$nombreItem = "";
if (isset($_GET['nombreItem']) && $_GET['nombreItem'] != "") {
	$nombreItem = $_GET['nombreItem'];
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
$bandeja = "";
if (isset($_GET['bandeja']) && $_GET['bandeja'] != "") {
	$bandeja = $_GET['bandeja'];
}
$accion = "";
if (isset($_GET['accion']) && $_GET['accion'] != "") {
	$accion = $_GET['accion'];
}
if($accion==ACCION_ANULAR_REQUISICION){
	$user_perfil_id = $_SESSION['user_perfil_id'];
	$user_login = $_SESSION['login'];
	$estadoAnulado = 15;
	$opcionAnular = 24;
	
	//Se modifica el estado del documento generado a anulado	
	$sql="UPDATE sai_doc_genera SET esta_id = ".$estadoAnulado." WHERE docg_id = '".$idOrdc."'";
	$resultado = pg_exec($conexion ,$sql);

	//Se modifica el estado de la orden de compra a anulado
	$sql="UPDATE sai_orden_compra SET esta_id = ".$estadoAnulado." WHERE ordc_id = '".$idOrdc."'";
	$resultado = pg_exec($conexion ,$sql);
	
	//Insertar la revision
	$sql = " SELECT * FROM sai_insert_revision_doc('$idOrdc', '$user_login', '$user_perfil_id', '$opcionAnular', '') as resultado ";
	$resultado = pg_query($conexion,$sql);
	header("Location:detalleOrdenDeCompra.php?accion=anular&codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
}else{
	if($error==""){
		$proveedoresCadena=trim($_REQUEST['proveedores']);
		if($proveedoresCadena==""){
			$error = "2A";//Debe ingresar al menos una (1) cotización.
		}else{
			$tok = strtok($proveedoresCadena, ";");
			$proveedores = array();
			$i=0;
			while($tok !== false){
				$proveedores[$i]=$tok;
			    $tok = strtok(";");
			    $i++;
			}
			if(sizeof($proveedores)<1){
				$error = "2B";//Debe ingresar al menos una (1) cotización.	
			}
			
			$articulosAdicionalesCadena=trim($_REQUEST['items']);
			$articulosAdicionalesIdItem = array();
			$articulosAdicionalesNumeroItem = array();
			$articulosAdicionalesEspecificaciones = array();
			if($articulosAdicionalesCadena!=""){
				$i=0;
				$indiceToken = 0;
				$tok = strtok(substr($articulosAdicionalesCadena,$indiceToken), "|");
				$indiceToken = strpos($articulosAdicionalesCadena, $tok)+strlen($tok);
				while($tok !== false){
					$tokArticulos = strtok($tok, "~");
					$k = 0;
					while($tokArticulos !== false){
						if($k==0){
							$idItem=$tokArticulos;
						}else if($k==1){
							$numeroItem=$tokArticulos;
						}else if($k==2){
							$especificaciones=validarTexto(trim($tokArticulos));
						}
					    $tokArticulos = strtok("~");
					    $k++;
					}
					$articulosAdicionalesIdItem[$i]=$idItem;
					$articulosAdicionalesNumeroItem[$i]=$numeroItem;
					$articulosAdicionalesEspecificaciones[$i]=$especificaciones;
					$tok = strtok(substr($articulosAdicionalesCadena,$indiceToken), "|");
					$indiceToken = strpos($articulosAdicionalesCadena, $tok)+strlen($tok);
					$i++;
				}
			}
			
			$i=0;
			$k=0;
			$fechaCotizacionProveedores = array();
			$proveedoresCantidadBases = array();
			
			//De la requisicion
			$proveedoresCantidadArticulos = array();
			$proveedoresArticulos = array();
			$proveedoresNumeroItems = array();
			$proveedoresCantidades = array();
			$proveedoresPrecios = array();
			$proveedoresUnidades = array();
			
			//De la orden de compra
			$proveedoresCantidadArticulosAdicionales = array();
			$proveedoresArticulosAdicionales = array();
			$proveedoresNumeroItemsAdicionales = array();
			$proveedoresCantidadesAdicionales = array();
			$proveedoresPreciosAdicionales = array();
			$proveedoresUnidadesAdicionales = array();
		
			$proveedoresIvas = array();
			$proveedoresBases = array();
			$proveedoresRedondear = array();
			$hayCotizacion = false;
			while($i<sizeof($proveedores)){
				$fechaCotizacionProveedores[$i] = $_REQUEST['fechaCotizacion'.$proveedores[$i]];
				$proveedoresRedondear[$i] = $_REQUEST['redondear'.$proveedores[$i]];
				
				//De la requisicion
				$proveedoresArticulosAuxiliar = array();
				$proveedoresNumeroItemsAuxiliar = array();
				$proveedoresCantidadesAuxiliar = array();
				$proveedoresPreciosAuxiliar = array();
				$proveedoresUnidadesAuxiliar = array();
				
				//De la orden de compra
				$proveedoresArticulosAdicionalesAuxiliar = array();
				$proveedoresNumeroItemsAdicionalesAuxiliar = array();
				$proveedoresCantidadesAdicionalesAuxiliar = array();
				$proveedoresPreciosAdicionalesAuxiliar = array();
				$proveedoresUnidadesAdicionalesAuxiliar = array();
				
				$proveedoresIvasAuxiliar = array();
				$proveedoresBasesAuxiliar = array();
				$proveedoresBasesCadena = $_REQUEST['base'.$proveedores[$i]];			
				if($proveedoresBasesCadena!=""){
					$j = 0;
					$indiceToken = 0;
					$tok = strtok(substr($proveedoresBasesCadena,$indiceToken), ";");
					$indiceToken = strpos($proveedoresBasesCadena, $tok)+strlen($tok);
					while($tok !== false){
						$tokArticulos = strtok($tok, ",");
						$k = 0;
						while($tokArticulos !== false){
							if($k==0){
								$iva=$tokArticulos;
							}else if($k==1){
								$base=$tokArticulos;
							}
						    $tokArticulos = strtok(",");
						    $k++;
						}
						if($base!="" && $base!="0"){
							$proveedoresIvasAuxiliar[sizeof($proveedoresIvasAuxiliar)]=$iva;
							$proveedoresBasesAuxiliar[sizeof($proveedoresBasesAuxiliar)]=$base;
						}
					    $tok = strtok(substr($proveedoresBasesCadena,$indiceToken), ";");
						$indiceToken = strpos($proveedoresBasesCadena, $tok)+strlen($tok);
					    $j++;
					}
					if(sizeof($proveedoresIvasAuxiliar)==0 || sizeof($proveedoresBasesAuxiliar)==0){
						$proveedoresIvasAuxiliar[0]=0;
						$proveedoresBasesAuxiliar[0]=0;
					}				
				}else{
					$proveedoresIvasAuxiliar[0]=0;
					$proveedoresBasesAuxiliar[0]=0;
				}
				$proveedoresCantidadBases[$i]=sizeof($proveedoresBasesAuxiliar);
				$proveedoresIvas[$i]=$proveedoresIvasAuxiliar;
				$proveedoresBases[$i]=$proveedoresBasesAuxiliar;
				
				$proveedoresArticulosCadena = $_REQUEST['cotizacion'.$proveedores[$i]];
				$j = 0;
				$indiceToken = 0;
				$tok = strtok(substr($proveedoresArticulosCadena,$indiceToken), ";");
				$indiceToken = strpos($proveedoresArticulosCadena, $tok)+strlen($tok);
				while($tok !== false){
					$tokArticulos = strtok($tok, ",");
					$k = 0;
					while($tokArticulos !== false){
						if($k==0){
							$articulo=$tokArticulos;
						}else if($k==1){
							$cantidad=$tokArticulos;
						}else if($k==2){
							$precio=$tokArticulos;
						}else if($k==3){
							$unidad=$tokArticulos;
						}else if($k==4){
							$numeroItem=$tokArticulos;
						}else if($k==5){
							$tipoItem=$tokArticulos;
						}
					    $tokArticulos = strtok(",");
					    $k++;
					}
					if($tipoItem=="rqui"){
						$proveedoresArticulosAuxiliar[sizeof($proveedoresArticulosAuxiliar)]=$articulo;
						$proveedoresNumeroItemsAuxiliar[sizeof($proveedoresNumeroItemsAuxiliar)]=$numeroItem;
						$proveedoresCantidadesAuxiliar[sizeof($proveedoresCantidadesAuxiliar)]=$cantidad;
						$proveedoresPreciosAuxiliar[sizeof($proveedoresPreciosAuxiliar)]=$precio;
						$proveedoresUnidadesAuxiliar[sizeof($proveedoresUnidadesAuxiliar)]=$unidad;
					}else if($tipoItem=="ordc"){
						$proveedoresArticulosAdicionalesAuxiliar[sizeof($proveedoresArticulosAdicionalesAuxiliar)]=$articulo;
						$proveedoresNumeroItemsAdicionalesAuxiliar[sizeof($proveedoresNumeroItemsAdicionalesAuxiliar)]=$numeroItem;
						$proveedoresCantidadesAdicionalesAuxiliar[sizeof($proveedoresCantidadesAdicionalesAuxiliar)]=$cantidad;
						$proveedoresPreciosAdicionalesAuxiliar[sizeof($proveedoresPreciosAdicionalesAuxiliar)]=$precio;
						$proveedoresUnidadesAdicionalesAuxiliar[sizeof($proveedoresUnidadesAdicionalesAuxiliar)]=$unidad;
					}
				    $tok = strtok(substr($proveedoresArticulosCadena,$indiceToken), ";");
					$indiceToken = strpos($proveedoresArticulosCadena, $tok)+strlen($tok);
				    $j++;
				}
				
				$proveedoresCantidadArticulos[$i]=sizeof($proveedoresArticulosAuxiliar);
				$proveedoresArticulos[$i]=$proveedoresArticulosAuxiliar;
				$proveedoresNumeroItems[$i]=$proveedoresNumeroItemsAuxiliar;
				$proveedoresCantidades[$i]=$proveedoresCantidadesAuxiliar;
				$proveedoresPrecios[$i]=$proveedoresPreciosAuxiliar;
				$proveedoresUnidades[$i]=$proveedoresUnidadesAuxiliar;
				
				$proveedoresCantidadArticulosAdicionales[$i]=sizeof($proveedoresArticulosAdicionalesAuxiliar);
				$proveedoresArticulosAdicionales[$i]=$proveedoresArticulosAdicionalesAuxiliar;
				$proveedoresNumeroItemsAdicionales[$i]=$proveedoresNumeroItemsAdicionalesAuxiliar;
				$proveedoresCantidadesAdicionales[$i]=$proveedoresCantidadesAdicionalesAuxiliar;
				$proveedoresPreciosAdicionales[$i]=$proveedoresPreciosAdicionalesAuxiliar;
				$proveedoresUnidadesAdicionales[$i]=$proveedoresUnidadesAdicionalesAuxiliar;
				
				if(sizeof($proveedoresArticulosAuxiliar)>0 && sizeof($proveedoresNumeroItemsAuxiliar)>0 && sizeof($proveedoresCantidadesAuxiliar)>0 && sizeof($proveedoresPreciosAuxiliar)>0 && sizeof($proveedoresUnidadesAuxiliar)>0){
					$hayCotizacion = true;
				}
				$i++;
			}
			if(sizeof($proveedoresArticulos)<1 || sizeof($proveedoresNumeroItems)<1 || sizeof($proveedoresCantidades)<1 || sizeof($proveedoresPrecios)<1 || sizeof($proveedoresUnidades)<1){
				$error = "2";//Debe ingresar al menos una (1) cotización.	
			}
			if($error==""){
				if($hayCotizacion==false){
					$error = "2";//Debe ingresar al menos una (1) cotización.	
				}
			}
		}
	}
	if($error==""){
		$proveedor=trim($_REQUEST['proveedor']);
		if($proveedor==""){
			$error = "3";//Debe indicar el proveedor que seleccionó.
		}
	}
	if($error==""){
		$criterio=trim($_REQUEST['criterio']);
		$observaciones=trim($_REQUEST['observaciones']);
		if($criterio==""){
			$error = "4";//Debe indicar el criterio de selección del proveedor.
		}else if($criterio=="6" && $observaciones==""){
			$error = "5";//El criterio de selección indicado es "Otros", por lo tanto debe especificarlo en las Observaciones.
		}
	}
	if($error==""){
		$formaPago=trim($_REQUEST['formaPago']);
		if($formaPago==""){
			$error = "6";//Debe indicar la Forma de Pago de la Orden de Compra.
		}
	}
	if($error==""){
		$lugarEntrega=trim($_REQUEST['lugarEntrega']);
		if($lugarEntrega==""){
			$error = "7";//Debe indicar el Lugar de Entrega de la Orden de Compra.
		}
	}
	if($error==""){
		$justificacion=trim($_REQUEST['justificacion']);
		if($justificacion==""){
			$error = "8";//Debe indicar la Justificación de la Orden de Compra.
		}
	}
	
	if($error == ""){
		$fechaEntrega=trim($_REQUEST['fechaEntrega']);
		$garantiaAnticipo=trim($_REQUEST['garantiaAnticipo']);
		$condicionesEntrega=trim($_REQUEST['condicionesEntrega']);
		$otrasGarantias=trim($_REQUEST['otrasGarantias']);
		$otrasCondiciones=trim($_REQUEST['otrasCondiciones']);
	
		$usua_login=$_SESSION['login'];
		$depe_id=$_SESSION['user_depe_id'];
		$estado = 10;
	
		$arregloProveedores=convierte_arreglo($proveedores);
		$arregloFechaCotizacion=convierte_arreglo($fechaCotizacionProveedores);
		$arregloRedondear=convierte_arreglo($proveedoresRedondear);
		$arregloCantidadBases=convierte_arreglo_numeros($proveedoresCantidadBases);
		
		$arregloArticulosAdicionalesIdItem=convierte_arreglo($articulosAdicionalesIdItem);
		$arregloArticulosAdicionalesNumeroItem=convierte_arreglo($articulosAdicionalesNumeroItem);
		$arregloArticulosAdicionalesEspecificaciones=convierte_arreglo($articulosAdicionalesEspecificaciones);
		
		$matrizIvas=matrizACadena($proveedoresIvas);
		$matrizBases=matrizACadena($proveedoresBases);
		
		$arregloCantidadArticulos=convierte_arreglo($proveedoresCantidadArticulos);
		$matrizArticulos=matrizACadena($proveedoresArticulos);
		$matrizNumeroItems=matrizACadena($proveedoresNumeroItems);
		$matrizCantidades=matrizACadena($proveedoresCantidades);
		$matrizPrecios=matrizACadena($proveedoresPrecios);
		$matrizUnidades=matrizACadena($proveedoresUnidades);
		
		$arregloCantidadArticulosAdicionales=convierte_arreglo($proveedoresCantidadArticulosAdicionales);
		$matrizArticulosAdicionales=matrizACadena($proveedoresArticulosAdicionales);
		$matrizNumeroItemsAdicionales=matrizACadena($proveedoresNumeroItemsAdicionales);
		$matrizCantidadesAdicionales=matrizACadena($proveedoresCantidadesAdicionales);
		$matrizPreciosAdicionales=matrizACadena($proveedoresPreciosAdicionales);
		$matrizUnidadesAdicionales=matrizACadena($proveedoresUnidadesAdicionales);
		
		$sql="select * from sai_modificar_orden_de_compra('".$proveedor."',".
														"'".$criterio."',".
														"'".$observaciones."',".
														"'".$justificacion."',".
														"'".$fechaEntrega."',".
														"'".$formaPago."',".
														"'".$garantiaAnticipo."',".
														"'".$lugarEntrega."',".
														"'".$condicionesEntrega."',".
														"'".$otrasGarantias."',".
														"'".$otrasCondiciones."',".
														"'".$idOrdc."',".
														"'".$depe_id."',".
														"'".$usua_login."',".
														"".$estado.",".
														"'".$arregloProveedores."',".
														"'".$arregloFechaCotizacion."',".
														"'".$arregloRedondear."',".
														"'".$arregloCantidadArticulos."',".
														"'".$arregloCantidadArticulosAdicionales."',".
														"'".$arregloCantidadBases."',".
														"'".$matrizArticulos."',".
														"'".$matrizNumeroItems."',".
														"'".$matrizCantidades."',".
														"'".$matrizPrecios."',".
														"'".$matrizUnidades."',".
														"'".$matrizArticulosAdicionales."',".
														"'".$matrizNumeroItemsAdicionales."',".
														"'".$matrizCantidadesAdicionales."',".
														"'".$matrizPreciosAdicionales."',".
														"'".$matrizUnidadesAdicionales."',".
														"'".$matrizIvas."',".
														"'".$matrizBases."',".
														"'".(($arregloArticulosAdicionalesIdItem!="")?$arregloArticulosAdicionalesIdItem:"{}")."',".
														"'".(($arregloArticulosAdicionalesNumeroItem!="")?$arregloArticulosAdicionalesNumeroItem:"{}")."',".
														"'".(($arregloArticulosAdicionalesEspecificaciones!="")?$arregloArticulosAdicionalesEspecificaciones:"{}")."') as resultado_set(text)";
		
		$resultado_modificar = pg_exec($conexion ,$sql);
		
		if($resultado_modificar){		
			$user_perfil_id = $_SESSION['user_perfil_id'];
			$documentoOrdenDeCompra = "ordc";
			$objeto = 2;
			$sql=	"SELECT ".
						"swc.wfca_id, ".
						"swc.wfob_id_ini, ".
						"swfg.wfgr_perf ".
					"FROM sai_wfcadena swc, sai_wfcadena swch, sai_wfgrupo swfg ".
					"WHERE ".
						"swc.docu_id = '".$documentoOrdenDeCompra."' AND ".
						"swc.wfob_id_ini = ".$objeto." AND ".
						"swc.wfca_id_hijo = swch.wfca_id AND ".
						"swch.wfgr_id = swfg.wfgr_id";
			$resultado = pg_exec($conexion ,$sql);
			if($resultado){
				$row = pg_fetch_array($resultado,0);
				$wfca_id=trim($row["wfca_id"]);
				$wfob_id_ini=trim($row["wfob_id_ini"]);
				$wfgr_perf=trim($row["wfgr_perf"]);
				
				$sql="UPDATE sai_doc_genera SET wfob_id_ini = ".$wfob_id_ini.", wfca_id = ".$wfca_id.", perf_id_act = '".$wfgr_perf."', esta_id = ".$estado." WHERE docg_id = '".$idOrdc."'";
				$resultado_insert = pg_exec($conexion ,$sql);			
			}
		}
		header("Location:detalleOrdenDeCompra.php?accion=modificar&codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
	}else{
		header("Location:modificarOrdenDeCompra.php?msg=".$error."&codigo=".$codigo."&codigoCR=".$codigoCR."&idOrdc=".$idOrdc."&tipoRequ=".$tipoRequ."&pagina=".$pagina."&estado=".$estado."&controlFechas=".$controlFechas."&fechaInicio=".$fechaInicio."&fechaFin=".$fechaFin."&dependencia=".$dependencia."&rifProveedor=".$rifProveedor."&nombreProveedor=".$nombreProveedor."&idItem=".$idItem."&nombreItem=".$nombreItem."&bandeja=".$bandeja,false);
	}
}
ob_end_flush();
pg_close($conexion);
?>