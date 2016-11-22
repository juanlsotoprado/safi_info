<?php

require_once(dirname(__FILE__) . '/../../../init.php');
require_once (SAFI_LIB_PATH . '/general.php');
// Modelo
require_once(SAFI_MODELO_PATH. '/firma.php');
ob_start();

$firmasSeleccionadas = array();
$firmasSeleccionadas = SafiModeloFirma::GetFirmaByPerfiles(array('46450', '65150'));

require("../../../includes/conexion.php");
require_once("../../../includes/fechas.php");
require("../../../includes/funciones.php");
if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../../includes/reporteBasePdf.php"); 
require("../../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../../includes/html2ps/funciones.php");


if (isset($_POST['rif']) && $_POST['rif'] != "") {
	$rif_prov = $_POST['rif'];
}

if($rif_prov && $rif_prov!=""){
	
	list( $rif, $nombre ) = split( ':', $_POST['rif'] );
	
    $sql="select prov_nombre,prov_domicilio,prov_tel_c from sai_proveedor_nuevo where prov_id_rif='".trim($rif)."'";
    $result=pg_query($conexion,$sql);
    if ($prov=pg_fetch_array($result)){
    	$prov_nombre=$prov['prov_nombre'];
    	$prov_direcc=$prov['prov_domicilio'];
    	$tlf=$prov['prov_tel_c'];
    }

	$anno_pres=$_POST['a_o'];//$_SESSION['an_o_presupuesto'];

	$contenido = "<style type='text/css'>
						.nombreCampo{
							font-weight: bold; 
							font-size: 14px; 
							font-family: arial; 
							TEXT-DECORATION: none
						}
						.bordeTabla{
							border: solid 1px #000000;
						}
						.textoTabla{
							font-weight: normal; 
							font-size: 14px; 
							font-family: arial; 
							TEXT-DECORATION: none
						}
						.titulo{
							font-weight: bold; 
							font-size: 18px; 
							font-family: arial; 
							TEXT-DECORATION: none
						}
					</style>";
	
    $contenido .="<br><div align='center'><span class='titulo'> ARCV </span></div>";
	$contenido .="<div align='center'><span class='titulo'> COMPROBANTE DE RETENCIONES VARIAS DEL IMPUESTO SOBRE LA RENTA A&Ntilde;O ".$anno_pres." </span></div>";
	$contenido .="<div align='center'><span class='nombreCampo'>Cierre del Ejercicio ".$anno_pres."</span><br>Periodo del 01/01/".substr($anno_pres,2,2)." al 31/12/".substr($anno_pres,2,2)."</div>";
	$contenido .="<br><table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	if ($pcta_id=="0"){
		$pcta_id="N/A";
	}
	$contenido .="<tr>";
	$contenido .="<td><span class='nombreCampo' width='50%'>DATOS DEL AGENTE DE RETENCI&Oacute;N: </span><span class='textoTabla'>".$codigo."</span></td>";
	$contenido .="<td><span class='nombreCampo' width='50%'>DATOS DEL BENEFICIARIO: </span><span class='textoTabla'>".$codigo."</span></td>";
	$contenido .="</tr>";
	$contenido .="<tr>";
	$contenido .="<td><span class='nombreCampo'>Raz&oacute;n Social:</span><span class='textoTabla'> Fundaci&oacute;n Infocentro</span></td>";
	$contenido .="<td><span class='nombreCampo'>Nombre: </span><span class='textoTabla'>".$prov_nombre."</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td><span class='nombreCampo'>RIF N&deg;:</span>  G-20007728-0</td>";
	$contenido .="<td><span class='nombreCampo'>RIF N&deg;:</span> ".$rif."</td>";
	$contenido .="</tr>";

	$contenido .="<tr>";
	$contenido .="<td><span class='nombreCampo' width='50%'>Direcci&oacute;n:</span> Av. Universidad Esquina del Chorro, Torre Ministerial Piso 11</td>";
	$contenido .="<td><span class='nombreCampo' width='50%'>Direcci&oacute;n:</span> ".$prov_direcc."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td><span class='nombreCampo'>Tel&eacute;fonos: </span> 0212-7718812 </td>";
	$contenido .="<td><span class='nombreCampo'>Tel&eacute;fonos: </span> ".$tlf." </td>";
	$contenido .="</tr>";
	$contenido .="</table>";
	
	$contenido .="<br><table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";

	$contenido .="<tr class='nombreCampo' align='center'>";
	$contenido .="<td>Mes</td>";
	$contenido .="<td>Factura</td>";
	$contenido .="<td>Cantidad Pagada</td>";
	$contenido .="<td>Cantidad Obj. Retenci&oacute;n</td>";
	$contenido .="<td>Impuesto Retenido</td>";
	$contenido .="<td>Cantidad Acumulada</td>";
	$contenido .="<td>Impuesto Acumulado</td>";
	$contenido .="<td>Fecha Enterado</td>";
	$contenido .="<td>Banco</td></tr>";
	
	$indice=1;
	$acumulado=0;
	$pagado=0;
	
	while ($indice<13){

	 if ($indice<10){
	  $fi=$anno_pres."-0".$indice."-01";
	  $ff=$anno_pres."-0".$indice."-".dias_del_mes($anno_pres,$indice)." 23:59:59";
	 }else{
		$fi=$anno_pres."-".$indice."-01";
		$ff=$anno_pres."-".$indice."-".dias_del_mes($anno_pres,$indice)." 23:59:59";
	 }
     
	 if ($indice<12){
	 $mes=$indice+1;
	  $fecha=$anno_pres."-0".$mes;
	 }else{
	  $fecha=$anno_pres."-01";}
	 
	$cheque="select * from sai_cheque t1,sai_chequera t2,sai_sol_pago t3 
	where t1.nro_chequera=t2.nro_chequera and estatus_cheque<>15 and ctab_numero like '%76363' and fechaemision_cheque like '".$anno_pres."%' and sopg_id=docg_id and sopg_tp_solicitud='43' and fechaemision_cheque like '".$fecha."%'";

	 $resultado_cheque=pg_query($conexion,$cheque);
	 if ($row_cheque=pg_fetch_array($resultado_cheque)){
	 	$fecha_cheque=cambia_esp(substr($row_cheque['fechaemision_cheque'],0,10));
	 } 
                       
	$query="select sopg_factura,monto_cheque,sopg_monto_base,sopg_por_rete,sopg_ret_monto,max(cast(substr(scd.comp_id,6) as numeric)) as comp 
			from sai_sol_pago sp,sai_sol_pago_retencion spr,sai_comp_diario scd,sai_cheque sc
		    where sc.docg_id=sp.sopg_id and spr.sopg_id=sc.docg_id and scd.comp_doc_id=sc.docg_id 
			
				--and  sp.sopg_id like '%11' 
			
			and sp.esta_id<>15 and sp.sopg_id=spr.sopg_id and 
		    impu_id='ISLR' and scd.comp_id like 'coda%' and comp_doc_id=sp.sopg_id and 
		    sopg_bene_ci_rif='".trim($rif)."' and comp_fec between '".$fi."' and '".$ff."' 
		    group by sp.sopg_id,sopg_factura,monto_cheque,sopg_monto_base,sopg_por_rete,sopg_ret_monto
		    order  by sp.sopg_id";
	$resultado=pg_query($conexion,$query);
	while ($row=pg_fetch_array($resultado)){
	  
	 $contenido .="<tr class='textoTabla'>";
	 $contenido .="<td>".convertir_mes_letras($indice)."</td>";
	 $contenido .="<td align='center'>".$row['sopg_factura']."</td>";
	 $contenido .="<td align='right'>".number_format($row['monto_cheque'],2,',','.')."</td>";
	 $pagado=$pagado+$row['monto_cheque'];
	 $contenido .="<td align='right'>".number_format($row['sopg_monto_base'],2,',','.')."</td>";
	 $contenido .="<td align='center'>".$row['sopg_por_rete']."</td>";
	 $contenido .="<td align='right'>".number_format($row['sopg_ret_monto'],2,',','.')."</td>";
	 $acumulado=$acumulado+$row['sopg_ret_monto'];
	 $contenido .="<td align='right'>".number_format($acumulado,2,',','.')."</td>";
	 $contenido .="<td align='center'>".$fecha_cheque."</td>";
	 $contenido .="<td align='center'>Banco Industrial</td></tr>";
	}
    $indice++;

	}
	 $contenido .="<tr class='nombreCampo'>";
	 $contenido .="<td>".TOTAL."</td>";
	 $contenido .="<td align='center'>&nbsp;</td>";
	 $contenido .="<td align='right'>".number_format($pagado,2,',','.')."</td>";
	 $contenido .="<td align='right'>".number_format($row['sopg_monto_base'],2,',','.')."</td>";
	 $contenido .="<td align='center'>&nbsp;</td>";
	 $contenido .="<td align='right'>".number_format($acumulado,2,',','.')."</td>";
	 $acumulado=$acumulado+$row['sopg_ret_monto'];
	 $contenido .="<td align='right'>".number_format($acumulado,2,',','.')."</td>";
	 $contenido .="<td align='center'>&nbsp;</td>";
	 $contenido .="<td align='center'>&nbsp;</td></tr>";
	 
	$contenido .="</table>";
	
	$contenido .="<br><table align='center' width='400px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	$contenido .="<tr>";
	$contenido .="<td align='center'><span class='nombreCampo'>Agente de Retenci&oacute;n </span></td>";
	$contenido .="</tr>";
	$contenido .="<tr>";
	$contenido .="<td align='center' rowspan='5' height='50'><span class='nombreCampo'>&nbsp;</span></td>";
	$contenido .="</tr></table>";

	$contenido .="<div align='center' ><span class='nombreCampo'>".$firmasSeleccionadas['46450']['nombre_empleado']."<br/>".$firmasSeleccionadas['46450']['nombre_cargo']." de ". $firmasSeleccionadas['46450']['nombre_dependencia']."</span></div>";

    $properties = array("marginBottom" => 40, "footerHtml" => $footer, "landscape" => true);
    
	ob_clean();
	convert_to_pdf($contenido,$properties);
}
pg_close($conexion);
?>
