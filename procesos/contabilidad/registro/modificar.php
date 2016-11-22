<?php 
	ob_start();
	require_once("../../../includes/conexion.php");
	require("../../../includes/perfiles/constantesPerfiles.php");
	$id_registro =  trim($_GET['idRegistro']);	
	$nro_documento =  trim($_GET['idDocumento']);
	$tipo_documento = trim($_GET['tipoDocumento']);
	$dependencia = trim($_GET['dependencia']);
	$beneficiario= trim($_GET['beneficiario']);
	$monto = trim($_GET['monto']);
	$nro_compromiso = trim($_GET['compromiso']);
	$observaciones = trim($_GET['observaciones']);	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link  rel="stylesheet" href="../../../css/plantilla.css" type="text/css" media="all"  />
<title>.:SAFI::Modificar Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" />
<link href="../../../css/safi0.2.css" rel="stylesheet" type="text/css" />
<link href="../../../js/lib/jquery/themes/ui.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
<script language="JavaScript" src="../../../js/funciones.js"> </script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/jquery.min.js"></script>
<script type="text/javascript" src="../../../js/lib/jquery/plugins/ui.min.js"></script>
<script language="JavaScript" src="../../../js/registrarDocumento.js"></script>
<script language="JavaScript">
</script>
</head>
<body>
<br/>
<form name="form1" method="post" action="modificarAccion.php" id="form1" enctype="multipart/form-data" >
<table align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas" >
	<tr class="td_gray"> 
		<td colspan="2" class="normalNegroNegrita" align="center">Entrega documento</td>
	</tr>
<tr>
<td class="normal" align="left"><strong>Dependencia:</strong></td>
<td><?php echo $dependencia;?></td>
</tr>
<tr>
	<td class="normal" align="left"><strong>Tipo de documento: </strong></td>
	<td class="normalNegro"><?php echo $tipo_documento;?>	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Documento:</b></div></td>
	<td width="80%" class="normalNegro">
	<?php echo $nro_documento;?>
	</td>
</tr>
<tr>
	<td><div align="left" class="normal"><strong>Beneficiario:</strong></div></td>
	<td><?php echo $beneficiario;?>	</td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Monto:</b></div></td>
	<td><?php echo $monto;?></td>
</tr>
<tr>
   	<td><div align="left" class="normal"><b>Nro. Compromiso:</b></div></td>
	<td><?php echo $nro_compromiso;?></td>
</tr>	
<tr>
   	<td><div align="left" class="normal"><b>Observaciones:</b></div></td>
	<td><?php echo $observaciones;?></td>
</tr>
<tr>
<td class="normal" align="left"><strong>Dependencia:</strong></td>
<td>
	<?php
	    $sql_str="SELECT depe_id,
	    			depe_nombrecort,
	    			depe_nombre
	    		FROM sai_dependenci
	    		WHERE depe_nivel='5' or depe_nivel='4' or depe_nivel='3'
	    		order by depe_nombre";
	    $res_q=pg_exec($sql_str);		  
	?>
        <select name="dependencia" class="normalNegro" id="dependencia">
    	    <option value="-1">Seleccione...</option>
	    	<?php while($depe_row=pg_fetch_array($res_q)){ ?>
             <option value="<?php echo $depe_row['depe_id'].":".trim($depe_row['depe_nombre']); ?>"><?php echo(trim($depe_row['depe_nombre'])); ?></option>
        	<?php }?>
        </select>		   
</td>
</tr>	
<tr>
	<td><div align="left" class="normal"><strong>Recibido por:</strong></div></td>
	<td><input type="text" name="beneficiario" id="beneficiario" class="normalNegro" size="70"/>
		<?php 	
		$query = 	"SELECT benvi_cedula AS id,
						 benvi_nombres || ' ' || benvi_apellidos AS nombre
					FROM
						sai_viat_benef 
					UNION
					SELECT empl_cedula AS id,
						empl_nombres || ' ' || empl_apellidos AS nombre
					FROM sai_empleado
					ORDER BY 2";
		
		        $resultado = pg_exec($conexion, $query);
				$numeroFilas = pg_num_rows($resultado);
				$arregloProveedores = "";
				$cedulasProveedores = "";
				$nombresProveedores = "";
				$indice=0;
				while($row=pg_fetch_array($resultado)){
					$arregloProveedores .= "'".$row["id"]." : ".strtoupper(str_replace("\n"," ",$row["nombre"]))."',";
					$cedulasProveedores .= "'".$row["id"]."',";
					$nombresProveedores .= "'".str_replace("\n"," ",strtoupper($row["nombre"]))."',";
					$indice++;
				}
					$arregloProveedores = substr($arregloProveedores, 0, -1);
					$cedulasProveedores = substr($cedulasProveedores, 0, -1);
					$nombresProveedores = substr($nombresProveedores, 0, -1);
			?>
				<script>			
					var proveedor = new Array(<?= $arregloProveedores?>);
					var arreglo_rif = new Array(<?= $cedulasProveedores?>);
					var nombre_proveedor= new Array(<?= $nombresProveedores?>);
					actb(document.getElementById('beneficiario'),proveedor);
				</script>
			
	</td></tr>

	<tr align="center">
		<td colspan="2" class="normal" align="center"><br></br>
		<input type="hidden" id="idRegistro" name="idRegistro" value="<?php echo $id_registro;?>"></input>
		<input type="hidden" id="idDocumento" name="idDocumento" value="<?php echo $nro_documento;?>"></input>		
		<input type="submit" value="Modificar"/>
		</td>
</tr>	
</table>
</form>
</body>
</html>