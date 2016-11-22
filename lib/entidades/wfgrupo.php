<?php
include_once(SAFI_ENTIDADES_PATH . '/cargo.php');

class EntidadWFGrupo
{

	private $_id = 0; // Código del Grupo (clave primaria)
	private $_wFObjeto = null; // Objeto del WF de la cadena
	private $_descripcion = ''; // Descripción del Grupo
	private $_perfiles = array(); // Especifica los perfiles asociados al grupo
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetWFObjeto(){
		return $this->_wFObjeto;
	}
	public function SetWFObjeto($wFObjeto){
		$this->_wFObjeto = $wFObjeto;
	}
	public function GetDescripcion(){
		return $this->_descripcion;
	}
	public function SetDescripcion($descripcion){
		$this->_descripcion = $descripcion;
	}
	public function GetPerfiles(){
		return $this->_perfiles;
	}
	public function SetPerfiles($perfiles){
		$this->_perfiles = array();
		if(is_array($perfiles)){
			foreach($perfiles as $perfil){
				if($perfil instanceof EntidadCargo){
					$this->_perfiles[] = $perfil; 
				}
			}
		} else if($perfiles == null){
			$this->_perfiles = $perfiles;
		}
	}
	public function GetPerfil($index){
		return $this->_perfiles[$index];
	}
	public function SetPerfil($perfil){
		if($perfil instanceof EntidadCargo){
			$this->_perfiles[] = $perfil;
		}
	}
}