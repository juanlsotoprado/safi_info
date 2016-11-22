<?php
/***VERIFICAR AÑO PRESUPUESTARIO, ESTÁ EN EL CÓDIGO PERO NO SE USA***/
ob_start();
session_start();
require_once("../../includes/perfiles/constantesPerfiles.php");
require_once("../../includes/conexion.php");
require_once("../../includes/constantes.php");
if(empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
$idPerfil = $_SESSION['user_perfil_id'];
ob_end_flush();
$tipoServicio = 3;
$opcion = ($_GET["opcion"] && $_GET["opcion"]!="")? $_GET["opcion"]:"2";
$codigo = ($_GET["codigo"] && $_GET["codigo"]!="")? $_GET["codigo"]:"";
$estado = ($_GET["estado"] && $_GET["estado"]!="" && $_GET["estado"]!="0")? $_GET["estado"]:"";
$partida = ($_GET["partida"] && $_GET["partida"]!="" && $_GET["partida"]!="0")? $_GET["partida"]:"";
$ano = ($_GET["ano"] && $_GET["ano"]!="")? $_GET["ano"]:"";
$palabraClave = ($_GET["palabraClave"] && $_GET["palabraClave"]!="")? trim($_GET["palabraClave"]):"";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.:SAFI:ADMINISTRAR SERVICIO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script>
function cambiarOpcion(elemento){
	if(elemento.value=='1'){
		document.getElementById("codigo").disabled=false;
		document.getElementById("estado").disabled=true;
		document.getElementById("partida").disabled=true;
		//document.getElementById("ano").disabled=true;
		document.getElementById("palabraClave").disabled=true;
	}else if(elemento.value=='2'){ 
		document.getElementById("codigo").disabled=true;
		document.getElementById("estado").disabled=false;
		document.getElementById("partida").disabled=false;
		//document.getElementById("ano").disabled=false;
		document.getElementById("palabraClave").disabled=false;
	}
}

function modificar(codigo){
	<?php
	if($opcion=="1"){
		echo "location.href='modificarServicio.php?opcion=".$opcion."&codigo=".$codigo."&id='+codigo;";
	}else{
		echo "location.href='modificarServicio.php?opcion=".$opcion."&estado=".$estado."&partida=".$partida."&palabraClave=".$palabraClave."&id='+codigo;";
	}
	?>
}

function detalle(codigo){
	url='detalle.php?id='+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}

function buscar(){
	if(document.getElementById("opcionCodigo").checked == true){
		var codigo = document.getElementById("codigo").value;
		if(codigo!=""){
			location.href = "buscar.php?opcion=1&codigo="+codigo;			
		}else{
			alert("Debe indicar el c"+oACUTE+"digo del servicio");
		}
	}else if(document.getElementById("opcionMultiple").checked == true){
		var estado = document.getElementById("estado").value;
		var partida = document.getElementById("partida").value;
		//var ano = document.getElementById("ano").value;
		var palabraClave = document.getElementById("palabraClave").value;
		//if(estado!="0" || partida!="0" /*|| ano!=""*/ || palabraClave!=""){
		location.href = "buscar.php?opcion=2&estado="+estado+"&partida="+partida+"&palabraClave="+palabraClave;//&ano="+ano
		/*}else{
			alert("Debe indicar al menos uno de los criterios de b"+uACUTE+"squeda m"+uACUTE+"ltiple");
		}*/
	}
}
</script>
</head>
<body class="normal">
<form name="form" method="post" action="buscar.php">
<input type="hidden" name="hid_buscar1" value="0"/>
<br/>
<br/>
<table width="900" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<tr class="td_gray">
		<td height="21" colspan="3" class="normalNegroNegrita">
			B&uacute;squeda de servicio
		</td>
	</tr>
	<tr>
		<td height="30"><input id="opcionCodigo" name="opcion" type="radio" value="1" onclick="javascript:cambiarOpcion(this);" <?php if($opcion=="1"){ echo "checked='checked'";}?>/></td>
		<td height="30" colspan="2">B&uacute;squeda por c&oacute;digo de servicio:&nbsp;<input class="normalNegro" onkeyup='validarInteger(this);' size="8" maxlength="10" id="codigo" name="codigo" type="text" value="<?= $codigo?>" <?php if($opcion!="1"){ echo "disabled='disabled'";}?>/></td>
	</tr>
	<tr>
		<td height="30"><input id="opcionMultiple" name="opcion" type="radio" value="2" onclick="javascript:cambiarOpcion(this);" <?php if($opcion=="2"){ echo "checked='checked'";}?>/></td>
		<td height="30" colspan="2">B&uacute;squeda por criterios m&uacute;ltiples:</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td background="../../imagenes/fondo_tabla.gif">Estado del Recurso:</td>
		<td height="30">
		    <select name="estado" class="normalNegro" id="estado" <?php if($opcion!="2"){ echo "disabled='disabled'";}?>>
		    	<option value="0">Todos</option>
				<option value="1" <?php if($estado==ESTADO_ACTIVO){ echo "selected='selected'";}?>>Activo</option>
				<option value="2" <?php if($estado==ESTADO_INACTIVO){ echo "selected='selected'";}?>>Inactivo</option>
		    </select>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td height="30">Partida:</td>
		<td height="30">
			<select name="partida" id="partida" class="normalNegro" <?php if($opcion!="2"){ echo "disabled='disabled'";}?>>
				<option value="0">Todas</option>
				<?php
				$part="4.03.00.00.00";
				$query="SELECT part_id, part_nombre ".
						"FROM sai_partida ".
						"WHERE ".
							"pres_anno='".$_SESSION['an_o_presupuesto']."' ".
							"AND part_id LIKE '".substr(trim($part),0, 4)."%' ".
							"AND substring(trim(part_id)from 9 for 5)<>'00.00' ".
						"ORDER BY part_id";
				$resultado=pg_query($conexion,$query);
				while($row=pg_fetch_array($resultado)){
					$part_id=$row['part_id'];
					$part_nombre=$row['part_nombre'];
				?>
					<option value="<?=$part_id?>" <?php if($partida==$part_id){ echo "selected='selected'";}?>><?=$part_id?>:<?= $part_nombre?></option>
				<?php
				}
				?>
			</select>
		</td>
	</tr>
	<!-- <tr>
		<td>&nbsp;</td>
		<td height="35" class="normalNegrita"><div align="right">A&#241;o de la Partida:</div></td>
		<td height="35" class="normalNegrita"><input type="text" id="ano" name="ano" value="<?= $ano?>" maxlength="4" onkeypress="return acceptNum(event)" <?php if($opcion!="2"){ echo "disabled='disabled'";}?>/></td>
	</tr> -->
	<tr>
		<td>&nbsp;</td>
		<td height="30">Palabra clave:</td>
		<td height="30"><input class="normalNegro" maxlength="100" onkeyup="validarTexto(this);" id="palabraClave" name="palabraClave" type="text" value="<?= $palabraClave?>" <?php if($opcion!="2"){ echo "disabled='disabled'";}?>/></td>
	</tr>
	<tr>
		<td height="44" colspan="3" align="center">
			<input type="button" onclick="buscar();" value="Buscar" class="normalNegro"/>
		</td>
	</tr>
	</table>
	</form>
	<br/>
	<form name="form2">
		<table width="900" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<?php
			/*if(($codigo && $codigo!="")
				|| ($palabraClave && $palabraClave!="")
				|| ($partida && $partida!="")
				|| ($estado && $estado!="")
				|| ($ano && $ano!="")){*/
				
				$query =	 	"SELECT ".
									"si.id, ".
									"si.nombre, ".
									"si.esta_id as estado, ".
									"sp.part_id as id_partida, ".
									"sp.pres_anno as pres_anno, ".
									"sp.part_nombre as nombre_partida ".
								"FROM ".
									"sai_item si ".
									"LEFT OUTER JOIN sai_item_partida sip ON (si.id = sip.id_item) ".
									"LEFT OUTER JOIN sai_partida sp ON (sip.part_id = sp.part_id AND sip.pres_anno = sp.pres_anno) ".
								"WHERE ".
									"si.id_tipo = ".$tipoServicio." ";
				if($codigo && $codigo!=""){
					$query .=		" AND si.id = '".$codigo."' ";
				}else{
					if($palabraClave && $palabraClave!=""){
						$query .=	"AND (si.nombre = upper('".$palabraClave."') OR ".
									"si.nombre like upper('".$palabraClave."') || '%' OR ".
									"si.nombre like '%' || upper('".$palabraClave."') || '%' OR ".
									"si.nombre like '%' || upper('".$palabraClave."')) ";
					}
					if($partida && $partida!=""){
						$query .=	"AND sip.part_id = '".$partida."' ";							
					}
					if($estado && $estado!=""){
						$query .=	"AND si.esta_id = ".$estado." ";
					}
					if($ano && $ano!=""){
						$query .=	"AND sip.pres_anno = ".$ano." ";
					}
				}
				$query .= 			"GROUP BY si.id, si.nombre, si.esta_id, sp.part_id, sp.pres_anno, sp.part_nombre ";	
				if(!$codigo || $codigo==""){
					$query .=	"ORDER BY si.nombre";
				}
				$resultado=pg_query($conexion,$query);
				$numeroFilas = pg_numrows($resultado);
				if($numeroFilas>0){
			?>
			<tr class="td_gray normalNegroNegrita">
				<td width="90" align="center">Partida</td>
				<td width="90" align="center">A&ntilde;o presupuestario</td>
				<td width="220" align="center">Denominaci&oacute;n</td>
				<td width="90" align="center">C&oacute;digo</td>
				<td width="220" align="center">Servicio</td>
				<td width="90" align="center">Estado</td>
				<td width="90" align="center">Opciones</td>
			</tr>
			<?php 
					for($i = 0; $i < $numeroFilas; $i++) {
				    	echo "<tr>\n";
				    	$row = pg_fetch_array($resultado, $i);
				    	echo "<td height='28' align='center'>".$row["id_partida"]."</td>
				    	<td align='center'>".$row["pres_anno"]."</td>
				    	<td align='left'>".$row["nombre_partida"]."</td>
				    	<td align='center'>".$row["id"]."</td>
				    	<td align='left'>".$row["nombre"]."</td>
				    	<td align='center'>".(($row["estado"]==ESTADO_ACTIVO)?"Activo":"Inactivo")."</td>
				    	<td align='center'>
				    		<a href='javascript:detalle(\"".$row["id"]."\")' class='link'> Ver Detalle</a><br/>
				    		".($idPerfil != PERFIL_ANALISTA_I_PASANTE_BIENES ?
				    			"<a href='javascript:modificar(\"".$row["id"]."\");' class='link'>Modificar</a>" : ""
				    		)."
				    	</td>";
				    	echo "</tr>\n";
					}
				}else{
					echo "<tr><td align='center' height='44'>No se encontraron resultados</td></tr>";
				}
			/*}else if($opcion!=""){
				echo "<tr><td class='normal' align='center' height='44'>Debe ingresar al menos un criterio de busqueda</td></tr>";
			}*/
			?>
		</table>
	</form>
</body>
</html>
<?php
pg_close($conexion);
?>