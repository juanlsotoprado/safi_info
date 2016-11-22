<?php 
    ob_start();
	session_start();
	require("../../includes/conexion.php");

	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   //header('Location:../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	  }
	ob_end_flush(); 

	$perfil = $_SESSION['user_perfil_id'];
	$tiene_permiso = 0;
        //Verificar si el usuario tiene permiso para el objeto (accion) actual
	$sql = " SELECT * FROM sai_permiso_reporte('repo_caus','$perfil') as resultado ";
    
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$tiene_permiso = $row["resultado"];
	}

	if ($tiene_permiso == 0) {
		//Enviar mensaje de error
		?>
		<script>
		document.location.href = "../../mensaje.php?pag=principal.php";
		</script>
		<?
		header('Location:index.php',false);	
	}

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reporte presupestario Pagado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />


<link type="text/css" 	href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" 	rel="stylesheet" />
<script type="text/javascript" 	src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" 	src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" 	src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript"> 	g_Calendar.setDateFormat('dd/mm/yyyy');	</script>


<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" /><script LANGUAGE="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript" SRC="../../js/lib/js/CalendarPopup.js"> </SCRIPT>
<script LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>


<script>


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

function habiDesabiFechas(elemento){
    txt_inicio = document.getElementById("txt_inicio");
    hid_hasta_itin = document.getElementById("hid_hasta_itin");
    if (elemento.checked==true){ 
            txt_inicio.disabled=false;
            hid_hasta_itin.disabled=false;
    }else{ 
            txt_inicio.disabled=true;
            hid_hasta_itin.disabled=true; 
            txt_inicio.value="";
            hid_hasta_itin.value="";
    }
}

function validar(op){
	if(op==1){
		document.form1.action="reporte_pagado.php?valor=1";		
	}
	document.form1.submit();
}
//-->
</script>
</head>
<body>
<br />
<br />
<form name="form1" method="post" >
<div align="center">
<font class="normal">
REPORTE PRESUPUESTARIO DEL PAGADO ANULADO</font>
<br /><br />
<table>
<tr><td colspan=2 class="normal">

Proyecto/Acci&oacute;n Central-Acci&oacute;n Espec&iacute;fica: <select name='proyac'>

<option value="0" class="normal">Todos</option>
<?php

$sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$_SESSION['an_o_presupuesto']." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$_SESSION['an_o_presupuesto']." order by 1,2";
	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");  
	while($rowor=pg_fetch_array($resultado_set))
	{
		?> 
		
		<option value="<?php echo $rowor['proyecto'].":::".$rowor['especifica']?>"  class="normal"><?php echo $rowor['centro_gestor'].'/'.$rowor['centro_costo']?></option>
		
		<?php		
	}
?>


</select>

 </td></tr>

<tr><td colspan=2 class="normal">

Partida: <input type="text" value="" name="partida" id="partida"/>

 </td></tr>
 <tr>
 <td class="normal">Fecha Inicio:
 
 <input type="text"
			size="10" id="hid_desde_itin" name="hid_desde_itin" class="dateparse"
			onfocus="javascript: comparar_fechas(this);" readonly="readonly" /> <a
			href="javascript:void(0);"
			onclick="g_Calendar.show(event, 'hid_desde_itin');"
			title="Show popup calendar"> <img
			src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
			alt="Open popup calendar" /> </a> 
	</td>
<td class="normal">Fecha Fin:


 <input type="text"
			size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
			onfocus="javascript: comparar_fechas(this);" readonly="readonly" /> <a
			href="javascript:void(0);"
			onclick="g_Calendar.show(event, 'hid_hasta_itin');"
			title="Show popup calendar"> <img
			src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img"
			alt="Open popup calendar" /> </a> 

    </td>
 </tr>
<tr><td colspan=2 class="normal"><input type="hidden" name="Buscars" vallue="1"/>
<input type="button" value="Buscar" onclick="validar(1);"/>  
<!--<input type="button" value="Buscar Contabilidad" onclick="validar(2);"/>-->
<!--  <input type="button" value="Excel" onclick="validar(3);"/>-->
</td></tr></table>
</div>
</form>
<br /><font class="normal">
<?php 
//echo "ssssss".$_REQUEST["valor"]."dddd";
if ($_REQUEST["valor"]=='1') {
	$valor=$_REQUEST["valor"];
	$proyectos[]=null;
	$especifica[]=null;
	$cc[]=null;
	$cg[]=null;
	$contador=0;
	$fechaIinicio= $_POST["hid_desde_itin"];
	$fechaFfin=$_POST["hid_hasta_itin"];
	
	$fecha_ini=substr($fechaIinicio,6,4)."-".substr($fechaIinicio,3,2)."-".substr($fechaIinicio,0,2);
	$ano1=substr($fecha_ini,0,4);
	
	
	$sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$ano1;

	if($_REQUEST['proyecto']!=null && $_REQUEST['proyecto']!='0') {
		list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
		$sql=$sql." and acce_id='".$_REQUEST['proyecto']."' and aces_id='".$_REQUEST['especifica']."'";
	}

	$sql=$sql." union select proy_id as proyecto, paes_id as especifica, centro_gestor, centro_costo from sai_proy_a_esp where pres_anno=".$ano1;
	if($_REQUEST['proyecto']!=null && $_REQUEST['proyecto']!='0') {
		//list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
		$sql=$sql." and proy_id='".$_REQUEST['proyecto']."' and paes_id='".$_REQUEST['especifica']."'";
	}

	$sql=$sql." order by 1,2";

//	echo $sql."<br>ggggg".$totalproy."<br>";
	$resultado_set=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");
	$quer2=" case cd.padt_id_p_ac||'_'||cd.padt_cod_aesp  ";
	$quer=" case proyecto||'_'||especifica  ";
	while($rowor=pg_fetch_array($resultado_set))
	{
		$proyectos[$contador]=$rowor['proyecto'];
		$especifica[$contador]=$rowor['especifica'];
		$cg[$contador]=$rowor['centro_gestor'];
		$cc[$contador]=$rowor['centro_costo'];
		$contador=$contador+1;
		$quer=$quer."  when '".$rowor['proyecto']."_".$rowor['especifica']."' then ".$contador;
		$quer2=$quer2."  when '".$rowor['proyecto']."_".$rowor['especifica']."' then '".$rowor['centro_gestor']."/".$rowor['centro_costo']."'";
	}
	
	$proy1_2009="30_30311";
	$proy2_2009="30_30211";
	$contador=$contador+1;
	$quer=$quer." when '".$proy1_2009."' then ".$contador;
	$contador=$contador+1;
	$quer=$quer." when '".$proy2_2009."' then ".$contador;
	$quer=$quer." else 0 end ";
	$quer2=$quer2." else '0' end ";
	$resultado_set_most_orc=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");

	//echo "voy";
	require_once("unionpresupuestocontabilidad2.php");
	//echo "yaa";
	
	$totalproy=count($proyectos);
	
	$montotapartado=0;
	$montototalapartado=0;
	$montotcausado=0;
	$montototalcausado=0;
	$montotpagado=0;
	$montototalpagado=0;
	$apartado;
	$causado;
	$causadopres;
	$pagado;
	

	
	$mesI=substr($fechaIinicio, 3, 2);
	$mesF=substr($fechaFfin, 3, 2);
	
	
 

echo "<br>REPORTE PRESUPUESTARIO DEL PAGADO ANULADO";  
if($_POST['partida']!=null && $_POST['partida']!=''){
	echo "PARTIDA: ".$_POST["partida"]; 
}
if($_POST['hid_desde_itin']!=null && $_POST['hid_desde_itin']!='' && $_POST['hid_hasta_itin']!=null && $_POST['hid_hasta_itin']!=''){
    echo " ENTRE: ".$fechaIinicio." y ".$fechaFfin."<br>";
}
?>



<table width="100%" border="1" align="center" class="tablaalertas">
   
   <tr>
     <td bgcolor="#E4E4E4" class="peq"><div align="center"><strong>Partida </strong></div></td>
     <?php
for ($i=0;$i<$totalproy;$i++ ){
	 ?>
	<!--   <td width="85" height="14" bgcolor="#E4E4E4"><div align="center" class="peq"><strong><?php echo " " .$cg[$i]."/".$cc[$i] ?></strong></div></td> -->
	  <td width="85" height="14" bgcolor="#E4E4E4"><div align="center" class="peq"><strong><?php echo "'".$cg[$i]."/".$cc[$i] ?></strong></div></td>
	 <?php
}
    
    
   $query = "select  part_id, posicion, monto_pres, proyecto, especifica, monto_ctble from  sai_prescontable5_".$login. " where especifica='30311' and proyecto=30 order by 1";

$resultado_set_query=pg_query($conexion,$query) or die("Error al consultar el Proyecto 30/30111");
if($rowquery=pg_fetch_array($resultado_set_query)){
 $totalproy++;

?>
<td width="85" height="14" bgcolor="#E4E4E4"><div align="center" class="peq"><strong>30/30311(2009)</strong></div></td>
  <?php }
   
 $query = "select  part_id, posicion, monto_pres, proyecto, especifica, monto_ctble from  sai_prescontable5_".$login. " where especifica='30211' and proyecto=30 order by 1";
 $resultado_set_query=pg_query($conexion,$query) or die("Error al consultar el Proyecto 30/30211");
if($rowquery=pg_fetch_array($resultado_set_query)){
 $totalproy++;
  ?>   
   <td width="85" height="14" bgcolor="#E4E4E4"><div align="center" class="peq"><strong>30/30211(2009)</strong></div></td>
  <?php }?>
     
	    
	  
	
     <td bgcolor="#E4E4E4" class="peq"><div align="center"><strong>Total</strong></div></td> 
   </tr>
    <?php //Inicio del While 
   /*while($rowo=pg_fetch_array($resultado_set_most_orc))
    {*/
   ?>
   
  
	
	<?php
	$montototalapartado=0;
	
	$montototalcausado=0;
	$montototalcausadopres=0;
	$montotcausado=0;
	$montotpagado=0;
	$montotapartado=0;
	$montototalpagado=0;
	$montotcausadoprinc=0;
	
   
	$sqla="";

 $sqla = "select  part_id, posicion, monto_pres, proyecto, especifica, monto_ctble from  sai_prescontable5_".$login. " order by 1";
   
	
//echo $sqla."<br><br><br>";	
$resultado_set_most_or=pg_query($conexion,$sqla) or die("Error al consultar las Cuentas");
$vueltas=0;
$montotcausado=$montotcausadoprinc;
$partida=0;
$partidaAnterior=0;
while($rowor=pg_fetch_array($resultado_set_most_or))
{
	//echo "<br>".$vueltas."----".$partidaAnterior."------".$rowor['part_id'];
	$partida=$rowor['part_id'];
	if($vueltas>0&&$partidaAnterior!=0&&$partidaAnterior!=$rowor['part_id']){
		while($vueltas<$totalproy){
	     	?>	
           <td align="right" class="peq" width="100"><?php  echo "0,00";?></td>   
  
        <?php  
 			$vueltas=$vueltas+1;
		}
	if($vueltas==$totalproy){
		if($valor==1){
	?>
		<td align="right" class="peq" width="100"><?php  if($montototalcausado==$montototalcausadopres) { echo number_format($montototalcausado,2,',','.');}else{echo number_format($montototalcausado,2,',','.')." (" .number_format($montototalcausadopres,2,',','.').")";};?></td>  </tr> 
	
		
		<?php
		}else{
			?>
		<td align="right" class="peq" width="100"><?php   echo number_format($montototalcausado,2,',','.');?></td>  </tr> 
	
		
		<?php
		}
	$vueltas=0;
	$montototalcausado=0;
	$montototalcausadopres=0;
}
	}
	
	
	if($vueltas==0) {
		?>
		<tr>
   	<td colspan="<?php echo $contador+2?>"><hr size="1" width="100%"></td>
   </tr>
		<tr>
    <td width="45" align="center" valign="middle" class="peq" ><?php echo "<a href='reporte_pagadoDetalle.php?partida=".$rowor['part_id']."&hid_desde_itin=".$_POST['hid_desde_itin']."&hid_hasta_itin=".$_POST['hid_hasta_itin']."&proyecto=".$proy."&especifica=".$especif."' target='_blanc'>".$rowor['part_id']."</a>";?></td>
    <?php 
    $partidaAnterior=$rowor['part_id'];
    
    }
		$vueltas=$vueltas+1;
	$causado=$rowor['monto_ctble'];
	$causadopres=$rowor['monto_pres'];
	/*if($fechaIinicio!=null && $fechaIinicio!='' ){
	  // if($mesI=='09' || $mesF=='09'){	
		  //$montotcausado=$montotcausado-$causado;
	   //}
	   $montotcausado=0;
	}else{	
		$montotcausado=$montotcausado-$causado;		
	}*/
	while($vueltas<$rowor['posicion']){
		
		?>	
           <td align="right" class="peq" width="100"><?php  echo "0,00";?></td>   
  
        <?php  
        $vueltas=$vueltas+1;
	}

	
	
  
    $montototalcausado=$montototalcausado+$causado;
$montototalcausadopres=$montototalcausadopres+$causadopres;
  

//$montototalapartado=$montototalapartado+$montotapartado;
	
//$montototalcausado=$montototalcausado+$montotcausado;
	
//$montototalpagado=$montototalpagado+$montotpagado;
if($vueltas==$rowor['posicion']){
	
	if($valor==1){
		?>	
    <td align="right" class="peq" width="100"><?php  if($causado==$causadopres) { echo number_format($causado,2,',','.');}else{echo number_format($causado,2,',','.')." (" .number_format($causadopres,2,',','.').")";};?></td>   
  
  <?php  }else{
  	?>	
    <td align="right" class="peq" width="100"><?php  echo number_format($causado,2,',','.');?></td>   
  
  <?php  
  }
 //$vueltas=$vueltas+1;
	}
if($vueltas==$totalproy){
	if($valor==1){
	?>
		<td align="right" class="peq" width="100"><?php if($montototalcausado==$montototalcausadopres) { echo number_format($montototalcausado,2,',','.');}else{echo number_format($montototalcausado,2,',','.')." (" .number_format($montototalcausadopres,2,',','.').")";};?></td>  </tr> 
	
		
		<?php }else{
			?>
		<td align="right" class="peq" width="100"><?php  echo number_format($montototalcausado,2,',','.');?></td>  </tr> 
	
		
		<?php
		}
	$vueltas=0;
	$montototalcausado=0;
	$montototalcausadopres=0;
}



}
//echo "<br>".$vueltas."----".$partidaAnterior."------".$rowor['part_id'];
if($vueltas>0&&$partidaAnterior!=0&&$partidaAnterior!=$rowor['part_id']){
		while($vueltas<$totalproy){
	     	?>	
           <td align="right" class="peq" width="100"><?php  echo "0,00";?></td>   
  
        <?php  
 			$vueltas=$vueltas+1;
		}
	if($vueltas==$totalproy){
		if($valor==1){
	?>
		<td align="right" class="peq" width="100"><?php  if($montototalcausado==$montototalcausadopres) { echo number_format($montototalcausado,2,',','.');}else{echo number_format($montototalcausado,2,',','.')." (" .number_format($montototalcausadopres,2,',','.').")";};?>
		</td>  </tr> 
	
		
		<?php }else{
			?>
		<td align="right" class="peq" width="100"><?php   echo number_format($montototalcausado,2,',','.');?>
		</td>  </tr> 
	
		
		<?php
		}
	$vueltas=0;
	$montototalcausado=0;
	$montototalcausadopres=0;
}
	}
//}
}

pg_close($conexion);

?>
	
	
</table></font>
<table width="667" border="0" align="center">
  <tr>
    <td width="632" scope="col">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
 