<?php
include_once('config.php');  // incluye el config del directorio de formularios

class FormManager
{
	public static function GetForm($formName)
	{
		$config = $GLOBALS['Safi']['__Forms']['__Config'];
		$file = $config[$formName]['File'];
		$className = $config[$formName]['ClassName'];
		$globalName = $config[$formName]['GlobalName'];
		$list = &$GLOBALS['Safi']['__Forms']['__List'];
		$form = null;
		
		if(!isset($list[$globalName]))
		{
			include_once(SAFI_FORMULARIOS_PATH . '/' . $file);
			$list[$globalName] = new $className();
			$form = $list[$globalName];
		} else {
			$form =  $list[$globalName];
		}
		return $form;
	}
}