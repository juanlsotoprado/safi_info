<?php
include_once (SAFI_ENTIDADES_PATH . '/memo.php');
include_once (SAFI_ENTIDADES_PATH . '/item.php');

class SafiModeloItem
{	
	public static function GetSelectFieldsItem()
	{
		return "
					item.id AS item_id,
					item.nombre AS item_nombre
		";
	}
	
	public static function LlenarItem($row)
	{
		$item = new EntidadItem();
				
		$item->SetId($row['item_id']);
		$item->SetNombre($row['item_nombre']);
		
		return $item;
	}
	
	public static function Search($key, $numItems)
	{
		$items = array();
		$query = "
			SELECT
				si.id,
				si.nombre,
				sip.part_id
			FROM
				sai_item si INNER JOIN sai_item_partida sip ON (si.id = sip.id_item) 
			WHERE
				LOWER(sip.part_id||' : '||si.nombre) LIKE '%"
					.utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8'))."%' 
			LIMIT
				".$numItems."
		";
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		if($result === false){
			echo $GLOBALS['SafiClassDb']->GetErrorMsg();
		}
		
		while ($row = $GLOBALS['SafiClassDb']->Fetch($result)) {
			$item = new EntidadItem();
			$item->SetId($row['id']);
			$item->SetNombre(utf8_encode($row['part_id']." : ".$row['nombre']));
			$items[] = $item;
		}
		return $items;
	}
	
	public static function SearchItems($key, $numItems, $selecteds = array(), $params = array())
	{
		try {
			
			$preMsg = "Error al buscar los items (SearchItems).";
			$items = array();
			
			$where = '';
			if($selecteds != null && is_array($selecteds) && count($selecteds) > 0){
				$where = "
					AND item.id NOT IN (".implode(',', $selecteds).")
				";
			}
			
			if($params != null && is_array($params) && count($params) > 0){
				if(($tipoItem = $params['tipoItem']) != null && ($tipoItem = trim($params['tipoItem'])) != ""){
					$where .= "
						AND item.id_tipo = ".$tipoItem."";
				}
			}
			
			$query = "
				SELECT
					".self::GetSelectFieldsItem()."
				FROM
					sai_item item
				WHERE
					(
						upper(item.nombre) LIKE '%" . utf8_decode(mb_strtoupper($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8')) . "%'
						OR item.id LIKE '".utf8_decode($GLOBALS['SafiClassDb']->Quote($key))."%' 
					)
					" . $where . "
				ORDER BY
					item.nombre
				LIMIT
				".$numItems."
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception(utf8_decode($preMsg." Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$items[$row['item_id']] = self::LlenarItem($row);
			}
			
			return $items;
			
		} catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
	
	public static function GetItemsByIds($idsItems = null)
	{
		try {
			
			$preMsg = "Error al obtener los items (GetItemsByIds).";
			$items = array();
			
			if($idsItems === null)
				throw new Exception(utf8_decode($preMsg." Detalles: El parametro idsItems es nulo."));
			if(!is_array($idsItems))
				throw new Exception(utf8_decode($preMsg." Detalles: El parametro idsItems no es un arreglo."));
			
			$query = "
				SELECT
					".self::GetSelectFieldsItem()."
				FROM
					sai_item item
				WHERE
					item.id IN ('".implode("', '", $idsItems)."')
				ORDER BY
					item.nombre
			";
			
			if(($result = $GLOBALS['SafiClassDb']->Query($query)) === false)
				throw new Exception(utf8_decode($preMsg." Detalles: ".$GLOBALS['SafiClassDb']->GetErrorMsg()));
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$items[$row['item_id']] = self::LlenarItem($row);
			}
			
			return $items;
			
		} catch (Exception $e)
		{
			error_log($e, 0);
			return null;
		}
	}
}