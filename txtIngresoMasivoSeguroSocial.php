<?php
	/*
	 Nacionalidad ** (tipo texto, 1 carácter, ver detalle)
	 Cédula ** (tipo numérico, 9 caracteres)
	 Apellidos y Nombres ** (tipo texto, sin restricción)
	 Fecha de Ingreso ** (tipo fecha, dd/mm/aaaa)
	 Salario semanal ** (tipo monto: 000.00)
	 Tipo de trabajador ** (tipo numérico, 1 carácter, ver detalle)
	 Ocupación ** (tipo numérico, ver listado)
	 Condición trabajador ** (tipo texto, 1 carácter, ver detalle)
	 Habilidad motriz ** (tipo texto, 1 carácter, ver detalle)
	 Estado** (tipo numérico, ver listado)
	 Municipio** (tipo numérico, ver listado)
	 Parroquia** (tipo numérico, ver listado)
	 Dirección de habitación (tipo texto, sin restricción)
	 Teléfono habitación (tipo numérico, ver detalle)
	 Teléfono móvil ** (tipo numérico, ver detalle)
	 Correo electrónico ** (tipo correo, ver detalle)
	 */
/***********************************************************************/
$conexionSigefirrhh=pg_pconnect("host=150.188.85.30 port=5432 dbname=sigefirrhh user=sigefirrhh password=s!g3f!rrhh");
/***********************************************************************/
pg_set_client_encoding($conexionSigefirrhh, "ISO-8859-1");

/*Sigefirrhh*/
$cedulas = "1,10,11,28,44,53";
//$tipoPersonal = "1,10,11,28,44,53";//t.id_tipo_personal IN (".$tipoPersonal.") AND
$estatusActivo = "A";
/*Fin Sigefirrhh*/

/*INGRESOS*/
$query = 	"
        	SELECT 
        		UPPER(TRIM(p.nacionalidad)) AS nacionalidad,  
        		TRIM(p.cedula) AS cedula,
        		UPPER(TRIM(p.primer_apellido||' '||COALESCE(p.segundo_apellido,''))) AS apellidos, 
        		UPPER(TRIM(p.primer_nombre||' '|| COALESCE(p.segundo_nombre,''))) AS nombres,
        		TO_CHAR(t.fecha_ingreso,'DD/MM/YYYY') AS fecha_ingreso,
        		0 AS salario_semanal,
        		0 AS tipo_trabajador,
        		0 AS ocupacion,
        		0 AS condicion_trabajador,
        		0 AS habilidad_motriz,
        		0 AS estado,
        		0 AS municipio,
        		0 AS parroquia,
        		0 AS direccion_habitacion,--no obligatorio
        		0 AS telefono_habitacion,--no obligatorio
        		0 AS telefono_movil,
        		UPPER(TRIM(p.email)) AS email
        	FROM 
        		personal p
        		INNER JOIN trabajador t ON (p.id_personal = t.id_personal)
        	WHERE 
        		--p.cedula IN (".$cedulas.") AND 
        		t.estatus = '".$estatusActivo."'
        	ORDER BY p.nacionalidad, p.cedula";
$resultado = pg_exec($conexionSigefirrhh, $query);
$empleados = "";
while($row=pg_fetch_array($resultado)){
	$empleados .= $row["nacionalidad"].";";
	$empleados .= $row["cedula"].";";
	$empleados .= $row["apellidos"]." ".$row["nombres"].";";
	$empleados .= $row["fecha_ingreso"].";";
	$empleados .= $row["salario_semanal"].";";
	$empleados .= $row["tipo_trabajador"].";";
	$empleados .= $row["ocupacion"].";";
	$empleados .= $row["condicion_trabajador"].";";
	$empleados .= $row["habilidad_motriz"].";";
	$empleados .= $row["estado"].";";
	$empleados .= $row["municipio"].";";
	$empleados .= $row["parroquia"].";";
	$empleados .= $row["direccion_habitacion"].";";
	$empleados .= $row["telefono_habitacion"].";";
	$empleados .= $row["telefono_movil"].";";
	$empleados .= $row["email"].";";
	$empleados .= "<br/>";
}
echo $empleados;
pg_close($conexionSigefirrhh);
?>