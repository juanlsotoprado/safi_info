<?php
ob_start();
require("../../../includes/conexion.php");
require("../../../includes/perfiles/constantesPerfiles.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}

   $bandeja_entrada="select t1.acta_id,to_char(fecha_notificacion,'DD/MM/YYYY') as fecha_acta,solicitante,serial
	from sai_bien_garantia t1,sai_biin_items t3
	where t1.esta_id=10 and t1.clave_bien=t3.clave_bien order by fecha_registro";
	$accion="Reportar"; 	 
	$accion2="Anular";
	 
	$bandeja_transito="select t1.acta_id,to_char(fecha_reporte,'DD/MM/YYYY') as fecha_acta,nro_caso,serial,sbn,modelo,t1.clave_bien
	from sai_bien_garantia t1,sai_biin_items t2
	where t1.esta_id=61 and t1.clave_bien=t2.clave_bien 
	order by fecha_reporte";
	$accion_transito="Seguimiento";
	$accion_transito2="Cerrar";
	
	$resultado_transito=pg_query($conexion,$bandeja_transito) or die("Error al Mostrar Lista de Documentos Transito");
    $total_transito=pg_num_rows($resultado_transito);
	
	 $resultado_entrada=pg_query($conexion,$bandeja_entrada) or die("Error al Mostrar Lista de Documentos Entrada");
     $total_entrada=pg_num_rows($resultado_entrada);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../../js/funciones.js"> </script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
	 <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center">
		<table width="326" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" >
          <tr>
            <td colspan="4"></td>
            </tr>
			  <tr>
				<td width="20"></td>
				<td width="10">&nbsp;</td>
				<td width="200" class="normalNegroNegrita"><div align="left">
                        Documentos en bandeja:
                      </div></td>
				<td width="96" class="normalNegroNegrita"><div align="center"><?php echo $total_entrada; ?></div></td>
			  </tr>
			   <tr>
				<td width="20"></td>
				<td width="10">&nbsp;</td>
				<td width="200" class="normalNegroNegrita"><div align="left">Documentos en tr&aacute;nsito:</div></td>
				<td width="96" class="normalNegroNegrita"><div align="center"><?php echo $total_transito; ?></div></td>
			  </tr>	<?php //}?>
       </table>
   </td>
   
   
        <td width="50%">
		 <table width="50%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="30">&nbsp;</td>
            <td width="170">&nbsp;</td>
          </tr>
          <tr>
            <td class="GrandeNeg" colspan="2" align="right">
            <?php 
            echo utf8_decode("Actas de Garantía");?></td>
          </tr>
        </table>
		</td>
      </tr>
     </table>
	</td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center" class="normalNegroNegrita">Bandeja de entrada </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <table width="100%" height="25" border="0" cellpadding="4" cellspacing="2" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
         <?php
         $resultado_transito=pg_query($conexion,$bandeja_entrada) or die("Error al Mostrar Lista de Documentos Devueltos");
         $total_transito=pg_num_rows($resultado_transito);
		 if ($total_transito>0) {
		 ?>
		  <tr class="td_gray" align="center">
            <td class="normalNegroNegrita">Acta</td>
            <td class="normalNegroNegrita">Fecha de notificaci&oacute;n</td>
			<td class="normalNegroNegrita">Solicitante</td>
			<td class="normalNegroNegrita">Serial activo</td>
 	   		<td class="normalNegroNegrita">Opciones</td>
          </tr>	  
	      <?php while($row_doc_bandeja=pg_fetch_array($resultado_transito)){?>
          <tr class="normal">
            <td align="left" class="link">			
			<a title="<? echo $detalle; ?>" class="link"><?php echo $row_doc_bandeja['acta_id']; ?></a>
			</td>
            <td align="center"><?php echo $row_doc_bandeja['fecha_acta'];?></td>
            <td><?php echo $row_doc_bandeja['solicitante'];?></td>
            <td><?php echo $row_doc_bandeja['serial'];?></td>      
 			<td align="center">
            <a href="ingresar_reporte.php?codigo=<? echo trim($row_doc_bandeja['acta_id']);  ?>&serial=<? echo $row_doc_bandeja['serial']; ?>" class="copyright"><?php echo($accion);?></a>
            <a href="anular_caso.php?codigo=<? echo trim($row_doc_bandeja['acta_id']);  ?>&serial=<? echo $row_doc_bandeja['serial']; ?>" class="copyright"><?php echo($accion2);?></a></td>
          </tr>
		 <?php
		    } //END WHILE
		   }
		   else  {
		 ?> 
		  <tr>
            <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
          </tr>
		  <?php }  
		   ?>
        </table>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center" class="normalNegroNegrita"> Bandeja en tr&aacute;nsito </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <table width="100%" height="25" border="0" cellpadding="4" cellspacing="2" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
         <?php
         $resultado_transito=pg_query($conexion,$bandeja_transito) or die("Error al Mostrar Lista de Documentos en transito");
         $total_transito=pg_num_rows($resultado_transito);
		 if ($total_transito>0) {
		 ?>
		  <tr class="td_gray" align="center">
            <td class="normalNegroNegrita">Acta</td>
            <td class="normalNegroNegrita">Fecha de reporte</td>
			<td class="normalNegroNegrita">N&deg; de ticket</td>
            <td class="normalNegroNegrita">Serial activo</td>			
 	   		<td class="normalNegroNegrita">Opciones</td>
          </tr>	  
	      <?php while($row_doc_bandeja=pg_fetch_array($resultado_transito)){?>
          <tr class="normal">
            <td align="left" class="link">			
			<a title="<? echo $detalle; ?>" class="link"><?php echo $row_doc_bandeja['acta_id']; ?></a>
			</td>
            <td align="center"><?php echo $row_doc_bandeja['fecha_acta'];?></td>
            <td><?php echo $row_doc_bandeja['nro_caso'];?></td>
            <td><?php echo $row_doc_bandeja['serial'];?></td>            
            <td align="center">
            <a href="javascript:abrir_ventana('nota_salida.php?codigo=<?php echo trim($row_doc_bandeja['acta_id']);?>&accion=iniciar')" class="copyright">Nota Salida T&eacute;cnico</a>
            <a href="seguimiento_caso.php?codigo=<? echo trim($row_doc_bandeja['acta_id']);?>&serial=<?php echo $row_doc_bandeja['serial'];?>&ticket=<?php echo $row_doc_bandeja['nro_caso'];?>" class="copyright"><? echo($accion_transito);?></a>
            <a href="cerrar_caso.php?codigo=<? echo trim($row_doc_bandeja['acta_id']);  ?>&serial=<?php echo $row_doc_bandeja['serial'];?>&ticket=<?php echo $row_doc_bandeja['nro_caso'];?>&sbn=<?php echo $row_doc_bandeja['sbn'];?>&modelo=<?php echo $row_doc_bandeja['modelo'];?>&clave=<?php echo $row_doc_bandeja['clave_bien'];?>" class="copyright"><? echo($accion_transito2);?></a></td>
          </tr>
		 <?php
		    } //END WHILE
		   }
		   else  {
		 ?> 
		  <tr>
            <td height="40" colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
          </tr>
		  <?php }  
		   ?>
        </table>
    </div></td>
  </tr>
</table>
</body>
</html>
<?php pg_close($conexion);?>