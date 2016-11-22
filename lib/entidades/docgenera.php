<?php
class EntidadDocGenera
{
	private $_id; // Código del documento generado
	private $_idWFObjeto; // Código del objeto actual del documento
	private $_idWFCadena; // Código del registro actual del documento en la cadena
	private $_usuaLogin; // Login del usuario que elabora el documento
	private $_idPerfil; // Perfil del usuario que elabora el documento
	private $_fecha; // Fecha y hora de elaboracion
	private $_idEstatus; // Estado del documento
	private $_prioridad; // Representa la prioridad del documento (1=Baja, 2=Media,3=Alta)
	private $_idPerfilActual = null; // Perfil Actual del Documento (Perfil en cuya bandeja aparecerá el documento)
	private $_estadoPres = null;
	private $_numeroReserva = null;
	private $_fuenteFinanciamiento = null;
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetIdWFObjeto(){
		return $this->_idWFObjeto;
	}
	public function SetIdWFObjeto($idWFObjeto){
		$this->_idWFObjeto = $idWFObjeto;
	}
	public function GetIdWFCadena(){
		return $this->_idWFCadena;
	}
	public function SetIdWFCadena($idWFCadena){
		$this->_idWFCadena = $idWFCadena;
	}
	public function GetUsuaLogin(){
		return $this->_usuaLogin;
	}
	public function SetUsuaLogin($usuaLogin){
		$this->_usuaLogin = $usuaLogin;
	}
	public function GetIdPerfil(){
		return $this->_idPerfil;
	}
	public function SetIdPerfil($idPerfil){
		$this->_idPerfil = $idPerfil;
	}
	public function GetFecha(){
		return $this->_fecha;
	}
	public function SetFecha($fecha){
		$this->_fecha = $fecha;
	}
	public function GetIdEstatus(){
		return $this->_idEstatus;
	}
	public function SetIdEstatus($idEstatus){
		$this->_idEstatus = $idEstatus;
	}
	public function GetPrioridad(){
		return $this->_prioridad;
	}
	public function SetPrioridad($prioridad){
		$this->_prioridad = $prioridad;
	}
	public function GetIdPerfilActual(){
		return $this->_idPerfilActual;
	}
	public function SetIdPerfilActual($idPerfilActual){
		$this->_idPerfilActual = $idPerfilActual;
	}
	public function GetEstadoPres(){
		return $this->_estadoPres;
	}
	public function SetEstadoPres($estadoPres){
		$this->_estadoPres = $estadoPres;
	}
	public function GetNumeroReserva(){
		return $this->_numeroReserva;
	}
	public function SetNumeroReserva($numeroReserva){
		$this->_numeroReserva = $numeroReserva;
	}
	public function GetFuenteFinanciamiento(){
		return $this->_fuenteFinanciamiento;
	}
	public function SetFuenteFinanciamiento($fuenteFinanciamiento){
		$this->_fuenteFinanciamiento = $fuenteFinanciamiento;
	}
	
}