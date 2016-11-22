<?php
ob_start();
session_start();
require_once("../../includes/conexion.php");

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../bienvenida.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
require_once("../../includes/constantes.php");

$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
$depe_id = $_SESSION['user_depe_id'];

$fechaInicio=trim($_POST['fechaInicio']);
$fechaFin=trim($_POST['fechaFin']);
$codigo=trim($_POST['codigo']);
$opcion=trim($_POST['opcion']);

$pagina = "1";
if (isset($_POST['pagina']) && $_POST['pagina'] != "") {
	$pagina = $_POST['pagina'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
	<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
	<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
	<script language="JavaScript" src="../../js/funciones.js"></script>
	<script language="JavaScript" src="../../js/botones.js"></script>
	<script>
	function verDetalle(codigo){
		<?php 
		if($depe_id=="400"){
		?>
    		url="detallePresupuestoOrdenDeCompra.php?idOrdc="+codigo+"&estadia=true";
    	<?php 
    	}else{
		?>
    		url="detalleOrdenDeCompra.php?idOrdc="+codigo+"&estadia=true";
    	<?php 
    	}
    	?>
		newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
		if(window.focus){newwindow.focus()}
	}
	
	function seleccionarOpcion(valor){
		if(valor=='1'){ 
			document.form.fechaInicio.disabled=false;
			document.form.fechaFin.disabled=false;
			document.form.codigo.value="";
			document.form.codigo.disabled=true;
		}else if(valor=='2'){ 
			document.form.fechaInicio.disabled=true;
			document.form.fechaFin.disabled=true; 
			document.form.fechaInicio.value="";
			document.form.fechaFin.value="";
			document.form.codigo.disabled=false;
		}
	}

	function buscar(pagina){
		if(pagina){
			document.getElementById("pagina").value = pagina;
		}
		var opcion1 = document.getElementById("opcion1").checked;
		var opcion2 = document.getElementById("opcion2").checked;
		var fechaInicio = document.getElementById("fechaInicio").value;
		var fechaFin = document.getElementById("fechaFin").value;
		var codigo = document.getElementById("codigo").value;
		
		if(opcion1==true && (fechaInicio=='' || fechaFin=='')){
			alert('Debe seleccionar un rango de fechas');
			return;
		}else if(opcion2==true && codigo==''){
			alert('Debe introducir el c'+oACUTE+'digo de la orden de compra');
			return;
		}else{
	  		document.form.submit();
		}
	}
	</script>
</head>
<body class="normal">
<form name="form" action="estadiaOrdenesDeCompra.php" method="post">
	<input type="hidden" id="pagina" name="pagina" value="<?= $pagina?>"/>
	<table width="600px" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray">
			<td height="21" colspan="4" class="normalNegroNegrita" align="center">
				Estad&iacute;a de ordenes de compra/servicio
			</td>
		</tr>
		<tr>
			<td height="5" colspan="4"></td>
		</tr>
		<tr>
			<td align="center" width="20px">&nbsp;</td>
			<td height="29" width="140px" align="left">
				<input id="opcion1" name="opcion" type="radio" value="1" onclick="javascript:seleccionarOpcion(1)" class="normal" <?php if(!$opcion || $opcion=="" || $opcion=="1"){echo 'checked="checked"';}?>/>
				Elaboradas entre:
			</td>
			<td width="220px">
				Fecha inicio:
				<input 
					type="text" size="10" id="fechaInicio" name="fechaInicio"
					class="dateparse" onfocus="javascript: comparar_fechas(document.getElementById('fechaInicio').value,document.getElementById('fechaFin').value);"
					readonly="readonly" <?php if($fechaInicio && $fechaInicio!=""){echo "value='".$fechaInicio."'";}?>/>
				<a href="javascript:void(0);" onclick="if(document.getElementById('opcion1').checked==true){g_Calendar.show(event, 'fechaInicio');}" title="Fecha inicio">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Fecha inicio"/>
				</a>
			</td>
			<td width="220px">
				Fecha fin:
				<input
					type="text" size="10" id="fechaFin" name="fechaFin"
					class="dateparse" onfocus="javascript: comparar_fechas(document.getElementById('fechaInicio').value,document.getElementById('fechaFin').value);"
					readonly="readonly" <?php if($fechaFin && $fechaFin!=""){echo "value='".$fechaFin."'";}?>/>
				<a href="javascript:void(0);" onclick="if(document.getElementById('opcion1').checked==true){g_Calendar.show(event, 'fechaFin');}" title="Fecha fin">
					<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"	alt="Fecha fin"/>
				</a>
			</td>
		</tr>
		<tr>
			<td height="30" align="center" class="normal">&nbsp;</td>
			<td align="left" colspan="3">
				<input id="opcion2" name="opcion" type="radio" value="2" class="normal" onclick="javascript:seleccionarOpcion(2)" <?php if($opcion=="2"){echo 'checked="checked"';}?>/>
				C&oacute;digo de orden de compra/servicio:
				<input name="codigo" type="text" class="normalNegro" id="codigo" size="12" <?php if($codigo && $codigo!=""){echo "value='".$codigo."'";}else{echo "disabled='disabled'";}?>/>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="center">
				<input class="normalNegro" type="button" value="Buscar" onclick="buscar(1);"/>
			</td>
		</tr>
	</table>
</form>
<?php 
if ($opcion && $opcion!="") {
	$tamanoPagina = 13;
	$tamanoVentana = 20;
	$desplazamiento = ($pagina-1)*$tamanoPagina;
	$condicion = false;
	
	$query = 	"SELECT ".
					"COUNT(soc.ordc_id) ".
				"FROM ".
					"sai_orden_compra soc ".
				"WHERE ";

	if ($opcion=="1" && $fechaInicio!="" && $fechaFin!="") {
		if($condicion == true){
			$query.= "AND ";
		}
		$query.=	"soc.fecha BETWEEN to_date('".$fechaInicio."','DD-MM-YYYY') AND to_date('".$fechaFin."','DD-MM-YYYY')+1 ";
	}else if ($opcion=="2" && $codigo!="") {
		if($condicion == true){
			$query.= "AND ";
		}
		$query.=	"soc.ordc_id LIKE '".trim($codigo)."'";
	}
	$resultadoContador = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultadoContador, 0);
	$contador = $row[0];
	$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;
	
	$query = 	"SELECT ".
					"soc.ordc_id as id, ".
					"srbms.rebms_tipo as tipo, ".
					"se.esta_nombre as estado, ".
					"srbms.pcta_id, ".
					"case sdg.perf_id_act ".
						"WHEN '' THEN 'Finalizado' ".
						"ELSE UPPER(sc.carg_nombre) ".
						"END AS cargo, ".
					"UPPER(sd.depe_nombre) as dependencia, ".
					"UPPER(dd.depe_nombre) as solicita, ".
					"to_char(soc.fecha, 'dd/mm/yyyy') as fecha, ".
					"em.empl_nombres || ' ' || em.empl_apellidos as solicitante ".
				"FROM ".
					"sai_empleado em, ".
					"sai_dependenci dd, ".
					"sai_estado se, ".
					"sai_orden_compra soc ".
					"INNER JOIN sai_req_bi_ma_ser srbms ON (soc.rebms_id = srbms.rebms_id), ".
					"sai_doc_genera sdg ".
					"LEFT OUTER JOIN sai_cargo sc ON (SUBSTR(sdg.perf_id_act, 1,2) = sc.carg_fundacion) ".
					"LEFT OUTER JOIN sai_dependenci sd ON (SUBSTR(sdg.perf_id_act, 3,3) = sd.depe_id) ".
				"WHERE ".
					"soc.ordc_id = sdg.docg_id AND ".
					"sdg.esta_id = se.esta_id AND ".
					"srbms.depe_id = dd.depe_id AND ".
					"soc.usua_login = em.empl_cedula ";

	if ($opcion=="1" && $fechaInicio!="" && $fechaFin!="") {
		$query.=	"AND soc.fecha BETWEEN to_date('".$fechaInicio."','DD-MM-YYYY') AND to_date('".$fechaFin."','DD-MM-YYYY')+1 ";
	}else if ($opcion=="2" && $codigo!="") {
		$query.=	"AND soc.ordc_id LIKE '".trim($codigo)."'";
	}
	$query.=	"ORDER BY soc.fecha DESC ";
	$query.=	"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
	$resultado=pg_query($conexion,$query);
	$numeroFilas = pg_numrows($resultado);
	?>
	<table width="100%" border="0" align="center">
		<tr>
			<td width="495" height="27" class="normal peq_verde_bold">
				<div align="center">Resultado de la b&uacute;squeda de &oacute;rdenes de compra/servicio</div>
			</td>
		</tr>
	</table>
	<table width="100%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
		<tr class="td_gray normalNegroNegrita">
			<td width="100" align="center">C&oacute;digo de orden</td>
			<td width="60" align="center">Tipo</td>
			<td width="80" align="center">Punto de cuenta</td>
			<td width="60" align="center">Estado</td>
			<td width="80" align="center">Ubicaci&oacute;n actual</td>
			<td width="160" align="center">Dependencia actual</td>
			<td width="100" align="center">Elaborada por</td>
			<td width="160" align="center">Dependencia solicitante</td>
			<td width="100" align="center">Fecha de elaboraci&oacute;n</td>
		</tr>
		<?
		if($numeroFilas>0){
			while($row=pg_fetch_array($resultado)){
			?>
			<tr class="resultados">
				<td height="28" align="center">
					<span class="link">
						<a href="javascript:verDetalle('<?= trim($row['id'])?>');"><?= $row['id']?></a>
					</span>
				</td>
				<td align="center"><?= ($row["tipo"]==TIPO_REQUISICION_COMPRA)?"Compra":(($row["tipo"]==TIPO_REQUISICION_SERVICIO)?"Servicio":"")?></td>
				<td align="center"><?= (($row['pcta'] && $row['pcta']!="")?$row['pcta']:"N/A")?></td>
				<td align="center"><?php echo $row['estado'];$estado=$row['estado'];?></td>
				<?if($estado=="Anulado"){?>
				<td align="center">--</td>
				<td align="center">--</td>
				<?}else{?>
				<td align="center"><?= $row['cargo']?></td>
				<td align="center"><?= $row['dependencia']?></td>
				<?}?>
				<td align="center"><?= $row['solicitante'];?></td>
				<td align="center"><?= $row['solicita'];?></td>
				<td align="center"><?= $row['fecha'];?></td>
			</tr>
			<?php
			}
			
			echo "<tr class='td_gray'><td colspan='9' align='center'>";
			$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
			$ri = (($ventanaActual-1)*$tamanoVentana)+1;
			while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
				if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
					echo "<a onclick='buscar(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
				}
				if($ri==$pagina){
					echo $ri." ";
				}else{
					echo "<a onclick='buscar(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
				}
				if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
					echo "<a onclick='buscar(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
				}
				$ri++;   	
			}
			echo "</td></tr>\n";
		}else{
			echo "<tr><td align='center' valign='middle' height='50' colspan='9'>No se encontraron resultados</td></tr>";
		}
	}
	?>
	</table>
</body>
</html>
<?php pg_close($conexion);?>