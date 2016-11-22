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
	$sql = " SELECT * FROM sai_permiso_reporte('repo_comp','".$perfil."') as resultado ";
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

$sql="SELECT * FROM sai_seleccionar_campo('sai_seq_comprobante','comp_tipo,comp_num,anno','','',2)
resultado_set(comp_tipo varchar, comp_num int4, anno varchar)"; 
$resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
$valido=$resultado_set_most_p;
while($row=pg_fetch_array($resultado_set_most_p))
{
 $tipo=trim($row['comp_tipo']);
 if ($row['anno']==date(Y)){
  if ($tipo=='IVA'){
   $seq_i=trim($row['comp_num']+1); //Solicitante
  }
  if ($tipo=='LTF'){
   $seq_l=trim($row['comp_num']+1); //Solicitante
  }
  if ($tipo=='ISLR'){
   $seq_s=trim($row['comp_num']+1); //Solicitante
  }
 }else{
 	$seq_i=1;
 	$seq_l=1;
 	$seq_s=1;
 }
}

 ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script LANGUAGE="JavaScript" SRC="../../../js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript">

function acceptFloat(evt){	
	// NOTE: Backspace = 8, Enter = 13, '0' = 48, '9' = 57	'.'=46
	var nav4 = window.Event ? true : false;
	
	var key = nav4 ? evt.which : evt.keyCode;	

	return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}


function buscar_seq(tipo,montol,montoi,montos){
 
 if (tipo=="LTF"){
  document.form.secuencia.value=montol;
 }
 if (tipo=="IVA"){
  document.form.secuencia.value=montoi;
 }
if (tipo=="ISLR"){
  document.form.secuencia.value=montos;
 }
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
	   alert(" Debe seleccionar el tipo de comprobante de retenci\u00F3n. ");
	   return;
	 }
	 
	  if(trim(document.form.cod_sopg.value)==''){
	    alert(" Debe indicar el c\u00F3digo del sopg ");
	    return;
	   }	

	  if(trim(document.form.secuencia.value)==''){
	    alert(" Debe indicar el N\u00FAmero de Secuencia del Comprobante de Retenci\u00F3n ");
	    return;
	   }
		  
	 if(document.form.rb_imp[0].checked==true){
	  document.form.action="comprobante_iva.php";
	 }
	 
	 if(document.form.rb_imp[1].checked==true){
	  document.form.action="comprobante_islr.php";
	 }
	 
	 if(document.form.rb_imp[2].checked==true){
	   document.form.action="comprobante_ltf.php";
	 }
	 
	document.form.submit();
	 
}

</SCRIPT>
</head>
<br>
<body>
<form name="form" method="post" action="">
  <table width="610" border="0" align="center">
    <tr>
      <td>
        <table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
          <tr>
            <td class="td_gray" colspan="7"><div align="center" class="normalNegroNegrita">Elija el tipo de Comprobante de Retenci&oacute;n </div></td>
          </tr>
          <tr>
            <td width="100"><div align="right"><input name="rb_imp" type="radio" value="1"  onchange="javascript:buscar_seq('IVA',<?=$seq_l;?>,<?=$seq_i;?>,<?=$seq_s;?>);"></div></td>
            <td width="100" class="normalNegro">IVA</td>
            <td width="10">&nbsp;</td>
            <td width="50"><div align="right"><input name="rb_imp" type="radio" value="2" onchange="javascript:buscar_seq('ISLR',<?=$seq_l;?>,<?=$seq_i;?>,<?=$seq_s;?>);"></div></td>
            <td class="normalNegro">ISRL</td>
            <td width="10"><div align="right"><input name="rb_imp" type="radio" value="3" onchange="javascript:buscar_seq('LTF',<?=$seq_l;?>,<?=$seq_i;?>,<?=$seq_s;?>);"></div></td>
            <td class="normalNegro">LTF</td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
       <tr>
        <td width="80">&nbsp;</td>
	<td width="80">&nbsp;</td>
	<td width="10">&nbsp;</td>
	<td><div align="center" class="normalNegro">N&#176; Sopg:</div></td>
        <td><input name="cod_sopg" type="text" id="cod_sopg" size="15" maxlength="20" value="sopg-"></td>
        <td>&nbsp;</td>
       </tr>
       <tr>
        <td width="80">&nbsp;</td>
	<td width="80">&nbsp;</td>
	<td width="10">&nbsp;</td>
	<td><div align="center" class="normalNegro">N&#176; Secuencia:</div></td>
        <td><input name="secuencia" type="text" id="secuencia" size="15" maxlength="20"></td>
        <td>&nbsp;</td>
       </tr>
      </table></td>
    </tr>
    <tr>
      <td><table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
<tr>
            <td class="td_gray" colspan="7"><div align="center" class="normalNegroNegrita">Opcional para el Comprobante del IVA</div></td>
          </tr>
       <tr>
        <td width="70">&nbsp;</td>
	<td><div align="center" class="normalNegro">RIF:</div></td>
        <td colspan="2"><input name="rif" type="text" id="rif" size="12" maxlength="10"></td>
        <td>&nbsp;</td>
       </tr>
       <tr>
        <td width="70">&nbsp;</td>
	<td><div align="center" class="normalNegro">Nombre Proveedor:</div></td>
        <td colspan="2"><input name="proveedor" type="text" id="proveedor" size="45" maxlength="80"></td>
        <td>&nbsp;</td>
       </tr>
      </table></td>
    </tr>    
        <tr>
      <td><table width="600" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
<tr>
            <td class="td_gray" colspan="7"><div align="center" class="normalNegroNegrita">Opcional para el Comprobante del ISLR</div></td>
          </tr>
       <tr>
        <td width="70">&nbsp;</td>
	<td><div align="center" class="normalNegro">Monto Deducible:</div></td>
        <td colspan="2" class="normalNegro"><input name="deducible" type="text" id="deducible" size="12" maxlength="10"  value="0.0" onKeyPress="return acceptFloat(event)"></td>
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
<br>
<br>
  <table width="600" align="center" border="0" class="tablaalertas" background="../../../imagenes/fondo_tabla.gif">
          <tr>
            <td class="td_gray" colspan="3"><div align="center" class="normalNegroNegrita">Listado de Comprobantes Emitidos</div></td>
          </tr>
	   <tr>
            <td class="normal" align="center"><a href="listado_comprobantes.php?tipo=IVA">IVA</a></td>
            <td class="normal" align="center"><a href="listado_comprobantes.php?tipo=ISLR">ISRL</a></td>
            <td class="normal" align="center"><a href="listado_comprobantes.php?tipo=LTF">LTF</a></td>
          </tr>
  </table>
</form>
</body>
</html>
