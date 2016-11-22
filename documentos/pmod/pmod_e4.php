<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAFI:Modificaciones presupuestarias</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
    
function bloquear(){
	for(i=0;i<3;i++){
		document.getElementsByName('rb_tp')[i].disabled=true;
	   if(document.getElementsByName('rb_tp')[i].value==activar){
	      document.getElementsByName('rb_tp')[i].checked=true;
	   }
	}	
}	

</script>
<script language="JavaScript" src="includes/js/funciones.js"> </SCRIPT>
<script language="JavaScript">

function enviar(){
  validar(document.form);
}

function validar(x)
{
    var aux=true;
    var msg="";
	

	if(document.getElementsByName('rb_tp')[0].checked==false &&document.getElementsByName('rb_tp')[1].checked==false &&document.getElementsByName('rb_tp')[2].checked==false ){  
	      aux=false;
	      msg='Seleccione el tipo de modificación';
	}	
    
	if(document.getElementById('opt_depe').value==""){
	      aux=false;
	      msg='Seleccione una dependencia';
	}
	
	var cant_pdas;
	
	cant_pdas = document.getElementById('tbl_mod').getElementsByTagName('tr').length;
	cant_pdas = cant_pdas -3;		
	
	if(cant_pdas<=0){
	      
	      aux=false;
	      msg='Este documento no posee partidas asociadas';
	}
			
	if (aux==true){
	 for (i=0;i<partidas.length; i++)
  	 { 
                alert(document.getElementById('hid_largo').value);
				var r1=document.getElementById('rb_ac_proy'+i);
				var r2=document.getElementById('rb_ced'+i);
				
				r1.disabled=false;
				r2.disabled=false;
	 } 
	 x.submit(); 
	}
	else{
	alert(msg);
	}
}


//-->
</SCRIPT>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onLoad="bloquear()">
<?php 
 
 $cod_doc = $request_codigo_documento;
 $pres_anno=$_SESSION['an_o_presupuesto'];
 
//inicio
if ($request_id_opcion==$_SESSION['cinco']) { 
	$sql = "update sai_doc_genera set esta_id=7 where docg_id='".trim($cod_doc)."'";
    $resultado_set = pg_exec($conexion ,$sql);
} 
	//Fin
 $sql_str="select * from sai_pres_consulta_0305('".$cod_doc."',".$pres_anno.") as resultado(a varchar, b int2, c timestamp, d text, e varchar, esta_id int4)";	  
 $result=pg_exec($sql_str);
  
 if(!$result){
   echo("Error");
 }
else{ 

 $row_modif=pg_fetch_array($result);
 $ano=substr($row_modif['c'],0,4);
 $mes=substr($row_modif['c'],5,2);
 $dia=substr($row_modif['c'],8,2);
 
 echo("<SCRIPT LANGUAGE='JavaScript'>");
 echo("var activar=".$row_modif['b'].";");
 echo("</SCRIPT>");

?>
<table width="80%" border="0" class="tablaalertas">
    <tr class="td_gray">
        <td align="center" class="normalNegroNegrita" colspan="2">MODIFICACI&Oacute;N PRESUPUESTARIA <br><a href="javascript:abrir_ventana('reportes/presupuesto/disponibilidadTotal.php')">Disponibilidad</a></td>
     </tr>
  <tr>
    <td class="normalNegroNegrita">C&oacute;digo:</td>
    <td class="normal"><?php echo($cod_doc);?></td>
   </tr>
          <tr>
            <td class="normalNegroNegrita">Fecha:</td>
            <td class="normal"><?php echo($dia."-".$mes."-".$ano);?></td>
          </tr>
          <tr>
            <td class="normalNegroNegrita">Dependencia:</td>
            <td class="normal"><?php echo($row_modif['a']);?></td>
          </tr>
          <tr>
          <td class="normalNegroNegrita">Tipo: </td>
          <td class="normal">  <input name="rb_tp" type="radio" value="3" >  Cr&eacute;dito
         <input name="rb_tp" type="radio" value="5" > Traspaso</span> 
        <input name="rb_tp" type="radio" value="2" >  Disminuci&oacute;n
        
	 <input name="hid_largo" type="hidden" id="hid_largo">
      <input name="hid_val" type="hidden" id="hid_val">        
        </td>
      </tr>   
  <?php 
     $sqldt_str="select * from sai_pres_consulta_0305_detalles('".$cod_doc."',".$pres_anno.") as resultado(pda varchar,ced_rec_sw bit, depe_id varchar,ac_proy_id varchar, ac_proy_sw bit, monto float8, acesp varchar )";
     $result_dt=pg_exec($sqldt_str); 
	 if(!$result_dt){
	   echo("Error Mostrando los detalles");
	 } 
  ?>
  <tr>
    <td colspan="2">
	  <table width="840" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
       <tr class="td_gray">
        <td colspan="2"><div align="center" class="normalNegrita">Proyecto o Acci&oacute;n Centralizada</div></td>
        <td width="70" class="normalNegrita"><div align="center">Acci&oacute;n Espec&iacute;fica </div></td>
        <td width="80" class="normalNegrita"><div align="center">Dependencia</div></td>
        <td width="95"><div align="center" class="normalNegrita"> Partida</div></td>
        <td width="195"><div align="center" class="normalNegrita">Denominaci&oacute;n</div></td>
        <td width="45" class="normal"><div align="center">Tipo</div></td>
        <td width="170"><div align="center" class="normalNegrita">Monto</div></td>
      </tr>
      <tbody id="item">
	  </tbody>
	  <tr>
        <td colspan="8">&nbsp;</td>
      </tr>
	    <?php  
		  while($row_dt=pg_fetch_array($result_dt))  { 
		     $sql_str3="SELECT * FROM sai_consulta_desc_pda('".$row_dt['pda']."',".$pres_anno.") as detalle";
		     $res=pg_exec($sql_str3);
		     $row_dt_pda=pg_fetch_array($res);
			 
			 if($row_dt['ced_rec_sw']==1){
			   $clase="normal";
			   $tipo="receptora";   
			 }
			 else{
			   $clase="normal";
               $tipo="(cedente)";    
			 }
		   ?>
		   <tr>
			<td height="60" colspan="2"><div align="center" class="normal"><?php echo($row_dt['ac_proy_id']); ?></div></td>
			<td width="70"><div align="center" class="normal"><?php echo($row_dt['acesp']); ?> </div></td>
			<td width="80"><div align="center"  class="normal"><?php echo($row_dt['depe_id']); ?></div></td>
			<td width="95"><div align="center" class="normal"><?php echo($row_dt['pda']); ?> </div></td>
			<td width="195"><div align="center" class="normal"><?php echo($row_dt_pda['detalle']); ?></div></td>
			<td width="45"><div align="center" class="<?php echo($clase);?>"><?php  echo($tipo);?></div></td>
			<td width="170"><div align="center" class="normal"><?php echo($row_dt['monto']); ?></div></td>
		  </tr>	
	    <?php  
		   }
		
		if(trim($row_modif['d'])<>""){
		?>
      <tr>
        <td colspan="8"><div align="center"></div></td>
        </tr>
	        <tr>
        <td colspan="9" class="normalNegroNegrita">Exposici&oacute;n de Motivos<br/></td>
      </tr>
      <tr>
        <td colspan="9" class="normal">
          <?php echo($row_modif['d']);  ?>        </div></td>
      </tr>	
	  <?php } ?>
	</table>
	</td>
  </tr>
   <tr>
      <td colspan="2">  
	  <div align="center" class="normalNegrita">
	  <br>
Solicitud revisada el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br>
<br>
<a href="javascript:window.print()" class="normal"><img src="imagenes/bot_imprimir.gif" width="23" height="20" border="0" /></a><br><br>
  </div></td>
    </tr>
    <div align="center">
<a href="documentos.php?tipo=pmod">Regresar</a>
</div>	
</table>
</form>
<?php } ?>
</body>
</html>