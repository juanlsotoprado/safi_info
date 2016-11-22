<?php
//echo "No hace nada";
//exit();

header("Content-Type: text/plain; charset=utf-8");

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');

new ClassHistoricoNomina2012();

class ClassHistoricoNomina2012
{
	private $_errors = array();
	private $_db = null;
	
	public function __construct()
	{
		try
		{
			$this->conectarSigefirrhh();
			
			for ($mes = 10; $mes <= 12; $mes++) {
				$fechaCorte = '28-'.($mes < 10 ? '0' . $mes : $mes).'-2012';
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
			    'REINTEGRO HCM' AS reintegro_hcm,
			    COALESCE(SUM(hqreintegrohcm.monto_asigna),0) AS monto_reintegro_hcm,
			    'HONORARIOS PROFESIONALES' AS honorarios_profesionales,
			    COALESCE(SUM(hqhonorariosprof.monto_asigna),0) AS monto_honorarios_profesionales,
			    'BONO UNICO' AS bono_unico,
			    COALESCE(SUM(hqbonounico.monto_asigna),0) AS monto_bono_unico,
			    'DIFERENCIA SUELDO BASICO' AS diferencia_sueldo_basico,
			    COALESCE(SUM(hqdifsueldobas.monto_asigna),0) AS monto_diferencia_sueldo_basico,
			    'AYUDA GASTOS OFTAMOLOGICOS' AS  ayuda_gastos_oftamologicos,
			    COALESCE(SUM(hqayudagastooft.monto_asigna),0) AS monto_ayuda_gastos_oftamologicos,
			    'PAGO COMPENSATORIO' AS pago_compensatorio,
			    COALESCE(SUM(hqpagocompensatorio.monto_asigna),0) AS monto_pago_compensatorio,
			    'BONIFICACION FIN DE AÑO' AS bonificacion_fin_de_año,
			    COALESCE(SUM(hqbonfinano.monto_asigna),0) AS monto_bonificacion_fin_de_año,
			    'AYUDA UTILES ESCOLARES' AS ayuda_utiles_escolares,
			    COALESCE(SUM(hqayudautilesc.monto_asigna),0) AS monto_ayuda_utiles_escolares ,
			
			    'BONO DE RENDIMIENTO Y COMPROMISO' AS bono_de_rendimiento_y_compromiso,
			    COALESCE(SUM(hqbonorendcomp.monto_asigna),0) AS monto_bono_de_rendimiento_y_compromiso,
			    'AYUDA  JUGUETE' AS  ayuda_juguete,
			    COALESCE(SUM(hqayudajuguete.monto_asigna),0) AS monto_ayuda_juguete,
			
			    'PAGO DE GASTOS DE MOVILIZACIÓN' AS pago_de_gastos_de_movilizacion,
			    COALESCE(SUM(hqgastosmovilizacion.monto_asigna),0) AS monto_pago_de_gastos_de_movilizacion,
			    'BENEFICIO DE ALIMENTACIÓN' AS beneficio_de_alimentacion,
			    COALESCE(SUM(hqbenefalimentacion.monto_asigna),0) AS monto_beneficio_de_alimentacion,
			
			    'PAGO COMPENSATORIO2'  AS pago_compensatorio2,
			    COALESCE(SUM(hqpagocompensatorio2.monto_asigna),0) AS monto_pago_compensatorio2,
			    'MOVILIZACIÓN INSPECTORES' AS movilizacion_inspectores,
			    COALESCE(SUM(hqmovinspectores.monto_asigna),0) AS monto_movilizacion_inspectores,
			    '2/3 TERCIO_BONIFICACION DE FIN DE ANO' AS dos_tercio_bonificacion_de_fin_de_año,
			    COALESCE(SUM(hq2terciobonfinano.monto_asigna),0) AS monto_2_tercio_bonificación_de_fin_de_año,
			    '1/3 TERCIO_BONIFICACION DE FIN DE ANO' AS uno_tercio_bonificacion_de_fin_de_año,
			    COALESCE(SUM(hq1terciobonfinano.monto_asigna),0) AS monto_1_tercio_bonificación_de_fin_de_año,
			
			    'PENSION' AS  concepto1,
			    COALESCE(SUM(hqpension.monto_asigna),0) AS concepto2,
			
			        'DIFERENCIA POR ENCARGADURIA' AS  concepto3,
			    COALESCE(SUM(hqdif_por_enc.monto_asigna),0) AS concepto4,
			
			         'REINTEGRO CUOTA POLIZA H.C.M.' AS  concepto5,
			    COALESCE(SUM(hqrein_cuota_poliza_hcm.monto_asigna),0) AS concepto6,
			
			         'BONO ESPECIAL' AS  concepto7,
			    COALESCE(SUM(hqbono_especial.monto_asigna),0) AS concepto8,
			
			       'DIFERENCIA BENEFICIO DE  ALIMENTACION' AS  concepto9,
			 COALESCE(SUM(hqdiferencia_beneficio_de_alimentacion.monto_asigna),0) AS concepto10,
			
			       'PRESTACIONES SOCIALES ENE - ABR 2011' AS  concepto11,
			 COALESCE(SUM(hqprestaciones_sociales_ene_abr_2011.monto_asigna),0) AS concepto12,
			
			       'PRESTACIONES SOCIALES_ MAY - AGO _ 2011' AS  concepto13,
			 COALESCE(SUM(hqprestaciones_sociales_may_ago_2011.monto_asigna),0) AS concepto14,
			
			
			       'PRESTACIONES SOCIALES SEP - DIC 2011' AS  concepto15,
			 COALESCE(SUM(hqprestaciones_sociales_sep_dic_2011.monto_asigna),0) AS concepto16,
			
			       'DIAS ADICIONALES PRESTACIONES SOCIALES 2011' AS  concepto17,
			 COALESCE(SUM(hqdias_adicionales_prestaciones_sociales_2011.monto_asigna),0) AS concepto18,
			
			
			       'INTERESES PRESTACIONES SOCIALES 2011' AS  concepto19,
			 COALESCE(SUM(hqdias_adicionales_prestaciones_sociales_2011.monto_asigna),0) AS concepto20,
			
			       'PAGO DE GASTO DE COMUNICACION' AS  concepto21,
			    COALESCE(SUM(hqpago_de_gasto_de_comunicacion.monto_asigna),0) AS concepto22,
			
			        'PAGO DE MAYOR RESPONSABILIDAD' AS  concepto23,
			    COALESCE(SUM(hqpago_de_mayor_responsabilidad.monto_asigna),0) AS concepto24,
			
			      'BONO VACACIONAL AÑO 2012' AS  concepto25,
			    COALESCE(SUM(hqbono_vacacional_agno_2012.monto_asigna),0) AS concepto26,
			
			       'BONO VACACIONAL FRACCIONADO' AS  concepto27,
			    COALESCE(SUM(hqbono_vacacional_fraccionado.monto_asigna),0) AS concepto28,
			
			       'ESTIPENDIO' AS  concepto29,
			    COALESCE(SUM(hqestipendio.monto_asigna),0) AS concepto30,
			
			       'BECA' AS  concepto31,
			    COALESCE(SUM(hqbeca.monto_asigna),0) AS concepto32,
			
			        'BECA_JUNIO' AS  concepto33,
			    COALESCE(SUM(hqbeca_junio.monto_asigna),0) AS concepto34,
			
			       'BECA_JULIO' AS  concepto35,
			    COALESCE(SUM(hqbeca_julio.monto_asigna),0) AS concepto36,
			
			     'INCENTIVO UNICO POR PROFESIONALIZACION' AS  concepto37,
			 COALESCE(SUM(hqincentivo_unico_por_profesionalizacion.monto_asigna),0) AS concepto38,
			
			     'BECA_AGOSTO' AS  concepto39,
			    COALESCE(SUM(hqbeca_agosto.monto_asigna),0) AS concepto40,
			
			     'ESTIPENDIO_JULIO_2012' AS  concepto41,
			    COALESCE(SUM(hqestipendio_julio_2012.monto_asigna),0) AS concepto42,
			
			    'ESTIPENDIO_AGOSTO_2012' AS  concepto43,
			        COALESCE(SUM(hqestipendio_agosto_2012.monto_asigna),0) AS concepto44,
			
			    'BECA_SEPTIEMBRE' AS  concepto45,
			        COALESCE(SUM(hqbeca_septiembre.monto_asigna),0) AS concepto46,
			
			    'BECA_OCTUBRE' AS  concepto47,
			        COALESCE(SUM(hqbeca_octubre.monto_asigna),0) AS concepto48,
			
			    'ESTIPENDIO_SEPTIEMBRE_12' AS  concepto49,
			        COALESCE(SUM(hqestipendio_septiembre_12.monto_asigna),0) AS concepto50,
			
			    'ESTIPENDIO_OCTUBRE_12' AS  concepto51,
			        COALESCE(SUM(hqestipendio_octubre_12.monto_asigna),0) AS concepto52,
			
			        'ESTIPENDIO_JUNIO_2012' AS  concepto53,
			        COALESCE(SUM(hqestipendio_junio_2012.monto_asigna),0) AS concepto54,
			
			        '3/3 TERCIO_BONIFICACIÓN DE FIN DE ANO' AS  concepto55,
			COALESCE(SUM(hq3barra3_tercio_bonificación_de_fin_de_ano.monto_asigna),0) AS concepto56,
			
			        'ESTIPENDIO_NOVIEMBRE_2012' AS  concepto57,
			        COALESCE(SUM(hqestipendio_noviembre_2012.monto_asigna),0) AS concepto58,
			
			        'PAGO ESPECIAL DICIEMBRE 2012' AS  concepto59,
			        COALESCE(SUM(hqpago_especial_diciembre_2012.monto_asigna),0) AS concepto60
			
			FROM
			    trabajador t
			    INNER JOIN historicoquincena hqfiltro ON (t.id_trabajador=hqfiltro.id_trabajador)
			--SUELDO BASICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 2
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqsueldobasico ON (t.id_trabajador = hqsueldobasico.id_trabajador AND hqsueldobasico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqsueldobasico.semana_quincena=hqfiltro.semana_quincena)
			--BONO VACACIONAL
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 12
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbonovacacional ON (t.id_trabajador = hqbonovacacional.id_trabajador AND hqbonovacacional.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonovacacional.semana_quincena=hqfiltro.semana_quincena)
			
			--BONO NACIMIENTO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 29
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbononacimiento ON (t.id_trabajador = hqbononacimiento.id_trabajador AND hqbononacimiento.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbononacimiento.semana_quincena=hqfiltro.semana_quincena)
			--BONO MATRIMONIO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 30
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbonomatrimonio ON (t.id_trabajador = hqbonomatrimonio.id_trabajador AND hqbonomatrimonio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonomatrimonio.semana_quincena=hqfiltro.semana_quincena)
			
			--REINTEGRO HCM
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 108
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqreintegrohcm ON (t.id_trabajador = hqreintegrohcm.id_trabajador AND hqreintegrohcm.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqreintegrohcm.semana_quincena=hqfiltro.semana_quincena)
			
			--HONORARIOS PROFESIONALES
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 132
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqhonorariosprof ON (t.id_trabajador = hqhonorariosprof.id_trabajador AND hqhonorariosprof.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqhonorariosprof.semana_quincena=hqfiltro.semana_quincena)
			
			--BONO UNICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 250
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbonounico ON (t.id_trabajador = hqbonounico.id_trabajador AND hqbonounico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonounico.semana_quincena=hqfiltro.semana_quincena)
			
			--DIFERENCIA SUELDO BASICO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 350
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqdifsueldobas ON (t.id_trabajador = hqdifsueldobas.id_trabajador AND hqdifsueldobas.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifsueldobas.semana_quincena=hqfiltro.semana_quincena)
			
			--AYUDA GASTOS OFTAMOLOGICOS
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 380
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqayudagastooft ON (t.id_trabajador = hqayudagastooft.id_trabajador AND hqayudagastooft.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudagastooft.semana_quincena=hqfiltro.semana_quincena)
			
			
			--PAGO COMPENSATORIO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 581
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpagocompensatorio ON (t.id_trabajador = hqpagocompensatorio.id_trabajador AND hqpagocompensatorio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio.semana_quincena=hqfiltro.semana_quincena)
			
			--BONIFICACION FIN DE AÑO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 675
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbonfinano ON (t.id_trabajador = hqbonfinano.id_trabajador AND hqbonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonfinano.semana_quincena=hqfiltro.semana_quincena)
			
			--AYUDA UTILES ESCOLARES
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 812
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqayudautilesc ON (t.id_trabajador = hqayudautilesc.id_trabajador AND hqayudautilesc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudautilesc.semana_quincena=hqfiltro.semana_quincena)
			
			--BONO DE RENDIMIENTO Y COMPROMISO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 872
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbonorendcomp ON (t.id_trabajador = hqbonorendcomp.id_trabajador AND hqbonorendcomp.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonorendcomp.semana_quincena=hqfiltro.semana_quincena)
			
			--AYUDA  JUGUETE
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 902
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqayudajuguete ON (t.id_trabajador = hqayudajuguete.id_trabajador AND hqayudajuguete.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudajuguete.semana_quincena=hqfiltro.semana_quincena)
			
			--PAGO DE GASTOS DE MOVILIZACIÓN
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1001
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqgastosmovilizacion ON (t.id_trabajador = hqgastosmovilizacion.id_trabajador AND hqgastosmovilizacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqgastosmovilizacion.semana_quincena=hqfiltro.semana_quincena)
			
			--BENEFICIO DE ALIMENTACIÓN
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1141
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbenefalimentacion ON (t.id_trabajador = hqbenefalimentacion.id_trabajador AND hqbenefalimentacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbenefalimentacion.semana_quincena=hqfiltro.semana_quincena)
			
			--PAGO COMPENSATORIO2
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1191
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpagocompensatorio2 ON (t.id_trabajador = hqpagocompensatorio2.id_trabajador AND hqpagocompensatorio2.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio2.semana_quincena=hqfiltro.semana_quincena)
			
			
			--INTERESES PRESTACIONES SOCIALES 2010
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1226
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqintprestsoc2010 ON (t.id_trabajador = hqintprestsoc2010.id_trabajador AND hqintprestsoc2010.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqintprestsoc2010.semana_quincena=hqfiltro.semana_quincena)
			
			--MOVILIZACIÓN INSPECTORES
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1261
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqmovinspectores ON (t.id_trabajador = hqmovinspectores.id_trabajador AND hqmovinspectores.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqmovinspectores.semana_quincena=hqfiltro.semana_quincena)
			
			--2/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1301
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hq2terciobonfinano ON (t.id_trabajador = hq2terciobonfinano.id_trabajador AND hq2terciobonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq2terciobonfinano.semana_quincena=hqfiltro.semana_quincena)
			
			--1/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1302
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hq1terciobonfinano ON (t.id_trabajador = hq1terciobonfinano.id_trabajador AND hq1terciobonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq1terciobonfinano.semana_quincena=hqfiltro.semana_quincena)
			
			-- PENSION
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 7
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpension ON (t.id_trabajador = hqpension.id_trabajador AND hqpension.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpension.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- DIFERENCIA POR ENCARGADURIA
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 70
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqdif_por_enc ON (t.id_trabajador = hqdif_por_enc.id_trabajador AND hqdif_por_enc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdif_por_enc.semana_quincena=hqfiltro.semana_quincena)
			
			-- REINTEGRO CUOTA POLIZA H.C.M.
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 552
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqrein_cuota_poliza_hcm ON (t.id_trabajador = hqrein_cuota_poliza_hcm.id_trabajador AND hqrein_cuota_poliza_hcm.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqrein_cuota_poliza_hcm.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- BONO ESPECIAL
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 674
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbono_especial ON (t.id_trabajador = hqbono_especial.id_trabajador AND hqbono_especial.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbono_especial.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- DIFERENCIA BENEFICIO DE  ALIMENTACION
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1162
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqdiferencia_beneficio_de_alimentacion ON (t.id_trabajador = hqdiferencia_beneficio_de_alimentacion.id_trabajador AND hqdiferencia_beneficio_de_alimentacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdiferencia_beneficio_de_alimentacion.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- PRESTACIONES SOCIALES ENE - ABR 2011
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1311
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqprestaciones_sociales_ene_abr_2011 ON (t.id_trabajador = hqprestaciones_sociales_ene_abr_2011.id_trabajador AND hqprestaciones_sociales_ene_abr_2011.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestaciones_sociales_ene_abr_2011.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- PRESTACIONES SOCIALES_ MAY - AGO _ 2011
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1312
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqprestaciones_sociales_may_ago_2011 ON (t.id_trabajador = hqprestaciones_sociales_may_ago_2011.id_trabajador AND hqprestaciones_sociales_may_ago_2011.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestaciones_sociales_may_ago_2011.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			
			-- PRESTACIONES SOCIALES SEP - DIC 2011
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1313
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqprestaciones_sociales_sep_dic_2011 ON (t.id_trabajador = hqprestaciones_sociales_sep_dic_2011.id_trabajador AND hqprestaciones_sociales_sep_dic_2011.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestaciones_sociales_sep_dic_2011.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- DIAS ADICIONALES PRESTACIONES SOCIALES 2011
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1314
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqdias_adicionales_prestaciones_sociales_2011 ON (t.id_trabajador = hqdias_adicionales_prestaciones_sociales_2011.id_trabajador AND hqdias_adicionales_prestaciones_sociales_2011.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdias_adicionales_prestaciones_sociales_2011.semana_quincena=hqfiltro.semana_quincena)
			
			-- INTERESES PRESTACIONES SOCIALES 2011
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1315
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqint_prestaciones_sociales_2011 ON (t.id_trabajador = hqint_prestaciones_sociales_2011.id_trabajador AND hqint_prestaciones_sociales_2011.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqint_prestaciones_sociales_2011.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- PAGO DE GASTO DE COMUNICACION
			
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1321
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpago_de_gasto_de_comunicacion ON (t.id_trabajador = hqpago_de_gasto_de_comunicacion.id_trabajador AND hqpago_de_gasto_de_comunicacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpago_de_gasto_de_comunicacion.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- PAGO DE MAYOR RESPONSABILIDAD
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1331
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpago_de_mayor_responsabilidad ON (t.id_trabajador = hqpago_de_mayor_responsabilidad.id_trabajador AND hqpago_de_mayor_responsabilidad.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpago_de_mayor_responsabilidad.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- BONO VACACIONAL AÑO 2012
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1351
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbono_vacacional_agno_2012 ON (t.id_trabajador = hqbono_vacacional_agno_2012.id_trabajador AND hqbono_vacacional_agno_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbono_vacacional_agno_2012.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			-- BONO VACACIONAL FRACCIONADO
			
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1352
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbono_vacacional_fraccionado ON (t.id_trabajador = hqbono_vacacional_fraccionado.id_trabajador AND hqbono_vacacional_fraccionado.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbono_vacacional_fraccionado.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- ESTIPENDIO
			
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1361
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio ON (t.id_trabajador = hqestipendio.id_trabajador AND hqestipendio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- BECA
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1371
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca ON (t.id_trabajador = hqbeca.id_trabajador AND hqbeca.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- BECA_JUNIO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1372
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca_junio ON (t.id_trabajador = hqbeca_junio.id_trabajador AND hqbeca_junio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_junio.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			
			-- BECA_JULIO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1373
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca_julio ON (t.id_trabajador = hqbeca_julio.id_trabajador AND hqbeca_julio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_julio.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- INCENTIVO UNICO POR PROFESIONALIZACION
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1384
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqincentivo_unico_por_profesionalizacion ON (t.id_trabajador = hqincentivo_unico_por_profesionalizacion.id_trabajador AND hqincentivo_unico_por_profesionalizacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqincentivo_unico_por_profesionalizacion.semana_quincena=hqfiltro.semana_quincena)
			
			-- BECA_AGOSTO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1391
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca_agosto ON (t.id_trabajador = hqbeca_agosto.id_trabajador AND hqbeca_agosto.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_agosto.semana_quincena=hqfiltro.semana_quincena)
			
			-- ESTIPENDIO_JULIO_2012
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1401
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_julio_2012 ON (t.id_trabajador = hqestipendio_julio_2012.id_trabajador AND hqestipendio_julio_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_julio_2012.semana_quincena=hqfiltro.semana_quincena)
			
			-- ESTIPENDIO_AGOSTO_2012
			
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1402
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_agosto_2012 ON (t.id_trabajador = hqestipendio_agosto_2012.id_trabajador AND hqestipendio_agosto_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_agosto_2012.semana_quincena=hqfiltro.semana_quincena)
			
			
			--  BECA_SEPTIEMBRE
			
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1421
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca_septiembre ON (t.id_trabajador = hqbeca_septiembre.id_trabajador AND hqbeca_septiembre.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_septiembre.semana_quincena=hqfiltro.semana_quincena)
			
			
			--  BECA_OCTUBRE
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1422
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqbeca_octubre ON (t.id_trabajador = hqbeca_octubre.id_trabajador AND hqbeca_octubre.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_octubre.semana_quincena=hqfiltro.semana_quincena)
			
			--  ESTIPENDIO_SEPTIEMBRE_12
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1431
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_septiembre_12 ON (t.id_trabajador = hqestipendio_septiembre_12.id_trabajador AND hqestipendio_septiembre_12.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_septiembre_12.semana_quincena=hqfiltro.semana_quincena)
			
			
			--  ESTIPENDIO_OCTUBRE_12
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1432
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_octubre_12 ON (t.id_trabajador = hqestipendio_octubre_12.id_trabajador AND hqestipendio_octubre_12.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_octubre_12.semana_quincena=hqfiltro.semana_quincena)
			
			
			--  ESTIPENDIO_JUNIO_2012
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1451
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_junio_2012 ON (t.id_trabajador = hqestipendio_junio_2012.id_trabajador AND hqestipendio_junio_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_junio_2012.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- 3/3 TERCIO_BONIFICACIÓN DE FIN DE ANO
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1461
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hq3barra3_tercio_bonificación_de_fin_de_ano ON (t.id_trabajador = hq3barra3_tercio_bonificación_de_fin_de_ano.id_trabajador AND hq3barra3_tercio_bonificación_de_fin_de_ano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq3barra3_tercio_bonificación_de_fin_de_ano.semana_quincena=hqfiltro.semana_quincena)
			
			
			
			--  ESTIPENDIO_NOVIEMBRE_2012
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1473
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqestipendio_noviembre_2012 ON (t.id_trabajador = hqestipendio_noviembre_2012.id_trabajador AND hqestipendio_noviembre_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqestipendio_noviembre_2012.semana_quincena=hqfiltro.semana_quincena)
			
			
			-- PAGO ESPECIAL DICIEMBRE 2012
			
			LEFT OUTER JOIN (SELECT id_trabajador,
			            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
			        FROM historicoquincena
			        WHERE id_concepto_tipo_personal IN
			                    (SELECT id_concepto_tipo_personal
			                    FROM conceptotipopersonal
			                    WHERE id_concepto = 1481
			                    )
			            AND anio=2012 AND mes=".$mes."
			        ) hqpago_especial_diciembre_2012 ON (t.id_trabajador = hqpago_especial_diciembre_2012.id_trabajador AND hqpago_especial_diciembre_2012.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpago_especial_diciembre_2012.semana_quincena=hqfiltro.semana_quincena)
			
			--CONDICIONES FIJAS
			INNER JOIN personal p ON (t.cedula=p.cedula)
			INNER JOIN cargo c ON (CASE WHEN t.id_cargo_real IS NOT NULL THEN t.id_cargo_real=c.id_cargo ELSE t.id_cargo=c.id_cargo END)
			WHERE
			    --t.cedula = 15023598
			    --AND
			     hqfiltro.anio=2012 AND hqfiltro.mes=".$mes."
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
			
			//echo $query;
			
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
						trim($trabajador['reintegro_hcm']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_reintegro_hcm'])) . ';' .
						trim($trabajador['honorarios_profesionales']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_honorarios_profesionales'])) . ';' .
						trim($trabajador['bono_unico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_unico'])) . ';' .
						trim($trabajador['diferencia_sueldo_basico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_diferencia_sueldo_basico'])) . ';' .
						trim($trabajador['ayuda_gastos_oftamologicos']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_gastos_oftamologicos'])) . ';' .
						trim($trabajador['pago_compensatorio']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_compensatorio'])) . ';' .
						trim($trabajador['bonificacion_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bonificacion_fin_de_año'])) . ';' .
						trim($trabajador['ayuda_utiles_escolares']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_utiles_escolares'])) . ';' .
						trim($trabajador['bono_de_rendimiento_y_compromiso']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_de_rendimiento_y_compromiso'])) . ';' .
						trim($trabajador['ayuda_juguete']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_juguete'])) . ';' .
						trim($trabajador['pago_de_gastos_de_movilizacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_de_gastos_de_movilizacion'])) . ';' .
						trim($trabajador['beneficio_de_alimentacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_beneficio_de_alimentacion'])) . ';' .
						trim($trabajador['pago_compensatorio2']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_compensatorio2'])) . ';' .
						trim($trabajador['movilizacion_inspectores']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_movilizacion_inspectores'])) . ';' .
						trim($trabajador['dos_tercio_bonificacion_de_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_2_tercio_bonificación_de_fin_de_año'])) . ';' .
						trim($trabajador['uno_tercio_bonificacion_de_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_1_tercio_bonificación_de_fin_de_año'])) . ';' .				
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
						str_replace('.', ',', trim($trabajador['concepto26'])) . ';' .
						trim($trabajador['concepto27']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto28'])) . ';' .
						trim($trabajador['concepto29']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto30'])) . ';' .
						trim($trabajador['concepto31']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto32'])) . ';' .
						trim($trabajador['concepto33']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto34'])) . ';' .
						trim($trabajador['concepto35']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto36'])) . ';' .
						trim($trabajador['concepto37']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto38'])) . ';' .
						trim($trabajador['concepto39']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto40'])) . ';' .
						trim($trabajador['concepto41']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto42'])) . ';' .
						trim($trabajador['concepto43']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto44'])) . ';' .
						trim($trabajador['concepto45']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto46'])) . ';' .
						trim($trabajador['concepto47']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto48'])) . ';' .
						trim($trabajador['concepto49']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto50'])) . ';' .
						trim($trabajador['concepto51']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto52'])) . ';' .
						trim($trabajador['concepto53']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto54'])) . ';' .
						trim($trabajador['concepto55']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto56'])) . ';' .
						trim($trabajador['concepto57']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto58'])) . ';' .
						trim($trabajador['concepto59']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto60'])) .
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