<?php 
    ob_start();
	session_start();
	 require_once("../../includes/conexion.php");
	   
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   //header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	  }
	ob_end_flush(); 
require("../../includes/fechas.php");
 ?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Anulaci&oacute;n Solicitud de Pago</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" /><script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');

	</script>
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script language="JavaScript">
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
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}


function validar()
{
  if(trim(document.form1.txt_fecha_f.value)=='' || trim(document.form1.txt_fecha_i.value)==''){
    alert(" Debe indicar el intervalo de fechas !!!. ");
    return;
  }else{document.form1.submit();}   
}

function enviar()
{
   contenido=prompt("Indique el motivo de la devoluci\u00F3n: ","");
   document.getElementById('contenido_memo').value=contenido;
   document.form1.action = "sopg_eaprobados.php";
   document.form1.submit();       
   
  
}

function abrir_selecion() {

	url = "sel_fondo.php";
	<?  if (!isset($_SESSION["var_fondo"])) { ?>
	newwindow=window.open(url,'fondo','height=210,width=520,scrollbars=yes,resizable=yes,status=no');
	if (window.focus) {newwindow.focus()}
	<? } ?>
}

</script>	
</head>
<body>
<br>
<br>

<form name="form1" method="post">
<table width="408" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaCentral">
    <tr class="td_gray">
      <td height="21" colspan="3" class="normalNegroNegrita" align="left">ANULACI&Oacute;N SOLICITUD DE PAGO </td>
    </tr>
  <tr>
            <td height="33" align="center" class="normalNegrita"><div align="right">Desde: </div></td>
           
             <td class="peq">
             <input type="text" size="10" id="txt_fecha_i" name="txt_fecha_i" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fecha_i');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
             
  
			  </td>
            <td><input type="button" value="Listar" onclick="validar()"/></td>
          </tr>					    
          <tr>
            <td height="33" align="center" class="normalNegrita"><div align="right">Hasta: </div></td>
            
          <td class="peq">
     		<input type="text" size="10" id="txt_fecha_f" name="txt_fecha_f" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" value=""/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fecha_f');" title="Show popup calendar">
<img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
			 </td>
            <td>&nbsp;</td>
          </tr>
  </table>	

<?	
if (isset($_POST["txt_fecha_i"])) {
?>
<br /><br />
<div align="center">
<br />
<?php 
$fecha_i=$_POST['txt_fecha_i'];
$fecha_f=$_POST['txt_fecha_f'];
$fecha_ia = cambia_fecha_iso($fecha_i);
$fecha_fa = cambia_fecha_iso($fecha_f);

/*Listado de solicitudes aprobadas*/
$sql="SELECT * FROM sai_consultar_sopg_aprobados('".$fecha_ia."','".$fecha_fa."') as (codigo_sopg varchar,monto_sopg float, ci_rif varchar, docg_fecha timestamp, bene_tp int2)";
$resultado=pg_query($conexion,$sql) or die("Error al consultar las Solicitudes");  
?>

<table width="60%" border="0" class="tablaalertas">
   <tr>
     <td height="14" colspan="5" bgcolor="#FFFFFF"><div align="center" class="normalNegrita"><strong>Solicitud de Pago Culminadas</strong></div></td>
   </tr>
   <tr class="td_gray">
     <td  class="normalNegroNegrita" width="10%"><div align="center"><strong> &nbsp; </strong></div></td>
     <td  width="25%"><div align="center" class="normalNegroNegrita"><strong>C&oacute;digo sopg</strong></div></td>
     <td class="normalNegroNegrita" width="20%"><div align="center"><strong>Fecha Solicitud</strong></div></td>
     <td width="25%" ><div align="center" class="normalNegroNegrita"><strong>Beneficiario</strong></div></td>
     <td  class="normalNegroNegrita" width="20%"><div align="center"><strong>Monto Bs.</strong></div></td>
   </tr>
    <?php //Inicio del While 
   $i=0;
   while($rowor=pg_fetch_array($resultado))
   {
	$i++;
   ?>
  <tr class="normalNegro">
    <td width="10%" align="center" ><input type="checkbox" name="solicitud[]" value="<?php echo $rowor['codigo_sopg'];?>" /> </td>
    <td width="25%"><div align="center"><?php echo $rowor['codigo_sopg'];?></div></td>
    <td width="20%" align="center"  ><?php $fecha_sopg= $rowor['docg_fecha']; 
                 
					$ano=substr($fecha_sopg,0,4);
					$mes=substr($fecha_sopg,5,2);
					$dia=substr($fecha_sopg,8,2);
					$hora=substr($fecha_sopg,10);
					echo $dia."-".$mes."-".$ano ; 
?></td>
<?$sopg_bene_tp=$rowor['bene_tp'];
  $sopg_bene_ci_rif=$rowor['ci_rif'];
//Buscar datos del benefiario segun sea el tipo (1:sai_empleado 2_sai_proveedor 3:sai_viat_benef)
	if($sopg_bene_tp==1) //Empleado
	{
	 	$sql_be="select * from sai_buscar_datos_sopg('$sopg_bene_ci_rif',1,'','','','','',0) 
		resultado_set(depe_id varchar, depe_nombre varchar,empl_nombres varchar,empl_apellidos varchar)"; 
		$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar empleado");
		if($rowbe=pg_fetch_array($resultado_set_most_be))
		{
		   $nombre_bene=$rowbe['empl_nombres'].' '.$rowbe['empl_apellidos'];
		   $depe_nombre_bene=trim($rowbe['depe_nombre']);
		}
	}
	else
	   if($sopg_bene_tp==2) //Proveedor
	   {
	       $sql_be="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$sopg_bene_ci_rif''','',2) 
		   resultado_set(prov_nombre varchar)"; 
		   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar proveedor");
		   if($rowbe=pg_fetch_array($resultado_set_most_be))
		   {
		   	  $nombre_bene=$rowbe['prov_nombre'];
		     //La dependencia es la del solicitante (buscarla por el usua_login registrado)
			 $depe_nombre_bene=$dependencia;
		   }
	   }
	   else
	       if($sopg_bene_tp==3) //Otro beneficiario
		   {
			   $sql_be="SELECT * FROM sai_seleccionar_campo('sai_viat_benef','benvi_nombres,benvi_apellidos','benvi_cedula='||'''$sopg_bene_ci_rif''','',2) 
			   resultado_set(benvi_nombres varchar,benvi_apellidos varchar)"; 
			   $resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar otro beneficiario");
			   if($rowbe=pg_fetch_array($resultado_set_most_be))
			   {
				  $nombre_bene=$rowbe['benvi_nombres'].' '.$rowbe['benvi_apellidos'];
				 //La dependencia es la del solicitante (buscarla por el usua_login registrado)
				  $depe_nombre_bene=$dependencia;
			   }
		   }
	

		  $tt_neto= $rowor['monto_sopg'];
		  
	$sql= "select * from sai_buscar_sopg_reten ('".trim($rowor['codigo_sopg'])."') as resultado ";
	$sql.= "(docu_id varchar, impu_id varchar, rete_monto float8,  por_rete float4,";
	$sql.= "por_imp float4,servicio varchar,monto_base float8)";
	$resultado_set= pg_exec($conexion ,$sql);
	$valido=$resultado_set;
		if ($resultado_set)
  		{
			$tt_retencion=0;
 			while($row_rete_doc=pg_fetch_array($resultado_set))	
			 {
				$tt_retencion=$row_rete_doc['rete_monto']+$tt_retencion;
			 }

		} 
		
 /*Consulto las OTRAS retenciones del documento */
 $sql_be="SELECT sopg_partida_rete,sopg_ret_monto,part_nombre FROM sai_sol_pago_otra_retencion t1, sai_partida t2 WHERE sopg_id='".$rowor['codigo_sopg']."' AND t1.sopg_partida_rete=t2.part_id and t2.pres_anno='".$_SESSION['an_o_presupuesto']."'"; 
	$resultado_set_most_be=pg_query($conexion,$sql_be) or die("Error al consultar partida");
	
	if ($resultado_set_most_be)
	{
  	  $total_otras_rete=0;
	  while($rowbe=pg_fetch_array($resultado_set_most_be))
	  {
		  $total_otras_rete=$total_otras_rete+$rowbe['sopg_ret_monto'];
	   }
 		
	}
		$tt_neto=$tt_neto-$tt_retencion-$total_otras_rete;   

?>

    <td width="25%"><div align="center"><?php echo $nombre_bene;?></div></td>
 <td width="20%" align="right" ><?php echo number_format($tt_neto,2,',','.');?></td>

  </tr>
  <?php }?>
<tr><td><input type="hidden" name="contador" id="contador" value="<?echo $i;?>" /></td></tr>
</table>
<br />
<input type="button" value="Anular" onclick="javascript:enviar();"/>
<input type="hidden" name="contenido_memo" id="contenido_memo">
</div>
<?}?>
</form>
</body>
</html>
<?php pg_close($conexion);?>