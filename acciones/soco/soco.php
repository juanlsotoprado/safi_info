<?php
if (isset($_REQUEST['PHPSESSID'])) { 

              $_COOKIE['PHPSESSID'] = $_REQUEST['PHPSESSID'];
}
                  
include(dirname(__FILE__).'/../../init.php');

// Acciones
require_once(SAFI_ACCIONES_PATH. '/acciones.php');

// Includes
require_once(SAFI_INCLUDE_PATH. '/perfiles/constantesPerfiles.php');

require_once(SAFI_INCLUDE_PATH. '/conexion.php');

//Modelos

require_once(SAFI_INCLUDE_PATH.'/tabmenu/tabmenuItems.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/revisionesDoc.php');
require_once(SAFI_MODELO_PATH. '/observacionesDoc.php');

if  (empty($_SESSION['login']) || ($_SESSION['registrado']!="registrado") )
{
	header('Location:../index.php',false);
	ob_end_flush();
	exit;
}

class Soco extends Acciones{


	public function  GuardarImg(){
		if (!empty($_FILES)) {

			if(!isset($_SESSION['SafiRequestVars']['nameFile'])){ $_SESSION['SafiRequestVars']['nameFile'] = array();}
			

			$prefijo = substr(md5(uniqid(rand())), 0, 6);

			$name = $_FILES['Filedata']['name'];

			$name2 =  $prefijo . "_" . $name;

			$_SESSION['SafiRequestVars']['nameFile'][] = $name2;


			$targetFolder = SAFI_TMP_PATH.'/';

			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $targetFolder;
			$targetFile = rtrim($targetPath,'/') . '/' .$name2 ;

			// Validate the file type
			$fileTypes = array('jpg','jpeg','gif','png','odt','txt','ods','xls','bmp','pdf','pdt','odp'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);

			if (in_array($fileParts['extension'],$fileTypes)) {
				copy($tempFile, $targetFile);

				//error_log($name .' bien');
			} else {

				error_log($name .' mal');
			}

		}
	}
	
	public function  Registrar(){
		if(isset($_SESSION['SafiRequestVars']['nameFile'])){
		
				
			$i = 0;
			foreach ( $_SESSION['SafiRequestVars']['nameFile'] as $index => $valor){
		
		
		
				$targetFolder = SAFI_UPLOADS_PATH.'/soco/'.$valor;
				$tempFile =  SAFI_TMP_PATH.'/'. $valor;
				copy($tempFile,$targetFolder);
			  
				$params['Digital'][] = $valor;
		
			}
		
		}
		
		//unset($_SESSION['SafiRequestVars']['nameFile']);
		require("../../documentos/soco/solicitudCotizacion_PDF.php");
	
	}	

}
new Soco();
