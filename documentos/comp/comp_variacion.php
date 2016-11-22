<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
	 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:../../index.php',false);
	ob_end_flush(); 	
	exit;
  }
  ob_end_flush(); 
	  
$usuario = $_SESSION['login'];
$user_perfil_id = $_SESSION['user_perfil_id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:Variaci&oacute;n Compromiso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script type="text/javascript">
  g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script>
function detalle(codigo) {
  url="detalle.php?codigo="+codigo;
  newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
  if (window.focus) {newwindow.focus()}
}
function ejecutar() { 
  document.form.hid_validar.value=2;
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
		
  if ( (anio1>anio2) || ((anio1==anio2)  &&  (mes1>mes2)) || ((anio1 == anio2) && (mes1==mes2) && (dia1>dia2)) )
  {
	alert("La fecha inicial no debe ser mayor a la fecha final"); 
    document.form.hid_hasta_itin.value='';
	return;
  }
}
</script>
</head>
<body>
<form name="form" action="comp_variacion.php" method="post">
<input type="hidden" value="0" name="hid_validar" /><br />
<table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
    <td height="21" colspan="2" class="normalNegroNegrita" align="left">B&uacute;squeda de variaci&oacute;n de solicitudes de compromiso</td>
  </tr>
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  <tr>
	<td width="175" height="29" class="normalNegrita" align="left">Solicitados entre:</td>
	<td>
      <input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["txt_inicio"];?>"/>
	  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
	  <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/></a>
      <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" value="<?php echo $_POST["hid_hasta_itin"];?>"/>
	  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
	  <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	  </a>	</td>
  </tr>
  <tr>
	<td class="normalNegrita" align="left">C&oacute;digo del documento:</td>
	<td><span class="normalNegrita"> <input name="txt_cod" type="text" class="peq" id="txt_cod" value="comp-" size="12" /></span></td>
  </tr>
</table>
<br></br>
<div align="center">
<input type="button" value="Buscar" onclick="javascript:ejecutar();">
</div>
</form>
<br/>

<form name="form3" action="" method="post">
<?php 
if ($_POST['hid_validar']==2) {
	$fecha_in=trim($_POST['txt_inicio']);
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$fecha_ini2=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_inis2=substr($fecha_in,6,4).substr($fecha_in,3,2).substr($fecha_in,0,2);
	$fecha_fin2=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2);
	$wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";

if (strlen($fecha_ini2)>2) {
	$wheretipo1 = "and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY MM DD')>='".$fecha_ini2."' and to_date(to_char(sc.comp_fecha, 'YYYY-MM-DD'), 'YYYY-MM-DD')<='".$fecha_fin2."' ";
}

if (strlen($_POST['txt_cod'])>5) {
	$wheretipo2 = " and sc.comp_id='".$_POST['txt_cod']."' ";
}

$sql_or=" select distinct sc.comp_id from  sai_comp_traza sc where sc.esta_id<>2  ".$wheretipo1. $wheretipo2 ." ";
$resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar la descripcion del compromiso");  
?>
  <table width="100%" border="0" align="center">
   <tr>
	 <?php
	   $ano1=substr($fecha_ini,0,4);
	   $mes1=substr($fecha_ini,5,2);
	   $dia1=substr($fecha_ini,8,2);
			
	   $ano2=substr($fecha_fin,0,4);
	   $mes2=substr($fecha_fin,5,2);
	   $dia2=substr($fecha_fin,8,2);
	  ?>
     <td width="495" height="27" class="normal"><div align="center">Resultado de la b&uacute;squeda de variaci&oacute;n de compromisos </div></td>
   </tr>
  </table>
  <table width="100%" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
	  <td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>				  
 	  <td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
 	  <td width="128" class="normalNegroNegrita" align="center">Variaci&oacute;n</td>
	</tr>
    <?
     while ($rowor=pg_fetch_array($resultado_set_most_or))  {
		$beneficiario_nombre= "";
		$beneficiario = "";?>
	<tr class="normal">
	  <td height="28" align="center"><span class="link"><a href="javascript:abrir_ventana('comp_detalle.php?codigo=<?php echo trim($rowor['comp_id']); ?>&amp;esta_id=<?php echo($rowor['esta_id']);?>')" class="copyright"><?php echo $rowor['comp_id'] ;?></a></span></td>
	  <td align="center"><span class="normalNegro"><?php echo $rowor['comp_fecha'];?></span></td>
	  <td align="center"><span class="normalNegro"><span class="link">
	<?php 
  	 // se carga el compromiso original, contra el que se va a comparar
	 $sql_or1=" select distinct t7.comp_id, 
	 to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha,
	 case sc.esta_id when '10' then 'Activo' else 'Inactivo' end  as esta_id,
	 t7.comp_sub_espe,
	 coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as comp_acc_pp, 
	 coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as comp_acc_esp,
	 
	 t7.comp_monto,
	 t7.comp_monto_exento,
	 t7.comp_fecha as fecha1,
	 to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss'),
	 to_char(sc.comp_fecha, 'DD/MM/YYYY') as fecha2
	 from sai_comp_traza sc,sai_comp_imputa_traza t7 left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
	 left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
	 left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
	 left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
	 where 
	 sc.esta_id<>2 and 
	 t7.comp_id=sc.comp_id and 
	 to_char(t7.comp_fecha, 'YYYY-MM-DD HH:mm:ss')=to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss') ".$wheretipo1. $wheretipo2 . "
	 and t7.comp_id='".$rowor['comp_id']. "' 
	 order by fecha1 asc,3,4,5,6  "; 

 	 $resultado_set_most_or1=pg_query($conexion,$sql_or1) or die("Error al consultar la descripcion del compromiso");  
 	 $vuelta=0;
	 $sub="";
	 $cg="";
	 $cc="";
	 $mto=0;
	 $esta="";
	 $fecha="";
	 $fecha22="";
	 ?>
									
<table border=0>
  <tr><td colspan=6><hr color="red"></td></tr>
  <tr><td><b>Fecha</b></td><td><b>Partida</b></td><td><b>CG</b></td><td><b>CC</b></td><td><b>Monto</b></td></tr>
  <tr><td colspan=6><hr color="red"></td></tr>
	<?php 
	$vuelta=0;
	$vuelta2=0;
	$recorrida="";
	while ($rowor1=pg_fetch_array($resultado_set_most_or1))  {
			
		if($fecha==""||($fecha==$rowor1['fecha1']&&$vuelta>0))	{
			
			$sub1[$vuelta]=$rowor1['comp_sub_espe'];
			$cg1[$vuelta]=$rowor1['comp_acc_pp'];
			$cc1[$vuelta]=$rowor1['comp_acc_esp'];
			$esta1[$vuelta]=$rowor1['esta_id'];
			$fecha1[$vuelta]=$rowor1['fecha1'];
			$fecha2[$vuelta]=$rowor1['fecha2'];
			$mto1[$vuelta]=$rowor1['comp_monto'];
			$fecha=$rowor1['fecha1'];		
	?>
  <tr><td class="normalNegro"><b><?php echo $fecha2[$vuelta]?></b></td><td><b><?php echo $sub1[$vuelta]?></b></td><td><b><?php echo $cg1[$vuelta]?></b></td><td><b><?php echo $cc1[$vuelta]?></b></td><td  align=right><b><?php echo number_format($mto1[$vuelta], 2, ",", ".");?></b></td></tr>
	<?php 
			$recorrida=$recorrida.",".$vuelta;
			$vuelta=$vuelta+1;
			$vuelta2=$vuelta;
		}else{
			
			  if($fecha!=$rowor1['fecha1']){
									
				$taman=count(explode(",",$recorrida))-1;
				$arreglo=explode(",",$recorrida);
				if($taman!=$vuelta2){
					for($i=1;$i<$taman;$i++){
					  if($arreglo[$i]!=$i){
					  	$mto1[$i]="";
					    $cg1[$i]="";
					    $cc1[$i]="";
					    $fecha1[$i]="";
					    $sub1[$i]="";
					    $esta1[$i]="";
					   }									
					 }
				}
				$recorrida="";
					
				?>
  <tr><td colspan=6 height=20></td></tr>
  <?php 
					 
				}
				$encontro=0;
				for($i=0;$i<$vuelta2;$i++){									
					if($rowor1['comp_sub_espe']==$sub1[$i]){
						
						if($mto1[$i]==$rowor1['comp_monto']){
							$mto=0;
						}else{
							  $mto=$rowor1['comp_monto']-$mto1[$i];
							 }
							if($cg1[$i]==$rowor1['comp_acc_pp']){
								$cg="-";
							}else{
								$cg=$rowor1['comp_acc_pp'];
							}
							if($cc1[$i]==$rowor1['comp_acc_esp']){
								$cc="-";
							}else{
								$cc=$rowor1['comp_acc_esp'];
							}
							$fecha=$rowor1['fecha1'];
							$sub=$rowor1['comp_sub_espe'];
							$esta=$rowor1['esta_id'];
							
							$mto1[$i]=$rowor1['comp_monto'];
							$cg1[$i]=$rowor1['comp_acc_pp'];
							$cc1[$i]=$rowor1['comp_acc_esp'];
							$fecha1[$i]=$rowor1['fecha1'];
							$fecha2[$i]=$rowor1['fecha2'];
							$sub1[$i]=$rowor1['comp_sub_espe'];
							$esta1[$i]=$rowor1['esta_id'];
							$recorrida=$recorrida.",".$i;
							$fecha22=$rowor1['fecha2'];										
							$encontro=1;
										
					}  
				 }
				 
				 if($encontro==0){
				 	$sub=$rowor1['comp_sub_espe'];
				 	$mto=$rowor1['comp_monto'];
				 	$cg=$rowor1['comp_acc_pp'];
				 	$cc=$rowor1['comp_acc_esp'];
				 	$fecha=$rowor1['fecha1'];
				 	$esta=$rowor1['esta_id'];
				 	$mto1[$vuelta2]=$rowor1['comp_monto'];
					$cg1[$vuelta2]=$rowor1['comp_acc_pp'];
					$cc1[$vuelta2]=$rowor1['comp_acc_esp'];
					$fecha1[$vuelta2]=$rowor1['fecha1'];
					$fecha2[$vuelta2]=$rowor1['fecha2'];
					$sub1[$vuelta2]=$rowor1['comp_sub_espe'];
					$esta1[$vuelta2]=$rowor1['esta_id'];
					$fecha22=$rowor1['fecha2'];
					
					$recorrida=$recorrida.",".$vuelta2;
					$vuelta2=$vuelta2+1;
				 }
				if($mto<>0||$cg!="-"||$cc!="-"){?>
  <tr><td><?php echo $fecha22?></td><td><?php echo $sub?></td><td><?php echo $cg?></td><td><?php echo $cc?></td><td  align=right><?php echo number_format($mto, 2, ",", ".")?></td></tr>
          <?php 
				}
				$vuelta=0;
			}
	}	

								
$sql_or1=" select distinct t7.comp_id, to_char(sc.comp_fecha, 'DD/MM/YYYY') as comp_fecha,
 case sc.esta_id when '10' then 'Activo' else 'Inactivo' end  as esta_id,
 t7.comp_sub_espe, coalesce(t8.centro_gestor, ' ') || coalesce(t9.centro_gestor, ' ') as comp_acc_pp, 
coalesce(t8.centro_costo, ' ') || coalesce(t9.centro_costo, ' ') as comp_acc_esp, 
 t7.comp_monto,t7.comp_monto_exento,t7.comp_fecha as fecha1, to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss'),
 to_char(sc.comp_fecha, 'DD/MM/YYYY') as fecha2
 from sai_comp_traza sc,sai_comp_imputa_traza t7 left outer join sai_acce_esp t8 on (t7.comp_acc_pp=t8.acce_id and t7.comp_acc_esp=t8.aces_id )
 left outer join sai_ac_central t10 on(t8.acce_id=t10.acce_id) 
 left outer join sai_proy_a_esp t9 on(t7.comp_acc_pp=t9.proy_id and t7.comp_acc_esp=t9.paes_id) 
 left outer join sai_proyecto t11 on (t9.proy_id=t11.proy_id ) 
 where 
 sc.esta_id<>2 and 
 t7.comp_id=sc.comp_id and 
 to_char(t7.comp_fecha, 'YYYY-MM-DD HH:mm:ss')=to_char(sc.comp_fecha2, 'YYYY-MM-DD HH:mm:ss') ".$wheretipo1. $wheretipo2 . "
 and t7.comp_id='".$rowor['comp_id']. "' 
 order by fecha1 desc,3,4,5,6  "; 

 $resultado_set_most_or1=pg_query($conexion,$sql_or1) or die("Error al consultar la descripcion del compromiso");  
	
 $vuelta=0;
 $sub="";
 $cg="";
 $cc="";
 $mto=0;
 $esta="";
 $fecha="";
 $fecha22="";
 $vuelta=0;
 $vuelta2=0;
 $recorrida="";
 ?>
  <tr><td colspan=6 height=20></td></tr>
  <?php 
	while ($rowor1=pg_fetch_array($resultado_set_most_or1))  {
							
	 if($fecha==""||($fecha==$rowor1['fecha1']&&$vuelta>0))	{
								
		$sub1[$vuelta]=$rowor1['comp_sub_espe'];
		$cg1[$vuelta]=$rowor1['comp_acc_pp'];
		$cc1[$vuelta]=$rowor1['comp_acc_esp'];
		$esta1[$vuelta]=$rowor1['esta_id'];
		$fecha1[$vuelta]=$rowor1['fecha1'];
		$fecha2[$vuelta]=$rowor1['fecha2'];
		$mto1[$vuelta]=$rowor1['comp_monto'];
		$fecha=$rowor1['fecha1'];	?>
  <tr><td><b><?php echo $fecha2[$vuelta]?></b></td><td><b><?php echo $sub1[$vuelta]?></b></td><td><b><?php echo $cg1[$vuelta]?></b></td><td><b><?php echo $cc1[$vuelta]?></b></td><td align=right><b><?php echo number_format($mto1[$vuelta], 2, ",", ".")?></b></td></tr>
  <?php 
		$vuelta=$vuelta+1;
		$vuelta2=$vuelta;
	 }else
		  {
			break;
		  }
	}
						
?>
	
</table></span></td></tr>
<?php	 
	}//fin del while que obtiene los datos de la consulta
 }
?> 
 </table> 
</form>
</body>
</html>