<?php 
  require("../../includes/fechas.php");
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
	 
  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
  {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
  }
  
  ob_end_flush(); 
	
$user_perfil_id = $_SESSION['user_perfil_id'];
$reserva=trim($_POST['numero_reserva']);
$cod_ajuste=trim($_POST['cod_doc']);
$total_debe=trim ($_POST['total_debe']);
$total_haber=trim($_POST['total_haber']);
$diferencia=trim($_POST['diferencia']);
$cod_imputacion=trim($_POST['txt_cod_imputa']);
$cod_accion=trim($_POST['txt_cod_accion']);
$tp_imputa=trim($_POST['chk_tp_imputa']);
$tp_imputa=(int)$tp_imputa;
$fec=trim($_POST['hid_desde_itin']);
list($dia,$mes,$an_o) = explode ("/",$fec);

$arr_reng=trim($_POST['txt_arreglo']);
$arr_cuenta=trim($_POST['txt_arreglo1']);
$arr_descri=trim($_POST['txt_arreglo2']); 
$arr_debe=trim($_POST['txt_arreglo3']); 
$arr_haber=trim($_POST['txt_arreglo4']); 
$arr_cta_presu=trim($_POST['txt_arreglo5']);
$arr_proy=trim($_POST['txt_arreglo9']); 
$arr_acc=trim($_POST['txt_arreglo10']);
$arr_tp_impu=trim($_POST['txt_arreglo8']);    
$str_a='{'.$arr_cuenta.'}';
$str_d='{'.$arr_debe.'}';
$str_e='{'.$arr_haber.'}';
$str_f='{'.$arr_reng.'}';
$str_p='{'.$arr_cta_presu.'}';
$str_proy='{'.$arr_proy.'}';
$str_acc='{'.$arr_acc.'}';
$str_tp_imp='{'.$arr_tp_impu.'}';
$tip=1;

$descripcion=explode("*",$arr_descri);
$elem_desc=count($descripcion);

for ($h=0;$h<$elem_desc;$h++){
 $matriz_descripcion[$h]=trim($descripcion[$h]);

}

$ctas=explode(",",$arr_cuenta);
$elem_cta=count($ctas);

for ($h=0;$h<$elem_cta;$h++){
 $matriz_ctas[$h]=trim($ctas[$h]);

}

$part=explode(",",$arr_cta_presu);
$elem_part=count($part);


for ($h=0;$h<$elem_part;$h++){
 $matriz_part[$h]=trim($part[$h]);
}

$proy=explode(",",$arr_proy);
$elem_proy=count($proy);


for ($h=0;$h<$elem_proy;$h++){
 $matriz_proy[$h]=trim($proy[$h]);
}

$acc=explode(",",$arr_acc);
$elem_acc=count($acc);


for ($h=0;$h<$elem_acc;$h++){
 $matriz_acc[$h]=trim($acc[$h]);
}

$arrdebe=explode(",",$arr_debe);
$elem_debe=count($arrdebe);


for ($h=0;$h<$elem_debe;$h++){
 $matriz_debe[$h]=trim($arrdebe[$h]);
}
/*Montos haber*/
$arrhaber=explode(",",$arr_haber);
$elem_haber=count($arrhaber);


for ($h=0;$h<$elem_haber;$h++){
	$matriz_haber[$h]=trim($arrhaber[$h]);
}

require_once("../../includes/arreglos_pg.php");
$arreglo_cuentas = convierte_arreglo($matriz_ctas);
$arreglo_partidas = convierte_arreglo($matriz_part);
$arreglo_descripcion = convierte_arreglo($matriz_descripcion);

$sql = "select * from sai_insert_comp_diario('".$fec."','Diario','".$_POST['txt_comentario']."','".$_SESSION['login']."','".$str_f."','".$str_a."','".$arreglo_descripcion."','".$str_d."','".$str_e."','".$total_debe."','".$total_haber."','".$diferencia."','".$_SESSION['user_depe_id']."','".$tip."','".$cod_ajuste."','".$_POST['num_ref']."','".$_POST['comp_id']."','".$reserva."','".$str_p."'
,'".$an_o."','".$str_proy."','".$str_acc."','".$str_tp_imp."','".$_SESSION['user_perfil_id']."','".$reserva."'
) resultado_set(text)";
$resultado=pg_query($conexion,$sql);
if($resultado)
{
  $rowa = pg_fetch_array($resultado,0); 
  if ($rowa[0] <> null)
  {
	$codigo=trim($rowa[0]);
	if (strtoupper($_POST['comp_id'])<>'N/A'){
	  $compromiso=$_POST['comp_id'];
	  //ACTUALIZAR MONTO DISPONIBLE DEL COMP, SIEMPRE QUE TENGA COMPROMISO ASOCIADO
	  for($j=0; $j<$elem_part; $j++)
	  {  
	   $query_disponible="SELECT monto as disponible FROM sai_disponibilidad_comp WHERE comp_id='".$compromiso."' and partida='".$matriz_part[$j]."' and comp_acc_pp='".$matriz_proy[$j]."' and comp_acc_esp='".$matriz_acc[$j]."'";
	   $resultado_query = pg_exec($conexion,$query_disponible);
	   if ($row=pg_fetch_array($resultado_query)){
	   	if ($matriz_debe[$j] > 0)
	   		$disponible=$row['disponible']-$matriz_debe[$j];
		/*El monto está por el haber, aumenta disponibilidad*/
	   	if ($matriz_debe[$j] == 0)
	   		$disponible=$row['disponible']+$matriz_haber[$j];
		$query = "UPDATE sai_disponibilidad_comp set monto='".$disponible."' WHERE comp_id='".$compromiso."' and partida='".$matriz_part[$j]."' and comp_acc_pp='".$matriz_proy[$j]."' and comp_acc_esp='".$matriz_acc[$j]."'";
		$resultado_set = pg_exec($conexion ,$query) or die(utf8_decode("Error al actualizar disponibilidad del Compromiso"));
	  	$cod_pcta=$pcta_id;
 	  }
	 }
	}
  }
}
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAI:Comprobante de Diario</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/lib/funciones.js"> </SCRIPT>
</head>
<?php
$sql="SELECT * FROM  sai_seleccionar_campo ('sai_codi','comp_id,comp_fec,comp_comen,comp_fec_emis,esta_id','comp_id=''$codigo''','',2)
resultado_set(comp_id varchar, comp_fec date,comp_comen text,comp_fec_emis timestamp,esta_id int4)"; 
$resultado=pg_query($conexion,$sql) or die("Error al mostrar"); 
while($row=pg_fetch_array($resultado))
{ 
  $id_comp=trim($row['comp_id']);
  $fecha_comp=$row['comp_fec'];
  $comen=$row['comp_comen'];
  $fecha_emis=$row['comp_fec_emis'];
} 
?>
<body >
<p>&nbsp;</p>
<form name="form" method="post" action="">
  <p align="center">
  <input type="hidden" value="0" name="hid_validar" />
  <input type="hidden" value="0" name="opt_validar" />
</p>
<table width="678" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" >
    <tr class="td_gray"> 
	  <td colspan="5" class="normalNegroNegrita" align="center">Comprobante diario</td>
	</tr>
    <tr>
      <td colspan="5" align="right" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
      <td class="normalNegrita">C&oacute;digo:</td>
      <td colspan="3" align="right" class="normalNegro"><div align="left"><?php echo $id_comp;?></div>      </td>
    </tr>
    <tr>
      <td width="82" class="normalNegrita">Fecha:</td>
      <td width="584" colspan="3" align="right" class="normalNegro"><div align="left"><?php echo $fecha_comp?></div></td>
    </tr>
    <tr>
      <td class="normalNegrita">Documento asociado:</td>
      <td colspan="3" align="right" class="normalNegro"><div align="left"><?php echo $cod_ajuste?></div></td>
    </tr>
    <tr>
      <td  class="normalNegrita">Justificaci&oacute;n:</td>
      <td colspan="2" align="right" class="normalNegro"><div align="left"><?php echo $comen?></div></td>
    </tr>
       <tr>
      <td colspan="3" align="right" class="normalNegrita">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" class="normalNegrita"><table width="673" height="75" align="center" background="../Imagenes/fondo_tabla.PNG" id="servicios">
    <tr valign="middle" class="Estilo4">
      <td width="39" class="normalNegrita"><div align="center">N&deg; Registro</div></td>
      <td width="127" class="normalNegrita">Cuenta contable</td>
      <td class="normalNegrita"><p  class="titularMedio">Descripci&oacute;n</p></td>
      <td width="103" align="right" class="normalNegrita">Debe</td>
      <td width="103" align="right" class="normalNegrita">Haber</td>
    </tr>
   <?php
	 $sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$id_comp''','',2)
	 resultado_set(comp_id varchar, reng_comp int8,cpat_id varchar,cpat_nombre varchar,rcomp_debe float8,rcomp_haber float8,rcomp_tot_db float8,rcomp_tot_hab float8)"; 
	 $resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar"); 
  	 while($row=pg_fetch_array($resultado))
  	 { 
		$id_comp=trim($row['comp_id']);
		$comp_reng=$row['reng_comp'];
		$id_cta=$row['cpat_id'];
		$nom_cta=$row['cpat_nombre'];
		$debe=$row['rcomp_debe'];	
		$haber=$row['rcomp_haber'];		
		$total_db=$row['rcomp_tot_db'];	
		$total_hb=$row['rcomp_tot_hab'];	
	 ?>
     <tr valign="top" class="normalNegro">
       <td valign="top" ><?php echo $comp_reng?></td>
       <td align="left" ><?php echo $id_cta?></td>
       <td width="277" valign="top"><div align="justify"><?php echo $nom_cta?></div></td>
       <td align="right" valign="top" ><?php echo number_format($debe,2,'.',',')?></td>
       <td height="22" colspan="7" align="right" valign="top"><?php echo number_format($haber,2,'.',',')?></td>
     </tr>
     <?php } ?>
     
     <tbody id="body">
     </tbody>
 </table>
 
 <table width="339" height="21" align="right" background="../../Imagenes/fondo_tabla.PNG" id="totales">
   <tr valign="top" class="normal">
     <td width="118" class="normalNegro"><div align="right">Total:</div></td>
     <td width="102" align="right" class="normalNegro"><?php echo number_format($total_db,2,'.',',');?></td>
     <td width="103" height="15" align="right" class="normalNegro"><?php echo number_format($total_hb,2,'.',',')?></td>
   </tr>

   <tbody id="body">
   </tbody>
 </table></td>
   </tr>
   <tr><TD colspan="4">
    <table>
      <tr>
         <td colspan="4" align="right" class="normalNegrita">&nbsp;</td>
      </tr>
      <tr>
        <td class="normalNegrita">N&deg; Compromiso:</td>
        <td align="left" class="normalNegro"><?php echo $_POST['comp_id'];?></td>
      </tr>
      <tr>
        <td class="normalNegrita">Fuente de financiamiento:</td>
        <td align="left" class="normalNegro"><div align="left"><?php
          $sql="SELECT * FROM sai_seleccionar_campo('sai_fuente_fin','fuef_descripcion','fuef_id='||'''$reserva''','',2) resultado_set(fuef_descripcion varchar)"; 
		  $resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
		  if($row=pg_fetch_array($resultado_set_most_p))
		  {
 			$fuente=trim($row['fuef_descripcion']); //Solicitante
		  }
	      echo $fuente;
?></div></td>
  </tr>
</table></TD></tr>
  <tr>
	<td height="46" colspan="4">
     <div align="center"> <a href="codi_pdf.php?cod_doc=<?php echo(trim($codigo)); ?>">
     <img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a></div>      
     </td>
  </tr>
</table>
</form>
</body>
</html>
