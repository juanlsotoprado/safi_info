<?php
class EntidadRequisicion
{
	private $_id;
	private $_idViaticoNacional;
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id= $id;
	}
	public function GetIdViaticoNacional(){
		return $this->_idViaticoNacional;
	}
	public function SetIdViaticoNacional($idViaticoNacional){
		$this->_idViaticoNacional= $idViaticoNacional;
	}
}