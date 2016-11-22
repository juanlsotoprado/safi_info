<?php 
    ob_start();
	session_start();
	 require_once("includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	
	ob_end_flush(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAI:Modificaciones</title>
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
<script LANGUAGE="JavaScript" SRC="js/funciones.js"> </SCRIPT>
<script LANGUAGE="JavaScript">

function revisar_doc(id_documento,id_tipo_documento,id_opcion,objeto_siguiente_id,cadena_siguiente_id,id_objeto_actual,nombre_opcion)
{ 
	if (confirm(" Está seguro que desea "+nombre_opcion+" ? ")) {
		document.form1.action="accion_ejecutar.php?tipo="+id_tipo_documento+"&accion="+id_objeto_actual+"&accion_sig=" + objeto_siguiente_id + "&hijo="+cadena_siguiente_id+"&opcion="+id_opcion+"&id="+id_documento;

		document.form1.submit();
	
	}

}

function cierra(){
  window.close();
}



function revisar(){

  if(document.form.txt_memo.value==''){
     alert('Debe especificar el motivo de la anulación.');   
  }
  else
  {
     document.form.submit();
  }
  return;
     
}

//-->
</SCRIPT>
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onLoad="bloquear()">
<?php 
 $cod_doc=$_GET['id'];

 $pres_anno=$_SESSION['an_o_presupuesto'];
 
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
<form name="form" method="post" action="">
<table width="850" border="0" class="tablaCentral">
  <tr>
    <td><div align="center">
      <table width="850" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center" class="normalNegrita">Solicitud de Modificaci&oacute;n Presupuestaria </div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
    </div></td>
  </tr>
  <tr>
    <td><table width="840" border="0" align="center" cellpadding="0" cellspacing="0" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
          <tr>
            <td width="100"><div align="right" class="normalNegroNegrita">C&oacute;digo:</div></td>
            <td width="100"><div align="center" class="normalNegrita"><?php echo($cod_doc); ?></div></td>
          </tr>
          <tr>
            <td><div align="right" class="normalNegroNegrita">Fecha:</div></td>
            <td><div align="center" class="normal"><?php echo($dia."-".$mes."-".$ano); ?></div></td>
          </tr>
          <tr>
            <td><div align="right" class="normalNegroNegrita">Dependencia:</div></td>
            <td><div align="center" class="normal"><?php echo($row_modif['a']); ?></div></td>
          </tr>
      
        </table></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="158" class="normalNegrita"> &nbsp;&nbsp;   <?php if(trim($row_modif['e'])=='A') {echo "   Ajuste"; }  if(trim($row_modif['e'])=='M') {echo "   Modificaci&oacute;n"; }?></td>
        <td width="193">          <span class="normal">  Cr&eacute;dito</span></td>
        <td width="193"><span class="normalNegrita"><span class="normal">Traspaso</span> </span></td>
        <td width="193">          <span class="normal">  Disminuci&oacute;n</span></td>
      </tr>
 
      <tr>
        <td>&nbsp;</td>
        <td class="normalNegrita">&nbsp;</td>
        <td class="normalNegrita">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td class="normalNegrita">&nbsp;</td>
        <td class="normalNegrita">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><input name="hid_largo" type="hidden" id="hid_largo">
      <input name="hid_val" type="hidden" id="hid_val"></td>
  </tr>
  <?php 
     $sqldt_str="select * from sai_pres_consulta_0305_detalles('".$cod_doc."',".$pres_anno.") as resultado(pda varchar,ced_rec_sw bit, depe_id varchar,ac_proy_id varchar, ac_proy_sw bit, monto float8, acesp varchar )";
     $result_dt=pg_exec($sqldt_str); 
	 if(!$result_dt){
	   echo("Error Mostrando los detalles");
	 } 
  ?>
  <tr>
    <td>
	  <table width="840" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
       <tr class="td_gray">
        <td colspan="2"><div align="center" class="normal">Proyecto o Acci&oacute;n Centralizada</div></td>
        <td width="70" class="normal"><div align="center">Acci&oacute;n Espec&iacute;fica </div></td>
        <td width="80" class="normal"><div align="center">Dependencia</div></td>
        <td width="95"><div align="center" class="normal"> Partida</div></td>
        <td width="195"><div align="center" class="normal">Denominaci&oacute;n</div></td>
        <td width="45" class="normal"><div align="center">Tipo</div></td>
        <td width="170"><div align="center" class="normal">Monto</div></td>
      </tr>
      <tbody id="item">
	  </tbody>
	  <tr>
        <td colspan="8">&nbsp;</td>
      </tr>
	    <?php  
		  while($row_dt=pg_fetch_array($result_dt))
		   { 
		     $sql_str3="SELECT * FROM sai_consulta_desc_pda('".$row_dt['pda']."',".$pres_anno.") as detalle";
		     $res=pg_exec($sql_str3);
		     $row_dt_pda=pg_fetch_array($res);
			 
			 if($row_dt['ced_rec_sw']==1){
			   $clase="peqNegrita_naranja";
			   $tipo="receptora";   
			 }
			 else{
			   $clase="peqNegrita_naranja";
               $tipo="(cedente)";    
			 }
		   ?>
		   <tr>
			<td height="60" colspan="2"><div align="center" class="normalNegroNegrita"><?php echo($row_dt['ac_proy_id']); ?></div></td>
			<td width="70"><div align="center" class="normalNegroNegrita"><?php echo($row_dt['acesp']); ?> </div></td>
			<td width="80"><div align="center"  class="normalNegroNegrita"><?php echo($row_dt['depe_id']); ?></div></td>
			<td width="95"><div align="center" class="normal"><?php echo($row_dt['pda']); ?> </div></td>
			<td width="195"><div align="center" class="normalNegroNegrita"><?php echo($row_dt_pda['detalle']); ?></div></td>
			<td width="45"><div align="center" class="<?php echo($clase);?>"><?php  echo($tipo);?></div></td>
			<td width="170"><div align="center" class="normalNegroNegrita"><?php echo($row_dt['monto']); ?></div></td>
		  </tr>	
	    <?php  
		   }
		?>
		

	
      <tr>
        <td colspan="8"><div align="center"></div></td>
        </tr>
    </table>
	</td>
  </tr>
  		    <tr>
  		      <td><div align="center" class="normalNegrita">Exposici&oacute;n de Motivos </div></td>
    </tr>
  		    <tr>
  		      <td><div align="center">
  		        <textarea name="textarea" cols="80" rows="15" class="normal"><?php echo($row_modif['d']);  ?></textarea>
	          </div></td>
    </tr>
	<tr> 
      <td  class="normal" >
        <div align="center">
		 <?php $memo_contenido=$_POST['txt_memo'];  ?>
          <table width="500" border="0" cellpadding="0" cellspacing="0">
                 <tr>
                   <td width="100"><div align="right" class="normalNegrita">
                     <div align="center"><span class="normalNegrita">Motivo Anulaci&oacute;n</span>:</div>
                   </div></td>
                   <td width="400"><span class="Estilo1"><strong>
                     <textarea name="txt_memo" cols="60" rows="8" class="normal">
					 <?php echo($memo_contenido); ?>
					 </textarea>
                   </strong></span></td>
                 </tr>
          </table>
      </div></td>
    </tr>
	<tr> 
      <td  class="normal" >
      </td>
    </tr>
	<tr> 
      <td  class="normal" >
	    <div align="center">
		  <?php 
		  
		        $valido=false;
				$sql  = "select * from sai_anular_pmod('".trim($_SESSION['login'])."','".trim($cod_doc)."','".trim($memo_contenido)."','".trim($_SESSION['user_depe_id'])."') as memo_id";
			
				$resultado_set = pg_exec($conexion ,$sql);
				$valido=$resultado_set;
		  
		  ?>
	      <table width="400" border="0" cellpadding="0" cellspacing="0">
		     <?php if($valido){
		      
			   $row_mem=pg_fetch_array($resultado_set);
			   $memo_id=$row_mem['memo_id'];
			   $fecha_crea=trim($row_forma['form_fecha']);
		   
		    ?>
		     <tr>
                <td height="22" colspan="4"><div align="center" class="normal">
	            Se emiti&oacute; un memo bajo el n&uacute;mero: <a href="javascript:alert('Se abre memo' )"><?php echo  $memo_id;?> </a></div></td>
             </tr>
	         <tr>
               <td colspan="4"><div align="center"> <span class="normal">Esta forma 0305 fue realizada el d&iacute;a:
                    <?php 
				   //Separo la Fecha para que sea mostrada
					$ano=substr($fecha_crea,0,4);
					$mes=substr($fecha_crea,5,2);
					$dia=substr($fecha_crea,8,2);
					$hora=substr($fecha_crea,10);
					echo $dia."-".$mes."-".$ano ." ". $hora; 
				?>
              </span></div>
		    </td>
           </tr>
	       <tr> 
             <td height="20" colspan="3" class="normal" align="center"> <p>Esta forma 0305 fue ANULADA  el d&iacute;a:
               <?php 
				   echo date ("d/m/Y ") ." a las ". date ("h:i:s a "); 
				?>
               </p>
             </td>
          </tr>
          <tr> 
            <td height="18" colspan="3"><div align="center"><a href="javascript:window.print()" class="normal"><img src="imagenes/bot_imprimir.gif" width="23" height="20" border="0" /></a></div></td>
          </tr>
    <?php
	} 
	 else{
	     ?>
        <tr>
          <td height="18" colspan="3" class="normal"><div align="center"><img src="imagenes/vineta_azul.gif" width="11" height="7" />Ha ocurrido un error al registrar los datos , <?php echo(pg_errormessage($conexion)); ?>
		  </div>
		  </td> 
		</tr>       
     <?php 
	    }
	 ?>
	 </table>
      </div></td>
    </tr>
	<tr> 
      <td  class="normal" >
	   <table width="420" align="center">
			<?
           include("includes/respaldos_mostrar.php");
			?>
		</table>
		</td>
    </tr>
	 
	
    <tr>
  	 <td>&nbsp;</td>
    </tr>

</table>
</form>
<?php
  
 } //end if
  
?>
</body>
</html>
