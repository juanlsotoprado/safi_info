<?php
//echo "No hace nada";
//exit();

header("Content-Type: text/plain; charset=utf-8");

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');

new ClassHistoricoNomina2011();

class ClassHistoricoNomina2011
{
	private $_errors = array();
	private $_db = null;
	
	public function __construct()
	{
		try
		{
			$this->conectarSigefirrhh();
			
			for ($mes = 10; $mes <= 12; $mes++) {
				$fechaCorte = '28-'.($mes < 10 ? '0' . $mes : $mes).'-2011';
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
    'DIFERENCIA DE BONO VACACIONAL' AS diferencia_de_bono_vacacional,
    COALESCE(SUM(hqdifbonovac.monto_asigna),0) AS monto_diferencia_de_bono_vacacional,
    'PAGO COMPENSATORIO' AS pago_compensatorio,
    COALESCE(SUM(hqpagocompensatorio.monto_asigna),0) AS monto_pago_compensatorio,
    'DIFERENCIA BONO UNICO' AS diferencia_bono_unico,
    COALESCE(SUM(hqdifbonounico.monto_asigna),0) AS monto_diferencia_bono_unico,
    'BONIFICACION FIN DE AÑO' AS bonificacion_fin_de_año,
    COALESCE(SUM(hqbonfinano.monto_asigna),0) AS monto_bonificacion_fin_de_año,
    'AYUDA UTILES ESCOLARES' AS ayuda_utiles_escolares,
    COALESCE(SUM(hqayudautilesc.monto_asigna),0) AS monto_ayuda_utiles_escolares ,
    'BONO DE RENDIMIENTO Y COMPROMISO' AS bono_de_rendimiento_y_compromiso,
    COALESCE(SUM(hqbonorendcomp.monto_asigna),0) AS monto_bono_de_rendimiento_y_compromiso,
    'AYUDA  JUGUETE' AS  ayuda_juguete,
    COALESCE(SUM(hqayudajuguete.monto_asigna),0) AS monto_ayuda_juguete,
    'REINTEGRO POR DTO. INDEBIDO EN EXCESO DE CELULAR' AS reintegro_por_dto_indebido_en_exceso_de_celular,
    COALESCE(SUM(hqreintdtoindcelular.monto_asigna),0) AS monto_reintegro_por_dto_indebido_en_exceso_de_celular,
    'PAGO DE GASTOS DE MOVILIZACIÓN' AS pago_de_gastos_de_movilizacion,
    COALESCE(SUM(hqgastosmovilizacion.monto_asigna),0) AS monto_pago_de_gastos_de_movilizacion,
    'BENEFICIO DE ALIMENTACIÓN' AS beneficio_de_alimentacion,
    COALESCE(SUM(hqbenefalimentacion.monto_asigna),0) AS monto_beneficio_de_alimentacion,
    'PAGO COMPENSATORIO2'  AS pago_compensatorio2,
    COALESCE(SUM(hqpagocompensatorio2.monto_asigna),0) AS monto_pago_compensatorio2,
    'PRESTACIONES SOCIALES ENE - FEB 2010' AS prestaciones_sociales_ene_feb_2010,
    COALESCE(SUM(hqprestsocenefeb2010.monto_asigna),0) AS monto_prestaciones_sociales_ene_feb_2010,
    'PRESTACIONES SOCIALES_MAR - ABR_ 2010' AS prestaciones_sociales_mar_abr_2010,
    COALESCE(SUM(hqprestsocmarabr2010.monto_asigna),0) AS monto_prestaciones_sociales_mar_abr_2010,
    'PRESTACIONES SOCIALES_MAY - DIC_ 2010' AS prestaciones_sociales_may_dic_2010,
    COALESCE(SUM(hqprestsocmaydic2010.monto_asigna),0) AS monto_prestaciones_sociales_may_dic_2010,
    'DIAS ADICIONALES PRESTACIONES SOCIALES 2010' AS dias_adicionales_prestaciones_sociales_2010,
    COALESCE(SUM(hqdiasadicprestsoc.monto_asigna),0) AS monto_dias_adicionales_prestaciones_sociales_2010,
    'INTERESES PRESTACIONES SOCIALES 2010' AS intereses_prestaciones_sociales_2010,
    COALESCE(SUM(hqintprestsoc2010.monto_asigna),0) AS monto_intereses_prestaciones_sociales_2010,
    'MOVILIZACIÓN INSPECTORES' AS movilizacion_inspectores,
    COALESCE(SUM(hqmovinspectores.monto_asigna),0) AS monto_movilizacion_inspectores,
    '2/3 TERCIO_BONIFICACION DE FIN DE ANO' AS dos_tercio_bonificacion_de_fin_de_año,
    COALESCE(SUM(hq2terciobonfinano.monto_asigna),0) AS monto_2_tercio_bonificación_de_fin_de_año,
    '1/3 TERCIO_BONIFICACION DE FIN DE ANO' AS uno_tercio_bonificacion_de_fin_de_año,
    COALESCE(SUM(hq1terciobonfinano.monto_asigna),0) AS monto_1_tercio_bonificación_de_fin_de_año

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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
        ) hqayudagastooft ON (t.id_trabajador = hqayudagastooft.id_trabajador AND hqayudagastooft.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudagastooft.semana_quincena=hqfiltro.semana_quincena)

--DIFERENCIA DE BONO VACACIONAL
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 505
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqdifbonovac ON (t.id_trabajador = hqdifbonovac.id_trabajador AND hqdifbonovac.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifbonovac.semana_quincena=hqfiltro.semana_quincena)

--PAGO COMPENSATORIO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 581
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqpagocompensatorio ON (t.id_trabajador = hqpagocompensatorio.id_trabajador AND hqpagocompensatorio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio.semana_quincena=hqfiltro.semana_quincena)

--DIFERENCIA BONO UNICO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 661
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqdifbonounico ON (t.id_trabajador = hqdifbonounico.id_trabajador AND hqdifbonounico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifbonounico.semana_quincena=hqfiltro.semana_quincena)

--BONIFICACION FIN DE AÑO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 675
                    )
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
        ) hqayudajuguete ON (t.id_trabajador = hqayudajuguete.id_trabajador AND hqayudajuguete.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudajuguete.semana_quincena=hqfiltro.semana_quincena)

--REINTEGRO POR DTO. INDEBIDO EN EXCESO DE CELULAR
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 991
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqreintdtoindcelular ON (t.id_trabajador = hqreintdtoindcelular.id_trabajador AND hqreintdtoindcelular.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqreintdtoindcelular.semana_quincena=hqfiltro.semana_quincena)

--PAGO DE GASTOS DE MOVILIZACIÓN
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1001
                    )
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
        ) hqpagocompensatorio2 ON (t.id_trabajador = hqpagocompensatorio2.id_trabajador AND hqpagocompensatorio2.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio2.semana_quincena=hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES ENE - FEB 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1221
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqprestsocenefeb2010 ON (t.id_trabajador = hqprestsocenefeb2010.id_trabajador AND hqprestsocenefeb2010.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocenefeb2010.semana_quincena=hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES_MAR - ABR_ 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1222
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqprestsocmarabr2010 ON (t.id_trabajador = hqprestsocmarabr2010.id_trabajador AND hqprestsocmarabr2010.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocmarabr2010.semana_quincena=hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES_MAY - DIC_ 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1223
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqprestsocmaydic2010 ON (t.id_trabajador = hqprestsocmaydic2010.id_trabajador AND hqprestsocmaydic2010.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocmaydic2010.semana_quincena=hqfiltro.semana_quincena)

--DIAS ADICIONALES PRESTACIONES SOCIALES 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1224
                    )
            AND anio=2011 AND mes=".$mes."
        ) hqdiasadicprestsoc ON (t.id_trabajador = hqdiasadicprestsoc.id_trabajador AND hqdiasadicprestsoc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdiasadicprestsoc.semana_quincena=hqfiltro.semana_quincena)

--INTERESES PRESTACIONES SOCIALES 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena 
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1226
                    )
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
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
            AND anio=2011 AND mes=".$mes."
        ) hq1terciobonfinano ON (t.id_trabajador = hq1terciobonfinano.id_trabajador AND hq1terciobonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq1terciobonfinano.semana_quincena=hqfiltro.semana_quincena)





--CONDICIONES FIJAS
INNER JOIN personal p ON (t.cedula=p.cedula)
INNER JOIN cargo c ON (CASE WHEN t.id_cargo_real IS NOT NULL THEN t.id_cargo_real=c.id_cargo ELSE t.id_cargo=c.id_cargo END)
WHERE
    --t.cedula = 15023598
    --AND
     hqfiltro.anio=2011 AND hqfiltro.mes=".$mes."
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
						trim($trabajador['diferencia_de_bono_vacacional']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_diferencia_de_bono_vacacional'])) . ';' .
						trim($trabajador['pago_compensatorio']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_compensatorio'])) . ';' .
						trim($trabajador['diferencia_bono_unico']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_diferencia_bono_unico'])) . ';' .
						trim($trabajador['bonificacion_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bonificacion_fin_de_año'])) . ';' .
						trim($trabajador['ayuda_utiles_escolares']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_utiles_escolares'])) . ';' .
						trim($trabajador['bono_de_rendimiento_y_compromiso']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_bono_de_rendimiento_y_compromiso'])) . ';' .
						trim($trabajador['ayuda_juguete']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_ayuda_juguete'])) . ';' .
						trim($trabajador['reintegro_por_dto_indebido_en_exceso_de_celular']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_reintegro_por_dto_indebido_en_exceso_de_celular'])) . ';' .
						trim($trabajador['pago_de_gastos_de_movilizacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_de_gastos_de_movilizacion'])) . ';' .
						trim($trabajador['beneficio_de_alimentacion']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_beneficio_de_alimentacion'])) . ';' .
						trim($trabajador['pago_compensatorio2']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_compensatorio2'])) . ';' .
						trim($trabajador['prestaciones_sociales_ene_feb_2010']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_prestaciones_sociales_ene_feb_2010'])) . ';' .
						trim($trabajador['prestaciones_sociales_mar_abr_2010']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_prestaciones_sociales_mar_abr_2010'])) . ';' .
						trim($trabajador['prestaciones_sociales_may_dic_2010']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_prestaciones_sociales_may_dic_2010'])) . ';' .
						trim($trabajador['dias_adicionales_prestaciones_sociales_2010']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_dias_adicionales_prestaciones_sociales_2010'])) . ';' .
						trim($trabajador['intereses_prestaciones_sociales_2010']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_intereses_prestaciones_sociales_2010'])) . ';' .
						trim($trabajador['movilizacion_inspectores']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_movilizacion_inspectores'])) . ';' .
						trim($trabajador['dos_tercio_bonificacion_de_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_2_tercio_bonificación_de_fin_de_año'])) . ';' .
						trim($trabajador['uno_tercio_bonificacion_de_fin_de_año']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_1_tercio_bonificación_de_fin_de_año'])) .
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