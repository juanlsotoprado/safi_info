<?php
/* Este script finaliza automáticamente los viatico nacionales referenciados por un archivo csv.
 * Estos viáticos nacionales se encuantran en la bandeja del director de la oficina de gestión
 * administrativa y financiera
 */ 

echo "No hace nada";
exit;

require_once(dirname(__FILE__) . "/../../init.php");
require_once(SAFI_MODELO_PATH. '/viaticonacional.php');
require_once(SAFI_MODELO_PATH. '/wfcadena.php');
require_once(SAFI_MODELO_PATH. '/docgenera.php');
require_once(SAFI_MODELO_PATH . '/compromiso.php' );

header("Content-type: text/html; charset=UTF-8");

new AprobacionMasivaViaticos();

class AprobacionMasivaViaticos
{
	private $errors = array();
	
	private $db = null;
	
	public function __construct()
	{
		//$this->AprobarViaticosEnBandejaAdministracionYFinanzas();
		//$this->FinalizarRendicionesViaticosMayor20140917();
		$this->AprobarViaticosEnBandejaPresupuesto();
		
		if ($this->HasErrors()){
			echo "<pre>";
			echo "<strong>Errores:</strong><br/>";
			foreach($this->GetErrors() as $errors) {
				echo $errors . "<br/>";
			}
			echo "</pre>";
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
	
	public function FinalizarRendicionesViaticosMayor20140917()
	{
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
				
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$huboErrores = false;
			
			$query = "
				SELECT
					documento.docg_id AS id_rendicion
				FROM
					sai_doc_genera documento
				WHERE
					documento.docg_id LIKE 'rvna-%'
					AND documento.docg_fecha >= to_date('17/09/2014', 'DD/MM/YYYY')
					AND perf_id_act IS NOT NULL
					AND
					(
						perf_id_act = '62400' -- Jefe de Presupuesto
						-- Director de administración, rendiciones de una gerencia distanta de adminitración
						OR (documento.docg_id NOT LIKE 'rvna-450%' AND documento.perf_id_act = '46450')
					)
				ORDER BY
					documento.docg_fecha
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception('Error al consultar las rendiciones de vi&aacute;ticos. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
			
			$idsRendiciones = array();
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$idsRendiciones[] = $row['id_rendicion'];
				$this->FinalizarRendicionViaticoBy($row['id_rendicion']);
			}
			
			echo "Rendiciones de vi&aacute;ticos finalizadas: <br><br>";
			echo '<table border="1">
					<tr>
						<td>Nº</td>
						<td>Id de rendici&oacute;n</td>
					</tr>
			';
			$count = 0;
			foreach ($idsRendiciones AS $idsRendicion)
			{
				echo '
					<tr>
						<td>'.(++$count).'</td>
						<td>'.$idsRendicion.'</td>		
					</tr>
				';
			}
			echo '</table>
				<br>		
			';
			
			$GLOBALS['SafiClassDb']->CommitTransaction();
			
		} catch(Exception $e){
			$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			$this->SetError($e->getMessage());
			error_log($e);
			return false;
		}
	}
	
	public function FinalizarRendicionViaticoBy($idRendicion)
	{
		if($idRendicion === null)
			throw new Exception("El parametro \"idRendicion\" es nulo.");
		
		if($idRendicion == '')
			throw new Exception("El parametro \"idRendicion\" est&aacute; vac&iacute;o.");
		
		$idPerfil = "46450";
		$loginUsuario = "11879929";
		$estadoAprobado = 13;
		$opcionDarleVistoBueno = 6;
		
		// Obtener una instancia de docgenera para la rendición del viático nacional a enviar (actualizar)
		$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idRendicion);
		if($docGenera === null) throw new Exception("Error al obtener el documento asociado a la rendici&oacute;n.");
			
		$docGenera->SetIdWFObjeto(99);
		$docGenera->SetIdWFCadena(0);
		$docGenera->SetIdEstatus($estadoAprobado);
		$docGenera->SetIdPerfilActual(null);
			
		$revisiones = new EntidadRevisionesDoc();
		
		$revisiones->SetIdDocumento($idRendicion);
		$revisiones->SetLoginUsuario($loginUsuario);
		$revisiones->SetIdPerfil($idPerfil);
		$revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
		$revisiones->SetIdWFOpcion($opcionDarleVistoBueno);
			
		// Guardar el registro del documento en docGenera (estado de la cadena)
		if(($enviado = SafiModeloDocGenera::EnviarDocumento($docGenera, $revisiones)) === false){
			throw new Exception("Error al enviar. No se pudo actualizar docGenera o revisionesDoc.");
		}
		
	}
	
	public function AprobarViaticosEnBandejaAdministracionYFinanzas()
	{
		$pathCSV = "/home/sai/solicitud_de_viaticos_a_eliminar_2011.csv";
		$IdViaticoColumn = 0;
		$headerRow = true;
		$idViaticos = array(); // array que almacena los id de los viativos a aprobar
		
		if (($handle = fopen($pathCSV, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
				if($headerRow){
					$headerRow = false;
					continue;
				}
				$idViaticos[$data[$IdViaticoColumn]] = $data[$IdViaticoColumn];
			}
			fclose($handle);
		}
		
		if(count($idViaticos)<=0){
			$this->SetError('No se pudo obtener ning&uacute;n id de vi&aacute;tico del el archivo: ' . $pathCSV);
			return false;
		}

		
		/******************************************************
		 ****** Finalizar como aprobados los viáticos *********
		 ******************************************************/
		
		try
		{
			$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
			
			if($resultTransaction === false)
				throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
				
			$huboErrores = false;
				
			foreach($idViaticos as $idViatico){
				
				try {
					$enviado = false;
					$idDependencia = "450";
					$idPerfil = "46450";
					$loginUsuario = "1192732";
					
					$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idViatico);
						
					if($wFCadena === null)
						throw new Exception('Error al enviar. WFCadena inicial no encontrada para el viático: ' . $idViatico);
						
					if(strcmp($wFCadena->GetId(), "0") == 0)
						throw new Exception('Error al enviar. viático: ' . $idViatico . ' finalizado');
					
					if($wFCadena->GetWFCadenaHijo() === null)
						throw new Exception('Error al enviar. WFCadena hija no encontrada para el viático: ' . $idViatico);
					
					$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViatico);
					
					// 0 = Documento finalizado
					if(strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") != 0)
						throw new Exception('El viático : ' . $idViatico . " no está en la bandeja de la oficina de administración y finanzas");
					
					if(
						strcmp($wFCadena->GetWFCadenaHijo()->GetId(), "0") == 0
					){
						// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
						$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViatico);
						
						$estadoAprobado = 13;
						
						$docGenera->SetIdWFObjeto(99);
						$docGenera->SetIdWFCadena(0);
						$docGenera->SetIdEstatus($estadoAprobado);
						$docGenera->SetIdPerfilActual(null);
						
						$Revisiones = new EntidadRevisionesDoc();
							
						$Revisiones->SetIdDocumento($idViatico);
						$Revisiones->SetLoginUsuario($loginUsuario);
						$Revisiones->SetIdPerfil($idPerfil);
						$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
						$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
						
						// No se genera la requisición ya que se debió haber creado manualmente 
						/*
						if($_SESSION['user_perfil_id'] == PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS){
							$requisicion = $this->LlenarRequisicion($docGenera);
						}*/
						$requisicion = null;
						
						// Guardar el registro del documento en docGenera (estado de la cadena)
						if(($enviado = SafiModeloViaticoNacional::EnviarViaticoNacional($docGenera, $Revisiones, $requisicion)) === false){
							throw new Exception('Error al enviar. No se pudo actualizar docGenera para el viatico: ' . $idViatico);
						}
						
					}
				}catch(Exception $eInterna){
					//$this->SetError($eInterna);  // Imprime el Stack trace
					$this->SetError($eInterna->getMessage());
					$huboErrores = true;
				}
			}// foreach($idViaticos as $idViatico){
			
			
			if($huboErrores === false)
				$GLOBALS['SafiClassDb']->CommitTransaction();
			else
				throw new Exception('Uno o más viáticos no se pueden finalizar.');
			
		}catch(Exception $e){
			$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			$this->SetError($e);
			return false;
		}
		
		
		/******************************************************
		 **** Fin de finalizar como aprobados los viáticos ****
		 ******************************************************/
		
		/******************************************************
		 ****** Obtener los viáticos con boleto aéreo *********
		 ******************************************************/
		
		
		
		
		/******************************************************
		 ****** Obtener los viáticos con boleto aéreo *********
		 ******************************************************/
		/*
		$idTipoTransporteAereo = 1; // Tipo aéreo indica que tiene un boleto aéreo
		
		$query = "
			SELECT DISTINCT
				viatico_id
			FROM
				safi_ruta_viatico
			WHERE
				viatico_id IN ('".implode("', '", $idViaticos)."') AND
				tipo_transporte = ".$idTipoTransporteAereo."
			ORDER BY
				viatico_id
				
		";
		
		if(($result=$GLOBALS['SafiClassDb']->Query($query)) === false){
			$this->SetError('Error al obtener los vi&aacute;ticos con boleto a&eacute;reo. Detalles: ' .
				$GLOBALS['SafiClassDb']->GetErrorMsg());
			return false;
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
		{
			echo $row['viatico_id'] . "<br/>";
		}
		*/
		/******************************************************
		 **** Fin de obtener los viáticos con boleto aéreo ****
		 ******************************************************/
	}
	
	// Aprobar los viáticos de la gerencia de redes que se encuentran en la bandeja de presupuesto. Luego
	// de esta operación quedarán en la bandeja de administración
	public function AprobarViaticosEnBandejaPresupuesto()
	{
		// array que almacena los id de los viativos a aprobar de la de la gerencia de3 redes
		$idViaticos = array("vnac-650115", "vnac-650215", "vnac-650315", "vnac-650415", "vnac-650515", "vnac-650615", "vnac-650715", "vnac-650815", "vnac-650915", "vnac-6501015", "vnac-6501115", "vnac-6501215", "vnac-6501315", "vnac-6501415", "vnac-6501515", "vnac-6501615", "vnac-6501715", "vnac-6501815", "vnac-6501915", "vnac-6502015", "vnac-6502115", "vnac-6502215", "vnac-6502315", "vnac-6502415", "vnac-6502515", "vnac-6502615", "vnac-6502715", "vnac-6502815", "vnac-6502915", "vnac-6503015", "vnac-6503115", "vnac-6503215", "vnac-6503315", "vnac-6503415", "vnac-6503515", "vnac-6503615", "vnac-6503715", "vnac-6503815", "vnac-6503915", "vnac-6504015", "vnac-6504115", "vnac-6504215", "vnac-6504315", "vnac-6504415", "vnac-6504515", "vnac-6504615", "vnac-6504715", "vnac-6504815", "vnac-6504915", "vnac-6505015", "vnac-6505115", "vnac-6505215", "vnac-6505315", "vnac-6505415", "vnac-6505515", "vnac-6505615", "vnac-6505715", "vnac-6505815", "vnac-6505915", "vnac-6506015", "vnac-6506115", "vnac-6506215", "vnac-6506315", "vnac-6506415", "vnac-6506515", "vnac-6506615", "vnac-6506715", "vnac-6506815", "vnac-6506915", "vnac-6507015", "vnac-6507115", "vnac-6507215", "vnac-6507315", "vnac-6507415", "vnac-6507515", "vnac-6507615", "vnac-6507715", "vnac-6507815", "vnac-6507915", "vnac-6508015", "vnac-6508115", "vnac-6508215", "vnac-6508315", "vnac-6508415", "vnac-6508515", "vnac-6508615", "vnac-6508715", "vnac-6508815", "vnac-6508915", "vnac-6509015", "vnac-6509115", "vnac-6509215", "vnac-6509315", "vnac-6509415", "vnac-6509515", "vnac-6509615", "vnac-6509715", "vnac-6509815", "vnac-6509915", "vnac-65010015", "vnac-65010115", "vnac-65010215", "vnac-65010315", "vnac-65010415", "vnac-65010515", "vnac-65010615", "vnac-65010715", "vnac-65010815", "vnac-65010915", "vnac-65011015", "vnac-65011115", "vnac-65011215", "vnac-65011315", "vnac-65011415", "vnac-65011515", "vnac-65011615", "vnac-65011715", "vnac-65011815", "vnac-65011915", "vnac-65012015", "vnac-65012115", "vnac-65012215", "vnac-65012315", "vnac-65012415", "vnac-65012515", "vnac-65012615", "vnac-65012715", "vnac-65012815", "vnac-65012915", "vnac-65013015", "vnac-65013115", "vnac-65013215", "vnac-65013315", "vnac-65013415", "vnac-65013515", "vnac-65013615", "vnac-65013715", "vnac-65013815", "vnac-65013915", "vnac-65014015", "vnac-65014115", "vnac-65014215", "vnac-65014315", "vnac-65014415", "vnac-65014515", "vnac-65014615", "vnac-65014715", "vnac-65014815", "vnac-65014915", "vnac-65015015", "vnac-65015115", "vnac-65015215", "vnac-65015315", "vnac-65015415", "vnac-65015515", "vnac-65015615", "vnac-65015715", "vnac-65015815", "vnac-65015915", "vnac-65016015", "vnac-65016115", "vnac-65016215", "vnac-65016315", "vnac-65016415", "vnac-65016515", "vnac-65016615", "vnac-65016715", "vnac-65016815", "vnac-65016915", "vnac-65017015", "vnac-65017115", "vnac-65017215", "vnac-65017315", "vnac-65017415", "vnac-65017515", "vnac-65017615", "vnac-65017715", "vnac-65017815", "vnac-65017915", "vnac-65018015", "vnac-65018115", "vnac-65018215", "vnac-65018315", "vnac-65018415", "vnac-65018515", "vnac-65018615", "vnac-65018715", "vnac-65018815", "vnac-65018915", "vnac-65019015", "vnac-65019115", "vnac-65019215", "vnac-65019315", "vnac-65019415", "vnac-65019515", "vnac-65019615", "vnac-65019715", "vnac-65019815", "vnac-65019915", "vnac-65020015", "vnac-65020115", "vnac-65020215", "vnac-65020315", "vnac-65020415", "vnac-65020515", "vnac-65020615", "vnac-65020715", "vnac-65020815", "vnac-65020915", "vnac-65021015", "vnac-65021115", "vnac-65021215", "vnac-65021315", "vnac-65021415", "vnac-65021515", "vnac-65021615", "vnac-65021715", "vnac-65021815", "vnac-65021915", "vnac-65022015", "vnac-65022115", "vnac-65022215", "vnac-65022315", "vnac-65022415", "vnac-65022515", "vnac-65022615", "vnac-65022715", "vnac-65022815", "vnac-65023015", "vnac-65023115", "vnac-65023215", "vnac-65023315", "vnac-65023415", "vnac-65023515", "vnac-65023615", "vnac-65023715", "vnac-65023815", "vnac-65023915", "vnac-65024015", "vnac-65024115", "vnac-65024215", "vnac-65024315", "vnac-65024415", "vnac-65024515", "vnac-65024615", "vnac-65024715", "vnac-65024815", "vnac-65024915", "vnac-65025015", "vnac-65025115", "vnac-65025215", "vnac-65025315", "vnac-65025415", "vnac-65025515", "vnac-65025615", "vnac-65025715", "vnac-65025815", "vnac-65025915", "vnac-65026015", "vnac-65026115", "vnac-65026215", "vnac-65026315", "vnac-65026415", "vnac-65026515", "vnac-65026615", "vnac-65026715", "vnac-65026815", "vnac-65026915", "vnac-65027015", "vnac-65027115", "vnac-65027215", "vnac-65027315", "vnac-65027415", "vnac-65027515", "vnac-65027615", "vnac-65027715", "vnac-65027815", "vnac-65027915", "vnac-65028015", "vnac-65028115", "vnac-65028215", "vnac-65028315", "vnac-65028415", "vnac-65028515", "vnac-65028615", "vnac-65028715", "vnac-65028815", "vnac-65028915", "vnac-65029015", "vnac-65029115", "vnac-65029215", "vnac-65029315", "vnac-65029415", "vnac-65029515", "vnac-65029615", "vnac-65029715", "vnac-65029815", "vnac-65029915", "vnac-65030015", "vnac-65030115", "vnac-65030215", "vnac-65030315", "vnac-65030415", "vnac-65030515", "vnac-65030615", "vnac-65030715", "vnac-65030815", "vnac-65030915", "vnac-65031015", "vnac-65031115", "vnac-65031215", "vnac-65031315", "vnac-65031415", "vnac-65031515", "vnac-65031615", "vnac-65031715", "vnac-65031815", "vnac-65031915", "vnac-65032015", "vnac-65032115", "vnac-65032215", "vnac-65032315", "vnac-65032415", "vnac-65032515", "vnac-65032615", "vnac-65032715", "vnac-65032815", "vnac-65032915", "vnac-65033015", "vnac-65033115", "vnac-65033215", "vnac-65033415", "vnac-65033515", "vnac-65033615", "vnac-65033715", "vnac-65033815", "vnac-65033915", "vnac-65034015", "vnac-65034115", "vnac-65034215", "vnac-65034315", "vnac-65034415", "vnac-65034515", "vnac-65034615", "vnac-65034715", "vnac-65034815", "vnac-65034915", "vnac-65035015", "vnac-65035115", "vnac-65035215", "vnac-65035315", "vnac-65035415", "vnac-65035515", "vnac-65035615", "vnac-65035715", "vnac-65035815", "vnac-65035915", "vnac-65036015", "vnac-65036115", "vnac-65036215", "vnac-65036315", "vnac-65036415", "vnac-65036515", "vnac-65036615", "vnac-65036715", "vnac-65036815", "vnac-65036915", "vnac-65037015", "vnac-65037115", "vnac-65037215", "vnac-65037315", "vnac-65037415", "vnac-65037515", "vnac-65037615", "vnac-65037715", "vnac-65037815", "vnac-65037915", "vnac-65038015", "vnac-65038115", "vnac-65038215", "vnac-65038315", "vnac-65038415", "vnac-65038515", "vnac-65038615", "vnac-65038715", "vnac-65038815", "vnac-65038915", "vnac-65039015", "vnac-65039115", "vnac-65039215", "vnac-65039315", "vnac-65039415", "vnac-65039515", "vnac-65039615", "vnac-65039715", "vnac-65039815", "vnac-65039915", "vnac-65040115", "vnac-65040215", "vnac-65040315", "vnac-65040415", "vnac-65040515", "vnac-65040615", "vnac-65040715", "vnac-65040815", "vnac-65040915", "vnac-65041015", "vnac-65041115", "vnac-65041215", "vnac-65041315", "vnac-65041415", "vnac-65041515", "vnac-65041615", "vnac-65041715", "vnac-65041815", "vnac-65041915", "vnac-65042015", "vnac-65042115", "vnac-65042215", "vnac-65042315", "vnac-65042415", "vnac-65042515", "vnac-65042615", "vnac-65042715", "vnac-65043215", "vnac-65043315", "vnac-65043415", "vnac-65043615", "vnac-65044015", "vnac-65044315", "vnac-65044515", "vnac-65044615", "vnac-65044715", "vnac-65044815", "vnac-65044915", "vnac-65045015", "vnac-65045115", "vnac-65045215", "vnac-65045315", "vnac-65045415", "vnac-65045515", "vnac-65045615", "vnac-65045715", "vnac-65045815", "vnac-65045915", "vnac-65046015", "vnac-65046115", "vnac-65046215", "vnac-65046315", "vnac-65046415", "vnac-65046515", "vnac-65046615", "vnac-65046715", "vnac-65046815", "vnac-65046915", "vnac-65047015", "vnac-65047115", "vnac-65047215", "vnac-65047315", "vnac-65047415", "vnac-65047815", "vnac-65047915", "vnac-65048015", "vnac-65048115", "vnac-65048215", "vnac-65048315", "vnac-65048415", "vnac-65048515", "vnac-65048615", "vnac-65048715", "vnac-65048815", "vnac-65048915", "vnac-65049015", "vnac-65049115", "vnac-65049215", "vnac-65049315", "vnac-65049415", "vnac-65049515", "vnac-65049615", "vnac-65049715", "vnac-65049815", "vnac-65049915", "vnac-65050015", "vnac-65050115", "vnac-65050215", "vnac-65050315", "vnac-65050415", "vnac-65050515", "vnac-65050615", "vnac-65050715", "vnac-65050815", "vnac-65050915", "vnac-65051015", "vnac-65051115", "vnac-65051215", "vnac-65051315", "vnac-65051415", "vnac-65051515", "vnac-65051615", "vnac-65051715", "vnac-65051815", "vnac-65051915", "vnac-65052015", "vnac-65052115", "vnac-65052215", "vnac-65052315", "vnac-65052415", "vnac-65052515", "vnac-65052615", "vnac-65052715", "vnac-65052815", "vnac-65052915", "vnac-65053015", "vnac-65053115", "vnac-65053215");
		
		// Aprobar viáticos desde la bandeja de presupuesto
		try
		{
			$huboErrores = false;
			$count = 0;
		
			foreach($idViaticos as $idViatico){
		
				try {
					
					$resultTransaction = $GLOBALS['SafiClassDb']->StartTransaction();
						
					if($resultTransaction === false)
						throw new Exception('Error al iniciar la transacci&oacute. Detalles: ' . $GLOBALS['SafiClassDb']->GetErrorMsg());
					
					if(substr($idViatico, 0, 8) != 'vnac-650')
						throw new Exception("El c&oacute;digo del vi&aacute;tico \"" . $idViatico . "\" es incorrecto o no pertencece"
							." a la gerencia de redes.");
						
					$enviado = false;
					$idPerfil = "62400";
					$loginUsuario = "11201729";
						
					$wFCadena = SafiModeloWFCadena::GetWFNextCadenaByIdDocument($idViatico);
					
					if($wFCadena === null)
						throw new Exception('WFCadena inicial no encontrada para el viático: ' . $idViatico);
					
					if(strcmp($wFCadena->GetId(), "0") == 0)
						throw new Exception('El viático: ' . $idViatico . ' est&aacute; finalizado');
						
					if($wFCadena->GetWFCadenaHijo() === null)
						throw new Exception('WFCadena hija no encontrada para el viático: ' . $idViatico);
						
					$viatico = SafiModeloViaticoNacional::GetViaticoNacionalById($idViatico);
					
					if($viatico === null)
						throw new Exception('El vi&aacute;tico: "' . $idViatico . '" no pudo ser cargado');
					
					// Obtener una instancia de docgenera para el viatico nacional a enviar (actualizar)
					$docGenera = SafiModeloDocGenera::GetDocGeneraByIdDocument($idViatico);
					
					if($docGenera === null)
						throw new Exception('El docGenera del vi&aacute;tico: "' . $idViatico . '" no pudo ser cargado');
					
					if($docGenera->GetIdPerfilActual() != PERFIL_JEFE_PRESUPUESTO)
						throw new Exception('El vi&aacute;tico: "' . $idViatico . '" no se encuentra en la bandeja del jede de presupuesto.');
					
					if(SafiModeloCompromiso::GetCompromisoByIdDocumento($idViatico) === null)
						throw new Exception('Para el vi&aacute;tico: "' . $idViatico . '" no se encontr&oacute; ning&uacute;n compromiso.');

					// Operaciones para aprobar el viático
						
					$docGenera->SetIdWFCadena(267);
					$docGenera->SetIdPerfilActual(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS);
						
					$Revisiones = new EntidadRevisionesDoc();
					
					$Revisiones->SetIdDocumento($idViatico);
					$Revisiones->SetLoginUsuario($loginUsuario);
					$Revisiones->SetIdPerfil($idPerfil);
					$Revisiones->SetFechaRevision(date("d/m/Y H:i:s"));
					$Revisiones->SetIdWFOpcion($wFCadena->GetWFOpcion()->GetId());
					
					$requisicion = null;
						
					// Guardar el registro del documento en docGenera (estado de la cadena)
					if(($enviado = SafiModeloViaticoNacional::EnviarViaticoNacional($docGenera, $Revisiones, $requisicion)) === false)
						throw new Exception('Error al enviar. No se pudo actualizar docGenera para el viatico: ' . $idViatico);
					
					$GLOBALS['SafiClassDb']->CommitTransaction();
				
				}catch(Exception $eInterna){
					$GLOBALS['SafiClassDb']->RollbackTransaction();
					$this->SetError((++$count) . ' - ' . $eInterna->getMessage());
					$huboErrores = true;
				}
			}// foreach($idViaticos as $idViatico){
						
			/*
			if($huboErrores === false)
				$GLOBALS['SafiClassDb']->CommitTransaction();
			else
				throw new Exception('Uno o más viáticos no se pueden aprobar.');
			*/
			
			if($huboErrores !== false)
				throw new Exception('Uno o más viáticos no se pueden aprobar.');
				
		}catch(Exception $e){
			//$GLOBALS['SafiClassDb']->RollbackAllTransactions();
			$this->SetError($e);
			return false;
		}
		
		echo "<pre>";
		//print_r($idViaticos);
		echo "</pre>";
	}
	
	
}