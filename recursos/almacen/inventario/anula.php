<?php 
  ob_start();
  session_start();
  require_once("../../../includes/conexion.php");
	 	 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:index.php',false);
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
<title>.:SAFI::ANULAR ACTA</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script type="text/javascript">
function detalle(codigo)
{
    url="anula_1.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
function deshabilitar_combo(valor)
{
 if (valor=='1') 
 { 
   document.form.txt_inicio.disabled=false;
   document.form.hid_hasta_itin.disabled=false;
   document.form.txt_cod.value="";
   document.form.txt_cod.disabled=true;
     
   }
 else
 if (valor=='3') 
 { 
   document.form.txt_inicio.disabled=true;
   document.form.hid_hasta_itin.disabled=true;
   document.form.txt_inicio.value="";
   document.form.hid_hasta_itin.value="";
   document.form.txt_cod.disabled=false;
   document.form.txt_cod.value="amat-";
 }

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
	  alert("La fecha inicial no debe ser mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}

</script>
<script language="javascript">
function ejecutar_varios(codigo,codigo1,codigo2)
{ 
   if ((codigo1=='') && (codigo2=='')) 
   {
   	 document.form.hid_validar.value=1;
   }
   else {document.form.hid_validar.value=2;
  
   }
 window.location="anula.php?&codigo="+codigo+"&txt_inicio="+codigo1+"&hid_hasta_itin="+codigo2+"&hid_validar="+document.form.hid_validar.value
  
}
</script>
</head>
<body> 
<form name="form" action="anula.php" method="post">
  <div align="center">
  <input type="hidden" value="0" name="hid_validar" />
  <input type="hidden" value="0" name="opt_validar" />
  <input type="hidden" value="<?php echo $request_id_tipo_documento;?>" name="tipo" />
 <br />
  <br />
  </div>
<table width="555" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
 <tr class="td_gray"> 
  <td height="21" colspan="4" class="titular" align="left">&nbsp;</td>
 </tr>
 <tr>
  <td height="10" colspan="3"></td>
 </tr>
 <tr>
  <td width="20" align="center"><input name="opt_fecha" type="radio" value="1" onClick="javascript:deshabilitar_combo(1)" class="normal" />	</td>
  <td width="175" height="29" class="normalNegrita" align="left">Elaborados entre:</td>
  <td >
  <input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
  <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
  <img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
  </a>
  <input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly"/>
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>	</td>
	
</tr>
<tr>
	<td height="30" align="center" class="normal">
	<input name="opt_fecha" type="radio" value="3" class="normal" onClick="javascript:deshabilitar_combo(3)" />	</td>
	<td class="normalNegrita" align="left">
            C&oacute;digo del documento:
          </td>
	<td><span class="normalNegrita">
	  <input name="txt_cod" type="text" class="peq" id="txt_cod" value="" size="10" disabled="disabled"/>
	</span></td>
</tr>
<tr><td height="10" colspan="3"></td></tr>
<tr><td colspan="3"><div align="center">
  <input class="normalNegro" type="button" value="Buscar" onclick="javascript:ejecutar_varios(document.form.txt_cod.value,document.form.txt_inicio.value,document.form.hid_hasta_itin.value);"/>
 
  <a href="index.php" ><img src="../../../imagenes/boton_reg.gif" name="regresar" width="90" height="31" border="0"></a> </div></td>
</tr>
</table>
</form>
<br>

<form name="form3" action="" method="post">
<?php 
if ($_REQUEST['hid_validar']==1)
{
   echo "<SCRIPT LANGUAGE='JavaScript'>"."alert ('Seleccione una opcion de busqueda......');"."</SCRIPT>";
}

if ( ( (($_GET['txt_inicio'])=='') and (($_GET['hid_hasta_itin'])!='') ) or ( (($_GET['txt_inicio'])!='') and (($_GET['hid_hasta_itin'])=='') ) )
{
  echo "<SCRIPT LANGUAGE='JavaScript'>";
  echo "alert ('Solo selecciono una fecha, la busqueda es entre las dos, verifique por favor...');"."</SCRIPT>";
}
		
if ( (($_GET['txt_inicio'])!='') and (($_GET['hid_hasta_itin'])!='') )
{  
  $fecha_in=trim($_GET['txt_inicio']);  
  $fecha_fi=trim($_GET['hid_hasta_itin']); 
  $fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
  $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
  $sql_or="SELECT * FROM sai_seleccionar_campo('sai_arti_acta_almacen','amat_id,fecha_acta,observaciones,usua_login,esta_id,depe_entregada,entregado_a,tipo','esta_id<>15 and fecha_acta >= '||'''$fecha_ini'' and fecha_acta <= '||'''$fecha_fin'' ORDER BY fecha_acta ASC','',2) 
  resultado_set(amat_id varchar,fecha_acta date,observaciones varchar,usua_login varchar,esta_id int,depe_entregada varchar,entregado_a int,tipo varchar)";    
  $resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar");  
  if(($rowor=pg_fetch_array($resultado_set_most_or))==null)
  {
   echo "<center><font color='#FF0000' class='titular'>"."Actualmente no existen documentos generados por Ud. en este periodo"."</font></center>";
  }
  else
	  {  ?>

<table width="501" border="0" align="center">
 <tr>
   <?php
	 $ano1=substr($fecha_ini,0,4);
	 $mes1=substr($fecha_ini,5,2);
	 $dia1=substr($fecha_ini,8,2);
	 $ano2=substr($fecha_fin,0,4);
	 $mes2=substr($fecha_fin,5,2);
	 $dia2=substr($fecha_fin,8,2);
?>
    <td width="495" height="27" class="titular"><div align="center">Documentos solicitados entre: <?php echo $dia1."-".$mes1."-".$ano1;?> y <?php echo $dia2."-".$mes2."-".$ano2;?></div></td>
  </tr>
</table>

<table width="502" align="center" background="../imagenes/fondo_tabla.gif" class="tablaalertas">
 <tr class="td_gray">
   <td width="137" class="normalNegroNegrita" align="center">C&oacute;digo del Documento </td>
   <td width="128" class="normalNegroNegrita" align="center">Fecha de la Solicitud </td>
   <td width="115" class="normalNegroNegrita" align="center">Estado </td>
   <td width="102" class="normalNegroNegrita" align="center">Opciones</td>
 </tr>
					
 <?php
   $resultado_set_most_pa=pg_query($conexion,$sql_or);  
   while($rowpa=pg_fetch_array($resultado_set_most_pa))
   {
	$ano=substr($rowpa['fecha_acta'],0,4);
	$mes=substr($rowpa['fecha_acta'],5,2);
	$dia=substr($rowpa['fecha_acta'],8,2);
  ?>
  <tr class="normal">
	<td height="28" align="center"><span class="link"><?php echo $rowpa['amat_id'] ;?></span></td>
	<td align="center"><span class="peq"><?php echo $dia."-".$mes."-".$ano;?></span></td>
	<td align="left" class="normal"><div align="center"><span class="peq">
  <?php 
	$sql_est="SELECT * FROM sai_consulta_desc_estado(".$rowpa['esta_id'].") as resultado"; 
	$resultado_est=pg_query($conexion,$sql_est) or die("Error al mostrar el estado de la solicitud");
	$row_est=pg_fetch_array($resultado_est);		
	echo $row_est['resultado'];?></span></div></td>
	<td align="left" class="normal">
	<div align="center"><span class="peqNegrita"> <a href="javascript:abrir_ventana('anula_1.php?codigo=<?php echo trim($rowpa['amat_id']); ?>&tipo=<?php echo $rowpa['tipo'];?>')" class="copyright"><?php echo "Ver detalle"; ?></a></span><br>						</div></td>
   </tr>
   <?php
   }//fin del while que obtiene los datos de la consulta ?>  </table> 
  <?php
	 }	//fin del else que comprueba que si se tiene resultados para mostrar	
}//fin del if que evalua el isset del seleccionar fecha
 else
	{
	 if (($_GET['codigo'])!='') 
	 {
	  $cod=$_GET['codigo']; 
      $sql_or="SELECT * FROM sai_seleccionar_campo('sai_arti_acta_almacen','amat_id,fecha_acta,observaciones,usua_login,esta_id,depe_entregada,entregado_a,tipo','esta_id<>15 and amat_id=''$cod'' ORDER BY fecha_acta ASC','',2)resultado_set(amat_id varchar,fecha_acta date,observaciones varchar,usua_login varchar,esta_id int,depe_entregada varchar,entregado_a int,tipo varchar)";    
	  $resultado_set_most_or=pg_query($conexion,$sql_or) or die("Error al consultar"); 
	  if(($rowor=pg_fetch_array($resultado_set_most_or))==null)
	  {
	   echo "<center><font color='#FF0000' class='titular'>"."Ud. no ha generado un acta con el codigo ingresado <br> o <br> el acta se encuentra ANULADA</font></center>" ?> 
<?php }
	  else{      
?>  
				  
 <table width="502" align="center" background="../imagenes/fondo_tabla.gif" class="tablaalertas">
   <tr class="td_gray">
     <td width="137" class="titularMedio" align="center">C&oacute;digo del Documento </td>
     <td width="128" class="titularMedio" align="center">Fecha del Acta</td>
     <td width="115" class="titularMedio" align="center">Estado </td>
     <td width="102" class="titularMedio" align="center">Opciones</td>
   </tr>
   <?php
	 $resultado_set_most_pa=pg_query($conexion,$sql_or);  
	 while($rowpa=pg_fetch_array($resultado_set_most_pa))
	 {
	  $ano=substr($rowpa['fecha_acta'],0,4);
	  $mes=substr($rowpa['fecha_acta'],5,2);
	  $dia=substr($rowpa['fecha_acta'],8,2);
	?>
   <tr class="normal">
     <td height="28" align="center"><span class="link"><?php echo $rowpa['amat_id'] ;?></span></td>
     <td align="center"><span class="peq"><?php echo $dia."-".$mes."-".$ano;?></span></td>
     <td align="left" class="normal"><div align="center"><span class="peq">
    <?php
	  $estado=$rowpa['esta_id'];
	  $sql_or="SELECT * FROM sai_seleccionar_campo('sai_estado','esta_nombre','esta_id=''$estado''','',2)resultado_set( nombre_estado varchar)";  
	  $resultado_set=pg_query($conexion,$sql_or) or die("Error al consultar"); 
	  if($rowe=pg_fetch_array($resultado_set))
	  {
	   $nombre_est=$rowe['nombre_estado'];
  	  }
 	   echo $nombre_est; ?>   </span></div></td>
      <td align="left" class="normal"><div align="center"><span class="peqNegrita">
		<a href="javascript:abrir_ventana('anula_1.php?codigo=<?php echo trim($rowpa['amat_id']); ?>&tipo=<?php echo $rowpa['tipo'];?>')" class="copyright"><?php echo "Ver detalle"; ?></a></span><br /></div></td>
   </tr>
    <?php	 }//fin del while que obtiene los datos de la consulta  ?>
</table>
	<?php
		 }	//fin del else que comprueba que si se tiene resultados para mostrar	
      }//fin de if que evalua el isset del seleccionar el estado		
}	?>
		
</form>
</body>
</html>
<?php pg_close($conexion);?>

