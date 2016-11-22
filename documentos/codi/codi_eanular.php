<?php 
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");
  if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado"))  {
	header('Location:../../index.php',false);
	ob_end_flush(); 	
	exit;
  }	
  ob_end_flush();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Comprobante Diario</title>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>

<?php	
//$codigo=trim($_POST['codigo']);
$codigo=trim($_GET['codigo']);
$usuario = $_SESSION['login'];
//$motivo=trim($_POST['txt_memo']);
$motivo=trim($_GET['txt_memo']);

$sql="SELECT * FROM  sai_seleccionar_campo ('sai_doc_genera,sai_empleado','empl_nombres, empl_apellidos','docg_id='||'''$codigo'''||' and sai_doc_genera.usua_login=empl_cedula' ,'',2)
resultado_set(empl_nombres varchar, empl_apellidos varchar)";

$resultado=pg_exec($conexion,$sql) or die("Error al mostrar"); 
if($row=pg_fetch_array($resultado))
{
 $usuario=$row['empl_nombres']." ".$row['empl_apellidos'];
}

$total_imputacion=0;
$sql_presupuesto="SELECT * FROM sai_buscar_datos_causado('".$codigo."','codi') as resultado (categoria varchar, aesp varchar, anno int2, apde_tipo bit,part_id varchar, apde_monto float8)";  
$resultado_pre=pg_query($conexion,$sql_presupuesto) or die ("Error al mostrar datos presupuestarios");

$i=0;
$total_imputacion=pg_num_rows($resultado_pre);
while ($row_pre=pg_fetch_array($resultado_pre)){
 $categoria[$i]=$row_pre['categoria'];
 $aesp[$i] =$row_pre['aesp'];
 $anno =$row_pre['anno'];
 $apde_tipo =$row_pre['apde_tipo'];
 $apde_partida[$i]=$row_pre['part_id'];
 $apde_monto[$i]=$row_pre['apde_monto'];
 $id_part=$apde_partida[$i];
  
 $convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','cpat_id','part_id=''$id_part''','',2) resultado_set(cpat_id varchar)"; 
 $resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor"); 
 if($row=pg_fetch_array($resultado_conv))
 { 
  $cuenta[$i]=trim($row['cpat_id']);
 }

  if ($apde_tipo==1){ //Por Proyecto
	$query ="Select * from sai_buscar_centro_gestor_costo_proy('".trim($categoria[$i])."','".trim($aesp[$i])."') as result (centro_gestor varchar, centro_costo varchar)";
  }else{ //Por Accion Centralizada
		$query ="Select * from  sai_buscar_centro_gestor_costo_acc('".trim($categoria[$i])."','".trim($aesp[$i])."') as result (centro_gestor varchar, centro_costo varchar)";
	    }

		$resultado_query= pg_exec($conexion,$query);
		if ($resultado_query){
		   if($row=pg_fetch_array($resultado_query)){
		   $centrog[$i] = trim($row['centro_gestor']);
		   $centroc[$i] = trim($row['centro_costo']);
		   }
		 }
 $i++;
}

$sql_documento="SELECT * FROM sai_seleccionar_campo('sai_doc_genera','numero_reserva','docg_id=''$codigo''','',2) resultado_set(numero_reserva varchar)";  
$resultado_doc=pg_query($conexion,$sql_documento) or die ("Error al mostrar datos del documento");
if ($row_doc=pg_fetch_array($resultado_doc)){
 $reserva=$row_doc['numero_reserva'];
}

$sql  = "select * from sai_anular_codi('" .  $_SESSION['login'] . "', '". $codigo. "' , '";
$sql .= $motivo . "','" . $_SESSION['user_depe_id'] . " ' ) as memo_id";
$resultado_set = pg_exec($conexion ,$sql) or die("Error al Anular el Codi");


$sql="SELECT * FROM  sai_seleccionar_campo ('sai_codi','comp_id,comp_fec,comp_comen,comp_fec_emis,esta_id,nro_compromiso','comp_id=''$codigo''','',2)
resultado_set(comp_id varchar, comp_fec date,comp_comen text,comp_fec_emis timestamp,esta_id int4,nro_compromiso varchar)"; 
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
{ 
  $id_comp=trim($row['comp_id']);
  $fecha_comp=$row['comp_fec'];
  $comentario=$row['comp_comen'];
  $fecha_emis=$row['comp_fec_emis'];
  $edo=$row['esta_id'];
  $compromiso=$row['nro_compromiso'];
} 
if ($edo==15){
  $anulado=1;
}

 if($compromiso<>'N/A'){
	//ACTUALIZAR MONTO DISPONIBLE DEL COMP
	for($j=0; $j<$total_imputacion; $j++)
	  {  
	  $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$apde_partida[$j]."' and comp_acc_pp='".$categoria[$j]."' and comp_acc_esp='".$aesp[$j]."'";
	  $resultado_query = pg_exec($conexion,$query_disponible);
	  if ($row=pg_fetch_array($resultado_query)){
		$disponible=$row['disponible']+$apde_monto[$j];
		$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$apde_partida[$j]."' and comp_acc_pp='".$categoria[$j]."' and comp_acc_esp='".$aesp[$j]."'";
		$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
	    $cod_pcta=$pcta_id;
 	  }
			
	 }
 }
?>
</head>
<link  rel="stylesheet" href="../../ccs/plantilla.css" type="text/css" media="all"  />
<body>
<form name="form" method="post" action="codi_e1.php">
<table width="764" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas" >
    <tr class="td_gray"> 
	<td colspan="5" class="normalNegroNegrita" >Anular comprobante diario</td>
  </tr>
  <tr>
    <td colspan="5" align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td class="normalNegrita">C&oacute;digo:</td>
    <td width="671" align="right" class="normal"><div align="left"><?php echo $codigo;?></div></td>
  </tr>
        
  <?if ($anulado==1) {?>
  <tr>
    <td  colspan="5"><div align="center"><font color="Red"><STRONG>ANULADO</STRONG></div></td>
  </tr>
  <?}?>
  <tr>
    <td width="81" class="normalNegrita"><strong>Fecha:</strong></td>
    <td width="671" colspan="3" align="right" class="normal"><div align="left"><?php echo $fecha_comp?></div></td>
  </tr>

  <tr>
    <td class="normalNegrita">Justificaci&oacute;n:</td>
    <td colspan="2" align="right" class="normal"><div align="left"><?php echo $comentario?></div></td>
  </tr>
  <tr>
    <td colspan="3" align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" class="normalNegrita"><table width="747" height="87" align="center" background="../Imagenes/fondo_tabla.PNG" id="servicios">
  <tr valign="middle" class="Estilo4">
    <td width="39" class="titularMedio"><div align="center">N&deg; Registro</div></td>
    <td width="90" class="titularMedio"><div align="center">Cuenta</div></td>
    <td><p align="center" class="titularMedio"><strong>Descripci&oacute;n</strong></p></td>
    <td width="156" align="right" class="titularMedio"><strong>Debe</strong></td>
    <td width="103" align="right" class="titularMedio"><strong>Haber</strong></td>
  </tr>
  <?php
	$sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$codigo''','',2) resultado_set(comp_id varchar, reng_comp int8,cpat_id varchar,cpat_nombre varchar,rcomp_debe float8,rcomp_haber float8,rcomp_tot_db float8,rcomp_tot_hab float8)"; 
    $resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar"); 
	  
	while($row=pg_fetch_array($resultado))
	{ 
		$id_comp=trim($row['comp_id']);
		$comp_reng=$row['reng_comp'];
		$id_cta=trim($row['cpat_id']);
		$nom_cta=$row['cpat_nombre'];
		$debe=$row['rcomp_debe'];	
		$haber=$row['rcomp_haber'];		
		$total_db=$row['rcomp_tot_db'];	
		$total_haber=$row['rcomp_tot_hab'];	

		?>
  <tr valign="top" class="normal">
    <td valign="top" class="normal"><?php echo $comp_reng?></td>
    <td align="left" class="normal"><?php echo $id_cta?></td>
    <td><div align="justify"><?php echo $nom_cta?></div></td>
    <td width="68" align="right"><?php echo number_format($debe,2,'.',',')?></td>
    <td height="34" colspan="7" align="right"><?php echo number_format($haber,2,'.',',')?></td>
  </tr>
  <?php } ?>
   <tbody id="body">
   </tbody>
</table>
<table width="379" height="65" align="right" background="../Imagenes/fondo_tabla.PNG" id="totales">
  <tr valign="top" class="normal">
    <td class="normal">&nbsp;</td>
    <td height="17" colspan="2" class="normal"><div align="right"></div></td>
  </tr>
  <tr valign="top" class="normal">
     <td class="normal"><div align="right"><strong>Total:</strong></div></td>
     <td align="right"><?php echo number_format($total_db,2,'.',',');?></td>
     <td width="108" height="15" align="right"><?php echo number_format($total_haber,2,'.',',')?></td>
  </tr>
</table>      </td>
   </tr>
   <tr>
     <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr><TD colspan="4">
     <table>
       <tr>
        <td class="normalNegrita">Fuente de financiamiento:</td>
        <td align="left" class="normal"><div align="left"><?php echo $reserva?></div></td>
      </tr>
      <tr>
        <td class="normalNegrita">N&deg; compromiso:</td>
        <td align="left" class="normal"><div align="left"><?php echo $reserva?></div></td>
      </tr>
    </table></TD>
   </tr>
   <tr class="normal">
     <td colspan="4" align="center" class="style2">Este comprobante fue generado el d&iacute;a: <?php  
       echo cambia_esp($fecha_emis).substr($fecha_emis,10)?><br>por: <?echo $usuario;?><br></td>
   </tr>
   <tr>
     <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
   </tr>
   <tr>
	 <td colspan="3">
	   <table width="50%" border="0" class="tablaalertas" align="center">
          <tr class="td_gray">
            <td  align="center"  class="peqNegrita">Imputaci&oacute;n presupuestaria </td>
          </tr>
		  
          <tr>
            <td  class="normal" align="center" >
              <table width="100%" border="0" >
                <tr>
                  <td width="15%" class="peqNegrita"><div align="center">Proyecto/Acc</div></td>
	 			  <td width="15%" class="peqNegrita"><div align="center">Acci&oacute;n espec&iacute;fica</div></td>
	              <td width="20%" class="peqNegrita"><div align="center">Partida</div></td>
	   		      <td width="15%" class="peqNegrita"><div align="center">Cuenta</div></td>
                  <td width="20%" class="peqNegrita"><div align="center">Monto</div></td>
                </tr>
                <tr>
                <?php
				  for ($ii=0; $ii<$total_imputacion; $ii++)
    			  {?>
    			   <td align="center"><?= $centrog[$ii];?></td>
		  		   <td align="center"><?= $centroc[$ii];?></td>
                  <td  class="peq" align="left" width="15%">
                    <div align="center"><?php $id_part=$apde_partida[$ii];
                    echo $id_part;?>
                    </div></td>
                  <td  class="peq" align="left" width="17%">
                    <div align="center"><?php echo  $cuenta[$ii];?></div></td>
                  <td  class="peq" align="left" width="20%">
                    <div align="center"><?php echo  number_format($apde_monto[$ii],2,'.',',');?></div></td>
                </tr>
               <?php  } ?>
            </table></td>
          </tr>
          </table><br></td></tr>
         <tr class="normal"> 
		  <td height="28" valign="left"><b>Motivo Anulaci&oacute;n: </b></td>
		  <td valign="midden" colspan="3">  <span class="Estilo1"><strong>
			<textarea name="txt_memo"   cols="90" rows="4" class="normal" readonly><? echo($motivo); ?></textarea>
			<input name="codigo" type="hidden" id="codigo" value="<? echo($codigo); ?>">
		  </strong></span></td>
		</tr>
    <tr> 
     <tr>
	    <td height="46" colspan="4">
        <div align="center"> <a href="codi_pdf.php?cod_doc=<?php echo(trim($codigo)); ?>"><img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div>      
       </td>
     </tr>
	   <td height="18" colspan="3">
	  		
	   </td>
	</tr>
  </table>
</form>
</body>
</html>
<?php pg_close($conexion);?>