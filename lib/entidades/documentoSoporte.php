<?php
/*
 * Indica la relación de un documento con sus respectivos documentos de soporte
 */
class EntidadDocumentoSoporte
{
	private $_idDocumentoFuente;  // Docuento padre de los soporte
	private $_idsDocumentosSoportes; // Arreglos de documentos soportes del documento fuente
	
	public function GetIdDocumentoFuete(){
		return $this->_idDocumentoFuente;
	}
	public function SetIdDocumentoFuente($idDocumentoFuente){
		$this->_idDocumentoFuente = $idDocumentoFuente;
	}
	public function GetIdsDocumentosSoportes(){
		return $this->_idsDocumentosSoportes;
	}
	public function SetIdsDocumentosSoportes(array $idsDocumentosSoportes = null){
		$this->_idsDocumentosSoportes = $idsDocumentosSoportes;
	}
}