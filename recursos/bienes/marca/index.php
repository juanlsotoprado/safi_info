<?php 
    ob_start();
	session_start();
	 require_once("../../../includes/conexion.php");
	 
	  if  ( empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
	  {
		   header('Location:../../../index.php',false);
	   	   ob_end_flush(); 	
		   exit;
	  }	ob_end_flush(); 
	  
 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>.:SAI:BIENES</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../ccs/plantilla.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="660" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" valign="middle"><table width="329" border="0" align="center" cellpadding="0" cellspacing="0" class="tablaalertas" >
          <tr>
            <td colspan="4"><img src="../../../imagenes/img_bandeja_prin.jpg" width="326" height="29"></td>
          </tr>
          <tr>
            <td><div align="right"><img src="../../../imagenes/vineta_azul.gif" width="11" height="7"></div></td>
            <td>&nbsp;</td>
            <td class="titularMedio">MARCAS</td>
            <td class="titularMedio">&nbsp;</td>
          </tr>
          <tr>
            <?php
	 $sql_1="SELECT * FROM sai_seleccionar_campo('sai_bien_marca','bmarc_id','','',2)resultado_set(bmarc_id int4)";
	 $resultado_set_most_1=pg_query($conexion,$sql_1) or die("Error al consultar");  
     $filas = pg_NumRows($resultado_set_most_1);
    	?>
            <td width="19"><div align="right"><img src="../../../imagenes/vineta_azul.gif" width="11" height="7"></div></td>
            <td width="9">&nbsp;</td>
            <td width="293" class="titularMedio">EXISTEN
              <?php  echo $filas;?>
              MARCAS REGISTRADAS </td>
            <td width="6" class="titularMedio">&nbsp;</td>
          </tr>
        </table></td>
        <td><table width="237" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="30">&nbsp;</td>
            <td width="207">&nbsp;</td>
          </tr>
          <tr>
            <td width="30"><div align="left"><img src="../../../imagenes/icon_opc.gif" width="21" height="14"></div></td>
            <td class="pestanaverde"> <span class="GrandeNeg">Opciones</span></td>
          </tr>
          <tr>
            <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td width="30"><div align="right"><img src="../../../imagenes/icon_chk_blu.gif" width="14" height="15"></div></td>
                <td width="10">&nbsp;</td>
                <td width="160"><a href="acciones_recientes.php" class="link">Acciones Recientes </a> </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="link">&nbsp;</td>
              </tr>
            </table></td>
            </tr>
          <tr>
            <td width="30"><div align="left"><img src="../../../imagenes/icon_doc.gif" width="20" height="10"></div></td>
            <td class="GrandeNeg">Procesos</td>
          </tr>
          <tr>
            <td colspan="2"><table width="200" border="0" cellpadding="0" cellspacing="0">
			  <tr>
                <td width="30"><div align="right"><img src="../../../imagenes/icon_chk_blu.gif" width="14" height="15"></div></td>
                <td width="10">&nbsp;</td>
                <td width="160" ><a href="marc_1.php" class="link">Ingresar </a></td>
              </tr>
			  <tr>
                <td><div align="right"><img src="../../../imagenes/icon_chk_blu.gif" width="14" height="15"></div></td>
                <td>&nbsp;</td>
                <td ><a href="buscar_marc.php" class="link">Administrar </a></td>
              </tr>
             </table></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="19">&nbsp;</td>
  </tr>
</table>
</body>
</html>