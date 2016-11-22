<?php
include(dirname(__FILE__) . "/../../init.php");

new ClassBorrarTemporales();

class ClassBorrarTemporales
{
	private $errors = array();
	private $db = null;
	
	public function __construct()
	{
		$this->BorrarTmp();
		$this->BorrarHtml2ps_Cache();
		$this->BorrarHtml2ps_Temp();
		$this->BorrarTemporales(SAFI_BASE_PATH . "/acciones/codi");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/acciones/reportes/tesoreria");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/documentos/pgch");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/documentos/codi");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/documentos/sopg/comprobantes/");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/procesos/contabilidad/retenciones/");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/procesos/memo/");
		$this->BorrarTemporalesPdf(SAFI_BASE_PATH . "/reportes/contabilidad");
		
		$fp = fopen(SAFI_LOG_PATH . "/log.txt", "a+");
		fwrite($fp, "Archivos temporales borrados satisfactoriamente. " . date("d/m/Y H:i:s") . "\n");
		fclose($fp);
	}
	
	public function BorrarTmp()
	{
		$path = SAFI_BASE_PATH . "/tmp";
		
		echo exec("/usr/bin/find" .
			" " . $path .
			" -maxdepth 1" .
			" -type f" .
			" -not -iname \"readme.txt\"" .
			" -exec /bin/rm -f '{}' \\;"
		);
	}
	
	public function BorrarHtml2ps_Cache()
	{
		$path = SAFI_BASE_PATH . "/includes/html2ps/cache";
		
		echo exec("/usr/bin/find" .
			" " . $path .
			" -maxdepth 1" .
			" -type f" .
			" -not -iname \"arial*.php\"" .
			" -not -iname \"arial*.z\"" .
			" -not -iname \"cour*.z\"" .
			" -not -iname \"helvetica*.php\"" .
			" -not -iname \"times*php\"" .
			" -not -iname \"times*.z\"" .
			" -not -iname \"readme.txt\"" .
			" -not -iname \"unicode.lb.classes.dat\"" .
			" -not -iname \"utf8.mappings.dat\"" .
			" -exec /bin/rm -f '{}' \\;"
		);
	}
	
	public function BorrarHtml2ps_Temp()
	{
		$path = SAFI_BASE_PATH . "/includes/html2ps/temp";
		
		echo exec("/usr/bin/find" .
			" " . $path .
			" -maxdepth 1" .
			" -type f" .
			" -not -iname \"readme.txt\"" .
			" -exec /bin/rm -f '{}' \\;"
		);
	}
	
	public function BorrarTemporales($path)
	{
		echo exec("/usr/bin/find" .
			" " . $path .
			" -maxdepth 1" .
			" -type f" .
			" -iname \"tmp*\"" .
			" -exec /bin/rm -f '{}' \\;"
		);
	}
	
	public function BorrarTemporalesPdf($path)
	{
		echo exec("/usr/bin/find" .
				" " . $path .
				" -maxdepth 1" .
				" -type f" .
				" -iname \"tmp*.pdf\"" .
				" -exec /bin/rm -f '{}' \\;"
		);
	}
}

?>