<?php
ob_start();
session_start();
$perfilActual=$_SESSION['user_perfil_id'];
$perfiles=$_SESSION['perfiles'];

if(!$perfilActual || $perfilActual==""){
	$perfilActual = $perfiles[0][0];
}

$indicePerfil = 0;
while($indicePerfil<sizeof($perfiles)){
	if(trim($perfiles[$indicePerfil][0])==trim($perfilActual)){
		$nombrePerfil = substr($perfiles[$indicePerfil][1],0,65);
	}
	$indicePerfil++;
}

$indicePerfil = 0;
?>

<div class="perfiles" id="perfiles">

	<div id="menu_parent"><?= $nombrePerfil?><!-- <img src="js/menu/down.gif" align="right" style="margin-top: 3px;margin-right: 1px;"/> --></div>
	
	<div id="menu_child">
	
		<?php
		$indicePerfil = 0;
		while($indicePerfil<sizeof($perfiles)){
			echo "<a href='javascript: parent.cambiarPerfil(\"".$perfiles[$indicePerfil][0]."\")'> ".substr($perfiles[$indicePerfil][1],0,65)."</a>";
			$indicePerfil++;
		}
		?>
	</div>
	<div id="menu_parent_label">Perfil: &nbsp;</div>	
</div>

<script type="text/javascript">
at_attach("menu_parent", "menu_child", "click", "y", "pointer");
</script>