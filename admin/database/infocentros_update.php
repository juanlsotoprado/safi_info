<?php
//echo "No hace nada";
//exit();
?>
<html>
	<head>
		<title>.:SAFI:. Ingresar Vi&aacute;tico Nacional</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../../css/plantilla.css" rel="stylesheet" type="text/css" />
		<link href="../../css/safi0.2.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
<?php
/*
 * Modificaciones temporales sobre las tablas del safi:
 * 	- safi_edos_venezuela
 * 		* estatus_actividad: colocarle 1 como valor por defecto
 * 	- safi_municipio
 * 		* estatus_actividad: colocarle 1 como valor por defecto
 * 	- safi_parroquia
 * 		* estatus_actividad: colocarle 1 como valor por defecto
 * 	- safi_infocentro
 * 		* estatus_actividad: colocarle 1 como valor por defecto
 * 		* direccion: permitir el valor NULL
 * */

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');


new ClassUpdateInfocentros();

class ClassUpdateInfocentros
{
	private $errors = array();
	private $db = null;
	
	public function __construct()
	{
		try{
			$this->ConectarSistemaInfocentros();
			
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$this->UpdateEstados();
			$this->UpdateMunicipios();
			$this->UpdateParroquias();
			$this->UpdateEstatusGeneral();
			$this->UpdateInfocentros();
			
			$GLOBALS['SafiClassDb']->CommitTransaction();
			
		}catch (Exception $e)
		{
			if(isset($resultTransaction) && $resultTransaction)
				$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			error_log($e, 0);
			$this->SetError($e->getMessage());
		}
		
		if ($this->HasErrors()){
			echo "<strong>Errores:</strong><br/>";
			foreach($this->GetErrors() as $errors) {
				echo $errors . "<br/>";
			}
		} else {
			echo "Finalizado con &eacute;xito.";
			
			$fp = fopen(SAFI_LOG_PATH . "/log.txt", "a+");
			fwrite($fp, "Infocentros actualizados satisfactoriamente. " . date("d/m/Y H:i:s") . "\n");
			fclose($fp); 
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
	
	public function PrepareInteger($integer)
	{
		if($integer === null || trim($integer) == '' ){
			return 'NULL';
		} else {
			return utf8_decode($integer);
		}
	}
	
	public function PrepareText($text)
	{
		if($text === null || trim($text) == ''){
			return 'NULL';
		} else {
			return "'" . utf8_decode($text) . "'";
		}
	}
	
	public function UpdateEstados()
	{
		try{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener los estados de Venezuela desde safi
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_edos_venezuela ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$toEstados = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$toEstados[] = $row['id'];
			}
			
			// Obtener todos los estado de venezuela desde infocentro
			if(!($result = $this->db->Query("SELECT id, nombre FROM estado ORDER BY id")))
				throw new Exception("Infocentro: " . $this->db->GetErrorMsg());
			
			// copiar los estados desde infocentro ($result) a safi
			while ($row = $this->db->Fetch($result)) {
				
				if(in_array($row['id'], $toEstados)){ // El estado existe en safi
					$query = "
						UPDATE safi_edos_venezuela SET
							nombre = " . $this->PrepareText($row['nombre']) . "
						WHERE
							id = " . $this->PrepareInteger($row['id']) . "
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} else {
					$query = "
						INSERT INTO safi_edos_venezuela (id, nombre) VALUES
						(" . $this->PrepareInteger($row['id']) . ", " . $this->PrepareText($row['nombre']) . ");
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		}catch (Exception $e){
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
	
	public function UpdateMunicipios()
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener los municipios desde safi
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_municipio ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$toMunicipios = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$toMunicipios[] = $row['id'];
			}
			
			// Obtener todos los municipios de venezuela desde infocentro
			if(!($result = $this->db->Query("SELECT id, id_estado, nombre FROM municipio ORDER BY id")))
				throw new Exception("Infocentro: " . $this->db->GetErrorMsg());
			
			// copiar los municipios desde infocentro ($result) a safi
			while ($row = $this->db->Fetch($result)) {
				
				if(in_array($row['id'], $toMunicipios)){ // El municipio existe en safi
					$query = "
						UPDATE safi_municipio SET
							nombre = " . $this->PrepareText($row['nombre']) . ",
							edo_id = " . $this->PrepareInteger($row['id_estado']) . "
						WHERE
							id = " . $this->PrepareInteger($row['id']) . "
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} else {
					$query = "
						INSERT INTO safi_municipio (id, edo_id, nombre) VALUES
						(
							" . $this->PrepareInteger($row['id']) . ", 
							" . $this->PrepareInteger($row['id_estado']) . ",
							" . $this->PrepareText($row['nombre']) . "
						);
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
	
	public function UpdateParroquias()
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener las parroquias desde safi
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_parroquia ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$toParroquias = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$toParroquias[] = $row['id'];
			}
			
			// Obtener todos las parroquias desde infocentro
			if(!($result = $this->db->Query("SELECT id, id_municipio, nombre FROM parroquia ORDER BY id")))
				throw new Exception("Infocentro: " . $this->db->GetErrorMsg());
				
			// copiar las parroquias desde infocentro ($result) a safi
			while ($row = $this->db->Fetch($result)) {
				
				if(in_array($row['id'], $toParroquias)){ // La parroquia existe en safi
					$query = "
						UPDATE safi_parroquia SET
							nombre = " . $this->PrepareText($row['nombre']) . ",
							municipio_id = " . $this->PrepareInteger($row['id_municipio']) . "
						WHERE
							id = " . $this->PrepareInteger($row['id']) . "
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} else {
					$query = "
						INSERT INTO safi_parroquia (id, municipio_id, nombre) VALUES
						(
							" . $this->PrepareInteger($row['id']) . ", 
							" . $this->PrepareInteger($row['id_municipio']) . ",
							" . $this->PrepareText($row['nombre']) . "
						);
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
				
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
	
	public function UpdateEstatusGeneral()
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener los Estatus Generales desde safi
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_estatus_general ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$toEstatusGeneral = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$toEstatusGeneral[] = $row['id'];
			}
			
			// Obtener todos las Estatus Generales desde el Sistema de Infocentro
			if(!($result = $this->db->Query("SELECT id, nombre FROM estatus_general ORDER BY id")))
				throw new Exception("Estatus general: " . $this->db->GetErrorMsg());
				
			// copiar los Estatus Generales desde el Sistema de Infocentros ($result) a safi
			while ($row = $this->db->Fetch($result)) {
				
				if(in_array($row['id'], $toEstatusGeneral)){ // El Estatus General existe en safi
					$query = "
						UPDATE safi_estatus_general SET
							nombre = " . $this->PrepareText($row['nombre']) . "
						WHERE
							id = " . $this->PrepareInteger($row['id']) . "
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} else {
					$query = "
						INSERT INTO safi_estatus_general (id, nombre) VALUES
						(
							" . $this->PrepareInteger($row['id']) . ", 
							" . $this->PrepareText($row['nombre']) . "
						);
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				}
			}
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}
	}
	
	public function UpdateInfocentros()
	{
		try {
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('SAFI: Error al iniciar la transacci&oacute. Detalles: ' .
					utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			// Obtener los infocentros desde safi
			if(!($result = $GLOBALS['SafiClassDb']->Query("SELECT id FROM safi_infocentro ORDER BY id")))
				throw new Exception('SAFI: ' .$GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$toInfocentros = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
				$toInfocentros[] = $row['id'];
			}
			
			// Obtener todos los infocentros desde el Sistema de Infocentro
			$query = "
				SELECT
					i.id,
					i.codigo,
					i.nombre,
					i.categoria,
					i.tipo_espacio,
					i.detalle_espacio,
					i.direccion,
					i.estado,
					i.municipio,
					i.parroquia,
					i.codigo_ine,
					i.estatus_general,
					i.estatus_especifico,
					i.sub_estatus_especifico,
					i.fecha_actualizacion,
					i.mantenimiento,
					i.etapa,
					i.ano_creacion,
					i.conectividad,
					i.accion,
					i.observacion,
					i.foto_fachada,
					i.foto_interna,
					i.latitud,
					i.longitud,
					i.agregado,
					e.nombre AS etapa_nombre
				FROM
					infocentro i
					LEFT JOIN etapa e ON (i.etapa = e.id)
				WHERE
					i.id <> 947
				ORDER BY
					i.id
			";
			
			// Obtener todos los infocentros desde infocentro
			if(!($result = $this->db->Query($query)))
				throw new Exception("Infocentro: " . $this->db->GetErrorMsg());
			
			
			/*
			
			echo '
				<table border="1">
					<tr>
						<td>Id</td>
						<td>Nombre</td>
						<td>Direccion</td>
						<td>A&ntilde;o</td>
						<td>Estado</td>
						<td>Parroquia</td>
						<td>Estatus</td>
						<td>Nemotecnico</td>
						<td>Estapa</td>
					</tr>
			';
			while ($row = $this->db->Fetch($result)) {
				
				switch ($row['estatus_general']){
					case "1": // Abierto
					case "2": // Cerrado
						$idEstatus = $row['estatus_general']; 
						break;
					case "3":
						$idEstatus = "202"; // En mantenimiento
						break;
					default:
						$idEstatus = "2"; // Cerrado
						break;
				}
				
				echo '
					<tr>
						<td>'.$this->PrepareInteger($row["id"]).'</td>
						<td>'.$this->PrepareText($row["nombre"]).'</td>
						<td>'.$this->PrepareText($row["direccion"]).'</td>
						<td>'.$this->PrepareInteger($row["ano_creacion"]).'</td>
						<td>'.$this->PrepareInteger($row["estado"]).'</td>
						<td>'.$this->PrepareInteger($row["parroquia"]).'</td>
						<td>'.$this->PrepareInteger($idEstatus).'</td>
						<td>'.$this->PrepareText($row["codigo"]).'</td>
						<td>'.$this->PrepareText($row["etapa_nombre"]).'</td>
					</tr>
				';
			}
			echo '
				</table>
			';
			
			return;
			*/
				
				
				
			
			// copiar los infocentros desde infocentro ($result) a safi
			while ($row = $this->db->Fetch($result)) {
				
				switch ($row['estatus_general']){
					case "1": // Abierto
					case "2": // Cerrado
						$idEstatus = $row['estatus_general']; 
						break;
					case "3":
						$idEstatus = "202"; // En mantenimiento
						break;
					default:
						$idEstatus = "2"; // Cerrado
						break;
				}
				
				
				if(in_array($row['id'], $toInfocentros)){ // El infocentro existe en safi
					$query = "
						UPDATE safi_infocentro SET
							nombre = " . $this->PrepareText($row['nombre']) . ",
							direccion = " . $this->PrepareText(trim($row['direccion'])) . ",
							anho = " . $this->PrepareInteger($row['ano_creacion']) . ",
							edo_id = " . $this->PrepareInteger($row['estado']) . ",
							parroquia_id = " . $this->PrepareInteger($row['parroquia']) . ",
							estatus_id = " . $this->PrepareInteger($idEstatus) . ",
							nemotecnico = " . $this->prepareText($row['codigo']) . ",
							etapa = " . $this->prepareText($row['etapa_nombre']) . ",
							id_estatus_general = " . $this->PrepareInteger($row['estatus_general']) . "
						WHERE
							id = " . $this->PrepareInteger($row['id']) . "
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg());
				} else {
					$query = "
						INSERT INTO safi_infocentro
						(
							id,
							nombre,
							direccion,
							anho,
							edo_id,
							parroquia_id,
							estatus_id,
							nemotecnico,
							etapa,
							id_estatus_general
						) 
						VALUES
						(
							" . $this->PrepareInteger($row['id']) . ", 
							" . $this->PrepareText($row['nombre']) . ",
							" . $this->PrepareText($row['direccion']) . ",
							" . $this->PrepareInteger($row['ano_creacion']) . ",
							" . $this->PrepareInteger($row['estado']) . ",
							" . $this->PrepareInteger($row['parroquia']) . ",
							" . $this->PrepareInteger($idEstatus) . ",
							" . $this->prepareText($row['codigo']) . ",
							" . $this->prepareText($row['etapa_nombre']) . ",
							" . $this->PrepareInteger($row['estatus_general']) . "
						);
					";
					
					if(!$GLOBALS['SafiClassDb']->Query($query))
						throw new Exception("SAFI: " . $GLOBALS['SafiClassDb']->GetErrorMsg() . $query);
				}
			}
			
			$result = $GLOBALS['SafiClassDb']->CommitTransaction();
			
		} catch (Exception $e) {
			if(isset($resultTransaction) && $resultTransaction === true)
				$result = $GLOBALS['SafiClassDb']->RollbackTransaction();
			throw $e;
		}	
	}
}

?>
	</body>
</html>