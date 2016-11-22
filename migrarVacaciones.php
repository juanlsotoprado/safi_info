<?php
/****************PARA CORRERLO SE DEBE DESCOMENTAR ESTO*****************/
//$conexionSigefirrhh=pg_pconnect("host=150.188.85.30 port=5432 dbname=sigefirrhh user=sigefirrhh password=s!g3f!rrhh");
/***********************************************************************/

pg_set_client_encoding($conexionSigefirrhh, "ISO-8859-1");

/****************VARIA DE ACUERDO AL NOMBRE DEL ARCHIVO .CSV*****************/
if (($handle = fopen("/home/pedro/Desktop/vacaciones.csv", "r")) !== FALSE) {
/****************************************************************************/

	/****************CANTIDAD DE CAMPOS A LEER*************************/
	$tamanoRegistros = 12;
	/******************************************************************/
	
	$query = "SELECT MAX(id_vacacion) AS id_vacacion FROM vacacion";
    $resultado = pg_exec($conexionSigefirrhh, $query);
    if($row=pg_fetch_array($resultado)){
    	$idVacacion = $row["id_vacacion"]+1;
	    while (($data = fgetcsv($handle, 1000, ",","\"")) !== FALSE) {
	    	$cedula = $data[1];//											CEDULA
	    	$nombre = utf8_decode($data[2]);//								NOMBRE
	    	$fechaInicio = substr($data[3], 0, -4);//						FECHA INICIO
	    	$idTipoPersonal = (($data[4]=='"Facilitador"')?"11":"44");//	TIPO DE PERSONAL
	    	$anio = substr($data[3], 6);//									AÑO
	    	$i = 5;//														A PARTIR DE ESTE CAMPO SE INICIAN LOS PERIODOS DE VACACIONES
	    	$anioInicio = 2004;//											AÑO DE INICIO
	    	$query = "SELECT p.id_personal FROM personal p WHERE p.cedula = '".$cedula."'";
	    	$resultado = pg_exec($conexionSigefirrhh, $query);
	    	if($row=pg_fetch_array($resultado)){
		    	$idPersonal = $row["id_personal"];
		    	while($i<$tamanoRegistros){
		    		if($data[$i]!=0 && $anioInicio==$anio){
		    			$query = 	"SELECT	COUNT(v.id_vacacion) AS contador_vacacion FROM vacacion v
			    					 WHERE 
			    						v.id_personal = ".$idPersonal." AND
			    						v.id_tipo_personal = ".$idTipoPersonal." AND
			    						v.anio = ".$anio."";
			    		$resultadoConsultaVacacion = pg_exec($conexionSigefirrhh, $query);
			    		$row=pg_fetch_array($resultadoConsultaVacacion);
			    		if($row["contador_vacacion"]==0){
				    		$query = 	"INSERT INTO vacacion 
				    					(	id_vacacion,
				    						id_personal,
				    						id_tipo_personal,
				    						tipo_vacacion,
				    						anio,
				    						dias_disfrute,
				    						dias_pendientes,
				    						fecha_inicio,
				    						suspendida,
				    						observaciones)
				    					VALUES 
				    					(	".$idVacacion.",".
				    						$idPersonal.",".
				    						$idTipoPersonal.",".
				    						"'P',".
				    						$anio.",".
				    						"0,".
				    						$data[$i].",".
				    						"to_date('".$fechaInicio.($anio+1)."','DD/MM/YYYY'),".
				    						"'N',".
				    						"'PERIODO ".$anio."-".($anio+1)."'".
				    					")";
				    		$resultadoInsertar = pg_exec($conexionSigefirrhh, $query);
				    		if($resultadoInsertar){
					    		echo "<span style='color: blue;'>Se insert&oacute; el registro de vacaci&oacute;n (a&ntilde;o ".$anio.", ".$data[$i]." d&iacute;as) para el empleado ".$nombre." con c&eacute;dula ".$cedula."</span>.<br/>";
					    		$idVacacion++;
				    		}else{
					    		echo "<span style='color: red;'>".$query."<br/>Error al insertar el registro de vacaci&oacute;n (a&ntilde;o ".$anio.", ".$data[$i]." d&iacute;as) para el empleado ".$nombre." con c&eacute;dula ".$cedula.". Error en el registro.</span><br/>";
				    		}
			    		}else{
			    			echo "<span style='color: red;'>Error al insertar el registro de vacaci&oacute;n (a&ntilde;o ".$anio.", ".$data[$i]." d&iacute;as) para el empleado ".$nombre." con c&eacute;dula ".$cedula.". El per&iacute;odo ".$anio." ya est&aacute; registrado para este empleado.</span><br/>";
			    		}
		    		}
		    		if($anioInicio==$anio){
		    			$anio++;
		    		}
		    		$anioInicio++;
		    		$i++;
		    	}
	    	}else{
	    		echo "<span style='color: red;'>El empleado ".$nombre." con c&eacute;dula ".$cedula." no est&aacute; registrado en el sistema</span><br/>";
	    	}
		}
    }else{
    	echo "<span style='color: red;'>No se pudo obtener el id de vacaci&oacute;n</span><br/>";
    }
	fclose($handle);
}
pg_close($conexionSigefirrhh);
?>