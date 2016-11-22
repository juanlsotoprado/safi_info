<?php
ob_start();
session_start();

require_once(dirname(__FILE__) . '/../../init.php');
require_once(SAFI_INCLUDE_PATH . '/conexion.php');
require_once(SAFI_INCLUDE_PATH . '/perfiles/constantesPerfiles.php');
require_once(SAFI_VISTA_CLASSES_PATH . '/fechas.php');

if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}	ob_end_flush();
 
//Login del usuario
$usuario = $_SESSION['login'];
//Perfil del usuario
$user_perfil_id = $_SESSION['user_perfil_id'];

$fecha_in = trim($_POST['txt_inicio']);
$fecha_fi = trim($_POST['hid_hasta_itin']);

$serialBienNacional = trim($_POST['serialBienNacional']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>.:SAFI:Buscar Actas</title>
	
		<link type="text/css" href="../../css/plantilla.css" rel="stylesheet" />
		<link type="text/css" href="../../css/safi0.2.css" rel="stylesheet" />
		<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
		
		<script type="text/javascript" src="../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript" src="../../js/lib/jquery/plugins/ui.min.js"></script>
		<script type="text/javascript" src="../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript">	g_Calendar.setDateFormat('dd/mm/yyyy');</script>
		<script type="text/javascript"
		src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>"
		charset="utf-8"></script>

		<script type="text/javascript"
			src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>"
			charset="utf-8"></script>
		<link
			href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>"
			rel="stylesheet" type="text/css" charset="utf-8" />
		<script type="text/javascript">
				g_Calendar.setDateFormat('dd/mm/yyyy');
		
		</script>
		<script type="text/javascript">
$().ready(function(){

	$("a").click(function(){

		$("#txt_cod").val('');
		$("#serialBienNacional").val('');

	});
	$("#txt_cod").keyup(function(){

		$("#txt_inicio").val('');
		$("#hid_hasta_itin").val('');
		$("#serialBienNacional").val(''); 

	});

	$("#serialBienNacional").keyup(function(){

		$("#txt_inicio").val('');
		$("#hid_hasta_itin").val('');
		$("#txt_cod").val(''); 

	});
	$(".cp_img").click(function(){
		$("#txt_cod").val('');

	});


});

			/*function deshabilitar_combo(valor)
			{
				if (valor=='1') 
				{ 
					document.form.txt_inicio.disabled=false;
					document.form.hid_hasta_itin.disabled=false;
					document.form.txt_cod.value="";
					document.form.txt_cod.disabled=true;
					document.form.tipo_acta.disabled=false;
					document.form.serialBienNacional.disabled=true;
					document.form.serialBienNacional.value="";
				}
				
				if (valor=='3') 
				{ 
					document.form.txt_inicio.disabled=true;
					document.form.hid_hasta_itin.disabled=true;
					document.form.txt_inicio.value="";
					document.form.hid_hasta_itin.value="";
					document.form.txt_cod.disabled=false;
					document.form.tipo_acta.disabled=true;
					document.form.serialBienNacional.disabled=true;
					document.form.serialBienNacional.value="";
				}

				if (valor=='4') 
				{ 
					document.form.txt_inicio.disabled=true;
					document.form.hid_hasta_itin.disabled=true;
					document.form.txt_inicio.value="";
					document.form.hid_hasta_itin.value="";
					document.form.txt_cod.value="";
					document.form.txt_cod.disabled=true;
					document.form.tipo_acta.disabled=true;
					document.form.serialBienNacional.disabled=false;
				}

			}*/
			
			function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
			{ 	
				var fecha_inicial=document.form.txt_inicio.value;
				var fecha_final=document.form.hid_hasta_itin.value;
					
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
					
				if (
					(anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
					((anio1 == anio2) && (mes1==mes2) && (dia1>dia2))
				)
				{
					alert("La fecha inicial no debe ser mayor a la fecha final"); 
					document.form.hid_hasta_itin.value='';
					return;
				}
			}

			function ejecutar_varios()
			{ 
				if (
					(document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') 
					&& (document.form.txt_cod.value=='') && (document.form.tipo_acta.value=='0')
					&& document.form.serialBienNacional.value==''
				)
				{
					document.form.hid_validar.value=1;
				} else {
					document.form.hid_validar.value=2;
				}
				document.form.submit();
			}
		</script>
	</head>
	
	<body>
		<form name="form" action="buscar_actas_reasignacion.php" method="post">
			<input type="hidden" value="0" name="hid_validar" />
			<br />
			<table width="700" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray">
					<td height="21" colspan="4" class="normalNegroNegrita" align="left">Buscar</td>
				</tr>
				<tr>
					<td height="10" colspan="3"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td>
						<!-- Agregar los accesos rapidos de las fechas (Hoy, ayer, semana, semana pasada, etc.) -->
						<?php VistaFechas::ConstruirAccesosRapidosFechas("txt_inicio", "hid_hasta_itin", "dd/mm/yy") ?>
					</td>
				</tr>
				<tr>
					<td width="20" align="center">
						<!-- <input name="opt_fecha" type="radio" value="1" onclick="javascript:deshabilitar_combo(1)" class="normal" />-->
					</td>
					<td width="175" height="29" class="normalNegrita" align="left">Elaborada entre:</td>
					<td width="304" class="normalNegrita" colspan="2">
						<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
							onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							value="<?php echo ($fecha_in == null || $fecha_in == "") ? "" : $fecha_in?>"
						/>
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar"><img
							src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" /></a> 
						<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
							onfocus="javascript: comparar_fechas(this);"
							readonly="readonly" value="<?php echo ($fecha_fi == null || $fecha_fi == "") ? "" : $fecha_fi?>"/> 
						<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar"><img
							src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar" /></a>
					</td>
				</tr>
				<tr>
					<td height="30" align="center" class="normal">
						<!-- <input name="opt_fecha" type="radio" value="3" class="normal" onclick="javascript:deshabilitar_combo(3)" />-->
					</td>
					<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
					<td>
						<span class="normalNegrita">
							<input name="txt_cod" type="text" class="peq" id="txt_cod" value="" size="10" />
						</span>
					</td>
				</tr>
				<tr>
					<td height="30" align="center" class="normal">
						<!-- <input name="opt_fecha" type="radio" value="4" class="normal"
							onclick="javascript:deshabilitar_combo(4)"
						/>-->
					</td>
					<td class="normalNegrita" align="left">Serial de bien nacional:</td>
					<td class="normalNegrita">
						<input name="serialBienNacional" type="text" class="peq" id="serialBienNacional" value=""
							size="10"
						/>
					</td>
				</tr>
				<tr>
					<td height="30" align="center" class="normal"></td>
					<td class="normalNegrita" align="left">Tipo de Acta:</td>
					<td>
						<span class="normalNegrita">
							<select name="tipo_acta" class="normalNegro">
								<option value="0" selected>[Seleccione]</option>
								<option value="1">Comodato</option>
								<option value="2">Re-asignaci&oacute;n</option>
								<option value="3">Retorno Inventario</option>
							</select>
						</span>
					</td>
				</tr>
				<tr>
					<td height="10" colspan="3"></td>
				</tr>
				<tr>
					<td colspan="3">
						<div align="center">
							<input type="button" class="normalNegro" value="Buscar" onclick="javascript:ejecutar_varios()" />
						</div>
					</td>
				</tr>
			</table>
		</form>
		<br />
<?php 
	if ($_POST['hid_validar']==1)
	{
		echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe especificar al menos un criterio de b\u00FAsqueda');"."</SCRIPT>";
	}elseif ($_POST['hid_validar']==2){

/*
	if (
		( (($_POST['txt_inicio'])=='') and (($_POST['hid_hasta_itin'])!='') ) or ( (($_POST['txt_inicio'])!='')
		and (($_POST['hid_hasta_itin'])=='') ) 
	){
		echo "<SCRIPT LANGUAGE='JavaScript'>";
		echo "alert ('Debe especificar el rango de fechas a buscar');"."</SCRIPT>";
	}
*/    
		$where = "";
		$from = "";
		
		if($fecha_in != "" && $fecha_fi != ""){
			$where = "
				reasignar.fecha_acta BETWEEN
					TO_TIMESTAMP('".$fecha_in."', 'DD/MM/YYYY') AND TO_TIMESTAMP('".$fecha_fi." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
			";
		} else if($fecha_in != ""){
			$where = "
				reasignar.fecha_acta > TO_TIMESTAMP('".$fecha_in."', 'DD/MM/YYYY')
			";
		} else if($fecha_fi != ""){
			$where = "
				reasignar.fecha_acta < TO_TIMESTAMP('".$fecha_fi." 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
			";
		}
		
		if ($_POST['tipo_acta']>0) {
			if($where != "") $where .= " AND ";
			$where .= "reasignar.tipo = '".$_POST['tipo_acta']."' ";
		}

		if ($_POST['txt_cod']<>"") {
			if($where != "") $where .= " AND ";
		   	$where .= "reasignar.acta_id = '".$_POST['txt_cod']."' ";
		}
		
		if ($serialBienNacional != "")
		{
			if($where != "") $where .= " AND ";
		   	$where .= "item_particular.etiqueta = '".$serialBienNacional."' ";
		   	
		   	$from .= "INNER JOIN sai_biin_items item_particular ON (item_particular.clave_bien = reasignar_item.clave_bien)";
		}
		
		$sql_or = "
			SELECT DISTINCT
				(SUBSTRING(reasignar.acta_id FROM 4 FOR LENGTH(reasignar.acta_id)-5) :: INT) AS correlativo,
				to_char(reasignar.fecha_acta,'DD/MM/YYYY') AS fecha_acta,
				reasignar.acta_id AS acta,
				reasignar.tipo,
				reasignar.esta_id,
				CASE reasignar.tipo
					WHEN 1 THEN
						'Comodato'
					WHEN 2 THEN
						'Re-asignaci&oacute;n'
					ELSE
						'Retorno al inventario'
				END AS tipo
			FROM
				sai_bien_reasignar reasignar
				INNER JOIN sai_bien_reasignar_item reasignar_item ON (reasignar_item.acta_id = reasignar.acta_id)
				".$from."
			".( $where != "" ? "WHERE " . $where : "" )."
			ORDER BY
				correlativo
				
		";
		//COALESCE(MAX((SUBSTRING(acta_id FROM 9 FOR LENGTH(acta_id)-10)) :: INT),0) + 1 AS max_id
		echo $sql_or;
		$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar");
		if(($rowor=pg_fetch_array($resultado_set_most_or))==null)
		{
			echo "<div align='center'><span color='#003399' class='normalNegrita'>No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</span></div>";
		}
		else
		{

		/*	$ano1=substr($fecha_ini,0,4);
			$mes1=substr($fecha_ini,5,2);
			$dia1=substr($fecha_ini,8,2);

			$ano2=substr($fecha_fin,0,4);
			$mes2=substr($fecha_fin,5,2);
			$dia2=substr($fecha_fin,8,2);
			?>
		<div class="normalNegroNegrita" align="center">
			Acta(s) elaboradas en el rango de fecha:
			<?php echo $dia1."-".$mes1."-".$ano1;?>
			y
			<?php echo $dia2."-".$mes2."-".$ano2;?>
		</div>*/?>
		<table width="700" align="center" background="../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
			    <td width="10" class="normalNegroNegrita" align="center">#</td>
				<td width="137" class="normalNegroNegrita" align="center">C&oacute;digo</td>
				<td width="120" class="normalNegroNegrita" align="center">Fecha</td>
				<td width="136" class="normalNegroNegrita" align="center">Estado del Acta</td>
				<td width="190" class="normalNegroNegrita" align="center">Tipo</td>
				<td class="normalNegroNegrita" align="center">Opciones</td>
			</tr>

			<?php
			$i=0;

			$resultado_set_most_pa=pg_query($conexion,$sql_or);
			while($rowpa=pg_fetch_array($resultado_set_most_pa))
			{
			$i++;
			$query_estado="Select esta_nombre from sai_estado where esta_id='".$rowpa['esta_id']."'";
			$resultado_estado=pg_query($conexion,$query_estado);
			if ($row_estado=pg_fetch_array($resultado_estado)){
				$nombre_estado=$row_estado['esta_nombre'];
			}
			?>
			<tr class="normal">
			<td align="center"><span class="peq"><?php echo $i;?></span></td>
				<td height="28" align="center"><span class="link"><?php echo $rowpa['acta'];?></span></td>
				<td align="center"><span class="peq"><?php echo $rowpa['fecha_acta'];?></span></td>
				<td align="left"><span class="peq"><?php echo $nombre_estado;?></span></td>
				<td align="left" class="normal"><?php echo $rowpa['tipo'];?></td>
				<td align="left" class="normal">
					<div align="center">
						<span class="peqNegrita">
							<a href="javascript:abrir_ventana('reasignar_activos_pdf.php?codigo=<?php echo trim($rowpa['acta']); ?>')"
								class="copyright"
							><?php echo "Ver detalle"; ?></a>
							<?php
								if(
									($user_perfil_id == PERFIL_ANALISTA_BIENES || $user_perfil_id == PERFIL_COORDINADOR_BIENES)
									&& $rowpa['esta_id'] <> 15
								){
							?>
							<a
								href="modificar_salida_activos.php?codigo=<?php echo trim($rowpa['acta']); ?>&tipo=6&accion=Anular"
								class="copyright"
							><?php echo "Anular"; ?></a>
							<?php
									
								}
							?>
						</span>
						<br/>
					</div>
				</td>
			</tr>
			<?php 
			}
			
			}
	}
			?>
		</table> 
</body>
</html>
	<?php pg_close($conexion);?>

