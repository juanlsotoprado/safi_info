<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:index.php',false);
	ob_end_flush(); 	
	exit;
}	
ob_end_flush(); 
	  
//Perfil del usuario
$user_perfil_id = $_SESSION['user_perfil_id'];
$perfil = $_SESSION['user_perfil_id'];
$pres_anno = $_SESSION['an_o_presupuesto'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Buscar Modificaciones presupuestarias</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript"> g_Calendar.setDateFormat('dd/mm/yyyy');	</script>
<script language="JavaScript" src="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript" type="text/JavaScript">
var tipo = 0;
var resultado =0;

function buscar() {
	if ((document.form.txt_inicio.value.length<4 || document.form.txt_fin.value.length <4) && document.form.txt_pmod.value == "" && document.form.txt_partida.value == "" && document.form.txt_detalle.value == "" && document.form.tipo_mod.value == "0" && document.form.categoria.value == "0") {
			alert("Debe seleccionar un criterio de búsqueda");
			return;
	}
	else resultado = 1;				
	if (resultado = 1) {
		 document.form.action="buscar.php";
		 document.form.hid_validar.value=2;
		 document.form.submit()
		
	 }					
}



function anular(id_cheque_p, nro_cheque_p) {
	if (confirm("Est\u00e1 seguro que desea ANULAR el codi "+nro_cheque_p)) {
		document.location.href = "anular_cheque.php?id="+id_cheque_p+"&pgch="+nro_cheque_p;	
	}
}
</script>
</head>
<body>
<form name="form" action="" method="post">
<input type="hidden" value="0" name="hid_validar" />
  <table width="70%" align="center" class="tablaalertas">
<tr class="td_gray"> 
  <td colspan="3" class="normalNegroNegrita">BUSCAR MODIFICACIONES PRESUPUESTARIAS</td>
</tr>
<tr class="normalNegrita">
	<td>&nbsp;</td>
	<td>Por fecha emisi&oacute;n:</td>
	<td>
	<?php 
	$fecha_sistema= date("d")."/".date("m")."/".date("Y");
	?>
		<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
		 readonly="readonly" value="<?php echo "01/01/".date("Y");?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
		<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
		<input type="text" size="10" id="txt_fin" name="txt_fin" class="dateparse"
		 readonly="readonly" value="<?php echo $fecha_sistema;?>"/>
		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fin');" title="Show popup calendar">
		<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
		</a>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Documento: </td>
	<td><input name="txt_pmod" type="text" class="normal" id="txt_pmod" value="" size="20" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Partida: </td>
	<td><input name="txt_partida" type="text" class="normal" id="txt_partida" value="" size="30" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Tipo: </td>
	<td class="normal">
	<select name="tipo_mod">
		<option value="0">Todos</option>
		<option value="3">Cr&eacute;dito</option>
		<option value="5">Traspaso</option>
		<option value="2">Disminuci&oacute;n</option>
		</select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Categor&iacute;a program&aacute;tica: </td>
	<td class="normal">
	<select name='categoria'>
	<option value="0" class="normal">Todos</option>
	<?php
	$sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$pres_anno." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$pres_anno." order by centro_gestor, centro_costo";
	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las categorias programaticas");  
	while($rowor=pg_fetch_array($resultado_set)) {?> 
		<option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
  <td class="normalNegrita">Detalle:</td>
  <td><input name="txt_detalle" type="text" class="normal" id="txt_detalle" value="" size="25" maxlength="20"/></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td class="normalNegrita">Estatus: </td>
	<td class="normal">
	<select name="estado">
		<option value="1">Activo</option>
		<option value="15">Anulado</option>
		</select>
	</td>
</tr>

  <tr>
  <td height="44" colspan="3" align="center">
  <input type="button" value="Buscar" onClick="javascript:buscar();">
</td>
</tr>
</table>
</form>
<br/>
<form name="form3" action="" method="post">
<?

if ($_POST['hid_validar']==2){
$tipo=$_POST["tipo"];
$fecha_inicio=$_POST["txt_inicio"];
$fecha_fin=$_POST["txt_fin"];
$condicion=0;
$documento=$_POST["txt_pmod"];
$partida=$_POST["txt_partida"];
$detalle=$_POST["txt_detalle"];
$tipo_mod=$_POST["tipo_mod"];
 $estado=$_POST["estado"];
$categoria=$_POST["categoria"];


if (strlen($fecha_inicio)<4) {
	$fecha_ini=substr($fecha_sistema,6,4)."-".substr($fecha_sistema,3,2)."-".substr($fecha_sistema,0,2);
	$condicion_emision = " AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD') <= '".$fecha_ini."'";
	$condicion=1;
	
}

else  {
$fecha_ini=substr($fecha_inicio,6,4)."-".substr($fecha_inicio,3,2)."-".substr($fecha_inicio,0,2);
$fecha_fin=substr($fecha_fin,6,4)."-".substr($fecha_fin,3,2)."-".substr($fecha_fin,0,2);
$condicion_emision = " AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD') <= '".$fecha_fin."'";
$condicion=1;		
}

if (strlen($documento)>4)  {
	$condicion_pmod = " AND f.f030_id = '".$_POST["txt_pmod"]."'";
	$condicion=1;
}
if (strlen($partida)>4)  {
	$condicion_partida = " AND fd.part_id like '".$_POST["txt_partida"]."%'";
	$condicion=1;
	
}
if ($tipo_mod!="0")  {
	$condicion_tipo = " AND f.f030_tipo LIKE '%".strtolower($_POST["tipo_mod"])."%'";
	$condicion=1;
}


if ($categoria!="0")  {
	list($proyac, $acc_esp) = split( ':::', $_POST['categoria']);	
	$condicion_categoria = " AND fd.f0dt_id_p_ac='".$proyac."' and fd.f0dt_id_acesp='".$acc_esp."'";		
	$condicion=1;
}
if (strlen($detalle)>2)  {
	$condicion_detalle = " AND lower(f.f030_motivo) LIKE '%".strtolower($_POST["txt_detalle"])."%'";
	$condicion=1;
}
/*else {
	$fecha_ini=substr($fecha_sistema,6,4)."-".substr($fecha_sistema,3,2)."-".substr($fecha_sistema,0,2);
	$condicion_emision = " AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini."' AND to_date(to_char(f.f030_fecha, 'YYYY-MM-DD'), 'YYYY MM DD') <= '".$fecha_ini."'";
	$condicion=1;			
}*/
if (strlen($condicion_emision)>2 ||strlen($condicion_pmod)>2 || strlen($condicion_partida)>2 ||strlen($condicion_tipo)>2 || strlen($condicion_categoria)>5 || strlen($condicion_detalle)>5) {
	$sql =	"SELECT ".
				"f.f030_id AS codigo, ".
				"fd.f0dt_id_p_ac AS proy_acc, ".
				"fd.f0dt_id_acesp AS acc_esp, ".
				"CASE fd.f0dt_tipo ".
					"WHEN '1' THEN 'RECEPTORA' ".
					"WHEN '0' THEN 'CEDENTE' ".
				"ELSE '' END AS tipo, ".
				"fd.f0dt_monto AS monto, ".
				"f.f030_motivo AS motivo, ".
				"to_char(f.f030_fecha, 'DD/MM/YYYY') AS fecha, ".
				"upper(em.empl_nombres)||' '||upper(em.empl_apellidos) AS usuario, ".
				"fd.part_id AS partida, ".
				"spae.centro_gestor, ".
				"spae.centro_costo ".
			"FROM sai_doc_genera d, sai_empleado em, sai_forma_0305 f, sai_fo0305_det fd, sai_proy_a_esp spae ".
			"WHERE ".
				"f.esta_id='".$estado."' AND " .
				"f.f030_id = fd.f030_id AND ".
				"f.f030_id = d.docg_id AND ".
				"d.usua_login = em.empl_cedula ".$condicion_emision.$condicion_pmod.$condicion_partida.$condicion_tipo.$condicion_categoria.$condicion_detalle." AND ".
				"fd.f0dt_proy_ac = 1::BIT AND ".
				"fd.pres_anno = spae.pres_anno AND ".
				"fd.f0dt_id_p_ac = spae.proy_id AND ".
				"fd.f0dt_id_acesp = spae.paes_id ".
			"UNION ".
			"SELECT ".
				"f.f030_id AS codigo, ".
				"fd.f0dt_id_p_ac AS proy_acc, ".
				"fd.f0dt_id_acesp AS acc_esp, ".
				"CASE fd.f0dt_tipo ".
					"WHEN '1' THEN 'RECEPTORA' ".
					"WHEN '0' THEN 'CEDENTE' ".
				"ELSE '' END AS tipo, ".
				"fd.f0dt_monto AS monto, ".
				"f.f030_motivo AS motivo, ".
				"to_char(f.f030_fecha, 'DD/MM/YYYY') AS fecha, ".
				"upper(em.empl_nombres)||' '||upper(em.empl_apellidos) AS usuario, ".
				"fd.part_id AS partida, ".
				"sae.centro_gestor, ".
				"sae.centro_costo ".
			"FROM sai_doc_genera d, sai_empleado em, sai_forma_0305 f, sai_fo0305_det fd, sai_acce_esp sae ".
			"WHERE ".
				"f.esta_id='".$estado."' AND " .
				"f.f030_id = fd.f030_id AND ".
				"f.f030_id = d.docg_id AND ".
				"d.usua_login = em.empl_cedula ".$condicion_emision.$condicion_pmod.$condicion_partida.$condicion_tipo.$condicion_categoria.$condicion_detalle." AND ".
				"fd.f0dt_proy_ac = 0::BIT AND ".
				"fd.pres_anno = sae.pres_anno AND ".
				"fd.f0dt_id_p_ac = sae.acce_id AND ".
				"fd.f0dt_id_acesp = sae.aces_id ".
			"ORDER BY codigo";
}

if ($condicion==1) {
//	echo $sql;
	$resultado=pg_query($conexion,$sql) or die("Error al mostrar la consulta de modificaciones presupuestarias");
	$total_busq=pg_num_rows($resultado);
	if ($total_busq>0) {
?>
 <div class="normalNegroNegrita" align="center">Modificaciones presupuestarias</div>
	  <table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr class="td_gray">
					<td class="normalNegroNegrita" align="center">Documento  </td>
					  <td class="normalNegroNegrita" align="center">Fecha  </td>					
					  <td class="normalNegroNegrita" align="center">Categor&iacute;a Program&aacute;tica </td>
					  <td class="normalNegroNegrita" align="center">Partida </td>
					  <td class="normalNegroNegrita" align="center">Tipo  </td>
					  <td class="normalNegroNegrita" align="center">Monto  </td>		
					    <td class="normalNegroNegrita" align="center">Motivo  </td>				  
					    <td class="normalNegroNegrita" align="center">Respaldo  </td>
					<td class="normalNegroNegrita" align="center">Usuario  </td>
                    </tr>
                     <?php while ($row=pg_fetch_array($resultado)) { ?>
                     
                      <tr class="normal">
                        <td class="link"><a href="javascript:abrir_ventana('pmod_detalle.php?codigo=<?php echo $row["codigo"]; ?>&amp;esta_id=10')" class="copyright"><?php echo $row["codigo"];?></a></td>
                           <td><?php echo $row["fecha"];?></td>
                         <td align="center"><?php echo $row["centro_gestor"]."/".$row["centro_costo"] ;?></td>
                          <td><?php echo $row["partida"];?></td>
                          <td><?php echo $row["tipo"];?></td>
                            <td align="right"><?php echo number_format($row["monto"],2,',','.');?></td>
                             <td><?php echo $row["motivo"];?></td>
                             <?php
                             	$respaldo = "";
                 				$sql="select r.resp_nombre || ' ('||r.resp_tipo||')' as respaldo from sai_respaldo r where r.docg_id='".$row["codigo"]."'";
                    			$resultado_r=pg_query($conexion,$sql);
                    			while($row_r=pg_fetch_array($resultado_r))  {
                    				$respaldo .= " ".$row_r['respaldo'];
                    			}
                             
                             ?>
                           <td><?php echo $respaldo;?></td>
                             <td><?php echo $row["usuario"];?></td>
                        </tr>
                   <?php }  ?>
	</table>
<?}
	else {?>
	<table width="60%" border="0" align="center">
      <tr>
         <td class="normalNegroNegrita">
		 <div align="center">No hay resultados coincidentes</div>
		 </td>
      </tr>
	</table>
	<?
	}
}
	}	
?></form>
</body>
</html>
<?php pg_close($conexion);?>