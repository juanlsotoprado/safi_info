<?php
class EntidadDependencia
{	
	private $_id;  // Código de la dependencia
	private $_nombre;  // Nombre completo de la dependencia
	private $_nombreCorto;  // Nombre corto de la dependencia
	private $_idDependenciaPadre; // Id de la dependencia padre. En caso de ser cero (0) identifica el nivel mas alto
	private $_nivel;  // Corresponde al nivel dentro del organigrama
	private $_codigoSigecof; // Código de la Dependencia, bajo el SIGECOF
	private $_loginUsuario;
	private $_observaciones;  // Observaciones de modificacion de la dependencia
	private $_idEstatus;  // Estado del Recurso
	
	public function __construct()
	{
		
	}
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetNombre(){
		return $this->_nombre;
	}
	public function SetNombre($nombre){
		$this->_nombre = $nombre;
	}
	public function GetNombreCorto(){
		return $this->_nombreCorto;
	}
	public function SetNombreCorto($nombreCorto){
		$this->_nombreCorto = $nombreCorto;
	}
	public function GetIdDependenciaPadre(){
		return $this->_idDependenciaPadre;
	}
	public function SetIdDependenciaPadre($idDependenciaPadre){
		$this->_idDependenciaPadre = $idDependenciaPadre;
	}
	public function GetNivel(){
		return $this->_nivel;
	}
	public function SetNivel($nivel){
		$this->_nivel = $nivel;
	}
	public function GetCodigoSigecof(){
		return $this->_codigoSigecof;
	}
	public function SetCodigoSigecof($codigoSigecof){
		$this->_codigoSigecof = $codigoSigecof;
	}
	public function GetLoginUsuario(){
		return $this->_loginUsuario;
	}
	public function SetLoginUsuario($loginUsuario){
		$this->_loginUsuario = $loginUsuario;
	}
	public function GetObservaciones(){
		return $this->_observaciones;
	}
	public function SetObservaciones($observaciones){
		$this->_observaciones = $observaciones;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			Nombre = ".$this->_nombre.",
			NombreCorto = ".$this->_nombreCorto.",
			IdDependenciaPadre = ".$this->_idDependenciaPadre.",
			Nivel = ".$this->_nivel.",
			CodigoSigecof = ".$this->_codigoSigecof.",
			LoginUsuario = ".$this->_loginUsuario.",
			Observaciones = ".$this->_observaciones.",
			IdEstatus = ".$this->_idEstatus."
		";
	}
	public function UTF8Encode()
	{
		$this->_id = utf8_encode($this->_id);
		$this->_nombre = utf8_encode($this->_nombre);
		$this->_nombreCorto = utf8_encode($this->_nombreCorto);
		$this->_idDependenciaPadre = utf8_encode($this->_idDependenciaPadre);
		$this->_nivel = utf8_encode($this->_nivel);
		$this->_codigoSigecof = utf8_encode($this->_codigoSigecof);
		$this->_loginUsuario = utf8_encode($this->_loginUsuario);
		$this->_observaciones = utf8_encode($this->_observaciones);
		$this->_idEstatus = utf8_encode($this->_idEstatus);
		
		return $this;
	}
	public function ToArray($properties = array())
	{
		$data = array();
		
		if(is_array($properties) && count($properties) > 0){
			foreach($properties as $property){
				$nameProperty = '_' . $property;
				$data[$property] = $this->$property;
			}
		} else {
			$data = array(
				'id' => $this->_id,
				'nombre'=>$this->_nombre,
				'nombreCorto' => $this->_nombreCorto,
				'idDependenciaPadre' => $this->_idDependenciaPadre,
				'nivel' => $this->_nivel,
				'codigoSigecof' => $this->_codigoSigecof,
				'loginUsuario' => $this->_loginUsuario,
				'observaciones' => $this->_observaciones,
				'idEstatus' => $this->_idEstatus
			);
		}
		
		return $data;
	}
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
}