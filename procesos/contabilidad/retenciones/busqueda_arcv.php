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
<title>.:SAI:Buscar Documentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../../css/plantilla.css" rel="stylesheet" type="text/css" /><script LANGUAGE="JavaScript" SRC="../../includes/js/funciones.js"> </SCRIPT>
<script language="JavaScript" src="../../../js/lib/actb.js"></script>
</head>
<SCRIPT>
function validarRif(rif){

	
	  if (document.form.rif.value==''){
			alert("Debe seleccionar el proveedor para generar el ARCV");
			document.form.rif.focus();
			return;
		  }

	  if (document.form.a_o.value==0) {
			alert("Debe seleccionar el a\u00F1o para generar el ARCV");
			document.form.a_o.focus();
			return;
		  }

	  
	var encuentra=0;
	for(j= 0; j < proveedor.length; j++){
		if(rif==proveedor[j]){
			document.form.submit();
			encuentra=1;
		}
	}
	
	if (encuentra==0){
	alert("Este RIF indicado no es v\u00E1lido");
	document.form.rif.focus();
	return false;			
	}
}

</SCRIPT>
<body>
<form name="form" action="arcv.php" method="post">
  <div align="center">
  <input type="hidden" value="0" name="hid_validar" />
  <br />
  </div>
  <table width="515" align="center" background="../../../imagenes/fondo_tabla.gif" class="tablaalertas">
<tr class="td_gray"> 
  <td height="21" colspan="2" class="normalNegroNegrita" align="left">ARCV</td>
</tr>
<tr>
  <td height="10" colspan="2"></td>
</tr>
	  <tr>
	    <td><div align="left" class="normal"><strong>Rif del Proveedor:</strong></div></td>
		<td><input type="text" name="rif" id="rif" class="normalNegro" size="70" >
		<?php 	
		$query = 	"SELECT prov_id_rif as id,prov_nombre as nombre ".
							"FROM ".
							"sai_proveedor_nuevo ".

							"UNION
							SELECT benvi_cedula as id, (benvi_nombres || ' ' || benvi_apellidos)  as nombre
							FROM
							sai_viat_benef WHERE benvi_esta_id=1
							UNION
							SELECT empl_cedula as id, (empl_nombres || ' ' || empl_apellidos) as nombre
							FROM sai_empleado WHERE esta_id=1
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
					actb(document.getElementById('rif'),proveedor);
				</script>
		</td></tr>
	 <tr>
	  <td><div align="left" class="normal"><strong>A&ntilde;o:</strong></div></td>
	 <td colspan="3" align="left">
		<span  class="normalNegro">
	 	
	   <select name="a_o" id="a_o"  class="normalNegro">
	   <?php 
		 $sql_ff="SELECT * FROM  sai_presupuest order by pres_anno DESC";
		 $res_ff=pg_exec($sql_ff);
	   ?>
		 <option value="0">--</option>
		 <?php while($row_ff=pg_fetch_array($res_ff)){?>
		 <option value="<?php echo $row_ff['pres_anno']?>"><?php echo $row_ff['pres_anno']?></option>
		 <?php }?>
		</select>
		</span>
	 </td>
   </tr>
</table><br>

<div align="center">
<input type="button" value="Buscar" onclick="javascript:validarRif(document.form.rif.value);">
  </div>
</form>
<br>
</body>
</html>
<?php pg_close($conexion);?>