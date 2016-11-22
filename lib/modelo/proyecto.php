<?php
include_once(SAFI_ENTIDADES_PATH . '/proyecto.php');

class SafiModeloProyecto
{
	public static function	GetAllProyectosAprobados($yearPresupuestario = null, $dependencia = null)
	{
		if ($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario=$_SESSION['an_o_presupuesto'];
		
		if($dependencia == null || $dependencia == "")
			$dependencia = $_SESSION['user_depe_id'];
		/*
		echo "<pre>";
		echo "Year presupuestario: " . $yearPresupuestario . "\n";
		echo "Dependencia: " . $dependencia;
		echo "</pre>";
		*/
		
		$proyectos = array();
		
		$query = "
			SELECT				
				p.proy_id,
				pe.centro_gestor || ':' || p.proy_titulo AS proy_titulo,
				p.proy_desc,
				p.proy_resultado,
				p.proy_obj,
				p.pre_anno,
				p.esta_id,
				p.proy_observa,
				p.usua_login,
				p.usua_log_resp,
				p.proy_cod_onapre
			FROM
				sai_proyecto p
			INNER JOIN 	(SELECT DISTINCT(SUBSTR(centro_gestor,1,2)) AS centro_gestor, proy_id
						FROM sai_proy_a_esp
						WHERE pres_anno = " . $yearPresupuestario . "
					)  pe ON (p.proy_id=pe.proy_id)	
			WHERE
				p.esta_id <> 13 AND
				p.pre_anno = " . $yearPresupuestario . " AND
				p.proy_id IN
				(
					SELECT
						pae.proy_id
					FROM
						sai_proy_a_esp pae,
						sai_forma_1125 f1125
					WHERE
						pae.pres_anno = f1125.pres_anno AND
						pae.proy_id = f1125.form_id_p_ac AND
						f1125.form_id_aesp = pae.paes_id AND
						f1125.pres_anno = " . $yearPresupuestario . "
						--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODOS_PROYECTOS)) ? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
		";
		
		if($dependencia == '550') {
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '120670' AND f1125.form_id_aesp = '120670 A-3-1') OR  (f1125.form_id_p_ac = '111721' AND f1125.form_id_aesp = '111721 E-1') OR (f1125.form_id_p_ac = '114254' AND f1125.form_id_aesp = '114254 E-2') OR (f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117659' AND f1125.form_id_aesp = '117659 A-1')))
					)
			";
		} else if($dependencia=='600'){
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117580' AND f1125.form_id_aesp = '117580 B-3') ))
					)
			";
		} 
		else{
		   ($dependencia !="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452" && $dependencia!="550" && $dependencia!="600")? $query .= "AND f1125.depe_cosige = '".$dependencia."'":"";
		}
		
		$query .= "
				)
			ORDER BY
				p.proy_titulo
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$proyectos[] = $row;
		}
		
		return $proyectos;
	}
	
	public static function GetAccionesEspecificasBy($idProyecto)
	{
		$dependencia = $_SESSION['user_depe_id'];
		$anno_pres=$_SESSION['an_o_presupuesto'];
		
		$accionesEspecificas = array();
		
		$query = "
			SELECT				
				pae.proy_id,
				pae.paes_id,
				pae.paes_fecha_ini,
				pae.paes_fecha_fin,
				pae.paes_nombre,
				pae.pres_anno,
				pae.centro_gestor,
				pae.centro_costo
			FROM
				sai_proy_a_esp pae
				INNER JOIN sai_forma_1125 f1125 ON
					(
						f1125.pres_anno = pae.pres_anno AND
						f1125.form_id_p_ac = pae.proy_id AND
						f1125.form_id_aesp = pae.paes_id
					)
			WHERE
				proy_id = '" . $idProyecto . "' AND
				f1125.esta_id = 1 AND
				f1125.pres_anno = " . $anno_pres . "
				--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODOS_PROYECTOS)) ? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
		";
		
		if($dependencia == '550') {
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '120670' AND f1125.form_id_aesp = '120670 A-3-1') OR (f1125.form_id_p_ac = '111721' AND f1125.form_id_aesp = '111721 E-1') OR (f1125.form_id_p_ac = '114254' AND f1125.form_id_aesp = '114254 E-2') OR (f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117659' AND f1125.form_id_aesp = '117659 A-1')))
					)
			";
		} else if($dependencia=='600'){
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117580' AND f1125.form_id_aesp = '117580 B-3') ))
					)
			";
		} else if($dependencia=='500'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";	
		} else if($dependencia=='250'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='700'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='650'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else{
		   ($dependencia !="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")? $query .= "AND f1125.depe_cosige = '".$dependencia."'":"";
		}
		
		$query .= "
			ORDER BY
				pae.centro_gestor,
				pae.centro_costo,
				pae.paes_id
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$accionesEspecificas[] = $row;
		}
		
		return $accionesEspecificas;
	}
	
	public static function GetAllAccionesEspecificasAprobadas()
	{
		$dependencia = $_SESSION['user_depe_id'];
		$anno_pres=$_SESSION['an_o_presupuesto'];
		
		$accionesEspecificas = array();
		
		$query = "
			SELECT				
				pae.proy_id,
				pae.paes_id,
				pae.paes_fecha_ini,
				pae.paes_fecha_fin,
				pae.paes_nombre,
				pae.pres_anno,
				pae.centro_gestor,
				pae.centro_costo
			FROM
				sai_proy_a_esp pae
				INNER JOIN sai_forma_1125 f1125 ON
					(
						f1125.pres_anno = pae.pres_anno AND
						f1125.form_id_p_ac = pae.proy_id AND
						f1125.form_id_aesp = pae.paes_id
					)
			WHERE
				f1125.esta_id = 1 AND
				f1125.pres_anno = " . $anno_pres . "
				--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODOS_PROYECTOS)) ? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
		";
		
		if($dependencia == '550') {
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '120670' AND f1125.form_id_aesp = '120670 A-3-1') OR (f1125.form_id_p_ac = '111721' AND f1125.form_id_aesp = '111721 E-1') OR (f1125.form_id_p_ac = '114254' AND f1125.form_id_aesp = '114254 E-2') OR (f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117659' AND f1125.form_id_aesp = '117659 A-1')))
					)
			";
		} else if($dependencia=='600'){
			$query.= "
				AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2') OR (f1125.form_id_p_ac = '117580' AND f1125.form_id_aesp = '117580 B-3') ))
					)
			";
		} else if($dependencia=='500'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";	
		} else if($dependencia=='250'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='700'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else if($dependencia=='650'){
			$query.= "AND 
					(
						(f1125.depe_cosige = '".$dependencia."') OR 
						(f1125.form_tipo = 1::BIT AND ((f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2')))
					)
			";
		} else{
		   ($dependencia !="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452")? $query .= "AND f1125.depe_cosige = '".$dependencia."'":"";
		}
		
		$query .= "
			ORDER BY
				pae.centro_gestor,
				pae.centro_costo,
				pae.paes_id
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$accionesEspecificas[] = $row;
		}
		
		return $accionesEspecificas;
	}
	
	public static function GetProyectoById($id, $anho){
		$proyecto = null;
		
		if($id != null && $id != '' && $anho != null && $anho != ''){
		
			$query = "
				SELECT
					p.proy_id,
					p.pre_anno,
					p.proy_titulo
				FROM
					sai_proyecto p
				WHERE
					p.proy_id = '".$id."' AND
					p.pre_anno = '".$anho."'
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					$proyecto = new EntidadProyecto();
					
					$proyecto->SetId($row['proy_id']);
					$proyecto->SetAnho($row['pre_anno']);
					$proyecto->SetNombre($row['proy_titulo']);
					
				}
			}
		}
		
		return $proyecto;
	}
}