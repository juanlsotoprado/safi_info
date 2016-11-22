<?php
//echo "No hace nada";
//exit();

header("Content-Type: text/plain; charset=utf-8");

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');

new ClassHistoricoNomina2014();

class ClassHistoricoNomina2014
{
	private $_errors = array();
	private $_db = null;
	
	public function __construct()
	{
		try
		{
			$this->conectarSigefirrhh();
			
			for ($mes = 1; $mes <= 4; $mes++) {
				$fechaCorte = '28-'.($mes < 10 ? '0' . $mes : $mes).'-2014';
				$datos[$mes] = array(
					'textoMes' => $this->getMesTexto($mes),
				);
				if( ($resultado = $this->consultarDatosMes($mes, $fechaCorte)) === false)
					throw new Exception('Error al consultar los datos.');
				$datos[$mes]['trabajadores'] = $resultado;
			}
			
			if( $this->generarArchivoTexto($datos) === false)
				throw new Exception('Error al generar el archivo.');
			
			
			
		} catch (Exception $e) {
			echo "\n " . $e->getMessage() . "\n";
		}
	}
	
	public function conectarSigefirrhh()
	{
		if ($this->_db !== null){
			return;
		}
	
		// Conexion al Sistema de Infecentros
		$configSigefirrhh = array();
	
		//Producción
		/*
		$configSigefirrhh["dbEncoding"] = 'UTF8';
		$configSigefirrhh["dbServer"] = "150.188.85.30";
		$configSigefirrhh["dbPort"] = "5432";
		$configSigefirrhh["dbUser"] = "sigefirrhh";
		$configSigefirrhh["dbPass"] = "s!g3f!rrhh";
		$configSigefirrhh["dbDatabase"] = "sigefirrhh";
		*/
		
		// Desarrollo
		$configSigefirrhh["dbEncoding"] = 'UTF8';
		$configSigefirrhh["dbServer"] = "150.188.84.32";
		$configSigefirrhh["dbPort"] = "5432";
		$configSigefirrhh["dbUser"] = "sistemas";
		$configSigefirrhh["dbPass"] = "d3s4rr0ll0";
		$configSigefirrhh["dbDatabase"] = "sigefirrhh_180814";
	
		$db_type = 'PGSQLDb';
		$db = new $db_type();
		$db->charset = $configSigefirrhh["dbEncoding"];
	
		$connection = $db->Connect(
				$configSigefirrhh["dbServer"].":".$configSigefirrhh["dbPort"],
				$configSigefirrhh["dbUser"],
				$configSigefirrhh["dbPass"],
				$configSigefirrhh["dbDatabase"]
		);
	
		$this->db = &$db;
	
		if (!$connection) {
			list($error, $level) = $db->GetError();
				
			$error = str_replace($configSigefirrhh['dbServer'], "[database server]", $error);
			$error = str_replace($configSigefirrhh['dbPort'], "[database port]", $error);
			$error = str_replace($configSigefirrhh['dbUser'], "[database user]", $error);
			$error = str_replace($configSigefirrhh['dbPass'], "[database pass]", $error);
			$error = str_replace($configSigefirrhh['dbDatabase'], "[database]", $error);
	
			echo "\nProblemas de conexion con la base de datos de sigefirrhh: ".$error."\n";
			exit;
		}
	}
	
	public function consultarDatosMes($mes, $fechaCorte)
	{
		$preMsg = "Error al consultar los datos.";
		$datos = array();
		
		try 
		{
			
			$query = "
			SELECT
			    p.nacionalidad AS nacionalidad,
			    t.cedula AS cedula,
			    COALESCE(p.primer_apellido, '') AS primer_apellido,
			    COALESCE(p.segundo_apellido, '') AS segundo_apellido,
			    COALESCE(p.primer_nombre, '') AS primer_nombre,
			    COALESCE(p.segundo_nombre, '') AS segundo_nombre,
			    p.sexo AS sexo,
			    TO_CHAR(t.fecha_ingreso, 'DD-MM-YYYY') AS fecha_antiguedad, 
				TO_CHAR(t.fecha_ingreso, 'DD-MM-YYYY') AS fecha_antiguedad,					
			    TO_CHAR(t.fecha_ingreso, 'DD-MM-YYYY') AS fecha_ingreso,
			    t.codigo_nomina AS codigo_nomina,
			    c.descripcion_cargo AS descripcion_cargo,
			    t.id_tipo_personal AS tipo_personal,
			    t.cod_tipo_personal AS categoria_personal,
			    '".$fechaCorte."' AS fecha_corte,
			    'SUELDO BASICO' AS sueldo_basico,
			    COALESCE(SUM(hqsueldobasico.monto_asigna),0) AS monto_sueldo_basico,
			    'BONO VACACIONAL' AS bono_vacacional,
			    COALESCE(SUM(hqbonovacacional.monto_asigna),0) AS monto_bono_vacacional,
			    'BONO NACIMIENTO' AS bono_nacimiento,
			    COALESCE(SUM(hqbononacimiento.monto_asigna),0) AS monto_bono_nacimiento,
			    'BONO MATRIMONIO' AS bono_matrimonio,
			    COALESCE(SUM(hqbonomatrimonio.monto_asigna),0) AS monto_bono_matrimonio,
			    'HONORARIOS PROFESIONALES' AS honorarios_profesionales,
			    COALESCE(SUM(hqhonorariosprof.monto_asigna),0) AS monto_honorarios_profesionales,
			    'BONO UNICO' AS bono_unico,
			    COALESCE(SUM(hqbonounico.monto_asigna),0) AS monto_bono_unico,
			    'DIFERENCIA SUELDO BASICO' AS diferencia_sueldo_basico,
			    COALESCE(SUM(hqdifsueldobas.monto_asigna),0) AS monto_diferencia_sueldo_basico,
			   'AYUDA GASTOS OFTAMOLOGICOS' AS  ayuda_gastos_oftamologicos,
			    COALESCE(SUM(hqayudagastooft.monto_asigna),0) AS monto_ayuda_gastos_oftamologicos,
			    'DIFERENCIA DE BONO VACACIONAL' AS diferencia_de_bono_vacacional,
			    COALESCE(SUM(hqdifbonovac.monto_asigna),0) AS monto_diferencia_de_bono_vacacional,
			    'AYUDA UTILES ESCOLARES' AS ayuda_utiles_escolares,
			    COALESCE(SUM(hqayudautilesc.monto_asigna),0) AS monto_ayuda_utiles_escolares ,
			    'PAGO DE GASTOS DE MOVILIZACIÓN' AS pago_de_gastos_de_movilizacion,
			    COALESCE(SUM(hqgastosmovilizacion.monto_asigna),0) AS monto_pago_de_gastos_de_movilizacion,
			
			    'BENEFICIO DE ALIMENTACIÓN' AS beneficio_de_alimentacion,
			    COALESCE(SUM(hqbenefalimentacion.monto_asigna),0) AS monto_beneficio_de_alimentacion,
			
				'PENSION' AS concepto1,
				COALESCE(SUM(hqpension.monto_asigna),0) AS  concepto2,
			
				'DIFERENCIA POR ENCARGADURIA' AS concepto3,
				COALESCE(SUM(hqdifeporecargadu.monto_asigna),0) AS  concepto4,
			
				'COMPENSACION SALARIAL' AS concepto5,
				COALESCE(SUM(hqcompensalarial.monto_asigna),0) AS  concepto6,
			
				'REINTEGRO SSO' AS concepto7,
				COALESCE(SUM(hqreintesso.monto_asigna),0) AS  concepto8,
			
				'REINTEGRO RPE' AS concepto9,
				COALESCE(SUM(hqreinterpe.monto_asigna),0) AS  concepto10,
			
				'REINTEGRO FAOV' AS concepto11,
				COALESCE(SUM(hqreintefaov.monto_asigna),0) AS  concepto12,
			
				'DIFERENCIA BENEFICIO DE  ALIMENTACION' AS concepto13,
				COALESCE(SUM(hqdifebeneali.monto_asigna),0) AS  concepto14,
			
				'PAGO DE GASTO DE COMUNICACION' AS concepto15,
				COALESCE(SUM(hqpagogastcomun.monto_asigna),0) AS  concepto16,
			
				'PAGO DE MAYOR RESPONSABILIDAD' AS concepto17,
				COALESCE(SUM(hqpagomayores.monto_asigna),0) AS  concepto18,
			
				'BONO VACACIONAL FRACCIONADO' AS concepto19,
				COALESCE(SUM(hqbonovacafracc.monto_asigna),0) AS  concepto20,
			
				'BECA' AS concepto21,
				COALESCE(SUM(hqbeca.monto_asigna),0) AS  concepto22,
			
				'BECA_JUNIO' AS concepto23,
				COALESCE(SUM(hqbecajuni.monto_asigna),0) AS  concepto24,
			
				'INCENTIVO UNICO POR PROFESIONALIZACION' AS concepto25,
				COALESCE(SUM(hqincenuniprofesio.monto_asigna),0) AS  concepto26
			
			
			FROM
			    trabajador t
			INNER JOIN historicoquincena hqfiltro ON (t.id_trabajador=hqfiltro.id_trabajador)			    		
			--BONO VACACIONAL
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 12
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbonovacacional ON (t.id_trabajador = hqbonovacacional.id_trabajador AND hqbonovacacional.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonovacacional.semana_quincena = hqfiltro.semana_quincena)
			--SUELDO BASICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 2
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqsueldobasico ON (t.id_trabajador = hqsueldobasico.id_trabajador AND hqsueldobasico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqsueldobasico.semana_quincena = hqfiltro.semana_quincena)
			
			--BONO NACIMIENTO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 29
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbononacimiento ON (t.id_trabajador = hqbononacimiento.id_trabajador AND hqbononacimiento.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbononacimiento.semana_quincena = hqfiltro.semana_quincena)
			--BONO MATRIMONIO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 30
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbonomatrimonio ON (t.id_trabajador = hqbonomatrimonio.id_trabajador AND hqbonomatrimonio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonomatrimonio.semana_quincena = hqfiltro.semana_quincena)
			
			
			
			--HONORARIOS PROFESIONALES
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 132
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqhonorariosprof ON (t.id_trabajador = hqhonorariosprof.id_trabajador AND hqhonorariosprof.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqhonorariosprof.semana_quincena = hqfiltro.semana_quincena)
			
			--BONO UNICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 250
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbonounico ON (t.id_trabajador = hqbonounico.id_trabajador AND hqbonounico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonounico.semana_quincena = hqfiltro.semana_quincena)
			
			--DIFERENCIA SUELDO BASICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 350
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqdifsueldobas ON (t.id_trabajador = hqdifsueldobas.id_trabajador AND hqdifsueldobas.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifsueldobas.semana_quincena = hqfiltro.semana_quincena)
			
			--AYUDA GASTOS OFTAMOLOGICOS
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 380
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqayudagastooft ON (t.id_trabajador = hqayudagastooft.id_trabajador AND hqayudagastooft.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudagastooft.semana_quincena = hqfiltro.semana_quincena)
			
			--DIFERENCIA DE BONO VACACIONAL
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 505
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqdifbonovac ON (t.id_trabajador = hqdifbonovac.id_trabajador AND hqdifbonovac.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifbonovac.semana_quincena = hqfiltro.semana_quincena)
			
			
			--AYUDA UTILES ESCOLARES
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 812
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqayudautilesc ON (t.id_trabajador = hqayudautilesc.id_trabajador AND hqayudautilesc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudautilesc.semana_quincena = hqfiltro.semana_quincena)
			
			
			--PAGO DE GASTOS DE MOVILIZACIÓN
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1001
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqgastosmovilizacion ON (t.id_trabajador = hqgastosmovilizacion.id_trabajador AND hqgastosmovilizacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqgastosmovilizacion.semana_quincena = hqfiltro.semana_quincena)
			
			--BENEFICIO DE ALIMENTACIÓN
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1141
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbenefalimentacion ON (t.id_trabajador = hqbenefalimentacion.id_trabajador AND hqbenefalimentacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbenefalimentacion.semana_quincena = hqfiltro.semana_quincena)
			
			--PENSION
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 7
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqpension ON (t.id_trabajador = hqpension.id_trabajador AND hqpension.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpension.semana_quincena = hqfiltro.semana_quincena)
			
			--DIFERENCIA POR ENCARGADURIA
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 70
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqdifeporecargadu ON (t.id_trabajador = hqdifeporecargadu.id_trabajador AND hqdifeporecargadu.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifeporecargadu.semana_quincena = hqfiltro.semana_quincena)
			
			--COMPENSACION SALARIAL
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 74
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqcompensalarial ON (t.id_trabajador = hqcompensalarial.id_trabajador AND hqcompensalarial.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqcompensalarial.semana_quincena = hqfiltro.semana_quincena)
			
			--REINTEGRO SSO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 103
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqreintesso ON (t.id_trabajador = hqreintesso.id_trabajador AND hqreintesso.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqreintesso.semana_quincena = hqfiltro.semana_quincena)
			
			--REINTEGRO RPE
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 104
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqreinterpe ON (t.id_trabajador = hqreinterpe.id_trabajador AND hqreinterpe.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqreinterpe.semana_quincena = hqfiltro.semana_quincena)
			
			--REINTEGRO FAOV
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 105
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqreintefaov ON (t.id_trabajador = hqreintefaov.id_trabajador AND hqreintefaov.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqreintefaov.semana_quincena = hqfiltro.semana_quincena)
			
			--DIFERENCIA BENEFICIO DE  ALIMENTACION
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1162
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqdifebeneali ON (t.id_trabajador = hqdifebeneali.id_trabajador AND hqdifebeneali.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifebeneali.semana_quincena = hqfiltro.semana_quincena)
			
			--PAGO DE GASTO DE COMUNICACION
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1321
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqpagogastcomun ON (t.id_trabajador = hqpagogastcomun.id_trabajador AND hqpagogastcomun.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagogastcomun.semana_quincena = hqfiltro.semana_quincena)
			
			--PAGO DE MAYOR RESPONSABILIDAD
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1331
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqpagomayores ON (t.id_trabajador = hqpagomayores.id_trabajador AND hqpagomayores.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagomayores.semana_quincena = hqfiltro.semana_quincena)
			
			--BONO VACACIONAL FRACCIONADO 
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1352
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbonovacafracc ON (t.id_trabajador = hqbonovacafracc.id_trabajador AND hqbonovacafracc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonovacafracc.semana_quincena = hqfiltro.semana_quincena)
			
			--BECA
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1371
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbeca ON (t.id_trabajador = hqbeca.id_trabajador AND hqbeca.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca.semana_quincena = hqfiltro.semana_quincena)
			
			--BECA_JUNIO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1372
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqbecajuni ON (t.id_trabajador = hqbecajuni.id_trabajador AND hqbecajuni.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecajuni.semana_quincena = hqfiltro.semana_quincena)
			
			--INCENTIVO UNICO POR PROFESIONALIZACION
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1384
			                    )
			            AND anio=2014 AND mes=".$mes."
			        ) hqincenuniprofesio ON (t.id_trabajador = hqincenuniprofesio.id_trabajador AND hqincenuniprofesio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqincenuniprofesio.semana_quincena = hqfiltro.semana_quincena)
			
			
			
			--CONDICIONES FIJAS
			INNER JOIN personal p ON (t.cedula=p.cedula)
			INNER JOIN cargo c ON (CASE WHEN t.id_cargo_real IS NOT NULL THEN t.id_cargo_real=c.id_cargo ELSE t.id_cargo=c.id_cargo END) 
			WHERE
			    --t.cedula = 15023598
			    --AND
			     hqfiltro.anio=2014 AND hqfiltro.mes=".$mes."
			    --ctp.id_concepto in (2,12,29,30,108,132,250,350,380,505,581,661,675,812,872,902,991,1001,1141,1191,1221,1222,1223,1224,1226,1261,1301,1302)
			GROUP BY
			    p.nacionalidad,
			    t.cedula,
			    p.primer_apellido,
			    p.segundo_apellido,
			    p.primer_nombre,
			    p.segundo_nombre,
			    p.sexo,
			    t.fecha_antiguedad,
			    t.fecha_ingreso,
			    t.codigo_nomina,
			    c.descripcion_cargo,
			    t.id_tipo_personal,
			    t.cod_tipo_personal
					
			";
			
			if(!($result = $this->db->Query($query)))
				throw new Exception($preMsg. ' Detalles: ' . $this->db->GetErrorMsg());
				
			while ($row = $this->db->Fetch($result)) {
				
				foreach ($row AS $index => $value){
					$dato [$index] = $value;
				}
				$datos[] = $dato;
			}
			
			return $datos;
			
		} catch (Exception $e){
			echo "\n " . $e->getMessage() . "\n";
			return false;
		}
	}
	
	public function generarArchivoTexto(array $datos)
	{
		$preMsg = 'Error al generar el archivo.';
		try
		{
			if ($datos === null)
				throw new Exception($preMsg . ' El parámetro \'$datos\' es nulo.');
			
			
			//print_r($datos);
			
			foreach ($datos AS $dato)
			{
				echo "Mes: " . $dato['textoMes'] . "\n";
				
				foreach ($dato['trabajadores'] AS $trabajador)
				{
					echo
						trim($trabajador['nacionalidad']) . ';' .
						trim($trabajador['cedula']) . ';' .
						trim($trabajador['primer_apellido']) . ';' .
						(($segundoApellido = trim($trabajador['segundo_apellido'])) == "" ? "NULL" : $segundoApellido) . ';' .
						trim($trabajador['primer_nombre']) . ';' .
						(($segundoNombre = trim($trabajador['segundo_nombre'])) == "" ? "NULL" : $segundoNombre) . ';' .
						trim($trabajador['sexo']) . ';' .
						trim($trabajador['fecha_antiguedad']) . ';' . 
						trim($trabajador['fecha_ingreso']) . ';' .
						trim($trabajador['codigo_nomina']) . ';' .
						trim($trabajador['descripcion_cargo']) . ';' .
						trim($trabajador['tipo_personal']) . ';' .
						trim($trabajador['categoria_personal']) . ';' .
						trim($trabajador['fecha_corte']) . ';' .
						trim($trabajador['sueldo_basico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_sueldo_basico'])) . ';' .
						'PRIMA PROFESIONALIZACION;' .
						trim('0') . ';' .
						'PRIMA DE ANTIGUEDAD;' .
						trim('0') . ';' .
						trim($trabajador['bono_vacacional']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_vacacional'])) . ';' .
						trim($trabajador['bono_nacimiento']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_nacimiento'])) . ';' .
						trim($trabajador['bono_matrimonio']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_matrimonio'])) . ';' .
						trim($trabajador['honorarios_profesionales']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_honorarios_profesionales'])) . ';' .
						trim($trabajador['bono_unico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_unico'])) . ';' .
						trim($trabajador['diferencia_sueldo_basico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_diferencia_sueldo_basico'])) . ';' .
						trim($trabajador['ayuda_gastos_oftamologicos']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_gastos_oftamologicos'])) . ';' .
						trim($trabajador['diferencia_de_bono_vacacional']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_diferencia_de_bono_vacacional'])) . ';' .
						trim($trabajador['ayuda_utiles_escolares']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_utiles_escolares'])) . ';' .
						trim($trabajador['pago_de_gastos_de_movilizacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_de_gastos_de_movilizacion'])) . ';' .
						trim($trabajador['beneficio_de_alimentacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_beneficio_de_alimentacion'])) . ';' .
						trim($trabajador['concepto1']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto2'])) . ';' .
						trim($trabajador['concepto3']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto4'])) . ';' .
						trim($trabajador['concepto5']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto6'])) . ';' .
						trim($trabajador['concepto7']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto8'])) . ';' .
						trim($trabajador['concepto9']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto10'])) . ';' .
						trim($trabajador['concepto11']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto12'])) . ';' .
						trim($trabajador['concepto13']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto14'])) . ';' .
						trim($trabajador['concepto15']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto16'])) . ';' .
						trim($trabajador['concepto17']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto18'])) . ';' .
						trim($trabajador['concepto19']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto20'])) . ';' .
						trim($trabajador['concepto21']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto22'])) . ';' .
						trim($trabajador['concepto23']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto24'])) . ';' .
						trim($trabajador['concepto25']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto26'])) .
						"\n";
				}
			}
			
		} catch (Exception $e){
			echo "\n " . $e->getMessage() . "\n";
			return false;
		}
	}
	
	public function getMesTexto($mes)
	{
		switch ($mes) {
			case 1: return 'Enero'; break;
			case 2: return 'Febrero'; break;
			case 3: return 'Marzo'; break;
			case 4: return 'Abril'; break;
			case 5: return 'Mayo'; break;
			case 6: return 'Junio'; break;
			case 7: return 'Julio'; break;
			case 8: return 'Agosto'; break;
			case 9: return 'Septiembre'; break;
			case 10: return 'Octubre'; break;
			case 11: return 'Noviembre'; break;
			case 12: return 'Diciembre'; break;
			default: return 'Sin mes'; break;
		}
	}

}
?>