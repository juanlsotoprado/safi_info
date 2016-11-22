<?php
include_once(SAFI_ENTIDADES_PATH . '/cuentaContable.php');
include_once(SAFI_ENTIDADES_PATH . '/partida.php');
include_once(SAFI_ENTIDADES_PATH . '/convertidor.php');

class SafiModeloConvertidor
{
	public static function GetAsociacionConvertidor($key){
	
		try {
			$convertidor =array();
	
			$query = "
	
				SELECT DISTINCT
					c.cpat_id AS cpat_id,
					c.cpat_nombre AS cpat_nombre,
					conv.part_id AS part_id, 
					--p.part_nombre AS part_nombre,
					conv.cpat_pasivo_id AS cpat_pasivo_id
				FROM
					sai_cue_pat c
				LEFT JOIN sai_convertidor conv ON (c.cpat_id = conv.cpat_id)
				LEFT JOIN sai_partida p ON (p.part_id = conv.part_id)
				--LEFT OUTER JOIN sai_partida p ON (p.part_id = conv.part_id)					
				WHERE
					(c.cpat_id LIKE '".$key."%' OR LOWER(c.cpat_nombre) LIKE '%".strtolower($key)."%' OR conv.part_id LIKE '".$key."%')
					AND SUBSTRING(c.cpat_id,16) != '00'
				ORDER BY
					c.cpat_id
			";				

			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error comp . Detalles: ".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			}
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$convertidor[] = self::LlenarConvertidor($row);	
			}
	
			return $convertidor;
	
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetAsociacionConvertidorActivas($key){
	
		try {
			$convertidor =array();
	
			$query = "
				SELECT DISTINCT
					c.cpat_id AS cpat_id,
					c.cpat_nombre AS cpat_nombre,
					conv.part_id AS part_id,
					--p.part_nombre AS part_nombre,
					conv.cpat_pasivo_id AS cpat_pasivo_id
				FROM
					sai_cue_pat c
					LEFT JOIN sai_convertidor conv ON (c.cpat_id = conv.cpat_id)
					LEFT JOIN sai_partida p ON (p.part_id = conv.part_id)
					LEFT JOIN
					(
						SELECT
							cpat_id,
							MAX (pres_anno) AS max_pres_anno
						FROM
							sai_convertidor
						GROUP BY
							cpat_id
					) AS max_convertidor ON (
						max_convertidor.cpat_id = conv.cpat_id
						AND max_convertidor.max_pres_anno = conv.pres_anno
					) 
				WHERE
					(c.cpat_id LIKE '".$key."%' OR LOWER(c.cpat_nombre) LIKE '%".strtolower($key)."%' OR conv.part_id LIKE '".$key."%')
					AND SUBSTRING(c.cpat_id,16) != '00'
					AND CASE WHEN p.esta_id IS NOT NULL THEN
							p.esta_id != 15
							AND p.esta_id != 2
						ELSE
							TRUE
					END
				ORDER BY
					c.cpat_id
			";
	
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error comp . Detalles: ".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			}
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$convertidor[] = self::LlenarConvertidor($row);
			}
	
			return $convertidor;
	
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	public static function GetAsociacionConvertidorCodi($key, $numItems, $selecteds = array()){
	
		try {
			$convertidor =array();
			
			$where = '';
			if($selecteds != null && is_array($selecteds) && count($selecteds)>0){
				$where = "
					AND
					c.cpat_id NOT IN (".implode(',', $selecteds).")
				";
			}
	
			$query = "
				SELECT DISTINCT
					c.cpat_id AS cpat_id,
					c.cpat_nombre AS cpat_nombre,
					conv.part_id AS part_id,
					--p.part_nombre AS part_nombre,
					conv.cpat_pasivo_id AS cpat_pasivo_id
				FROM
					sai_cue_pat c
					LEFT JOIN sai_convertidor conv ON (c.cpat_id = conv.cpat_id)
					LEFT JOIN sai_partida p ON (p.part_id = conv.part_id)
					LEFT JOIN
					(
						SELECT
							cpat_id,
							MAX (pres_anno) AS max_pres_anno
						FROM
							sai_convertidor
						GROUP BY
							cpat_id
					) AS max_convertidor ON (
						max_convertidor.cpat_id = conv.cpat_id
						AND max_convertidor.max_pres_anno = conv.pres_anno
					)
				WHERE
					(c.cpat_id LIKE '".$key."%' OR LOWER(c.cpat_nombre) LIKE '%".strtolower($key)."%' OR conv.part_id LIKE '".$key."%')
					AND SUBSTRING(c.cpat_id,16) != '00'
					AND (conv.part_id LIKE '4.11.0%' or conv.part_id is null or conv.part_id = '')
					AND CASE WHEN p.esta_id IS NOT NULL THEN
							p.esta_id != 15
							AND p.esta_id != 2
						ELSE
							TRUE
					END
					" . $where . "
				ORDER BY
					c.cpat_id
				LIMIT
					" . $numItems . "
			";
	
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false){
				throw new Exception("Error comp . Detalles: ".
						utf8_encode($GLOBALS['SafiClassDb']->GetErrorMsg()));
	
			}
	
			while($row = $GLOBALS['SafiClassDb']->Fetch($result)){
				$convertidor[] = self::LlenarConvertidor($row);
			}
	
			return $convertidor;
	
		} catch (Exception $e) {
			error_log($e, 0);
			return false;
		}
	}
	
	public static function LlenarConvertidor($row)
	{
		$convertidor = new EntidadConvertidor();
		$cuentaContable = new EntidadCuentaContable();
		$partida = new EntidadPartida();
		$cuentaPasivo = new EntidadCuentaContable();				
		$cuentaContable -> SetId($row['cpat_id']);
		$cuentaContable -> SetNombre($row['cpat_nombre']);
		$partida -> SetId($row['part_id']);
		$partida -> SetNombre('');
		$cuentaPasivo -> SetId($row['cpat_pasivo_id']);
		$convertidor -> SetCuentaContable ($cuentaContable);		
		$convertidor -> SetPartida ($partida);		
		$convertidor -> SetPasivo ($cuentaPasivo);		
		return $convertidor;
	}
}
?>