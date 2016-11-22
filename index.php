<SCRIPT LANGUAGE="JavaScript">	
	if(window.opener.parent){
		window.opener.location.reload();
		window.close();
		} 

</SCRIPT>
<?php
ob_start();
session_start();

if(isset($_REQUEST["error"])){
	$error = $_REQUEST["error"];
}else{
	$error = "";
}

if( !empty($_SESSION['login']) && ($_SESSION['registrado']=="registrado") ){
	header('Location:principal.php',false);
	ob_end_flush();
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>.:SAFI:Sistema Administrativo de la Fundaci&oacute;n Infocentro</title>
<link rel="stylesheet" type="text/css" href="css/safi0.2.css"/>
<link rel="stylesheet" type="text/css" href="css/plantilla.css"/>
<script type="text/javascript" src="js/constantes.js"></script>
<script type="text/javascript" src="js/lib/prototype.js"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
function onLoad()
{	
	$('usuario').focus();
}

function revisar(){	
	if($("usuario").value==""){
		alert("Debe colocar el nombre de usuario");
		$('usuario').focus();
		return;
	}else{
		if($("contrasena").value==""){
			alert("Debe colocar la contrasena");
			$("contrasena").focus();
			return;
		}else{
			$("form1").submit();
		}
	}
}
function iSubmitEnter(oEvento){
     var iAscii;
     if (oEvento.keyCode)
         iAscii = oEvento.keyCode;
     else if (oEvento.which)
         iAscii = oEvento.which;
     else
         return false;

     if (iAscii == 13){ 
		revisar();
     }
     return true;
}
//-->
</script>
</head>
<body class="body" onload="onLoad();">
	<img
		style="width: 100%; height: 100%; top: 0; left: 0; position: fixed; z-index: -1;"
		src="imagenes/bienvenida-index.jpg" alt="background image" />
	<!-- 
<img src="imagenes/banner-safi0.2.jpg" width="100%"/>
 -->
	<img src="imagenes/Banner-Safi-Modificacion.jpg" width="100%" />
	<form id="form1" name="form1" method="post" action="principal.php">
		<br /> <br /> <br /> <br /> <img src="imagenes/fondo_tabla.gif" alt="" />

		<table width="80%" align="center">
			<tr>
				<td>
					<table border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td height="100px">&nbsp;</td>
						</tr>
						<tr>
							<td height="30px" class='normal'
								style='color: red; text-align: center;'><?php
								if($error=="1"){
									$error = "Nombre de usuario o contrase&ntilde;a inv&aacute;lida";
									echo $error;
								}else{
									echo "&nbsp;";
								}
								?>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>

					<table background="imagenes/fondo_tabla.gif"
						style='border-radius: 10px; box-shadow: 0 0 10px #ddd; border: 1px solid #ddd;'
						Negrita tablaalertas" border="0" cellpadding="0" cellspacing="0"
						align="center">
						<tr>
							<td width="250px" height="110px" align="center" valign="middle" >
								<table width="280px" height="100px" border="0" cellpadding="0"
									cellspacing="0" align="center" class="peqNegrita" style="padding: 10px">
									<tr>
										<td style="font-size: 14px; font-weight: bold;" border="1"
											width="100px" class="normal">Usuario:</td>
										<td align="center"><input
											style="-webkit-border-radius: 6px; -webkit-border-bottom-right-radius: 10px; -moz-border-radius: 6px; -moz-border-radius-bottomright: 10px; border-radius: 6px; border-bottom-right-radius: 10px;height: 25px;"
											class="normalNegro" id="usuario" name="usuario" type="text"
											id="userid" onkeypress="iSubmitEnter(event)" />
											<br></br></td>
									</tr>
									<tr>
										<td  style="font-size: 14px; font-weight: bold;" class="normal">Contrase&ntilde;a:</td>
										<td align="center" ><input style="-webkit-border-radius: 6px; -webkit-border-bottom-right-radius: 10px; -moz-border-radius: 6px; -moz-border-radius-bottomright: 10px; border-radius: 6px; border-bottom-right-radius: 10px;height: 25px;" class="normalNegro" id="contrasena"
											name="contrasena" type="password"
											onkeypress="iSubmitEnter(event)" /><br></br></td>
									</tr>

									<tr>
										<td colspan="2" align="center"><input class="normalNegro"
											type="button" value="Entrar" onclick="revisar();" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td height="200px">&nbsp;</td>
			</tr>
		</table>
	</form>
	<!-- PIE DE PAGINA -->
	<div style="margin-top: 13.4%;" align="center"></div>
	<img src="imagenes/linea-safi0.2.jpg" width="100%" height="3px;" />
	<div
		style="background-color: #EBF5EC; color: #63666B; font-weight: bold; font-size: 9px; padding-top: 2px; padding-bottom: 2px;"
		align="center">Sistema Administrativo de la Fundaci&oacute;n
		Infocentro - SAFI v.0.2 2010. Basado en el Sistema Administrativo
		Integrado - SAI 2006 - de la Fundaci&oacute;n Instituto de
		Ingenier&iacute;a</div>
	<img src="imagenes/linea-safi0.2.jpg" width="100%" height="3px;" />
	<!-- FIN PIE DE PAGINA -->
</body>
</html>
