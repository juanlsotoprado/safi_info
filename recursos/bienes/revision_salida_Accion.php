<?php
ob_start();
require("../../includes/conexion.php");
require_once("../../includes/fechas.php");
if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush(); 
	exit;
}?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link  rel="stylesheet" href="../../css/plantilla.css" type="text/css" media="all"  />
<script LANGUAGE="JavaScript" SRC="../../js/funciones.js"> </SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<?
$codigo=$_POST['codigo'];
$opcion=$_POST['opcion'];
$genera=$_POST['usuario_genera'];

 $memo_contenido=trim($_POST['contenido_memo']);
 if ($memo_contenido==""){
 	$memo_contenido="No Especificado";
 }

if ($opcion==325){
	$opcion=25;
	$accion=" Aprobada para Finalizar";
	if (substr($codigo,0,1)=='a')//ASIGNACION
	 $query="UPDATE sai_bien_asbi SET esta_id=33 where asbi_id='".$codigo."'";
	else
	 $query="UPDATE sai_bien_reasignar SET esta_id=33 where acta_id='".$codigo."'";
	$resultado_acta=pg_query($conexion,$query);
	$edo_revision=33;
}
$sql = " SELECT * FROM sai_insert_revision_doc('$codigo', '".$_SESSION['login']."', '".$_SESSION['user_perfil_id']."', '$opcion', '$firma_doc') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al insertar la revision");
 
$opcion=$_POST['opcion'];

$sql = " SELECT max(revi_id) as max_revision FROM sai_revisiones_doc WHERE revi_doc='".$codigo."'";
$resultado = pg_query($conexion,$sql) or die("Error al buscar la ultima revision");

if ($row=pg_fetch_array($resultado))
 $max_revision=$row['max_revision'];
 
if ($opcion==6){ // Visto Bueno Analistas 		***********************seria caso analistas y 13 para susana
	$accion=" Revisada ";
	if (substr($codigo,0,1)=='a')//ASIGNACION
	 $query="UPDATE sai_bien_asbi SET esta_id=12 where asbi_id='".$codigo."'";
	else
	 $query="UPDATE sai_bien_reasignar SET esta_id=12 where acta_id='".$codigo."'";
	$resultado_acta=pg_query($conexion,$query);
	$edo_revision=12;//Revisado
}
if ($opcion==3){//Aprobación
	$accion=" Aprobada ";
	if (substr($codigo,0,1)=='a')//ASIGNACION
	 $query="UPDATE sai_bien_asbi SET esta_id=13 where asbi_id='".$codigo."'";
	else
	 $query="UPDATE sai_bien_reasignar SET esta_id=13 where acta_id='".$codigo."'";
	$edo_revision=13;
	$resultado_acta=pg_query($conexion,$query);
}


if ($opcion==25){ //Envia Galpón
	$accion=" Finalizada ";
	if (substr($codigo,0,1)=='a')//ASIGNACION
	 $query="UPDATE sai_bien_asbi SET esta_id=33 where asbi_id='".$codigo."'";
	else
	 $query="UPDATE sai_bien_reasignar SET esta_id=33 where acta_id='".$codigo."'";
	$resultado_acta=pg_query($conexion,$query);
	//Guardar Detalle de la Revisión
	$query="INSERT INTO sai_revisiones_detalle(revi_id,nombre,cedula,telefono,fecha,infocentro,observaciones) 
	VALUES('".$max_revision."','".$_POST['nombre']."','".$_POST['cedula']."',
	'".$_POST['telefono']."','".cambia_ing($_POST['fecha'])."','".$_POST['infocentro']."','".$_POST['observaciones']."' )";
	$resultado_acta=pg_query($conexion,$query);
	$edo_revision=33;//Enviado
}
if ($opcion==99){ //Finalizan los Analistas 		***********************seria caso analistas y 13 para el Coordinador
	$accion=" Finalizada ";
	$edo_revision=53;//Entregado
	
	if (substr($codigo,0,1)=='a'){//ASIGNACION
		$query="UPDATE sai_bien_asbi SET esta_id=9 where asbi_id='".$codigo."'";
	}
	else{
		$query="UPDATE sai_bien_reasignar SET esta_id=9 where acta_id='".$codigo."'";
		
		$query_reasignacion = "SELECT tipo FROM sai_bien_reasignar WHERE acta_id='".$codigo."'";
		$resultado_reasignacion = pg_query($conexion, $query_reasignacion);
		if ($row = pg_fetch_array($resultado_reasignacion))
		{
			if($row['tipo'] == "3") $edo_revision = 41;	//Disponible
		}
	}
	 
	$resultado_acta=pg_query($conexion,$query);
	$query="INSERT INTO sai_revisiones_detalle(revi_id,nombre,cedula,telefono,fecha,infocentro,observaciones) 
	VALUES('".$max_revision."','".$_POST['nombre']."','".$_POST['cedula']."',
	'".$_POST['telefono']."','".cambia_ing($_POST['fecha'])."','".$_POST['infocentro']."','".$_POST['observaciones']."' )";
	$resultado_acta=pg_query($conexion,$query);
	$fecha_actual = date("Y-m-d H:i:s");
	if(substr($codigo,0,2)!='ra'){
		$query="INSERT INTO sai_bien_asbi_archivo(asbi_id,usua_login,asbi_archivo,asbi_fecha) VALUES ('".$codigo."',
		'".$_SESSION['login']."','".$_POST['archivo']."','".$fecha_actual."')";
		$resultado_acta=pg_query($conexion,$query);
	}
}

if ($opcion==5){//Devolución
	$accion=" Devuelta ";
	if (substr($codigo,0,1)=='a')//ASIGNACION
	 $query="UPDATE sai_bien_asbi SET esta_id=10 where asbi_id='".$codigo."'";
	else
	 $query="UPDATE sai_bien_reasignar SET esta_id=10 where acta_id='".$codigo."'";	
	$resultado_acta=pg_query($conexion,$query);
	$codigo_memo=utf8_decode("select * from sai_insert_memo('".$_SESSION['login']."', '".$_SESSION['user_depe_id']."', '".$memo_contenido."', 'Devolución de Acta de Salida','0', '0','0','',0, 0, '0', '','".$codigo."') as resultado");
	$result_memo=pg_query($conexion,$codigo_memo) or die ("Error al registrar el memo");
	$edo_revision=10;
}

if (substr($codigo,0,1)=='a'){//ASIGNACION
	$acta="s".substr($codigo,1);
//Actualizar el Edo de los Activos según lo indique las revisiones
$query_activos="SELECT * FROM  sai_bien_asbi_item t1, sai_biin_items t2 
WHERE t1.clave_bien=t2.clave_bien and asbi_id='".$codigo."' ";
}else{
	$acta=$codigo;
$query_activos="SELECT * FROM  sai_bien_reasignar_item t1, sai_biin_items t2 
WHERE t1.clave_bien=t2.clave_bien and t1.acta_id='".$codigo."' ";
}
$resultado_activos=pg_query($conexion,$query_activos);
while ($rows=pg_fetch_array($resultado_activos)){
  $act_activo="UPDATE sai_biin_items SET esta_id='".$edo_revision."' WHERE clave_bien='".$rows['clave_bien']."'";
  $resultado_actualiza=pg_query($conexion,$act_activo);
}
?>

<br></br>
<div class="normal" align="center"><strong>El Acta N&deg; <? echo $codigo; ?> fue<?=$accion; ?>satisfactoriamente</div><br> 
<div align="center">
<?php if (substr($codigo,0,1)=='a'){//ASIGNACION?>
<a target="_blank" class="normal" href="salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Consultar detalle</a>
<?php }else{?>
<a target="_blank" class="normal" href="reasignar_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s">Consultar detalle</a>
<?php }?></div></b>
			
<?php pg_close($conexion);?>
<?php 

/*
Warning: pg_query() [function.pg-query]: Query failed: ERROR: inserción o actualización en la tabla 
«sai_bien_asbi_archivo» viola la llave foránea «id_acta_asbii» DETAIL: La llave (asbi_id)=(ra-312) 
no está presente en la tabla «sai_bien_asbi». in /var/www/safi0.2/recursos/bienes/revision_salida_Accion.php on line 97
 * */
?>