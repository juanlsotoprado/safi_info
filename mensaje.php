<?
	$pagina = "bise_entr.php";
	if (isset($_REQUEST["pag"])) {
		$pagina = strtolower($_REQUEST["pag"]);	
	}
	
	if ($pagina == "documentos") {
		if (isset($_REQUEST["tipo"])) {
			$tipo_documento = $_REQUEST["tipo"];	
		}	
		$pagina = $pagina .".php?tipo=".$tipo_documento;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link  rel="stylesheet" href="css/plantilla.css" type="text/css" media="all"  />
<body onLoad="bloquear()">
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" background="imagenes/fondo_tabla.gif">
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
<?php 

if ($_REQUEST['msj']==""){?>
  <tr>
    <td width="20"></td>
    <td width="10">&nbsp;</td>
    <td width="296" colspan="2" class="titularMedio"><div align="center">Usted no tiene permisos para realizar esta acci&oacute;n </div></td>
  </tr>
  <?php } else {?>
  <tr>
    <td width="20"></td>
    <td width="10">&nbsp;</td>
    <td width="296" colspan="2" class="titularMedio"><div align="center"><?php echo ($_REQUEST['msj']);?></div></td>
  </tr>
  <?php }?>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4"><div align="center"><img src="imagenes/mano_bad.gif" width="31" height="38"></div></td>
  </tr>
 
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4"><div align="center"></div></td>
  </tr>
</table>
</body>
</html>
