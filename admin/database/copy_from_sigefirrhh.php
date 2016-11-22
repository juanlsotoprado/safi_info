<?php
include(dirname(__FILE__) . "/../../init.php");

header("Content-type: text/html; charset=UTF-8");

new CopyFromSigefirrhh();

class CopyFromSigefirrhh
{

	private $errors = array();
	
	private $db = null;
	
	public function __construct()
	{
		echo 'No hace nada';
		exit;
		$this->CopyCiudades();

		if ($this->HasErrors()){
			echo "<strong>Errores:</strong><br/>";
			foreach($this->GetErrors() as $errors) {
				echo $errors . "<br/>";
			}
		} else {
			echo "Finalizado con éxito.";
		}
	}

	public function SetError($message)
	{
		$this->errors[] = $message;
	}

	public function GetErrors()
	{
		return $this->errors;
	}

	public function HasErrors()
	{
		return !empty($this->errors);
	}
	
	public function PrepareEstatusActividad($estatusActividad)
	{
		if($estatusActividad == '' || $estatusActividad === null){
		 return "'1'";
		} else {
			return "'" . $estatusActividad . "'";
		}
	}
	
	public function prepareInteger($integer)
	{
		if($integer === null || trim($integer) == '' ){
			return 'NULL';
		} else {
			return $integer;
		}
	}
	
	public function prepareText($text)
	{
		if($text === null || trim($text) == ''){
			return 'NULL';
		} else {
			return "'" . utf8_decode($text) . "'";
		}
	}
	
	public function prepareNombreCiudad($nombre){
		if($nombre === null || trim($nombre) == ''){
			return 'NULL';
		} else {
			return "'" . utf8_decode(mb_convert_case($nombre, MB_CASE_TITLE, "UTF-8")) . "'";
		}
	}

	public function ConectarSigefirrhh()
	{
		if ($this->db !== null){
			return;
		}
		
		// Conexion a sigetec
		$configSigefirrhh = array();
		
		$configSigefirrhh["dbEncoding"] = 'UTF8';
		//$configSigefirrhh["dbEncoding"] = 'LATIN1';
		$configSigefirrhh["dbServer"] = "localhost:5432";
		$configSigefirrhh["dbUser"] = "postgres";
		$configSigefirrhh["dbPass"] = "p748159";
		$configSigefirrhh["dbDatabase"] = "prueba2";
		
		$db_type = 'PGSQLDb';
		$db = new $db_type();
		
		$db->charset = $configSigefirrhh["dbEncoding"];
		//$db->timezone = '+0:00'; // Tell the database server to always do its time operations in GMT +0. We perform adjustments in the code for the timezone
		
		$connection = $db->Connect($configSigefirrhh["dbServer"], $configSigefirrhh["dbUser"], $configSigefirrhh["dbPass"], $configSigefirrhh["dbDatabase"]);
		
		$this->db = &$db;
		
		if (!$connection) {
			list($error, $level) = $db->GetError();
			
			$error = str_replace($configSigefirrhh['dbServer'], "[database server]", $error);
			$error = str_replace($configSigefirrhh['dbUser'], "[database user]", $error);
			$error = str_replace($configSigefirrhh['dbPass'], "[database pass]", $error);
			$error = str_replace($configSigefirrhh['dbDatabase'], "[database]", $error);
		
			echo "<strong>Problemas de conexion con la base de datos de sigefirhh: </strong>".$error;
			exit;
		}
	}
	
	public function CopyCiudades()
	{
		$this->ConectarSigefirrhh();
		
		$query = "
			SELECT
				e.id_safi as id_estado,
				e.nombre as nombre_estado,				
				c.nombre as nombre_ciudad
			FROM
				estado e
				INNER JOIN ciudad c ON c.id_estado = e.id_estado
			ORDER BY
				e.id_safi
		";
		
		if($result = $this->db->Query($query))
		{
			$count = 1;
			$ciudadesByEstado = array();
			
			$query = "
				SELECT setval('public.safi_ciudad__id__seq', 1, false)
			";
			
			if(!$GLOBALS['SafiClassDb']->Query($query)){
				$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			$query = "
				DELETE FROM safi_ciudad;
			";
			
			if(!$GLOBALS['SafiClassDb']->Query($query)){
				$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			}
			
			while ($row = $this->db->Fetch($result))
			{
				/*echo ($count++) . ' - ' . $row['id_estado'] . ' - ' . 
					$row['id'] . ' - ' . $row['nombre_estado'] . ' - ' . 
					mb_convert_case($row['nombre_ciudad'], MB_CASE_TITLE, "UTF-8") . '<br/>';*/
				
				$query = "
					INSERT INTO safi_ciudad (nombre, edo_id, estatus_actividad) VALUES
					(" . $this->prepareNombreCiudad( $row['nombre_ciudad']) . ", " . $row['id_estado'] . ", " . $this->PrepareEstatusActividad('1') . ");
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
				
					/*
					$ciudadesByEstado[$row['id_estado']]['nombre'] = $row['nombre_estado'];
					if(!isset($ciudadesByEstado[$row['id_estado']]['ciudades'])){
						$ciudadesByEstado[$row['id_estado']]['ciudades'] = array();
					}
					$ciudadesByEstado[$row['id_estado']]['ciudades'][]['nombre'] = $row['nombre_ciudad'];
					*/
			}
			/*
			echo "<pre>";
			print_r($ciudadesByEstado);
			echo "</pre>";
			*/
		}
	}
}