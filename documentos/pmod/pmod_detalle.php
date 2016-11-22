<?php 
ob_start();
session_start();
require_once("../../includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Modificaciones presupuestarias</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="../../js/funciones.js"> </script>
<script language="JavaScript">
function imprimir() {
document.getElementById('noimprimir').style.visibility='hidden';
window.print();
}

function cerrar(){
  window.close();
}

//-->
</SCRIPT>
</head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<body onLoad="bloquear()">
<?php 
$cod_doc=$_GET['codigo'];
$longitud=strlen($cod_doc);
$ao=substr($cod_doc,$longitud-2,2);
$pres_anno="20".$ao;
$des_est=$_GET['esta_id'];
$sql= "SELECT to_char(f.f030_fecha, 'DD/MM/YYYY') as fecha, case f.f030_tipo when 3 then 'Credito' when 5 then 'Traspaso' when 2 then 'Disminucion' end as tipo, d.depe_nombre as dependencia, f.f030_motivo as motivo
	   FROM sai_forma_0305 f, sai_dependenci d
	   WHERE f.depe_id=d.depe_id and f.f030_id='".$cod_doc."' AND f.pres_anno=".$pres_anno;
$result=pg_exec($sql);
  
if(!$result){
   echo("Error");
}
else{ 
	$row=pg_fetch_array($result);
?>
<table width="80%" border="0" class="tablaalertas">
    <tr class="td_gray">
        <td align="center" class="normalNegroNegrita" colspan="2">MODIFICACI&Oacute;N PRESUPUESTARIA <br></td>
     </tr>
  <tr>
    <td class="normalNegroNegrita">C&oacute;digo:</td>
    <td class="normal"><?php echo($cod_doc);?></td>
   </tr>
   <tr>
            <td class="normalNegroNegrita">Fecha:</td>
            <td class="normal"><?php echo($row['fecha']);?></td>
          </tr>
          <tr>
            <td class="normalNegroNegrita">Dependencia:</td>
            <td class="normal"><?php echo($row['dependencia']);?></td>
          </tr>
          <tr>
          <td class="normalNegroNegrita">Tipo: </td>
            <td class="normal"><?php echo($row['tipo']);?></td>        
   </tr>
    <tr>
          <td class="normalNegroNegrita">Motivo: </td>
            <td class="normal"><?php echo($row['motivo']);?></td>        
   </tr>   
<?php 
$sql= "SELECT fd.part_id AS partida,fd.f0dt_tipo AS tipo,fd.f0dt_id_acesp AS id_aesp, fd.f0dt_monto AS monto, coalesce(p.centro_gestor,'')||coalesce(a.centro_gestor,'') || '/'|| coalesce(p.centro_costo,'')||coalesce(a.centro_costo,'') as centro
	   FROM sai_fo0305_det fd
	   LEFT OUTER JOIN sai_proy_a_esp p on (p.paes_id=fd.f0dt_id_acesp)
	   LEFT OUTER JOIN sai_acce_esp a on (a.aces_id=fd.f0dt_id_acesp)
	   WHERE fD.f030_id='".$cod_doc."' AND fD.pres_anno=".$pres_anno;
     $result=pg_exec($sql); 
	 if(!$result){
	   echo("Error Mostrando los detalles");
	 } 
  ?>
  <tr>
    <td colspan="2">
	  <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
       <tr class="td_gray">
        <td align="center" class="normalNegrita">Proy/Acc</td>
      <!--   <td class="normalNegrita" align="center">Dependencia</td>-->
        <td align="center" class="normalNegrita"> Partida</td>
<!--         <td ><div align="center" class="normalNegrita">Denominaci&oacute;n</td> -->
        <td class="normalNegrita" align="center">Tipo</td>
        <td align="center" class="normalNegrita">Monto</td>
      </tr>
	    <?php  
		  while($row=pg_fetch_array($result))  { 
			 if($row['tipo']==1) $tipo="Receptora";   
			 else $tipo="Cedente";    
		   ?>
		   <tr>
			<td align="center" class="normal"><?php echo($row['centro']); ?></td>
		<!-- 	<td align="center"  class="normal"><?php echo($row['dependencia']); ?></td>-->
			<td align="center" class="normal"><?php echo($row['partida']); ?> </td>
		<!-- 	<td width="195"><div align="center" class="normal"><?php echo($row_dt_pda['detalle']); ?></div></td>-->
			<td align="center" class="normal"><?php  echo($tipo);?></td>
			<td align="right" class="normal"><?php echo(number_format($row['monto'],2,',','.')); ?></td>
		  </tr>	
	  <?php } ?>
	</table>		
 <tr> 
      <td class="normal" colspan="2">
	   <table width="420" align="center">
	   <tr><td>
			<?
           include("../../includes/respaldos_mostrar.php");
			?>
			</td></tr>
		</table>
		</td>
 </tr> 
</table>	
<br></br> 
   <div id="noimprimir" style="visibility:visible" align="center">
     <a href="javascript:imprimir();" class="normal"><img src="../../imagenes/boton_imprimir.gif" width="23" height="20" border="0"/></a>
    </div>
<?php } pg_close($conexion);?>
</body>
</html>