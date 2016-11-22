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
	
	$inventario = null;
	
	if (($_POST['hid_buscar'])==2)
	{
		$totales = $_POST['totales']; // 1 = General, 2 => Detallado
		$fecha_fi=trim($_POST['hid_hasta_itin']);
		$f=$_POST['hid_hasta_itin'];
		$fechaFin = substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
		
		if($totales == "1"){ // General
			$queryInventarioSelect = "
				item_particular.bien_id AS id_bien,
				item.nombre AS nombre_item,
				count(item.nombre) AS existencia
			";
			
			$queryInventarioGroupBy = "
				item_particular.bien_id,
				item.nombre
			";
			
			$queryInventarioOrderBy = "
				item.nombre
			";
			
		} else { // Detallado
			$queryInventarioSelect = "
				item_particular.bien_id AS id_bien,
				item.nombre AS nombre_item,
				count(item.nombre) AS existencia,
				item_particular.modelo,
				marca.bmarc_nombre AS nombre_marca,
				marca.bmarc_id AS id_marca,
				item_bien.existencia_minima
			";
			
			$queryInventarioGroupBy = "
				item_particular.bien_id,
				marca.bmarc_nombre,
				marca.bmarc_id,
				item_particular.modelo,
				item.nombre,
				item_bien.existencia_minima
			";
			
			$queryInventarioOrderBy = "
				item.nombre,
				marca.bmarc_nombre,
				item_particular.modelo
			";
		}
		

		$queryInventario = "
			SELECT
				".$queryInventarioSelect."
			FROM
				sai_bien_inco bien_inco		
				INNER JOIN sai_biin_items item_particular
					ON (bien_inco.acta_id = item_particular.acta_id)
				INNER JOIN sai_item item ON (item.id = item_particular.bien_id)			
				INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)						
				INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
						
			WHERE
				bien_inco.esta_id != 15 AND 
				item_particular.esta_id != 15 AND 
				item_particular.fecha_entrada <= '".$fechaFin."'
				AND item_particular.etiqueta NOT IN
				(
				SELECT
					COALESCE(asignacion.etiqueta, '') || COALESCE(reasignacion.etiqueta, '') AS etiqueta
				FROM
					(
					SELECT
						tabla.etiqueta,
						max(tabla.fecha_acta) AS fecha_acta
					FROM
						(
						SELECT
							asignacion.asbi_fecha AS fecha_acta,
							asignacion.esta_id AS id_estatus_acta,
							item_particular.bien_id AS id_bien,
							item_particular.modelo AS modelo,
							item_particular.marca_id AS id_marca,
							item_particular.etiqueta AS etiqueta
						FROM
							sai_bien_asbi asignacion
							INNER JOIN sai_bien_asbi_item asignacion_item
								ON (asignacion_item.asbi_id = asignacion.asbi_id)
							INNER JOIN sai_biin_items item_particular
								ON (item_particular.clave_bien = asignacion_item.clave_bien)
			
						UNION
						
						SELECT
							reasignar.fecha_acta AS fecha_acta,
							reasignar.esta_id AS id_estatus_acta,
							item_particular.bien_id AS id_bien,
							item_particular.modelo AS modelo,
							item_particular.marca_id AS id_marca,
							item_particular.etiqueta AS etiqueta
						FROM
							sai_bien_reasignar reasignar
							INNER JOIN sai_bien_reasignar_item reasignar_item
								ON (reasignar.acta_id = reasignar_item.acta_id)
							INNER JOIN sai_biin_items item_particular
								ON (item_particular.clave_bien = reasignar_item.clave_bien)
						) AS tabla
					WHERE
						tabla.fecha_acta <= '".$fechaFin."'
						AND tabla.id_estatus_acta <> 15
					GROUP BY
						tabla.etiqueta
					) fuera_inventario
					
					LEFT JOIN
			
					(
					SELECT
						asignacion.asbi_id AS id_acta,
						asignacion.asbi_fecha AS fecha_acta,
						item_particular.etiqueta
						
					FROM
						sai_bien_asbi asignacion
						INNER JOIN sai_bien_asbi_item asignacion_item
							ON (asignacion_item.asbi_id = asignacion.asbi_id)
						INNER JOIN sai_biin_items item_particular
							ON (item_particular.clave_bien = asignacion_item.clave_bien)
					) asignacion ON (
							asignacion.etiqueta = fuera_inventario.etiqueta
							AND asignacion.fecha_acta = fuera_inventario.fecha_acta
							)
			
					LEFT JOIN
			
					(
					SELECT
						reasignar.fecha_acta AS fecha_acta,
						reasignar.esta_id AS id_estatus_acta,
						reasignar.tipo AS tipo,
						item_particular.etiqueta AS etiqueta
					FROM
						sai_bien_reasignar reasignar
						INNER JOIN sai_bien_reasignar_item reasignar_item
							ON (reasignar.acta_id = reasignar_item.acta_id)
						INNER JOIN sai_biin_items item_particular
							ON (item_particular.clave_bien = reasignar_item.clave_bien)
					) reasignacion ON (
							reasignacion.etiqueta = fuera_inventario.etiqueta
							AND reasignacion.fecha_acta = fuera_inventario.fecha_acta
							)
				WHERE
					asignacion.id_acta LIKE 'a-%'
					OR
					(reasignacion.id_estatus_acta = '9' AND reasignacion.tipo <> 3)
					OR reasignacion.id_estatus_acta <> '9'
				)
			GROUP BY
				".$queryInventarioGroupBy."
			ORDER BY
				".$queryInventarioOrderBy."
		";
		
		$resultadoInventario = pg_query($conexion, $queryInventario) or die("Error al consultar lista de activos");
		
		$arrInventario = array();
		$idsBien = array();
		
		while($rowInventario = pg_fetch_array($resultadoInventario))
		{
			$arrInventario[] = $rowInventario;
			$idsBien[] = $rowInventario['id_bien'];
		}
		
		// Si totales General = 1
		if($totales == "1" && count($idsBien) > 0){
			$queryDistribucion = "
				SELECT
					item_particular.bien_id AS id_bien,
					count(
						CASE item_particular.ubicacion
							WHEN 2 THEN 1
							ELSE NULL
						END
					) AS en_galpon,
					count(
						CASE item_particular.ubicacion
							WHEN 1 THEN 1
							ELSE NULL
						END
					) AS en_torre
				FROM
					sai_item item
					INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
					INNER JOIN sai_biin_items item_particular
						ON (item.id = item_particular.bien_id)
					INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
				WHERE
					item_particular.bien_id IN ('".implode("', '", $idsBien)."')
					AND item_particular.esta_id = '41'
				GROUP BY
					item_particular.bien_id,
					item.nombre
				ORDER BY
					item.nombre
			";
			
			$resultadoDistribucion = pg_query($conexion, $queryDistribucion)
				or die("Error al consultar lista de activos en galpo y/o torre.");
			
			while($rowDistribucion = pg_fetch_array($resultadoDistribucion))
			{
				$arrDistribucion[$rowDistribucion['id_bien']] = $rowDistribucion;
			}
		}
	}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Inventario de Activos Existente</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<script languaje="JavaScript" SRC="../../../includes/js/funciones.js"> </script>
		<script languaje="JavaScript" SRC="../../../includes/js/CalendarPopup.js"> </script>
		
		<link type="text/css" href="../../../js/lib/calendarPopup/css/calpopup.css" media="screen" rel="stylesheet" />
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/events.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/calpopup.js"></script>
		<script type="text/javascript" src="../../../js/lib/calendarPopup/js/dateparse.js"></script>
		<script type="text/javascript">
			g_Calendar.setDateFormat('dd/mm/yyyy');
		</script>
		
		<script language="javascript">
			function detalle(codigo,nombre)
			{
				url="alma_rep_e1.php?codigo="+codigo+"&nombre="+nombre
					newwindow=window.open(url,'name','height=500,width=700,scrollbars=yes');
				if (window.focus) {newwindow.focus()}
			}
			
			function ejecutar(codigo1)
			{
				if (codigo1=='')
				{
					document.form1.hid_buscar.value=1;
				}
				else{
					document.form1.hid_buscar.value=2;
				}
				document.form1.submit();
			  //window.location="inventario_activos.php?hid_hasta_itin="+codigo1+"&hid_buscar="+document.form1.hid_buscar.value
			}

		</script>
	</head>
	
	<body>
		<form name="form1" action="" method="post">
			<br />
			<table width="500" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
				<tr class="td_gray" > 
					<td height="15" colspan="3" valign="middle" class="normalNegroNegrita">Inventario</td>
				</tr>
				<tr>
					<td width="259" height="34" align="left" class="normalNegrita">Fecha de corte:</td>
					<td width="306"  class="normal">
						<input NAME="hid_hasta_itin" value="<?php
							if(isset($fecha_fi) && trim($fecha_fi) != "")
								echo $fecha_fi;
							else
								echo date('d/m/Y')
							?>"  id="hid_hasta_itin" TYPE="text" size="10"
							class="dateparse" value="" onClick="cal1xx4.select(this,'anchor1xx4','dd/MM/yyyy'); return false;" readonly
						> 
				 		<a href="javascript:void(0);" onclick="g_Calendar.show(event, 'hid_hasta_itin');" title="Show popup calendar">
							<img src="../../../js/lib/calendarPopup/img/calendar.gif" class="cp_img" alt="Open popup calendar"/>
						</a>
						<input type="hidden" name="hid_buscar" id="hid_buscar" value="0">
					</td>
				</tr>
				<tr>
				  	<td width="259" height="34" align="left" class="normalNegrita">Tipo:</td>
					<td width="406" class="normalNegrita">
						<input type="radio" name="totales" value="1" <?php
							if(isset($totales)){
								if($totales == "1"){
									echo 'checked="checked"';
								}
							} else {
								echo 'checked="checked"';
							}
						?>>General</input>&nbsp;
						<input type="radio" name="totales" value="2" <?php
							if(isset($totales) && $totales == "2")
								echo 'checked="checked"';
						?>>Detallado</input>
					</td>
				</tr>
				<tr>
					<td height="52" colspan="3" align="center">
						<input type="button" class="normalNegro" value="Buscar"
							onclick="javascript:ejecutar(document.form1.hid_hasta_itin.value)"
						>
					</td>
				</tr>
			</table>
		</form>
		<br>
		
		<?php
			if($arrInventario !== null && is_array($arrInventario) && count($arrInventario) > 0){
				echo '
		<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
			<tr>
				<td>
					<div align="center"><span class="normalNegroNegrita">
						Inventario a la fecha '.$fecha_fi.'
					</span></div>
				</td>
			</tr>
			<tr>
    			<td colspan="5">
    				<table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
						<tr class="td_gray">
					        <td width="41"><div align="center"><span class="normalNegroNegrita">#</span></div></td>
					        <td width="107" height="25"><div align="center" class="normalNegroNegrita">C&oacute;digo</div></td>
					        <td width="321"><div align="center" class="normalNegroNegrita">Nombre</div></td>
		        ';
				
				if($totales == "2"){ // Detallado
					echo '
							<td width="78"><div align="center"><span class="normalNegroNegrita">Marca</span></div></td>
				        	<td width="78"><div align="center"><span class="normalNegroNegrita">Modelo </span></div></td>
					';
				}
				
				echo '
							<td width="70"><div align="center"><span class="normalNegroNegrita">Existencia</span></div></td>
				';
				
				if($totales == "1"){ // General
					echo '
							<td width="70"><div align="center">
								<span class="normalNegroNegrita">Galp&oacute;n al '. date("d/m/y").'
							</span></div></td>
				    		<td width="70">
				    			<div align="center"><span class="normalNegroNegrita">Torre al '.date("d/m/y").'</span></div>
				    		</td>
					';
				}
				
				echo '
						</tr>
				';
				
				$i = 0;
				foreach ($arrInventario AS $inventario){
					echo '
						<tr>
							<td bordercolor="1"><div align="right" class="normal">'.(++$i).'</div></td>
							<td height="21" bordercolor="1"><div align="right" class="normal">'.$inventario['id_bien'].'</div></td>
					';
					
					if($totales == "1"){ // General
						echo '
							<td bordercolor="1"><div align="left" class="normal">
						        <a href="detalle_activos.php?desc='.$inventario['nombre_item'].'&id='.$inventario['id_bien']
						        	.'&fecha='.$fechaFin.'"
						        >
						        	'.strtoupper($inventario['nombre_item']).'
						        </a></div>
							</td>
						';
					} else {
						echo '
							<td bordercolor="1"><div align="left" class="normal">
							     <a href="detalle_seriales.php?desc='.$inventario['nombre_item'].'&id='.$inventario['id_bien']
							     	.'&fecha='.$fechaFin.'&modelo='.$inventario['modelo'].'&marca='.$inventario['id_marca'].'"
							     >
							    	'.strtoupper($inventario['nombre_item']).'
							    </a>
							</td>
							<td bordercolor="1">
								<div align="left"><span class="normal">'.strtoupper($inventario['nombre_marca']).'</span></div>
							</td>        
			        		<td bordercolor="1">
			        			<div align="left"><span class="normal">'.strtoupper($inventario['modelo']).'</span></div>
			        		</td>
						';
					}
					
					echo '
							<td bordercolor="1"><div align="right"><span class="normal">'.$inventario['existencia'].'</span></div></td>
					'; 
					
					if($totales == "1"){
						echo '
							<td bordercolor="1"><div align="right"><span class="normal">
								'.($arrDistribucion[$inventario['id_bien']]
										? $arrDistribucion[$inventario['id_bien']]['en_galpon'] : "--" ).'
							</span></div></td>
							<td bordercolor="1"><div align="right"><span class="normal">
								'.($arrDistribucion[$inventario['id_bien']]
										? $arrDistribucion[$inventario['id_bien']]['en_torre'] : "--" ).'
							</span></div></td>
						';									     
					}
					
					echo '
						</tr>
					';
				}
				
				echo '
					</table>
				</td>
			</tr>
			
			<tr>
				<td height="16" colspan="5" class="normal">
					<div align="center">
						<br />
						<span class="peq_naranja">
							Detalle generado  el d&iacute;a '.date("d/m/y").' a las '.date("H:i:s").'<br /><br /><br />
							<a href="inventario_activos_pdf.php?ff='.$fecha_fi.'&totales='.$totales.'">
								<img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0">
							</a>
				 			<br /><br />
							<span class="link">Imprimir Documento</span>
						</span>
						<br /><br /><br />
    				</div>
				</td>
			</tr>
		</table>
				';
			}
		?>
		
	</body>
</html>
<?php















exit();













if (($_POST['hid_buscar'])==1)
{
 echo utf8_decode("<SCRIPT LANGUAGE='JavaScript'>"."alert ('Debe seleccionar una fecha de corte');"."</SCRIPT>");
}
else {
if ($_POST['hid_buscar']==2){?>
<form name="form" action="inventario_activos" method="post">
<?php 
 $fecha_fi=trim($_POST['hid_hasta_itin']); 
 $f=$_POST['hid_hasta_itin'];
 $fecha_fin=substr($fecha_fi,6,4)."-".substr($fecha_fi,3,2)."-".substr($fecha_fi,0,2)." 23:59:59";
 if ($_POST['totales']==1){
	$sql_ar="
	  	SELECT
			nombre,
			count(t1.id) as cantidad,
			existencia_minima,
			t1.id
		FROM sai_item t1,
			sai_item_bien t3,
			sai_biin_items t2
		WHERE
			t1.id=t3.id
			and t1.id=t2.bien_id
			and fecha_entrada <='".$fecha_fin."'
		group by
			t1.id,
			t1.nombre,
			existencia_minima
		order
			by nombre
	";
 }else{
	$sql_ar="
		SELECT
			t1.nombre,
			marca.bmarc_nombre,
			t2.modelo,
			count(t1.id) as cantidad,
			t3.existencia_minima,
			t1.id,
			marca.bmarc_id
		FROM
			sai_item t1,
			sai_item_bien t3,
			sai_biin_items t2,
			sai_bien_marca marca
		WHERE
			t1.id = t3.id
			and t1.id = t2.bien_id
			and fecha_entrada <= '".$fecha_fin."'
			and  bmarc_id = marca_id
		group by
			t1.id,
			marca.bmarc_nombre,
			t2.modelo,
			t1.nombre,
			t3.existencia_minima,
			marca.bmarc_id
		order by
		nombre
	"; 
 
 }
 //echo $sql_ar."<br>";
  $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de activos");  
if($row=pg_fetch_array($resultado_set_most_ar))
  {
?>
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
  <?if (($_POST['hid_buscar'])==1){?>

<div align="center"><span class="normalNegroNegrita">Inventario a la fecha <?=date("d/m/y")?> a las <?=date("H:i:s")?></span>
    </div>
  <?}else{?>
<div align="center"><span class="normalNegroNegrita">Inventario a la fecha 
    <?$fecha_fi=trim($_POST['hid_hasta_itin']); 
      echo $fecha_fi;?>
	</span>
    </div><?}?>
  <tr>
    <td colspan="5">
    <table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr class="td_gray">
        <td width="41"><div align="center"><span class="normalNegroNegrita">#</span></div></td>
        <td width="107" height="25"><div align="center" class="normalNegroNegrita">C&oacute;digo</div></td>
        <td width="321"><div align="center" class="normalNegroNegrita">Nombre</div></td>
        <?php if ($_POST['totales']==2){?>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Marca</span></div></td>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Modelo </span></div></td>  
       <?php }?>
        <td width="70"><div align="center"><span class="normalNegroNegrita">Existencia</span></div></td>
	 	<?php if ($_POST['totales']==1){?>
	 	<td width="70"><div align="center"><span class="normalNegroNegrita">Galp&oacute;n al <?= date("d/m/y");?></span></div></td>
    	<td width="70"><div align="center"><span class="normalNegroNegrita">Torre al <?= date("d/m/y");?></span></div></td>
    	<?php }?>
        </tr>
      <?php
		$i=1;
		//echo $sql_ar;
		$resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
		while($row=pg_fetch_array($resultado_set_most_ar)) 
		{	
			$cant_salida=0;
			$cantidad_existencia=0;
			if ($_POST['totales']==1){
				$salidas="
					select
						count (t2.clave_bien) as cantidad_salida
					from
						sai_bien_asbi t1,
						sai_bien_asbi_item t2,
						sai_biin_items t3
					where
						asbi_fecha<='".$fecha_fin."'
						and t1.esta_id<>15
						and t1.asbi_id=t2.asbi_id
						and t2.clave_bien=t3.clave_bien
						and bien_id='".$row['id']."'
				";
				$resultado_salida=pg_query($conexion,$salidas);  
				if($row_salida=pg_fetch_array($resultado_salida)){
					$cant_salida=$row_salida['cantidad_salida'];	
				}
			}else{
				$salidas="
					select
						count (t2.clave_bien) as cantidad_salida
					from
						sai_bien_asbi t1,
						sai_bien_asbi_item t2,
						sai_biin_items t3
					where
						asbi_fecha<='".$fecha_fin."'
						and t1.esta_id<>15
						and t1.asbi_id=t2.asbi_id
						and t2.clave_bien=t3.clave_bien
						and bien_id='".$row['id']."'
						and modelo='".$row['modelo']."'
						and marca_id='".$row['bmarc_id']."'
				";
				$resultado_salida=pg_query($conexion,$salidas);  
				if($row_salida=pg_fetch_array($resultado_salida)){
					$cant_salida=$row_salida['cantidad_salida'];	
				}
	       }
	       $cantidad_existencia=$row['cantidad']-$cant_salida;
	       if ($cantidad_existencia>0){
		 ?>
      <tr>
        <td bordercolor="1"><div align="right" class="normal"><?php echo $i;?></div></td>
        <td height="21" bordercolor="1"><div align="right" class="normal"><?php echo $row['id'];?></div></td>
        <?php if ($_POST['totales']==1){?>
        <td bordercolor="1"><div align="left" class="normal">
        <a href="detalle_activos.php?desc=<?php echo $row['nombre'];?>&id=<?php echo $row['id'];?>&fecha=<?php echo $fecha_fin;?>"><?php echo strtoupper($row['nombre']);?></a></div></td>
        <?php  
        }
        else{
        if ($_POST['totales']==2){?>
	    <td bordercolor="1"><div align="left" class="normal">
	     <a href="detalle_seriales.php?desc=<?php echo $row['nombre'];?>&id=<?php echo $row['id'];?>&fecha=<?php echo $fecha_fin;?>&modelo=<?php echo $row['modelo'];?>&marca=<?php echo $row['bmarc_id'];?>">
	    <?php echo strtoupper($row['nombre']);?></div></td>
	    </a>
        <td bordercolor="1"><div align="left"><span class="normal"><?php echo strtoupper($row['bmarc_nombre']);?> </span></div></td>        
        <td bordercolor="1"><div align="left"><span class="normal"><?php echo strtoupper($row['modelo']);?> </span></div></td>
       <?php 
        
        }}
        ?>
         <td bordercolor="1"><div align="right"><span class="normal"><?php echo $cantidad_existencia;?> </span></div></td>
      <?php 
      $torre=0;
     $sql_torre="SELECT nombre,count(t1.id)as cantidad,existencia_minima,t1.id 
      FROM sai_item t1, sai_item_bien t3, sai_biin_items t2 
      WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$fecha_fin."' and 
      t2.esta_id=41 and ubicacion=1 and t1.id='".$row['id']."' group by t1.id,t1.nombre,existencia_minima order by nombre";
      $resultado_torre=pg_query($conexion,$sql_torre) or die("Error al consultar lista de activos");  
      if($row_torre=pg_fetch_array($resultado_torre))
      {
      	$torre=$row_torre['cantidad'];
      }
      $galpon=0;
		$sql_galpon="
			SELECT
				nombre,
				count(t1.id) as cantidad,
				existencia_minima,
				t1.id 
      		FROM
      			sai_item t1,
      			sai_item_bien t3,
      			sai_biin_items t2 
			WHERE
				t1.id=t3.id
				and t1.id=t2.bien_id
				and fecha_entrada <='".$fecha_fin."'
				and t2.esta_id=41
				and ubicacion=2
				and t1.id='".$row['id']."'
			group by
				t1.id,
				t1.nombre,
				existencia_minima
			order by nombre
		";
      $resultado_galpon=pg_query($conexion,$sql_galpon) or die("Error al consultar lista de activos");  
      if($row_galpon=pg_fetch_array($resultado_galpon))
      {
      	$galpon=$row_galpon['cantidad'];
      }
       if ($_POST['totales']==1){?>
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo $galpon;?></span></div></td>
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo $torre;?> </span></div></td>
      <?php }?>
      </tr>
      <?php }
      $i++;
       }     
   	       ?>
    </table></td>
  </tr>
  <?php //}?>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado  el d&iacute;a 
              <?=date("d/m/y")?>
 a las
<?=date("H:i:s")?>
<br />
<br />
<br />
<!-- <a href="javascript:window.print()"><img src="../../imagenes/bimprimir_off.gif" width="100" height="27"></a> 
<span class="link"><a href="javascript:detalle('<?=$codigo_part?>','<?=$nombre_part?>')" class="link">Imprimir Documento</a></span> </span><br />-->
<a href="inventario_activos_pdf.php?ff=<?php echo $fecha_fi;?>&hid_buscar=<?php echo $_POST['totales'];?>"><img src="../../../imagenes/pdf_ico.jpg" width="32" height="32" border="0"></a>
			 <br><br>

<span class="link">Imprimir Documento</span> </span><br />
            <br />
     
      <br />
    </div></td>
  </tr>
</table>
<?php } ?>
</form>


	
	
<?php }}?>

<?php
/*
_______________________________________
Query Detallado
_______________________________________

SELECT
	item_particular.bien_id AS id_bien,
	item.nombre AS nombre_item,
	count(item.nombre) AS esxistencia,
	item_particular.modelo,
	marca.bmarc_nombre AS nombre_marca,
	item_bien.existencia_minima
FROM
	sai_item item
	INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
	INNER JOIN sai_biin_items item_particular
		ON (item.id = item_particular.bien_id)
	INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
WHERE
	item_particular.fecha_entrada <= '2012-09-11 23:59:59'
	AND etiqueta NOT IN
	(
	SELECT
		COALESCE(asignacion.etiqueta, '') || COALESCE(reasignacion.etiqueta, '') AS etiqueta
	FROM
		(
		SELECT
			tabla.etiqueta,
			max(tabla.fecha_acta) AS fecha_acta
		FROM
			(
			SELECT
				asignacion.asbi_fecha AS fecha_acta,
				asignacion.esta_id AS id_estatus_acta,
				item_particular.bien_id AS id_bien,
				item_particular.modelo AS modelo,
				item_particular.marca_id AS id_marca,
				item_particular.etiqueta AS etiqueta
			FROM
				sai_bien_asbi asignacion
				INNER JOIN sai_bien_asbi_item asignacion_item
					ON (asignacion_item.asbi_id = asignacion.asbi_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = asignacion_item.clave_bien)

			UNION
			
			SELECT
				reasignar.fecha_acta AS fecha_acta,
				reasignar.esta_id AS id_estatus_acta,
				item_particular.bien_id AS id_bien,
				item_particular.modelo AS modelo,
				item_particular.marca_id AS id_marca,
				item_particular.etiqueta AS etiqueta
			FROM
				sai_bien_reasignar reasignar
				INNER JOIN sai_bien_reasignar_item reasignar_item
					ON (reasignar.acta_id = reasignar_item.acta_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = reasignar_item.clave_bien)
			) AS tabla
		WHERE
			tabla.fecha_acta <= '2012-09-11 23:59:59'
			AND tabla.id_estatus_acta <> 15
		GROUP BY
			tabla.etiqueta
		) fuera_inventario
		
		LEFT JOIN

		(
		SELECT
			asignacion.asbi_id AS id_acta,
			asignacion.asbi_fecha AS fecha_acta,
			item_particular.etiqueta
			
		FROM
			sai_bien_asbi asignacion
			INNER JOIN sai_bien_asbi_item asignacion_item
				ON (asignacion_item.asbi_id = asignacion.asbi_id)
			INNER JOIN sai_biin_items item_particular
				ON (item_particular.clave_bien = asignacion_item.clave_bien)
		) asignacion ON (
				asignacion.etiqueta = fuera_inventario.etiqueta
				AND asignacion.fecha_acta = fuera_inventario.fecha_acta
				)

		LEFT JOIN

		(
		SELECT
			reasignar.fecha_acta AS fecha_acta,
			reasignar.esta_id AS id_estatus_acta,
			reasignar.tipo AS tipo,
			item_particular.etiqueta AS etiqueta
		FROM
			sai_bien_reasignar reasignar
			INNER JOIN sai_bien_reasignar_item reasignar_item
				ON (reasignar.acta_id = reasignar_item.acta_id)
			INNER JOIN sai_biin_items item_particular
				ON (item_particular.clave_bien = reasignar_item.clave_bien)
		) reasignacion ON (
				reasignacion.etiqueta = fuera_inventario.etiqueta
				AND reasignacion.fecha_acta = fuera_inventario.fecha_acta
				)
	WHERE
		asignacion.id_acta LIKE 'a-%'
		OR
		(reasignacion.id_estatus_acta = '9' AND reasignacion.tipo <> 3)
		OR reasignacion.id_estatus_acta <> '9'
	)
GROUP BY
	item_particular.bien_id,
	marca.bmarc_nombre,
	item_particular.modelo,
	item.nombre,
	item_bien.existencia_minima
ORDER BY
	item.nombre,
	marca.bmarc_nombre,
	item_particular.modelo







____________________________________________
Query general
____________________________________________


SELECT
	item_particular.bien_id AS id_bien,
	item.nombre AS nombre_item,
	count(item.nombre) AS esxistencia
FROM
	sai_item item
	INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
	INNER JOIN sai_biin_items item_particular
		ON (item.id = item_particular.bien_id)
	INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
WHERE
	item_particular.fecha_entrada <= '2012-01-01 23:59:59'
	AND item_particular.etiqueta NOT IN
	(
	SELECT
		COALESCE(asignacion.etiqueta, '') || COALESCE(reasignacion.etiqueta, '') AS etiqueta
	FROM
		(
		SELECT
			tabla.etiqueta,
			max(tabla.fecha_acta) AS fecha_acta
		FROM
			(
			SELECT
				asignacion.asbi_fecha AS fecha_acta,
				asignacion.esta_id AS id_estatus_acta,
				item_particular.bien_id AS id_bien,
				item_particular.modelo AS modelo,
				item_particular.marca_id AS id_marca,
				item_particular.etiqueta AS etiqueta
			FROM
				sai_bien_asbi asignacion
				INNER JOIN sai_bien_asbi_item asignacion_item
					ON (asignacion_item.asbi_id = asignacion.asbi_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = asignacion_item.clave_bien)

			UNION
			
			SELECT
				reasignar.fecha_acta AS fecha_acta,
				reasignar.esta_id AS id_estatus_acta,
				item_particular.bien_id AS id_bien,
				item_particular.modelo AS modelo,
				item_particular.marca_id AS id_marca,
				item_particular.etiqueta AS etiqueta
			FROM
				sai_bien_reasignar reasignar
				INNER JOIN sai_bien_reasignar_item reasignar_item
					ON (reasignar.acta_id = reasignar_item.acta_id)
				INNER JOIN sai_biin_items item_particular
					ON (item_particular.clave_bien = reasignar_item.clave_bien)
			) AS tabla
		WHERE
			tabla.fecha_acta <= '2012-01-01 23:59:59'
			AND tabla.id_estatus_acta <> 15
		GROUP BY
			tabla.etiqueta
		) fuera_inventario
		
		LEFT JOIN

		(
		SELECT
			asignacion.asbi_id AS id_acta,
			asignacion.asbi_fecha AS fecha_acta,
			item_particular.etiqueta
			
		FROM
			sai_bien_asbi asignacion
			INNER JOIN sai_bien_asbi_item asignacion_item
				ON (asignacion_item.asbi_id = asignacion.asbi_id)
			INNER JOIN sai_biin_items item_particular
				ON (item_particular.clave_bien = asignacion_item.clave_bien)
		) asignacion ON (
				asignacion.etiqueta = fuera_inventario.etiqueta
				AND asignacion.fecha_acta = fuera_inventario.fecha_acta
				)

		LEFT JOIN

		(
		SELECT
			reasignar.fecha_acta AS fecha_acta,
			reasignar.esta_id AS id_estatus_acta,
			reasignar.tipo AS tipo,
			item_particular.etiqueta AS etiqueta
		FROM
			sai_bien_reasignar reasignar
			INNER JOIN sai_bien_reasignar_item reasignar_item
				ON (reasignar.acta_id = reasignar_item.acta_id)
			INNER JOIN sai_biin_items item_particular
				ON (item_particular.clave_bien = reasignar_item.clave_bien)
		) reasignacion ON (
				reasignacion.etiqueta = fuera_inventario.etiqueta
				AND reasignacion.fecha_acta = fuera_inventario.fecha_acta
				)
	WHERE
		asignacion.id_acta LIKE 'a-%'
		OR
		(reasignacion.id_estatus_acta = '9' AND reasignacion.tipo <> 3)
		OR reasignacion.id_estatus_acta <> '9'
	)
GROUP BY
	item_particular.bien_id,
	item.nombre
ORDER BY
	item.nombre



______________________________________________
Query galpon/torre actual
______________________________________________


SELECT
	item_particular.bien_id AS id_bien,
	count(
		CASE item_particular.ubicacion
			WHEN 2 THEN 1
			ELSE NULL
		END
	) AS en_galpon,
	count(
		CASE item_particular.ubicacion
			WHEN 1 THEN 1
			ELSE NULL
		END

	) AS en_torre
FROM
	sai_item item
	INNER JOIN sai_item_bien item_bien ON (item_bien.id = item.id)
	INNER JOIN sai_biin_items item_particular
		ON (item.id = item_particular.bien_id)
	INNER JOIN sai_bien_marca marca ON (item_particular.marca_id = marca.bmarc_id)
WHERE
	item_particular.bien_id IN ('612')
	AND item_particular.esta_id = '41'
GROUP BY
	item_particular.bien_id,
	item.nombre
ORDER BY
	item.nombre



 * 
 * */


?>

