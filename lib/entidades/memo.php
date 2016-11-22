<?php
class EntidadMemo
{

	private $_id; // Código del memo
	private $_loginUsuario; // Usuario que creo el memo
	private $_asunto; // Asunto del memo
	private $_contenido; // Contenido del memo
	private $_revision = 0; // Indicativo de revision del memo
	private $_publicar = 0; // Indica el estatus de publicacion
	private $_firmado = 0; // Indica si el meo esta firmado
	private $_fechaCreacion; // Fecha y hora de creacion del memo
	private $_padre = ''; // Código del memo padre en caso de existir
	private $_numero = 0; // Número del memo
	private $_idValida = 0; // Código de verificacion
	private $_grupo = 0; // Indica si se le estaba enviando a un grupo como destinatario y/o Bcc
	private $_grupoDeta = ''; // Grupos a los que se le estab enviando el memo como destinatario o Bcc
	private $_idDependencia; // Dependencia ID
	
	public function __construct()
	{
		
	}
	
	public function GetId(){
		return $this->_id;
	}
	public function SetId($id){
		$this->_id = $id;
	}
	public function GetLoginUsuario(){
		return $this->_loginUsuario;
	}
	public function SetLoginUsuario($loginUsuario){
		$this->_loginUsuario = $loginUsuario;
	}
	public function GetContenido(){
		return $this->_contenido;
	}
	public function SetContenido($contenido){
		$this->_contenido = $contenido;
	}
	public function GetAsunto(){
		return $this->_asunto;
	}
	public function SetAsunto($asunto){
		$this->_asunto = $asunto;
	}
	public function GetRevision(){
		return $this->_revision;
	}
	public function SetRevision($revision){
		$this->_revision = $revision;
	}
	public function GetPublicar(){
		return $this->_publicar;
	}
	public function SetPublicar($publicar){
		$this->_publicar = $publicar;
	}
	public function GetFirmado(){
		return $this->_firmado;
	}
	public function SetFirmado($firmado){
		$this->_firmado = $firmado;
	}
	public function GetFechaCreacion(){
		return $this->_fechaCreacion;
	}
	public function SetFechaCreacion($fechaCreacion){
		$this->_fechaCreacion = $fechaCreacion;
	}
	public function GetPadre(){
		return $this->_padre;
	}
	public function SetPadre($padre){
		$this->_padre = $padre;
	}
	public function GetNumero(){
		return $this->_numero;
	}
	public function SetNumero($numero){
		$this->_numero = $numero;
	}
	public function GetIdValida(){
		return $this->_idValida;
	}
	public function SetIdValida($idValida){
		$this->_idValida = $idValida;
	}
	public function GetGrupo(){
		return $this->_grupo;
	}
	public function SetGrupo($grupo){
		$this->_grupo = $grupo;
	}
	public function GetGrupoDeta(){
		return $this->_grupoDeta;
	}
	public function SetGrupoDeta($grupoDeta){
		$this->_grupoDeta = $grupoDeta;
	}
	public function GetIdDependencia(){
		return $this->_idDependencia;
	}
	public function SetIdDependencia($idDependencia){
		$this->_idDependencia = $idDependencia;
	}
}