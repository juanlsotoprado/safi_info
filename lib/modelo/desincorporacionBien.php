<?php
require_once(SAFI_ENTIDADES_PATH . '/desincorporacionBien.php');

include_once(SAFI_MODELO_PATH . '/docgenera.php');
include_once(SAFI_MODELO_PATH . '/wfcadena.php');

class SafiModeloDesincorporacionBien
{	
	public static function GetDesincorporacion()
	{
		
	}
	public static function AnularActa($acta)
	{
		$query=
		"
		update
			sai_desincorporar
		set
			esta_id=15
		where
			acta_id= trim('".$acta."')
		";
		
		//error_log(print_r($query,true));
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false)
	
		return $result;
	}
	public static function ModificarSudebip($array,$acta)
	{
		$query =
		"
		delete from
			sai_desincorporar_item
		where
			arti_id NOT IN (".implode(", ",$array['id']).")
			and acta_id = '".$acta."'
		";
		
		//error_log(print_r($query,true));
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		return $result;
	}
	public static function ModificarActa($array,$acta)
	{
		$total = $array['total2'];
		$query =
		"
		delete from
			sai_desincorporar_item
		where
			acta_id = '".$acta."'
		";
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		
		//error_log(print_r($array,true));
		
		for($t=0;$t<$total;$t++)
		{
		$sqlarticulos=
		"
		INSERT INTO 
				sai_desincorporar_item(acta_id,arti_id,modelo,marca_id,ubicacion,precio,serial)
		VALUES (
				'".$acta."',
				'".$array['ArrayDesincorporacion']['id'][$t]."',
				'".$array['ArrayDesincorporacion']['modelo'][$t]."',
				".$array['ArrayDesincorporacion']['marca'][$t].",
				".$array['ArrayDesincorporacion']['ubicacion'][$t].",
				".$array['ArrayDesincorporacion']['precio'][$t].",
				'".$array['ArrayDesincorporacion']['serial'][$t]."'
				)
		";
						
		$GLOBALS['SafiClassDb']->Query($sqlarticulos);
						
		}
		
		return $result;
	}
	public static function BuscarDetalle($key)
	{
		$querydetalle=
		"
		SELECT
			general.acta_id,
			general.observaciones,
			to_char(general.fecha_acta, 'DD/MM/YYYY') AS fecha_acta,
			item.arti_id,
			biin.bien_id,
			itemnombre.nombre,
			item.modelo,
			item.marca_id,
			nombremarca.bmarc_nombre,
			item.ubicacion,
			item.precio,
			item.serial
		FROM
			sai_desincorporar general
			inner join sai_desincorporar_item item on(item.acta_id = general.acta_id)
			inner join sai_biin_items biin on(biin.clave_bien = item.arti_id)
			inner join sai_item itemnombre on(itemnombre.id = biin.bien_id)
			inner join sai_bien_marca nombremarca on(item.marca_id = nombremarca.bmarc_id)	
		WHERE
			general.acta_id = '".$key."'
		";
		
		//error_log(print_r($querydetalle,true));
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querydetalle)) != false){
		
			$indice = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
					
				$stringobj  ['acta_id'] = $key;
				$stringobj  ['observaciones'] = utf8_encode($row['observaciones']);
				$stringobj  ['fecha_acta'] = $row['fecha_acta'];		
				
				$stringobj  ['arti_id'][$indice] = $row['bien_id'];
				$stringobj  ['bien_id'][$indice] = $row['arti_id'];
				$stringobj  ['nombre'][$indice] = $row['nombre'];
				$stringobj  ['modelo'][$indice] = utf8_encode($row['modelo']);
				$stringobj  ['bmarc_nombre'][$indice] = $row['bmarc_nombre'];
				$stringobj  ['precio'][$indice] = $row['precio'];
				$stringobj  ['serial'][$indice] = $row['serial'];
				$stringobj  ['ubicacion'][$indice] = $row['ubicacion'];
				$stringobj  ['total'] = $indice;
				
				$indice ++;
					
			}
		
		}
		
		return $stringobj;
	}
	public static function BuscarActivo($key)
	{
		$query = 
				"
				select
					t2.nombre,
					t1.marca_id,
					t1.precio,
					t1.modelo, 
					t1.serial,
					t1.ubicacion,
					t1.clave_bien,
					t5.clave_bien as clave,
					t1.acta_id,
					t1.etiqueta,
					t5.tipo
				from	
					sai_biin_items t1
					inner join sai_item t2 on(t1.bien_id = t2.id)
					left join
					(
						SELECT
							t3.clave_bien,
							t4.tipo
						FROM
							sai_bien_reasignar_item t3
							inner join sai_bien_reasignar t4 on(t4.acta_id = t3.acta_id)
						WHERE 
							t4.esta_id != 15
						
					) as t5 ON (t5.clave_bien = t1.clave_bien)
				where 
					lower(t2.nombre) like '%".$key."%' and
					(t1.esta_id = 53 or t1.esta_id = 41) 
					
					and  (t5.tipo != 1 or t5.tipo IS NULL)
				order by 
					t5.tipo
				limit 10
				";
		
		//error_log(print_r($key,true));
		//error_log(print_r($query,true));
		//utf8_decode(mb_strtolower($GLOBALS['SafiClassDb']->Quote($key), 'UTF-8'))
		
		if(($result = $GLOBALS['SafiClassDb']->Query($query)) != false){
			
			$indice = 0;
			
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
				$stringobj[$indice]['nombre'] = $row['nombre'];
				$stringobj[$indice]['marca_id'] = $row['marca_id'];
				$stringobj[$indice]['precio'] = $row['precio'];
				$stringobj[$indice]['modelo'] = $row['modelo'];
				$stringobj[$indice]['serial'] = $row['serial'];
				$stringobj[$indice]['clave_bien'] = $row['clave_bien'];
				$stringobj[$indice]['acta_id'] = $row['acta_id'];
				$stringobj[$indice]['ubicacion'] = $row['ubicacion'];
				
				$indice++;
			}
			

		}
		//error_log(print_r($stringobj,true));	
		return $stringobj;
		
		
		
	}
	
	public static function BuscarDesi($key,$txt_inicio,$hid_desde_itin)
	{
		if($txt_inicio != null and $hid_desde_itin != null){
		$fecha_ini=substr($txt_inicio,6,4)."-".substr($txt_inicio,3,2)."-".substr($txt_inicio,0,2);
		$fecha_fin=substr($hid_desde_itin,6,4)."-".substr($hid_desde_itin,3,2)."-".substr($hid_desde_itin,0,2)." 23:59:59";
		if($key == null)
		{
			$queryfecha = " WHERE fecha_acta >= '".$fecha_ini."' and fecha_acta <= '".$fecha_fin."'";
		}
		}
		
		if($key != null)
		{
			$querykey = " WHERE acta_id = '".$key."' "; 
		}
		else
		{
			$querykey = " ";
		}

		$querybuscar=
		"
		SELECT
			*
		FROM
			sai_desincorporar general ".$querykey." ".$queryfecha." ";
		
		//error_log(print_r($querybuscar,true));
		
		if(($result = $GLOBALS['SafiClassDb']->Query($querybuscar)) != false){
		
			$indice = 0;
			while ($row = $GLOBALS['SafiClassDb']->Fetch($result))
			{
					
				$stringobj  ['acta_id'][$indice] = $row['acta_id'];
				$stringobj  ['observaciones'][$indice] = utf8_encode($row['observaciones']);
				if($row['esta_id']==1)
				{
					$stringobj['esta_id'][$indice]="Activa";
				}
				else if($row['esta_id']==15)
				{
					$stringobj['esta_id'][$indice]="Anulada";
				}
				$fechahora = explode (' ',$row['fecha_acta']);
				$fecha = explode ('-',$fechahora[0]);
				$fecha2  =  $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' '.$fechahora[1];
				$stringobj  ['fecha_acta'][$indice] = $fecha2;
				$stringobj  ['total'] = $indice;
				$indice ++;
					
			}
		
		}
		
		return $stringobj;
	}
	
	public static function GuardarActa($array)
	{
		//error_log(print_r($array,true));
		$total = $array['total'];
		$fechax = $array['hid_desde_itin'];
		$dateTime = new DateTime();
		$hora = (String) $dateTime->format('h:m:s');
		$fecha1 = explode("/", $fechax);
		$fecha = $fecha1[2]."-".$fecha1[1]."-".$fecha1[0]." ".$hora;
		
		//generar codigo
		$query = 
			"
				select * from sai_generar_codigo('desi', '453' ,'fecha_acta','acta_id') as codigo
			"
		;
		
		$result = $GLOBALS['SafiClassDb']->Query($query);
		$result2 = $GLOBALS['SafiClassDb']->Fetch($result);
		$codigo_acta = $result2['codigo'];
		
		$sqlgeneral=  
			 
		"
			INSERT INTO sai_desincorporar(acta_id,usua_login,observaciones,esta_id,depe_id,fecha_acta)
			VALUES ('".$codigo_acta."','".$_SESSION['login']."','".$array['observaciones']."',1,'".$_SESSION['user_depe_id']."','".$fecha."')
		"; 
		
		//echo $sqlgeneral;
		$GLOBALS['SafiClassDb']->Query($sqlgeneral);
		
		for($t=0;$t<$total;$t++)
		{
			$sqlarticulos=
			"
				INSERT INTO sai_desincorporar_item(acta_id,arti_id,modelo,marca_id,ubicacion,precio,serial)
				VALUES (
						'".$codigo_acta."',
						'".$array['ArrayDesincorporacion']['id'][$t]."',
						'".$array['ArrayDesincorporacion']['modelo'][$t]."',
						".$array['ArrayDesincorporacion']['marca'][$t].",
						".$array['ArrayDesincorporacion']['ubicacion'][$t].",
						".$array['ArrayDesincorporacion']['precio'][$t].",
						'".$array['ArrayDesincorporacion']['serial'][$t]."'
						)
			";
			
			//echo $sqlarticulos;
			$GLOBALS['SafiClassDb']->Query($sqlarticulos);
			
		}
		
		//cadena/////////accion luego de generar codigo
		$params['desi_id'] = $codigo_acta;
		 
		$params['PerfilSiguiente'] = $_SESSION['user_perfil_id'];
		$params['CadenaIdcadena'] = trim($_REQUEST['idCadenaSiguiente']);
		 
		$cadenaIdGrupo =  SafiModeloWFCadena::GetCadenaIdGrupo($params['CadenaIdcadena']);
		 
		$params['CadenaGrupo'] = $cadenaIdGrupo;
		$params['DependenciaTramita'] = $param['Dependencia'];
		$params['presAnno'] = $_SESSION['an_o_presupuesto'];
		 
		 
		 
		$dateTime = new DateTime();
		 
		$fechax = (String) $dateTime->format('d/m/Y h:m:s');
		 
		//echo $fechax;
		
		////////////////////////ingreso en doc genera de los datos
		
		$data['docg_id'] = $params['desi_id'];
		$data['docg_wfob_id_ini'] = $params['docg_wfob_id_ini'] != false ? $params['docg_wfob_id_ini'] :  0 ;
		$data['docg_wfca_id'] = $params['CadenaIdcadena'] ;
		$data['docg_usua_login'] = $_SESSION['login'];
		$data['docg_perf_id'] =  $params['IdPerfil']  != false ? $params['IdPerfil'] : $_SESSION['user_perfil_id'] ;
		$data['docg_fecha'] = $fechax;
		$data['docg_esta_id'] = $params['docg_esta_id'] != false ? $params['docg_esta_id'] :59 ;
		$data['docg_prioridad'] = 1 ;
		$data['docg_perf_id_act'] = $params['PerfilSiguiente'] ;
		$data['docg_estado_pres'] = '' ;
		$data['docg_numero_reserva'] =  '' ;
		$data['docg_fuente_finan'] = '' ;
		
		//error_log(print_r($params,true));
		//error_log(print_r($data,true));
		 
		 
		$docGenera = SafiModeloDocGenera::LlenarDocGenera($data);
		 
			 
		$result = SafiModeloDocGenera::GuardarDocGenera($docGenera);
		
		return $codigo_acta;
		
	}
}

?>