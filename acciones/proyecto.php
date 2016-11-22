<?php

include(dirname(__FILE__) . '/../init.php');
include(SAFI_MODELO_PATH. '/proyecto.php');

new ProyectoAccion();

class ProyectoAccion
{	
	public function __construct()
	{
		if(isset($_REQUEST["accion"]) && trim($_REQUEST["accion"]) != ''){
			$accion = trim($_REQUEST["accion"]);
			$this->$accion();
		} else {
			$accion = "GetAllProyectosAprobados";
			$this->$accion();
			//echo "No se ha selecionado ninguna accion";
			//exit;
		}
	}
	
	public function GetAllProyectosAprobados()
	{
		$proyectos = SafiModeloProyecto::GetAllProyectosAprobados();
		
		$GLOBALS['SafiRequestVars']['proyectos'] = $proyectos;
	
		include(SAFI_VISTA_PATH ."/json/proyectos.php");
	}
	
	public function GetAccionesEspecificasBy()
	{
		if(isset($_REQUEST["idProyecto"]) && trim($_REQUEST["idProyecto"]) != ''){
			$idproyecto = trim($_REQUEST["idProyecto"]);
			
			$accionesEspecificas = SafiModeloProyecto::GetAccionesEspecificasBy($idproyecto);
			
			$GLOBALS['SafiRequestVars']['proyectoAccionesEspecificas'] = $accionesEspecificas;
	
			include(SAFI_VISTA_PATH ."/json/proyectoaccionesespecificas.php");
		}
	}
}