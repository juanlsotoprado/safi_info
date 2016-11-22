<?php
include_once(SAFI_ENTIDADES_PATH . '/accioncentralizada.php');

class SafiModeloAccionCentralizada
{
	public static function GetAllAccionesCentralizadasAprobadas($yearPresupuestario = null, $dependencia = null)
	{
		if ($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario = $_SESSION['an_o_presupuesto'];
		
		if($dependencia == null || $dependencia == "")
			$dependencia = $_SESSION['user_depe_id'];
		
		$accionesCentralizadas = array();
		
		$query = "
			SELECT
				ac.acce_id,
				ac.pres_anno,
				pe.centro_gestor || ':' ||ac.acce_denom AS acce_denom,
				ac.esta_id,
				ac.acce_observa,
				ac.usua_login,
				ac.acce_visib
			FROM
				sai_ac_central ac
			INNER JOIN	(SELECT DISTINCT(SUBSTR(centro_gestor,1,2)) AS centro_gestor, acce_id
						FROM sai_acce_esp
						WHERE pres_anno = " . $yearPresupuestario . "
					)  pe ON (ac.acce_id=pe.acce_id)					
			WHERE
				ac.pres_anno = ".$yearPresupuestario." AND
				ac.acce_id IN
				(
					SELECT
						ae.acce_id
					FROM
						sai_acce_esp ae,
						sai_forma_1125 f1125
					WHERE
						ae.pres_anno = f1125.pres_anno AND
						ae.pres_anno = " . $yearPresupuestario . " AND
						f1125.form_id_aesp = ae.aces_id AND
						f1125.form_id_p_ac = ae.acce_id AND
						f1125.pres_anno = " . $yearPresupuestario . "
						--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODAS_ACCIONES_CENTRALIZADAS))
							? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
						".(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452") ?
						" AND (f1125.depe_cosige = '".$dependencia."' OR (f1125.form_tipo = 0::BIT AND f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2' ))"
						: "")."
				)
			ORDER BY
				ac.acce_denom
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$accionesCentralizadas[] = $row;
		}
		
		return $accionesCentralizadas;
	}
	
	public static function GetAccionesEspecificasBy($idAccionCentralizada)
	{
		$dependencia = $_SESSION['user_depe_id'];
		$anno_pres=$_SESSION['an_o_presupuesto'];
		
		$accionesEspecificas = array();
		
		$query = "
			SELECT
				ae.acce_id,
				ae.pres_anno,
				ae.aces_id,
				ae.aces_fecha_ini,
				ae.aces_fecha_fin,
				ae.aces_nombre,
				ae.centro_gestor,
				ae.centro_costo
			FROM
				sai_acce_esp ae
				INNER JOIN sai_forma_1125 f1125 ON
					(
						ae.pres_anno = f1125.pres_anno AND
						ae.aces_id = f1125.form_id_aesp AND
						ae.acce_id = f1125.form_id_p_ac
					)
			WHERE
				ae.acce_id = '" . $idAccionCentralizada . "' AND
				f1125.esta_id = 1 AND
				f1125.pres_anno = " . $anno_pres . "
				--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODAS_ACCIONES_CENTRALIZADAS)) ? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
				".(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452") ?
				" AND (f1125.depe_cosige = '".$dependencia."' OR (f1125.form_tipo = 0::BIT AND f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2' ))"
				: "")."
			ORDER BY
				ae.centro_gestor,
				ae.centro_costo,
				ae.aces_id
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
				ae.acce_id,
				ae.pres_anno,
				ae.aces_id,
				ae.aces_fecha_ini,
				ae.aces_fecha_fin,
				ae.aces_nombre,
				ae.centro_gestor,
				ae.centro_costo
			FROM
				sai_acce_esp ae
				INNER JOIN sai_forma_1125 f1125 ON
					(
						ae.pres_anno = f1125.pres_anno AND
						ae.aces_id = f1125.form_id_aesp AND
						ae.acce_id = f1125.form_id_p_ac
					)
			WHERE
				f1125.esta_id = 1 AND
				f1125.pres_anno = " . $anno_pres . "
				--".((DependenciaPuedeMostrarTodos($dependencia, MOSTRAR_TODAS_ACCIONES_CENTRALIZADAS)) ? "" :" AND f1125.depe_cosige = '".$dependencia."'")."
				".(($dependencia!="150" && $dependencia!="350" && $dependencia!="400" && $dependencia!="450" && $dependencia!="200" && $dependencia!="500" && $dependencia!="050" && $dependencia!="452") ?
				" AND (f1125.depe_cosige = '".$dependencia."' OR (f1125.form_tipo = 0::BIT AND f1125.form_id_p_ac = '2013-AC' AND f1125.form_id_aesp = '2013-AC2' ))"
				: "")."
			ORDER BY
				ae.centro_gestor,
				ae.centro_costo,
				ae.aces_id
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$accionesEspecificas[] = $row;
		}
		
		return $accionesEspecificas;
	}
	
	public static function GetAccionCentralizadaById($id, $anho){
		$accionCent = null;
		
		if($id != null && $id != '' && $anho != null && $anho != ''){
		
			$query = "
				SELECT
					ac.acce_id,
					ac.pres_anno,
					ac.acce_denom
				FROM
					sai_ac_central ac
				WHERE
					ac.acce_id = '".$id."' AND
					ac.pres_anno = '".$anho."'
			";
			
			if($result = $GLOBALS['SafiClassDb']->Query($query)){
				if ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
					
					$accionCent = new EntidadAccionCentralizada();
					
					$accionCent->SetId($row['acce_id']);
					$accionCent->SetAnho($row['pres_anno']);
					$accionCent->SetNombre($row['acce_denom']);
				}
			}
		}
		
		return $accionCent;
	}
}