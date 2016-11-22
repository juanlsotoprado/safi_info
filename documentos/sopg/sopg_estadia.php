<?php 
    ob_start();
	session_start();
	 require_once("../../includes/conexion.php");
	 require_once("../../includes/perfiles/constantesPerfiles.php");
//	 include("../../includes/FCKeditor/fckeditor.php") ;
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	ob_end_flush(); 
                  
	$user_perfil_id = $_SESSION['user_perfil_id'];
    //Verifica si el usuario tiene permiso para el objeto (accion) actual
	$sql = " SELECT * FROM sai_permiso_reporte('liber_pcta','".$user_perfil_id."') as resultado ";
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$tiene_permiso = $row["resultado"];
	}

	
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
	<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
	<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" /><script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
<script>
function detalle(codigo)
{
    url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar() { 
	document.form.hid_validar.value=2;
	document.form.submit();
}

function pctaarar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
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
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
	 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	{
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}

</script>
</head>
<body>
<form name="form" action="sopg_estadia.php" method="post">
  <div align="center">
  <input type="hidden" value="0" name="hid_validar" />
  
  
  <br />
  </div>
  <table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="21" colspan="2" class="normalNegroNegrita" align="left">Estad&iacute;a de solicitudes de pago</td>
</tr>
<tr>
  <td height="10" colspan="2"></td>
</tr>
<tr>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
	<td >
<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["txt_inicio"];?>"/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["hid_hasta_itin"];?>"/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>	</td>
</tr>
<tr>
	<td class="normalNegrita" align="left">
            Dependencia:
          </td>
	<td><span class="normalNegrita">
	<?php 
if ((substr($user_perfil_id,2,3)=="450" || substr($user_perfil_id,2,3)=="452" || substr($user_perfil_id,2,3)=="400") || ($user_perfil_id=="41200"))
    $sql = "select substr(depe_id, 0, 3) as depe_id, depe_nombre from sai_dependenci where depe_nivel=4 order by depe_nombre";
else     
    $sql = "select substr(depe_id, 0, 3) as depe_id, depe_nombre from sai_dependenci where depe_nivel=4 and depe_id IN (substr('".$user_perfil_id."',3,3), ".DEPENDENCIA_OFICINA_TALENTO_HUMANO.") order by depe_nombre";

	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las dependencias");	
	?>
<select name="dependencia" id="dependencia" class="normalNegro">
<?php if ((substr($user_perfil_id,2,3)=="450" || substr($user_perfil_id,2,3)=="452" || substr($user_perfil_id,2,3)=="400") || ($user_perfil_id=="41200")) {?>
<option value=-1>Todos</option>
<?php }?>
<?php
	while($rowor=pg_fetch_array($resultado_set))
	{
		?> 
	<option value="<?php echo $rowor['depe_id']?>">
    <?php echo $rowor['depe_nombre']?></option>
		
	<?php		
	}
?>

</select>

	</span></td>
</tr>
<tr>
	<td class="normalNegrita" align="left">
           Beneficiario:
          </td>
	<td><span class="normalNegrita">
	  <input name="txt_nombre" type="text" class="peq" id="txt_nombre" value="" size="25" />
	  <input type="hidden" value="1" name="validar" id="validar"></input>
	</span></td>
	
</tr>

</table><br>

<div align="center">
<input type="button" value="Buscar" onclick="javascript:ejecutar();">
  
</div>
</form>
<br>
<form name="form3" action="" method="post">
<?php 
if ($_POST['validar']==1) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);

$wheretipo1="";
$wheretipo2="";
$wheretipo3="";
$wheretipo4="";
$wheretipo5="";
$wheretipo6="";
$wherenombre="";
if (strlen($fecha_ini2)>5) {
$wherefecha = " WHERE to_date(to_char(s.sopg_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(s.sopg_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
}

if (strlen(trim($_POST['txt_nombre']))>0) {
	//if (strlen($fecha_ini2)>5) 	$wherenombre = " and ";
$wherenombre = "WHERE (upper(beneficiario_cheque) like '%".strtoupper($_POST['txt_nombre'])."%') or beneficiario like '%".strtoupper($_POST['txt_nombre'])."%'";
$wherenombre1e = $wherenombre1. " and (upper(emp.empl_nombres) like '%".strtoupper($_POST['txt_nombre'])."%' or upper(emp.empl_apellidos) like '%".strtoupper($_POST['txt_nombre'])."%' or emp.empl_cedula like '%".strtoupper($_POST['txt_nombre'])."%')";
$wherenombre1p = $wherenombre1. " and (upper(prov.prov_nombre) like '%".strtoupper($_POST['txt_nombre'])."%' or prov.prov_id_rif like '%".strtoupper($_POST['txt_nombre'])."%')";
$wherenombre1vb = $wherenombre1. " and (upper(vb.benvi_nombres) like '%".strtoupper($_POST['txt_nombre'])."%' or upper(vb.benvi_apellidos) like '%".strtoupper($_POST['txt_nombre'])."%' or vb.benvi_cedula like '%".strtoupper($_POST['txt_nombre'])."%')";

}

if (strcmp($_POST['dependencia'],-1)<>0) {
	if (strlen($fecha_ini2)>5) $wheredependencia= " and "; //(strlen(trim($_POST['txt_nombre']))>0 or
	else $wheredependencia= " WHERE ";
$wheredependencia= $wheredependencia. " s.depe_solicitante like '".$_POST['dependencia']."%'";
}

$sql_or="
select perfil_sopg,sopg_id,sopg_fecha,monto,beneficiario,dependencia,beneficiario_cheque
FROM(
select CASE WHEN length(ds.perf_id_act)<2 THEN 'Finalizado' ELSE (select carg_nombre from sai_cargo where carg_fundacion=substr(ds.perf_id_act,0,3)) END as perfil_sopg, 
s.sopg_id as sopg_id, to_char(s.sopg_fecha, 'DD/MM/YYYY') as sopg_fecha, coalesce(s.sopg_monto) as monto
, s.sopg_bene_ci_rif as beneficiario, initcap(dep.depe_nombre) as dependencia, coalesce(initcap(emp.empl_nombres),'')||' '|| coalesce(initcap(emp.empl_apellidos),'')||' '||coalesce(initcap(prov_nombre),'')||' '||coalesce(initcap(vb.benvi_nombres),'')||' '|| coalesce(initcap(vb.benvi_apellidos),'') as beneficiario_cheque 
from sai_sol_pago s 
left outer join sai_doc_genera ds on (ds.docg_id=s.sopg_id) 
left outer join sai_dependenci dep on (dep.depe_id=s.depe_solicitante)
left outer join sai_empleado emp on (trim(s.sopg_bene_ci_rif)=trim(emp.empl_cedula)".$wherenombre1e.")
left outer join sai_proveedor_nuevo prov on (trim(s.sopg_bene_ci_rif)=trim(prov.prov_id_rif)".$wherenombre1p.")
left outer join sai_viat_benef vb on (trim(s.sopg_bene_ci_rif)=trim(vb.benvi_cedula)".$wherenombre1vb.")
" 
.$wherefecha.$wheredependencia ."
) AS A ".$wherenombre." order by 3"; 
//echo $sql_or;
$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la orden de pago");  
	  ?>
	  <table width="100%" border="0" align="center">
        <tr>
          <td width="495" height="27" class="normalNegrita"><div align="center">Estad&iacute;a de solicitud de pagos </div></td>
        </tr>
  </table>
	  <table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
					<tr class="td_gray">
					  <td  class="normalNegroNegrita" align="center">Documento </td>
 					<td  class="normalNegroNegrita align="center">Fecha </td>					  
					  <td  class="normalNegroNegrita" align="center">Beneficiario</td>
 					<td  class="normalNegroNegrita" align="center">Monto Solicitud Bs. (Sin Retenciones) </td>					  
					  <td  class="normalNegroNegrita" align="center">Dependencia</td>
					  <td class="normalNegroNegrita" align="center">Estatus Solicitud Pago </td>
					  <td class="normalNegroNegrita" align="center">Estatus Pago Cheque </td>
					  <td class="normalNegroNegrita" align="center">Estatus Transferencia</td>
					</tr>
			<?
			while ($rowor=pg_fetch_array($resultado_set_most_or))  {
			$query_tran="select * from sai_pago_transferencia where docg_id='".$rowor['sopg_id']."' and esta_id<>15";
			$resultado_tran=pg_query($conexion,$query_tran);
			if ($row_tran=pg_fetch_array($resultado_tran)){
				$status_tran="Finalizado";
				$status_pgch="No Aplica";
			}else{
				  $status_tran="No ha iniciado";
				  $query_pgch="select * from sai_pago_cheque where docg_id='".$rowor['sopg_id']."' and esta_id<>15";
				  $resultado_pgch=pg_query($conexion,$query_pgch);
			      if ($row_pgch=pg_fetch_array($resultado_pgch)){
				     $status_pgch="Finalizado";
				     $status_tran="No Aplica";
			      }else{
			      	 $status_pgch="No ha iniciado";
			      }	  
			}
			?>

						<tr class="normalNegro">
						<td align="left"><span class="peq"><?php echo $rowor['sopg_id'];?></span></td>
						<td align="center"><span class="peq"><?php echo $rowor['sopg_fecha'];?></span></td>						
						<td align="left"><span class="peq"><?php echo $rowor['beneficiario_cheque'];?></span></td>						
						<td align="left"><span class="peq"><?php echo number_format($rowor['monto'],2,',','.');?></span></td>
						<td align="left"><span class="peq"><?php echo $rowor['dependencia'];?></span></td>												
						<td align="left"><span class="peq"><?php echo $rowor['perfil_sopg'];?></span></td>
						<td align="left"><span class="peq"><?php echo $status_pgch;?></span></td>
						<td align="left"><span class="peq"><?php echo $status_tran;?></span></td>

						</tr>
			<?php	 
				}//fin del while que obtiene los datos de la consulta
			}
			?> 
 </table> 
</form>
</body>
</html>
<?php pg_close($conexion);?>