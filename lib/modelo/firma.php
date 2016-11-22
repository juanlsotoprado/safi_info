<?php

include_once(SAFI_INCLUDE_PATH . "/perfiles/constantesPerfiles.php");

class SafiModeloFirma{
	public static function GetFirmaByPerfiles($perfiles){

		$firmas = array();
		
		
		
		$query = self::GetQueryFirmaByPerfiles($perfiles);
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$firmas [$row['perfil_empleado']]['perfil_empleado'] = $row['perfil_empleado'];
				$firmas [$row['perfil_empleado']]['cedula_empleado'] = $row['cedula_empleado'];
				$firmas [$row['perfil_empleado']]['nombre_empleado'] = mb_convert_case($row['nombre_empleado'], MB_CASE_TITLE, "ISO-8859-1");
				$firmas [$row['perfil_empleado']]['id_cargo'] = $row['id_cargo'];
				
				/***************************************************************
				 * Excepciones sobre los nombres de los cargos: "nombre_cargo" *
				 **************************************************************/
				if ( strcmp(trim($row['id_dependencia']), DEPENDENCIA_COORDINACION_GENERAL_DE_LA_RED_INFOCENTRO)==0 )
					$nombre_cargo = 'Coordinador General';
				else if ( strcmp(trim($row['id_dependencia']),DEPENDENCIA_COORDINACION_GENERAL_DE_LA_RED_DE_FORMACION)==0 )
					$nombre_cargo = 'Coordinador General';
				else if (
					strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_JEFE, 0, 2))==0
					&& strcmp($row['id_dependencia'],'453')==0
				){
					$nombre_cargo = "Jefe";
				}
				else if (
					(strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, 0, 2))==0
						&& strcmp($row['id_dependencia'],'450')==0
					)
				){
					$nombre_cargo = "Director (E)";
				}/*else if (
					(strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_DIRECTOR_TALENTO_HUMANO, 0, 2))==0
						&& strcmp($row['id_dependencia'],'500')==0
					)
				){
					$nombre_cargo = "Directora (E)";
				}*/
				else 
					$nombre_cargo = $row['nombre_cargo'];
					
				/**********************************************************************
				 * Fin de excepciones sobre los nombres de los cargos: "nombre_cargo" *
				 *********************************************************************/
					
				$de = " de ";

				/*Casos Gerentes/Directores*/
				if (	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_DIRECTOR, 0, 2))==0 || 
						strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_GERENTE, 0, 2))==0 || 
						strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_PRESIDENTE_CARGO, 0, 2))==0 || 
						strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_DIRECTOR_EJECUTIVO_CARGO, 0, 2))==0 ||
						strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_CONSULTOR_JURIDICO, 0, 2))==0
				) {
							
					if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_OFICINA_DE_COMUNICACION_ESTRATEGICA)==0)
						$nombre_dependencia = 'Comunicación Estratégica';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_OFICINA_DE_PLANIFICACION_PRESUPUESTO_Y_CONTROL)==0)
						$nombre_dependencia = 'Planificación, Presupuesto y Control';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_OFICINA_DE_GESTION_ADMINISTRATIVA_Y_FINANCIERA)==0)
						$nombre_dependencia = 'Gestión Administrativa y Financiera';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_OFICINA_TALENTO_HUMANO)==0)
						$nombre_dependencia = 'Talento Humano';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_GERENCIA_DE_TECNOLOGIA)==0)
						$nombre_dependencia = 'Tecnología';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_GERENCIA_DE_INFRAESTRUCTURA)==0)
						$nombre_dependencia = 'Infraestructura';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_COORDINACION_GENERAL_DE_LA_RED_INFOCENTRO)==0)
						$nombre_dependencia = 'la Red Infocentro';
					else if (strcmp(trim($row['id_dependencia']), DEPENDENCIA_COORDINACION_GENERAL_DE_LA_RED_DE_FORMACION)==0)
						$nombre_dependencia = 'la Red de Formación';
					else if (
						strcmp(trim($row['id_dependencia']), DEPENDENCIA_CONSULTORIA_JURIDICA)==0 || 
						strcmp(trim($row['id_dependencia']), DEPENDENCIA_PRESIDENCIA)==0 || 
						strcmp(trim($row['id_dependencia']), DEPENDENCIA_DIRECCION_EJECUTIVA)==0
					){
									
						$de = '';
						$nombre_dependencia = '';
					}else{$nombre_dependencia = utf8_encode($row['nombre_dependencia']);}
					
					$nombre_dependencia = utf8_decode($nombre_dependencia);
				} else {
					$nombre_dependencia = $row['nombre_dependencia'];
				}
				
				$firmas [$row['perfil_empleado']]['nombre_cargo'] = $nombre_cargo;
				$firmas [$row['perfil_empleado']]['id_dependencia'] = $row['id_dependencia'];
				$firmas [$row['perfil_empleado']]['nombre_dependencia'] = $nombre_dependencia;

				if ( 	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_COORDINADOR, 0, 2))==0 && 
						strcmp($firmas [$row['perfil_empleado']]['id_dependencia'],'450')==0 ) {
					//$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = utf8_decode("Coordinadora de Ordenación de Pagos");
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = utf8_decode("Coordinador(a) de la ").$nombre_dependencia;
				} else if ( 	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_JEFE, 0, 2))==0 && 
								strcmp($firmas [$row['perfil_empleado']]['id_dependencia'],'450')==0 ) {
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = utf8_decode("Jefe de Control de Gastos y Contabilidad");
				} else if ( 	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_TESORERO, 0, 2))==0 && 
								strcmp($firmas [$row['perfil_empleado']]['id_dependencia'],'450')==0 ) {
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = utf8_decode("Tesorero(a) de la Jefatura de Finanzas");
					
				} else if ( 	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_DIRECTOR, 0, 2))==0 && 
								strcmp($firmas [$row['perfil_empleado']]['id_dependencia'],'705')==0 ) {
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = utf8_decode("Director de Despacho");
					
				}else if ( 	strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_JEFE, 0, 2))==0 || 
								strcmp($firmas [$row['perfil_empleado']]['id_cargo'],substr(PERFIL_COORDINADOR, 0, 2))==0 ) {
					$nombre_dependencia = substr($nombre_dependencia, strpos($nombre_dependencia, $de), strlen($nombre_dependencia) - (strpos($nombre_dependencia, $de)) );
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = $nombre_cargo.$nombre_dependencia;
				} else {
					$firmas [$row['perfil_empleado']]['nombre_cargo_dependencia'] = $nombre_cargo.$de.$nombre_dependencia;
				}
			}
		}
		
		// Si no hay una persona a cargo de de Dirección de presupuesto y control,
		// se busca la persona a cargo de la jefatura de presupuesto y control
		if (in_array(PERFIL_DIRECTOR_PRESUPUESTO, $perfiles) && !array_key_exists(PERFIL_DIRECTOR_PRESUPUESTO, $firmas))
		{
			$query = self::GetQueryFirmaByPerfiles(array(PERFIL_JEFE_PRESUPUESTO));
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false) {
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$nombre_dependencia = "Planificación, Presupuesto y Control";
					
					$firmas[PERFIL_DIRECTOR_PRESUPUESTO] = array 
					(
						"perfil_empleado" => utf8_decode(PERFIL_DIRECTOR_PRESUPUESTO),
						"cedula_empleado" => $row['cedula_empleado'],
						"nombre_empleado" => mb_convert_case($row['nombre_empleado'], MB_CASE_TITLE, "ISO-8859-1"),
						//"nombre_empleado" => utf8_decode("José Luis Hurtado"),
						"id_cargo" => utf8_decode("46"),
						"nombre_cargo" => $row['nombre_cargo'],
						//"nombre_cargo" => utf8_decode("Director"),
						"id_dependencia" => $row['id_dependencia'],
						"nombre_dependencia" => utf8_decode($nombre_dependencia),
						"nombre_cargo_dependencia" => utf8_decode($row['nombre_cargo'] . " de " . $nombre_dependencia)
						//"nombre_cargo_dependencia" => utf8_decode("Director" . " de " . $nombre_dependencia)
					);
				}
			}
		}
				
		// Si no hay una persona a cargo de la Dirección de Talento Humano,
		// se coloca el cargo y no se coloca ningún nombre de persona encargada de la gerencia.
		
		if (in_array(PERFIL_DIRECTOR_TALENTO_HUMANO, $perfiles) && !array_key_exists(PERFIL_DIRECTOR_TALENTO_HUMANO, $firmas))
		{
					$nombre_dependencia = "Talento Humano";
						
					$firmas[PERFIL_DIRECTOR_TALENTO_HUMANO] = array
					(
							"perfil_empleado" => utf8_decode(PERFIL_DIRECTOR_TALENTO_HUMANO),
							//"cedula_empleado" => $row['cedula_empleado'],
							"cedula_empleado" => '',
							//"nombre_empleado" => mb_convert_case($row['nombre_empleado'], MB_CASE_TITLE, "ISO-8859-1"),
							"nombre_empleado" => '',
							"id_cargo" => utf8_decode("46"),
							//"nombre_cargo" => $row['nombre_cargo'],
							"nombre_cargo" => utf8_decode("Directora (E)"),
							//"id_dependencia" => $row['id_dependencia'],
							"id_dependencia" => 500,
							"nombre_dependencia" => utf8_decode($nombre_dependencia),
							//"nombre_cargo_dependencia" => utf8_decode($row['nombre_cargo'] . " de " . $nombre_dependencia)
							"nombre_cargo_dependencia" => utf8_decode("Directora (E)" . " de " . $nombre_dependencia)
					);
				
		}
		
		// Si no hay una persona a cargo de la Dirección de Talento Humano,
		// se coloca el cargo y no se coloca ningún nombre de persona encargada de la gerencia.
		
		if (in_array(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, $perfiles) && !array_key_exists(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS, $firmas))
		{
			$nombre_dependencia = "Gestión Administrativa y Financiera";
		
			$firmas[PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS] = array
			(
					"perfil_empleado" => utf8_decode(PERFIL_DIRECTOR_ADMINISTRACION_FINANZAS),
					//"cedula_empleado" => $row['cedula_empleado'],
					"cedula_empleado" => '',
					//"nombre_empleado" => mb_convert_case($row['nombre_empleado'], MB_CASE_TITLE, "ISO-8859-1"),
					"nombre_empleado" => '',
					"id_cargo" => utf8_decode("46"),
					//"nombre_cargo" => $row['nombre_cargo'],
					"nombre_cargo" => utf8_decode("Director"),
					//"id_dependencia" => $row['id_dependencia'],
					"id_dependencia" => 450,
					"nombre_dependencia" => utf8_decode($nombre_dependencia),
					//"nombre_cargo_dependencia" => utf8_decode($row['nombre_cargo'] . " de " . $nombre_dependencia)
					"nombre_cargo_dependencia" => utf8_decode("Director" . " de " . $nombre_dependencia)
			);
		
		}
		/**  QUITAR NOMBRE Y  CEDULA DEL DIRECTOR DE PRESUPUESTO*/
		if (in_array(PERFIL_DIRECTOR_PRESUPUESTO, $perfiles) && array_key_exists(PERFIL_DIRECTOR_PRESUPUESTO, $firmas))
		{
			$firmas[PERFIL_DIRECTOR_PRESUPUESTO]["cedula_empleado"] = "";
			$firmas[PERFIL_DIRECTOR_PRESUPUESTO]["nombre_empleado"] = "";
		}
		
		/**  QUITAR NOMBRE Y  CEDULA DEL COORDINADOR DE COMPRAS DE FIRMAS*/
		/*if (in_array(PERFIL_COORDINADOR_COMPRAS, $perfiles) && array_key_exists(PERFIL_COORDINADOR_COMPRAS, $firmas))
		{
			$firmas[PERFIL_COORDINADOR_COMPRAS]["cedula_empleado"] = "";
			$firmas[PERFIL_COORDINADOR_COMPRAS]["nombre_empleado"] = "";
		}*/
		
	/**  QUITAR NOMBRE Y  CEDULA DEL GERENTE DE TECNOLOGIA DE FIRMAS*/
		/*if (in_array(PERFIL_GERENTE_TECNOLOGIA, $perfiles) && array_key_exists(PERFIL_GERENTE_TECNOLOGIA, $firmas))
		{
					$firmas[PERFIL_GERENTE_TECNOLOGIA]["cedula_empleado"] = "";
					$firmas[PERFIL_GERENTE_TECNOLOGIA]["nombre_empleado"] = "";
		}*/
		
	/* QUITAR NOMBRE Y  CEDULA DEL JEFE DE BIENES */
		if (in_array(PERFIL_JEFE_BIENES, $perfiles) && array_key_exists(PERFIL_JEFE_BIENES, $firmas))
		{
			$firmas[PERFIL_JEFE_BIENES]["cedula_empleado"] = "";
			$firmas[PERFIL_JEFE_BIENES]["nombre_empleado"] = "";
		}
		//error_log(print_r($firmas,true));
		return $firmas;
		
	}
	
	private static function GetQueryFirmaByPerfiles ($perfiles) 
	{
		return "
			SELECT
				c.carg_fundacion||d.depe_id AS perfil_empleado,
				e.empl_cedula AS cedula_empleado, 
				e.empl_nombres||' '||e.empl_apellidos AS nombre_empleado, 
				c.carg_fundacion AS id_cargo,
				c.carg_nombre AS nombre_cargo,
				d.depe_id AS id_dependencia,
				d.depe_nombre AS nombre_dependencia
			FROM
				sai_empleado e
				INNER JOIN sai_cargo c ON (c.carg_fundacion = e.carg_fundacion)
				INNER JOIN sai_dependenci d ON (d.depe_id = e.depe_cosige)
			WHERE
				c.carg_fundacion||d.depe_id in ('".implode("','",$perfiles)."') AND 
				e.esta_id=1
		";
	}
}