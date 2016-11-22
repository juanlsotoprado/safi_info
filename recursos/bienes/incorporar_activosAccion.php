<?
ob_start();
require_once("../../includes/conexion.php");
require_once("../../includes/fechas.php");
	  
 if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
 {
   header('Location:../../index.php',false);
   ob_end_flush(); 
   exit;
 }
	
  ob_end_flush(); 

function completar_digitos($cadena)
{
 for ($tamano= strlen($cadena); $tamano<7; $tamano++)
 { 
  $cadena = "0".$cadena;
 }
 return $cadena;
}

  $listado_bienes=trim($_POST['txt_arreglo_bienes_head']);
  $longitud_arreglo=0;
  $vector=explode ("�" , $listado_bienes);
  $elem_vector=count($vector); 
  $tt_bienes= ($elem_vector/13);
  $longitud_arreglo=$longitud_arreglo+$elem_vector;
  $j=0;
  $i=-1;
  
for($l=0;$l<$tt_bienes;$l++)
{  	
	$matriz_bien_id[$l]=$vector[++$i];
	$matriz_nombre_bien[$l]=$vector[++$i];
	$matriz_etiquetas[$l]=$vector[++$i];  
	$matriz_marca_id[$l]=$vector[++$i];
	$matriz_nombre_marca[$l]=$vector[++$i];
	$matriz_model_id[$l]=$vector[++$i];
	$matriz_serial[$l]=$vector[++$i];
	$matriz_valor[$l]=$vector[++$i];
    $matriz_descrip[$l]="ninguna";
    
	$matriz_ubica[$l]=$vector[++$i];
	$matriz_nombre_ubicacion[$l]=$vector[++$i];
	$matriz_fecha_ing[$l]=cambia_ing($vector[++$i]);
	$matriz_garantia[$l]=$vector[++$i];
	$o=$vector[++$i];

	if ($o==" ")
	$matriz_observa[$l]="ninguna";
	else
	$matriz_observa[$l]=$o;
    $j++;

}

require_once("../../includes/arreglos_pg.php");
$arreglo_bien_id = convierte_arreglo($matriz_bien_id);
$arreglo_etiquetas = convierte_arreglo($matriz_etiquetas);
$arreglo_marca_id = convierte_arreglo($matriz_marca_id);
$arreglo_model_id = convierte_arreglo($matriz_model_id);
$arreglo_serial = convierte_arreglo($matriz_serial);
$arreglo_descrip = convierte_arreglo($matriz_descrip);
$arreglo_ubica = convierte_arreglo($matriz_ubica);
$arreglo_valor = convierte_arreglo($matriz_valor);
$arreglo_garantia = convierte_arreglo($matriz_garantia);
$arreglo_fecha_ing = convierte_arreglo($matriz_fecha_ing);
$arreglo_observa = convierte_arreglo($matriz_observa);
$arreglo_det_ubica = convierte_arreglo($matriz_detalle_ubi);
$valido=false;

  $nombre_proveedor=$_POST['nombre'];
  $sql_rif="SELECT * FROM sai_seleccionar_campo('sai_proveedor_nuevo','prov_id_rif','prov_nombre='||'''$nombre_proveedor''','',2) resultado_set (prov_id_rif varchar)";
  $resul_rif=pg_query($conexion,$sql_rif);
  if($rowa=pg_fetch_array($resul_rif)){
	$id_proveedor=trim($rowa['prov_id_rif']);       	
  }	

$sql="	Select * From
		sai_insert_inco_bienes(
			'".$_SESSION['user_depe_id']."',
			'".$_SESSION['login']."',
			 '".$arreglo_descrip."',
			 '".$_POST['cmb_tipo_inc']."',
			 '".$arreglo_etiquetas."', 
			 '".$arreglo_bien_id."',
			 '".$arreglo_ubica."',
			 '".$arreglo_marca_id."',
			 '".$arreglo_model_id."',
			 '".$arreglo_serial."',
			 '".$arreglo_valor."',
			 '".$arreglo_observa."',
			 '".$arreglo_garantia."',
			 '".$arreglo_fecha_ing."',
			 '".$_POST['opt_depe']."','".$_POST['num_doc']."',
			 '".$_POST['txt_pcta']."','".$id_proveedor."'
			 )
	";
//echo "<br>".$sql;
$resultado_set = pg_exec($conexion ,$sql) or die(utf8_decode("Error al ingresar los activos, enviar esta informacion a Sistemas: <br><br>".$sql));

	$row = pg_fetch_array($resultado_set,0); 
	
	if ($row[0] <> null)
	{
		$codigo_inco=$row[0];
		$valido=true;
		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript" SRC="../../includes/js/CalendarPopup.js"> </SCRIPT>
<script LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?if ($valido){?>
<form action="" name="form1" id="form1" method="post">
  <table width="820" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
    <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"> <span class="normalNegroNegrita">Registro de activos</span> </td>
    </tr>
    <tr>
      <td width="122">
        <div align="right" class="normal"><strong> Tipo de incorporacion:</strong>
           
      </div></td>
	  <td>
	  	<div align="left" class="normal">
				<?
					$sql="
						SELECT binc_id,binc_nombre
						FROM sai_binc_tipo
						WHERE esta_id=1 AND binc_id=".$_POST['cmb_tipo_inc']."";
					
					$resultado=pg_exec($conexion,$sql);
					if($row=pg_fetch_array($resultado))
					{
						$binc_id=$row['binc_id'];
						$binc_nombre=$row['binc_nombre'];
						
					}
					echo $binc_nombre;
					?>
			
		</div>
	  </td>
    </tr>
    <tr>
      <td width="122">
        <div align="right" class="normal"><strong> No. Acta:</strong>
           
      </div></td>
	  <td>
	  	<div align="left" class="normal"><?php echo $codigo_inco;?></div>
	  </td>
    </tr>
    
    <tr>
      <td colspan="2">
        <table width="540" align="center" background="/imagenes/fondo_tabla.gif">
          <tr>
            <td> <div align="center" class="normal"><strong> Nombre del activo</strong></div></td>
              <td>
              <div align="center" class="normal"><strong> N&deg; Bien Nacional </strong></div></td>
	         <td>
              <div align="center" class="normal"><strong> Marca</strong></div></td>
            <td>
              <div align="center" class="normal"><strong> Modelo</strong></div></td>
            <td>
              <div align="center" class="normal"><strong> Serial </strong></div></td>
	    <td>
              <div align="center" class="normal"><strong> Valor unitario </strong></div></td>
 	    <td>
              <div align="center" class="normal"><strong> Ubicaci&oacute;n </strong></div></td>
		<td>  
		  <div align="center" class="normal"><strong> Fecha de ingreso </strong></div>
			</td>
			<td>  
			  <div align="center" class="normal"><strong> Garant&iacute;a </strong></div>
			</td>
 		<td>
              <div align="center" class="normal"><strong> Observaciones </strong></div></td>
          </tr>
          <?
	
		$i=1;
  		for($j=0;$j<$tt_bienes;$j++)
		{
?>
          <tr>
            <td>
              	<div align="center" class="normal">
			<input type="text" class="normalNegro" size="10" value="<? echo $matriz_nombre_bien[$j];?>" disabled>
            	</div>
			</td>
            <td>
              <div align="center" class="normal">
                <input type="text" class="normalNegro" size="7" value="<? echo $matriz_etiquetas[$j]; ?>" disabled>
            </div></td>
            <td>
              <div align="center" class="normal"><input type="text" size="10" class="normalNegro"  value="<? echo $matriz_nombre_marca[$j]; ?>" disabled>
            </div></td>
            <td>
              <div align="center" class="normal">
		     <input class="normalNegro" size="8" value="<? echo $matriz_model_id[$j]; ?>" disabled> 
            </div></td>
            <td>
              <div align="center" class="normal">
                <input type="text" class="normalNegro" size="8" value="<? echo $matriz_serial[$j]; ?>" disabled>
            </div></td>
            <td>
              <div align="center" class="normal">
                <input type="text" class="normalNegro" size="8" value="<?echo $matriz_valor[$j]; ?>" disabled>
            </div></td>
            <td><?$i++;?>
              <div align="center" class="normal">
                <input type="text" class="normalNegro" size="6" value="<? echo $matriz_nombre_ubicacion[$j]; ?>" disabled>
            </div></td>

		<td>  
		<div align="center" class="normal"><strong> 
		<input size="10" class="normalNegro" value="<? echo $matriz_fecha_ing[$j]; ?>" disabled></strong></div>
		</td>
		<td>  
		  <input type="text" size="8" class="normalNegro" value="<? echo $matriz_garantia[$j]." Meses"; ?>" disabled>
		</td>
            <td>
              <div align="center" class="normal">
                <input type="text" class="normalNegro" size="10" value="<? echo $matriz_observa[$j]; ?>" disabled>
		<?$i=$i+6;?>
		
            </div></td>
          </tr>
          <?}?>
      </table></td>
    </tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<div align="center"> <a href="inco_pdf.php?codigo=<?php echo(trim($codigo_inco)); ?>">
                 <img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a><br><span class="normalNegro">Imprimir Acta</span></div>
			</div>		

		</td>
	</tr>
  </table>
<?}
   if ($valido==false)	
    {?> <br><br><br>
   <table width="76%" border="0" align="center"  class="tablaalertas">
    <tr>
       <td height="18" colspan="3" class="normal"><div align="center">
       <img src="imagenes/mano_bad.gif" width="31" height="38">
		 <br><br>
       <img src="imagenes/vineta_azul.gif" width="11" height="7" />No se puede efectuar el registro de los activos, la cantidad de seriales no concuerda con la cantidad de activos especificados.
    	</tr>
    <tr><tr><TD height="10"></TD>
    </tr>
  </table>
<?php }?>

</form>
</body>
</html>
