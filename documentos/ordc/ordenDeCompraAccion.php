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

$fec = $_POST['fechadeOrden'];
$fecha = explode ('/',$fec);
$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.strftime('%H:%M:%S');

$error = "";
$idRequ=trim($_REQUEST['idRequ']);
if($idRequ==""){
	$error = "1";//Debe indicar el id de la requisición.
}
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
	$fechaEntrega=trim($_REQUEST['fechaEntrega']);//REVISAR
	$garantiaAnticipo=trim($_REQUEST['garantiaAnticipo']);//REVISAR
	$condicionesEntrega=trim($_REQUEST['condicionesEntrega']);//REVISAR
	$otrasGarantias=trim($_REQUEST['otrasGarantias']);//REVISAR
	$otrasCondiciones=trim($_REQUEST['otrasCondiciones']);//REVISAR

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
	
	$sql="select * from sai_insert_orden_de_compra('".$proveedor."',".
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
													"'".$idRequ."',".
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
	$resultado_insert = pg_exec($conexion, $sql);
	if($resultado_insert){
		$row = pg_fetch_array($resultado_insert,0);
		
		$user_perfil_id = $_SESSION['user_perfil_id'];
		if ($row[0] <> null){
			$codigo=trim($row[0]);
			
			//fecha de orden se cambia a lo elegido
			
			$sqlfecha=	"update sai_orden_compra set fecha = '".$fecha2."' where ordc_id = '".$codigo."'";
			//error_log(print_r($sqlfecha,true));
			$resultado = pg_exec($conexion ,$sqlfecha);
			
			// fin fecha de orden se cambia a lo elegido
			
			$documentoOrdenDeCompra = "ordc";
			$objeto = 1;
			$sql=	"SELECT ".
						"swc.wfca_id, ".
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
				$wfgr_perf=trim($row["wfgr_perf"]);
						
				$sql="select * from sai_insert_doc_generado('".$codigo."',1,".$wfca_id.",'".$usua_login."','".$user_perfil_id."',10,1,'".$wfgr_perf."','N/A') as resultado_set(text)";
				$resultado_insert = pg_exec($conexion ,$sql);			
			}
		}
	}
	header("Location:detalleOrdenDeCompra.php?idOrdc=".$codigo."&accion=generar",false);
}else{
	header("Location:ordenDeCompra.php?idRequ=".$idRequ."&msg=".$error,false);
}
ob_end_flush();
pg_close($conexion);
?>