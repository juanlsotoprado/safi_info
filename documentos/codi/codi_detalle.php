<?php
  ob_start();
  session_start();
  require_once("../../includes/conexion.php");
  require_once("../../includes/fechas.php");
  if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
  }
  ob_end_flush();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title>.:SAFI:Comprobante Diario</title>
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="js/funciones.js"></script>
<script language="JavaScript" src="js/botones.js"></script>
<?php
  $cont=trim($_GET['cont']);
  $A= '             ';
  $codigo=$_REQUEST['codigo'];
  $usuario = $_SESSION['login'];
  $sql="SELECT * FROM sai_seleccionar_campo ('sai_doc_genera,sai_empleado','empl_nombres, empl_apellidos','docg_id='||'''$codigo'''||' and sai_doc_genera.usua_login=empl_cedula' ,'',2) resultado_set(empl_nombres VARCHAR, empl_apellidos VARCHAR)";
  $resultado=pg_exec($conexion,$sql) or die("Error al mostrar");

  if($row=pg_fetch_array($resultado)){
	$usuario=$row['empl_nombres']." ".$row['empl_apellidos'];
  }

  $total_imputacion=0;
  $sql_presupuesto="SELECT * FROM sai_buscar_datos_causado('".$codigo."','codi') AS resultado (categoria VARCHAR, aesp VARCHAR, anno INT2, apde_tipo BIT,part_id VARCHAR, apde_monto FLOAT8)";
  $resultado_pre=pg_query($conexion,$sql_presupuesto) or die ("Error al mostrar datos presupuestarios");

  $i=0;
  $total_imputacion=pg_num_rows($resultado_pre);
  while ($row_pre=pg_fetch_array($resultado_pre)){
	$categoria=$row_pre['categoria'];
	$aesp =$row_pre['aesp'];
	$anno =$row_pre['anno'];
	$apde_tipo =$row_pre['apde_tipo'];
	$apde_partida[$i]=$row_pre['part_id'];
	$apde_monto[$i]=$row_pre['apde_monto'];
	$id_part=$apde_partida[$i];

	$convertidor="SELECT * FROM  sai_seleccionar_campo ('sai_convertidor','cpat_id','part_id=''$id_part''','',2) resultado_set(cpat_id VARCHAR)";
	$resultado_conv=pg_query($conexion,$convertidor) or die("Error al consultar el convertidor");
	if($row=pg_fetch_array($resultado_conv)){
		$cuenta[$i]=trim($row['cpat_id']);
	}

	if ($apde_tipo==1){ //Por Proyecto
		$query ="SELECT * FROM sai_buscar_centro_gestor_costo_proy('".trim($categoria)."','".trim($aesp)."') AS result (centro_gestor VARCHAR, centro_costo VARCHAR)";
	}else{ //Por Accion Centralizada
		$query ="SELECT * FROM sai_buscar_centro_gestor_costo_acc('".trim($categoria)."','".trim($aesp)."') AS result (centro_gestor VARCHAR, centro_costo VARCHAR)";
	}

	$resultado_query= pg_exec($conexion,$query);
	if ($resultado_query){
		if($row=pg_fetch_array($resultado_query)){
			$centrog[$i] = trim($row['centro_gestor']);
			$centroc[$i] = trim($row['centro_costo']);
		}
	}
	$sql_nombre_presu="SELECT * FROM sai_consulta_proyecto_accion('".$categoria."','".$aesp."','$apde_tipo','$anno') AS resultado (nombre_categ VARCHAR, nombre_esp VARCHAR, cg varchar, cc varchar)";

	$resultado_nomb_pre=pg_query($conexion,$sql_nombre_presu) or die ("Error al buscar datos presupuestarios");
	if ($row_pre=pg_fetch_array($resultado_nomb_pre)){
		$nom_categoria=$row_pre['nombre_categ'];
		$nom_aesp =$row_pre['nombre_esp'];
	}
	$i++;
  }

  $sql="SELECT * FROM  sai_codi WHERE comp_id='".$codigo."'"; 
  $resultado=pg_query($conexion,$sql) or die("Error al mostrar");
  while($row=pg_fetch_array($resultado)){
	$id_comp=trim($row['comp_id']);
	$fecha_comp=$row['comp_fec'];
	$comentario=$row['comp_comen'];
	$fecha_emis=$row['comp_fec_emis'];
	$edo=$row['esta_id'];
	$documento=$row['comp_doc_id'];
	$referencia=$row['nro_referencia'];
	$compromiso=$row['nro_compromiso'];
	$reserva=$row['fte_financiamiento'];
  }
?>
</head>
<link rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all" />
<body>
<form name="form" method="post" action="codi_e1.php">
<table width="764" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
  <tr class="td_gray"> 
	<td colspan="5" class="normalNegroNegrita" align="center">Comprobante diario</td>
  </tr>
  <tr>
	  <td colspan="4">
		<table>
  <tr>
	<td colspan="5" align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  <tr>
	<td class="normalNegrita">C&oacute;digo: </td>
	<td class="normalNegro"><?= $codigo?></td>
  </tr>
  <?php 
	if ($edo==15) { ?>
  <tr>
	<td colspan="5">
	  <div align="center"><font color="Red"><STRONG>ANULADO</STRONG></font></div></td>
  </tr>
  <?}?>
  <tr>
	<td class="normalNegrita">Fecha:</td>
	<td class="normalNegro"><?= $fecha_comp?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Justificaci&oacute;n:</td>
	<td class="normalNegro"><?= $comentario?></td>
  </tr>
  <tr>
	<td class="normalNegrita">Documento asociado:</td>
	<td class="normalNegro"><?= $documento?></td>
  </tr>
  <tr>
	<td class="normalNegrita" width="140">N&deg; Referencia bancaria:</td>
	<td class="normalNegro"><?= $referencia?></td>
  </tr>
  <tr>
	<td  align="right" class="normalNegrita">&nbsp;</td>
  </tr>
  </td>
  </tr>
    </table>
	  
  <tr>
	<td colspan="4" class="normalNegrita">
	  <table width="747" height="87" align="center" background="../Imagenes/fondo_tabla.PNG" id="servicios">
		<tr valign="middle" class="Estilo4">
		  <td width="39" class="titularMedio">N&deg; Registro</td>
		  <td width="90" class="titularMedio"><div align="center">Cuenta contable</div></td>
		  <td><p class="titularMedio"><strong>Descripci&oacute;n</strong></p></td>
		  <td width="156" align="right" class="titularMedio"><strong>Debe</strong></td>
		  <td width="103" align="right" class="titularMedio"><strong>Haber</strong></td>
		</tr>
		<?php
		  $sql_reng="SELECT * FROM  sai_seleccionar_campo ('sai_reng_comp','comp_id,reng_comp,cpat_id,cpat_nombre,rcomp_debe,rcomp_haber,rcomp_tot_db,rcomp_tot_hab','comp_id=''$codigo''','',2) resultado_set(comp_id VARCHAR, reng_comp INT8,cpat_id VARCHAR,cpat_nombre VARCHAR,rcomp_debe FLOAT8,rcomp_haber float8,rcomp_tot_db FLOAT8,rcomp_tot_hab FLOAT8)";
		  $resultado=pg_query($conexion,$sql_reng) or die("Error al mostrar");
		  while($row=pg_fetch_array($resultado))
		  {
		  	$id_comp=trim($row['comp_id']);
		  	$comp_reng=$row['reng_comp'];
		  	$id_cta=trim($row['cpat_id']);
		  	$nom_cta=$row['cpat_nombre'];
		  	$debe=$row['rcomp_debe'];
		  	$haber=$row['rcomp_haber'];
		  	$total_db=$row['rcomp_tot_db'];
		  	$total_haber=$row['rcomp_tot_hab'];
		   ?>
		 <tr valign="top" class="normalNegro">
		   <td valign="top" ><?= $comp_reng?></td>
		   <td align="left" ><?= $id_cta?></td>
		   <td><div align="justify"><?= $nom_cta?></div></td>
		   <td width="68" align="right"><?= number_format($debe,2,'.',',')?></td>
		   <td height="34" colspan="7" align="right"><?= number_format($haber,2,'.',',')?></td>
	 	  </tr>
		  <?php
		  	  }
			?>
			<tbody id="body"></tbody>
	   </table>
	   <table width="379" height="65" align="right" background="../Imagenes/fondo_tabla.PNG" id="totales">
		 <tr valign="top" class="normal">
			<td class="normal">&nbsp;</td>
			<td height="17" colspan="2" class="normal"><div align="right"></div></td>
		 </tr>
		 <tr valign="top" class="normal">
		   <td class="normal"><div align="right"><strong>Total:</strong></div></td>
		   <td align="right" class="normalNegro"><?= number_format($total_db,2,'.',',')?></td>
		   <td width="108" height="15" align="right" class="normalNegro"><?= number_format($total_haber,2,'.',',')?></td>
		 </tr>
		 <tr valign="top" class="normal">
			<td width="141" class="normal">&nbsp;</td>
			<td width="114" class="normal">&nbsp;</td>
			<td height="15" class="normal"><label id="existepart"></label></td>
		 </tr>	
		    <tbody id="body"></tbody>
		</table>
		</td>
	</tr>
	<tr>
	  <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="4">
		<table>
		  <tr>
			<td class="normalNegrita">Fuente de financiamiento:</td>
			<td align="left" class="normalNegro">
			<?
			 
 			  $sql="SELECT fuef_descripcion FROM sai_fuente_fin WHERE fuef_id = '".$reserva."'"; 
			  $resultado_set_most_p=pg_query($conexion,$sql) or die("Error al consultar solicitud de pago");
			  if($row=pg_fetch_array($resultado_set_most_p))
			  {
 				$fuente=trim($row['fuef_descripcion']); 
			  }
	
              echo $fuente;?></td>
		   </tr>	
		   <tr>
			 <td class="normalNegrita">N&deg; Compromiso:</td>
			 <td align="left" class="normalNegro"><?= $compromiso?></td>
		   </tr>	
		   <tr>
			 <td colspan="4" align="center" class="normalNegrita"><a href="javascript:window.print()" class="normal"></a></td>
		   </tr>
		</table>
	   </td>
	</tr>
	<tr>
	  <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
	</tr>
	<tr class="normal">																			
	  <td colspan="4" align="center" class="style2">Este comprobante fue generado el d&iacute;a: <?= cambia_esp($fecha_emis).substr($fecha_emis,10)?><br/>por: <?= $usuario;?><br/></td>
	</tr>
	<?php
	  if($edo==15) 
	  {
		$sql="SELECT * from sai_buscar_memo_soporte('".$codigo."') AS resultado ".
		"(cod_memo VARCHAR, empl_nombres VARCHAR,empl_apellidos VARCHAR, asunto VARCHAR, contenido TEXT, memo_fecha_crea TIMESTAMP, depe_id VARCHAR)";
		$resultado_set_memo = pg_exec($conexion ,$sql);
		$valido=$resultado_set_memo;
		 if ($row_memo = pg_fetch_array($resultado_set_memo))
		 {
		  $memo_fecha=$row_memo['memo_fecha_crea'];
		  $memo_contenido=$row_memo['contenido'];
		  $memo_responsable=$row_memo['empl_nombres']." ".$row_memo['empl_apellidos'];
		?>
		<tr class="normal">
			<td colspan="4" align="center" class="style2">
				<font color="Red">
					Este comprobante fue ANULADO el d&iacute;a: <?= cambia_esp($memo_fecha).substr($memo_fecha,10)?><br/>
					por: <?= $memo_responsable?><br/>
					Justificaci&oacute;n:<?= $memo_contenido?>
				</font>
			</td>
		</tr>
		<?
		  }
		}
		?>
		<tr>
		  <td colspan="4" align="center" class="normalNegrita">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3">
			  <table width="50%" border="0" class="tablaalertas" align="center">
				<tr class="td_gray">
				  <td align="center" class="normalNegroNegrita">Imputaci&oacute;n presupuestaria</td>
				</tr>
				<tr>
				  <td class="normal" align="center">
					<table width="100%" border="0">
					 <tr>
					 <td width="15%" class="normalNegrita"><div align="center">Proyecto/Acc</div></td>
					 <td width="15%" class="normalNegrita"><div align="center">Acci&oacute;n espec&iacute;fica</div></td>
					  <td width="20%" class="normalNegrita"><div align="center">Partida</div></td>
					  <td width="15%" class="normalNegrita"><div align="center">Cuenta</div></td>
					  <td width="20%" class="normalNegrita"><div align="center">Monto</div></td>
					 </tr>
					 <tr>
					 <?php
					   for($ii=0; $ii<$total_imputacion; $ii++){
					 ?>
					 
					 <td class="normalNegro" align="left" width="17%"><div align="center"><?= $centrog[$ii];?></div></td>
					 <td class="normalNegro" align="left" width="17%"><div align="center"><?= $centroc[$ii];?></div></td>
					   <td class="normalNegro" align="left" width="15%"><div align="center">
					   <?php
						 $id_part=$apde_partida[$ii];
						 echo $id_part;
						?></div></td>
					   <td class="normalNegro" align="left" width="17%"><div align="center"><?= $cuenta[$ii];?></div></td>
					   <td class="normalNegro" align="left" width="20%">
						 <div align="center"><?= number_format($apde_monto[$ii],2,'.',',');?></div></td>
					 </tr>
					 <?php } ?>
					</table>
					  </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
		  <td height="46" colspan="4"><div align="center">
			<a href="codi_pdf.php?cod_doc=<?=trim($codigo)?>"><img src="../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"/></a></div>
		  </td>
		</tr>
		<tr>
		  <td colspan="4" align="center" class="normalNegrita">
			<a href="javascript:window.print()" class="normal"><img src="../../imagenes/boton_imprimir.gif" alt="impresora" width="23" height="20" border="0"/></a>
		  </td>
		</tr>
		<tr>
		  <td colspan="4" align="center" class="normalNegrita">
				<input type="button" value="Cerrar" onclick="javascript:window.close()">
   		  </td>
		</tr>
	</table>
</form>
</body>
</html>
<?php pg_close($conexion);?>