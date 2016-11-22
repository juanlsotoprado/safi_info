<?php
ob_start();
require_once '../../../includes/conexion.php';
require_once '../../../includes/perfiles/constantesPerfiles.php';

if( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../../index.php',false);
	ob_end_flush(); 
	exit;
}
?>

<?php

$codigo = $_REQUEST['codigo'];
$idsDependenciasSolicitantes = $_POST['opt_depe'];  // Arreglo de dependencias solicitantes
// $unidadGastos = $_POST['unidadGastos']; // Unidad a la que se la cargaran los gastos de la salida
$arrInfocentro = explode (":" , $_REQUEST['infocentro']);
$arrSerialesActivos = $_POST['serialesActivos'];
$arrSerialesBienesNacionales = $_POST['serialesBienesNacionales'];
$arrIdsMateriales = $_POST['idsMateriales'];
$arrCantidadesMateriales = $_POST['cantidadesMateriales'];

$srtIdsDependenciasSolicitantes = "'".implode("', '", $idsDependenciasSolicitantes)."'";
$id_infocentro = trim($arrInfocentro[0]);
$nombre_infocentro = trim($arrInfocentro[1]);

/*
* Obtener los datos de la dependencia y de su gerente, director, consultor jurídico *
* o presidente (en caso de que la dependencia sea presidencia).                      *
*/

$query = "
	SELECT
		dependencia.*,
		empleado.empl_cedula,
		empleado.empl_nombres,
		empleado.empl_apellidos
	FROM
		sai_dependenci dependencia
		INNER JOIN sai_empleado empleado ON (empleado.depe_cosige = dependencia.depe_id)
		INNER JOIN sai_usuario usuario ON (usuario.empl_cedula = empleado.empl_cedula)
	WHERE 
		usuario.usua_activo = true
		AND dependencia.depe_id IN (".$srtIdsDependenciasSolicitantes.")
		AND empleado.esta_id = '1'
		AND empleado.carg_fundacion IN (
			'".substr(PERFIL_GERENTE, 0, 2)."',
			'".substr(PERFIL_CONSULTOR_JURIDICO, 0, 2)."',
			'".substr(PERFIL_DIRECTOR, 0, 2)."',
			'".substr(PERFIL_DIRECTOR_EJECUTIVO, 0, 2)."',
			'".substr(PERFIL_PRESIDENTE, 0, 2)."'
		) 
	ORDER BY
		empleado.empl_nombres
";

$resultado = pg_query($conexion, $query);
$dependenciasYEncargadosSolicitantes = array();

if($resultado === false){
	echo "Error al realizar la consulta de las dependencias solicitantes y de sus encargados.";
	error_log("Error al realizar la consulta de las dependencias solicitantes y de sus encargados. Detalles: " . pg_last_error());
	exit;
} else {
	while($row = pg_fetch_array($resultado))
	{
		$dependenciasYEncargadosSolicitantes[] = $row;
	}
}
 
/************************
* Activos y mobiliarios *
*************************/

$strActivos = "";
$where = "";

if (is_array($arrSerialesActivos) && count($arrSerialesActivos) > 0)
	$where = "item_particular.serial IN ('".implode("', '", $arrSerialesActivos)."')";
	
if (is_array($arrSerialesBienesNacionales) && count($arrSerialesBienesNacionales) > 0)
{
	if ($where != "")
		$where .=  "
			OR ";
	$where .= "item_particular.etiqueta IN ('".implode("' , '", $arrSerialesBienesNacionales)."')";
}


if($where != "")
{
	$query = "
		SELECT
			item_particular.clave_bien AS clave_bien
		FROM
			sai_biin_items item_particular
		WHERE
			".$where."
	";
	
	$resultado = pg_query($conexion, $query);
	$datosActivos = array();
	
	if($resultado === false){
		echo "Error al realizar la consulta de los activos por el serial del activo.";
		error_log("Error al realizar la consulta de los activos por el serial del activo. Detalles: " . pg_last_error());
		exit;
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$datosActivos[] = $row;
		}
		
		// Crear arreglo psql de los clave_bien de los activos
		$strActivos =
			implode(
				'","'
				, array_map(
					function ($datoActivo)
					{
						return $datoActivo["clave_bien"];
					}
					, $datosActivos
				)
			)	
		;
	}
}

$strActivos = ($strActivos == "") ? '{}' : '{"'.$strActivos.'"}';

/*************
* Materiales *
*************/

$strIdsMateriales = "";
$strCantidadesMateriales = "";
$strUnidadesMedidasMateriales = "";

if(
	is_array($arrIdsMateriales) && count($arrIdsMateriales) > 0
	&& is_array($arrCantidadesMateriales) && count($arrCantidadesMateriales) > 0
	&& count($arrIdsMateriales) == count($arrCantidadesMateriales)
){
	$query = "
		SELECT
			item.id AS id_item,
			item_articulo.unidad_medida AS item_articulo_unidad_medida 
		FROM
			sai_item item
			INNER JOIN sai_item_articulo item_articulo ON (item_articulo.id = item.id)
		WHERE
			item.id IN ('".implode("', '", $arrIdsMateriales)."')
	";
	
	$resultado = pg_query($conexion, $query);
	$datosMateriales = array();
	
	if($resultado === false){
		echo "Error al realizar la consulta de los materiales.";
		error_log("Error al realizar la consulta materiales. Detalles: " . pg_last_error());
		exit;
	} else {
		while($row = pg_fetch_array($resultado))
		{
			$datosMateriales[$row['id_item']] = $row;
		}
		
		if(count($datosMateriales) == count($arrIdsMateriales))
		{
			// Crear arreglo psql de los ids de los materiales
			$strIdsMateriales = '{"'.implode('","', $arrIdsMateriales).'"}';
			
			// Crear arreglo psql de las cantidades de los materiales
			$strCantidadesMateriales = '{"'.implode('","', $arrCantidadesMateriales).'"}';
			
			// Ordenar por ids de materiales como están en el arreglo
			$arrUnidadesMedidas = array();
			foreach ($arrIdsMateriales AS $idMaterial)
			{
				$arrUnidadesMedidas[] = $datosMateriales[$idMaterial]['item_articulo_unidad_medida'];
			}
			
			// Crear arreglo psql de las unidades de medidas de los materiales
			$strUnidadesMedidasMateriales ='{"'.implode('","', $arrUnidadesMedidas).'"}';
		}
	}
}

$strIdsMateriales = ($strIdsMateriales == "") ? '{}' : $strIdsMateriales;
$strCantidadesMateriales = ($strCantidadesMateriales == "") ? '{}' : $strCantidadesMateriales;
$strUnidadesMedidasMateriales = ($strUnidadesMedidasMateriales == "") ? '{}' : $strUnidadesMedidasMateriales;

// Si pasa todas las validaciones se almacena la salida
/*
$query = "
	SELECT * FROM sai_insert_asbi(
		'".$_SESSION['user_depe_id']."',
		'".$_SESSION['login']."',
		'".$_POST['destino']."',
		'".$id_infocentro."',
		'".$strIdsMateriales."', 
		'".$strCantidadesMateriales."',
		'".$strUnidadesMedidasMateriales."',
		'".$strActivos."',
		'".implode(",", $idsDependenciasSolicitantes)."',
		'".$_POST['ubicacion']."',
		1,
		'".$unidadGastos."'
	) AS codigo
";
*/
$query = "
	SELECT * FROM sai_insert_asbi(
		'".$_SESSION['user_depe_id']."',
		'".$_SESSION['login']."',
		'".$_POST['destino']."',
		'".$id_infocentro."',
		'".$strIdsMateriales."', 
		'".$strCantidadesMateriales."',
		'".$strUnidadesMedidasMateriales."',
		'".$strActivos."',
		'".implode(",", $idsDependenciasSolicitantes)."',
		'".$_POST['ubicacion']."',
		1
	) AS codigo
";

$resultado = pg_query($conexion, $query);

if($resultado === false){
	echo "Error al ingresar los activos, verifique que el número del activo no se encuentre registrado.";
	error_log("Error al ingresar los activos, verifique que el número del activo no se encuentre registrado. Detalles: "
		. pg_last_error());
	exit;
} else {
	if ($row = pg_fetch_array($resultado))
	{
		$codigo_clave = explode ("*" , $row['codigo']);
		$codigo = trim($codigo_clave[0]);
		$clave_activos = trim($codigo_clave[1]);//"'".ereg_replace( "*", "','", $row['codigo'] )."'";
		$claves = explode ("/" , $codigo_clave[1]);
		$id_articulos = trim($codigo_clave[3]);
		$arti_sin_salida = explode ("/" , $codigo_clave[3]);
	
		$listado_claves='0';	   
		for ($i=1;$i<count($claves);$i++)
		{
			if ($i==1)
				$listado_claves=$claves[$i];
			else
				$listado_claves=$listado_claves.",".$claves[$i];
		}
		
		$buscar_datos="SELECT t2.asbi_id,etiqueta,serial FROM sai_biin_items t1, sai_bien_asbi_item t2 
			WHERE t1.clave_bien=t2.clave_bien and t1.clave_bien in (".$listado_claves.")";
		$resultado_busqueda=pg_query($conexion,$buscar_datos);
		
		$listado_articulos='0';	   
		for ($i=1;$i<count($arti_sin_salida);$i++)
		{
			if ($i==1)
				$listado_articulos=$arti_sin_salida[$i];
			else
				$listado_articulos=$listado_articulos.",".$arti_sin_salida[$i];
		}
		 
		$buscar_articulos="SELECT id,nombre FROM sai_item
			WHERE id in (".$listado_articulos.")";
		$resultado_busqueda_articulos=pg_query($conexion,$buscar_articulos);
	}
	
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
		<script type="text/javascript" src="../../../js/funciones.js"> </script>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>

	<body>
	<?php
		if ($codigo <> "0")
		{
	?>
		<form action="" name="form" id="form" method="post" >
			<table width="700" align="center" background="imagenes/fondo_tabla.gif" class="tablaalertas" id="sol_via">
				<tr>
					<td height="15" colspan="2" valign="middle" class="td_gray">
						<span class="normalNegroNegrita"> Salida de activos y/o materiales </span>
					</td>
				</tr>
				<tr>
					<td class="normal"><strong>N&deg; acta</strong></td>
					<td class="normalNegro"><b><?php echo($codigo); ?></b></td></tr>
				<tr>
					<td class="normal"><strong>Fecha</strong></td>
					<td class="normalNegro"><?php echo(date('d/m/Y')); ?></td></tr>
				<tr>
					<td class="normal"><strong>Dependencia solicitante</strong></td>
					<td class="normalNegro">
					<?php
					
			$sql_str = "SELECT * FROM sai_dependenci WHERE depe_id in (".$srtIdsDependenciasSolicitantes.")";	
			$res_q = pg_exec($sql_str) or die("Error al mostrar");	  
			$i = 0;
			$depe_nombre = '';
			while($depe_row = pg_fetch_array($res_q)){ 
				if ($i==0)
					$depe_nombre=$depe_row['depe_nombre'];
				else
					$depe_nombre=$depe_nombre.",".$depe_row['depe_nombre'];
	        
				$i++;
			}
			echo($depe_nombre);
			 
					?>
					</td>
				</tr>
				<tr>
					<td class="normal"><strong>Elaborado por</strong></td>
					<td class="normalNegro"><?php echo($_SESSION['solicitante']); ?></td>
				</tr>
				<?php if ($id_infocentro <> "") { ?>
				<tr>
					<td class="normal"><strong>Infocentro</strong></td>
					<td class="normalNegro"><?php echo($id_infocentro." : ".$nombre_infocentro); ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td class="normal"><strong>Destino</strong></td>
					<td class="normalNegro"><?php echo($_POST['destino']); ?></td></tr>
				<tr>
					<td class="normal"><strong>Activos/Mobiliarios</strong></td>
					<td class="normalNegro">
					<?php
			$sql_salida = "SELECT * FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_biin_items t3
				WHERE t1.asbi_id='".$codigo."' and t1.asbi_id=t2.asbi_id and t2.clave_bien=t3.clave_bien";
			$resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
			while($rowd=pg_fetch_array($resultado_salida)) 
			{ 
				echo("Serial Bien Nacional: <b>".$rowd['etiqueta']."</b> Serial Art&iacute;culo: <b>".$rowd['serial'])."</b><br>";  
			}
					?>
					</td>
				</tr>
				<tr>
					<td class="normal"><strong>Materiales</strong></td>
					<td class="normalNegro">
					<?php
			$sql_salida="SELECT * FROM sai_bien_asbi t1,sai_bien_asbi_item t2,sai_item t3
				WHERE t1.asbi_id='".$codigo."' and t1.asbi_id=t2.asbi_id and id=arti_id";
			$resultado_salida=pg_query($conexion,$sql_salida) or die("Error al consultar el detalle del acta");
			while($rowd=pg_fetch_array($resultado_salida)) 
			{
				echo($rowd['nombre']." : ".$rowd['cantidad'])."<br>";  
			}
					?>
					</td>
				</tr>
				<?php
			if (count($claves)>1) { 
				?>
				<tr>
					<td class="normal" height="20"><div style="color:#FF0000;">
						<strong>Activos asignados previamente:</strong>
					</div></td>
					<td class="normalNegro">
						<div style="color:#FF0000;">
							<strong><?
					while($row_buscar=pg_fetch_array($resultado_busqueda)){
	         			echo("Serial Bien Nacional: ".$row_buscar['etiqueta']." Serial Art&iacute;culo: "
	         				.$row_buscar['serial']." Acta de Salida: ".$row_buscar['asbi_id'])."<br>";
					}
							?></strong>
						</div>
					</td>
				</tr>
				<?php
			}
				
			if (count($arti_sin_salida) > 1){ ?>
				<tr>
					<td class="normal"  height="20px">
						<div style="color:#FF0000;">
							<strong>Materiales no asignados o incompletos en el acta:</strong>
						</div>
					</td>
					<td class="normalNegro">
						<div style="color:#FF0000;">
							<strong><?
					while($row_buscar=pg_fetch_array($resultado_busqueda_articulos)){
						echo($row_buscar['nombre'])."<br>";
					}
							?></strong>
						</div>
					</td>
				</tr>  
			<?php
			}
			?>
			</table>
			<br/><br/>
			<div align='center'>
				<a target="_blank" class="normal"
					href="../../../recursos/bienes/salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=s"
				>SALIDA<img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a target="_blank" class="normal"
					href="../../../recursos/bienes/salida_activos_pdf.php?codigo=<?=$codigo;?>&tipo=a&solicitante=
									<?=$srtIdsDependenciasSolicitantes;?>"
				>ASIGNACI&Oacute;N<img src="../../../imagenes/boton_imprimir.gif" width="23" height="20" border="0" /></a>
			</div>
			<br/>
		</form>
		<?php
		} else {
		?>
		<br/>
		<div  style="color:#FF0000;" align="center">
			<b>No se puede emitir el acta de salida con la informaci&oacute;n suministrada,<br>
			Verifique el serial o <br>si ya fue previamente sacado del inventario el art&iacute;culo</b>
			<br/><br/>Enviar esta informacion al Departamento de Sistemas: <?php echo $sql;?><br/>
		</div>
		<?php
		}
		?>
	</body>
</html>