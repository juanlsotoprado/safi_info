<?php
ob_start();
require("../../includes/conexion.php");
require_once("../../includes/fechas.php");

if	( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}

require("../../includes/reporteBasePdf.php"); 
require("../../includes/html2ps/config.inc.php");
require(HTML2PS_DIR.'pipeline.factory.class.php');
require("../../includes/html2ps/funciones.php");


$codigo = "";
if (isset($_GET['codigo']) && $_GET['codigo'] != "") {
	$codigo = $_GET['codigo'];
}

if($codigo && $codigo!=""){
	$anno_pres=$_SESSION['an_o_presupuesto'];
	$query =	"SELECT etiqueta,sbi.esta_id,to_char(fecha_registro,'DD/MM/YYYY') as fecha,pcta_id,num_licitacion,".
				"se.empl_nombres || ' ' || se.empl_apellidos as elaborado,sd.depe_nombre, bmarc_nombre,si.nombre,modelo,serial,precio,garantia,descripcion,proveedor, ".
				"case proveedor when '' then '--' else 
				 (select prov_nombre from sai_proveedor_nuevo where prov_id_rif=proveedor)  end as nombre_proveedor, ".
				"to_char(fecha_entrada,'DD/MM/YYYY') as fecha_entrada ".
				"FROM sai_bien_inco sbi
				 left outer join sai_biin_items sbii on (sbi.acta_id=sbii.acta_id)
				 left outer join sai_item si on (si.id=sbii.bien_id)
				 left outer join sai_empleado se on (sbi.usua_login=se.empl_cedula)
				 left outer join sai_dependenci sd on (sbi.depe_solicitante=sd.depe_id)
				 left outer join sai_bien_marca sbm on (sbm.bmarc_id=sbii.marca_id)
				  WHERE sbi.acta_id=trim('".$codigo."') ";
	
	$resultado=pg_query($conexion,$query);
	$row=pg_fetch_array($resultado);
    
	$esta_id=$row["esta_id"];
	$fecha_acta=$row["fecha"];
	$elaborado=$row["elaborado"];
	$depe_nombre=$row["depe_nombre"];
	$pcta=$row["pcta_id"];
	$licitacion=$row["num_licitacion"];
    $proveedor=$row["nombre_proveedor"];
    $fecha_entrada=$row['fecha_entrada'];

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
	

	$contenido .="<div align='center'><span class='titulo'> ENTRADA DE ACTIVOS</span></div>";
	$contenido .="<br><table width='1000px' class='bordeTabla textoTabla' border='1' cellspacing='0' cellpadding='0'>";
	if ($pcta_id=="0"){
		$pcta_id="N/A";
	}
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Acta: </span><span class='textoTabla'>".$codigo."</span></td>";
	$contenido .="</tr>";
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Fecha acta: </span><span class='textoTabla'>".$fecha_acta."</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Fecha recepci&oacute;n almac&eacute;n: </span><span class='textoTabla'>".$fecha_entrada."</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Dependencia solicitante:</span> ".$depe_nombre."</td>";
	$contenido .="</tr>";

	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Licitaci&oacute;n / Orden Compra:</span> ".$licitacion."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Punto de cuenta:</span> ".$pcta."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td colspan='2'><span class='nombreCampo'>Proveedor:</span> ".$proveedor."</td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td align='center' colspan='2'><span class='nombreCampo'>Activos Ingresados</span></td>";
	$contenido .="</tr>";
	
	$contenido .="<tr>";
	$contenido .="<td align='center' colspan='2'><table border='1' width='100%'><tr class='nombreCampo'>";
	$contenido .="<td>Nombre</td>";
	$contenido .="<td>Serial Bien Nacional</td>";
	$contenido .="<td>Marca</td>";
	$contenido .="<td>Modelo</td>";
	$contenido .="<td>Serial activo</td>";
	$contenido .="<td>Precio unitario</td>";
	$contenido .="<td>Garant&iacute;a</td></tr>";
	$resultado=pg_query($conexion,$query);
	while ($row=pg_fetch_array($resultado)){

	$contenido .="<tr class='textoTabla'>";
	$contenido .="<td>".$row['nombre']."</td>";
	$contenido .="<td>".$row['etiqueta']."</td>";
	$contenido .="<td>".$row['bmarc_nombre']."</td>";
	$contenido .="<td>".$row['modelo']."</td>";
	$contenido .="<td>".$row['serial']."</td>";
	$contenido .="<td>".$row['precio']."</td>";
	$contenido .="<td>".$row['garantia']." meses</td></tr>";
	}
	
	
	$contenido .="</table></td>";
	$contenido .="</tr>";


	$contenido .="<tr>";
	$contenido .="<td align='center' colspan='2'><span class='nombreCampo'>Firmas</span></td></tr>";
	$contenido .="<tr><td align='center'><span class='nombreCampo'><br>____________________<br/><br/> Realizado por:".strtoupper($elaborado)."<br><br></span></td>";
	$contenido .="<td align='center' ><span class='nombreCampo'><br>____________________<br/><br/>".utf8_decode('Coordinación de Bienes Nacionales')."</span></td>";

	$contenido .="</tr>";
	$contenido .="</table>";
    $properties = array("marginBottom" => 40, "footerHtml" => $footer, "landscape" => true);
    

	//echo $contenido;
	ob_clean();
	convert_to_pdf($contenido,$properties);
}
pg_close($conexion);
?>
