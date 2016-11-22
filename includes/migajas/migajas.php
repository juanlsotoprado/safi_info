<?php
ob_start();
session_start();
$perfilActual=$_SESSION['user_perfil_id'];
if(isset($_REQUEST['tabmenu'])){
	@require("./../perfiles/constantesPerfiles.php");
	@require("./../constantes.php");
	@require_once("./../tabmenu/tabmenuItems.php");
	@require("./../migajas/migajasStructure.php");
}else{
	@require("includes/perfiles/constantesPerfiles.php");
	@require_once("includes/tabmenu/tabmenuItems.php");
	@require("includes/migajas/migajasStructure.php");
}
$tabmenu=$_REQUEST['tabmenu'];
if($tabmenu){
	$i = 1;
	$crumbCadena = "";
	foreach ($tabmenuCrumbsArray[$tabmenu] as $crumb) {
		if($i == sizeof($tabmenuCrumbsArray[$tabmenu])){
			$crumbCadena .= "<span class='migajasAtual'>".$crumb."</span>";
		}else{
			$crumbCadena .= $crumb." &gt; ";
		}
		$i++;
	}
	echo  $crumbCadena;
}else{
	echo "&nbsp";
}
?>
