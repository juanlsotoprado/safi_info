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

	$sql = " SELECT * FROM sai_permiso_reporte('repo_pres','$perfil') as resultado ";

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
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Reporte presupestario pagaado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript"
	SRC="../../js/lib/js/CalendarPopup.js"> </SCRIPT>
<script LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>


<script>



//-->
</script>
</head>
<body>
<br />
<br />

REPORTE PRESUPUESTARIO DEL PAGADO ANULADO

<br />
<br />
<font class="normal"> <?php 

	$proyectos[]=null;
	$especifica[]=null;
	$cc[]=null;
	$cg[]=null;
	$contador=0;
	$login=$_SESSION['login'];
	$fechaIinicio= $_REQUEST["hid_desde_itin"];
	$fechaFfin=$_REQUEST["hid_hasta_itin"];
	
	$fecha_ini=substr($fechaIinicio,6,4)."-".substr($fechaIinicio,3,2)."-".substr($fechaIinicio,0,2);
	$ano1=substr($fecha_ini,0,4);
	$sql = "select acce_id as proyecto, aces_id as especifica, centro_gestor, centro_costo from sai_acce_esp where pres_anno=".$ano1;

	if($_REQUEST['proyecto']!=null && $_REQUEST['proyecto']!='0') {
		list( $proy, $especif ) = split( ':::', $_POST['proyac'] );
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
	$quer=$quer." when '".$proy1_2009."' then ".$contador;
	$contador=$contador+1;
	$quer=$quer." when '".$proy2_2009."' then ".$contador;
	$quer=$quer." else 0 end ";
	$quer2=$quer2." else '0' end ";
	
	$totalproy=count($proyectos);

	$montotapartado=0;
	$montototalapartado=0;
	$montotcausado=0;
	$montototalcausado=0;
	$montotpagado=0;
	$montototalpagado=0;
	$apartado;
	$causado;
	$pagado;



	$mesI=substr($fechaIinicio, 3, 2);
	$mesF=substr($fechaFfin, 3, 2);


	$resultado_set_most_orc=pg_query($conexion,$sql) or die("Error al consultar las Cuentas");

	echo "<br>REPORTE PRESUPUESTARIO DEL PAGADO ";
	if($_REQUEST['partida']!=null && $_REQUEST['partida']!=''){
		echo "PARTIDA: ".$_REQUEST["partida"];
	}
	if($_REQUEST['hid_desde_itin']!=null && $_REQUEST['hid_desde_itin']!='' && $_REQUEST['hid_hasta_itin']!=null && $_REQUEST['hid_hasta_itin']!=''){
		echo " ENTRE: ".$fechaIinicio." y ".$fechaFfin."<br>";
	}
	?>

 

<table width="100%" border="1" align="center" class="tablaalertas">
   
   <tr>
     <td bgcolor="#E4E4E4" class="peq"><div align="center"><strong>Documento</strong></div></td>    
     <td bgcolor="#E4E4E4" class="peq"><div align="center"><strong>Documento2</strong></div></td>     
     <td bgcolor="#E4E4E4" class="peq"><div align="center"><strong>Proyecto/especifica</strong></div></td>
      <td bgcolor="#E4E4E4"><div align="center" class="peq"><strong>Monto Contable</strong></div></td>
      <td bgcolor="#E4E4E4"><div align="center" class="peq"><strong>Monto Presupuesto</strong></div></td>
      <td bgcolor="#E4E4E4"><div align="center" class="peq"><strong>Numero Reserva</strong></div></td>
	
	
   </tr>

	<?php
	$montototalapartado=0;

	$montototalcausado=0;
	$montotcausado=0;
	$montotpagado=0;
	$montotapartado=0;
	$montototalpagado=0;
	$montotcausadoprinc=0;
	
require_once("unionpresupuestocontabilidad2det.php");

	$sqla="";


	
	$sqla=$sqla." select caus_id ,caus_docu_id ,part_id ,
monto_pres ,proyecto ,especifica ,comp_id , cpat_id ,monto_ctble ,
comp_fec ,$quer as posicion,doc2,numero_reserva,pos from sai_prescontable4_".$login." where proyecto<>0 " ;

if($_REQUEST['hid_desde_itin']!=null && $_REQUEST['hid_desde_itin']!='' && $_REQUEST['hid_hasta_itin']!=null && $_REQUEST['hid_hasta_itin']!=''){
	//$sqla=$sqla." and comp_fec >= to_date('".$fechaIinicio."', 'DD MM YYYY') and comp_fec <= to_date('".$fechaFfin."', 'DD MM YYYY')";
	$sqla=$sqla." and ((comp_fec >= to_date('".$fechaIinicio."', 'DD MM YYYY') and comp_fec <= to_date('".$fechaFfin."', 'DD MM YYYY') and pos=1) or (fecha_anulacion >= to_date('".$fechaIinicio."', 'DD MM YYYY') and fecha_anulacion <= to_date('".$fechaFfin."', 'DD MM YYYY') and pos=2))"; 
	
} 

if($_REQUEST['proyecto']!=null && $_REQUEST['proyecto']!='0') {
	//list( $proy, $especif ) = split( ':::', $_REQUEST['proyac'] );
	$sqla=$sqla." and proyecto='".$_REQUEST['proyecto']."' and especifica='".$_REQUEST['especifica']."'";
}
if($_REQUEST["partida"]!=null && $_REQUEST["partida"]!='' ){
	$sqla=$sqla." and part_id = '".$_REQUEST["partida"]."'";
}

	$sqla=$sqla." order by 3,5,6,2 ";
	//echo $sqla."<br><br><br>";
	$resultado_set_most_or=pg_query($conexion,$sqla) ;
	
	

	$vueltas=0;
	
	$posAnterior='0';
	
	
	
	
	$totalregistros=0;
	$totalregistrosimpresos=0;
	$proyes="";
	$compids="";
	
	while($rowor=pg_fetch_array($resultado_set_most_or))
	{
		
		$causado=$rowor['monto_ctble'];
	$causadopres=$rowor['monto_pres'];
	
	if($posAnterior!=0&&$posAnterior!=$rowor['posicion']){
		?>		
		<tr><td></td>
		<td></td><td></td>
		<td><b><?php echo number_format($montototalcausado,2,',','.');?></b></td>
		<td><b><?php echo number_format($montototalcausadopres,2,',','.');?></b></td>
		</tr>
		
	             <?php   
	             $montototalcausado=0;
	             $montototalcausadopres=0;
	}
		?>		
		<tr><td><?php echo $rowor['caus_docu_id'];?></td>
		<td><?php echo $rowor['doc2']."<br>".$rowor['comp_id'];?></td>
		<td><?php echo $rowor['proyecto']."/".$rowor['especifica'];?></td>
		<td><?php echo number_format($rowor['monto_ctble'],2,',','.');?></td>
		<td><?php echo number_format($rowor['monto_pres'],2,',','.');?></td>
		<td><?php echo $rowor['numero_reserva'];?></td>
		</tr>
		
	             <?php   
	            
				
		$montototalcausado=$montototalcausado+$causado;
$montototalcausadopres=$montototalcausadopres+$causadopres;
		
		$posAnterior=$rowor['posicion'];
		$caus_docu_id=$rowor['caus_docu_id'];
	      $proyecto=$rowor['proyecto'];
	      $especifica=$rowor['especifica'];
	            
		 }
		
if($posAnterior!=0){
		?>
		<tr><td></td>
		<td></td><td></td>
		<td><b><?php echo number_format($montototalcausado,2,',','.');?></b></td>
		<td><b><?php echo number_format($montototalcausadopres,2,',','.');?></b></td>
		</tr>
		<?php 
}
		$causado=0;
		$causadopres=0;
	
	

		
				
							 
	
		 



//	}

	
	//}
	
		pg_close($conexion);
?>


</table>
</font>
<table width="667" border="0" align="center">
	<tr>
		<td width="632" scope="col">&nbsp;</td>
	</tr>
</table>
<p>&nbsp;</p>
</body>
</html>
