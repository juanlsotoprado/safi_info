<?php
include(dirname(__FILE__) . "/../../init.php");

new UpdateFromSigetec();

class UpdateFromSigetec
{

	private $errors = array();
	
	private $db = null;
	
	public function __construct()
	{
		$this->UpdateStatus();
		$this->UpdateEstadosDeVenezuela();
		$this->UpdateMunicipios();
		$this->UpdateParroquias();
		$this->UpdateInfocentros();	
		
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
	
	public function ConectarSigetec()
	{
		if ($this->db !== null){
			return;
		}
		
		// Conexion a sigetec
		$configSigetec = array();
		
		$configSigetec["dbEncoding"] = 'UTF8';
		//$configSigetec["dbEncoding"] = 'LATIN1';
		$configSigetec["dbServer"] = "150.188.84.32:5433";
		$configSigetec["dbUser"] = "sigetec";
		$configSigetec["dbPass"] = "s1g3t3c";
		$configSigetec["dbDatabase"] = "sigetec_temp";
		
		$db_type = 'PGSQLDb';
		$db = new $db_type();
		
		$db->charset = $configSigetec["dbEncoding"];
		//$db->timezone = '+0:00'; // Tell the database server to always do its time operations in GMT +0. We perform adjustments in the code for the timezone
		
		$connection = $db->Connect($configSigetec["dbServer"], $configSigetec["dbUser"], $configSigetec["dbPass"], $configSigetec["dbDatabase"]);
		
		$this->db = &$db;
		
		if (!$connection) {
			list($error, $level) = $db->GetError();
			
			$error = str_replace($configSigetec['dbServer'], "[database server]", $error);
			$error = str_replace($configSigetec['dbUser'], "[database user]", $error);
			$error = str_replace($configSigetec['dbPass'], "[database pass]", $error);
			$error = str_replace($configSigetec['dbDatabase'], "[database]", $error);
		
			echo "<strong>Problemas de conexion con la base de datos de sigetec: </strong>".$error;
			exit;
		}
	}
	
	public function UpdateStatus()
	{
		$this->ConectarSigetec();
		
		// Obtener los status desde safi
		if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_estatus"))){
			$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		$currentStatus = array();
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$currentStatus[] = $row['id'];
		}
		
		// Obtener los status desde sigetec
		if(!($result = $this->db->Query("SELECT id, nombre, estatus_actividad FROM estatus"))){
			$this->SetError("Sigetec: " . $this->db->GetErrorMsg());
			return false;
		}
		
		// copiar los status desde sigetec ($result) a safi
		while ($row = $this->db->Fetch($result)) {
			
			if(in_array($row['id'], $currentStatus)){ // El estatus existe en safi
				$query = "
					UPDATE safi_estatus SET
						nombre = " . $this->prepareText($row['nombre']) . ",
						estatus_actividad = " . $this->PrepareEstatusActividad($row['estatus_actividad']) . "
					WHERE
						id = " . $row['id'] . "
				";
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
				
			} else { // El estatus no existe en safi
				$query = "
					INSERT INTO safi_estatus (id, nombre, estatus_actividad) VALUES
					(" . $row['id'] . ", " . $this->prepareText($row['nombre']) . ", " . $this->PrepareEstatusActividad($row['estatus_actividad']) . ");
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
		}
	}
	
	public function UpdateEstadosDeVenezuela()
	{
		
		$this->ConectarSigetec();
		
		// Obtener los estados de Venezuela desde safi
		if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_edos_venezuela"))){
			$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		$currentEdos = array();
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$currentEdos[] = $row['id'];
		}
		
		// Obtener todos los estado de venezuela desde sigetec
		if(!($result = $this->db->Query("SELECT id, nombre, estatus_actividad FROM estado"))){
			$this->SetError("Sigetec: " . $this->db->GetErrorMsg());
			return false;
		}
		
		// copiar los estados desde sigetec ($result) a safi
		while ($row = $this->db->Fetch($result)) {
			
			if(in_array($row['id'], $currentEdos)){ // El estado existe en safi
				$query = "
					UPDATE safi_edos_venezuela SET
						nombre = " . $this->prepareText($row['nombre']) . ",
						estatus_actividad = " . $this->PrepareEstatusActividad($row['estatus_actividad']) . "
					WHERE
						id = " . $row['id'] . "
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			} else {
				$query = "
					INSERT INTO safi_edos_venezuela (id, nombre, estatus_actividad) VALUES
					(" . $row['id'] . ", " . $this->prepareText($row['nombre']) . ", " . $this->PrepareEstatusActividad($row['estatus_actividad']) . ");
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
		}
	}
	
	public function UpdateMunicipios()
	{
		
		$this->ConectarSigetec();
		
		// Obtener los municipios desde safi
		if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_municipio"))){
			$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		$currentMunicipios = array();
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$currentMunicipios[] = $row['id'];
		}
		
		// Obtener todos los municipios de venezuela desde sigetec
		if(!($result = $this->db->Query("SELECT id, nombre, id_estado, estatus_actividad FROM municipio"))){
			$this->SetError("Sigetec: " . $this->db->GetErrorMsg());
			return false;
		}
		
		// copiar los municipios desde sigetec ($result) a safi
		while ($row = $this->db->Fetch($result)) {
			
			if(in_array($row['id'], $currentMunicipios)){ // El municipio existe en safi
				$query = "
					UPDATE safi_municipio SET
						nombre = " . $this->prepareText($row['nombre']) . ",
						edo_id = " . $row['id_estado'] . ",
						estatus_actividad = " . $this->PrepareEstatusActividad($row['estatus_actividad']) . "
					WHERE
						id = " . $row['id'] . "
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			} else {
				$query = "
					INSERT INTO safi_municipio (id, nombre, edo_id, estatus_actividad) VALUES
					(" . $row['id'] . ", " . $this->prepareText($row['nombre']) . ", " . $row['id_estado'] . ", " . $this->PrepareEstatusActividad($row['estatus_actividad']) . ");
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
		}
	}
	
	public function UpdateParroquias()
	{
		$this->ConectarSigetec();
		
		// Obtener las parroquias desde safi
		if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_parroquia"))){
			$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		$currentParroquias = array();
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$currentParroquias[] = $row['id'];
		}
		
		// Obtener todos las parroquias de venezuela desde sigetec
		if(!($result = $this->db->Query("SELECT id, nombre, id_municipio, estatus_actividad FROM parroquia"))){
			$this->SetError("Sigetec: " . $this->db->GetErrorMsg());
			return false;
		}
		
		// copiar las parroquias desde sigetec ($result) a safi
		while ($row = $this->db->Fetch($result)) {
			
			if(in_array($row['id'], $currentParroquias)){ // El municipio existe en safi
				$query = "
					UPDATE safi_parroquia SET
						nombre = " . $this->prepareText($row['nombre']) . ",
						municipio_id = " . $row['id_municipio'] . ",
						estatus_actividad = " . $this->PrepareEstatusActividad($row['estatus_actividad']) . "
					WHERE
						id = " . $row['id'] . "
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			} else {
				$query = "
					INSERT INTO safi_parroquia (id, nombre, municipio_id, estatus_actividad) VALUES
					(" . $row['id'] . ", " . $this->prepareText($row['nombre']) . ", " . $row['id_municipio'] . ", " . $this->PrepareEstatusActividad($row['estatus_actividad']) . ");
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
		}
	}
	
	public function UpdateInfocentros()
	{
		// Obtener los infocentros desde safi
		if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_infocentro"))){
			$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		$currentInfocentros = array();
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$currentInfocentros[] = $row['id'];
		}
		
		// Obtener todos los infocentros desde sigetec
		$query = "
			SELECT
				i.id,
				i.nombre,
				i.direccion,
				i.anho,
				i.id_parroquia,
				i.estado_actividad,
				i.id_estatus,
				i.nemotecnico,
				i.etapa,
				e.id AS id_estado
			FROM
				infocentro i
				INNER JOIN parroquia p ON (p.id = i.id_parroquia)
				INNER JOIN municipio m ON (m.id = p.id_municipio)
				INNER JOIN estado e ON (e.id = m.id_estado)
		";
		
		if(!($result = $this->db->Query($query))){
			$this->SetError("Sigetec (Fn: UpdateInfocentros): " . $this->db->GetErrorMsg());
			return false;
		}
		
		// copiar las infocentros desde sigetec ($result) a safi
		while ($row = $this->db->Fetch($result)) {
			
			if(in_array($row['id'], $currentInfocentros)){ // El infocentro existe en safi
				$query = "
					UPDATE safi_infocentro SET
						nombre = " . $this->prepareText($row['nombre']) . ",
						direccion = " . $this->prepareText($row['direccion']) . ",
						anho = " . $this->prepareInteger($row['anho']) . ",
						parroquia_id = " . $row['id_parroquia'] . ",
						estatus_actividad = " . $this->PrepareEstatusActividad($row['estado_actividad']) . ",
						estatus_id = " . $row['id_estatus'] . ",
						nemotecnico = " . $this->prepareText($row['nemotecnico']) . ",
						etapa = " . $this->prepareText($row['etapa']) . ",
						edo_id = " . $row['id_estado'] . "
					WHERE
						id = " . $row['id'] . "
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			} else { // El infocentro no existe en safi
				$query = "
				INSERT INTO safi_infocentro
					(	id,
						nombre,
						direccion,
						anho,
						parroquia_id,
						estatus_actividad,
						estatus_id,
						nemotecnico,
						etapa,
						edo_id
					)
				VALUES
					(	" . $row['id'] . ",
						" . $this->prepareText($row['nombre']) . ",
						" . $this->prepareText($row['direccion']) . ",
						" . $this->prepareInteger($row['anho']) . ",
						" . $row['id_parroquia'] . ",
						" . $this->PrepareEstatusActividad($row['estado_actividad']) . ",
						" . $row['id_estatus'] . ",
						" . $this->prepareText($row['nemotecnico']) . ",
						" . $this->prepareText($row['etapa']) . ",
						" . $row['id_estado'] . "
					)
				";
				
				if(!$GLOBALS['SafiClassDb']->Query($query)){
					$this->SetError("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
					exit;
				}
			}
		}
	}
}