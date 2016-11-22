<?php
ob_start();
session_start();
include (dirname ( __FILE__ ) . '/../../init.php');
require_once("../../includes/conexion.php");
require_once("../../includes/funciones.php");
if ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") ){
	header('Location:../../index.php',false);
	ob_end_flush();
	exit;
}
ob_end_flush();
$estadoActivo = "1";
$estadoInactivo = "2";
$user_perfil_id = $_SESSION['user_perfil_id'];
$rif = "";
if (isset($_REQUEST['rif']) && $_REQUEST['rif'] != "") {
	$rif = $_REQUEST['rif'];
}
$codigo = "";
if (isset($_REQUEST['codigo']) && $_REQUEST['codigo'] != "") {
	$codigo = $_REQUEST['codigo'];
}
$nombre = "";
if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != "") {
	$nombre = $_REQUEST['nombre'];
}
$estado = "";
if (isset($_REQUEST['estado']) && $_REQUEST['estado'] != "") {
	$estado = $_REQUEST['estado'];
}
$estadovenezuela = "";
if (isset($_REQUEST['estadovenezuela']) && $_REQUEST['estadovenezuela'] != "") {
	$estadovenezuela = $_REQUEST['estadovenezuela'];
}
$tipo = "";
if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] != "") {
	$tipo = $_REQUEST['tipo'];
}
$pagina = "1";
if (isset($_REQUEST['pagina']) && $_REQUEST['pagina'] != "") {
	$pagina = $_REQUEST['pagina'];
}
$tamanoPagina = 12;
$tamanoVentana = 20;
$desplazamiento = ($pagina-1)*$tamanoPagina;

$queryproveedor = "SELECT prov_nombre,prov_id_rif FROM sai_proveedor_nuevo ORDER BY prov_nombre";
$resultado = pg_exec( $conexion, $queryproveedor );
$indice = 0;
$stringobj;
while ( $row = pg_fetch_array ( $resultado ) ) {
	$stringobj [$indice] ['prov_id_rif'] = utf8_encode($row ['prov_id_rif']);
	$stringobj [$indice] ['prov_nombre'] = utf8_encode($row ['prov_nombre']);
	$indice ++;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
.ui-autocomplete {
	max-height: 110px;
	font-size: 12px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 30px;
}
/* IE 6 doesn't support max-height
                        * we use height instead, but this forces the menu to always be this tall
                        */
* html .ui-autocomplete {
	font-size: 10px;
}

.ui-menu-item a {
	font-size: 10px;
}
</style>
<title>.:SAFI:Buscar Proveedor</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../css/plantilla.css" rel="stylesheet" type="text/css"/>
<script language="JavaScript" src="../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../js/funciones.js"></script>
<script language="JavaScript" src="../../js/botones.js"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/lib/jquery/plugins/jquery.min.js';?>" charset="utf-8"></script>
<script type="text/javascript" src="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/js/jquery-ui-1.8.13.custom.min.js';?>" charset="utf-8"></script>
<link href="<?=SAFI_URL_JAVASCRIPT_PATH.'/editorlr/css/smoothness/jquery-ui-1.8.13.custom.css';?>" rel="stylesheet" type="text/css" charset="utf-8" />
<script language="javascript" type="text/javascript">
var stringobj = <?php echo json_encode($stringobj,JSON_FORCE_OBJECT); ?>;
var items = new Array();
var index = 0;
var idproveedor;
var nombreproveedor;
$().ready(function(){

	$("#nombre").keyup(function(){
		$("#rif").val("");
		$("#codigo").val("");
	});
	$("#rif").keyup(function(){
		$("#codigo").val("");
		$("#nombre").val("");
	});
	$("#codigo").keyup(function(){
		$("#rif").val("");
		$("#nombre").val("");
	});
	
	$.each(stringobj,function(id, params){
		items[index++] = {
				id: params.id,
				value: params.prov_nombre
		};
	});

	$("#nombre" ).autocomplete({
		source: items, 
		minLength: 1,
	    select: function(event,ui)
	    {
	   		idproveedor = ui.item.id;
	   		nombreproveedor = ui.item.value;
	    
	    return true;
	            
	    }
	});
	
});
/*function cambiarCriterio(){
	if(document.getElementById("criterioRif").checked==true){
		document.getElementById("rif").disabled = false;
		document.getElementById("codigo").disabled = true;
		document.getElementById("codigo").value = "";
		document.getElementById("nombre").disabled = true;
		document.getElementById("estado").disabled = true;
		document.getElementById("tipo").disabled = true;
	}else if(document.getElementById("criterioCodigo").checked==true){
		document.getElementById("rif").disabled = true;
		document.getElementById("rif").value = "";
		document.getElementById("codigo").disabled = false;
		document.getElementById("nombre").disabled = true;
		document.getElementById("estado").disabled = true;
		document.getElementById("tipo").disabled = true;
	}else if(document.getElementById("criteriosMultiples").checked==true){
		document.getElementById("rif").disabled = true;
		document.getElementById("rif").value = "";
		document.getElementById("codigo").disabled = true;
		document.getElementById("codigo").value = "";
		document.getElementById("nombre").disabled = false;
		document.getElementById("estado").disabled = false;
		document.getElementById("tipo").disabled = false;
	}
	return;
}*/
function buscar(){
	document.formProveedores.submit();
}
function modificar(proveedor){
	var rif = '<?= $rif?>';
	var codigo = '<?= $codigo?>';
	var nombre = '<?= $nombre?>';
	var estado = '<?= $estado?>';
	var tipo = '<?= $tipo?>';
	var pagina = '<?= $pagina?>';
	location.href = "modificar.php?prov="+proveedor+"&rif="+rif+"&codigo="+codigo+"&nombre="+nombre+"&estado="+estado+"&tipo="+tipo+"&pagina="+pagina;
}
function listadoTotal(){
	var rif = '<?= $rif?>';
	var codigo = '<?= $codigo?>';
	var nombre = '<?= $nombre?>';
	var estado = '<?= $estado?>';
	var tipo = '<?= $tipo?>';
	var pagina = '<?= $pagina?>';
	location.href = "listadoProveedor.php?rif="+rif+"&codigo="+codigo+"&nombre="+nombre+"&estado="+estado+"&tipo="+tipo+"&pagina="+pagina;
}
function listarProveedores(pagina){
	document.getElementById("pagina").value = pagina;
	document.formProveedores.submit();
}

/*AJAX*/
function createRequestObject() {
	var ro;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer"){
		ro = new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		ro = new XMLHttpRequest();
	}
	return ro;
}

var http = createRequestObject();

function sndReq(action) {
	http.open('get', action);
	http.onreadystatechange = handleResponse;
	http.send(null);
	return;
}

function handleResponse() {
	if(http.readyState == 4){
		if(http.responseText=="activo") {
			alert("El proveedor ha sido activado exitosamente");
			window.location.reload();
		}else if(http.responseText=="inactivo") {
			alert("El proveedor ha sido inactivado exitosamente");
			window.location.reload();
		}else{
			alert("La operaci"+oACUTE+"n no pudo ser realizada");
		}
	}
	return;
}
/*FIN AJAX*/
function activar(indice){
	if(confirm(pACUTE+"Est"+aACUTE+" seguro que desea activar el proveedor "+proveedoresNombre[indice]+"?")){
		sndReq("activar.php?rif="+proveedoresRif[indice]+"&estado=<?= $estadoActivo?>");
	}
	return;
}
function inactivar(indice){
	if(confirm(pACUTE+"Est"+aACUTE+" seguro que desea inactivar el proveedor "+proveedoresNombre[indice]+"?")){
		sndReq("activar.php?rif="+proveedoresRif[indice]+"&estado=<?= $estadoInactivo?>");
	}
	return;
}
function detalle(codigo){
	url="detalle.php?codigo="+codigo;
	newwindow=window.open(url,'name','height=470,width=600,scrollbars=yes');
	if (window.focus) { newwindow.focus(); }
}
</script>
</head>
<body>
	<form id="formProveedores" name="formProveedores" action="buscar.php">
		<input type="hidden" id="pagina" name="pagina" value="1"/>
		<table width="620" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
			<tr class="td_gray">
				<td height="16" colspan="3" align="left" class="normalNegroNegrita">B&Uacute;SQUEDA DE PROVEEDORES</td>
			</tr>
			<tr>
				<td class="normalNegrita" colspan="3">
					<!--  <input type="radio" value="3" name="criterio" id="criteriosMultiples"/> -->
					&nbsp;&nbsp;&nbsp;B&uacute;squeda por criterios m&uacute;ltiples
				</td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
				<td class="normalNegrita">Nombre:</td>
				<td class="normalNegrita">
					<input autocomplete="off" size="68" type="text" id="nombre" name="nombre" value="<?= $nombre?>" />
				</td>
			</tr>	
             <tr>

			<td width="40">&nbsp;</td>
			<td class="normalNegrita">Estado: </td>
			<td class="peq_naranja" colspan="3">
				<select id="estado" name="estadovenezuela" class="normal">
					<option value="">[Seleccione]</option>
					<option value="888" selected="selected">--</option>					
					<?php
					$sql="SELECT id, nombre FROM safi_edos_venezuela WHERE estatus_actividad = '1' ORDER BY nombre"; 
					$resultado=pg_query($conexion,$sql) or die("Error al mostrar los estados");
					while($row=pg_fetch_array($resultado)){ 
					?>
						<option <? if($estadovenezuela == $row['id']){ echo "selected='selected'";} ;  ?>value="<?= $row['id']?>"><?= $row['nombre']?></option>
					<?php 
					}
					?>
				</select>
			</td>
		</tr>		

					
			<tr>
				<td width="40">&nbsp;</td>
				<td class="normalNegrita">Estatus:</td>
				<td class="normalNegrita">
					<select name="estado" id="estado" class="normal" <?php /*if($rif!="" || $codigo!=""){echo "disabled='disabled'";}*/?>>
						<option value="0">[Seleccione]</option>
						<option value="1" <?php if($estado == $estadoActivo){echo "selected='selected'";}?>>Activo</option>
						<option value="2" <?php if($estado == $estadoInactivo){echo "selected='selected'";}?>>Inactivo</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40">&nbsp;</td>
				<td class="normalNegrita">Tipo:</td>
				<td class="normalNegrita">
					<select name="tipo" id="tipo" class="normal" <?php /*if($rif!="" || $codigo!=""){echo "disabled='disabled'";}*/?>>
						<option value="0">[Seleccione]</option>
						<?php
						$sql="SELECT prtp_id, prtp_nombre FROM sai_tipo_proveedor";
						$resultado=pg_query($conexion,$sql) or die("Error al mostrar");
						while($row=pg_fetch_array($resultado)){
							$prtp_id=trim($row['prtp_id']);
							$prtp_nombre=$row['prtp_nombre'];
							?>
							<option value="<?=$prtp_id?>" <?php if($tipo == $prtp_id){echo "selected='selected'";}?>><?= $prtp_nombre?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40" class="normalNegrita" colspan="2">
					<!--  <input type="radio" value="1" name="criterio" id="criterioRif" />-->
					&nbsp;&nbsp;&nbsp;RIF:
				</td>
				<td class="normalNegrita">
					<input type="text" id="rif" name="rif" value="<?= $rif?>" autocomplete="off" onkeyup="validarCodigo(this);" <?php /*if(!$rif || $rif==""){echo "disabled='disabled'";}*/?> class="normal"/>
				</td>
			</tr>
			<tr>
				<td width="40" class="normalNegrita" colspan="2">
					<!-- <input type="radio" value="2" name="criterio" id="criterioCodigo" /> -->
					&nbsp;&nbsp;&nbsp;C&oacute;digo:
				</td>
				<td class="normalNegrita">
					<input type="text" id="codigo" name="codigo" value="<?= $codigo?>" autocomplete="off" onkeyup="validarInteger(this);" <?php /*if(!$codigo || $codigo==""){echo "disabled='disabled'";}*/?> class="normal"/>
				</td>
			</tr>
			<tr align="center">
				<td colspan="3" align="center">
	  				<input class="normalNegro" type="button" value="Buscar" onclick="javascript:buscar();"/>
	  				<input class="normalNegro" 	type="reset" value="Limpiar"/>
	  			</td>
			</tr>
		</table>
	</form>
	<?php
	$query = 	"SELECT COUNT(prov_id_rif) ".
				"FROM sai_proveedor_nuevo sp ";
	if($rif && $rif!=""){
		$rif = trim($rif);
		$query .= 	"WHERE ".
					"upper(sp.prov_id_rif) like '%".strtoupper($rif)."%' ";
	}else if($codigo && $codigo!=""){
		$codigo = trim($codigo);
		$query .= 	"WHERE ".
					"sp.prov_codigo = ".$codigo." ";
	}else{
		
		
	$where = true;
		
		if($nombre && $nombre!=""){
			
			
			$query .="where ";
			$where = false;
			//$query .=	"lower(sp.prov_nombre) like '%".cadenaAMinusculas($nombre)."%' AND ";
			$query .=	" lower(sp.prov_nombre) like '%'||lower('".$nombre."')||'%'";
			
		}
		if($tipo!="" && $tipo!="0"){
			
			if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
			$query .=	"  sp.prov_prtp_id = ".$tipo." ";
		}
		if($estado!="" && $estado!="0"){
			
		if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
			$query .=	"  sp.prov_esta_id = '".$estado."' ";	
		}
	       if($estadovenezuela!=""){
	       	
	       if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
				if($estadovenezuela == "888")
				{
					$query .= " sp.id_estado is null ";
				}
				else
				{
					$query .= " sp.id_estado = '".$estadovenezuela."' ";
				}
			}
		
		
		
		
		
	}
	//echo $query."<br/>";
	
	$resultadoContador = pg_exec($conexion, $query);
	$row = pg_fetch_array($resultadoContador, 0);
	$contador = $row[0];
	$totalPaginas = ($contador%$tamanoPagina == 0)?$contador/$tamanoPagina:intval($contador/$tamanoPagina)+1;

	$query = 	"SELECT ".
					"sp.temporal, ".
					"sp.prov_id_rif, ".
					"sp.prov_nombre, ".
					"sp.prov_telefonos, ".
					"sp.prov_esta_id, ".
					"sp.prov_codigo, ".
					"se.esta_nombre, ".
					"stp.prtp_nombre, ".
	    		    "sp.id_estado,".
                    "ev.nombre as nombre_estado".
				" FROM 
				      sai_proveedor_nuevo sp
				      inner join sai_tipo_proveedor stp on(sp.prov_prtp_id=stp.prtp_id)
				      inner join sai_estado se on(sp.prov_esta_id=se.esta_id) 
				      left join safi_edos_venezuela ev on(sp.id_estado = ev.id) 
				
				";
	if($rif=="" && $codigo=="" && $nombre=="" && $estado=="")
	{
		$query .=	"WHERE sp.prov_id_rif <> '' ";
	}
	
	if($rif && $rif!="")
	{
		$query .=	"WHERE upper(sp.prov_id_rif) like '%".strtoupper($rif)."%'".
				
					"ORDER BY sp.prov_nombre ";
	}else if($codigo && $codigo!="")
	{
		$query .=	" WHERE sp.prov_codigo = ".$codigo." ".
				
					"ORDER BY sp.prov_nombre ";
	}
	else
	{
		$where = true;
		
		if($nombre && $nombre!=""){
			
			
			$query .="where ";
			$where = false;
			//$query .=	"lower(sp.prov_nombre) like '%".cadenaAMinusculas($nombre)."%' AND ";
			$query .=	" lower(sp.prov_nombre) like '%'||lower('".$nombre."')||'%'";
			
		}
		if($tipo!="" && $tipo!="0"){
			
			if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
			$query .=	"  sp.prov_prtp_id = ".$tipo."";
		}
		if($estado!="" && $estado!="0"){
			
		if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
			$query .=	"  sp.prov_esta_id = '".$estado."'";	
		}
	       if($estadovenezuela!=""){
	       	
	       if($where){
				
			$query .="where ";
			$where = false;
			}else{
				
				$query .="AND  ";
			}
			
				if($estadovenezuela == "888")
				{
					$query .= " sp.id_estado is null ";
				}
				else
				{
					$query .= " sp.id_estado = '".$estadovenezuela."' ";
				}
			}
		$query .= 	
					" ORDER BY sp.prov_nombre ".
					"LIMIT ".$tamanoPagina." OFFSET ".$desplazamiento;
	}
	//echo $query;	
	$resultado = pg_exec($conexion, $query);
	$numeroFilas= pg_num_rows($resultado);
	?>
	<br/>
	<table width="80%" align="center" background="../../imagenes/fondo_tabla.gif" class="tablaalertas">
	<?php
	if($numeroFilas>0){
	?>
		<tr class="td_gray">
			<td width="62" height="15" class="normalNegroNegrita">
				<div align="center">C&oacute;digo</div>
			</td>
			<td width="62" height="15" class="normalNegroNegrita">
				<div align="center">RIF</div>
			</td>
			<td width="254" class="normalNegroNegrita">
				<div align="center">Proveedor</div>
			</td>
			<td width="254" class="normalNegroNegrita">
				<div align="center">Tipo</div>
			</td>
			<td width="107" class="normalNegroNegrita">
				<div align="center">Tel&eacute;fono</div>
			</td>
			<td width="90" class="normalNegroNegrita">
				<div align="center">Estado</div>
			</td>
			<td width="90" class="normalNegroNegrita">
				<div align="center">Estatus</div>
			</td>
			<td width="90" class="normalNegroNegrita">
				<div align="center">Tipo</div>
			</td>
			<td width="83" class="normalNegroNegrita">
				<div align="center">Opciones</div>
			</td>
		</tr>
		<script>
			proveedoresRif = new Array();
			proveedoresNombre = new Array();
		</script>
		<?php
		$i = 0;
		while($row=pg_fetch_array($resultado)){
			$prov_id_rif=$row['prov_id_rif'];
			$prov_nombre=$row['prov_nombre'];
			$prov_telefonos=$row['prov_telefonos'];
			$prov_esta_id=$row['prov_esta_id'];
			$nombre_estado_venezuela=$row['nombre_estado'];
			$prov_codigo=$row['prov_codigo'];
			$esta_nombre=$row['esta_nombre'];
			$prtp_nombre=$row['prtp_nombre'];
			$temporal=$row['temporal'];
			?>
			<tr class="normal">
				<td align="center"><?=$prov_codigo?></td>
				<td height="28" align="left"><?=$prov_id_rif?></td>
				<td align="left"><?=$prov_nombre?></td>
				<td align="center"><?=$prtp_nombre?></td>
				<td align="left"><?=$prov_telefonos?></td>
				<td align="left"><div align="center"><? echo $nombre_estado_venezuela != "" ?  $nombre_estado_venezuela : '-';?></div></td>
				
				<td>
				<div align="center"><?= $esta_nombre?></div>
				</td>
				<td>
				<div align="center"><?= $temporal == 1 ? "Temporal" : "Fijo"?></div>
				</td>
				<td align="center">
					<img src="../../imagenes/vineta_azul.gif" width="11" height="7"/>
					<a href="javascript:detalle('<?= trim($prov_id_rif);?>')" class="normal"> Ver Detalle</a>
					<br/>
					<?if ($user_perfil_id == "01000" || $user_perfil_id == "15456" || $user_perfil_id == "42456"){?>
						<img src="../../imagenes/vineta_azul.gif" width="11" height="7"/>
						<a href="javascript:modificar('<?= trim($prov_id_rif);?>')" class="normal"> Modificar</a>
					<?}?>
					<?if ($user_perfil_id == "01000" || /*$user_perfil_id == "15456" || //solo puede anular coordinador*/ $user_perfil_id == "42456"){?>
						<br/>
						<img src="../../imagenes/vineta_azul.gif" width="11" height="7"/>
						<?php if($prov_esta_id==$estadoActivo){ ; ?>
						<a href="javascript:inactivar(<?= $i?>)" class="normal"> Inactivar</a>
						<?}else if($prov_esta_id==$estadoInactivo){?>
						<a href="javascript:activar(<?= $i?>)" class="normal"> Activar</a>
					<?	}
					}?>
				</td>
			</tr>
			<script>
			
				proveedoresRif[proveedoresRif.length] = '<?= $prov_id_rif;?>';
				proveedoresNombre[proveedoresNombre.length] = '<?= $prov_nombre." (Rif ".strtoupper(substr(trim($prov_id_rif),0,1))."-".substr(trim($prov_id_rif),1).")"?>';
			</script>
	<?php
			$i++;
		}
		
		echo "<tr class='td_gray normalNegrita'><td colspan='9' align='center'>";
		$ventanaActual = ($pagina%$tamanoVentana==0)?$pagina/$tamanoVentana:intval($pagina/$tamanoVentana)+1;
		$ri = (($ventanaActual-1)*$tamanoVentana)+1;
		while($ri<=$ventanaActual*$tamanoVentana && $ri<=$totalPaginas) {
			if($ri==(($ventanaActual-1)*$tamanoVentana)+1 && $ri!=1){
				echo "<a onclick='listarProveedores(".($ri-1).");' style='cursor: pointer;text-decoration: underline;'>&lt;</a> ";
			}
			if($ri==$pagina){
				echo $ri." ";
			}else{
				echo "<a onclick='listarProveedores(".$ri.");' style='cursor: pointer;text-decoration: underline;'>".$ri."</a> ";
			}
			if($ri==$ventanaActual*$tamanoVentana && $ri<$totalPaginas){
				echo "<a onclick='listarProveedores(".($ri+1).");' style='cursor: pointer;text-decoration: underline;'>&gt;</a> ";
			}
			$ri++;   	
		}
		echo "</td></tr>\n";
		
	}else{
		echo "<tr><td class='normal' height='40' align='center'>No se encontraron resultados</td></tr>";
	}
	?>
	</table>
	<br/>
</body>
</html>