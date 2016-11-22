<?php
//echo "No hace nada";
//exit();
?>
<html>
	<head>
		<title>.:SAFI:. Verificar eliminados</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
<?php
/*
 * 
 * */

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');


new InfocentrosVerificarEliminados();

class InfocentrosVerificarEliminados
{
	private $db = null;
	
	public function __construct()
	{
		$GLOBALS['SafiInfo']['general'] = array();
		$GLOBALS['SafiErrors']['general'] = array();
		
		try
		{
			$this->ConectarSistemaInfocentros();
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener todos los infocentros desde el Sistema de Infocentro
			if(!($result = $this->db->Query("SELECT id FROM infocentro ORDER BY id")))
				throw new Exception("SUI: " . $this->db->GetErrorMsg());
			
			$infocentrosSUI = array();
			while ($row = $this->db->Fetch($result))
			{
				$infocentrosSUI[] = $row['id'];
			}
					
			// Obtener los infocentros desde SAFI
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_infocentro ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
				

			$infocentrosFueraSUI = array();
			$infocentrosSAFI = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$infocentrosSAFI[] = $row['id'];
				if(!in_array($row['id'], $infocentrosSUI)){ // El infocentro no existe en SUI
					$infocentrosFueraSUI[] = $row['id']; 
				}
			}
					
			echo "Cantidad SUI: " . count($infocentrosSUI) . "<br/>";
			echo "Cantidad SAFI: " . count($infocentrosSAFI) . "<br/>";
			echo "Cantidad fuera SUI: " . count($infocentrosFueraSUI);
			echo "<br/><br/>";
			echo implode(", ", $infocentrosFueraSUI);			
					
					
			$GLOBALS['SafiInfo']['general'][] = "Finalizado con &eacute;xito";
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		}catch(Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
				
			$GLOBALS['SafiErrors']['general'][] = $e->getMessage();
			error_log($e, 0);
		}
	}
	
	public function ConectarSistemaInfocentros()
	{
		if ($this->db !== null){
			return;
		}
		
		// Conexion al Sistema de Infecentros
		$configInfocentros = array();
		
		//Producción
		$configInfocentros["dbEncoding"] = 'UTF8';
		//$configInfocentros["dbEncoding"] = 'LATIN1';
		$configInfocentros["dbServer"] = "192.168.110.22";
		$configInfocentros["dbPort"] = "3306";
		$configInfocentros["dbUser"] = "sui_lectura";
		$configInfocentros["dbPass"] = "*sui8485.";
		$configInfocentros["dbDatabase"] = "sui";
		
		/*
		//Localhost
		$configInfocentros["dbEncoding"] = 'UTF8';
		//$configInfocentros["dbEncoding"] = 'LATIN1';
		$configInfocentros["dbServer"] = "localhost";
		$configInfocentros["dbPort"] = "3306";
		$configInfocentros["dbUser"] = "root";
		$configInfocentros["dbPass"] = "p741852*";
		$configInfocentros["dbDatabase"] = "sui";
		*/
		
		$db_type = 'MySQLDb';
		$db = new $db_type();
		$db->charset = $configInfocentros["dbEncoding"];
		
		$connection = $db->Connect(
			$configInfocentros["dbServer"].":".$configInfocentros["dbPort"],
			$configInfocentros["dbUser"],
			$configInfocentros["dbPass"], 
			$configInfocentros["dbDatabase"]
		);
		
		$this->db = &$db;
		
		if (!$connection) {
			list($error, $level) = $db->GetError();
			
			$error = str_replace($configInfocentros['dbServer'], "[database server]", $error);
			$error = str_replace($configInfocentros['dbPort'], "[database port]", $error);
			$error = str_replace($configInfocentros['dbUser'], "[database user]", $error);
			$error = str_replace($configInfocentros['dbPass'], "[database pass]", $error);
			$error = str_replace($configInfocentros['dbDatabase'], "[database]", $error);
		
			echo "<strong>Problemas de conexion con la base de datos de infocentros: </strong>".$error;
			exit;
		}
	}
}

	include(SAFI_VISTA_PATH . "/mensajes.php");
?>
	</body>
</html>