<?php
ob_start();
require("../../includes/conexion.php");
require("../../includes/perfiles/constantesPerfiles.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}
	if ($_SESSION['user_perfil_id']==PERFIL_ALMACENISTA ){
	 $bandeja_transito="select asbi_id,to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,t4.empl_apellidos as apellido_revisa,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=13 and t2.empl_cedula=t1.usua_login and revi_doc=asbi_id and wfop_id=3 and t3.usua_login=t4.empl_cedula
	UNION
	select acta_id,to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,t4.empl_apellidos as apellido_revisa,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_reasignar t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=13 and t2.empl_cedula=t1.usua_login and revi_doc=acta_id and wfop_id=3 and t3.usua_login=t4.empl_cedula
	order by 2";
	$accion_transito="Enviar";
	//echo $bandeja_transito;
	$resultado_transito=pg_query($conexion,$bandeja_transito) or die("Error al Mostrar Lista de Documentos Transito");
    $total_transito=pg_num_rows($resultado_transito);
    
	$bandeja_entrada="select asbi_id,to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado
	where t1.esta_id=10 and 
	empl_cedula=t1.usua_login 
	order by asbi_fecha";
	$accion="Modificar";
	
	}
	if ($_SESSION['user_perfil_id']==PERFIL_ANALISTA_BIENES){
	 $bandeja_entrada="select asbi_id,to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,
		 case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado
	where t1.esta_id=10 and 
	empl_cedula=t1.usua_login 
	UNION
	select acta_id,to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,
		 case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_reasignar t1,sai_empleado
	where t1.esta_id=10 and 
	empl_cedula=t1.usua_login 
	order by fecha_acta
	";
	$accion="Visto Bueno"; 	 
	$accion2="Anular";
	 
	$bandeja_transito="select asbi_id,to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,t4.empl_apellidos as apellido_revisa,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=33 and t2.empl_cedula=t1.usua_login and revi_doc=asbi_id and wfop_id=25 and t3.usua_login=t4.empl_cedula 
	UNION
	select acta_id,to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,t4.empl_apellidos as apellido_revisa,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_reasignar t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=33 and t2.empl_cedula=t1.usua_login and revi_doc=acta_id and wfop_id=25 and t3.usua_login=t4.empl_cedula 
	order by 2";
	$accion_transito="Finalizar";
	
	$resultado_transito=pg_query($conexion,$bandeja_transito) or die("Error al Mostrar Lista de Documentos Transito");
    $total_transito=pg_num_rows($resultado_transito);
	
	
	$bandeja_imprimir="select asbi_id,to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,empl_nombres,empl_apellidos,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado,sai_revisiones_doc
	where revi_doc=asbi_id and ((t1.esta_id=13 and wfop_id=3) or
    ((t1.esta_id=33 and wfop_id=25 and perf_id='12453') or
     (t1.esta_id=33 and wfop_id=25 and perf_id='62453')))  
	and empl_cedula=t1.usua_login 
	order by asbi_fecha";
	//$accion="Visto Bueno"; 	
	 	  
	 }
	if ($_SESSION['user_perfil_id']==PERFIL_JEFE_BIENES  || $_SESSION['user_perfil_id']==PERFIL_COORDINADOR_BIENES) 
	{ 
	 	$bandeja_entrada="select distinct(asbi_id),to_char(asbi_fecha,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,
	 	t4.empl_apellidos as apellido_revisa,
	 	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_asbi t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=12 and revi_doc=asbi_id and wfop_id=6 and t3.usua_login=t4.empl_cedula and
	t2.empl_cedula=t1.usua_login 
	UNION
	select distinct(acta_id) as asbi_id,to_char(fecha_acta,'DD/MM/YYYY') as fecha_acta,infocentro,solicitante,t2.empl_nombres,t2.empl_apellidos,t4.empl_nombres as nombre_revisa,
	t4.empl_apellidos as apellido_revisa,
	case infocentro when null then '' else (select nombre from safi_infocentro where nemotecnico=infocentro)  end as info_nombre
	from sai_bien_reasignar t1,sai_empleado t2,sai_revisiones_doc t3,sai_empleado t4
	where t1.esta_id=12 and revi_doc=acta_id and wfop_id=6 and t3.usua_login=t4.empl_cedula and
	t2.empl_cedula=t1.usua_login 
	order by asbi_id";
	$accion="Revisar";
	 }

	 $resultado_entrada=pg_query($conexion,$bandeja_entrada) or die("Error al Mostrar Lista de Documentos Entrada");
     $total_entrada=pg_num_rows($resultado_entrada);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI:Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../js/funciones.js"> </script>
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
            <td colspan="4"><img src="../../imagenes/img_bandeja_prin.jpg" width="326" height="29"></td>
            </tr>
			  <tr>
				<td width="20"><div align="right"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"></div></td>
				<td width="10">&nbsp;</td>
				<td width="200" class="normalNegroNegrita"><div align="left">
                        Documentos en bandeja:
                      </div></td>
				<td width="96" class="normalNegroNegrita"><div align="center"><?php echo $total_entrada; ?></div></td>
			  </tr>
			  <?php //if ($_SESSION['user_perfil_id']!=PERFIL_JEFE_BIENES  && $_SESSION['user_perfil_id']!=PERFIL_COORDINADOR_BIENES &&
			 // 	  $_SESSION['user_perfil_id']!=PERFIL_ALMACENISTA ) {?>
			   <tr>
				<td width="20"><div align="right"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"></div></td>
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
            echo utf8_decode("Actas de Salida");?></td>
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
    <td><div align="center" class="normalNegroNegrita"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"> Bandeja de entrada </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <table width="100%" height="25" border="0" cellpadding="4" cellspacing="2" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
         <?php
         $resultado_transito=pg_query($conexion,$bandeja_entrada) or die("Error al Mostrar Lista de Documentos Devueltos");
         $total_transito=pg_num_rows($resultado_transito);
		 if ($total_transito>0) {
		 ?>
		  <tr class="td_gray" align="center">
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha</td>
            <td class="normalNegroNegrita">Infocentro</td>
			<td class="normalNegroNegrita">Solicitante</td>
			<td class="normalNegroNegrita">Usuario registra</td>	
			<?php if ($accion!="Visto Bueno"){?>
			<td class="normalNegroNegrita">Usuario revisa</td>
			<?php }else {?>	
			<td class="normalNegroNegrita">Observaciones</td>
			<?php }?>				
 	   		<td class="normalNegroNegrita">Opciones</td>
          </tr>	  
	      <?php while($row_doc_bandeja=pg_fetch_array($resultado_transito)){?>
          <tr class="normal">
            <td align="left" class="link">
            <?php if (substr($row_doc_bandeja['asbi_id'],0,1)=='a'){?>			
			<a title="<? echo $detalle; ?>" class="link"><?php echo "s".substr($row_doc_bandeja['asbi_id'],1); ?></a>
			<?php }else{?>
			<a title="<? echo $detalle; ?>" class="link"><?php echo $row_doc_bandeja['asbi_id']; ?></a>
			<?php }?>
			</td>
            <td align="center"><?php echo $row_doc_bandeja['fecha_acta'];?></td>
            <td><?php echo $row_doc_bandeja['infocentro'].":".$row_doc_bandeja['info_nombre'];?></td>
            <?php 
            $solicitante=$row_doc_bandeja['solicitante'];
            $sql_depe="SELECT * FROM sai_dependenci WHERE depe_id in (".$solicitante.")";
            $res_q=pg_exec($sql_depe) or die("Error al consultar el solicitante");
            $depe_nombre="";
            $cont=0;
            while($depe_row=pg_fetch_array($res_q)){ 
	          if ($cont>0)
            	$depe_nombre=$depe_nombre.", ".$depe_row['depe_nombre'];
              else
             	$depe_nombre=$depe_row['depe_nombre'];
 			  $cont++;
            }            

              $motivo_devolucion="";
			  $memo="SELECT max(oid) as oid_memo FROM sai_docu_sopor t1 WHERE  doso_doc_fuente='".$row_doc_bandeja['asbi_id']."' group by doso_doc_fuente";
			  $result_memo=pg_query($conexion,$memo);
			  if ($row_memo=pg_fetch_array($result_memo)){
			  	
			   $motivo="SELECT memo_contenido FROM sai_docu_sopor t1,sai_memo t2 WHERE memo_id=doso_doc_soport and
		       t1.oid='".$row_memo['oid_memo']."'";
			   $result_motivo=pg_query($conexion,$motivo);
			   if ($row_motivo=pg_fetch_array($result_motivo)){
			    $motivo_devolucion=strtoupper($row_motivo['memo_contenido']);	
			   }  
			  }
            
            ?>
            <td><?php echo $depe_nombre;?></td>            
            <td><?php echo $row_doc_bandeja['empl_nombres']." ".$row_doc_bandeja['empl_apellidos'];?></td>
            <?php if ($accion=="Revisar")	 {?>   
             <td><?php echo $row_doc_bandeja['nombre_revisa']." ".$row_doc_bandeja['apellido_revisa'];?></td>
			 <td><a href="revision_salida.php?codigo=<? echo trim($row_doc_bandeja['asbi_id']);  ?>&tipo=6&accion=<? echo $accion;?>" class="copyright"><?php echo($accion);?></a></td>
             <?php }
             elseif ($accion=="Visto Bueno"){?>
             <td><font color='Red'><STRONG><?php echo $motivo_devolucion;?></STRONG></font></td>      
            <td>
            <a href="modificar_salida_activos.php?codigo=<? echo trim($row_doc_bandeja['asbi_id']);  ?>&tipo=6&accion=<? echo $accion;?>" class="copyright"><?php echo($accion);?></a>
            <a href="modificar_salida_activos.php?codigo=<? echo trim($row_doc_bandeja['asbi_id']);  ?>&tipo=6&accion=<? echo $accion2;?>" class="copyright"><?php echo($accion2);?></a></td>
            <?php }elseif ($accion=="Modificar"){?>
            <td></td><td>
            <a href="modificar_salida_activos.php?codigo=<? echo trim($row_doc_bandeja['asbi_id']);  ?>&tipo=a&accion=<? echo $accion;?>" class="copyright"><?php echo($accion);?></a></td>
          </tr>
		 <?php }
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
  <?php if (($_SESSION['user_perfil_id']==PERFIL_ANALISTA_BIENES) || ($_SESSION['user_perfil_id']==PERFIL_ALMACENISTA)){?>
  
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center" class="normalNegroNegrita"><img src="../../imagenes/vineta_azul.gif" width="11" height="7"> Bandeja en tr&aacute;nsito </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
      <table width="100%" height="25" border="0" cellpadding="4" cellspacing="2" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
         <?php
         $resultado_transito=pg_query($conexion,$bandeja_transito) or die("Error al Mostrar Lista de Documentos en transito");
         $total_transito=pg_num_rows($resultado_transito);
		 if ($total_transito>0) {
		 ?>
		  <tr class="td_gray" align="center">
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha</td>
            <td class="normalNegroNegrita">Infocentro</td>
			<td class="normalNegroNegrita">Solicitante</td>
			<td class="normalNegroNegrita">Usuario registra</td>		
			<td class="normalNegroNegrita">Usuario revisa</td>		
 	   		<td class="normalNegroNegrita">Opciones</td>
          </tr>	  
	      <?php while($row_doc_bandeja=pg_fetch_array($resultado_transito)){?>
          <tr class="normal">
            <td align="left" class="link">			
             <?php if (substr($row_doc_bandeja['asbi_id'],0,1)=='a'){?>			
			<a title="<? echo $detalle; ?>" class="link"><?php echo "s".substr($row_doc_bandeja['asbi_id'],1); ?></a>
			<?php }else{?>
			<a title="<? echo $detalle; ?>" class="link"><?php echo $row_doc_bandeja['asbi_id']; ?></a>
			<?php }?>

			</td>
            <td align="center"><?php echo $row_doc_bandeja['fecha_acta'];?></td>
            <td><?php echo $row_doc_bandeja['infocentro'].":".$row_doc_bandeja['info_nombre'];?></td>
            <?php 
            $solicitante=$row_doc_bandeja['solicitante'];
            $sql_depe="SELECT * FROM sai_dependenci WHERE depe_id in (".$solicitante.")";
            $res_q=pg_exec($sql_depe) or die("Error al consultar el solicitante");
            $depe_nombre="";
            $cont=0;
            while($depe_row=pg_fetch_array($res_q)){ 
	          if ($cont>0)
            	$depe_nombre=$depe_nombre.", ".$depe_row['depe_nombre'];
              else
             	$depe_nombre=$depe_row['depe_nombre'];
 			  $cont++;
            }
            ?>
            <td><?php echo $depe_nombre;?></td>            
            <td><?php echo $row_doc_bandeja['empl_nombres']." ".$row_doc_bandeja['empl_apellidos'];?></td>  
            <td><?php echo $row_doc_bandeja['nombre_revisa']." ".$row_doc_bandeja['apellido_revisa'];?></td> 
            <td><img src="../../imagenes/vineta_azul.gif" width="11" height="7">
            <a href="revision_salida.php?codigo=<? echo trim($row_doc_bandeja['asbi_id']);  ?>&tipo=99&accion=Finalizar" class="copyright"><? echo($accion_transito);?></a></td>
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
  <?php }?>
</table>
</body>
</html>
<?php pg_close($conexion);?>