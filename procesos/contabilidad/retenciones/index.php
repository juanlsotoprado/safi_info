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
function habilita(){

	  if(document.form.chk_rif.checked==true){
		document.form.txt_rif.disabled=false;
	  }
	  else{
		document.form.txt_rif.disabled=true;
	  }
	  
	  
	  return;
} 

function incio(){
   
   var x;
   x=document.form;
   
   x.txt_rif.disabled=true;
   //x.txt_fecha_i.disabled=true;
   //x.txt_fecha_f.disabled=true;

}


function validar(){

     var sw=0;

     for(i=0;i<3;i++){
	    if(document.form.rb_imp[i].checked==true)
		 {
		    sw=1;
		 }
	 }
  
     if(sw==0){
	   alert(" Debe seleccionar el tipo de retenci\u00F3n a listar. ");
	   return;
	 }
	 
	 if(document.form.chk_rif.checked==true){
	   if(trim(document.form.txt_rif.value)==''){
	    alert(" Debe indicar un rif v\u00E1lido. ");
	    return;
	   }	
	 }
	  
	if(trim(document.form.txt_fecha_f.value)=='' || trim(document.form.txt_fecha_i.value)==''){
	  alert(" Debe indicar el intervalo de fechas !!!. ");
	  return;
	}   
	 
	 if(document.form.rb_imp[0].checked==true){
	   document.form.action="iva_1.php";
	 }
	 
	 if(document.form.rb_imp[1].checked==true){
	   document.form.action="isrl_1.php";
	 }
	 
	 if(document.form.rb_imp[2].checked==true){
	   document.form.action="ltf_1.php";
	 }
	 
	document.form.submit();
	 
}

function validar2(){

     var sw=0;

     for(i=0;i<3;i++){
	    if(document.form.rb_imp[i].checked==true)
		 {
		    sw=1;
		 }
	 }
  
     if(sw==0){
	   alert(" Debe seleccionar el tipo de retenci\u00F3n a listar. ");
	   return;
	 }
	 
	 if(document.form.chk_rif.checked==true){
	   if(trim(document.form.txt_rif.value)==''){
	    alert(" Debe indicar un rif v\u00E1lido. ");
	    return;
	   }	
	 }
	  
	if(trim(document.form.txt_fecha_f.value)=='' || trim(document.form.txt_fecha_i.value)==''){
	  alert(" Debe indicar el intervalo de fechas !!!. ");
	  return;
	}   
	 
	/* if(document.form.rb_imp[0].checked==true){
	   document.form.action="iva_1.php";
	 }
	 
	 if(document.form.rb_imp[1].checked==true){
	   document.form.action="isrl_1.php";
	 }
	 
	 if(document.form.rb_imp[2].checked==true){
	   document.form.action="ltf_1.php";
	 }*/
	 
	  document.form.action="sopg_rete.php";
	 
	 document.form.submit();
	 
}

function validar3(){

     var sw=0;

     for(i=0;i<3;i++){
	    if(document.form.rb_imp[i].checked==true)
		 {
		    sw=1;
		 }
	 }
  
     if(sw==0){
	   alert(" Debe seleccionar el tipo de retenci\u00F3n a listar. ");
	   return;
	 }
	 
	 if(document.form.chk_rif.checked==true){
	   if(trim(document.form.txt_rif.value)==''){
	    alert(" Debe indicar un rif v\u00E1lido. ");
	    return;
	   }	
	 }
	  
	if(trim(document.form.txt_fecha_f.value)=='' || trim(document.form.txt_fecha_i.value)==''){
	  alert(" Debe indicar el intervalo de fechas !!!. ");
	  return;
	}   
	 
	 if(document.form.rb_imp[0].checked==true){
	   document.form.action="iva.php";
	 }
	 
	 if(document.form.rb_imp[1].checked==true){
	   document.form.action="isrl.php";
	 }
	 
	 if(document.form.rb_imp[2].checked==true){
	   document.form.action="ltf.php";
	 }
	 
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

<body onLoad="MM_preloadImages('../../../imagenes/boton_generar_blk.gif');incio()">
<form name="form" method="post" action="">
  <table width="610" border="0" align="center">
    <tr>
      <td>
        <table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
          <tr>
            <td class="td_gray" colspan="3"><div align="center" class="normalNegroNegrita">Elija el tipo de Retenci&oacute;n </div></td>
 	    <td class="td_gray"><div align="center" class="normalNegroNegrita">Elija el tipo de Persona </div></td>
          </tr>
          <tr>
            <td width="100">
              <div align="right">
                <input name="rb_imp" type="radio" value="1">
            </div></td>
            <td width="50" class="normalNegro">IVA</td>
            <td width="100">&nbsp;</td>
	    <td>&nbsp;</td>
          </tr>

          <tr>
            <td width="50"><div align="right">
                <input name="rb_imp" type="radio" value="2">
            </div></td>
            <td class="normalNegro">ISRL</td>
	    <td width="100"></td>
            <td class="normalNegro"><div align="center"><input type="radio" name="tp" value="n">Natural  <input type="radio" name="tp" value="j">Juridica</div></td>
          </tr>
          <tr>
           <td width="100"><div align="right">
                <input name="rb_imp" type="radio" value="3">
            </div></td>
            <td class="normalNegro">LTF</td>
            <td>&nbsp;</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
          <tr>
            <td colspan="4" class="td_gray"><div align="center" class="normalNegroNegrita">Condiciones</div></td>
          </tr>
          <tr>
            <td width="100"><div align="center">
                <input name="chk_rif" type="checkbox" id="chk_rif" value="checkbox" onClick="habilita()">
            </div></td>
            <td width="200">
              <div align="right" class="normalNegro">Rif:</div></td>
            <td width="200" class="ptotal"><input name="txt_rif" type="text" class="normalNegro" id="txt_rif"></td>
            <td width="100">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center">
                
            </div></td>
            <td colspan="2"><div align="right" class="normalNegro">
                <div align="center">fecha </div>
            </div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center"> </div></td>
            <td><div align="right" class="normalNegro">Desde: </div></td>
             <td width="50%" class="peq">
             <input type="text" size="10" id="txt_fecha_i" name="txt_fecha_i" class="dateparse" onfocus="javascript: comparar_fechas(this);" readonly="readonly" />
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
			 <input type="text" size="10" id="txt_fecha_f" name="txt_fecha_f" class="dateparse"
onfocus="javascript: comparar_fechas(this);" readonly="readonly" />
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
	  
	  
      <input type="button" value="Reporte" onclick="javascript:validar3()">
	 <input type="button" value="Reporte PDF" onclick="javascript:validar()">
	  </a></div></td>
	  
    </tr>
  </table>
</form>
</body>
</html>
