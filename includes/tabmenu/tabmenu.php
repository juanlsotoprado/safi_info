<?php
ob_start();
session_start();
$perfilActual=$_SESSION['user_perfil_id'];
if(isset($_REQUEST['tabmenu'])){
	require("./../perfiles/constantesPerfiles.php");
	require("./../constantes.php");
}else{
	require("includes/perfiles/constantesPerfiles.php");
}
require_once("tabmenuItems.php");
require("tabmenuItemsStructure.php");
$tabmenu=$_REQUEST['tabmenu'];
$tabmenuItem=$_REQUEST['tabmenuItem'];
?>
<div id="ddtabs1" class="basictab" style="float: left;width: 100%;">
	<ul>
<?php

if($tabmenu && $tabmenuItem){
	$tabmenuCadena = "";
	foreach ($tabmenuItemsArray[$tabmenu] as $item) {
		if(sizeof($item["PERFILES"])==0 || in_array($perfilActual, $item["PERFILES"]) || in_array((substr($perfilActual, 0, 2)."000"), $item["PERFILES"])){
			if($item["ID"] == $tabmenuItem){
				$tabmenuCadena .= "<li><a id='".$item["ID"]."' href='javascript: abrirTabmenuItem(\"".$item["URL"]."\",\"".$item["ID"]."\");' style='	color: #575757;
	-webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px;
border: 1px solid #575757;'>".$item["NOMBRE"]."</a></li>";
			}else{
				$tabmenuCadena .= "<li><a id='".$item["ID"]."' href='javascript: abrirTabmenuItem(\"".$item["URL"]."\",\"".$item["ID"]."\");'>".$item["NOMBRE"]."</a></li>";
			}
		}
	}
	echo $tabmenuCadena;
}else{
	?>
	<li>&nbsp;</li>
	<?php
}
?>
	</ul>
</div>