<?php

	ob_start();

	// The absolute filesystem root to safi

	// We fetch the real path to index.php in the main directory and then dirname() it
	// due to a strange bug on some Windows based servers where simply resolving up a directory
	// returns false for realpath().
	define('SAFI_BASE_PATH', dirname(realpath(dirname(__FILE__).'/../index.php')));
	
	define('SAFI_CONFIG_FILE', SAFI_BASE_PATH.'/config/config.php');
	define('SAFI_ACCIONES_PATH', SAFI_BASE_PATH.'/acciones');
	define('SAFI_FORMULARIOS_PATH', SAFI_BASE_PATH.'/formularios');
	define('SAFI_MODELO_PATH', SAFI_BASE_PATH.'/lib/modelo');
	define('SAFI_LIB_PATH', SAFI_BASE_PATH.'/lib');
	define('SAFI_ENTIDADES_PATH', SAFI_BASE_PATH.'/lib/entidades');
	define('SAFI_VISTA_PATH', SAFI_BASE_PATH.'/vistas');
	define('SAFI_VISTA_CLASSES_PATH', SAFI_VISTA_PATH.'/classes');
	define('SAFI_INCLUDE_PATH', SAFI_BASE_PATH.'/includes');
	define('SAFI_TMP_PATH', SAFI_BASE_PATH.'/tmp');
	define('SAFI_UPLOADS_PATH', SAFI_BASE_PATH.'/uploads');
	define('SAFI_LOG_PATH', SAFI_BASE_PATH.'/var/log');
	define('SAFI_UPLOAD_RENDICION_VIATICO_NACIONAL_PATH', SAFI_UPLOADS_PATH.'/rendicionViaticoNacional');
	define('SAFI_CSS', SAFI_BASE_PATH.'/css');
	define('SAFI_JAVASCRIPT_PATH', SAFI_BASE_PATH.'/js');
	define('SAFI_IMAGES_PATH', SAFI_BASE_PATH.'/imagenes');
	
	require_once(SAFI_BASE_PATH.'/lib/general.php');
	
	require_once(SAFI_CONFIG_FILE);
	
	// The url root to safi
	define('SAFI_URL_BASE_PATH', GetConfig('siteURL'));
	define('SAFI_URL_ACCIONES_PATH', SAFI_URL_BASE_PATH.substr(SAFI_ACCIONES_PATH, strlen(SAFI_BASE_PATH)));
	define('SAFI_URL_IMAGES_PATH', SAFI_URL_BASE_PATH.substr(SAFI_IMAGES_PATH, strlen(SAFI_BASE_PATH)));
	define('SAFI_URL_JAVASCRIPT_PATH', SAFI_URL_BASE_PATH.substr(SAFI_JAVASCRIPT_PATH, strlen(SAFI_BASE_PATH)));
	
	require(SAFI_BASE_PATH . '/lib/database/pgsql.php');
	
	require(SAFI_INCLUDE_PATH . '/perfiles/constantesPerfiles.php');
	
	$db_type = 'PGSQLDb';
	$db = new $db_type();
	
	$db->TablePrefix = GetConfig("tablePrefix");
	$db->charset = GetConfig('dbEncoding');
	//$db->timezone = '+0:00'; // Tell the database server to always do its time operations in GMT +0. We perform adjustments in the code for the timezone

	$connection = $db->Connect(GetConfig("dbServer").":".GetConfig("dbPort"), GetConfig("dbUser"), GetConfig("dbPass"), GetConfig("dbDatabase"));

	// Create a reference to the database object
	$GLOBALS['SafiClassDb'] = &$db;
	
	if (!$connection) {
		list($error, $level) = $db->GetError();
		
		$error = str_replace(GetConfig('dbServer'), "[database server]", $error);
		$error = str_replace(GetConfig('dbUser'), "[database user]", $error);
		$error = str_replace(GetConfig('dbPass'), "[database pass]", $error);
		$error = str_replace(GetConfig('dbDatabase'), "[database]", $error);

		echo "<strong>Problemas de conexion con la base de datos: </strong>".$error;
		exit;
	}
	
	$GLOBALS['SafiRequestVars'] = array();
?>