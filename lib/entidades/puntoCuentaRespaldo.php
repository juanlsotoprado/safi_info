<?php


class EntidadPuntoCuentaRespaldo
{
	private $_id; 
	private $_docgId;
	private $_respTipo;
	private $_respNombre;
	private $_perfId; 
	private $_usuaLogin;
	private $_respValida;

	
	
	public function __construct(){
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	
    public function GetDocgId(){
		return $this->_docgId;
	}
	public function SetDocgId($docgId){
		$this->_docgId = $docgId;
	}
	
	public function GetRespTipo(){
		return $this->_id;
	}
	public function SetRespTipo($respTipo){
		$this->_respTipo = $respTipo;
	}
	
	public function GetRespnombre(){
		return $this->respNombre;
	}
	public function SetRespnombre($respNombre){
		$this->_respNombre = $respNombre;
	}
	
	public function GetPerfId(){
		return $this->_perfId;
	}
	public function SetPerfId($perfId){
		$this->_perfId = $perfId;
	}
	
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	
	public function GetRespValida(){
		return $this->_respValida;
	}
	public function SetRespValida($respValida){
		$this->_respValida = $respValida;
	}
   
	
	
	public function __toString()
	{
		return "
			Id = ".$this->_id.",
			DocgId = ".$this->_docgId.",
			RespTipo = ".$this->_respTipo.",
			Respnombre = ".$this->_respNombre.",
			PerfId = ".$this->_perfId.",
			UsuaLogin = ".$this->_usuaLogin.",
			RespValida = ".$this->_respValida."
			
		";
	}
	
	
	
	public function __clone()
	{
		
		$this->_id = ($this->_id !== null) ? clone $this->_id : null;
		$this->_docgId = ($this->_docgId !== null) ? clone $this->_docgId : null;
		$this->_respTipo = ($this->_respTipo !== null) ? clone $this->_respTipo : null;
		$this->_respNombre = ($this->_respNombre !== null) ? clone $this->_respNombre : null;
		$this->_perfId = ($this->_perfId !== null) ? clone $this->_perfId : null;
		$this->_usuaLogin = ($this->_usuaLogin !== null) ? clone $this->_usuaLogin : null;
		$this->_respValida = ($this->_respValida !== null) ? clone $this->_respValida : null;

	}
	
	
	
	public function UTF8Encode(){
		$this->_id = utf8_encode($this->_id);
		$this->_docgId = utf8_encode($this->_docgId);
		$this->_respTipo = utf8_encode($this->_respTipo);
	    $this->_respNombre = utf8_encode($this->_respNombre);
	    $this->_perfId = utf8_encode($this->_perfId);
		$this->_usuaLogin = utf8_encode($this->_usuaLogin);
	    $this->_respValida = utf8_encode($this->_respValida);
		
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
				'docgId' => $this->_docgId,
				'respTipo' => $this->_respTipo,
		     	'respNombre' => $this->_respNombre,
			    'perfId' => $this->_perfId,
				'usuaLogin' => $this->_usuaLogin,
		     	'respValida' => $this->_respValida,
				
			);
		}
		
		return $data;
	}
	
	public function ToJson($properties = array())
	{
		return  json_encode($this->ToArray());
	}
	
	
}