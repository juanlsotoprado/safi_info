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


  	$perfil = $_SESSION['user_perfil_id'];
        //Verificar si el usuario tiene permiso para el objeto (accion) actual
	$sql = " SELECT * FROM sai_permiso_reporte('repo_rete','$perfil') as resultado ";
    
	$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
	if ($row = pg_fetch_array($resultado)) {
		$tiene_permiso = $row["resultado"];
	}

	if ($tiene_permiso == 0) {
		//Enviar mensaje de error
		?>
		<script>
		document.location.href = "../../../mensaje.php?pag=principal.php";
		</script>
		<?
		}
 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
<script type="text/javascript">
		g_Calendar.setDateFormat('dd/mm/yyyy');
	</script>
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>

<script LANGUAGE="JavaScript">
<!--


function validar(){

     if(trim(document.form.txt_fecha_f.value)=='' || trim(document.form.txt_fecha_i.value)==''){
	  alert(" Debe indicar el intervalo de fechas !!!. ");
	  return;
     }   
	 
        document.form.action="seniat.php";
	document.form.submit();
	 
}



////////////////////////////
function verifica_fechas(fecha_inicial,fecha_final,actual){ 
  var op=false;
  var fecha_actual = document.getElementById(actual.id).value;
  if(fecha_actual.value!=""){
	var arreglo_f_desde = fecha_actual.split("/");
	var desde = new Date(arreglo_f_desde[2]+"/"+arreglo_f_desde[1]+"/"+arreglo_f_desde[0]);
	var hoy = new Date("<?php echo(date('Y/m/d')); ?>");
	if(desde.getTime() > hoy.getTime()){
	  alert("La Fecha no Puede ser Mayor a "+ "<?php echo(date('d/m/Y')); ?>");
	  document.getElementById(actual.id).value="";
	  return;
	}
  }
	
  if ( fecha_inicial.length=="" || fecha_final.length==""){
	return
  }	
  op=comparar_fechas(fecha_inicial,fecha_final);

  if (!op){
	alert("El rango de la fecha no es valida");
	document.getElementById(actual.id).value="";
	return
  }

}
////////////////////////////

//-->
</SCRIPT>
</head>

<body onLoad="MM_preloadImages('../../../imagenes/boton_generar_blk.gif');">
<form name="form" method="post" action="" target="_blank">
  <table width="610" border="0" align="center">
    
    <tr>
      <td><table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
          <tr>
            <td colspan="4" class="td_gray"><div class="normalNegroNegrita">Condiciones</div></td>
          </tr>
           <tr>
            <td><div align="center">
                
            </div></td>
            <td colspan="2"><div align="right" class="normalNegro">
                <div align="center">Fecha </div>
            </div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center"> </div></td>
            <td><div align="right" class="normalNegro">Desde: </div></td>
             <td width="50%" class="peq">
				    <input name="txt_fecha_i" type="text" class="normal"  value="" size="10" maxlength="10" readonly="true"  id= "txt_fecha_i">
                               
				<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fecha_i');" title="Show popup calendar">
				<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
				</a>
				
						  
			  </td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center"> </div></td>
            <td><div align="right" class="normalNegro">Hasta: </div></td>
          <td width="50%" class="peq">
     			 
			<input name="txt_fecha_f" TYPE="text"  class="normal"  value="" size="10" maxlength="10" readonly="true" id= "txt_fecha_f">
			<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_fecha_f');" title="Show popup calendar">
<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
</a> 

			 </td>
            <td>&nbsp;</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><div align="center">
	  
	  <input type="button" value="Generar" onclick="javascript:validar()">
	  </div></td>
	  
    </tr>
  </table>
</form>
</body>
</html>
