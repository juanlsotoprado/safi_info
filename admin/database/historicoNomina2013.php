<?php
header("Content-Type: text/plain; charset=utf-8");

include(dirname(__FILE__) . "/../../init.php");
require(SAFI_BASE_PATH . '/lib/database/mysql.php');

new ClassHistoricoNomina2013();

class ClassHistoricoNomina2013
{
	private $_errors = array();
	private $_db = null;
	
	public function __construct()
	{
		try
		{
			$this->conectarSigefirrhh();
			
			for ($mes = 1; $mes <= 3; $mes++) {
				$fechaCorte = '28-'.($mes < 10 ? '0' . $mes : $mes).'-2013';
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
		/*$configSigefirrhh["dbEncoding"] = 'UTF8';
		$configSigefirrhh["dbServer"] = "150.188.85.30";
		$configSigefirrhh["dbPort"] = "5432";
		$configSigefirrhh["dbUser"] = "sigefirrhh";
		$configSigefirrhh["dbPass"] = "s!g3f!rrhh";
		$configSigefirrhh["dbDatabase"] = "sigefirrhh";*/

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
    --TO_CHAR(t.fecha_antiguedad, 'DD-MM-YYYY') AS fecha_antiguedad,
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
    'PAGO COMPENSATORIO' AS pago_compensatorio,
    COALESCE(SUM(hqpagocompensatorio.monto_asigna),0) AS monto_pago_compensatorio,
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

    'PENSION' AS concepto1,
    COALESCE(SUM(hqpension.monto_asigna),0) AS  concepto2,

    'BONO FIN DE AÑO' AS concepto3,
    COALESCE(SUM(hqbonofinano.monto_asigna),0) AS concepto4,

    'UTILES' AS concepto5,
    COALESCE(SUM(hqutiles.monto_asigna),0) AS concepto6,

    'COMPENSACION SALARIAL' AS concepto7,
    COALESCE(SUM(hqcompesacion_sala.monto_asigna),0) AS concepto8,

    'DIFERENCIA BENEFICIO DE  ALIMENTACION' AS concepto9,
    COALESCE(SUM(hqdif_benef_alim.monto_asigna),0) AS concepto10,

    'PAGO DE GASTO DE COMUNICACION' AS concepto11,
    COALESCE(SUM(hqpago_gto_comu.monto_asigna),0) AS concepto12,

    'PAGO DE MAYOR RESPONSABILIDAD' AS concepto13,
    COALESCE(SUM(hqpago_may_resp.monto_asigna),0) AS concepto14,

    'BECA' AS concepto15,
    COALESCE(SUM(hqbeca.monto_asigna),0) AS concepto16,

    'BECA_JUNIO' AS concepto17,
    COALESCE(SUM(hqbeca_junio.monto_asigna),0) AS concepto18,

    'BECA_JULIO' AS concepto19,
    COALESCE(SUM(hqbeca_julio.monto_asigna),0) AS concepto20,

    'INCENTIVO UNICO POR PROFESIONALIZACION' AS concepto21,
    COALESCE(SUM(hqbeca_inc_unic_prof.monto_asigna),0) AS concepto22,

    'BECA_AGOSTO' AS concepto23,
    COALESCE(SUM(hqbeca_agosto.monto_asigna),0) AS concepto24,

    'BECA_SEPTIEMBRE' AS concepto25,
    COALESCE(SUM(hqbeca_septiembre.monto_asigna),0) AS concepto26,

    'BECA_OCTUBRE' AS concepto27,
    COALESCE(SUM(hqbeca_octubre.monto_asigna),0) AS concepto28,

    '3/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO' AS concepto29,
    COALESCE(SUM(hqtrestercio_bon_fina.monto_asigna),0) AS concepto30,

    'PRESTACIONES SOCIALES AL 30/04/2012' AS concepto31,
    COALESCE(SUM(hqprestsocabr.monto_asigna),0) AS concepto32,

    'PRESTACIONES SOCIALES AL 30/08/2012' AS concepto33,
    COALESCE(SUM(hqprestsocago.monto_asigna),0) AS concepto34,

    'PRESTACIONES SOCIALES AL 31/12/2012' AS concepto35,
    COALESCE(SUM(hqprestsocdic.monto_asigna),0) AS concepto36,

    'DIAS ADICIONALES AL 31/12/12' AS concepto37,
    COALESCE(SUM(hqdiasadicdic.monto_asigna),0) AS concepto38,

    'INTERESES DE PRESTACIONES SOCIALES ACUM. AL 2012' AS concepto39,
    COALESCE(SUM(hqintprestsoc12.monto_asigna),0) AS concepto40,

    'PRIMA POR RESPONSABILIDAD' AS concepto41,
    COALESCE(SUM(hqprima_resp.monto_asigna),0) AS concepto42,

    'BECA_ENERO' AS concepto43,
    COALESCE(SUM(hqbecaenero.monto_asigna),0) AS concepto44,

    'BECA_FEBRERO' AS concepto45,
    COALESCE(SUM(hqbecafebrero.monto_asigna),0) AS concepto46,

    'BECA_MARZO' AS concepto47,
    COALESCE(SUM(hqbecamarzo.monto_asigna),0) AS concepto48,

    'BECA_MARZO 2013' AS concepto49,
    COALESCE(SUM(hqbecamarzo13.monto_asigna),0) AS concepto50,

    'BECA_1ERA ABRIL 2013' AS concepto51,
    COALESCE(SUM(hqbecabrimabril13.monto_asigna),0) AS concepto52,

    'BECA_ABRIL' AS concepto53,
    COALESCE(SUM(hqbecabri.monto_asigna),0) AS concepto54,

    'BECA_MAYO' AS concepto55,
    COALESCE(SUM(hqbecamay.monto_asigna),0) AS concepto56,

    'BONO VACACIONAL 2012-2013' AS concepto57,
    COALESCE(SUM(hqbonovac1213.monto_asigna),0) AS concepto58,

    'FRACCIÓN DE BONO VACACIONAL AL 31/12/13' AS concepto59,
    COALESCE(SUM(hqfraccbonvacdic13.monto_asigna),0) AS concepto60,

    'DIFERENCIAS BONO UNICO' AS concepto61,
    COALESCE(SUM(hqdifbonunico.monto_asigna),0) AS concepto62,

    'BECA NOVIEMBRE' AS concepto63,
    COALESCE(SUM(hqbecanov.monto_asigna),0) AS concepto64

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
            AND anio=2013 AND mes=".$mes."
        ) hqsueldobasico ON (t.id_trabajador = hqsueldobasico.id_trabajador AND hqsueldobasico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqsueldobasico.semana_quincena = hqfiltro.semana_quincena)
--BONO VACACIONAL
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 12
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbonovacacional ON (t.id_trabajador = hqbonovacacional.id_trabajador AND hqbonovacacional.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonovacacional.semana_quincena = hqfiltro.semana_quincena)

--BONO NACIMIENTO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 29
                    )
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
        ) hqdifbonovac ON (t.id_trabajador = hqdifbonovac.id_trabajador AND hqdifbonovac.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifbonovac.semana_quincena = hqfiltro.semana_quincena)

--PAGO COMPENSATORIO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 581
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqpagocompensatorio ON (t.id_trabajador = hqpagocompensatorio.id_trabajador AND hqpagocompensatorio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio.semana_quincena = hqfiltro.semana_quincena)

--AYUDA UTILES ESCOLARES
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 812
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqayudautilesc ON (t.id_trabajador = hqayudautilesc.id_trabajador AND hqayudautilesc.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudautilesc.semana_quincena = hqfiltro.semana_quincena)

--BONO DE RENDIMIENTO Y COMPROMISO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 872
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbonorendcomp ON (t.id_trabajador = hqbonorendcomp.id_trabajador AND hqbonorendcomp.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonorendcomp.semana_quincena = hqfiltro.semana_quincena)

--AYUDA  JUGUETE
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 902
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqayudajuguete ON (t.id_trabajador = hqayudajuguete.id_trabajador AND hqayudajuguete.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqayudajuguete.semana_quincena = hqfiltro.semana_quincena)

--PAGO DE GASTOS DE MOVILIZACIÓN
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1001
                    )
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
        ) hqbenefalimentacion ON (t.id_trabajador = hqbenefalimentacion.id_trabajador AND hqbenefalimentacion.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbenefalimentacion.semana_quincena = hqfiltro.semana_quincena)

--PAGO COMPENSATORIO2
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1191
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqpagocompensatorio2 ON (t.id_trabajador = hqpagocompensatorio2.id_trabajador AND hqpagocompensatorio2.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpagocompensatorio2.semana_quincena = hqfiltro.semana_quincena)


--INTERESES PRESTACIONES SOCIALES 2010
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1226
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqintprestsoc2010 ON (t.id_trabajador = hqintprestsoc2010.id_trabajador AND hqintprestsoc2010.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqintprestsoc2010.semana_quincena = hqfiltro.semana_quincena)

--MOVILIZACIÓN INSPECTORES
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1261
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqmovinspectores ON (t.id_trabajador = hqmovinspectores.id_trabajador AND hqmovinspectores.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqmovinspectores.semana_quincena = hqfiltro.semana_quincena)

--2/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1301
                    )
            AND anio=2013 AND mes=".$mes."
        ) hq2terciobonfinano ON (t.id_trabajador = hq2terciobonfinano.id_trabajador AND hq2terciobonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq2terciobonfinano.semana_quincena = hqfiltro.semana_quincena)

--1/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1302
                    )
            AND anio=2013 AND mes=".$mes."
        ) hq1terciobonfinano ON (t.id_trabajador = hq1terciobonfinano.id_trabajador AND hq1terciobonfinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hq1terciobonfinano.semana_quincena = hqfiltro.semana_quincena)

--PENSION
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 7
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqpension ON (t.id_trabajador = hqpension.id_trabajador AND hqpension.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpension.semana_quincena = hqfiltro.semana_quincena)

--BONO FIN DE AÑO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 13
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbonofinano ON (t.id_trabajador = hqbonofinano.id_trabajador AND hqbonofinano.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonofinano.semana_quincena = hqfiltro.semana_quincena)

--UTILES
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 27
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqutiles ON (t.id_trabajador = hqutiles.id_trabajador AND hqutiles.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqutiles.semana_quincena = hqfiltro.semana_quincena)

--COMPENSACION SALARIAL
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 74
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqcompesacion_sala ON (t.id_trabajador = hqcompesacion_sala.id_trabajador AND hqcompesacion_sala.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqcompesacion_sala.semana_quincena = hqfiltro.semana_quincena)


--DIFERENCIA BENEFICIO DE  ALIMENTACION
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1164
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqdif_benef_alim ON (t.id_trabajador = hqdif_benef_alim.id_trabajador AND hqdif_benef_alim.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdif_benef_alim.semana_quincena = hqfiltro.semana_quincena)

--PAGO DE GASTO DE COMUNICACION
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1321
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqpago_gto_comu ON (t.id_trabajador = hqpago_gto_comu.id_trabajador AND hqpago_gto_comu.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpago_gto_comu.semana_quincena = hqfiltro.semana_quincena)

--PAGO DE MAYOR RESPONSABILIDAD
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1331
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqpago_may_resp ON (t.id_trabajador = hqpago_may_resp.id_trabajador AND hqpago_may_resp.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqpago_may_resp.semana_quincena = hqfiltro.semana_quincena)

--BECA
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1371
                    )
            AND anio=2013 AND mes=".$mes."
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
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_junio ON (t.id_trabajador = hqbeca_junio.id_trabajador AND hqbeca_junio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_junio.semana_quincena = hqfiltro.semana_quincena)

--BECA_JULIO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1373
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_julio ON (t.id_trabajador = hqbeca_julio.id_trabajador AND hqbeca_julio.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_julio.semana_quincena = hqfiltro.semana_quincena)

--INCENTIVO UNICO POR PROFESIONALIZACION
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1384
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_inc_unic_prof ON (t.id_trabajador = hqbeca_inc_unic_prof.id_trabajador AND hqbeca_inc_unic_prof.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_inc_unic_prof.semana_quincena = hqfiltro.semana_quincena)

--BECA_AGOSTO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1391
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_agosto ON (t.id_trabajador = hqbeca_agosto.id_trabajador AND hqbeca_agosto.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_agosto.semana_quincena = hqfiltro.semana_quincena)

--BECA_SEPTIEMBRE
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1421
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_septiembre ON (t.id_trabajador = hqbeca_septiembre.id_trabajador AND hqbeca_septiembre.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_septiembre.semana_quincena = hqfiltro.semana_quincena)

--BECA_OCTUBRE
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1422
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbeca_octubre ON (t.id_trabajador = hqbeca_octubre.id_trabajador AND hqbeca_octubre.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbeca_octubre.semana_quincena = hqfiltro.semana_quincena)

--3/3 TERCIO_BONIFICACIÓN DE FIN DE AÑO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1461
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqtrestercio_bon_fina ON (t.id_trabajador = hqtrestercio_bon_fina.id_trabajador AND hqtrestercio_bon_fina.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqtrestercio_bon_fina.semana_quincena = hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES AL 30/04/2012
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1491
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqprestsocabr ON (t.id_trabajador = hqprestsocabr.id_trabajador AND hqprestsocabr.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocabr.semana_quincena = hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES AL 30/08/2012
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1492
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqprestsocago ON (t.id_trabajador = hqprestsocago.id_trabajador AND hqprestsocago.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocago.semana_quincena = hqfiltro.semana_quincena)

--PRESTACIONES SOCIALES AL 31/12/2012
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1493
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqprestsocdic ON (t.id_trabajador = hqprestsocdic.id_trabajador AND hqprestsocdic.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprestsocdic.semana_quincena = hqfiltro.semana_quincena)

--DIAS ADICIONALES AL 31/12/12
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1494
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqdiasadicdic ON (t.id_trabajador = hqdiasadicdic.id_trabajador AND hqdiasadicdic.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdiasadicdic.semana_quincena = hqfiltro.semana_quincena)

--INTERESES DE PRESTACIONES SOCIALES ACUM. AL 2012
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1495
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqintprestsoc12 ON (t.id_trabajador = hqintprestsoc12.id_trabajador AND hqintprestsoc12.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqintprestsoc12.semana_quincena = hqfiltro.semana_quincena)

--PRIMA POR RESPONSABILIDAD
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1501
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqprima_resp ON (t.id_trabajador = hqprima_resp.id_trabajador AND hqprima_resp.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqprima_resp.semana_quincena = hqfiltro.semana_quincena)

--BECA_ENERO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1521
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecaenero ON (t.id_trabajador = hqbecaenero.id_trabajador AND hqbecaenero.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecaenero.semana_quincena = hqfiltro.semana_quincena)
--BECA_FEBRERO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1522
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecafebrero ON (t.id_trabajador = hqbecafebrero.id_trabajador AND hqbecafebrero.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecafebrero.semana_quincena = hqfiltro.semana_quincena)

--BECA_MARZO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1531
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecamarzo ON (t.id_trabajador = hqbecamarzo.id_trabajador AND hqbecamarzo.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecamarzo.semana_quincena = hqfiltro.semana_quincena)

--BECA_MARZO 2013
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1541
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecamarzo13 ON (t.id_trabajador = hqbecamarzo13.id_trabajador AND hqbecamarzo13.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecamarzo13.semana_quincena = hqfiltro.semana_quincena)

--BECA_1ERA ABRIL 2013
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1542
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecabrimabril13 ON (t.id_trabajador = hqbecabrimabril13.id_trabajador AND hqbecabrimabril13.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecabrimabril13.semana_quincena = hqfiltro.semana_quincena)

--BECA_ABRIL
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1551
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecabri ON (t.id_trabajador = hqbecabri.id_trabajador AND hqbecabri.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecabri.semana_quincena = hqfiltro.semana_quincena)

--BECA_MAYO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1561
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecamay ON (t.id_trabajador = hqbecamay.id_trabajador AND hqbecamay.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecamay.semana_quincena = hqfiltro.semana_quincena)

--BONO VACACIONAL 2012-2013
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1572
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbonovac1213 ON (t.id_trabajador = hqbonovac1213.id_trabajador AND hqbonovac1213.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbonovac1213.semana_quincena = hqfiltro.semana_quincena)

--FRACCIÓN DE BONO VACACIONAL AL 31/12/13
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1573
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqfraccbonvacdic13 ON (t.id_trabajador = hqfraccbonvacdic13.id_trabajador AND hqfraccbonvacdic13.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqfraccbonvacdic13.semana_quincena = hqfiltro.semana_quincena)

--DIFERENCIAS BONO UNICO
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1581
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqdifbonunico ON (t.id_trabajador = hqdifbonunico.id_trabajador AND hqdifbonunico.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqdifbonunico.semana_quincena = hqfiltro.semana_quincena)

--BECA NOVIEMBRE
LEFT OUTER JOIN (SELECT id_trabajador,
            monto_asigna, anio, mes, id_concepto_tipo_personal, semana_quincena
        FROM historicoquincena
        WHERE id_concepto_tipo_personal IN
                    (SELECT id_concepto_tipo_personal
                    FROM conceptotipopersonal
                    WHERE id_concepto = 1592
                    )
            AND anio=2013 AND mes=".$mes."
        ) hqbecanov ON (t.id_trabajador = hqbecanov.id_trabajador AND hqbecanov.id_concepto_tipo_personal=hqfiltro.id_concepto_tipo_personal AND hqbecanov.semana_quincena = hqfiltro.semana_quincena)






--CONDICIONES FIJAS
INNER JOIN personal p ON (t.cedula=p.cedula)
INNER JOIN cargo c ON (CASE WHEN t.id_cargo_real IS NOT NULL THEN t.id_cargo_real=c.id_cargo ELSE t.id_cargo=c.id_cargo END)
WHERE
    --t.cedula = 15023598
    --AND
     hqfiltro.anio=2013 AND hqfiltro.mes=".$mes."
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
                        (($segundoNombre = trim($trabajador['segundo_nombre'])) == "" ? "NULL" : $segundoNombre) . ';' . 						trim($trabajador['sexo']) . ';' .  
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
						trim($trabajador['pago_compensatorio']) . ';' .
						str_replace('.', ',', trim($trabajador['monto_pago_compensatorio'])) . ';' .
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
						str_replace('.', ',', trim($trabajador['concepto60'])) . ';' .
						trim($trabajador['concepto61']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto62'])) . ';' .
						trim($trabajador['concepto63']) . ';' .
						str_replace('.', ',', trim($trabajador['concepto64'])) .
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