<?php 
ob_start();
session_start();
require_once("includes/conexion.php");
if (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado")) {
	header('Location:index.php',false);
   	ob_end_flush(); 
	exit;
}
ob_end_flush(); 
	
//Login del usuario
$usuario = $_SESSION['login'];
//Perfil del usuario
$user_perfil_id = $_SESSION['user_perfil_id'];

//Buscar nombre del tipo de documento a iniciar
$request_id_tipo_documento = "";
if (isset($_REQUEST["tipo"])) {
	$request_id_tipo_documento = $_REQUEST["tipo"];	
}
$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$nombre_documento = $row["resultado"];
}
	
//Estado Pendiente, para las sopg que falta Pago Con Cheque
if ($user_perfil_id==36450)
$estado_doc = 39;
else if ($user_perfil_id==71450)
$estado_doc = 39;
	
//Id del doc tipo 1 pendiente para iniciar el siguiente
$request_id_tipo_documento1 = "sopg";
$sql = " SELECT * FROM sai_buscar_nombre_docu('$request_id_tipo_documento1') as resultado ";
$resultado = pg_query($conexion,$sql) or die("Error al mostrar");
if ($row = pg_fetch_array($resultado)) {
	$nombre_documento1 = $row["resultado"];
}
		
//Buscar documentos sopg pendientes por pgch 
$sql = "SELECT s.sopg_id as sopg, to_char(s.sopg_fecha, 'DD/MM/YYYY') as fecha, upper(coalesce(em.empl_nombres,''))||' '||upper(coalesce(em.empl_apellidos,'')) || upper(coalesce(p.prov_nombre,'')) as nombre_beneficiario, upper(coalesce(v.benvi_nombres,'')) ||' '|| upper(coalesce(v.benvi_apellidos,'')) as beneficiariov, upper(substring(s.sopg_detalle from 0 for 45))  as detalle 
		FROM sai_sol_pago s
		left outer join sai_doc_genera d on (d.docg_id=s.sopg_id and d.esta_id=".$estado_doc.")
		left outer join sai_proveedor_nuevo p on (trim(p.prov_id_rif)=trim(s.sopg_bene_ci_rif))
		left outer join sai_empleado em on (trim(em.empl_cedula)=trim(s.sopg_bene_ci_rif) and trim(em.empl_cedula) not in (select prov_id_rif from sai_proveedor_nuevo where prov_esta_id=1))
		left outer join sai_viat_benef v on (trim(v.benvi_cedula)=trim(s.sopg_bene_ci_rif) and trim(v.benvi_cedula) not in (select prov_id_rif from sai_proveedor_nuevo where prov_esta_id=1))
		where d.docg_id=s.sopg_id and sopg_fecha not like '2008%'
		ORDER BY s.sopg_fecha";
$resultado=pg_query($conexion,$sql) or die("Error al mostrar documentos pendientes por generar cheque y/o transferencia");
$total=pg_num_rows($resultado);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>SAFI: Documentos Pendientes Pgch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/plantilla.css" rel="stylesheet" type="text/css">
<script language="JavaScript" SRC="js/funciones.js"> </script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" background="imagenes/fondo_tabla.gif" class="tablaalertas">
      <?php	 if ($total>0) { ?>
		  <tr class="td_gray">
            <td class="normalNegroNegrita">C&oacute;digo</td>
            <td class="normalNegroNegrita">Fecha</td>
            <td class="normalNegroNegrita">Beneficiario</td>
            <td class="normalNegroNegrita">Detalle</td>            
            <td class="normalNegroNegrita">Opciones</td>
          </tr>	  
		 <?php  while($row=pg_fetch_array($resultado)) { ?>
          <tr>
            <td class="normal"><?php echo $row['sopg'];?></td>
            <td class="normal"><?php echo $row['fecha'];?></td>
            <?php
             if (strlen($row['beneficiariov'])>2) $beneficiario = $row['beneficiariov'];
             else $beneficiario = $row['nombre_beneficiario'];
            ?>
            <td class="normal"><?php echo $beneficiario;?></td>
            <td class="normal"><?php echo $row['detalle'];?></td>            
            <td><div align="center" class="normal"><img src="imagenes/vineta_azul.gif" width="11" height="7"><a href="accion_documento.php?accion=1&tipo=<? echo $request_id_tipo_documento;  ?>&codigo=<?php echo $row['sopg'];?>" class="copyright"> Iniciar Pago</a>
			<br>
			<img src="imagenes/vineta_azul.gif" border="0">
		<a href="javascript:abrir_ventana('documentos/<? echo $request_id_tipo_documento1;  ?>/<? echo $request_id_tipo_documento1;  ?>_detalle.php?codigo=<?php echo trim($row['sopg']); ?>&esta_id=39')" class="copyright">Ver detalle</a>
		</div></td>
          </tr>
		 <?php
		    } //END WHILE
		   }
		   else {
		 ?> 
          <tr>
            <td colspan="5"><div align="center" class="normalNegrita">No existen documentos en bandeja</div></td>
          </tr>
		  <?php } ?>
 </table>
<?pg_close($conexion);?>
</body>
</html>