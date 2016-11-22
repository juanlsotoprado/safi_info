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
<script languaje="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script languaje="JavaScript" SRC="../../../includes/js/CalendarPopup.js"> </SCRIPT>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>


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
  if ((document.form1.txt_inicio.value=='') && (document.form1.hid_hasta_itin.value=='')  && (document.form1.infocentro.value==''))
  {
    document.form1.hid_buscar.value=1;
  }
   else{document.form1.hid_buscar.value=2;}
   document.form1.submit();
 // window.location="salidas.php?hid_buscar="+document.form1.hid_buscar.value+"&infocentro="+document.form1.infocentro.value
}

function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
{ 	
	var fecha_inicial=document.form1.txt_inicio.value;
	var fecha_final=document.form1.hid_hasta_itin.value;
		
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
	  document.form1.hid_hasta_itin.value='';
	  return;
	}
}


</script>

</head>
<body>
<form name="form1" action="salidas.php" method="post">
  <br />
  <table width="650" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray" > 
      <td height="15" colspan="3" valign="midden" class="normalNegroNegrita">Salida de Activos</td>
</tr>
<tr>
<td height="34" class="normalNegrita">Fecha de salida entre:</td>

<td class="normalNegrita">
<input name="txt_inicio" id="txt_inicio" type="text" size="10" class="normalNegro" value="" onclick="cal1xx3.select(this,'anchor1xx3','dd/MM/yyyy'); return false;" readonly />
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="-">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
<input NAME="hid_hasta_itin" ID="hid_hasta_itin" TYPE="text" size="10" class="normalNegro" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" onFocus="javascript: comparar_fechas(document.form1.txt_inicio,document.form1.hid_hasta_itin)" readonly> 
<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="-">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a>
</td>
</tr>

<tr>
<td height="34" class="normalNegrita">Infocentro:</td>

<td><input type="text" name="infocentro" id="infocentro"
			class="normalNegro" size="70" onChange="validarRif(this)"  autocomplete="off">
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
	$wheretipo4="";
	$wheretipo5="";
	$wheretipo6="";
	
    $vector=explode (":" , $_POST['infocentro']);
    $id_infocentro=trim($vector[0]);
    $nombre_infocentro=trim($vector[1]);
	$fecha_in=trim($_POST['txt_inicio']); 
	$fecha_fi=trim($_POST['hid_hasta_itin']); 
	$fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	$fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";

 if ($id_infocentro<>''){
   $wheretipo1=" and infocentro='".$id_infocentro."'";
   $msj="  al Infocentro ".$id_infocentro.":".$nombre_infocentro;
 }
 
 if (strlen($fecha_ini)>2){
   $wheretipo2 = " and asbi_fecha >= '".$fecha_ini."' and asbi_fecha <= '".$fecha_fin."' ";
   $msj= $msj."  del ".$fecha_in." al ".$fecha_fi;
 }
 $sql_ar="SELECT id,nombre,count(t3.id)as cantidad
  FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_item t3, sai_biin_items t4 
  WHERE t1.asbi_id=t2.asbi_id and t1.esta_id<>15 and t2.clave_bien=t4.clave_bien and bien_id=id ".$wheretipo1.$wheretipo2."
   group by t3.id,t3.nombre order by nombre";

$sql_acta="SELECT distinct(t1.asbi_id),to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,asbi_fecha,t2.clave_bien
  FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_item t3, sai_biin_items t4 
  WHERE t1.asbi_id=t2.asbi_id and t1.esta_id<>15 and t2.clave_bien=t4.clave_bien and bien_id=id ".$wheretipo1.$wheretipo2."
  order by asbi_fecha";
 $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de activos");  
if($row=pg_fetch_array($resultado_set_most_ar))
  {
?>
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
<div align="center" class="normalNegroNegrita">Salida de Activos 
    <?php
   
      echo $msj;
    
   ?>
	
    </div>
  <tr>
    <td height="11" colspan="5"></td>
  </tr>
  <tr>
    <td height="11" colspan="5"></td>
  </tr>
  <tr>
    <td colspan="5">
    <table width="335" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr class="td_gray">
        <td width="321"><div align="center" class="normalNegroNegrita">N&deg;</div></td>
        <td width="321"><div align="center" class="normalNegroNegrita">Actas Enviadas</div></td>
        <td width="78"><div align="center" class="normalNegroNegrita">Fecha Acta </div></td>
        </tr>
      <?php
   	   $i=1;
   	   $resultado_acta=pg_query($conexion,$sql_acta) or die("Error al consultar lista de articulos");  
	   while($row_acta=pg_fetch_array($resultado_acta)) 
	   {	

		 ?>
      <tr>
        <td bordercolor="1"><div align="center"><span class="normal"><?php echo $i;?> </span></div></td>
        <td bordercolor="1"><div align="left" class="normal">
        <a href="javascript:abrir_ventana('../salida_activos_pdf.php?codigo=<?php echo trim($row_acta['asbi_id']); ?>&tipo=a')">
        <?php echo $row_acta['asbi_id'];?>    </a>

        </div></td>
        <td bordercolor="1"><div align="center"><span class="normal"><?php echo $row_acta['fecha_acta'];?> </span></div></td>
      </tr>
      <?php
        $actas_garantias="SELECT acta_id,to_char(fecha_registro,'DD/MM/YYYY') as fecha_acta FROM sai_bien_garantia 
        WHERE esta_id=35 AND accion_tomada='REEMPLAZADO' AND clave_bien='".$row_acta['clave_bien']."'";
        if (strlen($fecha_ini)>2){
        	$actas_garantias.=" and fecha_cierre >= '".$fecha_ini."' and fecha_cierre <= '".$fecha_fin."' ";
        }
        $resultadog=pg_query($conexion,$actas_garantias);
        if ($rowg=pg_fetch_array($resultadog)){
         $i++;?>
      <tr>
        <td bordercolor="1"><div align="center"><span class="normal"><?php echo $i;?> </span></div></td>
        <td bordercolor="1"><div align="left" class="normal">
        <a href="javascript:abrir_ventana('../garantia/garantia_pdf.php?codigo=<?php echo trim($rowg['acta_id']); ?>')">
        <?php echo $rowg['acta_id'];?>    </a>
        </div></td>
        <td bordercolor="1"><div align="center"><span class="normal"><?php echo $rowg['fecha_acta'];?> </span></div></td>
      </tr>        
         <?php 
        }
      $i++;
       }
          
   	       ?>
    </table><br><br>
    
    <table width="335" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr class="td_gray">
        <td width="321"><div align="center" class="normalNegroNegrita">Activo</div></td>
        <td width="78"><div align="center" class="normalNegroNegrita">Cantidad Enviada </div></td>
        </tr>
      <?php
   	   $i=1;
       $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
	   while($row=pg_fetch_array($resultado_set_most_ar)) 
	   {
	   	$desincorporados=0;	
        $activos_garantias="SELECT * FROM sai_bien_garantia t1, sai_biin_items t2
        WHERE t1.esta_id=35 AND accion_tomada='REEMPLAZADO' AND t1.clave_bien=t2.clave_bien AND bien_id='".$row['id']."'";
        if (strlen($fecha_ini)>2){
        	$activos_garantias .= " and fecha_cierre >= '".$fecha_ini."' and fecha_cierre <= '".$fecha_fin."' ";
        }
//echo $activos_garantias;
        $resultadog=pg_query($conexion,$activos_garantias);
        while ($rowg=pg_fetch_array($resultadog)){
        	$desincorporados++;
        }
		 ?>
      <tr>
        <td bordercolor="1" <?php echo $fondo_str;?>><div align="left" class="normal">
        <!-- <a href="detalle_activos.php?desc=<?php echo $row['nombre'];?>&id=<?php echo $row['id'];?>&fecha=<?php echo $fecha_fin;?>">         </a>-->
        <?php echo strtoupper($row['nombre']);?>

        </div></td>
        <td bordercolor="1" <?php echo $fondo_str;?>><div align="center"><span class="normal"><?php echo $row['cantidad']-$desincorporados;?> </span></div></td>
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
<?php } else{?>
	<div align="center" class="normalNegrita"><?php echo utf8_decode("No se consiguió resultados para ese criterio de búsqueda");?></div>
	
<?php }?>
</form>


	
	
<?php }?>











</body>
</html>
