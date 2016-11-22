<?php
require("includes/conexion.php");
/***********************************************************************/
$conexionSafi = $conexion;
$conexionSigefirrhh=pg_pconnect("host=150.188.85.30 port=5432 dbname=sigefirrhh user=sigefirrhh password=s!g3f!rrhh");
/***********************************************************************/
pg_set_client_encoding($conexionSigefirrhh, "ISO-8859-1");

/*Sigefirrhh*/
//$tipoPersonal = 61 => Alfabetizadores
//$tipoPersonal = 71 => Becarios
$tipoPersonal = "1,10,11,28,44,53,61,71";
$estatusActivo = "A";
$estatusEgresado = "E";
//$formaPagoCheque = "2";
//t.forma_pago = '".$formaPagoCheque."' AND
/*Fin Sigefirrhh*/

/*Safi*/
$honorariosProfesionales = "53";
$pensionados = "28";
$estadoActivo = "1";
$estadoInactivo = "2";
$exceptoEmpleados = "(
	'14454357', -- Jonas Reyes
	'11741240',
	'12375524',
	'13068368',
	'14270225',
	'6873214',
	'2981419',
	'12399171',
	'11226591',
	'16023946',
	'6088628',
	'11879929', --Rudexy Riveros
	'14548643',
	'11201729',
	'1192732',
	'12952064',
	'13066811', -- Lionel Benitez
	'11550602', -- Zuhail Chirinos
	'10826205',
	'13536258',
	'14045453',
	'13804821',
	'17376786',
	'19163769',
	'12401110',
	'19578975',
	'13292728',
	'14595993',
	'13528742',
	'13310571',
	'21413288',
	'19445890',
	'15316498',
	'14196354',
	'16433906',
	'18364121',
	'13560998',
	'14685677',
	'6873214',
	'17692785',
	'17455715',
	'17664605',
	'12070781',
	'18113421',
	'14871853',  -- German Manrique --
	'12625854',  -- Anibal Ghanem
	'13536258',  -- Patricia Zambrano
	'17060167',  -- Edgar Escobar
	'12401110',	 -- Maria Rossel
	'13649530',	 -- Jesús Enrique Castro
	'6342908',    -- Yaritza Mata (Auditor)	 
	'16619792',   -- Raymond David Martinez Canache  (jefe temparal de bienes y servicios)
	'18619576',	-- Vladimir Ernesto Guarache Angulo
	'6821627', -- Nury Fasanella
	'18113421', -- Jesus Mujica
	'13482459', -- Jenny Cristina Mejias Gallardo - Consultora Jurídica
	'19614443', -- Yorwuel Parada
	'18814361', -- Secretaria Ejecutiva (Presidencia) Aidalin Godoy
	'12210563',
	'12417995', -- Ervin Yarabi Fernandez Lara, Jefe de la Coordinación General de la Red Infocentro
	--CAMBIO DE NOMINA USUARIO QUE INACTIVARON (TEMPORAL)
	'6144216',
	'17856724',
	--FIN CAMBIO DE NOMINA USUARIO QUE INACTIVARON (TEMPORAL)
	'12210563', -- jefa de compras
	'10394989', -- Yumila Bruces - Jefa de Bienestar y desarrollo de talento humano
	'20669801', -- Aida Ibarra, Directora General del Despacho
	'12653148', --  	PASCACIO PINO
	'18461923', --HAYSKER DURLEY LUGO DIAZ tesorera
	'9958127', -- Luis Machado - Jefe de Jefatura de transporte
	'16581753', -- GUSTAVO ADOLFO DIAZ AULAR
	'2141905' --maria teresa brea	
)";
//
/*Fin Safi*/

/*
				INITCAP(TRIM(p.primer_nombre||' '||COALESCE(p.segundo_nombre,''))) AS nombres, 
     			INITCAP(TRIM(p.primer_apellido||' '||COALESCE(p.segundo_apellido,''))) AS apellidos, 
*/
/*INGRESOS*/
$query = 	"
        	SELECT 
        		TRIM(p.cedula) AS cedula,
        		TRIM(p.nacionalidad) AS nacionalidad,  
        		TRIM(p.primer_nombre) AS primer_nombre,
        		TRIM(COALESCE(p.segundo_nombre,'')) AS segundo_nombre, 
        		TRIM(p.primer_apellido) AS primer_apellido, 
        		TRIM(COALESCE(p.segundo_apellido,'')) AS segundo_apellido,
        		LOWER(TRIM(p.email)) AS email, 
        		dss.id_dependencia_safi AS dependencia, 
        		css.id_cargo_safi AS cargo,
        		t.id_tipo_personal AS tipo_personal 
        	FROM 
        		personal p
        		INNER JOIN trabajador t ON (p.id_personal = t.id_personal)
        		LEFT OUTER JOIN cargo_safi_sigefirrhh css ON (t.id_cargo = css.id_cargo_sigefirrhh)
        		LEFT OUTER JOIN dependencia_sigefirrhh_safi dss ON (t.id_dependencia = dss.id_dependencia_sigefirrhh) 
        	WHERE 
        		t.id_tipo_personal IN (".$tipoPersonal.") AND 
        		t.estatus = '".$estatusActivo."' AND
        		t.cedula NOT IN ".$exceptoEmpleados."
        	GROUP BY 
        		p.cedula,
        		p.nacionalidad,  
        		p.primer_nombre,
        		p.segundo_nombre, 
        		p.primer_apellido,
        		p.segundo_apellido, 
        		p.email, 
        		dss.id_dependencia_safi, 
        		css.id_cargo_safi,
        		t.id_tipo_personal 
        	ORDER BY p.primer_apellido, p.segundo_apellido, p.primer_nombre, p.segundo_nombre";
$resultado = pg_exec($conexionSigefirrhh, $query);
$i = 1;
$cedulasEmpleado = "";
$cedulasBeneficiario = "";
echo "<center><span style='color: blue;'>INGRESOS Y ACTUALIZACI&Oacute;N DE DATOS</span></center><br/>";
while($row=pg_fetch_array($resultado)){
	$apellidos = trim(ucfirst(mb_strtolower($row["primer_apellido"], 'ISO-8859-1'))." ".ucfirst(mb_strtolower($row["segundo_apellido"], 'ISO-8859-1')));
	$nombres = trim(ucfirst(mb_strtolower($row["primer_nombre"], 'ISO-8859-1'))." ".ucfirst(mb_strtolower($row["segundo_nombre"], 'ISO-8859-1')));
	
	if($row["tipo_personal"]==$honorariosProfesionales || $row["tipo_personal"]==$pensionados){
		$cedulasEmpleado .= "'".$row["cedula"]."',";
		$query = 	"
        			SELECT 
        				vb.benvi_cedula AS cedula
        			FROM sai_viat_benef vb 
        			WHERE 
        				benvi_cedula LIKE '".$row["cedula"]."'";
		$resultadoCedula = pg_exec($conexionSafi, $query);
		if($row["dependencia"] && $row["dependencia"]!=""){
			$dependencia = $row["dependencia"];
		}else{
			$dependencia = "500";
		}
		if($resultadoCedula !== false && $rowCedula=pg_fetch_array($resultadoCedula)){
			echo "<span style='color: red;'>".$i.". El beneficiario ".$apellidos.", ".$nombres." de c&eacute;dula ".$row["cedula"]." ya est&aacute; registrado en el sistema SAFI.</span><br/>";
			$query = 	"UPDATE sai_viat_benef SET
							nacionalidad = '".$row["nacionalidad"]."',
							benvi_nombres = '".$nombres."',
							benvi_apellidos = '".$apellidos."',
							depe_id = '".$dependencia."',
							tipo = '".(($row["tipo_personal"]==$honorariosProfesionales)?"HP":"PENSIONADO")."',
							benvi_esta_id = ".$estadoActivo."
						WHERE 
							benvi_cedula = '".$row["cedula"]."'";
			$resultadoUpdate = pg_exec($conexionSafi, $query);
			echo "<span style='color: blue;'>".$i.". Se activ&oacute; y se actualiz&oacute; la nacionalidad, el nombre, apellido, dependencia y tipo del beneficiario ".$apellidos.", ".$nombres." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";
		}else{
			$query = 	"INSERT INTO sai_viat_benef	(
													nacionalidad,
													benvi_cedula,
													benvi_nombres,
													benvi_apellidos,
													depe_id,
													tipo,
													benvi_esta_id) VALUES 
													(
													'".$row["nacionalidad"]."',
													'".$row["cedula"]."',
													'".$nombres."',
													'".$apellidos."',
													'".$dependencia."',
													'".(($row["tipo_personal"]==$honorariosProfesionales)?"HP":"PENSIONADO")."',
													".$estadoActivo.");";
			$resultadoInsert = pg_exec($conexionSafi, $query);
			echo "<span style='color: blue;'>".$i.". Se insert&oacute; y se activ&oacute; el beneficiario ".ucfirst($row["apellidos"]).", ".ucfirst($row["nombres"])." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";		
		}
	}else{
		$cedulasBeneficiario .= "'".$row["cedula"]."',";
		$query = 	"
        			SELECT 
        				e.empl_cedula AS cedula
        			FROM sai_empleado e 
        			WHERE 
        				empl_cedula LIKE '".$row["cedula"]."'";
		$resultadoCedula = pg_exec($conexionSafi, $query);
		if($row["dependencia"] && $row["dependencia"]!=""){
			$dependencia = $row["dependencia"];
		}else{
			$dependencia = "500";
		}
		if($row["email"] && $row["email"]!=""){
			$email = $row["email"];
		}else{
			$email = "''";
		}
		if($row["cargo"] && $row["cargo"]!=""){
			$cargo = $row["cargo"];
		}else{
			$cargo = "37";
		}
		if(strlen($cargo)>2){
			$cargo = substr($cargo,0,2);
		}
		$usua_login = 'saiadmin';
		if($resultadoCedula !== false && $rowCedula=pg_fetch_array($resultadoCedula)){
			echo "<span style='color: red;'>".$i.". El empleado ".$apellidos.", ".$nombres." de c&eacute;dula ".$row["cedula"]." ya est&aacute; registrado en el sistema SAFI.</span><br/>";
			$query = 	"UPDATE sai_empleado SET
							empl_nombres = '".$nombres."',
							empl_apellidos = '".$apellidos."',
							nacionalidad = '".$row["nacionalidad"]."',
							depe_cosige = '".$dependencia."',
							carg_fundacion = '".$cargo."',
							usua_login = '".$usua_login."',
							esta_id = ".$estadoActivo."
						WHERE 
							empl_cedula = '".$row["cedula"]."'";
			$resultadoUpdate = pg_exec($conexionSafi, $query);
			echo "<span style='color: blue;'>".$i.". Se activ&oacute; y se actualiz&oacute; la nacionalidad, el nombre, apellido, email, dependencia y cargo del empleado ".$apellidos.", ".$nombres." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";
		}else{
			$query = 	"INSERT INTO sai_empleado	(
													empl_cedula,
													empl_nombres,
													empl_apellidos,
													nacionalidad,
													empl_email,
													depe_cosige,
													carg_fundacion,
													usua_login,
													esta_id) VALUES 
													(
													'".$row["cedula"]."',
													'".$nombres."',
													'".$apellidos."',
													'".$row["nacionalidad"]."',
													'".$email."',
													'".$dependencia."',
													'".$cargo."',
													'".$usua_login."',
													".$estadoActivo.");";
			$resultadoInsert = pg_exec($conexionSafi, $query);
			echo "<span style='color: blue;'>".$i.". Se activ&oacute; y se insert&oacute; el empleado ".$apellidos.", ".$nombres." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";		
		}
	}
	$i++;
}
if($cedulasEmpleado!=""){
	$cedulasEmpleado = "(".substr($cedulasEmpleado, 0, -1).")";	
	$query = 	"UPDATE sai_empleado SET
					esta_id = ".$estadoInactivo."
				WHERE 
					empl_cedula IN ".$cedulasEmpleado."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
}
if($cedulasBeneficiario!=""){
	$cedulasBeneficiario = "(".substr($cedulasBeneficiario, 0, -1).")";		
	$query = 	"UPDATE sai_viat_benef SET
					benvi_esta_id = ".$estadoInactivo."
				WHERE 
					benvi_cedula IN ".$cedulasBeneficiario."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
}
/*FIN INGRESOS*/

/*EGRESOS*/
//Se quitó esta condición
//t.fecha_egreso BETWEEN TO_DATE('01/01/".date('Y')."','DD/MM/YYYY') AND TO_DATE('".date('d/m/Y')."','DD/MM/YYYY') AND
$query = 	"
        	SELECT 
        		TRIM(t.cedula) AS cedula
        	FROM 
				trabajador t
        	WHERE 
        		t.fecha_egreso IS NOT NULL AND
        		t.estatus = '".$estatusEgresado."' AND 
        		t.cedula NOT IN (SELECT t.cedula FROM trabajador t WHERE t.estatus = 'A') AND
        		t.cedula NOT IN ".$exceptoEmpleados."";
$resultado = pg_exec($conexionSigefirrhh, $query);
$cedulas = "";
while($row=pg_fetch_array($resultado)){
	$cedulas .= "'".$row["cedula"]."',";
}
if($cedulas!=""){
	$cedulas = "(".substr($cedulas, 0, -1).")";	
	echo "<br/><center><span style='color: blue;'>BENEFICIARIOS EGRESADOS</span></center><br/>";
	$i = 1;
	$query = 	"UPDATE sai_viat_benef SET
					benvi_esta_id = ".$estadoInactivo."
				WHERE 
					benvi_cedula IN ".$cedulas."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	$query = 	"SELECT
					benvi_cedula AS cedula,
					benvi_nombres AS nombres,
					benvi_apellidos AS apellidos
				FROM sai_viat_benef
				WHERE 
					benvi_cedula IN ".$cedulas." 
				ORDER BY benvi_apellidos, benvi_nombres";
	$resultado = pg_exec($conexionSafi, $query);
	while($row=pg_fetch_array($resultado)){
		$apellidos = trim(ucfirst(mb_strtolower($row["apellidos"], 'ISO-8859-1')));
		$nombres = trim(ucfirst(mb_strtolower($row["nombres"], 'ISO-8859-1')));	
		echo "<span style='color: blue;'>".$i.". Se ha inactivado el beneficiario ".$apellidos.", ".$nombres." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";
		$i++;
	}
	
	echo "<br/><center><span style='color: blue;'>EMPLEADOS EGRESADOS</span></center><br/>";
	$i = 1;
	$query = 	"UPDATE sai_empleado SET
					esta_id = ".$estadoInactivo."
				WHERE 
					empl_cedula IN ".$cedulas."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	$query = 	"SELECT
					empl_cedula AS cedula,
					empl_nombres AS nombres,
					empl_apellidos AS apellidos
				FROM sai_empleado
				WHERE 
					empl_cedula IN ".$cedulas."
				ORDER BY empl_apellidos, empl_nombres";
	$resultado = pg_exec($conexionSafi, $query);
	while($row=pg_fetch_array($resultado)){
		$apellidos = trim(ucfirst(mb_strtolower($row["apellidos"], 'ISO-8859-1')));
		$nombres = trim(ucfirst(mb_strtolower($row["nombres"], 'ISO-8859-1')));	
		echo "<span style='color: blue;'>".$i.". Se ha inactivado el empleado ".$apellidos.", ".$nombres." con c&eacute;dula ".$row["cedula"]."</span>.<br/>";
		$i++;
	}
	
	$query = 	"UPDATE sai_usuario SET
					usua_activo = false
				WHERE 
					empl_cedula IN ".$cedulas."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	
	$query = 	"UPDATE sai_usua_perfil SET
					uspe_fin = now()
				WHERE 
					usua_login IN ".$cedulas."";
	$resultadoUpdate = pg_exec($conexionSafi, $query);
}
/*FIN EGRESOS*/

/* MIGRAR CUENTAS BANCARIAS DE NOMINA EGRESADOS*/
$query = "
	SELECT 
		TRIM(t.cedula) AS cedula,
		banco.nombre AS banco_nombre,
		t.tipo_cta_nomina,
		t.cuenta_nomina
	FROM 
		trabajador t
		INNER JOIN
		(
			SELECT
				trabajador.id_personal,
				MAX(trabajador.oid) AS maximo_oid
			FROM
				trabajador
			WHERE
				trabajador.id_tipo_personal IN (1,10,11,28,44,53,61,71) AND 
				trabajador.estatus != 'A'
			GROUP BY
				trabajador.id_personal
		) AS filtro ON (t.oid = filtro.maximo_oid)
		LEFT JOIN banco ON (banco.id_banco = t.id_banco_nomina)
	GROUP BY 
		t.cedula,
		banco.nombre,
		t.tipo_cta_nomina,
		t.cuenta_nomina
	ORDER BY
		t.cedula
";

$resultado = pg_exec($conexionSigefirrhh, $query);
$egresados = array();
while($row = pg_fetch_array($resultado))
{
	$egresados[$row['cedula']] = $row;
}
foreach ($egresados AS $egresado)
{
	// Actualizar los datos de la cuenta bancaria en sai_empleado
	$query = "
		UPDATE sai_empleado SET
			banco_nomina = ".($egresado['banco_nombre'] != null && $egresado['banco_nombre'] != '' ? "'".$egresado['banco_nombre']."'" : "NULL").",
			tipo_cuenta_nomina = " . ($egresado['tipo_cta_nomina'] == "C" || $egresado['tipo_cta_nomina'] == "A"
					? "'".$egresado['tipo_cta_nomina']."'" : "NULL") .",
			cuenta_nomina = ".($egresado['cuenta_nomina'] != null && $egresado['cuenta_nomina'] != '' ? "'".$egresado['cuenta_nomina']."'" : "NULL")."
		WHERE
			empl_cedula = '".$egresado['cedula']."'";
	
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	
	if($resultadoUpdate === false){
		echo "Error al actualizar los datos de las cuentas bancarias del personal egresado, sobre la tabla sai_empleado.".
			" Detalles: " . pg_last_error($conexionSafi);
		return;
	}
	
	// Actualizar la cuenta bancaria en sai_viat_benef
	$query = "
		UPDATE sai_viat_benef
			banco_nomina = ".($egresado['banco_nombre'] != null && $egresado['banco_nombre'] != '' ? "'".$egresado['banco_nombre']."'" : "NULL").",
			tipo_cuenta_nomina = " . ($egresado['tipo_cta_nomina'] == "C" || $egresado['tipo_cta_nomina'] == "A"
					? "'".$egresado['tipo_cta_nomina']."'" : "NULL") .",
			cuenta_nomina = ".($egresado['cuenta_nomina'] != null && $egresado['cuenta_nomina'] != '' ? "'".$egresado['cuenta_nomina']."'" : "NULL")."
		WHERE
				benvi_cedula = '".$egresado['cedula']."'";
	
	if($resultadoUpdate === false){
		echo "Error al actualizar los datos de las cuentas bancarias del personal egresado, sobre la tabala sai_viat_benef.".
			" Detalles: " . pg_last_error($conexionSafi);
		
		return;
	}
}
/* FIN DE  MIGRAR CUENTAS BANCARIAS DE NOMINA EGRESADOS*/

/* MIGRAR CUENTAS BANCARIAS DE NOMINA ACTIVOS*/
$query = "
	SELECT 
		TRIM(t.cedula) AS cedula,
		banco.nombre AS banco_nombre,
		t.tipo_cta_nomina,
		t.cuenta_nomina
	FROM 
		trabajador t
		INNER JOIN
		(
			SELECT
				trabajador.id_personal,
				MAX(trabajador.oid) AS maximo_oid
			FROM
				trabajador
			WHERE
				trabajador.id_tipo_personal IN (1,10,11,28,44,53,61,71) AND 
				trabajador.estatus = 'A'
			GROUP BY
				trabajador.id_personal
		) AS filtro ON (t.oid = filtro.maximo_oid)
		LEFT JOIN banco ON (banco.id_banco = t.id_banco_nomina)
	GROUP BY 
		t.cedula,
		banco.nombre,
		t.tipo_cta_nomina,
		t.cuenta_nomina
	ORDER BY
		t.cedula
";

$resultado = pg_exec($conexionSigefirrhh, $query);
$activos = array();
while($row = pg_fetch_array($resultado))
{
	$activos[$row['cedula']] = $row;
}
foreach ($activos AS $activo)
{
	// Actualizar los datos de la cuenta bancaria en sai_empleado
	$query = "
		UPDATE sai_empleado SET
			banco_nomina = ".($activo['banco_nombre'] != null && $activo['banco_nombre'] != '' ? "'".$activo['banco_nombre']."'" : "NULL").",
			tipo_cuenta_nomina = " . ($activo['tipo_cta_nomina'] == "C" || $activo['tipo_cta_nomina'] == "A"
						? "'".$activo['tipo_cta_nomina']."'" : "NULL") .",
			cuenta_nomina = ".($activo['cuenta_nomina'] != null && $activo['cuenta_nomina'] != '' ? "'".$activo['cuenta_nomina']."'" : "NULL")."
		WHERE
			empl_cedula = '".$activo['cedula']."'";
	
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	
	if($resultadoUpdate === false){
		echo "Error al actualizar los datos de las cuentas bancarias del personal activo, sobre la tabla sai_empleado.".
			" Detalles: " . pg_last_error($conexionSafi);
		return;
	}
	
	// Actualizar los datos de la cuenta bancaria en sai_viat_benef
	$query = "
		UPDATE sai_viat_benef SET
			banco_nomina = ".($activo['banco_nombre'] != null && $activo['banco_nombre'] != '' ? "'".$activo['banco_nombre']."'" : "NULL").",
			tipo_cuenta_nomina = " . ($activo['tipo_cta_nomina'] == "C" || $activo['tipo_cta_nomina'] == "A"
						? "'".$activo['tipo_cta_nomina']."'" : "NULL") .",
			cuenta_nomina = ".($activo['cuenta_nomina'] != null && $activo['cuenta_nomina'] != '' ? "'".$activo['cuenta_nomina']."'" : "NULL")."
		WHERE
			benvi_cedula = '".$activo['cedula']."'";
	
	$resultadoUpdate = pg_exec($conexionSafi, $query);
	
	if($resultadoUpdate === false){
		echo "Error al actualizar los datos de las cuentas bancarias del personal activo, sobre la tabla sai_viat_benef.".
			" Detalles: " . pg_last_error($conexionSafi);
		return;
	}
	
}

/* FIN DE MIGRAR CUENTAS BANCARIAS DE NOMINA ACTIVOS*/

/*
banco_nomina character varying(250),
tipo_cuenta_nomina character varying(1),
cuenta_nomina character varying(20),
* */

pg_close($conexionSafi);
pg_close($conexionSigefirrhh);
?>
