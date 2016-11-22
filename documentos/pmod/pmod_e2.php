<?php 
    ob_start();
	session_start();
	 require_once("includes/conexion.php");
	 include('includes/arreglos_pg.php');
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	     {
		   header('Location:index.php',false);
	   	   ob_end_flush(); 
		   exit;
	     }
	
	ob_end_flush(); 
	$pres_anno = $_SESSION['an_o_presupuesto'];
	
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onLoad="bloquear();codigo_validacion()">
<table width="850" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" >
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  	<?php 
	
	 $largo = $_POST["hid_largo"];
	 
	  $fecha_in=trim($_POST['hid_hasta_itin']);
	 if($fecha_in!=''){
	 $fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2);
	 }else{
	 	$fecha_ini="";
	 }
	 
	 //datos fijos
	 	  
	 for($i=0;$i<$largo;$i++){	 
	   $a[$i]=$_POST["rb_ac_proy".$i];
	   $b[$i]=$_POST["txt_id_p_ac".$i];
	   $c[$i]=$_POST["txt_id_acesp".$i];
	   $d[$i]=$_POST["txt_id_depe".$i];
	   $e[$i]=$_POST["txt_id_pda".$i];
	   $f[$i]=$_POST["txt_den_pda".$i];
	   $g[$i]=$_POST["rb_ced".$i];
	   $h[$i]=$_POST["txt_monto_pda".$i]; 
	 }
	 
	//codigo de la modificacion
	$cod_doc = $request_codigo_documento;
	 //dependencia
	   $dep=$_POST['txt_depend'];
	
	//tipo de modificacion
	   $rb_tp=$_POST['rb_tp'];
	
	 //convierte a los arreglos de postgres
	 $str_a=convierte_arreglo($a);
	 $str_b=convierte_arreglo($b);
	 $str_c=convierte_arreglo($c);
	 $str_d=convierte_arreglo($d);
	 $str_e=convierte_arreglo($e);
	 $str_f=convierte_arreglo($f);
	 $str_g=convierte_arreglo($g);
	 $str_h=convierte_arreglo($h);
	  
	 //arma el query 
	 $sql_str="SELECT * FROM sai_pres_modifica0305('".$cod_doc."',".$rb_tp.",".$pres_anno.",'".$dep."','".$str_a."','".$str_b."','".$str_c."','".$str_d."','".$str_e."','".$str_h."','".$str_g."','".$fecha_ini."','".$_POST['txt_motivo']."') as resultado";
	 $res_q=pg_exec($sql_str);
	 
	 $msg='Modificaci&oacute;n Presupuestaria registrada con el c&oacute;digo: ';
	
     if ($res_q) {
	   
	    $rs_forma=pg_fetch_array($res_q);
	 	
		include("includes/respaldos_e1.php");
		
		$sql_str="select * from sai_pres_consulta_0305('".$cod_doc."',".$pres_anno.") as resultado(a varchar, b int2, c timestamp, d text,e varchar, esta_id int4)";
        $result=pg_exec($sql_str);
  
		 if(!$result){
		   echo("Error");
		 }
		else{ 

		 $row_modif=pg_fetch_array($result);
		 $ano=substr($row_modif['c'],0,4);
		 $mes=substr($row_modif['c'],5,2);
		 $dia=substr($row_modif['c'],8,2);
	    
		 echo("\n");		
	   	 echo("<SCRIPT LANGUAGE='JavaScript'>\n");
		 echo("var activar=".$row_modif['b'].";\n");
		 echo("</SCRIPT>\n");
	     echo("\n");
	   
	   ?>
	        <tr>
  	        <td width="20"><div align="right"><img src="imagenes/vineta_azul.gif" width="11" height="7"></div></td>
            <td width="10">&nbsp;</td>
	        <td width="296" colspan="2" class="titularMedio"><div align="left"><?php echo($msg.$cod_doc); ?></div></td>
  			</tr>
  			<tr>
   			<td colspan="4">&nbsp;</td>
			</tr>
  			<tr>
    		<td colspan="4"><div align="center"><img src="imagenes/mano_ok.gif" width="31" height="38"></div></td>
  			</tr>
   		    <tr>
   		      <td colspan="4">&nbsp;</td>
  </tr>
   		    <tr>
			  <td colspan="4">
<table width="850" border="0" class="tablaalertas">
  <tr>
    <td><div align="center">
      <table width="850" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="center" class="normalNegrita_naranja">Solicitud de Modificaci&oacute;n Presupuestaria </div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
    </div></td>
  </tr>
  <tr>
    <td><table width="850" border="0" cellpadding="0" cellspacing="0" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><table width="200" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas">
          <tr>
            <td width="100"><div align="right" class="titularMedio">C&oacute;digo:</div></td>
            <td width="100"><div align="center" class="normalNegrita"><?php echo($cod_doc); ?></div></td>
          </tr>
          <tr>
            <td><div align="right" class="titularMedio">Fecha:</div></td>
            <td><div align="center" class="normal"><?php echo($dia."-".$mes."-".$ano); ?></div></td>
          </tr>
          <tr>
            <td><div align="right" class="titularMedio">Dependencia:</div></td>
            <td><div align="center" class="normal"><?php echo($row_modif['a']); ?></div></td>
          </tr>
          <tr>
            <td><div align="right"><span class="titularMedio">Estado Actual :</span></div></td>
            <td><div align="center" class="normal"><?php echo(trim($des_est)); ?></div></td>
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
        <td width="158">&nbsp;</td>
      <!--   <td width="169" class="normal">Rebajas</td> -->
        <td width="193">          <span class="normal"> <input name="rb_tp" type="radio" value="3" >  Cr&eacute;dito</span></td>
        <td width="193"><span class="normalNegrita"><span class="normal"> <input name="rb_tp" type="radio" value="5" > Traspaso</span> </span></td>
        <td width="193">          <span class="normal"> <input name="rb_tp" type="radio" value="2" >  Disminuci&oacute;n</span></td>
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
     /*$sqldt_str="select * from sai_pres_consulta_0305_detalles('".$cod_doc."',".$pres_anno.") as resultado(pda varchar,ced_rec_sw bit, depe_id varchar,ac_proy_id varchar, ac_proy_sw bit, monto float8, acesp varchar )";
     $result_dt=pg_exec($sqldt_str);*/
  	$query = 	"SELECT ".
							"part_id as pda, ".
							"f0dt_tipo as ced_rec_sw, ".
							"depe_id, ".
							"f0dt_id_p_ac as ac_proy_id,".
							"f0dt_proy_ac as ac_proy_sw, ".
							"f0dt_monto as monto, ".
							"f0dt_id_acesp as acesp, ".
							"spae.centro_gestor, ".
							"spae.centro_costo ".
						"FROM sai_fo0305_det, sai_proy_a_esp spae ".
						"WHERE ".
							"f030_id = '".$cod_doc."' AND ".
							"f0dt_proy_ac = CAST(1 AS BIT) AND ".
							"f0dt_id_p_ac = spae.proy_id AND ".
							"f0dt_id_acesp = spae.paes_id ".
						"UNION ".
						"SELECT ".
							"part_id as pda, ".
							"f0dt_tipo as ced_rec_sw, ".
							"depe_id, ".
							"f0dt_id_p_ac as ac_proy_id,".
							"f0dt_proy_ac as ac_proy_sw, ".
							"f0dt_monto as monto, ".
							"f0dt_id_acesp as acesp, ".
							"sae.centro_gestor, ".
							"sae.centro_costo ".
						"FROM sai_fo0305_det, sai_acce_esp sae ".
						"WHERE ".
							"f030_id = '".$cod_doc."' AND ".
							"f0dt_proy_ac = CAST(0 AS BIT) AND ".
							"f0dt_id_p_ac = sae.acce_id AND ".
							"f0dt_id_acesp = sae.aces_id ".
						"ORDER BY centro_gestor, centro_costo";
			$result_dt=pg_exec($query);
			
	 if(!$result_dt){
	   echo("Error Mostrando los detalles");
	 } 
  ?>
  <tr>
    <td>
	  <table width="850" border="0" cellpadding="0" cellspacing="0" class="tablaalertas" id="tbl_mod">
       <tr class="td_gray">
        <td colspan="2"><div align="center" class="normal">Centro gestor</div></td>
        <td width="70" class="normal"><div align="center">Centro costo</div></td>
        <td width="80" class="normal"><div align="center">Dependencia</div></td>
        <td width="95"><div align="center" class="normal"> Partida</div></td>
        <td width="195"><div align="center" class="normal">Denominaci&oacute;n</div></td>
        <td width="45" class="normal"><div align="center">Tipo</div></td>
        <td width="170"><div align="center" class="normal">Monto</div></td>
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
			<td height="60" colspan="2"><div align="center" class="titularMedio"><?php echo($row_dt['centro_gestor']); ?></div></td>
			<td width="70"><div align="center" class="titularMedio"><?php echo($row_dt['centro_costo']); ?> </div></td>
			<td width="80"><div align="center"  class="titularMedio"><?php echo($row_dt['depe_id']); ?></div></td>
			<td width="95"><div align="center" class="normal"><?php echo($row_dt['pda']); ?> </div></td>
			<td width="195"><div align="center" class="titularMedio"><?php echo($row_dt_pda['detalle']); ?></div></td>
			<td width="45"><div align="center" class="<?php echo($clase);?>"><?php  echo($tipo);?></div></td>
			<td width="170"><div align="center" class="titularMedio"><?php echo($row_dt['monto']); ?></div></td>
		  </tr>	
	    <?php  
		   }
		?>
      <tr>
        <td colspan="8">
		  <div align="center" class="normalNegrita">Exposici&oacute;n de Motivos </div>
		</td>  
	  </tr>
	  <tr>
        <td colspan="8">
		  <div align="center">
		    <textarea name="textarea" cols="80" rows="15" class="normal"><?php echo($row_modif['d']);  ?></textarea>
		    </div></td>
	  </tr>	
	  
	  <tr> 
      <td colspan="8" class="normal" >
	   <table width="420" align="center">
			<?
		   
           include("includes/respaldos_mostrar.php");
			?>
		</table>
		</td>
    </tr> 
    </table>
	</td>
  </tr>
</table>
<?php 
    }
  }
   else{ 
?>
		</td>      
            </tr>
		    <tr>
  	        <td width="20"><div align="right"><img src="imagenes/vineta_azul.gif" width="11" height="7"></div></td>
            <td width="10">&nbsp;</td>
	        <td width="296" colspan="2" class="titularMedio"><div align="left">Ha ocurrido un error al registrar los datos , <?php echo(pg_errormessage($conexion)); ?></div></td>
  			</tr>
  			<tr>
   			<td colspan="4">&nbsp;</td>
			</tr>
  			<tr>
    		<td colspan="4"><div align="center"><img src="imagenes/mano_bad.gif" width="31" height="38"></div></td>
  			</tr>
	<?php  
	 } //END IF   
	?>
  <tr>
    <td colspan="4"><div align="center"></div></td>
  </tr>
</table>
<?php //  pg_close($conexion);?>
</body>
</html>
