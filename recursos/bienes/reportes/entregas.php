<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 
		   exit;
	  }
	ob_end_flush(); 
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Inventario de Materiales Existente</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../includes/js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>


<script language="javascript">
function validarRif(rif){
	var encuentra=0;
	for(j= 0; j < arreglo_rif.length; j++){
		if(rif==arreglo_rif[j]){
			return true;
			encuentra=1;
		}
	}
	/*if (encuentra==0){
	//return false;
	alert("Este RIF indicado no es v"+aACUTE+"lido");
	document.form1.rif_sugerido.focus();
			
	}*/
}

function detalle(codigo,nombre)
{
    url="alma_rep_e1.php?codigo="+codigo+"&nombre="+nombre
	newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function ejecutar()
{
  if ((document.form1.txt_cod.value=='') && (document.form1.infocentro.value=='') && (document.form1.opt_depe.value=='0'))
  {
    document.form1.hid_buscar.value=1;
  }
   else{document.form1.hid_buscar.value=2;}
   document.form1.submit();
 // window.location="salidas.php?hid_buscar="+document.form1.hid_buscar.value+"&infocentro="+document.form1.infocentro.value
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
	  alert("La fecha inicial no debe se mayor a la fecha final"); 
	  document.form.hid_hasta_itin.value='';
	  return;
	}
}

</script>

</head>
<body>
<form name="form1" action="entregas.php" method="post">
<br />
<table width="700" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray" > 
    <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">Recepci&oacute;n</td>
  </tr>
  <tr>
    <td height="34" class="normalNegrita">N&deg; del Acta:</td>
    <td class="normalNegrita">
    <input name="txt_cod" type="text" class="peq" id="txt_cod" value="" size="10"></td>
  </tr>
  <tr><td width="259" height="34" class="normalNegrita">Dependencia:</td><td>
  <select name="opt_depe" class="normalNegro" id="opt_depe">
	     <option value="0">[Seleccione]</option>
		<?php
	    $sql_str="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_nivel=4 or depe_id=150','',2) resultado_set (depe_id varchar, depe_nombre varchar)";	
	    $res_q=pg_exec($sql_str) or die("Error al mostrar");	  
	   $i=0;
	   while($depe_row=pg_fetch_array($res_q)){ 
 		 $depe_id=$depe_row['depe_id'];
		 $depe_nombre=$depe_row['depe_nombre'];
?>
             <option value="<?php echo(trim($depe_row['depe_id'])); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
             <?php }?>
           </select>
</td></tr>
  <tr>
	<td height="34" class="normalNegrita">Infocentro:</td>
	<td>
	<input type="text" name="infocentro" id="infocentro" class="normalNegro" size="70" onChange="validarRif(this)">
		<?php 	
			
			$query = "SELECT nemotecnico,t1.nombre,t2.nombre as n_edo,direccion ".
				 "FROM ".
				 "safi_infocentro t1,safi_estatus_general t2 ".
				 "WHERE t2.id=id_estatus_general ".
				 "ORDER BY nombre";
			
			$resultado = pg_exec($conexion, $query);
			$numeroFilas = pg_num_rows($resultado);
			$arregloProveedores = "";
			$cedulasProveedores = "";
			$nombresProveedores = "";
			$indice=0;
			while($row=pg_fetch_array($resultado)){
				$arregloProveedores .= "'".$row["nemotecnico"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."(".$row["n_edo"].")"."',";
				$cedulasProveedores .= "'".$row["nemotecnico"]."',";
				$dirInfo .= "'".$row["direccion"]."',";
				$indice++;
			}
			$arregloProveedores = substr($arregloProveedores, 0, -1);
			$cedulasProveedores = substr($cedulasProveedores, 0, -1);
			$dirInfo = substr($dirInfo, 0, -1);
			?> <script>
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					
					this.actb_delimiter = new Array(' ',',');
					obj = new actb(document.getElementById('infocentro'),proveedor);
					//obj11= new actb(document.getElementById('dir_info'),dir_info);
					
					//actb(document.getElementById('infocentro'),proveedor);
				</script></td>
</tr>
<tr>
<td height="52" colspan="3" align="center">
<input type="hidden" name="hid_buscar" id="hid_buscar" value="0">
<input type="button" class="normalNegro" value="Buscar" onclick="javascript:ejecutar()">
  
    
</td>
</tr>
</table>
</form>
<br>
<?php 
if ($_POST['hid_buscar']==1){?>
	<div align="center" class="normalNegrita"><?php echo ("Debe especificar alguna opci&oacute;n de b&uacute;squeda");?></div>
<?php }
if ($_POST['hid_buscar']==2){?>
<form name="form" action="" method="post">
<?php 

    $wheretipo1="";
	$wheretipo2="";
	$wheretipo3="";
	
    $vector=explode (":" , $_POST['infocentro']);
    $id_infocentro=trim($vector[0]);
    $nombre_infocentro=trim($vector[1]);
	$codigo=trim($_POST['txt_cod']); 
	$dependencia=$_POST['opt_depe'];

 if ($id_infocentro<>''){
   $wheretipo1=" and t1.infocentro='".$id_infocentro."'";
   $msj="  AL INFOCENTRO ".$id_infocentro.":".$nombre_infocentro;
 }
 
 if ($dependencia<>'0'){
   $wheretipo3=" and t1.solicitante='".$dependencia."'";
   $sql_depe="SELECT * FROM sai_seleccionar_campo('sai_dependenci','depe_id,depe_nombre','depe_id=''$dependencia''','',2) resultado_set (depe_id varchar, depe_nombre varchar)";									   
   $res_q=pg_exec($sql_depe) or die("Error al mostrar");	  
	 if($depe_row=pg_fetch_array($res_q)){ 
	  $depe_nombre=$depe_row['depe_nombre'];}
   $msj="  A LA DEPENDENCIA DE ".$dependencia.":".$depe_nombre;
   
   
 }
 
 if (strlen($codigo)>5){
   $wheretipo2 = " and t1.asbi_id = '".$codigo."'  ";
   $msj= $msj."  EN EL ACTA ".$codigo;
 }
 
 $sql_ar="select t1.asbi_id,to_char(t3.fecha,'DD/MM/YYYY') as fecha_acta,t1.infocentro,t3.nombre,t3.cedula,t3.observaciones from
sai_bien_asbi t1, sai_revisiones_doc t2, sai_revisiones_detalle t3
where t1.asbi_id=t2.revi_doc and t1.esta_id<>15 and t2.revi_id=t3.revi_id and wfop_id=99 ".$wheretipo1.$wheretipo2.$wheretipo3;
 
 $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de activos");  
 if (($rowt1=pg_fetch_array($resultado_set_most_ar)) == null)
 {?><center>
  <span color="#003399" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</span>
</center><?php 
 }
	$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de activos");   
if($row=pg_fetch_array($resultado_set_most_ar))
  {
?>
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<div align="center" class="normalNegroNegrita">ACTIVOS ENVIADOS 
    <?php
   
      echo $msj;
    
   ?>

    </div>
   <tr>
    <td colspan="5"><table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr class="td_gray">
        <td><div align="center" class="normalNegroNegrita">N&deg; Acta</div></td>
        <td><div align="center" class="normalNegroNegrita">Fecha de entrega </div></td>
        <td><div align="center" class="normalNegroNegrita">Infocentro</div></td>
        <td><div align="center" class="normalNegroNegrita">Nombre de la persona quien recibe</div></td>
        <td><div align="center" class="normalNegroNegrita">C&eacute;dula de la persona quien recibe</div></td>
        <td><div align="center" class="normalNegroNegrita">Observaciones</div></td>
        </tr>
      <?php
   	   $i=1;
       $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
	   while($row=pg_fetch_array($resultado_set_most_ar)) 
	   {	
		 ?>
      <tr>
        <td bordercolor="1" height="20" width="30"><div align="center" class="normal">
       <a href="../salida_activos_pdf.php?tipo=a&codigo=<?php echo $row['asbi_id'];?>"><?php echo $row['asbi_id'];?></a></div></td>
        <td bordercolor="1" width="20"><div align="center"><span class="normal"><?php echo $row['fecha_acta'];?> </span></div></td>
        <td bordercolor="1" width="10"><div align="center"><span class="normal"><?php echo $row['infocentro'];?> </span></div></td>
        <td bordercolor="1" width="80"><div align="center"><span class="normal"><?php echo $row['nombre'];?> </span></div></td>
        <td bordercolor="1" width="20"><div align="center"><span class="normal"><?php echo $row['cedula'];?> </span></div></td>
        <td bordercolor="1" width="80"><div align="center"><span class="normal"><?php echo $row['observaciones'];?> </span></div></td>
      </tr>
      <?php
      $i++;
       }     
   	       ?>
    </table></td>
  </tr>
  <?php //}?>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado  el d&iacute;a 
              <?=date("d/m/y")?>
 a las
<?=date("H:i:s")?>
<br />
<br />
<br />
<!-- <a href="javascript:window.print()"><img src="../../imagenes/bimprimir_off.gif" width="100" height="27"></a> 
<span class="link"><a href="javascript:detalle('<?=$codigo_part?>','<?=$nombre_part?>')" class="link">Imprimir Documento</a></span> </span><br />-->
<span class="link">Imprimir Documento</span> </span><br />
            <br />
        <a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br />
      <br />
     
      <br />
    </div></td>
  </tr>
</table>
<?php } 
?>
</form>

<?php }?>

</body>
</html>
