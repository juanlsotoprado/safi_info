<?php

class SafiModeloGeneral
{
	public static function GetAllProyectosAccionesGenerales()
	{
		$datos = array();
		
		$dependencia = $_SESSION['user_depe_id'];
		$anno_pres=$_SESSION['an_o_presupuesto'];
		
		$query = "
			SELECT
				*
			FROM
			(
		 		SELECT
					sp.proy_id as id_proyecto_accion,
					sp.proy_titulo as nombre,
					'1' as tipo
				FROM sai_proyecto sp
				WHERE
					sp.esta_id <> 13 AND
					sp.pre_anno = " . $anno_pres . " AND
					sp.proy_id IN
						(
							SELECT spae.proy_id
							FROM sai_proy_a_esp spae, sai_forma_1125 sf1125
							WHERE
								spae.pres_anno = sf1125.pres_anno AND
								spae.proy_id = sf1125.form_id_p_ac AND
								sf1125.form_id_aesp = spae.paes_id AND
								spae.pres_anno = ".$anno_pres . " AND
								sf1125.pres_anno = ".$anno_pres . "
								--AND position('" . $dependencia . "' IN sf1125.depe_id) > 0
		";					
			
		if($dependencia == '550') {
			$query.= "
				AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '111721' AND sf1125.form_id_aesp = '111721 E-1') OR (sf1125.form_id_p_ac = '114254' AND sf1125.form_id_aesp = '114254 E-2') OR (sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2') OR (sf1125.form_id_p_ac = '117659' AND sf1125.form_id_aesp = '117659 A-1')))
					)
			";
		} else if($dependencia=='600'){
			$query.= "
				AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2') OR (sf1125.form_id_p_ac = '117580' AND sf1125.form_id_aesp = '117580 B-3') ))
					)
			";
		} else if($dependencia=='500'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";	
		} else if($dependencia=='250'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='700'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='650'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else{
		   ($dependencia !="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")? $query .= "AND sf1125.depe_cosige = '".$dependencia."'":"";
		}
	
		$query .= "
						)
				UNION
				SELECT
					sac.acce_id as id_proyecto_accion,
					sac.acce_denom as nombre,
					'0' as tipo
				FROM sai_ac_central sac
				WHERE
					sac.esta_id <> 13 AND
					sac.pres_anno = ".$anno_pres." AND
					sac.acce_id IN
						(
							SELECT spae.acce_id
							FROM sai_acce_esp spae, sai_forma_1125 sf1125
							WHERE
								spae.pres_anno = sf1125.pres_anno AND
								spae.pres_anno = " . $anno_pres . " AND
								sf1125.form_id_aesp = spae.aces_id AND
								sf1125.form_id_p_ac = spae.acce_id AND
								sf1125.pres_anno = " . $anno_pres . "
								--AND position('" . $dependencia . "' in sf1125.depe_id) > 0
								".(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452") ?
								" AND (sf1125.depe_cosige = '".$dependencia."' OR (sf1125.form_tipo = 0::BIT AND sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2' ))"
								: "")."
						)
			) as s
			ORDER BY s.tipo DESC, s.nombre ASC
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$datos[] = $row;
		}

		return $datos;
	}

	public static function GetAllAccionesEspecificasCorta($params)
	{

		$datos = array();
		$condicion = '';
		$anno_pres= $params['agnoPcta'];
		//$anno_pres = 2012;

		$cargo = substr($_SESSION['user_perfil_id'],0,2);

		if(
		($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
		($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
		($cargo !== substr(PERFIL_PRESIDENTE,0,2))
		){

			if ($params['dependencia'] != '' && $params['dependencia'] != null) {
				$condicion = " AND position('550' in f.depe_cosige) > 0" ;
			}


		}	

		$query = "
			SELECT
				'1' AS tipo,
				pe.paes_id AS id_especifica,
				p.proy_id AS id_proy_accion,
				pe.centro_gestor||'/'||pe.centro_costo AS centro,
				COALESCE(pe.proy_id,'') || '/' || COALESCE(pe.paes_id,'')  AS id_proy_acc,
				pe.paes_nombre AS nombre,
				substr(p.proy_titulo, 0, 12) AS nompre_proyacc 
			
			FROM
				sai_proyecto p
			INNER JOIN sai_proy_a_esp pe ON (p.proy_id = pe.proy_id AND p.pre_anno = pe.pres_anno)
			INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = p.proy_id AND f.form_id_aesp = pe.paes_id AND f.pres_anno = p.pre_anno)
			WHERE pe.pres_anno = ".$anno_pres." AND
			f.pres_anno = ".$anno_pres."
					 
			UNION
			SELECT
				'0' AS tipo,
				ae.aces_id AS id_especifica,
				a.acce_id AS id_proy_accion,
				ae.centro_gestor||'/'||ae.centro_costo AS centro,
				COALESCE(ae.acce_id,'') || '/' || COALESCE(ae.aces_id,'') AS id_proy_acc,
				ae.aces_nombre AS nombre,
				substr(a.acce_denom,0,12) AS nompre_proyacc
				
					
			FROM
				sai_ac_central a
			INNER JOIN sai_acce_esp ae ON (a.acce_id = ae.acce_id AND a.pres_anno = ae.pres_anno)
			INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = a.acce_id AND f.form_id_aesp = aces_id AND f.pres_anno = a.pres_anno)
			WHERE a.pres_anno = ".$anno_pres."  AND
			f.pres_anno = ".$anno_pres;


		//error_log(print_r($query,true));
			
		$result = $GLOBALS['SafiClassDb']->Query($query);

		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$datos[] = $row;
		}

		return $datos;


	}

	public static function GetAllAccionesEspecificas()
	{
		$datos = array();

		$dependencia = $_SESSION['user_depe_id'];
		$anno_pres=$_SESSION['an_o_presupuesto'];

		$query = "
			SELECT
				id_proyecto_accion,
				tipo,
				id_accion_especifica,
				nombre,
				centro_gestor,
				centro_costo
			FROM
			(
				SELECT
					spae.proy_id as id_proyecto_accion,
					'1' as tipo,
					spae.paes_id as id_accion_especifica,
					spae.paes_nombre as nombre,
					spae.centro_gestor,
					spae.centro_costo
				FROM sai_proy_a_esp spae, sai_forma_1125 sf1125
				WHERE
					spae.pres_anno = sf1125.pres_anno AND
					spae.proy_id = sf1125.form_id_p_ac AND
					sf1125.form_id_aesp = spae.paes_id AND
					sf1125.pres_anno = " . $anno_pres . "
					--AND position('" . $dependencia . "' in sf1125.depe_id) > 0
		";
		
		if($dependencia == '550') {
			$query.= "
				AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '111721' AND sf1125.form_id_aesp = '111721 E-1') OR (sf1125.form_id_p_ac = '114254' AND sf1125.form_id_aesp = '114254 E-2') OR (sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2') OR (sf1125.form_id_p_ac = '117659' AND sf1125.form_id_aesp = '117659 A-1')))
					)
			";
		} else if($dependencia=='600'){
			$query.= "
				AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2') OR (sf1125.form_id_p_ac = '117580' AND sf1125.form_id_aesp = '117580 B-3') ))
					)
			";
		} else if($dependencia=='500'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";	
		} else if($dependencia=='250'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='700'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='650'){
			$query.= "AND 
					(
						(sf1125.depe_cosige = '".$dependencia."') OR 
						(sf1125.form_tipo = 1::BIT AND ((sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else{
		   ($dependencia !="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")? $query .= "AND sf1125.depe_cosige = '".$dependencia."'":"";
		}
		
		$query .= "
				UNION
				SELECT
					sae.acce_id as id_proyecto_accion,
					'0' as tipo,
					sae.aces_id as id_accion_especifica,
					sae.aces_nombre as nombre,
					sae.centro_gestor,
					sae.centro_costo
				FROM sai_acce_esp sae, sai_forma_1125 sf1125
				WHERE
					sae.pres_anno = sf1125.pres_anno AND
					sf1125.form_id_aesp = sae.aces_id AND
					sf1125.form_id_p_ac = sae.acce_id AND
					sf1125.pres_anno = " . $anno_pres . "
					--AND position('" . $dependencia . "' in sf1125.depe_id) > 0
					".(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452") ?
					" AND (sf1125.depe_cosige = '".$dependencia."' OR (sf1125.form_tipo = 0::BIT AND sf1125.form_id_p_ac = '2013-AC' AND sf1125.form_id_aesp = '2013-AC2' ))"
					: "")."
			) as s
			ORDER BY s.tipo DESC, s.centro_gestor, s.centro_costo, s.id_accion_especifica
		";

		$result = $GLOBALS['SafiClassDb']->Query($query);

		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$datos[] = $row;
		}

		return $datos;
	}



	public static function GetNexId(array $params = null)
	{
		$data = array();
		$query = "
	   SELECT COALESCE(MAX((SUBSTRING(docg_id FROM 9 FOR LENGTH(docg_id)-10)) :: INT),0) + 1 AS max_id
       FROM 
       sai_doc_genera
       WHERE
       SUBSTRING(docg_id FROM 1 FOR 4) = '".$params['lugar']."'
       AND SUBSTRING(docg_id FROM 6 FOR 3) = '".$params['Dependencia']."'
       AND SUBSTRING(docg_id FROM LENGTH(docg_id) -1 FOR 2) = '".$params['ano']."'";
		


		$result = $GLOBALS['SafiClassDb']->Query($query);
		if($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			
			$param  = $params['lugar']."-".$params['Dependencia'].$row['max_id'].$params['ano'];
			return $param;

		}

		
	}
		
	public static function GetPocentajesIva()
	{

		$data = array();
		$query = "
	       SELECT 
	            impu_porc 
		   FROM 
		        sai_impuesto_porce sip
		        
		   WHERE 
		        impu_id = 'IVA' AND
		        impu_retencion = CAST(0 as bit) AND 
		        impu_porc > 0 AND esta_id = 1
		        
		   ORDER BY 
		        impu_porc";
		
		//error_log(print_r($query,true));

		$result = $GLOBALS['SafiClassDb']->Query($query);
		while($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			
			$data[$row['impu_porc']] = $row['impu_porc'];

		}
		
		return $data;
	}

	public static function GetAllAccionesEspecificasCortas($params)
	{
		try
		{
			if($params['yearPresupuestario'] == null)
				throw new Exception('El parámetro $params[\'yearPresupuestario\'] es nulo.' );
				
			if($params['yearPresupuestario'] == "")
				throw new Exception('El parámetro $params[\'yearPresupuestario\'] está vacío.' );
			
			$datos = array();
			$condicion = '';
			$condicion2 = '';
			$condicion3 = '';
			$esentero = '';
			$esstring = '';
	
			$yearPresupuestario = $params['yearPresupuestario'];
	
			$cargo = substr($_SESSION['user_perfil_id'],0,2);
				
			if(($params['restrictivo']) ||
			//OTH 
			(($cargo !== substr(PERFIL_ASISTENTE_ADMINISTRATIVO_OTH,0,2)) &&
			($cargo !== substr(PERFIL_ANALISTA_PRESUPUESTO,0,2)) &&
			($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
			($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
			($cargo !== substr(PERFIL_PRESIDENTE,0,2))
			)){
	
				if ($params['dependencia'] != '' && $params['dependencia'] != null) {
						
					$condicion = " AND position('".$params['dependencia']."' in f.depe_cosige) > 0" ;
				}
			}
	
			if ($params['key'] != '' && $params['key'] != null) {
				$key = strtoupper($params['key']);
				$id = $params['idproyAcc'];
	
				$id2 = $params['idproyAcc'] != null? "AND p.proy_id = '$id'": '';
				$id3 = $params['idproyAcc'] != null? "AND a.acce_id = '$id'": '';
	
				$condicion2 = $id2."
				        AND (UPPER(pe.centro_gestor) LIKE '%".$key."%'
						OR UPPER(pe.centro_costo) LIKE '%".$key."%'
						OR UPPER(pe.paes_nombre) LIKE '%".$key."%')";
				$condicion3 = $id3."
				        AND (UPPER(ae.centro_gestor) LIKE '%".$key."%'
						OR UPPER(ae.centro_costo) LIKE '%".$key."%'
						OR UPPER(ae.aces_nombre) LIKE '%".$key."%')";
	
			}			
	
			$queryproy = "
				SELECT
					'1' AS tipo,
					pe.paes_id AS id_especifica,
					p.proy_id AS id_proy_accion,
					p.proy_titulo AS proy_titulo,
					pe.centro_gestor||'/'||pe.centro_costo AS centro,
					pe.paes_nombre AS nombre
				FROM
					sai_proyecto p
				INNER JOIN sai_proy_a_esp pe ON (p.proy_id = pe.proy_id)
				   INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = p.proy_id AND f.form_id_aesp = pe.paes_id AND f.pres_anno = p.pre_anno)
				WHERE pe.pres_anno = ".$yearPresupuestario." AND
				f.pres_anno = ".$yearPresupuestario. 
			$condicion.$condicion2;
						
			$queryacc = "	SELECT
					'0' AS tipo,
					ae.aces_id AS id_especifica,
					a.acce_id AS id_proy_accion,
					'Accion centralizada' AS proy_titulo,
					ae.centro_gestor||'/'||ae.centro_costo AS centro,
					ae.aces_nombre AS nombre
				FROM
					sai_ac_central a
				INNER JOIN sai_acce_esp ae ON (a.acce_id = ae.acce_id)
				 INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = a.acce_id AND f.form_id_aesp = aces_id AND f.pres_anno = a.pres_anno)
				WHERE ae.pres_anno = ".$yearPresupuestario." AND
				f.pres_anno = ".$yearPresupuestario. 
			$condicion.$condicion3;
				
			if($params['tipoproacc'] == 1){
	
				$query = $queryproy;
			}else if($params['tipoproacc'] == 0){
	
				$query =$queryacc;
	
			}else{
	
				$query = $queryproy." UNION ".$queryacc;
			}
	
			$i++;
	
	
			$result = $GLOBALS['SafiClassDb']->Query($query);
	
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
	
				$param[] = array(
	
			    "tipo" => $row['tipo'],
				"id_especifica" => $row['id_especifica'],
				"id_proy_accion" => $row['id_proy_accion'],
				"proy_titulo" => $row['proy_titulo'],
			    "centro" => $row['centro'],
				"nombre" => $row['nombre']	
				);
			}
			
			return $param;
			
		} catch (Exception $e){
			error_log($e, 0);
			return false;
		}
	}
	
	/*
	public static function GetAllAccionesEspecificasCortas($params)
	{
		$datos = array();
		$condicion = '';
		$condicion2 = '';
		$condicion3 = '';
		$esentero = '';
		$esstring = '';
	
	
		$anno_pres=$_SESSION['an_o_presupuesto'];
		$anno = $params['anno'];
			
		if(isset($anno)){
			$anno_pres = $anno;
		}
	
	
		$cargo = substr($_SESSION['user_perfil_id'],0,2);
			
		if(($params['restrictivo']) ||
				//OTH
				(($cargo !== substr(PERFIL_ASISTENTE_ADMINISTRATIVO_OTH,0,2)) &&
						($cargo !== substr(PERFIL_ANALISTA_PRESUPUESTO,0,2)) &&
						($cargo !== substr(PERFIL_JEFE_PRESUPUESTO,0,2)) &&
						($cargo !== substr(PERFIL_DIRECTOR_EJECUTIVO,0,2)) &&
						($cargo !== substr(PERFIL_PRESIDENTE,0,2))
				)){
	
			if ($params['dependencia'] != '' && $params['dependencia'] != null) {
					
				$condicion = " AND position('".$params['dependencia']."' in f.depe_cosige) > 0" ;
			}
	
		}
	
		if ($params['key'] != '' && $params['key'] != null) {
			$key = strtoupper($params['key']);
			$id = $params['idproyAcc'];
	
	
	
			$id2 = $params['idproyAcc'] != null? "AND p.proy_id = '$id'": '';
			$id3 = $params['idproyAcc'] != null? "AND a.acce_id = '$id'": '';
	
	
	
	
			$condicion2 = $id2."
			        AND (UPPER(pe.centro_gestor) LIKE '%".$key."%'
					OR UPPER(pe.centro_costo) LIKE '%".$key."%'
					OR UPPER(pe.paes_nombre) LIKE '%".$key."%')";
			$condicion3 = $id3."
			        AND (UPPER(ae.centro_gestor) LIKE '%".$key."%'
					OR UPPER(ae.centro_costo) LIKE '%".$key."%'
					OR UPPER(ae.aces_nombre) LIKE '%".$key."%')";
	
		}
			
			
	
		$queryproy = "
			SELECT
				'1' AS tipo,
				pe.paes_id AS id_especifica,
				p.proy_id AS id_proy_accion,
				p.proy_titulo AS proy_titulo,
				pe.centro_gestor||'/'||pe.centro_costo AS centro,
				pe.paes_nombre AS nombre
	
	
			FROM
				sai_proyecto p
			INNER JOIN sai_proy_a_esp pe ON (p.proy_id = pe.proy_id)
			   INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = p.proy_id AND f.form_id_aesp = pe.paes_id AND f.pres_anno = p.pre_anno)
			WHERE pe.pres_anno = ".$anno_pres." AND
			f.pres_anno = ".$anno_pres.
				$condicion.$condicion2;
	
	
		//	 error_log(print_r($queryproy,true));
	
			
		$queryacc = "	SELECT
				'0' AS tipo,
				ae.aces_id AS id_especifica,
				a.acce_id AS id_proy_accion,
				'Accion centralizada' AS proy_titulo,
				ae.centro_gestor||'/'||ae.centro_costo AS centro,
				ae.aces_nombre AS nombre
			FROM
				sai_ac_central a
			INNER JOIN sai_acce_esp ae ON (a.acce_id = ae.acce_id)
			 INNER JOIN sai_forma_1125 f ON (f.form_id_p_ac = a.acce_id AND f.form_id_aesp = aces_id AND f.pres_anno = a.pres_anno)
			WHERE ae.pres_anno = ".$anno_pres." AND
			f.pres_anno = ".$anno_pres.
				$condicion.$condicion3;
			
			
		if($params['tipoproacc'] == 1){
	
			$query = $queryproy;
		}else if($params['tipoproacc'] == 0){
	
			$query =$queryacc;
	
		}else{
	
			$query = $queryproy." UNION ".$queryacc;
		}
	
	
		$i++;
	
	
		$result = $GLOBALS['SafiClassDb']->Query($query);
	
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
	
			$param[] = array(
	
					"tipo" => $row['tipo'],
					"id_especifica" => $row['id_especifica'],
					"id_proy_accion" => $row['id_proy_accion'],
					"proy_titulo" => $row['proy_titulo'],
					"centro" => $row['centro'],
					"nombre" => $row['nombre']
			);
	
	
		}
		//error_log(print_r($param,true));
	
		return $param;
	
	}
*/

	public static function GetfuenteFinanciamiento($proyAccion,$proyAccionEspe)
	{
		try {

			if(!isset($proyAccion) && !isset($proyAccionEspe)){
				throw new Exception("GetfuenteFinanciamiento . Detalles: ");
			}

			$query = "
				   Select 
    					fuente_financiamiento
				  from 
    					sai_forma_1125
 
				 WHERE
    					esta_id=1 AND 
    					form_id_p_ac = '".$proyAccion."'AND
   					    form_id_aesp ='".$proyAccionEspe."'";
				
				


			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false){

				$row = $GLOBALS['SafiClassDb']->Fetch($result);
				return $row['fuente_financiamiento'];


			}
			return null;


		} catch (Exception $e) {
			error_log($e, 0);
		}

	}
	
public static function GetMontoComprometido($idDocumento)
	{
		
		$partidMonto = array();

		try {

			if($idDocumento == null || ($idDocumento=trim($idDocumento)) == '')
			throw new Exception("Error al obtener GetMontoComprometido dado  el id de un documento. Detalles: ".
					"el parámetro idDocumento es vacío o nulo");

			$query = "
			
			SELECT  
        categoria_monto.partida,
        categoria_monto.proyacc,
        categoria_monto.proyaccesp,
        sum(categoria_monto.monto) as  monto

        
FROM
(
                                SELECT
                                        rc.comp_id as id,
					rc.part_id as partida,
					rc.pr_ac as proyacc,
					rc.a_esp as proyaccesp,
					(rc.rcomp_debe - rc.rcomp_haber ) as monto

				FROM
    				sai_codi c
    				INNER JOIN sai_reng_comp rc ON (c.comp_id = rc.comp_id)
				WHERE
    				c.esta_id<>15 and
    				c.nro_compromiso = '".$idDocumento."' and
    				rc.part_id  NOT LIKE '4.11.0%'

    				     
    				
				UNION

				SELECT
                                     sp.sopg_id as id,
				     solpaim.sopg_sub_espe as partida,
				     solpaim.sopg_acc_pp as proyacc,
				     solpaim.sopg_acc_esp as proyaccesp,
				     (solpaim.sopg_monto_exento + solpaim.sopg_monto) as monto
				     
				FROM
				
  			        sai_sol_pago sp
  			        
  			        INNER JOIN sai_sol_pago_imputa solpaim ON (sp.sopg_id = solpaim.sopg_id)
  			        WHERE
  			        sp.esta_id<>15 and
  			        sp.comp_id = '".$idDocumento."' and
  			        solpaim.sopg_sub_espe NOT LIKE '4.11.0%'
  			        
  			        

  			        ) AS categoria_monto

	
 GROUP BY   

        categoria_monto.partida,
        categoria_monto.proyacc,
        categoria_monto.proyaccesp"; 
			

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
			throw new Exception("Error al obtener solicitud de pago  dado el id de un documento. Detalles: ".
			utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));

			$i= 0;
			
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result)){

				$partidMonto[$row['partida']] =  $row['monto'];

			}
			
			
			
			if(!$partidMonto){
				
			return  false;
			
			}
			
			
			return $partidMonto;

		} catch (Exception $e) {
			error_log($e, 0);
		}


		return $numero;
	}
	
	
	public static function GetAnnoDocumento($documento)
	{
		try {

			
			
			if(!isset($documento)){
				throw new Exception("GetAnnoDocumento . Detalles: ");
				utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg());
				
			}

			$query = "
				   Select 
				   
    					*
    					
				  from 
    					safi_anno_presupuestario
 
				 WHERE
    					documento =  '".$documento."'";
			            

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false){
				$row = $GLOBALS['SafiClassDb']->Fetch($result);
				
				if($row['anno_anterior'] == "t"){
						
					
												
	     	return self::GetAnnoActual() -1 ;
		
					}else{
					
					
					return self::GetAnnoActual();
					
					}
				
			}
			return null;


		} catch (Exception $e) {
			error_log($e, 0);
		}

	}
	
  public static function GetAnnoActual()
	{
		try {

	
				if(isset($_SESSION['an_o_presupuesto'])){
					
					return $_SESSION['an_o_presupuesto'];
					
					
				}else{
				
				    return false;
				
				}
			


		} catch (Exception $e) {
			error_log($e, 0);
		}

	}
	
	public static function GetBanderaCambioAnio($documento)
	{
		$query = "
				   Select
				
    					*
    	
				  from
    					safi_anno_presupuestario
		
				 WHERE
    					documento =  '".$documento."'";
		 
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false){
			$row = $GLOBALS['SafiClassDb']->Fetch($result);
			if($row['anno_anterior'] == "t"){
				return true;	
			}
			else
			{
				return 	false;
			}
		}
	}
	
	public static function GetFechaCambioAnio($documento)
	{
		$query = "
				   Select
	
    					fecha_creac
   
				  from
    					safi_anno_presupuestario
	
				 WHERE
    					documento =  '".$documento."'";
			
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) !== false){
			$row = $GLOBALS['SafiClassDb']->Fetch($result);
		}
		
		$fecha = $row['fecha_creac'];
		
		
		$fecha2 = date("d/m/Y",strtotime($fecha));
		
		return $fecha2;
	}
}