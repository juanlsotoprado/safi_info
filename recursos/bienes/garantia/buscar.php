<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush();   exit;
	   } ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reporte de Movimientos de Material</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script lenguaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script lenguage="javascript">
function habiDesabiOpciones(){
	 
	tipoReporte = document.form.tp_reporte[0].checked;
	estatus_activo=document.getElementById("estatus");
	if (tipoReporte==true){ 
		estatus_activo.disabled=true;
	}else{ 
		estatus_activo.disabled=false;
	}
}

function detalle(codigo1,codigo2,tp,tp_arti,depe,tp_fec)
{
    url="alma_rep_e3.php?codigo1="+codigo1+"&codigo2="+codigo2+"&tp_mov="+tp+"&tp_arti="+tp_arti+"&depe="+depe+"&tipo_f="+tp_fec
	newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function ejecutar()
{
   
  if ((document.form.txt_inicio.value=='') && (document.form.hid_hasta_itin.value=='') &&  
			(document.form.estatus.value=='0')
			)	{
		  alert("Debe seleccionar un criterio de b\u00fasqueda");
		  return;
	}
	
	document.form.hid_buscar.value=2;
	document.form.submit();
	
}
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
		
	if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || 
	 ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
	{
	  alert("La fecha inicial no debe se mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}
</script>

</head>
<body>
<form name="form" action="buscar.php" method="post">
<input type="hidden" name="hid_buscar" id="hid_buscar" value="0">
  <br />
  <table width="630" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray" > 
<td height="15" colspan="3" valign="midden" class="normalNegroNegrita">Casos de garant&iacute;a</td>
</tr>
<tr>
<td  height="34" class="normalNegrita">Fecha:</td>

<td width="406" class="normalNegrita">
<input name="txt_inicio" id="txt_inicio" type="text" size="10" class="normalNegro" value="" onclick="cal1xx3.select(this,'anchor1xx3','dd/MM/yyyy'); return false;" readonly />
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="-">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
<input NAME="hid_hasta_itin" ID="hid_hasta_itin" TYPE="text" size="10" class="normalNegro" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" onFocus="javascript: comparar_fechas(document.form.txt_inicio,document.form.hid_hasta_itin)" readonly> 
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="-">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
</td>
</tr>
     <tr>
  	<td height="34" class="normalNegrita">Estatus:</td>
	<td width="406" class="normalNegrita">
	 <select name="estatus" id="estatus"  class="normalNegro">
       <option value="0">[Seleccione]</option>
    <?php
	  $sql="SELECT esta_nombre,esta_id FROM sai_estado WHERE esta_id in ('10','15','35','61') order by esta_nombre"; 
	  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
	  $actual=trim($_REQUEST['unidad_med']);
	  while($row=pg_fetch_array($resultado))
	    { 
	  ?>
	  <option value="<?=$row['esta_id'];?>"><?php echo $row['esta_nombre'];?></option> 
     <?php 
	  }
	  ?>
   </select>
	</td></tr>
<tr>
 <!-- <tr>
  	<td height="34" class="normalNegrita">Serial de Bien Nacional:</td>
	<td width="406" class="normalNegrita"><input type="text" name="sbn" size="10"></input></td></tr>

<tr>-->
<td height="52" colspan="3" align="center">
  <input type="button" value="Buscar" onclick="javascript:ejecutar()" class="normalNegro">
    
</td>
</tr>
</table>
</form>
<br>
<form name="form1" action="" method="post">
<?php 


if (($_POST['hid_buscar'])==1)
{
 echo utf8_decode("<SCRIPT LANGUAGE='JavaScript'>"."alert ('Especifique un rango de fechas...');"."</SCRIPT>");
}
else 
    if (($_POST['hid_buscar'])==2)
    {
    $seleccion='';
    $group='';
    $from1="";
    $wheretipo1="";
	$wheretipo2="";
	$condicion="";

	$fecha_in=trim($_POST['txt_inicio']); 
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
	
    if (strlen($fecha_ini)>2) {
 	   $wheretipo1 = " and  fecha_registro >= '".$fecha_ini."' and fecha_registro <= '".$fecha_fin."' ";
    }
	
     if ($_POST['estatus']>0) {
	  $wheretipo2 = " and t1.esta_id='".$_POST['estatus']."' ";
	  $edo="SELECT esta_nombre FROM sai_estado WHERE esta_id='".$_POST['estatus']."'";
	  $result_edo=pg_query($conexion,$edo);
	  if($rowe=pg_fetch_array($result_edo))
	  $condicion=" Estatus: ".$rowe['esta_nombre'];
     }
	  
     $sql_tabla1="SELECT t1.acta_id,esta_nombre,to_char(fecha_notificacion,'DD/MM/YYYY') as fecha_n, t1.solicitante,
     bmarc_nombre,modelo,serial,falla,
     garantia,to_char(fecha_entrada,'DD/MM/YYYY') as fecha_e,persona_contacto,tlf_contacto,to_char(fecha_reporte,'DD/MM/YYYY') as fecha_r,nro_caso,datos_tecnico,
     to_char(fecha_visita,'DD/MM/YYYY') as fecha_v,to_char(fecha_cierre,'DD/MM/YYYY') as fecha_c,infocentro,
     observaciones,datos_revision,t7.nombre as nombre_activo,observaciones_cierre
     FROM sai_bien_garantia t1 
     LEFT OUTER JOIN sai_biin_items t2 ON (t1.clave_bien=t2.clave_bien)
     LEFT OUTER JOIN sai_bien_marca t3 ON (bmarc_id=marca_id)
     LEFT OUTER JOIN sai_estado t4 ON (t1.esta_id=t4.esta_id)
     LEFT OUTER JOIN sai_bien_asbi_item t5 ON (t5.clave_bien=t2.clave_bien)
     LEFT OUTER JOIN sai_bien_asbi t6 ON (t6.asbi_id=t5.asbi_id and t6.esta_id<>15)
     LEFT OUTER JOIN sai_item t7 ON (t7.id=t2.bien_id)
     WHERE  t1.acta_id is not null ".$wheretipo1.$wheretipo2." ";

    //echo $sql_tabla1."<br><br>";
     $resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta");	
	  if (($rowt1=pg_fetch_array($resultado_set_t1)) == null)
	   {?><center>
  <span color="#003399" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</span>
</center><?php 
	   }
	   else {
	   ?>
	    <table width="678" background="../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
		
		  <div align="center" class="normalNegroNegrita">Casos reportados 
		  <?php
		  if (strlen($fecha_ini)>2){
 	
		  $dia=substr($fecha_ini,8,2);
		  $mes=substr($fecha_ini,5,2);
		  $anno=substr($fecha_ini,0,4);
		  $fec_ini=$dia.'/'.$mes.'/'.$anno;
		  
		  $dia1=substr($fecha_fin,8,2);
		  $mes1=substr($fecha_fin,5,2);
		  $anno1=substr($fecha_fin,0,4);
		  $fec_fin1=$dia1.'/'.$mes1.'/'.$anno1;
		  ?>
		  desde el		  
  		<?php echo $fec_ini;?> al <?php echo $fec_fin1;} 
  		echo $condicion;?></div>
		<tr>
		  <td height="49" colspan="7" align="center">
		  <table width="729" border="0" class="tablaalertas">
            <tr class="td_gray">
              <td align="center" class="normalNegroNegrita">Acta</td>
              <td align="center" class="normalNegroNegrita">Estatus acta</td>
              <td align="center" class="normalNegroNegrita">Fecha de Notificaci&oacute;n</td>
	       	  <td align="center" class="normalNegroNegrita">Quien lo reporta</td>
              <td align="center" class="normalNegroNegrita">Infocentro</td>
			  <td align="center" class="normalNegroNegrita">Activo</td>
              <td align="center" class="normalNegroNegrita">Marca</td>
              <td align="center" class="normalNegroNegrita">Modelo</td>
              <td align="center" class="normalNegroNegrita">Serial</td>
              <td align="center" class="normalNegroNegrita">Falla</td>
              <td align="center" class="normalNegroNegrita">Ubicaci&oacute;n</td>
              <td align="center" class="normalNegroNegrita">Estado</td>
              <td align="center" class="normalNegroNegrita">Municipio</td>
              <td align="center" class="normalNegroNegrita">Parroquia</td>
              <td align="center" class="normalNegroNegrita">Garant&iacute;a</td>
              <td align="center" class="normalNegroNegrita">Fecha de Entrada</td>
              <td align="center" class="normalNegroNegrita">Persona contacto</td>
              <td align="center" class="normalNegroNegrita">Tel&eacute;fono del contacto</td>
              <td align="center" class="normalNegroNegrita">Fecha de reporte</td>
              <td align="center" class="normalNegroNegrita">N&deg; de ticket</td>
              <td align="center" class="normalNegroNegrita">Datos del t&eacute;cnico</td>
              <td align="center" class="normalNegroNegrita">Fecha de visita</td>
              <td align="center" class="normalNegroNegrita">Observaciones</td>
              <td align="center" class="normalNegroNegrita">Revisi&oacute;n</td>
              <td align="center" class="normalNegroNegrita">Fecha de cierre</td>
              <td align="center" class="normalNegroNegrita">Observaciones del cierre</td>
            </tr>
	<?php 
	
		$resultado_set_t1=pg_query($conexion,$sql_tabla1) or die("Error al mostrar consulta de casos");
		$i=0;
		
	    while ($rowt1=pg_fetch_array($resultado_set_t1))
	    {
		 if ($rowt1['infocentro']<>""){
		 	$datos_info="SELECT t1.nombre as info,direccion,t2.nombre as estado,t3.nombre as parroquia,t4.nombre as municipio 
		 	FROM safi_infocentro t1
		 	LEFT OUTER JOIN safi_edos_venezuela t2 ON (t1.edo_id=t2.id)
		 	LEFT OUTER JOIN safi_parroquia t3 ON (t3.id=t1.parroquia_id)
		 	LEFT OUTER JOIN safi_municipio t4 ON (t3.municipio_id=t4.id) 
		 	WHERE nemotecnico='".$rowt1['infocentro']."'";
		 //	echo $datos_info."<br><br>";
		 	$resultado_info=pg_query($conexion,$datos_info);
		 	if ($rowi=pg_fetch_array($resultado_info)){
		 		$nombrei=$rowi['info'];
		 		$direccion=$rowi['direccion'];
		 		$estado=$rowi['estado'];
		 		$municipio=$rowi['municipio'];
		 		$parroquia=$rowi['parroquia'];
		 	}
		 }else{
		 	$nombrei="";
		 	$direccion="";
		 	$estado="";
		 	$municipio="";
		 	$parroquia="";
		 	
		 	
		 }
		?>
 	    <tr>
 	     <td class="normal">
 	    <a href="javascript:abrir_ventana('actas/garantia_pdf.php?codigo=<?php echo $rowt1['acta_id']; ?>')" class="copyright"><?php echo $rowt1['acta_id'];?></a></td>
 	     <td class="normal"><?php echo $rowt1['esta_nombre'];?></td>
	     <td class="normal"><?php echo $rowt1['fecha_n'];?></td>
		 <td class="normal"><?php echo strtoupper($rowt1['solicitante']);?></td>
		 <td class="normal"><?php echo $nombrei; ?></td>
		 <td class="normal"><?php echo $rowt1['nombre_activo']; ?></td>
         <td class="normal"><?php echo strtoupper($rowt1['bmarc_nombre']);?></td>
	     <td class="normal"><?php echo $rowt1['modelo']; ?></td>
         <td class="normal"><?php echo $rowt1['serial']; ?></td>
         <td class="normal"><?php echo $rowt1['falla']; ?></td>
         <td class="normal"><?php echo $direccion; ?></td>
         <td class="normal"><?php echo $estado; ?></td>
         <td class="normal"><?php echo $municipio; ?></td>
         <td class="normal"><?php echo $parroquia; ?></td>
         <td class="normal"><?php echo $rowt1['garantia']." meses"; ?></td>
         <td class="normal"><?php echo $rowt1['fecha_e']; ?></td>
         <td class="normal"><?php echo $rowt1['persona_contacto']; ?></td>
         <td class="normal"><?php echo $rowt1['tlf_contacto']; ?></td>
         <td class="normal"><?php echo $rowt1['fecha_r']; ?></td>
         <td class="normal"><?php echo $rowt1['nro_caso']; ?></td>
         <td class="normal"><?php echo $rowt1['datos_tecnico']; ?></td>
         <td class="normal"><?php echo $rowt1['fecha_v']; ?></td>
         <td class="normal"><?php echo $rowt1['observaciones']; ?></td>
         <td class="normal"><?php echo $rowt1['datos_revision']; ?></td>
         <td class="normal"><?php echo $rowt1['fecha_c']; ?></td>
         <td class="normal"><?php echo $rowt1['observaciones_cierre']; ?></td>                  
        </tr>
  <?php } ?>
          </table></td>
		  </tr>
		<tr>
		  <td colspan="7" align="center" class="normal">&nbsp;</td>
		  </tr>
		<tr>
		<td colspan="7" align="center" class="normalNegrita">
		<br>
		<span class="normal"><span class="peq_naranja">Detalle generado  el d&iacute;a
        <?=date("d/m/y")?>
 a las
<?=date("H:i:s")?>
        </span><br />
        <br>
</td>
		</tr>
  </table>
	   <?php
	   }
    }
?>
</form>
</body>
</html>
