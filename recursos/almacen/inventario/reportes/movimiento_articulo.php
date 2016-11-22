<?php 
	session_start();
	require_once("../../../../includes/conexion.php");
	 
	if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	{
		header('Location:../../../../index.php',false);
		ob_end_flush(); 
		exit;
	}
	
	// Obtener todos los artículos
	$query = "
		SELECT
			item.id AS item_id,
			item.nombre AS item_nombre
		FROM
			sai_item item
			INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = item.id)
		ORDER BY
			item.nombre
	";
	
	$resultado = pg_query($conexion, $query) or die("Error al consultar los articulos.");
	$nombresArticulos = array();
	
	while($row=pg_fetch_array($resultado))
	{
		$nombresArticulos[$row['item_id']] = $row['item_nombre'];
	}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>SAFI::Movimiento por Art&iacute;culo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		
		<link href="../../../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link type="text/css" href="../../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet"/>
		
		<script type="text/javascript" src="../../../../js/lib/actb.js"></script>
		<script type="text/javascript" src="../../../../js/funciones.js"> </script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript" src="../../../../js/lib/jquery/plugins/jquery.min.js"></script>
		<script type="text/javascript">
		
			g_Calendar.setDateFormat('dd/mm/yyyy');

			$().ready(function(){

				// Llenar el input de artículos
				var nombresArticulos = new Array(<?php echo "'". implode("', '", $nombresArticulos) ."'" ?>);
				actb(document.getElementById('des_articulo'), nombresArticulos);

			});
			
			function detalle(codigo, nombre)
			{
				url = "alma_rep_e2.php?codigo=" + codigo + "&nombre=" + nombre;
				newwindow = window.open(url, 'name', 'height=500,width=700,scrollbars=yes');
				if (window.focus) { newwindow.focus(); }
			}


			function comparar_fechas(fecha_inicial,fecha_final) //Formato dd/mm/yyyy
			{ 	
				var fecha_inicial=document.form.txt_inicio.value;
				var fecha_final=document.form.hid_hasta_itin.value;
					
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
				  document.form.hid_hasta_itin.value='';
				  return;
				}
			}

			function activar()
			{
				if(document.form.hid_buscar[0].checked == true)
					document.form.ubicacion.disabled = false;
				else
					document.form.ubicacion.disabled = true;
			}

			function nombre_opcion() 
			{
				document.form.txt_articulo.value = document.form.des_articulo.options[document.form.des_articulo.selectedIndex].text;
			}
			
			function ejecutar(codigo1, codigo2, codigo3)
			{			
				if(document.form.txt_inicio.value==''){
					alert(" Debe indicar la fecha inicial para el reporte. ");
					return;
				}
				if(document.form.hid_hasta_itin.value==''){
					alert(" Debe indicar la fecha de final del rango para el reporte. ");
					return;
				}

				/* if(document.form.des_articulo.value==''){
					alert(" Debe especificar el art\u00EDculo a consultar. ");
					return;
				}*/
				
				var sw=0;
				
				for(i=0;i<2;i++)
				{
					if(document.form.hid_buscar[i].checked==true){ sw=1; }
				}

				if(sw==0){
					alert(" Debe seleccionar el tipo de reporte. ");
					return;
				}
					 
				if(document.form.hid_buscar[0].checked==true){
					document.form.hid_buscar.value=1;
				}
				else{
					document.form.hid_buscar.value=2;
				}

				document.form.hid_articulo.value=2; 
				document.form.submit();
			}
		</script>
	</head>
	<body>
		<form name="form" action="movimiento_articulo.php" method="post">
			<table width="729" align="center" <?php echo 'background="../../../imagenes/fondo_tabla.gif"' ?>  class="tablaalertas">
				<tr class="td_gray"> 
					<td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Control Interno</td>
				</tr>
				<tr>
					<td height="34" class="normalNegrita">Rango de Fecha:</td>
					<td  class="normalNegrita">
						<div align="left">
							<input type="text" size="10" id="txt_inicio" name="txt_inicio" class="dateparse"
								onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'txt_inicio');" title="Show popup calendar">
								<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
							<input type="text" size="10" id="hid_hasta_itin" name="hid_hasta_itin" class="dateparse"
								onfocus="javascript: comparar_fechas(this);" readonly="readonly"
							/>
							<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
								<img src="../../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
							</a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="normalNegrita">Art&iacute;culo:</td>
					<td>
						<input <?php echo 'autocomplete="off"' ?> size="60" type="text" id="des_articulo" name="des_articulo"
							value="<?php echo $nombre ?>"
						/>
						<input type="hidden" name="txt_articulo" value="" />
						<input type="hidden" name="hid_articulo" value="" />
					</td>
				</tr>
				<tr>
					<td class="normalNegrita" height="20">Tipo de Reporte:</td>
					<td class="normal">
						<input type="radio" name="hid_buscar" value="1" onchange="activar();" />
						Entradas/Salidas&nbsp;&nbsp;&nbsp;
						<input type="radio" name="hid_buscar" value="2" onchange="activar();" />
						Entradas/Proveedor
					</td>
				</tr>
				<tr>
					<td class="normalNegrita" height="20">Ubicaci&oacute;n:</td>
					<td>
						<select name="ubicacion" class="normal" id="ubicacion">
							<option value="0" selected="selected">[Seleccione]</option>
							<option value="1">Torre</option>
							<option value="2">Galp&oacute;n</option>
						</select>
					</td>
				</tr>
				<tr>
					<td height="52" colspan="3" align="center" class="normal">
						<div align="center">
							<input type="button" class="normalNegro" value="Buscar" onclick="javascript:ejecutar()" />
						</div>
					</td>
				</tr>
			</table>
		</form>
		<br />
		

<?php 
   if (($_POST['hid_articulo'])==2)
   {
   	?>
   	<script>
 		  document.form.txt_inicio.value="<? echo $_POST['txt_inicio']; ?>";
 		  document.form.hid_hasta_itin.value="<? echo $_POST['hid_hasta_itin']; ?>";
 		  document.form.des_articulo.value="<? echo $_POST['des_articulo']; ?>";

 		  var buscar="<? echo $_POST['hid_buscar'] ?>";
 		  if(buscar==1){
 			document.form.hid_buscar[0].checked=true;
 		  }
 		  else{
 			  document.form.hid_buscar[1].checked=true;
 			  }
	</script>
   	<?php 
   	$fecha_in=trim($_POST['txt_inicio']);
    $fecha_fi=trim($_POST['hid_hasta_itin']);
    $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
    $fecha_ini=substr($fecha_in,6,4)."-".substr($fecha_in,3,2)."-".substr($fecha_in,0,2)." 00:00:00";

    $condicion_arti='';
    if ($_POST['des_articulo']<>''){
     $nombre_arti=$_POST['des_articulo'];
     $sql_arti = "
		SELECT
			*
		FROM
			sai_seleccionar_campo('sai_item','id','nombre='||'''$nombre_arti''','',2) resultado_set (id varchar)
	";
     $resul_arti=pg_query($conexion,$sql_arti);
     if($rowa=pg_fetch_array($resul_arti)){
	   $codigo_arti=trim($rowa['id']);       	
     }
     $condicion_arti=" AND item.id='".$codigo_arti."' ";	  
    }  
    $condicion="";
    $condicion2="";
    $titulo="";
	if ($_POST['ubicacion']<>0){
		$condicion=" and ubicacion='".$_POST['ubicacion']."' ";
		if ($_POST['ubicacion']==1){
			$titulo=" en la torre ";
			$condicion2="
				UNION ALL 
					SELECT
						fecha_acta AS fecha,
						sum(cantidad),
						depe_id AS dependencia,
						'E' AS tipo,
						t1.acta_id,
						item.nombre AS articulo
					FROM
						sai_bien_custodia t1,
						sai_bien_item_custodia t2,
						sai_item item
					WHERE
						t2.id = item.id
						AND t1.acta_id = t2.acta_id
						AND t1.esta_id <> 15
						AND fecha_acta >= '".$fecha_ini."'
						AND fecha_acta<='".$fecha_fin."'
						".$condicion_arti."
					GROUP BY
						1,
						t1.acta_id,
						t2.id,
						item.nombre,
						depe_id,
						tipo
			";
		} else {
			$titulo=" en el galp&oacute;n ";
			$condicion2="
				UNION ALL 
					SELECT
						fecha_acta AS fecha,
						SUM(cantidad),
						depe_id AS dependencia,
						'S' AS tipo,
						t1.acta_id,
						item.nombre AS articulo
					FROM
						sai_bien_custodia t1,
						sai_bien_item_custodia t2,
						sai_item item
					WHERE
						t2.id = item.id
						AND t1.acta_id = t2.acta_id
						AND t1.esta_id <> 15
						AND fecha_acta >= '".$fecha_ini."' 
						AND fecha_acta <= '".$fecha_fin."'
						".$condicion_arti."
					GROUP BY
						1,
						t1.acta_id,
						t2.id,
						item.nombre,
						depe_id,
						tipo
			";
		}
	} else {
		$condicion2="
			UNION ALL 
				SELECT
					fecha_acta AS fecha,
					SUM(cantidad),
					depe_id AS dependencia,
					'S' AS tipo,
					t1.acta_id,
					item.nombre AS articulo 
				FROM
					sai_bien_custodia t1,
					sai_bien_item_custodia t2,
					sai_item item
				WHERE
					t2.id = item.id
					AND t1.acta_id = t2.acta_id
					AND t1.esta_id <> 15
					AND fecha_acta >= '".$fecha_ini."' 
					AND fecha_acta <= '".$fecha_fin."'
					".$condicion_arti."
				GROUP BY
					1,
					t1.acta_id,
					t2.id,
					item.nombre,
					depe_id,
					tipo 
					
			UNION ALL 

				SELECT
					fecha_acta AS fecha,
					SUM(cantidad),
					depe_id AS dependencia,
					'E' AS tipo,
					t1.acta_id,
					item.nombre AS articulo 
				FROM
					sai_bien_custodia t1,
					sai_bien_item_custodia t2,
					sai_item item
				WHERE
					t2.id = item.id
					AND t1.acta_id = t2.acta_id
					AND t1.esta_id <> 15
					AND fecha_acta >= '".$fecha_ini."'
					AND fecha_acta <= '".$fecha_fin."'
					".$condicion_arti."
					GROUP BY
						1,
						t1.acta_id,
						t2.id,
						item.nombre,
						depe_id,
						tipo
		";
    	
	}
    
	if (($_POST['hid_buscar'])==1)
	{
		$sql = "
				SELECT
					arti_almacen.alm_fecha_recepcion AS fecha,
					arti_almacen.cantidad,
					arti_almacen.depe_solicitante AS dependencia,
					'E' AS tipo,
					arti_almacen.acta_id,
					item.nombre AS articulo 
				FROM
					sai_arti_inco arti_inco
					INNER JOIN sai_arti_almacen arti_almacen ON (arti_almacen.acta_id = arti_inco.acta_id)
					INNER JOIN sai_item item ON (item.id = arti_almacen.arti_id)
				WHERE
					arti_inco.esta_id <> 15
					AND arti_almacen.alm_fecha_recepcion >= '".$fecha_ini."'
					AND arti_almacen.alm_fecha_recepcion<='".$fecha_fin."'
		  			".$condicion_arti."
		  			".$condicion."
		  		GROUP BY
		  			arti_almacen.alm_fecha_recepcion,
		  			arti_almacen.acta_id,
		  			arti_almacen.arti_id,
		  			arti_almacen.cantidad,
		  			arti_almacen.depe_solicitante,
		  			tipo,
		  			item.nombre
		  	
		  	".$condicion2."
		  			
			UNION ALL
			 
				SELECT
					arti_acta_almacen.fecha_acta AS fecha,
					SUM(arti_salida.cantidad),
					arti_acta_almacen.depe_entregada AS dependencia,
					arti_acta_almacen.tipo,
					arti_acta_almacen.amat_id AS acta_id,
					item.nombre AS articulo
				FROM
					sai_arti_acta_almacen arti_acta_almacen
					INNER JOIN sai_arti_salida arti_salida ON (arti_salida.n_acta = arti_acta_almacen.amat_id)
					INNER JOIN sai_item item ON (item.id = arti_salida.arti_id)
				WHERE
					arti_acta_almacen.esta_id <> 15
					AND arti_acta_almacen.fecha_acta >= '".$fecha_ini."'
					AND arti_acta_almacen.fecha_acta <= '".$fecha_fin."'
					".$condicion_arti."
					".$condicion."
				GROUP BY
					1,
					arti_acta_almacen.amat_id,
					arti_salida.arti_id,
					item.nombre,
					arti_acta_almacen.depe_entregada,
					arti_acta_almacen.tipo
					
			ORDER BY
				fecha
		";
		
		$resultado=pg_query($conexion,$sql) or die("Error al crear la tabla temporal");
		$movimientosArticulos = array();
		$idsDependencias = array();
		$dependencias = array();

		while ($row=pg_fetch_array($resultado)){
      		$movimientosArticulos[] = $row;
      		$idsDependencias[$row['dependencia']] = $row['dependencia'];
		}
		
		if(count($idsDependencias) > 0){
			
			$sql = "
				SELECT
					depe_id AS id_dependencia,
					depe_nombrecort AS nombre_corto_dependencia
				FROM
					sai_dependenci
				WHERE
					depe_id IN ('".implode("' ,'", $idsDependencias)."')
			";
		
			$resultado=pg_query($conexion, $sql) or die("Error al mostrar consulta de la dependencia");
			
			while ($row=pg_fetch_array($resultado)){
				$dependencias[$row['id_dependencia']] = $row;
			}
		}
		
       if(count($movimientosArticulos) == 0) 
	   {?><center> 
  		<span style="color: #003399" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</span></center>
	    <div align="center">
  		<?php 
	   }else 
	     { ?><br />
  </div>
  <div align="center"  class="normalNegroNegrita">Control interno <?=$titulo;?> del art&iacute;culo <?php echo strtoupper($nombre_arti);?> del <?php echo $fecha_in;?> al <?php echo $fecha_fi;?></div>
<table width="800" background="../../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">    
  <tr class="td_gray">
	<td colspan="5" align="center" bgcolor="#FFFFFF" class="normalNegrita style2">
	 <table width="100%" border="0" class="tablaalertas">
      <tr class="td_gray">
        <th width="59" class="normalNegroNegrita" scope="col">Fecha</th>
        <th width="55" class="normalNegroNegrita" scope="col">Dependencia</th>
        <th width="55" class="normalNegroNegrita" scope="col">Acta</th>
        <th width="55" class="normalNegroNegrita" scope="col">Material</th>
        <th width="58" class="normalNegroNegrita" scope="col">Entradas</th>
        <th width="58" class="normalNegroNegrita" scope="col">Salidas</th>
        <th width="58" class="normalNegroNegrita" scope="col">Devoluciones</th>                       
     </tr>
	<?php		   
		$i=0;
		$total_entradas=0;
		$total_salidas=0;
		$total_devolucion=0;
		foreach ($movimientosArticulos AS $rowt1)
		{
			$i++;
			$cantidad=trim($rowt1['cantidad']);
			$fecha=substr($rowt1['fecha'],8,2).'/'.substr($rowt1['fecha'],5,2).'/'.substr($rowt1['fecha'],0,4);
			
			if(trim($rowt1['tipo']=='E')){ 
				$movimiento='Entrada'; 
				$total_entradas=$total_entradas+$cantidad;
				if (substr($rowt1['acta_id'],0,1)=="c")
					$pagina="../../bienes/custodia";
				else 
					$pagina="entradas";
			}
			if(trim($rowt1['tipo']=='S')){ 
				$movimiento='Salida'; 
				$total_salidas=$total_salidas+$cantidad;
				if (substr($rowt1['acta_id'],0,1)=="c")
					$pagina="../../bienes/custodia";
				else
					$pagina="salidas";
			}
			if(trim($rowt1['tipo']=='D')){ 
				$movimiento=utf8_decode('Devolución'); 
				$total_devolucion=$total_devolucion+$cantidad;
				$pagina="devoluciones";
			}
	?>
  <tr>
    <td><div align="center"><span class="normal"><?=$fecha?></span></div></td>
	<td><div align="left"><span class="normal">
		<?php echo isset($dependencias[$rowt1['dependencia']]) ? $dependencias[$rowt1['dependencia']]['nombre_corto_dependencia'] : "" ?>
	</span></div></td>
	<td><div align="left"><span class="normal">
	 <a href="javascript:abrir_ventana('../<?=$pagina?>_pdf.php?id=<?php echo trim($rowt1['acta_id']); ?>&codigo=<?php echo trim($rowt1['acta_id']);?>')" class="copyright">
		<?php echo $rowt1['acta_id'];?>
	</a></span></div></td>
	<td><span class="normal"><?php echo $rowt1['articulo']; ?></span></td>  
    <?php if(trim($rowt1['tipo']=='E')){ ?>
    <td><div align="right"><span class="normal"><?php echo $cantidad; ?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<?php } 
	  if(trim($rowt1['tipo']=='S')){?>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo $cantidad;?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<?php }
	  if(trim($rowt1['tipo']=='D')){?>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo "-"; ?></span></div></td>
	<td><div align="right"><span class="normal"><?php echo $cantidad;?></span></div></td>
	<?php }}?>
  </tr>
  <tr>
	<td colspan="4"><div align="left" class="normal"><b>Totales:</b></div></td>
    <td><div align="right" class="normal"><b><?php echo $total_entradas;?></b></div></td>
    <td><div align="right" class="normal"><b><?php echo $total_salidas;?></b></div></td>
    <td><div align="right" class="normal"><b><?php echo $total_devolucion;?></b></div></td>
  </tr>
</table></td>
  </tr>
  <tr>
	<td colspan="6" align="center" class="normalNegrita">
	<br /><span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?>
    </span>
	<!-- <a href="movimiento_articulo_pdf.php?des_articulo=<?php echo($_POST['des_articulo']); ?>&txt_articulo=<?php echo $codigo_arti; ?>&txt_inicio=<?php echo $_POST['txt_inicio'];?>&hid_hasta_itin=<?php echo $_POST['hid_hasta_itin'];?>&hid_buscar=1"><img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a> -->
	<br></br><span class="link"><a href="javascript:window.print()" class="link"><img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></span>
	<br></br><span class="link">Imprimir Documento</span><br />
	<br /><br />			</td>
  </tr>
</table>
   <?php
    }
	   	
	 }
     else {
			$sql = "
				SELECT
					cantidad,
					precio,
					prov_id_rif
				FROM
					sai_arti_inco arti_inco
					INNER JOIN sai_arti_almacen arti_almacen ON (arti_almacen.acta_id = arti_inco.acta_id)
				WHERE
					arti_inco.esta_id <> 15
					AND arti_id='".$codigo_arti."'
					AND alm_fecha_recepcion >= '".$fecha_ini."'
					AND alm_fecha_recepcion <= '".$fecha_fin."'
				ORDER BY
					arti_almacen.alm_fecha_recepcion
			";
			
      	   $resultado=pg_query($conexion,$sql) or die("Error al crear la tabla temporal"); 

	      if (($rowt1=pg_fetch_array($resultado)) == null) 
	  	  {?><center> 
  		  <span style="color: #003399;" class="normalNegrita">No existen registros que cumplan con el criterio de b&uacute;squeda seleccionado</span></center>
	 
<div align="center">
<?php 
 }else{ ?><br />
  </div>
<table width="630" background="../../../../imagenes/fondo_tabla.gif" align="center" border="0" class="tablaalertas">
  <tr class="td_gray">
    <td width="622" colspan="6" align="center" class="normalNegrita style2"><span class="titularMedio">REPORTE DE MOVIMIENTOS DEL ART&Iacute;CULO: <?php echo strtoupper($nombre_arti);?> DEL <?php echo $fecha_in;?> AL <?php echo $fecha_fi;?></span></td>
  </tr>
  <tr class="td_gray">
	<td colspan="6" align="center" bgcolor="#FFFFFF" class="normalNegrita style2">&nbsp;</td>
  </tr>
  <tr class="td_gray">
    <td colspan="6" align="center" bgcolor="#FFFFFF" class="normalNegrita style2">
     <table width="613" border="0" class="tablaalertas">
       <tr>
         <th bgcolor="#F0F0F0" class="peqPeqNegrita" scope="col"><span class="peqNegrita">R.I.F.</span></th>
         <th bgcolor="#F0F0F0" class="peqPeqNegrita" scope="col">Proveedor</th>
         <th bgcolor="#F0F0F0" class="peqPeqNegrita" scope="col">Cantidad</th>
         <th bgcolor="#F0F0F0" class="peqPeqNegrita" scope="col">Costo unitario</th>
         <th bgcolor="#F0F0F0" class="peqPeqNegrita" scope="col">Costo total</th>                                       
       </tr>
	   <?php
		   $resultado_set_t1=pg_query($conexion,$sql) or die("Error al mostrar consulta en sai_arti_existen");
		   $total=0;
	       while ($rowt1=pg_fetch_array($resultado_set_t1)) 
		   {
		      $cantidad=trim($rowt1['cantidad']);
   
               if ($rowt1['prov_id_rif']==''){
               	$rif="--";
               	$nombre="--";
               }else{
               	 $rif=$rowt1['prov_id_rif'];
				$sql_p="
					SELECT
						*
					FROM
						sai_seleccionar_campo('sai_proveedor_nuevo','prov_nombre','prov_id_rif='||'''$rif''','',2)
						resultado_set(prov_nombre varchar)
				"; 
                 $resultado=pg_query($conexion,$sql_p) or die("Error al mostrar");
	            if($row=pg_fetch_array($resultado)){
	             $nombre=$row['prov_nombre'];	
	            }
               }
		 ?>
       <tr>
         <td><div align="center" class="normal"><?php echo $rif;?></div></td>
         <td><div align="left" class="normal"><? echo $nombre;?></div></td>
         <td><div align="right" class="normal"><?php echo $cantidad; ?></div></td>
		 <td><div align="right" class="normal"><b><?php echo str_replace('.',',',$rowt1['precio']);?></b></div></td>
         <td><div align="right" class="normal"><b><?php echo number_format($rowt1['precio']*$cantidad,2,',','.');?></b></div></td>                
        </tr>
        <?php
          $total=$total+$rowt1['precio']*$cantidad;
		 }?>
        <tr>
          <td colspan="4"><div align="left" class="normal"><b><?php echo "Totales:";?></b></div></td>
          <td><div align="right" class="normal"><b><?php echo number_format($total,2,',','.');?></b></div></td>
        </tr>
     </table>			</td>
        </tr>
	    <tr>
			<td colspan="6" align="center" class="normalNegrita">
			<br />
			<span class="peq_naranja">Detalle generado  el d&iacute;a <?=date("d/m/y")?> a las <?=date("h:i:s")?><br /><br /></span><br />
     	    <br />   
	        <a href="movimiento_articulo_pdf.php?des_articulo=<?php echo($_POST['des_articulo']); ?>&txt_articulo=<?php echo $codigo_arti; ?>&txt_inicio=<?php echo $_POST['txt_inicio'];?>&hid_hasta_itin=<?php echo $_POST['hid_hasta_itin'];?>&hid_buscar=2;?>"><img src="../../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0" /></a>
		    <br />
			<br /><span class="link"><a href="javascript:window.print()" class="link"><img src="../../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a></span>
			<br /><span class="link">Imprimir Documento</span><br />
			<br />
			<br /><br /><br /></td>
			</tr>
  </table>
<?php 	
  }
	 }
   }
?>

</body>
</html>
<?php pg_close($conexion);?>
