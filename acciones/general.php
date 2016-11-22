<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/general.php');

new generalAccion();

class GeneralAccion
{
	public $_yearPresupuestario = null;

	public function __construct()
	{
		$this->_yearPresupuestario = $_SESSION['an_o_presupuesto'];
		
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "Searchcategoria";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function  SearchCategoria()
	{
		$key = trim(utf8_decode($_REQUEST["key"]));
		$dependencia = trim($_REQUEST['dependencia']);
		$restrictivo = trim($_REQUEST['restrictivo']);
		$idproyAcc = trim($_REQUEST['idproyAcc']);
		$tipoproacc = trim($_REQUEST['tipoproacc']);
		$yearPresupuestario = trim($_REQUEST['yearPresupuestario']);
		
		if($yearPresupuestario == null || $yearPresupuestario == "")
			$yearPresupuestario = $this->_yearPresupuestario;

		$params = array(
			'key'=> $key,
			'dependencia'=> $dependencia,
			'idproyAcc' => $idproyAcc,
			'tipoproacc' => $tipoproacc,
			'restrictivo' => $restrictivo,
			'yearPresupuestario' => $yearPresupuestario
		);
		
		$categorias = SafiModeloGeneral::GetAllAccionesEspecificasCortas($params);
		
		$GLOBALS['SafiRequestVars']['categorias'] = $categorias;
		
		include(SAFI_VISTA_PATH ."/json/Categoria.php");
	}
}