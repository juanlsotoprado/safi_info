<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<link type="text/css" href="../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
<script type="text/javascript" src="../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
	g_Calendar.setDateFormat('dd/mm/yyyy');
</script>
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<script>
function comparar_fechas(fecha,fecha2) 
{
	var xMes=fecha.substring(3, 5);
	var xDia=fecha.substring(0, 2);
	var xAnio=fecha.substring(6,10);
	var yMes=fecha2.substring(3, 5);
	var yDia=fecha2.substring(0, 2);
	var yAnio=fecha2.substring(6,10);
	if (xAnio > yAnio){
	 return(true);
	}else{
	 if (xAnio == yAnio){
	  if (xMes > yMes){
	   return(true);
	  }
	  if (xMes == yMes){
	   if (xDia > yDia){
	    return(true);
	   }else{
	     return(false);
	        }
	  }else{
	   return(false);
	   }
	}else{
	 return(false);
	 }
	}
}


function enviar(tipo){
 document.form.opcion.value=tipo;
 if (tipo != 5)
 {
	 if ((tipo == 99) || (tipo == 25)){
	  if (document.form.nombre.value==""){
       alert("Debe indicar el nombre de la persona que retir\u00F3 los activos del galp\u00F3n");
       document.form.nombre.focus();
       return;
	  }

	  if (document.form.cedula.value==""){
	       alert("Debe especificar la c\u00E9dula de la persona que retir\u00F3 los activos del galp\u00F3n");
	       document.form.cedula.focus();
	       return;
		  }
	  if (document.form.fecha.value==""){
	       alert("Debe especificar la fecha en que se retiraron los activos del galp\u00F3n");
	       document.form.fecha.focus();
	       return;
		  }
     }
	 
   document.form.submit();	
 }
 else
	{

	contenido=prompt("Indique el motivo de la anulaci\u00F3n: ","");
	document.getElementById('contenido_memo').value=contenido;
	/*contenido=prompt("Indique el motivo de la devoluci\u00F3n: ","");
		 while (contenido==null){
		  contenido=prompt("Debe especificar el motivo de la devoluci\u00F3n: ","");
		 }
		 if (contenido!=null){
 		   document.getElementById('contenido_memo').value=contenido;
 		   document.form1.submit();
		}*/
	}
 document.form.submit();
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<?
$hoy = date("d/m/Y"); 
$codigo=$_REQUEST['codigo'];
if (substr($codigo,0,1)=='a'){//ASIGNACION
  $sql_acta="SELECT to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,t1.esta_id,t1.usua_login as genera,asbi_destino,
  case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
  FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_empleado
  WHERE t1.asbi_id='".$codigo."' and 
  t1.asbi_id=t2.asbi_id and empl_cedula=t1.usua_login" ;
}else{//REASIGNACION
  $sql_acta="SELECT to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,t1.esta_id,t1.usua_login as genera,destino as asbi_destino,
  case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
  FROM sai_bien_reasignar t1,sai_bien_reasignar_item t2,sai_empleado
  WHERE t1.acta_id='".$codigo."' and 
  t1.acta_id=t2.acta_id and empl_cedula=t1.usua_login" ;
}
  $resultado_acta=pg_query($conexion,$sql_acta) or die("Error al consultar totales");
  if($row_acta=pg_fetch_array($resultado_acta)) 
  {
  }
  ?>
	
<body>

<form action="revision_salida_Accion.php" name="form" id="form" method="post" >
<input type="hidden" name="opcion"></input>
<input type="hidden" name="hoy" value="<?=$hoy;?>">
<input type="hidden" name="contenido_memo" id="contenido_memo">
<input type="hidden" name="usuario_genera" id="usuario_genera" value="<?php echo $row_acta['genera'];?>">
 <table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
   <tr>
      <td height="15" colspan="2" valign="midden" class="td_gray"><span class="normalNegroNegrita"> Revisar salida de activos </span> </td>
   </tr>
   	<tr>
	  <td class="normal"><strong>N&deg; acta</strong></td>
	  <td class="normalNegro"><b><?php echo ((substr($codigo,0,1)=='a') ? "s" : "r").substr($codigo,1) ?>
	  <input type="hidden" value="<?=$codigo; ?>" name="codigo"></input>
	  <?php if (substr($codigo,0,1)=='a'){?> 
	  <a target="_blank" class="normal" href="salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Consultar Detalle</a>
      <?php }else{?>
      <a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Consultar Detalle</a>
	  <?php }?>
    </div></b></td></tr>
	  	<tr>
	  <td class="normal"><strong>Fecha</strong></td>
	  <td class="normalNegro"><?php echo($row_acta['fecha_acta']); ?></td></tr>
   <tr>
	 <td class="normal"><strong>Dependencia solicitante</strong></td>
     <td class="normalNegro">
		<?php
		    $sql_str="SELECT * FROM sai_dependenci WHERE depe_id in (".$row_acta['solicitante'].")";
	        $res_q=pg_exec($sql_str) or die("Error al mostrar");	  
	   	    $i=0;
	        $depe_nombre='';
	   		while($depe_row=pg_fetch_array($res_q)){ 
		    if ($i==0)
		 	  $depe_nombre=$depe_row['depe_nombre'];
		    else
		 	  $depe_nombre=$depe_nombre.",".$depe_row['depe_nombre'];
	        
	   		$i++;
	   		}
             echo($depe_nombre); 
             ?>
	  </td></tr>


	<tr>
	  <td class="normal"><strong>Elaborado por</strong></td>
	  <td class="normalNegro"><?php echo($row_acta['empl_nombres']." ".$row_acta['empl_apellidos']); ?></td></tr>
	<tr>
	  <td class="normal"><strong>Infocentro</strong></td>
	  <td class="normalNegro"><?php echo($row_acta['infocentro'].":".$row_acta['info_nombre']); ?>
	  <input type="hidden" name="infocentro" value="<?=$row_acta['infocentro'];?>"></td></tr>
	  
	<tr>
	  <td class="normal" hight="10"><strong>Destino</strong></td>
	  <td class="normalNegro"><?php echo($row_acta['asbi_destino']); ?></td></tr>
	<tr>
	  <td class="normal" hight="10"><strong>Activos</strong></td>
	  <td class="normalNegro">
	  <?php 
	  if (substr($codigo,0,1)=='a'){//ASIGNACION
       $sql_salida="SELECT * FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_biin_items t3
       WHERE t1.asbi_id='".$codigo."' and t1.asbi_id=t2.asbi_id and t2.clave_bien=t3.clave_bien";
	  }else{
       $sql_salida="SELECT * FROM sai_bien_reasignar t1,sai_bien_reasignar_item t2,sai_biin_items t3
       WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and t2.clave_bien=t3.clave_bien";
	  }
       $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
	   while($rowd=pg_fetch_array($resultado_salida)) 
	   { 
        echo("Serial Bien Nacional: ".$rowd['etiqueta']." Serial Activo: ".$rowd['serial'])."<br>";  
	   }?>
	  </td></tr>
	<tr>
	  <td class="normal"><strong>Materiales</strong></td>
	  <td class="normalNegro">
	  <?php 
	  if (substr($codigo,0,1)=='a'){//ASIGNACION
       $sql_salida="SELECT * FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_item t3
       WHERE t1.asbi_id='".$codigo."' and t1.asbi_id=t2.asbi_id and id=arti_id";
	  }else{
       $sql_salida="SELECT * FROM sai_bien_reasignar t1,sai_bien_reasignar_item t2,sai_item t3
       WHERE t1.acta_id='".$codigo."' and t1.acta_id=t2.acta_id and id=arti_id";
	  }
       $resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
	   while($rowd=pg_fetch_array($resultado_salida)) 
	   { 
        echo($rowd['nombre']." : ".$rowd['cantidad'])."<br>";  
	   }?>
	   </td></tr>
	   
	 <?php if (($_SESSION['user_perfil_id']==PERFIL_ALMACENISTA  || $_SESSION['user_perfil_id']==PERFIL_ANALISTA_BIENES )
	 		&& ($_REQUEST['accion']=="Finalizar" || $_REQUEST['accion']=="Enviar")){?>
	   <tr>
	  <td class="normal"><strong>Nombre de la persona que retira/recibe</strong></td>
	  <td class="normalNegro"><input type="text" name="nombre" size="25" class="normalNegro"></input></td></tr>
	  <tr>
	  <td class="normal"><strong>C&eacute;dula de la persona que retira/recibe</strong></td>
	  <td class="normalNegro"><input type="text" name="cedula" size="10" maxlength="8" class="normalNegro"></input></td></tr>
	  <tr>
	  <td class="normal"><strong>Fecha de retiro/recibo</strong></td>
	  <td >  
      <input type="text" size="10" id="fecha" name="fecha" value="" class="normalNegro" onfocus="javascript: comparar_fechas(this);" readonly/>
      <a href="javascript:void(0);" onclick="g_Calendar.show(event, 'fecha');" title="Show popup calendar">
	   <img src="../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
	   </a>
	  </td>
	  </tr>
	  
	  <tr>
	  <td class="normal"><strong>Observaciones</strong></td>
	  <td class="normalNegro"><textarea colspan="40" name="observaciones"></textarea></td></tr>
	<?php }
	if ($_REQUEST['accion']=="Finalizar"){?>
	  <tr>
	  <td class="normal"><strong>N&deg; Archivo:</strong></td>
	  <td class="normalNegro"><input type="text" name="archivo" size="10" maxsize="10"></td></tr>
	<?php }?>
	   <tr><td height="30"></td></tr>
	   <tr>
	   <td colspan="2" align="center">
	   <?php if ($_SESSION['user_perfil_id']==PERFIL_ALMACENISTA ){?>
	   <input type="button" value="Enviar" class="normalNegro" onclick="enviar(25)">
	   <?php } if ($_SESSION['user_perfil_id']==PERFIL_ANALISTA_BIENES ){ ?>
	   <input type="button" value="<?=$_REQUEST['accion']; ?>" class="normalNegro" onclick="enviar(<?=$_REQUEST['tipo']; ?>)">
	   <?php } if ($_SESSION['user_perfil_id']==PERFIL_JEFE_BIENES  || $_SESSION['user_perfil_id']==PERFIL_COORDINADOR_BIENES){?>
	   <input type="button" value="Aprobar (Galp&oacute;n)" class="normalNegro" onclick="enviar(3)">
	   <input type="button" value="Aprobar para Finalizar (Torre)" class="normalNegro" onclick="enviar(325)"><!-- Aprobar y Enviar -->
	   <input type="button" value="Devolver" class="normalNegro" onclick="enviar(5)">
	   <?php }?>
	   </td>
	   </tr>
	    
</table>
  	
</form>

</body>
</html>

			
<?php pg_close($conexion);?>
