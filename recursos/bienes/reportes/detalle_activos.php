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
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Inventario de Materiales Existente</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<script languaje="JavaScript" SRC="../../../includes/fechas.php"> </SCRIPT>
</head>
<br>
<form name="form" action="" method="post">
<?php 
/*
 $sql_ar="SELECT nombre,modelo,count(modelo)as cantidad,existencia_minima,t1.id,bmarc_nombre, bmarc_id
 FROM sai_item t1, sai_item_bien t3, sai_biin_items t2 ,sai_bien_marca
 WHERE t1.id=t3.id and t1.id=t2.bien_id and fecha_entrada <='".$_REQUEST['fecha']."'  
 and bien_id='".$_REQUEST['id']."'  and marca_id=bmarc_id
 group by modelo,existencia_minima,nombre,t1.id,bmarc_nombre, bmarc_id
 order by modelo";
 */
 
$fechaInventario = substr($_REQUEST['fecha'],8,2)."/".substr($_REQUEST['fecha'],5,2)."/".substr($_REQUEST['fecha'],0,4);

$aql_ar = "
	SELECT
		item_particular.bien_id AS id_bien,
		item.nombre AS nombre_item,
		count(item.nombre) AS existencia,
		item_particular.modelo,
		marca.bmarc_nombre AS nombre_marca,
		item_bien.existencia_minima
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
		item_particular.fecha_entrada <= '".$_REQUEST['fecha']."'
		AND item_particular.bien_id = '".$_REQUEST['id']."'
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
						tabla.fecha_acta <= '".$_REQUEST['fecha']."'
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
";
 
 $resultado_set_most_ar = pg_query($conexion, $aql_ar) or die("Error al consultar lista de activos");
 
 $arrInventario = array();
 $existenciaTotal = 0;
 
 while($row=pg_fetch_array($resultado_set_most_ar)){
 	$arrInventario[] = $row;
 	$totalExistencia += $row['existencia'];
 }
  
//if($row=pg_fetch_array($resultado_set_most_ar))
  if(count($arrInventario) > 0)
  {
?>
<table width="651" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
 
<div align="center"><span class="normalNegroNegrita">
	Inventario detallado del activo: <?php echo $_REQUEST['id'].":".$_REQUEST['desc'];?>
	<br />
	a la fecha <?php echo $fechaInventario;?>
</span></div>
  <tr>
    <td colspan="5"><table width="635" border="0" align="center" cellpadding="1" cellspacing="1" class="tablaalertas" id="factura_head">
      <tr  class="td_gray">
        <td width="41"><div align="center"><span class="normalNegroNegrita">#</span></div></td>
        <td width="50" height="25"><div align="center" class="normalNegroNegrita">C&oacute;digo</div></td>
        <td width="150"><div align="center" class="normalNegroNegrita">Activo</div></td>
        <td width="70"><div align="center" class="normalNegroNegrita">Marca</div></td>
        <td width="78"><div align="center"><span class="normalNegroNegrita">Modelo</span></div></td>
        <td width="70"><div align="center"><span class="normalNegroNegrita">Existencia</span></div></td>
        </tr>
      <?php
   	   $i=0;
   	   /*
       $resultado_set_most_ar=pg_query($conexion,$sql_ar) or die("Error al consultar lista de articulos");  
	   while($row=pg_fetch_array($resultado_set_most_ar)) 
	   {	
	   	$cantidad_existencia=0;
	   	$cant_salida=0;
	    $salidas="select count (t2.clave_bien) as cantidad_salida from sai_bien_asbi t1,sai_bien_asbi_item t2,sai_biin_items t3
		 where asbi_fecha<='".$_REQUEST['fecha']."' and t1.esta_id<>15 and t1.asbi_id=t2.asbi_id and t2.clave_bien=t3.clave_bien
	     and bien_id='".$row['id']."'  and modelo='".$row['modelo']."' and marca_id='".$row['bmarc_id']."'";
       //  echo $salidas;
	     $resultado_salida=pg_query($conexion,$salidas);  
	    if($row_salida=pg_fetch_array($resultado_salida)){
	      $cant_salida=$row_salida['cantidad_salida'];	
	    }
	    $cantidad_existencia=$row['cantidad']-$cant_salida;
	    */
   	   foreach ($arrInventario AS $inventario)
   	   {
	 ?>
      <tr>
        <td bordercolor="1" <?php echo $fondo_str;?>><div align="center" class="normal"><?php echo (++$i);?></div></td>
        <td height="21" bordercolor="1" ><div align="center" class="normal"><?php echo $inventario['id_bien'];?></div></td>
        <td bordercolor="1" ><div align="left" class="normal"><?php echo strtoupper($inventario['nombre_item']);?></div></td>
        <td bordercolor="1" ><div align="left"><span class="normal"><?php echo strtoupper($inventario['nombre_marca']);?></span></div></td>
        <td bordercolor="1" ><div align="left"><span class="normal"><?php echo strtoupper($inventario['modelo']);?></span></div></td>
        <td bordercolor="1"><div align="right"><span class="normal"><?php echo $inventario['existencia'];?> </span></div></td>
      </tr>
  <?php }?>
  	  <tr>
  	    <td colspan="5"><div align="right"><span class="normalNegroNegrita">Total existencia: </span></div></td>
  	    <td bordercolor="1"><div align="right"><span class="normalNegroNegrita"><?php echo $totalExistencia;?> </span></div></td>
  	  </tr>
  	</table></td>
  </tr>
  <tr>
    <td height="16" colspan="5" class="normal"><div align="center"> <br />
        <span class="peq_naranja">Detalle generado  el d&iacute;a 
              <?=date("d/m/y")?>
 a las
<?=date("H:i:s")?>
<br />
<br></br><span class="link">Imprimir Documento</span> </span><br />
            <br />
        <a href="javascript:window.print()" class="normal"><img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a><br />
      <br />
      <br />
<br />
<br />
 <input type="button" onclick="javascript:history.back()" value="Regresar"></input> 
    </div></td>
  </tr>
</table>
<?php } ?>
</form>


	
	












</body>
</html>
