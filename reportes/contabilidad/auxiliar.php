<?php
ob_start();//
session_start();
require_once("../../includes/conexion.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	ob_end_flush();
	exit;
}
ob_end_flush();

$datos = null;

if(isset($_POST["hid_desde_itin"]) && isset($_POST["hid_hasta_itin"])) {
	$fecha_inicio = $_POST["hid_desde_itin"];
	$fecha_fi = $_POST["hid_hasta_itin"];
	$login=$_SESSION['login'];
	/*Búsqueda de movimientos en las fechas registradas*/
	$sql= "
		
			SELECT TO_CHAR(s.fecha, 'DD/MM/YYYY') AS fecha,
			       s.comp_id,
			       CASE 
			         WHEN s.sopg  != '' AND spn.prov_id_rif != '' THEN spn.prov_nombre 
        			 WHEN s.sopg  != '' AND  empl.empl_cedula != '' THEN empl.empl_nombres || ' ' || empl.empl_apellidos
        			 WHEN s.sopg  != '' AND  svb.benvi_cedula != ''  THEN svb.benvi_nombres || ' ' || svb.benvi_apellidos
			         ELSE  '-'
			       END as beneficiario,
			       CASE 
			         WHEN Position('codi-' IN s.comp_id) = 1 THEN s.comentario 
			         ELSE ssp.sopg_observacion 
			       END AS comentario, 
			       s.monto, 
			       s.cpat_id,
				   cta.ctab_numero, 
			       s.sopg, 
			       s.fuente_financiamiento, 
			       s.nro_referencia,
					cuenta_banco.cuenta_bancaria
			FROM
				(SELECT
					s.fecha,
					s.comp_id,
					s.comentario,
					s.monto,
					s.cpat_id,
					s.sopg,
					sspi.fuente_financiamiento,
					scd.nro_referencia
				FROM
					(SELECT
						MAX(scd.comp_fec) AS fecha,
						MAX(scd.comp_id) AS comp_id,
						MAX(scd.comp_comen) AS comentario,
						SUM(src.rcomp_debe) - SUM(src.rcomp_haber) AS monto,
						src.cpat_id AS cpat_id,
						CASE
							WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
								comp_doc_id
							WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
								(SELECT
								 	docg_id
								  FROM sai_pago_cheque
								  WHERE pgch_id=comp_doc_id)
							WHEN substring(comp_doc_id, 1, 4)='tran' THEN
								(SELECT
									docg_id
								 FROM sai_pago_transferencia
								 WHERE trans_id=comp_doc_id)
						END AS sopg
					FROM
						sai_reng_comp src
						INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					WHERE
						scd.esta_id<>'15' AND
						src.cpat_id = '".$_POST["cuenta"]."' AND
						scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
					GROUP BY
						src.cpat_id,
						sopg
					) AS s
					LEFT OUTER JOIN sai_comp_diario scd ON (s.comp_id = scd.comp_id)
					LEFT OUTER JOIN
						(
							SELECT 
								sspi.sopg_id,
								sf1125.fuente_financiamiento
							FROM 
								sai_sol_pago_imputa sspi
								INNER JOIN sai_forma_1125 sf1125 ON (sspi.sopg_tipo_impu = sf1125.form_tipo AND sspi.sopg_acc_pp = sf1125.form_id_p_ac AND sspi.sopg_acc_esp = sf1125.form_id_aesp)
							GROUP BY 
								sspi.sopg_id,
								sf1125.fuente_financiamiento
						) AS sspi ON (s.sopg = sspi.sopg_id)
				WHERE
					s.sopg IS NOT NULL AND s.sopg <> ''
				GROUP BY
					s.fecha,
					s.comp_id,
					s.comentario,
					s.monto,
					s.cpat_id,
					s.sopg,
					sspi.fuente_financiamiento,
					scd.nro_referencia
				UNION
				SELECT
					s.fecha,
					s.comp_id,
					s.comentario,
					s.monto,
					s.cpat_id,
					s.sopg,
					src.fuente_financiamiento,
					scd.nro_referencia
				FROM
					(SELECT
						scd.comp_fec AS fecha,
						scd.comp_id AS comp_id,
						scd.comp_comen AS comentario,
						--MAX(scd.comp_fec) AS fecha,
						--MAX(scd.comp_id) AS comp_id,
						--MAX(scd.comp_comen) AS comentario,
	
						SUM(src.rcomp_debe)- SUM(src.rcomp_haber) AS monto,
						src.cpat_id AS cpat_id,
						CASE
							WHEN (substring(comp_doc_id, 1, 4)='sopg') THEN
								comp_doc_id
							WHEN (substring(comp_doc_id, 1, 4)='pgch') THEN
								(SELECT
								 	docg_id
								  FROM sai_pago_cheque
								  WHERE pgch_id=comp_doc_id)
							WHEN substring(comp_doc_id, 1, 4)='tran' THEN
								(SELECT
									docg_id
								 FROM sai_pago_transferencia
								 WHERE trans_id=comp_doc_id)
						END AS sopg
					FROM
						sai_reng_comp src
						INNER JOIN sai_comp_diario scd ON (scd.comp_id = src.comp_id)
					WHERE
						scd.esta_id<>'15' AND
						src.cpat_id = '".$_POST["cuenta"]."' AND
						scd.comp_fec BETWEEN TO_DATE('".$fecha_inicio."', 'DD/MM/YYYY') AND TO_DATE('".$fecha_fi."', 'DD/MM/YYYY')
					GROUP BY
						scd.comp_fec,
						scd.comp_id,
						scd.comp_comen,
						src.cpat_id,
						sopg
					) AS s
					LEFT OUTER JOIN sai_comp_diario scd ON (s.comp_id = scd.comp_id)
					LEFT OUTER JOIN
						(
							SELECT 
								src.comp_id,
								sf1125.fuente_financiamiento
							FROM 
								sai_reng_comp src
								INNER JOIN sai_forma_1125 sf1125 ON (src.pr_ac_tipo = sf1125.form_tipo AND src.pr_ac = sf1125.form_id_p_ac AND src.a_esp = sf1125.form_id_aesp)
							GROUP BY 
								src.comp_id,
								sf1125.fuente_financiamiento
						) AS src ON (s.comp_id = src.comp_id)
				WHERE
					s.sopg IS NULL OR s.sopg = ''
				GROUP BY
					s.fecha,
					s.comp_id,
					s.comentario,
					s.monto,
					s.cpat_id,
					s.sopg,
					src.fuente_financiamiento,
					scd.nro_referencia
				) AS s
				
		       left join sai_sol_pago ssp ON ( s.sopg = ssp.sopg_id )
		       left join sai_proveedor_nuevo spn ON ( ssp.sopg_bene_ci_rif = spn.prov_id_rif )
		       left join sai_empleado empl ON ( ssp.sopg_bene_ci_rif = empl.empl_cedula )
		       left join sai_viat_benef svb ON ( ssp.sopg_bene_ci_rif = svb.benvi_cedula )
			   left join sai_ctabanco cta ON ( cta.cpat_id = s.cpat_id )
				LEFT JOIN (

					SELECT
						cuentas.sopg_id,
						MAX(cuentas.cuenta_banco) AS cuenta_bancaria
					FROM
						(
							SELECT
								docg_id AS sopg_id,
								ctab_numero as cuenta_banco
							FROM
								sai_cheque c1
								INNER JOIN sai_chequera c2 ON (c1.nro_chequera = c2.nro_chequera)
												
							UNION
										
							SELECT
								docg_id AS sopg_id,
								nro_cuenta_emisor as cuenta_banco
							FROM
								sai_pago_transferencia
						) AS cuentas
					GROUP BY
						cuentas.sopg_id

				) as cuenta_banco ON (cuenta_banco.sopg_id = s.sopg)
				WHERE
					s.monto <> 0
				
				GROUP  BY s.fecha, 
				          s.comp_id, 
				          s.comentario, 
				          s.monto, 
				          s.cpat_id,
						  cta.ctab_numero, 
				          s.sopg, 
				          s.fuente_financiamiento, 
				          s.nro_referencia, 
				          ssp.sopg_observacion,
				          ssp.sopg_bene_ci_rif,
				          spn.prov_id_rif,spn.prov_nombre,
				          empl.empl_cedula,
					  svb.benvi_cedula,
					  empl.empl_nombres,
					  benvi_nombres,
	  				  empl.empl_apellidos,
	  				  svb.benvi_apellidos,
						cuenta_banco.cuenta_bancaria
				ORDER  BY s.fecha 
						";

	
	

	$resultado = pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
	
	if($resultado !== false)
	{
		$datos = array();
		while($row=pg_fetch_array($resultado))
			$datos[] = $row;
	}
	
	// Generar archivo CSV
	if($_REQUEST['tipoSalida'] == 'csv')
	{

		header ('Content-type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="auxiliar_contable_'.date('dmY').'.csv"');
		
		// Para evitar la cache del navegador o proxy
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado
		
		$handle = fopen("php://output", 'w');
			
		fputcsv($handle, array("Fecha", "Asiento", "Sopg", "Fuente Financiamiento", "Nro Referencia", "Cuenta", "Cuenta financiera", "Debe", "Haber", "Beneficiario", "Comentario"), ',', '"');
		
		
		foreach ($datos as $dato)
		{
			if ($dato['monto']>0) {$monto_debe = $dato['monto']; $monto_haber=null;}
			else {$monto_haber = $dato['monto']*-1; $monto_debe=null;}
			
			fputcsv($handle, array(
				$dato['fecha'],
				$dato['comp_id'],
				$dato['sopg'],
				$dato['fuente_financiamiento'],
				$dato['nro_referencia'],
				$dato['cpat_id'],
				$dato['cuenta_bancaria'],
				number_format($monto_debe,2,',','.'),
				number_format($monto_haber,2,',','.'),
				utf8_encode($dato['beneficiario']),
				utf8_encode($dato['comentario'])
			), ',', '"');
		}
		
		fclose($handle);
		
		exit;
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Auxiliar Contable</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../js/funciones.js"> </script>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">g_Calendar.setDateFormat('dd/mm/yyyy');</script>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script>
function validar(op, tipoSalida){
	if(trim(document.getElementById("cuentaActb").value)!=""){
		tokens = document.getElementById("cuentaActb").value.split( ":" );
		cuenta = (tokens[0])?trim(tokens[0]):"";
		document.getElementById("cuenta").value = cuenta;
	}
	if(op==1){
		document.form1.action="auxiliar.php";
	}

	if(tipoSalida == 'estandar' || tipoSalida == 'csv')
		document.getElementById("tipoSalida").value = tipoSalida; 
	
	document.form1.submit();
}

function comparar_fechas(fecha_inicial,fecha_final){//Formato dd/mm/yyyy
	var fecha_inicial=document.getElementById("hid_desde_itin").value;//document.form.hid_desde_itin.value;
	var fecha_final=document.getElementById("hid_hasta_itin").value;//document.form.hid_hasta_itin.value;

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
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) ){
		alert("La fecha inicial no debe ser mayor a la fecha final");
		document.form.hid_desde_itin.value='';
		document.form.hid_hasta_itin.value='';
		return;
	}
}

function habiDesabiFechas(elemento){
	hid_desde_itin = document.getElementById("hid_desde_itin");
	hid_hasta_itin = document.getElementById("hid_hasta_itin");
	if(elemento.checked==true){
		hid_desde_itin.disabled=false;
		hid_hasta_itin.disabled=false;
    }else{
    	hid_desde_itin.disabled=true;
		hid_hasta_itin.disabled=true; 
		hid_desde_itin.value="";
		hid_hasta_itin.value="";
    }
}
//-->
</script>
</head>
<body>
<br/>
<form name="form1" method="post">
	<input type="hidden" id="tipoSalida" name="tipoSalida" value="estandar"/>
	<table width="55%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td colspan="2" class="normalNegroNegrita">AUXILIAR CONTABLE</td>
		</tr>
		<tr>
			<td class="normalNegrita" colspan="2">
				Fecha Inicio:
				<input type="text" size="10" id="hid_desde_itin"
				name="hid_desde_itin" class="dateparse"
				onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= $_POST["hid_desde_itin"];?>"/>
				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_desde_itin');" title="Show popup calendar"><img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Open popup calendar"/></a>
				&nbsp;
				Fecha Fin:
				<input type="text" size="10" id="hid_hasta_itin"
				name="hid_hasta_itin" class="dateparse"
				onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?= $_POST["hid_hasta_itin"];?>"/>
				<a href="javascript:void(0);"
				onclick="g_Calendar.show(event, 'hid_hasta_itin');"
				title="Show popup calendar">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
				</a>
			</td>
		</tr>
		<tr>
			<td class="normalNegrita">Cuenta contable:</td>
			<td>
				<input type="hidden" value="" name="cuenta" id="cuenta"/>
				<input autocomplete="off" size="70" type="text" id="cuentaActb" name="cuentaActb" value="<?= $_POST["cuenta"];?>" class="normal" />
				<?php
					$query = 	"SELECT ".
									"scp.cpat_id, ".
									"scp.cpat_nombre ".
								"FROM ".
									"sai_cue_pat scp where scp.cpat_id not like '%.00' ".
								"ORDER BY scp.cpat_id";
					$resultado = pg_exec($conexion, $query);
					$numeroFilas = pg_num_rows($resultado);
					$arreglo = "";
					while($row=pg_fetch_array($resultado)){
						$arreglo .= "'".$row["cpat_id"]." : ".str_replace("\n"," ",$row["cpat_nombre"])."',";
					}
					$arreglo = substr($arreglo, 0, -1);
				?>
				<script>
					var cuentasAMostrar = new Array(<?= $arreglo?>);
					actb(document.getElementById('cuentaActb'),cuentasAMostrar);
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="button" value="Listar" onclick="validar(1, 'estandar')"/>
				<input type="button" value="CSV" onclick="validar(1, 'csv')"/>
				<input type="hidden" name="login" value="<?= $_SESSION['login']?>"/>
			</td>
		</tr>
	</table>
</form>
<br/>

<?php

if(is_array($datos) && count($datos) > 0) {

?>

<div align="center" class="normalNegroNegrita"><?= "AUXILIAR CONTABLE ".$_POST["hid_desde_itin"]." Y ".$_POST["hid_hasta_itin"]?></div>
<br/>
<table width="90%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray" align="center">
		<td width="7%" class="normalNegroNegrita">Fecha</td>
		<td width="6%" class="normalNegroNegrita">Asiento</td>			
		<td width="5%" class="normalNegroNegrita">Sopg</td>
		<td width="2%" class="normalNegroNegrita">Fuente Financiamiento</td>
		<td width="6%" class="normalNegroNegrita">Nro Referencia</td>		
		<td width="6%" class="normalNegroNegrita">Cuenta</td>
		<td width="6%" class="normalNegroNegrita">Cuenta financiera</td>
		<td width="10%" class="normalNegroNegrita">Debe</td>
		<td width="10%" class="normalNegroNegrita">Haber</td>
		<td width="10%" class="normalNegroNegrita">Beneficiario</td>		
		<td  class="normalNegroNegrita">Comentario</td>		
	</tr>
	
	
<?php
	foreach ($datos AS $row){
?>
	<tr style="border-bottom: #A2A2A2 1px solid;" >
		<td style="border-bottom: #A2A2A2 1px solid;"><div class="normal"><?= $row['fecha']?></div></td>
		
		<?php 
		
		if(substr($row['comp_id'], 0, 5) == 'codi-'){
		?>
		<td style="border-bottom: #A2A2A2 1px solid;" ><div class="normal"><a href="javascript:abrir_ventana('<?=GetConfig("siteURL");?>/acciones/codi/codi.php?accion=generarPDF&codis=<?php echo $row['comp_id']; ?>')" >  <?= $row['comp_id']?>    </a></div></td>
	   <?php 
		
		}else{
		?>
		
		<td style="border-bottom: #A2A2A2 1px solid;" ><div class="normal"> <?= $row['comp_id']?> </div></td>
		
		 <?php 
		
		}

		
		if($row['sopg'] != ''){
		?>
        <td style="border-bottom: #A2A2A2 1px solid;"><div class="normal"> <a target="_blank" href="contabilidadDocumento.php?control=1&cod_doc=<?echo $row['sopg']?>"><?= $row['sopg']?></a></div></td>	   
       
		<?php 
		
		}else{
		?>
		
          <td style="border-bottom: #A2A2A2 1px solid;"><div class="normal"><?= $row['sopg']?></div></td>		
		 <?php 
		
		}
		
		?>
		
		<td style="border-bottom: #A2A2A2 1px solid; text-align: center;"><div class="normal" ><?= $row['fuente_financiamiento']?></div></td>
		<td style="border-bottom: #A2A2A2 1px solid;text-align: center;"><div class="normal" ><?= $row['nro_referencia']?></div></td>				
		<td style="border-bottom: #A2A2A2 1px solid;" width="45" align="center" class="normal"><?= $row['cpat_id']?></td>
		<td style="border-bottom: #A2A2A2 1px solid;" width="45" align="center" class="normal"><?= $row['cuenta_bancaria']?></td>
		<?php 
		if ($row['monto']>0) {$monto_debe = $row['monto']; $monto_haber=null;}
	    else {$monto_haber = $row['monto']*-1; $monto_debe=null;}
		?>
		<td style="border-bottom: #A2A2A2 1px solid;"><div align="center" class="normal"><?= number_format($monto_debe,2,',','.');?></div></td>
		<td style="border-bottom: #A2A2A2 1px solid;"><div align="center" class="normal"><?= number_format($monto_haber,2,',','.');?></div></td>
		<td style="border-bottom: #A2A2A2 1px solid;"><div class="normal"><?= $row['beneficiario']?></div></td>
		<td style="border-bottom: #A2A2A2 1px solid;"><div class="normal"><?= $row['comentario']?></div></td>		
	</tr>
<?php
	}
?>


</table>
<?php }?>
</body>
</html>